<?php

/**
 * Perhitungan tagihan bulanan kredit berdasarkan type_kredit.
 *
 * Helper ini sengaja tidak bergantung pada database supaya bisa dipakai oleh
 * dashboard, detail debitur, dan laporan proyeksi lainnya.
 */
class LoanInstallmentHelper
{
    public static function calculateMonthlyBill(array $loan, string $periodDate): array
    {
        $principal = max(0.0, (float)($loan['jml_pinjaman'] ?? 0));
        $term = max(0, (int)($loan['jml_angsuran'] ?? 0));
        $annualRate = max(0.0, (float)($loan['suku_bunga_per_tahun'] ?? $loan['suku_bunga_pertahun'] ?? 0));
        $type = self::normalizeType($loan['type_kredit'] ?? '');
        $period = self::monthStart($periodDate);
        $realization = self::monthStart($loan['tgl_realisasi'] ?? '');

        if ($principal <= 0 || $term <= 0 || !$period || !$realization || $period <= $realization) {
            return self::emptyBill($type, 0);
        }

        $installmentNo = self::monthDiff($realization, $period);
        if ($installmentNo < 1 || $installmentNo > $term) {
            return self::emptyBill($type, $installmentNo);
        }

        $monthlyRate = $annualRate / 100 / 12;
        $principalPart = 0.0;
        $interestPart = 0.0;
        $method = self::methodForType($type);

        switch ($method) {
            case 'flat':
                $principalPart = $principal / $term;
                $interestPart = $principal * $monthlyRate;
                break;

            case 'declining':
                $principalPart = $principal / $term;
                $openingBalance = max(0.0, $principal - ($principalPart * ($installmentNo - 1)));
                $interestPart = $openingBalance * $monthlyRate;
                break;

            case 'bullet_flat':
                // Pokok dibayar di akhir tenor; selama periode berjalan hanya bunga.
                $interestPart = $principal * $monthlyRate;
                break;

            case 'bullet_effective':
                // Rekening koran tidak memiliki pokok terjadwal bulanan.
                $interestPart = $principal * $monthlyRate;
                break;

            case 'annuity_effective':
                if ($monthlyRate > 0) {
                    $payment = $principal * $monthlyRate / (1 - pow(1 + $monthlyRate, -$term));
                    $openingBalance = self::annuityBalanceBefore($principal, $monthlyRate, $term, $installmentNo);
                    $interestPart = $openingBalance * $monthlyRate;
                    $principalPart = max(0.0, $payment - $interestPart);
                } else {
                    $principalPart = $principal / $term;
                }
                break;

            default:
                // Kode baru tetap menghasilkan estimasi konservatif flat sampai
                // mapping bisnisnya ditambahkan secara eksplisit.
                $principalPart = $principal / $term;
                $interestPart = $principal * $monthlyRate;
                $method = 'flat_fallback';
                break;
        }

        $principalPart = min($principal, max(0.0, $principalPart));
        $interestPart = max(0.0, $interestPart);

        return [
            'type_kredit' => (string)($loan['type_kredit'] ?? ''),
            'method' => $method,
            'installment_no' => $installmentNo,
            'pokok' => $principalPart,
            'bunga' => $interestPart,
            'total' => $principalPart + $interestPart,
        ];
    }

    public static function normalizeType($type): string
    {
        $type = trim((string)$type);
        return $type === '' ? 'UNKNOWN' : $type;
    }

    private static function methodForType(string $type): string
    {
        switch ($type) {
            case '100': return 'flat';
            case '200': return 'declining';
            case '300': return 'bullet_flat';
            case '320': return 'declining';
            case '500': return 'bullet_effective';
            case '700':
            case '710': return 'annuity_effective';
            case '310':
            case '350': return 'flat';
            default: return 'flat_fallback';
        }
    }

    private static function emptyBill(string $type, int $installmentNo): array
    {
        return [
            'type_kredit' => $type,
            'method' => self::methodForType($type),
            'installment_no' => $installmentNo,
            'pokok' => 0.0,
            'bunga' => 0.0,
            'total' => 0.0,
        ];
    }

    private static function monthStart($date): ?DateTimeImmutable
    {
        if (!$date || strtotime((string)$date) === false) return null;
        return new DateTimeImmutable(date('Y-m-01', strtotime((string)$date)));
    }

    private static function monthDiff(DateTimeImmutable $from, DateTimeImmutable $to): int
    {
        return ((int)$to->format('Y') - (int)$from->format('Y')) * 12
            + ((int)$to->format('m') - (int)$from->format('m'));
    }

    private static function annuityBalanceBefore(float $principal, float $monthlyRate, int $term, int $installmentNo): float
    {
        if ($installmentNo <= 1) return $principal;
        $payment = $principal * $monthlyRate / (1 - pow(1 + $monthlyRate, -$term));
        $elapsed = $installmentNo - 1;
        return max(0.0, $principal * pow(1 + $monthlyRate, $elapsed)
            - $payment * ((pow(1 + $monthlyRate, $elapsed) - 1) / $monthlyRate));
    }
}

<?php

class JatuhTempoController
{
    private PDO $pdo;

    private const KORWIL_RANGES = [
        'SEMARANG' => ['001', '007'],
        'SOLO' => ['008', '014'],
        'BANYUMAS' => ['015', '021'],
        'PEKALONGAN' => ['022', '028'],
    ];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function sendResponse(int $status, string $message, array $data = []): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
        exit;
    }

    private function normalizeNominalField($value): string
    {
        return strtolower(trim((string) $value)) === 'baki_debet' ? 'baki_debet' : 'saldo_bank';
    }

    private function nominalLabel(string $field): string
    {
        return $field === 'baki_debet' ? 'Baki Debet' : 'Saldo Bank';
    }

    private function normalizeKolektibilitas($value): array
    {
        $values = is_array($value) ? $value : preg_split('/[,;|]+/', (string) $value);
        $selected = ['L'];

        foreach ($values ?: [] as $item) {
            if (strtoupper(trim((string) $item)) === 'DP') {
                $selected[] = 'DP';
            }
        }

        return array_values(array_unique($selected));
    }

    private function addKolektibilitasWhere(array &$where, array &$params, array $kolektibilitas, string $alias = 't1', string $prefix = 'kolek'): void
    {
        $placeholders = [];
        foreach (array_values($kolektibilitas) as $index => $kolek) {
            $placeholder = ":{$prefix}_{$index}";
            $placeholders[] = $placeholder;
            $params[$placeholder] = $kolek;
        }
        $where[] = "{$alias}.kolektibilitas IN (" . implode(', ', $placeholders) . ")";
    }

    private function normalizeOffice($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || $value === '000' || strtoupper($value) === 'ALL') return null;
        $value = preg_replace('/^(CABANG:|CAB-)/i', '', $value);
        return ctype_digit($value) ? str_pad($value, 3, '0', STR_PAD_LEFT) : $value;
    }

    private function normalizeKorwil($value): ?string
    {
        $value = strtoupper(trim((string) $value));
        $value = preg_replace('/^KOR(?:WIL)?[-_:]?/i', '', $value);
        return isset(self::KORWIL_RANGES[$value]) ? $value : null;
    }

    private function getPeriod(array $input): array
    {
        $closing = trim((string) ($input['closing_date'] ?? ''));
        $harian = trim((string) ($input['harian_date'] ?? ''));
        $closing = $closing !== '' ? $closing : date('Y-m-d', strtotime('last day of previous month'));
        $harian = $harian !== '' ? $harian : date('Y-m-d');

        $bulan = str_pad((string) ($input['bulan'] ?? date('m')), 2, '0', STR_PAD_LEFT);
        $tahun = (string) ($input['tahun'] ?? date('Y'));
        $jtStart = sprintf('%s-%s-01', $tahun, $bulan);
        $jtEnd = date('Y-m-t', strtotime($jtStart));

        return [$closing, $harian, $jtStart, $jtEnd];
    }

    private function addScopeWhere(array &$where, array &$params, ?string $office, ?string $korwil, string $alias = 't1'): void
    {
        if ($office !== null) {
            $where[] = "{$alias}.kode_cabang = :kode_kantor";
            $params[':kode_kantor'] = $office;
            return;
        }

        if ($korwil !== null) {
            [$start, $end] = self::KORWIL_RANGES[$korwil];
            $where[] = "LPAD(CAST({$alias}.kode_cabang AS CHAR), 3, '0') BETWEEN :korwil_start AND :korwil_end";
            $params[':korwil_start'] = $start;
            $params[':korwil_end'] = $end;
        }
    }

    private function bind(PDOStatement $stmt, array $params): void
    {
        foreach ($params as $key => $value) $stmt->bindValue($key, $value);
    }

    private function baseWhere(array $period, ?string $office, ?string $korwil, string $alias = 't1', array $kolektibilitas = ['L']): array
    {
        [$closing, , $jtStart, $jtEnd] = $period;
        $where = [
            "{$alias}.created = :closing_date",
            "{$alias}.tgl_jatuh_tempo BETWEEN :jt_start AND :jt_end",
        ];
        $params = [
            ':closing_date' => $closing,
            ':jt_start' => $jtStart,
            ':jt_end' => $jtEnd,
        ];
        $this->addKolektibilitasWhere($where, $params, $kolektibilitas, $alias);
        $this->addScopeWhere($where, $params, $office, $korwil, $alias);
        return [$where, $params];
    }

    private function getBreakdownMeta(array $period, ?string $office, ?string $korwil, array $kolektibilitas): array
    {
        [$where, $params] = $this->baseWhere($period, $office, $korwil, 't1', $kolektibilitas);
        $sql = "SELECT
                    COUNT(DISTINCT NULLIF(TRIM(t1.kode_group1), '')) AS kankas_count,
                    COUNT(DISTINCT NULLIF(TRIM(t1.kode_group2), '')) AS ao_count
                FROM nominatif t1
                WHERE " . implode(' AND ', $where);
        $stmt = $this->pdo->prepare($sql);
        $this->bind($stmt, $params);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $kankasCount = (int) ($row['kankas_count'] ?? 0);
        return [
            'kankas_count' => $kankasCount,
            'ao_count' => (int) ($row['ao_count'] ?? 0),
            'recommended_breakdown' => $kankasCount < 2 ? 'AO' : 'KANKAS',
        ];
    }

    /** Rekap jatuh tempo, default kolektibilitas L dengan DP opsional. */
    public function getRekapProspek($input = []): void
    {
        $b = is_array($input) ? $input : [];
        $period = $this->getPeriod($b);
        [$closing, $harian] = $period;
        $nominal = $this->normalizeNominalField($b['nominal_field'] ?? $b['hitung_berdasarkan'] ?? 'saldo_bank');
        $kolektibilitas = $this->normalizeKolektibilitas($b['kolektibilitas'] ?? $b['kolek'] ?? ['L']);
        $office = $this->normalizeOffice($b['kode_kantor'] ?? null);
        $korwil = $this->normalizeKorwil($b['korwil'] ?? null);
        $requestedBreakdown = strtoupper(trim((string) ($b['breakdown_by'] ?? '')));
        $hasBreakdown = in_array($requestedBreakdown, ['KANKAS', 'AO'], true);

        try {
            $isBranch = $office !== null;
            $breakdownMeta = $isBranch
                ? $this->getBreakdownMeta($period, $office, null, $kolektibilitas)
                : ['kankas_count' => 0, 'ao_count' => 0, 'recommended_breakdown' => 'CABANG'];
            $breakdown = $isBranch
                ? ($hasBreakdown ? $requestedBreakdown : $breakdownMeta['recommended_breakdown'])
                : 'CABANG';

            [$where, $params] = $this->baseWhere($period, $office, $korwil, 't1', $kolektibilitas);
            $params[':harian_date'] = $harian;
            $params[':closing_limit'] = $closing;
            $params[':harian_limit'] = $harian;

            if ($breakdown === 'KANKAS') {
                $groupCode = "COALESCE(NULLIF(TRIM(t1.kode_group1), ''), 'UNASSIGNED')";
                $groupLabel = "COALESCE(NULLIF(MAX(k.deskripsi_group1), ''), CONCAT('Kankas ', {$groupCode}))";
                $groupBy = $groupCode;
                $groupJoin = "LEFT JOIN kankas k ON TRIM(k.kode_group1) = TRIM(t1.kode_group1) AND LPAD(CAST(k.kode_kantor AS CHAR), 3, '0') = LPAD(CAST(t1.kode_cabang AS CHAR), 3, '0')";
            } elseif ($breakdown === 'AO') {
                $groupCode = "COALESCE(NULLIF(TRIM(t1.kode_group2), ''), 'UNASSIGNED')";
                $groupLabel = "COALESCE(NULLIF(MAX(ao.nama_ao), ''), CONCAT('AO ', {$groupCode}))";
                $groupBy = $groupCode;
                $groupJoin = "LEFT JOIN ao_kredit ao ON TRIM(ao.kode_group2) = TRIM(t1.kode_group2) AND LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') = LPAD(CAST(t1.kode_cabang AS CHAR), 3, '0')";
            } else {
                $groupCode = "LPAD(CAST(t1.kode_cabang AS CHAR), 3, '0')";
                $groupLabel = "COALESCE(MAX(kk.nama_kantor), CONCAT('CABANG ', {$groupCode}))";
                $groupBy = $groupCode;
                $groupJoin = "LEFT JOIN kode_kantor kk ON LPAD(CAST(kk.kode_kantor AS CHAR), 3, '0') = {$groupCode}";
            }

            $sql = "SELECT
                        {$groupCode} AS group_code,
                        {$groupLabel} AS group_label,
                        LPAD(CAST(t1.kode_cabang AS CHAR), 3, '0') AS kode_cabang,
                        COUNT(*) AS noa_potensi,
                        SUM(COALESCE(t1.jml_pinjaman, 0)) AS plafon_potensi,
                        SUM(CASE WHEN t3.nasabah_id IS NOT NULL THEN COALESCE(t3.noa_baru, 0) ELSE 0 END) AS noa_refinancing,
                        SUM(CASE WHEN t3.nasabah_id IS NOT NULL THEN COALESCE(t3.plafon_baru, 0) ELSE 0 END) AS plafon_refinancing,
                        SUM(CASE WHEN t3.nasabah_id IS NULL AND (t2.no_rekening IS NULL OR COALESCE(t2.{$nominal}, 0) <= 0) THEN 1 ELSE 0 END) AS noa_lunas,
                        SUM(CASE WHEN t3.nasabah_id IS NULL AND (t2.no_rekening IS NULL OR COALESCE(t2.{$nominal}, 0) <= 0) THEN COALESCE(t1.jml_pinjaman, 0) ELSE 0 END) AS plafon_lunas,
                        SUM(CASE WHEN t3.nasabah_id IS NULL AND t2.no_rekening IS NOT NULL AND COALESCE(t2.{$nominal}, 0) > 0 THEN COALESCE(t2.{$nominal}, 0) ELSE 0 END) AS sisa_belum_lunas,
                        SUM(CASE WHEN t3.nasabah_id IS NULL AND t2.no_rekening IS NOT NULL AND COALESCE(t2.{$nominal}, 0) > 0 THEN 1 ELSE 0 END) AS noa_belum_lunas,
                        SUM(CASE WHEN t3.nasabah_id IS NULL AND t2.no_rekening IS NOT NULL AND COALESCE(t2.{$nominal}, 0) > 0 THEN COALESCE(t1.jml_pinjaman, 0) ELSE 0 END) AS plafon_belum_lunas
                    FROM nominatif t1
                    LEFT JOIN nominatif t2
                      ON t2.no_rekening = t1.no_rekening
                     AND t2.created = :harian_date
                    LEFT JOIN (
                        SELECT nasabah_id,
                               COUNT(DISTINCT no_rekening) AS noa_baru,
                               SUM(COALESCE({$nominal}, 0)) AS nominal_baru,
                               SUM(COALESCE(jml_pinjaman, 0)) AS plafon_baru
                        FROM nominatif
                        WHERE created = :harian_date_topup
                          AND tgl_realisasi > :closing_limit
                          AND tgl_realisasi <= :harian_limit
                        GROUP BY nasabah_id
                    ) t3 ON t3.nasabah_id = t1.nasabah_id
                    {$groupJoin}
                    WHERE " . implode(' AND ', $where) . "
                    GROUP BY {$groupBy}, t1.kode_cabang
                    ORDER BY group_label ASC";

            $params[':harian_date_topup'] = $harian;
            $stmt = $this->pdo->prepare($sql);
            $this->bind($stmt, $params);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $finalData = [];
            $grand = [
                'kode_kantor' => 'ALL', 'nama_kantor' => 'TOTAL',
                'group_code' => 'ALL', 'group_label' => 'TOTAL',
                'noa_potensi' => 0, 'plafon_potensi' => 0,
                'noa_refinancing' => 0, 'plafon_refinancing' => 0,
                'noa_lunas' => 0, 'plafon_lunas' => 0,
                'sisa_belum_lunas' => 0, 'noa_belum_lunas' => 0, 'plafon_belum_lunas' => 0,
                'persentase' => 0,
            ];

            foreach ($rows as $row) {
                $noaPotensi = (int) ($row['noa_potensi'] ?? 0);
                $plafonPotensi = (float) ($row['plafon_potensi'] ?? 0);
                $noaRefinancing = (int) ($row['noa_refinancing'] ?? 0);
                $plafonRefinancing = (float) ($row['plafon_refinancing'] ?? 0);
                $noaLunas = (int) ($row['noa_lunas'] ?? 0);
                $plafonLunas = (float) ($row['plafon_lunas'] ?? 0);
                $sisaBelumLunas = (float) ($row['sisa_belum_lunas'] ?? 0);
                $noaBelumLunas = (int) ($row['noa_belum_lunas'] ?? 0);
                $plafonBelumLunas = (float) ($row['plafon_belum_lunas'] ?? 0);
                $item = [
                    'kode_kantor' => (string) ($row['kode_cabang'] ?? ''),
                    'nama_kantor' => (string) ($row['group_label'] ?? $row['kode_cabang'] ?? '-'),
                    'group_code' => (string) ($row['group_code'] ?? ''),
                    'group_label' => (string) ($row['group_label'] ?? '-'),
                    'group_type' => $breakdown,
                    'noa_potensi' => $noaPotensi, 'plafon_potensi' => $plafonPotensi,
                    'noa_refinancing' => $noaRefinancing, 'plafon_refinancing' => $plafonRefinancing,
                    'noa_lunas' => $noaLunas, 'plafon_lunas' => $plafonLunas,
                    'sisa_belum_lunas' => $sisaBelumLunas, 'noa_belum_lunas' => $noaBelumLunas,
                    'plafon_belum_lunas' => $plafonBelumLunas,
                    'persentase' => $plafonPotensi > 0 ? round(($plafonRefinancing / $plafonPotensi) * 100, 2) : 0,
                    // Alias lama dipertahankan agar export/detail lama tidak langsung rusak.
                    'plafon_lama' => $plafonPotensi, 'nominal_lama' => $plafonPotensi,
                    'noa_lama' => $noaPotensi, 'baki_debet' => $sisaBelumLunas,
                    'noa_baru' => $noaRefinancing, 'plafon_baru' => $plafonRefinancing,
                    'nominal_baru' => $plafonRefinancing,
                ];
                $finalData[] = $item;
                $grand['noa_potensi'] += $noaPotensi;
                $grand['plafon_potensi'] += $plafonPotensi;
                $grand['noa_refinancing'] += $noaRefinancing;
                $grand['plafon_refinancing'] += $plafonRefinancing;
                $grand['noa_lunas'] += $noaLunas;
                $grand['plafon_lunas'] += $plafonLunas;
                $grand['sisa_belum_lunas'] += $sisaBelumLunas;
                $grand['noa_belum_lunas'] += $noaBelumLunas;
                $grand['plafon_belum_lunas'] += $plafonBelumLunas;
            }
            $grand['persentase'] = $grand['plafon_potensi'] > 0
                ? round(($grand['plafon_refinancing'] / $grand['plafon_potensi']) * 100, 2) : 0;
            $grand['plafon_lama'] = $grand['plafon_potensi'];
            $grand['nominal_lama'] = $grand['plafon_potensi'];
            $grand['noa_lama'] = $grand['noa_potensi'];
            $grand['baki_debet'] = $grand['sisa_belum_lunas'];
            $grand['noa_baru'] = $grand['noa_refinancing'];
            $grand['plafon_baru'] = $grand['plafon_refinancing'];
            $grand['nominal_baru'] = $grand['plafon_refinancing'];

            $this->sendResponse(200, 'Sukses', [
                'grand_total' => $grand,
                'rekap_per_cabang' => $finalData,
                'nominal_field' => $nominal,
                'nominal_label' => $this->nominalLabel($nominal),
                'kolektibilitas' => $kolektibilitas,
                'breakdown' => [
                    'requested' => $hasBreakdown ? $requestedBreakdown : null,
                    'applied' => $breakdown,
                    'is_branch' => $isBranch,
                    'kankas_count' => $breakdownMeta['kankas_count'],
                    'ao_count' => $breakdownMeta['ao_count'],
                    'recommended' => $breakdownMeta['recommended_breakdown'],
                ],
            ]);
        } catch (Throwable $e) {
            $this->sendResponse(500, 'Error: ' . $e->getMessage());
        }
    }

    /** Detail nasabah JT dengan nominal yang sama dengan filter rekap. */
    public function getDetailProspek($input = []): void
    {
        $b = is_array($input) ? $input : [];
        $period = $this->getPeriod($b);
        [$closing, $harian, $jtStart, $jtEnd] = $period;
        $nominal = $this->normalizeNominalField($b['nominal_field'] ?? $b['hitung_berdasarkan'] ?? 'saldo_bank');
        $kolektibilitas = $this->normalizeKolektibilitas($b['kolektibilitas'] ?? $b['kolek'] ?? ['L']);
        $office = $this->normalizeOffice($b['kode_kantor'] ?? null);
        $korwil = $this->normalizeKorwil($b['korwil'] ?? null);
        $kankas = trim((string) ($b['kode_kankas'] ?? '')) ?: null;
        $ao = trim((string) ($b['kode_ao'] ?? '')) ?: null;
        $category = strtoupper(trim((string) ($b['kategori'] ?? 'POTENSI')));
        if (!in_array($category, ['POTENSI', 'REFINANCING', 'LUNAS', 'BELUM_LUNAS'], true)) $category = 'POTENSI';
        $page = max(1, (int) ($b['page'] ?? 1));
        $limit = min(10000, max(1, (int) ($b['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;

        try {
            [$where, $params] = $this->baseWhere($period, $office, $korwil, 't1', $kolektibilitas);
            if ($kankas !== null) { $where[] = 'TRIM(t1.kode_group1) = :kode_kankas'; $params[':kode_kankas'] = $kankas; }
            if ($ao !== null) { $where[] = 'TRIM(t1.kode_group2) = :kode_ao'; $params[':kode_ao'] = $ao; }
            if ($category !== 'POTENSI') {
                $params[':cat_harian_date'] = $harian;
                $params[':cat_closing_limit'] = $closing;
                $params[':cat_harian_limit'] = $harian;
                $topupExists = "EXISTS (
                    SELECT 1 FROM nominatif tx
                    WHERE tx.nasabah_id = t1.nasabah_id
                      AND tx.created = :cat_harian_date
                      AND tx.tgl_realisasi > :cat_closing_limit
                      AND tx.tgl_realisasi <= :cat_harian_limit
                )";
                $actualNominal = "COALESCE((
                    SELECT SUM(COALESCE(ta.{$nominal}, 0))
                    FROM nominatif ta
                    WHERE ta.no_rekening = t1.no_rekening
                      AND ta.created = :cat_harian_actual
                ), 0)";
                if ($category === 'REFINANCING') {
                    $where[] = $topupExists;
                } elseif ($category === 'LUNAS') {
                    $params[':cat_harian_actual'] = $harian;
                    $where[] = "NOT {$topupExists} AND {$actualNominal} <= 0";
                } elseif ($category === 'BELUM_LUNAS') {
                    $params[':cat_harian_actual'] = $harian;
                    $where[] = "NOT {$topupExists} AND {$actualNominal} > 0";
                }
            }
            $whereSql = implode(' AND ', $where);

            $stmtCount = $this->pdo->prepare("SELECT COUNT(*) FROM nominatif t1 WHERE {$whereSql}");
            $this->bind($stmtCount, $params);
            $stmtCount->execute();
            $totalRecords = (int) $stmtCount->fetchColumn();
            $totalPages = max(1, (int) ceil($totalRecords / $limit));

            $sql = "SELECT
                        t1.kode_cabang, t1.no_rekening AS no_rekening_lama, t1.nama_nasabah,
                        t1.alamat, t1.hp AS no_hp, t1.kode_group1 AS kankas, t1.kode_group2,
                        COALESCE(NULLIF(TRIM(k.deskripsi_group1), ''), t1.kode_group1, '-') AS nama_kankas,
                        t1.kode_produk,
                        COALESCE(
                            NULLIF(TRIM(pk_baru.nama_produk), ''),
                            NULLIF(TRIM(pk_lama.nama_produk), ''),
                            CONCAT('PRODUK ', COALESCE(CAST(t1.kode_produk AS CHAR), '-'))
                        ) AS nama_produk,
                        COALESCE(t1.{$nominal}, 0) AS nominal_lama,
                        COALESCE(t1.jml_pinjaman, 0) AS plafond_lama,
                        COALESCE(t1.jml_pinjaman, 0) AS jml_pinjaman,
                        COALESCE(t2.{$nominal}, 0) AS nominal_sisa,
                        COALESCE(t2.{$nominal}, 0) AS baki_debet_lama,
                        t1.tgl_jatuh_tempo,
                        COALESCE(ao.nama_ao, t1.kode_group2) AS nama_ao,
                        t3.nasabah_id AS topup_nasabah_id,
                        t3.nominal_baru, t3.plafond_baru, t3.tgl_realisasi_baru,
                        CASE
                            WHEN t3.nasabah_id IS NOT NULL THEN 'SUDAH TOP UP'
                            WHEN t2.no_rekening IS NOT NULL AND COALESCE(t2.{$nominal}, 0) > 0 THEN 'BELUM LUNAS'
                            ELSE 'LUNAS (POTENSI)'
                        END AS keterangan_status
                    FROM nominatif t1
                    LEFT JOIN nominatif t2 ON t2.no_rekening = t1.no_rekening AND t2.created = :harian_date
                    LEFT JOIN (
                        SELECT nasabah_id, SUM(COALESCE({$nominal}, 0)) AS nominal_baru,
                               SUM(COALESCE(jml_pinjaman, 0)) AS plafond_baru,
                               MAX(tgl_realisasi) AS tgl_realisasi_baru
                        FROM nominatif
                        WHERE created = :harian_date_topup
                          AND tgl_realisasi > :closing_limit AND tgl_realisasi <= :harian_limit
                        GROUP BY nasabah_id
                    ) t3 ON t3.nasabah_id = t1.nasabah_id
                    LEFT JOIN produk_kredit pk_lama
                      ON CAST(pk_lama.kode_produk AS CHAR) = CAST(t1.kode_produk AS CHAR)
                    LEFT JOIN produk_kredit pk_baru
                      ON CAST(pk_baru.kode_produk AS CHAR) = CAST(pk_lama.kode_baru AS CHAR)
                    LEFT JOIN kankas k
                      ON TRIM(k.kode_group1) = TRIM(t1.kode_group1)
                     AND LPAD(CAST(k.kode_kantor AS CHAR), 3, '0') = LPAD(CAST(t1.kode_cabang AS CHAR), 3, '0')
                    LEFT JOIN ao_kredit ao
                      ON TRIM(ao.kode_group2) = TRIM(t1.kode_group2)
                     AND LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') = LPAD(CAST(t1.kode_cabang AS CHAR), 3, '0')
                    WHERE {$whereSql}
                    ORDER BY t1.tgl_jatuh_tempo ASC, t1.no_rekening ASC
                    LIMIT :limit OFFSET :offset";

            $params[':harian_date'] = $harian;
            $params[':harian_date_topup'] = $harian;
            $params[':closing_limit'] = $closing;
            $params[':harian_limit'] = $harian;
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $type = in_array($key, [':limit', ':offset'], true) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $aoWhere = [
                't1.created = :ao_closing_date',
                't1.tgl_jatuh_tempo BETWEEN :ao_jt_start AND :ao_jt_end',
            ];
            $aoParams = [':ao_closing_date' => $closing, ':ao_jt_start' => $jtStart, ':ao_jt_end' => $jtEnd];
            $this->addKolektibilitasWhere($aoWhere, $aoParams, $kolektibilitas, 't1', 'ao_kolek');
            $this->addScopeWhere($aoWhere, $aoParams, $office, $korwil, 't1');
            if ($kankas !== null) { $aoWhere[] = 'TRIM(t1.kode_group1) = :ao_kankas'; $aoParams[':ao_kankas'] = $kankas; }
            $sqlAO = "SELECT DISTINCT t1.kode_group2, COALESCE(ao.nama_ao, t1.kode_group2) AS nama_ao
                      FROM nominatif t1
                      LEFT JOIN ao_kredit ao
                        ON TRIM(ao.kode_group2) = TRIM(t1.kode_group2)
                       AND LPAD(CAST(ao.kode_kantor AS CHAR), 3, '0') = LPAD(CAST(t1.kode_cabang AS CHAR), 3, '0')
                      WHERE " . implode(' AND ', $aoWhere) . ' ORDER BY nama_ao ASC';
            $stmtAO = $this->pdo->prepare($sqlAO);
            $this->bind($stmtAO, $aoParams);
            $stmtAO->execute();

            $this->sendResponse(200, 'Detail Data', [
                'pagination' => ['current_page' => $page, 'total_pages' => $totalPages, 'total_records' => $totalRecords],
                'data' => $data,
                'ao_list' => $stmtAO->fetchAll(PDO::FETCH_ASSOC),
                'nominal_field' => $nominal,
                'nominal_label' => $this->nominalLabel($nominal),
                'kolektibilitas' => $kolektibilitas,
                'kategori' => $category,
            ]);
        } catch (Throwable $e) {
            $this->sendResponse(500, 'Error: ' . $e->getMessage());
        }
    }
}

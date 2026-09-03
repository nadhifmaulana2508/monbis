<?php

require_once __DIR__ . '/../helpers/response.php';
// require_once __DIR__ . '/../helpers/MobHelper.php';

class LaporanKeuanganController 
{
    private PDO $pdo;

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    private function getKorwilRange(?string $korwil): ?array
    {
        $korwil = strtoupper(trim((string) $korwil));

        switch ($korwil) {
            case 'SEMARANG':
                return ['001', '007'];
            case 'SOLO':
                return ['008', '014'];
            case 'BANYUMAS':
                return ['015', '021'];
            case 'PEKALONGAN':
                return ['022', '028'];
            default:
                return null;
        }
    }

    /**
     * Kode 210 hanya dieliminasi pada konsolidasi.
     * Scope kantor dan korwil memakai saldo aset utuh.
     */
    private function isConsolidatedScope(string $kodeKantor, ?array $korwilRange = null): bool
    {
        return !$korwilRange && in_array(strtolower(trim($kodeKantor)), ['konsolidasi', '000'], true);
    }

    private function getKodePerkRangeWhere(array $kodePrefixes, string $field = 'kode_perk'): string
    {
        $prefixes = array_values(array_unique(array_map('strval', $kodePrefixes)));
        sort($prefixes, SORT_STRING);

        if ($prefixes === ['1', '2', '3']) {
            return "{$field} >= '1' AND {$field} < '4'";
        }

        if ($prefixes === ['4', '5']) {
            return "{$field} >= '4' AND {$field} < '6'";
        }

        $conditions = [];
        foreach ($prefixes as $prefix) {
            $prefix = preg_replace('/[^0-9]/', '', $prefix);
            if ($prefix !== '') {
                $conditions[] = "{$field} LIKE " . $this->pdo->quote($prefix . '%');
            }
        }

        return $conditions ? implode(' OR ', $conditions) : '1=0';
    }



    /**
     * =================================================================
     * 2. CORE ENGINE PIVOT REPORT (Matrix 000 - 028 + Konsolidasi)
     * =================================================================
     */
    private function generatePivotReport(array $input, array $kodePrefixes): array 
    {
        // 1. Ambil Tanggal H-1 atau dari request
        $tanggal = $input['harian_date'] ?? date('Y-m-d', strtotime('-1 day'));
        
        // 2. Susun range kode agar index kode_perk lebih mudah dipakai
        $sqlKodePerk = $this->getKodePerkRangeWhere($kodePrefixes);

        // 3. Query Database: Tarik semua cabang 000 - 028 secara vertical
        // Kita grouping berdasarkan KODE PERK dan KODE KANTOR
        $sql = "
            SELECT 
                kode_perk,
                kode_kantor,
                MAX(NULLIF(TRIM(nama_perk), '')) AS nama_perk,
                SUM(saldo_akhir) AS total_saldo
            FROM acc_history
            WHERE tanggal = :tanggal
              AND kode_kantor BETWEEN '000' AND '028'
              AND ({$sqlKodePerk})
            GROUP BY 
                kode_perk,
                kode_kantor
            HAVING SUM(saldo_akhir) <> 0
            ORDER BY kode_perk ASC
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':tanggal', $tanggal);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 4. Proses PIVOTING Menggunakan PHP Array (Super Cepat!)
            $pivotData = [];

            foreach ($results as $row) {
                $perk   = $row['kode_perk'];
                $kantor = $row['kode_kantor'];
                $saldo  = (float) $row['total_saldo'];
                $namaPerk = trim((string) ($row['nama_perk'] ?? ''));

                // Jika kode_perk ini belum ada di array pivot, kita inisialisasi dulu
                if (!isset($pivotData[$perk])) {
                    $pivotData[$perk] = [
                        'kode_perk'      => $perk,
                        'nama_perkiraan' => $namaPerk !== '' ? $namaPerk : $perk,
                        'konsolidasi'    => 0 // Untuk Total Keseluruhan
                    ];
                    
                    // Bikin kolom untuk 000 sampai 028 (Default 0)
                    for ($i = 0; $i <= 28; $i++) {
                        $kodeCabang = str_pad($i, 3, '0', STR_PAD_LEFT);
                        $pivotData[$perk]['cabang_' . $kodeCabang] = 0;
                    }
                }

                // Masukkan saldo ke kolom cabang yang tepat
                // Contoh: cabang_000, cabang_015, dll
                if (isset($pivotData[$perk]['cabang_' . $kantor])) {
                    $pivotData[$perk]['cabang_' . $kantor] += $saldo;
                }

                // Tambahkan langsung ke total konsolidasi
                $pivotData[$perk]['konsolidasi'] += $saldo;
            }

            // Kembalikan array associative menjadi array numerik biasa (index 0, 1, 2...)
            return array_values($pivotData);

        } catch (Exception $e) {
            error_log("Error generatePivotReport: " . $e->getMessage());
            return []; 
        }
    }

    /**
     * =================================================================
     * 3. ENDPOINT: API NERACA PIVOT (Kode 1, 2, 3)
     * =================================================================
     */
    public function apiGetNeraca(array $input) 
    {
        try {
            $data = $this->generatePivotReport($input, ['1', '2', '3']);
            sendResponse(200, "Berhasil memuat Laporan Neraca (Pivot)", $data);
        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat Laporan Neraca: " . $e->getMessage(), null);
        }
    }

    /**
     * =================================================================
     * 4. ENDPOINT: API LABA RUGI PIVOT (Kode 4, 5)
     * =================================================================
     */
    public function apiGetLabaRugi(array $input) 
    {
        try {
            $data = $this->generatePivotReport($input, ['4', '5']);
            sendResponse(200, "Berhasil memuat Laporan Laba Rugi (Pivot)", $data);
        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat Laporan Laba Rugi: " . $e->getMessage(), null);
        }
    }

    public function apiGetLapNeracaActual(array $input)
    {
        try {
            $tanggal = trim((string)($input['harian_date'] ?? $input['tanggal'] ?? ''));
            if ($tanggal === '') {
                $stmtDate = $this->pdo->query("SELECT MAX(tanggal) FROM acc_history");
                $tanggal = (string)$stmtDate->fetchColumn();
            }

            $kodeKantorReq = trim((string)($input['kode_kantor'] ?? '000'));
            $korwilReq = strtoupper(trim((string)($input['korwil'] ?? '')));
            $detailKantorReq = trim((string)($input['detail_kantor'] ?? ''));
            $detailKantor = preg_match('/^\d{1,3}$/', $detailKantorReq)
                ? str_pad($detailKantorReq, 3, '0', STR_PAD_LEFT)
                : '';
            $korwilRange = $this->getKorwilRange($korwilReq);

            $params = [':tanggal' => $tanggal];
            $scopeLabel = 'Konsolidasi';
            $sqlKantor = "AND kode_kantor BETWEEN '000' AND '028'";
            $isConsolidated = true;

            if ($korwilRange) {
                $sqlKantor = "AND kode_kantor BETWEEN :kw_start AND :kw_end";
                $params[':kw_start'] = str_pad((string)$korwilRange[0], 3, '0', STR_PAD_LEFT);
                $params[':kw_end'] = str_pad((string)$korwilRange[1], 3, '0', STR_PAD_LEFT);
                $scopeLabel = 'Korwil ' . ucfirst(strtolower($korwilReq));
                $isConsolidated = false;
            } elseif (strtolower($kodeKantorReq) === 'pusat') {
                $sqlKantor = "AND kode_kantor = '000'";
                $scopeLabel = 'Pusat';
                $isConsolidated = false;
            } elseif (!in_array(strtolower($kodeKantorReq), ['000', 'konsolidasi', 'all'], true)) {
                $kodeKantor = str_pad($kodeKantorReq, 3, '0', STR_PAD_LEFT);
                $sqlKantor = "AND kode_kantor = :kode_kantor";
                $params[':kode_kantor'] = $kodeKantor;
                $scopeLabel = $kodeKantor;
                $isConsolidated = false;
            }

            // Saat user membuka laporan cabang, sertakan metadata audit COA
            // untuk cabang tersebut agar tombol breakdown tetap tersedia.
            if ($detailKantor === '' && !$korwilRange && preg_match('/^\d{1,3}$/', $kodeKantorReq) && str_pad($kodeKantorReq, 3, '0', STR_PAD_LEFT) !== '000') {
                $detailKantor = str_pad($kodeKantorReq, 3, '0', STR_PAD_LEFT);
            }

            $sql = "
                SELECT
                    TRIM(CAST(kode_perk AS CHAR)) AS kode_perk,
                    MAX(NULLIF(TRIM(nama_perk), '')) AS nama_perk,
                    MAX(TRIM(CAST(kode_induk AS CHAR))) AS kode_induk,
                    MIN(COALESCE(level_perk, 0)) AS level_perk,
                    SUM(saldo_akhir) AS saldo
                FROM acc_history
                WHERE tanggal = :tanggal
                  $sqlKantor
                  AND kode_perk >= '1'
                  AND kode_perk < '4'
                GROUP BY TRIM(CAST(kode_perk AS CHAR))
                HAVING SUM(saldo_akhir) <> 0 OR CHAR_LENGTH(TRIM(CAST(kode_perk AS CHAR))) <= 3
                ORDER BY TRIM(CAST(kode_perk AS CHAR)) ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $assetRows = [];
            $pasivaRows = [];
            $totalAsset = 0.0;
            $totalPasiva = 0.0;
            $saldoAkun210 = 0.0;

            foreach ($rows as $row) {
                $kode = trim((string)($row['kode_perk'] ?? ''));
                if ($kode === '') {
                    continue;
                }

                $saldo = (float)($row['saldo'] ?? 0);
                $item = [
                    'kode_perk' => $kode,
                    'nama_perkiraan' => trim((string)($row['nama_perk'] ?? '')) !== '' ? trim((string)$row['nama_perk']) : $kode,
                    'kode_induk' => trim((string)($row['kode_induk'] ?? '')),
                    'level_perk' => (int)($row['level_perk'] ?? 0),
                    'saldo' => $saldo,
                    'depth' => max(0, min(5, strlen($kode) === 1 ? 0 : (int)floor((strlen($kode) - 1) / 2))),
                ];

                if (strpos($kode, '1') === 0) {
                    $assetRows[] = $item;
                    if ($kode === '1') {
                        $totalAsset = $saldo;
                    }
                } elseif (strpos($kode, '2') === 0 || strpos($kode, '3') === 0) {
                    $pasivaRows[] = $item;
                    if ($kode === '210') {
                        $saldoAkun210 = $saldo;
                    }
                    if ($kode === '2' || $kode === '3') {
                        $totalPasiva += $saldo;
                    }
                }
            }

            if ($isConsolidated && $saldoAkun210 != 0.0) {
                foreach ($assetRows as &$assetRow) {
                    if ($assetRow['kode_perk'] === '1') {
                        $assetRow['saldo'] = (float)$assetRow['saldo'] - $saldoAkun210;
                        break;
                    }
                }
                unset($assetRow);
                $totalAsset -= $saldoAkun210;

                foreach ($pasivaRows as &$pasivaRow) {
                    if ($pasivaRow['kode_perk'] === '2') {
                        $pasivaRow['saldo'] = (float)$pasivaRow['saldo'] - $saldoAkun210;
                    } elseif ($pasivaRow['kode_perk'] === '210') {
                        $pasivaRow['saldo'] = 0.0;
                    }
                }
                unset($pasivaRow);
                $totalPasiva -= $saldoAkun210;
            }

            if ($totalAsset == 0.0) {
                foreach ($assetRows as $row) {
                    if ((int)$row['depth'] === 1) {
                        $totalAsset += (float)$row['saldo'];
                    }
                }
            }

            if ($totalPasiva == 0.0) {
                foreach ($pasivaRows as $row) {
                    if ((int)$row['depth'] === 0) {
                        $totalPasiva += (float)$row['saldo'];
                    }
                }
            }

            // Pada konsolidasi, sediakan jejak selisih per cabang agar total
            // gabungan dapat ditelusuri sampai ke sumber saldonya.
            $branchBreakdown = [];
            if ($isConsolidated || $detailKantor !== '') {
                $branchFilter = "AND ah.kode_kantor BETWEEN '000' AND '028'";
                $branchParams = [];
                if ($detailKantor !== '') {
                    $branchFilter = 'AND ah.kode_kantor = :detail_kantor';
                    $branchParams[':detail_kantor'] = $detailKantor;
                }
                $sqlBranch = "
                    SELECT
                        TRIM(CAST(ah.kode_kantor AS CHAR)) AS kode_kantor,
                        MAX(NULLIF(TRIM(kk.nama_kantor), '')) AS nama_kantor,
                        SUM(CASE WHEN TRIM(CAST(ah.kode_perk AS CHAR)) = '1' THEN ah.saldo_akhir ELSE 0 END) AS aktiva,
                        SUM(CASE WHEN TRIM(CAST(ah.kode_perk AS CHAR)) IN ('2', '3') THEN ah.saldo_akhir ELSE 0 END) AS pasiva,
                        SUM(CASE WHEN TRIM(CAST(ah.kode_perk AS CHAR)) = '210' THEN ah.saldo_akhir ELSE 0 END) AS eliminasi_210
                    FROM acc_history ah
                    LEFT JOIN kode_kantor kk ON kk.kode_kantor = ah.kode_kantor
                    WHERE ah.tanggal = :tanggal_branch
                      {$branchFilter}
                      AND ah.kode_perk >= '1'
                      AND ah.kode_perk < '4'
                    GROUP BY TRIM(CAST(ah.kode_kantor AS CHAR))
                    ORDER BY TRIM(CAST(ah.kode_kantor AS CHAR)) ASC
                ";
                $stmtBranch = $this->pdo->prepare($sqlBranch);
                $stmtBranch->execute(array_merge([':tanggal_branch' => $tanggal], $branchParams));
                foreach ($stmtBranch->fetchAll(PDO::FETCH_ASSOC) as $branch) {
                    $aktivaBruto = (float)($branch['aktiva'] ?? 0);
                    $pasivaBruto = (float)($branch['pasiva'] ?? 0);
                    $eliminasi210 = (float)($branch['eliminasi_210'] ?? 0);
                    $aktivaBersih = $aktivaBruto - $eliminasi210;
                    $pasivaBersih = $pasivaBruto - $eliminasi210;
                    $kodeBranch = str_pad(trim((string)$branch['kode_kantor']), 3, '0', STR_PAD_LEFT);
                    $branchBreakdown[] = [
                        'kode_kantor' => $kodeBranch,
                        'nama_kantor' => trim((string)($branch['nama_kantor'] ?? '')) ?: ($kodeBranch === '000' ? 'Pusat' : 'Cabang ' . $kodeBranch),
                        'aktiva' => $aktivaBersih,
                        'pasiva' => $pasivaBersih,
                        'selisih' => $aktivaBersih - $pasivaBersih,
                        'sumber' => [
                            'aktiva_akun_1' => $aktivaBruto,
                            'pasiva_akun_2_3' => $pasivaBruto,
                            'eliminasi_akun_210' => $eliminasi210,
                        ],
                        'coa_breakdown' => [],
                    ];
                }

                // Rincian COA per cabang untuk menelusuri angka pembentuk selisih.
                $previousDateSql = 'SELECT MAX(tanggal) FROM acc_history WHERE tanggal < :tanggal_previous_lookup';
                $previousDateParams = [':tanggal_previous_lookup' => $tanggal];
                if ($detailKantor !== '') {
                    $previousDateSql .= ' AND kode_kantor = :detail_kantor_previous';
                    $previousDateParams[':detail_kantor_previous'] = $detailKantor;
                }
                $stmtPreviousDate = $this->pdo->prepare($previousDateSql);
                $stmtPreviousDate->execute($previousDateParams);
                $tanggalSebelumnya = (string)($stmtPreviousDate->fetchColumn() ?: '');
                $previousJoin = '';
                $previousSelect = 'NULL AS saldo_akhir_sebelumnya';
                $branchCoaParams = array_merge([':tanggal_branch_coa' => $tanggal], $branchParams);
                if ($tanggalSebelumnya !== '') {
                    $previousJoin = "
                        LEFT JOIN (
                            SELECT
                                TRIM(CAST(kode_kantor AS CHAR)) AS kode_kantor,
                                TRIM(CAST(kode_perk AS CHAR)) AS kode_perk,
                                SUM(saldo_akhir) AS saldo_akhir_sebelumnya
                            FROM acc_history
                            WHERE tanggal = :tanggal_branch_coa_prev
                              " . ($detailKantor !== '' ? 'AND kode_kantor = :detail_kantor_prev' : "AND kode_kantor BETWEEN '000' AND '028'") . "
                              AND kode_perk >= '1'
                              AND kode_perk < '4'
                            GROUP BY TRIM(CAST(kode_kantor AS CHAR)), TRIM(CAST(kode_perk AS CHAR))
                        ) prev ON prev.kode_kantor = TRIM(CAST(ah.kode_kantor AS CHAR))
                              AND prev.kode_perk = TRIM(CAST(ah.kode_perk AS CHAR))
                    ";
                    $previousSelect = 'prev.saldo_akhir_sebelumnya AS saldo_akhir_sebelumnya';
                    $branchCoaParams[':tanggal_branch_coa_prev'] = $tanggalSebelumnya;
                    if ($detailKantor !== '') $branchCoaParams[':detail_kantor_prev'] = $detailKantor;
                }
                $sqlBranchCoa = "
                    SELECT
                        TRIM(CAST(ah.kode_kantor AS CHAR)) AS kode_kantor,
                        TRIM(CAST(ah.kode_perk AS CHAR)) AS kode_perk,
                        MAX(NULLIF(TRIM(ah.nama_perk), '')) AS nama_perkiraan,
                        MAX(TRIM(CAST(ah.kode_induk AS CHAR))) AS kode_induk,
                        SUM(ah.saldo_awal) AS saldo_awal,
                        SUM(ah.debet) AS debet,
                        SUM(ah.kredit) AS kredit,
                        SUM(ah.saldo_akhir) AS saldo,
                        {$previousSelect}
                    FROM acc_history ah
                    {$previousJoin}
                    WHERE ah.tanggal = :tanggal_branch_coa
                      {$branchFilter}
                      AND ah.kode_perk >= '1'
                      AND ah.kode_perk < '4'
                    GROUP BY TRIM(CAST(ah.kode_kantor AS CHAR)), TRIM(CAST(ah.kode_perk AS CHAR))
                    HAVING SUM(ah.saldo_akhir) <> 0
                        OR SUM(ah.saldo_awal) <> 0
                        OR SUM(ah.debet) <> 0
                        OR SUM(ah.kredit) <> 0
                    ORDER BY TRIM(CAST(ah.kode_kantor AS CHAR)), ABS(SUM(ah.saldo_akhir)) DESC
                ";
                $stmtBranchCoa = $this->pdo->prepare($sqlBranchCoa);
                $stmtBranchCoa->execute($branchCoaParams);
                $branchIndex = [];
                foreach ($branchBreakdown as $idx => $branchItem) {
                    $branchIndex[$branchItem['kode_kantor']] = $idx;
                }
                foreach ($stmtBranchCoa->fetchAll(PDO::FETCH_ASSOC) as $coa) {
                    $kodeBranch = str_pad(trim((string)$coa['kode_kantor']), 3, '0', STR_PAD_LEFT);
                    if (!isset($branchIndex[$kodeBranch])) continue;
                    $branchBreakdown[$branchIndex[$kodeBranch]]['coa_breakdown'][] = [
                        'kode_perk' => trim((string)$coa['kode_perk']),
                        'nama_perkiraan' => trim((string)($coa['nama_perkiraan'] ?? '')) ?: trim((string)$coa['kode_perk']),
                        'kode_induk' => trim((string)($coa['kode_induk'] ?? '')),
                        'saldo_awal' => (float)($coa['saldo_awal'] ?? 0),
                        'debet' => (float)($coa['debet'] ?? 0),
                        'kredit' => (float)($coa['kredit'] ?? 0),
                        'saldo' => (float)($coa['saldo'] ?? 0),
                        'saldo_akhir_sebelumnya' => $coa['saldo_akhir_sebelumnya'] !== null ? (float)$coa['saldo_akhir_sebelumnya'] : null,
                        'selisih_saldo_awal_coa' => $coa['saldo_akhir_sebelumnya'] !== null
                            ? (float)$coa['saldo_awal'] - (float)$coa['saldo_akhir_sebelumnya']
                            : null,
                    ];
                }

                foreach ($branchBreakdown as &$branchItem) {
                    $parentCodes = [];
                    foreach ($branchItem['coa_breakdown'] as $coaItem) {
                        $parent = trim((string)($coaItem['kode_induk'] ?? ''));
                        if ($parent !== '') $parentCodes[$parent] = true;
                    }
                    foreach ($branchItem['coa_breakdown'] as &$coaItem) {
                        $coaItem['is_leaf'] = !isset($parentCodes[$coaItem['kode_perk']]);
                        // Dampak ke (Aktiva - Pasiva): debit menambah selisih,
                        // kredit mengurangi selisih; berlaku untuk kedua sisi.
                        $coaItem['impact_mutasi'] = (float)$coaItem['debet'] - (float)$coaItem['kredit'];
                        $coaItem['perubahan_saldo'] = (float)$coaItem['saldo'] - (float)$coaItem['saldo_awal'];
                        $isAktiva = strpos((string)$coaItem['kode_perk'], '1') === 0;
                        $saldoSeharusnya = $isAktiva
                            ? (float)$coaItem['saldo_awal'] + (float)$coaItem['debet'] - (float)$coaItem['kredit']
                            : (float)$coaItem['saldo_awal'] - (float)$coaItem['debet'] + (float)$coaItem['kredit'];
                        $coaItem['rekonsiliasi_saldo'] = (float)$coaItem['saldo'] - $saldoSeharusnya;
                        $coaItem['indikasi'] = [];
                        if ($coaItem['is_leaf'] && $coaItem['selisih_saldo_awal_coa'] !== null && abs((float)$coaItem['selisih_saldo_awal_coa']) > 0.01) {
                            $coaItem['indikasi'][] = 'Saldo awal tidak sama dengan saldo akhir sebelumnya';
                        }
                        if ($coaItem['is_leaf'] && abs((float)$coaItem['rekonsiliasi_saldo']) > 0.01) {
                            $coaItem['indikasi'][] = 'Rumus saldo awal/debet/kredit tidak sesuai saldo akhir';
                        }
                    }
                    unset($coaItem);
                }
                unset($branchItem);

                foreach ($branchBreakdown as &$branchItem) {
                    $root = [];
                    foreach ($branchItem['coa_breakdown'] as $coaItem) {
                        $kodeCoa = $coaItem['kode_perk'];
                        if (in_array($kodeCoa, ['1', '2', '3', '210'], true)) {
                            $root[$kodeCoa] = $coaItem;
                        }
                    }
                    $zero = ['saldo_awal' => 0.0, 'debet' => 0.0, 'kredit' => 0.0, 'saldo' => 0.0];
                    $r1 = $root['1'] ?? $zero;
                    $r2 = $root['2'] ?? $zero;
                    $r3 = $root['3'] ?? $zero;
                    $r210 = $root['210'] ?? $zero;
                    $totalDebetLeaf = 0.0;
                    $totalKreditLeaf = 0.0;
                    foreach ($branchItem['coa_breakdown'] as $coaItem) {
                        if ($coaItem['is_leaf']) {
                            $totalDebetLeaf += (float)$coaItem['debet'];
                            $totalKreditLeaf += (float)$coaItem['kredit'];
                        }
                    }
                    $awalPasiva = (float)$r2['saldo_awal'] + (float)$r3['saldo_awal'];
                    $akhirPasiva = (float)$r2['saldo'] + (float)$r3['saldo'];
                    $debetPasiva = (float)$r2['debet'] + (float)$r3['debet'];
                    $kreditPasiva = (float)$r2['kredit'] + (float)$r3['kredit'];
                    $branchItem['analisa'] = [
                        'tanggal_sebelumnya' => $tanggalSebelumnya !== '' ? $tanggalSebelumnya : null,
                        'saldo_awal_aktiva' => (float)$r1['saldo_awal'],
                        'saldo_awal_pasiva' => $awalPasiva,
                        'selisih_saldo_awal' => (float)$r1['saldo_awal'] - $awalPasiva,
                        'debet_aktiva' => (float)$r1['debet'],
                        'debet_pasiva' => $debetPasiva,
                        'kredit_aktiva' => (float)$r1['kredit'],
                        'kredit_pasiva' => $kreditPasiva,
                        'total_debet_leaf' => $totalDebetLeaf,
                        'total_kredit_leaf' => $totalKreditLeaf,
                        'selisih_debet_kredit' => $totalDebetLeaf - $totalKreditLeaf,
                        'saldo_akhir_aktiva' => (float)$r1['saldo'],
                        'saldo_akhir_pasiva' => $akhirPasiva,
                        'mutasi_aktiva' => ((float)$r1['debet'] - (float)$r1['kredit']),
                        'mutasi_pasiva' => (($kreditPasiva - $debetPasiva)),
                        'selisih_mutasi' => ((float)$r1['saldo'] - (float)$r1['saldo_awal']) - ($akhirPasiva - $awalPasiva),
                        'rekonsiliasi_akun_1' => (float)$r1['saldo'] - ((float)$r1['saldo_awal'] + (float)$r1['debet'] - (float)$r1['kredit']),
                        'rekonsiliasi_akun_2' => (float)$r2['saldo'] - ((float)$r2['saldo_awal'] - (float)$r2['debet'] + (float)$r2['kredit']),
                        'rekonsiliasi_akun_3' => (float)$r3['saldo'] - ((float)$r3['saldo_awal'] - (float)$r3['debet'] + (float)$r3['kredit']),
                        'saldo_akun_210' => (float)$r210['saldo'],
                    ];
                }
                unset($branchItem);

                foreach ($branchBreakdown as &$branchItem) {
                    $debetKreditGap = abs((float)($branchItem['analisa']['selisih_debet_kredit'] ?? 0));
                    if ($debetKreditGap > 0.01) {
                        foreach ($branchItem['coa_breakdown'] as &$coaItem) {
                            if ($coaItem['is_leaf'] && abs((float)$coaItem['impact_mutasi']) > 0.01) {
                                $coaItem['indikasi'][] = 'Debet/kredit ikut membentuk mismatch';
                            }
                        }
                        unset($coaItem);
                    }
                    $branchItem['coa_issue_count'] = 0;
                    foreach ($branchItem['coa_breakdown'] as $coaItem) {
                        if ($coaItem['is_leaf'] && !empty($coaItem['indikasi'])) $branchItem['coa_issue_count']++;
                    }
                    $branchItem['has_coa_issue'] = $branchItem['coa_issue_count'] > 0;
                }
                unset($branchItem);
                usort($branchBreakdown, static function ($a, $b) {
                    return abs((float)$b['selisih']) <=> abs((float)$a['selisih']);
                });
            }

            sendResponse(200, "Berhasil memuat Lap Neraca Actual", [
                'tanggal' => $tanggal,
                'kode_kantor' => $kodeKantorReq,
                'korwil' => $korwilReq,
                'scope_label' => $scopeLabel,
                'aktiva' => $assetRows,
                'pasiva' => $pasivaRows,
                'totals' => [
                    'aktiva' => $totalAsset,
                    'pasiva' => $totalPasiva,
                    'selisih' => $totalAsset - $totalPasiva,
                    'eliminasi_210' => $isConsolidated ? $saldoAkun210 : 0,
                ],
                'branch_breakdown' => $branchBreakdown,
            ]);
        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat Lap Neraca Actual: " . $e->getMessage(), null);
        }
    }

    public function apiGetLapLabaRugiActual(array $input)
    {
        try {
            $tanggal = trim((string)($input['harian_date'] ?? $input['tanggal'] ?? ''));
            if ($tanggal === '') {
                $stmtDate = $this->pdo->query("SELECT MAX(tanggal) FROM acc_history");
                $tanggal = (string)$stmtDate->fetchColumn();
            }

            $kodeKantorReq = trim((string)($input['kode_kantor'] ?? '000'));
            $korwilReq = strtoupper(trim((string)($input['korwil'] ?? '')));
            $korwilRange = $this->getKorwilRange($korwilReq);

            $params = [':tanggal' => $tanggal];
            $scopeLabel = 'Konsolidasi';
            $sqlKantor = "AND kode_kantor BETWEEN '000' AND '028'";
            $isBranchScope = false;

            if ($korwilRange) {
                $sqlKantor = "AND kode_kantor BETWEEN :kw_start AND :kw_end";
                $params[':kw_start'] = str_pad((string)$korwilRange[0], 3, '0', STR_PAD_LEFT);
                $params[':kw_end'] = str_pad((string)$korwilRange[1], 3, '0', STR_PAD_LEFT);
                $scopeLabel = 'Korwil ' . ucfirst(strtolower($korwilReq));
            } elseif (strtolower($kodeKantorReq) === 'pusat') {
                $sqlKantor = "AND kode_kantor = '000'";
                $scopeLabel = 'Pusat';
            } elseif (!in_array(strtolower($kodeKantorReq), ['000', 'konsolidasi', 'all'], true)) {
                $kodeKantor = str_pad($kodeKantorReq, 3, '0', STR_PAD_LEFT);
                $sqlKantor = "AND kode_kantor = :kode_kantor";
                $params[':kode_kantor'] = $kodeKantor;
                $scopeLabel = $kodeKantor;
                $isBranchScope = true;
            }

            $sql = "
                SELECT
                    TRIM(CAST(kode_perk AS CHAR)) AS kode_perk,
                    MAX(NULLIF(TRIM(nama_perk), '')) AS nama_perk,
                    MAX(TRIM(CAST(kode_induk AS CHAR))) AS kode_induk,
                    MIN(COALESCE(level_perk, 0)) AS level_perk,
                    SUM(saldo_akhir) AS saldo
                FROM acc_history
                WHERE tanggal = :tanggal
                  $sqlKantor
                  AND kode_perk >= '4'
                  AND kode_perk < '6'
                GROUP BY TRIM(CAST(kode_perk AS CHAR))
                HAVING SUM(saldo_akhir) <> 0 OR CHAR_LENGTH(TRIM(CAST(kode_perk AS CHAR))) <= 3
                ORDER BY TRIM(CAST(kode_perk AS CHAR)) ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $pendapatanRows = [];
            $biayaRows = [];
            $totalPendapatan = 0.0;
            $totalBiaya = 0.0;

            foreach ($rows as $row) {
                $kode = trim((string)($row['kode_perk'] ?? ''));
                if ($kode === '') {
                    continue;
                }

                $saldo = (float)($row['saldo'] ?? 0);
                $item = [
                    'kode_perk' => $kode,
                    'nama_perkiraan' => trim((string)($row['nama_perk'] ?? '')) !== '' ? trim((string)$row['nama_perk']) : $kode,
                    'kode_induk' => trim((string)($row['kode_induk'] ?? '')),
                    'level_perk' => (int)($row['level_perk'] ?? 0),
                    'saldo' => $saldo,
                    'depth' => max(0, min(5, strlen($kode) === 1 ? 0 : (int)floor((strlen($kode) - 1) / 2))),
                ];

                if (strpos($kode, '4') === 0) {
                    $pendapatanRows[] = $item;
                    if ($kode === '4') {
                        $totalPendapatan = $saldo;
                    }
                } elseif (strpos($kode, '5') === 0) {
                    $biayaRows[] = $item;
                    if ($kode === '5') {
                        $totalBiaya = $saldo;
                    }
                }
            }

            if ($totalPendapatan == 0.0) {
                foreach ($pendapatanRows as $row) {
                    if ((int)$row['depth'] === 1) {
                        $totalPendapatan += (float)$row['saldo'];
                    }
                }
            }
            if ($totalBiaya == 0.0) {
                foreach ($biayaRows as $row) {
                    if ((int)$row['depth'] === 1) {
                        $totalBiaya += (float)$row['saldo'];
                    }
                }
            }

            $labaKotor = $totalPendapatan - $totalBiaya;
            $pajak = (!$isBranchScope && $labaKotor > 0) ? ($labaKotor * 0.22) : 0.0;
            $labaBersih = $labaKotor - $pajak;

            sendResponse(200, "Berhasil memuat Lap Laba Rugi Actual", [
                'tanggal' => $tanggal,
                'kode_kantor' => $kodeKantorReq,
                'korwil' => $korwilReq,
                'scope_label' => $scopeLabel,
                'pajak_dihitung' => !$isBranchScope,
                'pendapatan' => $pendapatanRows,
                'biaya' => $biayaRows,
                'totals' => [
                    'pendapatan' => $totalPendapatan,
                    'biaya' => $totalBiaya,
                    'laba_kotor' => $labaKotor,
                    'pajak' => $pajak,
                    'laba_bersih' => $labaBersih,
                ],
            ]);
        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat Lap Laba Rugi Actual: " . $e->getMessage(), null);
        }
    }

    public function apiGetRekapLapkeuActual(array $input)
    {
        try {
            $tanggal = trim((string)($input['harian_date'] ?? $input['tanggal'] ?? ''));
            if ($tanggal === '') {
                $stmtDate = $this->pdo->query("SELECT MAX(tanggal) FROM acc_history");
                $tanggal = (string)$stmtDate->fetchColumn();
            }

            $kodeKantorReq = trim((string)($input['kode_kantor'] ?? '000'));
            $korwilReq = strtoupper(trim((string)($input['korwil'] ?? '')));
            $korwilRange = $this->getKorwilRange($korwilReq);

            $params = [':tanggal' => $tanggal];
            $scopeLabel = 'Konsolidasi';
            $sqlKantor = "AND ah.kode_kantor BETWEEN '000' AND '028'";
            $isConsolidated = true;

            if ($korwilRange) {
                $sqlKantor = "AND ah.kode_kantor BETWEEN :kw_start AND :kw_end";
                $params[':kw_start'] = str_pad((string)$korwilRange[0], 3, '0', STR_PAD_LEFT);
                $params[':kw_end'] = str_pad((string)$korwilRange[1], 3, '0', STR_PAD_LEFT);
                $scopeLabel = 'Korwil ' . ucfirst(strtolower($korwilReq));
                $isConsolidated = false;
            } elseif (strtolower($kodeKantorReq) === 'pusat') {
                $sqlKantor = "AND ah.kode_kantor = '000'";
                $scopeLabel = 'Pusat';
                $isConsolidated = false;
            } elseif (!in_array(strtolower($kodeKantorReq), ['000', 'konsolidasi', 'all'], true)) {
                $kodeKantor = str_pad($kodeKantorReq, 3, '0', STR_PAD_LEFT);
                $sqlKantor = "AND ah.kode_kantor = :kode_kantor";
                $params[':kode_kantor'] = $kodeKantor;
                $scopeLabel = $kodeKantor;
                $isConsolidated = false;
            }

            $assetCodes = [
                '101', '102', '103', '104', '105',
                '10601', '10602', '10604', '10605', '10606',
                '107', '108', '109', '110', '11102', '112', '113',
                '116', '117', '118', '119', '120', '121',
            ];
            $assetIn = implode(',', array_map([$this->pdo, 'quote'], $assetCodes));

            $sql = "
                SELECT
                    ah.kode_kantor,
                    COALESCE(kk.nama_kantor, CONCAT('Kc. ', ah.kode_kantor)) AS nama_kantor,
                    SUM(CASE WHEN ah.kode_perk IN ($assetIn) THEN ah.saldo_akhir ELSE 0 END) AS aset,
                    SUM(CASE WHEN ah.kode_perk = '10601' THEN ah.saldo_akhir ELSE 0 END) AS kredit,
                    SUM(CASE WHEN ah.kode_perk = '20401' THEN ah.saldo_akhir ELSE 0 END) AS tabungan,
                    SUM(CASE WHEN ah.kode_perk = '20402' THEN ah.saldo_akhir ELSE 0 END) AS deposito,
                    SUM(CASE WHEN ah.kode_perk = '210' THEN ah.saldo_akhir ELSE 0 END) AS eliminasi_210,
                    SUM(CASE WHEN ah.kode_perk = '2' THEN ah.saldo_akhir ELSE 0 END) AS liabilitas,
                    SUM(CASE WHEN ah.kode_perk = '3' THEN ah.saldo_akhir ELSE 0 END) AS ekuitas,
                    SUM(CASE WHEN ah.kode_perk = '4' THEN ah.saldo_akhir ELSE 0 END) AS pendapatan,
                    SUM(CASE WHEN ah.kode_perk = '5' THEN ah.saldo_akhir ELSE 0 END) AS beban
                FROM acc_history ah
                LEFT JOIN kode_kantor kk ON kk.kode_kantor = ah.kode_kantor
                WHERE ah.tanggal = :tanggal
                  $sqlKantor
                  AND (
                    ah.kode_perk IN ($assetIn)
                    OR ah.kode_perk IN ('2','3','4','5','210','20401','20402')
                  )
                GROUP BY ah.kode_kantor, kk.nama_kantor
                ORDER BY ah.kode_kantor ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $rows = [];
            $total = [
                'aset' => 0.0,
                'kredit' => 0.0,
                'tabungan' => 0.0,
                'deposito' => 0.0,
                'liabilitas' => 0.0,
                'ekuitas' => 0.0,
                'pendapatan' => 0.0,
                'beban' => 0.0,
                'laba_kotor' => 0.0,
                'eliminasi_210' => 0.0,
            ];

            foreach ($results as $row) {
                $aset = (float)($row['aset'] ?? 0);
                $kredit = (float)($row['kredit'] ?? 0);
                $tabungan = (float)($row['tabungan'] ?? 0);
                $deposito = (float)($row['deposito'] ?? 0);
                $liabilitas = (float)($row['liabilitas'] ?? 0);
                $ekuitas = (float)($row['ekuitas'] ?? 0);
                $pendapatan = (float)($row['pendapatan'] ?? 0);
                $beban = (float)($row['beban'] ?? 0);
                $labaKotor = $pendapatan - $beban;
                $eliminasi210 = (float)($row['eliminasi_210'] ?? 0);

                $rows[] = [
                    'kode_kantor' => str_pad((string)($row['kode_kantor'] ?? ''), 3, '0', STR_PAD_LEFT),
                    'nama_kantor' => (string)($row['nama_kantor'] ?? ''),
                    'aset' => $aset,
                    'kredit' => $kredit,
                    'tabungan' => $tabungan,
                    'deposito' => $deposito,
                    'liabilitas' => $liabilitas,
                    'ekuitas' => $ekuitas,
                    'pendapatan' => $pendapatan,
                    'beban' => $beban,
                    'laba_kotor' => $labaKotor,
                    'eliminasi_210' => $eliminasi210,
                ];

                $total['aset'] += $aset;
                $total['kredit'] += $kredit;
                $total['tabungan'] += $tabungan;
                $total['deposito'] += $deposito;
                $total['liabilitas'] += $liabilitas;
                $total['ekuitas'] += $ekuitas;
                $total['pendapatan'] += $pendapatan;
                $total['beban'] += $beban;
                $total['laba_kotor'] += $labaKotor;
                $total['eliminasi_210'] += $eliminasi210;
            }

            if ($isConsolidated && $total['eliminasi_210'] != 0.0) {
                $total['aset'] -= $total['eliminasi_210'];
                $total['liabilitas'] -= $total['eliminasi_210'];
            }

            $labaNegativeCount = 0;
            $lowestProfit = null;
            $highestExpense = null;
            foreach ($rows as $row) {
                if ((float)$row['laba_kotor'] < 0) {
                    $labaNegativeCount++;
                }
                if ($lowestProfit === null || (float)$row['laba_kotor'] < (float)$lowestProfit['laba_kotor']) {
                    $lowestProfit = $row;
                }
                if ($highestExpense === null || (float)$row['beban'] > (float)$highestExpense['beban']) {
                    $highestExpense = $row;
                }
            }

            sendResponse(200, "Berhasil memuat Rekap Lapkeu Actual", [
                'tanggal' => $tanggal,
                'kode_kantor' => $kodeKantorReq,
                'korwil' => $korwilReq,
                'scope_label' => $scopeLabel,
                'is_consolidated' => $isConsolidated,
                'rows' => $rows,
                'total' => $total,
                'summary' => [
                    'jumlah_kantor' => count($rows),
                    'laba_minus' => $labaNegativeCount,
                    'laba_terendah' => $lowestProfit,
                    'beban_terbesar' => $highestExpense,
                ],
            ]);
        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat Rekap Lapkeu Actual: " . $e->getMessage(), null);
        }
    }

    /**
     * FUNGSI DETAIL: Untuk cek per Kantor, per Kanwil, atau Total Konsolidasi saja
     */
    public function getReportDetail(array $input) 
    {
        try {
            $tanggal = $input['harian_date'] ?? date('Y-m-d', strtotime('-1 day'));
            $closingDate = $input['closing_date'] ?? null;
            if (!$closingDate) {
                $closingDateObj = new DateTime($tanggal);
                $closingDateObj->modify('last day of previous month');
                $closingDate = $closingDateObj->format('Y-m-d');
            }
            $typeReport = $input['type'] ?? ''; 
            $kodeKantor = $input['kode_kantor'] ?? 'konsolidasi';

            $prefixes = (strpos($typeReport, 'neraca') !== false) ? ['1', '2', '3'] : ['4', '5'];
            $sqlKodePerk = $this->getKodePerkRangeWhere($prefixes);

            $sqlFilter = "";
            $params = [
                ':tanggal_actual' => $tanggal,
                ':tanggal_filter' => $tanggal,
                ':closing_actual' => $closingDate,
                ':closing_filter' => $closingDate,
            ];

            if ($kodeKantor === 'konsolidasi') {
                $sqlFilter = " AND kode_kantor BETWEEN '000' AND '028' ";
            } else {
                $params[':kode_kantor'] = str_pad($kodeKantor, 3, '0', STR_PAD_LEFT);
                $sqlFilter = " AND kode_kantor = :kode_kantor ";
            }

            // Catatan performa: query ini sengaja hanya membaca dua tanggal dan satu
            // rentang kode akun. Agar agregasi tidak kembali ke tabel utama untuk
            // setiap baris, siapkan covering index di database (jalankan terpisah):
            // CREATE INDEX idx_acc_history_lapkeu_detail
            // ON acc_history (tanggal, kode_kantor, kode_perk, saldo_akhir, nama_perk);
            // Jangan dieksekusi otomatis dari aplikasi.
            $sql = "
                SELECT 
                    kode_perk,
                    MAX(nama_perk) AS nama_perk,
                    SUM(CASE WHEN tanggal = :tanggal_actual THEN saldo_akhir ELSE 0 END) AS total_saldo,
                    SUM(CASE WHEN tanggal = :closing_actual THEN saldo_akhir ELSE 0 END) AS closing_saldo
                FROM acc_history
                WHERE tanggal IN (:tanggal_filter, :closing_filter)
                {$sqlFilter}
                AND ({$sqlKodePerk})
                GROUP BY kode_perk
                ORDER BY
                    CASE WHEN CHAR_LENGTH(kode_perk) = 1 THEN 0 ELSE 1 END,
                    CAST(kode_perk AS UNSIGNED),
                    kode_perk ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $mappedData = [];

            foreach ($results as $row) {
                $kode = $row['kode_perk'];
                $saldo = (float)$row['total_saldo'];
                $closingSaldo = (float)($row['closing_saldo'] ?? 0);
                $namaPerk = trim((string) ($row['nama_perk'] ?? ''));
                $panjangKode = strlen($kode);
                $selisih = $saldo - $closingSaldo;
                $growth = $closingSaldo != 0.0 ? ($selisih / abs($closingSaldo)) * 100 : ($saldo == 0.0 ? 0.0 : 100.0);

                // 🔥 LOGIKA FILTER SAKTI:
                // 1. Jika panjang kode <= 3 digit, MASUKKAN (meskipun saldo 0)
                // 2. Jika panjang kode > 3 digit DAN saldo != 0, MASUKKAN
                // 3. Selain itu (kode > 3 digit dan saldo 0), ABAIKAN
                if ($panjangKode <= 3 || ($panjangKode > 3 && ($saldo != 0 || $closingSaldo != 0))) {
                    $mappedData[] = [
                        'kode_perk'      => $kode,
                        'nama_perkiraan' => $namaPerk !== '' ? $namaPerk : $kode,
                        'total_saldo'    => $saldo,
                        'closing_saldo'  => $closingSaldo,
                        'selisih_saldo'  => $selisih,
                        'growth_persen'  => round($growth, 2),
                        'closing_date'   => $closingDate,
                        'harian_date'    => $tanggal,
                        'kantor_cek'     => strtoupper($kodeKantor)
                    ];
                }
            }

            sendResponse(200, "Berhasil memuat Detail " . strtoupper($typeReport), $mappedData);

        } catch (Exception $e) {
            sendResponse(500, "Error Detail: " . $e->getMessage());
        }
    }

    /**
     * =================================================================
     * ENDPOINT: API TREN PERKIRAAN SPESIFIK (MtM & YtY)
     * =================================================================
     */
    public function apiGetTrenPerkiraan(array $input) 
    {
        try {
            $kodePerk = $input['kode_perk'] ?? '';
            $baseDate = $input['harian_date'] ?? date('Y-m-d');
            $kodeKantorReq = $input['kode_kantor'] ?? 'konsolidasi'; 

            if (empty($kodePerk)) {
                sendResponse(400, "Pilih Kode Perkiraan dulu broku!", null);
                return;
            }

            // 1. Generate Tanggal Target Secara Dinamis
            $baseDateObj = new DateTime($baseDate);
            $targetDates = [];
            $labelsMtM = [];
            $labelsYtY = [];
            
            // A. Generate 4 Bulan Terakhir (MtM)
            for ($i = 3; $i >= 0; $i--) {
                $d = clone $baseDateObj;
                $d->modify("first day of -$i month");
                $d->modify('last day of this month');
                if ($i == 0) $d = clone $baseDateObj; 
                
                $dtStr = $d->format('Y-m-d');
                $targetDates[$dtStr] = true;
                $labelsMtM[] = ['date' => $dtStr, 'label' => $d->format('M')];
            }

            // B. Generate 5 Tahun Terakhir (YtY) -> 31 Des
            $currentYear = (int) $baseDateObj->format('Y');
            for ($i = 4; $i >= 0; $i--) {
                $targetYear = $currentYear - $i;
                if ($i == 0) {
                    $dtStr = $baseDateObj->format('Y-m-d');
                } else {
                    $dtStr = $targetYear . '-12-31';
                }
                $targetDates[$dtStr] = true;
                $labelsYtY[] = ['date' => $dtStr, 'label' => (string)$targetYear];
            }

            $dateList = array_keys($targetDates);
            $inQuery = implode(',', array_fill(0, count($dateList), '?'));

            // 2. 🔥 PERBAIKAN LOGIKA FILTER KANTOR & PARAMETER PDO 🔥
            $sqlKantor = "";
            $params = $dateList; // Masukkan list tanggal ke parameter duluan

            if (strtolower($kodeKantorReq) === 'konsolidasi') {
                $sqlKantor = "AND kode_kantor BETWEEN '000' AND '028'";
            } else {
                $sqlKantor = "AND kode_kantor = ?";
                $params[] = str_pad($kodeKantorReq, 3, '0', STR_PAD_LEFT); // Tambah param kantor
            }

            // 3. Tarik Data Historikal
            if ($kodePerk === 'LABA_RUGI') {
                $sql = "
                    SELECT 
                        tanggal,
                        (
                            SUM(CASE WHEN kode_perk = '4' THEN saldo_akhir ELSE 0 END) -
                            SUM(CASE WHEN kode_perk = '5' THEN saldo_akhir ELSE 0 END)
                        ) AS total_saldo
                    FROM acc_history
                    WHERE tanggal IN ($inQuery)
                      $sqlKantor
                    GROUP BY tanggal
                ";
                $namaAkun = "LABA RUGI BERJALAN (Pendapatan - Biaya)";
                
            } else {
                $sql = "
                    SELECT 
                        tanggal,
                        MAX(NULLIF(TRIM(nama_perk), '')) AS nama_perk,
                        SUM(saldo_akhir) AS total_saldo
                    FROM acc_history
                    WHERE tanggal IN ($inQuery)
                      $sqlKantor
                      AND kode_perk = ?
                    GROUP BY tanggal
                ";
                
                $params[] = $kodePerk; // Tambah param kode_perk terakhir
                $namaAkun = $kodePerk;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($kodePerk !== 'LABA_RUGI') {
                foreach ($results as $row) {
                    $namaPerk = trim((string) ($row['nama_perk'] ?? ''));
                    if ($namaPerk !== '') {
                        $namaAkun = $namaPerk;
                        break;
                    }
                }
            }

            // 4. Mapping Hasil Query
            $dataMap = [];
            foreach ($results as $row) {
                $dataMap[$row['tanggal']] = (float) $row['total_saldo'];
            }

            $dataMtM = [];
            foreach ($labelsMtM as $item) {
                $dataMtM[] = ['label' => $item['label'], 'saldo' => $dataMap[$item['date']] ?? 0];
            }

            $dataYtY = [];
            foreach ($labelsYtY as $item) {
                $dataYtY[] = ['label' => $item['label'], 'saldo' => $dataMap[$item['date']] ?? 0];
            }

            $saldoSekarang = $dataMtM[3]['saldo'] ?? 0;
            $saldoBulanLalu = $dataMtM[2]['saldo'] ?? 0;
            $delta = $saldoSekarang - $saldoBulanLalu;
            $persen = ($saldoBulanLalu != 0) ? ($delta / abs($saldoBulanLalu)) * 100 : ($saldoSekarang != 0 ? 100 : 0);
            
            $responseData = [
                'summary' => [
                    'kode_perk' => $kodePerk,
                    'nama_perkiraan' => $namaAkun,
                    'saldo_sekarang' => $saldoSekarang,
                    'delta_nominal' => $delta,
                    'pertumbuhan_persen' => round($persen, 2)
                ],
                'mtm' => $dataMtM,
                'yty' => $dataYtY
            ];

            sendResponse(200, "Berhasil memuat tren data COA", $responseData);

        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat tren: " . $e->getMessage(), null);
        }
    }

    public function apiGetDefaultAccHistoryDate()
    {
        try {
            $stmt = $this->pdo->query("SELECT MAX(tanggal) AS last_created FROM acc_history");
            $lastCreated = (string) ($stmt->fetchColumn() ?: '');
            $closingDate = null;
            if ($lastCreated !== '') {
                $closingDateObj = new DateTime($lastCreated);
                $closingDateObj->modify('last day of previous month');
                $closingDate = $closingDateObj->format('Y-m-d');
            }

            sendResponse(200, "Tanggal terakhir data acc_history", [
                'last_created' => $lastCreated,
                'last_closing' => $closingDate,
            ]);
        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat tanggal acc_history: " . $e->getMessage(), null);
        }
    }

    public function apiGetTrenMakroMingguan(array $input)
    {
        try {
            $baseDate = $input['harian_date'] ?? date('Y-m-d');
            $kodeKantorReq = $input['kode_kantor'] ?? 'konsolidasi';
            $korwilReq = strtoupper(trim((string) ($input['korwil'] ?? '')));
            $korwilRange = $this->getKorwilRange($korwilReq);
            $isConsolidated = $this->isConsolidatedScope((string) $kodeKantorReq, $korwilRange);

            $baseDateObj = new DateTime($baseDate);
            $monthStart = $baseDateObj->format('Y-m-01');
            $monthEnd = $baseDateObj->format('Y-m-t');
            $effectiveEnd = min($baseDate, $monthEnd);
            $closingDateObj = clone $baseDateObj;
            $closingDateObj->modify('last day of previous month');
            $closingDate = $closingDateObj->format('Y-m-d');
            $lastDay = (int) $baseDateObj->format('j');
            $maxWeek = (int) floor(($lastDay - 1) / 7) + 1;
            $weekDateMap = [];
            $queryDates = [$closingDate];

            for ($week = 1; $week <= $maxWeek; $week++) {
                $endDay = min($week * 7, (int) $baseDateObj->format('t'), $lastDay);
                $endWeek = $baseDateObj->format('Y-m-') . str_pad((string) $endDay, 2, '0', STR_PAD_LEFT);
                $weekDateMap[$week] = $endWeek;
                $queryDates[] = $endWeek;
            }
            $queryDates = array_values(array_unique($queryDates));

            $sqlKantor = '';
            $params = [];
            $datePlaceholders = [];
            foreach ($queryDates as $index => $date) {
                $key = ':date_' . $index;
                $datePlaceholders[] = $key;
                $params[$key] = $date;
            }

            if ($korwilRange) {
                $sqlKantor = "AND kode_kantor BETWEEN :kw_start AND :kw_end";
                $params[':kw_start'] = str_pad((string) $korwilRange[0], 3, '0', STR_PAD_LEFT);
                $params[':kw_end'] = str_pad((string) $korwilRange[1], 3, '0', STR_PAD_LEFT);
            } elseif ($isConsolidated) {
                $sqlKantor = "AND kode_kantor BETWEEN '000' AND '028'";
            } else {
                $sqlKantor = "AND kode_kantor = :kode_kantor";
                $params[':kode_kantor'] = str_pad((string) $kodeKantorReq, 3, '0', STR_PAD_LEFT);
            }
            if ($korwilRange) {
                $sqlKantorNom = "AND kode_cabang BETWEEN :kw_start AND :kw_end";
            } elseif ($isConsolidated) {
                $sqlKantorNom = "AND kode_cabang BETWEEN '000' AND '028'";
            } else {
                $sqlKantorNom = "AND kode_cabang = :kode_kantor";
            }

            $asetCodes = [
                '101','102','103','104','105','10601','10602','10604','10605','10606',
                '107','108','109','110','11102','112','113','116','117','118','119','120','121'
            ];
            $quoteCode = function ($code) {
                return $this->pdo->quote((string) $code);
            };
            $asetQuoted = implode(',', array_map($quoteCode, $asetCodes));
            $trackedCodes = array_merge($asetCodes, ['210', '20401', '20402', '4', '5']);
            $trackedQuoted = implode(',', array_map($quoteCode, array_unique($trackedCodes)));
            $assetAdjustmentSql = $isConsolidated
                ? " - SUM(CASE WHEN kode_perk = '210' THEN saldo_akhir ELSE 0 END)"
                : '';

            $sql = "
                SELECT
                    tanggal,
                    SUM(CASE WHEN kode_perk IN ($asetQuoted) THEN saldo_akhir ELSE 0 END)
                    $assetAdjustmentSql AS aset_gabungan,
                    SUM(CASE WHEN kode_perk = 10601 THEN saldo_akhir ELSE 0 END) AS kredit_baki_debet,
                    SUM(CASE WHEN kode_perk = 20401 THEN saldo_akhir ELSE 0 END) AS tabungan,
                    SUM(CASE WHEN kode_perk = 20402 THEN saldo_akhir ELSE 0 END) AS deposito,
                    SUM(CASE WHEN kode_perk IN (20401, 20402) THEN saldo_akhir ELSE 0 END) AS dpk,
                    SUM(CASE WHEN kode_perk = 4 THEN saldo_akhir ELSE 0 END) AS pendapatan,
                    SUM(CASE WHEN kode_perk = 5 THEN saldo_akhir ELSE 0 END) AS beban,
                    SUM(CASE WHEN kode_perk = 4 THEN saldo_akhir ELSE 0 END)
                    - SUM(CASE WHEN kode_perk = 5 THEN saldo_akhir ELSE 0 END) AS laba_net
                FROM acc_history
                WHERE tanggal IN (" . implode(',', $datePlaceholders) . ")
                  $sqlKantor
                  AND kode_perk IN ($trackedQuoted)
                GROUP BY tanggal
                ORDER BY tanggal ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $rowsByDate = [];
            foreach ($rows as $row) {
                $rowsByDate[$row['tanggal']] = [
                    'tanggal' => $row['tanggal'],
                    'aset_gabungan' => (float) ($row['aset_gabungan'] ?? 0),
                    'kredit_baki_debet' => (float) ($row['kredit_baki_debet'] ?? 0),
                    'tabungan' => (float) ($row['tabungan'] ?? 0),
                    'deposito' => (float) ($row['deposito'] ?? 0),
                    'dpk' => (float) ($row['dpk'] ?? 0),
                    'pendapatan' => (float) ($row['pendapatan'] ?? 0),
                    'beban' => (float) ($row['beban'] ?? 0),
                    'laba_net' => (float) ($row['laba_net'] ?? 0),
                    'npl' => 0,
                    'npl_persen' => 0,
                ];
            }

            $nplParams = $params;
            $sqlNpl = "
                SELECT
                    created AS tanggal,
                    SUM(CASE WHEN kolektibilitas IN ('KL','D','M') THEN baki_debet ELSE 0 END) AS total_npl
                FROM nominatif
                WHERE created IN (" . implode(',', $datePlaceholders) . ")
                  $sqlKantorNom
                GROUP BY created
            ";
            $stmtNpl = $this->pdo->prepare($sqlNpl);
            $stmtNpl->execute($nplParams);
            foreach ($stmtNpl->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $tgl = $row['tanggal'];
                if (!isset($rowsByDate[$tgl])) {
                    $rowsByDate[$tgl] = [
                        'tanggal' => $tgl,
                        'aset_gabungan' => 0,
                        'kredit_baki_debet' => 0,
                        'tabungan' => 0,
                        'deposito' => 0,
                        'dpk' => 0,
                        'pendapatan' => 0,
                        'beban' => 0,
                        'laba_net' => 0,
                        'npl' => 0,
                        'npl_persen' => 0,
                    ];
                }
                $rowsByDate[$tgl]['npl'] = (float) ($row['total_npl'] ?? 0);
            }
            foreach ($rowsByDate as $tgl => $row) {
                $kredit = (float) ($row['kredit_baki_debet'] ?? 0);
                $rowsByDate[$tgl]['npl_persen'] = $kredit > 0 ? round((((float) ($row['npl'] ?? 0)) / $kredit) * 100, 2) : 0;
            }

            $closingRow = $rowsByDate[$closingDate] ?? [];
            $previousClosing = [
                'tanggal' => $closingDate,
                'aset_gabungan' => (float) ($closingRow['aset_gabungan'] ?? 0),
                'kredit_baki_debet' => (float) ($closingRow['kredit_baki_debet'] ?? 0),
                'tabungan' => (float) ($closingRow['tabungan'] ?? 0),
                'deposito' => (float) ($closingRow['deposito'] ?? 0),
                'dpk' => (float) ($closingRow['dpk'] ?? 0),
                'pendapatan' => (float) ($closingRow['pendapatan'] ?? 0),
                'beban' => (float) ($closingRow['beban'] ?? 0),
                'laba_net' => (float) ($closingRow['laba_net'] ?? 0),
                'npl' => (float) ($closingRow['npl'] ?? 0),
                'npl_persen' => (float) ($closingRow['npl_persen'] ?? 0),
            ];
            $weeks = [];

            for ($week = 1; $week <= $maxWeek; $week++) {
                $startDay = (($week - 1) * 7) + 1;
                $endDay = min($week * 7, (int) $baseDateObj->format('t'), $lastDay);
                $startWeek = $baseDateObj->format('Y-m-') . str_pad((string) $startDay, 2, '0', STR_PAD_LEFT);
                $endWeek = $weekDateMap[$week];
                $weekRow = $rowsByDate[$endWeek] ?? [
                    'tanggal' => $endWeek,
                    'aset_gabungan' => 0,
                    'kredit_baki_debet' => 0,
                    'tabungan' => 0,
                    'deposito' => 0,
                    'dpk' => 0,
                    'pendapatan' => 0,
                    'beban' => 0,
                    'laba_net' => 0,
                    'npl' => 0,
                    'npl_persen' => 0,
                ];

                $weeks[] = [
                    'label' => 'Pekan ' . $week,
                    'tanggal' => $weekRow['tanggal'],
                    'keterangan' => date('d M', strtotime($startWeek)) . ' - ' . date('d M', strtotime($endWeek)),
                    'aset_gabungan' => $weekRow['aset_gabungan'],
                    'kredit_baki_debet' => $weekRow['kredit_baki_debet'],
                    'tabungan' => $weekRow['tabungan'],
                    'deposito' => $weekRow['deposito'],
                    'dpk' => $weekRow['dpk'],
                    'pendapatan' => $weekRow['pendapatan'],
                    'beban' => $weekRow['beban'],
                    'laba_net' => $weekRow['laba_net'],
                    'npl' => $weekRow['npl'],
                    'npl_persen' => $weekRow['npl_persen'],
                ];
            }

            $latest = end($weeks) ?: null;
            $previous = count($weeks) > 1 ? $weeks[count($weeks) - 2] : null;
            $calcGrowth = function ($current, $past) {
                if ((float) $past == 0.0) {
                    return (float) $current == 0.0 ? 0.0 : 100.0;
                }
                return round(((($current - $past) / abs($past)) * 100), 2);
            };

            $summary = $latest ? [
                'label' => $latest['label'],
                'tanggal' => $latest['tanggal'],
                'aset_gabungan' => $latest['aset_gabungan'],
                'kredit_baki_debet' => $latest['kredit_baki_debet'],
                'tabungan' => $latest['tabungan'],
                'deposito' => $latest['deposito'],
                'dpk' => $latest['dpk'],
                'pendapatan' => $latest['pendapatan'],
                'beban' => $latest['beban'],
                'laba_net' => $latest['laba_net'],
                'npl' => $latest['npl'],
                'npl_persen' => $latest['npl_persen'],
                'laba_setelah_pajak' => $latest['laba_net'] - ($latest['laba_net'] * 0.22),
                'pajak_laba' => $latest['laba_net'] * 0.22,
                'previous' => [
                    'label' => $previous['label'] ?? null,
                    'tanggal' => $previous['tanggal'] ?? null,
                    'aset_gabungan' => $previous['aset_gabungan'] ?? 0,
                    'kredit_baki_debet' => $previous['kredit_baki_debet'] ?? 0,
                    'tabungan' => $previous['tabungan'] ?? 0,
                    'deposito' => $previous['deposito'] ?? 0,
                    'dpk' => $previous['dpk'] ?? 0,
                    'pendapatan' => $previous['pendapatan'] ?? 0,
                    'beban' => $previous['beban'] ?? 0,
                    'laba_net' => $previous['laba_net'] ?? 0,
                    'npl' => $previous['npl'] ?? 0,
                    'npl_persen' => $previous['npl_persen'] ?? 0,
                ],
                'previous_closing' => $previousClosing,
                'delta_aset' => $latest['aset_gabungan'] - ($previous['aset_gabungan'] ?? 0),
                'delta_kredit' => $latest['kredit_baki_debet'] - ($previous['kredit_baki_debet'] ?? 0),
                'delta_tabungan' => $latest['tabungan'] - ($previous['tabungan'] ?? 0),
                'delta_deposito' => $latest['deposito'] - ($previous['deposito'] ?? 0),
                'delta_dpk' => $latest['dpk'] - ($previous['dpk'] ?? 0),
                'delta_pendapatan' => $latest['pendapatan'] - ($previous['pendapatan'] ?? 0),
                'delta_beban' => $latest['beban'] - ($previous['beban'] ?? 0),
                'delta_laba' => $latest['laba_net'] - ($previous['laba_net'] ?? 0),
                'delta_npl' => $latest['npl'] - ($previous['npl'] ?? 0),
                'delta_npl_persen' => $latest['npl_persen'] - ($previous['npl_persen'] ?? 0),
                'delta_closing_aset' => $latest['aset_gabungan'] - $previousClosing['aset_gabungan'],
                'delta_closing_kredit' => $latest['kredit_baki_debet'] - $previousClosing['kredit_baki_debet'],
                'delta_closing_tabungan' => $latest['tabungan'] - $previousClosing['tabungan'],
                'delta_closing_deposito' => $latest['deposito'] - $previousClosing['deposito'],
                'delta_closing_dpk' => $latest['dpk'] - $previousClosing['dpk'],
                'delta_closing_pendapatan' => $latest['pendapatan'] - $previousClosing['pendapatan'],
                'delta_closing_beban' => $latest['beban'] - $previousClosing['beban'],
                'delta_closing_laba' => $latest['laba_net'] - $previousClosing['laba_net'],
                'delta_closing_npl' => $latest['npl'] - $previousClosing['npl'],
                'delta_closing_npl_persen' => $latest['npl_persen'] - $previousClosing['npl_persen'],
                'growth_aset' => $calcGrowth($latest['aset_gabungan'], $previous['aset_gabungan'] ?? 0),
                'growth_kredit' => $calcGrowth($latest['kredit_baki_debet'], $previous['kredit_baki_debet'] ?? 0),
                'growth_tabungan' => $calcGrowth($latest['tabungan'], $previous['tabungan'] ?? 0),
                'growth_deposito' => $calcGrowth($latest['deposito'], $previous['deposito'] ?? 0),
                'growth_dpk' => $calcGrowth($latest['dpk'], $previous['dpk'] ?? 0),
                'growth_pendapatan' => $calcGrowth($latest['pendapatan'], $previous['pendapatan'] ?? 0),
                'growth_beban' => $calcGrowth($latest['beban'], $previous['beban'] ?? 0),
                'growth_laba' => $calcGrowth($latest['laba_net'], $previous['laba_net'] ?? 0),
                'growth_npl' => $calcGrowth($latest['npl'], $previous['npl'] ?? 0),
                'growth_closing_aset' => $calcGrowth($latest['aset_gabungan'], $previousClosing['aset_gabungan']),
                'growth_closing_kredit' => $calcGrowth($latest['kredit_baki_debet'], $previousClosing['kredit_baki_debet']),
                'growth_closing_tabungan' => $calcGrowth($latest['tabungan'], $previousClosing['tabungan']),
                'growth_closing_deposito' => $calcGrowth($latest['deposito'], $previousClosing['deposito']),
                'growth_closing_dpk' => $calcGrowth($latest['dpk'], $previousClosing['dpk']),
                'growth_closing_pendapatan' => $calcGrowth($latest['pendapatan'], $previousClosing['pendapatan']),
                'growth_closing_beban' => $calcGrowth($latest['beban'], $previousClosing['beban']),
                'growth_closing_laba' => $calcGrowth($latest['laba_net'], $previousClosing['laba_net']),
                'growth_closing_npl' => $calcGrowth($latest['npl'], $previousClosing['npl']),
            ] : null;

            $scopeLabel = $korwilRange ? ('KORWIL ' . $korwilReq) : strtoupper($kodeKantorReq);

            sendResponse(200, "Berhasil memuat tren makro mingguan (" . $scopeLabel . ")", [
                'scope' => $scopeLabel,
                'periode' => [
                    'start' => $monthStart,
                    'end' => $effectiveEnd,
                ],
                'formula' => [
                    'aset_gabungan' => "101 + 102 + 103 + 104 + 105 + 10601 + 10602 + 10604 + 10605 + 10606 + 107 + 108 + 109 + 110 + 11102 + 112 + 113 + 116 + 117 + 118 + 119 + 120 + 121" . ($isConsolidated ? " - 210" : ""),
                    'kredit_baki_debet' => "10601",
                    'tabungan' => "20401",
                    'deposito' => "20402",
                    'dpk' => "20401 + 20402",
                    'pendapatan' => "4",
                    'beban' => "5",
                    'laba_net' => "4 - 5",
                    'npl' => "Nominatif kolektibilitas KL + D + M",
                    'npl_persen' => "NPL / Kredit Baki Debet x 100%",
                ],
                'summary' => $summary,
                'weeks' => $weeks,
            ]);
        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat tren makro mingguan: " . $e->getMessage(), null);
        }
    }

    private function getBranchYtdMakro(string $kodeKantor, string $baseDate, array $asetCodes): array
    {
        $baseDateObj = new DateTime($baseDate);
        $year = (int) $baseDateObj->format('Y');
        $throughMonth = (int) $baseDateObj->format('n');
        $periodStart = sprintf('%04d-01-01', $year);
        $asetIn = implode(',', array_map([$this->pdo, 'quote'], $asetCodes));
        $trackedIn = implode(',', array_map([$this->pdo, 'quote'], array_merge($asetCodes, ['4', '5'])));

        $sql = "
            SELECT
                ah.tanggal,
                SUM(CASE WHEN ah.kode_perk IN ($asetIn) THEN ah.saldo_akhir ELSE 0 END) AS aset,
                SUM(CASE WHEN ah.kode_perk = '4' THEN ah.saldo_akhir ELSE 0 END) AS pendapatan,
                SUM(CASE WHEN ah.kode_perk = '5' THEN ah.saldo_akhir ELSE 0 END) AS beban
            FROM acc_history ah
            INNER JOIN (
                SELECT YEAR(tanggal) AS tahun, MONTH(tanggal) AS bulan, MAX(tanggal) AS tanggal
                FROM acc_history
                WHERE kode_kantor = :kode_tanggal
                  AND tanggal BETWEEN :periode_awal AND :periode_akhir
                GROUP BY YEAR(tanggal), MONTH(tanggal)
            ) periode ON periode.tanggal = ah.tanggal
            WHERE ah.kode_kantor = :kode_nilai
              AND ah.kode_perk IN ($trackedIn)
            GROUP BY ah.tanggal
            ORDER BY ah.tanggal ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':kode_tanggal' => $kodeKantor,
            ':kode_nilai' => $kodeKantor,
            ':periode_awal' => $periodStart,
            ':periode_akhir' => $baseDate,
        ]);

        $rowsByMonth = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $month = (int) (new DateTime((string) $row['tanggal']))->format('n');
            $pendapatan = (float) ($row['pendapatan'] ?? 0);
            $beban = (float) ($row['beban'] ?? 0);
            $rowsByMonth[$month] = [
                'tanggal' => (string) $row['tanggal'],
                'aset' => (float) ($row['aset'] ?? 0),
                'pendapatan_akumulasi' => $pendapatan,
                'beban_akumulasi' => $beban,
                'laba_akumulasi' => $pendapatan - $beban,
            ];
        }

        $items = [];
        for ($month = 1; $month <= $throughMonth; $month++) {
            if (!isset($rowsByMonth[$month])) {
                continue;
            }
            $current = $rowsByMonth[$month];
            $previous = $month > 1 ? ($rowsByMonth[$month - 1] ?? null) : null;
            $previousPendapatan = (float) ($previous['pendapatan_akumulasi'] ?? 0);
            $previousBeban = (float) ($previous['beban_akumulasi'] ?? 0);
            $previousLaba = (float) ($previous['laba_akumulasi'] ?? 0);

            $items[] = [
                'bulan' => $month,
                'tanggal' => $current['tanggal'],
                'aset' => $current['aset'],
                'pendapatan' => $current['pendapatan_akumulasi'] - $previousPendapatan,
                'beban' => $current['beban_akumulasi'] - $previousBeban,
                'laba' => $current['laba_akumulasi'] - $previousLaba,
                'pendapatan_akumulasi' => $current['pendapatan_akumulasi'],
                'beban_akumulasi' => $current['beban_akumulasi'],
                'laba_akumulasi' => $current['laba_akumulasi'],
            ];
        }

        return [
            'tahun' => $year,
            'sampai_bulan' => $throughMonth,
            'kode_kantor' => $kodeKantor,
            'items' => $items,
        ];
    }

    public function apiGetDistribusiMakroKantor(array $input)
    {
        try {
            $baseDate = $input['harian_date'] ?? date('Y-m-d');
            $kodeKantorReq = $input['kode_kantor'] ?? 'konsolidasi';
            $korwilReq = strtoupper(trim((string) ($input['korwil'] ?? '')));
            $korwilRange = $this->getKorwilRange($korwilReq);
            $isConsolidated = $this->isConsolidatedScope((string) $kodeKantorReq, $korwilRange);
            $baseDateObj = new DateTime($baseDate);
            $previousDateObj = clone $baseDateObj;
            $previousDateObj->modify('last day of previous month');
            $previousDate = $previousDateObj->format('Y-m-d');

            $params = [
                ':tanggal_aktual' => $baseDate,
                ':tanggal_bulan_lalu' => $previousDate,
            ];
            $sqlKantor = "AND ah.kode_kantor BETWEEN '001' AND '028'";

            if ($korwilRange) {
                $sqlKantor = "AND ah.kode_kantor BETWEEN :kw_start AND :kw_end";
                $params[':kw_start'] = str_pad((string) $korwilRange[0], 3, '0', STR_PAD_LEFT);
                $params[':kw_end'] = str_pad((string) $korwilRange[1], 3, '0', STR_PAD_LEFT);
            } elseif (strtolower($kodeKantorReq) !== 'konsolidasi' && $kodeKantorReq !== '000') {
                $sqlKantor = "AND ah.kode_kantor = :kode_kantor";
                $params[':kode_kantor'] = str_pad((string) $kodeKantorReq, 3, '0', STR_PAD_LEFT);
            }

            $asetCodes = [
                '101','102','103','104','105','10601','10602','10604','10605','10606',
                '107','108','109','110','11102','112','113','116','117','118','119','120','121'
            ];
            $quoteCode = function ($code) {
                return $this->pdo->quote((string) $code);
            };
            $asetIn = implode(',', array_map($quoteCode, $asetCodes));
            // Distribusi ini menampilkan posisi setiap kantor, sehingga saldo 210
            // tidak boleh dieliminasi pada masing-masing cabang. Eliminasi 210
            // hanya berlaku sekali pada total konsolidasi di laporan keuangan.
            $trackedCodes = array_merge($asetCodes, ['4', '5']);
            $trackedIn = implode(',', array_map($quoteCode, array_unique($trackedCodes)));

            $sql = "
                SELECT
                    ah.tanggal,
                    ah.kode_kantor,
                    COALESCE(kk.nama_kantor, CONCAT('Kc. ', ah.kode_kantor)) AS nama_kantor,
                    SUM(CASE WHEN ah.kode_perk IN ($asetIn) THEN ah.saldo_akhir ELSE 0 END) AS aset,
                    SUM(CASE WHEN ah.kode_perk = '4' THEN ah.saldo_akhir ELSE 0 END) AS pendapatan,
                    SUM(CASE WHEN ah.kode_perk = '5' THEN ah.saldo_akhir ELSE 0 END) AS beban,
                    SUM(CASE WHEN ah.kode_perk = '4' THEN ah.saldo_akhir ELSE 0 END)
                    - SUM(CASE WHEN ah.kode_perk = '5' THEN ah.saldo_akhir ELSE 0 END) AS laba
                FROM acc_history ah
                LEFT JOIN kode_kantor kk ON kk.kode_kantor = ah.kode_kantor
                WHERE ah.tanggal IN (:tanggal_aktual, :tanggal_bulan_lalu)
                  $sqlKantor
                  AND ah.kode_perk IN ($trackedIn)
                GROUP BY ah.tanggal, ah.kode_kantor, kk.nama_kantor
                ORDER BY ah.kode_kantor ASC, ah.tanggal ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $metrics = ['aset' => [], 'laba' => [], 'pendapatan' => [], 'beban' => []];
            $kantorRows = [];

            foreach ($rows as $row) {
                $kode = str_pad((string) $row['kode_kantor'], 3, '0', STR_PAD_LEFT);
                if (!isset($kantorRows[$kode])) {
                    $kantorRows[$kode] = [
                        'kode_kantor' => $kode,
                        'nama_kantor' => $row['nama_kantor'] ?: ('Kc. ' . $kode),
                        'aktual' => ['aset' => 0, 'laba' => 0, 'pendapatan' => 0, 'beban' => 0],
                        'bulan_lalu' => ['aset' => 0, 'laba' => 0, 'pendapatan' => 0, 'beban' => 0],
                    ];
                }

                $bucket = ($row['tanggal'] === $baseDate) ? 'aktual' : 'bulan_lalu';
                foreach (array_keys($metrics) as $metric) {
                    $kantorRows[$kode][$bucket][$metric] = (float) ($row[$metric] ?? 0);
                }
            }

            foreach ($kantorRows as $kantor) {
                foreach (array_keys($metrics) as $metric) {
                    $aktual = (float) ($kantor['aktual'][$metric] ?? 0);
                    $bulanLalu = (float) ($kantor['bulan_lalu'][$metric] ?? 0);
                    $net = $aktual - $bulanLalu;
                    $nominal = ($metric === 'aset') ? $aktual : $net;
                    $growth = $bulanLalu != 0 ? round(($net / abs($bulanLalu)) * 100, 2) : ($aktual != 0 ? 100 : 0);

                    $metrics[$metric][] = [
                        'kode_kantor' => $kantor['kode_kantor'],
                        'nama_kantor' => $kantor['nama_kantor'],
                        'nominal' => $nominal,
                        'net' => $net,
                        'aktual' => $aktual,
                        'bulan_lalu' => $bulanLalu,
                        'growth' => $growth,
                    ];
                }
            }

            $buildSummary = function (array $items) {
                $total = array_sum(array_map(function ($item) {
                    return (float) $item['nominal'];
                }, $items));
                $count = count($items);
                $avg = $count ? $total / $count : 0;
                $top = $items;
                usort($top, function ($a, $b) {
                    return $b['nominal'] <=> $a['nominal'];
                });
                $bottom = $items;
                usort($bottom, function ($a, $b) {
                    return $a['nominal'] <=> $b['nominal'];
                });

                return [
                    'total' => $total,
                    'average' => $avg,
                    'top' => array_slice($top, 0, 3),
                    'bottom' => array_slice($bottom, 0, 3),
                ];
            };

            $scopeLabel = $korwilRange ? ('KORWIL ' . $korwilReq) : strtoupper($kodeKantorReq);
            $response = [];
            foreach ($metrics as $key => $items) {
                $response[$key] = [
                    'items' => $items,
                    'summary' => $buildSummary($items),
                ];
            }

            $singleBranchCode = str_pad((string) $kodeKantorReq, 3, '0', STR_PAD_LEFT);
            $isSingleBranch = !$korwilRange
                && !$isConsolidated
                && preg_match('/^\d{3}$/', $singleBranchCode)
                && $singleBranchCode >= '001'
                && $singleBranchCode <= '028';
            $branchYtd = $isSingleBranch
                ? $this->getBranchYtdMakro($singleBranchCode, $baseDate, $asetCodes)
                : null;

            sendResponse(200, "Berhasil memuat distribusi makro kantor (" . $scopeLabel . ")", [
                'scope' => $scopeLabel,
                'tanggal' => $baseDate,
                'tanggal_bulan_lalu' => $previousDate,
                'mode' => 'distribusi_makro',
                'keterangan' => 'Distribusi aset per kantor memakai seluruh komponen aset tanpa eliminasi kode 210. Eliminasi 210 hanya diterapkan pada total konsolidasi.',
                'metrics' => $response,
                'branch_ytd' => $branchYtd,
            ]);
        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat distribusi makro kantor: " . $e->getMessage(), null);
        }
    }

    /**
     * =================================================================
     * ENDPOINT: API FINANCIAL KPI DASHBOARD
     * (LDR, BOPO, CASA, ROA, ROE, Cash Ratio, Coverage Ratio, Top Biaya)
     * =================================================================
     */
    public function apiGetFinancialKPI(array $input) 
    {
        try {
            $baseDate = $input['harian_date'] ?? date('Y-m-d');
            $kodeKantorReq = $input['kode_kantor'] ?? 'konsolidasi';
            $korwilReq = strtoupper(trim((string) ($input['korwil'] ?? '')));
            $korwilRange = $this->getKorwilRange($korwilReq);
            $isConsolidated = $this->isConsolidatedScope((string) $kodeKantorReq, $korwilRange);

            // 1. 🔥 FILTER KANTOR (Bisa dipakai u/ acc_history & nominatif) 🔥
            $sqlKantorAcc = "";
            $sqlKantorNom = "";
            $params = [':tanggal' => $baseDate];

            if ($korwilRange) {
                $sqlKantorAcc = "AND LPAD(CAST(kode_kantor AS CHAR), 3, '0') BETWEEN :kw_start AND :kw_end";
                $sqlKantorNom = "AND LPAD(CAST(kode_cabang AS CHAR), 3, '0') BETWEEN :kw_start AND :kw_end";
                $params[':kw_start'] = $korwilRange[0];
                $params[':kw_end'] = $korwilRange[1];
            } elseif ($isConsolidated) {
                $sqlKantorAcc = "AND LPAD(CAST(kode_kantor AS CHAR), 3, '0') BETWEEN '000' AND '028'";
                // Nominatif konsolidasi tidak perlu filter cabang
            } else {
                $sqlKantorAcc = "AND LPAD(CAST(kode_kantor AS CHAR), 3, '0') = :kode_kantor";
                $sqlKantorNom = "AND LPAD(CAST(kode_cabang AS CHAR), 3, '0') = :kode_kantor";
                $params[':kode_kantor'] = str_pad($kodeKantorReq, 3, '0', STR_PAD_LEFT);
            }

            // =========================================================
            // QUERY 1: Hitung Semua Variabel dari ACC_HISTORY (Buku Besar)
            // =========================================================
            // Tambahan: '107' (CKPN Kredit) untuk hitung Coverage Ratio
            $asetCodes = [
                '101','102','103','104','105','10601','10602','10604','10605','10606',
                '107','108','109','110','11102','112','113','116','117','118','119','120','121'
            ];
            $asetIn = "'" . implode("','", $asetCodes) . "'";
            $assetAdjustmentSql = $isConsolidated
                ? " - SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '210' THEN saldo_akhir ELSE 0 END)"
                : '';
            $sqlRasio = "
                SELECT 
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) IN ($asetIn) THEN saldo_akhir ELSE 0 END)
                    $assetAdjustmentSql AS total_aset,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '3' THEN saldo_akhir ELSE 0 END) AS total_ekuitas,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '101' THEN saldo_akhir ELSE 0 END) AS kas,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '104' THEN saldo_akhir ELSE 0 END) AS penempatan_bank,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '201' THEN saldo_akhir ELSE 0 END) AS kewajiban_segera,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '106' THEN saldo_akhir ELSE 0 END) AS kredit,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '107' THEN saldo_akhir ELSE 0 END) AS ckpn_kredit,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '20401' THEN saldo_akhir ELSE 0 END) AS tabungan,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '20402' THEN saldo_akhir ELSE 0 END) AS deposito,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '4' THEN saldo_akhir ELSE 0 END) AS total_pendapatan,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '5' THEN saldo_akhir ELSE 0 END) AS total_biaya,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '401' THEN saldo_akhir ELSE 0 END) AS pend_ops,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '501' THEN saldo_akhir ELSE 0 END) AS biaya_ops
                FROM acc_history
                WHERE tanggal = :tanggal $sqlKantorAcc
            ";
            
            $stmt1 = $this->pdo->prepare($sqlRasio);
            $stmt1->execute($params);
            $rasioData = $stmt1->fetch(PDO::FETCH_ASSOC);

            // Ekstrak Nominal Acc History
            $totalAset    = (float) ($rasioData['total_aset'] ?? 0);
            $totalEkuitas = (float) ($rasioData['total_ekuitas'] ?? 0);
            $kas          = (float) ($rasioData['kas'] ?? 0);
            $penempatanBank = (float) ($rasioData['penempatan_bank'] ?? 0);
            $kwjbnSegera  = (float) ($rasioData['kewajiban_segera'] ?? 0);
            
            $kredit       = (float) ($rasioData['kredit'] ?? 0);
            // Saldo CKPN biasanya bernilai minus (kredit), jadi kita mutlakkan pakai abs()
            $ckpnKredit   = abs((float) ($rasioData['ckpn_kredit'] ?? 0)); 
            $tabungan     = (float) ($rasioData['tabungan'] ?? 0);
            $deposito     = (float) ($rasioData['deposito'] ?? 0);
            
            $pendOps      = (float) ($rasioData['pend_ops'] ?? 0);
            $biayaOps     = (float) ($rasioData['biaya_ops'] ?? 0);
            
            $labaBerjalan = ((float) ($rasioData['total_pendapatan'] ?? 0)) - ((float) ($rasioData['total_biaya'] ?? 0));
            $dpk          = $tabungan + $deposito;
            $alatLikuid   = $kas + $penempatanBank;

            // =========================================================
            // QUERY 2: Tarik Total NPL dari tabel NOMINATIF (Bocoran Broku)
            // =========================================================
            $sqlNPL = "
                SELECT SUM(baki_debet) as total_npl
                FROM nominatif
                WHERE created = :tanggal 
                  AND kolektibilitas IN ('KL', 'D', 'M')
                  $sqlKantorNom
            ";
            $stmtNpl = $this->pdo->prepare($sqlNPL);
            $stmtNpl->execute($params);
            $nplData = $stmtNpl->fetch(PDO::FETCH_ASSOC);
            $totalNpl = (float) ($nplData['total_npl'] ?? 0);


            // =========================================================
            // PERHITUNGAN RASIO KESEHATAN BANK
            // =========================================================
            $bopo = ($pendOps > 0) ? ($biayaOps / $pendOps) * 100 : 0;
            $ldr  = ($dpk > 0) ? ($kredit / $dpk) * 100 : 0;
            $casa = ($dpk > 0) ? ($tabungan / $dpk) * 100 : 0; 

            // Disetahunkan (Annualized) untuk ROA & ROE
            $currentMonth = (int) date('m', strtotime($baseDate));
            $labaDisetahunkan = ($currentMonth > 0) ? ($labaBerjalan / $currentMonth) * 12 : $labaBerjalan;

            $roa = ($totalAset > 0) ? ($labaDisetahunkan / $totalAset) * 100 : 0;
            $roe = ($totalEkuitas > 0) ? ($labaDisetahunkan / $totalEkuitas) * 100 : 0;
            $cashRatio = ($kwjbnSegera > 0) ? ($alatLikuid / $kwjbnSegera) * 100 : 0;
            
            // COVERAGE RATIO: CKPN vs NPL
            $coverageRatio = ($totalNpl > 0) ? ($ckpnKredit / $totalNpl) * 100 : ($ckpnKredit > 0 ? 100 : 0);

            // =========================================================
            // QUERY 3: Cari Top 5 Biaya Terbesar (Leaf Node Filter)
            // =========================================================
            $sqlTopBiaya = "
                SELECT 
                    TRIM(CAST(kode_perk AS CHAR)) as kode, 
                    MAX(NULLIF(TRIM(nama_perk), '')) AS nama,
                    SUM(saldo_akhir) as total_biaya
                FROM acc_history
                WHERE tanggal = :tanggal $sqlKantorAcc
                  AND TRIM(CAST(kode_perk AS CHAR)) LIKE '5%'
                GROUP BY TRIM(CAST(kode_perk AS CHAR))
                HAVING SUM(saldo_akhir) > 0
            ";
            
            $stmt2 = $this->pdo->prepare($sqlTopBiaya);
            $stmt2->execute($params);
            $allBiayaRaw = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            $allCodes = array_column($allBiayaRaw, 'kode');
            $leafBiaya = [];
            
            foreach ($allBiayaRaw as $row) {
                $kode = $row['kode'];
                $isParent = false;
                
                foreach ($allCodes as $otherKode) {
                    if ($kode !== $otherKode && strpos($otherKode, $kode) === 0) {
                        $isParent = true;
                        break;
                    }
                }
                
                if (!$isParent) {
                    $leafBiaya[] = $row;
                }
            }

            $leafBiaya = array_values(array_filter($leafBiaya, function($row) {
                $kode = (string) ($row['kode'] ?? '');
                $nama = strtoupper(trim((string) ($row['nama'] ?? '')));
                if (strpos($kode, '50101') === 0 || $kode === '50203') {
                    return false;
                }
                return strpos($nama, 'TAMADES') === false
                    && strpos($nama, 'BEBAN BUNGA') === false
                    && strpos($nama, 'BEBAN BG') === false;
            }));

            usort($leafBiaya, function($a, $b) {
                return $b['total_biaya'] <=> $a['total_biaya']; 
            });

            $top5BiayaRaw = array_slice($leafBiaya, 0, 5);

            $topBiaya = [];
            foreach($top5BiayaRaw as $row) {
                $topBiaya[] = [
                    'kode'    => $row['kode'],
                    'nama'    => trim((string) ($row['nama'] ?? '')) !== '' ? $row['nama'] : 'Biaya / Beban Lainnya',
                    'nominal' => (float) $row['total_biaya']
                ];
            }

            // =========================================================
            // BUNGKUS KE DALAM RESPONSE JSON
            // =========================================================
            $scopeLabel = $korwilRange ? ('KORWIL ' . $korwilReq) : strtoupper($kodeKantorReq);

            $responseData = [
                'rasio' => [
                    'bopo_persen'           => round($bopo, 2),
                    'ldr_persen'            => round($ldr, 2),
                    'casa_persen'           => round($casa, 2),
                    'roa_persen'            => round($roa, 2),
                    'roe_persen'            => round($roe, 2),
                    'cash_ratio_persen'     => round($cashRatio, 2),
                    'coverage_ratio_persen' => round($coverageRatio, 2), // ✨ INI DIA JAGOAN BARUNYA ✨
                    
                    'detail_nominal' => [
                        'total_kredit'      => $kredit,
                        'total_dpk'         => $dpk,
                        'pend_operasional'  => $pendOps,
                        'biaya_operasional' => $biayaOps,
                        'laba_disetahunkan' => $labaDisetahunkan,
                        'total_aset'        => $totalAset,
                        'total_ekuitas'     => $totalEkuitas,
                        'alat_likuid'       => $alatLikuid,
                        'kewajiban_segera'  => $kwjbnSegera,
                        'ckpn_kredit'       => $ckpnKredit,
                        'total_npl'         => $totalNpl
                    ]
                ],
                'top_5_biaya' => $topBiaya
            ];

            sendResponse(200, "Berhasil memuat KPI Kesehatan Bank (" . $scopeLabel . ")", $responseData);

        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat KPI: " . $e->getMessage(), null);
        }
    }

    /**
     * =================================================================
     * ENDPOINT: API SUMMARY PERBANDINGAN (Aktual vs M-1 vs Y-1)
     * =================================================================
     */
    public function GetSummaryPerbandingan(array $input) 
    {
        try {
            $baseDate = $input['harian_date'] ?? date('Y-m-d');
            $kodeKantorReq = $input['kode_kantor'] ?? 'konsolidasi';
            $korwilReq = strtoupper(trim((string) ($input['korwil'] ?? '')));
            $korwilRange = $this->getKorwilRange($korwilReq);
            $isConsolidated = $this->isConsolidatedScope((string) $kodeKantorReq, $korwilRange);

            // 1. Tentukan 3 Titik Waktu
            $baseDateObj = new DateTime($baseDate);
            $dateCurrent = $baseDateObj->format('Y-m-d');

            $dLastMonth = clone $baseDateObj;
            $dLastMonth->modify('last day of previous month');
            $dateLastMonth = $dLastMonth->format('Y-m-d');

            $dLastYear = clone $baseDateObj;
            $dLastYear->modify('-1 year');
            $dateLastYear = $dLastYear->format('Y') . '-12-31';

            // 2. Siapkan Filter Kantor
            $sqlKantor = "";
            $params = [$dateCurrent, $dateLastMonth, $dateLastYear];

            if ($korwilRange) {
                $sqlKantor = "AND LPAD(CAST(kode_kantor AS CHAR), 3, '0') BETWEEN ? AND ?";
                $params[] = $korwilRange[0];
                $params[] = $korwilRange[1];
            } elseif ($isConsolidated) {
                $sqlKantor = "AND LPAD(CAST(kode_kantor AS CHAR), 3, '0') BETWEEN '000' AND '028'";
            } else {
                $sqlKantor = "AND LPAD(CAST(kode_kantor AS CHAR), 3, '0') = ?";
                $params[] = str_pad($kodeKantorReq, 3, '0', STR_PAD_LEFT);
            }

            // 3. Query Super Cepat: Tarik Akun Makro (1-5) & Akun Mikro Pembentuk Rasio
            $asetCodes = [
                '101','102','103','104','105','10601','10602','10604','10605','10606',
                '107','108','109','110','11102','112','113','116','117','118','119','120','121'
            ];
            $asetIn = "'" . implode("','", $asetCodes) . "'";
            $assetAdjustmentSql = $isConsolidated
                ? " - SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '210' THEN saldo_akhir ELSE 0 END)"
                : '';
            $sql = "
                SELECT 
                    tanggal,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) IN ($asetIn) THEN saldo_akhir ELSE 0 END)
                    $assetAdjustmentSql AS aset,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '2' THEN saldo_akhir ELSE 0 END) AS kewajiban,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '3' THEN saldo_akhir ELSE 0 END) AS ekuitas,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '101' THEN saldo_akhir ELSE 0 END) AS kas,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '104' THEN saldo_akhir ELSE 0 END) AS penempatan_bank,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '201' THEN saldo_akhir ELSE 0 END) AS kewajiban_segera,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '4' THEN saldo_akhir ELSE 0 END) AS pendapatan,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '5' THEN saldo_akhir ELSE 0 END) AS biaya,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '106' THEN saldo_akhir ELSE 0 END) AS kredit,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '20401' THEN saldo_akhir ELSE 0 END) AS tabungan,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '20402' THEN saldo_akhir ELSE 0 END) AS deposito,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) IN ('20401', '20402') THEN saldo_akhir ELSE 0 END) AS dpk,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '401' THEN saldo_akhir ELSE 0 END) AS pend_ops,
                    SUM(CASE WHEN TRIM(CAST(kode_perk AS CHAR)) = '501' THEN saldo_akhir ELSE 0 END) AS biaya_ops
                FROM acc_history
                WHERE tanggal IN (?, ?, ?) $sqlKantor
                GROUP BY tanggal
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 4. Mapping Data berdasarkan Tanggal (Siapkan default 0)
            $defaultData = [
                'aset' => 0, 'kewajiban' => 0, 'ekuitas' => 0, 'pendapatan' => 0, 'biaya' => 0, 'dpk' => 0,
                'kredit' => 0, 'tabungan' => 0, 'deposito' => 0, 'pend_ops' => 0, 'biaya_ops' => 0,
                'kas' => 0, 'penempatan_bank' => 0, 'kewajiban_segera' => 0
            ];

            $dataMap = [
                $dateCurrent   => $defaultData,
                $dateLastMonth => $defaultData,
                $dateLastYear  => $defaultData,
            ];

            foreach ($results as $row) {
                $tgl = $row['tanggal'];
                foreach ($defaultData as $key => $val) {
                    $dataMap[$tgl][$key] = (float) $row[$key];
                }
                $dataMap[$tgl]['dpk'] = ((float) ($row['tabungan'] ?? 0)) + ((float) ($row['deposito'] ?? 0));
            }

            // 5. Fungsi Helper Buat Ngitung Pertumbuhan Nominal (%)
            $calculateGrowth = function($current, $past) {
                if ($past == 0) return $current > 0 ? 100 : 0;
                return round((($current - $past) / abs($past)) * 100, 2);
            };

            // Fungsi Helper Buat Ngitung Rasio (Persentase Kesehatan)
            $calculateRasio = function($data, $date) {
                $dpk = $data['tabungan'] + $data['deposito'];
                $labaBerjalan = $data['pendapatan'] - $data['biaya'];
                $monthNumber = max(1, (int) date('n', strtotime($date)));
                $labaDisetahunkan = ($labaBerjalan / $monthNumber) * 12;
                $alatLikuid = $data['kas'] + $data['penempatan_bank'];
                return [
                    'bopo' => ($data['pend_ops'] > 0) ? ($data['biaya_ops'] / $data['pend_ops']) * 100 : 0,
                    'ldr'  => ($dpk > 0) ? ($data['kredit'] / $dpk) * 100 : 0,
                    'casa' => ($dpk > 0) ? ($data['tabungan'] / $dpk) * 100 : 0,
                    'roa'  => ($data['aset'] > 0) ? ($labaDisetahunkan / $data['aset']) * 100 : 0,
                    'roe'  => ($data['ekuitas'] > 0) ? ($labaDisetahunkan / $data['ekuitas']) * 100 : 0,
                    'cash' => ($data['kewajiban_segera'] > 0) ? ($alatLikuid / $data['kewajiban_segera']) * 100 : 0,
                ];
            };

            // 6. Format Response Makro (Nominal Aset, Laba Rugi, dll)
            $kategori = ['aset', 'kewajiban', 'ekuitas', 'pendapatan', 'biaya'];
            $summaryData = [];

            foreach ($kategori as $kat) {
                $valCurr  = $dataMap[$dateCurrent][$kat];
                $valPrevM = $dataMap[$dateLastMonth][$kat];
                $valPrevY = $dataMap[$dateLastYear][$kat];

                $summaryData[$kat] = [
                    'nominal_aktual'     => $valCurr,
                    'nominal_bulan_lalu' => $valPrevM,
                    'nominal_tahun_lalu' => $valPrevY,
                    'growth_mom'         => $calculateGrowth($valCurr, $valPrevM),
                    'growth_yoy'         => $calculateGrowth($valCurr, $valPrevY) 
                ];
            }

            // Laba Rugi (Pendapatan - Biaya)
            $lrCurr  = $dataMap[$dateCurrent]['pendapatan'] - $dataMap[$dateCurrent]['biaya'];
            $lrPrevM = $dataMap[$dateLastMonth]['pendapatan'] - $dataMap[$dateLastMonth]['biaya'];
            $lrPrevY = $dataMap[$dateLastYear]['pendapatan'] - $dataMap[$dateLastYear]['biaya'];

            $summaryData['laba_rugi'] = [
                'nominal_aktual'     => $lrCurr,
                'nominal_bulan_lalu' => $lrPrevM,
                'nominal_tahun_lalu' => $lrPrevY,
                'growth_mom'         => $calculateGrowth($lrCurr, $lrPrevM),
                'growth_yoy'         => $calculateGrowth($lrCurr, $lrPrevY)
            ];

            $dpkCurr  = $dataMap[$dateCurrent]['tabungan'] + $dataMap[$dateCurrent]['deposito'];
            $dpkPrevM = $dataMap[$dateLastMonth]['tabungan'] + $dataMap[$dateLastMonth]['deposito'];
            $dpkPrevY = $dataMap[$dateLastYear]['tabungan'] + $dataMap[$dateLastYear]['deposito'];

            $summaryData['dpk'] = [
                'nominal_aktual'     => $dpkCurr,
                'nominal_bulan_lalu' => $dpkPrevM,
                'nominal_tahun_lalu' => $dpkPrevY,
                'growth_mom'         => $calculateGrowth($dpkCurr, $dpkPrevM),
                'growth_yoy'         => $calculateGrowth($dpkCurr, $dpkPrevY)
            ];

            // 7. Format Response Rasio Kesehatan (Khusus Persentase)
            $rasioCurr  = $calculateRasio($dataMap[$dateCurrent], $dateCurrent);
            $rasioPrevM = $calculateRasio($dataMap[$dateLastMonth], $dateLastMonth);
            $rasioPrevY = $calculateRasio($dataMap[$dateLastYear], $dateLastYear);

            $rasioData = [];
            foreach (['bopo', 'ldr', 'casa', 'roa', 'roe', 'cash'] as $r) {
                $rasioData[$r] = [
                    'persen_aktual'     => round($rasioCurr[$r], 2),
                    'persen_bulan_lalu' => round($rasioPrevM[$r], 2),
                    'persen_tahun_lalu' => round($rasioPrevY[$r], 2),
                    'delta_mom'         => round($rasioCurr[$r] - $rasioPrevM[$r], 2), 
                    'delta_yoy'         => round($rasioCurr[$r] - $rasioPrevY[$r], 2)
                ];
            }

            // 8. Rangkum Hasil
            $scopeLabel = $korwilRange ? ('KORWIL ' . $korwilReq) : strtoupper($kodeKantorReq);

            $responseData = [
                'info_kantor'  => $scopeLabel,
                'info_tanggal' => [
                    'aktual'     => $dateCurrent,
                    'bulan_lalu' => $dateLastMonth,
                    'tahun_lalu' => $dateLastYear
                ],
                'makro' => $summaryData,
                'kesehatan_rasio' => $rasioData
            ];

            sendResponse(200, "Berhasil memuat Summary Perbandingan (" . $scopeLabel . ")", $responseData);

        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat Summary: " . $e->getMessage(), null);
        }
    }

    public function apiGetTvMakroSummary(array $input)
    {
        try {
            $baseDate = $input['harian_date'] ?? date('Y-m-d');
            $kodeKantorReq = $input['kode_kantor'] ?? 'konsolidasi';
            $korwilReq = strtoupper(trim((string) ($input['korwil'] ?? '')));
            $korwilRange = $this->getKorwilRange($korwilReq);
            $isConsolidated = $this->isConsolidatedScope((string) $kodeKantorReq, $korwilRange);

            $baseDateObj = new DateTime($baseDate);
            $dateCurrent = $baseDateObj->format('Y-m-d');
            $dateLastMonthObj = clone $baseDateObj;
            $dateLastMonthObj->modify('last day of previous month');
            $dateLastMonth = $dateLastMonthObj->format('Y-m-d');
            $dateLastYearObj = clone $baseDateObj;
            $dateLastYearObj->modify('-1 year');
            $dateLastYear = $dateLastYearObj->format('Y') . '-12-31';

            $params = [
                ':date_current' => $dateCurrent,
                ':date_last_month' => $dateLastMonth,
                ':date_last_year' => $dateLastYear,
            ];
            $sqlKantorAcc = '';
            $sqlKantorNom = '';

            if ($korwilRange) {
                $sqlKantorAcc = "AND kode_kantor BETWEEN :kw_start AND :kw_end";
                $sqlKantorNom = "AND kode_cabang BETWEEN :kw_start AND :kw_end";
                $params[':kw_start'] = str_pad((string) $korwilRange[0], 3, '0', STR_PAD_LEFT);
                $params[':kw_end'] = str_pad((string) $korwilRange[1], 3, '0', STR_PAD_LEFT);
            } elseif ($isConsolidated) {
                $sqlKantorAcc = "AND kode_kantor BETWEEN '000' AND '028'";
            } else {
                $sqlKantorAcc = "AND kode_kantor = :kode_kantor";
                $sqlKantorNom = "AND kode_cabang = :kode_kantor";
                $params[':kode_kantor'] = str_pad((string) $kodeKantorReq, 3, '0', STR_PAD_LEFT);
            }

            $asetCodes = [
                '101','102','103','104','105','10601','10602','10604','10605','10606',
                '107','108','109','110','11102','112','113','116','117','118','119','120','121'
            ];
            $quoteCode = function ($code) {
                return $this->pdo->quote((string) $code);
            };
            $asetIn = implode(',', array_map($quoteCode, $asetCodes));
            $trackedCodes = array_unique(array_merge($asetCodes, [
                '210', '2', '3', '101', '104', '10401', '10402', '105', '201', '20202', '205', '211', '4', '5', '106', '10601',
                '10606', '107', '204', '20401', '20402', '20603', '30106', '401', '40101', '501', '50101'
            ]));
            $trackedIn = implode(',', array_map($quoteCode, $trackedCodes));
            $assetAdjustmentSql = $isConsolidated
                ? " - SUM(CASE WHEN kode_perk = '210' THEN saldo_akhir ELSE 0 END)"
                : '';

            $sql = "
                SELECT
                    tanggal,
                    SUM(CASE WHEN kode_perk IN ($asetIn) THEN saldo_akhir ELSE 0 END)
                    $assetAdjustmentSql AS aset,
                    SUM(CASE WHEN kode_perk = '2' THEN saldo_akhir ELSE 0 END) AS kewajiban,
                    SUM(CASE WHEN kode_perk = '3' THEN saldo_akhir ELSE 0 END) AS ekuitas,
                    SUM(CASE WHEN kode_perk = '101' THEN saldo_akhir ELSE 0 END) AS kas,
                    SUM(CASE WHEN kode_perk = '104' THEN saldo_akhir ELSE 0 END) AS ppbl,
                    SUM(CASE WHEN kode_perk = '10401' THEN saldo_akhir ELSE 0 END) AS ppbl_giro,
                    SUM(CASE WHEN kode_perk = '10402' THEN saldo_akhir ELSE 0 END) AS ppbl_tabungan,
                    SUM(CASE WHEN kode_perk = '105' THEN saldo_akhir ELSE 0 END) AS ckpn_ppbl,
                    SUM(CASE WHEN kode_perk = '201' THEN saldo_akhir ELSE 0 END) AS kewajiban_segera,
                    SUM(CASE WHEN kode_perk = '20202' THEN saldo_akhir ELSE 0 END) AS utang_bunga_deposito,
                    SUM(CASE WHEN kode_perk = '205' THEN saldo_akhir ELSE 0 END) AS simpanan_bank_lain,
                    SUM(CASE WHEN kode_perk = '211' THEN saldo_akhir ELSE 0 END) AS liabilitas_lainnya,
                    SUM(CASE WHEN kode_perk = '4' THEN saldo_akhir ELSE 0 END) AS pendapatan,
                    SUM(CASE WHEN kode_perk = '5' THEN saldo_akhir ELSE 0 END) AS biaya,
                    SUM(CASE WHEN kode_perk = '106' THEN saldo_akhir ELSE 0 END) AS kredit,
                    SUM(CASE WHEN kode_perk = '10601' THEN saldo_akhir ELSE 0 END) AS kredit_baki_debet,
                    SUM(CASE WHEN kode_perk = '10606' THEN saldo_akhir ELSE 0 END) AS saldo_bank_ead,
                    SUM(CASE WHEN kode_perk = '107' THEN saldo_akhir ELSE 0 END) AS ckpn_kredit,
                    SUM(CASE WHEN kode_perk = '204' THEN saldo_akhir ELSE 0 END) AS simpanan_pihak_ketiga,
                    SUM(CASE WHEN kode_perk = '20401' THEN saldo_akhir ELSE 0 END) AS tabungan,
                    SUM(CASE WHEN kode_perk = '20402' THEN saldo_akhir ELSE 0 END) AS deposito,
                    SUM(CASE WHEN kode_perk = '20603' THEN saldo_akhir ELSE 0 END) AS deposito_bank_lain_gt_3_bulan,
                    SUM(CASE WHEN kode_perk = '30106' THEN saldo_akhir ELSE 0 END) AS modal_pinjaman,
                    SUM(CASE WHEN kode_perk = '401' THEN saldo_akhir ELSE 0 END) AS pend_ops,
                    SUM(CASE WHEN kode_perk = '501' THEN saldo_akhir ELSE 0 END) AS biaya_ops,
                    SUM(CASE WHEN kode_perk = '40101' THEN saldo_akhir ELSE 0 END) AS pend_bunga,
                    SUM(CASE WHEN kode_perk = '50101' THEN saldo_akhir ELSE 0 END) AS beban_bunga
                FROM acc_history
                WHERE tanggal IN (:date_current, :date_last_month, :date_last_year)
                  $sqlKantorAcc
                  AND kode_perk IN ($trackedIn)
                GROUP BY tanggal
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sqlPerkiraan = "
                SELECT kode_perk, MAX(nama_perk) AS nama_perk
                FROM acc_history
                WHERE tanggal IN (:date_current, :date_last_month, :date_last_year)
                  $sqlKantorAcc
                  AND kode_perk IN ($trackedIn)
                GROUP BY kode_perk
            ";
            $stmtPerkiraan = $this->pdo->prepare($sqlPerkiraan);
            $stmtPerkiraan->execute($params);
            $perkiraanRows = $stmtPerkiraan->fetchAll(PDO::FETCH_ASSOC);
            $perkiraanMap = [];
            foreach ($perkiraanRows as $row) {
                $perkiraanMap[(string) $row['kode_perk']] = $row['nama_perk'] ?: (string) $row['kode_perk'];
            }

            $defaultData = [
                'aset' => 0, 'kewajiban' => 0, 'ekuitas' => 0, 'kas' => 0, 'ppbl' => 0,
                'ppbl_giro' => 0, 'ppbl_tabungan' => 0, 'ckpn_ppbl' => 0,
                'kewajiban_segera' => 0, 'utang_bunga_deposito' => 0, 'simpanan_bank_lain' => 0, 'liabilitas_lainnya' => 0,
                'pendapatan' => 0, 'biaya' => 0, 'kredit' => 0,
                'kredit_baki_debet' => 0, 'saldo_bank_ead' => 0, 'ckpn_kredit' => 0,
                'simpanan_pihak_ketiga' => 0, 'tabungan' => 0, 'deposito' => 0,
                'deposito_bank_lain_gt_3_bulan' => 0, 'modal_pinjaman' => 0,
                'pend_ops' => 0, 'biaya_ops' => 0,
                'pend_bunga' => 0, 'beban_bunga' => 0,
            ];
            $dataMap = [
                $dateCurrent => $defaultData,
                $dateLastMonth => $defaultData,
                $dateLastYear => $defaultData,
            ];
            foreach ($rows as $row) {
                $tgl = $row['tanggal'];
                if (!isset($dataMap[$tgl])) {
                    continue;
                }
                foreach ($defaultData as $key => $val) {
                    $dataMap[$tgl][$key] = (float) ($row[$key] ?? 0);
                }
            }

            $nplParams = [
                ':date_current' => $dateCurrent,
                ':date_last_month' => $dateLastMonth,
            ];
            if ($korwilRange) {
                $nplParams[':kw_start'] = str_pad((string) $korwilRange[0], 3, '0', STR_PAD_LEFT);
                $nplParams[':kw_end'] = str_pad((string) $korwilRange[1], 3, '0', STR_PAD_LEFT);
            } elseif (!$isConsolidated) {
                $nplParams[':kode_kantor'] = str_pad((string) $kodeKantorReq, 3, '0', STR_PAD_LEFT);
            }
            $sqlNpl = "
                SELECT created, SUM(baki_debet) AS total_npl
                FROM nominatif
                WHERE created IN (:date_current, :date_last_month)
                  AND kolektibilitas IN ('KL', 'D', 'M')
                  $sqlKantorNom
                GROUP BY created
            ";
            $stmtNpl = $this->pdo->prepare($sqlNpl);
            $stmtNpl->execute($nplParams);
            $nplMap = [
                $dateCurrent => 0,
                $dateLastMonth => 0,
            ];
            foreach ($stmtNpl->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $nplMap[$row['created']] = (float) ($row['total_npl'] ?? 0);
            }
            $totalNpl = (float) ($nplMap[$dateCurrent] ?? 0);
            $totalNplPrev = (float) ($nplMap[$dateLastMonth] ?? 0);

            $calculateGrowth = function ($current, $past) {
                if ((float) $past == 0.0) {
                    return (float) $current == 0.0 ? 0.0 : 100.0;
                }
                return round((($current - $past) / abs($past)) * 100, 2);
            };
            $buildNominalSummary = function ($key) use ($dataMap, $dateCurrent, $dateLastMonth, $dateLastYear, $calculateGrowth) {
                $curr = $dataMap[$dateCurrent][$key] ?? 0;
                $prevM = $dataMap[$dateLastMonth][$key] ?? 0;
                $prevY = $dataMap[$dateLastYear][$key] ?? 0;
                return [
                    'nominal_aktual' => $curr,
                    'nominal_bulan_lalu' => $prevM,
                    'nominal_tahun_lalu' => $prevY,
                    'growth_mom' => $calculateGrowth($curr, $prevM),
                    'growth_yoy' => $calculateGrowth($curr, $prevY),
                ];
            };
            $getAverageAsetProduktif = function ($targetDate) use ($sqlKantorAcc, $korwilRange, $kodeKantorReq, $isConsolidated) {
                $targetObj = new DateTime($targetDate);
                $year = $targetObj->format('Y');
                $month = (int) $targetObj->format('n');
                $dates = [];

                for ($m = 1; $m <= $month; $m++) {
                    if ($m === $month) {
                        $dates[] = $targetObj->format('Y-m-d');
                    } else {
                        $dates[] = (new DateTime($year . '-' . str_pad((string) $m, 2, '0', STR_PAD_LEFT) . '-01'))->format('Y-m-t');
                    }
                }

                $paramsAvg = [];
                $placeholders = [];
                foreach (array_values(array_unique($dates)) as $idx => $date) {
                    $key = ':avg_date_' . $idx;
                    $placeholders[] = $key;
                    $paramsAvg[$key] = $date;
                }

                if ($korwilRange) {
                    $paramsAvg[':kw_start'] = str_pad((string) $korwilRange[0], 3, '0', STR_PAD_LEFT);
                    $paramsAvg[':kw_end'] = str_pad((string) $korwilRange[1], 3, '0', STR_PAD_LEFT);
                } elseif (!$isConsolidated) {
                    $paramsAvg[':kode_kantor'] = str_pad((string) $kodeKantorReq, 3, '0', STR_PAD_LEFT);
                }

                $sqlAvg = "
                    SELECT tanggal, SUM(saldo_akhir) AS total_aset_produktif
                    FROM acc_history
                    WHERE tanggal IN (" . implode(',', $placeholders) . ")
                      $sqlKantorAcc
                      AND kode_perk IN ('104', '10601', '10606')
                    GROUP BY tanggal
                ";
                $stmtAvg = $this->pdo->prepare($sqlAvg);
                $stmtAvg->execute($paramsAvg);
                $rowsAvg = $stmtAvg->fetchAll(PDO::FETCH_ASSOC);
                if (!$rowsAvg) {
                    return 0.0;
                }
                $total = 0.0;
                foreach ($rowsAvg as $row) {
                    $total += (float) ($row['total_aset_produktif'] ?? 0);
                }
                return $total / count($rowsAvg);
            };

            $avgProduktifCurr = $getAverageAsetProduktif($dateCurrent);
            $avgProduktifPrevM = $getAverageAsetProduktif($dateLastMonth);

            $calculateRasio = function ($data, $date, $avgAsetProduktif = 0) {
                $dpk = $data['tabungan'] + $data['deposito'];
                $totalDanaDiterima = $data['simpanan_pihak_ketiga'] + $data['deposito_bank_lain_gt_3_bulan'] + $data['modal_pinjaman'];
                $labaBerjalan = $data['pendapatan'] - $data['biaya'];
                $monthNumber = max(1, (int) date('n', strtotime($date)));
                $labaDisetahunkan = ($labaBerjalan / $monthNumber) * 12;
                $asetLikuidTerhadapAset = $data['kas'] + $data['ppbl_giro'] + $data['ppbl_tabungan'];
                $hutangLancar = $data['kewajiban_segera'] + $data['tabungan'] + $data['utang_bunga_deposito'] + $data['simpanan_bank_lain'] + $data['liabilitas_lainnya'];
                $pendapatanBungaBersih = $data['pend_bunga'] - $data['beban_bunga'];
                $pendapatanBungaBersihDisetahunkan = ($pendapatanBungaBersih / $monthNumber) * 12;
                return [
                    'bopo' => ($data['pend_ops'] > 0) ? ($data['biaya_ops'] / $data['pend_ops']) * 100 : 0,
                    'ldr' => ($totalDanaDiterima > 0) ? ($data['kredit_baki_debet'] / $totalDanaDiterima) * 100 : 0,
                    'casa' => ($dpk > 0) ? ($data['tabungan'] / $dpk) * 100 : 0,
                    'roa' => ($data['aset'] > 0) ? ($labaDisetahunkan / $data['aset']) * 100 : 0,
                    'roe' => ($data['ekuitas'] > 0) ? ($labaDisetahunkan / $data['ekuitas']) * 100 : 0,
                    'cash' => ($hutangLancar > 0) ? ($asetLikuidTerhadapAset / $hutangLancar) * 100 : 0,
                    'aset_likuid' => ($data['aset'] > 0) ? ($asetLikuidTerhadapAset / $data['aset']) * 100 : 0,
                    'nim' => ($avgAsetProduktif > 0) ? ($pendapatanBungaBersihDisetahunkan / $avgAsetProduktif) * 100 : 0,
                ];
            };

            $curr = $dataMap[$dateCurrent];
            $prev = $dataMap[$dateLastMonth];
            $dpkCurr = $curr['tabungan'] + $curr['deposito'];
            $dpkPrev = $prev['tabungan'] + $prev['deposito'];
            $labaCurr = $curr['pendapatan'] - $curr['biaya'];
            $labaPrev = $prev['pendapatan'] - $prev['biaya'];
            $labaSetelahPajak = $labaCurr - ($labaCurr * 0.22);

            $summaryData = [
                'aset' => $buildNominalSummary('aset'),
                'kewajiban' => $buildNominalSummary('kewajiban'),
                'ekuitas' => $buildNominalSummary('ekuitas'),
                'pendapatan' => $buildNominalSummary('pendapatan'),
                'biaya' => $buildNominalSummary('biaya'),
                'laba_rugi' => [
                    'nominal_aktual' => $labaCurr,
                    'nominal_bulan_lalu' => $labaPrev,
                    'nominal_tahun_lalu' => ($dataMap[$dateLastYear]['pendapatan'] ?? 0) - ($dataMap[$dateLastYear]['biaya'] ?? 0),
                    'growth_mom' => $calculateGrowth($labaCurr, $labaPrev),
                    'growth_yoy' => $calculateGrowth($labaCurr, (($dataMap[$dateLastYear]['pendapatan'] ?? 0) - ($dataMap[$dateLastYear]['biaya'] ?? 0))),
                ],
                'dpk' => [
                    'nominal_aktual' => $dpkCurr,
                    'nominal_bulan_lalu' => $dpkPrev,
                    'nominal_tahun_lalu' => ($dataMap[$dateLastYear]['tabungan'] ?? 0) + ($dataMap[$dateLastYear]['deposito'] ?? 0),
                    'growth_mom' => $calculateGrowth($dpkCurr, $dpkPrev),
                    'growth_yoy' => $calculateGrowth($dpkCurr, (($dataMap[$dateLastYear]['tabungan'] ?? 0) + ($dataMap[$dateLastYear]['deposito'] ?? 0))),
                ],
                'npl' => [
                    'nominal_aktual' => $totalNpl,
                    'nominal_bulan_lalu' => $totalNplPrev,
                    'nominal_tahun_lalu' => 0,
                    'growth_mom' => $calculateGrowth($totalNpl, $totalNplPrev),
                    'growth_yoy' => 0,
                    'persen_aktual' => ($curr['kredit_baki_debet'] > 0) ? ($totalNpl / $curr['kredit_baki_debet']) * 100 : 0,
                    'persen_bulan_lalu' => ($prev['kredit_baki_debet'] > 0) ? ($totalNplPrev / $prev['kredit_baki_debet']) * 100 : 0,
                ],
            ];

            $rasioCurr = $calculateRasio($curr, $dateCurrent, $avgProduktifCurr);
            $rasioPrevM = $calculateRasio($prev, $dateLastMonth, $avgProduktifPrevM);
            $rasioData = [];
            foreach (['bopo', 'ldr', 'casa', 'roa', 'roe', 'cash', 'nim', 'aset_likuid'] as $key) {
                $rasioData[$key] = [
                    'persen_aktual' => round($rasioCurr[$key], 2),
                    'persen_bulan_lalu' => round($rasioPrevM[$key], 2),
                    'persen_tahun_lalu' => 0,
                    'delta_mom' => round($rasioCurr[$key] - $rasioPrevM[$key], 2),
                    'delta_yoy' => 0,
                ];
            }

            $topParams = [':tanggal' => $dateCurrent];
            if ($korwilRange) {
                $topParams[':kw_start'] = str_pad((string) $korwilRange[0], 3, '0', STR_PAD_LEFT);
                $topParams[':kw_end'] = str_pad((string) $korwilRange[1], 3, '0', STR_PAD_LEFT);
            } elseif (!$isConsolidated) {
                $topParams[':kode_kantor'] = str_pad((string) $kodeKantorReq, 3, '0', STR_PAD_LEFT);
            }
            $sqlTop = "
                SELECT
                    kode_perk AS kode,
                    MAX(NULLIF(TRIM(nama_perk), '')) AS nama,
                    SUM(saldo_akhir) AS total_biaya
                FROM acc_history
                WHERE tanggal = :tanggal
                  $sqlKantorAcc
                  AND kode_perk LIKE '5%'
                GROUP BY kode_perk
                HAVING SUM(saldo_akhir) > 0
            ";
            $stmtTop = $this->pdo->prepare($sqlTop);
            $stmtTop->execute($topParams);
            $topRows = $stmtTop->fetchAll(PDO::FETCH_ASSOC);

            $allExpenseCodes = array_column($topRows, 'kode');
            $leafExpenseRows = [];
            foreach ($topRows as $row) {
                $kodeStr = (string) ($row['kode'] ?? '');
                $nama = (string) ($row['nama'] ?? '');
                if (in_array($kodeStr, ['5', '501', '502'], true)
                    || strpos($kodeStr, '50101') === 0
                    || $kodeStr === '50203'
                    || stripos($nama, 'TAMADES') !== false
                    || stripos($nama, 'BEBAN BUNGA') !== false
                    || stripos($nama, 'BEBAN BG') !== false) {
                    continue;
                }

                $isParent = false;
                foreach ($allExpenseCodes as $otherKode) {
                    $otherStr = (string) $otherKode;
                    if ($kodeStr !== $otherStr && strpos($otherStr, $kodeStr) === 0) {
                        $isParent = true;
                        break;
                    }
                }

                if (!$isParent) {
                    $leafExpenseRows[] = $row;
                }
            }

            usort($leafExpenseRows, function($a, $b) {
                return (float) $b['total_biaya'] <=> (float) $a['total_biaya'];
            });

            $topBiaya = [];
            foreach (array_slice($leafExpenseRows, 0, 5) as $row) {
                $nama = trim((string) ($row['nama'] ?? ''));
                $topBiaya[] = [
                    'kode' => $row['kode'],
                    'nama' => $nama !== '' ? $nama : 'Biaya / Beban Lainnya',
                    'nominal' => (float) $row['total_biaya'],
                ];
            }

            $ckpnKredit = abs($curr['ckpn_kredit']);
            $scopeLabel = $korwilRange ? ('KORWIL ' . $korwilReq) : strtoupper($kodeKantorReq);

            sendResponse(200, "Berhasil memuat TV Makro Summary (" . $scopeLabel . ")", [
                'info_kantor' => $scopeLabel,
                'info_tanggal' => [
                    'aktual' => $dateCurrent,
                    'bulan_lalu' => $dateLastMonth,
                    'tahun_lalu' => $dateLastYear,
                ],
                'makro' => $summaryData,
                'ringkasan_detail' => [
                    'dana_masyarakat' => [
                        'total' => $dpkCurr,
                        'tabungan' => $curr['tabungan'],
                        'deposito' => $curr['deposito'],
                    ],
                    'ppbl' => $curr['ppbl'],
                    'kredit_diberikan' => [
                        'baki_debet' => $curr['kredit_baki_debet'],
                        'saldo_bank_ead' => $curr['saldo_bank_ead'],
                    ],
                    'ckpn' => [
                        'ckpn_ppbl' => abs($curr['ckpn_ppbl']),
                    'ckpn_kredit' => $ckpnKredit,
                    'npl' => $totalNpl,
                ],
                    'pendapatan' => $curr['pendapatan'],
                    'biaya' => $curr['biaya'],
                    'laba_sebelum_pajak' => $labaCurr,
                    'laba_setelah_pajak' => $labaSetelahPajak,
                    'rasio_utama' => [
                        'kap' => ($curr['kredit'] > 0) ? ($totalNpl / $curr['kredit']) * 100 : 0,
                        'ckpn_terhadap_ppka' => ($totalNpl > 0) ? ($ckpnKredit / $totalNpl) * 100 : 0,
                        'npl_baki_debet_gross' => ($curr['kredit_baki_debet'] > 0) ? ($totalNpl / $curr['kredit_baki_debet']) * 100 : 0,
                        'npl_baki_debet_netto' => ($curr['kredit_baki_debet'] > 0) ? (($totalNpl - $ckpnKredit) / $curr['kredit_baki_debet']) * 100 : 0,
                        'npl_saldo_bank_gross' => ($curr['saldo_bank_ead'] > 0) ? ($totalNpl / $curr['saldo_bank_ead']) * 100 : 0,
                    ],
                ],
                'kesehatan_rasio' => $rasioData,
                'perkiraan' => $perkiraanMap,
                'rumus_rasio' => [
                    'aset' => "101 + 102 + 103 + 104 + 105 + 10601 + 10602 + 10604 + 10605 + 10606 + 107 + 108 + 109 + 110 + 11102 + 112 + 113 + 116 + 117 + 118 + 119 + 120 + 121" . ($isConsolidated ? " - 210" : ""),
                    'bopo' => '501 / 401 x 100%',
                    'ldr' => '10601 / (204 + 20603 + 30106) x 100%',
                    'casa' => '20401 / (20401 + 20402) x 100%',
                    'roa' => 'Laba disetahunkan / Aset x 100%',
                    'roe' => 'Laba disetahunkan / Ekuitas x 100%',
                    'cash' => '(101 + 10401 + 10402) / (201 + 20401 + 20202 + 205 + 211) x 100%',
                    'aset_likuid' => '(101 + 10401 + 10402) / Total Aset x 100%',
                    'nim' => '((40101 - 50101) / bulan x 12) / rata-rata (104 + 10601 + 10606) x 100%',
                ],
                'rasio' => [
                    'bopo_persen' => round($rasioCurr['bopo'], 2),
                    'ldr_persen' => round($rasioCurr['ldr'], 2),
                    'casa_persen' => round($rasioCurr['casa'], 2),
                    'roa_persen' => round($rasioCurr['roa'], 2),
                    'roe_persen' => round($rasioCurr['roe'], 2),
                    'cash_ratio_persen' => round($rasioCurr['cash'], 2),
                    'aset_likuid_persen' => round($rasioCurr['aset_likuid'], 2),
                    'nim_persen' => round($rasioCurr['nim'], 2),
                    'coverage_ratio_persen' => ($totalNpl > 0) ? round(($ckpnKredit / $totalNpl) * 100, 2) : 0,
                    'detail_nominal' => [
                        'total_kredit' => $curr['kredit_baki_debet'],
                        'total_dana_diterima' => $curr['simpanan_pihak_ketiga'] + $curr['deposito_bank_lain_gt_3_bulan'] + $curr['modal_pinjaman'],
                        'total_dpk' => $dpkCurr,
                        'pend_operasional' => $curr['pend_ops'],
                        'biaya_operasional' => $curr['biaya_ops'],
                        'laba_disetahunkan' => ((int) date('n', strtotime($dateCurrent)) > 0) ? ($labaCurr / (int) date('n', strtotime($dateCurrent))) * 12 : $labaCurr,
                        'total_aset' => $curr['aset'],
                        'total_ekuitas' => $curr['ekuitas'],
                        'aset_likuid' => $curr['kas'] + $curr['ppbl_giro'] + $curr['ppbl_tabungan'],
                        'hutang_lancar' => $curr['kewajiban_segera'] + $curr['tabungan'] + $curr['utang_bunga_deposito'] + $curr['simpanan_bank_lain'] + $curr['liabilitas_lainnya'],
                        'kas' => $curr['kas'],
                        'ppbl_giro' => $curr['ppbl_giro'],
                        'ppbl_tabungan' => $curr['ppbl_tabungan'],
                        'utang_bunga_deposito' => $curr['utang_bunga_deposito'],
                        'simpanan_bank_lain' => $curr['simpanan_bank_lain'],
                        'liabilitas_lainnya' => $curr['liabilitas_lainnya'],
                        'simpanan_pihak_ketiga' => $curr['simpanan_pihak_ketiga'],
                        'deposito_bank_lain_gt_3_bulan' => $curr['deposito_bank_lain_gt_3_bulan'],
                        'modal_pinjaman' => $curr['modal_pinjaman'],
                        'pendapatan_bunga' => $curr['pend_bunga'],
                        'beban_bunga' => $curr['beban_bunga'],
                        'pendapatan_bunga_bersih_disetahunkan' => (($curr['pend_bunga'] - $curr['beban_bunga']) / max(1, (int) date('n', strtotime($dateCurrent)))) * 12,
                        'rata_aset_produktif' => $avgProduktifCurr,
                        'kewajiban_segera' => $curr['kewajiban_segera'],
                        'ckpn_kredit' => $ckpnKredit,
                        'total_npl' => $totalNpl,
                    ],
                ],
                'top_5_biaya' => $topBiaya,
            ]);
        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat TV Makro Summary: " . $e->getMessage(), null);
        }
    }

    public function apiGetCoaList() 
    {
        try {
            $coaDict = $this->getCoaDictionaryFromAccHistory();
            if (empty($coaDict)) {
                $coaDict = $this->getCoaDictionary();
            }
            $data = [];
            
            // Ubah format dari ['101' => 'Kas'] menjadi [['kode' => '101', 'nama' => 'Kas'], ...]
            foreach ($coaDict as $kode => $nama) {
                $data[] = [
                    'kode' => (string)$kode,
                    'nama' => $nama
                ];
            }

            sendResponse(200, "Berhasil memuat daftar COA", $data);

        } catch (Exception $e) {
            sendResponse(500, "Gagal memuat daftar COA: " . $e->getMessage(), null);
        }
    }

    private function getCoaDictionaryFromAccHistory(?string $tanggal = null): array
    {
        if ($tanggal === null || trim((string) $tanggal) === '') {
            $stmtDate = $this->pdo->query("SELECT MAX(tanggal) FROM acc_history");
            $tanggal = (string) $stmtDate->fetchColumn();
        }

        if ($tanggal === '') {
            return [];
        }

        $stmt = $this->pdo->prepare("
            SELECT
                TRIM(CAST(kode_perk AS CHAR)) AS kode,
                MAX(NULLIF(TRIM(nama_perk), '')) AS nama
            FROM acc_history
            WHERE tanggal = :tanggal
              AND kode_perk IS NOT NULL
              AND nama_perk IS NOT NULL
            GROUP BY TRIM(CAST(kode_perk AS CHAR))
            ORDER BY TRIM(CAST(kode_perk AS CHAR)) ASC
        ");
        $stmt->execute([':tanggal' => $tanggal]);

        $dict = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $kode = trim((string) ($row['kode'] ?? ''));
            $nama = trim((string) ($row['nama'] ?? ''));
            if ($kode !== '' && $nama !== '') {
                $dict[$kode] = $nama;
            }
        }

        return $dict;
    }

    private function isAccHistoryKodePerk($kode): bool
    {
        $kode = trim((string) $kode);
        return $kode !== '' && preg_match('/^[1-5][0-9]*$/', $kode) === 1;
    }

    /**
     * =================================================================
     * 1. KAMUS COA (Breakdown Nama Perkiraan)
     * =================================================================
    */
    private function getCoaDictionary(): array 
    {
        return [
            // ==========================================
            // KODE 1: ASET
            // ==========================================
            '1' => 'Aset',
            '101' => 'Kas',
            '10101' => 'Kas Besar',
            '10102' => 'Kas Kecil',
            '10103' => 'Kas Dalam Atm',
            '10104' => 'Kas Dalam Perjalanan (Cash In Transit)',
            '10105' => 'Kas Branchless',
            '10106' => 'Kas Echannel',
            '10199' => 'TDP',
            '102' => 'Kas Dalam Valuta Asing',
            '10201' => 'Kas Vallas - Dollar',
            '10202' => 'Kas Vallas - Euro',
            '10203' => 'Kas Vallas - Yen',
            '10299' => 'Travel Cheque',
            '103' => 'Surat Berharga',
            '10301' => 'Sertifikat Bank Indonesia',
            '104' => 'Penempatan Pada Bank Lain',
            '10401' => 'Giro',
            '10402' => 'Tabungan',
            '10403' => 'Deposito Berjangka',
            '10404' => 'Sertifikat Deposito',
            '105' => 'Cadangan Kerugian Penurunan Nilai (ABA)',
            '10501' => 'CKPN (ABA)',
            '106' => 'Kredit Yang Diberikan',
            '10601' => 'Kredit Yang Diberikan Baki Debet',
            '1060101' => 'Kredit Modal Kerja',
            '106010101' => 'Pertanian',
            '106010102' => 'Multi Manfaat',
            '106010103' => 'Sinden',
            '106010104' => 'Bumdes',
            '106010105' => 'Korporasi',
            '106010106' => 'KMB',
            '1060102' => 'Kredit Investasi',
            '1060103' => 'Kredit Konsumsi',
            '106010301' => 'Joglo',
            '106010302' => 'Pegawai',
            '106010303' => 'Karyawan',
            '106010304' => 'Pensiunan',
            '106010305' => 'Perangkat Desa',
            '106010306' => 'Lainnya',
            '1060104' => 'Kredit Mikro Bkk',
            '1060105' => 'Kredit BKK Joglo',
            '1060106' => 'Kredit BKK Sinden',
            '1060107' => 'Kredit BKK Korporasi',
            '1060108' => 'Kredit BKK Bumdes',
            '1060109' => 'Kredit BKK Musiman',
            '1060110' => 'Kredit Kolektif Karyawan (K3)',
            '1060111' => 'Kredit KPP',
            '1060112' => 'Kredit UMKM BKK (KUB)',
            '1060113' => 'Kredit UMKM BKK (KUB) 6',
            '1060114' => 'Kredit Koperasi',
            '1060115' => 'Kredit Agro',
            '1060116' => 'Kredit Bahari',
            '1060117' => 'Kredit BKK Joglo Mitra',
            '1060118' => 'Kredit BKK Joglo Khusus Pegawai',
            '10602' => 'KYD Provisi / Administrasi',
            '1060201' => 'Pendapatan Ditangguhkan - Provisi',
            '106020101' => 'KYD Provisi Kredit Modal Kerja',
            '106020102' => 'KYD Provisi Kredit Investasi',
            '106020103' => 'KYD Provisi Kredit Konsumsi',
            '106020104' => 'KYD Provisi Kredit Mikro Bkk',
            '106020105' => 'KYD Provisi Kredit BKK Joglo',
            '106020106' => 'KYD Provisi Kredit BKK Sinden',
            '106020107' => 'KYD Provisi Kredit BKK Korporasi',
            '106020108' => 'KYD Provisi Kredit BKK Bumdes',
            '106020109' => 'KYD Provisi Kredit BKK Musiman',
            '106020110' => 'KYD Provisi Kredit Kolektif Karyawan (K3)',
            '106020111' => 'KYD Provisi Kredit KPP',
            '106020112' => 'KYD Provisi Kredit KUB',
            '106020113' => 'KYD Provisi Kredit KUB 6',
            '106020114' => 'KYD Provisi Kredit Koperasi',
            '106020115' => 'KYD Provisi Kredit Agro',
            '106020116' => 'KYD Provisi Kredit Bahari',
            '106020117' => 'KYD Provisi Kredit BKK Joglo Mitra',
            '106020118' => 'KYD Provisi Kredit BKK Joglo Khusus Pegawai',
            '1060202' => 'Pendapatan Ditangguhkan - Administrasi',
            '106020201' => 'KYD Adm Kredit Modal Kerja',
            '106020202' => 'KYD Adm Kredit Investasi',
            '106020203' => 'KYD Adm Kredit Konsumsi',
            '106020204' => 'KYD Adm Kredit Mikro BKK',
            '106020205' => 'KYD Adm Kredit BKK Joglo',
            '106020206' => 'KYD Adm Kredit BKK Sinden',
            '106020207' => 'KYD Adm Kredit BKK Korporasi',
            '106020208' => 'KYD Adm Kredit BKK Bumdes',
            '106020209' => 'KYD Adm Kredit BKK Musiman',
            '106020210' => 'KYD Adm Kredit Kolektif Karyawan (K3)',
            '106020211' => 'KYD Adm Kredit KPP',
            '106020212' => 'KYD Adm Kredit KUB',
            '106020213' => 'KYD Adm Kredit KUB (Promo)',
            '106020214' => 'KYD Adm Kredit Agro',
            '106020215' => 'KYD Adm Kredit Bahari',
            '106020216' => 'KYD Adm Kredit BKK Joglo Mitra',
            '106020217' => 'KYD Adm Kredit BKK Joglo Khusus Pegawai',
            '10603' => 'KYD Biaya Transaksi',
            '1060301' => 'Biaya Di Tangguhkan - Biaya Transaksi',
            '106030101' => 'KYD By Trans Kredit Modal Kerja',
            '106030102' => 'KYD By Trans Kredit Investasi',
            '106030103' => 'KYD By Trans Kredit Konsumsi',
            '106030104' => 'KYD By Trans Kredit Mikro Bkk',
            '106030105' => 'KYD By Trans Kredit BKK Joglo',
            '106030106' => 'KYD By Trans Kredit BKK Sinden',
            '106030107' => 'KYD By Trans Kredit BKK Korporasi',
            '106030108' => 'KYD By Trans Kredit BKK Bumdes',
            '106030109' => 'KYD By Trans Kredit BKK Musiman',
            '106030110' => 'KYD By Trans Kredit Kolektif Karyawan (K3)',
            '106030111' => 'KYD By Trans Kredit KPP',
            '106030112' => 'KYD By Trans Kredit KUB',
            '106030113' => 'KYD By Trans Kredit Koperasi',
            '106030114' => 'KYD By Trans Kredit Agro',
            '106030115' => 'KYD By Trans Kredit Bahari',
            '106030116' => 'KYD By Trans Kredit BKK Joglo Mitra',
            '106030117' => 'KYD By Trans Kredit BKK Joglo Khusus Pegawai',
            '10604' => '-/- Pendapatan Yang Ditangguhkan Dalam Rangka',
            '10605' => '-/- Cadangan Kerugian Restrukturisasi',
            '10606' => 'Selisih Flat dan EIR',
            '107' => 'Cadangan Kerugian Penurunan Nilai (Kredit)',
            '10701' => 'CKPN Individual',
            '10702' => 'CKPN Kolektif',
            '108' => 'Agunan Yang Diambil Alih (AYDA)',
            '109' => 'Aktiva Tetap & Inventaris',
            '10901' => 'Tanah',
            '10902' => 'Gedung',
            '10903' => 'Peralatan Dan Perlengkapan',
            '10904' => 'Kendaraan',
            '10905' => 'Lainnya',
            '110' => '(-/-) Akumulasi Penyusutan Dan Penurunan Nilai',
            '11001' => 'Akumulasi Peny. Gedung',
            '1100101' => 'Akum. Gedung -/-',
            '11002' => 'Akumulasi Peny. Inventaris',
            '1100201' => 'Akum. Kendaraan',
            '1100202' => 'Akum. Peralatan Dan Perlengkapan',
            '1100203' => 'Akum. Lainnya',
            '111' => 'Aset Tidak Berwujud',
            '11101' => 'Program Aplikasi Core Banking (Software)',
            '11102' => 'Lainnya',
            '11103' => '(-/-) Akumulasi Amortisasi Dan Penurunan Nilai',
            '1110301' => 'Akum. Amortisasi Peny. Nilai -/-',
            '111030101' => 'Program Aplikasi Core Banking -/-',
            '111030102' => 'Lainnya -/-',
            '112' => 'Aset Antar Kantor',
            '11200' => 'AKA Kantor Pusat',
            '11201' => 'AKA KCU',
            '11202' => 'AKA KC Rembang',
            '11203' => 'AKA KC Pati',
            '11204' => 'AKA KC Demak',
            '11205' => 'AKA KC Kendal',
            '11206' => 'AKA KC Salatiga',
            '11207' => 'AKA KC Semarang',
            '11208' => 'AKA KC Wonogiri',
            '11209' => 'AKA KC Kota Surakarta',
            '11210' => 'AKA KC Karanganyar',
            '11211' => 'AKA KC Sukoharjo',
            '11212' => 'AKA KC Sragen',
            '11213' => 'AKA KC Boyolali',
            '11214' => 'AKA KC Magelang',
            '11215' => 'AKA KC Wonosobo',
            '11216' => 'AKA KC Purworejo',
            '11217' => 'AKA KC Kebumen',
            '11218' => 'AKA KC Banjarnegara',
            '11219' => 'AKA KC Purbalingga',
            '11220' => 'AKA KC Banyumas',
            '11221' => 'AKA KC Cilacap',
            '11222' => 'AKA KC Tegal',
            '11223' => 'AKA KC Brebes',
            '11224' => 'AKA KC Kota Tegal',
            '11225' => 'AKA KC Pemalang',
            '11226' => 'AKA KC Kota Pekalongan',
            '11227' => 'AKA KC Pekalongan',
            '11228' => 'AKA KC Batang',
            '113' => 'Aset Lain Lain',
            '11301' => 'Pendapatan Bunga Yang Akan Diterima (Pyad)',
            '1130101' => 'PYAD - Penempatan Pada Bank Lain',
            '1130102' => 'PYAD - Kredit Yang Diberikan',
            '11302' => 'Premi Penjamin Lps Dibayar Dimuka',
            '11303' => 'Pajak Di Bayar Dimuka',
            '1130301' => 'Pajak Dibayar Dimuka PPH',
            '1130302' => 'Pajak Dibayar Dimuka PPN',
            '11304' => 'Aset Pajak Tangguhan',
            '11305' => 'Biaya Di Bayar Di Muka',
            '1130501' => 'Biaya Dibayar Dimuka- Sewa',
            '1130502' => 'Biaya Dibayar Dimuka- Sewa Gedung',
            '1130503' => 'Biaya Dibayar Dimuka- Sewa Kendaraan',
            '1130504' => 'Biaya Dibayar Dimuka- Sewa Inventaris',
            '1130505' => 'Biaya Dibayar Dimuka- Sewa Lainnya',
            '1130506' => 'Biaya Dibayar Dimuka - Asuransi Purjab',
            '1130507' => 'Biaya Dibayar Dimuka - Bunga Deposito',
            '1130508' => 'Biaya Dibayar Dimuka - BPJS TK',
            '1130509' => 'Biaya Dibayar Dimuka - BPJS Kesehatan',
            '1130510' => 'Biaya Dibayar Dimuka - Asuransi',
            '1130599' => 'Biaya Dibayar Dimuka - Lainnya',
            '11306' => 'Tagihan Kepada Perusahaan Asuransi',
            '11307' => 'Uang Muka Kegiatan Operasional',
            '1130701' => 'Uang Muka Akomodasi dan Rapat',
            '1130702' => 'Uang Muka Pembelian',
            '1130703' => 'Uang Muka Operasional Kantor Wilayah',
            '1130704' => 'Uang Muka Jasa Pihak Ketiga',
            '1130705' => 'Uang Muka Pendidikan dan Pelatihan',
            '1130706' => 'Uang Muka Penanganan Kredit Bermasalah',
            '1130799' => 'Uang Muka Kegiatan Operasional Lainnya',
            '11399' => 'Aset lain-lain Lainnya',
            '1139901' => 'Persediaan Barang Cetakan',
            '1139902' => 'Persediaan Materai',
            '1139903' => 'Mata Uang Yg Ditarik Peredaran',
            '1139904' => 'Deposit PPOB',
            '1139905' => 'Deposit Mobile Banking',
            '1139906' => 'Kredit Dalam Penyelesaian',
            '1139907' => 'Titipan QRIS',
            '1139999' => 'Lain-Lain',
            

            // ==========================================
            // KODE 2: KEWAJIBAN
            // ==========================================
            '2' => 'Kewajiban',
            '201' => 'Kewajiban-Kewajiban Yang Segera',
            '20101' => 'Deposito Jatuh Tempo Yg Blm Ditarik',
            '20102' => 'Tabungan Berjangka Yg Jth Tmpo Yg Blm Ditarik',
            '20103' => 'Kewajiban Kpd Pemerintah Yg Hrs Dibayar',
            '2010301' => 'Pph Tabungan Final (Pasal 4 Ayat 2)',
            '2010302' => 'Pph Deposito (Pasal 4 Ayat 2)',
            '2010303' => 'PPh Pengurus Dan Pegawai (Ps 21 &/ 26)',
            '2010304' => 'Pph Juru Bayar (Pasal 21 &/ 26)',
            '2010305' => 'Ppn',
            '201030501' => 'Ppn Barang (Pasal 22)',
            '201030502' => 'Ppn Jasa (Pasal 23)',
            '2010306' => 'Hutang Pajak Badan (Pasal 29)',
            '2010307' => 'Pajak Lainnya',
            '20104' => 'Sanksi Kewajiban Membayar Kepada Bi Yg Blm Dib',
            '20105' => 'Titipan Nasabah',
            '2010501' => 'Kiriman Uang',
            '2010502' => 'Titipan Pln',
            '2010503' => 'Titipan Pdam',
            '2010504' => 'Kreditur / Simpanan',
            '2010505' => 'Debitur / Angsuran Kredit',
            '2010506' => 'Notaris',
            '2010507' => 'Premi Asuransi',
            '201050701' => 'Bumi Putra',
            '201050702' => 'Askrida',
            '201050703' => 'Jamkrida',
            '201050704' => 'Jiwasraya',
            '201050705' => 'BPJS TK KREDIT',
            '201050799' => 'Lainnya',
            '20106' => 'KYD Bersaldo Kredit',
            '20107' => 'Deviden Yang Belum Di Bayarkan',
            '2010701' => 'Pemerintah Provinsi',
            '2010702' => 'Pemerintah Kabupaten',
            '20108' => 'Selisih Hasil Penjualan Ayda',
            '20109' => 'Imbalan Kerja',
            '2010901' => 'Dana Kesejahteraan Yang Harus Dibayar',
            '2010902' => 'Jasa Produksi Yang Harus Dibayar',
            '20110' => 'Premi BPJS Kesehatan',
            '20111' => 'Premi BPJS Ketenagakerjaan',
            '20199' => 'Kewajiban Segera Lainnya',
            '2019901' => 'Dana Kesejahteraan',
            '2019902' => 'PPOB / EDC',
            '2019903' => 'Dana Bergulir',
            '2019904' => 'Subsidi Bunga',
            '2019905' => 'Pembayaran Pbb',
            '2019906' => 'Pembayaran Pdam',
            '2019907' => 'Pembayaran Pajak Kendaraan',
            '2019908' => 'ABA Dalam Penyelesaian',
            '2019909' => 'Kewajiban Gaji',
            '2019999' => 'KWS Lainnya Lain-lain',
            '202' => 'Utang Bunga',
            '20201' => 'Tabungan Berjangka',
            '20202' => 'Deposito',
            '2020201' => 'A. Sudah Jatuh Tempo',
            '2020202' => 'B. Belum Jatuh Tempo',
            '20203' => 'Simpanan Dari Bank Lain',
            '2020301' => 'A. Sudah Jatuh Tempo',
            '2020302' => 'B. Belum Jatuh Tempo',
            '20204' => 'Pinjaman Yang Diterima',
            '2020401' => 'A. Pinjaman Yang Diterima Sudah Jatuh Tempo',
            '2020402' => 'B. Pinjaman Yang Diterima Belum Jatuh Tempo',
            '20299' => 'Bunga Lainnya',
            '203' => 'Utang Pajak',
            '20301' => 'Taksiran Pajak Penghasilan Pph Badan',
            '204' => 'Simpanan',
            '20401' => 'Tabungan',
            '2040101' => 'Tabungan Wajib',
            '2040102' => 'Tabungan Tamades',
            '2040103' => 'Tabungan Tamades 1 (Bunga Harian)',
            '2040104' => 'Tabungan Tamades 2 (Tabungan Program)',
            '2040105' => 'Tabungan Tamades 3',
            '2040106' => 'Tabungan Tamades 4',
            '2040107' => 'Tabungan Tamades 5',
            '2040108' => 'Tabungan Pelajar',
            '2040109' => 'TAMADES',
            '2040110' => 'TAWA',
            '2040111' => 'TAWA PLUS',
            '2040112' => 'Tabungan Kredit BKK',
            '2040113' => 'Tabungan Mitra BKK',
            '2040114' => 'Tabungan BKK Prioritas',
            '20402' => 'Deposito',
            '2040201' => 'Deposito 1 Bulan',
            '2040202' => 'Deposito 3 Bulan',
            '2040203' => 'Deposito 6 Bulan',
            '2040204' => 'Deposito 9 Bulan',
            '2040205' => 'Deposito 12 Bulan',
            '205' => 'Simpanan Dari Bank Lain',
            '20501' => 'Bank Indonesia',
            '20502' => 'Bank Lain',
            '2050201' => 'Deposito',
            '2050202' => 'Tabungan',
            '206' => 'Pinjaman Diterima',
            '20601' => 'Bank Indonesia',
            '20602' => 'Bank Lain',
            '2060201' => 'Bank Umum',
            '2060202' => 'Bpr',
            '2060203' => 'Terkait Apex',
            '2060204' => 'Dalam Rangka Linkage',
            '20603' => 'Dari Bukan Bank',
            '2060301' => 'Kewajiban Sewa Pembiayaan',
            '2060399' => 'Lainnya',
            '20699' => 'Lainnya',
            '207' => 'Dana Setoran Modal Kewajiban',
            '20701' => 'Pemerintah Provinsi Jawa Tengah',
            '20702' => 'Pemerintah Kabupaten',
            '2070201' => 'Pemkab Semarang',
            '2070202' => 'Pemkot Salatiga',
            '2070203' => 'Pemkab Pati',
            '2070204' => 'Pemkab Rembang',
            '2070205' => 'Pemkab Kendal',
            '2070206' => 'Pemkab Demak',
            '2070207' => 'Pemkab Banjarnegara',
            '2070208' => 'Pemkab Wonosobo',
            '2070209' => 'Pemkab Purworejo',
            '2070210' => 'Pemkab Magelang',
            '2070211' => 'Pemkab Cilacap',
            '2070212' => 'Pemkab Purbalingga',
            '2070213' => 'Pemkab Banyumas',
            '2070214' => 'Pemkab Temanggung',
            '2070215' => 'Pemkab Boyolali',
            '2070216' => 'Pemkab Karanganyar',
            '2070217' => 'Pemkab Wonogiri',
            '2070218' => 'Pemkab Klaten',
            '2070219' => 'Pemkab Sukoharjo',
            '2070220' => 'Pemkot Surakarta',
            '2070221' => 'Pemkab Sragen',
            '2070222' => 'Pemkot Pekalongan',
            '2070223' => 'Pemkab Tegal',
            '2070224' => 'Pemkab Batang',
            '2070225' => 'Pemkab Pemalang',
            '2070226' => 'Pemkab Pekalongan',
            '2070227' => 'Pemkot Tegal',
            '2070228' => 'Pemkab Brebes',
            '2070229' => 'Pemkab Kebumen',
            '208' => 'Kewajiban Imbalan Kerja',
            '20801' => 'Jangka Pendek',
            '2080101' => 'Thr',
            '2080102' => 'Tunj. Bantuan Pendidikan',
            '2080103' => 'Kinerja',
            '208010301' => 'Kinerja 1',
            '208010302' => 'Kinerja 2',
            '20802' => 'Jangka Panjang',
            '2080201' => 'Jasa Pengabdian Pengurus',
            '2080202' => 'Jasa Pengabdian Pegawai',
            '2080203' => 'Imbalan Pesangon Phk',
            '20899' => 'Kewajiban Imbalan Kerja Lainnya',
            '209' => 'Pinjaman Subordinasi',
            '20901' => 'Modal Pinjaman',
            '210' => 'Kewajiban Antar Kantor',
            '21000' => 'AKP Kantor Pusat',
            '21001' => 'AKP KCU',
            '21002' => 'AKP KC Rembang',
            '21003' => 'AKP KC Pati',
            '21004' => 'AKP KC Demak',
            '21005' => 'AKP KC Kendal',
            '21006' => 'AKP KC Salatiga',
            '21007' => 'AKP KC Semarang',
            '21008' => 'AKP KC Wonogiri',
            '21009' => 'AKP KC Kota Surakarta',
            '21010' => 'AKP KC Karanganyar',
            '21011' => 'AKP KC Sukoharjo',
            '21012' => 'AKP KC Sragen',
            '21013' => 'AKP KC Boyolali',
            '21014' => 'AKP KC Magelang',
            '21015' => 'AKP KC Wonosobo',
            '21016' => 'AKP KC Purworejo',
            '21017' => 'AKP KC Kebumen',
            '21018' => 'AKP KC Banjarnegara',
            '21019' => 'AKP KC Purbalingga',
            '21020' => 'AKP KC Banyumas',
            '21021' => 'AKP KC Cilacap',
            '21022' => 'AKP KC Tegal',
            '21023' => 'AKP KC Brebes',
            '21024' => 'AKP KC Kota Tegal',
            '21025' => 'AKP KC Pemalang',
            '21026' => 'AKP KC Kota Pekalongan',
            '21027' => 'AKP KC Pekalongan',
            '21028' => 'AKP KC Batang',
            '211' => 'Kewajiban Lain Lain',
            '21101' => 'Taksiran Pajak Penghasilan',
            '21102' => 'Pendapatan Yang Ditangguhkan',
            '21103' => 'Lainnya',
            '2110301' => 'Pakaian Dinas',
            '2110302' => 'Rekreasi',
            '2110303' => 'Undian',
            '2110304' => 'Olah Raga',
            '2110305' => 'Dana Kesejahteraan',
            '2110306' => 'Jasa Produksi',
            '2110307' => 'Akomodasi KAP',
            '2110308' => 'Titipan Angs. BKK Pingsurat',
            '2110310' => 'CSR',
            '2110311' => 'Tantiem',
            '2110399' => 'Kewajiban Lain-lain Lainnya',
            
            // ==========================================
            // KODE 3: EKUITAS
            // ==========================================
            '3' => 'Ekuitas',
            '301' => 'Modal Disetor',
            '30101' => 'Modal Dasar',
            '3010101' => 'Pemerintah Provinsi Jawa Tengah 51 %',
            '3010102' => 'Pemerintah Kabupaten 49 %',
            '301010201' => 'Pemkab Semarang',
            '301010202' => 'Pemkot Salatiga',
            '301010203' => 'Pemkab Pati',
            '301010204' => 'Pemkab Rembang',
            '301010205' => 'Pemkab Kendal',
            '301010206' => 'Pemkab Demak',
            '301010207' => 'Pemkab Banjarnegara',
            '301010208' => 'Pemkab Wonosobo',
            '301010209' => 'Pemkab Purworejo',
            '301010210' => 'Pemkab Magelang',
            '301010211' => 'Pemkab Cilacap',
            '301010212' => 'Pemkab Purbalingga',
            '301010213' => 'Pemkab Banyumas',
            '301010214' => 'Pemkab Temanggung',
            '301010215' => 'Pemkab Boyolali',
            '301010216' => 'Pemkab Karanganyar',
            '301010217' => 'Pemkab Wonogiri',
            '301010218' => 'Pemkab Klaten',
            '301010219' => 'Pemkab Sukoharjo',
            '301010220' => 'Pemkot Surakarta',
            '301010221' => 'Pemkab Sragen',
            '301010222' => 'Pemkot Pekalongan',
            '301010223' => 'Pemkab Tegal',
            '301010224' => 'Pemkab Batang',
            '301010225' => 'Pemkab Pemalang',
            '301010226' => 'Pemkab Pekalongan',
            '301010227' => 'Pemkot Tegal',
            '301010228' => 'Pemkab Brebes',
            '301010229' => 'Pemkab Kebumen',
            '30102' => 'Modal Yang Belum Disetor -/-',
            '3010201' => 'Pemerintah Provinsi Jawa Tengah',
            '3010202' => 'Pemerintah Kabupaten / Kota',
            '301020201' => 'Pemkab Semarang',
            '301020202' => 'Pemkot Salatiga',
            '301020203' => 'Pemkab Pati',
            '301020204' => 'Pemkab Rembang',
            '301020205' => 'Pemkab Kendal',
            '301020206' => 'Pemkab Demak',
            '301020207' => 'Pemkab Banjarnegara',
            '301020208' => 'Pemkab Wonosobo',
            '301020209' => 'Pemkab Purworejo',
            '301020210' => 'Pemkab Magelang',
            '301020211' => 'Pemkab Cilacap',
            '301020212' => 'Pemkab Purbalingga',
            '301020213' => 'Pemkab Banyumas',
            '301020214' => 'Pemkab Temanggung',
            '301020215' => 'Pemkab Boyolali',
            '301020216' => 'Pemkab Karanganyar',
            '301020217' => 'Pemkab Wonogiri',
            '301020218' => 'Pemkab Klaten',
            '301020219' => 'Pemkab Sukoharjo',
            '301020220' => 'Pemkot Surakarta',
            '301020221' => 'Pemkab Sragen',
            '301020222' => 'Pemkot Pekalongan',
            '301020223' => 'Pemkab Tegal',
            '301020224' => 'Pemkab Batang',
            '301020225' => 'Pemkab Pemalang',
            '301020226' => 'Pemkab Pekalongan',
            '301020227' => 'Pemkot Tegal',
            '301020228' => 'Pemkab Brebes',
            '301020229' => 'Pemkab Kebumen',
            '30103' => 'Agio',
            '3010301' => 'Agio Saham',
            '30104' => 'Disagio -/-',
            '3010401' => 'Disagio Saham -/-',
            '30105' => 'Modal Sumbangan',
            '30106' => 'Modal Pinjaman',
            '30107' => 'Dana Setoran Modal - Ekuitas',
            '3010701' => 'Pemerintah Provinsi Jawa Tengah',
            '3010702' => 'Pemerintah Kabupaten',
            '301070201' => 'Pemkab Semarang',
            '301070202' => 'Pemkot Salatiga',
            '301070203' => 'Pemkab Pati',
            '301070204' => 'Pemkab Rembang',
            '301070205' => 'Pemkab Kendal',
            '301070206' => 'Pemkab Demak',
            '301070207' => 'Pemkab Banjarnegara',
            '301070208' => 'Pemkab Wonosobo',
            '301070209' => 'Pemkab Purworejo',
            '301070210' => 'Pemkab Magelang',
            '301070211' => 'Pemkab Cilacap',
            '301070212' => 'Pemkab Purbalingga',
            '301070213' => 'Pemkab Banyumas',
            '301070214' => 'Pemkab Temanggung',
            '301070215' => 'Pemkab Boyolali',
            '301070216' => 'Pemkab Karanganyar',
            '301070217' => 'Pemkab Wonogiri',
            '301070218' => 'Pemkab Klaten',
            '301070219' => 'Pemkab Sukoharjo',
            '301070220' => 'Pemkot Surakarta',
            '301070221' => 'Pemkab Sragen',
            '301070222' => 'Pemkot Pekalongan',
            '301070223' => 'Pemkab Tegal',
            '301070224' => 'Pemkab Batang',
            '301070225' => 'Pemkab Pemalang',
            '301070226' => 'Pemkab Pekalongan',
            '301070227' => 'Pemkot Tegal',
            '301070228' => 'Pemkab Brebes',
            '301070229' => 'Pemkab Kebumen',
            '302' => 'Laba / Rugi Yang Blm Direalisasi',
            '30201' => 'Surplus Revaluasi Aset Tetap',
            '303' => 'Saldo Laba',
            '30301' => 'Cadangan Umum',
            '30302' => 'Cadangan Tujuan',
            '30303' => 'Laba Rugi',
            '3030301' => 'Laba / Rugi Tahun Lalu',
            '303030101' => 'Laba / Rugi Tahun Lalu',
            '3030302' => 'Laba / Rugi Tahun Berjalan',
            
            // ==========================================
            // KODE 4: PENDAPATAN
            // ==========================================
            '4' => 'Pendapatan',
            '401' => 'Pendapatan Operasional',
            '40101' => '1. Pendapatan Bunga',
            '4010101' => 'A. Bunga Kontraktual',
            '401010101' => 'Surat Berharga',
            '40101010101' => 'Sertifikat Bank Indonesia',
            '401010102' => 'Bunga Penempatan Dari Bank Lain',
            '40101010201' => 'I. Giro',
            '40101010202' => 'Ii. Tabungan',
            '40101010203' => 'Iii. Deposito',
            '40101010204' => 'Iv. Sertifikat Deposito',
            '401010103' => 'Kredit Yang Diberikan',
            '40101010301' => 'Kepada Bank Lain',
            '40101010302' => 'Kepada Pihak Ketiga Bukan Bank',
            '4010101030201' => 'Pend. Bg Kredit Modal Kerja',
            '4010101030202' => 'Pend. Bg Kredit Investasi',
            '4010101030203' => 'Pend. Bg Kredit Konsumtif',
            '4010101030204' => 'Pend. Bg Kredit Mikro Bkk',
            '4010101030205' => 'Pend. Bg Kredit BKK Joglo',
            '4010101030206' => 'Pend. Bg Kredit BKK Sinden',
            '4010101030207' => 'Pend. Bg Kredit BKK Korporasi',
            '4010101030208' => 'Pend. Bg Kredit BKK Bumdes',
            '4010101030209' => 'Pend. Bg Kredit BKK Musiman',
            '4010101030210' => 'Pend. Bg Kredit Kolektif Karyawan (K3)',
            '4010101030211' => 'Pend. Bg Kredit KPP',
            '4010101030212' => 'Pend. Bg Krd KUB',
            '4010101030213' => 'Pend. Bg Krd KUB 6',
            '4010101030214' => 'Pend. Bg Krd Koperasi',
            '4010101030215' => 'Pend. Bg Krd Agro',
            '4010101030216' => 'Pend. Bg Krd Bahari',
            '4010101030217' => 'Pend. Bg Krd BKK Joglo Bahari',
            '4010101030218' => 'Pend. Bg Krd BKK Joglo Khusus Pegawai',
            '4010102' => 'B. Provisi Dan Administrasi',
            '401010201' => '1. Provisi Kredit',
            '40101020101' => 'A. Kepada Bank Lain',
            '40101020102' => 'B. Kepada Pihak Ketiga Bukan Bank',
            '4010102010201' => 'Pend. Provisi Kredit Modal Kerja',
            '4010102010202' => 'Pend. Provisi Kredit Investasi',
            '4010102010203' => 'Pend. Provisi Kredit Konsumtif',
            '4010102010204' => 'Pend. Provisi Kredit Mikro Bkk',
            '4010102010205' => 'Pend. Provisi Kredit BKK Joglo',
            '4010102010206' => 'Pend. Provisi Kredit BKK Sinden',
            '4010102010207' => 'Pend. Provisi Kredit BKK Korporasi',
            '4010102010208' => 'Pend. Provisi Kredit BKK Bumdes',
            '4010102010209' => 'Pend. Provisi Kredit BKK Musiman',
            '4010102010210' => 'Pend. Provisi Kredit Kolektif Karyawan (K3)',
            '4010102010211' => 'Pend. Provisi Kredit KPP',
            '4010102010212' => 'Pend. Provisi Kredit KUB',
            '4010102010213' => 'Pend. Provisi Kredit KUB 6',
            '4010102010214' => 'Pend. Provisi Kredit Koperasi',
            '4010102010215' => 'Pend. Provisi Kredit Agro',
            '4010102010216' => 'Pend. Provisi Kredit Bahari',
            '4010102010217' => 'Pend. Provisi Kredit BKK Joglo Mitra',
            '4010102010218' => 'Pend. Provisi Kredit BKK Joglo Khusus Pegawai',
            '401010202' => '2. Administrasi Kredit',
            '40101020201' => 'A. Kepada Bank Lain',
            '40101020202' => 'B. Kepada Pihak Ketiga Bukan Bank',
            '4010102020201' => 'Pend. Adm Kredit Modal Kerja',
            '4010102020202' => 'Pend. Adm Kredit Investasi',
            '4010102020203' => 'Pend. Adm Kredit Konsumtif',
            '4010102020204' => 'Pend. Adm Kredit Mikro Bkk',
            '4010102020205' => 'Pend. Adm Kredit BKK Joglo',
            '4010102020206' => 'Pend. Adm Kredit BKK Sinden',
            '4010102020207' => 'Pend. Adm Kredit BKK Korporasi',
            '4010102020208' => 'Pend. Adm Kredit BKK Bumdes',
            '4010102020209' => 'Pend. Adm Kredit BKK Musiman',
            '4010102020210' => 'Pend. Adm Kredit Kolektif Karyawan (K3)',
            '4010102020211' => 'Pend. Adm Kredit KKP',
            '4010102020212' => 'Pend. Adm Kredit KUB',
            '4010102020213' => 'Pend. Adm Kredit KUB (Promo)',
            '4010102020214' => 'Pend. Adm Kredit Koperasi',
            '4010102020215' => 'Pend. Adm Kredit Agro',
            '4010102020216' => 'Pend. Adm Kredit Bahari',
            '4010102020217' => 'Pend. Adm Kredit Joglo Mitra',
            '4010102020218' => 'Pend. Adm Kredit BKK Joglo Khusus Pegawai',
            '4010103' => 'C. Biaya Transaksi',
            '401010301' => 'Surat Berharga',
            '401010302' => 'Kredit Yang Diberikan',
            '40101030201' => 'Kepada Bank Lain',
            '40101030202' => 'Kepada Pihak Ketiga Bukan Bank',
            '4010104' => 'D. Pendapatan Bunga EIR',
            '40102' => '2. Lainnya',
            '4010201' => 'A. Pendapatan Jasa Transaksi',
            '401020101' => '1. Pend. Fee PPOB (EDC) PLN,Jastel, Dll',
            '401020102' => '2. Pend. Fee Biller Mobile Banking',
            '401020103' => '3. Pend. Fee PBB',
            '401020104' => '4. Pend. Fee PDAM',
            '401020105' => '6. Pend. Fee Pajak Kendaraan',
            '401020106' => '7. Pend. Fee Lainnya',
            '4010202' => 'B. Keuntungan Penjualan Valas',
            '4010203' => 'C. Keuntungan Penjualan Surat Berharga',
            '4010204' => 'D. Pendapatan Dari Kredit Yang Dihapus Buku',
            '401020401' => '1. Pend. Angsuran PH - Pokok',
            '401020402' => '2. Pend. Angsuran PH - Bunga',
            '401020403' => '3. Pend. Denda Angsuran PH',
            '4010205' => 'E. Pendapatan Dari Pemulihan CKPN',
            '401020501' => '1. Pend. Pemulihan CKPN ABA',
            '401020502' => '2. Pend. Pemulihan CKPN Kredit',
            '4010206' => 'F. Lainnya',
            '401020601' => '1. Pendapatan Administrasi',
            '40102060101' => 'A. Pend. Adm. Pengelolaan Rekening',
            '40102060102' => 'B. Pend. Adm. Penutupan Rekening',
            '40102060103' => 'C. Pend. Adm. Ganti Buku',
            '40102060104' => 'D. Pend. Adm. Tabungan Pasif',
            '40102060105' => 'E. Pend. Pinalty Dari Deposito',
            '40102060106' => 'F. Pend. Pinalty Kredit Pelunasan Belum Jatuh Tem',
            '40102060107' => 'G. Pend. Denda Dari Kredit',
            '40102060108' => 'H. Pend. Denda Dari Kredit Yg Melebihi Jangka Wak',
            '40102060109' => 'I. Pend. Amortisasi Restrukturisasi',
            '401020602' => '2. Pendapatan Koreksi Penyusutan Inventaris',
            '401020603' => '3. Pendapatan Fee',
            '40102060301' => 'A. Pend. Fee Asuransi',
            '40102060302' => 'B. Pend. Fee Notaris',
            '40102060303' => 'C. Pend. Fee Lainnya',
            '401020604' => '4. Pendapatan Pembulatan Kas',
            '401020605' => '5. Pendapatan Lainnya',
            '402' => 'Pendapatan Non Operasional',
            '40201' => '1. Keuntungan Penjualan',
            '4020101' => 'A. Aset Tetap & Inventaris',
            '402010101' => '1. Tanah',
            '402010102' => '2. Bangunan',
            '402010103' => '3. Inventaris',
            '4020102' => 'B. AYDA',
            '402010201' => '1. Tanah',
            '402010202' => '2. Bangunan',
            '402010203' => '3. Kendaraan',
            '40202' => '2. Pemulihan Penurunan Nilai',
            '4020201' => 'A. Aset Tetap & Inventaris',
            '402020101' => '1. Tanah',
            '402020102' => '2. Bangunan',
            '402020103' => '3. Inventaris',
            '4020202' => 'B. AYDA',
            '402020201' => '1. Tanah',
            '402020202' => '2. Bangunan',
            '402020203' => '3. Kendaraan',
            '40203' => '3. Pendapatan Ganti Rugi Asuransi',
            '40204' => '4. Pend. Bunga Antar Kantor',
            '40299' => '5. Lainnya',
            '4029999' => 'Lainnya',
            

            // KODE 5: BIAYA/BEBAN
            // ==========================================
            '5' => 'Biaya',
            '501' => 'Beban Operasional',
            '50101' => '1. Beban Bunga',
            '5010101' => 'A. Beban Bunga Kontraktual',
            '501010101' => 'I. Tabungan',
            '50101010101' => 'Beban Bg Tabungan Wajib',
            '50101010102' => 'Beban Bg Tabungan Tamades',
            '50101010103' => 'Beban Bg Tabungan Tamades 1',
            '50101010104' => 'Beban Bg Tabungan Tamades 2',
            '50101010105' => 'Beban Bg Tabungan Tamades 3',
            '50101010106' => 'Beban Bg Tabungan Tamades 4',
            '50101010107' => 'Beban Bg Tabungan Tamades 5',
            '50101010108' => 'Beban Bg Tabungan Pelajar',
            '50101010109' => 'Beban Bg TAMADES',
            '50101010110' => 'Beban Bg TAWA',
            '50101010111' => 'Beban Bg TAWA PLUS',
            '50101010112' => 'Beban Bg Tabungan Kredit BKK',
            '50101010113' => 'Beban Bg Tabungan Mitra BKK',
            '50101010114' => 'Beban Bg Tabungan BKK Prioritas',
            '501010102' => 'II. Deposito Berjangka',
            '50101010201' => 'Beban Bg Deposito 1 Bulan',
            '50101010202' => 'Beban Bg Deposito 3 Bulan',
            '50101010203' => 'Beban Bg Deposito 6 Bulan',
            '50101010204' => 'Beban Bg Deposito 9 Bulan',
            '50101010205' => 'Beban Bg Deposito 12 Bulan',
            '501010103' => 'III. Simpanan Dari Bank Lain',
            '50101010301' => 'Beban Bg Tabungan ABP',
            '50101010302' => 'Beban Bg Deposito ABP',
            '501010104' => 'IV. Pinjaman Yang Diterima',
            '50101010401' => 'Dari Bank Indonesia',
            '50101010402' => 'Dari Bank Lain',
            '501010403' => 'Dari Pihak Ketiga Bukan Bank',
            '501010105' => 'V. Pinjaman Subordinasi',
            '50101010501' => 'A. Dari Bank Lain',
            '50101010502' => 'B. Dari Pihak Ketiga Bukan Bank',
            '501010106' => 'VI. Premi Penjaminan Simpanan (LPS)',
            '5010102' => 'B. Biaya Transaksi',
            '501010201' => 'Kepada Bank Lain',
            '501010202' => 'Kepada Pihak Ketiga Bukan Bank',
            '50101020201' => 'A. Cash Back',
            '50101020202' => 'B. Asuransi',
            '50101020203' => 'C. Lainnya',
            '5010103' => 'C. Koreksi Atas Pendapatan Bunga',
            '501010301' => '1. Tabungan',
            '501010302' => '2. Deposito',
            '501010303' => '3. Kredit Yang Diberikan',
            '501010399' => '4. Lainnya',
            '50102' => '2. Beban Kerugian Restrukturisasi Kredit',
            '50103' => '3. Beban CKPN',
            '5010301' => 'A. Surat Berharga',
            '5010302' => 'B. Penempatan Pada Bank Lain',
            '5010303' => 'C. Kredit Yang Diberikan',
            '501030301' => 'i. Kepada Bank Lain',
            '501030302' => 'ii. Kepada Pihak Ketiga Bukan Bank',
            '50104' => '4. Beban Pemasaran',
            '5010401' => 'A. Beban Inklusi dan Literasi Keuangan',
            '5010402' => 'B. Beban Pemberian Hadiah',
            '5010403' => 'C. Beban Iklan/Promosi',
            '5010404' => 'D. Beban Edukasi & Sosialisasi Produk',
            '5010499' => 'E. Sponsorship',
            '50105' => '5. Beban Penelitian Dan Pengembangan',
            '5010501' => 'A. Tekhnologi Informasi',
            '5010502' => 'B. Pengembangan Produk Baru',
            '5010503' => 'C. Pembukaan Kantor Kas / Cabang',
            '5010599' => 'D. Lainnya',
            '50106' => '6. Beban Administrasi Dan Umum',
            '5010601' => 'A. Beban Tenaga Kerja',
            '501060101' => 'I. Gaji Dan Upah',
            '50106010101' => 'A. Gaji Direksi',
            '50106010102' => 'B. Gaji Pokok',
            '50106010103' => 'C. Tunjangan Suami / Istri',
            '50106010104' => 'D. Tunjangan Anak',
            '50106010105' => 'E. Tunjangan Pangan',
            '50106010106' => 'F. Tunjangan Jabatan',
            '50106010107' => 'G. Tunjangan Operasional',
            '50106010108' => 'H. Tunjangan Kinerja',
            '50106010110' => 'J. Tunjangan Fungsional',
            '50106010111' => 'K. Tunjangan Masa Kerja',
            '50106010112' => 'L. Honor Tenaga Kontrak',
            '50106010113' => 'M. Honor Tenaga Outsourcing',
            '501060102' => 'II. Honorarium',
            '50106010201' => 'A. Honor Dewan Komisaris',
            '50106010202' => 'B. Honor Kontrak',
            '501060103' => 'III. Lainnya',
            '50106010301' => 'A. Uang Makan',
            '50106010302' => 'B. Uang Lembur',
            '50106010303' => 'C. Uang Transport',
            '50106010304' => 'D. Jasa Pengabdian Pengurus',
            '50106010305' => 'E. Jasa Pengabdian Pegawai',
            '50106010306' => 'F. Premi Jht',
            '50106010307' => 'G. Dplk',
            '50106010308' => 'H. Tunj. Bantuan Pendidikan',
            '50106010309' => 'I. THR',
            '50106010310' => 'J. Tunjangan Kinerja',
            '50106010311' => 'K. Tunjangan PPh 21',
            '50106010312' => 'L. Uang Pesangon',
            '50106010313' => 'M. Uang Penghargaan Masa Kerja',
            '50106010314' => 'N. Tenaga Harian Lepas',
            '5010602' => 'B. Beban Pendidikan Dan Pelatihan',
            '501060201' => '1. In House Training',
            '501060202' => '2. Eksternal Training',
            '501060203' => '3. Study Banding',
            '501060299' => '4. Lainnya',
            '5010603' => 'C. Beban Sewa',
            '501060301' => '1. Sewa Tanah Dan Gedung',
            '50106030101' => 'A. Kantor Pusat',
            '50106030102' => 'B. Kantor Cabang',
            '50106030103' => 'C. Kantor Kas',
            '501060302' => '2. Lainnya',
            '50106030201' => 'Sewa Aplikasi Core Banking',
            '50106030202' => 'Sewa Koneksi Jaringan',
            '50106030203' => 'Sewa Kendaraan',
            '50106030204' => 'Sewa Peralatan Kantor',
            '50106030205' => 'Sewa Pengganti Rumah Dinas',
            '50106030206' => 'Sewa Layanan Teknologi Informasi',
            '50106030299' => 'Sewa Lainnya',
            '5010604' => 'D. Beban Penyusutan / Penghapusan Atas Ati',
            '501060401' => '1. Penyusutan Gedung',
            '501060402' => '2. Penyusutan Inventaris',
            '50106040201' => 'A. Kendaraan',
            '50106040202' => 'B. Inventaris',
            '5010605' => 'E. Beban Amortisasi Aset Tidak Berwujud',
            '501060501' => '1. Core Banking',
            '501060502' => '2. Instalasi Listrik',
            '501060599' => '3. Lainnya',
            '5010606' => 'F. Beban Premi Asuransi',
            '501060601' => 'Asuransi Aset Tetap Dan Inventaris',
            '50106060101' => 'A. Asuransi Gedung',
            '50106060102' => 'B. Asuransi Kendaraan',
            '5010606010201' => 'Asuransi Kend. Roda 4',
            '5010606010202' => 'Asuransi Kend. Roda 2',
            '50106060103' => 'C. Asuransi Inventaris Lainya',
            '501060602' => 'Asuransi Tenaga Kerja',
            '50106060201' => 'A. Bpjs Ketenagakerjaan',
            '50106060202' => 'B. Bpjs Kesehatan',
            '501060603' => 'Asuransi Uang Kas',
            '50106060301' => 'A. Cash In Save',
            '50106060302' => 'B. Cash In Transit',
            '501060699' => 'Lainnya',
            '50106069903' => 'Asuransi Mesin Fotocopy',
            '50106069904' => 'Asuransi Purna Jabatan Pengurus',
            '50106069999' => 'Lainnya',
            '5010607' => 'G. Beban Pemeliharaan Dan Perbaikan',
            '501060701' => '1. Pemeliharaan Ti',
            '501060702' => '2. Pemeliharaan Gedung Kantor',
            '501060703' => '3. Pemeliharaan Perabot Kantor',
            '501060704' => '4. Pemeliharaan Kendaraan',
            '50106070401' => 'By Pemeliharaan Kend. Roda 4',
            '50106070402' => 'By Pemeliharaan Kend. Roda 2',
            '501060799' => '5. Lainnya',
            '5010608' => 'H. Beban Barang Dan Jasa',
            '501060801' => '1. Listrik',
            '501060802' => '2. Air',
            '501060803' => '3. Telepon',
            '501060804' => '4. Materai',
            '501060805' => '5. Alat Tulis Kantor',
            '501060806' => '6. Percetakan',
            '501060807' => '7. Koran & Majalah',
            '501060808' => '8. Gas',
            '501060809' => '9. Akomodasi Tamu',
            '501060810' => '10. Perjalanan Dinas',
            '50106081001' => 'Perj Dinas Komisaris',
            '50106081002' => 'Perj Dinas Direksi',
            '50106081003' => 'Perj Dinas Pegawai',
            '50106081004' => 'Biaya Akomodasi dan Penginapan',
            '501060811' => '11. Jasa Pihak Lain',
            '50106081101' => 'A. Kantor Akuntan Publik (KAP)',
            '50106081102' => 'B. Lawyer',
            '50106081103' => 'C. Konsultan',
            '50106081104' => 'D. Notaris',
            '50106081105' => 'E. Keamanan',
            '50106081106' => 'F. Pungutan OJK',
            '50106081107' => 'G. Security (Outsourcing)',
            '50106081199' => 'H. Lainnya',
            '501060812' => '12. Pakaian Dinas',
            '501060813' => '13. Bahan Bakar Minyak',
            '501060814' => '14. Rapat Rapat',
            '501060815' => '15. Rumah Tangga Kantor',
            '501060816' => '16. Voucher Handphone',
            '501060817' => '17. Catering / Makan',
            '501060818' => '18. Perlengkapan IT',
            '501060819' => '19. Perabot Kantor',
            '501060820' => '20. Ekspedisi/Kurir',
            '501060899' => '21. Lainnya',
            '5010609' => 'I. Beban Pajak',
            '501060901' => '1. Beban Pajak Kendaraan',
            '501060902' => '2. Beban Pajak Bumi Dan Bangunan',
            '501060903' => '3. Beban Ppn Barang (Pasal 22)',
            '501060904' => '4. Beban Ppn Jasa (Pasal 23)',
            '501060999' => '5. Beban Pajak Lainnya',
            '50107' => '7. Beban Lainnya',
            '5010701' => 'Kerugian Penjualan Valas',
            '5010702' => 'Kerugian Penjualan Surat Berharga',
            '5010703' => 'Kerugian Piutang Asuransi',
            '5010799' => 'Lainnya',
            '501079901' => '1. Representatif',
            '501079902' => '2. Biaya Penagihan Kredit',
            '501079903' => '3. Konsolidasi',
            '501079904' => '4. Bingkisan/ Cinderamata',
            '501079905' => '5. Fee Juru Bayar',
            '501079906' => '6. By Adm PPBL',
            '501079907' => '7. Pajak Atas Bunga PPBL',
            '501079908' => '8. By. Pengadilan dan Gugatan Sederhana',
            '501079909' => '9. Iuran OJK',
            '502' => 'Beban Non Operasional',
            '50201' => 'Kerugian Penjualan / Kehilangan',
            '5020101' => 'Aset Tetap Dan Inventaris',
            '502010101' => '1. Kendaraan',
            '502010102' => '2. Inventaris',
            '5020102' => 'Ayda',
            '50202' => 'Kerugian Penurunan Nilai',
            '5020201' => 'Aset Tetap Dan Inventaris',
            '502020101' => '1. Kendaraan',
            '502020102' => '2. Inventaris',
            '5020202' => 'Ayda',
            '50203' => 'Beban Bunga Antar Kantor',
            '50204' => 'Selisih Kurs',
            '50299' => 'Lainnya',
            '5029901' => 'A. Rekreasi',
            '5029902' => 'B. Olah Raga',
            '5029903' => 'C. Iuran Asosiasi',
            '5029904' => 'E. Sumbangan',
            '5029905' => 'F. Denda',
            '5029907' => 'H. Bingkisan-Bingkisan',
            '5029908' => 'I. Lainnya'
            
            // 🔥 PASTE FULL DICTIONARY DARI KODE 1 - 5 DI SINI YAA BROOKUUU!
        ];
    }
}

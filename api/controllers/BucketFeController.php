<?php

class BucketFeController {

    private $pdo;
    private $visualBuckets = ['0', '1-7', '8-14', '15-21', '22-30', 'FE', 'BE'];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- HELPER 1: Range Tanggal ---
    private function getDayRange($date) {
        return [$date . ' 00:00:00', $date . ' 23:59:59'];
    }

    // --- HELPER 2: Bucket Logic ---
    private function getBucketLabel($dpd) {
        $d = (int)$dpd;
        if ($d <= 0)  return '0';
        if ($d <= 7)  return '1-7';
        if ($d <= 14) return '8-14';
        if ($d <= 21) return '15-21';
        if ($d <= 30) return '22-30';
        if ($d <= 180) return 'FE';
        return 'BE';
    }

    // --- HELPER 3: SQL Filter Detail ---
    private function getBucketConditionSql($colName, $bucketLabel) {
        $lbl = trim((string)$bucketLabel); 
        
        if ($lbl === '0')     return "$colName <= 0";
        if ($lbl === '1-7')   return "$colName BETWEEN 1 AND 7";
        if ($lbl === '8-14')  return "$colName BETWEEN 8 AND 14";
        if ($lbl === '15-21') return "$colName BETWEEN 15 AND 21";
        if ($lbl === '22-30') return "$colName BETWEEN 22 AND 30";
        if ($lbl === 'FE')    return "$colName BETWEEN 31 AND 180";
        if ($lbl === 'BE')    return "$colName > 180";
        
        return "1=0"; 
    }

    private function getVisualLabel($dpd) { return $this->getBucketLabel($dpd); }

    private function normalizeNominalField($field) {
        return strtolower(trim((string)$field)) === 'baki_debet' ? 'baki_debet' : 'saldo_bank';
    }

    private function resolveScope($raw) {
        $value = strtoupper(trim((string)$raw));
        $ranges = [
            'SEMARANG' => ['001', '007'],
            'SOLO' => ['008', '014'],
            'BANYUMAS' => ['015', '021'],
            'PEKALONGAN' => ['022', '028'],
        ];

        if ($value === '' || $value === '000' || $value === 'ALL' || $value === 'KONSOLIDASI') {
            return ['type' => 'all', 'value' => null];
        }

        if (strpos($value, 'KORWIL:') === 0) {
            $value = trim(substr($value, 7));
        } elseif (strpos($value, 'KORWIL|') === 0) {
            $value = trim(substr($value, 7));
        } elseif (strpos($value, 'CABANG:') === 0) {
            $value = trim(substr($value, 7));
        } elseif (strpos($value, 'CABANG|') === 0) {
            $value = trim(substr($value, 7));
        }

        if (isset($ranges[$value])) {
            return ['type' => 'korwil', 'value' => $value, 'range' => $ranges[$value]];
        }

        return ['type' => 'cabang', 'value' => str_pad($value, 3, '0', STR_PAD_LEFT)];
    }

    private function scopeSql($alias, $scope, $prefix, &$params) {
        $qualified = $alias !== '' ? $alias . '.' : '';
        if (($scope['type'] ?? 'all') === 'korwil') {
            $params[":{$prefix}_start"] = $scope['range'][0];
            $params[":{$prefix}_end"] = $scope['range'][1];
            return " AND {$qualified}kode_cabang BETWEEN :{$prefix}_start AND :{$prefix}_end";
        }
        if (($scope['type'] ?? 'all') === 'cabang') {
            $params[":{$prefix}_kc"] = $scope['value'];
            return " AND {$qualified}kode_cabang = :{$prefix}_kc";
        }
        return '';
    }

    /**
     * ENDPOINT 1: REKAP MATRIKS (Fast & Balanced)
     */
    public function migrasiBucketOsc($input = null) {
        set_time_limit(300); ini_set('memory_limit', '2048M');

        $b = is_array($input) ? $input : [];
        $closing = $b['closing_date'] ?? null;
        $harian  = $b['harian_date'] ?? null;
        $scope   = $this->resolveScope($b['kode_kantor'] ?? null);
        $nominalField = $this->normalizeNominalField($b['nominal_field'] ?? $b['hitung_berdasarkan'] ?? 'saldo_bank');

        if (!$closing || !$harian) return $this->send(400, "Tanggal wajib diisi.");

        [$s1, $e1] = $this->getDayRange($closing);
        [$s2, $e2] = $this->getDayRange($harian);

        // Kolom created bertipe DATE. Perbandingan equality menjaga index
        // snapshot tetap dipakai dan jauh lebih cepat daripada range datetime.
        // Agregasi langsung di database. Snapshot M-1 dan actual dibaca
        // sekaligus lalu dikelompokkan per rekening; PHP hanya menerima
        // maksimal 7 x 8 kelompok bucket, bukan seluruh detail rekening.
        // Placeholder tanggal dibuat terpisah karena native PDO tidak aman
        // memakai named placeholder yang sama berulang kali.
        $params = [
            ':m1_date'       => $closing,
            ':cur_date'      => $harian,
            ':m1_case_os'    => $closing,
            ':m1_case_dpd'   => $closing,
            ':cur_case_os'   => $harian,
            ':cur_case_dpd'  => $harian,
            ':m1_count_date' => $closing
        ];
        $snapshotScope = $this->scopeSql('n', $scope, 'snap', $params);
        $sql = "SELECT
                    bucketed.from_bucket,
                    bucketed.to_bucket,
                    COUNT(*) AS group_noa,
                    COALESCE(SUM(bucketed.os_m1), 0) AS group_os_m1,
                    COALESCE(SUM(CASE WHEN bucketed.os_cur IS NOT NULL AND bucketed.os_cur > 0 THEN 1 ELSE 0 END), 0) AS active_noa,
                    COALESCE(SUM(CASE WHEN bucketed.os_cur IS NOT NULL AND bucketed.os_cur > 0 THEN bucketed.os_cur ELSE 0 END), 0) AS active_os,
                    COALESCE(SUM(CASE WHEN bucketed.os_cur IS NULL OR bucketed.os_cur <= 0 THEN 1 ELSE 0 END), 0) AS lunas_noa,
                    COALESCE(SUM(CASE WHEN bucketed.os_cur IS NULL OR bucketed.os_cur <= 0 THEN bucketed.os_m1 ELSE 0 END), 0) AS lunas_os
                FROM (
                    SELECT
                        snap.os_m1,
                        snap.os_cur,
                        CASE
                            WHEN snap.dpd_m1 <= 0 THEN '0'
                            WHEN snap.dpd_m1 <= 7 THEN '1-7'
                            WHEN snap.dpd_m1 <= 14 THEN '8-14'
                            WHEN snap.dpd_m1 <= 21 THEN '15-21'
                            WHEN snap.dpd_m1 <= 30 THEN '22-30'
                            WHEN snap.dpd_m1 <= 180 THEN 'FE'
                            ELSE 'BE'
                        END AS from_bucket,
                        CASE
                            WHEN snap.os_cur IS NULL OR snap.os_cur <= 0 THEN 'O'
                            WHEN snap.dpd_cur <= 0 THEN '0'
                            WHEN snap.dpd_cur <= 7 THEN '1-7'
                            WHEN snap.dpd_cur <= 14 THEN '8-14'
                            WHEN snap.dpd_cur <= 21 THEN '15-21'
                            WHEN snap.dpd_cur <= 30 THEN '22-30'
                            WHEN snap.dpd_cur <= 180 THEN 'FE'
                            ELSE 'BE'
                        END AS to_bucket
                    FROM (
                        SELECT
                            n.no_rekening,
                            COALESCE(MAX(CASE WHEN n.created = :m1_case_os THEN n.{$nominalField} END), 0) AS os_m1,
                            MAX(CASE WHEN n.created = :m1_case_dpd THEN COALESCE(n.hari_menunggak, 0) END) AS dpd_m1,
                            MAX(CASE WHEN n.created = :cur_case_os THEN n.{$nominalField} END) AS os_cur,
                            MAX(CASE WHEN n.created = :cur_case_dpd THEN COALESCE(n.hari_menunggak, 0) END) AS dpd_cur,
                            MAX(CASE WHEN n.created = :m1_count_date THEN 1 ELSE 0 END) AS has_m1
                        FROM nominatif n
                        WHERE n.created IN (:m1_date, :cur_date)
                          AND n.no_rekening IS NOT NULL {$snapshotScope}
                        GROUP BY n.no_rekening
                    ) snap
                    WHERE snap.has_m1 = 1
                ) bucketed
                GROUP BY bucketed.from_bucket, bucketed.to_bucket";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $movementGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $summary = []; $matrix = []; $rowActiveTotals = [];
        $grandTotal = [
            'm1' => ['noa'=>0, 'os'=>0],
            'buckets' => [],
            'angsuran' => 0,
            'lunas' => ['noa'=>0, 'os'=>0],
            // Komponen run off dikirim terpisah agar summary dapat
            // membedakan angsuran, pelunasan, dan total run off.
            'runoff_angsuran' => ['noa'=>0, 'os'=>0],
            'runoff_pelunasan' => ['noa'=>0, 'os'=>0],
            'runoff_total' => ['noa'=>0, 'os'=>0]
        ];

        foreach ($this->visualBuckets as $lbl) {
            $summary[$lbl] = ['noa_m1'=>0, 'os_m1'=>0];
            $grandTotal['buckets'][$lbl] = ['noa'=>0, 'os'=>0];
            $rowActiveTotals[$lbl] = 0;
            foreach (array_merge($this->visualBuckets, ['O']) as $t) {
                $matrix[$lbl][$t] = ['noa'=>0, 'os'=>0, 'angsuran'=>0, 'pelunasan'=>0];
            }
        }

        foreach ($movementGroups as $group) {
            $from = (string)$group['from_bucket'];
            $to = (string)$group['to_bucket'];
            $groupOsM1 = (float)$group['group_os_m1'];

            $summary[$from]['noa_m1'] += (int)$group['group_noa'];
            $summary[$from]['os_m1'] += $groupOsM1;
            $grandTotal['m1']['noa'] += (int)$group['group_noa'];
            $grandTotal['m1']['os'] += $groupOsM1;

            if ($to === 'O') {
                // Rekening yang hilang / bersaldo nol di actual adalah LUNAS,
                // termasuk rekening lama yang ditutup karena restruk kapitalisasi.
                $lunasNoa = (int)$group['lunas_noa'];
                $lunasOs = (float)$group['lunas_os'];
                $matrix[$from]['O']['noa'] += $lunasNoa;
                $matrix[$from]['O']['pelunasan'] += $lunasOs;
                $grandTotal['lunas']['noa'] += $lunasNoa;
                $grandTotal['lunas']['os'] += $lunasOs;
                continue;
            }

            $activeNoa = (int)$group['active_noa'];
            $activeOs = (float)$group['active_os'];
            $matrix[$from][$to]['noa'] += $activeNoa;
            $matrix[$from][$to]['os'] += $activeOs;
            $rowActiveTotals[$from] += $activeOs;
            $grandTotal['buckets'][$to]['noa'] += $activeNoa;
            $grandTotal['buckets'][$to]['os'] += $activeOs;
        }

        foreach ($this->visualBuckets as $f) {
            $osStart  = $summary[$f]['os_m1'];
            $osLunas  = $matrix[$f]['O']['pelunasan'];
            $osActive = $rowActiveTotals[$f];
            $netAngsuran = $osStart - ($osActive + $osLunas);

            foreach ($this->visualBuckets as $t) {
                if ($f == $t) $matrix[$f][$t]['angsuran'] = $netAngsuran;
            }
            $grandTotal['angsuran'] += $netAngsuran;
        }

        // Realisasi dihitung langsung di database dengan anti-join terindeks.
        $realParams = [':r_date'=>$harian, ':r_m1_date'=>$closing];
        $realScope = $this->scopeSql('r', $scope, 'r', $realParams);
        $m1RealScope = $this->scopeSql('m', $scope, 'm', $realParams);
        $sqlReal = "SELECT COUNT(*) AS noa, COALESCE(SUM(COALESCE(r.{$nominalField},0)),0) AS os
                    FROM nominatif r
                    WHERE r.created = :r_date
                      AND COALESCE(r.{$nominalField},0) > 0 {$realScope}
                      AND NOT EXISTS (
                          SELECT 1 FROM nominatif m
                          WHERE m.no_rekening = r.no_rekening
                            AND m.created = :r_m1_date
                            AND m.no_rekening IS NOT NULL {$m1RealScope}
                      )";
        $stmtReal = $this->pdo->prepare($sqlReal);
        $stmtReal->execute($realParams);
        $realRow = $stmtReal->fetch(PDO::FETCH_ASSOC) ?: ['noa'=>0, 'os'=>0];
        $realisasi = ['noa' => (int)$realRow['noa'], 'os' => (float)$realRow['os']];

        $grandTotal['runoff_angsuran']['os'] = $grandTotal['angsuran'];
        $grandTotal['runoff_pelunasan'] = $grandTotal['lunas'];
        $grandTotal['runoff_total']['os'] = $grandTotal['runoff_angsuran']['os'] + $grandTotal['runoff_pelunasan']['os'];
        $grandTotal['runoff_total']['noa'] = $grandTotal['lunas']['noa'];

        unset($movementGroups);

        $this->send(200, "Sukses", [
            'meta'      => ['scope'=>$scope, 'nominal_field'=>$nominalField, 'm1'=>$closing, 'cur'=>$harian],
            'summary_m1'=> $summary,
            'matrix'    => $matrix,
            'realisasi' => $realisasi,
            'grand_total' => $grandTotal
        ]);
    }

    /**
     * ENDPOINT 2: DETAIL DATA 
     * Tambah Alamat, HP, Tabungan, Kankas (Nama), dan AO (Nama)
     */
    public function getMigrasiDetail($input = null) {
        $b = is_array($input) ? $input : [];
        $closing = $b['closing_date'] ?? null;
        $harian  = $b['harian_date'] ?? null;
        $scope   = $this->resolveScope($b['kode_kantor'] ?? null);
        $nominalField = $this->normalizeNominalField($b['nominal_field'] ?? $b['hitung_berdasarkan'] ?? 'saldo_bank');
        $kankas  = $b['kode_kankas'] ?? null; // Filter Kankas
        $ao      = $b['kode_ao'] ?? null;     // Filter AO

        $fromLbl = isset($b['from_bucket']) ? trim((string)$b['from_bucket']) : '';
        $toLbl   = isset($b['to_bucket']) ? trim((string)$b['to_bucket']) : '';
        
        $page    = isset($b['page']) ? (int)$b['page'] : 1;
        $limit   = isset($b['limit']) ? (int)$b['limit'] : 10;
        $offset  = ($page - 1) * $limit;

        if (!$closing || !$harian) return $this->send(400, "Tanggal wajib.");

        [$s1, $e1] = $this->getDayRange($closing);
        [$s2, $e2] = $this->getDayRange($harian);

        // Pembayaran detail mengikuti periode transaksi (closing_date, harian_date].
        // Agregasi dilakukan lebih dulu agar join tidak menggandakan baris nominatif.
        $txClosing = $closing . ' 23:59:59';
        $txHarian  = $harian . ' 23:59:59';
        $trxJoinTemplate = "LEFT JOIN (
                                SELECT no_rekening,
                                       SUM(COALESCE(angsuran_pokok, 0) + COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) AS total_bayar,
                                       SUM(COALESCE(angsuran_pokok, 0)) AS bayar_pokok,
                                       SUM(COALESCE(angsuran_bunga, 0) - COALESCE(diskon_bunga, 0)) AS bayar_bunga,
                                       MAX(tgl_trans) AS tgl_bayar_terakhir
                                FROM transaksi_kredit
                                WHERE tgl_trans > :tx_closing
                                  AND tgl_trans <= :tx_harian
                                GROUP BY no_rekening
                            ) trx ON trx.no_rekening = %s";
        $paymentCols = "COALESCE(trx.total_bayar, 0) AS total_bayar,
                        COALESCE(trx.bayar_pokok, 0) AS bayar_pokok,
                        COALESCE(trx.bayar_bunga, 0) AS bayar_bunga,
                        trx.tgl_bayar_terakhir";

        // Base Columns - Menggunakan COALESCE agar jika data di tabel kankas/ao kosong, kodenya tetap muncul
        $cols = "t2.no_rekening, t2.nama_nasabah, t2.alamat, t2.hp as no_hp, 
                 COALESCE(k.deskripsi_group1, t2.kode_group1) as kankas, 
                 COALESCE(ak.nama_ao, t2.kode_group2) as nama_ao,
                 COALESCE(tb.saldo_akhir, 0) as tabungan,
                  COALESCE(t2.{$nominalField}, 0) AS baki_debet, t2.hari_menunggak, t2.kode_produk, 
                  t2.kolektibilitas, t2.tunggakan_pokok, t2.tunggakan_bunga,
                  {$paymentCols}";
        
        $sqlCount = ""; $sqlData = "";
        $scopeParams = [];

        // 1. DETAIL REALISASI
        if ($fromLbl === 'REALISASI') {
            $baseWhere = "t2.created = :d2
                          AND NOT EXISTS (SELECT 1 FROM nominatif t1 WHERE t1.no_rekening = t2.no_rekening AND t1.created = :d1)";
            
            $baseWhere .= $this->scopeSql('t2', $scope, 'scope', $scopeParams);
            if ($kankas) $baseWhere .= " AND t2.kode_group1 = :kankas";
            if ($ao) $baseWhere .= " AND t2.kode_group2 = :ao"; 
            if ($toLbl !== '') $baseWhere .= " AND " . $this->getBucketConditionSql("t2.hari_menunggak", $toLbl);

            $sqlCount = "SELECT COUNT(1) FROM nominatif t2 WHERE $baseWhere";
             $sqlData  = "SELECT $cols, 0 as os_m1, 0 as dpd_m1, 'New' as status_migrasi 
                          FROM nominatif t2 
                          " . sprintf($trxJoinTemplate, 't2.no_rekening') . "
                          LEFT JOIN tabungan tb ON t2.norek_tabungan = tb.no_rekening
                         LEFT JOIN ao_kredit ak ON t2.kode_group2 = ak.kode_group2 AND (t2.kode_cabang = ak.kode_kantor OR ak.kode_kantor IS NULL)
                         LEFT JOIN kankas k ON t2.kode_group1 = k.kode_group1
                         WHERE $baseWhere";

        // 2. DETAIL LUNAS
        } elseif ($toLbl === 'O') {
            $baseWhere = "t1.created = :d1
                          AND NOT EXISTS (
                              SELECT 1 FROM nominatif t2 
                              WHERE t2.no_rekening = t1.no_rekening 
                              AND t2.created = :d2
                              AND COALESCE(t2.{$nominalField}, 0) > 0
                          )";
            
            $baseWhere .= $this->scopeSql('t1', $scope, 'scope', $scopeParams);
            if ($kankas) $baseWhere .= " AND t1.kode_group1 = :kankas";
            if ($ao) $baseWhere .= " AND t1.kode_group2 = :ao"; 
            if ($fromLbl !== '') $baseWhere .= " AND " . $this->getBucketConditionSql("t1.hari_menunggak", $fromLbl);

            $sqlCount = "SELECT COUNT(1) FROM nominatif t1 WHERE $baseWhere";
            
            $colsLunas = "t1.no_rekening, t1.nama_nasabah, t1.alamat, t1.hp as no_hp, 
                          COALESCE(k.deskripsi_group1, t1.kode_group1) as kankas, 
                          COALESCE(ak.nama_ao, t1.kode_group2) as nama_ao,
                          COALESCE(tb.saldo_akhir, 0) as tabungan,
                           0 as baki_debet, 0 as hari_menunggak, t1.kode_produk,
                           t1.kolektibilitas, t1.tunggakan_pokok, t1.tunggakan_bunga,
                           {$paymentCols}";

             $sqlData  = "SELECT $colsLunas, COALESCE(t1.{$nominalField},0) as os_m1, t1.hari_menunggak as dpd_m1, 'Lunas' as status_migrasi 
                          FROM nominatif t1 
                          " . sprintf($trxJoinTemplate, 't1.no_rekening') . "
                          LEFT JOIN tabungan tb ON t1.norek_tabungan = tb.no_rekening
                         LEFT JOIN ao_kredit ak ON t1.kode_group2 = ak.kode_group2 AND (t1.kode_cabang = ak.kode_kantor OR ak.kode_kantor IS NULL)
                         LEFT JOIN kankas k ON t1.kode_group1 = k.kode_group1
                         WHERE $baseWhere"; 

        // 3. DETAIL ACTIVE
        } else {
            $baseWhere = "t1.created = :d1
                          AND t2.created = :d2
                          AND t1.no_rekening = t2.no_rekening
                          AND COALESCE(t2.{$nominalField}, 0) > 0"; 
            
            $baseWhere .= $this->scopeSql('t1', $scope, 'scope', $scopeParams);
            if ($kankas) $baseWhere .= " AND t1.kode_group1 = :kankas";
            if ($ao) $baseWhere .= " AND t1.kode_group2 = :ao"; 
            if ($fromLbl !== '') $baseWhere .= " AND " . $this->getBucketConditionSql("t1.hari_menunggak", $fromLbl);
            if ($toLbl !== '')   $baseWhere .= " AND " . $this->getBucketConditionSql("t2.hari_menunggak", $toLbl);

            $sqlCount = "SELECT COUNT(1) FROM nominatif t1 JOIN nominatif t2 ON t1.no_rekening=t2.no_rekening WHERE $baseWhere";
            
             $sqlData  = "SELECT $cols, COALESCE(t1.{$nominalField},0) as os_m1, t1.hari_menunggak as dpd_m1, 'Active' as status_migrasi 
                          FROM nominatif t1 
                          JOIN nominatif t2 ON t1.no_rekening=t2.no_rekening 
                          " . sprintf($trxJoinTemplate, 't2.no_rekening') . "
                          LEFT JOIN tabungan tb ON t2.norek_tabungan = tb.no_rekening
                         LEFT JOIN ao_kredit ak ON t2.kode_group2 = ak.kode_group2 AND (t2.kode_cabang = ak.kode_kantor OR ak.kode_kantor IS NULL)
                         LEFT JOIN kankas k ON t2.kode_group1 = k.kode_group1
                         WHERE $baseWhere";
        }

        // EXEC COUNT
        $stmtCnt = $this->pdo->prepare($sqlCount);
        $stmtCnt->bindValue(':d1', $closing); $stmtCnt->bindValue(':d2', $harian);
        foreach ($scopeParams as $key => $value) $stmtCnt->bindValue($key, $value);
        if ($kankas) $stmtCnt->bindValue(':kankas', $kankas);
        if ($ao) $stmtCnt->bindValue(':ao', $ao); 
        $stmtCnt->execute();
        $total = $stmtCnt->fetchColumn();

        // EXEC DATA
        $sqlData .= " ORDER BY " . ($toLbl === 'O' ? "t1.{$nominalField}" : "t2.{$nominalField}") . " DESC LIMIT :lim OFFSET :off";
        
        $stmt = $this->pdo->prepare($sqlData);
        $stmt->bindValue(':d1', $closing); $stmt->bindValue(':d2', $harian);
        if (strpos($stmt->queryString, ':tx_closing') !== false) $stmt->bindValue(':tx_closing', $txClosing);
        if (strpos($stmt->queryString, ':tx_harian') !== false) $stmt->bindValue(':tx_harian', $txHarian);
        foreach ($scopeParams as $key => $value) $stmt->bindValue($key, $value);
        if ($kankas) $stmt->bindValue(':kankas', $kankas);
        if ($ao) $stmt->bindValue(':ao', $ao); 
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Visual Format
        foreach ($rows as &$r) {
            if ($r['status_migrasi'] === 'Active') {
                $f = $this->getVisualLabel($r['dpd_m1']);
                $t = $this->getVisualLabel($r['hari_menunggak']);
                $r['status_migrasi'] = "$f -> $t";
            }
            $r['baki_debet'] = (float)$r['baki_debet'];
            $r['os_m1'] = (float)$r['os_m1'];
            $r['tunggakan_pokok'] = (float)($r['tunggakan_pokok'] ?? 0);
            $r['tunggakan_bunga'] = (float)($r['tunggakan_bunga'] ?? 0);
            $r['totung'] = $r['tunggakan_pokok'] + $r['tunggakan_bunga'];
            $r['tabungan'] = (float)$r['tabungan'];
            $r['total_bayar'] = (float)($r['total_bayar'] ?? 0);
            $r['bayar_pokok'] = (float)($r['bayar_pokok'] ?? 0);
            $r['bayar_bunga'] = (float)($r['bayar_bunga'] ?? 0);
            $r['tgl_bayar_terakhir'] = $r['tgl_bayar_terakhir'] ?? null;

            // Logika Status Tabungan
            if (($r['tabungan'] * 0.015) > $r['totung']) {
                $r['status_tabungan'] = 'Aman';
            } else {
                $r['status_tabungan'] = 'Belum Aman';
            }
        }

        $this->send(200, "Detail Data", [
            'nominal_field' => $nominalField,
            'pagination' => ['current_page' => $page, 'total_records' => (int)$total, 'total_pages' => ceil($total / $limit)],
            'data' => $rows
        ]);
    }


    private function send($status, $msg, $data = []) {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $msg, 'data' => $data]);
        exit;
    }
}
?>

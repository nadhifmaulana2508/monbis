<?php

class PerhitunganCkpnActual {
    private $pdo;
    private $controller;

    public function __construct($pdo, $controller) {
        $this->pdo = $pdo;
        $this->controller = $controller;
    }

    /**
     * Ambil CKPN dari tabel nominatif (Bisa untuk M-1 maupun Actual)
     */
    public function ambil_ckpn_nominatif(string $tanggal, ?string $kc, array $accs): array {
        if (!$accs) return [];
        
        [$ds, $de] = $this->controller->dayRange($tanggal);
        $out = [];
        
        foreach (array_chunk($accs, 500) as $chunk) {
            $ph = implode(',', array_fill(0, count($chunk), '?'));
            
            $sql = "SELECT no_rekening, nilai_ckpn
                    FROM nominatif
                    WHERE created >= ? AND created < ? AND no_rekening IN ($ph)";
                    
            $params = array_merge([$ds, $de], $chunk);
            
            if ($kc !== null && $kc !== '000') { 
                $sql .= " AND LPAD(CAST(kode_cabang AS CHAR),3,'0') = ?"; 
                $params[] = $kc; 
            } else {
                $sql .= " AND LPAD(CAST(kode_cabang AS CHAR),3,'0') <> '000'";
            }
            
            $st = $this->pdo->prepare($sql); 
            $st->execute($params);
            
            while($r = $st->fetch(PDO::FETCH_ASSOC)) {
                $out[$r['no_rekening']] = (int)round((float)$r['nilai_ckpn']);
            }
        }
        return $out;
    }

    /**
     * Hitung CKPN ACTUAL dan kembalikan beserta nilai PD & LGD-nya
     */
    public function hitung_ckpn_actual(array $row, array $pdMap, float $LGD, array $indivMap, array $restrukSet, int $dbCkpnActualVal): array {
        $acc = $row['no_rekening'] ?? null;
        
        // Siapkan variabel dasar
        $ead  = (float)($row['saldo_bank'] ?? 0);
        $dpd  = (int)($row['hari_menunggak'] ?? 0);
        $prod = isset($row['kode_produk']) && $row['kode_produk'] !== '' ? (int)$row['kode_produk'] : null;

        $bucket = $row['to_bucket'] ?? $row['from_bucket'] ?? null;
        if (!$bucket) { 
            [$defs] = $this->controller->loadBuckets(); 
            $bucket = $this->controller->dpdToCode($dpd, $defs) ?? 'A'; 
        }

        // Cari PD sesuai mapping
        $pd = 0.0;
        if ($prod !== null && isset($pdMap[$prod][$bucket])) {
             $pd = (float)$pdMap[$prod][$bucket];
        }

        // Default response array
        $result = [
            'ckpn'       => 0,
            'pd_actual'  => $pd,
            'lgd_actual' => $LGD
        ];

        // 1. Cek apakah masuk daftar CKPN Individual
        if ($acc && isset($indivMap[$acc])) {
            $result['ckpn'] = $dbCkpnActualVal;
            return $result; 
        }

        // 2. Jika TIDAK (Kolektif) -> Lanjut filter OJK
        $isRestruk = $acc ? isset($restrukSet[$acc]) : false;

        // Aturan OJK: DPD <= 7 dan bukan restrukturisasi = Bebas CKPN (0)
        if ($dpd <= 7 && !$isRestruk) {
            return $result; // ckpn biarkan 0, tapi pd & lgd tetap dikembalikan untuk info
        }

        // Kalkulasi normal
        $result['ckpn'] = (int)round($ead * ($pd / 100.0) * ($LGD / 100.0));
        
        return $result;
    }
}
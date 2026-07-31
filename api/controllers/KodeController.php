<?php

require_once __DIR__ . '/../helpers/response.php';

class KodeController {
    private $pdo;
    private $cacheNamespace = 'report_dpk_kode_cache';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function cacheDir()
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->cacheNamespace;
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir;
    }

    private function cachePath($key)
    {
        return $this->cacheDir() . DIRECTORY_SEPARATOR . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key) . '.json';
    }

    private function remember($key, $ttlSeconds, $resolver)
    {
        $forceRefresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';
        $path = $this->cachePath($key);

        if (!$forceRefresh && is_file($path) && (time() - filemtime($path) <= $ttlSeconds)) {
            $cached = json_decode((string) @file_get_contents($path), true);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $data = $resolver();
        @file_put_contents($path, json_encode($data));
        return $data;
    }

    private function setBrowserCache($seconds)
    {
        header('Cache-Control: private, max-age=' . (int) $seconds);
    }

    // --- KODE KANTOR (EXISTING) ---
    public function getKodeKantor($input = [])
    {
        $data = $this->remember('kode_kantor', 3600, function () {
            $sql = "
                SELECT 
                    kode_kantor, 
                    nama_kantor
                FROM kode_kantor
                WHERE kode_kantor <> '000'
                ORDER BY kode_kantor
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });

        $this->setBrowserCache(3600);
        sendResponse(200, "Berhasil ambil daftar kode kantor", $data);
    }

    // --- KODE AO (BARU: DITAMBAHKAN) ---
    public function getKodeAOKredit($input = [])
    {
        $kode_kantor = $input['kode_kantor'] ?? null;

        try {
            $cacheKey = 'kode_ao_kredit_' . (empty($kode_kantor) ? 'all' : $kode_kantor);
            $data = $this->remember($cacheKey, 1800, function () use ($kode_kantor) {
                $sql = "SELECT kode_group2, nama_ao, kode_kantor FROM ao_kredit WHERE 1=1";

                if (!empty($kode_kantor) && $kode_kantor !== '000' && $kode_kantor !== '099') {
                    $sql .= " AND kode_kantor = :kc";
                }

                $sql .= " ORDER BY nama_ao ASC";

                $stmt = $this->pdo->prepare($sql);
                if (!empty($kode_kantor) && $kode_kantor !== '000' && $kode_kantor !== '099') {
                    $stmt->bindValue(':kc', $kode_kantor);
                }

                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            });

            $this->setBrowserCache(1800);
            sendResponse(200, "Berhasil ambil daftar AO", $data);

        } catch (PDOException $e) {
            sendResponse(500, "Database Error: " . $e->getMessage());
        }
    }

    // --- KODE KANKAS (BARU: UNTUK DROPDOWN MODAL) ---
    public function getKodeKankas($input = [])
    {
        $kode_kantor = $input['kode_kantor'] ?? null;

        try {
            $cacheKey = 'kode_kankas_' . (empty($kode_kantor) ? 'all' : $kode_kantor);
            $data = $this->remember($cacheKey, 1800, function () use ($kode_kantor) {
                $sql = "SELECT kode_group1, deskripsi_group1 FROM kankas WHERE 1=1";

                if (!empty($kode_kantor) && $kode_kantor !== '000') {
                    $sql .= " AND kode_kantor = :kc";
                }

                $sql .= " ORDER BY kode_group1 ASC";

                $stmt = $this->pdo->prepare($sql);
                if (!empty($kode_kantor) && $kode_kantor !== '000') {
                    $stmt->bindValue(':kc', $kode_kantor);
                }
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            });

            $this->setBrowserCache(1800);
            sendResponse(200, "Berhasil ambil daftar kankas", $data);
        } catch (PDOException $e) {
            sendResponse(500, "Database Error: " . $e->getMessage());
        }
    }

    public function getListWilayahDropdown($input) {
        $type = $input['type'] ?? '';
        $kode_kantor = $input['kode_kantor'] ?? '';
        
        // Tangkap parameter kecamatan dari FE
        $kecamatan = $input['kecamatan'] ?? ''; 

        // Pakai data H-1
        $tanggal_hari_ini = date('Y-m-d', strtotime('-1 day'));

        $where = " WHERE created = :tanggal ";
        $params = [':tanggal' => $tanggal_hari_ini];

        if ($kode_kantor !== '' && $kode_kantor !== '000') {
            $where .= " AND kode_cabang = :kode_kantor ";
            $params[':kode_kantor'] = $kode_kantor;
        }

        try {
            $sql = "";
            if ($type === 'kecamatan') {
                $sql = "SELECT DISTINCT deskripsi_kode_kecamatan 
                        FROM nominatif 
                        $where 
                          AND deskripsi_kode_kecamatan IS NOT NULL 
                          AND deskripsi_kode_kecamatan != ''
                        ORDER BY deskripsi_kode_kecamatan ASC";
                        
            } else if ($type === 'kelurahan') {
                
                // 🔥 FILTER KELURAHAN BERDASARKAN KECAMATAN 🔥
                // Jika dari FE mengirimkan kecamatan, tambahkan ke WHERE
                if ($kecamatan !== '') {
                    $where .= " AND deskripsi_kode_kecamatan = :kecamatan ";
                    $params[':kecamatan'] = $kecamatan;
                }

                $sql = "SELECT DISTINCT deskripsi_kode_kelurahan 
                        FROM nominatif 
                        $where 
                          AND deskripsi_kode_kelurahan IS NOT NULL 
                          AND deskripsi_kode_kelurahan != ''
                        ORDER BY deskripsi_kode_kelurahan ASC";
            } else {
                sendResponse(400, "Type tidak dikenali");
                return;
            }

            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) { 
                $stmt->bindValue($key, $val); 
            }
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendResponse(200, "Sukses", $data);

        } catch (Exception $e) {
            sendResponse(500, "Error BE: " . $e->getMessage());
        }
    }

}

<?php

require_once __DIR__ . '/../helpers/response.php';

class DateController {
    private $pdo;
    private $cacheTtl = 60;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function cacheDir() {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'report_dpk_date_cache';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        return $dir;
    }

    private function cachePath($key) {
        return $this->cacheDir() . DIRECTORY_SEPARATOR . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key) . '.json';
    }

    private function remember($key, $resolver) {
        $forceRefresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';
        $path = $this->cachePath($key);

        if (!$forceRefresh && is_file($path) && (time() - filemtime($path) <= $this->cacheTtl)) {
            $cached = json_decode((string) @file_get_contents($path), true);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $data = call_user_func($resolver);
        @file_put_contents($path, json_encode($data));
        return $data;
    }

    private function getLastCreatedFrom($table) {
        $allowedTables = array('nominatif', 'nominatif_hapus_buku', 'account_handle');
        if (!in_array($table, $allowedTables, true)) {
            return null;
        }

        $sql = "SELECT created AS last_created FROM {$table} ORDER BY created DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['last_created'] : null;
    }

    
    public function getDefaultDate() {
        $data = $this->remember('default_nominatif', function() {
            $lastCreated = $this->getLastCreatedFrom('nominatif');
            $closingDate = null;
            $awalBulan   = null;

            if ($lastCreated) {
                $closingDateObj = new DateTime($lastCreated);
                $closingDateObj->modify('last day of previous month');
                $closingDate = $closingDateObj->format('Y-m-d');

                $awalBulanObj = new DateTime($lastCreated);
                $awalBulanObj->modify('first day of this month');
                $awalBulan = $awalBulanObj->format('Y-m-d');
            }

            return array(
                'awal_bulan'   => $awalBulan,
                'last_created' => $lastCreated,
                'last_closing' => $closingDate
            );
        });

        sendResponse(200, "Tanggal terakhir data nominatif", $data);
    }

    public function getDefaultDatePH() {
        $data = $this->remember('default_nominatif_hapus_buku', function() {
            return array(
                'last_created' => $this->getLastCreatedFrom('nominatif_hapus_buku')
            );
        });

        sendResponse(200, "Tanggal terakhir data nominatif", $data);
    }


    public function getDefaultAccountHandle() {
        $data = $this->remember('default_account_handle', function() {
            return array(
                'last_created' => $this->getLastCreatedFrom('account_handle')
            );
        });

        sendResponse(200, "Tanggal terakhir data nominatif", $data);
    }

    // public function getDefaultAccountHandle() {
    //     $sql = "SELECT MAX(created) AS last_created FROM account_handle";
    //     $stmt = $this->pdo->prepare($sql);
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //     $lastCreated = $result['last_created'];
  

    //     sendResponse(200, "Tanggal terakhir data nominatif", [
    //         'last_created' => $lastCreated,
           
    //     ]);
    // }


    












}

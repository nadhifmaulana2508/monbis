<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/sso_guard.php';
require_once __DIR__ . '/../helpers/rbac.php';
// require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $pdo;
    private $cacheNamespace = 'report_dpk_auth_cache';

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

    public function login($data) {
        sendResponse(410, 'Gunakan layanan autentikasi SSO MONBIS.');
    }

    public function whoami($token) {
        $user = requireAppAuth();
        sendResponse(200, 'Data user berhasil diambil', $user);
    }
}

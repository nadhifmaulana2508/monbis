<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middlewares/auth.php';
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
        $employee_id = $data['employee_id'] ?? '';
        $password = $data['password'] ?? '';

        // Cek user di database
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE employee_id = :employee_id");
        $stmt->execute([':employee_id' => $employee_id]);
        $user = $stmt->fetch();

        if (!$user || $password !== 'bkkjtg123') {
            sendResponse(401, "employee_id atau password salah");
        }

        $payload = [
            "id" => $user['id'],
            "employee_id" => $user['employee_id'],
            "iat" => time(),
            "exp" => time() + (60 * 60 * 5) // 1 jam
        ];

        $token = generateJWT($payload);
        sendResponse(200, "Login berhasil", ["token" => $token]);
    }

    public function whoami($token) {
        $decoded = verifyJWT($token);
    
        if (!$decoded) {
            sendResponse(401, "Token tidak valid atau kadaluarsa");
        }
    
        // Ambil employee_id dari payload token
        $employee_id = $decoded['employee_id'] ?? null;
    
        if (!$employee_id) {
            sendResponse(400, "ID Karyawan tidak ditemukan dalam token");
        }
    
        $user = $this->remember('whoami_v2_' . md5($employee_id), 300, function () use ($employee_id) {
            $stmt = $this->pdo->prepare("SELECT id, kode, employee_id, full_name, job_position, branch_name, unit_kerja, level, group_jabatan, role FROM users WHERE employee_id = :employee_id");
            $stmt->execute([':employee_id' => $employee_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: [];
        });
    
        if (empty($user)) {
            sendResponse(404, "User tidak ditemukan");
        }

        header('Cache-Control: private, max-age=300');
        sendResponse(200, "Data user berhasil diambil", $user);
    }
}

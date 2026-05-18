<?php

require_once __DIR__ . '/../helpers/response.php';

class DevTrackingController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ============================================================
    // MODULE CRUD
    // ============================================================

    public function getModules() {
        $sql = "SELECT * FROM dev_module ORDER BY urutan ASC";
        $stmt = $this->pdo->query($sql);
        sendResponse(200, "Sukses", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function createModule($input) {
        $sql = "INSERT INTO dev_module (kode_module, nama_module, icon, urutan, is_dev_only) VALUES (:kode, :nama, :icon, :urutan, :dev)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':kode'   => strtoupper($input['kode_module'] ?? ''),
            ':nama'   => $input['nama_module'] ?? '',
            ':icon'   => $input['icon'] ?? null,
            ':urutan' => $input['urutan'] ?? 0,
            ':dev'    => $input['is_dev_only'] ?? 0
        ]);
        sendResponse(201, "Module berhasil dibuat", ['id' => $this->pdo->lastInsertId()]);
    }

    public function updateModule($input) {
        $id = $input['id'] ?? null;
        if (!$id) return sendResponse(400, "ID module wajib");

        $fields = [];
        $params = [':id' => $id];

        if (isset($input['kode_module']))  { $fields[] = "kode_module = :kode"; $params[':kode'] = strtoupper($input['kode_module']); }
        if (isset($input['nama_module']))  { $fields[] = "nama_module = :nama"; $params[':nama'] = $input['nama_module']; }
        if (isset($input['icon']))         { $fields[] = "icon = :icon"; $params[':icon'] = $input['icon']; }
        if (isset($input['urutan']))       { $fields[] = "urutan = :urutan"; $params[':urutan'] = $input['urutan']; }
        if (isset($input['is_dev_only']))  { $fields[] = "is_dev_only = :dev"; $params[':dev'] = $input['is_dev_only']; }

        if (empty($fields)) return sendResponse(400, "Tidak ada field yang diupdate");

        $sql = "UPDATE dev_module SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        sendResponse(200, "Module berhasil diupdate");
    }

    public function deleteModule($input) {
        $id = $input['id'] ?? null;
        if (!$id) return sendResponse(400, "ID module wajib");

        $stmt = $this->pdo->prepare("DELETE FROM dev_module WHERE id = :id");
        $stmt->execute([':id' => $id]);
        sendResponse(200, "Module berhasil dihapus");
    }

    // ============================================================
    // FEATURE CRUD
    // ============================================================

    public function getFeatures($input) {
        $module_id = $input['module_id'] ?? null;
        $status = $input['status'] ?? null;

        $sql = "SELECT f.*, m.nama_module, m.kode_module 
                FROM dev_feature f 
                JOIN dev_module m ON f.module_id = m.id 
                WHERE 1=1";
        $params = [];

        if ($module_id) {
            $sql .= " AND f.module_id = :module_id";
            $params[':module_id'] = $module_id;
        }
        if ($status) {
            $sql .= " AND f.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY m.urutan, f.urutan ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        sendResponse(200, "Sukses", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getFeatureDetail($input) {
        $id = $input['id'] ?? null;
        if (!$id) return sendResponse(400, "ID feature wajib");

        $stmt = $this->pdo->prepare("SELECT f.*, m.nama_module FROM dev_feature f JOIN dev_module m ON f.module_id = m.id WHERE f.id = :id");
        $stmt->execute([':id' => $id]);
        $feature = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$feature) return sendResponse(404, "Feature tidak ditemukan");

        // Ambil log terkait
        $stmtLog = $this->pdo->prepare("SELECT * FROM dev_progress_log WHERE feature_id = :id ORDER BY created_at DESC LIMIT 20");
        $stmtLog->execute([':id' => $id]);
        $logs = $stmtLog->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(200, "Sukses", ['feature' => $feature, 'logs' => $logs]);
    }

    public function createFeature($input) {
        $sql = "INSERT INTO dev_feature (module_id, kode_fitur, nama_fitur, slug, deskripsi, file_page, file_controller, file_route, status, progress_persen, prioritas, assignee, tanggal_mulai, deadline, urutan) 
                VALUES (:module_id, :kode, :nama, :slug, :desk, :fp, :fc, :fr, :status, :progress, :prioritas, :assignee, :tgl_mulai, :deadline, :urutan)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':module_id' => $input['module_id'],
            ':kode'      => strtoupper($input['kode_fitur'] ?? ''),
            ':nama'      => $input['nama_fitur'] ?? '',
            ':slug'      => $input['slug'] ?? '',
            ':desk'      => $input['deskripsi'] ?? null,
            ':fp'        => $input['file_page'] ?? null,
            ':fc'        => $input['file_controller'] ?? null,
            ':fr'        => $input['file_route'] ?? null,
            ':status'    => $input['status'] ?? 'backlog',
            ':progress'  => $input['progress_persen'] ?? 0,
            ':prioritas' => $input['prioritas'] ?? 'medium',
            ':assignee'  => $input['assignee'] ?? null,
            ':tgl_mulai' => $input['tanggal_mulai'] ?? null,
            ':deadline'  => $input['deadline'] ?? null,
            ':urutan'    => $input['urutan'] ?? 0
        ]);
        sendResponse(201, "Feature berhasil dibuat", ['id' => $this->pdo->lastInsertId()]);
    }

    public function updateFeature($input) {
        $id = $input['id'] ?? null;
        if (!$id) return sendResponse(400, "ID feature wajib");

        // Ambil status & progress sebelumnya untuk log
        $stmtOld = $this->pdo->prepare("SELECT status, progress_persen FROM dev_feature WHERE id = :id");
        $stmtOld->execute([':id' => $id]);
        $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

        $fields = [];
        $params = [':id' => $id];

        $allowedFields = ['module_id','kode_fitur','nama_fitur','slug','deskripsi','file_page','file_controller','file_route','status','progress_persen','prioritas','assignee','tanggal_mulai','tanggal_selesai','deadline','urutan'];

        foreach ($allowedFields as $f) {
            if (isset($input[$f])) {
                $fields[] = "$f = :$f";
                $params[":$f"] = $input[$f];
            }
        }

        // Auto-set tanggal_mulai jika status berubah ke in_progress
        if (isset($input['status']) && $input['status'] === 'in_progress' && !isset($input['tanggal_mulai'])) {
            $fields[] = "tanggal_mulai = COALESCE(tanggal_mulai, CURDATE())";
        }

        // Auto-set tanggal_selesai jika status berubah ke done
        if (isset($input['status']) && $input['status'] === 'done' && !isset($input['tanggal_selesai'])) {
            $fields[] = "tanggal_selesai = CURDATE()";
        }

        if (empty($fields)) return sendResponse(400, "Tidak ada field yang diupdate");

        $sql = "UPDATE dev_feature SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Auto-insert log jika status atau progress berubah
        $newStatus = $input['status'] ?? $old['status'];
        $newProgress = $input['progress_persen'] ?? $old['progress_persen'];
        $catatan = $input['catatan'] ?? null;

        if ($catatan || $old['status'] !== $newStatus || (int)$old['progress_persen'] !== (int)$newProgress) {
            $this->insertProgressLog([
                'feature_id'       => $id,
                'catatan'          => $catatan ?? "Update: {$old['status']}({$old['progress_persen']}%) → {$newStatus}({$newProgress}%)",
                'status_sebelum'   => $old['status'],
                'status_sesudah'   => $newStatus,
                'progress_sebelum' => $old['progress_persen'],
                'progress_sesudah' => $newProgress,
                'dikerjakan_oleh'  => $input['dikerjakan_oleh'] ?? null,
                'session_id'       => $input['session_id'] ?? null
            ]);
        }

        sendResponse(200, "Feature berhasil diupdate");
    }

    public function deleteFeature($input) {
        $id = $input['id'] ?? null;
        if (!$id) return sendResponse(400, "ID feature wajib");

        $stmt = $this->pdo->prepare("DELETE FROM dev_feature WHERE id = :id");
        $stmt->execute([':id' => $id]);
        sendResponse(200, "Feature berhasil dihapus");
    }

    // ============================================================
    // PROGRESS LOG
    // ============================================================

    public function getProgressLogs($input) {
        $feature_id = $input['feature_id'] ?? null;
        $limit = $input['limit'] ?? 50;

        $sql = "SELECT l.*, f.nama_fitur, f.slug 
                FROM dev_progress_log l 
                JOIN dev_feature f ON l.feature_id = f.id 
                WHERE 1=1";
        $params = [];

        if ($feature_id) {
            $sql .= " AND l.feature_id = :fid";
            $params[':fid'] = $feature_id;
        }

        $sql .= " ORDER BY l.created_at DESC LIMIT " . (int)$limit;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        sendResponse(200, "Sukses", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function createProgressLog($input) {
        $feature_id = $input['feature_id'] ?? null;
        if (!$feature_id) return sendResponse(400, "feature_id wajib");

        $this->insertProgressLog($input);

        // Auto-update feature jika status/progress dikirim
        if (isset($input['status_sesudah']) || isset($input['progress_sesudah'])) {
            $updateFields = [];
            $updateParams = [':id' => $feature_id];

            if (isset($input['status_sesudah'])) {
                $updateFields[] = "status = :status";
                $updateParams[':status'] = $input['status_sesudah'];

                if ($input['status_sesudah'] === 'done') {
                    $updateFields[] = "tanggal_selesai = CURDATE()";
                } elseif ($input['status_sesudah'] === 'in_progress') {
                    $updateFields[] = "tanggal_mulai = COALESCE(tanggal_mulai, CURDATE())";
                }
            }
            if (isset($input['progress_sesudah'])) {
                $updateFields[] = "progress_persen = :progress";
                $updateParams[':progress'] = $input['progress_sesudah'];
            }

            if (!empty($updateFields)) {
                $sql = "UPDATE dev_feature SET " . implode(', ', $updateFields) . " WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($updateParams);
            }
        }

        sendResponse(201, "Log progress berhasil dicatat");
    }

    private function insertProgressLog($input) {
        $sql = "INSERT INTO dev_progress_log (feature_id, catatan, status_sebelum, status_sesudah, progress_sebelum, progress_sesudah, dikerjakan_oleh, session_id)
                VALUES (:fid, :catatan, :sb, :sa, :pb, :pa, :oleh, :session)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':fid'     => $input['feature_id'],
            ':catatan' => $input['catatan'] ?? '',
            ':sb'      => $input['status_sebelum'] ?? null,
            ':sa'      => $input['status_sesudah'] ?? null,
            ':pb'      => $input['progress_sebelum'] ?? null,
            ':pa'      => $input['progress_sesudah'] ?? null,
            ':oleh'    => $input['dikerjakan_oleh'] ?? null,
            ':session' => $input['session_id'] ?? null
        ]);
    }

    // ============================================================
    // BACKLOG IDEAS
    // ============================================================

    public function getBacklogIdeas($input) {
        $status = $input['status'] ?? null;

        $sql = "SELECT b.*, m.nama_module FROM dev_backlog_idea b LEFT JOIN dev_module m ON b.module_id = m.id WHERE 1=1";
        $params = [];

        if ($status) {
            $sql .= " AND b.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY FIELD(b.prioritas, 'critical','high','medium','low'), b.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        sendResponse(200, "Sukses", $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function createBacklogIdea($input) {
        $sql = "INSERT INTO dev_backlog_idea (judul, deskripsi, module_id, prioritas, diusulkan_oleh) VALUES (:judul, :desk, :mid, :pri, :oleh)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':judul' => $input['judul'] ?? '',
            ':desk'  => $input['deskripsi'] ?? null,
            ':mid'   => $input['module_id'] ?? null,
            ':pri'   => $input['prioritas'] ?? 'medium',
            ':oleh'  => $input['diusulkan_oleh'] ?? null
        ]);
        sendResponse(201, "Ide berhasil ditambahkan", ['id' => $this->pdo->lastInsertId()]);
    }

    public function updateBacklogIdea($input) {
        $id = $input['id'] ?? null;
        if (!$id) return sendResponse(400, "ID wajib");

        $fields = [];
        $params = [':id' => $id];

        $allowedFields = ['judul','deskripsi','module_id','prioritas','status','diusulkan_oleh'];
        foreach ($allowedFields as $f) {
            if (isset($input[$f])) {
                $fields[] = "$f = :$f";
                $params[":$f"] = $input[$f];
            }
        }

        if (empty($fields)) return sendResponse(400, "Tidak ada field yang diupdate");

        $sql = "UPDATE dev_backlog_idea SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        sendResponse(200, "Ide berhasil diupdate");
    }

    public function deleteBacklogIdea($input) {
        $id = $input['id'] ?? null;
        if (!$id) return sendResponse(400, "ID wajib");

        $stmt = $this->pdo->prepare("DELETE FROM dev_backlog_idea WHERE id = :id");
        $stmt->execute([':id' => $id]);
        sendResponse(200, "Ide berhasil dihapus");
    }

    // ============================================================
    // DASHBOARD SUMMARY (Untuk Overview)
    // ============================================================

    public function getSummary() {
        // Ringkasan per module
        $sql = "SELECT 
                    m.id as module_id, m.nama_module, m.kode_module,
                    COUNT(f.id) AS total_fitur,
                    SUM(CASE WHEN f.status = 'done' THEN 1 ELSE 0 END) AS done,
                    SUM(CASE WHEN f.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
                    SUM(CASE WHEN f.status = 'backlog' THEN 1 ELSE 0 END) AS backlog,
                    SUM(CASE WHEN f.status = 'blocked' THEN 1 ELSE 0 END) AS blocked,
                    ROUND(AVG(f.progress_persen), 0) AS avg_progress
                FROM dev_module m
                LEFT JOIN dev_feature f ON f.module_id = m.id
                GROUP BY m.id, m.nama_module, m.kode_module
                ORDER BY m.urutan";

        $stmt = $this->pdo->query($sql);
        $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Grand total
        $sqlTotal = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) AS done,
                        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
                        SUM(CASE WHEN status = 'backlog' THEN 1 ELSE 0 END) AS backlog,
                        SUM(CASE WHEN status = 'blocked' THEN 1 ELSE 0 END) AS blocked,
                        ROUND(AVG(progress_persen), 0) AS avg_progress
                    FROM dev_feature";
        $stmtT = $this->pdo->query($sqlTotal);
        $grand = $stmtT->fetch(PDO::FETCH_ASSOC);

        // Recent logs
        $sqlLogs = "SELECT l.*, f.nama_fitur FROM dev_progress_log l JOIN dev_feature f ON l.feature_id = f.id ORDER BY l.created_at DESC LIMIT 10";
        $stmtL = $this->pdo->query($sqlLogs);
        $recentLogs = $stmtL->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(200, "Sukses", [
            'modules'     => $modules,
            'grand_total' => $grand,
            'recent_logs' => $recentLogs
        ]);
    }
}

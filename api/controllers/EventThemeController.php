<?php

require_once __DIR__ . '/../helpers/response.php';

class EventThemeController
{
    private $pdo;
    private $uploadDir;
    private $uploadUrl;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $root = dirname(__DIR__, 2);
        $this->uploadDir = $root . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'events';
        $this->uploadUrl = 'img/events';
        $this->ensureStorage();
    }

    private function ensureStorage()
    {
        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0775, true);
        }

        $sql = "CREATE TABLE IF NOT EXISTS monbis_event_themes (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_name VARCHAR(120) NOT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            start_date DATE NULL,
            end_date DATE NULL,
            accent_color VARCHAR(20) NOT NULL DEFAULT '#2563eb',
            header_bg VARCHAR(20) NOT NULL DEFAULT '#ffffff',
            sidebar_bg VARCHAR(20) NOT NULL DEFAULT '#ffffff',
            text_color VARCHAR(20) NOT NULL DEFAULT '#0f172a',
            sidebar_text VARCHAR(20) NOT NULL DEFAULT '#334155',
            font_family VARCHAR(80) NOT NULL DEFAULT 'Inter, system-ui, sans-serif',
            image_path VARCHAR(255) NULL,
            image_fit VARCHAR(20) NOT NULL DEFAULT 'cover',
            image_position VARCHAR(40) NOT NULL DEFAULT 'center',
            created_by VARCHAR(30) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_event_active_period (is_active, start_date, end_date),
            KEY idx_event_updated (updated_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->pdo->exec($sql);
        $this->ensureColumn('monbis_event_themes', 'image_fit', "VARCHAR(20) NOT NULL DEFAULT 'cover'");
        $this->ensureColumn('monbis_event_themes', 'image_position', "VARCHAR(40) NOT NULL DEFAULT 'center'");
    }

    private function ensureColumn($table, $column, $definition)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND COLUMN_NAME = :column_name");
        $stmt->execute(array(':table_name' => $table, ':column_name' => $column));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || (int)$row['total'] === 0) {
            $this->pdo->exec("ALTER TABLE `$table` ADD `$column` $definition");
        }
    }

    private function inputValue($input, $key, $default = '')
    {
        return isset($input[$key]) ? trim((string)$input[$key]) : $default;
    }

    private function adminId($input)
    {
        $headers = function_exists('getallheaders') ? getallheaders() : array();
        $candidates = array(
            isset($input['id_peg']) ? $input['id_peg'] : null,
            isset($input['idPeg']) ? $input['idPeg'] : null,
            isset($input['id_pegawai']) ? $input['id_pegawai'] : null,
            isset($input['idPegawai']) ? $input['idPegawai'] : null,
            isset($headers['X-Id-Peg']) ? $headers['X-Id-Peg'] : null,
            isset($headers['x-id-peg']) ? $headers['x-id-peg'] : null
        );

        foreach ($candidates as $candidate) {
            $value = trim((string) $candidate);
            if ($value === '102-119') {
                return $value;
            }
        }
        return '';
    }

    private function assertAdmin($input)
    {
        $id = $this->adminId($input);
        if ($id !== '102-119') {
            sendResponse(403, 'Akses admin event hanya untuk id_peg 102-119.');
        }
        return $id;
    }

    private function color($value, $fallback)
    {
        $value = trim((string)$value);
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
            return $value;
        }
        return $fallback;
    }

    private function font($value)
    {
        $allowed = array(
            'Inter, system-ui, sans-serif',
            'Poppins, system-ui, sans-serif',
            'Nunito, system-ui, sans-serif',
            'Montserrat, system-ui, sans-serif',
            'Arial, system-ui, sans-serif'
        );
        return in_array($value, $allowed, true) ? $value : $allowed[0];
    }

    private function imageFit($value)
    {
        $allowed = array('cover', 'contain');
        return in_array($value, $allowed, true) ? $value : 'cover';
    }

    private function imagePosition($value)
    {
        $allowed = array('center', 'top', 'bottom', 'left', 'right', 'top center', 'bottom center', 'center left', 'center right');
        return in_array($value, $allowed, true) ? $value : 'center';
    }

    private function normalizeRow($row)
    {
        if (!$row) {
            return null;
        }
        return array(
            'id' => (int)$row['id'],
            'event_name' => $row['event_name'],
            'is_active' => (int)$row['is_active'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'accent_color' => $row['accent_color'],
            'header_bg' => $row['header_bg'],
            'sidebar_bg' => $row['sidebar_bg'],
            'text_color' => $row['text_color'],
            'sidebar_text' => $row['sidebar_text'],
            'font_family' => $row['font_family'],
            'image_path' => $row['image_path'],
            'image_fit' => isset($row['image_fit']) ? $row['image_fit'] : 'cover',
            'image_position' => isset($row['image_position']) ? $row['image_position'] : 'center',
            'created_by' => $row['created_by'],
            'updated_at' => $row['updated_at']
        );
    }

    private function uploadImage($current = null)
    {
        if (empty($_FILES['event_image']) || !is_uploaded_file($_FILES['event_image']['tmp_name'])) {
            return $current;
        }

        $file = $_FILES['event_image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            sendResponse(400, 'Upload gambar gagal.');
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            sendResponse(400, 'Ukuran gambar maksimal 2MB.');
        }

        $info = @getimagesize($file['tmp_name']);
        if (!$info) {
            sendResponse(400, 'File harus berupa gambar.');
        }

        $ext = 'jpg';
        if ($info[2] === IMAGETYPE_PNG) $ext = 'png';
        elseif ($info[2] === IMAGETYPE_GIF) $ext = 'gif';
        elseif ($info[2] === IMAGETYPE_WEBP) $ext = 'webp';
        elseif ($info[2] !== IMAGETYPE_JPEG) {
            sendResponse(400, 'Format gambar hanya jpg, png, gif, atau webp.');
        }

        $name = 'event_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $dest = $this->uploadDir . DIRECTORY_SEPARATOR . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            sendResponse(500, 'Gagal menyimpan gambar event.');
        }
        return $this->uploadUrl . '/' . $name;
    }

    public function active()
    {
        $today = date('Y-m-d');
        $sql = "SELECT *
                FROM monbis_event_themes
                WHERE is_active = 1
                  AND (start_date IS NULL OR start_date <= :today_start)
                  AND (end_date IS NULL OR end_date >= :today_end)
                ORDER BY COALESCE(updated_at, created_at) DESC, id DESC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(':today_start' => $today, ':today_end' => $today));
        sendResponse(200, 'OK', $this->normalizeRow($stmt->fetch(PDO::FETCH_ASSOC)));
    }

    public function listing($input)
    {
        $this->assertAdmin($input);
        $stmt = $this->pdo->query("SELECT * FROM monbis_event_themes ORDER BY is_active DESC, COALESCE(updated_at, created_at) DESC, id DESC LIMIT 50");
        $rows = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $this->normalizeRow($row);
        }
        sendResponse(200, 'OK', $rows);
    }

    public function save($input)
    {
        $adminId = $this->assertAdmin($input);
        $id = (int)$this->inputValue($input, 'id', '0');

        $current = null;
        if ($id > 0) {
            $stmt = $this->pdo->prepare("SELECT * FROM monbis_event_themes WHERE id = :id");
            $stmt->execute(array(':id' => $id));
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $image = $this->uploadImage($current ? $current['image_path'] : null);
        if ($this->inputValue($input, 'remove_image', '0') === '1') {
            $image = null;
        }

        $data = array(
            ':event_name' => $this->inputValue($input, 'event_name', 'Event Monbis'),
            ':is_active' => $this->inputValue($input, 'is_active', '1') === '1' ? 1 : 0,
            ':start_date' => $this->inputValue($input, 'start_date', '') ?: null,
            ':end_date' => $this->inputValue($input, 'end_date', '') ?: null,
            ':accent_color' => $this->color($this->inputValue($input, 'accent_color', ''), '#2563eb'),
            ':header_bg' => $this->color($this->inputValue($input, 'header_bg', ''), '#ffffff'),
            ':sidebar_bg' => $this->color($this->inputValue($input, 'sidebar_bg', ''), '#ffffff'),
            ':text_color' => $this->color($this->inputValue($input, 'text_color', ''), '#0f172a'),
            ':sidebar_text' => $this->color($this->inputValue($input, 'sidebar_text', ''), '#334155'),
            ':font_family' => $this->font($this->inputValue($input, 'font_family', '')),
            ':image_path' => $image,
            ':image_fit' => $this->imageFit($this->inputValue($input, 'image_fit', 'cover')),
            ':image_position' => $this->imagePosition($this->inputValue($input, 'image_position', 'center')),
            ':created_by' => (string)$adminId
        );

        if ($id > 0 && $current) {
            $data[':id'] = $id;
            $sql = "UPDATE monbis_event_themes
                    SET event_name = :event_name,
                        is_active = :is_active,
                        start_date = :start_date,
                        end_date = :end_date,
                        accent_color = :accent_color,
                        header_bg = :header_bg,
                        sidebar_bg = :sidebar_bg,
                        text_color = :text_color,
                        sidebar_text = :sidebar_text,
                        font_family = :font_family,
                        image_path = :image_path,
                        image_fit = :image_fit,
                        image_position = :image_position,
                        created_by = :created_by
                    WHERE id = :id";
            $this->pdo->prepare($sql)->execute($data);
        } else {
            $sql = "INSERT INTO monbis_event_themes
                    (event_name, is_active, start_date, end_date, accent_color, header_bg, sidebar_bg, text_color, sidebar_text, font_family, image_path, image_fit, image_position, created_by)
                    VALUES
                    (:event_name, :is_active, :start_date, :end_date, :accent_color, :header_bg, :sidebar_bg, :text_color, :sidebar_text, :font_family, :image_path, :image_fit, :image_position, :created_by)";
            $this->pdo->prepare($sql)->execute($data);
            $id = (int)$this->pdo->lastInsertId();
        }

        $stmt = $this->pdo->prepare("SELECT * FROM monbis_event_themes WHERE id = :id");
        $stmt->execute(array(':id' => $id));
        sendResponse(200, 'Event berhasil disimpan.', $this->normalizeRow($stmt->fetch(PDO::FETCH_ASSOC)));
    }

    public function delete($input)
    {
        $this->assertAdmin($input);
        $id = (int)$this->inputValue($input, 'id', '0');
        if ($id <= 0) {
            sendResponse(400, 'ID event tidak valid.');
        }
        $stmt = $this->pdo->prepare("DELETE FROM monbis_event_themes WHERE id = :id");
        $stmt->execute(array(':id' => $id));
        sendResponse(200, 'Event berhasil dihapus.');
    }
}

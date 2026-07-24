<?php

require_once __DIR__ . '/../helpers/response.php';

class ProspekSyncController
{
    private $pdoProspek;
    private $targetTable = 'prospects';

    public function __construct($pdoProspek)
    {
        $this->pdoProspek = $pdoProspek;
    }

    public function createFromEprospek($input = null)
    {
        $b = is_array($input) ? $input : [];

        $oldId = (int)($b['id_prospects'] ?? ($b['id_prospect'] ?? ($b['old_id'] ?? 0)));
        $name = $this->clean($b['customer_name'] ?? ($b['nama'] ?? ($b['nama_nasabah'] ?? '')));
        $phone = $this->clean($b['phone_number'] ?? ($b['no_hp'] ?? ($b['hp'] ?? '')));
        $kodeKantor = $this->normalKodeKantor($b['kode_kantor'] ?? ($b['kode_cabang'] ?? '000'));
        $product = $this->normalProduct($b['rekomendasi_produk'] ?? ($b['jenis_produk'] ?? ($b['produk'] ?? 'Kredit')));
        $type = $this->normalProspectType($b['prospect_type'] ?? '', $product);

        if ($oldId <= 0) {
            sendResponse(400, 'id_prospects dari e-prospek lama wajib diisi.');
        }
        if ($name === '' || $phone === '') {
            sendResponse(400, 'customer_name/nama dan phone_number/no_hp wajib diisi.');
        }
        if ($type === '') {
            sendResponse(400, 'prospect_type/rekomendasi_produk tidak valid.');
        }

        $createdBy = $this->clean($b['created_by'] ?? ($b['input_by'] ?? ($b['employee_id'] ?? 'eprospek')));
        $createdByKantor = $this->normalKodeKantor($b['created_by_kode_kantor'] ?? $kodeKantor);
        $status = $this->normalStatus($b['status'] ?? 'OPEN');
        $createdAt = $this->normalDateTime($b['created_at'] ?? ($b['tanggal_prospek'] ?? null));

        try {
            $this->targetTable = $this->resolveTargetTable();
            $this->ensureSourceIdColumn();

            $this->pdoProspek->beginTransaction();

            $existing = $this->findByOldId($oldId);
            if ($existing) {
                $this->updateProspect($existing['id'], $b, [
                    'old_id' => $oldId,
                    'type' => $type,
                    'name' => $name,
                    'phone' => $phone,
                    'kode_kantor' => $kodeKantor,
                    'product' => $product,
                    'status' => $status,
                    'created_by' => $createdBy,
                    'created_by_kode_kantor' => $createdByKantor
                ]);
                $prospectId = (int)$existing['id'];
                $action = 'updated';
            } else {
                $prospectId = $this->insertProspect($b, [
                    'old_id' => $oldId,
                    'type' => $type,
                    'name' => $name,
                    'phone' => $phone,
                    'kode_kantor' => $kodeKantor,
                    'product' => $product,
                    'status' => $status,
                    'created_by' => $createdBy,
                    'created_by_kode_kantor' => $createdByKantor,
                    'created_at' => $createdAt
                ]);
                $this->addHistory($prospectId, 'SYNC_CREATED', null, $status, 'Sinkron dari e-prospek lama', $createdBy, [
                    'id_prospects' => $oldId
                ]);
                $action = 'created';
            }

            if ($type === 'KREDIT' || $type === 'DEBITUR_EXISTING') {
                $this->ensureCreditPipeline($prospectId, $b, $createdBy);
            }

            $this->pdoProspek->commit();

            sendResponse($action === 'created' ? 201 : 200, 'Prospek berhasil disinkronkan.', [
                'action' => $action,
                'id' => $prospectId,
                'id_prospects' => $oldId
            ]);
        } catch (PDOException $e) {
            if ($this->pdoProspek->inTransaction()) {
                $this->pdoProspek->rollBack();
            }
            sendResponse(500, 'Gagal sinkron prospek: ' . $e->getMessage());
        }
    }

    private function insertProspect($b, $v)
    {
        $sql = "INSERT INTO {$this->targetTable} (
                    id_prospects, prospect_type, customer_name, identity_number, phone_number,
                    jenis_usaha, rekomendasi_produk, keterangan_usaha, provinsi, kab_kota, kecamatan, desa,
                    address, latitude, longitude, geo_address, foto_url,
                    kode_kantor, description, created_by, created_by_kode_kantor, referral_by, is_ao_input,
                    delegation_status, assigned_to, assigned_by, assigned_at, status, created_at
                ) VALUES (
                    :id_prospects, :type, :name, :identity, :phone,
                    :jenis_usaha, :product, :keterangan_usaha, :provinsi, :kab_kota, :kecamatan, :desa,
                    :address, :latitude, :longitude, :geo_address, :foto_url,
                    :kode_kantor, :description, :created_by, :created_by_kode_kantor, :referral_by, 0,
                    'BELUM_DIDELEGASIKAN', NULL, NULL, NULL, :status, :created_at
                )";

        $stmt = $this->pdoProspek->prepare($sql);
        $stmt->execute([
            ':id_prospects' => $v['old_id'],
            ':type' => $v['type'],
            ':name' => $v['name'],
            ':identity' => $this->nullable($b['identity_number'] ?? ($b['nik'] ?? null)),
            ':phone' => $v['phone'],
            ':jenis_usaha' => $this->nullable($b['jenis_usaha'] ?? null),
            ':product' => $v['product'],
            ':keterangan_usaha' => $this->nullable($b['keterangan_usaha'] ?? ($b['catatan_usaha'] ?? null)),
            ':provinsi' => $this->nullable($b['provinsi'] ?? null),
            ':kab_kota' => $this->nullable($b['kab_kota'] ?? ($b['kabupaten'] ?? null)),
            ':kecamatan' => $this->nullable($b['kecamatan'] ?? null),
            ':desa' => $this->nullable($b['desa'] ?? ($b['kelurahan'] ?? null)),
            ':address' => $this->nullable($b['address'] ?? ($b['alamat'] ?? null)),
            ':latitude' => $this->nullable($b['latitude'] ?? ($b['lokasi_lat'] ?? null)),
            ':longitude' => $this->nullable($b['longitude'] ?? ($b['lokasi_lng'] ?? null)),
            ':geo_address' => $this->nullable($b['geo_address'] ?? null),
            ':foto_url' => $this->nullable($b['foto_url'] ?? null),
            ':kode_kantor' => $v['kode_kantor'],
            ':description' => $this->nullable($b['description'] ?? ($b['catatan'] ?? null)),
            ':created_by' => $v['created_by'],
            ':created_by_kode_kantor' => $v['created_by_kode_kantor'],
            ':referral_by' => $this->nullable($b['referral_by'] ?? ($b['referral_user_id'] ?? null)),
            ':status' => $v['status'],
            ':created_at' => $v['created_at']
        ]);

        return (int)$this->pdoProspek->lastInsertId();
    }

    private function updateProspect($id, $b, $v)
    {
        $sql = "UPDATE {$this->targetTable} SET
                    prospect_type = :type,
                    customer_name = :name,
                    identity_number = :identity,
                    phone_number = :phone,
                    jenis_usaha = :jenis_usaha,
                    rekomendasi_produk = :product,
                    keterangan_usaha = :keterangan_usaha,
                    provinsi = :provinsi,
                    kab_kota = :kab_kota,
                    kecamatan = :kecamatan,
                    desa = :desa,
                    address = :address,
                    latitude = :latitude,
                    longitude = :longitude,
                    geo_address = :geo_address,
                    foto_url = COALESCE(:foto_url, foto_url),
                    kode_kantor = :kode_kantor,
                    description = :description,
                    created_by_kode_kantor = :created_by_kode_kantor,
                    referral_by = :referral_by,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdoProspek->prepare($sql);
        $stmt->execute([
            ':type' => $v['type'],
            ':name' => $v['name'],
            ':identity' => $this->nullable($b['identity_number'] ?? ($b['nik'] ?? null)),
            ':phone' => $v['phone'],
            ':jenis_usaha' => $this->nullable($b['jenis_usaha'] ?? null),
            ':product' => $v['product'],
            ':keterangan_usaha' => $this->nullable($b['keterangan_usaha'] ?? ($b['catatan_usaha'] ?? null)),
            ':provinsi' => $this->nullable($b['provinsi'] ?? null),
            ':kab_kota' => $this->nullable($b['kab_kota'] ?? ($b['kabupaten'] ?? null)),
            ':kecamatan' => $this->nullable($b['kecamatan'] ?? null),
            ':desa' => $this->nullable($b['desa'] ?? ($b['kelurahan'] ?? null)),
            ':address' => $this->nullable($b['address'] ?? ($b['alamat'] ?? null)),
            ':latitude' => $this->nullable($b['latitude'] ?? ($b['lokasi_lat'] ?? null)),
            ':longitude' => $this->nullable($b['longitude'] ?? ($b['lokasi_lng'] ?? null)),
            ':geo_address' => $this->nullable($b['geo_address'] ?? null),
            ':foto_url' => $this->nullable($b['foto_url'] ?? null),
            ':kode_kantor' => $v['kode_kantor'],
            ':description' => $this->nullable($b['description'] ?? ($b['catatan'] ?? null)),
            ':created_by_kode_kantor' => $v['created_by_kode_kantor'],
            ':referral_by' => $this->nullable($b['referral_by'] ?? ($b['referral_user_id'] ?? null)),
            ':status' => $v['status'],
            ':id' => $id
        ]);
    }

    private function ensureCreditPipeline($prospectId, $b, $createdBy)
    {
        if (!$this->tableExists('prospect_credit_pipelines')) {
            return;
        }

        $stmt = $this->pdoProspek->prepare("SELECT id FROM prospect_credit_pipelines WHERE prospect_id = :id LIMIT 1");
        $stmt->execute([':id' => $prospectId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        $requestedAmount = $this->numericOrNull($b['requested_loan_amount'] ?? ($b['rencana_plafon'] ?? ($b['plafon'] ?? null)));
        if ($existing) {
            if ($requestedAmount !== null) {
                $up = $this->pdoProspek->prepare("UPDATE prospect_credit_pipelines SET requested_loan_amount = :amount, updated_at = NOW() WHERE id = :id");
                $up->execute([':amount' => $requestedAmount, ':id' => (int)$existing['id']]);
            }
            return;
        }

        $ins = $this->pdoProspek->prepare("INSERT INTO prospect_credit_pipelines
            (prospect_id, assigned_to, requested_loan_amount, confirmation_at, current_stage, pipeline_status, created_by)
            VALUES (:prospect_id, NULL, :amount, NOW(), 'FORMULIR', 'PROSPECT_CONFIRMED', :created_by)");
        $ins->execute([
            ':prospect_id' => $prospectId,
            ':amount' => $requestedAmount,
            ':created_by' => $createdBy
        ]);
    }

    private function addHistory($prospectId, $action, $oldStatus, $newStatus, $note, $createdBy, $metadata = null)
    {
        if (!$this->tableExists('prospect_histories')) {
            return;
        }

        $stmt = $this->pdoProspek->prepare("INSERT INTO prospect_histories
            (prospect_id, action, old_status, new_status, note, metadata, created_by)
            VALUES (:prospect_id, :action, :old_status, :new_status, :note, :metadata, :created_by)");
        $stmt->execute([
            ':prospect_id' => $prospectId,
            ':action' => $action,
            ':old_status' => $oldStatus,
            ':new_status' => $newStatus,
            ':note' => $note,
            ':metadata' => $metadata ? json_encode($metadata) : null,
            ':created_by' => $createdBy
        ]);
    }

    private function ensureSourceIdColumn()
    {
        if (!$this->columnExists($this->targetTable, 'id_prospects')) {
            $this->pdoProspek->exec("ALTER TABLE {$this->targetTable} ADD COLUMN id_prospects BIGINT UNSIGNED NULL AFTER id");
        }

        if (!$this->indexExists($this->targetTable, 'uq_prospects_source_old')) {
            $this->pdoProspek->exec("ALTER TABLE {$this->targetTable} ADD UNIQUE KEY uq_prospects_source_old (id_prospects)");
        }
    }

    private function findByOldId($oldId)
    {
        $stmt = $this->pdoProspek->prepare("SELECT id FROM {$this->targetTable} WHERE id_prospects = :old_id LIMIT 1");
        $stmt->execute([':old_id' => $oldId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function resolveTargetTable()
    {
        if ($this->tableExists('prospects') && $this->columnExists('prospects', 'prospect_type') && $this->columnExists('prospects', 'customer_name')) {
            return 'prospects';
        }

        $this->ensureBridgeTable();
        return 'prospects_kunjungan_ao';
    }

    private function ensureBridgeTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS prospects_kunjungan_ao (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            id_prospects BIGINT UNSIGNED NULL,
            prospect_type ENUM('KREDIT','TABUNGAN','DEPOSITO','PEMBELI_ASET','DEBITUR_EXISTING') NOT NULL,
            customer_name VARCHAR(200) NOT NULL,
            identity_number VARCHAR(20) DEFAULT NULL,
            phone_number VARCHAR(20) NOT NULL,
            jenis_usaha VARCHAR(50) DEFAULT NULL,
            rekomendasi_produk ENUM('Tabungan','Deposito','Kredit','Aset') DEFAULT NULL,
            keterangan_usaha TEXT DEFAULT NULL,
            provinsi VARCHAR(100) DEFAULT NULL,
            kab_kota VARCHAR(100) DEFAULT NULL,
            kecamatan VARCHAR(100) DEFAULT NULL,
            desa VARCHAR(100) DEFAULT NULL,
            address TEXT DEFAULT NULL,
            latitude DECIMAL(10,7) DEFAULT NULL,
            longitude DECIMAL(10,7) DEFAULT NULL,
            geo_address TEXT DEFAULT NULL,
            foto_url VARCHAR(500) DEFAULT NULL,
            kode_kantor VARCHAR(5) NOT NULL,
            description TEXT DEFAULT NULL,
            created_by VARCHAR(20) NOT NULL,
            created_by_kode_kantor VARCHAR(5) NOT NULL,
            referral_by VARCHAR(20) DEFAULT NULL,
            is_ao_input TINYINT(1) NOT NULL DEFAULT 0,
            delegation_status ENUM('BELUM_DIDELEGASIKAN','SUDAH_DIDELEGASIKAN') NOT NULL DEFAULT 'BELUM_DIDELEGASIKAN',
            assigned_to VARCHAR(20) DEFAULT NULL,
            assigned_by VARCHAR(20) DEFAULT NULL,
            assigned_at DATETIME DEFAULT NULL,
            status ENUM('OPEN','FOLLOW_UP','SLA','REJECT','CLOSING') NOT NULL DEFAULT 'OPEN',
            sla_started_at DATETIME DEFAULT NULL,
            sla_started_by VARCHAR(20) DEFAULT NULL,
            rejected_at DATETIME DEFAULT NULL,
            reject_reason VARCHAR(255) DEFAULT NULL,
            reject_note TEXT DEFAULT NULL,
            closed_at DATETIME DEFAULT NULL,
            closing_account_number VARCHAR(30) DEFAULT NULL,
            closing_realization_amount BIGINT UNSIGNED DEFAULT NULL,
            closing_tenor INT UNSIGNED DEFAULT NULL,
            closing_note TEXT DEFAULT NULL,
            closing_asset_name VARCHAR(200) DEFAULT NULL,
            closing_buyer_name VARCHAR(200) DEFAULT NULL,
            closing_asset_purchase_method ENUM('LELANG','CESSIE','LAINNYA') DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uq_prospects_source_old (id_prospects),
            KEY idx_type (prospect_type),
            KEY idx_status (status),
            KEY idx_kode_kantor (kode_kantor),
            KEY idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->pdoProspek->exec($sql);
    }

    private function columnExists($table, $column)
    {
        $stmt = $this->pdoProspek->prepare("SELECT COUNT(1)
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND COLUMN_NAME = :column_name");
        $stmt->execute([':table_name' => $table, ':column_name' => $column]);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    private function indexExists($table, $index)
    {
        $stmt = $this->pdoProspek->prepare("SELECT COUNT(1)
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name
              AND INDEX_NAME = :index_name");
        $stmt->execute([':table_name' => $table, ':index_name' => $index]);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    private function tableExists($table)
    {
        $stmt = $this->pdoProspek->prepare("SELECT COUNT(1)
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = :table_name");
        $stmt->execute([':table_name' => $table]);
        return ((int)$stmt->fetchColumn()) > 0;
    }

    private function normalProspectType($type, $product)
    {
        $type = strtoupper(trim((string)$type));
        $valid = ['KREDIT','TABUNGAN','DEPOSITO','PEMBELI_ASET','DEBITUR_EXISTING'];
        if (in_array($type, $valid, true)) {
            return $type;
        }

        return match (strtolower((string)$product)) {
            'kredit' => 'KREDIT',
            'tabungan' => 'TABUNGAN',
            'deposito' => 'DEPOSITO',
            'aset' => 'PEMBELI_ASET',
            default => ''
        };
    }

    private function normalProduct($product)
    {
        $product = strtolower(trim((string)$product));
        return match ($product) {
            'tabungan' => 'Tabungan',
            'deposito' => 'Deposito',
            'aset', 'asset' => 'Aset',
            default => 'Kredit'
        };
    }

    private function normalStatus($status)
    {
        $status = strtoupper(str_replace([' ', '-'], '_', trim((string)$status)));
        return match ($status) {
            'FOLLOWUP', 'FOLLOW_UP' => 'FOLLOW_UP',
            'REJECTED', 'REJECT' => 'REJECT',
            'CLOSED', 'CLOSING' => 'CLOSING',
            'SLA' => 'SLA',
            default => 'OPEN'
        };
    }

    private function normalKodeKantor($kode)
    {
        $kode = trim((string)$kode);
        if ($kode === '') return '000';
        if (ctype_digit($kode)) return str_pad($kode, 3, '0', STR_PAD_LEFT);
        return $kode;
    }

    private function normalDateTime($value)
    {
        if (!$value) return date('Y-m-d H:i:s');
        $time = strtotime((string)$value);
        return $time ? date('Y-m-d H:i:s', $time) : date('Y-m-d H:i:s');
    }

    private function nullable($value)
    {
        if ($value === null) return null;
        $value = trim((string)$value);
        return $value === '' ? null : $value;
    }

    private function numericOrNull($value)
    {
        if ($value === null || $value === '') return null;
        $clean = preg_replace('/[^0-9.-]/', '', (string)$value);
        if ($clean === '' || !is_numeric($clean)) return null;
        return (int)round((float)$clean);
    }

    private function clean($value)
    {
        return trim((string)$value);
    }
}
?>

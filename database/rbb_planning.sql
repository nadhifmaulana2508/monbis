/*
   Struktur RBB Planning.
   Aman berdampingan dengan tabel rbb lama yang dipakai report berjalan.
   rbb_coa.kode_perk adalah kunci join ke acc_history.kode_perk.
*/

CREATE TABLE IF NOT EXISTS rbb_coa (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    kode_monbis VARCHAR(20) NOT NULL,
    kode_perk VARCHAR(50) NULL,
    sandi_lbbpr VARCHAR(50) NULL,
    kategori VARCHAR(50) NOT NULL,
    keterangan VARCHAR(255) NOT NULL,
    input_mode VARCHAR(16) NOT NULL DEFAULT 'MANUAL',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rbb_coa_monbis (kode_monbis),
    KEY idx_rbb_coa_kode_perk (kode_perk),
    KEY idx_rbb_coa_kategori (kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO rbb_coa (kode_monbis, kode_perk, sandi_lbbpr, kategori, keterangan, sort_order)
SELECT kode_monbis, NULLIF(kode_perkiraan, ''), sandi_lbbpr, kategori,
       COALESCE(NULLIF(keterangan, ''), kode_monbis), id_ref
FROM ref_rbb;

CREATE TABLE IF NOT EXISTS rbb_plan (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    tahun SMALLINT UNSIGNED NOT NULL,
    kode_kantor VARCHAR(3) NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'DRAFT',
    catatan TEXT NULL,
    created_by VARCHAR(50) NULL,
    updated_by VARCHAR(50) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rbb_plan_tahun_kantor (tahun, kode_kantor),
    KEY idx_rbb_plan_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rbb_plan_value (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    plan_id BIGINT UNSIGNED NOT NULL,
    kode_monbis VARCHAR(20) NOT NULL,
    bulan TINYINT UNSIGNED NOT NULL,
    nilai DECIMAL(20,2) NOT NULL DEFAULT 0,
    input_mode VARCHAR(16) NOT NULL DEFAULT 'MANUAL',
    input_source VARCHAR(64) NULL,
    updated_by VARCHAR(50) NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rbb_plan_value (plan_id, kode_monbis, bulan),
    KEY idx_rbb_plan_value_code (kode_monbis),
    CONSTRAINT fk_rbb_plan_value_plan FOREIGN KEY (plan_id) REFERENCES rbb_plan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rbb_plan_approval (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    plan_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(24) NOT NULL,
    from_status VARCHAR(32) NOT NULL,
    to_status VARCHAR(32) NOT NULL,
    actor_id VARCHAR(50) NULL,
    actor_name VARCHAR(150) NULL,
    note TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_rbb_plan_approval_plan (plan_id),
    CONSTRAINT fk_rbb_plan_approval_plan FOREIGN KEY (plan_id) REFERENCES rbb_plan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rbb_plan_aba (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    plan_id BIGINT UNSIGNED NOT NULL,
    tipe VARCHAR(16) NOT NULL DEFAULT 'PLACEMENT',
    no_urut INT NOT NULL DEFAULT 0,
    jenis_penempatan VARCHAR(80) NULL,
    nama_bank VARCHAR(150) NULL,
    no_rekening_aba VARCHAR(80) NULL,
    no_rekening_cbs VARCHAR(80) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_rbb_plan_aba_plan (plan_id, tipe),
    CONSTRAINT fk_rbb_plan_aba_plan FOREIGN KEY (plan_id) REFERENCES rbb_plan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rbb_plan_aba_value (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    aba_id BIGINT UNSIGNED NOT NULL,
    bulan TINYINT UNSIGNED NOT NULL,
    nilai DECIMAL(20,2) NOT NULL DEFAULT 0,
    bunga DECIMAL(20,2) NOT NULL DEFAULT 0,
    updated_by VARCHAR(50) NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rbb_plan_aba_value (aba_id, bulan),
    CONSTRAINT fk_rbb_plan_aba_value_line FOREIGN KEY (aba_id) REFERENCES rbb_plan_aba(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Matrix akses menu Monbis.
-- Runtime API juga menjalankan CREATE TABLE IF NOT EXISTS agar deployment
-- baru tidak perlu menjalankan migration manual terlebih dahulu.

CREATE TABLE IF NOT EXISTS rbac_menu (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  feature_key VARCHAR(120) NOT NULL,
  feature_label VARCHAR(180) NOT NULL,
  page_slug VARCHAR(120) NULL,
  module_name VARCHAR(100) NOT NULL DEFAULT 'Lainnya',
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_rbac_menu_feature (feature_key),
  KEY idx_rbac_menu_page (page_slug),
  KEY idx_rbac_menu_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rbac_jabatan_permission (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  unit_kerja VARCHAR(150) NOT NULL DEFAULT '',
  job_position VARCHAR(150) NOT NULL,
  group_jabatan VARCHAR(80) NOT NULL DEFAULT '',
  kode_kantor VARCHAR(5) NOT NULL DEFAULT '*',
  menu_id BIGINT UNSIGNED NOT NULL,
  can_view TINYINT(1) NOT NULL DEFAULT 0,
  can_create TINYINT(1) NOT NULL DEFAULT 0,
  can_update TINYINT(1) NOT NULL DEFAULT 0,
  can_delete TINYINT(1) NOT NULL DEFAULT 0,
  can_export TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  updated_by VARCHAR(50) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_rbac_jabatan_menu (unit_kerja, job_position, group_jabatan, kode_kantor, menu_id),
  KEY idx_rbac_jabatan_lookup (unit_kerja, job_position, group_jabatan, kode_kantor, is_active),
  KEY idx_rbac_jabatan_menu (menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rbac_user_permission (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  employee_id VARCHAR(50) NOT NULL,
  menu_id BIGINT UNSIGNED NOT NULL,
  can_view TINYINT(1) NOT NULL DEFAULT 0,
  can_create TINYINT(1) NOT NULL DEFAULT 0,
  can_update TINYINT(1) NOT NULL DEFAULT 0,
  can_delete TINYINT(1) NOT NULL DEFAULT 0,
  can_export TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  updated_by VARCHAR(50) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_rbac_user_menu (employee_id, menu_id),
  KEY idx_rbac_user_lookup (employee_id, is_active),
  KEY idx_rbac_user_menu (menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS mapping_ao_remedial (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  no_rekening VARCHAR(50) NOT NULL,
  kode_kantor CHAR(3) NOT NULL,
  ao_fe_id_peg VARCHAR(10) NULL,
  ao_fe_nama VARCHAR(255) NULL,
  ao_be_id_peg VARCHAR(10) NULL,
  ao_be_nama VARCHAR(255) NULL,
  ao_id_peg VARCHAR(10) NULL COMMENT 'Kompatibilitas mapping lama',
  ao_nama VARCHAR(255) NULL COMMENT 'Kompatibilitas mapping lama',
  spesialisasi VARCHAR(20) NULL COMMENT 'Kompatibilitas mapping lama',
  bucket_awal VARCHAR(10) NOT NULL,
  dpd_awal INT NOT NULL DEFAULT 0,
  closing_date_awal DATE NOT NULL,
  assigned_by VARCHAR(20) NOT NULL,
  assigned_by_name VARCHAR(255) NOT NULL,
  assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_mapping_ao_remedial_rekening (no_rekening),
  KEY idx_mapping_ao_remedial_kantor (kode_kantor),
  KEY idx_mapping_ao_remedial_ao (ao_id_peg)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS mapping_ao_remedial_history (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mapping_id BIGINT UNSIGNED NOT NULL,
  no_rekening VARCHAR(50) NOT NULL,
  kode_kantor CHAR(3) NOT NULL,
  ao_id_peg VARCHAR(10) NOT NULL,
  ao_nama VARCHAR(255) NOT NULL,
  action_type VARCHAR(20) NOT NULL,
  action_by VARCHAR(20) NOT NULL,
  action_by_name VARCHAR(255) NOT NULL,
  action_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_mapping_history_mapping (mapping_id),
  KEY idx_mapping_history_rekening (no_rekening),
  CONSTRAINT fk_mapping_history_mapping FOREIGN KEY (mapping_id)
    REFERENCES mapping_ao_remedial (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE mapping_ao_remedial CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE mapping_ao_remedial_history CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

ALTER TABLE mapping_ao_remedial
  ADD COLUMN IF NOT EXISTS ao_fe_id_peg VARCHAR(10) NULL AFTER kode_kantor,
  ADD COLUMN IF NOT EXISTS ao_fe_nama VARCHAR(255) NULL AFTER ao_fe_id_peg,
  ADD COLUMN IF NOT EXISTS ao_be_id_peg VARCHAR(10) NULL AFTER ao_fe_nama,
  ADD COLUMN IF NOT EXISTS ao_be_nama VARCHAR(255) NULL AFTER ao_be_id_peg,
  MODIFY COLUMN ao_id_peg VARCHAR(10) NULL,
  MODIFY COLUMN ao_nama VARCHAR(255) NULL,
  MODIFY COLUMN spesialisasi VARCHAR(20) NULL;

UPDATE mapping_ao_remedial
SET ao_fe_id_peg=CASE WHEN UPPER(COALESCE(spesialisasi,''))='FE' THEN ao_id_peg ELSE ao_fe_id_peg END,
    ao_fe_nama=CASE WHEN UPPER(COALESCE(spesialisasi,''))='FE' THEN ao_nama ELSE ao_fe_nama END,
    ao_be_id_peg=CASE WHEN UPPER(COALESCE(spesialisasi,''))='BE' THEN ao_id_peg ELSE ao_be_id_peg END,
    ao_be_nama=CASE WHEN UPPER(COALESCE(spesialisasi,''))='BE' THEN ao_nama ELSE ao_be_nama END
WHERE ao_id_peg IS NOT NULL;

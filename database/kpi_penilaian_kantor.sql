-- Migrasi KPI: simpan kantor asal AO pada setiap hasil generate.
-- Aman dijalankan berulang kali pada database yang sudah memiliki tabel KPI.
SET @has_kpi_kantor := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'kpi_penilaian'
    AND COLUMN_NAME = 'kode_kantor'
);
SET @sql_kpi_kantor := IF(
  @has_kpi_kantor = 0,
  'ALTER TABLE kpi_penilaian ADD COLUMN kode_kantor VARCHAR(10) NULL AFTER id_peg',
  'SELECT 1'
);
PREPARE stmt_kpi_kantor FROM @sql_kpi_kantor;
EXECUTE stmt_kpi_kantor;
DEALLOCATE PREPARE stmt_kpi_kantor;

SET @has_kpi_kantor_index := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'kpi_penilaian'
    AND INDEX_NAME = 'idx_kpi_penilaian_pegawai'
);
SET @sql_kpi_kantor_index := IF(
  @has_kpi_kantor_index = 0,
  'ALTER TABLE kpi_penilaian ADD KEY idx_kpi_penilaian_pegawai (id_peg,kode_kantor,tahun,bulan)',
  'SELECT 1'
);
PREPARE stmt_kpi_kantor_index FROM @sql_kpi_kantor_index;
EXECUTE stmt_kpi_kantor_index;
DEALLOCATE PREPARE stmt_kpi_kantor_index;

-- Migrasi parameter range skor agar setiap indikator memiliki range sendiri.
-- Jalankan sekali pada database yang sudah memakai kpi_bisnis.sql.

ALTER TABLE kpi_parameter_skor_jabatan
  ADD COLUMN indikator_id INT UNSIGNED NULL AFTER jabatan_id,
  ADD KEY idx_kpi_parameter_skor_jabatan (jabatan_id),
  DROP INDEX uq_kpi_parameter_skor_jabatan;

INSERT INTO kpi_parameter_skor_jabatan
  (jabatan_id, indikator_id, skor, min_indeks, max_indeks, predikat, aktif)
SELECT i.jabatan_id, i.id, p.skor, p.min_indeks, p.max_indeks, p.predikat, p.aktif
FROM kpi_indikator i
JOIN kpi_parameter_skor_jabatan p
  ON p.jabatan_id = i.jabatan_id
 AND p.indikator_id IS NULL;

DELETE FROM kpi_parameter_skor_jabatan WHERE indikator_id IS NULL;

ALTER TABLE kpi_parameter_skor_jabatan
  ADD UNIQUE KEY uq_kpi_parameter_skor_indikator (indikator_id, skor),
  ADD CONSTRAINT fk_kpi_parameter_skor_indikator
    FOREIGN KEY (indikator_id) REFERENCES kpi_indikator(id);

ALTER TABLE kpi_parameter_skor_jabatan
  MODIFY indikator_id INT UNSIGNED NOT NULL;

-- Tambahkan level skor 0 sebagai batas di bawah skor 1 bila belum ada.
INSERT INTO kpi_parameter_skor_jabatan
  (jabatan_id, indikator_id, skor, min_indeks, max_indeks, predikat, aktif)
SELECT i.jabatan_id, i.id, 0, 0, COALESCE(s.min_indeks, 0), 'Di bawah target minimum', 1
FROM kpi_indikator i
LEFT JOIN kpi_parameter_skor_jabatan s
  ON s.indikator_id=i.id AND s.skor=1
LEFT JOIN kpi_parameter_skor_jabatan z
  ON z.indikator_id=i.id AND z.skor=0
WHERE z.id IS NULL;

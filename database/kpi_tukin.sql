-- Parameter faktor tukin berdasarkan skor KPI final (skala 0-5).
CREATE TABLE IF NOT EXISTS kpi_parameter_tukin (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  min_skor DECIMAL(5,2) NOT NULL,
  max_skor DECIMAL(5,2) NULL,
  min_nilai DECIMAL(6,2) NOT NULL,
  max_nilai DECIMAL(6,2) NULL,
  faktor_persen DECIMAL(6,2) NOT NULL,
  label VARCHAR(80) NOT NULL,
  urutan TINYINT UNSIGNED NOT NULL DEFAULT 1,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_kpi_tukin_urutan (urutan),
  KEY idx_kpi_tukin_aktif (aktif,urutan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO kpi_parameter_tukin
  (min_skor,max_skor,min_nilai,max_nilai,faktor_persen,label,urutan,aktif)
VALUES
  (0.00,2.50,0.00,50.00,25.00,'< 2,50',1,1),
  (2.50,3.00,50.00,60.00,60.00,'2,50 – 2,99',2,1),
  (3.00,3.50,60.00,70.00,80.00,'3,00 – 3,49',3,1),
  (3.50,4.00,70.00,80.00,90.00,'3,50 – 3,99',4,1),
  (4.00,4.25,80.00,85.00,100.00,'4,00 – 4,24',5,1),
  (4.25,4.50,85.00,90.00,110.00,'4,25 – 4,49',6,1),
  (4.50,NULL,90.00,100.00,120.00,'4,50 – 5,00',7,1)
ON DUPLICATE KEY UPDATE
  min_skor=VALUES(min_skor),max_skor=VALUES(max_skor),min_nilai=VALUES(min_nilai),
  max_nilai=VALUES(max_nilai),faktor_persen=VALUES(faktor_persen),label=VALUES(label),aktif=VALUES(aktif);

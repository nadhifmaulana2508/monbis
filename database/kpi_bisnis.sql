-- Fondasi KPI lini bisnis.
-- Jalankan sekali pada database aplikasi. Seed ini sengaja hanya mengaktifkan
-- AO Kredit dan AO Remedial; perhitungan indikator dapat dikalibrasi kemudian.

CREATE TABLE IF NOT EXISTS kpi_jabatan (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode VARCHAR(40) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  deskripsi VARCHAR(255) NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id), UNIQUE KEY uq_kpi_jabatan_kode (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kpi_indikator (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  jabatan_id INT UNSIGNED NOT NULL,
  kode VARCHAR(60) NOT NULL,
  kelompok VARCHAR(60) NOT NULL,
  nama VARCHAR(150) NOT NULL,
  bobot DECIMAL(8,5) NOT NULL DEFAULT 0,
  arah ENUM('HIGHER','LOWER') NOT NULL DEFAULT 'HIGHER',
  unit ENUM('RUPIAH','NOA','PERSEN','JUMLAH') NOT NULL DEFAULT 'RUPIAH',
  frekuensi ENUM('BULANAN','TRIWULANAN','TAHUNAN') NOT NULL DEFAULT 'BULANAN',
  formula_key VARCHAR(80) NULL,
  definisi TEXT NULL,
  sumber_data VARCHAR(150) NULL,
  validator VARCHAR(100) NULL,
  status ENUM('DRAFT','PILOT','AKTIF','NONAKTIF') NOT NULL DEFAULT 'PILOT',
  urutan SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_kpi_indikator (jabatan_id,kode),
  KEY idx_kpi_indikator_status (jabatan_id,status),
  CONSTRAINT fk_kpi_indikator_jabatan FOREIGN KEY (jabatan_id) REFERENCES kpi_jabatan(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kpi_parameter_skor (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  skor TINYINT UNSIGNED NOT NULL,
  min_indeks DECIMAL(8,5) NOT NULL,
  max_indeks DECIMAL(8,5) NOT NULL,
  predikat VARCHAR(60) NOT NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id), UNIQUE KEY uq_kpi_parameter_skor (skor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kpi_parameter_skor_jabatan (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  jabatan_id INT UNSIGNED NOT NULL,
  skor TINYINT UNSIGNED NOT NULL,
  min_indeks DECIMAL(8,5) NOT NULL,
  max_indeks DECIMAL(8,5) NOT NULL,
  predikat VARCHAR(60) NOT NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id), UNIQUE KEY uq_kpi_parameter_skor_jabatan (jabatan_id,skor),
  CONSTRAINT fk_kpi_parameter_skor_jabatan FOREIGN KEY (jabatan_id) REFERENCES kpi_jabatan(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kpi_risk_gate (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode VARCHAR(30) NOT NULL,
  nama VARCHAR(60) NOT NULL,
  faktor DECIMAL(8,5) NOT NULL,
  perlakuan VARCHAR(150) NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id), UNIQUE KEY uq_kpi_risk_gate_kode (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kpi_target_bulanan (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  jabatan_id INT UNSIGNED NOT NULL,
  indikator_id INT UNSIGNED NOT NULL,
  tahun SMALLINT UNSIGNED NOT NULL,
  bulan TINYINT UNSIGNED NOT NULL,
  id_peg VARCHAR(30) NULL,
  kode_kantor VARCHAR(10) NULL,
  target DECIMAL(20,4) NOT NULL DEFAULT 0,
  catatan VARCHAR(255) NULL,
  updated_by VARCHAR(30) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_kpi_target_scope (indikator_id,tahun,bulan,id_peg,kode_kantor),
  KEY idx_kpi_target_period (tahun,bulan,jabatan_id),
  CONSTRAINT fk_kpi_target_jabatan FOREIGN KEY (jabatan_id) REFERENCES kpi_jabatan(id),
  CONSTRAINT fk_kpi_target_indikator FOREIGN KEY (indikator_id) REFERENCES kpi_indikator(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kpi_penilaian (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  jabatan_id INT UNSIGNED NOT NULL,
  id_peg VARCHAR(30) NOT NULL,
  kode_kantor VARCHAR(10) NULL,
  kode_ao VARCHAR(30) NULL,
  nama_ao VARCHAR(150) NULL,
  tahun SMALLINT UNSIGNED NOT NULL,
  bulan TINYINT UNSIGNED NOT NULL,
  closing_date DATE NOT NULL,
  nilai_dasar DECIMAL(10,4) NOT NULL DEFAULT 0,
  risk_gate VARCHAR(30) NOT NULL DEFAULT 'NORMAL',
  faktor_risiko DECIMAL(8,5) NOT NULL DEFAULT 1,
  nilai_akhir DECIMAL(10,4) NOT NULL DEFAULT 0,
  predikat VARCHAR(60) NULL,
  status ENUM('DRAFT','REVIEW','DISETUJUI','DIKUNCI') NOT NULL DEFAULT 'DRAFT',
  generated_at DATETIME NULL,
  approved_by VARCHAR(30) NULL,
  approved_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_kpi_penilaian (jabatan_id,id_peg,tahun,bulan),
  KEY idx_kpi_penilaian_pegawai (id_peg,kode_kantor,tahun,bulan),
  KEY idx_kpi_penilaian_period (tahun,bulan,jabatan_id),
  CONSTRAINT fk_kpi_penilaian_jabatan FOREIGN KEY (jabatan_id) REFERENCES kpi_jabatan(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kpi_penilaian_detail (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  penilaian_id BIGINT UNSIGNED NOT NULL,
  indikator_id INT UNSIGNED NOT NULL,
  target DECIMAL(20,4) NOT NULL DEFAULT 0,
  realisasi DECIMAL(20,4) NOT NULL DEFAULT 0,
  indeks DECIMAL(8,5) NOT NULL DEFAULT 0,
  skor TINYINT UNSIGNED NOT NULL DEFAULT 1,
  nilai_tertimbang DECIMAL(10,5) NOT NULL DEFAULT 0,
  nilai_100 DECIMAL(10,4) NOT NULL DEFAULT 0,
  os_mob_menunggak DECIMAL(20,4) NOT NULL DEFAULT 0,
  os_mob_total DECIMAL(20,4) NOT NULL DEFAULT 0,
  os_dpd0 DECIMAL(20,4) NOT NULL DEFAULT 0,
  os_kelolaan DECIMAL(20,4) NOT NULL DEFAULT 0,
  os_run_off DECIMAL(20,4) NOT NULL DEFAULT 0,
  os_dpd0_m1 DECIMAL(20,4) NOT NULL DEFAULT 0,
  sumber_snapshot VARCHAR(150) NULL,
  catatan VARCHAR(255) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_kpi_penilaian_detail (penilaian_id,indikator_id),
  CONSTRAINT fk_kpi_detail_penilaian FOREIGN KEY (penilaian_id) REFERENCES kpi_penilaian(id) ON DELETE CASCADE,
  CONSTRAINT fk_kpi_detail_indikator FOREIGN KEY (indikator_id) REFERENCES kpi_indikator(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO kpi_jabatan (kode,nama,deskripsi,aktif) VALUES
 ('AO_KREDIT','AO Kredit','Account Officer Kredit',1),
 ('AO_DANA','AO Dana','Account Officer Dana',1),
 ('AO_REMEDIAL','AO Remedial','Account Officer Remedial',1)
ON DUPLICATE KEY UPDATE nama=VALUES(nama),deskripsi=VALUES(deskripsi),aktif=VALUES(aktif);

INSERT INTO kpi_parameter_skor_jabatan (jabatan_id,skor,min_indeks,max_indeks,predikat,aktif)
SELECT j.id,v.skor,v.min_indeks,v.max_indeks,v.predikat,1
FROM kpi_jabatan j JOIN (
 SELECT 'AO_KREDIT' jabatan,1 skor,0 min_indeks,0.60 max_indeks,'Di bawah target'
 UNION ALL SELECT 'AO_KREDIT',2,0.60,0.80,'Perlu perbaikan'
 UNION ALL SELECT 'AO_KREDIT',3,0.80,1.00,'Memenuhi target'
 UNION ALL SELECT 'AO_KREDIT',4,1.00,1.25,'Melampaui target'
 UNION ALL SELECT 'AO_KREDIT',5,1.25,999.00,'Istimewa'
 UNION ALL SELECT 'AO_DANA',1,0,0.50,'Di bawah target'
 UNION ALL SELECT 'AO_DANA',2,0.50,0.70,'Perlu perbaikan'
 UNION ALL SELECT 'AO_DANA',3,0.70,0.90,'Memenuhi target'
 UNION ALL SELECT 'AO_DANA',4,0.90,1.00,'Melampaui target'
 UNION ALL SELECT 'AO_DANA',5,1.00,999.00,'Istimewa'
 UNION ALL SELECT 'AO_REMEDIAL',1,0,0.05,'Di bawah target'
 UNION ALL SELECT 'AO_REMEDIAL',2,0.05,0.10,'Perlu perbaikan'
 UNION ALL SELECT 'AO_REMEDIAL',3,0.10,0.15,'Memenuhi target'
 UNION ALL SELECT 'AO_REMEDIAL',4,0.15,0.20,'Melampaui target'
 UNION ALL SELECT 'AO_REMEDIAL',5,0.20,999.00,'Istimewa'
) v ON v.jabatan=j.kode
ON DUPLICATE KEY UPDATE min_indeks=VALUES(min_indeks),max_indeks=VALUES(max_indeks),predikat=VALUES(predikat),aktif=1;

INSERT INTO kpi_parameter_skor (skor,min_indeks,max_indeks,predikat,aktif) VALUES
 (1,0,0.80000,'Jauh di bawah',1),
 (2,0.80000,0.95000,'Perlu perbaikan',1),
 (3,0.95000,1.05000,'Memenuhi target',1),
 (4,1.05000,1.20000,'Melampaui target',1),
 (5,1.20000,1.50000,'Istimewa',1)
ON DUPLICATE KEY UPDATE min_indeks=VALUES(min_indeks),max_indeks=VALUES(max_indeks),predikat=VALUES(predikat),aktif=VALUES(aktif);

INSERT INTO kpi_risk_gate (kode,nama,faktor,perlakuan,aktif) VALUES
 ('NORMAL','Normal',1.00000,'Nilai dibayar penuh',1),
 ('WARNING','Warning',0.85000,'Nilai akhir dikurangi 15%',1),
 ('BREACH','Breach',0.60000,'Nilai akhir dikurangi 40%',1),
 ('MATERIAL','Material',0.00000,'Nilai atau insentif dapat digugurkan',1)
ON DUPLICATE KEY UPDATE nama=VALUES(nama),faktor=VALUES(faktor),perlakuan=VALUES(perlakuan),aktif=VALUES(aktif);

INSERT INTO kpi_indikator
 (jabatan_id,kode,kelompok,nama,bobot,arah,unit,frekuensi,formula_key,definisi,sumber_data,validator,status,urutan)
SELECT j.id,v.kode,v.kelompok,v.nama,v.bobot,v.arah,v.unit,'BULANAN',v.formula_key,v.definisi,v.sumber_data,v.validator,'PILOT',v.urutan
FROM kpi_jabatan j
JOIN (SELECT 'AO_KREDIT' jabatan,'PENCAIRAN_NETO' kode,'Pertumbuhan' kelompok,'Nominal Pencairan Kredit Neto' nama,0.40 bobot,'HIGHER' arah,'RUPIAH' unit,'REALISASI_KREDIT' formula_key,'Realisasi pencairan neto dibanding target AO' definisi,'Monbis / kredit' sumber_data,'Atasan / Bisnis' validator,1 urutan
 UNION ALL SELECT 'AO_KREDIT','NOA_BARU','Pertumbuhan','NOA Debitur Baru',0.25,'HIGHER','NOA','NOA_REALISASI','Debitur baru aktif dibanding target','Monbis / nominatif','Atasan / Bisnis',2
 UNION ALL SELECT 'AO_KREDIT','PIPELINE','Aktivitas','Pipeline',0.05,'HIGHER','NOA','PIPELINE','NOA pipeline valid dibanding target','DSAR / pipeline','Atasan',3
 UNION ALL SELECT 'AO_KREDIT','REPAYMENT_RATE','Kualitas','Repayment Rate / DPD 0',0.15,'HIGHER','PERSEN','REPAYMENT_RATE','Kredit lancar tanpa tunggakan dibanding total kelolaan','Monbis / nominatif','Risiko',4
 UNION ALL SELECT 'AO_KREDIT','MOB_6','Kualitas','MOB <=6 Menunggak',0.10,'LOWER','PERSEN','MOB_6','OS MOB <=6 menunggak dibanding total OS MOB <=6 kelolaan','Monbis / nominatif','Risiko',5
 UNION ALL SELECT 'AO_KREDIT','EARLY_RUN_OFF','Kualitas','Early Run Off',0.05,'LOWER','PERSEN','EARLY_RUN_OFF','Pelunasan dipercepat sesuai definisi kebijakan','Monbis / kredit','Bisnis',6
 UNION ALL SELECT 'AO_KREDIT','NPL_VINTAGE','Kualitas','NPL Vintage',0.00,'LOWER','PERSEN','NPL_VINTAGE','NPL berdasarkan cohort pencairan AO','Monbis / nominatif','Risiko',7
 UNION ALL SELECT 'AO_KREDIT','KELENGKAPAN_DATA','Kepatuhan','Kelengkapan Data & Dokumen',0.00,'HIGHER','PERSEN','KELENGKAPAN_DATA','Dokumen lengkap dan data benar dibanding sampel/total','Audit / Admin Kredit','Kepatuhan',8
 UNION ALL SELECT 'AO_DANA','TABUNGAN_AO','Pertumbuhan','Tabungan AO',0.40,'HIGHER','RUPIAH','TABUNGAN_AO','Pencapaian nominal tabungan dibanding target AO per bulan','Monbis / tabungan','Atasan / Dana',1
 UNION ALL SELECT 'AO_DANA','DEPOSITO_AO','Pertumbuhan','Deposito',0.20,'HIGHER','RUPIAH','DEPOSITO_AO','Pencapaian nominal deposito dibanding target AO per bulan','Monbis / deposito','Atasan / Dana',2
 UNION ALL SELECT 'AO_DANA','NOA_BARU_DANA','Pertumbuhan','Noa Baru',0.30,'HIGHER','NOA','NOA_DANA_REALISASI','Jumlah NOA status baru (new CIF)','Monbis / tabungan dan deposito','Atasan / Dana',3
 UNION ALL SELECT 'AO_DANA','PIPELINE_AO_DANA','Aktivitas','Pipeline AO Dana',0.10,'HIGHER','NOA','PIPELINE_DANA','Pipeline calon nasabah yang sudah hot prospek','DSAR (Daily Sales Activity Report)','Atasan / Dana',4
 UNION ALL SELECT 'AO_REMEDIAL','AMOUNT_COLLECTION','Collection','Amcol NPL',0.25,'HIGHER','PERSEN','AMOUNT_COLLECTION','Jumlah amount collection pada bucket NPL dibanding target collection','Monbis / transaksi','Atasan / Remedial',2
 UNION ALL SELECT 'AO_REMEDIAL','BACKFLOW','Perbaikan','Back Flow NPL',0.35,'HIGHER','PERSEN','BACKFLOW','OS bucket NPL yang kembali ke Performing Loan dibanding OS bucket NPL','Monbis / nominatif','Risiko',1
 UNION ALL SELECT 'AO_REMEDIAL','PENURUNAN_NPL','Perbaikan','OS NPL (Perbaikan)',0.10,'HIGHER','PERSEN','PENURUNAN_NPL','Perbaikan OS NPL pada bulan berjalan dibanding OS NPL bulan lalu','Monbis / nominatif','Risiko',4
 UNION ALL SELECT 'AO_REMEDIAL','PTP_DIPENUHI','Aktivitas','Amcol Pelunasan NPL',0.30,'HIGHER','PERSEN','PTP_DIPENUHI','Jumlah amount collection dari pelunasan NPL dibanding target collection','Monbis / collection','Atasan',3
 UNION ALL SELECT 'AO_REMEDIAL','DOKUMENTASI_PENAGIHAN','Kepatuhan','Dokumentasi Penagihan',0.05,'HIGHER','PERSEN','DOKUMENTASI_PENAGIHAN','Aktivitas penagihan dengan bukti lengkap','Monbis / audit','Kepatuhan',6) v ON v.jabatan='AO_KREDIT' OR v.jabatan='AO_REMEDIAL'
WHERE j.kode=v.jabatan
ON DUPLICATE KEY UPDATE nama=VALUES(nama),kelompok=VALUES(kelompok),bobot=VALUES(bobot),arah=VALUES(arah),unit=VALUES(unit),formula_key=VALUES(formula_key),definisi=VALUES(definisi),sumber_data=VALUES(sumber_data),validator=VALUES(validator),status='PILOT',urutan=VALUES(urutan);

-- Skema AO Remedial saat ini hanya memakai empat indikator pada tabel KPI.
UPDATE kpi_indikator i
JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.status='NONAKTIF',i.bobot=0
WHERE j.kode='AO_REMEDIAL'
  AND i.kode IN ('PENYELESAIAN_KREDIT','DOKUMENTASI_PENAGIHAN');

-- Target default repayment rate 65%; target yang sudah diubah user tidak ditimpa.
UPDATE kpi_target_bulanan t
JOIN kpi_indikator i ON i.id=t.indikator_id
JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET t.target=0.65
WHERE j.kode='AO_KREDIT' AND i.formula_key='REPAYMENT_RATE'
  AND t.tahun=0 AND t.bulan=0 AND t.id_peg IS NULL AND t.kode_kantor IS NULL
  AND t.target=1.0;

-- Indikator aktif sesuai skema KPI bisnis.
UPDATE kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.status=CASE WHEN (j.kode='AO_KREDIT' AND i.kode IN ('EARLY_RUN_OFF','PENCAIRAN_NETO','NOA_BARU','REPAYMENT_RATE','MOB_6')) OR (j.kode='AO_DANA' AND i.kode IN ('TABUNGAN_AO','DEPOSITO_AO','NOA_BARU_DANA','PIPELINE_AO_DANA')) OR (j.kode='AO_REMEDIAL' AND i.kode IN ('BACKFLOW','AMOUNT_COLLECTION','PTP_DIPENUHI','PENURUNAN_NPL','PENYELESAIAN_KREDIT')) THEN 'AKTIF' ELSE 'NONAKTIF' END,
    i.bobot=CASE WHEN j.kode='AO_KREDIT' THEN CASE i.kode WHEN 'EARLY_RUN_OFF' THEN 0.10 WHEN 'PENCAIRAN_NETO' THEN 0.45 WHEN 'NOA_BARU' THEN 0.10 WHEN 'REPAYMENT_RATE' THEN 0.25 WHEN 'MOB_6' THEN 0.10 ELSE 0 END WHEN j.kode='AO_DANA' THEN CASE i.kode WHEN 'TABUNGAN_AO' THEN 0.40 WHEN 'DEPOSITO_AO' THEN 0.20 WHEN 'NOA_BARU_DANA' THEN 0.30 WHEN 'PIPELINE_AO_DANA' THEN 0.10 ELSE 0 END WHEN j.kode='AO_REMEDIAL' AND i.kode='PENYELESAIAN_KREDIT' THEN 0.80 ELSE i.bobot END,
    i.nama=CASE WHEN j.kode='AO_DANA' AND i.kode='PIPELINE_AO_DANA' THEN 'Maintaince Nasabah 20 Besar' WHEN j.kode='AO_REMEDIAL' AND i.kode='PENYELESAIAN_KREDIT' THEN 'Recovery' ELSE i.nama END,
    i.unit=CASE WHEN j.kode='AO_DANA' AND i.kode='PIPELINE_AO_DANA' THEN 'PERSEN' ELSE i.unit END
WHERE j.kode IN ('AO_KREDIT','AO_DANA','AO_REMEDIAL');

INSERT INTO kpi_target_bulanan (jabatan_id,indikator_id,tahun,bulan,id_peg,kode_kantor,target,catatan)
SELECT j.id,i.id,0,0,NULL,NULL,0.65,'Target default Repayment Rate 65%'
FROM kpi_jabatan j JOIN kpi_indikator i ON i.jabatan_id=j.id
WHERE j.kode='AO_KREDIT' AND i.formula_key='REPAYMENT_RATE'
  AND NOT EXISTS (SELECT 1 FROM kpi_target_bulanan t
                  WHERE t.indikator_id=i.id AND t.jabatan_id=j.id AND t.tahun=0 AND t.bulan=0
                    AND t.id_peg IS NULL AND t.kode_kantor IS NULL);

UPDATE kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.nama=CASE i.kode
  WHEN 'TABUNGAN_AO' THEN 'Tabungan AO (net growth 35.000.000)'
  WHEN 'DEPOSITO_AO' THEN 'Deposito AO (net growth 15.000.000)'
  WHEN 'NOA_BARU_DANA' THEN 'Noa Baru'
  WHEN 'PIPELINE_AO_DANA' THEN 'Maintaince Nasabah 20 Besar'
  ELSE i.nama END
WHERE j.kode='AO_DANA';

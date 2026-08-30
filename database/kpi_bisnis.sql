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
 ('AO_REMEDIAL','AO Remedial','Account Officer Remedial',1)
ON DUPLICATE KEY UPDATE nama=VALUES(nama),deskripsi=VALUES(deskripsi),aktif=VALUES(aktif);

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
 UNION ALL SELECT 'AO_KREDIT','PIPELINE','Aktivitas','Pipeline',0.05,'HIGHER','RUPIAH','PIPELINE','Nilai pipeline valid dibanding target','DSAR / pipeline','Atasan',3
 UNION ALL SELECT 'AO_KREDIT','REPAYMENT_RATE','Kualitas','Repayment Rate / DPD 0',0.15,'HIGHER','PERSEN','REPAYMENT_RATE','Kredit lancar tanpa tunggakan dibanding total kelolaan','Monbis / nominatif','Risiko',4
 UNION ALL SELECT 'AO_KREDIT','MOB_6','Kualitas','MOB <=6 Menunggak',0.10,'LOWER','PERSEN','MOB_6','OS MOB <=6 menunggak dibanding total OS MOB <=6 kelolaan','Monbis / nominatif','Risiko',5
 UNION ALL SELECT 'AO_KREDIT','EARLY_RUN_OFF','Kualitas','Early Run Off',0.05,'LOWER','PERSEN','EARLY_RUN_OFF','Pelunasan dipercepat sesuai definisi kebijakan','Monbis / kredit','Bisnis',6
 UNION ALL SELECT 'AO_KREDIT','NPL_VINTAGE','Kualitas','NPL Vintage',0.00,'LOWER','PERSEN','NPL_VINTAGE','NPL berdasarkan cohort pencairan AO','Monbis / nominatif','Risiko',7
 UNION ALL SELECT 'AO_KREDIT','KELENGKAPAN_DATA','Kepatuhan','Kelengkapan Data & Dokumen',0.00,'HIGHER','PERSEN','KELENGKAPAN_DATA','Dokumen lengkap dan data benar dibanding sampel/total','Audit / Admin Kredit','Kepatuhan',8
 UNION ALL SELECT 'AO_REMEDIAL','AMOUNT_COLLECTION','Collection','Amount Collection',0.30,'HIGHER','RUPIAH','AMOUNT_COLLECTION','Dana tertagih dibanding target collection','Monbis / transaksi','Atasan / Remedial',1
 UNION ALL SELECT 'AO_REMEDIAL','BACKFLOW','Perbaikan','Backflow ke Performing',0.25,'HIGHER','PERSEN','BACKFLOW','OS NPL kembali performing dibanding bucket kelolaan','Monbis / nominatif','Risiko',2
 UNION ALL SELECT 'AO_REMEDIAL','PENURUNAN_NPL','Perbaikan','Penurunan OS NPL',0.20,'HIGHER','PERSEN','PENURUNAN_NPL','Perbaikan OS NPL dibanding posisi awal','Monbis / nominatif','Risiko',3
 UNION ALL SELECT 'AO_REMEDIAL','PTP_DIPENUHI','Aktivitas','Janji Bayar Dipenuhi',0.10,'HIGHER','PERSEN','PTP_DIPENUHI','Promise to pay terpenuhi dibanding total janji','Monbis / collection','Atasan',4
 UNION ALL SELECT 'AO_REMEDIAL','PENYELESAIAN_KREDIT','Penyelesaian','Penyelesaian Kredit',0.10,'HIGHER','PERSEN','PENYELESAIAN_KREDIT','Realisasi restrukturisasi/pelunasan/lelang/litigasi','Monbis / remedial','Atasan / Legal',5
 UNION ALL SELECT 'AO_REMEDIAL','DOKUMENTASI_PENAGIHAN','Kepatuhan','Dokumentasi Penagihan',0.05,'HIGHER','PERSEN','DOKUMENTASI_PENAGIHAN','Aktivitas penagihan dengan bukti lengkap','Monbis / audit','Kepatuhan',6) v ON v.jabatan='AO_KREDIT' OR v.jabatan='AO_REMEDIAL'
WHERE j.kode=v.jabatan
ON DUPLICATE KEY UPDATE nama=VALUES(nama),kelompok=VALUES(kelompok),bobot=VALUES(bobot),arah=VALUES(arah),unit=VALUES(unit),formula_key=VALUES(formula_key),definisi=VALUES(definisi),sumber_data=VALUES(sumber_data),validator=VALUES(validator),status='PILOT',urutan=VALUES(urutan);

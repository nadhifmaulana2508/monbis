-- Konfigurasi KPI AO Kredit berbasis closing M-1 dan actual closing.
-- Jalankan setelah kpi_bisnis.sql dan kpi_score_per_indikator.sql.

START TRANSACTION;

ALTER TABLE kpi_parameter_skor_jabatan
  MODIFY min_indeks DECIMAL(20,5) NOT NULL,
  MODIFY max_indeks DECIMAL(20,5) NOT NULL;

ALTER TABLE kpi_penilaian_detail
  MODIFY indeks DECIMAL(20,5) NOT NULL DEFAULT 0;

UPDATE kpi_indikator i
JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.kode='GROWTH_NOA',
    i.kelompok='Pertumbuhan',
    i.nama='Growth Noa',
    i.bobot=0.10,
    i.arah='HIGHER',
    i.unit='JUMLAH',
    i.formula_key='GROWTH_NOA',
    i.definisi='Selisih jumlah rekening kredit aktif pada actual closing dibanding closing bulan lalu',
    i.sumber_data='Monbis / nominatif',
    i.validator='Atasan / Bisnis',
    i.input_pa='Selisih jumlah rekening actual dengan closing bulan lalu',
    i.status='AKTIF',
    i.urutan=3
WHERE j.kode='AO_KREDIT' AND i.kode='NOA_BARU';

INSERT INTO kpi_indikator
  (jabatan_id,kode,kelompok,nama,bobot,arah,unit,frekuensi,formula_key,definisi,sumber_data,validator,input_pa,status,urutan)
SELECT j.id,'GROWTH_KREDIT','Pertumbuhan','Growth Kredit',0.20,'HIGHER','RUPIAH','BULANAN','GROWTH_KREDIT',
       'Selisih baki debet kelolaan actual closing dibanding closing bulan lalu',
       'Monbis / nominatif','Atasan / Bisnis','Selisih baki debet actual dengan closing bulan lalu','AKTIF',2
FROM kpi_jabatan j
WHERE j.kode='AO_KREDIT'
  AND NOT EXISTS (
    SELECT 1 FROM kpi_indikator x
    WHERE x.jabatan_id=j.id AND x.kode='GROWTH_KREDIT'
  );

UPDATE kpi_indikator i
JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.kelompok='Pertumbuhan',
    i.nama='Growth Kredit',
    i.bobot=0.20,
    i.arah='HIGHER',
    i.unit='RUPIAH',
    i.formula_key='GROWTH_KREDIT',
    i.definisi='Selisih baki debet kelolaan actual closing dibanding closing bulan lalu',
    i.sumber_data='Monbis / nominatif',
    i.validator='Atasan / Bisnis',
    i.input_pa='Selisih baki debet actual dengan closing bulan lalu',
    i.status='AKTIF',
    i.urutan=2
WHERE j.kode='AO_KREDIT' AND i.kode='GROWTH_KREDIT';

INSERT INTO kpi_indikator
  (jabatan_id,kode,kelompok,nama,bobot,arah,unit,frekuensi,formula_key,definisi,sumber_data,validator,input_pa,status,urutan)
SELECT j.id,'FLOW_DPK','Kualitas','L 1-30 - Flow DPK (31++)',0.15,'LOWER','PERSEN','BULANAN','FLOW_DPK',
       'Baki debet rekening pada bucket 0-30 di closing bulan lalu yang mengalir ke hari menunggak 31+ pada actual closing',
       'Monbis / nominatif','Risiko','Baki debet flow 31+ dibagi baki debet bucket 0-30 closing bulan lalu','AKTIF',5
FROM kpi_jabatan j
WHERE j.kode='AO_KREDIT'
  AND NOT EXISTS (
    SELECT 1 FROM kpi_indikator x
    WHERE x.jabatan_id=j.id AND x.kode='FLOW_DPK'
  );

UPDATE kpi_indikator i
JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.kelompok='Kualitas',
    i.nama='L 1-30 - Flow DPK (31++)',
    i.bobot=0.15,
    i.arah='LOWER',
    i.unit='PERSEN',
    i.formula_key='FLOW_DPK',
    i.definisi='Baki debet rekening pada bucket 0-30 di closing bulan lalu yang mengalir ke hari menunggak 31+ pada actual closing',
    i.sumber_data='Monbis / nominatif',
    i.validator='Risiko',
    i.input_pa='Baki debet flow 31+ dibagi baki debet bucket 0-30 closing bulan lalu',
    i.status='AKTIF',
    i.urutan=5
WHERE j.kode='AO_KREDIT' AND i.kode='FLOW_DPK';

UPDATE kpi_indikator i
JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.nama=CASE i.kode
      WHEN 'PENCAIRAN_NETO' THEN 'Pencairan Kredit Netto'
      WHEN 'GROWTH_KREDIT' THEN 'Growth Kredit'
      WHEN 'GROWTH_NOA' THEN 'Growth NOA'
      WHEN 'REPAYMENT_RATE' THEN 'Repayment Rate'
      WHEN 'FLOW_DPK' THEN 'Flow DPK 1-30 ke 31+'
      ELSE i.nama
    END,
    i.definisi=CASE i.kode
      WHEN 'PENCAIRAN_NETO' THEN 'Mengukur pencapaian realisasi pencairan kredit netto selama periode penilaian dibandingkan dengan target bulanan sebesar Rp450.000.000.'
      WHEN 'GROWTH_KREDIT' THEN 'Mengukur pertumbuhan outstanding (OS) kredit kelolaan AO selama periode penilaian, berdasarkan selisih OS akhir periode dengan OS awal periode.'
      WHEN 'GROWTH_NOA' THEN 'Mengukur pertumbuhan jumlah rekening atau nasabah kredit baru (NOA/CIF) selama periode penilaian berdasarkan selisih jumlah pada akhir dan awal periode.'
      WHEN 'REPAYMENT_RATE' THEN 'Mengukur persentase outstanding kredit lancar (DPD 0) pada akhir periode terhadap total outstanding kredit kelolaan AO pada akhir periode.'
      WHEN 'FLOW_DPK' THEN 'Mengukur persentase outstanding kredit dengan DPD 1-30 pada awal periode yang mengalami penurunan kualitas menjadi DPD 31-60 pada periode berjalan.'
      ELSE i.definisi
    END,
    i.bobot=CASE i.kode
      WHEN 'PENCAIRAN_NETO' THEN 0.30
      WHEN 'GROWTH_NOA' THEN 0.10
      WHEN 'REPAYMENT_RATE' THEN 0.25
      ELSE i.bobot
    END,
    i.urutan=CASE i.kode
      WHEN 'PENCAIRAN_NETO' THEN 1
      WHEN 'GROWTH_NOA' THEN 3
      WHEN 'REPAYMENT_RATE' THEN 4
      ELSE i.urutan
    END,
    i.status=CASE
      WHEN i.kode IN ('PENCAIRAN_NETO','GROWTH_KREDIT','GROWTH_NOA','REPAYMENT_RATE','FLOW_DPK') THEN 'AKTIF'
      ELSE 'NONAKTIF'
    END
WHERE j.kode='AO_KREDIT';

UPDATE kpi_target_bulanan t
JOIN kpi_indikator i ON i.id=t.indikator_id
JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET t.target=450000000,
    t.catatan='Target default Nominal Pencairan Kredit Netto'
WHERE j.kode='AO_KREDIT' AND i.kode='PENCAIRAN_NETO'
  AND t.tahun=0 AND t.bulan=0 AND t.id_peg IS NULL AND t.kode_kantor IS NULL;

UPDATE kpi_target_bulanan t
JOIN kpi_indikator i ON i.id=t.indikator_id
JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET t.target=0,
    t.catatan='Indikator dihitung dari selisih atau rasio snapshot closing'
WHERE j.kode='AO_KREDIT' AND i.kode IN ('GROWTH_KREDIT','GROWTH_NOA','FLOW_DPK')
  AND t.tahun=0 AND t.bulan=0 AND t.id_peg IS NULL AND t.kode_kantor IS NULL;

INSERT INTO kpi_target_bulanan
  (jabatan_id,indikator_id,tahun,bulan,id_peg,kode_kantor,target,catatan)
SELECT j.id,i.id,0,0,NULL,NULL,
       CASE WHEN i.kode='PENCAIRAN_NETO' THEN 450000000 ELSE 0 END,
       CASE WHEN i.kode='PENCAIRAN_NETO' THEN 'Target default Nominal Pencairan Kredit Netto' ELSE 'Indikator berbasis actual closing' END
FROM kpi_jabatan j
JOIN kpi_indikator i ON i.jabatan_id=j.id
WHERE j.kode='AO_KREDIT'
  AND i.kode IN ('PENCAIRAN_NETO','GROWTH_KREDIT','GROWTH_NOA','FLOW_DPK')
  AND NOT EXISTS (
    SELECT 1 FROM kpi_target_bulanan t
    WHERE t.jabatan_id=j.id AND t.indikator_id=i.id
      AND t.tahun=0 AND t.bulan=0 AND t.id_peg IS NULL AND t.kode_kantor IS NULL
  );

INSERT INTO kpi_parameter_skor_jabatan
  (jabatan_id,indikator_id,skor,min_indeks,max_indeks,predikat,aktif)
SELECT j.id,i.id,v.skor,v.min_indeks,v.max_indeks,v.predikat,1
FROM kpi_jabatan j
JOIN kpi_indikator i ON i.jabatan_id=j.id
JOIN (
  SELECT 'PENCAIRAN_NETO' kode,0 skor,0.00000 min_indeks,0.50000 max_indeks,'< 50%' predikat
  UNION ALL SELECT 'PENCAIRAN_NETO',1,0.50000,0.60000,'>= 50% - < 60%'
  UNION ALL SELECT 'PENCAIRAN_NETO',2,0.60000,0.80000,'>= 60% - < 80%'
  UNION ALL SELECT 'PENCAIRAN_NETO',3,0.80000,1.00000,'>= 80% - < 100%'
  UNION ALL SELECT 'PENCAIRAN_NETO',4,1.00000,1.25000,'>= 100% - < 125%'
  UNION ALL SELECT 'PENCAIRAN_NETO',5,1.25000,999.00000,'>= 125%'

  UNION ALL SELECT 'GROWTH_KREDIT',0,-999999999999.00000,0.00000,'< 0'
  UNION ALL SELECT 'GROWTH_KREDIT',1,0.00000,50000000.00000,'0 - 50 Jt'
  UNION ALL SELECT 'GROWTH_KREDIT',2,50000000.00000,100000000.00000,'> 50 Jt - 100 Jt'
  UNION ALL SELECT 'GROWTH_KREDIT',3,100000000.00000,150000000.00000,'> 100 Jt - 150 Jt'
  UNION ALL SELECT 'GROWTH_KREDIT',4,150000000.00000,200000000.00000,'> 150 Jt - 200 Jt'
  UNION ALL SELECT 'GROWTH_KREDIT',5,200000000.00000,999999999999.00000,'> 200 Jt'

  UNION ALL SELECT 'GROWTH_NOA',0,-999.00000,0.00000,'<= 0'
  UNION ALL SELECT 'GROWTH_NOA',1,1.00000,2.00000,'> 0 - <= 1'
  UNION ALL SELECT 'GROWTH_NOA',2,2.00000,3.00000,'> 1 - <= 2'
  UNION ALL SELECT 'GROWTH_NOA',3,3.00000,4.00000,'> 2 - <= 3'
  UNION ALL SELECT 'GROWTH_NOA',4,4.00000,5.00000,'> 3 - <= 4'
  UNION ALL SELECT 'GROWTH_NOA',5,6.00000,999.00000,'> 5'

  UNION ALL SELECT 'REPAYMENT_RATE',0,0.00000,0.40000,'< 40%'
  UNION ALL SELECT 'REPAYMENT_RATE',1,0.40000,0.50000,'>= 40% - < 50%'
  UNION ALL SELECT 'REPAYMENT_RATE',2,0.50000,0.60000,'>= 50% - < 60%'
  UNION ALL SELECT 'REPAYMENT_RATE',3,0.60000,0.70000,'>= 60% - < 70%'
  UNION ALL SELECT 'REPAYMENT_RATE',4,0.70000,0.80000,'>= 70% - < 80%'
  UNION ALL SELECT 'REPAYMENT_RATE',5,0.80000,999.00000,'>= 80%'

  UNION ALL SELECT 'FLOW_DPK',0,0.05000,999.00000,'> 5%'
  UNION ALL SELECT 'FLOW_DPK',1,0.04000,0.05000,'<= 5%'
  UNION ALL SELECT 'FLOW_DPK',2,0.03000,0.04000,'<= 4%'
  UNION ALL SELECT 'FLOW_DPK',3,0.02000,0.03000,'<= 3%'
  UNION ALL SELECT 'FLOW_DPK',4,0.01000,0.02000,'<= 2%'
  UNION ALL SELECT 'FLOW_DPK',5,0.00000,0.01000,'<= 1%'
) v ON v.kode=i.kode
WHERE j.kode='AO_KREDIT'
  AND i.kode IN ('PENCAIRAN_NETO','GROWTH_KREDIT','GROWTH_NOA','REPAYMENT_RATE','FLOW_DPK')
ON DUPLICATE KEY UPDATE
  min_indeks=VALUES(min_indeks),
  max_indeks=VALUES(max_indeks),
  predikat=VALUES(predikat),
  aktif=1;

COMMIT;

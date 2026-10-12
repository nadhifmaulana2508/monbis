-- Parameter KPI AO Kredit 2024.
-- Jalankan setelah kpi_bisnis.sql dan kpi_score_per_indikator.sql.
-- Nilai persentase disimpan sebagai rasio desimal: 40% = 0.40.

START TRANSACTION;

UPDATE kpi_indikator i
JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.status=CASE
      WHEN i.kode IN ('EARLY_RUN_OFF','PENCAIRAN_NETO','NOA_BARU','REPAYMENT_RATE','MOB_6') THEN 'AKTIF'
      ELSE 'NONAKTIF'
    END,
    i.bobot=CASE i.kode
      WHEN 'EARLY_RUN_OFF' THEN 0.10
      WHEN 'PENCAIRAN_NETO' THEN 0.45
      WHEN 'NOA_BARU' THEN 0.10
      WHEN 'REPAYMENT_RATE' THEN 0.25
      WHEN 'MOB_6' THEN 0.10
      ELSE 0
    END,
    i.urutan=CASE i.kode
      WHEN 'EARLY_RUN_OFF' THEN 1
      WHEN 'PENCAIRAN_NETO' THEN 2
      WHEN 'NOA_BARU' THEN 3
      WHEN 'REPAYMENT_RATE' THEN 4
      WHEN 'MOB_6' THEN 5
      ELSE i.urutan
    END,
    i.kelompok=CASE i.kode
      WHEN 'PENCAIRAN_NETO' THEN 'Pertumbuhan'
      WHEN 'NOA_BARU' THEN 'Pertumbuhan'
      ELSE 'Kualitas'
    END,
    i.nama=CASE i.kode
      WHEN 'EARLY_RUN_OFF' THEN 'Early Run Off'
      WHEN 'PENCAIRAN_NETO' THEN 'Nominal Pencairan Kredit Neto'
      WHEN 'NOA_BARU' THEN 'NOA Debitur Baru'
      WHEN 'REPAYMENT_RATE' THEN 'Repayment Rate / DPD 0 (bomb factor kalau realisasi kecil)'
      WHEN 'MOB_6' THEN 'MOB <=6 Menunggak (bomb factor apabila tidak ada realisasi)'
      ELSE i.nama
    END,
    i.arah=CASE i.kode
      WHEN 'EARLY_RUN_OFF' THEN 'LOWER'
      WHEN 'MOB_6' THEN 'LOWER'
      ELSE 'HIGHER'
    END,
    i.unit=CASE i.kode
      WHEN 'PENCAIRAN_NETO' THEN 'RUPIAH'
      WHEN 'NOA_BARU' THEN 'NOA'
      ELSE 'PERSEN'
    END,
    i.formula_key=CASE i.kode
      WHEN 'PENCAIRAN_NETO' THEN 'REALISASI_KREDIT'
      WHEN 'NOA_BARU' THEN 'NOA_REALISASI'
      ELSE i.formula_key
    END,
    i.definisi=CASE i.kode
      WHEN 'EARLY_RUN_OFF' THEN 'Perbandingan dari pelunasan dipercepat DPD 0 pada bulan berjalan dibagi dengan kredit DPD 0 bulan lalu (kelolaan AO)'
      WHEN 'PENCAIRAN_NETO' THEN 'Jumlah realisasi netto pencairan kredit dibanding target AO'
      WHEN 'NOA_BARU' THEN 'Jumlah NOA Kredit status baru (NEW CIF)'
      WHEN 'REPAYMENT_RATE' THEN 'Kredit DPD 0 pada bulan berjalan dibandingkan dengan total kredit kelolaan AO'
      WHEN 'MOB_6' THEN 'OS pada MOB <=6 bulan berjalan yang sudah menunggak dibandingkan dengan total OS pada MOB <=6 bulan lalu (kelolaan AO)'
      ELSE i.definisi
    END,
    i.sumber_data=CASE i.kode
      WHEN 'EARLY_RUN_OFF' THEN 'Monbis / nominatif kredit'
      WHEN 'PENCAIRAN_NETO' THEN 'Monbis / realisasi kredit'
      WHEN 'NOA_BARU' THEN 'Monbis / nominatif kredit'
      WHEN 'REPAYMENT_RATE' THEN 'Monbis / nominatif kredit'
      WHEN 'MOB_6' THEN 'Monbis / nominatif kredit'
      ELSE i.sumber_data
    END,
    i.input_pa=CASE i.kode
      WHEN 'EARLY_RUN_OFF' THEN 'Average sampai akhir penilaian'
      WHEN 'PENCAIRAN_NETO' THEN 'Jumlah (akumulasi) realisasi netto setiap bulan'
      WHEN 'NOA_BARU' THEN 'Jumlah (akumulasi) NOA Baru (NEW CIF) setiap bulan'
      WHEN 'REPAYMENT_RATE' THEN 'Average sampai akhir penilaian'
      WHEN 'MOB_6' THEN 'Average sampai akhir penilaian'
      ELSE i.input_pa
    END
WHERE j.kode='AO_KREDIT';

-- Range skor 0-5 per indikator.
-- Untuk indikator LOWER, skor 5 adalah nilai paling rendah.
INSERT INTO kpi_parameter_skor_jabatan
  (jabatan_id,indikator_id,skor,min_indeks,max_indeks,predikat,aktif)
SELECT j.id,i.id,v.skor,v.min_indeks,v.max_indeks,v.predikat,1
FROM kpi_jabatan j
JOIN kpi_indikator i ON i.jabatan_id=j.id
JOIN (
  SELECT 'EARLY_RUN_OFF' kode,0 skor,0.02500 min_indeks,999.00000 max_indeks,'Di bawah target minimum' predikat
  UNION ALL SELECT 'EARLY_RUN_OFF',1,0.02000,0.02500,'Di bawah target'
  UNION ALL SELECT 'EARLY_RUN_OFF',2,0.01500,0.02000,'Perlu perbaikan'
  UNION ALL SELECT 'EARLY_RUN_OFF',3,0.01250,0.01500,'Memenuhi target'
  UNION ALL SELECT 'EARLY_RUN_OFF',4,0.01000,0.01250,'Melampaui target'
  UNION ALL SELECT 'EARLY_RUN_OFF',5,0.00000,0.01000,'Istimewa'

  UNION ALL SELECT 'PENCAIRAN_NETO',0,0.00000,0.50000,'Di bawah target minimum'
  UNION ALL SELECT 'PENCAIRAN_NETO',1,0.50000,0.60000,'Di bawah target'
  UNION ALL SELECT 'PENCAIRAN_NETO',2,0.60000,0.80000,'Perlu perbaikan'
  UNION ALL SELECT 'PENCAIRAN_NETO',3,0.80000,1.00000,'Memenuhi target'
  UNION ALL SELECT 'PENCAIRAN_NETO',4,1.00000,1.25000,'Melampaui target'
  UNION ALL SELECT 'PENCAIRAN_NETO',5,1.25000,999.00000,'Istimewa'

  UNION ALL SELECT 'NOA_BARU',0,0.00000,0.00000,'Di bawah target minimum'
  UNION ALL SELECT 'NOA_BARU',1,1.00001,2.00000,'Di bawah target'
  UNION ALL SELECT 'NOA_BARU',2,2.00001,3.00000,'Perlu perbaikan'
  UNION ALL SELECT 'NOA_BARU',3,3.00001,4.00000,'Memenuhi target'
  UNION ALL SELECT 'NOA_BARU',4,4.00001,5.00000,'Melampaui target'
  UNION ALL SELECT 'NOA_BARU',5,5.00001,999.00000,'Istimewa'

  UNION ALL SELECT 'REPAYMENT_RATE',0,0.00000,0.40000,'Di bawah target minimum'
  UNION ALL SELECT 'REPAYMENT_RATE',1,0.40000,0.50000,'Di bawah target'
  UNION ALL SELECT 'REPAYMENT_RATE',2,0.50000,0.60000,'Perlu perbaikan'
  UNION ALL SELECT 'REPAYMENT_RATE',3,0.60000,0.70000,'Memenuhi target'
  UNION ALL SELECT 'REPAYMENT_RATE',4,0.70000,0.80000,'Melampaui target'
  UNION ALL SELECT 'REPAYMENT_RATE',5,0.80000,999.00000,'Istimewa'

  UNION ALL SELECT 'MOB_6',0,0.09000,999.00000,'Di bawah target minimum'
  UNION ALL SELECT 'MOB_6',1,0.08000,0.09000,'Di bawah target'
  UNION ALL SELECT 'MOB_6',2,0.07000,0.08000,'Perlu perbaikan'
  UNION ALL SELECT 'MOB_6',3,0.06000,0.07000,'Memenuhi target'
  UNION ALL SELECT 'MOB_6',4,0.05000,0.06000,'Melampaui target'
  UNION ALL SELECT 'MOB_6',5,0.00000,0.05000,'Istimewa'
) v ON v.kode=i.kode
WHERE j.kode='AO_KREDIT'
ON DUPLICATE KEY UPDATE
  jabatan_id=VALUES(jabatan_id),
  min_indeks=VALUES(min_indeks),
  max_indeks=VALUES(max_indeks),
  predikat=VALUES(predikat),
  aktif=1;

COMMIT;

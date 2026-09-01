-- Sinkronisasi indikator aktif sesuai skema KPI bisnis.

-- AO Kredit: hanya lima indikator, total bobot 100%.
UPDATE kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.status=CASE WHEN i.kode IN ('EARLY_RUN_OFF','PENCAIRAN_NETO','NOA_BARU','REPAYMENT_RATE','MOB_6') THEN 'AKTIF' ELSE 'NONAKTIF' END,
    i.bobot=CASE i.kode WHEN 'EARLY_RUN_OFF' THEN 0.10 WHEN 'PENCAIRAN_NETO' THEN 0.45 WHEN 'NOA_BARU' THEN 0.10 WHEN 'REPAYMENT_RATE' THEN 0.25 WHEN 'MOB_6' THEN 0.10 ELSE 0 END,
    i.urutan=CASE i.kode WHEN 'EARLY_RUN_OFF' THEN 1 WHEN 'PENCAIRAN_NETO' THEN 2 WHEN 'NOA_BARU' THEN 3 WHEN 'REPAYMENT_RATE' THEN 4 WHEN 'MOB_6' THEN 5 ELSE i.urutan END
WHERE j.kode='AO_KREDIT';

-- AO Dana: empat indikator pada skema.
UPDATE kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.status=CASE WHEN i.kode IN ('TABUNGAN_AO','DEPOSITO_AO','NOA_BARU_DANA','PIPELINE_AO_DANA') THEN 'AKTIF' ELSE 'NONAKTIF' END,
    i.bobot=CASE i.kode WHEN 'TABUNGAN_AO' THEN 0.40 WHEN 'DEPOSITO_AO' THEN 0.20 WHEN 'NOA_BARU_DANA' THEN 0.30 WHEN 'PIPELINE_AO_DANA' THEN 0.10 ELSE 0 END,
    i.nama=CASE WHEN i.kode='PIPELINE_AO_DANA' THEN 'Maintaince Nasabah 20 Besar' ELSE i.nama END,
    i.unit=CASE WHEN i.kode='PIPELINE_AO_DANA' THEN 'PERSEN' ELSE i.unit END
WHERE j.kode='AO_DANA';

-- AO Remedial: aktifkan Recovery sebagai indikator kelima.
UPDATE kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.nama='Recovery', i.status='AKTIF', i.bobot=0.80, i.urutan=5
WHERE j.kode='AO_REMEDIAL' AND i.kode='PENYELESAIAN_KREDIT';

UPDATE kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.status=CASE WHEN i.kode IN ('BACKFLOW','AMOUNT_COLLECTION','PTP_DIPENUHI','PENURUNAN_NPL','PENYELESAIAN_KREDIT') THEN 'AKTIF' ELSE 'NONAKTIF' END
WHERE j.kode='AO_REMEDIAL';

-- Penamaan indikator mengikuti lembar skema penilaian.
UPDATE kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.nama=CASE i.kode
  WHEN 'TABUNGAN_AO' THEN 'Tabungan AO (net growth 35.000.000)'
  WHEN 'DEPOSITO_AO' THEN 'Deposito AO (net growth 15.000.000)'
  WHEN 'NOA_BARU_DANA' THEN 'Noa Baru'
  WHEN 'PIPELINE_AO_DANA' THEN 'Maintaince Nasabah 20 Besar'
  WHEN 'NOA_BARU' THEN 'NOA Debitur Baru'
  WHEN 'PIPELINE' THEN 'Pipeline'
  ELSE i.nama END
WHERE j.kode IN ('AO_KREDIT','AO_DANA');

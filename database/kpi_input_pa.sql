-- Tambahan metadata Input PA pada master indikator KPI.
ALTER TABLE kpi_indikator
  ADD COLUMN IF NOT EXISTS input_pa VARCHAR(255) NULL AFTER validator;

UPDATE kpi_indikator i JOIN kpi_jabatan j ON j.id=i.jabatan_id
SET i.input_pa=CASE
  WHEN j.kode='AO_KREDIT' AND i.kode='EARLY_RUN_OFF' THEN 'Average sampai akhir penilaian'
  WHEN j.kode='AO_KREDIT' AND i.kode='PENCAIRAN_NETO' THEN 'Jumlah (akumulasi) realisasi netto setiap bulan'
  WHEN j.kode='AO_KREDIT' AND i.kode='NOA_BARU' THEN 'Jumlah (akumulasi) NOA Baru (NEW CIF) setiap bulan'
  WHEN j.kode='AO_KREDIT' AND i.kode='PIPELINE' THEN 'DSAR (Daily Sales Activity Report)'
  WHEN j.kode='AO_KREDIT' AND i.kode IN ('REPAYMENT_RATE','MOB_6') THEN 'Average sampai akhir penilaian'
  WHEN j.kode='AO_REMEDIAL' THEN 'Average sampai akhir penilaian'
  WHEN j.kode='AO_DANA' AND i.kode IN ('TABUNGAN_AO','DEPOSITO_AO') THEN 'Jumlah nominal sampai akhir penilaian'
  WHEN j.kode='AO_DANA' AND i.kode='NOA_BARU_DANA' THEN 'Jumlah (akumulasi) NOA Baru (NEW CIF) setiap bulan'
  WHEN j.kode='AO_DANA' AND i.kode='PIPELINE_AO_DANA' THEN 'DSAR (Daily Sales Activity Report)'
  ELSE i.input_pa END
WHERE j.kode IN ('AO_KREDIT','AO_DANA','AO_REMEDIAL');

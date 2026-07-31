-- Index pendukung report OTP / CCL.
-- Jalankan sekali di database DPK saat maintenance window.
-- Jika index dengan nama yang sama sudah ada, hapus baris terkait sebelum run.

ALTER TABLE nominatif
  ADD INDEX idx_otp_created_scope (created, kolektibilitas, kode_cabang, kode_group1, kode_group2, hari_menunggak, baki_debet),
  ADD INDEX idx_otp_rekening_created (no_rekening, created),
  ADD INDEX idx_otp_jatuh_tempo (tgl_jatuh_tempo);

-- Opsional tambahan untuk mempercepat filter target M-1 dan lookup rekening harian.
-- Jalankan kalau endpoint rekap_rr masih terasa berat di filter Konsolidasi.
ALTER TABLE nominatif
  ADD INDEX idx_otp_created_scope_rekening (created, kolektibilitas, kode_cabang, kode_group1, kode_group2, hari_menunggak, no_rekening),
  ADD INDEX idx_otp_created_rekening_status (created, no_rekening, baki_debet, hari_menunggak);

ALTER TABLE transaksi_kredit
  ADD INDEX idx_otp_trx_rekening_tanggal (no_rekening, tgl_trans),
  ADD INDEX idx_otp_trx_tanggal_rekening (tgl_trans, no_rekening);

ALTER TABLE report_komitmen
  ADD INDEX idx_otp_komit_rekening_tanggal (rekening, tanggal),
  ADD INDEX idx_otp_komit_tanggal_rekening (tanggal, rekening);

-- Index tambahan untuk mempercepat list, rekap, detail, dan amount call mapping.
-- Jalankan sekali di database deployment.

ALTER TABLE nominatif
  ADD INDEX idx_mapping_ao_closing_scope (created,kode_cabang,hari_menunggak,baki_debet,no_rekening),
  ADD INDEX idx_mapping_ao_detail (created,kode_cabang,no_rekening);

ALTER TABLE mapping_ao_remedial
  ADD INDEX idx_mapping_ao_fe (ao_fe_id_peg),
  ADD INDEX idx_mapping_ao_be (ao_be_id_peg);

ALTER TABLE transaksi_kredit
  ADD INDEX idx_mapping_ao_trx_date_rek (tgl_trans,no_rekening);

-- Optimasi endpoint "test perkembangan tabungan".
-- Jalankan sekali di database DPK server saat maintenance window.
-- Index pertama mempercepat query top AO tabungan per tanggal.
-- Index kedua mempercepat query tabungan baru berbasis tgl_register.



ALTER TABLE nominatif_tabungan
    ADD INDEX idx_tab_created_ao (created, nama_ao, kode_kantor, saldo);

ALTER TABLE nominatif_tabungan
    ADD INDEX idx_tab_created_register_kantor (created, tgl_register, kode_kantor, saldo);

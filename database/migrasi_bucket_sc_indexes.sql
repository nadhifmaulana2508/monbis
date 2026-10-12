-- Index covering untuk rekap Migrasi Soft Collection.
-- Jalankan sekali pada database DPK saat maintenance window.
-- Index ini menutup kebutuhan lookup actual berdasarkan rekening + tanggal
-- sekaligus mengambil saldo_bank/baki_debet dan hari_menunggak tanpa table lookup.

ALTER TABLE nominatif
  ADD INDEX idx_bucket_sc_migrasi_join_saldo
    (no_rekening, created, saldo_bank, hari_menunggak),
  ADD INDEX idx_bucket_sc_migrasi_join_baki
    (no_rekening, created, baki_debet, hari_menunggak);

# Catatan RBB

Status: diparkir sementara. Fokus pengembangan berikutnya adalah KPI dan perbaikan bug report.

## Kondisi terakhir

- RBB yang sedang disiapkan adalah RBB tahun 2027 dengan periode Januari–Desember 2027.
- Halaman yang sudah dibuat:
  - `pages/input_rbb.php` — Proyeksi RBB.
  - `pages/input_rbb_detail.php` — Input detail COA untuk Kredit, DAMAS, Pendapatan, dan Beban.
  - `pages/input_rbb_aba.php` — Input penempatan ABA.
- Header tabel ABA, CKPN ABA, Pendapatan Bank Lain, input detail, dan proyeksi sudah diseragamkan menjadi JAN–DES.
- Input ABA memiliki tab Penempatan, CKPN ABA otomatis, Pendapatan Bank Lain otomatis, dan History 3 Tahun.
- CKPN ABA dihitung otomatis sebesar `nominal ABA × 0,5%`.
- Pendapatan bunga dihitung otomatis sebesar `nominal ABA × (1,25% / 12)`.
- History ABA mengambil data aktual 3 tahun terakhir dari `acc_history`.
- Akses Input RBB sementara dibatasi untuk `employee_id` / `id_peg = 102-119`.
- Draft, pengajuan, penolakan, dan approval diproses dari halaman Proyeksi RBB.

## Tabel RBB yang sudah disiapkan

- `rbb_coa`
- `rbb_plan`
- `rbb_plan_value`
- `rbb_plan_approval`
- `rbb_plan_aba`
- `rbb_plan_aba_value`

Tabel RBB dibuat terpisah agar data RBB 2026 tidak ikut rusak.

## Pekerjaan RBB berikutnya

- Finalisasi master kantor dan hak akses input cabang.
- Alur approval: cabang → Kanwil → pusat.
- Finalisasi mapping COA dari `acc_history`.
- Input produksi kredit per produk Januari–Desember.
- Perhitungan run off dari history dan kredit baru.
- Input rencana PH dan AYDA.
- Formula total OS: OS bulan sebelumnya + realisasi − run off − PH − AYDA.
- Breakdown kolektibilitas, dengan produksi otomatis masuk kolektibilitas L.
- Input RBB DAMAS.
- Input Pendapatan.
- Input Beban, termasuk pemasaran berdasarkan sub-COA dan pemeliharaan.
- Review ulang data existing sebelum RBB diaktifkan untuk user yang lebih luas.

## File backend terkait

- `api/controllers/RbbController.php`
- `database/rbb_planning.sql`
- `views/navbar.php`
- `views/footer.php`

Validasi syntax PHP terakhir untuk halaman dan controller RBB sudah berhasil. Jangan melanjutkan perubahan RBB sebelum pekerjaan KPI dan bug prioritas selesai.

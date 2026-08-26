# Standardisasi Report Monbis

Dokumen ini adalah checklist migrasi halaman report lama ke komponen bersama.
Tujuannya: setiap report memakai font, header, filter, tabel, modal detail, dark mode,
dan responsif yang sama dari `assets/` dan `components/`.

## Acuan Implementasi

- Referensi utama: `pages/report_npl.php` dan `pages/report_recovery_npl.php`.
- Bootstrap UI: `components/bootstrap.php`.
- Layout report standar: `components/report-page.php` melalui `mb_render_report_page()`.
- Asset komponen: `assets/css/monbis-components.css` dan `assets/js/monbis-components.js`.
- Jangan menyalin CSS tabel, modal, atau header lama ke halaman baru.
- Endpoint/API lama boleh tetap dipakai melalui adapter JavaScript halaman.

## Checklist Per Report

- [ ] Gunakan `mb_ui_assets('.')` lalu `mb_render_report_page()`; class `mb-report-page mb-report-standard` dibuat otomatis.
- [ ] Header, toolbar, pencarian, export, table shell, loading, dan scroll dibuat dari konfigurasi `mb_render_report_page()`.
- [ ] Gunakan `mb_render_detail_modal()` untuk detail rekening/debitur.
- [ ] Semua filter otomatis reload dengan debounce, tanpa tombol Cari kecuali diperlukan proses manual.
- [ ] Filter kantor mengikuti hak akses: pusat dapat memilih konsolidasi/korwil/cabang, cabang terkunci pada kantor sendiri.
- [ ] Tabel desktop tidak memaksa scroll halaman; scroll hanya berada di dalam table wrapper.
- [ ] Mobile/tablet dapat scroll tabel horizontal dan vertikal, kolom identitas sticky, serta filter dapat dibuka/tutup.
- [ ] Header, thead, nominal, NOA, badge, dan dark/light mode memakai class `mb-*`.
- [ ] Jika tabel memiliki judul dan subjudul kolom, buat `thead_html` dengan `mb_build_grouped_thead()` dari `components/data-table.php` dan tambahkan class root `mb-report-grouped-head`.
- [ ] Detail memiliki pencarian, pagination, export, loading, empty state, dan error state.
- [ ] Nominal menggunakan format Indonesia dan tidak memakai garis bawah bila bukan link/detail.
- [ ] Validasi PHP dengan `php -l`, lalu cek filter, total, detail, export, desktop, tablet, dan mobile.

## Status Migrasi

| Report | Halaman baru | Komponen | Detail | Responsif | Catatan |
| --- | --- | --- | --- | --- | --- |
| Monitoring Kredit | `report_npl.php` | Selesai | Selesai | Selesai | Acuan utama; memakai `mb_render_report_page()` |
| Recovery NPL | `report_recovery_npl.php` | Selesai | Selesai | Selesai | Acuan utama; memakai `mb_render_report_page()` |
| Monitoring PH | `report_ph.php` | Selesai | Selesai | Perlu visual QA | Mapping nama kantor, detail cabang, dan detail total Recovery/LGD sudah diperbaiki |
| Realisasi & Growth | `report_realisasi_kredit_growth.php` | Selesai | Selesai | Perlu visual QA | Kantor 000 dipertahankan dan menu tersedia di Dev Report |
| Mutasi Kredit | `report_mutasi_kredit.php` | Selesai | Selesai | Perlu visual QA | Menggunakan komponen bersama dan menampilkan nominal BE tanpa pembagian 1000 |
| Potensi NPL | `report_potensi_npl.php` | Selesai | Selesai | Perlu visual QA | Dev Report; grouped thead, detail component, serta filter konsolidasi/korwil/cabang |
| Recovery PH lama | `recovery_ph.php` | Belum | Ada | Lama | Dipertahankan sebagai pembanding |

## Aturan Tambahan

- Perubahan umum dilakukan di `assets/css/monbis-components.css` atau `assets/js/monbis-components.js`.
- Perubahan khusus data dilakukan di halaman report atau endpoint terkait.
- Setelah sebuah report sudah dimigrasi, menu diarahkan ke halaman `report_*`; halaman lama jangan dihapus sampai review selesai.
- Hindari `<style>` khusus di halaman report. Tambahkan variasi yang reusable ke `assets/css/monbis-components.css` dengan class report sendiri, lalu pakai komponen yang sama.

## Kontrak Tampilan Report Standar

- Root report wajib memakai class `mb-report-standard` melalui `mb_render_report_page()`.
- Struktur wajib: page header, card grow, toolbar, table region, sticky thead, sticky total, dan sticky kolom identitas.
- Kolom identitas report memakai acuan Mutasi Kredit: `Kode` 44px, `Kantor` 136px, sticky kedua mulai 44px, serta total bertuliskan `ALL` / `GRAND TOTAL`.
- Kolom pertama memakai `mb-code-col mb-sticky-left`; kolom nama memakai `mb-sticky-left-2 mb-name` dan offset sesuai lebar kolom kode.
- Nominal memakai `mb-num`; NOA tampil sebagai `mb-subvalue` di bawah nominal jika ruang terbatas.
- Halaman report tidak boleh memiliki `<style>` sendiri. Perubahan visual umum hanya di `assets/css/monbis-components.css`.
- JavaScript halaman hanya menjadi adapter endpoint, mapping data, render row, detail, dan export.
- Breakpoint desktop, tablet, dan mobile mengikuti `.mb-report-standard`; jangan membuat breakpoint khusus di halaman.

## Prompt Migrasi Siap Pakai

> Migrasikan halaman report ini ke Monbis Report Standard. Gunakan `components/bootstrap.php`, `mb_ui_assets('.')`, `mb_render_report_page()`, `mb_render_info_modal()`, dan `mb_render_detail_modal()`. Root wajib `mb-report-standard`. Jangan menambah `<style>` di halaman. Gunakan class `mb-*` untuk thead, sticky identity, total row, nominal, NOA, loading, empty/error state, modal, dark mode, dan responsif. Pertahankan endpoint lama hanya sebagai adapter JavaScript. Validasi PHP, JavaScript, endpoint, desktop, tablet, dan mobile.

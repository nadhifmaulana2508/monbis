# Panduan Migrasi Menu ke MONBIS React V2

Dokumen ini adalah acuan wajib saat memindahkan menu/report lama ke frontend React di
folder `monbis-v2/`. Tujuannya agar menu baru mengikuti pola React V2 yang sudah ada,
bukan menyalin ulang pola PHP lama atau membuat komponen yang tampil berbeda.

> Dokumen ini khusus untuk `monbis-v2/`. `../issue.md` mengatur report PHP berbasis
> `mb_*`, sedangkan `../issue_monbis_v2.md` membahas frontend PHP di folder `v-2/`.
> Jangan mencampur ketiga kontrak tersebut.

## Aturan Migrasi

### 1. Pelajari sumber sebelum mengubah

- Baca halaman lama, route, pemanggilnya, endpoint, controller/query terkait, dan
  referensi report React V2 yang paling mirip.
- Catat filter, perhitungan, sorting, total, detail, export, akses/RBAC, dan perilaku
  khusus yang harus tetap sama.
- Jangan menebak nama field atau payload API; pastikan dari pemakaian existing dan
  response endpoint.
- Pertahankan aturan bisnis dan sumber data lama. Jangan mengganti backend atau
  mengubah query di luar kebutuhan migrasi tanpa alasan yang terverifikasi.

### 2. Ikuti arsitektur React V2

- Implementasikan halaman di `monbis-v2/src/features/<nama-menu>/` dan daftarkan route
  serta navigasi mengikuti pola yang sudah ada.
- Gunakan `AppShell`, komponen shadcn/shared yang tersedia, helper API, token tema, dan
  komponen tabel/modal bersama. Cari komponen existing sebelum membuat yang baru.
- API harus memakai helper/base URL yang sudah tersedia. Jangan menanam URL localhost,
  dev, atau production di source.
- Jangan mengubah `dist/` sebagai source of truth. Baca script `package.json`; build
  hanya untuk verifikasi dan jangan menambahkan artifact generated ke source tanpa
  kebutuhan project.

### 3. Konsistensi filter dan data

- Filter harus benar-benar memengaruhi payload dan hasil. Hapus kontrol yang tidak
  berfungsi.
- Muat opsi kantor/pegawai dari endpoint master yang sudah ada, urutkan dengan benar,
  dan gunakan pencarian pada dropdown panjang.
- Filter kantor mengikuti akses: user pusat dapat memilih area sesuai haknya, user
  kantor terbatas pada kantor sendiri.
- Untuk filter dependent, reset pilihan turunan dan data lama ketika induk berubah.
- Terapkan default, breakdown, sorting, total, pagination, dan export sesuai halaman
  lama. Pastikan pilihan breakdown seperti kantor/Kankas/AO benar-benar mengubah
  payload dan data, bukan sekadar label.
- Request harus menangani pembatalan saat filter berubah atau halaman ditutup.

### 4. Tabel, modal, tema, dan responsif

- Gunakan shell tabel bersama: scroll tetap di dalam tabel, header/total sticky,
  kolom identitas sticky bila dibutuhkan, garis kolom sejajar, dan angka konsisten
  rata kanan.
- Pertahankan hierarki header bertingkat bila report lama memiliki grouped header.
- Modal detail gunakan `ReportDetailLayer` agar berada di atas navbar, sticky table,
  dan overlay lain. Dropdown di dalam modal harus tetap dapat tampil di atas modal.
- Modal informasi dari ikon `i` gunakan `ReportInfoDialog`. Backdrop meredupkan seluruh
  aplikasi tanpa blur; dialog dirender di portal pada lapisan tertinggi (di atas
  navbar), konsisten terpusat, serta bisa ditutup dengan tombol close, Escape, atau
  klik backdrop. Kunci scroll halaman dan kembalikan fokus ke pemicu saat ditutup.
- Toolbar detail konsisten: search buka/tutup, filter kompak, export, dan close; susun
  satu baris tanpa membuat modal melebar di viewport sempit.
- Di mobile gunakan kartu untuk detail bila tabel terlalu padat; kurangi padding yang
  tidak perlu dan hindari horizontal overflow halaman.
- Uji light/dark dengan token tema yang sama seperti menu lain. Hindari hardcode warna
  yang merusak salah satu tema.
- Gunakan loading, empty, error, disabled, dan feedback aksi yang jelas.
- Jaga keyboard/focus state, label tombol ikon, dan ukuran target sentuh.

### 5. Sorting dan export rekap

- Untuk header yang bisa diurutkan, gunakan `ReportSortHeader` dari
  `src/components/report-sort-header.tsx`. Jangan membuat tombol, ikon panah, atau
  atribut `aria-sort` sendiri di masing-masing report.
- Sorting default, key, arah awal, dan fungsi pembanding boleh berbeda jika memang
  mengikuti aturan report lama. Samakan interaksi: klik kolom baru mengurutkan naik,
  klik ulang kolom aktif membalik arah, ikon hanya muncul di kolom aktif, dan header
  dapat dipakai dengan keyboard.
- Header bertingkat tetap mempertahankan struktur group/rowSpan, tetapi kontrol sort
  setiap kolom harus memakai komponen bersama.
- Semua tabel rekap menyediakan export Excel dan gambar melalui
  `ReportExportMenu`. Export gambar hanya untuk rekap. Modal/detail hanya menyediakan
  Excel.
- Excel harus berupa workbook `.xlsx` melalui helper bersama
  `downloadReportExcel`, bukan CSV/TSV yang hanya diberi ekstensi `.xls`. Pertahankan
  kolom dan nilai yang diperlukan, letakkan angka sebagai nilai numerik agar mudah
  difilter, dihitung, dan diolah kembali. Nama kantor/kode, tanggal, persentase, dan
  satuan kolom harus jelas; jangan mengekspor format presentasi seperti pemisah ribuan
  sebagai teks jika nilai numerik tersedia.
- Ikuti perilaku export lama untuk menentukan dataset aktif (rekap, breakdown, atau
  mode perbandingan), tetapi pastikan header export sesuai dengan dataset yang sedang
  diekspor dan urutan baris mengikuti sorting yang tampil.

### 6. Keamanan dan batas perubahan

- Pertahankan RBAC, autentikasi, batas data per kantor, escaping, dan parameter binding.
- Jangan menampilkan credential atau data sensitif di log, source, atau laporan.
- Ubah file sesedikit mungkin. Jangan menyentuh `rbb/` atau `rbb-bisnis/` kecuali
  diminta eksplisit.
- Jangan commit atau push kecuali diminta.

## Checklist Sebelum Selesai

- [ ] Route menu dan refresh langsung berfungsi; navigasi/RBAC tetap benar.
- [ ] Payload API, data kosong, satu data, banyak data, tanggal berbeda, total, dan
      detail sudah diperiksa.
- [ ] Filter kantor/area, dependent filter, breakdown, sorting, pagination, dan export
      sudah dicoba.
- [ ] Semua header sortable memakai komponen bersama; default sort dan pembanding
      sesuai aturan report, ikon aktif konsisten, dan keyboard/focus sudah diuji.
- [ ] Export rekap Excel menghasilkan `.xlsx` dengan nilai numerik dan kolom yang
      sesuai mode aktif; export gambar hanya tersedia di rekap, bukan modal detail.
- [ ] Loading, empty, error, dan request batal tidak meninggalkan data lama.
- [ ] Desktop, tablet, dan mobile sudah diperiksa; tabel/modal tidak menutupi kontrol.
- [ ] Sticky header/total/kolom identitas dan light/dark sudah diperiksa.
- [ ] `npm run lint`, `npm run build`, dan `git diff --check` sudah dijalankan jika
      tersedia; warning lama dicatat, bukan disamarkan.
- [ ] Diff/status diperiksa; tidak ada file/generated artifact atau perubahan lain yang
      tidak diminta.

## Prompt Siap Pakai

Gunakan prompt ini untuk migrasi menu berikutnya, ganti bagian dalam kurung siku:

```text
Migrasikan submenu [NAMA MENU] dari [PATH/URL HALAMAN LAMA] ke MONBIS React V2 pada
route [ROUTE TARGET]. Ikuti seluruh aturan di monbis-v2/issue.md.

Mulai dengan membaca AGENTS.md dan panduan ini. Telusuri halaman lama, route dan
navigasinya, endpoint/controller/query, RBAC, serta semua perilaku yang terlihat oleh
pengguna. Cocokkan dengan implementasi React V2 yang sudah ada; gunakan halaman dan
komponen bersama sebagai acuan, bukan membuat pola baru. Jangan menebak nama field,
payload, rumus, filter, atau akses. Pertahankan data dan perilaku bisnis lama.

Sebelum coding, catat kontrak lama: filter dan nilai default, hak akses dan batas data,
breakdown, default sorting, rumus total, pagination/detail, mode laporan, serta kolom
dan dataset export. Implementasikan hanya fitur yang memang tersedia di menu sumber.
Jangan mengaktifkan ringkasan, menambah modal/detail, atau membuat filter palsu.

Untuk UI, gunakan AppShell dan komponen yang sudah ada. Cross-check konsistensi terhadap
menu React V2 lain, terutama:
- tabel/header/total/sticky dan angka rata kanan;
- ReportSortHeader untuk semua kolom sortable, dengan default sort lama, ikon hanya
  pada kolom aktif, dan dukungan keyboard;
- ReportInfoDialog untuk modal ikon i: overlay meredupkan tanpa blur, di atas navbar,
  posisi dan perilaku tutup konsisten;
- ReportDetailLayer untuk modal detail; toolbar search/filter/export/close satu baris;
- ReportExportMenu dan helper Excel bersama: export rekap Excel .xlsx dan gambar,
  modal detail Excel saja, nilai numerik tetap numerik;
- filter navbar, tema light/dark, loading/error/empty, serta layout desktop/mobile.

Integrasikan route, sidebar/menu, legacy link, dan hak akses mengikuti pola aplikasi.
Gunakan helper API dan base URL yang ada, batalkan request lama saat filter berubah,
serta cegah data lama tampil setelah filter/access berubah. Jangan mengubah endpoint atau
query di luar kebutuhan yang telah diverifikasi.

Setelah implementasi, uji data kosong/satu/banyak, filter dan mode, akses positif dan
negatif, total terhadap data sumber, sorting setiap mode, detail/pagination jika ada,
dan hasil export. Cek langsung route dan refresh, desktop/mobile, light/dark, sticky,
scroll, serta modal di browser bila tersedia. Bandingkan hasilnya dengan menu acuan;
jangan menyatakan selesai hanya karena build berhasil. Jika visual runtime tidak bisa
diuji, sebutkan sebagai batas verifikasi.

Jalankan lint, build, dan git diff --check yang tersedia. Periksa git diff/status agar
hanya perubahan yang diperlukan masuk dan perubahan user lain tetap utuh. Laporkan
file yang berubah, ringkasan perilaku yang dipertahankan, hasil cross-check/verifikasi,
warning yang masih ada, serta hal yang belum bisa diuji. Jangan commit atau push.
```

Contoh:

```text
Pelajari dan migrasikan menu NPL dari pages/report_npl.php ke MONBIS React V2 pada
route /npl. Ikuti seluruh aturan di monbis-v2/issue.md. Pertahankan endpoint, filter,
hak akses, sorting, total, detail, dan export dari menu lama, lalu verifikasi desktop,
mobile, light/dark, lint, dan build.
```

## Prompt: RBB Realisasi Kredit

```text
Pelajari dan migrasikan menu RBB Realisasi Kredit dari pages/realisasi_rbb.php ke MONBIS
React V2 pada route /rbb-realisasi-kredit. Ikuti seluruh aturan di monbis-v2/issue.md.

Sebelum mengubah kode, telusuri halaman lama, endpoint api/rbb/, api/kode/, api/date/,
controller/query dan hak akses yang dipakai. Pertahankan filter tanggal harian, pilihan
Konsolidasi/Korwil/cabang beserta pembatasan kantor user, mode perbandingan (termasuk
auto/history), breakdown bulanan, metadata, perhitungan total, kolom, dan urutan
default masing-masing mode. Gunakan API helper/base URL MONBIS V2; jangan menanam URL
localhost.

Pertahankan seluruh view yang benar-benar tersedia di halaman lama: rekap berjalan,
mode history/YoY bila dipicu oleh compare_mode, serta breakdown bulanan jika endpoint
mengembalikannya. Cek kontrak payload dan response; jangan mengasumsikan summary aktif
(saat ini RBB_SUMMARY_ENABLED=false) atau menambah detail/modal yang tidak ada.

Gunakan AppShell, filter navbar, tabel, dan komponen shadcn/shared yang tersedia.
Sorting harus memakai ReportSortHeader; pertahankan default sort lama per mode
(kode_kantor naik untuk rekap berjalan, selisih naik untuk history, periode turun untuk
breakdown bulanan), dengan ikon hanya pada kolom aktif. Implementasikan export rekap
Excel .xlsx memakai helper bersama, dengan angka numerik dan kolom mengikuti mode
aktif; sediakan export gambar pada rekap. Jangan mengekspor CSV/TSV ber-ekstensi .xls.

Uji akses konsolidasi/Korwil/cabang, tanggal berbeda, response kosong/error, request
batal saat filter berubah, perubahan mode, total-versus-detail bila memang ada,
sorting setiap mode, serta export Excel/gambar. Periksa desktop/mobile, light/dark,
overflow dan sticky table. Jalankan lint, build, dan git diff --check; laporkan file
berubah, hasil verifikasi, serta bagian yang tidak dapat diuji. Jangan commit atau push.
```

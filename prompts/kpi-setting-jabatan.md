# Prompt lengkap: migrasi Setting KPI Jabatan ke React

Prompt ini hanya menjadi instruksi kerja. Menjalankan prompt pada tahap
`AUDIT_ONLY` tidak boleh mengubah source code.

```text
[MONBIS-UI] [MIGRASI:PHP-REACT] [MODULE:KPI] [PAGE:setting-kpi-jabatan]
[MODE:AUDIT_ONLY]

Tujuan akhir:

Pindahkan frontend page `pages/setting_kpi_jabatan.php` dari PHP native ke
React. Page React harus menjadi bagian dari aplikasi KPI mandiri, memakai shell
MONBIS, komponen resmi shadcn/ui, REST API MONBIS, dan struktur feature yang
konsisten dengan menu KPI lain.

Aplikasi KPI sengaja dipisahkan agar nanti dapat dipindahkan ke repo SIMPEG
hanya dengan menyalin folder `kpi-tukin/`. Jangan mengambil import dari
`frontend/src/`.

Jangan mengerjakan implementasi pada tahap ini. Lakukan audit saja, lalu
berhenti dan tunggu persetujuan untuk tahap implementasi.

Kontrak target:

- Route local: `http://localhost:5173/kpi/setting_kpi_jabatan`
- Route server: `https://monbis.bkkjateng.co.id/kpi/setting_kpi_jabatan`
- Folder aplikasi: `kpi-tukin/`
- Folder feature: `kpi-tukin/src/features/setting-kpi-jabatan/`
- Fallback PHP: `/pages/setting_kpi_jabatan.php`
- API base: gunakan resolver dari `kpi-tukin/src/lib/api-base.ts`
- PRD utama: `docs/prd-migrasi-php-react.md`
- PRD visual: `docs/prd-modernisasi-4-laporan-kredit.md`
- Scope aplikasi: KPI AO Kredit, AO Dana, dan AO Remedial saja
- Route prefix: `/kpi/`

## Audit wajib

Baca dan petakan tanpa mengubah file:

1. `pages/setting_kpi_jabatan.php`
2. controller dan router API KPI
3. komponen PHP yang dipakai page tersebut
4. komponen React KPI yang sudah ada di `kpi-tukin/src/components/`
5. feature React KPI yang memiliki pola table, filter, modal, dan save action

Pastikan audit juga memeriksa bahwa aplikasi KPI dapat dibuild dari
`kpi-tukin/` tanpa dependency import ke `frontend/src/`.

Laporkan temuan dengan bagian berikut.

### A. Perilaku page lama

Petakan:

- judul, subtitle, icon info, dan isi modal informasi;
- filter jabatan dengan opsi `AO_KREDIT`, `AO_DANA`, dan `AO_REMEDIAL`;
- filter unit dengan opsi semua unit, `RUPIAH`, `NOA`, `PERSEN`, dan `JUMLAH`;
- search indikator;
- table indikator dan kolomnya;
- parameter skor untuk range skor 0 sampai 5;
- tombol simpan satu baris dan tombol simpan semua;
- perilaku reload setelah simpan;
- format bobot dalam persen;
- format target berdasarkan unit;
- validasi indeks minimum dan maksimum;
- pesan loading, empty, error, sukses, dan gagal simpan.

### B. Kontrak API

Catat untuk setiap request:

- URL atau request type;
- HTTP method;
- payload lengkap;
- field response yang dipakai;
- status sukses dan status error;
- auth header, cookie, dan permission;
- apakah request perlu abort atau race protection.

Minimal verifikasi action lama berikut. Jangan menganggap nama action benar
sebelum dibuktikan dari controller/router:

- `type: setting` untuk bootstrap indikator dan parameter skor;
- `type: setting_scores` bila dipakai untuk mengambil parameter skor;
- `type: save_indicator` untuk bobot, status, dan target default;
- `type: save_target_default` bila endpoint itu digunakan page lama;
- `type: save_score` untuk parameter range skor.

### C. Aturan bisnis yang tidak boleh berubah

Pastikan audit menjawab:

- apakah bobot satu jabatan harus berjumlah 100 persen;
- apakah status `PILOT`, `AKTIF`, dan `NONAKTIF` memiliki perlakuan berbeda;
- bagaimana target unit Rupiah, NOA, persen, dan jumlah diparse;
- apakah target default disimpan sebagai angka mentah;
- apakah indeks kosong berarti tidak terbatas atau memiliki nilai default;
- apakah `max_indeks` boleh bernilai 999 atau nilai khusus lain;
- siapa yang boleh menyimpan perubahan;
- apakah perubahan berlaku langsung atau mulai periode berikutnya;
- apakah save all bersifat atomic dari sisi UI atau hanya kumpulan request.

Jika jawabannya tidak ditemukan, tandai `BELUM TERKONFIRMASI`. Jangan mengisi
dengan asumsi dan jangan memindahkan rumus ke frontend.

### D. Rencana feature

Usulkan file berikut hanya jika memang diperlukan:

- `setting-kpi-jabatan-page.tsx` sebagai orchestrator;
- `setting-kpi-jabatan-types.ts` untuk type dan payload;
- `setting-kpi-jabatan-data.ts` untuk API, mapper, parser, dan formatter;
- `setting-kpi-jabatan-filter-panel.tsx` untuk filter dan search;
- `setting-kpi-jabatan-table.tsx` untuk tabel indikator;
- `setting-kpi-jabatan-modals.tsx` untuk info dan konfirmasi;
- `setting-kpi-jabatan-score-table.tsx` bila parameter skor memiliki boundary
  visual dan state sendiri.

Jangan membuat component wrapper baru jika kebutuhan dapat memakai component
resmi shadcn/ui atau pola yang sudah ada.

### E. Rencana UI

Tunjukkan rancangan ringkas untuk:

- navbar dan route aktif;
- toolbar page, icon info, search, filter, dan save;
- desktop table dengan sticky identitas bila diperlukan;
- mobile table dan kolom yang boleh disembunyikan;
- input bobot dan target;
- parameter skor 0 sampai 5;
- loading, empty, error, disabled, dirty, saving, dan success state;
- dark mode;
- modal informasi yang tidak memiliki scroll internal untuk ringkasan pendek.

Jangan menggunakan warna berbeda untuk setiap kolom. Gunakan warna hanya
untuk status, error, warning, dan keberhasilan penyimpanan.

### F. Acceptance criteria awal

Buat checklist parity yang dapat diuji:

- filter jabatan dan unit menghasilkan data yang sama;
- search indikator menghasilkan baris yang sama;
- bobot dan target ditampilkan serta diparse dengan benar;
- parameter skor 0 sampai 5 tetap dapat dibaca dan diedit;
- save satu baris dan save semua mengirim payload yang benar;
- validasi minimum dan maksimum tetap sama;
- permission dan auth tidak melemah;
- response error dapat dilihat pengguna;
- reload setelah save tidak kehilangan filter secara tidak sengaja;
- export hanya ditambahkan jika memang tersedia pada page lama;
- route React dan fallback PHP sama-sama dapat dibuka.

## Format output tahap audit

Berikan output dengan urutan:

1. Ringkasan page lama.
2. Daftar endpoint dan payload.
3. Aturan bisnis yang terkonfirmasi.
4. Hal yang belum terkonfirmasi.
5. Rencana struktur feature.
6. Rencana UI desktop dan mobile.
7. Acceptance criteria.
8. Risiko parity dan rencana rollback.

Setelah output tersebut, berhenti. Jangan memakai apply_patch, jangan membuat
file React, dan jangan mengubah route sampai pengguna memberi persetujuan
untuk tahap implementasi.
```

## Prompt tahap implementasi setelah audit disetujui

```text
[MONBIS-UI] [MIGRASI:PHP-REACT] [MODULE:KPI] [PAGE:setting-kpi-jabatan]
[MODE:IMPLEMENT]

Audit Setting KPI Jabatan sudah disetujui. Implementasikan hanya berdasarkan
temuan audit yang sudah disepakati.

Aturan wajib:

- semua perubahan berada di `kpi-tukin/`;
- gunakan `kpi-tukin/src/features/setting-kpi-jabatan/`;
- jangan mengimpor file dari `frontend/src/`;
- pastikan app dapat dibuild dan dipindahkan sebagai satu folder `kpi-tukin/`;
- route implementasi harus `/kpi/setting_kpi_jabatan`;
- API base harus memakai resolver `kpi-tukin/src/lib/api-base.ts`;
- pertahankan API, payload, rumus, permission, dan auth page lama;
- gunakan adapter API dan auth terpusat di aplikasi KPI;
- jangan mengubah controller atau query kecuali ada keputusan backend terpisah;
- gunakan komponen resmi shadcn/ui;
- buat page orchestrator di bawah 500 baris;
- implementasikan loading, empty, error, retry, saving, disabled, dan success;
- cegah response lama menimpa filter terbaru;
- pertahankan filter, search, format angka, edit bobot, edit target, parameter
  skor, save satu baris, save semua, dan reload behavior;
- validasi angka di boundary sebelum dikirim ke API;
- tampilkan error dari API tanpa menutupi kegagalan;
- jaga sticky header dan mobile overflow;
- dukung dark mode, keyboard, Escape, focus, dan aria label;
- jangan menampilkan fitur yang tidak ada di page lama.

Kerjakan berurutan:

1. types dan API adapter;
2. page shell dan route;
3. filter dan table read-only;
4. input edit dan validasi;
5. save satu baris dan save semua;
6. info modal dan state feedback;
7. responsive dan dark mode;
8. parity review dengan PHP lama.

Setelah selesai jalankan `npm run lint` dan `npm run build` dari
`kpi-tukin/`. Laporkan file yang berubah, hasil parity, hasil validasi,
ukuran dependency lintas folder, dan langkah rollback ke PHP lama.
```

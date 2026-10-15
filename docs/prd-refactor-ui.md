# PRD - MONBIS Modern UI

> **Arsip referensi.** Spesifikasi aktif untuk modernisasi page laporan kredit
> sekarang berada di [prd-modernisasi-4-laporan-kredit.md](prd-modernisasi-4-laporan-kredit.md).
> Gunakan dokumen tersebut sebagai source of truth agar keputusan UI tidak
> terpecah di dua PRD.
> Untuk request baru, gunakan tag utama `[MONBIS-UI]`.

**Status:** Archived - lihat PRD aktif
**Tanggal:** 12 Oktober 2026
**Branch:** `monbis-view`
**Owner:** FE MONBIS

## 1. Ringkasan

MONBIS akan memiliki tampilan modern, tenang, konsisten, dan berorientasi data
dengan kualitas visual yang mendekati produk Vercel: tipografi jelas, spacing
terukur, warna netral, border halus, dan interaksi yang terasa cepat.

Perombakan dilakukan bertahap menggunakan React, Vite, TypeScript, dan
shadcn/ui. Backend PHP, endpoint API, query, database, autentikasi, serta
aturan bisnis tetap menjadi sumber data utama.

Target utama bukan menyalin tampilan Vercel, tetapi mengambil prinsipnya:
minimal, presisi, tidak ramai, dan setiap elemen memiliki tujuan.

## 2. Masalah yang Diselesaikan

- Tampilan antar halaman belum konsisten.
- Banyak class dan pola UI yang dibuat ulang dengan variasi berbeda.
- Tabel, filter, modal, header, dan tombol belum memiliki standar tunggal.
- Tampilan mobile sering menyisakan ruang kosong atau mengalami overflow.
- Sebagian UI terlihat terlalu ramai, terlalu berwarna, atau terasa seperti
  hasil generator AI.
- Perubahan UI berisiko mengganggu API dan logic halaman lama.

## 3. Tujuan Produk

### Tujuan utama

1. Membuat visual MONBIS modern, konsisten, dan nyaman dipakai setiap hari.
2. Menjadikan shadcn/ui sebagai sumber component UI reusable.
3. Mempertahankan PHP dan API lama selama fase migrasi.
4. Memungkinkan rollback per halaman tanpa mengganggu sistem berjalan.
5. Membuat table laporan tetap padat, terbaca, dan responsif di semua device.

### Indikator keberhasilan

- Halaman pilot memiliki satu pola konsisten untuk layout, header, filter,
  table, modal, loading, empty state, dan error state.
- Tidak ada perubahan pada response API atau perhitungan bisnis.
- Fitur filter, detail modal, export, permission, dan navigasi tetap bekerja.
- Tampilan lolos pemeriksaan viewport 375px, 768px, dan 1280px.
- Component dasar tidak dibuat ulang dengan API yang berbeda.
- Halaman baru dapat dikembalikan ke tampilan lama dengan feature flag.

## 4. Batasan dan Non-goals

### Tetap dipertahankan

- `api/`, controller, query SQL, database, dan format response API.
- Autentikasi SSO, JWT, permission, dan router PHP.
- URL dan perilaku bisnis halaman lama.
- Export dan proses perhitungan yang sudah berjalan.

### Tidak dilakukan pada fase pertama

- Migrasi ke Laravel.
- Penggunaan Inertia.
- Rewrite query atau perubahan schema database.
- Penggantian seluruh backend.
- Dark mode sebagai prioritas awal.
- Refactor file arsip seperti `copy`, `v1`, `lama`, atau `ori`.

## 5. Keputusan Teknologi

### Frontend

- React
- TypeScript
- Vite untuk development dan build
- shadcn/ui untuk component UI
- Tailwind CSS v4
- Lucide untuk icon
- Geist sebagai font utama

Node/Vite hanya diperlukan saat development atau build. Production tetap dapat
menggunakan PHP Project dan Nginx/Apache untuk menyajikan hasil `dist/`.

### Backend

- PHP native tetap menjadi backend utama.
- React mengambil data dari endpoint API yang sudah tersedia.
- Tidak ada pemindahan logic bisnis ke component UI.

### Inertia

Inertia tidak digunakan pada fase awal. Inertia baru dipertimbangkan apabila
backend nantinya dimigrasikan ke Laravel atau adapter PHP yang stabil sudah
ditetapkan.

## 6. Arah Visual MONBIS

### Prinsip visual

- Netral dan bersih sebagai default.
- Satu warna aksen utama untuk action dan focus.
- Warna status hanya digunakan untuk makna bisnis.
- Border tipis lebih diutamakan daripada shadow berat.
- Radius konsisten, tidak semua elemen dibuat pill.
- Spacing menggunakan skala yang konsisten.
- Typography memiliki hierarchy yang jelas.
- Table boleh padat, tetapi tidak boleh sesak atau terpotong tanpa alasan.
- Mobile menjadi bagian dari desain awal, bukan perbaikan terakhir.

### Aturan anti-slop

- Tidak menggunakan gradient dekoratif tanpa fungsi.
- Tidak menggunakan sparkle, emoji, fake terminal, atau badge berlebihan.
- Tidak membuat setiap card atau kolom memakai warna berbeda.
- Tidak menambahkan angka, status, atau copy yang tidak berasal dari data nyata.
- Tidak memakai heading generik seperti "Unlock your potential".
- Tidak membuat nested card berlapis-lapis tanpa kebutuhan informasi.
- Tidak menambahkan animasi hanya agar halaman terlihat hidup.
- Setiap icon harus memiliki makna atau membantu menemukan action.
- Copy harus singkat, spesifik, dan memakai istilah bisnis MONBIS.
- Komponen visual harus memiliki state normal, hover, focus, loading, empty,
  error, dan disabled jika relevan.

## 7. Aturan Component

### `components/ui`

Berisi component dasar dari shadcn/ui. Component hanya ditambahkan jika
dibutuhkan oleh halaman dan dipakai ulang setelah tersedia.

Contoh:

```text
button
card
dialog
dropdown-menu
input
select
sheet
skeleton
table
tooltip
```

### `components/app`

Berisi komposisi khusus MONBIS yang menggabungkan component dasar tanpa
menduplikasi fungsi component UI.

Contoh:

```text
AppShell
PageHeader
FilterBar
ReportTable
MetricCard
LoadingState
EmptyState
```

Aturan penting:

- Jangan membuat `Button2`, `CustomButton`, atau `AnotherDialog` untuk fungsi
  yang sama.
- Variasi baru harus menjadi variant atau composition yang jelas.
- Component domain boleh dibuat jika memiliki aturan bisnis atau layout yang
  benar-benar khusus.
- Nama, props, dan perilaku component harus terdokumentasi singkat.

### `features/<page>` sebagai module page

File page tidak boleh menjadi tempat seluruh JSX, API, type, formatter, dan
modal sekaligus. Gunakan folder feature dengan satu orchestrator yang tipis:

```text
features/<page>/
├── <page>-page.tsx              # state, lifecycle fetch, handler, composition
├── <page>-types.ts              # type dan konfigurasi kolom
├── <page>-data.ts               # request API, formatter, sorting, mapper
├── <page>-filter-panel.tsx      # filter dan input page
├── <page>-table.tsx             # table dan cell domain
└── <page>-modals.tsx            # summary/detail dialog page
```

Kontraknya:

- Target file `*-page.tsx` maksimal 500 baris; jika melewati batas itu,
  lakukan split berdasarkan boundary tanggung jawab sebelum menambah fitur.
- `*-page.tsx` mengatur state dan orkestrasi, bukan menampung markup seluruh
  page.
- Component presentasional menerima data, status, dan callback lewat props;
  logic fetch tetap di orchestrator atau module data.
- Type, formatter, dan definisi kolom memiliki satu sumber; jangan disalin ke
  modal, table, atau page lain.
- Pecah component karena boundary visual/state/domain, bukan sekadar membuat
  file kecil tanpa reuse atau tanggung jawab jelas.
- `components/ui` tetap menjadi sumber primitive shadcn/ui. Feature component
  hanya composition khusus page dan tidak membuat Button/Dialog/Table tandingan.

`realisasi-kredit-growth` adalah contoh canonical pertama untuk arsitektur ini.

## 8. Arsitektur dan Integrasi

```text
PHP Router
    |
    +-- halaman legacy PHP
    |
    +-- halaman modern React (feature flag)
             |
             +-- frontend/dist
             +-- fetch API PHP lama
```

Strategi integrasi:

1. Frontend baru dikembangkan secara terisolasi di folder `frontend/`.
2. API lama digunakan tanpa mengubah response.
3. Halaman pilot diaktifkan melalui route atau feature flag.
4. Tampilan lama tetap menjadi fallback.
5. Setelah parity tercapai, halaman baru dapat menjadi default.
6. Halaman lama baru dihapus setelah beberapa siklus pemakaian dan review.

## 9. Phase 0: Shell Navigasi

Sidebar dan navbar dikerjakan lebih dulu sebagai fondasi bersama. Tidak ada
migrasi tabel atau query pada phase ini.

### Scope

- Sidebar desktop dengan kelompok menu yang mengikuti halaman PHP yang benar-benar tersedia.
- Sidebar desktop dapat diciutkan tanpa menghilangkan akses keyboard atau label item.
- Drawer navigasi mobile dengan backdrop, tombol tutup, dan dukungan tombol Escape.
- Navbar dengan konteks halaman aktif dan link nyata ke halaman lama.
- State aktif, focus-visible, hover, dan ukuran target sentuh minimum 44px.
- Satu design token dan satu spacing scale untuk halaman modern berikutnya.

### Acceptance criteria shell

- [ ] Semua link sidebar memiliki halaman tujuan yang nyata.
- [ ] Sidebar desktop dapat collapse dan expand.
- [ ] Navigasi mobile dapat dibuka, ditutup melalui tombol, backdrop, dan Escape.
- [ ] Active state mengikuti halaman yang sedang dipreview.
- [ ] Tidak ada horizontal overflow pada lebar mobile.
- [ ] Sidebar dan navbar tidak menambahkan statistik, badge, atau menu fiktif.
- [ ] Build, lint, dan source inspection berhasil.
- [ ] Visual click-through dilakukan ketika browser preview tersedia.

## 10. Halaman Pilot

### Pilot pertama setelah shell: `realisasi_kredit_growth`

Dipilih karena mewakili kebutuhan utama MONBIS:

- header halaman;
- filter tanggal dan area;
- table data;
- sticky/freeze column;
- loading, empty, dan error state;
- detail modal;
- export Excel;
- tampilan mobile.

### Pilot berikutnya

1. `jatuh_tempo.php`
2. `migrasi_kolek.php`
3. `dashboard.php`
4. halaman laporan pemasaran lainnya.

Dashboard tidak menjadi pilot pertama karena memiliki banyak widget dan
ketergantungan visual sehingga lebih baik dikerjakan setelah fondasi terbukti.

## 11. Workflow Migrasi Per Halaman

Setiap halaman mengikuti urutan berikut:

1. Inventaris perilaku lama: route, permission, API, filter, modal, export,
   dan state.
2. Buat mapping data dari response API ke props/type frontend.
3. Bangun layout menggunakan component yang sudah ada.
4. Pecah page ke feature module sebelum file menjadi monolitik; pertahankan
   endpoint dan payload lama.
5. Implementasikan state loading, empty, error, dan disabled.
6. Uji parity dengan halaman lama.
7. Uji viewport 375px, 768px, 1280px.
8. Review visual dan anti-slop.
9. Aktifkan feature flag untuk user terbatas.
10. Commit per halaman.

## 12. Kebutuhan UX Minimum

### Layout

- Page header memiliki judul, deskripsi singkat, dan action utama.
- Filter berada di area yang mudah ditemukan.
- Action penting tidak tersembunyi di area yang sulit dijangkau mobile.
- Konten memiliki max-width dan spacing yang konsisten.

### Table

- Header jelas dan sticky jika table panjang.
- Kolom angka rata kanan atau center sesuai jenis data.
- Nama dan label rata kiri.
- Freeze hanya untuk kolom yang benar-benar membantu orientasi.
- Tidak memaksa seluruh table mengecil sampai angka tidak terbaca.
- Mobile menggunakan horizontal scroll terkontrol atau layout kartu untuk
  detail tertentu.

### Modal dan filter

- Modal memiliki focus state dan tombol tutup yang jelas.
- Modal tidak menutup data di belakang secara tidak sengaja.
- Filter mempertahankan nilai saat refresh atau reload data.
- Loading tidak menggantung setelah response selesai.

### Accessibility

- Button memiliki label atau `aria-label`.
- Focus keyboard terlihat.
- Kontras warna memadai.
- Icon dekoratif diberi `aria-hidden`.
- Table memiliki struktur header yang benar.

## 13. QA dan Acceptance Criteria

Sebuah halaman dianggap selesai jika:

- [ ] Data dan perhitungan sama dengan halaman lama.
- [ ] Semua API request tetap berhasil.
- [ ] Filter dan reset bekerja.
- [ ] Modal detail bekerja dan sesuai data yang diklik.
- [ ] Export tetap menghasilkan format yang benar.
- [ ] Loading tidak macet dan error dapat dipahami.
- [ ] Empty state tersedia.
- [ ] Permission dan auth tetap berjalan.
- [ ] Tidak ada console error.
- [ ] Tidak ada overflow horizontal yang tidak disengaja.
- [ ] Lolos viewport 375px, 768px, dan 1280px.
- [ ] Tidak ada component dasar duplikat.
- [ ] File orchestrator page tidak menjadi monolitik dan mengikuti feature
  module boundary.
- [ ] Build dan lint frontend berhasil.

## 14. Risiko dan Mitigasi

| Risiko | Mitigasi |
|---|---|
| React dan PHP berjalan dengan route berbeda | Mulai dari feature flag dan pilot terisolasi |
| Data UI berbeda dari halaman lama | Gunakan API lama dan lakukan parity check |
| Bundle frontend terlalu besar | Code splitting per halaman dan lazy loading |
| Table besar lambat | Pagination/server-side filter, jangan render semua data sekaligus |
| Component menjadi terlalu banyak variasi | Review `components/ui` dan `components/app` sebelum menambah file |
| Vite/Node tidak tersedia di server | Build di lokal/CI, deploy hasil `dist/` |
| UI terlihat monoton atau terlalu generik | Gunakan design token MONBIS dan checklist anti-slop |
| Migrasi terlalu luas | Satu halaman, satu commit, satu rollback point |

## 15. Definition of Done Global

Refactor UI dianggap selesai jika:

- Semua halaman aktif memiliki layout dan design token yang konsisten.
- Component dasar menggunakan sumber yang sama.
- Tidak ada halaman penting yang memakai pola modal, filter, atau table berbeda
  tanpa alasan bisnis.
- API dan logic bisnis tidak berubah tanpa PR terpisah.
- Legacy UI dapat dihapus dengan aman atau dipertahankan sebagai fallback.
- Build production dapat dijalankan di luar server PHP.
- Pengguna pusat, korwil, cabang, dan AO dapat menggunakan halaman pada device
  yang relevan.

## 16. Keputusan yang Masih Terbuka

- Apakah production akan mengambil `dist/` dari CI atau dari proses build manual?
- Route modern akan memakai entrypoint PHP khusus atau satu shell React?
- Apakah perlu React Router pada fase pertama?
- Apakah font Geist akan menjadi standar penuh atau hanya untuk frontend baru?
- Kapan halaman lama boleh dihapus setelah halaman modern aktif?

## 17. Referensi

- shadcn/ui: https://ui.shadcn.com/docs
- Anti-slop: https://github.com/miqdadbadjuber/anti-slop
- Inertia, untuk pertimbangan fase lanjutan: https://inertiajs.com/
- Dokumen draft sebelumnya: `rencana-refactor-ui.md`

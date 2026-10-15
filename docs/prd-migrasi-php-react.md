# PRD - Migrasi Frontend PHP Native ke React MONBIS

**Status:** Active working spec
**Tanggal:** 15 Oktober 2026
**Pemilik:** FE MONBIS
**Scope utama:** `kpi-tukin/` untuk aplikasi KPI Tukin dan REST API MONBIS
**Scope pendukung:** `frontend/src/` untuk page React laporan yang sudah ada

Dokumen ini adalah kontrak kerja untuk memindahkan page PHP native ke React.
Dokumen ini mengatur arsitektur, migrasi perilaku, integrasi REST API,
deployment, responsive UI, dan pemeriksaan parity. Untuk visual laporan kredit,
gunakan [prd-modernisasi-4-laporan-kredit.md](prd-modernisasi-4-laporan-kredit.md).

## 1. Tujuan

MONBIS memiliki frontend React yang dapat dibangun dan dipindahkan tanpa membawa
logic tampilan PHP. React mengambil data dari REST API MONBIS, sedangkan PHP
tetap menjadi fallback selama parity belum disetujui.

KPI Tukin adalah aplikasi React mandiri di `kpi-tukin/`. Aplikasi ini hanya
menangani menu KPI AO Kredit, AO Dana, dan AO Remedial. Aplikasi tidak boleh
bergantung pada source code report di `frontend/src/`, sehingga migrasi ke repo
SIMPEG atau repo lain cukup memindahkan folder `kpi-tukin/`.

Hasil yang diharapkan:

- URL React bersih dan konsisten;
- perilaku bisnis, permission, rumus, dan kontrak API tetap sama;
- shell, typography, warna, modal, table, dan state konsisten;
- satu feature dapat diuji dan di-deploy tanpa memindahkan seluruh aplikasi;
- penggantian domain tidak memerlukan perubahan URL API di setiap feature.

## 2. Batasan

### Termasuk

- pemindahan markup dan interaksi frontend PHP ke React;
- komponen resmi shadcn/ui dan Lucide;
- adapter fetch untuk auth, loading, error, empty, retry, dan abort;
- route React bersih dengan fallback URL PHP;
- responsive desktop, tablet, dan mobile;
- filter, tabel, modal, export, sorting, pagination, dan permission yang sudah
  tersedia pada page lama;
- build static KPI ke `kpi-tukin/dist`;
- pemindahan satu folder `kpi-tukin/` ke repo lain.

### Tidak termasuk tanpa keputusan terpisah

- perubahan query, rumus, schema database, atau permission backend;
- pemindahan logic bisnis ke browser;
- penggantian REST API tanpa kontrak baru;
- migrasi seluruh aplikasi sekaligus;
- penggunaan Inertia sebelum backend dan route adapter ditetapkan;
- membuat variasi component UI yang fungsinya sudah tersedia di shadcn/ui.

## 3. Prinsip arsitektur

```text
PHP native lama        -> fallback selama masa migrasi
REST API MONBIS        -> sumber data dan aturan bisnis
React KPI app          -> kpi-tukin/, UI, state, dan interaksi KPI
React report app       -> frontend/src/, page laporan yang sudah ada
kpi-tukin/dist         -> artefak deployment KPI
```

React tidak membaca database, tidak menyalin query dari PHP, dan tidak
menghitung ulang KPI. Semua data berasal dari endpoint API yang sudah ada atau
endpoint baru yang disepakati. MONBIS tetap menjadi core backend untuk sumber
data, rumus, skor, nilai akhir, status, dan faktor Tukin.

### Pembagian tanggung jawab

| Area | Pemilik |
| --- | --- |
| Query, rumus, permission, auth server | PHP/API MONBIS |
| Mapping response, request state, formatter | Feature React |
| Primitive UI KPI | `kpi-tukin/src/components/ui` |
| Shell, sidebar, navbar, theme KPI | `kpi-tukin/src/components/app` |
| Route React KPI | `kpi-tukin/src/App.tsx` dan `kpi-tukin/src/lib/routes.ts` |
| Kontrak migrasi | Dokumen ini dan PRD UI aktif |

## 4. Struktur folder

### 4.1 Aplikasi KPI Tukin yang mandiri

Seluruh menu KPI Tukin berada di bawah `kpi-tukin/`. Package, Vite config,
route map, API resolver, shell, dan primitive UI berada di dalam folder ini.
Tidak boleh ada import dari `frontend/src/`.

```text
kpi-tukin/
├── package.json
├── vite.config.ts
├── index.html
├── public/
└── src/
    ├── components/
    │   ├── app/
    │   └── ui/
    ├── features/
    │   ├── setting-kpi-jabatan/
    │   ├── nilai-kpi-ao/
    │   ├── generate-kpi-ao/
    │   ├── rekap-kpi-ao/
    │   └── report-cabang/
    ├── lib/
    │   ├── api-base.ts
    │   ├── routes.ts
    │   └── utils.ts
    └── App.tsx
```

### 4.2 Aplikasi laporan yang sudah ada

Page laporan yang telah dimigrasikan tetap berada di `frontend/src/`. Struktur
tersebut bukan dependency aplikasi KPI dan tidak boleh diimpor silang.

```text
frontend/src/
├── components/
├── features/
│   ├── realisasi-growth/
│   └── jatuh-tempo/
├── lib/
└── App.tsx
```

### 4.3 Struktur minimum feature

Untuk page dengan tabel, filter, modal, atau action save:

```text
kpi-tukin/src/features/<page>/
├── <page>-page.tsx              # orchestrator state dan composition
├── <page>-types.ts              # type dan kontrak data
├── <page>-data.ts               # API call, mapper, parser, formatter
├── <page>-filter-panel.tsx      # kontrol filter bila diperlukan
├── <page>-table.tsx             # table dan cell domain bila diperlukan
└── <page>-modals.tsx            # dialog info/detail bila diperlukan
```

Aturan file:

- `*-page.tsx` adalah orchestrator dan ditargetkan di bawah 500 baris;
- API call tidak ditulis langsung di komponen presentasional;
- type, parser, dan formatter memiliki satu sumber;
- pecah file berdasarkan boundary visual, state, atau domain;
- jangan membuat `Button2`, `TableV2`, `ModalBaru`, atau wrapper tandingan;
- feature component boleh dibuat jika boundary-nya jelas;
- aplikasi KPI tidak boleh mengimpor file dari `frontend/src/`.

## 5. Kontrak API dan URL

Gunakan resolver terpusat di `kpi-tukin/src/lib/api-base.ts`.
Feature tidak boleh menyimpan URL API mentah.

| Kondisi | URL |
| --- | --- |
| Local React KPI | `http://localhost:5173/kpi/setting_kpi_jabatan` |
| Server React KPI | `https://monbis.bkkjateng.co.id/kpi/setting_kpi_jabatan` |
| API server | `https://monbis.bkkjateng.co.id/api` |
| Dev proxy | `/api`, diteruskan ke PHP API lokal oleh Vite |
| Production KPI | Build KPI memakai `/api` dari domain MONBIS yang sedang dibuka |
| Environment khusus | `VITE_API_BASE_URL` |

Path domain di atas adalah contoh untuk page Setting KPI Jabatan. Page KPI lain
mengikuti route map yang sama dan tetap berada di bawah prefix `/kpi/`.

Ketentuan request:

- gunakan method, payload, dan nama field dari API lama;
- cek HTTP status dan `payload.status`;
- tampilkan `payload.message` sebagai error singkat;
- gunakan `AbortController` dan race protection pada filter atau page berubah;
- response lama tidak boleh menimpa response filter terbaru;
- simpan auth pada adapter terpusat;
- jangan fetch detail sebelum pengguna memintanya.

### 5.1 Peta fetch menu KPI Tukin

Semua request KPI memakai `POST /api/index.php?request=kpi` dengan body JSON.
Frontend hanya memetakan response dan mengirim action yang dipilih pengguna.

| Menu | Fetch utama | Peran backend |
| --- | --- | --- |
| Setting KPI Jabatan | `{ type: "setting" }` | Menyediakan jabatan, indikator, bobot, target, unit, dan parameter skor. |
| Nilai KPI AO | `{ type: "directory" }`, `{ type: "evaluation" }`, `{ type: "detail" }` | Menyediakan direktori AO, nilai periode, dan detail hasil hitung. |
| Generate KPI AO | `{ type: "directory" }`, `{ type: "calculate" }` | Menjalankan perhitungan dan menyimpan hasil di backend. |
| Rekap KPI AO | `{ type: "directory" }`, `{ type: "annual" }` | Mengagregasi rekap tahunan, skor, dan faktor Tukin. |
| Report Cabang | `POST /api/kinerja_report/` dengan `{ type: "annual_report" }` | Menghitung report kinerja cabang tahun berjalan. |

Action simpan setting menggunakan `save_indicator`, `save_target_default`, dan
`save_score`; action tersebut tetap melewati permission dan validasi API.
React tidak boleh mengimplementasikan ulang formula `calculate`, agregasi
`annual`, atau rumus report cabang.

### 5.2 Identitas user dan SSO MONBIS

KPI Tukin tidak memiliki halaman login sendiri. Identitas user berasal dari
sesi SSO MONBIS yang sudah dipakai aplikasi internal lain.

- bearer token dibaca dari `window.AUTH_TOKEN`, `localStorage.dpk_token`, atau
  cookie `sso_token` melalui adapter terpusat;
- profil user dibaca dari `localStorage.dpk_user` sebagai fallback cepat lalu
  disegarkan dari endpoint SSO `GET /api/auth/whoami`;
- field minimum yang dapat ditampilkan: `full_name`, `employee_id`,
  `job_position`, `unit_kerja`, `branch_name`, dan `kode`;
- navbar KPI menampilkan profil user aktif dan link kembali ke profil MONBIS;
  tidak boleh menambahkan form login kedua;
- bila endpoint SSO tidak tersedia sementara, UI tetap boleh menampilkan profil
  cache lokal tanpa menghentikan render page KPI;
- logout, refresh token, dan permission tetap menjadi tanggung jawab MONBIS/SSO.

Adapter React untuk aturan ini berada di `kpi-tukin/src/lib/auth.ts` dan tidak
boleh digandakan di feature page.

## 6. Route KPI Tukin

| Menu | PHP lama | Target route React |
| --- | --- | --- |
| Setting KPI Jabatan | `pages/setting_kpi_jabatan.php` | `/kpi/setting_kpi_jabatan` |
| Nilai KPI AO | `pages/hitung_kpi_ao.php` | `/kpi/nilai_kpi_ao` |
| Generate KPI AO | `pages/generate_kpi_ao.php` | `/kpi/generate_kpi_ao` |
| Rekap KPI AO | `pages/rekap_kpi_ao.php` | `/kpi/rekap_kpi_ao` |
| Report Cabang | `pages/raport_cabang.php` | `/kpi/report_cabang` |

Route PHP tetap menjadi fallback sampai feature React selesai dan parity
disetujui. Semua route KPI harus dapat dibuka langsung setelah refresh.

Urutan implementasi:

1. shell KPI dan route map;
2. Setting KPI Jabatan;
3. Nilai KPI AO;
4. Generate KPI AO;
5. Rekap KPI AO;
6. Report Cabang.

## 7. Kontrak UI bersama

Setiap page KPI minimal memiliki:

1. `AppShell` dengan sidebar, navbar, theme toggle, dan route aktif;
2. toolbar page ringkas;
3. filter pada navbar bila filter tersedia;
4. loading, success, empty, error, retry, saving, dan disabled state;
5. action export memakai icon dan label responsif bila tersedia;
6. sticky header dengan background solid pada tabel panjang;
7. modal yang dapat ditutup dengan tombol, backdrop, dan Escape;
8. typography konsisten, angka `tabular-nums`, dan format `id-ID`;
9. light mode dan dark mode yang terbaca;
10. warna hanya untuk status, error, warning, dan keberhasilan.

Semua filter page KPI dirender melalui tombol `Filter` di navbar milik
`KpiShell`, bukan sebagai panel filter terpisah di body page. Modal filter
memakai layout responsive yang sama di setiap page. Perubahan filter tetap
mengubah state feature dan fetch ulang response API tanpa full page reload.

Konfirmasi action yang berdampak pada data, seperti generate ulang, wajib
memakai dialog React yang konsisten. Native `window.alert` dan
`window.confirm` tidak digunakan.

Navigasi antar-menu memakai `history.pushState` dan listener `popstate`. Route
KPI dapat dibuka langsung melalui URL, tetapi klik sidebar harus tetap menjadi
perpindahan SPA yang mempertahankan shell, theme, dan layout.

Navbar KPI selalu memiliki kontrol theme, filter page bila tersedia, dan profil
user SSO. Menu profil memakai dropdown ringan yang bisa ditutup dengan klik di
luar atau Escape; detail user harus berasal dari sesi MONBIS, bukan data dummy.

Untuk mobile:

- judul dan action utama satu baris jika memungkinkan;
- action sekunder dapat menjadi icon-only dengan `aria-label`;
- kolom yang tidak penting boleh disembunyikan;
- scroll horizontal hanya berada di viewport tabel;
- sticky cell memakai background opaque;
- hover tidak menjadi satu-satunya cara membuka detail.

## 8. Aksesibilitas

- button memiliki label terlihat atau `aria-label`;
- icon dekoratif memiliki `aria-hidden`;
- focus keyboard terlihat;
- modal memakai `role="dialog"`, `aria-modal`, dan judul;
- Escape menutup modal atau drawer;
- warna bukan satu-satunya pembeda status;
- target sentuh utama minimal 44px pada mobile;
- table memakai `thead`, `th`, dan `scope` dengan benar.

## 9. Workflow migrasi satu page

### Tahap A - Audit PHP

Catat route, permission, endpoint, method, payload, response, filter default,
table columns, formatter, modal, export, side effect, loading, empty, error,
dan perilaku yang tidak boleh berubah.

### Tahap B - Kontrak page

Tentukan pengguna, data utama, action, route React, fallback PHP, dan acceptance
criteria parity sebelum menulis component.

### Tahap C - Implementasi React

1. pasang route dan shell;
2. buat types dan adapter API;
3. pindahkan filter dan default state;
4. render success state dengan data nyata;
5. tambahkan loading, empty, error, retry, dan abort;
6. tambahkan modal, detail, export, dan permission;
7. terapkan sticky, responsive, dark mode, dan keyboard behavior;
8. hapus dead code hanya setelah parity terbukti.

### Tahap D - Parity dan rollout

Bandingkan PHP lama dan React pada filter, total, detail, export, permission,
dan response API yang sama. Aktifkan route React bertahap. PHP tetap fallback
sampai pengguna menyatakan page baru dapat dipakai.

## 10. Deployment dan pemindahan repo

Development membutuhkan Node/Vite. Production cukup menyajikan hasil build:

```text
cd kpi-tukin
npm run build
../kpi/ -> static root atau document root server
```

Server production tidak perlu menjalankan Node. Pastikan asset dapat diakses,
clean URL mendapat fallback ke `index.html`, API mengizinkan origin frontend,
dan auth cookie atau bearer token tetap sesuai kontrak.

Untuk memindahkan KPI ke repo lain:

1. salin folder `kpi-tukin/`;
2. salin environment yang diperlukan, tanpa hardcode domain di feature;
3. set `VITE_API_BASE_URL` bila domain API berbeda;
4. build dan commit folder `kpi/`, lalu server cukup melakukan `git pull`;
5. uji seluruh route `/kpi/*` dan endpoint API.

## 11. Definition of Done

Page selesai jika parity data dan rumus terverifikasi; permission, auth, filter,
detail, export, dan pagination tetap berfungsi; route React dan fallback PHP
dapat dibuka; feature tidak mengimpor `frontend/src/`; loading tidak menggantung;
empty/error dapat dipahami; tidak ada console error atau lint baru; desktop,
tablet, mobile, dark mode, keyboard, modal, dan focus dapat digunakan; serta
`npm run lint` dan `npm run build` berhasil.

## 12. Decision log

| Tanggal | Keputusan | Dampak |
| --- | --- | --- |
| 2026-10-13 | React menjadi frontend target untuk page KPI dan page baru. | PHP/API tetap menjadi backend dan fallback bertahap. |
| 2026-10-13 | API base URL ditentukan oleh environment dan hostname. | Local dan production dapat memakai build yang sama tanpa URL API di feature. |
| 2026-10-13 | Setiap page memakai folder feature sendiri. | State, data, type, table, dan modal tidak menumpuk dalam satu file. |
| 2026-10-13 | Menu KPI memakai route React dengan link PHP sebagai fallback. | Migrasi dapat dilakukan satu menu per satu menu. |
| 2026-10-15 | KPI Tukin menjadi aplikasi mandiri di `kpi-tukin/`. | Folder dapat dipindah ke repo SIMPEG atau repo lain. |
| 2026-10-15 | Route KPI memakai prefix `/kpi/` dan nama underscore. | Local dan server memakai pola URL yang sama. |
| 2026-10-15 | Semua menu KPI memakai feature React dan filter navbar bersama. | Navigasi sidebar smooth tanpa reload dan pola filter konsisten. |

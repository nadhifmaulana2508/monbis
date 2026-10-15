# Prompt migrasi page PHP native ke React

Salin prompt ini untuk setiap page baru. Ganti semua placeholder `<...>`
sebelum menjalankan pekerjaan.

```text
[MONBIS-UI] [MIGRASI:PHP-REACT] [PAGE:<slug>] [SCOPE:<scope>]

Migrasikan page PHP native berikut ke React:

- File PHP lama: <pages/nama_page.php>
- Route React target: <route-react>
- Folder feature: frontend/src/features/<slug>/
- Fallback lama: <route-php-lama>

Gunakan kontrak utama:

1. docs/prd-migrasi-php-react.md
2. docs/prd-modernisasi-4-laporan-kredit.md untuk aturan visual laporan
3. komponen yang sudah ada di frontend/src/components/ui dan components/app

Sebelum mengubah file:

- audit page PHP, endpoint, method, payload, response, permission, auth,
  filter, default value, table, modal, export, pagination, dan formatter;
- cari feature React yang sudah memiliki pola serupa;
- catat perilaku bisnis yang wajib tetap sama;
- cek perubahan workspace agar tidak menimpa pekerjaan lain.

Aturan implementasi:

- pertahankan endpoint, payload, rumus, permission, dan format bisnis lama;
- gunakan apiEndpoint() dari frontend/src/lib/api-base.ts;
- jangan menulis URL API mentah di component;
- gunakan folder feature dengan page orchestrator, types, data, filter, table,
  dan modal sesuai kebutuhan;
- jaga file page di bawah 500 baris;
- gunakan primitive resmi shadcn/ui dan lucide-react;
- jangan membuat component duplikat dengan fungsi yang sama;
- pindahkan filter ke navbar jika page memiliki filter;
- gunakan loading, empty, error, retry, abort, dan race protection;
- buat sticky header dan sticky identity cell memakai background solid;
- pastikan tabel tidak overflow pada desktop dan tetap dapat digunakan mobile;
- gunakan warna hanya untuk status bisnis, bukan untuk mewarnai semua kolom;
- dukung light mode, dark mode, keyboard, Escape, focus, dan aria label;
- pertahankan export dan detail modal jika tersedia di page lama.

Kontrak output:

- route React dapat dibuka;
- fallback PHP tetap dapat dibuka;
- response data dan total cocok dengan page lama;
- semua filter dan action utama berfungsi;
- tidak ada dead button atau placeholder yang terlihat sebagai fitur selesai;
- npm run lint berhasil tanpa error baru;
- npm run build berhasil;
- laporkan file yang berubah, risiko parity, dan langkah rollback.

Urutan kerja:

1. tampilkan hasil audit singkat;
2. implementasikan feature secara bertahap;
3. jalankan lint, build, dan pemeriksaan route;
4. review responsive dan state loading/error/empty;
5. ringkas hasil dan sisa risiko.
```

## Prompt khusus menu KPI

```text
[MONBIS-UI] [MIGRASI:PHP-REACT] [MODULE:KPI] [PAGE:<kpi-page>]

Pindahkan menu KPI berikut:

- PHP lama: <pages/kpi_page.php>
- Route target: <kpi-route>
- Feature: frontend/src/features/kpi/

Pertahankan seluruh kontrak API KPI, termasuk jabatan, kantor, AO, closing,
nilai, status generate, detail penilaian, dan rekap. Jangan memindahkan rumus
KPI ke frontend. React hanya memetakan response API menjadi UI.

Ikuti PRD migrasi PHP ke React. Gunakan shell KPI yang sama dengan feature
React lain, komponen shadcn/ui yang sudah tersedia, dan API adapter terpusat.
Selesaikan satu menu sampai parity sebelum mengerjakan menu KPI berikutnya.
```

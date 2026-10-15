# MONBIS KPI Tukin

Aplikasi React mandiri untuk seluruh menu KPI Tukin: Setting KPI Jabatan, Nilai
KPI AO, Generate KPI AO, Rekap KPI AO, dan Report Cabang. Semua data,
perhitungan KPI, skor, nilai akhir, dan faktor Tukin berasal dari REST API
MONBIS. Navigasi sidebar berjalan sebagai SPA tanpa full page reload; filter
page berada di navbar bersama.

## Local

```bash
cd kpi-tukin
npm install
npm run dev
```

Buka:

```text
http://localhost:5173/kpi/setting_kpi_jabatan
```

Route lain:

```text
/kpi/nilai_kpi_ao
/kpi/generate_kpi_ao
/kpi/rekap_kpi_ao
/kpi/report_cabang
```

Saat development, source code dan route React tetap berjalan dari folder
`kpi-tukin` lokal. Request `/api` diproxy ke PHP API lokal
`http://localhost/report-dpk/api`, sementara PHP API lokal tetap memakai
database server MONBIS.

Saat build dijalankan di domain MONBIS, aplikasi memakai `/api` dari domain yang
sama. Menu dan halaman MONBIS legacy tetap berada di aplikasi utama dan tidak
diubah oleh aplikasi KPI Tukin.

KPI Tukin tidak memiliki login sendiri. Navbar membaca sesi SSO MONBIS dari
`dpk_user`, `dpk_token`, dan cookie `sso_token`, lalu menyegarkan profil dari
endpoint SSO `whoami` bila token tersedia. Link profil kembali ke halaman profil
MONBIS.

## Production

```bash
npm run build
```

Deploy isi `dist/` pada route `/kpi/` di MONBIS. Resolver API otomatis memakai
`https://monbis.bkkjateng.co.id/api`, atau dapat diganti dengan
`VITE_API_BASE_URL`.

Jangan menambahkan rumus KPI di folder ini. React hanya fetch, memetakan, dan
menampilkan response backend.

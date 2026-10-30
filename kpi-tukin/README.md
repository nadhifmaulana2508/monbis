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
http://localhost:5174/kpi/setting_kpi_jabatan
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

KPI Tukin tidak memiliki login sendiri. Login dan pemeriksaan sesi diteruskan
melalui API PHP MONBIS; token aplikasi dan token sesi dikelola server-side dengan
cookie `HttpOnly`.

## Production

Build satu kali untuk KPI, Monbis V2, dan RBB React dari root repository:

```bash
bash tools/build-react-apps.sh
```

Build menghasilkan folder `../kpi/` dan folder tersebut disajikan sebagai route
`/kpi/` di MONBIS. Output build tidak di-commit; ikuti [panduan build server](../docs/react-server-build.md)
setelah source di-pull. Resolver API otomatis memakai `/api` pada domain MONBIS,
atau dapat diganti dengan `VITE_API_BASE_URL`.

Jangan menambahkan rumus KPI di folder ini. React hanya fetch, memetakan, dan
menampilkan response backend.

# Status dan Panduan Handoff Repo MONBIS

**Terakhir diperbarui:** 17 November 2026
**Tujuan:** memberi AI atau developer berikutnya gambaran arsitektur, sumber kode, alur login, API, database, cara menjalankan dan membangun aplikasi.

> Snapshot konteks, bukan pengganti kode. Branch, port lokal, data, konfigurasi server, dan dokumentasi lama dapat berubah; verifikasi sebelum mengubah atau deploy. Jangan salin nilai rahasia `.env` ke dokumen, log, chat, atau Git.

## Gambaran singkat

MONBIS terdiri dari aplikasi utama PHP dan tiga frontend React terpisah. PHP menangani routing utama, sesi/SSO, API, serta banyak halaman laporan. React memakai API PHP yang sama dan dibangun untuk subpath masing-masing.

```text
Browser
  â”œâ”€ PHP MONBIS: index.php â†’ pages/ + views/ â†’ api/
  â”œâ”€ KPI Tukin React: kpi-tukin/ â†’ output build: kpi/
  â”œâ”€ MONBIS V2 React: monbis-v2/ â†’ output build: v2-react/
  â””â”€ RBB React: rbb-bisnis/ â†’ output build: rbb/
                         â””â”€ API: api/index.php â†’ controllers/ + routes/
```

Baca `AGENTS.md` sebagai aturan kerja. `issue.md` untuk report PHP, `monbis-v2/issue.md` untuk migrasi report MONBIS React V2, dan `issue_monbis_v2.md` untuk frontend PHP `v-2/`; jangan samakan ketiganya.

## Peta folder

| Lokasi | Peran |
|---|---|
| `index.php`, `.htaccess` | Entry point dan routing PHP/SPA. `.htaccess` lokal di-ignore Git; konfigurasi server dapat berbeda. |
| `pages/` | Halaman PHP dan laporan bisnis lama. |
| `views/` | Layout, header/navbar, sidebar, footer dan script bersama. |
| `api/` | Front controller API, endpoint, auth, RBAC, controller, route dan koneksi database. |
| `api-tests/` | Pengujian API yang tersedia. |
| `assets/`, `components/`, `shared/` | Aset, komponen dan utilitas bersama. |
| `kpi-tukin/` | Source React KPI Tukin. |
| `monbis-v2/` | Source React MONBIS V2. |
| `rbb-bisnis/` | Source React RBB Bisnis. |
| `kpi/`, `v2-react/`, `rbb/` | Hasil build; di-ignore Git dan bukan source. Build membersihkan/mengganti output. |
| `v-2/` | Aplikasi/eksperimen PHP terpisah, bukan source React MONBIS V2. |
| `database/`, `docs/` | Referensi database dan dokumentasi; cek tanggal dan kecocokan. |
| `frontend/` | Aset/frontend pendukung; telusuri pemanggil sebelum mengubah. |
| `tools/build-react-apps.sh` | Build ketiga aplikasi React. |
| `.anti-slop/` | Preferensi/konteks lokal, tidak ikut Git. |

`README.md` root berisi konteks lama dan mungkin tidak mencerminkan kondisi saat ini. Utamakan kode aktif, `AGENTS.md`, package scripts, dan konfigurasi aktual.

## Request dan routing PHP

Browser membuka host root atau route seperti `/dashboard`; `.htaccess` meneruskan route aplikasi ke `index.php`, sementara file statis yang ada disajikan langsung. `index.php` membaca konfigurasi SSO/environment dari `api/helpers/sso_config.php`, menentukan route dan memeriksa sesi/token. Halaman PHP dicari di `pages/<route>.php`, lalu dirender dengan layout bersama. `/v2-react/...` dilayani hasil build V2 dengan fallback SPA.

`.htaccess` menolak dotfile selain `.well-known`, membatasi akses langsung ke file PHP internal, mengarahkan API ke front controller, dan memiliki aturan khusus KPI serta subpath SPA. Aturan aaPanel Nginx tidak otomatis sama dengan `.htaccess`; review konfigurasi aktif sebelum menyalin rewrite. Hindari hard-code URL local/dev/prod; gunakan konfigurasi host/environment.

Jika mengubah routing/API, periksa root, URL route langsung, refresh SPA, asset, route tidak dikenal, dan parameter invalid. Perbaiki error PHP dari sumbernya; jangan menutup error dengan redirect/output kosong.

## API

`api/index.php` adalah front controller. Pola request `/api/<endpoint>` (rewrite server dapat meneruskan ke `api/index.php?request=...`). Segmen pertama memilih endpoint; implementasi biasanya di `api/routes/` dan `api/controllers/`. Bentuk respons umum `{status, message, data}`. Endpoint selain auth mewajibkan autentikasi aplikasi.

Endpoint yang terdaftar mencakup auth, users/catalog, asset, hapus_buku, date, flow_par, npl, kredit, kode, anggota, bucket, ckpn, kolek, kunjungan, jt, bucket_fe, rr, pipelane, dashboard, monev, lapkeu, transaksi, prospek/prospek_sync, dev_tracking, rbb/rbb_v2, kinerja_report, pipelane_monitoring_kredit, kpi, paparan_settings, event_theme, chatbot, maping_ao_remedial, dan rbac. Periksa dispatcher/controller untuk kontrak aktual; daftar ini bukan jaminan setiap endpoint aktif di semua deployment.

Query harus menggunakan parameter binding, mengikuti filter tanggal/cabang dan pagination, memakai koneksi database yang benar, dan mengendalikan jumlah query untuk laporan berat.

## Login, sesi, dan RBAC

- Login terhubung ke SSO MONBIS (`apisso.bkkjateng.co.id`) melalui helper `api/helpers/`.
- Cookie sesi aplikasi `monbis_session`; token SSO dapat berupa `sso_token`. Endpoint terkait termasuk `GET /api/auth/whoami` dan `POST /api/auth/logout`.
- Masa idle yang disetel adalah **2 jam** (7.200.000 ms). PHP memuat `assets/js/idle-session.js` dari layout; React memakai `shared/idle-session.ts` dan handler logout app.
- Periksa login, refresh, idle, expired token, dan logout. Jangan mengandalkan tampilan UI saja.
- RBAC: pegawai paling spesifik, lalu jabatan, lalu divisi sebagai fallback. Bedakan akses eksplisit, warisan, dan efektif; setelah menyimpan matrix, reload dari API/database. Uji akses diberi/dicabut.
- Jika timeout/login gagal, cek respons SSO, cookie/domain/path/SameSite, API dan environment. Jangan masukkan token/password ke dokumentasi.

## Database dan konsep data

Konfigurasi dibaca dari `api/.env`; jangan commit atau salin nilainya.

| Koneksi | Nama variabel yang dibaca |
|---|---|
| DPK utama | `DB_HOST`, `DB_PORT`, `DB_ROOT`, `DB_NAME`, `DB_USER`, `DB_PASS` |
| Prospek | `DB_HOST1`, `DB_PORT1`, `DB_ROOT1`, `DB_NAME1`, `DB_USER1`, `DB_PASS1` |
| SIMPEG | `SIMPEG_DB_HOST`, `SIMPEG_DB_PORT`, `SIMPEG_DB_NAME`, `SIMPEG_DB_USER`, `SIMPEG_DB_PASS` |
| SSO/environment | `SSO_API_BASE`, `SSO_APP_CODE`, `SSO_APP_TOKEN`, `SSO_API_CONNECT_IP`, `APP_URL_LOCAL`, `APP_URL_DEV`, `APP_URL_PROD`, `CORS_ALLOWED_ORIGINS` |

Nama fallback di kode tidak berarti semua variabel perlu diisi sekaligus. Lihat `.env.example` dan helper koneksi terkait. Rahasia hanya boleh diatur di environment yang sesuai.

Catatan report kredit: `nominatif` memasok snapshot rekening, cabang, baki debet/saldo bank, kolektibilitas dan status restrukturisasi. `update_realisasi_kredit` dipakai untuk data realisasi/peristiwa kredit; cek nama kolom dan join query aktif sebelum mengubah rumus. `baki_debet` dan `saldo_bank` adalah ukuran berbeda.

Perubahan report yang tercatat mencakup baris `nilai_ppaw(d)` pada breakdown kolektibilitas dan komponen LAR di bawah template tabel `gambaran_kredit_cabang`. Aturan LAR yang diminta: baki debet Kol 2/DPK + Kol 3â€“5/NPL + baki debet Kol L berstatus restrukturisasi dengan tanggal realisasi di `update_realisasi_kredit` dalam tiga bulan kalender terakhir sampai tanggal closing. Contoh closing 31 Oktober: 1 Agustusâ€“31 Oktober. Ini catatan kebutuhan, bukan audit independen hasil produksi; validasi rumus, status, nama kolom dan batas tanggal pada implementasi.

Untuk report, bandingkan total terhadap detail; cek cabang kosong/tunggal/banyak, bulan berbeda, tanggal batas, pagination dan export.

## Aplikasi React

Ketiganya memakai React + TypeScript + Vite dan backend PHP yang sama. Navigasi internal seharusnya tetap di SPA agar pergantian menu tidak reload seluruh dokumen. Route/base path ditentukan per aplikasi; jangan menulis URL localhost ke source produksi.

| App | Source | Local XAMPP | Subpath deployment | Port dev terakhir diketahui |
|---|---|---|---|---|
| KPI Tukin | `kpi-tukin/` | `http://localhost/report-dpk/kpi/` | `/kpi/` | `http://127.0.0.1:5174/` |
| MONBIS V2 | `monbis-v2/` | `http://localhost/report-dpk/v2-react/` | `/v2-react/` | `http://127.0.0.1:5176/` |
| RBB Bisnis | `rbb-bisnis/` | `http://localhost/report-dpk/rbb/` | `/rbb/` | `http://127.0.0.1:5175/` |

Port di atas snapshot 16 November 2026, bukan jaminan server masih hidup. Saat itu 5173 dipakai aplikasi lain. Cek output Vite untuk port sebenarnya.

- Route KPI berada di `kpi-tukin/src/lib/routes.ts` (pengaturan KPI jabatan, nilai KPI AO, generate, rekap, report cabang).
- Route MONBIS V2 di `monbis-v2/src/lib/routes.ts` meliputi realisasi/growth, jatuh tempo, pipeline, NPL, RBB, repayment/recovery, flow PAR, laba rugi. Baca `monbis-v2/issue.md` sebelum migrasi report.
- `rbb-bisnis/` adalah frontend RBB React; telusuri routes/view untuk projection/detail/ABA.
- Dev server Vite memproxy API ke Apache lokal (`/report-dpk/api` pada konfigurasi XAMPP). React memakai sesi/API PHP; tidak punya auth terpisah.
- Saat belum login, API bisa membalas 401 dan aplikasi menampilkan login. 401 bukan berarti server mati.

## Menjalankan di local

**PHP/XAMPP:** hidupkan Apache dan database yang dibutuhkan, buka `http://localhost/report-dpk/`. API lokal berada di `/report-dpk/api/`. Bila login/data gagal, cek konfigurasi lokal, koneksi SSO dan log Apache/PHP; jangan tempel secret ke chat.

**React dev:** masuk ke folder aplikasi, jalankan `npm install` jika dependency belum tersedia, kemudian `npm run dev`. Buka alamat yang dicetak Vite; Apache perlu hidup untuk proxy API. Package script menyediakan proxy dan mode build masing-masing.

## Build tiga React app

Dari root repo, build untuk XAMPP lokal:

```bash
bash tools/build-react-apps.sh --local --skip-install
```

`--local` memakai `build:local` untuk base path XAMPP. `--skip-install` hanya jika dependency sudah tersedia; jika tidak, hilangkan flag itu agar script memasang dependency dari lockfile (`npm ci`). Output: `kpi/`, `v2-react/`, `rbb/`.

Build subpath deployment setelah pull:

```bash
bash tools/build-react-apps.sh
```

Script membangun ketiganya berurutan dengan TypeScript lalu Vite. Jangan jalankan build bersamaan ke target yang sama. Vite `emptyOutDir` membersihkan output app; backup ke luar folder target bila diperlukan. Baca `package.json` dan script sebelum mengubah workflow.

Versi Node minimum yang tercatat: **20.19** atau **22.12**. Dokumentasi tambahan: `docs/react-server-build.md`. Di aaPanel/Linux, jalankan dari root checkout dengan Bash, pastikan Node/dependency/hak tulis/base path/rewrite sesuai host. Uji root app, route langsung/refresh dan asset setelah build. Konfigurasi Apache lokal tidak membuktikan Nginx aaPanel benar.

## Verifikasi yang pernah dilakukan

Snapshot 16 November 2026: root/dev server ketiga app dan entry source Vite merespons HTTP 200; layar login KPI/RBB dirender di browser; API tanpa sesi membalas 401. Dev server terlihat pada 5174 KPI, 5175 RBB, 5176 MONBIS V2. Ulangi karena port lokal sementara.

Percobaan `oxlint` gagal karena alokasi memori WebAssembly (`memory allocation of 1280 bytes failed`), bukan temuan lint source. Jangan menyatakan lint lolos. Untuk perubahan PHP jalankan `php -l`; React gunakan package script; UI periksa viewport lebar/sempit sesuai area yang berubah.

## Git dan deployment (snapshot)

Status terakhir sebelum dokumen ini dibuat: branch lokal `develop` bersih dan sinkron dengan `origin/develop` pada `c24b36f` (`feat(auth): add idle logout to apps`). `origin/prod` pada `383b7de`; saat itu `develop` 3 commit di depan `prod`, dan prod adalah ancestor. Commit relevan:

- `776a179` â€” `feat(report): add LAR breakdown`
- `11f4c7a` â€” `fix(v2): keep navigation within React app`
- `c24b36f` â€” `feat(auth): add idle logout to apps`

Status awal dokumen ini dibuat pada `develop`; sebelum dipush, remote sudah di-fetch dan `develop` tercatat sinkron dengan `origin/develop` (0 commit ahead/behind), HEAD `c24b36f`. Kondisi ini bisa berubah setelah snapshot; AI berikutnya harus menjalankan `git status --short --branch`, `git log --oneline --decorate -8`, dan membandingkan remote sesuai kebutuhan.

URL deployment yang diketahui: local `http://localhost/report-dpk/`, dev `https://dev.monbis.bkkjateng.co.id/`, production `https://monbis.bkkjateng.co.id/`. Sebelum push, ringkas file/commit; kecualikan `.env`, credential, `.anti-slop/`, `node_modules/`, output build yang di-ignore. `rbb/` dan `rbb-bisnis/` tidak ikut kecuali diminta. Setelah push verifikasi branch/commit remote. Cek konfigurasi aktif dan rollback sebelum deploy.

## Checklist AI berikutnya

1. Baca `AGENTS.md` dan dokumen ini; untuk UI baca `.anti-slop/antislop.md`, tentukan mode antislop sesuai instruksi sesi/preferences.
2. Cek `git status` agar perubahan user tidak tertimpa; baca issue file aplikasi yang relevan.
3. Telusuri pemanggil, controller/query dan source asli sebelum mengedit; jangan edit generated output.
4. Jangan tebak tabel/kolom atau campur koneksi DB; jangan keluarkan credential.
5. Jalankan verifikasi sesuai area dan cek diff/status. Jangan menambah/menjalankan test kecuali diminta atau diwajibkan aturan project.
6. Jangan commit/push kecuali diminta; laporkan file, alasan, verifikasi dan yang belum terverifikasi.

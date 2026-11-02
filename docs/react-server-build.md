# Build dan test React

## Test Monbis V2 di lokal

Di Windows, buka Git Bash lalu build V2 dengan konfigurasi XAMPP:

```bash
cd /c/xampp/htdocs/report-dpk/monbis-v2
npm ci
npm run build:local
```

Build dibuat ke folder utama project `v2-react/`. Buka:

```text
http://localhost/report-dpk/v2-react/
```

Build lokal ini hanya untuk testing. Tidak perlu menyalin folder hasilnya ke
server.

## Build ulang di server setelah pull

Setelah source sudah di-push dan di-pull ke server, jalankan dari document root
project. Untuk dev:

```bash
cd /www/wwwroot/dev.monbis.bkkjateng.co.id
git pull --ff-only origin develop
bash tools/build-react-apps.sh
```

Untuk production, gunakan folder checkout production dan ganti branch menjadi
`prod`:

```bash
cd /www/wwwroot/monbis.bkkjateng.co.id
git pull --ff-only origin prod
bash tools/build-react-apps.sh
```

Script memasang dependency dari lockfile lalu membangun KPI Tukin, Monbis V2,
dan RBB React. Server membutuhkan Node.js 20.19+ atau 22.12+. Bila dependency
sudah tersedia dan lockfile tidak berubah, tambahkan `--skip-install`:

```bash
bash tools/build-react-apps.sh --skip-install
```

Hasil build server dan URL-nya:

| Aplikasi | Folder hasil build | URL |
| --- | --- | --- |
| KPI Tukin | `kpi/` | `/kpi/` |
| Monbis V2 React | `v2-react/` | `/v2-react/` |
| RBB React | `rbb/` | `/rbb/` |

V2 menghasilkan asset langsung di folder yang sama dengan path `/v2-react/`,
jadi tidak perlu aturan Nginx `alias` khusus. Aturan umum website tetap harus
meneruskan route React yang bukan file ke `index.php`.

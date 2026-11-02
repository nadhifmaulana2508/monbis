# Build dan test React

## Build dan test ketiga app di lokal

Di Windows, buka Git Bash lalu jalankan dari root repo. Perintah ini memasang
dependency dan membangun KPI Tukin, Monbis V2, serta RBB dengan path XAMPP:

```bash
cd /c/xampp/htdocs/report-dpk
bash tools/build-react-apps.sh --local
```

Hasil build masuk ke folder `kpi/`, `v2-react/`, dan `rbb/` di root repo. Buka
aplikasi lokal melalui:

```text
http://localhost/report-dpk/kpi/
http://localhost/report-dpk/v2-react/
http://localhost/report-dpk/rbb/
```

Build lokal hanya untuk testing; server tetap build ulang dari source setelah
pull. Jika dependency sudah terpasang dan lockfile tidak berubah, tambahkan
`--skip-install` agar lebih cepat:

```bash
bash tools/build-react-apps.sh --local --skip-install
```

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

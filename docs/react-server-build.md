# Build React di server

Build KPI Tukin, Monbis V2, dan RBB React dari source dengan satu perintah:

```bash
cd /www/wwwroot/dev.monbis.bkkjateng.co.id
bash tools/build-react-apps.sh
```

Sesuaikan direktori `cd` dengan lokasi checkout di server. Script akan memasang
dependency memakai `npm ci` dari lockfile, lalu menjalankan `npm run build` pada
masing-masing aplikasi. Build menghasilkan:

- KPI Tukin di `kpi/`, untuk route `/kpi/`.
- Monbis V2 di `monbis-v2/dist/`, untuk route `/v2-react/`.
- RBB React di `rbb/`, untuk route `/rbb/`.

Server perlu Node.js 20.19 atau lebih baru pada seri 20, atau 22.12 ke atas, serta
akses ke npm registry saat dependency dipasang. Untuk build ulang ketika dependency
sudah tersedia, gunakan:

```bash
bash tools/build-react-apps.sh --skip-install
```

Jalankan script setelah `git pull` selesai. Output build dan `node_modules` tidak
perlu di-commit; server membuatnya dari source dan lockfile.

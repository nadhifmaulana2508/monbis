# Issue MONBIS V2

Dokumen ini menjadi acuan bersama saat membuat tab atau modul baru di MONBIS V2.
Tujuannya supaya setiap halaman baru mempunyai arah, struktur, pola API, dan perilaku
yang konsisten tanpa menambah kerumitan ke halaman lama.

Status: acuan development aktif  
Tanggal dibuat: 28 September 2026

## 1. Visi

MONBIS V2 adalah workspace frontend yang ringan, rapi, responsif, dan konsisten untuk
memakai data bisnis dari API MONBIS.

Setiap menu harus terasa sebagai bagian dari satu aplikasi yang sama, baik dibuka dari
localhost maupun setelah folder V2 dipindahkan ke server. Frontend bertugas menampilkan,
memfilter, dan mengirim aksi pengguna; aturan bisnis dan query data tetap berada di
backend/API.

## 2. Misi

- Membuat tab baru dengan pola tampilan dan interaksi yang sama.
- Menggunakan endpoint/API yang sudah tersedia sebelum membuat endpoint baru.
- Menghindari duplikasi query, perhitungan, CSS, ikon, dan komponen.
- Menjaga halaman tetap cepat dengan loading, empty state, dan error state yang jelas.
- Memastikan tampilan nyaman di desktop, tablet, dan mobile.
- Membuat deployment AaPanel cukup dengan memindahkan frontend dan memastikan URL API
  tetap benar.
- Menyediakan checklist dan kontrak yang mudah diikuti oleh pengembangan berikutnya.

## 3. Prinsip utama

### 3.1 Frontend dan backend mempunyai batas yang jelas

- PHP view V2 tidak berisi query SQL bisnis.
- JavaScript V2 tidak menghitung ulang aturan bisnis yang seharusnya menjadi tanggung
  jawab API.
- Frontend hanya mengirim parameter filter/aksi, menerima JSON, lalu merender hasil.
- Gunakan response standar:

```json
{
  "status": 200,
  "message": "Data berhasil dimuat",
  "data": {}
}
```

- Error API harus ditampilkan sebagai pesan yang mudah dipahami, bukan raw warning PHP.

### 3.2 Satu tab satu tanggung jawab

Satu tab harus fokus pada satu pekerjaan utama, misalnya:

- `summary`: melihat rekap.
- `calculate`: menghitung satu data terpilih.
- `generate`: memproses data secara massal.
- `setting`: mengelola parameter.

Jika satu file sudah terlalu panjang atau memiliki banyak alur yang tidak berkaitan,
pecah menjadi halaman/module baru. Jangan terus menambahkan kondisi ke file besar hanya
karena route-nya masih berdekatan.

### 3.3 Komponen digunakan kembali

Gunakan komponen V2 yang sudah ada:

- layout dan shell halaman dari `v-2/components/layout.php`;
- filter drawer dari `v-2/components/ui.php`;
- ikon dari `v-2/components/icons.php`;
- search/filter/table/button yang sudah tersedia di `v-2/assets/css/components.css`;
- fungsi bersama dari `v-2/assets/js/components.js`.

Sebelum membuat class CSS atau ikon baru, cari dulu apakah komponen serupa sudah ada.
CSS khusus tab harus memakai prefix, misalnya `.v2-kpi-*`, `.v2-rbb-*`, atau `.v2-mob-*`.

### 3.4 V2 adalah frontend modular, API tetap terpusat di Monbis

Ini adalah keputusan arsitektur utama:

```text
FE KPI /v-2 atau /kpi  ─┐
FE RBB /v-2 atau /rbb  ─┼──> API Monbis /api
FE modul lain          ─┘
```

- Folder V2 berisi frontend, komponen UI, routing halaman, dan adapter JavaScript.
- API, controller, query database, autentikasi, dan aturan bisnis tetap berada di
  Monbis/backend.
- Folder KPI dan RBB boleh dipisah saat deployment tanpa menyalin controller API.
- Semua frontend harus menggunakan konfigurasi URL API, bukan URL yang ditulis ulang
  di setiap halaman.
- Pemisahan folder tidak boleh mengubah kontrak request/response API.
- Jika API tetap satu domain, gunakan same-origin/session yang sudah disediakan Monbis.
  Jika frontend berbeda domain, konfigurasi CORS dan autentikasi harus disiapkan di
  backend; jangan menyimpan token permanen di JavaScript.

Contoh deployment yang didukung:

```text
Local FE  : http://localhost/report-dpk/v-2/kpi/summary
Local API : http://localhost/report-dpk/api/index.php?request=kpi

Server FE : https://monbis.bkkjateng.co.id/kpi/summary
Server API: https://monbis.bkkjateng.co.id/api/index.php?request=kpi
```

Contoh konfigurasi FE yang dipisah dari folder utama:

```php
return [
    'module' => 'kpi',
    'backend_base_url' => 'https://monbis.bkkjateng.co.id',
    'api_base_url' => 'https://monbis.bkkjateng.co.id/api',
    'auth_base_url' => 'https://monbis.bkkjateng.co.id',
];
```

Atau gunakan environment server:

```text
V2_MODULE=kpi
V2_BACKEND_BASE_URL=https://monbis.bkkjateng.co.id
V2_API_BASE_URL=https://monbis.bkkjateng.co.id/api
V2_AUTH_BASE_URL=https://monbis.bkkjateng.co.id
```

Untuk RBB, cukup ubah `V2_MODULE` dan route FE-nya. API tetap memakai endpoint Monbis
yang sama, misalnya `/api/rbb_v2/`.

## 4. Struktur folder yang disepakati

```text
v-2/
  index.php                    # router frontend V2
  components/
    layout.php                 # layout utama
    ui.php                     # filter, modal, button, helper UI
    icons.php                  # icon system
  pages/
    <modul>.php                # view dan adapter JS modul
  assets/
    css/
      app.css
      tokens.css
      components.css
    js/
      components.js

api/
  index.php                    # API entry point
  routes/<modul>.php           # dispatch type request
  controllers/<Modul>Controller.php
```

Pola URL V2:

```text
/v-2/<modul>/<tab>
```

Contoh:

```text
/v-2/kpi/summary
/v-2/kpi/calculate
/v-2/rbb/projection
```

Jika folder frontend dipisah, route boleh menjadi:

```text
/kpi/summary
/kpi/calculate
/rbb/projection
```

Yang berubah hanya base URL frontend dan konfigurasi runtime. Endpoint API tidak ikut
dipindahkan ke dalam folder frontend.

## 5. Pola pembuatan tab baru

### Langkah A - Tentukan kontrak tab

Sebelum coding, tulis singkat:

```text
Nama tab       :
Tujuan         :
Pengguna       :
Filter         :
Endpoint       :
Aksi utama     :
Output         : tabel / kartu / form / grafik
Mobile         : perilaku scroll dan layout
```

### Langkah B - Tentukan sumber data

1. Cari endpoint yang sudah ada.
2. Cek request dan response dengan `Invoke-RestMethod` atau browser DevTools.
3. Jika response belum cukup, tambahkan field di controller API.
4. Jangan membuat data dummy di frontend untuk menutupi API yang belum siap.
5. Pisahkan endpoint master/filter dari endpoint detail jika datanya besar.

Pola request yang disarankan:

```javascript
const post = async (body) => {
  const response = await fetch(API, {
    method: 'POST',
    credentials: 'include',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(body)
  });
  const json = await response.json().catch(() => ({}));
  if (!response.ok || Number(json.status) !== 200) {
    throw new Error(json.message || 'Request gagal');
  }
  return json.data || {};
};
```

### Langkah C - Buat view yang tipis

View hanya bertanggung jawab untuk:

- memilih tab berdasarkan URL;
- membuat shell/layout;
- membuat elemen filter dan target render;
- memuat JavaScript adapter yang diperlukan.

Jangan menaruh query SQL, perhitungan bisnis panjang, atau style besar langsung di view.

### Langkah D - Atur dependency filter

Filter yang bergantung pada filter lain harus dikosongkan dan dinonaktifkan terlebih
dahulu.

Contoh:

```text
Kantor dipilih -> fetch daftar AO -> AO aktif
Jabatan berubah -> reset kantor dan AO -> fetch master baru
Periode berubah -> reset detail -> fetch ulang data
```

Setiap perubahan filter harus menghindari data lama tertinggal di layar.

### Langkah E - Buat state tampilan yang lengkap

Minimal setiap tab mempunyai:

- loading state;
- empty state;
- error state;
- success/status state untuk aksi simpan, hitung, generate, atau export;
- disabled state saat request berjalan;
- konfirmasi untuk aksi yang menimpa data lama.

## 6. Standar tampilan V2

- Gunakan satu shell kartu dengan judul, deskripsi singkat, dan action di sisi kanan.
- Filter umum berada di filter drawer/navbar.
- Filter yang dipakai terus-menerus atau berkaitan langsung dengan aksi utama boleh
  ditampilkan inline di header kartu.
- Tombol aksi utama hanya satu dan posisinya konsisten.
- Search yang tidak selalu dibutuhkan memakai komponen search buka/tutup.
- Tabel memakai wrapper scroll sendiri, bukan memaksa seluruh halaman melebar.
- Kolom identitas penting dibuat sticky saat tabel horizontal di-scroll.
- Jangan menampilkan kode teknis jika tidak membantu pengguna.
- Label harus menggunakan bahasa bisnis yang jelas.
- Ikon harus berasal dari `v2_icon()` dan selalu mempunyai `title`/`aria-label`.
- Jangan memakai inline style untuk kebutuhan umum.

### Responsive

Desktop:

- toolbar satu baris;
- tabel boleh horizontal scroll di dalam wrapper;
- kolom identitas tetap terbaca.

Mobile:

- padding dipadatkan;
- action tetap mudah disentuh;
- filter drawer tidak memenuhi lebar layar;
- tabel tetap bisa digeser horizontal;
- kolom identitas/sticky tidak boleh hilang;
- judul, tombol, dan dropdown tidak saling menimpa.

## 7. Standar penamaan

Gunakan prefix sesuai modul:

```text
ID HTML       : v2KpiSummaryBody
Class CSS     : v2-kpi-summary-table
State global  : window.__v2KpiDirectory
Endpoint type : summary, directory, detail, save_indicator
```

Aturan tambahan:

- ID harus unik dalam satu halaman.
- Jangan mendaftarkan event listener yang sama dua kali.
- Fungsi render harus aman ketika data kosong.
- Semua output dari API yang masuk ke `innerHTML` harus di-escape.
- Gunakan `data-*` attribute untuk hubungan filter, row, dan action.

## 8. Standar deployment

### Local

```text
http://localhost/report-dpk/v-2/<modul>/<tab>
```

### Server

```text
https://monbis.bkkjateng.co.id/v-2/<modul>/<tab>
```

Checklist deployment AaPanel:

- pastikan file router V2 ikut dipindahkan;
- pastikan route `.htaccess` atau fallback `index.php` aktif;
- pastikan base URL dan URL API tidak hardcode ke localhost;
- pastikan permission file dapat dibaca web server;
- pastikan versi PHP server kompatibel dan tidak memakai nested ternary tanpa kurung;
- naikkan cache version asset setelah perubahan CSS/JS;
- clear OPcache bila server masih membaca file lama;
- cek Network tab untuk response API dan status HTTP;
- jangan menyimpan secret, password, atau token permanen di frontend.

## 9. Checklist sebelum tab dianggap selesai

### Struktur dan routing

- [ ] URL lokal dapat dibuka langsung.
- [ ] URL server tidak menghasilkan 404.
- [ ] Router V2 mengarah ke halaman yang benar.
- [ ] Tidak ada nested ternary PHP yang tidak diberi kurung.

### Data dan API

- [ ] Endpoint dan payload sudah terdokumentasi.
- [ ] Filter dependent bekerja sesuai urutan.
- [ ] Data lama dibersihkan ketika filter induk berubah.
- [ ] Response kosong, error, dan timeout ditangani.
- [ ] Tidak ada query SQL di frontend.
- [ ] Tidak ada data dummy yang tertinggal.

### Tampilan

- [ ] Desktop rapi.
- [ ] Tablet tidak memotong action.
- [ ] Mobile tidak membuat halaman melebar tanpa kontrol.
- [ ] Tabel memiliki scroll internal bila kolom banyak.
- [ ] Search/filter/action konsisten dengan tab lain.
- [ ] Ikon memiliki tooltip atau accessible label.

### Validasi teknis

```powershell
php -l v-2/pages/<modul>.php
php -l api/controllers/<Modul>Controller.php
node --check v-2/assets/js/components.js
```

Uji endpoint tanpa mengubah data:

```powershell
Invoke-RestMethod `
  -Method Post `
  -Uri 'http://localhost/report-dpk/api/index.php?request=<modul>' `
  -ContentType 'application/json' `
  -Body (@{type='directory'} | ConvertTo-Json -Compress)
```

## 10. Template issue untuk tab baru

Salin bagian ini ketika memulai tab baru:

```markdown
## [ ] Modul: <nama modul>

- [ ] Tujuan dan pengguna sudah jelas.
- [ ] URL V2 sudah ditentukan.
- [ ] API existing sudah dicek.
- [ ] Kontrak request/response ditulis.
- [ ] Filter dan dependency filter ditentukan.
- [ ] View memakai layout/component V2.
- [ ] Loading, empty, error, dan success state tersedia.
- [ ] Desktop/tablet/mobile sudah dicek.
- [ ] PHP lint dan JavaScript check lulus.
- [ ] Endpoint read-only sudah diuji.
- [ ] Cache asset dinaikkan bila CSS/JS berubah.
- [ ] Catatan deployment AaPanel ditambahkan bila diperlukan.
```

## 11. Definition of Done

Tab baru dinyatakan selesai apabila pengguna dapat membuka URL-nya, memilih filter,
melihat data yang benar, memahami status proses, dan memakai halaman tersebut dari
desktop maupun mobile tanpa perlu mengetahui detail internal API.

Jika masih ada masalah, catat masalahnya di dokumen ini dengan format:

```text
Tanggal:
Modul/tab:
URL:
Masalah:
Penyebab:
Perbaikan:
Status:
```

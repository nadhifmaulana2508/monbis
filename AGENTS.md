# Panduan kerja project Monbis

Dokumen ini menjadi konteks kerja utama ketika membantu project ini. Ikuti aturan di sini sebelum mengubah kode. Preferensi lokal yang sifatnya pribadi disimpan di `.anti-slop/` dan tidak ikut Git.

## Cara bekerja dengan user

- Gunakan bahasa Indonesia yang santai, jelas, dan langsung ke hasil.
- Pahami request dari konteks percakapan sebelumnya, tetapi jangan menebak perubahan yang berisiko.
- Jika scope sudah jelas, kerjakan langsung. Tanyakan hanya hal yang benar-benar mengubah hasil atau berisiko terhadap data/production.
- Saat memberi laporan, sebutkan file yang berubah, alasan perubahan, verifikasi yang dijalankan, dan hal yang belum bisa diverifikasi.
- Jangan membuat penjelasan generik, placeholder, atau komentar kode yang tidak membantu.
- Jangan commit atau push kecuali user memintanya secara eksplisit.

## Struktur project

Project ini terdiri dari aplikasi PHP utama dan frontend React terpisah:

- `index.php` dan `.htaccess`: entry point serta routing aplikasi PHP.
- `pages/`: halaman PHP utama dan laporan bisnis.
- `api/`: endpoint backend, akses database, autentikasi, RBAC, dan data laporan.
- `views/`: layout, navbar, sidebar, footer, dan script bersama.
- `v-2/`: halaman/eksperimen versi baru yang harus diperlakukan sebagai area terpisah.
- `kpi-tukin/`: frontend React KPI Tukin.
- `rbb-bisnis/` dan `rbb/`: frontend/pekerjaan RBB; jangan disentuh atau dipush kecuali diminta.
- `frontend/`: aset atau frontend pendukung; cek isi dan build script sebelum mengubahnya.
- `docs/`: dokumentasi dan referensi database/PRD.

Jangan menganggap folder `dist/` sebagai source of truth. Baca `package.json` dan script build yang sesuai sebelum generate ulang.

## Environment dan route

- Local: `http://localhost/report-dpk/`
- Dev: `https://dev.monbis.bkkjateng.co.id/`
- Production: `https://monbis.bkkjateng.co.id/`

Route/base URL harus mengikuti host atau konfigurasi environment. Jangan menanam URL local, dev, atau production secara sembarang di halaman, API, asset, redirect, atau fetch React.

Jika memperbaiki routing:

1. Periksa `index.php`, `.htaccess`, konfigurasi web server, dan endpoint yang dipanggil.
2. Bedakan route aplikasi dari file fisik agar `/dashboard`, `/pages/...`, dan asset tidak saling tertukar.
3. Uji root URL, route langsung, refresh pada route, asset, dan route yang tidak ditemukan.
4. Error PHP harus diperbaiki dari syntax/logic sebenarnya; jangan menutup error dengan output kosong atau redirect palsu.

## Database dan sumber data

- Jangan menebak nama tabel/kolom. Cari pemakaian existing di `api/`, `pages/`, dan dokumentasi database terlebih dahulu.
- Jangan mencampur koneksi database DPK dan SIMPEG tanpa memastikan koneksi serta sumber datanya.
- Jangan menampilkan, menyalin, atau commit credential dari `.env` atau konfigurasi server.
- Query harus menggunakan parameter binding/escaping yang benar dan tetap membatasi data sesuai filter serta pagination.
- Untuk laporan berat, periksa query, indeks, jumlah data, pagination di backend, dan jumlah request sebelum mengubah UI.

## KPI Tukin

- Setting KPI adalah sumber aturan perhitungan. Jangan menduplikasi threshold atau bobot di banyak menu.
- Perubahan setting harus berdampak konsisten pada tabel setting, perhitungan, rekap, detail, export, dan laporan yang memakai indikator tersebut.
- Nilai nominal ditampilkan ringkas dan mudah diolah; hindari angka desimal/padding yang tidak diperlukan.
- Untuk perhitungan AO, pastikan indikator, bobot, arah, unit, target, skor, dan deskripsi tetap sinkron dengan data/API.
- Jika menambah indikator atau parameter, uji batas bawah, batas atas, nilai kosong, nol, dan nilai di luar range.
- React KPI memakai komponen UI yang sudah tersedia atau pola shadcn yang konsisten. Jangan membuat komponen duplikat jika komponen existing bisa dipakai.

## RBAC

- Perlakukan akses sebagai matrix yang dapat disimpan dan dibaca ulang, bukan hanya state checkbox di frontend.
- Uji tiga lapisan akses sesuai kebutuhan aplikasi: per pegawai, per jabatan, dan per divisi; pegawai adalah lapisan paling spesifik, lalu jabatan, lalu divisi sebagai fallback.
- Bedakan akses tersimpan, akses warisan, dan akses efektif. UI harus memberi tahu jika data masih mengikuti legacy/fallback.
- Setelah menyimpan matrix, reload data dari API/database dan pastikan checkbox serta jumlah akses tetap benar.
- Uji kasus positif dan negatif: akses diberi, akses dicabut, menu baru, role tanpa matrix, dan user yang tidak berhak.

## UI dan React

- Utamakan UI minimal, padat, bersih, responsif, dan mudah dipindai.
- Gunakan komponen shadcn yang sudah ada/pola yang konsisten. Hindari membuat komponen baru hanya untuk satu tombol, dialog, tabel, atau select.
- Gunakan button/icon yang jelas, state loading/error/empty, focus keyboard, dan label yang dapat dipahami.
- Tabel panjang harus punya header sticky yang benar, kolom identitas penting sticky bila diperlukan, overflow horizontal/vertikal yang terkendali, dan scrollbar tipis.
- Jangan membuat modal, input, card, atau header terlalu besar hingga menutup judul atau menghabiskan area kerja.
- Filter harus benar-benar mempengaruhi data; hapus filter yang tidak dipakai daripada membiarkan pilihan palsu.
- Sebelum pekerjaan UI, baca `.anti-slop/antislop.md` dan tentukan mode sesuai aturan antislop.

## Alur implementasi

1. Baca file terkait dan cari pemanggilnya sebelum mengedit.
2. Tentukan batas perubahan: PHP, API, React source, generated build, database, atau route.
3. Buat perubahan sekecil mungkin dan pertahankan perubahan user yang tidak terkait.
4. Jalankan verifikasi sesuai area yang berubah.
5. Periksa `git diff`, `git diff --check`, dan `git status` sebelum laporan.
6. Laporkan file yang berubah, hasil test, risiko, dan langkah deploy bila diperlukan.

## Verifikasi minimum

- PHP: `php -l` untuk setiap file PHP yang berubah.
- Routing/API: cek response HTTP, route langsung, error log, dan parameter invalid.
- React: gunakan script yang tercantum di `package.json`; minimal jalankan lint/build yang tersedia untuk app yang berubah.
- Database/report: cek data kosong, satu data, banyak data, tanggal/bulan berbeda, pagination, export, dan total-versus-detail.
- UI: cek desktop, viewport sempit, scroll tabel, sticky header/kolom, loading, error, dan empty state.
- RBAC: cek positif dan negatif, termasuk setelah reload dan login ulang.

## Git dan deploy

- Pertahankan perubahan user yang tidak terkait.
- Stage hanya file yang diminta. Jangan pernah ikut men-stage `.env`, credential, `.anti-slop/`, `node_modules/`, atau build artifact yang memang di-ignore.
- `rbb/` dan `rbb-bisnis/` tidak ikut push kecuali user meminta secara eksplisit.
- Sebelum push, tampilkan ringkasan file/commit yang akan dipush dan pastikan scope-nya benar.
- Setelah push, verifikasi branch, commit remote, dan file penting di server bila akses tersedia.
- Rollback harus menggunakan commit/tag yang sudah diketahui; jangan menjalankan reset destruktif tanpa persetujuan jelas.

## Shortcut konteks

- Pesan yang hanya berisi `.` berarti: muat `.anti-slop/project-context.md` dan `.anti-slop/prompts.md`, lalu lanjutkan request terakhir yang belum selesai.
- `simpan prompt: ...` berarti: simpan prompt tersebut ke `.anti-slop/prompts.md` untuk konteks lokal berikutnya.
- Shortcut ini tidak mengubah kode dan tidak menggantikan instruksi terbaru dari user.

<!-- antislop:start -->
## antislop

Untuk pekerjaan UI, copy, people, mobile layout, atau komentar kode, baca `.anti-slop/antislop.md` terlebih dahulu.
Skill antislop opsional belum dipasang; gunakan core rules kecuali user meminta skill tambahan.

Sebelum pekerjaan UI, tentukan mode antislop dengan urutan: instruksi sesi, preferensi global, lalu tanyakan `DURING` atau `AFTER` jika belum ada.
<!-- antislop:end -->

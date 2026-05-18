# MONBIS - Monitoring Bisnis BKK Jateng

> Sistem monitoring dan analisis kredit untuk PT BPR BKK Jawa Tengah (Perseroda).  
> Digunakan oleh manajemen pusat, korwil, dan cabang untuk memantau performa kredit, NPL, collection, dan operasional harian.

---

## Daftar Isi

- [Tech Stack](#tech-stack)
- [Arsitektur Sistem](#arsitektur-sistem)
- [Struktur Folder](#struktur-folder)
- [Autentikasi](#autentikasi)
- [Konsep Domain Penting](#konsep-domain-penting)
- [Peta Menu & Fitur](#peta-menu--fitur)
  - [Dashboard](#1-dashboard)
  - [Pemasaran](#2-pemasaran)
  - [NPL](#3-npl)
  - [PH (Penghapusan Buku)](#4-ph-penghapusan-buku)
  - [Collection](#5-collection)
  - [Laporan (Dev Only)](#6-laporan-dev-only)
  - [Fitur Tambahan (Belum di Navbar)](#7-fitur-tambahan-belum-di-navbar-utama)
- [API Routing](#api-routing)
- [Database Tracking Progress](#database-tracking-progress)
- [Status Pengerjaan Ringkas](#status-pengerjaan-ringkas)
- [Catatan Development](#catatan-development)

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.x (Native, tanpa framework) |
| **Frontend** | PHP Views + Tailwind CSS + Vanilla JavaScript (Fetch API) |
| **Database** | MySQL (PDO) |
| **Auth** | SSO Eksternal (`apisso.bkkjateng.co.id`) + JWT Lokal (fallback) |
| **Charting** | ApexCharts |
| **Server** | Apache (.htaccess rewrite) |

---

## Arsitektur Sistem

```
┌──────────────────────────────────────────────────────────────────┐
│                        BROWSER (USER)                            │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│   index.php (Router)                                             │
│   ├── ?url=login     → pages/login.php                          │
│   ├── ?url=dashboard → [navbar] + pages/dashboard.php           │
│   ├── ?url=npl       → [navbar] + pages/npl.php                 │
│   └── ?url=api/...   → Redirect ke /api/                        │
│                                                                  │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│   /api/index.php (API Router)                                    │
│   ├── ?request=dashboard  → routes/dashboard.php                 │
│   ├── ?request=kredit     → routes/kredit.php                    │
│   ├── ?request=npl        → routes/npl.php                       │
│   ├── ?request=bucket     → routes/bucket.php                    │
│   ├── ?request=kunjungan  → routes/kunjungan.php                 │
│   └── ... (17 endpoint modules)                                  │
│                                                                  │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│   Controllers (Business Logic)                                   │
│   ├── DashboardController    → Executive KPI + Chart             │
│   ├── KreditController       → Realisasi, Migrasi, MOB, Aging   │
│   ├── NplController          → NPL, Recovery, Perbandingan       │
│   ├── BucketController       → DPD Bucket, OTP, Migrasi Bucket  │
│   ├── KunjunganController    → Pipelane Mapping, CKPN, Visit     │
│   ├── FlowParController      → Flow PAR, Update Catatan          │
│   ├── PipelaneController     → Jatuh Tempo Pipeline AO           │
│   ├── HapusBukuController    → Recovery PH, LGD                  │
│   ├── MonevController        → Komitmen Mingguan                  │
│   ├── ProspekController      → CRUD Prospek Nasabah              │
│   ├── LapkeuController       → Pivot Laporan Keuangan            │
│   ├── RepaymentRateController→ Rekap RR per Cabang               │
│   └── ...                                                        │
│                                                                  │
├──────────────────────────────────────────────────────────────────┤
│                        MySQL DATABASE                             │
│   Tabel Utama: nominatif, account_handle, transaksi_kredit,      │
│   kode_kantor, kankas, ao_kredit, ref_dpd_bucket,                │
│   update_realisasi_kredit, summary_kredit_harian, acc_history,   │
│   monev_header, monev_detail, prospek, dll.                      │
└──────────────────────────────────────────────────────────────────┘
```

---

## Struktur Folder

```
monbis/
├── index.php                 # Router utama (frontend)
├── .htaccess                 # URL Rewrite
├── .env                      # Konfigurasi DB (tidak di-commit)
│
├── api/
│   ├── index.php             # API Router (dispatch endpoint)
│   ├── .htaccess             # Rewrite API
│   ├── config/
│   │   ├── database.php      # Koneksi PDO MySQL
│   │   └── config.php        # (Kosong, config via .env)
│   ├── controllers/          # Business logic per modul
│   ├── routes/               # Routing per endpoint (type-based dispatch)
│   ├── helpers/              # JWT, Response, Upload, CKPN Utils
│   ├── middlewares/          # Auth middleware (SSO + JWT)
│   └── img/kunjungan/        # Upload foto kunjungan
│
├── pages/                    # Halaman frontend (1 file = 1 halaman)
├── views/                    # Layout (header, navbar, footer, script)
├── img/                      # Asset gambar statis
│
└── docs/
    └── database/
        └── dev_tracking.sql  # DDL + Data tracking progress pengerjaan
```

---

## Autentikasi

### Flow Login
1. User buka aplikasi → cek cookie `sso_token`
2. Jika **belum ada** → redirect ke `pages/login.php`
3. Login via **SSO BKK Jateng** (`apisso.bkkjateng.co.id/api/login`)
4. Jika SSO berhasil → set cookie `sso_token` + simpan user info di `localStorage`
5. Fallback: login lokal dengan password hardcoded (untuk development)

### Token & Guard
- Cookie: `sso_token` (httpOnly, 8 jam expiry)
- localStorage: `user` object (nama, kode_kantor, role, dll)
- API Guard: JWT verify di `middlewares/auth.php` (untuk endpoint yang butuh auth)
- Frontend Guard: di `index.php` cek cookie sebelum load halaman

---

## Konsep Domain Penting

| Istilah | Penjelasan |
|---------|-----------|
| **Nominatif** | Snapshot data rekening kredit harian. Setiap hari ada record baru per rekening dengan `created` = tanggal data. Ini tabel utama yang jadi sumber hampir semua laporan. |
| **Closing Date** | Tanggal akhir bulan sebelumnya. Dipakai sebagai baseline pembanding (M-1). |
| **Harian Date** | Tanggal data terkini (hari ini atau tanggal yang dipilih user). |
| **Kolektibilitas** | L (Lancar), DP (Dalam Perhatian Khusus), KL (Kurang Lancar), D (Diragukan), M (Macet). NPL = KL + D + M. |
| **DPD (Days Past Due)** | Hari menunggak. Basis pengelompokan bucket: Current (0), SC (1-30), FE (31-90), BE (>90). |
| **Bucket** | Kategori berdasarkan DPD. SC=Short Collection, FE=First Effort, BE=Bad Effort. |
| **Run-off** | Realisasi - (Saldo Akhir - Saldo Awal). Mengukur kredit yang "keluar" (pelunasan/angsuran) vs yang masuk (pencairan). |
| **Repayment Rate (RR)** | OS Lancar (hari_menunggak=0 & kolek='L') / Total OS. Mengukur kualitas portofolio. |
| **Flow PAR** | Nasabah yang berpindah dari status baik ke NPL dalam periode tertentu. |
| **Account Handle** | Mapping siapa AO Remedial/PIC yang menangani rekening tertentu pada tanggal tertentu. |
| **MOB (Month on Book)** | Usia kredit sejak pencairan. MOB 6 = 6 bulan pertama (early delinquency indicator). |
| **CKPN** | Cadangan Kerugian Penurunan Nilai. Dihitung: PD x LGD x Outstanding. |
| **Korwil** | Koordinator Wilayah. Semarang (001-007), Solo (008-014), Banyumas (015-021), Pekalongan (022-028). |
| **PH (Penghapusan Buku)** | Kredit macet yang sudah dihapus dari neraca tapi masih ditagih (off-balance sheet). |

---

## Peta Menu & Fitur

### 1. Dashboard

| # | Fitur | Slug | Status | Progress | Deskripsi |
|---|-------|------|--------|----------|-----------|
| 1 | Executive Dashboard | `dashboard` | Done | 95% | Dashboard utama: KPI box (Saldo Bank, NPL, RR, Realisasi), Tren NPL multi-periode, Top/Bottom cabang, Flow vs Recovery NPL, Tren Portofolio Kredit (OS, NPL%, RR%), Realisasi by Produk, Runoff vs Realisasi per Korwil, Perkembangan DPK. |
| 2 | Dashboard V2 (Korwil) | `dashboard_v2` | Done | 90% | Versi enhanced dengan filter Korwil & scroll area responsif. |

---

### 2. Pemasaran

| # | Fitur | Slug | Status | Progress | Deskripsi |
|---|-------|------|--------|----------|-----------|
| 1 | Realisasi Kredit | `realisasi_kredit` | Done | 100% | Rekap realisasi MTD per cabang/kankas. NOA, nominal, run-off, growth. Drill-down detail per rekening. |
| 2 | Realisasi Kredit & Promo | `realisasi_promo` | Done | 100% | Filter berdasarkan kode promo. Chart tren promo vs non-promo mingguan. |
| 3 | Realisasi Kredit AO | `realisasi_ao` | Done | 90% | Breakdown performa realisasi per individu AO. |
| 4 | Ontime Payment (OTP) | `otp` | Done | 90% | Monitoring tingkat pembayaran tepat waktu. |
| 5 | Rekap Repayment Rate | `rekap_rr` | Done | 90% | RR per cabang: OS Lancar (0 hari tunggak & kolek L) / Total OS. |
| 6 | Migrasi Bucket SC | `migrasi_bucket_sc` | Done | 85% | Perpindahan bucket DPD kategori SC (1-30 hari). Stay/membaik/memburuk. |
| 7 | MOB 6 Bulan | `mob` | Done | 85% | Tracking kredit baru 6 bulan pertama. Early delinquency measurement. |
| 8 | Pipelane AO Kredit | `pipelane_ao_jt` | Done | 90% | Tracking nasabah jatuh tempo: Sudah Refi / Lunas / Top-Up / Retensi / Drop. |
| 9 | Jatuh Tempo & Refinancing | `jatuh_tempo` | Done | 85% | Rekap kredit jatuh tempo per bulan/tahun + status refinancing. |
| 10 | Monitoring & Evaluasi | `monev` | Done | 85% | Form input komitmen mingguan (W1-W4) per cabang. Kontrol akses per minggu. |

---

### 3. NPL

| # | Fitur | Slug | Status | Progress | Deskripsi |
|---|-------|------|--------|----------|-----------|
| 1 | NPL | `npl` | Done | 95% | Rekap NPL per cabang/kankas. Closing vs Harian, selisih nominal & %. Support mode baki_debet/saldo_bank. |
| 2 | Perbandingan NPL | `perbandingan_npl` | Done | 90% | Perbandingan NPL antar periode. Tren kenaikan/penurunan. |
| 3 | Recovery NPL | `recovery_npl` | Done | 85% | Monitoring nasabah yang berhasil turun kolektibilitas (membaik). |
| 4 | Flow PAR | `flow_par` | Done | 90% | Tracking nasabah pindah dari Lancar/DPK ke NPL. Update catatan per nasabah. |
| 5 | 25 NPL Besar | `npl_25_besar` | Done | 85% | Top 25 nasabah NPL berdasarkan outstanding terbesar. |
| 6 | Potensi NPL | `potensi_npl` | Done | 85% | Early warning: deteksi nasabah berpotensi jadi NPL (jatuh tempo, flow kolek). |
| 7 | Flow 50 Besar | `flow_50_besar` | In Progress | 60% | Top 50 nasabah flow terbesar berdasarkan nominal. |

---

### 4. PH (Penghapusan Buku)

| # | Fitur | Slug | Status | Progress | Deskripsi |
|---|-------|------|--------|----------|-----------|
| 1 | Recovery PH | `recovery_ph` | Done | 85% | Monitoring recovery dari kredit hapus buku. Tracking pembayaran nasabah PH. |
| 2 | Rekap Recovery (LGD) | `lgd` | Done | 80% | Loss Given Default - total recovery PH per cabang. Efektivitas penagihan. |

---

### 5. Collection

| # | Fitur | Slug | Status | Progress | Deskripsi |
|---|-------|------|--------|----------|-----------|
| 1 | Migrasi Kolek | `migrasi_kolek` | Done | 90% | Matrix migrasi kolektibilitas (L/DP/KL/D/M). NOA & nominal naik/turun. |
| 2 | Bucket DPD & Kolek | `actual_kredit` | Done | 90% | Distribusi kredit per bucket DPD. Breakdown SC/FE/BE per AO Remedial. |
| 3 | Migrasi Bucket | `migrasi_bucket` | Done | 85% | Matrix perpindahan antar bucket DPD (Current/1-30/31-60/61-90/>90). |
| 4 | Search Debitur Kredit | `search_debitur` | Done | 90% | Pencarian detail debitur. Filter status/kecamatan/kelurahan. Form pipelane & komitmen. |
| 5 | OTP Bucket FE (31-90) | `otp_bucket_fe` | Done | 80% | On Time Payment khusus nasabah DPD 31-90 hari. |
| 6 | Kunjungan Nasabah | `kunjungan` | Done | 85% | Pencatatan kunjungan AO Remedial. Upload foto, catatan, history per atasan. |
| 7 | Perhitungan CKPN | `ckpn` | Done | 80% | Perhitungan CKPN otomatis: PD x LGD x Outstanding. Individual & kolektif. |

---

### 6. Laporan (Dev Only)

> Menu ini hanya tampil untuk user dengan role developer.

| # | Fitur | Slug | Status | Progress | Deskripsi |
|---|-------|------|--------|----------|-----------|
| 1 | Laporan Keuangan | `lapkeu_kantor` | Done | 75% | Pivot report saldo akun (acc_history) per cabang 000-028. |
| 2 | Rekap Aging Kredit | `aging_kredit` | In Progress | 60% | Aging schedule berdasarkan usia kredit. Analisis vintage/MOB. |
| 3 | Layanan Digital | `layanan_digital` | Done | 80% | Dashboard transaksi digital: Top 5, chart donut, growth per channel. |
| 4 | Pipelane Prospek | `prospek` | In Progress | 70% | CRUD data prospek/calon nasabah kredit per cabang. |

---

### 7. Fitur Tambahan (Belum di Navbar Utama)

| Fitur | Slug | Status | Keterangan |
|-------|------|--------|------------|
| Profile | `profile` | Done | Halaman profil user |
| Report Mingguan | `report_mingguan` | In Progress | Laporan mingguan per cabang (dummy data) |
| Monev Mingguan | `monev_mingguan` | In Progress | Rekap monev antar minggu |
| Rekap Monev | `rekap_monev` | In Progress | Ringkasan monev lintas cabang |
| Update Flow PAR | `update_flowpar` | Done | Form update keterangan nasabah flow |
| Update Potensi | `update_potensi` | Done | Form update keterangan potensi NPL |
| Account Handle | `account_handle` | Done | Mapping AO Remedial per rekening |
| Monitoring AO | `monitoring_ao` | In Progress | Tracking performa AO Remedial |
| Plan CKPN | `plan_ckpn` | In Progress | Perencanaan/simulasi CKPN |
| M1 Actual | `m1_actual` | In Progress | Data actual bulan M-1 |

---

## API Routing

Semua API diakses via `POST /api/?request={endpoint}` dengan body JSON berisi field `type` sebagai sub-action.

| Endpoint | Route File | Controller | Fungsi Utama |
|----------|-----------|-----------|--------------|
| `auth` | routes/auth.php | AuthController | Login SSO, verify token |
| `dashboard` | routes/dashboard.php | DashboardController | Executive KPI, Tren, Top/Bottom |
| `kredit` | routes/kredit.php | KreditController | Realisasi, Promo, Detail, MOB |
| `npl` | routes/npl.php | NplController | NPL Rekap, Recovery, Perbandingan |
| `bucket` | routes/bucket.php | BucketController | DPD Bucket, OTP, Migrasi SC |
| `bucket_fe` | routes/bucket_fe.php | BucketFeController | OTP Bucket FE (31-90) |
| `kolek` | routes/kolek.php | KreditController | Migrasi Kolektibilitas |
| `kunjungan` | routes/kunjungan.php | KunjunganController | Pipelane, Bucket AO, Visit, CKPN |
| `flow_par` | routes/flow_par.php | FlowParController | Flow PAR, Update Catatan |
| `jt` | routes/jatuh_tempo.php | JatuhTempoController | Jatuh Tempo & Refinancing |
| `pipelane` | routes/pipelane.php | PipelaneController | Pipeline AO Kredit JT |
| `hapus_buku` | routes/hapus_buku.php | HapusBukuController | Recovery PH, LGD |
| `rr` | routes/rr.php | RepaymentRateController | Repayment Rate per Cabang |
| `monev` | routes/monev.php | MonevController | Komitmen Mingguan |
| `lapkeu` | routes/lapkeu.php | LapkeuController | Laporan Keuangan Pivot |
| `prospek` | routes/prospek.php | ProspekController | CRUD Prospek Nasabah |
| `transaksi` | routes/transaksi.php | TransaksiController | Data Transaksi Kredit |
| `catalog` | routes/catalog.php | CatalogController | Master Data (kode kantor, dll) |
| `date` | routes/date.php | DateController | Tanggal tersedia di DB |
| `kode` | routes/kode.php | KodeController | Master kode (kankas, AO, dll) |
| `ckpn` | routes/ckpn.php | CkpnController | Perhitungan CKPN |

### Pola Request API

```javascript
// Contoh pemanggilan API dari Frontend
fetch('/api/?request=kredit', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        type: 'realisasi_sum',        // Sub-action
        closing_date: '2025-12-31',   // Baseline M-1
        harian_date: '2026-01-15',    // Data aktual
        kode_kantor: '001'            // Filter cabang (opsional)
    })
})
```

### Pola Response API

```json
{
    "status": 200,
    "message": "Sukses",
    "data": { ... }
}
```

---

## Database Tracking Progress

Untuk tracking pengerjaan fitur secara terstruktur, gunakan file:

```
docs/database/dev_tracking.sql
```

### Tabel yang Dibuat

| Tabel | Fungsi |
|-------|--------|
| `dev_module` | Master modul/parent menu (Dashboard, Pemasaran, NPL, dll) |
| `dev_feature` | Daftar fitur + status, progress %, prioritas, file terkait |
| `dev_progress_log` | Log catatan per session (apa yang dikerjakan, oleh siapa) |
| `dev_backlog_idea` | Penampung ide fitur baru yang belum masuk pengerjaan |

### Cara Pakai

1. **Jalankan SQL** di database monbis:
   ```bash
   mysql -u root -p monbis < docs/database/dev_tracking.sql
   ```

2. **Cek status semua fitur:**
   ```sql
   SELECT m.nama_module, f.nama_fitur, f.status, f.progress_persen, f.prioritas
   FROM dev_feature f
   JOIN dev_module m ON f.module_id = m.id
   ORDER BY m.urutan, f.urutan;
   ```

3. **Cek ringkasan per modul:**
   ```sql
   SELECT m.nama_module,
       COUNT(*) AS total_fitur,
       SUM(CASE WHEN f.status = 'done' THEN 1 ELSE 0 END) AS selesai,
       SUM(CASE WHEN f.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
       SUM(CASE WHEN f.status = 'backlog' THEN 1 ELSE 0 END) AS backlog,
       ROUND(AVG(f.progress_persen), 0) AS avg_progress
   FROM dev_feature f
   JOIN dev_module m ON f.module_id = m.id
   GROUP BY m.nama_module ORDER BY m.urutan;
   ```

4. **Catat progress baru:**
   ```sql
   INSERT INTO dev_progress_log (feature_id, catatan, status_sebelum, status_sesudah, progress_sebelum, progress_sesudah, dikerjakan_oleh)
   VALUES (25, 'Selesai integrasi API prospek dengan filter korwil', 'in_progress', 'done', 70, 100, 'Nadhif');
   
   UPDATE dev_feature SET status = 'done', progress_persen = 100 WHERE id = 25;
   ```

5. **Tambah ide fitur baru:**
   ```sql
   INSERT INTO dev_backlog_idea (judul, deskripsi, module_id, prioritas, diusulkan_oleh)
   VALUES ('Export PDF Laporan NPL', 'User bisa download rekap NPL dalam format PDF', 3, 'medium', 'Nadhif');
   ```

---

## Status Pengerjaan Ringkas

> Update terakhir: Mei 2026

| Modul | Total Fitur | Done | In Progress | Backlog | Avg Progress |
|-------|:-----------:|:----:|:-----------:|:-------:|:------------:|
| Dashboard | 2 | 2 | 0 | 0 | 93% |
| Pemasaran | 10 | 10 | 0 | 0 | 90% |
| NPL | 7 | 6 | 1 | 0 | 86% |
| PH | 2 | 2 | 0 | 0 | 83% |
| Collection | 7 | 7 | 0 | 0 | 86% |
| Laporan | 4 | 2 | 2 | 0 | 71% |
| **TOTAL** | **32** | **29** | **3** | **0** | **86%** |

### Yang Masih In Progress (Prioritas)
1. **Pipelane Prospek** (`prospek`) - 70% - CRUD + filter korwil
2. **Rekap Aging Kredit** (`aging_kredit`) - 60% - Aging schedule vintage
3. **Flow 50 Besar** (`flow_50_besar`) - 60% - Top 50 flow nasabah

---

## Catatan Development

### Konvensi Kode
- **Routing API**: Semua endpoint pakai `POST` dengan `type` di body JSON sebagai dispatcher
- **Filter Wilayah**: Semua controller punya pattern yang sama: `kode_kantor` (spesifik cabang) atau `korwil` (Semarang/Solo/Banyumas/Pekalongan)
- **Tanggal**: Selalu 2 parameter: `closing_date` (M-1) + `harian_date` (aktual)
- **Response**: Format standar `{ status, message, data }`
- **Frontend**: Setiap halaman self-contained (PHP + inline JS), fetch API ke backend

### Hal yang Perlu Diperhatikan
1. **File Duplikat** - Banyak file `*copy.php` dan `*copy 2.php` di pages/ dan helpers/. Ini WIP yang belum dirapikan.
2. **JWT Secret Hardcoded** - `'your-secret-key'` di auth.php & JWT.php. Untuk production harus pindah ke `.env`.
3. **Login Fallback** - Password hardcoded `bkkjtg123` di AuthController (hanya untuk dev).
4. **Config** - `api/config/config.php` kosong. Semua konfigurasi via `.env` di `database.php`.
5. **Menu Dev Only** - Menu "Laporan" disembunyikan dengan `display:none`, hanya tampil jika user role dev.

### Quick Start (Local Development)
```bash
# 1. Clone repo
git clone https://github.com/nadhifmaulana2508/monbis.git

# 2. Copy & edit env
cp .env.example .env
# Edit: DB_HOST, DB_NAME, DB_USER, DB_PASS

# 3. Import database
mysql -u root -p < docs/database/dev_tracking.sql

# 4. Jalankan di Apache/XAMPP
# Pastikan DocumentRoot mengarah ke folder monbis/
# Atau buat virtual host: monbis.local → /path/to/monbis/

# 5. Akses
# http://localhost/report-dpk/login
```

---

## Kontributor

- **Nadhif Maulana** - Lead Developer

---

*Dokumentasi ini di-generate dan di-maintain bersama dengan AI Assistant (Kiro). Untuk tracking detail per fitur, lihat tabel `dev_feature` di database atau file `docs/database/dev_tracking.sql`.*

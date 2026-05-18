-- ============================================================
-- MONBIS - Development Tracking System
-- ============================================================
-- Tabel ini digunakan untuk tracking progress pengerjaan fitur
-- agar setiap memulai session baru, bisa langsung tahu:
-- 1. Fitur apa saja yang SUDAH selesai
-- 2. Fitur apa yang SEDANG dikerjakan
-- 3. Fitur apa yang BELUM dikerjakan
-- 4. Catatan/progress terakhir
-- ============================================================

-- ============================================================
-- 1. TABEL MASTER MODUL (Parent Menu)
-- ============================================================
CREATE TABLE IF NOT EXISTS dev_module (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_module VARCHAR(30) NOT NULL UNIQUE COMMENT 'Kode unik module (ex: PEMASARAN, NPL, PH)',
    nama_module VARCHAR(100) NOT NULL COMMENT 'Nama tampilan module',
    icon VARCHAR(50) DEFAULT NULL COMMENT 'Icon sidebar (heroicons class)',
    urutan INT DEFAULT 0 COMMENT 'Urutan tampil di sidebar',
    is_dev_only TINYINT(1) DEFAULT 0 COMMENT '1 = hanya muncul untuk developer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Master modul/parent menu Monbis';

-- ============================================================
-- 2. TABEL FITUR (Sub Menu / Halaman)
-- ============================================================
CREATE TABLE IF NOT EXISTS dev_feature (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL COMMENT 'FK ke dev_module',
    kode_fitur VARCHAR(50) NOT NULL UNIQUE COMMENT 'Kode unik fitur (ex: REALISASI_KREDIT)',
    nama_fitur VARCHAR(150) NOT NULL COMMENT 'Nama tampilan fitur',
    slug VARCHAR(100) NOT NULL COMMENT 'URL slug di navbar (ex: realisasi_kredit)',
    deskripsi TEXT DEFAULT NULL COMMENT 'Penjelasan singkat fungsi fitur ini',
    
    -- File terkait (untuk referensi cepat)
    file_page VARCHAR(200) DEFAULT NULL COMMENT 'Path file halaman (ex: pages/realisasi_kredit.php)',
    file_controller VARCHAR(200) DEFAULT NULL COMMENT 'Path file controller',
    file_route VARCHAR(200) DEFAULT NULL COMMENT 'Path file route API',
    
    -- Status pengerjaan
    status ENUM('backlog','in_progress','done','blocked','deprecated') DEFAULT 'backlog' 
        COMMENT 'Status pengerjaan fitur',
    progress_persen TINYINT UNSIGNED DEFAULT 0 COMMENT 'Persentase selesai (0-100)',
    prioritas ENUM('critical','high','medium','low') DEFAULT 'medium' COMMENT 'Prioritas pengerjaan',
    
    -- Metadata
    assignee VARCHAR(100) DEFAULT NULL COMMENT 'Siapa yang mengerjakan',
    tanggal_mulai DATE DEFAULT NULL COMMENT 'Tanggal mulai pengerjaan',
    tanggal_selesai DATE DEFAULT NULL COMMENT 'Tanggal selesai (actual)',
    deadline DATE DEFAULT NULL COMMENT 'Target deadline',
    
    urutan INT DEFAULT 0 COMMENT 'Urutan tampil di submenu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (module_id) REFERENCES dev_module(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Daftar fitur/halaman beserta status pengerjaannya';

-- ============================================================
-- 3. TABEL LOG PROGRESS (Catatan Harian / Per-Session)
-- ============================================================
CREATE TABLE IF NOT EXISTS dev_progress_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feature_id INT NOT NULL COMMENT 'FK ke dev_feature',
    catatan TEXT NOT NULL COMMENT 'Apa yang dikerjakan / progress terakhir',
    status_sebelum ENUM('backlog','in_progress','done','blocked','deprecated') DEFAULT NULL,
    status_sesudah ENUM('backlog','in_progress','done','blocked','deprecated') DEFAULT NULL,
    progress_sebelum TINYINT UNSIGNED DEFAULT NULL,
    progress_sesudah TINYINT UNSIGNED DEFAULT NULL,
    dikerjakan_oleh VARCHAR(100) DEFAULT NULL COMMENT 'Siapa yang mengerjakan',
    session_id VARCHAR(100) DEFAULT NULL COMMENT 'ID session AI/dev (opsional)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (feature_id) REFERENCES dev_feature(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Log perubahan status dan catatan progress per fitur';

-- ============================================================
-- 4. TABEL RENCANA FITUR BARU (Backlog Ide)
-- ============================================================
CREATE TABLE IF NOT EXISTS dev_backlog_idea (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL COMMENT 'Judul ide fitur',
    deskripsi TEXT DEFAULT NULL COMMENT 'Penjelasan detail',
    module_id INT DEFAULT NULL COMMENT 'FK ke dev_module (opsional, belum tentu masuk module mana)',
    prioritas ENUM('critical','high','medium','low') DEFAULT 'medium',
    status ENUM('idea','approved','rejected','merged') DEFAULT 'idea' 
        COMMENT 'idea=baru diusulkan, approved=akan dikerjakan, merged=sudah jadi dev_feature',
    diusulkan_oleh VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (module_id) REFERENCES dev_module(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Penampung ide fitur baru yang belum masuk pengerjaan';


-- ============================================================
-- ============================================================
-- INSERT DATA AWAL (Sesuai kondisi Monbis saat ini)
-- ============================================================
-- ============================================================

-- ==========================
-- INSERT MODUL
-- ==========================
INSERT INTO dev_module (kode_module, nama_module, urutan, is_dev_only) VALUES
('DASHBOARD',  'Dashboard',   1, 0),
('PEMASARAN',  'Pemasaran',   2, 0),
('NPL',        'NPL',         3, 0),
('PH',         'PH (Penghapusan Buku)', 4, 0),
('COLLECTION', 'Collection',  5, 0),
('LAPORAN',    'Laporan',     6, 1);

-- ==========================
-- INSERT FITUR - DASHBOARD
-- ==========================
INSERT INTO dev_feature (module_id, kode_fitur, nama_fitur, slug, deskripsi, file_page, file_controller, file_route, status, progress_persen, prioritas, urutan) VALUES
(1, 'DASHBOARD_EXECUTIVE', 'Executive Dashboard', 'dashboard', 
 'Dashboard utama berisi KPI box (Saldo Bank, NPL, RR, Realisasi), Tren NPL, Top/Bottom cabang, Flow vs Recovery, Tren Portofolio Kredit, Realisasi by Produk, Runoff vs Realisasi per Korwil, dan Perkembangan Deposito/Tabungan.',
 'pages/dashboard.php', 'api/controllers/DashboardController.php', 'api/routes/dashboard.php',
 'done', 95, 'critical', 1);

-- ==========================
-- INSERT FITUR - PEMASARAN
-- ==========================
INSERT INTO dev_feature (module_id, kode_fitur, nama_fitur, slug, deskripsi, file_page, file_controller, file_route, status, progress_persen, prioritas, urutan) VALUES
(2, 'REALISASI_KREDIT', 'Realisasi Kredit', 'realisasi_kredit',
 'Rekap realisasi kredit MTD per cabang/kankas. Menampilkan NOA, nominal realisasi, run-off, dan growth. Bisa drill-down ke detail per rekening.',
 'pages/realisasi_kredit.php', 'api/controllers/KreditController.php', 'api/routes/kredit.php',
 'done', 100, 'critical', 1),

(2, 'REALISASI_PROMO', 'Realisasi Kredit & Promo', 'realisasi_promo',
 'Rekap realisasi yang difilter berdasarkan kode promo. Chart tren promo vs non-promo mingguan. Drill-down detail per cabang.',
 'pages/realisasi_promo.php', 'api/controllers/KreditController.php', 'api/routes/kredit.php',
 'done', 100, 'high', 2),

(2, 'REALISASI_AO', 'Realisasi Kredit AO', 'realisasi_ao',
 'Breakdown realisasi kredit per Account Officer (AO). Menampilkan performa individu AO dalam pencairan kredit.',
 'pages/realisasi_ao.php', 'api/controllers/KreditController.php', 'api/routes/kredit.php',
 'done', 90, 'high', 3),

(2, 'OTP', 'Ontime Payment (OTP)', 'otp',
 'Monitoring tingkat pembayaran tepat waktu nasabah kredit. OTP = nasabah yang membayar sebelum/tepat jatuh tempo angsuran.',
 'pages/otp.php', 'api/controllers/BucketController.php', 'api/routes/bucket.php',
 'done', 90, 'high', 4),

(2, 'REKAP_RR', 'Rekap Repayment Rate', 'rekap_rr',
 'Rekap Repayment Rate (RR) per cabang. RR = Outstanding Lancar (0 hari tunggak & kolek L) / Total Outstanding.',
 'pages/rekap_rr.php', 'api/controllers/RepaymentRateController.php', 'api/routes/rr.php',
 'done', 90, 'high', 5),

(2, 'MIGRASI_BUCKET_SC', 'Migrasi Bucket SC', 'migrasi_bucket_sc',
 'Analisis perpindahan (migrasi) bucket DPD khusus kategori SC (Short Collection, 1-30 hari). Melihat pola nasabah yang stay, membaik, atau memburuk.',
 'pages/migrasi_bucket_sc.php', 'api/controllers/BucketController.php', 'api/routes/bucket.php',
 'done', 85, 'medium', 6),

(2, 'MOB_6_BULAN', 'MOB 6 Bulan', 'mob',
 'Month on Book 6 Bulan - tracking performa kredit baru dalam 6 bulan pertama setelah pencairan. Mengukur early delinquency.',
 'pages/mob.php', 'api/controllers/KreditController.php', 'api/routes/kredit.php',
 'done', 85, 'medium', 7),

(2, 'PIPELANE_AO_JT', 'Pipelane AO Kredit', 'pipelane_ao_jt',
 'Tracking nasabah yang akan jatuh tempo di tahun tertentu. Status: Sudah Refinancing, Lunas, Top-Up Potensial, Retensi, atau Drop (macet).',
 'pages/pipelane_ao_jt.php', 'api/controllers/PipelaneController.php', 'api/routes/pipelane.php',
 'done', 90, 'high', 8),

(2, 'JATUH_TEMPO', 'Jatuh Tempo & Refinancing', 'jatuh_tempo',
 'Rekap kredit yang jatuh tempo per bulan/tahun beserta status refinancing-nya. Untuk perencanaan target pencairan.',
 'pages/jatuh_tempo.php', 'api/controllers/JatuhTempoController.php', 'api/routes/jatuh_tempo.php',
 'done', 85, 'high', 9);

-- ==========================
-- INSERT FITUR - NPL
-- ==========================
INSERT INTO dev_feature (module_id, kode_fitur, nama_fitur, slug, deskripsi, file_page, file_controller, file_route, status, progress_persen, prioritas, urutan) VALUES
(3, 'NPL_REKAP', 'NPL', 'npl',
 'Rekap NPL (Non Performing Loan) per cabang/kankas. Membandingkan NPL closing vs harian, menampilkan selisih nominal dan persentase.',
 'pages/npl.php', 'api/controllers/NplController.php', 'api/routes/npl.php',
 'done', 95, 'critical', 1),

(3, 'PERBANDINGAN_NPL', 'Perbandingan NPL', 'perbandingan_npl',
 'Perbandingan NPL antar periode (closing vs closing sebelumnya). Visualisasi tren kenaikan/penurunan NPL.',
 'pages/perbandingan_npl.php', 'api/controllers/NplController.php', 'api/routes/npl.php',
 'done', 90, 'high', 2),

(3, 'RECOVERY_NPL', 'Recovery NPL', 'recovery_npl',
 'Monitoring recovery/penyelamatan kredit NPL. Menampilkan nasabah yang berhasil turun kolektibilitas (membaik) dari KL/D/M.',
 'pages/recovery_npl.php', 'api/controllers/NplController.php', 'api/routes/npl.php',
 'done', 85, 'high', 3),

(3, 'FLOW_PAR', 'Flow PAR', 'flow_par',
 'Flow PAR (Portfolio at Risk) - tracking nasabah yang berpindah dari Lancar/DPK ke NPL dalam periode tertentu. Bisa update catatan per nasabah.',
 'pages/flow_par.php', 'api/controllers/FlowParController.php', 'api/routes/flow_par.php',
 'done', 90, 'critical', 4),

(3, 'NPL_25_BESAR', '25 NPL Besar', 'npl_25_besar',
 'Daftar 25 nasabah NPL dengan outstanding terbesar. Fokus penanganan untuk impact recovery terbesar.',
 'pages/npl_25_besar.php', 'api/controllers/NplController.php', 'api/routes/npl.php',
 'done', 85, 'high', 5),

(3, 'POTENSI_NPL', 'Potensi NPL', 'potensi_npl',
 'Deteksi dini nasabah berpotensi jadi NPL berdasarkan pola tunggakan (jatuh tempo, flow kolek, dll). Early warning system.',
 'pages/potensi_npl.php', 'api/controllers/NplController.php', 'api/routes/npl.php',
 'done', 85, 'high', 6);

-- ==========================
-- INSERT FITUR - PH (Penghapusan Buku)
-- ==========================
INSERT INTO dev_feature (module_id, kode_fitur, nama_fitur, slug, deskripsi, file_page, file_controller, file_route, status, progress_persen, prioritas, urutan) VALUES
(4, 'RECOVERY_PH', 'Recovery PH', 'recovery_ph',
 'Monitoring recovery (penagihan) dari kredit yang sudah dihapus buku. Tracking pembayaran dari nasabah PH.',
 'pages/recovery_ph.php', 'api/controllers/HapusBukuController.php', 'api/routes/hapus_buku.php',
 'done', 85, 'high', 1),

(4, 'LGD_REKAP', 'Rekap Recovery (LGD)', 'lgd',
 'Loss Given Default - Rekap total recovery dari kredit hapus buku per cabang. Mengukur efektivitas penagihan PH.',
 'pages/lgd.php', 'api/controllers/HapusBukuController.php', 'api/routes/hapus_buku.php',
 'done', 80, 'medium', 2);

-- ==========================
-- INSERT FITUR - COLLECTION
-- ==========================
INSERT INTO dev_feature (module_id, kode_fitur, nama_fitur, slug, deskripsi, file_page, file_controller, file_route, status, progress_persen, prioritas, urutan) VALUES
(5, 'MIGRASI_KOLEK', 'Migrasi Kolek', 'migrasi_kolek',
 'Matrix migrasi kolektibilitas (L, DP, KL, D, M) dari closing ke harian. Melihat berapa NOA/nominal yang naik/turun kolek.',
 'pages/migrasi_kolek.php', 'api/controllers/KreditController.php', 'api/routes/kolek.php',
 'done', 90, 'critical', 1),

(5, 'ACTUAL_KREDIT', 'Bucket DPD & Kolek', 'actual_kredit',
 'Rekap distribusi kredit berdasarkan Bucket DPD (Days Past Due) dan Kolektibilitas. Breakdown SC/FE/BE per AO Remedial.',
 'pages/actual_kredit.php', 'api/controllers/KunjunganController.php', 'api/routes/kunjungan.php',
 'done', 90, 'critical', 2),

(5, 'MIGRASI_BUCKET', 'Migrasi Bucket', 'migrasi_bucket',
 'Matrix migrasi antar bucket DPD (Current, 1-30, 31-60, 61-90, >90). Tracking perpindahan nasabah antar bucket.',
 'pages/migrasi_bucket.php', 'api/controllers/BucketController.php', 'api/routes/bucket.php',
 'done', 85, 'high', 3),

(5, 'SEARCH_DEBITUR', 'Search Debitur Kredit', 'search_debitur',
 'Pencarian detail data debitur kredit. Filter berdasarkan status, kecamatan, kelurahan. Termasuk form create pipelane & komitmen.',
 'pages/search_debitur.php', 'api/controllers/KunjunganController.php', 'api/routes/kunjungan.php',
 'done', 90, 'high', 4),

(5, 'OTP_BUCKET_FE', 'OTP Bucket FE (31-90)', 'otp_bucket_fe',
 'On Time Payment khusus bucket FE (First Effort, 31-90 hari). Monitoring pembayaran nasabah di rentang DPD 31-90.',
 'pages/otp_bucket_fe.php', 'api/controllers/BucketFeController.php', 'api/routes/bucket_fe.php',
 'done', 80, 'medium', 5);

-- ==========================
-- INSERT FITUR - LAPORAN (Dev Only)
-- ==========================
INSERT INTO dev_feature (module_id, kode_fitur, nama_fitur, slug, deskripsi, file_page, file_controller, file_route, status, progress_persen, prioritas, urutan) VALUES
(6, 'LAPKEU_KANTOR', 'Laporan Keuangan', 'lapkeu_kantor',
 'Pivot report saldo akun keuangan (dari tabel acc_history) per cabang 000-028. Format matrix kode perkiraan vs cabang.',
 'pages/lapkeu_kantor.php', 'api/controllers/LapkeuController.php', 'api/routes/lapkeu.php',
 'done', 75, 'medium', 1),

(6, 'AGING_KREDIT', 'Rekap Aging Kredit', 'aging_kredit',
 'Aging schedule kredit berdasarkan usia kredit (berapa bulan sejak realisasi). Untuk analisis vintage/MOB.',
 'pages/aging_kredit.php', 'api/controllers/KreditController.php', 'api/routes/kredit.php',
 'in_progress', 60, 'medium', 2),

(6, 'LAYANAN_DIGITAL', 'Layanan Digital', 'layanan_digital',
 'Dashboard monitoring transaksi layanan digital (mobile banking, internet banking). Top 5, chart donut, growth per channel.',
 'pages/layanan_digital.php', NULL, NULL,
 'done', 80, 'low', 3),

(6, 'PROSPEK_PIPELANE', 'Pipelane Prospek', 'prospek',
 'Manajemen data prospek/calon nasabah kredit. CRUD data prospek per cabang dengan tracking status follow-up.',
 'pages/prospek.php', 'api/controllers/ProspekController.php', 'api/routes/prospek.php',
 'in_progress', 70, 'high', 4);

-- ==========================
-- INSERT FITUR TAMBAHAN (Halaman yang ada tapi belum masuk navbar utama)
-- ==========================
INSERT INTO dev_feature (module_id, kode_fitur, nama_fitur, slug, deskripsi, file_page, file_controller, file_route, status, progress_persen, prioritas, urutan) VALUES
(1, 'DASHBOARD_V2', 'Dashboard V2 (Korwil Mode)', 'dashboard_v2',
 'Versi kedua dashboard dengan support filter Korwil (Semarang, Solo, Banyumas, Pekalongan). Enhanced UI dengan scroll area.',
 'pages/dashboard_v2.php', 'api/controllers/DashboardController.php', 'api/routes/dashboard.php',
 'done', 90, 'high', 2),

(5, 'KUNJUNGAN', 'Kunjungan Nasabah', 'kunjungan',
 'Pencatatan kunjungan AO Remedial ke nasabah bermasalah. Upload foto, catatan, dan history kunjungan per atasan.',
 'pages/kunjungan.php', 'api/controllers/KunjunganController.php', 'api/routes/kunjungan.php',
 'done', 85, 'high', 6),

(3, 'FLOW_50_BESAR', 'Flow 50 Besar', 'flow_50_besar',
 'Daftar 50 nasabah dengan flow (perpindahan ke NPL) terbesar berdasarkan nominal baki debet.',
 'pages/flow_50_besar.php', 'api/controllers/FlowParController.php', 'api/routes/flow_par.php',
 'in_progress', 60, 'medium', 7),

(2, 'MONEV', 'Monitoring & Evaluasi', 'monev',
 'Form input komitmen mingguan per cabang (W1-W4). Indikator: Realisasi, Run-off, NPL, Recovery, dll. Dengan kontrol akses per minggu.',
 'pages/monev.php', 'api/controllers/MonevController.php', 'api/routes/monev.php',
 'done', 85, 'high', 10),

(5, 'CKPN', 'Perhitungan CKPN', 'ckpn',
 'Cadangan Kerugian Penurunan Nilai - perhitungan otomatis CKPN individual & kolektif berdasarkan PD, LGD, dan Outstanding.',
 'pages/ckpn.php', 'api/controllers/CkpnController.php', 'api/routes/ckpn.php',
 'done', 80, 'high', 7);


-- ============================================================
-- INSERT LOG PROGRESS AWAL (Sebagai baseline)
-- ============================================================
INSERT INTO dev_progress_log (feature_id, catatan, status_sesudah, progress_sesudah, dikerjakan_oleh) VALUES
(1, 'Dashboard executive sudah live dengan semua KPI box, chart tren NPL, top/bottom cabang, flow vs recovery, portofolio kredit.', 'done', 95, 'Nadhif'),
(2, 'Realisasi kredit sudah complete dengan drill-down detail, filter cabang/kankas/AO.', 'done', 100, 'Nadhif'),
(3, 'Realisasi promo sudah jalan termasuk chart mingguan promo vs non-promo.', 'done', 100, 'Nadhif'),
(10, 'NPL rekap sudah complete, support mode hitung baki_debet dan saldo_bank.', 'done', 95, 'Nadhif'),
(14, 'Flow PAR sudah jalan dengan update catatan per nasabah.', 'done', 90, 'Nadhif'),
(19, 'Migrasi kolek matrix closing vs harian sudah berfungsi.', 'done', 90, 'Nadhif'),
(20, 'Bucket DPD per AO Remedial dengan mapping account_handle.', 'done', 90, 'Nadhif'),
(22, 'Search debitur dengan filter status, kecamatan, kelurahan + form create pipelane.', 'done', 90, 'Nadhif');


-- ============================================================
-- QUERY HELPER: Cek Status Semua Fitur (View Ringkasan)
-- ============================================================
-- Jalankan query ini untuk melihat progress keseluruhan:

-- SELECT 
--     m.nama_module,
--     f.nama_fitur,
--     f.slug,
--     f.status,
--     f.progress_persen,
--     f.prioritas,
--     f.deskripsi
-- FROM dev_feature f
-- JOIN dev_module m ON f.module_id = m.id
-- ORDER BY m.urutan, f.urutan;

-- ============================================================
-- QUERY HELPER: Ringkasan per Module
-- ============================================================
-- SELECT 
--     m.nama_module,
--     COUNT(*) AS total_fitur,
--     SUM(CASE WHEN f.status = 'done' THEN 1 ELSE 0 END) AS selesai,
--     SUM(CASE WHEN f.status = 'in_progress' THEN 1 ELSE 0 END) AS sedang_dikerjakan,
--     SUM(CASE WHEN f.status = 'backlog' THEN 1 ELSE 0 END) AS belum,
--     ROUND(AVG(f.progress_persen), 0) AS avg_progress
-- FROM dev_feature f
-- JOIN dev_module m ON f.module_id = m.id
-- GROUP BY m.nama_module
-- ORDER BY m.urutan;

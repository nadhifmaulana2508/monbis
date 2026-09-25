<?php
/**
 * Konfigurasi FE V2.
 *
 * Biarkan kosong jika FE berada di instalasi Monbis yang sama.
 * Jika FE dipindah ke folder/domain sendiri, isi backend_base_url dengan
 * alamat Monbis yang menyediakan endpoint API dan autentikasi.
 */
return [
    'module' => '',            // otomatis: workspace, rbb, atau kpi
    'backend_base_url' => '', // contoh: https://monbis.bkkjateng.co.id
    'api_base_url' => '',     // contoh: https://monbis.bkkjateng.co.id/api
    'auth_base_url' => '',    // contoh: https://monbis.bkkjateng.co.id
    // Fallback resmi jika FE dijalankan di dua lingkungan ini.
    'local_backend_base_url' => 'http://localhost/report-dpk',
    'server_backend_base_url' => 'https://monbis.bkkjateng.co.id',
    'page_auth' => true,
];

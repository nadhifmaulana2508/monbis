<?php
$kpiLinks = [
    ['label' => 'Setting KPI Jabatan', 'route' => 'setting_kpi_jabatan'],
    ['label' => 'Nilai KPI AO', 'route' => 'hitung_kpi_ao'],
    ['label' => 'Generate KPI AO', 'route' => 'generate_kpi_ao'],
    ['label' => 'Rekap KPI AO', 'route' => 'rekap_kpi_ao'],
];
$rbbLinks = [
    ['label' => 'Proyeksi RBB', 'route' => 'input_rbb'],
    ['label' => 'Input RBB ABA', 'route' => 'input_rbb_aba'],
    ['label' => 'Input RBB Kredit', 'route' => 'rbb/detail/kredit'],
    ['label' => 'Input RBB DAMAS', 'route' => 'rbb/detail/damas'],
    ['label' => 'Input RBB Pendapatan', 'route' => 'rbb/detail/pendapatan'],
    ['label' => 'Input RBB Beban', 'route' => 'rbb/detail/beban'],
];
?>
<section class="v2-page-heading v2-launcher-heading">
  <div>
    <p class="v2-eyebrow">MONBIS WORKSPACE</p>
    <h1>Monitoring Bisnis</h1>
    <p>Semua modul bisnis penting dalam satu launcher. Pilih workspace yang ingin kamu buka.</p>
  </div>
  <div class="v2-page-status"><?= v2_badge('Workspace aktif', 'success') ?></div>
</section>

<section class="v2-launcher-hero">
  <div class="v2-launcher-hero-icon"><?= v2_icon('chart', 25) ?></div>
  <div class="v2-launcher-hero-copy">
    <p class="v2-eyebrow">QUICK ACCESS</p>
    <h2>Mulai dari modul yang kamu butuhkan.</h2>
    <p>KPI Bisnis dan Input RBB sekarang memakai FE V2 dengan backend/API Monbis yang sudah ada, jadi alur lama tetap aman dan tidak berubah.</p>
  </div>
  <div class="v2-launcher-hero-meta"><strong>MONBIS</strong><span>Launcher v2</span></div>
</section>

<section class="v2-launcher-section">
  <div class="v2-section-heading">
    <div><p class="v2-eyebrow">MAIN MODULES</p><h2>Pilih workspace</h2></div>
    <span class="v2-section-code">KPI · RBB · COLLECTION</span>
  </div>
  <div class="v2-launcher-grid">
    <a class="v2-module-card v2-module-card--kpi" href="<?= v2_e(v2_route_url($baseUrl, 'kpi/summary')) ?>" data-v2-launcher-access="kpi">
      <div class="v2-module-card-top"><span class="v2-module-icon"><?= v2_icon('chart', 24) ?></span><?= v2_badge('KPI', 'success') ?></div>
      <h3>KPI Bisnis</h3>
      <p>Kelola parameter, hitung, generate, dan lihat rekap kinerja KPI AO.</p>
      <ul><?php foreach ($kpiLinks as $item): ?><li><span><?= v2_icon('check', 13) ?></span><?= v2_e($item['label']) ?></li><?php endforeach; ?></ul>
      <span class="v2-module-card-link">Buka KPI Bisnis <?= v2_icon('arrow', 16) ?></span>
    </a>

    <a class="v2-module-card v2-module-card--rbb" href="<?= v2_e(v2_route_url($baseUrl, 'rbb/projection')) ?>" data-v2-launcher-access="rbb">
      <div class="v2-module-card-top"><span class="v2-module-icon"><?= v2_icon('file', 24) ?></span><?= v2_badge('RBB', 'warning') ?></div>
      <h3>Input RBB</h3>
      <p>Siapkan proyeksi dan input detail RBB untuk seluruh bagian bisnis terkait.</p>
      <ul><?php foreach ($rbbLinks as $item): ?><li><span><?= v2_icon('check', 13) ?></span><?= v2_e($item['label']) ?></li><?php endforeach; ?></ul>
      <span class="v2-module-card-link">Buka Input RBB <?= v2_icon('arrow', 16) ?></span>
    </a>

    <a class="v2-module-card v2-module-card--collection" href="<?= v2_e(v2_route_url($baseUrl, 'report_npl')) ?>">
      <div class="v2-module-card-top"><span class="v2-module-icon"><?= v2_icon('users', 24) ?></span><?= v2_badge('REPORT', 'default') ?></div>
      <h3>Collection</h3>
      <p>Pantau Report NPL dan kolektibilitas melalui tampilan report yang konsisten.</p>
      <ul><li><span><?= v2_icon('check', 13) ?></span>Report NPL</li><li><span><?= v2_icon('check', 13) ?></span>Filter dan perbandingan</li><li><span><?= v2_icon('check', 13) ?></span>Export report</li></ul>
      <span class="v2-module-card-link">Buka Collection <?= v2_icon('arrow', 16) ?></span>
    </a>
  </div>
</section>

<section class="v2-launcher-note">
  <span class="v2-launcher-note-icon"><?= v2_icon('check', 18) ?></span>
  <div><strong>Menu Monbis lama tetap tersedia.</strong><p>Launcher ini hanya menjadi pintu masuk tambahan; halaman dan menu lama tetap berjalan seperti sebelumnya.</p></div>
  <a href="<?= v2_e($legacyBase . '/dashboard') ?>" class="v2-button v2-button--soft">Buka Monbis lama <?= v2_icon('arrow', 15) ?></a>
</section>

<script>
(() => {
  const cards = [...document.querySelectorAll('[data-v2-launcher-access]')];
  let user = null;
  try { user = JSON.parse(localStorage.getItem('dpk_user') || 'null'); } catch {}
  if (!user || !cards.length) return;
  const fields = ['job_position', 'unit_kerja', 'role'].map((key) => String(user[key] || '').toLowerCase());
  const isDev = fields.some((value) => value.includes('divisi operasional') || value === 'dev');
  const kpiAllowed = isDev || fields[1].includes('divisi sdm dan umum');
  const rbbAllowed = ['id_peg', 'idPeg', 'id_pegawai', 'idPegawai', 'employee_id'].some((key) => String(user[key] || '').trim() === '102-119');
  cards.forEach((card) => {
    const allowed = card.dataset.v2LauncherAccess === 'kpi' ? kpiAllowed : rbbAllowed;
    if (!allowed) card.hidden = true;
  });
})();
</script>

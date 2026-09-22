<section class="v2-page-heading">
  <div><p class="v2-eyebrow">Workspace baru</p><h1>Dashboard MONBIS</h1><p>Fondasi frontend v2 untuk KPI, laporan, dan perencanaan bisnis.</p></div>
  <div class="v2-actions"><?= v2_button('Refresh data', 'soft', 'refresh') ?><?= v2_button('Panduan komponen', 'primary', 'file', ['data-v2-modal-open'=>'v2ComponentModal']) ?></div>
</section>
<div class="v2-grid v2-grid--4">
  <article class="v2-stat"><span class="v2-stat-label">Portfolio</span><strong>Rp 1,62 T</strong><small>Data contoh komponen</small></article>
  <article class="v2-stat"><span class="v2-stat-label">NPL</span><strong>8,42%</strong><small>Ringkasan KPI</small></article>
  <article class="v2-stat"><span class="v2-stat-label">Realisasi</span><strong>Rp 35,4 M</strong><small>Periode berjalan</small></article>
  <article class="v2-stat"><span class="v2-stat-label">Status</span><strong><span class="v2-badge">Ready</span></strong><small>Shell v2 aktif</small></article>
</div>
<div class="v2-grid v2-grid--3" style="margin-top:16px">
  <?= v2_card_open('Komponen inti', 'Semua page baru akan memakai fondasi yang sama.') ?>
    <div class="v2-card-body"><div class="v2-grid"><span class="v2-badge">Roboto default</span><span class="v2-badge">Responsive layout</span><span class="v2-badge">Sidebar modern</span><span class="v2-badge">Filter, modal, table</span></div></div>
  <?= v2_card_close() ?>
  <?= v2_card_open('Prioritas development', 'Urutan pekerjaan setelah branch siap.') ?>
    <div class="v2-card-body"><table class="v2-table"><tbody><tr><td>1</td><td>KPI Bisnis</td><td><span class="v2-badge">Next</span></td></tr><tr><td>2</td><td>Bug fixing report</td><td><span class="v2-badge">Next</span></td></tr><tr><td>3</td><td>Migrasi page ke v2</td><td><span class="v2-badge">Planned</span></td></tr></tbody></table></div>
  <?= v2_card_close() ?>
  <?= v2_card_open('Warna dasar', 'Token warna FE v2.') ?>
    <div class="v2-card-body"><div class="v2-grid"><span class="v2-badge" style="background:#EBFFD8">#EBFFD8</span><span class="v2-badge" style="background:#C4E1E6">#C4E1E6</span><span class="v2-badge" style="background:#A4CCD9">#A4CCD9</span><span class="v2-badge" style="background:#8DBCC7">#8DBCC7</span></div></div>
  <?= v2_card_close() ?>
</div>
<?= v2_modal('v2ComponentModal', 'Komponen FE v2', '<p class="v2-empty">Fondasi komponen sudah disiapkan di <code>v-2/components</code>. Page berikutnya tinggal memakai layout, form, filter, modal, table, dan icon helper yang sama.</p>') ?>

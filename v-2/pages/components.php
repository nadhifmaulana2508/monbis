<section class="v2-page-heading"><div><p class="v2-eyebrow">Design system</p><h1>UI Kit MONBIS</h1><p>Catalog komponen reusable untuk semua page baru di folder <span class="v2-code-chip">v-2/components</span>.</p></div><div class="v2-actions"><?= v2_button('Tampilkan toast', 'primary', 'check', ['data-v2-toast'=>'Komponen toast siap dipakai.']) ?></div></section>
<div class="v2-component-grid">
  <?= v2_card_open('Tabs dan badge', 'Navigasi konteks tanpa membuat page penuh.') ?>
  <div class="v2-card-body"><?= v2_tabs(['overview'=>'Overview', 'detail'=>'Detail', 'history'=>'History'], 'overview') ?><div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:14px"><?= v2_badge('Active', 'success') ?><?= v2_badge('Draft', 'warning') ?><?= v2_badge('Approved') ?></div></div>
  <?= v2_card_close() ?>
  <?= v2_card_open('Alert dan progress', 'Status dan progres memakai warna token.') ?>
  <div class="v2-card-body v2-form-stack"><?= v2_alert('Data berhasil dimuat dari endpoint.', 'success', 'Success') ?><?= v2_alert('Periksa filter sebelum export.', 'warning', 'Attention') ?><?= v2_progress(68, 'Migrasi page') ?></div>
  <?= v2_card_close() ?>
  <?= v2_card_open('Table component', 'Wrapper tabel aman untuk desktop dan mobile.') ?>
  <div class="v2-table-wrap"><table class="v2-table"><thead><tr><th>Komponen</th><th>Jenis</th><th>Status</th></tr></thead><tbody><tr><td>Filter bar</td><td>Form</td><td><span class="v2-badge">Ready</span></td></tr><tr><td>Modal</td><td>Overlay</td><td><span class="v2-badge">Ready</span></td></tr><tr><td>Responsive table</td><td>Data</td><td><span class="v2-badge">Ready</span></td></tr></tbody></table></div>
  <?= v2_card_close() ?>
  <?= v2_card_open('Modal dan form', 'Helper PHP dan data attribute JS tersedia.') ?>
  <div class="v2-card-body"><div class="v2-filter-bar" style="margin:0"><?= v2_input('demoSearch', 'Pencarian', '', 'Cari komponen...') ?><?= v2_select('demoStatus', 'Status', ['all'=>'Semua', 'ready'=>'Ready'], 'all') ?><?= v2_button('Primary', 'primary', 'check') ?><?= v2_button('Buka modal', 'secondary', 'file', ['data-v2-modal-open'=>'v2KitModal']) ?></div></div>
  <?= v2_card_close() ?>
</div>
<?= v2_modal('v2KitModal', 'Modal component', '<p class="v2-empty">Modal ini memakai helper <span class="v2-code-chip">v2_modal()</span> dan dapat dipanggil dari page mana pun.</p>') ?>

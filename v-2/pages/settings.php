<section class="v2-page-heading"><div><p class="v2-eyebrow">System preferences</p><h1>Pengaturan Tampilan</h1><p>Atur pengalaman kerja MONBIS v2 tanpa mengubah konfigurasi page lama.</p></div><div class="v2-actions"><?= v2_button('Reset default', 'soft', 'refresh', ['data-v2-settings-reset'=>'']) ?></div></section>
<?= v2_alert('Pengaturan disimpan otomatis di browser ini. User lain tidak ikut berubah.', 'info', 'Personal workspace') ?>
<div class="v2-component-grid" style="margin-top:16px">
  <?= v2_card_open('Tema dan tipografi', 'Token visual global untuk seluruh page v2.') ?>
  <div class="v2-card-body v2-form-stack">
    <?= v2_select('v2SettingTheme', 'Tema', ['light'=>'Light', 'dim'=>'Soft light', 'dark'=>'Dark mode'], 'light', ['data-v2-setting'=>'theme']) ?>
    <?= v2_select('v2SettingScale', 'Ukuran teks', ['small'=>'Kecil', 'normal'=>'Normal', 'large'=>'Besar'], 'normal', ['data-v2-setting'=>'scale']) ?>
    <?= v2_select('v2SettingDensity', 'Kerapatan komponen', ['comfortable'=>'Nyaman', 'compact'=>'Compact'], 'comfortable', ['data-v2-setting'=>'density']) ?>
  </div>
  <?= v2_card_close() ?>
  <?= v2_card_open('Layout workspace', 'Pengaturan navigasi dan aksen.') ?>
  <div class="v2-card-body v2-form-stack">
    <?= v2_select('v2SettingAccent', 'Accent color', ['teal'=>'Teal', 'mint'=>'Mint', 'blue'=>'Blue'], 'teal', ['data-v2-setting'=>'accent']) ?>
    <div class="v2-setting-row"><div><strong>Sidebar</strong><small>Status saat ini: <span data-v2-sidebar-state>Lebar</span></small></div><button type="button" class="v2-button v2-button--secondary" data-v2-sidebar-setting>Toggle sidebar</button></div>
  </div>
  <?= v2_card_close() ?>
</div>
<?= v2_card_open('Palette MONBIS', 'Warna dasar yang disediakan untuk component v2.') ?>
<div class="v2-card-body"><div class="v2-palette-grid"><div style="--swatch:#EBFFD8"><span></span><strong>Mint</strong><small>#EBFFD8</small></div><div style="--swatch:#C4E1E6"><span></span><strong>Sky</strong><small>#C4E1E6</small></div><div style="--swatch:#A4CCD9"><span></span><strong>Blue</strong><small>#A4CCD9</small></div><div style="--swatch:#8DBCC7"><span></span><strong>Teal</strong><small>#8DBCC7</small></div></div></div>
<?= v2_card_close() ?>

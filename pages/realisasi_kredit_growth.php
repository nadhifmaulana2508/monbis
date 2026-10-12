<?php
// File: pages/realisasi.php
// Tampilan Utama Realisasi & Growth Kredit
?>
<?php include __DIR__ . '/components/realisasi_components/style.php'; ?>

<div id="realisasiGrowthPage" class="max-w-[1920px] w-full h-full min-h-full mx-auto px-2 py-3 md:px-4 md:py-4 flex flex-col gap-3 md:gap-4 font-sans text-slate-800 bg-slate-50">
  
  <?php include __DIR__ . '/components/realisasi_components/filter_main.php'; ?>

  <?php include __DIR__ . '/components/realisasi_components/table_main.php'; ?>

</div>

<?php include __DIR__ . '/components/realisasi_components/modal_detail.php'; ?>

<?php include __DIR__ . '/components/realisasi_components/scripts.php'; ?>

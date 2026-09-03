<?php
$detailInput = !empty($_POST) ? $_POST : $_GET;
$detailTanggal = htmlspecialchars((string)($detailInput['tanggal'] ?? ''), ENT_QUOTES, 'UTF-8');
$detailKantor = htmlspecialchars((string)($detailInput['kode_kantor'] ?? ''), ENT_QUOTES, 'UTF-8');
?>

<div id="lnIssuePage" class="lni-page" data-tanggal="<?= $detailTanggal ?>" data-kantor="<?= $detailKantor ?>">
  <?php include __DIR__ . '/components/lap_neraca_selisih/header.php'; ?>
  <?php include __DIR__ . '/components/lap_neraca_selisih/summary.php'; ?>
  <?php include __DIR__ . '/components/lap_neraca_selisih/table.php'; ?>
</div>

<?php include __DIR__ . '/components/lap_neraca_selisih/style.php'; ?>
<?php include __DIR__ . '/components/lap_neraca_selisih/scripts.php'; ?>

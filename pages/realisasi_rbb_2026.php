<?php
// Snapshot manual capaian RBB Agustus 2026 berdasarkan materi upload.
// Data sengaja dipisahkan dari API sampai sumber data mentah tiap divisi seragam.

$rrbViews = [
    'kinerja_pusat' => 'Kinerja Pusat',
    'indikator_keuangan_pusat' => 'Indikator Keuangan Pusat',
    'kanwil_banyumas' => 'Kanwil Banyumas',
    'cabang_banjarnegara' => 'Cabang Banjarnegara',
    'cabang_banyumas' => 'Cabang Banyumas',
];

$rrbView = isset($_GET['view']) ? (string) $_GET['view'] : 'kinerja_pusat';
if (!isset($rrbViews[$rrbView])) {
    $rrbView = 'kinerja_pusat';
}

$rrbEscape = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

// Nominal snapshot disimpan dalam ribuan rupiah. Untuk tampilan, pangkas
// tiga digit terakhir agar kartu lebih ringkas; nilai asli tetap tersedia
// pada tooltip setiap nominal.
$rrbShortNominal = static function ($value): string {
    $text = trim((string) $value);
    if ($text === '' || !preg_match('/^\(?[0-9]+(?:\.[0-9]+)*\)?$/', $text)) {
        return $text;
    }

    $negative = str_starts_with($text, '(') && str_ends_with($text, ')');
    $digits = preg_replace('/[^0-9]/', '', $text) ?: '0';
    $shortDigits = strlen($digits) > 3 ? substr($digits, 0, -3) : '0';
    $short = number_format((int) $shortDigits, 0, ',', '.');

    return $negative ? '(' . $short . ')' : $short;
};

$rrbCentralCards = [
    ['name' => 'ASSET', 'icon' => '↗', 'tone' => 'blue', 'rbb' => '2.518.648.398', 'actual' => '2.494.864.781', 'year' => '2.599.022.455', 'period' => '99,1%', 'year_pct' => '96,0%'],
    ['name' => 'DAMAS', 'icon' => '◎', 'tone' => 'cyan', 'rbb' => '2.012.982.216', 'actual' => '1.973.762.534', 'year' => '2.102.964.016', 'period' => '98,1%', 'year_pct' => '93,9%'],
    ['name' => 'KREDIT', 'caption' => 'Saldo Bank', 'icon' => 'Rp', 'tone' => 'indigo', 'rbb' => '1.766.389.552', 'actual' => '1.711.039.993', 'year' => '1.804.794.924', 'period' => '96,9%', 'year_pct' => '94,8%'],
    ['name' => 'PENDAPATAN', 'icon' => '↗', 'tone' => 'teal', 'rbb' => '216.948.919', 'actual' => '214.100.633', 'year' => '345.674.691', 'period' => '98,7%', 'year_pct' => '61,9%'],
    ['name' => 'BIAYA', 'icon' => '▥', 'tone' => 'orange', 'rbb' => '190.465.340', 'actual' => '190.617.218', 'year' => '300.966.651', 'period' => '100,1%', 'year_pct' => '63,3%'],
    ['name' => 'LABA (RUGI)', 'caption' => 'Sebelum Pajak', 'icon' => 'Rp', 'tone' => 'gold', 'rbb' => '26.483.579', 'actual' => '23.483.415', 'year' => '44.708.040', 'period' => '88,7%', 'year_pct' => '52,5%'],
];

$rrbRegionalCards = [
    'kanwil_banyumas' => [
        'title' => 'Kanwil Banyumas',
        'subtitle' => 'Capaian Kinerja Kanwil Banyumas Raya · Agustus 2026',
        'cards' => [
            ['name' => 'ASSET', 'caption' => 'Total Aset', 'icon' => '◎', 'tone' => 'blue', 'rbb' => '358.569.304', 'actual' => '354.120.614', 'year' => '385.794.328', 'period' => '98,76%', 'year_pct' => '91,79%'],
            ['name' => 'TABUNGAN', 'caption' => 'Dana Pihak Ketiga (Tabungan)', 'icon' => '●', 'tone' => 'cyan', 'rbb' => '224.809.543', 'actual' => '222.448.842', 'year' => '238.602.212', 'period' => '98,95%', 'year_pct' => '93,23%'],
            ['name' => 'DEPOSITO', 'caption' => 'Dana Pihak Ketiga (Deposito)', 'icon' => 'Rp', 'tone' => 'indigo', 'rbb' => '81.561.238', 'actual' => '109.372.050', 'year' => '85.326.923', 'period' => '134,10%', 'year_pct' => '128,18%'],
            ['name' => 'TOTAL DAMAS', 'caption' => 'Dana Masyarakat', 'icon' => '●', 'tone' => 'teal', 'rbb' => '306.370.782', 'actual' => '331.820.892', 'year' => '323.917.310', 'period' => '108,31%', 'year_pct' => '102,44%'],
            ['name' => 'KREDIT', 'caption' => 'Penyaluran Kredit', 'icon' => 'Rp', 'tone' => 'blue', 'rbb' => '285.688.481', 'actual' => '306.514.234', 'year' => '338.652.555', 'period' => '107,29%', 'year_pct' => '90,51%'],
            ['name' => 'LABA (RUGI)', 'caption' => 'Laba Sebelum Pajak', 'icon' => '▥', 'tone' => 'green', 'rbb' => '3.848.936', 'actual' => '5.402.048', 'year' => '10.570.577', 'period' => '140,35%', 'year_pct' => '51,10%'],
        ],
    ],
    'cabang_banjarnegara' => [
        'title' => 'Cabang Banjarnegara',
        'subtitle' => 'Capaian Kinerja KC Banjarnegara · Agustus 2026',
        'cards' => [
            ['name' => 'ASSET', 'caption' => 'Total Aset', 'icon' => '◎', 'tone' => 'blue', 'rbb' => '81.109.947', 'actual' => '79.725.527', 'year' => '90.069.822', 'period' => '98,29%', 'year_pct' => '88,52%'],
            ['name' => 'TABUNGAN', 'caption' => 'Dana Pihak Ketiga (Tabungan)', 'icon' => '●', 'tone' => 'cyan', 'rbb' => '60.625.571', 'actual' => '56.545.359', 'year' => '63.894.980', 'period' => '93,27%', 'year_pct' => '88,50%'],
            ['name' => 'DEPOSITO', 'caption' => 'Dana Pihak Ketiga (Deposito)', 'icon' => 'Rp', 'tone' => 'indigo', 'rbb' => '20.659.850', 'actual' => '19.380.250', 'year' => '21.174.350', 'period' => '93,81%', 'year_pct' => '91,53%'],
            ['name' => 'TOTAL DAMAS', 'caption' => 'Dana Masyarakat', 'icon' => '●', 'tone' => 'teal', 'rbb' => '81.285.421', 'actual' => '75.925.649', 'year' => '85.069.330', 'period' => '93,41%', 'year_pct' => '89,25%'],
            ['name' => 'KREDIT', 'caption' => 'Penyaluran Kredit', 'icon' => 'Rp', 'tone' => 'blue', 'rbb' => '90.623.462', 'actual' => '80.978.675', 'year' => '93.210.303', 'period' => '89,36%', 'year_pct' => '86,88%'],
            ['name' => 'NPL', 'caption' => 'Rasio Kredit Bermasalah', 'icon' => '!', 'tone' => 'red', 'rbb' => '43,28', 'actual' => '43,64', 'year' => '38,65', 'period' => '99,16%', 'year_pct' => '88,56%', 'ratio' => true],
            ['name' => 'LABA (RUGI)', 'caption' => 'Laba Sebelum Pajak', 'icon' => '▥', 'tone' => 'green', 'rbb' => '(885.665)', 'actual' => '2.780.622', 'year' => '3.779.719', 'period' => '313,96%', 'year_pct' => '73,57%'],
        ],
    ],
    'cabang_banyumas' => [
        'title' => 'Cabang Banyumas',
        'subtitle' => 'Capaian Kinerja KC Banyumas · Agustus 2026',
        'cards' => [
            ['name' => 'ASSET', 'caption' => 'Total Aset', 'icon' => '◎', 'tone' => 'blue', 'rbb' => '107.270.848', 'actual' => '113.578.114', 'year' => '113.864.266', 'period' => '105,88%', 'year_pct' => '99,75%'],
            ['name' => 'TABUNGAN', 'caption' => 'Dana Pihak Ketiga (Tabungan)', 'icon' => '●', 'tone' => 'cyan', 'rbb' => '73.700.000', 'actual' => '78.519.709', 'year' => '79.678.663', 'period' => '106,54%', 'year_pct' => '98,55%'],
            ['name' => 'DEPOSITO', 'caption' => 'Dana Pihak Ketiga (Deposito)', 'icon' => 'Rp', 'tone' => 'indigo', 'rbb' => '31.329.508', 'actual' => '34.777.925', 'year' => '31.831.850', 'period' => '111,01%', 'year_pct' => '109,26%'],
            ['name' => 'TOTAL DAMAS', 'caption' => 'Dana Masyarakat', 'icon' => '●', 'tone' => 'teal', 'rbb' => '105.029.508', 'actual' => '113.297.634', 'year' => '111.510.513', 'period' => '107,87%', 'year_pct' => '101,60%'],
            ['name' => 'KREDIT', 'caption' => 'Penyaluran Kredit', 'icon' => 'Rp', 'tone' => 'blue', 'rbb' => '56.407.568', 'actual' => '49.139.019', 'year' => '60.384.482', 'period' => '87,11%', 'year_pct' => '81,38%'],
            ['name' => 'NPL', 'caption' => 'Rasio Kredit Bermasalah', 'icon' => '!', 'tone' => 'red', 'rbb' => '36,71', 'actual' => '48,57', 'year' => '31,21', 'period' => '75,58%', 'year_pct' => '64,25%', 'ratio' => true],
            ['name' => 'LABA (RUGI)', 'caption' => 'Laba Sebelum Pajak', 'icon' => '▥', 'tone' => 'green', 'rbb' => '1.904.884', 'actual' => '(56.860)', 'year' => '1.764.629', 'period' => '-2,98%', 'year_pct' => '(3,22)%'],
        ],
    ],
];

$rrbRatios = [
    ['name' => 'KPMM', 'rbb' => '46,40', 'actual' => '66,44', 'year' => '46,26', 'period' => '143,2%', 'year_pct' => '143,6%'],
    ['name' => 'Modal Inti', 'rbb' => '97,89', 'actual' => '98,12', 'year' => '97,78', 'period' => '100,2%', 'year_pct' => '100,3%'],
    ['name' => 'KAP', 'rbb' => '15,40', 'actual' => '15,56', 'year' => '11,59', 'period' => '101,0%', 'year_pct' => '134,3%'],
    ['name' => 'PPAP', 'rbb' => '100,00', 'actual' => '100,00', 'year' => '100,00', 'period' => '100,0%', 'year_pct' => '100,0%'],
    ['name' => 'NPL Brutto', 'rbb' => '23,29', 'actual' => '24,00', 'year' => '19,05', 'period' => '97,0%', 'year_pct' => '79,3%', 'inverse' => true],
    ['name' => 'NPL Netto', 'rbb' => '15,08', 'actual' => '15,64', 'year' => '11,31', 'period' => '96,4%', 'year_pct' => '72,3%', 'inverse' => true],
    ['name' => 'Kredit terhadap Total Aset Produktif', 'rbb' => '70,08', 'actual' => '68,35', 'year' => '67,14', 'period' => '97,5%', 'year_pct' => '101,8%'],
    ['name' => 'ROA', 'rbb' => '1,61', 'actual' => '1,64', 'year' => '1,89', 'period' => '102,0%', 'year_pct' => '87,1%'],
    ['name' => 'NIM', 'rbb' => '9,40', 'actual' => '10,46', 'year' => '9,86', 'period' => '111,3%', 'year_pct' => '106,1%'],
    ['name' => 'BOPO', 'rbb' => '85,95', 'actual' => '87,95', 'year' => '86,51', 'period' => '102,3%', 'year_pct' => '101,7%'],
    ['name' => 'CASH RATIO', 'rbb' => '19,35', 'actual' => '27,10', 'year' => '21,56', 'period' => '140,0%', 'year_pct' => '125,7%'],
    ['name' => 'LDR', 'rbb' => '87,96', 'actual' => '88,04', 'year' => '85,82', 'period' => '100,1%', 'year_pct' => '102,6%'],
];

$rrbIconSvg = static function (string $name): string {
    $name = strtoupper(trim($name));
    if ($name === 'ASSET') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16"/><path d="m7 15 3-4 3 2 5-6"/><path d="M17 5h4v4"/></svg>';
    }
    if ($name === 'DAMAS' || $name === 'TOTAL DAMAS') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3 19c.5-3.2 2.5-5 6-5s5.5 1.8 6 5M14 15c3.2-.4 5.3.9 6 4"/></svg>';
    }
    if ($name === 'TABUNGAN') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h15a1 1 0 0 1 1 1v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h12"/><path d="M17 13h3"/><circle cx="17" cy="13" r=".8"/></svg>';
    }
    if ($name === 'DEPOSITO') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 20h18M5 20V9h14v11M3 9l9-6 9 6M8 12v5M12 12v5M16 12v5"/></svg>';
    }
    if ($name === 'KREDIT') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12h4l2-3 3 7 2-4h7"/><path d="M4 20h16"/><path d="M6 17v3M18 17v3"/></svg>';
    }
    if ($name === 'NPL') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 21 20H3L12 3Z"/><path d="M12 9v5M12 17h.01"/></svg>';
    }
    if ($name === 'PENDAPATAN') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V5M4 19h16"/><path d="m7 15 3-4 3 2 5-6"/></svg>';
    }
    if ($name === 'BIAYA') {
        return '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18H6z"/><path d="M9 7h6M9 11h6M9 15h4"/><path d="M8 3v-1h8v1"/></svg>';
    }
    return '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/></svg>';
};

$rrbRenderMetricCard = static function (array $card, callable $escape, bool $shortNominal = false) use ($rrbIconSvg, $rrbShortNominal): string {
    $ratioClass = !empty($card['ratio']) ? ' is-ratio' : '';
    $isRatio = !empty($card['ratio']);
    $caption = isset($card['caption']) ? '<span>' . $escape($card['caption']) . '</span>' : '';
    $periodClass = strpos((string) $card['period'], '-') === 0 ? ' negative' : '';
    $yearClass = strpos((string) $card['year_pct'], '-') === 0 || strpos((string) $card['year_pct'], '(') === 0 ? ' negative' : '';
    $metricValue = static function ($value) use ($escape, $rrbShortNominal, $isRatio, $shortNominal): string {
        $display = trim((string) $value);
        if (!$isRatio && $shortNominal) {
            $display = $rrbShortNominal($display);
        }
        return '<strong>' . $escape($display) . '</strong>';
    };
    $footer = $shortNominal ? 'Dalam jutaan rupiah' : 'Dalam rupiah';
    return '<article class="rrb26-card rrb26-tone-' . $escape($card['tone']) . $ratioClass . '">' .
        '<div class="rrb26-card-head"><div class="rrb26-card-icon">' . $rrbIconSvg((string) $card['name']) . '</div><div><h3>' . $escape($card['name']) . '</h3>' . $caption . '</div></div>' .
        '<div class="rrb26-values"><div><small>RBB Agustus 2026</small>' . $metricValue($card['rbb']) . '</div><div><small>Realisasi Agustus</small><strong class="rrb26-actual">' . $escape($shortNominal && !$isRatio ? $rrbShortNominal($card['actual']) : trim((string) $card['actual'])) . '</strong></div><div><small>RBB Des 2026</small>' . $metricValue($card['year']) . '</div></div>' .
        '<div class="rrb26-achievements"><div><small>% RBB Agst 2026</small><b class="' . $periodClass . '">' . $escape($card['period']) . '</b></div><div><small>% RBB Des 2026</small><b class="' . $yearClass . '">' . $escape($card['year_pct']) . '</b></div></div>' .
        '<footer>' . $escape($footer) . '</footer></article>';
};
?>

<div id="realisasiRbb2026" class="rrb26-page">
  <section class="rrb26-header">
    <div class="rrb26-heading">
      <div class="rrb26-heading-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="m7 15 3-4 3 2 5-6"/></svg>
      </div>
      <div>
        <div class="rrb26-kicker">LAPORAN RBB</div>
        <h1>Realisasi RBB 2026</h1>
      </div>
    </div>
  </section>

  <section class="rrb26-workspace">
    <nav class="rrb26-tabs" role="tablist" aria-label="Wilayah laporan Realisasi RBB 2026">
      <?php foreach ($rrbViews as $key => $label): ?>
        <button type="button" class="rrb26-tab<?= $rrbView === $key ? ' active' : '' ?>" role="tab" aria-selected="<?= $rrbView === $key ? 'true' : 'false' ?>" aria-controls="rrb26-panel-<?= $rrbEscape($key) ?>" data-rrb26-tab="<?= $rrbEscape($key) ?>"><?= $rrbEscape($label) ?></button>
      <?php endforeach; ?>
    </nav>

    <div class="rrb26-note"><span><b>Posisi:</b> Agustus 2026</span><span><b>Satuan tampilan:</b> jutaan rupiah</span></div>

    <section id="rrb26-panel-kinerja_pusat" class="rrb26-panel<?= $rrbView === 'kinerja_pusat' ? ' active' : '' ?>" role="tabpanel" data-rrb26-panel="kinerja_pusat"<?= $rrbView === 'kinerja_pusat' ? '' : ' hidden' ?> >
      <div class="rrb26-panel-heading"><div><h2>Kinerja Pusat</h2><p>Perbandingan target RBB dengan realisasi bulan berjalan dan target akhir tahun.</p></div><span class="rrb26-scope-pill">PT BPR BKK JATENG</span></div>
      <div class="rrb26-card-grid rrb26-central-grid"><?php foreach ($rrbCentralCards as $card): echo $rrbRenderMetricCard($card, $rrbEscape, true); endforeach; ?></div>
    </section>

    <section id="rrb26-panel-indikator_keuangan_pusat" class="rrb26-panel<?= $rrbView === 'indikator_keuangan_pusat' ? ' active' : '' ?>" role="tabpanel" data-rrb26-panel="indikator_keuangan_pusat"<?= $rrbView === 'indikator_keuangan_pusat' ? '' : ' hidden' ?> >
      <div class="rrb26-panel-heading"><div><h2>Indikator Keuangan Pusat</h2><p>Rasio utama sesuai materi “Rasio Indikator Keuangan Utama”. Capaian NPL memakai rumus terbalik: RBB ÷ realisasi.</p></div><span class="rrb26-scope-pill">PUSAT</span></div>
      <div class="rrb26-table-shell"><table class="rrb26-table"><thead><tr><th>INDIKATOR</th><th>RBB AGUSTUS</th><th>REALISASI AGUSTUS</th><th>RBB DESEMBER</th><th>CAPAIAN RBB AGS</th><th>CAPAIAN RBB DES</th></tr></thead><tbody><?php foreach ($rrbRatios as $row): ?><tr<?= !empty($row['inverse']) ? ' class="inverse-row"' : '' ?>><th><?= $rrbEscape($row['name']) ?><?= !empty($row['inverse']) ? '<small> · inverse</small>' : '' ?></th><td><?= $rrbEscape($row['rbb']) ?>%</td><td class="rrb26-table-actual"><?= $rrbEscape($row['actual']) ?>%</td><td><?= $rrbEscape($row['year']) ?>%</td><td><b><?= $rrbEscape($row['period']) ?></b></td><td><b><?= $rrbEscape($row['year_pct']) ?></b></td></tr><?php endforeach; ?></tbody></table></div>
    </section>

    <?php foreach ($rrbRegionalCards as $key => $region): ?>
      <section id="rrb26-panel-<?= $rrbEscape($key) ?>" class="rrb26-panel<?= $rrbView === $key ? ' active' : '' ?>" role="tabpanel" data-rrb26-panel="<?= $rrbEscape($key) ?>"<?= $rrbView === $key ? '' : ' hidden' ?> >
        <div class="rrb26-panel-heading"><div><h2><?= $rrbEscape($region['title']) ?></h2><p><?= $rrbEscape($region['subtitle']) ?>.</p></div><span class="rrb26-scope-pill">AGUSTUS 2026</span></div>
        <div class="rrb26-card-grid rrb26-regional-grid"><?php foreach ($region['cards'] as $card): echo $rrbRenderMetricCard($card, $rrbEscape, true); endforeach; ?></div>
      </section>
    <?php endforeach; ?>

    <footer class="rrb26-footnote"><span>RBB Ags = target bulan berjalan · RBB Des = target akhir tahun</span></footer>
  </section>
</div>

<style>
  #realisasiRbb2026{--rrb26-navy:#092f59;--rrb26-blue:#1265e5;--rrb26-teal:#1599a8;--rrb26-line:#d6e3ed;--rrb26-bg:#f5f9fc;min-height:calc(100vh - 62px);width:100%;padding:14px;background:var(--rrb26-bg);color:var(--rrb26-navy);font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}
  #realisasiRbb2026 *{box-sizing:border-box}.rrb26-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:15px 17px;border:1px solid #d0dfeb;border-radius:17px;background:#fff;box-shadow:0 5px 18px rgba(12,69,117,.08)}.rrb26-heading{display:flex;align-items:center;gap:13px;min-width:0}.rrb26-heading-icon{display:grid;place-items:center;width:43px;height:43px;flex:0 0 auto;border-radius:12px;background:#2563eb;color:#fff;box-shadow:0 7px 15px rgba(37,99,235,.2)}.rrb26-heading-icon svg{width:23px;height:23px}.rrb26-kicker{margin-bottom:2px;color:#2c7b9a;font-size:9px;font-weight:950;letter-spacing:.15em}.rrb26-heading h1{margin:0;color:#0d3154;font-size:22px;line-height:1.15;font-weight:950;letter-spacing:-.03em}.rrb26-heading p{margin:3px 0 0;color:#72889a;font-size:10px;font-weight:650}
  .rrb26-workspace{margin-top:12px;overflow:hidden;border:1px solid #d0dfeb;border-radius:17px;background:#fff;box-shadow:0 5px 18px rgba(12,69,117,.06)}.rrb26-tabs{display:flex;gap:4px;overflow-x:auto;padding:9px 11px 0;border-bottom:1px solid #d9e6ee;background:linear-gradient(180deg,#fbfdfe 0%,#f5fafc 100%)}.rrb26-tabs::-webkit-scrollbar{height:5px}.rrb26-tabs::-webkit-scrollbar-thumb{background:#c0d4df;border-radius:99px}.rrb26-tab{position:relative;flex:0 0 auto;padding:10px 14px;border:1px solid transparent;border-radius:10px 10px 0 0;background:transparent;color:#718999;font-size:10px;font-weight:950;white-space:nowrap;cursor:pointer;transition:color .22s ease,background .22s ease,transform .22s ease}.rrb26-tab:after{content:"";position:absolute;right:12px;bottom:-1px;left:12px;height:3px;border-radius:99px 99px 0 0;background:#2563eb;transform:scaleX(0);transform-origin:center;transition:transform .25s ease}.rrb26-tab:hover{background:#eef8fa;color:#17667d;transform:translateY(-2px)}.rrb26-tab.active{border-color:#cee0e8;border-bottom-color:#fff;background:#fff;color:#0a3c61;box-shadow:0 -3px 12px rgba(28,101,143,.06)}.rrb26-tab.active:after{transform:scaleX(1)}.rrb26-note{display:flex;align-items:center;gap:7px;flex-wrap:wrap;padding:10px 15px;border-bottom:1px solid #e1eaf0;background:#fff;color:#627c8e;font-size:9px}.rrb26-note>span{padding:4px 8px;border:1px solid #d6e4eb;border-radius:999px}.rrb26-note b{color:#315e76}.rrb26-note-warning{border-color:#ffd7a8!important;background:#fff8ed;color:#b45309!important}.rrb26-panel{padding:16px}.rrb26-panel[hidden]{display:none}.rrb26-panel.active{animation:rrb26PanelIn .35s ease both}.rrb26-panel-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:13px}.rrb26-panel-heading h2{margin:0;color:#113b60;font-size:16px;line-height:1.2;font-weight:950}.rrb26-panel-heading p{max-width:800px;margin:4px 0 0;color:#78909f;font-size:10px;line-height:1.45}.rrb26-scope-pill{flex:0 0 auto;padding:5px 9px;border:1px solid #cce1ea;border-radius:999px;background:#f4fbfc;color:#237188;font-size:8px;font-weight:950;letter-spacing:.05em;white-space:nowrap}
  .rrb26-card-grid{display:grid;gap:12px}.rrb26-central-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.rrb26-regional-grid{grid-template-columns:repeat(4,minmax(0,1fr))}.rrb26-card{position:relative;overflow:hidden;min-width:0;padding:13px;border:1px solid #cfe0ea;border-radius:14px;background:linear-gradient(145deg,#fff 0%,#f6fbfe 100%);box-shadow:0 5px 13px rgba(12,69,117,.06);transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease;animation:rrb26CardIn .45s cubic-bezier(.2,.8,.2,1) both;animation-delay:var(--rrb26-delay,0ms)}.rrb26-card:hover{transform:translateY(-5px);border-color:#9ccce4;box-shadow:0 12px 24px rgba(12,69,117,.13)}.rrb26-card:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--rrb26-accent);transition:width .25s ease}.rrb26-card:hover:before{width:6px}.rrb26-card-head{display:flex;align-items:center;gap:9px;padding-left:2px;margin-bottom:11px}.rrb26-card-icon{display:grid;place-items:center;width:34px;height:34px;flex:0 0 auto;border-radius:50%;background:var(--rrb26-accent);color:#fff;box-shadow:0 4px 8px rgba(12,69,117,.16);transition:transform .25s ease}.rrb26-card-icon svg{width:18px;height:18px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.rrb26-card:hover .rrb26-card-icon{transform:rotate(-8deg) scale(1.08)}.rrb26-card-head h3{margin:0;color:#0b3481;font-size:16px;line-height:1.05;font-weight:950}.rrb26-card-head span{display:block;margin-top:3px;color:#1d67aa;font-size:9px;font-weight:700;line-height:1.15}.rrb26-values{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));border-top:1px solid #dbe9f0;border-bottom:1px solid #dbe9f0}.rrb26-values>div{min-width:0;padding:8px 4px;text-align:center}.rrb26-values>div+div{border-left:1px solid #dbe9f0}.rrb26-values small,.rrb26-achievements small{display:block;color:#46718a;font-size:7px;line-height:1.1;font-weight:850}.rrb26-values strong{display:block;margin-top:4px;color:#073d94;font-size:11px;line-height:1.1;font-weight:950;white-space:nowrap}.rrb26-values strong.rrb26-actual{color:#168b63}.rrb26-achievements{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px;margin-top:8px}.rrb26-achievements>div{padding:6px 4px;border-radius:7px;background:#eaf7ed;text-align:center}.rrb26-achievements>div+div{background:#fff0f0}.rrb26-achievements b{display:block;margin-top:3px;color:#11731d;font-size:17px;line-height:1;font-weight:950}.rrb26-achievements>div+div b{color:#ef2222}.rrb26-achievements b.negative{color:#ef2222!important}.rrb26-card footer{margin-top:6px;color:#245ad1;font-size:8px;font-style:italic;text-align:center}.rrb26-tone-blue{--rrb26-accent:#159fe8}.rrb26-tone-cyan{--rrb26-accent:#08a9bf}.rrb26-tone-indigo{--rrb26-accent:#5160dd}.rrb26-tone-teal{--rrb26-accent:#16a47b}.rrb26-tone-orange{--rrb26-accent:#f59e0b}.rrb26-tone-gold{--rrb26-accent:#e9a827}.rrb26-tone-green{--rrb26-accent:#27a95a}.rrb26-tone-red{--rrb26-accent:#e23742}.rrb26-card.is-ratio .rrb26-values strong{font-size:16px}.rrb26-card.is-ratio .rrb26-values small{font-size:7px}
  .rrb26-workspace,.rrb26-panel,.rrb26-panel-heading,.rrb26-card-grid{min-width:0}.rrb26-table-shell{max-width:100%;overflow-x:auto;overflow-y:hidden;border:1px solid #d1e0e9;border-radius:11px;overscroll-behavior-inline:contain;scrollbar-width:thin}.rrb26-table{width:100%;min-width:760px;border-collapse:separate;border-spacing:0;table-layout:fixed;color:#163d5c}.rrb26-table th,.rrb26-table td{padding:10px 11px;border-right:1px solid #d4e2eb;border-bottom:1px solid #d4e2eb;font-size:10px}.rrb26-table thead th{background:#2f99ad;color:#fff;font-size:9px;font-weight:950;text-align:center;white-space:nowrap}.rrb26-table tbody th{background:#f8fbfd;font-weight:900;text-align:left}.rrb26-table tbody td{background:#fff;font-family:"JetBrains Mono",ui-monospace,monospace;text-align:right;white-space:nowrap}.rrb26-table tbody tr:nth-child(even) td{background:#fbfdfe}.rrb26-table tbody tr:hover th,.rrb26-table tbody tr:hover td{background:#eef8fa}.rrb26-table th:first-child{width:29%;border-left:1px solid #d4e2eb}.rrb26-table th:not(:first-child){width:14.2%}.rrb26-table-actual{color:#008c67;font-weight:900}.rrb26-table td b{display:inline-block;padding:3px 6px;border-radius:999px;background:#eef9f2;color:#087d2c}.rrb26-table .inverse-row td b{background:#fff7e9;color:#b45309}.rrb26-table small{color:#b45309;font-size:8px;font-weight:800}
  .rrb26-footnote{display:flex;justify-content:space-between;gap:12px;padding:10px 15px;border-top:1px solid #e0eaf0;color:#7b909e;font-size:9px}.rrb26-footnote b{color:#496e81}@keyframes rrb26PanelIn{from{opacity:0;transform:translateY(7px)}to{opacity:1;transform:translateY(0)}}@keyframes rrb26CardIn{from{opacity:0;transform:translateY(12px) scale(.985)}to{opacity:1;transform:translateY(0) scale(1)}}@media(prefers-reduced-motion:reduce){#realisasiRbb2026 *{animation-duration:.01ms!important;animation-iteration-count:1!important;scroll-behavior:auto!important;transition-duration:.01ms!important}}
  @media(max-width:1100px){.rrb26-regional-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
  @media(max-width:850px){#realisasiRbb2026{padding:10px}.rrb26-header{align-items:flex-start;flex-direction:column}.rrb26-central-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.rrb26-regional-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.rrb26-panel{padding:12px}.rrb26-panel-heading{flex-direction:column}.rrb26-scope-pill{align-self:flex-start}}
  @media(max-width:540px){.rrb26-heading h1{font-size:19px}.rrb26-heading p{max-width:245px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.rrb26-central-grid,.rrb26-regional-grid{grid-template-columns:1fr}.rrb26-values strong{font-size:10px}.rrb26-card-head h3{font-size:15px}.rrb26-footnote{align-items:flex-start;flex-direction:column;gap:4px}.rrb26-note{padding:9px 10px}}
  @media(max-width:540px){#realisasiRbb2026{min-height:calc(100dvh - 56px);padding:7px;overflow-x:hidden}.rrb26-header{padding:11px;border-radius:13px}.rrb26-heading{width:100%;align-items:flex-start;gap:9px}.rrb26-heading-icon{width:38px;height:38px;border-radius:10px}.rrb26-heading-icon svg{width:20px;height:20px}.rrb26-heading h1{font-size:18px}.rrb26-heading p{max-width:none;font-size:8px;white-space:normal;line-height:1.35}.rrb26-workspace{margin-top:7px;border-radius:13px}.rrb26-tabs{gap:2px;padding:6px 6px 0;scrollbar-width:none}.rrb26-tabs::-webkit-scrollbar{display:none}.rrb26-tab{padding:8px 10px;font-size:8px}.rrb26-note{gap:5px;padding:8px 9px;font-size:8px}.rrb26-note>span{padding:4px 6px}.rrb26-panel{padding:10px}.rrb26-panel-heading{gap:8px;margin-bottom:10px}.rrb26-panel-heading h2{font-size:14px}.rrb26-panel-heading p{font-size:8px}.rrb26-scope-pill{padding:4px 7px;font-size:7px}.rrb26-card-grid{gap:8px}.rrb26-card{padding:10px;border-radius:11px}.rrb26-card-head{gap:7px;margin-bottom:8px}.rrb26-card-icon{width:30px;height:30px}.rrb26-card-icon svg{width:16px;height:16px}.rrb26-card-head h3{font-size:14px}.rrb26-card-head span{font-size:8px}.rrb26-values>div{padding:7px 3px}.rrb26-values small,.rrb26-achievements small{font-size:6.5px}.rrb26-values strong{max-width:100%;overflow:hidden;text-overflow:ellipsis;font-size:10px}.rrb26-card.is-ratio .rrb26-values strong{font-size:14px}.rrb26-achievements{gap:5px;margin-top:6px}.rrb26-achievements>div{padding:6px 3px}.rrb26-achievements b{font-size:15px}.rrb26-card footer{font-size:7px}.rrb26-footnote{padding:8px 10px;font-size:8px}.rrb26-table{min-width:700px}.rrb26-table th,.rrb26-table td{padding:8px 7px;font-size:8px}.rrb26-table thead th{font-size:7px}}
</style>

<script>
(function () {
  const root = document.getElementById('realisasiRbb2026');
  if (!root) return;
  const tabs = Array.from(root.querySelectorAll('[data-rrb26-tab]'));
  const panels = Array.from(root.querySelectorAll('[data-rrb26-panel]'));
  root.querySelectorAll('.rrb26-card').forEach((card, index) => {
    card.style.setProperty('--rrb26-delay', `${Math.min(index, 8) * 45}ms`);
  });
  const setActive = (key, replaceUrl) => {
    tabs.forEach((tab) => {
      const active = tab.dataset.rrb26Tab === key;
      tab.classList.toggle('active', active);
      tab.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    panels.forEach((panel) => {
      const active = panel.dataset.rrb26Panel === key;
      panel.hidden = !active;
      panel.classList.toggle('active', active);
    });
    if (replaceUrl) {
      const url = new URL(window.location.href);
      url.searchParams.set('view', key);
      window.history.replaceState({}, '', url.toString());
    }
  };
  tabs.forEach((tab) => tab.addEventListener('click', () => setActive(tab.dataset.rrb26Tab, true)));
})();
</script>

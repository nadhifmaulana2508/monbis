<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');
$requestedId = max(0, (int)($_GET['id'] ?? 0));
$requestedPeriod = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string)($_GET['period'] ?? '')) ? (string)$_GET['period'] : date('Y-m');
$requestedBranch = preg_match('/^0(?:0[1-9]|1[0-9]|2[0-8])$/', str_pad((string)($_GET['kode_cabang'] ?? ''), 3, '0', STR_PAD_LEFT)) ? str_pad((string)$_GET['kode_cabang'], 3, '0', STR_PAD_LEFT) : '';
$requestedCycleId = max(0, (int)($_GET['cycle_id'] ?? 0));
$requestedActualDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)($_GET['actual_date'] ?? '')) ? (string)$_GET['actual_date'] : '';
$requestedBasis = in_array((string)($_GET['nominal_field'] ?? ''), ['saldo_bank', 'baki_debet'], true) ? (string)$_GET['nominal_field'] : 'saldo_bank';
$canCreateCalculator = !empty($rbacInitialAccess['permissions']['page:komitmen_kalkulator']['can_create']);
?>
<style>
  #komitmenKalkulator { --kc-border:#dce5ef; --kc-muted:#64748b; --kc-ink:#17243a; width:100%; min-width:0; height:auto; min-height:calc(100dvh - 64px); overflow:visible; padding:10px; color:#334155; background:#f7f9fc; }
  #komitmenKalkulator * { box-sizing:border-box; }
  #komitmenKalkulator .kc-card { min-width:0; margin-bottom:10px; overflow:hidden; border:1px solid var(--kc-border); border-radius:11px; background:#fff; box-shadow:0 5px 18px rgba(15,23,42,.035); }
  #komitmenKalkulator .kc-head { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:12px; }
  #komitmenKalkulator .kc-head h2 { margin:0; color:var(--kc-ink); font-size:14px; font-weight:850; }
  #komitmenKalkulator .kc-head p,#komitmenKalkulator .kc-meta { margin:3px 0 0; color:var(--kc-muted); font-size:11px; line-height:1.45; }
  #komitmenKalkulator .kc-filter { display:grid; grid-template-columns:repeat(5,minmax(120px,1fr)); gap:9px; padding:12px; }
  #komitmenKalkulator .kc-layout { display:grid; grid-template-columns:minmax(0,1.3fr) minmax(340px,.9fr); align-items:start; gap:10px; }
  #komitmenKalkulator .kc-column { display:grid; align-content:start; gap:10px; min-width:0; }
  #komitmenKalkulator .kc-step { display:flex; align-items:center; gap:7px; margin:0; color:#1e293b; font-size:12px; font-weight:900; }
  #komitmenKalkulator .kc-step span { display:inline-flex; align-items:center; justify-content:center; width:21px; height:21px; border-radius:50%; background:#1e3a5f; color:#fff; font-size:10px; }
  #komitmenKalkulator .kc-subhead { margin:10px 12px 0; color:#334155; font-size:10px; font-weight:900; }
  #komitmenKalkulator .kc-opening-table { min-width:400px; }
  #komitmenKalkulator .kc-opening-table td:first-child { min-width:130px; font-weight:750; }
  #komitmenKalkulator .kc-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; padding:12px; }
  #komitmenKalkulator .kc-field { min-width:0; display:grid; align-content:start; gap:4px; }
  #komitmenKalkulator .kc-field label { color:#475569; font-size:10px; font-weight:850; }
  #komitmenKalkulator input,#komitmenKalkulator select,#komitmenKalkulator button { min-width:0; min-height:38px; border:1px solid #d5dfeb; border-radius:8px; padding:0 10px; background:#fff; color:#1e293b; font:inherit; font-size:12px; }
  #komitmenKalkulator input:focus,#komitmenKalkulator select:focus { border-color:#60a5fa; outline:3px solid #bfdbfe; }
  #komitmenKalkulator .kc-field small { min-height:28px; color:var(--kc-muted); font-size:10px; line-height:1.4; }
  #komitmenKalkulator .kc-warning { color:#b91c1c !important; font-weight:700; }
  #komitmenKalkulator .kc-alert { margin:0 12px 10px; padding:10px 12px; border:1px solid #bfdbfe; border-left:3px solid #2563eb; border-radius:8px; background:#eff6ff; color:#334155; font-size:11px; line-height:1.5; }
  #komitmenKalkulator .kc-alert.is-error { border-color:#fecaca; border-left-color:#dc2626; background:#fef2f2; color:#991b1b; }
  #komitmenKalkulator .kc-alert.is-warn { border-color:#fde68a; border-left-color:#d97706; background:#fffbeb; color:#92400e; }
  #komitmenKalkulator .kc-results { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:8px; padding:0 12px 12px; }
  #komitmenKalkulator .kc-results--compact { grid-template-columns:repeat(2,minmax(0,1fr)); }
  #komitmenKalkulator .kc-progress-list { display:grid; gap:9px; padding:0 12px 12px; }
  #komitmenKalkulator .kc-progress-head { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:4px; color:#475569; font-size:10px; }
  #komitmenKalkulator .kc-progress-head strong { color:#17243a; }
  #komitmenKalkulator .kc-meter { height:7px; overflow:hidden; border-radius:99px; background:#edf2f7; }
  #komitmenKalkulator .kc-meter i { display:block; width:0; height:100%; border-radius:inherit; background:#2f8b57; transition:width .18s ease; }
  #komitmenKalkulator .kc-meter.is-warning i { background:#d09c19; }
  #komitmenKalkulator .kc-balance-bars { display:grid; gap:10px; padding:0 12px 10px; }
  #komitmenKalkulator .kc-bar-row { display:grid; grid-template-columns:72px minmax(0,1fr); align-items:center; gap:8px; }
  #komitmenKalkulator .kc-bar-label { color:#64748b; font-size:9px; }
  #komitmenKalkulator .kc-bar { display:flex; height:17px; overflow:hidden; border-radius:5px; background:#eef2f6; }
  #komitmenKalkulator .kc-bar span { min-width:0; transition:width .18s ease; }
  #komitmenKalkulator .kc-bar .is-l { background:#4d9364; }
  #komitmenKalkulator .kc-bar .is-par { background:#d2a934; }
  #komitmenKalkulator .kc-bar .is-npl { background:#ad443b; }
  #komitmenKalkulator .kc-legend { display:flex; flex-wrap:wrap; gap:10px; padding:0 12px 12px; color:#64748b; font-size:9px; }
  #komitmenKalkulator .kc-legend span { display:inline-flex; align-items:center; gap:4px; }
  #komitmenKalkulator .kc-legend i { width:8px; height:8px; border-radius:2px; }
  #komitmenKalkulator .kc-note { width:100%; min-height:100px !important; padding:9px 10px !important; resize:vertical; line-height:1.45; }
  #komitmenKalkulator .kc-result { padding:10px; border:1px solid #e4eaf2; border-radius:9px; background:#fbfcfe; }
  #komitmenKalkulator .kc-result span { display:block; color:var(--kc-muted); font-size:10px; font-weight:750; }
  #komitmenKalkulator .kc-result strong { display:block; margin-top:4px; color:var(--kc-ink); font-size:19px; line-height:1.2; }
  #komitmenKalkulator .kc-result small { display:block; margin-top:3px; color:var(--kc-muted); font-size:10px; }
  #komitmenKalkulator .kc-strategies { display:grid; grid-template-columns:1fr; gap:8px; padding:0 12px 12px; }
  #komitmenKalkulator .kc-strategy { min-width:0; padding:10px; border:1px solid #e1e8f0; border-radius:9px; background:#fff; }
  #komitmenKalkulator .kc-strategy.is-recommended { border-color:#f2c14e; background:#fffdf5; }
  #komitmenKalkulator .kc-strategy-head { display:flex; align-items:flex-start; justify-content:space-between; gap:6px; }
  #komitmenKalkulator .kc-strategy h3 { margin:0; color:#26364d; font-size:11px; font-weight:850; }
  #komitmenKalkulator .kc-strategy-status { flex:0 0 auto; padding:3px 6px; border-radius:999px; background:#f1f5f9; color:#475569; font-size:8px; font-weight:900; }
  #komitmenKalkulator .kc-strategy-status.is-good { background:#dcfce7; color:#166534; }
  #komitmenKalkulator .kc-strategy p { margin:7px 0; color:#64748b; font-size:10px; line-height:1.45; }
  #komitmenKalkulator .kc-strategy ul { margin:5px 0 9px; padding-left:16px; color:#475569; font-size:10px; line-height:1.55; }
  #komitmenKalkulator .kc-strategy .kc-strategy-footer { display:grid; gap:7px; padding-top:7px; border-top:1px solid #edf1f5; }
  #komitmenKalkulator .kc-strategy .kc-strategy-footer strong { color:#17243a; }
  #komitmenKalkulator .kc-strategy button { min-height:32px; padding:0 9px; font-size:10px; }
  #komitmenKalkulator .kc-table-wrap { width:100%; overflow:auto; }
  #komitmenKalkulator table { width:100%; min-width:680px; border-collapse:separate; border-spacing:0; font-size:10px; }
  #komitmenKalkulator .kc-comparison-table { min-width:1080px; }
  #komitmenKalkulator th,#komitmenKalkulator td { padding:8px; border-bottom:1px solid #e9eef5; text-align:right; white-space:nowrap; }
  #komitmenKalkulator th { position:sticky; top:0; z-index:2; background:#f5f8fc; color:#475569; font-size:9px; font-weight:850; }
  #komitmenKalkulator th:first-child,#komitmenKalkulator td:first-child { position:sticky; left:0; z-index:1; background:#fff; text-align:left; }
  #komitmenKalkulator th:first-child { z-index:3; background:#f5f8fc; }
  #komitmenKalkulator tbody tr:last-child td { border-bottom:0; }
  #komitmenKalkulator .kc-actions { display:flex; align-items:center; justify-content:flex-end; flex-wrap:wrap; gap:7px; padding:10px 12px; }
  #komitmenKalkulator .kc-save-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 12px; }
  #komitmenKalkulator .kc-save-footer .kc-actions { padding:0; }
  #komitmenKalkulator button { padding:0 14px; font-weight:800; cursor:pointer; }
  #komitmenKalkulator .kc-primary { border-color:#2563eb; background:#2563eb; color:#fff; }
  #komitmenKalkulator button:disabled { opacity:.55; cursor:wait; }
  #komitmenKalkulator .kc-link { color:#2455bd; font-size:11px; font-weight:800; }
  #komitmenKalkulator .kc-status { padding:5px 9px; border-radius:999px; background:#dcfce7; color:#166534; font-size:9px; font-weight:900; white-space:nowrap; }
  #komitmenKalkulator .kc-status.is-bad { background:#fee2e2; color:#991b1b; }
  #komitmenKalkulator .kc-status.is-warn { background:#fef3c7; color:#92400e; }
  #komitmenKalkulator .kc-status.is-muted { background:#f1f5f9; color:#475569; }
  #komitmenKalkulator :is(button,input,select,textarea,a):focus-visible { outline:3px solid #93c5fd; outline-offset:2px; }
  @media(max-width:980px) { #komitmenKalkulator .kc-layout { grid-template-columns:minmax(0,1fr) minmax(300px,.85fr); } }
  @media(max-width:820px) { #komitmenKalkulator .kc-filter { grid-template-columns:repeat(2,minmax(0,1fr)); } #komitmenKalkulator .kc-layout { grid-template-columns:1fr; } #komitmenKalkulator .kc-right { grid-row:1; } #komitmenKalkulator .kc-results { grid-template-columns:repeat(2,minmax(0,1fr)); } }
  @media(max-width:560px) {
    #komitmenKalkulator { padding:8px 6px 16px; }
    #komitmenKalkulator .kc-head { align-items:flex-start; }
    #komitmenKalkulator .kc-filter,#komitmenKalkulator .kc-grid { grid-template-columns:1fr; gap:8px; padding:9px; }
    #komitmenKalkulator input,#komitmenKalkulator select,#komitmenKalkulator button { min-height:44px; font-size:14px; }
    #komitmenKalkulator .kc-field label { font-size:11px; }
    #komitmenKalkulator .kc-field small { min-height:0; }
    #komitmenKalkulator .kc-alert { margin:0 9px 9px; }
    #komitmenKalkulator .kc-results { gap:6px; padding:0 9px 9px; }
    #komitmenKalkulator .kc-strategies { grid-template-columns:1fr; padding:0 9px 9px; }
    #komitmenKalkulator .kc-result { padding:8px; }
    #komitmenKalkulator .kc-result strong { font-size:17px; }
    #komitmenKalkulator .kc-actions { flex-direction:column-reverse; padding:0 9px 9px; }
    #komitmenKalkulator .kc-actions button { width:100%; }
    #komitmenKalkulator .kc-save-footer { align-items:stretch; flex-direction:column; padding:9px; }
    #komitmenKalkulator .kc-save-footer .kc-actions { display:grid; grid-template-columns:1fr; width:100%; }
    #komitmenKalkulator .kc-bar-row { grid-template-columns:62px minmax(0,1fr); }
  }
  #komitmenKalkulator .kc-card { border-radius:8px; box-shadow:none; }
  #komitmenKalkulator .kc-projection-card { border-top:2px solid #2563eb; }
  #komitmenKalkulator .kc-field small { min-height:0; }
  #komitmenKalkulator .kc-result strong,
  #komitmenKalkulator th:not(:first-child),
  #komitmenKalkulator td:not(:first-child) { font-variant-numeric:tabular-nums; }
  #komitmenKalkulator .kc-note-wrap { display:grid; gap:5px; padding:0 12px 12px; }
  #komitmenKalkulator .kc-note-wrap label { color:#475569; font-size:10px; font-weight:850; cursor:pointer; }
  #komitmenKalkulator .kc-note { min-height:92px; border:1px solid #d5dfeb; border-radius:8px; background:#fff; color:#1e293b; font:inherit; font-size:12px; }
  #komitmenKalkulator .kc-note:focus { border-color:#60a5fa; outline:3px solid #bfdbfe; }
  #komitmenKalkulator input[readonly] { background:#f8fafc; color:#475569; cursor:default; }
  #komitmenKalkulator :is(button,a,select,label) { cursor:pointer; }
  #komitmenKalkulator :is(button,input,select,textarea):disabled { cursor:not-allowed; }
  #komitmenKalkulator button:not(:disabled):hover { filter:brightness(.96); }
  #komitmenKalkulator .kc-actions button { min-height:36px; }
  #komitmenKalkulator .kc-field label,#komitmenKalkulator .kc-field small,#komitmenKalkulator .kc-note-wrap label,
  #komitmenKalkulator .kc-result span,#komitmenKalkulator .kc-result small,#komitmenKalkulator .kc-strategy p,
  #komitmenKalkulator .kc-strategy ul { font-size:11px; }
  #komitmenKalkulator .kc-strategy-status { font-size:9px; }
  #komitmenKalkulator .kc-bar-label,#komitmenKalkulator .kc-legend { font-size:10px; }
  #komitmenKalkulator table { font-size:11px; }
  #komitmenKalkulator th { font-size:10px; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator { --kc-border:#334155; --kc-muted:#a7b4c5; --kc-ink:#f8fafc; color:#e2e8f0; background:#0b1220; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-card { border-color:#334155; background:#111827; color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-head h2,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-step,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-subhead,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-result strong,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-progress-head strong,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-strategy h3,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-strategy-footer strong { color:#f8fafc; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-head p,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-meta,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-field label,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-field small,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-note-wrap label,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-strategy p,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-strategy ul { color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator input,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator select,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator textarea { border-color:#475569; background:#0f172a; color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator input[readonly] { background:#1e293b; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-result,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-strategy { border-color:#334155; background:#0f172a; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-strategy.is-recommended { border-color:#a16207; background:#1c1917; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator th { border-color:#334155; background:#1e293b; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator td { border-color:#334155; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator td:first-child { background:#111827; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-alert { border-color:#1e3a5f; border-left-color:#60a5fa; background:#172554; color:#dbeafe; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-alert.is-error { border-color:#7f1d1d; border-left-color:#f87171; background:#450a0a; color:#fecaca; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-alert.is-warn { border-color:#854d0e; border-left-color:#fbbf24; background:#422006; color:#fde68a; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-status.is-warn { background:#422006; color:#fde68a; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-status.is-muted { background:#1e293b; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenKalkulator .kc-warning { color:#fca5a5 !important; }
  @media(max-width:560px) {
    #komitmenKalkulator .kc-filter,#komitmenKalkulator .kc-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
    #komitmenKalkulator .kc-scenario-field { grid-column:1/-1; }
    #komitmenKalkulator .kc-note-wrap { padding:0 9px 9px; }
    #komitmenKalkulator .kc-save-footer .kc-actions { grid-template-columns:repeat(2,minmax(0,1fr)); }
    #komitmenKalkulator .kc-save-footer .kc-actions button,#komitmenKalkulator .kc-strategy button { min-height:44px; padding:0 7px; font-size:12px; }
    #komitmenKalkulator .kc-link { display:inline-flex; align-items:center; min-height:44px; }
  }
  @media(max-width:360px) {
    #komitmenKalkulator .kc-filter,#komitmenKalkulator .kc-grid { grid-template-columns:1fr; }
    #komitmenKalkulator .kc-scenario-field { grid-column:auto; }
  }
  @media(prefers-reduced-motion:reduce) { #komitmenKalkulator *,#komitmenKalkulator *::before,#komitmenKalkulator *::after { scroll-behavior:auto !important; transition:none !important; animation:none !important; } }
</style>

<main id="komitmenKalkulator" class="mb-report-page">
  <header class="mb-page-header kc-card kc-head">
    <div class="mb-page-header__copy"><h1 class="mb-page-header__title">Kalkulator Komitmen</h1><p class="mb-page-header__subtitle">Masukkan asumsi; saldo akhir, RR, dan NPL dihitung otomatis sebelum disimpan.</p></div>
    <span id="kcStatus" class="kc-status">MEMUAT POSISI</span>
  </header>
  <div id="kcMessage" class="kc-alert" role="status" aria-live="polite">Memuat filter dan posisi saldo…</div>
  <section class="kc-card" aria-label="Identitas komitmen">
    <div class="kc-filter">
      <div class="kc-field"><label for="kcPeriod">Periode proyeksi EOM</label><input id="kcPeriod" type="month" required></div>
      <div class="kc-field"><label for="kcBranch">Cabang</label><select id="kcBranch"><option value="">Memuat cabang…</option></select></div>
      <div class="kc-field"><label for="kcCycle">Siklus komitmen</label><select id="kcCycle"><option value="">Memuat siklus...</option></select></div>
      <div class="kc-field"><label for="kcActualDate">Tanggal actual sampai</label><input id="kcActualDate" type="date" required></div>
      <div class="kc-field"><label for="kcBasis">Basis nominal</label><select id="kcBasis"><option value="saldo_bank">Saldo Bank</option><option value="baki_debet">Baki Debet</option></select></div>
      <div class="kc-field kc-scenario-field"><label for="kcScenario">Nama skenario</label><input id="kcScenario" type="text" maxlength="120" placeholder="Nama rencana cabang"></div>
    </div>
    <div id="kcPositionMeta" class="kc-alert">Posisi awal akan ditampilkan setelah cabang dan periode dimuat.</div>
  </section>
  <div class="kc-layout">
    <div class="kc-column kc-left">
      <section class="kc-card" aria-labelledby="kcOpeningTitle">
        <div class="kc-head"><div><h2 id="kcOpeningTitle" class="kc-step"><span>1</span> Closing M-1 dan actual posisi komitmen</h2><p id="kcOpeningMeta">Saldo closing dan actual tanggal pilihan akan tampil setelah posisi dimuat.</p></div></div>
        <div class="kc-table-wrap"><table class="kc-opening-table"><thead><tr><th>Komponen</th><th>Closing M-1</th><th>Actual posisi</th></tr></thead><tbody id="kcOpeningRows"><tr><td colspan="3" style="text-align:center;color:#64748b">Memuat posisi…</td></tr></tbody></table></div>
      </section>
      <section id="kcInputSection" class="kc-card" aria-labelledby="kcInputTitle">
        <div class="kc-head"><div><h2 id="kcInputTitle" class="kc-step"><span>2</span> Rencana proyeksi sampai EOM</h2><p id="kcInputSubtitle">Proyeksi dimulai dari Closing M-1. Asumsi actual menjadi angka awal yang bisa disesuaikan.</p></div></div>
        <h3 class="kc-subhead">Growth dan pergerakan L</h3>
        <div class="kc-grid">
          <div class="kc-field"><label for="kcGrowth">Growth ke DPD 0 / L</label><input id="kcGrowth" data-nominal type="text" inputmode="decimal" value="0"><small>Menambah atau mengurangi DPD 0, saldo L, dan total KYD.</small><small id="kcGrowthWarning" class="kc-warning"></small></div>
          <div class="kc-field"><label for="kcBtc">BTC: DPD 1–30 ke DPD 0</label><input id="kcBtc" data-nominal type="text" inputmode="numeric" value="0"><small>Menggeser saldo di dalam L; total KYD tetap.</small><small id="kcBtcWarning" class="kc-warning"></small></div>
          <div class="kc-field"><label for="kcMigration">Migrasi DPD 0 ke DPD 1–30</label><input id="kcMigration" data-nominal type="text" inputmode="numeric" value="0"><small>Berpindah di dalam L; total KYD tetap.</small><small id="kcMigrationWarning" class="kc-warning"></small></div>
          <div class="kc-field"><label for="kcFlowPar">Flow PAR ke NPL</label><input id="kcFlowPar" data-nominal type="text" inputmode="numeric" value="0"><small>Mengurangi saldo L atau DP, lalu menambah KL, D, dan M.</small><small id="kcFlowWarning" class="kc-warning"></small></div>
        </div>
        <h3 class="kc-subhead">Rencana recovery NPL · KL + D + M</h3>
        <div class="kc-grid">
          <div class="kc-field"><label for="kcRecovery">Recovery / pelunasan NPL</label><input id="kcRecovery" data-nominal type="text" inputmode="numeric" value="0"><small>Mengurangi KL dahulu, lalu D dan M; menurunkan total KYD.</small><small id="kcRecoveryWarning" class="kc-warning"></small></div>
          <div class="kc-field"><label for="kcUpgrade">Upgrade NPL ke DPD 0</label><input id="kcUpgrade" data-nominal type="text" inputmode="numeric" value="0"><small>Mengurangi NPL dan menambah DPD 0; KYD tetap.</small><small id="kcUpgradeWarning" class="kc-warning"></small></div>
          <div class="kc-field"><label for="kcBackflow">Perbaikan NPL ke DP</label><input id="kcBackflow" data-nominal type="text" inputmode="numeric" value="0"><small>Mengurangi NPL dan menambah DP; KYD tetap.</small><small id="kcBackflowWarning" class="kc-warning"></small></div>
        </div>
        <h3 class="kc-subhead">Target periode</h3>
        <div class="kc-grid">
          <div class="kc-field"><label for="kcTargetRr">Target RR pusat (%)</label><input id="kcTargetRr" type="number" min="0" max="100" step="0.01" value="74.15" readonly><small id="kcTargetRrNote">Target ditetapkan pusat saat membuka siklus.</small></div>
          <div class="kc-field"><label for="kcTargetNpl">Batas NPL pusat (%)</label><input id="kcTargetNpl" type="number" min="0" max="100" step="0.01" value="14.90" readonly><small id="kcTargetNplNote">Target ditetapkan pusat saat membuka siklus.</small></div>
        </div>
        <div id="kcIssueMessage" class="kc-alert" hidden></div>
      </section>
      <section class="kc-card" aria-labelledby="kcNotesTitle">
        <div class="kc-head"><div><h2 id="kcNotesTitle" class="kc-step"><span>3</span> Rencana aksi dan komitmen</h2><p>Catat langkah yang akan dikerjakan cabang.</p></div></div>
        <div class="kc-note-wrap"><label for="kcCommitmentNote">Rencana aksi cabang</label><textarea id="kcCommitmentNote" class="kc-note" maxlength="4000" placeholder="Tulis langkah, penanggung jawab, dan hasil yang dituju sampai akhir bulan."></textarea></div>
      </section>
    </div>
    <aside class="kc-column kc-right">
      <section class="kc-card kc-projection-card" aria-labelledby="kcProjectionTitle">
        <div class="kc-head"><div><h2 id="kcProjectionTitle">Proyeksi akhir bulan</h2><p>Saldo akhir dihitung otomatis dari asumsi.</p></div></div>
        <div class="kc-results kc-results--compact">
           <div class="kc-result"><span>RR · DPD 0 / KYD</span><strong id="kcRR">-</strong><small id="kcRrTarget">Target -</small></div>
           <div class="kc-result"><span>NPL Gross</span><strong id="kcNPL">-</strong><small id="kcNplTarget">Target -</small></div>
           <div class="kc-result"><span>DPD 0</span><strong id="kcDPD0">-</strong><small>Saldo lancar tanpa tunggakan</small></div>
           <div class="kc-result"><span>Total KYD</span><strong id="kcKYD">-</strong><small>L + DP + KL + D + M</small></div>
        </div>
        <div class="kc-progress-list">
           <div><div class="kc-progress-head"><span>RR terhadap target</span><strong id="kcRRProgressLabel">-</strong></div><div id="kcRrMeter" class="kc-meter"><i id="kcRRProgress"></i></div></div>
           <div><div class="kc-progress-head"><span>NPL terhadap batas</span><strong id="kcNplProgressLabel">-</strong></div><div id="kcNplMeter" class="kc-meter"><i id="kcNPLProgress"></i></div></div>
        </div>
      </section>
      <section class="kc-card" aria-labelledby="kcBalanceTitle">
        <div class="kc-head"><div><h2 id="kcBalanceTitle">Mutasi saldo</h2><p>DPD 0, PAR non-NPL, dan NPL.</p></div></div>
        <div id="kcBalanceBars" class="kc-balance-bars"></div>
        <div class="kc-legend"><span><i style="background:#4d9364"></i>DPD 0</span><span><i style="background:#d2a934"></i>PAR non-NPL</span><span><i style="background:#ad443b"></i>NPL</span></div>
      </section>
      <section class="kc-card" aria-labelledby="kcStrategyTitle">
        <div class="kc-head"><div><h2 id="kcStrategyTitle">Kebutuhan dan strategi menuju target</h2><p id="kcTargetGap">Isi asumsi untuk melihat tambahan aksi yang dibutuhkan.</p></div></div>
        <div id="kcRecommendations" class="kc-strategies" aria-live="polite"></div>
      </section>
    </aside>
  </div>
  <section id="kcComparisonSection" class="kc-card" aria-labelledby="kcComparisonTitle">
     <div class="kc-head"><div><h2 id="kcComparisonTitle">Perbandingan closing, proyeksi, actual, dan selisih</h2><p id="kcComparisonMeta">-</p></div></div>
    <div class="kc-table-wrap"><table class="kc-comparison-table"><thead><tr><th>Komponen</th><th>Closing M-1</th><th>Proyeksi EOM</th><th>Actual posisi komitmen</th><th>Gap actual − closing</th><th>Gap proyeksi − closing</th><th>Gap proyeksi − actual</th></tr></thead><tbody id="kcComparisonRows"><tr><td colspan="7" style="text-align:center;color:#64748b">Menunggu posisi.</td></tr></tbody></table></div>
  </section>
  <footer class="kc-card kc-save-footer"><a class="kc-link" href="<?= htmlspecialchars(rtrim((string)BASE_APP, '/') . '/komitmen_rekap', ENT_QUOTES, 'UTF-8') ?>">Kembali ke Rekap Komitmen</a><div class="kc-actions"><button id="kcSaveDraft" type="button" disabled>Simpan draf</button><button id="kcSubmit" class="kc-primary" type="button" disabled>Simpan &amp; ajukan</button></div></footer>
</main>

<script>
(() => {
  const el=id=>document.getElementById(id);
  const apiBase=`${window.BASE_APP||''}/api/komitmen`;
  const apiCall=(url,options={})=>window.apiFetch?window.apiFetch(url,options):fetch(url,{credentials:'include',...options});
  const canCreate=<?= $canCreateCalculator ? 'true' : 'false' ?>;
  const numberFmt=new Intl.NumberFormat('id-ID',{maximumFractionDigits:0});
  const pct=v=>v==null||!Number.isFinite(Number(v))?'-':`${(Number(v)*100).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2})}%`;
  const money=v=>numberFmt.format(Number(v||0));
  const signedDifference=(value,type)=>{if(value==null||!Number.isFinite(Number(value)))return '-';const delta=Number(value),arrow=delta>0?'↑ ':delta<0?'↓ ':'→ ';return arrow+(type==='ratio'?pct(Math.abs(delta)):money(Math.abs(delta)));};
  const state={metadata:null,cycle:null,snapshot:null,projection:null,busy:false,recordId:<?= $requestedId ?>,recommendations:{}};
  const initial={period:<?= json_encode($requestedPeriod) ?>,branch:<?= json_encode($requestedBranch) ?>,cycleId:<?= $requestedCycleId ?>,actualDate:<?= json_encode($requestedActualDate) ?>,basis:<?= json_encode($requestedBasis) ?>};
  function syncPageUrl(){const params=new URLSearchParams({period:el('kcPeriod').value,kode_cabang:el('kcBranch').value,cycle_id:el('kcCycle').value,actual_date:el('kcActualDate').value,nominal_field:el('kcBasis').value});if(state.recordId)params.set('id',String(state.recordId));history.replaceState(null,'',`${location.pathname}?${params}`);}
  function clearAssumptions(){document.querySelectorAll('#komitmenKalkulator [data-nominal]').forEach(input=>input.value='0');el('kcScenario').value='';el('kcCommitmentNote').value='';}
  function setInputsEnabled(enabled){document.querySelectorAll('#komitmenKalkulator [data-nominal]').forEach(input=>input.disabled=!enabled);el('kcScenario').disabled=!enabled;el('kcCommitmentNote').disabled=!enabled;}
  const read=id=>{const raw=String(el(id).value||'');const digits=raw.replace(/\D/g,'');const value=digits?Number(digits):0;return id==='kcGrowth'&&raw.trim().startsWith('-')?-value:value;};
  function formatNominalInput(input){const cursor=input.selectionStart??input.value.length;const digitsBefore=input.value.slice(0,cursor).replace(/\D/g,'').length;const negative=input.id==='kcGrowth'&&input.value.trim().startsWith('-');const digits=input.value.replace(/\D/g,'');const formatted=digits?numberFmt.format(Number(digits)):'';input.value=(negative?'-':'')+formatted;let caret=negative?1:0,seen=0;while(caret<input.value.length&&seen<digitsBefore){if(/\d/.test(input.value[caret]))seen++;caret++;}try{input.setSelectionRange(caret,caret);}catch(error){}}
  function applyActualMovementDefaults(movements){const map={growth:'kcGrowth',btc:'kcBtc',migration:'kcMigration',flow_par:'kcFlowPar',recovery_npl:'kcRecovery',npl_upgrade:'kcUpgrade',npl_backflow:'kcBackflow'};for(const [key,id] of Object.entries(map)){const value=Number(movements?.[key]??0);el(id).value=money(Number.isFinite(value)?Math.round(value):0);}}
  const apiData=async(url,options={})=>{const controller=new AbortController(),timer=setTimeout(()=>controller.abort(),60000);try{const response=await apiCall(url,{...options,signal:controller.signal});const json=await response.json();if(!response.ok||json.status!==200)throw new Error(json.message||'Permintaan gagal.');return json.data;}catch(error){if(error.name==='AbortError')throw new Error('Permintaan melewati 60 detik. Periksa koneksi lalu coba lagi.');throw error;}finally{clearTimeout(timer);}};
  function message(value,type='') { const box=el('kcMessage');box.textContent=value;box.hidden=!value;box.classList.toggle('is-error',type==='error');box.classList.toggle('is-warn',type==='warn'); }
  function warning(id,value){el(id).textContent=value||'';}
  function addOption(select,value,label){const option=document.createElement('option');option.value=value;option.textContent=label;select.append(option);}
  function posKyd(pos){return Number(pos?.l||0)+Number(pos?.dp||0)+Number(pos?.kl||0)+Number(pos?.d||0)+Number(pos?.m||0);}
  function posNpl(pos){return Number(pos?.kl||0)+Number(pos?.d||0)+Number(pos?.m||0);}
  function ratio(val,base){return base>0?val/base:null;}
  function renderOpening(){
    if(!state.snapshot)return;
    const closing=state.snapshot.closing,position=state.snapshot.position;
    const rows=[
      ['DPD 0',closing.dpd0,position.dpd0],['DPD 1–30 (L)',Number(closing.l||0)-Number(closing.dpd0||0),Number(position.l||0)-Number(position.dpd0||0)],
      ['Lancar (L)',closing.l,position.l],['Perhatian (DP)',closing.dp,position.dp],['KL',closing.kl,position.kl],['Diragukan (D)',closing.d,position.d],['Macet (M)',closing.m,position.m],
      ['NPL Gross',posNpl(closing),posNpl(position)],['Total KYD',posKyd(closing),posKyd(position)],
      ['RR',ratio(Number(closing.dpd0||0),posKyd(closing)),ratio(Number(position.dpd0||0),posKyd(position)),'ratio'],
      ['NPL Gross (%)',ratio(posNpl(closing),posKyd(closing)),ratio(posNpl(position),posKyd(position)),'ratio']
    ];
    const body=el('kcOpeningRows');body.replaceChildren();
    for(const [label,closeValue,positionValue,type] of rows){const tr=document.createElement('tr');const name=document.createElement('td');name.textContent=label;tr.append(name);for(const value of [closeValue,positionValue]){const cell=document.createElement('td');cell.textContent=value==null?'-':type==='ratio'?pct(value):money(value);tr.append(cell);}body.append(tr);}
  }
  function renderBalanceBars(){
    if(!state.snapshot)return;
    const closing=state.snapshot.closing,actual=state.snapshot.position,forecast=state.projection?.balances;
    const bucket=pos=>({l:Math.max(0,Number(pos?.dpd0||0)),par:Math.max(0,Number(pos?.dpd1_30??(Number(pos?.l||0)-Number(pos?.dpd0||0)))+Number(pos?.dp||0)),npl:Math.max(0,Number(pos?.kl||0)+Number(pos?.d||0)+Number(pos?.m||0))});
    const rows=[{label:`Closing ${state.snapshot.closing_date}`,values:bucket(closing)},{label:`Actual ${state.snapshot.position_date}`,values:bucket(actual)},{label:'Proyeksi EOM',values:forecast?{l:Math.max(0,Number(forecast.dpd0||0)),par:Math.max(0,Number(forecast.dpd1_30||0)+Number(forecast.dp||0)),npl:Math.max(0,Number(forecast.npl||0))}:null}];
    const host=el('kcBalanceBars');host.replaceChildren();
    for(const row of rows){const line=document.createElement('div');line.className='kc-bar-row';const label=document.createElement('span');label.className='kc-bar-label';label.textContent=row.label;const bar=document.createElement('div');bar.className='kc-bar';
      if(row.values){const total=row.values.l+row.values.par+row.values.npl;for(const [key,color] of [['l','is-l'],['par','is-par'],['npl','is-npl']]){const segment=document.createElement('span');segment.className=color;segment.style.width=`${total>0?Math.max(0,row.values[key]/total*100):0}%`;bar.append(segment);}}
      line.append(label,bar);host.append(line);
    }
  }
  function fillMetadata(data){
    state.metadata=data;
    const select=el('kcBranch');select.replaceChildren();
    (data.branches||[]).forEach(row=>addOption(select,String(row.code).padStart(3,'0'),`${String(row.code).padStart(3,'0')} · ${row.name}`));
    const selected=initial.branch||String(data.selected_branch||'');
    if(selected&&[...select.options].some(option=>option.value===selected))select.value=selected;
    select.disabled=!data.allow_branch_selection;
    el('kcPeriod').value=initial.period||data.period;
    el('kcBasis').value=initial.basis;
    const cycles=el('kcCycle');cycles.replaceChildren();
    (data.cycles||[]).forEach(cycle=>addOption(cycles,String(cycle.id),`Siklus ${cycle.cycle_no} · ${cycle.cycle_name}${cycle.status==='open'?' · terbuka':''}`));
    const wanted=initial.cycleId||data.cycle?.id||'';if(wanted&&[...cycles.options].some(option=>option.value===String(wanted)))cycles.value=String(wanted);
    setCycle(data.cycles?.find(cycle=>String(cycle.id)===cycles.value)||data.cycle);
    const actual=el('kcActualDate'),month=el('kcPeriod').value,last=new Date(Number(month.slice(0,4)),Number(month.slice(5,7)),0).getDate();actual.min=month+'-01';actual.max=month+'-'+String(last).padStart(2,'0');actual.value=initial.actualDate||data.latest_actual_date||'';
  }
  function setCycle(cycle){state.cycle=cycle||null;el('kcTargetRr').value=cycle?.target_rr??'';el('kcTargetNpl').value=cycle?.target_npl??'';el('kcTargetRrNote').textContent=cycle?`Target pusat · ${cycle.cycle_name}`:'Target ditetapkan pusat saat membuka siklus.';el('kcTargetNplNote').textContent=cycle?`Batas pusat · ${cycle.cycle_name}`:'Target ditetapkan pusat saat membuka siklus.';}
  function fillRecord(record){
    initial.period=String(record.period_month||'').slice(0,7);initial.branch=String(record.branch_code||'');initial.cycleId=Number(record.cycle_id||0);initial.basis=record.nominal_field;
    el('kcPeriod').value=initial.period;el('kcBranch').value=initial.branch;el('kcCycle').value=String(record.cycle_id||'');el('kcBasis').value=record.nominal_field;el('kcScenario').value=record.scenario_name||'';el('kcActualDate').value=record.inputs?.actual_date_selected||el('kcActualDate').value;setCycle(record.cycle||state.cycle);
    const values=record.inputs||{};
    const map={growth:'kcGrowth',btc:'kcBtc',migration:'kcMigration',flow_par:'kcFlowPar',recovery_npl:'kcRecovery',npl_upgrade:'kcUpgrade',npl_backflow:'kcBackflow'};
    for(const [key,id] of Object.entries(map))el(id).value=money(values[key]||0);
    if(record.cycle){el('kcTargetRr').value=record.cycle.target_rr;el('kcTargetNpl').value=record.cycle.target_npl;}
    el('kcCommitmentNote').value=values.commitment_note||'';
    message(`Rekaman #${record.id} dimuat. Saat disimpan, revisi baru akan dibuat; komitmen tersimpan tidak ditimpa.`);
  }
  function renderTable(closing,position,projection){
    const closeKyd=posKyd(closing),positionKyd=posKyd(position),projected=projection?.balances||{};
    const values=[
      ['DPD 0',closing?.dpd0,projected.dpd0,position?.dpd0],
      ['DPD 1–30 (L)',Number(closing?.l||0)-Number(closing?.dpd0||0),projected.dpd1_30,Number(position?.l||0)-Number(position?.dpd0||0)],
      ['Lancar (L)',closing?.l,projected.l,position?.l],['Perhatian (DP)',closing?.dp,projected.dp,position?.dp],
      ['KL',closing?.kl,projected.kl,position?.kl],['Diragukan (D)',closing?.d,projected.d,position?.d],['Macet (M)',closing?.m,projected.m,position?.m],
      ['NPL Gross',posNpl(closing),projected.npl,posNpl(position)],['KYD',closeKyd,projected.kyd,positionKyd],
      ['RR',ratio(Number(closing?.dpd0||0),closeKyd),projection?.ratios?.rr,ratio(Number(position?.dpd0||0),positionKyd),'ratio'],
      ['NPL Gross (%)',ratio(posNpl(closing),closeKyd),projection?.ratios?.npl,ratio(posNpl(position),positionKyd),'ratio']
    ];
    const body=el('kcComparisonRows');body.replaceChildren();
    for(const [label,closeValue,projectionValue,actualValue,type] of values){const tr=document.createElement('tr');const name=document.createElement('td');name.textContent=label;tr.append(name);for(const value of [closeValue,projectionValue,actualValue]){const td=document.createElement('td');td.textContent=value==null?'-':type==='ratio'?pct(value):money(value);tr.append(td);}for(const delta of [actualValue==null||closeValue==null?null:Number(actualValue)-Number(closeValue),projectionValue==null||closeValue==null?null:Number(projectionValue)-Number(closeValue),projectionValue==null||actualValue==null?null:Number(projectionValue)-Number(actualValue)]){const td=document.createElement('td');td.textContent=signedDifference(delta,type);tr.append(td);}body.append(tr);}
    el('kcComparisonMeta').textContent=`Closing ${state.snapshot.closing_date} · actual ${state.snapshot.position_date} · proyeksi ${state.snapshot.projection_date}`;
  }
  function renderRecommendations(targetRr,targetNpl,base,upgradeCapacity,issues){
    const host=el('kcRecommendations');host.replaceChildren();state.recommendations={};
    if(!Number.isFinite(targetRr)||!Number.isFinite(targetNpl)||targetRr<0||targetRr>1||targetNpl<0||targetNpl>1){el('kcTargetGap').textContent='Masukkan target RR dan NPL antara 0% sampai 100%.';return;}
    if(issues.length){el('kcTargetGap').textContent='Perbaiki input yang membuat saldo proyeksi tidak valid untuk melihat kebutuhan target.';return;}
    const rrNow=base.kyd>0?base.dpd0/base.kyd:null,nplNow=base.kyd>0?base.npl/base.kyd:null;
    const rrNeed=rrNow!==null&&rrNow<targetRr-1e-10?Math.ceil(Math.max(0,targetRr*base.kyd-base.dpd0-1e-7)):0;
    const nplNeed=nplNow!==null&&nplNow>targetNpl+1e-10?Math.ceil(Math.max(0,base.npl-targetNpl*base.kyd-1e-7)):0;
    el('kcTargetGap').textContent=`Tambahan DPD 0 untuk RR: ${money(rrNeed)} · penurunan NPL untuk target: ${money(nplNeed)}.`;
    const evaluate=({upgrade=0,backflow=0,btc=0,preventMigration=0,payoff=0}={})=>{
      const kyd=base.kyd-payoff,npl=base.npl-upgrade-backflow-payoff,dpd0=base.dpd0+upgrade+btc+preventMigration;
      const rr=kyd>0?dpd0/kyd:null,nplRatio=kyd>0?npl/kyd:null;
      return {rr,nplRatio,ok:rr!==null&&nplRatio!==null&&rr+1e-10>=targetRr&&nplRatio<=targetNpl+1e-10};
    };
    const makeCard=(id,title,description,items,actions,result,capacityIssue=false,recommended=false)=>{
      const hasActions=Object.values(actions).some(value=>value>0.01);
      const card=document.createElement('article');card.className=`kc-strategy${recommended?' is-recommended':''}`;
      const head=document.createElement('div');head.className='kc-strategy-head';head.append(textNode('h3',title));
      const status=textNode('span',result.ok?'DUA TARGET TERCAPAI':capacityIssue?'CEK KAPASITAS':'BELUM DUA TARGET','kc-strategy-status');if(result.ok)status.classList.add('is-good');head.append(status);card.append(head);
      card.append(textNode('p',description));
      const list=document.createElement('ul');const active=items.filter(item=>item.amount>0.01);
      for(const item of active){const li=document.createElement('li');li.textContent=`${item.label}: ${money(item.amount)}`;list.append(li);}
      if(!active.length)list.append(textNode('li','Tidak perlu aksi tambahan.'));card.append(list);
      const footer=document.createElement('div');footer.className='kc-strategy-footer';
      const outcome=textNode('div',`Hasil: RR ${pct(result.rr)} · NPL ${pct(result.nplRatio)}`);footer.append(outcome);
      const apply=document.createElement('button');apply.type='button';apply.className='kc-primary';apply.textContent='Terapkan';apply.disabled=!hasActions||(!result.ok&&capacityIssue);apply.addEventListener('click',()=>applyRecommendation(actions));footer.append(apply);card.append(footer);
      state.recommendations[id]=actions;host.append(card);
    };
    const s1Required=Math.max(rrNeed,nplNeed),s1Upgrade=Math.min(s1Required,upgradeCapacity),s1={upgrade:s1Upgrade};
    makeCard('s1','S1 · Fokus upgrade NPL → DPD 0','Satu rupiah upgrade memperbaiki RR dan menurunkan NPL; KYD tetap.',[{label:'Upgrade NPL → DPD 0',amount:s1Required}],s1,evaluate(s1),s1Upgrade+0.01<s1Required);
    const preventMigration=Math.min(base.migration,Math.ceil(rrNeed*.35));
    const btc=Math.min(Math.max(0,base.dpd1_30+preventMigration),Math.ceil(rrNeed*.30));
    const backflowRequired=Math.ceil(nplNeed*.5),backflow=Math.min(upgradeCapacity,backflowRequired);
    const s2UpgradeRequired=Math.max(Math.max(0,nplNeed-backflow),Math.max(0,rrNeed-preventMigration-btc));
    const s2Upgrade=Math.min(s2UpgradeRequired,Math.max(0,upgradeCapacity-backflow)),s2={preventMigration,btc,backflow,upgrade:s2Upgrade};
    makeCard('s2','S2 · Kombinasi tersebar','Cegah sebagian migrasi, tambah BTC dan backflow, lalu upgrade untuk menutup sisa target.',[{label:'Cegah migrasi DPD 0 → DPD 1–30',amount:preventMigration},{label:'Tambah BTC DPD 1–30 → DPD 0',amount:btc},{label:'Backflow NPL → DP',amount:backflowRequired},{label:'Upgrade NPL → DPD 0',amount:s2UpgradeRequired}],s2,evaluate(s2),backflow+.01<backflowRequired||s2Upgrade+.01<s2UpgradeRequired,true);
    let s3Upgrade=Math.min(upgradeCapacity,Math.ceil(rrNeed*.5));
    const payoffNeeded=upgrade=>targetNpl<1?Math.ceil(Math.max(0,(base.npl-upgrade-targetNpl*base.kyd-1e-7)/(1-targetNpl))):0;
    const s3PayoffRequired=payoffNeeded(s3Upgrade),s3Payoff=Math.min(s3PayoffRequired,Math.max(0,upgradeCapacity-s3Upgrade));
    const rrNeedAfterPayoff=Math.ceil(Math.max(0,targetRr*(base.kyd-s3Payoff)-(base.dpd0+s3Upgrade)-1e-7));
    const s3ExtraUpgrade=Math.min(rrNeedAfterPayoff,Math.max(0,upgradeCapacity-s3Upgrade-s3Payoff));s3Upgrade+=s3ExtraUpgrade;
    const s3={upgrade:s3Upgrade,payoff:s3Payoff},s3Capacity=s3Payoff+.01<s3PayoffRequired||s3ExtraUpgrade+.01<rrNeedAfterPayoff;
    makeCard('s3','S3 · Upgrade + pelunasan NPL','Bagi perbaikan RR antara upgrade NPL dan pelunasan yang mengurangi NPL serta KYD.',[{label:'Upgrade NPL → DPD 0',amount:s3Upgrade},{label:'Pelunasan / recovery NPL',amount:s3Payoff}],s3,evaluate(s3),s3Capacity);
  }
  function textNode(tag,value,className=''){const node=document.createElement(tag);node.textContent=value;if(className)node.className=className;return node;}
  function applyRecommendation(actions){
    const update=(id,value)=>{const input=el(id);input.value=money(Math.max(0,Math.round(value)));};
    if(actions.preventMigration)update('kcMigration',read('kcMigration')-actions.preventMigration);
    if(actions.btc)update('kcBtc',read('kcBtc')+actions.btc);
    if(actions.upgrade)update('kcUpgrade',read('kcUpgrade')+actions.upgrade);
    if(actions.backflow)update('kcBackflow',read('kcBackflow')+actions.backflow);
    if(actions.payoff)update('kcRecovery',read('kcRecovery')+actions.payoff);
    calculate();
  }
  function calculate(){
    if(!state.snapshot)return;
    const base=state.snapshot.closing,inputs={growth:read('kcGrowth'),btc:read('kcBtc'),migration:read('kcMigration'),flow_par:read('kcFlowPar'),recovery_npl:read('kcRecovery'),npl_upgrade:read('kcUpgrade'),npl_backflow:read('kcBackflow'),target_rr:el('kcTargetRr').value.trim()===''?NaN:Number(el('kcTargetRr').value),target_npl:el('kcTargetNpl').value.trim()===''?NaN:Number(el('kcTargetNpl').value)};
    const issues=[];if(Number(base.negative_rows||0)>0)issues.push('Snapshot posisi mengandung saldo negatif.');
    const kyd=posKyd(base);if(kyd<=0)issues.push('KYD posisi harus lebih besar dari nol.');if(Number(base.dpd0||0)>Number(base.l||0))issues.push('DPD 0 melebihi saldo L pada posisi awal.');
    const openingDpd1=Number(base.l||0)-Number(base.dpd0||0);
    const dpd0AfterMovement=Number(base.dpd0||0)+inputs.growth+inputs.btc-inputs.migration;
    const dpd1AfterMovement=openingDpd1-inputs.btc+inputs.migration;
    const flowFromDp=Math.min(inputs.flow_par,Math.max(Number(base.dp||0),0));
    let flowFromL=Math.max(0,inputs.flow_par-flowFromDp);
    const flowFromDpd1=Math.min(flowFromL,Math.max(dpd1AfterMovement,0));flowFromL-=flowFromDpd1;
    const flowFromDpd0=flowFromL;
    const dpd1=dpd1AfterMovement-flowFromDpd1;
    const dpd0Before=dpd0AfterMovement-flowFromDpd0;
    const flowParRemaining=Math.max(0,flowFromDpd0-Math.max(dpd0AfterMovement,0));
    let remaining=inputs.recovery_npl;
    const recoveryKl=Math.min(remaining,Math.max(Number(base.kl||0),0));remaining-=recoveryKl;
    const recoveryD=Math.min(remaining,Math.max(Number(base.d||0),0));remaining-=recoveryD;
    const recoveryM=Math.min(remaining,Math.max(Number(base.m||0),0));remaining-=recoveryM;
    const klAfter=Math.max(Number(base.kl||0)-recoveryKl,0),dAfter=Math.max(Number(base.d||0)-recoveryD,0),mAfter=Math.max(Number(base.m||0)-recoveryM,0);
    let upgradeRemaining=inputs.npl_upgrade;
    const upgradeKl=Math.min(upgradeRemaining,klAfter);upgradeRemaining-=upgradeKl;
    const upgradeD=Math.min(upgradeRemaining,dAfter);upgradeRemaining-=upgradeD;
    const upgradeM=Math.min(upgradeRemaining,mAfter);upgradeRemaining-=upgradeM;
    const upgradeApplied=inputs.npl_upgrade-upgradeRemaining;
    let backflowRemaining=inputs.npl_backflow;
    const backflowKl=Math.min(backflowRemaining,Math.max(0,klAfter-upgradeKl));backflowRemaining-=backflowKl;
    const backflowD=Math.min(backflowRemaining,Math.max(0,dAfter-upgradeD));backflowRemaining-=backflowD;
    const backflowM=Math.min(backflowRemaining,Math.max(0,mAfter-upgradeM));backflowRemaining-=backflowM;
    const backflowApplied=inputs.npl_backflow-backflowRemaining;
    const dpd0=dpd0Before+upgradeApplied;
    const kl=Math.max(klAfter-upgradeKl-backflowKl,0)+inputs.flow_par;
    const d=Math.max(dAfter-upgradeD-backflowD,0),m=Math.max(mAfter-upgradeM-backflowM,0);
    const dp=Number(base.dp||0)-flowFromDp+backflowApplied;
    const upgradeCapacity=Math.max(0,klAfter+dAfter+mAfter-upgradeApplied-backflowApplied);
    const l=dpd0+dpd1,npl=kl+d+m,projectedKyd=l+dp+npl;
    if(dpd0<-.01)issues.push('Proyeksi DPD 0 negatif; kurangi migrasi DPD 0.');
    if(dpd1<-.01)issues.push('Proyeksi DPD 1–30 negatif; kurangi BTC atau sesuaikan migrasi.');
    if(flowParRemaining>.01)issues.push('Flow PAR melebihi total saldo L dan DP yang tersedia.');
    if(remaining>.01)issues.push('Recovery melebihi total saldo KL, D, dan M yang tersedia.');
    if(upgradeRemaining>.01)issues.push('Upgrade NPL melebihi saldo NPL yang tersedia setelah recovery.');
    if(backflowRemaining>.01)issues.push('Backflow melebihi saldo NPL yang tersedia.');
    if(projectedKyd<=0)issues.push('KYD proyeksi harus lebih besar dari nol.');
    if(!Number.isFinite(inputs.target_rr)||inputs.target_rr<0||inputs.target_rr>100)issues.push('Target RR harus berada di antara 0% dan 100%.');
    if(!Number.isFinite(inputs.target_npl)||inputs.target_npl<0||inputs.target_npl>100)issues.push('Target NPL harus berada di antara 0% dan 100%.');
    const rr=projectedKyd>0?dpd0/projectedKyd:null,nplRatio=projectedKyd>0?npl/projectedKyd:null;
    const projection={balances:{dpd0,dpd1_30:dpd1,l,dp,kl,d,m,npl,kyd:projectedKyd},ratios:{rr,npl:nplRatio},targets:{rr:inputs.target_rr,npl:inputs.target_npl,rr_met:rr!==null&&rr>=inputs.target_rr/100,npl_met:nplRatio!==null&&nplRatio<=inputs.target_npl/100,dpd0_needed:rr===null?null:Math.max(0,inputs.target_rr/100*projectedKyd-dpd0),npl_reduction_needed:nplRatio===null?null:Math.max(0,npl-inputs.target_npl/100*projectedKyd)},applied:{recovery_npl:inputs.recovery_npl-remaining,npl_upgrade:upgradeApplied,npl_backflow:backflowApplied},issues:[...new Set(issues)]};
    state.projection=projection;
    el('kcRR').textContent=pct(rr);el('kcNPL').textContent=pct(nplRatio);el('kcDPD0').textContent=money(dpd0);el('kcKYD').textContent=money(projectedKyd);
    el('kcRrTarget').textContent=`Target ${pct(inputs.target_rr/100)} · ${projection.targets.rr_met?'tercapai':'belum tercapai'}`;
    el('kcNplTarget').textContent=`Target maks. ${pct(inputs.target_npl/100)} · ${projection.targets.npl_met?'tercapai':'belum tercapai'}`;
    warning('kcBtcWarning',dpd1<-.01?`BTC melebihi DPD 1–30 sebesar ${money(-dpd1)}.`:'');
    warning('kcGrowthWarning',dpd0<-.01?`Growth dan pergerakan membuat proyeksi DPD 0 minus ${money(-dpd0)}.`:'');
    warning('kcMigrationWarning',dpd0Before<-.01?`Migrasi melebihi DPD 0 tersedia sebesar ${money(-dpd0Before)}.`:'');
    warning('kcFlowWarning',flowParRemaining>.01?`Flow PAR melebihi total saldo L dan DP sebesar ${money(flowParRemaining)}.`:'');
    warning('kcRecoveryWarning',remaining>.01?`Recovery melebihi NPL tersedia sebesar ${money(remaining)}.`:'');
    warning('kcUpgradeWarning',upgradeRemaining>.01?`Upgrade NPL melebihi saldo tersedia sebesar ${money(upgradeRemaining)}.`:'');
    warning('kcBackflowWarning',backflowRemaining>.01?`Backflow melebihi saldo NPL tersisa sebesar ${money(backflowRemaining)}.`:'');
    const rrProgress=rr===null?0:inputs.target_rr>0?Math.min(100,Math.max(0,rr*10000/inputs.target_rr)):100;
    const nplProgress=nplRatio===null?0:inputs.target_npl>0?Math.min(100,Math.max(0,nplRatio*10000/inputs.target_npl)):100;
    el('kcRRProgress').style.width=`${rrProgress}%`;el('kcRRProgressLabel').textContent=`${pct(rr)} / ${pct(inputs.target_rr/100)}`;
    el('kcNPLProgress').style.width=`${nplProgress}%`;el('kcNplProgressLabel').textContent=`${pct(nplRatio)} / maks. ${pct(inputs.target_npl/100)}`;
    el('kcRrMeter').classList.toggle('is-warning',!projection.targets.rr_met);el('kcNplMeter').classList.toggle('is-warning',!projection.targets.npl_met);
    const issueBox=el('kcIssueMessage');issueBox.hidden=issues.length===0;issueBox.classList.toggle('is-error',issues.length>0);issueBox.textContent=issues.join(' ');
    const writable=canCreate&&state.cycle?.status==='open';el('kcStatus').textContent=issues.length?'PERIKSA INPUT':writable?'INPUT SIKLUS TERBUKA':canCreate?'SIKLUS DITUTUP':'LIHAT POSISI';el('kcStatus').classList.toggle('is-bad',issues.length>0);el('kcStatus').classList.toggle('is-muted',!issues.length&&!writable);el('kcStatus').classList.remove('is-warn');
    renderRecommendations(inputs.target_rr/100,inputs.target_npl/100,{dpd0,dpd1_30:dpd1,npl,kyd:projectedKyd,migration:inputs.migration},upgradeCapacity,issues);
    renderTable(state.snapshot.closing,state.snapshot.position,projection);renderBalanceBars();
  }
  async function loadPosition(){
    if(state.busy)return;state.busy=true;state.snapshot=null;state.projection=null;el('kcSaveDraft').disabled=true;el('kcSubmit').disabled=true;
    setInputsEnabled(false);
    for(const id of ['kcPeriod','kcBranch','kcCycle','kcActualDate','kcBasis'])el(id).disabled=true;
    el('kcOpeningRows').innerHTML='<tr><td colspan="3" style="text-align:center;color:#64748b">Memuat posisi...</td></tr>';
    el('kcComparisonRows').innerHTML='<tr><td colspan="7" style="text-align:center;color:#64748b">Memuat posisi...</td></tr>';
    el('kcBalanceBars').replaceChildren();el('kcRecommendations').replaceChildren();
    for(const id of ['kcRR','kcNPL','kcDPD0','kcKYD','kcRrTarget','kcNplTarget','kcRRProgressLabel','kcNplProgressLabel'])el(id).textContent='-';
    el('kcRRProgress').style.width='0';el('kcNPLProgress').style.width='0';
    el('kcStatus').textContent='MEMUAT POSISI';el('kcStatus').classList.remove('is-bad','is-warn','is-muted');
    const params=new URLSearchParams({period:el('kcPeriod').value,kode_cabang:el('kcBranch').value,cycle_id:el('kcCycle').value,actual_date:el('kcActualDate').value,nominal_field:el('kcBasis').value});
    message('Memuat closing dan posisi dasar komitmen…');
    try{
      const data=await apiData(`${apiBase}/position?${params}`);state.snapshot=data;
      setCycle(data.cycle);el('kcCycle').value=String(data.cycle.id);el('kcActualDate').value=data.selected_actual_date;
      el('kcPositionMeta').textContent=`Siklus ${data.cycle.cycle_no} · ${data.cycle.cycle_name} · target RR ${pct(Number(data.cycle.target_rr)/100)} · batas NPL ${pct(Number(data.cycle.target_npl)/100)}. Actual dipilih ${data.selected_actual_date}; snapshot yang dipakai ${data.position_date}.`;
      el('kcOpeningMeta').textContent=`Closing M-1 ${data.closing_date} dibandingkan dengan actual posisi ${data.position_date}.`;
      el('kcInputSubtitle').textContent=`Pijakan proyeksi Closing M-1 ${data.closing_date}. Asumsi awal mengikuti realisasi actual sampai ${data.position_date}; sesuaikan untuk rencana sampai EOM.`;
      if(!state.recordId)applyActualMovementDefaults(data.actual_movements);
      const writable=canCreate&&data.cycle.status==='open';setInputsEnabled(writable);el('kcSaveDraft').disabled=!writable;el('kcSubmit').disabled=!writable;
      message(writable?'Siklus dibuka pusat. Proyeksi dihitung dari Closing M-1; asumsi actual bisa kamu sesuaikan.':canCreate?'Siklus ditutup pusat. Posisi dan komitmen hanya dapat dilihat.':'Akun ini hanya dapat melihat posisi komitmen.');renderOpening();calculate();syncPageUrl();
    }catch(error){const detail=error.message||'Posisi gagal dimuat.';const noCycle=detail.includes('Pusat belum membuka siklus');setInputsEnabled(false);el('kcSaveDraft').disabled=true;el('kcSubmit').disabled=true;el('kcPositionMeta').textContent=noCycle?'Belum ada siklus untuk periode ini. Buka Rekap Komitmen untuk melihat statusnya.':'Posisi belum dapat dihitung.';for(const [id,columns] of [['kcOpeningRows',3],['kcComparisonRows',7]]){const tr=document.createElement('tr'),td=document.createElement('td');td.colSpan=columns;td.textContent=noCycle?'Belum ada posisi. Kantor Pusat perlu membuka siklus komitmen terlebih dahulu.':detail;td.style.textAlign='center';td.style.color=noCycle?'#64748b':'#991b1b';tr.append(td);el(id).replaceChildren(tr);}message(noCycle?'Kantor Pusat belum membuka siklus komitmen untuk bulan ini.':detail,noCycle?'warn':'error');el('kcStatus').textContent=noCycle?'SIKLUS BELUM DIBUKA':'POSISI BELUM SIAP';el('kcStatus').classList.toggle('is-bad',!noCycle);el('kcStatus').classList.toggle('is-warn',noCycle);el('kcStatus').classList.remove('is-muted');}
    finally{state.busy=false;for(const id of ['kcPeriod','kcCycle','kcActualDate','kcBasis'])el(id).disabled=false;el('kcBranch').disabled=!state.metadata?.allow_branch_selection;}
  }
  async function loadMetadata(){
    try{
      const params=new URLSearchParams({period:initial.period,kode_cabang:initial.branch||'ALL',cycle_id:initial.cycleId||'',nominal_field:initial.basis});const data=await apiData(`${apiBase}/metadata?${params}`);fillMetadata(data);
      if(state.recordId){const record=await apiData(`${apiBase}/detail?id=${encodeURIComponent(state.recordId)}`);fillRecord(record);}
      await loadPosition();
    }catch(error){message(error.message||'Filter gagal dimuat.','error');el('kcStatus').textContent='GAGAL MEMUAT';el('kcStatus').classList.add('is-bad');el('kcStatus').classList.remove('is-warn','is-muted');}
  }
  async function save(status){
    if(!state.snapshot||!state.projection||state.busy)return;
    calculate();if(status==='diajukan'&&state.projection.issues.length){message('Perbaiki asumsi yang tidak valid sebelum mengajukan.','error');return;}
    state.busy=true;setInputsEnabled(false);for(const id of ['kcPeriod','kcBranch','kcCycle','kcActualDate','kcBasis'])el(id).disabled=true;el('kcSaveDraft').disabled=true;el('kcSubmit').disabled=true;message(status==='draft'?'Menyimpan draf…':'Mengajukan komitmen…');
    const inputs={growth:read('kcGrowth'),btc:read('kcBtc'),migration:read('kcMigration'),flow_par:read('kcFlowPar'),recovery_npl:read('kcRecovery'),npl_upgrade:read('kcUpgrade'),npl_backflow:read('kcBackflow'),target_rr:el('kcTargetRr').value.trim()===''?null:Number(el('kcTargetRr').value),target_npl:el('kcTargetNpl').value.trim()===''?null:Number(el('kcTargetNpl').value),commitment_note:el('kcCommitmentNote').value};
    try{
      const data=await apiData(`${apiBase}/save`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({period:el('kcPeriod').value,kode_cabang:el('kcBranch').value,cycle_id:el('kcCycle').value,actual_date:el('kcActualDate').value,nominal_field:el('kcBasis').value,scenario_name:el('kcScenario').value,status,inputs})});
      state.recordId=data.id;syncPageUrl();message(`${status==='draft'?'Draf':'Komitmen'} tersimpan sebagai rekaman #${data.id}. Rekaman lama tetap tersimpan.`);
    }catch(error){message(error.message||'Komitmen gagal disimpan.','error');}
    finally{state.busy=false;const writable=canCreate&&state.cycle?.status==='open'&&!!state.snapshot;setInputsEnabled(writable);for(const id of ['kcPeriod','kcCycle','kcActualDate','kcBasis'])el(id).disabled=false;el('kcBranch').disabled=!state.metadata?.allow_branch_selection;el('kcSaveDraft').disabled=!writable;el('kcSubmit').disabled=!writable;}
  }
  el('kcPeriod').addEventListener('change',()=>{state.recordId=0;initial.period=el('kcPeriod').value;initial.cycleId=0;initial.actualDate='';clearAssumptions();syncPageUrl();loadMetadata();});
  el('kcBranch').addEventListener('change',()=>{state.recordId=0;initial.branch=el('kcBranch').value;clearAssumptions();syncPageUrl();loadPosition();});
  el('kcBasis').addEventListener('change',()=>{state.recordId=0;initial.basis=el('kcBasis').value;syncPageUrl();loadPosition();});
  el('kcActualDate').addEventListener('change',()=>{state.recordId=0;initial.actualDate=el('kcActualDate').value;syncPageUrl();loadPosition();});
  el('kcCycle').addEventListener('change',()=>{state.recordId=0;initial.cycleId=Number(el('kcCycle').value||0);clearAssumptions();setCycle(state.metadata?.cycles?.find(cycle=>String(cycle.id)===el('kcCycle').value));el('kcScenario').value=state.cycle?.cycle_name||'';syncPageUrl();loadPosition();});
  document.querySelectorAll('#komitmenKalkulator [data-nominal]').forEach(input=>{
    input.addEventListener('input',()=>{formatNominalInput(input);calculate();});
    input.addEventListener('blur',()=>{const value=read(input.id);input.value=money(value);calculate();});
  });
  ['kcTargetRr','kcTargetNpl'].forEach(id=>el(id).addEventListener('input',calculate));
  el('kcSaveDraft').addEventListener('click',()=>save('draft'));el('kcSubmit').addEventListener('click',()=>save('diajukan'));
  setInputsEnabled(false);loadMetadata();
})();
</script>

<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');
$canOpenCalculator = !empty($rbacInitialAccess['permissions']['page:komitmen_kalkulator']['can_view']);
$canCreateCalculator = !empty($rbacInitialAccess['permissions']['page:komitmen_kalkulator']['can_create']);
$calculatorUrl = rtrim((string)BASE_APP, '/') . '/komitmen_kalkulator';
?>
<style>
  #komitmenRekap { --km-border:#dce5ef; --km-muted:#64748b; --km-ink:#17243a; width:100%; min-width:0; height:auto; min-height:calc(100dvh - 64px); overflow:visible; padding:10px; color:#334155; background:#f7f9fc; }
  #komitmenRekap * { box-sizing:border-box; }
  #komitmenRekap .km-card { min-width:0; margin-bottom:10px; overflow:hidden; border:1px solid var(--km-border); border-radius:9px; background:#fff; }
  #komitmenRekap .km-head { display:flex; align-items:center; justify-content:space-between; gap:10px; }
  #komitmenRekap .km-tabs { display:flex; gap:4px; margin:0 0 10px; padding:4px; border:1px solid var(--km-border); border-radius:9px; background:#eef3f9; }
  #komitmenRekap .km-tab { min-height:34px; padding:0 13px; border:0; border-radius:7px; background:transparent; color:#52627a; font-size:11px; font-weight:800; }
  #komitmenRekap .km-tabs .km-tab[aria-selected="true"] { background:#fff; color:#1d4ed8; box-shadow:0 1px 3px #0f172a14; }
  #komitmenRekap .km-tab-panel[hidden] { display:none !important; }
  #komitmenRekap .km-head h2 { margin:0; color:var(--km-ink); font-size:14px; font-weight:850; }
  #komitmenRekap .km-head p,#komitmenRekap .km-meta { margin:3px 0 0; color:var(--km-muted); font-size:11px; line-height:1.45; }
  #komitmenRekap .km-toolbar { display:grid; grid-template-columns:repeat(5,minmax(120px,1fr)) auto; gap:9px; padding:12px; }
  #komitmenRekap .km-field { min-width:0; display:grid; gap:4px; }
  #komitmenRekap .km-field label { color:#475569; font-size:10px; font-weight:800; }
  #komitmenRekap input,#komitmenRekap select,#komitmenRekap button { min-width:0; min-height:38px; border:1px solid #d5dfeb; border-radius:8px; padding:0 10px; background:#fff; color:#1e293b; font:inherit; font-size:12px; }
  #komitmenRekap button { align-self:end; padding:0 15px; border-color:#2563eb; background:#2563eb; color:#fff; font-weight:800; cursor:pointer; }
  #komitmenRekap button:disabled { opacity:.55; cursor:wait; }
  #komitmenRekap .km-kpis { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:8px; padding:10px 12px; }
  #komitmenRekap .km-kpi { padding:10px; border:1px solid #e4eaf2; border-radius:9px; background:#fbfcfe; }
  #komitmenRekap .km-kpi span { display:block; color:var(--km-muted); font-size:10px; font-weight:700; }
  #komitmenRekap .km-kpi strong { display:block; margin-top:3px; color:var(--km-ink); font-size:19px; line-height:1.2; }
  #komitmenRekap .km-table-wrap { width:100%; overflow:auto; }
  #komitmenRekap table { width:100%; min-width:1320px; border-collapse:separate; border-spacing:0; font-size:10px; }
  #komitmenRekap th,#komitmenRekap td { padding:9px 8px; border-bottom:1px solid #e9eef5; text-align:right; vertical-align:middle; white-space:nowrap; }
  #komitmenRekap th { position:sticky; top:0; z-index:2; background:#f5f8fc; color:#475569; font-size:9px; font-weight:850; }
  #komitmenRekap th:first-child,#komitmenRekap td:first-child { position:sticky; left:0; z-index:1; background:#fff; text-align:left; }
  #komitmenRekap th:first-child { z-index:3; background:#f5f8fc; }
  #komitmenRekap tbody tr:last-child td { border-bottom:0; }
  #komitmenRekap .km-branch { min-width:145px; color:#1e293b; font-weight:800; }
  #komitmenRekap .km-pair { display:flex; justify-content:flex-end; gap:7px; }
  #komitmenRekap .km-pair span { color:#475569; }
  #komitmenRekap .km-record-meta { display:block; margin-top:3px; color:#64748b; font-size:10px; line-height:1.3; }
  #komitmenRekap .km-muted { color:#64748b; }
  #komitmenRekap .km-actions { display:flex; justify-content:flex-end; gap:5px; }
  #komitmenRekap .km-actions a { display:inline-flex; align-items:center; min-height:30px; padding:0 8px; border:1px solid #d5dfeb; border-radius:7px; color:#2455bd; font-size:10px; font-weight:800; text-decoration:none; }
  #komitmenRekap .km-alert { margin:0 0 10px; padding:10px 12px; border:1px solid #bfdbfe; border-left:3px solid #2563eb; border-radius:8px; background:#eff6ff; color:#334155; font-size:11px; line-height:1.5; }
  #komitmenRekap .km-alert.is-error { border-color:#fecaca; border-left-color:#dc2626; background:#fef2f2; color:#991b1b; }
  #komitmenRekap .km-cycle-admin { padding:12px; }
  #komitmenRekap .km-cycle-admin[hidden] { display:none; }
  #komitmenRekap .km-cycle-readonly { padding:12px; }
  #komitmenRekap .km-cycle-readonly h2 { margin:0; color:var(--km-ink); font-size:13px; }
  #komitmenRekap .km-cycle-readonly p { margin:5px 0 0; color:var(--km-muted); font-size:11px; line-height:1.5; }
  #komitmenRekap .km-cycle-admin-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:10px; }
  #komitmenRekap .km-cycle-admin-head h2 { margin:0; color:var(--km-ink); font-size:13px; }
  #komitmenRekap .km-cycle-admin-head p { margin:3px 0 0; color:var(--km-muted); font-size:11px; }
  #komitmenRekap .km-cycle-form { display:grid; grid-template-columns:minmax(180px,1.4fr) repeat(2,minmax(120px,.7fr)) auto; align-items:end; gap:8px; }
  #komitmenRekap .km-cycle-state { margin-top:9px; color:var(--km-muted); font-size:11px; }
  #komitmenRekap .km-pill { display:inline-flex; align-items:center; min-height:22px; padding:0 8px; border:1px solid #dce5ef; border-radius:6px; color:#475569; font-size:10px; font-weight:800; }
  #komitmenRekap .km-pill.is-open { border-color:#bbf7d0; background:#f0fdf4; color:#166534; }
  #komitmenRekap .km-pill.is-closed { background:#f8fafc; }
  #komitmenRekap .km-result-good { color:#15803d; font-weight:800; }
  #komitmenRekap .km-result-gap { color:#b45309; font-weight:800; }
  #komitmenRekap .km-result-pending { color:#64748b; font-weight:750; }
  #komitmenRekap .km-mobile-list { display:none; padding:9px; }
  #komitmenRekap .km-mobile-row { margin-bottom:8px; padding:10px; border:1px solid #e5eaf1; border-radius:9px; background:#fff; }
  #komitmenRekap .km-mobile-row:last-child { margin-bottom:0; }
  #komitmenRekap .km-mobile-row h3 { margin:0 0 8px; color:#17243a; font-size:12px; }
  #komitmenRekap .km-mobile-metric { display:grid; grid-template-columns:1fr auto; gap:8px; padding:5px 0; border-top:1px solid #eff2f6; font-size:10px; }
  #komitmenRekap .km-mobile-metric span:first-child { color:#64748b; }
  #komitmenRekap .km-mobile-metric strong { text-align:right; font-weight:750; }
  #komitmenRekap :is(button,input,select,a):focus-visible { outline:3px solid #93c5fd; outline-offset:2px; }
  @media(max-width:760px) {
    #komitmenRekap { padding:8px 6px 16px; }
    #komitmenRekap .km-toolbar { grid-template-columns:repeat(2,minmax(0,1fr)); }
    #komitmenRekap .km-cycle-form { grid-template-columns:repeat(2,minmax(0,1fr)); }
    #komitmenRekap .km-toolbar button { width:100%; }
    #komitmenRekap .km-kpis { grid-template-columns:repeat(2,minmax(0,1fr)); }
  }
  @media(max-width:560px) {
    #komitmenRekap .km-head { align-items:flex-start; flex-direction:column; }
    #komitmenRekap .km-tab { min-height:40px; }
    #komitmenRekap .km-toolbar { grid-template-columns:1fr 1fr; gap:7px; padding:9px; }
    #komitmenRekap .km-field:first-child { grid-column:1 / -1; }
    #komitmenRekap input,#komitmenRekap select,#komitmenRekap button { min-height:44px; font-size:14px; }
    #komitmenRekap .km-actions a { min-height:44px; }
    #komitmenRekap .km-table-wrap { display:none; }
    #komitmenRekap .km-mobile-list { display:block; }
    #komitmenRekap .km-kpi { padding:8px; }
    #komitmenRekap .km-kpi strong { font-size:17px; }
    #komitmenRekap .km-toolbar button { grid-column:1/-1; }
    #komitmenRekap .km-mobile-metric { grid-template-columns:minmax(78px,.7fr) minmax(0,1.3fr); }
    #komitmenRekap .km-mobile-metric strong { max-width:100%; overflow-wrap:anywhere; }
  }
  #komitmenRekap .km-card { border-radius:8px; box-shadow:none; }
  #komitmenRekap th:not(:first-child),#komitmenRekap td:not(:first-child),#komitmenRekap .km-kpi strong { font-variant-numeric:tabular-nums; }
  #komitmenRekap :is(button,a,select,label) { cursor:pointer; }
  #komitmenRekap :is(button,input,select):disabled { cursor:not-allowed; }
  #komitmenRekap button:not(:disabled):hover { filter:brightness(.96); }
  #komitmenRekap .km-actions a { cursor:pointer; }
  #komitmenRekap .km-actions a:hover { border-color:#93b4ef; background:#f5f8ff; color:#1746a2; }
  #komitmenRekap .km-actions a.is-disabled { color:#475569; background:#f8fafc; cursor:not-allowed; }
  #komitmenRekap .km-field label,#komitmenRekap .km-kpi span,#komitmenRekap .km-mobile-metric,
  #komitmenRekap .km-cycle-state,#komitmenRekap .km-actions a { font-size:11px; }
  #komitmenRekap table { font-size:11px; }
  #komitmenRekap th { font-size:10px; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap { --km-border:#334155; --km-muted:#a7b4c5; --km-ink:#f8fafc; color:#e2e8f0; background:#0b1220; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-card { border-color:#334155; background:#111827; color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-tabs { border-color:#334155; background:#0f172a; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-tabs .km-tab { color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-tabs .km-tab[aria-selected="true"] { background:#1e293b; color:#93c5fd; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-head h2,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-cycle-admin-head h2,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-cycle-readonly h2,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-branch,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-kpi strong,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-mobile-row h3 { color:#f8fafc; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-field label,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-head p,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-meta,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-cycle-admin-head p,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-cycle-readonly p,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-cycle-state,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-kpi span,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-mobile-metric span:first-child { color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap input,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap select { border-color:#475569; background:#0f172a; color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap th { border-color:#334155; background:#1e293b; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap td { border-color:#334155; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap th:first-child,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap td:first-child { background:#111827; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-kpi,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-mobile-row { border-color:#334155; background:#0f172a; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-alert { border-color:#1e3a5f; border-left-color:#60a5fa; background:#172554; color:#dbeafe; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-alert.is-error { border-color:#7f1d1d; border-left-color:#f87171; background:#450a0a; color:#fecaca; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-pill.is-open { border-color:#166534; background:#052e16; color:#86efac; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-pill.is-closed { border-color:#334155; background:#1e293b; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-result-good { color:#86efac; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-result-gap { color:#fcd34d; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-result-pending { color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-actions a { border-color:#475569; color:#93c5fd; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-record-meta,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-muted { color:#a7b4c5; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #komitmenRekap .km-actions a.is-disabled { color:#a7b4c5; background:#1e293b; }
  @media(prefers-reduced-motion:reduce) { #komitmenRekap *,#komitmenRekap *::before,#komitmenRekap *::after { scroll-behavior:auto !important; transition:none !important; animation:none !important; } }
</style>

<main id="komitmenRekap" class="mb-report-page">
  <header class="mb-page-header km-card" style="padding:12px">
    <div class="mb-page-header__copy">
      <h1 class="mb-page-header__title">Rekap Komitmen RR &amp; NPL</h1>
      <p class="mb-page-header__subtitle">Pantau komitmen per siklus, actual berjalan, dan hasil akhir bulan setiap cabang.</p>
    </div>
  </header>
  <nav class="km-tabs" role="tablist" aria-label="Menu komitmen">
    <button id="kmTabRecap" class="km-tab" type="button" role="tab" aria-selected="true" aria-controls="kmPanelRecap" tabindex="0">Rekap Komitmen</button>
    <button id="kmTabControl" class="km-tab" type="button" role="tab" aria-selected="false" aria-controls="kmPanelControl" tabindex="-1">Kontrol Siklus</button>
  </nav>
  <section id="kmPanelRecap" class="km-tab-panel" role="tabpanel" aria-labelledby="kmTabRecap">
    <div id="kmMessage" class="km-alert" role="status" aria-live="polite">Memuat rekap komitmen...</div>
    <section class="km-card" aria-label="Filter rekap komitmen">
      <form id="kmFilter" class="km-toolbar">
        <div class="km-field"><label for="kmPeriod">Periode EOM</label><input id="kmPeriod" type="month" required></div>
        <div class="km-field"><label for="kmCycle">Siklus komitmen</label><select id="kmCycle"><option value="">Siklus terbaru</option></select></div>
        <div class="km-field"><label for="kmBranch">Cabang</label><select id="kmBranch"><option value="ALL">Semua wilayah</option></select></div>
        <div class="km-field"><label for="kmActualDate">Tanggal actual sampai</label><input id="kmActualDate" type="date" required></div>
        <div class="km-field"><label for="kmBasis">Basis nominal</label><select id="kmBasis"><option value="saldo_bank">Saldo Bank</option><option value="baki_debet">Baki Debet</option></select></div>
        <button id="kmShow" type="submit" disabled>Tampilkan</button>
      </form>
    </section>
    <section class="km-card" aria-label="Ringkasan komitmen">
      <div class="km-kpis">
        <div class="km-kpi"><span>Cabang ditampilkan</span><strong id="kmCountBranches">-</strong></div>
        <div class="km-kpi"><span>Siklus dalam bulan</span><strong id="kmCountCycles">-</strong></div>
        <div class="km-kpi"><span>Cabang mengisi siklus</span><strong id="kmCountCommitted">-</strong></div>
        <div class="km-kpi"><span>Cabang dengan actual</span><strong id="kmCountActual">-</strong></div>
      </div>
    </section>
    <section class="km-card">
      <div class="km-head" style="padding:12px 12px 9px">
        <div><h2>Perbandingan posisi per cabang</h2><p id="kmPeriodLabel" class="km-meta"></p></div>
      </div>
      <div class="km-table-wrap"><table><thead><tr>
        <th>Cabang</th><th>Closing M-1</th><th>Proyeksi komitmen EOM</th><th>Actual s.d. tanggal pilihan</th><th>Actual vs komitmen</th><th>Actual vs closing M-1</th><th>Evaluasi</th><th>Komitmen</th>
      </tr></thead><tbody id="kmRows"><tr><td colspan="8" class="km-muted" style="text-align:center">Memuat data...</td></tr></tbody></table></div>
      <div id="kmMobileRows" class="km-mobile-list" aria-live="polite"></div>
    </section>
  </section>
  <section id="kmPanelControl" class="km-tab-panel" role="tabpanel" aria-labelledby="kmTabControl" hidden>
    <section id="kmCycleAdmin" class="km-card km-cycle-admin" aria-label="Pengaturan siklus komitmen" hidden>
      <div class="km-cycle-admin-head"><div><h2>Kontrol siklus komitmen</h2><p>Tetapkan target, lalu buka input komitmen untuk seluruh cabang.</p></div><span id="kmCycleBadge" class="km-pill">Belum ada siklus aktif</span></div>
      <form id="kmCycleForm" class="km-cycle-form">
        <div class="km-field"><label for="kmCycleName">Nama siklus</label><input id="kmCycleName" type="text" maxlength="100" placeholder="Contoh: Komitmen Minggu 1" required></div>
        <div class="km-field"><label for="kmTargetRr">Target RR (%)</label><input id="kmTargetRr" type="number" min="0" max="100" step="0.01" value="74.15" required></div>
        <div class="km-field"><label for="kmTargetNpl">Batas NPL (%)</label><input id="kmTargetNpl" type="number" min="0" max="100" step="0.01" value="14.90" required></div>
        <button id="kmOpenCycle" type="submit">Buka siklus</button>
      </form>
      <div class="km-cycle-state"><span id="kmCycleAdminState">Siklus yang ditutup tetap tersimpan untuk evaluasi.</span> <button id="kmCloseCycle" type="button" hidden>Tutup siklus</button></div>
    </section>
    <section id="kmCycleReadOnly" class="km-card km-cycle-readonly" aria-live="polite">
      <h2>Status siklus komitmen</h2>
      <p id="kmCycleReadOnlyState">Memuat status siklus...</p>
      <p>Pengelolaan siklus tersedia untuk Kantor Pusat, Divisi Pemasaran, Divisi Penyelesaian Kredit, dan Divisi Operasional.</p>
    </section>
  </section>
</main>

<script>
(() => {
  const el = id => document.getElementById(id);
  const apiBase = (window.BASE_APP || '') + '/api/komitmen';
  const calcBase = <?= json_encode($calculatorUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const canOpenCalculator = <?= $canOpenCalculator ? 'true' : 'false' ?>;
  const canCreateCalculator = <?= $canCreateCalculator ? 'true' : 'false' ?>;
  const number = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });
  const percent = value => value == null ? '-' : (Number(value) * 100).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%';
  const money = value => value == null ? '-' : number.format(Number(value));
  const signedMoney = value => (value > 0 ? '↑ ' : value < 0 ? '↓ ' : '→ ') + money(Math.abs(value));
  const signedPp = value => (value > 0 ? '↑ ' : value < 0 ? '↓ ' : '→ ') + (Math.abs(value) * 100).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%';
  const text = (tag, value, className) => {
    const node = document.createElement(tag);
    node.textContent = value;
    if (className) node.className = className;
    return node;
  };
  const state = { cycles: [], cycle: null, activeCycle: null, rows: [], allowBranchSelection: false, canManage: false, busy: false };
  const tabs = [['kmTabRecap', 'kmPanelRecap'], ['kmTabControl', 'kmPanelControl']];
  function activateTab(tabId, moveFocus) {
    for (const [id, panelId] of tabs) {
      const selected = id === tabId;
      el(id).setAttribute('aria-selected', selected ? 'true' : 'false');
      el(id).tabIndex = selected ? 0 : -1;
      el(panelId).hidden = !selected;
    }
    if (moveFocus) el(tabId).focus();
  }
  for (const [tabId] of tabs) {
    el(tabId).addEventListener('click', () => activateTab(tabId, false));
    el(tabId).addEventListener('keydown', event => {
      const current = tabs.findIndex(([id]) => id === tabId);
      let next = current;
      if (event.key === 'ArrowRight') next = (current + 1) % tabs.length;
      else if (event.key === 'ArrowLeft') next = (current - 1 + tabs.length) % tabs.length;
      else if (event.key === 'Home') next = 0;
      else if (event.key === 'End') next = tabs.length - 1;
      else return;
      event.preventDefault();
      activateTab(tabs[next][0], true);
    });
  }
  const today = new Date();
  const currentMonth = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
  const paramsOnLoad = new URLSearchParams(location.search);
  el('kmPeriod').value = paramsOnLoad.get('period') || currentMonth;
  function showMessage(value, error) {
    const box = el('kmMessage');
    box.textContent = value;
    box.hidden = !value;
    box.classList.toggle('is-error', !!error);
  }
  function setDateBounds(month) {
    if (!/^\d{4}-\d{2}$/.test(month)) return;
    const last = new Date(Number(month.slice(0, 4)), Number(month.slice(5, 7)), 0).getDate();
    const end = month + '-' + String(last).padStart(2, '0');
    const input = el('kmActualDate');
    input.min = month + '-01';
    input.max = end;
    if (input.value && input.value.slice(0, 7) !== month) input.value = '';
  }
  function summary(position) {
    if (!position || !Number(position.row_count)) return null;
    const kyd = Number(position.l || 0) + Number(position.dp || 0) + Number(position.kl || 0) + Number(position.d || 0) + Number(position.m || 0);
    const npl = Number(position.kl || 0) + Number(position.d || 0) + Number(position.m || 0);
    return { kyd, rr: kyd > 0 ? Number(position.dpd0 || 0) / kyd : null, npl: kyd > 0 ? npl / kyd : null };
  }
  function projectionSummary(record) {
    const balances = record && record.projection && record.projection.balances;
    const ratios = record && record.projection && record.projection.ratios;
    return balances && ratios ? { kyd: Number(balances.kyd), rr: ratios.rr, npl: ratios.npl } : null;
  }
  function metric(value) {
    return value ? money(value.kyd) + ' · RR ' + percent(value.rr) + ' · NPL ' + percent(value.npl) : '-';
  }
  function difference(actual, commitment) {
    if (!actual || !commitment) return '-';
    return signedMoney(actual.kyd - commitment.kyd) + ' · RR ' + (actual.rr == null || commitment.rr == null ? '-' : signedPp(actual.rr - commitment.rr)) + ' · NPL ' + (actual.npl == null || commitment.npl == null ? '-' : signedPp(actual.npl - commitment.npl));
  }
  function updateCycleAdmin() {
    const active = state.cycles.find(cycle => cycle.status === 'open') || null;
    state.activeCycle = active;
    el('kmCycleAdmin').hidden = !state.canManage;
    el('kmCycleReadOnly').hidden = state.canManage;
    const latest = active || state.cycles[0] || null;
    el('kmCycleReadOnlyState').textContent = active
      ? active.cycle_name + ' sedang terbuka. Cabang dapat mengisi atau memperbarui komitmen pada siklus ini.'
      : latest ? latest.cycle_name + ' sudah ditutup. Siklus tetap tersimpan untuk evaluasi.'
        : 'Belum ada siklus komitmen untuk periode ini.';
    if (active) {
      el('kmCycleBadge').textContent = 'Siklus ' + active.cycle_no + ' terbuka';
      el('kmCycleBadge').className = 'km-pill is-open';
      el('kmCycleAdminState').textContent = active.cycle_name + ' · target RR ' + percent(Number(active.target_rr) / 100) + ' · batas NPL ' + percent(Number(active.target_npl) / 100) + '. Cabang dapat menyimpan revisi selama siklus terbuka.';
      el('kmCloseCycle').hidden = false;
      el('kmOpenCycle').disabled = true;
      el('kmCycleName').disabled = true;
      el('kmTargetRr').disabled = true;
      el('kmTargetNpl').disabled = true;
    } else {
      el('kmCycleBadge').textContent = 'Tidak ada siklus aktif';
      el('kmCycleBadge').className = 'km-pill is-closed';
      el('kmCycleAdminState').textContent = 'Pusat menentukan arah dan waktu input. Siklus yang ditutup tetap tersimpan untuk evaluasi.';
      el('kmCloseCycle').hidden = true;
      el('kmOpenCycle').disabled = false;
      el('kmCycleName').disabled = false;
      el('kmTargetRr').disabled = false;
      el('kmTargetNpl').disabled = false;
      if (!el('kmCycleName').value) el('kmCycleName').value = 'Komitmen Minggu ' + (state.cycles.length + 1);
    }
  }
  function makeLink(row) {
    const wrap = text('div', '', 'km-actions');
    if (!canOpenCalculator || !state.cycle) return wrap;
    const link = document.createElement('a');
    const record = row.commitment;
    link.textContent = record ? (canCreateCalculator && state.cycle.status === 'open' ? 'Revisi komitmen' : 'Lihat komitmen') : (canCreateCalculator && state.cycle.status === 'open' ? 'Input komitmen' : 'Belum diisi');
    const query = new URLSearchParams({
      period: el('kmPeriod').value,
      kode_cabang: row.branch_code,
      cycle_id: String(state.cycle.id),
      actual_date: el('kmActualDate').value,
      nominal_field: el('kmBasis').value
    });
    if (record && record.id) query.set('id', String(record.id));
    link.href = calcBase + '?' + query.toString();
    if (!record && (state.cycle.status !== 'open' || !canCreateCalculator)) {
      link.removeAttribute('href');
      link.setAttribute('aria-disabled', 'true');
      link.classList.add('is-disabled');
      link.style.pointerEvents = 'none';
    }
    wrap.append(link);
    return wrap;
  }
  function buildStatus(row, isFinalActual) {
    const commitment = projectionSummary(row.commitment);
    const actual = summary(row.actual);
    const box = document.createElement('div');
    if (!commitment) {
      box.append(text('strong', 'Belum ada komitmen', 'km-result-pending'));
      return box;
    }
    if (commitment.rr == null || commitment.npl == null) {
      box.append(text('strong', 'Proyeksi belum bisa dievaluasi', 'km-result-pending'));
      box.append(text('small', 'Periksa asumsi pada kalkulator komitmen.'));
      return box;
    }
    if (!actual || actual.rr == null || actual.npl == null) {
      box.append(text('strong', 'Pantauan berjalan', 'km-result-pending'));
      box.append(text('small', 'Snapshot actual belum tersedia untuk tanggal pilihan.'));
      return box;
    }
    if (!isFinalActual) {
      box.append(text('strong', 'Pantauan berjalan', 'km-result-pending'));
      box.append(text('small', 'Evaluasi akhir muncul saat snapshot tanggal EOM tersedia.'));
      return box;
    }
    const commitmentMet = actual.rr >= commitment.rr && actual.npl <= commitment.npl;
    box.append(text('strong', commitmentMet ? 'Komitmen sesuai / lebih baik' : 'Masih ada gap komitmen', commitmentMet ? 'km-result-good' : 'km-result-gap'));
    if (state.cycle) {
      const targetMet = actual.rr >= Number(state.cycle.target_rr) / 100 && actual.npl <= Number(state.cycle.target_npl) / 100;
      box.append(text('small', targetMet ? 'Target pusat tercapai' : 'Target pusat belum tercapai', targetMet ? 'km-result-good' : 'km-result-gap'));
    }
    box.append(text('small', 'Snapshot EOM dibanding proyeksi siklus.'));
    return box;
  }
  function appendCell(row, value, muted) {
    row.append(text('td', value, muted ? 'km-muted' : ''));
  }
  function render(data) {
    state.rows = data.rows || [];
    state.cycles = data.cycles || [];
    state.cycle = data.selected_cycle || null;
    state.allowBranchSelection = !!data.allow_branch_selection;
    state.canManage = !!data.can_manage_cycles;
    const branch = el('kmBranch');
    const wantedBranch = data.selected_branch || (state.allowBranchSelection ? 'ALL' : '');
    branch.replaceChildren();
    if (state.allowBranchSelection) {
      const all = text('option', 'Semua wilayah');
      all.value = 'ALL';
      branch.append(all);
    }
    for (const item of (data.branches || [])) {
      const option = text('option', String(item.code).padStart(3, '0') + ' · ' + item.name);
      option.value = String(item.code).padStart(3, '0');
      branch.append(option);
    }
    branch.value = Array.from(branch.options).some(option => option.value === wantedBranch) ? wantedBranch : (state.allowBranchSelection ? 'ALL' : (branch.options[0] ? branch.options[0].value : ''));
    branch.disabled = !state.allowBranchSelection;

    const cycleSelect = el('kmCycle');
    const wantedCycle = state.cycle ? String(state.cycle.id) : '';
    cycleSelect.replaceChildren();
    const latestOption = text('option', 'Siklus terbaru');
    latestOption.value = '';
    cycleSelect.append(latestOption);
    for (const cycle of state.cycles) {
      const option = text('option', 'Siklus ' + cycle.cycle_no + ' · ' + cycle.cycle_name + (cycle.status === 'open' ? ' · terbuka' : ''));
      option.value = String(cycle.id);
      cycleSelect.append(option);
    }
    cycleSelect.value = Array.from(cycleSelect.options).some(option => option.value === wantedCycle) ? wantedCycle : '';

    setDateBounds(data.period);
    el('kmActualDate').value = data.actual_date_selected || '';
    const cycleLabel = state.cycle ? ' · ' + state.cycle.cycle_name + ' · target RR ' + percent(Number(state.cycle.target_rr) / 100) + ', NPL ' + percent(Number(state.cycle.target_npl) / 100) : ' · Belum ada siklus';
    el('kmPeriodLabel').textContent = 'Closing ' + data.closing_date + ' · actual dipilih sampai ' + data.actual_date_selected + cycleLabel + (data.projection_date <= new Date().toISOString().slice(0, 10) ? ' · EOM ' + data.projection_date : ' · EOM belum tiba');
    const subtitle = document.querySelector('#komitmenRekap .mb-page-header__subtitle');
    if (subtitle) subtitle.textContent = 'Bandingkan komitmen dengan actual pada tanggal pilihan dan closing bulan lalu.';
    el('kmCountBranches').textContent = String(state.rows.length);
    el('kmCountCycles').textContent = String(state.cycles.length);
    el('kmCountCommitted').textContent = String(state.rows.filter(row => row.commitment).length);
    el('kmCountActual').textContent = String(state.rows.filter(row => summary(row.actual)).length);
    updateCycleAdmin();

    const table = document.querySelector('#komitmenRekap .km-table-wrap table');
    const header = table.querySelector('thead tr');
    header.replaceChildren();
    for (const label of ['Cabang', 'Closing M-1', 'Proyeksi komitmen EOM', 'Actual s.d. ' + (data.actual_date_selected || 'tanggal pilihan'), 'Actual vs komitmen', 'Actual vs closing M-1', 'Evaluasi', 'Komitmen']) header.append(text('th', label));
    const body = el('kmRows');
    const mobile = el('kmMobileRows');
    body.replaceChildren();
    mobile.replaceChildren();
    for (const item of state.rows) {
      const closing = summary(item.closing);
      const commitment = projectionSummary(item.commitment);
      const actual = summary(item.actual);
      const isFinalActual = item.actual_date === data.projection_date;
      const tr = document.createElement('tr');
      tr.append(text('td', item.branch_code + ' · ' + item.branch_name, 'km-branch'));
      appendCell(tr, metric(closing), !closing);
      const commitmentCell = text('td', metric(commitment), !commitment);
      if (item.commitment) commitmentCell.append(text('small', 'Disimpan ' + (item.commitment.created_at || 'pada siklus ini'), 'km-record-meta'));
      tr.append(commitmentCell);
      const actualCell = text('td', metric(actual), !actual);
      if (item.actual_date && item.actual_date !== data.actual_date_selected) actualCell.append(text('small', 'Snapshot ' + item.actual_date, 'km-record-meta'));
      tr.append(actualCell);
      appendCell(tr, difference(actual, commitment), !actual || !commitment);
      appendCell(tr, difference(actual, closing), !actual || !closing);
      const statusCell = text('td', '');
      statusCell.append(buildStatus(item, isFinalActual));
      tr.append(statusCell);
      const action = text('td', '');
      action.append(makeLink(item));
      tr.append(action);
      body.append(tr);

      const card = text('article', '', 'km-mobile-row');
      card.append(text('h3', item.branch_code + ' · ' + item.branch_name));
      const addMetric = (label, value) => {
        const line = text('div', '', 'km-mobile-metric');
        line.append(text('span', label), text('strong', value));
        card.append(line);
      };
      addMetric('Closing M-1', metric(closing));
      addMetric(state.cycle ? state.cycle.cycle_name : 'Proyeksi komitmen EOM', metric(commitment));
      const actualSnapshotNote = item.actual_date && item.actual_date !== data.actual_date_selected ? ' · snapshot ' + item.actual_date : '';
      addMetric('Actual s.d. ' + (data.actual_date_selected || 'tanggal pilihan'), metric(actual) + actualSnapshotNote);
      addMetric('Actual vs komitmen', difference(actual, commitment));
      addMetric('Actual vs closing M-1', difference(actual, closing));
      const statusLine = text('div', '', 'km-mobile-metric');
      statusLine.append(text('span', 'Evaluasi'), buildStatus(item, isFinalActual));
      card.append(statusLine, makeLink(item));
      mobile.append(card);
    }
    if (!state.rows.length) {
      const empty = document.createElement('tr');
      const cell = text('td', 'Tidak ada cabang untuk filter ini.', 'km-muted');
      cell.colSpan = 8;
      cell.style.textAlign = 'center';
      empty.append(cell);
      body.append(empty);
      mobile.append(text('p', 'Tidak ada cabang untuk filter ini.', 'km-meta'));
    }
  }
  async function loadList() {
    if (state.busy) return;
    state.busy = true;
    el('kmShow').disabled = true;
    showMessage('Memuat rekap dan actual…');
    try {
      const query = new URLSearchParams({ period: el('kmPeriod').value, kode_cabang: el('kmBranch').value || 'ALL', nominal_field: el('kmBasis').value });
      if (el('kmCycle').value) query.set('cycle_id', el('kmCycle').value);
      if (el('kmActualDate').value) query.set('actual_date', el('kmActualDate').value);
      const request = window.apiFetch ? window.apiFetch(apiBase + '/list?' + query) : fetch(apiBase + '/list?' + query, { credentials: 'include' });
      const response = await request;
      const json = await response.json();
      if (!response.ok || json.status !== 200) throw new Error(json.message || 'Rekap komitmen gagal dimuat.');
      render(json.data);
      if (!json.data.cycles.length) showMessage(state.canManage ? 'Belum ada siklus. Buka tab Kontrol Siklus untuk menetapkan target dan membuka input.' : 'Belum ada siklus input yang dibuka oleh pengelola siklus.');
      else if (state.activeCycle) showMessage(state.activeCycle.cycle_name + ' sedang terbuka. Komitmen cabang dapat diperbarui selama siklus berlangsung.');
      else showMessage('Siklus ditutup. Evaluasi akhir aktif setelah snapshot actual EOM tersedia.');
    } catch (error) {
      showMessage(error.message || 'Rekap komitmen gagal dimuat.', true);
    } finally {
      state.busy = false;
      el('kmShow').disabled = false;
    }
  }
  async function postAction(action, body) {
    const options = { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body) };
    const response = await (window.apiFetch ? window.apiFetch(apiBase + '/' + action, options) : fetch(apiBase + '/' + action, { credentials: 'include', ...options }));
    const json = await response.json();
    if (!response.ok || json.status !== 200) throw new Error(json.message || 'Aksi gagal.');
    return json.data;
  }
  el('kmFilter').addEventListener('submit', event => { event.preventDefault(); loadList(); });
  for (const id of ['kmPeriod', 'kmCycle', 'kmBranch', 'kmActualDate', 'kmBasis']) {
    el(id).addEventListener('change', () => {
      if (id === 'kmPeriod') { el('kmCycle').value = ''; el('kmActualDate').value = ''; setDateBounds(el('kmPeriod').value); }
      loadList();
    });
  }
  el('kmCycleForm').addEventListener('submit', async event => {
    event.preventDefault();
    el('kmOpenCycle').disabled = true;
    try {
      await postAction('cycle-open', { period: el('kmPeriod').value, cycle_name: el('kmCycleName').value.trim(), target_rr: Number(el('kmTargetRr').value), target_npl: Number(el('kmTargetNpl').value) });
      el('kmCycle').value = '';
      await loadList();
    } catch (error) {
      showMessage(error.message || 'Siklus gagal dibuka.', true);
      el('kmOpenCycle').disabled = false;
    }
  });
  el('kmCloseCycle').addEventListener('click', async () => {
    if (!state.activeCycle || !window.confirm('Tutup ' + state.activeCycle.cycle_name + '? Cabang tidak dapat mengubah komitmen sampai pusat membuka siklus berikutnya.')) return;
    el('kmCloseCycle').disabled = true;
    try {
      await postAction('cycle-close', { cycle_id: state.activeCycle.id });
      await loadList();
    } catch (error) {
      showMessage(error.message || 'Siklus gagal ditutup.', true);
    } finally {
      el('kmCloseCycle').disabled = false;
    }
  });
  loadList();
})();
</script>

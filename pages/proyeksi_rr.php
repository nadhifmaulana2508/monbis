<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');
?>

<style>
  #proyeksiRr { --pr-border:#dde6f1; --pr-muted:#64748b; --pr-ink:#0f172a; width:100%; min-width:0; max-width:100%; min-height:calc(100vh - 64px); min-height:calc(100dvh - 64px); height:auto; margin:0; padding:8px 10px 20px; gap:10px; overflow:visible; color:#334155; background:#f8fafc; }
  #proyeksiRr * { box-sizing:border-box; }
  #proyeksiRr .pr-page-header { position:relative; z-index:20; align-items:center; flex-direction:row; margin:0; }
  #proyeksiRr .pr-page-header .mb-page-header__copy { flex:1 1 auto; min-width:210px; }
  #proyeksiRr .pr-page-header .mb-page-header__title { font-size:17px; }
  #proyeksiRr .pr-page-header .mb-page-header__subtitle { max-width:none; white-space:normal; }
  #proyeksiRr .pr-header-controls { display:flex; flex:0 1 auto; min-width:0; align-items:center; justify-content:flex-end; gap:8px; }
  #proyeksiRr .pr-header-controls .pr-status { white-space:nowrap; }
  #proyeksiRr .pr-card { min-width:0; border:1px solid var(--pr-border); border-radius:10px; background:#fff; box-shadow:0 10px 24px rgba(15,23,42,.045); }
  #proyeksiRr .pr-section { padding:0; overflow:hidden; }
  #proyeksiRr .pr-section-head { min-height:40px; }
  #proyeksiRr .pr-section-heading { min-width:0; }
  #proyeksiRr .pr-section-heading h2 { margin:0; color:var(--pr-ink); font-size:12px; font-weight:900; }
  #proyeksiRr .pr-section-heading p { margin:2px 0 0; }
  #proyeksiRr .pr-section-content { display:flex; flex-direction:column; gap:10px; min-width:0; padding:10px; }
  #proyeksiRr .pr-subsection { display:flex; flex-direction:column; gap:6px; min-width:0; }
  #proyeksiRr .pr-subsection-title { margin:0; color:#334155; font-size:10px; font-weight:900; }
  #proyeksiRr .pr-subsection-heading { display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
  #proyeksiRr .pr-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:9px; }
  #proyeksiRr .pr-field { min-width:0; display:flex; flex-direction:column; gap:4px; }
  #proyeksiRr .pr-field label,#proyeksiRr .pr-detail-search label { color:#334155; font-size:8px; font-weight:900; letter-spacing:.035em; text-transform:uppercase; }
  #proyeksiRr .pr-field small { min-height:24px; color:#64748b; font-size:9px; line-height:1.35; }
  #proyeksiRr .pr-field .pr-input-warning { min-height:0; color:#b91c1c; font-weight:800; }
  #proyeksiRr .pr-field .pr-input-warning[hidden] { display:none; }
  #proyeksiRr input,#proyeksiRr select { width:100%; min-width:0; min-height:var(--mb-control-h,36px); padding:0 9px; border:1px solid #cbd5e1; border-radius:8px; background:#fff; color:#1e293b; font:750 11px var(--mb-font,Arial,sans-serif); outline:none; }
  #proyeksiRr input:focus,#proyeksiRr select:focus { border-color:#60a5fa; outline:3px solid rgba(96,165,250,.18); }
  #proyeksiRr .pr-btn { display:inline-flex; min-height:34px; align-items:center; justify-content:center; gap:7px; border:1px solid #cbd5e1; border-radius:8px; padding:0 11px; background:#fff; color:#334155; font-size:10px; font-weight:850; cursor:pointer; }
  #proyeksiRr .pr-btn:hover { background:#f8fafc; }
  #proyeksiRr .pr-btn-primary { border-color:#2563eb; background:#2563eb; color:#fff; }
  #proyeksiRr .pr-btn-primary:hover { background:#1d4ed8; }
  #proyeksiRr .pr-btn:active:not(:disabled) { filter:brightness(.94); }
  #proyeksiRr .pr-btn:disabled { opacity:.55; cursor:not-allowed; }
  #proyeksiRr .pr-alert { border:1px solid #dbeafe; border-left:3px solid #2563eb; border-radius:5px; padding:10px 12px; background:#eff6ff; color:#334155; font-size:11px; line-height:1.55; }
  #proyeksiRr .pr-message { margin:8px 0 0; padding:7px 10px; font-size:10px; }
  #proyeksiRr .pr-forecast-note { border:1px solid #dbeafe; border-radius:6px; padding:8px 10px; background:#f8fbff; color:#475569; font-size:10px; line-height:1.5; }
  #proyeksiRr .pr-error { border-color:#fecaca; border-left-color:#dc2626; background:#fef2f2; color:#991b1b; }
  #proyeksiRr .pr-meta { color:var(--pr-muted); font-size:10px; line-height:1.5; }
  #proyeksiRr .pr-status { display:inline-flex; min-height:23px; align-items:center; border-radius:999px; padding:2px 9px; background:#e2e8f0; color:#334155; font-size:10px; font-weight:850; }
  #proyeksiRr .pr-status-ok { background:#dcfce7; color:#166534; }
  #proyeksiRr .pr-status-bad { background:#fee2e2; color:#991b1b; }
  #proyeksiRr .pr-table-wrap { width:100%; min-width:0; max-width:100%; height:auto; min-height:0; overflow:auto; overscroll-behavior:contain; -webkit-overflow-scrolling:touch; scrollbar-width:thin; border:1px solid #e2e8f0; border-radius:8px; background:#fff; }
  #proyeksiRr .pr-table { width:100%; min-width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; color:#334155; font-size:10px; }
  #proyeksiRr .pr-table th,#proyeksiRr .pr-table td { height:34px; padding:5px 7px; border-right:1px solid #e8eef5; border-bottom:1px solid #edf2f7; text-align:left; vertical-align:middle; white-space:nowrap; }
  #proyeksiRr .pr-table th { position:sticky; top:0; z-index:1; background:#f8fafc; color:#334155; font-size:9px; font-weight:950; line-height:1.15; letter-spacing:.02em; text-transform:uppercase; box-shadow:0 1px 0 #dbe3ee; }
  #proyeksiRr .pr-table tr:last-child td { border-bottom:0; }
  #proyeksiRr .pr-table th:last-child,#proyeksiRr .pr-table td:last-child { border-right:0; }
  #proyeksiRr .pr-table tbody tr:hover td { background:#f8fafc; }
  #proyeksiRr .pr-table .pr-number { text-align:right; font-variant-numeric:tabular-nums; }
  #proyeksiRr .pr-table .pr-delta-cell { text-align:center; }
  #proyeksiRr .pr-delta { display:inline-flex; align-items:center; justify-content:center; min-height:22px; padding:3px 7px; border:1px solid #dbe3ee; border-radius:999px; background:#f8fafc; color:#475569; font-size:8px; font-weight:900; white-space:nowrap; }
  #proyeksiRr .pr-delta--good { border-color:#bbf7d0; background:#ecfdf5; color:#047857; }
  #proyeksiRr .pr-delta--bad { border-color:#fecaca; background:#fef2f2; color:#b91c1c; }
  #proyeksiRr .pr-delta--neutral { border-color:#dbe3ee; background:#f8fafc; color:#64748b; }
  #proyeksiRr .pr-movements-table { min-width:720px; }
  #proyeksiRr .pr-forecast-grid { grid-template-columns:repeat(5,minmax(0,1fr)); }
  #proyeksiRr .pr-target-grid { grid-template-columns:repeat(4,minmax(0,1fr)); }
  #proyeksiRr .pr-recommendation-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:9px; }
  #proyeksiRr .pr-recommendation-card { display:flex; min-width:0; flex-direction:column; gap:7px; border:1px solid #dde6f1; border-radius:9px; padding:10px; background:#fff; }
  #proyeksiRr .pr-recommendation-card.is-recommended { border-color:#f2c14e; background:#fffdf6; }
  #proyeksiRr .pr-recommendation-head { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; }
  #proyeksiRr .pr-recommendation-title { margin:0; color:#1e293b; font-size:11px; font-weight:900; line-height:1.35; }
  #proyeksiRr .pr-recommendation-status { flex:none; border-radius:999px; padding:3px 7px; background:#e2e8f0; color:#475569; font-size:8px; font-weight:900; white-space:nowrap; }
  #proyeksiRr .pr-recommendation-status.is-good { background:#dcfce7; color:#166534; }
  #proyeksiRr .pr-recommendation-status.is-capacity { background:#fef3c7; color:#92400e; }
  #proyeksiRr .pr-recommendation-description,#proyeksiRr .pr-recommendation-result { margin:0; color:#64748b; font-size:9px; line-height:1.45; }
  #proyeksiRr .pr-recommendation-list { display:grid; gap:3px; margin:0; padding-left:16px; color:#334155; font-size:9px; line-height:1.4; }
  #proyeksiRr .pr-recommendation-footer { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:auto; }
  #proyeksiRr .pr-recommendation-result strong { color:#1e293b; }
  #proyeksiRr .pr-balance-table { min-width:760px; }
  #proyeksiRr .pr-breakdown-table { min-width:1120px; }
  #proyeksiRr .pr-balance-table td { color:#0f172a; font-size:12px; font-weight:750; }
  #proyeksiRr .pr-movement-label { color:#0f172a; font-weight:750; }
  #proyeksiRr .pr-movement-note { display:block; margin-top:3px; color:#64748b; font-size:9px; line-height:1.4; }
  #proyeksiRr .pr-inline-link { border:0; padding:5px 0; background:none; color:#1d4ed8; font-size:10px; font-weight:800; text-decoration:underline; text-underline-offset:3px; cursor:pointer; }
  #proyeksiRr .pr-inline-link:hover { color:#1e40af; }
  #proyeksiRr .pr-actions { display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
  #proyeksiRr .pr-dialog { position:fixed; inset:auto; top:50%; left:50%; transform:translate(-50%,-50%); margin:0; width:min(1180px,calc(100vw - 28px)); max-width:none; max-height:calc(100vh - 40px); max-height:calc(100dvh - 40px); overflow:auto; padding:16px; border:1px solid #cbd5e1; border-radius:10px; background:#fff; color:#1e293b; box-shadow:0 18px 55px rgba(15,23,42,.22); }
  #proyeksiRr .pr-dialog::backdrop { background:rgba(15,23,42,.48); }
  #proyeksiRr .pr-dialog-head,#proyeksiRr .pr-detail-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; }
  #proyeksiRr .pr-dialog-head { margin-bottom:12px; }
  #proyeksiRr .pr-dialog-head h2 { margin:0; color:#0f172a; font-size:16px; font-weight:850; }
  #proyeksiRr .pr-detail-search { display:grid; grid-template-columns:minmax(150px,1fr) minmax(240px,2fr); align-items:end; gap:10px; margin-bottom:10px; }
  #proyeksiRr .pr-detail-search-row { display:flex; gap:7px; }
  #proyeksiRr .pr-detail-search-row input { flex:1; }
  #proyeksiRr .pr-detail-summary { min-height:24px; color:#475569; font-size:10px; }
  #proyeksiRr .pr-detail-table-wrap { max-height:58vh; max-height:58dvh; }
  #proyeksiRr .pr-detail-table-wrap .pr-table { min-width:1080px; }
  #proyeksiRr .pr-empty { padding:24px; color:#64748b; text-align:center; }
  #proyeksiRr .pr-detail-footer { margin-top:11px; }
  #proyeksiRr :is(button,input,select):focus-visible { outline:3px solid rgba(37,99,235,.27); outline-offset:2px; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr { color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-card,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-dialog { border-color:#334155; background:#111827; color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-title,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-subsection-title,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-section-heading h2,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-dialog-head h2,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-balance-table td,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-movement-label { color:#f8fafc; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr input,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr select,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr input { border-color:#475569; background:#0f172a; color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-table th { border-color:#334155; background:#1e293b; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-table td { border-color:#334155; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-table tbody tr:hover td { background:#172033; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-inline-link { color:#93c5fd; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-alert { border-color:#1e3a5f; border-left-color:#60a5fa; background:#172554; color:#dbeafe; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-forecast-note { border-color:#334155; background:#0f172a; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-field label,
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-field small { color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-field .pr-input-warning { color:#fca5a5; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-delta--good { border-color:#166534; background:#052e16; color:#86efac; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-delta--bad { border-color:#7f1d1d; background:#450a0a; color:#fca5a5; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-delta--neutral { border-color:#334155; background:#1e293b; color:#cbd5e1; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-recommendation-card { border-color:#334155; background:#111827; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-recommendation-card.is-recommended { border-color:#a16207; background:#1c1917; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-recommendation-title,:is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-recommendation-list { color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #proyeksiRr .pr-recommendation-result strong { color:#f8fafc; }
  @media (max-width:850px) {
    #proyeksiRr .pr-page-header { align-items:flex-start; flex-wrap:wrap; gap:10px; }
    #proyeksiRr .pr-header-controls { width:100%; justify-content:flex-end; }
    #proyeksiRr .pr-grid,#proyeksiRr .pr-recommendation-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
  }
  @media (max-width:560px) {
    #proyeksiRr { padding:8px 7px 16px; }
    #proyeksiRr .pr-page-header { align-items:stretch; gap:9px; }
    #proyeksiRr .pr-page-header .mb-page-header__copy { flex-basis:100%; min-width:0; }
    #proyeksiRr .pr-page-header .mb-page-header__title { font-size:18px; line-height:1.2; }
    #proyeksiRr .pr-header-controls { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:7px; }
    #proyeksiRr .pr-header-controls .pr-btn { width:100%; min-height:44px; }
    #proyeksiRr .pr-header-controls .pr-status { justify-self:end; align-self:center; max-width:100%; white-space:normal; }
    #proyeksiRr .pr-grid,#proyeksiRr .pr-recommendation-grid { grid-template-columns:1fr; }
    #proyeksiRr .pr-section-head { align-items:flex-start; flex-wrap:wrap; gap:6px; }
    #proyeksiRr .pr-section-heading { flex:1 1 100%; }
    #proyeksiRr .pr-section-content { padding:8px; }
    #proyeksiRr input,#proyeksiRr select,#proyeksiRr .pr-btn { min-height:44px; }
    #proyeksiRr input,#proyeksiRr select { font-size:16px; }
    #proyeksiRr .pr-field label,#proyeksiRr .pr-detail-search label { font-size:10px; }
    #proyeksiRr .pr-field small { min-height:0; font-size:10px; }
    #proyeksiRr .pr-recommendation-head { flex-wrap:wrap; }
    #proyeksiRr .pr-recommendation-title { min-width:0; overflow-wrap:anywhere; }
    #proyeksiRr .pr-recommendation-status { white-space:normal; }
    #proyeksiRr .pr-recommendation-footer { align-items:stretch; flex-wrap:wrap; }
    #proyeksiRr .pr-recommendation-result { flex:1 1 100%; }
    #proyeksiRr .pr-recommendation-footer .pr-btn { width:100%; }
    #proyeksiRr .pr-inline-link { display:inline-flex; min-height:44px; align-items:center; }
    #proyeksiRr .pr-movements-table { width:100%; min-width:0; table-layout:fixed; }
    #proyeksiRr .pr-movements-table thead { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
    #proyeksiRr .pr-movements-table tbody { display:block; width:100%; }
    #proyeksiRr .pr-movements-table tbody tr { display:grid; grid-template-columns:minmax(0,1fr) auto; align-items:center; gap:4px 8px; padding:8px; border-bottom:1px solid #edf2f7; }
    #proyeksiRr .pr-movements-table tbody tr:last-child { border-bottom:0; }
    #proyeksiRr .pr-movements-table tbody td { display:block; min-width:0; height:auto; border:0; padding:0; white-space:normal; }
    #proyeksiRr .pr-movements-table tbody td:first-child { grid-column:1 / -1; }
    #proyeksiRr .pr-movement-label { font-size:11px; }
    #proyeksiRr .pr-movement-note { font-size:10px; }
    #proyeksiRr .pr-movements-table tbody td:nth-child(2) { text-align:left; font-size:11px; font-weight:800; }
    #proyeksiRr .pr-movements-table tbody td:nth-child(2)::before { display:block; margin-bottom:2px; color:#64748b; content:'Nominal actual'; font-size:8px; font-weight:700; }
    #proyeksiRr .pr-movements-table tbody td:nth-child(3) { justify-self:end; text-align:right; }
    #proyeksiRr .pr-movements-table .pr-inline-link { min-height:44px; padding:0 8px; }
    #proyeksiRr .pr-breakdown-table { min-width:840px; }
    #proyeksiRr .pr-breakdown-table th:first-child,#proyeksiRr .pr-breakdown-table td:first-child { width:112px; }
    #proyeksiRr .pr-detail-search { grid-template-columns:1fr; gap:5px; }
    #proyeksiRr .pr-dialog { width:calc(100vw - 16px); max-height:calc(100vh - 16px); max-height:calc(100dvh - 16px); padding:11px; }
    #proyeksiRr .pr-dialog-head { align-items:flex-start; }
    #proyeksiRr .pr-dialog-head h2 { font-size:14px; }
    #proyeksiRr .pr-detail-footer { align-items:flex-start; flex-direction:column; }
    #proyeksiRr .pr-detail-footer > div { display:flex; width:100%; }
    #proyeksiRr .pr-detail-footer .pr-btn { flex:1; }
  }
  @media (max-width:360px) {
    #proyeksiRr { padding-right:5px; padding-left:5px; }
    #proyeksiRr .pr-section-content { padding:6px; }
    #proyeksiRr .pr-header-controls { gap:5px; }
  }
</style>

<main id="proyeksiRr" class="mb-report-page mb-report-standard pr-page">
  <header class="mb-page-header pr-page-header">
    <div class="mb-page-header__copy">
      <div class="mb-page-header__title-row"><h1 class="mb-page-header__title">Proyeksi RR &amp; NPL</h1></div>
      <p class="mb-page-header__subtitle">Bandingkan posisi closing, actual, dan proyeksi akhir bulan.</p>
    </div>
    <div class="pr-header-controls">
      <button type="submit" form="proyeksiRrNavbarFilterPanel" id="prLoad" class="pr-btn pr-btn-primary">Proyeksi</button>
      <span id="prLoadState" class="pr-status">Menyiapkan</span>
    </div>
  </header>
  <div id="prMessage" class="pr-alert pr-message hidden" role="status" aria-live="polite"></div>

  <section id="prSnapshotSection" class="mb-report-card pr-card pr-section hidden" aria-labelledby="prSnapshotTitle">
    <div class="mb-report-toolbar pr-section-head">
      <div class="pr-section-heading"><h2 id="prSnapshotTitle" class="mb-report-toolbar__title">Pergerakan actual sejak closing</h2><p id="prSnapshotMeta" class="pr-meta"></p></div>
      <span id="prSnapshotCheck" class="pr-status">Snapshot</span>
    </div>
    <div class="pr-section-content">
      <div class="pr-subsection">
        <div class="pr-subsection-heading"><h3 class="pr-subsection-title">Rincian flow aktual</h3><span id="prMovementState" class="pr-meta" aria-live="polite"></span></div>
        <div class="mb-table-wrap pr-table-wrap"><table class="mb-table pr-table pr-movements-table"><thead><tr><th>Pergerakan</th><th class="pr-number">Nominal actual</th><th>Rincian</th></tr></thead><tbody id="prActualMovements"></tbody></table></div>
        <p class="pr-meta">Flow PAR actual menghitung rekening closing L atau DP yang menjadi KL, D, atau M. Nilai proyeksi Flow PAR menggantikan flow aktual pada periode closing sampai akhir bulan.</p>
      </div>
    </div>
  </section>

  <section id="prResultSection" class="mb-report-card pr-card pr-section hidden" aria-labelledby="prResultTitle">
    <div class="mb-report-toolbar pr-section-head"><div class="pr-section-heading"><h2 id="prResultTitle" class="mb-report-toolbar__title">Perbandingan posisi dan proyeksi</h2><p id="prResultMeta" class="pr-meta"></p></div><span id="prResultStatus" class="pr-status"></span></div>
    <div class="pr-section-content"><div class="mb-table-wrap pr-table-wrap"><table class="mb-table pr-table pr-breakdown-table"><thead><tr><th>Komponen</th><th>Closing M-1</th><th>Actual</th><th>Proyeksi akhir bulan</th><th>Actual vs Closing M-1</th><th>Proyeksi vs Closing M-1</th></tr></thead><tbody id="prBreakdown"></tbody></table></div></div>
  </section>
  <section id="prInputSection" class="mb-report-card pr-card pr-section hidden" aria-labelledby="prInputTitle">
    <div class="mb-report-toolbar pr-section-head"><div class="pr-section-heading"><h2 id="prInputTitle" class="mb-report-toolbar__title">Asumsi proyeksi akhir bulan</h2><p id="prPeriodLabel" class="pr-meta"></p></div></div>
    <div class="pr-section-content">
      <div class="pr-grid pr-forecast-grid">
        <div class="pr-field"><label class="mb-field-label" for="prFlowPar">Proyeksi total DP ke KL (Rp)</label><input class="mb-field-control" data-pr-nominal id="prFlowPar" type="text" inputmode="numeric" autocomplete="off" value="" aria-describedby="prFlowParHelp prFlowParWarning"><small id="prFlowParHelp">Mengurangi saldo DP dan menambah KL.</small><small id="prFlowParWarning" class="pr-input-warning" role="status" hidden></small></div>
        <div class="pr-field"><label class="mb-field-label" for="prGrowth">Growth ke DPD 0 / L (Rp)</label><input class="mb-field-control" data-pr-nominal id="prGrowth" type="text" inputmode="numeric" autocomplete="off" value="0"><small>Menambah DPD 0 pada kolektibilitas L dan KYD.</small></div>
        <div class="pr-field"><label class="mb-field-label" for="prBtc">BTC: DPD 1–30 ke DPD 0 (Rp)</label><input class="mb-field-control" data-pr-nominal id="prBtc" type="text" inputmode="numeric" autocomplete="off" value="0" aria-describedby="prBtcHelp prBtcWarning"><small id="prBtcHelp">Mengurangi DPD 1–30 (L) dan menambah DPD 0; total L tetap.</small><small id="prBtcWarning" class="pr-input-warning" role="status" hidden></small></div>
        <div class="pr-field"><label class="mb-field-label" for="prMigration">Migrasi DPD 0 ke DPD 1–30 (Rp)</label><input class="mb-field-control" data-pr-nominal id="prMigration" type="text" inputmode="numeric" autocomplete="off" value="0" aria-describedby="prMigrationHelp prMigrationWarning"><small id="prMigrationHelp">Masuk ke DPD 1–30 dalam L; total L dan KYD tetap.</small><small id="prMigrationWarning" class="pr-input-warning" role="status" hidden></small></div>
        <div class="pr-field"><label class="mb-field-label" for="prSettlement">Recovery NPL (Rp)</label><input class="mb-field-control" data-pr-nominal id="prSettlement" type="text" inputmode="numeric" autocomplete="off" value="0" aria-describedby="prSettlementHelp prSettlementWarning"><small id="prSettlementHelp">Mengurangi KL, lalu D dan M tanpa saldo negatif.</small><small id="prSettlementWarning" class="pr-input-warning" role="status" hidden></small></div>
      </div>
      <p class="pr-forecast-note"><strong>Aturan proyeksi:</strong> BTC memindahkan saldo DPD 1–30 ke DPD 0. Backflow memindahkan saldo NPL ke DP. Flow DP ke KL menambah KL; recovery dan upgrade mengurangi NPL yang sudah ada, sehingga flow tetap tercatat. Mutasi dihitung dari closing sampai akhir bulan.</p>
      <div class="pr-subsection" aria-labelledby="prTargetHeading">
        <div class="pr-subsection-heading"><h3 id="prTargetHeading" class="pr-subsection-title">Target dan tambahan perbaikan</h3><span class="pr-meta">Dihitung dari proyeksi saat ini; Terapkan mengisi asumsi tambahan.</span></div>
        <div class="pr-grid pr-target-grid">
          <div class="pr-field"><label class="mb-field-label" for="prTargetRr">Target RR (%)</label><input class="mb-field-control" id="prTargetRr" type="number" min="0" max="100" step="0.01" value="74.15" inputmode="decimal" aria-describedby="prTargetRrHelp prTargetRrWarning"><small id="prTargetRrHelp">Target rasio DPD 0 terhadap KYD.</small><small id="prTargetRrWarning" class="pr-input-warning" role="status" hidden></small></div>
          <div class="pr-field"><label class="mb-field-label" for="prTargetNpl">Target NPL Gross (%)</label><input class="mb-field-control" id="prTargetNpl" type="number" min="0" max="100" step="0.01" value="14.90" inputmode="decimal" aria-describedby="prTargetNplHelp prTargetNplWarning"><small id="prTargetNplHelp">Batas maksimum rasio NPL terhadap KYD.</small><small id="prTargetNplWarning" class="pr-input-warning" role="status" hidden></small></div>
          <div class="pr-field"><label class="mb-field-label" for="prNplUpgrade">Upgrade NPL ke DPD 0 (Rp)</label><input class="mb-field-control" data-pr-nominal id="prNplUpgrade" type="text" inputmode="numeric" autocomplete="off" value="0" aria-describedby="prNplUpgradeHelp prNplUpgradeWarning"><small id="prNplUpgradeHelp">Mengurangi NPL, menambah DPD 0, KYD tetap.</small><small id="prNplUpgradeWarning" class="pr-input-warning" role="status" hidden></small></div>
          <div class="pr-field"><label class="mb-field-label" for="prNplBackflow">Backflow NPL ke PAR / DP (Rp)</label><input class="mb-field-control" data-pr-nominal id="prNplBackflow" type="text" inputmode="numeric" autocomplete="off" value="0" aria-describedby="prNplBackflowHelp prNplBackflowWarning"><small id="prNplBackflowHelp">Mengurangi NPL dan menambah DP; KYD tetap.</small><small id="prNplBackflowWarning" class="pr-input-warning" role="status" hidden></small></div>
        </div>
        <div id="prRecommendations" class="pr-recommendation-grid"><p class="pr-meta">Rekomendasi muncul setelah posisi dimuat.</p></div>
      </div>
      <div class="pr-actions"><p id="prValidation" class="pr-meta" aria-live="polite">Proyeksi otomatis mengikuti perubahan asumsi.</p><button id="prReset" class="pr-btn" type="button">Reset input</button></div>
    </div>
  </section>

  <dialog id="prDetailDialog" class="pr-dialog" aria-labelledby="prDetailTitle">
    <div class="pr-dialog-head"><div><h2 id="prDetailTitle">Detail rekening</h2><p id="prDetailSubtitle" class="pr-meta"></p></div><button id="prDetailClose" class="pr-btn" type="button">Tutup</button></div>
    <form id="prDetailSearchForm" class="pr-detail-search"><label for="prDetailSearch">Cari rekening atau nama debitur</label><div class="pr-detail-search-row"><input id="prDetailSearch" type="search" autocomplete="off" placeholder="No rekening atau nama"><button class="pr-btn" type="submit">Cari</button></div></form>
    <div id="prDetailSummary" class="pr-detail-summary" aria-live="polite"></div>
    <div class="mb-table-wrap pr-table-wrap pr-detail-table-wrap"><table class="mb-table pr-table"><thead><tr><th>No rekening</th><th>Nama debitur</th><th class="pr-number" id="prDetailClosingBasisLabel">Saldo closing</th><th class="pr-number" id="prDetailBasisLabel">Saldo actual</th><th class="pr-number">Nilai pergerakan</th><th class="pr-number">Tunggakan pokok</th><th class="pr-number">Tunggakan bunga</th><th>DPD / kolektibilitas closing ke actual</th></tr></thead><tbody id="prDetailRows"><tr><td colspan="8" class="pr-empty">Pilih rincian pergerakan untuk memuat rekening.</td></tr></tbody></table></div>
    <div class="pr-detail-footer"><span id="prDetailPageInfo" class="pr-meta"></span><div class="flex gap-2"><button id="prDetailPrev" class="pr-btn" type="button" disabled>Sebelumnya</button><button id="prDetailNext" class="pr-btn" type="button" disabled>Berikutnya</button></div></div>
  </dialog>
</main>

<script>
(() => {
  const el = id => document.getElementById(id);
  const apiBase = `${window.BASE_APP || ''}/api/proyeksi_rr`;
  const apiCall = (url, options={}) => window.apiFetch
    ? window.apiFetch(url, options)
    : fetch(url, {credentials:'include', ...options});
  const rupiah = new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0});
  const nominalNumber = new Intl.NumberFormat('id-ID',{maximumFractionDigits:0});
  const percent = value => `${(Number(value||0)*100).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2})}%`;
  const showMoney = value => rupiah.format(Number(value||0));
  const readNominal = id => {const digits=String(el(id).value||'').replace(/\D/g,'');return digits===''?0:Number(digits);};
  function formatNominalInput(input){
    const before=input.value.slice(0,input.selectionStart??input.value.length);
    const digitsBefore=(before.match(/\d/g)||[]).length;
    const digits=input.value.replace(/\D/g,'').replace(/^0+(?=\d)/,'');
    const formatted=digits?nominalNumber.format(Number(digits)):'';
    input.value=formatted;
    let caret=0,seen=0;
    while(caret<formatted.length&&seen<digitsBefore){if(/\d/.test(formatted[caret]))seen++;caret++;}
    input.setSelectionRange(caret,caret);
  }
  const state={metadata:null,snapshot:null};
  const detailState={movement:'',page:1,totalPages:0,search:'',abort:null};
  let recommendationActions={};
  const movementNames={growth:'Growth bersih KYD',flow_par:'Flow PAR actual',recovery_npl:'Penyelesaian / recovery NPL',btc:'BTC aktual',migration_d0:'Migrasi DPD 0 ke PAR'};
  const setStatus=(node,type,text)=>{node.className=`pr-status ${type?`pr-status-${type}`:''}`;node.textContent=text;};
  const setMessage=(text,isError=false)=>{const box=el('prMessage');box.textContent=text;box.classList.remove('hidden');box.classList.toggle('pr-error',isError);if(isError)setStatus(el('prLoadState'),'bad','Perlu perhatian');};
  const setInputWarning=(id,message)=>{const warning=el(id);warning.textContent=message;warning.hidden=!message;};
  function setProyeksiNavbarFilter(open){
    const panel=el('proyeksiRrNavbarFilterPanel'),toggle=el('proyeksiRrNavbarFilterToggle');
    if(!panel)return;
    panel.classList.toggle('hidden',!open);toggle?.classList.toggle('is-active',open);toggle?.setAttribute('aria-expanded',open?'true':'false');
  }
  const addOption=(select,value,label)=>{const option=document.createElement('option');option.value=value;option.textContent=label;select.append(option);};
  const previousMonthEnd=date=>{const parsed=new Date(`${date}T00:00:00Z`);parsed.setUTCDate(0);return parsed.toISOString().slice(0,10);};
  const isMonthEnd=date=>{const parsed=new Date(`${date}T00:00:00Z`);return parsed.getUTCDate()===new Date(Date.UTC(parsed.getUTCFullYear(),parsed.getUTCMonth()+1,0)).getUTCDate();};

  function fillMetadata(data){
    state.metadata=data;
    const closing=el('prClosing'),actual=el('prActual'),branch=el('prBranch'),selected=String(branch.value||data.selected_branch||'ALL');
    branch.replaceChildren();addOption(branch,'ALL','ALL  -  konsolidasi');
    data.branches.forEach(row=>{const code=String(row.code).padStart(3,'0');addOption(branch,code,`${code}  -  ${row.name}`);});
    if([...branch.options].some(option=>option.value===selected))branch.value=selected;
    const latestActual=String(data.latest_actual_date||'');
    actual.max=latestActual;actual.value=latestActual;
    closing.max=latestActual?previousMonthEnd(latestActual):'';
    closing.value=String(data.default_closing_date||'');
    closing.disabled=!latestActual;actual.disabled=!latestActual;
    if(!latestActual){setMessage('Snapshot nominatif belum tersedia untuk cabang ini.',true);return;}
    setStatus(el('prLoadState'),'ok','Siap');
    setMessage(data.default_closing_date?'Pilih basis saldo, lalu klik Proyeksi untuk memuat posisi. Tanggal snapshot akan diperiksa server.':'Snapshot closing M-1 belum ditemukan; pilih tanggal closing dan actual yang tersedia.');
  }

  function syncClosingToActual(){
    const actualDate=el('prActual').value;
    if(!actualDate)return;
    el('prClosing').value=previousMonthEnd(actualDate);
  }

  function syncActualToClosing(preferredDate=''){
    const closingDate=el('prClosing').value,actual=el('prActual');
    if(!closingDate)return;
    const [year,month]=closingDate.split('-').map(Number);
    const nextMonth=new Date(Date.UTC(year,month,1)).toISOString().slice(0,7);
    const current=preferredDate||actual.value;
    if(current.startsWith(nextMonth))return;
    const latest=String(state.metadata?.latest_actual_date||'');
    actual.value=latest.startsWith(nextMonth)?latest:'';
  }

  async function loadMetadata(branch=el('prBranch').value||'ALL'){
    setStatus(el('prLoadState'),'','Memuat snapshot');
    el('prClosing').disabled=true;el('prActual').disabled=true;
    try{
      const response=await apiCall(`${apiBase}/metadata?kode_cabang=${encodeURIComponent(branch)}`,{cache:'no-store'});
      const json=await response.json();
      if(!response.ok||Number(json.status)>=400)throw new Error(json.message||'Daftar snapshot gagal dimuat.');
      fillMetadata(json.data||{});
    }catch(error){setMessage(error.message||'Gagal memuat daftar snapshot.',true);}
  }

  async function loadSnapshot(){
    const closingDate=el('prClosing').value,actualDate=el('prActual').value;
    if(!closingDate||!actualDate){setMessage('Pilih tanggal closing dan actual yang tersedia.',true);return;}
    if(!isMonthEnd(closingDate)){setMessage('Closing M-1 harus tanggal akhir bulan.',true);return;}
    if(previousMonthEnd(actualDate)!==closingDate){setMessage('Tanggal actual harus berada pada bulan setelah closing M-1.',true);return;}
    el('prLoad').disabled=true;setStatus(el('prLoadState'),'','Memuat data');
    try{
      const response=await apiCall(`${apiBase}/snapshot`,{method:'POST',headers:{'Content-Type':'application/json'},cache:'no-store',body:JSON.stringify({closing_date:closingDate,harian_date:actualDate,kode_cabang:el('prBranch').value,nominal_field:el('prBasis').value})});
      const json=await response.json();
      if(!response.ok||Number(json.status)>=400)throw new Error(json.message||'Snapshot gagal dimuat.');
      state.snapshot=json.data;renderSnapshot();calculate();
      setProyeksiNavbarFilter(false);
      setStatus(el('prLoadState'),'ok','Posisi dimuat');setMessage('Posisi closing dan actual berhasil dimuat. Proyeksi memakai saldo closing M-1 sebagai dasar.');
      loadMovementSummary();
    }catch(error){setMessage(error.message||'Gagal memuat snapshot.',true);}
    finally{el('prLoad').disabled=false;}
  }

  function renderSnapshot(){
    const data=state.snapshot,s=data.actual,c=data.closing;
    const kyd=s.l+s.dp+s.kl+s.d+s.m;
    const branch=el('prBranch').value==='ALL'?'Konsolidasi':el('prBranch').selectedOptions[0]?.textContent||data.kode_cabang;
    el('prSnapshotMeta').textContent=`${branch} · Closing ${data.closing_date} · Actual ${data.harian_date} · ${data.nominal_field==='baki_debet'?'Baki Debet':'Saldo Bank'}`;
    const closingKyd=c.l+c.dp+c.kl+c.d+c.m;
    const invalid=c.negative_rows>0||s.negative_rows>0||closingKyd<=0||kyd<=0||c.dpd0>c.l||s.dpd0>s.l;
    setStatus(el('prSnapshotCheck'),invalid?'bad':'ok',invalid?'Periksa snapshot':'Snapshot valid');
    const [year,month]=data.projection_date.split('-').map(Number);
    const monthName=new Date(Date.UTC(year,month-1,1)).toLocaleDateString('id-ID',{month:'long',year:'numeric',timeZone:'UTC'});
    el('prPeriodLabel').textContent=`Basis proyeksi: closing ${data.closing_date} sampai ${data.projection_date} (${monthName}); actual menjadi pembanding.`;
    const movements=data.actual_movements||{};
    el('prFlowPar').value='';delete el('prFlowPar').dataset.userEdited;
    renderActualMovements(movements,true);
    const basisLabel=data.nominal_field==='baki_debet'?'Baki debet':'Saldo bank';
    el('prDetailClosingBasisLabel').textContent=`${basisLabel} closing`;
    el('prDetailBasisLabel').textContent=`${basisLabel} actual`;
    el('prSnapshotSection').classList.remove('hidden');el('prInputSection').classList.remove('hidden');
    el('prValidation').textContent=invalid?'Snapshot perlu diperiksa: saldo negatif, KYD nol, atau DPD 0 melebihi L.':'Proyeksi dihitung dari closing M-1 dengan total asumsi movement sampai akhir bulan.';
  }

  function ratioDelta(value,previous,key){
    if(value===null||previous===null)return '<span class="pr-delta pr-delta--neutral">Belum dihitung</span>';
    const delta=(value-previous)*100;
    const direction=delta>0?'Naik':delta<0?'Turun':'Tetap',icon=delta>0?'↑':delta<0?'↓':'→';
    const favorable=key==='rr'?delta>0:delta<0;
    const tone=delta===0?'neutral':favorable?'good':'bad';
    const amount=Math.abs(delta).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});
    return `<span class="pr-delta pr-delta--${tone}" aria-label="${direction} ${amount}%">${icon} ${amount}%</span>`;
  }

  function renderActualMovements(movements,loading=false,error=false){
    const movementRows=[
      {key:'growth',label:'Growth bersih KYD',amount:movements.growth_net,accounts:null,note:'Selisih total KYD actual dan closing'},
      {key:'flow_par',label:'Flow PAR actual',amount:movements.flow_par,accounts:movements.flow_par_noa,note:'Closing L/DP menjadi KL, D, atau M actual'},
      {key:'recovery_npl',label:'Penyelesaian / recovery NPL',amount:movements.recovery_npl,accounts:movements.recovery_npl_noa,note:'Lunas, turun saldo NPL, atau kembali ke L/DP'},
      {key:'btc',label:'BTC aktual',amount:movements.btc,accounts:movements.btc_noa,note:'Closing DPD > 0, actual DPD 0 dan kolektibilitas L'},
      {key:'migration_d0',label:'Migrasi DPD 0 ke PAR',amount:movements.migration_d0,accounts:movements.migration_d0_noa,note:'Closing DPD 0, actual DPD > 0 di L/DP'}
    ];
    el('prActualMovements').innerHTML=movementRows.map(item=>{
      const amount=item.amount==null?(loading?'Memuat…':error?'Gagal dimuat':'—'):showMoney(item.amount);
      const disabled=loading?'disabled':'';
      return `<tr><td><span class="pr-movement-label">${item.label}</span><small class="pr-movement-note">${item.note}</small></td><td class="pr-number">${amount}</td><td><button class="pr-inline-link" type="button" data-movement="${item.key}" aria-label="Lihat rincian ${item.label}" ${disabled}>Lihat debitur</button></td></tr>`;
    }).join('');
    el('prMovementState').textContent=loading?'Memuat flow rekening…':error?'Ringkasan flow gagal dimuat.':'Flow actual siap';
  }

  async function loadMovementSummary(){
    const snapshot=state.snapshot;
    if(!snapshot)return;
    try{
      const response=await apiCall(`${apiBase}/movements`,{method:'POST',headers:{'Content-Type':'application/json'},cache:'no-store',body:JSON.stringify({
        closing_date:snapshot.closing_date,
        harian_date:snapshot.harian_date,
        kode_cabang:snapshot.kode_cabang,
        nominal_field:snapshot.nominal_field
      })});
      const json=await response.json();
      if(!response.ok||Number(json.status)>=400)throw new Error(json.message||'Ringkasan flow gagal dimuat.');
      if(state.snapshot!==snapshot)return;
      snapshot.actual_movements={...snapshot.actual_movements,...(json.data||{})};
      renderActualMovements(snapshot.actual_movements);
      const flowInput=el('prFlowPar');
      if(flowInput.dataset.userEdited!=='1'){
        flowInput.value=nominalNumber.format(Number(snapshot.actual_movements.flow_par)||0);
        if(!el('prResultSection').classList.contains('hidden'))calculate();
      }
    }catch(error){
      if(state.snapshot===snapshot)renderActualMovements(snapshot.actual_movements||{},false,true);
    }
  }

  function renderDetailEmpty(message,isError=false){
    const body=el('prDetailRows');body.replaceChildren();
    const row=document.createElement('tr'),cell=document.createElement('td');
    cell.colSpan=8;cell.className=`pr-empty ${isError?'text-red-700':''}`;cell.textContent=message;row.append(cell);body.append(row);
  }

  async function loadMovementDetail(page=1){
    if(!state.snapshot)return;
    if(detailState.abort)detailState.abort.abort();
    detailState.abort=new AbortController();detailState.page=page;
    renderDetailEmpty('Memuat rekening untuk pergerakan actual...');
    el('prDetailSummary').textContent='';
    try{
      const response=await apiCall(`${apiBase}/detail`,{method:'POST',headers:{'Content-Type':'application/json'},cache:'no-store',signal:detailState.abort.signal,body:JSON.stringify({
        movement:detailState.movement,
        closing_date:state.snapshot.closing_date,
        harian_date:state.snapshot.harian_date,
        kode_cabang:state.snapshot.kode_cabang,
        nominal_field:state.snapshot.nominal_field,
        page,
        page_size:25,
        search:detailState.search
      })});
      const json=await response.json();
      if(!response.ok||Number(json.status)>=400)throw new Error(json.message||'Detail debitur gagal dimuat.');
      const data=json.data||{};detailState.totalPages=Number(data.total_pages||0);
      const body=el('prDetailRows');body.replaceChildren();
      if(!data.rows?.length){renderDetailEmpty(detailState.search?'Tidak ada rekening yang cocok dengan pencarian.':'Tidak ada rekening pada pergerakan ini.');}
      else data.rows.forEach(item=>{
        const row=document.createElement('tr');
        const status=`${item.dpd_closing} / ${item.kolek_closing||'-'} ke ${item.dpd_actual} / ${item.kolek_actual||'-'}`;
        const values=[item.no_rekening,item.nama_nasabah||'-',showMoney(item.saldo_closing),showMoney(item.saldo_actual),showMoney(item.movement_amount),showMoney(item.tunggakan_pokok),showMoney(item.tunggakan_bunga),status];
        values.forEach((value,index)=>{
          const cell=document.createElement('td');cell.textContent=String(value);
          if(index>=2&&index<=6)cell.classList.add('pr-number');
          row.append(cell);
        });
        body.append(row);
      });
      el('prDetailSummary').textContent=`${Number(data.total_rows||0).toLocaleString('id-ID')} rekening | Total pergerakan ${showMoney(data.total_amount||0)}`;
      el('prDetailPageInfo').textContent=data.total_pages?`Halaman ${data.page} dari ${data.total_pages}`:'Tidak ada halaman';
      el('prDetailPrev').disabled=Number(data.page||1)<=1;
      el('prDetailNext').disabled=Number(data.page||1)>=Number(data.total_pages||0);
    }catch(error){
      if(error.name!=='AbortError'){
        renderDetailEmpty(error.message||'Detail debitur gagal dimuat.',true);
        el('prDetailPageInfo').textContent='Periksa filter tanggal atau coba muat ulang posisi.';
      }
    }
  }

  function openMovementDetail(movement){
    if(!state.snapshot)return;
    detailState.movement=movement;detailState.search='';detailState.page=1;
    el('prDetailSearch').value='';
    el('prDetailTitle').textContent=movementNames[movement]||'Detail pergerakan';
    el('prDetailSubtitle').textContent=`${state.snapshot.closing_date} ke ${state.snapshot.harian_date} | ${state.snapshot.nominal_field==='baki_debet'?'Baki Debet':'Saldo Bank'}`;
    const dialog=el('prDetailDialog');if(!dialog.open)dialog.showModal();
    loadMovementDetail(1);
  }

  function moneyDelta(value,previous){
    const delta=value-previous,direction=delta>0?'Naik':delta<0?'Turun':'Tetap',icon=delta>0?'↑':delta<0?'↓':'→';
    const amount=nominalNumber.format(Math.abs(delta));
    return `<span class="pr-delta pr-delta--neutral" aria-label="${direction} ${amount}">${icon} ${amount}</span>`;
  }

  function renderRecommendations({targetRr,targetNpl,base,upgradeCapacity,issues}){
    const host=el('prRecommendations');
    recommendationActions={};
    if(!Number.isFinite(targetRr)||!Number.isFinite(targetNpl)||targetRr<0||targetRr>1||targetNpl<0||targetNpl>1){host.innerHTML='<p class="pr-meta">Masukkan target RR dan NPL antara 0% sampai 100%.</p>';return;}
    if(issues.length){host.innerHTML='<p class="pr-meta">Perbaiki input proyeksi yang bermasalah untuk melihat rekomendasi.</p>';return;}

    const rrNow=base.kyd>0?base.dpd0/base.kyd:null,nplNow=base.kyd>0?base.npl/base.kyd:null;
    const rrNeed=rrNow!==null&&rrNow<targetRr-1e-10?Math.ceil(Math.max(0,targetRr*base.kyd-base.dpd0-1e-7)):0;
    const nplNeed=nplNow!==null&&nplNow>targetNpl+1e-10?Math.ceil(Math.max(0,base.npl-targetNpl*base.kyd-1e-7)):0;
    const evaluate=({upgrade=0,backflow=0,btc=0,preventMigration=0,payoff=0}={})=>{
      const kyd=base.kyd-payoff,npl=base.npl-upgrade-backflow-payoff,dpd0=base.dpd0+upgrade+btc+preventMigration;
      const rr=kyd>0?dpd0/kyd:null,nplRatio=kyd>0?npl/kyd:null;
      return {rr,nplRatio,ok:rr!==null&&nplRatio!==null&&rr+1e-10>=targetRr&&nplRatio<=targetNpl+1e-10};
    };
    const card=(id,title,description,items,actions,result,capacityIssue=false,recommended=false)=>{
      const hasActions=Object.values(actions).some(value=>value>0.01);
      const status=result.ok?'DUA TARGET TERCAPAI':capacityIssue?'CEK KAPASITAS':'BELUM DUA TARGET';
      const statusClass=result.ok?'is-good':capacityIssue?'is-capacity':'';
      const list=items.filter(item=>item.amount>0.01).map(item=>`<li>${item.label}: <strong>${showMoney(item.amount)}</strong></li>`).join('')||'<li>Tidak perlu aksi tambahan.</li>';
      const capacityNote=capacityIssue&&!result.ok?'<p class="pr-recommendation-description">Saldo NPL yang tersedia belum cukup untuk memenuhi kedua target.</p>':'';
      recommendationActions[id]=actions;
      return `<article class="pr-recommendation-card${recommended?' is-recommended':''}"><div class="pr-recommendation-head"><h4 class="pr-recommendation-title">${title}</h4><span class="pr-recommendation-status ${statusClass}">${status}</span></div><p class="pr-recommendation-description">${description}</p><ul class="pr-recommendation-list">${list}</ul>${capacityNote}<div class="pr-recommendation-footer"><p class="pr-recommendation-result">Hasil: RR <strong>${result.rr===null?'—':percent(result.rr)}</strong> · NPL <strong>${result.nplRatio===null?'—':percent(result.nplRatio)}</strong></p><button class="pr-btn pr-btn-primary" type="button" data-recommendation="${id}" ${!hasActions||(!result.ok&&capacityIssue)?'disabled':''}>Terapkan</button></div></article>`;
    };

    const s1Required=Math.max(rrNeed,nplNeed),s1Upgrade=Math.min(s1Required,upgradeCapacity),s1={upgrade:s1Upgrade};
    const s1Html=card('s1','S1 · Fokus upgrade NPL → DPD 0','Satu rupiah upgrade memperbaiki RR dan menurunkan NPL; KYD tetap.',[{label:'Upgrade NPL → DPD 0',amount:s1Required}],s1,evaluate(s1),s1Upgrade+0.01<s1Required);

    const preventMigration=Math.min(base.migration,Math.ceil(rrNeed*0.35));
    const btc=Math.min(Math.max(0,base.dpd1_30+preventMigration),Math.ceil(rrNeed*0.30));
    const backflowRequired=Math.ceil(nplNeed*0.5);
    const backflow=Math.min(upgradeCapacity,backflowRequired);
    const s2UpgradeRequired=Math.max(Math.max(0,nplNeed-backflow),Math.max(0,rrNeed-preventMigration-btc));
    const s2Upgrade=Math.min(s2UpgradeRequired,Math.max(0,upgradeCapacity-backflow)),s2={preventMigration,btc,backflow,upgrade:s2Upgrade};
    const s2Html=card('s2','S2 · Kombinasi tersebar','Cegah sebagian migrasi, tambah BTC dan backflow NPL ke PAR, lalu upgrade untuk menutup sisa target.',[
      {label:'Cegah migrasi DPD 0 → DPD 1–30',amount:preventMigration},
      {label:'Tambah BTC DPD 1–30 → DPD 0',amount:btc},
      {label:'Backflow NPL → PAR / DP',amount:backflowRequired},
      {label:'Upgrade NPL → DPD 0',amount:s2UpgradeRequired}
    ],s2,evaluate(s2),backflow+0.01<backflowRequired||s2Upgrade+0.01<s2UpgradeRequired,true);

    let s3Upgrade=Math.min(upgradeCapacity,Math.ceil(rrNeed*0.5));
    const payoffNeeded=upgrade=>targetNpl<1?Math.ceil(Math.max(0,(base.npl-upgrade-targetNpl*base.kyd-1e-7)/(1-targetNpl))):0;
    const s3PayoffRequired=payoffNeeded(s3Upgrade);
    let s3Payoff=Math.min(s3PayoffRequired,Math.max(0,upgradeCapacity-s3Upgrade));
    const rrNeedAfterPayoff=Math.ceil(Math.max(0,targetRr*(base.kyd-s3Payoff)-(base.dpd0+s3Upgrade)-1e-7));
    const s3ExtraUpgrade=Math.min(rrNeedAfterPayoff,Math.max(0,upgradeCapacity-s3Upgrade-s3Payoff));
    s3Upgrade+=s3ExtraUpgrade;
    const s3={upgrade:s3Upgrade,payoff:s3Payoff};
    const s3CapacityIssue=s3Payoff+0.01<s3PayoffRequired||s3ExtraUpgrade+0.01<rrNeedAfterPayoff;
    const s3Html=card('s3','S3 · Upgrade NPL + pelunasan','Bagi perbaikan RR antara upgrade NPL dan pelunasan yang mengurangi NPL serta KYD.',[
      {label:'Upgrade NPL → DPD 0',amount:s3Upgrade},
      {label:'Pelunasan / recovery NPL',amount:s3Payoff}
    ],s3,evaluate(s3),s3CapacityIssue);

    host.innerHTML=s1Html+s2Html+s3Html;
  }

  function calculate(){
    if(!state.snapshot)return;
    const c=state.snapshot.closing,a=state.snapshot.actual;
    const growth=readNominal('prGrowth'),btc=readNominal('prBtc'),migration=readNominal('prMigration'),flowPar=readNominal('prFlowPar'),settlement=readNominal('prSettlement'),nplUpgrade=readNominal('prNplUpgrade'),nplBackflow=readNominal('prNplBackflow');
    const targetRrPct=el('prTargetRr').value.trim()===''?NaN:Number(el('prTargetRr').value),targetNplPct=el('prTargetNpl').value.trim()===''?NaN:Number(el('prTargetNpl').value);
    const targetRr=targetRrPct/100,targetNpl=targetNplPct/100;
    const targetRrValid=Number.isFinite(targetRrPct)&&targetRrPct>=0&&targetRrPct<=100,targetNplValid=Number.isFinite(targetNplPct)&&targetNplPct>=0&&targetNplPct<=100;
    const values=[growth,btc,migration,flowPar,settlement,nplUpgrade,nplBackflow],issues=[];
    setInputWarning('prTargetRrWarning',targetRrValid?'':'Masukkan target RR dari 0 sampai 100%.');
    setInputWarning('prTargetNplWarning',targetNplValid?'':'Masukkan target NPL dari 0 sampai 100%.');
    if(values.some(value=>!Number.isFinite(value)||value<0))issues.push('Semua input harus nol atau positif.');
    if(!targetRrValid||!targetNplValid)issues.push('Target RR dan NPL harus berada di antara 0% dan 100%.');
    if(c.negative_rows>0||a.negative_rows>0)issues.push('Saldo sumber snapshot mengandung nilai negatif.');
    const closingKyd=c.l+c.dp+c.kl+c.d+c.m,actualKyd=a.l+a.dp+a.kl+a.d+a.m;
    if(closingKyd<=0)issues.push('KYD closing harus lebih besar dari nol.');
    if(c.dpd0>c.l)issues.push('Saldo DPD 0 melebihi saldo L pada closing.');
    const openingDpd1_30=c.l-c.dpd0;
    const dpd0BeforeUpgrade=c.dpd0+growth+btc-migration;
    const dpd1_30=openingDpd1_30-btc+migration;
    let recoveryRemaining=Math.max(settlement,0);
    const recoveryKl=Math.min(recoveryRemaining,Math.max(c.kl,0));
    recoveryRemaining-=recoveryKl;
    const recoveryD=Math.min(recoveryRemaining,Math.max(c.d,0));
    recoveryRemaining-=recoveryD;
    const recoveryM=Math.min(recoveryRemaining,Math.max(c.m,0));
    recoveryRemaining-=recoveryM;
    const afterRecoveryKl=Math.max(c.kl-recoveryKl,0),afterRecoveryD=Math.max(c.d-recoveryD,0),afterRecoveryM=Math.max(c.m-recoveryM,0);
    const afterRecoveryNpl=afterRecoveryKl+afterRecoveryD+afterRecoveryM;
    let upgradeRemaining=Math.max(nplUpgrade,0);
    const upgradeKl=Math.min(upgradeRemaining,afterRecoveryKl);
    upgradeRemaining-=upgradeKl;
    const upgradeD=Math.min(upgradeRemaining,afterRecoveryD);
    upgradeRemaining-=upgradeD;
    const upgradeM=Math.min(upgradeRemaining,afterRecoveryM);
    upgradeRemaining-=upgradeM;
    const upgradeApplied=nplUpgrade-upgradeRemaining;
    let backflowRemaining=Math.max(nplBackflow,0);
    const backflowKl=Math.min(backflowRemaining,Math.max(0,afterRecoveryKl-upgradeKl));
    backflowRemaining-=backflowKl;
    const backflowD=Math.min(backflowRemaining,Math.max(0,afterRecoveryD-upgradeD));
    backflowRemaining-=backflowD;
    const backflowM=Math.min(backflowRemaining,Math.max(0,afterRecoveryM-upgradeM));
    backflowRemaining-=backflowM;
    const backflowApplied=nplBackflow-backflowRemaining;
    const dpd0=dpd0BeforeUpgrade+upgradeApplied;
    const kl=Math.max(afterRecoveryKl-upgradeKl-backflowKl,0)+flowPar,d=Math.max(afterRecoveryD-upgradeD-backflowD,0),m=Math.max(afterRecoveryM-upgradeM-backflowM,0);
    const dp=c.dp-flowPar+backflowApplied;
    const upgradeCapacity=Math.max(0,afterRecoveryNpl-upgradeApplied-backflowApplied);
    setInputWarning('prFlowParWarning',dp< -0.01?`Flow DP ke KL melebihi saldo DP dan backflow yang tersedia sebesar ${showMoney(Math.abs(dp))}; saldo DP proyeksi akan minus.`:'');
    setInputWarning('prBtcWarning',dpd1_30< -0.01?`Proyeksi DPD 1–30 minus ${showMoney(Math.abs(dpd1_30))}; kurangi BTC atau sesuaikan migrasi.`:'');
    setInputWarning('prMigrationWarning',dpd0< -0.01?`Proyeksi DPD 0 minus ${showMoney(Math.abs(dpd0))}; kurangi migrasi DPD 0.`:'');
    setInputWarning('prSettlementWarning',recoveryRemaining>0.01?`Recovery melebihi saldo NPL sebesar ${showMoney(recoveryRemaining)}; kurangi recovery.`:'');
    setInputWarning('prNplUpgradeWarning',upgradeRemaining>0.01?`Upgrade melebihi saldo NPL yang tersedia sebesar ${showMoney(upgradeRemaining)}.`:'');
    setInputWarning('prNplBackflowWarning',backflowRemaining>0.01?`Backflow melebihi saldo NPL yang tersedia sebesar ${showMoney(backflowRemaining)}.`:'');
    if(dpd0< -0.01)issues.push('Hasil DPD 0 negatif; kurangi nilai migrasi.');
    if(dpd1_30< -0.01)issues.push('Proyeksi DPD 1–30 negatif; kurangi BTC atau sesuaikan migrasi.');
    if(dp< -0.01)issues.push('Proyeksi Flow DP ke KL melebihi saldo DP yang tersedia.');
    if(recoveryRemaining>0.01)issues.push('Recovery NPL melebihi total saldo KL, D, dan M yang tersedia.');
    if(upgradeRemaining>0.01)issues.push('Upgrade NPL melebihi saldo KL, D, dan M yang tersedia setelah recovery.');
    if(backflowRemaining>0.01)issues.push('Backflow NPL melebihi saldo KL, D, dan M yang tersedia.');
    const l=dpd0+dpd1_30,npl=kl+d+m,kyd=l+dp+npl;
    if(kyd<=0)issues.push('KYD proyeksi harus lebih besar dari nol.');
    const rr=kyd>0?dpd0/kyd:null,nplRatio=kyd>0?npl/kyd:null;
    renderRecommendations({targetRr,targetNpl,base:{dpd0,dpd1_30,npl,kyd,migration},upgradeCapacity,issues});
    const closingNpl=c.kl+c.d+c.m,actualNpl=a.kl+a.d+a.m;
    const closingDpd1To30=c.l-c.dpd0,actualDpd1To30=a.l-a.dpd0;
    const closingRr=closingKyd>0?c.dpd0/closingKyd:null,actualRr=actualKyd>0?a.dpd0/actualKyd:null;
    const closingNplRatio=closingKyd>0?closingNpl/closingKyd:null,actualNplRatio=actualKyd>0?actualNpl/actualKyd:null;
    const rows=[
      {label:'DPD 0',closing:c.dpd0,actual:a.dpd0,projection:dpd0},
      {label:'DPD 1–30 (L)',closing:closingDpd1To30,actual:actualDpd1To30,projection:dpd1_30},
      {label:'Lancar (L)',closing:c.l,actual:a.l,projection:l},
      {label:'Perhatian (DP)',closing:c.dp,actual:a.dp,projection:dp},
      {label:'KL',closing:c.kl,actual:a.kl,projection:kl},
      {label:'Diragukan (D)',closing:c.d,actual:a.d,projection:d},
      {label:'Macet (M)',closing:c.m,actual:a.m,projection:m},
      {label:'NPL Gross',closing:closingNpl,actual:actualNpl,projection:npl},
      {label:'KYD',closing:closingKyd,actual:actualKyd,projection:kyd},
      {label:'RR',type:'ratio',key:'rr',closing:closingRr,actual:actualRr,projection:rr},
      {label:'NPL Gross (%)',type:'ratio',key:'npl',closing:closingNplRatio,actual:actualNplRatio,projection:nplRatio}
    ];
    const valueCell=(value,type)=>type==='ratio'?(value===null?'—':percent(value)):nominalNumber.format(Number(value||0));
    const deltaCell=(value,base,type,key)=>type==='ratio'?ratioDelta(value,base,key):moneyDelta(value,base);
    el('prBreakdown').innerHTML=rows.map(row=>`<tr><td><strong>${row.label}</strong></td><td class="pr-number">${valueCell(row.closing,row.type)}</td><td class="pr-number">${valueCell(row.actual,row.type)}</td><td class="pr-number">${valueCell(row.projection,row.type)}</td><td class="pr-delta-cell">${deltaCell(row.actual,row.closing,row.type,row.key)}</td><td class="pr-delta-cell">${deltaCell(row.projection,row.closing,row.type,row.key)}</td></tr>`).join('');
    const invalid=issues.length>0;
    setStatus(el('prResultStatus'),invalid?'bad':'ok',invalid?'PERIKSA INPUT':'PROYEKSI VALID');
    const [year,month]=state.snapshot.projection_date.split('-').map(Number);
    el('prResultMeta').textContent=`Closing ${state.snapshot.closing_date} sampai ${state.snapshot.projection_date} · Flow DP ke KL ${showMoney(flowPar)} · Recovery NPL ${showMoney(settlement)} · Upgrade ke DPD 0 ${showMoney(upgradeApplied)} · Backflow ke DP ${showMoney(backflowApplied)} · ${new Date(Date.UTC(year,month-1,1)).toLocaleDateString('id-ID',{month:'long',year:'numeric',timeZone:'UTC'})}`;
    el('prResultSection').classList.remove('hidden');
    el('prValidation').textContent=issues.join(' ')||'Proyeksi otomatis dihitung dari closing M-1; actual menjadi pembanding.';
    el('prValidation').classList.toggle('text-red-600',invalid);
  }
  function invalidate(){state.snapshot=null;el('prSnapshotSection').classList.add('hidden');el('prInputSection').classList.add('hidden');el('prResultSection').classList.add('hidden');if(el('prDetailDialog').open)el('prDetailDialog').close();}
  el('proyeksiRrNavbarFilterToggle')?.addEventListener('click',()=>setProyeksiNavbarFilter(el('proyeksiRrNavbarFilterPanel').classList.contains('hidden')));
  el('proyeksiRrNavbarFilterClose')?.addEventListener('click',()=>setProyeksiNavbarFilter(false));
  el('proyeksiRrNavbarFilterPanel')?.addEventListener('submit',event=>{event.preventDefault();loadSnapshot();});
  el('prBranch').addEventListener('change',()=>{invalidate();loadMetadata(el('prBranch').value);});
  el('prBasis').addEventListener('change',invalidate);
  el('prClosing').addEventListener('change',()=>{syncActualToClosing();invalidate();if(!el('prActual').value)setMessage('Pilih closing akhir bulan yang memiliki snapshot actual di bulan berikutnya.',true);else setMessage('Tanggal actual terpilih untuk closing ini.');});
  el('prActual').addEventListener('change',()=>{syncClosingToActual();invalidate();if(!el('prClosing').value)setMessage('Closing M-1 untuk tanggal actual ini belum tersedia.',true);else setMessage('Closing M-1 mengikuti tanggal actual yang dipilih.');});

  el('prReset').addEventListener('click',()=>{['prGrowth','prBtc','prMigration','prSettlement','prNplUpgrade','prNplBackflow'].forEach(id=>el(id).value='0');el('prTargetRr').value='74.15';el('prTargetNpl').value='14.90';const flowInput=el('prFlowPar');const flowActual=Number(state.snapshot?.actual_movements?.flow_par);flowInput.value=Number.isFinite(flowActual)?nominalNumber.format(flowActual):'';delete flowInput.dataset.userEdited;if(state.snapshot)calculate();});
  el('proyeksiRr').addEventListener('input',event=>{const input=event.target.closest('[data-pr-nominal]');if(input){input.dataset.userEdited='1';formatNominalInput(input);if(state.snapshot)calculate();}});
  ['prTargetRr','prTargetNpl'].forEach(id=>el(id).addEventListener('input',()=>{if(state.snapshot)calculate();}));
  el('prRecommendations').addEventListener('click',event=>{
    const button=event.target.closest('[data-recommendation]');
    if(!button)return;
    const actions=recommendationActions[button.dataset.recommendation];
    if(!actions)return;
    const update=(id,value)=>{const input=el(id);input.value=nominalNumber.format(Math.max(0,Math.round(value)));input.dataset.userEdited='1';};
    if(actions.preventMigration)update('prMigration',readNominal('prMigration')-actions.preventMigration);
    if(actions.btc)update('prBtc',readNominal('prBtc')+actions.btc);
    if(actions.upgrade)update('prNplUpgrade',readNominal('prNplUpgrade')+actions.upgrade);
    if(actions.backflow)update('prNplBackflow',readNominal('prNplBackflow')+actions.backflow);
    if(actions.payoff)update('prSettlement',readNominal('prSettlement')+actions.payoff);
    calculate();
  });
  el('proyeksiRr').addEventListener('focusin',event=>{const input=event.target.closest('[data-pr-nominal]');if(input&&input.value==='0')input.select();});
  el('prActualMovements').addEventListener('click',event=>{const button=event.target.closest('[data-movement]');if(button)openMovementDetail(button.dataset.movement);});
  el('prDetailSearchForm').addEventListener('submit',event=>{event.preventDefault();detailState.search=el('prDetailSearch').value.trim();loadMovementDetail(1);});
  el('prDetailPrev').addEventListener('click',()=>loadMovementDetail(Math.max(1,detailState.page-1)));
  el('prDetailNext').addEventListener('click',()=>loadMovementDetail(Math.min(detailState.totalPages,detailState.page+1)));
  el('prDetailClose').addEventListener('click',()=>el('prDetailDialog').close());
  el('prDetailDialog').addEventListener('click',event=>{if(event.target===el('prDetailDialog'))el('prDetailDialog').close();});
  el('prDetailDialog').addEventListener('close',()=>{if(detailState.abort)detailState.abort.abort();});
  loadMetadata();
})();
</script>

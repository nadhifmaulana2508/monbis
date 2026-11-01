<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-page w-full max-w-[1680px] mx-auto px-3 sm:px-5 lg:px-7 py-3 md:py-5 bg-gray-50 min-h-screen font-sans overflow-x-hidden">
    <svg xmlns="http://www.w3.org/2000/svg" class="hidden" aria-hidden="true" focusable="false">
      <symbol id="dashboard-icon-chart" viewBox="0 0 24 24"><path d="M3 3v18h18M7 14l4-4 4 3 5-7"/><path d="M17 6h3v3"/></symbol>
      <symbol id="dashboard-icon-trend" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-9"/><path d="M15 6h6v6"/></symbol>
      <symbol id="dashboard-icon-box" viewBox="0 0 24 24"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="M3 8v9l9 5 9-5V8M12 13v9"/></symbol>
      <symbol id="dashboard-icon-refresh" viewBox="0 0 24 24"><path d="M20 7v5h-5M4 17v-5h5"/><path d="M5.6 9A7 7 0 0 1 18 6l2 6M4 12l2 6a7 7 0 0 0 12.4-3"/></symbol>
      <symbol id="dashboard-icon-shield" viewBox="0 0 24 24"><path d="M12 3 20 6v5c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-3Z"/><path d="m9 12 2 2 4-4"/></symbol>
      <symbol id="dashboard-icon-alert" viewBox="0 0 24 24"><path d="m10.3 4.3-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-2.7l-8-14a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4m0 4h.01"/></symbol>
      <symbol id="dashboard-icon-award" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="m8.2 13-1 8 4.8-2.5 4.8 2.5-1-8M9.5 8l1.7 1.7L14.8 6"/></symbol>
      <symbol id="dashboard-icon-lightbulb" viewBox="0 0 24 24"><path d="M9 18h6m-5 4h4m-5-7a7 7 0 1 1 6 0c-.8.6-1 1.3-1 3h-4c0-1.7-.2-2.4-1-3Z"/></symbol>
      <symbol id="dashboard-icon-coins" viewBox="0 0 24 24"><ellipse cx="9" cy="6" rx="6" ry="3"/><path d="M3 6v5c0 1.7 2.7 3 6 3 1 0 2-.1 2.8-.4M3 11v5c0 1.7 2.7 3 6 3 .8 0 1.6-.1 2.3-.3"/><path d="M15 10c3.3 0 6 1.3 6 3s-2.7 3-6 3-6-1.3-6-3 2.7-3 6-3Zm-6 3v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/></symbol>
      <symbol id="dashboard-icon-bank" viewBox="0 0 24 24"><path d="m3 9 9-6 9 6M4 10h16M5 10v9m4-9v9m6-9v9m4-9v9M3 21h18M2 19h20"/></symbol>
      <symbol id="dashboard-icon-wallet" viewBox="0 0 24 24"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H20v16H5a2 2 0 0 1-2-2V6.5Z"/><path d="M3 8h17m-4 5h4m-4 0h.01"/></symbol>
    </svg>
    
    <?php include 'components/header_filter.php'; ?>

    <div id="loadingDash" class="hidden flex flex-col justify-center items-center py-16 md:py-28">
        <div class="animate-spin rounded-full h-10 w-10 md:h-14 md:w-14 border-t-4 border-b-4 border-blue-600 mb-4"></div>
        <span class="text-xs md:text-sm text-gray-500 font-semibold animate-pulse">Loading data dari database...</span>
    </div>

    <div id="contentDash" class="hidden space-y-5 md:space-y-7 overflow-x-hidden">
        
        <?php include 'components/kpi_cards.php'; ?>

        <?php include 'components/chart_kredit.php'; ?>

        <?php include 'components/chart_runoff_npl.php'; ?>

        <?php include 'components/kinerja_npl.php'; ?>

        <?php include 'components/best_performance.php'; ?>

        <?php include 'components/simpanan.php'; ?>

    </div>
</div>

<style>
  .dashboard-page,
  .dashboard-page * {
    font-family: 'Roboto', Arial, sans-serif;
  }
  .dashboard-icon { display:inline-block; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; vertical-align:middle; }
  .dashboard-page { color:#172033; background:#f4f6f9 !important; }
  .dashboard-header { padding-bottom:12px; border-bottom:1px solid #e1e7ef; }
  .dashboard-header h1 { font-size:clamp(1.15rem, 2vw, 1.65rem); line-height:1.2; letter-spacing:-.025em; }
  .dashboard-header p { max-width:70ch; font-size:11px; }
  .dashboard-panel { border-color:#e1e7ef !important; border-radius:16px !important; box-shadow:0 1px 3px rgba(15,23,42,.045) !important; }
  .dashboard-section-heading { letter-spacing:-.02em; }
  .dashboard-kpi-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
  .dashboard-kpi-grid .kpi-card:last-child { grid-column:auto !important; }
  .dashboard-kpi-grid .kpi-card { min-height:112px; padding:13px 14px !important; border-radius:14px !important; border-color:#e1e7ef !important; background:#fff !important; box-shadow:0 1px 2px rgba(15,23,42,.04) !important; }
  .dashboard-kpi-grid .kpi-card > .absolute { display:none; }
  .dashboard-kpi-grid .kpi-card > p:not(.kpi-date-label) { margin-bottom:8px; color:#64748b; font-size:10px; letter-spacing:.035em; }
  .dashboard-kpi-grid .kpi-card h3 { margin-bottom:8px; overflow:hidden; font-size:clamp(1rem,1.45vw,1.55rem); line-height:1.1; text-overflow:ellipsis; }
  .dashboard-kpi-grid .kpi-pill { gap:5px; }
  .dashboard-kpi-grid .kpi-card--saldo h3,
  .dashboard-kpi-grid .kpi-card--dpk h3,
  .dashboard-kpi-grid .kpi-card--laba h3 { color:#1d4ed8; }
  .dashboard-chart-panel { min-width:0; }
  .dashboard-chart-panel h3 { letter-spacing:-.01em; }
  .dashboard-page canvas { max-width:100%; }
  .dashboard-savings-grid { min-width:0; }
  .bar-fill { transition: height 1s cubic-bezier(0.4, 0, 0.2, 1), width 1s ease-in-out; }
  .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
  .custom-scrollbar::-webkit-scrollbar { width: 3px; height: 3px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .kpi-pill { min-width: 0; max-width: 100%; overflow: visible; display: flex; flex-direction: column; gap: 4px; }
  .kpi-pill > div { min-width: 0; max-width: 100%; flex-wrap: wrap; row-gap: 2px; }
  .kpi-compare-row { display: flex; align-items: center; gap: 6px; min-width: 0; min-height: 21px; }
  .kpi-compare-badge { display: inline-flex; align-items: center; gap: 4px; min-width: 0; max-width: 100%; padding: 4px 7px; border-radius: 6px; font-size: 9px; line-height: 1; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .kpi-compare-badge span { font-weight: 950; }
  .kpi-compare-badge--month { color: #475569; background: #f1f5f9; border: 1px solid #e2e8f0; }
  .kpi-compare-badge--month span { color: #0f172a; }
  .kpi-compare-badge--year { color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; }
  .kpi-compare-badge--year span { color: #1e40af; }
  .kpi-status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 7px; border-radius: 6px; font-size: 9px; line-height: 1; font-weight: 800; white-space: nowrap; }
  .kpi-status-badge span { font-weight: 950; }
  .kpi-status-badge--negative { color: #dc2626; background: #fef2f2; border: 1px solid #fecaca; }
  .kpi-status-badge--negative span { color: #b91c1c; }
  .kpi-status-badge--positive { color: #059669; background: #ecfdf5; border: 1px solid #bbf7d0; }
  .kpi-status-badge--positive span { color: #047857; }
  .kpi-status-badge--right { margin-left: auto; }
  .kpi-date-label {
    position: absolute;
    top: 12px;
    right: 12px;
    color: #94a3b8;
    font-size: 8px;
    line-height: 1;
    font-weight: 700;
    white-space: nowrap;
  }
  .kpi-loading-spinner {
    display: inline-block;
    width: 1em;
    height: 1em;
    border: 2px solid currentColor;
    border-top-color: transparent;
    border-radius: 9999px;
    animation: kpi-spin 0.75s linear infinite;
    vertical-align: -0.12em;
  }
  @keyframes kpi-spin { to { transform: rotate(360deg); } }
  .kpi-compare-row .text-\[9px\] { font-size: 9px; }
  @media (max-width: 639px) {
    .dashboard-page { padding-left:10px; padding-right:10px; }
    .dashboard-header { padding-bottom:10px; }
    .dashboard-kpi-grid .kpi-card { min-height:106px; padding:11px 12px !important; }
    .dashboard-kpi-grid .kpi-card h3 { font-size:clamp(.94rem,4.2vw,1.2rem); }
    .kpi-compare-row { gap: 4px; }
    .kpi-compare-badge, .kpi-status-badge { padding: 5px 7px; font-size: 9px; }
    .kpi-date-label { top: 13px; right: 14px; font-size: 8px; }
  }
  @media (min-width:640px) {
    .dashboard-kpi-grid { grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
  }
  @media (min-width:1024px) {
    .dashboard-kpi-grid { grid-template-columns:repeat(5,minmax(0,1fr)); gap:14px; }
  }
  @media (max-width:420px) {
    .dashboard-savings-grid { grid-template-columns:1fr !important; }
    .dashboard-panel { border-radius:13px !important; }
  }
  .kpi-card {
  }
  .kpi-card--npl .kpi-status-badge--right,
  .kpi-card--rr .kpi-status-badge--right {
    position: absolute;
    top: 15px;
    right: 16px;
    margin: 0 !important;
    z-index: 2;
  }
  @media (max-width: 639px) {
    .kpi-card--npl .kpi-status-badge--right,
    .kpi-card--rr .kpi-status-badge--right { top: 13px; right: 12px; }
  }

  /* Dashboard theme: keep dark mode readable without changing the light layout. */
  :root[data-monbis-theme="dark"] .dashboard-page {
    background: #0b1220 !important;
    color: #e5e7eb;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .bg-white,
  :root[data-monbis-theme="dark"] .dashboard-page .bg-gray-50 {
    background-color: #111827 !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .border-gray-100,
  :root[data-monbis-theme="dark"] .dashboard-page .border-gray-200 {
    border-color: #334155 !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .border-gray-50 {
    border-color: #1e293b !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .text-gray-900,
  :root[data-monbis-theme="dark"] .dashboard-page .text-gray-800,
  :root[data-monbis-theme="dark"] .dashboard-page .text-gray-700 {
    color: #f8fafc !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .text-gray-600,
  :root[data-monbis-theme="dark"] .dashboard-page .text-gray-500,
  :root[data-monbis-theme="dark"] .dashboard-page .text-gray-400 {
    color: #94a3b8 !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .bg-gray-100 {
    background-color: #1e293b !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .bg-blue-50 {
    background-color: #172554 !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .bg-blue-200 {
    background-color: #1e40af !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .text-blue-800,
  :root[data-monbis-theme="dark"] .dashboard-page .text-blue-900 {
    color: #bfdbfe !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .text-blue-600 {
    color: #93c5fd !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .text-indigo-700 {
    color: #c7d2fe !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page select,
  :root[data-monbis-theme="dark"] .dashboard-page input[type="date"] {
    color: #e5e7eb !important;
    background-color: #111827 !important;
    border-color: #475569 !important;
    color-scheme: dark;
  }
  :root[data-monbis-theme="dark"] #formFilterMaster,
  :root[data-monbis-theme="dark"] #btnToggleFilter {
    background-color: #111827 !important;
    border-color: #334155 !important;
    color: #e5e7eb !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .kpi-card {
    background:#111827 !important;
    border-color: #334155 !important;
    box-shadow: 0 12px 28px rgba(0,0,0,.24);
  }
  :root[data-monbis-theme="dark"] .dashboard-page .kpi-compare-badge--month {
    color: #cbd5e1;
    background: #1e293b;
    border-color: #475569;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .kpi-compare-badge--month span { color: #f8fafc; }
  :root[data-monbis-theme="dark"] .dashboard-page .kpi-compare-badge--year {
    color: #93c5fd;
    background: #172554;
    border-color: #1d4ed8;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .kpi-compare-badge--year span { color: #bfdbfe; }
  :root[data-monbis-theme="dark"] .dashboard-page .kpi-status-badge--negative {
    color: #fca5a5; background: #450a0a; border-color: #7f1d1d;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .kpi-status-badge--negative span { color: #fecaca; }
  :root[data-monbis-theme="dark"] .dashboard-page .kpi-status-badge--positive {
    color: #86efac; background: #052e16; border-color: #166534;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .kpi-status-badge--positive span { color: #bbf7d0; }
  :root[data-monbis-theme="dark"] .dashboard-page .custom-scrollbar {
    scrollbar-color: #475569 transparent;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .bg-red-50 {
    background-color: #450a0a !important;
    border-color: #7f1d1d !important;
  }
  :root[data-monbis-theme="dark"] .dashboard-page .bg-green-50 {
    background-color: #052e16 !important;
    border-color: #166534 !important;
  }
</style>

<?php include 'components/scripts.php'; ?>

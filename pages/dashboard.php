<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="dashboard-page w-full max-w-[1400px] mx-auto px-2 sm:px-3 md:px-4 py-4 md:py-6 bg-gray-50 min-h-screen font-sans overflow-x-hidden">
    
    <?php include 'components/header_filter.php'; ?>

    <div id="loadingDash" class="hidden flex flex-col justify-center items-center py-32">
        <div class="animate-spin rounded-full h-10 w-10 md:h-14 md:w-14 border-t-4 border-b-4 border-blue-600 mb-4"></div>
        <span class="text-xs md:text-sm text-gray-500 font-semibold animate-pulse">Loading data dari database...</span>
    </div>

    <div id="contentDash" class="hidden space-y-4 md:space-y-6 overflow-x-hidden">
        
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
  .kpi-compare-row .text-\[9px\] { font-size: 9px; }
  @media (max-width: 639px) {
    .kpi-compare-row { gap: 4px; }
    .kpi-compare-badge, .kpi-status-badge { padding: 4px 5px; font-size: 8px; }
  }
  .kpi-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(248,250,252,0.92));
    border-color: rgba(226,232,240,0.9);
    border-radius: 1.25rem;
    box-shadow: 0 8px 24px rgba(15,23,42,0.06);
    transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
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
  .kpi-card:hover { transform: translateY(-2px); border-color: rgba(148,163,184,0.65); box-shadow: 0 12px 28px rgba(15,23,42,0.1); }

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
    background: linear-gradient(135deg, #111827, #172033) !important;
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

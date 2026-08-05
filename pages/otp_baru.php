<style>
  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  /* Animasi Modal */
  @keyframes scaleUp { from { transform: scale(0.98); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.15s ease-out forwards; }

  /* ========================================================
     MAGIC STICKY TABLE UTAMA (CLEAN LOOK)
     ======================================================== */
  #tabelRekapRR th { position: sticky !important; z-index: 10; }
  #tabelRekapRR thead th.head-row { cursor: pointer; transition: background 0.2s; }
  #tabelRekapRR thead th.head-row:hover { filter: brightness(0.95); }
  
  .rr-row-1 { height: 36px; }
  .rr-row-2 { height: 34px; }
  .rr-row-tot { height: 42px; }
  @media (min-width: 768px) {
      .rr-row-1 { height: 44px; }
      .rr-row-2 { height: 40px; }
      .rr-row-tot { height: 48px; }
  }

  #tabelRekapRR thead tr:nth-child(1) th { top: 0 !important; z-index: 30; }
  #tabelRekapRR thead tr:nth-child(2) th { top: 36px !important; z-index: 29; }
  #tabelRekapRR thead tr.sticky-total th { top: 70px !important; z-index: 40 !important; box-shadow: 0 2px 4px -1px rgba(0,0,0,0.05); }
  @media (min-width: 768px) { 
      #tabelRekapRR thead tr:nth-child(2) th { top: 44px !important; } 
      #tabelRekapRR thead tr.sticky-total th { top: 84px !important; }
  }

  #tabelRekapRR th.sticky.left-0 { z-index: 50 !important; }
  #tabelRekapRR td.sticky.left-0 { position: sticky !important; left: 0; z-index: 20; background-color: #fff; box-shadow: 1px 0 0 #cbd5e1; }
  #tabelRekapRR tr.sticky-total th.sticky.left-0 { z-index: 45 !important; background-color: #f1f5f9 !important; }

  /* ========================================================
     TABEL MODAL DETAIL RR (FIX STICKY & CLEAN TAMPILAN)
     ======================================================== */
  #tableExportRR { border-collapse: separate; border-spacing: 0; min-width: 100%; }
  #tableExportRR th, #tableExportRR td { background-clip: padding-box; background-color: #fff; }
  #tableExportRR thead th { position: sticky !important; top: 0 !important; z-index: 40 !important; background-color: #f1f5f9 !important; box-shadow: inset 0 -1px 0 #cbd5e1; }

  .col-rek { position: sticky !important; left: 0 !important; min-width: 100px; max-width: 100px; box-shadow: inset -1px 0 0 #e2e8f0; }
  .col-nas { position: sticky !important; left: 100px !important; min-width: 160px; max-width: 160px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1), inset -1px 0 0 #e2e8f0; }
  .col-nas-lunas { position: sticky !important; left: 0 !important; min-width: 160px; max-width: 160px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1), inset -1px 0 0 #e2e8f0; }

  @media (min-width: 768px) { 
      .col-rek { min-width: 120px; max-width: 120px; }
      .col-nas { left: 120px !important; min-width: 250px; max-width: 250px; } 
      .col-nas-lunas { min-width: 250px; max-width: 250px; } 
  }

  #bodyModalRR td.col-rek, #bodyModalRR td.col-nas, #bodyModalRR td.col-nas-lunas { z-index: 20 !important; background-color: #fff !important; }
  #headModalRR th.col-rek, #headModalRR th.col-nas, #headModalRR th.col-nas-lunas { z-index: 50 !important; background-color: #f1f5f9 !important; }

  tbody tr:hover td { background-color: #f8fafc !important; }
  #bodyModalRR tr:hover td.col-rek, #bodyModalRR tr:hover td.col-nas, #bodyModalRR tr:hover td.col-nas-lunas { filter: brightness(0.98); }
  
  #tabelRekapRR td { font-size: 12px; line-height: 1.15; }
  #tableExportRR td { font-size: 11px; line-height: 1.2; }
  @media (min-width: 768px) {
    #tabelRekapRR td { font-size: 13px; }
    #tableExportRR td { font-size: 12px; }
  }

  /* Form Inputs */
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; background:#fff; outline:none; transition: border 0.2s; height: 34px; font-weight: 600; font-size: 13px; color: #334155; }
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f1f5f9; color: #64748b; cursor: not-allowed; }
  .lbl { font-size:10px; color:#475569; font-weight:700; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap; }
  @media (min-width: 768px) { .inp { border-radius: 8px; padding:0 10px; } }
  
  .field { display:flex; flex-direction:column; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }

  input[type="date"]::-webkit-inner-spin-button, input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
  input[type="date"] { -moz-appearance: textfield; }
  
  .filter-collapsed { max-height: 0; overflow: hidden; opacity: 0; padding-top: 0; padding-bottom: 0; margin-top: 0; }
  .filter-expanded { max-height: 500px; overflow: visible; opacity: 1; }
  .filter-transition { transition: max-height 0.3s ease, opacity 0.2s ease, padding 0.2s ease, margin 0.2s ease; }
  
  @media (min-width: 1280px) {
    .filter-transition { max-height: none !important; opacity: 1 !important; overflow: visible !important; }
  }

  /* UI POLISH V2 */
  .otp-shell { min-height:0; }
  .otp-card { background:#fff; border:1px solid #e2e8f0; box-shadow:0 2px 4px rgba(0,0,0,0.02); overflow:visible !important; }
  
  .otp-info-root { position:relative; z-index:200000 !important; }
  .otp-info-root .otp-help-panel { z-index:200001 !important; max-width:calc(100vw - 28px); box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); }
  .otp-info-root:hover .otp-help-panel { display:none; }

  .otp-due-summary { position: relative; z-index: 25; }
  .otp-due-card {
    width: 100%;
    border: 1px solid;
    border-radius: 10px;
    padding: 10px 12px;
    text-align: left;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .otp-due-label {
    font-size: 10px;
    line-height: 1.1;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
  }
  .otp-due-value {
    margin-top: 4px;
    font-size: 20px;
    line-height: 1;
    font-weight: 900;
    color: #0f172a;
  }
  .otp-due-pct {
    flex: 0 0 auto;
    border-radius: 999px;
    background: rgba(255,255,255,.72);
    padding: 4px 8px;
    font-size: 12px;
    line-height: 1;
    font-weight: 900;
  }
  .otp-due-meta {
    margin-top: 8px;
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 11px;
    line-height: 1.2;
    font-weight: 700;
    color: #475569;
  }

  #tabelRekapRR th { background:#f1f5f9 !important; color:#1e293b !important; }
  #tabelRekapRR td, #tableExportRR td { color:#334155; }
  #tabelRekapRR a, #tableExportRR a { color:#2563eb !important; text-decoration:none; }
  #tabelRekapRR a:hover, #tableExportRR a:hover { color:#1d4ed8 !important; text-decoration:underline; }
  
  #tabelRekapRR td[class*="bg-blue"], #tabelRekapRR td[class*="bg-green"], #tabelRekapRR td[class*="bg-red"], #tabelRekapRR td[class*="bg-purple"], #tabelRekapRR td[class*="bg-amber"],
  #tableExportRR td[class*="bg-blue"], #tableExportRR td[class*="bg-green"], #tableExportRR td[class*="bg-red"], #tableExportRR td[class*="bg-cyan"], #tableExportRR td[class*="bg-amber"] { background-color:transparent !important; }
  
  #tabelRekapRR tr:hover td, #tableExportRR tr:hover td { background-color:#f8fafc !important; }

  .otp-modal-head { box-shadow:0 1px 0 rgba(148,163,184,.24); }
  #modalDetailRR > .relative { border:1px solid rgba(226,232,240,.9); box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); }
  #tableExportRR thead th { background:#f1f5f9 !important; color:#334155 !important; border-bottom: 1px solid #cbd5e1; }
  
  @media (min-width:1280px) {
    .otp-card { display:flex !important; flex-direction: row; align-items:center !important; gap:16px !important; padding:12px 16px !important; }
    .otp-title-wrap { flex: 0 0 auto; width: auto; }
    #filterWrapperMain { width:100% !important; margin:0 !important; padding:0 !important; border:0 !important; flex: 1 1 auto; display: flex; justify-content: flex-end; min-w:0; }
  }

  @media (max-width:640px) {
    .otp-shell { height:100dvh !important; padding:8px !important; }
    #tabelRekapRR { min-width:960px; }
    #tableExportRR { min-width:1180px; }
    .otp-modal-head .btn-icon span { display:none; }
    .otp-modal-head .btn-icon { width:36px; padding-left:0 !important; padding-right:0 !important; }
    .col-nas { min-width:170px !important; max-width:170px !important; left:0 !important; }
    .col-rek { display:none !important; }
    .col-nas-lunas { min-width:180px !important; max-width:180px !important; }
  }

  /* READABILITY V5 - NON-BOLD CLEAN TEXT */
  #tabelRekapRR, #tableExportRR { font-variant-numeric: tabular-nums; }

  #tabelRekapRR thead th {
    background: #f1f5f9 !important;
    color: #1e293b !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: .02em !important;
    padding-top: 10px !important;
    padding-bottom: 10px !important;
    border-bottom: 1px solid #cbd5e1 !important;
  }
  
  #tabelRekapRR tbody td, #tabelRekapRR tbody th {
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #334155 !important;
  }
  
  #tabelRekapRR tbody td a { font-size: 13px !important; font-weight: 600 !important; }

  #rowTotalRRAtas th, #rowTotalRRAtas a {
    font-size: 14px !important;
    font-weight: 700 !important;
    background: #f8fafc !important;
    color: #0f172a !important;
  }
  
  #tabelRekapRR [class*="text-[10px]"] { font-size: 11px !important; font-weight: 500 !important; color: #64748b !important; }
  #tabelRekapRR [class*="text-[10px]"] span { font-weight: 600 !important; }
  
  #tabelRekapRR tbody tr { height: 50px !important; }
  
  #tableExportRR thead th {
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: .02em !important;
    padding-top: 10px !important;
    padding-bottom: 10px !important;
  }
  
  #tableExportRR tbody td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #334155 !important;
  }
  
  #tableExportRR tbody td a { font-weight: 600 !important; }
  
  .otp-help-panel { font-size: 12px !important; line-height: 1.45 !important; }


  /* ========================================================
     RESPONSIVE LAYOUT V6
     Satu sumber aturan untuk mobile, tablet, laptop, desktop.
     ======================================================== */
  html, body { width:100%; min-width:0; }
  body.otp-modal-open { overflow:hidden !important; }

  #otpPage {
    height:calc(100dvh - 60px);
    min-height:0;
    padding:6px;
    gap:6px;
    background:#f8fafc;
  }
  .otp-card { margin:0 !important; border-radius:10px; }
  #otpMainPanel { min-height:0; border-radius:10px; }
  #otpMainScroll { overscroll-behavior:contain; -webkit-overflow-scrolling:touch; }

  #tabelRekapRR {
    width:100%;
    min-width:1220px;
    table-layout:fixed;
  }
  #tabelRekapRR th:first-child,
  #tabelRekapRR td:first-child { width:74px; min-width:74px; max-width:74px; }
  #tabelRekapRR th:last-child,
  #tabelRekapRR td:last-child { width:82px; min-width:82px; max-width:82px; }

  .otp-excel-head,
  .otp-excel-sub {
    border-right:1px solid #d1d5db !important;
    border-bottom:1px solid #d1d5db !important;
    padding:7px 5px !important;
    font-size:12px !important;
    line-height:1.1 !important;
    font-weight:800 !important;
    text-align:center !important;
    letter-spacing:0 !important;
    cursor:pointer;
  }
  .otp-excel-head { top:0 !important; }
  .otp-excel-sub { top:38px !important; }
  #tabelRekapRR .otp-excel-head.bg-orange-500,
  #tabelRekapRR .otp-excel-sub.bg-orange-500 { background:#f97316 !important; color:#fff !important; }
  #tabelRekapRR .otp-excel-head.bg-red-600,
  #tabelRekapRR .otp-excel-sub.bg-red-600 { background:#dc2626 !important; color:#fff !important; }
  #tabelRekapRR .otp-excel-head.bg-green-600,
  #tabelRekapRR .otp-excel-sub.bg-green-600 { background:#65a845 !important; color:#fff !important; }
  .otp-report-total,
  .otp-report-cell {
    border-right:1px solid #e5e7eb !important;
    border-bottom:1px solid #e5e7eb !important;
    padding:5px 6px !important;
    height:28px !important;
    font-size:12px !important;
    line-height:1.1 !important;
    font-weight:700 !important;
    color:#0f172a !important;
    white-space:nowrap;
    text-align:center;
    background:#fff;
  }
  .otp-report-total {
    background:#f8fafc !important;
    color:#0f172a !important;
    font-weight:800 !important;
  }
  .otp-report-tgl {
    width:58px !important;
    min-width:58px !important;
    max-width:58px !important;
  }
  .otp-mini-detail {
    display:inline-flex;
    align-items:flex-end;
    justify-content:flex-end;
    gap:4px;
    width:100%;
    border:0;
    background:transparent;
    font-weight:800;
    cursor:pointer;
  }
  .otp-mini-detail small {
    font-size:10px;
    line-height:1;
    font-weight:800;
    color:inherit;
  }

  .otp-filter-grid { min-width:0; }
  .otp-filter-grid .field { min-width:0; }
  .otp-filter-grid .inp { width:100%; box-sizing:border-box; }

  #dueSummaryRR > div { min-width:0; }
  .otp-due-card { min-height:94px; }

  #modalDetailRR .otp-modal-head { position:relative; }
  #detailCardsRR { background:#f8fafc; }
  .otp-detail-tabs {
    display:inline-flex;
    align-items:center;
    gap:4px;
    padding:3px;
    border:1px solid #dbeafe;
    border-radius:9px;
    background:#eff6ff;
  }
  .otp-detail-tab {
    display:inline-flex;
    align-items:center;
    gap:6px;
    height:30px;
    padding:0 10px;
    border-radius:7px;
    color:#64748b;
    font-size:11px;
    font-weight:800;
    transition:.15s ease;
  }
  .otp-detail-tab svg { width:13px; height:13px; }
  .otp-detail-tab.active {
    background:#fff;
    color:#2563eb;
    box-shadow:0 1px 2px rgba(15,23,42,.08);
  }
  .otp-summary-panel { padding:14px; background:#f8fafc; min-height:100%; }
  .otp-summary-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; }
  .otp-summary-card {
    min-width:0;
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#fff;
    padding:13px;
    box-shadow:0 1px 2px rgba(15,23,42,.04);
  }
  .otp-summary-head { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:12px; }
  .otp-summary-label { font-size:11px; line-height:1; font-weight:900; letter-spacing:.04em; text-transform:uppercase; }
  .otp-summary-count { font-size:24px; line-height:1; font-weight:900; color:#0f172a; }
  .otp-summary-meta { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:7px; margin-top:10px; }
  .otp-summary-mini { border-radius:9px; background:#f8fafc; padding:8px; min-width:0; }
  .otp-summary-mini span { display:block; font-size:8px; line-height:1; font-weight:900; letter-spacing:.04em; text-transform:uppercase; color:#94a3b8; }
  .otp-summary-mini b { display:block; margin-top:5px; font-size:12px; line-height:1.15; font-weight:900; color:#1e293b; overflow-wrap:anywhere; }
  .otp-summary-rule { margin-top:12px; border-top:1px solid #e2e8f0; padding-top:10px; font-size:10px; line-height:1.5; color:#64748b; font-weight:700; }
  .otp-status-pill {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:42px;
    height:22px;
    border-radius:999px;
    padding:0 8px;
    font-size:10px;
    font-weight:900;
  }
  .otp-status-nc { background:#f1f5f9; color:#475569; }
  .otp-status-ptp { background:#fef3c7; color:#b45309; }
  .otp-status-po { background:#dcfce7; color:#15803d; }
  .otp-detail-card {
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#fff;
    padding:10px;
    box-shadow:0 1px 2px rgba(15,23,42,.04);
  }
  .otp-detail-card + .otp-detail-card { margin-top:8px; }
  .otp-detail-title { font-size:12px; line-height:1.25; font-weight:800; color:#0f172a; }
  .otp-detail-sub { margin-top:2px; font-size:9px; line-height:1.25; color:#64748b; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; overflow-wrap:anywhere; }
  .otp-detail-badge { display:inline-flex; align-items:center; border-radius:999px; padding:3px 7px; font-size:9px; line-height:1; font-weight:800; white-space:nowrap; }
  .otp-detail-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; margin-top:9px; }
  .otp-detail-item { min-width:0; border:1px solid #e2e8f0; border-radius:8px; background:#f8fafc; padding:7px; }
  .otp-detail-label { font-size:8px; line-height:1.1; font-weight:800; letter-spacing:.04em; text-transform:uppercase; color:#64748b; }
  .otp-detail-value { margin-top:4px; font-size:11px; line-height:1.2; font-weight:750; color:#1e293b; overflow-wrap:anywhere; }
  .otp-detail-meta { margin-top:8px; display:flex; flex-wrap:wrap; gap:5px; font-size:9px; color:#475569; }
  .otp-detail-chip { border:1px solid #e2e8f0; border-radius:999px; background:#fff; padding:3px 6px; }

  @media (min-width:768px) {
    #otpPage { height:calc(100dvh - 72px); padding:8px; gap:8px; }
    .otp-card { border-radius:12px; }
    #otpMainPanel { border-radius:12px; }
    #tabelRekapRR { min-width:1220px; }
  }

  @media (min-width:1280px) {
    #otpPage { height:calc(100dvh - 80px); padding:6px 8px; gap:6px; }
    .otp-card { padding:9px 12px !important; gap:10px !important; }
    #tabelRekapRR { min-width:1220px; }
    #tabelRekapRR tbody tr { height:44px !important; }
    #tabelRekapRR thead th { padding-top:7px !important; padding-bottom:7px !important; }
    .rr-row-1 { height:38px; }
    .rr-row-2 { height:34px; }
    .rr-row-tot { height:42px; }
    #tabelRekapRR thead tr:nth-child(2) th { top:38px !important; }
    #tabelRekapRR thead tr.sticky-total th { top:72px !important; }
  }

  @media (min-width:641px) and (max-width:1279px) {
    .otp-filter-grid {
      display:grid !important;
      grid-template-columns:repeat(12,minmax(0,1fr));
      gap:7px !important;
      align-items:end;
    }
    .otp-filter-grid > * { width:auto !important; max-width:none !important; min-width:0 !important; margin:0 !important; }
    .otp-filter-grid > .w-px { display:none !important; }
    .otp-filter-closing { grid-column:span 2; }
    .otp-filter-harian { grid-column:span 2; }
    .otp-filter-cabang { grid-column:span 3; }
    .otp-filter-sub { grid-column:span 2; }
    .otp-filter-dpd { grid-column:span 2; }
    .otp-filter-ao { grid-column:span 3; }
    .otp-filter-kpp { grid-column:span 1; }
    .otp-filter-export { grid-column:span 1; }
    .otp-filter-export .lbl { display:block !important; visibility:hidden; }
    #filterWrapperMain { padding-top:9px; }
  }

  @media (max-width:900px) {
    .otp-summary-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
  }

  @media (max-width:640px) {
    .otp-detail-tabs { width:100%; justify-content:space-between; }
    .otp-detail-tab { flex:1; justify-content:center; height:32px; padding:0 8px; }
    .otp-summary-panel { padding:10px; }
    .otp-summary-grid { grid-template-columns:1fr; gap:8px; }
    .otp-summary-card { padding:11px; }
  }

  @media (max-width:767px) {
    #otpPage {
      height:calc(100dvh - 54px);
      padding:4px;
      gap:5px;
      overflow:hidden;
    }
    .otp-card { padding:7px !important; gap:6px !important; border-radius:9px; }
    .otp-title-wrap { padding:0 !important; }
    .otp-title { max-width:160px; }
    #filterWrapperMain { margin-top:5px !important; padding-top:7px !important; }

    .otp-filter-grid {
      display:grid !important;
      grid-template-columns:repeat(12,minmax(0,1fr));
      gap:6px !important;
      align-items:end;
    }
    .otp-filter-grid > * { width:auto !important; max-width:none !important; min-width:0 !important; margin:0 !important; }
    .otp-filter-grid > .w-px { display:none !important; }
    .otp-filter-closing, .otp-filter-harian { grid-column:span 6; }
    .otp-filter-cabang { grid-column:1 / -1; }
    .otp-filter-sub, .otp-filter-dpd { grid-column:span 6; }
    .otp-filter-ao { grid-column:span 8; }
    .otp-filter-kpp, .otp-filter-export { grid-column:span 2; }
    .otp-filter-export .lbl { display:block !important; visibility:hidden; }
    .inp { height:32px; padding:0 7px; border-radius:7px; font-size:11px; }
    .lbl { margin-bottom:2px; font-size:8px; }
    .otp-filter-kpp > div, .otp-filter-export button { height:32px !important; border-radius:7px !important; }

    #dueSummaryRR {
      padding:5px !important;
      overflow-x:auto;
      overflow-y:hidden;
      -webkit-overflow-scrolling:touch;
    }
    #dueSummaryRR > div {
      display:flex !important;
      gap:6px !important;
      width:max-content;
    }
    .otp-due-card { width:185px; min-height:72px; padding:7px 8px; border-radius:9px; }
    .otp-due-label { font-size:8px; }
    .otp-due-value { margin-top:3px; font-size:15px; }
    .otp-due-pct { padding:3px 6px; font-size:9px; }
    .otp-due-meta { margin-top:5px; font-size:8px; }

    #tabelRekapRR { min-width:1120px !important; }
    #tabelRekapRR th:first-child,
    #tabelRekapRR td:first-child { width:64px; min-width:64px; max-width:64px; }
    #tabelRekapRR th:last-child,
    #tabelRekapRR td:last-child { width:66px; min-width:66px; max-width:66px; }
    #tabelRekapRR thead th { font-size:8px !important; line-height:1.1; letter-spacing:0 !important; padding:5px 3px !important; white-space:normal; }
    #tabelRekapRR tbody td,
    #tabelRekapRR tbody th { font-size:9px !important; padding:5px 4px !important; }
    #tabelRekapRR tbody td a { font-size:9px !important; }
    #tabelRekapRR [class*="text-[10px]"] { font-size:7px !important; }
    #rowTotalRRAtas th, #rowTotalRRAtas a { font-size:9px !important; }
    #tabelRekapRR tbody tr { height:42px !important; }
    .rr-row-1 { height:30px; }
    .rr-row-2 { height:28px; }
    .rr-row-tot { height:38px; }
    #tabelRekapRR thead tr:nth-child(2) th { top:30px !important; }
    #tabelRekapRR thead tr.sticky-total th { top:58px !important; }

    .otp-info-root .otp-help-panel {
      position:fixed !important;
      left:8px !important;
      right:8px !important;
      top:72px !important;
      width:auto !important;
      max-width:none !important;
      max-height:calc(100dvh - 88px) !important;
      margin:0 !important;
      padding:12px !important;
      border-radius:12px !important;
    }

    #modalDetailRR { padding:0 !important; align-items:flex-end !important; }
    #modalDetailRR > .relative {
      width:100%;
      height:96dvh !important;
      max-height:96dvh;
      border-radius:14px 14px 0 0 !important;
      border-bottom:0;
    }
    .otp-modal-head > div:first-child { padding:8px 9px !important; gap:6px !important; }
    #modal-title-container h3 { font-size:12px !important; }
    #modalSubTitleRR { margin-left:8px !important; font-size:8px !important; }
    .otp-modal-head .btn-icon,
    .otp-modal-head button[onclick="closeModalRR()"] { width:31px !important; height:31px !important; min-width:31px; padding:0 !important; border-radius:8px !important; }
    .otp-modal-head > div:last-child { padding:0 9px 8px !important; }
    #search_nasabah_mobile { height:32px !important; border-radius:8px !important; }

    #tableExportRR { display:none !important; }
    #detailCardsRR { display:block !important; padding:7px; min-height:100%; }
    #modalDetailRR .flex-1.overflow-auto { background:#f8fafc !important; }
    #modalDetailRR > .relative > div:last-child { padding:7px 9px !important; }
    #pageInfoRR { max-width:48%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:9px !important; }
    #btnPrevRR, #btnNextRR { padding:6px 9px !important; font-size:9px !important; }
  }

  @media (max-width:374px) {
    .otp-title { max-width:125px; }
    #tabelRekapRR { min-width:704px !important; }
    .otp-due-card { width:168px; }
    .otp-detail-grid { gap:5px; }
    .otp-detail-item { padding:6px; }
    .otp-detail-value { font-size:10px; }
  }



  /* ========================================================
     CLEAN ELEGANT TABLE V7
     Header lembut, ukuran kolom konsisten, dan mobile compact.
     ======================================================== */
  :root {
    --otp-line:#e2e8f0;
    --otp-line-soft:#edf2f7;
    --otp-text:#334155;
    --otp-muted:#64748b;
    --otp-head:#f8fafc;
  }

  #otpPage { max-width:none !important; }
  .otp-card {
    border-color:var(--otp-line) !important;
    box-shadow:0 1px 2px rgba(15,23,42,.04), 0 8px 24px rgba(15,23,42,.025) !important;
  }
  #otpMainPanel {
    border-color:var(--otp-line) !important;
    box-shadow:0 1px 2px rgba(15,23,42,.035) !important;
  }
  #otpMainScroll { background:#fff; }

  #tabelRekapRR {
    border-collapse:separate;
    border-spacing:0;
    font-variant-numeric:tabular-nums;
  }
  #tabelRekapRR th,
  #tabelRekapRR td { box-sizing:border-box; }

  #tabelRekapRR .otp-excel-head,
  #tabelRekapRR .otp-excel-sub {
    border-right:1px solid var(--otp-line) !important;
    border-bottom:1px solid #cbd5e1 !important;
    background:var(--otp-head) !important;
    color:#334155 !important;
    text-transform:uppercase;
    white-space:normal;
    overflow:hidden;
  }
  #tabelRekapRR .otp-excel-head {
    font-size:10px !important;
    font-weight:800 !important;
    letter-spacing:.035em !important;
  }
  #tabelRekapRR .otp-excel-sub {
    font-size:9px !important;
    font-weight:750 !important;
    letter-spacing:.015em !important;
  }

  #tabelRekapRR .otp-group-date {
    background:#f1f5f9 !important;
    color:#334155 !important;
    border-top:3px solid #64748b !important;
  }
  #tabelRekapRR .otp-group-target {
    background:#fff7ed !important;
    color:#9a3412 !important;
    border-top:3px solid #f97316 !important;
  }
  #tabelRekapRR .otp-group-otp {
    background:#eff6ff !important;
    color:#1d4ed8 !important;
    border-top:3px solid #3b82f6 !important;
  }
  #tabelRekapRR .otp-group-collect {
    background:#fff1f2 !important;
    color:#be123c !important;
    border-top:3px solid #f43f5e !important;
  }
  #tabelRekapRR .otp-group-paid {
    background:#ecfdf5 !important;
    color:#047857 !important;
    border-top:3px solid #10b981 !important;
  }
  #tabelRekapRR .otp-group-installment {
    background:#ecfeff !important;
    color:#0e7490 !important;
    border-top:3px solid #06b6d4 !important;
  }
  #tabelRekapRR .otp-group-runoff {
    background:#eef2ff !important;
    color:#4338ca !important;
    border-top:3px solid #6366f1 !important;
  }
  #tabelRekapRR .otp-group-percent {
    background:#f5f3ff !important;
    color:#6d28d9 !important;
    border-top:3px solid #8b5cf6 !important;
  }

  #tabelRekapRR .otp-report-cell,
  #tabelRekapRR .otp-report-total {
    height:auto !important;
    min-height:40px;
    padding:7px 8px !important;
    border-right:1px solid var(--otp-line-soft) !important;
    border-bottom:1px solid var(--otp-line-soft) !important;
    color:var(--otp-text) !important;
    background:#fff;
    line-height:1.15 !important;
    overflow:hidden;
  }
  #tabelRekapRR .otp-report-total {
    background:#f1f5f9 !important;
    color:#0f172a !important;
    font-weight:800 !important;
    box-shadow:inset 0 -1px 0 #cbd5e1, 0 3px 7px -5px rgba(15,23,42,.5);
  }
  #tabelRekapRR .otp-report-row:nth-child(even) .otp-report-cell { background:#fbfdff; }
  #tabelRekapRR .otp-report-row:hover .otp-report-cell { background:#f8fafc !important; }
  #tabelRekapRR .otp-report-row:hover .otp-report-tgl { background:#f1f5f9 !important; }
  #tabelRekapRR .otp-report-tgl,
  #tabelRekapRR .otp-head-tgl {
    background:#f8fafc !important;
    box-shadow:3px 0 7px -6px rgba(15,23,42,.65) !important;
  }

  #tabelRekapRR a {
    color:#1d4ed8 !important;
    font-weight:650 !important;
    text-decoration:none !important;
  }
  #tabelRekapRR a:hover { color:#1e40af !important; text-decoration:underline !important; }

  .otp-mini-detail {
    display:flex !important;
    flex-direction:column;
    align-items:flex-end;
    justify-content:center;
    gap:3px;
    min-height:30px;
    line-height:1;
  }
  .otp-mini-detail span { display:block; width:100%; text-align:right; white-space:nowrap; }
  .otp-mini-detail small {
    display:block;
    width:100%;
    text-align:right;
    font-size:9px !important;
    font-weight:800 !important;
    opacity:.88;
    white-space:nowrap;
  }

  .otp-mobile-metric {
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    justify-content:center;
    gap:4px;
    min-height:34px;
    width:100%;
    text-decoration:none !important;
  }
  .otp-mobile-metric-main {
    display:block;
    width:100%;
    text-align:right;
    font-size:10px;
    line-height:1;
    font-weight:800;
    white-space:nowrap;
  }
  .otp-mobile-metric-noa {
    display:block;
    width:100%;
    text-align:right;
    color:var(--otp-muted);
    font-size:8px;
    line-height:1;
    font-weight:750;
    white-space:nowrap;
  }
  .otp-pct-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:54px;
    padding:5px 7px;
    border:1px solid #cbd5e1;
    border-radius:999px;
    background:#f8fafc;
    color:#475569;
    font-weight:850;
    white-space:nowrap;
    font-variant-numeric:tabular-nums;
    transition:background-color .15s ease, border-color .15s ease, color .15s ease;
  }
  .otp-pct-badge.otp-pct-green {
    background:#ecfdf5;
    border-color:#a7f3d0;
    color:#047857;
  }
  .otp-pct-badge.otp-pct-yellow {
    background:#fffbeb;
    border-color:#fde68a;
    color:#b45309;
  }
  .otp-pct-badge.otp-pct-red {
    background:#fff1f2;
    border-color:#fecdd3;
    color:#be123c;
  }
  .otp-pct-badge.otp-pct-neutral {
    background:#f8fafc;
    border-color:#e2e8f0;
    color:#94a3b8;
  }

  #tabelRekapRR .otp-total-main-row .otp-report-total {
    font-size:10.5px !important;
    padding:5px 6px !important;
    line-height:1.05 !important;
  }
  #tabelRekapRR .otp-total-main-row .otp-report-total a,
  #tabelRekapRR .otp-total-main-row .otp-report-total .otp-total-text {
    font-size:10.5px !important;
    line-height:1.05 !important;
    white-space:nowrap;
  }
  #tabelRekapRR .otp-total-main-row .otp-mini-detail {
    gap:2px;
    min-height:26px;
  }
  #tabelRekapRR .otp-total-main-row .otp-mini-detail span {
    font-size:10px !important;
    line-height:1 !important;
  }
  #tabelRekapRR .otp-total-main-row .otp-mini-detail small {
    font-size:8px !important;
    line-height:1 !important;
  }
  #tabelRekapRR .otp-total-main-row .otp-pct-badge {
    min-width:46px;
    padding:4px 6px;
    font-size:10px;
  }

  @media (min-width:1280px) {
    #otpPage { padding:4px 5px !important; gap:4px !important; }
    .otp-card { padding:7px 9px !important; }

    /* Desktop: tabel mengikuti lebar panel dan tidak memaksa horizontal scroll. */
    #otpMainScroll { overflow-x:hidden !important; }
    #tabelRekapRR {
      width:100% !important;
      min-width:0 !important;
      max-width:100% !important;
      table-layout:fixed !important;
    }

    #tabelRekapRR .otp-excel-head,
    #tabelRekapRR .otp-excel-sub {
      padding-left:3px !important;
      padding-right:3px !important;
      font-size:9px !important;
      line-height:1.05 !important;
    }

    #tabelRekapRR .otp-report-cell,
    #tabelRekapRR .otp-report-total {
      padding:6px 4px !important;
      font-size:10px !important;
      letter-spacing:-0.015em;
    }

    #tabelRekapRR .otp-report-cell a,
    #tabelRekapRR .otp-report-total a {
      font-size:10px !important;
      letter-spacing:-0.02em;
    }

    #tabelRekapRR .otp-total-main-row .otp-report-total,
    #tabelRekapRR .otp-total-main-row .otp-report-total a,
    #tabelRekapRR .otp-total-main-row .otp-report-total .otp-total-text {
      font-size:9px !important;
      letter-spacing:-0.035em;
    }

    #tabelRekapRR .otp-total-main-row .otp-mini-detail span { font-size:9px !important; }
    #tabelRekapRR .otp-total-main-row .otp-mini-detail small { font-size:7px !important; }
    #tabelRekapRR .otp-total-main-row .otp-pct-badge {
      min-width:42px;
      padding:3px 4px;
      font-size:8px;
    }

    #tabelRekapRR tbody tr { height:40px !important; }
    .rr-row-1 { height:34px !important; }
    .rr-row-2 { height:30px !important; }
    .rr-row-tot { height:38px !important; }
    #tabelRekapRR thead tr:nth-child(2) th { top:34px !important; }
    #tabelRekapRR thead tr.sticky-total th { top:64px !important; }
  }

  @media (min-width:768px) and (max-width:1279px) {
    #tabelRekapRR { min-width:1246px !important; }
    #tabelRekapRR .otp-report-cell,
    #tabelRekapRR .otp-report-total { font-size:10px !important; padding:6px !important; }
    .otp-card { padding:9px !important; }
  }

  @media (max-width:767px) {
    #otpPage { padding:4px !important; gap:4px !important; }
    .otp-card { padding:7px !important; }
    #otpMainPanel { border-radius:9px !important; }
    #tabelRekapRR { min-width:870px !important; }

    #tabelRekapRR .otp-excel-head {
      font-size:8px !important;
      line-height:1.08 !important;
      padding:5px 3px !important;
      letter-spacing:0 !important;
    }
    #tabelRekapRR .otp-excel-sub {
      font-size:7px !important;
      line-height:1.05 !important;
      padding:4px 2px !important;
      letter-spacing:0 !important;
    }
    #tabelRekapRR .otp-report-cell,
    #tabelRekapRR .otp-report-total {
      min-height:42px;
      padding:5px 5px !important;
      font-size:9px !important;
    }
    #tabelRekapRR .otp-total-main-row .otp-report-total { font-size:8px !important; padding:4px 4px !important; }
    #tabelRekapRR .otp-total-main-row .otp-mini-detail span { font-size:8px !important; }
    #tabelRekapRR .otp-total-main-row .otp-mini-detail small { font-size:6px !important; }
    #tabelRekapRR .otp-total-main-row .otp-pct-badge { min-width:40px; font-size:7px; padding:3px 4px; }
    #tabelRekapRR tbody tr { height:44px !important; }
    .rr-row-1 { height:34px !important; }
    .rr-row-2 { height:26px !important; }
    .rr-row-tot { height:42px !important; }
    #tabelRekapRR thead tr:nth-child(2) th { top:34px !important; }
    #tabelRekapRR thead tr.sticky-total th { top:60px !important; }
    #tabelRekapRR .otp-report-tgl,
    #tabelRekapRR .otp-head-tgl { width:48px !important; min-width:48px !important; max-width:48px !important; }
    .otp-mini-detail span { font-size:9px; }
    .otp-mini-detail small { font-size:7px !important; }
    .otp-pct-badge { min-width:48px; padding:4px 5px; font-size:8px; }
  }

  @media (max-width:374px) {
    #tabelRekapRR { min-width:806px !important; }
    #tabelRekapRR .otp-report-cell,
    #tabelRekapRR .otp-report-total { padding:4px !important; }
    .otp-mobile-metric-main { font-size:9px; }
    .otp-mobile-metric-noa { font-size:7px; }
  }


  /* ========================================================
     VIEW SWITCH + HELP POPOVER V8
     ======================================================== */
  .otp-view-switch {
    display:inline-flex !important;
    align-items:center;
    justify-content:center;
    gap:6px;
    border-color:#bfdbfe !important;
    background:#eff6ff !important;
    color:#1d4ed8 !important;
    font-weight:850 !important;
    cursor:pointer;
    box-shadow:0 1px 2px rgba(37,99,235,.06);
  }
  .otp-view-switch:hover { background:#dbeafe !important; border-color:#93c5fd !important; }
  .otp-view-switch.is-collection {
    border-color:#c7d2fe !important;
    background:#eef2ff !important;
    color:#4338ca !important;
  }
  .otp-view-switch-icon { display:inline-flex; width:14px; height:14px; flex:0 0 auto; }
  .otp-view-switch-icon svg { width:14px; height:14px; }

  .otp-info-root { position:relative; display:inline-flex; align-items:center; z-index:200000 !important; }
  .otp-info-button {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:20px;
    height:20px;
    border:1px solid transparent;
    border-radius:999px;
    color:#94a3b8;
    background:transparent;
    transition:.15s ease;
    cursor:pointer;
  }
  .otp-info-button svg { width:15px; height:15px; }
  .otp-info-button:hover,
  .otp-info-button.is-open {
    color:#2563eb;
    border-color:#bfdbfe;
    background:#eff6ff;
  }

  .otp-help-panel {
    position:fixed;
    z-index:200001 !important;
    display:none;
    flex-direction:column;
    width:min(420px, calc(100vw - 24px));
    max-height:min(620px, calc(100dvh - 24px));
    overflow:hidden;
    border:1px solid #dbe4f0;
    border-radius:16px;
    background:#fff;
    color:#475569;
    box-shadow:0 24px 60px -18px rgba(15,23,42,.34), 0 8px 20px -10px rgba(15,23,42,.18);
    white-space:normal;
    text-align:left;
    font-size:12px !important;
    line-height:1.45 !important;
  }
  .otp-help-panel.is-open { display:flex !important; animation:otpHelpIn .16s ease-out both; }
  @keyframes otpHelpIn {
    from { opacity:0; transform:translateY(-5px) scale(.985); }
    to { opacity:1; transform:translateY(0) scale(1); }
  }
  .otp-help-head {
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    padding:14px 14px 12px;
    border-bottom:1px solid #e2e8f0;
    background:linear-gradient(135deg,#f8fafc 0%,#eff6ff 100%);
    flex:0 0 auto;
  }
  .otp-help-title-wrap { display:flex; align-items:center; gap:10px; min-width:0; }
  .otp-help-title-icon {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:34px;
    height:34px;
    flex:0 0 34px;
    border-radius:10px;
    background:#2563eb;
    color:#fff;
    box-shadow:0 6px 14px -8px rgba(37,99,235,.8);
  }
  .otp-help-title-icon svg { width:17px; height:17px; }
  .otp-help-title { font-size:13px; line-height:1.2; font-weight:900; color:#0f172a; }
  .otp-help-subtitle { margin-top:3px; font-size:9px; line-height:1.3; font-weight:650; color:#64748b; }
  .otp-help-close {
    width:28px;
    height:28px;
    flex:0 0 28px;
    border:0;
    border-radius:8px;
    background:#e2e8f0;
    color:#64748b;
    font-size:19px;
    line-height:1;
    font-weight:700;
    cursor:pointer;
  }
  .otp-help-close:hover { background:#fee2e2; color:#dc2626; }
  .otp-help-body { padding:12px; overflow:auto; overscroll-behavior:contain; }
  .otp-help-intro {
    display:flex;
    align-items:flex-start;
    gap:9px;
    padding:10px;
    border:1px solid #dbeafe;
    border-radius:11px;
    background:#f8fbff;
    color:#334155;
  }
  .otp-help-section-icon { display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; flex:0 0 24px; border-radius:7px; }
  .otp-help-section-icon svg { width:14px; height:14px; }
  .otp-help-blue { background:#dbeafe; color:#2563eb; }
  .otp-help-card { margin-top:9px; padding:10px; border:1px solid #e2e8f0; border-radius:11px; background:#fff; }
  .otp-help-card-blue { border-color:#bfdbfe; background:#eff6ff; color:#1e3a8a; }
  .otp-help-card-title { margin-bottom:7px; font-size:10px; line-height:1; font-weight:900; letter-spacing:.045em; text-transform:uppercase; color:#334155; }
  .otp-help-card p { margin:0; font-size:10px; line-height:1.5; }
  .otp-help-status-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:6px; }
  .otp-help-status-grid > div { min-width:0; padding:7px; border-radius:8px; background:#f8fafc; }
  .otp-help-status-grid p { margin-top:6px; font-size:9px; line-height:1.35; color:#64748b; }
  .otp-help-status { display:inline-flex; align-items:center; justify-content:center; min-width:34px; height:20px; border-radius:999px; padding:0 7px; font-size:9px; font-weight:900; }
  .otp-help-status-nc { background:#e2e8f0; color:#475569; }
  .otp-help-status-ptp { background:#fef3c7; color:#b45309; }
  .otp-help-status-po { background:#dcfce7; color:#15803d; }
  .otp-help-definition-grid { display:grid; grid-template-columns:1fr; gap:6px; }
  .otp-help-definition-grid > div { display:grid; grid-template-columns:92px minmax(0,1fr); gap:8px; padding:7px 8px; border-radius:8px; background:#f8fafc; }
  .otp-help-definition-grid b { color:#334155; font-size:9px; }
  .otp-help-definition-grid span { font-size:9px; line-height:1.4; color:#64748b; }
  .otp-help-formula { margin-top:9px; display:grid; gap:6px; padding:10px; border:1px dashed #cbd5e1; border-radius:11px; background:#f8fafc; }
  .otp-help-formula > div { display:grid; grid-template-columns:105px minmax(0,1fr); gap:8px; }
  .otp-help-formula b { font-size:9px; color:#0f172a; }
  .otp-help-formula span { font-size:9px; line-height:1.4; color:#64748b; }
  .otp-help-legend { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:6px; }
  .otp-help-legend span { display:flex; align-items:center; gap:5px; min-width:0; padding:7px; border-radius:8px; font-size:8px; white-space:nowrap; }
  .otp-help-legend i { width:8px; height:8px; flex:0 0 8px; border-radius:999px; }
  .otp-help-legend-green { background:#ecfdf5; color:#047857; }
  .otp-help-legend-green i { background:#10b981; }
  .otp-help-legend-yellow { background:#fffbeb; color:#b45309; }
  .otp-help-legend-yellow i { background:#f59e0b; }
  .otp-help-legend-red { background:#fff1f2; color:#be123c; }
  .otp-help-legend-red i { background:#f43f5e; }

  #tabelRekapRR.otp-collection-view .collection-area-col {
    position:sticky !important;
    left:72px;
    z-index:22;
    background:#fff;
    box-shadow:3px 0 7px -6px rgba(15,23,42,.7);
  }
  #tabelRekapRR.otp-collection-view thead .collection-area-col { z-index:35; background:#f8fafc !important; }
  #tabelRekapRR.otp-collection-view .collection-total-row .collection-area-col { background:#eff6ff !important; }

  @media (min-width:641px) and (max-width:1279px) {
    .otp-filter-rekap { grid-column:span 2; }
  }

  @media (max-width:767px) {
    .otp-filter-rekap { grid-column:span 4; }
    .otp-filter-closing, .otp-filter-harian { grid-column:span 4 !important; }
    .otp-view-switch { padding-left:6px !important; padding-right:6px !important; font-size:10px !important; }

    .otp-help-panel {
      left:8px !important;
      right:8px !important;
      top:58px !important;
      bottom:8px;
      width:auto !important;
      max-height:none !important;
      border-radius:15px;
    }
    .otp-help-head { padding:11px; }
    .otp-help-body { padding:9px; }
    .otp-help-status-grid { grid-template-columns:1fr; }
    .otp-help-status-grid > div { display:grid; grid-template-columns:42px minmax(0,1fr); align-items:start; gap:6px; }
    .otp-help-status-grid p { margin:2px 0 0; }
    .otp-help-legend { grid-template-columns:1fr; }
    .otp-help-legend span { font-size:9px; }
    .otp-help-definition-grid > div,
    .otp-help-formula > div { grid-template-columns:88px minmax(0,1fr); }

    #tabelRekapRR.otp-collection-view { min-width:1060px !important; }
    #tabelRekapRR.otp-collection-view .collection-code-col { width:52px !important; min-width:52px !important; max-width:52px !important; }
    #tabelRekapRR.otp-collection-view .collection-area-col { left:52px; min-width:132px; max-width:132px; }
  }



  /* ========================================================
     COLLECTION COMPACT + LARGE HELP MODAL V9
     ======================================================== */
  .collection-group-title {
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:2px;
    min-width:0;
    line-height:1.05;
  }
  .collection-group-title > span { font-size:9px; font-weight:900; letter-spacing:.035em; white-space:nowrap; }
  .collection-group-title > small {
    display:block; max-width:100%; overflow:hidden; text-overflow:ellipsis;
    font-size:7px; font-weight:750; letter-spacing:0; text-transform:none;
    white-space:nowrap; opacity:.8;
  }

  #tabelRekapRR.otp-collection-view .collection-code-col {
    width:48px !important; min-width:48px !important; max-width:48px !important;
  }
  #tabelRekapRR.otp-collection-view .collection-area-col {
    left:48px !important; width:150px !important; min-width:150px !important; max-width:150px !important;
    overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
  }
  #tabelRekapRR.otp-collection-view tbody td {
    padding:7px 6px !important; font-size:9.5px !important; white-space:nowrap;
  }

  .otp-help-panel {
    width:min(780px, calc(100vw - 32px)); max-height:none !important;
    overflow:visible !important; border-radius:18px;
  }
  .otp-help-body {
    display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:9px;
    padding:12px; overflow:visible !important;
  }
  .otp-help-body > section { margin-top:0 !important; min-width:0; }
  .otp-help-body > .otp-help-intro,
  .otp-help-body > section:last-child { grid-column:1 / -1; }
  .otp-help-status-grid { grid-template-columns:repeat(3,minmax(0,1fr)); }
  .otp-help-definition-grid > div { grid-template-columns:88px minmax(0,1fr); }

  @media (min-width:1280px) {
    #otpMainScroll:has(#tabelRekapRR.otp-collection-view) { overflow-x:hidden !important; }
    #tabelRekapRR.otp-collection-view {
      width:100% !important; min-width:0 !important; max-width:100% !important; table-layout:fixed !important;
    }
    #tabelRekapRR.otp-collection-view .otp-excel-head,
    #tabelRekapRR.otp-collection-view .otp-excel-sub {
      padding-left:3px !important; padding-right:3px !important;
      font-size:8px !important; line-height:1.02 !important;
    }
    #tabelRekapRR.otp-collection-view tbody td {
      padding:7px 4px !important; font-size:9px !important; letter-spacing:-.015em;
    }
  }

  @media (min-width:768px) and (max-width:1279px) {
    #tabelRekapRR.otp-collection-view { min-width:980px !important; }
  }

  @media (max-width:767px) {
    #tabelRekapRR.otp-collection-view { min-width:900px !important; }
    #tabelRekapRR.otp-collection-view .collection-code-col {
      width:46px !important; min-width:46px !important; max-width:46px !important;
    }
    #tabelRekapRR.otp-collection-view .collection-area-col {
      left:46px !important; width:124px !important; min-width:124px !important; max-width:124px !important;
    }
    .collection-group-title > span { font-size:8px; }
    .collection-group-title > small { font-size:6px; }

    .otp-help-panel {
      left:6px !important; right:auto !important; top:6px; bottom:auto !important;
      width:calc(100vw - 12px) !important; max-height:none !important;
      overflow:visible !important; border-radius:13px;
    }
    .otp-help-head { padding:8px 9px; }
    .otp-help-title-icon { width:28px; height:28px; flex-basis:28px; border-radius:8px; }
    .otp-help-title { font-size:11px; }
    .otp-help-subtitle { font-size:7px; }
    .otp-help-close { width:25px; height:25px; flex-basis:25px; }
    .otp-help-body { grid-template-columns:repeat(2,minmax(0,1fr)); gap:5px; padding:6px; }
    .otp-help-intro, .otp-help-card, .otp-help-formula { padding:6px; border-radius:8px; }
    .otp-help-intro { gap:5px; font-size:8px; line-height:1.3; }
    .otp-help-section-icon { width:19px; height:19px; flex-basis:19px; }
    .otp-help-card-title { margin-bottom:4px; font-size:8px; }
    .otp-help-card p { font-size:7.5px; line-height:1.3; }
    .otp-help-status-grid { grid-template-columns:repeat(3,minmax(0,1fr)); gap:3px; }
    .otp-help-status-grid > div { display:block; padding:4px; }
    .otp-help-status-grid p { margin-top:3px; font-size:6.5px; line-height:1.22; }
    .otp-help-status { min-width:27px; height:16px; padding:0 5px; font-size:7px; }
    .otp-help-definition-grid { gap:3px; }
    .otp-help-definition-grid > div { grid-template-columns:62px minmax(0,1fr); gap:4px; padding:4px; }
    .otp-help-definition-grid b, .otp-help-definition-grid span,
    .otp-help-formula b, .otp-help-formula span { font-size:6.8px; line-height:1.25; }
    .otp-help-formula { gap:3px; }
    .otp-help-formula > div { grid-template-columns:70px minmax(0,1fr); gap:4px; }
    .otp-help-legend { grid-template-columns:repeat(3,minmax(0,1fr)); gap:3px; }
    .otp-help-legend span { padding:4px; font-size:6.5px; white-space:normal; line-height:1.2; }
  }



  /* ========================================================
     COLLECTION STICKY TOTAL + RESPONSIVE POLISH V10
     ======================================================== */
  #tabelRekapRR.otp-collection-view { --collection-head-h:64px; }

  #tabelRekapRR.otp-collection-view .collection-head-row th { z-index:62 !important; }
  #tabelRekapRR.otp-collection-view .collection-subhead-row th { z-index:61 !important; }

  #tabelRekapRR.otp-collection-view .collection-total-row td {
    position:sticky !important;
    top:var(--collection-head-h) !important;
    z-index:50 !important;
    background:#eff6ff !important;
    color:#1e3a8a !important;
    font-weight:850 !important;
    box-shadow:inset 0 -1px 0 #bfdbfe, 0 5px 10px -9px rgba(15,23,42,.8);
  }
  #tabelRekapRR.otp-collection-view .collection-total-row .collection-code-col {
    left:0 !important;
    z-index:58 !important;
    background:#eaf2ff !important;
  }
  #tabelRekapRR.otp-collection-view .collection-total-row .collection-area-col {
    z-index:57 !important;
    background:#eaf2ff !important;
  }

  #tabelRekapRR.otp-collection-view tbody td {
    font-size:10.5px !important;
    line-height:1.15 !important;
  }
  #tabelRekapRR.otp-collection-view .collection-group-title > span { font-size:10px !important; }
  #tabelRekapRR.otp-collection-view .collection-group-title > small { font-size:7.5px !important; }
  #tabelRekapRR.otp-collection-view .collection-subhead-row th { font-size:8.5px !important; }

  @media (min-width:1280px) {
    #tabelRekapRR.otp-collection-view {
      width:100% !important;
      min-width:0 !important;
      max-width:100% !important;
      table-layout:fixed !important;
    }
    #tabelRekapRR.otp-collection-view tbody td {
      padding:8px 5px !important;
      font-size:10px !important;
      letter-spacing:-.01em;
    }
    #tabelRekapRR.otp-collection-view .collection-code-col {
      width:48px !important;
      min-width:48px !important;
      max-width:48px !important;
    }
    #tabelRekapRR.otp-collection-view .collection-area-col {
      left:48px !important;
      width:152px !important;
      min-width:152px !important;
      max-width:152px !important;
    }
  }

  @media (min-width:768px) and (max-width:1279px) {
    #tabelRekapRR.otp-collection-view { min-width:940px !important; }
    #tabelRekapRR.otp-collection-view tbody td {
      padding:7px 5px !important;
      font-size:10px !important;
    }
    #tabelRekapRR.otp-collection-view .collection-code-col {
      width:48px !important;
      min-width:48px !important;
      max-width:48px !important;
    }
    #tabelRekapRR.otp-collection-view .collection-area-col {
      left:48px !important;
      width:142px !important;
      min-width:142px !important;
      max-width:142px !important;
    }
  }

  @media (max-width:767px) {
    #tabelRekapRR.otp-collection-view { min-width:820px !important; }
    #tabelRekapRR.otp-collection-view tbody td {
      padding:6px 4px !important;
      font-size:9.25px !important;
    }
    #tabelRekapRR.otp-collection-view .collection-code-col {
      width:44px !important;
      min-width:44px !important;
      max-width:44px !important;
    }
    #tabelRekapRR.otp-collection-view .collection-area-col {
      left:44px !important;
      width:116px !important;
      min-width:116px !important;
      max-width:116px !important;
    }
    #tabelRekapRR.otp-collection-view .collection-group-title > span { font-size:8.5px !important; }
    #tabelRekapRR.otp-collection-view .collection-group-title > small { font-size:6.25px !important; }
    #tabelRekapRR.otp-collection-view .collection-subhead-row th { font-size:7px !important; }
  }

  @media (max-width:374px) {
    #tabelRekapRR.otp-collection-view { min-width:760px !important; }
    #tabelRekapRR.otp-collection-view tbody td {
      font-size:8.75px !important;
      padding:5px 3px !important;
    }
    #tabelRekapRR.otp-collection-view .collection-area-col {
      width:108px !important;
      min-width:108px !important;
      max-width:108px !important;
    }
  }


  /* ========================================================
     UNIFIED MONBIS TEMPLATE V12
     Final override: desktop, tablet, mobile, and help modal.
     ======================================================== */
  :root {
    --otp-vh: 100dvh;
    --otp-page-offset: 74px;
    --otp-border: #dbe3ee;
    --otp-line: #e2e8f0;
    --otp-muted: #64748b;
    --otp-ink: #0f172a;
    --otp-blue: #2563eb;
  }

  #otpPage {
    width: 100% !important;
    max-width: none !important;
    height: calc(var(--otp-vh) - var(--otp-page-offset)) !important;
    min-height: 430px !important;
    padding: 8px !important;
    gap: 8px !important;
    overflow: hidden !important;
    background: #f8fafc !important;
  }

  .otp-card {
    min-height: 62px;
    padding: 10px 12px !important;
    border: 1px solid var(--otp-border) !important;
    border-radius: 14px !important;
    background: #fff !important;
    box-shadow: 0 1px 3px rgba(15,23,42,.05) !important;
  }

  .otp-title-wrap { min-width: 0; }
  .otp-title-wrap h1 { gap: 9px !important; color: var(--otp-ink) !important; font-size: 20px !important; line-height: 1.05 !important; font-weight: 950 !important; letter-spacing: -.025em; }
  .otp-title-wrap h1 > span:first-child {
    width: 38px; height: 38px; padding: 0 !important; border-radius: 10px !important;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(145deg,#2563eb,#1d4ed8) !important;
    box-shadow: 0 7px 18px rgba(37,99,235,.22);
  }
  .otp-title-wrap h1 > span:first-child svg { width: 19px !important; height: 19px !important; }
  .otp-page-subtitle { margin: 3px 0 0 47px; max-width: 410px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--otp-muted); font-size: 10px; line-height: 1.2; font-weight: 700; }

  .otp-info-button { width: 21px !important; height: 21px !important; border-color: #bfdbfe !important; background: #eff6ff !important; color: #2563eb !important; }
  .otp-info-button:hover { transform: translateY(-1px); background: #dbeafe !important; }

  #filterWrapperMain { min-width: 0; }
  .otp-filter-grid { display: grid !important; grid-template-columns: 42px 116px 116px minmax(150px,190px) minmax(115px,145px) 104px minmax(130px,165px) 46px 42px; gap: 7px !important; justify-content: end; align-items: end; }
  .otp-filter-grid > * { width: auto !important; max-width: none !important; min-width: 0 !important; margin: 0 !important; }
  .otp-filter-grid > .w-px { display: none !important; }
  .inp { height: 36px !important; border: 1px solid #cbd5e1 !important; border-radius: 9px !important; padding: 0 9px !important; color: #0f172a !important; font-size: 11px !important; font-weight: 800 !important; }
  .lbl { margin: 0 0 3px 2px !important; color: #475569 !important; font-size: 8px !important; line-height: 1 !important; font-weight: 950 !important; letter-spacing: .08em !important; }
  .otp-filter-kpp > div, .otp-filter-export button { height: 36px !important; border-radius: 9px !important; }
  .otp-view-switch { height: 36px !important; border-radius: 9px !important; }

  .otp-template-summary {
    flex: 0 0 auto; display: grid; grid-template-columns: repeat(5,minmax(0,1fr)); gap: 7px;
    overflow-x: auto; overflow-y: hidden; scrollbar-width: none;
  }
  .otp-template-summary::-webkit-scrollbar { display: none; }
  .otp-template-summary-card {
    position: relative; min-width: 0; min-height: 65px; padding: 9px 10px 8px 13px;
    border: 1px solid var(--otp-border); border-radius: 12px; background: #fff;
    text-align: left; overflow: hidden; cursor: pointer; transition: .16s ease;
    box-shadow: 0 1px 2px rgba(15,23,42,.035);
  }
  .otp-template-summary-card::before { content: ''; position: absolute; inset: 0 auto 0 0; width: 3px; background: #94a3b8; }
  .otp-template-summary-card:hover { transform: translateY(-1px); border-color: #bfdbfe; box-shadow: 0 6px 18px -12px rgba(15,23,42,.35); }
  .otp-template-summary-card span { display: block; color: #64748b; font-size: 8px; line-height: 1; font-weight: 950; letter-spacing: .06em; text-transform: uppercase; white-space: nowrap; }
  .otp-template-summary-card b { display: block; margin-top: 7px; overflow: hidden; text-overflow: ellipsis; color: #0f172a; font-size: 15px; line-height: 1; font-weight: 950; white-space: nowrap; font-variant-numeric: tabular-nums; }
  .otp-template-summary-card small { display: block; margin-top: 5px; overflow: hidden; text-overflow: ellipsis; color: #94a3b8; font-size: 8px; line-height: 1; font-weight: 750; white-space: nowrap; }
  .otp-template-summary-card.tone-blue::before { background:#2563eb; }
  .otp-template-summary-card.tone-cyan::before { background:#06b6d4; }
  .otp-template-summary-card.tone-red::before { background:#f43f5e; }
  .otp-template-summary-card.tone-green::before { background:#10b981; }
  .otp-template-summary-card.tone-purple::before { background:#8b5cf6; }
  .otp-template-summary-card.is-critical { background:#fff7f7; border-color:#fecdd3; }
  .otp-template-summary-card.is-warning { background:#fffdf5; border-color:#fde68a; }
  .otp-template-summary-card.is-good { background:#f5fffb; border-color:#a7f3d0; }

  #dueSummaryRR { flex: 0 0 auto; padding: 0 !important; }
  #dueSummaryRR > div { gap: 7px !important; }
  .otp-due-card { min-height: 72px !important; padding: 8px 10px !important; border-radius: 12px !important; }
  .otp-due-value { font-size: 16px !important; }

  #otpMainPanel { border: 1px solid var(--otp-border) !important; border-radius: 14px !important; box-shadow: 0 1px 3px rgba(15,23,42,.04) !important; }
  #otpMainScroll { background: #fff; overscroll-behavior: contain; }
  #tabelRekapRR .otp-excel-head, #tabelRekapRR .otp-excel-sub { font-weight: 900 !important; }
  #tabelRekapRR .otp-report-cell, #tabelRekapRR .otp-report-total { font-variant-numeric: tabular-nums; }

  .otp-mobile-main { flex: 1 1 auto; min-height: 0; overflow: auto; padding: 7px; background: #f8fafc; overscroll-behavior: contain; -webkit-overflow-scrolling: touch; }
  .otp-mobile-total, .otp-mobile-day-card, .otp-mobile-collection-card { border: 1px solid #dbe3ee; border-radius: 12px; background: #fff; box-shadow: 0 1px 2px rgba(15,23,42,.035); }
  .otp-mobile-total { padding: 10px; margin-bottom: 7px; background: linear-gradient(145deg,#eff6ff,#fff); border-color: #bfdbfe; }
  .otp-mobile-day-card, .otp-mobile-collection-card { padding: 9px; margin-bottom: 7px; }
  .otp-mobile-card-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding-bottom: 8px; border-bottom: 1px solid #eef2f7; }
  .otp-mobile-card-title { min-width: 0; color:#0f172a; font-size: 12px; line-height:1.1; font-weight: 950; }
  .otp-mobile-card-sub { margin-top:3px; color:#94a3b8; font-size:8px; font-weight:750; }
  .otp-mobile-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; margin-top:8px; }
  .otp-mobile-item { min-width:0; padding:7px; border:1px solid #e2e8f0; border-radius:9px; background:#f8fafc; text-align:left; }
  button.otp-mobile-item { cursor:pointer; transition:.15s ease; }
  button.otp-mobile-item:active { transform:scale(.985); }
  .otp-mobile-item span { display:block; color:#64748b; font-size:7px; line-height:1; font-weight:900; letter-spacing:.045em; text-transform:uppercase; }
  .otp-mobile-item b { display:block; margin-top:5px; overflow:hidden; text-overflow:ellipsis; color:#1e293b; font-size:10px; line-height:1.1; font-weight:900; white-space:nowrap; font-variant-numeric:tabular-nums; }
  .otp-mobile-item small { display:block; margin-top:3px; color:#94a3b8; font-size:7px; font-weight:750; }
  .otp-mobile-item.item-red { background:#fff7f7; border-color:#fecdd3; }
  .otp-mobile-item.item-red b { color:#be123c; }
  .otp-mobile-item.item-green { background:#f3fff9; border-color:#a7f3d0; }
  .otp-mobile-item.item-green b { color:#047857; }
  .otp-mobile-item.item-amber { background:#fffdf5; border-color:#fde68a; }
  .otp-mobile-item.item-amber b { color:#b45309; }
  .otp-mobile-card-empty { display:flex; min-height:180px; align-items:center; justify-content:center; padding:20px; color:#94a3b8; font-size:11px; font-weight:800; text-align:center; }

  .otp-help-backdrop { position:fixed; inset:0; z-index:200000; display:none; background:rgba(15,23,42,.5); backdrop-filter:blur(4px); }
  .otp-help-backdrop.is-open { display:block; }
  body.otp-help-open { overflow:hidden !important; }
  .otp-help-panel {
    position:fixed !important; z-index:200001 !important; left:50% !important; top:50% !important; right:auto !important; bottom:auto !important;
    width:min(920px,calc(100vw - 32px)) !important; max-width:none !important; max-height:calc(100dvh - 40px) !important;
    transform:translate(-50%,-50%) !important; overflow:hidden !important; border:1px solid #dbe3ee !important; border-radius:18px !important;
    background:#fff !important; box-shadow:0 30px 80px -25px rgba(15,23,42,.5) !important;
  }
  .otp-help-panel.is-open { display:flex !important; }
  .otp-help-head { padding:14px 16px !important; background:linear-gradient(135deg,#f8fafc,#eff6ff) !important; }
  .otp-help-title { font-size:16px !important; }
  .otp-help-subtitle { font-size:9px !important; }
  .otp-help-body { display:grid !important; grid-template-columns:repeat(2,minmax(0,1fr)) !important; gap:9px !important; padding:12px !important; overflow:auto !important; overscroll-behavior:contain; }
  .otp-help-body > section { margin:0 !important; min-width:0; }
  .otp-help-body > .otp-help-intro, .otp-help-body > .otp-help-current, .otp-help-body > .otp-help-priority, .otp-help-body > section:last-child { grid-column:1 / -1; }
  .otp-help-intro, .otp-help-card, .otp-help-formula { border-radius:12px !important; }
  .otp-help-card-title { font-size:9px !important; }
  .otp-help-card p { font-size:9px !important; }
  .otp-help-kpi-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:7px; }
  .otp-help-kpi-grid > div { min-width:0; padding:9px; border:1px solid #e2e8f0; border-radius:10px; background:#f8fafc; }
  .otp-help-kpi-grid span { display:block; color:#64748b; font-size:7px; font-weight:900; letter-spacing:.05em; text-transform:uppercase; }
  .otp-help-kpi-grid b { display:block; margin-top:6px; overflow:hidden; text-overflow:ellipsis; color:#0f172a; font-size:13px; font-weight:950; white-space:nowrap; }
  .otp-help-condition { margin-top:9px !important; padding:9px 10px; border-radius:9px; background:#eff6ff; color:#1e3a8a; font-weight:800; }
  .otp-help-action-list { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; }
  .otp-help-action-list > div { position:relative; padding:8px 8px 8px 27px; border:1px solid #e2e8f0; border-radius:9px; background:#fff; color:#475569; font-size:9px; line-height:1.4; font-weight:750; }
  .otp-help-action-list > div::before { content:'✓'; position:absolute; left:8px; top:8px; width:13px; height:13px; border-radius:999px; display:flex; align-items:center; justify-content:center; background:#dbeafe; color:#2563eb; font-size:8px; font-weight:950; }

  #modalDetailRR > .relative, #modalAreaRekapRR > .relative { border:1px solid var(--otp-border); border-radius:16px !important; }
  .otp-modal-head { background:#fbfdff !important; }

  @media (min-width:1280px) {
    #otpPage { --otp-page-offset: 80px; }
    .otp-card { display:flex !important; flex-direction:row !important; align-items:center !important; gap:14px !important; }
    .otp-title-wrap { flex:0 0 auto; width:auto !important; }
    #filterWrapperMain { flex:1 1 auto; width:auto !important; border:0 !important; margin:0 !important; padding:0 !important; }
    #otpMainScroll { overflow-x:hidden !important; }
    #tabelRekapRR { width:100% !important; min-width:0 !important; table-layout:fixed !important; }
  }

  @media (min-width:768px) and (max-width:1279px) {
    #otpPage { --otp-page-offset: 72px; }
    .otp-card { align-items:stretch !important; }
    .otp-filter-grid { grid-template-columns:repeat(12,minmax(0,1fr)) !important; }
    .otp-filter-rekap { grid-column:span 1; }
    .otp-filter-closing,.otp-filter-harian { grid-column:span 2; }
    .otp-filter-cabang { grid-column:span 3; }
    .otp-filter-sub,.otp-filter-dpd { grid-column:span 2; }
    .otp-filter-ao { grid-column:span 3; }
    .otp-filter-kpp,.otp-filter-export { grid-column:span 1; }
    .otp-template-summary { grid-template-columns:repeat(5,minmax(150px,1fr)); }
    #tabelRekapRR { min-width:1180px !important; }
  }

  @media (max-width:767px) {
    :root { --otp-page-offset: 54px; }
    #otpPage { padding:5px !important; gap:5px !important; min-height:360px !important; }
    .otp-card { min-height:0; padding:8px !important; border-radius:12px !important; gap:6px !important; }
    .otp-title-wrap { padding:0 !important; }
    .otp-title-wrap h1 { font-size:13px !important; gap:7px !important; }
    .otp-title-wrap h1 > span:first-child { width:30px; height:30px; border-radius:8px !important; }
    .otp-title-wrap h1 > span:first-child svg { width:15px !important; height:15px !important; }
    .otp-title { max-width:150px !important; }
    .otp-page-subtitle { margin:2px 0 0 37px; max-width:180px; font-size:7px; }
    .otp-filter-toggle-main { height:30px !important; padding:0 8px !important; border-radius:8px !important; }

    #filterWrapperMain { margin-top:5px !important; padding-top:7px !important; }
    .otp-filter-grid { grid-template-columns:repeat(12,minmax(0,1fr)) !important; gap:5px !important; }
    .otp-filter-rekap { grid-column:span 2; }
    .otp-filter-closing,.otp-filter-harian { grid-column:span 5 !important; }
    .otp-filter-cabang { grid-column:1 / -1; }
    .otp-filter-sub,.otp-filter-dpd { grid-column:span 6; }
    .otp-filter-ao { grid-column:span 8; }
    .otp-filter-kpp,.otp-filter-export { grid-column:span 2; }
    .inp { height:31px !important; padding:0 7px !important; border-radius:8px !important; font-size:9px !important; }
    .lbl { font-size:7px !important; margin-bottom:2px !important; }
    .otp-filter-kpp > div,.otp-filter-export button,.otp-view-switch { height:31px !important; border-radius:8px !important; }

    .otp-template-summary { display:flex; gap:5px; padding:0; }
    .otp-template-summary-card { flex:0 0 138px; min-height:57px; padding:8px 8px 7px 11px; border-radius:10px; }
    .otp-template-summary-card span { font-size:7px; }
    .otp-template-summary-card b { margin-top:6px; font-size:12px; }
    .otp-template-summary-card small { margin-top:4px; font-size:7px; }

    #dueSummaryRR { overflow-x:auto; overflow-y:hidden; }
    #dueSummaryRR > div { display:flex !important; width:max-content; }
    .otp-due-card { width:178px !important; min-height:66px !important; }

    #otpMainPanel { border-radius:12px !important; }
    #otpMainScroll { display:none !important; }
    #otpMobileMain { display:block !important; }

    .otp-help-panel { left:0 !important; right:0 !important; top:auto !important; bottom:0 !important; width:100% !important; max-height:92dvh !important; transform:none !important; border-radius:18px 18px 0 0 !important; border-bottom:0 !important; }
    .otp-help-head { padding:10px 11px !important; }
    .otp-help-title-icon { width:30px !important; height:30px !important; flex-basis:30px !important; border-radius:8px !important; }
    .otp-help-title { font-size:12px !important; }
    .otp-help-subtitle { font-size:7px !important; }
    .otp-help-body { grid-template-columns:1fr !important; gap:6px !important; padding:7px !important; }
    .otp-help-body > * { grid-column:1 !important; }
    .otp-help-kpi-grid { grid-template-columns:repeat(2,minmax(0,1fr)); gap:5px; }
    .otp-help-kpi-grid > div { padding:7px; }
    .otp-help-kpi-grid b { font-size:11px; }
    .otp-help-action-list { grid-template-columns:1fr; gap:5px; }
    .otp-help-status-grid { grid-template-columns:repeat(3,minmax(0,1fr)) !important; }
    .otp-help-definition-grid > div,.otp-help-formula > div { grid-template-columns:82px minmax(0,1fr) !important; }

    #modalDetailRR,#modalAreaRekapRR { padding:0 !important; align-items:flex-end !important; }
    #modalDetailRR > .relative,#modalAreaRekapRR > .relative { width:100% !important; height:94dvh !important; max-height:94dvh !important; border-radius:16px 16px 0 0 !important; border-bottom:0 !important; }
    .otp-modal-head > div:first-child { padding:8px 9px !important; }
    .otp-modal-head .btn-icon span { display:none !important; }
  }

  @media (max-width:374px) {
    .otp-title { max-width:118px !important; }
    .otp-page-subtitle { max-width:145px; }
    .otp-template-summary-card { flex-basis:128px; }
    .otp-mobile-grid { gap:5px; }
    .otp-mobile-item { padding:6px; }
  }



  /* ========================================================
     V12 — TABLE-FIRST LAYOUT + HELP MODAL STACKING FIX
     ======================================================== */
  #otpSummaryStrip,
  #dueSummaryRR {
    display: none !important;
  }

  #otpMainPanel {
    flex: 1 1 auto !important;
    min-height: 0 !important;
  }

  body > #otpHelpBackdrop {
    position: fixed !important;
    inset: 0 !important;
    z-index: 2147483000 !important;
    background: rgba(15, 23, 42, .48) !important;
    -webkit-backdrop-filter: blur(2px) !important;
    backdrop-filter: blur(2px) !important;
  }

  body > #otpHelpPanel {
    position: fixed !important;
    z-index: 2147483001 !important;
    isolation: isolate;
    filter: none !important;
    -webkit-backdrop-filter: none !important;
    backdrop-filter: none !important;
    opacity: 1 !important;
  }

  body > #otpHelpPanel,
  body > #otpHelpPanel * {
    -webkit-font-smoothing: antialiased;
  }

  @media (min-width: 768px) {
    body > #otpHelpPanel {
      width: min(900px, calc(100vw - 32px)) !important;
      max-height: calc(100dvh - 32px) !important;
    }
  }

  @media (max-width: 767px) {
    body > #otpHelpPanel {
      left: 0 !important;
      right: 0 !important;
      top: auto !important;
      bottom: 0 !important;
      width: 100% !important;
      max-height: 92dvh !important;
      transform: none !important;
      border-radius: 18px 18px 0 0 !important;
    }
  }


  /* ========================================================
     V13 — MOBILE TABLE + COMPACT HEADER ACTIONS
     ======================================================== */
  .otp-page-subtitle { display:none !important; }

  .otp-mobile-head-actions {
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:6px;
  }

  #btnMainViewRRMobile { display:none !important; }

  @media (max-width:767px) {
    .otp-title-wrap > div:first-child { min-width:0; }
    .otp-title-wrap h1 { margin:0 !important; }
    .otp-title { max-width:145px !important; }

    .otp-mobile-head-actions {
      display:flex !important;
      margin-left:auto !important;
      gap:5px !important;
    }

    #btnMainViewRRMobile {
      display:inline-flex !important;
      width:31px !important;
      min-width:31px !important;
      height:30px !important;
      padding:0 !important;
      border-radius:8px !important;
    }

    .otp-filter-toggle-main {
      width:31px !important;
      min-width:31px !important;
      height:30px !important;
      padding:0 !important;
      justify-content:center !important;
      border-radius:8px !important;
    }

    /* Tombol pergantian laporan dipindah ke kiri tombol Filter. */
    .otp-filter-rekap { display:none !important; }

    /* Tabel utama tetap dipakai pada mobile; kartu mobile dinonaktifkan. */
    #otpMobileMain { display:none !important; }
    #otpMainScroll {
      display:block !important;
      flex:1 1 auto !important;
      min-height:0 !important;
      overflow:auto !important;
      -webkit-overflow-scrolling:touch;
      overscroll-behavior:contain;
    }

    #tabelRekapRR {
      min-width:870px !important;
      width:870px !important;
      max-width:none !important;
      table-layout:fixed !important;
    }

    #tabelRekapRR.otp-collection-view {
      min-width:820px !important;
      width:820px !important;
      max-width:none !important;
    }

    /* Kolom tanggal/kode tetap terlihat saat tabel digeser. */
    #tabelRekapRR .otp-report-tgl,
    #tabelRekapRR .otp-head-tgl,
    #tabelRekapRR .collection-code-col {
      position:sticky !important;
      left:0 !important;
      z-index:55 !important;
    }
  }

  /* ========================================================
     V14 — STICKY HEADER PRESISI + NOA ANGSURAN
     Tinggi baris header dihitung melalui JavaScript sehingga
     TGL, subheader, dan TOTAL tidak saling menimpa di mobile.
     ======================================================== */
  #tabelRekapRR {
    --otp-head-row-1-h: 34px;
    --otp-head-row-2-h: 26px;
    --otp-head-total-top: 60px;
  }

  /* Hindari nested sticky pada THEAD. Semua sticky ditangani TH. */
  #tabelRekapRR > thead {
    position: static !important;
    top: auto !important;
    z-index: auto !important;
  }

  #tabelRekapRR > thead > tr:nth-child(1) > th {
    top: 0 !important;
  }

  #tabelRekapRR > thead > tr:nth-child(2) > th {
    top: var(--otp-head-row-1-h) !important;
  }

  #tabelRekapRR > thead > tr.sticky-total > th {
    top: var(--otp-head-total-top) !important;
    z-index: 64 !important;
  }

  #tabelRekapRR > thead > tr.sticky-total > th.otp-report-tgl {
    left: 0 !important;
    z-index: 72 !important;
    background: #eef5ff !important;
  }

  #tabelRekapRR > thead > tr:nth-child(1) > th.otp-head-tgl {
    left: 0 !important;
    z-index: 75 !important;
  }

  .otp-due-metric {
    display: flex !important;
    width: 100%;
    min-height: 34px;
    flex-direction: column;
    align-items: stretch;
    justify-content: center;
    gap: 4px;
    border: 0;
    background: transparent;
    cursor: pointer;
    font-variant-numeric: tabular-nums;
  }

  .otp-due-metric-main {
    display: block;
    width: 100%;
    overflow: hidden;
    text-align: right;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 10px;
    line-height: 1;
    font-weight: 850;
  }

  .otp-due-metric-meta {
    display: flex;
    width: 100%;
    align-items: center;
    justify-content: space-between;
    gap: 5px;
    font-size: 7.5px;
    line-height: 1;
    font-weight: 850;
    white-space: nowrap;
  }

  .otp-due-metric-noa {
    color: #64748b;
    text-align: left;
  }

  .otp-due-metric-pct {
    color: inherit;
    text-align: right;
  }

  @media (min-width: 768px) {
    .otp-due-metric-main { font-size: 10.5px; }
    .otp-due-metric-meta { font-size: 8px; }
  }

  @media (max-width: 767px) {
    #tabelRekapRR > thead > tr.sticky-total > th {
      box-shadow: inset 0 -1px 0 #bfdbfe, 0 4px 9px -8px rgba(15,23,42,.75) !important;
    }

    .otp-due-metric { min-height: 36px; gap: 5px; }
    .otp-due-metric-main { font-size: 9px; }
    .otp-due-metric-meta { font-size: 7px; }
  }



  /* ========================================================
     V15 - MOBILE REKAP COLLECTION COMPACT
     Kode cabang disembunyikan pada HP. Nama kantor menjadi
     kolom sticky pertama dengan lebar lebih hemat.
     ======================================================== */
  @media (max-width:767px) {
    #tabelRekapRR.otp-collection-view {
      width:760px !important;
      min-width:760px !important;
      max-width:none !important;
    }

    #tabelRekapRR.otp-collection-view col.collection-code-track,
    #tabelRekapRR.otp-collection-view .collection-code-col {
      display:none !important;
      width:0 !important;
      min-width:0 !important;
      max-width:0 !important;
      padding:0 !important;
      border:0 !important;
    }

    #tabelRekapRR.otp-collection-view .collection-area-col {
      position:sticky !important;
      left:0 !important;
      width:96px !important;
      min-width:96px !important;
      max-width:96px !important;
      padding-left:6px !important;
      padding-right:5px !important;
      overflow:hidden !important;
      text-overflow:ellipsis !important;
      white-space:nowrap !important;
      z-index:56 !important;
      box-shadow:3px 0 7px -6px rgba(15,23,42,.72) !important;
    }

    #tabelRekapRR.otp-collection-view thead .collection-area-col {
      z-index:66 !important;
      background:#f8fafc !important;
    }

    #tabelRekapRR.otp-collection-view .collection-total-row .collection-area-col {
      z-index:59 !important;
      background:#eaf2ff !important;
    }

    #tabelRekapRR.otp-collection-view tbody .collection-area-col {
      font-size:8.5px !important;
      font-weight:700 !important;
    }
  }

  @media (max-width:374px) {
    #tabelRekapRR.otp-collection-view {
      width:720px !important;
      min-width:720px !important;
    }

    #tabelRekapRR.otp-collection-view .collection-area-col {
      width:88px !important;
      min-width:88px !important;
      max-width:88px !important;
      padding-left:5px !important;
      padding-right:4px !important;
      font-size:8px !important;
    }
  }



  /* ========================================================
     V16 - ACCESS GATE REKAP CCL
     Gate sementara sisi frontend. Akses berlaku selama tab aktif.
     ======================================================== */
  body.otp-ccl-access-open { overflow:hidden !important; }

  .otp-ccl-access-modal {
    position:fixed;
    inset:0;
    z-index:300000;
    display:none;
    align-items:center;
    justify-content:center;
    padding:16px;
  }
  .otp-ccl-access-modal.is-open { display:flex; }
  .otp-ccl-access-backdrop {
    position:absolute;
    inset:0;
    border:0;
    background:rgba(15,23,42,.56);
    backdrop-filter:blur(5px);
    -webkit-backdrop-filter:blur(5px);
  }
  .otp-ccl-access-card {
    position:relative;
    z-index:1;
    width:min(420px, calc(100vw - 32px));
    overflow:hidden;
    border:1px solid rgba(226,232,240,.95);
    border-radius:18px;
    background:#fff;
    box-shadow:0 28px 70px -22px rgba(15,23,42,.62);
    animation:otpCclAccessIn .18s ease-out both;
  }
  @keyframes otpCclAccessIn {
    from { opacity:0; transform:translateY(8px) scale(.985); }
    to { opacity:1; transform:translateY(0) scale(1); }
  }
  @keyframes otpCclAccessShake {
    0%,100% { transform:translateX(0); }
    25% { transform:translateX(-7px); }
    50% { transform:translateX(7px); }
    75% { transform:translateX(-4px); }
  }
  .otp-ccl-access-card.is-error { animation:otpCclAccessShake .28s ease; }
  .otp-ccl-access-head {
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    padding:16px;
    border-bottom:1px solid #e2e8f0;
    background:linear-gradient(135deg,#f8fafc 0%,#eff6ff 100%);
  }
  .otp-ccl-access-title-wrap { display:flex; align-items:center; gap:11px; min-width:0; }
  .otp-ccl-access-icon {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:38px;
    height:38px;
    flex:0 0 38px;
    border-radius:11px;
    background:#2563eb;
    color:#fff;
    box-shadow:0 9px 18px -12px rgba(37,99,235,.95);
  }
  .otp-ccl-access-icon svg { width:19px; height:19px; }
  .otp-ccl-access-title { font-size:15px; line-height:1.2; font-weight:900; color:#0f172a; }
  .otp-ccl-access-subtitle { margin-top:4px; font-size:10px; line-height:1.4; font-weight:650; color:#64748b; }
  .otp-ccl-access-close {
    width:30px;
    height:30px;
    flex:0 0 30px;
    border:0;
    border-radius:9px;
    background:#e2e8f0;
    color:#64748b;
    font-size:20px;
    line-height:1;
    cursor:pointer;
  }
  .otp-ccl-access-close:hover { background:#fee2e2; color:#dc2626; }
  .otp-ccl-access-body { padding:16px; }
  .otp-ccl-access-label {
    display:block;
    margin-bottom:6px;
    color:#475569;
    font-size:10px;
    line-height:1;
    font-weight:850;
    letter-spacing:.05em;
    text-transform:uppercase;
  }
  .otp-ccl-access-input-wrap { position:relative; }
  .otp-ccl-access-input {
    width:100%;
    height:42px;
    padding:0 44px 0 12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    background:#fff;
    color:#0f172a;
    outline:none;
    font-size:14px;
    font-weight:750;
    letter-spacing:.02em;
  }
  .otp-ccl-access-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.11); }
  .otp-ccl-access-toggle {
    position:absolute;
    top:50%;
    right:6px;
    transform:translateY(-50%);
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:32px;
    height:32px;
    border:0;
    border-radius:8px;
    background:#f1f5f9;
    color:#64748b;
    cursor:pointer;
  }
  .otp-ccl-access-toggle:hover { background:#e2e8f0; color:#334155; }
  .otp-ccl-access-toggle svg { width:16px; height:16px; }
  .otp-ccl-access-error {
    min-height:18px;
    margin-top:7px;
    color:#dc2626;
    font-size:10px;
    line-height:1.35;
    font-weight:750;
  }
  .otp-ccl-access-note {
    margin-top:11px;
    padding:9px 10px;
    border:1px solid #dbeafe;
    border-radius:9px;
    background:#f8fbff;
    color:#475569;
    font-size:10px;
    line-height:1.45;
  }
  .otp-ccl-access-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:14px; }
  .otp-ccl-access-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:90px;
    height:36px;
    padding:0 13px;
    border-radius:9px;
    font-size:11px;
    font-weight:850;
    cursor:pointer;
  }
  .otp-ccl-access-cancel { border:1px solid #cbd5e1; background:#fff; color:#475569; }
  .otp-ccl-access-cancel:hover { background:#f8fafc; }
  .otp-ccl-access-submit { border:1px solid #2563eb; background:#2563eb; color:#fff; box-shadow:0 5px 12px -8px rgba(37,99,235,.9); }
  .otp-ccl-access-submit:hover { background:#1d4ed8; }

  @media (max-width:767px) {
    .otp-ccl-access-modal { align-items:flex-end; padding:0; }
    .otp-ccl-access-card {
      width:100%;
      max-width:none;
      border-radius:18px 18px 0 0;
      border-bottom:0;
      padding-bottom:max(0px, env(safe-area-inset-bottom));
    }
    .otp-ccl-access-head { padding:13px 14px; }
    .otp-ccl-access-body { padding:14px; }
    .otp-ccl-access-actions { display:grid; grid-template-columns:1fr 1fr; }
    .otp-ccl-access-btn { width:100%; }
  }

</style>

<div id="otpPage" class="otp-shell max-w-[1920px] w-full mx-auto flex flex-col overflow-hidden">
<div class="otp-card relative z-[60] flex-none w-full bg-white p-3 rounded-lg border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3 shrink-0">
    <div class="otp-title-wrap flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
        <div class="flex flex-col gap-0.5 md:gap-1 min-w-0 flex-1">
          <h1 class="text-base md:text-lg font-bold flex items-center gap-2 text-slate-800 whitespace-nowrap">
            <span class="p-1.5 bg-blue-600 text-white rounded shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v10"></path></svg>
            </span>
            <span id="otpTitle" class="otp-title truncate">OTP - ALL</span>
            <div class="otp-info-root ml-1">
              <button id="otpHelpButton" type="button" aria-label="Buka informasi OTP" aria-expanded="false" onclick="toggleOtpHelp(event)" class="otp-info-button">
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
              </button>

              <div id="otpHelpPanel" class="otp-help-panel custom-scrollbar" role="dialog" aria-modal="false" aria-labelledby="otpHelpTitle" aria-hidden="true">
                <div class="otp-help-head">
                  <div class="otp-help-title-wrap">
                    <span class="otp-help-title-icon">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V9m8 10V5m8 14v-7M3 19h18"></path></svg>
                    </span>
                    <div>
                      <div id="otpHelpTitle" class="otp-help-title">Panduan OTP & Collection</div>
                      <div class="otp-help-subtitle">Ringkasan definisi, tindak lanjut, dan indikator.</div>
                    </div>
                  </div>
                  <button type="button" class="otp-help-close" onclick="closeOtpHelp(event)" aria-label="Tutup informasi">&times;</button>
                </div>

                <div class="otp-help-body">
                  <section class="otp-help-intro">
                    <span class="otp-help-section-icon otp-help-blue">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M4.93 19h14.14a2 2 0 001.73-3L13.73 4a2 2 0 00-3.46 0L3.2 16A2 2 0 004.93 19z"></path></svg>
                    </span>
                    <div><b>OTP</b> memonitor ketepatan pembayaran debitur sesuai tanggal jatuh tempo sampai posisi harian atau aktual.</div>
                  </section>

                  <section class="otp-help-card otp-help-current">
                    <div class="otp-help-card-title">Kondisi Data Saat Ini</div>
                    <div class="otp-help-kpi-grid">
                      <div><span>Capaian OTP</span><b id="otpInsightPct">-</b></div>
                      <div><span>Target M-1</span><b id="otpInsightTarget">-</b></div>
                      <div><span>Total Run Off</span><b id="otpInsightRunoff">-</b></div>
                      <div><span>Sisa Dikejar</span><b id="otpInsightGap">-</b></div>
                    </div>
                    <p id="otpInsightCondition" class="otp-help-condition">Data belum dimuat.</p>
                  </section>

                  <section class="otp-help-card otp-help-priority">
                    <div class="otp-help-card-title">Prioritas Kepala Cabang / Direksi</div>
                    <div id="otpInsightAction" class="otp-help-action-list">
                      <div>Memuat rekomendasi berdasarkan posisi OTP...</div>
                    </div>
                  </section>

                  <section class="otp-help-card otp-help-card-blue">
                    <div class="otp-help-card-title">Reminder pencatatan CCL</div>
                    <p>CS/AO wajib mencatat hasil monitoring, janji bayar, kendala, dan tindak lanjut. Input CCL bulan berjalan menjadi dasar klasifikasi <b>NC</b>, <b>PTP</b>, atau <b>PO</b>.</p>
                  </section>

                  <section class="otp-help-card">
                    <div class="otp-help-card-title">Status Collection</div>
                    <div class="otp-help-status-grid">
                      <div><span class="otp-help-status otp-help-status-nc">NC</span><p>Belum ada komitmen dan belum ada pembayaran.</p></div>
                      <div><span class="otp-help-status otp-help-status-ptp">PTP</span><p>Sudah ada janji, tetapi pembayaran belum memenuhi 80%.</p></div>
                      <div><span class="otp-help-status otp-help-status-po">PO</span><p>Sudah membayar tanpa janji atau minimal 80% dari nominal janji.</p></div>
                    </div>
                  </section>

                  <section class="otp-help-card">
                    <div class="otp-help-card-title">Komponen utama OTP</div>
                    <div class="otp-help-definition-grid">
                      <div><b>Target M-1</b><span>Outstanding atau rekening jatuh tempo dari closing bulan sebelumnya.</span></div>
                      <div><b>OTP Lancar</b><span>Rekening yang masih lancar pada posisi aktual.</span></div>
                      <div><b>Ditagih</b><span>Rekening prioritas yang belum memenuhi pembayaran.</span></div>
                    </div>
                  </section>

                  <section class="otp-help-formula">
                    <div><b>% OTP per tanggal</b><span>(Lancar + Lunas + Angsuran) / Target tanggal terkait.</span></div>
                    <div><b>% OTP total</b><span>Total pencapaian kumulatif / total target kumulatif sampai hari aktual.</span></div>
                  </section>

                  <section class="otp-help-card">
                    <div class="otp-help-card-title">Indikator pencapaian</div>
                    <div class="otp-help-legend">
                      <span class="otp-help-legend-green"><i></i><b>Baik</b> ≥ 85%</span>
                      <span class="otp-help-legend-yellow"><i></i><b>Perhatian</b> 60–84,99%</span>
                      <span class="otp-help-legend-red"><i></i><b>Kritis</b> &lt; 60%</span>
                    </div>
                  </section>
                </div>
              </div>
            </div>
          </h1>
        </div>

        <div class="otp-mobile-head-actions ml-auto shrink-0">
            <button id="btnMainViewRRMobile" type="button" onclick="toggleMainOtpViewRR()" aria-pressed="false" class="otp-view-switch otp-mobile-view-switch" title="Buka Rekap CCL" aria-label="Buka Rekap CCL">
                <span id="mainViewIconRRMobile" class="otp-view-switch-icon" aria-hidden="true"></span>
                <span class="sr-only">Ganti tampilan OTP dan Collection</span>
            </button>
            <button type="button" onclick="toggleMainFilter()" class="otp-filter-toggle-main xl:hidden h-[32px] px-3 bg-white border border-slate-200 text-slate-700 rounded flex items-center gap-1.5 shadow-sm transition font-bold text-xs whitespace-nowrap shrink-0 hover:bg-slate-50">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                <span class="hidden sm:inline">Filter</span>
            </button>
        </div>
    </div>

    <div id="filterWrapperMain" class="filter-transition w-full xl:w-auto xl:flex-1 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
        <form id="formFilterRR" class="w-full flex-1 min-w-0" onsubmit="event.preventDefault(); fetchRekapRR();">
            <div class="otp-filter-grid flex flex-wrap xl:flex-nowrap items-end gap-2 w-full justify-end">
                <div class="field otp-filter-rekap shrink-0 w-[42px]">
                    <!-- <label class="lbl">TAMPILAN</label> -->
                    <button id="btnMainViewRR" type="button" onclick="toggleMainOtpViewRR()" aria-pressed="false" class="otp-view-switch inp w-full">
                        <span id="mainViewIconRR" class="otp-view-switch-icon" aria-hidden="true"></span>
                        <span id="mainViewLabelRR" class="sr-only">Rekap</span>
                    </button>
                </div>
                
                <div class="field otp-filter-closing shrink-0 w-[calc(50%-4px)] sm:w-[115px]">
                    <label class="lbl">CLOSING (M-1)</label>
                    <input type="date" id="closing_date" onchange="fetchRekapRR()" class="inp w-full" required onclick="try{this.showPicker()}catch(e){}">
                </div>
                <div class="field otp-filter-harian shrink-0 w-[calc(50%-4px)] sm:w-[115px]">
                    <label class="lbl">ACTUAL (HARIAN)</label>
                    <input type="date" id="harian_date" onchange="fetchRekapRR()" class="inp w-full" required onclick="try{this.showPicker()}catch(e){}">
                </div>
                
                <div class="w-px h-6 bg-slate-200 shrink-0 mx-0.5 hidden xl:block mb-1.5"></div>
                
                <div class="field otp-filter-cabang shrink-0 w-[calc(50%-4px)] sm:flex-1 sm:min-w-[140px] xl:max-w-[180px]">
                    <label class="lbl">CABANG</label>
                    <select id="opt_kantor" class="inp w-full truncate" onchange="handleCabangChangeOtp()">
                        <option value="">Loading...</option>
                    </select>
                </div>
                <div class="field otp-filter-sub shrink-0 w-[calc(50%-4px)] sm:flex-1 sm:min-w-[120px] xl:max-w-[140px]">
                    <label id="lbl_sub_otp" class="lbl">KORWIL</label>
                    <select id="opt_sub_otp" class="inp w-full truncate" onchange="fetchRekapRR()">
                        <option value="">ALL KORWIL</option>
                    </select>
                </div>
                
                <div class="field otp-filter-dpd flex-1 min-w-[70px] sm:w-[100px] sm:flex-none">
                    <label class="lbl">DPD BUCKET</label>
                    <select id="opt_dpd_bucket" class="inp w-full" onchange="fetchRekapRR()">
                        <option value="all">ALL</option>
                        <option value="dpd0">DPD 0</option>
                        <option value="dpd1-30">DPD 1-30</option>
                    </select>
                </div>
                <div class="field otp-filter-ao flex-1 min-w-[80px] sm:w-[140px] xl:max-w-[160px] sm:flex-none">
                    <label class="lbl">AO KREDIT</label>
                    <select id="opt_ao_otp" class="inp w-full truncate disabled:bg-slate-50 disabled:text-slate-400" onchange="fetchRekapRR()" disabled>
                        <option value="">PILIH CABANG DULU</option>
                    </select>
                </div>
                <div class="field otp-filter-kpp shrink-0 w-[44px] sm:w-[48px]">
                    <label class="lbl text-center w-full">KPP</label>
                    <div class="flex items-center justify-center h-[34px] px-2 bg-slate-50 border border-slate-200 rounded cursor-pointer hover:bg-slate-100 transition" onclick="document.getElementById('chk_127').click()">
                        <input type="checkbox" id="chk_127" class="w-3.5 h-3.5 text-blue-600 bg-white border-slate-300 rounded cursor-pointer" onclick="event.stopPropagation()" onchange="fetchRekapRR()">
                    </div>
                </div>
                <div class="field otp-filter-export shrink-0 w-[40px]">
                    <label class="lbl opacity-0 hidden sm:block select-none">&nbsp;</label>
                    <button type="button" onclick="exportExcelRekapRR()" class="btn-icon h-[34px] w-full bg-emerald-600 hover:bg-emerald-700 text-white rounded shadow-sm shrink-0 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline></svg>
                    </button>
                </div>
                
            </div>
        </form>
    </div>
</div>


<div id="otpMainPanel" class="flex-1 min-h-0 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm relative flex flex-col">
    <div id="loadingRR" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-xs backdrop-blur-[1px]">
        <div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mb-3"></div>
        <span>Menyiapkan Matriks...</span>
    </div>
    <div id="otpMobileMain" class="otp-mobile-main hidden custom-scrollbar"></div>
    <div id="otpMainScroll" class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table class="min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelRekapRR">
        <colgroup id="otpColgroup"></colgroup>
        <thead class="uppercase select-none" id="headRR"></thead>
        <tbody id="bodyRR" class="divide-y divide-slate-100 bg-white group-tbody text-xs"></tbody>
      </table>
    </div>
</div>

</div>

<div id="otpHelpBackdrop" class="otp-help-backdrop" onclick="closeOtpHelp(event)" aria-hidden="true"></div>



<!-- Gate sementara akses Rekap CCL -->
<div id="otpCclAccessModal" class="otp-ccl-access-modal" role="dialog" aria-modal="true" aria-labelledby="otpCclAccessTitle" aria-hidden="true">
  <button type="button" class="otp-ccl-access-backdrop" onclick="closeCclAccessModalRR()" aria-label="Tutup akses Rekap CCL"></button>
  <form id="otpCclAccessForm" class="otp-ccl-access-card" onsubmit="submitCclAccessRR(event)" autocomplete="off">
    <div class="otp-ccl-access-head">
      <div class="otp-ccl-access-title-wrap">
        <span class="otp-ccl-access-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="10" rx="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
        </span>
        <div class="min-w-0">
          <div id="otpCclAccessTitle" class="otp-ccl-access-title">Akses Rekap CCL</div>
          <div class="otp-ccl-access-subtitle">Masukkan kode akses untuk membuka laporan collection.</div>
        </div>
      </div>
      <button type="button" class="otp-ccl-access-close" onclick="closeCclAccessModalRR()" aria-label="Tutup">&times;</button>
    </div>

    <div class="otp-ccl-access-body">
      <label for="otpCclAccessInput" class="otp-ccl-access-label">Kode Akses</label>
      <div class="otp-ccl-access-input-wrap">
        <input id="otpCclAccessInput" class="otp-ccl-access-input" type="password" inputmode="text" autocapitalize="none" spellcheck="false" placeholder="Masukkan kode akses" aria-describedby="otpCclAccessError">
        <button id="otpCclAccessToggle" type="button" class="otp-ccl-access-toggle" onclick="toggleCclAccessVisibilityRR()" aria-label="Tampilkan kode">
          <svg id="otpCclAccessEye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
        </button>
      </div>
      <div id="otpCclAccessError" class="otp-ccl-access-error" aria-live="polite"></div>
      <div class="otp-ccl-access-note">Akses berlaku selama tab ini masih dibuka. Tutup tab atau browser untuk mengunci kembali Rekap CCL.</div>

      <div class="otp-ccl-access-actions">
        <button type="button" class="otp-ccl-access-btn otp-ccl-access-cancel" onclick="closeCclAccessModalRR()">Batal</button>
        <button type="submit" class="otp-ccl-access-btn otp-ccl-access-submit">Buka Rekap CCL</button>
      </div>
    </div>
  </form>
</div>


<div id="modalAreaRekapRR" class="fixed inset-0 hidden z-[9998] flex items-end md:items-center justify-center p-0 sm:p-4">
  <div class="absolute inset-0 bg-slate-900/35 backdrop-blur-sm" onclick="closeAreaRekapRR()"></div>
  <div class="relative bg-white w-full md:max-w-[1180px] h-[88vh] md:h-[78vh] rounded-t-xl md:rounded-lg shadow-2xl flex flex-col overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-3 shrink-0">
      <div class="min-w-0">
        <h3 class="font-black text-slate-800 text-base md:text-lg leading-tight flex items-center gap-2">
          <span class="p-1.5 rounded bg-blue-600 text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 19V9m8 10V5m8 14v-7M3 19h18"></path></svg>
          </span>
          <span id="areaRekapTitleRR" class="truncate">Rekap Area CCL</span>
        </h3>
        <p id="areaRekapSubRR" class="mt-1 text-[10px] font-bold uppercase tracking-wide text-slate-500 truncate">...</p>
      </div>
      <button type="button" onclick="closeAreaRekapRR()" class="w-9 h-9 rounded bg-slate-200 hover:bg-rose-500 hover:text-white text-slate-600 font-black text-xl leading-none">&times;</button>
    </div>
    <div id="areaRekapLoadingRR" class="hidden absolute inset-0 bg-white/85 z-10 flex items-center justify-center text-blue-600 font-black text-xs uppercase tracking-widest">
      <span class="animate-spin h-6 w-6 border-4 border-slate-200 border-t-blue-600 rounded-full mr-3"></span> Memuat Rekap...
    </div>
    <div class="flex-1 overflow-auto custom-scrollbar bg-white">
      <table class="min-w-[980px] w-full text-xs text-slate-700 border-separate border-spacing-0">
        <thead class="sticky top-0 z-20 uppercase">
          <tr class="bg-sky-50 text-blue-900">
            <th rowspan="2" class="px-3 py-2 border-b border-r border-slate-200 text-left w-[70px]">Kode</th>
            <th rowspan="2" class="px-3 py-2 border-b border-r border-slate-200 text-left min-w-[190px]">Area</th>
            <th colspan="2" class="px-3 py-2 border-b border-r border-slate-200 text-center">Target M-1</th>
            <th colspan="2" class="px-3 py-2 border-b border-r border-slate-200 text-center">OTP Lancar</th>
            <th colspan="2" class="px-3 py-2 border-b border-r border-slate-200 text-center">Ditagih</th>
            <th colspan="2" class="px-3 py-2 border-b border-r border-slate-200 text-center">Lunas</th>
            <th rowspan="2" class="px-3 py-2 border-b border-r border-slate-200 text-right min-w-[120px]">Angsuran</th>
            <th rowspan="2" class="px-3 py-2 border-b border-slate-200 text-right w-[76px]">%</th>
          </tr>
          <tr class="bg-slate-50 text-blue-900">
            <th class="px-3 py-2 border-b border-r border-slate-200 text-right min-w-[120px]">Nom</th>
            <th class="px-3 py-2 border-b border-r border-slate-200 text-center w-[58px]">NOA</th>
            <th class="px-3 py-2 border-b border-r border-slate-200 text-right min-w-[120px]">Nom</th>
            <th class="px-3 py-2 border-b border-r border-slate-200 text-center w-[58px]">NOA</th>
            <th class="px-3 py-2 border-b border-r border-slate-200 text-right min-w-[120px]">Nom</th>
            <th class="px-3 py-2 border-b border-r border-slate-200 text-center w-[58px]">NOA</th>
            <th class="px-3 py-2 border-b border-r border-slate-200 text-right min-w-[120px]">Nom</th>
            <th class="px-3 py-2 border-b border-r border-slate-200 text-center w-[58px]">NOA</th>
          </tr>
        </thead>
        <tbody id="areaRekapBodyRR" class="divide-y divide-slate-100"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="modalDetailRR" class="fixed inset-0 hidden z-[9999] flex items-end md:items-center justify-center p-0 sm:p-4">
  <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModalRR()"></div>
  <div class="relative bg-white w-full h-[95vh] md:h-[92vh] max-w-[1700px] rounded-t-xl md:rounded-lg shadow-2xl flex flex-col overflow-hidden animate-scale-up">
    
    <div class="otp-modal-head flex flex-col bg-slate-50 border-b border-slate-200 shrink-0 w-full z-[80]">
        <div class="flex items-center justify-between px-4 py-3 gap-3 w-full">
            <div class="flex-1 min-w-0" id="modal-title-container">
              <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm md:text-lg leading-tight truncate">
                  <span class="w-1.5 h-5 bg-blue-600 rounded shrink-0"></span>
                  <span id="modalTitleRR" class="truncate">Detail Matriks OTP</span>
              </h3>
              <p class="text-[10px] text-slate-500 mt-1 ml-3 font-semibold tracking-wide uppercase truncate" id="modalSubTitleRR">...</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <select id="collection_filter_rr" onchange="changeCollectionFilterRR()" class="hidden sm:block h-[34px] w-[118px] rounded border border-slate-200 bg-white px-2 text-[10px] font-extrabold uppercase tracking-wide text-slate-700 outline-none focus:border-blue-500">
                    <option value="ALL">Semua</option>
                    <option value="CALL">Call</option>
                    <option value="NC">NC</option>
                    <option value="PTP">PTP</option>
                    <option value="PO">PO</option>
                </select>
                <div class="relative hidden sm:block w-[220px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="search_nasabah" oninput="filterTableDetail()" class="w-full h-[34px] pl-9 pr-3 rounded border border-slate-200 bg-white text-xs font-semibold outline-none focus:border-blue-500" placeholder="Cari nama / rekening...">
                </div>
                
                <button onclick="downloadExcelFull(event)" class="btn-icon bg-emerald-600 hover:bg-emerald-700 text-white px-3 h-[34px] rounded shadow-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="ml-1.5 text-[10px] font-bold uppercase tracking-wide">Export</span>
                </button>
                
                <button onclick="closeModalRR()" class="w-[34px] h-[34px] flex items-center justify-center rounded bg-slate-200 hover:bg-rose-500 hover:text-white text-slate-500 transition font-black text-xl leading-none shrink-0">&times;</button>
            </div>
        </div>
        
        <div class="sm:hidden px-4 pb-3">
            <div class="grid grid-cols-[92px,1fr] gap-2">
                <select id="collection_filter_rr_mobile" onchange="changeCollectionFilterRR()" class="h-[36px] rounded border border-slate-200 bg-white px-2 text-[10px] font-extrabold uppercase tracking-wide text-slate-700 outline-none focus:border-blue-500">
                    <option value="ALL">Semua</option>
                    <option value="CALL">Call</option>
                    <option value="NC">NC</option>
                    <option value="PTP">PTP</option>
                    <option value="PO">PO</option>
                </select>
                <input type="text" id="search_nasabah_mobile" oninput="filterTableDetail()" class="w-full h-[36px] pl-3 pr-3 rounded border border-slate-200 bg-white text-[11px] outline-none focus:border-blue-500" placeholder="Cari nama / rekening...">
            </div>
        </div>

        <div id="detailTabsRR" class="hidden px-4 pb-3">
            <div class="otp-detail-tabs">
                <button type="button" id="tabSummaryRR" class="otp-detail-tab active" onclick="setDetailTabRR('summary')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M4 19V9m8 10V5m8 14v-7M3 19h18"></path></svg>
                    <span>Rekap Summary</span>
                </button>
                <button type="button" id="tabMatrixRR" class="otp-detail-tab" onclick="setDetailTabRR('matrix')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M4 5h16M4 12h16M4 19h16M8 5v14m8-14v14"></path></svg>
                    <span>Matriks Migrasi</span>
                </button>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-auto bg-white relative p-0 custom-scrollbar">
      <div id="loadingModalRR" class="hidden absolute inset-0 bg-white/90 z-[60] flex flex-col items-center justify-center text-blue-600 backdrop-blur-[1px]">
         <div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mb-2"></div>
         <span class="text-xs font-bold uppercase tracking-widest">Memuat Detail...</span>
      </div>
      
      <div id="detailSummaryRR" class="hidden otp-summary-panel"></div>
      <div id="detailCardsRR" class="hidden"></div>

      <table class="w-max min-w-full text-center md:text-left text-slate-700 bg-white table-fixed" id="tableExportRR">
        <thead id="headModalRR" class="uppercase select-none"></thead>
        <tbody id="bodyModalRR" class="divide-y divide-slate-100 bg-white modal-tbody"></tbody>
      </table>
    </div>

    <div class="px-3 py-2 md:px-5 md:py-3 border-t bg-slate-50 flex justify-between items-center shrink-0">
      <span class="text-xs font-semibold text-slate-500 bg-white border px-2.5 py-1 rounded" id="pageInfoRR">0 Data</span>
      <div class="flex gap-2">
          <button id="btnPrevRR" onclick="changePageDetail(-1)" class="px-3 py-1.5 bg-white border border-slate-300 rounded text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50 transition shadow-sm">« Prev</button>
          <button id="btnNextRR" onclick="changePageDetail(1)" class="px-3 py-1.5 bg-white border border-slate-300 rounded text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50 transition shadow-sm">Next »</button>
      </div>
    </div>
  </div>
</div>

<script>
  function exportExcelRekapRR() {
      const rows = Array.isArray(rekapDataRaw) ? rekapDataRaw : [];
      const gt = rekapGtRaw;
      if (!rows.length && !gt) { alert('Tidak ada data untuk diexport.'); return; }
      let csv = 'TGL\tTARGET M-1 BAKI DEBET\tTARGET M-1 NOA\tOTP BAKI DEBET\tOTP NOA\tDITAGIH BAKI DEBET\tDITAGIH NOA\tLUNAS BAKI DEBET\tLUNAS NOA\tLEWAT JT (%)\tOTP ANGSURAN (%)\tTOTAL ANGSURAN\tTOTAL RUN OFF\tPERSEN\n';
      if (gt) {
          csv += `TOTAL\t${Math.round(gt.target_os||0)}\t${gt.target_noa||0}\t${Math.round(gt.lancar_os||0)}\t${gt.lancar_noa||0}\t${Math.round(gt.macet_os||0)}\t${gt.macet_noa||0}\t${Math.round(gt.lunas_os||0)}\t${gt.lunas_noa||0}\t${Math.round(gt.angsuran_lewat||0)} (${gt.angsuran_lewat_persen||0}%)\t${Math.round(gt.angsuran_sesuai||0)} (${gt.angsuran_sesuai_persen||0}%)\t${Math.round(gt.angsuran||0)}\t${Math.round(gt.total_bayar||0)}\t${gt.persen||0}%\n`;
      }
      rows.forEach(r => {
          const p = (r.persen !== null && r.persen !== undefined) ? `${r.persen}%` : '-';
          csv += `${r.tgl}\t${Math.round(r.target_os||0)}\t${r.target_noa||0}\t${Math.round(r.lancar_os||0)}\t${r.lancar_noa||0}\t${Math.round(r.macet_os||0)}\t${r.macet_noa||0}\t${Math.round(r.lunas_os||0)}\t${r.lunas_noa||0}\t${Math.round(r.angsuran_lewat||0)} (${r.angsuran_lewat_persen||0}%)\t${Math.round(r.angsuran_sesuai||0)} (${r.angsuran_sesuai_persen||0}%)\t${Math.round(r.angsuran||0)}\t${Math.round(r.total_bayar||0)}\t${p}\n`;
      });
      const blob = new Blob([csv], { type: 'application/vnd.ms-excel;charset=utf-8' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `OTP_Rekap_${document.getElementById('harian_date')?.value || 'harian'}.xls`;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
  }

  const API_RR_URL = './api/rr'; 
  const API_KODE_URL = './api/kode/'; 
  const nfID = new Intl.NumberFormat('id-ID');
  const fmt  = n => nfID.format(Math.round(Number(n||0)));
  const fmtPct = n => `${Number(n || 0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}%`;

  // Indikator kolom % paling kanan:
  // >= 85% hijau, >= 60% kuning, dan < 60% merah.
  const getOtpPctTone = value => {
      const pct = Number(value);
      if (!Number.isFinite(pct)) return 'otp-pct-neutral';
      if (pct >= 85) return 'otp-pct-green';
      if (pct >= 60) return 'otp-pct-yellow';
      return 'otp-pct-red';
  };

  const renderOtpPctBadge = value => {
      if (value === null || value === undefined || value === '') {
          return '<span class="otp-pct-badge otp-pct-neutral">-</span>';
      }
      const pct = Number(value);
      if (!Number.isFinite(pct)) {
          return '<span class="otp-pct-badge otp-pct-neutral">-</span>';
      }
      const label = pct >= 85 ? 'Baik' : (pct >= 60 ? 'Perlu perhatian' : 'Kritis');
      return `<span class="otp-pct-badge ${getOtpPctTone(pct)}" title="${label}: ${fmtPct(pct)}" aria-label="${label} ${fmtPct(pct)}">${fmtPct(pct)}</span>`;
  };

  const fmtDateID = s => {
      if(!s) return '-';
      const d = new Date(s);
      if (isNaN(d)) return String(s).slice(0, 10);
      return `${String(d.getDate()).padStart(2,'0')}-${String(d.getMonth()+1).padStart(2,'0')}-${d.getFullYear()}`;
  };
  
  let rekapDataRaw = [];
  let rekapGtRaw = null;
  let dueSummaryRaw = null;
  let detailDataCache = [];
  let detailCollectionSummary = null;
  let detailActiveTab = 'matrix';
  let mainOtpView = 'otp';
  let mainCollectionStatus = 'ALL';
  let mainCollectionPage = 1;
  let mainCollectionTotalPages = 1;
  let mainCollectionRows = [];
  let mainCollectionSummary = null;

  function syncOtpViewportRR() {
      const viewportHeight = window.visualViewport?.height || window.innerHeight;
      document.documentElement.style.setProperty('--otp-vh', `${Math.round(viewportHeight)}px`);
  }

  function setOtpMainSurfaceRR(useMobile) {
      const scroll = document.getElementById('otpMainScroll');
      const mobile = document.getElementById('otpMobileMain');
      if (scroll) scroll.classList.toggle('hidden', !!useMobile);
      if (mobile) mobile.classList.toggle('hidden', !useMobile);
  }

  function setSummaryCardRR(index, label, value, meta, action = null) {
      const card = document.getElementById(`otpSumCard${index}`);
      const labelEl = document.getElementById(`otpSumLabel${index}`);
      const valueEl = document.getElementById(`otpSumValue${index}`);
      const metaEl = document.getElementById(`otpSumMeta${index}`);
      if (labelEl) labelEl.textContent = label;
      if (valueEl) valueEl.textContent = value;
      if (metaEl) metaEl.textContent = meta;
      if (card) {
          card.onclick = typeof action === 'function' ? action : null;
          card.style.cursor = typeof action === 'function' ? 'pointer' : 'default';
      }
  }

  function renderOtpTemplateSummaryRR(data, mode = 'otp') {
      const strip = document.getElementById('otpSummaryStrip');
      if (!strip) {
          updateOtpHelpInsightRR();
          return;
      }
      const d = data || {};
      const card5 = document.getElementById('otpSumCard5');
      card5?.classList.remove('is-good','is-warning','is-critical');

      if (mode === 'collection') {
          setSummaryCardRR(1, 'Target Collection', `${fmt(d.target_noa || 0)} NOA`, 'Wajib dihubungi', () => openCollectionAreaDetailRR('TOTAL','ALL'));
          setSummaryCardRR(2, 'Sudah Call', `${fmt(d.call_noa || 0)} NOA`, fmtPct(d.call_percent || 0), () => openCollectionAreaDetailRR('TOTAL','CALL'));
          setSummaryCardRR(3, 'NC', `${fmt(d.nc_noa || 0)} NOA`, fmtShort(d.nc_os || 0), () => openCollectionAreaDetailRR('TOTAL','NC'));
          setSummaryCardRR(4, 'PTP', `${fmt(d.ptp_noa || 0)} NOA`, fmtShort(d.ptp_os || 0), () => openCollectionAreaDetailRR('TOTAL','PTP'));
          setSummaryCardRR(5, 'PO', `${fmt(d.po_noa || 0)} NOA`, fmtShort(d.po_bayar || 0), () => openCollectionAreaDetailRR('TOTAL','PO'));
          const callPct = Number(d.call_percent || 0);
          card5?.classList.add(callPct >= 80 ? 'is-good' : (callPct >= 50 ? 'is-warning' : 'is-critical'));
      } else {
          const pct = Number(d.persen || 0);
          const target = Number(d.target_os || 0);
          const runoff = Number(d.total_bayar || 0);
          const gap = Math.max(0, target - runoff);
          setSummaryCardRR(1, 'Target M-1', fmtShort(target), `${fmt(d.target_noa || 0)} NOA`, () => initModalDetail('ALL','ALL'));
          setSummaryCardRR(2, 'OTP Lancar', fmtShort(d.lancar_os || 0), `${fmt(d.lancar_noa || 0)} NOA`, () => initModalDetail('ALL','LANCAR'));
          setSummaryCardRR(3, 'Ditagih', fmtShort(d.macet_os || 0), `${fmt(d.macet_noa || 0)} NOA`, () => initModalDetail('ALL','MENUNGGAK'));
          setSummaryCardRR(4, 'Total Run Off', fmtShort(runoff), `Sisa ${fmtShort(gap)}`, () => initModalDetail('ALL','TOTAL_BAYAR'));
          setSummaryCardRR(5, 'Capaian', fmtPct(pct), pct >= 85 ? 'Baik' : (pct >= 60 ? 'Perlu perhatian' : 'Kritis'));
          card5?.classList.add(pct >= 85 ? 'is-good' : (pct >= 60 ? 'is-warning' : 'is-critical'));
      }
      updateOtpHelpInsightRR();
  }

  function updateOtpHelpInsightRR() {
      const gt = rekapGtRaw || {};
      const pct = Number(gt.persen || 0);
      const target = Number(gt.target_os || 0);
      const runoff = Number(gt.total_bayar || 0);
      const gap = Math.max(0, target - runoff);
      const ditagihNoa = Number(gt.macet_noa || 0);
      const ditagihOs = Number(gt.macet_os || 0);
      const lewatPct = Number(dueSummaryRaw?.lewat?.persen || gt.angsuran_lewat_persen || 0);
      const nc = Number(mainCollectionSummary?.nc_noa || 0);
      const ptp = Number(mainCollectionSummary?.ptp_noa || 0);

      const put = (id, value) => { const el = document.getElementById(id); if (el) el.textContent = value; };
      put('otpInsightPct', Number.isFinite(pct) ? fmtPct(pct) : '-');
      put('otpInsightTarget', fmtShort(target));
      put('otpInsightRunoff', fmtShort(runoff));
      put('otpInsightGap', fmtShort(gap));

      let condition = 'Data belum tersedia untuk dianalisis.';
      if (target > 0) {
          if (pct >= 85) condition = `Kondisi OTP tergolong baik pada ${fmtPct(pct)}. Fokus berikutnya adalah menutup sisa ${fmtShort(gap)} dan menjaga rekening yang sudah lancar agar tidak kembali menunggak.`;
          else if (pct >= 60) condition = `Capaian OTP ${fmtPct(pct)} masih perlu perhatian. Terdapat ${fmt(ditagihNoa)} NOA senilai ${fmtShort(ditagihOs)} yang perlu dipastikan komitmen dan realisasi pembayarannya.`;
          else condition = `Capaian OTP masih kritis pada ${fmtPct(pct)}. Sisa target ${fmtShort(gap)} harus dipecah per AO, debitur, dan tanggal janji bayar untuk monitoring harian.`;
      }
      put('otpInsightCondition', condition);

      const actions = [];
      if (ditagihNoa > 0) actions.push(`Prioritaskan ${fmt(ditagihNoa)} rekening ditagih dengan outstanding ${fmtShort(ditagihOs)}; urutkan dari nominal terbesar dan DPD paling berisiko.`);
      if (lewatPct > 0) actions.push(`${fmtPct(lewatPct)} angsuran tercatat lewat tanggal tagih; pastikan penyebab, tanggal janji baru, dan PIC tindak lanjut tercatat di CCL.`);
      if (nc > 0) actions.push(`${fmt(nc)} rekening berstatus NC belum mempunyai komitmen; Kepala Cabang perlu menetapkan target call dan batas waktu pembaruan.`);
      if (ptp > 0) actions.push(`${fmt(ptp)} rekening berstatus PTP harus dipantau sampai realisasi minimal memenuhi ketentuan pembayaran.`);
      actions.push(`Bandingkan sisa target ${fmtShort(gap)} dengan kapasitas janji bayar harian agar proyeksi pencapaian akhir periode realistis.`);
      actions.push('Lakukan review singkat setiap hari: rekening terbesar, janji jatuh tempo hari ini, pembayaran masuk, kendala, dan eskalasi yang dibutuhkan.');
      const actionEl = document.getElementById('otpInsightAction');
      if (actionEl) actionEl.innerHTML = actions.slice(0,6).map(item => `<div>${escRR(item)}</div>`).join('');
  }

  function otpMobileItemRR(label, value, meta, onclick, tone = '') {
      const tag = onclick ? 'button' : 'div';
      const click = onclick ? ` type="button" onclick="${onclick}"` : '';
      return `<${tag}${click} class="otp-mobile-item ${tone}"><span>${label}</span><b>${value}</b>${meta ? `<small>${meta}</small>` : ''}</${tag}>`;
  }

  function renderOtpMobileCardsRR(rows, gt) {
      const el = document.getElementById('otpMobileMain');
      if (!el) return;
      setOtpMainSurfaceRR(true);
      renderOtpTemplateSummaryRR(gt, 'otp');
      if (!Array.isArray(rows) || !rows.length) {
          el.innerHTML = '<div class="otp-mobile-card-empty">Tidak ada data penagihan.</div>';
          return;
      }

      const total = gt ? `
        <article class="otp-mobile-total">
          <div class="otp-mobile-card-head">
            <div><div class="otp-mobile-card-title">Grand Total OTP</div><div class="otp-mobile-card-sub">Ringkasan seluruh tanggal tagih</div></div>
            ${renderOtpPctBadge(gt.persen)}
          </div>
          <div class="otp-mobile-grid">
            ${otpMobileItemRR('Target M-1', fmtShort(gt.target_os), `${fmt(gt.target_noa)} NOA`, "initModalDetail('ALL','ALL')")}
            ${otpMobileItemRR('OTP Lancar', fmtShort(gt.lancar_os), `${fmt(gt.lancar_noa)} NOA`, "initModalDetail('ALL','LANCAR')", 'item-green')}
            ${otpMobileItemRR('Ditagih', fmtShort(gt.macet_os), `${fmt(gt.macet_noa)} NOA`, "initModalDetail('ALL','MENUNGGAK')", 'item-red')}
            ${otpMobileItemRR('Lunas', fmtShort(gt.lunas_os), `${fmt(gt.lunas_noa)} NOA`, "initModalLunas('ALL')", 'item-green')}
            ${otpMobileItemRR('Angsuran', fmtShort(gt.angsuran), `${fmtPct(gt.angsuran_sesuai_persen)} sesuai JT`, "initModalDetail('ALL','ANGSURAN')", 'item-amber')}
            ${otpMobileItemRR('Total Run Off', fmtShort(gt.total_bayar), 'Realisasi kumulatif', null)}
          </div>
        </article>` : '';

      el.innerHTML = total + rows.map(r => {
          const tgl = escRR(r.tgl);
          return `
            <article class="otp-mobile-day-card">
              <div class="otp-mobile-card-head">
                <div><div class="otp-mobile-card-title">Tanggal ${tgl}</div><div class="otp-mobile-card-sub">Klik nominal untuk membuka detail rekening</div></div>
                ${renderOtpPctBadge(r.persen)}
              </div>
              <div class="otp-mobile-grid">
                ${otpMobileItemRR('Target M-1', fmtShort(r.target_os), `${fmt(r.target_noa)} NOA`, `initModalDetail('${tgl}','ALL')`)}
                ${otpMobileItemRR('OTP Lancar', fmtShort(r.lancar_os), `${fmt(r.lancar_noa)} NOA`, `initModalDetail('${tgl}','LANCAR')`, 'item-green')}
                ${otpMobileItemRR('Ditagih', fmtShort(r.macet_os), `${fmt(r.macet_noa)} NOA`, `initModalDetail('${tgl}','MENUNGGAK')`, 'item-red')}
                ${otpMobileItemRR('Lunas', fmtShort(r.lunas_os), `${fmt(r.lunas_noa)} NOA`, `initModalLunas('${tgl}')`, 'item-green')}
                ${otpMobileItemRR('Angsuran', fmtShort(r.angsuran), `${fmtPct(r.angsuran_sesuai_persen)} sesuai JT`, `initModalDetail('${tgl}','ANGSURAN')`, 'item-amber')}
                ${otpMobileItemRR('Total Run Off', fmtShort(r.total_bayar), `${fmtPct(r.angsuran_lewat_persen)} lewat JT`, null)}
              </div>
            </article>`;
      }).join('');
  }

  function renderCollectionMobileCardsRR() {
      const el = document.getElementById('otpMobileMain');
      if (!el) return;
      setOtpMainSurfaceRR(true);
      renderOtpTemplateSummaryRR(mainCollectionSummary, 'collection');
      const rows = [];
      if (mainCollectionSummary) rows.push({ ...mainCollectionSummary, kode_area:'TOTAL', nama_area:'TOTAL KONSOLIDASI', __total:true });
      rows.push(...(mainCollectionRows || []));
      if (!rows.length) {
          el.innerHTML = '<div class="otp-mobile-card-empty">Tidak ada data collection.</div>';
          return;
      }
      el.innerHTML = rows.map(r => {
          const codeRaw = String(r.kode_area || '-');
          const codeJs = JSON.stringify(codeRaw);
          const callPct = Number(r.call_percent || 0);
          return `
            <article class="otp-mobile-collection-card ${r.__total ? 'otp-mobile-total' : ''}">
              <div class="otp-mobile-card-head">
                <div class="min-w-0"><div class="otp-mobile-card-title">${escRR(r.nama_area || '-')}</div><div class="otp-mobile-card-sub">Kode ${escRR(codeRaw)}</div></div>
                <span class="otp-pct-badge ${callPct >= 80 ? 'otp-pct-green' : (callPct >= 50 ? 'otp-pct-yellow' : 'otp-pct-red')}">${fmtPct(callPct)}</span>
              </div>
              <div class="otp-mobile-grid">
                ${otpMobileItemRR('Target', `${fmt(r.target_noa)} NOA`, 'Wajib dihubungi', `openCollectionAreaDetailRR(${codeJs},'ALL')`)}
                ${otpMobileItemRR('Sudah Call', `${fmt(r.call_noa)} NOA`, fmtPct(callPct), `openCollectionAreaDetailRR(${codeJs},'CALL')`)}
                ${otpMobileItemRR('NC', `${fmt(r.nc_noa)} NOA`, fmtShort(r.nc_os), `openCollectionAreaDetailRR(${codeJs},'NC')`, 'item-red')}
                ${otpMobileItemRR('PTP', `${fmt(r.ptp_noa)} NOA`, fmtShort(r.ptp_os), `openCollectionAreaDetailRR(${codeJs},'PTP')`, 'item-amber')}
                ${otpMobileItemRR('PO', `${fmt(r.po_noa)} NOA`, fmtShort(r.po_os), `openCollectionAreaDetailRR(${codeJs},'PO')`, 'item-green')}
                ${otpMobileItemRR('Pembayaran PO', fmtShort(r.po_bayar), 'Realisasi bayar', null, 'item-green')}
              </div>
            </article>`;
      }).join('');
  }

  const apiCall = async (url, opt = {}) => {
      const res = await fetch(url, opt);
      try {
          const json = await res.json();
          return { ok: res.ok, status: res.status, json: json };
      } catch (e) {
          throw new Error("Gagal parsing JSON.");
      }
  };

  let abortRR;
  let currentDetailParams = {};
  let currentDetailPage = 1;
  let currentDetailTotalPages = 1;
  let currentMode = 'NORMAL'; 
  const detailLimit = 20;
  let detailSearchTimer = null;

  let sortMainCol = '', sortMainAsc = true;
  let sortDetailCol = '', sortDetailAsc = true;
  let otpMainViewMode = window.innerWidth < 768 ? 'mobile' : 'desktop';

  const isOtpMobile = () => window.innerWidth < 768;
  const getMainColspanRR = () => mainOtpView === 'collection' ? 12 : (isOtpMobile() ? 10 : 14);
  const getDetailColspanRR = () => currentMode === 'NORMAL' ? 21 : 10;

  function renderOtpColgroupRR() {
      const cg = document.getElementById('otpColgroup');
      if (!cg) return;
      if (mainOtpView === 'collection') {
          /*
           * Desktop/tablet tetap menampilkan kode cabang.
           * Mobile menyembunyikan kode dan memakai nama kantor yang lebih compact.
           */
          if (isOtpMobile()) {
              cg.innerHTML = `
                  <col class="collection-code-track" style="width:0">
                  <col style="width:96px">
                  <col style="width:58px">
                  <col style="width:58px">
                  <col style="width:64px">
                  <col style="width:58px">
                  <col style="width:104px">
                  <col style="width:58px">
                  <col style="width:104px">
                  <col style="width:58px">
                  <col style="width:104px">
                  <col style="width:104px">
              `;
          } else {
              cg.innerHTML = `
                  <col class="collection-code-track" style="width:48px">
                  <col style="width:150px">
                  <col style="width:58px">
                  <col style="width:58px">
                  <col style="width:64px">
                  <col style="width:58px">
                  <col style="width:104px">
                  <col style="width:58px">
                  <col style="width:104px">
                  <col style="width:58px">
                  <col style="width:104px">
                  <col style="width:104px">
              `;
          }
          return;
      }

      if (isOtpMobile()) {
          cg.innerHTML = `
              <col style="width:48px">
              <col style="width:92px">
              <col style="width:92px">
              <col style="width:92px">
              <col style="width:92px">
              <col style="width:100px">
              <col style="width:100px">
              <col style="width:92px">
              <col style="width:100px">
              <col style="width:62px">
          `;
      } else {
          /* Total lebar dasar desktop sekitar 1.086px agar muat di panel web tanpa scroll horizontal. */
          cg.innerHTML = `
              <col style="width:56px">
              <col style="width:96px">
              <col style="width:44px">
              <col style="width:96px">
              <col style="width:44px">
              <col style="width:96px">
              <col style="width:44px">
              <col style="width:96px">
              <col style="width:44px">
              <col style="width:104px">
              <col style="width:104px">
              <col style="width:96px">
              <col style="width:102px">
              <col style="width:64px">
          `;
      }
  }

  function mobileMetricRR(value, noa, action, tone = '') {
      return `
          <a href="javascript:void(0)" onclick="${action}" class="otp-mobile-metric ${tone}">
              <span class="otp-mobile-metric-main">${fmt(value)}</span>
              <small class="otp-mobile-metric-noa">${fmt(noa)} NOA</small>
          </a>
      `;
  }

  const getSortIcon = (col, currentCol, asc) => {
      if (col !== currentCol) return '<span class="opacity-30 text-[10px] ml-1">&#8597;</span>';
      return asc ? '<span class="text-blue-600 ml-1 text-[11px]">&#9650;</span>' : '<span class="text-blue-600 ml-1 text-[11px]">&#9660;</span>';
  };

  const CCL_ACCESS_CODE_RR = 'ccl2026';
  const CCL_ACCESS_SESSION_KEY_RR = 'otp_ccl_access_granted_v1';
  let pendingCclCollectionStatusRR = 'ALL';
  let cclAccessLastFocusedRR = null;

  function isCclAccessGrantedRR() {
      try {
          return sessionStorage.getItem(CCL_ACCESS_SESSION_KEY_RR) === '1';
      } catch (error) {
          return false;
      }
  }

  function grantCclAccessRR() {
      try {
          sessionStorage.setItem(CCL_ACCESS_SESSION_KEY_RR, '1');
      } catch (error) {
          /* Fallback hanya untuk halaman aktif jika sessionStorage diblokir. */
          window.__otpCclAccessGrantedRR = true;
      }
  }

  function hasCclAccessRR() {
      return isCclAccessGrantedRR() || window.__otpCclAccessGrantedRR === true;
  }

  function openCclAccessModalRR(status = 'ALL') {
      pendingCclCollectionStatusRR = ['CALL', 'NC', 'PTP', 'PO', 'ALL'].includes(String(status || '').toUpperCase())
          ? String(status || 'ALL').toUpperCase()
          : 'ALL';

      const modal = document.getElementById('otpCclAccessModal');
      const input = document.getElementById('otpCclAccessInput');
      const error = document.getElementById('otpCclAccessError');
      const card = document.querySelector('#otpCclAccessModal .otp-ccl-access-card');
      if (!modal || !input) return;

      cclAccessLastFocusedRR = document.activeElement instanceof HTMLElement ? document.activeElement : null;
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('otp-ccl-access-open');
      card?.classList.remove('is-error');
      input.value = '';
      input.type = 'password';
      if (error) error.textContent = '';
      syncCclAccessEyeRR(false);

      requestAnimationFrame(() => input.focus({ preventScroll:true }));
  }

  window.closeCclAccessModalRR = function() {
      const modal = document.getElementById('otpCclAccessModal');
      const error = document.getElementById('otpCclAccessError');
      const input = document.getElementById('otpCclAccessInput');
      if (!modal) return;

      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('otp-ccl-access-open');
      if (error) error.textContent = '';
      if (input) input.value = '';
      pendingCclCollectionStatusRR = 'ALL';

      if (cclAccessLastFocusedRR && document.contains(cclAccessLastFocusedRR)) {
          cclAccessLastFocusedRR.focus({ preventScroll:true });
      }
      cclAccessLastFocusedRR = null;
  };

  function syncCclAccessEyeRR(visible) {
      const btn = document.getElementById('otpCclAccessToggle');
      const eye = document.getElementById('otpCclAccessEye');
      if (btn) btn.setAttribute('aria-label', visible ? 'Sembunyikan kode' : 'Tampilkan kode');
      if (eye) {
          eye.innerHTML = visible
              ? '<path d="M3 3l18 18"></path><path d="M10.6 10.7a2 2 0 0 0 2.7 2.7"></path><path d="M9.9 4.2A10.8 10.8 0 0 1 12 4c6.5 0 10 8 10 8a17.6 17.6 0 0 1-2.1 3.2"></path><path d="M6.6 6.6C3.7 8.5 2 12 2 12s3.5 8 10 8a10.7 10.7 0 0 0 5.4-1.5"></path>'
              : '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle>';
      }
  }

  window.toggleCclAccessVisibilityRR = function() {
      const input = document.getElementById('otpCclAccessInput');
      if (!input) return;
      const visible = input.type === 'password';
      input.type = visible ? 'text' : 'password';
      syncCclAccessEyeRR(visible);
      input.focus({ preventScroll:true });
  };

  window.submitCclAccessRR = function(event) {
      event?.preventDefault();
      const input = document.getElementById('otpCclAccessInput');
      const error = document.getElementById('otpCclAccessError');
      const card = document.querySelector('#otpCclAccessModal .otp-ccl-access-card');
      const code = String(input?.value || '').trim();

      if (code !== CCL_ACCESS_CODE_RR) {
          if (error) error.textContent = 'Kode akses tidak sesuai. Silakan periksa kembali.';
          card?.classList.remove('is-error');
          void card?.offsetWidth;
          card?.classList.add('is-error');
          if (input) {
              input.select();
              input.focus({ preventScroll:true });
          }
          return;
      }

      const requestedStatus = pendingCclCollectionStatusRR;
      grantCclAccessRR();
      closeCclAccessModalRR();
      setMainOtpViewRR('collection', requestedStatus);
  };

  function syncMainViewButtonRR() {
      const isCollection = mainOtpView === 'collection';
      const label = document.getElementById('mainViewLabelRR');
      const table = document.getElementById('tabelRekapRR');
      const title = document.getElementById('otpTitle');
      const bucket = getBucketLabel(document.getElementById('opt_dpd_bucket')?.value || 'all');
      const buttonLabel = isCollection ? 'Kembali ke tampilan OTP' : 'Buka Rekap CCL';
      const iconHtml = isCollection
          ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18"></path><path d="m9 18-6-6 6-6"></path></svg>`
          : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V9"></path><path d="M12 19V5"></path><path d="M20 19v-7"></path><path d="M3 19h18"></path></svg>`;

      ['btnMainViewRR', 'btnMainViewRRMobile'].forEach(id => {
          const btn = document.getElementById(id);
          if (!btn) return;
          btn.classList.toggle('is-collection', isCollection);
          btn.setAttribute('aria-pressed', isCollection ? 'true' : 'false');
          btn.title = buttonLabel;
          btn.setAttribute('aria-label', buttonLabel);
      });

      ['mainViewIconRR', 'mainViewIconRRMobile'].forEach(id => {
          const icon = document.getElementById(id);
          if (icon) icon.innerHTML = iconHtml;
      });

      if (label) label.textContent = isCollection ? 'OTP' : 'Rekap';
      if (table) table.classList.toggle('otp-collection-view', isCollection);
      if (title) title.textContent = isCollection ? 'REKAP CCL' : `OTP - ${bucket}`;
  }

  function setMainOtpViewRR(view = 'otp', collectionStatus = 'ALL') {
      const requestedView = view === 'collection' ? 'collection' : 'otp';
      if (requestedView === 'collection' && !hasCclAccessRR()) {
          openCclAccessModalRR(collectionStatus);
          return;
      }

      mainOtpView = requestedView;
      mainCollectionStatus = ['CALL', 'NC', 'PTP', 'PO', 'ALL'].includes(String(collectionStatus || '').toUpperCase())
          ? String(collectionStatus || 'ALL').toUpperCase()
          : 'ALL';

      syncMainViewButtonRR();
      const mainScroll = document.getElementById('otpMainScroll');
      if (mainScroll) { mainScroll.scrollTop = 0; mainScroll.scrollLeft = 0; }

      if (mainOtpView === 'otp') {
          renderMainHeaderRR();
          renderTableRR(rekapDataRaw, rekapGtRaw);
          return;
      }
      loadMainCollectionRR(1);
  }

  window.toggleMainOtpViewRR = function() {
      setMainOtpViewRR(mainOtpView === 'collection' ? 'otp' : 'collection', 'ALL');
  }

  window.showOtpDefaultRR = function() {
      setMainOtpViewRR('otp', 'ALL');
  }

  window.showCollectionRR = function(status = 'ALL') {
      setMainOtpViewRR('collection', status);
  }

  const getBucketLabel = bucket => {
      if (bucket === 'dpd0') return 'DPD 0';
      if (bucket === 'dpd1-30') return 'DPD 1-30';
      return 'ALL';
  };

  const getStatusLabelRR = status => {
      const map = {
          ALL: 'Semua',
          LANCAR: 'OTP Lancar',
          MENUNGGAK: 'Ditagih',
          ANGSURAN: 'Angsuran',
          TOTAL_BAYAR: 'Total Bayar',
          SESUAI_TAGIH: 'Angsuran Sesuai Tgl Tagih',
          LEWAT_TAGIH: 'Angsuran Lewat Tgl Tagih'
      };
      return map[String(status || '').toUpperCase()] || status || '-';
  };

  const fmtShort = n => {
      const v = Math.abs(Number(n || 0));
      const sign = Number(n || 0) < 0 ? '-' : '';
      if (v >= 1e12) return `${sign}${(v / 1e12).toFixed(2)} T`;
      if (v >= 1e9) return `${sign}${(v / 1e9).toFixed(2)} M`;
      if (v >= 1e6) return `${sign}${(v / 1e6).toFixed(2)} Jt`;
      if (v >= 1e3) return `${sign}${(v / 1e3).toFixed(1)} Rb`;
      return `${sign}${nfID.format(v)}`;
  };



  const escRR = value => String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');

  const getDetailSearchValueRR = () => {
      const desktop = document.getElementById('search_nasabah');
      const mobile = document.getElementById('search_nasabah_mobile');
      if (mobile && document.activeElement === mobile) return mobile.value || '';
      if (desktop && document.activeElement === desktop) return desktop.value || '';
      return (desktop?.value || mobile?.value || '');
  };

  function showModalRR() {
      document.getElementById('modalDetailRR')?.classList.remove('hidden');
      document.body.classList.add('otp-modal-open');
      setTimeout(() => {
          const content = document.querySelector('#modalDetailRR .flex-1.overflow-auto');
          if (content) { content.scrollTop = 0; content.scrollLeft = 0; }
      }, 30);
  }

  let mainFilterOpen = window.innerWidth >= 1280;

  function toggleMainFilter() {
      mainFilterOpen = !mainFilterOpen;
      applyFilterState();
  }

  function applyFilterState() {
      const el = document.getElementById('filterWrapperMain');
      if(mainFilterOpen) {
          el.classList.remove('filter-collapsed');
          el.classList.add('filter-expanded');
      } else {
          el.classList.remove('filter-expanded');
          el.classList.add('filter-collapsed');
      }
  }

  function mountOtpHelpPortalRR() {
      const panel = document.getElementById('otpHelpPanel');
      const backdrop = document.getElementById('otpHelpBackdrop');
      if (!panel) return;

      /*
       * The help panel originally lived inside the page header. The header forms
       * its own stacking context, so the full-screen backdrop could cover and
       * blur the panel. Move both elements directly under <body> so their
       * z-index is evaluated at the document level.
       */
      if (backdrop && backdrop.parentElement !== document.body) {
          document.body.appendChild(backdrop);
      }
      if (panel.parentElement !== document.body) {
          document.body.appendChild(panel);
      }
      if (!panel.dataset.portalReady) {
          panel.addEventListener('click', event => event.stopPropagation());
          panel.dataset.portalReady = '1';
      }
  }

  function positionOtpHelpPanel() {
      mountOtpHelpPortalRR();
      const panel = document.getElementById('otpHelpPanel');
      if (!panel) return;
      panel.removeAttribute('style');
  }

  function setOtpHelpOpen(open) {
      mountOtpHelpPortalRR();
      const panel = document.getElementById('otpHelpPanel');
      const button = document.getElementById('otpHelpButton');
      const backdrop = document.getElementById('otpHelpBackdrop');
      if (!panel) return;
      panel.classList.toggle('is-open', open);
      panel.setAttribute('aria-hidden', open ? 'false' : 'true');
      button?.classList.toggle('is-open', open);
      button?.setAttribute('aria-expanded', open ? 'true' : 'false');
      backdrop?.classList.toggle('is-open', open);
      document.body.classList.toggle('otp-help-open', open);
      if (open) {
          updateOtpHelpInsightRR();
          requestAnimationFrame(positionOtpHelpPanel);
      }
  }

  function toggleOtpHelp(event) {
      event?.stopPropagation();
      const panel = document.getElementById('otpHelpPanel');
      setOtpHelpOpen(!panel?.classList.contains('is-open'));
  }

  function closeOtpHelp(event) {
      event?.stopPropagation();
      setOtpHelpOpen(false);
  }

  document.addEventListener('click', (event) => {
      const panel = document.getElementById('otpHelpPanel');
      const button = document.getElementById('otpHelpButton');
      if (!panel?.classList.contains('is-open')) return;
      if (panel.contains(event.target) || button?.contains(event.target)) return;
      setOtpHelpOpen(false);
  });

  document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && document.getElementById('otpHelpPanel')?.classList.contains('is-open')) {
          setOtpHelpOpen(false);
      }
  });

  window.addEventListener('DOMContentLoaded', async () => {
    mountOtpHelpPortalRR();
    syncOtpViewportRR();
    mainFilterOpen = window.innerWidth >= 1280;
    mainOtpView = 'otp';
    applyFilterState();
    syncMainViewButtonRR();

    const user = (window.getUser && window.getUser()) || null;
    let uKode = (user && user.kode) ? String(user.kode).padStart(3, '0') : '000';
    if(uKode === '099') uKode = '000';
    
    await populateKantor(uKode);

    const d = await getLastHarianData(); 
    if(d) {
        document.getElementById('closing_date').value = d.last_closing;
        document.getElementById('harian_date').value  = d.last_created;
    } else {
        const now = new Date();
        now.setDate(now.getDate() - 1);
        const strH1 = now.toISOString().split('T')[0];
        document.getElementById('closing_date').value = strH1;
        document.getElementById('harian_date').value  = strH1;
    }
    fetchRekapRR();
  });

  window.addEventListener('resize', () => {
      syncOtpViewportRR();
      clearTimeout(window.__otpResizeTimer);
      window.__otpResizeTimer = setTimeout(() => {
          if(window.innerWidth >= 1280 && !mainFilterOpen) {
              mainFilterOpen = true;
              applyFilterState();
          }

          const nextMode = isOtpMobile() ? 'mobile' : 'desktop';
          if (nextMode !== otpMainViewMode) {
              otpMainViewMode = nextMode;
              renderMainHeaderRR();
              if (mainOtpView === 'collection') renderMainCollectionRR();
              else renderTableRR(rekapDataRaw, rekapGtRaw);
              const mainScroll = document.getElementById('otpMainScroll');
              if (mainScroll) mainScroll.scrollLeft = 0;
          } else {
              renderOtpColgroupRR();
          }

          updateCollectionStickyOffsetRR();
          updateOtpStickyOffsetsRR();
          if (document.getElementById('otpHelpPanel')?.classList.contains('is-open')) positionOtpHelpPanel();
          if (detailDataCache.length) renderDetailViewRR(detailDataCache);
      }, 120);
  });

  window.visualViewport?.addEventListener('resize', () => {
      syncOtpViewportRR();
      updateOtpStickyOffsetsRR();
  });

  async function getLastHarianData(){ 
      try{ const r = await fetch('./api/date/'); const j = await r.json(); return j.data||null; }catch{ return null; } 
  }
  
  async function populateKantor(uKode) {
    const el = document.getElementById('opt_kantor'); if(!el) return;
    if (uKode !== '000') { 
        try {
            const res = await fetch(API_KODE_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
            const json = await res.json();
            const myKantor = (json.data||[]).find(x => String(x.kode_kantor).padStart(3,'0') === uKode);
            const nama = myKantor ? myKantor.nama_kantor : `CABANG ${uKode}`;
            el.innerHTML = `<option value="${uKode}">${uKode} - ${nama}</option>`;
        } catch(e) {
            el.innerHTML = `<option value="${uKode}">CABANG ${uKode}</option>`; 
        }
        el.value = uKode; el.disabled = true;
        await handleCabangChangeOtp(true);
        return; 
    }
    try {
        const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ type: 'kode_kantor' }) });
        const j = await r.json();
        let h = '<option value="">ALL CABANG</option>';
        if(j.data) j.data.filter(x => x.kode_kantor !== '000').forEach(x => { h += `<option value="${x.kode_kantor}">${x.kode_kantor} - ${x.nama_kantor}</option>`; });
        el.innerHTML = h;
    } catch { el.innerHTML = '<option value="">ALL CABANG</option>'; }
    await handleCabangChangeOtp(true);
  }

  async function handleCabangChangeOtp(isInit = false) {
      const cabangVal = document.getElementById('opt_kantor').value;
      const lblSub = document.getElementById('lbl_sub_otp');
      const optSub = document.getElementById('opt_sub_otp');
      const optAo = document.getElementById('opt_ao_otp');

      if (cabangVal === "" || cabangVal === "000") {
          lblSub.innerText = "KORWIL";
          optSub.innerHTML = `
              <option value="">ALL KORWIL</option>
              <option value="SEMARANG">SEMARANG</option>
              <option value="SOLO">SOLO</option>
              <option value="BANYUMAS">BANYUMAS</option>
              <option value="PEKALONGAN">PEKALONGAN</option>
          `;
          optAo.innerHTML = '<option value="">PILIH CABANG DULU</option>';
          optAo.disabled = true;
      } else {
          lblSub.innerText = "KANKAS";
          optSub.innerHTML = '<option value="">ALL KANKAS</option>';
          optAo.innerHTML = '<option value="">ALL AO</option>';
          optAo.disabled = false;
          await loadKankasSubOtp(cabangVal);
          await loadAoMainOtp(cabangVal);
      }
      if (!isInit) fetchRekapRR();
  }

  async function loadKankasSubOtp(kodeCabang) {
      const optSub = document.getElementById('opt_sub_otp');
      try {
          const payload = { type: 'kode_kankas', kode_kantor: kodeCabang };
          const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          let h = '<option value="">ALL KANKAS</option>';
          if(j.data && Array.isArray(j.data)) j.data.forEach(x => { h += `<option value="${x.kode_group1}">${x.deskripsi_group1 || x.kode_group1}</option>`; });
          optSub.innerHTML = h;
      } catch(err) {}
  }

  async function loadAoMainOtp(kodeCabang) {
      const optAo = document.getElementById('opt_ao_otp');
      if (!optAo) return;
      try {
          const payload = { type: 'kode_ao_kredit', kode_kantor: kodeCabang };
          const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          let h = '<option value="">ALL AO</option>';
          if(j.data && Array.isArray(j.data)) {
              j.data.forEach(x => { h += `<option value="${x.kode_group2}">${x.nama_ao || x.kode_group2}</option>`; });
          }
          optAo.innerHTML = h;
      } catch(err) {
          optAo.innerHTML = '<option value="">ALL AO</option>';
      }
  }

  /* ============================
     RENDER MAIN TABLE 
     ============================ */
  function renderDueSummaryRR(summary) {
      const el = document.getElementById('dueSummaryRR');
      if (!el) return;
      if (!summary || !summary.total || Number(summary.total.angsuran || 0) <= 0) {
          el.classList.add('hidden');
          el.innerHTML = '';
          return;
      }

      const card = (key, title, tone, clickStatus) => {
          const d = summary[key] || {};
          const pct = Number(d.persen || 0);
          const noa = Number(d.noa || 0);
          const angsuran = Number(d.angsuran || 0);
          const baki = Number(d.baki_debet || 0);
          const cls = {
              green: 'border-emerald-200 bg-emerald-50 text-emerald-700',
              red: 'border-rose-200 bg-rose-50 text-rose-700',
              blue: 'border-blue-200 bg-blue-50 text-blue-700',
              slate: 'border-slate-200 bg-slate-50 text-slate-700'
          }[tone] || 'border-slate-200 bg-slate-50 text-slate-700';
          const clickAttr = clickStatus ? `onclick="initModalDetail('ALL','${clickStatus}')"` : '';
          const clickCls = clickStatus ? 'cursor-pointer hover:-translate-y-0.5 hover:shadow-sm' : '';
          return `
              <button type="button" ${clickAttr} class="otp-due-card ${cls} ${clickCls}">
                  <div class="flex items-start justify-between gap-2">
                      <div class="min-w-0">
                          <div class="otp-due-label">${title}</div>
                          <div class="otp-due-value">${fmtShort(angsuran)}</div>
                      </div>
                      <div class="otp-due-pct">${pct}%</div>
                  </div>
                  <div class="otp-due-meta">
                      <span>${fmt(noa)} NOA</span>
                      <span>Baki Debet ${fmtShort(baki)}</span>
                  </div>
              </button>
          `;
      };

      el.innerHTML = `
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
              ${card('sesuai', 'Angsuran Sesuai Tgl Tagih', 'green', 'SESUAI_TAGIH')}
              ${card('lewat', 'Angsuran Lewat Tgl Tagih', 'red', 'LEWAT_TAGIH')}
              ${card('total', 'Total Angsuran', 'blue', 'ANGSURAN')}
          </div>
      `;
      el.classList.remove('hidden');
  }


  function updateOtpStickyOffsetsRR() {
      const table = document.getElementById('tabelRekapRR');
      const head = document.getElementById('headRR');
      if (!table || !head || !head.rows?.length) return;

      requestAnimationFrame(() => {
          const rows = Array.from(head.rows);
          const row1 = rows[0];
          const row2 = rows[1] && !rows[1].classList.contains('sticky-total') ? rows[1] : null;

          const row1Height = Math.max(1, Math.ceil(row1?.getBoundingClientRect().height || 34));
          const row2Height = Math.max(0, Math.ceil(row2?.getBoundingClientRect().height || 0));
          const totalTop = row1Height + row2Height;

          table.style.setProperty('--otp-head-row-1-h', `${row1Height}px`);
          table.style.setProperty('--otp-head-row-2-h', `${row2Height}px`);
          table.style.setProperty('--otp-head-total-top', `${totalTop}px`);

          if (mainOtpView === 'collection') {
              table.style.setProperty('--collection-head-h', `${totalTop}px`);
          }
      });
  }

  function getDueNoaRR(source, type, isGrandTotal = false) {
      const row = source || {};
      const isLewat = String(type || '').toLowerCase() === 'lewat';
      const perDateCollection = dueSummaryRaw?.by_date || dueSummaryRaw?.per_tanggal || dueSummaryRaw?.dates || null;
      let perDate = null;
      if (!isGrandTotal && perDateCollection) {
          if (Array.isArray(perDateCollection)) {
              perDate = perDateCollection.find(item => String(item?.tgl || item?.tanggal || '') === String(row?.tgl || '')) || null;
          } else if (typeof perDateCollection === 'object') {
              perDate = perDateCollection[row?.tgl] || null;
          }
      }

      const nested = isGrandTotal
          ? (isLewat ? dueSummaryRaw?.lewat : dueSummaryRaw?.sesuai)
          : (isLewat
              ? (row?.due_summary?.lewat || perDate?.lewat)
              : (row?.due_summary?.sesuai || perDate?.sesuai));

      const keys = isLewat
          ? ['angsuran_lewat_noa', 'angsuran_lewat_nasabah', 'lewat_noa', 'noa_lewat', 'jumlah_nasabah_lewat', 'jumlah_debitur_lewat']
          : ['angsuran_sesuai_noa', 'angsuran_sesuai_nasabah', 'sesuai_noa', 'noa_sesuai', 'jumlah_nasabah_sesuai', 'jumlah_debitur_sesuai'];

      const candidates = [nested?.noa, nested?.jumlah_nasabah, nested?.jumlah_debitur];
      keys.forEach(key => candidates.push(row[key]));

      for (const value of candidates) {
          if (value !== null && value !== undefined && value !== '' && Number.isFinite(Number(value))) {
              return Number(value);
          }
      }
      return null;
  }

  function renderDueMetricRR(amount, percent, noa, action, tone = '') {
      const noaLabel = noa === null || noa === undefined
          ? '- NOA'
          : `${fmt(noa)} NOA`;
      return `
          <button type="button" onclick="${action}" class="otp-due-metric ${tone}">
              <span class="otp-due-metric-main">${fmt(amount)}</span>
              <span class="otp-due-metric-meta">
                  <span class="otp-due-metric-noa">${noaLabel}</span>
                  <span class="otp-due-metric-pct">${fmtPct(percent)}</span>
              </span>
          </button>
      `;
  }

  function updateCollectionStickyOffsetRR() {
      const table = document.getElementById('tabelRekapRR');
      if (!table || mainOtpView !== 'collection') return;

      requestAnimationFrame(() => {
          const thead = table.tHead;
          const height = thead ? Math.ceil(thead.getBoundingClientRect().height) : 64;
          table.style.setProperty('--collection-head-h', `${Math.max(height, 1)}px`);
      });
  }

  function renderMainHeaderRR() {
      const head = document.getElementById('headRR');
      if (!head) return;
      renderOtpColgroupRR();

      if (mainOtpView === 'collection') {
          head.innerHTML = `
              <tr class="rr-row-1 collection-head-row">
                <th rowspan="2" class="otp-excel-head otp-group-date collection-code-col sticky left-0 z-40">Kode</th>
                <th rowspan="2" class="otp-excel-head otp-group-date collection-area-col text-left">Nama Kantor</th>
                <th colspan="3" class="otp-excel-head otp-group-target">
                    <div class="collection-group-title"><span>Target Collection</span><small>Target yang wajib dihubungi</small></div>
                </th>
                <th colspan="2" class="otp-excel-head otp-group-collect">
                    <div class="collection-group-title"><span>NC</span><small>Belum Ada Komitmen</small></div>
                </th>
                <th colspan="2" class="otp-excel-head otp-group-installment">
                    <div class="collection-group-title"><span>PTP</span><small>Promise to Pay / Janji Bayar</small></div>
                </th>
                <th colspan="3" class="otp-excel-head otp-group-paid">
                    <div class="collection-group-title"><span>PO</span><small>Sudah Ada Pembayaran</small></div>
                </th>
              </tr>
              <tr class="rr-row-2 collection-subhead-row">
                <th class="otp-excel-sub otp-group-target">NOA</th>
                <th class="otp-excel-sub otp-group-target">Call</th>
                <th class="otp-excel-sub otp-group-target">% Call</th>
                <th class="otp-excel-sub otp-group-collect">NOA</th>
                <th class="otp-excel-sub otp-group-collect">OS</th>
                <th class="otp-excel-sub otp-group-installment">NOA</th>
                <th class="otp-excel-sub otp-group-installment">OS</th>
                <th class="otp-excel-sub otp-group-paid">NOA</th>
                <th class="otp-excel-sub otp-group-paid">OS</th>
                <th class="otp-excel-sub otp-group-paid">Bayar</th>
              </tr>
          `;
          updateCollectionStickyOffsetRR();
          updateOtpStickyOffsetsRR();
          return;
      }

      if (isOtpMobile()) {
          head.innerHTML = `
              <tr class="rr-row-1">
                <th rowspan="2" class="otp-excel-head otp-head-tgl otp-group-date sticky left-0 z-30" onclick="sortMainRR('tgl', 'string')">
                    <div class="flex items-center justify-center">TGL ${getSortIcon('tgl', sortMainCol, sortMainAsc)}</div>
                </th>
                <th rowspan="2" class="otp-excel-head otp-group-target" onclick="sortMainRR('target_os', 'number')">TARGET M-1</th>
                <th rowspan="2" class="otp-excel-head otp-group-otp" onclick="sortMainRR('lancar_os', 'number')">OTP</th>
                <th rowspan="2" class="otp-excel-head otp-group-collect" onclick="sortMainRR('macet_os', 'number')">DITAGIH</th>
                <th rowspan="2" class="otp-excel-head otp-group-paid" onclick="sortMainRR('lunas_os', 'number')">LUNAS</th>
                <th colspan="3" class="otp-excel-head otp-group-installment">ANGSURAN</th>
                <th rowspan="2" class="otp-excel-head otp-group-runoff" onclick="sortMainRR('total_bayar', 'number')">TOTAL RUN OFF</th>
                <th rowspan="2" class="otp-excel-head otp-group-percent" onclick="sortMainRR('persen', 'number')">%</th>
              </tr>
              <tr class="rr-row-2">
                <th class="otp-excel-sub otp-group-installment" onclick="sortMainRR('angsuran_lewat_persen', 'number')">LEWAT JT</th>
                <th class="otp-excel-sub otp-group-installment" onclick="sortMainRR('angsuran_sesuai_persen', 'number')">SESUAI JT</th>
                <th class="otp-excel-sub otp-group-installment" onclick="sortMainRR('angsuran', 'number')">TOTAL</th>
              </tr>
              <tr class="rr-row-tot sticky-total otp-total-main-row" id="rowTotalRRAtas"></tr>
          `;
          updateOtpStickyOffsetsRR();
          return;
      }

      head.innerHTML = `
          <tr class="rr-row-1">
            <th rowspan="2" class="otp-excel-head otp-head-tgl otp-group-date sticky left-0 z-30" onclick="sortMainRR('tgl', 'string')">
                <div class="flex items-center justify-center">TGL ${getSortIcon('tgl', sortMainCol, sortMainAsc)}</div>
            </th>
            <th colspan="2" class="otp-excel-head otp-group-target">TARGET M-1</th>
            <th colspan="2" class="otp-excel-head otp-group-otp">OTP</th>
            <th colspan="2" class="otp-excel-head otp-group-collect">DITAGIH</th>
            <th colspan="2" class="otp-excel-head otp-group-paid">LUNAS</th>
            <th colspan="3" class="otp-excel-head otp-group-installment">ANGSURAN</th>
            <th rowspan="2" class="otp-excel-head otp-group-runoff" onclick="sortMainRR('total_bayar', 'number')">
                <div class="flex items-center justify-center">TOTAL RUN OFF ${getSortIcon('total_bayar', sortMainCol, sortMainAsc)}</div>
            </th>
            <th rowspan="2" class="otp-excel-head otp-group-percent" onclick="sortMainRR('persen', 'number')">
                <div class="flex items-center justify-center">% ${getSortIcon('persen', sortMainCol, sortMainAsc)}</div>
            </th>
          </tr>
          <tr class="rr-row-2">
            <th class="otp-excel-sub otp-group-target" onclick="sortMainRR('target_os', 'number')">BAKI DEBET</th>
            <th class="otp-excel-sub otp-group-target" onclick="sortMainRR('target_noa', 'number')">NOA</th>
            <th class="otp-excel-sub otp-group-otp" onclick="sortMainRR('lancar_os', 'number')">BAKI DEBET</th>
            <th class="otp-excel-sub otp-group-otp" onclick="sortMainRR('lancar_noa', 'number')">NOA</th>
            <th class="otp-excel-sub otp-group-collect" onclick="sortMainRR('macet_os', 'number')">BAKI DEBET</th>
            <th class="otp-excel-sub otp-group-collect" onclick="sortMainRR('macet_noa', 'number')">NOA</th>
            <th class="otp-excel-sub otp-group-paid" onclick="sortMainRR('lunas_os', 'number')">BAKI DEBET</th>
            <th class="otp-excel-sub otp-group-paid" onclick="sortMainRR('lunas_noa', 'number')">NOA</th>
            <th class="otp-excel-sub otp-group-installment" onclick="sortMainRR('angsuran_lewat_persen', 'number')">LEWAT JT</th>
            <th class="otp-excel-sub otp-group-installment" onclick="sortMainRR('angsuran_sesuai_persen', 'number')">SESUAI JT</th>
            <th class="otp-excel-sub otp-group-installment" onclick="sortMainRR('angsuran', 'number')">TOTAL</th>
          </tr>
          <tr class="rr-row-tot sticky-total otp-total-main-row" id="rowTotalRRAtas"></tr>
      `;
      updateOtpStickyOffsetsRR();
  }

  window.sortMainRR = function(col, type) {
      if (!rekapDataRaw || rekapDataRaw.length === 0) return;
      if (sortMainCol === col) sortMainAsc = !sortMainAsc;
      else { sortMainCol = col; sortMainAsc = true; }

      rekapDataRaw.sort((a, b) => {
          let valA = a[col], valB = b[col];
          if (type === 'number') {
              return sortMainAsc ? (parseFloat(valA)||0) - (parseFloat(valB)||0) : (parseFloat(valB)||0) - (parseFloat(valA)||0);
          } else {
              valA = String(valA||'').toLowerCase(); valB = String(valB||'').toLowerCase();
              if (valA < valB) return sortMainAsc ? -1 : 1;
              if (valA > valB) return sortMainAsc ? 1 : -1;
              return 0;
          }
      });
      renderMainHeaderRR(); renderTableRR(rekapDataRaw, rekapGtRaw);
  }

  async function fetchRekapRR(){
    const l = document.getElementById('loadingRR');
    const tb = document.getElementById('bodyRR');
    let trTotal = document.getElementById('rowTotalRRAtas');
    const colspan = getMainColspanRR();

    if(abortRR) abortRR.abort();
    abortRR = new AbortController();
    l.classList.remove('hidden');
    tb.innerHTML = `<tr><td colspan="${colspan}" class="py-20 text-center text-slate-400 italic text-sm">Sedang mengambil data...</td></tr>`;
    if(trTotal) trTotal.innerHTML = '';
    rekapDataRaw = [];
    rekapGtRaw = null;
    const mobileMain = document.getElementById('otpMobileMain');
    if (isOtpMobile() && mobileMain) { setOtpMainSurfaceRR(true); mobileMain.innerHTML = '<div class="otp-mobile-card-empty">Memuat data OTP...</div>'; }
    dueSummaryRaw = null;
    mainOtpView = 'otp';
    syncMainViewButtonRR();
    sortMainCol = '';
    sortMainAsc = true;
    renderDueSummaryRR(null);

    try {
        const cabangVal = document.getElementById('opt_kantor').value;
        const subVal = document.getElementById('opt_sub_otp').value;
        const dpdBucket = document.getElementById('opt_dpd_bucket').value;
        const aoVal = document.getElementById('opt_ao_otp')?.value || '';

        let reqKorwil = '', reqKankas = '';
        if (!cabangVal || cabangVal === '000') reqKorwil = subVal;
        else reqKankas = subVal;

        const titleEl = document.getElementById('otpTitle');
        if(titleEl && mainOtpView === 'otp') titleEl.textContent = `OTP - ${getBucketLabel(dpdBucket)}`;

        const payload = {
            type: 'rekap_rr',
            closing_date: document.getElementById('closing_date').value,
            harian_date: document.getElementById('harian_date').value,
            kode_kantor: cabangVal || null,
            korwil: reqKorwil,
            kode_kankas: reqKankas,
            kode_ao: aoVal,
            dpd_bucket: dpdBucket,
            include_127: document.getElementById('chk_127').checked
        };

        const res = await apiCall(API_RR_URL, {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify(payload),
            signal:abortRR.signal
        });
        if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || 'Gagal memuat data');

        rekapDataRaw = res.json.data.data || [];
        rekapGtRaw = res.json.data.grand_total;
        dueSummaryRaw = res.json.data.due_summary || null;
        renderDueSummaryRR(dueSummaryRaw);
        renderMainHeaderRR();
        renderTableRR(rekapDataRaw, rekapGtRaw);

        const mainScroll = document.getElementById('otpMainScroll');
        if (mainScroll) mainScroll.scrollLeft = 0;
        if (window.innerWidth < 1280) {
            mainFilterOpen = false;
            applyFilterState();
        }
    } catch(err) {
        if(err.name !== 'AbortError') {
            tb.innerHTML = `<tr><td colspan="${getMainColspanRR()}" class="py-16 text-center text-rose-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">Error: ${escRR(err.message)}</td></tr>`;
            if (isOtpMobile() && mobileMain) mobileMain.innerHTML = `<div class="otp-mobile-card-empty text-rose-600">Error: ${escRR(err.message)}</div>`;
        }
    } finally {
        l.classList.add('hidden');
    }
  }

  async function loadMainCollectionRR(page = 1) {
      const l = document.getElementById('loadingRR');
      const tb = document.getElementById('bodyRR');
      if (!tb) return;
      l?.classList.remove('hidden');
      mainCollectionPage = page;
      renderMainHeaderRR();
      tb.innerHTML = `<tr><td colspan="12" class="py-20 text-center text-slate-400 italic text-sm">Sedang mengambil rekap collection...</td></tr>`;

      try {
          const payload = getCurrentOtpFilterPayloadRR('rekap_rr_collection_area');
          const res = await apiCall(API_RR_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          if (!res.ok || res.json.status !== 200) throw new Error(res.json.message || 'Gagal memuat rekap collection');
          mainCollectionRows = res.json.data?.data || [];
          mainCollectionSummary = res.json.data?.summary || null;
          mainCollectionTotalPages = 1;
          renderMainCollectionRR();
      } catch (err) {
          console.error(err);
          tb.innerHTML = `<tr><td colspan="12" class="py-16 text-center text-rose-600 font-bold">${escRR(err.message || 'Gagal memuat rekap collection')}</td></tr>`;
      } finally {
          l?.classList.add('hidden');
      }
  }

  function collectionDetailStatusLabelRR(status) {
      const s = String(status || 'ALL').toUpperCase();
      if (s === 'CALL') return 'Call';
      if (['NC', 'PTP', 'PO'].includes(s)) return s;
      return 'Target';
  }

  window.openCollectionAreaDetailRR = function(areaCode, status = 'ALL') {
      const payload = getCurrentOtpFilterPayloadRR('detail_rr');
      const currentBranch = document.getElementById('opt_kantor')?.value || '';
      const cleanArea = String(areaCode || '').trim();
      const normalizedStatus = ['ALL', 'CALL', 'NC', 'PTP', 'PO'].includes(String(status || '').toUpperCase())
          ? String(status).toUpperCase()
          : 'ALL';
      const isTotal = cleanArea === 'TOTAL' || cleanArea === '-' || cleanArea === '';

      if (!isTotal) {
          if (currentBranch && currentBranch !== '000') {
              payload.kode_kankas = cleanArea;
          } else {
              payload.kode_kantor = cleanArea;
              payload.korwil = '';
              payload.kode_kankas = '';
          }
      }

      currentMode = 'NORMAL';
      detailActiveTab = 'matrix';
      detailCollectionSummary = null;
      currentDetailParams = {
          ...payload,
          tgl_tagih: 'ALL',
          status: 'ALL',
          collection_status: normalizedStatus,
          limit: detailLimit
      };

      syncCollectionFilterInputsRR(normalizedStatus);
      const title = collectionDetailStatusLabelRR(normalizedStatus);
      document.getElementById('modalTitleRR').textContent = `Detail CCL ${title}`;
      document.getElementById('modalSubTitleRR').textContent = isTotal ? 'TOTAL' : cleanArea;
      showModalRR();

      if (document.getElementById('search_nasabah')) document.getElementById('search_nasabah').value = '';
      if (document.getElementById('search_nasabah_mobile')) document.getElementById('search_nasabah_mobile').value = '';
      sortDetailCol = '';
      sortDetailAsc = true;
      renderModalHeaderRR();
      loadDetailPage(1);
  }

  function renderMainCollectionRR() {
      const tb = document.getElementById('bodyRR');
      if (!tb) return;
      renderOtpTemplateSummaryRR(mainCollectionSummary, 'collection');
      /* Rekap CCL pada mobile juga tetap menggunakan tabel. */
      setOtpMainSurfaceRR(false);
      const renderRow = (r, isTotal = false) => {
          const rowClass = isTotal ? 'bg-blue-50 font-black text-blue-900' : 'hover:bg-slate-50';
          const areaCodeRaw = String(r.kode_area || '-');
          const areaCode = escRR(areaCodeRaw);
          const areaCodeJs = JSON.stringify(areaCodeRaw);
          return `
            <tr class="${rowClass} ${isTotal ? 'collection-total-row' : ''}">
              <td class="collection-code-col px-3 py-2 border-r border-slate-100 font-mono sticky left-0 z-20 ${isTotal ? 'bg-blue-50' : 'bg-white'}">${areaCode}</td>
              <td title="${escRR(r.nama_area || '-')}" class="collection-area-col px-3 py-2 border-r border-slate-100 text-left ${isTotal ? 'bg-blue-50' : 'bg-white'}">${escRR(r.nama_area || '-')}</td>
              <td class="px-3 py-2 border-r border-slate-100 text-center font-mono text-blue-700"><button class="font-black hover:underline" onclick='openCollectionAreaDetailRR(${areaCodeJs}, "ALL")'>${fmt(r.target_noa)}</button></td>
              <td class="px-3 py-2 border-r border-slate-100 text-center font-mono text-cyan-700"><button class="font-black hover:underline" onclick='openCollectionAreaDetailRR(${areaCodeJs}, "CALL")'>${fmt(r.call_noa)}</button></td>
              <td class="px-3 py-2 border-r border-slate-100 text-center font-mono ${Number(r.call_percent || 0) >= 80 ? 'text-emerald-700' : (Number(r.call_percent || 0) >= 50 ? 'text-amber-700' : 'text-rose-700')}">${fmtPct(r.call_percent)}</td>
              <td class="px-3 py-2 border-r border-slate-100 text-center font-mono text-slate-700"><button class="font-black hover:underline" onclick='openCollectionAreaDetailRR(${areaCodeJs}, "NC")'>${fmt(r.nc_noa)}</button></td>
              <td class="px-3 py-2 border-r border-slate-100 text-right font-mono">${fmt(r.nc_os)}</td>
              <td class="px-3 py-2 border-r border-slate-100 text-center font-mono text-amber-700"><button class="font-black hover:underline" onclick='openCollectionAreaDetailRR(${areaCodeJs}, "PTP")'>${fmt(r.ptp_noa)}</button></td>
              <td class="px-3 py-2 border-r border-slate-100 text-right font-mono">${fmt(r.ptp_os)}</td>
              <td class="px-3 py-2 border-r border-slate-100 text-center font-mono text-emerald-700"><button class="font-black hover:underline" onclick='openCollectionAreaDetailRR(${areaCodeJs}, "PO")'>${fmt(r.po_noa)}</button></td>
              <td class="px-3 py-2 border-r border-slate-100 text-right font-mono">${fmt(r.po_os)}</td>
              <td class="px-3 py-2 text-right font-mono">${fmt(r.po_bayar)}</td>
            </tr>`;
      };

      tb.innerHTML = `
        ${mainCollectionSummary ? renderRow(mainCollectionSummary, true) : ''}
        ${mainCollectionRows.map(r => renderRow(r)).join('') || `<tr><td colspan="12" class="py-16 text-center text-slate-400 font-bold">Tidak ada data collection.</td></tr>`}
      `;
      updateCollectionStickyOffsetRR();
  }

  window.changeMainCollectionPageRR = function(step) {
      const next = mainCollectionPage + step;
      if (next < 1 || next > mainCollectionTotalPages) return;
      loadMainCollectionRR(next);
  }

  function renderTableRR(rows, gt) {
      const tb = document.getElementById('bodyRR');
      const trTotal = document.getElementById('rowTotalRRAtas');
      const colspan = getMainColspanRR();
      tb.innerHTML = '';
      renderOtpTemplateSummaryRR(gt, 'otp');

      /* Mobile tetap memakai tabel agar struktur laporan konsisten di semua device. */
      setOtpMainSurfaceRR(false);

      if(rows.length === 0){
          tb.innerHTML = `<tr><td colspan="${colspan}" class="py-20 text-center text-slate-400 text-sm">Tidak ada data penagihan.</td></tr>`;
          if (trTotal) trTotal.innerHTML = '';
          return;
      }

      if (isOtpMobile()) {
          if(gt && trTotal) {
              trTotal.innerHTML = `
                  <th class="otp-report-total otp-report-tgl sticky left-0 z-20">TOTAL</th>
                  <th class="otp-report-total">${mobileMetricRR(gt.target_os, gt.target_noa, "initModalDetail('ALL','ALL')")}</th>
                  <th class="otp-report-total">${mobileMetricRR(gt.lancar_os, gt.lancar_noa, "initModalDetail('ALL','LANCAR')")}</th>
                  <th class="otp-report-total">${mobileMetricRR(gt.macet_os, gt.macet_noa, "initModalDetail('ALL','MENUNGGAK')", 'text-rose-700')}</th>
                  <th class="otp-report-total">${mobileMetricRR(gt.lunas_os, gt.lunas_noa, "initModalLunas('ALL')", 'text-emerald-700')}</th>
                  <th class="otp-report-total">${renderDueMetricRR(gt.angsuran_lewat, gt.angsuran_lewat_persen, getDueNoaRR(gt, 'lewat', true), "initModalDetail('ALL','LEWAT_TAGIH')", 'text-rose-700')}</th>
                  <th class="otp-report-total">${renderDueMetricRR(gt.angsuran_sesuai, gt.angsuran_sesuai_persen, getDueNoaRR(gt, 'sesuai', true), "initModalDetail('ALL','SESUAI_TAGIH')", 'text-emerald-700')}</th>
                  <th class="otp-report-total text-right"><a href="javascript:void(0)" onclick="initModalDetail('ALL','ANGSURAN')">${fmt(gt.angsuran)}</a></th>
                  <th class="otp-report-total text-right">${fmt(gt.total_bayar)}</th>
                  <th class="otp-report-total">${renderOtpPctBadge(gt.persen)}</th>
              `;
          }

          tb.innerHTML = rows.map(r => {
              const pctHtml = renderOtpPctBadge(r.persen);
              return `
                  <tr class="otp-report-row">
                      <td class="otp-report-cell otp-report-tgl sticky left-0 z-20">${escRR(r.tgl)}</td>
                      <td class="otp-report-cell">${mobileMetricRR(r.target_os, r.target_noa, `initModalDetail('${escRR(r.tgl)}','ALL')`)}</td>
                      <td class="otp-report-cell">${mobileMetricRR(r.lancar_os, r.lancar_noa, `initModalDetail('${escRR(r.tgl)}','LANCAR')`)}</td>
                      <td class="otp-report-cell">${mobileMetricRR(r.macet_os, r.macet_noa, `initModalDetail('${escRR(r.tgl)}','MENUNGGAK')`, 'text-rose-700')}</td>
                      <td class="otp-report-cell">${mobileMetricRR(r.lunas_os, r.lunas_noa, `initModalLunas('${escRR(r.tgl)}')`, 'text-emerald-700')}</td>
                      <td class="otp-report-cell">${renderDueMetricRR(r.angsuran_lewat, r.angsuran_lewat_persen, getDueNoaRR(r, 'lewat'), `initModalDetail('${escRR(r.tgl)}','LEWAT_TAGIH')`, 'text-rose-700')}</td>
                      <td class="otp-report-cell">${renderDueMetricRR(r.angsuran_sesuai, r.angsuran_sesuai_persen, getDueNoaRR(r, 'sesuai'), `initModalDetail('${escRR(r.tgl)}','SESUAI_TAGIH')`, 'text-emerald-700')}</td>
                      <td class="otp-report-cell text-right"><a href="javascript:void(0)" onclick="initModalDetail('${escRR(r.tgl)}','ANGSURAN')">${fmt(r.angsuran)}</a></td>
                      <td class="otp-report-cell text-right">${fmt(r.total_bayar)}</td>
                      <td class="otp-report-cell">${pctHtml}</td>
                  </tr>
              `;
          }).join('');
          updateOtpStickyOffsetsRR();
          return;
      }

      if(gt && trTotal) {
        trTotal.innerHTML = `
            <th class="otp-report-total otp-report-tgl sticky left-0 z-20">TOTAL</th>
            <th class="otp-report-total text-right"><a href="javascript:void(0)" onclick="initModalDetail('ALL','ALL')">${fmt(gt.target_os)}</a></th>
            <th class="otp-report-total"><a href="javascript:void(0)" onclick="initModalDetail('ALL','ALL')">${fmt(gt.target_noa)}</a></th>
            <th class="otp-report-total text-right"><a href="javascript:void(0)" onclick="initModalDetail('ALL','LANCAR')">${fmt(gt.lancar_os)}</a></th>
            <th class="otp-report-total"><a href="javascript:void(0)" onclick="initModalDetail('ALL','LANCAR')">${fmt(gt.lancar_noa)}</a></th>
            <th class="otp-report-total text-right"><a href="javascript:void(0)" onclick="initModalDetail('ALL','MENUNGGAK')">${fmt(gt.macet_os)}</a></th>
            <th class="otp-report-total"><a href="javascript:void(0)" onclick="initModalDetail('ALL','MENUNGGAK')">${fmt(gt.macet_noa)}</a></th>
            <th class="otp-report-total text-right"><a href="javascript:void(0)" onclick="initModalLunas('ALL')">${fmt(gt.lunas_os)}</a></th>
            <th class="otp-report-total"><a href="javascript:void(0)" onclick="initModalLunas('ALL')">${fmt(gt.lunas_noa)}</a></th>
            <th class="otp-report-total">${renderDueMetricRR(gt.angsuran_lewat, gt.angsuran_lewat_persen, getDueNoaRR(gt, 'lewat', true), "initModalDetail('ALL','LEWAT_TAGIH')", 'text-rose-700')}</th>
            <th class="otp-report-total">${renderDueMetricRR(gt.angsuran_sesuai, gt.angsuran_sesuai_persen, getDueNoaRR(gt, 'sesuai', true), "initModalDetail('ALL','SESUAI_TAGIH')", 'text-emerald-700')}</th>
            <th class="otp-report-total text-right"><a href="javascript:void(0)" onclick="initModalDetail('ALL','ANGSURAN')">${fmt(gt.angsuran)}</a></th>
            <th class="otp-report-total text-right">${fmt(gt.total_bayar)}</th>
            <th class="otp-report-total">${renderOtpPctBadge(gt.persen)}</th>
        `;
      }

      tb.innerHTML = rows.map(r => {
          const pctHtml = renderOtpPctBadge(r.persen);
          return `
            <tr class="otp-report-row">
                <td class="otp-report-cell otp-report-tgl sticky left-0 z-20">${escRR(r.tgl)}</td>
                <td class="otp-report-cell text-right"><a href="javascript:void(0)" onclick="initModalDetail('${escRR(r.tgl)}','ALL')">${fmt(r.target_os)}</a></td>
                <td class="otp-report-cell"><a href="javascript:void(0)" onclick="initModalDetail('${escRR(r.tgl)}','ALL')">${fmt(r.target_noa)}</a></td>
                <td class="otp-report-cell text-right"><a href="javascript:void(0)" onclick="initModalDetail('${escRR(r.tgl)}','LANCAR')">${fmt(r.lancar_os)}</a></td>
                <td class="otp-report-cell"><a href="javascript:void(0)" onclick="initModalDetail('${escRR(r.tgl)}','LANCAR')">${fmt(r.lancar_noa)}</a></td>
                <td class="otp-report-cell text-right"><a href="javascript:void(0)" onclick="initModalDetail('${escRR(r.tgl)}','MENUNGGAK')">${fmt(r.macet_os)}</a></td>
                <td class="otp-report-cell"><a href="javascript:void(0)" onclick="initModalDetail('${escRR(r.tgl)}','MENUNGGAK')">${fmt(r.macet_noa)}</a></td>
                <td class="otp-report-cell text-right"><a href="javascript:void(0)" onclick="initModalLunas('${escRR(r.tgl)}')">${fmt(r.lunas_os)}</a></td>
                <td class="otp-report-cell"><a href="javascript:void(0)" onclick="initModalLunas('${escRR(r.tgl)}')">${fmt(r.lunas_noa)}</a></td>
                <td class="otp-report-cell">${renderDueMetricRR(r.angsuran_lewat, r.angsuran_lewat_persen, getDueNoaRR(r, 'lewat'), `initModalDetail('${escRR(r.tgl)}','LEWAT_TAGIH')`, 'text-rose-700')}</td>
                <td class="otp-report-cell">${renderDueMetricRR(r.angsuran_sesuai, r.angsuran_sesuai_persen, getDueNoaRR(r, 'sesuai'), `initModalDetail('${escRR(r.tgl)}','SESUAI_TAGIH')`, 'text-emerald-700')}</td>
                <td class="otp-report-cell text-right"><a href="javascript:void(0)" onclick="initModalDetail('${escRR(r.tgl)}','ANGSURAN')">${fmt(r.angsuran)}</a></td>
                <td class="otp-report-cell text-right">${fmt(r.total_bayar)}</td>
                <td class="otp-report-cell">${pctHtml}</td>
            </tr>
          `;
      }).join('');
      updateOtpStickyOffsetsRR();
  }

  function createWABtn(phone) {
      if (!phone || phone.trim() === '') return `<span class="text-slate-400 font-mono">-</span>`;
      return `<span class="text-slate-600 font-mono">${phone}</span>`;
  }

  function collectionStatusClassRR(status) {
      const s = String(status || 'NC').toUpperCase();
      if (s === 'PO') return 'otp-status-po';
      if (s === 'PTP') return 'otp-status-ptp';
      return 'otp-status-nc';
  }

  function toggleDetailTabsRR() {
      const tabs = document.getElementById('detailTabsRR');
      const summary = document.getElementById('detailSummaryRR');
      const table = document.getElementById('tableExportRR');
      const cards = document.getElementById('detailCardsRR');
      const summaryBtn = document.getElementById('tabSummaryRR');
      const matrixBtn = document.getElementById('tabMatrixRR');
      const filterDesktop = document.getElementById('collection_filter_rr');
      const filterMobile = document.getElementById('collection_filter_rr_mobile');
      const showTabs = false;

      if (tabs) tabs.classList.toggle('hidden', !showTabs);
      if (filterDesktop) filterDesktop.classList.toggle('hidden', !showTabs);
      if (filterMobile) filterMobile.disabled = !showTabs;
      if (!showTabs) {
          if (summary) summary.classList.add('hidden');
          if (table) table.classList.remove('hidden');
          return;
      }

      const isSummary = detailActiveTab === 'summary';
      if (summaryBtn) summaryBtn.classList.toggle('active', isSummary);
      if (matrixBtn) matrixBtn.classList.toggle('active', !isSummary);
      if (summary) summary.classList.toggle('hidden', !isSummary);
      if (table) table.classList.toggle('hidden', isSummary);
      if (cards) cards.classList.toggle('hidden', isSummary || !isOtpMobile());
  }

  function renderCollectionSummaryRR() {
      const el = document.getElementById('detailSummaryRR');
      if (!el) return;
      const data = detailCollectionSummary || {};
      const configs = [
          { key:'NC', title:'NC', subtitle:'No Contact', tone:'text-slate-600', pill:'otp-status-nc', rule:'Belum ada janji bayar dan belum ada pembayaran.' },
          { key:'PTP', title:'PTP', subtitle:'Promise To Pay', tone:'text-amber-700', pill:'otp-status-ptp', rule:'Ada janji bayar, tetapi pembayaran belum mencapai 80%.' },
          { key:'PO', title:'PO', subtitle:'Paid Off', tone:'text-emerald-700', pill:'otp-status-po', rule:'Pembayaran ada tanpa janji, atau minimal 80% dari janji.' },
          { key:'TOTAL', title:'TOTAL', subtitle:'Semua rekening', tone:'text-blue-700', pill:'bg-blue-100 text-blue-700', rule:'Total mengikuti filter detail yang sedang dibuka.' },
      ];

      el.innerHTML = `
        <div class="otp-summary-grid">
          ${configs.map(c => {
              const item = data[c.key] || { rekening:0, outstanding:0, payment:0 };
              return `
                <article class="otp-summary-card ${c.key !== 'TOTAL' ? 'cursor-pointer hover:border-blue-300 hover:shadow-sm transition' : ''}" ${c.key !== 'TOTAL' ? `onclick="setCollectionFilterRR('${c.key}')"` : ''}>
                  <div class="otp-summary-head">
                    <div>
                      <div class="otp-summary-label ${c.tone}">${c.title}</div>
                      <div class="mt-1 text-[10px] font-bold text-slate-500">${c.subtitle}</div>
                    </div>
                    <span class="otp-status-pill ${c.pill}">${fmt(item.rekening || 0)}</span>
                  </div>
                  <div class="otp-summary-count">${fmt(item.rekening || 0)} Rek</div>
                  <div class="otp-summary-meta">
                    <div class="otp-summary-mini">
                      <span>Outstanding</span>
                      <b>${fmt(item.outstanding || 0)}</b>
                    </div>
                    <div class="otp-summary-mini">
                      <span>Pembayaran</span>
                      <b>${fmt(item.payment || 0)}</b>
                    </div>
                  </div>
                  <div class="otp-summary-rule">${c.rule}</div>
                </article>
              `;
          }).join('')}
        </div>
      `;
  }

  function renderDetailViewRR(list = detailDataCache) {
      toggleDetailTabsRR();
      renderTableDetailBodyRR(list);
  }

  window.setDetailTabRR = function(tab) {
      detailActiveTab = 'matrix';
      renderDetailViewRR(detailDataCache);
  }

  function syncCollectionFilterInputsRR(value) {
      const v = ['CALL', 'NC', 'PTP', 'PO'].includes(String(value || '').toUpperCase()) ? String(value).toUpperCase() : 'ALL';
      const desktop = document.getElementById('collection_filter_rr');
      const mobile = document.getElementById('collection_filter_rr_mobile');
      if (desktop) desktop.value = v;
      if (mobile) mobile.value = v;
      return v;
  }

  window.setCollectionFilterRR = function(value) {
      const v = syncCollectionFilterInputsRR(value);
      currentDetailParams.collection_status = v;
      detailActiveTab = 'matrix';
      loadDetailPage(1);
  }

  window.changeCollectionFilterRR = function() {
      const desktop = document.getElementById('collection_filter_rr');
      const mobile = document.getElementById('collection_filter_rr_mobile');
      const active = document.activeElement === mobile ? mobile : desktop;
      window.setCollectionFilterRR(active?.value || 'ALL');
  }

  function renderModalHeaderRR() {
      const mHead = document.getElementById('headModalRR');
      
      if (currentMode === 'NORMAL') {
          mHead.innerHTML = `
              <tr class="bg-slate-100">
                  <th class="col-rek hidden md:table-cell px-2 md:px-3 py-2 border-b border-r border-slate-200 text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-center">REKENING ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="col-nas px-2 md:px-4 py-2 border-b border-r border-slate-200 text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[60px] md:w-[80px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kode_produk', 'string')">
                      <div class="flex items-center justify-center">PRODUK ${getSortIcon('kode_produk', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[200px] md:w-[320px] text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_hp', 'string')">
                      <div class="flex items-center justify-center">NO HP ${getSortIcon('no_hp', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kankas', 'string')">
                      <div class="flex items-center justify-center">KANKAS ${getSortIcon('kankas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[150px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_jatuh_tempo', 'string')">
                      <div class="flex items-center justify-center">TGL JT ${getSortIcon('tgl_jatuh_tempo', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('jml_pinjaman', 'number')">
                      <div class="flex items-center justify-end">PLAFOND ${getSortIcon('jml_pinjaman', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('os_m1', 'number')">
                      <div class="flex items-center justify-end">TARGET M-1 ${getSortIcon('os_m1', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('os_curr', 'number')">
                      <div class="flex items-center justify-end">BAKI DEBET ${getSortIcon('os_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('totung', 'number')">
                      <div class="flex items-center justify-end">TUNGGAKAN ${getSortIcon('totung', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[50px] md:w-[70px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('dpd_curr', 'number')">
                      <div class="flex items-center justify-center">DPD ${getSortIcon('dpd_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[100px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tabungan', 'number')">
                      <div class="flex items-center justify-end">TABUNGAN ${getSortIcon('tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_tabungan', 'string')">
                      <div class="flex items-center justify-center">STAT TAB ${getSortIcon('status_tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('trx_bulan_lalu', 'number')">
                      <div class="flex items-center justify-end">TRX LALU ${getSortIcon('trx_bulan_lalu', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('trx_bulan_ini', 'number')">
                      <div class="flex items-center justify-end">TRX INI ${getSortIcon('trx_bulan_ini', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nominal_janji', 'number')">
                      <div class="flex items-center justify-end">JANJI BAYAR ${getSortIcon('nominal_janji', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_komitmen', 'string')">
                      <div class="flex items-center justify-center">TGL KOMIT ${getSortIcon('tgl_komitmen', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[70px] md:w-[90px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('collection_status', 'string')">
                      <div class="flex items-center justify-center">COLL ${getSortIcon('collection_status', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-slate-200 w-[100px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_ket', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_ket', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      } else {
          mHead.innerHTML = `
              <tr class="bg-slate-100">
                  <th class="col-nas-lunas px-2 md:px-4 py-2 border-b border-r border-slate-200 text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[200px] md:w-[350px] text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[100px] md:w-[150px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-center">REK LAMA ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('plafon_lama', 'number')">
                      <div class="flex items-center justify-end">PLAFOND LAMA ${getSortIcon('plafon_lama', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('os_lunas', 'number')">
                      <div class="flex items-center justify-end">OS M-1 ${getSortIcon('os_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[80px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_lunas', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('rek_baru', 'string')">
                      <div class="flex items-center justify-center">REK BARU ${getSortIcon('rek_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('plafond_baru', 'number')">
                      <div class="flex items-center justify-end">PLAFOND BARU ${getSortIcon('plafond_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-slate-200 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_baru', 'string')">
                      <div class="flex items-center justify-center">TGL REALISASI ${getSortIcon('tgl_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      }
  }

  window.sortDetailRR = function(col, type) {
      if (!detailDataCache || detailDataCache.length === 0) return;
      detailActiveTab = 'matrix';
      if (sortDetailCol === col) sortDetailAsc = !sortDetailAsc; else { sortDetailCol = col; sortDetailAsc = true; }

      detailDataCache.sort((a, b) => {
          let valA = a[col], valB = b[col];
          if (type === 'number') return sortDetailAsc ? (parseFloat(valA)||0) - (parseFloat(valB)||0) : (parseFloat(valB)||0) - (parseFloat(valA)||0);
          valA = String(valA||'').toLowerCase(); valB = String(valB||'').toLowerCase();
          if (valA < valB) return sortDetailAsc ? -1 : 1;
          if (valA > valB) return sortDetailAsc ? 1 : -1;
          return 0;
      });
      renderModalHeaderRR(); renderDetailViewRR(detailDataCache);
  }

  async function initModalDetail(tgl, status) {
      currentMode = 'NORMAL';
      detailActiveTab = 'matrix';
      detailCollectionSummary = null;
      const branch = document.getElementById('opt_kantor').value || null;
      const subVal = document.getElementById('opt_sub_otp').value;
      const dpdBucket = document.getElementById('opt_dpd_bucket').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;
      const mainAo = document.getElementById('opt_ao_otp')?.value || "";

      let reqKorwil = "", reqKankas = "";
      if (!branch || branch === "000") reqKorwil = subVal;
      else reqKankas = subVal;

      let preselectKankasCode = "";
      if (lblSub === "KANKAS" && subVal !== "") preselectKankasCode = subVal;

      currentDetailParams = { 
          type: 'detail_rr', closing_date: document.getElementById('closing_date').value, harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, korwil: reqKorwil, kode_kankas: preselectKankasCode || reqKankas, kode_ao: mainAo,
          tgl_tagih: tgl, status: status, collection_status: 'ALL', dpd_bucket: dpdBucket, include_127: document.getElementById('chk_127').checked, limit: detailLimit 
      };
      syncCollectionFilterInputsRR('ALL');

      const bucketLabel = getBucketLabel(dpdBucket);
      document.getElementById('modalTitleRR').textContent = `Detail Matriks OTP ${bucketLabel} - Tgl ${tgl}`;
      document.getElementById('modalSubTitleRR').textContent = `Status: ${getStatusLabelRR(status)}`;
      showModalRR();
      
      if(document.getElementById('search_nasabah')) document.getElementById('search_nasabah').value = ''; if(document.getElementById('search_nasabah_mobile')) document.getElementById('search_nasabah_mobile').value = '';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      loadDetailPage(1);
  }

  function getCurrentOtpFilterPayloadRR(type) {
      const branch = document.getElementById('opt_kantor').value || null;
      const subVal = document.getElementById('opt_sub_otp').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;
      const mainAo = document.getElementById('opt_ao_otp')?.value || "";
      let reqKorwil = "", reqKankas = "";
      if (!branch || branch === "000") reqKorwil = subVal;
      else reqKankas = subVal;
      return {
          type,
          closing_date: document.getElementById('closing_date').value,
          harian_date: document.getElementById('harian_date').value,
          kode_kantor: branch,
          korwil: reqKorwil,
          kode_kankas: lblSub === "KANKAS" ? subVal : reqKankas,
          kode_ao: mainAo,
          dpd_bucket: document.getElementById('opt_dpd_bucket').value,
          include_127: document.getElementById('chk_127').checked
      };
  }

  window.openAreaRekapRR = async function() {
      const modal = document.getElementById('modalAreaRekapRR');
      const body = document.getElementById('areaRekapBodyRR');
      const loading = document.getElementById('areaRekapLoadingRR');
      const title = document.getElementById('areaRekapTitleRR');
      const sub = document.getElementById('areaRekapSubRR');
      if (!modal || !body) return;

      modal.classList.remove('hidden');
      document.body.classList.add('otp-modal-open');
      body.innerHTML = `<tr><td colspan="12" class="py-12 text-center text-slate-400 font-bold">Memuat rekap...</td></tr>`;
      loading?.classList.remove('hidden');

      const branch = document.getElementById('opt_kantor').value || '000';
      const subVal = document.getElementById('opt_sub_otp').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;
      title.textContent = branch && branch !== '000' ? 'Rekap Kankas OTP' : 'Rekap Cabang OTP';
      sub.textContent = branch && branch !== '000'
          ? `Breakdown kankas ${document.getElementById('opt_kantor').selectedOptions[0]?.text || branch}`
          : (subVal ? `Filter ${lblSub}: ${subVal}` : 'Konsolidasi semua cabang');

      try {
          const payload = getCurrentOtpFilterPayloadRR('rekap_rr_area');
          const res = await apiCall(API_RR_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          if (!res.ok || res.json.status !== 200) throw new Error(res.json.message || 'Gagal memuat rekap');
          const rows = res.json.data?.data || [];
          const gt = res.json.data?.summary || null;
          const renderRow = (r, total = false) => `
              <tr class="${total ? 'bg-blue-50 font-black text-blue-900' : 'hover:bg-slate-50'}">
                <td class="px-3 py-2 border-r border-slate-100 font-mono ${total ? '' : 'text-slate-500'}">${escRR(r.kode_area || '-')}</td>
                <td class="px-3 py-2 border-r border-slate-100 font-bold text-left">${escRR(r.nama_area || '-')}</td>
                <td class="px-3 py-2 border-r border-slate-100 text-right text-blue-700 font-mono">${fmt(r.target_os)}</td>
                <td class="px-3 py-2 border-r border-slate-100 text-center font-mono">${fmt(r.target_noa)}</td>
                <td class="px-3 py-2 border-r border-slate-100 text-right text-emerald-700 font-mono">${fmt(r.lancar_os)}</td>
                <td class="px-3 py-2 border-r border-slate-100 text-center font-mono">${fmt(r.lancar_noa)}</td>
                <td class="px-3 py-2 border-r border-slate-100 text-right text-rose-700 font-mono">${fmt(r.macet_os)}</td>
                <td class="px-3 py-2 border-r border-slate-100 text-center font-mono">${fmt(r.macet_noa)}</td>
                <td class="px-3 py-2 border-r border-slate-100 text-right text-indigo-700 font-mono">${fmt(r.lunas_os)}</td>
                <td class="px-3 py-2 border-r border-slate-100 text-center font-mono">${fmt(r.lunas_noa)}</td>
                <td class="px-3 py-2 border-r border-slate-100 text-right text-cyan-700 font-mono">${fmt(r.angsuran)}</td>
                <td class="px-3 py-2 text-right font-mono ${Number(r.persen || 0) >= 90 ? 'text-emerald-700' : (Number(r.persen || 0) >= 70 ? 'text-amber-700' : 'text-rose-700')}">${fmtPct(r.persen)}</td>
              </tr>`;
          body.innerHTML = `${gt ? renderRow(gt, true) : ''}${rows.map(r => renderRow(r)).join('')}` || `<tr><td colspan="12" class="py-12 text-center text-slate-400 font-bold">Tidak ada data.</td></tr>`;
      } catch (err) {
          console.error(err);
          body.innerHTML = `<tr><td colspan="12" class="py-12 text-center text-rose-600 font-bold">${escRR(err.message || 'Gagal memuat rekap')}</td></tr>`;
      } finally {
          loading?.classList.add('hidden');
      }
  }

  window.closeAreaRekapRR = function() {
      document.getElementById('modalAreaRekapRR')?.classList.add('hidden');
      if (document.getElementById('modalDetailRR')?.classList.contains('hidden')) {
          document.body.classList.remove('otp-modal-open');
      }
  }

  async function initModalLunas(tgl) {
      currentMode = 'LUNAS';
      detailActiveTab = 'matrix';
      detailCollectionSummary = null;
      const branch = document.getElementById('opt_kantor').value || null;
      const subVal = document.getElementById('opt_sub_otp').value;
      const dpdBucket = document.getElementById('opt_dpd_bucket').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;
      const mainAo = document.getElementById('opt_ao_otp')?.value || "";

      let reqKorwil = "", reqKankas = "";
      if (!branch || branch === "000") reqKorwil = subVal;
      else reqKankas = subVal;

      let preselectKankasCode = "";
      if (lblSub === "KANKAS" && subVal !== "") preselectKankasCode = subVal;

      currentDetailParams = { 
          type: 'detail_lunas_rr', closing_date: document.getElementById('closing_date').value, harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, korwil: reqKorwil, kode_kankas: preselectKankasCode || reqKankas, kode_ao: mainAo,
          tgl_tagih: tgl, dpd_bucket: dpdBucket, include_127: document.getElementById('chk_127').checked, limit: detailLimit 
      };
      syncCollectionFilterInputsRR('ALL');

      const bucketLabel = getBucketLabel(dpdBucket);
      document.getElementById('modalTitleRR').textContent = `Pelunasan OTP ${bucketLabel} - Tgl ${tgl}`;
      document.getElementById('modalSubTitleRR').textContent = `Refinancing & Prospek`;
      showModalRR();
      
      if(document.getElementById('search_nasabah')) document.getElementById('search_nasabah').value = ''; if(document.getElementById('search_nasabah_mobile')) document.getElementById('search_nasabah_mobile').value = '';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      loadDetailPage(1);
  }

  window.filterTableDetail = function() {
      const desktop = document.getElementById('search_nasabah');
      const mobile = document.getElementById('search_nasabah_mobile');
      const active = document.activeElement === mobile ? mobile : desktop;
      const value = (active?.value || '').trim();
      if (desktop && active !== desktop) desktop.value = value;
      if (mobile && active !== mobile) mobile.value = value;

      detailActiveTab = 'matrix';
      currentDetailParams.search = value;
      clearTimeout(detailSearchTimer);
      detailSearchTimer = setTimeout(() => {
          loadDetailPage(1);
      }, 450);
  }

  async function loadDetailPage(page) {
      const l = document.getElementById('loadingModalRR'), tb = document.getElementById('bodyModalRR'), info = document.getElementById('pageInfoRR');
      l.classList.remove('hidden'); tb.innerHTML = '';

      try {
          const searchValue = (currentDetailParams.search || '').trim();
          const payload = { ...currentDetailParams, search: searchValue, page: page };
          const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          
          if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Gagal memuat detail");
          
          detailDataCache = res.json.data?.data || [];
          detailCollectionSummary = res.json.data?.collection_summary || null;
          const meta = res.json.data?.pagination || { total_records:0, total_pages:1 };

          currentDetailPage = page; currentDetailTotalPages = meta.total_pages;

          if(detailDataCache.length === 0) {
              tb.innerHTML = `<tr><td colspan="${getDetailColspanRR()}" class="py-20 text-center text-slate-400 italic text-sm">Tidak ada data detail.</td></tr>`;
              const cards = document.getElementById('detailCardsRR');
              if (cards) cards.innerHTML = `<div class="py-16 text-center text-slate-400 text-xs">Tidak ada data detail.</div>`;
              renderDetailViewRR([]);
              info.innerText = `0 Data`;
          } else {
              sortDetailCol = ''; sortDetailAsc = true;
              renderModalHeaderRR(); renderDetailViewRR(detailDataCache);
              info.innerText = `Hal ${page} dari ${meta.total_pages} (${fmt(meta.total_records)} Data)`;
          }

          document.getElementById('btnPrevRR').disabled = page <= 1;
          document.getElementById('btnNextRR').disabled = page >= meta.total_pages;
      } catch(err){ 
          console.error(err); 
          tb.innerHTML = `<tr><td colspan="${getDetailColspanRR()}" class="py-16 text-center text-rose-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">Gagal memuat detail</td></tr>`;
          const cards = document.getElementById('detailCardsRR');
          if (cards) cards.innerHTML = `<div class="py-16 text-center text-rose-500 text-xs font-bold">Gagal memuat detail.</div>`;
      } finally { l.classList.add('hidden'); }
  }

  function renderTableDetailBodyRR(list) {
      const tb = document.getElementById('bodyModalRR');
      const cards = document.getElementById('detailCardsRR');
      let h = '';
      let mobileHtml = '';

      const moneyItem = (label, value, tone = '') => `
          <div class="otp-detail-item">
              <div class="otp-detail-label">${label}</div>
              <div class="otp-detail-value ${tone}">${fmt(value)}</div>
          </div>`;

      list.forEach(r => {
          const aoName = (r.nama_ao || '-').split(' ').slice(0, 2).join(' ');
          const safeName = escRR(r.nama_nasabah || '-');
          const safeAddress = escRR(r.alamat || '-');

          if(currentMode === 'NORMAL') {
              const btnWa = createWABtn(r.no_hp);
              const status = String(r.status_ket || '-').toUpperCase();
              const statusClass = status === 'LANCAR'
                  ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                  : (status === 'MENUNGGAK'
                      ? 'bg-rose-50 text-rose-700 border border-rose-200'
                      : 'bg-slate-100 text-slate-600 border border-slate-200');
              const tabClass = r.status_tabungan === 'Aman' ? 'text-emerald-700' : 'text-rose-600';
              const collStatus = String(r.collection_status || 'NC').toUpperCase();
              const collBadge = `<span class="otp-status-pill ${collectionStatusClassRR(collStatus)}">${escRR(collStatus)}</span>`;

              h += `<tr class="transition border-b border-slate-100 hover:bg-slate-50 h-[44px] md:h-[48px]">
                    <td class="col-rek hidden md:table-cell px-2 md:px-3 py-1.5 border-r border-slate-100 font-mono text-slate-500">${escRR(r.no_rekening)}</td>
                    <td class="col-nas px-2 md:px-4 py-1.5 border-r border-slate-100 truncate" title="${safeName}">${safeName}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono text-slate-500">${escRR(r.kode_produk || '-')}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-slate-500 truncate max-w-[200px] md:max-w-[320px]" title="${safeAddress}">${safeAddress}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center">${btnWa}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono">${escRR(r.kankas || '-')}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-center truncate">${escRR(aoName)}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono text-slate-500">${escRR(r.tgl_jatuh_tempo || '-')}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">${fmt(r.jml_pinjaman)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.os_m1)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.os_curr)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.totung)}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center text-slate-600">${escRR(r.dpd_curr)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">${fmt(r.tabungan)}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center"><span class="font-semibold ${tabClass}">${escRR(r.status_tabungan || '-')}</span></td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600"><div>${fmt(r.trx_bulan_lalu)}</div><div class="text-[9px] text-slate-400 font-mono">${Number(r.trx_bulan_lalu || 0) > 0 ? fmtDateID(r.tgl_bayar_lalu) : ''}</div></td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600"><div>${fmt(r.trx_bulan_ini)}</div><div class="text-[9px] text-slate-400 font-mono">${Number(r.trx_bulan_ini || 0) > 0 ? fmtDateID(r.tgl_bayar_ini) : ''}</div></td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-amber-700"><div>${fmt(r.nominal_janji)}</div><div class="text-[9px] text-slate-400 font-mono">${Number(r.nominal_janji || 0) > 0 ? `Pokok ${fmt(r.janji_pokok)} | Bunga ${fmt(r.janji_bunga)}` : ''}</div></td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono text-slate-500" title="${escRR(r.komit_keterangan || '')}">${fmtDateID(r.tgl_komitmen)}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center">${collBadge}</td>
                    <td class="px-2 md:px-4 py-1.5 text-center font-semibold ${status === 'LANCAR' ? 'text-emerald-600' : (status === 'MENUNGGAK' ? 'text-rose-600' : 'text-slate-500')}">${escRR(status)}</td>
                </tr>`;

              mobileHtml += `
                <article class="otp-detail-card">
                  <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                      <div class="otp-detail-title">${safeName}</div>
                      <div class="otp-detail-sub">${escRR(r.no_rekening || '-')}</div>
                    </div>
                    <span class="otp-detail-badge ${statusClass}">${escRR(status)}</span>
                  </div>
                  <div class="otp-detail-meta">
                    <span class="otp-detail-chip">Produk ${escRR(r.kode_produk || '-')}</span>
                    <span class="otp-detail-chip">DPD ${escRR(r.dpd_curr ?? '-')}</span>
                    <span class="otp-detail-chip">JT ${escRR(r.tgl_jatuh_tempo || '-')}</span>
                    <span class="otp-detail-chip">AO ${escRR(aoName)}</span>
                    <span class="otp-detail-chip">Kankas ${escRR(r.kankas || '-')}</span>
                    <span class="otp-detail-chip">Coll ${escRR(collStatus)}</span>
                  </div>
                  <div class="otp-detail-grid">
                    ${moneyItem('Plafond', r.jml_pinjaman)}
                    ${moneyItem('Target M-1', r.os_m1)}
                    ${moneyItem('Baki Debet', r.os_curr, 'text-blue-700')}
                    ${moneyItem('Tunggakan', r.totung, 'text-rose-700')}
                    ${moneyItem('Tabungan', r.tabungan, tabClass)}
                    ${moneyItem('Trx Bulan Ini', r.trx_bulan_ini, 'text-emerald-700')}
                    ${moneyItem('Janji Bayar', r.nominal_janji, 'text-amber-700')}
                    <div class="otp-detail-item"><div class="otp-detail-label">Status Collection</div><div class="otp-detail-value">${collBadge}</div></div>
                  </div>
                  <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-2 text-[9px] leading-relaxed text-slate-600">
                    <div><b>Alamat:</b> ${safeAddress}</div>
                    <div class="mt-1"><b>No. HP:</b> ${escRR(r.no_hp || '-')} &nbsp; <b>Status Tab:</b> <span class="${tabClass}">${escRR(r.status_tabungan || '-')}</span></div>
                    <div class="mt-1"><b>Trx lalu:</b> ${fmt(r.trx_bulan_lalu)} ${Number(r.trx_bulan_lalu || 0) > 0 ? `(${fmtDateID(r.tgl_bayar_lalu)})` : ''}</div>
                    <div class="mt-1"><b>Komitmen:</b> ${fmtDateID(r.tgl_komitmen)} ${r.komit_keterangan ? `- ${escRR(r.komit_keterangan)}` : ''}</div>
                  </div>
                </article>`;
          } else {
              const alamatLengkap = r.alamat || '-';
              const isRefi = r.status_lunas === 'REFINANCING / Top Up';
              const statusLabel = isRefi ? 'REFINANCING' : 'PROSPEK';
              const badgeClass = isRefi
                  ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                  : 'bg-blue-50 text-blue-700 border border-blue-200';
              const badge = `<span class="text-xs font-semibold ${isRefi ? 'text-emerald-600' : 'text-blue-600'}">${statusLabel}</span>`;

              h += `<tr class="transition border-b border-slate-100 hover:bg-slate-50 h-[44px] md:h-[48px]">
                    <td class="col-nas-lunas px-2 md:px-4 py-1.5 border-r border-slate-100 truncate">${safeName}<div class="text-[9px] text-slate-400 font-mono font-normal">ID: ${escRR(r.nasabah_id)}</div></td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-slate-500 truncate max-w-[200px] md:max-w-[350px]" title="${safeAddress}">${safeAddress}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-center truncate">${escRR(aoName)}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 font-mono text-center text-slate-500">${escRR(r.no_rekening)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">${fmt(r.plafon_lama)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.os_lunas)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-center">${badge}</td>
                    <td class="px-2 md:px-3 border-r border-slate-100 font-mono text-center text-emerald-600">${escRR(r.rek_baru || '-')}</td>
                    <td class="px-2 md:px-4 border-r border-slate-100 text-right text-emerald-600">${fmt(r.plafond_baru)}</td>
                    <td class="px-2 md:px-3 text-center text-slate-500">${escRR(r.tgl_baru || '-')}</td>
                </tr>`;

              mobileHtml += `
                <article class="otp-detail-card">
                  <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                      <div class="otp-detail-title">${safeName}</div>
                      <div class="otp-detail-sub">ID ${escRR(r.nasabah_id || '-')}</div>
                    </div>
                    <span class="otp-detail-badge ${badgeClass}">${statusLabel}</span>
                  </div>
                  <div class="otp-detail-meta">
                    <span class="otp-detail-chip">AO ${escRR(aoName)}</span>
                    <span class="otp-detail-chip">Rek lama ${escRR(r.no_rekening || '-')}</span>
                    <span class="otp-detail-chip">Tgl baru ${escRR(r.tgl_baru || '-')}</span>
                  </div>
                  <div class="otp-detail-grid">
                    ${moneyItem('Plafond Lama', r.plafon_lama)}
                    ${moneyItem('OS M-1', r.os_lunas)}
                    <div class="otp-detail-item"><div class="otp-detail-label">Rekening Baru</div><div class="otp-detail-value text-emerald-700">${escRR(r.rek_baru || '-')}</div></div>
                    ${moneyItem('Plafond Baru', r.plafond_baru, 'text-emerald-700')}
                  </div>
                  <div class="mt-2 rounded-lg border border-slate-200 bg-slate-50 p-2 text-[9px] leading-relaxed text-slate-600"><b>Alamat:</b> ${safeAddress}</div>
                </article>`;
          }
      });

      tb.innerHTML = h || `<tr><td colspan="${getDetailColspanRR()}" class="py-16 text-center text-slate-400">Tidak ada data.</td></tr>`;
      if (cards) cards.innerHTML = mobileHtml || `<div class="py-16 text-center text-slate-400 text-xs">Tidak ada data.</div>`;
  }

  async function downloadExcelFull(ev) {
      const btn = (ev?.target || window.event?.target)?.closest('button'); if(!btn) return; const txt = btn.innerHTML;
      btn.innerHTML = `<span class="animate-spin inline-block h-3.5 w-3.5 border-2 border-white border-t-transparent rounded-full md:mr-2"></span><span class="hidden md:inline">...</span>`;
      btn.disabled = true;

      try {
          const payload = { ...currentDetailParams, page: 1, limit: 10000 };
          const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Export gagal");
          
          const rows = res.json.data?.data || [];
          if(rows.length === 0) { alert("Tidak ada data untuk diexport"); return; }

          let csv = "";
          if(currentMode === 'NORMAL') {
              csv = `No Rekening\tNama Nasabah\tKode Produk\tAlamat\tNo HP\tKankas\tNama AO\tTgl JT\tPlafond\tTarget (M-1)\tBaki Debet Actual\tTot Tunggakan\tDPD\tSaldo Tabungan\tStatus Tabungan\tTrx Bulan Lalu\tTgl Bayar Lalu\tTrx Bulan Ini\tTgl Bayar Ini\tJanji Pokok\tJanji Bunga\tJanji Bayar\tTgl Komitmen\tKeterangan Komitmen\tStatus Collection\tStatus Tagih\n`;
              rows.forEach(r => {
                  csv += `'${r.no_rekening}\t${r.nama_nasabah}\t${r.kode_produk||''}\t${r.alamat||''}\t'${r.no_hp||''}\t${r.kankas||''}\t${r.nama_ao}\t${r.tgl_jatuh_tempo}\t${Math.round(r.jml_pinjaman)}\t${Math.round(r.os_m1)}\t${Math.round(r.os_curr)}\t${Math.round(r.totung)}\t${r.dpd_curr}\t${Math.round(r.tabungan)}\t${r.status_tabungan}\t${Math.round(r.trx_bulan_lalu||0)}\t${fmtDateID(r.tgl_bayar_lalu)}\t${Math.round(r.trx_bulan_ini||0)}\t${fmtDateID(r.tgl_bayar_ini)}\t${Math.round(r.janji_pokok||0)}\t${Math.round(r.janji_bunga||0)}\t${Math.round(r.nominal_janji||0)}\t${fmtDateID(r.tgl_komitmen)}\t${r.komit_keterangan||''}\t${r.collection_status||'NC'}\t${r.status_ket}\n`;
              });
          } else {
              csv = `Nama Nasabah\tID Nasabah\tAlamat\tNama AO\tRek Lama\tPlafond Lama\tOS Lunas (M-1)\tStatus\tRek Baru\tPlafond Baru\tTgl Realisasi Baru\n`;
              rows.forEach(r => {
                  csv += `${r.nama_nasabah}\t'${r.nasabah_id}\t${r.alamat||''}\t${r.nama_ao}\t'${r.no_rekening}\t${Math.round(r.plafon_lama)}\t${Math.round(r.os_lunas)}\t${r.status_lunas}\t'${r.rek_baru}\t${Math.round(r.plafond_baru)}\t${r.tgl_baru}\n`;
              });
          }

          const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a'); a.href = url; a.download = `RR_Detail_${currentMode}_${currentDetailParams.tgl_tagih}.xls`;
          document.body.appendChild(a); a.click(); document.body.removeChild(a);

      } catch(e) { console.error(e); alert("Gagal export data."); } 
      finally { btn.innerHTML = txt; btn.disabled = false; }
  }

  window.changePageDetail = (step) => { const n = currentDetailPage + step; if (n > 0 && n <= currentDetailTotalPages) loadDetailPage(n); }
  window.closeModalRR = () => {
      document.getElementById('modalDetailRR')?.classList.add('hidden');
      document.body.classList.remove('otp-modal-open');
  };
  document.addEventListener('keydown', e => { if(e.key === 'Escape') { setOtpHelpOpen(false); closeModalRR(); } });


  document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && document.getElementById('otpCclAccessModal')?.classList.contains('is-open')) {
          event.preventDefault();
          closeCclAccessModalRR();
      }
  });

</script>



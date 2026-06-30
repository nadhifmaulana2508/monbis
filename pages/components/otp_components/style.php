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
  .otp-info-root:hover .otp-help-panel { display:flex !important; }

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
</style>
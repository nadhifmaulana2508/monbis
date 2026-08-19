<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  /* Animasi Modal */
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* ========================================================
     🔥 MAGIC STICKY TABLE (FIX OVERLAP BAKI DEBET & NAMA) 🔥
     ======================================================== */
  
  /* Hindari background tembus pandang di Safari/Webkit Chrome */
  #tabelRR th, #tabelRR td, #tableExportRR th, #tableExportRR td { background-clip: padding-box; } 

  /* 1. Kunci Tinggi dan Posisi Header Tabel Utama */
  #tabelRR thead th { position: sticky !important; z-index: 40; cursor: pointer; transition: background 0.2s; }
  #tabelRR thead th:hover { filter: brightness(0.95); }
  
  .rr-row-1 th { top: 0 !important; height: 36px; box-shadow: inset 0 -1px 0 #d7dee8; }
  .rr-row-2 th { top: 36px !important; height: 34px; box-shadow: inset 0 -1px 0 #d7dee8; }
  .rr-row-tot th { top: 70px !important; height: 42px; box-shadow: inset 0 -1px 0 #d7dee8; z-index: 42 !important; cursor: default; }
  .rr-row-tot th:hover { filter: none; }
  
  @media (min-width: 768px) {
      .rr-row-1 th { height: 44px; }
      .rr-row-2 th { top: 44px !important; height: 40px; }
      .rr-row-tot th { top: 84px !important; height: 48px; }
  }

  /* 2. Kunci Posisi Kiri Sticky (Untuk NAMA KANTOR & KODE) */
  .sticky-left-1 { position: sticky !important; left: 0 !important; }
  .sticky-left-2 { position: sticky !important; left: 0 !important; }
  @media (min-width: 768px) { .sticky-left-2 { left: 80px !important; } }

  /* 3. Tumpukan Z-Index Perpotongan Kiri & Atas (Harus Paling Atas) */
  #tabelRR thead th.sticky-left-1, #tabelRR thead th.sticky-left-2 { z-index: 60 !important; background-color: #dcedc8 !important; }
  #tabelRR thead tr.rr-row-tot th.sticky-left-1, #tabelRR thead tr.rr-row-tot th.sticky-left-2 { z-index: 62 !important; background-color: #eff6ff !important; box-shadow: inset -1px 0 0 #d7dee8, inset 0 -1px 0 #d7dee8; }

  /* 4. Tumpukan Z-Index Data Kiri (Harus Di Atas Data Scroll) */
  #tabelRR tbody td.sticky-left-1, #tabelRR tbody td.sticky-left-2 { 
      z-index: 30 !important; 
      background-color: #ffffff !important; 
      box-shadow: inset -1px 0 0 #e2e8f0; 
  }
  
  /* Hover Effect Tabel Utama */
  #bodyRekap tr:hover td { background-color: #f8fafc !important; }
  #bodyRekap tr:hover td.sticky-left-1, #bodyRekap tr:hover td.sticky-left-2 { background-color: #f8fafc !important; }

  /* ========================================================
     🔥 TABEL MODAL DETAIL RR 🔥
     ======================================================== */
  #tableExportRR th { height: 46px; background-color: #f1f5f9 !important; box-shadow: inset 0 -1px 0 #cbd5e1; top: 0 !important; position: sticky !important; z-index: 40; cursor: pointer; transition: background 0.2s; }
  #tableExportRR th:hover { background-color: #e2e8f0 !important; }
  @media (min-width: 768px) { #tableExportRR th { height: 48px; } }

  /* Kunci Lebar Modal Sticky (Responsif Hide Rekening) */
  .mod-freeze-rek, .mod-td-rekening { position: sticky !important; left: 0 !important; min-width: 100px; max-width: 100px;}
  .mod-freeze-nas, .mod-td-nasabah { position: sticky !important; left: 0 !important; min-width: 160px; max-width: 160px;}
  @media (min-width: 768px) { 
      .mod-freeze-rek, .mod-td-rekening { min-width: 120px; max-width: 120px; }
      .mod-freeze-nas, .mod-td-nasabah { left: 120px !important; min-width: 250px; max-width: 250px;} 
  }

  /* Z-Index Hierarchy Modal */
  #tableExportRR th.mod-freeze-rek, #tableExportRR th.mod-freeze-nas { z-index: 60 !important; background-color: #f1f5f9 !important; }
  #tableExportRR tbody td.mod-td-rekening, #tableExportRR tbody td.mod-td-nasabah { z-index: 30 !important; background-color: #ffffff !important; box-shadow: inset -1px 0 0 #e2e8f0; }

  /* Hover Effect Modal Detail */
  #bodyModalRR tr:hover td { background-color: #f8fafc !important; }
  #bodyModalRR tr:hover td.mod-td-rekening, #bodyModalRR tr:hover td.mod-td-nasabah { background-color: #f8fafc !important; }

  /* Form Inputs */
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; background:#fff; outline:none; transition: border 0.2s;}
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f1f5f9; color: #64748b; font-weight: 700; cursor: not-allowed; }
  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap;}
  @media (min-width: 768px) { .lbl { font-size:11px; margin-bottom:4px; } .inp { border-radius: 8px; padding:0 12px; } }
  .field { display:flex; flex-direction:column; }
  
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }

  /* HIDE DATEPICKER ICON */
  input[type="date"]::-webkit-inner-spin-button, input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
  input[type="date"] { -moz-appearance: textfield; }

  .rr-header-card { background:#fff; border:1px solid #cbd5e1; border-radius:14px; box-shadow:0 1px 2px rgba(15,23,42,.05); }
  .rr-info-popover { position:absolute; left:20px; top:72px; width:360px; max-width:calc(100vw - 32px); background:#fff; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 20px 40px rgba(15,23,42,.16); z-index:200; overflow:hidden; }
  .rr-info-box { border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; padding:10px 12px; }
  @media (max-width: 767px) {
    .rr-info-popover { left:12px; top:62px; width:calc(100vw - 24px); }
  }


  /* Header RR: samakan ketebalan garis header */
  #tabelRR thead th { border-color:#d7dee8 !important; }
  #tabelRR .rr-row-1 th,
  #tabelRR .rr-row-2 th,
  #tabelRR .rr-row-tot th {
      box-shadow: inset 0 -1px 0 #d7dee8 !important;
      border-bottom-width: 1px !important;
  }
  #tabelRR thead tr.rr-row-tot th.sticky-left-1,
  #tabelRR thead tr.rr-row-tot th.sticky-left-2 {
      box-shadow: inset -1px 0 0 #d7dee8, inset 0 -1px 0 #d7dee8 !important;
  }



  /* ========================================================
     RR UI V2 - KONSISTEN DENGAN PAGE MONBIS LAIN
     ======================================================== */
  #rrPage {
    width:100%; max-width:none !important; min-height:0;
    padding:10px 14px 12px !important; gap:10px;
  }
  #rrPage > .flex-none { margin-bottom:0 !important; }
  #rrHeaderCard {
    padding:9px 11px !important; border-color:#e2e8f0;
    border-radius:12px; box-shadow:0 1px 3px rgba(15,23,42,.05);
  }
  #rrHeaderCard > div:first-child { gap:10px !important; }
  #rrHeaderCard .bg-blue-600 { box-shadow:none !important; }
  #rrHeaderTitle { font-size:17px !important; color:#172033 !important; letter-spacing:-.015em; }
  #rrHeaderSubtitle { max-width:290px; font-size:9px !important; color:#64748b; font-style:normal !important; }

  .rr-info-button {
    display:inline-flex; align-items:center; justify-content:center;
    width:20px; min-width:20px; height:20px; padding:0;
    border:1px solid #bfdbfe; border-radius:999px;
    background:#eff6ff; color:#2563eb; font-size:11px; font-weight:900;
    cursor:pointer; transition:.16s ease; flex:0 0 auto;
  }
  .rr-info-button:hover,.rr-info-button[aria-expanded="true"] { background:#2563eb; color:#fff; border-color:#2563eb; }

  @media (min-width:1280px) {
    #rrHeaderCard > div:first-child { flex-wrap:nowrap !important; }
    #filterWrapperMain { display:flex !important; flex:1 1 auto; justify-content:flex-end; min-width:0; }
    #formFilter { width:auto !important; }
    #formFilter > div { flex-wrap:nowrap !important; gap:7px !important; }
    #formFilter .field { flex:none !important; min-width:0 !important; }
    #formFilter .field:nth-child(1), #formFilter .field:nth-child(2) { width:116px; }
    #formFilter .field:nth-child(3) { width:122px; }
    #formFilter .field:nth-child(4) { width:220px; }
    #formFilter .inp { height:34px !important; font-size:10px !important; border-radius:8px; background:#fff !important; }
    #formFilter .lbl { font-size:7.5px !important; margin-bottom:3px; }
    #formFilter .btn-icon { width:34px !important; height:34px !important; border-radius:9px; }
  }

  /* Table utama: soft blue, bold, nyaman dibaca */
  #tabelRR { min-width:1020px; font-size:11px; }
  #tabelRR .rr-col-noa { width:72px; min-width:72px; max-width:72px; text-align:center; }
  #tabelRR thead th { color:#1e3a5f !important; font-weight:900 !important; letter-spacing:.025em; }
  #tabelRR thead tr.rr-row-1 th { background:#eaf4ff !important; }
  #tabelRR thead tr.rr-row-2 th { background:#f4f7fb !important; }
  #tabelRR thead th.sticky-left-1,#tabelRR thead th.sticky-left-2 { background:#eaf4ff !important; }
  #tabelRR tbody td { color:#334155; font-variant-numeric:tabular-nums; }
  #bodyRekap tr { height:44px !important; }

  /* ===== INFO MODAL ===== */
  .rr-info-backdrop {
    position:fixed; inset:0; z-index:99990; display:none;
    background:rgba(15,23,42,.58); backdrop-filter:blur(5px);
  }
  .rr-info-backdrop.open { display:block; }
  .rr-info-panel {
    position:fixed; left:50%; top:50%; z-index:100000; display:none;
    width:min(820px,calc(100vw - 44px)); max-height:min(88dvh,760px);
    transform:translate(-50%,-50%); overflow:auto;
    border:1px solid #dbe3ee; border-radius:15px; background:#fff;
    box-shadow:0 28px 70px rgba(15,23,42,.28);
  }
  .rr-info-panel.open { display:block; }
  .rr-info-head {
    position:sticky; top:0; z-index:4; display:flex; align-items:flex-start; justify-content:space-between;
    gap:12px; padding:13px 15px 11px; border-bottom:1px solid #e2e8f0;
    background:rgba(255,255,255,.97); backdrop-filter:blur(8px);
  }
  .rr-info-title { margin:0; color:#172033; font-size:15px; font-weight:900; line-height:1.2; }
  .rr-info-subtitle { margin:3px 0 0; color:#64748b; font-size:9.5px; font-weight:650; line-height:1.4; }
  .rr-info-close { width:30px; min-width:30px; height:30px; border:1px solid #e2e8f0; border-radius:8px; background:#f8fafc; color:#64748b; font-size:18px; cursor:pointer; }
  .rr-info-close:hover { background:#fee2e2; color:#dc2626; border-color:#fecaca; }
  .rr-info-body { padding:13px 15px 16px; }
  .rr-info-context { display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:8px; }
  .rr-info-mode { padding:5px 8px; border:1px solid #bfdbfe; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:8px; font-weight:900; text-transform:uppercase; }
  .rr-info-date { overflow:hidden; color:#64748b; font-size:8.5px; font-weight:750; text-overflow:ellipsis; white-space:nowrap; }
  .rr-insight-hero { padding:11px 12px; border:1px solid #dbeafe; border-radius:11px; background:#f8fbff; }
  .rr-insight-hero.good { border-color:#bbf7d0; background:#f4fdf7; }
  .rr-insight-hero.alert { border-color:#fecaca; background:#fff7f7; }
  .rr-insight-hero.warn { border-color:#fde68a; background:#fffbeb; }
  .rr-insight-eyebrow { color:#64748b; font-size:7.5px; font-weight:900; letter-spacing:.06em; text-transform:uppercase; }
  .rr-insight-headline { margin-top:3px; color:#0f172a; font-size:12px; font-weight:900; line-height:1.35; }
  .rr-insight-copy { margin-top:5px; color:#475569; font-size:9.5px; font-weight:650; line-height:1.5; }
  .rr-insight-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:7px; margin-top:8px; }
  .rr-insight-stat { padding:8px 9px; border:1px solid #e2e8f0; border-radius:9px; background:#fff; min-width:0; }
  .rr-insight-stat-label { color:#64748b; font-size:7px; font-weight:900; text-transform:uppercase; letter-spacing:.035em; }
  .rr-insight-stat-value { margin-top:3px; color:#0f172a; font-size:12px; font-weight:900; font-variant-numeric:tabular-nums; }
  .rr-insight-stat-value.bad { color:#dc2626; } .rr-insight-stat-value.good { color:#059669; }
  .rr-insight-section { margin-top:9px; padding:10px; border:1px solid #e2e8f0; border-radius:10px; background:#fff; }
  .rr-section-head { display:flex; justify-content:space-between; gap:8px; margin-bottom:8px; color:#1e293b; font-size:10px; font-weight:900; }
  .rr-section-head span:last-child { color:#94a3b8; font-size:7.5px; font-weight:750; }
  .rr-action-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; }
  .rr-action-card { display:grid; grid-template-columns:26px minmax(0,1fr); gap:8px; padding:9px; border:1px solid #e2e8f0; border-left-width:4px; border-radius:9px; background:#f8fafc; }
  .rr-action-card b { color:#334155; font-size:9.5px; font-weight:900; }
  .rr-action-card p { margin:3px 0 0; color:#64748b; font-size:8.5px; font-weight:650; line-height:1.45; }
  .rr-action-num { display:flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:7px; background:#e2e8f0; font-size:9px; font-weight:900; }
  .rr-action-danger { border-left-color:#e11d48; background:#fff9fa; } .rr-action-danger .rr-action-num { background:#ffe4e6; color:#be123c; }
  .rr-action-warn { border-left-color:#f59e0b; background:#fffdf7; } .rr-action-warn .rr-action-num { background:#fef3c7; color:#b45309; }
  .rr-action-info { border-left-color:#2563eb; background:#f8fbff; } .rr-action-info .rr-action-num { background:#dbeafe; color:#1d4ed8; }
  .rr-action-neutral { border-left-color:#64748b; }
  .rr-driver-list { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:7px; }
  .rr-driver-item { display:grid; grid-template-columns:22px minmax(0,1fr) auto; gap:6px; align-items:center; padding:7px; border:1px solid #eef2f7; border-radius:8px; background:#f8fafc; min-width:0; }
  .rr-driver-rank { display:flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:6px; background:#fee2e2; color:#b91c1c; font-size:8px; font-weight:900; }
  .rr-driver-name { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#334155; font-size:9px; font-weight:900; }
  .rr-driver-meta { margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#94a3b8; font-size:7px; font-weight:700; }
  .rr-driver-item strong { color:#dc2626; font-size:8.5px; white-space:nowrap; }
  .rr-driver-empty { grid-column:1/-1; padding:10px; border:1px dashed #cbd5e1; border-radius:8px; color:#64748b; background:#f8fafc; font-size:9px; text-align:center; }
  .rr-definition-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:7px; }
  .rr-definition-grid > div { padding:8px; border:1px solid #eef2f7; border-radius:8px; background:#f8fafc; }
  .rr-definition-grid b { display:block; color:#334155; font-size:9px; font-weight:900; }
  .rr-definition-grid span { display:block; margin-top:2px; color:#64748b; font-size:8px; line-height:1.4; }
  .rr-info-footnote { margin-top:8px; padding:8px 9px; border:1px solid #fde68a; border-radius:9px; background:#fffbeb; color:#92400e; font-size:8px; font-weight:700; line-height:1.45; }
  .rr-info-lock { overflow:hidden !important; }

  /* Detail modal: proporsional di desktop, bottom sheet di mobile */
  #modalDetailRR > .relative.bg-white { width:min(1380px,calc(100vw - 28px)) !important; height:min(88dvh,820px) !important; border:1px solid rgba(226,232,240,.9); border-radius:14px !important; }
  #modalDetailRR #modal-title-container { min-width:0 !important; }
  #modalDetailRR #modalTitleRR { font-size:15px; }
  #modalDetailRR #modalSubTitleRR { font-size:9px; }
  #modalFilterWrapper > div { flex-wrap:wrap; overflow:visible !important; }
  #modalFilterWrapper select { width:auto !important; min-width:118px; max-width:170px; }
  #tableExportRR { font-size:10.5px; }
  #tableExportRR th { height:38px !important; font-size:9px !important; color:#475569; }
  #bodyModalRR tr { height:38px !important; }
  #bodyModalRR td { font-size:10px !important; }

  @media (min-width:768px) and (max-width:1279px) {
    #rrPage { padding:8px !important; }
    #rrHeaderCard { padding:8px 9px !important; }
    #filterWrapperMain.flex { display:block !important; width:100%; padding-top:8px; border-top:1px solid #e2e8f0; }
    #formFilter > div { display:grid !important; grid-template-columns:repeat(2,minmax(0,1fr)) 42px; gap:7px !important; }
    #formFilter .field:nth-child(4) { grid-column:1/3; }
  }

  @media (max-width:767px) {
    #rrPage { height:calc(100dvh - 56px) !important; padding:5px !important; gap:5px !important; }
    #rrHeaderCard { padding:6px 7px !important; border-radius:9px !important; }
    #rrHeaderCard > div:first-child { gap:5px !important; }
    #rrHeaderCard .p-1\.5 { padding:5px !important; border-radius:7px !important; }
    #rrHeaderCard .p-1\.5 svg { width:14px !important; height:14px !important; }
    #rrHeaderTitle { font-size:11.5px !important; }
    #rrHeaderSubtitle { max-width:170px; font-size:6.7px !important; margin-top:2px !important; }
    .rr-info-button { width:18px; min-width:18px; height:18px; font-size:9px; }
    #rrFilterToggle { height:28px !important; padding:0 8px !important; font-size:8px !important; border-radius:7px !important; }
    #filterWrapperMain.flex { display:block !important; width:100%; padding-top:5px; border-top:1px solid #e2e8f0; }
    #formFilter { width:100% !important; }
    #formFilter > div { display:grid !important; grid-template-columns:repeat(2,minmax(0,1fr)) 32px; gap:4px !important; width:100% !important; }
    #formFilter .field { min-width:0 !important; width:auto !important; }
    #formFilter .field:nth-child(1) { grid-column:1; }
    #formFilter .field:nth-child(2) { grid-column:2; }
    #formFilter .field:nth-child(3) { grid-column:1; }
    #formFilter .field:nth-child(4) { grid-column:2; }
    #formFilter .btn-icon { grid-column:3; grid-row:2; width:29px !important; height:29px !important; align-self:end; border-radius:7px !important; }
    #formFilter .inp { height:28px !important; min-width:0 !important; padding:0 5px !important; border-radius:6px !important; font-size:7.8px !important; }
    #formFilter .lbl { font-size:6.2px !important; margin-bottom:1px !important; }

    #tabelRR { min-width:760px !important; font-size:8px !important; }
    #tabelRR .rr-col-noa { width:46px !important; min-width:46px !important; max-width:46px !important; padding-left:2px !important; padding-right:2px !important; }
    .rr-row-1 th { height:27px !important; font-size:6.7px !important; }
    .rr-row-2 th { top:27px !important; height:26px !important; font-size:6.2px !important; }
    .rr-row-tot th { top:53px !important; height:31px !important; font-size:7.3px !important; }
    #tabelRR thead th { padding:3px !important; line-height:1.05 !important; }
    #bodyRekap tr { height:31px !important; }
    #bodyRekap td { padding:3px 4px !important; font-size:7.5px !important; line-height:1.05 !important; }
    #bodyRekap td div { font-size:7.5px !important; }
    #bodyRekap td div + div { font-size:5.8px !important; margin-top:1px !important; }
    .sticky-left-2 { min-width:94px !important; max-width:94px !important; width:94px !important; }

    .rr-info-panel { left:0; right:0; top:auto; bottom:0; width:100%; max-height:88dvh; transform:none; border-left:0; border-right:0; border-bottom:0; border-radius:16px 16px 0 0; }
    .rr-info-head { padding:10px 11px 9px; }
    .rr-info-title { font-size:12px; }
    .rr-info-subtitle { font-size:7.8px; }
    .rr-info-body { padding:8px 9px 12px; }
    .rr-info-context { margin-bottom:6px; }
    .rr-info-mode { font-size:6.5px; padding:4px 6px; }
    .rr-info-date { font-size:6.7px; }
    .rr-insight-hero { padding:8px 9px; }
    .rr-insight-headline { font-size:10px; }
    .rr-insight-copy { font-size:8px; line-height:1.4; }
    .rr-insight-stats { gap:4px; }
    .rr-insight-stat { padding:6px; border-radius:7px; }
    .rr-insight-stat-label { font-size:5.8px; }
    .rr-insight-stat-value { font-size:9px; }
    .rr-insight-section { margin-top:6px; padding:7px; }
    .rr-section-head { margin-bottom:6px; font-size:8.5px; }
    .rr-section-head span:last-child { font-size:6px; }
    .rr-action-grid { grid-template-columns:1fr; gap:5px; }
    .rr-action-card { grid-template-columns:22px minmax(0,1fr); padding:6px; gap:6px; }
    .rr-action-num { width:22px; height:22px; font-size:7px; }
    .rr-action-card b { font-size:8px; } .rr-action-card p { font-size:7px; }
    .rr-driver-list { grid-template-columns:1fr; gap:5px; }
    .rr-driver-item { padding:6px; }
    .rr-definition-grid { grid-template-columns:1fr; gap:5px; }
    .rr-definition-grid > div { padding:6px; }
    .rr-info-footnote { font-size:7px; }

    #modalDetailRR { align-items:flex-end !important; padding:0 !important; }
    #modalDetailRR > .relative.bg-white { width:100% !important; height:94dvh !important; max-height:94dvh !important; border-left:0; border-right:0; border-bottom:0; border-radius:14px 14px 0 0 !important; }
    #modalDetailRR .flex-row.items-center.justify-between { align-items:flex-start !important; padding:8px !important; overflow:visible !important; }
    #modalDetailRR #modalTitleRR { font-size:11px !important; }
    #modalDetailRR #modalSubTitleRR { font-size:7px !important; margin-left:0 !important; }
    #modalDetailRR #search_nasabah { width:112px !important; height:29px !important; font-size:7.5px !important; }
    #modalFilterWrapper > div { display:grid !important; grid-template-columns:repeat(2,minmax(0,1fr)) 31px; gap:4px !important; padding:5px 8px 7px !important; }
    #modalFilterWrapper select { width:100% !important; min-width:0 !important; max-width:none !important; height:28px !important; font-size:7.5px !important; }
    #modalFilterWrapper button { grid-column:3; grid-row:2; width:29px !important; height:28px !important; padding:0 !important; justify-self:end; }
    #tableExportRR { font-size:8px !important; }
    #tableExportRR th { height:30px !important; padding:4px !important; font-size:6.7px !important; line-height:1.05 !important; }
    #bodyModalRR tr { height:30px !important; }
    #bodyModalRR td { padding:3px 4px !important; font-size:7.4px !important; line-height:1.05 !important; }
    .mod-freeze-nas,.mod-td-nasabah { min-width:108px !important; max-width:108px !important; width:108px !important; }
  }


  /* ========================================================
     RR DETAIL MODAL V4 - COMPACT, TOTAL SUMMARY, RESPONSIVE
     ======================================================== */
  #modalDetailRR > .relative.bg-white {
    width:min(1240px,calc(100vw - 36px)) !important;
    height:min(84dvh,760px) !important;
    max-width:none !important;
    border-radius:14px !important;
  }
  .rr-detail-summary-wrap {
    padding:0 14px 10px;
    background:#fff;
  }
  .rr-detail-summary-head {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    margin-bottom:5px;
  }
  .rr-detail-summary-title {
    color:#475569;
    font-size:8px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.05em;
  }
  .rr-detail-summary-scope {
    color:#94a3b8;
    font-size:7.5px;
    font-weight:750;
    white-space:nowrap;
  }
  .rr-detail-summary {
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:6px;
  }
  .rr-detail-summary-card {
    min-width:0;
    padding:7px 9px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    background:#f8fafc;
  }
  .rr-detail-summary-card .label {
    color:#64748b;
    font-size:7px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.04em;
  }
  .rr-detail-summary-card .value {
    margin-top:2px;
    overflow:hidden;
    color:#172033;
    font-size:11px;
    line-height:1.1;
    font-weight:900;
    text-overflow:ellipsis;
    white-space:nowrap;
    font-variant-numeric:tabular-nums;
  }
  .rr-detail-summary-card.is-blue .value { color:#1d4ed8; }
  .rr-detail-summary-card.is-green .value { color:#047857; }
  .rr-detail-summary-card.is-red .value { color:#dc2626; }

  #modalFilterWrapper > div {
    align-items:center;
  }
  #modalFilterWrapper select {
    min-width:125px !important;
    max-width:165px !important;
  }
  #status_pembayaran_modal { min-width:155px !important; }

  #tableExportRR .rr-col-date-pay,
  #bodyModalRR .rr-col-date-pay {
    min-width:92px;
    width:92px;
    max-width:92px;
    text-align:center;
  }

  @media (max-width:1023px) and (min-width:768px) {
    #modalDetailRR > .relative.bg-white {
      width:calc(100vw - 24px) !important;
      height:88dvh !important;
    }
    .rr-detail-summary { grid-template-columns:repeat(4,minmax(0,1fr)); }
  }

  @media (max-width:767px) {
    #modalDetailRR > .relative.bg-white {
      width:100% !important;
      height:94dvh !important;
      max-height:94dvh !important;
      border-radius:14px 14px 0 0 !important;
    }
    .rr-detail-summary-wrap { padding:0 8px 7px; }
    .rr-detail-summary-head { margin-bottom:4px; }
    .rr-detail-summary-title { font-size:6.5px; }
    .rr-detail-summary-scope { font-size:6px; }
    .rr-detail-summary { grid-template-columns:repeat(2,minmax(0,1fr)); gap:4px; }
    .rr-detail-summary-card { padding:5px 6px; border-radius:6px; }
    .rr-detail-summary-card .label { font-size:5.8px; }
    .rr-detail-summary-card .value { font-size:8.5px; }

    #modalFilterWrapper > div {
      display:grid !important;
      grid-template-columns:minmax(0,1fr) minmax(0,1fr) 31px !important;
      gap:4px !important;
      padding:5px 8px 7px !important;
    }
    #modalFilterWrapper select {
      width:100% !important;
      min-width:0 !important;
      max-width:none !important;
      height:28px !important;
      font-size:7.5px !important;
    }
    #opt_kankas_modal { grid-column:1; grid-row:1; }
    #opt_ao_modal { grid-column:2; grid-row:1; }
    #status_pembayaran_modal { grid-column:1 / 3; grid-row:2; min-width:0 !important; }
    #modalFilterWrapper button {
      grid-column:3 !important;
      grid-row:1 !important;
      width:29px !important;
      min-width:29px !important;
      height:28px !important;
      padding:0 !important;
      justify-self:end;
    }
    #tableExportRR .rr-col-date-pay,
    #bodyModalRR .rr-col-date-pay {
      min-width:76px;
      width:76px;
      max-width:76px;
    }
  }



  /* ========================================================
     RR UI V6 - MODAL TOOLBAR + WARNA RR + ARAH DELTA
     ======================================================== */
  .rr-pct-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:58px;
    min-height:24px;
    padding:3px 8px;
    border-radius:999px;
    border:1px solid transparent;
    font-size:10px;
    line-height:1;
    font-weight:900;
    font-variant-numeric:tabular-nums;
    white-space:nowrap;
  }
  .rr-pct-low { color:#b91c1c; background:#fff1f2; border-color:#fecdd3; }
  .rr-pct-mid { color:#a16207; background:#fffbeb; border-color:#fde68a; }
  .rr-pct-high { color:#047857; background:#ecfdf5; border-color:#a7f3d0; }

  .rr-movement {
    display:inline-flex;
    align-items:center;
    justify-content:flex-end;
    gap:4px;
    font-size:10px;
    line-height:1;
    font-weight:900;
    font-variant-numeric:tabular-nums;
    white-space:nowrap;
  }
  .rr-movement svg { width:11px; height:11px; flex:0 0 auto; }
  .rr-movement-down { color:#dc2626; }
  .rr-movement-up { color:#059669; }
  .rr-movement-flat { color:#64748b; }
  .rr-col-noa .rr-movement { justify-content:center; font-size:9px; }

  .rr-modal-head-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    width:100%;
    padding:10px 12px 8px;
  }
  .rr-modal-title {
    flex:1 1 auto;
    min-width:180px;
    overflow:hidden;
  }
  .rr-modal-toolbar {
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:6px;
    min-width:0;
    flex:0 1 auto;
  }
  .rr-modal-search {
    position:relative;
    width:200px;
    flex:0 1 200px;
    min-width:150px;
  }
  .rr-modal-filter-inline {
    width:auto !important;
    border:0 !important;
    flex:0 1 auto;
  }
  #modalFilterWrapper > .rr-modal-filter-controls {
    display:flex !important;
    align-items:center !important;
    justify-content:flex-end !important;
    gap:6px !important;
    padding:0 !important;
    overflow:visible !important;
    flex-wrap:nowrap !important;
  }
  .rr-modal-select {
    width:124px !important;
    min-width:110px !important;
    max-width:140px !important;
    height:32px !important;
    padding:0 25px 0 8px !important;
    border-radius:8px !important;
    font-size:9px !important;
    font-weight:800 !important;
    cursor:pointer;
  }
  .rr-modal-status { width:136px !important; max-width:150px !important; }
  .rr-modal-export {
    height:32px !important;
    min-width:34px;
    padding:0 10px !important;
    gap:5px;
  }
  .rr-export-label { font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.035em; }
  .rr-modal-close {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:32px;
    min-width:32px;
    height:32px;
    padding:0;
    border:1px solid #fee2e2;
    border-radius:8px;
    background:#fff1f2;
    color:#ef4444;
    font-size:19px;
    font-weight:800;
    line-height:1;
    cursor:pointer;
    transition:.16s ease;
  }
  .rr-modal-close:hover { background:#ef4444; border-color:#ef4444; color:#fff; }
  .rr-modal-filter-toggle {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:30px;
    min-width:30px;
    height:30px;
    border:1px solid #cbd5e1;
    border-radius:7px;
    background:#fff;
    color:#475569;
  }
  .rr-detail-summary-wrap { padding-top:0 !important; }

  @media (max-width:1100px) and (min-width:768px) {
    .rr-modal-head-row { align-items:flex-start; }
    .rr-modal-toolbar { flex-wrap:wrap; max-width:680px; }
    .rr-modal-search { width:180px; flex-basis:180px; }
    .rr-modal-select { width:112px !important; min-width:100px !important; }
    .rr-modal-status { width:124px !important; }
    .rr-export-label { display:none; }
    .rr-modal-export { width:32px !important; min-width:32px !important; padding:0 !important; }
  }

  @media (max-width:767px) {
    .rr-pct-badge {
      min-width:42px;
      min-height:18px;
      padding:2px 5px;
      font-size:6.7px;
    }
    .rr-movement { gap:2px; font-size:7px; }
    .rr-movement svg { width:8px; height:8px; }
    .rr-col-noa .rr-movement { font-size:6.6px; }

    .rr-modal-head-row {
      display:grid;
      grid-template-columns:minmax(0,1fr);
      gap:6px;
      padding:7px 8px 6px;
    }
    .rr-modal-title { min-width:0; width:100%; }
    .rr-modal-toolbar {
      display:grid;
      grid-template-columns:minmax(0,1fr) 30px 30px 30px;
      gap:4px;
      width:100%;
    }
    .rr-modal-search {
      width:auto;
      min-width:0;
      max-width:none;
      flex:none;
    }
    #modalDetailRR #search_nasabah {
      width:100% !important;
      height:30px !important;
      font-size:7.8px !important;
    }
    .rr-modal-filter-toggle { grid-column:2; width:30px; height:30px; }
    .rr-modal-export {
      grid-column:3;
      width:30px !important;
      min-width:30px !important;
      height:30px !important;
      padding:0 !important;
    }
    .rr-modal-export .rr-export-label { display:none; }
    .rr-modal-close { grid-column:4; width:30px; min-width:30px; height:30px; }

    #modalFilterWrapper {
      grid-column:1 / -1;
      grid-row:2;
      width:100% !important;
      padding-top:2px;
    }
    #modalFilterWrapper > .rr-modal-filter-controls {
      display:grid !important;
      grid-template-columns:repeat(2,minmax(0,1fr)) !important;
      gap:4px !important;
      width:100%;
    }
    .rr-modal-select,
    .rr-modal-status {
      width:100% !important;
      min-width:0 !important;
      max-width:none !important;
      height:28px !important;
      padding:0 20px 0 6px !important;
      font-size:7.2px !important;
    }
    #status_pembayaran_modal { grid-column:1 / -1; }
  }



  /* ========================================================
     RR UI V8 - MOBILE-FIRST COMPACT & RESPONSIVE
     Tujuan:
     - mobile tidak terasa "centered"
     - kolom report lebih rapat, tanpa ruang kosong berlebihan
     - text tetap terbaca dan alignment mengikuti tipe data
     - desktop/tablet tetap mempertahankan layout yang sudah ada
     ======================================================== */

  /* Seluruh area scroll memanfaatkan lebar card, bukan menyisakan gutter semu. */
  #rrPage > .flex-1 { min-width:0; }
  #rrPage > .flex-1 > .custom-scrollbar { min-width:0; width:100%; }

  /* Alignment semantik: label kiri, angka kanan, NOA/% tetap center. */
  #tabelRR tbody td { vertical-align:middle; }
  #tabelRR tbody td.sticky-left-1,
  #tabelRR tbody td.sticky-left-2 { text-align:left; }
  #tabelRR .rr-col-noa { text-align:center !important; }

  /* Modal detail tidak lagi memakai alignment center sebagai default di HP. */
  #tableExportRR,
  #tableExportRR tbody,
  #bodyModalRR { text-align:left !important; }
  #tableExportRR td,
  #tableExportRR th { vertical-align:middle; }
  .rr-detail-summary-card { text-align:left; }

  @media (min-width:768px) and (max-width:1279px) {
    /* Tablet: filter tetap compact dan table memakai ruang yang tersedia. */
    #tabelRR { width:max-content; min-width:100%; table-layout:auto; }
    #rrHeaderSubtitle { max-width:240px; }
    #modalDetailRR > .relative.bg-white { width:calc(100vw - 20px) !important; }
  }

  @media (max-width:767px) {
    /* ---------- PAGE / HEADER ---------- */
    html, body { overscroll-behavior:none; }
    #rrPage {
      width:100% !important;
      max-width:100% !important;
      padding:4px !important;
      gap:4px !important;
      overflow:hidden !important;
    }
    #rrHeaderCard {
      width:100%;
      padding:6px !important;
      border-radius:10px !important;
    }
    #rrHeaderCard > div:first-child { width:100%; }
    #rrHeaderCard .flex.items-center.justify-between.w-full { min-width:0; }
    #rrHeaderTitle {
      max-width:150px;
      font-size:12px !important;
      line-height:1.05 !important;
      text-align:left !important;
    }
    #rrHeaderSubtitle {
      max-width:160px !important;
      font-size:6.6px !important;
      line-height:1.2 !important;
      text-align:left !important;
    }
    #rrFilterToggle {
      min-width:46px;
      margin-left:4px !important;
      padding:0 7px !important;
    }

    /* ---------- MAIN REPORT ----------
       Gunakan intrinsic width + ukuran kolom eksplisit. Dengan ini tabel tidak
       membagi ruang rata (table-fixed) yang membuat banyak area kosong. */
    #tabelRR {
      width:max-content !important;
      min-width:100% !important;
      table-layout:auto !important;
      font-size:8px !important;
    }
    #tabelRR thead,
    #tabelRR tbody { width:auto !important; }

    /* Kode disembunyikan di mobile; Nama Kantor menjadi kolom freeze utama. */
    #tabelRR .sticky-left-2,
    #tabelRR tbody td.sticky-left-2,
    #tabelRR thead th.sticky-left-2 {
      left:0 !important;
      width:108px !important;
      min-width:108px !important;
      max-width:108px !important;
      padding-left:7px !important;
      padding-right:6px !important;
      text-align:left !important;
    }

    /* Row-2 berisi pola: Nominal, NOA, %, diulang 3x. */
    #tabelRR .rr-row-2 th:nth-child(3n + 1) {
      width:92px !important;
      min-width:92px !important;
      max-width:92px !important;
      text-align:right !important;
    }
    #tabelRR .rr-row-2 th:nth-child(3n + 2) {
      width:42px !important;
      min-width:42px !important;
      max-width:42px !important;
      text-align:center !important;
    }
    #tabelRR .rr-row-2 th:nth-child(3n) {
      width:50px !important;
      min-width:50px !important;
      max-width:50px !important;
      text-align:center !important;
    }
    #tabelRR .rr-row-2 th:nth-child(7) {
      width:100px !important;
      min-width:100px !important;
      max-width:100px !important;
    }

    #tabelRR thead th {
      padding:3px 4px !important;
      line-height:1.05 !important;
      white-space:normal !important;
    }
    #tabelRR .rr-row-1 th {
      height:29px !important;
      font-size:7px !important;
    }
    #tabelRR .rr-row-2 th {
      top:29px !important;
      height:29px !important;
      font-size:6.3px !important;
    }
    #tabelRR .rr-row-tot th {
      top:58px !important;
      height:34px !important;
      font-size:7.6px !important;
      padding:4px 5px !important;
    }
    #bodyRekap tr { height:34px !important; }
    #bodyRekap td {
      height:34px !important;
      padding:4px 5px !important;
      font-size:7.8px !important;
      line-height:1.15 !important;
      white-space:nowrap;
    }
    #bodyRekap td.sticky-left-2 {
      font-size:7.6px !important;
      font-weight:850 !important;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    #bodyRekap td.rr-col-noa {
      padding-left:2px !important;
      padding-right:2px !important;
      font-size:7.2px !important;
    }
    #tabelRR .rr-pct-badge {
      min-width:43px;
      min-height:19px;
      padding:2px 4px;
      font-size:6.8px;
    }
    #tabelRR .rr-movement {
      width:100%;
      justify-content:flex-end;
      gap:2px;
      font-size:6.8px;
    }
    #tabelRR .rr-col-noa .rr-movement { justify-content:center; }

    /* ---------- DETAIL MODAL ---------- */
    #modalDetailRR {
      align-items:flex-end !important;
      padding:0 !important;
    }
    #modalDetailRR > .relative.bg-white {
      width:100% !important;
      height:92dvh !important;
      max-height:92dvh !important;
      border-radius:14px 14px 0 0 !important;
    }
    .rr-modal-head-row {
      gap:5px !important;
      padding:7px 8px 5px !important;
      text-align:left !important;
    }
    .rr-modal-title,
    #modalTitleRR,
    #modalSubTitleRR { text-align:left !important; }
    #modalTitleRR {
      font-size:10.8px !important;
      line-height:1.15 !important;
    }
    #modalSubTitleRR {
      max-width:100%;
      font-size:6.7px !important;
      line-height:1.2 !important;
    }
    .rr-modal-toolbar {
      grid-template-columns:minmax(0,1fr) 30px 30px 30px !important;
      gap:4px !important;
    }
    #modalDetailRR #search_nasabah {
      height:30px !important;
      font-size:7.7px !important;
      text-align:left !important;
    }

    /* Summary lebih padat: 4 kartu tetap terbaca tanpa mengambil tinggi berlebih. */
    .rr-detail-summary-wrap { padding:0 8px 6px !important; }
    .rr-detail-summary-head { margin-bottom:3px !important; }
    .rr-detail-summary { gap:4px !important; }
    .rr-detail-summary-card {
      min-height:38px;
      padding:5px 6px !important;
      text-align:left !important;
    }
    .rr-detail-summary-card .label { font-size:5.8px !important; }
    .rr-detail-summary-card .value { font-size:8.4px !important; }

    /* Tabel detail memakai lebar sesuai isi, bukan fixed-grid penuh ruang kosong. */
    #tableExportRR {
      width:max-content !important;
      min-width:100% !important;
      table-layout:auto !important;
      text-align:left !important;
      font-size:8px !important;
    }
    #tableExportRR th:not(.mod-freeze-rek):not(.mod-freeze-nas) {
      width:auto !important;
      min-width:70px !important;
      max-width:138px !important;
    }
    #tableExportRR th,
    #tableExportRR td {
      padding:4px 6px !important;
      line-height:1.12 !important;
    }
    #tableExportRR th {
      height:31px !important;
      font-size:6.7px !important;
      white-space:nowrap !important;
    }
    #bodyModalRR tr { height:32px !important; }
    #bodyModalRR td {
      height:32px !important;
      font-size:7.7px !important;
      white-space:nowrap;
    }

    /* Nama adalah anchor utama saat scroll horizontal; rekening tetap disembunyikan. */
    .mod-freeze-nas,
    .mod-td-nasabah {
      left:0 !important;
      width:118px !important;
      min-width:118px !important;
      max-width:118px !important;
      text-align:left !important;
      padding-left:7px !important;
    }
    .mod-td-nasabah {
      overflow:hidden;
      text-overflow:ellipsis;
    }

    /* Alamat tidak lagi mengambil 200-350px di HP. */
    #tableExportRR th:nth-child(3),
    #bodyModalRR td:nth-child(3) {
      width:142px !important;
      min-width:142px !important;
      max-width:142px !important;
      text-align:left !important;
      overflow:hidden;
      text-overflow:ellipsis;
    }

    /* Kolom teks operasional rata kiri; tanggal/WA/NOA tetap natural. */
    #bodyModalRR td:nth-child(6) { text-align:left !important; }
    #bodyModalRR td:nth-child(4),
    #bodyModalRR td:nth-child(5),
    #bodyModalRR td:nth-child(7),
    #bodyModalRR td:nth-child(8) { text-align:center !important; }

    /* Footer pagination lebih tipis agar tabel mendapat ruang vertikal lebih banyak. */
    #modalDetailRR > .relative.bg-white > .px-3.py-2\.5 {
      padding:6px 8px !important;
    }
    #pageInfoRR { font-size:7.5px !important; padding:4px 7px !important; }
    #btnPrevRR, #btnNextRR {
      height:29px;
      padding:0 9px !important;
      font-size:7.5px !important;
    }
  }
</style>

<div id="rrPage" class="max-w-[1920px] mx-auto px-2 md:px-4 py-4 md:py-6 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">
  
  <div class="flex-none mb-3 md:mb-4 w-full shrink-0">
    <div id="rrHeaderCard" class="relative rr-header-card px-3 md:px-5 py-3 md:py-4">
      <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 w-full">
        <div class="flex items-center justify-between w-full xl:w-auto shrink-0">
          <div class="flex items-center gap-2 md:gap-3 min-w-0">
            <span class="p-1.5 md:p-2.5 bg-blue-600 rounded-lg text-white shadow-sm shrink-0">
              <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v12"></path></svg>
            </span>
            <div class="min-w-0">
              <div class="flex items-center gap-2 min-w-0">
                <h1 id="rrHeaderTitle" class="text-[15px] md:text-2xl font-extrabold text-slate-800 tracking-tight leading-none truncate">Repayment Rate (RR)</h1>
                <button type="button" id="rrInfoButton" onclick="toggleInfoRR()" class="rr-info-button" title="Ringkasan kondisi RR" aria-label="Buka ringkasan kondisi Repayment Rate" aria-expanded="false">i</button>
              </div>
              <p id="rrHeaderSubtitle" class="text-[9px] md:text-sm text-slate-500 mt-1 truncate">Posisi repayment closing dibanding actual harian</p>
            </div>
          </div>

          <button type="button" id="rrFilterToggle" onclick="toggleMainFilter()" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] whitespace-nowrap ml-2 shrink-0">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Filter
          </button>
        </div>

        <div id="filterWrapperMain" class="hidden xl:flex w-full xl:w-auto transition-all duration-300">
          <form id="formFilter" class="w-full xl:w-auto flex flex-col sm:flex-row sm:items-end gap-2 sm:gap-3" onsubmit="event.preventDefault(); fetchRekap();">
            <div class="flex flex-wrap md:flex-nowrap items-end gap-2 w-full xl:w-auto">
              <div class="field flex-1 min-w-[120px] md:min-w-[130px]">
                <label class="lbl text-slate-700">CLOSING (M-1)</label>
                <input type="date" id="closing_date" onchange="fetchRekap()" class="inp w-full text-[10px] md:text-sm font-semibold h-[32px] md:h-[38px] px-2 md:px-3 text-slate-700 cursor-pointer bg-slate-50" required onclick="try{this.showPicker()}catch(e){}">
              </div>
              <div class="field flex-1 min-w-[120px] md:min-w-[130px]">
                <label class="lbl text-slate-700">ACTUAL (HARIAN)</label>
                <input type="date" id="harian_date" onchange="fetchRekap()" class="inp w-full text-[10px] md:text-sm font-semibold h-[32px] md:h-[38px] px-2 md:px-3 text-slate-700 cursor-pointer bg-slate-50" required onclick="try{this.showPicker()}catch(e){}">
              </div>
              <div class="field flex-1 min-w-[120px] md:min-w-[140px]">
                <label class="lbl text-slate-700">TIPE SALDO</label>
                <select id="tipe_saldo_rr" class="inp bg-slate-50 text-[10px] md:text-sm font-bold h-[32px] md:h-[38px] px-2 md:px-3 text-slate-700 cursor-pointer w-full" onchange="fetchRekap()">
                  <option value="baki_debet">BAKI DEBET</option>
                  <option value="saldo_bank">SALDO BANK</option>
                </select>
              </div>
              <div class="field flex-1 min-w-[180px] md:min-w-[220px]">
                <label class="lbl text-slate-700">AREA / CABANG</label>
                <select id="opt_kantor" class="inp bg-slate-50 text-[10px] md:text-sm font-bold h-[32px] md:h-[38px] px-2 md:px-3 text-slate-700 cursor-pointer w-full truncate" onchange="fetchRekap()">
                  <option value="">Loading...</option>
                </select>
              </div>
              <button type="button" onclick="exportExcelRekap()" class="btn-icon h-[32px] md:h-[38px] w-[36px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm shrink-0" title="Download Excel">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="md:w-[18px] md:h-[18px]"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline></svg>
              </button>
            </div>
          </form>
        </div>
      </div>

      <div id="rrInfoBackdrop" class="rr-info-backdrop" aria-hidden="true" onclick="closeRRInfo()"></div>
      <aside id="infoRR" class="rr-info-panel custom-scrollbar" role="dialog" aria-modal="true" aria-labelledby="rrInfoTitle" aria-hidden="true">
        <div class="rr-info-head">
          <div class="min-w-0">
            <h2 id="rrInfoTitle" class="rr-info-title">Ringkasan Repayment Rate</h2>
            <p class="rr-info-subtitle">Kondisi actual dibanding closing dan prioritas follow-up agar kualitas repayment tetap terjaga.</p>
          </div>
          <button type="button" class="rr-info-close" onclick="closeRRInfo(true)" aria-label="Tutup informasi">&times;</button>
        </div>

        <div class="rr-info-body">
          <div class="rr-info-context">
            <span class="rr-info-mode">Kondisi Saat Ini</span>
            <span id="rrInsightDate" class="rr-info-date">-</span>
          </div>

          <section id="rrInsightHero" class="rr-insight-hero">
            <div class="rr-insight-eyebrow">Ringkasan</div>
            <div id="rrInsightHeadline" class="rr-insight-headline">Memuat ringkasan Repayment Rate...</div>
            <div id="rrInsightCopy" class="rr-insight-copy">Ringkasan mengikuti data dan filter yang sedang aktif.</div>
          </section>

          <div class="rr-insight-stats">
            <div class="rr-insight-stat">
              <div class="rr-insight-stat-label">RR Closing</div>
              <div id="rrStatClosing" class="rr-insight-stat-value">-</div>
            </div>
            <div class="rr-insight-stat">
              <div class="rr-insight-stat-label">RR Actual</div>
              <div id="rrStatActual" class="rr-insight-stat-value">-</div>
            </div>
            <div class="rr-insight-stat">
              <div class="rr-insight-stat-label">Perubahan</div>
              <div id="rrStatDelta" class="rr-insight-stat-value">-</div>
            </div>
          </div>

          <section class="rr-insight-section">
            <div class="rr-section-head">
              <span>Prioritas Tindak Lanjut</span>
              <span>berdasarkan detail pembayaran</span>
            </div>
            <div class="rr-action-grid">
              <div class="rr-action-card rr-action-danger">
                <span class="rr-action-num">1</span>
                <div><b>Belum Bayar</b><p>Prioritaskan rekening yang sudah melewati jatuh tempo tetapi belum melakukan pembayaran. Hubungi debitur dan pastikan komitmen pembayaran segera ditindaklanjuti.</p></div>
              </div>
              <div class="rr-action-card rr-action-warn">
                <span class="rr-action-num">2</span>
                <div><b>Telat Bayar</b><p>Pantau rekening yang sudah membayar namun melewati jatuh tempo. Cegah keterlambatan berulang agar RR tidak terus menurun pada posisi berikutnya.</p></div>
              </div>
              <div class="rr-action-card rr-action-info">
                <span class="rr-action-num">3</span>
                <div><b>Belum Jatuh Tempo</b><p>Rekening belum wajib bayar, tetapi tetap masuk daftar pantau sampai tanggal jatuh tempo. Pastikan saldo atau sumber pembayaran sudah tersedia.</p></div>
              </div>
              <div class="rr-action-card rr-action-neutral">
                <span class="rr-action-num">4</span>
                <div><b>Tunggakan & Tabungan</b><p>Gunakan filter <b>Tunggakan &gt; 0</b> dan status tabungan untuk menentukan rekening yang perlu follow-up lebih dulu atau memiliki sumber pembayaran yang dapat dipantau.</p></div>
              </div>
            </div>
          </section>

          <section class="rr-insight-section">
            <div class="rr-section-head">
              <span>Cabang Perlu Perhatian</span>
              <span>penurunan RR terbesar</span>
            </div>
            <div id="rrDriverList" class="rr-driver-list">
              <div class="rr-driver-empty">Data prioritas akan muncul setelah rekap selesai dimuat.</div>
            </div>
          </section>

          <section class="rr-insight-section rr-definition-section">
            <div class="rr-section-head"><span>Cara Membaca RR</span><span>ringkas</span></div>
            <div class="rr-definition-grid">
              <div><b>RR</b><span>Saldo lancar / seluruh saldo outstanding sesuai tipe saldo yang dipilih.</span></div>
              <div><b>M-1</b><span>Posisi closing bulan sebelumnya sebagai pembanding.</span></div>
              <div><b>Actual</b><span>Posisi harian pada tanggal yang dipilih.</span></div>
              <div><b>Delta</b><span>Perubahan kondisi Actual terhadap M-1. Nilai negatif perlu menjadi perhatian.</span></div>
              <div><b>OTP</b><span>Pembayaran dilakukan tepat waktu.</span></div>
              <div><b>Belum Bayar</b><span>Sudah melewati jatuh tempo dan pembayaran belum diterima.</span></div>
            </div>
          </section>

          <div class="rr-info-footnote">Gunakan angka Actual atau Delta pada tabel untuk membuka detail debitur. Ringkasan ini merupakan alat bantu monitoring; tindak lanjut tetap menyesuaikan kondisi rekening dan ketentuan internal.</div>
        </div>
      </aside>
    </div>
  </div>

  <div class="flex-1 min-h-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm relative flex flex-col">
    
    <div id="loadingRekap" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
        <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
        <span>Menyiapkan Matriks...</span>
    </div>

    <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table class="min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelRR">
        <thead class="uppercase bg-slate-50 text-slate-600 font-bold select-none" id="headRR">
          </thead>
        <tbody id="bodyRekap" class="divide-y divide-slate-100 bg-white group-tbody text-[10px] md:text-sm"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="modalDetailRR" class="fixed inset-0 hidden z-[9999] flex items-end md:items-center justify-center p-0 sm:p-4">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalRR()"></div>
  <div class="relative bg-white w-full h-[95vh] md:h-[92vh] max-w-[1600px] rounded-t-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up">
    
    <div class="flex flex-col bg-white border-b shrink-0 w-full z-50">
        <div class="rr-modal-head-row">
            <div class="rr-modal-title" id="modal-title-container">
              <h3 class="font-bold text-slate-800 flex items-center gap-1.5 text-[12px] md:text-xl leading-none truncate">
                  <span class="w-1.5 md:w-2 h-4 md:h-6 bg-blue-600 rounded-full hidden md:block shrink-0"></span>
                  <span id="modalTitleRR" class="truncate">Detail Rekap RR</span>
              </h3>
              <p class="text-[9px] md:text-sm text-slate-500 mt-1 md:ml-4 font-mono font-medium leading-none truncate" id="modalSubTitleRR">...</p>
            </div>

            <div class="rr-modal-toolbar">
                <div class="rr-modal-search">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="search_nasabah" onkeyup="filterTableDetail()" class="w-full pl-8 pr-3 h-[32px] bg-slate-50 border border-slate-200 rounded-lg text-[10px] md:text-xs outline-none focus:border-blue-500 focus:bg-white transition-all placeholder-slate-400 font-medium" placeholder="Cari nama / rekening...">
                </div>

                <button type="button" id="rrModalFilterToggle" onclick="toggleRRModalFilter()" class="rr-modal-filter-toggle md:hidden" aria-controls="modalFilterWrapper" aria-expanded="false" title="Filter detail">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M10 18h4"></path></svg>
                </button>

                <div id="modalFilterWrapper" class="hidden md:block rr-modal-filter-inline">
                    <div class="rr-modal-filter-controls">
                        <select id="opt_kankas_modal" class="inp rr-modal-select text-blue-800 bg-blue-50/50 border-blue-200" onchange="handleModalKankasChangeRR()" title="Filter Kankas">
                            <option value="">Semua Kankas</option>
                        </select>

                        <select id="opt_ao_modal" class="inp rr-modal-select text-slate-700 bg-slate-50 border-slate-200" onchange="loadDetailPage(1)" title="Filter AO">
                            <option value="">Semua AO</option>
                        </select>

                        <select id="status_pembayaran_modal" class="inp rr-modal-select rr-modal-status text-slate-700 bg-slate-50 border-slate-200" onchange="loadDetailPage(1)" title="Status Pembayaran">
                            <option value="ALL">Semua Status</option>
                            <option value="OTP">OTP</option>
                            <option value="TELAT">Telat</option>
                            <option value="BELUM_JATUH_TEMPO">Belum Jatuh Tempo</option>
                            <option value="BELUM_BAYAR">Belum Bayar</option>
                        </select>
                    </div>
                </div>

                <button onclick="downloadExcelFull(event)" class="rr-modal-export btn-icon bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm shrink-0" title="Export Excel" aria-label="Export Excel">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="rr-export-label">Export</span>
                </button>

                <button onclick="closeModalRR()" class="rr-modal-close" title="Tutup" aria-label="Tutup">&times;</button>
            </div>
        </div>

        <div class="rr-detail-summary-wrap">
          <div class="rr-detail-summary-head">
            <span id="rrDetailSummaryTitle" class="rr-detail-summary-title">Ringkasan Detail</span>
            <span id="rrDetailSummaryScope" class="rr-detail-summary-scope">Mengikuti filter aktif</span>
          </div>
          <div class="rr-detail-summary">
            <div class="rr-detail-summary-card">
              <div id="rrSummaryLabel1" class="label">Debitur</div>
              <div id="rrSummaryValue1" class="value">0</div>
            </div>
            <div class="rr-detail-summary-card is-green">
              <div id="rrSummaryLabel2" class="label">Bayar</div>
              <div id="rrSummaryValue2" class="value">0</div>
            </div>
            <div class="rr-detail-summary-card is-blue">
              <div id="rrSummaryLabel3" class="label">Actual</div>
              <div id="rrSummaryValue3" class="value">0</div>
            </div>
            <div class="rr-detail-summary-card is-red">
              <div id="rrSummaryLabel4" class="label">Tunggakan</div>
              <div id="rrSummaryValue4" class="value">0</div>
            </div>
          </div>
        </div>

    </div>

    <div class="flex-1 overflow-auto bg-slate-50 relative p-0 md:p-3 custom-scrollbar">
      <div id="loadingModalRR" class="hidden absolute inset-0 bg-white/90 z-40 flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
         <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
         <span class="text-[10px] md:text-sm font-bold uppercase tracking-widest">Memuat Detail...</span>
      </div>
      
      <table class="w-max min-w-full text-left text-slate-700 border-separate border-spacing-0 md:border border-slate-200 md:rounded-xl shadow-sm bg-white table-fixed" id="tableExportRR">
        <thead id="headModalRR" class="text-[9px] md:text-xs text-slate-600 uppercase bg-slate-100 font-bold tracking-wider select-none"></thead>
        <tbody id="bodyModalRR" class="divide-y divide-slate-100 bg-white modal-tbody text-[9.5px] md:text-xs"></tbody>
      </table>
    </div>

    <div class="px-3 py-2.5 md:px-5 md:py-4 border-t bg-white flex justify-between items-center shrink-0">
      <span class="text-[9px] md:text-sm font-bold text-slate-600 bg-slate-100 px-2 md:px-3 py-1 rounded-md md:rounded-lg" id="pageInfoRR">0 Data</span>
      <div class="flex gap-1 md:gap-2">
          <button id="btnPrevRR" onclick="changePageDetail(-1)" class="px-2.5 md:px-4 py-1.5 md:py-2 bg-white border border-slate-300 rounded-md md:rounded-lg text-[9px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">« Prev</button>
          <button id="btnNextRR" onclick="changePageDetail(1)" class="px-2.5 md:px-4 py-1.5 md:py-2 bg-white border border-slate-300 rounded-md md:rounded-lg text-[9px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">Next »</button>
      </div>
    </div>
  </div>
</div>

<script>
  const API_URL  = './api/rr/'; 
  const API_DATE = './api/date/';
  const API_KODE_URL = './api/kode/'; 
  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Math.round(Number(n||0)));

  let abortRekap;
  let rekapDataCache = []; 
  let rekapGtCache = null;
  let detailDataCache = [];
  let userKodeGlobal = '000'; 

  // 🔥 STATE SORTING 🔥
  let sortCol = '';
  let sortAsc = true;
  let sortDetailCol = '';
  let sortDetailAsc = true;

  let currentDetailParams = {};
  let currentDetailPage = 1;
  let currentDetailTotalPages = 1;
  let currentMode = 'NORMAL'; 
  const detailLimit = 20;
  const getTipeSaldoRR = () => document.getElementById('tipe_saldo_rr')?.value || 'baki_debet';
  const getTipeSaldoLabelRR = () => getTipeSaldoRR() === 'saldo_bank' ? 'SALDO BANK' : 'BAKI DEBET';

  const getSortIcon = (col, currentCol, asc) => {
      if (col !== currentCol) return '<span class="opacity-30 text-[8px] md:text-[10px] ml-1.5 font-sans">↕</span>';
      return asc ? '<span class="text-blue-600 ml-1.5 text-[10px] md:text-[11px] font-sans">▲</span>' : '<span class="text-blue-600 ml-1.5 text-[10px] md:text-[11px] font-sans">▼</span>';
  };

  // 🔥 FUNGSI TOGGLE FILTER UTAMA HP 🔥
  function toggleMainFilter() {
      const el = document.getElementById('filterWrapperMain');
      if(el.classList.contains('hidden')) {
          el.classList.remove('hidden');
          el.classList.add('flex');
      } else {
          el.classList.add('hidden');
          el.classList.remove('flex');
      }
  }


  function toggleRRModalFilter() {
      const wrapper = document.getElementById('modalFilterWrapper');
      const button = document.getElementById('rrModalFilterToggle');
      if (!wrapper) return;
      const willOpen = wrapper.classList.contains('hidden');
      wrapper.classList.toggle('hidden', !willOpen);
      wrapper.classList.toggle('block', willOpen);
      button?.setAttribute('aria-expanded', String(willOpen));
  }
  window.toggleRRModalFilter = toggleRRModalFilter;

  let rrInfoOpen = false;

  function rrNum(v) {
      const n = Number(String(v ?? 0).replace('%','').replace(',','.'));
      return Number.isFinite(n) ? n : 0;
  }

  function rrPct(v) {
      return `${rrNum(v).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2})}%`;
  }

  function rrSignedPct(v) {
      const n = rrNum(v);
      if (n > 0) return `+${n.toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2})}%`;
      if (n < 0) return `-${Math.abs(n).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2})}%`;
      return '0,00%';
  }

  // Delta NOA dan Delta % harus merepresentasikan perubahan Actual terhadap M-1.
  // Jangan memakai delta_noa / delta_pct dari response API karena field tersebut
  // dapat memiliki arti migrasi/flow pada endpoint RR. Untuk tabel rekap, hitung
  // langsung dari angka yang benar-benar ditampilkan di kolom M-1 dan Actual.
  function recalcRRDisplayDelta(row) {
      if (!row || typeof row !== 'object') return row;

      const m1Noa = Number(row.m1_all_noa || 0);
      const actualNoa = Number(row.cur_all_noa || 0);
      const m1Pct = rrNum(row.m1_pct);
      const actualPct = rrNum(row.cur_pct);

      row.delta_noa = actualNoa - m1Noa;
      row.delta_pct = Number((actualPct - m1Pct).toFixed(2));
      return row;
  }

  function rrDateLabel(value) {
      if (!value) return '-';
      const p = String(value).split('-');
      return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : value;
  }

  function selectedRRAreaLabel() {
      const select = document.getElementById('opt_kantor');
      return select?.options?.[select.selectedIndex]?.textContent?.trim() || 'KONSOLIDASI';
  }

  function updateRRInsight() {
      const gt = rekapGtCache || {};
      const rows = Array.isArray(rekapDataCache) ? rekapDataCache : [];
      const hero = document.getElementById('rrInsightHero');
      const headline = document.getElementById('rrInsightHeadline');
      const copy = document.getElementById('rrInsightCopy');
      const date = document.getElementById('rrInsightDate');
      const list = document.getElementById('rrDriverList');
      if (!hero || !headline || !copy || !list) return;

      hero.classList.remove('good','alert','warn');
      if (date) {
          date.textContent = `${selectedRRAreaLabel()} • ${rrDateLabel(document.getElementById('closing_date')?.value)} → ${rrDateLabel(document.getElementById('harian_date')?.value)}`;
      }

      if (!Object.keys(gt).length && !rows.length) {
          headline.textContent = 'Data Repayment Rate belum tersedia.';
          copy.textContent = 'Ringkasan akan otomatis diperbarui setelah data rekap selesai dimuat.';
          document.getElementById('rrStatClosing').textContent = '-';
          document.getElementById('rrStatActual').textContent = '-';
          document.getElementById('rrStatDelta').textContent = '-';
          list.innerHTML = '<div class="rr-driver-empty">Belum ada data yang dapat dianalisis.</div>';
          return;
      }

      const m1 = rrNum(gt.m1_pct);
      const actual = rrNum(gt.cur_pct);
      const delta = rrNum(gt.delta_pct);
      document.getElementById('rrStatClosing').textContent = rrPct(m1);
      document.getElementById('rrStatActual').textContent = rrPct(actual);
      const deltaNode = document.getElementById('rrStatDelta');
      deltaNode.textContent = rrSignedPct(delta);
      deltaNode.classList.remove('good','bad');
      if (delta < 0) deltaNode.classList.add('bad');
      if (delta > 0) deltaNode.classList.add('good');

      const declining = [...rows]
          .filter(r => rrNum(r.delta_pct) < 0)
          .sort((a,b) => rrNum(a.delta_pct) - rrNum(b.delta_pct));

      if (delta < 0) {
          hero.classList.add('alert');
          headline.textContent = `RR turun menjadi ${rrPct(actual)} dibanding closing ${rrPct(m1)}.`;
          copy.textContent = declining.length
              ? `${declining.length} cabang/area mengalami penurunan. Prioritaskan rekening Belum Bayar dan Telat pada cabang dengan penurunan terbesar agar kualitas repayment tidak semakin melemah.`
              : 'Posisi RR turun dibanding closing. Buka detail Actual atau Delta untuk menelusuri rekening penyebab penurunan.';
      } else if (delta > 0) {
          hero.classList.add('good');
          headline.textContent = `RR membaik menjadi ${rrPct(actual)} dibanding closing ${rrPct(m1)}.`;
          copy.textContent = declining.length
              ? `Secara total RR membaik, tetapi masih ada ${declining.length} cabang/area yang menurun dan perlu dipantau.`
              : 'Tidak terlihat cabang dengan penurunan RR pada data yang tampil. Pertahankan follow-up pembayaran sampai akhir periode.';
      } else {
          hero.classList.add('warn');
          headline.textContent = `RR relatif tetap di ${rrPct(actual)}.`;
          copy.textContent = declining.length
              ? `Total relatif tetap, namun ${declining.length} cabang/area masih mengalami penurunan. Gunakan daftar prioritas di bawah untuk follow-up.`
              : 'Tetap pantau status Belum Bayar, Telat, dan rekening yang mendekati jatuh tempo.';
      }

      const top = declining.slice(0,3);
      if (!top.length) {
          list.innerHTML = '<div class="rr-driver-empty">Belum ada cabang/area dengan penurunan RR pada data yang tampil.</div>';
      } else {
          list.innerHTML = top.map((r,i) => `
              <div class="rr-driver-item">
                <span class="rr-driver-rank">${i+1}</span>
                <div class="min-w-0">
                  <div class="rr-driver-name" title="${attrRR(r.nama || r.kode || '-')}">${attrRR(r.nama || r.kode || '-')}</div>
                  <div class="rr-driver-meta">M-1 ${rrPct(r.m1_pct)} → Actual ${rrPct(r.cur_pct)}</div>
                </div>
                <strong>${rrSignedPct(r.delta_pct)}</strong>
              </div>`).join('');
      }
  }

  function openRRInfo() {
      const panel = document.getElementById('infoRR');
      const backdrop = document.getElementById('rrInfoBackdrop');
      const button = document.getElementById('rrInfoButton');
      if (!panel || !backdrop) return;
      rrInfoOpen = true;
      updateRRInsight();
      panel.classList.add('open');
      backdrop.classList.add('open');
      panel.setAttribute('aria-hidden','false');
      backdrop.setAttribute('aria-hidden','false');
      button?.setAttribute('aria-expanded','true');
      document.documentElement.classList.add('rr-info-lock');
  }

  function closeRRInfo(returnFocus = false) {
      const panel = document.getElementById('infoRR');
      const backdrop = document.getElementById('rrInfoBackdrop');
      const button = document.getElementById('rrInfoButton');
      rrInfoOpen = false;
      panel?.classList.remove('open');
      backdrop?.classList.remove('open');
      panel?.setAttribute('aria-hidden','true');
      backdrop?.setAttribute('aria-hidden','true');
      button?.setAttribute('aria-expanded','false');
      document.documentElement.classList.remove('rr-info-lock');
      if (returnFocus) button?.focus({preventScroll:true});
  }

  function toggleInfoRR() {
      rrInfoOpen ? closeRRInfo(true) : openRRInfo();
  }
  window.closeRRInfo = closeRRInfo;

  window.addEventListener('DOMContentLoaded', async () => {
      const user = (window.getUser && window.getUser()) || null;
      userKodeGlobal = (user?.kode ? String(user.kode).padStart(3,'0') : '000');

      const now = new Date();
      try {
          const r = await fetch(API_DATE); const j = await r.json();
          const d = j.data || null;
          if (d) {
              document.getElementById('closing_date').value = d.last_closing;
              document.getElementById('harian_date').value = d.last_created;
          } else {
              document.getElementById('closing_date').value = `${now.getFullYear() - 1}-12-31`;
              document.getElementById('harian_date').value = now.toISOString().split('T')[0];
          }
      } catch(e) { 
          document.getElementById('closing_date').value = `${now.getFullYear() - 1}-12-31`;
          document.getElementById('harian_date').value = now.toISOString().split('T')[0]; 
      }

      await populateKantor(userKodeGlobal);
      setupRekapDetailClickRR();
      fetchRekap();
  });

  async function apiCall(url, payload, signal = null) {
      const opt = { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) };
      if (signal) opt.signal = signal;
      const res = await fetch(url, opt);
      let json = null;
      try { json = await res.json(); }
      catch (err) { throw new Error(`Response API tidak valid (HTTP ${res.status})`); }
      if (!res.ok) throw new Error(json?.message || `HTTP ${res.status}`);
      return json || {};
  }

  const attrRR = (v) => String(v ?? '').replace(/[&<>"']/g, ch => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
  }[ch])).replace(/\r?\n/g, ' ');

  function setupRekapDetailClickRR() {
      const table = document.getElementById('tabelRR');
      if (!table || table.dataset.detailClickBound === '1') return;
      table.dataset.detailClickBound = '1';
      table.addEventListener('click', (e) => {
          const target = e.target.closest('[data-detail-rr="1"]');
          if (!target) return;
          initModalDetail(
              target.dataset.tgl || 'ALL',
              target.dataset.status || 'ALL',
              target.dataset.kode || null,
              target.dataset.nama || '',
              target.dataset.label || 'Detail',
              target.dataset.kankas || null
          );
      });
  }

  async function loadKankasModalDropdown(kodeKantor = null) {
      const el = document.getElementById('opt_kankas_modal');
      if (!el) return;
      el.innerHTML = '<option value="">Semua Kankas</option>';
      if (!kodeKantor) return;

      try {
          const res = await apiCall(API_KODE_URL, { type: 'kode_kankas', kode_kantor: kodeKantor });
          let h = '<option value="">Semua Kankas</option>';
          (res.data || []).forEach(x => {
              const kode = x.kode_group1 || '';
              const nama = x.deskripsi_group1 || kode;
              h += `<option value="${kode}">${nama}</option>`;
          });
          el.innerHTML = h;
      } catch (err) {
          console.error('Gagal load kankas RR', err);
      }
  }

  async function loadAOModalDropdown(kodeKantor = null) {
      const el = document.getElementById('opt_ao_modal');
      if (!el) return;
      el.innerHTML = '<option value="">Semua AO</option>';
      if (!kodeKantor) return;

      try {
          const res = await apiCall(API_KODE_URL, { type: 'kode_ao_kredit', kode_kantor: kodeKantor });
          let h = '<option value="">Semua AO</option>';
          (res.data || []).forEach(x => {
              const kode = x.kode_group2 || '';
              const nama = x.nama_ao || kode;
              h += `<option value="${kode}">${nama}</option>`;
          });
          el.innerHTML = h;
      } catch (err) {
          console.error('Gagal load AO RR', err);
      }
  }

  async function populateKantor(uKode) {
    const el = document.getElementById('opt_kantor'); if(!el) return;
    if (uKode !== '000') { 
        try {
            const res = await apiCall(API_KODE_URL, { type:'kode_kantor' });
            const myKantor = (res.data||[]).find(x => String(x.kode_kantor).padStart(3,'0') === uKode);
            const nama = myKantor ? myKantor.nama_kantor : `CABANG ${uKode}`;
            el.innerHTML = `<option value="${uKode}">${uKode} - ${nama}</option>`;
        } catch(e) {
            el.innerHTML = `<option value="${uKode}">CABANG ${uKode}</option>`; 
        }
        el.value = uKode;
        el.disabled = true; 
        return; 
    }
    try {
        const res = await apiCall(API_KODE_URL, { type: 'kode_kantor' });
        let h = '<option value="">KONSOLIDASI</option>';
        if(res.data) res.data.filter(x => x.kode_kantor !== '000').forEach(x => { h += `<option value="${x.kode_kantor}">${x.kode_kantor} - ${x.nama_kantor}</option>`; });
        el.innerHTML = h;
    } catch { el.innerHTML = '<option value="">KONSOLIDASI</option>'; }
  }

  // 🔥 SETUP HEADER UTAMA (KUNCI NAMA KANTOR) 🔥
  function setupHeaderRR(userKode) {
      const th = document.getElementById('headRR');
      let thHtml = `<tr class="rr-row-1 text-[10px] md:text-sm">`;

      if (userKode === '000') {
          thHtml += `
            <th rowspan="2" class="hidden md:table-cell sticky-left-1 w-[60px] md:w-[80px] border-r border-b border-slate-200 align-middle bg-[#dcedc8] text-slate-800 text-center" onclick="sortData('kode', 'string')">
                <div class="flex items-center justify-center">KODE ${getSortIcon('kode', sortCol, sortAsc)}</div>
            </th>
            <th rowspan="2" class="sticky-left-2 min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px] border-r border-b border-white align-middle text-left pl-3 md:pl-5 bg-[#dcedc8] text-slate-800 truncate" onclick="sortData('nama', 'string')">
                <div class="flex items-center justify-start">NAMA KANTOR ${getSortIcon('nama', sortCol, sortAsc)}</div>
            </th>
          `;
      } else {
          thHtml += `
            <th rowspan="2" class="sticky-left-1 min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px] border-r border-b border-white align-middle text-left pl-3 md:pl-5 bg-[#dcedc8] text-slate-800 truncate" onclick="sortData('nama', 'string')">
                <div class="flex items-center justify-start">NAMA KANTOR ${getSortIcon('nama', sortCol, sortAsc)}</div>
            </th>
          `;
      }

      thHtml += `
            <th colspan="3" class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 align-middle bg-[#dcedc8] text-slate-800 text-[10px] md:text-sm text-center">M-1</th>
            <th colspan="3" class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 align-middle bg-[#dcedc8] text-slate-800 text-[10px] md:text-sm text-center">ACTUAL</th>
            <th colspan="3" class="px-2 md:px-4 py-1.5 md:py-2 border-b border-slate-200 align-middle bg-[#dcedc8] text-slate-800 text-[10px] md:text-sm text-center">DELTA</th>
          </tr>
          <tr class="rr-row-2 text-[8.5px] md:text-[10px] tracking-wider">
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('m1_lancar_os', 'number')">
                <div class="flex items-center justify-end">NOMINAL ${getSortIcon('m1_lancar_os', sortCol, sortAsc)}</div>
            </th>
            <th class="rr-col-noa px-2 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('m1_all_noa', 'number')">
                <div class="flex items-center justify-center">NOA ${getSortIcon('m1_all_noa', sortCol, sortAsc)}</div>
            </th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('m1_pct', 'number')">
                <div class="flex items-center justify-center">% ${getSortIcon('m1_pct', sortCol, sortAsc)}</div>
            </th>

            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('cur_lancar_os', 'number')">
                <div class="flex items-center justify-end">NOMINAL ${getSortIcon('cur_lancar_os', sortCol, sortAsc)}</div>
            </th>
            <th class="rr-col-noa px-2 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('cur_all_noa', 'number')">
                <div class="flex items-center justify-center">NOA ${getSortIcon('cur_all_noa', sortCol, sortAsc)}</div>
            </th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('cur_pct', 'number')">
                <div class="flex items-center justify-center">% ${getSortIcon('cur_pct', sortCol, sortAsc)}</div>
            </th>

            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('delta_os_lancar', 'number')">
                <div class="flex items-center justify-end">SELISIH NOMINAL ${getSortIcon('delta_os_lancar', sortCol, sortAsc)}</div>
            </th>
            <th class="rr-col-noa px-2 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('delta_noa', 'number')">
                <div class="flex items-center justify-center">SELISIH NOA ${getSortIcon('delta_noa', sortCol, sortAsc)}</div>
            </th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('delta_pct', 'number')">
                <div class="flex items-center justify-center">SELISIH % ${getSortIcon('delta_pct', sortCol, sortAsc)}</div>
            </th>
          </tr>
          <tr class="rr-row-tot font-bold text-[10px] md:text-sm bg-slate-100 sticky-total border-b border-slate-200" id="rowTotalRRAtas"></tr>
      `;
      th.innerHTML = thHtml;
  }

  function getTrafficLightColor(pct) {
      const value = Number(pct || 0);
      if (value < 60) return 'rr-pct-badge rr-pct-low';
      if (value <= 75) return 'rr-pct-badge rr-pct-mid';
      return 'rr-pct-badge rr-pct-high';
  }

  function renderRRPercent(pct) {
      const value = Number(pct || 0);
      const label = Number.isFinite(value)
          ? value.toLocaleString('id-ID', { maximumFractionDigits: 2 })
          : '0';
      return `<span class="${getTrafficLightColor(value)}">${label}%</span>`;
  }

  function rrMoveIcon(direction) {
      if (direction === 'up') {
          return `<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a1 1 0 0 1 .707.293l4 4a1 1 0 0 1-1.414 1.414L11 6.414V16a1 1 0 1 1-2 0V6.414L6.707 8.707a1 1 0 0 1-1.414-1.414l4-4A1 1 0 0 1 10 3Z" clip-rule="evenodd"/></svg>`;
      }
      return `<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 17a1 1 0 0 1-.707-.293l-4-4a1 1 0 0 1 1.414-1.414L9 13.586V4a1 1 0 1 1 2 0v9.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4A1 1 0 0 1 10 17Z" clip-rule="evenodd"/></svg>`;
  }

  function renderRRDelta(value, type = 'nominal') {
      const n = Number(value || 0);
      let label;
      if (type === 'pct') {
          label = `${Math.abs(n).toLocaleString('id-ID', { maximumFractionDigits: 2 })}%`;
      } else {
          label = fmt(Math.abs(n));
      }
      if (n < 0) return `<span class="rr-movement rr-movement-down">${rrMoveIcon('down')}<span>-${label}</span></span>`;
      if (n > 0) return `<span class="rr-movement rr-movement-up">${rrMoveIcon('up')}<span>+${label}</span></span>`;
      return `<span class="rr-movement rr-movement-flat"><span>${type === 'pct' ? '0%' : '0'}</span></span>`;
  }

  window.sortData = function(col, type) {
      if (!rekapDataCache || rekapDataCache.length === 0) return;

      if (sortCol === col) {
          sortAsc = !sortAsc;
      } else {
          sortCol = col;
          sortAsc = true;
      }

      rekapDataCache.sort((a, b) => {
          let valA = a[col];
          let valB = b[col];

          if (type === 'number') {
              valA = parseFloat(valA) || 0;
              valB = parseFloat(valB) || 0;
              return sortAsc ? valA - valB : valB - valA;
          } else {
              valA = String(valA || '').toLowerCase();
              valB = String(valB || '').toLowerCase();
              if (valA < valB) return sortAsc ? -1 : 1;
              if (valA > valB) return sortAsc ? 1 : -1;
              return 0;
          }
      });

      setupHeaderRR(userKodeGlobal);
      renderTableBodyRR(rekapDataCache, rekapGtCache);
  }

  document.getElementById('formFilter')?.addEventListener('submit', e => { e.preventDefault(); fetchRekap(); });

  async function fetchRekap() {
      const l = document.getElementById('loadingRekap');
      const tb = document.getElementById('bodyRekap');
      
      if(abortRekap) abortRekap.abort();
      abortRekap = new AbortController();

      l.classList.remove('hidden'); 
      
      const colSpan = userKodeGlobal === '000' ? 11 : 10;
      tb.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-20 text-slate-400 italic text-xs md:text-base">Sedang mengambil data...</td></tr>`;
      
      rekapDataCache = [];
      rekapGtCache = null;
      sortCol = ''; 
      sortAsc = true;

      try {
          const payload = {
              type: 'rr',
              closing_date: document.getElementById('closing_date').value,
              harian_date: document.getElementById('harian_date').value,
              kode_kantor: document.getElementById('opt_kantor').value || null,
              hitung_berdasarkan: getTipeSaldoRR()
          };

          const json = await apiCall(API_URL, payload, abortRekap.signal);
          if(json.status !== 200) throw new Error(json.message);

          rekapDataCache = (json.data?.data || []).map(row => recalcRRDisplayDelta({ ...row }));
          rekapGtCache = recalcRRDisplayDelta({ ...(json.data?.grand_total || {}) });

          setupHeaderRR(userKodeGlobal);

          if(rekapDataCache.length === 0) {
              tb.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-20 text-slate-400 italic text-xs md:text-base">Tidak ada data.</td></tr>`;
              return;
          }

          renderTableBodyRR(rekapDataCache, rekapGtCache);
          updateRRInsight();

      } catch(e) { 
          if(e.name!=='AbortError') {
              tb.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-16 text-red-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">Error: ${e.message}</td></tr>`;
          }
      } finally { l.classList.add('hidden'); }
  }

  function renderTableBodyRR(rows, gt) {
      const tb = document.getElementById('bodyRekap');
      const trTot = document.getElementById('rowTotalRRAtas');
      let html = '';

      rows.forEach(r => {

          const rowKode = attrRR(r.kode || '');
          const rowNama = attrRR(r.nama || '');

          /*
           * PENTING: ketika filter utama sudah memilih CABANG, baris rekap yang
           * dikirim API adalah level KANKAS. Sebelumnya rowKode selalu dianggap
           * sebagai kode_kantor untuk user pusat, sehingga payload detail menjadi
           * salah: kode_kantor=<kode kankas>, kode_kankas=null.
           *
           * Context yang benar:
           * - Konsolidasi pusat  : rowKode = kode cabang
           * - Cabang terpilih    : rowKode = kode kankas, kode_kantor = cabang filter
           * - Login cabang       : rowKode = kode kankas, kode_kantor = cabang login
           */
          const selectedMainBranch = String(document.getElementById('opt_kantor')?.value || '').trim();
          const branchContext = (selectedMainBranch && selectedMainBranch !== '000')
              ? selectedMainBranch
              : (userKodeGlobal !== '000' ? userKodeGlobal : '');
          const rowsAreKankas = branchContext !== '';
          const detailKode = rowsAreKankas ? attrRR(branchContext) : rowKode;
          const detailKankas = rowsAreKankas ? rowKode : '';

          const actualDetailAttr = `data-detail-rr="1" data-status="ALL" data-kode="${detailKode}" data-kankas="${detailKankas}" data-nama="${rowNama}" data-label="Actual" title="Klik detail Actual"`;
          const deltaDetailAttr = `data-detail-rr="1" data-status="TOTAL_BAYAR" data-kode="${detailKode}" data-kankas="${detailKankas}" data-nama="${rowNama}" data-label="Delta" title="Klik detail Delta"`;
          let rowHtml = `<tr class="transition h-[42px] md:h-[52px] border-b border-slate-100 hover:bg-slate-50">`;

          if (userKodeGlobal === '000') {
              rowHtml += `
                <td class="hidden md:table-cell sticky-left-1 px-2 md:px-4 py-2 border-r border-slate-100 font-semibold text-blue-700 z-20 shadow-[inset_-1px_0_0_#e2e8f0] text-center text-[10px] md:text-sm">${r.kode}</td>
                <td class="sticky-left-2 px-3 md:px-5 py-2 border-r border-slate-100 font-bold text-slate-700 text-left truncate z-20 shadow-[inset_-1px_0_0_#e2e8f0] text-[10px] md:text-sm min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px]" title="${attrRR(r.nama)}">${attrRR(r.nama)}</td>
              `;
          } else {
              rowHtml += `
                <td class="sticky-left-1 px-3 md:px-5 py-2 border-r border-slate-100 font-bold text-slate-700 text-left truncate z-20 shadow-[inset_-1px_0_0_#e2e8f0] text-[10px] md:text-sm min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px]" title="${attrRR(r.nama)}">${attrRR(r.nama)}</td>
              `;
          }

          rowHtml += `
                <td class="px-2 md:px-4 py-2 border-r border-slate-100 text-right font-semibold text-slate-700 text-[10px] md:text-sm">${fmt(r.m1_lancar_os)}</td>
                <td class="rr-col-noa px-2 py-2 border-r border-slate-100 text-center font-bold text-slate-600 text-[9px] md:text-xs">${fmt(r.m1_all_noa)}</td>
                <td class="px-2 md:px-4 py-2 border-r border-slate-100 text-center">${renderRRPercent(r.m1_pct)}</td>

                <td ${actualDetailAttr} class="px-2 md:px-4 py-2 border-r border-slate-100 text-right bg-blue-50/20 cursor-pointer hover:bg-blue-100/70 transition font-semibold text-blue-800 text-[10px] md:text-sm">${fmt(r.cur_lancar_os)}</td>
                <td ${actualDetailAttr} class="rr-col-noa px-2 py-2 border-r border-slate-100 text-center bg-blue-50/20 cursor-pointer hover:bg-blue-100/70 transition font-bold text-blue-600 text-[9px] md:text-xs">${fmt(r.cur_all_noa)}</td>
                <td ${actualDetailAttr} class="px-2 md:px-4 py-2 border-r border-slate-100 text-center bg-blue-50/20 cursor-pointer hover:bg-blue-100/70 transition">${renderRRPercent(r.cur_pct)}</td>

                <td ${deltaDetailAttr} class="px-2 md:px-4 py-2 border-r border-slate-100 text-right cursor-pointer hover:bg-amber-50 transition">${renderRRDelta(r.delta_os_lancar, 'nominal')}</td>
                <td ${deltaDetailAttr} class="rr-col-noa px-2 py-2 border-r border-slate-100 text-center cursor-pointer hover:bg-amber-50 transition">${renderRRDelta(r.delta_noa, 'noa')}</td>
                <td ${deltaDetailAttr} class="px-2 md:px-4 py-2 text-center cursor-pointer hover:bg-amber-50 transition">${renderRRDelta(r.delta_pct, 'pct')}</td>
            </tr>`;
          html += rowHtml;
      });
      tb.innerHTML = html;

      if(gt && Object.keys(gt).length > 0) {

          const selectedMainBranchTotal = String(document.getElementById('opt_kantor')?.value || '').trim();
          const totalKode = attrRR(
              selectedMainBranchTotal || (userKodeGlobal !== '000' ? userKodeGlobal : '')
          );
          const actualTotalAttr = `data-detail-rr="1" data-status="ALL" data-kode="${totalKode}" data-kankas="" data-nama="TOTAL" data-label="Actual Total" title="Klik detail Actual Total"`;
          const deltaTotalAttr = `data-detail-rr="1" data-status="TOTAL_BAYAR" data-kode="${totalKode}" data-kankas="" data-nama="TOTAL" data-label="Delta Total" title="Klik detail Delta Total"`;
          let gtHtml = '';
          if (userKodeGlobal === '000') {
              gtHtml += `
                  <th class="hidden md:table-cell sticky-left-1 px-2 md:px-4 border-r border-blue-200 text-center text-blue-900 bg-[#eff6ff] !important text-[10px] md:text-sm">-</th>
                  <th class="sticky-left-2 px-3 md:px-5 border-r border-blue-200 text-left text-blue-900 tracking-wide font-extrabold text-[11px] md:text-base bg-[#eff6ff] !important min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px] truncate" title="GRAND TOTAL">GRAND TOTAL</th>
              `;
          } else {
              gtHtml += `
                  <th class="sticky-left-1 px-3 md:px-5 border-r border-blue-200 text-left text-blue-900 tracking-wide font-extrabold text-[11px] md:text-base bg-[#eff6ff] !important min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px] truncate" title="TOTAL KANTOR">TOTAL KANTOR</th>
              `;
          }

          gtHtml += `
              <th class="px-2 md:px-4 border-r border-blue-200 text-right align-middle bg-[#eff6ff] font-bold text-[10px] md:text-sm text-blue-900">${fmt(gt.m1_lancar_os)}</th>
              <th class="rr-col-noa px-2 border-r border-blue-200 text-center align-middle bg-[#eff6ff] font-extrabold text-blue-700 text-[9px] md:text-xs">${fmt(gt.m1_all_noa)}</th>
              <th class="px-2 md:px-4 border-r border-blue-200 text-center align-middle bg-[#eff6ff]">${renderRRPercent(gt.m1_pct)}</th>

              <th ${actualTotalAttr} class="px-2 md:px-4 border-r border-blue-200 text-right align-middle bg-[#eff6ff] cursor-pointer hover:bg-blue-100 transition font-bold text-[10px] md:text-sm text-blue-900">${fmt(gt.cur_lancar_os)}</th>
              <th ${actualTotalAttr} class="rr-col-noa px-2 border-r border-blue-200 text-center align-middle bg-[#eff6ff] cursor-pointer hover:bg-blue-100 transition font-extrabold text-blue-700 text-[9px] md:text-xs">${fmt(gt.cur_all_noa)}</th>
              <th ${actualTotalAttr} class="px-2 md:px-4 border-r border-blue-200 text-center align-middle bg-[#eff6ff] cursor-pointer hover:bg-blue-100 transition">${renderRRPercent(gt.cur_pct)}</th>

              <th ${deltaTotalAttr} class="px-2 md:px-4 border-r border-blue-200 text-right align-middle bg-[#eff6ff] cursor-pointer hover:bg-amber-50 transition">${renderRRDelta(gt.delta_os_lancar, 'nominal')}</th>
              <th ${deltaTotalAttr} class="rr-col-noa px-2 border-r border-blue-200 text-center align-middle bg-[#eff6ff] cursor-pointer hover:bg-amber-50 transition">${renderRRDelta(gt.delta_noa, 'noa')}</th>
              <th ${deltaTotalAttr} class="px-2 md:px-4 text-center align-middle bg-[#eff6ff] cursor-pointer hover:bg-amber-50 transition">${renderRRDelta(gt.delta_pct, 'pct')}</th>
          `;
          trTot.innerHTML = gtHtml;
          trTot.classList.remove('cursor-pointer');
          trTot.title = '';
          trTot.onclick = null;
      }
  }

  window.exportExcelRekap = function() {
      if(!rekapDataCache || rekapDataCache.length === 0) return alert("Tidak ada data rekap untuk didownload.");

      let csv = "";
      if (userKodeGlobal === '000') {
          csv = `Kode\tNama Kantor\tM-1 NOMINAL\tM-1 NOA\tM-1 %\tActual NOMINAL\tActual NOA\tActual %\tSelisih Nominal\tSelisih NOA\tSelisih %\n`;
      } else {
          csv = `Nama Kantor\tM-1 NOMINAL\tM-1 NOA\tM-1 %\tActual NOMINAL\tActual NOA\tActual %\tSelisih Nominal\tSelisih NOA\tSelisih %\n`;
      }
      
      rekapDataCache.forEach(r => {
          if (userKodeGlobal === '000') {
              csv += `'${r.kode}\t${r.nama||''}\t`;
          } else {
              csv += `${r.nama||''}\t`;
          }
          csv += `${Math.round(r.m1_lancar_os)}\t${r.m1_all_noa}\t${r.m1_pct}%\t${Math.round(r.cur_lancar_os)}\t${r.cur_all_noa}\t${r.cur_pct}%\t${Math.round(r.delta_os_lancar)}\t${r.delta_noa}\t${r.delta_pct}%\n`;
      });

      const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      a.download = `Rekap_RR_${userKodeGlobal}_${document.getElementById("harian_date").value}.xls`; 
      a.click();
  }

  // ==========================================
  // 🔥 MODAL DETAIL LOGIC 🔥
  // ==========================================
  function formatWA(phone) {
      if (!phone) return null;
      let cleaned = phone.replace(/\D/g, ''); 
      if (cleaned.startsWith('0')) { cleaned = '62' + cleaned.substring(1); } 
      else if (cleaned.startsWith('8')) { cleaned = '62' + cleaned; }
      if (cleaned.length < 10) return null;
      return cleaned;
  }

  function createWABtn(phone, nama, norek, totung) {
      const formatted = formatWA(phone);
      if (!formatted) return `<span class="text-slate-400 font-mono text-[9px] md:text-sm">${phone || '-'}</span>`;
      
      // 🔥 FIX 5: Pesan di-comment, langsung redirect ke WA murni 🔥
      // const msg = `Yth. Bapak/Ibu *${nama}*,\n\nKami menginformasikan bahwa terdapat tagihan angsuran kredit pada rekening *${norek}* dengan total tunggakan sebesar *Rp ${fmt(totung)}*.\n\nMohon untuk segera melakukan pembayaran angsuran.\n\n_(Jika Bapak/Ibu sudah melakukan pembayaran, mohon abaikan pesan ini)_\n\nTerima kasih.`;
      // const waUrl = `https://wa.me/${formatted}?text=${encodeURIComponent(msg)}`;
      const waUrl = `https://wa.me/${formatted}`;
      
      return `
          <a href="${waUrl}" target="_blank" class="inline-flex items-center gap-1 md:gap-1.5 px-2 md:px-3 py-1 md:py-1.5 bg-emerald-50 hover:bg-emerald-500 hover:text-white text-emerald-600 rounded-md md:rounded-lg border border-emerald-200 transition font-bold text-[10px] md:text-xs" title="Hubungi WhatsApp">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="md:w-[16px] md:h-[16px]"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.319-.883-.665-1.479-1.488-1.653-1.787-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
              WA
          </a>
      `;
  }

  function getTglBayarRR(row) {
      return row?.tgl_bayar_ini || '-';
  }

  function rrSummaryNumber(obj, keys, fallback = 0) {
      if (!obj || typeof obj !== 'object') return fallback;
      for (const key of keys) {
          if (obj[key] !== undefined && obj[key] !== null && obj[key] !== '') {
              const n = Number(obj[key]);
              if (Number.isFinite(n)) return n;
          }
      }
      return fallback;
  }

  function setRRDetailSummaryCard(index, label, value) {
      const labelEl = document.getElementById(`rrSummaryLabel${index}`);
      const valueEl = document.getElementById(`rrSummaryValue${index}`);
      if (labelEl) labelEl.textContent = label;
      if (valueEl) valueEl.textContent = value;
  }

  function renderRRDetailSummary(rows = [], totalRecords = 0, summary = null, page = 1) {
      const list = Array.isArray(rows) ? rows : [];
      const hasServerSummary = !!(summary && typeof summary === 'object' && Object.keys(summary).length);
      const scope = document.getElementById('rrDetailSummaryScope');
      const title = document.getElementById('rrDetailSummaryTitle');

      if (currentMode === 'LUNAS') {
          const osM1Page = list.reduce((a,r) => a + Number(r.os_lunas || 0), 0);
          const plafonBaruPage = list.reduce((a,r) => a + Number(r.plafond_baru || 0), 0);
          const refinancingPage = list.filter(r => String(r.status_lunas || '').toUpperCase().includes('REFINANC')).length;
          const osM1 = hasServerSummary ? rrSummaryNumber(summary,['os_lunas','total_os_lunas','os_m1'],osM1Page) : osM1Page;
          const plafonBaru = hasServerSummary ? rrSummaryNumber(summary,['plafond_baru','total_plafond_baru'],plafonBaruPage) : plafonBaruPage;
          const refinancing = hasServerSummary ? rrSummaryNumber(summary,['refinancing','total_refinancing'],refinancingPage) : refinancingPage;
          if (title) title.textContent = 'Ringkasan Pelunasan';
          setRRDetailSummaryCard(1,'Debitur',fmt(totalRecords || list.length));
          setRRDetailSummaryCard(2,'OS M-1',fmt(osM1));
          setRRDetailSummaryCard(3,'Plafond Baru',fmt(plafonBaru));
          setRRDetailSummaryCard(4,'Refinancing',fmt(refinancing));
      } else {
          const bayarPage = list.reduce((a,r) => a + Number(r.trx_bulan_ini || 0), 0);
          const actualPage = list.reduce((a,r) => a + Number(r.os_curr || 0), 0);
          const tunggakanPage = list.reduce((a,r) => a + Number(r.totung || 0), 0);
          const bayar = hasServerSummary ? rrSummaryNumber(summary,['trx_bulan_ini','total_trx_bulan_ini','total_bayar','bayar'],bayarPage) : bayarPage;
          const actual = hasServerSummary ? rrSummaryNumber(summary,['os_curr','total_os_curr','actual','total_actual'],actualPage) : actualPage;
          const tunggakan = hasServerSummary ? rrSummaryNumber(summary,['totung','total_totung','total_tunggakan','tunggakan'],tunggakanPage) : tunggakanPage;
          if (title) title.textContent = 'Ringkasan Detail RR';
          setRRDetailSummaryCard(1,'Debitur',fmt(totalRecords || list.length));
          setRRDetailSummaryCard(2,'Total Bayar',fmt(bayar));
          setRRDetailSummaryCard(3,'Actual',fmt(actual));
          setRRDetailSummaryCard(4,'Tunggakan',fmt(tunggakan));
      }

      if (scope) scope.textContent = hasServerSummary ? 'Total sesuai filter aktif' : `Nominal halaman ${page} • debitur seluruh filter`;
  }

  function renderModalHeaderRR() {
      const mHead = document.getElementById('headModalRR');
      
      if (currentMode === 'NORMAL') {
          mHead.innerHTML = `
              <tr class="modal-head-1 mod-row-1">
                  <th class="mod-freeze-rek hidden md:table-cell px-2 md:px-3 border-b border-r border-slate-300 rounded-tl-lg text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-start md:justify-center">REKENING ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="mod-freeze-nas px-2 md:px-4 border-b border-r border-slate-300 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start md:justify-center">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[200px] md:w-[350px] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start md:justify-center">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_hp', 'string')">
                      <div class="flex items-center justify-center">NO HP (WA) ${getSortIcon('no_hp', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kankas', 'string')">
                      <div class="flex items-center justify-center">KANKAS ${getSortIcon('kankas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[110px] md:w-[150px] text-center text-blue-700 cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_jatuh_tempo', 'string')">
                      <div class="flex items-center justify-center">TGL JT ${getSortIcon('tgl_jatuh_tempo', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="rr-col-date-pay px-2 md:px-3 border-b border-r border-slate-300 text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_bayar_ini', 'string')">
                      <div class="flex items-center justify-center">TGL BYR ${getSortIcon('tgl_bayar_ini', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[110px] md:w-[150px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_pembayaran_code', 'string')">
                      <div class="flex items-center justify-center">STATUS BAYAR ${getSortIcon('status_pembayaran_code', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('trx_bulan_ini', 'number')">
                      <div class="flex items-center justify-end">BAYAR ${getSortIcon('trx_bulan_ini', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[70px] md:w-[95px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('hari_menunggak_jt', 'number')">
                      <div class="flex items-center justify-center">HARI ${getSortIcon('hari_menunggak_jt', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('jml_pinjaman', 'number')">
                      <div class="flex items-center justify-end">PLAFOND ${getSortIcon('jml_pinjaman', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-blue-50 text-blue-700 border-b border-r border-blue-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-blue-100 transition select-none" onclick="sortDetailRR('os_m1', 'number')">
                      <div class="flex items-center justify-end">TARGET (M-1) ${getSortIcon('os_m1', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-green-50 text-green-700 border-b border-r border-green-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('os_curr', 'number')">
                      <div class="flex items-center justify-end">ACTUAL (CURR) ${getSortIcon('os_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-red-50 text-red-700 border-b border-r border-red-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-red-100 transition select-none" onclick="sortDetailRR('totung', 'number')">
                      <div class="flex items-center justify-end">TUNGGAKAN ${getSortIcon('totung', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[50px] md:w-[70px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('dpd_curr', 'number')">
                      <div class="flex items-center justify-center">DPD ${getSortIcon('dpd_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[100px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tabungan', 'number')">
                      <div class="flex items-center justify-end">TABUNGAN ${getSortIcon('tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_tabungan', 'string')">
                      <div class="flex items-center justify-center">STAT TAB ${getSortIcon('status_tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-slate-300 w-[100px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_ket', 'string')">
                      <div class="flex items-center justify-center">DLL ${getSortIcon('status_ket', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      } else {
          mHead.innerHTML = `
              <tr class="modal-head-1 mod-row-1">
                  <th class="mod-freeze-nas px-2 md:px-4 border-b border-r border-slate-300 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start md:justify-center">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[200px] md:w-[350px] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start md:justify-center">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[100px] md:w-[150px] text-center text-blue-700 cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-center">REK LAMA ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('plafon_lama', 'number')">
                      <div class="flex items-center justify-end">PLAFOND LAMA ${getSortIcon('plafon_lama', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-blue-50 text-blue-700 border-b border-r border-blue-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-blue-100 transition select-none" onclick="sortDetailRR('os_lunas', 'number')">
                      <div class="flex items-center justify-end">OS M-1 ${getSortIcon('os_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[80px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_lunas', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 bg-green-50 text-green-700 border-b border-r border-green-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('rek_baru', 'string')">
                      <div class="flex items-center justify-center">REK BARU ${getSortIcon('rek_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-green-50 text-green-700 border-b border-r border-green-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('plafond_baru', 'number')">
                      <div class="flex items-center justify-end">PLAFOND BARU ${getSortIcon('plafond_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 bg-green-50 text-green-700 border-b border-green-200 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('tgl_baru', 'string')">
                      <div class="flex items-center justify-center">TGL REALISASI ${getSortIcon('tgl_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      }
  }

  window.sortDetailRR = function(col, type) {
      if (!detailDataCache || detailDataCache.length === 0) return;

      if (sortDetailCol === col) {
          sortDetailAsc = !sortDetailAsc;
      } else {
          sortDetailCol = col;
          sortDetailAsc = true;
      }

      detailDataCache.sort((a, b) => {
          let valA = a[col];
          let valB = b[col];

          if (type === 'number') {
              valA = parseFloat(valA) || 0;
              valB = parseFloat(valB) || 0;
              return sortDetailAsc ? valA - valB : valB - valA;
          } else {
              valA = String(valA || '').toLowerCase();
              valB = String(valB || '').toLowerCase();
              if (valA < valB) return sortDetailAsc ? -1 : 1;
              if (valA > valB) return sortDetailAsc ? 1 : -1;
              return 0;
          }
      });

      renderModalHeaderRR();
      renderTableDetailBodyRR(detailDataCache);
  }

  async function initModalDetail(tgl, status, kodeKantor = null, namaArea = '', label = 'Detail', kodeKankasAwal = null) {
      currentMode = 'NORMAL';
      const modal = document.getElementById('modalDetailRR');
      const body = document.getElementById('bodyModalRR');
      const loading = document.getElementById('loadingModalRR');
      const branch = kodeKantor || document.getElementById('opt_kantor')?.value || null;

      // Tampilkan modal langsung. Jangan menunggu dropdown Kankas/AO selesai,
      // supaya detail tidak terasa "tidak muncul" saat endpoint kode lambat.
      modal?.classList.remove('hidden');
      loading?.classList.remove('hidden');
      if (body) body.innerHTML = `<tr><td colspan="19" class="py-20 text-center text-slate-400 font-bold">Menyiapkan detail...</td></tr>`;

      const titleArea = namaArea ? ` - ${namaArea}` : '';
      const title = document.getElementById('modalTitleRR');
      const subtitle = document.getElementById('modalSubTitleRR');
      if (title) title.textContent = `${label} Rekap RR${titleArea}`;
      if (subtitle) subtitle.textContent = `${getTipeSaldoLabelRR()} | Lancar = kolektibilitas L dan hari menunggak 0`;

      const search = document.getElementById('search_nasabah');
      if (search) search.value = '';
      if (document.getElementById('status_pembayaran_modal')) document.getElementById('status_pembayaran_modal').value = 'ALL';

      // Reset state dahulu agar request detail tidak membawa filter dari modal sebelumnya.
      currentDetailParams = {
          type: 'detail_rekap_rr',
          closing_date: document.getElementById('closing_date')?.value || '',
          harian_date: document.getElementById('harian_date')?.value || '',
          kode_kantor: branch,
          kode_kankas: kodeKankasAwal || null,
          kode_ao: null,
          tgl_tagih: tgl || 'ALL',
          status: status || 'ALL',
          hitung_berdasarkan: getTipeSaldoRR(),
          status_bayar: 'all',
          status_tunggakan: 'all',
          status_pembayaran: 'ALL',
          search: '',
          limit: detailLimit
      };

      sortDetailCol = '';
      sortDetailAsc = true;
      renderModalHeaderRR();
      renderRRDetailSummary([], 0, null, 1);

      // Dropdown hanya fasilitas filter. Kalau endpoint dropdown gagal,
      // data detail utama tetap harus bisa dimuat.
      await Promise.allSettled([
          loadKankasModalDropdown(branch),
          loadAOModalDropdown(branch)
      ]);

      const kankasEl = document.getElementById('opt_kankas_modal');
      if (kankasEl) {
          if (kodeKankasAwal && Array.from(kankasEl.options).some(o => o.value === kodeKankasAwal)) {
              kankasEl.value = kodeKankasAwal;
          }
          currentDetailParams.kode_kankas = kodeKankasAwal || kankasEl.value || null;
      }
      const aoEl = document.getElementById('opt_ao_modal');
      currentDetailParams.kode_ao = aoEl?.value || null;

      await loadDetailPage(1, { preserveInitialKankas: true });
  }
  window.initModalDetail = initModalDetail;

  async function initModalLunas(tgl) {
      currentMode = 'LUNAS';
      const modal = document.getElementById('modalDetailRR');
      const body = document.getElementById('bodyModalRR');
      const loading = document.getElementById('loadingModalRR');
      const branch = document.getElementById('opt_kantor')?.value || null;

      modal?.classList.remove('hidden');
      loading?.classList.remove('hidden');
      if (body) body.innerHTML = `<tr><td colspan="10" class="py-20 text-center text-slate-400 font-bold">Menyiapkan detail pelunasan...</td></tr>`;

      document.getElementById('modalTitleRR').textContent = `Detail Pelunasan (Tgl ${tgl})`;
      document.getElementById('modalSubTitleRR').textContent = `Cek Refinancing vs Prospek`;
      const search = document.getElementById('search_nasabah');
      if (search) search.value = '';

      currentDetailParams = {
          type: 'detail_lunas_rr',
          closing_date: document.getElementById('closing_date')?.value || '',
          harian_date: document.getElementById('harian_date')?.value || '',
          kode_kantor: branch,
          kode_kankas: null,
          kode_ao: null,
          tgl_tagih: tgl,
          search: '',
          limit: detailLimit
      };

      sortDetailCol = '';
      sortDetailAsc = true;
      renderModalHeaderRR();
      renderRRDetailSummary([], 0, null, 1);

      await Promise.allSettled([
          loadKankasModalDropdown(branch),
          loadAOModalDropdown(branch)
      ]);
      currentDetailParams.kode_kankas = document.getElementById('opt_kankas_modal')?.value || null;
      currentDetailParams.kode_ao = document.getElementById('opt_ao_modal')?.value || null;
      await loadDetailPage(1);
  }

  window.handleModalKankasChangeRR = function() {
      const kankasEl = document.getElementById('opt_kankas_modal');
      const aoEl = document.getElementById('opt_ao_modal');

      currentDetailParams.kode_kankas = kankasEl?.value || null;

      // AO pada modal berasal dari level cabang. Saat Kankas diganti, reset AO
      // agar filter AO lama tidak membuat kombinasi filter menjadi kosong.
      if (aoEl) aoEl.value = '';
      currentDetailParams.kode_ao = null;

      loadDetailPage(1);
  };

  let searchDetailTimerRR = null;
  window.filterTableDetail = function() {
      const input = document.getElementById("search_nasabah");
      currentDetailParams.search = input ? input.value.trim() : '';
      clearTimeout(searchDetailTimerRR);
      searchDetailTimerRR = setTimeout(() => loadDetailPage(1), 350);
  }

  async function loadDetailPage(page, options = {}) {
      const l = document.getElementById('loadingModalRR');
      const tb = document.getElementById('bodyModalRR');
      const info = document.getElementById('pageInfoRR');
      const prev = document.getElementById('btnPrevRR');
      const next = document.getElementById('btnNextRR');
      if (!tb) return;

      l?.classList.remove('hidden');
      tb.innerHTML = `<tr><td colspan="19" class="py-20 text-center text-slate-400 font-bold">Memuat detail...</td></tr>`;

      try {
          const kankasModal = document.getElementById('opt_kankas_modal');
          const aoModal = document.getElementById('opt_ao_modal');

          // Pada request pertama jangan menghapus kode kankas yang sudah dikirim
          // dari row utama hanya karena option dropdown belum tersedia.
          if (!options.preserveInitialKankas || kankasModal?.value) {
              currentDetailParams.kode_kankas = kankasModal?.value || null;
          }
          currentDetailParams.kode_ao = aoModal?.value || null;
          currentDetailParams.hitung_berdasarkan = getTipeSaldoRR();
          currentDetailParams.status_bayar = 'all';
          currentDetailParams.status_tunggakan = 'all';
          currentDetailParams.status_pembayaran = document.getElementById('status_pembayaran_modal')?.value || 'ALL';
          currentDetailParams.search = document.getElementById('search_nasabah')?.value?.trim() || currentDetailParams.search || '';

          const payload = { ...currentDetailParams, page: Number(page) || 1, limit: detailLimit };
          if (window.RR_DEBUG === true) {
              console.log('[RR] detail payload', payload);
          }
          const res = await apiCall(API_URL, payload);
          const statusCode = Number(res?.status ?? 200);
          if (statusCode !== 200) throw new Error(res?.message || 'Gagal memuat detail');

          // Dukungan dua bentuk response API:
          // {data:{data:[],pagination:{...}}} atau {data:[]}
          const dataNode = res?.data;
          const rows = Array.isArray(dataNode?.data)
              ? dataNode.data
              : (Array.isArray(dataNode) ? dataNode : []);
          const pagination = dataNode?.pagination || {};
          const detailSummary = dataNode?.grand_total || dataNode?.summary || dataNode?.total_summary || null;
          const totalRecords = Number(pagination.total_records ?? pagination.total ?? rows.length ?? 0);
          const totalPages = Math.max(1, Number(pagination.total_pages ?? pagination.last_page ?? 1));

          detailDataCache = rows;
          currentDetailPage = Number(page) || 1;
          currentDetailTotalPages = totalPages;

          renderModalHeaderRR();
          renderRRDetailSummary(rows, totalRecords, detailSummary, currentDetailPage);

          if (!rows.length) {
              const colspan = currentMode === 'NORMAL' ? 19 : 10;
              tb.innerHTML = `<tr><td colspan="${colspan}" class="py-20 px-4 text-center text-slate-500 italic text-xs md:text-sm">Tidak ada data detail untuk filter yang dipilih.</td></tr>`;
              if (info) info.innerText = `0 Data`;
          } else {
              sortDetailCol = '';
              sortDetailAsc = true;
              renderTableDetailBodyRR(rows);
              if (info) info.innerText = `Hal ${currentDetailPage} dari ${totalPages} (${fmt(totalRecords)} Data)`;
          }

          if (prev) prev.disabled = currentDetailPage <= 1;
          if (next) next.disabled = currentDetailPage >= totalPages;
      } catch (err) {
          console.error('RR detail error:', err, currentDetailParams);
          const colspan = currentMode === 'NORMAL' ? 19 : 10;
          tb.innerHTML = `<tr><td colspan="${colspan}" class="py-16 px-4 text-center text-red-500 font-bold text-[10px] md:text-sm">Gagal memuat detail: ${attrRR(err?.message || 'Unknown error')}</td></tr>`;
          if (info) info.innerText = 'Gagal memuat data';
          if (prev) prev.disabled = true;
          if (next) next.disabled = true;
      } finally {
          l?.classList.add('hidden');
      }
  }
  window.loadDetailPage = loadDetailPage;

  function getPaymentBadgeRR(r) {
      const code = r.status_pembayaran_code || '';
      const extra = code === 'TELAT' ? ` ${fmt(r.hari_telat)} hr` : (code === 'BELUM_BAYAR' ? ` ${fmt(r.hari_menunggak_jt)} hr` : '');
      if (code === 'OTP') return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 font-black">OTP</span>`;
      if (code === 'TELAT') return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 border border-orange-200 font-black">Telat${extra}</span>`;
      if (code === 'BELUM_JATUH_TEMPO') return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 border border-blue-200 font-black">Belum JT</span>`;
      if (code === 'BELUM_BAYAR') return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 border border-rose-200 font-black">Belum Bayar${extra}</span>`;
      return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-black">-</span>`;
  }

  function renderTableDetailBodyRR(list) {
      const tb = document.getElementById('bodyModalRR');
      let h = '';
      
      list.forEach(r => {
          const aoName = (r.nama_ao || '-').split(' ').slice(0, 2).join(' ');

          if(currentMode === 'NORMAL') {
              let badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">${r.status_ket}</span>`;
              if(r.status_ket==='LANCAR') badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-green-100 text-green-700 border border-green-200">LANCAR</span>`;
              else if(r.status_ket==='MENUNGGAK') badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-red-100 text-red-700 border border-red-200">MENUNGGAK</span>`;
              
              let statTabungan = `<span class="text-red-500 font-bold text-[10px] md:text-xs">Belum Aman</span>`;
              if(r.status_tabungan === 'Aman') statTabungan = `<span class="text-green-600 font-bold text-[10px] md:text-xs">Aman</span>`;

              const btnWa = createWABtn(r.no_hp, r.nama_nasabah, r.no_rekening, r.totung);
              const paymentBadge = getPaymentBadgeRR(r);
              const hariFollowUp = r.status_pembayaran_code === 'TELAT' ? (r.hari_telat || 0) : (r.hari_menunggak_jt || 0);

              // 🔥 ALAMAT FULL TANPA HARDCODE 25 CHAR 🔥
              const alamatLengkap = r.alamat || '-';

              h += `<tr class="transition border-b border-slate-100 h-[40px] md:h-[48px]">
                    <td class="mod-td-rekening hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 font-mono text-[9.5px] md:text-sm text-slate-600 shadow-[1px_0_0_#f1f5f9]">${r.no_rekening}</td>
                    <td class="mod-td-nasabah px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 font-bold text-slate-700 truncate shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] text-[9.5px] md:text-sm" title="${r.nama_nasabah}">${r.nama_nasabah}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-slate-500 text-[9.5px] md:text-sm truncate max-w-[200px] md:max-w-[350px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center">${btnWa}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-mono text-slate-500 text-[9px] md:text-sm">${r.kankas||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-center font-bold text-[9.5px] md:text-sm text-blue-700 truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-mono text-[9.5px] md:text-sm text-slate-500">${r.tgl_jatuh_tempo||'-'}</td>
                    <td class="rr-col-date-pay px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-mono text-[9px] md:text-xs text-slate-600">${getTglBayarRR(r)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center text-[9px] md:text-xs">${paymentBadge}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-bold text-blue-700 bg-blue-50/20 text-[9.5px] md:text-sm">${fmt(r.trx_bulan_ini)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-black text-[9.5px] md:text-sm ${hariFollowUp > 0 ? 'text-rose-600' : 'text-slate-500'}">${fmt(hariFollowUp)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-medium text-slate-600 text-[9.5px] md:text-sm">${fmt(r.jml_pinjaman)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-blue-100 text-right font-bold text-blue-700 bg-blue-50/30 text-[9.5px] md:text-sm">${fmt(r.os_m1)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-green-100 text-right font-bold text-green-700 bg-green-50/30 text-[9.5px] md:text-sm">${fmt(r.os_curr)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-red-100 text-right font-bold text-red-600 bg-red-50/30 text-[9.5px] md:text-sm">${fmt(r.totung)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-bold text-slate-700 text-[9.5px] md:text-sm">${r.dpd_curr}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-bold text-emerald-600 bg-emerald-50/10 text-[9.5px] md:text-sm">${fmt(r.tabungan)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center">${statTabungan}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-center">${badge}</td>
                </tr>`;
          } else {
              let badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">PROSPEK</span>`;
              if(r.status_lunas === 'REFINANCING / Top Up') badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-green-100 text-green-700 border border-green-200">REFINANCING</span>`;
              
              const alamatLengkap = r.alamat || '-';

              h += `<tr class="transition border-b border-slate-100 h-[40px] md:h-[48px]">
                    <td class="mod-td-nasabah px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 font-bold text-slate-700 truncate shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] text-[9.5px] md:text-sm">
                        ${r.nama_nasabah}
                        <div class="text-[8px] md:text-xs text-slate-400 font-mono mt-0.5 font-normal">ID: ${r.nasabah_id}</div>
                    </td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-slate-500 text-[9.5px] md:text-sm truncate max-w-[200px] md:max-w-[350px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-center font-bold text-[9.5px] md:text-sm text-blue-700 truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 font-mono text-[9.5px] md:text-sm text-center text-slate-600">${r.no_rekening}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-medium text-slate-600 bg-slate-50/50 text-[9.5px] md:text-sm">${fmt(r.plafon_lama)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-blue-100 text-right font-bold text-blue-700 bg-blue-50/30 text-[9.5px] md:text-sm">${fmt(r.os_lunas)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-center">${badge}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-green-100 font-mono text-[9.5px] md:text-sm text-center bg-green-50/30 text-green-800 font-bold">${r.rek_baru}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-green-100 text-right bg-green-50/30 text-green-700 font-bold text-[9.5px] md:text-sm">${fmt(r.plafond_baru)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center bg-green-50/30 text-[9.5px] md:text-sm font-medium text-green-700">${r.tgl_baru}</td>
                </tr>`;
          }
      });
      tb.innerHTML = h;
  }

  async function downloadExcelFull(evt) {
      const btn = evt?.currentTarget || evt?.target?.closest('button');
      if (!btn) return;
      const txt = btn.innerHTML;
      btn.innerHTML = `<span class="animate-spin inline-block h-3.5 w-3.5 md:h-5 md:w-5 border-2 border-white border-t-transparent rounded-full md:mr-2"></span><span class="hidden md:inline">...</span>`;
      btn.disabled = true;

      try {
          const kankasModal = document.getElementById('opt_kankas_modal').value;
          const aoModal = document.getElementById('opt_ao_modal');
          let kodeAoVal = currentDetailParams.kode_ao;
          if (aoModal) { kodeAoVal = aoModal.value; }

          const payload = {
              ...currentDetailParams,
              kode_kankas: kankasModal,
              kode_ao: kodeAoVal,
              hitung_berdasarkan: getTipeSaldoRR(),
              status_bayar: 'all',
              status_tunggakan: 'all',
              status_pembayaran: document.getElementById('status_pembayaran_modal')?.value || 'ALL',
              page: 1,
              limit: 10000
          };
          const res = await apiCall(API_URL, payload);
          if(res.status !== 200) throw new Error(res.message || 'Export gagal');
          const rows = res.data?.data || [];
          if(rows.length === 0) { alert("Tidak ada data untuk diexport"); return; }

          let csv = "";
          if(currentMode === 'NORMAL') {
              csv = `No Rekening\tNama Nasabah\tAlamat\tNo HP\tKankas\tNama AO\tTgl JT\tTgl Bayar\tStatus Pembayaran\tBayar Bulan Ini\tHari Telat\tHari Menunggak\tPlafond\tTarget (M-1)\tActual (Curr)\tTot Tunggakan\tDPD\tSaldo Tabungan\tStatus Tabungan\tStatus Tagih\n`;
              rows.forEach(r => {
                  csv += `'${r.no_rekening}\t${r.nama_nasabah}\t${r.alamat||''}\t'${r.no_hp||''}\t${r.kankas||''}\t${r.nama_ao}\t${r.tgl_jatuh_tempo}\t${getTglBayarRR(r)}\t${r.status_pembayaran||''}\t${Math.round(r.trx_bulan_ini||0)}\t${r.hari_telat||0}\t${r.hari_menunggak_jt||0}\t${Math.round(r.jml_pinjaman)}\t${Math.round(r.os_m1)}\t${Math.round(r.os_curr)}\t${Math.round(r.totung)}\t${r.dpd_curr}\t${Math.round(r.tabungan)}\t${r.status_tabungan}\t${r.status_ket}\n`;
              });
          } else {
              csv = `Nama Nasabah\tID Nasabah\tAlamat\tNama AO\tRek Lama\tPlafond Lama\tOS Lunas (M-1)\tStatus\tRek Baru\tPlafond Baru\tTgl Realisasi Baru\n`;
              rows.forEach(r => {
                  csv += `${r.nama_nasabah}\t'${r.nasabah_id}\t${r.alamat||''}\t${r.nama_ao}\t'${r.no_rekening}\t${Math.round(r.plafon_lama)}\t${Math.round(r.os_lunas)}\t${r.status_lunas}\t'${r.rek_baru}\t${Math.round(r.plafond_baru)}\t${r.tgl_baru}\n`;
              });
          }

          const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `RR_Detail_${currentMode}_${currentDetailParams.tgl_tagih}.xls`;
          document.body.appendChild(a); a.click(); document.body.removeChild(a);

      } catch(e) { console.error(e); alert("Gagal export data."); } 
      finally { btn.innerHTML = txt; btn.disabled = false; }
  }

  window.changePageDetail = (step) => { const n = currentDetailPage + step; if (n > 0 && n <= currentDetailTotalPages) loadDetailPage(n); }
  window.closeModalRR = () => {
      document.getElementById('modalDetailRR')?.classList.add('hidden');
      document.getElementById('loadingModalRR')?.classList.add('hidden');
  };
  document.addEventListener('keydown', e => { if(e.key === 'Escape') { closeModalRR(); closeRRInfo(); } });
</script>

<style>
  :root { --primary: #2563eb; --bg: #f8fafc; --text: #334155; }
  
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow: hidden; }
  
  /* === INPUTS === */
  .inp { 
      border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0 0.5rem; 
      font-size: 13px; background: #fff; width: 100%; height: 38px; 
      min-width: 0; transition: all 0.2s; outline: none; color: #334155;
  }
  .inp:focus { border-color: var(--primary); }
  .inp:disabled { background-color: #f1f5f9; color: #64748b; font-weight: 600; cursor: not-allowed; border-color: #e2e8f0; }
  
  input[type="date"] { position: relative; cursor: pointer; }
  input[type="date"]::-webkit-inner-spin-button,
  input[type="date"]::-webkit-calendar-picker-indicator {
      position: absolute; top: 0; left: 0; right: 0; bottom: 0;
      width: 100%; height: 100%; opacity: 0; cursor: pointer;
  }

  /* === ICON BUTTONS CLEAN === */
  .btn-icon { 
      width: 38px; height: 38px; border-radius: 8px; 
      background: var(--primary); color: white; border: none; cursor: pointer; 
      display: inline-flex; align-items: center; justify-content: center; 
      transition: 0.2s; flex-shrink: 0; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
  }
  .btn-icon:hover { background: #1d4ed8; transform: translateY(-1px); }

  /* === TABLE SCROLLER REKAP === */
  #poScroller {
      --col1: 60px;   
      --col2: 200px;  
      --po_headH: 60px; 
      position: relative; border: 1px solid #e2e8f0; border-radius: 8px; background: white;
      height: 100%; overflow: auto; -webkit-overflow-scrolling: touch; 
  }

  table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 11px; }
  th, td { white-space: nowrap; padding: 8px 10px; vertical-align: middle; }
  
  /* HEADER STYLES REKAP (2 BARIS) */
  #tabelPotensi thead th { 
      position: sticky; z-index: 60; background: #f1f5f9; color: #475569; 
      font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; font-size: 10px;
      border-bottom: 1px solid #cbd5e1; border-right: 1px solid #e2e8f0;
  }
  #tabelPotensi thead tr:first-child th { top: 0; border-bottom: 1px solid #cbd5e1; }
  #tabelPotensi thead tr:last-child th { top: 30px; }

  /* STICKY COLUMNS LOGIC */
  .sticky-left-1 { position: sticky; left: 0; z-index: 45; background: #fff; border-right: 1px solid #f1f5f9; width: var(--col1); min-width: var(--col1); max-width: var(--col1); text-align: center; }
  .sticky-left-2 { position: sticky; left: var(--col1); z-index: 44; background: #fff; border-right: 1px solid #e2e8f0; width: var(--col2); min-width: var(--col2); max-width: var(--col2); overflow: hidden; text-overflow: ellipsis; }

  #tabelPotensi thead th.sticky-left-1 { z-index: 70; background: #f1f5f9; }
  #tabelPotensi thead th.sticky-left-2 { z-index: 69; background: #f1f5f9; }

  /* TOTAL ROW STICKY */
  #poTotalRow td { position: sticky; top: var(--po_headH); z-index: 50; background: #eff6ff; color: #1e40af; font-weight: 700; border-bottom: 2px solid #bfdbfe; border-right: 1px solid #bfdbfe; box-shadow: 0 4px 6px -2px rgba(0,0,0,0.05); }
  #poTotalRow td.sticky-left-1 { z-index: 59; background: #eff6ff; }
  #poTotalRow td.sticky-left-2 { z-index: 58; background: #eff6ff; }

  #poBody td { background-color: #fff; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; color: #334155; }
  #poBody tr:hover td { background-color: #f8fafc; }

  /* MODAL DETAIL SCROLLER */
  #modalScroll { --colRek: 130px; --colNama: 180px; }
  #modalTablePO { width: 100%; min-width: 1900px; }
  #modalTablePO th { position: sticky; top: 0; z-index: 30; background: #f8fafc; padding: 10px 12px; border-bottom: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; }
  #modalTablePO td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 11px; }
  
  .modal-freeze-1 { position: sticky; left: 0; z-index: 35; background: #fff; border-right: 1px solid #e2e8f0; width: var(--colRek); }
  .modal-freeze-2 { position: sticky; left: var(--colRek); z-index: 34; background: #fff; border-right: 1px solid #e2e8f0; width: var(--colNama); }
  
  #modalTablePO th.modal-freeze-1 { z-index: 40; background: #f8fafc; }
  #modalTablePO th.modal-freeze-2 { z-index: 39; background: #f8fafc; }
  
  .modal-total-row td { position: sticky; top: 36px; z-index: 25; background: #eff6ff; color: #1e40af; font-weight: bold; border-bottom: 1px solid #bfdbfe; }
  .modal-total-row td.modal-freeze-1 { z-index: 38; background: #eff6ff; }
  .modal-total-row td.modal-freeze-2 { z-index: 37; background: #eff6ff; }
  
  /* Status Warna-Warni */
  .status-aman { color: #16a34a; font-weight: bold; background: #dcfce7; padding: 3px 8px; border-radius: 6px; }
  .status-jt { color: #ca8a04; font-weight: bold; background: #fef08a; padding: 3px 8px; border-radius: 6px; }
  .status-flow { color: #dc2626; font-weight: bold; background: #fee2e2; padding: 3px 8px; border-radius: 6px; }

  /* MOBILE RESPONSIVE */
  @media (max-width: 767px) {
      #filterForm { flex-wrap: wrap; justify-content: flex-end; gap: 8px; }
      .filter-box { flex: 1 1 30%; min-width: 100px; }
      #opt_kantor_rec { font-size: 11px; padding: 0 4px; }
      #closing_date, #harian_date { font-size: 11px; padding: 0 4px; text-align: center; width: 100%; }

      .sticky-left-1 { display: none !important; }
      .sticky-left-2 { left: 0 !important; z-index: 45 !important; min-width: 140px; max-width: 160px; white-space: normal; line-height: 1.2; }
      #tabelPotensi thead th.sticky-left-2 { z-index: 70 !important; }
      #poTotalRow td.sticky-left-2 { z-index: 65 !important; }
      #modalScroll { --colRek: 0px; --colNama: 120px; }
      .modal-freeze-1 { display: none; }
      .modal-freeze-2 { left: 0; }
  }


  /* === POTENSI NPL MODERN RESPONSIVE V2 === */
  * { box-sizing:border-box; }
  .hidden { display:none !important; }
  .po-page { display:flex; flex-direction:column; width:100%; height:calc(100vh - 64px); height:calc(100dvh - 64px); min-height:430px; padding:8px; gap:7px; overflow:hidden; background:#f8fafc; }
  .po-header { display:flex; align-items:center; justify-content:space-between; gap:12px; flex:none; padding:9px 11px; border:1px solid #dbe3ee; border-radius:12px; background:#fff; box-shadow:0 1px 3px rgba(15,23,42,.05); }
  .po-titlebar { display:flex; align-items:center; justify-content:space-between; min-width:0; }
  .po-title-wrap { display:flex; align-items:center; min-width:0; gap:9px; }
  .po-title-icon { display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; flex:0 0 38px; border-radius:10px; background:#2563eb; color:#fff; box-shadow:0 5px 12px rgba(37,99,235,.2); }
  .po-title-icon svg { width:19px; height:19px; }
  .po-title-copy { min-width:0; }
  .po-title-line { display:flex; align-items:center; min-width:0; gap:7px; }
  .po-title-line h1 { margin:0; color:#0f172a; font-size:18px; font-weight:900; line-height:1.1; white-space:nowrap; }
  .po-title-copy p { margin:3px 0 0; overflow:hidden; color:#64748b; font-size:9px; font-weight:650; line-height:1.2; text-overflow:ellipsis; white-space:nowrap; }
  .po-unit-badge { display:inline-flex; align-items:center; height:20px; padding:0 7px; border:1px solid #bfdbfe; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-size:8px; font-weight:900; text-transform:uppercase; white-space:nowrap; }
  .po-mobile-actions,.po-filter-toggle { display:none; }
  .po-filter-form { display:grid; grid-template-columns:220px 122px 122px auto; align-items:end; gap:7px; min-width:0; }
  .po-field { display:flex; min-width:0; flex-direction:column; }
  .po-field > span { margin:0 0 3px 1px; color:#64748b; font-size:7px; font-weight:900; letter-spacing:.04em; line-height:1; text-transform:uppercase; white-space:nowrap; }
  .po-header .inp { height:34px; border-radius:8px; padding:0 9px; font-size:10px; font-weight:700; }
  .po-filter-actions { display:flex; align-items:center; gap:6px; }
  .po-action-btn { display:inline-flex; align-items:center; justify-content:center; width:36px; height:34px; border:0; border-radius:8px; color:#fff; cursor:pointer; transition:.16s ease; }
  .po-action-btn:hover { transform:translateY(-1px); }
  .po-action-search { background:#2563eb; }
  .po-action-excel { background:#059669; }

  #poScroller { flex:1; min-height:0; border-radius:9px; }
  #tabelPotensi { width:100%; min-width:0; table-layout:fixed; }
  #tabelPotensi col.po-col-code { width:4%; }
  #tabelPotensi col.po-col-name { width:15%; }
  #tabelPotensi col.po-col-noa { width:4%; }
  #tabelPotensi col.po-col-money { width:12.2%; }
  #tabelPotensi th,#tabelPotensi td { height:36px; padding:5px 5px; overflow:hidden; font-size:9px; text-overflow:ellipsis; }
  #tabelPotensi thead th { font-size:8px; letter-spacing:.015em; }
  #tabelPotensi .sticky-left-1 { width:auto; min-width:0; max-width:none; }
  #tabelPotensi .sticky-left-2 { width:auto; min-width:0; max-width:none; }

  .po-modal { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:12px; background:rgba(15,23,42,.68); backdrop-filter:blur(7px); }
  .po-modal-card { display:flex; flex-direction:column; width:min(1760px,calc(100vw - 24px)); height:min(94dvh,920px); overflow:hidden; border:1px solid rgba(226,232,240,.9); border-radius:16px; background:#fff; box-shadow:0 30px 80px rgba(15,23,42,.32); }
  .po-modal-header { display:flex; align-items:center; justify-content:space-between; gap:10px; flex:none; padding:10px 12px; border-bottom:1px solid #e2e8f0; background:linear-gradient(180deg,#fff,#f8fafc); }
  .po-modal-title-wrap { display:flex; align-items:center; min-width:0; gap:9px; }
  .po-modal-icon { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; flex:0 0 36px; border:1px solid #bfdbfe; border-radius:9px; background:#eff6ff; color:#2563eb; }
  .po-modal-title-copy { min-width:0; }
  .po-modal-title-copy h3 { margin:0; overflow:hidden; color:#0f172a; font-size:17px; font-weight:900; line-height:1.15; text-overflow:ellipsis; white-space:nowrap; }
  .po-modal-title-copy p { margin:3px 0 0; color:#64748b; font-size:9px; font-weight:650; }
  .po-modal-actions { display:flex; align-items:center; gap:6px; flex:none; }
  .po-modal-btn,.po-modal-close { display:inline-flex; align-items:center; justify-content:center; height:34px; border:0; border-radius:8px; cursor:pointer; transition:.15s ease; }
  .po-modal-btn { gap:5px; padding:0 11px; color:#fff; font-size:9px; font-weight:850; }
  .po-modal-btn.primary { background:#2563eb; }
  .po-modal-btn.excel { background:#059669; }
  .po-modal-close { width:34px; border:1px solid #fecaca; background:#fff1f2; color:#e11d48; }
  .po-modal-btn:hover,.po-modal-close:hover { transform:translateY(-1px); }
  .po-modal-toolbar { display:grid; grid-template-columns:minmax(220px,1fr) 155px 145px 155px 155px; align-items:center; gap:6px; flex:none; padding:7px 10px; border-bottom:1px solid #e2e8f0; background:#fff; }
  .po-modal-search { position:relative; min-width:0; }
  .po-modal-search svg { position:absolute; left:9px; top:50%; width:14px; height:14px; color:#94a3b8; transform:translateY(-50%); pointer-events:none; }
  .po-modal-search input,.po-modal-select { width:100%; height:32px; min-width:0; border:1px solid #cbd5e1; border-radius:8px; background:#fff; color:#334155; font-size:9px; font-weight:650; outline:none; }
  .po-modal-search input { padding:0 9px 0 29px; }
  .po-modal-select { padding:0 8px; }
  .po-modal-search input:focus,.po-modal-select:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.09); }
  .po-detail-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:6px; flex:none; padding:7px 10px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
  .po-summary-item { min-width:0; padding:6px 8px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; }
  .po-summary-item span { display:block; overflow:hidden; color:#64748b; font-size:6.5px; font-weight:900; letter-spacing:.035em; text-overflow:ellipsis; text-transform:uppercase; white-space:nowrap; }
  .po-summary-item strong { display:block; margin-top:2px; overflow:hidden; color:#0f172a; font-size:12px; font-weight:900; text-overflow:ellipsis; white-space:nowrap; }
  .po-summary-item.blue strong { color:#1d4ed8; } .po-summary-item.orange strong { color:#c2410c; } .po-summary-item.green strong { color:#047857; }
  .po-modal-content { position:relative; flex:1; min-height:0; overflow:auto; background:#fff; -webkit-overflow-scrolling:touch; }
  #modalTablePO { width:max-content; min-width:2540px; table-layout:fixed; }
  #modalTablePO th { height:36px; padding:5px 7px; font-size:8px; }
  #modalTablePO td { height:36px; padding:5px 7px; font-size:9px; }
  #modalTablePO th:nth-child(1),#modalTablePO td:nth-child(1){width:128px;min-width:128px;max-width:128px}
  #modalTablePO th:nth-child(2),#modalTablePO td:nth-child(2){width:190px;min-width:190px;max-width:190px}
  #modalTablePO th:nth-child(3),#modalTablePO td:nth-child(3){width:190px}
  #modalTablePO th:nth-child(4),#modalTablePO td:nth-child(4){width:130px}
  #modalTablePO th:nth-child(5),#modalTablePO td:nth-child(5){width:120px}
  #modalTablePO th:nth-child(20),#modalTablePO td:nth-child(20){width:112px}
  #modalTablePO th:nth-child(21),#modalTablePO td:nth-child(21){width:130px}
  #modalTablePO th:nth-child(22),#modalTablePO td:nth-child(22){width:94px}
  #modalTablePO th:nth-child(23),#modalTablePO td:nth-child(23){width:115px}
  #modalTablePO th:nth-child(24),#modalTablePO td:nth-child(24){width:180px}
  .po-commit-badge { display:inline-flex; align-items:center; justify-content:center; min-height:20px; padding:2px 7px; border-radius:999px; font-size:7.5px; font-weight:850; white-space:nowrap; }
  .po-commit-badge.done { background:#dcfce7; color:#15803d; } .po-commit-badge.empty { background:#fff7ed; color:#c2410c; }
  .po-mobile-detail-list { display:none; }

  @media (max-width:1279px) {
    .po-header { align-items:stretch; flex-direction:column; }
    .po-filter-form { grid-template-columns:minmax(220px,1fr) 130px 130px auto; width:100%; padding-top:7px; border-top:1px solid #e2e8f0; }
  }
  @media (max-width:1023px) {
    #poScroller { overflow:auto; }
    #tabelPotensi { width:1180px; min-width:1180px; }
    #tabelPotensi col.po-col-code { width:52px; } #tabelPotensi col.po-col-name { width:170px; } #tabelPotensi col.po-col-noa { width:56px; } #tabelPotensi col.po-col-money { width:136px; }
    .po-modal-toolbar { grid-template-columns:minmax(180px,1fr) repeat(4,135px); overflow-x:auto; }
  }
  @media (max-width:767px) {
    body { overflow:hidden; }
    .po-page { height:calc(100dvh - 54px); min-height:0; padding:4px; gap:4px; }
    .po-header { gap:7px; padding:7px 8px; border-radius:9px; }
    .po-title-icon { width:31px; height:31px; flex-basis:31px; border-radius:8px; }
    .po-title-icon svg { width:16px; height:16px; }
    .po-title-line h1 { font-size:13px; }
    .po-title-copy p { max-width:185px; margin-top:2px; font-size:7px; }
    .po-unit-badge { height:17px; padding:0 5px; font-size:6.5px; }
    .po-mobile-actions { display:flex; align-items:center; gap:7px; margin-left:auto; }
    .po-filter-toggle { display:inline-flex; align-items:center; justify-content:center; gap:5px; height:29px; padding:0 9px; border:1px solid #cbd5e1; border-radius:7px; background:#fff; color:#334155; font-size:8px; font-weight:850; }
    .po-filter-form { display:none; grid-template-columns:minmax(0,1fr) minmax(0,1fr) 34px; gap:5px; padding-top:7px; }
    .po-filter-form.is-open { display:grid; }
    .po-field-office { grid-column:1/3; }
    .po-filter-actions { grid-column:3; grid-row:1/3; align-self:end; flex-direction:column; }
    .po-action-btn { width:34px; height:32px; }
    .po-header .inp { height:32px; padding:0 6px; font-size:9px; }
    .po-field > span { font-size:6.5px; }
    #tabelPotensi { width:920px; min-width:920px; }
    #tabelPotensi col.po-col-code { display:none; }
    #tabelPotensi .sticky-left-1 { display:none !important; }
    #tabelPotensi col.po-col-name { width:116px; }
    #tabelPotensi col.po-col-noa { width:42px; }
    #tabelPotensi col.po-col-money { width:116px; }
    #tabelPotensi .sticky-left-2 { left:0; width:116px; min-width:116px; max-width:116px; white-space:normal; line-height:1.1; }
    #tabelPotensi th,#tabelPotensi td { height:34px; padding:4px; font-size:8px; }
    #tabelPotensi thead th { font-size:6.8px; }

    .po-modal { align-items:flex-end; padding:0; }
    .po-modal-card { width:100%; height:96dvh; max-height:96dvh; border-right:0; border-bottom:0; border-left:0; border-radius:16px 16px 0 0; }
    .po-modal-header { padding:8px 9px; }
    .po-modal-icon { width:30px; height:30px; flex-basis:30px; border-radius:8px; }
    .po-modal-title-copy h3 { font-size:13px; }
    .po-modal-title-copy p { font-size:7.5px; }
    .po-modal-actions { gap:4px; }
    .po-modal-btn { width:30px; height:30px; padding:0; }
    .po-modal-btn span { display:none; }
    .po-modal-close { width:30px; height:30px; }
    .po-modal-toolbar { grid-template-columns:minmax(0,1fr) minmax(115px,38%); gap:5px; padding:6px 8px; overflow:visible; }
    .po-modal-search { grid-column:1/-1; }
    .po-modal-search input,.po-modal-select { height:30px; font-size:8px; }
    #modalFilterKankas,#modalFilterAo { display:none; }
    .po-detail-summary { grid-template-columns:repeat(4,minmax(104px,1fr)); gap:5px; padding:6px 8px; overflow-x:auto; scrollbar-width:none; }
    .po-detail-summary::-webkit-scrollbar { display:none; }
    .po-summary-item { padding:5px 7px; }
    .po-summary-item span { font-size:5.5px; } .po-summary-item strong { font-size:9px; }
    .po-modal-content { overflow-y:auto; overflow-x:hidden; background:#f8fafc; }
    #modalTablePO { display:none; }
    .po-mobile-detail-list { display:grid; gap:7px; padding:7px 8px 22px; }
    .po-detail-card { overflow:hidden; border:1px solid #e2e8f0; border-radius:10px; background:#fff; box-shadow:0 1px 2px rgba(15,23,42,.04); }
    .po-detail-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:7px; padding:7px 8px 6px; border-bottom:1px solid #f1f5f9; }
    .po-detail-card-name { overflow:hidden; color:#0f172a; font-size:10px; font-weight:900; text-overflow:ellipsis; white-space:nowrap; }
    .po-detail-card-rek { margin-top:2px; color:#64748b; font-family:ui-monospace,monospace; font-size:7px; font-weight:800; }
    .po-detail-card-address { margin-top:2px; overflow:hidden; color:#64748b; font-size:7px; text-overflow:ellipsis; white-space:nowrap; }
    .po-detail-card-metrics { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1px; background:#e2e8f0; }
    .po-detail-card-metric { min-width:0; padding:6px 7px; background:#fff; }
    .po-detail-card-metric span { display:block; color:#64748b; font-size:5.5px; font-weight:900; text-transform:uppercase; }
    .po-detail-card-metric strong { display:block; margin-top:2px; overflow:hidden; color:#0f172a; font-size:8.5px; font-weight:900; text-overflow:ellipsis; white-space:nowrap; }
    .po-detail-card-commit { padding:6px 8px 7px; }
    .po-detail-card-commit-top { display:flex; align-items:center; justify-content:space-between; gap:7px; }
    .po-detail-card-commit-money { color:#1d4ed8; font-size:8.5px; font-weight:900; }
    .po-detail-card-meta { display:grid; grid-template-columns:auto minmax(0,1fr); gap:3px 7px; margin-top:5px; color:#64748b; font-size:6.8px; }
    .po-detail-card-meta b { color:#334155; font-weight:900; }
    .po-detail-card-meta span { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  }


  /* === POTENSI NPL REPORT POLISH V3 === */
  #poScroller {
      --col1:52px;
      --col2:184px;
      overflow-x:hidden;
      border:1px solid #dbe3ee;
      border-radius:12px;
      box-shadow:0 1px 3px rgba(15,23,42,.05);
      scrollbar-gutter:stable;
      overscroll-behavior:contain;
  }
  #poScroller::-webkit-scrollbar,
  .po-modal-content::-webkit-scrollbar { width:7px; height:7px; }
  #poScroller::-webkit-scrollbar-track,
  .po-modal-content::-webkit-scrollbar-track { background:#f8fafc; }
  #poScroller::-webkit-scrollbar-thumb,
  .po-modal-content::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }

  #tabelPotensi { width:100%; min-width:0; table-layout:fixed; }
  #tabelPotensi col.po-col-code { width:var(--col1); }
  #tabelPotensi col.po-col-name { width:var(--col2); }
  #tabelPotensi col.po-col-noa { width:48px; }
  #tabelPotensi col.po-col-money { width:auto; }
  #tabelPotensi .sticky-left-1 {
      width:var(--col1); min-width:var(--col1); max-width:var(--col1); left:0;
  }
  #tabelPotensi .sticky-left-2 {
      width:var(--col2); min-width:var(--col2); max-width:var(--col2); left:var(--col1);
      box-shadow:4px 0 8px -7px rgba(15,23,42,.85);
  }
  #tabelPotensi th,#tabelPotensi td { height:38px; padding:5px 6px; }
  #tabelPotensi thead tr:first-child th { height:40px; }
  #tabelPotensi thead tr:last-child th { top:40px; height:32px; }
  #tabelPotensi thead th { border-right:1px solid #dbe3ee; }
  #tabelPotensi tbody tr:nth-child(even) td { background:#fbfdff; }
  #tabelPotensi tbody tr:hover td { background:#eff6ff; }
  #tabelPotensi tbody tr:nth-child(even) td.sticky-left-1,
  #tabelPotensi tbody tr:nth-child(even) td.sticky-left-2 { background:#fbfdff; }
  #tabelPotensi tbody tr:hover td.sticky-left-1,
  #tabelPotensi tbody tr:hover td.sticky-left-2 { background:#eff6ff; }

  .po-group-head { position:relative; padding:5px 4px !important; border-top:3px solid transparent; }
  .po-group-head > span { display:block; font-size:8px; font-weight:900; line-height:1.05; }
  .po-group-head > small { display:block; margin-top:3px; font-size:5.7px; font-weight:750; letter-spacing:0; text-transform:none; opacity:.72; }
  .po-group-total { border-top-color:#2563eb !important; background:#eff6ff !important; color:#1d4ed8 !important; }
  .po-group-safe { border-top-color:#10b981 !important; background:#ecfdf5 !important; color:#047857 !important; }
  .po-group-due { border-top-color:#f59e0b !important; background:#fffbeb !important; color:#b45309 !important; }
  .po-group-flow { border-top-color:#ef4444 !important; background:#fff1f2 !important; color:#be123c !important; }
  .po-group-risk { border-top-color:#f97316 !important; background:#fff7ed !important; color:#c2410c !important; }

  .po-noa-link {
      display:inline-flex; align-items:center; justify-content:center; min-width:27px; min-height:22px;
      padding:2px 6px; border-radius:999px; text-decoration:none !important; transition:.15s ease;
  }
  .po-noa-link:hover { background:rgba(37,99,235,.09); transform:translateY(-1px); }
  #poTotalRow td { top:72px; height:40px; background:#eaf2ff; }

  .po-info-btn {
      display:inline-flex; align-items:center; justify-content:center; width:21px; height:21px; flex:0 0 21px;
      border:1px solid #bfdbfe; border-radius:999px; background:#eff6ff; color:#2563eb;
      font-size:11px; font-weight:950; cursor:pointer; transition:.15s ease;
  }
  .po-info-btn:hover { background:#dbeafe; transform:translateY(-1px); }

  .po-help-modal { position:fixed; inset:0; z-index:10050; display:flex; align-items:center; justify-content:center; padding:12px; background:rgba(15,23,42,.62); backdrop-filter:blur(6px); }
  .po-help-card { display:flex; flex-direction:column; width:min(680px,calc(100vw - 24px)); max-height:min(88dvh,760px); overflow:hidden; border:1px solid #e2e8f0; border-radius:16px; background:#fff; box-shadow:0 28px 75px rgba(15,23,42,.3); }
  .po-help-head { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:12px 14px; border-bottom:1px solid #e2e8f0; background:linear-gradient(180deg,#fff,#f8fafc); }
  .po-help-title { display:flex; align-items:center; min-width:0; gap:9px; }
  .po-help-title-icon { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; flex:0 0 34px; border-radius:9px; background:#eff6ff; color:#2563eb; }
  .po-help-title h3 { margin:0; color:#0f172a; font-size:16px; font-weight:900; }
  .po-help-title p { margin:2px 0 0; color:#64748b; font-size:9px; }
  .po-help-close { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border:1px solid #fecaca; border-radius:8px; background:#fff1f2; color:#e11d48; cursor:pointer; }
  .po-help-body { overflow:auto; padding:12px 14px 15px; }
  .po-help-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; }
  .po-help-item { min-width:0; padding:10px 11px; border:1px solid #e2e8f0; border-radius:10px; background:#fff; }
  .po-help-item-head { display:flex; align-items:center; gap:7px; color:#0f172a; font-size:11px; font-weight:900; }
  .po-help-dot { width:8px; height:8px; flex:0 0 8px; border-radius:999px; }
  .po-help-item p { margin:5px 0 0; color:#475569; font-size:10px; line-height:1.45; }
  .po-help-note { margin-top:9px; padding:9px 10px; border:1px solid #bfdbfe; border-radius:10px; background:#eff6ff; color:#1e3a8a; font-size:9.5px; line-height:1.45; }

  .po-detail-summary { grid-template-columns:repeat(5,minmax(0,1fr)); }
  .po-modal-toolbar { grid-template-columns:minmax(240px,1fr) 158px 150px 155px 155px; }
  .po-modal-card { width:min(1680px,calc(100vw - 24px)); }
  #modalTablePO { min-width:1760px; }
  #modalTablePO th,#modalTablePO td { border-right:1px solid #edf2f7; }
  #modalTablePO tbody tr:nth-child(even) td { background:#fbfdff; }
  #modalTablePO tbody tr:hover td { background:#eff6ff; }
  #modalTablePO .modal-freeze-2 { box-shadow:5px 0 9px -8px rgba(15,23,42,.9); }
  .po-stacked { display:flex; flex-direction:column; align-items:flex-end; gap:2px; line-height:1.05; }
  .po-stacked small { color:#64748b; font-size:7px; font-weight:750; }
  .po-stacked strong { color:#0f172a; font-size:9px; font-weight:850; }
  .po-kolek-shift { display:inline-flex; align-items:center; gap:4px; white-space:nowrap; }
  .po-kolek-chip { display:inline-flex; align-items:center; justify-content:center; min-width:25px; height:20px; padding:0 5px; border-radius:6px; font-size:7.5px; font-weight:900; }
  .po-kolek-chip.old { background:#f1f5f9; color:#475569; }
  .po-kolek-chip.new { background:#fff1f2; color:#be123c; }

  @media (min-width:1024px) {
      #poScroller { overflow-x:hidden; }
  }
  @media (max-width:1023px) {
      #poScroller { overflow:auto; }
      #tabelPotensi { width:1120px; min-width:1120px; }
      #tabelPotensi col.po-col-code { width:50px; }
      #tabelPotensi col.po-col-name { width:160px; }
      #tabelPotensi col.po-col-noa { width:50px; }
      #tabelPotensi col.po-col-money { width:132px; }
      #tabelPotensi .sticky-left-1 { width:50px; min-width:50px; max-width:50px; }
      #tabelPotensi .sticky-left-2 { left:50px; width:160px; min-width:160px; max-width:160px; }
  }
  @media (max-width:767px) {
      #poScroller { --col1:0px; --col2:112px; }
      #tabelPotensi { width:860px; min-width:860px; }
      #tabelPotensi col.po-col-code { display:none; }
      #tabelPotensi col.po-col-name { width:112px; }
      #tabelPotensi col.po-col-noa { width:38px; }
      #tabelPotensi col.po-col-money { width:112px; }
      #tabelPotensi .sticky-left-2 { left:0; width:112px; min-width:112px; max-width:112px; }
      #tabelPotensi th,#tabelPotensi td { height:33px; padding:3px 4px; }
      #tabelPotensi thead tr:first-child th { height:36px; }
      #tabelPotensi thead tr:last-child th { top:36px; height:29px; }
      #poTotalRow td { top:65px; }
      .po-group-head > span { font-size:6.5px; }
      .po-group-head > small { display:none; }
      .po-title-copy p { max-width:205px; }
      .po-help-modal { align-items:flex-end; padding:0; }
      .po-help-card { width:100%; max-height:88dvh; border-right:0; border-bottom:0; border-left:0; border-radius:16px 16px 0 0; }
      .po-help-grid { grid-template-columns:1fr; }
      .po-help-head { padding:10px 11px; }
      .po-help-body { padding:10px 11px 16px; }
      .po-detail-summary { grid-template-columns:repeat(5,minmax(104px,1fr)); }
  }

</style>

<div id="poPage" class="po-page">

  <header id="poHeader" class="po-header">
    <div class="po-titlebar">
      <div class="po-title-wrap">
        <span class="po-title-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.7 2.8 17a2 2 0 0 0 1.74 3h14.92a2 2 0 0 0 1.74-3L13.7 3.7a2 2 0 0 0-3.4 0Z"></path></svg>
        </span>
        <div class="po-title-copy">
          <div class="po-title-line">
            <h1>Potensi NPL</h1>
            <button type="button" id="btnInfoPotensi" class="po-info-btn" title="Panduan Potensi NPL" aria-label="Buka panduan Potensi NPL">i</button>
            <span id="badgeUnit" class="po-unit-badge">MEMUAT...</span>
          </div>
          <p>Monitoring kandidat NPL, status penyelamatan, dan komitmen pembayaran cabang.</p>
        </div>
      </div>
      <div class="po-mobile-actions">
        <div id="loadingMini" class="hidden animate-spin h-4 w-4 border-2 border-blue-600 border-t-transparent rounded-full"></div>
        <button type="button" id="btnTogglePOFilter" class="po-filter-toggle" aria-expanded="false">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
          <span>Filter</span>
        </button>
      </div>
    </div>

    <form id="filterForm" class="po-filter-form">
      <label class="po-field po-field-office">
        <span>Kantor</span>
        <select id="opt_kantor_rec" class="inp"><option value="">Memuat...</option></select>
      </label>
      <label class="po-field">
        <span>Closing (M-1)</span>
        <input type="date" id="closing_date" class="inp" required>
      </label>
      <label class="po-field">
        <span>Actual (Harian)</span>
        <input type="date" id="harian_date" class="inp" required>
      </label>
      <div class="po-filter-actions">
        <button type="submit" class="po-action-btn po-action-search" title="Terapkan filter" aria-label="Terapkan filter">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.6-3.6"></path></svg>
        </button>
        <button type="button" onclick="exportPotensiExcel()" class="po-action-btn po-action-excel" title="Export Excel" aria-label="Export Excel">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 21h14a2 2 0 0 0 2-2v-3"></path><path d="M3 16v3a2 2 0 0 0 2 2"></path></svg>
        </button>
      </div>
    </form>
  </header>

  <div class="flex-1 min-h-0 relative flex flex-col">
    <div id="loadingPO" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold backdrop-blur-sm rounded-lg">
       <div class="animate-spin h-10 w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-3"></div>
       <span class="text-sm tracking-wide">MEMUAT DATA...</span>
    </div>

    <div id="poScroller" class="table-wrapper shadow-sm">
      <table id="tabelPotensi">
        <colgroup>
          <col class="po-col-code"><col class="po-col-name">
          <col class="po-col-noa"><col class="po-col-money">
          <col class="po-col-noa"><col class="po-col-money">
          <col class="po-col-noa"><col class="po-col-money">
          <col class="po-col-noa"><col class="po-col-money">
          <col class="po-col-noa"><col class="po-col-money">
        </colgroup>
        <thead id="poHead1">
          <tr>
            <th class="sticky-left-1" rowspan="2">KODE</th>
            <th class="sticky-left-2" id="thNamaPO" rowspan="2">NAMA KANTOR</th>
            <th class="po-group-head po-group-total text-center" colspan="2"><span>TOTAL POTENSI NPL</span><small>Seluruh kandidat yang dipantau</small></th>
            <th class="po-group-head po-group-safe text-center" colspan="2"><span>AMAN / LUNAS</span><small>Selamat, membaik, backflow, atau lunas</small></th>
            <th class="po-group-head po-group-due text-center" colspan="2"><span>JATUH TEMPO</span><small>Perlu penyelesaian saat jatuh tempo</small></th>
            <th class="po-group-head po-group-flow text-center" colspan="2"><span>FLOW (KL/D/M)</span><small>Sudah masuk kolektibilitas NPL</small></th>
            <th class="po-group-head po-group-risk text-center" colspan="2"><span>MASIH POTENSI</span><small>Belum flow tetapi masih berisiko</small></th>
          </tr>
          <tr>
            <th class="text-center w-[60px] bg-blue-50 cursor-pointer hover:bg-blue-100 transition" onclick="doSort('total_noa')">NOA ⬍</th>
            <th class="text-right min-w-[120px] bg-blue-50 cursor-pointer hover:bg-blue-100 transition" onclick="doSort('total_baki')">BAKI DEBET ⬍</th>
            
            <th class="text-center w-[60px] bg-green-50">NOA</th><th class="text-right min-w-[120px] bg-green-50">BAKI DEBET</th>
            <th class="text-center w-[60px] bg-yellow-50">NOA</th><th class="text-right min-w-[120px] bg-yellow-50">BAKI DEBET</th>
            <th class="text-center w-[60px] bg-red-50">NOA</th><th class="text-right min-w-[120px] bg-red-50">BAKI DEBET</th>
            <th class="text-center w-[60px] bg-orange-50">NOA</th><th class="text-right min-w-[120px] bg-orange-50">BAKI DEBET</th>
          </tr>
        </thead>
        <tbody id="poTotalRow"></tbody>
        <tbody id="poBody"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="modalDebiturPotensi" class="po-modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitlePotensi">
  <div id="modalCardPO" class="po-modal-card">
    <header class="po-modal-header">
      <div class="po-modal-title-wrap">
        <span class="po-modal-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path><path d="M14 2v6h6"></path><path d="M8 13h8M8 17h6"></path></svg>
        </span>
        <div class="po-modal-title-copy">
          <h3 id="modalTitlePotensi">Detail Potensi NPL</h3>
          <p id="modalSubtitlePO">Posisi: -</p>
        </div>
      </div>
      <div class="po-modal-actions">
        <button type="button" onclick="gotoUpdatePotensiNPL()" class="po-modal-btn primary" title="Update komitmen" aria-label="Update komitmen">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg><span>Update</span>
        </button>
        <button type="button" onclick="exportDetailPotensiExcel()" class="po-modal-btn excel" title="Export Excel" aria-label="Export Excel">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 21h14a2 2 0 0 0 2-2v-3"></path><path d="M3 16v3a2 2 0 0 0 2 2"></path></svg><span>Excel</span>
        </button>
        <button id="btnClosePO" type="button" class="po-modal-close" title="Tutup" aria-label="Tutup">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
        </button>
      </div>
    </header>

    <section class="po-modal-toolbar">
      <label class="po-modal-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
        <input type="search" id="modalSearchPO" placeholder="Cari nama, rekening, AO, alamat..." autocomplete="off" oninput="renderDetailRows()">
      </label>
      <select id="modalFilterStatus" class="po-modal-select" onchange="handlePOStatusChange()">
        <option value="ALL">Semua Status Potensi</option>
        <option value="AMAN">Aman / Lunas / Backflow</option>
        <option value="JATUH TEMPO">Jatuh Tempo</option>
        <option value="FLOW KOLEK">Flow Kolek</option>
        <option value="MASIH POTENSI">Masih Potensi</option>
      </select>
      <select id="modalFilterCommitment" class="po-modal-select" onchange="renderDetailRows()">
        <option value="ALL">Semua Komitmen</option>
        <option value="SUDAH">Sudah Komitmen</option>
        <option value="BELUM">Belum Komitmen</option>
      </select>
      <select id="modalFilterKankas" class="po-modal-select" onchange="fetchDetailPotensiNpl()"><option value="">Semua Kankas</option></select>
      <select id="modalFilterAo" class="po-modal-select" onchange="fetchDetailPotensiNpl()"><option value="">Semua AO</option></select>
    </section>

    <section id="poDetailSummary" class="po-detail-summary">
      <div class="po-summary-item"><span>Data Terfilter</span><strong id="poSumCandidates">0</strong></div>
      <div class="po-summary-item green"><span>Aman / Lunas</span><strong id="poSumSafe">0</strong></div>
      <div class="po-summary-item blue"><span>Sudah Komitmen</span><strong id="poSumCommitted">0</strong></div>
      <div class="po-summary-item orange"><span>Belum Komitmen</span><strong id="poSumUncommitted">0</strong></div>
      <div class="po-summary-item green"><span>Nominal Janji</span><strong id="poSumPromise">Rp 0</strong></div>
    </section>

    <div class="po-modal-content" id="modalScroll">
      <table id="modalTablePO">
        <thead><tr>
          <th class="modal-freeze-1 text-center">No Rekening</th>
          <th class="modal-freeze-2 text-left">Debitur / Alamat / AO</th>
          <th class="text-center">Status Potensi</th>
          <th class="text-center">Kolek C → H</th>
          <th class="text-right">BD Closing</th>
          <th class="text-right">BD Harian</th>
          <th class="text-right">Tunggakan P / B</th>
          <th class="text-right">Saldo Tab</th>
          <th class="text-center">JT</th>
          <th class="text-center">DPD / P / B</th>
          <th class="text-right">Angsuran P / B</th>
          <th class="text-center">Status Komitmen</th>
          <th class="text-left">Komitmen</th>
          <th class="text-center">Tgl Janji</th>
          <th class="text-right">Nominal Janji</th>
          <th class="text-left">Alasan</th>
        </tr></thead>
        <tbody id="modalTotalRowPO"></tbody>
        <tbody id="modalBodyRowsPO"></tbody>
      </table>
      <div id="modalMobileCardsPO" class="po-mobile-detail-list"></div>
    </div>
  </div>
</div>

<div id="modalInfoPotensi" class="po-help-modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalInfoPotensiTitle">
  <div class="po-help-card">
    <header class="po-help-head">
      <div class="po-help-title">
        <span class="po-help-title-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5"></path><path d="M12 8h.01"></path></svg>
        </span>
        <div><h3 id="modalInfoPotensiTitle">Panduan Potensi NPL</h3><p>Kamus status dan cara menggunakan report untuk proyeksi NPL.</p></div>
      </div>
      <button type="button" id="btnCloseInfoPotensi" class="po-help-close" aria-label="Tutup panduan">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
      </button>
    </header>
    <div class="po-help-body">
      <div class="po-help-grid">
        <article class="po-help-item"><div class="po-help-item-head"><span class="po-help-dot" style="background:#2563eb"></span>Total Potensi NPL</div><p>Seluruh rekening kandidat yang dipantau dari posisi closing sampai posisi actual.</p></article>
        <article class="po-help-item"><div class="po-help-item-head"><span class="po-help-dot" style="background:#10b981"></span>Aman / Lunas</div><p>Rekening yang tidak menjadi NPL, termasuk tetap aman, membaik/backflow, atau sudah lunas.</p></article>
        <article class="po-help-item"><div class="po-help-item-head"><span class="po-help-dot" style="background:#f59e0b"></span>Jatuh Tempo</div><p>Rekening yang jatuh tempo dan perlu diselesaikan agar tidak masuk flow.</p></article>
        <article class="po-help-item"><div class="po-help-item-head"><span class="po-help-dot" style="background:#ef4444"></span>Flow (KL/D/M)</div><p>Rekening yang pada posisi actual sudah masuk kolektibilitas Kurang Lancar, Diragukan, atau Macet.</p></article>
        <article class="po-help-item"><div class="po-help-item-head"><span class="po-help-dot" style="background:#f97316"></span>Masih Potensi</div><p>Belum tercatat sebagai flow, tetapi masih memiliki risiko berdasarkan DPD, tunggakan, atau jatuh tempo.</p></article>
        <article class="po-help-item"><div class="po-help-item-head"><span class="po-help-dot" style="background:#6366f1"></span>Komitmen Cabang</div><p>Isikan rencana penyelesaian, tanggal janji bayar, nominal, dan alasan untuk memproyeksikan rekening yang terselamatkan serta yang berpotensi flow.</p></article>
      </div>
      <div class="po-help-note"><b>Cara menggunakan:</b> klik angka NOA pada report untuk membuka detail. Gunakan filter status dan komitmen, lalu klik <b>Update</b> untuk memperbarui komitmen debitur. Data lunas akan masuk kelompok Aman/Lunas apabila backend mengirim rekening closing yang sudah tidak ada pada posisi harian.</div>
    </div>
  </div>
</div>

<div id="modalPeringatan" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm items-center justify-center z-[9999] px-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden animate-scale-up">
    <div class="bg-red-50 p-4 border-b border-red-100 flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl shrink-0">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
      </div>
      <h3 class="font-bold text-red-800 text-lg">Akses Ditolak</h3>
    </div>
    <div class="p-6 text-center text-slate-600 text-sm">
      <p>Anda login sebagai <span class="font-bold text-blue-600 px-1 bg-blue-50 rounded" id="warnUserLvl">Cabang</span>.</p>
      <p class="mt-2">Anda tidak memiliki izin untuk melihat detail data milik <span class="font-bold text-red-600 px-1 bg-red-50 rounded" id="warnTargetLvl">Unit</span>.</p>
    </div>
    <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
      <button onclick="closeModalPeringatan()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold transition shadow-sm">Mengerti</button>
    </div>
  </div>
</div>

<script>
  // =========================================================
  // UTILS & FORMATTER
  // =========================================================
  const nfID = new Intl.NumberFormat('id-ID');
  const fmtNom = n => nfID.format(Number(n||0));
  const fmtInt = n => new Intl.NumberFormat("id-ID",{maximumFractionDigits:0}).format(+n||0);
  const num = v => Number(v||0);
  const kodeNum = v => Number(String(v??'').replace(/\D/g,'')||0);
  const formatDate = (s) => { if(!s) return '-'; const d=new Date(s); return isNaN(d)?'-': `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`; };

  const poEscape = value => String(value ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  const poCommitment = d => ({
      komitmen: d.komitmen || d.komitmen_dpd || '',
      tanggal: d.tgl_pembayaran || d.tgl_janji_bayar || '',
      nominal: num(d.nominal ?? d.nominal_janji_bayar ?? 0),
      alasan: d.alasan || d.alasan_keterlambatan || ''
  });
  const poHasCommitment = d => {
      const c = poCommitment(d);
      return String(c.komitmen).trim() !== '' || String(c.tanggal).trim() !== '' || c.nominal > 0;
  };

  function poNormalizeStatus(value) {
      return String(value ?? '').trim().toUpperCase().replace(/[_-]+/g,' ').replace(/\s+/g,' ');
  }

  function poStatusRaw(d) {
      return d?.status_potensi ?? d?.status_saat_ini ?? d?.status_update ?? d?.status ?? '';
  }

  function poStatusKind(d) {
      const status = poNormalizeStatus(poStatusRaw(d));
      const bdClosing = num(d?.baki_debet_closing ?? d?.baki_debet_awal ?? 0);
      const bdHarian = num(d?.baki_debet_harian ?? d?.baki_debet_actual ?? d?.baki_debet ?? 0);
      const explicitLunas = Number(d?.is_lunas ?? d?.lunas ?? 0) === 1;

      if (explicitLunas || status.includes('LUNAS') || status.includes('AMAN') || status.includes('BACKFLOW') || status.includes('SELAMAT') || status.includes('PERBAIKAN')) return 'AMAN';
      if (status.includes('FLOW') || status.includes('KL/D/M') || status.includes('NPL')) return 'FLOW KOLEK';
      if (status.includes('JATUH TEMPO') || status === 'JT') return 'JATUH TEMPO';
      if (bdClosing > 0 && bdHarian <= 0) return 'AMAN';
      return 'MASIH POTENSI';
  }

  function poStatusDisplay(d) {
      const raw = String(poStatusRaw(d) || '').trim();
      if (raw) return raw;
      if (poStatusKind(d) === 'AMAN' && num(d?.baki_debet_closing) > 0 && num(d?.baki_debet_harian) <= 0) return 'LUNAS / AMAN';
      return poStatusKind(d);
  }

  function startOfDay(d){ const x=new Date(d); x.setHours(0,0,0,0); return x; }
  function formatJTByRule(jt){ 
      if(!jt) return '-'; 
      const d = new Date(jt); 
      if(isNaN(d)) return '-'; 
      const today = startOfDay(new Date()); 
      const due = startOfDay(d); 
      if(due < today){ 
          const yyyy = d.getFullYear(); 
          const mm = String(d.getMonth()+1).padStart(2,'0'); 
          const dd = String(d.getDate()).padStart(2,'0'); 
          return `${yyyy}-${mm}-${dd}`; 
      } 
      return String(d.getDate()); 
  }

  // --- STATE GLOBAL ---
  window.poDataRaw = [];
  window.poGtRaw = null;
  let detailPoRaw = [];
  let detailPoFiltered = []; 
  let sortState = { col: null, dir: 1 };
  let currentFilter = { closing:'', harian:'' };
  let currentDetailKode = '';
  let currentDetailStatus = 'ALL'; 
  let poAbort;
  window.currentUserKode = '000';

  function updatePoStickyHeader() {
      const thead = document.getElementById('poHead1');
      const scroller = document.getElementById('poScroller');
      if(thead && scroller) {
          scroller.style.setProperty('--po_headH', (thead.offsetHeight - 1) + 'px');
      }
  }
  window.addEventListener('resize', updatePoStickyHeader);

  // =========================================================
  // INIT PAGE & USER LOGIN (ANTI BOCOR)
  // =========================================================

  function syncPOFilterButton() {
      const form = document.getElementById('filterForm');
      const btn = document.getElementById('btnTogglePOFilter');
      if(!form || !btn) return;
      const open = form.classList.contains('is-open');
      btn.setAttribute('aria-expanded', String(open));
      const label = btn.querySelector('span');
      if(label) label.textContent = open ? 'Tutup' : 'Filter';
  }
  document.getElementById('btnTogglePOFilter')?.addEventListener('click', () => {
      document.getElementById('filterForm')?.classList.toggle('is-open');
      syncPOFilterButton();
  });

  function openInfoPotensi() {
      document.getElementById('modalInfoPotensi')?.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
  }
  function closeInfoPotensi() {
      document.getElementById('modalInfoPotensi')?.classList.add('hidden');
      if (document.getElementById('modalDebiturPotensi')?.classList.contains('hidden')) document.body.style.overflow = '';
  }
  document.getElementById('btnInfoPotensi')?.addEventListener('click', openInfoPotensi);
  document.getElementById('btnCloseInfoPotensi')?.addEventListener('click', closeInfoPotensi);
  document.getElementById('modalInfoPotensi')?.addEventListener('click', e => { if (e.target.id === 'modalInfoPotensi') closeInfoPotensi(); });

  window.addEventListener('DOMContentLoaded', async () => {
      // 1. TANGKAP USER LOGIN DENGAN BENAR
      let k = '000';
      try {
          if (typeof window.getUser === 'function' && window.getUser()) {
              let u = window.getUser();
              k = u.kode || u.kode_kantor || u.kode_cabang || '000';
          } else if (localStorage.getItem('app_user')) {
              let u = JSON.parse(localStorage.getItem('app_user'));
              k = u.kode || u.kode_kantor || u.kode_cabang || '000';
          }
      } catch(e) {}
      
      window.currentUserKode = String(k).padStart(3, '0');
      document.getElementById('badgeUnit').innerText = (window.currentUserKode === '000') ? 'KONSOLIDASI' : `CABANG ${window.currentUserKode}`;

      // 2. KUNCI DROPDOWN CABANG, jalan paralel dengan ambil tanggal
      const kantorOptionsPromise = populateKantorOptionsPO(window.currentUserKode);

      // 3. SET DEFAULT DATE & FETCH
      try { 
          const res = await fetch('./api/date/');
          const j = await res.json();
          if(j?.data){
              document.getElementById('closing_date').value = j.data.last_closing;
              document.getElementById('harian_date').value  = j.data.last_created;
              currentFilter = { closing: j.data.last_closing, harian: j.data.last_created };
              fetchPotensiData();
          }
      } catch(e) { 
          const today = new Date().toISOString().split('T')[0];
          document.getElementById('closing_date').value = today;
          document.getElementById('harian_date').value = today;
          currentFilter = { closing: today, harian: today };
          fetchPotensiData();
      }
      kantorOptionsPromise.catch(() => {});
  });

  // --- FUNGSI LOCK DROPDOWN KANTOR ---
  async function populateKantorOptionsPO(userKode){
      const optKantor = document.getElementById('opt_kantor_rec');

      // JIKA YANG LOGIN CABANG -> LANGSUNG KUNCI MATI
      if(userKode && userKode !== '000'){
          optKantor.innerHTML = `<option value="${userKode}">CABANG ${userKode}</option>`;
          optKantor.value = userKode;
          optKantor.disabled = true;
          optKantor.classList.add('bg-slate-100', 'cursor-not-allowed');
          return; 
      }

      // JIKA PUSAT -> BUKA SEMUA OPSI
      try {
          const res = await fetch('./api/kode/', { 
              method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) 
          });
          const json = await res.json();
          let list = json.data || [];
          
          let html = `<option value="">KONSOLIDASI (SEMUA)</option>`;
          list.filter(x => x.kode_kantor !== '000').sort((a,b) => String(a.kode_kantor).localeCompare(b.kode_kantor)).forEach(it => {
              html += `<option value="${String(it.kode_kantor).padStart(3,'0')}">${String(it.kode_kantor).padStart(3,'0')} - ${it.nama_kantor}</option>`;
          });
          optKantor.innerHTML = html;
          optKantor.disabled = false;
      } catch(e){
          optKantor.innerHTML = `<option value="">Error Load</option>`;
      }
  }

  // --- FILTER SUBMIT ---
  document.getElementById('filterForm').addEventListener('submit', e => {
    e.preventDefault();
    currentFilter.closing = document.getElementById('closing_date').value;
    currentFilter.harian  = document.getElementById('harian_date').value;
    sortState = { col:null, dir:1 }; 
    fetchPotensiData();
  });

  ['opt_kantor_rec','closing_date','harian_date'].forEach(id => {
      document.getElementById(id)?.addEventListener('change', () => {
          currentFilter.closing = document.getElementById('closing_date').value;
          currentFilter.harian = document.getElementById('harian_date').value;
          sortState = {col:null,dir:1};
          fetchPotensiData();
          if(window.innerWidth < 768) { document.getElementById('filterForm')?.classList.remove('is-open'); syncPOFilterButton(); }
      });
  });

  // =========================================================
  // FETCH REKAP POTENSI NPL
  // =========================================================
  async function fetchPotensiData(){
    const loading = document.getElementById('loadingPO');
    const loadingMini = document.getElementById('loadingMini');
    loading.classList.remove('hidden'); loadingMini.classList.remove('hidden');

    if(poAbort) poAbort.abort();
    poAbort = new AbortController();

    const tbody = document.getElementById('poBody');
    const ttotal = document.getElementById('poTotalRow');
    tbody.innerHTML = ''; ttotal.innerHTML = '';

    const kantor = document.getElementById('opt_kantor_rec').value || '';
    document.getElementById('thNamaPO').innerText = (kantor !== '') ? "NAMA KANKAS" : "NAMA KANTOR";

    try {
        const payload = { 
            type: 'Potensi NPL', 
            closing_date: currentFilter.closing, 
            harian_date: currentFilter.harian,
            kode_kantor: kantor
        };

        const res = await fetch('./api/npl/', {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify(payload),
            signal: poAbort.signal
        });
        const json = await res.json();
        
        let data = [];
        let totalRow = null;

        if(json.data && json.data.data && json.data.grand_total) {
            data = json.data.data; 
            totalRow = json.data.grand_total;
        } else if (Array.isArray(json.data)) {
            data = json.data;
            totalRow = data.find(d => String(d.nama_cabang||d.nama_kantor||'').toUpperCase().includes('TOTAL'));
            data = data.filter(d => d !== totalRow);
        } else if (json.data && Array.isArray(json.data.data)) {
             data = json.data.data;
             totalRow = json.data.grand_total;
        }

        window.poGtRaw = totalRow;
        window.poDataRaw = data;
        window.poDataRaw.sort((a,b) => kodeNum(a.kode_cabang) - kodeNum(b.kode_cabang));

        renderTotal(totalRow);
        renderRows(window.poDataRaw);

    } catch(err){
        if(err.name !== 'AbortError') {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="12" class="p-8 text-center text-red-500 font-bold bg-red-50">Gagal memuat data rekap.</td></tr>`;
        }
    } finally {
        loading.classList.add('hidden'); loadingMini.classList.add('hidden');
        setTimeout(updatePoStickyHeader, 50);
    }
  }

  // --- RENDER TOTAL BARIS ---
  function renderTotal(tot){
      const el = document.getElementById('poTotalRow');
      if(!tot) return;

      const dropVal = document.getElementById('opt_kantor_rec').value || '';
      const targetKode = dropVal !== '' ? dropVal : '000'; 

      const getLink = (angka, status, colorClass) => {
          if (num(angka) > 0) {
              return `<a href="#" class="po-noa-link ${colorClass} font-bold" onclick="event.preventDefault(); checkAccessAndOpenModal('${targetKode}', '${status}')">${fmtInt(angka)}</a>`;
          }
          return `<span class="text-slate-400">${fmtInt(angka)}</span>`;
      };

      el.innerHTML = `
        <tr class="row-total">
            <td class="sticky-left-1 font-mono font-bold text-slate-500 text-xs">ALL</td>
            <td class="sticky-left-2 font-bold text-slate-800 text-xs md:text-sm uppercase text-blue-900">${tot.nama_cabang || tot.nama_kantor || 'TOTAL KONSOLIDASI'}</td>
            
            <td class="text-center bg-blue-100">${getLink(tot.total_noa, 'ALL', 'text-blue-700 hover:text-blue-900')}</td>
            <td class="text-right font-bold text-blue-700 bg-blue-100 pr-4">${fmtNom(tot.total_baki)}</td>
            
            <td class="text-center bg-green-100">${getLink(tot.noa_aman, 'AMAN', 'text-green-700 hover:text-green-900')}</td>
            <td class="text-right font-bold text-green-700 bg-green-100 pr-4">${fmtNom(tot.baki_aman)}</td>
            
            <td class="text-center bg-yellow-100">${getLink(tot.noa_jt, 'JATUH TEMPO', 'text-yellow-700 hover:text-yellow-900')}</td>
            <td class="text-right font-bold text-yellow-700 bg-yellow-100 pr-4">${fmtNom(tot.baki_jt)}</td>
            
            <td class="text-center bg-red-100">${getLink(tot.noa_flow, 'FLOW KOLEK', 'text-red-700 hover:text-red-900')}</td>
            <td class="text-right font-bold text-red-700 bg-red-100 pr-4">${fmtNom(tot.baki_flow)}</td>
            
            <td class="text-center bg-orange-100">${getLink(tot.noa_potensi, 'MASIH POTENSI', 'text-orange-700 hover:text-orange-900')}</td>
            <td class="text-right font-bold text-orange-700 bg-orange-100 pr-4">${fmtNom(tot.baki_potensi)}</td>
        </tr>
      `;
  }

  function renderRows(rows){
      const tbody = document.getElementById('poBody');
      if(rows.length === 0){ tbody.innerHTML = `<tr><td colspan="12" class="p-8 text-center text-slate-400">Tidak ada data.</td></tr>`; return; }
      
      tbody.innerHTML = rows.map(r => {
          const rawKode = r.kode_cabang || r.kode_unit || '';
          const kode = String(rawKode).padStart(3,'0');
          const nama = r.nama_cabang || r.nama_kantor || '-';
          
          const getLink = (angka, status, colorClass) => {
              if (num(angka) > 0) {
                  return `<a href="#" class="po-noa-link ${colorClass} font-bold" onclick="event.preventDefault(); checkAccessAndOpenModal('${kode}', '${status}')">${fmtInt(angka)}</a>`;
              }
              return `<span class="text-slate-300">${fmtInt(angka)}</span>`;
          };

          return `
            <tr class="transition border-b">
                <td class="sticky-left-1 font-mono font-bold text-slate-500 text-xs">${kode}</td>
                <td class="sticky-left-2 font-semibold text-slate-700 text-xs md:text-sm"><div class="truncate" title="${nama}">${nama}</div></td>
                
                <td class="text-center bg-blue-50/30">${getLink(r.total_noa, 'ALL', 'text-blue-600')}</td>
                <td class="text-right text-slate-700 font-bold bg-blue-50/30 pr-4">${fmtNom(r.total_baki)}</td>
                
                <td class="text-center bg-green-50/30">${getLink(r.noa_aman, 'AMAN', 'text-green-600')}</td>
                <td class="text-right text-slate-600 bg-green-50/30 pr-4">${fmtNom(r.baki_aman)}</td>
                
                <td class="text-center bg-yellow-50/30">${getLink(r.noa_jt, 'JATUH TEMPO', 'text-yellow-600')}</td>
                <td class="text-right text-slate-600 bg-yellow-50/30 pr-4">${fmtNom(r.baki_jt)}</td>
                
                <td class="text-center bg-red-50/30">${getLink(r.noa_flow, 'FLOW KOLEK', 'text-red-600')}</td>
                <td class="text-right text-slate-700 font-bold bg-red-50/30 pr-4">${fmtNom(r.baki_flow)}</td>
                
                <td class="text-center bg-orange-50/30">${getLink(r.noa_potensi, 'MASIH POTENSI', 'text-orange-600')}</td>
                <td class="text-right text-slate-600 bg-orange-50/30 pr-4">${fmtNom(r.baki_potensi)}</td>
            </tr>
          `;
      }).join('');

      tbody.innerHTML += `<tr style="height: 60px;"><td colspan="12" class="border-none bg-transparent"></td></tr>`;
  }

  // --- SORTING REKAP ---
  window.doSort = function(col) {
      sortState = { col: col, dir: sortState.col === col ? -sortState.dir : 1 };
      const sorted = [...window.poDataRaw].sort((a,b) => {
          const valA = num(a[col]);
          const valB = num(b[col]);
          return (valA - valB) * sortState.dir;
      });
      renderRows(sorted);
  };

  // --- EXPORT EXCEL REKAP UTAMA ---
  function exportPotensiExcel() {
      const rows = window.poDataRaw || [];
      const gt = window.poGtRaw || null;
      if(rows.length === 0) { alert("Tidak ada data rekap untuk diexport!"); return; }

      let table = `<table border="1">
          <thead>
              <tr>
                  <th rowspan="2" style="background-color:#eff6ff;">KODE</th>
                  <th rowspan="2" style="background-color:#eff6ff;">NAMA KANTOR</th>
                  <th colspan="2" style="background-color:#dbeafe;">TOTAL POTENSI</th>
                  <th colspan="2" style="background-color:#dcfce7;">AMAN</th>
                  <th colspan="2" style="background-color:#fef08a;">JATUH TEMPO</th>
                  <th colspan="2" style="background-color:#fee2e2;">FLOW KOLEK</th>
                  <th colspan="2" style="background-color:#ffedd5;">MASIH POTENSI</th>
              </tr>
              <tr>
                  <th>NOA</th><th>BAKI DEBET</th>
                  <th>NOA</th><th>BAKI DEBET</th>
                  <th>NOA</th><th>BAKI DEBET</th>
                  <th>NOA</th><th>BAKI DEBET</th>
                  <th>NOA</th><th>BAKI DEBET</th>
              </tr>
          </thead>
          <tbody>`;
      
      if(gt) {
          table += `<tr>
              <td style="font-weight:bold;"></td>
              <td style="font-weight:bold;">${gt.nama_cabang || gt.nama_kantor || 'GRAND TOTAL'}</td>
              <td style="font-weight:bold;">${gt.total_noa}</td>
              <td style="font-weight:bold;">${gt.total_baki}</td>
              <td style="font-weight:bold;">${gt.noa_aman}</td>
              <td style="font-weight:bold;">${gt.baki_aman}</td>
              <td style="font-weight:bold;">${gt.noa_jt}</td>
              <td style="font-weight:bold;">${gt.baki_jt}</td>
              <td style="font-weight:bold;">${gt.noa_flow}</td>
              <td style="font-weight:bold;">${gt.baki_flow}</td>
              <td style="font-weight:bold;">${gt.noa_potensi}</td>
              <td style="font-weight:bold;">${gt.baki_potensi}</td>
          </tr>`;
      }

      rows.forEach(r => {
          const kode = r.kode_cabang || r.kode_unit || '-';
          const nama = r.nama_cabang || r.nama_kantor || '-';
          table += `<tr>
              <td style="mso-number-format:'\\@'">${kode}</td>
              <td>${nama}</td>
              <td>${r.total_noa}</td>
              <td>${r.total_baki}</td>
              <td>${r.noa_aman}</td>
              <td>${r.baki_aman}</td>
              <td>${r.noa_jt}</td>
              <td>${r.baki_jt}</td>
              <td>${r.noa_flow}</td>
              <td>${r.baki_flow}</td>
              <td>${r.noa_potensi}</td>
              <td>${r.baki_potensi}</td>
          </tr>`;
      });
      table += `</tbody></table>`;

      const tgl = document.getElementById('harian_date').value;
      const blob = new Blob([table], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      a.download = `Rekap_Potensi_NPL_${tgl}.xls`;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
  }

  // =========================================================
  // LOGIC MODAL DETAIL & SECURITY
  // =========================================================
  function closeModalPeringatan() {
      const modal = document.getElementById('modalPeringatan');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
  }

  window.checkAccessAndOpenModal = function(targetKode, statusFilter = 'ALL') {
      const userKode = window.currentUserKode;
      const targetCabang = targetKode.length >= 3 ? targetKode.substring(0,3) : targetKode; 
      
      // PROTEKSI: CEGAH CABANG A MELIHAT DATA CABANG B
      if (userKode !== '000' && userKode !== targetCabang) {
          document.getElementById('warnUserLvl').innerText = `Unit ${userKode}`;
          document.getElementById('warnTargetLvl').innerText = `Unit ${targetCabang}`;
          const modalWarn = document.getElementById('modalPeringatan');
          modalWarn.classList.remove('hidden');
          modalWarn.classList.add('flex');
          return;
      }
      openModalDetail(targetKode, statusFilter);
  };

  async function openModalDetail(kode, statusFilter = 'ALL'){
      const targetCabang = kode.length >= 3 ? kode.substring(0,3) : kode;
      const targetKankas = kode.length > 3 ? kode : '';
      
      currentDetailKode = targetCabang;
      currentDetailStatus = statusFilter || 'ALL'; 
      
      const modal = document.getElementById('modalDebiturPotensi');
      const title = document.getElementById('modalTitlePotensi');
      const sub   = document.getElementById('modalSubtitlePO');
      
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      let titleLabel = kode === '000' ? 'KONSOLIDASI' : kode;
      const activeLabel = statusFilter === 'AMAN' ? 'Aman / Lunas' : (statusFilter === 'ALL' ? 'Potensi' : statusFilter);
      title.innerHTML = `Detail ${poEscape(activeLabel)} <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded font-mono border border-blue-200">${titleLabel}</span>`;
      sub.innerText = `Posisi: ${formatDate(currentFilter.closing)} vs ${formatDate(currentFilter.harian)}`;
      
      // AUTO SELECT STATUS DROPDOWN BERDASARKAN ANGKA YG DIKLIK
      const statDropdown = document.getElementById('modalFilterStatus');
      if (statDropdown) statDropdown.value = statusFilter;
      const commitmentDropdown = document.getElementById('modalFilterCommitment');
      if (commitmentDropdown) commitmentDropdown.value = 'ALL';
      const searchInput = document.getElementById('modalSearchPO');
      if (searchInput) searchInput.value = '';

      // POPULATE DROPDOWN KANKAS DI DALAM MODAL
      const selKankas = document.getElementById('modalFilterKankas');
      selKankas.innerHTML = '<option value="">Semua Kankas</option>';
      
      if(targetCabang !== '000') {
          selKankas.classList.remove('hidden');
          try {
              const r = await fetch('./api/kode/', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kankas', kode_kantor:targetCabang}) });
              const j = await r.json();
              (j.data||[]).forEach(k => { 
                  const isSelected = (targetKankas === k.kode_group1) ? 'selected' : '';
                  selKankas.innerHTML += `<option value="${k.kode_group1}" ${isSelected}>${k.kode_group1} - ${k.deskripsi_group1}</option>`; 
              });
          } catch(e){}
      } else {
          selKankas.classList.add('hidden');
      }

      // 🔥 POPULATE DROPDOWN AO DI DALAM MODAL 🔥
      const selAo = document.getElementById('modalFilterAo');
      selAo.innerHTML = '<option value="">Semua AO</option>';
      if(targetCabang !== '000') {
          selAo.classList.remove('hidden');
          try {
              const r = await fetch('./api/kode/', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_ao_kredit', kode_kantor:targetCabang}) });
              const j = await r.json();
              (j.data||[]).forEach(k => {
                  const namaAo = k.nama_ao || k.kode_group2;
                  selAo.innerHTML += `<option value="${k.kode_group2}">${namaAo}</option>`; 
              });
          } catch(e){}
      } else {
          selAo.classList.add('hidden');
      }

      fetchDetailPotensiNpl();
  }

  async function fetchDetailPotensiNpl() {
      const tbody = document.getElementById('modalBodyRowsPO');
      const ttot  = document.getElementById('modalTotalRowPO');
      const kankas = document.getElementById('modalFilterKankas')?.value || ''; 
      const aoFilter = document.getElementById('modalFilterAo')?.value || ''; 

      tbody.innerHTML = `<tr><td colspan="24" class="p-12 text-center"><div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mx-auto mb-3"></div><span class="text-slate-500 font-medium">Sedang mengambil data detail...</span></td></tr>`;
      ttot.innerHTML = '';

      try {
          console.log("Request Detail Potensi NPL:", currentDetailKode, "Kankas:", kankas, "AO:", aoFilter);
          
          const payload = { 
              type: 'Debitur Potensi NPL', 
              kode_kantor: currentDetailKode === '000' ? '' : currentDetailKode, 
              kode_kankas: kankas,             
              kode_ao: aoFilter,             
              closing_date: currentFilter.closing, 
              harian_date: currentFilter.harian,
              status_filter: 'ALL',
              include_lunas: 1,
              include_aman_backflow: 1
          };
          
          const res = await fetch('./api/npl/', {
              method:'POST', headers:{'Content-Type':'application/json'},
              body: JSON.stringify(payload)
          });
          const json = await res.json();
          detailPoRaw = Array.isArray(json.data) ? json.data : []; 
          
          console.log("Berhasil load detail:", detailPoRaw.length, "baris");

          renderDetailRows(); 

      } catch(e){
          console.error("Error saat fetch Detail Potensi:", e); 
          tbody.innerHTML = `<tr><td colspan="24" class="p-10 text-center text-red-500">Gagal memuat data detail. ${e.message}</td></tr>`;
      }
  }

  // --- RENDER DETAIL + KOMITMEN ---
  function handlePOStatusChange() {
      currentDetailStatus = document.getElementById('modalFilterStatus')?.value || 'ALL';
      renderDetailRows();
  }
  window.handlePOStatusChange = handlePOStatusChange;

  function getFilteredDetailRows() {
      const statusFilter = document.getElementById('modalFilterStatus')?.value || 'ALL';
      const commitmentFilter = document.getElementById('modalFilterCommitment')?.value || 'ALL';
      const query = String(document.getElementById('modalSearchPO')?.value || '').trim().toLowerCase();
      return detailPoRaw.filter(d => {
          const kind = poStatusKind(d);
          const statusMatch = statusFilter === 'ALL' || kind === statusFilter;
          const hasCommitment = poHasCommitment(d);
          const commitmentMatch = commitmentFilter === 'ALL'
              || (commitmentFilter === 'SUDAH' ? hasCommitment : !hasCommitment);
          const c = poCommitment(d);
          const haystack = `${d.no_rekening || ''} ${d.nama_nasabah || ''} ${d.alamat || ''} ${d.nama_ao || ''} ${poStatusDisplay(d)} ${kind} ${c.komitmen} ${c.alasan}`.toLowerCase();
          return statusMatch && commitmentMatch && (!query || haystack.includes(query));
      });
  }

  function renderDetailSummary(rows) {
      const committed = rows.filter(poHasCommitment).length;
      const safe = rows.filter(d => poStatusKind(d) === 'AMAN').length;
      const promise = rows.reduce((sum,d) => sum + poCommitment(d).nominal, 0);
      document.getElementById('poSumCandidates').textContent = fmtInt(rows.length);
      document.getElementById('poSumSafe').textContent = fmtInt(safe);
      document.getElementById('poSumCommitted').textContent = fmtInt(committed);
      document.getElementById('poSumUncommitted').textContent = fmtInt(rows.length - committed);
      document.getElementById('poSumPromise').textContent = `Rp ${fmtNom(promise)}`;
  }

  function statusPotensiBadge(d) {
      const kind = poStatusKind(d);
      const label = poStatusDisplay(d);
      if(kind === 'AMAN') return `<span class="status-aman">${poEscape(label)}</span>`;
      if(kind === 'FLOW KOLEK') return `<span class="status-flow">${poEscape(label)}</span>`;
      if(kind === 'JATUH TEMPO') return `<span class="status-jt">${poEscape(label)}</span>`;
      return `<span class="text-orange-600 font-bold bg-orange-100 px-2 py-0.5 rounded">${poEscape(label)}</span>`;
  }

  function renderDetailMobileCards(rows) {
      const el = document.getElementById('modalMobileCardsPO');
      if (!el) return;
      el.innerHTML = rows.map(d => {
          const c = poCommitment(d);
          const has = poHasCommitment(d);
          return `<article class="po-detail-card">
            <div class="po-detail-card-head">
              <div class="min-w-0">
                <div class="po-detail-card-name">${poEscape(d.nama_nasabah || '-')}</div>
                <div class="po-detail-card-rek">${poEscape(d.no_rekening || '-')} · ${poEscape(d.nama_ao || '-')}</div>
                <div class="po-detail-card-address">${poEscape(d.alamat || '-')}</div>
              </div>
              ${statusPotensiBadge(d)}
            </div>
            <div class="po-detail-card-metrics">
              <div class="po-detail-card-metric"><span>BD Harian</span><strong>${fmtNom(d.baki_debet_harian)}</strong></div>
              <div class="po-detail-card-metric"><span>Tungg. Pokok</span><strong>${fmtNom(d.tunggakan_pokok)}</strong></div>
              <div class="po-detail-card-metric"><span>Tungg. Bunga</span><strong>${fmtNom(d.tunggakan_bunga)}</strong></div>
              <div class="po-detail-card-metric"><span>Kolek C → H</span><strong>${poEscape(d.kolek_closing || '-')} → ${poEscape(d.kolek_harian || '-')}</strong></div>
              <div class="po-detail-card-metric"><span>DPD P / B</span><strong>${fmtInt(d.hmp_harian)} / ${fmtInt(d.hmb_harian)}</strong></div>
              <div class="po-detail-card-metric"><span>Jatuh Tempo</span><strong>${formatJTByRule(d.jt_harian)}</strong></div>
            </div>
            <div class="po-detail-card-commit">
              <div class="po-detail-card-commit-top">
                <span class="po-commit-badge ${has ? 'done' : 'empty'}">${has ? poEscape(c.komitmen || 'Sudah Komitmen') : 'Belum Komitmen'}</span>
                <span class="po-detail-card-commit-money">Rp ${fmtNom(c.nominal)}</span>
              </div>
              <div class="po-detail-card-meta">
                <b>Tgl Janji</b><span>${formatDate(c.tanggal)}</span>
                <b>Alasan</b><span title="${poEscape(c.alasan)}">${poEscape(c.alasan || '-')}</span>
              </div>
            </div>
          </article>`;
      }).join('');
  }

  window.renderDetailRows = function() {
      const tbody = document.getElementById('modalBodyRowsPO');
      const ttot = document.getElementById('modalTotalRowPO');
      detailPoFiltered = getFilteredDetailRows();
      renderDetailSummary(detailPoFiltered);
      renderDetailMobileCards(detailPoFiltered);

      if(detailPoFiltered.length === 0) {
          tbody.innerHTML = `<tr><td colspan="16" class="p-10 text-center text-slate-400">Data tidak ditemukan pada filter ini.</td></tr>`;
          ttot.innerHTML = '';
          return;
      }

      let totals = { bd_c:0, bd_h:0, tp:0, tb:0, sa:0, ap:0, ab:0, promise:0 };
      const rowsHtml = detailPoFiltered.map(d => {
          const c = poCommitment(d);
          const has = poHasCommitment(d);
          totals.bd_c += num(d.baki_debet_closing); totals.bd_h += num(d.baki_debet_harian);
          totals.tp += num(d.tunggakan_pokok); totals.tb += num(d.tunggakan_bunga); totals.sa += num(d.saldo_akhir);
          totals.ap += num(d.angsuran_pokok); totals.ab += num(d.angsuran_bunga); totals.promise += c.nominal;
          return `<tr>
            <td class="modal-freeze-1 font-mono text-slate-600">${poEscape(d.no_rekening || '-')}</td>
            <td class="modal-freeze-2 text-left" title="${poEscape(d.nama_nasabah || '-')}">
              <div class="font-bold text-slate-800 truncate">${poEscape(d.nama_nasabah || '-')}</div>
              <div class="mt-0.5 text-[7px] text-slate-500 truncate" title="${poEscape(d.alamat || '-')}">${poEscape(d.alamat || '-')}</div>
              <div class="mt-0.5 text-[7px] font-semibold text-blue-600 truncate">${poEscape(d.nama_ao || '-')}</div>
            </td>
            <td class="text-center">${statusPotensiBadge(d)}</td>
            <td class="text-center"><span class="po-kolek-shift"><span class="po-kolek-chip old">${poEscape(d.kolek_closing || '-')}</span>→<span class="po-kolek-chip new">${poEscape(d.kolek_harian || '-')}</span></span></td>
            <td class="text-right">${fmtNom(d.baki_debet_closing)}</td>
            <td class="text-right font-bold text-red-700 bg-red-50/40">${fmtNom(d.baki_debet_harian)}</td>
            <td class="text-right"><div class="po-stacked"><strong>${fmtNom(d.tunggakan_pokok)}</strong><small>B: ${fmtNom(d.tunggakan_bunga)}</small></div></td>
            <td class="text-right text-green-700 font-bold">${fmtNom(d.saldo_akhir)}</td>
            <td class="text-center">${formatJTByRule(d.jt_harian)}</td>
            <td class="text-center"><div class="po-stacked"><strong>${fmtInt(d.hm_harian)}</strong><small>P ${fmtInt(d.hmp_harian)} · B ${fmtInt(d.hmb_harian)}</small></div></td>
            <td class="text-right"><div class="po-stacked"><strong>${fmtNom(d.angsuran_pokok)}</strong><small>B: ${fmtNom(d.angsuran_bunga)}</small></div></td>
            <td class="text-center"><span class="po-commit-badge ${has ? 'done' : 'empty'}">${has ? 'Sudah' : 'Belum'}</span></td>
            <td class="text-left truncate" title="${poEscape(c.komitmen)}">${poEscape(c.komitmen || '-')}</td>
            <td class="text-center">${formatDate(c.tanggal)}</td>
            <td class="text-right font-bold text-blue-700">${fmtNom(c.nominal)}</td>
            <td class="text-left truncate" title="${poEscape(c.alasan)}">${poEscape(c.alasan || '-')}</td>
          </tr>`;
      }).join('');

      ttot.innerHTML = `<tr class="modal-total-row">
        <td class="modal-freeze-1">TOTAL</td><td class="modal-freeze-2">${fmtInt(detailPoFiltered.length)} Debitur</td>
        <td colspan="2"></td><td class="text-right">${fmtNom(totals.bd_c)}</td><td class="text-right text-red-700">${fmtNom(totals.bd_h)}</td>
        <td class="text-right"><div class="po-stacked"><strong>${fmtNom(totals.tp)}</strong><small>B: ${fmtNom(totals.tb)}</small></div></td>
        <td class="text-right text-green-700">${fmtNom(totals.sa)}</td><td colspan="2"></td>
        <td class="text-right"><div class="po-stacked"><strong>${fmtNom(totals.ap)}</strong><small>B: ${fmtNom(totals.ab)}</small></div></td>
        <td colspan="3"></td><td class="text-right text-blue-700">${fmtNom(totals.promise)}</td><td></td>
      </tr>`;
      tbody.innerHTML = rowsHtml;
  }

  // --- EXPORT EXCEL DETAIL DARI MODAL ---
  function exportDetailPotensiExcel() {
      const filteredData = getFilteredDetailRows();
      if(filteredData.length === 0) return alert('Tidak ada detail untuk diexport!');
      let table = `<table border="1"><thead><tr>
        <th>NO REKENING</th><th>NAMA NASABAH</th><th>ALAMAT</th><th>NAMA AO</th><th>STATUS POTENSI</th>
        <th>KOL CLOSING</th><th>BD CLOSING</th><th>KOL HARIAN</th><th>BD HARIAN</th>
        <th>TUNGG POKOK</th><th>TUNGG BUNGA</th><th>SALDO TABUNGAN</th><th>JT</th><th>DPD</th><th>DPD TP</th><th>DPD TB</th>
        <th>ANGS. POKOK</th><th>ANGS. BUNGA</th><th>TGL TRANS</th><th>STATUS KOMITMEN</th><th>KOMITMEN</th><th>TGL JANJI</th><th>NOMINAL JANJI</th><th>ALASAN</th>
      </tr></thead><tbody>`;
      filteredData.forEach(d => {
          const c = poCommitment(d); const has = poHasCommitment(d);
          table += `<tr>
            <td style="mso-number-format:'\\@'">${poEscape(d.no_rekening || '')}</td><td>${poEscape(d.nama_nasabah || '')}</td><td>${poEscape(d.alamat || '')}</td><td>${poEscape(d.nama_ao || '')}</td><td>${poEscape(poStatusDisplay(d))}</td>
            <td>${poEscape(d.kolek_closing || '')}</td><td>${num(d.baki_debet_closing)}</td><td>${poEscape(d.kolek_harian || '')}</td><td>${num(d.baki_debet_harian)}</td>
            <td>${num(d.tunggakan_pokok)}</td><td>${num(d.tunggakan_bunga)}</td><td>${num(d.saldo_akhir)}</td><td>${poEscape(d.jt_harian || '')}</td><td>${num(d.hm_harian)}</td><td>${num(d.hmp_harian)}</td><td>${num(d.hmb_harian)}</td>
            <td>${num(d.angsuran_pokok)}</td><td>${num(d.angsuran_bunga)}</td><td>${poEscape(d.tgl_trans_terakhir || '')}</td><td>${has ? 'SUDAH' : 'BELUM'}</td><td>${poEscape(c.komitmen)}</td><td>${poEscape(c.tanggal)}</td><td>${c.nominal}</td><td>${poEscape(c.alasan)}</td>
          </tr>`;
      });
      table += `</tbody></table>`;
      const blob = new Blob(['\ufeff',table], {type:'application/vnd.ms-excel;charset=utf-8'});
      const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
      const status = document.getElementById('modalFilterStatus')?.value || 'ALL';
      a.download = `Detail_Potensi_NPL_${currentDetailKode}_${status}.xls`;
      document.body.appendChild(a); a.click(); a.remove(); setTimeout(()=>URL.revokeObjectURL(a.href),1000);
  }

  // --- TRIGGER UPDATE BULK (TOMBOL UPDATE DI MODAL POTENSI NPL) ---
  window.gotoUpdatePotensiNPL = function() {
      const selectedKankas = document.getElementById('modalFilterKankas')?.value || '';
      const selectedAo = document.getElementById('modalFilterAo')?.value || '';
      const selectedStatus = document.getElementById('modalFilterStatus')?.value || 'ALL';
      
      const payload = {
          source: 'potensi_npl',
          kode_kantor: currentDetailKode === '000' ? '' : currentDetailKode,
          kode_kankas: selectedKankas,
          kode_ao: selectedAo,
          status_filter: selectedStatus,
          commitment_filter: document.getElementById('modalFilterCommitment')?.value || 'ALL',
          closing_date: currentFilter.closing,
          harian_date: currentFilter.harian
      };
      
      sessionStorage.setItem("flowpar_update", JSON.stringify(payload));
      sessionStorage.removeItem("potensinpl_update");
      window.location.href = './update_flowpar'; 
  };

  const closePoModal = () => { document.getElementById('modalDebiturPotensi').classList.add('hidden'); document.body.style.overflow = ''; };
  document.getElementById('btnClosePO').onclick = closePoModal;
  document.getElementById('modalDebiturPotensi')?.addEventListener('click', e => { if(e.target.id === 'modalDebiturPotensi') closePoModal(); });
  document.addEventListener('keydown', e => { if(e.key === 'Escape') { closePoModal(); closeInfoPotensi(); } });
</script>

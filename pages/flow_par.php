<style>
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  :root { --primary: #2563eb; --bg: #f8fafc; --text: #334155; }
  
  /* === GLOBAL === */
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow: hidden; }
  
  /* === CONTROLS === */
  .inp {
      box-sizing: border-box;
      border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 8px;
      font-size: 10px; background: #fff; width: 100%; height: 32px;
      min-width: 0; transition: all 0.2s; outline: none; color: #334155;
  }
  .inp:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f8fafc; color: #475569; font-weight: 600; cursor: not-allowed; border-color: #e2e8f0; }
  select.inp { appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1em; padding-right: 1.5rem; }
  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space:nowrap; }
  .field { display:flex; flex-direction:column; min-width:0; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition:transform 0.2s; height:32px; border-radius:6px; }
  .btn-icon:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); }
  @media (min-width: 768px) {
      .lbl { font-size:10px; }
      .inp { border-radius:8px; padding:0 10px; font-size:12px; height:36px; }
      select.inp { padding-right:2rem; }
      .btn-icon { height:36px; border-radius:8px; }
  }
  
  /* === DATEPICKER FIX === */
  input[type="date"] { position: relative; cursor: pointer; }
  input[type="date"]::-webkit-inner-spin-button,
  input[type="date"]::-webkit-calendar-picker-indicator {
      position: absolute; top: 0; left: 0; right: 0; bottom: 0;
      width: 100%; height: 100%; opacity: 0; cursor: pointer;
  }

  /* === TABLE CONTAINER === */
  #fpScroller {
      /* Semua lebar kolom utama dikontrol dari variabel ini. */
      --col1: 56px;          /* Kode kantor */
      --col2: 190px;         /* Nama area/kantor */
      --colNoa: 58px;        /* Kolom NOA */
      --colNom: 138px;       /* Kolom nominal kategori */
      --colNomTotal: 146px;  /* Kolom nominal total */
      --fp_headH: 65px;

      position: relative;
      border: 0; border-radius: 0; background: white;
      height: 100%; overflow: auto;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
      scrollbar-gutter: stable;
  }

  table { border-collapse: separate; border-spacing: 0; width: max-content; min-width: 100%; table-layout: fixed; font-size: 12px; }
  #tabelFlowPar {
      width: calc(
          var(--col1) + var(--col2) +
          var(--colNoa) + var(--colNom) +
          var(--colNoa) + var(--colNom) +
          var(--colNoa) + var(--colNom) +
          var(--colNoa) + var(--colNom) +
          var(--colNoa) + var(--colNomTotal)
      );
      min-width: 100%;
  }

  #tabelFlowPar col.fp-col-code { width: var(--col1) !important; }
  #tabelFlowPar col.fp-col-area { width: var(--col2) !important; }
  #tabelFlowPar col.fp-col-noa { width: var(--colNoa) !important; }
  #tabelFlowPar col.fp-col-nom { width: var(--colNom) !important; }
  #tabelFlowPar col.fp-col-nom-total { width: var(--colNomTotal) !important; }

  th, td { white-space: nowrap; padding: 8px 8px; vertical-align: middle; }
  
  /* === HEADER STYLES === */
  #tabelFlowPar thead th { 
      position: sticky; top: 0; z-index: 60; 
      background: #f1f5f9; color: #475569; 
      font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; font-size: 11px;
      border-bottom: 1px solid #cbd5e1; 
  }
  #tabelFlowPar .fp-head-1 th {
      top: 0;
      height: 40px;
      background-color: #f1f5f9 !important;
      color: #1e3a8a;
      font-weight: 800;
      box-shadow: inset 0 -1px 0 #cbd5e1, inset 0 1px 0 #cbd5e1;
  }
  #tabelFlowPar .fp-head-2 th {
      top: 40px;
      height: 34px;
      background-color: #f8fafc !important;
      color: #334155;
      box-shadow: inset 0 -1px 0 #cbd5e1;
  }
  #tabelFlowPar .head-jt { color:#1e40af !important; background:#eff6ff !important; }
  #tabelFlowPar .head-pokok { color:#7e22ce !important; background:#faf5ff !important; }
  #tabelFlowPar .head-bunga { color:#b45309 !important; background:#fffbeb !important; }
  #tabelFlowPar .head-pokok-bunga { color:#be123c !important; background:#fff1f2 !important; }
  #tabelFlowPar .head-total { color:#0e7490 !important; background:#ecfeff !important; }
  #tabelFlowPar thead th.sticky-left-1,
  #tabelFlowPar thead th.sticky-left-2 {
      background-color: #e0f2fe !important;
      color: #1e3a8a;
  }
  #tabelFlowPar thead th.sticky-left-1,
  #tabelFlowPar thead th.sticky-left-2 {
      top: 0 !important;
  }
  
  /* === TOTAL ROW STICKY === */
  #fpTotalRow td { 
      position: sticky; top: var(--fp_headH); z-index: 50; 
      background: #eff6ff; color: #1e40af; font-weight: 700; 
      border-bottom: 2px solid #bfdbfe;
      box-shadow: 0 4px 6px -2px rgba(0,0,0,0.05);
  }

  /* === STICKY COLUMNS LOGIC (Desktop) === */
  .sticky-left-1 { 
      position: sticky; left: 0; z-index: 45; 
      background: #fff; border-right: 1px solid #f1f5f9; 
      width: var(--col1); min-width: var(--col1); max-width: var(--col1); text-align: center; 
  }
  .sticky-left-2 { 
      position: sticky; left: var(--col1); z-index: 44; 
      background: #fff; border-right: 1px solid #e2e8f0; 
      width: var(--col2); min-width: var(--col2); max-width: var(--col2); 
      overflow: hidden; text-overflow: ellipsis; 
  }

  #tabelFlowPar thead th.sticky-left-1 { z-index: 70; background: #f1f5f9; }
  #tabelFlowPar thead th.sticky-left-2 { z-index: 69; background: #f1f5f9; }
  
  #fpTotalRow td.sticky-left-1 { z-index: 59; background: #eff6ff; border-right: 1px solid #bfdbfe; }
  #fpTotalRow td.sticky-left-2 { z-index: 58; background: #eff6ff; border-right: 1px solid #bfdbfe; }

  #fpBody td { background-color: #fff; border-bottom: 1px solid #f1f5f9; color: #334155; }
  #fpBody tr { height: 38px; }
  #fpBody tr:hover td { background-color: #f8fafc; }
  .flow-metric { display:flex; align-items:center; justify-content:space-between; gap:10px; width:100%; min-width:0; }
  .flow-metric .metric-nom { min-width:0; overflow:hidden; text-overflow:ellipsis; }
  .flow-metric .metric-noa { flex:0 0 auto; font-size:10px; color:#64748b; font-weight:800; }
  .fp-cell-link {
      display:block; width:100%; border-radius:4px; padding:2px 4px;
      text-decoration:none !important; overflow:hidden; text-overflow:ellipsis;
      font-variant-numeric: tabular-nums;
  }
  .fp-cell-empty { color:#94a3b8; font-weight:700; }
  .fp-mobile-noa { display:none; }
  .fp-nom-value { display:block; min-width:0; overflow:hidden; text-overflow:ellipsis; }
  .fp-noa-cell {
      width:var(--colNoa); min-width:var(--colNoa); max-width:var(--colNoa);
      padding-left:2px !important; padding-right:2px !important;
  }
  .fp-noa-cell .fp-cell-link { text-align:center; padding-left:2px; padding-right:2px; }
  .fp-nom-cell {
      width:var(--colNom); min-width:var(--colNom); max-width:var(--colNom);
      padding-left:4px !important; padding-right:6px !important;
  }
  .fp-nom-cell .fp-cell-link { text-align:right; padding-left:2px; padding-right:3px; }
  #tabelFlowPar tr > .fp-nom-cell:last-child {
      width:var(--colNomTotal); min-width:var(--colNomTotal); max-width:var(--colNomTotal);
  }
  #tabelFlowPar .fp-head-1 th:not(.sticky-left-1):not(.sticky-left-2) {
      white-space:normal; line-height:1.15; padding-left:4px; padding-right:4px;
  }
  #tabelFlowPar .fp-head-2 th { padding-left:2px; padding-right:2px; }
  #tabelFlowPar .fp-head-noa {
      width:var(--colNoa); min-width:var(--colNoa); max-width:var(--colNoa);
  }
  #tabelFlowPar .fp-head-nom {
      width:var(--colNom); min-width:var(--colNom); max-width:var(--colNom);
  }
  #tabelFlowPar .fp-head-nom-total {
      width:var(--colNomTotal); min-width:var(--colNomTotal); max-width:var(--colNomTotal);
  }

  /* === MODAL DETAIL FLOW PAR === */
  #modalDebiturFlowPar {
      padding:10px;
      background:rgba(15,23,42,.68);
      backdrop-filter:blur(7px);
  }
  #modalCardFP {
      width:min(1580px,calc(100vw - 20px));
      height:min(94dvh,920px);
      max-height:94dvh;
      border:1px solid rgba(226,232,240,.92);
      border-radius:16px;
      background:#fff;
      box-shadow:0 28px 80px rgba(15,23,42,.32);
  }
  .fp-modal-header {
      flex:none;
      border-bottom:1px solid #e2e8f0;
      background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);
  }
  .fp-modal-head-main {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:10px 12px 8px;
  }
  .fp-modal-title-wrap {
      display:flex;
      align-items:center;
      min-width:0;
      gap:9px;
  }
  .fp-modal-title-icon {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:38px;
      height:38px;
      flex:0 0 38px;
      border:1px solid #bfdbfe;
      border-radius:10px;
      background:#eff6ff;
      color:#2563eb;
  }
  .fp-modal-title-copy { min-width:0; }
  #modalTitleFlowPar {
      display:flex;
      align-items:center;
      flex-wrap:wrap;
      gap:6px;
      color:#0f172a;
      font-size:17px;
      font-weight:900;
      line-height:1.15;
  }
  #modalSubtitleFP {
      margin-top:3px;
      color:#64748b;
      font-size:10px;
      font-weight:650;
      line-height:1.25;
  }
  .fp-modal-actions {
      display:flex;
      align-items:center;
      justify-content:flex-end;
      gap:6px;
      flex:0 0 auto;
  }
  .fp-modal-action {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:6px;
      height:34px;
      padding:0 11px;
      border:1px solid transparent;
      border-radius:8px;
      font-size:10px;
      font-weight:850;
      white-space:nowrap;
      transition:transform .15s,background .15s,box-shadow .15s;
  }
  .fp-modal-action:hover { transform:translateY(-1px); box-shadow:0 4px 10px rgba(15,23,42,.1); }
  .fp-modal-action.filter { border-color:#cbd5e1; background:#fff; color:#475569; }
  .fp-modal-action.update { background:#2563eb; color:#fff; }
  .fp-modal-action.excel { background:#059669; color:#fff; }
  .fp-modal-action.close { width:34px; padding:0; border-color:#fecaca; background:#fff1f2; color:#e11d48; }
  .fp-modal-toolbar {
      display:flex;
      align-items:end;
      gap:7px;
      padding:0 12px 9px;
  }
  .fp-modal-tool-field { min-width:0; }
  .fp-modal-tool-field.kankas { width:235px; }
  .fp-modal-tool-field.search { flex:1; max-width:360px; }
  .fp-modal-tool-field.status { width:175px; }
  .fp-modal-search-wrap { position:relative; }
  .fp-modal-search-wrap svg {
      position:absolute;
      left:9px;
      top:50%;
      width:14px;
      height:14px;
      color:#94a3b8;
      transform:translateY(-50%);
      pointer-events:none;
  }
  #modalSearchFP { padding-left:29px; }

  #modalProjectionSummary {
      display:grid;
      grid-template-columns:repeat(4,minmax(0,1fr));
      gap:7px;
      padding:8px 10px;
      border-bottom:1px solid #e2e8f0;
      background:#f8fafc;
      flex:none;
  }
  .fp-projection-card {
      min-width:0;
      padding:7px 9px;
      border:1px solid #e2e8f0;
      border-radius:9px;
      background:#fff;
      box-shadow:0 1px 2px rgba(15,23,42,.035);
  }
  .fp-projection-label {
      color:#64748b;
      font-size:8px;
      font-weight:900;
      letter-spacing:.04em;
      text-transform:uppercase;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
  }
  .fp-projection-value {
      margin-top:3px;
      color:#0f172a;
      font-size:14px;
      font-weight:900;
      line-height:1.05;
      font-variant-numeric:tabular-nums;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
  }
  .fp-projection-value.blue { color:#1d4ed8; }
  .fp-projection-value.green { color:#047857; }
  .fp-projection-value.orange { color:#c2410c; }
  .fp-projection-note {
      grid-column:1 / -1;
      display:flex;
      align-items:center;
      gap:6px;
      color:#64748b;
      font-size:8.5px;
      font-weight:650;
      line-height:1.25;
  }
  .fp-projection-note svg { width:13px; height:13px; flex:0 0 13px; color:#2563eb; }

  #modalScroll {
      --colRek:128px;
      --colNama:190px;
      position:relative;
      flex:1;
      min-height:0;
      overflow:auto;
      background:#fff;
      scrollbar-gutter:stable;
      -webkit-overflow-scrolling:touch;
      overscroll-behavior:contain;
  }
  #modalScroll::-webkit-scrollbar { width:7px; height:7px; }
  #modalScroll::-webkit-scrollbar-track { background:#f8fafc; }
  #modalScroll::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  #modalTableFP {
      width:max-content;
      min-width:2320px;
      border-collapse:separate;
      border-spacing:0;
      table-layout:fixed;
      font-size:10.5px;
  }
  #modalTableFP th {
      position:sticky;
      top:0;
      z-index:30;
      height:38px;
      padding:6px 8px;
      border-right:1px solid #e2e8f0;
      border-bottom:1px solid #cbd5e1;
      background:#f1f5f9;
      color:#475569;
      font-size:8.5px;
      font-weight:850;
      letter-spacing:.025em;
      text-transform:uppercase;
      white-space:nowrap;
  }
  #modalTableFP td {
      height:37px;
      padding:5px 8px;
      border-right:1px solid #f1f5f9;
      border-bottom:1px solid #edf2f7;
      background:#fff;
      color:#334155;
      white-space:nowrap;
      font-variant-numeric:tabular-nums;
  }
  #modalTableFP tbody tr:nth-child(even) td { background:#fbfdff; }
  #modalTableFP tbody tr:hover td { background:#eff6ff; }
  .modal-freeze-1 {
      position:sticky;
      left:0;
      z-index:35;
      width:var(--colRek);
      min-width:var(--colRek);
      max-width:var(--colRek);
      background:#fff;
      border-right:1px solid #cbd5e1 !important;
  }
  .modal-freeze-2 {
      position:sticky;
      left:var(--colRek);
      z-index:34;
      width:var(--colNama);
      min-width:var(--colNama);
      max-width:var(--colNama);
      overflow:hidden;
      background:#fff;
      border-right:1px solid #cbd5e1 !important;
      box-shadow:5px 0 9px -8px rgba(15,23,42,.9);
      text-overflow:ellipsis;
      white-space:nowrap;
  }
  #modalTableFP th.modal-freeze-1,
  #modalTableFP th.modal-freeze-2 { z-index:45; background:#eaf1f8; }
  #modalTableFP tbody tr:nth-child(even) td.modal-freeze-1,
  #modalTableFP tbody tr:nth-child(even) td.modal-freeze-2 { background:#fbfdff; }
  #modalTableFP tbody tr:hover td.modal-freeze-1,
  #modalTableFP tbody tr:hover td.modal-freeze-2 { background:#eff6ff; }
  .modal-total-row td {
      position:sticky;
      top:38px;
      z-index:25;
      height:38px;
      background:#eaf3ff !important;
      color:#1e40af;
      font-weight:850;
      border-bottom:2px solid #bfdbfe;
      box-shadow:0 4px 7px -5px rgba(15,23,42,.45);
  }
  .modal-total-row td.modal-freeze-1 { z-index:43; background:#eaf3ff !important; }
  .modal-total-row td.modal-freeze-2 { z-index:42; background:#eaf3ff !important; }
  .overdue td { background-color:#fff7f7 !important; }
  .hot90 { background-color:#fee2e2 !important; font-weight:900; color:#991b1b !important; }
  .fp-kolek-badge,
  .fp-commit-badge {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-height:20px;
      padding:2px 7px;
      border-radius:999px;
      font-size:8px;
      font-weight:900;
      line-height:1.1;
      white-space:nowrap;
  }
  .fp-kolek-badge.good { background:#ecfdf5; color:#047857; }
  .fp-kolek-badge.watch { background:#fffbeb; color:#b45309; }
  .fp-kolek-badge.bad { background:#fff1f2; color:#be123c; }
  .fp-kolek-badge.neutral { background:#f1f5f9; color:#475569; }
  .fp-commit-badge.yes { background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; }
  .fp-commit-badge.no { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
  .fp-address-cell {
      max-width:210px;
      overflow:hidden;
      color:#64748b;
      text-overflow:ellipsis;
  }
  #modalMobileCardsFP { display:none; }
  .fp-detail-empty,
  .fp-detail-loading {
      display:flex;
      min-height:240px;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      padding:24px;
      color:#64748b;
      font-size:11px;
      font-weight:700;
      text-align:center;
  }

  @media (min-width:768px) and (max-width:1100px) {
      #modalCardFP { width:calc(100vw - 18px); height:92dvh; }
      .fp-modal-toolbar { display:grid; grid-template-columns:210px minmax(180px,1fr) 165px; }
      #modalTableFP { min-width:2180px; }
  }

  @media (max-width:767px) {
      #modalDebiturFlowPar { align-items:flex-end !important; padding:0 !important; }
      #modalCardFP {
          width:100% !important;
          height:95dvh !important;
          max-height:95dvh !important;
          border-radius:16px 16px 0 0 !important;
          border-left:0;
          border-right:0;
          border-bottom:0;
          box-shadow:0 -18px 46px rgba(15,23,42,.28);
      }
      .fp-modal-head-main { align-items:flex-start; gap:7px; padding:8px 8px 7px; }
      .fp-modal-title-wrap { gap:7px; min-width:0; }
      .fp-modal-title-icon { width:31px; height:31px; flex-basis:31px; border-radius:8px; }
      .fp-modal-title-icon svg { width:16px; height:16px; }
      #modalTitleFlowPar { font-size:13px; gap:4px; }
      #modalSubtitleFP { margin-top:2px; font-size:8px; }
      .fp-modal-actions { gap:4px; }
      .fp-modal-action { width:30px; height:30px; padding:0; border-radius:7px; }
      .fp-modal-action.close { width:30px; }
      .fp-modal-action svg { width:14px; height:14px; }
      .fp-action-label { display:none; }
      #modalFilterPanel {
          width:100%;
          padding:7px 8px 8px !important;
          border-top:1px solid #e2e8f0;
      }
      #modalFilterPanel:not(.hidden) { display:block !important; }
      .fp-modal-toolbar {
          display:grid;
          grid-template-columns:minmax(0,1fr) minmax(0,1fr);
          gap:5px;
          padding:0;
      }
      .fp-modal-tool-field.kankas { width:auto; grid-column:1 / -1; }
      .fp-modal-tool-field.search { max-width:none; }
      .fp-modal-tool-field.status { width:auto; }
      .fp-modal-tool-field .lbl { font-size:7px; }
      .fp-modal-tool-field .inp { height:31px; font-size:9px; }
      #modalProjectionSummary {
          grid-template-columns:repeat(2,minmax(0,1fr));
          gap:5px;
          padding:6px 7px;
      }
      .fp-projection-card { padding:6px 7px; border-radius:8px; }
      .fp-projection-label { font-size:6.5px; }
      .fp-projection-value { font-size:10px; margin-top:2px; }
      .fp-projection-note { font-size:7px; grid-column:1 / -1; }
      #modalScroll { overflow-y:auto; overflow-x:hidden; background:#f8fafc; }
      #modalTableFP { display:none !important; }
      #modalMobileCardsFP { display:grid; gap:7px; padding:7px 8px 22px; }
      .fp-mobile-card {
          overflow:hidden;
          border:1px solid #e2e8f0;
          border-radius:10px;
          background:#fff;
          box-shadow:0 1px 2px rgba(15,23,42,.04);
      }
      .fp-mobile-card-head {
          display:flex;
          align-items:flex-start;
          justify-content:space-between;
          gap:7px;
          padding:7px 8px 6px;
          border-bottom:1px solid #f1f5f9;
          background:linear-gradient(180deg,#fff,#fbfdff);
      }
      .fp-mobile-identity { min-width:0; }
      .fp-mobile-rek {
          color:#475569;
          font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
          font-size:8px;
          font-weight:850;
          line-height:1.1;
      }
      .fp-mobile-name {
          margin-top:2px;
          overflow:hidden;
          color:#0f172a;
          font-size:10px;
          font-weight:900;
          line-height:1.15;
          text-overflow:ellipsis;
          white-space:nowrap;
      }
      .fp-mobile-address {
          padding:5px 8px;
          border-bottom:1px solid #f1f5f9;
          color:#64748b;
          font-size:7.5px;
          line-height:1.25;
      }
      .fp-mobile-metrics {
          display:grid;
          grid-template-columns:repeat(2,minmax(0,1fr));
          gap:1px;
          background:#e2e8f0;
      }
      .fp-mobile-metric { min-width:0; padding:6px 8px; background:#fff; }
      .fp-mobile-label {
          color:#64748b;
          font-size:6.5px;
          font-weight:900;
          letter-spacing:.025em;
          text-transform:uppercase;
      }
      .fp-mobile-value {
          margin-top:2px;
          overflow:hidden;
          color:#0f172a;
          font-size:9.5px;
          font-weight:900;
          line-height:1.05;
          text-overflow:ellipsis;
          white-space:nowrap;
          font-variant-numeric:tabular-nums;
      }
      .fp-mobile-value.red { color:#be123c; }
      .fp-mobile-value.green { color:#047857; }
      .fp-mobile-commit {
          padding:6px 8px 7px;
          background:#fbfdff;
          color:#475569;
          font-size:7.5px;
          line-height:1.3;
      }
      .fp-mobile-commit-row {
          display:flex;
          align-items:flex-start;
          justify-content:space-between;
          gap:8px;
          margin-top:3px;
      }
      .fp-mobile-commit-row:first-child { margin-top:0; }
      .fp-mobile-commit-row span:first-child { color:#64748b; font-weight:800; }
      .fp-mobile-commit-row b { color:#334155; text-align:right; }
  }

  /* === RESPONSIVE TABLE WIDTHS === */
  @media (min-width: 1440px) {
      #fpScroller {
          --col1: 60px;
          --col2: 220px;
          --colNoa: 62px;
          --colNom: 150px;
          --colNomTotal: 160px;
      }
  }

  @media (min-width: 1024px) and (max-width: 1439px) {
      #fpScroller {
          --col1: 54px;
          --col2: 180px;
          --colNoa: 54px;
          --colNom: 128px;
          --colNomTotal: 138px;
      }
      #tabelFlowPar thead th { font-size:10px; }
      #fpBody td { font-size:11px; }
  }

  @media (min-width: 768px) and (max-width: 1023px) {
      #fpScroller {
          --col1: 50px;
          --col2: 160px;
          --colNoa: 50px;
          --colNom: 118px;
          --colNomTotal: 126px;
      }
      #tabelFlowPar thead th { font-size:9px; letter-spacing:0.025em; }
      #tabelFlowPar .fp-head-1 th { height:38px; }
      #tabelFlowPar .fp-head-2 th { top:38px; height:32px; }
      #fpBody td, #fpTotalRow td { font-size:10px; }
      #fpBody tr { height:36px; }
  }

  @media (min-width: 480px) and (max-width: 767px) {
      #fpScroller {
          --col1: 0px;
          --col2: 126px;
          --colNoa: 0px;
          --colNom: 104px;
          --colNomTotal: 110px;
      }
  }

  @media (max-width: 479px) {
      #fpScroller {
          --col1: 0px;
          --col2: 104px;
          --colNoa: 0px;
          --colNom: 92px;
          --colNomTotal: 98px;
      }
  }

  /* === RESPONSIVE FIX (MOBILE) === */
  @media (max-width: 767px) {
      #opt_kantor_rec, #closing_date, #harian_date {
          font-size:12px; padding:0 8px; text-align:left; width:100%;
      }

      #fpScroller {
          --col1: 0px;
          --col2: 116px;
          --colMobileMetric: 88px;
          --colMobileTotal: 96px;
          overflow-x:auto;
          overflow-y:auto;
          -webkit-overflow-scrolling:touch;
          scroll-padding-left:var(--col2);
          isolation:isolate;
      }
      #fpScroller::-webkit-scrollbar { height:4px; width:4px; }

      table { font-size:10px; }
      th, td { padding:4px; }

      #tabelFlowPar.mobile-view {
          width: calc(var(--col2) + (var(--colMobileMetric) * 4) + var(--colMobileTotal));
          min-width: calc(var(--col2) + (var(--colMobileMetric) * 4) + var(--colMobileTotal));
      }
      #tabelFlowPar.mobile-view col.fp-col-area { width:var(--col2) !important; }
      #tabelFlowPar.mobile-view col.fp-col-mobile-metric { width:var(--colMobileMetric) !important; }
      #tabelFlowPar.mobile-view col.fp-col-mobile-total { width:var(--colMobileTotal) !important; }

      #tabelFlowPar.mobile-view thead th {
          font-size:8px;
          letter-spacing:0;
          line-height:1.1;
          white-space:normal;
          padding:4px 3px;
      }
      #tabelFlowPar.mobile-view .fp-head-1 th {
          height:38px;
      }
      #tabelFlowPar.mobile-view .fp-head-2 {
          display:none !important;
      }

      #tabelFlowPar.mobile-view .sticky-left-1 { display:none !important; }
      #tabelFlowPar.mobile-view .sticky-left-2 {
          display:table-cell !important;
          position:-webkit-sticky !important;
          position:sticky !important;
          left:0 !important;
          z-index:70 !important;
          width:var(--col2) !important;
          min-width:var(--col2) !important;
          max-width:var(--col2) !important;
          padding:4px 6px !important;
          white-space:normal !important;
          line-height:1.15;
          background:#fff !important;
          background-clip:padding-box;
          box-shadow:4px 0 8px -6px rgba(15,23,42,.7);
          border-right:1px solid #cbd5e1 !important;
      }
      #tabelFlowPar.mobile-view thead th.sticky-left-2 {
          top:0 !important;
          z-index:95 !important;
          background:#e0f2fe !important;
          text-align:left;
      }
      #tabelFlowPar.mobile-view #fpTotalRow td.sticky-left-2 {
          z-index:82 !important;
          background:#eff6ff !important;
      }
      #tabelFlowPar.mobile-view #fpBody td.sticky-left-2 {
          z-index:72 !important;
      }
      #tabelFlowPar.mobile-view #fpBody .sticky-left-2 > div,
      #tabelFlowPar.mobile-view #fpTotalRow .sticky-left-2 > div {
          display:-webkit-box;
          -webkit-line-clamp:2;
          -webkit-box-orient:vertical;
          overflow:hidden;
          white-space:normal;
          overflow-wrap:anywhere;
          font-size:9px;
          line-height:1.15;
      }

      #tabelFlowPar.mobile-view .fp-mobile-metric-cell,
      #tabelFlowPar.mobile-view .fp-mobile-total-cell {
          padding:2px 4px !important;
          text-align:right;
          vertical-align:middle;
      }
      #tabelFlowPar.mobile-view .fp-mobile-metric-cell {
          width:var(--colMobileMetric);
          min-width:var(--colMobileMetric);
          max-width:var(--colMobileMetric);
      }
      #tabelFlowPar.mobile-view .fp-mobile-total-cell {
          width:var(--colMobileTotal);
          min-width:var(--colMobileTotal);
          max-width:var(--colMobileTotal);
      }
      #tabelFlowPar.mobile-view .fp-mobile-metric-cell .fp-cell-link,
      #tabelFlowPar.mobile-view .fp-mobile-total-cell .fp-cell-link {
          display:flex;
          min-height:34px;
          flex-direction:column;
          align-items:flex-end;
          justify-content:center;
          gap:2px;
          padding:2px 1px;
          overflow:visible;
          text-overflow:clip;
          line-height:1;
      }
      #tabelFlowPar.mobile-view .fp-mobile-metric-cell .fp-cell-empty,
      #tabelFlowPar.mobile-view .fp-mobile-total-cell .fp-cell-empty {
          display:flex;
          min-height:34px;
          align-items:center;
          justify-content:center;
          padding:0;
      }
      #tabelFlowPar.mobile-view .fp-mobile-noa {
          display:block;
          width:100%;
          font-size:7px;
          font-weight:800;
          text-align:right;
          opacity:.78;
          white-space:nowrap;
      }
      #tabelFlowPar.mobile-view .fp-nom-value {
          display:block;
          width:100%;
          font-size:9px;
          font-weight:800;
          line-height:1.05;
          text-align:right;
          white-space:nowrap;
          overflow:hidden;
          text-overflow:ellipsis;
          font-variant-numeric:tabular-nums;
      }
      #tabelFlowPar.mobile-view #fpBody tr { height:44px; }
      #tabelFlowPar.mobile-view #fpBody td,
      #tabelFlowPar.mobile-view #fpTotalRow td { font-size:9px; }

      #modalScroll { --colRek:0px; --colNama:120px; }
      .modal-freeze-1 { display:none; }
      .modal-freeze-2 { left:0; }
  }

  @media (max-width: 479px) {
      #fpScroller {
          --col2: 108px;
          --colMobileMetric: 82px;
          --colMobileTotal: 90px;
      }
  }

  @media (max-width: 374px) {
      #fpScroller {
          --col2: 98px;
          --colMobileMetric: 76px;
          --colMobileTotal: 84px;
      }
      #tabelFlowPar.mobile-view thead th { font-size:7px; }
      #tabelFlowPar.mobile-view .fp-nom-value { font-size:8px; }
      #tabelFlowPar.mobile-view #fpBody .sticky-left-2 > div,
      #tabelFlowPar.mobile-view #fpTotalRow .sticky-left-2 > div { font-size:8px; }
  }


  /* === FLOW PAR REPORT REDESIGN === */
  #flowParPage {
      width:100%;
      max-width:none;
      height:calc(100vh - 60px);
      height:calc(100dvh - 60px);
      padding:6px 8px 8px;
      gap:7px;
      background:#f8fafc;
  }
  #flowParHeader {
      margin:0;
      padding:8px 10px;
      border-radius:11px;
      border:1px solid #dbe3ee;
      box-shadow:0 2px 8px rgba(15,23,42,.045);
      background:linear-gradient(180deg,#fff 0%,#fbfdff 100%);
  }
  .fp-page-title-icon {
      width:36px;
      height:36px;
      flex:0 0 36px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:0 !important;
      border-radius:10px !important;
      background:linear-gradient(145deg,#2563eb,#1d4ed8) !important;
      box-shadow:0 6px 14px rgba(37,99,235,.18) !important;
  }
  .fp-page-title-copy { min-width:0; }
  .fp-page-title-copy h1 { line-height:1.08; }
  .fp-page-subtitle {
      margin:3px 0 0 0 !important;
      color:#64748b;
      font-size:10px;
      font-weight:650;
      line-height:1.2;
  }

  /* Main report surface */
  #fpReportCard {
      border-radius:11px;
      border-color:#dbe3ee;
      box-shadow:0 2px 8px rgba(15,23,42,.045);
  }
  #fpScroller {
      border-radius:10px;
      background:#fff;
  }
  #tabelFlowPar {
      font-variant-numeric:tabular-nums;
  }
  #tabelFlowPar thead th {
      border-right:1px solid #dbe3ee;
  }
  #tabelFlowPar .fp-head-1 th {
      height:36px;
      border-top:3px solid transparent;
      font-size:9px;
      letter-spacing:.025em;
  }
  #tabelFlowPar .fp-head-2 th {
      top:36px;
      height:29px;
      font-size:8px;
      letter-spacing:.025em;
  }
  #tabelFlowPar .head-jt { border-top-color:#3b82f6 !important; }
  #tabelFlowPar .head-pokok { border-top-color:#a855f7 !important; }
  #tabelFlowPar .head-bunga { border-top-color:#f59e0b !important; }
  #tabelFlowPar .head-pokok-bunga { border-top-color:#f43f5e !important; }
  #tabelFlowPar .head-total { border-top-color:#06b6d4 !important; }
  #fpBody tr:nth-child(even) td { background:#fbfdff; }
  #fpBody tr:hover td { background:#eff6ff !important; }
  #fpBody td,
  #fpTotalRow td {
      border-right:1px solid #edf2f7;
  }
  #fpBody tr { height:35px; }
  #fpBody td { font-size:10.5px; }
  #fpTotalRow td {
      font-size:10px;
      background:#edf5ff;
      color:#1e3a8a;
      border-bottom:2px solid #93c5fd;
  }
  .fp-noa-cell { color:#475569; font-weight:850; }
  .fp-nom-cell { font-weight:800; }
  .fp-cell-link {
      padding:3px 3px;
      border-radius:5px;
  }
  .fp-cell-link:hover { background:rgba(219,234,254,.72); }

  /* Desktop: semua kolom muat tanpa horizontal scroll */
  @media (min-width:1024px) {
      #fpScroller {
          --col1:44px;
          --col2:148px;
          --colNoa:42px;
          --colNom:96px;
          --colNomTotal:104px;
          overflow-x:hidden;
      }
      #tabelFlowPar {
          width:100%;
          min-width:100%;
      }
      #tabelFlowPar th,
      #tabelFlowPar td {
          padding-left:4px;
          padding-right:4px;
      }
      #tabelFlowPar .sticky-left-2 { font-size:10px; }
      #fpBody td { font-size:9.5px; }
      #fpTotalRow td { font-size:9px; }
  }

  @media (min-width:1440px) {
      #fpScroller {
          --col1:50px;
          --col2:175px;
          --colNoa:46px;
          --colNom:108px;
          --colNomTotal:116px;
      }
      #tabelFlowPar .fp-head-1 th { font-size:9.5px; }
      #tabelFlowPar .fp-head-2 th { font-size:8.5px; }
      #fpBody td { font-size:10.5px; }
      #fpTotalRow td { font-size:10px; }
  }

  @media (max-width:767px) {
      #flowParPage {
          height:calc(100vh - 54px);
          height:calc(100dvh - 54px);
          padding:4px;
          gap:4px;
      }
      #flowParHeader {
          padding:6px 7px;
          border-radius:8px;
      }
      .fp-page-title-icon {
          width:30px;
          height:30px;
          flex-basis:30px;
          border-radius:8px !important;
      }
      .fp-page-title-icon svg { width:15px !important; height:15px !important; }
      .fp-page-title-copy h1 { font-size:13px !important; gap:5px !important; }
      .fp-page-subtitle { margin-top:2px !important; font-size:8px; }
      #fpReportCard { border-radius:8px; }
      #tabelFlowPar.mobile-view .fp-head-1 th {
          height:35px;
          border-top-width:2px;
      }
      #tabelFlowPar.mobile-view #fpBody tr { height:40px; }
      #tabelFlowPar.mobile-view .fp-mobile-metric-cell .fp-cell-link,
      #tabelFlowPar.mobile-view .fp-mobile-total-cell .fp-cell-link { min-height:31px; }
  }

  /* === INFO FLOW PAR === */
  .fp-info-btn {
      width:21px;
      height:21px;
      flex:0 0 21px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1px solid #bfdbfe;
      border-radius:999px;
      background:#eff6ff;
      color:#2563eb;
      cursor:pointer;
      transition:background .16s,border-color .16s,transform .16s;
  }
  .fp-info-btn:hover {
      background:#dbeafe;
      border-color:#93c5fd;
      transform:translateY(-1px);
  }
  .fp-info-btn svg { width:13px; height:13px; }

  #modalInfoFlowPar {
      padding:14px;
      background:rgba(15,23,42,.64);
      backdrop-filter:blur(6px);
  }
  .fp-info-card {
      width:min(780px,calc(100vw - 28px));
      max-height:min(88dvh,760px);
      display:flex;
      flex-direction:column;
      overflow:hidden;
      border:1px solid rgba(226,232,240,.95);
      border-radius:16px;
      background:#fff;
      box-shadow:0 28px 70px rgba(15,23,42,.32);
  }
  .fp-info-header {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      padding:13px 15px;
      border-bottom:1px solid #e2e8f0;
      background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);
  }
  .fp-info-heading {
      display:flex;
      align-items:center;
      min-width:0;
      gap:10px;
  }
  .fp-info-heading-icon {
      width:36px;
      height:36px;
      flex:0 0 36px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1px solid #bfdbfe;
      border-radius:10px;
      background:#eff6ff;
      color:#2563eb;
  }
  .fp-info-heading-icon svg { width:19px; height:19px; }
  .fp-info-title {
      color:#0f172a;
      font-size:16px;
      font-weight:900;
      line-height:1.15;
  }
  .fp-info-subtitle {
      margin-top:3px;
      color:#64748b;
      font-size:10px;
      font-weight:650;
      line-height:1.3;
  }
  .fp-info-close {
      width:34px;
      height:34px;
      flex:0 0 34px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1px solid #e2e8f0;
      border-radius:9px;
      background:#fff;
      color:#64748b;
      cursor:pointer;
      transition:.16s ease;
  }
  .fp-info-close:hover { background:#fff1f2; border-color:#fecdd3; color:#e11d48; }
  .fp-info-body {
      overflow:auto;
      padding:14px 15px 16px;
      color:#475569;
  }
  .fp-info-intro {
      padding:10px 12px;
      border:1px solid #bfdbfe;
      border-radius:10px;
      background:#eff6ff;
      color:#1e3a8a;
      font-size:11px;
      font-weight:700;
      line-height:1.45;
  }
  .fp-guide-grid {
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:9px;
      margin-top:10px;
  }
  .fp-guide-item {
      min-width:0;
      padding:10px 11px;
      border:1px solid #e2e8f0;
      border-left-width:4px;
      border-radius:10px;
      background:#fff;
      box-shadow:0 1px 2px rgba(15,23,42,.035);
  }
  .fp-guide-item.jt { border-left-color:#3b82f6; background:#f8fbff; }
  .fp-guide-item.one { border-left-color:#64748b; background:#f8fafc; }
  .fp-guide-item.pokok { border-left-color:#a855f7; background:#fdfaff; }
  .fp-guide-item.bunga { border-left-color:#f59e0b; background:#fffdf7; }
  .fp-guide-item.gabungan { border-left-color:#f43f5e; background:#fff9fa; }
  .fp-guide-item.full { grid-column:1 / -1; }
  .fp-guide-label {
      display:flex;
      align-items:center;
      gap:6px;
      color:#0f172a;
      font-size:11px;
      font-weight:900;
      line-height:1.2;
  }
  .fp-guide-badge {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-width:22px;
      height:19px;
      padding:0 6px;
      border-radius:999px;
      background:#e2e8f0;
      color:#475569;
      font-size:8px;
      font-weight:900;
      white-space:nowrap;
  }
  .fp-guide-text {
      margin-top:6px;
      color:#475569;
      font-size:10.5px;
      font-weight:600;
      line-height:1.48;
  }
  .fp-guide-text b { color:#0f172a; font-weight:900; }
  .fp-info-note {
      margin-top:10px;
      padding:9px 11px;
      border:1px solid #fde68a;
      border-radius:9px;
      background:#fffbeb;
      color:#92400e;
      font-size:9.5px;
      font-weight:700;
      line-height:1.4;
  }

  @media (max-width:767px) {
      .fp-info-btn { width:19px; height:19px; flex-basis:19px; }
      .fp-info-btn svg { width:12px; height:12px; }
      #modalInfoFlowPar {
          align-items:flex-end !important;
          padding:0;
      }
      .fp-info-card {
          width:100%;
          max-height:90dvh;
          border-radius:16px 16px 0 0;
          border-bottom:0;
      }
      .fp-info-header { padding:10px 11px; }
      .fp-info-heading { gap:8px; }
      .fp-info-heading-icon { width:32px; height:32px; flex-basis:32px; border-radius:9px; }
      .fp-info-heading-icon svg { width:17px; height:17px; }
      .fp-info-title { font-size:13px; }
      .fp-info-subtitle { font-size:8.5px; }
      .fp-info-close { width:31px; height:31px; flex-basis:31px; }
      .fp-info-body { padding:10px 11px 14px; }
      .fp-info-intro { padding:8px 9px; font-size:9.5px; }
      .fp-guide-grid { grid-template-columns:1fr; gap:7px; margin-top:8px; }
      .fp-guide-item,
      .fp-guide-item.full { grid-column:auto; padding:8px 9px; }
      .fp-guide-label { font-size:10px; }
      .fp-guide-text { margin-top:4px; font-size:9px; line-height:1.42; }
      .fp-info-note { margin-top:8px; padding:8px 9px; font-size:8.5px; }
  }

</style>

<div id="flowParPage" class="w-full mx-auto flex flex-col font-sans text-slate-800 overflow-hidden">

  <div id="flowParHeader" class="relative z-20 flex-none w-full flex flex-col xl:flex-row items-start xl:items-center justify-between gap-2 shrink-0">
    
    <div class="flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
      <div class="fp-page-title-copy">
        <h1 class="text-base md:text-xl font-extrabold flex items-center gap-2 text-slate-800 whitespace-nowrap">
            <span class="fp-page-title-icon text-white shrink-0">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-6m4 6V7m4 10v-4M5 19h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </span> 
            <span>Rekap Flow PAR</span>
            <button type="button" id="btnInfoFlowPar" class="fp-info-btn" title="Panduan tindak lanjut Flow PAR" aria-label="Buka panduan tindak lanjut Flow PAR">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5"></path><path d="M12 8h.01"></path></svg>
            </button>
            <span id="badgeUnit" class="hidden"></span>
        </h1>
        <p class="fp-page-subtitle">Posisi closing dibanding actual harian berdasarkan penyebab flow.</p>
      </div>
      
      <button id="btnToggleFilter" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] whitespace-nowrap ml-2 shrink-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
          Filter
      </button>
    </div>

    <div id="filterPanel" class="hidden xl:flex w-full xl:w-auto flex-1 min-w-0 justify-end transition-all duration-300 shrink-0 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
      <form id="filterForm" class="flex flex-row flex-wrap xl:flex-nowrap items-end gap-2 md:gap-2.5 w-full xl:w-auto">
        
        <div class="flex gap-2 w-full xl:w-auto shrink-0">
            <div class="field w-1/2 xl:w-[120px]">
                <label class="lbl">CLOSING (M-1)</label>
                <input type="date" id="closing_date" class="inp font-bold text-slate-700 cursor-pointer" required onclick="this.showPicker && this.showPicker()">
            </div>
            
            <div class="field w-1/2 xl:w-[120px]">
                <label class="lbl">HARIAN (ACTUAL)</label>
                <input type="date" id="harian_date" class="inp font-bold text-slate-700 cursor-pointer" required onclick="this.showPicker && this.showPicker()">
            </div>
        </div>

        <div class="flex gap-2 w-full xl:w-auto xl:flex-1 items-end">
            <div class="field flex-1 min-w-[180px] xl:w-[260px]">
                <label class="lbl">AREA/CABANG</label>
                <select id="opt_kantor_rec" class="inp font-bold text-slate-700 truncate"><option value="ALL">Konsolidasi</option></select>
            </div>
            
            <div class="flex items-center gap-2 shrink-0 ml-auto xl:ml-0 mt-2 xl:mt-0">
              <button type="button" onclick="exportFlowParExcel()" class="btn-icon w-[32px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shrink-0" title="Download Excel">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              </button>
            </div>
        </div>

      </form>
    </div>
  </div>
  <!-- Ringkasan KPI dihilangkan: setelah header langsung masuk ke tabel utama. -->
  <div id="fpReportCard" class="flex-1 min-h-0 overflow-hidden bg-white shadow-sm border border-slate-200 relative flex flex-col z-10">
    <div id="loadingFP" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold backdrop-blur-sm rounded-lg">
       <div class="animate-spin h-10 w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-3"></div>
       <span class="text-sm tracking-wide">MEMUAT DATA...</span>
    </div>

    <div id="fpScroller" class="table-wrapper custom-scrollbar">
      <table id="tabelFlowPar" class="w-full min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed">
        <colgroup id="colgroupFP"></colgroup>
        <thead id="theadFP">
          <tr>
            <th class="sticky-left-1 text-center">KODE</th>
            <th class="sticky-left-2 text-left" id="thNamaFP">NAMA KANTOR</th>
            <th class="text-center w-[120px] cursor-pointer hover:bg-slate-200 transition" id="sortNoa" title="Urutkan">NOA FLOW ⬍</th>
            <th class="text-right w-[180px] cursor-pointer hover:bg-slate-200 transition" id="sortBaki" title="Urutkan">BAKI DEBET FLOW ⬍</th>
            <th class="text-center w-[120px]" title="Jatuh tempo, lainnya, atau one obligor">JT / Lain</th>
            <th class="text-center w-[120px]" title="KL karena hari menunggak pokok > 90 hari">Pokok &gt; 90</th>
            <th class="text-center w-[120px]" title="KL karena hari menunggak bunga > 90 hari">Bunga &gt; 90</th>
            <th class="text-center w-[140px]" title="KL karena pokok dan bunga > 90 hari">Pokok+Bunga</th>
          </tr>
        </thead>
        <tbody id="fpTotalRow"></tbody>
        <tbody id="fpBody"></tbody>
      </table>
    </div>
  </div>
</div>


<div id="modalInfoFlowPar" class="fixed inset-0 hidden items-center justify-center z-[10000]" role="dialog" aria-modal="true" aria-labelledby="titleInfoFlowPar">
  <div class="fp-info-card animate-scale-up">
    <div class="fp-info-header">
      <div class="fp-info-heading">
        <span class="fp-info-heading-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 11l3 3L22 4"></path>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
          </svg>
        </span>
        <div>
          <div id="titleInfoFlowPar" class="fp-info-title">Panduan Tindak Lanjut Flow PAR</div>
          <div class="fp-info-subtitle">Acuan sederhana agar rekening tidak ikut flow dan kolektibilitas dapat diperbaiki.</div>
        </div>
      </div>
      <button type="button" id="btnCloseInfoFlowPar" class="fp-info-close" title="Tutup" aria-label="Tutup panduan">
        <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
      </button>
    </div>

    <div class="fp-info-body">
      <div class="fp-info-intro">
        Prioritaskan tindak lanjut sebelum posisi harian/closing. Klik angka pada tabel untuk melihat rekening yang perlu ditangani berdasarkan penyebab flow.
      </div>

      <div class="fp-guide-grid">
        <section class="fp-guide-item jt">
          <div class="fp-guide-label"><span class="fp-guide-badge">JT</span> Jatuh Tempo</div>
          <div class="fp-guide-text">Rekening yang flow karena jatuh tempo perlu <b>diselesaikan sampai lunas</b> sesuai kewajiban pada sistem.</div>
        </section>

        <section class="fp-guide-item one">
          <div class="fp-guide-label"><span class="fp-guide-badge">OBL</span> Lainnya / One Obligor</div>
          <div class="fp-guide-text">Periksa seluruh fasilitas atau rekening yang saling berkaitan. Penuhi cicilan rekening terkait agar <b>kolektibilitas membaik</b> dan rekening lain tidak ikut flow.</div>
        </section>

        <section class="fp-guide-item pokok">
          <div class="fp-guide-label"><span class="fp-guide-badge">TP</span> DPD Pokok &gt; 90 Hari</div>
          <div class="fp-guide-text">Minimal membayar <b>1 kali angsuran pokok</b>. Jika memungkinkan, lakukan <b>1 kali angsuran penuh: pokok + bunga</b>.</div>
        </section>

        <section class="fp-guide-item bunga">
          <div class="fp-guide-label"><span class="fp-guide-badge">TB</span> DPD Bunga &gt; 90 Hari</div>
          <div class="fp-guide-text">Selesaikan <b>seluruh tunggakan pokok</b> dan minimal <b>1 kali angsuran bunga</b>.</div>
        </section>

        <section class="fp-guide-item gabungan full">
          <div class="fp-guide-label"><span class="fp-guide-badge">TP+TB</span> DPD Pokok + Bunga &gt; 90 Hari</div>
          <div class="fp-guide-text">Minimal membayar <b>3 kali angsuran pokok</b> dan <b>1 kali angsuran bunga</b> agar penyebab flow pokok dan bunga dapat ditangani.</div>
        </section>
      </div>

      <div class="fp-info-note">
        Nominal pembayaran akhir tetap mengikuti data tunggakan, jadwal angsuran, kondisi rekening pada sistem, dan ketentuan internal yang berlaku. Pastikan hasil pembayaran sudah tercermin pada posisi harian berikutnya.
      </div>
    </div>
  </div>
</div>

<div id="modalDebiturFlowPar" class="fixed inset-0 hidden items-center justify-center z-[9999]">
  <div id="modalCardFP" class="flex flex-col overflow-hidden animate-scale-up">
    <div class="fp-modal-header">
      <div class="fp-modal-head-main">
        <div class="fp-modal-title-wrap">
          <span class="fp-modal-title-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <path d="M14 2v6h6"></path>
              <path d="M8 13h8M8 17h6"></path>
            </svg>
          </span>
          <div class="fp-modal-title-copy">
            <div id="modalTitleFlowPar">Detail Debitur</div>
            <div id="modalSubtitleFP">Posisi: -</div>
          </div>
        </div>

        <div class="fp-modal-actions">
          <button id="btnToggleModalFilter" type="button" class="fp-modal-action filter md:hidden" title="Buka filter" aria-label="Buka filter">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"><path d="M3 6h18M7 12h10M10 18h4"></path></svg>
          </button>
          <button type="button" onclick="gotoUpdateFlowPar()" class="fp-modal-action update" title="Update komitmen Flow PAR">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
            <span class="fp-action-label">Update</span>
          </button>
          <button type="button" onclick="exportDetailExcel()" class="fp-modal-action excel" title="Export Excel detail">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1"></path><path d="m8 12 4 4 4-4M12 16V4"></path></svg>
            <span class="fp-action-label">Excel</span>
          </button>
          <button id="btnCloseFP" type="button" class="fp-modal-action close" title="Tutup" aria-label="Tutup modal">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"></path></svg>
          </button>
        </div>
      </div>

      <div id="modalFilterPanel" class="hidden md:block">
        <div class="fp-modal-toolbar">
          <div class="fp-modal-tool-field kankas">
            <label class="lbl">Kankas</label>
            <select id="modalFilterKankas" class="inp" onchange="fetchDetailFlowPar()">
              <option value="">Semua Kankas</option>
            </select>
          </div>
          <div class="fp-modal-tool-field search">
            <label class="lbl">Cari Debitur</label>
            <div class="fp-modal-search-wrap">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
              <input type="search" id="modalSearchFP" class="inp" placeholder="Nama, rekening, alamat, komitmen..." autocomplete="off" oninput="renderDetailFlowPar()">
            </div>
          </div>
          <div class="fp-modal-tool-field status">
            <label class="lbl">Status Komitmen</label>
            <select id="modalCommitmentFilterFP" class="inp" onchange="renderDetailFlowPar()">
              <option value="all">Semua Status</option>
              <option value="committed">Sudah Komitmen</option>
              <option value="none">Belum Komitmen</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div id="modalProjectionSummary">
      <div class="fp-projection-card">
        <div class="fp-projection-label">Kandidat Flow</div>
        <div class="fp-projection-value blue" id="fpSummaryCandidate">0 Debitur</div>
      </div>
      <div class="fp-projection-card">
        <div class="fp-projection-label">Sudah Komitmen</div>
        <div class="fp-projection-value green" id="fpSummaryCommitted">0 Debitur</div>
      </div>
      <div class="fp-projection-card">
        <div class="fp-projection-label">Belum Komitmen</div>
        <div class="fp-projection-value orange" id="fpSummaryUncommitted">0 Debitur</div>
      </div>
      <div class="fp-projection-card">
        <div class="fp-projection-label">Nominal Janji Bayar</div>
        <div class="fp-projection-value" id="fpSummaryPromise">Rp 0</div>
      </div>
      <div class="fp-projection-note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M12 11v5M12 8h.01"></path></svg>
        Gunakan tombol Update untuk mengisi komitmen cabang. Ringkasan ini membantu proyeksi tindak lanjut; hasil akhir tetap mengikuti realisasi pembayaran dan posisi harian.
      </div>
    </div>

    <div id="modalScroll">
      <table id="modalTableFP">
        <colgroup>
          <col style="width:var(--colRek)">
          <col style="width:var(--colNama)">
          <col style="width:68px">
          <col style="width:210px">
          <col style="width:125px">
          <col style="width:112px">
          <col style="width:112px">
          <col style="width:125px">
          <col style="width:105px">
          <col style="width:76px">
          <col style="width:58px">
          <col style="width:62px">
          <col style="width:62px">
          <col style="width:108px">
          <col style="width:108px">
          <col style="width:96px">
          <col style="width:112px">
          <col style="width:190px">
          <col style="width:105px">
          <col style="width:125px">
        </colgroup>
        <thead>
          <tr>
            <th class="modal-freeze-1 text-left">No Rekening</th>
            <th class="modal-freeze-2 text-left">Nama Nasabah</th>
            <th class="text-center">Kolek</th>
            <th class="text-left">Alamat</th>
            <th class="text-right">Baki Debet</th>
            <th class="text-right">Tungg. Pokok</th>
            <th class="text-right">Tungg. Bunga</th>
            <th class="text-right">Tot. Tunggakan</th>
            <th class="text-right">Saldo Tab</th>
            <th class="text-center">JT</th>
            <th class="text-center">DPD</th>
            <th class="text-center">DPD TP</th>
            <th class="text-center">DPD TB</th>
            <th class="text-right">Angs. Pokok</th>
            <th class="text-right">Angs. Bunga</th>
            <th class="text-center">Tgl Trans</th>
            <th class="text-center">Status Komitmen</th>
            <th class="text-left">Komitmen</th>
            <th class="text-center">Tgl Janji Bayar</th>
            <th class="text-right">Nominal Janji</th>
          </tr>
        </thead>
        <tbody id="modalTotalRow"></tbody>
        <tbody id="modalBodyRows"></tbody>
      </table>
      <div id="modalMobileCardsFP"></div>
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
      <p class="mt-2">Anda tidak memiliki izin untuk melihat detail data nasabah milik <span class="font-bold text-red-600 px-1 bg-red-50 rounded" id="warnTargetLvl">Unit</span>.</p>
    </div>
    <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
      <button onclick="closeModalPeringatan()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold transition shadow-sm">Mengerti</button>
    </div>
  </div>
</div>

<script>


  // --- INFO PANDUAN FLOW PAR ---
  function openInfoFlowPar() {
      const modal = document.getElementById('modalInfoFlowPar');
      if (!modal) return;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      document.body.style.overflow = 'hidden';
  }

  function closeInfoFlowPar() {
      const modal = document.getElementById('modalInfoFlowPar');
      if (!modal) return;
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      document.body.style.overflow = '';
  }

  document.getElementById('btnInfoFlowPar')?.addEventListener('click', openInfoFlowPar);
  document.getElementById('btnCloseInfoFlowPar')?.addEventListener('click', closeInfoFlowPar);
  document.getElementById('modalInfoFlowPar')?.addEventListener('click', event => {
      if (event.target.id === 'modalInfoFlowPar') closeInfoFlowPar();
  });
  document.addEventListener('keydown', event => {
      if (event.key === 'Escape') closeInfoFlowPar();
  });

  // --- UTILS ---
  const nfID = new Intl.NumberFormat('id-ID');
  const fmtNom = n => nfID.format(Number(n||0));
  const fmtInt = n => new Intl.NumberFormat("id-ID",{maximumFractionDigits:0}).format(+n||0);
  const num = v => Number(v||0);
  const kodeNum = v => Number(String(v??'').replace(/\D/g,'')||0);
  const formatDate = (s) => { if(!s) return '-'; const d=new Date(s); return isNaN(d)?'-': `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`; };
  
  function startOfDay(d){ const x=new Date(d); x.setHours(0,0,0,0); return x; }
  function endOfMonth(dateLike){ const d = new Date(dateLike); if (isNaN(d)) return null; return startOfDay(new Date(d.getFullYear(), d.getMonth()+1, 0)); }
  function formatJTByRule(jt){ if(!jt) return '-'; const d = new Date(jt); if(isNaN(d)) return '-'; const today = startOfDay(new Date()); const due = startOfDay(d); if(due < today){ const yyyy = d.getFullYear(); const mm = String(d.getMonth()+1).padStart(2,'0'); const dd = String(d.getDate()).padStart(2,'0'); return `${yyyy}-${mm}-${dd}`; } return String(d.getDate()); }
  function calcHariMenunggak(jt){ if(!jt) return 0; const d = new Date(jt); if(isNaN(d)) return 0; const today = startOfDay(new Date()); const due = startOfDay(d); const days = Math.floor((today - due) / 86400000); return days > 0 ? days : 0; }

  // --- STATE PENTING ---
  window.fpDataRaw = [];
  window.fpGtRaw = null;
  let detailDataRaw = [];
  let detailDataView = []; 
  let sortState = { column: null, direction: 1 };
  let currentFilter = { closing:'', harian:'' };
  let currentDetailKode = ''; 
  let currentDetailKorwil = '';
  let currentDetailKlasifikasi = '';
  let fpAbort;

  const flowClassLabels = {
      jt_lain: 'JT / Lain / One Obligor',
      pokok_90: 'Pokok > 90',
      bunga_90: 'Bunga > 90',
      pokok_bunga_90: 'Pokok + Bunga > 90'
  };

  function detailTargetCode(defaultKode) {
      const dropVal = document.getElementById('opt_kantor_rec')?.value || '';
      if (dropVal.startsWith('CAB-')) return dropVal.replace('CAB-', '');
      return defaultKode || '000';
  }

  function flowLinkClass(noa, color = 'blue') {
      if (num(noa) <= 0) return 'text-slate-300 pointer-events-none';
      const colors = {
          blue: 'text-blue-800 hover:bg-blue-100 hover:text-blue-900',
          slate: 'text-blue-700 hover:bg-blue-100 hover:text-blue-900',
          orange: 'text-purple-700 hover:bg-purple-100 hover:text-purple-900',
          amber: 'text-amber-700 hover:bg-amber-100 hover:text-amber-900',
          red: 'text-rose-700 hover:bg-rose-100 hover:text-rose-900'
      };
      return `${colors[color] || colors.blue} font-mono font-bold px-1.5 py-1 rounded transition cursor-pointer`;
  }

  function flowCellNoa(noa, kode, klasifikasi = '', color = 'blue') {
      const safeKode = String(kode || '000');
      const safeKlas = String(klasifikasi || '');
      if (num(noa) <= 0) return `<span class="fp-cell-empty">-</span>`;
      return `<a href="#" class="fp-cell-link ${flowLinkClass(noa, color)}" onclick="event.preventDefault(); checkAccessAndOpenModal('${safeKode}', '${safeKlas}')">${fmtInt(noa)}</a>`;
  }

  function flowCellNom(noa, nominal, kode, klasifikasi = '', color = 'slate') {
      const safeKode = String(kode || '000');
      const safeKlas = String(klasifikasi || '');
      if (num(noa) <= 0) return `<span class="fp-cell-empty">-</span>`;
      return `
          <a href="#" class="fp-cell-link ${flowLinkClass(noa, color)}" onclick="event.preventDefault(); checkAccessAndOpenModal('${safeKode}', '${safeKlas}')">
              <span class="fp-mobile-noa">${fmtInt(noa)} NOA</span>
              <span class="fp-nom-value">${fmtNom(nominal)}</span>
          </a>
      `;
  }

  function flowMetricCell(noa, nominal, kode, klasifikasi = '', color = 'blue') {
      const safeKode = String(kode || '000');
      const safeKlas = String(klasifikasi || '');
      const disabled = num(noa) <= 0 ? 'pointer-events-none opacity-45' : '';
      return `
          <a href="#" class="flow-metric ${disabled}" onclick="event.preventDefault(); checkAccessAndOpenModal('${safeKode}', '${safeKlas}')">
              <span class="metric-nom ${flowLinkClass(noa, color)}">${fmtNom(nominal)}</span>
              <span class="metric-noa">${fmtInt(noa)} NOA</span>
          </a>
      `;
  }


  function isMobileFlowPar() {
      return window.innerWidth < 768;
  }

  function renderFlowParColgroup() {
      const cg = document.getElementById('colgroupFP');
      const table = document.getElementById('tabelFlowPar');
      if (!cg || !table) return;

      if (isMobileFlowPar()) {
          table.classList.add('mobile-view');
          cg.innerHTML = `
              <col class="fp-col-area">
              <col class="fp-col-mobile-metric">
              <col class="fp-col-mobile-metric">
              <col class="fp-col-mobile-metric">
              <col class="fp-col-mobile-metric">
              <col class="fp-col-mobile-total">
          `;
      } else {
          table.classList.remove('mobile-view');
          cg.innerHTML = `
              <col class="fp-col-code">
              <col class="fp-col-area">
              <col class="fp-col-noa">
              <col class="fp-col-nom">
              <col class="fp-col-noa">
              <col class="fp-col-nom">
              <col class="fp-col-noa">
              <col class="fp-col-nom">
              <col class="fp-col-noa">
              <col class="fp-col-nom">
              <col class="fp-col-noa">
              <col class="fp-col-nom-total">
          `;
      }
  }

  function flowCellStacked(noa, nominal, kode, klasifikasi = '', color = 'slate') {
      const safeKode = String(kode || '000');
      const safeKlas = String(klasifikasi || '');
      if (num(noa) <= 0) return `<span class="fp-cell-empty">-</span>`;
      return `
          <a href="#" class="fp-cell-link ${flowLinkClass(noa, color)}" onclick="event.preventDefault(); checkAccessAndOpenModal('${safeKode}', '${safeKlas}')">
              <span class="fp-mobile-noa">${fmtInt(noa)} NOA</span>
              <span class="fp-nom-value">${fmtNom(nominal)}</span>
          </a>
      `;
  }

  let fpViewMode = null;
  function syncFlowParResponsiveView(force = false) {
      const mode = isMobileFlowPar() ? 'mobile' : 'desktop';
      if (!force && fpViewMode === mode) {
          setTimeout(updateFpStickyHeader, 20);
          return;
      }
      fpViewMode = mode;
      renderFlowParColgroup();
      renderFlowParHeader();
      if (window.fpGtRaw) renderTotal(window.fpGtRaw); else document.getElementById('fpTotalRow').innerHTML = '';
      if (Array.isArray(window.fpDataRaw)) renderRows(window.fpDataRaw);
      setTimeout(updateFpStickyHeader, 20);
  }

  function renderFlowParHeader() {
      const thead = document.getElementById('theadFP');
      if(!thead) return;

      if (isMobileFlowPar()) {
          thead.innerHTML = `
              <tr class="fp-head-1 fp-mobile-head">
                <th class="sticky-left-2 text-left align-middle border-r border-slate-200 cursor-pointer hover:bg-slate-200 transition" id="thNamaFP" onclick="doSort('nama')">AREA</th>
                <th class="text-center head-jt border-r border-slate-200" title="Jatuh tempo, lainnya, atau one obligor">JATUH TEMPO / LAINNYA</th>
                <th class="text-center head-pokok border-r border-slate-200" title="KL karena hari menunggak pokok > 90 hari">DPD POKOK &gt; 90</th>
                <th class="text-center head-bunga border-r border-slate-200" title="KL karena hari menunggak bunga > 90 hari">DPD BUNGA &gt; 90</th>
                <th class="text-center head-pokok-bunga border-r border-slate-200" title="KL karena pokok dan bunga > 90 hari">DPD POKOK + BUNGA &gt; 90</th>
                <th class="text-center head-total cursor-pointer hover:bg-cyan-100 transition" id="sortBaki" title="Urutkan berdasarkan nominal total">TOTAL FLOW</th>
              </tr>
          `;
      } else {
          thead.innerHTML = `
              <tr class="fp-head-1">
                <th class="sticky-left-1 hidden md:table-cell text-center w-[48px] align-middle border-r border-slate-200 cursor-pointer hover:bg-slate-200 transition" rowspan="2" onclick="doSort('kode')">KODE</th>
                <th class="sticky-left-2 text-left w-[124px] align-middle border-r border-slate-200 cursor-pointer hover:bg-slate-200 transition" id="thNamaFP" rowspan="2" onclick="doSort('nama')">AREA</th>
                <th class="text-center head-jt border-r border-slate-200" colspan="2" title="Jatuh tempo, lainnya, atau one obligor">JATUH TEMPO / LAINNYA</th>
                <th class="text-center head-pokok border-r border-slate-200" colspan="2" title="KL karena hari menunggak pokok > 90 hari">DPD POKOK &gt; 90</th>
                <th class="text-center head-bunga border-r border-slate-200" colspan="2" title="KL karena hari menunggak bunga > 90 hari">DPD BUNGA &gt; 90</th>
                <th class="text-center head-pokok-bunga border-r border-slate-200" colspan="2" title="KL karena pokok dan bunga > 90 hari">DPD POKOK + BUNGA &gt; 90</th>
                <th class="text-center head-total" colspan="2">TOTAL FLOW</th>
              </tr>
              <tr class="fp-head-2 text-[9px] md:text-[10px]">
                <th class="text-center fp-head-noa head-jt border-r border-slate-200">NOA</th>
                <th class="text-right fp-head-nom head-jt border-r border-slate-200">NOM</th>
                <th class="text-center fp-head-noa head-pokok border-r border-slate-200">NOA</th>
                <th class="text-right fp-head-nom head-pokok border-r border-slate-200">NOM</th>
                <th class="text-center fp-head-noa head-bunga border-r border-slate-200">NOA</th>
                <th class="text-right fp-head-nom head-bunga border-r border-slate-200">NOM</th>
                <th class="text-center fp-head-noa head-pokok-bunga border-r border-slate-200">NOA</th>
                <th class="text-right fp-head-nom head-pokok-bunga border-r border-slate-200">NOM</th>
                <th class="text-center fp-head-noa head-total cursor-pointer hover:bg-cyan-100 transition border-r border-slate-200" id="sortNoa" title="Urutkan NOA total">NOA</th>
                <th class="text-right fp-head-nom-total head-total cursor-pointer hover:bg-cyan-100 transition" id="sortBaki" title="Urutkan nominal total">NOM</th>
              </tr>
          `;
      }

      const sortNoa = document.getElementById('sortNoa');
      const sortBaki = document.getElementById('sortBaki');
      if (sortNoa) sortNoa.onclick = () => doSort('noa');
      if (sortBaki) sortBaki.onclick = () => doSort('baki');
      setTimeout(updateFpStickyHeader, 20);
  }

  // TOGGLE FILTER MOBILE LOGIC (Main Page)
  document.getElementById('btnToggleFilter').addEventListener('click', function() {
      const panel = document.getElementById('filterPanel');
      const isOpening = panel.classList.contains('hidden');
      panel.classList.toggle('hidden');
      this.setAttribute('aria-expanded', isOpening ? 'true' : 'false');
      const textNode = Array.from(this.childNodes).find(node => node.nodeType === Node.TEXT_NODE && node.textContent.trim());
      if (textNode) textNode.textContent = isOpening ? ' Tutup' : ' Filter';
  });

  // TOGGLE FILTER MOBILE LOGIC (Modal)
  document.getElementById('btnToggleModalFilter').addEventListener('click', function() {
      const panel = document.getElementById('modalFilterPanel');
      panel.classList.toggle('hidden');
  });

  // Header Sticky Adjuster
  function updateFpStickyHeader() {
      const thead = document.getElementById('theadFP');
      const scroller = document.getElementById('fpScroller');
      if(thead && scroller) {
          scroller.style.setProperty('--fp_headH', (thead.offsetHeight - 1) + 'px');
      }
  }
  window.addEventListener('resize', () => {
      clearTimeout(window.__fpResizeDebounce);
      window.__fpResizeDebounce = setTimeout(() => syncFlowParResponsiveView(false), 120);
  });

  // --- INIT ---
  window.addEventListener('DOMContentLoaded', async () => {
    syncFlowParResponsiveView(true);
    const user = (window.getUser && window.getUser()) || null;
    const uKode = user?.kode ? String(user.kode).padStart(3,'0') : '000';
    window.currentUser = { kode: uKode };

    await populateKantorOptionsFP(uKode);

    try { 
        const res = await fetch('./api/date/');
        const j = await res.json();
        if(j?.data){
            document.getElementById('closing_date').value = j.data.last_closing;
            document.getElementById('harian_date').value  = j.data.last_created;
            currentFilter = { closing: j.data.last_closing, harian: j.data.last_created };
            fetchFlowPar();
        }
    } catch(e) { 
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('closing_date').value = today;
        document.getElementById('harian_date').value = today;
        currentFilter = { closing: today, harian: today };
        fetchFlowPar();
    }
  });

  // --- POPULATE DROPDOWN KANTOR (UTAMA) ---
  async function populateKantorOptionsFP(userKode){
      const optKantor = document.getElementById('opt_kantor_rec');

      if(userKode && userKode !== '000'){
          optKantor.innerHTML = `<option value="CAB-${userKode}">CABANG ${userKode}</option>`;
          optKantor.value = `CAB-${userKode}`;
          optKantor.disabled = true;
          return; 
      }

      try {
          const res = await fetch('./api/kode/', { 
              method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) 
          });
          const json = await res.json();
          let list = json.data || [];
          
          let html = `
              <option value="ALL">Konsolidasi</option>
              <option value="KOR-SEMARANG">Korwil Semarang</option>
              <option value="KOR-SOLO">Korwil Solo</option>
              <option value="KOR-BANYUMAS">Korwil Banyumas</option>
              <option value="KOR-PEKALONGAN">Korwil Pekalongan</option>
          `;
          list.filter(x => x.kode_kantor !== '000').sort((a,b) => String(a.kode_kantor).localeCompare(b.kode_kantor)).forEach(it => {
              const kode = String(it.kode_kantor).padStart(3,'0');
              html += `<option value="CAB-${kode}">${kode} - ${it.nama_kantor}</option>`;
          });
          optKantor.innerHTML = html;
          optKantor.disabled = false;
      } catch(e){
          optKantor.innerHTML = `<option value="ALL">Error Load Area</option>`;
      }
  }

  // --- FILTER SUBMIT ---
  document.getElementById('filterForm').addEventListener('submit', e => {
    e.preventDefault();
    applyFlowParFilter();
  });

  ['closing_date', 'harian_date', 'opt_kantor_rec'].forEach(id => {
      document.getElementById(id)?.addEventListener('change', applyFlowParFilter);
  });

  function applyFlowParFilter() {
      currentFilter.closing = document.getElementById('closing_date').value;
      currentFilter.harian  = document.getElementById('harian_date').value;
      sortState = { column:null, direction:1 };
      if(window.innerWidth < 1280) {
          document.getElementById('filterPanel').classList.add('hidden');
      }
      fetchFlowPar();
  }

  // --- FETCH REKAP ---
  async function fetchFlowPar(){
    const loading = document.getElementById('loadingFP');
    loading.classList.remove('hidden'); 

    if(fpAbort) fpAbort.abort();
    fpAbort = new AbortController();

    const tbody = document.getElementById('fpBody');
    const ttotal = document.getElementById('fpTotalRow');
    tbody.innerHTML = ''; ttotal.innerHTML = '';

    const kantor = document.getElementById('opt_kantor_rec').value || 'ALL';
    document.getElementById('thNamaFP').innerText = "NAMA KANTOR";

    try {
        const payload = { 
            type: 'Flow Par', 
            closing_date: currentFilter.closing, 
            harian_date: currentFilter.harian
        };
        if(kantor.startsWith('KOR-')) {
            payload.korwil = kantor.replace('KOR-', '');
        } else if(kantor.startsWith('CAB-')) {
            payload.kode_kantor = kantor.replace('CAB-', '');
        }

        const res = await fetch('./api/flow_par/', {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify(payload),
            signal: fpAbort.signal
        });
        const json = await res.json();
        
        let data = [];
        let totalRow = null;

        if(json.data && json.data.data && json.data.grand_total) {
            data = json.data.data; 
            totalRow = json.data.grand_total;
        } else if (Array.isArray(json.data)) {
            data = json.data;
            totalRow = data.find(d => String(d.kode_cabang).toUpperCase() === 'TOTAL' || String(d.nama_kantor).toUpperCase().includes('TOTAL'));
            data = data.filter(d => d !== totalRow);
        }

        window.fpGtRaw = totalRow;
        window.fpDataRaw = data;
        window.fpDataRaw.sort((a,b) => kodeNum(a.kode_cabang || a.kode_unit) - kodeNum(b.kode_cabang || b.kode_unit));

        syncFlowParResponsiveView(false);
        renderTotal(totalRow);
        renderRows(window.fpDataRaw);

        // Selalu kembali ke sisi kiri pada mobile agar kolom AREA terlihat.
        if (window.innerWidth < 768) {
            const scroller = document.getElementById('fpScroller');
            requestAnimationFrame(() => {
                if (scroller) scroller.scrollLeft = 0;
            });
        }

    } catch(err){
        if(err.name !== 'AbortError') {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="12" class="p-8 text-center text-red-500 font-bold bg-red-50">Gagal memuat data.</td></tr>`;
        }
    } finally {
        loading.classList.add('hidden'); 
        setTimeout(updateFpStickyHeader, 50);
    }
  }

  // --- RENDER TOTAL & BISA DIKLIK ---
  function renderTotal(tot){
      const el = document.getElementById('fpTotalRow');
      if(!tot) return;

      const targetKode = detailTargetCode('000');

      if (isMobileFlowPar()) {
          el.innerHTML = `
            <tr class="row-total">
                <td class="sticky-left-2 text-left font-extrabold text-blue-900 pl-2 border-r border-blue-300"><div>TOTAL</div></td>
                <td class="text-right font-bold bg-blue-100/30 border-r border-blue-300 fp-mobile-metric-cell">${flowCellStacked(tot.noa_jt_lain, tot.nom_jt_lain, targetKode, 'jt_lain', 'slate')}</td>
                <td class="text-right font-bold bg-purple-100/30 border-r border-blue-300 fp-mobile-metric-cell">${flowCellStacked(tot.noa_pokok_90, tot.nom_pokok_90, targetKode, 'pokok_90', 'orange')}</td>
                <td class="text-right font-bold bg-amber-100/30 border-r border-blue-300 fp-mobile-metric-cell">${flowCellStacked(tot.noa_bunga_90, tot.nom_bunga_90, targetKode, 'bunga_90', 'amber')}</td>
                <td class="text-right font-bold bg-rose-100/30 border-r border-blue-300 fp-mobile-metric-cell">${flowCellStacked(tot.noa_pokok_bunga_90, tot.nom_pokok_bunga_90, targetKode, 'pokok_bunga_90', 'red')}</td>
                <td class="text-right font-bold bg-cyan-100/40 fp-mobile-total-cell">${flowCellStacked(tot.noa_flow, tot.baki_debet_flow, targetKode, '', 'blue')}</td>
            </tr>
          `;
      } else {
          el.innerHTML = `
            <tr class="row-total">
                <td class="sticky-left-1 hidden md:table-cell font-mono font-bold text-slate-500 text-xs border-r border-blue-300">ALL</td>
                <td class="sticky-left-2 text-left font-extrabold text-blue-900 pl-2 border-r border-blue-300">TOTAL</td>
                <td class="text-center font-bold bg-blue-100/30 border-r border-blue-300 fp-noa-cell">${flowCellNoa(tot.noa_jt_lain, targetKode, 'jt_lain', 'slate')}</td>
                <td class="text-right font-bold bg-blue-100/30 border-r border-blue-300 fp-nom-cell">${flowCellNom(tot.noa_jt_lain, tot.nom_jt_lain, targetKode, 'jt_lain', 'slate')}</td>
                <td class="text-center font-bold bg-purple-100/30 border-r border-blue-300 fp-noa-cell">${flowCellNoa(tot.noa_pokok_90, targetKode, 'pokok_90', 'orange')}</td>
                <td class="text-right font-bold bg-purple-100/30 border-r border-blue-300 fp-nom-cell">${flowCellNom(tot.noa_pokok_90, tot.nom_pokok_90, targetKode, 'pokok_90', 'orange')}</td>
                <td class="text-center font-bold bg-amber-100/30 border-r border-blue-300 fp-noa-cell">${flowCellNoa(tot.noa_bunga_90, targetKode, 'bunga_90', 'amber')}</td>
                <td class="text-right font-bold bg-amber-100/30 border-r border-blue-300 fp-nom-cell">${flowCellNom(tot.noa_bunga_90, tot.nom_bunga_90, targetKode, 'bunga_90', 'amber')}</td>
                <td class="text-center font-bold bg-rose-100/30 border-r border-blue-300 fp-noa-cell">${flowCellNoa(tot.noa_pokok_bunga_90, targetKode, 'pokok_bunga_90', 'red')}</td>
                <td class="text-right font-bold bg-rose-100/30 border-r border-blue-300 fp-nom-cell">${flowCellNom(tot.noa_pokok_bunga_90, tot.nom_pokok_bunga_90, targetKode, 'pokok_bunga_90', 'red')}</td>
                <td class="text-center font-bold bg-cyan-100/40 border-r border-blue-300 fp-noa-cell">${flowCellNoa(tot.noa_flow, targetKode, '', 'blue')}</td>
                <td class="text-right font-bold bg-cyan-100/40 fp-nom-cell">${flowCellNom(tot.noa_flow, tot.baki_debet_flow, targetKode, '', 'blue')}</td>
            </tr>
          `;
      }
  }

  function renderRows(rows){
      const tbody = document.getElementById('fpBody');
      if(rows.length === 0){
          const colspan = isMobileFlowPar() ? 6 : 12;
          tbody.innerHTML = `<tr><td colspan="${colspan}" class="p-8 text-center text-slate-400">Tidak ada data.</td></tr>`;
          return;
      }

      if (isMobileFlowPar()) {
          tbody.innerHTML = rows.map(r => {
              const rawKode = r.kode_cabang || r.kode_unit || '';
              const kode = String(rawKode).padStart(3,'0');
              const nama = r.nama_kantor || r.nama_unit || '-';

              return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition">
                    <td class="sticky-left-2 text-left font-bold text-slate-700 text-xs pl-2 border-r border-slate-100"><div title="${nama}">${nama}</div></td>
                    <td class="text-right text-xs bg-blue-50/10 border-r border-slate-200 fp-mobile-metric-cell">${flowCellStacked(r.noa_jt_lain, r.nom_jt_lain, kode, 'jt_lain', 'slate')}</td>
                    <td class="text-right text-xs bg-purple-50/10 border-r border-slate-200 fp-mobile-metric-cell">${flowCellStacked(r.noa_pokok_90, r.nom_pokok_90, kode, 'pokok_90', 'orange')}</td>
                    <td class="text-right text-xs bg-amber-50/10 border-r border-slate-200 fp-mobile-metric-cell">${flowCellStacked(r.noa_bunga_90, r.nom_bunga_90, kode, 'bunga_90', 'amber')}</td>
                    <td class="text-right text-xs bg-rose-50/10 border-r border-slate-200 fp-mobile-metric-cell">${flowCellStacked(r.noa_pokok_bunga_90, r.nom_pokok_bunga_90, kode, 'pokok_bunga_90', 'red')}</td>
                    <td class="text-right text-xs bg-cyan-50/10 fp-mobile-total-cell">${flowCellStacked(r.noa_flow, r.baki_debet_flow, kode, '', 'blue')}</td>
                </tr>
              `;
          }).join('');

          tbody.innerHTML += `<tr style="height: 44px;"><td colspan="6" class="border-none bg-transparent"></td></tr>`;
      } else {
          tbody.innerHTML = rows.map(r => {
              const rawKode = r.kode_cabang || r.kode_unit || '';
              const kode = String(rawKode).padStart(3,'0');
              const nama = r.nama_kantor || r.nama_unit || '-';

              return `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition">
                    <td class="sticky-left-1 hidden md:table-cell font-mono font-bold text-slate-500 text-xs border-r border-slate-100">${kode}</td>
                    <td class="sticky-left-2 text-left font-bold text-slate-700 text-xs md:text-sm pl-2 border-r border-slate-100"><div class="truncate" title="${nama}">${nama}</div></td>
                    <td class="text-center text-xs bg-blue-50/10 border-r border-slate-100 fp-noa-cell">${flowCellNoa(r.noa_jt_lain, kode, 'jt_lain', 'slate')}</td>
                    <td class="text-right text-xs bg-blue-50/10 border-r border-slate-200 fp-nom-cell">${flowCellNom(r.noa_jt_lain, r.nom_jt_lain, kode, 'jt_lain', 'slate')}</td>
                    <td class="text-center text-xs bg-purple-50/10 border-r border-slate-100 fp-noa-cell">${flowCellNoa(r.noa_pokok_90, kode, 'pokok_90', 'orange')}</td>
                    <td class="text-right text-xs bg-purple-50/10 border-r border-slate-200 fp-nom-cell">${flowCellNom(r.noa_pokok_90, r.nom_pokok_90, kode, 'pokok_90', 'orange')}</td>
                    <td class="text-center text-xs bg-amber-50/10 border-r border-slate-100 fp-noa-cell">${flowCellNoa(r.noa_bunga_90, kode, 'bunga_90', 'amber')}</td>
                    <td class="text-right text-xs bg-amber-50/10 border-r border-slate-200 fp-nom-cell">${flowCellNom(r.noa_bunga_90, r.nom_bunga_90, kode, 'bunga_90', 'amber')}</td>
                    <td class="text-center text-xs bg-rose-50/10 border-r border-slate-100 fp-noa-cell">${flowCellNoa(r.noa_pokok_bunga_90, kode, 'pokok_bunga_90', 'red')}</td>
                    <td class="text-right text-xs bg-rose-50/10 border-r border-slate-200 fp-nom-cell">${flowCellNom(r.noa_pokok_bunga_90, r.nom_pokok_bunga_90, kode, 'pokok_bunga_90', 'red')}</td>
                    <td class="text-center text-xs bg-cyan-50/10 border-r border-slate-100 fp-noa-cell">${flowCellNoa(r.noa_flow, kode, '', 'blue')}</td>
                    <td class="text-right text-xs bg-cyan-50/10 fp-nom-cell">${flowCellNom(r.noa_flow, r.baki_debet_flow, kode, '', 'blue')}</td>
                </tr>
              `;
          }).join('');

          tbody.innerHTML += `<tr style="height: 60px;"><td colspan="12" class="border-none bg-transparent"></td></tr>`;
      }
  }

  // --- SORTING REKAP ---
  const doSort = (col) => {
      sortState = { column: col, direction: sortState.column === col ? -sortState.direction : 1 };
      const sorted = [...window.fpDataRaw].sort((a,b) => {
          if (col === 'kode') return (kodeNum(a.kode_cabang || a.kode_unit) - kodeNum(b.kode_cabang || b.kode_unit)) * sortState.direction;
          if (col === 'nama') return String(a.nama_kantor || a.nama_unit || '').localeCompare(String(b.nama_kantor || b.nama_unit || '')) * sortState.direction;
          const key = col === 'noa' ? 'noa_flow' : 'baki_debet_flow';
          return (num(a[key]) - num(b[key])) * sortState.direction;
      });
      document.getElementById('sortNoa').innerText = `NOA ${col==='noa' ? (sortState.direction>0?'ASC':'DESC') : ''}`;
      document.getElementById('sortBaki').innerText = `NOM ${col==='baki' ? (sortState.direction>0?'ASC':'DESC') : ''}`;
      renderRows(sorted);
  };

  // --- EXPORT EXCEL REKAP ---
  function exportFlowParExcel() {
      const rows = window.fpDataRaw || [];
      const gt = window.fpGtRaw || null;
      if(rows.length === 0) { alert("Tidak ada data rekap untuk diexport!"); return; }

      let table = `<table border="1">
          <thead>
              <tr>
                  <th style="background-color:#eff6ff;">KODE</th>
                  <th style="background-color:#eff6ff;">NAMA KANTOR</th>
                  <th style="background-color:#eff6ff;">NOA FLOW</th>
                  <th style="background-color:#eff6ff;">NOM FLOW</th>
                  <th style="background-color:#eff6ff;">NOA JT / LAIN / ONE OBLIGOR</th>
                  <th style="background-color:#eff6ff;">NOM JT / LAIN / ONE OBLIGOR</th>
                  <th style="background-color:#eff6ff;">NOA POKOK > 90</th>
                  <th style="background-color:#eff6ff;">NOM POKOK > 90</th>
                  <th style="background-color:#eff6ff;">NOA BUNGA > 90</th>
                  <th style="background-color:#eff6ff;">NOM BUNGA > 90</th>
                  <th style="background-color:#eff6ff;">NOA POKOK + BUNGA > 90</th>
                  <th style="background-color:#eff6ff;">NOM POKOK + BUNGA > 90</th>
              </tr>
          </thead>
          <tbody>`;
      
      if(gt) {
          table += `<tr>
              <td style="font-weight:bold;"></td>
              <td style="font-weight:bold;">${gt.nama_kantor || 'GRAND TOTAL'}</td>
              <td style="font-weight:bold;">${gt.noa_flow}</td>
              <td style="font-weight:bold;">${gt.baki_debet_flow}</td>
              <td style="font-weight:bold;">${gt.noa_jt_lain || 0}</td>
              <td style="font-weight:bold;">${gt.nom_jt_lain || 0}</td>
              <td style="font-weight:bold;">${gt.noa_pokok_90 || 0}</td>
              <td style="font-weight:bold;">${gt.nom_pokok_90 || 0}</td>
              <td style="font-weight:bold;">${gt.noa_bunga_90 || 0}</td>
              <td style="font-weight:bold;">${gt.nom_bunga_90 || 0}</td>
              <td style="font-weight:bold;">${gt.noa_pokok_bunga_90 || 0}</td>
              <td style="font-weight:bold;">${gt.nom_pokok_bunga_90 || 0}</td>
          </tr>`;
      }

      rows.forEach(r => {
          const kode = r.kode_cabang || r.kode_unit || '-';
          const nama = r.nama_kantor || r.nama_unit || '-';
          table += `<tr>
              <td style="mso-number-format:'\\@'">${kode}</td>
              <td>${nama}</td>
              <td>${r.noa_flow}</td>
              <td>${r.baki_debet_flow}</td>
              <td>${r.noa_jt_lain || 0}</td>
              <td>${r.nom_jt_lain || 0}</td>
              <td>${r.noa_pokok_90 || 0}</td>
              <td>${r.nom_pokok_90 || 0}</td>
              <td>${r.noa_bunga_90 || 0}</td>
              <td>${r.nom_bunga_90 || 0}</td>
              <td>${r.noa_pokok_bunga_90 || 0}</td>
              <td>${r.nom_pokok_bunga_90 || 0}</td>
          </tr>`;
      });
      table += `</tbody></table>`;

      const tgl = document.getElementById('harian_date').value;
      const blob = new Blob([table], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      a.download = `Rekap_Flow_PAR_${tgl}.xls`;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
  }

  // --- MODAL & ACCESS LOGIC ---
  function closeModalPeringatan() {
      const modal = document.getElementById('modalPeringatan');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
  }

  window.checkAccessAndOpenModal = function(targetKode, klasifikasi = '') {
      const userKode = window.currentUser.kode;
      const targetCabang = targetKode.length >= 3 ? targetKode.substring(0,3) : targetKode; 
      
      if (userKode !== '000' && userKode !== targetCabang) {
          document.getElementById('warnUserLvl').innerText = `Unit ${userKode}`;
          document.getElementById('warnTargetLvl').innerText = `Unit ${targetCabang}`;
          const modalWarn = document.getElementById('modalPeringatan');
          modalWarn.classList.remove('hidden');
          modalWarn.classList.add('flex');
          return;
      }
      openModalDetail(targetKode, klasifikasi);
  };

  async function openModalDetail(kode, klasifikasi = ''){
      const targetCabang = kode.length >= 3 ? kode.substring(0,3) : kode;
      const targetKankas = kode.length > 3 ? kode : '';
      const areaVal = document.getElementById('opt_kantor_rec')?.value || 'ALL';
      
      currentDetailKode = targetCabang; 
      currentDetailKorwil = (targetCabang === '000' && areaVal.startsWith('KOR-')) ? areaVal.replace('KOR-', '') : '';
      currentDetailKlasifikasi = klasifikasi || '';
      
      const modal = document.getElementById('modalDebiturFlowPar');
      const title = document.getElementById('modalTitleFlowPar');
      const sub   = document.getElementById('modalSubtitleFP');
      
      modal.classList.remove('hidden'); modal.classList.add('flex');
      let titleLabel = currentDetailKorwil ? `KORWIL ${currentDetailKorwil}` : (kode === '000' ? 'KONSOLIDASI' : kode);
      title.innerHTML = `Detail Debitur <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded font-mono border border-blue-200">${titleLabel}</span>`;
      const labelKlas = currentDetailKlasifikasi ? ` | ${flowClassLabels[currentDetailKlasifikasi] || currentDetailKlasifikasi}` : '';
      sub.innerText = `Posisi: ${formatDate(currentFilter.closing)} vs ${formatDate(currentFilter.harian)}${labelKlas}`;
      
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

      // Auto close modal filter on mobile when opening
      if(window.innerWidth < 768) {
          const mPanel = document.getElementById('modalFilterPanel');
          if(mPanel) mPanel.classList.add('hidden');
      }

      const searchFP = document.getElementById('modalSearchFP');
      const statusFP = document.getElementById('modalCommitmentFilterFP');
      if (searchFP) searchFP.value = '';
      if (statusFP) statusFP.value = 'all';
      fetchDetailFlowPar();
  }

  function fpEscape(value) {
      return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
  }

  function getFlowKolek(item) {
      return String(item?.kolektibilitas ?? item?.kolek_harian ?? item?.kolek ?? '-').trim() || '-';
  }

  function hasFlowCommitment(item) {
      const text = String(item?.komitmen ?? '').trim();
      const normalized = text.toLowerCase();
      const validText = text !== '' && text !== '-' && !['belum ada', 'belum komitmen', 'tidak ada'].includes(normalized);
      return validText || !!item?.tgl_pembayaran || num(item?.nominal) > 0;
  }

  function flowKolekBadge(value) {
      const kolek = String(value || '-').toUpperCase();
      const cls = ['L'].includes(kolek) ? 'good' : (['DP','DPK'].includes(kolek) ? 'watch' : (['KL','D','M'].includes(kolek) ? 'bad' : 'neutral'));
      return `<span class="fp-kolek-badge ${cls}">${fpEscape(kolek)}</span>`;
  }

  function flowCommitBadge(item) {
      const committed = hasFlowCommitment(item);
      return `<span class="fp-commit-badge ${committed ? 'yes' : 'no'}">${committed ? 'Sudah Komitmen' : 'Belum Komitmen'}</span>`;
  }

  function getFilteredDetailFlowPar() {
      const query = String(document.getElementById('modalSearchFP')?.value || '').trim().toLowerCase();
      const status = document.getElementById('modalCommitmentFilterFP')?.value || 'all';

      return detailDataRaw.filter(item => {
          const committed = hasFlowCommitment(item);
          if (status === 'committed' && !committed) return false;
          if (status === 'none' && committed) return false;
          if (!query) return true;
          const haystack = [
              item.no_rekening,
              item.nama_nasabah,
              item.alamat,
              getFlowKolek(item),
              item.komitmen,
              item.tgl_pembayaran
          ].join(' ').toLowerCase();
          return haystack.includes(query);
      });
  }

  function renderProjectionSummaryFP(rows) {
      const committed = rows.filter(hasFlowCommitment);
      const promise = committed.reduce((sum, item) => sum + num(item.nominal), 0);
      document.getElementById('fpSummaryCandidate').textContent = `${fmtInt(rows.length)} Debitur`;
      document.getElementById('fpSummaryCommitted').textContent = `${fmtInt(committed.length)} Debitur`;
      document.getElementById('fpSummaryUncommitted').textContent = `${fmtInt(rows.length - committed.length)} Debitur`;
      document.getElementById('fpSummaryPromise').textContent = `Rp ${fmtNom(promise)}`;
  }

  function renderDetailMobileCardsFP(rows) {
      const target = document.getElementById('modalMobileCardsFP');
      if (!target) return;
      if (!rows.length) {
          target.innerHTML = `<div class="fp-detail-empty"><b>Data tidak ditemukan</b><span class="mt-1 text-[9px]">Coba ubah pencarian atau filter komitmen.</span></div>`;
          return;
      }

      target.innerHTML = rows.map(item => {
          const totalTunggakan = num(item.total_tunggakan) > 0 ? num(item.total_tunggakan) : num(item.tunggakan_pokok) + num(item.tunggakan_bunga);
          const hari = Number.isFinite(+item.hari_menunggak) ? +item.hari_menunggak : calcHariMenunggak(item.tgl_jatuh_tempo);
          return `
            <article class="fp-mobile-card">
              <div class="fp-mobile-card-head">
                <div class="fp-mobile-identity">
                  <div class="fp-mobile-rek">${fpEscape(item.no_rekening || '-')}</div>
                  <div class="fp-mobile-name" title="${fpEscape(item.nama_nasabah || '-')}">${fpEscape(item.nama_nasabah || '-')}</div>
                </div>
                <div class="flex flex-col items-end gap-1">
                  ${flowKolekBadge(getFlowKolek(item))}
                  ${flowCommitBadge(item)}
                </div>
              </div>
              <div class="fp-mobile-address">${fpEscape(item.alamat || 'Alamat belum tersedia')}</div>
              <div class="fp-mobile-metrics">
                <div class="fp-mobile-metric"><div class="fp-mobile-label">Baki Debet</div><div class="fp-mobile-value">Rp ${fmtNom(item.baki_debet)}</div></div>
                <div class="fp-mobile-metric"><div class="fp-mobile-label">Total Tunggakan</div><div class="fp-mobile-value red">Rp ${fmtNom(totalTunggakan)}</div></div>
                <div class="fp-mobile-metric"><div class="fp-mobile-label">Saldo Tabungan</div><div class="fp-mobile-value green">Rp ${fmtNom(item.saldo_akhir)}</div></div>
                <div class="fp-mobile-metric"><div class="fp-mobile-label">DPD / TP / TB</div><div class="fp-mobile-value">${fmtInt(hari)} / ${fmtInt(item.hari_menunggak_pokok)} / ${fmtInt(item.hari_menunggak_bunga)}</div></div>
                <div class="fp-mobile-metric"><div class="fp-mobile-label">Angsuran Pokok</div><div class="fp-mobile-value">Rp ${fmtNom(item.angsuran_pokok)}</div></div>
                <div class="fp-mobile-metric"><div class="fp-mobile-label">Angsuran Bunga</div><div class="fp-mobile-value">Rp ${fmtNom(item.angsuran_bunga)}</div></div>
              </div>
              <div class="fp-mobile-commit">
                <div class="fp-mobile-commit-row"><span>Komitmen</span><b>${fpEscape(item.komitmen || '-')}</b></div>
                <div class="fp-mobile-commit-row"><span>Janji bayar</span><b>${item.tgl_pembayaran ? formatDate(item.tgl_pembayaran) : '-'}</b></div>
                <div class="fp-mobile-commit-row"><span>Nominal janji</span><b>Rp ${fmtNom(item.nominal)}</b></div>
                <div class="fp-mobile-commit-row"><span>Jatuh tempo</span><b>${item.tgl_jatuh_tempo ? formatDate(item.tgl_jatuh_tempo) : '-'}</b></div>
              </div>
            </article>
          `;
      }).join('');
  }

  window.renderDetailFlowPar = function() {
      const tbody = document.getElementById('modalBodyRows');
      const ttot = document.getElementById('modalTotalRow');
      detailDataView = getFilteredDetailFlowPar();
      renderProjectionSummaryFP(detailDataView);
      renderDetailMobileCardsFP(detailDataView);

      if (!detailDataView.length) {
          tbody.innerHTML = `<tr><td colspan="20" class="p-10 text-center text-slate-400">Data tidak ditemukan.</td></tr>`;
          ttot.innerHTML = '';
          return;
      }

      let totals = { bd:0, tp:0, tb:0, tt:0, sa:0, ap:0, ab:0, promise:0, committed:0 };
      const refDate = currentFilter.harian ? new Date(currentFilter.harian) : new Date();
      const eom = endOfMonth(refDate) || endOfMonth(new Date());

      tbody.innerHTML = detailDataView.map(item => {
          const hari = Number.isFinite(+item.hari_menunggak) ? +item.hari_menunggak : calcHariMenunggak(item.tgl_jatuh_tempo);
          const jt = item.tgl_jatuh_tempo ? startOfDay(new Date(item.tgl_jatuh_tempo)) : null;
          const overdue = jt && jt.getTime() <= eom.getTime();
          const dpdTP = Number.isFinite(+item.hari_menunggak_pokok) ? +item.hari_menunggak_pokok : 0;
          const dpdTB = Number.isFinite(+item.hari_menunggak_bunga) ? +item.hari_menunggak_bunga : 0;
          const totalTunggakan = num(item.total_tunggakan) > 0 ? num(item.total_tunggakan) : num(item.tunggakan_pokok) + num(item.tunggakan_bunga);
          const committed = hasFlowCommitment(item);

          totals.bd += num(item.baki_debet);
          totals.tp += num(item.tunggakan_pokok);
          totals.tb += num(item.tunggakan_bunga);
          totals.tt += totalTunggakan;
          totals.sa += num(item.saldo_akhir);
          totals.ap += num(item.angsuran_pokok);
          totals.ab += num(item.angsuran_bunga);
          totals.promise += num(item.nominal);
          totals.committed += committed ? 1 : 0;

          return `
            <tr class="${overdue ? 'overdue' : ''}">
              <td class="modal-freeze-1 font-mono font-bold text-slate-600">${fpEscape(item.no_rekening || '-')}</td>
              <td class="modal-freeze-2 font-bold text-slate-800" title="${fpEscape(item.nama_nasabah || '-')}"><div class="truncate">${fpEscape(item.nama_nasabah || '-')}</div></td>
              <td class="text-center">${flowKolekBadge(getFlowKolek(item))}</td>
              <td class="fp-address-cell" title="${fpEscape(item.alamat || '-')}">${fpEscape(item.alamat || '-')}</td>
              <td class="text-right font-bold text-blue-700">${fmtNom(item.baki_debet)}</td>
              <td class="text-right">${fmtNom(item.tunggakan_pokok)}</td>
              <td class="text-right">${fmtNom(item.tunggakan_bunga)}</td>
              <td class="text-right font-bold text-red-700 bg-red-50">${fmtNom(totalTunggakan)}</td>
              <td class="text-right font-bold text-emerald-700">${fmtNom(item.saldo_akhir)}</td>
              <td class="text-center">${formatJTByRule(item.tgl_jatuh_tempo)}</td>
              <td class="text-center font-bold">${fmtInt(hari)}</td>
              <td class="text-center ${dpdTP >= 90 ? 'hot90' : ''}">${fmtInt(dpdTP)}</td>
              <td class="text-center ${dpdTB >= 90 ? 'hot90' : ''}">${fmtInt(dpdTB)}</td>
              <td class="text-right text-slate-600">${fmtNom(item.angsuran_pokok)}</td>
              <td class="text-right text-slate-600">${fmtNom(item.angsuran_bunga)}</td>
              <td class="text-center text-slate-500">${item.tgl_trans ? formatDate(item.tgl_trans) : '-'}</td>
              <td class="text-center">${flowCommitBadge(item)}</td>
              <td class="text-left max-w-[190px] overflow-hidden text-ellipsis text-slate-600" title="${fpEscape(item.komitmen || '-')}">${fpEscape(item.komitmen || '-')}</td>
              <td class="text-center font-semibold">${item.tgl_pembayaran ? formatDate(item.tgl_pembayaran) : '-'}</td>
              <td class="text-right font-bold">${fmtNom(item.nominal)}</td>
            </tr>
          `;
      }).join('');

      ttot.innerHTML = `
        <tr class="modal-total-row">
          <td class="modal-freeze-1">TOTAL</td>
          <td class="modal-freeze-2">${fmtInt(detailDataView.length)} Debitur</td>
          <td class="text-center">-</td>
          <td></td>
          <td class="text-right">${fmtNom(totals.bd)}</td>
          <td class="text-right">${fmtNom(totals.tp)}</td>
          <td class="text-right">${fmtNom(totals.tb)}</td>
          <td class="text-right text-red-700">${fmtNom(totals.tt)}</td>
          <td class="text-right">${fmtNom(totals.sa)}</td>
          <td colspan="4"></td>
          <td class="text-right">${fmtNom(totals.ap)}</td>
          <td class="text-right">${fmtNom(totals.ab)}</td>
          <td></td>
          <td class="text-center">${fmtInt(totals.committed)} Komitmen</td>
          <td></td>
          <td></td>
          <td class="text-right">${fmtNom(totals.promise)}</td>
        </tr>
      `;
  };

  async function fetchDetailFlowPar() {
      const tbody = document.getElementById('modalBodyRows');
      const ttot = document.getElementById('modalTotalRow');
      const mobileCards = document.getElementById('modalMobileCardsFP');
      const kankas = document.getElementById('modalFilterKankas').value || '';

      if (window.innerWidth < 768) {
          document.getElementById('modalFilterPanel').classList.add('hidden');
      }

      tbody.innerHTML = `<tr><td colspan="20"><div class="fp-detail-loading"><div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mb-3"></div><span>Sedang mengambil data...</span></div></td></tr>`;
      ttot.innerHTML = '';
      if (mobileCards) mobileCards.innerHTML = `<div class="fp-detail-loading"><div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mb-3"></div><span>Sedang mengambil data...</span></div>`;
      renderProjectionSummaryFP([]);

      try {
          const payload = {
              type:'KL Baru',
              kode_kantor:currentDetailKode === '000' ? '' : currentDetailKode,
              korwil:currentDetailKorwil,
              kode_kankas:kankas,
              closing_date:currentFilter.closing,
              harian_date:currentFilter.harian,
              klasifikasi_flow:currentDetailKlasifikasi
          };

          const res = await fetch('./api/flow_par/', {
              method:'POST',
              headers:{'Content-Type':'application/json'},
              body:JSON.stringify(payload)
          });
          const json = await res.json();
          detailDataRaw = Array.isArray(json.data) ? json.data : [];
          renderDetailFlowPar();

          const scroll = document.getElementById('modalScroll');
          if (scroll) { scroll.scrollTop = 0; scroll.scrollLeft = 0; }
      } catch (error) {
          console.error(error);
          detailDataRaw = [];
          detailDataView = [];
          tbody.innerHTML = `<tr><td colspan="20"><div class="fp-detail-empty text-red-500"><b>Gagal memuat data</b><span class="mt-1 text-[9px]">${fpEscape(error.message)}</span></div></td></tr>`;
          ttot.innerHTML = '';
          if (mobileCards) mobileCards.innerHTML = `<div class="fp-detail-empty text-red-500"><b>Gagal memuat data</b></div>`;
          renderProjectionSummaryFP([]);
      }
  }

  // --- EXPORT EXCEL DETAIL ---
  function exportDetailExcel() {
      const rows = detailDataView;
      if (!rows.length) { alert('Tidak ada detail untuk diexport!'); return; }

      let table = `<table border="1">
        <thead><tr>
          <th style="background-color:#dbeafe;">NO REKENING</th>
          <th style="background-color:#dbeafe;">NAMA NASABAH</th>
          <th style="background-color:#dbeafe;">KOLEKTIBILITAS</th>
          <th style="background-color:#dbeafe;">ALAMAT</th>
          <th style="background-color:#dbeafe;">BAKI DEBET</th>
          <th style="background-color:#dbeafe;">TUNGG. POKOK</th>
          <th style="background-color:#dbeafe;">TUNGG. BUNGA</th>
          <th style="background-color:#fee2e2;">TOTAL TUNGGAKAN</th>
          <th style="background-color:#dcfce7;">SALDO TABUNGAN</th>
          <th style="background-color:#dbeafe;">TGL JATUH TEMPO</th>
          <th style="background-color:#dbeafe;">DPD</th>
          <th style="background-color:#dbeafe;">DPD TP</th>
          <th style="background-color:#dbeafe;">DPD TB</th>
          <th style="background-color:#dbeafe;">ANGS. POKOK</th>
          <th style="background-color:#dbeafe;">ANGS. BUNGA</th>
          <th style="background-color:#dbeafe;">TGL TRANS</th>
          <th style="background-color:#fef3c7;">STATUS KOMITMEN</th>
          <th style="background-color:#fef3c7;">KOMITMEN</th>
          <th style="background-color:#fef3c7;">TGL JANJI BAYAR</th>
          <th style="background-color:#fef3c7;">NOMINAL JANJI BAYAR</th>
        </tr></thead><tbody>`;

      rows.forEach(item => {
          const totalTunggakan = num(item.total_tunggakan) > 0 ? num(item.total_tunggakan) : num(item.tunggakan_pokok) + num(item.tunggakan_bunga);
          const hari = Number.isFinite(+item.hari_menunggak) ? +item.hari_menunggak : calcHariMenunggak(item.tgl_jatuh_tempo);
          table += `<tr>
            <td style="mso-number-format:'\@'">${fpEscape(item.no_rekening || '')}</td>
            <td>${fpEscape(item.nama_nasabah || '')}</td>
            <td>${fpEscape(getFlowKolek(item))}</td>
            <td>${fpEscape(item.alamat || '')}</td>
            <td>${num(item.baki_debet)}</td>
            <td>${num(item.tunggakan_pokok)}</td>
            <td>${num(item.tunggakan_bunga)}</td>
            <td>${totalTunggakan}</td>
            <td>${num(item.saldo_akhir)}</td>
            <td>${fpEscape(item.tgl_jatuh_tempo || '')}</td>
            <td>${hari}</td>
            <td>${num(item.hari_menunggak_pokok)}</td>
            <td>${num(item.hari_menunggak_bunga)}</td>
            <td>${num(item.angsuran_pokok)}</td>
            <td>${num(item.angsuran_bunga)}</td>
            <td>${fpEscape(item.tgl_trans || '')}</td>
            <td>${hasFlowCommitment(item) ? 'Sudah Komitmen' : 'Belum Komitmen'}</td>
            <td>${fpEscape(item.komitmen || '')}</td>
            <td>${fpEscape(item.tgl_pembayaran || '')}</td>
            <td>${num(item.nominal)}</td>
          </tr>`;
      });
      table += '</tbody></table>';

      const blob = new Blob([table], {type:'application/vnd.ms-excel'});
      const a = document.createElement('a');
      const url = URL.createObjectURL(blob);
      const valKankas = document.getElementById('modalFilterKankas').value;
      const downloadName = valKankas || currentDetailKode || 'Konsolidasi';
      a.href = url;
      a.download = `Detail_FlowPAR_${downloadName}_${currentFilter.harian}.xls`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      setTimeout(() => URL.revokeObjectURL(url), 1000);
  }


  // --- TRIGGER UPDATE BULK (TOMBOL UPDATE DI ATAS) ---
  window.gotoUpdateFlowPar = function() {
      const selectedKankas = document.getElementById('modalFilterKankas').value || '';
      const payload = {
          kode_kantor: currentDetailKode === '000' ? '' : currentDetailKode,
          kode_kankas: selectedKankas,
          korwil: currentDetailKorwil,
          klasifikasi_flow: currentDetailKlasifikasi,
          closing_date: currentFilter.closing,
          harian_date: currentFilter.harian
      };
      sessionStorage.setItem("flowpar_update", JSON.stringify(payload));
      window.location.href = './update_flowpar'; 
  };

  const closeFlowParDetailModal = () => {
    const modal = document.getElementById('modalDebiturFlowPar');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    detailDataRaw = [];
    detailDataView = [];
  };
  document.getElementById('btnCloseFP').onclick = closeFlowParDetailModal;
  document.getElementById('modalDebiturFlowPar').addEventListener('click', e => {
    if (e.target.id === 'modalDebiturFlowPar') closeFlowParDetailModal();
  });
</script>

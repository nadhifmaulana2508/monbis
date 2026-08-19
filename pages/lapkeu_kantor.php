<style>
  :root { --primary: #2563eb; --bg: #f8fafc; --text: #334155; }
  
  /* Styling Baris Berdasarkan Level */
  .row-level-1 { background-color: #e2e8f0 !important; font-weight: 800; cursor: pointer; }
  .row-level-2 { background-color: #f1f5f9 !important; font-weight: 700; cursor: pointer; }
  .row-level-3 { background-color: #f8fafc !important; font-weight: 600; cursor: pointer; }
  .row-detail { display: table-row; }
  .hidden-row { display: none; }
  
  .caret { display: inline-block; transition: transform 0.2s; margin-right: 6px; color: #64748b; font-size: 10px;}
  .rotate { transform: rotate(90deg); }

  /* Padding Card diperkecil di HP */
  .rekap-card { background: white; border-radius: 10px; padding: 10px 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
  @media (min-width: 768px) { .rekap-card { padding: 12px 16px; } }

  .val-plus { color: #059669; font-weight: 800; font-family: 'JetBrains Mono', monospace; }
  .val-minus { color: #dc2626; font-weight: 800; font-family: 'JetBrains Mono', monospace; }
  
  .table-container { border: 1px solid #e2e8f0; border-radius: 12px; background: white; overflow: hidden; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
  .report-panel { border: 1px solid #e2e8f0; border-radius: 14px; background: #fff; box-shadow: 0 4px 10px -8px rgb(15 23 42 / .35); }
  .report-section-title { font-size: 11px; line-height: 1.1; font-weight: 900; color: #0f172a; letter-spacing: -.01em; }
  .metric-card { border-radius: 12px; border: 1px solid #e2e8f0; background: linear-gradient(180deg,#fff,#f8fafc); padding: 12px; min-height: 86px; overflow: hidden; }
  .metric-card .label { font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: .08em; }
  .metric-card .value { margin-top: 8px; font-size: clamp(20px, 2.4vw, 32px); line-height: 1; font-weight: 950; font-family: 'JetBrains Mono', monospace; letter-spacing: 0; }
  .metric-card .sub { margin-top: 8px; display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 10px; font-weight: 850; color: #64748b; }
  .metric-blue { border-color:#bae6fd; background: linear-gradient(180deg,#f0f9ff,#fff); }
  .metric-green { border-color:#bbf7d0; background: linear-gradient(180deg,#f0fdf4,#fff); }
  .metric-purple { border-color:#ddd6fe; background: linear-gradient(180deg,#f5f3ff,#fff); }
  .metric-orange { border-color:#fed7aa; background: linear-gradient(180deg,#fff7ed,#fff); }
  .macro-grid { display:grid; grid-template-columns: minmax(0,1.1fr) minmax(0,1fr) minmax(260px,.85fr); gap:12px; }
  .ratio-grid { display:grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap:8px; }
  .ratio-card { border:1px solid #e2e8f0; border-radius:10px; padding:10px; background:#f8fafc; min-height:74px; }
  .ratio-card .label { font-size:9px; text-transform:uppercase; letter-spacing:.08em; font-weight:900; color:#64748b; }
  .ratio-card .value { margin-top:8px; font-size:clamp(17px,1.8vw,24px); line-height:1; font-weight:950; font-family:'JetBrains Mono', monospace; }
  .mini-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:9px 10px; border-radius:10px; background:#f8fafc; border:1px solid #eef2f7; }
  .mini-row .name { min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:11px; font-weight:900; color:#334155; }
  .mini-row .value { flex:0 0 auto; font-size:12px; font-weight:950; font-family:'JetBrains Mono', monospace; color:#0f172a; }
  .week-grid { display:grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap:12px; }
  .week-card { border-radius:14px; border:1px solid #e2e8f0; background:linear-gradient(180deg,#fff,#f8fafc); padding:12px; min-height:190px; }
  .week-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding-bottom:10px; border-bottom:1px solid #e2e8f0; }
  .week-head .label { font-size:16px; font-weight:950; color:#0f172a; }
  .week-head .date { font-size:10px; font-weight:800; color:#64748b; margin-top:2px; }
  .week-lines { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; margin-top:10px; }
  .week-line { border-radius:10px; padding:10px; background:#f8fafc; border:1px solid #eef2f7; }
  .week-line .name { font-size:9px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; color:#64748b; }
  .week-line .value { margin-top:7px; font-size:clamp(15px,1.55vw,21px); font-weight:950; font-family:'JetBrains Mono', monospace; line-height:1; }
  .auto-note { font-size:10px; color:#64748b; font-weight:800; }
  
  /* Input & Button Styling */
  .inp-modern { border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 8px; font-size: 11px; font-weight: 600; background: #fff; width: 100%; height: 34px; color: #334155; transition: all 0.2s; outline: none; }
  @media (min-width: 768px) { .inp-modern { font-size: 13px; height: 40px; padding: 0 12px; } }
  select.inp-modern {
    appearance:none;
    -webkit-appearance:none;
    -moz-appearance:none;
    padding-right:34px;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2.25' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M7 10l5 5 5-5'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 10px center;
    background-size:15px 15px;
    cursor:pointer;
  }
  .inp-modern:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
  .inp-modern:disabled { background-color: #f1f5f9; color: #64748b; cursor: not-allowed; }

  /* Sembunyikan scrollbar di form filter untuk mobile jika ada sisa */
  #filterForm::-webkit-scrollbar { display: none; }
  @media (min-width: 1280px) {
    #rekapContainer { grid-template-columns: repeat(4,minmax(0,1fr)); }
  }
  @media (max-width: 1100px) {
    .macro-grid { grid-template-columns: 1fr; }
    .ratio-grid { grid-template-columns: repeat(3,minmax(0,1fr)); }
  }
  @media (max-width: 720px) {
    .week-grid, .ratio-grid { grid-template-columns: 1fr; }
    .week-lines { grid-template-columns: 1fr; }
    .metric-card { min-height: 78px; padding:10px; }
  }


  /* ========================================================
     LAPORAN KEUANGAN DASHBOARD V2
     Ringkasan makro dan tren mingguan responsif.
     ======================================================== */
  :root {
    --lap-line:#e2e8f0;
    --lap-soft:#f8fafc;
    --lap-muted:#64748b;
    --lap-dark:#0f172a;
  }

  html, body { width:100%; min-width:0; }
  #lapkeuPage {
    width:100%;
    max-width:none !important;
    height:calc(100dvh - 60px) !important;
    padding:5px !important;
    gap:6px !important;
    background:#f8fafc;
    overflow:hidden;
  }
  #lapkeuPage > * { min-width:0; }
  #lapkeuHeader {
    margin:0 !important;
    padding:9px 10px !important;
    border-radius:11px !important;
    box-shadow:0 1px 2px rgba(15,23,42,.04),0 8px 24px rgba(15,23,42,.025) !important;
  }
  #reportPageSubtitle { display:block; }
  #filterForm { min-width:0; }
  .lapkeu-mobile-filter-toggle {
    display:none;
    height:34px;
    padding:0 12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    background:#fff;
    color:#334155;
    font-size:11px;
    font-weight:800;
    align-items:center;
    justify-content:center;
    gap:6px;
    box-shadow:0 1px 2px rgba(15,23,42,.05);
    transition:all .18s ease;
  }
  .lapkeu-mobile-filter-toggle svg { width:14px; height:14px; }
  .lapkeu-mobile-filter-toggle.is-open { color:#2563eb; border-color:#bfdbfe; background:#eff6ff; }
  #rekapContainer {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:6px !important;
    margin:0 !important;
  }
  .table-container {
    min-height:0;
    border-radius:11px !important;
    box-shadow:0 1px 2px rgba(15,23,42,.035) !important;
  }
  .custom-scrollbar::-webkit-scrollbar { width:6px; height:6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background:#f8fafc; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  #customReport { padding:8px !important; min-height:100%; }

  /* Summary utama */
  .overview-card {
    position:relative;
    min-width:0;
    overflow:hidden;
    border:1px solid var(--lap-line);
    border-radius:11px;
    background:#fff;
    padding:10px 11px;
    box-shadow:0 1px 2px rgba(15,23,42,.035);
  }
  .overview-card::before {
    content:'';
    position:absolute;
    left:0; right:0; top:0;
    height:3px;
    background:var(--overview-accent,#3b82f6);
  }
  .overview-card-head { display:flex; align-items:center; justify-content:space-between; gap:7px; }
  .overview-card-icon {
    display:inline-flex; align-items:center; justify-content:center;
    width:25px; height:25px; flex:0 0 25px;
    border-radius:8px;
    background:var(--overview-soft,#eff6ff);
    color:var(--overview-accent,#2563eb);
  }
  .overview-card-icon svg { width:13px; height:13px; }
  .overview-card-label {
    min-width:0;
    font-size:8px;
    line-height:1.1;
    font-weight:900;
    letter-spacing:.065em;
    text-transform:uppercase;
    color:#64748b;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .overview-card-value {
    margin-top:8px;
    font-family:'JetBrains Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:clamp(15px,4vw,20px);
    line-height:1;
    font-weight:900;
    color:var(--overview-text,#1d4ed8);
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    font-variant-numeric:tabular-nums;
  }
  .overview-card-foot {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:5px;
    margin-top:7px;
    font-size:8px;
    font-weight:800;
    color:#94a3b8;
  }
  .overview-card-foot > span:first-child { min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .overview-blue { --overview-accent:#3b82f6; --overview-soft:#eff6ff; --overview-text:#1d4ed8; }
  .overview-green { --overview-accent:#10b981; --overview-soft:#ecfdf5; --overview-text:#047857; }
  .overview-purple { --overview-accent:#8b5cf6; --overview-soft:#f5f3ff; --overview-text:#6d28d9; }
  .overview-orange { --overview-accent:#f97316; --overview-soft:#fff7ed; --overview-text:#c2410c; }
  .overview-red { --overview-accent:#ef4444; --overview-soft:#fef2f2; --overview-text:#dc2626; }
  .lapkeu-date-label-row { display:flex; align-items:center; gap:5px; margin-left:4px; margin-bottom:2px; }
  .lapkeu-date-label-row label { margin:0 !important; }
  .lapkeu-insight-btn {
    width:18px; height:18px; border:1px solid #bfdbfe; border-radius:999px;
    display:inline-flex; align-items:center; justify-content:center;
    background:#eff6ff; color:#2563eb; font-size:11px; line-height:1;
    font-weight:950; cursor:pointer;
  }
  .lapkeu-insight-btn:hover { background:#dbeafe; color:#1d4ed8; }
  .lapkeu-insight-modal {
    position:fixed; inset:0; z-index:80; display:none; align-items:center; justify-content:center;
    padding:18px; background:rgba(15,23,42,.46); backdrop-filter:blur(3px);
  }
  .lapkeu-insight-modal.is-open { display:flex; }
  .lapkeu-insight-card {
    width:min(720px,96vw); max-height:min(620px,88vh); overflow:auto;
    border-radius:16px; border:1px solid #dbeafe; background:#fff;
    box-shadow:0 24px 70px rgba(15,23,42,.28);
  }
  .lapkeu-insight-head {
    position:sticky; top:0; z-index:1; display:flex; align-items:flex-start; justify-content:space-between; gap:12px;
    padding:16px 18px; border-bottom:1px solid #e2e8f0; background:#fff;
  }
  .lapkeu-insight-kicker { font-size:9px; font-weight:950; letter-spacing:.1em; text-transform:uppercase; color:#2563eb; }
  .lapkeu-insight-title { margin-top:3px; font-size:20px; line-height:1.1; font-weight:950; color:#0f172a; }
  .lapkeu-insight-sub { margin-top:4px; font-size:11px; font-weight:750; color:#64748b; }
  .lapkeu-insight-close {
    width:34px; height:34px; border:0; border-radius:999px; background:#f1f5f9;
    color:#334155; font-size:22px; line-height:1; font-weight:700; cursor:pointer;
  }
  .lapkeu-insight-body { padding:16px 18px 18px; }
  .lapkeu-insight-condition {
    margin-bottom:10px;
    padding:11px 13px;
    border:1px solid #bfdbfe;
    border-left:4px solid #2563eb;
    border-radius:11px;
    background:#eff6ff;
    color:#1e3a8a;
    font-size:11px;
    line-height:1.5;
    font-weight:750;
  }
  .lapkeu-insight-condition b { color:#0f172a; }
  .lapkeu-insight-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
  .lapkeu-insight-block {
    border:1px solid #e2e8f0; border-radius:12px; background:#f8fafc; padding:12px;
  }
  .lapkeu-insight-block.full { grid-column:1 / -1; }
  .lapkeu-insight-block h4 { margin:0 0 8px; font-size:11px; font-weight:950; color:#0f172a; }
  .lapkeu-insight-block ul { margin:0; padding-left:16px; color:#334155; font-size:11px; line-height:1.55; font-weight:700; }
  .lapkeu-insight-block li + li { margin-top:5px; }
  .trend-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
    border:1px solid;
    border-radius:999px;
    padding:3px 6px;
    font-size:7px;
    line-height:1;
    font-weight:900;
    white-space:nowrap;
  }
  .trend-up { background:#ecfdf5; border-color:#a7f3d0; color:#047857; }
  .trend-down { background:#fff1f2; border-color:#fecdd3; color:#be123c; }
  .trend-flat { background:#f8fafc; border-color:#e2e8f0; color:#64748b; }

  /* Header setiap laporan custom */
  .report-dashboard { min-height:100%; }
  .report-hero {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom:8px;
    padding:9px 10px;
    border:1px solid #dbeafe;
    border-radius:11px;
    background:linear-gradient(135deg,#eff6ff 0%,#fff 55%,#f5f3ff 100%);
  }
  .report-hero-main { display:flex; align-items:center; gap:9px; min-width:0; }
  .report-hero-icon {
    width:34px; height:34px; flex:0 0 34px;
    display:flex; align-items:center; justify-content:center;
    border-radius:10px;
    background:#2563eb;
    color:#fff;
    box-shadow:0 7px 15px -9px rgba(37,99,235,.9);
  }
  .report-hero-icon svg { width:17px; height:17px; }
  .report-hero-title { font-size:13px; line-height:1.1; font-weight:950; color:#0f172a; }
  .report-hero-sub { margin-top:3px; font-size:8px; line-height:1.35; font-weight:700; color:#64748b; }
  .report-position-badge {
    flex:0 0 auto;
    display:inline-flex;
    align-items:center;
    gap:5px;
    border:1px solid #dbeafe;
    border-radius:999px;
    background:rgba(255,255,255,.85);
    padding:5px 8px;
    color:#1e40af;
    font-size:8px;
    font-weight:900;
    white-space:nowrap;
  }

  /* Section umum */
  .dashboard-section {
    min-width:0;
    border:1px solid var(--lap-line);
    border-radius:12px;
    background:#fff;
    padding:10px;
    box-shadow:0 1px 2px rgba(15,23,42,.025);
  }
  .section-heading {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    margin-bottom:9px;
  }
  .section-heading-main { min-width:0; }
  .section-kicker { font-size:7px; line-height:1; font-weight:900; letter-spacing:.1em; text-transform:uppercase; color:#94a3b8; }
  .section-title { margin-top:3px; font-size:11px; line-height:1.15; font-weight:950; color:#0f172a; }
  .section-meta { flex:0 0 auto; font-size:8px; font-weight:850; color:#64748b; }

  /* Ringkasan makro */
  .macro-dashboard-grid {
    display:grid;
    grid-template-columns:1fr;
    gap:8px;
  }
  .macro-ratio-section { grid-column:1 / -1; }
  .health-grid {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:6px;
  }
  .health-card {
    position:relative;
    min-width:0;
    min-height:70px;
    overflow:hidden;
    border:1px solid #e2e8f0;
    border-radius:10px;
    background:#f8fafc;
    padding:9px;
  }
  .health-card::after {
    content:'';
    position:absolute;
    right:-15px; bottom:-25px;
    width:55px; height:55px;
    border-radius:999px;
    background:var(--health-soft,#dbeafe);
    opacity:.55;
  }
  .health-label { position:relative; z-index:1; font-size:8px; font-weight:900; letter-spacing:.07em; text-transform:uppercase; color:#64748b; }
  .health-value {
    position:relative; z-index:1;
    margin-top:8px;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:clamp(16px,4.3vw,23px);
    line-height:1;
    font-weight:950;
    color:var(--health-color,#1d4ed8);
  }
  .health-note { position:relative; z-index:1; margin-top:6px; font-size:7px; font-weight:750; color:#94a3b8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .health-blue { --health-color:#0369a1; --health-soft:#bae6fd; }
  .health-green { --health-color:#047857; --health-soft:#a7f3d0; }
  .health-purple { --health-color:#6d28d9; --health-soft:#ddd6fe; }
  .health-red { --health-color:#be123c; --health-soft:#fecdd3; }
  .health-orange { --health-color:#c2410c; --health-soft:#fed7aa; }

  .nominal-group-grid { display:grid; grid-template-columns:1fr; gap:7px; }
  .nominal-group {
    min-width:0;
    border:1px solid #eef2f7;
    border-radius:10px;
    background:#f8fafc;
    padding:8px;
  }
  .nominal-group-title { margin-bottom:6px; font-size:8px; font-weight:950; letter-spacing:.07em; text-transform:uppercase; color:#64748b; }
  .nominal-list { display:grid; gap:5px; }
  .nominal-item {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:9px;
    min-width:0;
    border-radius:8px;
    background:#fff;
    padding:7px 8px;
    box-shadow:inset 0 0 0 1px #eef2f7;
  }
  .nominal-name { min-width:0; font-size:8px; font-weight:800; color:#475569; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .nominal-value {
    flex:0 0 auto;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:9px;
    line-height:1;
    font-weight:900;
    color:#0f172a;
    white-space:nowrap;
  }

  .expense-list { display:grid; gap:7px; }
  .expense-item { min-width:0; }
  .expense-row { display:flex; align-items:center; justify-content:space-between; gap:9px; }
  .expense-rank {
    display:inline-flex; align-items:center; justify-content:center;
    width:21px; height:21px; flex:0 0 21px;
    border-radius:7px;
    background:#fff1f2;
    color:#be123c;
    font-size:8px;
    font-weight:950;
  }
  .expense-name { min-width:0; flex:1; font-size:8px; font-weight:850; color:#475569; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .expense-value { flex:0 0 auto; font-family:'JetBrains Mono',ui-monospace,monospace; font-size:9px; font-weight:900; color:#be123c; white-space:nowrap; }
  .expense-track { height:4px; margin-top:5px; margin-left:30px; border-radius:999px; background:#f1f5f9; overflow:hidden; }
  .expense-bar { height:100%; border-radius:999px; background:linear-gradient(90deg,#fb7185,#e11d48); }

  /* Tren mingguan */
  .weekly-summary-strip {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:6px;
    margin-bottom:8px;
  }
  .closing-reference {
    min-width:0;
    border:1px solid #e2e8f0;
    border-radius:10px;
    background:#fff;
    padding:8px;
  }
  .closing-reference .label { font-size:7px; font-weight:900; letter-spacing:.07em; text-transform:uppercase; color:#94a3b8; }
  .closing-reference .value { margin-top:6px; font-family:'JetBrains Mono',ui-monospace,monospace; font-size:11px; font-weight:900; color:#334155; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .weekly-card-grid {
    display:grid;
    grid-template-columns:1fr;
    gap:8px;
  }
  .weekly-card {
    min-width:0;
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#fff;
    overflow:hidden;
    box-shadow:0 1px 2px rgba(15,23,42,.03);
  }
  .weekly-card.is-latest { border-color:#93c5fd; box-shadow:0 0 0 2px rgba(59,130,246,.08),0 7px 20px -16px rgba(37,99,235,.8); }
  .weekly-card-head {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    padding:9px 10px;
    border-bottom:1px solid #e2e8f0;
    background:linear-gradient(180deg,#f8fafc,#fff);
  }
  .weekly-card-title { font-size:11px; line-height:1.1; font-weight:950; color:#0f172a; }
  .weekly-card-date { margin-top:3px; font-size:7px; line-height:1.25; font-weight:750; color:#64748b; }
  .weekly-latest-badge {
    flex:0 0 auto;
    border:1px solid #bfdbfe;
    border-radius:999px;
    background:#eff6ff;
    padding:4px 7px;
    color:#1d4ed8;
    font-size:7px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.05em;
  }
  .weekly-metrics { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:0; }
  .weekly-metric {
    min-width:0;
    padding:9px 10px;
    border-right:1px solid #eef2f7;
    border-bottom:1px solid #eef2f7;
  }
  .weekly-metric:nth-child(even) { border-right:0; }
  .weekly-metric:nth-last-child(-n+2) { border-bottom:0; }
  .weekly-metric-label { font-size:7px; font-weight:900; letter-spacing:.07em; text-transform:uppercase; color:#94a3b8; }
  .weekly-metric-value {
    margin-top:6px;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:clamp(11px,3.4vw,15px);
    line-height:1;
    font-weight:950;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .weekly-metric-foot { display:flex; align-items:center; justify-content:space-between; gap:4px; margin-top:6px; min-height:17px; }
  .weekly-metric-prev { min-width:0; font-size:7px; font-weight:750; color:#94a3b8; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

  .empty-report {
    display:flex;
    min-height:220px;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:7px;
    border:1px dashed #cbd5e1;
    border-radius:12px;
    background:#f8fafc;
    color:#94a3b8;
    text-align:center;
    font-size:10px;
    font-weight:800;
  }

  @media (min-width:640px) {
    #lapkeuPage { padding:7px !important; gap:7px !important; }
    #rekapContainer { grid-template-columns:repeat(4,minmax(0,1fr)); gap:7px !important; }
    .overview-card { padding:11px 12px; }
    .overview-card-label { font-size:9px; }
    .overview-card-foot { font-size:9px; }
    .health-grid { grid-template-columns:repeat(4,minmax(0,1fr)); }
    .nominal-group-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .weekly-summary-strip { grid-template-columns:repeat(4,minmax(0,1fr)); }
    .weekly-card-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
  }

  @media (min-width:768px) {
    #lapkeuPage { height:calc(100dvh - 72px) !important; padding:8px !important; gap:8px !important; }
    #lapkeuHeader { padding:10px 12px !important; }
    #customReport { padding:10px !important; }
    .report-hero { padding:11px 12px; }
    .report-hero-title { font-size:15px; }
    .report-hero-sub { font-size:9px; }
    .dashboard-section { padding:12px; }
    .section-title { font-size:13px; }
    .section-kicker,.section-meta { font-size:8px; }
    .health-card { min-height:80px; padding:10px; }
    .health-label { font-size:9px; }
    .health-note { font-size:8px; }
    .nominal-group-grid { grid-template-columns:repeat(3,minmax(0,1fr)); }
    .nominal-name { font-size:9px; }
    .nominal-value { font-size:10px; }
    .expense-name { font-size:9px; }
    .expense-value { font-size:10px; }
    .weekly-card-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .weekly-card-title { font-size:13px; }
    .weekly-card-date { font-size:8px; }
    .weekly-metric-label { font-size:8px; }
    .weekly-metric-prev { font-size:8px; }
  }

  @media (min-width:1024px) {
    .macro-dashboard-grid { grid-template-columns:minmax(0,1.45fr) minmax(300px,.8fr); }
    .macro-ratio-section { grid-column:1 / -1; }
    .macro-nominal-section { grid-column:1; }
    .macro-expense-section { grid-column:2; }
    .weekly-card-grid { grid-template-columns:repeat(3,minmax(0,1fr)); }
  }

  @media (min-width:1280px) {
    #lapkeuPage { height:calc(100dvh - 80px) !important; padding:5px 7px !important; gap:6px !important; }
    #lapkeuHeader {
      display:grid !important;
      grid-template-columns:auto minmax(0,1fr);
      align-items:end !important;
      gap:14px !important;
      padding:8px 10px !important;
    }
    #filterForm {
      display:grid !important;
      grid-template-columns:160px minmax(190px,230px) 140px auto;
      justify-content:end;
      align-items:end;
      gap:7px !important;
      width:100% !important;
    }
    #filterForm > div { width:auto !important; min-width:0 !important; }
    #filterForm > div:last-child { justify-self:end; }
    .inp-modern { height:35px !important; font-size:11px !important; border-radius:8px; }
    #filterForm > div:last-child { height:35px !important; }
    #rekapContainer { gap:6px !important; }
    .overview-card { padding:9px 11px; }
    .overview-card-value { font-size:18px; }
    .report-hero { margin-bottom:7px; padding:8px 10px; }
    .report-hero-icon { width:31px; height:31px; flex-basis:31px; }
    .dashboard-section { padding:10px; }
    .health-grid { grid-template-columns:repeat(auto-fit,minmax(125px,1fr)); }
    .health-card { min-height:70px; padding:8px; }
    .health-value { font-size:18px; }
    .nominal-group-grid { grid-template-columns:repeat(3,minmax(0,1fr)); }
    .weekly-card-grid { grid-template-columns:repeat(4,minmax(0,1fr)); }
  }

  @media (max-width:767px) {
    #lapkeuHeader { display:block !important; }
    #lapkeuHeader > div:first-child { margin-bottom:7px !important; }
    #reportPageTitle { font-size:14px !important; }
    #reportPageSubtitle { max-width:230px; font-size:8px !important; }
    #filterForm {
      display:grid !important;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:6px !important;
      width:100% !important;
    }
    #filterForm > div { width:auto !important; min-width:0 !important; }
    #filterForm > div:nth-child(1) { grid-column:span 1; }
    #filterForm > div:nth-child(2) { grid-column:span 1; }
    #filterForm > div:nth-child(3) { grid-column:span 1; }
    #filterForm > div:nth-child(4) { grid-column:span 1; justify-content:flex-end; }
    .inp-modern { height:32px !important; padding:0 7px !important; font-size:10px !important; border-radius:7px; }
    #filterForm label { font-size:8px !important; margin-bottom:2px !important; }
    #filterForm > div:last-child { height:32px !important; }
    #btnExportLapkeu { height:32px; }
    .report-position-badge { display:none; }
  }

  @media (max-width:420px) {
    #lapkeuPage { padding:4px !important; gap:5px !important; }
    #lapkeuHeader { padding:7px !important; }
    #reportPageSubtitle { display:none; }
    #rekapContainer { gap:5px !important; }
    .overview-card { padding:9px; border-radius:9px; }
    .overview-card-icon { width:22px; height:22px; flex-basis:22px; border-radius:7px; }
    .overview-card-value { font-size:14px; }
    .trend-badge { padding:2px 4px; font-size:6px; }
    #customReport { padding:6px !important; }
    .report-hero { padding:8px; border-radius:10px; }
    .report-hero-icon { width:30px; height:30px; flex-basis:30px; }
    .report-hero-title { font-size:12px; }
    .report-hero-sub { font-size:7px; }
    .dashboard-section { padding:8px; border-radius:10px; }
    .health-card { min-height:64px; padding:8px; }
    .health-value { font-size:15px; }
    .weekly-metric { padding:8px; }
  }



  /* ========================================================
     FINANCIAL STATEMENT + WEEKLY CHART V3
     ======================================================== */
  .financial-toolbar {
    flex:0 0 auto;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    min-height:48px;
    padding:7px 9px;
    border-bottom:1px solid #e2e8f0;
    background:linear-gradient(180deg,#fff,#f8fafc);
  }
  .financial-toolbar-main { min-width:0; display:flex; align-items:center; gap:8px; }
  .financial-toolbar-icon {
    width:30px; height:30px; flex:0 0 30px;
    display:flex; align-items:center; justify-content:center;
    border-radius:9px; background:#eff6ff; color:#2563eb;
  }
  .financial-toolbar-icon svg { width:15px; height:15px; }
  .financial-toolbar-title { font-size:12px; line-height:1.15; font-weight:900; color:#0f172a; }
  .financial-toolbar-meta { margin-top:2px; font-size:8px; line-height:1.2; font-weight:750; color:#94a3b8; }
  .financial-toolbar-actions { display:flex; align-items:center; justify-content:flex-end; gap:5px; min-width:0; }
  .financial-search-wrap { position:relative; width:min(230px,30vw); }
  .financial-search-wrap svg {
    position:absolute; left:9px; top:50%; transform:translateY(-50%);
    width:13px; height:13px; color:#94a3b8; pointer-events:none;
  }
  .financial-search {
    width:100%; height:31px; padding:0 9px 0 29px;
    border:1px solid #dbe3ee; border-radius:8px;
    background:#fff; color:#334155; outline:none;
    font-size:10px; font-weight:700;
  }
  .financial-search:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
  .financial-tool-btn {
    height:31px; min-width:31px; padding:0 9px;
    display:inline-flex; align-items:center; justify-content:center; gap:5px;
    border:1px solid #dbe3ee; border-radius:8px;
    background:#fff; color:#475569; cursor:pointer;
    font-size:9px; line-height:1; font-weight:850;
    transition:.15s ease;
  }
  .financial-tool-btn:hover { border-color:#bfdbfe; background:#eff6ff; color:#1d4ed8; }
  .financial-tool-btn svg { width:13px; height:13px; }

  #tabelLapKeu {
    width:100% !important;
    min-width:0 !important;
    border-collapse:separate;
    border-spacing:0;
    table-layout:fixed;
    font-variant-numeric:tabular-nums;
  }
  #tabelLapKeu .lap-code-col { width:92px; }
  #tabelLapKeu .lap-value-col { width:150px; }
  #tabelLapKeu .lap-pct-col { width:76px; }
  #tabelLapKeu thead th {
    height:37px;
    padding:7px 10px !important;
    background:#f8fafc !important;
    border-bottom:1px solid #cbd5e1 !important;
    color:#64748b !important;
    font-size:9px !important;
    line-height:1 !important;
    font-weight:900 !important;
    letter-spacing:.065em;
    text-transform:uppercase;
  }
  #tabelLapKeu tbody tr { height:40px; }
  #tabelLapKeu tbody td {
    padding:7px 10px !important;
    border-bottom:1px solid #eef2f7;
    background:#fff;
    color:#334155;
    font-size:11px;
    line-height:1.2;
    vertical-align:middle;
  }
  #tabelLapKeu tbody tr:hover td { background:#f8fafc !important; }
  #tabelLapKeu .financial-code {
    font-family:'JetBrains Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
    color:#64748b; font-size:10px; font-weight:750;
  }
  #tabelLapKeu .financial-name-wrap { display:flex; align-items:center; min-width:0; }
  #tabelLapKeu .financial-name {
    min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
  }
  #tabelLapKeu .financial-mobile-code {
    display:none; flex:0 0 auto; margin-right:6px;
    border:1px solid #dbeafe; border-radius:5px;
    padding:2px 4px; background:#eff6ff; color:#1d4ed8;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:7px; line-height:1; font-weight:850;
  }
  #tabelLapKeu .financial-amount {
    display:block; width:100%; text-align:right;
    font-family:'JetBrains Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
    color:#0f172a; font-size:11px; font-weight:800;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  #tabelLapKeu .financial-amount.is-negative { color:#be123c; }
  #tabelLapKeu .financial-amount.is-positive { color:#047857; }
  #tabelLapKeu .financial-pct {
    display:inline-flex; justify-content:flex-end; width:100%;
    font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:10px; font-weight:900; white-space:nowrap;
  }
  #tabelLapKeu .financial-pct.is-negative { color:#be123c; }
  #tabelLapKeu .financial-pct.is-positive { color:#047857; }
  #tabelLapKeu .financial-pct.is-flat { color:#64748b; }
  #tabelLapKeu .financial-amount-short { display:none; }
  #tabelLapKeu .financial-parent-badge {
    flex:0 0 auto; margin-left:7px;
    border:1px solid #dbe3ee; border-radius:999px;
    padding:2px 5px; background:rgba(255,255,255,.7);
    color:#64748b; font-size:7px; line-height:1; font-weight:850;
  }
  #tabelLapKeu .caret {
    width:17px; height:17px; margin:0 5px 0 0;
    display:inline-flex; align-items:center; justify-content:center;
    border-radius:5px; color:#64748b; font-size:8px;
    transition:transform .18s ease,background .18s ease;
  }
  #tabelLapKeu tr[data-parent="1"] { cursor:pointer; }
  #tabelLapKeu tr[data-parent="1"]:hover .caret { background:#e2e8f0; color:#1d4ed8; }
  #tabelLapKeu .rotate { transform:rotate(90deg); }
  #tabelLapKeu .financial-level-1 td {
    background:#eaf2ff !important; color:#1e3a8a; font-weight:900;
    border-bottom-color:#bfdbfe;
  }
  #tabelLapKeu .financial-level-1 td:first-child { box-shadow:inset 4px 0 0 #2563eb; }
  #tabelLapKeu .financial-level-2 td {
    background:#f4f7fb !important; color:#1e293b; font-weight:850;
  }
  #tabelLapKeu .financial-level-2 td:first-child { box-shadow:inset 3px 0 0 #94a3b8; }
  #tabelLapKeu .financial-level-3 td { background:#fafcff !important; font-weight:750; }
  #tabelLapKeu .financial-level-detail:nth-child(even) td { background:#fcfdff; }
  #tabelLapKeu .financial-level-1 .financial-amount,
  #tabelLapKeu .financial-level-2 .financial-amount { color:inherit; font-weight:900; }
  #financialEmptyState { display:none; padding:42px 15px; text-align:center; color:#94a3b8; font-size:11px; font-weight:800; }

  /* Weekly line chart */
  .weekly-chart-shell {
    overflow:hidden;
    border:1px solid #e2e8f0;
    border-radius:14px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.04);
  }
  .weekly-chart-toolbar {
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:10px 11px; border-bottom:1px solid #e2e8f0;
    background:linear-gradient(180deg,#fff,#f8fafc);
  }
  .weekly-chart-title { font-size:12px; font-weight:900; color:#0f172a; }
  .weekly-chart-sub { margin-top:2px; font-size:8px; font-weight:750; color:#94a3b8; }
  .weekly-metric-tabs {
    display:flex; align-items:center; flex-wrap:wrap; gap:4px; padding:3px;
    border:1px solid #e2e8f0; border-radius:9px; background:#f8fafc;
  }
  .weekly-chart-controls {
    display:flex; align-items:center; justify-content:flex-end; gap:6px; flex-wrap:wrap;
  }
  .weekly-metric-tab {
    height:28px; padding:0 9px; border:0; border-radius:7px;
    background:transparent; color:#64748b; cursor:pointer;
    font-size:9px; font-weight:850; transition:.15s ease;
  }
  .weekly-metric-tab:hover { color:#1d4ed8; }
  .weekly-metric-tab.active { background:#fff; color:var(--chart-accent,#2563eb); box-shadow:0 1px 3px rgba(15,23,42,.1); }
  .weekly-mode-toggle {
    height:36px; display:inline-flex; align-items:center; gap:7px; padding:0 10px;
    border:1px solid #dbeafe; border-radius:10px; background:#fff; color:#1e3a8a;
    font-size:9px; font-weight:950; cursor:pointer; box-shadow:0 1px 3px rgba(15,23,42,.06);
  }
  .weekly-mode-toggle input {
    width:15px; height:15px; accent-color:var(--chart-accent,#2563eb); cursor:pointer;
  }
  .weekly-mode-toggle span { white-space:nowrap; }
  .weekly-chart-stat-grid {
    display:grid; grid-template-columns:repeat(4,minmax(0,1fr));
    gap:6px; padding:8px 10px 0;
  }
  .weekly-chart-stat {
    min-width:0; border:1px solid #edf2f7; border-radius:9px;
    background:#f8fafc; padding:7px 8px;
  }
  .weekly-chart-stat .label { font-size:7px; line-height:1; font-weight:900; letter-spacing:.06em; text-transform:uppercase; color:#94a3b8; }
  .weekly-chart-stat .value {
    margin-top:5px; font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:12px; line-height:1; font-weight:900; color:#0f172a;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  .weekly-chart-canvas { position:relative; width:100%; min-height:310px; padding:4px 6px 0; }
  .weekly-chart-canvas svg { display:block; width:100%; height:auto; min-height:290px; }
  .weekly-chart-grid { stroke:#e2e8f0; stroke-width:1; stroke-dasharray:4 5; }
  .weekly-chart-axis-label { fill:#64748b; font-size:11px; font-weight:750; }
  .weekly-chart-x-label { fill:#64748b; font-size:10px; font-weight:800; }
  .weekly-chart-line { fill:none; stroke:var(--chart-accent,#2563eb); stroke-width:4; stroke-linejoin:round; stroke-linecap:round; }
  .weekly-chart-area { fill:url(#weeklyAreaGradient); opacity:.5; }
  .weekly-chart-dot { fill:#fff; stroke:var(--chart-accent,#2563eb); stroke-width:4; }
  .weekly-chart-dot.latest { fill:var(--chart-accent,#2563eb); stroke:#fff; stroke-width:4; }
  .weekly-chart-latest-label {
    fill:#fff; font-size:11px; line-height:1; font-weight:900;
  }
  .weekly-chart-closing-line { stroke:#94a3b8; stroke-width:1.5; stroke-dasharray:6 5; }
  .weekly-chart-closing-label { fill:#64748b; font-size:9px; font-weight:850; }
  .weekly-chart-footer {
    display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:6px;
    padding:0 10px 10px;
  }
  .weekly-latest-card {
    min-width:0; border:1px solid #e2e8f0; border-radius:9px;
    background:#fff; padding:8px;
  }
  .weekly-latest-card .name { font-size:8px; font-weight:900; letter-spacing:.055em; text-transform:uppercase; color:#64748b; }
  .weekly-latest-card .value {
    margin-top:5px; font-family:'JetBrains Mono',ui-monospace,monospace;
    font-size:12px; line-height:1; font-weight:900;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  .weekly-latest-card .change { margin-top:6px; display:flex; align-items:center; justify-content:space-between; gap:5px; font-size:7px; font-weight:800; color:#94a3b8; }
  .weekly-checklist {
    margin:0 10px 10px; padding:9px; border:1px solid #e2e8f0; border-radius:10px;
    background:#f8fafc;
  }
  .weekly-checklist-head {
    display:flex; align-items:flex-end; justify-content:space-between; gap:8px;
    padding-bottom:7px; border-bottom:1px solid #e2e8f0;
  }
  .weekly-checklist-title { font-size:10px; font-weight:950; color:#0f172a; }
  .weekly-checklist-sub { margin-top:2px; font-size:8px; font-weight:800; color:#64748b; }
  .weekly-checklist-grid {
    display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:7px; margin-top:8px;
  }
  .weekly-check-card {
    min-width:0; border:1px solid #e5edf7; border-radius:9px; background:#fff; padding:8px;
  }
  .weekly-check-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:7px; }
  .weekly-check-title { font-size:10px; font-weight:950; color:#0f172a; }
  .weekly-check-date { margin-top:1px; font-size:7px; font-weight:800; color:#94a3b8; }
  .weekly-check-lines { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:5px 8px; }
  .weekly-check-line {
    min-width:0; display:grid; grid-template-columns:minmax(0,1fr) auto; gap:5px;
    align-items:baseline; border-top:1px dashed #e2e8f0; padding-top:5px;
  }
  .weekly-check-line .label { min-width:0; font-size:7px; font-weight:900; color:#64748b; text-transform:uppercase; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .weekly-check-line .amount { font-size:8px; font-weight:950; color:#0f172a; white-space:nowrap; }
  .weekly-check-line .delta { grid-column:1 / -1; text-align:right; font-size:7px; font-weight:950; white-space:nowrap; }
  .weekly-check-line .delta.up { color:#059669; }
  .weekly-check-line .delta.down { color:#dc2626; }
  .weekly-check-line .delta.flat { color:#64748b; }

  @media (min-width:1280px) {
    .financial-toolbar { min-height:46px; padding:6px 9px; }
    #tabelLapKeu tbody tr { height:38px; }
    #tabelLapKeu tbody td { padding:6px 10px !important; font-size:11px; }
    .weekly-chart-canvas { min-height:330px; }
  }
  @media (max-width:900px) {
    .weekly-chart-stat-grid, .weekly-chart-footer { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .weekly-chart-canvas { min-height:290px; overflow:hidden; }
    .weekly-chart-canvas svg { min-width:0; }
  }
  @media (max-width:767px) {
    .financial-toolbar { min-height:auto; align-items:flex-start; padding:6px; gap:6px; }
    .financial-toolbar-icon { width:27px; height:27px; flex-basis:27px; }
    .financial-toolbar-title { font-size:10px; }
    .financial-toolbar-meta { font-size:7px; }
    .financial-toolbar-actions { gap:4px; }
    .financial-search-wrap { width:min(150px,43vw); }
    .financial-search { height:29px; font-size:9px; }
    .financial-tool-btn { width:29px; min-width:29px; height:29px; padding:0; }
    .financial-tool-btn span { display:none; }
    #tabelLapKeu .lap-code-col { display:none; }
    #tabelLapKeu .lap-value-col { width:96px; }
    #tabelLapKeu .lap-pct-col { width:56px; }
    #tabelLapKeu thead th { height:32px; padding:6px !important; font-size:8px !important; }
    #tabelLapKeu tbody tr { height:36px; }
    #tabelLapKeu tbody td { padding:6px !important; font-size:9px; }
    #tabelLapKeu .financial-mobile-code { display:inline-flex; }
    #tabelLapKeu .financial-name { font-size:9px; }
    #tabelLapKeu .financial-amount-full { display:none; }
    #tabelLapKeu .financial-amount-short { display:block; font-size:9px; }
    #tabelLapKeu .financial-pct { font-size:8px; }
    #tabelLapKeu .financial-parent-badge { display:none; }
    #tabelLapKeu .caret { width:15px; height:15px; margin-right:3px; }
    .weekly-chart-toolbar { align-items:flex-start; flex-direction:column; padding:8px; }
    .weekly-chart-controls { width:100%; justify-content:space-between; }
    .weekly-metric-tabs { width:100%; display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); }
    .weekly-metric-tab { width:100%; padding:0 3px; font-size:8px; }
    .weekly-mode-toggle { height:30px; font-size:8px; padding:0 8px; }
    .weekly-chart-stat-grid { padding:6px 7px 0; gap:5px; }
    .weekly-chart-stat { padding:6px; }
    .weekly-chart-stat .value { font-size:10px; }
    .weekly-chart-canvas { min-height:255px; padding:0; }
    .weekly-chart-canvas svg { min-width:0; min-height:250px; }
    .weekly-chart-footer { padding:0 7px 7px; gap:5px; }
    .weekly-latest-card { padding:7px; }
    .weekly-latest-card .value { font-size:10px; }
    .weekly-checklist { margin:0 7px 7px; padding:7px; }
    .weekly-checklist-grid { grid-template-columns:1fr; }
    .weekly-check-lines { grid-template-columns:1fr 1fr; }
    .lapkeu-insight-grid { grid-template-columns:1fr; }
    .lapkeu-insight-title { font-size:16px; }
  }
  @media (max-width:420px) {
    .financial-search-wrap { width:125px; }
    #tabelLapKeu .lap-value-col { width:82px; }
    #tabelLapKeu .lap-pct-col { width:50px; }
    .weekly-chart-stat-grid, .weekly-chart-footer { grid-template-columns:1fr 1fr; }
    .weekly-check-lines { grid-template-columns:1fr; }
  }



  /* ========================================================
     REPORT WORKSPACE V4
     Setiap opsi dropdown memiliki view terpisah.
     ======================================================== */
  :root {
    --view-border:#e2e8f0;
    --view-bg:#f8fafc;
    --view-text:#0f172a;
    --view-muted:#64748b;
  }

  #lapkeuPage {
    display:flex !important;
    flex-direction:column;
    overflow:hidden;
  }

  #lapkeuHeader {
    flex:0 0 auto;
    align-items:center !important;
  }

  #reportStage {
    position:relative;
    flex:1 1 auto;
    min-height:0;
    width:100%;
    overflow:hidden;
  }

  .report-view {
    width:100%;
    height:100%;
    min-width:0;
    min-height:0;
  }

  .report-view.is-entering {
    animation:reportViewIn .18s ease-out both;
  }

  @keyframes reportViewIn {
    from { opacity:0; transform:translateY(4px); }
    to { opacity:1; transform:translateY(0); }
  }

  #viewFinancial {
    display:flex;
    flex-direction:column;
    gap:7px;
  }

  #viewFinancial.hidden,
  #viewMacro.hidden,
  #viewTrend.hidden { display:none !important; }

  #rekapContainer {
    flex:0 0 auto;
    grid-template-columns:repeat(3,minmax(0,1fr)) !important;
    gap:7px !important;
  }

  #financialPanel {
    flex:1 1 auto;
    min-height:0;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    border:1px solid var(--view-border);
    border-radius:13px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.04),0 10px 30px rgba(15,23,42,.03);
  }

  #financialPanel .financial-toolbar {
    flex:0 0 auto;
    border-radius:0 !important;
    border:0 !important;
    border-bottom:1px solid var(--view-border) !important;
    background:linear-gradient(180deg,#fff,#fbfdff) !important;
  }

  #financialTableScroll {
    flex:1 1 auto;
    min-height:0;
    overflow:auto;
    background:#fff;
  }

  .custom-view-shell {
    height:100%;
    min-height:0;
    overflow:auto;
    padding:0;
    background:transparent;
  }

  .custom-view-inner {
    min-height:100%;
    padding:1px;
  }

  #macroReport,
  #trendReport { min-height:100%; }

  .view-mode-chip {
    display:inline-flex;
    align-items:center;
    gap:5px;
    width:max-content;
    margin-top:4px;
    padding:3px 7px;
    border:1px solid #dbeafe;
    border-radius:999px;
    background:#eff6ff;
    color:#1d4ed8;
    font-size:8px;
    line-height:1;
    font-weight:900;
    letter-spacing:.05em;
    text-transform:uppercase;
  }
  .view-mode-chip::before {
    content:'';
    width:5px;
    height:5px;
    border-radius:999px;
    background:#3b82f6;
  }

  /* Header/filter dibuat seperti control bar modern */
  #filterForm {
    padding:3px;
    border:1px solid #e2e8f0;
    border-radius:11px;
    background:#f8fafc;
  }
  #filterForm > div { min-width:0; }
  #filterForm label { color:#64748b !important; }
  #filterForm .inp-modern {
    border-color:transparent;
    background:#fff;
    box-shadow:0 1px 1px rgba(15,23,42,.03);
  }
  #filterForm .inp-modern:focus {
    border-color:#60a5fa;
    box-shadow:0 0 0 3px rgba(59,130,246,.10);
  }
  #type_report {
    color:#1e40af;
    font-weight:850;
  }

  /* Ringkasan hanya khusus Neraca / Laba Rugi */
  #viewFinancial .overview-card,
  #viewFinancial .rekap-card {
    min-height:72px;
    padding:9px 11px;
    border-radius:11px;
  }
  #viewFinancial .overview-card-value,
  #viewFinancial .rekap-card > p:nth-child(2) {
    font-size:clamp(15px,1.7vw,22px) !important;
  }

  /* Dashboard custom tidak membawa summary eksternal */
  .custom-top-summary {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:7px;
    margin-bottom:8px;
  }
  .custom-top-summary .overview-card { min-height:76px; }

  .report-dashboard {
    padding:0;
  }
  .report-hero {
    margin-bottom:7px !important;
    border-radius:12px !important;
    box-shadow:0 1px 2px rgba(15,23,42,.025);
  }
  .dashboard-section,
  .weekly-chart-shell {
    border-radius:12px !important;
    box-shadow:0 1px 2px rgba(15,23,42,.03) !important;
  }

  /* Prevent komponen lama tampil silang */
  body[data-lap-view="macro"] #financialTableToolbar,
  body[data-lap-view="macro"] #tabelLapKeu,
  body[data-lap-view="trend"] #financialTableToolbar,
  body[data-lap-view="trend"] #tabelLapKeu { display:none !important; }

  @media (min-width:1280px) {
    #lapkeuHeader {
      padding:8px 10px !important;
      gap:12px !important;
    }
    #filterForm { gap:4px !important; }
    #filterForm > div:nth-child(1) { width:178px !important; }
    #filterForm > div:nth-child(2) { width:220px !important; }
    #filterForm > div:nth-child(3) { width:142px !important; }
    .custom-view-shell { padding:0; }
    .custom-view-inner { padding:0; }
  }

  @media (min-width:768px) and (max-width:1279px) {
    #lapkeuHeader { align-items:flex-start !important; }
    #filterForm {
      width:100%;
      display:grid !important;
      grid-template-columns:1.15fr 1.5fr 1fr auto;
      gap:6px !important;
    }
    #filterForm > div { width:auto !important; }
    #rekapContainer { grid-template-columns:repeat(3,minmax(0,1fr)) !important; }
    .custom-top-summary { grid-template-columns:repeat(4,minmax(0,1fr)); }
  }

  @media (max-width:767px) {
    #lapkeuHeader {
      padding:7px !important;
      align-items:stretch !important;
    }
    #lapkeuHeader > div:first-child { margin-bottom:0 !important; }
    #reportPageSubtitle { display:none !important; }
    .view-mode-chip { margin-top:3px; }
    #filterForm {
      display:grid !important;
      grid-template-columns:repeat(2,minmax(0,1fr));
      width:100%;
      gap:5px !important;
      padding:5px;
    }
    #filterForm > div { width:auto !important; }
    #filterForm > div:nth-child(2) { grid-column:1 / -1; }
    #filterForm > div:last-child {
      align-self:end;
      justify-content:flex-end;
    }
    #filterForm .inp-modern { height:32px; font-size:10px; padding:0 7px; }
    #filterForm label { font-size:7px !important; margin-bottom:2px !important; }

    #viewFinancial { gap:5px; }
    #rekapContainer {
      display:flex !important;
      grid-template-columns:none !important;
      gap:5px !important;
      overflow-x:auto;
      overflow-y:hidden;
      padding-bottom:1px;
      scroll-snap-type:x proximity;
    }
    #rekapContainer > * {
      width:155px;
      min-width:155px;
      scroll-snap-align:start;
    }
    #financialPanel { border-radius:10px; }
    #financialPanel .financial-toolbar { padding:6px !important; }
    .financial-toolbar-meta { display:none; }
    .financial-search-wrap { width:140px; }
    .financial-tool-btn span { display:none; }
    .financial-tool-btn { width:31px; padding:0 !important; }

    .custom-view-shell { padding:0; }
    .custom-view-inner { padding:0; }
    .custom-top-summary {
      display:flex;
      grid-template-columns:none;
      gap:5px;
      overflow-x:auto;
      padding-bottom:2px;
      margin-bottom:6px;
    }
    .custom-top-summary > * { width:155px; min-width:155px; }
    .report-hero { padding:8px !important; }
    .report-hero-sub { display:none; }
  }

  @media (max-width:380px) {
    #filterForm { grid-template-columns:1fr 1fr; }
    #filterForm > div:nth-child(1),
    #filterForm > div:nth-child(2) { grid-column:1 / -1; }
    #rekapContainer > *, .custom-top-summary > * { width:145px; min-width:145px; }
  }



  /* ========================================================
     HEADER & FILTER WORKSPACE V5
     Satu judul aplikasi, judul laporan hanya di workspace aktif.
     ======================================================== */
  #lapkeuHeader {
    display:grid !important;
    grid-template-columns:minmax(245px,.72fr) minmax(0,1.55fr);
    align-items:center !important;
    gap:12px !important;
    padding:9px 11px !important;
    overflow:visible;
  }

  .lapkeu-brand {
    display:flex;
    align-items:center;
    gap:10px;
    min-width:0;
  }

  .lapkeu-brand-icon {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    flex:0 0 39px;
    width:39px;
    height:39px;
    border-radius:11px;
    color:#fff;
    background:linear-gradient(145deg,#2563eb,#1d4ed8);
    box-shadow:0 7px 16px -10px rgba(37,99,235,.8);
  }
  .lapkeu-brand-icon svg { width:19px; height:19px; }

  .lapkeu-brand-copy { min-width:0; }
  #reportPageTitle {
    display:block;
    font-size:16px !important;
    line-height:1.05 !important;
    font-weight:950 !important;
    letter-spacing:-.025em !important;
    color:#0f172a !important;
    white-space:nowrap;
  }
  #reportPageSubtitle {
    display:block;
    max-width:430px;
    margin-top:3px !important;
    font-size:9px !important;
    line-height:1.25;
    color:#64748b !important;
    font-weight:700 !important;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  .lapkeu-meta-row {
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:5px;
    margin-top:5px;
  }
  .view-mode-chip,
  #badgeUnit {
    margin:0 !important;
    height:20px;
    padding:0 7px !important;
    border-radius:6px !important;
    font-size:8px !important;
    line-height:20px !important;
    letter-spacing:.045em !important;
    white-space:nowrap;
  }
  .view-mode-chip {
    border:1px solid #bfdbfe;
    background:#eff6ff;
    color:#1d4ed8;
  }
  .view-mode-chip::before { width:4px; height:4px; }
  #badgeUnit {
    display:inline-flex;
    align-items:center;
    border:1px solid #e2e8f0;
    background:#f8fafc !important;
    color:#475569 !important;
  }

  #filterForm {
    display:grid !important;
    grid-template-columns:minmax(155px,1.05fr) minmax(190px,1.35fr) minmax(132px,.86fr) 40px;
    align-items:end;
    gap:6px !important;
    width:100% !important;
    min-width:0;
    padding:6px !important;
    border:1px solid #e2e8f0 !important;
    border-radius:12px !important;
    background:#f8fafc !important;
  }
  #filterForm > div {
    width:auto !important;
    min-width:0 !important;
    max-width:none !important;
  }
  #filterForm label {
    margin:0 0 3px 1px !important;
    font-size:8px !important;
    line-height:1 !important;
    color:#64748b !important;
    font-weight:850 !important;
    letter-spacing:.055em !important;
  }
  #filterForm .inp-modern {
    height:34px !important;
    min-width:0;
    padding:0 9px !important;
    border:1px solid #dbe3ee !important;
    border-radius:8px !important;
    background:#fff !important;
    color:#334155 !important;
    font-size:10.5px !important;
    box-shadow:0 1px 1px rgba(15,23,42,.025) !important;
  }
  #filterForm .inp-modern:focus {
    border-color:#60a5fa !important;
    box-shadow:0 0 0 3px rgba(59,130,246,.10) !important;
  }
  #type_report {
    color:#1e40af !important;
    font-weight:900 !important;
  }
  #autoApplyNote { display:none !important; }
  .lapkeu-filter-actions {
    display:flex !important;
    align-items:flex-end !important;
    justify-content:flex-end !important;
    height:auto !important;
  }
  #btnExportLapkeu {
    width:34px !important;
    height:34px !important;
    border-radius:8px !important;
    box-shadow:none !important;
  }

  /* Hero dalam workspace menjadi section header, bukan judul halaman kedua. */
  .report-hero {
    min-height:0 !important;
    padding:9px 11px !important;
    margin-bottom:7px !important;
    border:1px solid #e2e8f0 !important;
    background:linear-gradient(180deg,#fff,#fbfdff) !important;
  }
  .report-hero-icon {
    width:32px !important;
    height:32px !important;
    flex-basis:32px !important;
    border-radius:9px !important;
  }
  .report-hero-title {
    font-size:13px !important;
    letter-spacing:-.015em !important;
  }
  .report-hero-sub { font-size:8.5px !important; }

  @media (max-width:1279px) {
    #lapkeuHeader {
      grid-template-columns:1fr;
      align-items:stretch !important;
      gap:8px !important;
    }
    .lapkeu-brand { width:100%; }
    #filterForm {
      grid-template-columns:minmax(145px,1fr) minmax(190px,1.35fr) minmax(130px,.85fr) 40px;
    }
  }

  @media (max-width:767px) {
    #lapkeuHeader {
      padding:7px !important;
      gap:7px !important;
      border-radius:10px !important;
    }
    .lapkeu-brand { gap:8px; }
    .lapkeu-brand-icon {
      flex-basis:34px;
      width:34px;
      height:34px;
      border-radius:9px;
    }
    .lapkeu-brand-icon svg { width:17px; height:17px; }
    #reportPageTitle { font-size:14px !important; }
    #reportPageSubtitle { display:block !important; max-width:270px; font-size:7.5px !important; }
    .lapkeu-meta-row { margin-top:4px; gap:4px; }
    .view-mode-chip, #badgeUnit {
      height:18px;
      padding:0 6px !important;
      font-size:7px !important;
      line-height:18px !important;
    }

    #filterForm {
      grid-template-columns:repeat(12,minmax(0,1fr));
      gap:5px !important;
      padding:5px !important;
      border-radius:9px !important;
    }
    #filterForm > div:nth-child(1) { grid-column:span 6; }
    #filterForm > div:nth-child(2) { grid-column:span 6; }
    #filterForm > div:nth-child(3) { grid-column:span 9; }
    #filterForm > div:nth-child(4) { grid-column:span 3; }
    #filterForm .inp-modern { height:31px !important; padding:0 7px !important; font-size:9px !important; }
    #filterForm label { font-size:6.8px !important; margin-bottom:2px !important; }
    #btnExportLapkeu { width:31px !important; height:31px !important; }

    .report-hero {
      padding:7px 8px !important;
      margin-bottom:5px !important;
      border-radius:9px !important;
    }
    .report-hero-icon { width:28px !important; height:28px !important; flex-basis:28px !important; }
    .report-hero-title { font-size:11px !important; }
    .report-hero-sub { display:none !important; }
  }

  @media (max-width:460px) {
    #reportPageSubtitle { max-width:220px; }
    #filterForm > div:nth-child(1),
    #filterForm > div:nth-child(2) { grid-column:1 / -1; }
    #filterForm > div:nth-child(3) { grid-column:span 9; }
    #filterForm > div:nth-child(4) { grid-column:span 3; }
  }



  /* ========================================================
     HEADER FINAL V6
     - Tidak ada judul ganda di workspace Makro/Tren.
     - Filter menempel ke kanan pada desktop.
     ======================================================== */
  @media (min-width:1280px) {
    #lapkeuHeader {
      grid-template-columns:minmax(280px,1fr) auto !important;
      align-items:center !important;
      column-gap:18px !important;
    }

    #filterForm {
      width:max-content !important;
      min-width:0 !important;
      max-width:none !important;
      justify-self:end !important;
      grid-template-columns:178px 220px 142px 34px !important;
      padding:5px !important;
      gap:5px !important;
    }

    #filterForm > div:nth-child(1),
    #filterForm > div:nth-child(2),
    #filterForm > div:nth-child(3),
    #filterForm > div:nth-child(4) {
      width:auto !important;
      min-width:0 !important;
      max-width:none !important;
    }
  }

  @media (max-width: 767px) {
    #lapkeuHeader {
      gap:8px !important;
      padding:8px !important;
    }
    .lapkeu-mobile-filter-toggle {
      display:inline-flex;
      margin-left:auto;
      flex:0 0 auto;
    }
    #reportPageSubtitle { font-size:10px; }
    .lapkeu-meta-row { margin-top:3px; }

    /* Mobile: filter selalu ringkas menjadi tepat 2 baris. */
    #filterForm {
      display:none !important;
      width:100% !important;
      grid-template-columns:repeat(12,minmax(0,1fr)) !important;
      align-items:end !important;
      gap:6px !important;
      padding:7px !important;
      margin-top:2px !important;
      border:1px solid #e2e8f0 !important;
      border-radius:10px !important;
      background:#f8fafc !important;
    }
    #filterForm.lapkeu-filter-open { display:grid !important; }

    .lapkeu-filter-type {
      grid-column:span 6;
      min-width:0;
    }
    .lapkeu-filter-office {
      grid-column:span 6;
      min-width:0;
    }
    .lapkeu-filter-date {
      grid-column:span 10;
      min-width:0;
    }
    .lapkeu-filter-actions {
      grid-column:span 2;
      display:flex !important;
      align-items:flex-end !important;
      justify-content:flex-end !important;
      min-width:0;
      gap:0 !important;
    }
    .lapkeu-filter-actions #autoApplyNote { display:none !important; }

    #filterForm label {
      margin:0 0 2px 1px !important;
      font-size:7px !important;
      letter-spacing:.035em !important;
    }
    #filterForm .inp-modern {
      height:32px !important;
      padding-left:7px !important;
      padding-right:7px !important;
      border-radius:7px !important;
      font-size:9px !important;
    }
    #filterForm select.inp-modern {
      padding-right:25px !important;
      background-position:right 7px center !important;
      background-size:12px 12px !important;
    }
    #btnExportLapkeu {
      width:32px !important;
      height:32px !important;
      min-width:32px !important;
      border-radius:7px !important;
    }
  }

  @media (min-width:768px) and (max-width:1279px) {
    #lapkeuHeader {
      grid-template-columns:1fr !important;
      align-items:stretch !important;
    }

    #filterForm {
      width:100% !important;
      justify-self:stretch !important;
    }
  }

  /* Setelah hero ganda dihapus, workspace langsung dimulai dari isi utama. */
  #macroReport .report-dashboard,
  #trendReport .report-dashboard {
    padding-top:0 !important;
  }

  #macroReport .custom-top-summary,
  #trendReport .weekly-chart-shell {
    margin-top:0 !important;
  }



  /* ========================================================
     STANDARD HEADER V9 — mengikuti pola Flow PAR
     Desktop satu baris, mobile judul + tombol filter.
     ======================================================== */
  #lapkeuHeader {
    display:flex !important;
    flex-direction:column !important;
    align-items:stretch !important;
    justify-content:space-between !important;
    gap:8px !important;
    padding:8px 10px !important;
    border-radius:11px !important;
    overflow:visible !important;
  }

  .lapkeu-brand {
    display:flex !important;
    align-items:center !important;
    width:100%;
    min-width:0;
    gap:9px !important;
  }
  .lapkeu-brand-icon {
    width:36px !important;
    height:36px !important;
    flex:0 0 36px !important;
    border-radius:9px !important;
    background:#2563eb !important;
    box-shadow:0 2px 5px rgba(37,99,235,.18) !important;
  }
  .lapkeu-brand-icon svg { width:18px !important; height:18px !important; }
  .lapkeu-brand-copy { min-width:0; flex:1; }
  #reportPageTitle {
    display:block;
    font-size:16px !important;
    line-height:1.1 !important;
    font-weight:850 !important;
    letter-spacing:-.02em !important;
    color:#172033 !important;
    text-transform:none !important;
  }
  #reportPageSubtitle {
    display:block !important;
    max-width:none !important;
    margin-top:4px !important;
    font-size:9px !important;
    line-height:1.15 !important;
    font-weight:600 !important;
    color:#64748b !important;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .lapkeu-meta-row { display:none !important; }

  .lapkeu-mobile-filter-toggle {
    display:none;
    height:30px !important;
    padding:0 10px !important;
    margin-left:auto !important;
    border:1px solid #dbe3ee !important;
    border-radius:8px !important;
    background:#fff !important;
    color:#475569 !important;
    font-size:9px !important;
    font-weight:800 !important;
    gap:5px !important;
    box-shadow:0 1px 2px rgba(15,23,42,.04) !important;
  }
  .lapkeu-mobile-filter-toggle svg { width:13px !important; height:13px !important; }
  .lapkeu-mobile-filter-toggle.is-open {
    color:#2563eb !important;
    border-color:#bfdbfe !important;
    background:#eff6ff !important;
  }

  #filterForm {
    display:grid !important;
    grid-template-columns:155px 220px 140px 36px !important;
    align-items:end !important;
    justify-content:end !important;
    width:max-content !important;
    max-width:100% !important;
    min-width:0 !important;
    margin-left:auto !important;
    padding:0 !important;
    gap:7px !important;
    border:0 !important;
    border-radius:0 !important;
    background:transparent !important;
  }
  #filterForm label {
    margin:0 0 3px 1px !important;
    font-size:8px !important;
    line-height:1 !important;
    font-weight:850 !important;
    letter-spacing:.055em !important;
    color:#475569 !important;
  }
  #filterForm .inp-modern {
    width:100% !important;
    height:36px !important;
    padding:0 10px !important;
    border:1px solid #cbd5e1 !important;
    border-radius:8px !important;
    background:#fff !important;
    color:#334155 !important;
    font-size:11px !important;
    font-weight:700 !important;
    box-shadow:none !important;
  }
  #filterForm select.inp-modern {
    padding-right:29px !important;
    background-position:right 9px center !important;
    background-size:13px 13px !important;
  }
  #filterForm .inp-modern:focus {
    border-color:#2563eb !important;
    box-shadow:0 0 0 3px rgba(37,99,235,.1) !important;
  }
  #type_report { color:#334155 !important; }
  .lapkeu-filter-actions {
    display:flex !important;
    align-items:flex-end !important;
    justify-content:flex-end !important;
    height:36px !important;
  }
  #btnExportLapkeu {
    width:36px !important;
    height:36px !important;
    min-width:36px !important;
    border-radius:8px !important;
  }

  @media (min-width:1280px) {
    #lapkeuHeader {
      flex-direction:row !important;
      align-items:center !important;
      padding:10px 12px !important;
      gap:16px !important;
    }
    .lapkeu-brand { width:auto !important; flex:0 0 auto; }
    #filterForm { flex:0 0 auto; }
  }

  @media (min-width:768px) and (max-width:1279px) {
    #lapkeuHeader { padding:9px 10px !important; }
    #filterForm {
      width:100% !important;
      grid-template-columns:minmax(145px,.9fr) minmax(180px,1.25fr) minmax(130px,.8fr) 36px !important;
      margin-left:0 !important;
    }
  }

  @media (max-width:767px) {
    #lapkeuHeader {
      padding:7px !important;
      gap:6px !important;
      border-radius:9px !important;
    }
    .lapkeu-brand { gap:7px !important; }
    .lapkeu-brand-icon {
      width:28px !important;
      height:28px !important;
      flex-basis:28px !important;
      border-radius:7px !important;
    }
    .lapkeu-brand-icon svg { width:14px !important; height:14px !important; }
    #reportPageTitle { font-size:12px !important; }
    #reportPageSubtitle {
      margin-top:2px !important;
      max-width:205px !important;
      font-size:7px !important;
    }
    .lapkeu-mobile-filter-toggle { display:inline-flex !important; }

    #filterForm {
      display:none !important;
      grid-template-columns:repeat(12,minmax(0,1fr)) !important;
      width:100% !important;
      margin:2px 0 0 !important;
      padding:7px 0 0 !important;
      gap:5px !important;
      border-top:1px solid #e2e8f0 !important;
    }
    #filterForm.lapkeu-filter-open { display:grid !important; }
    .lapkeu-filter-type { grid-column:span 6 !important; }
    .lapkeu-filter-office { grid-column:span 6 !important; }
    .lapkeu-filter-closing { grid-column:span 5 !important; }
    .lapkeu-filter-date { grid-column:span 5 !important; }
    .lapkeu-filter-actions { grid-column:span 2 !important; height:31px !important; }
    #filterForm label {
      margin-bottom:2px !important;
      font-size:6.5px !important;
    }
    #filterForm .inp-modern {
      height:31px !important;
      padding:0 7px !important;
      border-radius:7px !important;
      font-size:8.5px !important;
    }
    #filterForm select.inp-modern {
      padding-right:23px !important;
      background-position:right 6px center !important;
      background-size:11px 11px !important;
    }
    #btnExportLapkeu {
      width:31px !important;
      height:31px !important;
      min-width:31px !important;
      border-radius:7px !important;
    }
  }

  /* ========================================================
     FINAL RESPONSIVE OVERRIDE V10
     - Desktop: header + filter satu baris dan seluruh kontrol muat.
     - Tablet: filter satu baris penuh di bawah judul.
     - Mobile: filter buka/tutup, tepat dua baris.
     - Workspace tidak saling menimpa dan tabel memiliki scroll aman.
     ======================================================== */
  *, *::before, *::after { box-sizing:border-box; }
  html, body { width:100%; min-width:0; margin:0; }
  body { overflow:hidden !important; }

  #lapkeuPage {
    width:100% !important;
    max-width:none !important;
    height:calc(100dvh - 60px) !important;
    min-height:0 !important;
    padding:6px !important;
    gap:6px !important;
    overflow:hidden !important;
  }

  #lapkeuHeader {
    width:100% !important;
    min-width:0 !important;
    flex:0 0 auto !important;
    display:grid !important;
    grid-template-columns:minmax(255px,1fr) auto !important;
    align-items:center !important;
    gap:12px !important;
    margin:0 !important;
    padding:9px 10px !important;
    border-radius:11px !important;
    overflow:visible !important;
  }

  .lapkeu-brand { width:auto !important; min-width:0 !important; }
  .lapkeu-brand-copy { min-width:0 !important; }
  #reportPageTitle,
  #reportPageSubtitle { max-width:100% !important; }

  #filterForm {
    display:grid !important;
    grid-template-columns:145px minmax(190px,230px) 122px 122px 36px !important;
    align-items:end !important;
    justify-content:end !important;
    width:max-content !important;
    max-width:100% !important;
    min-width:0 !important;
    margin:0 0 0 auto !important;
    padding:0 !important;
    gap:6px !important;
    border:0 !important;
    background:transparent !important;
  }

  #filterForm > div,
  .lapkeu-filter-type,
  .lapkeu-filter-office,
  .lapkeu-filter-closing,
  .lapkeu-filter-date,
  .lapkeu-filter-actions {
    width:auto !important;
    min-width:0 !important;
    max-width:none !important;
  }

  #filterForm label {
    display:block;
    margin:0 0 3px 1px !important;
    font-size:7.5px !important;
    line-height:1 !important;
    white-space:nowrap;
  }

  .lapkeu-date-label-row {
    min-width:0;
    height:auto;
    margin:0 0 3px 1px !important;
    gap:4px !important;
  }
  .lapkeu-date-label-row label { margin:0 !important; }
  .lapkeu-insight-btn { width:16px !important; height:16px !important; font-size:9px !important; }

  #filterForm .inp-modern {
    width:100% !important;
    min-width:0 !important;
    height:34px !important;
    padding:0 8px !important;
    border-radius:8px !important;
    font-size:10px !important;
    text-overflow:ellipsis;
  }
  #filterForm select.inp-modern {
    padding-right:26px !important;
    background-position:right 7px center !important;
    background-size:12px 12px !important;
  }
  .lapkeu-filter-actions { height:34px !important; align-items:flex-end !important; }
  #btnExportLapkeu { width:34px !important; min-width:34px !important; height:34px !important; }

  #reportStage {
    flex:1 1 auto !important;
    min-height:0 !important;
    width:100% !important;
    overflow:hidden !important;
  }
  .report-view { min-height:0 !important; min-width:0 !important; }
  #viewFinancial { height:100% !important; min-height:0 !important; overflow:hidden !important; }
  #financialPanel { flex:1 1 auto !important; min-height:0 !important; overflow:hidden !important; }
  #financialTableScroll {
    flex:1 1 auto !important;
    min-height:0 !important;
    overflow:auto !important;
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
  }
  .custom-view-shell {
    min-height:0 !important;
    overflow:auto !important;
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
  }

  #rekapContainer { grid-template-columns:repeat(3,minmax(0,1fr)) !important; }
  #tabelLapKeu { width:100% !important; min-width:0 !important; }

  @media (min-width:1440px) {
    #lapkeuPage { height:calc(100dvh - 76px) !important; padding:7px !important; }
    #lapkeuHeader { padding:10px 12px !important; gap:16px !important; }
    #filterForm { grid-template-columns:155px 230px 128px 128px 36px !important; gap:7px !important; }
    #filterForm .inp-modern { height:36px !important; font-size:11px !important; }
    .lapkeu-filter-actions { height:36px !important; }
    #btnExportLapkeu { width:36px !important; min-width:36px !important; height:36px !important; }
  }

  @media (min-width:768px) and (max-width:1279px) {
    #lapkeuPage { height:calc(100dvh - 68px) !important; padding:7px !important; gap:7px !important; }
    #lapkeuHeader {
      grid-template-columns:1fr !important;
      align-items:stretch !important;
      gap:8px !important;
    }
    #filterForm {
      width:100% !important;
      margin-left:0 !important;
      grid-template-columns:minmax(125px,.85fr) minmax(175px,1.3fr) minmax(112px,.75fr) minmax(112px,.75fr) 34px !important;
    }
    #rekapContainer { grid-template-columns:repeat(3,minmax(0,1fr)) !important; }
    .weekly-card-grid { grid-template-columns:repeat(2,minmax(0,1fr)) !important; }
  }

  @media (max-width:767px) {
    body { overflow:hidden !important; }
    #lapkeuPage {
      height:calc(100dvh - 56px) !important;
      min-height:0 !important;
      padding:4px !important;
      gap:5px !important;
    }
    #lapkeuHeader {
      display:block !important;
      padding:7px !important;
      border-radius:9px !important;
    }
    .lapkeu-brand { width:100% !important; gap:7px !important; }
    .lapkeu-brand-icon {
      width:30px !important;
      height:30px !important;
      flex:0 0 30px !important;
      border-radius:8px !important;
    }
    .lapkeu-brand-icon svg { width:15px !important; height:15px !important; }
    #reportPageTitle { font-size:12px !important; line-height:1.1 !important; }
    #reportPageSubtitle {
      display:block !important;
      max-width:205px !important;
      margin-top:2px !important;
      font-size:7px !important;
      line-height:1.2 !important;
    }
    .lapkeu-mobile-filter-toggle {
      display:inline-flex !important;
      height:29px !important;
      padding:0 9px !important;
      font-size:8px !important;
    }

    #filterForm {
      display:none !important;
      grid-template-columns:repeat(12,minmax(0,1fr)) !important;
      width:100% !important;
      max-width:none !important;
      margin:7px 0 0 !important;
      padding:7px 0 0 !important;
      gap:5px !important;
      border-top:1px solid #e2e8f0 !important;
    }
    #filterForm.lapkeu-filter-open { display:grid !important; }
    .lapkeu-filter-type { grid-column:span 6 !important; }
    .lapkeu-filter-office { grid-column:span 6 !important; }
    .lapkeu-filter-closing { grid-column:span 5 !important; }
    .lapkeu-filter-date { grid-column:span 5 !important; }
    .lapkeu-filter-actions { grid-column:span 2 !important; height:31px !important; }
    #filterForm label { margin-bottom:2px !important; font-size:6.5px !important; }
    .lapkeu-date-label-row { margin-bottom:2px !important; }
    .lapkeu-insight-btn { width:14px !important; height:14px !important; font-size:8px !important; }
    #filterForm .inp-modern {
      height:31px !important;
      padding:0 6px !important;
      border-radius:7px !important;
      font-size:8.5px !important;
    }
    #filterForm select.inp-modern {
      padding-right:21px !important;
      background-position:right 5px center !important;
      background-size:10px 10px !important;
    }
    #btnExportLapkeu { width:31px !important; min-width:31px !important; height:31px !important; }

    #rekapContainer {
      display:flex !important;
      grid-template-columns:none !important;
      overflow-x:auto !important;
      overflow-y:hidden !important;
      gap:5px !important;
      padding-bottom:2px;
      scroll-snap-type:x proximity;
      scrollbar-width:none;
    }
    #rekapContainer::-webkit-scrollbar { display:none; }
    #rekapContainer > * { width:150px !important; min-width:150px !important; scroll-snap-align:start; }

    .financial-toolbar {
      display:grid !important;
      grid-template-columns:minmax(0,1fr) auto !important;
      align-items:center !important;
      gap:5px !important;
      padding:6px !important;
    }
    .financial-toolbar-main { min-width:0 !important; }
    .financial-toolbar-icon { width:27px !important; height:27px !important; flex:0 0 27px !important; }
    .financial-toolbar-title { font-size:10px !important; }
    .financial-toolbar-meta { display:none !important; }
    .financial-toolbar-actions { min-width:0 !important; gap:3px !important; }
    .financial-search-wrap { width:min(132px,39vw) !important; }
    .financial-search { height:29px !important; padding-left:27px !important; font-size:8.5px !important; }
    .financial-tool-btn { width:29px !important; min-width:29px !important; height:29px !important; padding:0 !important; }

    #financialTableScroll { overflow:auto !important; }
    #tabelLapKeu {
      width:100% !important;
      min-width:570px !important;
      table-layout:fixed !important;
    }
    #tabelLapKeu .lap-code-col { display:none !important; }
    #tabelLapKeu col:nth-child(2) { width:205px !important; }
    #tabelLapKeu .lap-value-col { width:96px !important; }
    #tabelLapKeu .lap-pct-col { width:58px !important; }
    #tabelLapKeu thead th { height:32px !important; padding:5px 6px !important; font-size:7.5px !important; }
    #tabelLapKeu tbody tr { height:35px !important; }
    #tabelLapKeu tbody td { padding:5px 6px !important; font-size:8.5px !important; }
    #tabelLapKeu th:nth-child(2),
    #tabelLapKeu td:nth-child(2) {
      position:sticky;
      left:0;
      z-index:18;
      border-right:1px solid #e2e8f0;
      box-shadow:4px 0 8px -8px rgba(15,23,42,.8);
    }
    #tabelLapKeu thead th:nth-child(2) { z-index:48; background:#f8fafc !important; }
    #tabelLapKeu tbody td:nth-child(2) { background:#fff; }
    #tabelLapKeu .financial-level-1 td:nth-child(2) { background:#eaf2ff !important; }
    #tabelLapKeu .financial-level-2 td:nth-child(2) { background:#f4f7fb !important; }
    #tabelLapKeu .financial-level-3 td:nth-child(2) { background:#fafcff !important; }
    #tabelLapKeu .financial-name { font-size:8.5px !important; }
    #tabelLapKeu .financial-amount-short { font-size:8.5px !important; }
    #tabelLapKeu .financial-pct { font-size:8px !important; }

    .custom-top-summary {
      display:flex !important;
      overflow-x:auto !important;
      gap:5px !important;
      scrollbar-width:none;
    }
    .custom-top-summary::-webkit-scrollbar { display:none; }
    .custom-top-summary > * { width:150px !important; min-width:150px !important; }
    .macro-dashboard-grid { grid-template-columns:1fr !important; }
    .weekly-card-grid { grid-template-columns:1fr !important; }
    .weekly-chart-toolbar { align-items:stretch !important; }
    .weekly-chart-controls { width:100% !important; }
    .weekly-metric-tabs { width:100% !important; grid-template-columns:repeat(2,minmax(0,1fr)) !important; }
    .weekly-chart-stat-grid,
    .weekly-chart-footer { grid-template-columns:repeat(2,minmax(0,1fr)) !important; }
    .lapkeu-insight-modal { padding:8px !important; align-items:flex-end !important; }
    .lapkeu-insight-card { width:100% !important; max-height:90dvh !important; border-radius:16px 16px 0 0 !important; }
  }

  @media (max-width:380px) {
    #reportPageSubtitle { max-width:170px !important; }
    #filterForm { gap:4px !important; }
    #filterForm .inp-modern { font-size:8px !important; padding:0 5px !important; }
    #rekapContainer > *, .custom-top-summary > * { width:142px !important; min-width:142px !important; }
    .financial-search-wrap { width:112px !important; }
  }



  /* ========================================================
     INSIGHT DIREKSI V11
     Tombol informasi di judul dan modal analisis komprehensif.
     ======================================================== */
  .lapkeu-title-row {
    display:flex;
    align-items:center;
    gap:7px;
    min-width:0;
  }

  .lapkeu-title-row #reportPageTitle { min-width:0; }

  .lapkeu-title-insight {
    flex:0 0 21px !important;
    width:21px !important;
    height:21px !important;
    border-color:#bfdbfe !important;
    background:#eff6ff !important;
    color:#2563eb !important;
    font-size:11px !important;
    box-shadow:none !important;
  }

  .lapkeu-title-insight:hover {
    border-color:#93c5fd !important;
    background:#dbeafe !important;
    color:#1d4ed8 !important;
    transform:translateY(-1px);
  }

  .lapkeu-insight-modal {
    z-index:9999 !important;
    padding:16px !important;
    background:rgba(15,23,42,.56) !important;
    backdrop-filter:blur(5px) !important;
  }

  .lapkeu-insight-card {
    width:min(1120px,97vw) !important;
    max-height:92dvh !important;
    overflow:auto !important;
    border:1px solid #dbeafe !important;
    border-radius:18px !important;
    background:#f8fafc !important;
    box-shadow:0 30px 90px rgba(15,23,42,.34) !important;
  }

  .lapkeu-insight-head {
    padding:15px 17px !important;
    background:rgba(255,255,255,.97) !important;
    backdrop-filter:blur(8px);
  }

  .lapkeu-insight-kicker { font-size:9px !important; }
  .lapkeu-insight-title { font-size:20px !important; }
  .lapkeu-insight-sub { font-size:10px !important; }
  .lapkeu-insight-body { padding:12px !important; background:#f8fafc; }

  .lapkeu-insight-summary {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(155px,1fr));
    gap:8px;
    margin-bottom:9px;
  }

  .lapkeu-insight-stat {
    min-width:0;
    border:1px solid #e2e8f0;
    border-left:4px solid var(--insight-accent,#3b82f6);
    border-radius:11px;
    background:#fff;
    padding:10px;
    box-shadow:0 1px 2px rgba(15,23,42,.035);
  }

  .lapkeu-insight-stat.good { --insight-accent:#10b981; }
  .lapkeu-insight-stat.warn { --insight-accent:#f59e0b; }
  .lapkeu-insight-stat.bad { --insight-accent:#ef4444; }
  .lapkeu-insight-stat.info { --insight-accent:#3b82f6; }
  .lapkeu-insight-stat.purple { --insight-accent:#8b5cf6; }

  .lapkeu-insight-stat-label {
    font-size:8px;
    line-height:1.1;
    font-weight:900;
    letter-spacing:.075em;
    text-transform:uppercase;
    color:#64748b;
  }

  .lapkeu-insight-stat-value {
    margin-top:7px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    font-family:'JetBrains Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:16px;
    line-height:1;
    font-weight:950;
    color:#0f172a;
  }

  .lapkeu-insight-stat-note {
    margin-top:6px;
    font-size:8px;
    line-height:1.35;
    font-weight:750;
    color:#64748b;
  }

  .lapkeu-insight-grid {
    display:grid !important;
    grid-template-columns:repeat(3,minmax(0,1fr)) !important;
    gap:9px !important;
  }

  .lapkeu-insight-block {
    min-width:0;
    border:1px solid #e2e8f0 !important;
    border-radius:12px !important;
    background:#fff !important;
    padding:11px !important;
    box-shadow:0 1px 2px rgba(15,23,42,.025);
  }

  .lapkeu-insight-block.full { grid-column:1 / -1 !important; }
  .lapkeu-insight-block.span-2 { grid-column:span 2; }
  .lapkeu-insight-block h4 {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    margin:0 0 8px !important;
    padding-bottom:7px;
    border-bottom:1px solid #eef2f7;
    font-size:11px !important;
    line-height:1.2;
    font-weight:950 !important;
    color:#0f172a !important;
  }

  .lapkeu-insight-block ul {
    margin:0 !important;
    padding-left:16px !important;
    font-size:9px !important;
    line-height:1.5 !important;
    font-weight:700 !important;
    color:#334155 !important;
  }

  .lapkeu-insight-block li + li { margin-top:5px !important; }

  .insight-table-wrap {
    width:100%;
    overflow:auto;
    border:1px solid #e2e8f0;
    border-radius:9px;
  }

  .insight-table {
    width:100%;
    min-width:520px;
    border-collapse:collapse;
    font-size:8px;
    font-variant-numeric:tabular-nums;
  }

  .insight-table th {
    position:sticky;
    top:0;
    z-index:1;
    padding:7px 8px;
    border-bottom:1px solid #cbd5e1;
    background:#f8fafc;
    color:#64748b;
    font-size:7px;
    font-weight:900;
    letter-spacing:.055em;
    text-transform:uppercase;
    white-space:nowrap;
  }

  .insight-table td {
    padding:7px 8px;
    border-bottom:1px solid #eef2f7;
    color:#334155;
    font-weight:750;
    vertical-align:top;
  }

  .insight-table tr:last-child td { border-bottom:0; }
  .insight-table .num { text-align:right; white-space:nowrap; font-family:'JetBrains Mono',ui-monospace,monospace; }

  .insight-signal {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:48px;
    padding:3px 6px;
    border:1px solid;
    border-radius:999px;
    font-size:7px;
    line-height:1;
    font-weight:900;
    white-space:nowrap;
  }

  .insight-signal.good { color:#047857; background:#ecfdf5; border-color:#a7f3d0; }
  .insight-signal.warn { color:#b45309; background:#fffbeb; border-color:#fde68a; }
  .insight-signal.bad { color:#be123c; background:#fff1f2; border-color:#fecdd3; }
  .insight-signal.info { color:#1d4ed8; background:#eff6ff; border-color:#bfdbfe; }

  .insight-callout {
    margin-top:9px;
    padding:8px 9px;
    border:1px solid #bfdbfe;
    border-radius:9px;
    background:#eff6ff;
    color:#1e3a8a;
    font-size:8px;
    line-height:1.45;
    font-weight:750;
  }


  /* ========================================================
     DECISION CENTER V12
     Rekomendasi dibuat operasional untuk Kepala Cabang.
     ======================================================== */
  .lapkeu-decision-center {
    margin-bottom:10px;
    overflow:hidden;
    border:1px solid #bfdbfe;
    border-radius:14px;
    background:linear-gradient(145deg,#eff6ff 0%,#fff 58%,#f8fafc 100%);
    box-shadow:0 5px 18px -16px rgba(37,99,235,.8);
  }

  .lapkeu-decision-head {
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    padding:12px 13px;
    border-bottom:1px solid #dbeafe;
  }

  .lapkeu-decision-head-main { min-width:0; }
  .lapkeu-decision-eyebrow {
    font-size:7px;
    line-height:1;
    font-weight:950;
    letter-spacing:.105em;
    text-transform:uppercase;
    color:#2563eb;
  }

  .lapkeu-decision-title {
    margin-top:5px;
    color:#0f172a;
    font-size:14px;
    line-height:1.15;
    font-weight:950;
    letter-spacing:-.015em;
  }

  .lapkeu-decision-caption {
    max-width:720px;
    margin-top:4px;
    color:#64748b;
    font-size:8px;
    line-height:1.45;
    font-weight:700;
  }

  .lapkeu-decision-counts {
    display:flex;
    align-items:center;
    justify-content:flex-end;
    flex-wrap:wrap;
    gap:5px;
    flex:0 0 auto;
  }

  .decision-count {
    display:inline-flex;
    align-items:center;
    min-height:23px;
    padding:0 7px;
    border:1px solid;
    border-radius:999px;
    font-size:7px;
    line-height:1;
    font-weight:950;
    white-space:nowrap;
  }

  .decision-count.bad { color:#be123c; border-color:#fecdd3; background:#fff1f2; }
  .decision-count.warn { color:#b45309; border-color:#fde68a; background:#fffbeb; }
  .decision-count.good { color:#047857; border-color:#a7f3d0; background:#ecfdf5; }

  .lapkeu-decision-grid {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:8px;
    padding:10px;
  }

  .decision-card {
    position:relative;
    min-width:0;
    overflow:hidden;
    border:1px solid #e2e8f0;
    border-left:4px solid var(--decision-accent,#3b82f6);
    border-radius:11px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.035);
  }

  .decision-card.bad { --decision-accent:#ef4444; }
  .decision-card.warn { --decision-accent:#f59e0b; }
  .decision-card.good { --decision-accent:#10b981; }
  .decision-card.info { --decision-accent:#3b82f6; }

  .decision-card-head {
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:8px;
    padding:9px 10px 8px;
    border-bottom:1px solid #eef2f7;
    background:linear-gradient(180deg,#fff,#fbfdff);
  }

  .decision-card-title {
    min-width:0;
    color:#0f172a;
    font-size:10px;
    line-height:1.25;
    font-weight:950;
  }

  .decision-priority {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:31px;
    height:21px;
    padding:0 6px;
    border-radius:7px;
    background:var(--decision-accent,#3b82f6);
    color:#fff;
    font-size:7px;
    line-height:1;
    font-weight:950;
    white-space:nowrap;
  }

  .decision-card-body { padding:9px 10px 10px; }
  .decision-finding {
    margin-bottom:8px;
    padding:7px 8px;
    border:1px solid #eef2f7;
    border-radius:8px;
    background:#f8fafc;
    color:#475569;
    font-size:8px;
    line-height:1.45;
    font-weight:750;
  }

  .decision-fields {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:7px;
  }

  .decision-field { min-width:0; }
  .decision-field.full { grid-column:1 / -1; }
  .decision-field-label {
    color:#94a3b8;
    font-size:6.5px;
    line-height:1;
    font-weight:950;
    letter-spacing:.07em;
    text-transform:uppercase;
  }

  .decision-field-value {
    margin-top:3px;
    color:#334155;
    font-size:8px;
    line-height:1.4;
    font-weight:800;
  }

  .decision-field-value.strong { color:#0f172a; font-weight:950; }
  .decision-footer-note {
    margin:0 10px 10px;
    padding:8px 9px;
    border:1px dashed #bfdbfe;
    border-radius:9px;
    background:rgba(255,255,255,.72);
    color:#475569;
    font-size:7.5px;
    line-height:1.45;
    font-weight:750;
  }

  .decision-question-grid {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:6px;
  }

  .decision-question {
    display:flex;
    align-items:flex-start;
    gap:7px;
    min-width:0;
    padding:8px;
    border:1px solid #e2e8f0;
    border-radius:9px;
    background:#f8fafc;
    color:#334155;
    font-size:8px;
    line-height:1.4;
    font-weight:800;
  }

  .decision-question-num {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:19px;
    height:19px;
    flex:0 0 19px;
    border-radius:6px;
    background:#dbeafe;
    color:#1d4ed8;
    font-size:7px;
    font-weight:950;
  }

  @media (max-width:900px) {
    .lapkeu-decision-grid { grid-template-columns:1fr; }
    .decision-question-grid { grid-template-columns:1fr; }
  }

  @media (max-width:900px) {
    .lapkeu-insight-grid { grid-template-columns:repeat(2,minmax(0,1fr)) !important; }
    .lapkeu-insight-block.span-2 { grid-column:1 / -1; }
  }

  @media (max-width:767px) {
    .lapkeu-title-insight {
      flex-basis:18px !important;
      width:18px !important;
      height:18px !important;
      font-size:9px !important;
    }
    .lapkeu-insight-modal {
      padding:0 !important;
      align-items:flex-end !important;
    }
    .lapkeu-insight-card {
      width:100% !important;
      max-height:92dvh !important;
      border-radius:17px 17px 0 0 !important;
    }
    .lapkeu-insight-head { padding:12px !important; }
    .lapkeu-insight-title { font-size:15px !important; }
    .lapkeu-insight-sub { font-size:8px !important; }
    .lapkeu-insight-body { padding:8px !important; }
    .lapkeu-insight-summary { grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; }
    .lapkeu-insight-stat { padding:8px; }
    .lapkeu-insight-stat-value { font-size:13px; }
    .lapkeu-insight-grid { grid-template-columns:1fr !important; gap:7px !important; }
    .lapkeu-insight-block.full,
    .lapkeu-insight-block.span-2 { grid-column:auto !important; }
    .lapkeu-insight-block { padding:9px !important; }
    .lapkeu-insight-block h4 { font-size:10px !important; }
    .lapkeu-insight-block ul { font-size:8px !important; }
    .insight-table { min-width:470px; }
  }



  /* ========================================================
     MODAL INSIGHT BUGFIX V13
     Header tetap utuh, hanya isi modal yang scroll, tabel tidak
     lagi menimpa judul, dan ukuran tabel mengikuti jumlah kolom.
     ======================================================== */
  .lapkeu-insight-modal {
    overflow:hidden !important;
    overscroll-behavior:contain;
  }

  .lapkeu-insight-card {
    display:flex !important;
    flex-direction:column !important;
    width:min(1160px,calc(100vw - 32px)) !important;
    height:min(92dvh,860px) !important;
    max-height:calc(100dvh - 32px) !important;
    min-height:0 !important;
    overflow:hidden !important;
    isolation:isolate;
  }

  .lapkeu-insight-head {
    position:relative !important;
    top:auto !important;
    z-index:100 !important;
    flex:0 0 auto !important;
    min-height:78px;
    overflow:hidden;
    border-bottom:1px solid #dbe3ee !important;
    background:#fff !important;
    box-shadow:0 5px 16px -16px rgba(15,23,42,.75);
  }

  .lapkeu-insight-head > div:first-child {
    min-width:0;
    max-width:calc(100% - 48px);
    padding-right:6px;
  }

  .lapkeu-insight-kicker,
  .lapkeu-insight-title,
  .lapkeu-insight-sub {
    position:relative;
    z-index:1;
  }

  .lapkeu-insight-title {
    max-width:100%;
    overflow-wrap:anywhere;
    white-space:normal !important;
  }

  .lapkeu-insight-sub {
    max-width:940px;
    line-height:1.4 !important;
    white-space:normal !important;
  }

  .lapkeu-insight-close {
    position:relative;
    z-index:102 !important;
    flex:0 0 auto;
  }

  .lapkeu-insight-body {
    position:relative;
    z-index:1;
    flex:1 1 auto !important;
    min-width:0;
    min-height:0 !important;
    overflow-x:hidden !important;
    overflow-y:auto !important;
    overscroll-behavior:contain;
    scrollbar-gutter:stable;
    -webkit-overflow-scrolling:touch;
    padding-bottom:max(14px,env(safe-area-inset-bottom)) !important;
  }

  .lapkeu-insight-grid {
    grid-template-columns:repeat(2,minmax(0,1fr)) !important;
    align-items:start;
  }

  .lapkeu-insight-block,
  .lapkeu-decision-center,
  .decision-card {
    max-width:100%;
  }

  .lapkeu-insight-block.span-2,
  .lapkeu-insight-block.full {
    grid-column:1 / -1 !important;
  }

  .insight-table-wrap {
    position:relative;
    display:block;
    max-width:100%;
    min-width:0;
    overflow-x:auto !important;
    overflow-y:hidden !important;
    overscroll-behavior-inline:contain;
    scrollbar-gutter:stable;
    -webkit-overflow-scrolling:touch;
    background:#fff;
  }

  .insight-table th {
    position:static !important;
    top:auto !important;
    z-index:auto !important;
  }

  .insight-table-wrap.insight-table-compact .insight-table {
    width:100% !important;
    min-width:100% !important;
  }

  .insight-table-wrap.insight-table-medium .insight-table {
    width:100% !important;
    min-width:460px !important;
  }

  .insight-table-wrap.insight-table-wide .insight-table {
    width:100% !important;
    min-width:760px !important;
  }

  .insight-table th,
  .insight-table td {
    overflow-wrap:anywhere;
  }

  .insight-table th.num,
  .insight-table td.num {
    overflow-wrap:normal;
  }

  .lapkeu-decision-head,
  .decision-card-head {
    min-width:0;
  }

  .decision-field-value,
  .decision-finding,
  .decision-question > span:last-child {
    overflow-wrap:anywhere;
  }

  @media (max-width:900px) {
    .lapkeu-insight-grid {
      grid-template-columns:1fr !important;
    }
    .lapkeu-insight-block.span-2,
    .lapkeu-insight-block.full {
      grid-column:auto !important;
    }
  }

  @media (max-width:767px) {
    .lapkeu-insight-card {
      width:100% !important;
      height:min(94dvh,820px) !important;
      max-height:94dvh !important;
      border-radius:17px 17px 0 0 !important;
    }

    .lapkeu-insight-head {
      min-height:72px;
      padding:11px 12px !important;
    }

    .lapkeu-insight-head > div:first-child {
      max-width:calc(100% - 42px);
    }

    .lapkeu-insight-title {
      font-size:14px !important;
      line-height:1.2 !important;
    }

    .lapkeu-insight-sub {
      display:-webkit-box;
      overflow:hidden;
      -webkit-line-clamp:2;
      -webkit-box-orient:vertical;
      font-size:7.5px !important;
    }

    .lapkeu-insight-body {
      padding:7px 7px max(12px,env(safe-area-inset-bottom)) !important;
      scrollbar-gutter:auto;
    }

    .lapkeu-decision-head {
      flex-direction:column;
      gap:8px;
    }

    .lapkeu-decision-counts {
      width:100%;
      justify-content:flex-start;
    }

    .decision-fields {
      grid-template-columns:1fr;
    }

    .decision-field.full {
      grid-column:auto;
    }

    .insight-table-wrap.insight-table-medium .insight-table {
      min-width:430px !important;
    }

    .insight-table-wrap.insight-table-wide .insight-table {
      min-width:680px !important;
    }
  }



  /* ========================================================
     FINANCIAL HIERARCHY FIX V14
     - Breakdown akun selalu menempel tepat di bawah parent.
     - Level ditentukan dari parent terdekat yang benar, bukan panjang kode.
     - Desktop/tablet/mobile tetap aman dan mudah dibaca.
     ======================================================== */
  #financialTableScroll {
    position:relative;
    scrollbar-gutter:stable;
  }

  #tabelLapKeu thead {
    position:sticky;
    top:0;
    z-index:60;
  }

  #tabelLapKeu thead th {
    position:sticky;
    top:0;
    z-index:61;
    background:#f8fafc !important;
    box-shadow:inset 0 -1px 0 #cbd5e1;
  }

  #tabelLapKeu tbody tr.financial-row {
    transition:background-color .14s ease, box-shadow .14s ease;
  }

  #tabelLapKeu tbody tr.financial-row[data-parent="1"] {
    cursor:pointer;
  }

  #tabelLapKeu .financial-name-wrap {
    position:relative;
    min-width:0;
  }

  #tabelLapKeu .financial-level-1 td:nth-child(2) {
    box-shadow:inset 4px 0 0 #2563eb;
  }

  #tabelLapKeu .financial-level-2 td:nth-child(2) {
    box-shadow:inset 3px 0 0 #94a3b8;
  }

  #tabelLapKeu .financial-level-3 td:nth-child(2) {
    box-shadow:inset 2px 0 0 #cbd5e1;
  }

  #tabelLapKeu .financial-level-detail td:nth-child(2) {
    box-shadow:inset 1px 0 0 #e2e8f0;
  }

  #tabelLapKeu tr[data-depth="1"] .financial-name,
  #tabelLapKeu tr[data-depth="2"] .financial-name,
  #tabelLapKeu tr[data-depth="3"] .financial-name {
    font-weight:800;
  }

  #tabelLapKeu .caret {
    border:1px solid transparent;
  }

  #tabelLapKeu tr[data-parent="1"]:hover .caret {
    border-color:#dbeafe;
    background:#eff6ff;
  }

  #tabelLapKeu tr[data-parent="1"]:focus-visible {
    outline:2px solid #60a5fa;
    outline-offset:-2px;
  }

  #tabelLapKeu tr[data-parent="1"]:focus-visible td {
    background:#eff6ff !important;
  }

  .financial-tree-hint {
    display:inline-flex;
    align-items:center;
    gap:4px;
    margin-left:6px;
    color:#94a3b8;
    font-size:7px;
    font-weight:800;
    white-space:nowrap;
  }

  @media (min-width:1280px) {
    #tabelLapKeu .lap-code-col { width:74px !important; }
    #tabelLapKeu .lap-value-col { width:142px !important; }
    #tabelLapKeu .lap-pct-col { width:70px !important; }
    #tabelLapKeu tbody td { font-size:10.5px !important; }
    #tabelLapKeu .financial-amount { font-size:10.5px !important; }
    #tabelLapKeu .financial-name { font-size:10.5px !important; }
  }

  @media (min-width:768px) and (max-width:1279px) {
    #financialPanel { min-height:0; }
    #financialTableScroll { overflow:auto !important; }
    #tabelLapKeu {
      min-width:760px !important;
      width:100% !important;
    }
    #tabelLapKeu .lap-code-col { width:72px !important; }
    #tabelLapKeu .lap-value-col { width:128px !important; }
    #tabelLapKeu .lap-pct-col { width:68px !important; }
  }

  @media (max-width:767px) {
    #financialPanel {
      border-radius:9px !important;
    }

    .financial-toolbar {
      grid-template-columns:minmax(0,1fr) minmax(0,auto) !important;
      padding:5px 6px !important;
    }

    .financial-toolbar-main {
      gap:5px !important;
    }

    .financial-toolbar-actions {
      display:flex !important;
      min-width:0;
      gap:3px !important;
    }

    .financial-search-wrap {
      width:min(128px,38vw) !important;
      min-width:86px;
    }

    .financial-search {
      width:100% !important;
      height:28px !important;
      padding:0 6px 0 25px !important;
      font-size:8px !important;
    }

    .financial-search-wrap svg {
      left:7px !important;
      width:11px !important;
      height:11px !important;
    }

    .financial-tool-btn {
      width:28px !important;
      min-width:28px !important;
      height:28px !important;
      border-radius:7px !important;
    }

    #financialTableScroll {
      overflow:auto !important;
      scrollbar-gutter:auto;
    }

    #tabelLapKeu {
      width:100% !important;
      min-width:540px !important;
      table-layout:fixed !important;
    }

    #tabelLapKeu col:nth-child(2) { width:188px !important; }
    #tabelLapKeu .lap-value-col { width:86px !important; }
    #tabelLapKeu .lap-pct-col { width:52px !important; }

    #tabelLapKeu thead th {
      height:30px !important;
      padding:5px !important;
      font-size:7px !important;
    }

    #tabelLapKeu tbody tr { height:34px !important; }
    #tabelLapKeu tbody td {
      height:34px !important;
      padding:5px !important;
      font-size:8.2px !important;
    }

    #tabelLapKeu th:nth-child(2),
    #tabelLapKeu td:nth-child(2) {
      width:188px !important;
      min-width:188px !important;
      max-width:188px !important;
    }

    #tabelLapKeu .financial-name-wrap {
      width:100%;
      overflow:hidden;
    }

    #tabelLapKeu .financial-name {
      display:block;
      max-width:100%;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
      font-size:8.3px !important;
    }

    #tabelLapKeu .financial-mobile-code {
      margin-right:4px !important;
      padding:2px 3px !important;
      font-size:6.5px !important;
    }

    #tabelLapKeu .financial-amount-short {
      font-size:8px !important;
      letter-spacing:-.025em;
    }

    #tabelLapKeu .financial-pct {
      font-size:7.5px !important;
      letter-spacing:-.03em;
    }

    #tabelLapKeu .caret {
      width:14px !important;
      height:14px !important;
      margin-right:2px !important;
      font-size:6px !important;
    }

    #tabelLapKeu .financial-level-1 td:nth-child(2) {
      box-shadow:inset 3px 0 0 #2563eb, 4px 0 8px -8px rgba(15,23,42,.8) !important;
    }
    #tabelLapKeu .financial-level-2 td:nth-child(2) {
      box-shadow:inset 2px 0 0 #94a3b8, 4px 0 8px -8px rgba(15,23,42,.8) !important;
    }

    .financial-tree-hint { display:none; }
  }

  @media (max-width:380px) {
    .financial-search-wrap { width:104px !important; }
    #tabelLapKeu { min-width:510px !important; }
    #tabelLapKeu col:nth-child(2),
    #tabelLapKeu th:nth-child(2),
    #tabelLapKeu td:nth-child(2) {
      width:174px !important;
      min-width:174px !important;
      max-width:174px !important;
    }
    #tabelLapKeu .lap-value-col { width:82px !important; }
    #tabelLapKeu .lap-pct-col { width:48px !important; }
    #tabelLapKeu tbody td { padding:4px !important; }
  }



  /* ========================================================
     TREN MINGGUAN - COMPACT METRIC DROPDOWN V15
     Pilihan indikator memakai satu dropdown di semua device
     agar toolbar tidak memakan ruang vertikal.
     ======================================================== */
  .weekly-chart-controls {
    flex-wrap:nowrap !important;
    align-items:center !important;
    gap:6px !important;
  }

  .weekly-metric-select-wrap {
    position:relative;
    flex:0 1 176px;
    width:176px;
    min-width:132px;
  }

  .weekly-metric-select {
    display:block;
    width:100%;
    height:36px;
    min-width:0;
    padding:0 32px 0 10px;
    border:1px solid #dbe3ee;
    border-radius:10px;
    outline:none;
    appearance:none;
    -webkit-appearance:none;
    background-color:#fff;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 9px center;
    background-size:13px 13px;
    color:#334155;
    font-size:9.5px;
    line-height:1;
    font-weight:900;
    text-overflow:ellipsis;
    cursor:pointer;
    box-shadow:0 1px 2px rgba(15,23,42,.035);
    transition:border-color .15s ease,box-shadow .15s ease,background-color .15s ease;
  }

  .weekly-metric-select:hover {
    border-color:#bfdbfe;
    background-color:#fbfdff;
  }

  .weekly-metric-select:focus {
    border-color:var(--chart-accent,#2563eb);
    box-shadow:0 0 0 3px rgba(37,99,235,.10);
  }

  @media (min-width:768px) {
    .weekly-chart-toolbar {
      align-items:center !important;
    }
    .weekly-metric-select-wrap {
      flex-basis:180px;
      width:180px;
    }
  }

  @media (max-width:767px) {
    .weekly-chart-toolbar {
      align-items:stretch !important;
      flex-direction:column !important;
      gap:6px !important;
      padding:8px !important;
    }

    .weekly-chart-controls {
      display:grid !important;
      grid-template-columns:minmax(0,1fr) auto !important;
      width:100% !important;
      min-width:0 !important;
      align-items:center !important;
      justify-content:stretch !important;
      gap:5px !important;
    }

    .weekly-metric-select-wrap {
      width:100% !important;
      min-width:0 !important;
      max-width:none !important;
    }

    .weekly-metric-select {
      width:100% !important;
      height:30px !important;
      padding-left:8px !important;
      padding-right:25px !important;
      border-radius:8px !important;
      background-position:right 7px center !important;
      background-size:11px 11px !important;
      font-size:8.5px !important;
    }

    .weekly-mode-toggle {
      height:30px !important;
      min-width:68px;
      padding:0 8px !important;
      border-radius:8px !important;
      font-size:8px !important;
      justify-content:center;
    }

    .weekly-mode-toggle input {
      width:13px !important;
      height:13px !important;
    }
  }

  @media (max-width:380px) {
    .weekly-chart-controls {
      grid-template-columns:minmax(0,1fr) 64px !important;
      gap:4px !important;
    }
    .weekly-metric-select { font-size:8px !important; }
    .weekly-mode-toggle {
      min-width:64px;
      padding:0 6px !important;
      gap:5px !important;
    }
  }

</style>

<div id="lapkeuPage" class="max-w-[1920px] mx-auto px-2 md:px-4 py-2 md:py-4 h-[calc(100vh-10px)] flex flex-col space-y-2 md:space-y-4 bg-[#f8fafc]">
  
  <div id="lapkeuHeader" class="flex flex-col md:flex-row justify-between md:items-end gap-2 md:gap-4 shrink-0 bg-white p-2.5 md:p-4 rounded-xl border border-slate-200 shadow-sm">
    
    <div class="lapkeu-brand">
      <span class="lapkeu-brand-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V9"></path><path d="M10 19V5"></path><path d="M16 19v-7"></path><path d="M22 19H2"></path></svg>
      </span>
      <div class="lapkeu-brand-copy">
        <div class="lapkeu-title-row">
          <span id="reportPageTitle">Laporan Keuangan</span>
          <button type="button" class="lapkeu-insight-btn lapkeu-title-insight" onclick="openLapkeuInsight()" title="Buka rekomendasi keputusan kepala cabang" aria-label="Buka rekomendasi keputusan kepala cabang">i</button>
        </div>
        <span id="reportPageSubtitle">*Neraca, Laba Rugi, Ringkasan Makro &amp; Tren Mingguan</span>
        <div class="lapkeu-meta-row">
          <span id="activeViewChip" class="view-mode-chip">Neraca</span>
          <span id="badgeUnit">Memuat...</span>
        </div>
      </div>
      <button type="button" id="lapkeuMobileFilterToggle" class="lapkeu-mobile-filter-toggle" aria-expanded="false" aria-controls="filterForm" title="Buka filter">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
        <span>Filter</span>
      </button>
    </div>

    <form id="filterForm" class="grid grid-cols-2 md:flex md:flex-row items-end gap-2 md:gap-3 w-full md:w-auto">
      
      <div class="lapkeu-filter-type flex flex-col">
        <label class="text-[9px] md:text-[10px] font-bold text-blue-900 uppercase ml-1 mb-0.5 tracking-wider">Jenis Laporan</label>
        <select id="type_report" class="inp-modern text-[10px] md:text-sm">
          <option value="neraca detail kantor">NERACA</option>
          <option value="laba rugi detail kantor">LABA RUGI</option>
          <option value="tv_makro_summary">RINGKASAN MAKRO</option>
          <option value="tren_makro_mingguan">TREN MINGGUAN</option>
        </select>
      </div>

      <div class="lapkeu-filter-office flex flex-col">
        <label class="text-[9px] md:text-[10px] font-bold text-blue-900 uppercase ml-1 mb-0.5 tracking-wider">Cabang</label>
        <select id="opt_kantor_rec" class="inp-modern text-[10px] md:text-sm truncate"><option value="">Memuat...</option></select>
      </div>

      <div class="lapkeu-filter-closing flex flex-col">
        <label class="text-[9px] md:text-[10px] font-bold text-blue-900 uppercase ml-1 mb-0.5 tracking-wider">Closing</label>
        <input type="date" id="closing_date" class="inp-modern text-center text-[10px] md:text-sm" onclick="try{this.showPicker()}catch(e){}">
      </div>

      <div class="lapkeu-filter-date flex flex-col">
        <div class="lapkeu-date-label-row">
          <label class="text-[9px] md:text-[10px] font-bold text-blue-900 uppercase tracking-wider">Actual (Harian)</label>
        </div>
        <input type="date" id="harian_date" class="inp-modern text-center text-[10px] md:text-sm" onclick="try{this.showPicker()}catch(e){}">
      </div>
      
      <div class="lapkeu-filter-actions">
        <span id="autoApplyNote" class="auto-note hidden md:inline-block min-w-[92px] text-right">Auto apply</span>
        <button id="btnExportLapkeu" type="button" onclick="exportToExcel()" class="w-[36px] md:w-[40px] shrink-0 bg-[#10b981] hover:bg-[#059669] text-white rounded-lg shadow-md transition flex items-center justify-center" title="Export Excel">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="md:w-[20px] md:h-[20px]"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        </button>
      </div>
    </form>
  </div>

  <div id="lapkeuInsightModal" class="lapkeu-insight-modal" aria-hidden="true" onclick="if(event.target===this) closeLapkeuInsight()">
    <div class="lapkeu-insight-card custom-scrollbar">
      <div class="lapkeu-insight-head">
        <div>
          <div class="lapkeu-insight-kicker">Pusat Keputusan Kepala Cabang &amp; Direksi</div>
          <div id="lapkeuInsightTitle" class="lapkeu-insight-title">Ringkasan Kondisi</div>
          <div id="lapkeuInsightSub" class="lapkeu-insight-sub">Memuat insight...</div>
        </div>
        <button type="button" class="lapkeu-insight-close" onclick="closeLapkeuInsight()" title="Tutup">&times;</button>
      </div>
      <div id="lapkeuInsightBody" class="lapkeu-insight-body"></div>
    </div>
  </div>

  <div id="reportStage">
    <div id="loadingFP" class="hidden absolute inset-0 bg-white/90 z-50 flex flex-col items-center justify-center backdrop-blur-sm rounded-xl">
       <div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2"></div>
       <span class="text-[10px] md:text-xs font-bold text-blue-600 tracking-widest uppercase">Menyusun Data...</span>
    </div>

    <!-- VIEW 1 & 2: NERACA / LABA RUGI -->
    <section id="viewFinancial" class="report-view">
      <div id="rekapContainer" class="grid shrink-0"></div>

      <div id="financialPanel">
        <div id="financialTableToolbar" class="financial-toolbar">
          <div class="financial-toolbar-main">
            <span class="financial-toolbar-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 5h16M4 12h16M4 19h16M8 5v14"/></svg>
            </span>
            <div class="min-w-0">
              <div id="financialToolbarTitle" class="financial-toolbar-title">Neraca Keuangan</div>
              <div id="financialToolbarMeta" class="financial-toolbar-meta">0 akun · Klik kelompok untuk membuka detail</div>
            </div>
          </div>
          <div class="financial-toolbar-actions">
            <div class="financial-search-wrap">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
              <input id="financialSearch" type="search" class="financial-search" placeholder="Cari akun..." oninput="filterFinancialRows()">
            </div>
            <button type="button" class="financial-tool-btn" onclick="setFinancialExpansion(true)" title="Buka seluruh akun">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 10 5 5 5-5M4 4h16"/></svg><span>Buka</span>
            </button>
            <button type="button" class="financial-tool-btn" onclick="setFinancialExpansion(false)" title="Tutup seluruh akun">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 14 5-5 5 5M4 20h16"/></svg><span>Tutup</span>
            </button>
          </div>
        </div>

        <div id="financialTableScroll" class="custom-scrollbar">
          <table class="w-full text-left" id="tabelLapKeu">
            <colgroup>
              <col class="lap-code-col">
              <col>
              <col class="lap-value-col">
              <col class="lap-value-col">
              <col class="lap-value-col">
              <col class="lap-pct-col">
            </colgroup>
            <thead class="sticky top-0 z-40">
              <tr>
                <th class="lap-code-col">Kode</th>
                <th>Uraian Perkiraan</th>
                <th class="text-right">Actual</th>
                <th class="text-right">Closing</th>
                <th class="text-right">Selisih</th>
                <th class="text-right">%</th>
              </tr>
            </thead>
            <tbody id="lapBody"></tbody>
          </table>
          <div id="financialEmptyState">Akun yang dicari tidak ditemukan.</div>
        </div>
      </div>
    </section>

    <!-- VIEW 3: RINGKASAN MAKRO -->
    <section id="viewMacro" class="report-view custom-view-shell custom-scrollbar hidden">
      <div class="custom-view-inner"><div id="macroReport"></div></div>
    </section>

    <!-- VIEW 4: TREN MINGGUAN -->
    <section id="viewTrend" class="report-view custom-view-shell custom-scrollbar hidden">
      <div class="custom-view-inner"><div id="trendReport"></div></div>
    </section>
  </div>
</div>

<script>
  const API_LAP = './api/lapkeu';
  const API_KODE = './api/kode/';
  const API_DATE = './api/date/';
  
  let rawDataResult = [];
  window.currentUser = { kode: '000' };
  let fetchTimer = null;
  let latestPayloadKey = '';
  let currentFinancialRows = [];
  let weeklyTrendState = { weeks: [], summary: {}, metric: 'aset_gabungan', showActual: true };

  const fmtNom = n => new Intl.NumberFormat('id-ID').format(Math.round(Number(n||0)));
  const asetGabunganCodes = ['101','102','103','104','105','10601','10602','10604','10605','10606','107','108','109','110','11102','112','113','116','117','118','119','120','121'];

  const fmtSingkat = (n) => {
      let num = Math.abs(Number(n) || 0);
      let sign = n < 0 ? '-' : '';
      if (num >= 1e12) return sign + (num / 1e12).toFixed(2).replace('.', ',') + ' T';
      if (num >= 1e9) return sign + (num / 1e9).toFixed(2).replace('.', ',') + ' M';
      if (num >= 1e6) return sign + (num / 1e6).toFixed(2).replace('.', ',') + ' Jt';
      return sign + new Intl.NumberFormat('id-ID').format(Math.round(num));
  };

  function getYesterdayDate() {
      const today = new Date();
      today.setDate(today.getDate() - 1); 
      const yyyy = today.getFullYear();
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const dd = String(today.getDate()).padStart(2, '0');
      return `${yyyy}-${mm}-${dd}`;
  }

  function getPreviousMonthEndDate(dateValue) {
      const base = dateValue ? new Date(`${dateValue}T00:00:00`) : new Date();
      if (Number.isNaN(base.getTime())) return '';
      return new Date(base.getFullYear(), base.getMonth(), 0).toISOString().slice(0, 10);
  }

  async function loadDefaultReportDates() {
      const fallbackHarian = getYesterdayDate();
      const fallbackClosing = getPreviousMonthEndDate(fallbackHarian);

      try {
          const res = await fetch(API_DATE, {
              method: 'GET',
              cache: 'no-store',
              headers: { 'Accept': 'application/json' }
          });
          if (!res.ok) throw new Error(`HTTP ${res.status}`);

          const json = await res.json();
          const data = json?.data || json || {};
          const harian = data.last_created || data.harian_date || data.actual_date || fallbackHarian;
          const closing = data.last_closing || data.closing_date || getPreviousMonthEndDate(harian) || fallbackClosing;

          return { closing, harian };
      } catch (error) {
          console.warn('Gagal mengambil tanggal default dari ./api/date/:', error);
          return { closing: fallbackClosing, harian: fallbackHarian };
      }
  }

  function scheduleFetchRekap(delay = 600) {
      const note = document.getElementById('autoApplyNote');
      if (fetchTimer) clearTimeout(fetchTimer);
      if (note) note.textContent = delay > 0 ? 'Menunggu...' : 'Auto apply';
      fetchTimer = setTimeout(() => {
          if (note) note.textContent = 'Auto apply';
          fetchRekap();
      }, delay);
  }

  function getReportViewKey(type) {
      if (type === 'tv_makro_summary') return 'macro';
      if (type === 'tren_makro_mingguan') return 'trend';
      return 'financial';
  }

  function setActiveReportView(type, animate = true) {
      const key = getReportViewKey(type);
      const views = {
          financial: document.getElementById('viewFinancial'),
          macro: document.getElementById('viewMacro'),
          trend: document.getElementById('viewTrend')
      };
      Object.entries(views).forEach(([name, element]) => {
          if (!element) return;
          const active = name === key;
          element.classList.toggle('hidden', !active);
          element.classList.remove('is-entering');
          if (active && animate) requestAnimationFrame(() => element.classList.add('is-entering'));
          if (active) {
              element.scrollTop = 0;
              const nested = element.querySelector('.custom-view-inner');
              if (nested) nested.scrollTop = 0;
          }
      });
      document.body.dataset.lapView = key;

      const labels = {
          'neraca detail kantor':'Neraca',
          'laba rugi detail kantor':'Laba Rugi',
          'tv_makro_summary':'Pusat Keputusan Ringkasan Makro',
          'tren_makro_mingguan':'Tren Mingguan'
      };
      const chip = document.getElementById('activeViewChip');
      if (chip) chip.textContent = labels[type] || 'Laporan';

      const exportBtn = document.getElementById('btnExportLapkeu');
      const canExport = key === 'financial';
      if (exportBtn) {
          exportBtn.disabled = !canExport;
          exportBtn.classList.toggle('opacity-40', !canExport);
          exportBtn.classList.toggle('cursor-not-allowed', !canExport);
          exportBtn.title = canExport ? 'Export Excel' : 'Export tersedia untuk Neraca dan Laba Rugi';
      }
  }

  function handleReportTypeChange() {
      const type = document.getElementById('type_report')?.value || 'neraca detail kantor';
      setActiveReportView(type, true);
      scheduleFetchRekap(0);
  }

  window.addEventListener('DOMContentLoaded', async () => {
      const user = (window.getUser && window.getUser()) || null;
      const uKode = (user?.kode ? String(user.kode).padStart(3,'0') : '000');
      window.currentUser.kode = uKode;

      document.getElementById('badgeUnit').innerText = (uKode === '000') ? 'KONSOLIDASI PUSAT' : `CABANG ${uKode}`;

      const kantorPromise = populateKantorOptionsFP(uKode);
      const datesPromise = loadDefaultReportDates();
      const [, defaultDates] = await Promise.all([kantorPromise, datesPromise]);

      const harianEl = document.getElementById('harian_date');
      const closingEl = document.getElementById('closing_date');
      if (harianEl) harianEl.value = defaultDates.harian;
      if (closingEl) {
          closingEl.value = defaultDates.closing;
          delete closingEl.dataset.userChanged;
      }

      document.getElementById('type_report')?.addEventListener('change', handleReportTypeChange);
      document.getElementById('harian_date')?.addEventListener('change', () => {
          const closingEl = document.getElementById('closing_date');
          if (closingEl && !closingEl.dataset.userChanged) {
              closingEl.value = getPreviousMonthEndDate(document.getElementById('harian_date').value);
          }
          scheduleFetchRekap(350);
      });
      document.getElementById('closing_date')?.addEventListener('change', () => {
          document.getElementById('closing_date').dataset.userChanged = '1';
          scheduleFetchRekap(350);
      });
      ['opt_kantor_rec'].forEach(id => {
          document.getElementById(id)?.addEventListener('change', () => scheduleFetchRekap(350));
      });
      setActiveReportView(document.getElementById('type_report')?.value || 'neraca detail kantor', false);
      fetchRekap();
  });

  async function populateKantorOptionsFP(userKode) {
      const optKantor = document.getElementById('opt_kantor_rec');
      
      if (userKode && userKode !== '000') {
          try {
              const res = await fetch(API_KODE, { 
                  method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) 
              });
              const j = await res.json();
              const myKantor = (j.data || []).find(k => String(k.kode_kantor).padStart(3,'0') === userKode);
              const nama = myKantor ? myKantor.nama_kantor : `CABANG ${userKode}`;
              optKantor.innerHTML = `<option value="${userKode}">${userKode} - ${nama}</option>`;
          } catch(e) {
              optKantor.innerHTML = `<option value="${userKode}">${userKode} - CABANG ${userKode}</option>`;
          }
          optKantor.value = userKode;
          optKantor.disabled = true;
          return; 
      }
      
      try {
          const res = await fetch(API_KODE, { 
              method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) 
          });
          const json = await res.json();
          let list = Array.isArray(json.data) ? json.data : [];
          
          let html = `<option value="konsolidasi">KONSOLIDASI (SEMUA)</option>`;
          html += `<option value="000">000 - PUSAT</option>`;
          
          list.filter(x => x.kode_kantor && x.kode_kantor !== '000')
              .sort((a,b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor)))
              .forEach(it => {
                  const code = String(it.kode_kantor).padStart(3,'0');
                  const nama = it.nama_kantor || `CABANG ${code}`;
                  html += `<option value="${code}">${code} - ${nama}</option>`;
              });
              
          optKantor.innerHTML = html;
          optKantor.disabled = false;
      } catch(e) {
          optKantor.innerHTML = `<option value="konsolidasi">KONSOLIDASI (SEMUA)</option><option value="000">000 - PUSAT</option>`;
          optKantor.disabled = false;
      }
  }

  async function fetchRekap() {
      const loader = document.getElementById('loadingFP');
      const tbody = document.getElementById('lapBody');
      loader.classList.remove('hidden');
      tbody.innerHTML = '';
      const macroPanel = document.getElementById('macroReport');
      const trendPanel = document.getElementById('trendReport');
      if (macroPanel) macroPanel.innerHTML = '';
      if (trendPanel) trendPanel.innerHTML = '';

      const payload = {
          type: document.getElementById('type_report').value,
          kode_kantor: document.getElementById('opt_kantor_rec').value,
          harian_date: document.getElementById('harian_date').value,
          closing_date: document.getElementById('closing_date')?.value || getPreviousMonthEndDate(document.getElementById('harian_date').value)
      };

      setActiveReportView(payload.type, false);

      // Judul aplikasi tetap satu. Nama laporan aktif ditampilkan pada chip dan workspace masing-masing.

      const payloadKey = JSON.stringify(payload);
      latestPayloadKey = payloadKey;

      try {
          const res = await fetch(API_LAP, {
              method: 'POST',
              headers: {'Content-Type':'application/json'},
              body: JSON.stringify(payload)
          });
          const json = await res.json();
          if (payloadKey !== latestPayloadKey) return;
          rawDataResult = json.data || [];

          if (payload.type === 'tv_makro_summary') {
              renderMakroSummary(rawDataResult);
          } else if (payload.type === 'tren_makro_mingguan') {
              renderTrenMingguan(rawDataResult);
          } else {
              renderTable(rawDataResult);
              renderSummary(rawDataResult, payload.type);
          }
      } catch (e) {
          console.error(e);
          tbody.innerHTML = `<tr><td colspan="6" class="text-center p-10 text-red-500 font-bold text-xs">Gagal memuat data laporan!</td></tr>`;
      } finally {
          loader.classList.add('hidden');
      }
  }


  /* === HIERARKI AKUN: parent terdekat + pre-order === */
  const financialCodeCollator = new Intl.Collator('id-ID', { numeric:true, sensitivity:'base' });
  let financialHierarchyMeta = new Map();

  function compareFinancialCode(a, b) {
    const aa = String(a ?? '').trim();
    const bb = String(b ?? '').trim();
    if (!aa && bb) return 1;
    if (aa && !bb) return -1;
    return financialCodeCollator.compare(aa, bb);
  }

  function buildFinancialHierarchy(rows) {
    const source = Array.isArray(rows) ? rows : [];
    const indexed = source.map((row, index) => ({
      row,
      index,
      code:String(row?.kode_perk ?? '').trim()
    }));

    const codeSet = new Set(indexed.filter(item => item.code).map(item => item.code));
    const rowByCode = new Map();
    indexed.forEach(item => {
      if (item.code && !rowByCode.has(item.code)) rowByCode.set(item.code, item);
    });

    const parentByCode = new Map();
    codeSet.forEach(code => {
      let parentCode = '';
      for (let length = code.length - 1; length >= 1; length--) {
        const candidate = code.slice(0, length);
        if (codeSet.has(candidate)) {
          parentCode = candidate;
          break;
        }
      }
      parentByCode.set(code, parentCode);
    });

    const childrenByCode = new Map();
    codeSet.forEach(code => childrenByCode.set(code, []));
    const roots = [];
    codeSet.forEach(code => {
      const parentCode = parentByCode.get(code) || '';
      if (parentCode && childrenByCode.has(parentCode)) childrenByCode.get(parentCode).push(code);
      else roots.push(code);
    });

    roots.sort(compareFinancialCode);
    childrenByCode.forEach(children => children.sort(compareFinancialCode));

    financialHierarchyMeta = new Map();
    const ordered = [];
    const visited = new Set();

    function walk(code, depth = 0, ancestors = []) {
      if (visited.has(code)) return;
      visited.add(code);
      const item = rowByCode.get(code);
      if (!item) return;

      const parentCode = parentByCode.get(code) || '';
      const children = childrenByCode.get(code) || [];
      const path = [...ancestors, code];
      financialHierarchyMeta.set(code, {
        parentCode,
        depth,
        path,
        hasChildren:children.length > 0,
        children:[...children]
      });
      ordered.push(item.row);
      children.forEach(child => walk(child, depth + 1, path));
    }

    roots.forEach(root => walk(root, 0, []));

    // Baris tanpa kode atau duplikat kode tetap dipertahankan di bagian akhir.
    indexed.forEach(item => {
      if (!item.code || rowByCode.get(item.code)?.index !== item.index) ordered.push(item.row);
    });

    return ordered;
  }

  function financialRowMap() {
    return new Map(
      [...document.querySelectorAll('#lapBody tr.financial-row')]
        .map(row => [row.dataset.kode || '', row])
        .filter(([code]) => code)
    );
  }

  function isFinancialRowVisibleByExpansion(row, rowMap) {
    let parentCode = row.dataset.parentCode || '';
    const visited = new Set();

    while (parentCode) {
      if (visited.has(parentCode)) return false;
      visited.add(parentCode);
      const parentRow = rowMap.get(parentCode);
      if (!parentRow) return true;
      const icon = parentRow.querySelector('.caret');
      if (!icon?.classList.contains('rotate')) return false;
      parentCode = parentRow.dataset.parentCode || '';
    }
    return true;
  }

  function syncFinancialHierarchyVisibility() {
    const rows = [...document.querySelectorAll('#lapBody tr.financial-row')];
    const rowMap = financialRowMap();
    rows.forEach(row => {
      row.classList.toggle('hidden-row', !isFinancialRowVisibleByExpansion(row, rowMap));
    });
  }

  function financialAncestorCodes(row, rowMap) {
    const result = [];
    let parentCode = row?.dataset?.parentCode || '';
    const visited = new Set();
    while (parentCode && !visited.has(parentCode)) {
      visited.add(parentCode);
      result.push(parentCode);
      parentCode = rowMap.get(parentCode)?.dataset?.parentCode || '';
    }
    return result;
  }

  function collapseFinancialDescendants(parentRow, allRows) {
    const parentPath = parentRow?.dataset?.treePath || '';
    if (!parentPath) return;
    allRows.forEach(row => {
      const path = row.dataset.treePath || '';
      if (path.startsWith(`${parentPath}>`)) {
        row.querySelector('.caret')?.classList.remove('rotate');
      }
    });
  }

  function renderTable(data) {
    const tbody = document.getElementById('lapBody');
    const reportType = document.getElementById('type_report')?.value || '';
    const isNeraca = reportType.includes('neraca');

    // API kadang mengirim parent dahulu (1,2,3) lalu detail (101,102,...).
    // Susun ulang menjadi pre-order agar breakdown selalu tepat di bawah parent.
    currentFinancialRows = buildFinancialHierarchy(Array.isArray(data) ? data : []);

    const title = isNeraca ? 'Neraca Keuangan' : 'Laporan Laba Rugi';
    const toolbarTitle = document.getElementById('financialToolbarTitle');
    const toolbarMeta = document.getElementById('financialToolbarMeta');
    const search = document.getElementById('financialSearch');
    if (toolbarTitle) toolbarTitle.textContent = title;
    if (toolbarMeta) toolbarMeta.innerHTML = `${currentFinancialRows.length} akun · Klik kelompok untuk membuka breakdown <span class="financial-tree-hint">Parent → Sub Akun</span>`;
    if (search) search.value = '';

    tbody.innerHTML = currentFinancialRows.map((d, index) => {
      const kode = String(d?.kode_perk ?? '').trim();
      const namaRaw = String(d?.nama_perkiraan || '-');
      const nama = safeText(namaRaw);
      const meta = financialHierarchyMeta.get(kode) || {
        parentCode:'', depth:0, path:kode ? [kode] : [], hasChildren:false, children:[]
      };
      const depth = Math.max(0, Number(meta.depth || 0));
      const parent = Boolean(meta.hasChildren);
      const levelClass = depth === 0
        ? 'financial-level-1'
        : (depth === 1 ? 'financial-level-2' : (depth === 2 ? 'financial-level-3' : 'financial-level-detail'));
      const hiddenClass = ''; // Default OPEN: seluruh breakdown langsung tampil
      const indent = Math.min(64, depth * (window.innerWidth < 768 ? 10 : 14));
      const total = Number(d?.total_saldo || 0);
      const closing = Number(d?.closing_saldo || 0);
      const selisih = Number(d?.selisih_saldo ?? (total - closing));
      const growth = Number(d?.growth_persen ?? growthValue(total, closing) ?? 0);
      const amountClass = total < 0 ? 'is-negative' : '';
      const closingClass = closing < 0 ? 'is-negative' : '';
      const diffClass = selisih > 0 ? 'is-positive' : (selisih < 0 ? 'is-negative' : '');
      const pctClass = growth > 0 ? 'is-positive' : (growth < 0 ? 'is-negative' : 'is-flat');
      const parentIcon = parent
        ? '<span class="caret rotate" aria-hidden="true">▶</span>'
        : '<span class="caret" aria-hidden="true" style="visibility:hidden">▶</span>';
      const parentBadge = parent ? '<span class="financial-parent-badge">Kelompok</span>' : '';
      const treePath = (meta.path || [kode]).join('>');
      const rowCode = kode || `__row_${index}`;

      return `
        <tr class="financial-row ${levelClass} ${hiddenClass}"
            data-kode="${safeText(rowCode)}"
            data-display-kode="${safeText(kode)}"
            data-parent-code="${safeText(meta.parentCode || '')}"
            data-tree-path="${safeText(treePath)}"
            data-depth="${depth}"
            data-parent="${parent ? '1' : '0'}"
            data-search="${safeText(`${kode} ${namaRaw}`.toLowerCase())}"
            ${parent ? `onclick="toggleRow('${safeText(rowCode)}')" tabindex="0" role="button" aria-expanded="true" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();toggleRow('${safeText(rowCode)}')}"` : ''}>
          <td class="lap-code-col financial-code">${safeText(kode)}</td>
          <td>
            <div class="financial-name-wrap" style="padding-left:${indent}px">
              ${parentIcon}
              <span class="financial-mobile-code">${safeText(kode)}</span>
              <span class="financial-name" title="${nama}">${nama}</span>
              ${parentBadge}
            </div>
          </td>
          <td title="Rp ${fmtNom(total)}">
            <span class="financial-amount financial-amount-full ${amountClass}">${fmtNom(total)}</span>
            <span class="financial-amount financial-amount-short ${amountClass}">Rp ${fmtSingkat(total)}</span>
          </td>
          <td title="Rp ${fmtNom(closing)}">
            <span class="financial-amount financial-amount-full ${closingClass}">${fmtNom(closing)}</span>
            <span class="financial-amount financial-amount-short ${closingClass}">Rp ${fmtSingkat(closing)}</span>
          </td>
          <td title="${selisih >= 0 ? '+' : '-'} Rp ${fmtNom(Math.abs(selisih))}">
            <span class="financial-amount financial-amount-full ${diffClass}">${selisih >= 0 ? '+' : '-'} ${fmtNom(Math.abs(selisih))}</span>
            <span class="financial-amount financial-amount-short ${diffClass}">${signedMoney(selisih)}</span>
          </td>
          <td title="${pctText(growth)}">
            <span class="financial-pct ${pctClass}">${growth > 0 ? '+' : ''}${pctText(growth)}</span>
          </td>
        </tr>
      `;
    }).join('');

    const empty = document.getElementById('financialEmptyState');
    if (!currentFinancialRows.length) {
      if (empty) empty.style.display = 'block';
    } else {
      if (empty) empty.style.display = 'none';
      syncFinancialHierarchyVisibility();
    }

    const scroll = document.getElementById('financialTableScroll');
    if (scroll) {
      scroll.scrollTop = 0;
      scroll.scrollLeft = 0;
    }
  }

  window.setFinancialExpansion = function(expand) {
    const search = document.getElementById('financialSearch');
    if (search?.value.trim()) search.value = '';

    const rows = [...document.querySelectorAll('#lapBody tr.financial-row')];
    rows.forEach(row => {
      const icon = row.querySelector('.caret');
      if (icon && row.dataset.parent === '1') {
        icon.classList.toggle('rotate', Boolean(expand));
        row.setAttribute('aria-expanded', expand ? 'true' : 'false');
      }
    });

    syncFinancialHierarchyVisibility();
    const empty = document.getElementById('financialEmptyState');
    if (empty) empty.style.display = rows.length ? 'none' : 'block';
  };

  window.filterFinancialRows = function() {
    const input = document.getElementById('financialSearch');
    const query = String(input?.value || '').trim().toLowerCase();
    const rows = [...document.querySelectorAll('#lapBody tr.financial-row')];
    const rowMap = financialRowMap();
    const empty = document.getElementById('financialEmptyState');

    if (!query) {
      syncFinancialHierarchyVisibility();
      if (empty) empty.style.display = rows.length ? 'none' : 'block';
      return;
    }

    const visibleCodes = new Set();
    rows.forEach(row => {
      if ((row.dataset.search || '').includes(query)) {
        const code = row.dataset.kode || '';
        if (code) visibleCodes.add(code);
        financialAncestorCodes(row, rowMap).forEach(parentCode => visibleCodes.add(parentCode));
      }
    });

    let visible = 0;
    rows.forEach(row => {
      const show = visibleCodes.has(row.dataset.kode || '');
      row.classList.toggle('hidden-row', !show);
      if (show) visible += 1;
    });

    if (empty) empty.style.display = visible ? 'none' : 'block';
  };


  function pctText(value) {
    return `${Number(value || 0).toFixed(2).replace('.', ',')}%`;
  }


  function formatViewDate(value) {
    if (!value) return '-';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return String(value);
    return new Intl.DateTimeFormat('id-ID', { day:'2-digit', month:'short', year:'numeric' }).format(d);
  }

  function safeText(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function growthValue(current, previous) {
    const now = Number(current || 0);
    const prev = Number(previous || 0);
    if (!Number.isFinite(now) || !Number.isFinite(prev) || prev === 0) return null;
    return ((now - prev) / Math.abs(prev)) * 100;
  }

  function trendBadge(value, suffix = '') {
    if (value === null || value === undefined || !Number.isFinite(Number(value))) {
      return '<span class="trend-badge trend-flat">Posisi</span>';
    }
    const n = Number(value);
    const cls = n > 0 ? 'trend-up' : (n < 0 ? 'trend-down' : 'trend-flat');
    const icon = n > 0 ? '▲' : (n < 0 ? '▼' : '•');
    return `<span class="trend-badge ${cls}">${icon} ${pctText(Math.abs(n))}${suffix ? ` ${suffix}` : ''}</span>`;
  }

  function signedMoney(value) {
    const n = Number(value || 0);
    const prefix = n > 0 ? '+' : (n < 0 ? '-' : '');
    return `${prefix}Rp ${fmtSingkat(Math.abs(n))}`;
  }

  function deltaClass(value) {
    const n = Number(value || 0);
    if (n > 0) return 'up';
    if (n < 0) return 'down';
    return 'flat';
  }

  function overviewIcon(tone) {
    const icons = {
      blue: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V9m6 10V5m6 14v-7m4 7H2"/>',
      green: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l6-6 4 4 8-9M14 6h7v7"/>',
      purple: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h10"/>',
      orange: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18m6-14H9a3 3 0 000 6h6a3 3 0 010 6H6"/>',
      red: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.9L2.7 17.2A2 2 0 004.4 20h15.2a2 2 0 001.7-2.8L13.7 3.9a2 2 0 00-3.4 0z"/>'
    };
    return `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">${icons[tone] || icons.blue}</svg>`;
  }

  function overviewCard(label, value, growth, tone = 'blue', suffix = '', footText = 'Posisi aktual') {
    return `
      <article class="overview-card overview-${tone}" title="Rp ${fmtNom(value)}">
        <div class="overview-card-head">
          <div class="overview-card-label">${safeText(label)}</div>
          <span class="overview-card-icon">${overviewIcon(tone)}</span>
        </div>
        <div class="overview-card-value">Rp ${fmtSingkat(value)}</div>
        <div class="overview-card-foot">
          <span>${safeText(footText)}</span>
          ${trendBadge(growth, suffix)}
        </div>
      </article>
    `;
  }

  function reportHero(title, subtitle, iconPath) {
    const position = document.getElementById('harian_date')?.value || '';
    return `
      <header class="report-hero">
        <div class="report-hero-main">
          <span class="report-hero-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="${iconPath}"/></svg>
          </span>
          <div class="min-w-0">
            <div class="report-hero-title">${safeText(title)}</div>
            <div class="report-hero-sub">${safeText(subtitle)}</div>
          </div>
        </div>
        <span class="report-position-badge">● Posisi ${formatViewDate(position)}</span>
      </header>
    `;
  }

  function sectionHeading(kicker, title, meta = '') {
    return `
      <div class="section-heading">
        <div class="section-heading-main">
          <div class="section-kicker">${safeText(kicker)}</div>
          <div class="section-title">${safeText(title)}</div>
        </div>
        ${meta ? `<span class="section-meta">${safeText(meta)}</span>` : ''}
      </div>
    `;
  }

  function trendPill(value, suffix = 'MoM') {
    const n = Number(value || 0);
    const cls = n >= 0 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-red-700 bg-red-50 border-red-200';
    return `<span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-black ${cls}">${n >= 0 ? '▲' : '▼'} ${pctText(Math.abs(n))} ${suffix}</span>`;
  }

  function metricCard(label, value, prev, growth, tone, suffix = 'MoM') {
    const color = {
      blue: 'metric-blue text-sky-800',
      green: 'metric-green text-emerald-800',
      purple: 'metric-purple text-violet-800',
      orange: 'metric-orange text-orange-800',
    }[tone] || 'metric-blue text-sky-800';

    return `
      <div class="metric-card ${color}">
        <div class="label">${label}</div>
        <div class="value">Rp ${fmtSingkat(value)}</div>
        <div class="sub">
          <span>${prev === null || prev === undefined ? 'Posisi aktual' : `M-1 Rp ${fmtSingkat(prev)}`}</span>
          ${growth === null || growth === undefined ? '' : trendPill(growth, suffix)}
        </div>
      </div>
    `;
  }

  function insightList(items) {
    const clean = (items || []).filter(Boolean);
    return clean.length
      ? `<ul>${clean.map(item => `<li>${safeText(item)}</li>`).join('')}</ul>`
      : '<ul><li>Data belum cukup untuk membentuk analisis otomatis.</li></ul>';
  }

  function insightBlock(title, itemsOrHtml, className = '') {
    const content = Array.isArray(itemsOrHtml) ? insightList(itemsOrHtml) : String(itemsOrHtml || '');
    return `<section class="lapkeu-insight-block ${className}"><h4>${safeText(title)}</h4>${content}</section>`;
  }

  function insightSignal(label, tone = 'info') {
    return `<span class="insight-signal ${tone}">${safeText(label)}</span>`;
  }

  function insightStat(label, value, note, tone = 'info') {
    return `
      <article class="lapkeu-insight-stat ${tone}">
        <div class="lapkeu-insight-stat-label">${safeText(label)}</div>
        <div class="lapkeu-insight-stat-value" title="${safeText(value)}">${safeText(value)}</div>
        <div class="lapkeu-insight-stat-note">${safeText(note || '')}</div>
      </article>
    `;
  }


  function decisionField(label, value, full = false, strong = false) {
    return `
      <div class="decision-field ${full ? 'full' : ''}">
        <div class="decision-field-label">${safeText(label)}</div>
        <div class="decision-field-value ${strong ? 'strong' : ''}">${safeText(value || '-')}</div>
      </div>
    `;
  }

  function decisionCard(item, index) {
    const tone = ['bad','warn','good','info'].includes(item?.tone) ? item.tone : 'info';
    const priority = item?.priority || (tone === 'bad' ? 'P1' : (tone === 'warn' ? 'P2' : 'P3'));
    return `
      <article class="decision-card ${tone}">
        <div class="decision-card-head">
          <div class="decision-card-title">${safeText(item?.title || `Keputusan ${index + 1}`)}</div>
          <span class="decision-priority">${safeText(priority)}</span>
        </div>
        <div class="decision-card-body">
          <div class="decision-finding"><b>Temuan:</b> ${safeText(item?.finding || 'Perlu validasi lebih lanjut dari unit terkait.')}</div>
          <div class="decision-fields">
            ${decisionField('Keputusan Kepala Cabang', item?.decision, true, true)}
            ${decisionField('PIC Utama', item?.owner)}
            ${decisionField('Batas Waktu', item?.due)}
            ${decisionField('Indikator Berhasil', item?.kpi, true)}
          </div>
        </div>
      </article>
    `;
  }

  function decisionPanel(items, contextNote = '') {
    const clean = (Array.isArray(items) ? items : []).filter(Boolean).slice(0, 6);
    if (!clean.length) {
      clean.push({
        priority:'P3', tone:'good', title:'Pertahankan Kondisi dan Cegah Pemburukan',
        finding:'Belum ditemukan sinyal material negatif dari data yang tersedia.',
        decision:'Pertahankan ritme monitoring harian dan fokus pada pencapaian target RBB/RKAP tanpa menurunkan kualitas.',
        owner:'Kepala Cabang dan seluruh pemilik indikator',
        due:'Evaluasi mingguan',
        kpi:'Tidak ada pemburukan material dan target berjalan sesuai rencana.'
      });
    }
    const p1 = clean.filter(item => item.priority === 'P1' || item.tone === 'bad').length;
    const p2 = clean.filter(item => item.priority === 'P2' || item.tone === 'warn').length;
    const stable = clean.length - p1 - p2;
    return `
      <section class="lapkeu-decision-center">
        <div class="lapkeu-decision-head">
          <div class="lapkeu-decision-head-main">
            <div class="lapkeu-decision-eyebrow">Rekomendasi Operasional</div>
            <div class="lapkeu-decision-title">Keputusan yang Perlu Ditetapkan Kepala Cabang</div>
            <div class="lapkeu-decision-caption">Urutan P1–P3 menunjukkan prioritas. Setiap rekomendasi sudah dilengkapi keputusan, PIC, batas waktu, dan indikator hasil agar dapat langsung dibawa ke rapat cabang.</div>
          </div>
          <div class="lapkeu-decision-counts">
            ${p1 ? `<span class="decision-count bad">${p1} Prioritas Utama</span>` : ''}
            ${p2 ? `<span class="decision-count warn">${p2} Perlu Perhatian</span>` : ''}
            ${stable ? `<span class="decision-count good">${stable} Penguatan</span>` : ''}
          </div>
        </div>
        <div class="lapkeu-decision-grid">${clean.map(decisionCard).join('')}</div>
        <div class="decision-footer-note"><b>Catatan keputusan:</b> ${safeText(contextNote || 'Sesuaikan target nominal dengan RBB/RKAP, kewenangan cabang, kondisi nasabah, dan ketentuan internal. Rekomendasi otomatis berfungsi sebagai bahan prioritas, bukan pengganti keputusan pejabat berwenang.')}</div>
      </section>
    `;
  }

  function decisionQuestions(items) {
    const clean = (items || []).filter(Boolean);
    return `<div class="decision-question-grid">${clean.map((item,index) => `
      <div class="decision-question"><span class="decision-question-num">${index + 1}</span><span>${safeText(item)}</span></div>
    `).join('')}</div>`;
  }

  function buildMakroDecisionItems({ nplPct, nplAssess, bopo, bopoAssess, ldr, ldrAssess, casa, labaGrowth }) {
    const actions = [];
    if (nplAssess?.tone !== 'good') actions.push({
      priority:'P1', tone:'bad', title:'Kendalikan NPL dan Cegah Flow Baru',
      finding:`NPL Gross berada pada ${pctText(nplPct)} dengan status ${nplAssess?.label || 'perlu perhatian'}.`,
      decision:'Tetapkan daftar debitur prioritas per AO, validasi komitmen pembayaran, dan lakukan review harian flow, migrasi, serta recovery.',
      owner:'Kepala Cabang, Kabid Pemasaran, AO/Remedial',
      due:'Mulai hari ini; evaluasi harian',
      kpi:'Seluruh kandidat berisiko memiliki PIC dan komitmen; realisasi pembayaran termonitor; flow baru tertahan.'
    });
    if (bopoAssess?.tone !== 'good') actions.push({
      priority:bopoAssess?.tone === 'bad' ? 'P1' : 'P2', tone:bopoAssess?.tone === 'bad' ? 'bad' : 'warn', title:'Kendalikan BOPO dan Top Beban',
      finding:`BOPO berada pada ${pctText(bopo)} dengan status ${bopoAssess?.label || 'perlu kontrol'}.`,
      decision:'Tetapkan Top 5 beban yang harus dikendalikan, bedakan biaya wajib dan biaya yang dapat ditunda, lalu hubungkan setiap biaya dengan kontribusi pendapatan.',
      owner:'Kepala Cabang, Operasional, dan pemilik pos biaya',
      due:'Rencana aksi maksimal 3 hari kerja',
      kpi:'Setiap Top 5 beban memiliki alasan, PIC, target pengendalian, dan hasil evaluasi berikutnya.'
    });
    if (ldrAssess?.tone !== 'good') {
      const low = Number(ldr) < 75;
      actions.push({
        priority:'P2', tone:'warn', title:low ? 'Produktifkan Dana melalui Kredit Berkualitas' : 'Jaga Likuiditas dan Perkuat DPK',
        finding:`LDR berada pada ${pctText(ldr)} dan dinilai ${ldrAssess?.label || 'perlu dijaga'}.`,
        decision:low
          ? 'Review pipeline kredit yang layak, percepat proses yang sudah lengkap, dan hindari pencairan yang mengorbankan kualitas.'
          : 'Amankan deposan utama, perkuat pipeline DPK, dan siapkan proyeksi kebutuhan likuiditas harian.',
        owner:'Kepala Cabang, Kabid Pemasaran, AO Kredit/AO Dana',
        due:'Review pipeline mingguan',
        kpi:low ? 'Pipeline layak memiliki jadwal pencairan dan kualitas kolektibilitas terjaga.' : 'Kebutuhan likuiditas tercukupi dan risiko penarikan deposan besar termonitor.'
      });
    }
    if (ratioAssessment('casa', casa).tone !== 'good') actions.push({
      priority:'P2', tone:'warn', title:'Naikkan CASA dan Kurangi Ketergantungan Dana Mahal',
      finding:`CASA berada pada ${pctText(casa)} dan belum pada kategori kuat.`,
      decision:'Tetapkan target tabungan aktif per petugas, aktivasi rekening pasif, dan fokus pada transaksi payroll, komunitas, serta ekosistem debitur.',
      owner:'Kepala Cabang, AO Dana, dan seluruh pegawai pemilik prospek',
      due:'Target dan daftar prospek ditetapkan minggu ini',
      kpi:'Tabungan aktif bertambah, komposisi dana murah membaik, dan ketergantungan deposito mahal menurun.'
    });
    if (Number(labaGrowth) < 0) actions.push({
      priority:'P1', tone:'bad', title:'Pulihkan Laba Berjalan',
      finding:`Laba turun ${pctText(Math.abs(Number(labaGrowth)))} dibanding bulan lalu.`,
      decision:'Bedah penurunan laba menjadi pendapatan bunga, biaya dana, pendapatan nonbunga, CKPN, dan beban operasional; tetapkan dua pengungkit pendapatan serta dua pengendalian biaya.',
      owner:'Kepala Cabang bersama Pemasaran dan Operasional',
      due:'Analisis penyebab maksimal 2 hari kerja',
      kpi:'Penyebab penurunan terukur, pemilik aksi ditetapkan, dan proyeksi laba akhir bulan diperbarui.'
    });
    return actions;
  }

  function financialActionForRow(row, isIncome) {
    if (!row) return null;
    const name = String(row.name || '').toLowerCase();
    const down = Number(row.delta) < 0;
    const amount = signedMoney(Math.abs(Number(row.delta || 0)));
    if (isIncome) {
      if (name.includes('laba') && down) return {title:'Pulihkan Laba', decision:'Bedah pendapatan, biaya dana, CKPN, beban operasional, dan transaksi nonrutin; tetapkan pengungkit pemulihan laba.', owner:'Kepala Cabang, Pemasaran, Operasional', kpi:'Proyeksi laba diperbarui dan gap terhadap target memiliki rencana penutupan.'};
      if (name.includes('pendapatan') && down) return {title:'Pulihkan Pendapatan', decision:'Review volume kredit produktif, yield, penerimaan bunga, tunggakan bunga, dan pendapatan nonbunga.', owner:'Kepala Cabang dan tim pemasaran', kpi:'Sumber penurunan pendapatan teridentifikasi dan pipeline pemulihan tersedia.'};
      if ((name.includes('beban') || name.includes('biaya')) && !down) return {title:'Kendalikan Kenaikan Beban', decision:'Tentukan pos biaya utama, validasi kebutuhan, dan tetapkan batas pengeluaran serta PIC pengendalian.', owner:'Kepala Cabang dan Operasional', kpi:'Top beban memiliki target pengendalian dan tidak tumbuh tanpa dasar produktivitas.'};
    } else {
      if (name.includes('kredit') && down) return {title:'Tutup Gap Kredit', decision:'Pisahkan run off, pelunasan, hapus buku, dan realisasi; prioritaskan pipeline layak dengan jadwal pencairan yang realistis.', owner:'Kepala Cabang dan AO Kredit', kpi:'Gap kredit memiliki sumber jelas dan pipeline pencairan terukur.'};
      if ((name.includes('tabungan') || name.includes('deposito')) && down) return {title:'Pulihkan Dana Masyarakat', decision:'Identifikasi penurunan deposan utama, lakukan retensi, dan tetapkan pipeline dana baru dengan fokus dana murah.', owner:'Kepala Cabang dan AO Dana', kpi:'Nasabah retensi memiliki status tindak lanjut dan pipeline DPK baru tersedia.'};
      if (name.includes('kas') && down) return {title:'Jaga Kecukupan Likuiditas', decision:'Validasi kebutuhan kas, penarikan besar, dan posisi penempatan likuid agar operasional harian tetap aman.', owner:'Kepala Cabang dan Operasional/Treasury', kpi:'Proyeksi kas harian tersedia dan kebutuhan penarikan utama tercukupi.'};
    }
    return {
      title:`Tindak Lanjut ${row.name}`,
      decision:'Validasi penyebab perubahan, tetapkan pemilik aksi, dan tentukan langkah koreksi atau penguatan berdasarkan akun turunannya.',
      owner:'Kepala Cabang dan unit pemilik akun',
      kpi:'Penyebab, PIC, dan target perbaikan tercatat dan dievaluasi.'
    };
  }

  function buildFinancialDecisionItems(rows, isIncome, topMoves) {
    const unfavorable = (topMoves || []).filter(row => {
      const goodUp = financialGoodWhenUp(row.name, isIncome);
      return row.delta !== 0 && ((row.delta > 0) !== goodUp);
    }).slice(0,4);
    return unfavorable.map((row,index) => {
      const action = financialActionForRow(row, isIncome);
      return {
        priority:index === 0 ? 'P1' : 'P2',
        tone:index === 0 ? 'bad' : 'warn',
        title:action.title,
        finding:`${row.name} ${row.delta > 0 ? 'naik' : 'turun'} ${signedMoney(Math.abs(row.delta))} (${pctText(Math.abs(row.growth))}) dibanding closing.`,
        decision:action.decision,
        owner:action.owner,
        due:index === 0 ? 'Analisis maksimal 2 hari kerja' : 'Tindak lanjut minggu berjalan',
        kpi:action.kpi
      };
    });
  }

  function buildTrendDecisionItems(metrics) {
    const actions = [];
    const byKey = key => metrics.find(item => item.key === key);
    const npl = byKey('npl');
    const laba = byKey('laba_net');
    const kredit = byKey('kredit_baki_debet');
    const dpk = byKey('dpk');
    if (npl?.deltaClosing > 0 || npl?.deltaWeekly > 0) actions.push({
      priority:'P1', tone:'bad', title:'Hentikan Kenaikan NPL Mingguan',
      finding:`NPL berubah ${signedMoney(npl.deltaClosing)} dari closing dan ${signedMoney(npl.deltaWeekly)} dari pekan sebelumnya.`,
      decision:'Buka daftar debitur penyumbang kenaikan, tetapkan komitmen, PIC, tanggal bayar, dan skenario recovery sebelum pekan berikutnya.',
      owner:'Kepala Cabang, AO/Remedial',
      due:'Review harian sampai pekan berikutnya',
      kpi:'Seluruh penyumbang kenaikan memiliki tindak lanjut dan realisasi pembayaran termonitor.'
    });
    if (laba?.deltaWeekly < 0) actions.push({
      priority:'P1', tone:'bad', title:'Pulihkan Penurunan Laba Pekanan',
      finding:`Laba turun ${signedMoney(Math.abs(laba.deltaWeekly))} dari pekan sebelumnya.`,
      decision:'Validasi perubahan pendapatan, biaya dana, beban, CKPN, dan transaksi satu kali; perbarui proyeksi akhir bulan.',
      owner:'Kepala Cabang, Pemasaran, Operasional',
      due:'Maksimal 2 hari kerja',
      kpi:'Penyebab penurunan dan rencana penutupan gap telah ditetapkan.'
    });
    if (kredit?.deltaClosing < 0) actions.push({
      priority:'P2', tone:'warn', title:'Percepat Pipeline Kredit Berkualitas',
      finding:`Kredit masih ${signedMoney(kredit.deltaClosing)} terhadap closing.`,
      decision:'Pisahkan run off dan realisasi baru; prioritaskan berkas layak serta tetapkan jadwal pencairan yang realistis.',
      owner:'Kepala Cabang dan AO Kredit',
      due:'Review pipeline dua kali seminggu',
      kpi:'Setiap pipeline memiliki status, kekurangan dokumen, dan jadwal keputusan/pencairan.'
    });
    if (dpk?.deltaClosing < 0) actions.push({
      priority:'P2', tone:'warn', title:'Pulihkan DPK dan Amankan Deposan Utama',
      finding:`DPK masih ${signedMoney(dpk.deltaClosing)} terhadap closing.`,
      decision:'Tetapkan daftar nasabah retensi, deposan besar berisiko keluar, dan pipeline dana baru per petugas.',
      owner:'Kepala Cabang dan AO Dana',
      due:'Kontak prioritas maksimal 3 hari kerja',
      kpi:'Seluruh deposan prioritas memiliki status retensi dan pipeline dana baru termonitor.'
    });
    return actions;
  }

  function insightTable(headers, rows) {
    if (!Array.isArray(rows) || !rows.length) return '<div class="insight-callout">Data belum tersedia untuk ditampilkan.</div>';

    const columnCount = Array.isArray(headers) ? headers.length : 0;
    const widthClass = columnCount >= 6
      ? 'insight-table-wide'
      : (columnCount >= 4 ? 'insight-table-medium' : 'insight-table-compact');

    return `
      <div class="insight-table-wrap custom-scrollbar ${widthClass}" tabindex="0" aria-label="Tabel analisis, dapat digeser horizontal bila diperlukan">
        <table class="insight-table">
          <thead><tr>${headers.map(h => `<th class="${h.num ? 'num' : ''}">${safeText(h.label)}</th>`).join('')}</tr></thead>
          <tbody>${rows.map(row => `<tr>${row.map((cell, index) => `<td class="${headers[index]?.num ? 'num' : ''}">${cell?.html ? cell.html : safeText(cell?.value ?? cell ?? '')}</td>`).join('')}</tr>`).join('')}</tbody>
        </table>
      </div>
    `;
  }

  function changeSentence(label, growth, goodWhenUp = true) {
    if (growth === null || growth === undefined || !Number.isFinite(Number(growth))) return '';
    const n = Number(growth);
    const direction = n > 0 ? 'naik' : (n < 0 ? 'turun' : 'stabil');
    const tone = n === 0 ? 'stabil' : ((n > 0) === goodWhenUp ? 'positif' : 'perlu perhatian');
    return `${label} ${direction}${n === 0 ? '' : ` ${pctText(Math.abs(n))}`} dibanding bulan lalu; status ${tone}.`;
  }

  function selectedOfficeLabel() {
    const el = document.getElementById('opt_kantor_rec');
    return el?.selectedOptions?.[0]?.textContent?.trim() || '-';
  }

  function ratioAssessment(key, value) {
    const n = Number(value || 0);
    const defs = {
      npl: {
        good: v => v <= 3,
        warn: v => v <= 5,
        goodText:'Terkendali', warnText:'Waspada', badText:'Prioritas',
        note:'Semakin rendah semakin baik. Fokus pada migrasi, flow, recovery, dan konsentrasi debitur bermasalah.'
      },
      bopo: {
        good: v => v <= 85,
        warn: v => v <= 95,
        goodText:'Efisien', warnText:'Perlu kontrol', badText:'Tidak efisien',
        note:'Semakin rendah semakin efisien. Telusuri pos beban terbesar dan produktivitas pendapatan.'
      },
      ldr: {
        good: v => v >= 75 && v <= 95,
        warn: v => (v >= 65 && v < 75) || (v > 95 && v <= 100),
        goodText:'Seimbang', warnText:'Perlu dijaga', badText:'Tidak seimbang',
        note:'Nilai rendah menunjukkan dana belum produktif; nilai terlalu tinggi meningkatkan tekanan likuiditas.'
      },
      casa: {
        good: v => v >= 50,
        warn: v => v >= 35,
        goodText:'Kuat', warnText:'Cukup', badText:'Perlu ditingkatkan',
        note:'CASA lebih tinggi membantu menurunkan biaya dana melalui komposisi tabungan yang lebih besar.'
      },
      roa: {
        good: v => v >= 1.5,
        warn: v => v >= .5,
        goodText:'Baik', warnText:'Terbatas', badText:'Rendah',
        note:'Menggambarkan kemampuan aset menghasilkan laba. Hubungkan dengan margin, biaya, dan kualitas kredit.'
      },
      roe: {
        good: v => v >= 10,
        warn: v => v >= 5,
        goodText:'Baik', warnText:'Terbatas', badText:'Rendah',
        note:'Menggambarkan tingkat pengembalian modal. Bandingkan dengan target RBB/RKAP.'
      },
      nim: {
        good: v => v >= 5,
        warn: v => v >= 3,
        goodText:'Kuat', warnText:'Cukup', badText:'Tipis',
        note:'Margin bunga bersih dipengaruhi yield kredit, biaya dana, dan kualitas aset produktif.'
      },
      cash: {
        good: v => v >= 10,
        warn: v => v >= 5,
        goodText:'Memadai', warnText:'Perlu dijaga', badText:'Rendah',
        note:'Pantau kecukupan kas dan penempatan likuid terhadap kebutuhan penarikan dana.'
      },
      liquid: {
        good: v => v >= 15,
        warn: v => v >= 10,
        goodText:'Memadai', warnText:'Cukup', badText:'Terbatas',
        note:'Aset likuid menjadi bantalan kebutuhan kas, tetapi nilai berlebih juga dapat menekan produktivitas.'
      }
    };
    const def = defs[key];
    if (!def) return { tone:'info', label:'Monitoring', note:'Bandingkan dengan target internal, RBB/RKAP, dan posisi historis.' };
    if (def.good(n)) return { tone:'good', label:def.goodText, note:def.note };
    if (def.warn(n)) return { tone:'warn', label:def.warnText, note:def.note };
    return { tone:'bad', label:def.badText, note:def.note };
  }

  function growthTone(value, goodWhenUp = true) {
    const n = Number(value || 0);
    if (!Number.isFinite(n) || n === 0) return 'info';
    return ((n > 0) === goodWhenUp) ? 'good' : 'bad';
  }

  function ratioRow(label, value, key) {
    const assessment = ratioAssessment(key, value);
    return [
      label,
      { value:pctText(value) },
      { html:insightSignal(assessment.label, assessment.tone) },
      assessment.note
    ];
  }

  function buildMakroInsight(data) {
    const makro = data?.makro || data?.keuangan || {};
    const rasio = data?.rasio || {};
    const detail = data?.ringkasan_detail || {};
    const biaya = Array.isArray(data?.top_5_biaya) ? data.top_5_biaya : [];

    const aset = Number(makro?.aset?.nominal_aktual || 0);
    const kredit = Number(detail?.kredit_diberikan?.baki_debet || 0);
    const dpk = Number(makro?.dpk?.nominal_aktual || detail?.dana_masyarakat?.total || 0);
    const nplNom = Number(makro?.npl?.nominal_aktual || detail?.ckpn?.npl || 0);
    const nplPct = Number(makro?.npl?.persen_aktual || 0);
    const laba = Number(makro?.laba_rugi?.nominal_aktual ?? makro?.laba?.nominal_aktual ?? detail?.laba_setelah_pajak ?? 0);
    const labaGrowth = makro?.laba_rugi?.growth_mom ?? makro?.laba?.growth_mom;
    const bopo = Number(rasio?.bopo_persen || 0);
    const ldr = Number(rasio?.ldr_persen || 0);
    const casa = Number(rasio?.casa_persen || 0);
    const roa = Number(rasio?.roa_persen || 0);
    const roe = Number(rasio?.roe_persen || 0);
    const nim = Number(rasio?.nim_persen || 0);
    const cashRatio = Number(rasio?.cash_ratio_persen || 0);
    const liquid = Number(rasio?.aset_likuid_persen || 0);

    const nplAssess = ratioAssessment('npl', nplPct);
    const bopoAssess = ratioAssessment('bopo', bopo);
    const ldrAssess = ratioAssessment('ldr', ldr);

    const summary = `
      <div class="lapkeu-insight-summary">
        ${insightStat('Aset Gabungan', `Rp ${fmtSingkat(aset)}`, changeSentence('Aset', makro?.aset?.growth_mom, true) || 'Posisi aset aktual', growthTone(makro?.aset?.growth_mom, true))}
        ${insightStat('Kredit', `Rp ${fmtSingkat(kredit)}`, `LDR ${pctText(ldr)} · ${ldrAssess.label}`, ldrAssess.tone)}
        ${insightStat('Dana Pihak Ketiga', `Rp ${fmtSingkat(dpk)}`, changeSentence('DPK', makro?.dpk?.growth_mom, true) || `CASA ${pctText(casa)}`, growthTone(makro?.dpk?.growth_mom, true))}
        ${insightStat('NPL Gross', `${pctText(nplPct)}`, `Nominal Rp ${fmtSingkat(nplNom)} · ${nplAssess.label}`, nplAssess.tone)}
        ${insightStat('Laba Berjalan', `Rp ${fmtSingkat(laba)}`, changeSentence('Laba', labaGrowth, true) || 'Posisi laba aktual', growthTone(labaGrowth, true))}
        ${insightStat('BOPO', pctText(bopo), bopoAssess.note, bopoAssess.tone)}
      </div>
    `;

    const ratioRows = [
      ratioRow('NPL Gross', nplPct, 'npl'),
      ratioRow('BOPO', bopo, 'bopo'),
      ratioRow('LDR', ldr, 'ldr'),
      ratioRow('CASA', casa, 'casa'),
      ratioRow('ROA', roa, 'roa'),
      ratioRow('ROE', roe, 'roe'),
      ratioRow('NIM', nim, 'nim'),
      ratioRow('Cash Ratio', cashRatio, 'cash'),
      ratioRow('Aset Likuid', liquid, 'liquid')
    ];

    const fundingRows = [
      ['Dana masyarakat', { value:`Rp ${fmtSingkat(detail?.dana_masyarakat?.total)}` }, 'Basis pendanaan utama'],
      ['Tabungan', { value:`Rp ${fmtSingkat(detail?.dana_masyarakat?.tabungan)}` }, `Kontributor CASA · ${pctText(casa)}`],
      ['Deposito', { value:`Rp ${fmtSingkat(detail?.dana_masyarakat?.deposito)}` }, 'Pantau biaya dana dan konsentrasi deposan'],
      ['Kredit baki debet', { value:`Rp ${fmtSingkat(detail?.kredit_diberikan?.baki_debet)}` }, `Produktivitas dana · LDR ${pctText(ldr)}`],
      ['Saldo bank/EAD', { value:`Rp ${fmtSingkat(detail?.kredit_diberikan?.saldo_bank_ead)}` }, 'Eksposur kredit untuk monitoring risiko']
    ];

    const riskRows = [
      ['OSC NPL', { value:`Rp ${fmtSingkat(detail?.ckpn?.npl ?? nplNom)}` }, { html:insightSignal(nplAssess.label, nplAssess.tone) }],
      ['CKPN Kredit', { value:`Rp ${fmtSingkat(detail?.ckpn?.ckpn_kredit)}` }, 'Cadangan kerugian kredit'],
      ['CKPN PPBL', { value:`Rp ${fmtSingkat(detail?.ckpn?.ckpn_ppbl)}` }, 'Cadangan aset produktif lainnya'],
      ['PPBL', { value:`Rp ${fmtSingkat(detail?.ppbl)}` }, 'Perlu dikaitkan dengan kualitas aset dan recovery']
    ];

    const profitRows = [
      ['Laba sebelum pajak', { value:`Rp ${fmtSingkat(detail?.laba_sebelum_pajak)}` }, 'Pantau capaian terhadap target bulanan'],
      ['Laba setelah pajak', { value:`Rp ${fmtSingkat(detail?.laba_setelah_pajak)}` }, 'Hasil bersih berjalan'],
      ['NIM', { value:pctText(nim) }, ratioAssessment('nim', nim).label],
      ['ROA', { value:pctText(roa) }, ratioAssessment('roa', roa).label],
      ['ROE', { value:pctText(roe) }, ratioAssessment('roe', roe).label],
      ['BOPO', { value:pctText(bopo) }, bopoAssess.label]
    ];

    const expenseRows = biaya.map((item, index) => [
      `${index + 1}. ${item?.nama || '-'}`,
      { value:`Rp ${fmtSingkat(item?.nominal)}` },
      item?.kode || '-'
    ]);

    const priorities = [];
    if (nplAssess.tone !== 'good') priorities.push(`Kualitas kredit: turunkan NPL ${pctText(nplPct)} melalui daftar debitur prioritas, pencegahan flow, recovery, dan evaluasi migrasi kolektibilitas.`);
    if (bopoAssess.tone !== 'good') priorities.push(`Efisiensi: BOPO ${pctText(bopo)} memerlukan kontrol Top 5 beban serta evaluasi produktivitas setiap pos biaya.`);
    if (ldrAssess.tone === 'bad' || ldrAssess.tone === 'warn') priorities.push(`Intermediasi dan likuiditas: LDR ${pctText(ldr)} perlu diseimbangkan melalui kredit berkualitas, retensi DPK, dan pengelolaan aset likuid.`);
    if (ratioAssessment('casa', casa).tone !== 'good') priorities.push(`Pendanaan: tingkatkan CASA dari ${pctText(casa)} melalui pertumbuhan tabungan aktif dan pengendalian ketergantungan deposito mahal.`);
    if (Number(labaGrowth) < 0) priorities.push(`Profitabilitas: laba turun ${pctText(Math.abs(Number(labaGrowth)))}; bedah pendapatan bunga, biaya dana, CKPN, dan beban operasional.`);
    if (!priorities.length) priorities.push('Kondisi utama relatif terkendali; fokus pada konsistensi pencapaian target, kualitas pertumbuhan, dan pencegahan pemburukan sampai akhir bulan.');

    const monitoring = [
      'Bandingkan seluruh angka dengan RBB/RKAP, posisi bulan lalu, dan tren mingguan; indikator warna pada dashboard adalah alat monitoring, bukan pengganti ketentuan internal maupun regulator.',
      'Pertumbuhan kredit harus dibaca bersama pertumbuhan DPK, LDR, NPL, CKPN, serta realisasi laba agar tidak terjadi pertumbuhan yang mengorbankan kualitas.',
      'DPK yang tumbuh tetapi CASA turun dapat menaikkan biaya dana; evaluasi komposisi tabungan versus deposito.',
      'Laba yang naik dengan BOPO atau NPL memburuk perlu ditelaah kualitas dan keberlanjutannya.',
      'Gunakan Top 5 beban untuk menetapkan pemilik aksi, target penghematan, tenggat, dan hasil evaluasi berikutnya.'
    ];

    const decisionItems = buildMakroDecisionItems({ nplPct, nplAssess, bopo, bopoAssess, ldr, ldrAssess, casa, labaGrowth });
    const decisionContext = 'Tetapkan keputusan berdasarkan target RBB/RKAP cabang. Untuk setiap keputusan, catat angka awal, target, PIC, tenggat, serta bukti tindak lanjut pada rapat monitoring berikutnya.';

    return summary + decisionPanel(decisionItems, decisionContext) + `<div class="lapkeu-insight-grid">
      ${insightBlock('Ringkasan Perubahan Utama', [
        changeSentence('Aset gabungan', makro?.aset?.growth_mom, true),
        changeSentence('DPK', makro?.dpk?.growth_mom, true),
        changeSentence('NPL nominal', makro?.npl?.growth_mom, false),
        changeSentence('Laba berjalan', labaGrowth, true),
        changeSentence('Beban', makro?.biaya?.growth_mom, false)
      ])}
      ${insightBlock('Rasio Kesehatan & Interpretasi', insightTable(
        [{label:'Rasio'}, {label:'Posisi',num:true}, {label:'Sinyal'}, {label:'Makna'}], ratioRows
      ), 'span-2')}
      ${insightBlock('Pendanaan, Kredit & Likuiditas', insightTable(
        [{label:'Pos'}, {label:'Nominal',num:true}, {label:'Catatan'}], fundingRows
      ), 'span-2')}
      ${insightBlock('Kualitas Aset & Cadangan', insightTable(
        [{label:'Pos'}, {label:'Nominal',num:true}, {label:'Sinyal'}], riskRows
      ))}
      ${insightBlock('Profitabilitas & Efisiensi', insightTable(
        [{label:'Indikator'}, {label:'Posisi',num:true}, {label:'Interpretasi'}], profitRows
      ))}
      ${insightBlock('Top Beban yang Perlu Dikendalikan', insightTable(
        [{label:'Pos Beban'}, {label:'Nominal',num:true}, {label:'Kode'}], expenseRows
      ), 'span-2')}
      ${insightBlock('Prioritas Manajemen Tambahan', priorities, 'full')}
      ${insightBlock('Pertanyaan yang Harus Dijawab dalam Rapat Cabang', decisionQuestions([
        'Indikator mana yang paling jauh dari target RBB/RKAP dan berapa gap nominalnya?',
        'Nasabah, akun, atau transaksi apa yang paling besar menyebabkan gap tersebut?',
        'Siapa PIC utama, tindakan apa yang dilakukan, dan kapan hasil pertama harus terlihat?',
        'Risiko apa yang dapat muncul apabila keputusan ditunda sampai akhir bulan?',
        'Data apa yang harus dilaporkan kembali pada rapat monitoring berikutnya?'
      ]), 'full')}
      ${insightBlock('Cara Membaca dan Agenda Monitoring', monitoring, 'full')}
    </div>`;
  }

  function normalizeFinancialRows(rows) {
    const source = Array.isArray(rows)
      ? rows
      : (Array.isArray(rows?.data) ? rows.data : (Array.isArray(rows?.rows) ? rows.rows : []));
    return source.map(row => {
      const current = Number(row?.total_saldo || 0);
      const closing = Number(row?.closing_saldo || 0);
      const delta = Number(row?.selisih_saldo ?? (current - closing));
      const growth = Number(row?.growth_persen ?? growthValue(current, closing) ?? 0);
      return {
        code:String(row?.kode_perk || ''),
        name:String(row?.nama_perkiraan || '-'),
        current, closing, delta, growth
      };
    });
  }

  function findFinancialRow(rows, keywords) {
    const terms = keywords.map(k => String(k).toLowerCase());
    return rows.find(row => terms.some(term => row.name.toLowerCase().includes(term))) || null;
  }

  function financialGoodWhenUp(name, isIncome) {
    const n = String(name || '').toLowerCase();
    const negativeTerms = ['beban','biaya','kerugian','cadangan','ckpn','npl','tunggakan','pajak'];
    if (negativeTerms.some(term => n.includes(term))) return false;
    if (isIncome) return ['pendapatan','laba','surplus','keuntungan'].some(term => n.includes(term));
    return !['kewajiban','hutang','pinjaman diterima'].some(term => n.includes(term));
  }

  function buildFinancialInsight(data, type) {
    const rows = normalizeFinancialRows(data);
    const isIncome = String(type).includes('laba rugi');
    const categoryOrder = isIncome ? ['4', '5'] : ['1', '2', '3'];
    const categoryRows = categoryOrder
      .map(code => rows.find(row => row.code === code))
      .filter(Boolean);
    const summaryRows = rows.filter(row => row.code.length <= 3 && Math.abs(row.delta) > 0);
    const analysisRows = summaryRows.length >= 4 ? summaryRows : rows.filter(row => Math.abs(row.delta) > 0);
    const topMoves = [...analysisRows].sort((a,b) => Math.abs(b.delta) - Math.abs(a.delta)).slice(0,8);
    const increases = rows.filter(r => r.delta > 0).length;
    const decreases = rows.filter(r => r.delta < 0).length;
    const largest = topMoves[0];

    const keyRows = isIncome ? [
      findFinancialRow(rows, ['pendapatan bunga']),
      findFinancialRow(rows, ['pendapatan operasional']),
      findFinancialRow(rows, ['beban bunga']),
      findFinancialRow(rows, ['beban tenaga kerja','beban pegawai']),
      findFinancialRow(rows, ['beban umum','beban administrasi']),
      findFinancialRow(rows, ['laba sebelum pajak']),
      findFinancialRow(rows, ['laba setelah pajak','laba bersih'])
    ] : [
      findFinancialRow(rows, ['jumlah aset','total aset','aset']),
      findFinancialRow(rows, ['kas']),
      findFinancialRow(rows, ['penempatan pada bank','bank lain']),
      findFinancialRow(rows, ['kredit yang diberikan','kredit diberikan']),
      findFinancialRow(rows, ['tabungan']),
      findFinancialRow(rows, ['deposito']),
      findFinancialRow(rows, ['modal','ekuitas']),
      findFinancialRow(rows, ['ckpn','cadangan kerugian'])
    ];
    const uniqueKeyRows = [...new Map(keyRows.filter(Boolean).map(row => [row.code || row.name, row])).values()];

    const summary = `
      <div class="lapkeu-insight-summary">
        ${insightStat('Jumlah Pos', fmtNom(rows.length), isIncome ? 'Akun laporan laba rugi yang dianalisis' : 'Akun neraca yang dianalisis', 'info')}
        ${insightStat('Pos Naik', fmtNom(increases), 'Dibanding posisi closing', increases >= decreases ? 'good' : 'info')}
        ${insightStat('Pos Turun', fmtNom(decreases), 'Dibanding posisi closing', decreases > increases ? 'warn' : 'info')}
        ${insightStat('Perubahan Terbesar', largest ? `Rp ${fmtSingkat(Math.abs(largest.delta))}` : 'Rp 0', largest ? largest.name : 'Belum ada perubahan', largest ? (financialGoodWhenUp(largest.name, isIncome) === (largest.delta > 0) ? 'good' : 'warn') : 'info')}
      </div>
    `;

    const categorySummary = categoryRows.map(row => {
      const goodUp = financialGoodWhenUp(row.name, isIncome);
      const tone = row.delta === 0 ? 'info' : (((row.delta > 0) === goodUp) ? 'good' : 'warn');
      const direction = row.delta > 0 ? 'Naik' : (row.delta < 0 ? 'Turun' : 'Tetap');
      return [
        `${row.code} · ${row.name}`,
        { value:`Rp ${fmtSingkat(row.current)}` },
        { value:`Rp ${fmtSingkat(row.closing)}` },
        { value:signedMoney(row.delta) },
        { html:insightSignal(`${direction} ${row.growth === null ? '' : pctText(Math.abs(row.growth))}`, tone) }
      ];
    });

    const moveRows = topMoves.map(row => {
      const goodUp = financialGoodWhenUp(row.name, isIncome);
      const tone = row.delta === 0 ? 'info' : (((row.delta > 0) === goodUp) ? 'good' : 'warn');
      const signal = row.delta > 0 ? 'Naik' : 'Turun';
      return [
        `${row.code} · ${row.name}`,
        { value:`Rp ${fmtSingkat(row.current)}` },
        { value:`Rp ${fmtSingkat(row.closing)}` },
        { value:signedMoney(row.delta) },
        { value:pctText(row.growth) },
        { html:insightSignal(signal, tone) }
      ];
    });

    const keyTableRows = uniqueKeyRows.map(row => {
      const goodUp = financialGoodWhenUp(row.name, isIncome);
      const tone = row.delta === 0 ? 'info' : (((row.delta > 0) === goodUp) ? 'good' : 'warn');
      return [
        `${row.code} · ${row.name}`,
        { value:`Rp ${fmtSingkat(row.current)}` },
        { value:signedMoney(row.delta) },
        { html:insightSignal(row.delta > 0 ? 'Naik' : (row.delta < 0 ? 'Turun' : 'Tetap'), tone) }
      ];
    });

    const priorities = [];
    topMoves.forEach(row => {
      const goodUp = financialGoodWhenUp(row.name, isIncome);
      const favorable = row.delta === 0 || ((row.delta > 0) === goodUp);
      if (!favorable && priorities.length < 5) {
        priorities.push(`${row.name}: ${row.delta > 0 ? 'naik' : 'turun'} ${signedMoney(Math.abs(row.delta))} (${pctText(Math.abs(row.growth))}). Telusuri penyebab, unit pemilik, dan rencana koreksi.`);
      }
    });

    if (isIncome) {
      const profit = findFinancialRow(rows, ['laba setelah pajak','laba bersih','laba sebelum pajak']);
      const income = findFinancialRow(rows, ['pendapatan bunga','pendapatan operasional']);
      const expense = findFinancialRow(rows, ['beban operasional','jumlah beban','total beban']);
      if (profit && profit.delta < 0) priorities.unshift(`Laba turun ${signedMoney(Math.abs(profit.delta))}; bedah pendapatan, biaya dana, CKPN, dan beban operasional terbesar.`);
      if (income && income.delta < 0) priorities.push('Pendapatan turun; evaluasi yield kredit, volume aset produktif, penerimaan bunga, dan pendapatan nonbunga.');
      if (expense && expense.delta > 0) priorities.push('Beban meningkat; tetapkan Top 5 biaya, pemilik aksi, target efisiensi, dan tenggat evaluasi.');
    } else {
      const credit = findFinancialRow(rows, ['kredit yang diberikan','kredit diberikan']);
      const savings = findFinancialRow(rows, ['tabungan']);
      const deposits = findFinancialRow(rows, ['deposito']);
      const cash = findFinancialRow(rows, ['kas']);
      if (credit && credit.delta < 0) priorities.unshift('Kredit menurun; pisahkan dampak run off, pelunasan, hapus buku, dan realisasi baru untuk memastikan pertumbuhan berkualitas.');
      if ((savings && savings.delta < 0) || (deposits && deposits.delta < 0)) priorities.push('Dana masyarakat menurun; prioritaskan retensi nasabah, pipeline penghimpunan, dan komposisi dana murah.');
      if (cash && cash.delta < 0) priorities.push('Kas turun; pastikan penurunan masih sejalan dengan kebutuhan likuiditas harian dan proyeksi penarikan dana.');
    }
    if (!priorities.length) priorities.push('Tidak ada sinyal material negatif pada pos ringkasan; pertahankan kualitas pertumbuhan dan pantau perubahan terbesar sampai akhir bulan.');

    const reading = isIncome ? [
      'Actual adalah posisi berjalan sampai tanggal harian; Closing adalah posisi akhir bulan sebelumnya.',
      'Pendapatan dan laba yang naik umumnya positif, sedangkan beban, biaya, CKPN, dan kerugian yang naik perlu dianalisis lebih lanjut.',
      'Perubahan laba harus dibaca bersama pendapatan bunga, biaya dana, pendapatan nonbunga, beban operasional, CKPN, dan pajak.',
      'Gunakan fitur buka kelompok untuk menelusuri akun turunan yang membentuk perubahan pada akun induk.'
    ] : [
      'Actual adalah posisi neraca berjalan; Closing adalah posisi akhir bulan sebelumnya.',
      'Pertumbuhan aset atau kredit tidak otomatis baik apabila DPK, likuiditas, NPL, atau modal memburuk.',
      'Penurunan tabungan/deposito perlu dipisahkan antara transaksi normal, deposan besar, dan perpindahan antarproduk.',
      'Perubahan akun induk dapat mengandung akun turunan; gunakan fitur buka kelompok untuk menemukan sumber perubahan.'
    ];

    const decisionItems = buildFinancialDecisionItems(rows, isIncome, topMoves);
    const decisionContext = isIncome
      ? 'Untuk keputusan laba rugi, pastikan setiap perbaikan menghubungkan perubahan pendapatan atau beban dengan dampaknya pada laba akhir bulan.'
      : 'Untuk keputusan neraca, baca perubahan kredit, DPK, kas, aset, dan modal secara terpadu agar pertumbuhan tidak mengganggu likuiditas maupun kualitas aset.';

    const conditionText = categoryRows.length
      ? `Ringkasan kategori utama: ${categoryRows.map(row => `${row.name} ${row.delta > 0 ? 'naik' : (row.delta < 0 ? 'turun' : 'tetap')} ${row.growth === null ? '' : pctText(Math.abs(row.growth))}`).join('; ')}.`
      : 'Belum ada kategori utama yang dapat diringkas dari data yang diterima.';

    return summary + `<div class="lapkeu-insight-condition"><b>Kondisi singkat:</b> ${safeText(conditionText)} ${largest ? `Perubahan nominal terbesar ada pada ${safeText(largest.name)} sebesar ${safeText(signedMoney(largest.delta))}.` : ''}</div>` + decisionPanel(decisionItems, decisionContext) + `<div class="lapkeu-insight-grid">
      ${insightBlock('Ringkasan Kategori Utama', insightTable(
        [{label:'Kategori'}, {label:'Actual',num:true}, {label:'Closing',num:true}, {label:'Selisih',num:true}, {label:'Perubahan'}], categorySummary
      ), 'full')}
      ${insightBlock('Perubahan Nominal Terbesar', insightTable(
        [{label:'Pos'}, {label:'Actual',num:true}, {label:'Closing',num:true}, {label:'Selisih',num:true}, {label:'Growth',num:true}, {label:'Sinyal'}], moveRows
      ), 'full')}
      ${insightBlock(isIncome ? 'Pos Pendapatan, Beban & Laba' : 'Pos Neraca Strategis', insightTable(
        [{label:'Pos'}, {label:'Actual',num:true}, {label:'Selisih',num:true}, {label:'Sinyal'}], keyTableRows
      ), 'span-2')}
      ${insightBlock('Kesimpulan Cepat', [
        `${increases} pos meningkat, ${decreases} pos menurun, dan ${rows.length - increases - decreases} pos relatif tetap dibanding closing.`,
        largest ? `Perubahan terbesar terdapat pada ${largest.name} sebesar ${signedMoney(largest.delta)}.` : 'Belum terdapat perubahan material.',
        'Prioritaskan analisis pada perubahan nominal terbesar, bukan hanya persentase terbesar, agar dampak finansial lebih relevan.'
      ])}
      ${insightBlock('Pos yang Perlu Ditingkatkan / Dikendalikan', priorities, 'full')}
      ${insightBlock('Pertanyaan Keputusan Kepala Cabang', decisionQuestions(isIncome ? [
        'Pos apa yang paling besar menekan laba dan apakah sifatnya berulang atau hanya satu kali?',
        'Pendapatan mana yang dapat dipulihkan paling cepat tanpa menurunkan kualitas kredit?',
        'Beban mana yang wajib, dapat dikendalikan, atau dapat ditunda?',
        'Berapa proyeksi laba akhir bulan setelah rencana perbaikan dijalankan?'
      ] : [
        'Apakah penurunan kredit disebabkan run off, pelunasan, hapus buku, atau kurangnya realisasi baru?',
        'Nasabah dana mana yang berisiko menarik dana dan siapa PIC retensinya?',
        'Apakah posisi kas dan aset likuid memadai untuk kebutuhan penarikan?',
        'Apakah pertumbuhan aset/kredit masih sejalan dengan DPK, kualitas kredit, dan modal?'
      ]), 'full')}
      ${insightBlock('Panduan Membaca Laporan', reading, 'full')}
    </div>`;
  }

  function buildTrendInsight(data) {
    const summary = data?.summary || {};
    const weeks = Array.isArray(data?.weeks) ? [...data.weeks] : [];
    weeks.sort((a,b) => new Date(a?.tanggal || 0) - new Date(b?.tanggal || 0));
    const configs = weeklyMetricConfig();
    const latestWeek = weeks.at(-1) || {};
    const previousWeek = weeks.length > 1 ? weeks.at(-2) : (summary?.previous_closing || {});
    const metrics = Object.entries(configs).map(([key, config]) => {
      const current = Number(latestWeek?.[key] ?? summary?.[key] ?? 0);
      const closing = Number(summary?.previous_closing?.[key] || 0);
      const previous = Number(previousWeek?.[key] || 0);
      const deltaClosing = current - closing;
      const deltaWeekly = current - previous;
      return {
        key, label:config.label, current, closing, previous,
        deltaClosing, deltaWeekly,
        growthClosing:growthValue(current, closing),
        growthWeekly:growthValue(current, previous)
      };
    });

    const rising = [...metrics].sort((a,b) => b.deltaClosing - a.deltaClosing).slice(0,3);
    const falling = [...metrics].sort((a,b) => a.deltaClosing - b.deltaClosing).slice(0,3);
    const npl = metrics.find(item => item.key === 'npl');
    const profit = metrics.find(item => item.key === 'laba_net');

    const summaryHtml = `
      <div class="lapkeu-insight-summary">
        ${insightStat('Periode Mingguan', `${weeks.length} pekan`, weeks.length ? `Terbaru ${formatViewDate(latestWeek?.tanggal || '')}` : 'Belum ada data', 'info')}
        ${insightStat('Aset Terbaru', `Rp ${fmtSingkat(metrics.find(m => m.key === 'aset_gabungan')?.current)}`, 'Dibanding closing bulan lalu', growthTone(metrics.find(m => m.key === 'aset_gabungan')?.growthClosing, true))}
        ${insightStat('Kredit Terbaru', `Rp ${fmtSingkat(metrics.find(m => m.key === 'kredit_baki_debet')?.current)}`, 'Pergerakan kualitas dan realisasi perlu dibaca bersama', growthTone(metrics.find(m => m.key === 'kredit_baki_debet')?.growthClosing, true))}
        ${insightStat('NPL Terbaru', `Rp ${fmtSingkat(npl?.current)}`, npl?.deltaClosing > 0 ? 'Naik dari closing · prioritas recovery' : 'Tidak naik dari closing', npl?.deltaClosing > 0 ? 'bad' : 'good')}
        ${insightStat('Laba Terbaru', `Rp ${fmtSingkat(profit?.current)}`, profit?.deltaWeekly < 0 ? 'Turun dari pekan sebelumnya' : 'Tidak turun dari pekan sebelumnya', profit?.deltaWeekly < 0 ? 'warn' : 'good')}
      </div>
    `;

    const metricRows = metrics.map(item => {
      const goodWhenUp = !['beban','npl'].includes(item.key);
      const tone = item.deltaClosing === 0 ? 'info' : (((item.deltaClosing > 0) === goodWhenUp) ? 'good' : 'warn');
      return [
        item.label,
        { value:`Rp ${fmtSingkat(item.current)}` },
        { value:`Rp ${fmtSingkat(item.closing)}` },
        { value:signedMoney(item.deltaClosing) },
        { value:item.growthClosing === null ? '-' : pctText(item.growthClosing) },
        { value:signedMoney(item.deltaWeekly) },
        { html:insightSignal(item.deltaClosing > 0 ? 'Naik' : (item.deltaClosing < 0 ? 'Turun' : 'Tetap'), tone) }
      ];
    });

    const priorities = [];
    if (npl?.deltaClosing > 0) priorities.push(`NPL naik ${signedMoney(npl.deltaClosing)} dari closing; lakukan breakdown debitur, migrasi kolektibilitas, flow, dan recovery per cabang/AO.`);
    if (profit?.deltaWeekly < 0) priorities.push(`Laba turun ${signedMoney(Math.abs(profit.deltaWeekly))} dari pekan sebelumnya; cek pendapatan, beban, CKPN, dan transaksi satu kali.`);
    const credit = metrics.find(m => m.key === 'kredit_baki_debet');
    const dpk = metrics.find(m => m.key === 'dpk');
    if (credit?.deltaClosing < 0) priorities.push('Kredit masih di bawah closing; bedakan run off/pelunasan dengan realisasi baru dan pastikan pipeline pencairan berkualitas.');
    if (dpk?.deltaClosing < 0) priorities.push('DPK masih di bawah closing; tetapkan nasabah retensi, pipeline dana baru, dan pemantauan deposan besar.');
    if (!priorities.length) priorities.push('Arah mingguan relatif mendukung; pertahankan momentum dan pantau indikator yang mulai melemah sebelum akhir bulan.');

    const decisionItems = buildTrendDecisionItems(metrics);
    const decisionContext = 'Gunakan tren mingguan sebagai sistem peringatan dini. Keputusan tidak cukup hanya mencatat naik/turun; harus ada pemilik aksi dan hasil yang diperiksa pada pekan berikutnya.';

    return summaryHtml + decisionPanel(decisionItems, decisionContext) + `<div class="lapkeu-insight-grid">
      ${insightBlock('Matriks Pergerakan Mingguan', insightTable(
        [{label:'Indikator'}, {label:'Terbaru',num:true}, {label:'Closing',num:true}, {label:'Δ Closing',num:true}, {label:'Growth',num:true}, {label:'Δ Pekan',num:true}, {label:'Sinyal'}], metricRows
      ), 'full')}
      ${insightBlock('Penguatan Terbesar', rising.map(item => `${item.label} berubah ${signedMoney(item.deltaClosing)} (${item.growthClosing === null ? '-' : pctText(item.growthClosing)}) dibanding closing.`))}
      ${insightBlock('Pelemahan Terbesar', falling.map(item => `${item.label} berubah ${signedMoney(item.deltaClosing)} (${item.growthClosing === null ? '-' : pctText(item.growthClosing)}) dibanding closing.`))}
      ${insightBlock('Prioritas Minggu Berikutnya', priorities)}
      ${insightBlock('Pertanyaan Review Mingguan', decisionQuestions([
        'Indikator mana yang memburuk dua pekan berturut-turut dan apa penyebab utamanya?',
        'Apakah perubahan kredit dan DPK berasal dari satu nasabah besar atau pergerakan yang menyebar?',
        'Debitur atau akun mana yang harus diselesaikan sebelum posisi pekan berikutnya?',
        'Apakah tindakan pekan lalu menghasilkan perbaikan yang dapat diukur?'
      ]), 'full')}
      ${insightBlock('Cara Membaca Tren', [
        'Pekan 1 dibandingkan dengan closing bulan sebelumnya; pekan berikutnya dibandingkan dengan pekan sebelumnya.',
        'Mode Aktual menunjukkan posisi nominal, sedangkan mode selisih menunjukkan penambahan atau penurunan antarpekan.',
        'Kredit, DPK, aset, dan laba umumnya diharapkan tumbuh berkualitas; NPL dan beban perlu dikendalikan.',
        'Gunakan perubahan mingguan untuk mendeteksi masalah lebih awal sebelum posisi akhir bulan.'
      ], 'full')}
    </div>`;
  }

  let lapkeuInsightPreviousBodyOverflow = null;

  window.openLapkeuInsight = function() {
    const modal = document.getElementById('lapkeuInsightModal');
    const card = modal?.querySelector('.lapkeu-insight-card');
    const title = document.getElementById('lapkeuInsightTitle');
    const sub = document.getElementById('lapkeuInsightSub');
    const body = document.getElementById('lapkeuInsightBody');
    const type = document.getElementById('type_report')?.value || '';
    const labels = {
      'neraca detail kantor':'Analisis Neraca',
      'laba rugi detail kantor':'Analisis Laba Rugi',
      'tv_makro_summary':'Pusat Keputusan Ringkasan Makro',
      'tren_makro_mingguan':'Pusat Keputusan Tren Mingguan'
    };
    const reportName = labels[type] || 'Laporan Keuangan';

    if (title) title.textContent = `${reportName} - ${selectedOfficeLabel()}`;
    if (sub) {
      const closing = formatViewDate(document.getElementById('closing_date')?.value || '');
      const actual = formatViewDate(document.getElementById('harian_date')?.value || '');
      sub.textContent = `Closing ${closing} dibanding posisi ${actual} · Rekomendasi otomatis untuk membantu kepala cabang menetapkan keputusan, PIC, tenggat, dan indikator hasil`;
    }
    if (body) {
      body.innerHTML = type === 'tren_makro_mingguan'
        ? buildTrendInsight(rawDataResult || {})
        : (type === 'tv_makro_summary'
          ? buildMakroInsight(rawDataResult || {})
          : buildFinancialInsight(rawDataResult || [], type));
      body.scrollTop = 0;
      body.scrollLeft = 0;
    }

    if (!modal) return;

    if (!modal.classList.contains('is-open')) {
      lapkeuInsightPreviousBodyOverflow = document.body.style.overflow;
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    if (card) {
      card.scrollTop = 0;
      card.scrollLeft = 0;
    }

    window.requestAnimationFrame(() => {
      if (body) body.scrollTop = 0;
      modal.querySelector('.lapkeu-insight-close')?.focus({ preventScroll:true });
    });
  };

  window.closeLapkeuInsight = function() {
    const modal = document.getElementById('lapkeuInsightModal');
    if (!modal) return;

    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = lapkeuInsightPreviousBodyOverflow ?? '';
    lapkeuInsightPreviousBodyOverflow = null;
  };

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') closeLapkeuInsight();
  });

  function renderMakroSummary(data) {
    const panel = document.getElementById('macroReport');
    const rasio = data?.rasio || {};
    const detail = data?.ringkasan_detail || {};
    const biaya = Array.isArray(data?.top_5_biaya) ? data.top_5_biaya : [];

    const ratioRows = [
      { label:'BOPO', value:rasio.bopo_persen, tone:'red', note:'Efisiensi operasional' },
      { label:'LDR', value:rasio.ldr_persen, tone:'green', note:'Intermediasi kredit' },
      { label:'CASA', value:rasio.casa_persen, tone:'green', note:'Dana murah' },
      { label:'NPL Gross', value:data?.makro?.npl?.persen_aktual, tone:'red', note:'Kualitas kredit' },
      { label:'ROA', value:rasio.roa_persen, tone:'blue', note:'Imbal hasil aset' },
      { label:'ROE', value:rasio.roe_persen, tone:'purple', note:'Imbal hasil ekuitas' },
      { label:'Cash Ratio', value:rasio.cash_ratio_persen, tone:'blue', note:'Likuiditas kas' },
      { label:'NIM', value:rasio.nim_persen, tone:'purple', note:'Margin bunga bersih' },
      { label:'Aset Likuid', value:rasio.aset_likuid_persen, tone:'orange', note:'Komposisi aset likuid' },
    ];

    const nominalGroups = [
      {
        title:'Pendanaan',
        rows:[
          ['Dana Masyarakat', detail.dana_masyarakat?.total],
          ['Tabungan', detail.dana_masyarakat?.tabungan],
          ['Deposito', detail.dana_masyarakat?.deposito],
        ]
      },
      {
        title:'Kredit & Penempatan',
        rows:[
          ['Kredit Baki Debet', detail.kredit_diberikan?.baki_debet],
          ['Saldo Bank EAD', detail.kredit_diberikan?.saldo_bank_ead],
          ['PPBL', detail.ppbl],
        ]
      },
      {
        title:'Risiko & Profitabilitas',
        rows:[
          ['CKPN PPBL', detail.ckpn?.ckpn_ppbl],
          ['CKPN Kredit', detail.ckpn?.ckpn_kredit],
          ['OSC NPL', detail.ckpn?.npl ?? data?.makro?.npl?.nominal_aktual],
          ['Laba Sebelum Pajak', detail.laba_sebelum_pajak],
          ['Laba Setelah Pajak', detail.laba_setelah_pajak],
        ]
      },
    ];

    const maxBiaya = Math.max(...biaya.map(item => Math.abs(Number(item?.nominal || 0))), 0);

    panel.innerHTML = `
      <div class="report-dashboard">
        <div class="custom-top-summary">
          ${(() => {
            const k = data?.makro || data?.keuangan || {};
            return [
              overviewCard('Aset Gabungan', k.aset?.nominal_aktual, k.aset?.growth_mom, 'blue', 'MoM', 'Posisi aset'),
              overviewCard('Kredit', detail.kredit_diberikan?.baki_debet, null, 'green', '', 'Baki debet'),
              overviewCard('Dana Pihak Ketiga', k.dpk?.nominal_aktual, k.dpk?.growth_mom, 'purple', 'MoM', 'Total DPK'),
              overviewCard('NPL', k.npl?.nominal_aktual, k.npl?.growth_mom, 'red', 'MoM', `${pctText(k.npl?.persen_aktual)} gross`),
              overviewCard('Laba', k.laba_rugi?.nominal_aktual ?? k.laba?.nominal_aktual, k.laba_rugi?.growth_mom ?? k.laba?.growth_mom, 'orange', 'MoM', 'Laba berjalan')
            ].join('');
          })()}
        </div>

        <div class="macro-dashboard-grid">
          <section class="dashboard-section macro-ratio-section">
            ${sectionHeading('Kesehatan Bank', 'Indikator Rasio Utama', `${ratioRows.length} indikator`)}
            <div class="health-grid">
              ${ratioRows.map(item => `
                <article class="health-card health-${item.tone}">
                  <div class="health-label">${safeText(item.label)}</div>
                  <div class="health-value">${pctText(item.value)}</div>
                  <div class="health-note">${safeText(item.note)}</div>
                </article>
              `).join('')}
            </div>
          </section>

          <section class="dashboard-section macro-nominal-section">
            ${sectionHeading('Posisi Keuangan', 'Komponen Nominal Utama', 'Nilai ringkas')}
            <div class="nominal-group-grid">
              ${nominalGroups.map(group => `
                <article class="nominal-group">
                  <div class="nominal-group-title">${safeText(group.title)}</div>
                  <div class="nominal-list">
                    ${group.rows.map(row => `
                      <div class="nominal-item" title="Rp ${fmtNom(row[1])}">
                        <span class="nominal-name">${safeText(row[0])}</span>
                        <span class="nominal-value">Rp ${fmtSingkat(row[1])}</span>
                      </div>
                    `).join('')}
                  </div>
                </article>
              `).join('')}
            </div>
          </section>

          <section class="dashboard-section macro-expense-section">
            ${sectionHeading('Efisiensi', 'Top 5 Beban Biaya', biaya.length ? `${biaya.length} pos` : 'Tidak ada data')}
            <div class="expense-list">
              ${biaya.length ? biaya.map((item, index) => {
                const nominal = Math.abs(Number(item?.nominal || 0));
                const width = maxBiaya > 0 ? Math.max(5, (nominal / maxBiaya) * 100) : 0;
                return `
                  <article class="expense-item" title="${safeText(item?.kode || '')} · Rp ${fmtNom(item?.nominal)}">
                    <div class="expense-row">
                      <span class="expense-rank">${index + 1}</span>
                      <span class="expense-name">${safeText(item?.nama || '-')}</span>
                      <span class="expense-value">Rp ${fmtSingkat(item?.nominal)}</span>
                    </div>
                    <div class="expense-track"><div class="expense-bar" style="width:${width.toFixed(2)}%"></div></div>
                  </article>
                `;
              }).join('') : '<div class="empty-report">Belum ada data beban biaya pada posisi ini.</div>'}
            </div>
          </section>
        </div>
      </div>
    `;
  }

  function weeklyMetricConfig() {
    return {
      aset_gabungan: { label:'Aset', tone:'blue', color:'#2563eb', soft:'#dbeafe' },
      kredit_baki_debet: { label:'Kredit', tone:'green', color:'#059669', soft:'#d1fae5' },
      dpk: { label:'DPK', tone:'purple', color:'#7c3aed', soft:'#ede9fe' },
      tabungan: { label:'Tabungan', tone:'teal', color:'#0d9488', soft:'#ccfbf1' },
      deposito: { label:'Deposito', tone:'amber', color:'#d97706', soft:'#fef3c7' },
      pendapatan: { label:'Pendapatan', tone:'emerald', color:'#16a34a', soft:'#dcfce7' },
      beban: { label:'Beban', tone:'red', color:'#dc2626', soft:'#fee2e2' },
      npl: { label:'NPL', tone:'red', color:'#ef4444', soft:'#fee2e2' },
      laba_net: { label:'Laba', tone:'orange', color:'#ea580c', soft:'#ffedd5' },
    };
  }

  function buildWeeklyChartSvg(metricKey) {
    const config = weeklyMetricConfig()[metricKey] || weeklyMetricConfig().aset_gabungan;
    const weeks = weeklyTrendState.weeks;
    const closing = Number(weeklyTrendState.summary?.previous_closing?.[metricKey] || 0);
    const showActual = weeklyTrendState.showActual !== false;
    const points = [
      { label:'Closing', date:'M-1', value:showActual ? closing : 0, actual:closing, previous:null, delta:0, growth:null },
      ...weeks.map((week, index) => {
        const previous = index > 0 ? Number(weeks[index - 1]?.[metricKey] || 0) : closing;
        const actual = Number(week?.[metricKey] || 0);
        const delta = actual - previous;
        return {
          label:week?.label || `M${index + 1}`,
          date:week?.tanggal || '',
          value:showActual ? actual : delta,
          actual,
          previous,
          delta,
          growth:growthValue(actual, previous)
        };
      })
    ];
    if (!points.length) return '<div class="empty-report">Belum ada data tren mingguan.</div>';

    const canvasWidth = document.getElementById('weeklyChartCanvas')?.clientWidth || 1000;
    const width = Math.max(360, Math.round(canvasWidth));
    const height = width < 560 ? 300 : 350;
    const pad = width < 560 ? { left:60, right:18, top:32, bottom:54 } : { left:92, right:34, top:34, bottom:62 };
    const plotW = width - pad.left - pad.right;
    const plotH = height - pad.top - pad.bottom;
    const values = points.map(p => Number(p.value || 0));
    let minV = Math.min(...values), maxV = Math.max(...values);
    if (minV === maxV) {
      const span = Math.max(Math.abs(maxV) * .08, 1);
      minV -= span; maxV += span;
    } else {
      const span = maxV - minV;
      minV -= span * .12; maxV += span * .12;
    }
    const x = i => pad.left + (points.length === 1 ? plotW / 2 : (i * plotW / (points.length - 1)));
    const y = value => pad.top + ((maxV - value) / (maxV - minV)) * plotH;
    const coords = points.map((p, i) => ({ ...p, x:x(i), y:y(p.value) }));
    const linePoints = coords.map(p => `${p.x.toFixed(2)},${p.y.toFixed(2)}`).join(' ');
    const areaPath = `M ${coords[0].x.toFixed(2)} ${height-pad.bottom} L ${coords.map(p => `${p.x.toFixed(2)} ${p.y.toFixed(2)}`).join(' L ')} L ${coords[coords.length-1].x.toFixed(2)} ${height-pad.bottom} Z`;
    const ticks = 5;
    const grid = Array.from({length:ticks}, (_, i) => {
      const ratio = i/(ticks-1);
      const val = maxV - ratio*(maxV-minV);
      const yy = pad.top + ratio*plotH;
      return `<line class="weekly-chart-grid" x1="${pad.left}" y1="${yy}" x2="${width-pad.right}" y2="${yy}"/><text class="weekly-chart-axis-label" x="${pad.left-12}" y="${yy+4}" text-anchor="end">${safeText(fmtSingkat(val))}</text>`;
    }).join('');

    const labelStep = points.length > 8 ? Math.ceil(points.length / 7) : 1;
    const labels = coords.map((p, i) => {
      if (i !== 0 && i !== coords.length - 1 && i % labelStep !== 0) return '';
      return `<text class="weekly-chart-x-label" x="${p.x}" y="${height-pad.bottom+28}" text-anchor="middle">${safeText(p.label)}</text>`;
    }).join('');
    const dots = coords.map((p, i) => {
      const latest = i === coords.length - 1;
      const tooltip = i === 0
        ? `${p.label}\n${config.label}: Rp ${fmtNom(p.actual)}`
        : `${p.label}\n${config.label}: Rp ${fmtNom(p.actual)}\nPekan sebelumnya: Rp ${fmtNom(p.previous)}\nSelisih: ${signedMoney(p.delta)}\nGrowth: ${p.growth === null ? '-' : pctText(p.growth)}`;
      return `<circle class="weekly-chart-dot ${latest ? 'latest' : ''}" cx="${p.x}" cy="${p.y}" r="${latest ? 7 : 5}"><title>${safeText(tooltip)}</title></circle>`;
    }).join('');
    const closingY = y(showActual ? closing : 0);
    const latest = coords[coords.length-1];
    const latestBoxX = Math.min(width-pad.right-120, Math.max(pad.left, latest.x-58));
    const latestBoxY = Math.max(6, latest.y-48);

    return `
      <svg viewBox="0 0 ${width} ${height}" role="img" aria-label="Grafik tren mingguan ${safeText(config.label)}" style="--chart-accent:${config.color}">
        <defs>
          <linearGradient id="weeklyAreaGradient" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="${config.color}" stop-opacity=".28"/>
            <stop offset="100%" stop-color="${config.color}" stop-opacity=".02"/>
          </linearGradient>
        </defs>
        ${grid}
        <line class="weekly-chart-closing-line" x1="${pad.left}" y1="${closingY}" x2="${width-pad.right}" y2="${closingY}"/>
        <text class="weekly-chart-closing-label" x="${width-pad.right}" y="${closingY-7}" text-anchor="end">${showActual ? `Closing Rp ${safeText(fmtSingkat(closing))}` : `Basis Rp ${safeText(fmtSingkat(closing))}`}</text>
        <path class="weekly-chart-area" d="${areaPath}"/>
        <polyline class="weekly-chart-line" points="${linePoints}"/>
        ${dots}
        ${labels}
        <g transform="translate(${latestBoxX},${latestBoxY})">
          <rect width="116" height="31" rx="8" fill="${config.color}"/>
          <text class="weekly-chart-latest-label" x="58" y="19" text-anchor="middle">${showActual ? `Rp ${safeText(fmtSingkat(latest.value))}` : safeText(signedMoney(latest.value))}</text>
        </g>
      </svg>
    `;
  }

  function renderWeeklyChartSelected(metricKey) {
    const configMap = weeklyMetricConfig();
    const metric = configMap[metricKey] ? metricKey : 'aset_gabungan';
    weeklyTrendState.metric = metric;
    const config = configMap[metric];
    const metricSelect = document.getElementById('weeklyMetricSelect');
    if (metricSelect) {
      metricSelect.value = metric;
      metricSelect.style.setProperty('--chart-accent', config.color);
    }
    const chartShell = document.querySelector('#trendReport .weekly-chart-shell');
    if (chartShell) chartShell.style.setProperty('--chart-accent', config.color);
    const canvas = document.getElementById('weeklyChartCanvas');
    if (canvas) canvas.innerHTML = buildWeeklyChartSvg(metric);

    const weeks = weeklyTrendState.weeks;
    const closing = Number(weeklyTrendState.summary?.previous_closing?.[metric] || 0);
    const latest = Number(weeks.at(-1)?.[metric] || 0);
    const previous = Number(weeks.length > 1 ? weeks.at(-2)?.[metric] || 0 : closing);
    const showActual = weeklyTrendState.showActual !== false;
    const values = showActual
      ? weeks.map(w => Number(w?.[metric] || 0))
      : weeks.map((w, index) => Number(w?.[metric] || 0) - Number(index > 0 ? weeks[index - 1]?.[metric] || 0 : closing));
    const latestDisplay = showActual ? latest : latest - previous;
    const statMap = {
      weeklyCurrentValue: showActual ? `Rp ${fmtSingkat(latest)}` : signedMoney(latestDisplay),
      weeklyClosingValue: showActual ? `Rp ${fmtSingkat(closing)}` : `Basis Rp ${fmtSingkat(closing)}`,
      weeklyHighestValue: showActual ? `Rp ${fmtSingkat(values.length ? Math.max(...values) : 0)}` : signedMoney(values.length ? Math.max(...values) : 0),
      weeklyLowestValue: showActual ? `Rp ${fmtSingkat(values.length ? Math.min(...values) : 0)}` : signedMoney(values.length ? Math.min(...values) : 0),
    };
    Object.entries(statMap).forEach(([id,value]) => { const el=document.getElementById(id); if(el) el.textContent=value; });
    const changeEl = document.getElementById('weeklyCurrentChange');
    if (changeEl) changeEl.innerHTML = trendBadge(growthValue(latest, previous), 'vs pekan lalu');
  }

  window.selectWeeklyMetric = function(metricKey) {
    renderWeeklyChartSelected(metricKey);
  };

  window.toggleWeeklyTrendMode = function(checked) {
    weeklyTrendState.showActual = Boolean(checked);
    renderWeeklyChartSelected(weeklyTrendState.metric);
  };

  function buildWeeklyChecklist(weeks, configs, summary) {
    const previousClosing = summary?.previous_closing || {};
    return `
      <div class="weekly-checklist">
        <div class="weekly-checklist-head">
          <div>
            <div class="weekly-checklist-title">Checklist Mingguan</div>
            <div class="weekly-checklist-sub">Pekan 1 dibanding closing, berikutnya dibanding pekan sebelumnya.</div>
          </div>
          <span class="trend-badge trend-flat">${weeks.length} pekan</span>
        </div>
        <div class="weekly-checklist-grid">
          ${weeks.map((week, index) => {
            const previous = index > 0 ? weeks[index - 1] : previousClosing;
            return `
              <article class="weekly-check-card">
                <div class="weekly-check-card-head">
                  <div>
                    <div class="weekly-check-title">${safeText(week?.label || `Pekan ${index + 1}`)}</div>
                    <div class="weekly-check-date">${safeText(formatViewDate(week?.tanggal || ''))}</div>
                  </div>
                  ${trendBadge(growthValue(Number(week?.[weeklyTrendState.metric] || 0), Number(previous?.[weeklyTrendState.metric] || 0)))}
                </div>
                <div class="weekly-check-lines">
                  ${Object.entries(configs).map(([key, config]) => {
                    const current = Number(week?.[key] || 0);
                    const prev = Number(previous?.[key] || 0);
                    const delta = current - prev;
                    const growth = growthValue(current, prev);
                    return `
                      <div class="weekly-check-line" title="${safeText(`${config.label}\nAktual: Rp ${fmtNom(current)}\nPembanding: Rp ${fmtNom(prev)}\nSelisih: ${signedMoney(delta)}\nGrowth: ${growth === null ? '-' : pctText(growth)}`)}">
                        <span class="label">${safeText(config.label)}</span>
                        <span class="amount">Rp ${safeText(fmtSingkat(current))}</span>
                        <span class="delta ${deltaClass(delta)}">${safeText(signedMoney(delta))} (${growth === null ? '-' : pctText(growth)})</span>
                      </div>
                    `;
                  }).join('')}
                </div>
              </article>
            `;
          }).join('')}
        </div>
      </div>
    `;
  }

  function renderTrenMingguan(data) {
    const panel = document.getElementById('trendReport');
    const weeks = Array.isArray(data?.weeks) ? [...data.weeks] : [];
    weeks.sort((a,b) => new Date(a?.tanggal || 0) - new Date(b?.tanggal || 0));
    const summary = data?.summary || {};
    weeklyTrendState = {
      weeks,
      summary,
      metric:weeklyTrendState.metric || 'aset_gabungan',
      showActual:weeklyTrendState.showActual !== false
    };
    const configs = weeklyMetricConfig();
    const latestWeek = weeks.at(-1) || {};
    const previousClosing = summary.previous_closing || {};

    panel.innerHTML = `
      <div class="report-dashboard">
        ${weeks.length ? `
          <section class="weekly-chart-shell" style="--chart-accent:${configs[weeklyTrendState.metric]?.color || configs.aset_gabungan.color}">
            <div class="weekly-chart-toolbar">
              <div>
                <div class="weekly-chart-title">Pergerakan Nominal Mingguan</div>
                <div class="weekly-chart-sub">Pilih indikator untuk mengganti grafik · ${weeks.length} periode mingguan</div>
              </div>
              <div class="weekly-chart-controls">
                <div class="weekly-metric-select-wrap">
                  <select id="weeklyMetricSelect" class="weekly-metric-select" onchange="selectWeeklyMetric(this.value)" aria-label="Pilih indikator tren mingguan" title="Pilih indikator grafik">
                    ${Object.entries(configs).map(([key,config]) => `<option value="${safeText(key)}" ${key === weeklyTrendState.metric ? 'selected' : ''}>${safeText(config.label)}</option>`).join('')}
                  </select>
                </div>
                <label class="weekly-mode-toggle" title="Centang untuk tren nominal aktual. Hilangkan centang untuk tren selisih pekan.">
                  <input type="checkbox" ${weeklyTrendState.showActual ? 'checked' : ''} onchange="toggleWeeklyTrendMode(this.checked)">
                  <span>Aktual</span>
                </label>
              </div>
            </div>

            <div class="weekly-chart-stat-grid">
              <div class="weekly-chart-stat"><div class="label">Posisi Terbaru</div><div id="weeklyCurrentValue" class="value">-</div><div id="weeklyCurrentChange" class="mt-1"></div></div>
              <div class="weekly-chart-stat"><div class="label">Posisi Closing</div><div id="weeklyClosingValue" class="value">-</div></div>
              <div class="weekly-chart-stat"><div class="label">Tertinggi</div><div id="weeklyHighestValue" class="value">-</div></div>
              <div class="weekly-chart-stat"><div class="label">Terendah</div><div id="weeklyLowestValue" class="value">-</div></div>
            </div>

            <div id="weeklyChartCanvas" class="weekly-chart-canvas"></div>

            <div class="weekly-chart-footer">
              ${Object.entries(configs).map(([key,config]) => {
                const current = Number(latestWeek?.[key] || 0);
                const previous = Number(previousClosing?.[key] || 0);
                const delta = current - previous;
                const growth = growthValue(current, previous);
                return `
                  <article class="weekly-latest-card" title="${safeText(`${config.label}\nAktual: Rp ${fmtNom(current)}\nBulan lalu: Rp ${fmtNom(previous)}\nSelisih: ${signedMoney(delta)}\nGrowth: ${growth === null ? '-' : pctText(growth)}`)}">
                    <div class="name">${safeText(config.label)} Terbaru</div>
                    <div class="value" style="color:${config.color}">Rp ${fmtSingkat(current)}</div>
                    <div class="change"><span>${safeText(latestWeek?.label || 'Pekan terbaru')}</span>${trendBadge(growth, 'vs bulan lalu')}</div>
                  </article>
                `;
              }).join('')}
            </div>
          </section>
        ` : '<div class="empty-report">Belum ada data tren mingguan pada periode yang dipilih.</div>'}
      </div>
    `;
    if (weeks.length) renderWeeklyChartSelected(weeklyTrendState.metric);
  }

  window.toggleRow = function(parentKode) {
    const searchValue = document.getElementById('financialSearch')?.value.trim() || '';
    if (searchValue) return;

    const allRows = [...document.querySelectorAll('#lapBody tr.financial-row')];
    const clickedRow = allRows.find(row => row.dataset.kode === String(parentKode));
    const clickedIcon = clickedRow?.querySelector('.caret');
    if (!clickedRow || !clickedIcon || clickedRow.dataset.parent !== '1') return;

    const isOpening = !clickedIcon.classList.contains('rotate');
    clickedIcon.classList.toggle('rotate', isOpening);
    clickedRow.setAttribute('aria-expanded', isOpening ? 'true' : 'false');

    // Saat parent ditutup, seluruh turunan juga di-reset agar saat dibuka kembali
    // hanya level anak langsung yang tampil. Ini mencegah baris "loncat".
    if (!isOpening) {
      collapseFinancialDescendants(clickedRow, allRows);
      allRows.forEach(row => {
        const parentPath = clickedRow.dataset.treePath || '';
        const path = row.dataset.treePath || '';
        if (parentPath && path.startsWith(`${parentPath}>`) && row.dataset.parent === '1') {
          row.setAttribute('aria-expanded', 'false');
        }
      });
    }

    syncFinancialHierarchyVisibility();
  };

  function renderSummary(data, type) {
    const container = document.getElementById('rekapContainer');
    if (!container) return;
    if (['tv_makro_summary', 'tren_makro_mingguan'].includes(type)) {
      container.innerHTML = '';
      return;
    }

    const rows = Array.isArray(data) ? data : [];
    const getVal = code => Math.round(Number(rows.find(item => String(item?.kode_perk || '') === code)?.total_saldo || 0));
    const getAsetGabungan = () => {
      const totalAset = asetGabunganCodes.reduce((sum, code) => sum + getVal(code), 0);
      const kantor = String(document.getElementById('opt_kantor_rec')?.value || '').trim().toLowerCase();
      const isConsolidated = kantor === 'konsolidasi' || kantor === '000';
      return totalAset - (isConsolidated ? getVal('210') : 0);
    };

    if (type.includes('neraca')) {
      const aset = getAsetGabungan();
      const kewajiban = getVal('2');
      const ekuitas = getVal('3');
      container.innerHTML = [
        overviewCard('Aset Gabungan', aset, null, 'blue', '', 'Total aset bersih'),
        overviewCard('Kewajiban', kewajiban, null, 'purple', '', 'Kelompok rekening 2'),
        overviewCard('Ekuitas', ekuitas, null, 'orange', '', 'Kelompok rekening 3')
      ].join('');
      return;
    }

    const pendapatan = getVal('4');
    const biaya = getVal('5');
    const laba = pendapatan - biaya;
    container.innerHTML = [
      overviewCard('Pendapatan', pendapatan, null, 'green', '', 'Kelompok rekening 4'),
      overviewCard('Biaya', biaya, null, 'orange', '', 'Kelompok rekening 5'),
      overviewCard('Laba Rugi Berjalan', laba, null, laba >= 0 ? 'blue' : 'purple', '', laba >= 0 ? 'Surplus berjalan' : 'Defisit berjalan')
    ].join('');
  }



  window.addEventListener('resize', () => {
    clearTimeout(window.__lapViewResize);
    window.__lapViewResize = setTimeout(() => {
      const type = document.getElementById('type_report')?.value || '';
      if (type === 'tren_makro_mingguan' && weeklyTrendState.weeks.length) {
        renderWeeklyChartSelected(weeklyTrendState.metric);
      }
    }, 140);
  });


  function syncLapkeuMobileFilter() {
    const btn = document.getElementById('lapkeuMobileFilterToggle');
    const form = document.getElementById('filterForm');
    if (!btn || !form) return;
    const isMobile = window.innerWidth < 768;
    const isOpen = form.classList.contains('lapkeu-filter-open');
    if (!isMobile) {
      form.classList.remove('lapkeu-filter-open');
      btn.classList.remove('is-open');
      btn.setAttribute('aria-expanded', 'false');
      btn.setAttribute('title', 'Buka filter');
      return;
    }
    btn.classList.toggle('is-open', isOpen);
    btn.setAttribute('aria-expanded', String(isOpen));
    btn.setAttribute('title', isOpen ? 'Tutup filter' : 'Buka filter');
    btn.querySelector('span').textContent = isOpen ? 'Tutup' : 'Filter';
  }

  function toggleLapkeuMobileFilter(forceOpen = null) {
    const form = document.getElementById('filterForm');
    if (!form || window.innerWidth >= 768) return;
    const shouldOpen = forceOpen === null ? !form.classList.contains('lapkeu-filter-open') : !!forceOpen;
    form.classList.toggle('lapkeu-filter-open', shouldOpen);
    syncLapkeuMobileFilter();
  }

  window.addEventListener('resize', () => {
    syncLapkeuMobileFilter();
  });

  document.getElementById('lapkeuMobileFilterToggle')?.addEventListener('click', () => toggleLapkeuMobileFilter());
  syncLapkeuMobileFilter();

  function exportToExcel() {
    if(!Array.isArray(rawDataResult) || rawDataResult.length === 0) return alert("Export Excel untuk mode ini belum tersedia, pilih NERACA atau LABA RUGI dulu ya.");
    let table = `<table border="1"><thead><tr style="background:#f1f5f9"><th>Kode</th><th>Uraian</th><th>Actual</th><th>Closing</th><th>Selisih</th><th>%</th></tr></thead><tbody>`;
    rawDataResult.forEach(d => {
      let roundedSaldo = Math.round(Number(d.total_saldo || 0));
      let closingSaldo = Math.round(Number(d.closing_saldo || 0));
      let selisihSaldo = Math.round(Number(d.selisih_saldo || (roundedSaldo - closingSaldo)));
      let growth = Number(d.growth_persen || 0).toFixed(2);
      table += `<tr><td style="mso-number-format:'\\@'">${d.kode_perk}</td><td>${d.nama_perkiraan}</td><td>${roundedSaldo}</td><td>${closingSaldo}</td><td>${selisihSaldo}</td><td>${growth}%</td></tr>`;
    });
    table += `</tbody></table>`;
    const blob = new Blob([table], { type: 'application/vnd.ms-excel' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    
    const ddl = document.getElementById('opt_kantor_rec');
    const namaKantor = ddl.options[ddl.selectedIndex].text.replace(/ /g, '_');
    
    a.download = `Laporan_Keuangan_${namaKantor}_${document.getElementById('harian_date').value}.xls`;
    a.click();
  }

  document.getElementById('filterForm').onsubmit = e => { e.preventDefault(); fetchRekap(); if (window.innerWidth < 768) toggleLapkeuMobileFilter(false); };

  ['type_report','opt_kantor_rec','harian_date','closing_date'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', () => {
      if (window.innerWidth < 768) setTimeout(() => toggleLapkeuMobileFilter(false), 50);
    });
  });
</script>

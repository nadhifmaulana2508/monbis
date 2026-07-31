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
  #tabelLapKeu .lap-value-col { width:190px; }
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
    #tabelLapKeu .lap-value-col { width:119px; }
    #tabelLapKeu thead th { height:32px; padding:6px !important; font-size:8px !important; }
    #tabelLapKeu tbody tr { height:36px; }
    #tabelLapKeu tbody td { padding:6px !important; font-size:9px; }
    #tabelLapKeu .financial-mobile-code { display:inline-flex; }
    #tabelLapKeu .financial-name { font-size:9px; }
    #tabelLapKeu .financial-amount-full { display:none; }
    #tabelLapKeu .financial-amount-short { display:block; font-size:9px; }
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
    #tabelLapKeu .lap-value-col { width:103px; }
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
    .lapkeu-filter-date { grid-column:span 10 !important; }
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

</style>

<div id="lapkeuPage" class="max-w-[1920px] mx-auto px-2 md:px-4 py-2 md:py-4 h-[calc(100vh-10px)] flex flex-col space-y-2 md:space-y-4 bg-[#f8fafc]">
  
  <div id="lapkeuHeader" class="flex flex-col md:flex-row justify-between md:items-end gap-2 md:gap-4 shrink-0 bg-white p-2.5 md:p-4 rounded-xl border border-slate-200 shadow-sm">
    
    <div class="lapkeu-brand">
      <span class="lapkeu-brand-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V9"></path><path d="M10 19V5"></path><path d="M16 19v-7"></path><path d="M22 19H2"></path></svg>
      </span>
      <div class="lapkeu-brand-copy">
        <span id="reportPageTitle">Laporan Keuangan</span>
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

      <div class="lapkeu-filter-date flex flex-col">
        <div class="lapkeu-date-label-row">
          <button type="button" class="lapkeu-insight-btn" onclick="openLapkeuInsight()" title="Insight kondisi dan tindak lanjut">i</button>
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

  <div id="lapkeuInsightModal" class="lapkeu-insight-modal" onclick="if(event.target===this) closeLapkeuInsight()">
    <div class="lapkeu-insight-card custom-scrollbar">
      <div class="lapkeu-insight-head">
        <div>
          <div class="lapkeu-insight-kicker">Insight Direksi</div>
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
            </colgroup>
            <thead class="sticky top-0 z-40">
              <tr>
                <th class="lap-code-col">Kode</th>
                <th>Uraian Perkiraan</th>
                <th class="text-right">Saldo (IDR)</th>
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

  async function loadDefaultAccHistoryDate() {
      try {
          const res = await fetch(API_LAP, {
              method: 'POST',
              headers: {'Content-Type':'application/json'},
              body: JSON.stringify({ type: 'default_acc_history_date' })
          });
          const json = await res.json();
          return json?.data?.last_created || getYesterdayDate();
      } catch(e) {
          return getYesterdayDate();
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
          'tv_makro_summary':'Ringkasan Makro',
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

      await populateKantorOptionsFP(uKode);
      document.getElementById('harian_date').value = await loadDefaultAccHistoryDate();
      document.getElementById('type_report')?.addEventListener('change', handleReportTypeChange);
      ['opt_kantor_rec', 'harian_date'].forEach(id => {
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
          harian_date: document.getElementById('harian_date').value
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
          tbody.innerHTML = `<tr><td colspan="3" class="text-center p-10 text-red-500 font-bold text-xs">Gagal memuat data laporan!</td></tr>`;
      } finally {
          loader.classList.add('hidden');
      }
  }

  function renderTable(data) {
    const tbody = document.getElementById('lapBody');
    const reportType = document.getElementById('type_report')?.value || '';
    const isNeraca = reportType.includes('neraca');
    currentFinancialRows = Array.isArray(data) ? data : [];

    const title = isNeraca ? 'Neraca Keuangan' : 'Laporan Laba Rugi';
    const toolbarTitle = document.getElementById('financialToolbarTitle');
    const toolbarMeta = document.getElementById('financialToolbarMeta');
    const search = document.getElementById('financialSearch');
    if (toolbarTitle) toolbarTitle.textContent = title;
    if (toolbarMeta) toolbarMeta.textContent = `${currentFinancialRows.length} akun · Klik kelompok untuk membuka detail`;
    if (search) search.value = '';

    const codeSet = new Set(currentFinancialRows.map(row => String(row?.kode_perk || '')));
    const hasChildren = code => currentFinancialRows.some(row => {
      const child = String(row?.kode_perk || '');
      return child !== code && child.startsWith(code);
    });

    tbody.innerHTML = currentFinancialRows.map(d => {
      const kode = String(d?.kode_perk || '');
      const nama = safeText(d?.nama_perkiraan || '-');
      const len = kode.length;
      const parent = hasChildren(kode);
      const levelClass = len === 1 ? 'financial-level-1' : (len === 2 ? 'financial-level-2' : (len === 3 ? 'financial-level-3' : 'financial-level-detail'));
      const hiddenClass = len > 3 ? 'hidden-row' : '';
      const indent = Math.min(46, Math.max(0, len - 1) * 7);
      const total = Number(d?.total_saldo || 0);
      const amountClass = total < 0 ? 'is-negative' : '';
      const parentIcon = parent ? '<span class="caret">▶</span>' : '<span class="caret" style="visibility:hidden">▶</span>';
      const parentBadge = parent ? '<span class="financial-parent-badge">Kelompok</span>' : '';

      return `
        <tr class="financial-row ${levelClass} ${hiddenClass}" data-kode="${safeText(kode)}" data-len="${len}" data-parent="${parent ? '1' : '0'}" data-search="${safeText(`${kode} ${d?.nama_perkiraan || ''}`.toLowerCase())}" ${parent ? `onclick="toggleRow('${safeText(kode)}')"` : ''}>
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
        </tr>
      `;
    }).join('');

    if (!currentFinancialRows.length) {
      document.getElementById('financialEmptyState').style.display = 'block';
    } else {
      document.getElementById('financialEmptyState').style.display = 'none';
    }
  }

  window.setFinancialExpansion = function(expand) {
    const query = document.getElementById('financialSearch')?.value.trim() || '';
    if (query) {
      document.getElementById('financialSearch').value = '';
    }
    document.querySelectorAll('#lapBody tr.financial-row').forEach(row => {
      const len = Number(row.dataset.len || 0);
      row.classList.toggle('hidden-row', !expand && len > 3);
      const icon = row.querySelector('.caret');
      if (icon && row.dataset.parent === '1') icon.classList.toggle('rotate', expand);
    });
    filterFinancialRows();
  };

  window.filterFinancialRows = function() {
    const input = document.getElementById('financialSearch');
    const query = String(input?.value || '').trim().toLowerCase();
    const rows = [...document.querySelectorAll('#lapBody tr.financial-row')];
    const empty = document.getElementById('financialEmptyState');

    if (!query) {
      rows.forEach(row => {
        const len = Number(row.dataset.len || 0);
        const hasExpandedAncestor = [...rows].some(parent => {
          if (parent.dataset.parent !== '1') return false;
          const parentCode = parent.dataset.kode || '';
          const rowCode = row.dataset.kode || '';
          return rowCode !== parentCode && rowCode.startsWith(parentCode) && parent.querySelector('.caret')?.classList.contains('rotate');
        });
        row.classList.toggle('hidden-row', len > 3 && !hasExpandedAncestor);
      });
      if (empty) empty.style.display = rows.length ? 'none' : 'block';
      return;
    }

    const matchedCodes = new Set();
    rows.forEach(row => {
      if ((row.dataset.search || '').includes(query)) matchedCodes.add(row.dataset.kode || '');
    });
    rows.forEach(parent => {
      const parentCode = parent.dataset.kode || '';
      if ([...matchedCodes].some(code => code.startsWith(parentCode))) matchedCodes.add(parentCode);
    });
    let visible = 0;
    rows.forEach(row => {
      const show = matchedCodes.has(row.dataset.kode || '');
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
    const clean = items.filter(Boolean);
    return clean.length ? `<ul>${clean.map(item => `<li>${safeText(item)}</li>`).join('')}</ul>` : '<ul><li>Data belum cukup untuk membentuk insight otomatis.</li></ul>';
  }

  function insightBlock(title, items, full = false) {
    return `<section class="lapkeu-insight-block ${full ? 'full' : ''}"><h4>${safeText(title)}</h4>${insightList(items)}</section>`;
  }

  function changeSentence(label, growth, goodWhenUp = true) {
    if (growth === null || growth === undefined || !Number.isFinite(Number(growth))) return '';
    const n = Number(growth);
    const direction = n >= 0 ? 'naik' : 'turun';
    const tone = n === 0 ? 'stabil' : ((n > 0) === goodWhenUp ? 'positif' : 'perlu perhatian');
    return `${label} ${direction} ${pctText(Math.abs(n))} dibanding bulan lalu; status ${tone}.`;
  }

  function selectedOfficeLabel() {
    const el = document.getElementById('opt_kantor_rec');
    return el?.selectedOptions?.[0]?.textContent?.trim() || '-';
  }

  function buildMakroInsight(data) {
    const makro = data?.makro || {};
    const rasio = data?.rasio || {};
    const detail = data?.ringkasan_detail || {};
    const nplPct = Number(makro?.npl?.persen_aktual || 0);
    const bopo = Number(rasio?.bopo_persen || 0);
    const ldr = Number(rasio?.ldr_persen || 0);
    const labaGrowth = makro?.laba_rugi?.growth_mom ?? makro?.laba?.growth_mom;
    const bebanGrowth = makro?.biaya?.growth_mom;
    const dpkGrowth = makro?.dpk?.growth_mom;

    const kondisi = [
      changeSentence('Aset gabungan', makro?.aset?.growth_mom, true),
      changeSentence('DPK', dpkGrowth, true),
      changeSentence('Laba berjalan', labaGrowth, true),
      changeSentence('NPL nominal', makro?.npl?.growth_mom, false),
      nplPct ? `Rasio NPL gross berada di ${pctText(nplPct)} dari baki debet kredit.` : '',
      bopo ? `BOPO tercatat ${pctText(bopo)}; semakin rendah semakin efisien.` : '',
    ];

    const perhatian = [
      nplPct > 5 ? 'NPL melewati batas nyaman 5%, perlu prioritas penagihan dan recovery pada debitur bermasalah.' : 'NPL masih perlu dijaga agar tidak naik pada akhir bulan.',
      bopo > 90 ? 'BOPO tinggi, kontrol pos beban terbesar dan evaluasi efisiensi operasional.' : '',
      ldr < 75 ? 'LDR relatif rendah, dorong penyaluran kredit berkualitas agar dana lebih produktif.' : '',
      ldr > 95 ? 'LDR tinggi, jaga likuiditas dan kualitas pencairan baru.' : '',
      Number(labaGrowth) < 0 ? 'Laba turun, cek kombinasi pendapatan, biaya, dan kualitas portofolio kredit.' : '',
      Number(bebanGrowth) > 0 ? 'Beban naik dibanding bulan lalu, fokus review Top 5 Beban Biaya.' : '',
    ];

    const tindak = [
      'Tetapkan daftar cabang/debitur prioritas dari NPL dan beban terbesar untuk follow up harian.',
      'Dorong realisasi kredit yang sehat jika kredit/DPK belum produktif, tanpa mengorbankan kualitas kolektibilitas.',
      'Pantau laba sebelum dan setelah pajak agar target akhir bulan tidak tertinggal.',
      detail?.ckpn?.npl ? `OSC NPL saat ini Rp ${fmtSingkat(detail.ckpn.npl)}; gunakan sebagai dasar fokus recovery.` : '',
    ];

    return [
      insightBlock('Kondisi Utama', kondisi),
      insightBlock('Perlu Perhatian', perhatian),
      insightBlock('Tindak Lanjut', tindak, true),
    ].join('');
  }

  function buildTrendInsight(data) {
    const summary = data?.summary || {};
    const weeks = Array.isArray(data?.weeks) ? data.weeks : [];
    const configs = weeklyMetricConfig();
    const metrics = Object.entries(configs).map(([key, config]) => {
      const current = Number(summary?.[key] || 0);
      const closing = Number(summary?.previous_closing?.[key] || 0);
      const delta = current - closing;
      const growth = growthValue(current, closing);
      return { key, label:config.label, current, closing, delta, growth };
    });
    const rising = [...metrics].sort((a,b) => b.delta - a.delta).slice(0, 3);
    const falling = [...metrics].sort((a,b) => a.delta - b.delta).slice(0, 3);
    const npl = metrics.find(item => item.key === 'npl');

    const kondisi = [
      weeks.length ? `Tren memakai ${weeks.length} pekan pada bulan berjalan; pembanding utama adalah closing bulan sebelumnya.` : '',
      ...rising.map(item => `${item.label} naik ${signedMoney(item.delta)} (${item.growth === null ? '-' : pctText(item.growth)}) dari bulan lalu.`),
      npl && npl.delta > 0 ? `NPL naik ${signedMoney(npl.delta)} dari bulan lalu, perlu akselerasi recovery.` : '',
    ];
    const perhatian = [
      ...falling.map(item => `${item.label} turun ${signedMoney(item.delta)} (${item.growth === null ? '-' : pctText(item.growth)}) dari bulan lalu.`),
      'Gunakan checkbox Aktual: aktif untuk posisi nominal, nonaktif untuk melihat selisih pekan-ke-pekan.',
    ];
    const tindak = [
      'Fokuskan evaluasi pada pekan dengan penurunan terbesar dibanding pekan sebelumnya.',
      'Jika NPL naik sementara kredit tumbuh, cek kualitas pencairan dan migrasi kolektibilitas.',
      'Jika laba turun, bedah kontribusi pendapatan dan beban per pekan untuk menentukan aksi cepat.',
    ];

    return [
      insightBlock('Arah Tren', kondisi),
      insightBlock('Peringatan', perhatian),
      insightBlock('Tindak Lanjut', tindak, true),
    ].join('');
  }

  window.openLapkeuInsight = function() {
    const modal = document.getElementById('lapkeuInsightModal');
    const title = document.getElementById('lapkeuInsightTitle');
    const sub = document.getElementById('lapkeuInsightSub');
    const body = document.getElementById('lapkeuInsightBody');
    const type = document.getElementById('type_report')?.value || '';
    const reportName = type === 'tren_makro_mingguan' ? 'Tren Mingguan' : (type === 'tv_makro_summary' ? 'Ringkasan Makro' : 'Laporan Keuangan');
    if (title) title.textContent = `${reportName} - ${selectedOfficeLabel()}`;
    if (sub) sub.textContent = `Posisi ${formatViewDate(document.getElementById('harian_date')?.value || '')}`;
    if (body) {
      body.innerHTML = `<div class="lapkeu-insight-grid">${
        type === 'tren_makro_mingguan'
          ? buildTrendInsight(rawDataResult || {})
          : buildMakroInsight(rawDataResult || {})
      }</div>`;
    }
    modal?.classList.add('is-open');
  };

  window.closeLapkeuInsight = function() {
    document.getElementById('lapkeuInsightModal')?.classList.remove('is-open');
  };

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
    document.querySelectorAll('.weekly-metric-tab').forEach(button => {
      button.classList.toggle('active', button.dataset.metric === metric);
      button.style.setProperty('--chart-accent', config.color);
    });
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
                <label class="weekly-mode-toggle" title="Centang untuk tren nominal aktual. Hilangkan centang untuk tren selisih pekan.">
                  <input type="checkbox" ${weeklyTrendState.showActual ? 'checked' : ''} onchange="toggleWeeklyTrendMode(this.checked)">
                  <span>Aktual</span>
                </label>
                <div class="weekly-metric-tabs">
                  ${Object.entries(configs).map(([key,config]) => `<button type="button" data-metric="${key}" class="weekly-metric-tab ${key === weeklyTrendState.metric ? 'active' : ''}" onclick="selectWeeklyMetric('${key}')">${safeText(config.label)}</button>`).join('')}
                </div>
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
    const parentLen = Number(clickedRow.dataset.len || 0);

    allRows.forEach(row => {
      const rowKode = row.dataset.kode || '';
      if (!rowKode.startsWith(parentKode) || rowKode === parentKode) return;
      if (!isOpening) {
        row.classList.add('hidden-row');
        row.querySelector('.caret')?.classList.remove('rotate');
        return;
      }
      const rowLen = Number(row.dataset.len || 0);
      if (rowLen <= parentLen + 2) row.classList.remove('hidden-row');
    });
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
    const getAsetGabungan = () => asetGabunganCodes.reduce((sum, code) => sum + getVal(code), 0) - getVal('210');

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
    let table = `<table border="1"><thead><tr style="background:#f1f5f9"><th>Kode</th><th>Uraian</th><th>Saldo</th></tr></thead><tbody>`;
    rawDataResult.forEach(d => {
      let roundedSaldo = Math.round(Number(d.total_saldo || 0));
      table += `<tr><td style="mso-number-format:'\\@'">${d.kode_perk}</td><td>${d.nama_perkiraan}</td><td>${roundedSaldo}</td></tr>`;
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

  ['type_report','opt_kantor_rec','harian_date'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', () => {
      if (window.innerWidth < 768) setTimeout(() => toggleLapkeuMobileFilter(false), 50);
    });
  });
</script>

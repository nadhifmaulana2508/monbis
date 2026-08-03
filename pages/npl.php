<style>
  :root {
    --mk-primary:#2563eb;
    --mk-bg:#f8fafc;
    --mk-text:#334155;
    --mk-border:#e2e8f0;
    --mk-head:#f1f5f9;
    --mk-name:170px;
    --mk-code:58px;
    --mk-nav-overlap:0px;
    --mk-page-top:56px;
  }

  body {
    font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    background:var(--mk-bg);
    color:var(--mk-text);
    overflow:hidden;
  }

  .mk-scrollbar::-webkit-scrollbar { width:6px; height:6px; }
  .mk-scrollbar::-webkit-scrollbar-track { background:#f8fafc; }
  .mk-scrollbar::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  .mk-scrollbar::-webkit-scrollbar-thumb:hover { background:#94a3b8; }

  .mk-input {
    box-sizing:border-box;
    width:100%;
    min-width:0;
    height:34px;
    padding:0 9px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    background:#fff;
    color:#334155;
    font-size:11px;
    font-weight:700;
    outline:none;
    transition:border-color .18s ease,box-shadow .18s ease,background .18s ease;
  }
  .mk-input:focus {
    border-color:var(--mk-primary);
    box-shadow:0 0 0 3px rgba(37,99,235,.1);
  }
  .mk-input:disabled {
    background:#f8fafc;
    color:#64748b;
    cursor:not-allowed;
  }
  select.mk-input {
    appearance:none;
    -webkit-appearance:none;
    padding-right:28px;
    cursor:pointer;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 8px center;
    background-size:14px;
  }
  input[type="date"].mk-input { position:relative; cursor:pointer; }
  input[type="date"].mk-input::-webkit-calendar-picker-indicator {
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    opacity:0;
    cursor:pointer;
  }

  .mk-label {
    display:block;
    margin:0 0 3px 1px;
    color:#475569;
    font-size:8px;
    line-height:1;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.07em;
    white-space:nowrap;
  }

  .mk-tab {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    height:30px;
    padding:0 11px;
    border:1px solid transparent;
    border-radius:8px;
    color:#64748b;
    background:transparent;
    font-size:10px;
    font-weight:850;
    white-space:nowrap;
    cursor:pointer;
    transition:all .18s ease;
  }
  .mk-tab:hover { background:#f8fafc; color:#334155; }
  .mk-tab.active {
    background:#eff6ff;
    border-color:#bfdbfe;
    color:#1d4ed8;
    box-shadow:0 1px 2px rgba(15,23,42,.04);
  }
  .mk-tab svg { width:14px; height:14px; flex:0 0 auto; }

  .mk-view-toggle {
    position:relative;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:36px;
    height:36px;
    flex:0 0 36px;
    border:1px solid #bfdbfe;
    border-radius:10px;
    background:#eff6ff;
    color:#2563eb;
    box-shadow:0 1px 2px rgba(15,23,42,.06), inset 0 0 0 1px rgba(255,255,255,.7);
    cursor:pointer;
    transition:transform .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
  }
  .mk-view-toggle:hover {
    transform:translateY(-1px);
    background:#dbeafe;
    border-color:#93c5fd;
    color:#1d4ed8;
    box-shadow:0 5px 12px rgba(37,99,235,.14);
  }
  .mk-view-toggle:active { transform:translateY(0) scale(.97); }
  .mk-view-toggle:focus-visible {
    outline:0;
    box-shadow:0 0 0 3px rgba(37,99,235,.16), 0 4px 10px rgba(37,99,235,.12);
  }
  .mk-view-toggle svg { width:17px; height:17px; }
  .mk-view-toggle.is-npl {
    background:#eef2ff;
    border-color:#c7d2fe;
    color:#4f46e5;
  }
  .mk-view-toggle .mk-toggle-tooltip {
    position:absolute;
    top:calc(100% + 7px);
    right:0;
    z-index:120;
    width:max-content;
    max-width:210px;
    padding:6px 8px;
    border-radius:7px;
    background:#0f172a;
    color:#fff;
    font-size:9px;
    font-weight:750;
    line-height:1.25;
    opacity:0;
    visibility:hidden;
    transform:translateY(-3px);
    pointer-events:none;
    transition:all .15s ease;
    white-space:nowrap;
  }
  .mk-view-toggle:hover .mk-toggle-tooltip,
  .mk-view-toggle:focus-visible .mk-toggle-tooltip {
    opacity:1;
    visibility:visible;
    transform:translateY(0);
  }
  .mk-actions .mk-view-toggle .mk-toggle-tooltip {
    right:auto;
    left:50%;
    transform:translate(-50%,-3px);
  }
  .mk-actions .mk-view-toggle:hover .mk-toggle-tooltip,
  .mk-actions .mk-view-toggle:focus-visible .mk-toggle-tooltip {
    transform:translate(-50%,0);
  }

  .mk-filter-toggle {
    display:none;
    align-items:center;
    justify-content:center;
    gap:6px;
    height:30px;
    padding:0 10px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    background:#fff;
    color:#475569;
    font-size:9px;
    font-weight:850;
    cursor:pointer;
    transition:all .18s ease;
  }
  .mk-filter-toggle.active {
    border-color:#bfdbfe;
    background:#eff6ff;
    color:#2563eb;
  }

  .mk-actions {
    display:inline-flex;
    align-items:flex-end;
    justify-content:flex-end;
    gap:6px;
  }
  .mk-export {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:36px;
    min-width:36px;
    height:36px;
    padding:0;
    border:0;
    border-radius:10px;
    background:#059669;
    color:#fff;
    box-shadow:0 2px 5px rgba(5,150,105,.2);
    cursor:pointer;
    transition:transform .18s ease,background .18s ease,box-shadow .18s ease;
  }
  .mk-export:hover {
    background:#047857;
    transform:translateY(-1px);
    box-shadow:0 5px 12px rgba(5,150,105,.18);
  }
  .mk-export:active { transform:translateY(0) scale(.97); }
  .mk-export:focus-visible { outline:0; box-shadow:0 0 0 3px rgba(5,150,105,.18); }
  .mk-export svg { width:17px; height:17px; flex:0 0 auto; }

  .mk-view {
    display:none;
    min-width:0;
    max-width:100%;
    min-height:0;
    height:100%;
    overflow:hidden;
  }
  .mk-view.active { display:flex; flex-direction:column; }

  .mk-table-shell {
    --mk-head-h:38px;
    position:relative;
    width:100%;
    max-width:100%;
    min-width:0;
    height:100%;
    overflow-x:auto;
    overflow-y:auto;
    background:#fff;
    border:1px solid var(--mk-border);
    border-radius:10px;
    -webkit-overflow-scrolling:touch;
    overscroll-behavior-x:none;
    overscroll-behavior-y:contain;
    touch-action:pan-x pan-y;
    scrollbar-gutter:auto;
  }
  .mk-table {
    width:max-content;
    min-width:100%;
    max-width:none;
    border-collapse:separate;
    border-spacing:0;
    table-layout:fixed;
    font-size:11px;
  }
  #tableKolek { min-width:1080px; }
  #tableNpl { min-width:900px; }

  .mk-table th,
  .mk-table td {
    height:38px;
    padding:6px 8px;
    border-right:1px solid #eef2f7;
    border-bottom:1px solid #eef2f7;
    vertical-align:middle;
    white-space:nowrap;
    font-variant-numeric:tabular-nums;
  }
  .mk-table th:last-child,
  .mk-table td:last-child {
    border-right:0;
    box-shadow:inset -1px 0 0 #e2e8f0;
  }

  .mk-table thead th {
    position:sticky;
    top:0;
    z-index:50;
    height:38px;
    background:#f1f5f9;
    color:#475569;
    font-size:9px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.035em;
    box-shadow:inset 0 -1px 0 #cbd5e1;
  }
  .mk-sort { cursor:pointer; user-select:none; transition:background .18s ease; }
  .mk-sort:hover { background:#e2e8f0 !important; }
  .mk-sort-icon { margin-left:2px; color:#94a3b8; font-size:9px; }

  .mk-col-code {
    position:sticky;
    left:0;
    z-index:35;
    width:var(--mk-code);
    min-width:var(--mk-code);
    max-width:var(--mk-code);
    background:#fff;
    text-align:center;
  }
  .mk-col-name {
    position:sticky;
    left:var(--mk-code);
    z-index:34;
    width:var(--mk-name);
    min-width:var(--mk-name);
    max-width:var(--mk-name);
    background:#fff;
    text-align:left;
    box-shadow:3px 0 7px -5px rgba(15,23,42,.5);
  }
  .mk-table thead .mk-col-code {
    z-index:70;
    background:#e0f2fe;
    color:#1e3a8a;
  }
  .mk-table thead .mk-col-name {
    z-index:69;
    background:#e0f2fe;
    color:#1e3a8a;
  }

  .mk-total-row td {
    position:sticky;
    top:var(--mk-head-h);
    z-index:45;
    background:#eff6ff;
    color:#1e3a8a;
    font-weight:850;
    border-bottom:2px solid #bfdbfe;
    box-shadow:0 4px 7px -5px rgba(15,23,42,.45);
  }
  .mk-total-row td.mk-col-code { z-index:65; background:#eff6ff; }
  .mk-total-row td.mk-col-name { z-index:64; background:#eff6ff; }

  .mk-table tbody:not(.mk-total-body) tr:nth-child(even) td { background:#fbfdff; }
  .mk-table tbody:not(.mk-total-body) tr:hover td { background:#f8fafc; }
  .mk-table tbody:not(.mk-total-body) tr:hover td.mk-col-code,
  .mk-table tbody:not(.mk-total-body) tr:hover td.mk-col-name { background:#f8fafc; }

  .mk-money { font-weight:800; line-height:1.05; }
  .mk-noa { margin-top:3px; color:#94a3b8; font-size:8px; line-height:1; font-weight:750; }
  .mk-name-text { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

  .mk-tone-l { color:#1d4ed8; }
  .mk-tone-dp { color:#475569; }
  .mk-tone-kl { color:#d97706; }
  .mk-tone-d { color:#ea580c; }
  .mk-tone-m { color:#dc2626; }
  .mk-tone-npl { color:#b91c1c; }
  .mk-tone-porto { color:#1e40af; }

  .mk-pct-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:52px;
    min-height:23px;
    padding:3px 7px;
    border-radius:999px;
    font-size:9px;
    font-weight:900;
  }
  .mk-pct-good { background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; }
  .mk-pct-warn { background:#fef3c7; color:#b45309; border:1px solid #fde68a; }
  .mk-pct-bad { background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; }

  .mk-status {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:62px;
    padding:4px 7px;
    border-radius:999px;
    font-size:8px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.04em;
  }
  .mk-status.up { color:#b91c1c; background:#fee2e2; border:1px solid #fecaca; }
  .mk-status.down { color:#15803d; background:#dcfce7; border:1px solid #bbf7d0; }
  .mk-status.flat { color:#64748b; background:#f1f5f9; border:1px solid #e2e8f0; }

  .mk-loading {
    position:absolute;
    inset:0;
    z-index:100;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    gap:9px;
    background:rgba(255,255,255,.84);
    backdrop-filter:blur(2px);
    color:#2563eb;
    font-size:10px;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
  }
  .mk-loading.hidden { display:none; }
  .mk-spinner {
    width:30px;
    height:30px;
    border:4px solid #dbeafe;
    border-top-color:#2563eb;
    border-radius:50%;
    animation:mk-spin .8s linear infinite;
  }
  @keyframes mk-spin { to { transform:rotate(360deg); } }

  .mk-empty {
    padding:40px 16px !important;
    color:#94a3b8;
    text-align:center;
    font-weight:750;
  }

  #mkHeaderSubtitle {
    display:block;
    max-width:none;
    overflow:visible;
    text-overflow:clip;
    white-space:normal;
    line-height:1.25;
  }

  @media (min-width:768px) {
    .mk-input { height:36px; padding:0 10px; font-size:12px; }
    .mk-label { font-size:9px; }
    .mk-export { width:38px; min-width:38px; height:38px; border-radius:11px; }
    .mk-tab { height:32px; padding:0 13px; font-size:11px; }
    .mk-view-toggle { width:38px; height:38px; flex-basis:38px; border-radius:11px; }
    .mk-view-toggle svg { width:18px; height:18px; }
    .mk-table { font-size:11.5px; }
    .mk-table thead th { font-size:9.5px; }
  }

  @media (max-width:1179px) {
    #mkFilterPanel { display:none; }
    #mkFilterPanel.open { display:block; }
    .mk-filter-toggle { display:inline-flex; }
  }

  @media (max-width:767px) {
    :root { --mk-name:112px; --mk-code:0px; }

    #monitoringKreditPage {
      height:calc(100dvh - 58px) !important;
      padding:7px !important;
    }
    #mkHeaderCard { padding:8px !important; gap:8px !important; border-radius:10px !important; }
    #mkHeaderTitle { font-size:13px !important; }
    #mkHeaderSubtitle { font-size:8px !important; }
    #mkHeaderIcon { width:26px !important; height:26px !important; border-radius:7px !important; }
    #mkHeaderIcon svg { width:14px !important; height:14px !important; }

    #mkTabs {
      width:100%;
      display:grid;
      grid-template-columns:1fr 1fr;
      padding:3px;
      border:1px solid #e2e8f0;
      border-radius:9px;
      background:#f8fafc;
    }
    .mk-tab { width:100%; height:29px; padding:0 5px; font-size:9px; }
    .mk-view-toggle { width:32px; height:32px; flex-basis:32px; border-radius:9px; }
    .mk-view-toggle svg { width:15px; height:15px; }
    .mk-view-toggle .mk-toggle-tooltip { display:none; }

    #mkFilterPanel.open {
      padding-top:8px;
      border-top:1px solid #e2e8f0;
    }
    #mkFilterForm {
      display:grid !important;
      grid-template-columns:minmax(0,.8fr) minmax(0,1fr) minmax(0,1fr) 58px;
      gap:6px !important;
      align-items:end;
      width:100%;
    }
    #mkFieldMode { grid-column:1 / 2; }
    #mkFieldClosing { grid-column:2 / 3; }
    #mkFieldActual { grid-column:3 / 4; }
    #mkFieldArea { grid-column:1 / 4; }
    #mkFieldExport { grid-column:4 / 5; grid-row:2; }
    #mkFilterForm.kolek-mode #mkFieldActual { grid-column:2 / 4; }
    #mkFilterForm.kolek-mode #mkFieldClosing { display:none !important; }

    .mk-input { height:31px; padding:0 6px; border-radius:7px; font-size:9px; }
    select.mk-input { padding-right:22px; background-position:right 5px center; background-size:12px; }
    .mk-label { margin-bottom:2px; font-size:6.8px; letter-spacing:.04em; }
    .mk-actions { gap:5px; }
    .mk-export { width:32px; min-width:32px; height:32px; padding:0; border-radius:9px; }
    .mk-export svg { width:15px; height:15px; }

    .mk-table-shell { border-radius:8px; }
    .mk-table { font-size:9px; }
    #tableKolek { min-width:820px; }
    #tableNpl { min-width:720px; }
    .mk-table th,
    .mk-table td { height:34px; padding:5px 6px; }
    .mk-table thead th { height:34px; font-size:7px; line-height:1.15; }
    .mk-col-code { display:none !important; }
    .mk-col-name {
      left:0 !important;
      z-index:36;
      width:var(--mk-name);
      min-width:var(--mk-name);
      max-width:var(--mk-name);
      white-space:normal;
      line-height:1.15;
    }
    .mk-table thead .mk-col-name { z-index:71; }
    .mk-total-row td.mk-col-name { z-index:66; }
    .mk-name-text { white-space:normal; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
    .mk-money { font-size:8.5px; }
    .mk-noa { margin-top:2px; font-size:6.8px; }
    .mk-pct-badge { min-width:43px; min-height:20px; padding:2px 5px; font-size:7px; }
    .mk-status { min-width:49px; padding:3px 5px; font-size:6.8px; }
  }

  @media (max-width:374px) {
    :root { --mk-name:96px; }
    #mkFilterForm { grid-template-columns:minmax(0,.78fr) minmax(0,1fr) minmax(0,1fr) 54px; gap:4px !important; }
    .mk-input { padding:0 5px; font-size:8px; }
    .mk-actions { gap:4px; }
    .mk-export { width:31px; min-width:31px; height:31px; padding:0; }
    .mk-tab { font-size:8px; }
    #tableKolek { min-width:760px; }
    #tableNpl { min-width:660px; }
  }


  /* === MONITORING KREDIT UI V3: STABLE HEADER, MODERN TABS, RESPONSIVE FILTER === */
  #mkHeaderCard {
    display:grid !important;
    grid-template-columns:minmax(0,1fr);
    align-items:center;
  }
  #mkHeaderLead,
  #mkTabs,
  #mkFilterPanel { min-width:0; }
  #mkTabs {
    max-width:100%;
    overflow:visible;
  }
  .mk-tab-label-short { display:none; }

  .mk-table thead th.mk-head-l { background:#eff6ff; color:#1d4ed8; box-shadow:inset 0 2px 0 #60a5fa,inset 0 -1px 0 #bfdbfe; }
  .mk-table thead th.mk-head-dp { background:#f8fafc; color:#475569; box-shadow:inset 0 2px 0 #94a3b8,inset 0 -1px 0 #cbd5e1; }
  .mk-table thead th.mk-head-kl { background:#fffbeb; color:#b45309; box-shadow:inset 0 2px 0 #fbbf24,inset 0 -1px 0 #fde68a; }
  .mk-table thead th.mk-head-d { background:#fff7ed; color:#c2410c; box-shadow:inset 0 2px 0 #fb923c,inset 0 -1px 0 #fed7aa; }
  .mk-table thead th.mk-head-m { background:#fef2f2; color:#b91c1c; box-shadow:inset 0 2px 0 #f87171,inset 0 -1px 0 #fecaca; }
  .mk-table thead th.mk-head-npl { background:#fff1f2; color:#be123c; box-shadow:inset 0 2px 0 #fb7185,inset 0 -1px 0 #fecdd3; }
  .mk-table thead th.mk-head-porto { background:#eef2ff; color:#3730a3; box-shadow:inset 0 2px 0 #818cf8,inset 0 -1px 0 #c7d2fe; }
  .mk-table thead th.mk-head-pct { background:#ecfeff; color:#0e7490; box-shadow:inset 0 2px 0 #22d3ee,inset 0 -1px 0 #a5f3fc; }

  @media (min-width:1180px) {
    #mkHeaderCard {
      display:flex !important;
      flex-direction:row;
      align-items:center;
      flex-wrap:nowrap;
      column-gap:9px !important;
      padding:9px 11px !important;
    }
    #mkHeaderLead {
      flex:0 1 215px;
      min-width:185px;
      max-width:235px;
    }
    #mkHeaderTitle { font-size:17px !important; }
    #mkHeaderSubtitle {
      font-size:9px !important;
      overflow:visible;
      text-overflow:clip;
      white-space:normal;
      line-height:1.25 !important;
    }
    #mkTabs {
      flex:0 0 auto;
      width:auto;
      padding:3px;
      gap:2px;
    }
    .mk-tab {
      height:31px;
      padding:0 9px;
      gap:5px;
      font-size:9.5px;
    }
    .mk-tab svg { width:13px; height:13px; }
    #mkFilterPanel {
      display:flex !important;
      flex:1 1 auto;
      min-width:0;
      justify-content:flex-end;
      padding:0;
      margin:0;
      border:0;
    }
    #mkFilterForm {
      display:flex !important;
      flex:0 1 auto;
      flex-wrap:nowrap !important;
      align-items:end;
      justify-content:flex-end;
      gap:7px !important;
      min-width:0;
      width:auto !important;
    }
    #mkFilterForm > div { flex:none; min-width:0 !important; }
    #mkFieldMode { width:112px !important; }
    #mkFieldClosing { width:108px !important; }
    #mkFieldActual { width:108px !important; }
    #mkFieldArea { width:205px !important; min-width:170px !important; }
    #mkFieldExport { width:auto !important; }
    .mk-input { height:34px; padding-left:8px; padding-right:8px; font-size:10.5px; }
    select.mk-input { padding-right:25px; }
    .mk-label { font-size:7.5px; }
    .mk-export { width:34px; min-width:34px; height:34px; padding:0; border-radius:9px; }
    #tableKolek,
    #tableNpl { width:100%; min-width:100%; }
  }

  @media (min-width:768px) and (max-width:1179px) {
    #mkHeaderCard { row-gap:9px !important; }
    #mkHeaderLead { grid-row:1; }
    #mkTabs {
      grid-row:2;
      width:max-content;
      max-width:100%;
    }
    #mkFilterPanel {
      grid-row:3;
      width:100%;
    }
    #mkFilterPanel.open {
      display:block !important;
      padding-top:9px;
      border-top:1px solid #e2e8f0;
    }
    #mkFilterForm {
      display:grid !important;
      grid-template-columns:130px 124px 124px minmax(220px,1fr) 82px;
      gap:8px !important;
      align-items:end;
    }
    #mkFilterForm > div { width:auto !important; min-width:0 !important; }
    #mkFieldMode { grid-column:1; }
    #mkFieldClosing { grid-column:2; }
    #mkFieldActual { grid-column:3; }
    #mkFieldArea { grid-column:4; }
    #mkFieldExport { grid-column:5; }
    #mkFilterForm.kolek-mode { grid-template-columns:130px 124px minmax(220px,1fr) 82px; }
    #mkFilterForm.kolek-mode #mkFieldMode { grid-column:1; }
    #mkFilterForm.kolek-mode #mkFieldActual { grid-column:2; }
    #mkFilterForm.kolek-mode #mkFieldArea { grid-column:3; }
    #mkFilterForm.kolek-mode #mkFieldExport { grid-column:4; }
  }

  @media (max-width:767px) {
    #mkHeaderCard { row-gap:8px !important; }
    #mkHeaderLead { grid-row:1; }
    #mkTabs { grid-row:2; }
    #mkFilterPanel { grid-row:3; }
    .mk-tab-label-long { display:none; }
    .mk-tab-label-short { display:inline; overflow:hidden; text-overflow:ellipsis; }
    .mk-tab { min-width:0; overflow:hidden; }

    #mkFilterPanel.open {
      display:block !important;
      padding-top:8px;
      border-top:1px solid #e2e8f0;
    }
    #mkFilterForm,
    #mkFilterForm.kolek-mode {
      display:grid !important;
      grid-template-columns:minmax(82px,.78fr) minmax(0,1.35fr) 69px;
      grid-template-rows:auto auto;
      gap:6px !important;
      align-items:end;
    }
    #mkFilterForm > div { width:auto !important; min-width:0 !important; }

    /* NPL: row 1 closing + actual, row 2 mode + area + export */
    #mkFieldClosing { display:block; grid-column:1; grid-row:1; }
    #mkFieldActual { grid-column:2 / 4; grid-row:1; }
    #mkFieldMode { grid-column:1; grid-row:2; }
    #mkFieldArea { grid-column:2; grid-row:2; }
    #mkFieldExport { grid-column:3; grid-row:2; }

    /* Kolektibilitas: row 1 mode + actual, row 2 area + export */
    #mkFilterForm.kolek-mode #mkFieldClosing { display:none !important; }
    #mkFilterForm.kolek-mode #mkFieldMode { grid-column:1; grid-row:1; }
    #mkFilterForm.kolek-mode #mkFieldActual { grid-column:2 / 4; grid-row:1; }
    #mkFilterForm.kolek-mode #mkFieldArea { grid-column:1 / 3; grid-row:2; }
    #mkFilterForm.kolek-mode #mkFieldExport { grid-column:3; grid-row:2; }

    #tableKolek { min-width:800px; }
    #tableNpl { min-width:700px; }
  }

  @media (max-width:374px) {
    #mkFilterForm,
    #mkFilterForm.kolek-mode {
      grid-template-columns:minmax(76px,.72fr) minmax(0,1.28fr) 66px;
      gap:4px !important;
    }
    .mk-view-toggle,
    .mk-export { width:31px; min-width:31px; height:31px; }
    .mk-tab svg { width:12px; height:12px; }
    .mk-tab { gap:4px; padding:0 4px; }
    #tableKolek { min-width:750px; }
    #tableNpl { min-width:650px; }
  }


  /* === MOBILE COMPACT V7 === */
  .mk-mobile-head-actions {
    display:inline-flex;
    align-items:center;
    justify-content:flex-end;
    gap:5px;
    margin-left:auto;
  }

  @media (max-width:767px) {
    html,
    body {
      max-width:100%;
      overflow-x:hidden !important;
      overscroll-behavior-x:none;
    }

    :root {
      --mk-name:94px;
      --mk-code:0px;
    }

    #monitoringKreditPage {
      width:100% !important;
      max-width:100vw !important;
      min-width:0 !important;
      overflow:hidden !important;
      /*
       * Navbar Monbis berada fixed/sticky di atas pada mobile.
       * Nilai overlap dihitung otomatis lewat JS agar halaman selalu mulai
       * tepat di bawah navbar dan bagian bawah tetap pas di viewport.
       */
      margin-top:var(--mk-nav-overlap, 0px) !important;
      height:calc(100dvh - var(--mk-page-top, 56px)) !important;
      padding:5px !important;
      gap:5px !important;
      scroll-margin-top:var(--mk-page-top, 56px);
    }

    #mkHeaderCard {
      padding:6px 7px !important;
      gap:5px !important;
      row-gap:5px !important;
      border-radius:9px !important;
    }

    #mkHeaderLead {
      gap:6px !important;
      min-height:30px;
    }

    #mkHeaderLead > div:first-child {
      gap:6px !important;
      flex:1 1 auto;
      min-width:0;
    }

    #mkHeaderIcon {
      width:24px !important;
      height:24px !important;
      border-radius:6px !important;
    }
    #mkHeaderIcon svg {
      width:13px !important;
      height:13px !important;
    }
    #mkHeaderTitle {
      font-size:11.5px !important;
      line-height:1.05 !important;
    }
    #mkHeaderSubtitle {
      margin-top:2px !important;
      max-width:none !important;
      font-size:7.4px !important;
      line-height:1.25 !important;
      white-space:normal !important;
      overflow:visible !important;
      text-overflow:clip !important;
      display:-webkit-box;
      -webkit-box-orient:vertical;
      -webkit-line-clamp:2;
    }

    .mk-mobile-head-actions {
      gap:4px;
    }
    .mk-mobile-head-actions .mk-view-toggle {
      display:inline-flex;
      order:1;
      width:29px;
      min-width:29px;
      height:29px;
      flex-basis:29px;
      border-radius:8px;
    }
    .mk-mobile-head-actions .mk-view-toggle svg {
      width:14px;
      height:14px;
    }
    .mk-mobile-head-actions .mk-filter-toggle {
      order:2;
      display:inline-flex;
      height:29px;
      padding:0 8px;
      gap:4px;
      border-radius:8px;
      font-size:8px;
    }
    .mk-mobile-head-actions .mk-filter-toggle svg {
      width:12px;
      height:12px;
    }

    #mkFilterPanel.open {
      padding-top:5px !important;
    }
    #mkFilterForm,
    #mkFilterForm.kolek-mode {
      gap:4px !important;
      grid-template-columns:minmax(72px,.72fr) minmax(0,1.28fr) 34px !important;
    }
    .mk-label {
      margin-bottom:1px !important;
      font-size:6.2px !important;
      line-height:1 !important;
    }
    .mk-input {
      height:28px !important;
      padding:0 5px !important;
      border-radius:6px !important;
      font-size:8px !important;
      line-height:28px !important;
    }
    select.mk-input {
      padding-right:18px !important;
      background-position:right 4px center !important;
      background-size:10px !important;
    }
    #mkFieldExport {
      justify-content:flex-end;
      align-self:end;
    }
    #mkFieldExport .mk-view-toggle {
      display:none !important;
    }
    .mk-actions {
      gap:3px !important;
    }
    .mk-export {
      width:29px !important;
      min-width:29px !important;
      height:29px !important;
      border-radius:7px !important;
    }
    .mk-export svg {
      width:13px !important;
      height:13px !important;
    }

    .mk-table-shell {
      border-radius:7px !important;
    }
    .mk-table {
      font-size:8px !important;
      line-height:1.08 !important;
    }
    /* Exact width = total seluruh kolom. Tidak ada area kosong setelah kolom terakhir. */
    #tableKolek {
      width:674px !important;
      min-width:674px !important;
      max-width:674px !important;
    }
    #tableNpl {
      width:550px !important;
      min-width:550px !important;
      max-width:550px !important;
    }

    #tableKolek col:nth-child(1),
    #tableNpl col:nth-child(1) { width:0 !important; }
    #tableKolek col:nth-child(2),
    #tableNpl col:nth-child(2) { width:94px !important; }

    #tableKolek col:nth-child(n+3):nth-child(-n+7) { width:72px !important; }
    #tableKolek col:nth-child(8) { width:82px !important; }
    #tableKolek col:nth-child(9) { width:86px !important; }
    #tableKolek col:nth-child(10) { width:52px !important; }

    #tableNpl col:nth-child(3),
    #tableNpl col:nth-child(4),
    #tableNpl col:nth-child(5) { width:82px !important; }
    #tableNpl col:nth-child(6),
    #tableNpl col:nth-child(7),
    #tableNpl col:nth-child(8) { width:54px !important; }
    #tableNpl col:nth-child(9) { width:48px !important; }

    .mk-table th,
    .mk-table td {
      height:29px !important;
      padding:3px 4px !important;
    }
    .mk-table thead th {
      height:29px !important;
      padding:3px 3px !important;
      font-size:6.2px !important;
      line-height:1.05 !important;
      letter-spacing:.015em !important;
    }
    .mk-col-name {
      width:var(--mk-name) !important;
      min-width:var(--mk-name) !important;
      max-width:var(--mk-name) !important;
      padding-left:5px !important;
      padding-right:4px !important;
      line-height:1.08 !important;
    }
    .mk-name-text {
      font-size:7.5px !important;
      line-height:1.08 !important;
      -webkit-line-clamp:1 !important;
      white-space:nowrap !important;
      overflow:hidden !important;
      text-overflow:ellipsis !important;
      display:block !important;
    }
    .mk-money {
      font-size:7.7px !important;
      line-height:1 !important;
      letter-spacing:-.02em !important;
    }
    .mk-noa {
      margin-top:1px !important;
      font-size:5.8px !important;
      line-height:1 !important;
    }
    .mk-pct-badge {
      min-width:38px !important;
      min-height:17px !important;
      padding:1px 4px !important;
      font-size:6px !important;
      line-height:1 !important;
    }
    .mk-status {
      min-width:39px !important;
      padding:2px 3px !important;
      font-size:5.8px !important;
      line-height:1 !important;
    }
    .mk-sort-icon {
      margin-left:1px !important;
      font-size:6px !important;
    }
    .mk-total-row td {
      box-shadow:0 2px 4px rgba(15,23,42,.06) !important;
    }
  }

  @media (max-width:374px) {
    :root { --mk-name:86px; }
    #monitoringKreditPage { padding:4px !important; }
    #mkHeaderCard { padding:5px 6px !important; }
    #mkHeaderSubtitle { max-width:none !important; font-size:6.9px !important; line-height:1.2 !important; }
    #mkFilterForm,
    #mkFilterForm.kolek-mode {
      grid-template-columns:minmax(68px,.68fr) minmax(0,1.32fr) 31px !important;
      gap:3px !important;
    }
    .mk-input { height:27px !important; font-size:7.3px !important; }
    .mk-mobile-head-actions .mk-view-toggle,
    .mk-mobile-head-actions .mk-filter-toggle,
    .mk-export {
      height:27px !important;
    }
    .mk-mobile-head-actions .mk-view-toggle,
    .mk-export {
      width:27px !important;
      min-width:27px !important;
      flex-basis:27px !important;
    }
    #tableKolek {
      width:666px !important;
      min-width:666px !important;
      max-width:666px !important;
    }
    #tableNpl {
      width:542px !important;
      min-width:542px !important;
      max-width:542px !important;
    }
    #tableKolek col:nth-child(2),
    #tableNpl col:nth-child(2) { width:86px !important; }
    .mk-table th,
    .mk-table td { padding:3px !important; }
    .mk-money { font-size:7.1px !important; }
  }

</style>

<div id="monitoringKreditPage" class="max-w-[1920px] w-full mx-auto px-2 md:px-4 py-2 md:py-4 h-[calc(100vh-72px)] flex flex-col gap-2 md:gap-3 overflow-hidden bg-slate-50">

  <section id="mkHeaderCard" class="relative z-20 shrink-0 bg-white border border-slate-200 rounded-xl shadow-sm p-2 md:p-3 flex flex-col xl:flex-row xl:items-center gap-2.5 xl:gap-4">
    <div id="mkHeaderLead" class="flex items-center justify-between gap-3 min-w-0 xl:shrink-0">
      <div class="flex items-center gap-2 min-w-0">
        <span id="mkHeaderIcon" class="w-8 h-8 md:w-9 md:h-9 rounded-lg bg-blue-600 text-white shadow-sm flex items-center justify-center shrink-0">
          <svg class="w-4 h-4 md:w-5 md:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
            <path d="M3 10h18"></path>
            <path d="M7 15h4"></path>
            <path d="M17 14v3"></path>
          </svg>
        </span>
        <div class="min-w-0">
          <h1 id="mkHeaderTitle" class="text-base md:text-xl font-extrabold text-slate-800 leading-tight truncate">Monitoring Kredit</h1>
          <p id="mkHeaderSubtitle" class="text-[9px] md:text-[11px] text-slate-500 mt-0.5">Rekap kolektibilitas dan perbandingan NPL</p>
        </div>
      </div>

      <div id="mkHeaderMobileActions" class="mk-mobile-head-actions shrink-0">
        <button type="button" id="mkFilterToggle" class="mk-filter-toggle shrink-0" aria-expanded="false" aria-controls="mkFilterPanel">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>
          <span>Filter</span>
        </button>
      </div>
    </div>


    <div id="mkFilterPanel" class="xl:flex xl:flex-1 xl:justify-end min-w-0">
      <form id="mkFilterForm" class="kolek-mode flex flex-row flex-wrap xl:flex-nowrap items-end gap-2 w-full xl:w-auto" onsubmit="event.preventDefault(); fetchActiveCreditTab(true);">
        <div id="mkFieldMode" class="w-[116px] md:w-[130px] min-w-0">
          <label class="mk-label" for="hitungBerdasarkanCredit">Tipe Saldo</label>
          <select id="hitungBerdasarkanCredit" class="mk-input">
            <option value="baki_debet">BAKI DEBET</option>
            <option value="saldo_bank">SALDO BANK</option>
          </select>
        </div>

        <div id="mkFieldClosing" class="hidden w-[112px] md:w-[124px] min-w-0">
          <label class="mk-label" for="closingDateCredit">Closing (M-1)</label>
          <input type="date" id="closingDateCredit" class="mk-input" required onclick="this.showPicker && this.showPicker()">
        </div>

        <div id="mkFieldActual" class="w-[112px] md:w-[124px] min-w-0">
          <label class="mk-label" for="actualDateCredit">Actual (Harian)</label>
          <input type="date" id="actualDateCredit" class="mk-input" required onclick="this.showPicker && this.showPicker()">
        </div>

        <div id="mkFieldArea" class="flex-1 xl:flex-none xl:w-[250px] min-w-[180px]">
          <label class="mk-label" for="optAreaCredit">Area/Cabang</label>
          <select id="optAreaCredit" class="mk-input truncate"><option value="ALL">Memuat...</option></select>
        </div>

        <div id="mkFieldExport" class="mk-actions shrink-0">
          <button type="button" id="mkViewToggle" class="mk-view-toggle" aria-label="Buka Perbandingan NPL" aria-controls="viewKolek viewNpl" aria-pressed="false" title="Buka Perbandingan NPL">
            <span id="mkViewToggleIcon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.15" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19h16"></path>
                <path d="M7 16v-5"></path>
                <path d="M12 16V7"></path>
                <path d="M17 16v-8"></path>
              </svg>
            </span>
            <span id="mkViewToggleTooltip" class="mk-toggle-tooltip">Buka Perbandingan NPL</span>
          </button>

          <button type="button" class="mk-export" onclick="exportActiveCreditTab()" title="Export Excel (.xlsx)" aria-label="Export Excel">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>
              <path d="M14 2v6h6"></path>
              <path d="M8 13l4 5"></path>
              <path d="M12 13l-4 5"></path>
              <path d="M15 13h2"></path>
              <path d="M15 16h2"></path>
            </svg>
          </button>
        </div>
      </form>
    </div>
  </section>

  <main class="relative flex-1 min-h-0 overflow-hidden">
    <div id="mkLoading" class="mk-loading hidden rounded-xl">
      <div class="mk-spinner"></div>
      <span>Memuat Data</span>
    </div>

    <section id="viewKolek" class="mk-view active" role="region" aria-label="Rekap Kolektibilitas">
      <div id="kolekScroller" class="mk-table-shell mk-scrollbar">
        <table id="tableKolek" class="mk-table">
          <colgroup>
            <col style="width:58px">
            <col style="width:170px">
            <col style="width:112px">
            <col style="width:112px">
            <col style="width:112px">
            <col style="width:112px">
            <col style="width:112px">
            <col style="width:120px">
            <col style="width:126px">
            <col style="width:76px">
          </colgroup>
          <thead id="theadKolekCombined">
            <tr>
              <th class="mk-col-code mk-sort" onclick="sortKolekCombined('kode_unit')">Kode <span class="mk-sort-icon" data-kolek-sort="kode_unit">↕</span></th>
              <th class="mk-col-name mk-sort" onclick="sortKolekCombined('nama_unit')">Nama Kantor <span class="mk-sort-icon" data-kolek-sort="nama_unit">↕</span></th>
              <th class="text-right mk-sort mk-head-l" onclick="sortKolekCombined('bd_L')">Lancar (L) <span class="mk-sort-icon" data-kolek-sort="bd_L">↕</span></th>
              <th class="text-right mk-sort mk-head-dp" onclick="sortKolekCombined('bd_DP')">DPK (DP) <span class="mk-sort-icon" data-kolek-sort="bd_DP">↕</span></th>
              <th class="text-right mk-sort mk-head-kl" onclick="sortKolekCombined('bd_KL')">Kurang Lancar <span class="mk-sort-icon" data-kolek-sort="bd_KL">↕</span></th>
              <th class="text-right mk-sort mk-head-d" onclick="sortKolekCombined('bd_D')">Diragukan (D) <span class="mk-sort-icon" data-kolek-sort="bd_D">↕</span></th>
              <th class="text-right mk-sort mk-head-m" onclick="sortKolekCombined('bd_M')">Macet (M) <span class="mk-sort-icon" data-kolek-sort="bd_M">↕</span></th>
              <th class="text-right mk-sort mk-head-npl" onclick="sortKolekCombined('bd_npl')">Total NPL <span class="mk-sort-icon" data-kolek-sort="bd_npl">↕</span></th>
              <th class="text-right mk-sort mk-head-porto" onclick="sortKolekCombined('total_bd')">Portofolio <span class="mk-sort-icon" data-kolek-sort="total_bd">↕</span></th>
              <th class="text-right mk-sort mk-head-pct" onclick="sortKolekCombined('persentase_npl')">% NPL <span class="mk-sort-icon" data-kolek-sort="persentase_npl">↕</span></th>
            </tr>
          </thead>
          <tbody id="totalKolekCombined" class="mk-total-body"></tbody>
          <tbody id="bodyKolekCombined"></tbody>
        </table>
      </div>
    </section>

    <section id="viewNpl" class="mk-view" role="region" aria-label="Perbandingan NPL">
      <div id="nplScrollerCombined" class="mk-table-shell mk-scrollbar">
        <table id="tableNpl" class="mk-table">
          <colgroup>
            <col style="width:58px">
            <col style="width:170px">
            <col style="width:128px">
            <col style="width:128px">
            <col style="width:128px">
            <col style="width:90px">
            <col style="width:90px">
            <col style="width:95px">
            <col style="width:82px">
          </colgroup>
          <thead id="theadNplCombined">
            <tr>
              <th class="mk-col-code mk-sort" onclick="sortNplCombined('kode_unit')">Kode <span class="mk-sort-icon" data-npl-sort="kode_unit">↕</span></th>
              <th class="mk-col-name mk-sort" onclick="sortNplCombined('nama_unit')">Nama Kantor <span class="mk-sort-icon" data-npl-sort="nama_unit">↕</span></th>
              <th class="text-right mk-sort" onclick="sortNplCombined('npl_closing')">NPL Closing <span class="mk-sort-icon" data-npl-sort="npl_closing">↕</span></th>
              <th class="text-right mk-sort" onclick="sortNplCombined('npl_harian')">NPL Actual <span class="mk-sort-icon" data-npl-sort="npl_harian">↕</span></th>
              <th class="text-right mk-sort" onclick="sortNplCombined('selisih_npl')">Selisih <span class="mk-sort-icon" data-npl-sort="selisih_npl">↕</span></th>
              <th class="text-right mk-sort" onclick="sortNplCombined('npl_closing_persen')">% Closing <span class="mk-sort-icon" data-npl-sort="npl_closing_persen">↕</span></th>
              <th class="text-right mk-sort" onclick="sortNplCombined('npl_harian_persen')">% Actual <span class="mk-sort-icon" data-npl-sort="npl_harian_persen">↕</span></th>
              <th class="text-right mk-sort" onclick="sortNplCombined('selisih_npl_persen')">% Selisih <span class="mk-sort-icon" data-npl-sort="selisih_npl_persen">↕</span></th>
              <th class="text-center">Status</th>
            </tr>
          </thead>
          <tbody id="totalNplCombined" class="mk-total-body"></tbody>
          <tbody id="bodyNplCombined"></tbody>
        </table>
      </div>
    </section>
  </main>
</div>

<script>
(() => {
  'use strict';

  const API_KOLEK = './api/kredit/';
  const API_NPL   = './api/npl/';
  const API_KODE  = './api/kode/';
  const API_DATE  = './api/date/';

  const nfID = new Intl.NumberFormat('id-ID');
  const fmt = value => nfID.format(Number(value || 0));
  const fmt2 = value => Number(value || 0).toLocaleString('id-ID', { minimumFractionDigits:2, maximumFractionDigits:2 });
  const num = value => Number(value || 0);
  const safeText = value => String(value ?? '')
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#039;');

  const state = {
    activeTab:'kolek',
    userKode:'000',
    kantor:[],
    kolekRows:[],
    kolekTotal:null,
    nplRows:[],
    nplTotal:null,
    kolekSort:{ col:null, dir:null },
    nplSort:{ col:null, dir:null },
    abortKolek:null,
    abortNpl:null,
    fetchTimer:null
  };

  const el = id => document.getElementById(id);

  function setLoading(show) {
    el('mkLoading')?.classList.toggle('hidden', !show);
  }

  function closeFilterSmall() {
    if (window.innerWidth >= 1180) return;
    el('mkFilterPanel')?.classList.remove('open');
    syncFilterToggle();
  }

  function syncFilterToggle() {
    const panel = el('mkFilterPanel');
    const btn = el('mkFilterToggle');
    if (!panel || !btn) return;
    const open = panel.classList.contains('open');
    btn.classList.toggle('active', open);
    btn.setAttribute('aria-expanded', String(open));
    const label = btn.querySelector('span');
    if (label) label.textContent = open ? 'Tutup' : 'Filter';
  }

  function toggleFilter() {
    if (window.innerWidth >= 1180) return;
    el('mkFilterPanel')?.classList.toggle('open');
    syncFilterToggle();
  }

  function enableKeyboardSorting() {
    document.querySelectorAll('th.mk-sort').forEach(th => {
      th.setAttribute('tabindex', '0');
      th.setAttribute('role', 'button');
      th.setAttribute('aria-sort', 'none');
      th.addEventListener('keydown', event => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        th.click();
      });
    });
  }

  function updateStickyOffsets() {
    [
      ['theadKolekCombined','kolekScroller'],
      ['theadNplCombined','nplScrollerCombined']
    ].forEach(([headId,scrollId]) => {
      const head = el(headId);
      const scroll = el(scrollId);
      if (head && scroll) scroll.style.setProperty('--mk-head-h', `${Math.max(0, head.offsetHeight - 1)}px`);
    });
  }


  /*
   * Menjaga halaman tidak tertutup navbar aplikasi pada mobile.
   * Fungsi ini tidak bergantung pada satu nama class navbar saja:
   * elemen header/nav fixed atau sticky yang berada di bagian atas akan
   * dideteksi, lalu halaman digeser hanya sebesar bagian yang menindihnya.
   */
  function syncMonitoringCreditNavbarClearance() {
    const page = el('monitoringKreditPage');
    if (!page) return;

    if (window.innerWidth >= 768) {
      page.style.removeProperty('--mk-nav-overlap');
      page.style.removeProperty('--mk-page-top');
      return;
    }

    // Reset margin dahulu supaya pengukuran tidak memakai offset sebelumnya.
    page.style.setProperty('--mk-nav-overlap', '0px');

    requestAnimationFrame(() => {
      const pageRect = page.getBoundingClientRect();
      const baseTop = Math.max(0, pageRect.top);
      let navbarBottom = 0;

      const selectors = [
        'nav',
        'header',
        '[role="banner"]',
        '.navbar',
        '.topbar',
        '.app-navbar',
        '.main-header',
        '#navbar',
        '#topbar',
        '#appHeader'
      ].join(',');

      document.querySelectorAll(selectors).forEach(node => {
        if (!(node instanceof HTMLElement) || node === page || page.contains(node)) return;
        const style = window.getComputedStyle(node);
        if (style.display === 'none' || style.visibility === 'hidden' || Number(style.opacity) === 0) return;
        if (!['fixed', 'sticky'].includes(style.position)) return;

        const rect = node.getBoundingClientRect();
        const isTopBar = rect.height >= 34 && rect.height <= 120 && rect.width >= window.innerWidth * 0.6 && rect.top <= 12 && rect.bottom > 0;
        if (isTopBar) navbarBottom = Math.max(navbarBottom, rect.bottom);
      });

      // Fallback untuk navbar aplikasi yang tidak memakai selector semantik.
      if (navbarBottom <= 0 && baseTop < 40) navbarBottom = 56;

      const targetTop = Math.max(baseTop, navbarBottom);
      const overlap = Math.max(0, Math.ceil(targetTop - baseTop));

      page.style.setProperty('--mk-nav-overlap', `${overlap}px`);
      page.style.setProperty('--mk-page-top', `${Math.max(0, Math.ceil(targetTop))}px`);

      requestAnimationFrame(updateStickyOffsets);
    });
  }

  function areaOptionsHtml(tab, selected = '') {
    if (state.userKode !== '000') {
      return `<option value="CAB-${safeText(state.userKode)}">${safeText(state.userKode)} - Cabang Login</option>`;
    }

    let html = `<option value="ALL">Konsolidasi</option>`;
    html += `
      <option value="KOR-SEMARANG">Korwil Semarang</option>
      <option value="KOR-SOLO">Korwil Solo</option>
      <option value="KOR-BANYUMAS">Korwil Banyumas</option>
      <option value="KOR-PEKALONGAN">Korwil Pekalongan</option>`;

    state.kantor
      .filter(item => item.kode_kantor && String(item.kode_kantor) !== '000')
      .sort((a,b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor)))
      .forEach(item => {
        const kode = String(item.kode_kantor).padStart(3,'0');
        html += `<option value="CAB-${safeText(kode)}">${safeText(kode)} - ${safeText(item.nama_kantor || `Cabang ${kode}`)}</option>`;
      });

    return html;
  }

  function rebuildAreaOptions() {
    const select = el('optAreaCredit');
    if (!select) return;
    const previous = select.value || 'ALL';
    select.innerHTML = areaOptionsHtml(state.activeTab, previous);
    select.disabled = state.userKode !== '000';

    const valid = Array.from(select.options).some(option => option.value === previous);
    if (valid) select.value = previous;
    else select.value = state.userKode !== '000' ? `CAB-${state.userKode}` : 'ALL';
  }

  function syncViewTogglePlacement() {
    const toggle = el('mkViewToggle');
    const mobileHost = el('mkHeaderMobileActions');
    const desktopHost = el('mkFieldExport');
    const filterButton = el('mkFilterToggle');
    const exportButton = desktopHost?.querySelector('.mk-export');
    if (!toggle || !mobileHost || !desktopHost) return;

    if (window.innerWidth < 768) {
      if (toggle.parentElement !== mobileHost) {
        mobileHost.insertBefore(toggle, filterButton || mobileHost.firstChild);
      } else if (filterButton && toggle.nextElementSibling !== filterButton) {
        mobileHost.insertBefore(toggle, filterButton);
      }
    } else {
      if (toggle.parentElement !== desktopHost) {
        desktopHost.insertBefore(toggle, exportButton || desktopHost.firstChild);
      } else if (exportButton && toggle.nextElementSibling !== exportButton) {
        desktopHost.insertBefore(toggle, exportButton);
      }
    }
  }

  function updateTabUI() {
    syncViewTogglePlacement();
    const kolekActive = state.activeTab === 'kolek';
    el('viewKolek')?.classList.toggle('active', kolekActive);
    el('viewNpl')?.classList.toggle('active', !kolekActive);

    const toggle = el('mkViewToggle');
    const icon = el('mkViewToggleIcon');
    const tooltip = el('mkViewToggleTooltip');
    if (toggle && icon) {
      toggle.classList.toggle('is-npl', !kolekActive);
      toggle.setAttribute('aria-pressed', String(!kolekActive));
      toggle.setAttribute('aria-label', kolekActive ? 'Buka Perbandingan NPL' : 'Kembali ke Rekap Kolektibilitas');
      toggle.title = kolekActive ? 'Buka Perbandingan NPL' : 'Kembali ke Rekap Kolektibilitas';
      icon.innerHTML = kolekActive
        ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.15" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19h16"></path><path d="M7 16v-5"></path><path d="M12 16V7"></path><path d="M17 16v-8"></path></svg>`
        : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>`;
      if (tooltip) tooltip.textContent = kolekActive ? 'Buka Perbandingan NPL' : 'Kembali ke Rekap Kolektibilitas';
    }

    el('mkFieldClosing')?.classList.toggle('hidden', kolekActive);
    el('mkFilterForm')?.classList.toggle('kolek-mode', kolekActive);
    el('mkHeaderSubtitle').textContent = kolekActive
      ? 'Posisi kredit berdasarkan kolektibilitas, NOA, dan saldo'
      : 'NPL closing bulan sebelumnya dibanding actual harian';

    rebuildAreaOptions();
    requestAnimationFrame(updateStickyOffsets);
  }


  function getHorizontalScrollMax(scroller) {
    if (!scroller) return 0;
    return Math.max(0, Math.ceil(scroller.scrollWidth - scroller.clientWidth));
  }

  function clampHorizontalScroll(scroller) {
    if (!scroller) return;
    const max = getHorizontalScrollMax(scroller);
    const current = Number(scroller.scrollLeft || 0);
    if (current < 0) {
      scroller.scrollLeft = 0;
    } else if (current > max) {
      scroller.scrollLeft = max;
    }
  }

  function bindHorizontalScrollBounds(scroller) {
    if (!scroller || scroller.dataset.mkScrollBound === '1') return;
    scroller.dataset.mkScrollBound = '1';

    let startX = 0;
    let startY = 0;
    let scrollFrame = 0;

    scroller.addEventListener('scroll', () => {
      cancelAnimationFrame(scrollFrame);
      scrollFrame = requestAnimationFrame(() => clampHorizontalScroll(scroller));
    }, { passive:true });

    scroller.addEventListener('touchstart', event => {
      const touch = event.touches && event.touches[0];
      if (!touch) return;
      startX = touch.clientX;
      startY = touch.clientY;
      clampHorizontalScroll(scroller);
    }, { passive:true });

    scroller.addEventListener('touchmove', event => {
      const touch = event.touches && event.touches[0];
      if (!touch) return;

      const dx = touch.clientX - startX;
      const dy = touch.clientY - startY;
      if (Math.abs(dx) <= Math.abs(dy)) return;

      const max = getHorizontalScrollMax(scroller);
      const atStart = scroller.scrollLeft <= 0.5;
      const atEnd = scroller.scrollLeft >= max - 0.5;

      // Hentikan rubber-band iOS ketika sudah mencapai batas kiri/kanan.
      if ((atStart && dx > 0) || (atEnd && dx < 0)) {
        event.preventDefault();
        scroller.scrollLeft = atStart ? 0 : max;
      }
    }, { passive:false });

    ['touchend','touchcancel','pointerup'].forEach(type => {
      scroller.addEventListener(type, () => {
        requestAnimationFrame(() => clampHorizontalScroll(scroller));
      }, { passive:true });
    });

    clampHorizontalScroll(scroller);
  }

  function initHorizontalScrollBounds() {
    ['kolekScroller','nplScrollerCombined'].forEach(id => {
      bindHorizontalScrollBounds(el(id));
    });
  }

  async function switchTab(tab) {
    if (!['kolek','npl'].includes(tab) || state.activeTab === tab) return;
    state.activeTab = tab;
    updateTabUI();
    closeFilterSmall();

    const scroll = tab === 'kolek' ? el('kolekScroller') : el('nplScrollerCombined');
    if (scroll) {
      scroll.scrollLeft = 0;
      scroll.scrollTop = 0;
      requestAnimationFrame(() => clampHorizontalScroll(scroll));
    }

    if (tab === 'kolek') await fetchKolektibilitasCombined();
    else await fetchNplCombined();
  }

  function scheduleFetch(delay = 250) {
    clearTimeout(state.fetchTimer);
    state.fetchTimer = setTimeout(() => fetchActiveCreditTab(false), delay);
  }

  async function fetchActiveCreditTab(closePanel = false) {
    if (state.activeTab === 'kolek') await fetchKolektibilitasCombined();
    else await fetchNplCombined();
    if (closePanel) closeFilterSmall();
  }

  async function fetchKolektibilitasCombined() {
    const actual = el('actualDateCredit')?.value;
    const area = el('optAreaCredit')?.value || 'ALL';
    const mode = el('hitungBerdasarkanCredit')?.value || 'baki_debet';
    if (!actual) return;

    if (state.abortKolek) state.abortKolek.abort();
    state.abortKolek = new AbortController();
    setLoading(true);

    try {
      const payload = {
        type:'kolektibilitas',
        harian_date:actual,
        kode_kantor:'',
        hitung_berdasarkan:mode
      };
      if (area.startsWith('CAB-')) payload.kode_kantor = area.replace('CAB-','');
      else if (area.startsWith('KOR-')) payload.korwil = area.replace('KOR-','');

      const response = await fetch(API_KOLEK, {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body:JSON.stringify(payload),
        signal:state.abortKolek.signal
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const json = await response.json();

      state.kolekRows = Array.isArray(json.data?.data)
        ? json.data.data
        : (Array.isArray(json.data) ? json.data : []);
      state.kolekTotal = json.data?.grand_total || null;
      state.kolekSort = { col:null, dir:null };
      resetSortIcons('kolek');
      renderKolekTotal();
      renderKolekRows(state.kolekRows);
    } catch (error) {
      if (error.name !== 'AbortError') {
        console.error(error);
        el('bodyKolekCombined').innerHTML = `<tr><td colspan="10" class="mk-empty text-red-500">Gagal memuat data kolektibilitas.</td></tr>`;
        el('totalKolekCombined').innerHTML = '';
      }
    } finally {
      setLoading(false);
      requestAnimationFrame(() => {
        updateStickyOffsets();
        clampHorizontalScroll(el('kolekScroller'));
      });
    }
  }

  async function fetchNplCombined() {
    const closing = el('closingDateCredit')?.value;
    const actual = el('actualDateCredit')?.value;
    const area = el('optAreaCredit')?.value || 'ALL';
    const mode = el('hitungBerdasarkanCredit')?.value || 'baki_debet';
    if (!closing || !actual) return;

    if (state.abortNpl) state.abortNpl.abort();
    state.abortNpl = new AbortController();
    setLoading(true);

    try {
      const payload = {
        type:'NPL',
        closing_date:closing,
        harian_date:actual,
        kode_kantor:'',
        korwil:'',
        hitung_berdasarkan:mode
      };
      if (area.startsWith('CAB-')) payload.kode_kantor = area.replace('CAB-','');
      else if (area.startsWith('KOR-')) payload.korwil = area.replace('KOR-','');

      const response = await fetch(API_NPL, {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body:JSON.stringify(payload),
        signal:state.abortNpl.signal
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const json = await response.json();

      state.nplRows = Array.isArray(json.data?.data)
        ? json.data.data
        : (Array.isArray(json.data) ? json.data : []);
      state.nplTotal = json.data?.grand_total || null;
      state.nplSort = { col:null, dir:null };
      resetSortIcons('npl');
      renderNplTotal();
      renderNplRows(state.nplRows);
    } catch (error) {
      if (error.name !== 'AbortError') {
        console.error(error);
        el('bodyNplCombined').innerHTML = `<tr><td colspan="9" class="mk-empty text-red-500">Gagal memuat data perbandingan NPL.</td></tr>`;
        el('totalNplCombined').innerHTML = '';
      }
    } finally {
      setLoading(false);
      requestAnimationFrame(() => {
        updateStickyOffsets();
        clampHorizontalScroll(el('nplScrollerCombined'));
      });
    }
  }

  function nplPctClass(value) {
    const pct = num(value);
    if (pct > 5) return 'mk-pct-bad';
    if (pct >= 3) return 'mk-pct-warn';
    return 'mk-pct-good';
  }

  function renderMetric(value, noa, tone = '') {
    return `<div class="mk-money ${tone}">${fmt(value)}</div><div class="mk-noa">${fmt(noa)} NOA</div>`;
  }

  function renderKolekTotal() {
    const gt = state.kolekTotal;
    const body = el('totalKolekCombined');
    if (!gt) { body.innerHTML = ''; return; }

    body.innerHTML = `
      <tr class="mk-total-row">
        <td class="mk-col-code"></td>
        <td class="mk-col-name"><div class="mk-name-text">GRAND TOTAL</div></td>
        <td class="text-right">${renderMetric(gt.bd_L,gt.noa_L,'mk-tone-l')}</td>
        <td class="text-right">${renderMetric(gt.bd_DP,gt.noa_DP,'mk-tone-dp')}</td>
        <td class="text-right">${renderMetric(gt.bd_KL,gt.noa_KL,'mk-tone-kl')}</td>
        <td class="text-right">${renderMetric(gt.bd_D,gt.noa_D,'mk-tone-d')}</td>
        <td class="text-right">${renderMetric(gt.bd_M,gt.noa_M,'mk-tone-m')}</td>
        <td class="text-right">${renderMetric(gt.bd_npl,gt.noa_npl,'mk-tone-npl')}</td>
        <td class="text-right">${renderMetric(gt.total_bd,gt.total_noa,'mk-tone-porto')}</td>
        <td class="text-right"><span class="mk-pct-badge ${nplPctClass(gt.persentase_npl)}">${fmt2(gt.persentase_npl)}%</span></td>
      </tr>`;
  }

  function renderKolekRows(rows) {
    const body = el('bodyKolekCombined');
    if (!Array.isArray(rows) || rows.length === 0) {
      body.innerHTML = `<tr><td colspan="10" class="mk-empty">Data kolektibilitas tidak ditemukan.</td></tr>`;
      return;
    }

    body.innerHTML = rows.map(row => {
      const kode = safeText(row.kode_unit || row.kode_cabang || '-');
      const nama = safeText(row.nama_unit || row.nama_kantor || '-');
      return `
        <tr>
          <td class="mk-col-code font-mono text-slate-500">${kode}</td>
          <td class="mk-col-name font-bold text-slate-700" title="${nama}"><div class="mk-name-text">${nama}</div></td>
          <td class="text-right">${renderMetric(row.bd_L,row.noa_L,'mk-tone-l')}</td>
          <td class="text-right">${renderMetric(row.bd_DP,row.noa_DP,'mk-tone-dp')}</td>
          <td class="text-right">${renderMetric(row.bd_KL,row.noa_KL,'mk-tone-kl')}</td>
          <td class="text-right">${renderMetric(row.bd_D,row.noa_D,'mk-tone-d')}</td>
          <td class="text-right">${renderMetric(row.bd_M,row.noa_M,'mk-tone-m')}</td>
          <td class="text-right">${renderMetric(row.bd_npl,row.noa_npl,'mk-tone-npl')}</td>
          <td class="text-right">${renderMetric(row.total_bd,row.total_noa,'mk-tone-porto')}</td>
          <td class="text-right"><span class="mk-pct-badge ${nplPctClass(row.persentase_npl)}">${fmt2(row.persentase_npl)}%</span></td>
        </tr>`;
    }).join('');
  }

  function movementValue(value) {
    const number = num(value);
    if (number < 0) return { text:`(${fmt(Math.abs(number))})`, cls:'text-emerald-600 font-extrabold' };
    if (number > 0) return { text:`+${fmt(number)}`, cls:'text-red-600 font-extrabold' };
    return { text:'-', cls:'text-slate-400 font-bold' };
  }

  function movementPct(value) {
    const number = num(value);
    if (number < 0) return { text:`▼ ${fmt2(Math.abs(number))}%`, cls:'text-emerald-600 font-extrabold' };
    if (number > 0) return { text:`▲ ${fmt2(number)}%`, cls:'text-red-600 font-extrabold' };
    return { text:'0,00%', cls:'text-slate-400 font-bold' };
  }

  function statusBadge(value) {
    const number = num(value);
    if (number > 0) return '<span class="mk-status up">Naik</span>';
    if (number < 0) return '<span class="mk-status down">Turun</span>';
    return '<span class="mk-status flat">Tetap</span>';
  }

  function renderNplTotal() {
    const gt = state.nplTotal;
    const body = el('totalNplCombined');
    if (!gt) { body.innerHTML = ''; return; }
    const change = movementValue(gt.selisih_npl);
    const changePct = movementPct(gt.selisih_npl_persen);

    body.innerHTML = `
      <tr class="mk-total-row">
        <td class="mk-col-code"></td>
        <td class="mk-col-name"><div class="mk-name-text">GRAND TOTAL</div></td>
        <td class="text-right">${fmt(gt.npl_closing)}</td>
        <td class="text-right font-extrabold text-blue-800">${fmt(gt.npl_harian)}</td>
        <td class="text-right ${change.cls}">${change.text}</td>
        <td class="text-right">${fmt2(gt.npl_closing_persen)}%</td>
        <td class="text-right font-extrabold text-blue-800">${fmt2(gt.npl_harian_persen)}%</td>
        <td class="text-right ${changePct.cls}">${changePct.text}</td>
        <td class="text-center">${statusBadge(gt.selisih_npl)}</td>
      </tr>`;
  }

  function renderNplRows(rows) {
    const body = el('bodyNplCombined');
    if (!Array.isArray(rows) || rows.length === 0) {
      body.innerHTML = `<tr><td colspan="9" class="mk-empty">Data perbandingan NPL tidak ditemukan.</td></tr>`;
      return;
    }

    body.innerHTML = rows.map(row => {
      const kode = safeText(row.kode_unit || row.kode_cabang || '-');
      const nama = safeText(row.nama_unit || row.nama_kantor || '-');
      const change = movementValue(row.selisih_npl);
      const changePct = movementPct(row.selisih_npl_persen);
      return `
        <tr>
          <td class="mk-col-code font-mono text-slate-500">${kode}</td>
          <td class="mk-col-name font-bold text-slate-700" title="${nama}"><div class="mk-name-text">${nama}</div></td>
          <td class="text-right">${fmt(row.npl_closing)}</td>
          <td class="text-right font-extrabold text-blue-800">${fmt(row.npl_harian)}</td>
          <td class="text-right ${change.cls}">${change.text}</td>
          <td class="text-right text-slate-600">${fmt2(row.npl_closing_persen)}%</td>
          <td class="text-right font-extrabold text-blue-800">${fmt2(row.npl_harian_persen)}%</td>
          <td class="text-right ${changePct.cls}">${changePct.text}</td>
          <td class="text-center">${statusBadge(row.selisih_npl)}</td>
        </tr>`;
    }).join('');
  }

  // === SORTING YANG KONSISTEN UNTUK STRING ANGKA DARI API ===
  // Nilai PDO/MySQL biasanya diterima JavaScript sebagai string. Jika langsung
  // localeCompare, angka seperti 9.000 akan dianggap lebih besar dari 80.000.
  // Semua kolom nominal/persen dipaksa menjadi number sebelum dibandingkan.
  const creditSortTypes = {
    kolek: {
      kode_unit:'code', nama_unit:'text',
      bd_L:'number', bd_DP:'number', bd_KL:'number', bd_D:'number', bd_M:'number',
      bd_npl:'number', total_bd:'number', persentase_npl:'number'
    },
    npl: {
      kode_unit:'code', nama_unit:'text',
      npl_closing:'number', npl_harian:'number', selisih_npl:'number',
      npl_closing_persen:'number', npl_harian_persen:'number', selisih_npl_persen:'number'
    }
  };

  function sortableNumber(value) {
    if (typeof value === 'number') return Number.isFinite(value) ? value : 0;
    if (value === null || value === undefined || value === '') return 0;

    let raw = String(value).trim().replace(/\s+/g, '').replace(/[^0-9,\.\-+eE]/g, '');
    if (!raw) return 0;

    const commaCount = (raw.match(/,/g) || []).length;
    const dotCount = (raw.match(/\./g) || []).length;

    if (commaCount && dotCount) {
      // Separator paling kanan dianggap desimal.
      if (raw.lastIndexOf(',') > raw.lastIndexOf('.')) {
        raw = raw.replace(/\./g, '').replace(',', '.');
      } else {
        raw = raw.replace(/,/g, '');
      }
    } else if (commaCount) {
      // Format Indonesia: 29,20 atau 1.234.567,89.
      if (commaCount > 1) raw = raw.replace(/,/g, '');
      else raw = raw.replace(',', '.');
    } else if (dotCount > 1 && /^[-+]?\d{1,3}(\.\d{3})+$/.test(raw)) {
      // Format ribuan Indonesia tanpa desimal: 243.016.279.823.
      raw = raw.replace(/\./g, '');
    }

    const parsed = Number(raw);
    return Number.isFinite(parsed) ? parsed : 0;
  }

  function creditSortValue(row, column, type) {
    if (type === 'code') {
      return String(row?.[column] ?? row?.kode_cabang ?? row?.kode_unit ?? '').trim();
    }
    if (type === 'text') {
      return String(row?.[column] ?? row?.nama_kantor ?? row?.nama_unit ?? '').trim();
    }
    return sortableNumber(row?.[column]);
  }

  function sortCreditRows(rows, reportType, column, direction) {
    const type = creditSortTypes[reportType]?.[column] || 'text';
    const multiplier = direction === 'asc' ? 1 : -1;

    return rows
      .map((row, originalIndex) => ({ row, originalIndex }))
      .sort((left, right) => {
        const av = creditSortValue(left.row, column, type);
        const bv = creditSortValue(right.row, column, type);
        let result = 0;

        if (type === 'number') {
          result = av - bv;
        } else if (type === 'code') {
          result = String(av).localeCompare(String(bv), 'id', {
            numeric:true,
            sensitivity:'base'
          });
        } else {
          result = String(av).localeCompare(String(bv), 'id', {
            numeric:true,
            sensitivity:'base',
            ignorePunctuation:true
          });
        }

        // Urutan stabil saat dua nilai sama: kembali ke urutan asli API.
        if (result === 0) result = left.originalIndex - right.originalIndex;
        return result * multiplier;
      })
      .map(item => item.row);
  }

  function defaultSortDirection(reportType, column) {
    const type = creditSortTypes[reportType]?.[column] || 'text';
    return type === 'number' ? 'desc' : 'asc';
  }

  function resetSortIcons(type) {
    document.querySelectorAll(`[data-${type}-sort]`).forEach(icon => {
      icon.textContent = '↕';
      const th = icon.closest('th');
      if (th) th.setAttribute('aria-sort', 'none');
    });
  }

  function updateSortIndicator(type, column, direction) {
    resetSortIcons(type);
    const icon = document.querySelector(`[data-${type}-sort="${column}"]`);
    if (!icon) return;
    icon.textContent = direction === 'asc' ? '↑' : '↓';
    const th = icon.closest('th');
    if (th) th.setAttribute('aria-sort', direction === 'asc' ? 'ascending' : 'descending');
  }

  window.sortKolekCombined = function(column) {
    if (!Array.isArray(state.kolekRows) || !state.kolekRows.length) return;

    const sort = state.kolekSort;
    const nextDirection = sort.col === column
      ? (sort.dir === 'asc' ? 'desc' : 'asc')
      : defaultSortDirection('kolek', column);

    state.kolekSort = { col:column, dir:nextDirection };
    updateSortIndicator('kolek', column, nextDirection);
    renderKolekRows(sortCreditRows(state.kolekRows, 'kolek', column, nextDirection));
  };

  window.sortNplCombined = function(column) {
    if (!Array.isArray(state.nplRows) || !state.nplRows.length) return;

    const sort = state.nplSort;
    const nextDirection = sort.col === column
      ? (sort.dir === 'asc' ? 'desc' : 'asc')
      : defaultSortDirection('npl', column);

    state.nplSort = { col:column, dir:nextDirection };
    updateSortIndicator('npl', column, nextDirection);
    renderNplRows(sortCreditRows(state.nplRows, 'npl', column, nextDirection));
  };

  function xlsxEscape(value) {
    return String(value ?? '')
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&apos;');
  }

  function xlsxColumnName(index) {
    let result = '';
    let number = index;
    while (number > 0) {
      const remainder = (number - 1) % 26;
      result = String.fromCharCode(65 + remainder) + result;
      number = Math.floor((number - 1) / 26);
    }
    return result;
  }

  const xlsxCrcTable = (() => {
    const table = new Uint32Array(256);
    for (let n = 0; n < 256; n++) {
      let c = n;
      for (let k = 0; k < 8; k++) c = (c & 1) ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1);
      table[n] = c >>> 0;
    }
    return table;
  })();

  function xlsxCrc32(bytes) {
    let crc = 0xFFFFFFFF;
    for (let i = 0; i < bytes.length; i++) crc = xlsxCrcTable[(crc ^ bytes[i]) & 0xFF] ^ (crc >>> 8);
    return (crc ^ 0xFFFFFFFF) >>> 0;
  }

  function xlsxU16(value) {
    const out = new Uint8Array(2);
    new DataView(out.buffer).setUint16(0, value, true);
    return out;
  }

  function xlsxU32(value) {
    const out = new Uint8Array(4);
    new DataView(out.buffer).setUint32(0, value >>> 0, true);
    return out;
  }

  function xlsxConcat(parts) {
    const length = parts.reduce((sum, part) => sum + part.length, 0);
    const output = new Uint8Array(length);
    let offset = 0;
    parts.forEach(part => { output.set(part, offset); offset += part.length; });
    return output;
  }

  function xlsxDosDateTime(date = new Date()) {
    const year = Math.max(1980, date.getFullYear());
    return {
      time:(date.getHours() << 11) | (date.getMinutes() << 5) | Math.floor(date.getSeconds() / 2),
      date:((year - 1980) << 9) | ((date.getMonth() + 1) << 5) | date.getDate()
    };
  }

  function xlsxCreateZip(entries) {
    const encoder = new TextEncoder();
    const localParts = [];
    const centralParts = [];
    let localOffset = 0;
    const stamp = xlsxDosDateTime();

    entries.forEach(entry => {
      const nameBytes = encoder.encode(entry.name);
      const dataBytes = typeof entry.data === 'string' ? encoder.encode(entry.data) : entry.data;
      const crc = xlsxCrc32(dataBytes);
      const localHeader = xlsxConcat([
        xlsxU32(0x04034b50), xlsxU16(20), xlsxU16(0), xlsxU16(0),
        xlsxU16(stamp.time), xlsxU16(stamp.date), xlsxU32(crc),
        xlsxU32(dataBytes.length), xlsxU32(dataBytes.length),
        xlsxU16(nameBytes.length), xlsxU16(0), nameBytes
      ]);
      localParts.push(localHeader, dataBytes);

      const centralHeader = xlsxConcat([
        xlsxU32(0x02014b50), xlsxU16(20), xlsxU16(20), xlsxU16(0), xlsxU16(0),
        xlsxU16(stamp.time), xlsxU16(stamp.date), xlsxU32(crc),
        xlsxU32(dataBytes.length), xlsxU32(dataBytes.length),
        xlsxU16(nameBytes.length), xlsxU16(0), xlsxU16(0), xlsxU16(0),
        xlsxU16(0), xlsxU32(0), xlsxU32(localOffset), nameBytes
      ]);
      centralParts.push(centralHeader);
      localOffset += localHeader.length + dataBytes.length;
    });

    const centralDirectory = xlsxConcat(centralParts);
    const endRecord = xlsxConcat([
      xlsxU32(0x06054b50), xlsxU16(0), xlsxU16(0),
      xlsxU16(entries.length), xlsxU16(entries.length),
      xlsxU32(centralDirectory.length), xlsxU32(localOffset), xlsxU16(0)
    ]);
    return xlsxConcat([...localParts, centralDirectory, endRecord]);
  }

  function xlsxCellXml(cell, columnIndex, rowIndex) {
    const ref = `${xlsxColumnName(columnIndex)}${rowIndex}`;
    const style = Number(cell.style || 0);
    if (cell.type === 'n') {
      const value = Number(cell.value || 0);
      return `<c r="${ref}" s="${style}"><v>${Number.isFinite(value) ? value : 0}</v></c>`;
    }
    const text = xlsxEscape(cell.value ?? '');
    return `<c r="${ref}" s="${style}" t="inlineStr"><is><t xml:space="preserve">${text}</t></is></c>`;
  }

  function downloadRealXlsx({ filename, sheetName, columns, rows }) {
    const safeSheet = String(sheetName || 'Data').replace(/[\\/*?:\[\]]/g,' ').slice(0,31) || 'Data';
    const rowXml = [];
    const headerCells = columns.map((column,index) => xlsxCellXml({ value:column.title, type:'s', style:1 }, index + 1, 1)).join('');
    rowXml.push(`<row r="1" ht="24" customHeight="1">${headerCells}</row>`);
    rows.forEach((row,rowIndex) => {
      const cells = row.map((cell,columnIndex) => xlsxCellXml(cell, columnIndex + 1, rowIndex + 2)).join('');
      rowXml.push(`<row r="${rowIndex + 2}">${cells}</row>`);
    });

    const columnXml = columns.map((column,index) => `<col min="${index + 1}" max="${index + 1}" width="${Number(column.width || 14)}" customWidth="1"/>`).join('');
    const finalRow = rows.length + 1;
    const finalColumn = xlsxColumnName(columns.length);
    const sheetXml = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>
  <sheetFormatPr defaultRowHeight="18"/>
  <cols>${columnXml}</cols>
  <sheetData>${rowXml.join('')}</sheetData>
  <autoFilter ref="A1:${finalColumn}${finalRow}"/>
</worksheet>`;

    const stylesXml = `<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="3">
    <font><sz val="10"/><name val="Calibri"/><family val="2"/></font>
    <font><b/><color rgb="FFFFFFFF"/><sz val="10"/><name val="Calibri"/><family val="2"/></font>
    <font><b/><color rgb="FF1E3A8A"/><sz val="10"/><name val="Calibri"/><family val="2"/></font>
  </fonts>
  <fills count="4">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF2563EB"/><bgColor indexed="64"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFEFF6FF"/><bgColor indexed="64"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/><diagonal/></border>
    <border><left style="thin"><color rgb="FFDCE6F1"/></left><right style="thin"><color rgb="FFDCE6F1"/></right><top style="thin"><color rgb="FFDCE6F1"/></top><bottom style="thin"><color rgb="FFDCE6F1"/></bottom><diagonal/></border>
  </borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="10">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
    <xf numFmtId="3" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <xf numFmtId="10" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <xf numFmtId="0" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
    <xf numFmtId="3" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <xf numFmtId="10" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyNumberFormat="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
    <xf numFmtId="0" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  </cellXfs>
  <cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>
</styleSheet>`;

    const now = new Date().toISOString();
    const entries = [
      { name:'[Content_Types].xml', data:`<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>` },
      { name:'_rels/.rels', data:`<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>` },
      { name:'docProps/core.xml', data:`<?xml version="1.0" encoding="UTF-8" standalone="yes"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:creator>Monbis</dc:creator><cp:lastModifiedBy>Monbis</cp:lastModifiedBy><dcterms:created xsi:type="dcterms:W3CDTF">${now}</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">${now}</dcterms:modified></cp:coreProperties>` },
      { name:'docProps/app.xml', data:`<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Monbis</Application><TitlesOfParts><vt:vector size="1" baseType="lpstr"><vt:lpstr>${xlsxEscape(safeSheet)}</vt:lpstr></vt:vector></TitlesOfParts></Properties>` },
      { name:'xl/workbook.xml', data:`<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="${xlsxEscape(safeSheet)}" sheetId="1" r:id="rId1"/></sheets></workbook>` },
      { name:'xl/_rels/workbook.xml.rels', data:`<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>` },
      { name:'xl/styles.xml', data:stylesXml },
      { name:'xl/worksheets/sheet1.xml', data:sheetXml }
    ];

    const zipBytes = xlsxCreateZip(entries);
    const blob = new Blob([zipBytes], { type:'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename.endsWith('.xlsx') ? filename : `${filename}.xlsx`;
    document.body.appendChild(link);
    link.click();
    link.remove();
    setTimeout(() => URL.revokeObjectURL(url),1000);
  }

  function exportKolekCombined() {
    if (!state.kolekRows.length) return alert('Data kolektibilitas kosong.');
    const columns = [
      { title:'KODE', width:10 }, { title:'NAMA KANTOR', width:27 },
      { title:'LANCAR (OS)', width:18 }, { title:'LANCAR (NOA)', width:14 },
      { title:'DPK (OS)', width:18 }, { title:'DPK (NOA)', width:14 },
      { title:'KL (OS)', width:18 }, { title:'KL (NOA)', width:14 },
      { title:'D (OS)', width:18 }, { title:'D (NOA)', width:14 },
      { title:'M (OS)', width:18 }, { title:'M (NOA)', width:14 },
      { title:'TOTAL NPL (OS)', width:20 }, { title:'TOTAL NPL (NOA)', width:17 },
      { title:'PORTOFOLIO (OS)', width:20 }, { title:'PORTOFOLIO (NOA)', width:17 },
      { title:'% NPL', width:12 }
    ];
    const makeRow = (row,kode,nama,total = false) => {
      const textStyle = total ? 5 : 2;
      const codeStyle = total ? 9 : 8;
      const numberStyle = total ? 6 : 3;
      const percentStyle = total ? 7 : 4;
      return [
        { value:kode, type:'s', style:codeStyle }, { value:nama, type:'s', style:textStyle },
        { value:num(row.bd_L), type:'n', style:numberStyle }, { value:num(row.noa_L), type:'n', style:numberStyle },
        { value:num(row.bd_DP), type:'n', style:numberStyle }, { value:num(row.noa_DP), type:'n', style:numberStyle },
        { value:num(row.bd_KL), type:'n', style:numberStyle }, { value:num(row.noa_KL), type:'n', style:numberStyle },
        { value:num(row.bd_D), type:'n', style:numberStyle }, { value:num(row.noa_D), type:'n', style:numberStyle },
        { value:num(row.bd_M), type:'n', style:numberStyle }, { value:num(row.noa_M), type:'n', style:numberStyle },
        { value:num(row.bd_npl), type:'n', style:numberStyle }, { value:num(row.noa_npl), type:'n', style:numberStyle },
        { value:num(row.total_bd), type:'n', style:numberStyle }, { value:num(row.total_noa), type:'n', style:numberStyle },
        { value:num(row.persentase_npl) / 100, type:'n', style:percentStyle }
      ];
    };
    const rows = [];
    if (state.kolekTotal) rows.push(makeRow(state.kolekTotal,'','GRAND TOTAL',true));
    state.kolekRows.forEach(row => rows.push(makeRow(row,String(row.kode_unit || row.kode_cabang || '').padStart(3,'0'),row.nama_unit || row.nama_kantor || '',false)));
    downloadRealXlsx({ filename:`Kolektibilitas_${el('actualDateCredit').value}.xlsx`, sheetName:'Rekap Kolektibilitas', columns, rows });
  }

  function exportNplCombined() {
    if (!state.nplRows.length) return alert('Data perbandingan NPL kosong.');
    const columns = [
      { title:'KODE', width:10 }, { title:'NAMA KANTOR', width:27 },
      { title:'NPL CLOSING', width:19 }, { title:'NPL ACTUAL', width:19 },
      { title:'SELISIH', width:19 }, { title:'% CLOSING', width:13 },
      { title:'% ACTUAL', width:13 }, { title:'% SELISIH', width:13 },
      { title:'STATUS', width:12 }
    ];
    const makeRow = (row,kode,nama,total = false) => {
      const textStyle = total ? 5 : 2;
      const codeStyle = total ? 9 : 8;
      const numberStyle = total ? 6 : 3;
      const percentStyle = total ? 7 : 4;
      const status = num(row.selisih_npl) > 0 ? 'NAIK' : num(row.selisih_npl) < 0 ? 'TURUN' : 'TETAP';
      return [
        { value:kode, type:'s', style:codeStyle }, { value:nama, type:'s', style:textStyle },
        { value:num(row.npl_closing), type:'n', style:numberStyle },
        { value:num(row.npl_harian), type:'n', style:numberStyle },
        { value:num(row.selisih_npl), type:'n', style:numberStyle },
        { value:num(row.npl_closing_persen) / 100, type:'n', style:percentStyle },
        { value:num(row.npl_harian_persen) / 100, type:'n', style:percentStyle },
        { value:num(row.selisih_npl_persen) / 100, type:'n', style:percentStyle },
        { value:status, type:'s', style:codeStyle }
      ];
    };
    const rows = [];
    if (state.nplTotal) rows.push(makeRow(state.nplTotal,'','GRAND TOTAL',true));
    state.nplRows.forEach(row => rows.push(makeRow(row,String(row.kode_unit || row.kode_cabang || '').padStart(3,'0'),row.nama_unit || row.nama_kantor || '',false)));
    downloadRealXlsx({ filename:`Perbandingan_NPL_${el('actualDateCredit').value}.xlsx`, sheetName:'Perbandingan NPL', columns, rows });
  }

  window.exportActiveCreditTab = function() {
    if (state.activeTab === 'kolek') exportKolekCombined();
    else exportNplCombined();
  };

  async function initMonitoringKredit() {
    const user = (typeof window.getUser === 'function' && window.getUser()) || null;
    state.userKode = user?.kode ? String(user.kode).padStart(3,'0') : '000';
    if (state.userKode === '099') state.userKode = '000';

    try {
      const [dateResponse,kodeResponse] = await Promise.all([
        fetch(API_DATE),
        fetch(API_KODE, {
          method:'POST',
          headers:{ 'Content-Type':'application/json' },
          body:JSON.stringify({ type:'kode_kantor' })
        })
      ]);

      const dateJson = await dateResponse.json();
      const kodeJson = await kodeResponse.json();
      const today = new Date().toISOString().split('T')[0];
      el('closingDateCredit').value = dateJson.data?.last_closing || today;
      el('actualDateCredit').value = dateJson.data?.last_created || today;
      state.kantor = Array.isArray(kodeJson.data) ? kodeJson.data : [];
    } catch (error) {
      console.error(error);
      const today = new Date().toISOString().split('T')[0];
      el('closingDateCredit').value ||= today;
      el('actualDateCredit').value ||= today;
      state.kantor = [];
    }

    rebuildAreaOptions();
    updateTabUI();
    await fetchKolektibilitasCombined();
  }

  el('mkFilterToggle')?.addEventListener('click', toggleFilter);
  el('mkViewToggle')?.addEventListener('click', () => {
    switchTab(state.activeTab === 'kolek' ? 'npl' : 'kolek');
  });

  ['hitungBerdasarkanCredit','closingDateCredit','actualDateCredit','optAreaCredit'].forEach(id => {
    el(id)?.addEventListener('change', () => {
      if (id === 'closingDateCredit' && state.activeTab === 'kolek') return;
      scheduleFetch();
      if (window.innerWidth < 768) setTimeout(closeFilterSmall,300);
    });
  });

  window.fetchActiveCreditTab = fetchActiveCreditTab;

  window.addEventListener('resize', () => {
    clearTimeout(window.__mkResizeTimer);
    window.__mkResizeTimer = setTimeout(() => {
      if (window.innerWidth >= 1280) {
        el('mkFilterPanel')?.classList.remove('open');
        syncFilterToggle();
      }
      syncViewTogglePlacement();
      syncMonitoringCreditNavbarClearance();
      updateStickyOffsets();
      clampHorizontalScroll(el('kolekScroller'));
      clampHorizontalScroll(el('nplScrollerCombined'));
    },100);
  });

  window.addEventListener('orientationchange', () => {
    setTimeout(syncMonitoringCreditNavbarClearance, 120);
  });

  if (window.visualViewport) {
    window.visualViewport.addEventListener('resize', () => {
      clearTimeout(window.__mkViewportTimer);
      window.__mkViewportTimer = setTimeout(syncMonitoringCreditNavbarClearance, 80);
    });
  }

  window.addEventListener('DOMContentLoaded', () => {
    syncMonitoringCreditNavbarClearance();
    initHorizontalScrollBounds();
    enableKeyboardSorting();
    initMonitoringKredit();
    // Navbar kadang dirender sesudah konten halaman.
    setTimeout(syncMonitoringCreditNavbarClearance, 120);
    setTimeout(syncMonitoringCreditNavbarClearance, 450);
  });
})();
</script>

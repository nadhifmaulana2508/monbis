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

  .mk-view { display:none; min-height:0; height:100%; }
  .mk-view.active { display:flex; flex-direction:column; }

  .mk-table-shell {
    --mk-head-h:38px;
    position:relative;
    height:100%;
    overflow:auto;
    background:#fff;
    border:1px solid var(--mk-border);
    border-radius:10px;
    -webkit-overflow-scrolling:touch;
  }
  .mk-table {
    width:max-content;
    min-width:100%;
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
  .mk-table td:last-child { border-right:0; }

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
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
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
    :root {
      --mk-name:94px;
      --mk-code:0px;
    }

    #monitoringKreditPage {
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
      max-width:172px;
      font-size:6.8px !important;
      line-height:1.15 !important;
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
    #tableKolek {
      min-width:690px !important;
    }
    #tableNpl {
      min-width:570px !important;
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
    #mkHeaderSubtitle { max-width:142px; font-size:6.2px !important; }
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
    #tableKolek { min-width:650px !important; }
    #tableNpl { min-width:540px !important; }
    #tableKolek col:nth-child(2),
    #tableNpl col:nth-child(2) { width:86px !important; }
    .mk-table th,
    .mk-table td { padding:3px !important; }
    .mk-money { font-size:7.1px !important; }
  }



  /* === INFO RINGKASAN KONDISI MONITORING KREDIT === */
  .mk-title-line {
    display:flex;
    align-items:center;
    gap:7px;
    min-width:0;
    max-width:100%;
  }
  .mk-title-line #mkHeaderTitle {
    min-width:0;
    overflow:visible !important;
    text-overflow:clip !important;
    white-space:nowrap !important;
    flex:0 1 auto;
  }

  .mk-info-button {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:20px;
    min-width:20px;
    height:20px;
    padding:0;
    border:1px solid #bfdbfe;
    border-radius:999px;
    background:#eff6ff;
    color:#2563eb;
    cursor:pointer;
    box-shadow:0 1px 2px rgba(15,23,42,.05);
    transition:background .16s ease,border-color .16s ease,color .16s ease,transform .16s ease,box-shadow .16s ease;
    flex:0 0 auto;
  }
  .mk-info-button:hover,
  .mk-info-button[aria-expanded="true"] {
    background:#2563eb;
    border-color:#2563eb;
    color:#fff;
    box-shadow:0 4px 10px rgba(37,99,235,.2);
  }
  .mk-info-button:active { transform:scale(.94); }
  .mk-info-button:focus-visible {
    outline:0;
    box-shadow:0 0 0 3px rgba(37,99,235,.18);
  }
  .mk-info-button svg { width:12px; height:12px; }

  .mk-info-backdrop {
    position:fixed;
    inset:0;
    z-index:99990;
    display:none;
    background:rgba(15,23,42,.56);
    backdrop-filter:blur(4px);
  }
  .mk-info-backdrop.open { display:block; }

  .mk-info-panel {
    position:fixed;
    z-index:100000;
    display:none;
    width:min(820px,calc(100vw - 48px));
    max-height:min(88dvh,760px);
    overflow:auto;
    padding:0;
    border:1px solid #dbe3ee;
    border-radius:14px;
    background:#fff;
    color:#334155;
    box-shadow:0 22px 52px rgba(15,23,42,.22),0 4px 12px rgba(15,23,42,.08);
    overscroll-behavior:contain;
  }
  .mk-info-panel.open { display:block; }

  .mk-info-panel-head {
    position:sticky;
    top:0;
    z-index:3;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    padding:13px 14px 11px;
    border-bottom:1px solid #e2e8f0;
    background:rgba(255,255,255,.97);
    backdrop-filter:blur(8px);
  }
  .mk-info-panel-title {
    margin:0;
    color:#1e293b;
    font-size:14px;
    line-height:1.2;
    font-weight:900;
  }
  .mk-info-panel-subtitle {
    margin:3px 0 0;
    color:#64748b;
    font-size:9.5px;
    line-height:1.4;
    font-weight:650;
  }
  .mk-info-close {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:28px;
    min-width:28px;
    height:28px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    background:#f8fafc;
    color:#64748b;
    font-size:18px;
    line-height:1;
    font-weight:700;
    cursor:pointer;
    transition:all .16s ease;
  }
  .mk-info-close:hover { background:#fee2e2; border-color:#fecaca; color:#dc2626; }

  .mk-info-body { padding:14px 15px 16px; }

  .mk-insight-context {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    margin-bottom:8px;
  }
  .mk-info-mode {
    display:inline-flex;
    align-items:center;
    gap:6px;
    min-width:0;
    padding:5px 8px;
    border:1px solid #bfdbfe;
    border-radius:999px;
    background:#eff6ff;
    color:#1d4ed8;
    font-size:8px;
    line-height:1;
    font-weight:900;
    letter-spacing:.025em;
    text-transform:uppercase;
    white-space:nowrap;
  }
  .mk-info-mode::before {
    content:'';
    width:6px;
    height:6px;
    border-radius:999px;
    background:#2563eb;
    flex:0 0 auto;
  }
  .mk-insight-date {
    min-width:0;
    overflow:hidden;
    color:#64748b;
    font-size:8.5px;
    font-weight:750;
    text-align:right;
    text-overflow:ellipsis;
    white-space:nowrap;
  }

  .mk-insight-hero {
    padding:11px 12px;
    border:1px solid #dbeafe;
    border-radius:11px;
    background:#f8fbff;
  }
  .mk-insight-hero.alert {
    border-color:#fecaca;
    background:#fff7f7;
  }
  .mk-insight-hero.good {
    border-color:#bbf7d0;
    background:#f4fdf7;
  }
  .mk-insight-eyebrow {
    color:#64748b;
    font-size:7.5px;
    font-weight:900;
    letter-spacing:.055em;
    text-transform:uppercase;
  }
  .mk-insight-headline {
    margin-top:3px;
    color:#0f172a;
    font-size:12px;
    font-weight:900;
    line-height:1.35;
  }
  .mk-insight-copy {
    margin-top:5px;
    color:#475569;
    font-size:9.5px;
    font-weight:650;
    line-height:1.5;
  }

  .mk-insight-stats {
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:6px;
    margin-top:8px;
  }
  .mk-insight-stat {
    min-width:0;
    padding:8px;
    border:1px solid #e2e8f0;
    border-radius:9px;
    background:#fff;
  }
  .mk-insight-stat-label {
    color:#64748b;
    font-size:7px;
    font-weight:900;
    letter-spacing:.035em;
    text-transform:uppercase;
  }
  .mk-insight-stat-value {
    margin-top:3px;
    overflow:hidden;
    color:#0f172a;
    font-size:11px;
    font-weight:900;
    line-height:1.1;
    text-overflow:ellipsis;
    white-space:nowrap;
    font-variant-numeric:tabular-nums;
  }
  .mk-insight-stat-value.bad { color:#dc2626; }
  .mk-insight-stat-value.good { color:#059669; }

  .mk-insight-section {
    margin-top:9px;
    padding:10px;
    border:1px solid #e2e8f0;
    border-radius:10px;
    background:#fff;
  }
  .mk-insight-section-title {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    margin:0 0 7px;
    color:#1e293b;
    font-size:10px;
    font-weight:900;
  }
  .mk-insight-section-note {
    color:#94a3b8;
    font-size:7.5px;
    font-weight:750;
    white-space:nowrap;
  }
  .mk-driver-list {
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:7px;
  }
  .mk-driver-item {
    display:grid;
    grid-template-columns:23px minmax(0,1fr) auto;
    align-items:center;
    gap:7px;
    min-width:0;
    padding:7px 8px;
    border:1px solid #eef2f7;
    border-radius:8px;
    background:#f8fafc;
  }
  .mk-driver-rank {
    display:flex;
    align-items:center;
    justify-content:center;
    width:23px;
    height:23px;
    border-radius:7px;
    background:#fee2e2;
    color:#b91c1c;
    font-size:8px;
    font-weight:900;
  }
  .mk-driver-name {
    overflow:hidden;
    color:#334155;
    font-size:9.5px;
    font-weight:850;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .mk-driver-meta {
    margin-top:2px;
    overflow:hidden;
    color:#94a3b8;
    font-size:7.5px;
    font-weight:700;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .mk-driver-change {
    color:#dc2626;
    font-size:9px;
    font-weight:900;
    text-align:right;
    white-space:nowrap;
    font-variant-numeric:tabular-nums;
  }
  .mk-driver-empty {
    padding:10px;
    border:1px dashed #cbd5e1;
    border-radius:8px;
    background:#f8fafc;
    color:#64748b;
    font-size:9px;
    font-weight:700;
    line-height:1.45;
    text-align:center;
  }

  .mk-action-list {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:9px;
  }
  .mk-action-item {
    display:grid;
    grid-template-columns:30px minmax(0,1fr);
    gap:9px;
    align-items:start;
    min-width:0;
    padding:10px 11px;
    border:1px solid #e2e8f0;
    border-left-width:4px;
    border-radius:10px;
    background:#f8fafc;
    box-shadow:0 1px 2px rgba(15,23,42,.035);
  }
  .mk-action-item.priority-potential { border-left-color:#2563eb; background:#f8fbff; }
  .mk-action-item.priority-flow { border-left-color:#7c3aed; background:#fcfaff; }
  .mk-action-item.priority-top25 { grid-column:1 / -1; border-left-color:#e11d48; background:#fff9fa; }
  .mk-action-icon {
    display:flex;
    align-items:center;
    justify-content:center;
    width:30px;
    height:30px;
    border-radius:8px;
    background:#e2e8f0;
    color:#334155;
    font-size:10px;
    font-weight:900;
  }
  .priority-potential .mk-action-icon { background:#dbeafe; color:#1d4ed8; }
  .priority-flow .mk-action-icon { background:#ede9fe; color:#6d28d9; }
  .priority-top25 .mk-action-icon { background:#ffe4e6; color:#be123c; }
  .mk-action-title {
    color:#334155;
    font-size:9.5px;
    font-weight:900;
    line-height:1.2;
  }
  .mk-action-copy {
    margin-top:3px;
    color:#64748b;
    font-size:9px;
    font-weight:650;
    line-height:1.45;
  }
  .mk-action-tags {
    display:flex;
    flex-wrap:wrap;
    gap:5px;
    margin-top:7px;
  }
  .mk-action-tags span {
    display:inline-flex;
    align-items:center;
    min-height:21px;
    padding:3px 7px;
    border:1px solid #fecdd3;
    border-radius:999px;
    background:#fff1f2;
    color:#9f1239;
    font-size:7.5px;
    font-weight:900;
    line-height:1;
    white-space:nowrap;
  }
  .mk-insight-footnote {
    margin-top:8px;
    padding:8px 9px;
    border:1px solid #fde68a;
    border-radius:9px;
    background:#fffbeb;
    color:#92400e;
    font-size:8.5px;
    font-weight:700;
    line-height:1.45;
  }

  @media (min-width:1180px) {
    #mkHeaderLead {
      flex:0 0 260px !important;
      min-width:245px !important;
      max-width:280px !important;
    }
  }

  @media (max-width:767px) {
    #mkHeaderLead > div:first-child {
      flex:1 1 auto;
      min-width:0;
    }
    .mk-title-line {
      gap:5px;
      overflow:visible;
    }
    .mk-title-line #mkHeaderTitle {
      font-size:11px !important;
      letter-spacing:-.01em;
      white-space:nowrap !important;
    }
    .mk-info-button {
      width:18px;
      min-width:18px;
      height:18px;
    }
    .mk-info-button svg { width:10px; height:10px; }

    .mk-info-backdrop.open {
      background:rgba(15,23,42,.42);
      backdrop-filter:blur(2px);
    }
    .mk-info-panel {
      left:0 !important;
      right:0 !important;
      bottom:0 !important;
      top:auto !important;
      width:100% !important;
      max-height:88dvh !important;
      transform:none !important;
      border-left:0;
      border-right:0;
      border-bottom:0;
      border-radius:16px 16px 0 0;
      box-shadow:0 -18px 46px rgba(15,23,42,.25);
    }
    .mk-info-panel-head { padding:11px 12px 9px; }
    .mk-info-body { padding:9px 10px 13px; }
    .mk-info-panel-title { font-size:12px; }
    .mk-info-panel-subtitle { font-size:8px; }
    .mk-insight-context { margin-bottom:7px; }
    .mk-info-mode { font-size:7px; padding:4px 7px; }
    .mk-insight-date { font-size:7px; }
    .mk-insight-hero { padding:9px 10px; }
    .mk-insight-headline { font-size:10.5px; }
    .mk-insight-copy { font-size:8.5px; line-height:1.42; }
    .mk-insight-stats { gap:4px; }
    .mk-insight-stat { padding:6px; border-radius:7px; }
    .mk-insight-stat-label { font-size:6px; }
    .mk-insight-stat-value { font-size:9px; }
    .mk-insight-section { margin-top:7px; padding:8px; }
    .mk-insight-section-title { font-size:9px; }
    .mk-insight-section-note { font-size:6.5px; }
    .mk-driver-list { grid-template-columns:1fr; }
    .mk-driver-item { grid-template-columns:20px minmax(0,1fr) auto; gap:6px; padding:6px; }
    .mk-driver-rank { width:20px; height:20px; font-size:7px; }
    .mk-driver-name { font-size:8.5px; }
    .mk-driver-meta { font-size:6.5px; }
    .mk-driver-change { font-size:8px; }
    .mk-action-list { grid-template-columns:1fr; gap:6px; }
    .mk-action-item,
    .mk-action-item.priority-top25 { grid-column:auto; grid-template-columns:23px minmax(0,1fr); padding:7px; }
    .mk-action-icon { width:23px; height:23px; }
    .mk-action-tags { gap:4px; margin-top:5px; }
    .mk-action-tags span { min-height:19px; padding:2px 6px; font-size:6.8px; }
    .mk-action-icon { width:23px; height:23px; }
    .mk-action-title { font-size:8.5px; }
    .mk-action-copy { font-size:7.5px; }
    .mk-insight-footnote { font-size:7.5px; }
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
          <div class="mk-title-line">
            <h1 id="mkHeaderTitle" class="text-base md:text-xl font-extrabold text-slate-800 leading-tight">Monitoring Kredit</h1>
            <button type="button" id="mkInfoButton" class="mk-info-button" aria-label="Buka ringkasan kondisi Monitoring Kredit" aria-controls="mkInfoPanel" aria-expanded="false" title="Ringkasan kondisi Monitoring Kredit">
              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0ZM9 7a1 1 0 102 0 1 1 0 00-2 0Zm1 2a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1Z" clip-rule="evenodd"></path></svg>
            </button>
          </div>
          <p id="mkHeaderSubtitle" class="text-[9px] md:text-[11px] text-slate-500 mt-0.5 truncate">Rekap kolektibilitas dan perbandingan NPL</p>
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

  <div id="mkInfoBackdrop" class="mk-info-backdrop" aria-hidden="true"></div>
  <aside id="mkInfoPanel" class="mk-info-panel mk-scrollbar" role="dialog" aria-modal="false" aria-labelledby="mkInfoTitle" aria-hidden="true">
    <div class="mk-info-panel-head">
      <div>
        <h2 id="mkInfoTitle" class="mk-info-panel-title">Ringkasan Kondisi NPL</h2>
        <p class="mk-info-panel-subtitle">Sorotan posisi saat ini dan prioritas tindak lanjut untuk membantu menjaga NPL sampai akhir bulan.</p>
      </div>
      <button type="button" id="mkInfoClose" class="mk-info-close" aria-label="Tutup ringkasan">&times;</button>
    </div>

    <div class="mk-info-body">
      <div class="mk-insight-context">
        <div id="mkInfoModeBadge" class="mk-info-mode">Kondisi Saat Ini</div>
        <div id="mkInsightDate" class="mk-insight-date">-</div>
      </div>

      <section id="mkInsightHero" class="mk-insight-hero">
        <div class="mk-insight-eyebrow">Ringkasan</div>
        <div id="mkInsightHeadline" class="mk-insight-headline">Memuat ringkasan kondisi kredit...</div>
        <div id="mkInsightCopy" class="mk-insight-copy">Ringkasan mengikuti data dan filter yang sedang aktif pada halaman Monitoring Kredit.</div>
      </section>

      <div class="mk-insight-stats">
        <div class="mk-insight-stat">
          <div id="mkInsightStat1Label" class="mk-insight-stat-label">NPL Actual</div>
          <div id="mkInsightStat1" class="mk-insight-stat-value">-</div>
        </div>
        <div class="mk-insight-stat">
          <div id="mkInsightStat2Label" class="mk-insight-stat-label">Perubahan</div>
          <div id="mkInsightStat2" class="mk-insight-stat-value">-</div>
        </div>
        <div class="mk-insight-stat">
          <div id="mkInsightStat3Label" class="mk-insight-stat-label">Cabang Naik</div>
          <div id="mkInsightStat3" class="mk-insight-stat-value">-</div>
        </div>
      </div>

      <section class="mk-insight-section">
        <div class="mk-insight-section-title">
          <span>Urutan Prioritas Tindak Lanjut</span>
          <span class="mk-insight-section-note">mulai dari pencegahan sampai penyelesaian NPL</span>
        </div>
        <div class="mk-action-list">
          <div class="mk-action-item priority-potential">
            <div class="mk-action-icon">1</div>
            <div>
              <div class="mk-action-title">Potensi NPL</div>
              <div class="mk-action-copy">Cek debitur yang berisiko masuk NPL dan lakukan follow-up lebih awal. Prioritaskan rekening yang masih bisa dicegah agar tidak turun kualitas pada posisi akhir bulan.</div>
            </div>
          </div>
          <div class="mk-action-item priority-flow">
            <div class="mk-action-icon">2</div>
            <div>
              <div class="mk-action-title">Flow PAR</div>
              <div class="mk-action-copy">Telusuri rekening yang berpotensi flow atau memperburuk kolektibilitas. Pastikan tindak lanjut pembayaran dan komitmen cabang dipantau pada posisi harian berikutnya.</div>
            </div>
          </div>
          <div class="mk-action-item priority-top25">
            <div class="mk-action-icon">3</div>
            <div>
              <div class="mk-action-title">Selesaikan 25 NPL Terbesar</div>
              <div class="mk-action-copy">Fokus pada debitur NPL dengan eksposur terbesar karena penyelesaian pada kelompok ini paling cepat membantu menekan nominal dan rasio NPL. Tentukan jalur penyelesaian sesuai kondisi debitur, dokumen, agunan, dasar hukum, dan ketentuan internal.</div>
              <div class="mk-action-tags" aria-label="Alternatif tindak lanjut 25 NPL terbesar">
                <span>Litigasi</span>
                <span>Lelang</span>
                <span>SKK Kejaksaan</span>
                <span>Cessie</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="mk-insight-section">
        <div class="mk-insight-section-title">
          <span id="mkDriverTitle">Prioritas Cabang</span>
          <span id="mkDriverNote" class="mk-insight-section-note">berdasarkan perubahan terbesar</span>
        </div>
        <div id="mkDriverList" class="mk-driver-list">
          <div class="mk-driver-empty">Data prioritas akan muncul setelah data tersedia.</div>
        </div>
      </section>

      <div class="mk-insight-footnote">
        Ringkasan ini merupakan alat bantu monitoring berdasarkan data yang tampil di halaman. Keputusan tindak lanjut tetap mengikuti detail rekening, kondisi debitur, dan ketentuan internal yang berlaku.
      </div>
    </div>
  </aside>

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

  function parseNumeric(value) {
    if (typeof value === 'number') return Number.isFinite(value) ? value : 0;
    if (value === null || value === undefined || value === '') return 0;

    let raw = String(value)
      .trim()
      .replace(/\s+/g, '')
      .replace(/%/g, '')
      .replace(/^\((.*)\)$/, '-$1');

    if (!raw || raw === '-') return 0;

    // Nilai API normal seperti 1234.56 tetap dibaca langsung.
    const direct = Number(raw);
    if (Number.isFinite(direct)) return direct;

    // Mendukung format Indonesia: 1.234.567,89 atau 29,20.
    if (raw.includes('.') && raw.includes(',')) {
      const lastDot = raw.lastIndexOf('.');
      const lastComma = raw.lastIndexOf(',');
      raw = lastComma > lastDot
        ? raw.replace(/\./g, '').replace(',', '.')
        : raw.replace(/,/g, '');
    } else if (raw.includes(',')) {
      raw = raw.replace(/\./g, '').replace(',', '.');
    } else if ((raw.match(/\./g) || []).length > 1) {
      raw = raw.replace(/\./g, '');
    }

    raw = raw.replace(/[^0-9+\-.]/g, '');
    const parsed = Number(raw);
    return Number.isFinite(parsed) ? parsed : 0;
  }

  const num = parseNumeric;
  const fmt = value => nfID.format(num(value));
  const fmt2 = value => num(value).toLocaleString('id-ID', { minimumFractionDigits:2, maximumFractionDigits:2 });
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
    kolekSort:{ col:null, dir:'asc' },
    nplSort:{ col:null, dir:'asc' },
    abortKolek:null,
    abortNpl:null,
    fetchTimer:null
  };

  const el = id => document.getElementById(id);


  let mkInfoOpen = false;

  function selectedAreaLabel() {
    const select = el('optAreaCredit');
    if (!select) return 'Konsolidasi';
    const option = select.options?.[select.selectedIndex];
    return option?.textContent?.trim() || 'Konsolidasi';
  }

  function compactRupiah(value) {
    const n = Math.abs(num(value));
    if (n >= 1e12) return `${(n / 1e12).toLocaleString('id-ID',{maximumFractionDigits:2})} T`;
    if (n >= 1e9) return `${(n / 1e9).toLocaleString('id-ID',{maximumFractionDigits:2})} M`;
    if (n >= 1e6) return `${(n / 1e6).toLocaleString('id-ID',{maximumFractionDigits:2})} Jt`;
    if (n >= 1e3) return `${(n / 1e3).toLocaleString('id-ID',{maximumFractionDigits:1})} Rb`;
    return nfID.format(n);
  }

  function signedPct(value) {
    const n = num(value);
    if (n > 0) return `+${fmt2(n)}%`;
    if (n < 0) return `-${fmt2(Math.abs(n))}%`;
    return '0,00%';
  }

  function signedNominal(value) {
    const n = num(value);
    if (n > 0) return `+Rp ${compactRupiah(n)}`;
    if (n < 0) return `-Rp ${compactRupiah(n)}`;
    return 'Rp 0';
  }

  function formatInsightDate() {
    const closing = el('closingDateCredit')?.value || '';
    const actual = el('actualDateCredit')?.value || '';
    const shortDate = value => {
      if (!value) return '-';
      const parts = value.split('-');
      return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : value;
    };
    return state.activeTab === 'npl'
      ? `${shortDate(closing)} → ${shortDate(actual)}`
      : `Posisi ${shortDate(actual)}`;
  }

  function setInsightStat(id, value, tone = '') {
    const node = el(id);
    if (!node) return;
    node.textContent = value;
    node.classList.remove('bad','good');
    if (tone) node.classList.add(tone);
  }

  function buildNplDriverRows() {
    return [...(state.nplRows || [])]
      .filter(row => num(row.selisih_npl) > 0 || num(row.selisih_npl_persen) > 0)
      .sort((a,b) => {
        const pctDiff = num(b.selisih_npl_persen) - num(a.selisih_npl_persen);
        if (pctDiff !== 0) return pctDiff;
        return num(b.selisih_npl) - num(a.selisih_npl);
      });
  }

  function buildKolekPriorityRows() {
    return [...(state.kolekRows || [])]
      .filter(row => num(row.persentase_npl) > 0)
      .sort((a,b) => {
        const pctDiff = num(b.persentase_npl) - num(a.persentase_npl);
        if (pctDiff !== 0) return pctDiff;
        return num(b.bd_npl) - num(a.bd_npl);
      });
  }

  function renderDriverList(rows, mode = 'npl') {
    const target = el('mkDriverList');
    if (!target) return;

    const top = rows.slice(0,3);
    if (!top.length) {
      target.innerHTML = `
        <div class="mk-driver-empty">
          ${mode === 'npl'
            ? 'Belum ada cabang dengan kenaikan NPL pada data yang sedang ditampilkan.'
            : 'Belum ada data cabang yang dapat dijadikan prioritas pada posisi ini.'}
        </div>`;
      return;
    }

    target.innerHTML = top.map((row,index) => {
      const nama = safeText(row.nama_unit || row.nama_kantor || row.kode_unit || row.kode_cabang || '-');
      const kode = safeText(row.kode_unit || row.kode_cabang || '');
      if (mode === 'npl') {
        return `
          <div class="mk-driver-item">
            <div class="mk-driver-rank">${index + 1}</div>
            <div class="min-w-0">
              <div class="mk-driver-name" title="${nama}">${nama}</div>
              <div class="mk-driver-meta">${kode ? `Kode ${kode} • ` : ''}${signedNominal(row.selisih_npl)} vs closing</div>
            </div>
            <div class="mk-driver-change">${signedPct(row.selisih_npl_persen)}</div>
          </div>`;
      }
      return `
        <div class="mk-driver-item">
          <div class="mk-driver-rank">${index + 1}</div>
          <div class="min-w-0">
            <div class="mk-driver-name" title="${nama}">${nama}</div>
            <div class="mk-driver-meta">${kode ? `Kode ${kode} • ` : ''}NPL Rp ${compactRupiah(row.bd_npl)}</div>
          </div>
          <div class="mk-driver-change">${fmt2(row.persentase_npl)}%</div>
        </div>`;
    }).join('');
  }

  function updateMonitoringInsight() {
    const hero = el('mkInsightHero');
    const headline = el('mkInsightHeadline');
    const copy = el('mkInsightCopy');
    const badge = el('mkInfoModeBadge');
    const date = el('mkInsightDate');
    const driverTitle = el('mkDriverTitle');
    const driverNote = el('mkDriverNote');
    if (!hero || !headline || !copy) return;

    hero.classList.remove('alert','good');
    if (date) date.textContent = `${selectedAreaLabel()} • ${formatInsightDate()}`;

    if (state.activeTab === 'npl') {
      const gt = state.nplTotal;
      const rows = state.nplRows || [];
      const drivers = buildNplDriverRows();
      const risingCount = drivers.length;

      if (badge) badge.textContent = 'Perbandingan NPL';
      if (driverTitle) driverTitle.textContent = 'Kontributor Kenaikan NPL';
      if (driverNote) driverNote.textContent = 'urut % kenaikan terbesar';

      if (!gt && !rows.length) {
        headline.textContent = 'Data perbandingan NPL belum tersedia.';
        copy.textContent = 'Pilih tanggal dan area/cabang, lalu tunggu data selesai dimuat. Ringkasan akan mengikuti posisi yang tampil.';
        setInsightStat('mkInsightStat1','-');
        setInsightStat('mkInsightStat2','-');
        setInsightStat('mkInsightStat3','-');
        renderDriverList([], 'npl');
        return;
      }

      const totalChange = num(gt?.selisih_npl);
      const totalChangePct = num(gt?.selisih_npl_persen);
      const actualPct = num(gt?.npl_harian_persen);

      if (totalChange > 0 || totalChangePct > 0) {
        hero.classList.add('alert');
        headline.textContent = `NPL meningkat dibanding closing. ${risingCount} cabang perlu menjadi perhatian.`;
        copy.textContent = drivers.length
          ? `Kenaikan terbesar saat ini berasal dari ${drivers.slice(0,3).map(row => row.nama_unit || row.nama_kantor || row.kode_unit || row.kode_cabang || '-').join(', ')}. Prioritaskan tindak lanjut agar kenaikan tidak berlanjut sampai akhir bulan: mulai dari Potensi NPL, lanjut Flow PAR, lalu percepat penyelesaian 25 NPL Terbesar.`
          : 'Posisi total NPL meningkat dibanding closing. Lakukan penelusuran rekening penyebab kenaikan sebelum posisi akhir bulan melalui Potensi NPL dan Flow PAR, lalu fokuskan recovery pada 25 NPL Terbesar.';
      } else if (totalChange < 0 || totalChangePct < 0) {
        hero.classList.add('good');
        headline.textContent = 'Posisi NPL membaik dibanding closing.';
        copy.textContent = risingCount
          ? `Secara total NPL turun, tetapi masih ada ${risingCount} cabang yang mengalami kenaikan. Cabang tersebut tetap perlu dipantau agar perbaikan konsolidasi bertahan sampai akhir bulan.`
          : 'Tidak terlihat cabang dengan kenaikan pada data yang tampil. Pertahankan tindak lanjut dan pantau rekening berisiko agar perbaikan tetap terjaga.';
      } else {
        headline.textContent = 'Posisi NPL relatif tetap dibanding closing.';
        copy.textContent = risingCount
          ? `Walaupun total relatif tetap, terdapat ${risingCount} cabang yang meningkat. Gunakan daftar prioritas untuk menentukan follow-up lebih awal.`
          : 'Belum terlihat kenaikan pada data yang tampil. Tetap pantau potensi flow dan rekening berisiko menjelang akhir bulan.';
      }

      el('mkInsightStat1Label').textContent = '% NPL Actual';
      el('mkInsightStat2Label').textContent = '% Perubahan';
      el('mkInsightStat3Label').textContent = 'Cabang Naik';
      setInsightStat('mkInsightStat1', `${fmt2(actualPct)}%`, actualPct > 5 ? 'bad' : '');
      setInsightStat('mkInsightStat2', signedPct(totalChangePct), totalChangePct > 0 ? 'bad' : (totalChangePct < 0 ? 'good' : ''));
      setInsightStat('mkInsightStat3', `${fmt(risingCount)} Cabang`, risingCount > 0 ? 'bad' : 'good');
      renderDriverList(drivers, 'npl');
      return;
    }

    const gt = state.kolekTotal;
    const rows = state.kolekRows || [];
    const priorities = buildKolekPriorityRows();

    if (badge) badge.textContent = 'Kolektibilitas';
    if (driverTitle) driverTitle.textContent = 'Cabang % NPL Tertinggi';
    if (driverNote) driverNote.textContent = 'posisi actual saat ini';

    if (!gt && !rows.length) {
      headline.textContent = 'Data kolektibilitas belum tersedia.';
      copy.textContent = 'Ringkasan akan mengikuti data actual setelah proses pemuatan selesai.';
      setInsightStat('mkInsightStat1','-');
      setInsightStat('mkInsightStat2','-');
      setInsightStat('mkInsightStat3','-');
      renderDriverList([], 'kolek');
      return;
    }

    const nplPct = num(gt?.persentase_npl);
    const nplNominal = num(gt?.bd_npl);
    const nplNoa = num(gt?.noa_npl);

    if (nplPct > 5) {
      hero.classList.add('alert');
      headline.textContent = `NPL saat ini ${fmt2(nplPct)}%. Perlu pengendalian lebih aktif menjelang akhir bulan.`;
      copy.textContent = priorities.length
        ? `Cabang dengan rasio NPL tertinggi antara lain ${priorities.slice(0,3).map(row => row.nama_unit || row.nama_kantor || row.kode_unit || row.kode_cabang || '-').join(', ')}. Gunakan Perbandingan NPL untuk memastikan cabang mana yang sedang mengalami kenaikan.`
        : 'Gunakan Perbandingan NPL untuk melihat perubahan terhadap closing dan menentukan cabang prioritas.';
    } else {
      hero.classList.add('good');
      headline.textContent = `Posisi NPL saat ini ${fmt2(nplPct)}%. Tetap jaga agar tidak memburuk sampai akhir bulan.`;
      copy.textContent = 'Pantau cabang dengan % NPL tertinggi dan gunakan Perbandingan NPL untuk mendeteksi kenaikan lebih awal. Urutan tindak lanjut: Potensi NPL, Flow PAR, kemudian 25 NPL Terbesar.';
    }

    el('mkInsightStat1Label').textContent = '% NPL Saat Ini';
    el('mkInsightStat2Label').textContent = 'Nominal NPL';
    el('mkInsightStat3Label').textContent = 'NOA NPL';
    setInsightStat('mkInsightStat1', `${fmt2(nplPct)}%`, nplPct > 5 ? 'bad' : '');
    setInsightStat('mkInsightStat2', `Rp ${compactRupiah(nplNominal)}`);
    setInsightStat('mkInsightStat3', `${fmt(nplNoa)} NOA`);
    renderDriverList(priorities, 'kolek');
  }

  function syncMonitoringInfoMode() {
    updateMonitoringInsight();
  }

  function positionMonitoringInfo() {
    const button = el('mkInfoButton');
    const panel = el('mkInfoPanel');
    if (!button || !panel || !mkInfoOpen) return;

    if (window.innerWidth < 768) {
      panel.style.left = '0';
      panel.style.right = '0';
      panel.style.top = 'auto';
      panel.style.bottom = '0';
      panel.style.transform = 'none';
      panel.style.width = '100%';
      panel.style.maxHeight = '88dvh';
      return;
    }

    panel.style.right = 'auto';
    panel.style.bottom = 'auto';
    panel.style.left = '50%';
    panel.style.top = '50%';
    panel.style.transform = 'translate(-50%, -50%)';
    panel.style.width = `${Math.min(820, window.innerWidth - 48)}px`;
    panel.style.maxHeight = `${Math.min(Math.floor(window.innerHeight * 0.88), 760)}px`;
  }

  function openMonitoringInfo() {
    const button = el('mkInfoButton');
    const panel = el('mkInfoPanel');
    const backdrop = el('mkInfoBackdrop');
    if (!button || !panel || !backdrop) return;

    mkInfoOpen = true;
    updateMonitoringInsight();
    panel.classList.add('open');
    backdrop.classList.add('open');
    panel.setAttribute('aria-hidden','false');
    backdrop.setAttribute('aria-hidden','false');
    button.setAttribute('aria-expanded','true');
    requestAnimationFrame(() => {
      positionMonitoringInfo();
      el('mkInfoClose')?.focus({ preventScroll:true });
    });
  }

  function closeMonitoringInfo(returnFocus = false) {
    const button = el('mkInfoButton');
    const panel = el('mkInfoPanel');
    const backdrop = el('mkInfoBackdrop');
    mkInfoOpen = false;
    panel?.classList.remove('open');
    backdrop?.classList.remove('open');
    panel?.setAttribute('aria-hidden','true');
    backdrop?.setAttribute('aria-hidden','true');
    button?.setAttribute('aria-expanded','false');
    if (returnFocus) button?.focus({ preventScroll:true });
  }

  function toggleMonitoringInfo() {
    if (mkInfoOpen) closeMonitoringInfo(true);
    else openMonitoringInfo();
  }

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

    syncMonitoringInfoMode();
    rebuildAreaOptions();
    requestAnimationFrame(updateStickyOffsets);
  }

  async function switchTab(tab) {
    if (!['kolek','npl'].includes(tab) || state.activeTab === tab) return;
    state.activeTab = tab;
    updateTabUI();
    if (mkInfoOpen) requestAnimationFrame(positionMonitoringInfo);
    closeFilterSmall();

    const scroll = tab === 'kolek' ? el('kolekScroller') : el('nplScrollerCombined');
    if (scroll) { scroll.scrollLeft = 0; scroll.scrollTop = 0; }

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
      state.kolekSort = { col:null, dir:'asc' };
      resetSortIcons('kolek');
      renderKolekTotal();
      renderKolekRows(state.kolekRows);
      updateMonitoringInsight();
    } catch (error) {
      if (error.name !== 'AbortError') {
        console.error(error);
        el('bodyKolekCombined').innerHTML = `<tr><td colspan="10" class="mk-empty text-red-500">Gagal memuat data kolektibilitas.</td></tr>`;
        el('totalKolekCombined').innerHTML = '';
      }
    } finally {
      setLoading(false);
      requestAnimationFrame(updateStickyOffsets);
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
      state.nplSort = { col:null, dir:'asc' };
      resetSortIcons('npl');
      renderNplTotal();
      renderNplRows(state.nplRows);
      updateMonitoringInsight();
    } catch (error) {
      if (error.name !== 'AbortError') {
        console.error(error);
        el('bodyNplCombined').innerHTML = `<tr><td colspan="9" class="mk-empty text-red-500">Gagal memuat data perbandingan NPL.</td></tr>`;
        el('totalNplCombined').innerHTML = '';
      }
    } finally {
      setLoading(false);
      requestAnimationFrame(updateStickyOffsets);
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

  const sortCollator = new Intl.Collator('id-ID', {
    numeric:true,
    sensitivity:'base',
    ignorePunctuation:true
  });

  const textSortColumns = new Set(['nama_unit']);
  const codeSortColumns = new Set(['kode_unit']);

  function sortColumnType(column) {
    if (textSortColumns.has(column)) return 'text';
    if (codeSortColumns.has(column)) return 'code';
    return 'number';
  }

  function sortValue(row,column) {
    if (column === 'kode_unit') return row?.kode_unit ?? row?.kode_cabang ?? '';
    if (column === 'nama_unit') return row?.nama_unit ?? row?.nama_kantor ?? '';
    return row?.[column];
  }

  function isBlankSortValue(value) {
    return value === null || value === undefined || String(value).trim() === '';
  }

  function compareRows(a,b,column,direction) {
    const av = sortValue(a,column);
    const bv = sortValue(b,column);
    const aBlank = isBlankSortValue(av);
    const bBlank = isBlankSortValue(bv);

    // Nilai kosong selalu ditempatkan paling bawah, baik ASC maupun DESC.
    if (aBlank && !bBlank) return 1;
    if (!aBlank && bBlank) return -1;
    if (aBlank && bBlank) return 0;

    const type = sortColumnType(column);
    if (type === 'text') {
      return sortCollator.compare(String(av), String(bv)) * direction;
    }

    if (type === 'code') {
      const aCode = String(av).trim();
      const bCode = String(bv).trim();
      return sortCollator.compare(aCode, bCode) * direction;
    }

    return (num(av) - num(bv)) * direction;
  }

  function stableSortRows(rows,column,direction) {
    return rows
      .map((row,index) => ({ row,index }))
      .sort((left,right) => {
        const primary = compareRows(left.row,right.row,column,direction);
        if (primary !== 0) return primary;

        // Jika nilainya sama, urutkan berdasarkan kode agar hasil tidak meloncat.
        const codeTie = compareRows(left.row,right.row,'kode_unit',1);
        return codeTie !== 0 ? codeTie : left.index - right.index;
      })
      .map(item => item.row);
  }

  function nextSortDirection(sort,column) {
    if (sort.col === column) return sort.dir === 'asc' ? 'desc' : 'asc';
    return sortColumnType(column) === 'number' ? 'desc' : 'asc';
  }

  function resetSortIcons(type) {
    document.querySelectorAll(`[data-${type}-sort]`).forEach(icon => {
      icon.textContent = '↕';
      icon.closest('th')?.removeAttribute('aria-sort');
    });
  }

  function updateSortIndicator(type,column,direction) {
    resetSortIcons(type);
    const icon = document.querySelector(`[data-${type}-sort="${column}"]`);
    if (!icon) return;
    icon.textContent = direction === 'asc' ? '↑' : '↓';
    icon.closest('th')?.setAttribute('aria-sort', direction === 'asc' ? 'ascending' : 'descending');
  }

  window.sortKolekCombined = function(column) {
    if (!state.kolekRows.length) return;
    const directionName = nextSortDirection(state.kolekSort,column);
    state.kolekSort = { col:column, dir:directionName };
    updateSortIndicator('kolek',column,directionName);
    const direction = directionName === 'asc' ? 1 : -1;
    renderKolekRows(stableSortRows(state.kolekRows,column,direction));
  };

  window.sortNplCombined = function(column) {
    if (!state.nplRows.length) return;
    const directionName = nextSortDirection(state.nplSort,column);
    state.nplSort = { col:column, dir:directionName };
    updateSortIndicator('npl',column,directionName);
    const direction = directionName === 'asc' ? 1 : -1;
    renderNplRows(stableSortRows(state.nplRows,column,direction));
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

  // Deklarasi XML disusun terpisah agar tidak dibaca sebagai pembuka PHP
  // oleh extension atau linter ketika file ini disimpan sebagai .php.
  const XLSX_XML_DECL = '<' + '?xml version="1.0" encoding="UTF-8" standalone="yes"?' + '>';

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
    const sheetXml = `${XLSX_XML_DECL}
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>
  <sheetFormatPr defaultRowHeight="18"/>
  <cols>${columnXml}</cols>
  <sheetData>${rowXml.join('')}</sheetData>
  <autoFilter ref="A1:${finalColumn}${finalRow}"/>
</worksheet>`;

    const stylesXml = `${XLSX_XML_DECL}
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
      { name:'[Content_Types].xml', data:`${XLSX_XML_DECL}<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>` },
      { name:'_rels/.rels', data:`${XLSX_XML_DECL}<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>` },
      { name:'docProps/core.xml', data:`${XLSX_XML_DECL}<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:creator>Monbis</dc:creator><cp:lastModifiedBy>Monbis</cp:lastModifiedBy><dcterms:created xsi:type="dcterms:W3CDTF">${now}</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">${now}</dcterms:modified></cp:coreProperties>` },
      { name:'docProps/app.xml', data:`${XLSX_XML_DECL}<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Monbis</Application><TitlesOfParts><vt:vector size="1" baseType="lpstr"><vt:lpstr>${xlsxEscape(safeSheet)}</vt:lpstr></vt:vector></TitlesOfParts></Properties>` },
      { name:'xl/workbook.xml', data:`${XLSX_XML_DECL}<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="${xlsxEscape(safeSheet)}" sheetId="1" r:id="rId1"/></sheets></workbook>` },
      { name:'xl/_rels/workbook.xml.rels', data:`${XLSX_XML_DECL}<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>` },
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

  el('mkInfoButton')?.addEventListener('click', event => {
    event.preventDefault();
    event.stopPropagation();
    toggleMonitoringInfo();
  });
  el('mkInfoClose')?.addEventListener('click', () => closeMonitoringInfo(true));
  el('mkInfoBackdrop')?.addEventListener('click', () => closeMonitoringInfo(false));
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && mkInfoOpen) closeMonitoringInfo(true);
  });

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

  document.querySelectorAll('.mk-sort').forEach(header => {
    header.setAttribute('tabindex','0');
    header.setAttribute('role','button');
    header.addEventListener('keydown', event => {
      if (event.key !== 'Enter' && event.key !== ' ') return;
      event.preventDefault();
      header.click();
    });
  });

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
      if (mkInfoOpen) requestAnimationFrame(positionMonitoringInfo);
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
    initMonitoringKredit();
    // Navbar kadang dirender sesudah konten halaman.
    setTimeout(syncMonitoringCreditNavbarClearance, 120);
    setTimeout(syncMonitoringCreditNavbarClearance, 450);
  });
})();
</script>

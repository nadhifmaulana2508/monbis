<style>
  :root {
    --mph-primary:#2563eb;
    --mph-bg:#f8fafc;
    --mph-text:#334155;
    --mph-border:#e2e8f0;
    --mph-head:#eef6ff;
    --mph-total:#eff6ff;
    --mph-code:58px;
    --mph-name:176px;
  }

  * { box-sizing:border-box; }
  html, body { width:100%; min-width:0; overflow-x:hidden; }
  body { background:var(--mph-bg); color:var(--mph-text); }

  .mph-scroll::-webkit-scrollbar { width:6px; height:6px; }
  .mph-scroll::-webkit-scrollbar-track { background:#f8fafc; }
  .mph-scroll::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  .mph-scroll::-webkit-scrollbar-thumb:hover { background:#94a3b8; }

  #monitoringPHPage {
    width:100%;
    max-width:1920px;
    height:calc(100dvh - 72px);
    margin:0 auto;
    padding:8px 10px;
    display:flex;
    flex-direction:column;
    gap:8px;
    overflow:hidden;
    font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
  }

  /* HEADER */
  #mphHeader {
    position:relative;
    z-index:30;
    flex:0 0 auto;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    padding:10px 12px;
    border:1px solid var(--mph-border);
    border-radius:12px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.04);
  }
  .mph-brand {
    display:flex;
    align-items:center;
    gap:9px;
    min-width:0;
    flex:0 0 auto;
  }
  .mph-brand-icon {
    width:36px;
    height:36px;
    flex:0 0 36px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:9px;
    background:#2563eb;
    color:#fff;
    box-shadow:0 2px 5px rgba(37,99,235,.18);
  }
  .mph-brand-icon svg { width:18px; height:18px; }
  .mph-brand-copy { min-width:0; }
  .mph-title-line { display:flex; align-items:center; gap:6px; min-width:0; }
  #mphTitle {
    margin:0;
    color:#172033;
    font-size:17px;
    line-height:1.05;
    font-weight:900;
    letter-spacing:-.02em;
    white-space:nowrap;
  }
  #mphSubtitle {
    display:block;
    max-width:300px;
    margin-top:3px;
    overflow:hidden;
    color:#64748b;
    font-size:8.5px;
    line-height:1.2;
    font-weight:650;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .mph-info-btn {
    width:20px;
    height:20px;
    flex:0 0 20px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:0;
    border:1px solid #bfdbfe;
    border-radius:999px;
    background:#eff6ff;
    color:#2563eb;
    font-size:11px;
    font-weight:950;
    cursor:pointer;
    transition:.16s ease;
  }
  .mph-info-btn:hover,
  .mph-info-btn[aria-expanded="true"] { background:#2563eb; color:#fff; border-color:#2563eb; }

  .mph-mobile-actions { display:none; align-items:center; gap:5px; margin-left:auto; }
  .mph-filter-toggle {
    height:30px;
    padding:0 9px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:5px;
    border:1px solid #dbe3ee;
    border-radius:8px;
    background:#fff;
    color:#475569;
    font-size:8.5px;
    font-weight:850;
    cursor:pointer;
  }
  .mph-filter-toggle.open { border-color:#bfdbfe; background:#eff6ff; color:#2563eb; }
  .mph-filter-toggle svg { width:12px; height:12px; }

  .mph-view-toggle {
    position:relative;
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
    cursor:pointer;
    transition:.16s ease;
  }
  .mph-view-toggle:hover { background:#dbeafe; border-color:#93c5fd; transform:translateY(-1px); }
  .mph-view-toggle.is-list { background:#eef2ff; border-color:#c7d2fe; color:#4f46e5; }
  .mph-view-toggle svg { width:17px; height:17px; }
  .mph-tooltip {
    position:absolute;
    right:0;
    top:calc(100% + 6px);
    width:max-content;
    max-width:210px;
    padding:6px 8px;
    border-radius:7px;
    background:#0f172a;
    color:#fff;
    font-size:8.5px;
    font-weight:750;
    line-height:1.2;
    opacity:0;
    visibility:hidden;
    pointer-events:none;
    transform:translateY(-2px);
    transition:.15s ease;
  }
  .mph-view-toggle:hover .mph-tooltip { opacity:1; visibility:visible; transform:translateY(0); }

  /* FILTER */
  #mphFilterPanel { min-width:0; margin-left:auto; }
  .mph-filter-form {
    display:flex;
    align-items:flex-end;
    justify-content:flex-end;
    gap:6px;
    min-width:0;
  }
  .mph-filter-set { display:none; align-items:flex-end; gap:6px; min-width:0; }
  .mph-filter-set.active { display:flex; }
  .mph-field { min-width:0; }
  .mph-field.date { width:122px; }
  .mph-field.office { width:190px; }
  .mph-field.bucket { width:92px; }
  .mph-field.search { width:200px; }
  .mph-label {
    display:block;
    margin:0 0 3px 1px;
    color:#475569;
    font-size:7.5px;
    line-height:1;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
    white-space:nowrap;
  }
  .mph-input {
    width:100%;
    min-width:0;
    height:34px;
    padding:0 8px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    background:#fff;
    color:#334155;
    font-size:10px;
    font-weight:750;
    outline:none;
    transition:.16s ease;
  }
  .mph-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
  select.mph-input {
    appearance:none;
    -webkit-appearance:none;
    padding-right:24px;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 6px center;
    background-size:12px;
  }
  input[type="date"].mph-input { cursor:pointer; }
  input[type="date"].mph-input::-webkit-calendar-picker-indicator { opacity:.65; cursor:pointer; }

  .mph-action-btn {
    width:34px;
    height:34px;
    min-width:34px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border:0;
    border-radius:9px;
    background:#059669;
    color:#fff;
    cursor:pointer;
    transition:.16s ease;
  }
  .mph-action-btn:hover { background:#047857; transform:translateY(-1px); }
  .mph-action-btn svg { width:16px; height:16px; }

  /* VIEW */
  #mphStage { position:relative; flex:1 1 auto; min-height:0; overflow:hidden; }
  .mph-view { display:none; width:100%; height:100%; min-height:0; }
  .mph-view.active { display:flex; flex-direction:column; }
  .mph-table-shell {
    position:relative;
    flex:1 1 auto;
    min-height:0;
    overflow:auto;
    border:1px solid var(--mph-border);
    border-radius:10px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.03);
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
  }
  .mph-loading {
    position:absolute;
    inset:0;
    z-index:110;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    gap:8px;
    background:rgba(255,255,255,.86);
    backdrop-filter:blur(2px);
    color:#2563eb;
    font-size:9px;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
  }
  .mph-loading.hidden { display:none; }
  .mph-spinner { width:30px; height:30px; border:4px solid #dbeafe; border-top-color:#2563eb; border-radius:999px; animation:mphSpin .8s linear infinite; }
  @keyframes mphSpin { to { transform:rotate(360deg); } }

  /* RECOVERY TABLE */
  #recoveryScroller { --rec-head:38px; --rec-code:58px; --rec-name:176px; }
  #tableRecoveryPH {
    width:100%;
    min-width:670px;
    border-collapse:separate;
    border-spacing:0;
    table-layout:fixed;
    color:#334155;
    font-size:11px;
    font-variant-numeric:tabular-nums;
  }
  #tableRecoveryPH th,
  #tableRecoveryPH td {
    height:38px;
    padding:6px 8px;
    border-right:1px solid #eef2f7;
    border-bottom:1px solid #eef2f7;
    vertical-align:middle;
    white-space:nowrap;
  }
  #tableRecoveryPH thead th {
    position:sticky;
    top:0;
    z-index:50;
    background:var(--mph-head);
    color:#1e3a8a;
    font-size:9px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.035em;
    box-shadow:inset 0 -1px 0 #cbd5e1;
  }
  .rec-col-code { width:var(--rec-code); min-width:var(--rec-code); max-width:var(--rec-code); position:sticky; left:0; z-index:35; background:#fff; text-align:center; }
  .rec-col-name { width:var(--rec-name); min-width:var(--rec-name); max-width:var(--rec-name); position:sticky; left:var(--rec-code); z-index:34; background:#fff; box-shadow:4px 0 8px -7px rgba(15,23,42,.8); }
  #tableRecoveryPH thead .rec-col-code { z-index:70; background:#e0f2fe; }
  #tableRecoveryPH thead .rec-col-name { z-index:69; background:#e0f2fe; }
  #tableRecoveryPH .recovery-total td {
    position:sticky;
    top:var(--rec-head);
    z-index:45;
    background:var(--mph-total);
    color:#1e40af;
    font-weight:900;
    border-bottom:2px solid #bfdbfe;
    box-shadow:0 4px 7px -5px rgba(15,23,42,.45);
  }
  #tableRecoveryPH .recovery-total .rec-col-code { z-index:65; background:var(--mph-total); }
  #tableRecoveryPH .recovery-total .rec-col-name { z-index:64; background:var(--mph-total); }
  #tableRecoveryPH tbody:not(#recoveryTotalBody) tr:nth-child(even) td { background:#fbfdff; }
  #tableRecoveryPH tbody:not(#recoveryTotalBody) tr:hover td { background:#f8fafc; }
  #tableRecoveryPH tbody:not(#recoveryTotalBody) tr:hover .rec-col-code,
  #tableRecoveryPH tbody:not(#recoveryTotalBody) tr:hover .rec-col-name { background:#f8fafc; }
  .rec-click { color:#1d4ed8; font-weight:900; cursor:pointer; }
  .rec-click:hover { text-decoration:underline; }

  /* LGD TABLE */
  #lgdScrollerCombined {
    --lgd-head:36px;
    --lgd-norek:116px;
    --lgd-debitur:180px;
  }
  #tablePHLGDCombined {
    width:max-content;
    min-width:100%;
    border-collapse:separate;
    border-spacing:0;
    table-layout:fixed;
    color:#334155;
    font-size:10.5px;
    font-variant-numeric:tabular-nums;
  }
  #tablePHLGDCombined th,
  #tablePHLGDCombined td {
    height:36px;
    padding:5px 7px;
    border-right:1px solid #eef2f7;
    border-bottom:1px solid #eef2f7;
    vertical-align:middle;
    white-space:nowrap;
  }
  #tablePHLGDCombined thead th {
    position:sticky;
    top:0;
    z-index:50;
    background:#f1f5f9;
    color:#334155;
    font-size:8px;
    line-height:1.08;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.025em;
    box-shadow:inset 0 -1px 0 #cbd5e1;
    cursor:pointer;
    user-select:none;
  }
  #tablePHLGDCombined thead th:hover { background:#eaf2ff; }
  .lgd-sort-mark { margin-left:2px; color:#94a3b8; font-size:8px; }
  .lgd-col-office { width:154px; min-width:154px; }
  .lgd-col-rek { width:var(--lgd-norek); min-width:var(--lgd-norek); position:sticky; left:0; z-index:35; background:#fff; }
  .lgd-col-debitur { width:var(--lgd-debitur); min-width:var(--lgd-debitur); position:sticky; left:var(--lgd-norek); z-index:34; background:#fff; box-shadow:4px 0 8px -7px rgba(15,23,42,.8); }
  .lgd-col-address { width:260px; min-width:260px; overflow:hidden; text-overflow:ellipsis; }
  .lgd-col-year { width:70px; min-width:70px; text-align:center; }
  .lgd-col-money { width:116px; min-width:116px; text-align:right; }
  .lgd-col-date { width:98px; min-width:98px; }
  #tablePHLGDCombined thead .lgd-col-rek { z-index:70; background:#e0f2fe; }
  #tablePHLGDCombined thead .lgd-col-debitur { z-index:69; background:#e0f2fe; }
  #tablePHLGDCombined .lgd-total td {
    position:sticky;
    top:var(--lgd-head);
    z-index:45;
    background:var(--mph-total);
    color:#1e40af;
    font-weight:900;
    border-bottom:2px solid #bfdbfe;
    box-shadow:0 4px 7px -5px rgba(15,23,42,.45);
  }
  #tablePHLGDCombined .lgd-total .lgd-col-rek { z-index:65; background:var(--mph-total); }
  #tablePHLGDCombined .lgd-total .lgd-col-debitur { z-index:64; background:var(--mph-total); }
  #tablePHLGDCombined tbody:not(#lgdTotalBodyCombined) tr:nth-child(even) td { background:#fbfdff; }
  #tablePHLGDCombined tbody:not(#lgdTotalBodyCombined) tr:hover td { background:#f8fafc; }
  #tablePHLGDCombined tbody:not(#lgdTotalBodyCombined) tr:hover .lgd-col-rek,
  #tablePHLGDCombined tbody:not(#lgdTotalBodyCombined) tr:hover .lgd-col-debitur { background:#f8fafc; }

  /* INFO MODAL */
  .mph-info-backdrop {
    position:fixed;
    inset:0;
    z-index:99990;
    display:none;
    background:rgba(15,23,42,.56);
    backdrop-filter:blur(4px);
  }
  .mph-info-backdrop.open { display:block; }
  .mph-info-panel {
    position:fixed;
    left:50%;
    top:50%;
    z-index:100000;
    display:none;
    width:min(820px,calc(100vw - 48px));
    max-height:min(88dvh,720px);
    overflow:auto;
    transform:translate(-50%,-50%);
    border:1px solid #dbe3ee;
    border-radius:15px;
    background:#fff;
    box-shadow:0 26px 70px rgba(15,23,42,.28);
    overscroll-behavior:contain;
  }
  .mph-info-panel.open { display:block; }
  .mph-info-head {
    position:sticky;
    top:0;
    z-index:3;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    padding:14px 15px 12px;
    border-bottom:1px solid #e2e8f0;
    background:rgba(255,255,255,.98);
  }
  .mph-info-title { margin:0; color:#1e293b; font-size:15px; line-height:1.2; font-weight:900; }
  .mph-info-sub { margin:3px 0 0; color:#64748b; font-size:9px; line-height:1.4; font-weight:650; }
  .mph-info-close {
    width:30px;
    height:30px;
    flex:0 0 30px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    background:#f8fafc;
    color:#64748b;
    font-size:19px;
    cursor:pointer;
  }
  .mph-info-close:hover { background:#fee2e2; color:#dc2626; border-color:#fecaca; }
  .mph-info-body { padding:13px 14px 15px; }
  .mph-info-hero { padding:10px 11px; border:1px solid #bfdbfe; border-radius:10px; background:#eff6ff; color:#1e3a8a; font-size:9.5px; line-height:1.5; font-weight:700; }
  .mph-info-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; margin-top:9px; }
  .mph-info-card { min-width:0; padding:10px; border:1px solid #e2e8f0; border-radius:10px; background:#f8fafc; }
  .mph-info-card.full { grid-column:1 / -1; }
  .mph-info-card h4 { margin:0 0 6px; color:#1e293b; font-size:10px; font-weight:900; }
  .mph-info-card p,
  .mph-info-card li { color:#475569; font-size:9px; line-height:1.5; font-weight:650; }
  .mph-info-card ul { margin:0; padding-left:16px; }
  .mph-info-card li + li { margin-top:4px; }
  .mph-info-note { margin-top:8px; padding:8px 9px; border:1px solid #fde68a; border-radius:9px; background:#fffbeb; color:#92400e; font-size:8.5px; line-height:1.45; font-weight:750; }

  /* DETAIL MODAL RECOVERY */
  #recoveryDetailModal {
    position:fixed;
    inset:0;
    z-index:100010;
    display:none;
    align-items:center;
    justify-content:center;
    padding:12px;
    background:rgba(15,23,42,.68);
    backdrop-filter:blur(6px);
  }
  #recoveryDetailModal.open { display:flex; }
  #recoveryDetailCard {
    width:min(1080px,calc(100vw - 48px));
    height:min(76dvh,680px);
    max-height:76dvh;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    border:1px solid rgba(226,232,240,.95);
    border-radius:15px;
    background:#fff;
    box-shadow:0 30px 80px rgba(15,23,42,.32);
  }
  .rec-detail-head { flex:0 0 auto; display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 12px; border-bottom:1px solid #e2e8f0; }
  .rec-detail-title { margin:0; color:#0f172a; font-size:15px; font-weight:900; }
  .rec-detail-sub { margin-top:2px; color:#64748b; font-size:9px; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; }
  .rec-detail-actions { display:flex; align-items:center; gap:6px; }
  .rec-detail-search { position:relative; }
  .rec-detail-search svg { position:absolute; left:9px; top:50%; width:13px; height:13px; transform:translateY(-50%); color:#94a3b8; pointer-events:none; }
  .rec-detail-search input { width:220px; height:34px; padding:0 8px 0 28px; border:1px solid #cbd5e1; border-radius:8px; outline:none; color:#334155; font-size:10px; font-weight:700; }
  .rec-detail-search input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
  .rec-detail-close { width:34px; height:34px; border:0; border-radius:8px; background:#f1f5f9; color:#64748b; font-size:20px; cursor:pointer; }
  .rec-detail-close:hover { background:#fee2e2; color:#dc2626; }
  #recoveryDetailSummary { flex:0 0 auto; display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:6px; padding:7px 9px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
  .rec-stat { min-width:0; padding:7px 8px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; }
  .rec-stat-label { color:#64748b; font-size:7px; font-weight:900; text-transform:uppercase; letter-spacing:.04em; }
  .rec-stat-value { margin-top:3px; overflow:hidden; color:#0f172a; font-size:12px; line-height:1.1; font-weight:900; text-overflow:ellipsis; white-space:nowrap; }
  #recoveryDetailScroll { flex:1 1 auto; min-height:0; overflow:auto; }
  #recoveryDetailTable { width:max-content; min-width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; color:#334155; font-size:10px; }
  #recoveryDetailTable th,
  #recoveryDetailTable td { height:35px; padding:5px 7px; border-right:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; white-space:nowrap; }
  #recoveryDetailTable thead th { position:sticky; top:0; z-index:45; background:#f1f5f9; color:#475569; font-size:8px; font-weight:900; text-transform:uppercase; }
  .rec-detail-rek { position:sticky; left:0; z-index:30; width:124px; min-width:124px; max-width:124px; background:#fff; }
  .rec-detail-name { position:sticky; left:124px; z-index:29; width:210px; min-width:210px; max-width:210px; background:#fff; box-shadow:4px 0 8px -7px rgba(15,23,42,.8); overflow:hidden; text-overflow:ellipsis; }
  #recoveryDetailTable thead .rec-detail-rek { z-index:60; background:#f1f5f9; }
  #recoveryDetailTable thead .rec-detail-name { z-index:59; background:#f1f5f9; }
  #recoveryDetailTable tbody tr:hover td { background:#f8fafc; }
  #recoveryDetailTable tbody tr:hover .rec-detail-rek,
  #recoveryDetailTable tbody tr:hover .rec-detail-name { background:#f8fafc; }

  /* RESPONSIVE */
  @media (min-width:768px) and (max-width:1180px) {
    #monitoringPHPage { height:calc(100dvh - 64px); padding:7px; }
    #mphHeader { flex-direction:column; align-items:stretch; gap:8px; }
    .mph-brand { width:100%; }
    #mphFilterPanel { margin-left:0; width:100%; }
    .mph-filter-form { width:100%; justify-content:flex-end; }
    .mph-filter-set.active { width:100%; justify-content:flex-end; }
    .mph-field.office { width:min(230px,26vw); }
    .mph-field.search { width:min(220px,25vw); }
  }

  @media (max-width:767px) {
    #monitoringPHPage { height:calc(100dvh - 56px); padding:5px 4px; gap:5px; }
    #mphHeader { display:block; padding:6px 7px; border-radius:9px; }
    .mph-brand { width:100%; gap:6px; }
    .mph-brand-icon { width:27px; height:27px; flex-basis:27px; border-radius:7px; }
    .mph-brand-icon svg { width:14px; height:14px; }
    #mphTitle { font-size:11.5px; }
    #mphSubtitle { max-width:170px; margin-top:2px; font-size:6.5px; }
    .mph-info-btn { width:17px; height:17px; flex-basis:17px; font-size:9px; }
    .mph-mobile-actions { display:flex; }
    .mph-mobile-actions .mph-view-toggle { width:29px; height:29px; flex-basis:29px; border-radius:8px; }
    .mph-mobile-actions .mph-view-toggle svg { width:14px; height:14px; }
    .mph-tooltip { display:none; }

    #mphFilterPanel { display:none; width:100%; margin:6px 0 0; padding-top:6px; border-top:1px solid #e2e8f0; }
    #mphFilterPanel.open { display:block; }
    .mph-filter-form { display:block; width:100%; }
    .mph-filter-set.active { display:grid; width:100%; gap:4px; align-items:end; }
    #recoveryFilterSet.active { grid-template-columns:1fr 1fr 31px; }
    #lgdFilterSet.active { grid-template-columns:1fr .68fr 1fr 31px; }
    #lgdFilterSet .mph-field.search { grid-column:1 / 4; grid-row:2; width:auto; }
    #lgdFilterSet .mph-action-btn { grid-column:4; grid-row:2; }
    .mph-field,
    .mph-field.date,
    .mph-field.office,
    .mph-field.bucket,
    .mph-field.search { width:auto; min-width:0; }
    .mph-label { margin-bottom:1px; font-size:6px; letter-spacing:.03em; }
    .mph-input { height:28px; padding:0 5px; border-radius:6px; font-size:7.5px; }
    select.mph-input { padding-right:18px; background-position:right 4px center; background-size:9px; }
    .mph-action-btn { width:29px; height:29px; min-width:29px; border-radius:7px; }
    .mph-action-btn svg { width:13px; height:13px; }

    #recoveryScroller { --rec-head:28px; --rec-code:0px; --rec-name:94px; }
    #tableRecoveryPH { min-width:400px; width:400px; font-size:7.4px; }
    #tableRecoveryPH th,
    #tableRecoveryPH td { height:28px; padding:3px 4px; }
    #tableRecoveryPH thead th { height:28px; font-size:6.3px; letter-spacing:0; }
    #tableRecoveryPH .rec-col-code { display:none; }
    #tableRecoveryPH .rec-col-name { left:0; width:94px; min-width:94px; max-width:94px; overflow:hidden; text-overflow:ellipsis; }
    #tableRecoveryPH th:nth-child(3), #tableRecoveryPH td:nth-child(3) { width:82px; }
    #tableRecoveryPH th:nth-child(4), #tableRecoveryPH td:nth-child(4) { width:72px; }
    #tableRecoveryPH th:nth-child(5), #tableRecoveryPH td:nth-child(5) { width:82px; }
    #tableRecoveryPH th:nth-child(6), #tableRecoveryPH td:nth-child(6) { width:48px; }
    #tableRecoveryPH .recovery-total td { top:28px; }

    #lgdScrollerCombined { --lgd-head:28px; --lgd-norek:0px; --lgd-debitur:105px; }
    #tablePHLGDCombined { min-width:960px; font-size:7.2px; }
    #tablePHLGDCombined th,
    #tablePHLGDCombined td { height:28px; padding:3px 4px; }
    #tablePHLGDCombined thead th { height:28px; padding:3px; font-size:6.2px; letter-spacing:0; }
    #tablePHLGDCombined .lgd-col-office,
    #tablePHLGDCombined .lgd-col-rek { display:none; }
    #tablePHLGDCombined .lgd-col-debitur { left:0; width:105px; min-width:105px; max-width:105px; overflow:hidden; text-overflow:ellipsis; }
    #tablePHLGDCombined .lgd-col-address { width:150px; min-width:150px; }
    #tablePHLGDCombined .lgd-col-year { width:52px; min-width:52px; }
    #tablePHLGDCombined .lgd-col-money { width:78px; min-width:78px; }
    #tablePHLGDCombined .lgd-col-date { width:72px; min-width:72px; }
    #tablePHLGDCombined .lgd-total td { top:28px; }

    .mph-info-backdrop.open { background:rgba(15,23,42,.44); backdrop-filter:blur(2px); }
    .mph-info-panel {
      left:0;
      right:0;
      top:auto;
      bottom:0;
      width:100%;
      max-height:89dvh;
      transform:none;
      border-left:0;
      border-right:0;
      border-bottom:0;
      border-radius:16px 16px 0 0;
      box-shadow:0 -18px 46px rgba(15,23,42,.25);
    }
    .mph-info-head { padding:11px 12px 9px; }
    .mph-info-title { font-size:12px; }
    .mph-info-sub { font-size:7.5px; }
    .mph-info-body { padding:9px 10px 13px; }
    .mph-info-hero { padding:8px 9px; font-size:8px; }
    .mph-info-grid { grid-template-columns:1fr; gap:6px; }
    .mph-info-card.full { grid-column:auto; }
    .mph-info-card { padding:8px; }
    .mph-info-card h4 { font-size:9px; }
    .mph-info-card p, .mph-info-card li { font-size:7.5px; }
    .mph-info-note { font-size:7.5px; }

    #recoveryDetailModal { align-items:flex-end; padding:0; }
    #recoveryDetailCard { width:100%; height:94dvh; max-height:94dvh; border-radius:15px 15px 0 0; border-left:0; border-right:0; border-bottom:0; }
    .rec-detail-head { padding:8px 9px; align-items:flex-start; }
    .rec-detail-title { font-size:12px; }
    .rec-detail-sub { font-size:7.5px; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .rec-detail-actions { gap:4px; }
    .rec-detail-search.desktop { display:none; }
    .rec-detail-close, .rec-detail-actions .mph-action-btn { width:30px; height:30px; min-width:30px; }
    #recoveryDetailSearchMobileWrap { display:block !important; padding:6px 8px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
    #recoveryDetailSearchMobileWrap .rec-detail-search input { width:100%; height:29px; font-size:8.5px; }
    #recoveryDetailSummary { grid-template-columns:repeat(2,minmax(0,1fr)); gap:4px; padding:5px 7px; }
    .rec-stat { padding:5px 6px; }
    .rec-stat-label { font-size:6px; }
    .rec-stat-value { font-size:9px; }
    #recoveryDetailTable { min-width:560px; font-size:8px; }
    #recoveryDetailTable th, #recoveryDetailTable td { height:29px; padding:4px 5px; }
    #recoveryDetailTable thead th { font-size:7px; }
    .rec-detail-rek { width:82px; min-width:82px; max-width:82px; }
    .rec-detail-name { left:82px; width:112px; min-width:112px; max-width:112px; }
  }

  @media (max-width:380px) {
    #mphSubtitle { max-width:140px; }
    #lgdFilterSet.active { grid-template-columns:1fr .62fr 1fr 29px; gap:3px; }
    .mph-input { font-size:7px; padding:0 4px; }
    #tablePHLGDCombined { min-width:900px; }
    #tablePHLGDCombined .lgd-col-debitur { width:96px; min-width:96px; max-width:96px; }
  }


  /* =========================================================
     REKAP LGD - CORRECT VIEW / RESPONSIVE
  ========================================================== */
  #lgdFilterSet.active {
    display:flex;
    align-items:flex-end;
    justify-content:flex-end;
    gap:6px;
  }
  #lgdFilterSet .lgd-position-field { width:132px; }

  .lgd-correct-shell { --lgdc-head:38px; }
  #lgdCorrectTable {
    width:100%;
    min-width:1030px;
    border-collapse:separate;
    border-spacing:0;
    table-layout:fixed;
    color:#334155;
    font-size:11.5px;
  }
  #lgdCorrectTable th,
  #lgdCorrectTable td {
    height:38px;
    padding:6px 8px;
    border-right:1px solid #edf2f7;
    border-bottom:1px solid #edf2f7;
    white-space:nowrap;
    vertical-align:middle;
    font-variant-numeric:tabular-nums;
  }
  #lgdCorrectTable thead th {
    position:sticky;
    top:0;
    z-index:50;
    height:38px;
    background:#f1f5f9;
    color:#334155;
    font-size:9px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.035em;
    box-shadow:inset 0 -1px 0 #cbd5e1;
  }
  #lgdCorrectTable .lgdc-code {
    position:sticky;
    left:0;
    z-index:34;
    width:58px;
    min-width:58px;
    max-width:58px;
    text-align:center;
    background:#fff;
  }
  #lgdCorrectTable .lgdc-name {
    position:sticky;
    left:58px;
    z-index:33;
    width:185px;
    min-width:185px;
    max-width:185px;
    background:#fff;
    box-shadow:4px 0 8px -7px rgba(15,23,42,.8);
  }
  #lgdCorrectTable .lgdc-name span {
    display:block;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  #lgdCorrectTable thead .lgdc-code,
  #lgdCorrectTable thead .lgdc-name {
    z-index:70;
    background:#eaf6ff;
    color:#1e3a8a;
  }
  #lgdCorrectTable .lgdc-total td {
    position:sticky;
    top:var(--lgdc-head);
    z-index:44;
    background:#eff6ff !important;
    color:#1e40af;
    font-weight:900;
    box-shadow:0 4px 7px -6px rgba(15,23,42,.5);
  }
  #lgdCorrectTable .lgdc-total .lgdc-code { z-index:65; }
  #lgdCorrectTable .lgdc-total .lgdc-name { z-index:64; }
  #lgdCorrectTable tbody:not(#lgdTotalCorrect) tr:nth-child(even) td { background:#fbfdff; }
  #lgdCorrectTable tbody:not(#lgdTotalCorrect) tr:hover td { background:#f8fafc; }
  #lgdCorrectTable tbody:not(#lgdTotalCorrect) tr:hover td.lgdc-code,
  #lgdCorrectTable tbody:not(#lgdTotalCorrect) tr:hover td.lgdc-name { background:#f8fafc; }
  .lgdc-recovery { color:#047857; }
  .lgdc-click { color:#1d4ed8; font-weight:900; cursor:pointer; }
  .lgdc-click:hover { text-decoration:underline; }
  .lgdc-click.restricted { color:#94a3b8; }
  .lgdc-lgd-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:54px;
    min-height:23px;
    padding:3px 7px;
    border-radius:999px;
    font-size:8.5px;
    font-weight:900;
  }
  .lgdc-lgd-badge.good { color:#15803d; background:#dcfce7; border:1px solid #bbf7d0; }
  .lgdc-lgd-badge.warn { color:#b45309; background:#fef3c7; border:1px solid #fde68a; }
  .lgdc-lgd-badge.bad { color:#b91c1c; background:#fee2e2; border:1px solid #fecaca; }

  .lgdc-modal,
  .lgdc-warn-modal {
    position:fixed;
    inset:0;
    z-index:100000;
    display:none;
    align-items:center;
    justify-content:center;
    padding:12px;
    background:rgba(15,23,42,.65);
    backdrop-filter:blur(6px);
  }
  .lgdc-modal.open,
  .lgdc-warn-modal.open { display:flex; }
  .lgdc-modal-card {
    width:min(1180px,calc(100vw - 48px));
    height:min(78dvh,720px);
    max-height:78dvh;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    border:1px solid #e2e8f0;
    border-radius:14px;
    background:#fff;
    box-shadow:0 28px 80px rgba(15,23,42,.3);
  }
  .lgdc-modal-head {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding:10px 12px;
    border-bottom:1px solid #e2e8f0;
    background:#fff;
    flex:0 0 auto;
  }
  .lgdc-modal-title { color:#0f172a; font-size:15px; font-weight:900; line-height:1.15; }
  .lgdc-modal-sub { margin-top:3px; color:#64748b; font-size:9px; font-weight:700; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; }
  .lgdc-modal-actions { display:flex; align-items:center; gap:6px; flex:0 0 auto; }
  .lgdc-search { position:relative; display:block; }
  .lgdc-search svg { position:absolute; left:9px; top:50%; width:14px; height:14px; transform:translateY(-50%); color:#94a3b8; pointer-events:none; }
  .lgdc-search input {
    width:220px;
    height:34px;
    padding:0 9px 0 30px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    background:#fff;
    color:#334155;
    outline:none;
    font-size:10px;
    font-weight:700;
  }
  .lgdc-search input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
  .lgdc-search-mobile { display:none; padding:7px 8px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
  .lgdc-summary {
    display:grid;
    grid-template-columns:repeat(5,minmax(0,1fr));
    gap:6px;
    padding:7px 9px;
    border-bottom:1px solid #e2e8f0;
    background:#f8fafc;
    flex:0 0 auto;
  }
  .lgdc-detail-scroll { flex:1; min-height:0; overflow:auto; }
  #lgdDetailTable {
    width:max-content;
    min-width:1320px;
    border-collapse:separate;
    border-spacing:0;
    table-layout:fixed;
    color:#334155;
    font-size:10.5px;
  }
  #lgdDetailTable th,
  #lgdDetailTable td { height:36px; padding:6px 8px; border-right:1px solid #edf2f7; border-bottom:1px solid #edf2f7; white-space:nowrap; font-variant-numeric:tabular-nums; }
  #lgdDetailTable thead th { position:sticky; top:0; z-index:50; background:#f1f5f9; color:#475569; font-size:8.5px; font-weight:900; text-transform:uppercase; letter-spacing:.025em; cursor:default; }
  #lgdDetailTable thead th[data-lgd-sort] { cursor:pointer; }
  .lgdc-detail-rek { position:sticky !important; left:0 !important; z-index:34; width:126px; min-width:126px; max-width:126px; background:#fff !important; }
  .lgdc-detail-name { position:sticky !important; left:126px !important; z-index:33; width:210px; min-width:210px; max-width:210px; background:#fff !important; box-shadow:5px 0 9px -8px rgba(15,23,42,.9); }
  #lgdDetailTable thead .lgdc-detail-rek,
  #lgdDetailTable thead .lgdc-detail-name { z-index:70; background:#f1f5f9 !important; }
  .lgdc-detail-name span { display:block; overflow:hidden; text-overflow:ellipsis; }
  #lgdDetailTable tbody tr:hover td { background:#f8fafc; }
  #lgdDetailTable tbody tr:hover td.lgdc-detail-rek,
  #lgdDetailTable tbody tr:hover td.lgdc-detail-name { background:#f8fafc !important; }

  .lgdc-warn-card {
    width:min(390px,calc(100vw - 28px));
    padding:22px;
    border-radius:16px;
    border-top:4px solid #ef4444;
    background:#fff;
    box-shadow:0 24px 70px rgba(15,23,42,.3);
    text-align:center;
  }
  .lgdc-warn-icon { width:56px; height:56px; margin:0 auto 12px; display:flex; align-items:center; justify-content:center; border-radius:999px; background:#fee2e2; color:#dc2626; font-size:26px; font-weight:900; }
  .lgdc-warn-card h3 { margin:0; color:#0f172a; font-size:18px; font-weight:900; }
  .lgdc-warn-card p { margin:8px 0 16px; color:#64748b; font-size:12px; line-height:1.5; }
  .lgdc-warn-card button { width:100%; height:38px; border:0; border-radius:9px; background:#0f172a; color:#fff; font-size:11px; font-weight:900; cursor:pointer; }

  @media (min-width:768px) and (max-width:1199px) {
    #recoveryDetailCard {
      width:calc(100vw - 28px);
      height:min(82dvh,700px);
      max-height:82dvh;
    }
    .lgdc-modal-card {
      width:calc(100vw - 28px);
      height:min(84dvh,740px);
      max-height:84dvh;
    }
  }

  @media (max-width:767px) {
    #lgdFilterSet.active {
      display:grid !important;
      grid-template-columns:minmax(0,1fr) 30px !important;
      gap:4px !important;
      align-items:end;
    }
    #lgdFilterSet .lgd-position-field { width:auto !important; min-width:0 !important; }
    #lgdCorrectTable { min-width:720px; font-size:7.6px; }
    #lgdCorrectTable col:nth-child(1) { width:0 !important; }
    #lgdCorrectTable col:nth-child(2) { width:96px !important; }
    #lgdCorrectTable col:nth-child(3) { width:44px !important; }
    #lgdCorrectTable col:nth-child(n+4):nth-child(-n+7) { width:90px !important; }
    #lgdCorrectTable col:nth-child(8),
    #lgdCorrectTable col:nth-child(9) { width:56px !important; }
    #lgdCorrectTable th,
    #lgdCorrectTable td { height:29px; padding:3px 4px; }
    #lgdCorrectTable thead th { height:29px; padding:3px; font-size:6.2px; line-height:1.05; white-space:normal; }
    #lgdCorrectTable .lgdc-code { display:none !important; }
    #lgdCorrectTable .lgdc-name { left:0 !important; width:96px; min-width:96px; max-width:96px; }
    #lgdCorrectTable .lgdc-name span { font-size:7.2px; }
    .lgdc-lgd-badge { min-width:39px; min-height:18px; padding:2px 4px; font-size:6px; }

    .lgdc-modal { align-items:flex-end; padding:0; }
    .lgdc-modal-card { width:100%; height:94dvh; max-height:94dvh; border-left:0; border-right:0; border-bottom:0; border-radius:14px 14px 0 0; }
    .lgdc-modal-head { padding:8px 9px; align-items:flex-start; }
    .lgdc-modal-title { font-size:12px; }
    .lgdc-modal-sub { font-size:7.5px; }
    .lgdc-search.desktop { display:none; }
    .lgdc-search-mobile { display:block; }
    .lgdc-search input { width:100%; height:30px; font-size:8.5px; }
    .lgdc-summary { grid-template-columns:repeat(2,minmax(0,1fr)); gap:4px; padding:5px 7px; }
    .lgdc-summary .rec-stat:last-child { grid-column:1/-1; }
    #lgdDetailTable { min-width:1050px; font-size:7.8px; }
    #lgdDetailTable th,
    #lgdDetailTable td { height:29px; padding:4px 5px; font-size:7.8px; }
    #lgdDetailTable thead th { font-size:6.5px; }
    .lgdc-detail-rek { width:84px; min-width:84px; max-width:84px; }
    .lgdc-detail-name { left:84px !important; width:116px; min-width:116px; max-width:116px; }
    .lgdc-warn-card { padding:18px; border-radius:14px; }
  }
</style>

<div id="monitoringPHPage">
  <header id="mphHeader">
    <div class="mph-brand">
      <span class="mph-brand-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 4h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"></path>
          <path d="M3 9h18"></path><path d="M8 14h3"></path><path d="M8 17h6"></path><path d="M17 13v4"></path>
        </svg>
      </span>
      <div class="mph-brand-copy">
        <div class="mph-title-line">
          <h1 id="mphTitle">Monitoring PH</h1>
          <button type="button" id="mphInfoButton" class="mph-info-btn" aria-expanded="false" aria-controls="mphInfoPanel" title="Informasi menu">i</button>
        </div>
        <span id="mphSubtitle">Recovery Pinjaman Hapus Buku dan Rekap LGD dalam satu menu.</span>
      </div>
      <div class="mph-mobile-actions">
        <button type="button" id="mphMobileViewToggle" class="mph-view-toggle" aria-label="Buka Rekap LGD" title="Buka Rekap LGD">
          <span class="mph-view-icon"></span>
          <span class="mph-tooltip">Buka Rekap LGD</span>
        </button>
        <button type="button" id="mphFilterToggle" class="mph-filter-toggle" aria-expanded="false" aria-controls="mphFilterPanel">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
          <span>Filter</span>
        </button>
      </div>
    </div>

    <div id="mphFilterPanel">
      <form id="mphFilterForm" class="mph-filter-form" onsubmit="event.preventDefault();">
        <!-- RECOVERY PH FILTER -->
        <div id="recoveryFilterSet" class="mph-filter-set active">
          <div class="mph-field date">
            <label class="mph-label" for="recoveryStartDate">Dari</label>
            <input id="recoveryStartDate" type="date" class="mph-input" required onclick="try{this.showPicker()}catch(e){}">
          </div>
          <div class="mph-field date">
            <label class="mph-label" for="recoveryEndDate">Sampai</label>
            <input id="recoveryEndDate" type="date" class="mph-input" required onclick="try{this.showPicker()}catch(e){}">
          </div>
          <button type="button" id="exportRecoveryBtn" class="mph-action-btn" title="Export Recovery PH" aria-label="Export Recovery PH">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          </button>
        </div>

        <!-- REKAP LGD FILTER -->
        <div id="lgdFilterSet" class="mph-filter-set">
          <div class="mph-field date lgd-position-field">
            <label class="mph-label" for="lgdPositionDate">Posisi Data</label>
            <input id="lgdPositionDate" type="date" class="mph-input" required onclick="try{this.showPicker()}catch(e){}">
          </div>
          <button type="button" id="exportLgdBtn" class="mph-action-btn" title="Export Rekap LGD" aria-label="Export Rekap LGD">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          </button>
        </div>

        <button type="button" id="mphDesktopViewToggle" class="mph-view-toggle" aria-label="Buka Rekap LGD" title="Buka Rekap LGD">
          <span class="mph-view-icon"></span>
          <span class="mph-tooltip">Buka Rekap LGD</span>
        </button>
      </form>
    </div>
  </header>

  <!-- INFO -->
  <div id="mphInfoBackdrop" class="mph-info-backdrop" aria-hidden="true"></div>
  <aside id="mphInfoPanel" class="mph-info-panel mph-scroll" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="mphInfoTitle">
    <div class="mph-info-head">
      <div class="min-w-0">
        <h2 id="mphInfoTitle" class="mph-info-title">Informasi Recovery PH</h2>
        <p id="mphInfoSub" class="mph-info-sub">Panduan singkat membaca data dan menentukan tindak lanjut.</p>
      </div>
      <button id="mphInfoClose" type="button" class="mph-info-close" aria-label="Tutup informasi">&times;</button>
    </div>
    <div id="mphInfoBody" class="mph-info-body"></div>
  </aside>

  <main id="mphStage">
    <div id="mphLoading" class="mph-loading hidden"><div class="mph-spinner"></div><span>Memuat Data</span></div>

    <!-- VIEW 1 RECOVERY -->
    <section id="recoveryView" class="mph-view active" aria-label="Recovery PH">
      <div id="recoveryScroller" class="mph-table-shell mph-scroll">
        <table id="tableRecoveryPH">
          <colgroup>
            <col style="width:58px"><col style="width:176px"><col style="width:135px"><col style="width:135px"><col style="width:135px"><col style="width:72px">
          </colgroup>
          <thead id="recoveryHead">
            <tr>
              <th class="rec-col-code">Kode</th>
              <th class="rec-col-name text-left">Nama Kantor</th>
              <th class="text-right">Pokok</th>
              <th class="text-right">Bunga</th>
              <th class="text-right">Total</th>
              <th class="text-center">NOA</th>
            </tr>
          </thead>
          <tbody id="recoveryTotalBody"></tbody>
          <tbody id="recoveryBody"></tbody>
        </table>
      </div>
    </section>

    <!-- VIEW 2 REKAP LGD -->
    <section id="lgdView" class="mph-view" aria-label="Rekap LGD">
      <div id="lgdScrollerCombined" class="mph-table-shell mph-scroll lgd-correct-shell">
        <table id="lgdCorrectTable">
          <colgroup>
            <col style="width:58px">
            <col style="width:185px">
            <col style="width:72px">
            <col style="width:145px">
            <col style="width:145px">
            <col style="width:145px">
            <col style="width:145px">
            <col style="width:84px">
            <col style="width:84px">
          </colgroup>
          <thead id="lgdHeadCorrect">
            <tr>
              <th class="lgdc-code">Kode</th>
              <th class="lgdc-name text-left">Nama Kantor</th>
              <th class="text-center">NOA</th>
              <th class="text-right">Baki Debet HB</th>
              <th class="text-right">Recovery Nominal</th>
              <th class="text-right">Recovery NPV</th>
              <th class="text-right">Sisa Saldo</th>
              <th class="text-right">RR (%)</th>
              <th class="text-right">LGD (%)</th>
            </tr>
          </thead>
          <tbody id="lgdTotalCorrect"></tbody>
          <tbody id="lgdBodyCorrect"></tbody>
        </table>
      </div>
    </section>
  </main>
</div>

<!-- DETAIL RECOVERY -->
<div id="recoveryDetailModal" aria-hidden="true">
  <div id="recoveryDetailCard">
    <div class="rec-detail-head">
      <div class="min-w-0">
        <h3 id="recoveryDetailTitle" class="rec-detail-title">Daftar Debitur</h3>
        <div id="recoveryDetailSub" class="rec-detail-sub">-</div>
      </div>
      <div class="rec-detail-actions">
        <label class="rec-detail-search desktop">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
          <input id="recoveryDetailSearch" type="search" placeholder="Cari nama / rekening..." autocomplete="off">
        </label>
        <button type="button" id="exportRecoveryDetailBtn" class="mph-action-btn" title="Export detail">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        </button>
        <button type="button" id="recoveryDetailClose" class="rec-detail-close" aria-label="Tutup">&times;</button>
      </div>
    </div>
    <div id="recoveryDetailSearchMobileWrap" style="display:none">
      <label class="rec-detail-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
        <input id="recoveryDetailSearchMobile" type="search" placeholder="Cari nama / rekening..." autocomplete="off">
      </label>
    </div>
    <div id="recoveryDetailSummary"></div>
    <div id="recoveryDetailScroll" class="mph-scroll"></div>
  </div>
</div>

<!-- DETAIL REKAP LGD -->
<div id="lgdDetailModal" class="lgdc-modal" aria-hidden="true">
  <div id="lgdDetailCard" class="lgdc-modal-card">
    <div class="lgdc-modal-head">
      <div class="min-w-0">
        <h3 class="lgdc-modal-title">Detail LGD Belum Lunas</h3>
        <div id="lgdDetailSub" class="lgdc-modal-sub">-</div>
      </div>
      <div class="lgdc-modal-actions">
        <label class="lgdc-search desktop">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
          <input id="lgdDetailSearch" type="search" placeholder="Cari nama / rekening..." autocomplete="off">
        </label>
        <button type="button" id="exportLgdDetailBtn" class="mph-action-btn" title="Export Detail LGD" aria-label="Export Detail LGD">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        </button>
        <button type="button" id="lgdDetailClose" class="rec-detail-close" aria-label="Tutup">&times;</button>
      </div>
    </div>
    <div id="lgdDetailSearchMobileWrap" class="lgdc-search-mobile">
      <label class="lgdc-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
        <input id="lgdDetailSearchMobile" type="search" placeholder="Cari nama / rekening..." autocomplete="off">
      </label>
    </div>
    <div id="lgdDetailSummary" class="lgdc-summary"></div>
    <div id="lgdDetailScroll" class="lgdc-detail-scroll mph-scroll">
      <table id="lgdDetailTable">
        <thead>
          <tr>
            <th class="lgdc-detail-rek">No Rekening</th>
            <th class="lgdc-detail-name text-left" data-lgd-sort="nama_nasabah">Nama Nasabah <span class="lgdc-sort-mark">↕</span></th>
            <th class="text-right" data-lgd-sort="balance_hapus_buku">BD Hapus Buku <span class="lgdc-sort-mark">↕</span></th>
            <th class="text-center">Tahun PH</th>
            <th class="text-center">Bunga</th>
            <th class="text-right">Rec. Nominal</th>
            <th class="text-right">Rec. NPV</th>
            <th class="text-right">RR (%)</th>
            <th class="text-right">LGD (%)</th>
            <th class="text-right">Sisa Saldo</th>
            <th class="text-right">Pokok Lalu</th>
            <th class="text-right">Pokok Berjalan</th>
          </tr>
        </thead>
        <tbody id="lgdDetailBody"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- WARNING AKSES LGD -->
<div id="lgdWarnModal" class="lgdc-warn-modal" aria-hidden="true">
  <div class="lgdc-warn-card">
    <div class="lgdc-warn-icon">!</div>
    <h3>Akses Ditolak</h3>
    <p>Anda login sebagai Cabang <b id="lgdWarnUser">-</b>. Anda tidak diijinkan membuka detail nasabah milik <b id="lgdWarnTarget">-</b>.</p>
    <button type="button" id="lgdWarnClose">Mengerti</button>
  </div>
</div>

<script>
(() => {
  'use strict';

  const API_HAPUS_BUKU = './api/hapus_buku/';
  const API_HAPUS_DETAIL = './api/hapus_buku/detail';
  const API_KODE = './api/kode/';
  const API_DATE = './api/date/';

  const el = id => document.getElementById(id);
  const nfID = new Intl.NumberFormat('id-ID');
  const fmt = value => nfID.format(Number(value || 0));
  const fmtPct = value => Number(value || 0).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});
  const esc = value => String(value ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
  const escAttr = esc;
  const ymdLocal = date => {
    const y = date.getFullYear();
    const m = String(date.getMonth()+1).padStart(2,'0');
    const d = String(date.getDate()).padStart(2,'0');
    return `${y}-${m}-${d}`;
  };
  const fmtDate = value => {
    if (!value) return '-';
    const s = String(value).slice(0,10);
    const p = s.split('-');
    return p.length === 3 ? `${p[2]}-${p[1]}-${p[0]}` : value;
  };
  const shortTail = (s,n=18) => { s=String(s||''); return s.length<=n ? s : `${s.slice(0,n).trimEnd()}…`; };
  const shortMid = (s,n=12) => { s=String(s||''); if(s.length<=n) return s; const k=n-1,f=Math.ceil(k/2),b=Math.floor(k/2); return `${s.slice(0,f)}…${s.slice(-b)}`; };
  const debounce = (fn,ms=180) => { let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args),ms); }; };

  const state = {
    activeView:'recovery',
    filterOpen:false,
    infoOpen:false,
    recoveryRows:[],
    recoveryAbort:null,
    detailRows:[],
    detailFiltered:[],
    detailAbort:null,
    detailMeta:{ kode:'',start:'',end:'',nama:'' },
    lgdRows:[],
    lgdAbort:null,
    lgdDetailRows:[],
    lgdDetailFiltered:[],
    lgdDetailAbort:null,
    lgdDetailMeta:{ kode:'', nama:'', end:'' },
    lgdDetailSort:{ col:'', dir:1 },
    currentUserKode:'000'
  };

  function setLoading(show) {
    el('mphLoading')?.classList.toggle('hidden', !show);
  }

  function viewIcon(view) {
    return view === 'recovery'
      ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.15" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19h16"></path><path d="M7 16v-5"></path><path d="M12 16V7"></path><path d="M17 16v-8"></path></svg>`
      : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>`;
  }

  function syncViewButtons() {
    const isRecovery = state.activeView === 'recovery';
    [el('mphDesktopViewToggle'),el('mphMobileViewToggle')].forEach(btn => {
      if (!btn) return;
      btn.classList.toggle('is-list', !isRecovery);
      btn.querySelector('.mph-view-icon').innerHTML = viewIcon(state.activeView);
      const text = isRecovery ? 'Buka Rekap LGD' : 'Kembali ke Recovery PH';
      btn.title = text;
      btn.setAttribute('aria-label',text);
      const tooltip = btn.querySelector('.mph-tooltip');
      if (tooltip) tooltip.textContent = text;
    });
  }

  function syncViewUI() {
    const isRecovery = state.activeView === 'recovery';
    el('recoveryView')?.classList.toggle('active',isRecovery);
    el('lgdView')?.classList.toggle('active',!isRecovery);
    el('recoveryFilterSet')?.classList.toggle('active',isRecovery);
    el('lgdFilterSet')?.classList.toggle('active',!isRecovery);
    el('mphSubtitle').textContent = isRecovery
      ? 'Recovery Pinjaman Hapus Buku berdasarkan periode transaksi.'
      : 'Rekap Loss Given Default, recovery, RR, dan sisa saldo hapus buku.';
    syncViewButtons();
    updateInfoContent();
    requestAnimationFrame(syncStickyHeights);
  }

  async function switchView() {
    state.activeView = state.activeView === 'recovery' ? 'lgd' : 'recovery';
    syncViewUI();
    closeFilterSmall();
    const scroller = state.activeView === 'recovery' ? el('recoveryScroller') : el('lgdScrollerCombined');
    if (scroller) { scroller.scrollTop=0; scroller.scrollLeft=0; }
    if (state.activeView === 'recovery') {
      if (!state.recoveryRows.length) await fetchRecovery();
    } else {
      if (!state.lgdRows.length) await fetchLGD();
    }
  }

  function toggleFilter() {
    if (window.innerWidth >= 768) return;
    state.filterOpen = !state.filterOpen;
    el('mphFilterPanel')?.classList.toggle('open',state.filterOpen);
    el('mphFilterToggle')?.classList.toggle('open',state.filterOpen);
    el('mphFilterToggle')?.setAttribute('aria-expanded',String(state.filterOpen));
    const span = el('mphFilterToggle')?.querySelector('span');
    if (span) span.textContent = state.filterOpen ? 'Tutup' : 'Filter';
  }

  function closeFilterSmall() {
    if (window.innerWidth >= 768) return;
    state.filterOpen = false;
    el('mphFilterPanel')?.classList.remove('open');
    el('mphFilterToggle')?.classList.remove('open');
    el('mphFilterToggle')?.setAttribute('aria-expanded','false');
    const span = el('mphFilterToggle')?.querySelector('span');
    if (span) span.textContent='Filter';
  }

  function syncStickyHeights() {
    const recH = el('recoveryHead')?.offsetHeight || 38;
    const lgdH = el('lgdHeadCorrect')?.offsetHeight || 38;
    el('recoveryScroller')?.style.setProperty('--rec-head',`${recH}px`);
    el('lgdScrollerCombined')?.style.setProperty('--lgd-head',`${lgdH}px`);
    el('lgdScrollerCombined')?.style.setProperty('--lgdc-head',`${lgdH}px`);
  }

  function infoRecoveryHtml() {
    return `
      <div class="mph-info-hero"><b>Recovery PH</b> memantau pembayaran debitur yang sudah berada pada posisi hapus buku. Klik <b>Nama Kantor</b> atau <b>NOA</b> untuk membuka detail transaksi debiturnya.</div>
      <div class="mph-info-grid">
        <section class="mph-info-card"><h4>Arti Kolom</h4><ul><li><b>Pokok</b>: pembayaran pokok pada periode yang dipilih.</li><li><b>Bunga</b>: pembayaran bunga pada periode tersebut.</li><li><b>Total</b>: akumulasi pembayaran pokok + bunga.</li><li><b>NOA</b>: jumlah rekening/debitur yang melakukan pembayaran.</li></ul></section>
        <section class="mph-info-card"><h4>Cara Menggunakan</h4><ul><li>Pilih tanggal <b>Dari</b> dan <b>Sampai</b>.</li><li>Data otomatis dimuat kembali ketika tanggal berubah.</li><li>Buka detail cabang untuk melihat rekening, nama nasabah, tanggal transaksi dan nominal pembayaran.</li><li>Gunakan pencarian detail untuk mencari nama atau nomor rekening.</li></ul></section>
        <section class="mph-info-card full"><h4>Fokus Cabang</h4><p>Prioritaskan debitur PH yang masih memiliki kemampuan bayar dan pastikan tindak lanjut recovery tercatat. Pantau cabang dengan NOA dan nominal recovery rendah dibanding potensi portofolio PH yang dimiliki.</p></section>
      </div>
      <div class="mph-info-note">Periode default memakai tanggal 1 bulan berjalan sampai posisi hari ini. Nominal pada halaman mengikuti data transaksi yang tersedia pada API.</div>`;
  }

  function infoLgdHtml() {
    const total = findLGDTotal(state.lgdRows);
    const rr = total ? numLGD(total.persen_rr) : 0;
    const lgd = total ? numLGD(total.persen_lgd) : 0;
    const rec = total ? numLGD(total.total_recovery_nominal) : 0;
    const bd = total ? numLGD(total.total_balance_ph) : 0;
    return `
      <div class="mph-info-hero"><b>Rekap LGD</b> menunjukkan efektivitas recovery atas portofolio hapus buku. Semakin tinggi <b>RR</b> dan semakin rendah <b>LGD</b>, semakin baik hasil recovery.</div>
      <div class="mph-info-grid">
        <section class="mph-info-card"><h4>Kondisi Posisi</h4><ul><li><b>Baki Debet HB</b>: Rp ${fmt(bd)}</li><li><b>Recovery Nominal</b>: Rp ${fmt(rec)}</li><li><b>RR</b>: ${fmtPct(rr)}%</li><li><b>LGD</b>: ${fmtPct(lgd)}%</li></ul></section>
        <section class="mph-info-card"><h4>Arti Kolom</h4><ul><li><b>Recovery NPV</b>: recovery setelah penyesuaian nilai kini.</li><li><b>Sisa Saldo</b>: baki debet yang belum tertutup recovery.</li><li><b>RR (%)</b>: tingkat recovery terhadap baki debet hapus buku.</li><li><b>LGD (%)</b>: sisa potensi loss setelah recovery.</li></ul></section>
        <section class="mph-info-card full"><h4>Prioritas Cabang</h4><p>Klik <b>Nama Kantor</b> untuk melihat debitur LGD yang belum lunas. Prioritaskan rekening dengan <b>sisa saldo besar</b>, <b>RR rendah</b>, dan <b>LGD tinggi</b>. Pastikan strategi penagihan/recovery ditindaklanjuti agar LGD turun dan recovery meningkat.</p></section>
      </div>
      <div class="mph-info-note">Data LGD mengikuti posisi tanggal yang dipilih. Detail nasabah tetap mengikuti hak akses cabang yang login.</div>`;
  }

  function updateInfoContent() {
    const recovery = state.activeView === 'recovery';
    el('mphInfoTitle').textContent = recovery ? 'Informasi Recovery PH' : 'Informasi Rekap LGD';
    el('mphInfoSub').textContent = recovery
      ? 'Panduan membaca recovery dan detail transaksi debitur PH.'
      : 'Ringkasan LGD, Recovery Rate, dan prioritas tindak lanjut recovery.';
    el('mphInfoBody').innerHTML = recovery ? infoRecoveryHtml() : infoLgdHtml();
  }

  function openInfo() {
    state.infoOpen = true;
    updateInfoContent();
    el('mphInfoBackdrop')?.classList.add('open');
    el('mphInfoPanel')?.classList.add('open');
    el('mphInfoBackdrop')?.setAttribute('aria-hidden','false');
    el('mphInfoPanel')?.setAttribute('aria-hidden','false');
    el('mphInfoButton')?.setAttribute('aria-expanded','true');
  }

  function closeInfo(returnFocus=false) {
    state.infoOpen = false;
    el('mphInfoBackdrop')?.classList.remove('open');
    el('mphInfoPanel')?.classList.remove('open');
    el('mphInfoBackdrop')?.setAttribute('aria-hidden','true');
    el('mphInfoPanel')?.setAttribute('aria-hidden','true');
    el('mphInfoButton')?.setAttribute('aria-expanded','false');
    if (returnFocus) el('mphInfoButton')?.focus({preventScroll:true});
  }

  /* RECOVERY PH */
  async function fetchRecovery() {
    const start = el('recoveryStartDate')?.value;
    const end = el('recoveryEndDate')?.value;
    if (!start || !end) return;
    if (state.recoveryAbort) state.recoveryAbort.abort();
    state.recoveryAbort = new AbortController();
    setLoading(true);
    try {
      const response = await fetch(API_HAPUS_BUKU,{
        method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({type:'recovery',start_date:start,end_date:end}),
        signal:state.recoveryAbort.signal
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const json = await response.json();
      state.recoveryRows = Array.isArray(json.data) ? json.data : [];
      renderRecovery(state.recoveryRows,start,end);
    } catch(error) {
      if (error.name !== 'AbortError') {
        console.error(error);
        el('recoveryTotalBody').innerHTML='';
        el('recoveryBody').innerHTML='<tr><td colspan="6" class="py-12 text-center text-red-500 font-bold">Gagal memuat data Recovery PH.</td></tr>';
      }
    } finally {
      setLoading(false);
      requestAnimationFrame(syncStickyHeights);
    }
  }

  function renderRecovery(data,start,end) {
    const total = data.find(row => String(row.kode_kantor) === 'TOTAL');
    el('recoveryTotalBody').innerHTML = total ? `
      <tr class="recovery-total">
        <td class="rec-col-code">TOTAL</td>
        <td class="rec-col-name text-left">GRAND TOTAL</td>
        <td class="text-right">${fmt(total.total_pokok)}</td>
        <td class="text-right">${fmt(total.total_bunga)}</td>
        <td class="text-right">${fmt(total.total_ph)}</td>
        <td class="text-center">${fmt(total.noa)}</td>
      </tr>` : '';

    const rows = data.filter(row => String(row.kode_kantor) !== 'TOTAL');
    if (!rows.length) {
      el('recoveryBody').innerHTML='<tr><td colspan="6" class="py-12 text-center text-slate-400 font-bold">Data tidak ditemukan.</td></tr>';
      return;
    }
    el('recoveryBody').innerHTML = rows.map(row => {
      const clickable = Number(row.noa || 0) > 0;
      const kode = escAttr(row.kode_kantor || '');
      const nama = esc(row.nama_kantor || '-');
      const click = clickable ? `onclick="window.openRecoveryDetailCombined('${kode}','${escAttr(start)}','${escAttr(end)}','${escAttr(row.nama_kantor || '')}')"` : '';
      return `<tr>
        <td class="rec-col-code font-mono text-slate-500">${esc(row.kode_kantor || '')}</td>
        <td class="rec-col-name text-left ${clickable ? 'rec-click' : ''}" ${click} title="${escAttr(row.nama_kantor || '')}">${nama}</td>
        <td class="text-right font-bold">${fmt(row.total_pokok)}</td>
        <td class="text-right">${fmt(row.total_bunga)}</td>
        <td class="text-right font-bold text-blue-700">${fmt(row.total_ph)}</td>
        <td class="text-center ${clickable ? 'rec-click' : ''}" ${click}>${fmt(row.noa)}</td>
      </tr>`;
    }).join('');
  }

  window.openRecoveryDetailCombined = async function(kode,start,end,nama='') {
    if (state.detailAbort) state.detailAbort.abort();
    state.detailAbort = new AbortController();
    state.detailMeta={kode,start,end,nama};
    state.detailRows=[];
    state.detailFiltered=[];
    el('recoveryDetailTitle').textContent=`Daftar Debitur - ${kode}`;
    el('recoveryDetailSub').textContent=`${nama || '-'} | ${start} s/d ${end}`;
    el('recoveryDetailSearch').value='';
    el('recoveryDetailSearchMobile').value='';
    el('recoveryDetailSummary').innerHTML='';
    el('recoveryDetailScroll').innerHTML='<div class="h-full flex items-center justify-center py-20 text-blue-600 font-bold text-xs">Mengambil data debitur...</div>';
    el('recoveryDetailModal').classList.add('open');
    el('recoveryDetailModal').setAttribute('aria-hidden','false');
    try {
      const response = await fetch(API_HAPUS_DETAIL,{
        method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({type:'debitur',kode_kantor:kode,start_date:start,end_date:end}),
        signal:state.detailAbort.signal
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const json = await response.json();
      state.detailRows = Array.isArray(json.data) ? json.data : [];
      state.detailFiltered = state.detailRows;
      renderRecoveryDetail(state.detailRows);
    } catch(error) {
      if (error.name !== 'AbortError') {
        el('recoveryDetailScroll').innerHTML='<div class="py-20 text-center text-red-500 font-bold">Gagal mengambil data detail.</div>';
      }
    }
  };

  function closeRecoveryDetail() {
    state.detailAbort?.abort();
    el('recoveryDetailModal')?.classList.remove('open');
    el('recoveryDetailModal')?.setAttribute('aria-hidden','true');
  }

  function renderRecoveryDetail(list) {
    if (!list.length) {
      el('recoveryDetailSummary').innerHTML='';
      el('recoveryDetailScroll').innerHTML='<div class="py-20 text-center text-slate-400 font-bold">Data tidak ditemukan.</div>';
      return;
    }
    const pokok=list.reduce((s,r)=>s+Number(r.pokok||0),0);
    const bunga=list.reduce((s,r)=>s+Number(r.bunga||0),0);
    const total=list.reduce((s,r)=>s+Number(r.total||0),0);
    el('recoveryDetailSummary').innerHTML=`
      <div class="rec-stat"><div class="rec-stat-label">Debitur/Transaksi</div><div class="rec-stat-value">${fmt(list.length)}</div></div>
      <div class="rec-stat"><div class="rec-stat-label">Pokok</div><div class="rec-stat-value">${fmt(pokok)}</div></div>
      <div class="rec-stat"><div class="rec-stat-label">Bunga</div><div class="rec-stat-value">${fmt(bunga)}</div></div>
      <div class="rec-stat"><div class="rec-stat-label">Total</div><div class="rec-stat-value text-blue-700">${fmt(total)}</div></div>`;
    el('recoveryDetailScroll').innerHTML=`
      <table id="recoveryDetailTable">
        <thead><tr><th class="rec-detail-rek">No Rekening</th><th class="rec-detail-name text-left">Nama Nasabah</th><th style="width:130px">Tanggal Transaksi</th><th style="width:120px" class="text-right">Pokok</th><th style="width:110px" class="text-right">Bunga</th><th style="width:120px" class="text-right">Total</th></tr></thead>
        <tbody>${list.map(row=>`<tr>
          <td class="rec-detail-rek text-center font-mono text-slate-600">${esc(row.no_rekening || '')}</td>
          <td class="rec-detail-name font-bold" title="${escAttr(row.nama_nasabah || '')}">${esc(row.nama_nasabah || '')}</td>
          <td class="text-center font-mono text-slate-600">${esc(row.tanggal_transaksi || '')}</td>
          <td class="text-right">${fmt(row.pokok)}</td>
          <td class="text-right">${fmt(row.bunga)}</td>
          <td class="text-right font-bold text-blue-700">${fmt(row.total)}</td>
        </tr>`).join('')}</tbody>
      </table>`;
  }

  function filterRecoveryDetail(value) {
    const q=String(value||'').trim().toLowerCase();
    el('recoveryDetailSearch').value=value;
    el('recoveryDetailSearchMobile').value=value;
    state.detailFiltered = !q ? state.detailRows : state.detailRows.filter(row =>
      String(row.no_rekening||'').toLowerCase().includes(q) ||
      String(row.nama_nasabah||'').toLowerCase().includes(q) ||
      String(row.tanggal_transaksi||'').toLowerCase().includes(q)
    );
    renderRecoveryDetail(state.detailFiltered);
  }

  /* REKAP LGD */
  const numLGD = value => Number(value || 0);

  function findLGDTotal(rows) {
    return (rows || []).find(row => {
      const nama = String(row.nama_kantor || '').toUpperCase();
      const kode = String(row.kode_kantor || '').toUpperCase();
      return nama.includes('KONSOLIDASI') || kode === 'TOTAL';
    }) || null;
  }

  function syncLGDInfoIfOpen() {
    if (state.infoOpen && state.activeView === 'lgd') updateInfoContent();
  }

  async function fetchLGD() {
    const end = el('lgdPositionDate')?.value;
    if (!end) return;
    if (state.lgdAbort) state.lgdAbort.abort();
    state.lgdAbort = new AbortController();
    setLoading(true);
    el('lgdTotalCorrect').innerHTML='';
    el('lgdBodyCorrect').innerHTML='<tr><td colspan="9" class="py-12 text-center text-slate-400 font-bold">Memuat Rekap LGD...</td></tr>';
    try {
      const response = await fetch(API_HAPUS_BUKU,{
        method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({type:'get lgd',end_date:end}),
        signal:state.lgdAbort.signal
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const json = await response.json();
      if (json.status && Number(json.status) !== 200) throw new Error(json.message || 'Gagal memuat Rekap LGD.');
      state.lgdRows = Array.isArray(json.data) ? json.data : [];
      renderLGD(state.lgdRows);
      syncLGDInfoIfOpen();
    } catch(error) {
      if (error.name !== 'AbortError') {
        console.error(error);
        state.lgdRows=[];
        el('lgdTotalCorrect').innerHTML='';
        el('lgdBodyCorrect').innerHTML=`<tr><td colspan="9" class="py-12 text-center text-red-500 font-bold">${esc(error.message || 'Gagal memuat Rekap LGD.')}</td></tr>`;
      }
    } finally {
      setLoading(false);
      requestAnimationFrame(syncStickyHeights);
    }
  }

  function renderLGD(rows) {
    const totalBody=el('lgdTotalCorrect');
    const body=el('lgdBodyCorrect');
    totalBody.innerHTML='';
    body.innerHTML='';
    if (!Array.isArray(rows) || !rows.length) {
      body.innerHTML='<tr><td colspan="9" class="py-12 text-center text-slate-400 font-bold">Data LGD tidak ditemukan.</td></tr>';
      return;
    }

    const total=findLGDTotal(rows);
    if (total) {
      totalBody.innerHTML=`<tr class="lgdc-total">
        <td class="lgdc-code">ALL</td>
        <td class="lgdc-name text-left">${esc(total.nama_kantor || 'KONSOLIDASI')}</td>
        <td class="text-center">${fmt(total.noa)}</td>
        <td class="text-right">${fmt(total.total_balance_ph)}</td>
        <td class="text-right lgdc-recovery">${fmt(total.total_recovery_nominal)}</td>
        <td class="text-right">${fmt(total.total_recovery_npv)}</td>
        <td class="text-right">${fmt(total.sisa_saldo_nominal)}</td>
        <td class="text-right">${fmtPct(total.persen_rr)}%</td>
        <td class="text-right"><span class="lgdc-lgd-badge ${lgdTone(total.persen_lgd)}">${fmtPct(total.persen_lgd)}%</span></td>
      </tr>`;
    }

    const branches=rows.filter(row=>row!==total);
    body.innerHTML=branches.map(row=>{
      const kode=String(row.kode_kantor || '').padStart(3,'0');
      const clickable=numLGD(row.noa)>0;
      const allowed=state.currentUserKode==='000' || state.currentUserKode===kode;
      const cls=clickable ? (allowed ? 'lgdc-click' : 'lgdc-click restricted') : '';
      return `<tr>
        <td class="lgdc-code font-mono text-slate-500">${esc(row.kode_kantor || '')}</td>
        <td class="lgdc-name text-left ${cls}" ${clickable ? `onclick="window.openLGDDetailCombined('${escAttr(kode)}','${escAttr(row.nama_kantor || '')}')"` : ''} title="${escAttr(row.nama_kantor || '')}"><span>${esc(row.nama_kantor || '-')}</span></td>
        <td class="text-center">${fmt(row.noa)}</td>
        <td class="text-right font-bold">${fmt(row.total_balance_ph)}</td>
        <td class="text-right lgdc-recovery font-bold">${fmt(row.total_recovery_nominal)}</td>
        <td class="text-right">${fmt(row.total_recovery_npv)}</td>
        <td class="text-right">${fmt(row.sisa_saldo_nominal)}</td>
        <td class="text-right font-bold">${fmtPct(row.persen_rr)}%</td>
        <td class="text-right"><span class="lgdc-lgd-badge ${lgdTone(row.persen_lgd)}">${fmtPct(row.persen_lgd)}%</span></td>
      </tr>`;
    }).join('');
  }

  function lgdTone(value) {
    const n=numLGD(value);
    if (n <= 50) return 'good';
    if (n <= 75) return 'warn';
    return 'bad';
  }

  window.openLGDDetailCombined = async function(kode,nama='') {
    if (!kode) return;
    if (state.currentUserKode !== '000' && state.currentUserKode !== kode) {
      el('lgdWarnUser').textContent=state.currentUserKode;
      el('lgdWarnTarget').textContent=nama || kode;
      el('lgdWarnModal').classList.add('open');
      el('lgdWarnModal').setAttribute('aria-hidden','false');
      return;
    }

    const end=el('lgdPositionDate')?.value;
    if (!end) return;
    if (state.lgdDetailAbort) state.lgdDetailAbort.abort();
    state.lgdDetailAbort=new AbortController();
    state.lgdDetailMeta={kode,nama,end};
    state.lgdDetailRows=[];
    state.lgdDetailFiltered=[];
    state.lgdDetailSort={col:'',dir:1};
    el('lgdDetailSub').textContent=`${kode} - ${nama || '-'} | Posisi: ${end}`;
    el('lgdDetailSearch').value='';
    el('lgdDetailSearchMobile').value='';
    el('lgdDetailSummary').innerHTML='';
    el('lgdDetailBody').innerHTML='<tr><td colspan="12" class="py-16 text-center text-blue-600 font-bold">Mengambil detail LGD...</td></tr>';
    el('lgdDetailModal').classList.add('open');
    el('lgdDetailModal').setAttribute('aria-hidden','false');
    try {
      const response=await fetch(API_HAPUS_BUKU,{
        method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({type:'detail lgd blm lunas',end_date:end,kode_kantor:kode}),
        signal:state.lgdDetailAbort.signal
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const json=await response.json();
      if (json.status && Number(json.status)!==200) throw new Error(json.message || 'Gagal memuat detail LGD.');
      state.lgdDetailRows=Array.isArray(json.data)?json.data:[];
      state.lgdDetailFiltered=state.lgdDetailRows.slice();
      renderLGDDetail(state.lgdDetailFiltered);
    } catch(error) {
      if (error.name!=='AbortError') el('lgdDetailBody').innerHTML=`<tr><td colspan="12" class="py-16 text-center text-red-500 font-bold">${esc(error.message || 'Gagal memuat detail LGD.')}</td></tr>`;
    }
  };

  function closeLGDDetail() {
    state.lgdDetailAbort?.abort();
    el('lgdDetailModal')?.classList.remove('open');
    el('lgdDetailModal')?.setAttribute('aria-hidden','true');
  }

  function closeLGDWarn() {
    el('lgdWarnModal')?.classList.remove('open');
    el('lgdWarnModal')?.setAttribute('aria-hidden','true');
  }

  function renderLGDDetail(list) {
    const body=el('lgdDetailBody');
    const summary=el('lgdDetailSummary');
    if (!Array.isArray(list) || !list.length) {
      summary.innerHTML='';
      body.innerHTML='<tr><td colspan="12" class="py-16 text-center text-slate-400 font-bold">Data detail tidak ditemukan.</td></tr>';
      return;
    }
    const bd=list.reduce((s,r)=>s+numLGD(r.balance_hapus_buku),0);
    const rec=list.reduce((s,r)=>s+numLGD(r.total_recovery_nominal),0);
    const sisa=list.reduce((s,r)=>s+numLGD(r.sisa_saldo_nominal),0);
    const avgLgd=list.reduce((s,r)=>s+numLGD(r.lgd_persen),0)/list.length;
    summary.innerHTML=`
      <div class="rec-stat"><div class="rec-stat-label">Debitur</div><div class="rec-stat-value">${fmt(list.length)}</div></div>
      <div class="rec-stat"><div class="rec-stat-label">BD Hapus Buku</div><div class="rec-stat-value">${fmt(bd)}</div></div>
      <div class="rec-stat"><div class="rec-stat-label">Recovery</div><div class="rec-stat-value text-emerald-700">${fmt(rec)}</div></div>
      <div class="rec-stat"><div class="rec-stat-label">Sisa Saldo</div><div class="rec-stat-value">${fmt(sisa)}</div></div>
      <div class="rec-stat"><div class="rec-stat-label">Rata-rata LGD</div><div class="rec-stat-value text-red-600">${fmtPct(avgLgd)}%</div></div>`;
    body.innerHTML=list.map(row=>`<tr>
      <td class="lgdc-detail-rek text-center font-mono text-slate-600">${esc(row.no_rekening || '')}</td>
      <td class="lgdc-detail-name font-bold" title="${escAttr(row.nama_nasabah || '')}"><span>${esc(row.nama_nasabah || '-')}</span></td>
      <td class="text-right font-bold">${fmt(row.balance_hapus_buku)}</td>
      <td class="text-center">${esc(row.tahun_ph || '-')}</td>
      <td class="text-center">${esc(row.suku_bunga_efektif || '0')}%</td>
      <td class="text-right text-emerald-700 font-bold">${fmt(row.total_recovery_nominal)}</td>
      <td class="text-right">${fmt(row.jumlah_recovery_npv)}</td>
      <td class="text-right">${fmtPct(row.recovery_rate_npv)}%</td>
      <td class="text-right"><span class="lgdc-lgd-badge ${lgdTone(row.lgd_persen)}">${fmtPct(row.lgd_persen)}%</span></td>
      <td class="text-right font-bold text-blue-700">${fmt(row.sisa_saldo_nominal)}</td>
      <td class="text-right text-slate-500">${fmt(row.pokok_bulan_lalu)}</td>
      <td class="text-right text-blue-700 font-bold">${fmt(row.pokok_bulan_berjalan)}</td>
    </tr>`).join('');
  }

  function filterLGDDetail(value) {
    const q=String(value||'').trim().toLowerCase();
    el('lgdDetailSearch').value=value;
    el('lgdDetailSearchMobile').value=value;
    state.lgdDetailFiltered=!q ? state.lgdDetailRows.slice() : state.lgdDetailRows.filter(row=>
      String(row.no_rekening||'').toLowerCase().includes(q) ||
      String(row.nama_nasabah||'').toLowerCase().includes(q)
    );
    applyLGDDetailSort(false);
  }

  function sortLGDDetail(col) {
    state.lgdDetailSort.dir = state.lgdDetailSort.col===col ? -state.lgdDetailSort.dir : 1;
    state.lgdDetailSort.col=col;
    applyLGDDetailSort(true);
  }

  function applyLGDDetailSort(updateMarks=true) {
    const {col,dir}=state.lgdDetailSort;
    if (col) {
      state.lgdDetailFiltered.sort((a,b)=>{
        let A=a[col], B=b[col];
        if (!isNaN(Number(A)) && !isNaN(Number(B))) { A=Number(A); B=Number(B); }
        else { A=String(A||'').toLowerCase(); B=String(B||'').toLowerCase(); }
        return A>B ? dir : A<B ? -dir : 0;
      });
    }
    renderLGDDetail(state.lgdDetailFiltered);
    if (updateMarks) document.querySelectorAll('[data-lgd-sort]').forEach(th=>{
      const mark=th.querySelector('.lgdc-sort-mark');
      if(mark) mark.textContent=th.dataset.lgdSort===col ? (dir===1?'↑':'↓') : '↕';
    });
  }

  /* EXPORT */
  function downloadXls(html,filename) {
    const blob=new Blob(['\ufeff'+html],{type:'application/vnd.ms-excel;charset=utf-8;'});
    const a=document.createElement('a');
    a.href=URL.createObjectURL(blob); a.download=filename; a.click();
    setTimeout(()=>URL.revokeObjectURL(a.href),0);
  }

  function exportRecovery() {
    if(!state.recoveryRows.length) return alert('Tidak ada data untuk diexport.');
    let table='<table border="1"><thead><tr><th>Kode Kantor</th><th>Nama Kantor</th><th>Pokok</th><th>Bunga</th><th>Total</th><th>NOA</th></tr></thead><tbody>';
    state.recoveryRows.forEach(row=>{ table+=`<tr><td style="mso-number-format:'\\@'">${esc(row.kode_kantor)}</td><td>${esc(row.nama_kantor || (row.kode_kantor==='TOTAL'?'TOTAL':''))}</td><td>${Number(row.total_pokok||0)}</td><td>${Number(row.total_bunga||0)}</td><td>${Number(row.total_ph||0)}</td><td>${Number(row.noa||0)}</td></tr>`; });
    downloadXls(table+'</tbody></table>',`Recovery_PH_${el('recoveryStartDate').value}_${el('recoveryEndDate').value}.xls`);
  }

  function exportRecoveryDetail() {
    if(!state.detailRows.length) return alert('Tidak ada data detail untuk diexport.');
    let table='<table border="1"><thead><tr><th>No Rekening</th><th>Nama Nasabah</th><th>Tanggal Transaksi</th><th>Pokok</th><th>Bunga</th><th>Total</th></tr></thead><tbody>';
    state.detailRows.forEach(row=>{ table+=`<tr><td style="mso-number-format:'\\@'">${esc(row.no_rekening)}</td><td>${esc(row.nama_nasabah)}</td><td>${esc(row.tanggal_transaksi)}</td><td>${Number(row.pokok||0)}</td><td>${Number(row.bunga||0)}</td><td>${Number(row.total||0)}</td></tr>`; });
    const m=state.detailMeta;
    downloadXls(table+'</tbody></table>',`Detail_Recovery_PH_${m.kode}_${m.start}_${m.end}.xls`);
  }

  function exportLGD() {
    if(!state.lgdRows.length) return alert('Tidak ada data LGD untuk diexport.');
    let table='<table border="1"><thead><tr><th>KODE</th><th>KANTOR</th><th>NOA</th><th>BAKI DEBET HB</th><th>REC NOMINAL</th><th>REC NPV</th><th>SISA SALDO</th><th>RR %</th><th>LGD %</th></tr></thead><tbody>';
    state.lgdRows.forEach(row=>{
      table+=`<tr><td style="mso-number-format:'\@'">${esc(row.kode_kantor||'')}</td><td>${esc(row.nama_kantor||'')}</td><td>${numLGD(row.noa)}</td><td>${numLGD(row.total_balance_ph)}</td><td>${numLGD(row.total_recovery_nominal)}</td><td>${numLGD(row.total_recovery_npv)}</td><td>${numLGD(row.sisa_saldo_nominal)}</td><td>${numLGD(row.persen_rr)}</td><td>${numLGD(row.persen_lgd)}</td></tr>`;
    });
    downloadXls(table+'</tbody></table>',`Rekap_LGD_${el('lgdPositionDate').value}.xls`);
  }

  function exportLGDDetail() {
    if(!state.lgdDetailRows.length) return alert('Tidak ada detail LGD untuk diexport.');
    let table='<table border="1"><thead><tr><th>NO REKENING</th><th>NAMA NASABAH</th><th>BD HB</th><th>TAHUN PH</th><th>BUNGA</th><th>REC NOMINAL</th><th>REC NPV</th><th>RR %</th><th>LGD %</th><th>SISA SALDO</th><th>POKOK LALU</th><th>POKOK BERJALAN</th></tr></thead><tbody>';
    state.lgdDetailRows.forEach(row=>{
      table+=`<tr><td style="mso-number-format:'\@'">${esc(row.no_rekening||'')}</td><td>${esc(row.nama_nasabah||'')}</td><td>${numLGD(row.balance_hapus_buku)}</td><td>${esc(row.tahun_ph||'')}</td><td>${esc(row.suku_bunga_efektif||'')}</td><td>${numLGD(row.total_recovery_nominal)}</td><td>${numLGD(row.jumlah_recovery_npv)}</td><td>${numLGD(row.recovery_rate_npv)}</td><td>${numLGD(row.lgd_persen)}</td><td>${numLGD(row.sisa_saldo_nominal)}</td><td>${numLGD(row.pokok_bulan_lalu)}</td><td>${numLGD(row.pokok_bulan_berjalan)}</td></tr>`;
    });
    const m=state.lgdDetailMeta;
    downloadXls(table+'</tbody></table>',`Detail_LGD_${m.kode}_${m.end}.xls`);
  }

  async function init() {
    const now=new Date();
    const first=new Date(now.getFullYear(),now.getMonth(),1,12);
    const today=new Date(now.getFullYear(),now.getMonth(),now.getDate(),12);
    el('recoveryStartDate').value=ymdLocal(first);
    el('recoveryEndDate').value=ymdLocal(today);
    el('lgdPositionDate').value=ymdLocal(today);

    const user=(window.getUser && window.getUser()) || null;
    state.currentUserKode=user?.kode ? String(user.kode).padStart(3,'0') : '000';

    syncViewUI();
    await fetchRecovery();
  }

  /* EVENTS */
  el('mphDesktopViewToggle')?.addEventListener('click',switchView);
  el('mphMobileViewToggle')?.addEventListener('click',switchView);
  el('mphFilterToggle')?.addEventListener('click',toggleFilter);
  el('mphInfoButton')?.addEventListener('click',()=>state.infoOpen?closeInfo(true):openInfo());
  el('mphInfoClose')?.addEventListener('click',()=>closeInfo(true));
  el('mphInfoBackdrop')?.addEventListener('click',()=>closeInfo(false));

  el('recoveryStartDate')?.addEventListener('change',fetchRecovery);
  el('recoveryEndDate')?.addEventListener('change',fetchRecovery);
  el('exportRecoveryBtn')?.addEventListener('click',exportRecovery);

  el('lgdPositionDate')?.addEventListener('change',fetchLGD);
  el('exportLgdBtn')?.addEventListener('click',exportLGD);
  el('lgdDetailClose')?.addEventListener('click',closeLGDDetail);
  el('lgdDetailModal')?.addEventListener('click',event=>{ if(event.target===el('lgdDetailModal')) closeLGDDetail(); });
  el('lgdDetailSearch')?.addEventListener('input',event=>filterLGDDetail(event.target.value));
  el('lgdDetailSearchMobile')?.addEventListener('input',event=>filterLGDDetail(event.target.value));
  el('exportLgdDetailBtn')?.addEventListener('click',exportLGDDetail);
  el('lgdWarnClose')?.addEventListener('click',closeLGDWarn);
  el('lgdWarnModal')?.addEventListener('click',event=>{ if(event.target===el('lgdWarnModal')) closeLGDWarn(); });
  el('lgdDetailTable')?.querySelector('thead')?.addEventListener('click',event=>{ const th=event.target.closest('[data-lgd-sort]'); if(th) sortLGDDetail(th.dataset.lgdSort); });

  el('recoveryDetailClose')?.addEventListener('click',closeRecoveryDetail);
  el('recoveryDetailModal')?.addEventListener('click',event=>{ if(event.target===el('recoveryDetailModal')) closeRecoveryDetail(); });
  el('recoveryDetailSearch')?.addEventListener('input',event=>filterRecoveryDetail(event.target.value));
  el('recoveryDetailSearchMobile')?.addEventListener('input',event=>filterRecoveryDetail(event.target.value));
  el('exportRecoveryDetailBtn')?.addEventListener('click',exportRecoveryDetail);

  document.addEventListener('keydown',event=>{
    if(event.key!=='Escape') return;
    if(el('recoveryDetailModal')?.classList.contains('open')) closeRecoveryDetail();
    else if(el('lgdDetailModal')?.classList.contains('open')) closeLGDDetail();
    else if(el('lgdWarnModal')?.classList.contains('open')) closeLGDWarn();
    else if(state.infoOpen) closeInfo(true);
  });

  window.addEventListener('resize',debounce(()=>{
    if(window.innerWidth>=768) {
      state.filterOpen=false;
      el('mphFilterPanel')?.classList.remove('open');
      el('mphFilterToggle')?.classList.remove('open');
      el('mphFilterToggle')?.setAttribute('aria-expanded','false');
    }
    syncStickyHeights();
  },100));

  init();
})();
</script>


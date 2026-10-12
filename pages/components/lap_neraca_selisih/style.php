<style>
  #lnIssuePage {
    --lni-blue:#2563eb;
    --lni-blue-soft:#eff6ff;
    --lni-red:#be123c;
    --lni-red-soft:#fff1f2;
    --lni-green:#047857;
    --lni-green-soft:#ecfdf5;
    --lni-amber:#b45309;
    --lni-amber-soft:#fffbeb;
    --lni-line:#dbe4f0;
    --lni-soft-line:#edf2f7;
    --lni-text:#172033;
    --lni-muted:#64748b;
    min-height:calc(100vh - 60px);
    padding:clamp(10px,2vw,22px);
    background:linear-gradient(145deg,#f8fafc 0%,#eef4fb 100%);
    color:var(--lni-text);
    font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
  }
  #lnIssuePage * { box-sizing:border-box; }
  .lni-surface { border:1px solid var(--lni-line); border-radius:16px; background:rgba(255,255,255,.96); box-shadow:0 8px 26px rgba(15,23,42,.055); }
  .lni-header,.lni-card { width:min(100%,1440px); margin:0 auto; }
  .lni-header { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:clamp(14px,2vw,21px) clamp(15px,2.4vw,26px); }
  .lni-title-block { min-width:0; }
  .lni-back { display:inline-flex; align-items:center; gap:5px; color:var(--lni-blue); font-size:12px; font-weight:850; text-decoration:none; }
  .lni-back:hover { text-decoration:underline; }
  .lni-heading-line { display:flex; align-items:center; gap:10px; margin-top:9px; }
  .lni-heading-icon { display:grid; place-items:center; width:38px; height:38px; flex:0 0 38px; border:1px solid #fecdd3; border-radius:11px; color:var(--lni-red); background:var(--lni-red-soft); font-size:19px; font-weight:950; }
  .lni-header h1 { margin:0; font-size:clamp(17px,2.2vw,24px); line-height:1.15; letter-spacing:-.02em; }
  .lni-header p { margin:4px 0 0; color:var(--lni-muted); font-size:11px; font-weight:650; }
  .lni-status { padding:8px 11px; border:1px solid #cbd5e1; border-radius:999px; background:#f8fafc; color:#475569; font-size:11px; font-weight:900; white-space:nowrap; }
  .lni-status.bad { border-color:#fecdd3; background:var(--lni-red-soft); color:var(--lni-red); }
  .lni-status.audit { border-color:#fde68a; background:var(--lni-amber-soft); color:var(--lni-amber); }
  .lni-status.ok { border-color:#bbf7d0; background:var(--lni-green-soft); color:var(--lni-green); }
  .lni-summary { width:min(100%,1440px); margin:12px auto; display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; }
  .lni-stat { min-height:79px; padding:13px 14px; border:1px solid var(--lni-line); border-radius:12px; background:#fff; box-shadow:0 2px 8px rgba(15,23,42,.025); }
  .lni-stat span { display:block; color:var(--lni-muted); font-size:10px; font-weight:850; letter-spacing:.035em; text-transform:uppercase; }
  .lni-stat strong { display:block; margin-top:7px; color:#1e293b; font-size:clamp(14px,1.7vw,19px); font-variant-numeric:tabular-nums; }
  .lni-stat.alert { border-color:#fecdd3; background:linear-gradient(135deg,#fff,#fff7f8); }
  .lni-stat.alert strong { color:var(--lni-red); }
  .lni-skeleton { background:linear-gradient(100deg,#f1f5f9 30%,#f8fafc 50%,#f1f5f9 70%); background-size:200% 100%; animation:lniShimmer 1.2s infinite; }
  @keyframes lniShimmer { to { background-position:-200% 0; } }
  .lni-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:14px; padding:17px 19px; border-bottom:1px solid var(--lni-line); }
  .lni-section-kicker { margin-bottom:3px; color:var(--lni-blue); font-size:9px; font-weight:950; letter-spacing:.12em; text-transform:uppercase; }
  .lni-card-head h2 { margin:0 0 4px; font-size:16px; letter-spacing:-.01em; }
  .lni-card-head p { max-width:750px; margin:0; color:var(--lni-muted); font-size:11px; line-height:1.4; }
  .lni-card-head strong { padding:6px 8px; border-radius:7px; color:var(--lni-red); background:var(--lni-red-soft); font-size:11px; white-space:nowrap; }
  .lni-table-wrap { overflow:auto; border-radius:0 0 16px 16px; }
  .lni-table-wrap table { width:100%; min-width:1080px; border-collapse:collapse; }
  .lni-table-wrap th { position:sticky; top:0; z-index:1; padding:11px 13px; border-bottom:1px solid var(--lni-line); background:#f8fafc; color:#475569; font-size:9px; font-weight:950; text-align:left; text-transform:uppercase; letter-spacing:.045em; }
  .lni-table-wrap td { padding:12px 13px; border-bottom:1px solid var(--lni-soft-line); color:#334155; font-size:11px; vertical-align:middle; }
  .lni-table-wrap tbody tr:last-child td { border-bottom:0; }
  .lni-table-wrap tbody tr:hover { background:#f8fbff; }
  .lni-coa-code { color:#1e40af; font-size:12px; font-weight:950; }
  .lni-coa-name { max-width:270px; margin-top:3px; color:var(--lni-muted); font-size:10px; line-height:1.3; }
  .lni-num { text-align:right !important; white-space:nowrap; font-variant-numeric:tabular-nums; }
  .lni-badge { display:inline-block; margin:2px 4px 2px 0; padding:4px 7px; border:1px solid #fecdd3; border-radius:999px; background:var(--lni-red-soft); color:var(--lni-red); font-size:9px; font-weight:850; }
  .lni-badge.mutation { border-color:#fed7aa; background:#fff7ed; color:#c2410c; }
  .lni-empty { padding:42px 20px !important; color:var(--lni-muted); text-align:center; }
  @media (max-width:760px) {
    #lnIssuePage { min-height:calc(100vh - 52px); padding:8px; }
    .lni-header { align-items:flex-start; gap:10px; border-radius:13px; }
    .lni-heading-line { gap:8px; margin-top:8px; }
    .lni-heading-icon { width:31px; height:31px; flex-basis:31px; border-radius:9px; font-size:15px; }
    .lni-header h1 { font-size:16px; }
    .lni-header p { font-size:9px; }
    .lni-status { padding:6px 8px; font-size:9px; }
    .lni-summary { grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; margin:8px auto; }
    .lni-stat { min-height:63px; padding:9px 10px; border-radius:10px; }
    .lni-stat span { font-size:8px; }
    .lni-stat strong { margin-top:5px; font-size:12px; }
    .lni-card { border-radius:13px; }
    .lni-card-head { padding:12px; }
    .lni-card-head h2 { font-size:14px; }
    .lni-card-head p { font-size:9px; }
    .lni-card-head strong { font-size:9px; }
    .lni-table-wrap { border-radius:0 0 13px 13px; }
    .lni-table-wrap { padding:7px; background:#f8fafc; }
    .lni-table-wrap table { display:block; min-width:0; }
    .lni-table-wrap thead { display:none; }
    .lni-table-wrap tbody,.lni-table-wrap tr,.lni-table-wrap td { display:block; }
    .lni-table-wrap tr { margin-bottom:7px; border:1px solid var(--lni-line); border-radius:10px; background:#fff; box-shadow:0 2px 7px rgba(15,23,42,.035); overflow:hidden; }
    .lni-table-wrap tr:last-child { margin-bottom:0; }
    .lni-table-wrap td { display:grid; grid-template-columns:112px minmax(0,1fr); gap:8px; align-items:start; padding:8px 10px; border-bottom:1px solid var(--lni-soft-line); text-align:left !important; white-space:normal; }
    .lni-table-wrap td:last-child { border-bottom:0; }
    .lni-table-wrap td::before { content:attr(data-label); color:var(--lni-muted); font-size:9px; font-weight:850; }
    .lni-table-wrap td.lni-num { font-size:10px; font-weight:750; }
    .lni-table-wrap td.lni-empty { display:block; }
    .lni-coa-name { max-width:none; }
  }
  @media (max-width:420px) { .lni-header { display:block; } .lni-status { display:inline-block; margin-top:10px; } .lni-card-head { display:block; } .lni-card-head strong { display:inline-block; margin-top:8px; } }
</style>

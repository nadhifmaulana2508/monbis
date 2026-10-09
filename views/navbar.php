<style id="monbisGlobalThemeStyle">
  :root[data-monbis-theme="dark"] body { background:#0f172a; color:#e5e7eb; }
  :root[data-monbis-theme="dark"] .bg-slate-50 { background-color:#0b1220 !important; }
  :root[data-monbis-theme="dark"] .bg-white { background-color:#111827 !important; }
  :root[data-monbis-theme="dark"] .border-slate-200,
  :root[data-monbis-theme="dark"] .border-slate-100 { border-color:#334155 !important; }
  :root[data-monbis-theme="dark"] .text-slate-800,
  :root[data-monbis-theme="dark"] .text-slate-700 { color:#f8fafc !important; }
  :root[data-monbis-theme="dark"] .text-slate-600,
  :root[data-monbis-theme="dark"] .text-slate-500,
  :root[data-monbis-theme="dark"] .text-slate-400 { color:#94a3b8 !important; }
  :root[data-monbis-theme="dark"] .hover\:bg-slate-100:hover,
  :root[data-monbis-theme="dark"] .hover\:bg-slate-50:hover { background-color:#1f2937 !important; }
  :root[data-monbis-theme="dark"] .bg-blue-50 { background-color:#172554 !important; }
  :root[data-monbis-theme="dark"] .shadow-sm,
  :root[data-monbis-theme="dark"] .shadow-lg { box-shadow:0 14px 32px rgba(0,0,0,.35) !important; }
  .monbis-theme-toggle {
    display:none; align-items:center; justify-content:center;
    width:36px; height:36px; border:1px solid #dbe3ee; border-radius:10px;
    background:#fff; color:#475569; box-shadow:0 1px 2px rgba(15,23,42,.06);
    transition:background .15s,border-color .15s,color .15s,transform .15s;
  }
  .monbis-theme-toggle:hover { transform:translateY(-1px); color:#2563eb; border-color:#bfdbfe; }
  :root[data-monbis-theme="dark"] .monbis-theme-toggle { background:#0b1220; border-color:#475569; color:#cbd5e1; }
  .monbis-theme-toggle .monbis-theme-icon-sun { display:none; }
  :root[data-monbis-theme="dark"] .monbis-theme-toggle .monbis-theme-icon-moon { display:none; }
  :root[data-monbis-theme="dark"] .monbis-theme-toggle .monbis-theme-icon-sun { display:block; }
  .dashboard-navbar-filter-toggle {
    display:inline-flex; align-items:center; justify-content:center;
    width:36px; height:36px; border:1px solid #dbe3ee; border-radius:10px;
    background:#fff; color:#475569; box-shadow:0 1px 2px rgba(15,23,42,.06);
    transition:background .15s,border-color .15s,color .15s,transform .15s;
  }
  .dashboard-navbar-filter-toggle:hover { transform:translateY(-1px); color:#2563eb; border-color:#bfdbfe; }
  .dashboard-navbar-filter-toggle.is-active { color:#2563eb; border-color:#93c5fd; background:#eff6ff; }
  .dashboard-navbar-filter {
    position:absolute; top:calc(100% + 9px); right:12px; z-index:140;
    width:min(980px, calc(100vw - 24px)); padding:15px 16px 16px;
    border:1px solid #e2e8f0; border-radius:16px; background:rgba(255,255,255,.98);
    box-shadow:0 20px 44px rgba(15,23,42,.18); backdrop-filter:blur(14px);
    gap:14px; animation:dashboardFilterIn .16s ease-out both;
  }
  .dashboard-navbar-filter,
  .dashboard-navbar-filter * { font-family:'Roboto', Arial, sans-serif; }
  .dashboard-navbar-filter__head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
  .dashboard-navbar-filter__eyebrow { display:block; color:#64748b; font-size:9px; font-weight:900; letter-spacing:.14em; text-transform:uppercase; }
  .dashboard-navbar-filter__title { display:block; margin-top:2px; color:#0f172a; font-size:13px; font-weight:900; }
  .dashboard-navbar-filter__close { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border:1px solid #dbe3ee; border-radius:10px; color:#334155; background:#fff; }
  .dashboard-navbar-filter__close:hover { color:#2563eb; border-color:#93c5fd; background:#eff6ff; }
  .dashboard-navbar-filter__fields { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:10px; align-items:end; }
  .dashboard-navbar-filter__field { min-width:0; display:flex; flex-direction:column; gap:5px; }
  .dashboard-navbar-filter__field label { color:#64748b; font-size:9px; font-weight:850; letter-spacing:.03em; }
  .dashboard-navbar-filter__field input,
  .dashboard-navbar-filter__field select { width:100%; min-height:38px; padding:0 10px; border:1px solid #dbe3ee; border-radius:9px; background:#f8fafc; color:#1e3a5f; font-size:12px; font-weight:800; outline:none; }
  .dashboard-navbar-filter__field input:focus,
  .dashboard-navbar-filter__field select:focus { border-color:#60a5fa; box-shadow:0 0 0 3px rgba(96,165,250,.15); background:#fff; }
  .dashboard-navbar-filter__submit { display:inline-flex; align-items:center; justify-content:center; min-height:38px; min-width:82px; padding:0 13px; border:0; border-radius:9px; background:#2563eb; color:#fff; font-family:'Roboto',Arial,sans-serif; font-size:11px; font-weight:800; box-shadow:0 8px 16px rgba(37,99,235,.2); }
  .dashboard-navbar-filter__submit:hover { background:#1d4ed8; }
  .dashboard-navbar-filter__submit,
  .npl-navbar-filter-panel__submit { display:none !important; }
  @keyframes dashboardFilterIn { from { opacity:0; transform:translateY(-5px) scale(.99); } to { opacity:1; transform:translateY(0) scale(1); } }
  :root[data-monbis-theme="dark"] .dashboard-navbar-filter-toggle { background:#0b1220; border-color:#475569; color:#cbd5e1; }
  :root[data-monbis-theme="dark"] .dashboard-navbar-filter-toggle.is-active { color:#bfdbfe; border-color:#60a5fa; background:#172554; }
  :root[data-monbis-theme="dark"] .dashboard-navbar-filter { border-color:#334155; background:rgba(15,23,42,.98); box-shadow:0 20px 44px rgba(0,0,0,.42); }
  :root[data-monbis-theme="dark"] .dashboard-navbar-filter__title { color:#f8fafc; }
  :root[data-monbis-theme="dark"] .dashboard-navbar-filter__close { border-color:#475569; background:#111827; color:#cbd5e1; }
  :root[data-monbis-theme="dark"] .dashboard-navbar-filter__field input,
  :root[data-monbis-theme="dark"] .dashboard-navbar-filter__field select { border-color:#475569; background:#111827; color:#e2e8f0; }
  :root[data-monbis-theme="dark"] .dashboard-navbar-filter__field input:focus,
  :root[data-monbis-theme="dark"] .dashboard-navbar-filter__field select:focus { background:#0f172a; }
  @media (max-width:767px) {
    .dashboard-navbar-filter { right:8px; width:calc(100vw - 16px); padding:13px; }
    .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
    .dashboard-navbar-filter__submit { width:100%; }
  }
  @media (max-width:420px) {
    .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  .npl-navbar-filter-panel {
    position:absolute; top:calc(100% + 9px); right:12px; z-index:140;
    width:min(1000px, calc(100vw - 24px)); padding:15px 16px 16px;
    border:1px solid #e2e8f0; border-radius:16px; background:rgba(255,255,255,.98);
    box-shadow:0 20px 44px rgba(15,23,42,.18); backdrop-filter:blur(14px);
    animation:dashboardFilterIn .16s ease-out both;
  }
  .npl-navbar-filter-panel,
  .npl-navbar-filter-panel * { font-family:'Roboto', Arial, sans-serif; }
  .npl-navbar-filter-panel__head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:14px; }
  .npl-navbar-filter-panel__eyebrow { display:block; color:#64748b; font-size:9px; font-weight:900; letter-spacing:.14em; text-transform:uppercase; }
  .npl-navbar-filter-panel__title { display:block; margin-top:2px; color:#0f172a; font-size:13px; font-weight:900; }
  .npl-navbar-filter-panel__close { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border:1px solid #dbe3ee; border-radius:10px; color:#334155; background:#fff; }
  .npl-navbar-filter-panel__close:hover { color:#2563eb; border-color:#93c5fd; background:#eff6ff; }
  #nplNavbarFilterPanel #mkFilterForm.npl-navbar-filter-form {
    display:grid; grid-template-columns:repeat(4, minmax(0, 1fr));
    gap:10px; align-items:end; width:100%;
  }
  #nplNavbarFilterPanel #mkFilterForm.npl-navbar-filter-form > div { width:auto !important; min-width:0; }
  #nplNavbarFilterPanel #mkFilterForm.npl-navbar-filter-form .mk-input { width:100%; min-height:38px; }
  #nplNavbarFilterPanel #mkFilterForm.npl-navbar-filter-form .npl-navbar-filter-panel__submit-wrap { display:none !important; }
  .npl-navbar-filter-panel__submit { display:inline-flex; align-items:center; justify-content:center; min-width:82px; min-height:38px; padding:0 13px; border:0; border-radius:9px; background:#2563eb; color:#fff; font-family:'Roboto',Arial,sans-serif; font-size:11px; font-weight:800; box-shadow:0 8px 16px rgba(37,99,235,.2); }
  .npl-navbar-filter-panel__submit:hover { background:#1d4ed8; }
  :root[data-monbis-theme="dark"] .npl-navbar-filter-panel { border-color:#334155; background:rgba(15,23,42,.98); box-shadow:0 20px 44px rgba(0,0,0,.42); }
  :root[data-monbis-theme="dark"] .npl-navbar-filter-panel__title { color:#f8fafc; }
  :root[data-monbis-theme="dark"] .npl-navbar-filter-panel__close { border-color:#475569; background:#111827; color:#cbd5e1; }
  :root[data-monbis-theme="dark"] .npl-navbar-filter-panel .mk-label { color:#94a3b8; }
  :root[data-monbis-theme="dark"] .npl-navbar-filter-panel .mk-input { border-color:#475569; background:#111827; color:#e2e8f0; }
  :root[data-monbis-theme="dark"] .npl-navbar-filter-panel .mk-input:focus { border-color:#60a5fa; box-shadow:0 0 0 3px rgba(96,165,250,.16); background:#0f172a; }
  @media (max-width:767px) {
    .npl-navbar-filter-panel { right:8px; width:calc(100vw - 16px); padding:13px; }
    #nplNavbarFilterPanel #mkFilterForm.npl-navbar-filter-form { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:8px; }
    #nplNavbarFilterPanel #mkFilterForm.npl-navbar-filter-form .npl-navbar-filter-panel__submit-wrap { grid-column:1 / -1; }
    .npl-navbar-filter-panel__submit { width:100%; }
  }
  @media (max-width:420px) {
    #nplNavbarFilterPanel #mkFilterForm.npl-navbar-filter-form { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
    #nplNavbarFilterPanel #mkFilterForm.npl-navbar-filter-form .npl-navbar-filter-panel__submit-wrap { grid-column:auto; }
  }
  #npl25NavbarFilterPanel { width:min(440px, calc(100vw - 24px)); }
  #npl25NavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:minmax(0, 1fr); }
  @media (max-width:767px) {
    #npl25NavbarFilterPanel { right:8px; width:calc(100vw - 16px); }
  }
  #realisasiAoNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(3, minmax(0, 1fr)); }
  :root[data-monbis-theme="dark"] #realisasiAoNavbarFilterPanel .dashboard-navbar-filter__field label { color:#94a3b8; }
  @media (max-width:767px) {
    #realisasiAoNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #realisasiAoNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #realisasiGrowthNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(4, minmax(0, 1fr)); }
  :root[data-monbis-theme="dark"] #realisasiGrowthNavbarFilterPanel .dashboard-navbar-filter__field label { color:#94a3b8; }
  @media (max-width:767px) {
    #realisasiGrowthNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #realisasiGrowthNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #realisasiRbbNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(3, minmax(0, 1fr)); }
  :root[data-monbis-theme="dark"] #realisasiRbbNavbarFilterPanel .dashboard-navbar-filter__field label { color:#94a3b8; }
  @media (max-width:767px) {
    #realisasiRbbNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #realisasiRbbNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #mobNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(6, minmax(0, 1fr)); }
  @media (max-width:767px) {
    #mobNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #mobNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #rrNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(5, minmax(0, 1fr)); }
  #rrNavbarFilterPanel .rr-breakdown-field.is-hidden { display:none !important; }
  @media (max-width:767px) {
    #rrNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #rrNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #otpNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(4, minmax(0, 1fr)); }
  @media (max-width:767px) {
    #otpNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #otpNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #otpBucketNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(6, minmax(0, 1fr)); }
  :root[data-monbis-theme="dark"] #otpBucketNavbarFilterPanel .dashboard-navbar-filter__field label { color:#94a3b8; }
  @media (max-width:767px) {
    #otpBucketNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #otpBucketNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #migrasiBucketNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(5, minmax(0, 1fr)); }
  :root[data-monbis-theme="dark"] #migrasiBucketNavbarFilterPanel .dashboard-navbar-filter__field label { color:#94a3b8; }
  @media (max-width:767px) {
    #migrasiBucketNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #migrasiBucketNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #migrasiBucketNavbarFilterPanel .dashboard-navbar-filter__field--switch > label:first-child { display:block; }
  #migrasiBucketNavbarFilterPanel .dashboard-navbar-filter__switch {
    min-height:38px;
    display:flex;
    flex-direction:row;
    align-items:center;
    gap:8px;
    padding:0 10px;
    border:1px solid #dbe3ee;
    border-radius:9px;
    background:#f8fafc;
    color:#1e3a5f;
    cursor:pointer;
  }
  #migrasiBucketNavbarFilterPanel .dashboard-navbar-filter__switch input { width:15px; min-height:15px; accent-color:#2563eb; }
  #migrasiBucketNavbarFilterPanel .dashboard-navbar-filter__switch span { font-size:12px; font-weight:800; }
  :root[data-monbis-theme="dark"] #migrasiBucketNavbarFilterPanel .dashboard-navbar-filter__switch { border-color:#475569; background:#111827; color:#e2e8f0; }
  #migrasiKolekNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(4, minmax(0, 1fr)); }
  :root[data-monbis-theme="dark"] #migrasiKolekNavbarFilterPanel .dashboard-navbar-filter__field label { color:#94a3b8; }
  @media (max-width:767px) {
    #migrasiKolekNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #migrasiKolekNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #actualKreditNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(4, minmax(0, 1fr)); }
  :root[data-monbis-theme="dark"] #actualKreditNavbarFilterPanel .dashboard-navbar-filter__field label { color:#94a3b8; }
  @media (max-width:767px) {
    #actualKreditNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #actualKreditNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #recoveryNplNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(4, minmax(0, 1fr)); }
  :root[data-monbis-theme="dark"] #recoveryNplNavbarFilterPanel .dashboard-navbar-filter__field label { color:#94a3b8; }
  @media (max-width:767px) {
    #recoveryNplNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #recoveryNplNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #recoveryPhNavbarFilterPanel { width:min(560px, calc(100vw - 24px)); }
  #recoveryPhNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  #recoveryPhNavbarFilterPanel .dashboard-navbar-filter__field.hidden { display:none; }
  @media (max-width:767px) {
    #recoveryPhNavbarFilterPanel { right:8px; width:calc(100vw - 16px); }
    #recoveryPhNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  #layananDigitalNavbarFilterPanel,
  #vaNavbarFilterPanel,
  #branchlessNavbarFilterPanel,
  #qrisMerchantNavbarFilterPanel { width:min(980px, calc(100vw - 24px)); }
  #layananDigitalNavbarFilterPanel .dashboard-navbar-filter__fields,
  #vaNavbarFilterPanel .dashboard-navbar-filter__fields,
  #branchlessNavbarFilterPanel .dashboard-navbar-filter__fields,
  #qrisMerchantNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(4, minmax(0, 1fr)); }
  @media (max-width:767px) {
    #layananDigitalNavbarFilterPanel,
    #vaNavbarFilterPanel,
    #branchlessNavbarFilterPanel,
    #qrisMerchantNavbarFilterPanel { right:8px; width:calc(100vw - 16px); }
    #layananDigitalNavbarFilterPanel .dashboard-navbar-filter__fields,
    #vaNavbarFilterPanel .dashboard-navbar-filter__fields,
    #branchlessNavbarFilterPanel .dashboard-navbar-filter__fields,
    #qrisMerchantNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  #potensiNplNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(4, minmax(0, 1fr)); }
  @media (max-width:767px) {
    #potensiNplNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #potensiNplNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #flowParNavbarFilterForm .dashboard-navbar-filter__fields { grid-template-columns:repeat(4, minmax(0, 1fr)); }
  :root[data-monbis-theme="dark"] #flowParNavbarFilterForm .dashboard-navbar-filter__field label { color:#94a3b8; }
  @media (max-width:767px) {
    #flowParNavbarFilterForm .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #flowParNavbarFilterForm .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #jatuhTempoNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(6, minmax(0, 1fr)); }
  @media (max-width:767px) {
    #jatuhTempoNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #jatuhTempoNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); gap:7px; }
  }
  #recomPipelaneNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(3, minmax(150px, 1fr)); }
  @media (max-width:767px) {
    #recomPipelaneNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width:420px) {
    #recomPipelaneNavbarFilterPanel .dashboard-navbar-filter__fields { grid-template-columns:1fr; gap:7px; }
  }
  :root {
    --monbis-event-accent:#2563eb;
    --monbis-event-header-bg:#ffffff;
    --monbis-event-sidebar-bg:#ffffff;
    --monbis-event-text:#0f172a;
    --monbis-event-sidebar-text:#334155;
    --monbis-event-border:#dbe3ee;
    --monbis-event-font:Roboto, Arial, system-ui, sans-serif;
    --monbis-page-scroll-thumb:#94a3b8;
    --monbis-page-scroll-thumb-hover:#64748b;
    --monbis-page-scroll-track:rgba(226,232,240,.42);
  }
  :root[data-monbis-theme="dark"] {
    --monbis-event-header-bg:#111827;
    --monbis-event-sidebar-bg:#0f172a;
    --monbis-event-text:#e5e7eb;
    --monbis-event-sidebar-text:#cbd5e1;
    --monbis-event-border:#334155;
    --monbis-page-scroll-thumb:#475569;
    --monbis-page-scroll-thumb-hover:#64748b;
    --monbis-page-scroll-track:rgba(15,23,42,.55);
  }
  html,
  body,
  .monbis-app-shell main {
    scrollbar-width:thin;
    scrollbar-color:var(--monbis-page-scroll-thumb) var(--monbis-page-scroll-track);
  }
  :is(html,body,.monbis-app-shell main)::-webkit-scrollbar { width:4px; height:4px; }
  :is(html,body,.monbis-app-shell main)::-webkit-scrollbar-track {
    background:var(--monbis-page-scroll-track);
    border-radius:999px;
  }
  :is(html,body,.monbis-app-shell main)::-webkit-scrollbar-thumb {
    border:1px solid transparent;
    border-radius:999px;
    background:var(--monbis-page-scroll-thumb);
    background-clip:padding-box;
  }
  :is(html,body,.monbis-app-shell main)::-webkit-scrollbar-thumb:hover { background:var(--monbis-page-scroll-thumb-hover); }
  :is(html,body,.monbis-app-shell main)::-webkit-scrollbar-button { display:none; width:0; height:0; }
  :is(html,body,.monbis-app-shell main)::-webkit-scrollbar-corner { background:transparent; }
  body { font-family:var(--monbis-event-font); }
  .monbis-app-shell {
    background:
      radial-gradient(circle at top left, rgba(37,99,235,.08), transparent 30%),
      linear-gradient(180deg, #f8fafc 0%, #eef5fb 100%) !important;
  }
  #sidebar {
    position:relative;
    overflow:visible;
    background:
      linear-gradient(180deg, rgba(255,255,255,.94), rgba(248,250,252,.98)),
      var(--monbis-event-sidebar-bg) !important;
    border-color:var(--monbis-event-border) !important;
    color:var(--monbis-event-sidebar-text);
    box-shadow:16px 0 36px rgba(15,23,42,.08);
  }
  #sidebar > .h-16 {
    height:68px;
    padding-left:18px;
    padding-right:14px;
    border-color:rgba(148,163,184,.22) !important;
  }
  #sidebar > .h-16 .monbis-logo--icon {
    width:36px;
    height:36px;
    padding:4px;
    border-radius:14px;
    background:rgba(255,255,255,.86);
    box-shadow:0 12px 24px rgba(15,23,42,.10);
  }
  #sidebar > .h-16 .monbis-logo--wordmark { display:none; }
  #sidebar > .h-16 span {
    font-size:20px;
    font-weight:950;
    letter-spacing:-.03em;
  }
  #sidebar nav {
    padding:14px 10px 18px !important;
    overflow-x:hidden;
    overflow-y:auto;
    scrollbar-width:none;
    -ms-overflow-style:none;
  }
  #sidebar nav::-webkit-scrollbar { display:none; width:0; height:0; }
  .monbis-sidebar-promo {
    margin:10px;
    min-height:78px;
    border:1px solid rgba(148,163,184,.25);
    border-radius:18px;
    overflow:hidden;
    background:
      radial-gradient(circle at top right, rgba(37,99,235,.18), transparent 38%),
      linear-gradient(135deg, rgba(37,99,235,.10), rgba(14,165,233,.08));
    color:var(--monbis-event-sidebar-text);
    box-shadow:0 14px 26px rgba(15,23,42,.08);
    flex-shrink:0;
    cursor:default;
  }
  .monbis-sidebar-promo.is-ai-access {
    cursor:pointer;
    border-color:rgba(37,99,235,.45);
  }
  .monbis-sidebar-promo.is-ai-access:hover {
    transform:translateY(-1px);
    box-shadow:0 16px 30px rgba(37,99,235,.16);
  }
  .monbis-sidebar-promo__inner {
    display:flex;
    align-items:center;
    gap:10px;
    min-height:78px;
    padding:12px;
    background:linear-gradient(180deg, rgba(255,255,255,.55), rgba(255,255,255,.18));
  }
  .monbis-sidebar-promo__spark {
    width:34px;
    height:34px;
    border-radius:14px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    background:var(--monbis-event-accent);
    box-shadow:0 10px 22px rgba(37,99,235,.25);
    animation:none;
    flex:0 0 auto;
  }
  .monbis-sidebar-promo__spark--chat { display:none; }
  .monbis-sidebar-promo.is-ai-access .monbis-sidebar-promo__spark--electric { display:none; }
  .monbis-sidebar-promo.is-ai-access .monbis-sidebar-promo__spark--chat { display:inline-flex; }
  .monbis-sidebar-promo__text {
    min-width:0;
    opacity:1;
    transition:opacity .2s ease;
  }
  .monbis-sidebar-promo__text strong {
    display:block;
    font-size:12px;
    line-height:1.1;
    font-weight:950;
    letter-spacing:-.01em;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .monbis-sidebar-promo__text span {
    display:block;
    margin-top:3px;
    font-size:10px;
    line-height:1.2;
    font-weight:800;
    color:rgba(100,116,139,.88);
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .monbis-sidebar-promo.has-image {
    min-height:116px;
    background-size:cover;
    background-position:center;
  }
  .monbis-sidebar-promo.has-image .monbis-sidebar-promo__inner {
    min-height:116px;
    align-items:flex-end;
    background:linear-gradient(180deg, rgba(15,23,42,.10), rgba(15,23,42,.78));
    color:#fff;
  }
  .monbis-sidebar-promo.has-image .monbis-sidebar-promo__text span {
    color:rgba(255,255,255,.76);
  }
  @keyframes monbisPulse {
    0%,100% { transform:translateY(0) scale(1); }
    50% { transform:translateY(-2px) scale(1.04); }
  }
  #sidebar .accordion-group {
    margin:4px 0;
  }
  #sidebar .accordion-btn,
  #sidebar nav > a {
    position:relative;
    min-height:46px;
    border-radius:15px !important;
    font-size:14px;
    font-weight:720 !important;
    letter-spacing:-.01em;
    border:1px solid transparent;
    transition:background .18s ease, color .18s ease, border-color .18s ease, box-shadow .18s ease, transform .18s ease;
  }
  #sidebar .accordion-btn:hover,
  #sidebar nav > a:hover {
    transform:translateX(2px);
    border-color:rgba(37,99,235,.14);
    box-shadow:0 10px 22px rgba(15,23,42,.07);
  }
  #sidebar .accordion-btn.is-open {
    background:color-mix(in srgb, var(--monbis-event-accent) 8%, transparent) !important;
    border-color:rgba(37,99,235,.16);
  }
  #sidebar .accordion-btn > div > svg,
  #sidebar nav > a > svg {
    width:24px !important;
    height:24px !important;
    padding:4px;
    border-radius:11px;
    color:#64748b !important;
    background:rgba(148,163,184,.10);
    transition:background .18s ease, color .18s ease, box-shadow .18s ease;
  }
  #sidebar .accordion-btn > div > svg path,
  #sidebar nav > a > svg path,
  #sidebar .caret path {
    stroke-width:1.7 !important;
  }
  #sidebar .accordion-btn:hover > div > svg,
  #sidebar nav > a:hover > svg,
  #sidebar .accordion-btn.is-active > div > svg,
  #sidebar nav > a.is-active > svg,
  #sidebar nav > a.bg-blue-50 > svg {
    color:var(--monbis-event-accent) !important;
    background:rgba(37,99,235,.09);
    box-shadow:inset 0 0 0 1px rgba(37,99,235,.10);
  }
  #sidebar nav > a.is-active::before,
  #sidebar nav > a.bg-blue-50::before,
  #sidebar .accordion-btn.is-active::before {
    content:"";
    position:absolute;
    left:6px;
    top:12px;
    bottom:12px;
    width:3px;
    border-radius:999px;
    background:var(--monbis-event-accent);
    box-shadow:0 0 0 4px rgba(37,99,235,.08);
  }
  #sidebar .accordion-content {
    margin:4px 8px 8px 18px;
    padding:6px 4px 6px 22px !important;
    border-left:1px solid rgba(148,163,184,.28);
  }
  #sidebar .accordion-content a {
    display:block;
    min-height:32px;
    padding:8px 10px !important;
    border-radius:11px !important;
    font-size:12px !important;
    font-weight:680;
    letter-spacing:-.005em;
  }
  #sidebar .accordion-content a.is-active {
    color:var(--monbis-event-accent) !important;
    background:rgba(37,99,235,.10) !important;
  }
  #sidebar .accordion-content:not(.hidden) {
    animation:monbisSidebarMenuIn .18s ease both;
  }
  @keyframes monbisSidebarMenuIn {
    from { opacity:0; transform:translateY(-4px); }
    to { opacity:1; transform:translateY(0); }
  }
  @media (min-width:768px) {
    #sidebar {
      width:78px !important;
      overflow:hidden;
    }
    #sidebar:hover {
      width:278px !important;
      overflow:visible;
    }
    #sidebar:focus-within {
      width:278px !important;
      overflow:visible;
    }
    #sidebar:not(:hover):not(:focus-within) nav {
      overflow-y:hidden;
    }
    #sidebar:not(:hover):not(:focus-within) .accordion-content {
      display:none !important;
    }
    #sidebar:not(:hover):not(:focus-within) .accordion-btn,
    #sidebar:not(:hover):not(:focus-within) nav > a {
      justify-content:center;
      padding-left:10px !important;
      padding-right:10px !important;
    }
    #sidebar:not(:hover):not(:focus-within) .accordion-btn > div {
      width:100%;
      justify-content:center;
    }
    #sidebar:not(:hover):not(:focus-within) .accordion-btn > div > span,
    #sidebar:not(:hover):not(:focus-within) nav > a > span {
      width:0;
      margin-left:0 !important;
      overflow:hidden;
      opacity:0 !important;
    }
    #sidebar:not(:hover):not(:focus-within) .accordion-btn > .caret {
      display:none;
    }
    #sidebar:not(:hover):not(:focus-within) .monbis-sidebar-promo {
      min-height:54px;
      border-radius:16px;
    }
    #sidebar:not(:hover):not(:focus-within) .monbis-sidebar-promo__inner {
      min-height:54px;
      justify-content:center;
      padding:10px;
    }
    #sidebar:not(:hover):not(:focus-within) .monbis-sidebar-promo__text {
      display:none;
    }
    #sidebar:hover > .h-16 .monbis-logo--icon,
    #sidebar:focus-within > .h-16 .monbis-logo--icon { display:none; }
    #sidebar:hover > .h-16 .monbis-logo--wordmark,
    #sidebar:focus-within > .h-16 .monbis-logo--wordmark {
      display:block;
      width:180px;
      height:auto;
      max-height:50px;
      padding:0;
      border-radius:0;
      background:transparent;
      box-shadow:none;
      object-fit:contain;
    }
    :root[data-monbis-theme="dark"] #sidebar:hover > .h-16 .monbis-logo--wordmark,
    :root[data-monbis-theme="dark"] #sidebar:focus-within > .h-16 .monbis-logo--wordmark {
      padding:4px 8px;
      border-radius:10px;
      background:#ffffff;
    }
  }
  #sidebar .accordion-btn,
  #sidebar nav a,
  #sidebar .text-slate-700,
  #sidebar .text-slate-600,
  #sidebar .text-slate-800 {
    color:var(--monbis-event-sidebar-text) !important;
  }
  #sidebar nav a:hover,
  #sidebar .accordion-btn:hover {
    background:color-mix(in srgb, var(--monbis-event-accent) 10%, transparent) !important;
    color:var(--monbis-event-accent) !important;
  }
  #sidebar nav a.bg-blue-50 {
    background:color-mix(in srgb, var(--monbis-event-accent) 12%, white) !important;
    color:var(--monbis-event-accent) !important;
  }
  #mainNavbar {
    min-height:68px;
    background:
      linear-gradient(135deg, color-mix(in srgb, var(--monbis-event-header-bg) 92%, white), rgba(255,255,255,.92)) !important;
    border-color:var(--monbis-event-border) !important;
    color:var(--monbis-event-text);
    box-shadow:0 14px 34px rgba(15,23,42,.07) !important;
    backdrop-filter:blur(16px);
  }
  #mainNavbar .text-slate-800,
  #mainNavbar .text-slate-700 { color:var(--monbis-event-text) !important; }
  #btnToggleSidebar,
  #mainNavbar button:not(#btnProfileMenu):not(#monbisThemeToggle) {
    border-radius:12px;
    transition:background .16s ease, color .16s ease, transform .16s ease, box-shadow .16s ease;
  }
  #btnToggleSidebar:hover,
  #mainNavbar button:not(#btnProfileMenu):not(#monbisThemeToggle):hover {
    background:rgba(37,99,235,.08);
    color:var(--monbis-event-accent) !important;
    transform:translateY(-1px);
  }
  #mainNavbar .border-l {
    border-color:rgba(148,163,184,.28) !important;
  }
  #navUserName {
    font-weight:900 !important;
    letter-spacing:-.015em;
  }
  #navBranch {
    font-weight:700;
  }
  #btnProfileMenu {
    width:38px !important;
    height:38px !important;
    border-radius:14px !important;
    background:linear-gradient(135deg, rgba(37,99,235,.12), rgba(14,165,233,.12)) !important;
    color:var(--monbis-event-accent) !important;
    border:1px solid rgba(37,99,235,.18) !important;
    box-shadow:0 12px 24px rgba(37,99,235,.12) !important;
  }
  #btnProfileMenu:hover {
    transform:translateY(-1px);
    box-shadow:0 16px 28px rgba(37,99,235,.18) !important;
  }
  #dropdownProfileMenu {
    width:190px !important;
    border-radius:14px !important;
    border-color:rgba(148,163,184,.24) !important;
    box-shadow:0 22px 50px rgba(15,23,42,.16) !important;
    overflow:hidden;
  }
  #dropdownProfileMenu a {
    font-size:12px !important;
    font-weight:850 !important;
  }
  .monbis-event-badge {
    display:none;
    align-items:center;
    gap:8px;
    min-width:0;
    max-width:min(360px,38vw);
    padding:6px 10px;
    border:1px solid color-mix(in srgb, var(--monbis-event-accent) 35%, white);
    border-radius:999px;
    background:rgba(255,255,255,.72);
    color:var(--monbis-event-text);
    box-shadow:0 12px 26px rgba(15,23,42,.08);
    backdrop-filter:blur(10px);
  }
  .monbis-event-badge.is-active { display:flex; }
  .monbis-event-badge__image {
    width:26px;
    height:26px;
    border-radius:999px;
    object-fit:cover;
    border:1px solid rgba(255,255,255,.7);
    background:#fff;
  }
  .monbis-event-badge__title {
    min-width:0;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    font-size:12px;
    font-weight:900;
    letter-spacing:.01em;
  }
  :root[data-monbis-theme="dark"] #mainNavbar {
    background:linear-gradient(135deg, #111827, #0f172a) !important;
    border-color:#334155 !important;
  }
  :root[data-monbis-theme="dark"] .monbis-app-shell {
    background:
      radial-gradient(circle at top left, rgba(37,99,235,.16), transparent 32%),
      linear-gradient(180deg, #0b1220 0%, #0f172a 100%) !important;
  }
  :root[data-monbis-theme="dark"] #sidebar {
    background:
      linear-gradient(180deg, rgba(15,23,42,.96), rgba(2,6,23,.98)),
      #0f172a !important;
    border-color:#334155 !important;
    color:#cbd5e1 !important;
    box-shadow:16px 0 36px rgba(0,0,0,.32);
  }
  :root[data-monbis-theme="dark"] #sidebar .border-slate-200,
  :root[data-monbis-theme="dark"] #sidebar .border-slate-100 {
    border-color:#334155 !important;
  }
  :root[data-monbis-theme="dark"] #sidebar .accordion-btn,
  :root[data-monbis-theme="dark"] #sidebar nav a,
  :root[data-monbis-theme="dark"] #sidebar .text-slate-700,
  :root[data-monbis-theme="dark"] #sidebar .text-slate-600,
  :root[data-monbis-theme="dark"] #sidebar .text-slate-800 {
    color:#cbd5e1 !important;
  }
  :root[data-monbis-theme="dark"] #sidebar nav a:hover,
  :root[data-monbis-theme="dark"] #sidebar .accordion-btn:hover {
    background:#1f2937 !important;
    color:#93c5fd !important;
  }
  :root[data-monbis-theme="dark"] #sidebar nav a.bg-blue-50 {
    background:#172554 !important;
    color:#bfdbfe !important;
  }
  :root[data-monbis-theme="dark"] #sidebar .accordion-content {
    border-left-color:rgba(71,85,105,.72);
  }
  :root[data-monbis-theme="dark"] .monbis-sidebar-promo {
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.22), transparent 38%),
      linear-gradient(135deg, rgba(15,23,42,.92), rgba(30,41,59,.92));
    border-color:rgba(71,85,105,.75);
  }
  :root[data-monbis-theme="dark"] .monbis-sidebar-promo__inner {
    background:linear-gradient(180deg, rgba(15,23,42,.44), rgba(15,23,42,.14));
  }
  :root[data-monbis-theme="dark"] .monbis-sidebar-promo__text span {
    color:#94a3b8;
  }
  :root[data-monbis-theme="dark"] #sidebar .accordion-btn > div > svg,
  :root[data-monbis-theme="dark"] #sidebar nav > a > svg {
    color:#94a3b8 !important;
    background:rgba(148,163,184,.12);
  }
  :root[data-monbis-theme="dark"] #sidebar svg.text-slate-400 {
    color:#94a3b8 !important;
  }
  :root[data-monbis-theme="dark"] .monbis-event-badge {
    background:rgba(15,23,42,.72);
    border-color:#334155;
  }
  :root[data-monbis-theme="dark"] #dropdownProfileMenu {
    background:#111827 !important;
    border-color:#334155 !important;
  }
  :root[data-monbis-theme="dark"] #dropdownProfileMenu a {
    color:#cbd5e1 !important;
  }
  :root[data-monbis-theme="dark"] #dropdownProfileMenu a:hover {
    background:#1f2937 !important;
    color:#93c5fd !important;
  }
  @media (max-width:767px) {
    #sidebar {
      position:absolute;
      top:0;
      left:0;
      bottom:0;
    }
    #mainNavbar {
      min-height:56px;
      height:56px !important;
      padding-left:10px !important;
      padding-right:10px !important;
      gap:6px;
    }
    #btnToggleSidebar {
      margin-right:6px !important;
      padding:7px;
      border:1px solid rgba(148,163,184,.25);
      background:rgba(255,255,255,.45);
    }
    #mainNavbar .md\:hidden span {
      max-width:84px;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
      font-size:13px !important;
    }
    #mainNavbar .flex.items-center.gap-4 {
      gap:7px !important;
    }
    #mainNavbar .h-8.border-l,
    #mainNavbar button.relative.text-slate-500 {
      display:none !important;
    }
    .monbis-theme-toggle,
    #btnProfileMenu {
      width:34px !important;
      height:34px !important;
      border-radius:12px !important;
      flex:0 0 auto;
    }
    .monbis-event-badge {
      max-width:104px;
      padding:4px 6px;
      gap:5px;
      margin-left:4px !important;
    }
    .monbis-event-badge__title { font-size:9px; max-width:70px; }
    .monbis-event-badge__image { width:22px; height:22px; }
    #sidebar {
      width:min(82vw, 292px) !important;
      box-shadow:24px 0 50px rgba(15,23,42,.22);
    }
    #sidebar > .h-16 {
      height:60px;
    }
    #sidebar nav {
      padding-bottom:10px !important;
    }
    .monbis-sidebar-promo {
      margin:8px 10px 12px;
    }
  }
</style>
<script>
  (function () {
    const key = 'monbisTheme';
    const root = document.documentElement;

    function readUser() {
      if (typeof window.getUser === 'function') {
        const direct = window.getUser();
        if (direct) return direct;
      }
      if (window.__USER) return window.__USER;
      for (const storageKey of ['dpk_user', 'app_user', 'user']) {
        try {
          const parsed = JSON.parse(localStorage.getItem(storageKey) || 'null');
          if (parsed) return parsed;
        } catch (error) {}
      }
      return null;
    }

    function isOperasional(user) {
      const values = [
        user?.role,
        user?.job_position,
        user?.unit_kerja,
        user?.division,
        user?.divisi,
        user?.department
      ].map(value => String(value || '').toLowerCase());
      return values.includes('dev') || values.some(value => value.includes('divisi operasional'));
    }

    function sync(user) {
      const button = document.getElementById('monbisThemeToggle');
      const allowed = isOperasional(user || readUser());
      if (button) button.style.display = allowed ? 'inline-flex' : 'none';
      if (!allowed) {
        root.setAttribute('data-monbis-theme', 'light');
        localStorage.setItem(key, 'light');
        return;
      }
      const saved = localStorage.getItem(key) || 'light';
      root.setAttribute('data-monbis-theme', saved === 'dark' ? 'dark' : 'light');
    }

    root.setAttribute('data-monbis-theme', 'light');
    window.MonbisTheme = window.MonbisTheme || {};
    window.MonbisTheme.sync = sync;
    window.MonbisTheme.isOperasional = isOperasional;

    document.addEventListener('click', function (event) {
      const button = event.target.closest('#monbisThemeToggle');
      if (!button) return;
      if (!isOperasional(readUser())) {
        sync(readUser());
        return;
      }
      const next = root.getAttribute('data-monbis-theme') === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-monbis-theme', next);
      localStorage.setItem(key, next);
      document.dispatchEvent(new CustomEvent('monbis-theme-change', { detail:{ theme:next } }));
    });

    document.addEventListener('DOMContentLoaded', () => {
      let tries = 0;
      sync(readUser());
      const timer = setInterval(() => {
        tries += 1;
        sync(readUser());
        if (readUser() || tries >= 25) clearInterval(timer);
      }, 200);
    });
  })();

  /*
   * Filter area global antar halaman.
   * Nilai disimpan per user supaya pilihan Cilacap milik user A tidak
   * menimpa pilihan user B pada browser yang sama.
   */
  (function () {
    const STORAGE_PREFIX = 'monbis_global_area_filter_v1';
    const AREA_SELECTOR = 'select[data-monbis-global-area]';
    const KORWIL_NAMES = new Set(['SEMARANG', 'SOLO', 'BANYUMAS', 'PEKALONGAN']);
    let restoreTimer = null;

    function readUser() {
      try {
        if (typeof window.getUser === 'function') {
          const direct = window.getUser();
          if (direct) return direct;
        }
      } catch (error) {}

      for (const key of ['dpk_user', 'app_user', 'user']) {
        try {
          const parsed = JSON.parse(localStorage.getItem(key) || 'null');
          if (parsed) return parsed;
        } catch (error) {}
      }
      return null;
    }

    function getUserScope() {
      const user = readUser() || {};
      const keys = ['employee_id', 'id_peg', 'idPeg', 'id', 'user_id', 'username', 'nik'];
      for (const key of keys) {
        const value = String(user?.[key] ?? '').trim();
        if (value) return value.replace(/[^a-z0-9_.-]/gi, '_').toLowerCase();
      }
      return 'anonymous';
    }

    function getStorageKey() {
      return `${STORAGE_PREFIX}:${getUserScope()}`;
    }

    function addAlias(set, value) {
      const raw = String(value ?? '').trim().toUpperCase();
      if (!raw) return;

      const compact = raw.replace(/\s+/g, ' ');
      if (compact === 'ALL' || compact === '000' || compact.includes('KONSOLIDASI')) {
        set.add('ALL');
      }

      const korwilMatch = compact.match(/KOR(?:WIL)?\s*[-_:]?\s*([A-Z]+)/);
      if (korwilMatch) set.add(`KOR:${korwilMatch[1]}`);
      if (KORWIL_NAMES.has(compact)) set.add(`KOR:${compact}`);

      const branchMatch = compact.match(/^(?:CAB(?:ANG)?\s*[-_:]?\s*)?(\d{1,3})(?:\s|$|-)/);
      if (branchMatch) {
        const code = branchMatch[1].padStart(3, '0');
        set.add(code === '000' ? 'ALL' : `CAB:${code}`);
      }

      if (/^[A-Z][A-Z0-9 _.-]*$/.test(compact) && !compact.includes('KORWIL')) {
        set.add(`RAW:${compact}`);
      }

      // Contoh label option: "010 - CILACAP". Simpan nama cabang juga
      // agar tetap cocok dengan halaman lain yang memakai value berbeda.
      compact.split(/[^A-Z0-9]+/).filter(token => token.length >= 3).forEach(token => {
        if (token === 'CABANG' || token === 'KONSOLIDASI' || token === 'KORWIL') return;
        if (/^\d+$/.test(token)) return;
        if (KORWIL_NAMES.has(token)) set.add(`KOR:${token}`);
        else set.add(`RAW:${token}`);
      });
    }

    function areaAliases(value, label = '') {
      const aliases = new Set();
      addAlias(aliases, value);
      addAlias(aliases, label);
      return aliases;
    }

    function preferredAlias(aliases) {
      return Array.from(aliases).find(value => value.startsWith('CAB:'))
        || Array.from(aliases).find(value => value.startsWith('KOR:'))
        || (aliases.has('ALL') ? 'ALL' : Array.from(aliases)[0] || '');
    }

    function readSaved() {
      try {
        const saved = JSON.parse(localStorage.getItem(getStorageKey()) || 'null');
        if (!saved) return null;
        if (typeof saved === 'string') {
          return { value: saved, canonical: preferredAlias(areaAliases(saved)) };
        }
        if (saved.canonical || saved.value) {
          return {
            value: String(saved.value || ''),
            canonical: String(saved.canonical || preferredAlias(areaAliases(saved.value, saved.label)))
          };
        }
      } catch (error) {}
      return null;
    }

    function saveSelect(select) {
      const option = select.selectedOptions?.[0];
      const value = String(select.value || '');
      const label = String(option?.textContent || '').trim();
      const aliases = areaAliases(value, label);
      const payload = {
        value,
        label,
        canonical: preferredAlias(aliases),
        saved_at: new Date().toISOString()
      };
      try { localStorage.setItem(getStorageKey(), JSON.stringify(payload)); } catch (error) {}
      select.dataset.monbisGlobalAreaValue = value;
      select.dataset.monbisGlobalAreaScope = getUserScope();
    }

    function restoreSelect(select, saved) {
      if (!saved?.canonical) return false;

      const option = Array.from(select.options || []).find(item => {
        const aliases = areaAliases(item.value, item.textContent);
        return aliases.has(saved.canonical) || String(item.value) === saved.value;
      });
      if (!option) return false;

      if (select.value === option.value) return true;
      select.value = option.value;
      if (select.value !== option.value) return false;

      // Trigger handler halaman agar data langsung memakai area tersimpan.
      select.dispatchEvent(new Event('change', { bubbles: true }));
      return true;
    }

    function restoreAll() {
      const saved = readSaved();
      if (!saved) return;
      document.querySelectorAll(AREA_SELECTOR).forEach(select => restoreSelect(select, saved));
    }

    function scheduleRestore() {
      clearTimeout(restoreTimer);
      restoreTimer = setTimeout(restoreAll, 0);
    }

    function init() {
      document.addEventListener('change', event => {
        const select = event.target?.closest?.(AREA_SELECTOR);
        if (select) saveSelect(select);
      }, true);

      restoreAll();

      if (document.body) {
        const observer = new MutationObserver(scheduleRestore);
        observer.observe(document.body, { childList: true, subtree: true });
      }

      // Auth user bisa baru tersedia sesaat setelah halaman selesai dirender.
      let tries = 0;
      const timer = setInterval(() => {
        tries += 1;
        restoreAll();
        if (tries >= 20) clearInterval(timer);
      }, 250);
    }

    window.MonbisGlobalAreaFilter = {
      restore: restoreAll,
      save: saveSelect,
      key: getStorageKey
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
    window.addEventListener('storage', event => {
      if (event.key === getStorageKey()) scheduleRestore();
    });
  })();

  (function () {
    function readUser() {
      if (typeof window.getUser === 'function') {
        const direct = window.getUser();
        if (direct) return direct;
      }
      for (const key of ['dpk_user', 'app_user', 'user']) {
        try {
          const parsed = JSON.parse(localStorage.getItem(key) || 'null');
          if (parsed) return parsed;
        } catch (error) {}
      }
      return null;
    }

    function isOperasional(user) {
      const fields = [
        user?.job_position,
        user?.unit_kerja,
        user?.branch_name,
        user?.role
      ].map(value => String(value || '').toLowerCase());
      return fields.some(value => value.includes('divisi operasional')) || fields.includes('dev');
    }

    function isHeadOfficePe(user) {
      const office = String(user?.kode_kantor ?? user?.kode ?? '').trim().padStart(3, '0');
      const group = String(user?.group_jabatan ?? user?.groupJabatan ?? '').trim().toUpperCase();
      return office === '000' && group === 'PE';
    }

    function resolvePegId(user) {
      const keys = ['id_peg', 'idPeg', 'id_pegawai', 'idPegawai', 'employee_id'];
      for (const key of keys) {
        const value = String(user?.[key] || '').trim();
        if (value === '102-119') return value;
      }
      return '';
    }

    function canAccessInputRbb(user) {
      return resolvePegId(user) === '102-119';
    }
    window.MonbisRbbAccess = canAccessInputRbb;

    function applyDevMenuVisibility() {
      const menu = document.getElementById('menuDevReport');
      const reportMenu = document.getElementById('menuMonevDev');
      const menuInputRbb = document.getElementById('menuInputRbb');
      const adminMenu = document.getElementById('menuEventAdmin');
      const paparanRbbMenu = document.getElementById('menuPaparanRbb');
      const user = readUser();
      const operational = !!user && isOperasional(user);
      const headOfficePe = !!user && isHeadOfficePe(user);
      if (reportMenu) {
        reportMenu.style.display = headOfficePe || operational ? 'block' : 'none';
        reportMenu.querySelectorAll('a').forEach(link => {
          const neracaLink = link.getAttribute('href') === 'lap_neraca';
          link.style.display = neracaLink && !operational ? 'none' : '';
        });
      }
      if (menu) {
        menu.style.display = operational ? 'block' : 'none';
        menu.querySelectorAll('a').forEach(link => {
          link.style.display = operational ? '' : 'none';
        });
      }
      if (menuInputRbb) {
        menuInputRbb.style.display = canAccessInputRbb(user) ? 'block' : 'none';
      }
      if (paparanRbbMenu) {
        paparanRbbMenu.style.display = operational ? '' : 'none';
      }
      if (adminMenu) adminMenu.style.setProperty('display', user && resolvePegId(user) === '102-119' ? 'block' : 'none', 'important');
      return !!user;
    }

    document.addEventListener('DOMContentLoaded', () => {
      let tries = 0;
      applyDevMenuVisibility();
      const timer = setInterval(() => {
        tries += 1;
        if (applyDevMenuVisibility() || tries >= 25) clearInterval(timer);
      }, 200);
    });
  })();
</script>

<!-- Wrapper Utama: Full Screen -->
<div class="monbis-app-shell flex h-screen bg-slate-50 font-sans overflow-hidden relative">

  <!-- ================= OVERLAY MOBILE SIDEBAR ================= -->
  <!-- z-[90] di atas tabel, di bawah sidebar -->
  <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-[90] hidden md:hidden"></div>

  <!-- ================= 1. SIDEBAR (Slide Mobile & Hover Desktop) ================= -->
  <!-- z-[100] Jalan Tengah: Menang telak dari Tabel, tapi tetap di bawah Modal aplikasi (z-1050) -->
  <aside id="sidebar" class="absolute md:relative z-[100] h-full flex flex-col bg-white border-r border-slate-200 shrink-0 transition-all duration-300 ease-in-out -translate-x-full md:translate-x-0 w-64 md:w-[4.5rem] md:hover:w-64 group">
    
    <!-- Bagian Logo (Di Sidebar) -->
    <div class="h-16 flex items-center px-4 border-b border-slate-200 shrink-0 whitespace-nowrap">
      <img src="./img/monbis-icon.webp?v=1" class="monbis-logo--icon h-8 w-8 object-contain shrink-0" alt="Monbis">
      <img src="./img/monbis-judul.webp?v=1" class="monbis-logo--wordmark object-contain shrink-0" alt="Monbis Monitoring Bisnis">
    </div>

    <!-- Navigasi Menu -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-2 space-y-1 custom-scrollbar">
      
      <!-- Menu Single -->
      <a href="dashboard" class="flex items-center px-3 py-2.5 text-blue-600 bg-blue-50 rounded-lg font-medium transition-colors whitespace-nowrap">
        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5.5A1.5 1.5 0 015.5 4h13A1.5 1.5 0 0120 5.5v13a1.5 1.5 0 01-1.5 1.5h-13A1.5 1.5 0 014 18.5v-13zM8 16V9m4 7v-4m4 4V7"></path></svg>
        <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">Dashboard</span>
      </a>

      <!-- Parent Pemasaran -->
      <div class="accordion-group">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20a8 8 0 100-16 8 8 0 000 16zM12 7v5l3 2M5 19l3-3m11 3l-3-3"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">Pemasaran</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">
          <!-- <a href="realisasi_kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Realisasi Kredit</a> -->
          <a href="realisasi_kredit_growth" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Realisasi Kredit</a>
          <a href="realisasi_ao" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Realisasi Kredit AO</a>
          <a href="realisasi_rbb" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Produksi vs RBB</a>
          
          <a href="migrasi_bucket_sc" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Migrasi Soft Collection</a>
          
          <a href="recom_pipelane" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Rekomendasi Pipelane AO Kredit</a>
          <a href="jatuh_tempo" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Jatuh Tempo Kredit</a>
        </div>
      </div>

      <!-- Parent NPL -->
      <div class="accordion-group">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">Monitoring</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">

          <a href="search_debitur" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Search Debitur Kredit</a>
          <a href="mob" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">MOB 6 Bulan</a>
          <a href="otp_baru" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Ontime Payment (OTP)</a>
          <a href="rekap_rr" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Repayment Rate (RR)</a>
          <!-- <a href="perbandingan_npl" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Perbandingan NPL</a> -->
          <a href="potensi_npl" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Potensi NPL</a>
          <a href="flow_par" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Flow Par</a>
          <a href="otp_bucket_fe" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Otp Bucket FE (31-90)</a>
        </div>
      </div>

      <!-- Parent PH -->
      <!-- <div class="accordion-group">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">PH</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">
          <a href="report_ph" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Recovery PH</a>
          <a href="lgd" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Rekap Recovery (LGD)</a>
        </div>
      </div> -->

      <!-- Parent Collection -->
      <div class="accordion-group">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">Collection</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">
          <a href="npl" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report NPL</a>
          <a href="migrasi_kolek" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Migrasi Kolek</a>
          <a href="actual_kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report Actual Kredit</a>
          <a href="migrasi_bucket" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Migrasi Bucket</a>
          <a href="recovery_npl" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Recovery NPL</a>
          <a href="npl_25_besar" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">25 NPL Besar</a>
          <a href="recovery_ph" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Hapus Buku</a>
          <a href="maping_ao_remedial" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Mapping AO Remedial</a>
        </div>
      </div>

      <!-- Parent Laporan (Khusus Dev) -->
      <div id="menuMonevDev" class="accordion-group" style="display: none;">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">Laporan</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">
          <a href="lapkeu_kantor" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Laporan Keuangan</a>
          <a href="lap_neraca" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Lap Neraca</a>
          <a href="lap_laba_rugi" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Lap Laba Rugi</a>
          <a href="rekap_lapkeu" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Rekap Lapkeu</a>
          <a href="rbb_vs_realisasi" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">RBB vs Realisasi</a>
          <a href="ikhtisar" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Ikhtisar</a>
          <!-- Menu Realisasi RBB 2026 disembunyikan sementara. -->
          <a id="menuPaparanRbb" href="paparan_rbb_realisasi" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Paparan RBB Direksi</a>
          <!-- <a href="realisasi_rbb" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Produksi vs RBB</a> -->

          <a href="aging_kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Rekap Aging Kredit</a>
        </div>
      </div>
      
      <!-- Parent Layanan Digital (Khusus Dev) -->
      <div id="menuLayananDigital" class="accordion-group" style="display: none;">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">Layanan Digital</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">
          <a href="layanan_digital" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Dashboard Layanan Digital</a>
          <a href="va" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Virtual Account (VA)</a>
          <a href="branchless" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Branchless</a>
          <a href="qris_merchant" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">QRIS Merchant</a>
        </div>
      </div>

            <!-- Parent Dev Report (Khusus Divisi Operasional) -->
      <div id="menuDevReport" class="accordion-group" style="display: none;">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20h4M4 7h16M5 7l2 13h10l2-13M9 7V4h6v3"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">Dev Report</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">
          <a href="report_npl" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report NPL</a>
          <a href="report_recovery_npl" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report Recovery NPL</a>
          <a href="report_mutasi_kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report Mutasi Kredit</a>
          <a href="report_potensi_npl" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report Potensi NPL</a>
          <a href="report_flowpar" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report Flow PAR</a>
          <a href="rbb_produksi_kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report Produksi vs RBB</a>
          <a href="report_realisasi_ao" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report Realisasi AO</a>
          <a href="report_otp" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Report OTP</a>
          <a href="pipelane_monitoring_kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Monitoring Pipeline Kredit</a>
          <a href="prospek" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Pipelane Prospek</a>
        </div>
      </div>

      <!-- Parent Input RBB (sementara khusus employee_id/id_peg 102-119) -->
      <div id="menuInputRbb" class="accordion-group" style="display: none;">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V5m0 14h16M8 16v-4m4 4V8m4 8v-7"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">Input RBB</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">
          <a href="input_rbb" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Proyeksi RBB</a>
          <a href="input_rbb_aba" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Input RBB ABA</a>
          <a href="input_rbb_detail?bagian=kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Input RBB Kredit</a>
          <a href="input_rbb_detail?bagian=damas" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Input RBB DAMAS</a>
          <a href="input_rbb_detail?bagian=pendapatan" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Input RBB Pendapatan</a>
          <a href="input_rbb_detail?bagian=beban" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Input RBB Beban</a>
        </div>
      </div>

      <!-- Parent KPI Bisnis (termasuk evaluasi Raport Cabang) -->
      <div id="menuKpiBisnis" class="accordion-group" style="display: none;">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" d="M4 19V5m0 14h16M8 16v-4m4 4V8m4 8v-7"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">KPI Bisnis</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">
          <a href="setting_kpi_jabatan" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Setting KPI Jabatan</a>
           <a href="hitung_kpi_ao" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Nilai KPI AO</a>
           <a href="generate_kpi_ao" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Generate KPI AO</a>
           <a href="rekap_kpi_ao" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Rekap KPI AO</a>
           <a href="raport_cabang" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Raport Cabang</a>
        </div>
      </div>

      <!-- Parent Admin Event (Khusus id_peg 102-119) -->
      <div id="menuEventAdmin" class="accordion-group" style="display: none;">
        <button class="accordion-btn w-full flex items-center justify-between px-3 py-2.5 text-slate-700 rounded-lg hover:bg-slate-100 font-medium transition-colors whitespace-nowrap focus:outline-none">
          <div class="flex items-center shrink-0">
            <svg class="w-6 h-6 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4h10a2 2 0 012 2v3.5a2 2 0 01-.586 1.414l-5.5 5.5a2 2 0 01-2.828 0l-4.5-4.5A2 2 0 015 10.5V6a2 2 0 012-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8h.01M4 20h16"></path></svg>
            <span class="ml-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300">Admin</span>
          </div>
          <svg class="caret w-4 h-4 shrink-0 transition-transform text-slate-400 opacity-100 md:opacity-0 md:group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
        <div class="accordion-content hidden pl-[3.25rem] pr-2 py-1 space-y-1">
          <a href="event_theme_admin" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Setting Event</a>
        </div>
      </div>
    </nav>
    <div id="monbisSidebarPromo" class="monbis-sidebar-promo" role="button" tabindex="0" aria-label="Buka Asisten Data">
      <div class="monbis-sidebar-promo__inner">
        <div class="monbis-sidebar-promo__spark" aria-hidden="true">
          <svg class="w-5 h-5 monbis-sidebar-promo__spark--electric" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          <svg class="w-5 h-5 monbis-sidebar-promo__spark--chat" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="4" y="7" width="16" height="13" rx="3" stroke-width="2"></rect><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7V4h3M15 7V4h-3M2 13h2m16 0h2M9 13h.01M15 13h.01M9 17h6"></path></svg>
        </div>
        <div class="monbis-sidebar-promo__text">
          <strong id="monbisSidebarPromoTitle">Semangat kerja</strong>
          <span id="monbisSidebarPromoSubtitle">Data rapi, keputusan cepat</span>
        </div>
      </div>
    </div>
  </aside>

  <!-- ================= KONTEN KANAN ================= -->
  <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
    
    <!-- HEADER ATAS KONTEN -->
    <!-- z-[80] biar Navbar Header aman nutupin konten, tapi di bawah Overlay & Sidebar -->
    <header id="mainNavbar" class="relative h-16 bg-white border-b border-slate-200 flex items-center px-4 sm:px-6 z-[80] shadow-sm shrink-0">
      
      <!-- Tombol Hamburger Mobile -->
      <button id="btnToggleSidebar" class="md:hidden text-slate-500 hover:text-slate-800 focus:outline-none mr-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
      </button>

      <!-- Logo Monbis Mobile -->
      <div class="flex items-center md:hidden">
        <img src="./img/monbis-icon.webp?v=1" class="h-8 w-8 object-contain mr-2" alt="Monbis">
      </div>

      <div id="monbisEventBadge" class="monbis-event-badge ml-2 sm:ml-0" title="">
        <img id="monbisEventImage" class="monbis-event-badge__image hidden" alt="Event">
        <span id="monbisEventTitle" class="monbis-event-badge__title"></span>
      </div>

      <div class="flex-1"></div>

      <?php if (($page ?? '') === 'dashboard'): ?>
      <form id="formFilterMaster" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter dashboard">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter halaman</strong>
          </div>
          <button type="button" id="btnCloseFilter" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="filter_closing">Closing M-1</label>
            <input type="date" id="filter_closing" onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="filter_harian">Harian / Actual</label>
            <input type="date" id="filter_harian" onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="filter_nominal">Basis Nominal</label>
            <select id="filter_nominal">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="filter_kantor">Area / Cabang</label>
            <select id="filter_kantor" data-monbis-global-area></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'npl'): ?>
      <div id="nplNavbarFilterPanel" class="npl-navbar-filter-panel hidden" aria-label="Filter Monitoring Kredit">
        <div class="npl-navbar-filter-panel__head">
          <div>
            <span class="npl-navbar-filter-panel__eyebrow">Filter</span>
            <strong class="npl-navbar-filter-panel__title">Atur filter halaman</strong>
          </div>
          <button type="button" id="mkNavbarFilterClose" class="npl-navbar-filter-panel__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <form id="mkFilterForm" class="npl-navbar-filter-form kolek-mode" onsubmit="event.preventDefault(); fetchActiveCreditTab(true);">
          <div id="mkFieldMode" class="min-w-0">
            <label class="mk-label" for="hitungBerdasarkanCredit">Tipe Saldo</label>
            <select id="hitungBerdasarkanCredit" class="mk-input">
              <option value="saldo_bank" selected>SALDO BANK</option>
              <option value="baki_debet">BAKI DEBET</option>
            </select>
          </div>
          <div id="mkFieldClosing" class="hidden min-w-0">
            <label class="mk-label" for="closingDateCredit">Closing (M-1)</label>
            <input type="date" id="closingDateCredit" class="mk-input" required onclick="this.showPicker && this.showPicker()">
          </div>
          <div id="mkFieldActual" class="min-w-0">
            <label class="mk-label" for="actualDateCredit">Actual (Harian)</label>
            <input type="date" id="actualDateCredit" class="mk-input" required onclick="this.showPicker && this.showPicker()">
          </div>
          <div id="mkFieldArea" class="min-w-0">
            <label class="mk-label" for="optAreaCredit">Area / Cabang</label>
            <select id="optAreaCredit" class="mk-input" data-monbis-global-area><option value="ALL">Memuat...</option></select>
          </div>
        </form>
      </div>
      <?php endif; ?>

      <?php if (($page ?? '') === 'npl_25_besar'): ?>
      <div id="npl25NavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter 25 Debitur Terbesar NPL">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter 25 Debitur Terbesar NPL</strong>
          </div>
          <button type="button" id="npl25NavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <form id="formFilterTopNpl" class="dashboard-navbar-filter__fields" aria-label="Filter 25 Debitur Terbesar NPL">
          <div class="dashboard-navbar-filter__field">
            <label for="selCabangNpl">Area / Cabang</label>
            <select id="selCabangNpl" data-monbis-global-area>
              <option value="">Konsolidasi</option>
            </select>
          </div>
        </form>
      </div>
      <?php endif; ?>

      <?php if (($page ?? '') === 'realisasi_ao'): ?>
      <form id="realisasiAoNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Realisasi AO" onsubmit="event.preventDefault()">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Realisasi AO</strong>
          </div>
          <button type="button" id="realisasiAoNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="tgl_awal">Closing (M-1)</label>
            <input type="date" id="tgl_awal" onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="tgl_akhir">Harian / Actual</label>
            <input type="date" id="tgl_akhir" onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="filter_kantor">Area / Cabang</label>
            <select id="filter_kantor" data-monbis-global-area><option value="ALL">Memuat...</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'realisasi_kredit_growth'): ?>
      <form id="realisasiGrowthNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Realisasi dan Growth" onsubmit="event.preventDefault()">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Realisasi &amp; Growth</strong>
          </div>
          <button type="button" id="realisasiGrowthNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="closing_date">Closing (M-1)</label>
            <input type="date" id="closing_date" onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date">Harian / Actual</label>
            <input type="date" id="harian_date" onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_area">Area / Cabang</label>
            <select id="opt_area" data-monbis-global-area><option value="ALL">Memuat...</option></select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="realisasiGrowthNominalField">Nominal</label>
            <select id="realisasiGrowthNominalField">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'realisasi_rbb'): ?>
      <form id="realisasiRbbNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Realisasi vs RBB" onsubmit="event.preventDefault()">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Realisasi vs RBB</strong>
          </div>
          <button type="button" id="realisasiRbbNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="rbb_harian_date">Harian / Actual</label>
            <input type="date" id="rbb_harian_date" onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="rbb_kantor">Area / Cabang</label>
            <select id="rbb_kantor" data-monbis-global-area>
              <option value="000">000 - Konsolidasi</option>
              <option value="SEMARANG">Korwil Semarang</option>
              <option value="SOLO">Korwil Solo</option>
              <option value="BANYUMAS">Korwil Banyumas</option>
              <option value="PEKALONGAN">Korwil Pekalongan</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="rbb_compare_mode">Pembanding</label>
            <select id="rbb_compare_mode">
              <option value="auto">Auto</option>
              <option value="rbb">RBB</option>
              <option value="history">History YoY</option>
            </select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'mob'): ?>
      <form id="mobNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter MOB dan FPD" onsubmit="event.preventDefault(); fetchRekapMob();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter MOB / FPD</strong>
          </div>
          <button type="button" id="mobNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date_mob">Posisi Data</label>
            <input type="date" id="harian_date_mob" required onclick="this.showPicker && this.showPicker()" onchange="fetchRekapMob()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="status_jatuh_tempo_mob">Status Jatuh Tempo</label>
            <select id="status_jatuh_tempo_mob" onchange="fetchRekapMob()">
              <option value="ALL" selected>Semua Status</option>
              <option value="SUDAH_LEWAT">Sudah Lewat JT</option>
              <option value="BELUM_LEWAT">Belum Lewat JT</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="tipe_saldo_mob">Basis Nominal</label>
            <select id="tipe_saldo_mob" onchange="fetchRekapMob()">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_area">Area / Cabang</label>
            <select id="opt_area" data-monbis-global-area onchange="updateFilterUI()"><option value="ALL">Memuat...</option></select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label id="lbl_sub" for="opt_sub_main">Korwil</label>
            <select id="opt_sub_main" onchange="fetchRekapMob()"><option value="ALL">ALL KORWIL</option></select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_ao_main">AO Kredit</label>
            <select id="opt_ao_main" onchange="fetchRekapMob()" disabled><option value="ALL">PILIH CABANG DULU</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'rekap_rr'): ?>
      <form id="rrNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Repayment Rate" onsubmit="event.preventDefault(); fetchRekap();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Repayment Rate</strong>
          </div>
          <button type="button" id="rrNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="closing_date">Closing (M-1)</label>
            <input type="date" id="closing_date" required onclick="this.showPicker && this.showPicker()" onchange="fetchRekap()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date">Actual (Harian)</label>
            <input type="date" id="harian_date" required onclick="this.showPicker && this.showPicker()" onchange="fetchRekap()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="tipe_saldo_rr">Basis Nominal</label>
            <select id="tipe_saldo_rr" onchange="fetchRekap()">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_kantor">Area / Cabang</label>
            <select id="opt_kantor" data-monbis-global-area onchange="handleRRAreaChange()"><option value="000">Memuat...</option></select>
          </div>
          <div class="dashboard-navbar-filter__field rr-breakdown-field is-hidden">
            <label for="rr_breakdown_by">Breakdown</label>
            <select id="rr_breakdown_by" onchange="fetchRekap()">
              <option value="KANKAS">Per Kankas</option>
              <option value="AO">Per AO Kredit</option>
            </select>
          </div>
        </div>
      </form>
      <?php endif; ?>


      <?php if (($page ?? '') === 'otp_baru'): ?>
      <form id="otpNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter OTP" onsubmit="event.preventDefault(); fetchRekapRR();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter OTP</strong>
          </div>
          <button type="button" id="otpNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="closing_date">Closing (M-1)</label>
            <input type="date" id="closing_date" required onclick="this.showPicker && this.showPicker()" onchange="fetchRekapRR()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date">Actual (Harian)</label>
            <input type="date" id="harian_date" required onclick="this.showPicker && this.showPicker()" onchange="fetchRekapRR()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_nominal_otp">Basis Nominal</label>
            <select id="opt_nominal_otp" onchange="fetchRekapRR()">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_kantor">Cabang</label>
            <select id="opt_kantor" data-monbis-global-area onchange="handleCabangChangeOtp()"><option value="">Memuat...</option></select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label id="lbl_sub_otp" for="opt_sub_otp">Korwil</label>
            <select id="opt_sub_otp" onchange="fetchRekapRR()"><option value="">ALL KORWIL</option></select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_dpd_bucket">DPD Bucket</label>
            <select id="opt_dpd_bucket" onchange="fetchRekapRR()">
              <option value="all">ALL</option>
              <option value="dpd0">DPD 0</option>
              <option value="dpd1-30">DPD 1-30</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_ao_otp">AO Kredit</label>
            <select id="opt_ao_otp" onchange="fetchRekapRR()" disabled><option value="">PILIH CABANG DULU</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'otp_bucket_fe'): ?>
      <form id="otpBucketNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter OTP Migration" onsubmit="event.preventDefault(); triggerAutoRefresh();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter OTP Migration</strong>
          </div>
          <button type="button" id="otpBucketNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="closing_date_otp">Closing (M-1)</label>
            <input type="date" id="closing_date_otp" required onclick="this.showPicker && this.showPicker()" onchange="triggerAutoRefresh()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date_otp">Actual (Harian)</label>
            <input type="date" id="harian_date_otp" required onclick="this.showPicker && this.showPicker()" onchange="triggerAutoRefresh()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_nominal_otp_bucket">Basis Nominal</label>
            <select id="opt_nominal_otp_bucket" onchange="triggerAutoRefresh()">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="type_bucket_otp">Bucket</label>
            <select id="type_bucket_otp" onchange="triggerAutoRefresh()">
              <option value="fe_all">ALL</option>
              <option value="31-60">31 - 60</option>
              <option value="61-90">61 - 90</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_kantor_otp">Cabang</label>
            <select id="opt_kantor_otp" data-monbis-global-area onchange="handleCabangChange()"><option value="">Memuat...</option></select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label id="lbl_sub_otp" for="opt_sub_otp">Korwil</label>
            <select id="opt_sub_otp" onchange="triggerAutoRefresh()"><option value="">ALL KORWIL</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'migrasi_bucket'): ?>
      <form id="migrasiBucketNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Migrasi DPD" onsubmit="event.preventDefault(); MB_autoFetch();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Migrasi DPD</strong>
          </div>
          <button type="button" id="migrasiBucketNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="MB_closing">Closing (M-1)</label>
            <input type="date" id="MB_closing" required onclick="this.showPicker && this.showPicker()" onchange="MB_checkDate()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="MB_harian">Actual / Proyeksi</label>
            <input type="date" id="MB_harian" required onclick="this.showPicker && this.showPicker()" onchange="MB_checkDate()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="MB_nominalField">Basis Nominal</label>
            <select id="MB_nominalField" onchange="MB_checkDate()">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="MB_optFilter">Area / Cabang</label>
            <select id="MB_optFilter" data-monbis-global-area onchange="MB_filterWilayah()"><option value="000">Konsolidasi</option></select>
          </div>
          <div class="dashboard-navbar-filter__field dashboard-navbar-filter__field--switch">
            <label for="MB_isProyeksi">Mode Data</label>
            <label class="dashboard-navbar-filter__switch">
              <input type="checkbox" id="MB_isProyeksi" onchange="MB_toggleProyeksi()">
              <span>Proyeksi</span>
            </label>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'migrasi_bucket_sc'): ?>
      <form id="migrasiBucketScNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Migrasi Bucket SC" onsubmit="event.preventDefault(); fetchMatrix();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Migrasi Bucket SC</strong>
          </div>
          <button type="button" id="migrasiBucketScNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="closing_date">Closing (M-1)</label>
            <input type="date" id="closing_date" required onclick="this.showPicker && this.showPicker()" onchange="fetchMatrix()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date">Actual (Harian)</label>
            <input type="date" id="harian_date" required onclick="this.showPicker && this.showPicker()" onchange="fetchMatrix()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="migrasiScNominal">Basis Nominal</label>
            <select id="migrasiScNominal" onchange="fetchMatrix()">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_kantor">Area / Cabang / Korwil</label>
            <select id="opt_kantor" data-monbis-global-area onchange="updateTitleCabangMigrasi(); fetchMatrix()"><option value="">Konsolidasi</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'migrasi_kolek'): ?>
      <form id="migrasiKolekNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Migrasi Kolektibilitas" onsubmit="event.preventDefault(); fetchMigrasiKolekFromNavbar();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Migrasi Kolektibilitas</strong>
          </div>
          <button type="button" id="migrasiKolekNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="migrasiKolekClosing">Closing (M-1)</label>
            <input type="date" id="migrasiKolekClosing" required onclick="this.showPicker && this.showPicker()" onchange="fetchMigrasiKolekFromNavbar()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="migrasiKolekHarian">Actual (Harian)</label>
            <input type="date" id="migrasiKolekHarian" required onclick="this.showPicker && this.showPicker()" onchange="fetchMigrasiKolekFromNavbar()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="migrasiKolekNominal">Basis Nominal</label>
            <select id="migrasiKolekNominal" onchange="fetchMigrasiKolekFromNavbar()">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="migrasiKolekKantor">Area / Cabang / Korwil</label>
            <select id="migrasiKolekKantor" data-monbis-global-area onchange="fetchMigrasiKolekFromNavbar()"><option value="">Konsolidasi</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'actual_kredit'): ?>
      <form id="actualKreditNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Report Actual Kredit" onsubmit="event.preventDefault(); fetchActualKreditFromNavbar();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Report Actual Kredit</strong>
          </div>
          <button type="button" id="actualKreditNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="closing_date_kolek">Closing (M-1)</label>
            <input type="date" id="closing_date_kolek" required onclick="this.showPicker && this.showPicker()" onchange="fetchActualKreditFromNavbar()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date_kolek">Actual (Harian)</label>
            <input type="date" id="harian_date_kolek" required onclick="this.showPicker && this.showPicker()" onchange="fetchActualKreditFromNavbar()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_nominal_kolek">Basis Nominal</label>
            <select id="opt_nominal_kolek">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_kantor_kolek">Area / Cabang</label>
            <select id="opt_kantor_kolek" data-monbis-global-area><option value="">Konsolidasi</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'recovery_npl'): ?>
      <form id="recoveryNplNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Recovery NPL" onsubmit="event.preventDefault(); fetchRecoveryData();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Recovery NPL</strong>
          </div>
          <button type="button" id="recoveryNplNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="closing_date_recovery">Closing (M-1)</label>
            <input type="date" id="closing_date_recovery" required onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date_recovery">Actual (Harian)</label>
            <input type="date" id="harian_date_recovery" required onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_nominal_recovery">Basis Nominal</label>
            <select id="opt_nominal_recovery">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_kantor_recovery">Area / Cabang</label>
            <select id="opt_kantor_recovery" data-monbis-global-area><option value="ALL">Konsolidasi</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'recovery_ph'): ?>
      <form id="recoveryPhNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Monitoring PH" onsubmit="event.preventDefault();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Monitoring PH</strong>
          </div>
          <button type="button" id="recoveryPhNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div id="recoveryPhStartField" class="dashboard-navbar-filter__field">
            <label for="recoveryStartDate">Dari</label>
            <input type="date" id="recoveryStartDate" required onclick="this.showPicker && this.showPicker()">
          </div>
          <div id="recoveryPhEndField" class="dashboard-navbar-filter__field">
            <label for="recoveryEndDate">Sampai</label>
            <input type="date" id="recoveryEndDate" required onclick="this.showPicker && this.showPicker()">
          </div>
          <div id="recoveryPhLgdField" class="dashboard-navbar-filter__field hidden">
            <label for="lgdPositionDate">Posisi Data LGD</label>
            <input type="date" id="lgdPositionDate" required onclick="this.showPicker && this.showPicker()">
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'layanan_digital'): ?>
      <form id="layananDigitalNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Layanan Digital" onsubmit="event.preventDefault();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Layanan Digital</strong>
          </div>
          <button type="button" id="layananDigitalNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field"><label for="closing_date">Closing (M-1)</label><input type="date" id="closing_date" required onclick="this.showPicker && this.showPicker()"></div>
          <div class="dashboard-navbar-filter__field"><label for="harian_date">Harian / Actual</label><input type="date" id="harian_date" required onclick="this.showPicker && this.showPicker()"></div>
          <div class="dashboard-navbar-filter__field"><label for="opt_area">Area / Cabang</label><select id="opt_area" data-monbis-global-area><option value="KONSOLIDASI">Konsolidasi</option><option value="KORWIL_SEMARANG">Korwil Semarang</option><option value="KORWIL_SOLO">Korwil Solo</option><option value="KORWIL_BANYUMAS">Korwil Banyumas</option><option value="KORWIL_PEKALONGAN">Korwil Pekalongan</option><optgroup label="Berdasarkan Cabang" id="opt_cabang_list"></optgroup></select></div>
          <div class="dashboard-navbar-filter__field"><label for="ldChannelSelect">Channel</label><select id="ldChannelSelect"><option value="VA">Virtual Account (VA)</option><option value="BRANCHLESS">Branchless</option><option value="QRIS">QRIS Merchant</option></select></div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'va'): ?>
      <form id="vaNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Virtual Account" onsubmit="event.preventDefault();">
        <div class="dashboard-navbar-filter__head"><div><span class="dashboard-navbar-filter__eyebrow">Filter</span><strong class="dashboard-navbar-filter__title">Atur filter Virtual Account</strong></div><button type="button" id="vaNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg></button></div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field"><label for="closing_date">Closing (M-1)</label><input type="date" id="closing_date" required onclick="this.showPicker && this.showPicker()"></div>
          <div class="dashboard-navbar-filter__field"><label for="harian_date">Harian / Actual</label><input type="date" id="harian_date" required onclick="this.showPicker && this.showPicker()"></div>
          <div class="dashboard-navbar-filter__field"><label for="opt_area">Area / Cabang</label><select id="opt_area" data-monbis-global-area><option value="KONSOLIDASI">Konsolidasi</option><option value="KORWIL_SEMARANG">Korwil Semarang</option><option value="KORWIL_SOLO">Korwil Solo</option><option value="KORWIL_BANYUMAS">Korwil Banyumas</option><option value="KORWIL_PEKALONGAN">Korwil Pekalongan</option><optgroup label="Berdasarkan Cabang" id="opt_cabang_list"></optgroup></select></div>
          <div class="dashboard-navbar-filter__field"><label for="vaViewSelect">Tampilan</label><select id="vaViewSelect"><option value="rekap">Rekap</option><option value="chart">Chart</option><option value="detail">Detail</option></select></div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'branchless'): ?>
      <form id="branchlessNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Branchless Banking" onsubmit="event.preventDefault();">
        <div class="dashboard-navbar-filter__head"><div><span class="dashboard-navbar-filter__eyebrow">Filter</span><strong class="dashboard-navbar-filter__title">Atur filter Branchless Banking</strong></div><button type="button" id="branchlessNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg></button></div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field"><label for="closing_date">Closing (M-1)</label><input type="date" id="closing_date" required onclick="this.showPicker && this.showPicker()"></div>
          <div class="dashboard-navbar-filter__field"><label for="harian_date">Harian / Actual</label><input type="date" id="harian_date" required onclick="this.showPicker && this.showPicker()"></div>
          <div class="dashboard-navbar-filter__field"><label for="opt_area">Area / Cabang</label><select id="opt_area" data-monbis-global-area><option value="KONSOLIDASI">Konsolidasi</option><option value="KORWIL_SEMARANG">Korwil Semarang</option><option value="KORWIL_SOLO">Korwil Solo</option><option value="KORWIL_BANYUMAS">Korwil Banyumas</option><option value="KORWIL_PEKALONGAN">Korwil Pekalongan</option><optgroup label="Berdasarkan Cabang" id="opt_cabang_list"></optgroup></select></div>
          <div class="dashboard-navbar-filter__field"><label for="branchlessViewSelect">Tampilan</label><select id="branchlessViewSelect"><option value="rekap">Rekap</option><option value="chart">Chart</option><option value="device">Device</option></select></div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'qris_merchant'): ?>
      <form id="qrisMerchantNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter QRIS Merchant" onsubmit="event.preventDefault();">
        <div class="dashboard-navbar-filter__head"><div><span class="dashboard-navbar-filter__eyebrow">Filter</span><strong class="dashboard-navbar-filter__title">Atur filter QRIS Merchant</strong></div><button type="button" id="qrisMerchantNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg></button></div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field"><label for="closing_date">Closing (M-1)</label><input type="date" id="closing_date" required onclick="this.showPicker && this.showPicker()"></div>
          <div class="dashboard-navbar-filter__field"><label for="harian_date">Harian / Actual</label><input type="date" id="harian_date" required onclick="this.showPicker && this.showPicker()"></div>
          <div class="dashboard-navbar-filter__field"><label for="opt_area">Area / Cabang</label><select id="opt_area" data-monbis-global-area><option value="KONSOLIDASI">Konsolidasi</option><option value="KORWIL_SEMARANG">Korwil Semarang</option><option value="KORWIL_SOLO">Korwil Solo</option><option value="KORWIL_BANYUMAS">Korwil Banyumas</option><option value="KORWIL_PEKALONGAN">Korwil Pekalongan</option><optgroup label="Berdasarkan Cabang" id="opt_cabang_list"></optgroup></select></div>
          <div class="dashboard-navbar-filter__field"><label for="qrisViewSelect">Tampilan</label><select id="qrisViewSelect"><option value="rekap">Rekap</option><option value="chart">Chart</option></select></div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'potensi_npl'): ?>
      <form id="potensiNplNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Potensi NPL">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Potensi NPL</strong>
          </div>
          <button type="button" id="potensiNplNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="potensiNplClosing">Closing (M-1)</label>
            <input type="date" id="potensiNplClosing" required onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="potensiNplActual">Actual (Harian)</label>
            <input type="date" id="potensiNplActual" required onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="potensiNplNominal">Basis Nominal</label>
            <select id="potensiNplNominal">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="potensiNplArea">Area / Cabang</label>
            <select id="potensiNplArea" data-monbis-global-area><option value="">Memuat...</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'flow_par'): ?>
      <form id="flowParNavbarFilterForm" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Flow PAR" onsubmit="event.preventDefault(); applyFlowParFilter();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Flow PAR</strong>
          </div>
          <button type="button" id="flowParNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="closing_date">Closing (M-1)</label>
            <input type="date" id="closing_date" required onclick="this.showPicker && this.showPicker()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date">Harian / Actual</label>
            <input type="date" id="harian_date" required onclick="this.showPicker && this.showPicker()" onchange="fetchRekap()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="fpNominalMode">Basis Nominal</label>
            <select id="fpNominalMode">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="opt_kantor_rec">Area / Cabang</label>
            <select id="opt_kantor_rec" data-monbis-global-area><option value="ALL">Konsolidasi</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (($page ?? '') === 'jatuh_tempo'): ?>
      <form id="jatuhTempoNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Jatuh Tempo Kredit" onsubmit="event.preventDefault(); fetchRekapJT();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Jatuh Tempo Kredit</strong>
          </div>
          <button type="button" id="jatuhTempoNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="closing_date_jt">Closing (M-1)</label>
            <input type="date" id="closing_date_jt" required onclick="this.showPicker && this.showPicker()" onchange="fetchRekapJT()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date_jt">Harian / Actual</label>
            <input type="date" id="harian_date_jt" required onclick="this.showPicker && this.showPicker()" onchange="fetchRekapJT()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="filter_bulan">Bulan JT</label>
            <select id="filter_bulan" onchange="fetchRekapJT()">
              <option value="01">Januari</option><option value="02">Februari</option><option value="03">Maret</option>
              <option value="04">April</option><option value="05">Mei</option><option value="06">Juni</option>
              <option value="07">Juli</option><option value="08">Agustus</option><option value="09">September</option>
              <option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="filter_tahun">Tahun JT</label>
            <input type="number" id="filter_tahun" min="2000" max="2100" required onchange="fetchRekapJT()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="jt_nominal_field">Basis Nominal</label>
            <select id="jt_nominal_field" onchange="fetchRekapJT()">
              <option value="saldo_bank" selected>Saldo Bank</option>
              <option value="baki_debet">Baki Debet</option>
            </select>
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="jt_kantor_filter">Area / Cabang / Korwil</label>
            <select id="jt_kantor_filter" data-monbis-global-area onchange="handleJTAreaChange()"><option value="ALL">Memuat...</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>

      <?php if (in_array(($page ?? ''), ['recom_pipelane', 'pipelane_ao_jt'], true)): ?>
      <form id="recomPipelaneNavbarFilterPanel" class="dashboard-navbar-filter hidden flex-col" aria-label="Filter Rekomendasi Pipelane AO Kredit" onsubmit="event.preventDefault(); fetchRekap();">
        <div class="dashboard-navbar-filter__head">
          <div>
            <span class="dashboard-navbar-filter__eyebrow">Filter</span>
            <strong class="dashboard-navbar-filter__title">Atur filter Rekomendasi Pipelane AO Kredit</strong>
          </div>
          <button type="button" id="recomPipelaneNavbarFilterClose" class="dashboard-navbar-filter__close" aria-label="Tutup filter" title="Tutup filter">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"></path></svg>
          </button>
        </div>
        <div class="dashboard-navbar-filter__fields">
          <div class="dashboard-navbar-filter__field">
            <label for="harian_date">Posisi Actual</label>
            <input type="date" id="harian_date" required onclick="this.showPicker && this.showPicker()" onchange="fetchRekap()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="tahun_jt">Tahun Jatuh Tempo</label>
            <input type="number" id="tahun_jt" min="2000" max="2100" value="<?= (int) date('Y') ?>" required onchange="fetchRekap()">
          </div>
          <div class="dashboard-navbar-filter__field">
            <label for="recomPipelaneKantorFilter">Area / Cabang / Korwil</label>
            <select id="recomPipelaneKantorFilter" data-monbis-global-area onchange="handleRecomPipelaneAreaChange()"><option value="ALL">Memuat...</option></select>
          </div>
        </div>
      </form>
      <?php endif; ?>
      
      <!-- Area Lonceng & Profile -->
      <div class="flex items-center gap-4 sm:gap-6 ml-auto">
        <?php if (($page ?? '') === 'dashboard'): ?>
        <button type="button" id="btnToggleFilter" class="dashboard-navbar-filter-toggle" aria-label="Buka filter" title="Buka filter" aria-expanded="false">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'npl'): ?>
        <button type="button" id="mkNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Monitoring Kredit" title="Buka filter Monitoring Kredit" aria-expanded="false" aria-controls="nplNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'npl_25_besar'): ?>
        <button type="button" id="npl25NavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter 25 Debitur Terbesar NPL" title="Buka filter 25 Debitur Terbesar NPL" aria-expanded="false" aria-controls="npl25NavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'realisasi_ao'): ?>
        <button type="button" id="realisasiAoNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Realisasi AO" title="Buka filter Realisasi AO" aria-expanded="false" aria-controls="realisasiAoNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'realisasi_kredit_growth'): ?>
        <button type="button" id="realisasiGrowthNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Realisasi dan Growth" title="Buka filter Realisasi dan Growth" aria-expanded="false" aria-controls="realisasiGrowthNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'realisasi_rbb'): ?>
        <button type="button" id="realisasiRbbNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Realisasi vs RBB" title="Buka filter Realisasi vs RBB" aria-expanded="false" aria-controls="realisasiRbbNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'mob'): ?>
        <button type="button" id="mobNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter MOB dan FPD" title="Buka filter MOB dan FPD" aria-expanded="false" aria-controls="mobNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'rekap_rr'): ?>
        <button type="button" id="rrNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Repayment Rate" title="Buka filter Repayment Rate" aria-expanded="false" aria-controls="rrNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'otp_baru'): ?>
        <button type="button" id="otpNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter OTP" title="Buka filter OTP" aria-expanded="false" aria-controls="otpNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'otp_bucket_fe'): ?>
        <button type="button" id="otpBucketNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter OTP Migration" title="Buka filter OTP Migration" aria-expanded="false" aria-controls="otpBucketNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'migrasi_bucket'): ?>
        <button type="button" id="migrasiBucketNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Migrasi DPD" title="Buka filter Migrasi DPD" aria-expanded="false" aria-controls="migrasiBucketNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'migrasi_bucket_sc'): ?>
        <button type="button" id="migrasiBucketScNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Migrasi Bucket SC" title="Buka filter Migrasi Bucket SC" aria-expanded="false" aria-controls="migrasiBucketScNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'migrasi_kolek'): ?>
        <button type="button" id="migrasiKolekNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Migrasi Kolektibilitas" title="Buka filter Migrasi Kolektibilitas" aria-expanded="false" aria-controls="migrasiKolekNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'actual_kredit'): ?>
        <button type="button" id="actualKreditNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Report Actual Kredit" title="Buka filter Report Actual Kredit" aria-expanded="false" aria-controls="actualKreditNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'recovery_npl'): ?>
        <button type="button" id="recoveryNplNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Recovery NPL" title="Buka filter Recovery NPL" aria-expanded="false" aria-controls="recoveryNplNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'recovery_ph'): ?>
        <button type="button" id="recoveryPhNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Monitoring PH" title="Buka filter Monitoring PH" aria-expanded="false" aria-controls="recoveryPhNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'layanan_digital'): ?>
        <button type="button" id="layananDigitalNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Layanan Digital" title="Buka filter Layanan Digital" aria-expanded="false" aria-controls="layananDigitalNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'va'): ?>
        <button type="button" id="vaNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Virtual Account" title="Buka filter Virtual Account" aria-expanded="false" aria-controls="vaNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'branchless'): ?>
        <button type="button" id="branchlessNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Branchless Banking" title="Buka filter Branchless Banking" aria-expanded="false" aria-controls="branchlessNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'qris_merchant'): ?>
        <button type="button" id="qrisMerchantNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter QRIS Merchant" title="Buka filter QRIS Merchant" aria-expanded="false" aria-controls="qrisMerchantNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'potensi_npl'): ?>
        <button type="button" id="potensiNplNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Potensi NPL" title="Buka filter Potensi NPL" aria-expanded="false" aria-controls="potensiNplNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'flow_par'): ?>
        <button type="button" id="flowParNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Flow PAR" title="Buka filter Flow PAR" aria-expanded="false" aria-controls="flowParNavbarFilterForm">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (($page ?? '') === 'jatuh_tempo'): ?>
        <button type="button" id="jatuhTempoNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Jatuh Tempo Kredit" title="Buka filter Jatuh Tempo Kredit" aria-expanded="false" aria-controls="jatuhTempoNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <?php if (in_array(($page ?? ''), ['recom_pipelane', 'pipelane_ao_jt'], true)): ?>
        <button type="button" id="recomPipelaneNavbarFilterToggle" class="dashboard-navbar-filter-toggle" aria-label="Buka filter Rekomendasi Pipelane AO Kredit" title="Buka filter Rekomendasi Pipelane AO Kredit" aria-expanded="false" aria-controls="recomPipelaneNavbarFilterPanel">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"></path></svg>
        </button>
        <?php endif; ?>
        <button id="monbisThemeToggle" type="button" class="monbis-theme-toggle" title="Ganti mode terang / gelap" aria-label="Ganti mode terang / gelap">
          <svg class="monbis-theme-icon-moon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path></svg>
          <svg class="monbis-theme-icon-sun w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414m12.728 0-1.414-1.414M7.05 7.05 5.636 5.636M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
        </button>

        <button class="relative text-slate-500 hover:text-slate-800 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
          <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-red-500"></span>
        </button>
        
        <div class="h-8 border-l border-slate-200"></div>
        
        <div class="relative flex items-center gap-3">
          <div class="hidden sm:flex flex-col leading-tight text-right select-none">
            <span id="navUserName" class="text-slate-800 text-sm font-semibold truncate max-w-[120px]">—</span>
            <span id="navBranch" class="text-slate-500 text-[11px] truncate max-w-[120px]">—</span>
          </div>
          
          <button id="btnProfileMenu" class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center ring-2 ring-white border border-slate-200 shadow-sm hover:ring-blue-200 transition-all focus:outline-none">
             <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
          </button>

          <!-- Dropdown Profile di set ke z-[90] biar gak kalah sama header tabel -->
          <div id="dropdownProfileMenu" class="hidden absolute right-0 top-[2.75rem] mt-2 w-40 bg-white border border-slate-100 rounded-lg shadow-lg py-1 z-[90]">
            <a href="profile" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 font-medium">My Profile</a>
            <div class="border-t border-slate-100 my-1"></div>
            <a href="#" id="linkLogoutDesk" onclick="logoutSSO(event)" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium">Logout</a>
          </div>
        </div>
      </div>
    </header>

    <!-- BUKA AREA KONTEN UTAMA -->
    <main class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-0 sm:px-6 bg-slate-50">
      <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
      </style>

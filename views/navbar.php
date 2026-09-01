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
  :is(html,body,.monbis-app-shell main)::-webkit-scrollbar { width:5px; height:5px; }
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
  #sidebar > .h-16 img {
    width:36px;
    height:36px;
    padding:4px;
    border-radius:14px;
    background:rgba(255,255,255,.86);
    box-shadow:0 12px 24px rgba(15,23,42,.10);
  }
  #sidebar > .h-16 span {
    font-size:20px;
    font-weight:950;
    letter-spacing:-.03em;
  }
  #sidebar nav {
    padding:14px 10px 18px !important;
    scrollbar-width:thin;
  }
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
    animation:monbisPulse 1.8s ease-in-out infinite;
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
  @media (min-width:768px) {
    #sidebar {
      width:78px !important;
    }
    #sidebar:hover {
      width:278px !important;
    }
    #sidebar:not(:hover) .monbis-sidebar-promo {
      min-height:54px;
      border-radius:16px;
    }
    #sidebar:not(:hover) .monbis-sidebar-promo__inner {
      min-height:54px;
      justify-content:center;
      padding:10px;
    }
    #sidebar:not(:hover) .monbis-sidebar-promo__text {
      display:none;
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

    function canAccessMappingAo(user) {
      const job = String(user?.job_position || '').toLowerCase();
      const unit = String(user?.unit_kerja || '').toLowerCase();
      return job.includes('kepala cabang')
        || job.includes('kepala bidang pemasaran')
        || job.includes('kepala sub bidang remedial')
        || unit.includes('divisi operasional')
        || unit.includes('divisi penyelesaian kredit');
    }

    function resolvePegId(user) {
      const keys = ['id_peg', 'idPeg', 'id_pegawai', 'idPegawai', 'employee_id'];
      for (const key of keys) {
        const value = String(user?.[key] || '').trim();
        if (value === '102-119') return value;
      }
      return '';
    }

    function applyDevMenuVisibility() {
      const menu = document.getElementById('menuDevReport');
      const adminMenu = document.getElementById('menuEventAdmin');
      const user = readUser();
      if (menu) {
        const operational = !!user && isOperasional(user);
        const mappingAccess = !!user && canAccessMappingAo(user);
        menu.style.display = operational || mappingAccess ? 'block' : 'none';
        menu.querySelectorAll('a').forEach(link => {
          const mappingLink = link.getAttribute('href') === 'maping_ao_remedial';
          link.style.display = operational || mappingLink ? '' : 'none';
        });
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
      <img src="./img/logodpk.png" class="h-8 w-8 object-contain shrink-0" alt="Logo">
      <span class="text-slate-800 text-xl font-bold tracking-tight opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 ml-3">Monbis</span>
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
          
          <a href="migrasi_bucket_sc" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Migrasi Bucket SC</a>
          
          <a href="pipelane_ao_jt" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Pipelane AO Kredit</a>
          <a href="jatuh_tempo" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Jatuh Tempo & Refinancing</a>
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
          <a href="actual_kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Bucket DPD & Kolek</a>
          <a href="migrasi_bucket" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Migrasi Bucket</a>
          <a href="recovery_npl" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Recovery NPL</a>
          <a href="npl_25_besar" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">25 NPL Besar</a>
          <a href="recovery_ph" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Hapus Buku</a>
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
          <!-- <a href="realisasi_rbb" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Produksi vs RBB</a> -->

          <a href="aging_kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Rekap Aging Kredit</a>
          <a href="pipelane_monitoring_kredit" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Monitoring Pipeline Kredit</a>
          <a href="prospek" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Pipelane Prospek</a>
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
          <a href="maping_ao_remedial" class="block px-2 py-2 text-[11px] truncate text-slate-600 rounded-md hover:text-blue-600 hover:bg-blue-50">Mapping AO Remedial</a>
        </div>
      </div>

      <!-- Parent KPI Bisnis (mengikuti akses menu Laporan) -->
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
    <header id="mainNavbar" class="h-16 bg-white border-b border-slate-200 flex items-center px-4 sm:px-6 z-[80] shadow-sm shrink-0">
      
      <!-- Tombol Hamburger Mobile -->
      <button id="btnToggleSidebar" class="md:hidden text-slate-500 hover:text-slate-800 focus:outline-none mr-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
      </button>

      <!-- Logo Monbis Mobile -->
      <div class="flex items-center md:hidden">
        <img src="./img/logodpk.png" class="h-8 w-8 object-contain mr-2" alt="Logo">
        <span class="text-slate-800 text-lg font-bold tracking-tight">Monbis</span>
      </div>

      <div id="monbisEventBadge" class="monbis-event-badge ml-2 sm:ml-0" title="">
        <img id="monbisEventImage" class="monbis-event-badge__image hidden" alt="Event">
        <span id="monbisEventTitle" class="monbis-event-badge__title"></span>
      </div>

      <div class="flex-1"></div>
      
      <!-- Area Lonceng & Profile -->
      <div class="flex items-center gap-4 sm:gap-6 ml-auto">
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

<?php
/**
 * Shared sidebar behavior and performance rules.
 * Loaded after the navbar markup so it can bind once to the rendered shell.
 */
if (defined('MONBIS_SIDEBAR_BEHAVIOR_LOADED')) return;
define('MONBIS_SIDEBAR_BEHAVIOR_LOADED', true);
?>
<style id="monbisSidebarPerformanceStyle">
  /* Shared navigation polish: stable, readable, and touch-friendly. */
  #mainNavbar {
    position:relative;
    isolation:isolate;
    background:linear-gradient(135deg, rgba(255,255,255,.94), rgba(248,251,255,.88)) !important;
    border-bottom-color:rgba(148,163,184,.24) !important;
    box-shadow:0 8px 24px rgba(15,23,42,.06) !important;
    backdrop-filter:none;
  }
  #mainNavbar::after {
    content:"";
    position:absolute;
    inset:auto 18px 0;
    height:1px;
    background:linear-gradient(90deg, transparent, rgba(37,99,235,.22), transparent);
    pointer-events:none;
  }
  #sidebar {
    min-height:0;
    overflow:hidden !important;
    background:
      radial-gradient(circle at 15% 4%, rgba(52,171,190,.18), transparent 24%),
      linear-gradient(180deg, #103f56 0%, #0c334b 52%, #09283d 100%) !important;
    border-right-color:rgba(125,211,222,.18) !important;
    box-shadow:18px 0 42px rgba(4,25,40,.24) !important;
    color:#d9eef3 !important;
  }
  #sidebar > .h-16 {
    background:linear-gradient(180deg, rgba(7,31,47,.25), rgba(7,31,47,.06));
    border-bottom-color:rgba(148,221,231,.16) !important;
  }
  #sidebar > .h-16 .monbis-logo--wordmark {
    padding:6px 9px;
    border-radius:13px;
    background:rgba(255,255,255,.96);
    box-shadow:0 10px 24px rgba(2,23,36,.20);
  }
  #sidebar .accordion-btn,
  #sidebar nav > a,
  #sidebar .text-slate-700,
  #sidebar .text-slate-600,
  #sidebar .text-slate-800 {
    color:#d8edf2 !important;
  }
  #sidebar .accordion-btn:hover,
  #sidebar nav > a:hover {
    color:#ffffff !important;
    background:rgba(91,196,211,.13) !important;
    border-color:rgba(125,211,222,.20) !important;
    box-shadow:0 10px 22px rgba(2,23,36,.14);
  }
  #sidebar nav > a.is-active,
  #sidebar nav > a.bg-blue-50,
  #sidebar .accordion-btn.is-active {
    color:#ffffff !important;
    background:linear-gradient(100deg, rgba(71,171,190,.34), rgba(54,139,166,.17)) !important;
    border-color:rgba(125,211,222,.38) !important;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.10), 0 12px 24px rgba(2,23,36,.16);
  }
  #sidebar .accordion-btn > div > svg,
  #sidebar nav > a > svg {
    color:#9bcbd4 !important;
    background:rgba(143,211,220,.12);
  }
  #sidebar .accordion-btn:hover > div > svg,
  #sidebar nav > a:hover > svg,
  #sidebar .accordion-btn.is-active > div > svg,
  #sidebar nav > a.is-active > svg {
    color:#ffffff !important;
    background:rgba(113,211,221,.22);
    box-shadow:inset 0 0 0 1px rgba(161,232,237,.16);
  }
  #sidebar nav {
    min-height:0;
    flex:1 1 auto;
    overflow-x:hidden;
    overflow-y:auto;
    overscroll-behavior:contain;
    scrollbar-width:thin;
    scrollbar-color:rgba(100,116,139,.55) transparent;
  }
  #sidebar nav::-webkit-scrollbar { display:block; width:4px; height:4px; }
  #sidebar nav::-webkit-scrollbar-track { background:transparent; }
  #sidebar nav::-webkit-scrollbar-thumb { border-radius:999px; background:rgba(100,116,139,.48); }
  #sidebar nav::-webkit-scrollbar-thumb:hover { background:rgba(37,99,235,.7); }
  #sidebar > .h-16,
  #sidebar > .monbis-sidebar-promo { flex:0 0 auto; }
  #sidebar .accordion-btn,
  #sidebar nav > a { min-height:44px; }
  #sidebar .accordion-btn.is-active {
    color:#1d4ed8 !important;
    background:linear-gradient(90deg, rgba(37,99,235,.13), rgba(14,165,233,.06)) !important;
    border-color:rgba(37,99,235,.18) !important;
    box-shadow:0 8px 18px rgba(37,99,235,.08);
  }
  #sidebar .accordion-content {
    background:rgba(4,27,43,.18);
    border-left-color:rgba(125,211,222,.34);
    border-radius:0 12px 12px 0;
  }
  #sidebar .accordion-content a { min-height:34px; color:#c2dfe5 !important; }
  #sidebar .accordion-content a:hover,
  #sidebar .accordion-content a.is-active {
    color:#ffffff !important;
    background:rgba(84,183,199,.17) !important;
  }
  #sidebar .monbis-sidebar-promo {
    border-color:rgba(125,211,222,.22);
    background:linear-gradient(135deg, rgba(53,143,165,.28), rgba(6,35,53,.66));
    box-shadow:0 14px 26px rgba(2,23,36,.20);
  }
  #sidebar .monbis-sidebar-promo__inner {
    background:linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.02));
  }
  #sidebar .monbis-sidebar-promo__text strong { color:#f1fbfc; }
  #sidebar .monbis-sidebar-promo__text span { color:#a9ced5; }
  #sidebarOverlay {
    background:rgba(15,23,42,.42) !important;
    backdrop-filter:none;
  }
  :root[data-monbis-theme="dark"] #mainNavbar {
    background:linear-gradient(135deg, rgba(17,24,39,.94), rgba(15,23,42,.90)) !important;
    border-bottom-color:rgba(71,85,105,.72) !important;
  }
  :root[data-monbis-theme="dark"] #sidebar {
    background:linear-gradient(180deg, #111827 0%, #0f172a 100%) !important;
  }
  :root[data-monbis-theme="dark"] #sidebar > .h-16 {
    background:linear-gradient(135deg, rgba(30,41,59,.90), rgba(15,23,42,.90));
  }
  :root[data-monbis-theme="dark"] #sidebar .accordion-content {
    background:rgba(15,23,42,.34);
  }
  /* Light navigation variant: clean white surface with tree-style submenus. */
  :root:not([data-monbis-theme="dark"]) #mainNavbar {
    background:rgba(255,255,255,.94) !important;
    border-bottom-color:#dbe5ee !important;
    box-shadow:0 6px 20px rgba(15,23,42,.055) !important;
    backdrop-filter:none;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar {
    background:linear-gradient(180deg, #ffffff 0%, #fbfdff 58%, #f4f8fb 100%) !important;
    border-right-color:#dbe5ee !important;
    box-shadow:12px 0 30px rgba(15,23,42,.075) !important;
    color:#334155 !important;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar > .h-16 {
    background:linear-gradient(180deg, #ffffff, #f8fbff);
    border-bottom-color:#e2eaf0 !important;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar > .h-16 .monbis-logo--wordmark {
    background:transparent;
    box-shadow:none;
    padding:0;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-btn,
  :root:not([data-monbis-theme="dark"]) #sidebar nav > a,
  :root:not([data-monbis-theme="dark"]) #sidebar .text-slate-700,
  :root:not([data-monbis-theme="dark"]) #sidebar .text-slate-600,
  :root:not([data-monbis-theme="dark"]) #sidebar .text-slate-800 {
    color:#334155 !important;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-btn:hover,
  :root:not([data-monbis-theme="dark"]) #sidebar nav > a:hover {
    color:#2563eb !important;
    background:#f4f8ff !important;
    border-color:#d5e3ff !important;
    box-shadow:0 8px 18px rgba(37,99,235,.07);
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-btn.is-open {
    background:#f8fbff !important;
    border-color:#e0eaf5 !important;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar nav > a.is-active,
  :root:not([data-monbis-theme="dark"]) #sidebar nav > a.bg-blue-50,
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-btn.is-active {
    color:#2563eb !important;
    background:linear-gradient(100deg, #eaf1ff, #f3f8ff) !important;
    border-color:#c9dcff !important;
    box-shadow:inset 0 1px 0 rgba(255,255,255,.9), 0 8px 18px rgba(37,99,235,.08);
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-btn > div > svg,
  :root:not([data-monbis-theme="dark"]) #sidebar nav > a > svg {
    color:#64748b !important;
    background:#f1f5f9;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-btn:hover > div > svg,
  :root:not([data-monbis-theme="dark"]) #sidebar nav > a:hover > svg,
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-btn.is-active > div > svg,
  :root:not([data-monbis-theme="dark"]) #sidebar nav > a.is-active > svg {
    color:#2563eb !important;
    background:#e5efff;
    box-shadow:inset 0 0 0 1px #cfe0ff;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-content {
    position:relative;
    margin:5px 8px 9px 18px;
    padding:5px 4px 6px 22px !important;
    border-left:1px solid #d7e3ed !important;
    border-radius:0 12px 12px 0;
    background:transparent !important;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-content a {
    position:relative;
    color:#475569 !important;
    background:transparent !important;
    transition:color .16s ease, background .16s ease, transform .16s ease;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-content a::before {
    content:"";
    position:absolute;
    left:-22px;
    top:50%;
    width:14px;
    border-top:1px solid #d7e3ed;
    transform:translateY(-50%);
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-content a:hover,
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-content a.is-active {
    color:#2563eb !important;
    background:#f1f6ff !important;
    transform:translateX(2px);
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-content a.is-active::before {
    border-color:#8eb3ff;
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo {
    border-color:#d7e5f0;
    background:linear-gradient(135deg, #eef5ff, #f8fbff);
    box-shadow:0 12px 24px rgba(15,23,42,.07);
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo__inner {
    background:linear-gradient(180deg, rgba(255,255,255,.72), rgba(255,255,255,.28));
  }
  :root:not([data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo__text strong { color:#334155; }
  :root:not([data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo__text span { color:#74899d; }
  :root:not([data-monbis-theme="dark"]) #sidebar nav > a.is-active::before,
  :root:not([data-monbis-theme="dark"]) #sidebar .accordion-btn.is-active::before {
    background:#75a7ff;
    box-shadow:0 0 0 4px rgba(117,167,255,.12);
  }
  @media (max-width:767px) {
    #sidebar {
      width:min(86vw, 320px) !important;
      max-width:calc(100vw - 18px);
      border-radius:0 22px 22px 0;
      box-shadow:24px 0 54px rgba(15,23,42,.24) !important;
    }
    #sidebar > .h-16 { padding-left:16px; padding-right:14px; }
    #sidebar nav { padding:12px 10px 18px !important; }
    #sidebar .accordion-btn,
    #sidebar nav > a { min-height:46px; }
    #mainNavbar { box-shadow:0 6px 20px rgba(15,23,42,.08) !important; }
  }

  /* Keep the desktop sidebar in the flex layout so content never sits underneath it. */
  @media (min-width:768px) {
    .monbis-app-shell { position:relative; }
    .monbis-app-shell > #sidebar {
      position:relative !important;
      inset:auto;
      flex:0 0 78px !important;
      width:78px !important;
      overflow:hidden !important;
      transition:flex-basis .24s cubic-bezier(.22,.61,.36,1), width .24s cubic-bezier(.22,.61,.36,1) !important;
      will-change:flex-basis, width;
    }
    .monbis-app-shell > #sidebar.is-expanded {
      flex-basis:278px !important;
      width:278px !important;
      overflow:hidden !important;
    }
    .monbis-app-shell > #sidebar + div {
      flex:1 1 auto;
      width:auto;
      margin-left:0;
      min-width:0;
      transition:none;
      will-change:auto;
    }
    .monbis-app-shell > #sidebar.is-expanded + div {
      flex:1 1 auto;
      width:auto;
      margin-left:0;
    }
    /* The older navbar stylesheet still contains hover rules; explicit state owns them now. */
    .monbis-app-shell > #sidebar:not(.is-expanded):hover,
    .monbis-app-shell > #sidebar:not(.is-expanded):focus-within {
      flex-basis:78px !important;
      width:78px !important;
      overflow:hidden !important;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) nav {
      overflow-y:auto;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-content {
      display:none !important;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-btn,
    .monbis-app-shell > #sidebar:not(.is-expanded) nav > a {
      justify-content:center;
      padding-left:10px !important;
      padding-right:10px !important;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-btn > div {
      width:100%;
      justify-content:center;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-btn > div > span,
    .monbis-app-shell > #sidebar:not(.is-expanded) nav > a > span,
    .monbis-app-shell > #sidebar:not(.is-expanded) .caret {
      width:0;
      margin-left:0 !important;
      overflow:hidden;
      opacity:0 !important;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-btn > .caret {
      display:none;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) .monbis-sidebar-promo {
      min-height:54px;
      border-radius:16px;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) .monbis-sidebar-promo__inner {
      min-height:54px;
      justify-content:center;
      padding:10px;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) .monbis-sidebar-promo__text {
      display:none;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) > .h-16 .monbis-logo--icon {
      display:block;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) > .h-16 .monbis-logo--wordmark {
      display:none;
    }
    .monbis-app-shell > #sidebar.is-expanded nav { min-height:0; overflow-y:auto; }
    .monbis-app-shell > #sidebar.is-expanded .accordion-btn,
    .monbis-app-shell > #sidebar.is-expanded nav > a {
      justify-content:flex-start;
      padding-left:12px !important;
      padding-right:12px !important;
    }
    .monbis-app-shell > #sidebar.is-expanded .accordion-btn > div {
      width:auto;
      justify-content:flex-start;
    }
    .monbis-app-shell > #sidebar.is-expanded .accordion-btn > div > span,
    .monbis-app-shell > #sidebar.is-expanded nav > a > span,
    .monbis-app-shell > #sidebar.is-expanded .caret {
      width:auto;
      margin-left:.75rem !important;
      overflow:visible;
      opacity:1 !important;
    }
    .monbis-app-shell > #sidebar.is-expanded .accordion-btn > .caret {
      display:block;
      margin-left:auto !important;
    }
    .monbis-app-shell > #sidebar.is-expanded > .h-16 .monbis-logo--icon { display:none; }
    .monbis-app-shell > #sidebar.is-expanded > .h-16 .monbis-logo--wordmark {
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
  }

  @media (max-width:767px) {
    body.sidebar-mobile-open { overflow:hidden; }
    .monbis-app-shell > #sidebar {
      transition:transform .2s ease !important;
      will-change:transform;
    }
  }

  /* shadcn/Vercel navigation surface: compact, quiet, and predictable. */
  #sidebar {
    --nav-surface:#ffffff;
    --nav-border:#e5e7eb;
    --nav-text:#111827;
    --nav-muted:#64748b;
    --nav-hover:#f8fafc;
    --nav-active:#eff6ff;
    --nav-active-text:#2563eb;
    --nav-subline:#e5e7eb;
    background:var(--nav-surface) !important;
    border-right:1px solid var(--nav-border) !important;
    box-shadow:none !important;
    color:var(--nav-text) !important;
  }
  :root[data-monbis-theme="dark"] #sidebar {
    --nav-surface:#0f172a;
    --nav-border:#1e293b;
    --nav-text:#e5e7eb;
    --nav-muted:#94a3b8;
    --nav-hover:#172033;
    --nav-active:#172554;
    --nav-active-text:#93c5fd;
    --nav-subline:#334155;
  }
  #sidebar > .h-16 {
    height:56px;
    min-height:56px;
    padding:0 12px;
    background:var(--nav-surface) !important;
    border-bottom:1px solid var(--nav-border) !important;
  }
  #sidebar > .h-16 .monbis-logo--icon {
    width:30px;
    height:30px;
    padding:3px;
    border-radius:8px;
    background:transparent;
    box-shadow:none;
  }
  #sidebar > .h-16 .monbis-logo--wordmark {
    width:148px;
    max-height:34px;
    padding:0;
    border-radius:0;
    background:transparent;
    box-shadow:none;
  }
  #sidebar nav {
    padding:8px !important;
    gap:2px;
    scrollbar-color:#cbd5e1 transparent;
  }
  :root[data-monbis-theme="dark"] #sidebar nav { scrollbar-color:#475569 transparent; }
  #sidebar .accordion-group { margin:1px 0; }
  #sidebar .accordion-btn,
  #sidebar nav > a {
    min-height:36px;
    padding:0 9px !important;
    border:1px solid transparent;
    border-radius:7px !important;
    color:var(--nav-text) !important;
    font-size:13px;
    font-weight:600 !important;
    letter-spacing:-.01em;
    box-shadow:none !important;
    transform:none !important;
    transition:background-color .15s ease, color .15s ease, border-color .15s ease;
  }
  #sidebar .accordion-btn:hover,
  #sidebar nav > a:hover {
    background:var(--nav-hover) !important;
    border-color:var(--nav-border) !important;
    color:var(--nav-text) !important;
  }
  #sidebar nav > a.is-active,
  #sidebar nav > a.bg-blue-50,
  #sidebar .accordion-btn.is-active,
  #sidebar .accordion-btn.is-open {
    background:var(--nav-active) !important;
    border-color:color-mix(in srgb, var(--nav-active-text) 20%, var(--nav-border)) !important;
    color:var(--nav-active-text) !important;
    box-shadow:none !important;
  }
  #sidebar .accordion-btn > div > svg {
    stroke-linecap:round;
    stroke-linejoin:round;
  }
  #sidebar .accordion-btn > div > svg,
  #sidebar nav > a > svg {
    width:20px !important;
    height:20px !important;
    padding:3px;
    border-radius:6px;
    color:var(--nav-muted) !important;
    background:transparent !important;
    box-shadow:none !important;
  }
  #sidebar .accordion-btn:hover > div > svg,
  #sidebar nav > a:hover > svg,
  #sidebar .accordion-btn.is-active > div > svg,
  #sidebar nav > a.is-active > svg,
  #sidebar nav > a.bg-blue-50 > svg {
    color:var(--nav-active-text) !important;
    background:transparent !important;
    box-shadow:none !important;
  }
  #sidebar nav > a.is-active::before,
  #sidebar nav > a.bg-blue-50::before,
  #sidebar .accordion-btn.is-active::before {
    left:0;
    top:8px;
    bottom:8px;
    width:2px;
    border-radius:999px;
    background:var(--nav-active-text);
    box-shadow:none;
  }
  #sidebar .accordion-content {
    margin:2px 0 5px 12px !important;
    padding:3px 0 3px 16px !important;
    border-left:1px solid var(--nav-subline) !important;
    border-radius:0;
    background:transparent !important;
  }
  #sidebar .accordion-content a {
    min-height:30px;
    padding:6px 8px !important;
    border-radius:6px !important;
    color:var(--nav-muted) !important;
    font-size:12px !important;
    font-weight:500;
  }
  #sidebar .accordion-content a:hover,
  #sidebar .accordion-content a.is-active {
    background:var(--nav-hover) !important;
    color:var(--nav-active-text) !important;
    transform:none;
  }
  #sidebar .monbis-sidebar-promo {
    min-height:64px;
    margin:8px;
    border:1px solid var(--nav-border);
    border-radius:10px;
    background:var(--nav-hover) !important;
    box-shadow:none !important;
  }
  #sidebar .monbis-sidebar-promo__inner {
    min-height:64px;
    padding:10px;
    background:transparent !important;
  }
  #sidebar .monbis-sidebar-promo__spark {
    width:28px;
    height:28px;
    border-radius:8px;
    box-shadow:none;
  }
  #sidebar .monbis-sidebar-promo__text strong { color:var(--nav-text) !important; font-size:11px; }
  #sidebar .monbis-sidebar-promo__text span { color:var(--nav-muted) !important; font-size:9px; }

  #mainNavbar {
    height:56px !important;
    min-height:56px !important;
    flex-basis:56px !important;
    padding:0 16px !important;
    background:var(--nav-surface, #ffffff) !important;
    border-bottom:1px solid var(--nav-border, #e5e7eb) !important;
    box-shadow:none !important;
    backdrop-filter:none !important;
  }
  :root[data-monbis-theme="dark"] #mainNavbar {
    --nav-surface:#0f172a;
    --nav-border:#1e293b;
  }
  #mainNavbar::after { display:none; }
  #mainNavbar #btnToggleSidebar,
  #mainNavbar .monbis-theme-toggle,
  #mainNavbar .dashboard-navbar-filter-toggle {
    width:32px;
    height:32px;
    min-height:32px;
    padding:0;
    border:1px solid #e5e7eb;
    border-radius:7px;
    background:transparent;
    color:#64748b;
    box-shadow:none;
    transform:none;
  }
  #mainNavbar #btnToggleSidebar { margin-right:8px !important; }
  #mainNavbar #btnToggleSidebar:hover,
  #mainNavbar .monbis-theme-toggle:hover,
  #mainNavbar .dashboard-navbar-filter-toggle:hover,
  #mainNavbar .dashboard-navbar-filter-toggle.is-active {
    background:#f8fafc;
    border-color:#d1d5db;
    color:#2563eb !important;
    box-shadow:none;
    transform:none;
  }
  :root[data-monbis-theme="dark"] #mainNavbar #btnToggleSidebar,
  :root[data-monbis-theme="dark"] #mainNavbar .monbis-theme-toggle,
  :root[data-monbis-theme="dark"] #mainNavbar .dashboard-navbar-filter-toggle {
    border-color:#334155;
    color:#94a3b8;
  }
  :root[data-monbis-theme="dark"] #mainNavbar #btnToggleSidebar:hover,
  :root[data-monbis-theme="dark"] #mainNavbar .monbis-theme-toggle:hover,
  :root[data-monbis-theme="dark"] #mainNavbar .dashboard-navbar-filter-toggle:hover,
  :root[data-monbis-theme="dark"] #mainNavbar .dashboard-navbar-filter-toggle.is-active {
    background:#172033;
    border-color:#475569;
    color:#93c5fd !important;
  }
  #mainNavbar .border-l { height:20px; border-color:#e5e7eb !important; }
  #navUserName { font-size:12px !important; font-weight:600 !important; }
  #navBranch { font-size:10px !important; color:#64748b !important; }
  :root[data-monbis-theme="dark"] #navBranch { color:#94a3b8 !important; }
  #btnProfileMenu {
    width:32px !important;
    height:32px !important;
    border-radius:8px !important;
    border:1px solid #e5e7eb !important;
    background:#f8fafc !important;
    color:#475569 !important;
    box-shadow:none !important;
  }
  #btnProfileMenu:hover { background:#f1f5f9 !important; box-shadow:none !important; transform:none; }
  :root[data-monbis-theme="dark"] #btnProfileMenu {
    border-color:#334155 !important;
    background:#172033 !important;
    color:#cbd5e1 !important;
  }
  :root[data-monbis-theme="dark"] #btnProfileMenu:hover { background:#1e293b !important; }
  #dropdownProfileMenu {
    width:192px !important;
    margin-top:6px;
    border:1px solid #e5e7eb !important;
    border-radius:8px !important;
    box-shadow:0 10px 30px rgba(15,23,42,.12) !important;
  }
  #dropdownProfileMenu a { padding:8px 10px !important; font-size:12px !important; }
  /* Final shell system: quiet surfaces, clear hierarchy, consistent controls. */
  :root:not([data-monbis-theme="dark"]) #sidebar,
  :root:not([data-monbis-theme="dark"]) #mainNavbar {
    --nav-surface:#ffffff;
    --nav-border:#e5e7eb;
    --nav-text:#111827;
    --nav-muted:#64748b;
    --nav-hover:#f8fafc;
    --nav-active:#f1f2f4;
    --nav-active-text:#111827;
    --nav-subline:#e5e7eb;
  }
  :root[data-monbis-theme="dark"] #sidebar,
  :root[data-monbis-theme="dark"] #mainNavbar {
    --nav-surface:#0f172a;
    --nav-border:#1e293b;
    --nav-text:#e2e8f0;
    --nav-muted:#94a3b8;
    --nav-hover:#172033;
    --nav-active:#1e293b;
    --nav-active-text:#e2e8f0;
    --nav-subline:#334155;
  }

  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar {
    min-height:0;
    overflow:hidden !important;
    color:var(--nav-text) !important;
    background:var(--nav-surface) !important;
    border-right:1px solid var(--nav-border) !important;
    box-shadow:none !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar > .h-16 {
    display:flex;
    height:56px;
    min-height:56px;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    padding:0 10px;
    background:var(--nav-surface) !important;
    border-bottom:1px solid var(--nav-border) !important;
  }
  .sidebar-brand { display:flex; flex:1 1 auto; align-items:center; min-width:0; overflow:hidden; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar > .h-16 .monbis-logo--icon {
    width:32px;
    height:32px;
    padding:2px;
    border:1px solid var(--nav-border);
    border-radius:8px;
    background:var(--nav-surface);
    box-shadow:none;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar > .h-16 .monbis-logo--wordmark {
    width:148px;
    max-height:34px;
    padding:0;
    border-radius:0;
    background:transparent;
    box-shadow:none;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav {
    min-height:0;
    padding:10px 8px 12px !important;
    gap:2px;
    scrollbar-width:thin;
    scrollbar-color:#cbd5e1 transparent;
  }
  .sidebar-section-label {
    padding:10px 12px 4px;
    color:var(--nav-muted);
    font-size:11px;
    font-weight:500;
    letter-spacing:.01em;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-group { margin:2px 0; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a {
    display:flex;
    min-height:44px;
    align-items:center;
    border:1px solid transparent;
    border-radius:8px !important;
    color:var(--nav-text) !important;
    font-size:13px;
    font-weight:500 !important;
    letter-spacing:0;
    box-shadow:none !important;
    transform:none !important;
    transition:background-color .15s ease, border-color .15s ease, color .15s ease;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a:hover {
    background:var(--nav-hover) !important;
    border-color:var(--nav-border) !important;
    color:var(--nav-text) !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a.is-active,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a.bg-blue-50,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn.is-active,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn.is-open {
    background:var(--nav-active) !important;
    border-color:transparent !important;
    color:var(--nav-active-text) !important;
    box-shadow:none !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn > div > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a > svg {
    width:20px !important;
    height:20px !important;
    padding:1px;
    border-radius:5px;
    color:var(--nav-muted) !important;
    background:transparent !important;
    box-shadow:none !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn:hover > div > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a:hover > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn.is-active > div > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a.is-active > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a.bg-blue-50 > svg {
    color:var(--nav-active-text) !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a.is-active::before,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a.bg-blue-50::before,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn.is-active::before {
    display:none;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-content {
    margin:3px 0 5px 12px !important;
    padding:3px 0 3px 12px !important;
    border-left:1px solid var(--nav-subline) !important;
    border-radius:0;
    background:transparent !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-content a {
    display:flex;
    min-height:36px;
    align-items:center;
    padding:5px 9px !important;
    border-radius:6px !important;
    color:var(--nav-muted) !important;
    font-size:12px !important;
    font-weight:500;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-content > a + a { margin-top:2px !important; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-content a:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-content a.is-active {
    background:var(--nav-hover) !important;
    color:var(--nav-active-text) !important;
    transform:none;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo {
    min-height:48px;
    margin:8px;
    border:1px solid transparent;
    border-radius:8px;
    background:transparent !important;
    box-shadow:none !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo:hover { background:var(--nav-hover) !important; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo__inner {
    min-height:48px;
    padding:6px 8px;
    background:transparent !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo__spark {
    width:30px;
    height:30px;
    border-radius:8px;
    box-shadow:none;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo__text strong { color:var(--nav-text) !important; font-size:11px; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo__text span { color:var(--nav-muted) !important; font-size:10px; }
  #sidebarOverlay { background:rgba(15,23,42,.42) !important; backdrop-filter:none; }

  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar {
    height:56px !important;
    min-height:56px !important;
    flex-basis:56px !important;
    padding:0 16px !important;
    background:var(--nav-surface, #fff) !important;
    border-bottom:1px solid var(--nav-border, #e5e7eb) !important;
    box-shadow:none !important;
    backdrop-filter:none !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar::after { display:none; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .sidebar-toggle-control,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .dashboard-navbar-filter-toggle {
    width:44px;
    height:44px;
    min-width:44px;
    min-height:44px;
    align-items:center;
    justify-content:center;
    padding:0;
    border:1px solid var(--nav-border);
    border-radius:8px;
    background:var(--nav-surface);
    color:var(--nav-muted);
    box-shadow:none;
    transform:none;
    transition:background-color .15s ease, border-color .15s ease, color .15s ease;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .sidebar-toggle-control,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .dashboard-navbar-filter-toggle { display:inline-flex; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .sidebar-mobile-toggle {
    width:44px;
    height:44px;
    min-width:44px;
    min-height:44px;
    align-items:center;
    justify-content:center;
    padding:0;
    border:1px solid var(--nav-border);
    border-radius:8px;
    background:var(--nav-surface);
    color:var(--nav-muted);
    box-shadow:none;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .sidebar-toggle-control:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .sidebar-mobile-toggle:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .dashboard-navbar-filter-toggle:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .dashboard-navbar-filter-toggle.is-active {
    background:var(--nav-hover);
    border-color:var(--nav-border);
    color:var(--nav-active-text) !important;
    box-shadow:none;
    transform:none;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .sidebar-toggle-control svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .sidebar-mobile-toggle svg { flex:0 0 auto; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .border-l { height:20px; border-color:var(--nav-border) !important; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar #navUserName { color:var(--nav-text) !important; font-size:12px !important; font-weight:600 !important; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar #navBranch { color:var(--nav-muted) !important; font-size:10px !important; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar #btnProfileMenu {
    width:44px !important;
    height:44px !important;
    border:1px solid var(--nav-border) !important;
    border-radius:8px !important;
    background:var(--nav-hover) !important;
    color:var(--nav-muted) !important;
    box-shadow:none !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar #btnProfileMenu:hover { background:var(--nav-active) !important; color:var(--nav-active-text) !important; }
  #dropdownProfileMenu {
    width:192px !important;
    margin-top:6px;
    border:1px solid var(--nav-border) !important;
    border-radius:8px !important;
    box-shadow:0 10px 28px rgba(15,23,42,.12) !important;
  }
  #dropdownProfileMenu a { min-height:44px; padding:9px 10px !important; font-size:12px !important; }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar button:focus-visible,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar a:focus-visible,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn:focus-visible,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .monbis-sidebar-promo:focus-visible {
    outline:2px solid #2563eb;
    outline-offset:2px;
  }

  @media (min-width:768px) {
    .monbis-app-shell > #sidebar { flex-basis:64px !important; width:64px !important; }
    .monbis-app-shell > #sidebar.is-expanded { flex-basis:240px !important; width:240px !important; }
    .monbis-app-shell > #sidebar:not(.is-expanded):hover,
    .monbis-app-shell > #sidebar:not(.is-expanded):focus-within { flex-basis:64px !important; width:64px !important; }
    .monbis-app-shell > #sidebar:not(.is-expanded) > .sidebar-header {
      height:104px;
      min-height:104px;
      flex-direction:column;
      justify-content:center;
      gap:8px;
      padding:8px;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded) .sidebar-brand { display:flex; flex:0 0 auto; justify-content:center; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .monbis-logo--icon { width:30px; height:30px; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .monbis-logo--wordmark { display:none; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .sidebar-toggle-control { margin:0; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-content { display:none !important; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-btn,
    .monbis-app-shell > #sidebar:not(.is-expanded) nav > a { justify-content:center; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-btn > div { width:100%; justify-content:center; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-btn > div > span,
    .monbis-app-shell > #sidebar:not(.is-expanded) nav > a > span,
    .monbis-app-shell > #sidebar:not(.is-expanded) .caret { width:0; margin-left:0 !important; overflow:hidden; opacity:0 !important; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .accordion-btn > .caret { display:none; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .monbis-sidebar-promo__text { display:none; }
    .monbis-app-shell > #sidebar:not(.is-expanded) .monbis-sidebar-promo__inner { justify-content:center; }
    .monbis-app-shell > #sidebar:not(.is-expanded) > .h-16 .monbis-logo--icon { display:block; }
    .monbis-app-shell > #sidebar:not(.is-expanded) > .h-16 .monbis-logo--wordmark { display:none; }
    .monbis-app-shell > #sidebar.is-expanded .accordion-btn,
    .monbis-app-shell > #sidebar.is-expanded nav > a { justify-content:flex-start; }
    .monbis-app-shell > #sidebar.is-expanded .accordion-btn > div { width:auto; justify-content:flex-start; }
    .monbis-app-shell > #sidebar.is-expanded .accordion-btn > div > span,
    .monbis-app-shell > #sidebar.is-expanded nav > a > span,
    .monbis-app-shell > #sidebar.is-expanded .caret { width:auto; margin-left:.75rem !important; overflow:visible; opacity:1 !important; }
    .monbis-app-shell > #sidebar.is-expanded .accordion-btn > .caret { display:block; margin-left:auto !important; }
    .monbis-app-shell > #sidebar.is-expanded > .sidebar-header {
      height:56px;
      min-height:56px;
      flex-direction:row;
      justify-content:space-between;
      gap:8px;
      padding-inline:10px;
    }
    .monbis-app-shell > #sidebar.is-expanded .sidebar-brand { display:flex; }
    .monbis-app-shell > #sidebar.is-expanded > .h-16 .monbis-logo--icon { display:none; }
    .monbis-app-shell > #sidebar.is-expanded > .h-16 .monbis-logo--wordmark { display:block; width:148px; max-height:34px; }
  }
  @media (max-width:767px) {
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar { padding-inline:10px !important; }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar { width:min(82vw, 264px) !important; border-radius:0 12px 12px 0; }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar > .sidebar-header {
      height:56px;
      min-height:56px;
      flex-direction:row;
      justify-content:space-between;
      padding-inline:12px;
    }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .sidebar-brand { display:flex; }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .sidebar-section-label { display:block; }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar > .h-16 .monbis-logo--icon { display:none; }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar > .h-16 .monbis-logo--wordmark { display:block; width:128px; }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .sidebar-mobile-toggle { display:inline-flex; }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > :not([hidden]) ~ :not([hidden]) { margin-top:2px !important; }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-group { margin:1px 0; }
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn,
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a {
      box-sizing:border-box;
      min-height:44px;
      padding-block:0 !important;
    }
  }
  @media (min-width:768px) {
    .monbis-app-shell > #sidebar:not(.is-expanded) .sidebar-section-label { display:none; }
  }
  @media (prefers-reduced-motion:reduce) {
    :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar *, #mainNavbar * { transition-duration:0.01ms !important; animation-duration:0.01ms !important; }
  }
  /* Icon controls use color and position for state, not a filled tile. */
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .dashboard-navbar-filter-toggle,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .sidebar-mobile-toggle,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar #btnProfileMenu,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .sidebar-toggle-control,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) .dashboard-navbar-filter__close,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) .dashboard-navbar-filter__submit,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) .npl-navbar-filter-panel__close {
    border-color:transparent !important;
    border-radius:0 !important;
    background:transparent !important;
    color:var(--nav-muted) !important;
    box-shadow:none !important;
    transform:none !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .dashboard-navbar-filter-toggle:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .dashboard-navbar-filter-toggle.is-active,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar .sidebar-mobile-toggle:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #mainNavbar #btnProfileMenu:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .sidebar-toggle-control:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) .dashboard-navbar-filter__close:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) .dashboard-navbar-filter__submit:hover,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) .npl-navbar-filter-panel__close:hover {
    border-color:transparent !important;
    background:transparent !important;
    color:var(--monbis-event-accent, #2563eb) !important;
    box-shadow:none !important;
    transform:none !important;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn > div > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a > svg {
    padding:0 !important;
    border-radius:0 !important;
    background:transparent !important;
    box-shadow:none !important;
    transition:color .15s ease;
  }
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn:hover > div > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a:hover > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar .accordion-btn.is-active > div > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a.is-active > svg,
  :root:is(:not([data-monbis-theme="dark"]), [data-monbis-theme="dark"]) #sidebar nav > a.bg-blue-50 > svg {
    background:transparent !important;
    box-shadow:none !important;
    color:var(--monbis-event-accent, #2563eb) !important;
  }
</style>
<script src="./assets/js/sidebar.js?v=6" defer></script>

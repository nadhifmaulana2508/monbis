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
  #sidebar nav { overscroll-behavior:contain; }
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

  /* Move the desktop content column with the sidebar so nothing is hidden underneath it. */
  @media (min-width:768px) {
    .monbis-app-shell { position:relative; }
    .monbis-app-shell > #sidebar {
      position:absolute !important;
      inset:0 auto 0 0;
      width:78px !important;
      overflow:visible !important;
      transition:width .18s cubic-bezier(.22,.61,.36,1) !important;
      will-change:width;
    }
    .monbis-app-shell > #sidebar.is-expanded {
      width:278px !important;
    }
    .monbis-app-shell > #sidebar + div {
      flex:0 0 calc(100% - 78px);
      width:calc(100% - 78px);
      margin-left:78px;
      transition:width .18s cubic-bezier(.22,.61,.36,1), margin-left .18s cubic-bezier(.22,.61,.36,1);
      will-change:width, margin-left;
    }
    .monbis-app-shell > #sidebar.is-expanded + div {
      flex-basis:calc(100% - 278px);
      width:calc(100% - 278px);
      margin-left:278px;
    }
    /* The older navbar stylesheet still contains hover rules; the state class owns them now. */
    .monbis-app-shell > #sidebar:not(.is-expanded):hover,
    .monbis-app-shell > #sidebar:not(.is-expanded):focus-within {
      width:78px !important;
    }
    .monbis-app-shell > #sidebar:not(.is-expanded):hover + div,
    .monbis-app-shell > #sidebar:not(.is-expanded):focus-within + div {
      flex-basis:calc(100% - 78px);
      width:calc(100% - 78px);
      margin-left:78px;
    }
  }

  @media (max-width:767px) {
    body.sidebar-mobile-open { overflow:hidden; }
    .monbis-app-shell > #sidebar {
      transition:transform .2s ease !important;
      will-change:transform;
    }
  }
</style>
<script src="./assets/js/sidebar.js?v=1" defer></script>

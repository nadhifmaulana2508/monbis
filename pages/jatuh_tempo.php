<style>
  /* Custom Scrollbar */
  .custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 #f8fafc; }
  .custom-scrollbar::-webkit-scrollbar { height: 4px; width: 4px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 999px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

  /* Sembunyikan Scrollbar Filter di Mobile */
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  /* Animasi Modal */
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* ========================================================
     CSS MAGIC STICKY TABLE (Responsive Mobile & Desktop)
     ======================================================== */
  #tabelJT thead th { position: sticky; box-shadow: inset 0 -1px 0 #cbd5e1; }

  /* DEFAULT (MOBILE VIEW) */
  #tabelJT thead tr:nth-child(1) th { top: 0; z-index: 40; height: 44px; background-color: #f1f5f9; }
  #tabelJT thead tr:nth-child(2) th { top: 44px; z-index: 38; height: 40px; background-color: #dbeafe !important; border-bottom: 2px solid #bfdbfe; box-shadow: inset 0 -1px 0 #93c5fd; }

  /* DESKTOP VIEW (MD) */
  @media (min-width: 768px) {
      #tabelJT thead tr:nth-child(1) th { height: 52px; }
      #tabelJT thead tr:nth-child(2) th { top: 52px; height: 46px; }
      .sticky-left-2 { left: 80px; } /* Kolom Kode muncul di PC */
  }

  /* Freeze Kolom Kiri Rekap Utama */
  .sticky-left-1 { position: sticky; left: 0; z-index: 20; background: white; box-shadow: inset -1px 0 0 #e2e8f0; }
  .sticky-left-2 { position: sticky; left: 0; z-index: 20; background: white; box-shadow: inset -1px 0 0 #e2e8f0; }

  #tabelJT thead tr:nth-child(1) th.sticky-left-1 { z-index: 50; background-color: #e2e8f0; box-shadow: inset -1px -1px 0 #cbd5e1; border-top-left-radius: 8px;}
  #tabelJT thead tr:nth-child(1) th.sticky-left-2 { z-index: 49; background-color: #e2e8f0; box-shadow: inset -1px -1px 0 #cbd5e1; }
  #tabelJT thead tr:nth-child(2) th.sticky-left-1 { z-index: 48; background-color: #bfdbfe !important; box-shadow: inset -1px -2px 0 #93c5fd; }
  #tabelJT thead tr:nth-child(2) th.sticky-left-2 { z-index: 47; background-color: #bfdbfe !important; box-shadow: inset -1px -2px 0 #93c5fd; }

  #bodyJT tr:hover td { background-color: #eff6ff !important; cursor: pointer; }
  #bodyJT tr:hover td.sticky-left-1, #bodyJT tr:hover td.sticky-left-2 { background-color: #eff6ff !important; }

  /* TABEL MODAL DETAIL */
  #tableDetailJT thead th { position: sticky; box-shadow: inset 0 -1px 0 #cbd5e1; }

  /* DEFAULT (MOBILE VIEW) MODAL */
  #tableDetailJT thead tr:nth-child(1) th { top: 0; z-index: 40; height: 42px; background-color: #f1f5f9; }
  #tableDetailJT thead tr:nth-child(2) th { top: 42px; z-index: 39; height: 40px; background-color: #dbeafe !important; border-bottom: 2px solid #bfdbfe; box-shadow: inset 0 -1px 0 #93c5fd; }
  .mod-sticky-2 { left: 90px; }

  /* DESKTOP VIEW (MD) MODAL */
  @media (min-width: 768px) {
      #tableDetailJT thead tr:nth-child(1) th { height: 46px; }
      #tableDetailJT thead tr:nth-child(2) th { top: 46px; height: 44px; }
      .mod-sticky-2 { left: 120px; }
  }

  .mod-sticky-1 { position: sticky; left: 0; z-index: 20; background: white; box-shadow: inset -1px 0 0 #e2e8f0; }
  .mod-sticky-2 { position: sticky; z-index: 20; background: white; box-shadow: inset -1px 0 0 #e2e8f0; }

  #tableDetailJT thead tr:nth-child(1) th.mod-sticky-1 { z-index: 50; background-color: #e2e8f0; box-shadow: inset -1px -1px 0 #cbd5e1; border-top-left-radius: 8px;}
  #tableDetailJT thead tr:nth-child(1) th.mod-sticky-2 { z-index: 49; background-color: #e2e8f0; box-shadow: inset -1px -1px 0 #cbd5e1; }
  #tableDetailJT thead tr:nth-child(2) th.mod-sticky-1 { z-index: 48; background-color: #bfdbfe !important; box-shadow: inset -1px -1px 0 #93c5fd; }
  #tableDetailJT thead tr:nth-child(2) th.mod-sticky-2 { z-index: 47; background-color: #bfdbfe !important; box-shadow: inset -1px -1px 0 #93c5fd; }

  /* Di desktop identitas tetap dipisah; di mobile digabung agar area freeze lebih hemat. */
  .jt-mobile-identity { display:none; }
  #tableDetailJT thead tr:first-child th.jt-mobile-identity,
  #tableDetailJT thead tr:nth-child(2) th.jt-mobile-identity { display:none; }

  #bodyModalJT tr:hover td { background-color: #f8fafc !important; }
  #bodyModalJT tr:hover td.mod-sticky-1, #bodyModalJT tr:hover td.mod-sticky-2 { background-color: #f8fafc !important; }
  #bodyModalJT tr:hover td.jt-mobile-identity { background-color: #f8fafc !important; }

  /* Form Inputs */
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; background:#fff; outline:none; transition: border 0.2s;}
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  @media (min-width: 768px) { .inp { border-radius:8px; padding:0 12px; } }

  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap;}
  @media (min-width: 768px) { .lbl { font-size:11px; } }

  .field { display:flex; flex-direction:column; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }

  input[type="date"]::-webkit-inner-spin-button, input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
  input[type="date"] { -moz-appearance: textfield; }
  .badge-clean { display: inline-flex; align-items: center; justify-content: center; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase; border: 1px solid; letter-spacing: 0.5px;}
  @media (min-width: 768px) { .badge-clean { padding: 4px 8px; border-radius: 6px; font-size: 10px; } }

  /* JT UI: selaraskan dengan report card/menu lain */
  #jtPage,
  #jtPage * { font-family:'Roboto',Arial,system-ui,sans-serif; }
  #jtPage { min-height:0; padding-top:10px; padding-bottom:12px; }
  #jtHeaderCard {
      position:relative;
      padding:10px 12px;
      background:#fff;
      border:1px solid #e2e8f0;
      border-radius:12px;
      box-shadow:0 1px 3px rgba(15,23,42,.05);
  }
  #jtHeaderCard h1 { color:#172033; letter-spacing:-.015em; }
  #jtHeaderCard p { color:#64748b; font-style:normal; }
  .jt-title-copy { min-width:0; }
  .jt-title-subtitle { margin:2px 0 0; color:#64748b; font-size:9px; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .jt-info-btn {
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
      font-size:11px;
      font-weight:900;
      line-height:1;
      cursor:pointer;
      transition:.16s ease;
  }
  .jt-info-btn:hover,
  .jt-info-btn[aria-expanded="true"] { background:#2563eb; color:#fff; border-color:#2563eb; }
  .jt-info-popover {
      position:absolute;
      top:calc(100% + 6px);
      left:12px;
      z-index:130;
      width:min(380px,calc(100vw - 32px));
      padding:12px;
      border:1px solid #dbe3ee;
      border-radius:12px;
      background:#fff;
      box-shadow:0 18px 40px rgba(15,23,42,.18);
  }
  .jt-info-popover.hidden { display:none !important; }
  .jt-info-popover__head { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
  .jt-info-popover__title { color:#1e3a5f; font-size:11px; font-weight:900; }
  .jt-info-popover__close { border:0; background:transparent; color:#94a3b8; font-size:18px; line-height:1; cursor:pointer; }
  .jt-info-popover__text { margin:4px 0 0; color:#64748b; font-size:9px; line-height:1.45; }
  .jt-info-popover__list { display:grid; gap:5px; margin:9px 0 0; padding:0; list-style:none; color:#475569; font-size:9px; line-height:1.35; }
  .jt-info-popover__list li { padding:6px 7px; border-radius:7px; background:#f8fafc; }
  .jt-info-popover__list b { color:#1e3a5f; }
  #jtHeaderCard .bg-blue-600 { box-shadow:none; }
  #jtHeaderCard .btn-icon,
  #jtModalHeader .btn-icon { transition:transform .16s ease, box-shadow .16s ease, background-color .16s ease; }
  #jtHeaderCard .btn-icon:hover,
  #jtModalHeader .btn-icon:hover { transform:translateY(-1px); box-shadow:0 6px 14px rgba(15,23,42,.12); }
  .jt-breakdown-toggle {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:34px;
      height:34px;
      padding:0 9px;
      border:1px solid #bfdbfe;
      border-radius:8px;
      background:#eff6ff;
      color:#1d4ed8;
      cursor:pointer;
      font-size:9px;
      font-weight:900;
      letter-spacing:.03em;
      transition:.16s ease;
  }
  .jt-breakdown-toggle svg { width:14px; height:14px; flex:0 0 auto; }
  .jt-breakdown-toggle:hover { background:#dbeafe; border-color:#93c5fd; transform:translateY(-1px); }
  .jt-breakdown-toggle.hidden { display:none !important; }
  .jt-kolek-wrap { position:relative; }
  .jt-kolek-toggle {
      width:34px;
      height:34px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      padding:0;
      border:1px solid #bbf7d0;
      border-radius:8px;
      background:#f0fdf4;
      color:#047857;
      cursor:pointer;
      transition:.16s ease;
  }
  .jt-kolek-toggle svg { width:15px; height:15px; }
  .jt-kolek-toggle:hover { background:#dcfce7; border-color:#86efac; transform:translateY(-1px); }
  .jt-kolek-menu {
      position:absolute;
      top:calc(100% + 7px);
      right:0;
      z-index:120;
      width:170px;
      padding:9px;
      border:1px solid #dbe3ee;
      border-radius:10px;
      background:#fff;
      box-shadow:0 12px 28px rgba(15,23,42,.16);
  }
  .jt-kolek-menu.hidden { display:none !important; }
  .jt-kolek-menu__title { margin-bottom:6px; color:#1e3a5f; font-size:9px; font-weight:900; letter-spacing:.05em; text-transform:uppercase; }
  .jt-kolek-menu__option { display:flex; align-items:center; gap:7px; min-height:28px; padding:4px 5px; border-radius:6px; color:#334155; font-size:11px; font-weight:700; cursor:pointer; }
  .jt-kolek-menu__option:hover { background:#f8fafc; }
  .jt-kolek-menu__option input { width:14px; height:14px; margin:0; accent-color:#059669; }
  .jt-kolek-menu__option input:disabled { opacity:.75; cursor:not-allowed; }
  #jtPage > .flex-1 { border-color:#e2e8f0; border-radius:12px; }
  #tabelJT { min-width:960px; font-size:11px; font-variant-numeric:tabular-nums; }
  #tabelJT th { color:#1e3a5f !important; font-weight:900 !important; letter-spacing:.025em; background:#f4f7fb !important; border-color:#d7dee8 !important; }
  #tabelJT thead tr:first-child th { background:#eaf4ff !important; }
  #tabelJT thead tr:last-child th { background:#eff6ff !important; }
  #tabelJT tbody td { border-color:#edf2f7 !important; background-clip:padding-box; }
  #tabelJT tbody tr:nth-child(even) td { background-color:#fbfdff; }
  #tabelJT tbody tr:hover td { background-color:#f0f7ff !important; }
  #tabelJT tbody tr:hover td.sticky-left-1,
  #tabelJT tbody tr:hover td.sticky-left-2 { background-color:#f0f7ff !important; }
  #modalDetailJT > .relative { border:1px solid #dbe3ee; }
  #jtModalHeader { background:#fff; border-color:#e2e8f0; }
  #modalTitleJT::before { content:''; display:inline-block; width:6px; height:20px; border-radius:999px; background:#2563eb; flex:0 0 auto; }
  #modalTitleJT > span { display:none !important; }
  #modalSubTitleJT { color:#64748b; font-style:normal; }
  #tableDetailJT { border-color:#dbe3ee; }
  #tableDetailJT thead th { color:#1e3a5f; font-weight:900; letter-spacing:.025em; }
  #bodyModalJT tr:nth-child(even) td { background-color:#fbfdff; }
  #bodyModalJT tr:hover td { background-color:#f0f7ff !important; }
  @media (max-width:767px) {
      #jtPage { height:calc(100vh - 60px); padding:8px; }
      #jtHeaderCard { padding:9px 10px; border-radius:10px; gap:9px; }
      #jtHeaderCard h1 { font-size:15px; }
      #jtHeaderCard p { font-size:9px; margin-left:0; }
      .jt-title-subtitle { max-width:220px; font-size:8px; }
      #jtHeaderCard .bg-blue-600 { padding:6px; border-radius:8px; }
      #jtModalHeader { padding:10px; }
      #jtModalHeader > div:first-child { min-width:0; width:100%; }
      #jtModalHeader > div:last-child { width:100%; margin-top:2px; }
      #modalTitleJT { font-size:12px; }
      #modalTitleJT::before { height:16px; width:5px; }
      #modalSubTitleJT { font-size:9px; margin-left:0; }
  }

  /* Layout mengikuti Potensi NPL: padat, terang, dan fokus ke tabel */
  .jt-page {
      display:flex;
      flex-direction:column;
      width:100%;
      height:calc(100dvh - 64px);
      min-height:430px;
      padding:8px;
      gap:7px;
      overflow:hidden;
      background:#f8fafc;
  }
  .jt-header-card {
      display:flex;
      flex:none;
      align-items:center;
      justify-content:space-between;
      gap:12px;
      margin:0 !important;
      padding:9px 11px !important;
      border:1px solid #dbe3ee;
      border-radius:12px;
      background:#fff;
      box-shadow:0 1px 3px rgba(15,23,42,.05);
  }
  .jt-table-wrapper {
      flex:1;
      min-height:0;
      border:1px solid #e2e8f0;
      border-radius:9px;
      background:#fff;
      box-shadow:0 1px 2px rgba(15,23,42,.04);
      -webkit-overflow-scrolling:touch;
  }
  #tabelJT {
      width:100%;
      min-width:0;
      table-layout:fixed;
      border-collapse:separate;
      border-spacing:0;
  }
  #tabelJT th,
  #tabelJT td {
      height:38px;
      padding:6px 8px;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
      vertical-align:middle;
      font-size:11px;
      font-variant-numeric:tabular-nums;
  }
  #tabelJT thead th {
      background:#f1f5f9 !important;
      color:#475569 !important;
      font-size:9px !important;
      font-weight:900 !important;
      letter-spacing:.035em;
      text-transform:uppercase;
      border-right:1px solid #dbe3ee !important;
      border-bottom:1px solid #cbd5e1 !important;
  }
  #tabelJT thead tr:first-child th { top:0; height:30px; }
  #tabelJT thead tr:nth-child(2) th { top:30px; height:30px; background:#eff6ff !important; }
  #tabelJT thead th.jt-head-neutral { background:#f1f5f9 !important; color:#475569 !important; }
  #tabelJT thead th.jt-head-blue { background:#eff6ff !important; color:#1e40af !important; }
  #tabelJT thead th.jt-head-green { background:#ecfdf5 !important; color:#047857 !important; }
  #tabelJT thead th.jt-head-sky { background:#f0f9ff !important; color:#0369a1 !important; }
  #tabelJT thead th.jt-head-orange { background:#fff7ed !important; color:#c2410c !important; }
  #tabelJT tbody td { border-right:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; color:#334155; }
  #tabelJT tbody tr:nth-child(even) td { background:#fbfdff; }
  #tabelJT tbody tr:hover td,
  #tabelJT tbody tr:hover td.sticky-left-1,
  #tabelJT tbody tr:hover td.sticky-left-2 { background:#eff6ff !important; }
  #tabelJT td.jt-clickable { cursor:pointer; }
  #tabelJT td.jt-clickable:hover { filter:brightness(.97); text-decoration:underline; text-underline-offset:2px; }
  #tabelJT tbody td.sticky-left-1,
  #tabelJT tbody td.sticky-left-2 { background:#fff; }
  #tabelJT .jt-grouped-name,
  #tabelJT .jt-office-name { font-size:11px; font-weight:800; }
  #rowTotalJTAtas th { position:sticky; top:60px; z-index:42; height:38px; background:#eff6ff !important; color:#1e40af !important; border-bottom:1px solid #bfdbfe !important; }
  #rowTotalJTAtas th.sticky-left-1,
  #rowTotalJTAtas th.sticky-left-2 { z-index:59; background:#eff6ff !important; }
  #rowTotalJTAtas.jt-total-clickable { cursor:pointer; }
  #rowTotalJTAtas.jt-total-clickable:hover th { filter:brightness(.98); }
  #rowTotalJTAtas th.jt-total-detail-clickable { cursor:pointer; }
  #rowTotalJTAtas th.jt-total-detail-clickable:hover { text-decoration:underline; text-underline-offset:2px; filter:brightness(.96); }
  #tabelJT thead tr:first-child th.sticky-left-1,
  #tabelJT thead tr:first-child th.sticky-left-2 { z-index:70; background:#f1f5f9 !important; }
  #tabelJT thead tr:last-child th.sticky-left-1,
  #tabelJT thead tr:last-child th.sticky-left-2 { z-index:69; background:#eff6ff !important; }
  #tabelJT .sticky-left-1 { width:60px; min-width:60px; max-width:60px; }
  #tabelJT .sticky-left-2 { width:190px; min-width:190px; max-width:190px; }
  #tabelJT .jt-code-col { width:80px; min-width:80px; max-width:80px; }
  #tabelJT .jt-grouped-name,
  #tabelJT .jt-office-name { width:230px; min-width:230px; max-width:230px; }
  #modalDetailJT { padding:12px; background:rgba(15,23,42,.68); backdrop-filter:blur(7px); }
  #modalDetailJT > .relative { width:min(1760px,calc(100vw - 24px)); height:min(94dvh,920px); max-width:none; border-radius:16px; }
  #jtModalHeader > div:last-child { max-width:100%; }
  #jtModalContent {
      background:#fff;
      padding:0 !important;
      isolation:isolate;
      overscroll-behavior:contain;
      -webkit-overflow-scrolling:touch;
  }
  #tableDetailJT { width:max-content; min-width:1560px; table-layout:fixed; }
  #tableDetailJT th { height:36px !important; padding:5px 7px !important; background:#f8fafc !important; color:#64748b !important; font-size:8px !important; }
  #tableDetailJT td { height:36px; padding:5px 7px; font-size:9px; }
  #tableDetailJT thead { position:relative; z-index:80; }
  #tableDetailJT thead tr:first-child th { top:0 !important; z-index:80 !important; }
  #tableDetailJT thead tr:nth-child(2) th { top:36px !important; z-index:79 !important; }
  #tableDetailJT thead tr:first-child th.mod-sticky-1,
  #tableDetailJT thead tr:first-child th.mod-sticky-2,
  #tableDetailJT thead tr:first-child th.jt-mobile-identity { z-index:100 !important; }
  #tableDetailJT thead tr:nth-child(2) th.mod-sticky-1,
  #tableDetailJT thead tr:nth-child(2) th.mod-sticky-2,
  #tableDetailJT thead tr:nth-child(2) th.jt-mobile-identity { z-index:99 !important; }
  #tableDetailJT tbody td { position:relative; z-index:1; background-color:#fff; }
  #tableDetailJT tbody td.mod-sticky-1,
  #tableDetailJT tbody td.mod-sticky-2,
  #tableDetailJT tbody td.jt-mobile-identity {
      position:sticky;
      z-index:20;
  }
  #rowTotalDetailAtas th { top:36px !important; height:36px; background:#eff6ff !important; color:#1e40af !important; }
  @media (max-width:1279px) {
      .jt-header-card { align-items:stretch; flex-direction:column; }
      #jtHeaderCard > div:last-child { width:100%; justify-content:flex-end; }
  }
  @media (max-width:1023px) {
      #jtScroller { overflow:auto; }
      #tabelJT { width:1180px; min-width:1180px; }
      #tableDetailJT { min-width:1560px; }
  }
  @media (max-width:767px) {
      .jt-page { height:calc(100dvh - 54px); min-height:0; padding:4px; gap:4px; }
      .jt-header-card { gap:7px; padding:7px 8px !important; border-radius:9px; }
      #jtHeaderCard > div:first-child { width:100%; }
      #jtHeaderCard > div:last-child { width:auto; align-self:flex-end; margin-top:-38px; }
      #jtHeaderCard h1 { font-size:13px; }
      #jtHeaderCard p { max-width:185px; font-size:7px; }
      #jtHeaderCard .bg-blue-600 { width:31px; height:31px; padding:6px; border-radius:8px; }
      #jtHeaderCard .btn-icon { width:34px; height:32px; border-radius:7px; }
      .jt-info-btn { width:18px; min-width:18px; height:18px; font-size:10px; }
      .jt-info-popover { left:8px; width:calc(100vw - 32px); }
      .jt-breakdown-toggle { min-height:32px; padding:0 7px; font-size:8px; }
      .jt-breakdown-toggle span { display:none; }
      #tabelJT { width:1100px; min-width:1100px; }
      #tabelJT th,#tabelJT td { height:34px; padding:5px 6px; font-size:9px; }
      #tabelJT thead th { font-size:7px !important; }
      #tabelJT .sticky-left-1 { display:none !important; }
      #tabelJT .sticky-left-1.jt-grouped-name,
      #tabelJT .sticky-left-1.jt-office-name { display:table-cell !important; left:0; width:160px; min-width:160px; max-width:160px; white-space:normal; line-height:1.1; }
      #tabelJT .sticky-left-2,
      #tabelJT .jt-grouped-name,
      #tabelJT .jt-office-name { left:0; width:160px; min-width:160px; max-width:160px; white-space:normal; line-height:1.1; }
      #tabelJT .sticky-left-2,
      #tabelJT .jt-grouped-name,
      #tabelJT .jt-office-name { left:0; }
      #tabelJT .jt-grouped-name,
      #tabelJT .jt-office-name { font-size:9px; }
      #tabelJT thead tr:first-child th { height:25px; }
      #tabelJT thead tr:nth-child(2) th { top:25px; height:25px; }
      #rowTotalJTAtas th { top:50px; }
      #modalDetailJT { padding:0; align-items:flex-end; }
      #modalDetailJT > .relative { width:100%; height:96dvh; max-height:96dvh; border-radius:16px 16px 0 0; }
      #jtModalHeader { padding:8px; }
      #jtModalHeader > div:first-child { width:100%; min-width:0; }
      #jtModalHeader > div:last-child { width:100%; margin-top:0; display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr) 34px 34px; gap:5px; overflow:visible; }
      #jtModalHeader > div:last-child select { width:100%; min-width:0; }
      #jtModalContent { padding:0; }
      #tableDetailJT { min-width:0; }
      #tableDetailJT .jt-desktop-identity { display:none !important; }
      #tableDetailJT .jt-mobile-identity {
          display:table-cell !important;
          position:sticky;
          left:0;
          z-index:20;
          width:145px !important;
          min-width:145px !important;
          max-width:145px !important;
          background:#fff;
          box-shadow:inset -1px 0 0 #e2e8f0;
      }
      #tableDetailJT thead tr:first-child th.jt-mobile-identity {
          z-index:50;
          background:#e2e8f0 !important;
          box-shadow:inset -1px -1px 0 #cbd5e1;
      }
      #tableDetailJT thead tr:nth-child(2) th.jt-mobile-identity {
          z-index:48;
          background:#bfdbfe !important;
          box-shadow:inset -1px -1px 0 #93c5fd;
      }
      #tableDetailJT .jt-mobile-identity .jt-mobile-account,
      #tableDetailJT .jt-mobile-identity .jt-mobile-name {
          display:block;
          overflow:hidden;
          text-overflow:ellipsis;
          white-space:nowrap;
          line-height:1.15;
      }
      #tableDetailJT .jt-mobile-identity .jt-mobile-account { color:#64748b; font:700 8px/1.15 ui-monospace,SFMono-Regular,Menlo,monospace; }
      #tableDetailJT .jt-mobile-identity .jt-mobile-name { margin-top:2px; color:#334155; font-size:9px; font-weight:800; }
  }
</style>

<script>
    window.currentUser = { kode_kantor: (typeof USER_KODE_KANTOR !== 'undefined') ? USER_KODE_KANTOR : '000' };
</script>

<div id="jtPage" class="jt-page max-w-[1920px] mx-auto px-2 md:px-4 py-3 md:py-6 h-[calc(100vh-80px)] flex flex-col bg-slate-50 font-sans text-slate-800 overflow-hidden">

  <div id="jtHeaderCard" class="jt-header-card flex-none mb-3 md:mb-4 flex flex-col xl:flex-row justify-between xl:items-center gap-3 md:gap-4 w-full">

      <div class="jt-title-copy flex flex-col gap-1 shrink-0">
          <h1 class="text-lg md:text-2xl font-bold text-slate-800 flex items-center gap-2 mb-0.5">
              <span class="p-1.5 md:p-2 bg-blue-600 rounded-lg text-white shadow-sm">
                  <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
              </span>
              <span class="jt-title-text">Jatuh Tempo Kredit</span>
              <button type="button" id="jtInfoButton" class="jt-info-btn" onclick="toggleJTInfo(event)" title="Informasi Jatuh Tempo Kredit" aria-label="Buka informasi Jatuh Tempo Kredit" aria-expanded="false">i</button>
          </h1>
          <p class="jt-title-subtitle">Rekap kredit jatuh tempo, refinancing, dan potensi penyelesaian berdasarkan periode terpilih.</p>
      </div>

      <div class="flex items-center gap-2 shrink-0 xl:ml-auto">
          <select id="jt_breakdown_by" class="hidden" aria-hidden="true">
              <option value="KANKAS" selected>Per Kankas</option>
              <option value="AO">Per AO Kredit</option>
          </select>
          <button type="button" id="jtBreakdownToggle" onclick="toggleJTBreakdown()" class="jt-breakdown-toggle hidden" title="Ganti tampilan cabang" aria-label="Ganti tampilan cabang">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7h11"></path><path d="m14 3 4 4-4 4"></path><path d="M17 17H6"></path><path d="m10 13-4 4 4 4"></path></svg>
          </button>
          <div class="jt-kolek-wrap">
              <button type="button" id="jtKolekToggle" class="jt-kolek-toggle" onclick="toggleJTKolekMenu(event)" title="Filter kolektibilitas" aria-label="Filter kolektibilitas" aria-expanded="false">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 6 1.5 1.5L8 5"></path><path d="M11 6h9"></path><path d="m4 12 1.5 1.5L8 11"></path><path d="M11 12h9"></path><path d="m4 18 1.5 1.5L8 17"></path><path d="M11 18h9"></path></svg>
              </button>
              <div id="jtKolekMenu" class="jt-kolek-menu hidden" role="group" aria-label="Filter kolektibilitas">
                  <div class="jt-kolek-menu__title">Kolektibilitas</div>
                  <label class="jt-kolek-menu__option"><input type="checkbox" value="L" checked disabled> <span>L (wajib)</span></label>
                  <label class="jt-kolek-menu__option"><input type="checkbox" id="jtKolekDP" value="DP" onchange="handleJTKolekChange()"> <span>DP</span></label>
              </div>
          </div>
          <button type="button" onclick="exportExcelRekapJT()" class="btn-icon jt-download-btn h-[34px] md:h-[38px] w-[34px] md:w-[38px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm" title="Download Excel" aria-label="Download Excel">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="md:w-[18px] md:h-[18px]"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          </button>
      </div>

      <aside id="jtInfoPopover" class="jt-info-popover hidden" role="dialog" aria-labelledby="jtInfoTitle" aria-hidden="true">
          <div class="jt-info-popover__head">
              <div>
                  <div id="jtInfoTitle" class="jt-info-popover__title">Tentang Jatuh Tempo Kredit</div>
                  <p class="jt-info-popover__text">Report ini membantu melihat kredit yang jatuh tempo dan peluang penyelesaian/refinancing.</p>
              </div>
              <button type="button" class="jt-info-popover__close" onclick="toggleJTInfo(event, false)" aria-label="Tutup informasi">&times;</button>
          </div>
          <ul class="jt-info-popover__list">
              <li><b>Potensi Kredit</b> menampilkan seluruh daftar jatuh tempo sesuai filter.</li>
              <li><b>Refinacing</b> menampilkan kredit yang memiliki realisasi baru.</li>
              <li><b>Basis nominal</b> dapat dipilih antara Saldo Bank dan Baki Debet.</li>
          </ul>
      </aside>
  </div>

  <div class="flex-1 min-h-0 relative flex flex-col">
    <div id="loadingJT" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
        <div class="animate-spin rounded-full h-8 w-8 md:h-10 md:w-10 border-4 border-blue-500 border-t-transparent mb-3"></div>
        <span class="text-[10px] md:text-sm font-bold uppercase tracking-widest">Menyiapkan Data...</span>
    </div>

    <div id="jtScroller" class="jt-table-wrapper h-full overflow-auto custom-scrollbar relative">
      <table class="w-max min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelJT">
        <thead class="tracking-wider bg-slate-50 text-slate-800 font-bold text-[10px] md:text-sm" id="headJT">
          </thead>
        <tbody id="bodyJT" class="divide-y divide-slate-100 bg-white text-xs md:text-sm"></tbody>
      </table>
    </div>
  </div>

</div>

<div id="modalDetailJT" class="fixed inset-0 z-[9999] hidden items-end md:items-center justify-center p-0 sm:p-4">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalJT()"></div>

  <div class="relative bg-white w-full h-[95vh] md:h-[92vh] max-w-[1700px] rounded-t-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up">

    <div id="jtModalHeader" class="jt-modal-header flex justify-between items-center px-3 py-3 md:px-5 md:py-4 border-b bg-slate-50 shrink-0 flex-wrap gap-2">
        <div class="flex-1 min-w-[200px]">
            <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm md:text-base" id="modalTitleJT">
                <span class="bg-blue-100 text-blue-600 p-1 md:p-1.5 rounded-lg shadow-sm text-xs">👥</span>
                Detail Nasabah
            </h3>
            <p class="text-[10px] md:text-xs text-slate-500 mt-0.5 ml-1 md:ml-8 font-mono" id="modalSubTitleJT">...</p>
        </div>

        <div class="flex flex-wrap items-center gap-1.5 ml-auto shrink-0 w-full sm:w-auto mt-2 sm:mt-0 overflow-x-auto no-scrollbar">
            <select id="filter_kankas_modal" class="inp px-2 md:px-3 h-[34px] md:h-10 flex-1 sm:w-[160px] text-xs md:text-sm font-bold text-blue-800 bg-blue-50 outline-none shrink-0 cursor-pointer" onchange="filterAODetail()">
                <option value="">Semua Kankas</option>
            </select>
            <select id="filter_ao_modal" class="inp px-2 md:px-3 h-[34px] md:h-10 flex-1 sm:w-[160px] text-xs md:text-sm font-bold text-slate-700 bg-white outline-none shrink-0 cursor-pointer" onchange="filterAODetail()">
                <option value="">Semua AO</option>
            </select>

            <button onclick="downloadExcelDetailJT(event)" class="btn-icon bg-emerald-600 hover:bg-emerald-700 text-white w-[34px] md:w-10 h-[34px] md:h-10 rounded-lg shadow-sm shrink-0" title="Download Excel" aria-label="Download Excel">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </button>
            <button onclick="closeModalJT()" class="w-[34px] md:w-10 h-[34px] md:h-10 flex items-center justify-center rounded-xl bg-slate-200 hover:bg-red-500 hover:text-white text-slate-600 transition font-bold text-xl md:text-2xl leading-none shrink-0">&times;</button>
        </div>
    </div>

    <div id="jtModalContent" class="jt-modal-content flex-1 overflow-auto bg-slate-50 relative custom-scrollbar p-0 md:p-3">
        <div id="loadingModalJT" class="hidden absolute inset-0 bg-white/90 z-40 flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
            <div class="animate-spin rounded-full h-8 w-8 md:h-10 md:w-10 border-4 border-blue-500 border-t-transparent mb-3"></div>
            <span class="text-[10px] md:text-sm font-bold uppercase tracking-widest">Memuat Detail...</span>
        </div>

        <table class="w-max min-w-full text-left text-slate-700 border border-slate-200 md:rounded-xl shadow-sm bg-white table-fixed" id="tableDetailJT">
            <thead class="text-slate-600 font-extrabold uppercase tracking-wider text-[9px] md:text-xs">
                <tr>
                    <th class="jt-desktop-identity px-2 md:px-3 py-2.5 md:py-4 border-b border-r border-slate-300 w-[90px] md:w-[120px] mod-sticky-1 rounded-tl-lg md:rounded-tl-xl text-blue-900 bg-[#f1f5f9]">Rekening</th>
                    <th class="jt-desktop-identity px-3 md:px-4 py-2.5 md:py-4 border-b border-r border-slate-300 w-[150px] md:w-[240px] mod-sticky-2 text-blue-900 bg-[#f1f5f9]">Nama Nasabah</th>
                    <th class="jt-mobile-identity px-2 py-2.5 border-b border-r border-slate-300 rounded-tl-lg text-blue-900 bg-[#f1f5f9]">Rekening / Nama Nasabah</th>
                    <th class="px-3 md:px-4 py-2.5 md:py-4 border-b border-r border-slate-300 w-[140px] md:w-[210px] text-blue-800">Nama Produk</th>
                    <th class="px-3 md:px-4 py-2.5 md:py-4 border-b border-r border-slate-300 w-[140px] md:w-[200px]">Alamat</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-4 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-center">No HP</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-4 border-b border-r border-slate-300 w-[110px] md:w-[150px] text-center">Nama Kankas</th>
                    <th class="px-3 md:px-4 py-2.5 md:py-4 border-b border-r border-slate-300 w-[120px] md:w-[160px] text-blue-800">Nama AO</th>
                    <th id="jtDetailNominalOld" class="px-3 md:px-4 py-2.5 md:py-4 border-b border-r border-slate-300 w-[90px] md:w-[140px] text-right">Plafon (Jml Pinjaman)</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-4 border-b border-r border-slate-300 w-[80px] md:w-[120px] text-center">Tanggal JT</th>
                    <th id="jtDetailNominalRemain" class="px-3 md:px-4 py-2.5 md:py-4 border-b border-r border-slate-300 w-[90px] md:w-[140px] text-right">Sisa Saldo Bank</th>
                    <th class="px-2 md:px-4 py-2.5 md:py-4 border-b border-r border-slate-300 w-[80px] md:w-[120px] text-center">Status</th>
                    <th id="jtDetailNominalNew" class="px-3 md:px-4 py-2.5 md:py-4 border-b border-r border-emerald-300 w-[90px] md:w-[140px] text-right bg-emerald-50 text-emerald-900">Plafon Baru</th>
                    <th class="px-2 md:px-3 py-2.5 md:py-4 border-b border-slate-300 w-[70px] md:w-[90px] text-center">Aksi</th>
                </tr>
                <tr id="rowTotalDetailAtas"></tr>
            </thead>
            <tbody id="bodyModalJT" class="divide-y divide-slate-100 bg-white text-[10px] md:text-sm"></tbody>
        </table>
    </div>

    <div class="px-3 py-3 md:px-6 md:py-4 border-t bg-white flex justify-between items-center shrink-0">
        <span id="pageInfoJT" class="text-[10px] md:text-sm font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">0 Data</span>
        <div class="flex gap-1.5 md:gap-2">
            <button id="btnPrevJT" onclick="changePageDetail(-1)" class="px-3 md:px-4 py-1.5 md:py-2 bg-white border border-slate-300 rounded-lg text-[10px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">« Prev</button>
            <button id="btnNextJT" onclick="changePageDetail(1)" class="px-3 md:px-4 py-1.5 md:py-2 bg-white border border-slate-300 rounded-lg text-[10px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">Next »</button>
        </div>
    </div>
  </div>
</div>

<script>
  // --- CONFIG & GLOBAL VARS ---
  const API_JT_URL = './api/jt/';
  const API_KODE   = './api/kode/';
  const API_DATE   = './api/date/';
  const nfID = new Intl.NumberFormat('id-ID');
  const fmt  = n => nfID.format(Math.round(Number(n||0)));
  const escapeJT = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
  const apiCall = (url, opt={}) => (window.apiFetch ? window.apiFetch(url,opt) : fetch(url,opt));

  let abortJT;
  let currentDetailParams = {};
  let currentDetailPage = 1;
  let currentDetailTotalPages = 1;
  const detailLimit = 20;
  let userKodeGlobal = '000';
  let rekapDataCache = null;
  let currentJTBreakdown = 'CABANG';
  let currentJTArea = 'ALL';
  let currentJTNominal = 'saldo_bank';
  let currentJTKolek = ['L'];
  let jtAutoBreakdownArea = '';

  window.toggleJTInfo = function(event, force) {
      event?.stopPropagation();
      const popover = document.getElementById('jtInfoPopover');
      const button = document.getElementById('jtInfoButton');
      if (!popover || !button) return;
      const shouldOpen = typeof force === 'boolean' ? force : popover.classList.contains('hidden');
      popover.classList.toggle('hidden', !shouldOpen);
      popover.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
      button.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
  };

  document.addEventListener('click', event => {
      if (!event.target.closest('#jtInfoPopover') && !event.target.closest('#jtInfoButton')) {
          window.toggleJTInfo(null, false);
      }
  });
  document.addEventListener('keydown', event => {
      if (event.key === 'Escape') window.toggleJTInfo(null, false);
  });

  // --- INIT ---
  window.addEventListener('DOMContentLoaded', async () => {
      const user = (window.getUser && window.getUser()) || null;
      userKodeGlobal = (user?.kode ? String(user.kode).padStart(3,'0') : '000');

      bindJTNavbarFilter();
      bindJTKolekFilter();
      await populateJTAreaOptions(userKodeGlobal);
      setupHeaderJT(userKodeGlobal, 'CABANG');

      const d = await getLastHarianData();
      if(d) {
          document.getElementById('closing_date_jt').value = d.last_closing;
          document.getElementById('harian_date_jt').value  = d.last_created;
          document.getElementById('filter_bulan').value = String(new Date(d.last_created).getMonth() + 1).padStart(2, '0');
      } else {
          const now = new Date();
          document.getElementById('closing_date_jt').value = `${now.getFullYear() - 1}-12-31`;
          document.getElementById('harian_date_jt').value = now.toISOString().split('T')[0];
          document.getElementById('filter_bulan').value = String(now.getMonth() + 1).padStart(2, '0');
      }
      document.getElementById('filter_tahun').value = new Date().getFullYear();

      updateJTFilterView();
      fetchRekapJT();
  });

  async function getLastHarianData(){
      try{ const r=await apiCall(API_DATE); const j=await r.json(); return j.data||null; }catch{ return null; }
  }

  function jtAreaValue() {
      return String(document.getElementById('jt_kantor_filter')?.value || 'ALL').trim();
  }

  function normalizeJTOfficeValue(value) {
      const raw = String(value || '').trim().toUpperCase();
      if (!raw || raw === 'ALL' || raw === '000') return null;
      const code = raw.replace(/^CAB(?:ANG)?(?:-|:)/, '');
      return /^\d{1,3}$/.test(code) ? code.padStart(3, '0') : null;
  }

  function jtNominalValue() {
      return document.getElementById('jt_nominal_field')?.value === 'baki_debet' ? 'baki_debet' : 'saldo_bank';
  }

  function jtNominalLabel(value = currentJTNominal) {
      return value === 'baki_debet' ? 'Baki Debet' : 'Saldo Bank';
  }

  function jtKolekValue() {
      return document.getElementById('jtKolekDP')?.checked ? ['L', 'DP'] : ['L'];
  }

  function jtKolekLabel(value = currentJTKolek) {
      return value.join(' + ');
  }

  function updateJTKolekControl() {
      const button = document.getElementById('jtKolekToggle');
      const label = jtKolekLabel();
      button?.setAttribute('title', `Kolektibilitas: ${label}`);
      button?.setAttribute('aria-label', `Kolektibilitas aktif: ${label}`);
  }

  window.toggleJTKolekMenu = function(event) {
      event?.preventDefault();
      event?.stopPropagation();
      const menu = document.getElementById('jtKolekMenu');
      const button = document.getElementById('jtKolekToggle');
      if (!menu) return;
      const open = menu.classList.toggle('hidden') === false;
      button?.setAttribute('aria-expanded', String(open));
  };

  window.handleJTKolekChange = function() {
      currentJTKolek = jtKolekValue();
      updateJTKolekControl();
      fetchRekapJT();
  };

  function bindJTKolekFilter() {
      updateJTKolekControl();
      document.addEventListener('click', event => {
          const menu = document.getElementById('jtKolekMenu');
          const button = document.getElementById('jtKolekToggle');
          if (!menu || menu.contains(event.target) || button?.contains(event.target)) return;
          menu.classList.add('hidden');
          button?.setAttribute('aria-expanded', 'false');
      });
  }

  function isJTBranchSelected() {
      const area = jtAreaValue();
      return /^CAB(?:-|:)/i.test(area) || (/^\d+$/.test(area) && area !== '000');
  }

  function currentJTDetailScope() {
      const area = jtAreaValue();
      let kode_kantor = null;
      let korwil = null;
      if (/^KOR-/i.test(area)) korwil = area.replace(/^KOR-/i, '');
      else if (area !== 'ALL' && area !== '') kode_kantor = normalizeJTOfficeValue(area);
      if (userKodeGlobal !== '000' && !kode_kantor && !korwil) kode_kantor = userKodeGlobal;
      return { kode_kantor, korwil };
  }

  function updateJTBreakdownControl() {
      const button = document.getElementById('jtBreakdownToggle');
      const visible = isJTBranchSelected();
      button?.classList.toggle('hidden', !visible);
      const activeLabel = currentJTBreakdown === 'AO' ? 'Per AO Kredit' : 'Per Kankas';
      const nextLabel = currentJTBreakdown === 'AO' ? 'Per Kankas' : 'Per AO Kredit';
      button?.setAttribute('title', `Saat ini ${activeLabel}. Klik untuk ${nextLabel}`);
      button?.setAttribute('aria-label', `Saat ini ${activeLabel}. Klik untuk ${nextLabel}`);
  }

  function updateJTFilterView() {
      const branch = isJTBranchSelected();
      if (!branch) {
          currentJTBreakdown = 'CABANG';
          const select = document.getElementById('jt_breakdown_by');
          if (select) select.value = 'KANKAS';
      } else {
          currentJTBreakdown = String(document.getElementById('jt_breakdown_by')?.value || 'KANKAS').toUpperCase();
      }
      currentJTArea = jtAreaValue();
      currentJTNominal = jtNominalValue();
      updateJTBreakdownControl();
  }

  function toggleJTNavbarFilter(open) {
      const panel = document.getElementById('jatuhTempoNavbarFilterPanel');
      const toggle = document.getElementById('jatuhTempoNavbarFilterToggle');
      if (!panel) return;
      const next = typeof open === 'boolean' ? open : panel.classList.contains('hidden');
      panel.classList.toggle('hidden', !next);
      panel.classList.toggle('flex', next);
      toggle?.classList.toggle('is-active', next);
      toggle?.setAttribute('aria-expanded', String(next));
  }

  function bindJTNavbarFilter() {
      document.getElementById('jatuhTempoNavbarFilterToggle')?.addEventListener('click', () => toggleJTNavbarFilter());
      document.getElementById('jatuhTempoNavbarFilterClose')?.addEventListener('click', () => toggleJTNavbarFilter(false));
      document.addEventListener('click', event => {
          const panel = document.getElementById('jatuhTempoNavbarFilterPanel');
          const toggle = document.getElementById('jatuhTempoNavbarFilterToggle');
          if (panel && !panel.contains(event.target) && !toggle?.contains(event.target)) toggleJTNavbarFilter(false);
      });
  }

  async function populateJTAreaOptions(userKode) {
      const select = document.getElementById('jt_kantor_filter');
      if (!select) return;
      const code = String(userKode || '000').padStart(3, '0');
      if (code !== '000') {
          select.innerHTML = `<option value="CAB-${code}">${code} - Cabang Login</option>`;
          select.value = `CAB-${code}`;
          select.disabled = true;
          return;
      }
      try {
          const response = await apiCall(API_KODE, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'})});
          const json = await response.json();
          const list = Array.isArray(json.data) ? json.data : [];
          let html = '<option value="ALL">Konsolidasi</option>';
          ['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].forEach(korwil => { html += `<option value="KOR-${korwil}">Korwil ${korwil[0]}${korwil.slice(1).toLowerCase()}</option>`; });
          list.filter(item => String(item.kode_kantor || '') !== '000').sort((a,b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor))).forEach(item => {
              const codeItem = String(item.kode_kantor).padStart(3, '0');
              html += `<option value="CAB-${codeItem}">${codeItem} - ${item.nama_kantor || `Cabang ${codeItem}`}</option>`;
          });
          select.innerHTML = html;
          select.disabled = false;
      } catch (error) {
          select.innerHTML = '<option value="ALL">Konsolidasi</option>';
      }
  }

  window.handleJTAreaChange = function() {
      jtAutoBreakdownArea = '';
      updateJTFilterView();
      if (document.getElementById('closing_date_jt')?.value && document.getElementById('harian_date_jt')?.value) fetchRekapJT();
      if (window.innerWidth < 768) setTimeout(() => toggleJTNavbarFilter(false), 180);
  };

  window.handleJTBreakdownChange = function() {
      updateJTFilterView();
      fetchRekapJT();
      if (window.innerWidth < 768) setTimeout(() => toggleJTNavbarFilter(false), 180);
  };

  window.toggleJTBreakdown = function() {
      if (!isJTBranchSelected()) return;
      const select = document.getElementById('jt_breakdown_by');
      if (!select) return;
      select.value = String(select.value || 'KANKAS').toUpperCase() === 'AO' ? 'KANKAS' : 'AO';
      jtAutoBreakdownArea = '';
      handleJTBreakdownChange();
  };

  // --- SETUP HEADER UTAMA (RESPONSIVE & FONT KECIL NAMA KANTOR) ---
  function setupHeaderJT(userKode, breakdown = 'CABANG') {
      const th = document.getElementById('headJT');
      const grouped = breakdown === 'KANKAS' || breakdown === 'AO';
      const nominalLabel = jtNominalLabel();
      const identityClass = 'border-r border-slate-300 align-middle text-left bg-slate-50';
      const metricClass = 'border-r border-slate-300 align-middle text-center bg-slate-50';
      const nominalClass = 'border-r border-slate-300 align-middle text-right bg-slate-50';
      const identityWidth = grouped ? 'min-w-[190px] md:min-w-[230px]' : userKode === '000' ? 'min-w-[160px] md:min-w-[200px]' : 'min-w-[190px] md:min-w-[250px]';
      const kankasLabel = 'Nama Kankas';
      const codeLabel = 'Kode';

      let identity = '';

      identity = `
          <th rowspan="2" class="sticky-left-1 jt-code-col jt-head-neutral ${identityClass} uppercase text-center text-slate-700 rounded-tl-lg">${codeLabel}</th>
          <th rowspan="2" class="sticky-left-2 ${grouped ? 'jt-grouped-name' : 'jt-office-name'} jt-head-neutral ${identityWidth} ${identityClass} uppercase pl-3 md:pl-4 text-slate-700">${grouped ? (breakdown === 'AO' ? 'Nama AO' : kankasLabel) : 'Nama Kantor'}</th>
      `;

      const thContent = `
          <tr class="jt-group-row">
              ${identity}
              <th colspan="2" class="jt-head-blue ${metricClass} bg-blue-50 text-blue-900">Potensi Kredit</th>
              <th colspan="2" class="jt-head-green ${metricClass} bg-emerald-50 text-emerald-900">Refinacing</th>
              <th rowspan="2" class="jt-head-neutral ${metricClass} bg-slate-50 text-slate-700">%</th>
              <th colspan="2" class="jt-head-sky ${metricClass} bg-sky-50 text-sky-900">Lunas (Prospek)</th>
              <th colspan="3" class="jt-head-orange ${metricClass} bg-orange-50 text-orange-900">Belum Lunas</th>
          </tr>
          <tr class="jt-sub-row">
              <th class="jt-head-blue ${metricClass} bg-blue-50 text-blue-900 w-[58px] md:w-[76px]">NOA</th>
              <th class="jt-head-blue ${nominalClass} bg-blue-50 text-blue-900 w-[120px] md:w-[160px]">Plafon</th>
              <th class="jt-head-green ${metricClass} bg-emerald-50 text-emerald-900 w-[58px] md:w-[76px]">NOA</th>
              <th class="jt-head-green ${nominalClass} bg-emerald-50 text-emerald-900 w-[120px] md:w-[160px]">Plafon</th>
              
              <th class="jt-head-sky ${metricClass} bg-sky-50 text-sky-900 w-[58px] md:w-[76px]">NOA</th>
              <th class="jt-head-sky ${nominalClass} bg-sky-50 text-sky-900 w-[120px] md:w-[160px]">Plafon</th>
              <th class="jt-head-orange ${nominalClass} bg-orange-50 text-orange-900 w-[150px] md:w-[210px]">Sisa (${nominalLabel})</th>
              <th class="jt-head-orange ${metricClass} bg-orange-50 text-orange-900 w-[58px] md:w-[76px]">NOA</th>
              <th class="jt-head-orange ${nominalClass} bg-orange-50 text-orange-900 w-[120px] md:w-[160px]">Plafon</th>
          </tr>
          <tr id="rowTotalJTAtas"></tr>
      `;
      th.innerHTML = thContent;
  }

  async function loadKankasModal(kode_cabang) {
      const el = document.getElementById('filter_kankas_modal');
      el.innerHTML = '<option value="">Semua Kankas</option>';
      if(!kode_cabang) return;
      try {
          const r = await apiCall(API_KODE, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type: 'kode_kankas', kode_kantor: kode_cabang}) });
          const j = await r.json();
          if(j.data && Array.isArray(j.data)) {
              j.data.forEach(x => { el.add(new Option(x.deskripsi_group1 || x.kode_group1, x.kode_group1)); });
              if (currentDetailParams.kode_kankas) el.value = currentDetailParams.kode_kankas;
          }
      } catch(e) {}
  }

  // --- FETCH REKAP UTAMA ---
  async function fetchRekapJT(){
      const l=document.getElementById('loadingJT'); const tb=document.getElementById('bodyJT'); const trTot=document.getElementById('rowTotalJTAtas');

      if(abortJT) abortJT.abort(); abortJT = new AbortController();
      l.classList.remove('hidden');
      updateJTFilterView();
      const grouped = currentJTBreakdown === 'KANKAS' || currentJTBreakdown === 'AO';
      const identityCols = 2;
      const colSpan = identityCols + 10;
      tb.innerHTML = `<tr><td colspan="${colSpan}" class="py-16 md:py-20 text-center text-slate-400 italic text-xs md:text-base">Sedang mengambil data...</td></tr>`;
      trTot.innerHTML = '';
      rekapDataCache = null;

      try {
          const area = jtAreaValue();
          const breakdownBy = grouped ? currentJTBreakdown : null;
          const payload = {
              type: 'rekap prospek jatuh tempo',
              closing_date: document.getElementById('closing_date_jt').value,
              harian_date: document.getElementById('harian_date_jt').value,
              bulan: document.getElementById('filter_bulan').value,
              tahun: document.getElementById('filter_tahun').value,
              nominal_field: currentJTNominal,
              hitung_berdasarkan: currentJTNominal,
              kolektibilitas: currentJTKolek,
              kode_kantor: null,
              korwil: null,
              breakdown_by: breakdownBy
          };
          if (/^KOR-/i.test(area)) payload.korwil = area.replace(/^KOR-/i, '');
          else if (area !== 'ALL' && area !== '') payload.kode_kantor = normalizeJTOfficeValue(area);
          if (userKodeGlobal !== '000' && !payload.kode_kantor && !payload.korwil) payload.kode_kantor = userKodeGlobal;
          const res = await apiCall(API_JT_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload), signal: abortJT.signal });
          const json = await res.json();
          if(json.status !== 200) throw new Error(json.message);
          const meta = json.data?.breakdown || {};
          const recommended = String(meta.recommended || '').toUpperCase();
          const branchArea = Boolean(payload.kode_kantor && payload.kode_kantor !== '000');
          if (branchArea && currentJTBreakdown === 'KANKAS' && recommended === 'AO' && jtAutoBreakdownArea !== area) {
              jtAutoBreakdownArea = area;
              const breakdownSelect = document.getElementById('jt_breakdown_by');
              if (breakdownSelect) breakdownSelect.value = 'AO';
              currentJTBreakdown = 'AO';
              updateJTBreakdownControl();
              setupHeaderJT(userKodeGlobal, currentJTBreakdown);
              fetchRekapJT();
              return;
          }
          currentJTBreakdown = String(meta.applied || currentJTBreakdown || 'CABANG').toUpperCase();
          setupHeaderJT(userKodeGlobal, currentJTBreakdown);
          let rows = json.data.rekap_per_cabang || [];
          rekapDataCache = rows;
          renderTableJT(rows, userKodeGlobal);

      } catch(err) { if(err.name !== 'AbortError') tb.innerHTML=`<tr><td colspan="${colSpan}" class="py-12 md:py-16 text-center text-red-500 tracking-widest uppercase font-bold text-[10px] md:text-sm">${err.message}</td></tr>`; }
      finally { l.classList.add('hidden'); }
  }

  function renderTableJT(rows, userKode) {
      const tb = document.getElementById('bodyJT'); tb.innerHTML = '';
      const trTot = document.getElementById('rowTotalJTAtas'); trTot.innerHTML = '';
      trTot.onclick = null;
      trTot.classList.remove('jt-total-clickable');
      const grouped = currentJTBreakdown === 'KANKAS' || currentJTBreakdown === 'AO';
      const identityCols = 2;
      const colSpan = identityCols + 10;

      if(rows.length === 0){ tb.innerHTML = `<tr><td colspan="${colSpan}" class="py-16 md:py-20 text-center text-slate-500 text-xs md:text-base">Tidak ada data.</td></tr>`; return; }

      let T = { noa_potensi:0, plafon_potensi:0, noa_refinancing:0, plafon_refinancing:0, noa_lunas:0, plafon_lunas:0, sisa_belum_lunas:0, noa_belum_lunas:0, plafon_belum_lunas:0 };
      let html = '';
      const safe = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
      const arg = value => encodeURIComponent(String(value ?? '')).replace(/'/g, '%27');
      const fmtOrDash = value => Number(value || 0) === 0 ? '-' : fmt(value);

      rows.forEach(r => {
          T.noa_potensi += Number(r.noa_potensi || 0); T.plafon_potensi += Number(r.plafon_potensi || 0);
          T.noa_refinancing += Number(r.noa_refinancing || 0); T.plafon_refinancing += Number(r.plafon_refinancing || 0);
          T.noa_lunas += Number(r.noa_lunas || 0); T.plafon_lunas += Number(r.plafon_lunas || 0);
          T.sisa_belum_lunas += Number(r.sisa_belum_lunas || 0); T.noa_belum_lunas += Number(r.noa_belum_lunas || 0); T.plafon_belum_lunas += Number(r.plafon_belum_lunas || 0);

          const rowLabel = grouped ? (r.group_label || r.nama_kantor || '-') : (r.nama_kantor || '-');
          const groupCode = grouped ? (r.group_code || '') : '';
          const detailClick = category => `onclick="event.stopPropagation(); initModalDetail('${arg(r.kode_kantor)}','${arg(rowLabel)}','${arg(grouped ? currentJTBreakdown : 'CABANG')}','${arg(groupCode)}','${arg(category)}')"`;
          let rowHtml = `<tr class="transition h-[42px] md:h-[52px] border-b border-slate-100 group" onclick="initModalDetail('${arg(r.kode_kantor)}','${arg(rowLabel)}','${arg(grouped ? currentJTBreakdown : 'CABANG')}','${arg(groupCode)}','POTENSI')">`;

          // 🔥 FIX: Nama Kantor Font Dikecilkan (text-[10px] md:text-sm), Angka Dibesarkan
          if (grouped) {
              rowHtml += `<td class="sticky-left-1 jt-code-col px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-slate-500 shadow-[inset_-1px_0_0_#e2e8f0] z-20">${safe(groupCode || '-')}</td>
                <td class="sticky-left-2 jt-grouped-name px-3 md:px-5 py-1.5 md:py-2 text-left font-bold text-slate-700 truncate shadow-[inset_-1px_0_0_#e2e8f0] z-20 text-[10px] md:text-sm" title="${safe(rowLabel)}">${safe(rowLabel)}</td>`;
          } else if (userKode === '000') {
              rowHtml += `
                <td class="sticky-left-1 jt-code-col px-2 md:px-4 py-1.5 md:py-2 text-center font-mono font-bold text-slate-500 shadow-[inset_-1px_0_0_#e2e8f0] z-20 text-[10px] md:text-sm">${safe(r.kode_kantor)}</td>
                <td class="sticky-left-2 jt-office-name px-3 md:px-5 py-1.5 md:py-2 text-left font-bold text-slate-700 truncate shadow-[inset_-1px_0_0_#e2e8f0] z-20 text-[10px] md:text-sm" title="${safe(rowLabel)}">${safe(rowLabel)}</td>
              `;
          } else {
              rowHtml += `
                <td class="sticky-left-1 jt-code-col px-2 md:px-3 py-1.5 md:py-2 text-center font-mono font-bold text-slate-500 shadow-[inset_-1px_0_0_#e2e8f0] z-20">${safe(r.kode_kantor)}</td>
                <td class="sticky-left-2 jt-office-name px-3 md:px-5 py-1.5 md:py-2 text-left font-bold text-slate-700 truncate shadow-[inset_-1px_0_0_#e2e8f0] z-20 text-[10px] md:text-sm" title="${safe(rowLabel)}">${safe(rowLabel)}</td>
              `;
          }

          rowHtml += `
                <td class="px-2 md:px-4 py-1.5 md:py-2 text-center text-slate-700 border-r border-slate-100">${fmtOrDash(r.noa_potensi)}</td>
                <td class="px-2 md:px-4 py-1.5 md:py-2 text-right text-slate-700 border-r border-slate-100">${fmtOrDash(r.plafon_potensi)}</td>
                <td ${detailClick('REFINANCING')} class="jt-clickable px-2 md:px-4 py-1.5 md:py-2 text-center text-emerald-700 border-r border-emerald-100 bg-emerald-50/30">${fmtOrDash(r.noa_refinancing)}</td>
                <td ${detailClick('REFINANCING')} class="jt-clickable px-2 md:px-4 py-1.5 md:py-2 text-right text-emerald-700 border-r border-emerald-100 bg-emerald-50/30">${fmtOrDash(r.plafon_refinancing)}</td>
                <td class="px-2 md:px-3 py-1.5 md:py-2 text-center text-orange-600 border-r border-slate-100">${Number(r.persentase || 0).toFixed(2).replace('.', ',')}%</td>
                <td ${detailClick('LUNAS')} class="jt-clickable px-2 md:px-4 py-1.5 md:py-2 text-center text-sky-700 border-r border-sky-100 bg-sky-50/30">${fmtOrDash(r.noa_lunas)}</td>
                <td ${detailClick('LUNAS')} class="jt-clickable px-2 md:px-4 py-1.5 md:py-2 text-right text-sky-700 border-r border-sky-100 bg-sky-50/30">${fmtOrDash(r.plafon_lunas)}</td>
                <td ${detailClick('BELUM_LUNAS')} class="jt-clickable px-2 md:px-4 py-1.5 md:py-2 text-right text-orange-700 border-r border-orange-100 bg-orange-50/30">${fmtOrDash(r.sisa_belum_lunas)}</td>
                <td ${detailClick('BELUM_LUNAS')} class="jt-clickable px-2 md:px-4 py-1.5 md:py-2 text-center text-orange-700 border-r border-orange-100 bg-orange-50/30">${fmtOrDash(r.noa_belum_lunas)}</td>
                <td ${detailClick('BELUM_LUNAS')} class="jt-clickable px-2 md:px-4 py-1.5 md:py-2 text-right text-orange-700 border-r border-orange-100 bg-orange-50/30">${fmtOrDash(r.plafon_belum_lunas)}</td>
            </tr>`;

          html += rowHtml;
      });
      tb.innerHTML = html;

      if(grouped && rows.length > 0) {
          const gp = (T.plafon_potensi > 0) ? (T.plafon_refinancing / T.plafon_potensi * 100) : 0;
          trTot.innerHTML = `
              <th class="sticky-left-1 jt-code-col px-2 md:px-3 border-r border-blue-300 text-center text-slate-500 align-middle"></th>
              <th class="sticky-left-2 jt-grouped-name px-3 md:px-5 border-r border-blue-300 text-left uppercase tracking-widest font-extrabold text-[10px] md:text-sm text-blue-900">TOTAL</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-center text-blue-900 align-middle">${fmtOrDash(T.noa_potensi)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-blue-900 align-middle">${fmtOrDash(T.plafon_potensi)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-center text-emerald-800 align-middle">${fmtOrDash(T.noa_refinancing)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-emerald-800 align-middle">${fmtOrDash(T.plafon_refinancing)}</th>
              <th class="px-2 md:px-3 border-r border-blue-300 text-center text-orange-700 align-middle">${gp.toFixed(2).replace('.', ',')}%</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-center text-sky-800 align-middle">${fmtOrDash(T.noa_lunas)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-sky-800 align-middle">${fmtOrDash(T.plafon_lunas)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-orange-800 align-middle">${fmtOrDash(T.sisa_belum_lunas)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-center text-orange-800 align-middle">${fmtOrDash(T.noa_belum_lunas)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-orange-800 align-middle">${fmtOrDash(T.plafon_belum_lunas)}</th>
          `;
      } else if(rows.length > 0) {
          const gp = (T.plafon_potensi > 0) ? (T.plafon_refinancing / T.plafon_potensi * 100) : 0;
          trTot.innerHTML = `
              <th class="sticky-left-1 jt-code-col px-2 md:px-4 border-r border-blue-300 text-center text-blue-900"></th>
              <th class="sticky-left-2 jt-office-name px-3 md:px-5 border-r border-blue-300 text-left uppercase tracking-widest font-extrabold text-[10px] md:text-sm text-blue-900">TOTAL</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-center text-blue-900 align-middle">${fmtOrDash(T.noa_potensi)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-blue-900 align-middle">${fmtOrDash(T.plafon_potensi)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-center text-emerald-800 align-middle">${fmtOrDash(T.noa_refinancing)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-emerald-800 align-middle">${fmtOrDash(T.plafon_refinancing)}</th>
              <th class="px-2 md:px-3 border-r border-blue-300 text-center text-orange-700 align-middle">${gp.toFixed(2).replace('.', ',')}%</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-center text-sky-800 align-middle">${fmtOrDash(T.noa_lunas)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-sky-800 align-middle">${fmtOrDash(T.plafon_lunas)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-orange-800 align-middle">${fmtOrDash(T.sisa_belum_lunas)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-center text-orange-800 align-middle">${fmtOrDash(T.noa_belum_lunas)}</th>
              <th class="px-2 md:px-4 border-r border-blue-300 text-right text-orange-800 align-middle">${fmtOrDash(T.plafon_belum_lunas)}</th>
          `;
      } else {
          trTot.innerHTML = ``;
      }

      if (trTot.children.length > 0) {
          trTot.classList.add('jt-total-clickable');
          trTot.onclick = () => initModalDetail('', 'TOTAL', 'TOTAL', '', 'POTENSI', true);

          // Klik angka TOTAL harus membuka kategori yang sesuai, bukan selalu POTENSI.
          const bindTotalDetail = (indexes, category, label) => indexes.forEach(index => {
              const cell = trTot.children[index];
              if (!cell) return;
              cell.classList.add('jt-total-detail-clickable');
              cell.title = `Lihat detail ${label}`;
              cell.onclick = event => {
                  event.stopPropagation();
                  initModalDetail('', 'TOTAL', 'TOTAL', '', category, true);
              };
          });
          bindTotalDetail([2, 3], 'POTENSI', 'Potensi Kredit');
          bindTotalDetail([4, 5, 6], 'REFINANCING', 'Refinacing');
          bindTotalDetail([7, 8], 'LUNAS', 'Lunas (Prospek)');
          bindTotalDetail([9, 10, 11], 'BELUM_LUNAS', 'Belum Lunas');
      }
  }

  // --- EXPORT EXCEL REKAP UTAMA ---
  window.exportExcelRekapJT = function() {
      if(!rekapDataCache || rekapDataCache.length === 0) return alert("Tidak ada data rekap untuk didownload.");

      const groupLabel = currentJTBreakdown === 'AO' ? 'AO Kredit' : currentJTBreakdown === 'KANKAS' ? 'Kankas' : 'Nama Kantor';
      const nominalLabel = jtNominalLabel();
      let csv = `Kode\t${groupLabel}\tPotensi Kredit NOA\tPotensi Kredit Plafon\tRefinacing NOA\tRefinacing Plafon\tLunas (Prospek) NOA\tLunas (Prospek) Plafon\tBelum Lunas Sisa (${nominalLabel})\tBelum Lunas NOA\tBelum Lunas Plafon\t%\n`;
      rekapDataCache.forEach(r => {
          csv += `'${r.kode_kantor}\t${r.group_label || r.nama_kantor || ''}\t${r.noa_potensi || 0}\t${Math.round(r.plafon_potensi || 0)}\t${r.noa_refinancing || 0}\t${Math.round(r.plafon_refinancing || 0)}\t${r.noa_lunas || 0}\t${Math.round(r.plafon_lunas || 0)}\t${Math.round(r.sisa_belum_lunas || 0)}\t${r.noa_belum_lunas || 0}\t${Math.round(r.plafon_belum_lunas || 0)}\t${Number(r.persentase || 0).toFixed(2).replace('.', ',')}%\n`;
      });

      const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      a.download = `Rekap_JatuhTempo_${document.getElementById("filter_tahun").value}.xls`;
      a.click();
  }

  // --- MODAL & FILTER LOGIC ---
  function initModalDetail(kode, nama, groupType = 'CABANG', groupCode = '', category = 'POTENSI', isTotal = false) {
      kode = decodeURIComponent(String(kode || ''));
      nama = decodeURIComponent(String(nama || ''));
      groupType = decodeURIComponent(String(groupType || 'CABANG')).toUpperCase();
      groupCode = decodeURIComponent(String(groupCode || ''));
      category = decodeURIComponent(String(category || 'POTENSI')).toUpperCase();
      isTotal = isTotal === true || String(isTotal).toLowerCase() === 'true';
      if (!['POTENSI', 'REFINANCING', 'LUNAS', 'BELUM_LUNAS'].includes(category)) category = 'POTENSI';
      const categoryLabel = {POTENSI:'Potensi Kredit', REFINANCING:'Refinacing', LUNAS:'Lunas (Prospek)', BELUM_LUNAS:'Belum Lunas'}[category];
      const scope = currentJTDetailScope();
      const detailKode = isTotal ? (scope.kode_kantor || '') : kode;
      if (!isTotal && userKodeGlobal !== '000' && String(kode) !== userKodeGlobal) {
          alert(`AKSES DITOLAK!\nAnda tidak memiliki izin untuk melihat detail Cabang ${kode}.`);
          return;
      }

      currentDetailParams = {
          type: 'detail prospek jatuh tempo',
          closing_date: document.getElementById('closing_date_jt').value,
          harian_date: document.getElementById('harian_date_jt').value,
          bulan: document.getElementById('filter_bulan').value,
          tahun: document.getElementById('filter_tahun').value,
          nominal_field: currentJTNominal,
          hitung_berdasarkan: currentJTNominal,
          kolektibilitas: currentJTKolek,
          kode_kantor: isTotal ? scope.kode_kantor : detailKode,
          korwil: isTotal ? scope.korwil : null,
          kode_kankas: !isTotal && groupType === 'KANKAS' ? groupCode : null,
          kode_ao: !isTotal && groupType === 'AO' ? groupCode : null,
          kategori: category,
          limit: detailLimit
      };

      const selAO = document.getElementById('filter_ao_modal');
      selAO.innerHTML = '<option value="">Semua AO</option>';

      const modal = document.getElementById('modalDetailJT');
      modal.classList.remove('hidden'); modal.classList.add('flex');

      document.getElementById('modalTitleJT').innerHTML = `<span class="bg-blue-100 text-blue-600 p-1 md:p-1.5 rounded-lg shadow-sm text-xs md:text-sm">👥</span> Detail Nasabah - ${nama}`;
      document.getElementById('modalSubTitleJT').textContent = `Periode JT: ${currentDetailParams.bulan}/${currentDetailParams.tahun} • ${currentJTNominal === 'baki_debet' ? 'Baki Debet' : 'Saldo Bank'} • Kolek L & DP`;
      document.getElementById('modalTitleJT').innerHTML = `<span class="bg-blue-100 text-blue-600 p-1 md:p-1.5 rounded-lg shadow-sm text-xs md:text-sm"></span> ${categoryLabel} - ${nama}`;
      document.getElementById('modalSubTitleJT').textContent = `Periode JT: ${currentDetailParams.bulan}/${currentDetailParams.tahun} | ${jtNominalLabel()} | Kolek ${jtKolekLabel()}`;
      document.getElementById('jtDetailNominalOld').textContent = 'Plafon (Jml Pinjaman)';
      document.getElementById('jtDetailNominalRemain').textContent = `Sisa ${currentJTNominal === 'baki_debet' ? 'Baki Debet' : 'Saldo Bank'}`;
      document.getElementById('jtDetailNominalNew').textContent = 'Plafon Baru';

      loadKankasModal(detailKode);
      loadDetailPage(1);
  }

  function filterAODetail() {
      currentDetailParams.kode_ao = document.getElementById('filter_ao_modal').value;
      currentDetailParams.kode_kankas = document.getElementById('filter_kankas_modal').value;
      loadDetailPage(1);
  }

  async function loadDetailPage(page) {
      const l = document.getElementById('loadingModalJT'); const tb = document.getElementById('bodyModalJT'); const info = document.getElementById('pageInfoJT');
      const trTot = document.getElementById('rowTotalDetailAtas');

      l.classList.remove('hidden'); tb.innerHTML = ''; trTot.innerHTML = '';
      const actDate = new Date(document.getElementById('harian_date_jt').value);

      try {
          const payload = { ...currentDetailParams, page: page };
          const res = await apiCall(API_JT_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const json = await res.json();

          const list = json.data?.data || [];
          const aoList = json.data?.ao_list || [];
          const meta = json.data?.pagination || { total_records:0, total_pages:1 };

          currentDetailPage = page; currentDetailTotalPages = meta.total_pages;

          const selAO = document.getElementById('filter_ao_modal');
          if (selAO.options.length === 1 && aoList.length > 0) {
              aoList.forEach(ao => { selAO.add(new Option(ao.nama_ao, ao.kode_group2)); });
              if (currentDetailParams.kode_ao) selAO.value = currentDetailParams.kode_ao;
          }

          if(list.length === 0) {
              tb.innerHTML = `<tr><td colspan="13" class="py-16 md:py-20 text-center text-slate-400 italic text-xs md:text-base">Tidak ada data detail.</td></tr>`;
              info.innerText = `0 data`;
              return;
          }

          list.sort((a, b) => new Date(a.tgl_jatuh_tempo) - new Date(b.tgl_jatuh_tempo));

          let t_plafon_lama = 0, t_plafon_baru = 0, t_sisa_bd = 0;
          let html = '';

          list.forEach(r => {
              t_plafon_lama += parseFloat(r.plafond_lama||0);
              t_plafon_baru += parseFloat(r.plafond_baru||0);

              const alamatLengkap = r.alamat || '-';
              const alamatPendek = alamatLengkap.length > 25 ? alamatLengkap.substring(0, 25) + '...' : alamatLengkap;
              const hp = r.no_hp ? `<span class="font-mono text-slate-600">${r.no_hp}</span>` : `<span class="text-slate-400">-</span>`;
              const kankas = r.nama_kankas || r.kankas || '-';
              const aoName = (r.nama_ao || '-').split(' ').slice(0, 2).join(' ');
              const productName = r.nama_produk || (r.kode_produk ? `PRODUK ${r.kode_produk}` : '-');

              let statStr = (r.keterangan_status || '').toUpperCase();
              let isClear = statStr.includes("SUDAH") || statStr === "LUNAS" || statStr === "LUNAS (POTENSI)";
              let isDrop = statStr.includes("DROP");

              if(!isClear) t_sisa_bd += parseFloat(r.baki_debet_lama||0);

              let badgeClass = "text-slate-600 border-slate-300 bg-slate-50";
              if(statStr.includes("SUDAH")) badgeClass = "text-emerald-700 border-emerald-300 bg-emerald-50/80";
              else if(statStr === "LUNAS" || statStr === "LUNAS (POTENSI)") badgeClass = "text-blue-700 border-blue-300 bg-blue-50/80";
              else if(statStr.includes("TOP UP")) badgeClass = "text-purple-700 border-purple-300 bg-purple-50/80";
              else if(statStr.includes("BELUM")) badgeClass = "text-rose-700 border-rose-300 bg-rose-50/80";

              let nomBaru = '-';
              if (r.plafond_baru > 0) {
                  nomBaru = `<div class="font-bold text-emerald-700 text-sm">${fmt(r.plafond_baru)}</div><div class="text-[8px] md:text-[9px] text-emerald-600 font-mono mt-0.5">${r.tgl_realisasi_baru||''}</div>`;
              }

              let sisaBdVisual = isClear ? '-' : fmt(r.baki_debet_lama);

              const jtDate = new Date(r.tgl_jatuh_tempo);
              const diffDays = Math.ceil((jtDate - actDate) / (1000 * 60 * 60 * 24));
              let strJt = `<div class="font-mono text-[10px] md:text-sm text-slate-700">${r.tgl_jatuh_tempo}</div>`;

              if (!isClear) {
                  if (diffDays < 0) strJt += `<div class="text-[8px] md:text-[9px] text-rose-600 font-bold mt-1 bg-rose-50 rounded inline-block px-1.5 py-0.5">Lewat ${Math.abs(diffDays)} Hari</div>`;
                  else if (diffDays === 0) strJt += `<div class="text-[8px] md:text-[9px] text-orange-600 font-bold mt-1 bg-orange-50 rounded inline-block px-1.5 py-0.5">HARI INI!</div>`;
                  else strJt += `<div class="text-[8px] md:text-[10px] text-slate-500 mt-1 font-medium">Kurang ${diffDays} Hari</div>`;
              }

              let isLocked = statStr.includes("SUDAH") || isDrop;
              const btnAksi = isLocked
                  ? `<span class="text-[9px] md:text-xs font-bold text-slate-400">LOCKED</span>`
                  : `<button class="bg-blue-600 hover:bg-blue-700 text-white px-2 md:px-3 py-1 md:py-1.5 rounded md:rounded-lg text-[9px] md:text-xs font-bold shadow-sm transition w-full uppercase tracking-widest">PROSPEK</button>`;

              html += `<tr class="transition h-[42px] md:h-[52px] group border-b border-slate-100">
                    <td class="jt-desktop-identity mod-sticky-1 px-2 md:px-3 py-1.5 md:py-2 font-mono text-[9px] md:text-sm text-slate-500 bg-white border-r border-slate-100 shadow-[inset_-1px_0_0_#e2e8f0]">${escapeJT(r.no_rekening_lama)}</td>
                    <td class="jt-desktop-identity mod-sticky-2 px-3 md:px-4 py-1.5 md:py-2 font-bold text-[10px] md:text-sm text-slate-700 bg-white truncate border-r border-slate-100 max-w-[150px] md:max-w-[280px] shadow-[inset_-1px_0_0_#e2e8f0]" title="${escapeJT(r.nama_nasabah)}">${escapeJT(r.nama_nasabah)}</td>
                    <td class="jt-mobile-identity px-2 py-1.5 bg-white border-r border-slate-100" title="${escapeJT(`${r.no_rekening_lama || '-'} - ${r.nama_nasabah || '-'}`)}"><span class="jt-mobile-account">${escapeJT(r.no_rekening_lama || '-')}</span><span class="jt-mobile-name">${escapeJT(r.nama_nasabah || '-')}</span></td>
                    <td class="px-3 md:px-4 py-1.5 md:py-2 text-[9px] md:text-sm font-bold text-blue-700 truncate border-r border-slate-100 max-w-[140px] md:max-w-[210px]" title="${escapeJT(productName)}">${escapeJT(productName)}</td>
                    <td class="px-3 md:px-4 py-1.5 md:py-2 text-[9px] md:text-sm text-slate-500 whitespace-nowrap border-r border-slate-100" title="${alamatLengkap}">${alamatPendek}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center border-r border-slate-100 text-[9px] md:text-sm">${hp}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-[9px] md:text-sm text-slate-500 border-r border-slate-100">${kankas}</td>

                    <td class="px-3 md:px-4 py-1.5 md:py-2 text-[9px] md:text-sm font-bold text-blue-700 truncate border-r border-slate-100">${aoName}</td>
                    <td class="px-3 md:px-4 py-1.5 md:py-2 text-right font-medium text-[10px] md:text-sm text-slate-600 border-r border-slate-100">${fmt(r.plafond_lama)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center border-r border-slate-100">${strJt}</td>
                    <td class="px-3 md:px-4 py-1.5 md:py-2 text-right font-mono font-bold text-[10px] md:text-sm text-slate-800 border-r border-slate-100 bg-slate-50/50">${sisaBdVisual}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-center border-r border-slate-100"><span class="badge-clean ${badgeClass}">${r.keterangan_status}</span></td>
                    <td class="px-3 md:px-4 py-1.5 md:py-2 text-right bg-emerald-50/30 border-r border-slate-100">${nomBaru}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center">${btnAksi}</td>
                </tr>`;
          });
          tb.innerHTML = html;

          trTot.innerHTML = `
              <th class="jt-desktop-identity mod-sticky-1 px-2 md:px-3 border-r border-b border-blue-200 uppercase tracking-widest text-center text-blue-900 bg-[#eff6ff]">-</th>
              <th class="jt-desktop-identity mod-sticky-2 px-3 md:px-4 border-r border-b border-blue-200 uppercase tracking-widest font-extrabold text-[10px] md:text-sm text-blue-900 bg-[#eff6ff]">TOTAL HALAMAN INI</th>
              <th class="jt-mobile-identity px-2 border-r border-b border-blue-200 uppercase tracking-widest font-extrabold text-[8px] text-blue-900 bg-[#eff6ff]">TOTAL</th>
              <th class="px-3 md:px-4 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-3 md:px-4 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-2 md:px-3 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-2 md:px-3 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-3 md:px-4 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-3 md:px-4 border-r border-b border-blue-200 text-right font-mono font-bold text-[11px] md:text-sm text-blue-900 bg-[#eff6ff]">${fmt(t_plafon_lama)}</th>
              <th class="px-2 md:px-3 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-3 md:px-4 border-r border-b border-blue-300 text-right font-mono font-bold text-[11px] md:text-sm text-blue-900 bg-blue-100/50">${fmt(t_sisa_bd)}</th>
              <th class="px-2 md:px-4 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-3 md:px-4 border-r border-b border-blue-200 text-right font-mono font-bold text-[11px] md:text-sm text-emerald-800 bg-emerald-50/50">${fmt(t_plafon_baru)}</th>
              <th class="px-2 md:px-3 border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
          `;

          const start = ((page-1)*detailLimit)+1; const end = Math.min(page*detailLimit, meta.total_records);
          info.innerText = `Hal ${page} / ${meta.total_pages} (${start}-${end} dari ${fmt(meta.total_records)})`;

          document.getElementById('btnPrevJT').disabled = page <= 1;
          document.getElementById('btnNextJT').disabled = page >= meta.total_pages;

      } catch(err){ console.error(err); } finally { l.classList.add('hidden'); }
  }

  // --- EXPORT EXCEL ---
  async function downloadExcelDetailJT(event) {
      const btn = event?.target?.closest('button') || document.querySelector('#jtModalHeader button[title="Download Excel"]');
      if (!btn) return;
      const txt = btn.innerHTML;
      btn.innerHTML = `<span class="animate-spin inline-block h-3 w-3 md:h-4 md:w-4 border-2 border-white border-t-transparent rounded-full mr-1 md:mr-2"></span>...`;
      btn.disabled = true;

      try {
          const payload = { ...currentDetailParams, page: 1, limit: 10000 };
          const res = await apiCall(API_JT_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const json = await res.json();
          const rows = json.data?.data || [];

          if(rows.length === 0) { alert("Tidak ada data."); return; }

          rows.sort((a, b) => new Date(a.tgl_jatuh_tempo) - new Date(b.tgl_jatuh_tempo));

          let csv = `No Rekening\tNama Nasabah\tNama Produk\tAlamat\tNo HP\tKankas\tNama AO\tPlafond Lama\tSisa Baki Debet\tTgl JT\tStatus\tPlafond Baru\tTgl Realisasi Baru\n`;
          rows.forEach(r => {
              const alamat = r.alamat || '-';
              const hp = r.no_hp || '-';
              const kankas = r.nama_kankas || r.kankas || '-';

              let statStr = (r.keterangan_status || '').toUpperCase();
              let isClear = statStr.includes("SUDAH") || statStr === "LUNAS" || statStr === "LUNAS (POTENSI)";
              let sisaBdEx = isClear ? 0 : Math.round(r.baki_debet_lama||0);

              csv += `'${r.no_rekening_lama}\t${r.nama_nasabah}\t${r.nama_produk || '-'}\t${alamat}\t'${hp}\t${kankas}\t${r.nama_ao}\t${Math.round(r.plafond_lama||0)}\t${sisaBdEx}\t${r.tgl_jatuh_tempo}\t${r.keterangan_status}\t${Math.round(r.plafond_baru||0)}\t${r.tgl_realisasi_baru||'-'}\n`;
          });

          const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `Detail_JT_${currentDetailParams.kode_kantor}_${currentDetailParams.bulan}.xls`;
          document.body.appendChild(a); a.click(); document.body.removeChild(a);

      } catch(e) { alert("Gagal export."); } finally { btn.innerHTML = txt; btn.disabled = false; }
  }

  window.changePageDetail = (step) => { const n = currentDetailPage + step; if (n > 0 && n <= currentDetailTotalPages) loadDetailPage(n); }
  window.closeModalJT = () => {
      const modal = document.getElementById('modalDetailJT');
      modal.classList.add('hidden'); modal.classList.remove('flex');
  }
  document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModalJT(); });
</script>

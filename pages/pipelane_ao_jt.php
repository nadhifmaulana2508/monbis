<style>
  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  
  /* Sembunyikan Scrollbar Filter di Mobile */
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  /* Animasi Modal */
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* ========================================================
     CSS MAGIC STICKY TABLE REKAP UTAMA (Disesuaikan Font Besar)
     ======================================================== */
  #tabelPipeline thead th { position: sticky; box-shadow: inset 0 -1px 0 #cbd5e1; }
  
  /* Lapis 1 (Header Utama) */
  #tabelPipeline thead tr:nth-child(1) th { top: 0; z-index: 40; height: 50px; }
  
  /* Lapis 2 (Sub-Header Lunas/Topup/Retensi) */
  #tabelPipeline thead tr:nth-child(2) th { top: 50px; z-index: 39; height: 42px; }
  
  /* Lapis 3 (Grand Total - Biru Soft) */
  #tabelPipeline thead tr:nth-child(3) th { 
      top: 92px; z-index: 38; height: 56px; 
      background-color: #dbeafe !important; 
      border-bottom: 2px solid #bfdbfe;
      box-shadow: inset 0 -1px 0 #93c5fd;
  }

  /* Freeze Kolom Kiri Rekap */
  .sticky-left-1 { position: sticky; left: 0; z-index: 20; background: white; box-shadow: inset -1px 0 0 #e2e8f0; }
  .sticky-left-2 { position: sticky; left: 0; z-index: 20; background: white; box-shadow: inset -1px 0 0 #e2e8f0; }
  
  @media (min-width: 640px) { .sticky-left-2 { left: 82px; } } 

  /* Z-Index Header Freeze Kiri Rekap */
  #tabelPipeline thead tr:nth-child(1) th.sticky-left-1 { z-index: 50; box-shadow: inset -1px -1px 0 #cbd5e1; background-color: #f1f5f9; }
  #tabelPipeline thead tr:nth-child(1) th.sticky-left-2 { z-index: 49; box-shadow: inset -1px -1px 0 #cbd5e1; background-color: #f1f5f9; }
  #tabelPipeline thead tr:nth-child(3) th.sticky-left-1 { z-index: 48; background-color: #bfdbfe !important; box-shadow: inset -1px -2px 0 #93c5fd; }
  #tabelPipeline thead tr:nth-child(3) th.sticky-left-2 { z-index: 47; background-color: #bfdbfe !important; box-shadow: inset -1px -2px 0 #93c5fd; }

  /* Hover Body Rekap */
  #bodyRekap tr:hover td { background-color: #eff6ff !important; cursor: pointer; }
  #bodyRekap tr:hover td.sticky-left-1, #bodyRekap tr:hover td.sticky-left-2 { background-color: #eff6ff !important; }

  /* ========================================================
     CSS MAGIC STICKY MODAL DETAIL
     ======================================================== */
  #tableExportModal thead th { position: sticky; box-shadow: inset 0 -1px 0 #cbd5e1; }
  #tableExportModal thead tr:nth-child(1) th { top: 0; z-index: 40; height: 46px; background-color: #f1f5f9; }
  #tableExportModal thead tr:nth-child(2) th { top: 46px; z-index: 39; height: 42px; background-color: #dbeafe !important; box-shadow: inset 0 -1px 0 #93c5fd; border-bottom: 2px solid #bfdbfe;}

  /* Freeze Kiri Modal */
  .mod-sticky-1 { position: sticky; left: 0; z-index: 20; background: white; box-shadow: inset -1px 0 0 #e2e8f0; }
  .mod-sticky-2 { position: sticky; left: 100px; z-index: 20; background: white; box-shadow: inset -1px 0 0 #e2e8f0; }

  #tableExportModal thead tr:nth-child(1) th.mod-sticky-1 { z-index: 50; background-color: #e2e8f0; box-shadow: inset -1px -1px 0 #cbd5e1; }
  #tableExportModal thead tr:nth-child(1) th.mod-sticky-2 { z-index: 49; background-color: #e2e8f0; box-shadow: inset -1px -1px 0 #cbd5e1; }
  #tableExportModal thead tr:nth-child(2) th.mod-sticky-1 { z-index: 48; background-color: #bfdbfe !important; box-shadow: inset -1px -1px 0 #93c5fd; }
  #tableExportModal thead tr:nth-child(2) th.mod-sticky-2 { z-index: 47; background-color: #bfdbfe !important; box-shadow: inset -1px -1px 0 #93c5fd; }

  #bodyDetail tr:hover td { background-color: #f8fafc !important; }
  #bodyDetail tr:hover td.mod-sticky-1, #bodyDetail tr:hover td.mod-sticky-2 { background-color: #f8fafc !important; }

  /* Form Inp */
  .inp { border:1px solid #cbd5e1; border-radius:8px; padding:0 12px; background:#fff; outline:none; transition: border 0.2s;}
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .lbl { font-size:11px; color:#475569; font-weight:800; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap;}
  .field { display:flex; flex-direction:column; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }

  /* Layout konsisten dengan Jatuh Tempo Kredit */
  #recomPipelanePage,
  #recomPipelanePage * { box-sizing:border-box; font-family:'Roboto',Arial,system-ui,sans-serif; }
  #recomPipelanePage { height:calc(100dvh - 64px); min-height:430px; padding:8px; gap:7px; background:#f8fafc; }
  #recomPipelaneHeader { position:relative; margin:0 !important; padding:9px 11px; gap:12px; border:1px solid #dbe3ee; border-radius:12px; background:#fff; box-shadow:0 1px 3px rgba(15,23,42,.05); }
  #recomPipelaneHeader h1 { color:#172033; letter-spacing:-.015em; }
  #recomPipelaneHeader .recom-title-copy { min-width:0; }
  #recomPipelaneHeader .recom-title-subtitle { margin:2px 0 0; color:#64748b; font-size:9px; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .recom-info-btn { display:inline-flex; align-items:center; justify-content:center; width:20px; min-width:20px; height:20px; padding:0; border:1px solid #bfdbfe; border-radius:999px; background:#eff6ff; color:#2563eb; font-size:11px; font-weight:900; line-height:1; cursor:pointer; transition:.16s ease; }
  .recom-info-btn:hover { background:#2563eb; color:#fff; border-color:#2563eb; }
  #recomPipelanePage > #recomPipelaneTableCard { flex:1; min-height:0; margin:0; border:1px solid #e2e8f0; border-radius:9px; background:#fff; box-shadow:0 1px 2px rgba(15,23,42,.04); }
  #recomPipelaneTableScroll { height:100%; overflow:auto; -webkit-overflow-scrolling:touch; }
  #tabelPipeline { min-width:1180px; font-size:10px; font-variant-numeric:tabular-nums; }
  #tabelPipeline th { color:#1e3a5f !important; font-weight:900 !important; letter-spacing:.025em; background:#f4f7fb !important; border-color:#d7dee8 !important; }
  #tabelPipeline thead tr:first-child th { background:#eaf4ff !important; }
  #tabelPipeline thead tr:nth-child(2) th { background:#eff6ff !important; }
  #tabelPipeline tbody td { border-color:#edf2f7 !important; background-clip:padding-box; }
  #tabelPipeline tbody tr:nth-child(even) td { background:#fbfdff; }
  #tabelPipeline tbody tr:hover td { background:#f0f7ff !important; }
  #rowTotalPipelineAtas th { background:#eff6ff !important; color:#1e40af !important; }
  #recomPipelanePage .custom-scrollbar { scrollbar-width:thin; scrollbar-color:#cbd5e1 #f8fafc; }
  #recomPipelanePage .custom-scrollbar::-webkit-scrollbar { height:4px; width:4px; }
  #recomPipelanePage .custom-scrollbar::-webkit-scrollbar-track { background:#f8fafc; border-radius:999px; }
  #recomPipelanePage .custom-scrollbar::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  #modalDetail { padding:12px; background:rgba(15,23,42,.68); backdrop-filter:blur(7px); }
  #modalDetail > .relative { width:min(1760px,calc(100vw - 24px)); height:min(94dvh,920px); max-width:none; border:1px solid #dbe3ee; border-radius:16px; }
  #tableExportModal { width:max-content; min-width:1560px; table-layout:fixed; }
  #tableExportModal th { height:36px !important; padding:5px 7px !important; background:#f8fafc !important; color:#64748b !important; font-size:8px !important; }
  #tableExportModal td { height:36px; padding:5px 7px; font-size:9px; }
  #tableExportModal thead { position:relative; z-index:80; }
  #tableExportModal thead tr:first-child th { top:0 !important; z-index:80 !important; }
  #tableExportModal thead tr:nth-child(2) th { top:36px !important; z-index:79 !important; }
  #tableExportModal thead tr:first-child th.mod-sticky-1,
  #tableExportModal thead tr:first-child th.mod-sticky-2,
  #tableExportModal thead tr:first-child th.pipeline-mobile-identity { z-index:100 !important; }
  #tableExportModal thead tr:nth-child(2) th.mod-sticky-1,
  #tableExportModal thead tr:nth-child(2) th.mod-sticky-2,
  #tableExportModal thead tr:nth-child(2) th.pipeline-mobile-identity { z-index:99 !important; }
  #tableExportModal tbody td { position:relative; z-index:1; background:#fff; }
  #tableExportModal tbody td.mod-sticky-1,
  #tableExportModal tbody td.mod-sticky-2 { position:sticky; z-index:20; }
  .pipeline-mobile-identity { display:none; }
  @media (max-width:767px) {
      #recomPipelanePage { height:calc(100dvh - 54px); min-height:0; padding:4px; gap:4px; }
      #recomPipelaneHeader { padding:7px 8px; border-radius:9px; }
      #recomPipelaneHeader h1 { font-size:13px; }
      #recomPipelaneHeader .recom-title-subtitle { max-width:230px; font-size:8px; }
      #recomPipelaneHeader .recom-info-btn { width:18px; min-width:18px; height:18px; font-size:10px; }
      #tabelPipeline { width:1180px; min-width:1180px; }
      #tabelPipeline th,#tabelPipeline td { height:34px; padding:5px 6px; font-size:9px; }
      #tabelPipeline thead th { font-size:7px !important; }
      #tabelPipeline .sticky-left-1 { display:none !important; }
      #tabelPipeline .sticky-left-2 { left:0 !important; width:135px; min-width:135px; max-width:135px; white-space:normal; line-height:1.1; }
      #tabelPipeline thead tr:nth-child(1) th.sticky-left-2 { z-index:70; }
      #tabelPipeline thead tr:nth-child(3) th.sticky-left-2 { z-index:69; }
      #modalDetail { padding:0; align-items:flex-end; }
      #modalDetail > .relative { width:100%; height:96dvh; max-height:96dvh; border-radius:16px 16px 0 0; }
      #tableExportModal { min-width:0; }
      #tableExportModal .pipeline-desktop-identity { display:none !important; }
      #tableExportModal .pipeline-mobile-identity {
          display:table-cell !important;
          position:sticky;
          left:0;
          z-index:20;
          width:145px;
          min-width:145px;
          max-width:145px;
          background:#fff;
          box-shadow:inset -1px 0 0 #e2e8f0;
      }
      #tableExportModal thead tr:first-child th.pipeline-mobile-identity { z-index:50; background:#e2e8f0 !important; }
      #tableExportModal thead tr:nth-child(2) th.pipeline-mobile-identity { z-index:48; background:#bfdbfe !important; }
      #tableExportModal .pipeline-mobile-account,
      #tableExportModal .pipeline-mobile-name { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; line-height:1.15; }
      #tableExportModal .pipeline-mobile-account { color:#64748b; font:700 8px/1.15 ui-monospace,SFMono-Regular,Menlo,monospace; }
      #tableExportModal .pipeline-mobile-name { margin-top:2px; color:#334155; font-size:9px; font-weight:800; }
  }

  /* Pipeline mengikuti shell visual Jatuh Tempo Kredit */
  #recomPipelanePage { min-height:0; padding-top:10px; padding-bottom:12px; }
  #recomPipelaneHeader {
      position:relative;
      padding:10px 12px;
      margin:0 0 12px !important;
      background:#fff;
      border:1px solid #e2e8f0;
      border-radius:12px;
      box-shadow:0 1px 3px rgba(15,23,42,.05);
  }
  #recomPipelaneHeader h1 { color:#172033; letter-spacing:-.015em; }
  #recomPipelaneHeader .recom-title-copy { min-width:0; }
  #recomPipelaneHeader .recom-title-subtitle { margin:2px 0 0; color:#64748b; font-style:normal; font-size:9px; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  #recomPipelaneHeader .recom-info-btn { box-shadow:none; }
  #recomPipelaneHeader .btn-icon,
  #recomPipelaneHeader .jt-breakdown-toggle,
  #recomPipelaneHeader .pipeline-kolek-toggle { transition:transform .16s ease, box-shadow .16s ease, background-color .16s ease; }
  #recomPipelaneHeader .btn-icon:hover,
  #recomPipelaneHeader .jt-breakdown-toggle:hover,
  #recomPipelaneHeader .pipeline-kolek-toggle:hover { transform:translateY(-1px); box-shadow:0 6px 14px rgba(15,23,42,.12); }
  #recomPipelaneHeader .jt-breakdown-toggle,
  #recomPipelaneHeader .pipeline-kolek-toggle {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:34px;
      height:34px;
      padding:0;
      border:1px solid #bfdbfe;
      border-radius:8px;
      background:#eff6ff;
      color:#1d4ed8;
      cursor:pointer;
  }
  #recomPipelaneHeader .pipeline-kolek-toggle {
      border-color:#bbf7d0;
      background:#f0fdf4;
      color:#047857;
  }
  #recomPipelaneHeader .jt-breakdown-toggle svg,
  #recomPipelaneHeader .pipeline-kolek-toggle svg { width:15px; height:15px; flex:0 0 auto; }
  #recomPipelaneHeader .pipeline-kolek-wrap { position:relative; }
  #recomPipelaneHeader .pipeline-kolek-menu {
      position:absolute;
      z-index:120;
      top:calc(100% + 8px);
      right:0;
      width:172px;
      padding:9px;
      border:1px solid #dbe3ee;
      border-radius:10px;
      background:#fff;
      box-shadow:0 12px 28px rgba(15,23,42,.16);
  }
  #recomPipelaneHeader .pipeline-kolek-menu.hidden { display:none !important; }
  #recomPipelaneHeader .pipeline-kolek-menu__title { margin-bottom:6px; color:#1e3a5f; font-size:9px; font-weight:900; letter-spacing:.05em; text-transform:uppercase; }
  #recomPipelaneHeader .pipeline-kolek-menu__option { display:flex; align-items:center; gap:7px; min-height:28px; padding:4px 5px; border-radius:6px; color:#334155; font-size:11px; font-weight:700; cursor:pointer; }
  #recomPipelaneHeader .pipeline-kolek-menu__option:hover { background:#f8fafc; }
  #recomPipelaneHeader .pipeline-kolek-menu__option input { width:14px; height:14px; margin:0; accent-color:#059669; }
  #recomPipelaneHeader .pipeline-kolek-menu__option input:disabled { opacity:.75; cursor:not-allowed; }
  #recomPipelanePage > #recomPipelaneTableCard {
      flex:1;
      min-height:0;
      border:1px solid #e2e8f0;
      border-radius:12px;
      background:#fff;
      box-shadow:0 1px 2px rgba(15,23,42,.04);
  }
  #recomPipelaneTableScroll { height:100%; overflow:auto; -webkit-overflow-scrolling:touch; }
  #tabelPipeline {
      width:100%;
      min-width:0;
      table-layout:fixed;
      border-collapse:separate;
      border-spacing:0;
      font-size:11px;
      font-variant-numeric:tabular-nums;
  }
  #tabelPipeline th,
  #tabelPipeline td { height:38px; padding:6px 8px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; vertical-align:middle; font-size:11px; }
  #tabelPipeline thead th { background:#f1f5f9 !important; color:#475569 !important; font-size:9px !important; font-weight:900 !important; letter-spacing:.035em; text-transform:uppercase; border-right:1px solid #dbe3ee !important; border-bottom:1px solid #cbd5e1 !important; }
  #tabelPipeline thead tr:first-child th { top:0; height:30px; background:#f1f5f9 !important; }
  #tabelPipeline thead tr:nth-child(2) th { top:30px; height:30px; background:#eff6ff !important; }
  #tabelPipeline thead tr:nth-child(3) th { top:60px; height:30px; z-index:38; background:#eff6ff !important; }
  #tabelPipeline thead tr:nth-child(4) th { top:90px; height:38px; z-index:38; background:#eff6ff !important; }
  #tabelPipeline tbody td { border-right:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; color:#334155; background-clip:padding-box; }
  #tabelPipeline tbody tr:nth-child(even) td { background:#fbfdff; }
  #tabelPipeline tbody tr:hover td,
  #tabelPipeline tbody tr:hover td.sticky-left-1,
  #tabelPipeline tbody tr:hover td.sticky-left-2 { background:#eff6ff !important; }
  #tabelPipeline .sticky-left-1 { width:82px; min-width:82px; max-width:82px; }
  #tabelPipeline .sticky-left-2 { width:180px; min-width:180px; max-width:180px; }
  #tabelPipeline .sticky-left-2 { left:68px; }
  #rowTotalPipelineAtas th { top:90px; height:38px; background:#eff6ff !important; color:#1e40af !important; border-bottom:1px solid #bfdbfe !important; }
  #rowTotalPipelineAtas th:not(.sticky-left-1):not(.sticky-left-2) { font-weight:500 !important; }
  #rowTotalPipelineAtas th.sticky-left-1,
  #rowTotalPipelineAtas th.sticky-left-2 { z-index:59; background:#eff6ff !important; }
  #rowTotalPipelineAtas.pipeline-total-clickable { cursor:pointer; }
  #rowTotalPipelineAtas.pipeline-total-clickable:hover th { filter:brightness(.98); }
  #rowTotalPipelineAtas .pipeline-total-detail-clickable { cursor:pointer; }
  #rowTotalPipelineAtas .pipeline-total-detail-clickable:hover { text-decoration:underline; text-underline-offset:2px; filter:brightness(.96); }
  #tabelPipeline thead tr:first-child th.sticky-left-1,
  #tabelPipeline thead tr:first-child th.sticky-left-2 { z-index:70; background:#f1f5f9 !important; }
  #tabelPipeline thead tr:nth-child(2) th.sticky-left-1,
  #tabelPipeline thead tr:nth-child(2) th.sticky-left-2 { z-index:69; background:#eff6ff !important; }
  #tabelPipeline thead th.pipeline-head-potensi { background:#eff6ff !important; color:#1d4ed8 !important; }
  #tabelPipeline thead th.pipeline-head-refi { background:#ecfdf5 !important; color:#047857 !important; }
  #tabelPipeline thead th.pipeline-head-percent { background:#f8fafc !important; color:#475569 !important; width:90px; min-width:90px; }
  #tabelPipeline thead th.pipeline-head-lunas { background:#f0f9ff !important; color:#0369a1 !important; }
  #tabelPipeline thead th.pipeline-head-belum { background:#fff7ed !important; color:#c2410c !important; }
  #tabelPipeline thead th.pipeline-head-rekom { background:#eaf4ff !important; color:#1e3a5f !important; }
  #tabelPipeline thead th.pipeline-head-top { background:#f5f3ff !important; color:#6d28a9 !important; }
  #tabelPipeline thead th.pipeline-head-ret { background:#fff7ed !important; color:#c2410c !important; }
  #tabelPipeline thead th.pipeline-head-drop { background:#fff1f2 !important; color:#be123c !important; }
  #tabelPipeline .pipeline-noa-col {
      width:32px;
      min-width:32px;
      max-width:32px;
      padding-left:2px !important;
      padding-right:2px !important;
  }
  #tabelPipeline .pipeline-amount-col {
      width:100px;
      min-width:100px;
      max-width:100px;
  }
  #tabelPipeline .pipeline-percent-col {
      width:60px;
      min-width:60px;
      max-width:60px;
  }
  #tabelPipeline .pipeline-noa-col,
  #tabelPipeline .pipeline-amount-col,
  #tabelPipeline .pipeline-percent-col {
      text-align:center !important;
  }
  #tabelPipeline .pipeline-cell-potensi { color:#1e3a5f; background:#fff !important; }
  #tabelPipeline .pipeline-cell-refi { color:#047857; background:#ecfdf5 !important; }
  #tabelPipeline .pipeline-cell-percent { color:#c2410c; background:#fff !important; }
  #tabelPipeline .pipeline-cell-lunas { color:#0369a1; background:#f0f9ff !important; }
  #tabelPipeline .pipeline-cell-belum { color:#c2410c; background:#fff7ed !important; }
  #tabelPipeline .pipeline-cell-top,
  #tabelPipeline .pipeline-total-top { color:#6d28d9 !important; background:#f5f3ff !important; }
  #tabelPipeline .pipeline-cell-ret,
  #tabelPipeline .pipeline-total-ret { color:#c2410c !important; background:#fff7ed !important; }
  #tabelPipeline .pipeline-cell-drop,
  #tabelPipeline .pipeline-total-drop { color:#be123c !important; background:#fff1f2 !important; }
  #tabelPipeline .pipeline-total-potensi { color:#1e40af !important; background:#eff6ff !important; }
  #tabelPipeline .pipeline-total-refi { color:#047857 !important; background:#ecfdf5 !important; }
  #tabelPipeline .pipeline-total-percent { color:#c2410c !important; background:#eff6ff !important; }
  #tabelPipeline .pipeline-total-lunas { color:#0369a1 !important; background:#f0f9ff !important; }
  #tabelPipeline .pipeline-total-belum { color:#c2410c !important; background:#fff7ed !important; }
  #modalDetail { padding:12px; background:rgba(15,23,42,.68); backdrop-filter:blur(7px); }
  #modalDetail > .relative { width:min(1760px,calc(100vw - 24px)); height:min(94dvh,920px); max-width:none; border:1px solid #dbe3ee; border-radius:16px; }
  #pipelineModalHeader { background:#fff; border-color:#e2e8f0; }
  #pipelineModalHeader .btn-icon { transition:transform .16s ease, box-shadow .16s ease, background-color .16s ease; }
  #pipelineModalHeader .btn-icon:hover { transform:translateY(-1px); box-shadow:0 6px 14px rgba(15,23,42,.12); }
  #pipelineModalContent { background:#fff; padding:0 !important; isolation:isolate; overscroll-behavior:contain; -webkit-overflow-scrolling:touch; }
  @media (min-width:1024px) { #tabelPipeline { width:max-content; min-width:100%; } }
  @media (max-width:1023px) {
      #recomPipelaneTableScroll { overflow:auto; }
      #tabelPipeline { width:max-content; min-width:100%; }
  }
  @media (max-width:1279px) {
      #recomPipelaneHeader { align-items:stretch; flex-direction:column; }
      #recomPipelaneHeader > div:last-child { width:100%; justify-content:flex-end; }
      #tabelPipeline { width:max-content; min-width:100%; }
  }
  @media (max-width:767px) {
      #recomPipelanePage { height:calc(100dvh - 54px); min-height:0; padding:4px; gap:4px; }
      #recomPipelaneHeader { gap:7px; padding:7px 8px; margin-bottom:4px !important; border-radius:9px; }
      #recomPipelaneHeader > div:first-child { width:100%; }
      #recomPipelaneHeader > div:last-child { width:auto; align-self:flex-end; margin-top:-38px; }
      #recomPipelaneHeader h1 { font-size:13px; }
      #recomPipelaneHeader .recom-title-subtitle { max-width:220px; font-size:8px; }
      #recomPipelaneHeader .bg-blue-600 { width:31px; height:31px; padding:6px; border-radius:8px; }
      #recomPipelaneHeader .btn-icon,
      #recomPipelaneHeader .jt-breakdown-toggle,
      #recomPipelaneHeader .pipeline-kolek-toggle { width:34px; height:32px; border-radius:7px; }
      #tabelPipeline .sticky-left-1 { display:none !important; }
      #tabelPipeline .sticky-left-2 { left:0 !important; width:135px; min-width:135px; max-width:135px; white-space:normal; line-height:1.1; }
      #tabelPipeline thead tr:first-child th.sticky-left-2 { z-index:70; }
      #tabelPipeline thead tr:nth-child(2) th.sticky-left-2 { z-index:69; }
      #modalDetail { padding:0; align-items:flex-end; }
      #modalDetail > .relative { width:100%; height:96dvh; max-height:96dvh; border-radius:16px 16px 0 0; }
      #pipelineModalHeader { padding:8px; }
      #pipelineModalHeader > div:first-child { width:100%; min-width:0; }
      #pipelineModalHeader > div:last-child { width:100%; margin-top:0; display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr) minmax(0,1fr) 34px 34px; gap:5px; overflow:visible; }
      #pipelineModalHeader > div:last-child select { width:100%; min-width:0; }
      #pipelineModalHeader h3 { font-size:12px; }
      #pipelineModalHeader #detailSubTitle { font-size:9px; margin-left:0; }
  }
  #tabelPipeline .sticky-left-1.jt-office-name {
      width:180px;
      min-width:180px;
      max-width:180px;
  }
  #tabelPipeline .sticky-left-1:not(.jt-office-name) {
      width:68px;
      min-width:68px;
      max-width:68px;
  }
  @media (max-width:767px) {
      #tabelPipeline .sticky-left-1.jt-office-name {
          display:table-cell !important;
          left:0 !important;
          width:135px;
          min-width:135px;
          max-width:135px;
      }
  }

  /* Final sticky layer: one scroll container, opaque cells, stable offsets. */
  #recomPipelaneTableScroll {
      position:relative;
      isolation:isolate;
      overscroll-behavior:contain;
  }
  #tabelPipeline thead th { position:sticky !important; }
  #tabelPipeline tbody td.sticky-left-1,
  #tabelPipeline tbody td.sticky-left-2 {
      position:sticky !important;
      z-index:20 !important;
      background-color:#fff !important;
      background-clip:padding-box;
  }
  #tabelPipeline tbody tr:nth-child(even) td.sticky-left-1,
  #tabelPipeline tbody tr:nth-child(even) td.sticky-left-2 { background-color:#fbfdff !important; }
  #tabelPipeline tbody tr:hover td.sticky-left-1,
  #tabelPipeline tbody tr:hover td.sticky-left-2 { background-color:#eff6ff !important; }
  #tabelPipeline .sticky-left-1 { left:0 !important; }
  #tabelPipeline .sticky-left-2 { left:68px !important; }
  #tabelPipeline .sticky-left-1.jt-office-name { left:0 !important; }
  #tabelPipeline thead tr:first-child th.sticky-left-1,
  #tabelPipeline thead tr:first-child th.sticky-left-2 { z-index:70 !important; }
  #tabelPipeline thead tr:nth-child(2) th.sticky-left-1,
  #tabelPipeline thead tr:nth-child(2) th.sticky-left-2 { z-index:69 !important; }
  #rowTotalPipelineAtas th.sticky-left-1,
  #rowTotalPipelineAtas th.sticky-left-2 { z-index:60 !important; }
  #tableExportModal .mod-sticky-1 { left:0; }
  #tableExportModal .mod-sticky-2 { left:120px; }
  @media (min-width:768px) {
      #tableExportModal .mod-sticky-1 { width:120px; min-width:120px; max-width:120px; }
      #tableExportModal .mod-sticky-2 { width:280px; min-width:280px; max-width:280px; }
  }
  @media (max-width:767px) {
      #tabelPipeline .sticky-left-1:not(.jt-office-name) { display:none !important; }
      #tabelPipeline .sticky-left-2 { left:0 !important; width:135px; min-width:135px; max-width:135px; }
      #tabelPipeline .sticky-left-1.jt-office-name { display:table-cell !important; width:135px; min-width:135px; max-width:135px; }
      #tableExportModal .pipeline-mobile-identity { left:0 !important; width:145px; min-width:145px; max-width:145px; }
  }
</style>

<div id="recomPipelanePage" class="pipeline-page jt-page max-w-[1920px] mx-auto px-2 md:px-4 py-3 md:py-6 h-[calc(100vh-80px)] flex flex-col bg-slate-50 font-sans text-slate-800 overflow-hidden">
  
  <div id="recomPipelaneHeader" class="pipeline-header-card jt-header-card flex-none mb-3 md:mb-4 flex flex-col xl:flex-row justify-between xl:items-center gap-3 md:gap-4 w-full">
      
      <div class="recom-title-copy jt-title-copy flex flex-col gap-1 shrink-0">
          <h1 class="text-lg md:text-2xl font-bold text-slate-800 flex items-center gap-2 mb-0.5">
              <span class="p-1.5 md:p-2 bg-blue-600 rounded-lg text-white shadow-sm">
                  <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
              </span>
              <span>Pipelane Kredit</span>
              <button type="button" onclick="openInfoModal()" class="recom-info-btn jt-info-btn" title="Informasi Status" aria-label="Buka informasi status pipeline">i
              </button>
          </h1>
          <p class="recom-title-subtitle jt-title-subtitle">Rekomendasi kredit jatuh tempo, refinancing, dan potensi penyelesaian berdasarkan periode terpilih.</p>
          
      </div>

      <form id="legacyPipelineFilter" hidden class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-wrap md:flex-nowrap items-end gap-2 md:gap-3 w-full xl:w-auto shrink-0 xl:ml-auto overflow-x-auto no-scrollbar" onsubmit="event.preventDefault(); fetchRekap();">
          <input type="hidden" id="closing_date" disabled>
          
          <div class="field shrink-0 w-[130px] md:w-[150px]">
              <label class="lbl">POSISI (ACTUAL)</label>
              <input type="date" id="legacy_harian_date" class="inp text-sm font-semibold h-[38px] text-slate-700 bg-slate-50 cursor-not-allowed" readonly required>
          </div>
          
          <div class="field shrink-0 w-[100px] md:w-[120px]">
              <label class="lbl">TAHUN JT</label>
              <input type="number" id="legacy_tahun_jt" class="inp text-sm font-semibold h-[38px] text-slate-700" value="<?= (int) date('Y') ?>" required>
          </div>
          
          <div class="flex items-center gap-1.5 shrink-0 h-[38px] mb-px">
              <button type="submit" class="btn-icon bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 md:px-5 h-full shadow-sm" title="Cari Data">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                  <span class="ml-1.5 text-sm font-bold uppercase tracking-wider hidden sm:inline">CARI</span>
              </button>
              <button type="button" onclick="exportExcelRekapPipeline()" class="btn-icon bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-3 md:px-4 h-full shadow-sm" title="Download Excel">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              </button>
          </div>
      </form>
      <div class="flex items-center gap-2 shrink-0 xl:ml-auto">
          <button type="button" id="recomPipelaneBreakdownToggle" onclick="togglePipelineBreakdown()" class="btn-icon jt-breakdown-toggle hidden" title="Ganti tampilan breakdown" aria-label="Ganti tampilan breakdown">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 7h11"></path><path d="m14 3 4 4-4 4"></path><path d="M17 17H6"></path><path d="m10 13-4 4 4 4"></path></svg>
          </button>
          <div class="pipeline-kolek-wrap">
              <button type="button" id="recomPipelaneKolekToggle" class="btn-icon pipeline-kolek-toggle" onclick="togglePipelineKolekMenu(event)" title="Filter kolektibilitas" aria-label="Filter kolektibilitas" aria-expanded="false">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 6 1.5 1.5L8 5"></path><path d="M11 6h9"></path><path d="m4 12 1.5 1.5L8 11"></path><path d="M11 12h9"></path><path d="m4 18 1.5 1.5L8 17"></path><path d="M11 18h9"></path></svg>
              </button>
              <div id="recomPipelaneKolekMenu" class="pipeline-kolek-menu hidden" role="group" aria-label="Filter kolektibilitas">
                  <div class="pipeline-kolek-menu__title">Kolektibilitas</div>
                  <label class="pipeline-kolek-menu__option"><input type="checkbox" value="L" checked disabled> <span>L (wajib)</span></label>
                  <label class="pipeline-kolek-menu__option"><input type="checkbox" id="recomPipelaneKolekDP" value="DP" onchange="handlePipelineKolekChange()"> <span>DP</span></label>
              </div>
          </div>
          <button type="button" onclick="exportExcelRekapPipeline()" class="btn-icon jt-download-btn bg-emerald-600 hover:bg-emerald-700 text-white h-[34px] md:h-[38px] w-[34px] md:w-[38px] rounded-lg shadow-sm" title="Download Excel" aria-label="Download Excel">
              <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          </button>
      </div>
  </div>

  <div id="recomPipelaneTableCard" class="pipeline-table-wrapper jt-table-wrapper flex-1 min-h-0 relative">
    <div id="loadingRekap" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
        <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent mb-3"></div>
        <span class="text-sm font-bold uppercase tracking-widest">Menyiapkan Pipeline...</span>
    </div>
    
    <div id="recomPipelaneTableScroll" class="h-full overflow-auto custom-scrollbar relative">
       <table class="w-max min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelPipeline">
         <colgroup id="pipelineColGroup"></colgroup>
        <thead class="tracking-wider bg-slate-50 text-slate-800 font-bold text-xs md:text-sm" id="headPipeline">
            </thead>
        <tbody id="bodyRekap" class="divide-y divide-slate-100 bg-white"></tbody>
      </table>
    </div>
  </div>

</div>

<div id="infoModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeInfoModal()"></div>
    <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up border border-slate-200">
        <div class="flex justify-between items-center px-5 py-4 border-b bg-slate-50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <span class="w-1.5 h-5 bg-blue-600 rounded-full"></span>
                Panduan Status Pipeline
            </h3>
            <button onclick="closeInfoModal()" class="text-slate-400 hover:text-red-500 transition text-2xl leading-none">&times;</button>
        </div>
        <div class="p-5 space-y-4 text-sm text-slate-600 bg-white">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 px-2 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded text-xs font-bold w-[80px] text-center shrink-0">LUNAS</span>
                <p>Nasabah yang telah lunas. Anda dapat melanjutkan aksi <strong>PROSPEK</strong> pada nasabah ini.</p>
            </div>
            <div class="flex items-start gap-3">
                <span class="mt-0.5 px-2 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded text-xs font-bold w-[80px] text-center shrink-0">TOP UP</span>
                <p>Nasabah berpotensi dengan proporsi sisa Baki Debet (BD) <strong>&le; 50%</strong>.</p>
            </div>
            <div class="flex items-start gap-3">
                <span class="mt-0.5 px-2 py-1 bg-orange-50 text-orange-700 border border-orange-200 rounded text-xs font-bold w-[80px] text-center shrink-0">RETENSI</span>
                <p>Nasabah yang masih berjalan dengan sisa Baki Debet (BD) <strong>&gt; 50%</strong>.</p>
            </div>
        </div>
        <div class="px-5 py-3 border-t bg-slate-50 flex justify-end">
            <button onclick="closeInfoModal()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-sm transition">Mengerti</button>
        </div>
    </div>
</div>

<div id="modalDetail" class="fixed inset-0 z-[9999] hidden items-end md:items-center justify-center p-2 md:p-4">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
  
  <div class="relative bg-white w-full h-[95vh] md:h-[92vh] max-w-[1700px] rounded-t-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up">
    
    <div id="pipelineModalHeader" class="pipeline-modal-header jt-modal-header flex justify-between items-center px-3 py-3 md:px-5 md:py-4 border-b bg-slate-50 shrink-0 flex-wrap gap-2">
        <div class="flex-1 min-w-[250px]">
            <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm md:text-base">
                <span class="w-2 h-6 bg-blue-600 rounded-full hidden md:block"></span> 
                Detail Nasabah Pipeline 
            </h3>
            <p class="text-[10px] md:text-xs text-slate-500 mt-0.5 ml-1 md:ml-8 font-mono" id="detailSubTitle">...</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-1.5 ml-auto shrink-0 w-full sm:w-auto mt-2 sm:mt-0 overflow-x-auto no-scrollbar">
            <select id="filter_kankas_modal" class="inp px-2 md:px-3 h-[34px] md:h-10 flex-1 sm:w-[160px] text-xs md:text-sm font-bold text-blue-800 bg-blue-50 outline-none shrink-0 cursor-pointer" onchange="changeFilter()">
                <option value="">Semua Kankas</option>
            </select>
            <select id="filter_status_modal" class="inp px-2 md:px-3 h-[34px] md:h-10 flex-1 sm:w-[160px] text-xs md:text-sm font-bold text-blue-800 bg-blue-50 outline-none shrink-0 cursor-pointer" onchange="changeFilter()">
                <option value="">Semua Status</option>
                <option value="sudah">✅ Sudah Ambil</option>
                <option value="lunas">🔵 Lunas</option>
                <option value="topup">🟣 BD <= 50%</option>
                <option value="retensi">🟠 BD > 50%</option>
                <option value="drop">⛔ Drop</option>
            </select>
            <select id="filter_ao_modal" class="inp px-2 md:px-3 h-[34px] md:h-10 flex-1 sm:w-[160px] text-xs md:text-sm font-bold text-slate-700 bg-white outline-none shrink-0 cursor-pointer" onchange="changeFilter()">
                <option value="">Semua AO</option>
            </select>

            <button onclick="downloadExcelDetail(event)" class="btn-icon bg-emerald-600 hover:bg-emerald-700 text-white w-[34px] md:w-10 h-[34px] md:h-10 rounded-lg shadow-sm shrink-0" title="Download Excel" aria-label="Download Excel">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </button>
            <button onclick="closeModal()" class="w-[34px] md:w-10 h-[34px] md:h-10 flex items-center justify-center rounded-xl bg-slate-200 hover:bg-red-500 hover:text-white text-slate-600 transition font-bold text-xl md:text-2xl leading-none shrink-0">&times;</button>
        </div>
    </div>

    <div id="modalStats" class="bg-slate-100 border-b border-slate-200 px-4 py-3 text-xs md:text-sm font-mono font-medium text-slate-600 overflow-x-auto no-scrollbar whitespace-nowrap shrink-0"></div>

    <div id="pipelineModalContent" class="pipeline-modal-content jt-modal-content flex-1 overflow-auto bg-slate-50 relative custom-scrollbar p-0 md:p-3">
        <div id="loadingDetail" class="hidden absolute inset-0 bg-white/90 z-40 flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent mb-3"></div>
            <span class="text-sm font-bold uppercase tracking-widest">Memuat Detail...</span>
        </div>
        
        <table class="w-max min-w-full text-sm text-left text-slate-700 border border-slate-200 md:rounded-xl shadow-sm bg-white table-fixed" id="tableExportModal">
            <thead class="text-slate-600 font-bold uppercase tracking-wider text-[10px] md:text-xs">
                <tr>
                    <th class="pipeline-desktop-identity px-3 py-4 border-b border-r border-slate-300 w-[120px] mod-sticky-1 rounded-tl-xl text-blue-900 bg-[#f1f5f9]">REKENING</th>
                    <th class="pipeline-desktop-identity px-4 py-4 border-b border-r border-slate-300 w-[220px] md:w-[280px] mod-sticky-2 text-blue-900 bg-[#f1f5f9]">NAMA NASABAH</th>
                    <th class="pipeline-mobile-identity px-2 py-3 border-b border-r border-slate-300 rounded-tl-xl text-blue-900 bg-[#f1f5f9]">REKENING / NAMA NASABAH</th>
                    <th class="px-4 py-4 border-b border-r border-slate-300 w-[160px] md:w-[190px] text-blue-800">NAMA PRODUK</th>
                    <th class="px-4 py-4 border-b border-r border-slate-300 w-[200px] md:w-[250px]">ALAMAT</th>
                    <th class="px-3 py-4 border-b border-r border-slate-300 w-[120px] text-center">NO HP</th>
                    <th class="px-3 py-4 border-b border-r border-slate-300 w-[120px] text-center">KANKAS</th>
                    <th class="px-4 py-4 border-b border-r border-slate-300 w-[160px] text-blue-800">NAMA AO</th>
                    <th class="px-4 py-4 border-b border-r border-slate-300 w-[130px] text-right">PLAFON AWAL</th>
                    <th class="px-3 py-4 border-b border-r border-slate-300 w-[120px] text-center">TANGGAL JT</th>
                    <th class="px-4 py-4 border-b border-r border-blue-300 w-[130px] text-right bg-blue-50 text-blue-900">SISA OS</th>
                    <th class="px-4 py-4 border-b border-r border-slate-300 w-[140px] text-center">STATUS</th>
                    <th class="px-4 py-4 border-b border-r border-emerald-300 w-[140px] text-right bg-emerald-50 text-emerald-900">NOMINAL BARU</th>
                    <th class="px-3 py-4 border-b border-slate-300 w-[90px] text-center">AKSI</th>
                </tr>
                <tr id="rowTotalDetailAtas"></tr>
            </thead>
            <tbody id="bodyDetail" class="divide-y divide-slate-100 bg-white"></tbody>
        </table>
    </div>

    <div class="px-4 py-4 md:px-6 border-t bg-white flex justify-between items-center shrink-0">
        <span id="pageInfo" class="text-xs md:text-sm font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-lg">0 Data</span>
        <div class="flex gap-2">
            <button id="btnPrev" onclick="changePage(-1)" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">« Prev</button>
            <button id="btnNext" onclick="changePage(1)" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">Next »</button>
        </div>
    </div>
  </div>
</div>

<script>
  // --- CONFIG & GLOBAL VARS ---
  const API_URL  = './api/pipelane/'; 
  const API_KODE = './api/kode/'; 
  const API_DATE = './api/date/';
  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Math.round(Number(n||0)));

  let state = { cabang:'', kankas:'', ao:'', status:'', breakdown:'CABANG', kolektibilitas:['L'], page:1, limit:20, totalPages:1 };
  let abortRekap;
  let rekapDataCache = null; 
  let userKodeGlobal = '000'; 

  function pipelineAreaValue() {
      return String(document.getElementById('recomPipelaneKantorFilter')?.value || 'ALL').trim();
  }

  function normalizePipelineOffice(value) {
      const raw = String(value || '').trim().toUpperCase();
      if (!raw || raw === 'ALL' || raw === '000') return null;
      const code = raw.replace(/^CAB(?:ANG)?(?:-|:)/, '');
      return /^\d{1,3}$/.test(code) ? code.padStart(3, '0') : null;
  }

  function selectedPipelineKorwil() {
      const raw = pipelineAreaValue().toUpperCase();
      return raw.startsWith('KOR-') ? raw.slice(4) : null;
  }

  function selectedPipelineOffice() {
      const fromFilter = normalizePipelineOffice(pipelineAreaValue());
      if (fromFilter) return fromFilter;
      return userKodeGlobal !== '000' ? userKodeGlobal : null;
  }

  function isBranchPipelineScope() {
      return !!selectedPipelineOffice();
  }

  function getPipelineBreakdown() {
      if (!isBranchPipelineScope()) return 'CABANG';
      return state.breakdown === 'AO' ? 'AO' : 'KANKAS';
  }

  function updatePipelineBreakdownControl() {
      const button = document.getElementById('recomPipelaneBreakdownToggle');
      if (!button) return;
      const visible = isBranchPipelineScope();
      const current = getPipelineBreakdown();
      const next = current === 'KANKAS' ? 'AO' : 'KANKAS';
      button.classList.toggle('hidden', !visible);
      button.title = `Tampilan ${current === 'KANKAS' ? 'Per Kankas' : 'Per AO Kredit'} · klik untuk ${next === 'KANKAS' ? 'Per Kankas' : 'Per AO Kredit'}`;
      button.setAttribute('aria-label', button.title);
  }

  function updatePipelineKolekControl() {
      const button = document.getElementById('recomPipelaneKolekToggle');
      const includeDP = state.kolektibilitas.includes('DP');
      if (!button) return;
      const title = includeDP ? 'Kolektibilitas: L + DP' : 'Kolektibilitas: L';
      button.title = title;
      button.setAttribute('aria-label', title);
      button.setAttribute('aria-expanded', document.getElementById('recomPipelaneKolekMenu')?.classList.contains('hidden') ? 'false' : 'true');
      const dp = document.getElementById('recomPipelaneKolekDP');
      if (dp) dp.checked = includeDP;
  }

  window.togglePipelineKolekMenu = function(event) {
      event?.stopPropagation();
      const menu = document.getElementById('recomPipelaneKolekMenu');
      const button = document.getElementById('recomPipelaneKolekToggle');
      if (!menu || !button) return;
      const open = menu.classList.toggle('hidden') === false;
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
  };

  window.handlePipelineKolekChange = function() {
      const includeDP = !!document.getElementById('recomPipelaneKolekDP')?.checked;
      state.kolektibilitas = includeDP ? ['L', 'DP'] : ['L'];
      updatePipelineKolekControl();
      fetchRekap();
  };

  window.togglePipelineBreakdown = function() {
      if (!isBranchPipelineScope()) return;
      state.breakdown = getPipelineBreakdown() === 'KANKAS' ? 'AO' : 'KANKAS';
      updatePipelineBreakdownControl();
      setupHeaderPipeline(userKodeGlobal);
      fetchRekap();
  };

  async function populatePipelineAreaOptions(userKode) {
      const select = document.getElementById('recomPipelaneKantorFilter');
      if (!select) return;
      const code = String(userKode || '000').padStart(3, '0');
      if (code !== '000') {
          select.innerHTML = `<option value="CAB-${code}">${code}</option>`;
          select.value = `CAB-${code}`;
          select.disabled = true;
          return;
      }

      try {
          const json = await apiCall(API_KODE, { type: 'kode_kantor' });
          const list = Array.isArray(json.data) ? json.data : [];
          let html = '<option value="ALL">Konsolidasi</option>';
          ['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].forEach(korwil => {
              html += `<option value="KOR-${korwil}">Korwil ${korwil[0]}${korwil.slice(1).toLowerCase()}</option>`;
          });
          list.filter(item => String(item.kode_kantor || '') !== '000')
              .sort((a, b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor)))
              .forEach(item => {
                  const codeItem = String(item.kode_kantor).padStart(3, '0');
                  html += `<option value="CAB-${codeItem}">${codeItem} - ${item.nama_kantor || `Cabang ${codeItem}`}</option>`;
              });
          select.innerHTML = html;
          select.disabled = false;
          window.MonbisGlobalAreaFilter?.restore?.();
      } catch (error) {
          select.innerHTML = '<option value="ALL">Konsolidasi</option>';
      }
  }

  window.handleRecomPipelaneAreaChange = function() {
      state.breakdown = selectedPipelineOffice() ? 'KANKAS' : 'CABANG';
      updatePipelineBreakdownControl();
      setupHeaderPipeline(userKodeGlobal);
      if (document.getElementById('harian_date')?.value) fetchRekap();
      if (window.innerWidth < 768) setTimeout(() => toggleRecomPipelaneNavbarFilter(false), 180);
  };

  function toggleRecomPipelaneNavbarFilter(open) {
      const panel = document.getElementById('recomPipelaneNavbarFilterPanel');
      const toggle = document.getElementById('recomPipelaneNavbarFilterToggle');
      if (!panel) return;
      const shouldOpen = typeof open === 'boolean' ? open : panel.classList.contains('hidden');
      panel.classList.toggle('hidden', !shouldOpen);
      panel.classList.toggle('flex', shouldOpen);
      toggle?.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
  }

  function bindRecomPipelaneNavbarFilter() {
      const panel = document.getElementById('recomPipelaneNavbarFilterPanel');
      const toggle = document.getElementById('recomPipelaneNavbarFilterToggle');
      const close = document.getElementById('recomPipelaneNavbarFilterClose');
      if (!panel || !toggle || toggle.dataset.bound === '1') return;
      toggle.addEventListener('click', event => { event.stopPropagation(); toggleRecomPipelaneNavbarFilter(); });
      close?.addEventListener('click', () => toggleRecomPipelaneNavbarFilter(false));
      document.addEventListener('click', event => {
          if (!panel.contains(event.target) && !toggle.contains(event.target)) toggleRecomPipelaneNavbarFilter(false);
      });
      toggle.dataset.bound = '1';
  }

  // --- INIT ---
  window.addEventListener('DOMContentLoaded', async () => {
      const user = (window.getUser && window.getUser()) || null;
      userKodeGlobal = (user?.kode ? String(user.kode).padStart(3,'0') : '000');

      bindRecomPipelaneNavbarFilter();
      await populatePipelineAreaOptions(userKodeGlobal);
      state.breakdown = selectedPipelineOffice() ? 'KANKAS' : 'CABANG';
      updatePipelineBreakdownControl();
      updatePipelineKolekControl();
      document.addEventListener('click', event => {
          const wrap = document.querySelector('#recomPipelaneHeader .pipeline-kolek-wrap');
          const menu = document.getElementById('recomPipelaneKolekMenu');
          const button = document.getElementById('recomPipelaneKolekToggle');
          if (wrap && menu && button && !wrap.contains(event.target)) {
              menu.classList.add('hidden');
              button.setAttribute('aria-expanded', 'false');
          }
      });
      setupHeaderPipeline(userKodeGlobal);

      const now = new Date();
      const yearField = document.getElementById('tahun_jt');
      if (yearField && !yearField.value) yearField.value = String(now.getFullYear());
      try {
          const r = await fetch(API_DATE); const j = await r.json();
          const lastData = j && j.data ? j.data : {};
          document.getElementById('closing_date').value = lastData.last_closing || `${now.getFullYear() - 1}-12-31`;
          document.getElementById('harian_date').value = lastData.last_created || now.toISOString().split('T')[0];
      } catch(e) {
          document.getElementById('closing_date').value = `${now.getFullYear() - 1}-12-31`;
          document.getElementById('harian_date').value = now.toISOString().split('T')[0];
      }

      fetchRekap();
  });

  async function apiCall(url, payload, signal = null) {
      const opt = { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) };
      if (signal) opt.signal = signal;
      const res = await fetch(url, opt);
      return await res.json();
  }

  // --- SETUP HEADER REKAP UTAMA: mengikuti tabel Jatuh Tempo Kredit ---
  function setupHeaderPipeline(userKode) {
      const th = document.getElementById('headPipeline');
      const breakdown = getPipelineBreakdown();
      const showCode = userKode === '000' || breakdown !== 'CABANG';
      const codeTitle = breakdown === 'AO' ? 'Kode AO' : breakdown === 'KANKAS' ? 'Kode Kankas' : 'Kode';
      const nameTitle = breakdown === 'AO' ? 'Nama AO' : breakdown === 'KANKAS' ? 'Nama Kankas' : 'Nama Kantor';
      const colGroup = document.getElementById('pipelineColGroup');
      if (colGroup) {
          const codeHiddenOnMobile = showCode && window.innerWidth < 768;
          const widths = showCode && !codeHiddenOnMobile ? [68, 180] : [codeHiddenOnMobile ? 135 : 180];
          widths.push(32, 100, 32, 100, 60, 32, 100, 32, 100, 32, 100, 32, 100);
          colGroup.innerHTML = widths.map(width => `<col style="width:${width}px">`).join('');
      }
      const identity = showCode
          ? `<th rowspan="3" class="sticky-left-1 jt-code-col hidden sm:table-cell text-center">${codeTitle}</th><th rowspan="3" class="sticky-left-2 jt-grouped-name text-left">${nameTitle}</th>`
          : `<th rowspan="3" class="sticky-left-1 jt-office-name text-left">${nameTitle}</th>`;

      th.innerHTML = `
          <tr>
              ${identity}
              <th colspan="2" class="pipeline-head-potensi text-center border-r border-b">POTENSI KREDIT</th>
              <th colspan="3" class="pipeline-head-refi text-center border-r border-b">REFINANCING / TOP UP</th>
              <th colspan="6" class="pipeline-head-rekom text-center border-r border-b">REKOMENDASI PIPELINE KREDIT</th>
              <th colspan="2" class="pipeline-head-drop text-center border-b">DROP</th>
          </tr>
          <tr>
              <th rowspan="2" class="pipeline-head-potensi pipeline-noa-col text-center border-r border-b">NOA</th>
              <th rowspan="2" class="pipeline-head-potensi pipeline-amount-col text-center border-r border-b">PLAFON</th>
              <th rowspan="2" class="pipeline-head-refi pipeline-noa-col text-center border-r border-b">NOA</th>
              <th rowspan="2" class="pipeline-head-refi pipeline-amount-col text-right border-r border-b">PLAFON</th>
              <th rowspan="2" class="pipeline-head-refi pipeline-percent-col text-center border-r border-b">%</th>
              <th colspan="2" class="pipeline-head-lunas text-center border-r border-b">LUNAS</th>
              <th colspan="2" class="pipeline-head-top text-center border-r border-b">NOMINAL &lt;= 50%</th>
              <th colspan="2" class="pipeline-head-ret text-center border-r border-b">NOMINAL &gt; 50%</th>
              <th rowspan="2" class="pipeline-head-drop pipeline-noa-col text-center border-r border-b">NOA</th>
              <th rowspan="2" class="pipeline-head-drop pipeline-amount-col text-right border-b">NOMINAL</th>
          </tr>
          <tr>
              <th class="pipeline-head-lunas pipeline-noa-col text-center border-r border-b">NOA</th>
              <th class="pipeline-head-lunas pipeline-amount-col text-right border-r border-b">PLAFON</th>
              <th class="pipeline-head-top pipeline-noa-col text-center border-r border-b">NOA</th>
              <th class="pipeline-head-top pipeline-amount-col text-right border-r border-b">NOMINAL</th>
              <th class="pipeline-head-ret pipeline-noa-col text-center border-r border-b">NOA</th>
              <th class="pipeline-head-ret pipeline-amount-col text-right border-r border-b">NOMINAL</th>
          </tr>
          <tr id="rowTotalPipelineAtas"></tr>`;
  }

  // --- FETCH REKAP UTAMA ---
  async function fetchRekap() {
      const l = document.getElementById('loadingRekap');
      const tb = document.getElementById('bodyRekap');
      const trTot = document.getElementById('rowTotalPipelineAtas');
      
      if(abortRekap) abortRekap.abort();
      abortRekap = new AbortController();

      l.classList.remove('hidden');
      
      const showCode = userKodeGlobal === '000' || getPipelineBreakdown() !== 'CABANG';
      const colSpan = showCode ? 15 : 14;
      tb.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-20 text-slate-400 italic text-base">Sedang mengambil data...</td></tr>`;
      trTot.innerHTML = '';
      trTot.onclick = null;
      trTot.classList.remove('pipeline-total-clickable');
      rekapDataCache = null;

      try {
          const reqCabang = selectedPipelineOffice();

          const payload = {
              type: 'rekap_pipeline',
              closing_date: document.getElementById('closing_date').value,
              harian_date: document.getElementById('harian_date').value,
              tahun_jt: document.getElementById('tahun_jt').value,
              kode_kantor: reqCabang,
              korwil: selectedPipelineKorwil(),
              breakdown_by: getPipelineBreakdown(),
              kolektibilitas: state.kolektibilitas
          };

          const json = await apiCall(API_URL, payload, abortRekap.signal);
          let rows = json.data || [];

          if (reqCabang) {
              rows = rows.filter(r => String(r.kode_cabang).padStart(3, '0') === reqCabang);
          }

          if(rows.length === 0) {
              tb.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-20 text-slate-400 italic text-base">Tidak ada data.</td></tr>`;
              return;
          }
          rekapDataCache = rows; 

          let T = { tgt_noa:0, tgt_nom:0, sdh_noa:0, sdh_nom:0, lun_noa:0, lun_nom:0, top_noa:0, top_nom:0, ret_noa:0, ret_nom:0, drop_noa:0, drop_nom:0 };
          let html = '';
          const fmtOrDash = value => Number(value || 0) === 0 ? '-' : fmt(value);

          rows.forEach(r => {
              T.tgt_noa += +r.noa_target; T.tgt_nom += +r.plafon_closing;
              T.sdh_noa += +r.noa_sudah;  T.sdh_nom += +r.nominal_sudah;
              T.lun_noa += +r.noa_lunas;  T.lun_nom += +r.nominal_lunas;
              T.top_noa += +r.noa_topup;  T.top_nom += +r.os_topup;
              T.ret_noa += +r.noa_retensi; T.ret_nom += +r.os_retensi;
              T.drop_noa += +r.noa_drop;  T.drop_nom += +r.os_drop;

              const breakdown = getPipelineBreakdown();
              const isGroupBreakdown = breakdown !== 'CABANG';
              const namaK = isGroupBreakdown ? (r.group_label || r.group_code || '-') : (r.nama_kantor || r.kode_cabang);
              const kodeK = isGroupBreakdown ? (r.group_code || '-') : (r.kode_cabang || '-');
              const modalArgs = [r.kode_cabang || userKodeGlobal, namaK, breakdown, isGroupBreakdown ? (r.group_code || '') : '']
                  .map(value => JSON.stringify(String(value ?? ''))
                      .replace(/</g, '\\u003c')
                      .replace(/&/g, '&amp;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#39;'))
                  .join(', ');

              const divisorRefi = Number(r.plafon_closing || 0);
              const pctRefi = divisorRefi > 0 ? ((Number(r.nominal_sudah || 0) / divisorRefi) * 100).toFixed(2).replace('.', ',') : '0,00';
              const detailStatus = category => category === 'REFINANCING' ? 'sudah' : category === 'LUNAS' ? 'lunas' : category === 'TOPUP' ? 'topup' : category === 'RETENSI' ? 'retensi' : category === 'DROP' ? 'drop' : '';
              const detailClick = category => `onclick="event.stopPropagation(); openModal(${modalArgs}, '${detailStatus(category)}')"`;

              let rowHtml = `<tr onclick="openModal(${modalArgs})" class="transition h-[52px] group border-b border-slate-100 cursor-pointer">`;
              
              if (showCode) {
                  rowHtml += `
                    <td class="sticky-left-1 px-3 py-2 border-r border-slate-100 font-mono text-slate-500 text-center hidden sm:table-cell bg-white group-hover:bg-slate-50 shadow-[inset_-1px_0_0_#e2e8f0] z-20 text-sm">${kodeK}</td>
                    <td class="sticky-left-2 px-4 py-2 border-r border-slate-100 font-semibold text-slate-700 text-left truncate bg-white group-hover:bg-slate-50 shadow-[inset_-1px_0_0_#e2e8f0] z-20 text-sm" title="${namaK}">${namaK}</td>
                  `;
              } else {
                  rowHtml += `
                    <td class="sticky-left-1 jt-office-name px-4 py-2 border-r border-slate-100 font-semibold text-slate-700 text-left truncate bg-white group-hover:bg-slate-50 shadow-[inset_-1px_0_0_#e2e8f0] z-20 text-sm" title="${namaK}">${namaK}</td>
                  `;
              }

              rowHtml += `
                    <td class="pipeline-cell-potensi pipeline-noa-col text-center border-r">${fmtOrDash(r.noa_target)}</td>
                    <td class="pipeline-cell-potensi pipeline-amount-col text-right border-r">${fmtOrDash(r.plafon_closing)}</td>
                    <td ${detailClick('REFINANCING')} class="pipeline-cell-refi pipeline-noa-col jt-clickable text-center border-r">${fmtOrDash(r.noa_sudah)}</td>
                    <td ${detailClick('REFINANCING')} class="pipeline-cell-refi pipeline-amount-col jt-clickable text-right border-r">${fmtOrDash(r.nominal_sudah)}</td>
                    <td class="pipeline-cell-percent pipeline-percent-col text-center border-r">${pctRefi}%</td>
                    <td ${detailClick('LUNAS')} class="pipeline-cell-lunas pipeline-noa-col jt-clickable text-center border-r">${fmtOrDash(r.noa_lunas)}</td>
                    <td ${detailClick('LUNAS')} class="pipeline-cell-lunas pipeline-amount-col jt-clickable text-right border-r">${fmtOrDash(r.nominal_lunas)}</td>
                    <td ${detailClick('TOPUP')} class="pipeline-cell-top pipeline-noa-col jt-clickable text-center border-r">${fmtOrDash(r.noa_topup)}</td>
                    <td ${detailClick('TOPUP')} class="pipeline-cell-top pipeline-amount-col jt-clickable text-right border-r">${fmtOrDash(r.os_topup)}</td>
                    <td ${detailClick('RETENSI')} class="pipeline-cell-ret pipeline-noa-col jt-clickable text-center border-r">${fmtOrDash(r.noa_retensi)}</td>
                    <td ${detailClick('RETENSI')} class="pipeline-cell-ret pipeline-amount-col jt-clickable text-right border-r">${fmtOrDash(r.os_retensi)}</td>
                    <td ${detailClick('DROP')} class="pipeline-cell-drop pipeline-noa-col jt-clickable text-center border-r">${fmtOrDash(r.noa_drop)}</td>
                    <td ${detailClick('DROP')} class="pipeline-cell-drop pipeline-amount-col jt-clickable text-right">${fmtOrDash(r.os_drop)}</td>
                </tr>`;
              html += rowHtml;
          });
          tb.innerHTML = html;

          const pctTotal = T.tgt_nom > 0 ? ((T.sdh_nom / T.tgt_nom) * 100).toFixed(2).replace('.', ',') : '0,00';

          // Inject Grand Total ke Bawah Thead
          if (showCode) {
              trTot.innerHTML = `
                  <th class="sticky-left-1 px-3 border-r border-blue-200 text-center text-blue-900 hidden sm:table-cell bg-[#eff6ff]">-</th>
                  <th class="sticky-left-2 px-4 border-r border-blue-200 text-left text-blue-900 uppercase tracking-widest font-extrabold text-sm md:text-base bg-[#eff6ff]">TOTAL </th>
              `;
          } else {
              trTot.innerHTML = `
                  <th class="sticky-left-1 jt-office-name px-4 border-r border-blue-200 text-left text-blue-900 uppercase tracking-widest font-extrabold text-sm md:text-base bg-[#eff6ff]">TOTAL </th>
              `;
          }

          trTot.innerHTML += `
              <th class="pipeline-total-potensi pipeline-noa-col text-center border-r align-middle">${fmtOrDash(T.tgt_noa)}</th>
              <th class="pipeline-total-potensi pipeline-amount-col text-right border-r align-middle">${fmtOrDash(T.tgt_nom)}</th>
              <th class="pipeline-total-refi pipeline-noa-col text-center border-r align-middle">${fmtOrDash(T.sdh_noa)}</th>
              <th class="pipeline-total-refi pipeline-amount-col text-right border-r align-middle">${fmtOrDash(T.sdh_nom)}</th>
              <th class="pipeline-total-percent pipeline-percent-col text-center border-r align-middle">${pctTotal}%</th>
              <th class="pipeline-total-lunas pipeline-noa-col text-center border-r align-middle">${fmtOrDash(T.lun_noa)}</th>
              <th class="pipeline-total-lunas pipeline-amount-col text-right border-r align-middle">${fmtOrDash(T.lun_nom)}</th>
              <th class="pipeline-total-top pipeline-noa-col text-center border-r align-middle">${fmtOrDash(T.top_noa)}</th>
              <th class="pipeline-total-top pipeline-amount-col text-right border-r align-middle">${fmtOrDash(T.top_nom)}</th>
              <th class="pipeline-total-ret pipeline-noa-col text-center border-r align-middle">${fmtOrDash(T.ret_noa)}</th>
              <th class="pipeline-total-ret pipeline-amount-col text-right border-r align-middle">${fmtOrDash(T.ret_nom)}</th>
              <th class="pipeline-total-drop pipeline-noa-col text-center border-r align-middle">${fmtOrDash(T.drop_noa)}</th>
              <th class="pipeline-total-drop pipeline-amount-col text-right align-middle">${fmtOrDash(T.drop_nom)}</th>
          `;

          if (trTot.children.length > 0) {
              const totalScope = selectedPipelineOffice() || '';
              trTot.classList.add('pipeline-total-clickable');
              trTot.onclick = () => openModal(totalScope, 'TOTAL', 'CABANG', '', '');
              const bindTotalDetail = (indexes, status) => indexes.forEach(index => {
                  const cell = trTot.children[index];
                  if (!cell) return;
                  cell.classList.add('pipeline-total-detail-clickable');
                  cell.onclick = event => {
                      event.stopPropagation();
                      openModal(totalScope, 'TOTAL', 'CABANG', '', status);
                  };
              });
              const totalIdentityCols = showCode ? 2 : 1;
              bindTotalDetail([totalIdentityCols + 2, totalIdentityCols + 3], 'sudah');
              bindTotalDetail([totalIdentityCols + 5, totalIdentityCols + 6], 'lunas');
              bindTotalDetail([totalIdentityCols + 7, totalIdentityCols + 8], 'topup');
              bindTotalDetail([totalIdentityCols + 9, totalIdentityCols + 10], 'retensi');
              bindTotalDetail([totalIdentityCols + 11, totalIdentityCols + 12], 'drop');
          }

      } catch(e) { if(e.name!=='AbortError') console.error(e); } finally { l.classList.add('hidden'); }
  }

  // --- EXPORT EXCEL REKAP UTAMA ---
  window.exportExcelRekapPipeline = function() {
      if(!rekapDataCache || rekapDataCache.length === 0) return alert("Tidak ada data rekap untuk didownload.");

      const breakdown = getPipelineBreakdown();
      const codeTitle = breakdown === 'AO' ? 'Kode AO' : breakdown === 'KANKAS' ? 'Kode Kankas' : 'Kode';
      const nameTitle = breakdown === 'AO' ? 'Nama AO Kredit' : breakdown === 'KANKAS' ? 'Nama Kankas' : 'Nama Kantor';
      let csv = `${codeTitle}\t${nameTitle}\tPotensi Kredit NOA\tPotensi Kredit Plafon\tRefinancing / Top Up NOA\tRefinancing / Top Up Plafon\t%\tLunas NOA\tLunas Plafon\tNominal <= 50% NOA\tNominal <= 50% Nominal\tNominal > 50% NOA\tNominal > 50% Nominal\tDrop NOA\tDrop Plafon\n`;
      
      rekapDataCache.forEach(r => {
          const code = breakdown === 'CABANG' ? r.kode_cabang : (r.group_code || '');
          const name = breakdown === 'CABANG' ? (r.nama_kantor || '') : (r.group_label || code);
          const pct = Number(r.plafon_closing || 0) > 0 ? (Number(r.nominal_sudah || 0) / Number(r.plafon_closing) * 100).toFixed(2).replace('.', ',') + '%' : '0,00%';
          csv += `'${code}\t${name}\t${r.noa_target}\t${Math.round(r.plafon_closing)}\t${r.noa_sudah}\t${Math.round(r.nominal_sudah)}\t${pct}\t${r.noa_lunas}\t${Math.round(r.nominal_lunas)}\t${r.noa_topup}\t${Math.round(r.os_topup)}\t${r.noa_retensi}\t${Math.round(r.os_retensi)}\t${r.noa_drop}\t${Math.round(r.os_drop)}\n`;
      });

      const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      a.download = `Rekomendasi_Pipeline_Kredit_${document.getElementById("tahun_jt").value}.xls`; 
      a.click();
  }

  // --- MODAL DETAIL NASABAH ---
  async function openModal(cabang, nama, groupType = 'CABANG', groupCode = '', detailStatus = '') {
      if (userKodeGlobal !== '000' && cabang && String(cabang) !== userKodeGlobal) {
          alert(`AKSES DITOLAK!\nAnda tidak memiliki izin untuk melihat detail Cabang ${cabang}.`);
          return;
      }

      state.cabang = cabang;
      state.kankas = groupType === 'KANKAS' ? groupCode : '';
      state.ao = groupType === 'AO' ? groupCode : '';
      state.status = detailStatus || '';
      state.page = 1;
      const modal = document.getElementById('modalDetail');
      modal.classList.remove('hidden'); modal.classList.add('flex');
      
      const groupLabel = groupType === 'AO' ? 'Per AO Kredit' : groupType === 'KANKAS' ? 'Per Kankas' : 'Per Cabang';
      document.getElementById('detailSubTitle').innerText = `${nama} - ${groupLabel} - Tahun JT ${document.getElementById('tahun_jt').value}`;
      document.getElementById('filter_ao_modal').innerHTML = '<option value="">Semua AO</option>';
      document.getElementById('filter_status_modal').value = state.status;
      await loadKankasModal(cabang);
      fetchDetail();
  }

  async function loadKankasModal(kode_cabang) {
      const el = document.getElementById('filter_kankas_modal');
      el.innerHTML = '<option value="">Semua Kankas</option>';
      if(!kode_cabang) return;
      try {
          const payload = { type: 'kode_kankas', kode_kantor: kode_cabang };
          const json = await apiCall(API_KODE, payload);
          if(json.data && Array.isArray(json.data)) {
              json.data.forEach(x => { el.add(new Option(x.deskripsi_group1 || x.kode_group1, x.kode_group1)); });
          }
          el.value = state.kankas || '';
      } catch(e) {}
  }

  function changeFilter() {
      state.status = document.getElementById('filter_status_modal').value;
      state.ao = document.getElementById('filter_ao_modal').value;
      state.kankas = document.getElementById('filter_kankas_modal').value;
      state.page = 1;
      fetchDetail();
  }

  function changePage(step) {
      const next = state.page + step;
      if(next > 0 && next <= state.totalPages) { state.page = next; fetchDetail(); }
  }

  async function fetchDetail() {
      const l=document.getElementById('loadingDetail'), tb=document.getElementById('bodyDetail');
      const trTot = document.getElementById('rowTotalDetailAtas');
      l.classList.remove('hidden'); tb.innerHTML=''; trTot.innerHTML='';
      
      const actDate = new Date(document.getElementById('harian_date').value);

      try {
          const payload = {
              type: 'detail_pipeline',
              closing_date: document.getElementById('closing_date').value,
              harian_date: document.getElementById('harian_date').value,
              tahun_jt: document.getElementById('tahun_jt').value,
              kode_kantor: state.cabang, kode_kankas: state.kankas, kode_ao: state.ao, filter_status: state.status, kolektibilitas: state.kolektibilitas,
              page: state.page, limit: state.limit
          };

          const json = await apiCall(API_URL, payload);
          const rows = json.data?.data || [];
          const stats = json.data?.stats || {};
          const aoList = json.data?.list_ao || [];

          let totBaru = 0, totBaseLunasSudah = 0;
          let t_plafon_awal = 0, t_sisa_os = 0, t_nom_baru = 0; 

          rows.forEach(r => {
              const isClear = r.status_ket.toUpperCase().includes("SUDAH") || r.status_ket.toUpperCase() === "LUNAS" || r.status_ket.toUpperCase() === "LUNAS (POTENSI)";
              
              totBaru += parseFloat(r.plafon_baru || 0);
              if (isClear) totBaseLunasSudah += parseFloat(r.plafon_awal || 0);

              t_plafon_awal += parseFloat(r.plafon_awal||0);
              if(!isClear) t_sisa_os += parseFloat(r.os_actual||0);
              t_nom_baru += parseFloat(r.plafon_baru||0);
          });
          const pctBaru = totBaseLunasSudah > 0 ? ((totBaru / totBaseLunasSudah) * 100).toFixed(2) : 0;

          document.getElementById('modalStats').innerHTML = `
              <div class="flex gap-4 md:gap-8 px-2 items-center">
                 <div>Total: <span class="font-bold text-slate-800 text-sm">${fmt(stats.total_data)}</span></div>
                 <div class="text-emerald-600">Sudah: <span class="font-bold text-sm">${fmt(stats.cnt_sudah)}</span></div>
                 <div class="text-blue-600">Lunas: <span class="font-bold text-sm">${fmt(stats.cnt_lunas)}</span></div>
                 <div class="text-purple-600">TopUp: <span class="font-bold text-sm">${fmt(stats.cnt_topup)}</span></div>
                 <div class="text-orange-600">Retensi: <span class="font-bold text-sm">${fmt(stats.cnt_retensi)}</span></div>
                 <div class="text-rose-600">Drop: <span class="font-bold text-sm">${fmt(stats.cnt_drop)}</span></div>
                 
                 <div class="ml-auto bg-emerald-50 text-emerald-800 px-3 py-1 rounded border border-emerald-200">
                     % Realisasi Baru: <span class="font-bold font-mono text-sm">${pctBaru}%</span>
                 </div>
              </div>`;

          const selAO = document.getElementById('filter_ao_modal');
          if(selAO.options.length === 1 && aoList.length > 0) {
              aoList.forEach(ao => { selAO.add(new Option(ao.nama_ao, ao.kode_group2)); });
              selAO.value = state.ao;
          }

          if(rows.length === 0) {
              tb.innerHTML = `<tr><td colspan="13" class="text-center py-20 text-slate-400 italic text-base">Tidak ada data nasabah.</td></tr>`;
              document.getElementById('pageInfo').innerText = '0 Data';
              return;
          }

          state.totalPages = json.data?.pagination?.total_pages || 1;
          document.getElementById('pageInfo').innerText = `Hal ${state.page} / ${state.totalPages}`;

          rows.sort((a, b) => new Date(a.tgl_jatuh_tempo) - new Date(b.tgl_jatuh_tempo));

          trTot.innerHTML = `
              <th class="pipeline-desktop-identity mod-sticky-1 px-3 border-r border-b border-blue-200 uppercase tracking-widest text-center bg-[#eff6ff]">-</th>
              <th class="pipeline-desktop-identity mod-sticky-2 px-4 border-r border-b border-blue-200 uppercase tracking-widest font-extrabold text-sm bg-[#eff6ff]">TOTAL HALAMAN INI</th>
              <th class="pipeline-mobile-identity px-2 border-r border-b border-blue-200 uppercase tracking-widest font-extrabold text-[8px] bg-[#eff6ff]">TOTAL</th>
               <th class="px-4 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-4 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-3 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-3 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-4 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-4 border-r border-b border-blue-200 text-right font-mono font-bold text-sm text-blue-900 bg-[#eff6ff]">${fmt(t_plafon_awal)}</th>
              <th class="px-3 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-4 border-r border-b border-blue-300 text-right font-mono font-bold text-sm text-blue-900 bg-blue-100/50">${fmt(t_sisa_os)}</th>
              <th class="px-4 border-r border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
              <th class="px-4 border-r border-b border-blue-200 text-right font-mono font-bold text-sm text-emerald-800 bg-emerald-50/50">${fmt(t_nom_baru)}</th>
              <th class="px-3 border-b border-blue-200 text-center bg-[#eff6ff]">-</th>
          `;

          let html = '';
          rows.forEach(r => {
              const aoName = (r.nama_ao || '-').split(' ').slice(0,2).join(' ');
              const alamatLengkap = r.alamat || '-';
              const alamatPendek = alamatLengkap.length > 25 ? alamatLengkap.substring(0, 25) + '...' : alamatLengkap;
              const noHp = r.no_hp ? `<span class="font-mono text-slate-600">${r.no_hp}</span>` : `<span class="text-slate-400">-</span>`;
              const kankas = r.nama_kankas || r.kankas || '-';
              const productName = r.nama_produk || (r.kode_produk_lama ? `PRODUK ${r.kode_produk_lama}` : '-');
              
              let statStr = (r.status_ket || '').toUpperCase();
              let isClear = statStr.includes("SUDAH") || statStr === "LUNAS" || statStr === "LUNAS (POTENSI)";
              let isDrop = statStr.includes("DROP");

              let sisaOsVisual = isClear ? '-' : fmt(r.os_actual);
              
              let nomBaru = '-';
              if (parseFloat(r.plafon_baru) > 0) {
                  nomBaru = `<div class="font-bold text-emerald-700 text-sm">${fmt(r.plafon_baru)}</div><div class="text-[10px] md:text-[11px] text-emerald-600 font-mono mt-0.5">${r.tgl_baru || ''}</div>`;
              }

              // MODIFIKASI 1: "LUNAS" TIDAK LAGI DILOCK (BISA PROSPEK)
              let isLocked = statStr.includes("SUDAH") || isDrop;
              const btnAksi = isLocked 
                  ? `<span class="text-xs font-bold text-slate-400">LOCKED</span>`
                  : `<button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition w-full uppercase tracking-widest">PROSPEK</button>`;

              let badgeClass = "text-slate-600 border-slate-300 bg-slate-50";
              if(statStr.includes("SUDAH")) badgeClass = "text-emerald-700 border-emerald-300 bg-emerald-50/80";
              else if(statStr === "LUNAS" || statStr === "LUNAS (POTENSI)") badgeClass = "text-blue-700 border-blue-300 bg-blue-50/80";
              else if(statStr.includes("TOP UP")) badgeClass = "text-purple-700 border-purple-300 bg-purple-50/80";
              else if(statStr.includes("RETENSI")) badgeClass = "text-orange-700 border-orange-300 bg-orange-50/80";
              else if(statStr.includes("DROP")) badgeClass = "text-rose-700 border-rose-300 bg-rose-50/80";

              // MODIFIKASI 2 & 3: TAMBAHAN KETERANGAN PERSENTASE BD UNTUK LABEL RETENSI DAN TOP UP
              let displayStatus = r.status_ket;
              if (statStr.includes("RETENSI")) displayStatus = "RETENSI (BD > 50%)";
              else if (statStr.includes("TOP UP")) displayStatus = "TOP UP (BD <= 50%)";

              const jtDate = new Date(r.tgl_jatuh_tempo);
              const diffTime = jtDate - actDate;
              const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
              
              let strJt = `<div class="font-mono text-sm text-slate-700">${r.tgl_jatuh_tempo}</div>`;
              if (!isClear) {
                  if (diffDays < 0) {
                      strJt += `<div class="text-[10px] text-rose-600 font-bold mt-1 bg-rose-50 rounded inline-block px-1.5 py-0.5">Lewat ${Math.abs(diffDays)} Hari</div>`;
                  } else if (diffDays === 0) {
                      strJt += `<div class="text-[10px] text-orange-600 font-bold mt-1 bg-orange-50 rounded inline-block px-1.5 py-0.5">HARI INI!</div>`;
                  } else if (diffDays <= 30) {
                      strJt += `<div class="text-[10px] text-orange-500 font-bold mt-1">Kurang ${diffDays} Hari</div>`;
                  } else {
                      strJt += `<div class="text-[10px] text-slate-400 mt-1">${diffDays} Hari lagi</div>`;
                  }
              }

              html += `
                <tr class="transition h-[52px] group border-b border-slate-100">
                    <td class="pipeline-desktop-identity mod-sticky-1 px-3 py-2 font-mono text-sm text-slate-500 bg-white border-r border-slate-100 shadow-[inset_-1px_0_0_#e2e8f0]">${r.no_rekening}</td>
                    <td class="pipeline-desktop-identity mod-sticky-2 px-4 py-2 font-bold text-sm text-slate-700 bg-white truncate border-r border-slate-100 max-w-[220px] md:max-w-[280px] shadow-[inset_-1px_0_0_#e2e8f0]" title="${r.nama_nasabah}">${r.nama_nasabah}</td>
                    <td class="pipeline-mobile-identity px-2 py-1.5 bg-white border-r border-slate-100" title="${r.no_rekening} - ${r.nama_nasabah}"><span class="pipeline-mobile-account">${r.no_rekening || '-'}</span><span class="pipeline-mobile-name">${r.nama_nasabah || '-'}</span></td>
                    <td class="px-4 py-2 text-sm font-semibold text-slate-700 truncate border-r border-slate-100" title="${productName}">${productName}</td>
                    <td class="px-4 py-2 text-sm text-slate-500 whitespace-nowrap border-r border-slate-100" title="${alamatLengkap}">${alamatPendek}</td>
                    <td class="px-3 py-2 text-center border-r border-slate-100 text-sm">${noHp}</td>
                    <td class="px-3 py-2 text-center font-mono text-xs md:text-sm text-slate-500 border-r border-slate-100">${kankas}</td>
                    
                    <td class="px-4 py-2 text-sm font-bold text-blue-700 truncate border-r border-slate-100">${aoName}</td>
                    <td class="px-4 py-2 text-right font-medium text-sm text-slate-600 border-r border-slate-100">${fmt(r.plafon_awal)}</td>
                    <td class="px-3 py-2 text-center border-r border-slate-100">${strJt}</td>
                    <td class="px-4 py-2 text-right font-mono font-bold text-sm text-blue-700 bg-blue-50/30 border-r border-blue-100">${sisaOsVisual}</td>
                    <td class="px-4 py-2 text-center border-r border-slate-100"><span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] md:text-xs font-bold uppercase tracking-widest border ${badgeClass}">${displayStatus}</span></td>
                    <td class="px-4 py-2 text-right bg-emerald-50/30 border-r border-slate-100">${nomBaru}</td>
                    <td class="px-3 py-2 text-center">${btnAksi}</td>
                </tr>`;
          });
          tb.innerHTML = html;

          document.getElementById('btnPrev').disabled = state.page <= 1;
          document.getElementById('btnNext').disabled = state.page >= state.totalPages;

      } catch(e) { console.error(e); } finally { l.classList.add('hidden'); }
  }

  // --- EXPORT EXCEL DETAIL ---
  window.downloadExcelDetail = async function(event) {
      const btn = event?.target?.closest('button') || document.querySelector('#modalDetail button[title="Download Excel"]');
      if (!btn) return;
      const txt = btn.innerHTML;
      btn.innerHTML = `<span class="animate-spin inline-block h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>...`;
      btn.disabled = true;

      try {
          const payload = {
              type: 'detail_pipeline',
              closing_date: document.getElementById('closing_date').value,
              harian_date: document.getElementById('harian_date').value,
              tahun_jt: document.getElementById('tahun_jt').value,
              kode_kantor: state.cabang, kode_kankas: state.kankas, kode_ao: state.ao, filter_status: state.status, kolektibilitas: state.kolektibilitas,
              page: 1, limit: 10000 
          };
          const json = await apiCall(API_URL, payload);
          let rows = json.data?.data || [];
          
          if(rows.length===0) { alert('Data kosong'); btn.innerHTML=txt; btn.disabled=false; return; }

          rows.sort((a, b) => new Date(a.tgl_jatuh_tempo) - new Date(b.tgl_jatuh_tempo));

          let csv = "No Rekening\tNama Nasabah\tNama Produk\tAlamat\tNo HP\tKankas\tNama AO\tPlafon Awal\tTgl JT\tSisa OS\tStatus\tTgl Realisasi Baru\tNominal Baru\n";
          rows.forEach(r => {
              const isClear = r.status_ket.toUpperCase().includes("SUDAH") || r.status_ket.toUpperCase() === "LUNAS" || r.status_ket.toUpperCase() === "LUNAS (POTENSI)";
              const sisaOsEx = isClear ? 0 : Math.round(r.os_actual);
              const alamatEx = r.alamat || '-';
              const hpEx = r.no_hp || '-';
              const kankasEx = r.nama_kankas || r.kankas || '-';
              const productEx = r.nama_produk || (r.kode_produk_lama ? `PRODUK ${r.kode_produk_lama}` : '-');

              csv += `'${r.no_rekening}\t${r.nama_nasabah}\t${productEx}\t${alamatEx}\t'${hpEx}\t${kankasEx}\t${r.nama_ao}\t${Math.round(r.plafon_awal)}\t${r.tgl_jatuh_tempo}\t${sisaOsEx}\t${r.status_ket}\t${r.tgl_baru||'-'}\t${Math.round(r.plafon_baru||0)}\n`;
          });

          const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `Detail_Pipeline_JT_${state.cabang}.xls`;
          document.body.appendChild(a); a.click(); document.body.removeChild(a);

      } catch(e) { alert('Gagal export'); } finally { btn.innerHTML=txt; btn.disabled=false; }
  }

  function closeModal() { 
      const modal = document.getElementById('modalDetail');
      modal.classList.add('hidden'); 
      modal.classList.remove('flex');
  }

  // --- MODAL INFO HANDLERS ---
  function openInfoModal() {
      const m = document.getElementById('infoModal');
      m.classList.remove('hidden'); m.classList.add('flex');
  }
  
  function closeInfoModal() {
      const m = document.getElementById('infoModal');
      m.classList.add('hidden'); m.classList.remove('flex');
  }

  document.addEventListener('keydown', e => { 
      if(e.key === 'Escape') {
          closeModal();
          closeInfoModal();
      }
  });
</script>

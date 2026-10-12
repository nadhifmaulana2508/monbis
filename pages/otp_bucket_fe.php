<style>
  :root { --primary: #059669; --bg: #f8fafc; --text: #334155; }
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow: hidden; }
  
  .inp { 
      box-sizing: border-box;
      border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0 0.5rem; 
      font-size: 12px; background: #fff; height: 36px; cursor: pointer; 
      outline: none; transition: border 0.2s; min-width: 0; font-weight: 600;
  }
  .inp:focus { border-color: var(--primary); box-shadow: 0 0 0 2px #a7f3d0; }
  
  input[type="date"] { position: relative; cursor: pointer; }
  input[type="date"]::-webkit-inner-spin-button,
  input[type="date"]::-webkit-calendar-picker-indicator {
      position: absolute; top: 0; left: 0; right: 0; bottom: 0;
      width: 100%; height: 100%; opacity: 0; cursor: pointer;
  }

  /* Scroller Tabel Utama */
  #progScroller { 
      overflow: auto; height: 100%; border-radius: 8px; 
      border: 1px solid #e2e8f0; background: white; position: relative;
  }
  
  table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 12px; }
  th, td { white-space: nowrap; padding: 6px 8px; vertical-align: middle; }
  tr:hover td { background-color: #f8fafc; }

  /* --- HEADER TABEL UTAMA (FIX ANTI BOCOR/CELAH) --- */
  thead th { 
      position: sticky; z-index: 60; 
      background: #e2e8f0; color: #1e293b; font-weight: 800; 
      text-transform: uppercase; 
      font-size: 11px; letter-spacing: 0.05em; text-align: center;
      /* Pakai box-shadow agar tidak ada spasi putih antar cell */
      box-shadow: inset -1px 0 0 #cbd5e1, inset 0 1px 0 #cbd5e1; 
  }
  
  /* Multilevel Header Fix - Patok Tinggi Statis */
  thead tr:nth-child(1) th { top: 0; height: 40px; }
  /* Khusus TGL & TARGET (Gabung 2 Baris) */
  thead tr:nth-child(1) th[rowspan="2"] { height: 80px; box-shadow: inset -1px 0 0 #cbd5e1, inset 0 -2px 0 #cbd5e1, inset 0 1px 0 #cbd5e1; }
  
  thead tr:nth-child(2) th { top: 40px; height: 40px; box-shadow: inset -1px 0 0 #cbd5e1, inset 0 -2px 0 #cbd5e1; }

  .col-kategori { 
      position: sticky; left: 0; z-index: 45; background: white; 
      min-width: 60px; font-weight: bold; text-align: center;
      box-shadow: inset -1px 0 0 #e2e8f0, inset 0 -1px 0 #f1f5f9;
  }
  thead th.col-kategori { z-index: 70; background: #e2e8f0; box-shadow: inset -1px 0 0 #cbd5e1, inset 0 -2px 0 #cbd5e1, inset 0 1px 0 #cbd5e1; }

  /* Row Grand Total */
  .sticky-total td { 
      position: sticky; top: 80px; z-index: 55; 
      background: #f4f7fb; font-weight: 800; 
      box-shadow: inset -1px 0 0 #e2e8f0, inset 0 -2px 0 #bfdbfe; text-align: right;
  }
  .sticky-total td.col-kategori { z-index: 65; background: #f4f7fb; box-shadow: inset -1px 0 0 #bfdbfe, inset 0 -2px 0 #bfdbfe; text-align: center; }

  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* --- TABEL DETAIL MODAL --- */
  #tableDetailProg th, #tableDetailProg td { background-color: #fff; } 
  #tableDetailProg thead th { position: sticky; top: 0; z-index: 40; background-color: #f1f5f9; box-shadow: inset -1px 0 0 #cbd5e1, inset 0 -2px 0 #cbd5e1, inset 0 1px 0 #cbd5e1; height: 36px;}
  .mod-freeze-rek { position: sticky; left: 0; z-index: 42 !important; background-color: #f1f5f9 !important; box-shadow: inset -1px 0 0 #cbd5e1, inset 0 -2px 0 #cbd5e1; min-width: 90px; max-width: 90px;}
  .mod-freeze-nas { position: sticky; left: 90px; z-index: 41 !important; background-color: #f1f5f9 !important; box-shadow: inset -1px 0 0 #cbd5e1, inset 0 -2px 0 #cbd5e1; min-width: 160px; max-width: 180px;}
  .mod-td-rek { position: sticky; left: 0; z-index: 32 !important; background-color: #fff !important; box-shadow: inset -1px 0 0 #f1f5f9, inset 0 -1px 0 #f1f5f9;}
  .mod-td-nas { position: sticky; left: 90px; z-index: 31 !important; background-color: #fff !important; box-shadow: inset -1px 0 0 #f1f5f9, inset 0 -1px 0 #f1f5f9; }
  tbody.mod-body tr:hover td { background-color: #f8fafc !important; }
  tbody.mod-body tr:hover td.mod-td-rek, tbody.mod-body tr:hover td.mod-td-nas { filter: brightness(0.97); }

  @media (max-width: 767px) {
      .col-kategori { left: 0 !important; z-index: 45 !important; min-width: 40px; font-size: 10px; padding: 4px 4px;}
      thead th.col-kategori { z-index: 70 !important; }
      .sticky-total td.col-kategori { z-index: 65 !important; }
      .mod-freeze-nas, .mod-td-nas { left: 0 !important; box-shadow: inset -1px 0 0 #f1f5f9; min-width: 100px; max-width: 120px; font-size: 10px;}
      .mod-freeze-rek, .mod-td-rek { display: none !important; }
      table { font-size: 10px; }
      th, td { padding: 4px 5px; }
      #progScroller { -webkit-overflow-scrolling: touch; }

      /* Modal detail mobile fixes */
      #modalDetailProg > div:last-child { border-radius: 0.75rem 0.75rem 0 0; height: 100vh; max-height: 100vh; }
      #tableDetailProg { font-size: 9px; }
      #tableDetailProg th, #tableDetailProg td { padding: 3px 4px; white-space: nowrap; }
      #tableDetailProg thead th { height: 30px; font-size: 8px; }
  }
  /* ========================================================
     OTP BUCKET FINAL POLISH - CLEAN, RESPONSIVE, THEMED
     ======================================================== */
  :root {
    --otp-bucket-bg: #f8fafc;
    --otp-bucket-surface: #ffffff;
    --otp-bucket-soft: #f8fafc;
    --otp-bucket-muted-surface: #f1f5f9;
    --otp-bucket-text: #1e293b;
    --otp-bucket-muted: #64748b;
    --otp-bucket-border: #dbe3ee;
    --otp-bucket-line: #e2e8f0;
    --otp-bucket-scroll: #94a3b8;
    --otp-bucket-scroll-track: #eef2f7;
  }

  :root[data-monbis-theme="dark"] {
    --otp-bucket-bg: #0f172a;
    --otp-bucket-surface: #111827;
    --otp-bucket-soft: #0f172a;
    --otp-bucket-muted-surface: #1e293b;
    --otp-bucket-text: #e2e8f0;
    --otp-bucket-muted: #94a3b8;
    --otp-bucket-border: #334155;
    --otp-bucket-line: #263449;
    --otp-bucket-scroll: #64748b;
    --otp-bucket-scroll-track: #111827;
  }

  #otpBucketPage,
  #otpBucketPage button,
  #otpBucketPage input,
  #otpBucketPage select,
  #modalDetailProg,
  #modalDetailProg button,
  #modalDetailProg input,
  #modalDetailProg select {
    font-family: Roboto, Arial, system-ui, sans-serif !important;
  }

  #otpBucketPage {
    height: calc(100dvh - 72px) !important;
    min-height: 360px !important;
    padding: 8px !important;
    color: var(--otp-bucket-text) !important;
    background: var(--otp-bucket-bg) !important;
    color-scheme: light;
  }

  :root[data-monbis-theme="dark"] #otpBucketPage,
  :root[data-monbis-theme="dark"] #modalDetailProg { color-scheme: dark; }

  #otpBucketPage .otp-bucket-page-head {
    min-height: 54px;
    padding: 9px 12px;
    border: 1px solid var(--otp-bucket-border);
    border-radius: 14px;
    background: var(--otp-bucket-surface);
    box-shadow: 0 1px 3px rgba(15,23,42,.05);
  }
  #otpBucketPage .otp-bucket-page-icon {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #2563eb !important;
  }
  #otpBucketPage .otp-bucket-page-subtitle { color: var(--otp-bucket-muted) !important; }
  #otpBucketPage .otp-bucket-page-subtitle { display: none !important; }
  #otpBucketPage .otp-bucket-page-head h1 { color: var(--otp-bucket-text) !important; }
  #otpBucketPage .otp-bucket-page-actions { margin-left: auto; }
  #otpBucketPage .bucket-kpp-toggle,
  #otpBucketPage .bucket-view-button,
  #otpBucketPage .otp-bucket-info-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--otp-bucket-border);
    border-radius: 9px;
    background: var(--otp-bucket-surface);
    color: var(--otp-bucket-text);
    transition: border-color .15s ease, background-color .15s ease, color .15s ease, transform .15s ease;
  }
  #otpBucketPage .bucket-kpp-toggle {
    height: 36px;
    gap: 6px;
    padding: 0 9px;
    font-size: 10px;
    font-weight: 800;
    cursor: pointer;
  }
  #otpBucketPage .bucket-kpp-toggle input {
    width: 13px;
    height: 13px;
    margin: 0;
    accent-color: #2563eb;
  }
  #otpBucketPage .bucket-view-button {
    color: #2563eb;
    background: #eff6ff;
    border-color: #bfdbfe;
  }
  #otpBucketPage .bucket-kpp-toggle:hover,
  #otpBucketPage .bucket-view-button:hover,
  #otpBucketPage .otp-bucket-info-button:hover { border-color: #94a3b8; transform: translateY(-1px); }
  #otpBucketPage .bucket-export-button {
    background: #059669 !important;
    border-color: #059669 !important;
    color: #fff !important;
  }
  #otpBucketPage .bucket-export-button:hover { background: #047857 !important; }
  #otpBucketPage .otp-bucket-info-button {
    width: 18px;
    height: 18px;
    padding: 0;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 900;
    color: #2563eb;
    background: #eff6ff;
    border-color: #bfdbfe;
    cursor: pointer;
  }
  #otpBucketPage.is-compact #tabelProgKredit th,
  #otpBucketPage.is-compact #tabelProgKredit td { padding-top: 4px !important; padding-bottom: 4px !important; }

  .otp-bucket-help-modal {
    position: fixed;
    inset: 0;
    z-index: 10050;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }
  .otp-bucket-help-modal.is-open { display: flex; }
  .otp-bucket-help-backdrop {
    position: absolute;
    inset: 0;
    border: 0;
    background: rgba(15, 23, 42, .58);
    backdrop-filter: blur(4px);
    cursor: pointer;
  }
  .otp-bucket-help-card {
    position: relative;
    z-index: 1;
    width: min(540px, calc(100vw - 24px));
    max-height: calc(100dvh - 32px);
    overflow: auto;
    border: 1px solid var(--otp-bucket-border);
    border-radius: 16px;
    background: var(--otp-bucket-surface);
    color: var(--otp-bucket-text);
    box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
    font-family: Roboto, Arial, system-ui, sans-serif;
  }
  .otp-bucket-help-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 18px;
    border-bottom: 1px solid var(--otp-bucket-line);
  }
  .otp-bucket-help-title-wrap { display: flex; align-items: center; gap: 10px; min-width: 0; }
  .otp-bucket-help-icon {
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #2563eb;
    font-size: 16px;
    font-weight: 900;
    flex: 0 0 auto;
  }
  .otp-bucket-help-title { margin: 0; font-size: 15px; font-weight: 800; line-height: 1.2; }
  .otp-bucket-help-caption { margin: 3px 0 0; color: var(--otp-bucket-muted); font-size: 10px; line-height: 1.35; }
  .otp-bucket-help-close {
    width: 30px;
    height: 30px;
    border: 1px solid var(--otp-bucket-border);
    border-radius: 8px;
    background: var(--otp-bucket-soft);
    color: var(--otp-bucket-muted);
    font-size: 19px;
    line-height: 1;
    cursor: pointer;
  }
  .otp-bucket-help-body { padding: 16px 18px 18px; font-size: 12px; line-height: 1.55; }
  .otp-bucket-help-body p { margin: 0 0 10px; }
  .otp-bucket-help-list { margin: 0; padding-left: 20px; }
  .otp-bucket-help-list li { margin: 7px 0; }
  .otp-bucket-help-note {
    margin-top: 14px;
    padding: 10px 12px;
    border: 1px solid var(--otp-bucket-border);
    border-radius: 10px;
    background: var(--otp-bucket-soft);
    color: var(--otp-bucket-text);
    font-weight: 700;
  }
  @media (max-width: 767px) {
    #otpBucketPage .otp-bucket-page-actions { gap: 4px; }
    #otpBucketPage .bucket-kpp-toggle { height: 30px; padding: 0 6px; font-size: 9px; }
    #otpBucketPage .bucket-view-button,
    #otpBucketPage .bucket-export-button { width: 30px !important; height: 30px !important; }
    #otpBucketPage .otp-bucket-info-button { width: 17px; height: 17px; font-size: 10px; }
    .otp-bucket-help-modal { align-items: flex-end; padding: 0; }
    .otp-bucket-help-card { width: 100%; max-height: min(88dvh, 620px); border-radius: 16px 16px 0 0; }
  }
  #otpBucketPage .bucket-export-button {
    border: 1px solid var(--otp-bucket-border);
    background: #059669 !important;
    color: #fff;
    box-shadow: 0 5px 12px rgba(15,23,42,.12);
    transition: transform .15s ease, background .15s ease;
  }
  #otpBucketPage .bucket-export-button:hover { background: #047857 !important; transform: translateY(-1px); }

  #otpBucketPage > .flex-1 {
    min-height: 0 !important;
    border-color: var(--otp-bucket-border) !important;
    background: var(--otp-bucket-surface) !important;
    box-shadow: 0 1px 3px rgba(15,23,42,.05) !important;
  }
  #otpBucketPage #progScroller {
    min-height: 0;
    flex: 1 1 auto;
    border-color: var(--otp-bucket-line);
    background: var(--otp-bucket-surface);
    scrollbar-width: thin;
    scrollbar-color: var(--otp-bucket-scroll) var(--otp-bucket-scroll-track);
    -webkit-overflow-scrolling: touch;
  }
  #otpBucketPage #progScroller::-webkit-scrollbar,
  #modalDetailProg ::-webkit-scrollbar { width: 5px; height: 5px; }
  #otpBucketPage #progScroller::-webkit-scrollbar-track,
  #modalDetailProg ::-webkit-scrollbar-track { background: var(--otp-bucket-scroll-track); border-radius: 999px; }
  #otpBucketPage #progScroller::-webkit-scrollbar-thumb,
  #modalDetailProg ::-webkit-scrollbar-thumb { background: var(--otp-bucket-scroll); border-radius: 999px; }
  #otpBucketPage #progScroller::-webkit-scrollbar-button,
  #modalDetailProg ::-webkit-scrollbar-button { display: none; width: 0; height: 0; }

  #tabelProgKredit,
  #tableDetailProg {
    color: var(--otp-bucket-text) !important;
    border-color: var(--otp-bucket-line) !important;
  }
  #tabelProgKredit thead th,
  #tableDetailProg thead th {
    background: var(--otp-bucket-muted-surface) !important;
    color: var(--otp-bucket-text) !important;
    border-color: var(--otp-bucket-line) !important;
    box-shadow: inset -1px 0 0 var(--otp-bucket-line), inset 0 1px 0 var(--otp-bucket-line) !important;
  }
  #tabelProgKredit tbody td,
  #tableDetailProg tbody td {
    background: var(--otp-bucket-surface) !important;
    color: var(--otp-bucket-text) !important;
    border-color: var(--otp-bucket-line) !important;
  }
  #tabelProgKredit tbody tr:hover td,
  #tableDetailProg tbody tr:hover td { background: var(--otp-bucket-soft) !important; }
  #tabelProgKredit .sticky-total td { background: var(--otp-bucket-muted-surface) !important; color: var(--otp-bucket-text) !important; border-color: var(--otp-bucket-border) !important; }
  #tabelProgKredit .col-kategori,
  #tabelProgKredit .sticky-total td.col-kategori { background: var(--otp-bucket-surface) !important; color: var(--otp-bucket-text) !important; }
  #tabelProgKredit thead .col-kategori { background: var(--otp-bucket-muted-surface) !important; }
  #tabelProgKredit thead,
  #tabelProgKredit thead tr,
  #tabelProgKredit thead th,
  #tabelProgKredit thead th.col-kategori {
    background: var(--otp-bucket-muted-surface) !important;
    color: var(--otp-bucket-text) !important;
  }
  #tabelProgKredit #totalProg td,
  #tabelProgKredit #totalProg td.col-kategori {
    background: var(--otp-bucket-muted-surface) !important;
    color: var(--otp-bucket-text) !important;
    border-color: var(--otp-bucket-line) !important;
  }
  #tabelProgKredit #totalProg td[class*="border-b-"] {
    border-bottom-color: var(--otp-bucket-line) !important;
  }

  /* Neutral palette: status tetap dibedakan oleh teks/statusnya, bukan warna-warni. */
  #tabelProgKredit [class*="text-blue-"],
  #tabelProgKredit [class*="text-emerald-"],
  #tabelProgKredit [class*="text-teal-"],
  #tabelProgKredit [class*="text-orange-"],
  #tabelProgKredit [class*="text-red-"],
  #tabelProgKredit [class*="text-indigo-"],
  #tabelProgKredit [class*="text-slate-"],
  #tableDetailProg [class*="text-blue-"],
  #tableDetailProg [class*="text-emerald-"],
  #tableDetailProg [class*="text-teal-"],
  #tableDetailProg [class*="text-amber-"],
  #tableDetailProg [class*="text-red-"],
  #tableDetailProg [class*="text-indigo-"],
  #tableDetailProg [class*="text-slate-"] { color: var(--otp-bucket-text) !important; }
  #tableDetailProg [class*="bg-emerald-"],
  #tableDetailProg [class*="bg-teal-"],
  #tableDetailProg [class*="bg-amber-"],
  #tableDetailProg [class*="bg-red-"],
  #tableDetailProg [class*="bg-indigo-"],
  #tableDetailProg [class*="bg-slate-"] { background: var(--otp-bucket-muted-surface) !important; color: var(--otp-bucket-text) !important; }

  #modalDetailProg > div:last-child {
    border: 1px solid var(--otp-bucket-border);
    background: var(--otp-bucket-surface) !important;
  }
  #modalDetailProg .bg-white,
  #modalDetailProg .bg-slate-50 { background: var(--otp-bucket-surface) !important; }
  #modalDetailProg .text-slate-500,
  #modalDetailProg .text-slate-600 { color: var(--otp-bucket-muted) !important; }
  #modalDetailProg input,
  #modalDetailProg select { border-color: var(--otp-bucket-border) !important; background: var(--otp-bucket-surface) !important; color: var(--otp-bucket-text) !important; }

  #legacyProgFilter { display: none !important; }

  @media (max-width: 767px) {
    #otpBucketPage { height: calc(100dvh - 54px) !important; min-height: 320px !important; padding: 5px !important; }
    #otpBucketPage .otp-bucket-page-head { min-height: 48px; padding: 7px 8px; border-radius: 11px; }
    #otpBucketPage .otp-bucket-page-head h1 { font-size: 13px !important; gap: 6px !important; }
    #otpBucketPage .otp-bucket-page-icon { width: 28px; height: 28px; border-radius: 8px; }
    #otpBucketPage .otp-bucket-page-icon svg { width: 16px; height: 16px; }
    #otpBucketPage .otp-bucket-page-subtitle { margin-left: 34px; font-size: 7px !important; }
    #otpBucketPage #progScroller { overflow: auto !important; }
    #tabelProgKredit { min-width: 860px !important; width: 860px !important; }
    #tableDetailProg { min-width: 1180px !important; }
    #modalDetailProg > div:last-child { width: 100%; height: 94dvh; max-height: 94dvh; border-radius: 16px 16px 0 0; }
  }
</style>

<div id="otpBucketPage" class="max-w-[1600px] mx-auto px-2 sm:px-3 md:px-4 py-3 md:py-4 h-[calc(100vh-64px)] sm:h-[calc(100vh-80px)] md:h-[calc(100vh-120px)] flex flex-col relative z-10">
  
  <div class="otp-bucket-page-head flex flex-col xl:flex-row xl:items-end justify-between gap-3 mb-4 shrink-0">
    <div class="flex items-center justify-between w-full">
        <div>
            <h1 class="text-lg md:text-2xl font-bold flex items-center gap-2 text-slate-800">
                <span class="otp-bucket-page-icon bg-slate-700 text-white p-1.5 rounded-lg text-sm shadow-sm">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 19V9"></path><path d="M10 19V5"></path><path d="M16 19v-7"></path><path d="M22 19V3"></path></svg>
                </span> 
                <span id="otpBucketTitle" class="truncate">OTP - ALL</span>
                <button id="otpBucketHelpButton" type="button" class="otp-bucket-info-button" onclick="toggleOtpBucketHelp()" aria-label="Buka informasi OTP" aria-expanded="false" title="Informasi tindak lanjut OTP">i</button>
            </h1>
             <p class="otp-bucket-page-subtitle text-[8px] md:text-xs text-slate-500 font-medium ml-8 md:ml-[42px] leading-tight truncate">
                 *Berdasarkan Tanggal Jatuh Tempo
              </p>
        </div>

        <div class="otp-bucket-page-actions flex items-center gap-1.5">
            <button type="button" onclick="exportProgExcel()" class="bucket-export-button btn-icon h-9 w-9 rounded-lg flex items-center justify-center" title="Export Excel Rekap" aria-label="Export Excel Rekap">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 20h14"></path></svg>
            </button>
        </div>
    </div>

    <div id="legacyProgFilter" class="hidden bg-white border border-gray-200 rounded-xl p-3 shadow-sm w-full xl:w-auto transition-all">
        <form id="legacyFormFilterProg" class="flex flex-col md:flex-row items-end gap-2 md:gap-3 w-full">
            <div class="flex flex-col w-full md:w-[120px]">
                <label class="text-[8px] md:text-[9px] font-extrabold text-slate-500 uppercase ml-1 mb-1 tracking-wider">CLOSING (M-1)</label>
                <input type="date" id="legacy_closing_date_otp" class="inp shadow-sm text-slate-700" required>
            </div>

            <div class="flex flex-col w-full md:w-[120px]">
                <label class="text-[8px] md:text-[9px] font-extrabold text-slate-500 uppercase ml-1 mb-1 tracking-wider">HARIAN (ACTUAL)</label>
                <input type="date" id="legacy_harian_date_otp" class="inp shadow-sm text-slate-700" required>
            </div>

            <div class="flex flex-col w-full md:w-[100px]">
                <label class="text-[8px] md:text-[9px] font-extrabold text-slate-500 uppercase ml-1 mb-1 tracking-wider">BUCKET</label>
                <select id="legacy_type_bucket_otp" class="inp text-slate-700 shadow-sm" onchange="triggerAutoRefresh()">
                    <option value="fe_all">ALL</option>
                    <option value="31-60">31 - 60</option>
                    <option value="61-90">61 - 90</option>
                </select>
            </div>

            <div class="flex flex-col w-full md:w-[150px]" id="wrap-cabang">
                <label class="text-[8px] md:text-[9px] font-extrabold text-slate-500 uppercase ml-1 mb-1 tracking-wider">CABANG</label>
                <select id="legacy_opt_kantor_otp" class="inp text-slate-700 shadow-sm truncate" onchange="handleCabangChange()">
                    <option value="">ALL | SEMUA CABANG</option>
                </select>
            </div>

            <div class="flex flex-col w-full md:w-[130px]">
                <label id="legacy_lbl_sub_otp" class="text-[8px] md:text-[9px] font-extrabold text-slate-500 uppercase ml-1 mb-1 tracking-wider">KORWIL</label>
                <select id="legacy_opt_sub_otp" class="inp text-slate-700 shadow-sm truncate" onchange="triggerAutoRefresh()">
                    <option value="">ALL KORWIL</option>
                    <option value="SEMARANG">SEMARANG</option>
                    <option value="SOLO">SOLO</option>
                    <option value="BANYUMAS">BANYUMAS</option>
                    <option value="PEKALONGAN">PEKALONGAN</option>
                </select>
            </div>
            
            <div class="flex gap-2 shrink-0 mt-2 md:mt-0 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-emerald-600 hover:bg-emerald-700 text-white h-[36px] w-[36px] md:w-[40px] rounded-lg shadow-sm flex items-center justify-center transition" title="Cari Data">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
                <button type="button" onclick="exportProgExcel()" class="bg-indigo-600 hover:bg-indigo-700 text-white h-[36px] w-[36px] md:w-[40px] rounded-lg shadow-sm flex items-center justify-center transition" title="Export Excel Rekap">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                </button>
            </div>
        </form>
    </div>
  </div>

  <div class="flex-1 min-h-0 relative flex flex-col bg-white rounded-xl shadow-sm border border-slate-200">
    <div id="loadingProg" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-emerald-600 font-bold text-xs backdrop-blur-sm rounded-xl">
        <div class="animate-spin h-6 w-6 border-4 border-emerald-200 border-t-emerald-600 rounded-full mb-2"></div>
        Kalkulasi...
    </div>

    <div class="table-wrapper custom-scrollbar" id="progScroller">
      <table id="tabelProgKredit">
        <thead id="theadProg">
          <tr>
            <th class="col-kategori" rowspan="2">TGL</th>
            <th rowspan="2" class="text-blue-800">TARGET (M-1)</th>
            <th colspan="6">STATUS MIGRASI (M)</th>
          </tr>
          <tr>
            <th class="text-emerald-600 leading-tight">BTC<br><span class="text-[8px] md:text-[9px] font-medium">(LANCAR)</span></th>
            <th class="text-teal-600 leading-tight">BACKFLOW<br><span class="text-[8px] md:text-[9px] font-medium">(MEMBAIK)</span></th>
            <th class="text-orange-500 leading-tight">STAY<br><span class="text-[8px] md:text-[9px] font-medium">(TETAP)</span></th>
            <th class="text-red-600 leading-tight">MIGRASI<br><span class="text-[8px] md:text-[9px] font-medium">(MEMBURUK)</span></th>
            <th class="text-indigo-600 leading-tight">ANGSURAN<br><span class="text-[8px] md:text-[9px] font-medium">(SELISIH)</span></th>
            <th class="text-slate-500 leading-tight">LUNAS<br><span class="text-[8px] md:text-[9px] font-medium">(RUN OFF)</span></th>
          </tr>
        </thead>
        <tbody id="totalProg"></tbody>
        <tbody id="bodyProg"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="otpBucketHelpModal" class="otp-bucket-help-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="otpBucketHelpTitle">
  <button type="button" class="otp-bucket-help-backdrop" onclick="toggleOtpBucketHelp(false)" aria-label="Tutup informasi OTP"></button>
  <div class="otp-bucket-help-card">
    <div class="otp-bucket-help-head">
      <div class="otp-bucket-help-title-wrap">
        <span class="otp-bucket-help-icon" aria-hidden="true">i</span>
        <div>
          <h2 id="otpBucketHelpTitle" class="otp-bucket-help-title">Informasi OTP - ALL</h2>
          <p class="otp-bucket-help-caption">Panduan tindak lanjut AO remedial</p>
        </div>
      </div>
      <button type="button" class="otp-bucket-help-close" onclick="toggleOtpBucketHelp(false)" aria-label="Tutup">&times;</button>
    </div>
    <div class="otp-bucket-help-body">
      <p><strong>Tujuan monitoring:</strong> membantu tim AO remedial menemukan debitur yang perlu segera ditindaklanjuti sebelum masuk atau memburuk ke bucket berikutnya.</p>
      <ol class="otp-bucket-help-list">
        <li><strong>BTC (Lancar):</strong> pantau pembayaran debitur yang kembali lancar dan pastikan tidak terlambat pada jatuh tempo berikutnya.</li>
        <li><strong>Backflow:</strong> prioritaskan konfirmasi pembayaran dan lakukan reminder agar debitur tidak kembali ke bucket tunggakan.</li>
        <li><strong>Stay:</strong> segera susun rencana kunjungan atau penagihan bersama AO remedial karena debitur masih berada di bucket yang sama.</li>
        <li><strong>Migrasi:</strong> lakukan eskalasi dan penanganan intensif karena kualitas debitur sudah memburuk ke bucket yang lebih tinggi.</li>
      </ol>
      <div class="otp-bucket-help-note">Klik nominal pada setiap status untuk membuka detail debitur, lalu koordinasikan tindak lanjut dengan AO remedial secepatnya.</div>
    </div>
  </div>
</div>

<div id="modalDetailProg" class="fixed inset-0 hidden z-[9999] flex items-end sm:items-center justify-center p-0 sm:p-2 md:p-4">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalProg()"></div>
  <div class="relative bg-white w-full h-[100dvh] sm:h-[95vh] md:h-[92vh] max-w-[1600px] rounded-t-xl sm:rounded-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up">
    
    <div class="flex flex-col bg-white border-b shrink-0 w-full z-50">
        <div class="flex flex-col md:flex-row md:items-center justify-between px-3 py-2.5 md:px-4 md:py-3 gap-2 w-full">
            <div class="flex items-center justify-between w-full md:w-auto md:flex-1 min-w-0">
              <div class="flex-1 min-w-0">
                <h3 class="font-bold text-slate-800 flex items-center gap-1.5 text-[11px] md:text-lg leading-none truncate">
                    <span class="w-1.5 md:w-2 h-3 md:h-5 bg-emerald-600 rounded-full hidden md:block shrink-0"></span> 
                    <span id="mdlTitleProg" class="truncate">Detail Debitur</span>
                </h3>
                <p class="text-[8px] md:text-xs text-slate-500 mt-1 md:ml-3 font-mono font-medium leading-none truncate" id="mdlSubTitleProg">...</p>
              </div>
              <button onclick="closeModalProg()" class="md:hidden w-[28px] h-[28px] flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-500 hover:text-white text-red-500 transition font-bold text-lg leading-none shrink-0">&times;</button>
            </div>
            
            <div class="flex flex-row items-center gap-1 md:gap-2 shrink-0 w-full md:w-auto overflow-x-auto">
                <input type="text" id="opt_search_prog_modal" class="inp px-2 h-[28px] md:h-[36px] w-[90px] md:w-[160px] text-[9px] md:text-xs text-slate-700 bg-slate-50 border-slate-200 shrink-0" placeholder="Cari nama/rek..." onkeydown="if(event.key==='Enter'){event.preventDefault();loadDetailProgPage(1);}">

                <select id="opt_ao_prog_modal" class="inp px-1 md:px-2 h-[28px] md:h-[36px] w-[70px] md:w-[130px] text-[9px] md:text-xs font-bold text-slate-700 bg-slate-50 border-slate-200 cursor-pointer shrink-0" onchange="loadDetailProgPage(1)">
                    <option value="">ALL AO</option>
                </select>

                <select id="opt_kankas_prog_modal" class="inp px-1 md:px-2 h-[28px] md:h-[36px] w-[80px] md:w-[130px] text-[9px] md:text-xs font-bold text-slate-700 bg-slate-50 border-slate-200 cursor-pointer shrink-0" onchange="loadDetailProgPage(1)">
                    <option value="">ALL KANKAS</option>
                </select>

                <button onclick="exportDetailProgExcel()" class="btn-icon bg-indigo-600 hover:bg-indigo-700 text-white h-[28px] w-[28px] md:h-[36px] md:w-[36px] rounded-lg shadow-sm shrink-0 transition flex items-center justify-center" title="Export Excel Detail">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                </button>
                <button onclick="closeModalProg()" class="hidden md:flex w-[28px] h-[28px] md:w-[36px] md:h-[36px] items-center justify-center rounded-lg bg-red-50 hover:bg-red-500 hover:text-white text-red-500 transition font-bold text-lg leading-none shrink-0">&times;</button>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-auto bg-slate-50 relative custom-scrollbar" style="-webkit-overflow-scrolling: touch;">
      <div id="loadingModalProg" class="hidden absolute inset-0 bg-white/90 z-40 flex flex-col items-center justify-center text-emerald-600 backdrop-blur-sm">
         <div class="animate-spin h-6 w-6 md:h-10 md:w-10 border-4 border-emerald-200 border-t-emerald-600 rounded-full mb-2"></div>
         <span class="text-[9px] md:text-xs font-bold uppercase tracking-widest">Loading...</span>
      </div>
      
      <table class="w-max min-w-full text-left text-slate-700 table-fixed" id="tableDetailProg">
        <thead class="text-[8.5px] md:text-xs text-slate-600 uppercase tracking-wider select-none">
            <tr>
                <th class="mod-freeze-rek px-1 md:px-2 hidden md:table-cell py-2">REKENING</th>
                <th class="mod-freeze-nas px-1 md:px-3 py-2">NAMA NASABAH</th>
                <th class="px-1 md:px-2 text-center w-[40px]">KOL</th>
                <th class="px-2 md:px-3 text-left w-[150px]">ALAMAT</th>
                <th class="px-1 md:px-2 text-center w-[80px]">NO HP</th>
                <th class="px-1 md:px-2 text-center w-[90px]">KANKAS</th>
                <th class="px-1 md:px-2 text-center w-[90px]">AO</th>
                <th class="px-1 md:px-2 text-center w-[70px]">TGL JT</th>
                <th class="px-2 md:px-3 text-right w-[110px]">PLAFON</th>
                <th class="px-2 md:px-3 text-right w-[110px]">OS (CURR)</th>
                <th class="px-2 md:px-3 text-right w-[90px]">TABUNGAN</th>
                <th class="px-2 md:px-3 text-right w-[90px]">TGK POKOK</th>
                <th class="px-2 md:px-3 text-right w-[90px]">TGK BUNGA</th>
                <th class="px-1 md:px-2 text-center w-[50px]">DPD PK</th>
                <th class="px-1 md:px-2 text-center w-[50px]">DPD BG</th>
                <th class="px-2 md:px-3 text-center w-[60px]">DPD</th>
                <th class="px-2 md:px-3 text-center w-[100px]">MIGRASI</th>
                <th class="px-1 md:px-2 text-center w-[75px]">TGL BYR LALU</th>
                <th class="px-2 md:px-3 text-right w-[95px]">BYR LALU</th>
                <th class="px-1 md:px-2 text-center w-[75px]">TGL BYR SKR</th>
                <th class="px-2 md:px-3 text-right w-[95px]">BYR SKR</th>
            </tr>
        </thead>
        <tbody id="bodyModalProg" class="mod-body text-[8.5px] md:text-xs"></tbody>
      </table>
    </div>

    <div class="px-2 py-2 md:px-5 md:py-4 border-t bg-white flex justify-between items-center shrink-0">
      <span class="text-[8.5px] md:text-xs font-bold text-slate-600 bg-slate-100 px-1.5 md:px-3 py-1 rounded-md" id="pageInfoProg">0 Data</span>
      <div class="flex gap-1 md:gap-2">
          <button id="btnPrevProg" onclick="changePageProg(-1)" class="px-2 md:px-4 py-1 md:py-2 bg-white border border-slate-300 rounded-md text-[8.5px] md:text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-50 shadow-sm">« Prev</button>
          <button id="btnNextProg" onclick="changePageProg(1)" class="px-2 md:px-4 py-1 md:py-2 bg-white border border-slate-300 rounded-md text-[8.5px] md:text-xs font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-50 shadow-sm">Next »</button>
      </div>
    </div>
  </div>
</div>

<script>
  const API_RR     = './api/rr/'; 
  const API_KODE   = './api/kode/';

  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Number(n||0));
  const fmt2 = x => (x == null || x === '' ? '0.00' : Number(x).toFixed(2));

  window.progDataRaw = [];
  window.progGtRaw = null;

  let currentProgDetailParams = {};
  let currentProgPage = 1;
  let currentProgTotalPages = 1;

  let bucketFilterOpen = false;

  function toggleOtpBucketHelp(open) {
      const modal = document.getElementById('otpBucketHelpModal');
      const button = document.getElementById('otpBucketHelpButton');
      if (!modal) return;
      const shouldOpen = typeof open === 'boolean' ? open : !modal.classList.contains('is-open');
      modal.classList.toggle('is-open', shouldOpen);
      modal.setAttribute('aria-hidden', String(!shouldOpen));
      button?.setAttribute('aria-expanded', String(shouldOpen));
      document.body.classList.toggle('otp-bucket-help-open', shouldOpen);
  }

  function toggleBucketTableDensity() {
      const page = document.getElementById('otpBucketPage');
      const button = document.getElementById('btnBucketDensity');
      if (!page) return;
      const compact = page.classList.toggle('is-compact');
      button?.setAttribute('aria-pressed', String(compact));
      button?.setAttribute('title', compact ? 'Tampilan normal' : 'Tampilan ringkas');
      button?.setAttribute('aria-label', compact ? 'Tampilan normal' : 'Tampilan ringkas');
  }

  function updateOtpBucketTitle() {
      const bucket = document.getElementById('type_bucket_otp')?.value || 'fe_all';
      const label = bucket === '31-60' ? '31 - 60' : bucket === '61-90' ? '61 - 90' : 'ALL';
      const title = document.getElementById('otpBucketTitle');
      const helpTitle = document.getElementById('otpBucketHelpTitle');
      if (title) title.textContent = `OTP - ${label}`;
      if (helpTitle) helpTitle.textContent = `Informasi OTP - ${label}`;
  }

  document.addEventListener('keydown', event => {
      if (event.key === 'Escape') toggleOtpBucketHelp(false);
  });

  function applyBucketFilterState() {
      const panel = document.getElementById('otpBucketNavbarFilterPanel');
      const toggle = document.getElementById('otpBucketNavbarFilterToggle');
      if (!panel) return;
      panel.classList.toggle('hidden', !bucketFilterOpen);
      panel.classList.toggle('flex', bucketFilterOpen);
      toggle?.classList.toggle('is-active', bucketFilterOpen);
      toggle?.setAttribute('aria-expanded', String(bucketFilterOpen));
  }

  function bindBucketNavbarFilter() {
      const panel = document.getElementById('otpBucketNavbarFilterPanel');
      const toggle = document.getElementById('otpBucketNavbarFilterToggle');
      const close = document.getElementById('otpBucketNavbarFilterClose');
      if (!panel || !toggle || toggle.dataset.bound === '1') return;
      toggle.dataset.bound = '1';
      toggle.addEventListener('click', () => {
          bucketFilterOpen = !bucketFilterOpen;
          applyBucketFilterState();
      });
      close?.addEventListener('click', () => {
          bucketFilterOpen = false;
          applyBucketFilterState();
      });
      document.addEventListener('click', event => {
          if (bucketFilterOpen && !panel.contains(event.target) && !toggle.contains(event.target)) {
              bucketFilterOpen = false;
              applyBucketFilterState();
          }
      });
  }

  window.addEventListener('DOMContentLoaded', async () => {
    bindBucketNavbarFilter();
    applyBucketFilterState();
    const nominal = document.getElementById('opt_nominal_otp_bucket');
    if (nominal) nominal.value = 'saldo_bank';

    const today = new Date();
    document.getElementById('harian_date_otp').value = today.toISOString().split('T')[0];
    
    const lastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
    const pad = (n) => n < 10 ? '0'+n : n;
    document.getElementById('closing_date_otp').value = `${lastMonth.getFullYear()}-${pad(lastMonth.getMonth()+1)}-${pad(lastMonth.getDate())}`;

    const user = (window.getUser && window.getUser()) || null;
    let uKode = user?.kode ? String(user.kode).padStart(3,'0') : '000';
    if(uKode === '099') uKode = '000'; 

    const optKantor = document.getElementById('opt_kantor_otp');

    if (uKode === '000') {
        await loadCabangProg();
        optKantor.value = ""; 
    } else {
        optKantor.innerHTML = `<option value="${uKode}">CABANG ${uKode}</option>`;
        optKantor.value = uKode;
        optKantor.disabled = true; 
        optKantor.classList.add('bg-slate-50');
    }

    await handleCabangChange(true);
    fetchProgKredit();
  });

  async function handleCabangChange(isInit = false) {
      const cabangVal = document.getElementById('opt_kantor_otp').value;
      const lblSub = document.getElementById('lbl_sub_otp');
      const optSub = document.getElementById('opt_sub_otp');

      if (cabangVal === "" || cabangVal === "000") {
          lblSub.innerText = "KORWIL";
          optSub.innerHTML = `
              <option value="">ALL KORWIL</option>
              <option value="SEMARANG">SEMARANG</option>
              <option value="SOLO">SOLO</option>
              <option value="BANYUMAS">BANYUMAS</option>
              <option value="PEKALONGAN">PEKALONGAN</option>
          `;
      } else {
          lblSub.innerText = "KANKAS";
          optSub.innerHTML = '<option value="">ALL KANKAS</option>';
          await loadKankasProg(cabangVal);
      }
      if (!isInit) triggerAutoRefresh();
  }

  function triggerAutoRefresh() {
      if(window.innerWidth < 1280) {
          bucketFilterOpen = false;
          applyBucketFilterState();
      }
      fetchProgKredit();
  }

  async function loadCabangProg() {
    const optKantor = document.getElementById('opt_kantor_otp');
    try {
        const res = await fetch(API_KODE, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
        const json = await res.json();
        let html = `<option value="">ALL | SEMUA CABANG</option>`;
        (json.data || []).filter(x => x.kode_kantor && x.kode_kantor !== '000').forEach(it => {
            html += `<option value="${String(it.kode_kantor).padStart(3,'0')}">${String(it.kode_kantor).padStart(3,'0')} - ${it.nama_kantor}</option>`;
        });
        optKantor.innerHTML = html;
    } catch(e){}
  }

  async function loadKankasProg(kodeCabang) {
      const optSub = document.getElementById('opt_sub_otp');
      try {
          const payload = { type: 'kode_kankas', kode_kantor: kodeCabang };
          const r = await fetch(API_KODE, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          let h = '<option value="">ALL KANKAS</option>';
          if(j.data && Array.isArray(j.data)) j.data.forEach(x => { h += `<option value="${x.kode_group1}">${x.deskripsi_group1 || x.kode_group1}</option>`; });
          optSub.innerHTML = h;
      } catch(err) {}
  }

  async function fetchProgKredit() {
      const loading = document.getElementById('loadingProg');
      updateOtpBucketTitle();
      const cabangVal = document.getElementById('opt_kantor_otp').value;
      const subVal = document.getElementById('opt_sub_otp').value;
      
      let reqKorwil = ""; let reqKankas = "";
      if (cabangVal === "" || cabangVal === "000") reqKorwil = subVal; 
      else reqKankas = subVal; 

      const payload = { 
          type: "otp_fe", 
          closing_date: document.getElementById('closing_date_otp').value,
          harian_date: document.getElementById('harian_date_otp').value,
          hitung_berdasarkan: document.getElementById('opt_nominal_otp_bucket')?.value === 'baki_debet' ? 'baki_debet' : 'saldo_bank',
          type_bucket: document.getElementById('type_bucket_otp').value,
          include_127: document.getElementById('chk_127_bucket')?.checked === true,
          kode_kantor: cabangVal,
          korwil: reqKorwil,
          kode_kankas: reqKankas
      };

      loading.classList.remove('hidden');
      
      try {
          const res = await fetch(API_RR, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const json = await res.json();
          if(json.status !== 200) throw new Error(json.message);

          window.progDataRaw = json.data?.data || [];
          window.progGtRaw = json.data?.grand_total || null;

          renderProgTotal(window.progGtRaw);
          renderProgTable(window.progDataRaw);
      } catch(e) { 
          document.getElementById('bodyProg').innerHTML = `<tr><td colspan="7" class="text-center py-10 text-red-500 font-bold uppercase tracking-widest">${e.message || 'Error Load Data'}</td></tr>`;
          document.getElementById('totalProg').innerHTML = '';
      } finally { loading.classList.add('hidden'); }
  }

  function renderProgTotal(gt) {
      const tbodyTotal = document.getElementById('totalProg');
      tbodyTotal.innerHTML = '';
      if (!gt) return;
      
      tbodyTotal.innerHTML = `
        <tr class="sticky-total">
            <td class="col-kategori text-center text-slate-800 uppercase tracking-widest text-[11px] leading-tight pt-3">TOTAL</td>
            <td class="text-right font-black text-blue-800 border-r border-slate-300">
                <a href="#" onclick="openModalProg('ALL', 'ALL'); return false;" class="hover:underline">${fmt(gt.m1_os)}</a>
                <div class="text-[9px] text-slate-500 font-semibold">${fmt(gt.m1_noa)} NOA</div>
            </td>
            
            <td class="border-r border-slate-300 border-b-2 border-b-emerald-500">
                <a href="#" onclick="openModalProg('ALL', 'BTC'); return false;" class="text-emerald-600 hover:underline hover:text-emerald-800">${fmt(gt.btc_os)}</a>
                <div class="text-[9px] text-emerald-600/70 font-semibold">${fmt(gt.btc_noa)} NOA | ${fmt2(gt.btc_pct)}%</div>
            </td>

            <td class="border-r border-slate-300 border-b-2 border-b-teal-400">
                <a href="#" onclick="openModalProg('ALL', 'BACKFLOW'); return false;" class="text-teal-600 hover:underline hover:text-teal-800">${fmt(gt.backflow_os)}</a>
                <div class="text-[9px] text-teal-600/70 font-semibold">${fmt(gt.backflow_noa)} NOA | ${fmt2(gt.backflow_pct)}%</div>
            </td>

            <td class="border-r border-slate-300 border-b-2 border-b-orange-400">
                <a href="#" onclick="openModalProg('ALL', 'STAY'); return false;" class="text-orange-500 hover:underline hover:text-orange-700">${fmt(gt.stay_os)}</a>
                <div class="text-[9px] text-orange-500/70 font-semibold">${fmt(gt.stay_noa)} NOA | ${fmt2(gt.stay_pct)}%</div>
            </td>

            <td class="border-r border-slate-300 border-b-2 border-b-red-500">
                <a href="#" onclick="openModalProg('ALL', 'MIGRASI'); return false;" class="text-red-600 hover:underline hover:text-red-800">${fmt(gt.migrasi_os)}</a>
                <div class="text-[9px] text-red-600/70 font-semibold">${fmt(gt.migrasi_noa)} NOA | ${fmt2(gt.migrasi_pct)}%</div>
            </td>

            <td class="border-r border-slate-300 border-b-2 border-b-indigo-400">
                <span class="text-indigo-600 font-black">${fmt(gt.angsuran_os)}</span>
            </td>

            <td class="border-b-2 border-b-slate-400">
                <a href="#" onclick="openModalProg('ALL', 'RUNOFF'); return false;" class="text-slate-600 hover:underline hover:text-slate-800">${fmt(gt.runoff_os)}</a>
                <div class="text-[9px] text-slate-400 font-semibold">${fmt(gt.runoff_noa)} NOA | ${fmt2(gt.runoff_pct)}%</div>
            </td>
        </tr>`;
  }

  function renderProgTable(rows) {
      const tbody = document.getElementById('bodyProg');
      tbody.innerHTML = '';
      if (rows.length === 0) return tbody.innerHTML = `<tr><td colspan="8" class="text-center py-12 text-slate-400 font-medium">Data tidak ditemukan.</td></tr>`;
      
      let html = '';
      rows.forEach(r => {
          if(r.m1_os <= 0) return; 
          
          html += `
            <tr class="transition border-b border-slate-200 h-[50px]">
                <td class="col-kategori font-bold text-slate-700 text-xs">${r.tgl}</td>
                
                <td class="text-right font-bold text-blue-700 border-r border-slate-200">
                    <a href="#" onclick="openModalProg(${r.tgl}, 'ALL'); return false;" class="hover:underline">${fmt(r.m1_os)}</a>
                    <div class="text-[9px] text-slate-400 font-medium">${fmt(r.m1_noa)} NOA</div>
                </td>

                <td class="text-right font-semibold text-emerald-600 border-r border-slate-200">
                    <a href="#" onclick="openModalProg(${r.tgl}, 'BTC'); return false;" class="hover:underline">${fmt(r.btc_os)}</a>
                    <div class="text-[8.5px] text-slate-400 font-medium">${fmt(r.btc_noa)} NOA | ${fmt2(r.btc_pct)}%</div>
                </td>

                <td class="text-right font-semibold text-teal-600 border-r border-slate-200">
                    <a href="#" onclick="openModalProg(${r.tgl}, 'BACKFLOW'); return false;" class="hover:underline">${fmt(r.backflow_os)}</a>
                    <div class="text-[8.5px] text-slate-400 font-medium">${fmt(r.backflow_noa)} NOA | ${fmt2(r.backflow_pct)}%</div>
                </td>

                <td class="text-right font-semibold text-orange-500 border-r border-slate-200">
                    <a href="#" onclick="openModalProg(${r.tgl}, 'STAY'); return false;" class="hover:underline">${fmt(r.stay_os)}</a>
                    <div class="text-[8.5px] text-slate-400 font-medium">${fmt(r.stay_noa)} NOA | ${fmt2(r.stay_pct)}%</div>
                </td>

                <td class="text-right font-semibold text-red-500 border-r border-slate-200">
                    <a href="#" onclick="openModalProg(${r.tgl}, 'MIGRASI'); return false;" class="hover:underline">${fmt(r.migrasi_os)}</a>
                    <div class="text-[8.5px] text-slate-400 font-medium">${fmt(r.migrasi_noa)} NOA | ${fmt2(r.migrasi_pct)}%</div>
                </td>

                <td class="text-right font-semibold text-indigo-600 border-r border-slate-200">
                    ${fmt(r.angsuran_os)}
                </td>

                <td class="text-right font-semibold text-slate-600">
                    <a href="#" onclick="openModalProg(${r.tgl}, 'RUNOFF'); return false;" class="hover:underline">${fmt(r.runoff_os)}</a>
                    <div class="text-[8.5px] text-slate-400 font-medium">${fmt(r.runoff_noa)} NOA | ${fmt2(r.runoff_pct)}%</div>
                </td>
            </tr>`;
      });
      tbody.innerHTML = html;
  }

  async function loadKankasDetailProg(kodeCabang) {
      const optKankas = document.getElementById('opt_kankas_prog_modal');
      const optAo = document.getElementById('opt_ao_prog_modal');
      optKankas.innerHTML = '<option value="">SEMUA KANKAS</option>';
      optAo.innerHTML = '<option value="">ALL AO</option>';
      if (!kodeCabang || kodeCabang === '000') return; 

      try {
          const payload = { type: 'kode_kankas', kode_kantor: kodeCabang };
          const r = await fetch(API_KODE, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          let h = '<option value="">SEMUA KANKAS</option>';
          if(j.data) j.data.forEach(x => { h += `<option value="${x.kode_group1}">${x.deskripsi_group1 || x.kode_group1}</option>`; });
          optKankas.innerHTML = h;
          
          const mainKankas = document.getElementById('opt_sub_otp').value;
          if(mainKankas && document.getElementById('lbl_sub_otp').innerText === "KANKAS") {
              optKankas.value = mainKankas;
          }
      } catch(err) {}

      // Load AO list from actual detail data (fetch all without AO filter to get unique AOs)
      try {
          const payloadAo = { 
              ...currentProgDetailParams, 
              kode_ao: '', 
              search: '', 
              page: 1, 
              limit: 5000 
          };
          const rAo = await fetch(API_RR, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payloadAo) });
          const jAo = await rAo.json();
          let h2 = '<option value="">ALL AO</option>';
          if(jAo.status === 200 && jAo.data?.data) {
              const aoMap = {};
              jAo.data.data.forEach(row => {
                  const kodeAo = row.kode_group2 || '';
                  const namaAo = row.nama_ao || kodeAo;
                  if(kodeAo && kodeAo !== '-' && !aoMap[kodeAo]) {
                      aoMap[kodeAo] = namaAo;
                  }
              });
              // Sort AO names alphabetically
              Object.entries(aoMap).sort((a, b) => a[1].localeCompare(b[1])).forEach(([kode, nama]) => {
                  h2 += `<option value="${kode}">${nama}</option>`;
              });
          }
          optAo.innerHTML = h2;
      } catch(err) {}
  }

  async function openModalProg(tglParam, statusParam) {
      const cabangVal = document.getElementById('opt_kantor_otp').value;
      const subVal = document.getElementById('opt_sub_otp').value;
      const typeB = document.getElementById('type_bucket_otp').value;

      let reqKorwil = ""; let reqKankas = "";
      if (cabangVal === "" || cabangVal === "000") reqKorwil = subVal; 
      else reqKankas = subVal; 

      currentProgDetailParams = { 
          type: "detail_otp_fe", 
          closing_date: document.getElementById('closing_date_otp').value,
          harian_date: document.getElementById('harian_date_otp').value,
          hitung_berdasarkan: document.getElementById('opt_nominal_otp_bucket')?.value === 'baki_debet' ? 'baki_debet' : 'saldo_bank',
          type_bucket: typeB,
          include_127: document.getElementById('chk_127_bucket')?.checked === true,
          kode_kantor: cabangVal,
          korwil: reqKorwil,
          kode_kankas: reqKankas,
          status: statusParam,
          limit: 20
      };

      if(tglParam !== 'ALL') currentProgDetailParams.tgl_tagih = tglParam;

      document.getElementById('mdlTitleProg').textContent = `Status: ${statusParam}`;
      document.getElementById('mdlSubTitleProg').textContent = `Tgl JT: ${tglParam === 'ALL' ? 'Semua Tgl' : tglParam} | Bucket: ${typeB.toUpperCase()}`;
      document.getElementById('modalDetailProg').classList.remove('hidden');

      document.getElementById('opt_kankas_prog_modal').value = "";
      await loadKankasDetailProg(cabangVal);
      loadDetailProgPage(1);
  }

  function closeModalProg() { document.getElementById('modalDetailProg').classList.add('hidden'); }

  async function loadDetailProgPage(page) {
      const loading = document.getElementById('loadingModalProg'); 
      const tbody = document.getElementById('bodyModalProg'); 
      const info = document.getElementById('pageInfoProg');
      
      loading.classList.remove('hidden'); 
      tbody.innerHTML = '';

      try {
          const kankasVal = document.getElementById('opt_kankas_prog_modal').value;
          const aoVal = document.getElementById('opt_ao_prog_modal').value;
          const searchVal = document.getElementById('opt_search_prog_modal').value.trim();
          const payload = { ...currentProgDetailParams, kode_kankas: kankasVal || currentProgDetailParams.kode_kankas, kode_ao: aoVal, search: searchVal, page: page };
          
          const res = await fetch(API_RR, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const json = await res.json();
          
          if(json.status !== 200) throw new Error(json.message || "Gagal memuat detail");
          
          const rows = json.data?.data || [];
          const meta = json.data?.pagination || { total_records:0, total_pages:1 };

          currentProgPage = page; 
          currentProgTotalPages = meta.total_pages;

          if(rows.length === 0) {
              tbody.innerHTML = `<tr><td colspan="21" class="py-20 text-center text-slate-500 italic">Tidak ada data debitur.</td></tr>`;
              info.innerText = `0 Data`;
          } else {
              let html = '';
              rows.forEach(r => {
                  let badge = 'bg-slate-100 text-slate-700';
                  if(r.status_ket.includes('RUNOFF')) badge = 'bg-slate-200 text-slate-700';
                  else if(r.status_ket.includes('BTC')) badge = 'bg-emerald-50 text-emerald-700';
                  else if(r.status_ket.includes('BACKFLOW')) badge = 'bg-teal-50 text-teal-700';
                  else if(r.status_ket.includes('STAY')) badge = 'bg-amber-50 text-amber-700';
                  else if(r.status_ket.includes('MIGRASI')) badge = 'bg-red-50 text-red-700';

                  let txtTabungan = fmt(r.tabungan);
                  if (r.status_tabungan === 'Aman') txtTabungan = `<span class="font-bold">${txtTabungan}</span>`;

                  const textAlamat = r.alamat ? (r.alamat.length > 25 ? r.alamat.substring(0, 25) + '...' : r.alamat) : '-';

                  // Format tanggal transaksi
                  const fmtTgl = (d) => { if(!d) return '-'; const dt = new Date(d); return dt.toLocaleDateString('id-ID', {day:'2-digit', month:'short'}); };
                  const tglByrLalu = fmtTgl(r.tgl_trans_lalu);
                  const tglByrSkr = fmtTgl(r.tgl_trans_sekarang);
                  const byrLalu = r.total_bayar_lalu > 0 ? fmt(r.total_bayar_lalu) : '-';
                  const byrSkr = r.total_bayar_sekarang > 0 ? fmt(r.total_bayar_sekarang) : '-';

                  html += `
                    <tr class="border-b border-slate-100 hover:bg-slate-50/50">
                        <td class="mod-td-rek hidden md:table-cell px-1 md:px-2 font-mono text-slate-500 border-r border-slate-100">${r.no_rekening}</td>
                        <td class="mod-td-nas px-1 md:px-3 font-semibold text-slate-800 truncate border-r border-slate-100" title="${r.nama_nasabah}">${r.nama_nasabah}</td>
                        <td class="px-1 md:px-2 text-center font-bold text-slate-600 border-r border-slate-100">${r.kolektibilitas || '-'}</td>
                        <td class="px-2 md:px-3 truncate text-slate-600 border-r border-slate-100" title="${r.alamat || ''}">${textAlamat}</td>
                        <td class="px-1 md:px-2 text-center font-mono text-slate-500 border-r border-slate-100">${r.no_hp || '-'}</td>
                        <td class="px-1 md:px-2 text-center text-slate-600 border-r border-slate-100">${r.kankas || '-'}</td>
                        <td class="px-1 md:px-2 text-center text-slate-600 border-r border-slate-100">${r.nama_ao || '-'}</td>
                        <td class="px-1 md:px-2 text-center font-medium border-r border-slate-100">${r.tgl_jatuh_tempo}</td>
                        <td class="px-2 md:px-3 text-right font-bold text-indigo-700 border-r border-slate-100">${fmt(r.jml_pinjaman)}</td>
                        <td class="px-2 md:px-3 text-right font-bold text-slate-800 border-r border-slate-100">${fmt(r.os_curr)}</td>
                        <td class="px-2 md:px-3 text-right text-slate-600 border-r border-slate-100">${txtTabungan}</td>
                        <td class="px-2 md:px-3 text-right text-slate-600 border-r border-slate-100">${fmt(r.tunggakan_pokok)}</td>
                        <td class="px-2 md:px-3 text-right text-slate-600 border-r border-slate-100">${fmt(r.tunggakan_bunga)}</td>
                        <td class="px-1 md:px-2 text-center text-slate-600 border-r border-slate-100">${r.dpd_pokok || 0}</td>
                        <td class="px-1 md:px-2 text-center text-slate-600 border-r border-slate-100">${r.dpd_bunga || 0}</td>
                        <td class="px-2 md:px-3 text-center font-bold text-slate-700 border-r border-slate-100">${r.dpd_curr}</td>
                        <td class="px-2 md:px-3 text-center"><span class="px-1.5 py-0.5 rounded text-[9px] font-bold ${badge}">${r.status_ket}</span></td>
                        <td class="px-1 md:px-2 text-center text-slate-500 border-r border-slate-100">${tglByrLalu}</td>
                        <td class="px-2 md:px-3 text-right text-slate-600 border-r border-slate-100">${byrLalu}</td>
                        <td class="px-1 md:px-2 text-center text-slate-500 border-r border-slate-100">${tglByrSkr}</td>
                        <td class="px-2 md:px-3 text-right text-slate-600">${byrSkr}</td>
                    </tr>`;
              });
              tbody.innerHTML = html;

              const start = ((page - 1) * 20) + 1;
              const end = Math.min(page * 20, meta.total_records);
              info.innerText = `${start}-${end} / ${fmt(meta.total_records)}`;
          }
          document.getElementById('btnPrevProg').disabled = page <= 1;
          document.getElementById('btnNextProg').disabled = page >= meta.total_pages;
      } catch(err){ 
          tbody.innerHTML = `<tr><td colspan="21" class="py-16 text-center text-red-500 font-bold">${err.message}</td></tr>`;
      } finally { loading.classList.add('hidden'); }
  }

  function changePageProg(step) {
      const n = currentProgPage + step; 
      if (n > 0 && n <= currentProgTotalPages) loadDetailProgPage(n); 
  }

  async function exportDetailProgExcel() {
      const btn = event.target.closest('button'); const originalHTML = btn.innerHTML;
      btn.innerHTML = `<span class="text-[9px] font-bold">...</span>`; btn.disabled = true;

      try {
          const kankasVal = document.getElementById('opt_kankas_prog_modal').value;
          const payload = { ...currentProgDetailParams, kode_kankas: kankasVal || currentProgDetailParams.kode_kankas, page: 1, limit: 20000 }; 
          
          const res = await fetch(API_RR, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const json = await res.json();
          if(json.status !== 200) throw new Error(json.message);
          
          const rows = json.data?.data || [];
          if(rows.length === 0) { alert("Tidak ada data untuk diexport"); return; }

          let tableHtml = `<table border="1">
            <tr>
              <th>No Rekening</th><th>Nama Nasabah</th><th>Kolektibilitas</th><th>Alamat</th><th>No HP</th>
              <th>Kankas</th><th>Nama AO</th><th>Tgl Realisasi</th><th>Tgl Jatuh Tempo</th>
              <th>Plafon</th><th>OS (Baki Debet Curr)</th><th>Tabungan</th>
              <th>Tgl Byr Lalu</th><th>Total Byr Lalu</th>
              <th>Tgl Byr Sekarang</th><th>Total Byr Sekarang</th>
              <th>Tunggakan Pokok</th><th>Tunggakan Bunga</th>
              <th>DPD Pokok</th><th>DPD Bunga</th><th>DPD (Curr)</th><th>Status Migrasi</th>
            </tr>`;
          
          rows.forEach(r => {
              tableHtml += `<tr>
                <td style="mso-number-format:'\\@';">${r.no_rekening}</td>
                <td>${r.nama_nasabah}</td>
                <td>${r.kolektibilitas || ''}</td>
                <td>${r.alamat || ''}</td>
                <td style="mso-number-format:'\\@';">${r.no_hp || ''}</td>
                <td>${r.kankas || ''}</td>
                <td>${r.nama_ao || ''}</td>
                <td>${r.tgl_realisasi || ''}</td>
                <td>${r.tgl_jatuh_tempo || ''}</td>
                <td>${r.jml_pinjaman || 0}</td>
                <td>${r.os_curr}</td>
                <td>${r.tabungan}</td>
                <td>${r.tgl_trans_lalu || ''}</td>
                <td>${r.total_bayar_lalu || 0}</td>
                <td>${r.tgl_trans_sekarang || ''}</td>
                <td>${r.total_bayar_sekarang || 0}</td>
                <td>${r.tunggakan_pokok || 0}</td>
                <td>${r.tunggakan_bunga || 0}</td>
                <td>${r.dpd_pokok || 0}</td>
                <td>${r.dpd_bunga || 0}</td>
                <td>${r.dpd_curr}</td>
                <td>${r.status_ket}</td>
              </tr>`;
          });
          tableHtml += `</table>`;

          const blob = new Blob([tableHtml], { type: 'application/vnd.ms-excel' });
          const a = document.createElement('a');
          a.href = window.URL.createObjectURL(blob);
          const fTgl = currentProgDetailParams.tgl_tagih ? `_TGL${currentProgDetailParams.tgl_tagih}` : '';
          a.download = `Detail_OTP_${currentProgDetailParams.status}${fTgl}.xls`;
          document.body.appendChild(a); a.click(); document.body.removeChild(a);
      } catch(e) { alert("Gagal export: " + e.message); } 
      finally { btn.innerHTML = originalHTML; btn.disabled = false; }
  }

  function exportProgExcel() {
      const rows = window.progDataRaw || [];
      const gt = window.progGtRaw || null;
      if(rows.length === 0 || !gt) return alert("Data Kosong!");

      // URUTAN BARU: BTC, BACKFLOW, STAY, MIGRASI, RUNOFF
      let tableHtml = `<table border="1">
        <tr>
            <th rowspan="2">TGL JATUH TEMPO</th>
            <th rowspan="2">TARGET (M-1) OS</th>
            <th rowspan="2">TARGET (M-1) NOA</th>
            <th colspan="10">STATUS MIGRASI (M)</th>
        </tr>
        <tr>
            <th>BTC (OS)</th><th>BTC (NOA)</th>
            <th>BACKFLOW (OS)</th><th>BACKFLOW (NOA)</th>
            <th>STAY (OS)</th><th>STAY (NOA)</th>
            <th>MIGRASI (OS)</th><th>MIGRASI (NOA)</th>
            <th>RUN OFF (OS)</th><th>RUN OFF (NOA)</th>
        </tr>`;

      tableHtml += `<tr>
        <td><b>TOTAL</b></td>
        <td><b>${gt.m1_os}</b></td><td><b>${gt.m1_noa}</b></td>
        <td>${gt.btc_os}</td><td>${gt.btc_noa}</td>
        <td>${gt.backflow_os}</td><td>${gt.backflow_noa}</td>
        <td>${gt.stay_os}</td><td>${gt.stay_noa}</td>
        <td>${gt.migrasi_os}</td><td>${gt.migrasi_noa}</td>
        <td>${gt.runoff_os}</td><td>${gt.runoff_noa}</td>
      </tr>`;

      rows.forEach(r => {
          if(r.m1_os <= 0) return;
          tableHtml += `<tr>
            <td>${r.tgl}</td>
            <td>${r.m1_os}</td><td>${r.m1_noa}</td>
            <td>${r.btc_os}</td><td>${r.btc_noa}</td>
            <td>${r.backflow_os}</td><td>${r.backflow_noa}</td>
            <td>${r.stay_os}</td><td>${r.stay_noa}</td>
            <td>${r.migrasi_os}</td><td>${r.migrasi_noa}</td>
            <td>${r.runoff_os}</td><td>${r.runoff_noa}</td>
          </tr>`;
      });
      tableHtml += `</table>`;

      const blob = new Blob([tableHtml], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      a.download = `Rekap_OTP_Migration_${document.getElementById('harian_date_otp').value}.xls`;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
  }
</script>

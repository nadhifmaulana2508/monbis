<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  /* Animasi Modal */
  @keyframes scaleUp { from { transform: scale(0.98); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.15s ease-out forwards; }

  /* ========================================================
     MAGIC STICKY TABLE UTAMA (CLEAN LOOK)
     ======================================================== */
  #tabelRekapRR th { position: sticky !important; z-index: 10; }
  #tabelRekapRR thead th.head-row { cursor: pointer; transition: background 0.2s; }
  #tabelRekapRR thead th.head-row:hover { filter: brightness(0.95); }
  
  .rr-row-1 { height: 36px; }
  .rr-row-2 { height: 34px; }
  .rr-row-tot { height: 42px; }
  @media (min-width: 768px) {
      .rr-row-1 { height: 44px; }
      .rr-row-2 { height: 40px; }
      .rr-row-tot { height: 48px; }
  }

  #tabelRekapRR thead tr:nth-child(1) th { top: 0 !important; z-index: 30; }
  #tabelRekapRR thead tr:nth-child(2) th { top: 36px !important; z-index: 29; }
  #tabelRekapRR thead tr.sticky-total th { top: 70px !important; z-index: 40 !important; box-shadow: 0 2px 4px -1px rgba(0,0,0,0.05); }
  @media (min-width: 768px) { 
      #tabelRekapRR thead tr:nth-child(2) th { top: 44px !important; } 
      #tabelRekapRR thead tr.sticky-total th { top: 84px !important; }
  }

  #tabelRekapRR th.sticky.left-0 { z-index: 50 !important; }
  #tabelRekapRR td.sticky.left-0 { position: sticky !important; left: 0; z-index: 20; background-color: #fff; box-shadow: 1px 0 0 #cbd5e1; }
  #tabelRekapRR tr.sticky-total th.sticky.left-0 { z-index: 45 !important; background-color: #f1f5f9 !important; }

  /* ========================================================
     TABEL MODAL DETAIL RR (FIX STICKY & CLEAN TAMPILAN)
     ======================================================== */
  #tableExportRR { border-collapse: separate; border-spacing: 0; min-width: 100%; }
  #tableExportRR th, #tableExportRR td { background-clip: padding-box; background-color: #fff; }
  #tableExportRR thead th { position: sticky !important; top: 0 !important; z-index: 40 !important; background-color: #f1f5f9 !important; box-shadow: inset 0 -1px 0 #cbd5e1; }

  /* NORMAL MODAL STICKY */
  .col-rek { position: sticky !important; left: 0 !important; min-width: 100px; max-width: 100px; box-shadow: inset -1px 0 0 #e2e8f0; }
  .col-nas { position: sticky !important; left: 100px !important; min-width: 160px; max-width: 160px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1), inset -1px 0 0 #e2e8f0; }
  
  /* LUNAS MODAL STICKY */
  .col-nas-lunas { position: sticky !important; left: 0 !important; min-width: 160px; max-width: 160px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1), inset -1px 0 0 #e2e8f0; }

  @media (min-width: 768px) { 
      .col-rek { min-width: 120px; max-width: 120px; }
      .col-nas { left: 120px !important; min-width: 250px; max-width: 250px; } 
      .col-nas-lunas { min-width: 250px; max-width: 250px; } 
  }

  #bodyModalRR td.col-rek, #bodyModalRR td.col-nas, #bodyModalRR td.col-nas-lunas { z-index: 20 !important; background-color: #fff !important; }
  #headModalRR th.col-rek, #headModalRR th.col-nas, #headModalRR th.col-nas-lunas { z-index: 50 !important; background-color: #f1f5f9 !important; }

  tbody tr:hover td { background-color: #f8fafc !important; }
  #bodyModalRR tr:hover td.col-rek, #bodyModalRR tr:hover td.col-nas, #bodyModalRR tr:hover td.col-nas-lunas { filter: brightness(0.98); }
  
  #tabelRekapRR td { font-size: 12px; line-height: 1.15; }
  #tableExportRR td { font-size: 11px; line-height: 1.2; }
  @media (min-width: 768px) {
    #tabelRekapRR td { font-size: 13px; }
    #tableExportRR td { font-size: 12px; }
  }

  /* Form Inputs */
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; background:#fff; outline:none; transition: border 0.2s; height: 34px; font-weight: 600; font-size: 13px; color: #334155; }
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f1f5f9; color: #64748b; cursor: not-allowed; }
  .lbl { font-size:10px; color:#475569; font-weight:700; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap; }
  @media (min-width: 768px) { .inp { border-radius: 8px; padding:0 10px; } }
  
  .field { display:flex; flex-direction:column; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }

  input[type="date"]::-webkit-inner-spin-button, input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
  input[type="date"] { -moz-appearance: textfield; }
  
  .filter-collapsed { max-height: 0; overflow: hidden; opacity: 0; padding-top: 0; padding-bottom: 0; margin-top: 0; }
  .filter-expanded { max-height: 500px; overflow: visible; opacity: 1; }
  .filter-transition { transition: max-height 0.3s ease, opacity 0.2s ease, padding 0.2s ease, margin 0.2s ease; }
  
  @media (min-width: 1280px) {
    .filter-transition { max-height: none !important; opacity: 1 !important; overflow: visible !important; }
  }

  /* ========================================================
     UI POLISH V2 - clean, compact, responsive
     ======================================================== */
  .otp-shell { min-height:0; }
  .otp-card { background:#fff; border:1px solid #e2e8f0; box-shadow:0 2px 4px rgba(0,0,0,0.02); overflow:visible !important; }
  
  .otp-info-root { position:relative; z-index:200000 !important; }
  .otp-info-root .otp-help-panel { z-index:200001 !important; max-width:calc(100vw - 28px); box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); }
  .otp-info-root:hover .otp-help-panel { display:flex !important; }

  #tabelRekapRR th { background:#f1f5f9 !important; color:#1e293b !important; }
  #tabelRekapRR td, #tableExportRR td { color:#334155; }
  #tabelRekapRR a, #tableExportRR a { color:#2563eb !important; text-decoration:none; }
  #tabelRekapRR a:hover, #tableExportRR a:hover { color:#1d4ed8 !important; text-decoration:underline; }
  
  #tabelRekapRR td[class*="bg-blue"], #tabelRekapRR td[class*="bg-green"], #tabelRekapRR td[class*="bg-red"], #tabelRekapRR td[class*="bg-purple"], #tabelRekapRR td[class*="bg-amber"],
  #tableExportRR td[class*="bg-blue"], #tableExportRR td[class*="bg-green"], #tableExportRR td[class*="bg-red"], #tableExportRR td[class*="bg-cyan"], #tableExportRR td[class*="bg-amber"] { background-color:transparent !important; }
  
  #tabelRekapRR tr:hover td, #tableExportRR tr:hover td { background-color:#f8fafc !important; }

  .otp-modal-head { box-shadow:0 1px 0 rgba(148,163,184,.24); }
  #modalDetailRR > .relative { border:1px solid rgba(226,232,240,.9); box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); }
  #tableExportRR thead th { background:#f1f5f9 !important; color:#334155 !important; border-bottom: 1px solid #cbd5e1; }
  
  @media (min-width:1280px) {
    .otp-card { display:flex !important; flex-direction: row; align-items:center !important; gap:16px !important; padding:12px 16px !important; }
    .otp-title-wrap { flex: 0 0 auto; width: auto; }
    #filterWrapperMain { width:100% !important; margin:0 !important; padding:0 !important; border:0 !important; flex: 1 1 auto; display: flex; justify-content: flex-end; min-w:0; }
  }

  @media (max-width:640px) {
    .otp-shell { height:100dvh !important; padding:8px !important; }
    #tabelRekapRR { min-width:960px; }
    #tableExportRR { min-width:1180px; }
    .otp-modal-head .btn-icon span { display:none; }
    .otp-modal-head .btn-icon { width:36px; padding-left:0 !important; padding-right:0 !important; }
    .col-nas { min-width:170px !important; max-width:170px !important; left:0 !important; }
    .col-rek { display:none !important; }
    .col-nas-lunas { min-width:180px !important; max-width:180px !important; }
  }

  /* ========================================================
     READABILITY V5 - NON-BOLD CLEAN TEXT
     ======================================================== */
  #tabelRekapRR, #tableExportRR { font-variant-numeric: tabular-nums; }

  #tabelRekapRR thead th {
    background: #f1f5f9 !important;
    color: #1e293b !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: .02em !important;
    padding-top: 10px !important;
    padding-bottom: 10px !important;
    border-bottom: 1px solid #cbd5e1 !important;
  }
  
  #tabelRekapRR tbody td,
  #tabelRekapRR tbody th {
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #334155 !important;
  }
  
  #tabelRekapRR tbody td a {
    font-size: 13px !important;
    font-weight: 600 !important; 
  }

  #rowTotalRRAtas th, #rowTotalRRAtas a {
    font-size: 14px !important;
    font-weight: 700 !important;
    background: #f8fafc !important;
    color: #0f172a !important;
  }
  
  #tabelRekapRR [class*="text-[10px]"] {
    font-size: 11px !important;
    font-weight: 500 !important;
    color: #64748b !important;
  }
  #tabelRekapRR [class*="text-[10px]"] span {
    font-weight: 600 !important;
  }
  
  #tabelRekapRR tbody tr { height: 50px !important; }
  
  #tableExportRR thead th {
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: .02em !important;
    padding-top: 10px !important;
    padding-bottom: 10px !important;
  }
  
  #tableExportRR tbody td {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #334155 !important;
  }
  
  #tableExportRR tbody td a { font-weight: 600 !important; }
  
  .otp-help-panel { font-size: 12px !important; line-height: 1.45 !important; }
</style>

<div class="otp-shell max-w-[1920px] mx-auto px-2 md:px-4 py-3 md:py-6 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">
  
  <div class="otp-card relative z-[3000] flex-none mb-3 md:mb-4 w-full bg-white p-3 rounded-lg border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3 shrink-0">
    <div class="otp-title-wrap flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
        <div class="flex flex-col gap-0.5 md:gap-1 min-w-0 flex-1">
          <h1 class="text-base md:text-lg font-bold flex items-center gap-2 text-slate-800 whitespace-nowrap">
            <span class="p-1.5 bg-blue-600 text-white rounded shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v10"></path></svg>
            </span>
            <span id="otpTitle" class="otp-title truncate">OTP - ALL</span>
            <div class="relative group cursor-help ml-1 otp-info-root z-[200000]">
              <button type="button" aria-label="Informasi OTP" onclick="toggleOtpHelp(event)" class="inline-flex items-center justify-center rounded-full mt-0.5">
                <svg class="w-4 h-4 text-slate-400 hover:text-blue-600 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
              </button>
              <div id="otpHelpPanel" class="otp-help-panel absolute left-0 top-full mt-2 w-[280px] md:w-[350px] bg-white border border-slate-200 shadow-xl rounded-lg p-3 md:p-4 hidden group-hover:flex flex-col gap-2 z-[200001] text-xs font-normal text-slate-600 whitespace-normal">
                <div class="font-bold text-slate-800 mb-1 border-b pb-2 text-sm">Informasi OTP</div>
                <p><b>OTP</b> adalah monitoring ketepatan bayar debitur sesuai tanggal jatuh tempo sampai posisi harian/aktual.</p>
                <div class="grid grid-cols-1 gap-1.5 text-[10.5px] leading-relaxed">
                  <div class="rounded border border-slate-200 bg-slate-50 p-2"><b>Target M-1</b>: outstanding/rekening jatuh tempo dari closing bulan sebelumnya.</div>
                  <div class="rounded border border-slate-200 bg-slate-50 p-2"><b>OTP Lancar</b>: rekening yang masih lancar pada posisi aktual.</div>
                  <div class="rounded border border-slate-200 bg-slate-50 p-2"><b>Ditagih</b>: rekening prioritas yang belum memenuhi pembayaran.</div>
                </div>
                <p class="font-bold text-slate-800 border-t pt-2 mt-1"><b>% OTP</b> = (Lancar + Lunas + Angsuran) / Target M-1</p>
                
                <div class="font-bold text-slate-800 border-t pt-2 mt-1 mb-0.5">Indikator Warna % OTP:</div>
                <div class="flex flex-col gap-1 text-[11px]">
                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-emerald-100 border border-emerald-200"></span> <span class="font-semibold text-emerald-700">Optimal</span> (>= 90%)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-amber-100 border border-amber-200"></span> <span class="font-semibold text-amber-700">Monitoring</span> (70% - 89%)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-rose-100 border border-rose-200"></span> <span class="font-semibold text-rose-700">Atensi</span> (&lt; 70%)</div>
                </div>
              </div>
            </div>
          </h1>
        </div>

        <button type="button" onclick="toggleMainFilter()" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded flex items-center gap-1.5 shadow-sm transition font-bold text-xs whitespace-nowrap ml-2 shrink-0 hover:bg-slate-50">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            <span class="hidden sm:inline">Filter</span>
        </button>
    </div>

    <div id="filterWrapperMain" class="filter-transition w-full xl:w-auto xl:flex-1 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
        <form id="formFilterRR" class="w-full flex-1 min-w-0" onsubmit="event.preventDefault(); fetchRekapRR();">
            <div class="flex flex-wrap xl:flex-nowrap items-end gap-2 w-full justify-end">
                <div class="field shrink-0 w-[calc(50%-4px)] sm:w-[115px]">
                    <label class="lbl">CLOSING (M-1)</label>
                    <input type="date" id="closing_date" onchange="fetchRekapRR()" class="inp w-full" required>
                </div>
                <div class="field shrink-0 w-[calc(50%-4px)] sm:w-[115px]">
                    <label class="lbl">ACTUAL (HARIAN)</label>
                    <input type="date" id="harian_date" onchange="fetchRekapRR()" class="inp w-full" required>
                </div>
                <div class="w-px h-6 bg-slate-200 shrink-0 mx-0.5 hidden xl:block mb-1.5"></div>
                
                <div class="field flex-1 min-w-0 sm:min-w-[140px] xl:max-w-[180px]">
                    <label class="lbl">CABANG</label>
                    <select id="opt_kantor" class="inp w-full truncate" onchange="handleCabangChangeOtp()">
                        <option value="">Loading...</option>
                    </select>
                </div>
                <div class="field flex-1 min-w-0 sm:min-w-[120px] xl:max-w-[140px]">
                    <label id="lbl_sub_otp" class="lbl">KORWIL</label>
                    <select id="opt_sub_otp" class="inp w-full truncate" onchange="fetchRekapRR()">
                        <option value="">ALL KORWIL</option>
                    </select>
                </div>
                <div class="field shrink-0 w-[calc(50%-4px)] sm:w-[100px]">
                    <label class="lbl">DPD BUCKET</label>
                    <select id="opt_dpd_bucket" class="inp w-full" onchange="fetchRekapRR()">
                        <option value="all">ALL</option>
                        <option value="dpd0">DPD 0</option>
                        <option value="dpd1-30">DPD 1-30</option>
                    </select>
                </div>
                <div class="field flex-1 min-w-0 sm:min-w-[140px] xl:max-w-[160px]">
                    <label class="lbl">AO KREDIT</label>
                    <select id="opt_ao_otp" class="inp w-full truncate disabled:bg-slate-50 disabled:text-slate-400" onchange="fetchRekapRR()" disabled>
                        <option value="">PILIH CABANG DULU</option>
                    </select>
                </div>
                
                <div class="field shrink-0 w-[calc(25%-4px)] sm:w-[48px]">
                    <label class="lbl text-center w-full">KPP</label>
                    <div class="flex items-center justify-center h-[34px] px-2 bg-slate-50 border border-slate-200 rounded cursor-pointer hover:bg-slate-100 transition" onclick="document.getElementById('chk_127').click()">
                        <input type="checkbox" id="chk_127" class="w-3.5 h-3.5 text-blue-600 bg-white border-slate-300 rounded cursor-pointer" onclick="event.stopPropagation()" onchange="fetchRekapRR()">
                    </div>
                </div>
                
                <div class="field shrink-0 w-[calc(25%-4px)] sm:w-[40px]">
                    <label class="lbl opacity-0 hidden sm:block select-none">&nbsp;</label>
                    <button type="button" onclick="exportExcelRekapRR()" class="btn-icon h-[34px] w-full sm:w-[40px] bg-emerald-600 hover:bg-emerald-700 text-white rounded shadow-sm shrink-0">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
  </div>

  <div class="flex-1 min-h-0 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm relative flex flex-col">
    <div id="loadingRR" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-xs backdrop-blur-[1px]">
        <div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mb-3"></div>
        <span>Menyiapkan Matriks...</span>
    </div>
    <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table class="min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelRekapRR">
        <thead class="uppercase sticky top-0 z-50 select-none" id="headRR"></thead>
        <tbody id="bodyRR" class="divide-y divide-slate-100 bg-white group-tbody text-xs"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="modalDetailRR" class="fixed inset-0 hidden z-[9999] flex items-end md:items-center justify-center p-0 sm:p-4">
  <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModalRR()"></div>
  <div class="relative bg-white w-full h-[95vh] md:h-[92vh] max-w-[1700px] rounded-t-xl md:rounded-lg shadow-2xl flex flex-col overflow-hidden animate-scale-up">
    
    <div class="otp-modal-head flex flex-col bg-slate-50 border-b border-slate-200 shrink-0 w-full z-[80]">
        <div class="flex items-center justify-between px-4 py-3 gap-3 w-full">
            <div class="flex-1 min-w-0" id="modal-title-container">
              <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm md:text-lg leading-tight truncate">
                  <span class="w-1.5 h-5 bg-blue-600 rounded shrink-0"></span>
                  <span id="modalTitleRR" class="truncate">Detail Matriks OTP</span>
              </h3>
              <p class="text-[10px] text-slate-500 mt-1 ml-3 font-semibold tracking-wide uppercase truncate" id="modalSubTitleRR">...</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <div class="field hidden sm:block">
                    <select id="opt_kankas_modal" onchange="loadDetailPage(1)" class="inp h-[34px] w-[130px]"></select>
                </div>
                <div class="field hidden sm:block">
                    <select id="opt_ao_modal" onchange="loadDetailPage(1)" class="inp h-[34px] w-[130px]"></select>
                </div>

                <div class="relative hidden sm:block w-[220px]">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="search_nasabah" oninput="filterTableDetail()" class="w-full h-[34px] pl-9 pr-3 rounded border border-slate-200 bg-white text-xs font-semibold outline-none focus:border-blue-500" placeholder="Cari nama / rekening...">
                </div>
                <button onclick="downloadExcelFull(event)" class="btn-icon bg-emerald-600 hover:bg-emerald-700 text-white px-3 h-[34px] rounded shadow-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="ml-1.5 text-[10px] font-bold uppercase tracking-wide">Export</span>
                </button>
                <button onclick="closeModalRR()" class="w-[34px] h-[34px] flex items-center justify-center rounded bg-slate-200 hover:bg-rose-500 hover:text-white text-slate-500 transition font-black text-xl leading-none shrink-0">&times;</button>
            </div>
        </div>
        <div class="sm:hidden px-4 pb-3">
            <input type="text" id="search_nasabah_mobile" oninput="filterTableDetail()" class="w-full h-[36px] pl-3 pr-3 rounded border border-slate-200 bg-white text-[11px] outline-none focus:border-blue-500" placeholder="Cari nama / rekening...">
        </div>
    </div>

    <div class="flex-1 overflow-auto bg-white relative p-0 custom-scrollbar">
      <div id="loadingModalRR" class="hidden absolute inset-0 bg-white/90 z-[60] flex flex-col items-center justify-center text-blue-600 backdrop-blur-[1px]">
         <div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mb-2"></div>
         <span class="text-xs font-bold uppercase tracking-widest">Memuat Detail...</span>
      </div>
      
      <table class="w-max min-w-full text-center md:text-left text-slate-700 bg-white table-fixed" id="tableExportRR">
        <thead id="headModalRR" class="uppercase select-none"></thead>
        <tbody id="bodyModalRR" class="divide-y divide-slate-100 bg-white modal-tbody"></tbody>
      </table>
    </div>

    <div class="px-3 py-2 md:px-5 md:py-3 border-t bg-slate-50 flex justify-between items-center shrink-0">
      <span class="text-xs font-semibold text-slate-500 bg-white border px-2.5 py-1 rounded" id="pageInfoRR">0 Data</span>
      <div class="flex gap-2">
          <button id="btnPrevRR" onclick="changePageDetail(-1)" class="px-3 py-1.5 bg-white border border-slate-300 rounded text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50 transition shadow-sm">« Prev</button>
          <button id="btnNextRR" onclick="changePageDetail(1)" class="px-3 py-1.5 bg-white border border-slate-300 rounded text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50 transition shadow-sm">Next »</button>
      </div>
    </div>
  </div>
</div>

<script>
  function exportExcelRekapRR() {
      const rows = Array.isArray(rekapDataRaw) ? rekapDataRaw : [];
      const gt = rekapGtRaw;
      if (!rows.length && !gt) { alert('Tidak ada data untuk diexport.'); return; }
      let csv = 'TGL\tTARGET M-1\tNOA TARGET\tOTP LANCAR\tNOA LANCAR\tDITAGIH\tNOA DITAGIH\tLUNAS\tNOA LUNAS\tANGSURAN\tTOTAL BAYAR\tPERSEN\n';
      if (gt) {
          csv += `TOTAL\t${Math.round(gt.target_os||0)}\t${gt.target_noa||0}\t${Math.round(gt.lancar_os||0)}\t${gt.lancar_noa||0}\t${Math.round(gt.macet_os||0)}\t${gt.macet_noa||0}\t${Math.round(gt.lunas_os||0)}\t${gt.lunas_noa||0}\t${Math.round(gt.angsuran||0)}\t${Math.round(gt.total_bayar||0)}\t${gt.persen||0}%\n`;
      }
      rows.forEach(r => {
          // Hanya tulis persen ke excel jika nilainya bukan null/undefined
          const p = (r.persen !== null && r.persen !== undefined) ? `${r.persen}%` : '-';
          csv += `${r.tgl}\t${Math.round(r.target_os||0)}\t${r.target_noa||0}\t${Math.round(r.lancar_os||0)}\t${r.lancar_noa||0}\t${Math.round(r.macet_os||0)}\t${r.macet_noa||0}\t${Math.round(r.lunas_os||0)}\t${r.lunas_noa||0}\t${Math.round(r.angsuran||0)}\t${Math.round(r.total_bayar||0)}\t${p}\n`;
      });
      const blob = new Blob([csv], { type: 'application/vnd.ms-excel;charset=utf-8' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `OTP_Rekap_${document.getElementById('harian_date')?.value || 'harian'}.xls`;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
  }

  const API_RR_URL = './api/rr'; 
  const API_KODE_URL = './api/kode/'; 
  const nfID = new Intl.NumberFormat('id-ID');
  const fmt  = n => nfID.format(Number(n||0));
  const fmtDateID = s => {
      if(!s) return '-';
      const d = new Date(s);
      if (isNaN(d)) return String(s).slice(0, 10);
      return `${String(d.getDate()).padStart(2,'0')}-${String(d.getMonth()+1).padStart(2,'0')}-${d.getFullYear()}`;
  };
  
  let rekapDataRaw = [];
  let rekapGtRaw = null;
  let detailDataCache = [];

  const apiCall = async (url, opt = {}) => {
      const res = await fetch(url, opt);
      try {
          const json = await res.json();
          return { ok: res.ok, status: res.status, json: json };
      } catch (e) {
          throw new Error("Gagal parsing JSON.");
      }
  };

  let abortRR;
  let currentDetailParams = {};
  let currentDetailPage = 1;
  let currentDetailTotalPages = 1;
  let currentMode = 'NORMAL'; 
  const detailLimit = 20;

  let sortMainCol = '', sortMainAsc = true;
  let sortDetailCol = '', sortDetailAsc = true;

  const getSortIcon = (col, currentCol, asc) => {
      if (col !== currentCol) return '<span class="opacity-30 text-[10px] ml-1">&#8597;</span>';
      return asc ? '<span class="text-blue-600 ml-1 text-[11px]">&#9650;</span>' : '<span class="text-blue-600 ml-1 text-[11px]">&#9660;</span>';
  };

  const getBucketLabel = bucket => {
      if (bucket === 'dpd0') return 'DPD 0';
      if (bucket === 'dpd1-30') return 'DPD 1-30';
      return 'ALL';
  };

  let mainFilterOpen = window.innerWidth >= 1280;

  function toggleMainFilter() {
      mainFilterOpen = !mainFilterOpen;
      applyFilterState();
  }

  function applyFilterState() {
      const el = document.getElementById('filterWrapperMain');
      const chevron = document.getElementById('iconChevron');
      if(mainFilterOpen) {
          el.classList.remove('filter-collapsed');
          el.classList.add('filter-expanded');
          if(chevron) chevron.style.transform = 'rotate(180deg)';
      } else {
          el.classList.remove('filter-expanded');
          el.classList.add('filter-collapsed');
          if(chevron) chevron.style.transform = 'rotate(0deg)';
      }
  }

  function toggleOtpHelp(event) {
      if (event) event.stopPropagation();
      const panel = document.getElementById('otpHelpPanel');
      if (!panel) return;
      panel.classList.toggle('hidden');
      panel.classList.toggle('flex');
  }

  document.addEventListener('click', (event) => {
      const root = document.querySelector('.otp-info-root');
      const panel = document.getElementById('otpHelpPanel');
      if (root && panel && !root.contains(event.target)) {
          panel.classList.add('hidden');
          panel.classList.remove('flex');
      }
  });

  window.addEventListener('DOMContentLoaded', async () => {
    mainFilterOpen = window.innerWidth >= 1280;
    applyFilterState();

    const user = (window.getUser && window.getUser()) || null;
    let uKode = (user && user.kode) ? String(user.kode).padStart(3, '0') : '000';
    if(uKode === '099') uKode = '000';
    
    await populateKantor(uKode);

    const d = await getLastHarianData(); 
    if(d) {
        document.getElementById('closing_date').value = d.last_closing;
        document.getElementById('harian_date').value  = d.last_created;
    } else {
        const now = new Date();
        now.setDate(now.getDate() - 1);
        const strH1 = now.toISOString().split('T')[0];
        document.getElementById('closing_date').value = strH1;
        document.getElementById('harian_date').value  = strH1;
    }
    fetchRekapRR();
  });

  window.addEventListener('resize', () => {
      if(window.innerWidth >= 1280 && !mainFilterOpen) {
          mainFilterOpen = true;
          applyFilterState();
      }
  });

  async function getLastHarianData(){ 
      try{ const r = await fetch('./api/date/'); const j = await r.json(); return j.data||null; }catch{ return null; } 
  }
  
  async function populateKantor(uKode) {
    const el = document.getElementById('opt_kantor'); if(!el) return;
    if (uKode !== '000') { 
        try {
            const res = await fetch(API_KODE_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
            const json = await res.json();
            const myKantor = (json.data||[]).find(x => String(x.kode_kantor).padStart(3,'0') === uKode);
            const nama = myKantor ? myKantor.nama_kantor : `CABANG ${uKode}`;
            el.innerHTML = `<option value="${uKode}">${uKode} - ${nama}</option>`;
        } catch(e) {
            el.innerHTML = `<option value="${uKode}">CABANG ${uKode}</option>`; 
        }
        el.value = uKode; el.disabled = true;
        await handleCabangChangeOtp(true);
        return; 
    }
    try {
        const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ type: 'kode_kantor' }) });
        const j = await r.json();
        let h = '<option value="">ALL CABANG</option>';
        if(j.data) j.data.filter(x => x.kode_kantor !== '000').forEach(x => { h += `<option value="${x.kode_kantor}">${x.kode_kantor} - ${x.nama_kantor}</option>`; });
        el.innerHTML = h;
    } catch { el.innerHTML = '<option value="">ALL CABANG</option>'; }
    await handleCabangChangeOtp(true);
  }

  async function handleCabangChangeOtp(isInit = false) {
      const cabangVal = document.getElementById('opt_kantor').value;
      const lblSub = document.getElementById('lbl_sub_otp');
      const optSub = document.getElementById('opt_sub_otp');
      const optAo = document.getElementById('opt_ao_otp');

      if (cabangVal === "" || cabangVal === "000") {
          lblSub.innerText = "KORWIL";
          optSub.innerHTML = `
              <option value="">ALL KORWIL</option>
              <option value="SEMARANG">SEMARANG</option>
              <option value="SOLO">SOLO</option>
              <option value="BANYUMAS">BANYUMAS</option>
              <option value="PEKALONGAN">PEKALONGAN</option>
          `;
          optAo.innerHTML = '<option value="">PILIH CABANG DULU</option>';
          optAo.disabled = true;
      } else {
          lblSub.innerText = "KANKAS";
          optSub.innerHTML = '<option value="">ALL KANKAS</option>';
          optAo.innerHTML = '<option value="">ALL AO</option>';
          optAo.disabled = false;
          await loadKankasSubOtp(cabangVal);
          await loadAoMainOtp(cabangVal);
      }
      if (!isInit) fetchRekapRR();
  }

  async function loadKankasSubOtp(kodeCabang) {
      const optSub = document.getElementById('opt_sub_otp');
      try {
          const payload = { type: 'kode_kankas', kode_kantor: kodeCabang };
          const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          let h = '<option value="">ALL KANKAS</option>';
          if(j.data && Array.isArray(j.data)) j.data.forEach(x => { h += `<option value="${x.kode_group1}">${x.deskripsi_group1 || x.kode_group1}</option>`; });
          optSub.innerHTML = h;
      } catch(err) {}
  }

  async function loadAoMainOtp(kodeCabang) {
      const optAo = document.getElementById('opt_ao_otp');
      if (!optAo) return;
      try {
          const payload = { type: 'kode_ao_kredit', kode_kantor: kodeCabang };
          const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          let h = '<option value="">ALL AO</option>';
          if(j.data && Array.isArray(j.data)) {
              j.data.forEach(x => { h += `<option value="${x.kode_group2}">${x.nama_ao || x.kode_group2}</option>`; });
          }
          optAo.innerHTML = h;
      } catch(err) {
          optAo.innerHTML = '<option value="">ALL AO</option>';
      }
  }

  async function loadMasterKankasForModal(kodeCabang) {
      const elKankas = document.getElementById('opt_kankas_modal');
      try {
          const isKonsolidasi = (kodeCabang === '000' || !kodeCabang);
          const payload = { type: 'kode_kankas', kode_kantor: isKonsolidasi ? '' : kodeCabang };
          const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          
          let h = '<option value="">Semua Kankas</option>';
          if(j.data && Array.isArray(j.data)) {
              j.data.forEach(x => { h += `<option value="${x.kode_group1}">${x.deskripsi_group1 || x.kode_group1}</option>`; });
          }
          elKankas.innerHTML = h;
      } catch(e) {
          elKankas.innerHTML = '<option value="">Semua Kankas</option>';
      }
  }

  function populateAOFromData(data, preselect) {
      const elAO = document.getElementById('opt_ao_modal');
      const aoMap = new Map();
      (data || []).forEach(r => {
          const aoKey = r.kode_ao || r.nama_ao || '';
          const aoName = r.nama_ao || r.kode_ao || '';
          if(aoKey && aoKey.trim() !== '' && aoKey !== '-') aoMap.set(aoKey, aoName);
      });
      
      if (preselect && preselect !== "" && !aoMap.has(preselect)) {
          aoMap.set(preselect, preselect); 
      }

      let h = '<option value="">Semua AO</option>';
      [...aoMap.entries()].sort((a,b) => a[1].localeCompare(b[1])).forEach(([k, v]) => { 
          h += `<option value="${k}">${v}</option>`; 
      });
      elAO.innerHTML = h;
      if(preselect) elAO.value = preselect;
  }

  /* ============================
     RENDER MAIN TABLE 
     ============================ */
  function renderMainHeaderRR() {
      const head = document.getElementById('headRR');
      head.innerHTML = `
          <tr class="rr-row-1">
            <th rowspan="2" class="head-row px-2 border-b border-r border-slate-200 bg-slate-100 sticky left-0 z-30 shadow-[1px_0_0_#e2e8f0]" onclick="sortMainRR('tgl', 'string')">
                <div class="flex items-center justify-center">TGL ${getSortIcon('tgl', sortMainCol, sortMainAsc)}</div>
            </th>
            <th colspan="3" class="head-row px-3 border-b border-r border-slate-200 bg-slate-100 text-blue-900 cursor-default tracking-widest text-center border-t-2 border-t-blue-500">TOTAL OUTSTANDING</th>
            <th colspan="3" class="head-row px-3 border-b border-slate-200 bg-slate-100 text-purple-900 cursor-default tracking-widest text-center border-t-2 border-t-purple-500">RECOVERY / PEMBAYARAN</th>
            <th rowspan="2" class="head-row px-2 border-b border-l border-slate-200 bg-slate-100 z-30 text-center" onclick="sortMainRR('persen', 'number')">
                <div class="flex items-center justify-center">% ${getSortIcon('persen', sortMainCol, sortMainAsc)}</div>
            </th>
          </tr>
          <tr class="rr-row-2 text-[10px] md:text-[11px] tracking-wider text-slate-700 bg-slate-50">
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[140px] md:w-[180px]" onclick="sortMainRR('target_os', 'number')">
                <div class="flex items-center justify-center">TARGET (M-1) ${getSortIcon('target_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[140px] md:w-[180px]" onclick="sortMainRR('lancar_os', 'number')">
                <div class="flex items-center justify-center">OTP (LANCAR) ${getSortIcon('lancar_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[140px] md:w-[180px]" onclick="sortMainRR('macet_os', 'number')">
                <div class="flex items-center justify-center">DITAGIH ${getSortIcon('macet_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[120px] md:w-[160px]" onclick="sortMainRR('lunas_os', 'number')">
                <div class="flex items-center justify-center">LUNAS ${getSortIcon('lunas_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[120px] md:w-[160px]" onclick="sortMainRR('angsuran', 'number')">
                <div class="flex items-center justify-center">ANGSURAN ${getSortIcon('angsuran', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-slate-200 w-[120px] md:w-[160px]" onclick="sortMainRR('total_bayar', 'number')">
                <div class="flex items-center justify-center">TOTAL BAYAR ${getSortIcon('total_bayar', sortMainCol, sortMainAsc)}</div>
            </th>
          </tr>
          <tr class="rr-row-tot bg-slate-50 sticky-total border-b border-slate-300" id="rowTotalRRAtas"></tr>
      `;
  }

  window.sortMainRR = function(col, type) {
      if (!rekapDataRaw || rekapDataRaw.length === 0) return;
      if (sortMainCol === col) sortMainAsc = !sortMainAsc;
      else { sortMainCol = col; sortMainAsc = true; }

      rekapDataRaw.sort((a, b) => {
          let valA = a[col], valB = b[col];
          if (type === 'number') {
              return sortMainAsc ? (parseFloat(valA)||0) - (parseFloat(valB)||0) : (parseFloat(valB)||0) - (parseFloat(valA)||0);
          } else {
              valA = String(valA||'').toLowerCase(); valB = String(valB||'').toLowerCase();
              if (valA < valB) return sortMainAsc ? -1 : 1;
              if (valA > valB) return sortMainAsc ? 1 : -1;
              return 0;
          }
      });
      renderMainHeaderRR(); renderTableRR(rekapDataRaw, rekapGtRaw);
  }

  async function fetchRekapRR(){
    const l = document.getElementById('loadingRR'), tb = document.getElementById('bodyRR'), trTotal = document.getElementById('rowTotalRRAtas'); 
    if(abortRR) abortRR.abort(); abortRR = new AbortController();
    l.classList.remove('hidden'); 
    tb.innerHTML = `<tr><td colspan="8" class="py-20 text-center text-slate-400 italic text-sm">Sedang mengambil data...</td></tr>`;
    if(trTotal) trTotal.innerHTML = '';
    rekapDataRaw = []; rekapGtRaw = null; sortMainCol = ''; sortMainAsc = true;

    try {
        const cabangVal = document.getElementById('opt_kantor').value;
        const subVal = document.getElementById('opt_sub_otp').value;
        const dpdBucket = document.getElementById('opt_dpd_bucket').value;
        const aoVal = document.getElementById('opt_ao_otp')?.value || "";

        let reqKorwil = "", reqKankas = "";
        if (!cabangVal || cabangVal === "000") reqKorwil = subVal;
        else reqKankas = subVal;

        const titleEl = document.getElementById('otpTitle');
        if(titleEl) titleEl.textContent = `OTP - ${getBucketLabel(dpdBucket)}`;

        const payload = { 
            type: 'rekap_rr', closing_date: document.getElementById('closing_date').value, harian_date: document.getElementById('harian_date').value, 
            kode_kantor: cabangVal || null, korwil: reqKorwil, kode_kankas: reqKankas, kode_ao: aoVal, dpd_bucket: dpdBucket, include_127: document.getElementById('chk_127').checked
        };
        
        const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload), signal: abortRR.signal });
        if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Gagal memuat data");
        
        rekapDataRaw = res.json.data.data || []; rekapGtRaw = res.json.data.grand_total;
        renderMainHeaderRR(); renderTableRR(rekapDataRaw, rekapGtRaw);
    } catch(err) {
        if(err.name !== 'AbortError') tb.innerHTML=`<tr><td colspan="8" class="py-16 text-center text-rose-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">Error: ${err.message}</td></tr>`;
    } finally { l.classList.add('hidden'); }
  }

  function renderTableRR(rows, gt) {
      const tb = document.getElementById('bodyRR'), trTotal = document.getElementById('rowTotalRRAtas');
      tb.innerHTML = '';
      if(rows.length === 0){ tb.innerHTML = `<tr><td colspan="8" class="py-20 text-center text-slate-400 text-sm">Tidak ada data penagihan.</td></tr>`; return; }

      if(gt && trTotal) {
        const gtPct = Number(gt.persen || 0);
        const gtPctClass = gtPct >= 90 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : (gtPct >= 70 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-rose-700 bg-rose-50 border-rose-200');
        
        trTotal.innerHTML = `
            <th class="px-2 sticky left-0 text-center uppercase tracking-widest shadow-[1px_0_0_#cbd5e1] bg-slate-100/80 text-slate-800 z-20">TOTAL</th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="mb-0.5"><a href="javascript:void(0)" onclick="initModalDetail('ALL','ALL')" class="hover:underline">${fmt(gt.target_os)}</a></div>
                <div class="text-[10px]">NOA: <span>${fmt(gt.target_noa)}</span></div>
            </th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="mb-0.5"><a href="javascript:void(0)" onclick="initModalDetail('ALL','LANCAR')" class="hover:underline">${fmt(gt.lancar_os)}</a></div>
                <div class="text-[10px]">NOA: <span>${fmt(gt.lancar_noa)}</span></div>
            </th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="mb-0.5"><a href="javascript:void(0)" onclick="initModalDetail('ALL','MENUNGGAK')" class="hover:underline">${fmt(gt.macet_os)}</a></div>
                <div class="text-[10px]">NOA: <span>${fmt(gt.macet_noa)}</span></div>
            </th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="mb-0.5"><a href="javascript:void(0)" onclick="initModalLunas('ALL')" class="hover:underline">${fmt(gt.lunas_os)}</a></div>
                <div class="text-[10px]">NOA: <span>${fmt(gt.lunas_noa)}</span></div>
            </th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="align-middle pt-1 md:pt-2"><span class="text-slate-700">${fmt(gt.angsuran)}</span></div>
            </th>
            <th class="px-2 md:px-3 text-center">
                <div class="align-middle pt-1 md:pt-2"><span class="text-slate-700">${fmt(gt.total_bayar)}</span></div>
            </th>
            <th class="px-2 text-center align-middle bg-slate-50/80 border-l border-slate-200 z-20">
                <div class="inline-flex items-center justify-center min-w-[70px] rounded border px-2 py-1.5 ${gtPctClass}">
                    <span class="leading-none">${gt.persen}%</span>
                </div>
            </th>
        `;
      }

      let h = '';
      rows.forEach(r => {
          // Default: tampil strip '-' jika nilai dari backend adalah null
          let pctHtml = `<span class="text-slate-300 font-bold text-lg">-</span>`;
          
          if (r.persen !== null && r.persen !== undefined) {
              const pct = Number(r.persen);
              const pctClass = pct >= 90 ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : (pct >= 70 ? 'text-amber-700 bg-amber-50 border-amber-100' : 'text-rose-700 bg-rose-50 border-rose-100');
              
              pctHtml = `
                  <div class="inline-flex items-center justify-center min-w-[64px] rounded border px-1.5 py-1 ${pctClass}">
                      <span class="font-semibold leading-none">${pct}%</span>
                  </div>
              `;
          }
          
          h += `
            <tr class="transition border-b border-slate-100 hover:bg-slate-50">
                <td class="px-2 py-2 sticky left-0 bg-white border-r border-slate-100 text-center shadow-[1px_0_0_#f1f5f9] z-20">${r.tgl}</td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition">
                    <a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','ALL')" class="block mb-0.5">${fmt(r.target_os)}</a>
                    <div class="text-[10px]">NOA: <span>${fmt(r.target_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition">
                    <a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','LANCAR')" class="block mb-0.5">${fmt(r.lancar_os)}</a>
                    <div class="text-[10px]">NOA: <span>${fmt(r.lancar_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition">
                    <a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','MENUNGGAK')" class="block mb-0.5">${fmt(r.macet_os)}</a>
                    <div class="text-[10px]">NOA: <span>${fmt(r.macet_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition">
                    <a href="javascript:void(0)" onclick="initModalLunas('${r.tgl}')" class="block mb-0.5">${fmt(r.lunas_os)}</a>
                    <div class="text-[10px]">NOA: <span>${fmt(r.lunas_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition align-middle">
                    <span class="text-slate-700 block">${fmt(r.angsuran)}</span>
                </td>
                <td class="px-2 md:px-3 py-2 text-center transition align-middle border-r border-slate-100">
                    <span class="text-slate-700 block">${fmt(r.total_bayar)}</span>
                </td>
                <td class="px-2 py-2 text-center align-middle">
                    ${pctHtml}
                </td>
            </tr>`;
      });
      tb.innerHTML = h;
  }

  function createWABtn(phone) {
      if (!phone || phone.trim() === '') return `<span class="text-slate-400 font-mono">-</span>`;
      return `<span class="text-slate-600 font-mono">${phone}</span>`;
  }

  function renderModalHeaderRR() {
      const mHead = document.getElementById('headModalRR');
      
      if (currentMode === 'NORMAL') {
          mHead.innerHTML = `
              <tr class="bg-slate-100">
                  <th class="col-rek hidden md:table-cell px-2 md:px-3 py-2 border-b border-r border-slate-200 text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-center">REKENING ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="col-nas px-2 md:px-4 py-2 border-b border-r border-slate-200 text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[60px] md:w-[80px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kode_produk', 'string')">
                      <div class="flex items-center justify-center">PRODUK ${getSortIcon('kode_produk', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[200px] md:w-[320px] text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_hp', 'string')">
                      <div class="flex items-center justify-center">NO HP ${getSortIcon('no_hp', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kankas', 'string')">
                      <div class="flex items-center justify-center">KANKAS ${getSortIcon('kankas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[150px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_jatuh_tempo', 'string')">
                      <div class="flex items-center justify-center">TGL JT ${getSortIcon('tgl_jatuh_tempo', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('jml_pinjaman', 'number')">
                      <div class="flex items-center justify-end">PLAFOND ${getSortIcon('jml_pinjaman', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('os_m1', 'number')">
                      <div class="flex items-center justify-end">TARGET M-1 ${getSortIcon('os_m1', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('os_curr', 'number')">
                      <div class="flex items-center justify-end">BAKI DEBET ${getSortIcon('os_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('totung', 'number')">
                      <div class="flex items-center justify-end">TUNGGAKAN ${getSortIcon('totung', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[50px] md:w-[70px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('dpd_curr', 'number')">
                      <div class="flex items-center justify-center">DPD ${getSortIcon('dpd_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[100px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tabungan', 'number')">
                      <div class="flex items-center justify-end">TABUNGAN ${getSortIcon('tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_tabungan', 'string')">
                      <div class="flex items-center justify-center">STAT TAB ${getSortIcon('status_tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('trx_bulan_lalu', 'number')">
                      <div class="flex items-center justify-end">TRX LALU ${getSortIcon('trx_bulan_lalu', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('trx_bulan_ini', 'number')">
                      <div class="flex items-center justify-end">TRX INI ${getSortIcon('trx_bulan_ini', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-slate-200 w-[100px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_ket', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_ket', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      } else {
          mHead.innerHTML = `
              <tr class="bg-slate-100">
                  <th class="col-nas-lunas px-2 md:px-4 py-2 border-b border-r border-slate-200 text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[200px] md:w-[350px] text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[100px] md:w-[150px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-center">REK LAMA ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('plafon_lama', 'number')">
                      <div class="flex items-center justify-end">PLAFOND LAMA ${getSortIcon('plafon_lama', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('os_lunas', 'number')">
                      <div class="flex items-center justify-end">OS M-1 ${getSortIcon('os_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[80px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_lunas', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('rek_baru', 'string')">
                      <div class="flex items-center justify-center">REK BARU ${getSortIcon('rek_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('plafond_baru', 'number')">
                      <div class="flex items-center justify-end">PLAFOND BARU ${getSortIcon('plafond_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-slate-200 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_baru', 'string')">
                      <div class="flex items-center justify-center">TGL REALISASI ${getSortIcon('tgl_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      }
  }

  window.sortDetailRR = function(col, type) {
      if (!detailDataCache || detailDataCache.length === 0) return;
      if (sortDetailCol === col) sortDetailAsc = !sortDetailAsc; else { sortDetailCol = col; sortDetailAsc = true; }

      detailDataCache.sort((a, b) => {
          let valA = a[col], valB = b[col];
          if (type === 'number') return sortDetailAsc ? (parseFloat(valA)||0) - (parseFloat(valB)||0) : (parseFloat(valB)||0) - (parseFloat(valA)||0);
          valA = String(valA||'').toLowerCase(); valB = String(valB||'').toLowerCase();
          if (valA < valB) return sortDetailAsc ? -1 : 1;
          if (valA > valB) return sortDetailAsc ? 1 : -1;
          return 0;
      });
      renderModalHeaderRR(); renderTableDetailBodyRR(detailDataCache);
  }

  async function initModalDetail(tgl, status) {
      currentMode = 'NORMAL';
      const branch = document.getElementById('opt_kantor').value || null;
      const subVal = document.getElementById('opt_sub_otp').value;
      const dpdBucket = document.getElementById('opt_dpd_bucket').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;
      const mainAo = document.getElementById('opt_ao_otp')?.value || "";

      let reqKorwil = "", reqKankas = "";
      if (!branch || branch === "000") reqKorwil = subVal;
      else reqKankas = subVal;

      let preselectKankasCode = "";
      if (lblSub === "KANKAS" && subVal !== "") preselectKankasCode = subVal;

      currentDetailParams = { 
          type: 'detail_rr', closing_date: document.getElementById('closing_date').value, harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, korwil: reqKorwil, kode_kankas: preselectKankasCode || reqKankas, kode_ao: mainAo,
          tgl_tagih: tgl, status: status, dpd_bucket: dpdBucket, include_127: document.getElementById('chk_127').checked, limit: detailLimit 
      };

      const bucketLabel = getBucketLabel(dpdBucket);
      document.getElementById('modalTitleRR').textContent = `Detail Matriks OTP ${bucketLabel} - Tgl ${tgl}`;
      document.getElementById('modalSubTitleRR').textContent = `Status: ${status}`;
      document.getElementById('modalDetailRR').classList.remove('hidden');
      
      if(document.getElementById('search_nasabah')) document.getElementById('search_nasabah').value = ''; if(document.getElementById('search_nasabah_mobile')) document.getElementById('search_nasabah_mobile').value = '';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      await loadMasterKankasForModal(branch);
      document.getElementById('opt_kankas_modal').value = currentDetailParams.kode_kankas;
      document.getElementById('opt_ao_modal').innerHTML = mainAo ? `<option value="${mainAo}">${mainAo}</option>` : '<option value="">Semua AO</option>';

      loadDetailPage(1);
  }

  async function initModalLunas(tgl) {
      currentMode = 'LUNAS';
      const branch = document.getElementById('opt_kantor').value || null;
      const subVal = document.getElementById('opt_sub_otp').value;
      const dpdBucket = document.getElementById('opt_dpd_bucket').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;
      const mainAo = document.getElementById('opt_ao_otp')?.value || "";

      let reqKorwil = "", reqKankas = "";
      if (!branch || branch === "000") reqKorwil = subVal;
      else reqKankas = subVal;

      let preselectKankasCode = "";
      if (lblSub === "KANKAS" && subVal !== "") preselectKankasCode = subVal;

      currentDetailParams = { 
          type: 'detail_lunas_rr', closing_date: document.getElementById('closing_date').value, harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, korwil: reqKorwil, kode_kankas: preselectKankasCode || reqKankas, kode_ao: mainAo,
          tgl_tagih: tgl, dpd_bucket: dpdBucket, include_127: document.getElementById('chk_127').checked, limit: detailLimit 
      };

      const bucketLabel = getBucketLabel(dpdBucket);
      document.getElementById('modalTitleRR').textContent = `Pelunasan OTP ${bucketLabel} - Tgl ${tgl}`;
      document.getElementById('modalSubTitleRR').textContent = `Refinancing & Prospek`;
      document.getElementById('modalDetailRR').classList.remove('hidden');
      
      if(document.getElementById('search_nasabah')) document.getElementById('search_nasabah').value = ''; if(document.getElementById('search_nasabah_mobile')) document.getElementById('search_nasabah_mobile').value = '';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      await loadMasterKankasForModal(branch);
      document.getElementById('opt_kankas_modal').value = currentDetailParams.kode_kankas;
      document.getElementById('opt_ao_modal').innerHTML = mainAo ? `<option value="${mainAo}">${mainAo}</option>` : '<option value="">Semua AO</option>';

      loadDetailPage(1);
  }

  window.filterTableDetail = function() {
      const desktop = document.getElementById("search_nasabah");
      const mobile = document.getElementById("search_nasabah_mobile");
      const active = (desktop && document.activeElement === desktop) ? desktop : ((mobile && document.activeElement === mobile) ? mobile : (desktop || mobile));
      const filter = (active?.value || '').toLowerCase().trim();
      const tbody = document.getElementById("bodyModalRR");
      if (!tbody) return;
      const trs = tbody.getElementsByTagName("tr");

      for (let i = 0; i < trs.length; i++) {
          const cells = trs[i].getElementsByTagName("td");
          if (!cells.length) continue;
          let haystack = '';
          if (currentMode === 'NORMAL') {
              haystack = `${cells[0]?.textContent || ''} ${cells[1]?.textContent || ''}`;
          } else {
              haystack = `${cells[0]?.textContent || ''} ${cells[3]?.textContent || ''} ${cells[7]?.textContent || ''}`;
          }
          trs[i].style.display = haystack.toLowerCase().indexOf(filter) > -1 ? "" : "none";
      }
  }

  async function loadDetailPage(page) {
      const l = document.getElementById('loadingModalRR'), tb = document.getElementById('bodyModalRR'), info = document.getElementById('pageInfoRR');
      l.classList.remove('hidden'); tb.innerHTML = '';

      try {
          currentDetailParams.kode_kankas = document.getElementById('opt_kankas_modal').value;
          currentDetailParams.kode_ao = document.getElementById('opt_ao_modal').value || "";

          const payload = { ...currentDetailParams, page: page };
          const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          
          if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Gagal memuat detail");
          
          detailDataCache = res.json.data?.data || [];
          const meta = res.json.data?.pagination || { total_records:0, total_pages:1 };

          currentDetailPage = page; currentDetailTotalPages = meta.total_pages;

          if(detailDataCache.length === 0) {
              tb.innerHTML = `<tr><td colspan="${currentMode === 'NORMAL' ? 18 : 10}" class="py-20 text-center text-slate-400 italic text-sm">Tidak ada data detail.</td></tr>`;
              info.innerText = `0 Data`;
          } else {
              sortDetailCol = ''; sortDetailAsc = true;
              renderModalHeaderRR(); renderTableDetailBodyRR(detailDataCache);
              info.innerText = `Hal ${page} dari ${meta.total_pages} (${fmt(meta.total_records)} Data)`;
          }
          
          populateAOFromData(detailDataCache, currentDetailParams.kode_ao);

          document.getElementById('btnPrevRR').disabled = page <= 1;
          document.getElementById('btnNextRR').disabled = page >= meta.total_pages;
      } catch(err){ 
          console.error(err); 
          tb.innerHTML = `<tr><td colspan="${currentMode === 'NORMAL' ? 18 : 10}" class="py-16 text-center text-rose-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">Gagal memuat detail</td></tr>`;
      } finally { l.classList.add('hidden'); }
  }

  function renderTableDetailBodyRR(list) {
      const tb = document.getElementById('bodyModalRR');
      let h = '';
      
      list.forEach(r => {
          const aoName = (r.nama_ao || '-').split(' ').slice(0, 2).join(' ');

          if(currentMode === 'NORMAL') {
              const btnWa = createWABtn(r.no_hp);
              const alamatLengkap = r.alamat || '-';

              h += `<tr class="transition border-b border-slate-100 hover:bg-slate-50 h-[44px] md:h-[48px]">
                    <td class="col-rek hidden md:table-cell px-2 md:px-3 py-1.5 border-r border-slate-100 font-mono text-slate-500">${r.no_rekening}</td>
                    <td class="col-nas px-2 md:px-4 py-1.5 border-r border-slate-100 truncate" title="${r.nama_nasabah}">${r.nama_nasabah}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono text-slate-500">${r.kode_produk || '-'}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-slate-500 truncate max-w-[200px] md:max-w-[320px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center">${btnWa}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono">
                        ${r.kankas && r.kankas !== '-' ? `<span class="font-medium text-slate-600">${r.kankas}</span>` : '-'}
                    </td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-center truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono text-slate-500">${r.tgl_jatuh_tempo||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">${fmt(r.jml_pinjaman)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.os_m1)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.os_curr)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.totung)}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center text-slate-600">${r.dpd_curr}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">${fmt(r.tabungan)}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center">
                        ${r.status_tabungan === 'Aman' ? '<span class="text-emerald-600 font-semibold">Aman</span>' : '<span class="text-rose-500 font-semibold">Belum Aman</span>'}
                    </td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">
                        <div>${fmt(r.trx_bulan_lalu)}</div>
                        <div class="text-[9px] text-slate-400 font-mono">${Number(r.trx_bulan_lalu || 0) > 0 ? fmtDateID(r.tgl_bayar_lalu) : ''}</div>
                    </td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">
                        <div>${fmt(r.trx_bulan_ini)}</div>
                        <div class="text-[9px] text-slate-400 font-mono">${Number(r.trx_bulan_ini || 0) > 0 ? fmtDateID(r.tgl_bayar_ini) : ''}</div>
                    </td>
                    <td class="px-2 md:px-4 py-1.5 text-center font-semibold ${r.status_ket === 'LANCAR' ? 'text-emerald-600' : (r.status_ket === 'MENUNGGAK' ? 'text-rose-600' : 'text-slate-500')}">${r.status_ket}</td>
                </tr>`;
          } else {
              const alamatLengkap = r.alamat || '-';
              let badge = `<span class="text-xs font-semibold text-blue-600">PROSPEK</span>`;
              if(r.status_lunas === 'REFINANCING / Top Up') badge = `<span class="text-xs font-semibold text-emerald-600">REFINANCING</span>`;

              h += `<tr class="transition border-b border-slate-100 hover:bg-slate-50 h-[44px] md:h-[48px]">
                    <td class="col-nas-lunas px-2 md:px-4 py-1.5 border-r border-slate-100 truncate">
                        ${r.nama_nasabah}
                        <div class="text-[9px] text-slate-400 font-mono font-normal">ID: ${r.nasabah_id}</div>
                    </td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-slate-500 truncate max-w-[200px] md:max-w-[350px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-center truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 font-mono text-center text-slate-500">${r.no_rekening}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">${fmt(r.plafon_lama)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.os_lunas)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-center">${badge}</td>
                    <td class="px-2 md:px-3 border-r border-slate-100 font-mono text-center text-emerald-600">${r.rek_baru}</td>
                    <td class="px-2 md:px-4 border-r border-slate-100 text-right text-emerald-600">${fmt(r.plafond_baru)}</td>
                    <td class="px-2 md:px-3 text-center text-slate-500">${r.tgl_baru}</td>
                </tr>`;
          }
      });
      tb.innerHTML = h;
  }

  async function downloadExcelFull(ev) {
      const btn = (ev?.target || window.event?.target)?.closest('button'); if(!btn) return; const txt = btn.innerHTML;
      btn.innerHTML = `<span class="animate-spin inline-block h-3.5 w-3.5 border-2 border-white border-t-transparent rounded-full md:mr-2"></span><span class="hidden md:inline">...</span>`;
      btn.disabled = true;

      try {
          const payload = { ...currentDetailParams, page: 1, limit: 10000 };
          const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Export gagal");
          
          const rows = res.json.data?.data || [];
          if(rows.length === 0) { alert("Tidak ada data untuk diexport"); return; }

          let csv = "";
          if(currentMode === 'NORMAL') {
              csv = `No Rekening\tNama Nasabah\tKode Produk\tAlamat\tNo HP\tKankas\tNama AO\tTgl JT\tPlafond\tTarget (M-1)\tBaki Debet Actual\tTot Tunggakan\tDPD\tSaldo Tabungan\tStatus Tabungan\tTrx Bulan Lalu\tTgl Bayar Lalu\tTrx Bulan Ini\tTgl Bayar Ini\tStatus Tagih\n`;
              rows.forEach(r => {
                  csv += `'${r.no_rekening}\t${r.nama_nasabah}\t${r.kode_produk||''}\t${r.alamat||''}\t'${r.no_hp||''}\t${r.kankas||''}\t${r.nama_ao}\t${r.tgl_jatuh_tempo}\t${Math.round(r.jml_pinjaman)}\t${Math.round(r.os_m1)}\t${Math.round(r.os_curr)}\t${Math.round(r.totung)}\t${r.dpd_curr}\t${Math.round(r.tabungan)}\t${r.status_tabungan}\t${Math.round(r.trx_bulan_lalu||0)}\t${fmtDateID(r.tgl_bayar_lalu)}\t${Math.round(r.trx_bulan_ini||0)}\t${fmtDateID(r.tgl_bayar_ini)}\t${r.status_ket}\n`;
              });
          } else {
              csv = `Nama Nasabah\tID Nasabah\tAlamat\tNama AO\tRek Lama\tPlafond Lama\tOS Lunas (M-1)\tStatus\tRek Baru\tPlafond Baru\tTgl Realisasi Baru\n`;
              rows.forEach(r => {
                  csv += `${r.nama_nasabah}\t'${r.nasabah_id}\t${r.alamat||''}\t${r.nama_ao}\t'${r.no_rekening}\t${Math.round(r.plafon_lama)}\t${Math.round(r.os_lunas)}\t${r.status_lunas}\t'${r.rek_baru}\t${Math.round(r.plafond_baru)}\t${r.tgl_baru}\n`;
              });
          }

          const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a'); a.href = url; a.download = `RR_Detail_${currentMode}_${currentDetailParams.tgl_tagih}.xls`;
          document.body.appendChild(a); a.click(); document.body.removeChild(a);

      } catch(e) { console.error(e); alert("Gagal export data."); } 
      finally { btn.innerHTML = txt; btn.disabled = false; }
  }

  window.changePageDetail = (step) => { const n = currentDetailPage + step; if (n > 0 && n <= currentDetailTotalPages) loadDetailPage(n); }
  window.closeModalRR = () => document.getElementById('modalDetailRR').classList.add('hidden');
  document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModalRR(); });

  window.setKankasModal = function(kankasKode) {
      const optKankas = document.getElementById('opt_kankas_modal');
      if (optKankas && kankasKode) { optKankas.value = kankasKode; loadDetailPage(1); }
  };
  window.setAOModal = function(aoKey) {
      const optAo = document.getElementById('opt_ao_modal');
      if (optAo && aoKey) { optAo.value = aoKey; loadDetailPage(1); }
  };
</script>
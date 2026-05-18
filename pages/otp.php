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
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* ========================================================
     MAGIC STICKY TABLE UTAMA (FIX OVERLAP & FREEZE)
     ======================================================== */
  #tabelUtama th, #tabelRekapRR th { position: sticky !important; z-index: 10; }
  
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
  #tabelRekapRR td.sticky.left-0 { position: sticky !important; left: 0; z-index: 20; background-color: #f8fafc; box-shadow: 1px 0 0 #cbd5e1; }
  #tabelRekapRR tr.sticky-total th.sticky.left-0 { z-index: 45 !important; background-color: #e2e8f0 !important; }

  /* ========================================================
     TABEL MODAL DETAIL RR (FIX FREEZE)
     ======================================================== */
  #tableExportRR { border-collapse: separate; border-spacing: 0; min-width: 100%; }
  #tableExportRR th, #tableExportRR td { background-clip: padding-box; background-color: #fff; }

  #tableExportRR thead th { 
      position: sticky !important; 
      top: 0 !important; 
      z-index: 40 !important; 
      background-color: #f1f5f9 !important; 
      box-shadow: inset 0 -2px 0 #cbd5e1; 
  }

  .mod-col-rek { position: sticky !important; left: 0 !important; min-width: 100px; max-width: 100px; box-shadow: inset -1px 0 0 #e2e8f0; }
  .mod-col-nas { position: sticky !important; left: 0 !important; min-width: 160px; max-width: 160px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1), inset -1px 0 0 #e2e8f0; }
  
  @media (min-width: 768px) { 
      .mod-col-rek { min-width: 120px; max-width: 120px; }
      .mod-col-nas { left: 120px !important; min-width: 250px; max-width: 250px; } 
  }

  #bodyModalRR td.mod-col-rek, #bodyModalRR td.mod-col-nas { z-index: 20 !important; background-color: #fff !important; }
  #headModalRR th.mod-col-rek, #headModalRR th.mod-col-nas { z-index: 50 !important; background-color: #e2e8f0 !important; }

  tbody tr:hover td { background-color: #f8fafc !important; }
  #bodyModalRR tr:hover td.mod-col-rek, #bodyModalRR tr:hover td.mod-col-nas { filter: brightness(0.98); }
  tbody.group-tbody tr:hover td.sticky { filter: brightness(0.95); }

  /* Form Inputs */
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; background:#fff; outline:none; transition: border 0.2s;}
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f1f5f9; color: #64748b; font-weight: 700; cursor: not-allowed; }
  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap;}
  @media (min-width: 768px) { .lbl { font-size:11px; margin-bottom:4px; } .inp { border-radius: 8px; padding:0 12px; } }
  .field { display:flex; flex-direction:column; }
  
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }

  /* HIDE DATEPICKER ICON */
  input[type="date"]::-webkit-inner-spin-button, input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
  input[type="date"] { -moz-appearance: textfield; }

  /* Filter toggle animation */
  .filter-collapsed { max-height: 0; overflow: hidden; opacity: 0; padding-top: 0; padding-bottom: 0; margin-top: 0; }
  .filter-expanded { max-height: 500px; overflow: visible; opacity: 1; }
  .filter-transition { transition: max-height 0.3s ease, opacity 0.2s ease, padding 0.2s ease, margin 0.2s ease; }
</style>

<div class="max-w-[1920px] mx-auto px-2 md:px-4 py-3 md:py-6 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">
  
  <!-- HEADER + FILTER SECTION -->
  <div class="flex-none mb-2 md:mb-4 flex flex-col w-full shrink-0">
    
    <!-- Title row with toggle button -->
    <div class="flex items-center justify-between w-full mb-2 md:mb-3">
        <div class="flex flex-col gap-0.5 md:gap-1.5 min-w-0 flex-1">
          <h1 class="text-base md:text-2xl font-bold flex items-center gap-1.5 md:gap-2 text-slate-800">
            <span class="p-1 md:p-2 bg-blue-600 text-white rounded-lg shadow-sm text-xs md:text-sm shrink-0">
              <svg class="w-3.5 h-3.5 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v10"></path></svg>
            </span>
            <span id="otpTitle" class="truncate">Ontime Payment (OTP) - DPD 0</span>
          </h1>
          <p class="text-[8px] md:text-xs text-slate-500 font-medium tracking-wide ml-0 md:ml-11">*Data OTP = Target (M-1) / Total Bayar (Aktual)*</p>
        </div>

        <!-- Toggle Filter Button -->
        <button type="button" id="btnToggleFilter" onclick="toggleMainFilter()" class="h-[30px] md:h-[34px] px-2.5 md:px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] md:text-xs whitespace-nowrap ml-2 shrink-0 hover:bg-slate-50">
            <svg id="iconFilterToggle" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            <span class="hidden sm:inline">Filter</span>
            <svg id="iconChevron" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
        </button>
    </div>

    <!-- Filter Form (collapsible) -->
    <div id="filterWrapperMain" class="filter-transition w-full">
        <form id="formFilterRR" class="bg-white p-2 md:p-2.5 rounded-lg md:rounded-xl border border-slate-200 shadow-sm w-full" onsubmit="event.preventDefault(); fetchRekapRR();">
            
            <div class="flex flex-wrap items-end gap-1.5 md:gap-2 w-full">
                <div class="field w-[calc(50%-4px)] sm:w-auto sm:flex-none" id="wrap-closing">
                    <label class="lbl text-blue-700">CLOSING (M-1)</label>
                    <input type="date" id="closing_date" class="inp w-full sm:w-[130px] text-[10px] md:text-sm font-semibold h-[30px] md:h-[38px] px-1 md:px-3 text-slate-700 cursor-pointer" required onclick="try{this.showPicker()}catch(e){}">
                </div>
                <div class="field w-[calc(50%-4px)] sm:w-auto sm:flex-none">
                    <label class="lbl">ACTUAL (HARIAN)</label>
                    <input type="date" id="harian_date" class="inp w-full sm:w-[130px] text-[10px] md:text-sm font-semibold h-[30px] md:h-[38px] px-1 md:px-3 text-slate-700 cursor-pointer" required onclick="try{this.showPicker()}catch(e){}">
                </div>
                
                <div class="w-px h-6 bg-slate-200 shrink-0 mx-0.5 hidden md:block mb-1.5"></div>

                <div class="field w-[calc(50%-4px)] sm:w-auto sm:flex-none">
                    <label class="lbl text-slate-600">CABANG</label>
                    <select id="opt_kantor" class="inp border-slate-200 focus:border-blue-500 bg-slate-50/50 text-[10px] md:text-sm font-bold h-[30px] md:h-[38px] px-2 text-slate-700 cursor-pointer w-full sm:w-[200px] truncate" onchange="handleCabangChangeOtp()">
                        <option value="">Loading...</option>
                    </select>
                </div>

                <div class="field w-[calc(50%-4px)] sm:w-auto sm:flex-none">
                    <label id="lbl_sub_otp" class="lbl text-slate-600">KORWIL</label>
                    <select id="opt_sub_otp" class="inp border-slate-200 focus:border-blue-500 bg-slate-50/50 text-[10px] md:text-sm font-bold h-[30px] md:h-[38px] px-2 text-slate-700 cursor-pointer w-full sm:w-[150px] truncate" onchange="fetchRekapRR()">
                        <option value="">ALL KORWIL</option>
                        <option value="SEMARANG">SEMARANG</option>
                        <option value="SOLO">SOLO</option>
                        <option value="BANYUMAS">BANYUMAS</option>
                        <option value="PEKALONGAN">PEKALONGAN</option>
                    </select>
                </div>

                <div class="field w-[calc(50%-4px)] sm:w-auto sm:flex-none">
                    <label class="lbl text-blue-700">DPD BUCKET</label>
                    <select id="opt_dpd_bucket" class="inp border-blue-200 focus:border-blue-500 bg-blue-50/50 text-[10px] md:text-sm font-bold h-[30px] md:h-[38px] px-2 text-blue-700 cursor-pointer w-full sm:w-[120px]" onchange="fetchRekapRR()">
                        <option value="dpd0">DPD 0</option>
                        <option value="dpd1-30">DPD 1-30</option>
                    </select>
                </div>
                
                <!-- TOGGLE PRODUK 127 -->
                <div class="flex items-center gap-1.5 shrink-0 h-[30px] md:h-[38px] px-2 md:px-3 bg-slate-100 border border-slate-200 rounded-md md:rounded-lg cursor-pointer hover:bg-slate-200 transition" onclick="document.getElementById('chk_127').click()">
                    <input type="checkbox" id="chk_127" class="w-3.5 h-3.5 md:w-4 md:h-4 text-blue-600 bg-white border-slate-300 rounded focus:ring-blue-500 cursor-pointer" onclick="event.stopPropagation()" onchange="fetchRekapRR()">
                    <span class="text-[9px] md:text-xs font-bold text-slate-700 uppercase tracking-wide select-none">KPP</span>
                </div>

                <div class="flex items-center gap-1 h-[30px] md:h-[38px] shrink-0 ml-auto sm:ml-0">
                    <button type="submit" id="btn-cari" class="btn-icon h-full w-[36px] md:w-[80px] bg-blue-600 hover:bg-blue-700 text-white rounded-md md:rounded-lg shadow-sm" title="Cari Data">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="md:w-[16px] md:h-[16px]"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <span class="hidden md:inline font-bold text-xs uppercase tracking-wider ml-1.5">CARI</span>
                    </button>
                    <button type="button" onclick="exportExcelRekapRR()" class="btn-icon h-full w-[36px] md:w-[40px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-md md:rounded-lg shadow-sm shrink-0" title="Download Excel">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="md:w-[18px] md:h-[18px]"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline></svg>
                    </button>
                </div>
            </div>
            
        </form>
    </div>
  </div>

  <!-- TABLE SECTION -->
  <div class="flex-1 min-h-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm relative flex flex-col">
    
    <div id="loadingRR" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
        <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
        <span>Menyiapkan Matriks...</span>
    </div>

    <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table class="min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelRekapRR">
        <thead class="uppercase bg-slate-50 text-slate-600 font-bold sticky top-0 z-50 text-[9px] md:text-xs tracking-wider select-none" id="headRR">
          </thead>
        <tbody id="bodyRR" class="divide-y divide-slate-100 bg-white group-tbody text-[10px] md:text-sm"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL DETAIL -->
<div id="modalDetailRR" class="fixed inset-0 hidden z-[9999] flex items-end md:items-center justify-center p-0 sm:p-4">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalRR()"></div>
  <div class="relative bg-white w-full h-[95vh] md:h-[92vh] max-w-[1600px] rounded-t-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up">
    
    <div class="flex flex-col bg-white border-b shrink-0 w-full z-50">
        <div class="flex flex-row items-center justify-between px-3 py-2 md:px-4 md:py-3 gap-2 w-full">
            
            <div class="flex-1 min-w-0" id="modal-title-container">
              <h3 class="font-bold text-slate-800 flex items-center gap-1.5 text-[11px] md:text-xl leading-tight truncate">
                  <span class="w-1.5 md:w-2 h-4 md:h-6 bg-blue-600 rounded-full hidden md:block shrink-0"></span> 
                  <span id="modalTitleRR" class="truncate">Detail Penagihan</span>
              </h3>
              <p class="text-[8px] md:text-sm text-slate-500 mt-0.5 md:mt-1 md:ml-4 font-mono font-medium leading-none truncate" id="modalSubTitleRR">...</p>
            </div>
            
            <div class="flex flex-row items-center gap-1.5 md:gap-2 shrink-0">
                <div class="relative w-[100px] md:w-[200px] shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                        <svg class="w-3 h-3 md:w-3.5 md:h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="search_nasabah" onkeyup="filterTableDetail()" class="w-full pl-7 md:pl-8 pr-2 md:pr-3 py-1.5 h-[28px] md:h-[32px] bg-slate-50 border border-slate-200 rounded-lg text-[9px] md:text-xs outline-none focus:border-blue-500 focus:bg-white transition-all placeholder-slate-400 font-medium" placeholder="Cari nama...">
                </div>
                
                <!-- Toggle filter modal -->
                <button type="button" onclick="toggleModalFilter()" id="btnToggleModalFilter" class="md:hidden h-[28px] w-[28px] bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 rounded-lg flex items-center justify-center transition shrink-0">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </button>
                
                <button onclick="closeModalRR()" class="w-[28px] h-[28px] md:w-[32px] md:h-[32px] flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-500 hover:text-white text-red-500 transition font-bold text-lg md:text-xl leading-none shrink-0">&times;</button>
            </div>
        </div>

        <div id="modalFilterWrapper" class="hidden md:block w-full border-t border-slate-100 md:border-none transition-all">
            <div class="flex flex-row items-center justify-end gap-1.5 md:gap-2 px-3 py-2 md:px-4 md:pb-3 overflow-x-auto no-scrollbar">
                <select id="opt_kankas_modal" class="inp px-1 md:px-2 h-[28px] md:h-[32px] w-[90px] md:w-[140px] text-[9px] md:text-xs font-bold text-blue-800 bg-blue-50/50 border-blue-200 outline-none shrink-0 cursor-pointer" onchange="onKankasModalChange()">
                    <option value="">Semua Kankas</option>
                </select>

                <select id="opt_ao_modal" class="inp px-1 md:px-2 h-[28px] md:h-[32px] w-[90px] md:w-[140px] text-[9px] md:text-xs font-bold text-slate-700 bg-slate-50 border-slate-200 outline-none shrink-0 cursor-pointer" onchange="loadDetailPage(1)">
                    <option value="">Semua AO</option>
                </select>
                
                <button onclick="downloadExcelFull()" class="btn-icon bg-emerald-600 hover:bg-emerald-700 text-white px-2 md:px-3 h-[28px] md:h-[32px] rounded-lg shadow-sm shrink-0" title="Export Excel">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="ml-1 text-[9px] md:text-xs font-bold uppercase tracking-wider">Export</span>
                </button>
            </div>
        </div>

    </div>

    <div class="flex-1 overflow-auto bg-slate-50 relative p-0 md:p-3 custom-scrollbar">
      <div id="loadingModalRR" class="hidden absolute inset-0 bg-white/90 z-[60] flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
         <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
         <span class="text-[10px] md:text-sm font-bold uppercase tracking-widest">Memuat Detail...</span>
      </div>
      
      <table class="w-max min-w-full text-center md:text-left text-slate-700 md:rounded-xl shadow-sm bg-white table-fixed" id="tableExportRR">
        <thead id="headModalRR" class="text-[9px] md:text-xs text-slate-600 uppercase font-bold tracking-wider select-none"></thead>
        <tbody id="bodyModalRR" class="divide-y divide-slate-100 bg-white modal-tbody text-[9.5px] md:text-xs"></tbody>
      </table>
    </div>

    <div class="px-3 py-2 md:px-5 md:py-4 border-t bg-white flex justify-between items-center shrink-0">
      <span class="text-[8px] md:text-sm font-bold text-slate-600 bg-slate-100 px-2 md:px-3 py-1 rounded-md md:rounded-lg" id="pageInfoRR">0 Data</span>
      <div class="flex gap-1 md:gap-2">
          <button id="btnPrevRR" onclick="changePageDetail(-1)" class="px-2 md:px-4 py-1 md:py-2 bg-white border border-slate-300 rounded-md md:rounded-lg text-[9px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">« Prev</button>
          <button id="btnNextRR" onclick="changePageDetail(1)" class="px-2 md:px-4 py-1 md:py-2 bg-white border border-slate-300 rounded-md md:rounded-lg text-[9px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">Next »</button>
      </div>
    </div>
  </div>
</div>


<script>
  /* CONFIGURATION */
  const API_RR_URL = './api/rr'; 
  const API_KODE_URL = './api/kode/'; 
  const nfID = new Intl.NumberFormat('id-ID');
  const fmt  = n => nfID.format(Number(n||0));
  
  let rekapDataRaw = [];
  let rekapGtRaw = null;
  let detailDataCache = [];
  let allDetailDataForDropdown = []; // cache semua data detail untuk populate dropdown

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

  let sortMainCol = '';
  let sortMainAsc = true;
  let sortDetailCol = '';
  let sortDetailAsc = true;

  const getSortIcon = (col, currentCol, asc) => {
      if (col !== currentCol) return '<span class="opacity-30 text-[8px] md:text-[10px] ml-1 font-sans">&#8597;</span>';
      return asc ? '<span class="text-blue-600 ml-1 text-[10px] md:text-[11px] font-sans">&#9650;</span>' : '<span class="text-blue-600 ml-1 text-[10px] md:text-[11px] font-sans">&#9660;</span>';
  };

  /* ============================
     FILTER TOGGLE LOGIC
     ============================ */
  let mainFilterOpen = window.innerWidth >= 1280; // xl breakpoint: default open on desktop

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

  function toggleModalFilter() {
      const el = document.getElementById('modalFilterWrapper');
      if(el.classList.contains('hidden')) {
          el.classList.remove('hidden'); el.classList.add('block');
      } else {
          el.classList.add('hidden'); el.classList.remove('block');
      }
  }

  /* ============================
     INIT ON LOAD
     ============================ */
  window.addEventListener('DOMContentLoaded', async () => {
    // Set initial filter state based on viewport
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

  // Listen for resize to auto-expand filter on desktop
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
        let h = '<option value="">ALL | SEMUA CABANG (KONSOLIDASI)</option>';
        if(j.data) j.data.filter(x => x.kode_kantor !== '000').forEach(x => { h += `<option value="${x.kode_kantor}">${x.kode_kantor} - ${x.nama_kantor}</option>`; });
        el.innerHTML = h;
    } catch { el.innerHTML = '<option value="">ALL | SEMUA CABANG (KONSOLIDASI)</option>'; }
    await handleCabangChangeOtp(true);
  }

  async function handleCabangChangeOtp(isInit = false) {
      const cabangVal = document.getElementById('opt_kantor').value;
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
          await loadKankasSubOtp(cabangVal);
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


  /* ============================
     DROPDOWN MODAL - POPULATE FROM DATA
     ============================ */
  
  // Populate kankas dropdown dari data detail yang sudah di-fetch (bukan master)
  function populateKankasFromData(data, preselect) {
      const elKankas = document.getElementById('opt_kankas_modal');
      const kankasSet = new Set();
      (data || []).forEach(r => {
          if(r.kankas && r.kankas.trim() !== '' && r.kankas !== '-') kankasSet.add(r.kankas.trim());
      });
      let h = '<option value="">Semua Kankas</option>';
      [...kankasSet].sort().forEach(k => { h += `<option value="${k}">${k}</option>`; });
      elKankas.innerHTML = h;
      if(preselect && kankasSet.has(preselect)) elKankas.value = preselect;
  }

  // Populate AO dropdown dari data detail yang sudah di-fetch (bukan master)
  function populateAOFromData(data, preselect) {
      const elAO = document.getElementById('opt_ao_modal');
      const aoMap = new Map(); // kode_ao -> nama_ao
      (data || []).forEach(r => {
          const aoKey = r.kode_ao || r.nama_ao || '';
          const aoName = r.nama_ao || r.kode_ao || '';
          if(aoKey && aoKey.trim() !== '' && aoKey !== '-') {
              if(!aoMap.has(aoKey)) aoMap.set(aoKey, aoName);
          }
      });
      let h = '<option value="">Semua AO</option>';
      [...aoMap.entries()].sort((a,b) => a[1].localeCompare(b[1])).forEach(([k, v]) => { 
          h += `<option value="${k}">${v}</option>`; 
      });
      elAO.innerHTML = h;
      if(preselect) elAO.value = preselect;
  }

  // Ketika kankas berubah di modal, filter juga AO sesuai kankas yg dipilih
  function onKankasModalChange() {
      const selectedKankas = document.getElementById('opt_kankas_modal').value;
      // Filter AO dropdown berdasarkan kankas yang dipilih
      if(selectedKankas && allDetailDataForDropdown.length > 0) {
          const filteredData = allDetailDataForDropdown.filter(r => r.kankas && r.kankas.trim() === selectedKankas);
          populateAOFromData(filteredData, '');
      } else {
          populateAOFromData(allDetailDataForDropdown, '');
      }
      loadDetailPage(1);
  }

  // Fetch all data (tanpa limit) untuk populate dropdown secara akurat
  async function fetchAllDetailForDropdown(params) {
      try {
          const payload = { ...params, page: 1, limit: 9999 };
          // Hapus filter kankas & ao agar dapat semua data
          delete payload.kode_kankas;
          delete payload.kode_ao;
          const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          if(res.ok && res.json.status === 200) {
              return res.json.data?.data || [];
          }
      } catch(e) {}
      return [];
  }

  /* ============================
     RENDER MAIN TABLE
     ============================ */
  function renderMainHeaderRR() {
      const head = document.getElementById('headRR');
      head.innerHTML = `
          <tr class="rr-row-1">
            <th rowspan="2" class="head-row px-2 border-b border-r border-slate-200 bg-slate-50 sticky left-0 shadow-[1px_0_0_#cbd5e1]" onclick="sortMainRR('tgl', 'string')">
                <div class="flex items-center justify-center">TGL ${getSortIcon('tgl', sortMainCol, sortMainAsc)}</div>
            </th>
            <th colspan="3" class="head-row px-3 border-b border-r border-slate-200 bg-slate-50 text-slate-700 cursor-default font-extrabold tracking-widest text-center">TOTAL OUTSTANDING</th>
            <th colspan="3" class="head-row px-3 border-b border-slate-200 bg-slate-50 text-purple-800 cursor-default font-extrabold tracking-widest text-center">RECOVERY / PEMBAYARAN</th>
            <th rowspan="2" class="head-row px-2 border-b border-l border-slate-200 bg-slate-50 z-40 text-center" onclick="sortMainRR('persen', 'number')">
                <div class="flex items-center justify-center">% ${getSortIcon('persen', sortMainCol, sortMainAsc)}</div>
            </th>
          </tr>
          <tr class="rr-row-2 text-[8px] md:text-[10px] uppercase tracking-wider">
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 bg-white text-blue-600 w-[140px] md:w-[180px] border-t-2 border-t-blue-500" onclick="sortMainRR('target_os', 'number')">
                <div class="flex items-center justify-center">TARGET (M-1) ${getSortIcon('target_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 bg-white text-green-600 w-[140px] md:w-[180px] border-t-2 border-t-green-500" onclick="sortMainRR('lancar_os', 'number')">
                <div class="flex items-center justify-center">OTP (LANCAR) ${getSortIcon('lancar_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 bg-white text-red-600 w-[140px] md:w-[180px] border-t-2 border-t-red-500" onclick="sortMainRR('macet_os', 'number')">
                <div class="flex items-center justify-center">DITAGIH ${getSortIcon('macet_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 bg-white text-purple-600 w-[120px] md:w-[160px] border-t-2 border-t-purple-500" onclick="sortMainRR('lunas_os', 'number')">
                <div class="flex items-center justify-center">LUNAS ${getSortIcon('lunas_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 bg-white text-purple-600 w-[120px] md:w-[160px] border-t-2 border-t-purple-500" onclick="sortMainRR('angsuran', 'number')">
                <div class="flex items-center justify-center">ANGSURAN ${getSortIcon('angsuran', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-slate-200 bg-white text-purple-600 w-[120px] md:w-[160px] border-t-2 border-t-purple-500" onclick="sortMainRR('total_bayar', 'number')">
                <div class="flex items-center justify-center">TOTAL BAYAR ${getSortIcon('total_bayar', sortMainCol, sortMainAsc)}</div>
            </th>
          </tr>
          <tr class="rr-row-tot font-bold text-[10px] md:text-sm bg-slate-100 sticky-total shadow-[0_2px_4px_-1px_rgba(0,0,0,0.05)] border-b-2 border-slate-300" id="rowTotalRRAtas"></tr>
      `;
  }

  window.sortMainRR = function(col, type) {
      if (!rekapDataRaw || rekapDataRaw.length === 0) return;
      if (sortMainCol === col) sortMainAsc = !sortMainAsc;
      else { sortMainCol = col; sortMainAsc = true; }

      rekapDataRaw.sort((a, b) => {
          let valA = a[col]; let valB = b[col];
          if (type === 'number') {
              valA = parseFloat(valA) || 0; valB = parseFloat(valB) || 0;
              return sortMainAsc ? valA - valB : valB - valA;
          } else {
              valA = String(valA || '').toLowerCase(); valB = String(valB || '').toLowerCase();
              if (valA < valB) return sortMainAsc ? -1 : 1;
              if (valA > valB) return sortMainAsc ? 1 : -1;
              return 0;
          }
      });
      renderMainHeaderRR(); renderTableRR(rekapDataRaw, rekapGtRaw);
  }


  async function fetchRekapRR(){
    const l = document.getElementById('loadingRR');
    const tb = document.getElementById('bodyRR');
    const trTotal = document.getElementById('rowTotalRRAtas'); 
    
    if(abortRR) abortRR.abort(); abortRR = new AbortController();
    l.classList.remove('hidden'); 
    tb.innerHTML = `<tr><td colspan="8" class="py-20 text-center text-slate-400 italic text-xs md:text-base">Sedang mengambil data...</td></tr>`;
    if(trTotal) trTotal.innerHTML = '';
    rekapDataRaw = []; rekapGtRaw = null; sortMainCol = ''; sortMainAsc = true;

    try {
        const cabangVal = document.getElementById('opt_kantor').value;
        const subVal = document.getElementById('opt_sub_otp').value;
        const dpdBucket = document.getElementById('opt_dpd_bucket').value;

        let reqKorwil = ""; let reqKankas = "";
        if (cabangVal === "" || cabangVal === "000") reqKorwil = subVal;
        else reqKankas = subVal;

        const titleEl = document.getElementById('otpTitle');
        if(titleEl) titleEl.textContent = dpdBucket === 'dpd0' ? 'Ontime Payment (OTP) - DPD 0' : 'Ontime Payment (OTP) - DPD 1-30';

        const payload = { 
            type: 'rekap_rr', 
            closing_date: document.getElementById('closing_date').value, 
            harian_date: document.getElementById('harian_date').value, 
            kode_kantor: cabangVal || null,
            korwil: reqKorwil,
            kode_kankas: reqKankas,
            dpd_bucket: dpdBucket,
            include_127: document.getElementById('chk_127').checked
        };
        
        const res = await apiCall(API_RR_URL, { 
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload), signal: abortRR.signal 
        });

        if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Gagal memuat data");
        
        rekapDataRaw = res.json.data.data || [];
        rekapGtRaw = res.json.data.grand_total;

        renderMainHeaderRR(); renderTableRR(rekapDataRaw, rekapGtRaw);
    } catch(err) {
        if(err.name !== 'AbortError') {
            tb.innerHTML=`<tr><td colspan="8" class="py-16 text-center text-red-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">Error: ${err.message}</td></tr>`;
        }
    } finally { l.classList.add('hidden'); }
  }

  function renderTableRR(rows, gt) {
      const tb = document.getElementById('bodyRR'); 
      const trTotal = document.getElementById('rowTotalRRAtas');
      
      tb.innerHTML = '';
      if(rows.length === 0){ tb.innerHTML = `<tr><td colspan="8" class="py-20 text-center text-slate-500 text-xs md:text-base">Tidak ada data penagihan.</td></tr>`; return; }

      if(gt && trTotal) {
        trTotal.innerHTML = `
            <th class="px-2 sticky left-0 text-center uppercase tracking-widest shadow-[1px_0_0_#cbd5e1] text-[10px] md:text-[13px] text-slate-700 bg-slate-200/50">TOTAL</th>
            <th class="border-r border-slate-300 px-2 md:px-3 text-center bg-slate-100">
                <div class="text-blue-800 font-black text-[11px] md:text-base mb-0.5"><a href="javascript:void(0)" onclick="initModalDetail('ALL','ALL')" class="hover:underline">${fmt(gt.target_os)}</a></div>
                <div class="text-[8px] md:text-[10px] text-slate-500 font-medium">NOA: <span class="font-bold text-slate-700">${fmt(gt.target_noa)}</span></div>
            </th>
            <th class="border-r border-slate-300 px-2 md:px-3 text-center bg-slate-100">
                <div class="text-green-700 font-black text-[11px] md:text-base mb-0.5"><a href="javascript:void(0)" onclick="initModalDetail('ALL','LANCAR')" class="hover:underline">${fmt(gt.lancar_os)}</a></div>
                <div class="text-[8px] md:text-[10px] text-slate-500 font-medium">NOA: <span class="font-bold text-slate-700">${fmt(gt.lancar_noa)}</span></div>
            </th>
            <th class="border-r border-slate-300 px-2 md:px-3 text-center bg-slate-100">
                <div class="text-red-600 font-black text-[11px] md:text-base mb-0.5"><a href="javascript:void(0)" onclick="initModalDetail('ALL','MENUNGGAK')" class="hover:underline">${fmt(gt.macet_os)}</a></div>
                <div class="text-[8px] md:text-[10px] text-slate-500 font-medium">NOA: <span class="font-bold text-slate-700">${fmt(gt.macet_noa)}</span></div>
            </th>
            <th class="border-r border-slate-300 px-2 md:px-3 text-center bg-slate-100">
                <div class="text-slate-700 font-black text-[11px] md:text-base mb-0.5"><a href="javascript:void(0)" onclick="initModalLunas('ALL')" class="hover:underline">${fmt(gt.lunas_os)}</a></div>
                <div class="text-[8px] md:text-[10px] text-slate-500 font-medium">NOA: <span class="font-bold text-slate-700">${fmt(gt.lunas_noa)}</span></div>
            </th>
            <th class="border-r border-slate-300 px-2 md:px-3 text-center bg-slate-100">
                <div class="text-slate-800 font-black text-[11px] md:text-base align-middle pt-1 md:pt-2"><a href="javascript:void(0)" onclick="initModalDetail('ALL','ANGSURAN')" class="hover:underline">${fmt(gt.angsuran)}</a></div>
            </th>
            <th class="px-2 md:px-3 text-center bg-slate-100">
                <div class="text-purple-700 font-black text-[11px] md:text-base align-middle pt-1 md:pt-2"><a href="javascript:void(0)" onclick="initModalDetail('ALL','TOTAL_BAYAR')" class="hover:underline">${fmt(gt.total_bayar)}</a></div>
            </th>
            <th class="px-2 text-center text-blue-700 font-black text-[12px] md:text-xl align-middle bg-slate-200/50 border-l border-slate-300">${gt.persen}%</th>
        `;
      }

      let h = '';
      rows.forEach(r => {
          const bg = (r.persen < 50 && r.target_os > 0) ? 'bg-red-50/20' : '';
          const clkAll = `<a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','ALL')" class="font-bold text-blue-700 hover:text-blue-800 hover:underline cursor-pointer block mb-0.5 text-[10px] md:text-sm">${fmt(r.target_os)}</a>`;
          const clkLcr = `<a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','LANCAR')" class="font-bold text-green-600 hover:text-green-700 hover:underline cursor-pointer block mb-0.5 text-[10px] md:text-sm">${fmt(r.lancar_os)}</a>`;
          const clkTgh = `<a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','MENUNGGAK')" class="font-bold text-red-600 hover:text-red-700 hover:underline cursor-pointer block mb-0.5 text-[10px] md:text-sm">${fmt(r.macet_os)}</a>`;
          const clkLns = `<a href="javascript:void(0)" onclick="initModalLunas('${r.tgl}')" class="font-bold text-slate-700 hover:text-blue-700 hover:underline cursor-pointer block mb-0.5 text-[10px] md:text-sm">${fmt(r.lunas_os)}</a>`;
          const clkAng = `<a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','ANGSURAN')" class="font-bold text-slate-600 hover:text-blue-700 hover:underline cursor-pointer block text-[10px] md:text-sm">${fmt(r.angsuran)}</a>`;
          const clkTotByr = `<a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','TOTAL_BAYAR')" class="font-bold text-purple-700 hover:text-purple-800 hover:underline cursor-pointer block text-[10px] md:text-sm">${fmt(r.total_bayar)}</a>`;

          h += `
            <tr class="transition border-b border-slate-100 group h-[46px] md:h-[52px] ${bg}">
                <td class="px-2 py-1.5 md:py-2 sticky left-0 bg-white border-r border-slate-100 font-mono font-bold text-slate-700 text-center shadow-[1px_0_0_#f1f5f9] text-[9.5px] md:text-sm z-20">${r.tgl}</td>
                <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center bg-blue-50/10 hover:bg-blue-50 transition">
                    ${clkAll}
                    <div class="text-[8px] md:text-[10px] text-slate-500">NOA: <span class="font-bold text-slate-600">${fmt(r.target_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center bg-green-50/10 hover:bg-green-50 transition">
                    ${clkLcr}
                    <div class="text-[8px] md:text-[10px] text-slate-500">NOA: <span class="font-bold text-slate-600">${fmt(r.lancar_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center bg-red-50/10 hover:bg-red-50 transition">
                    ${clkTgh}
                    <div class="text-[8px] md:text-[10px] text-slate-500">NOA: <span class="font-bold text-slate-600">${fmt(r.macet_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center bg-slate-50 hover:bg-slate-100 transition">
                    ${clkLns}
                    <div class="text-[8px] md:text-[10px] text-slate-500">NOA: <span class="font-bold text-slate-600">${fmt(r.lunas_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center bg-slate-50 hover:bg-slate-100 transition align-top pt-2 md:pt-3">
                    ${clkAng}
                </td>
                <td class="px-2 md:px-3 py-1.5 md:py-2 text-center bg-purple-50/10 hover:bg-purple-50 transition align-top pt-2 md:pt-3 border-r border-slate-100">
                    ${clkTotByr}
                </td>
                <td class="px-2 py-1.5 md:py-2 font-extrabold text-center text-[10px] md:text-lg align-middle ${r.persen>=90?'text-green-600':'text-orange-500'} bg-slate-50/30">${r.persen}%</td>
            </tr>`;
      });
      tb.innerHTML = h;
  }

  window.exportExcelRekapRR = function() {
      if(!rekapDataRaw || rekapDataRaw.length === 0) return alert("Tidak ada data rekap untuk didownload.");

      let csv = "Tanggal\tTarget NOA\tTarget OS\tLancar NOA\tLancar OS\tDitagih NOA\tDitagih OS\tLunas NOA\tLunas OS\tAngsuran\tTotal Bayar\tPersen Recovery\n";
      rekapDataRaw.forEach(r => {
          csv += `${r.tgl}\t${r.target_noa}\t${Math.round(r.target_os)}\t${r.lancar_noa}\t${Math.round(r.lancar_os)}\t${r.macet_noa}\t${Math.round(r.macet_os)}\t${r.lunas_noa}\t${Math.round(r.lunas_os)}\t${Math.round(r.angsuran)}\t${Math.round(r.total_bayar)}\t${r.persen}%\n`;
      });
      if(rekapGtRaw) {
          csv += `TOTAL\t${rekapGtRaw.target_noa}\t${Math.round(rekapGtRaw.target_os)}\t${rekapGtRaw.lancar_noa}\t${Math.round(rekapGtRaw.lancar_os)}\t${rekapGtRaw.macet_noa}\t${Math.round(rekapGtRaw.macet_os)}\t${rekapGtRaw.lunas_noa}\t${Math.round(rekapGtRaw.lunas_os)}\t${Math.round(rekapGtRaw.angsuran)}\t${Math.round(rekapGtRaw.total_bayar)}\t${rekapGtRaw.persen}%\n`;
      }
      const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      const tglAwal = document.getElementById("closing_date").value;
      const tglAkhir = document.getElementById("harian_date").value;
      a.download = `Rekap_RR_${tglAwal}_sd_${tglAkhir}.xls`; 
      a.click();
  }


  /* ============================
     MODAL DETAIL FUNCTIONS
     ============================ */
  function createWABtn(phone) {
      if (!phone || phone.trim() === '') return `<span class="text-slate-400 font-mono text-[9px] md:text-sm">-</span>`;
      return `<span class="text-slate-700 font-mono font-medium text-[9px] md:text-[11px]">${phone}</span>`;
  }

  function renderModalHeaderRR() {
      const mHead = document.getElementById('headModalRR');
      
      if (currentMode === 'NORMAL') {
          mHead.innerHTML = `
              <tr>
                  <th class="mod-col-rek hidden md:table-cell px-2 md:px-3 border-b border-r border-slate-300 rounded-tl-lg text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-start md:justify-center">REKENING ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="mod-col-nas px-2 md:px-4 border-b border-r border-slate-300 text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start md:justify-center">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[60px] md:w-[80px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kode_produk', 'string')">
                      <div class="flex items-center justify-center">PRODUK ${getSortIcon('kode_produk', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[200px] md:w-[350px] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start md:justify-center">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_hp', 'string')">
                      <div class="flex items-center justify-center">NO HP ${getSortIcon('no_hp', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kankas', 'string')">
                      <div class="flex items-center justify-center">KANKAS ${getSortIcon('kankas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[110px] md:w-[150px] text-center text-blue-700 cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_jatuh_tempo', 'string')">
                      <div class="flex items-center justify-center">TGL JT ${getSortIcon('tgl_jatuh_tempo', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('jml_pinjaman', 'number')">
                      <div class="flex items-center justify-end">PLAFOND ${getSortIcon('jml_pinjaman', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-blue-50 text-blue-700 border-b border-r border-blue-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-blue-100 transition select-none" onclick="sortDetailRR('os_m1', 'number')">
                      <div class="flex items-center justify-end">TARGET (M-1) ${getSortIcon('os_m1', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-green-50 text-green-700 border-b border-r border-green-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('os_curr', 'number')">
                      <div class="flex items-center justify-end">BAKI DEBET ACTUAL ${getSortIcon('os_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-red-50 text-red-700 border-b border-r border-red-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-red-100 transition select-none" onclick="sortDetailRR('totung', 'number')">
                      <div class="flex items-center justify-end">TUNGGAKAN ${getSortIcon('totung', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[50px] md:w-[70px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('dpd_curr', 'number')">
                      <div class="flex items-center justify-center">DPD ${getSortIcon('dpd_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[100px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tabungan', 'number')">
                      <div class="flex items-center justify-end">TABUNGAN ${getSortIcon('tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_tabungan', 'string')">
                      <div class="flex items-center justify-center">STAT TAB ${getSortIcon('status_tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-amber-50 text-amber-700 border-b border-r border-amber-200 w-[90px] md:w-[120px] text-right cursor-pointer hover:bg-amber-100 transition select-none" onclick="sortDetailRR('trx_bulan_lalu', 'number')">
                      <div class="flex items-center justify-end">TRX BLN LALU ${getSortIcon('trx_bulan_lalu', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-cyan-50 text-cyan-700 border-b border-r border-cyan-200 w-[90px] md:w-[120px] text-right cursor-pointer hover:bg-cyan-100 transition select-none" onclick="sortDetailRR('trx_bulan_ini', 'number')">
                      <div class="flex items-center justify-end">TRX BLN INI ${getSortIcon('trx_bulan_ini', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-slate-300 w-[100px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_ket', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_ket', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      } else {
          mHead.innerHTML = `
              <tr>
                  <th class="mod-col-nas px-2 md:px-4 border-b border-r border-slate-300 text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start md:justify-center">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[200px] md:w-[350px] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start md:justify-center">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[100px] md:w-[150px] text-center text-blue-700 cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-center">REK LAMA ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('plafon_lama', 'number')">
                      <div class="flex items-center justify-end">PLAFOND LAMA ${getSortIcon('plafon_lama', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-blue-50 text-blue-700 border-b border-r border-blue-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-blue-100 transition select-none" onclick="sortDetailRR('os_lunas', 'number')">
                      <div class="flex items-center justify-end">OS M-1 ${getSortIcon('os_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[80px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_lunas', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 bg-green-50 text-green-700 border-b border-r border-green-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('rek_baru', 'string')">
                      <div class="flex items-center justify-center">REK BARU ${getSortIcon('rek_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-green-50 text-green-700 border-b border-r border-green-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('plafond_baru', 'number')">
                      <div class="flex items-center justify-end">PLAFOND BARU ${getSortIcon('plafond_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 bg-green-50 text-green-700 border-b border-green-200 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('tgl_baru', 'string')">
                      <div class="flex items-center justify-center">TGL REALISASI ${getSortIcon('tgl_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      }
  }

  window.sortDetailRR = function(col, type) {
      if (!detailDataCache || detailDataCache.length === 0) return;
      if (sortDetailCol === col) sortDetailAsc = !sortDetailAsc;
      else { sortDetailCol = col; sortDetailAsc = true; }

      detailDataCache.sort((a, b) => {
          let valA = a[col]; let valB = b[col];
          if (type === 'number') {
              valA = parseFloat(valA) || 0; valB = parseFloat(valB) || 0;
              return sortDetailAsc ? valA - valB : valB - valA;
          } else {
              valA = String(valA || '').toLowerCase(); valB = String(valB || '').toLowerCase();
              if (valA < valB) return sortDetailAsc ? -1 : 1;
              if (valA > valB) return sortDetailAsc ? 1 : -1;
              return 0;
          }
      });
      renderModalHeaderRR(); renderTableDetailBodyRR(detailDataCache);
  }


  /* ============================
     INIT MODAL - KEY FIX: auto-filter from source
     ============================ */
  async function initModalDetail(tgl, status) {
      currentMode = 'NORMAL';
      const branch = document.getElementById('opt_kantor').value || null;
      const subVal = document.getElementById('opt_sub_otp').value;
      const dpdBucket = document.getElementById('opt_dpd_bucket').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;

      // Tentukan korwil vs kankas
      let reqKorwil = ""; let reqKankas = "";
      if (branch === "" || branch === null || branch === "000") reqKorwil = subVal;
      else reqKankas = subVal;

      // PERBAIKAN: Jika sumber data adalah KANKAS (filter kankas aktif di rekap),
      // maka otomatis set filter kankas di modal detail
      let preselectKankas = "";
      if (lblSub === "KANKAS" && subVal && subVal !== "") {
          preselectKankas = subVal; // kode_group1 dari opt_sub_otp
      }

      currentDetailParams = { 
          type: 'detail_rr', 
          closing_date: document.getElementById('closing_date').value, 
          harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, 
          korwil: reqKorwil,
          kode_kankas: reqKankas, // kirim kankas dari rekap ke API (bisa kosong = ALL)
          kode_ao: null,
          tgl_tagih: tgl, 
          status: status, 
          dpd_bucket: dpdBucket,
          include_127: document.getElementById('chk_127').checked,
          limit: detailLimit 
      };

      const bucketLabel = dpdBucket === 'dpd0' ? 'DPD 0' : 'DPD 1-30';
      document.getElementById('modalTitleRR').textContent = `Detail OTP ${bucketLabel} (Tgl ${tgl})`;
      document.getElementById('modalSubTitleRR').textContent = `Status: ${status}`;
      document.getElementById('modalDetailRR').classList.remove('hidden');
      
      document.getElementById('search_nasabah').value = '';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      // Fetch semua data (tanpa filter) untuk populate dropdown dari data aktual
      const baseParams = { 
          type: 'detail_rr', 
          closing_date: document.getElementById('closing_date').value, 
          harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, 
          korwil: reqKorwil,
          kode_kankas: reqKankas, // kankas dari rekap (bisa kosong)
          tgl_tagih: tgl, 
          status: status, 
          dpd_bucket: dpdBucket,
          include_127: document.getElementById('chk_127').checked
      };
      allDetailDataForDropdown = await fetchAllDetailForDropdown(baseParams);
      populateKankasFromData(allDetailDataForDropdown, preselectKankas);
      
      // Filter AO berdasarkan kankas yang dipilih
      if(preselectKankas) {
          const filteredForAO = allDetailDataForDropdown.filter(r => r.kankas && r.kankas.trim() === preselectKankas);
          populateAOFromData(filteredForAO, '');
      } else {
          populateAOFromData(allDetailDataForDropdown, '');
      }

      loadDetailPage(1);
  }

  async function initModalLunas(tgl) {
      currentMode = 'LUNAS';
      const branch = document.getElementById('opt_kantor').value || null;
      const subVal = document.getElementById('opt_sub_otp').value;
      const dpdBucket = document.getElementById('opt_dpd_bucket').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;

      let reqKorwil = ""; let reqKankas = "";
      if (branch === "" || branch === null || branch === "000") reqKorwil = subVal;
      else reqKankas = subVal;

      // PERBAIKAN: auto-filter kankas dari sumber
      let preselectKankas = "";
      if (lblSub === "KANKAS" && subVal && subVal !== "") {
          preselectKankas = subVal; // kode_group1 dari opt_sub_otp
      }

      currentDetailParams = { 
          type: 'detail_lunas_rr', 
          closing_date: document.getElementById('closing_date').value, 
          harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, 
          korwil: reqKorwil,
          kode_kankas: reqKankas, // kirim kankas dari rekap ke API (bisa kosong = ALL)
          kode_ao: null,
          tgl_tagih: tgl, 
          dpd_bucket: dpdBucket,
          include_127: document.getElementById('chk_127').checked,
          limit: detailLimit 
      };

      const bucketLabel = dpdBucket === 'dpd0' ? 'DPD 0' : 'DPD 1-30';
      document.getElementById('modalTitleRR').textContent = `Detail Pelunasan OTP ${bucketLabel} (Tgl ${tgl})`;
      document.getElementById('modalSubTitleRR').textContent = `Cek Refinancing vs Prospek`;
      document.getElementById('modalDetailRR').classList.remove('hidden');
      
      document.getElementById('search_nasabah').value = '';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      // Fetch all for dropdown
      const baseParams = { 
          type: 'detail_lunas_rr', 
          closing_date: document.getElementById('closing_date').value, 
          harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, 
          korwil: reqKorwil,
          kode_kankas: reqKankas,
          tgl_tagih: tgl, 
          dpd_bucket: dpdBucket,
          include_127: document.getElementById('chk_127').checked
      };
      allDetailDataForDropdown = await fetchAllDetailForDropdown(baseParams);
      populateKankasFromData(allDetailDataForDropdown, preselectKankas);
      
      if(preselectKankas) {
          const filteredForAO = allDetailDataForDropdown.filter(r => r.kankas && r.kankas.trim() === preselectKankas);
          populateAOFromData(filteredForAO, '');
      } else {
          populateAOFromData(allDetailDataForDropdown, '');
      }

      loadDetailPage(1);
  }

  window.filterTableDetail = function() {
      const input = document.getElementById("search_nasabah");
      const filter = input.value.toLowerCase();
      const tbody = document.getElementById("bodyModalRR");
      const trs = tbody.getElementsByTagName("tr");

      for (let i = 0; i < trs.length; i++) {
          const tdName = currentMode === 'NORMAL' ? trs[i].getElementsByTagName("td")[1] : trs[i].getElementsByTagName("td")[0];
          if (tdName) {
              const txtValue = tdName.textContent || tdName.innerText;
              if (txtValue.toLowerCase().indexOf(filter) > -1) {
                  trs[i].style.display = "";
              } else {
                  trs[i].style.display = "none";
              }
          }
      }
  }


  async function loadDetailPage(page) {
      const l = document.getElementById('loadingModalRR'); 
      const tb = document.getElementById('bodyModalRR'); 
      const info = document.getElementById('pageInfoRR');
      l.classList.remove('hidden'); tb.innerHTML = '';

      try {
          const kankasModal = document.getElementById('opt_kankas_modal').value;
          currentDetailParams.kode_kankas = kankasModal;

          const aoModal = document.getElementById('opt_ao_modal');
          if(aoModal) currentDetailParams.kode_ao = aoModal.value;

          const payload = { ...currentDetailParams, page: page };
          const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          
          if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Gagal memuat detail");
          
          detailDataCache = res.json.data?.data || [];
          const meta = res.json.data?.pagination || { total_records:0, total_pages:1 };

          currentDetailPage = page; currentDetailTotalPages = meta.total_pages;

          if(detailDataCache.length === 0) {
              tb.innerHTML = `<tr><td colspan="18" class="py-20 text-center text-slate-500 italic text-xs md:text-base">Tidak ada data detail.</td></tr>`;
              info.innerText = `0 Data`;
          } else {
              sortDetailCol = ''; sortDetailAsc = true;
              renderModalHeaderRR(); renderTableDetailBodyRR(detailDataCache);
              info.innerText = `Hal ${page} dari ${meta.total_pages} (${fmt(meta.total_records)} Data)`;
          }
          document.getElementById('btnPrevRR').disabled = page <= 1;
          document.getElementById('btnNextRR').disabled = page >= meta.total_pages;
      } catch(err){ 
          console.error(err); 
          tb.innerHTML = `<tr><td colspan="18" class="py-16 text-center text-red-500 font-bold tracking-widest uppercase text-[10px] md:text-sm">Gagal memuat detail</td></tr>`;
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

              h += `<tr class="transition border-b border-slate-100 h-[40px] md:h-[48px]">
                    <td class="mod-col-rek hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 font-mono text-[9.5px] md:text-sm text-slate-600">${r.no_rekening}</td>
                    <td class="mod-col-nas px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 font-bold text-slate-700 truncate text-[9.5px] md:text-sm" title="${r.nama_nasabah}">${r.nama_nasabah}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-mono text-[9px] md:text-sm text-slate-500">${r.kode_produk || '-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-slate-500 text-[9.5px] md:text-sm truncate max-w-[200px] md:max-w-[350px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center">${btnWa}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-mono text-slate-500 text-[9px] md:text-sm">${r.kankas||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-center font-bold text-[9.5px] md:text-sm text-blue-700 truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-mono text-[9.5px] md:text-sm text-slate-500">${r.tgl_jatuh_tempo||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-medium text-slate-600 text-[9.5px] md:text-sm">${fmt(r.jml_pinjaman)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-blue-100 text-right font-bold text-blue-700 bg-blue-50/30 text-[9.5px] md:text-sm">${fmt(r.os_m1)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-green-100 text-right font-bold text-green-700 bg-green-50/30 text-[9.5px] md:text-sm">${fmt(r.os_curr)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-red-100 text-right font-bold text-red-600 bg-red-50/30 text-[9.5px] md:text-sm">${fmt(r.totung)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-bold text-slate-700 text-[9.5px] md:text-sm">${r.dpd_curr}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-bold text-emerald-600 bg-emerald-50/10 text-[9.5px] md:text-sm">${fmt(r.tabungan)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center text-[9px] md:text-xs">${r.status_tabungan === 'Aman' ? '<span class="text-green-600 font-bold">Aman</span>' : '<span class="text-red-500 font-bold">Belum Aman</span>'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-amber-100 text-right font-bold text-amber-700 bg-amber-50/30 text-[9.5px] md:text-sm">${fmt(r.trx_bulan_lalu)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-cyan-100 text-right font-bold text-cyan-700 bg-cyan-50/30 text-[9.5px] md:text-sm">${fmt(r.trx_bulan_ini)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-center text-[9px] md:text-xs font-bold ${r.status_ket === 'LANCAR' ? 'text-green-600' : (r.status_ket === 'MENUNGGAK' ? 'text-red-600' : 'text-slate-600')}">${r.status_ket}</td>
                </tr>`;
          } else {
              const alamatLengkap = r.alamat || '-';
              let badge = `<span class="text-[9px] md:text-xs font-bold text-blue-700">PROSPEK</span>`;
              if(r.status_lunas === 'REFINANCING / Top Up') badge = `<span class="text-[9px] md:text-xs font-bold text-green-700">REFINANCING</span>`;

              h += `<tr class="transition border-b border-slate-100 h-[40px] md:h-[48px]">
                    <td class="mod-col-nas px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 font-bold text-slate-700 truncate text-[9.5px] md:text-sm">
                        ${r.nama_nasabah}
                        <div class="text-[8px] md:text-xs text-slate-400 font-mono mt-0.5 font-normal">ID: ${r.nasabah_id}</div>
                    </td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-slate-500 text-[9.5px] md:text-sm truncate max-w-[200px] md:max-w-[350px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-center font-bold text-[9.5px] md:text-sm text-blue-700 truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 font-mono text-[9.5px] md:text-sm text-center text-slate-600">${r.no_rekening}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-medium text-slate-600 bg-slate-50/50 text-[9.5px] md:text-sm">${fmt(r.plafon_lama)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-blue-100 text-right font-bold text-blue-700 bg-blue-50/30 text-[9.5px] md:text-sm">${fmt(r.os_lunas)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-center">${badge}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-green-100 font-mono text-[9.5px] md:text-sm text-center bg-green-50/30 text-green-800 font-bold">${r.rek_baru}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-green-100 text-right bg-green-50/30 text-green-700 font-bold text-[9.5px] md:text-sm">${fmt(r.plafond_baru)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center bg-green-50/30 text-[9.5px] md:text-sm font-medium text-green-700">${r.tgl_baru}</td>
                </tr>`;
          }
      });
      tb.innerHTML = h;
  }


  async function downloadExcelFull() {
      const btn = event.target.closest('button'); const txt = btn.innerHTML;
      btn.innerHTML = `<span class="animate-spin inline-block h-3.5 w-3.5 md:h-5 md:w-5 border-2 border-white border-t-transparent rounded-full md:mr-2"></span><span class="hidden md:inline">...</span>`;
      btn.disabled = true;

      try {
          const kankasModal = document.getElementById('opt_kankas_modal').value;
          const aoModal = document.getElementById('opt_ao_modal');
          let kodeAoVal = currentDetailParams.kode_ao;
          if (aoModal) { kodeAoVal = aoModal.value; }

          const payload = { ...currentDetailParams, kode_kankas: kankasModal, kode_ao: kodeAoVal, page: 1, limit: 10000 };
          const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          
          if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Export gagal");
          
          const rows = res.json.data?.data || [];
          if(rows.length === 0) { alert("Tidak ada data untuk diexport"); return; }

          let csv = "";
          if(currentMode === 'NORMAL') {
              csv = `No Rekening\tNama Nasabah\tKode Produk\tAlamat\tNo HP\tKankas\tNama AO\tTgl JT\tPlafond\tTarget (M-1)\tBaki Debet Actual\tTot Tunggakan\tDPD\tSaldo Tabungan\tStatus Tabungan\tTrx Bulan Lalu\tTrx Bulan Ini\tStatus Tagih\n`;
              rows.forEach(r => {
                  csv += `'${r.no_rekening}\t${r.nama_nasabah}\t${r.kode_produk||''}\t${r.alamat||''}\t'${r.no_hp||''}\t${r.kankas||''}\t${r.nama_ao}\t${r.tgl_jatuh_tempo}\t${Math.round(r.jml_pinjaman)}\t${Math.round(r.os_m1)}\t${Math.round(r.os_curr)}\t${Math.round(r.totung)}\t${r.dpd_curr}\t${Math.round(r.tabungan)}\t${r.status_tabungan}\t${Math.round(r.trx_bulan_lalu||0)}\t${Math.round(r.trx_bulan_ini||0)}\t${r.status_ket}\n`;
              });
          } else {
              csv = `Nama Nasabah\tID Nasabah\tAlamat\tNama AO\tRek Lama\tPlafond Lama\tOS Lunas (M-1)\tStatus\tRek Baru\tPlafond Baru\tTgl Realisasi Baru\n`;
              rows.forEach(r => {
                  csv += `${r.nama_nasabah}\t'${r.nasabah_id}\t${r.alamat||''}\t${r.nama_ao}\t'${r.no_rekening}\t${Math.round(r.plafon_lama)}\t${Math.round(r.os_lunas)}\t${r.status_lunas}\t'${r.rek_baru}\t${Math.round(r.plafond_baru)}\t${r.tgl_baru}\n`;
              });
          }

          const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `RR_Detail_${currentMode}_${currentDetailParams.tgl_tagih}.xls`;
          document.body.appendChild(a); a.click(); document.body.removeChild(a);

      } catch(e) { console.error(e); alert("Gagal export data."); } 
      finally { btn.innerHTML = txt; btn.disabled = false; }
  }

  window.changePageDetail = (step) => { const n = currentDetailPage + step; if (n > 0 && n <= currentDetailTotalPages) loadDetailPage(n); }
  window.closeModalRR = () => document.getElementById('modalDetailRR').classList.add('hidden');
  document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModalRR(); });
</script>

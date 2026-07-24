<style>
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  :root { --primary: #2563eb; --bg: #f8fafc; --text: #334155; }
  
  /* === GLOBAL === */
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow: hidden; }
  
  /* === CONTROLS === */
  .inp {
      box-sizing: border-box;
      border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 8px;
      font-size: 10px; background: #fff; width: 100%; height: 32px;
      min-width: 0; transition: all 0.2s; outline: none; color: #334155;
  }
  .inp:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f8fafc; color: #475569; font-weight: 600; cursor: not-allowed; border-color: #e2e8f0; }
  select.inp { appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1em; padding-right: 1.5rem; }
  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space:nowrap; }
  .field { display:flex; flex-direction:column; min-width:0; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition:transform 0.2s; height:32px; border-radius:6px; }
  .btn-icon:hover:not(:disabled) { transform:translateY(-1px); box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); }
  @media (min-width: 768px) {
      .lbl { font-size:10px; }
      .inp { border-radius:8px; padding:0 10px; font-size:12px; height:36px; }
      select.inp { padding-right:2rem; }
      .btn-icon { height:36px; border-radius:8px; }
  }
  
  /* === DATEPICKER FIX === */
  input[type="date"] { position: relative; cursor: pointer; }
  input[type="date"]::-webkit-inner-spin-button,
  input[type="date"]::-webkit-calendar-picker-indicator {
      position: absolute; top: 0; left: 0; right: 0; bottom: 0;
      width: 100%; height: 100%; opacity: 0; cursor: pointer;
  }

  /* === TABLE CONTAINER === */
  #fpScroller {
      --col1: 60px;   /* Lebar Kode */
      --col2: 190px;  /* Lebar Nama Kantor */
      --fp_headH: 40px; 
      
      position: relative;
      border: 0; border-radius: 0; background: white;
      height: 100%; overflow: auto;
      -webkit-overflow-scrolling: touch; 
  }

  table { border-collapse: separate; border-spacing: 0; width: 100%; table-layout: fixed; font-size: 12px; }
  th, td { white-space: nowrap; padding: 9px 10px; vertical-align: middle; }
  
  /* === HEADER STYLES === */
  #tabelFlowPar thead th { 
      position: sticky; top: 0; z-index: 60; 
      background: #f1f5f9; color: #475569; 
      font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; font-size: 11px;
      border-bottom: 1px solid #cbd5e1; 
  }
  #tabelFlowPar thead th.sticky-left-1,
  #tabelFlowPar thead th.sticky-left-2 {
      background-color: #e0f2fe !important;
      color: #1e3a8a;
  }
  
  /* === TOTAL ROW STICKY === */
  #fpTotalRow td { 
      position: sticky; top: var(--fp_headH); z-index: 50; 
      background: #eff6ff; color: #1e40af; font-weight: 700; 
      border-bottom: 2px solid #bfdbfe;
      box-shadow: 0 4px 6px -2px rgba(0,0,0,0.05);
  }

  /* === STICKY COLUMNS LOGIC (Desktop) === */
  .sticky-left-1 { 
      position: sticky; left: 0; z-index: 45; 
      background: #fff; border-right: 1px solid #f1f5f9; 
      width: var(--col1); min-width: var(--col1); max-width: var(--col1); text-align: center; 
  }
  .sticky-left-2 { 
      position: sticky; left: var(--col1); z-index: 44; 
      background: #fff; border-right: 1px solid #e2e8f0; 
      width: var(--col2); min-width: var(--col2); max-width: var(--col2); 
      overflow: hidden; text-overflow: ellipsis; 
  }

  #tabelFlowPar thead th.sticky-left-1 { z-index: 70; background: #f1f5f9; }
  #tabelFlowPar thead th.sticky-left-2 { z-index: 69; background: #f1f5f9; }
  
  #fpTotalRow td.sticky-left-1 { z-index: 59; background: #eff6ff; border-right: 1px solid #bfdbfe; }
  #fpTotalRow td.sticky-left-2 { z-index: 58; background: #eff6ff; border-right: 1px solid #bfdbfe; }

  #fpBody td { background-color: #fff; border-bottom: 1px solid #f1f5f9; color: #334155; }
  #fpBody tr:hover td { background-color: #f8fafc; }
  .flow-metric { display:flex; align-items:center; justify-content:space-between; gap:10px; width:100%; min-width:0; }
  .flow-metric .metric-nom { min-width:0; overflow:hidden; text-overflow:ellipsis; }
  .flow-metric .metric-noa { flex:0 0 auto; font-size:10px; color:#64748b; font-weight:800; }

  /* === MODAL STYLES === */
  #modalScroll { --colRek: 130px; --colNama: 200px; }
  #modalTableFP { width: 100%; min-width: 1700px; } 
  #modalTableFP th { position: sticky; top: 0; z-index: 30; background: #f8fafc; padding: 10px 12px; height: 40px; border-bottom: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; }
  #modalTableFP td { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 12px; }
  
  .modal-freeze-1 { position: sticky; left: 0; z-index: 35; background: #fff; border-right: 1px solid #e2e8f0; width: var(--colRek); min-width: var(--colRek); max-width: var(--colRek); }
  .modal-freeze-2 { position: sticky; left: var(--colRek); z-index: 34; background: #fff; border-right: 1px solid #e2e8f0; width: var(--colNama); min-width: var(--colNama); max-width: var(--colNama); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  
  #modalTableFP th.modal-freeze-1 { z-index: 40; background: #f8fafc; }
  #modalTableFP th.modal-freeze-2 { z-index: 39; background: #f8fafc; }
  .modal-total-row td { position: sticky; top: 38px; z-index: 25; background: #f0f9ff; color: #0369a1; font-weight: bold; border-bottom: 1px solid #bae6fd; }
  .modal-total-row td.modal-freeze-1 { z-index: 38; background: #f0f9ff; }
  .modal-total-row td.modal-freeze-2 { z-index: 37; background: #f0f9ff; }
  .overdue td { background-color: #fef2f2 !important; color: #991b1b; }
  .hot90 { background-color: #fee2e2 !important; font-weight: bold; color: #7f1d1d; }

  /* === RESPONSIVE FIX (MOBILE) === */
  @media (max-width: 767px) {
      #opt_kantor_rec, #closing_date, #harian_date { font-size: 12px; padding: 0 8px; text-align: left; width: 100%; }

      table { font-size: 11px; }
      th, td { padding: 6px 8px; }

      .sticky-left-1 { display: none !important; }
      .sticky-left-2 { left: 0 !important; z-index: 45 !important; min-width: 140px; max-width: 160px; white-space: normal; line-height: 1.2; }
      #tabelFlowPar thead th.sticky-left-2 { z-index: 70 !important; }
      #fpTotalRow td.sticky-left-2 { z-index: 65 !important; }

      #modalScroll { --colRek: 0px; --colNama: 120px; }
      .modal-freeze-1 { display: none; }
      .modal-freeze-2 { left: 0; }
  }
</style>

<div class="max-w-[1920px] w-full mx-auto px-2 md:px-4 py-4 md:py-6 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">

  <div class="relative z-20 flex-none mb-3 md:mb-4 w-full bg-white p-2 md:p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3 shrink-0">
    
    <div class="flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
      <div>
        <h1 class="text-base md:text-xl font-extrabold flex items-center gap-2 text-slate-800 whitespace-nowrap">
            <span class="bg-blue-600 text-white p-1.5 rounded-lg text-sm md:text-base shadow-sm">📊</span> 
            <span>Rekap Flow PAR</span>
            <span id="badgeUnit" class="hidden"></span>
        </h1>
        <p class="text-[10px] md:text-xs text-slate-500 mt-1 ml-1 font-medium">*Data Posisi Closing vs Harian</p>
      </div>
      
      <button id="btnToggleFilter" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] whitespace-nowrap ml-2 shrink-0">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
          Filter
      </button>
    </div>

    <div id="filterPanel" class="hidden xl:flex w-full xl:w-auto flex-1 min-w-0 justify-end transition-all duration-300 shrink-0 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
      <form id="filterForm" class="flex flex-row flex-wrap xl:flex-nowrap items-end gap-2 md:gap-2.5 w-full xl:w-auto">
        
        <div class="flex gap-2 w-full xl:w-auto shrink-0">
            <div class="field w-1/2 xl:w-[120px]">
                <label class="lbl">CLOSING (M-1)</label>
                <input type="date" id="closing_date" class="inp font-bold text-slate-700 cursor-pointer" required onclick="this.showPicker && this.showPicker()">
            </div>
            
            <div class="field w-1/2 xl:w-[120px]">
                <label class="lbl">HARIAN (ACTUAL)</label>
                <input type="date" id="harian_date" class="inp font-bold text-slate-700 cursor-pointer" required onclick="this.showPicker && this.showPicker()">
            </div>
        </div>

        <div class="flex gap-2 w-full xl:w-auto xl:flex-1 items-end">
            <div class="field flex-1 min-w-[180px] xl:w-[260px]">
                <label class="lbl">AREA/CABANG</label>
                <select id="opt_kantor_rec" class="inp font-bold text-slate-700 truncate"><option value="ALL">Konsolidasi</option></select>
            </div>
            
            <div class="flex items-center gap-2 shrink-0 ml-auto xl:ml-0 mt-2 xl:mt-0">
              <button type="button" onclick="exportFlowParExcel()" class="btn-icon w-[32px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shrink-0" title="Download Excel">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
              </button>
            </div>
        </div>

      </form>
    </div>
  </div>
  <div class="flex-1 min-h-0 overflow-hidden bg-white rounded-xl shadow-sm border border-slate-200 relative flex flex-col z-10">
    <div id="loadingFP" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold backdrop-blur-sm rounded-lg">
       <div class="animate-spin h-10 w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-3"></div>
       <span class="text-sm tracking-wide">MEMUAT DATA...</span>
    </div>

    <div id="fpScroller" class="table-wrapper custom-scrollbar">
      <table id="tabelFlowPar" class="w-full min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed">
        <thead id="theadFP">
          <tr>
            <th class="sticky-left-1 text-center">KODE</th>
            <th class="sticky-left-2 text-left" id="thNamaFP">NAMA KANTOR</th>
            <th class="text-center w-[120px] cursor-pointer hover:bg-slate-200 transition" id="sortNoa" title="Urutkan">NOA FLOW ⬍</th>
            <th class="text-right w-[180px] cursor-pointer hover:bg-slate-200 transition" id="sortBaki" title="Urutkan">BAKI DEBET FLOW ⬍</th>
            <th class="text-center w-[120px]" title="Jatuh tempo, lainnya, atau one obligor">JT / Lain</th>
            <th class="text-center w-[120px]" title="KL karena hari menunggak pokok > 90 hari">Pokok &gt; 90</th>
            <th class="text-center w-[120px]" title="KL karena hari menunggak bunga > 90 hari">Bunga &gt; 90</th>
            <th class="text-center w-[140px]" title="KL karena pokok dan bunga > 90 hari">Pokok+Bunga</th>
          </tr>
        </thead>
        <tbody id="fpTotalRow"></tbody>
        <tbody id="fpBody"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="modalDebiturFlowPar" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm items-center justify-center z-[9999] px-2 md:px-4">
  <div id="modalCardFP" class="bg-white rounded-xl shadow-2xl flex flex-col w-full max-w-[1400px] h-[90vh] overflow-hidden animate-scale-up">
    
    <div class="flex flex-col border-b border-slate-100 bg-slate-50 shrink-0">
        <div class="flex items-center justify-between p-3 md:p-4">
            <div>
                <h3 class="font-bold text-slate-800 text-base md:text-xl flex items-center gap-2">
                    📄 <span id="modalTitleFlowPar" class="truncate max-w-[250px] md:max-w-none">Detail Debitur</span>
                </h3>
                <p class="text-[10px] md:text-xs text-slate-500 mt-1" id="modalSubtitleFP">Posisi: -</p>
            </div>
            
            <div class="flex items-center gap-2">
                <button id="btnToggleModalFilter" class="md:hidden flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 rounded-lg bg-white text-xs font-semibold text-slate-700 shadow-sm hover:bg-gray-50 focus:outline-none transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    Filter
                </button>
                <button id="btnCloseFP" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg bg-slate-200 hover:bg-red-100 hover:text-red-600 transition text-slate-600 font-bold">✕</button>
            </div>
        </div>
        
        <div id="modalFilterPanel" class="hidden md:flex flex-col md:flex-row items-stretch md:items-center justify-end gap-2 px-3 pb-3 md:px-4 md:pt-0 md:pb-4 transition-all">
            
            <div class="flex flex-row items-center gap-2 w-full md:w-auto">
                <select id="modalFilterKankas" class="inp !h-9 !py-0 text-xs w-full md:w-[250px] font-medium text-slate-700 shadow-sm" onchange="fetchDetailFlowPar()">
                    <option value="">Semua Kankas</option>
                </select>
                
                <div class="flex gap-2 shrink-0">
                    <button onclick="gotoUpdateFlowPar()" class="h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center justify-center gap-1.5" title="Update Flow PAR">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                        <span>Update</span>
                    </button>

                    <button onclick="exportDetailExcel()" class="h-9 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center justify-center gap-1.5" title="Export Excel Detail">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Excel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex-1 overflow-auto bg-white relative" id="modalScroll">
        <table id="modalTableFP">
            <thead>
                <tr>
                    <th class="modal-freeze-1 text-center">No Rekening</th>
                    <th class="modal-freeze-2 text-left">Nama Nasabah</th>
                    <th class="text-right w-[140px]">Baki Debet</th>
                    <th class="text-right w-[120px]">Tungg. Pokok</th>
                    <th class="text-right w-[120px]">Tungg. Bunga</th>
                    <th class="text-right w-[140px] text-red-700 bg-red-50">Tot. Tunggakan</th>
                    <th class="text-right w-[120px]">Saldo Tab</th>
                    <th class="text-center w-[80px]">JT</th>
                    <th class="text-center w-[60px]">DPD</th>
                    <th class="text-center w-[60px]">DPD TP</th>
                    <th class="text-center w-[60px]">DPD TB</th>
                    <th class="text-right w-[120px]">Angs. Pokok</th>
                    <th class="text-right w-[120px]">Angs. Bunga</th>
                    <th class="text-center w-[100px]">Tgl Trans</th>
                    <th class="text-left w-[180px]">Komitmen</th>
                    <th class="text-center w-[110px]">Tgl Janji Bayar</th>
                    <th class="text-right w-[130px]">Nominal Janji Bayar</th>
                </tr>
            </thead>
            <tbody id="modalTotalRow"></tbody> 
            <tbody id="modalBodyRows"></tbody> 
        </table>
    </div>
  </div>
</div>

<div id="modalPeringatan" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm items-center justify-center z-[9999] px-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden animate-scale-up">
    <div class="bg-red-50 p-4 border-b border-red-100 flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl shrink-0">
        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
      </div>
      <h3 class="font-bold text-red-800 text-lg">Akses Ditolak</h3>
    </div>
    <div class="p-6 text-center text-slate-600 text-sm">
      <p>Anda login sebagai <span class="font-bold text-blue-600 px-1 bg-blue-50 rounded" id="warnUserLvl">Cabang</span>.</p>
      <p class="mt-2">Anda tidak memiliki izin untuk melihat detail data nasabah milik <span class="font-bold text-red-600 px-1 bg-red-50 rounded" id="warnTargetLvl">Unit</span>.</p>
    </div>
    <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
      <button onclick="closeModalPeringatan()" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-bold transition shadow-sm">Mengerti</button>
    </div>
  </div>
</div>

<script>
  // --- UTILS ---
  const nfID = new Intl.NumberFormat('id-ID');
  const fmtNom = n => nfID.format(Number(n||0));
  const fmtInt = n => new Intl.NumberFormat("id-ID",{maximumFractionDigits:0}).format(+n||0);
  const num = v => Number(v||0);
  const kodeNum = v => Number(String(v??'').replace(/\D/g,'')||0);
  const formatDate = (s) => { if(!s) return '-'; const d=new Date(s); return isNaN(d)?'-': `${String(d.getDate()).padStart(2,'0')}/${String(d.getMonth()+1).padStart(2,'0')}/${d.getFullYear()}`; };
  
  function startOfDay(d){ const x=new Date(d); x.setHours(0,0,0,0); return x; }
  function endOfMonth(dateLike){ const d = new Date(dateLike); if (isNaN(d)) return null; return startOfDay(new Date(d.getFullYear(), d.getMonth()+1, 0)); }
  function formatJTByRule(jt){ if(!jt) return '-'; const d = new Date(jt); if(isNaN(d)) return '-'; const today = startOfDay(new Date()); const due = startOfDay(d); if(due < today){ const yyyy = d.getFullYear(); const mm = String(d.getMonth()+1).padStart(2,'0'); const dd = String(d.getDate()).padStart(2,'0'); return `${yyyy}-${mm}-${dd}`; } return String(d.getDate()); }
  function calcHariMenunggak(jt){ if(!jt) return 0; const d = new Date(jt); if(isNaN(d)) return 0; const today = startOfDay(new Date()); const due = startOfDay(d); const days = Math.floor((today - due) / 86400000); return days > 0 ? days : 0; }

  // --- STATE PENTING ---
  window.fpDataRaw = [];
  window.fpGtRaw = null;
  let detailDataRaw = []; 
  let sortState = { column: null, direction: 1 };
  let currentFilter = { closing:'', harian:'' };
  let currentDetailKode = ''; 
  let currentDetailKorwil = '';
  let currentDetailKlasifikasi = '';
  let fpAbort;

  const flowClassLabels = {
      jt_lain: 'JT / Lain / One Obligor',
      pokok_90: 'Pokok > 90',
      bunga_90: 'Bunga > 90',
      pokok_bunga_90: 'Pokok + Bunga > 90'
  };

  function detailTargetCode(defaultKode) {
      const dropVal = document.getElementById('opt_kantor_rec')?.value || '';
      if (dropVal.startsWith('CAB-')) return dropVal.replace('CAB-', '');
      return defaultKode || '000';
  }

  function flowLinkClass(noa, color = 'blue') {
      if (num(noa) <= 0) return 'text-slate-300 pointer-events-none';
      const colors = {
          blue: 'text-blue-700 hover:bg-blue-100 hover:text-blue-900 decoration-blue-300',
          slate: 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 decoration-slate-300',
          orange: 'text-orange-700 hover:bg-orange-100 hover:text-orange-900 decoration-orange-300',
          amber: 'text-amber-700 hover:bg-amber-100 hover:text-amber-900 decoration-amber-300',
          red: 'text-red-700 hover:bg-red-100 hover:text-red-900 decoration-red-300'
      };
      return `${colors[color] || colors.blue} font-bold px-2 py-1 rounded transition cursor-pointer underline underline-offset-2`;
  }

  function flowCellNoa(noa, kode, klasifikasi = '', color = 'blue') {
      const safeKode = String(kode || '000');
      const safeKlas = String(klasifikasi || '');
      return `<a href="#" class="${flowLinkClass(noa, color)}" onclick="event.preventDefault(); checkAccessAndOpenModal('${safeKode}', '${safeKlas}')">${fmtInt(noa)}</a>`;
  }

  function flowCellNom(noa, nominal, kode, klasifikasi = '', color = 'slate') {
      const safeKode = String(kode || '000');
      const safeKlas = String(klasifikasi || '');
      return `<a href="#" class="${flowLinkClass(noa, color)}" onclick="event.preventDefault(); checkAccessAndOpenModal('${safeKode}', '${safeKlas}')">${fmtNom(nominal)}</a>`;
  }

  function flowMetricCell(noa, nominal, kode, klasifikasi = '', color = 'blue') {
      const safeKode = String(kode || '000');
      const safeKlas = String(klasifikasi || '');
      const disabled = num(noa) <= 0 ? 'pointer-events-none opacity-45' : '';
      return `
          <a href="#" class="flow-metric ${disabled}" onclick="event.preventDefault(); checkAccessAndOpenModal('${safeKode}', '${safeKlas}')">
              <span class="metric-nom ${flowLinkClass(noa, color)}">${fmtNom(nominal)}</span>
              <span class="metric-noa">${fmtInt(noa)} NOA</span>
          </a>
      `;
  }

  function renderFlowParHeader() {
      const thead = document.getElementById('theadFP');
      if(!thead) return;
      thead.innerHTML = `
          <tr>
            <th class="sticky-left-1 text-center">KODE</th>
            <th class="sticky-left-2 text-left" id="thNamaFP">AREA</th>
            <th class="text-left cursor-pointer hover:bg-slate-200 transition" id="sortBaki" title="Urutkan nominal Flow PAR">FLOW PAR</th>
            <th class="text-left" title="Jatuh tempo, lainnya, atau one obligor">JT / LAIN</th>
            <th class="text-left" title="KL karena hari menunggak pokok > 90 hari">POKOK &gt; 90</th>
            <th class="text-left" title="KL karena hari menunggak bunga > 90 hari">BUNGA &gt; 90</th>
            <th class="text-left" title="KL karena pokok dan bunga > 90 hari">POKOK+BUNGA</th>
          </tr>
      `;
      document.getElementById('sortBaki').onclick = () => doSort('baki');
      setTimeout(updateFpStickyHeader, 20);
  }

  // TOGGLE FILTER MOBILE LOGIC (Main Page)
  document.getElementById('btnToggleFilter').addEventListener('click', function() {
      const panel = document.getElementById('filterPanel');
      panel.classList.toggle('hidden');
  });

  // TOGGLE FILTER MOBILE LOGIC (Modal)
  document.getElementById('btnToggleModalFilter').addEventListener('click', function() {
      const panel = document.getElementById('modalFilterPanel');
      panel.classList.toggle('hidden');
  });

  // Header Sticky Adjuster
  function updateFpStickyHeader() {
      const thead = document.getElementById('theadFP');
      const scroller = document.getElementById('fpScroller');
      if(thead && scroller) {
          scroller.style.setProperty('--fp_headH', (thead.offsetHeight - 1) + 'px');
      }
  }
  window.addEventListener('resize', updateFpStickyHeader);

  // --- INIT ---
  window.addEventListener('DOMContentLoaded', async () => {
    renderFlowParHeader();
    const user = (window.getUser && window.getUser()) || null;
    const uKode = user?.kode ? String(user.kode).padStart(3,'0') : '000';
    window.currentUser = { kode: uKode };

    await populateKantorOptionsFP(uKode);

    try { 
        const res = await fetch('./api/date/');
        const j = await res.json();
        if(j?.data){
            document.getElementById('closing_date').value = j.data.last_closing;
            document.getElementById('harian_date').value  = j.data.last_created;
            currentFilter = { closing: j.data.last_closing, harian: j.data.last_created };
            fetchFlowPar();
        }
    } catch(e) { 
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('closing_date').value = today;
        document.getElementById('harian_date').value = today;
        currentFilter = { closing: today, harian: today };
        fetchFlowPar();
    }
  });

  // --- POPULATE DROPDOWN KANTOR (UTAMA) ---
  async function populateKantorOptionsFP(userKode){
      const optKantor = document.getElementById('opt_kantor_rec');

      if(userKode && userKode !== '000'){
          optKantor.innerHTML = `<option value="CAB-${userKode}">CABANG ${userKode}</option>`;
          optKantor.value = `CAB-${userKode}`;
          optKantor.disabled = true;
          return; 
      }

      try {
          const res = await fetch('./api/kode/', { 
              method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) 
          });
          const json = await res.json();
          let list = json.data || [];
          
          let html = `
              <option value="ALL">Konsolidasi</option>
              <option value="KOR-SEMARANG">Korwil Semarang</option>
              <option value="KOR-SOLO">Korwil Solo</option>
              <option value="KOR-BANYUMAS">Korwil Banyumas</option>
              <option value="KOR-PEKALONGAN">Korwil Pekalongan</option>
          `;
          list.filter(x => x.kode_kantor !== '000').sort((a,b) => String(a.kode_kantor).localeCompare(b.kode_kantor)).forEach(it => {
              const kode = String(it.kode_kantor).padStart(3,'0');
              html += `<option value="CAB-${kode}">${kode} - ${it.nama_kantor}</option>`;
          });
          optKantor.innerHTML = html;
          optKantor.disabled = false;
      } catch(e){
          optKantor.innerHTML = `<option value="ALL">Error Load Area</option>`;
      }
  }

  // --- FILTER SUBMIT ---
  document.getElementById('filterForm').addEventListener('submit', e => {
    e.preventDefault();
    applyFlowParFilter();
  });

  ['closing_date', 'harian_date', 'opt_kantor_rec'].forEach(id => {
      document.getElementById(id)?.addEventListener('change', applyFlowParFilter);
  });

  function applyFlowParFilter() {
      currentFilter.closing = document.getElementById('closing_date').value;
      currentFilter.harian  = document.getElementById('harian_date').value;
      sortState = { column:null, direction:1 };
      if(window.innerWidth < 1280) {
          document.getElementById('filterPanel').classList.add('hidden');
      }
      fetchFlowPar();
  }

  // --- FETCH REKAP ---
  async function fetchFlowPar(){
    const loading = document.getElementById('loadingFP');
    loading.classList.remove('hidden'); 

    if(fpAbort) fpAbort.abort();
    fpAbort = new AbortController();

    const tbody = document.getElementById('fpBody');
    const ttotal = document.getElementById('fpTotalRow');
    tbody.innerHTML = ''; ttotal.innerHTML = '';

    const kantor = document.getElementById('opt_kantor_rec').value || 'ALL';
    document.getElementById('thNamaFP').innerText = "NAMA KANTOR";

    try {
        const payload = { 
            type: 'Flow Par', 
            closing_date: currentFilter.closing, 
            harian_date: currentFilter.harian
        };
        if(kantor.startsWith('KOR-')) {
            payload.korwil = kantor.replace('KOR-', '');
        } else if(kantor.startsWith('CAB-')) {
            payload.kode_kantor = kantor.replace('CAB-', '');
        }

        const res = await fetch('./api/flow_par/', {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify(payload),
            signal: fpAbort.signal
        });
        const json = await res.json();
        
        let data = [];
        let totalRow = null;

        if(json.data && json.data.data && json.data.grand_total) {
            data = json.data.data; 
            totalRow = json.data.grand_total;
        } else if (Array.isArray(json.data)) {
            data = json.data;
            totalRow = data.find(d => String(d.kode_cabang).toUpperCase() === 'TOTAL' || String(d.nama_kantor).toUpperCase().includes('TOTAL'));
            data = data.filter(d => d !== totalRow);
        }

        window.fpGtRaw = totalRow;
        window.fpDataRaw = data;
        window.fpDataRaw.sort((a,b) => kodeNum(a.kode_cabang || a.kode_unit) - kodeNum(b.kode_cabang || b.kode_unit));

        renderTotal(totalRow);
        renderRows(window.fpDataRaw);

    } catch(err){
        if(err.name !== 'AbortError') {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="7" class="p-8 text-center text-red-500 font-bold bg-red-50">Gagal memuat data.</td></tr>`;
        }
    } finally {
        loading.classList.add('hidden'); 
        setTimeout(updateFpStickyHeader, 50);
    }
  }

  // --- RENDER TOTAL & BISA DIKLIK ---
  function renderTotal(tot){
      const el = document.getElementById('fpTotalRow');
      if(!tot) return;

      const targetKode = detailTargetCode('000');

      el.innerHTML = `
        <tr class="row-total">
            <td class="sticky-left-1 font-mono font-bold text-slate-500 text-xs">ALL</td>
            <td class="sticky-left-2 font-bold text-slate-800 text-xs md:text-sm uppercase text-blue-900">TOTAL</td>
            <td class="font-bold bg-blue-50/50">${flowMetricCell(tot.noa_flow, tot.baki_debet_flow, targetKode, '', 'blue')}</td>
            <td class="font-bold bg-slate-50">${flowMetricCell(tot.noa_jt_lain, tot.nom_jt_lain, targetKode, 'jt_lain', 'slate')}</td>
            <td class="font-bold bg-orange-50/70">${flowMetricCell(tot.noa_pokok_90, tot.nom_pokok_90, targetKode, 'pokok_90', 'orange')}</td>
            <td class="font-bold bg-amber-50/70">${flowMetricCell(tot.noa_bunga_90, tot.nom_bunga_90, targetKode, 'bunga_90', 'amber')}</td>
            <td class="font-bold bg-red-50/70">${flowMetricCell(tot.noa_pokok_bunga_90, tot.nom_pokok_bunga_90, targetKode, 'pokok_bunga_90', 'red')}</td>
        </tr>
      `;
  }

  function renderRows(rows){
      const tbody = document.getElementById('fpBody');
      if(rows.length === 0){ tbody.innerHTML = `<tr><td colspan="7" class="p-8 text-center text-slate-400">Tidak ada data.</td></tr>`; return; }
      
      tbody.innerHTML = rows.map(r => {
          const rawKode = r.kode_cabang || r.kode_unit || '';
          const kode = String(rawKode).padStart(3,'0');
          const nama = r.nama_kantor || r.nama_unit || '-';
          
          return `
            <tr class="transition border-b">
                <td class="sticky-left-1 font-mono font-bold text-slate-500 text-xs">${kode}</td>
                <td class="sticky-left-2 font-semibold text-slate-700 text-xs md:text-sm"><div class="truncate" title="${nama}">${nama}</div></td>
                <td class="text-xs">${flowMetricCell(r.noa_flow, r.baki_debet_flow, kode, '', 'blue')}</td>
                <td class="text-xs">${flowMetricCell(r.noa_jt_lain, r.nom_jt_lain, kode, 'jt_lain', 'slate')}</td>
                <td class="text-xs">${flowMetricCell(r.noa_pokok_90, r.nom_pokok_90, kode, 'pokok_90', 'orange')}</td>
                <td class="text-xs">${flowMetricCell(r.noa_bunga_90, r.nom_bunga_90, kode, 'bunga_90', 'amber')}</td>
                <td class="text-xs">${flowMetricCell(r.noa_pokok_bunga_90, r.nom_pokok_bunga_90, kode, 'pokok_bunga_90', 'red')}</td>
            </tr>
          `;
      }).join('');

      tbody.innerHTML += `<tr style="height: 60px;"><td colspan="7" class="border-none bg-transparent"></td></tr>`;
  }

  // --- SORTING REKAP ---
  const doSort = (col) => {
      sortState = { column: col, direction: sortState.column === col ? -sortState.direction : 1 };
      const sorted = [...window.fpDataRaw].sort((a,b) => {
          const valA = num(a[col === 'noa' ? 'noa_flow' : 'baki_debet_flow']);
          const valB = num(b[col === 'noa' ? 'noa_flow' : 'baki_debet_flow']);
          return (valA - valB) * sortState.direction;
      });
      document.getElementById('sortBaki').innerText = `FLOW PAR ${sortState.direction>0?'ASC':'DESC'}`;
      renderRows(sorted);
  };

  // --- EXPORT EXCEL REKAP ---
  function exportFlowParExcel() {
      const rows = window.fpDataRaw || [];
      const gt = window.fpGtRaw || null;
      if(rows.length === 0) { alert("Tidak ada data rekap untuk diexport!"); return; }

      let table = `<table border="1">
          <thead>
              <tr>
                  <th style="background-color:#eff6ff;">KODE</th>
                  <th style="background-color:#eff6ff;">NAMA KANTOR</th>
                  <th style="background-color:#eff6ff;">NOA FLOW</th>
                  <th style="background-color:#eff6ff;">NOM FLOW</th>
                  <th style="background-color:#eff6ff;">NOA JT / LAIN / ONE OBLIGOR</th>
                  <th style="background-color:#eff6ff;">NOM JT / LAIN / ONE OBLIGOR</th>
                  <th style="background-color:#eff6ff;">NOA POKOK > 90</th>
                  <th style="background-color:#eff6ff;">NOM POKOK > 90</th>
                  <th style="background-color:#eff6ff;">NOA BUNGA > 90</th>
                  <th style="background-color:#eff6ff;">NOM BUNGA > 90</th>
                  <th style="background-color:#eff6ff;">NOA POKOK + BUNGA > 90</th>
                  <th style="background-color:#eff6ff;">NOM POKOK + BUNGA > 90</th>
              </tr>
          </thead>
          <tbody>`;
      
      if(gt) {
          table += `<tr>
              <td style="font-weight:bold;"></td>
              <td style="font-weight:bold;">${gt.nama_kantor || 'GRAND TOTAL'}</td>
              <td style="font-weight:bold;">${gt.noa_flow}</td>
              <td style="font-weight:bold;">${gt.baki_debet_flow}</td>
              <td style="font-weight:bold;">${gt.noa_jt_lain || 0}</td>
              <td style="font-weight:bold;">${gt.nom_jt_lain || 0}</td>
              <td style="font-weight:bold;">${gt.noa_pokok_90 || 0}</td>
              <td style="font-weight:bold;">${gt.nom_pokok_90 || 0}</td>
              <td style="font-weight:bold;">${gt.noa_bunga_90 || 0}</td>
              <td style="font-weight:bold;">${gt.nom_bunga_90 || 0}</td>
              <td style="font-weight:bold;">${gt.noa_pokok_bunga_90 || 0}</td>
              <td style="font-weight:bold;">${gt.nom_pokok_bunga_90 || 0}</td>
          </tr>`;
      }

      rows.forEach(r => {
          const kode = r.kode_cabang || r.kode_unit || '-';
          const nama = r.nama_kantor || r.nama_unit || '-';
          table += `<tr>
              <td style="mso-number-format:'\\@'">${kode}</td>
              <td>${nama}</td>
              <td>${r.noa_flow}</td>
              <td>${r.baki_debet_flow}</td>
              <td>${r.noa_jt_lain || 0}</td>
              <td>${r.nom_jt_lain || 0}</td>
              <td>${r.noa_pokok_90 || 0}</td>
              <td>${r.nom_pokok_90 || 0}</td>
              <td>${r.noa_bunga_90 || 0}</td>
              <td>${r.nom_bunga_90 || 0}</td>
              <td>${r.noa_pokok_bunga_90 || 0}</td>
              <td>${r.nom_pokok_bunga_90 || 0}</td>
          </tr>`;
      });
      table += `</tbody></table>`;

      const tgl = document.getElementById('harian_date').value;
      const blob = new Blob([table], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      a.download = `Rekap_Flow_PAR_${tgl}.xls`;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
  }

  // --- MODAL & ACCESS LOGIC ---
  function closeModalPeringatan() {
      const modal = document.getElementById('modalPeringatan');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
  }

  window.checkAccessAndOpenModal = function(targetKode, klasifikasi = '') {
      const userKode = window.currentUser.kode;
      const targetCabang = targetKode.length >= 3 ? targetKode.substring(0,3) : targetKode; 
      
      if (userKode !== '000' && userKode !== targetCabang) {
          document.getElementById('warnUserLvl').innerText = `Unit ${userKode}`;
          document.getElementById('warnTargetLvl').innerText = `Unit ${targetCabang}`;
          const modalWarn = document.getElementById('modalPeringatan');
          modalWarn.classList.remove('hidden');
          modalWarn.classList.add('flex');
          return;
      }
      openModalDetail(targetKode, klasifikasi);
  };

  async function openModalDetail(kode, klasifikasi = ''){
      const targetCabang = kode.length >= 3 ? kode.substring(0,3) : kode;
      const targetKankas = kode.length > 3 ? kode : '';
      const areaVal = document.getElementById('opt_kantor_rec')?.value || 'ALL';
      
      currentDetailKode = targetCabang; 
      currentDetailKorwil = (targetCabang === '000' && areaVal.startsWith('KOR-')) ? areaVal.replace('KOR-', '') : '';
      currentDetailKlasifikasi = klasifikasi || '';
      
      const modal = document.getElementById('modalDebiturFlowPar');
      const title = document.getElementById('modalTitleFlowPar');
      const sub   = document.getElementById('modalSubtitleFP');
      
      modal.classList.remove('hidden'); modal.classList.add('flex');
      let titleLabel = currentDetailKorwil ? `KORWIL ${currentDetailKorwil}` : (kode === '000' ? 'KONSOLIDASI' : kode);
      title.innerHTML = `Detail Debitur <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded font-mono border border-blue-200">${titleLabel}</span>`;
      const labelKlas = currentDetailKlasifikasi ? ` | ${flowClassLabels[currentDetailKlasifikasi] || currentDetailKlasifikasi}` : '';
      sub.innerText = `Posisi: ${formatDate(currentFilter.closing)} vs ${formatDate(currentFilter.harian)}${labelKlas}`;
      
      const selKankas = document.getElementById('modalFilterKankas');
      selKankas.innerHTML = '<option value="">Semua Kankas</option>';
      
      if(targetCabang !== '000') {
          selKankas.classList.remove('hidden');
          try {
              const r = await fetch('./api/kode/', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kankas', kode_kantor:targetCabang}) });
              const j = await r.json();
              (j.data||[]).forEach(k => { 
                  const isSelected = (targetKankas === k.kode_group1) ? 'selected' : '';
                  selKankas.innerHTML += `<option value="${k.kode_group1}" ${isSelected}>${k.kode_group1} - ${k.deskripsi_group1}</option>`; 
              });
          } catch(e){}
      } else {
          selKankas.classList.add('hidden');
      }

      // Auto close modal filter on mobile when opening
      if(window.innerWidth < 768) {
          const mPanel = document.getElementById('modalFilterPanel');
          if(mPanel) mPanel.classList.add('hidden');
      }

      fetchDetailFlowPar();
  }

  async function fetchDetailFlowPar() {
      const tbody = document.getElementById('modalBodyRows');
      const ttot  = document.getElementById('modalTotalRow');
      const kankas = document.getElementById('modalFilterKankas').value || ''; 

      // Auto tutup filter di mobile setelah fetch (change dropdown)
      if(window.innerWidth < 768) {
          document.getElementById('modalFilterPanel').classList.add('hidden');
      }

      tbody.innerHTML = `<tr><td colspan="17" class="p-12 text-center"><div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mx-auto mb-3"></div><span class="text-slate-500 font-medium">Sedang mengambil data...</span></td></tr>`;
      ttot.innerHTML = '';

      try {
          const payload = { 
              type: 'KL Baru', 
              kode_kantor: currentDetailKode === '000' ? '' : currentDetailKode, 
              korwil: currentDetailKorwil,
              kode_kankas: kankas,             
              closing_date: currentFilter.closing, 
              harian_date: currentFilter.harian,
              klasifikasi_flow: currentDetailKlasifikasi
          };
          
          const res = await fetch('./api/flow_par/', {
              method:'POST', headers:{'Content-Type':'application/json'},
              body: JSON.stringify(payload)
          });
          const json = await res.json();
          const list = Array.isArray(json.data) ? json.data : [];
          detailDataRaw = list; 

          if(list.length === 0){ tbody.innerHTML = `<tr><td colspan="17" class="p-10 text-center text-slate-400">Data tidak ditemukan.</td></tr>`; return; }

          const refDate = currentFilter.harian ? new Date(currentFilter.harian) : new Date();
          const eom = endOfMonth(refDate) || endOfMonth(new Date());
          
          let totals = { bd:0, tp:0, tb:0, tt:0, sa:0, ap:0, ab:0 };

          const rowsHtml = list.map(d => {
              const hasApiHari = (d.hari_menunggak ?? d.hari_menunggak === 0);
              let hari = hasApiHari ? Number(d.hari_menunggak) : calcHariMenunggak(d.tgl_jatuh_tempo);
              const jt = d.tgl_jatuh_tempo ? startOfDay(new Date(d.tgl_jatuh_tempo)) : null;
              const merahBaris = jt && (jt.getTime() <= eom.getTime());
              
              const dpdTP = Number.isFinite(+d.hari_menunggak_pokok) ? +d.hari_menunggak_pokok : 0;
              const dpdTB = Number.isFinite(+d.hari_menunggak_bunga) ? +d.hari_menunggak_bunga : 0;
              const hotTP = dpdTP >= 90 ? 'hot90' : '';
              const hotTB = dpdTB >= 90 ? 'hot90' : '';

              const totTunggakan = num(d.total_tunggakan) > 0 ? num(d.total_tunggakan) : (num(d.tunggakan_pokok) + num(d.tunggakan_bunga));

              totals.bd += num(d.baki_debet); totals.tp += num(d.tunggakan_pokok);
              totals.tb += num(d.tunggakan_bunga); totals.tt += totTunggakan;
              totals.sa += num(d.saldo_akhir);
              totals.ap += num(d.angsuran_pokok); totals.ab += num(d.angsuran_bunga);

              return `
                <tr class="${merahBaris ? 'overdue' : 'hover:bg-slate-50'} border-b">
                    <td class="modal-freeze-1 font-mono text-slate-600 text-xs">${d.no_rekening}</td>
                    <td class="modal-freeze-2 font-medium text-xs text-slate-700" title="${d.nama_nasabah}">${d.nama_nasabah}</td>
                    <td class="text-right text-xs">${fmtNom(d.baki_debet)}</td>
                    <td class="text-right text-xs">${fmtNom(d.tunggakan_pokok)}</td>
                    <td class="text-right text-xs">${fmtNom(d.tunggakan_bunga)}</td>
                    <td class="text-right text-xs font-bold text-red-700 bg-red-50">${fmtNom(totTunggakan)}</td>
                    <td class="text-right text-xs font-semibold text-green-700">${fmtNom(d.saldo_akhir)}</td>
                    <td class="text-center text-xs">${formatJTByRule(d.tgl_jatuh_tempo)}</td>
                    <td class="text-center text-xs font-bold">${fmtInt(hari)}</td>
                    <td class="text-center text-xs ${hotTP}">${fmtInt(dpdTP)}</td>
                    <td class="text-center text-xs ${hotTB}">${fmtInt(dpdTB)}</td>
                    <td class="text-right text-xs text-slate-500">${fmtNom(d.angsuran_pokok)}</td>
                    <td class="text-right text-xs text-slate-500">${fmtNom(d.angsuran_bunga)}</td>
                    <td class="text-center text-xs text-slate-500">${d.tgl_trans ? formatDate(d.tgl_trans) : '-'}</td>
                    <td class="text-left text-xs text-slate-500 truncate max-w-[150px]" title="${d.komitmen}">${d.komitmen||'-'}</td>
                    <td class="text-center text-xs font-semibold text-slate-600">${d.tgl_pembayaran ? formatDate(d.tgl_pembayaran) : '-'}</td>
                    <td class="text-right text-xs font-semibold text-slate-600">${fmtNom(d.nominal)}</td>
                </tr>
              `;
          }).join('');

          ttot.innerHTML = `
            <tr class="modal-total-row">
                <td class="modal-freeze-1">TOTAL</td>
                <td class="modal-freeze-2">${list.length} Debitur</td>
                <td class="text-right">${fmtNom(totals.bd)}</td>
                <td class="text-right">${fmtNom(totals.tp)}</td>
                <td class="text-right">${fmtNom(totals.tb)}</td>
                <td class="text-right text-red-700">${fmtNom(totals.tt)}</td>
                <td class="text-right">${fmtNom(totals.sa)}</td>
                <td colspan="4"></td>
                <td class="text-right">${fmtNom(totals.ap)}</td>
                <td class="text-right">${fmtNom(totals.ab)}</td>
                <td colspan="4"></td> 
            </tr>
          `;
          tbody.innerHTML = rowsHtml;

      } catch(e){
          console.error(e); tbody.innerHTML = `<tr><td colspan="17" class="p-10 text-center text-red-500">Gagal load data.</td></tr>`;
      }
  }

  // --- EXPORT EXCEL DETAIL ---
  function exportDetailExcel() {
      if(detailDataRaw.length === 0) { alert("Tidak ada detail untuk diexport!"); return; }

      let table = `<table border="1">
          <thead>
              <tr>
                  <th style="background-color:#f1f5f9;">NO REKENING</th>
                  <th style="background-color:#f1f5f9;">NAMA NASABAH</th>
                  <th style="background-color:#f1f5f9;">BAKI DEBET</th>
                  <th style="background-color:#f1f5f9;">TUNGG. POKOK</th>
                  <th style="background-color:#f1f5f9;">TUNGG. BUNGA</th>
                  <th style="background-color:#fee2e2;">TOT. TUNGGAKAN</th>
                  <th style="background-color:#f1f5f9;">SALDO TAB</th>
                  <th style="background-color:#f1f5f9;">JT</th>
                  <th style="background-color:#f1f5f9;">DPD</th>
                  <th style="background-color:#f1f5f9;">DPD TP</th>
                  <th style="background-color:#f1f5f9;">DPD TB</th>
                  <th style="background-color:#f1f5f9;">ANGS. POKOK</th>
                  <th style="background-color:#f1f5f9;">ANGS. BUNGA</th>
                  <th style="background-color:#f1f5f9;">TGL TRANS</th>
                  <th style="background-color:#f1f5f9;">KOMITMEN</th>
                  <th style="background-color:#f1f5f9;">TGL JANJI BAYAR</th>
                  <th style="background-color:#f1f5f9;">NOMINAL JANJI BAYAR</th>
              </tr>
          </thead>
          <tbody>`;

      detailDataRaw.forEach(d => {
          const totTunggakan = num(d.total_tunggakan) > 0 ? num(d.total_tunggakan) : (num(d.tunggakan_pokok) + num(d.tunggakan_bunga));
          table += `<tr>
              <td style="mso-number-format:'\\@'">${d.no_rekening}</td>
              <td>${d.nama_nasabah}</td>
              <td>${d.baki_debet}</td>
              <td>${d.tunggakan_pokok}</td>
              <td>${d.tunggakan_bunga}</td>
              <td style="background-color:#fef2f2;">${totTunggakan}</td>
              <td>${d.saldo_akhir}</td>
              <td>${d.tgl_jatuh_tempo || ''}</td>
              <td>${d.hari_menunggak || 0}</td>
              <td>${d.hari_menunggak_pokok || 0}</td>
              <td>${d.hari_menunggak_bunga || 0}</td>
              <td>${d.angsuran_pokok}</td>
              <td>${d.angsuran_bunga}</td>
              <td>${d.tgl_trans || ''}</td>
              <td>${d.komitmen || ''}</td>
              <td>${d.tgl_pembayaran || ''}</td>
              <td>${d.nominal || ''}</td>
          </tr>`;
      });
      table += `</tbody></table>`;

      const blob = new Blob([table], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      
      const valKankas = document.getElementById('modalFilterKankas').value;
      const downloadName = valKankas ? valKankas : currentDetailKode;
      
      a.download = `Detail_FlowPAR_${downloadName}.xls`;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
  }

  // --- TRIGGER UPDATE BULK (TOMBOL UPDATE DI ATAS) ---
  window.gotoUpdateFlowPar = function() {
      const selectedKankas = document.getElementById('modalFilterKankas').value || '';
      const payload = {
          kode_kantor: currentDetailKode === '000' ? '' : currentDetailKode,
          kode_kankas: selectedKankas, 
          closing_date: currentFilter.closing,
          harian_date: currentFilter.harian
      };
      sessionStorage.setItem("flowpar_update", JSON.stringify(payload));
      window.location.href = './update_flowpar'; 
  };

  document.getElementById('btnCloseFP').onclick = () => {
    document.getElementById('modalDebiturFlowPar').classList.add('hidden'); 
    document.getElementById('modalDebiturFlowPar').classList.remove('flex');
  };
</script>

<div class="max-w-7xl mx-auto px-4 py-5" id="MB_root">
  
  <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-4">
    <div class="flex items-center justify-between w-full lg:w-auto shrink-0">
        <div>
          <h1 id="MB_title" class="font-bold flex items-center gap-2 text-slate-800 text-xl md:text-2xl">
            <span class="bg-blue-600 text-white p-1.5 rounded-lg shadow-sm text-sm">📊</span>
            <span>Migrasi Bucket (DPD)</span>
          </h1>
          <p class="text-[11px] text-slate-500 mt-1.5 ml-1 font-medium">*Laporan pergerakan DPD M-1 ke Actual</p>
        </div>
        
        <button type="button" onclick="document.getElementById('MB_formFilter').classList.toggle('hidden'); document.getElementById('MB_formFilter').classList.toggle('flex');" class="lg:hidden h-[32px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[11px] whitespace-nowrap ml-2 shrink-0">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Filter
        </button>
    </div>

    <form id="MB_formFilter" class="hidden lg:flex bg-white p-2 md:p-3 rounded-xl border border-slate-200 shadow-sm flex-wrap items-end gap-3 shrink-0 w-full lg:w-auto transition-all">
      <div class="field flex flex-col gap-1 flex-1 lg:flex-none">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1" for="MB_closing">Closing</label>
        <input type="date" id="MB_closing" class="w-full border border-slate-300 rounded-lg px-2 text-sm h-9 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition cursor-pointer" required onclick="try{this.showPicker()}catch(e){}">
      </div>
      <div class="field flex flex-col gap-1 flex-1 lg:flex-none">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1" for="MB_harian">Harian</label>
        <input type="date" id="MB_harian" class="w-full border border-slate-300 rounded-lg px-2 text-sm h-9 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition cursor-pointer" required onclick="try{this.showPicker()}catch(e){}">
      </div>
      <div class="field flex flex-col gap-1 w-full lg:w-auto">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1" for="MB_optKantor">Cabang</label>
        <select id="MB_optKantor" class="w-full border border-slate-300 rounded-lg px-2 text-sm h-9 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition min-w-[180px] cursor-pointer">
          <option value="">Konsolidasi (Semua Cabang)</option>
        </select>
      </div>

      <div class="flex gap-2 mt-2 lg:mt-auto w-full lg:w-auto justify-end">
          <button id="MB_btnFilter" type="submit" class="btn-icon h-9 w-[42px] bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition flex items-center justify-center shrink-0" title="Terapkan Filter">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65" stroke-linecap="round"></line>
            </svg>
          </button>
          <button type="button" onclick="MB_exportRekap()" class="btn-icon h-9 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition flex items-center justify-center gap-2 shrink-0" title="Download Excel Rekap">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span class="text-xs font-bold uppercase hidden lg:block">Export</span>
          </button>
      </div>
    </form>
  </div>

  <div id="MB_summary" class="space-y-1.5 mb-3 hidden">
    <div class="flex flex-wrap items-center gap-2 text-[12px] font-medium">
      <span id="MB_chip_m1"        class="pill pill-blue shadow-sm">Grand OS M-1: <b id="MB_grand_m1">0</b></span>
      <span id="MB_chip_actual"    class="pill pill-green shadow-sm">Actual: <b id="MB_os_actual_an">0</b></span>
      <span id="MB_chip_realisasi" class="pill pill-purple shadow-sm">Realisasi OS: <b id="MB_realisasi_os">0</b></span>
      <span id="MB_chip_lunas"     class="pill pill-sky shadow-sm">Lunas (O): <b id="MB_total_lunas">0</b></span>
      <span id="MB_chip_runoff"    class="pill pill-emerald shadow-sm">Run Off: <b id="MB_total_runoff">0</b></span>
      <span class="text-[11px] text-slate-400 ml-1 italic">* OS tampil dalam <b>ribuan</b></span>
    </div>
    <div id="MB_summaryBreakdown" class="hidden mt-3">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
          <div class="text-xs font-bold text-slate-600 mb-2 uppercase tracking-wide">SC <span class="text-slate-400 font-normal">(DPD 0-30)</span></div>
          <div id="MB_sum_sc" class="space-y-1 text-xs"></div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
          <div class="text-xs font-bold text-slate-600 mb-2 uppercase tracking-wide">FE <span class="text-slate-400 font-normal">(DPD 31-180)</span></div>
          <div id="MB_sum_fe" class="space-y-1 text-xs"></div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
          <div class="text-xs font-bold text-slate-600 mb-2 uppercase tracking-wide">BE <span class="text-slate-400 font-normal">(DPD 181++)</span></div>
          <div id="MB_sum_be" class="space-y-1 text-xs"></div>
        </div>
      </div>
    </div>
  </div>

  <div id="MB_loading" class="hidden flex items-center gap-2 text-sm text-blue-600 font-bold mb-3 tracking-wider">
    <svg class="animate-spin h-5 w-5 text-blue-600" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="CurrentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
    <span>MEMUAT MATRIKS...</span>
  </div>

  <div id="MB_tblWrap" class="overflow-auto border border-slate-200 rounded-xl shadow-sm relative bg-white" style="max-height:68vh;">
    <table id="MB_table" class="min-w-full text-center table-fixed text-xs">
      <thead id="MB_thead" class="bg-slate-50 text-slate-600 font-semibold uppercase text-[10px] tracking-wider"></thead>
      <tbody id="MB_tbody" class="text-slate-700"></tbody>
    </table>
  </div>
</div>

<div id="MB_modal" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm z-[99999] flex items-center justify-center p-0 lg:px-4">
  <div id="MB_modalCard" class="bg-white max-w-[min(1500px,100vw)] w-[100vw] lg:w-[96vw] h-[100vh] lg:h-[90vh] flex flex-col rounded-none lg:rounded-2xl shadow-2xl overflow-hidden animate-scale-up">
    
    <div class="px-4 lg:px-5 py-3 lg:py-4 border-b border-slate-100 bg-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-3 shrink-0">
      <div>
        <h3 id="MB_modalTitle" class="font-bold text-slate-800 text-sm lg:text-lg flex items-center gap-2">Detail Debitur</h3>
        <p id="MB_modalSubtitle" class="text-[10px] lg:text-[11px] text-slate-500 font-mono mt-0.5"></p>
      </div>
      
      <div class="flex items-center gap-1.5 lg:gap-2 self-end md:self-auto w-full md:w-auto overflow-x-auto no-scrollbar pb-1 md:pb-0">
        <div class="relative flex-1 min-w-[130px] md:w-[180px] shrink-0">
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" id="MB_searchDetail" oninput="MB_filterDetail()" class="w-full pl-8 pr-3 py-1.5 h-8 lg:h-9 bg-white border border-slate-200 rounded-lg text-[10px] lg:text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-100 transition-all placeholder-slate-400" placeholder="Cari Norek/Nama/Alamat...">
        </div>
        
        <select id="MB_modKankas" onchange="MB_filterDetail()" class="w-24 md:w-32 px-2 py-1.5 h-8 lg:h-9 bg-white border border-slate-200 rounded-lg text-[10px] lg:text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-100 transition-all text-slate-600 shrink-0 cursor-pointer">
            <option value="">Semua Kankas</option>
        </select>
        <select id="MB_modAo" onchange="MB_filterDetail()" class="w-24 md:w-32 px-2 py-1.5 h-8 lg:h-9 bg-white border border-slate-200 rounded-lg text-[10px] lg:text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-100 transition-all text-slate-600 shrink-0 cursor-pointer">
            <option value="">Semua AO</option>
        </select>
        
        <button onclick="MB_exportDetail()" class="flex items-center justify-center gap-1.5 lg:gap-2 px-3 lg:px-4 h-8 lg:h-9 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition shrink-0">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lg:w-[16px] lg:h-[16px]"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          <span class="text-[10px] lg:text-xs font-bold uppercase tracking-wide">Excel</span>
        </button>
        <button id="MB_modalClose" class="w-8 h-8 lg:w-9 lg:h-9 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-500 hover:text-white text-red-500 transition font-bold text-lg lg:text-xl leading-none shrink-0">&times;</button>
      </div>
    </div>

    <div id="MB_modalTotals" class="px-4 lg:px-5 py-2 lg:py-3 bg-white border-b border-slate-100 text-[12px] flex flex-row overflow-x-auto gap-2 lg:gap-3 shrink-0 no-scrollbar"></div>

    <div id="MB_modalTableWrap" class="flex-1 overflow-auto bg-slate-50 relative custom-scrollbar">
      <table id="MB_modalTable" class="w-max min-w-full text-xs text-left bg-white shadow-sm table-fixed">
        <thead id="MB_modalThead" class="bg-slate-100 text-slate-600 uppercase text-[10px] tracking-wider"></thead>
        <tbody id="MB_modalTbody" class="text-slate-700"></tbody>
      </table>
    </div>
    <div id="MB_pagination" class="px-4 lg:px-5 py-2 border-t border-slate-100 bg-white flex items-center justify-between gap-2 shrink-0 text-xs"></div>
  </div>
</div>

<style>
  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  /* Animasi Modal */
  @keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  :root { --colFrom:8.4rem; --col2:6.8rem; --colN:5.6rem; }
  
  /* Pills */
  .pill { padding: 4px 10px; border-radius: 6px; border: 1px solid; display: inline-block; }
  .pill-blue { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
  .pill-emerald { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
  .pill-purple { background: #faf5ff; color: #6b21a8; border-color: #e9d5ff; }
  .pill-sky { background: #e0f2fe; color: #075985; border-color: #bae6fd; }
  .pill-green { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }

  /* ==============================================
     🔥 MATRIKS UTAMA (FREEZE SISI KIRI AMAN) 🔥
     ============================================== */
  #MB_table { table-layout: fixed; border-collapse: separate; border-spacing: 0; min-width: 100%; width: max-content; }
  
  .col-from { width: var(--colFrom); min-width: var(--colFrom); max-width: var(--colFrom); }
  .col-6 { width: var(--col2); min-width: var(--col2); max-width: var(--col2); }
  .col-N { width: var(--colN); min-width: var(--colN); max-width: var(--colN); }

  #MB_table thead th { position: sticky; top: 0; z-index: 20; background: #f8fafc; border-bottom: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0; padding: 10px 6px; }
  
  /* DPD M-1 selalu Freeze */
  .sticky-col-1 { position: sticky; left: 0; z-index: 10; background: #fff; border-right: 1px solid #cbd5e1; box-shadow: inset -1px 0 0 #cbd5e1; }
  #MB_table thead th.sticky-col-1 { z-index: 30; background: #f8fafc; }
  
  /* Default Mobile: OS M-1 TIDAK FREEZE */
  .sticky-col-2 { background: #fff; border-right: 2px solid #cbd5e1; }
  #MB_table thead th.sticky-col-2 { background: #f8fafc; }

  /* FREEZE BARIS TOTAL DI BAWAH THEAD */
  #MB_row_total td { 
      position: sticky; 
      top: 41px; 
      z-index: 25; 
      background: #eff6ff !important; 
      border-bottom: 2px solid #bfdbfe; 
      box-shadow: 0 2px 4px -2px rgba(0,0,0,0.1);
  }
  #MB_row_total td.sticky-col-1 { z-index: 35 !important; background: #eff6ff !important; }
  #MB_row_total td.sticky-col-2 { background: #eff6ff !important; border-right: 2px solid #bfdbfe; }

  /* KONDISI UNTUK PC/LAPTOP: OS M-1 DI FREEZE */
  @media (min-width: 768px) {
      .sticky-col-2 { position: sticky !important; left: var(--colFrom) !important; z-index: 10; box-shadow: inset -2px 0 0 #cbd5e1; }
      #MB_table thead th.sticky-col-2 { z-index: 30 !important; }
      #MB_row_total td.sticky-col-2 { z-index: 35 !important; }
  }

  #MB_tbody tr { background: #fff; }
  #MB_tbody tr.bg-indigo-50 { background: #f8fafc !important; }
  #MB_tbody tr:hover td { background: #f1f5f9; }
  
  #MB_tbody td { padding: 8px 6px; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; }

  /* Angka & Link */
  .num-wrap { display: flex; justify-content: flex-end; }
  .num { font-variant-numeric: tabular-nums; font-size: 13px; }
  .cell-sub { display: block; font-size: 9px; color: #64748b; margin-top: 2px; }
  .cell-sub:empty { display: none; }
  .cell-link { color: inherit; font-weight: 700; cursor: pointer; transition: 0.2s; }
  .cell-link:hover { color: #2563eb; text-decoration: underline; }

  .flow-worse { background: #fef2f2; color: #b91c1c; }
  .flow-better { background: #f0fdf4; color: #15803d; }


  /* ==============================================
     🔥 TABEL MODAL DETAIL (RESPONSIF FREEZE) 🔥
     ============================================== */
  #MB_modalTable { border-collapse: separate; border-spacing: 0; }
  #MB_modalTable th, #MB_modalTable td { background-clip: padding-box; background-color: #fff; }
  
  /* Header Z-Index & Freeze */
  #MB_modalThead tr:first-child th { position: sticky !important; top: 0 !important; z-index: 40 !important; background: #f1f5f9 !important; box-shadow: inset 0 -2px 0 #cbd5e1; }
  
  #MB_modalTbody td { position: relative; z-index: 10; padding: 8px 12px; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
  
  /* Baris TOTAL dimasukkan ke Thead agar konsisten nempel dengan Thead saat digulung */
  #MB_modalThead tr.sticky-total td { position: sticky !important; top: 36px !important; z-index: 35 !important; background: #eff6ff !important; box-shadow: inset 0 -1px 0 #bfdbfe, inset 0 1px 0 #bfdbfe; font-weight: 700; color: #1e40af; }

  /* Setup Default Lebar Kolom Modals Biasa */
  #MB_modalTable{ --w-sm: 6.5rem; --w-md: 9.5rem; --w-lg: 14.0rem; --w-lgA: 18.0rem; --shrink: 1.5rem; }
  .col-sm{ min-width:calc(var(--w-sm) - var(--shrink)); max-width:calc(var(--w-sm) - var(--shrink)); }
  .col-md{ min-width:calc(var(--w-md) - var(--shrink)); max-width:calc(var(--w-md) - var(--shrink)); }
  .col-lg{ min-width:calc(var(--w-lg) - var(--shrink)); max-width:calc(var(--w-lg) - var(--shrink)); }
  .col-lgA{ min-width:var(--w-lgA); max-width:var(--w-lgA); }
  #MB_modalTable th, #MB_modalTable td{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

  /* ----------------------------------------------------
     PENGATURAN FREEZE HORIZONTAL MODAL (Norek & Nama) 
  ---------------------------------------------------- */
  .mod-col-rek { position: sticky !important; left: 0 !important; z-index: 30 !important; background-color: #fff !important; min-width: 100px; max-width: 100px; box-shadow: inset -1px 0 0 #e2e8f0; }
  
  /* Di Mobile: Nama Nasabah freeze di pojok kiri karena Rekening disembunyikan */
  .mod-col-nas { position: sticky !important; left: 0 !important; z-index: 30 !important; background-color: #fff !important; min-width: 180px; max-width: 180px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); }
  
  @media (min-width: 768px) {
      .mod-col-rek { min-width: 110px; max-width: 110px; }
      /* Di PC: Nama Nasabah geser letaknya mengikuti Rekening (110px) */
      .mod-col-nas { left: 110px !important; min-width: 250px; max-width: 250px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); }
  }

  /* Perpotongan Header/Total & Sticky Kiri Modal (Z-Index Dewa) */
  #MB_modalThead tr:first-child th.mod-col-rek, #MB_modalThead tr:first-child th.mod-col-nas { z-index: 60 !important; background-color: #e2e8f0 !important; }
  #MB_modalThead tr.sticky-total td.mod-col-rek, #MB_modalThead tr.sticky-total td.mod-col-nas { 
      z-index: 50 !important; background-color: #eff6ff !important; box-shadow: inset -1px 0 0 #bfdbfe, inset 0 1px 0 #bfdbfe, inset 0 -1px 0 #bfdbfe; 
  }

  /* Hover Efek Sticky */
  #MB_modalTbody tr:hover td { background-color: #f8fafc !important; }
  #MB_modalTbody tr:hover td.mod-col-rek, #MB_modalTbody tr:hover td.mod-col-nas { filter: brightness(0.98); }

  /* Sorting Arrows */
  .th-sort { cursor: pointer; transition: 0.2s; }
  .th-sort:hover { background: #e2e8f0 !important; }
  .th-sort:after { content: " ⬍"; font-size: 10px; color: #9ca3af; margin-left: 4px; }
  .th-sort.asc:after { content: " ▲"; color: #2563eb; }
  .th-sort.desc:after { content: " ▼"; color: #2563eb; }
</style>

<script>
(() => {
  // ===== HELPERS =====
  const getNum = v => isNaN(Number(v)) ? 0 : Number(v);
  
  const nfID = new Intl.NumberFormat('id-ID');
  const SCALE = 1000;
  const fmtK = n => nfID.format(Math.round(getNum(n)/SCALE));
  const pick = (o, keys, d=0) => { for(const k of keys){ if(o && o[k]!=null) return o[k]; } return d; };
  const $ = s => document.querySelector(s);
  const digitLen = s => String(s).replace(/[^\d]/g,'').length;
  const cut = (s,n) => { s=String(s||''); return s.length<=n ? s : (s.slice(0,n).trimEnd()+'…'); };

  function numHTML(val){
    const full = nfID.format(getNum(val));
    const short = fmtK(val);
    const d = Math.max(digitLen(short), 1);
    return `<span class="num-wrap" title="${full}"><span class="num" style="--d:${d}">${short}</span></span>`;
  }
  const dashHTML = v => getNum(v)>0 ? numHTML(v) : '<span class="text-slate-300">–</span>';

  const DPD_LABEL = {
    A:'A_DPD 0', B:'B_DPD 1-30', C:'C_DPD 31-60', D:'D_DPD 61-90',
    E:'E_DPD 91-120', F:'F_DPD 121-150', G:'G_DPD 151-180', H:'H_DPD 181-210',
    I:'I_DPD 211-240', J:'J_DPD 241-270', K:'K_DPD 271-300', L:'L_DPD 301-330',
    M:'M_DPD 331-360', N:'N_DPD >360', O:'O_LUNAS'
  };
  const shortDPD = c => (DPD_LABEL[c]||c).split('_').slice(1).join(' ');
  const BUCKET_ORDER = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];
  const idxBucket = b => (b==='O' ? -1 : BUCKET_ORDER.indexOf(String(b||'').toUpperCase()));

  const elClosing = $('#MB_closing');
  const elHarian = $('#MB_harian');
  const elKantor = $('#MB_optKantor');
  
  const elHead = $('#MB_thead');
  const elBody = $('#MB_tbody');
  const elLoad = $('#MB_loading');
  const elSummary = $('#MB_summary');

  const elMod = $('#MB_modal');
  const elModTitle = $('#MB_modalTitle');
  const elModSub = $('#MB_modalSubtitle');
  const elModTotals = $('#MB_modalTotals');
  const elModThead = $('#MB_modalThead');
  const elModTbody = $('#MB_modalTbody');

  let currentDetailData = []; 
  let currentFromRaw = '';
  let currentToRaw = '';
  let currentFilteredList = [];
  let currentPage = 1;
  const PAGE_SIZE = 20;

  document.getElementById('MB_modalClose').onclick = () => elMod.classList.add('hidden');
  elMod.addEventListener('click', e => { if(!e.target.closest('#MB_modalCard')) elMod.classList.add('hidden'); });
  window.addEventListener('keydown', e => { if(e.key==='Escape') elMod.classList.add('hidden'); });

  let ABORT, ABORT_DETAIL;
  let gIsKonsol = true;

  // INIT
  (async function init(){
    const d = await getLastDates();
    if(d){ elClosing.value=d.last_closing; elHarian.value=d.last_created; }
    await populateKantor();

    const user = (window.getUser && window.getUser()) || {};
    const kodeLogin = String(user?.kode||'').padStart(3,'0');
    if(kodeLogin && kodeLogin!=='000'){
      elKantor.value = kodeLogin; elKantor.disabled = true;
      elKantor.classList.add('bg-slate-100','text-slate-500','cursor-not-allowed');
    }
    if(elClosing.value && elHarian.value){
      fetchBucket(elClosing.value, elHarian.value, elKantor.disabled ? elKantor.value : (elKantor.value || null));
    }
  })();

  document.getElementById('MB_formFilter').addEventListener('submit', e=>{
    e.preventDefault();
    fetchBucket(elClosing.value, elHarian.value, elKantor.disabled ? elKantor.value : (elKantor.value || null));
  });
  elKantor.addEventListener('change', ()=>{
    if (!elKantor.disabled && elClosing.value && elHarian.value){
      fetchBucket(elClosing.value, elHarian.value, elKantor.value || null);
    }
  });

  async function getLastDates(){ try{ const r=await fetch('./api/date/'); const j=await r.json(); return j.data||null; }catch{ return null; } }
  
  async function populateKantor(){
    try{
      const r = await fetch('./api/kode/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'})});
      const j = await r.json();
      const list = Array.isArray(j.data)?j.data:[];
      let html = `<option value="">Konsolidasi (Semua Cabang)</option>`;
      list.filter(x=>x.kode_kantor && x.kode_kantor!=='000')
          .sort((a,b)=> String(a.kode_kantor).localeCompare(String(b.kode_kantor)))
          .forEach(it=>{
            const code=String(it.kode_kantor).padStart(3,'0');
            html += `<option value="${code}">${code} — ${it.nama_kantor||it.nama_cabang||''}</option>`;
          });
      elKantor.innerHTML = html;
    }catch{
      elKantor.innerHTML = `<option value="">Konsolidasi (Semua Cabang)</option>`;
    }
  }

  // FETCH REKAP MATRIX UTAMA
  async function fetchBucket(closing_date, harian_date, kode_kantor){
    if(ABORT) ABORT.abort();
    ABORT = new AbortController();
    gIsKonsol = !kode_kantor;
    elLoad.classList.remove('hidden'); elSummary.classList.add('hidden');
    elHead.innerHTML=''; elBody.innerHTML = `<tr><td class="py-10 text-center text-slate-400 font-medium">Sedang memproses data matriks...</td></tr>`;

    try{
      const payload = { type:'migrasi bucket', closing_date, harian_date };
      if(kode_kantor) payload.kode_kantor = kode_kantor;

      const f = (window.apiFetch || fetch);
      const r = await f('./api/kolek/', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload), signal:ABORT.signal });
      const j = await r.json();
      if(j.status !== 200) throw new Error(j.message||'Gagal memuat data');
      renderBucket(j.data || {});
    }catch(e){
      elBody.innerHTML = `<tr><td class="py-10 text-center text-red-500 font-bold">${e.message||'Gagal memuat data'}</td></tr>`;
    }finally{
      elLoad.classList.add('hidden');
    }
  }

  function renderBucket(data){
    const orderTo = (Array.isArray(data.order_to) && data.order_to.length) ? data.order_to : ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O'];
    const matrixArr = Array.isArray(data.matrix) ? data.matrix : [];
    const mtx = {};
    for(const it of matrixArr){
      const f = String(pick(it,['from_bucket','from','dpd_from','bucket_m1'],'')).toUpperCase();
      const t = String(pick(it,['to_bucket','to','dpd_to','bucket_curr'],'')).toUpperCase();
      if(!f || !t) continue;
      if(!mtx[f]) mtx[f] = {};
      mtx[f][t] = { os: getNum(pick(it,['os','os_curr','saldo','amount'],0)), noa: getNum(pick(it,['noa','count','jumlah'],0)), pct: pick(it,['actual_pct','pct'],null) };
    }
    const fromTotals = data.from_totals || {};
    const real = data.realisasi || {};
    const rTot = real.total || {noa:0, os:0};
    const rByB = real.by_bucket || {};

    let head = `<tr id="MB_headRow">
      <th class="sticky-col-1 col-from font-bold tracking-wide">DPD M-1</th>
      <th class="sticky-col-2 col-6">OS M-1 <span class="block text-slate-400 font-normal mt-0.5">(×1.000)</span></th>`;
    for(const t of orderTo) head += `<th class="col-N">→ ${t} <span class="block text-slate-400 font-normal mt-0.5">${shortDPD(t)}</span></th>`;
    head += `<th class="col-N bg-slate-100">Run Off <span class="block text-slate-400 font-normal mt-0.5">(Lunas/Angs)</span></th></tr>`;
    elHead.innerHTML = head;

    const totalByTo = Object.fromEntries(orderTo.map(t=>[t,0]));
    let grand_m1_os = 0, grand_lunas = 0, grand_runoff=0;
    const fromOrder = orderTo.filter(x=>x!=='O');
    const rowsHtml = [];

    for(const f of fromOrder){
      const ft = fromTotals[f] || {};
      const os_m1 = getNum(pick(ft,['os_m1','saldo_m1'],0));
      grand_m1_os += os_m1;

      let sumNonO = 0, lunas=0;
      const cellsHtml = [];
      for(const t of orderTo){
        const c = (mtx[f] && mtx[f][t]) || {os:0,noa:0,pct:null};
        totalByTo[t] += getNum(c.os);
        if(t==='O') lunas += getNum(c.os); else sumNonO += getNum(c.os);

        let flowCls = '';
        const fi = idxBucket(f), ti = idxBucket(t);
        if(t==='RUNOFF' || t==='O') flowCls = 'flow-better';
        else if(fi>=0 && ti>=0){ flowCls = (ti>fi) ? 'flow-worse' : (ti<fi ? 'flow-better' : ''); }

        const sub = [];
        if(getNum(c.noa)>0) sub.push(nfID.format(c.noa)+' NOA');
        if(c.pct!=null && !isNaN(c.pct) && getNum(c.pct)!==0) sub.push(Number(c.pct).toFixed(2)+'%');

        cellsHtml.push(`<td class="text-right col-N ${flowCls}">${linkCell(f,t,c.os)}<span class="cell-sub">${sub.join(' • ')}</span></td>`);
      }
      grand_lunas += lunas;
      const runOffData = data.run_off_per_from || {};
      const runoff = (runOffData[f] !== undefined && runOffData[f] !== null)
        ? getNum(runOffData[f])
        : Math.max(0, os_m1 - sumNonO);
      grand_runoff += runoff;

      rowsHtml.push(`<tr>
          <td class="text-left sticky-col-1 col-from font-bold text-slate-700">${DPD_LABEL[f]||f}</td>
          <td class="text-right sticky-col-2 col-6 text-slate-800">${dashHTML(os_m1)}</td>
          ${cellsHtml.join('')}
          <td class="text-right col-N bg-emerald-50 text-emerald-700">${linkCell(f,'RUNOFF',runoff)}</td>
        </tr>`);
    }

    let totalRow = `<tr id="MB_row_total" class="text-blue-900">
      <td class="text-left sticky-col-1 col-from font-bold">TOTAL</td>
      <td class="text-right sticky-col-2 col-6 font-bold">${dashHTML(grand_m1_os)}</td>`;
    for(const t of orderTo) totalRow += `<td class="text-right col-N font-bold">${dashHTML(totalByTo[t])}</td>`;
    totalRow += `<td class="text-right col-N font-bold">${dashHTML(grand_runoff)}</td></tr>`;

    let realRow = `<tr id="MB_row_realisasi" class="bg-indigo-50 text-slate-700">
      <td class="text-left sticky-col-1 col-from font-bold">Realisasi (Baru)</td>
      <td class="text-center sticky-col-2 col-6 font-bold">–</td>`;
    for (const t of orderTo){
      const robj = rByB[t] || {noa:0, os:0};
      realRow += `<td class="text-right col-N">${linkCell('REALISASI', t, robj.os)}<span class="cell-sub">${nfID.format(robj.noa)} NOA</span></td>`;
    }
    realRow += `<td class="text-center col-N">–</td></tr>`;

    // Posisi Baris Total dimasukkan paling atas di dalam Body
    elBody.innerHTML = totalRow + realRow + rowsHtml.join('');

    $('#MB_grand_m1').textContent = fmtK(grand_m1_os);
    $('#MB_os_actual_an').textContent = fmtK(orderTo.filter(t=>t!=='O').reduce((s,t)=>s+getNum(totalByTo[t]),0));
    $('#MB_realisasi_os').textContent = fmtK(getNum(rTot.os));
    $('#MB_total_lunas').textContent = fmtK(grand_lunas);
    $('#MB_total_runoff').textContent = fmtK(grand_runoff);

    // Render summary breakdown (Pemburukan/Stay/Perbaikan by SC/FE/BE)
    const summary = data.summary || {};
    const sumEl = document.getElementById('MB_summaryBreakdown');
    if (summary && Object.keys(summary).length > 0) {
      sumEl.classList.remove('hidden');
      ['SC','FE','BE'].forEach(tag => {
        const s = summary[tag] || {};
        const el = document.getElementById('MB_sum_' + tag.toLowerCase());
        if (el) {
          el.innerHTML = `
            <div class="flex justify-between items-center py-1 border-b border-slate-100">
              <span class="text-red-600 font-semibold">Pemburukan</span>
              <span><b>${nfID.format(s.pemburukan?.noa||0)}</b> NOA | <b>${fmtK(s.pemburukan?.os||0)}</b></span>
            </div>
            <div class="flex justify-between items-center py-1 border-b border-slate-100">
              <span class="text-slate-600 font-semibold">Stay</span>
              <span><b>${nfID.format(s.stay?.noa||0)}</b> NOA | <b>${fmtK(s.stay?.os||0)}</b></span>
            </div>
            <div class="flex justify-between items-center py-1">
              <span class="text-emerald-600 font-semibold">Perbaikan</span>
              <span><b>${nfID.format(s.perbaikan?.noa||0)}</b> NOA | <b>${fmtK(s.perbaikan?.os||0)}</b></span>
            </div>
          `;
        }
      });
    } else {
      sumEl.classList.add('hidden');
    }

    elSummary.classList.remove('hidden');
  }

  function linkCell(from_bucket, to_bucket, val){
    const n = getNum(val);
    if(n<=0) return '<span class="text-slate-300">–</span>';
    if(to_bucket === 'RUNOFF') return numHTML(n);
    return `<a href="#" class="cell-link" onclick="return MB_openDetail('${from_bucket}','${to_bucket}')">${numHTML(n)}</a>`;
  }

  // ===== FILTER PENCARIAN & KANKAS/AO CLIENT SIDE =====
  window.MB_filterDetail = function() {
      const searchInput = document.getElementById("MB_searchDetail").value.toLowerCase();
      const kankasInput = document.getElementById("MB_modKankas").value.toLowerCase();
      const aoInput     = document.getElementById("MB_modAo").value.toLowerCase();

      if (!currentDetailData) return;
      
      const filtered = currentDetailData.filter(d => {
        const rek  = String(d.no_rekening || '').toLowerCase();
        const nama = String(d.nama_nasabah || '').toLowerCase();
        const almt = String(d.alamat || '').toLowerCase();
        const knk  = String(d.kankas || '').toLowerCase();
        const aok  = String(d.ao_kredit || '').toLowerCase();
        
        // Pengecekan Filter Dropdown (Exact match/includes)
        const matchSearch = searchInput === '' || rek.includes(searchInput) || nama.includes(searchInput) || almt.includes(searchInput);
        const matchKankas = kankasInput === '' || knk === kankasInput;
        const matchAo     = aoInput === '' || aok === aoInput;

        return matchSearch && matchKankas && matchAo;
      });
      
      currentFilteredList = filtered;
      currentPage = 1;
      renderDetailTable(filtered);
  };

  // ===== MODAL DETAIL MURNI =====
  window.MB_openDetail = async function(from_raw, to_raw){
    if(ABORT_DETAIL) ABORT_DETAIL.abort();
    ABORT_DETAIL = new AbortController();

    currentFromRaw = from_raw; currentToRaw = to_raw;
    const closing = elClosing.value, harian = elHarian.value;
    const kode = elKantor.disabled ? elKantor.value : (elKantor.value || null);

    const fLabel = DPD_LABEL[from_raw] || from_raw;
    const tLabel = DPD_LABEL[to_raw] || to_raw;

    elModTitle.innerHTML = `Detail Migrasi <span class="bg-blue-100 text-blue-800 text-[10px] lg:text-xs px-2 py-1 rounded-md font-mono border border-blue-200 ml-1.5 lg:ml-2">${fLabel} ➔ ${tLabel}</span>`;
    $('#MB_modalSubtitle').textContent = `Posisi: ${closing} vs ${harian}`;
    
    // Reset Form Pencarian
    const searchInput = document.getElementById('MB_searchDetail');
    if(searchInput) searchInput.value = '';

    elMod.classList.remove('hidden');

    elModTotals.innerHTML = ''; elModThead.innerHTML = '';
    elModTbody.innerHTML = `<tr><td class="p-12 text-center text-slate-400 font-bold uppercase tracking-widest"><div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mx-auto mb-3"></div>Menarik Data Nasabah...</td></tr>`;

    try{
      // Tarik SEMUA data nasabah untuk bucket tersebut
      const payload = { 
        type: 'detail debutir migrasi', 
        closing_date: closing, 
        harian_date: harian, 
        from_bucket: from_raw, 
        to_bucket: to_raw
      };
      if(kode) payload.kode_kantor = kode;
      
      const f = (window.apiFetch || fetch);
      const r = await f('./api/kolek/', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload), signal:ABORT_DETAIL.signal });
      const j = await r.json();
      currentDetailData = Array.isArray(j?.data) ? j.data : [];
      
      if(!currentDetailData.length) {
        elModTbody.innerHTML = `<tr><td class="px-4 py-8 text-center text-slate-400">Tidak ada debitur pada kriteria ini.</td></tr>`;
        return false;
      }

      currentDetailData = currentDetailData.map(d=>{
        const t = d?.tgl_jatuh_tempo ? new Date(d.tgl_jatuh_tempo) : null;
        return { ...d, tgl_tagih: t && !isNaN(t) ? t.getDate() : null };
      });

      // ISI DATA DROPDOWN FILTER KANKAS & AO
      const uniqueKankas = [...new Set(currentDetailData.map(d => d.kankas).filter(Boolean))].sort();
      const uniqueAo = [...new Set(currentDetailData.map(d => d.ao_kredit).filter(Boolean))].sort();

      const modKankas = document.getElementById('MB_modKankas');
      if (modKankas) {
          modKankas.innerHTML = '<option value="">Semua Kankas</option>' + 
              uniqueKankas.map(k => `<option value="${k}">${k}</option>`).join('');
          modKankas.value = ''; // reset value
      }

      const modAo = document.getElementById('MB_modAo');
      if (modAo) {
          modAo.innerHTML = '<option value="">Semua AO</option>' + 
              uniqueAo.map(a => `<option value="${a}">${a}</option>`).join('');
          modAo.value = ''; // reset value
      }

      currentFilteredList = currentDetailData;
      currentPage = 1;
      renderDetailTable(currentDetailData);
    }catch(e){
      if(e.name!=='AbortError') elModTbody.innerHTML = `<tr><td class="px-4 py-8 text-center text-red-500 font-bold">Gagal menarik data.</td></tr>`;
    }
    return false;
  };

  function renderDetailTable(list) {
      const nf = new Intl.NumberFormat('id-ID');
      const sum = k => list.reduce((s,d)=> s + getNum(d?.[k]), 0);
      
      const total = {
        noa: list.length,
        os_m1: sum('os_m1'),
        os_curr: sum('os_curr'),
        ckpn_m1: sum('ckpn_m1'),
        ckpn_actual: sum('ckpn_actual'),
        pemulihan: sum('pemulihan_pembentukan'),
        angs_p: sum('angsuran_pokok'),
        angs_b: sum('angsuran_bunga'),
        tung_p: sum('tunggakan_pokok'),
        tung_b: sum('tunggakan_bunga')
      };

      // Mini Cards untuk Totals Modal
      elModTotals.innerHTML = `
        <div class="px-3 py-1.5 bg-blue-50 text-blue-900 border border-blue-100 rounded-lg min-w-[90px] lg:min-w-[100px]"><span class="block text-[9px] lg:text-[10px] uppercase font-bold text-blue-600 mb-0.5">Total NOA</span><b class="text-xs lg:text-sm">${nf.format(total.noa)}</b></div>
        <div class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg min-w-[120px] lg:min-w-[130px] shadow-sm"><span class="block text-[9px] lg:text-[10px] uppercase font-bold text-slate-500 mb-0.5">Total OS M-1</span><b class="text-xs lg:text-sm">${nf.format(total.os_m1)}</b></div>
        <div class="px-3 py-1.5 bg-purple-50 text-purple-900 border border-purple-200 rounded-lg min-w-[120px] lg:min-w-[130px] shadow-sm"><span class="block text-[9px] lg:text-[10px] uppercase font-bold text-purple-600 mb-0.5">CKPN M-1</span><b class="text-xs lg:text-sm">${nf.format(total.ckpn_m1)}</b></div>
        <div class="px-3 py-1.5 bg-emerald-50 text-emerald-900 border border-emerald-200 rounded-lg min-w-[120px] lg:min-w-[130px] shadow-sm"><span class="block text-[9px] lg:text-[10px] uppercase font-bold text-emerald-600 mb-0.5">Total OS Actual</span><b class="text-xs lg:text-sm">${nf.format(total.os_curr)}</b></div>
        <div class="px-3 py-1.5 bg-fuchsia-50 text-fuchsia-900 border border-fuchsia-200 rounded-lg min-w-[120px] lg:min-w-[130px] shadow-sm"><span class="block text-[9px] lg:text-[10px] uppercase font-bold text-fuchsia-600 mb-0.5">CKPN Actual</span><b class="text-xs lg:text-sm">${nf.format(total.ckpn_actual)}</b></div>
        <div class="px-3 py-1.5 bg-orange-50 text-orange-900 border border-orange-200 rounded-lg min-w-[120px] lg:min-w-[130px] shadow-sm"><span class="block text-[9px] lg:text-[10px] uppercase font-bold text-orange-600 mb-0.5">+/- CKPN</span><b class="text-xs lg:text-sm">${nf.format(total.pemulihan)}</b></div>
        <div class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg min-w-[110px] lg:min-w-[120px] shadow-sm"><span class="block text-[9px] lg:text-[10px] uppercase font-bold text-slate-500 mb-0.5">Angs. Pokok</span><b class="text-xs lg:text-sm">${nf.format(total.angs_p)}</b></div>
        <div class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg min-w-[110px] lg:min-w-[120px] shadow-sm"><span class="block text-[9px] lg:text-[10px] uppercase font-bold text-slate-500 mb-0.5">Angs. Bunga</span><b class="text-xs lg:text-sm">${nf.format(total.angs_b)}</b></div>
      `;

      const cols = [
        ['no_rekening','Norek','md','text', 'mod-col-rek hidden md:table-cell'],
        ['nama_nasabah','Nama Nasabah','lg','text', 'mod-col-nas'],
        ['kankas','Kankas','md','text'],        
        ['ao_kredit','AO Kredit','md','text'],  
        ['alamat','Alamat','lgA','text'],
        ['kolektibilitas','KOL','sm','text'],
        ['os_m1','OS M-1','md','num'],
        ['ckpn_m1','CKPN M-1','md','num'],
        ['os_curr','OS Act','md','num'],
        ['ckpn_actual','CKPN Act','md','num'],
        ['pemulihan_pembentukan','+/- CKPN','md','num'],
        ['pd_actual','PD (%)','sm','num'],
        ['lgd_actual','LGD (%)','sm','num'],
        ['tunggakan_pokok','T.Pokok','md','num'],
        ['tunggakan_bunga','T.Bunga','md','num'],
        ['hari_menunggak','HM','sm','num'],
        ['hari_menunggak_pokok','HMP','sm','num'],
        ['hari_menunggak_bunga','HMB','sm','num'],
        ['tgl_jatuh_tempo','JtTmp','md','text'],
        ['tgl_tagih','TglTg','sm','num'],
        ['tgl_trans_terakhir','Tgl Trans','md','text'],
        ['angsuran_pokok','AngsP','md','num'],
        ['angsuran_bunga','AngsB','md','num']
      ];

      const totalCells = cols.map(([key,label,sz,type,xtraClass])=>{
        let v = '';
        let isNum = false;
        if (key==='no_rekening') v = `<span class="hidden md:inline font-bold text-slate-700">TOTAL</span>`; 
        else if (key==='nama_nasabah') v = `<span class="md:hidden font-bold text-slate-700">TOTAL</span> <span class="font-bold text-blue-700 md:ml-1">(${nf.format(total.noa)} Akun)</span>`;
        else if (['os_m1','os_curr','ckpn_m1','ckpn_actual','pemulihan_pembentukan','angsuran_pokok','angsuran_bunga','tunggakan_pokok','tunggakan_bunga'].includes(key)){
          const mapKey = ({os_m1:'os_m1',os_curr:'os_curr',ckpn_m1:'ckpn_m1',ckpn_actual:'ckpn_actual',pemulihan_pembentukan:'pemulihan',angsuran_pokok:'angs_p',angsuran_bunga:'angs_b',tunggakan_pokok:'tung_p',tunggakan_bunga:'tung_b'})[key];
          v = nf.format(total[mapKey]);
          isNum = true;
        }
        else if (['pd_actual', 'lgd_actual'].includes(key)) {
          v = ''; 
        }
        
        let customClass = xtraClass ? xtraClass : `col-${sz}`;
        let alignClass = isNum ? 'text-right' : 'text-left';
        return `<td class="px-2 border-r border-blue-200 ${customClass} nowrap ${alignClass} font-bold text-blue-900" style="height:34px; box-sizing:border-box;">${String(v)}</td>`;
      }).join('');

      elModThead.innerHTML = `
        <tr>${
          cols.map(([key,label,sz,type,xtraClass]) => {
            let customClass = xtraClass ? xtraClass : `col-${sz}`;
            return `<th class="px-2 border-r border-slate-200 ${customClass} nowrap th-sort" data-key="${key}" data-type="${type||'text'}" title="${label}" style="height:36px; box-sizing:border-box;">${label}</th>`
          }).join('')
        }</tr>
        <tr class="sticky-total">${totalCells}</tr>
      `;

      // Pagination: slice data for current page
      const totalPages = Math.ceil(list.length / PAGE_SIZE);
      const startIdx = (currentPage - 1) * PAGE_SIZE;
      const endIdx = currentPage * PAGE_SIZE;
      const pageData = list.slice(startIdx, endIdx);

      if(list.length === 0) {
        elModTbody.innerHTML = `<tr><td colspan="${cols.length}" class="py-10 text-center text-slate-400 font-medium">Data tidak ditemukan.</td></tr>`;
      } else {
        elModTbody.innerHTML = pageData.map(d => MB_rowHtml(d, cols, nf)).join('');
      }

      // Render pagination controls
      MB_renderPagination(list.length, totalPages);

      // INIT SORTING
      const thEls = elModThead.querySelectorAll('th.th-sort');
      let sortState = { key:null, dir:1 };
      thEls.forEach(th=>{
        th.addEventListener('click', ()=>{
          const key  = th.dataset.key;
          const type = th.dataset.type || 'text';
          thEls.forEach(t=>t.classList.remove('asc','desc'));
          sortState.dir = (sortState.key===key ? -sortState.dir : 1);
          sortState.key = key;
          th.classList.add(sortState.dir===1 ? 'asc' : 'desc');

          currentFilteredList.sort((a, b)=>{
            const av = (type==='num') ? getNum(a[key]) : String(a[key]||'');
            const bv = (type==='num') ? getNum(b[key]) : String(b[key]||'');
            if (type==='num'){
              return sortState.dir * (Number(av) - Number(bv));
            } else {
              return sortState.dir * String(av).localeCompare(String(bv), 'id', {numeric:true});
            }
          });
          currentPage = 1;
          const nf2 = new Intl.NumberFormat('id-ID');
          const pgData = currentFilteredList.slice(0, PAGE_SIZE);
          elModTbody.innerHTML = pgData.map(d => MB_rowHtml(d, cols, nf2)).join('');
          MB_renderPagination(currentFilteredList.length, Math.ceil(currentFilteredList.length / PAGE_SIZE));
        });
      });
  }

  function MB_rowHtml(d, cols, nf) {
    let h = '<tr class="border-b border-slate-100 hover:bg-blue-50/40 transition">';
    for (const [key,label,sz,type,xtraClass] of cols){
      let v = d[key]; if (v==null) v='';
      if (key==='nama_nasabah') v = cut(v,20);
      if (key==='alamat')       v = cut(v,30);
      const raw = (type==='num') ? getNum(d[key]) : String(d[key]||'');
      let shown = (type==='num') ? nf.format(raw) : String(v);
      if (key === 'pd_actual' || key === 'lgd_actual') {
          shown = d[key] != null ? d[key] + '%' : '';
      }
      let alignClass = type === 'num' ? 'text-right' : 'text-left';
      if(key === 'kolektibilitas') alignClass = 'text-center font-bold text-slate-600';
      if(key === 'pemulihan_pembentukan') {
          alignClass += raw > 0 ? ' text-red-600 font-bold' : (raw < 0 ? ' text-emerald-600 font-bold' : '');
      }
      let customClass = xtraClass ? xtraClass : `col-${sz}`;
      h += `<td class="px-2 py-1.5 border-r border-slate-100 ${customClass} ${alignClass} nowrap" data-key="${key}" data-raw="${raw}" title="${String(d[key]??'')}">${String(shown)}</td>`;
    }
    h += '</tr>';
    return h;
  }

  function MB_renderPagination(totalItems, totalPages) {
    const elPag = document.getElementById('MB_pagination');
    if (!elPag) return;
    if (totalItems === 0 || totalPages <= 1) {
      elPag.innerHTML = totalItems > 0
        ? `<span class="text-slate-500">Menampilkan 1-${totalItems} dari ${totalItems}</span><span></span>`
        : '';
      return;
    }
    const startItem = (currentPage - 1) * PAGE_SIZE + 1;
    const endItem = Math.min(currentPage * PAGE_SIZE, totalItems);
    let btns = '';
    btns += `<button onclick="MB_goPage(${currentPage - 1})" class="px-2 py-1 rounded border ${currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 text-slate-600 hover:bg-slate-100'}" ${currentPage === 1 ? 'disabled' : ''}>&laquo;</button>`;
    const maxVisible = 7;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
    if (endPage - startPage < maxVisible - 1) startPage = Math.max(1, endPage - maxVisible + 1);
    if (startPage > 1) {
      btns += `<button onclick="MB_goPage(1)" class="px-2 py-1 rounded border border-slate-300 text-slate-600 hover:bg-slate-100">1</button>`;
      if (startPage > 2) btns += `<span class="px-1 text-slate-400">...</span>`;
    }
    for (let i = startPage; i <= endPage; i++) {
      if (i === currentPage) {
        btns += `<button class="px-2 py-1 rounded border border-blue-500 bg-blue-500 text-white font-bold">${i}</button>`;
      } else {
        btns += `<button onclick="MB_goPage(${i})" class="px-2 py-1 rounded border border-slate-300 text-slate-600 hover:bg-slate-100">${i}</button>`;
      }
    }
    if (endPage < totalPages) {
      if (endPage < totalPages - 1) btns += `<span class="px-1 text-slate-400">...</span>`;
      btns += `<button onclick="MB_goPage(${totalPages})" class="px-2 py-1 rounded border border-slate-300 text-slate-600 hover:bg-slate-100">${totalPages}</button>`;
    }
    btns += `<button onclick="MB_goPage(${currentPage + 1})" class="px-2 py-1 rounded border ${currentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 text-slate-600 hover:bg-slate-100'}" ${currentPage === totalPages ? 'disabled' : ''}>&raquo;</button>`;
    elPag.innerHTML = `<span class="text-slate-500">Menampilkan ${startItem}-${endItem} dari ${nfID.format(totalItems)}</span><div class="flex items-center gap-1">${btns}</div>`;
  }

  window.MB_goPage = function(page) {
    const totalPages = Math.ceil(currentFilteredList.length / PAGE_SIZE);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    const nf = new Intl.NumberFormat('id-ID');
    const cols = [
      ['no_rekening','Norek','md','text', 'mod-col-rek hidden md:table-cell'],
      ['nama_nasabah','Nama Nasabah','lg','text', 'mod-col-nas'],
      ['kankas','Kankas','md','text'],
      ['ao_kredit','AO Kredit','md','text'],
      ['alamat','Alamat','lgA','text'],
      ['kolektibilitas','KOL','sm','text'],
      ['os_m1','OS M-1','md','num'],
      ['ckpn_m1','CKPN M-1','md','num'],
      ['os_curr','OS Act','md','num'],
      ['ckpn_actual','CKPN Act','md','num'],
      ['pemulihan_pembentukan','+/- CKPN','md','num'],
      ['pd_actual','PD (%)','sm','num'],
      ['lgd_actual','LGD (%)','sm','num'],
      ['tunggakan_pokok','T.Pokok','md','num'],
      ['tunggakan_bunga','T.Bunga','md','num'],
      ['hari_menunggak','HM','sm','num'],
      ['hari_menunggak_pokok','HMP','sm','num'],
      ['hari_menunggak_bunga','HMB','sm','num'],
      ['tgl_jatuh_tempo','JtTmp','md','text'],
      ['tgl_tagih','TglTg','sm','num'],
      ['tgl_trans_terakhir','Tgl Trans','md','text'],
      ['angsuran_pokok','AngsP','md','num'],
      ['angsuran_bunga','AngsB','md','num']
    ];
    const startIdx = (currentPage - 1) * PAGE_SIZE;
    const pageData = currentFilteredList.slice(startIdx, startIdx + PAGE_SIZE);
    elModTbody.innerHTML = pageData.map(d => MB_rowHtml(d, cols, nf)).join('');
    MB_renderPagination(currentFilteredList.length, totalPages);
    const wrap = document.getElementById('MB_modalTableWrap');
    if (wrap) wrap.scrollTop = 0;
  };

  // --- EXPORT REKAP UTAMA MATRIX ---
  window.MB_exportRekap = function() {
      let html = `<table border="1"><thead><tr>`;
      const ths = document.querySelectorAll('#MB_headRow th');
      ths.forEach(th => {
          let txt = th.innerText.replace(/\n/g, ' ').replace(/\(×1.000\)/g, '').trim();
          html += `<th style="background:#f1f5f9">${txt}</th>`;
      });
      html += `</tr></thead><tbody>`;

      const rows = document.querySelectorAll('#MB_tbody tr');
      rows.forEach(tr => {
          html += `<tr>`;
          const tds = tr.querySelectorAll('td');
          tds.forEach(td => {
              let val = '';
              const numWrap = td.querySelector('.num-wrap');
              if(numWrap) {
                  val = numWrap.getAttribute('title').replace(/\./g, ''); 
              } else if (td.innerText.trim() === '–') {
                  val = '0';
              } else {
                  val = td.innerText.replace(/\n/g, ' ').split('•')[0].trim();
              }
              let bg = '';
              if(tr.id === 'MB_row_total') bg = 'background:#eff6ff; font-weight:bold;';
              else if(tr.id === 'MB_row_realisasi') bg = 'background:#f8fafc; font-weight:bold;';
              html += `<td style="${bg} mso-number-format:'\\@'">${val}</td>`;
          });
          html += `</tr>`;
      });
      html += `</tbody></table>`;

      const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
      a.download = `Rekap_Migrasi_Bucket_${elClosing.value}_vs_${elHarian.value}.xls`; a.click();
  };

  // --- DOWNLOAD EXCEL DETAIL MURNI TANPA BAGI 1000 ---
  window.MB_exportDetail = function() {
      if(!currentDetailData || currentDetailData.length === 0) {
          alert("Tidak ada data detail untuk di-download.");
          return;
      }
      
      let html = `<table border="1"><thead><tr>
        <th style="background:#f1f5f9">NO REKENING</th>
        <th style="background:#f1f5f9">NAMA NASABAH</th>
        <th style="background:#f1f5f9">KANKAS</th>
        <th style="background:#f1f5f9">AO KREDIT</th>
        <th style="background:#f1f5f9">ALAMAT</th>
        <th style="background:#f1f5f9">KOL</th>
        <th style="background:#fef08a">OS M-1</th>
        <th style="background:#fef08a">CKPN M-1</th>
        <th style="background:#dcfce7">OS ACTUAL</th>
        <th style="background:#dcfce7">CKPN ACTUAL</th>
        <th style="background:#ffedd5">+/- CKPN</th>
        <th style="background:#f1f5f9">PD (%)</th>
        <th style="background:#f1f5f9">LGD (%)</th>
        <th style="background:#f1f5f9">TUNGG. POKOK</th>
        <th style="background:#f1f5f9">TUNGG. BUNGA</th>
        <th style="background:#fee2e2">HM</th>
        <th style="background:#f1f5f9">HMP</th>
        <th style="background:#f1f5f9">HMB</th>
        <th style="background:#f1f5f9">JATUH TEMPO</th>
        <th style="background:#f1f5f9">TGL TAGIH</th>
        <th style="background:#f1f5f9">TGL TRANS. TERAKHIR</th>
        <th style="background:#f1f5f9">ANGS. POKOK</th>
        <th style="background:#f1f5f9">ANGS. BUNGA</th>
      </tr></thead><tbody>`;

      // Ekspor data yang difilter aja
      const searchInput = document.getElementById("MB_searchDetail").value.toLowerCase();
      const kankasInput = document.getElementById("MB_modKankas").value.toLowerCase();
      const aoInput     = document.getElementById("MB_modAo").value.toLowerCase();

      const filtered = currentDetailData.filter(d => {
        const rek  = String(d.no_rekening || '').toLowerCase();
        const nama = String(d.nama_nasabah || '').toLowerCase();
        const almt = String(d.alamat || '').toLowerCase();
        const knk  = String(d.kankas || '').toLowerCase();
        const aok  = String(d.ao_kredit || '').toLowerCase();
        
        return (searchInput === '' || rek.includes(searchInput) || nama.includes(searchInput) || almt.includes(searchInput)) &&
               (kankasInput === '' || knk === kankasInput) &&
               (aoInput === '' || aok === aoInput);
      });

      filtered.forEach(d => {
          html += `<tr>
              <td style="mso-number-format:'\\@'">${d.no_rekening||''}</td>
              <td>${d.nama_nasabah||''}</td>
              <td>${d.kankas||''}</td>
              <td>${d.ao_kredit||''}</td>
              <td>${d.alamat||''}</td>
              <td>${d.kolektibilitas||''}</td>
              <td>${getNum(d.os_m1)}</td>
              <td>${getNum(d.ckpn_m1)}</td>
              <td>${getNum(d.os_curr)}</td>
              <td>${getNum(d.ckpn_actual)}</td>
              <td>${getNum(d.pemulihan_pembentukan)}</td>
              <td>${d.pd_actual!=null ? d.pd_actual : ''}</td>
              <td>${d.lgd_actual!=null ? d.lgd_actual : ''}</td>
              <td>${getNum(d.tunggakan_pokok)}</td>
              <td>${getNum(d.tunggakan_bunga)}</td>
              <td>${getNum(d.hari_menunggak)}</td>
              <td>${getNum(d.hari_menunggak_pokok)}</td>
              <td>${getNum(d.hari_menunggak_bunga)}</td>
              <td>${d.tgl_jatuh_tempo||''}</td>
              <td>${d.tgl_tagih||''}</td>
              <td>${d.tgl_trans_terakhir||''}</td>
              <td>${getNum(d.angsuran_pokok)}</td>
              <td>${getNum(d.angsuran_bunga)}</td>
          </tr>`;
      });
      html += '</tbody></table>';

      const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
      a.download = `Detail_Migrasi_${currentFromRaw}_ke_${currentToRaw}.xls`; a.click();
  };

})();
</script>
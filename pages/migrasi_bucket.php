<div class="max-w-[100vw] lg:max-w-7xl mx-auto px-2 md:px-4 py-4" id="MB_root">
  
  <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-3 mb-4">
    <div class="flex items-center justify-between w-full lg:w-auto shrink-0">
        <div class="flex items-center gap-3">
          <h1 id="MB_title" class="font-bold flex items-center gap-2 text-slate-800 text-lg md:text-xl">
            <span class="bg-blue-600 text-white p-1.5 rounded-lg shadow-sm text-xs">📊</span>
            <span>Migrasi DPD</span>
          </h1>
          <button onclick="MB_showAnalisis()" class="flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300 rounded-md text-[10px] font-bold shadow-sm transition">
              <span class="text-xs">ℹ️</span> Analisis
          </button>
        </div>
        
        <button type="button" onclick="document.getElementById('MB_formFilter').classList.toggle('hidden'); document.getElementById('MB_formFilter').classList.toggle('flex');" class="lg:hidden h-[28px] px-2.5 bg-white border border-slate-200 text-slate-700 rounded-md flex items-center gap-1 shadow-sm font-bold text-[10px] ml-2 shrink-0">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Filter
        </button>
    </div>

    <form id="MB_formFilter" class="hidden lg:flex bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm flex-wrap items-center lg:items-end gap-2 shrink-0 w-full lg:w-auto transition-all">
      <div class="flex flex-col justify-center px-1 mr-1">
          <label class="relative inline-flex items-center cursor-pointer mb-1" title="Aktifkan mode Proyeksi">
            <input type="checkbox" id="MB_isProyeksi" onchange="MB_toggleProyeksi()" class="sr-only peer">
            <div class="w-8 h-4 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-indigo-600"></div>
            <span class="ml-1.5 text-[9px] font-bold text-indigo-700 uppercase tracking-wider">Proyeksi</span>
          </label>
      </div>

      <div class="flex flex-col gap-0.5 flex-1 lg:flex-none lg:w-[110px]">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1" for="MB_closing">Closing</label>
        <input type="date" id="MB_closing" onchange="MB_checkDate()" class="w-full border border-slate-300 rounded-md px-2 text-[11px] font-medium h-7 shadow-sm focus:border-blue-500 outline-none cursor-pointer">
      </div>
      <div class="flex flex-col gap-0.5 flex-1 lg:flex-none lg:w-[110px]">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1" for="MB_harian">Act/Proj</label>
        <input type="date" id="MB_harian" onchange="MB_checkDate()" class="w-full border border-slate-300 rounded-md px-2 text-[11px] font-medium h-7 shadow-sm focus:border-blue-500 outline-none cursor-pointer">
      </div>
      <div class="flex flex-col gap-0.5 w-full lg:w-auto lg:min-w-[150px]">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1" for="MB_optKantor">Cabang</label>
        <select id="MB_optKantor" onchange="MB_checkDate()" class="w-full border border-slate-300 rounded-md px-2 text-[11px] font-medium h-7 shadow-sm focus:border-blue-500 outline-none cursor-pointer">
          <option value="">Konsolidasi (Semua)</option>
        </select>
      </div>

      <div class="flex gap-1 mt-1 lg:mt-auto w-full lg:w-auto justify-end">
          <button type="button" onclick="MB_exportRekap()" class="h-7 w-8 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md shadow-sm transition flex items-center justify-center shrink-0" title="Export Excel Rekap">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          </button>
      </div>
    </form>
  </div>

  <div class="flex bg-slate-200/60 p-1 rounded-md w-max mb-3 shadow-inner border border-slate-200">
      <button onclick="MB_switchTab('rekap')" id="btnTabRekap" class="px-4 py-1 text-[11px] md:text-xs font-bold rounded bg-white text-blue-600 shadow-sm transition">Rekap Summary</button>
      <button onclick="MB_switchTab('migrasi')" id="btnTabMigrasi" class="px-4 py-1 text-[11px] md:text-xs font-bold rounded text-slate-500 hover:text-slate-800 transition">Matriks Migrasi</button>
  </div>

  <div id="MB_loading" class="hidden flex items-center gap-2 text-sm text-blue-600 font-bold mb-3 tracking-wider">
    <svg class="animate-spin h-4 w-4 text-blue-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="CurrentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
    <span>MEMUAT DATA...</span>
  </div>

  <div id="view_rekap" class="block animate-scale-up">
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
          
          <div class="bg-white border border-slate-200 rounded-lg p-3 shadow-sm flex flex-col justify-between">
              <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2 border-b pb-1">📊 NPL Closing (M-1)</div>
              <div class="flex justify-between items-end">
                  <div>
                      <div class="text-[10px] text-slate-400 font-medium">Total OS M-1</div>
                      <div id="stat_os_m1" class="text-sm font-bold text-slate-700">0</div>
                  </div>
                  <div class="text-right">
                      <div class="text-[10px] text-red-400 font-medium">NPL OS M-1</div>
                      <div class="flex items-baseline gap-1 justify-end">
                          <div id="stat_npl_m1" class="text-sm font-bold text-red-600">0</div>
                          <div id="stat_pct_m1" class="text-[10px] font-bold text-red-600 bg-red-50 px-1 rounded">0%</div>
                      </div>
                  </div>
              </div>
          </div>

          <div class="bg-blue-50/40 border border-blue-200 rounded-lg p-3 shadow-sm flex flex-col justify-between">
              <div class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-2 border-b border-blue-100 pb-1">📈 NPL Actual/Proyeksi</div>
              <div class="flex justify-between items-end">
                  <div>
                      <div class="text-[10px] text-slate-500 font-medium">Total OS Act</div>
                      <div id="stat_os_act" class="text-sm font-bold text-slate-800">0</div>
                  </div>
                  <div class="text-right">
                      <div class="text-[10px] text-red-500 font-medium">NPL OS Act</div>
                      <div class="flex items-baseline gap-1 justify-end">
                          <div id="stat_npl_act" class="text-sm font-bold text-red-600">0</div>
                          <div id="stat_pct_act" class="text-[10px] font-bold text-white bg-red-500 px-1 rounded shadow-sm">0%</div>
                      </div>
                      <div id="stat_delta_npl" class="text-[9px] font-bold mt-0.5"></div>
                  </div>
              </div>
          </div>

          <div class="bg-indigo-50/40 border border-indigo-200 rounded-lg p-3 shadow-sm flex flex-col justify-between">
              <div class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider mb-2 border-b border-indigo-100 pb-1">🚀 Portofolio Flow</div>
              <div class="flex justify-between items-end">
                  <div>
                      <div class="text-[10px] text-emerald-600 font-medium">Realisasi (Baru)</div>
                      <div id="stat_real_os" class="text-sm font-bold text-emerald-700">0</div>
                  </div>
                  <div class="text-right">
                      <div class="text-[10px] text-slate-500 font-medium">Run-Off / Lunas</div>
                      <div id="stat_runoff_os" class="text-sm font-bold text-slate-600">0</div>
                      <div id="stat_net_growth" class="text-[9px] font-bold mt-0.5"></div>
                  </div>
              </div>
          </div>

      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div class="bg-emerald-50/30 border border-emerald-200 rounded-lg flex flex-col h-[350px]">
              <div class="p-2 border-b border-emerald-200 bg-emerald-100/50 flex justify-between items-center shrink-0">
                  <span class="text-[11px] font-bold text-emerald-800 uppercase">👍 Perbaikan</span>
                  <span id="tot_baik" class="text-[10px] font-bold text-emerald-700 bg-emerald-200/50 px-1.5 rounded">0 Akun</span>
              </div>
              <div id="list_perbaikan" class="flex-1 overflow-y-auto p-1.5 space-y-1 custom-scrollbar"></div>
          </div>

          <div class="bg-slate-50 border border-slate-200 rounded-lg flex flex-col h-[350px]">
              <div class="p-2 border-b border-slate-200 bg-slate-100 flex justify-between items-center shrink-0">
                  <span class="text-[11px] font-bold text-slate-700 uppercase">➖ Stay (Tetap)</span>
                  <span id="tot_stay" class="text-[10px] font-bold text-slate-600 bg-slate-200 px-1.5 rounded">0 Akun</span>
              </div>
              <div id="list_stay" class="flex-1 overflow-y-auto p-1.5 space-y-1 custom-scrollbar"></div>
          </div>

          <div class="bg-red-50/30 border border-red-200 rounded-lg flex flex-col h-[350px]">
              <div class="p-2 border-b border-red-200 bg-red-100/50 flex justify-between items-center shrink-0">
                  <span class="text-[11px] font-bold text-red-800 uppercase">👎 Pemburukan</span>
                  <span id="tot_buruk" class="text-[10px] font-bold text-red-700 bg-red-200/50 px-1.5 rounded">0 Akun</span>
              </div>
              <div id="list_pemburukan" class="flex-1 overflow-y-auto p-1.5 space-y-1 custom-scrollbar"></div>
          </div>
      </div>

  </div>

  <div id="view_migrasi" class="hidden animate-scale-up">
      <div id="MB_tblWrap" class="overflow-auto border border-slate-200 rounded-lg shadow-sm relative bg-white" style="max-height:65vh;">
        <table id="MB_table" class="min-w-full text-center table-fixed text-xs">
          <thead id="MB_thead" class="bg-slate-50 text-slate-600 font-semibold uppercase text-[10px] tracking-wider"></thead>
          <tbody id="MB_tbody" class="text-slate-700"></tbody>
        </table>
      </div>
  </div>

</div>

<div id="MB_modalAnalisis" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm z-[99999] flex items-center justify-center p-4">
  <div class="bg-amber-50 border border-amber-200 rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-scale-up">
    <div class="px-4 py-3 border-b border-amber-200 flex justify-between items-center bg-amber-100/50">
      <h3 class="font-bold text-amber-800 text-sm flex items-center gap-2">💡 Analisis Eksekutif & Rekomendasi</h3>
      <button onclick="document.getElementById('MB_modalAnalisis').classList.add('hidden')" class="text-amber-800 hover:text-red-600 font-bold text-lg leading-none">&times;</button>
    </div>
    <div class="p-4 text-xs text-amber-900 space-y-3 leading-relaxed" id="MB_narrative_box">
        </div>
  </div>
</div>

<div id="MB_modal" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm z-[99999] flex items-center justify-center p-0 lg:px-4">
  <div id="MB_modalCard" class="bg-white max-w-[min(1500px,100vw)] w-[100vw] lg:w-[96vw] h-[100vh] lg:h-[90vh] flex flex-col rounded-none lg:rounded-2xl shadow-2xl overflow-hidden animate-scale-up">
    <div class="px-4 py-2.5 lg:py-3 border-b border-slate-100 bg-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-2 shrink-0">
      <div>
        <h3 id="MB_modalTitle" class="font-bold text-slate-800 text-sm flex items-center gap-2">Detail Debitur</h3>
        <p id="MB_modalSubtitle" class="text-[9px] lg:text-[10px] text-slate-500 font-mono mt-0.5"></p>
      </div>
      <div class="flex items-center gap-1.5 w-full md:w-auto overflow-x-auto no-scrollbar pb-1 md:pb-0">
        <div class="relative flex-1 min-w-[120px] md:w-[150px] shrink-0">
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none"><span class="text-xs text-slate-400">🔍</span></div>
            <input type="text" id="MB_searchDetail" oninput="MB_filterDetail()" class="w-full pl-6 pr-2 py-1 h-7 bg-white border border-slate-200 rounded text-[10px] outline-none focus:border-blue-500" placeholder="Cari Norek/Nama...">
        </div>
        <select id="MB_modKankas" onchange="MB_filterDetail()" class="w-20 md:w-28 px-1 h-7 bg-white border border-slate-200 rounded text-[10px] outline-none focus:border-blue-500 text-slate-600 shrink-0"><option value="">Kankas (All)</option></select>
        <select id="MB_modAo" onchange="MB_filterDetail()" class="w-20 md:w-28 px-1 h-7 bg-white border border-slate-200 rounded text-[10px] outline-none focus:border-blue-500 text-slate-600 shrink-0"><option value="">AO (All)</option></select>
        <button onclick="MB_exportDetail()" class="flex items-center justify-center w-7 h-7 bg-emerald-600 hover:bg-emerald-700 text-white rounded shadow-sm transition shrink-0" title="Download Excel">📥</button>
        <button id="MB_modalClose" class="w-7 h-7 flex items-center justify-center rounded bg-red-50 hover:bg-red-500 hover:text-white text-red-500 transition font-bold text-base leading-none shrink-0">&times;</button>
      </div>
    </div>
    <div id="MB_modalTotals" class="px-3 py-2 bg-white border-b border-slate-100 text-[10px] flex flex-row overflow-x-auto gap-2 shrink-0 no-scrollbar"></div>
    <div id="MB_modalTableWrap" class="flex-1 overflow-auto bg-slate-50 relative custom-scrollbar">
      <table id="MB_modalTable" class="w-max min-w-full text-[10px] lg:text-xs text-left bg-white shadow-sm table-fixed">
        <thead id="MB_modalThead" class="bg-slate-100 text-slate-600 uppercase text-[9px] tracking-wider"></thead>
        <tbody id="MB_modalTbody" class="text-slate-700"></tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  @keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* Table Matriks Utility */
  :root { --colFrom:8.0rem; --col2:6.4rem; --colN:5.0rem; }
  #MB_table { table-layout: fixed; border-collapse: separate; border-spacing: 0; min-width: 100%; width: max-content; }
  .col-from { width: var(--colFrom); min-width: var(--colFrom); max-width: var(--colFrom); }
  .col-6 { width: var(--col2); min-width: var(--col2); max-width: var(--col2); }
  .col-N { width: var(--colN); min-width: var(--colN); max-width: var(--colN); }

  #MB_table thead th { position: sticky; top: 0; z-index: 20; background: #f8fafc; border-bottom: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0; padding: 8px 4px; }
  .sticky-col-1 { position: sticky; left: 0; z-index: 10; background: #fff; border-right: 1px solid #cbd5e1; box-shadow: inset -1px 0 0 #cbd5e1; }
  #MB_table thead th.sticky-col-1 { z-index: 30; background: #f8fafc; }
  
  .sticky-col-2 { background: #fff; border-right: 2px solid #cbd5e1; }
  #MB_table thead th.sticky-col-2 { background: #f8fafc; }

  #MB_row_total td { position: sticky; top: 37px; z-index: 25; background: #eff6ff !important; border-bottom: 2px solid #bfdbfe; box-shadow: 0 2px 4px -2px rgba(0,0,0,0.1); }
  #MB_row_total td.sticky-col-1 { z-index: 35 !important; background: #eff6ff !important; }
  #MB_row_total td.sticky-col-2 { background: #eff6ff !important; border-right: 2px solid #bfdbfe; }

  @media (min-width: 768px) {
      .sticky-col-2 { position: sticky !important; left: var(--colFrom) !important; z-index: 10; box-shadow: inset -2px 0 0 #cbd5e1; }
      #MB_table thead th.sticky-col-2 { z-index: 30 !important; }
      #MB_row_total td.sticky-col-2 { z-index: 35 !important; }
  }

  #MB_tbody tr { background: #fff; }
  #MB_tbody tr.bg-indigo-50 { background: #f8fafc !important; }
  #MB_tbody tr:hover td { background: #f1f5f9; }
  #MB_tbody td { padding: 6px 4px; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; }

  .num-wrap { display: flex; justify-content: flex-end; }
  .num { font-variant-numeric: tabular-nums; font-size: 11px; }
  .cell-sub { display: block; font-size: 8px; color: #64748b; margin-top: 1px; }
  .cell-sub:empty { display: none; }
  .cell-link { color: inherit; font-weight: 700; cursor: pointer; transition: 0.2s; }
  .cell-link:hover { color: #2563eb; text-decoration: underline; }
  .flow-worse { background: #fef2f2; color: #b91c1c; }
  .flow-better { background: #f0fdf4; color: #15803d; }

  /* Modal Details */
  #MB_modalTable { border-collapse: separate; border-spacing: 0; }
  #MB_modalTable th, #MB_modalTable td { background-clip: padding-box; background-color: #fff; }
  #MB_modalThead tr:first-child th { position: sticky !important; top: 0 !important; z-index: 40 !important; background: #f1f5f9 !important; box-shadow: inset 0 -1px 0 #cbd5e1; padding: 6px; }
  #MB_modalTbody td { position: relative; z-index: 10; padding: 6px 8px; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
  #MB_modalThead tr.sticky-total td { position: sticky !important; top: 25px !important; z-index: 35 !important; background: #eff6ff !important; box-shadow: inset 0 -1px 0 #bfdbfe, inset 0 1px 0 #bfdbfe; font-weight: 700; color: #1e40af; padding: 4px 6px; }

  #MB_modalTable{ --w-sm: 5.5rem; --w-md: 8.0rem; --w-lg: 12.0rem; --w-lgA: 16.0rem; --shrink: 1.0rem; }
  .col-sm{ min-width:calc(var(--w-sm) - var(--shrink)); max-width:calc(var(--w-sm) - var(--shrink)); }
  .col-md{ min-width:calc(var(--w-md) - var(--shrink)); max-width:calc(var(--w-md) - var(--shrink)); }
  .col-lg{ min-width:calc(var(--w-lg) - var(--shrink)); max-width:calc(var(--w-lg) - var(--shrink)); }
  .col-lgA{ min-width:var(--w-lgA); max-width:var(--w-lgA); }
  #MB_modalTable th, #MB_modalTable td{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

  .mod-col-rek { position: sticky !important; left: 0 !important; z-index: 30 !important; background-color: #fff !important; min-width: 90px; max-width: 90px; box-shadow: inset -1px 0 0 #e2e8f0; }
  .mod-col-nas { position: sticky !important; left: 0 !important; z-index: 30 !important; background-color: #fff !important; min-width: 150px; max-width: 150px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); }
  
  @media (min-width: 768px) {
      .mod-col-rek { min-width: 100px; max-width: 100px; }
      .mod-col-nas { left: 100px !important; min-width: 200px; max-width: 200px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); }
  }

  #MB_modalThead tr:first-child th.mod-col-rek, #MB_modalThead tr:first-child th.mod-col-nas { z-index: 60 !important; background-color: #e2e8f0 !important; }
  #MB_modalThead tr.sticky-total td.mod-col-rek, #MB_modalThead tr.sticky-total td.mod-col-nas { z-index: 50 !important; background-color: #eff6ff !important; box-shadow: inset -1px 0 0 #bfdbfe, inset 0 1px 0 #bfdbfe, inset 0 -1px 0 #bfdbfe; }
  #MB_modalTbody tr:hover td { background-color: #f8fafc !important; }
  #MB_modalTbody tr:hover td.mod-col-rek, #MB_modalTbody tr:hover td.mod-col-nas { filter: brightness(0.98); }

  .th-sort { cursor: pointer; transition: 0.2s; }
  .th-sort:hover { background: #e2e8f0 !important; }
  .th-sort:after { content: " ⬍"; font-size: 8px; color: #9ca3af; margin-left: 2px; }
  .th-sort.asc:after { content: " ▲"; color: #2563eb; }
  .th-sort.desc:after { content: " ▼"; color: #2563eb; }
</style>

<script>
(() => {
  const getNum = v => isNaN(Number(v)) ? 0 : Number(v);
  const nfID = new Intl.NumberFormat('id-ID');
  const fmtFull = n => nfID.format(Math.round(getNum(n)));
  const pick = (o, keys, d=0) => { for(const k of keys){ if(o && o[k]!=null) return o[k]; } return d; };
  const $ = s => document.querySelector(s);
  const digitLen = s => String(s).replace(/[^\d]/g,'').length;
  const cut = (s,n) => { s=String(s||''); return s.length<=n ? s : (s.slice(0,n).trimEnd()+'…'); };

  function numHTML(val){
    const full = nfID.format(getNum(val));
    const short = nfID.format(Math.round(getNum(val) / 1000));
    const d = Math.max(digitLen(short), 1);
    return `<span class="num-wrap" title="${full}"><span class="num" style="--d:${d}">${short}</span></span>`;
  }
  const dashHTML = v => getNum(v)>0 ? numHTML(v) : '<span class="text-slate-300">–</span>';

  const DPD_LABEL = {
    A:'A_DPD 0', B:'B_DPD 1-30', C:'C_DPD 31-60', D:'D_DPD 61-90',
    E:'E_DPD 91-120', F:'F_DPD 121-150', G:'G_DPD 151-180', H:'H_DPD 181-210',
    I:'I_DPD 211-240', J:'J_DPD 241-270', K:'K_DPD 271-300', L:'L_DPD 301-330',
    M:'M_DPD 331-360', N:'N_DPD >360', O:'O_LUNAS', REALISASI: 'Realisasi Baru'
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

  const elMod = $('#MB_modal');
  const elModTitle = $('#MB_modalTitle');
  const elModSub = $('#MB_modalSubtitle');
  const elModTotals = $('#MB_modalTotals');
  const elModThead = $('#MB_modalThead');
  const elModTbody = $('#MB_modalTbody');

  let currentDetailData = []; 
  let currentFromRaw = '';
  let currentToRaw = '';

  document.getElementById('MB_modalClose').onclick = () => elMod.classList.add('hidden');
  elMod.addEventListener('click', e => { if(!e.target.closest('#MB_modalCard')) elMod.classList.add('hidden'); });
  window.addEventListener('keydown', e => { if(e.key==='Escape') elMod.classList.add('hidden'); });

  let ABORT, ABORT_DETAIL;
  let gIsKonsol = true;

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
    MB_autoFetch();
  })();

  // TAB SWITCHER
  window.MB_switchTab = function(tab) {
      const vRekap = document.getElementById('view_rekap');
      const vMigrasi = document.getElementById('view_migrasi');
      const bRekap = document.getElementById('btnTabRekap');
      const bMigrasi = document.getElementById('btnTabMigrasi');

      if (tab === 'rekap') {
          vRekap.classList.remove('hidden'); vMigrasi.classList.add('hidden');
          bRekap.className = "px-4 py-1 text-[11px] md:text-xs font-bold rounded bg-white text-blue-600 shadow-sm transition";
          bMigrasi.className = "px-4 py-1 text-[11px] md:text-xs font-bold rounded text-slate-500 hover:text-slate-800 transition";
      } else {
          vRekap.classList.add('hidden'); vMigrasi.classList.remove('hidden');
          bMigrasi.className = "px-4 py-1 text-[11px] md:text-xs font-bold rounded bg-white text-blue-600 shadow-sm transition";
          bRekap.className = "px-4 py-1 text-[11px] md:text-xs font-bold rounded text-slate-500 hover:text-slate-800 transition";
      }
  };

  window.MB_toggleProyeksi = function() {
      const isProj = document.getElementById('MB_isProyeksi').checked;
      const elH = document.getElementById('MB_harian');
      if (isProj && elH.value) {
          const d = new Date(elH.value);
          const eom = new Date(d.getFullYear(), d.getMonth() + 1, 0); 
          elH.value = `${eom.getFullYear()}-${String(eom.getMonth()+1).padStart(2,'0')}-${String(eom.getDate()).padStart(2,'0')}`;
      } else if (!isProj) {
          const today = new Date();
          elH.value = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
      }
      MB_autoFetch();
  };

  window.MB_checkDate = function() {
      const isProj = document.getElementById('MB_isProyeksi').checked;
      const elH = document.getElementById('MB_harian');
      if (isProj && elH.value) {
          const d = new Date(elH.value);
          const eom = new Date(d.getFullYear(), d.getMonth() + 1, 0);
          const eomStr = `${eom.getFullYear()}-${String(eom.getMonth()+1).padStart(2,'0')}-${String(eom.getDate()).padStart(2,'0')}`;
          if (elH.value !== eomStr) elH.value = eomStr;
      }
      MB_autoFetch();
  };

  window.MB_autoFetch = function() {
      if(elClosing.value && elHarian.value) {
          fetchBucket(elClosing.value, elHarian.value, elKantor.disabled ? elKantor.value : (elKantor.value || null));
      }
  };

  window.MB_showAnalisis = function() {
      document.getElementById('MB_modalAnalisis').classList.remove('hidden');
  };

  async function getLastDates(){ try{ const r=await fetch('./api/date/'); const j=await r.json(); return j.data||null; }catch{ return null; } }
  
  async function populateKantor(){
    try{
      const r = await fetch('./api/kode/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'})});
      const j = await r.json();
      const list = Array.isArray(j.data)?j.data:[];
      let html = `<option value="">Konsolidasi (Semua)</option>`;
      list.filter(x=>x.kode_kantor && x.kode_kantor!=='000')
          .sort((a,b)=> String(a.kode_kantor).localeCompare(String(b.kode_kantor)))
          .forEach(it=>{
            const code=String(it.kode_kantor).padStart(3,'0');
            html += `<option value="${code}">${code} — ${it.nama_kantor||it.nama_cabang||''}</option>`;
          });
      elKantor.innerHTML = html;
    }catch{
      elKantor.innerHTML = `<option value="">Konsolidasi (Semua)</option>`;
    }
  }

  // FETCH UTAMA
  async function fetchBucket(closing_date, harian_date, kode_kantor){
    if(ABORT) ABORT.abort();
    ABORT = new AbortController();
    gIsKonsol = !kode_kantor;
    elLoad.classList.remove('hidden'); 
    elHead.innerHTML=''; elBody.innerHTML = `<tr><td class="py-10 text-center text-slate-400 font-medium">Sedang memproses data...</td></tr>`;

    try{
      const payload = { 
          type:'migrasi bucket', 
          closing_date, 
          harian_date,
          is_proyeksi: document.getElementById('MB_isProyeksi').checked 
      };
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

  function renderListCol(listArr, containerId, isClickable = false) {
      const cont = document.getElementById(containerId);
      if(!listArr || !listArr.length) {
          cont.innerHTML = `<div class="text-center text-[10px] text-slate-400 py-4">Tidak ada data.</div>`;
          return;
      }
      
      let html = '';
      listArr.forEach(item => {
          const f = shortDPD(item.from_bucket);
          const t = item.to_bucket === 'O' ? 'LUNAS' : shortDPD(item.to_bucket);
          
          let hoverClass = isClickable ? 'hover:bg-red-100/50 cursor-pointer transition' : 'hover:bg-slate-100 transition';
          let clickAttr = isClickable ? `onclick="MB_openDetail('${item.from_bucket}','${item.to_bucket}')"` : '';
          
          html += `
              <div ${clickAttr} class="bg-white border border-slate-200 rounded p-1.5 flex justify-between items-center shadow-sm ${hoverClass}">
                  <div class="flex flex-col">
                      <span class="text-[9px] font-bold text-slate-700">${f} <span class="text-slate-400 mx-0.5">➔</span> ${t}</span>
                      <span class="text-[8px] text-slate-500">${nfID.format(item.noa)} Akun</span>
                  </div>
                  <span class="text-[10px] font-bold text-slate-800">${fmtFull(item.os)}</span>
              </div>
          `;
      });
      cont.innerHTML = html;
  }

  function renderBucket(data){
    const orderTo = (Array.isArray(data.order_to) && data.order_to.length) ? data.order_to : ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O'];
    const matrixArr = Array.isArray(data.matrix) ? data.matrix : [];
    const colTotalsData = data.column_totals || {};
    const fromTotals = data.from_totals || {};
    const flow = data.portfolio_flow || { realisasi: {noa:0,os:0}, run_off: 0, net_growth: 0 };
    const m = data.movement_summary || { perbaikan:{os:0,noa:0}, pemburukan:{os:0,noa:0}, stay:{os:0,noa:0} };
    const details = data.movement_details || { pemburukan_list:[], perbaikan_list:[], stay_list:[] };

    // UPDATE CARDS REKAP SUMMARY
    if(data.npl_comparison) {
        const c = data.npl_comparison.closing;
        const a = data.npl_comparison.actual;
        const d = data.npl_comparison.delta;

        $('#stat_os_m1').textContent = fmtFull(c.total_os);
        $('#stat_npl_m1').textContent = fmtFull(c.npl_os);
        $('#stat_pct_m1').textContent = c.npl_pct + '%';
        
        $('#stat_os_act').textContent = fmtFull(a.total_os);
        $('#stat_npl_act').textContent = fmtFull(a.npl_os);
        $('#stat_pct_act').textContent = a.npl_pct + '%';

        let delNplHtml = '';
        if(d.npl_growth > 0) {
            delNplHtml = `<span class="text-red-500">▲ +Rp ${fmtFull(d.npl_growth)} (+${d.pct_growth}%)</span>`;
        } else if (d.npl_growth < 0) {
            delNplHtml = `<span class="text-emerald-500">▼ -Rp ${fmtFull(Math.abs(d.npl_growth))} (${d.pct_growth}%)</span>`;
        } else {
            delNplHtml = `<span class="text-slate-400">Tetap (0%)</span>`;
        }
        $('#stat_delta_npl').innerHTML = delNplHtml;
    }

    $('#stat_real_os').textContent = fmtFull(flow.realisasi.os);
    $('#stat_runoff_os').textContent = fmtFull(flow.run_off);
    
    let netGrHtml = '';
    if(flow.net_growth > 0) netGrHtml = `<span class="text-emerald-500">▲ Growth: +Rp ${fmtFull(flow.net_growth)}</span>`;
    else if(flow.net_growth < 0) netGrHtml = `<span class="text-red-500">▼ Susut: -Rp ${fmtFull(Math.abs(flow.net_growth))}</span>`;
    else netGrHtml = `<span class="text-slate-400">Stagnan</span>`;
    $('#stat_net_growth').innerHTML = netGrHtml;

    // UPDATE LIST BREAKDOWN
    document.getElementById('tot_baik').textContent = `${nfID.format(m.perbaikan.noa)} Akun`;
    document.getElementById('tot_stay').textContent = `${nfID.format(m.stay.noa)} Akun`;
    document.getElementById('tot_buruk').textContent = `${nfID.format(m.pemburukan.noa)} Akun`;
    
    renderListCol(details.perbaikan_list, 'list_perbaikan', false);
    renderListCol(details.stay_list, 'list_stay', false);
    renderListCol(details.pemburukan_list, 'list_pemburukan', true); // Pemburukan bisa diklik

    // GENERATE NARRATIVE (Analisis Modal)
    let narGrowth = '';
    if(flow.net_growth < 0) {
        narGrowth = `<span class="text-red-600 font-bold">⚠️ Pertumbuhan Negatif:</span> Harus segera mendorong <i>growth</i>! Realisasi baru (<b>Rp ${fmtFull(flow.realisasi.os)}</b>) tertinggal dari Run Off / Pelunasan (<b>Rp ${fmtFull(flow.run_off)}</b>). Portofolio menyusut dan berdampak pada pendapatan.`;
    } else {
        narGrowth = `<span class="text-emerald-600 font-bold">✅ Pertumbuhan Positif:</span> Ekspansi berjalan baik. Realisasi baru (<b>Rp ${fmtFull(flow.realisasi.os)}</b>) berhasil melampaui Run Off.`;
    }

    let d_to_e_os = 0;
    details.pemburukan_list.forEach(i => { if(i.from_bucket === 'D' && i.to_bucket === 'E') d_to_e_os += i.os; });

    let narCkpn = '';
    if (d_to_e_os > 0) {
       narCkpn += `<span class="text-red-600 font-bold">🚨 Alert NPL Baru:</span> Segera tangani debitur yang memburuk dari <b>61-90 hari (D) ke 91-120 hari (E) senilai Rp ${fmtFull(d_to_e_os)}</b> agar tidak membentuk CKPN signifikan.<br><br>`;
    }

    if (m.pemburukan.os > m.perbaikan.os) {
       narCkpn += `Kualitas aset memburuk! Aliran pemburukan (<b>Rp ${fmtFull(m.pemburukan.os)}</b>) lebih besar dari <i>recovery</i>/perbaikan (<b>Rp ${fmtFull(m.perbaikan.os)}</b>). <i>Flow</i> ke NPL tidak boleh lebih besar dari <i>recovery</i>. Fokus penagihan ekstra di area <b>FE (31-180 hari)</b> sangat diperlukan!`;
    } else {
       narCkpn += `Kualitas aset terkendali. Tingkat <i>recovery</i>/perbaikan (<b>Rp ${fmtFull(m.perbaikan.os)}</b>) lebih besar dari pemburukan (<b>Rp ${fmtFull(m.pemburukan.os)}</b>). Pertahankan intensitas <i>collection</i> di area <b>SC (0-30)</b> dan <b>FE (31-180)</b>.`;
    }

    $('#MB_narrative_box').innerHTML = `
        <div class="border-b border-amber-200/50 pb-2"><p>${narGrowth}</p></div>
        <div class="pt-1"><p>${narCkpn}</p></div>
    `;

    // MATRIKS RENDER
    const mtx = {};
    for(const it of matrixArr){
      const f = String(pick(it,['from_bucket','from','dpd_from','bucket_m1'],'')).toUpperCase();
      const t = String(pick(it,['to_bucket','to','dpd_to','bucket_curr'],'')).toUpperCase();
      if(!f || !t || f === 'REALISASI') continue; 
      if(!mtx[f]) mtx[f] = {};
      mtx[f][t] = { os: getNum(pick(it,['os','os_curr','saldo','amount'],0)), noa: getNum(pick(it,['noa','count','jumlah'],0)), pct: pick(it,['actual_pct','pct'],null) };
    }

    let head = `<tr id="MB_headRow">
      <th class="sticky-col-1 col-from font-bold tracking-wide">DPD M-1</th>
      <th class="sticky-col-2 col-6">OS M-1 <span class="block text-slate-400 font-normal mt-0.5">(×1.000)</span></th>`;
    for(const t of orderTo) head += `<th class="col-N">→ ${t} <span class="block text-slate-400 font-normal mt-0.5">${shortDPD(t)}</span></th>`;
    head += `<th class="col-N bg-slate-100">Run Off <span class="block text-slate-400 font-normal mt-0.5">(Lunas/Angs)</span></th></tr>`;
    elHead.innerHTML = head;

    const fromOrder = orderTo.filter(x=>x!=='O');
    const rowsHtml = [];
    let grand_m1_os = 0;

    for(const f of fromOrder){
      const ft = fromTotals[f] || {};
      const os_m1 = getNum(pick(ft,['os_m1','saldo_m1'],0));
      grand_m1_os += os_m1;
      let sumNonO = 0;
      const cellsHtml = [];
      
      for(const t of orderTo){
        const c = (mtx[f] && mtx[f][t]) || {os:0,noa:0,pct:null};
        if(t !== 'O') sumNonO += getNum(c.os);

        let flowCls = '';
        const fi = idxBucket(f), ti = idxBucket(t);
        if(t==='O') flowCls = 'flow-better';
        else if(fi>=0 && ti>=0){ flowCls = (ti>fi) ? 'flow-worse' : (ti<fi ? 'flow-better' : ''); }

        const sub = [];
        if(getNum(c.noa)>0) sub.push(nfID.format(c.noa)+' NOA');
        if(c.pct!=null && !isNaN(c.pct) && getNum(c.pct)!==0) sub.push(Number(c.pct).toFixed(2)+'%');

        cellsHtml.push(`<td class="text-right col-N ${flowCls}">${linkCell(f,t,c.os)}<span class="cell-sub">${sub.join(' • ')}</span></td>`);
      }
      
      const runoff = Math.max(0, os_m1 - sumNonO);
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
    for(const t of orderTo) {
      const colOs = colTotalsData[t] ? getNum(colTotalsData[t].os) : 0;
      totalRow += `<td class="text-right col-N font-bold">${dashHTML(colOs)}</td>`;
    }
    totalRow += `<td class="text-right col-N font-bold">${dashHTML(flow.run_off)}</td></tr>`;

    let realRow = `<tr id="MB_row_realisasi" class="bg-indigo-50 text-slate-700">
      <td class="text-left sticky-col-1 col-from font-bold">Realisasi (Baru)</td>
      <td class="text-center sticky-col-2 col-6 font-bold">–</td>`;
    for (const t of orderTo){
      if(t === 'A') {
          realRow += `<td class="text-right col-N bg-indigo-100/50">${linkCell('REALISASI', t, flow.realisasi.os)}<span class="cell-sub">${nfID.format(flow.realisasi.noa)} NOA</span></td>`;
      } else {
          realRow += `<td class="text-center col-N text-slate-300">–</td>`;
      }
    }
    realRow += `<td class="text-center col-N bg-indigo-100/50">–</td></tr>`;

    elBody.innerHTML = totalRow + realRow + rowsHtml.join('');
  }

  function linkCell(from_bucket, to_bucket, val){
    const n = getNum(val);
    if(n<=0) return '<span class="text-slate-300">–</span>';
    if(to_bucket === 'RUNOFF' || gIsKonsol || from_bucket === 'REALISASI') return numHTML(n);
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
        
        const matchSearch = searchInput === '' || rek.includes(searchInput) || nama.includes(searchInput) || almt.includes(searchInput);
        const matchKankas = kankasInput === '' || knk === kankasInput;
        const matchAo     = aoInput === '' || aok === aoInput;

        return matchSearch && matchKankas && matchAo;
      });
      
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
    
    const searchInput = document.getElementById('MB_searchDetail');
    if(searchInput) searchInput.value = '';

    elMod.classList.remove('hidden');

    elModTotals.innerHTML = ''; elModThead.innerHTML = '';
    elModTbody.innerHTML = `<tr><td class="p-12 text-center text-slate-400 font-bold uppercase tracking-widest"><div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mx-auto mb-3"></div>Menarik Data Nasabah...</td></tr>`;

    try{
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

      const uniqueKankas = [...new Set(currentDetailData.map(d => d.kankas).filter(Boolean))].sort();
      const uniqueAo = [...new Set(currentDetailData.map(d => d.ao_kredit).filter(Boolean))].sort();

      const modKankas = document.getElementById('MB_modKankas');
      if (modKankas) {
          modKankas.innerHTML = '<option value="">Kankas (All)</option>' + 
              uniqueKankas.map(k => `<option value="${k}">${k}</option>`).join('');
          modKankas.value = '';
      }

      const modAo = document.getElementById('MB_modAo');
      if (modAo) {
          modAo.innerHTML = '<option value="">AO (All)</option>' + 
              uniqueAo.map(a => `<option value="${a}">${a}</option>`).join('');
          modAo.value = '';
      }

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

      elModTotals.innerHTML = `
        <div class="px-2 py-1 bg-blue-50 text-blue-900 border border-blue-100 rounded min-w-[70px]"><span class="block text-[8px] uppercase font-bold text-blue-600">Total NOA</span><b class="text-[10px]">${nf.format(total.noa)}</b></div>
        <div class="px-2 py-1 bg-white border border-slate-200 rounded min-w-[100px] shadow-sm"><span class="block text-[8px] uppercase font-bold text-slate-500">Total OS M-1</span><b class="text-[10px]">${nf.format(total.os_m1)}</b></div>
        <div class="px-2 py-1 bg-purple-50 text-purple-900 border border-purple-200 rounded min-w-[100px] shadow-sm"><span class="block text-[8px] uppercase font-bold text-purple-600">CKPN M-1</span><b class="text-[10px]">${nf.format(total.ckpn_m1)}</b></div>
        <div class="px-2 py-1 bg-emerald-50 text-emerald-900 border border-emerald-200 rounded min-w-[100px] shadow-sm"><span class="block text-[8px] uppercase font-bold text-emerald-600">Total OS Actual</span><b class="text-[10px]">${nf.format(total.os_curr)}</b></div>
        <div class="px-2 py-1 bg-fuchsia-50 text-fuchsia-900 border border-fuchsia-200 rounded min-w-[100px] shadow-sm"><span class="block text-[8px] uppercase font-bold text-fuchsia-600">CKPN Actual</span><b class="text-[10px]">${nf.format(total.ckpn_actual)}</b></div>
        <div class="px-2 py-1 bg-orange-50 text-orange-900 border border-orange-200 rounded min-w-[100px] shadow-sm"><span class="block text-[8px] uppercase font-bold text-orange-600">+/- CKPN</span><b class="text-[10px]">${nf.format(total.pemulihan)}</b></div>
        <div class="px-2 py-1 bg-white border border-slate-200 rounded min-w-[90px] shadow-sm"><span class="block text-[8px] uppercase font-bold text-slate-500">Angs. Pokok</span><b class="text-[10px]">${nf.format(total.angs_p)}</b></div>
        <div class="px-2 py-1 bg-white border border-slate-200 rounded min-w-[90px] shadow-sm"><span class="block text-[8px] uppercase font-bold text-slate-500">Angs. Bunga</span><b class="text-[10px]">${nf.format(total.angs_b)}</b></div>
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
        if (key==='no_rekening') v = `<span class="hidden md:inline text-slate-700">TOTAL</span>`; 
        else if (key==='nama_nasabah') v = `<span class="md:hidden text-slate-700">TOTAL</span> <span class="text-blue-700 md:ml-1">(${nf.format(total.noa)} Akun)</span>`;
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
        return `<td class="px-2 border-r border-blue-200 ${customClass} nowrap ${alignClass} text-blue-900">${String(v)}</td>`;
      }).join('');

      elModThead.innerHTML = `
        <tr>${
          cols.map(([key,label,sz,type,xtraClass]) => {
            let customClass = xtraClass ? xtraClass : `col-${sz}`;
            return `<th class="px-2 border-r border-slate-200 ${customClass} nowrap th-sort" data-key="${key}" data-type="${type||'text'}" title="${label}">${label}</th>`
          }).join('')
        }</tr>
        <tr class="sticky-total">${totalCells}</tr>
      `;

      if(list.length === 0) {
        elModTbody.innerHTML = `<tr><td colspan="${cols.length}" class="py-10 text-center text-slate-400 font-medium">Data tidak ditemukan.</td></tr>`;
      } else {
        const rowHtml = d=>{
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

            h += `<td class="border-r border-slate-100 ${customClass} ${alignClass} nowrap" data-key="${key}" data-raw="${raw}" title="${String(d[key]??'')}">${String(shown)}</td>`;
          }
          h += '</tr>';
          return h;
        };

        elModTbody.innerHTML = list.map(rowHtml).join('');
      }

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

          const rows = Array.from(elModTbody.querySelectorAll('tr'));
          rows.sort((ra, rb)=>{
            const a = ra.querySelector(`td[data-key="${key}"]`)?.getAttribute('data-raw');
            const b = rb.querySelector(`td[data-key="${key}"]`)?.getAttribute('data-raw');
            if (type==='num'){
              return sortState.dir * (Number(a||0) - Number(b||0));
            } else {
              return sortState.dir * String(a||'').localeCompare(String(b||''), 'id', {numeric:true});
            }
          });
          rows.forEach(r=>elModTbody.appendChild(r));
        });
      });
  }

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
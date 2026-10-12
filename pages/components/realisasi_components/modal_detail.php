<div id="modalDetailReal" class="fixed inset-0 hidden z-[9999] flex items-end md:items-center justify-center p-0 md:p-4">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalReal()"></div>

  <div class="relative bg-white w-full h-[95vh] md:h-[92vh] max-w-[1700px] rounded-t-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up">

    <div class="flex flex-col bg-white border-b shrink-0 w-full z-50">
      <div class="flex flex-col md:flex-row md:items-center justify-between px-3 py-2.5 md:px-4 md:py-3 gap-2 w-full">

        <div class="flex-1 min-w-0 shrink-0">
          <h3 class="font-bold text-slate-800 flex items-center gap-2 text-xs md:text-lg leading-none">
            <span class="w-1.5 h-5 bg-blue-600 rounded-full hidden sm:block"></span>
            <span class="truncate">Detail Debitur</span>
            <span id="badgeBucketDetail" class="text-[9px] md:text-xs text-white px-2 py-0.5 rounded-full font-mono shrink-0">Tipe ?</span>
          </h3>
          <p class="text-[9px] md:text-[11px] text-slate-500 mt-1 md:ml-4 font-mono truncate" id="subTitleDetail">Memuat...</p>
        </div>

        <div class="flex items-center gap-1 w-full md:w-auto shrink-0 mt-1 md:mt-0">

          <div class="flex-1 min-w-0">
            <select
              id="filter_ao_modal"
              class="inp font-bold text-blue-800 bg-blue-50 border-blue-200 truncate"
              onchange="detailPageReal = 1; fetchDetailReal()"
            >
              <option value="ALL">SEMUA AO</option>
            </select>
          </div>

          <div class="relative flex-1 min-w-0">
            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
              <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input
              type="text"
              id="search_nasabah"
              onkeyup="filterTableDetail()"
              class="w-full pl-6 pr-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] md:text-xs outline-none focus:border-blue-500 font-medium placeholder-slate-400"
              placeholder="Cari nama / rek..."
            >
          </div>

          <button onclick="exportExcelDetailReal(event)" class="btn-icon bg-indigo-600 hover:bg-indigo-700 text-white px-2 rounded-lg shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
          </button>

          <button onclick="closeModalReal()" class="w-[32px] h-[32px] flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-500 hover:text-white text-red-500 transition font-bold text-lg shrink-0">
            &times;
          </button>
        </div>
      </div>
    </div>

    <div class="flex-1 overflow-auto bg-slate-50 relative p-1 md:p-3 custom-scrollbar">
      <div id="loadingModal" class="hidden absolute inset-0 bg-white/90 z-40 flex items-center justify-center text-blue-600 backdrop-blur-sm">
        <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
      </div>

      <table class="w-max min-w-full text-center md:text-left text-slate-700 border-separate border-spacing-0 md:border md:border-slate-200 md:rounded-xl shadow-sm bg-white table-fixed" id="tableExportMob">
        <thead id="headModalDetail" class="text-slate-600 font-extrabold select-none text-[9px] md:text-xs uppercase tracking-wider"></thead>
        <tbody id="bodyModalDetail" class="divide-y divide-slate-100 bg-white text-[9.5px] md:text-[12px]"></tbody>
      </table>
    </div>

    <div class="px-3 py-2 border-t bg-white flex justify-between items-center shrink-0">
      <span id="pageInfoDetail" class="text-[9px] md:text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md">0 Data</span>
      <div class="flex gap-1.5">
        <button id="btnPrevDetail" onclick="changePageDetailReal(-1)" class="px-3 py-1 bg-white border border-slate-300 rounded-md text-[9px] md:text-xs font-bold hover:bg-slate-50 disabled:opacity-50 shadow-sm">« Prev</button>
        <button id="btnNextDetail" onclick="changePageDetailReal(1)" class="px-3 py-1 bg-white border border-slate-300 rounded-md text-[9px] md:text-xs font-bold hover:bg-slate-50 disabled:opacity-50 shadow-sm">Next »</button>
      </div>
    </div>

  </div>
</div>
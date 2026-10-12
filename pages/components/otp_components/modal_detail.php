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
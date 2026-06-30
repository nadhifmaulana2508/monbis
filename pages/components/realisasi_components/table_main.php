<div class="flex-1 min-h-0 overflow-hidden bg-white rounded-xl shadow-sm border border-slate-200 relative flex flex-col z-10">
  <div id="loadingUtama" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
      <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
      <span>Menyiapkan Data...</span>
  </div>
  
  <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
    <table class="w-max min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelUtama">
      <thead class="font-bold tracking-wider text-[9px] md:text-[11px] select-none" id="headUtama">
        </thead>
      <tbody id="bodyUtama" class="divide-y divide-slate-100 bg-white text-[9.5px] md:text-xs"></tbody>
    </table>
  </div>
</div>
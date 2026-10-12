<div class="flex-1 min-h-0 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm relative flex flex-col">
    <div id="loadingRR" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-xs backdrop-blur-[1px]">
        <div class="animate-spin h-8 w-8 border-4 border-slate-200 border-t-blue-600 rounded-full mb-3"></div>
        <span>Menyiapkan Matriks...</span>
    </div>
    <div id="dueSummaryRR" class="otp-due-summary hidden shrink-0 border-b border-slate-200 bg-white p-2 md:p-3"></div>
    <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table class="min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelRekapRR">
        <thead class="uppercase sticky top-0 z-50 select-none" id="headRR"></thead>
        <tbody id="bodyRR" class="divide-y divide-slate-100 bg-white group-tbody text-xs"></tbody>
      </table>
    </div>
</div>

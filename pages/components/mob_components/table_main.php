<div class="flex-1 min-h-0 overflow-hidden bg-white rounded-xl shadow-sm border border-slate-200 relative flex flex-col">
    <div id="loadingMob" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
        <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
        <span>Menyiapkan Matriks...</span>
    </div>
    
    <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table class="w-max min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelMob">
        <thead class="font-bold tracking-wider text-[9px] md:text-xs select-none">
          <tr class="mob-row-1">
            <th id="th_grouping_label" rowspan="2" class="sticky-left px-3 md:px-4 text-left min-w-[120px] md:min-w-[180px] uppercase align-middle border-r border-slate-200">Grup Realisasi</th>
            <th rowspan="2" class="px-2 py-2 border-r border-slate-200 align-middle">MOB</th>
            <th rowspan="2" class="px-3 md:px-4 py-2 border-r border-slate-200 text-right w-[140px] md:w-[180px] align-middle">Tot Plafond</th>
            <th colspan="8" class="py-2 border-b border-slate-200 text-center uppercase tracking-widest">DPD (Days Past Due)</th>
          </tr>
          <tr class="mob-row-2 text-[9px] md:text-[11px]">
            <th class="px-2 py-1.5 border-r border-slate-200 w-[110px] md:w-[140px]">0</th>
            <th class="px-2 py-1.5 border-r border-slate-200 w-[110px] md:w-[140px]">1 - 7</th>
            <th class="px-2 py-1.5 border-r border-slate-200 w-[110px] md:w-[140px]">8 - 14</th>
            <th class="px-2 py-1.5 border-r border-slate-200 w-[110px] md:w-[140px]">15 - 21</th>
            <th class="px-2 py-1.5 border-r border-slate-200 w-[110px] md:w-[140px]">22 - 30</th>
            <th class="px-2 py-1.5 border-r border-slate-200 w-[110px] md:w-[140px]">31 - 60</th>
            <th class="px-2 py-1.5 border-r border-slate-200 w-[110px] md:w-[140px]">61 - 90</th>
            <th class="px-2 py-1.5 border-slate-200 w-[110px] md:w-[140px]">&gt; 90</th>
          </tr>
          <tr id="rowTotalMobAtas" class="mob-row-tot text-[10px] md:text-sm font-extrabold tracking-wide"></tr>
        </thead>
        <tbody id="bodyMatrix" class="divide-y divide-slate-100 bg-white text-[10px] md:text-sm"></tbody>
      </table>
    </div>
</div>
<div class="flex-1 min-h-0 overflow-hidden bg-white rounded-xl shadow-sm border border-slate-200 relative flex flex-col z-10">
  <div id="loadingMob" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
      <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
      <span>Menyiapkan Matriks...</span>
  </div>
  
  <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
    <table class="w-max min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelMob">
      <thead class="font-bold tracking-wider text-[9px] md:text-[11px] select-none">
        <tr class="mob-row-1">
          <th rowspan="2" class="sticky-left px-2 md:px-3 text-left w-[80px] md:w-[100px] uppercase align-middle border-r border-slate-200">Bulan Real</th>
          <th rowspan="2" class="px-1 md:px-2 py-1.5 border-r border-slate-200 align-middle text-blue-700 w-[40px] md:w-[50px]">MOB</th>
          <th rowspan="2" class="px-2 md:px-3 py-1.5 border-r border-slate-200 text-right w-[100px] md:w-[120px] align-middle text-blue-700">Tot Plafond</th>
          <th colspan="8" class="py-1.5 border-b border-slate-200 text-center uppercase tracking-widest text-slate-600">DPD (Days Past Due)</th>
        </tr>
        <tr class="mob-row-2 text-[8.5px] md:text-[10px]">
          <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-emerald-700">0</th>
          <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-yellow-700">1 - 7</th>
          <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-yellow-700">8 - 14</th>
          <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-orange-700">15 - 21</th>
          <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-orange-700">22 - 30</th>
          <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-red-700">31 - 60</th>
          <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-red-700">61 - 90</th>
          <th class="px-1 py-1 border-slate-200 w-[70px] md:w-[95px] text-red-800">&gt; 90</th>
        </tr>
        <tr id="rowTotalMobAtas" class="mob-row-tot text-[9px] md:text-xs font-extrabold tracking-wide"></tr>
      </thead>
      <tbody id="bodyMatrix" class="divide-y divide-slate-100 bg-white text-[9.5px] md:text-xs"></tbody>
    </table>
  </div>
</div>
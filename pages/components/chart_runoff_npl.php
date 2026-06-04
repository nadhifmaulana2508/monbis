<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-3 md:gap-4 mt-4 md:mt-6">
  
  <div class="bg-white p-4 md:p-5 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 lg:col-span-3 flex flex-col">
    <h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 border-b border-gray-100 pb-2 text-[11px] md:text-[12px] flex items-center gap-1.5 leading-tight shrink-0">
      <span>🔄</span> Realisasi vs Run Off
    </h3>
    <div id="box_runoff_realisasi" class="space-y-3 flex-grow mb-3 overflow-y-auto custom-scrollbar pr-2 max-h-[400px]"></div>
    
    <div class="mt-auto pt-2 md:pt-3 border-t border-gray-50 flex items-center justify-center gap-3 md:gap-4 text-[9px] md:text-[10px] font-bold text-gray-500 shrink-0">
        <div class="flex items-center gap-1 md:gap-1.5">
            <span class="w-2.5 h-1.5 md:w-3 md:h-1.5 rounded-full bg-green-400"></span> Realisasi
        </div>
        <div class="flex items-center gap-1 md:gap-1.5">
            <span class="w-2.5 h-1.5 md:w-3 md:h-1.5 rounded-full bg-red-400"></span> Run Off
        </div>
    </div>
  </div>

  <div class="bg-white p-4 md:p-5 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 lg:col-span-3 flex flex-col">
    <h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 border-b border-gray-100 pb-2 text-[11px] md:text-[12px] flex items-center gap-1.5 leading-tight shrink-0">
      <span>🛡️</span> Flow NPL vs Recovery
    </h3>
    <div id="box_flow_recovery" class="space-y-3 flex-grow mb-3 overflow-y-auto custom-scrollbar pr-2 max-h-[400px]"></div>
    
    <div class="mt-auto pt-2 md:pt-3 border-t border-gray-50 flex items-center justify-center gap-3 md:gap-4 text-[9px] md:text-[10px] font-bold text-gray-500 shrink-0">
        <div class="flex items-center gap-1 md:gap-1.5">
            <span class="w-2.5 h-1.5 md:w-3 md:h-1.5 rounded-full bg-red-400"></span> Flow NPL
        </div>
        <div class="flex items-center gap-1 md:gap-1.5">
            <span class="w-2.5 h-1.5 md:w-3 md:h-1.5 rounded-full bg-green-400"></span> Recovery
        </div>
    </div>
  </div>

  <div class="bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 lg:col-span-6 relative flex flex-col">
    <div id="loadingChartRunoff" class="absolute inset-0 flex justify-center items-center bg-white bg-opacity-90 z-10 hidden rounded-xl md:rounded-3xl">
       <div class="animate-spin rounded-full h-6 w-6 md:h-8 md:w-8 border-b-2 border-blue-500"></div>
    </div>
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-3 md:mb-4 border-b border-gray-100 pb-2 md:pb-3 shrink-0 gap-2 md:gap-0">
      <div>
        <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 text-[13px] md:text-lg">
          <span class="text-blue-500">📊</span> Tren Realisasi vs Run Off
        </h3>
        <span class="text-[9px] md:text-xs text-gray-400 font-medium" id="label_runoff_date">Berdasarkan Tanggal: -</span>
      </div>
      
      <select id="filter_tren_runoff" class="border border-gray-200 rounded-md px-2 md:px-3 py-1 md:py-1.5 text-[10px] md:text-sm font-semibold text-gray-600 outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer bg-white shadow-sm w-full md:w-auto">
          <option value="tahunan">Periode Tahunan</option>
          <option value="bulanan" selected>Periode Bulanan</option>
          <option value="mingguan">Periode Mingguan</option>
          <option value="30_hari">30 Hari Terakhir</option>
          <option value="14_hari">14 Hari Terakhir</option>
          <option value="7_hari">7 Hari Terakhir</option>
      </select>
    </div>
    <div class="relative w-full flex-grow min-h-[220px] md:min-h-[250px]">
      <canvas id="canvasTrenRunoff"></canvas>
    </div>
  </div>

</div>
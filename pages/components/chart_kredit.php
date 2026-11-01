<div class="grid grid-cols-1 lg:grid-cols-5 gap-3 md:gap-4">
  
  <div class="dashboard-panel dashboard-chart-panel min-w-0 overflow-hidden bg-white p-4 md:p-5 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 lg:col-span-3 flex flex-col">
    <div class="flex flex-col justify-between gap-2 md:flex-row md:items-center md:gap-0 mb-2 border-b border-gray-100 pb-3">
      <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 text-sm md:text-base">
        <svg class="dashboard-icon h-4 w-4 text-blue-500" aria-hidden="true"><use href="#dashboard-icon-trend"></use></svg> Tren Portofolio Kredit
      </h3>
      <div class="flex min-w-0 w-full flex-row gap-1.5 md:w-auto md:gap-2">
        <select id="filter_tren_tipe" class="min-w-0 flex-1 border border-gray-200 rounded-md px-2 py-1.5 text-[9px] font-semibold text-gray-600 outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer bg-white shadow-sm md:flex-none md:px-3 md:text-xs">
            <option value="nom">Nominal (Rp)</option>
            <option value="pct">Persentase (%)</option>
            <option value="npl" selected>NPL</option>
        </select>
        <select id="filter_tren" class="min-w-0 flex-1 border border-gray-200 rounded-md px-2 py-1.5 text-[9px] font-semibold text-gray-600 outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer bg-white shadow-sm md:flex-none md:px-3 md:text-xs">
            <option value="tahunan">Periode Tahunan</option>
            <option value="bulanan" selected>Periode Bulanan</option>
            <option value="mingguan">Periode Mingguan</option>
            <option value="30_hari">30 Hari Terakhir</option>
            <option value="14_hari">14 Hari Terakhir</option>
            <option value="7_hari">7 Hari Terakhir</option>
        </select>
      </div>
    </div>
    <div class="relative flex-grow min-h-[260px] sm:min-h-[300px] md:min-h-[360px] w-full mt-2">
      <div id="loadingChartTren" class="absolute inset-0 flex justify-center items-center bg-white bg-opacity-80 z-10 hidden">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
      </div>
      <canvas id="canvasTrenPortofolio"></canvas>
    </div>
  </div>

  <div class="dashboard-panel dashboard-chart-panel min-w-0 overflow-hidden bg-white p-4 md:p-5 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 lg:col-span-2 flex flex-col">
    <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
      <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 text-sm">
        <svg class="dashboard-icon h-4 w-4 text-indigo-500" aria-hidden="true"><use href="#dashboard-icon-box"></use></svg> Realisasi by Produk
      </h3>
      <div class="text-right flex flex-col items-end">
        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Total Realisasi</span>
        <span id="label_total_realisasi_produk" class="text-xs md:text-sm font-black text-indigo-600">Rp 0</span>
      </div>
    </div>
    <div id="box_realisasi_produk" class="space-y-3 flex-grow overflow-y-auto custom-scrollbar pr-2 max-h-[300px] md:max-h-[400px]"></div>
  </div>
  
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
  <div class="bg-white p-4 md:p-5 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 lg:col-span-3 flex flex-col">
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-2 md:gap-0 mb-2 border-b border-gray-100 pb-3">
      <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 text-[13px] md:text-base">
        <span class="text-blue-500">📈</span> Tren Portofolio Kredit
      </h3>
      <div class="flex flex-wrap md:flex-nowrap gap-1.5 md:gap-2 w-full md:w-auto">
        <select id="filter_tren_tipe" class="border border-gray-200 rounded-md px-2 py-1 text-[10px] md:text-xs font-semibold text-gray-600 outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer bg-white shadow-sm flex-1 md:flex-none">
            <option value="nom">Nominal (Rp)</option>
            <option value="pct">Persentase (%)</option>
            <option value="npl" selected>NPL</option>
        </select>
        <select id="filter_tren" class="border border-gray-200 rounded-md px-2 py-1 text-[10px] md:text-xs font-semibold text-gray-600 outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer bg-white shadow-sm flex-1 md:flex-none">
            <option value="tahunan">Periode Tahunan</option>
            <option value="bulanan" selected>Periode Bulanan</option>
            <option value="mingguan">Periode Mingguan</option>
            <option value="30_hari">30 Hari Terakhir</option>
            <option value="14_hari">14 Hari Terakhir</option>
            <option value="7_hari">7 Hari Terakhir</option>
        </select>
      </div>
    </div>
    <div class="relative flex-grow min-h-[300px] md:min-h-[400px] w-full mt-2">
      <div id="loadingChartTren" class="absolute inset-0 flex justify-center items-center bg-white bg-opacity-80 z-10 hidden">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
      </div>
      <canvas id="canvasTrenPortofolio"></canvas>
    </div>
  </div>

  <div class="bg-white p-4 md:p-5 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 lg:col-span-2 flex flex-col">
    <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2">
      <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 text-[13px] md:text-sm">
        <span class="text-indigo-500">📦</span> Realisasi by Produk
      </h3>
      <div class="text-right flex flex-col items-end">
        <span class="text-[8px] md:text-[9px] text-gray-400 font-bold uppercase tracking-wider">Total Realisasi</span>
        <span id="label_total_realisasi_produk" class="text-xs md:text-sm font-black text-indigo-600">Rp 0</span>
      </div>
    </div>
    <div id="box_realisasi_produk" class="space-y-3 flex-grow overflow-y-auto custom-scrollbar pr-2 max-h-[300px] md:max-h-[400px]"></div>
  </div>
</div>
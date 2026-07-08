<div class="tv-fit grid lg:grid-cols-3 gap-3 md:gap-4">
    <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 lg:col-span-2 flex flex-col">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-2 md:gap-0 mb-3 md:mb-4 border-b border-gray-100 pb-3 shrink-0">
            <div>
                <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 text-[13px] md:text-xl">
                    <span class="text-blue-500">NPL</span> Tren Portofolio Kredit
                </h3>
                <p class="text-[10px] md:text-sm text-gray-500 font-medium mt-1">Periode bulanan konsolidasi</p>
            </div>
            <div class="flex gap-2 hidden">
                <select id="filter_tren_tipe"><option value="npl" selected>NPL</option></select>
                <select id="filter_tren"><option value="bulanan" selected>Periode Bulanan</option></select>
            </div>
        </div>
        <div class="tv-chart relative flex-grow min-h-0 w-full">
            <div id="loadingChartTren" class="absolute inset-0 flex justify-center items-center bg-white bg-opacity-80 z-10 hidden">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
            </div>
            <canvas id="canvasTrenPortofolio" class="w-full h-full"></canvas>
        </div>
    </div>

    <div class="tv-card bg-white p-4 md:p-5 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col">
        <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-2 shrink-0">
            <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 text-[13px] md:text-base">
                <span class="text-indigo-500">Produk</span> Realisasi
            </h3>
            <div class="text-right flex flex-col items-end">
                <span class="text-[8px] md:text-[9px] text-gray-400 font-bold uppercase tracking-wider">Total Realisasi</span>
                <span id="label_total_realisasi_produk" class="text-xs md:text-base font-black text-indigo-600">Rp 0</span>
            </div>
        </div>
        <div id="box_realisasi_produk" class="tv-list space-y-3 flex-grow"></div>
    </div>
</div>

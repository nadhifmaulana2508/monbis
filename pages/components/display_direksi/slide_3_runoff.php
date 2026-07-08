<div class="tv-fit grid lg:grid-cols-12 gap-3 md:gap-4">
    <div class="lg:col-span-5 grid grid-rows-2 gap-3 md:gap-4 min-h-0">
        <div class="tv-card bg-white p-3 md:p-4 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-2 mb-2 shrink-0">
                <h3 class="font-bold text-gray-800 text-[12px] md:text-base flex items-center gap-2 leading-tight">
                    <span class="text-blue-500">Run Off</span> Realisasi vs Run Off
                </h3>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-2 shrink-0">
                <div class="bg-green-50 border border-green-100 rounded-lg p-2">
                    <p class="text-[8px] md:text-[9px] font-extrabold uppercase tracking-wider text-green-700">Total Realisasi</p>
                    <p id="summary_runoff_realisasi" class="text-base md:text-xl font-black text-green-600">Rp 0</p>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-lg p-2">
                    <p class="text-[8px] md:text-[9px] font-extrabold uppercase tracking-wider text-red-700">Total Run Off</p>
                    <p id="summary_runoff_total" class="text-base md:text-xl font-black text-red-600">Rp 0</p>
                </div>
            </div>

            <div id="box_runoff_realisasi" class="tv-list grid grid-cols-2 gap-x-3 gap-y-2 flex-grow mb-2"></div>
            <div class="mt-auto pt-2 border-t border-gray-50 flex items-center justify-center gap-3 md:gap-4 text-[9px] md:text-[10px] font-bold text-gray-500 shrink-0">
                <div class="flex items-center gap-1 md:gap-1.5"><span class="w-2.5 h-1.5 md:w-3 md:h-1.5 rounded-full bg-green-400"></span> Realisasi</div>
                <div class="flex items-center gap-1 md:gap-1.5"><span class="w-2.5 h-1.5 md:w-3 md:h-1.5 rounded-full bg-red-400"></span> Run Off</div>
            </div>
        </div>

        <div class="tv-card bg-white p-3 md:p-4 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col">
            <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-2 mb-2 shrink-0">
                <h3 class="font-bold text-gray-800 text-[12px] md:text-base flex items-center gap-2 leading-tight">
                    <span class="text-red-500">Flow PAR</span> Flow NPL vs Recovery
                </h3>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-2 shrink-0">
                <div class="bg-red-50 border border-red-100 rounded-lg p-2">
                    <p class="text-[8px] md:text-[9px] font-extrabold uppercase tracking-wider text-red-700">Total Flow NPL</p>
                    <p id="summary_flow_npl" class="text-base md:text-xl font-black text-red-600">Rp 0</p>
                </div>
                <div class="bg-green-50 border border-green-100 rounded-lg p-2">
                    <p class="text-[8px] md:text-[9px] font-extrabold uppercase tracking-wider text-green-700">Total Recovery</p>
                    <p id="summary_recovery_npl" class="text-base md:text-xl font-black text-green-600">Rp 0</p>
                </div>
            </div>

            <div id="box_flow_recovery" class="tv-list grid grid-cols-2 gap-x-3 gap-y-2 flex-grow mb-2"></div>
            <div class="mt-auto pt-2 border-t border-gray-50 flex items-center justify-center gap-3 md:gap-4 text-[9px] md:text-[10px] font-bold text-gray-500 shrink-0">
                <div class="flex items-center gap-1 md:gap-1.5"><span class="w-2.5 h-1.5 md:w-3 md:h-1.5 rounded-full bg-red-400"></span> Flow NPL</div>
                <div class="flex items-center gap-1 md:gap-1.5"><span class="w-2.5 h-1.5 md:w-3 md:h-1.5 rounded-full bg-green-400"></span> Recovery</div>
            </div>
        </div>
    </div>

    <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 lg:col-span-7 relative flex flex-col">
        <div id="loadingChartRunoff" class="absolute inset-0 flex justify-center items-center bg-white bg-opacity-90 z-10 hidden rounded-xl md:rounded-3xl">
            <div class="animate-spin rounded-full h-6 w-6 md:h-8 md:w-8 border-b-2 border-blue-500"></div>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-3 md:mb-4 border-b border-gray-100 pb-2 md:pb-3 shrink-0 gap-2 md:gap-0">
            <div>
                <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 text-[14px] md:text-2xl">
                    <span class="text-blue-500">Tren</span> Realisasi vs Run Off
                </h3>
                <span class="text-[10px] md:text-sm text-gray-400 font-medium" id="label_runoff_date">Berdasarkan Tanggal: -</span>
            </div>
            <select id="filter_tren_runoff" class="hidden"><option value="bulanan" selected>Periode Bulanan</option></select>
        </div>
        <div class="tv-chart relative w-full flex-grow min-h-0">
            <canvas id="canvasTrenRunoff" class="w-full h-full"></canvas>
        </div>
    </div>
</div>

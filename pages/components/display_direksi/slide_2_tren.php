<div class="tv-fit flex flex-col">
    <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4 md:mb-6 border-b border-gray-100 pb-4 shrink-0">
            <div class="w-full md:w-1/2 lg:w-1/3 hidden">
                <select id="select_coa" class="w-full font-semibold text-gray-700 bg-white cursor-pointer" placeholder="Cari Kode atau Nama Perkiraan...">
                    <option value="LABA_RUGI">TOTAL LABA / RUGI BERJALAN</option>
                </select>
            </div>
            <div class="min-w-0">
                <h2 class="text-base md:text-xl xl:text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                    <span class="text-blue-500 bg-blue-50 p-1.5 rounded-lg">COA</span> Analisis Tren Perkiraan
                </h2>
                <p class="text-[10px] md:text-sm text-gray-500 font-medium mt-1">Laba rugi konsolidasi berdasarkan data lapkeu H-1</p>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 px-5 flex flex-col items-end min-w-[220px] shrink-0 w-full md:w-auto">
                <span class="text-[10px] text-blue-600 font-extrabold uppercase tracking-wider mb-1" id="lbl_coa_summary">TOTAL SALDO (H-1)</span>
                <span class="text-xl md:text-3xl font-black text-gray-900" id="txt_coa_saldo">Rp 0</span>
                <div id="txt_coa_growth" class="mt-1 flex items-center gap-1"></div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 md:gap-6 relative flex-grow min-h-0">
            <div id="loadingChartCoa" class="absolute inset-0 flex justify-center items-center bg-white bg-opacity-90 z-20 hidden rounded-xl">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
            <div class="tv-card border border-gray-100 rounded-xl p-4 md:p-5 bg-white shadow-sm flex flex-col">
                <h3 class="font-bold text-gray-800 mb-1 text-sm md:text-lg">Perbandingan Month-to-Month</h3>
                <p class="text-[10px] md:text-xs text-gray-400 mb-4">Tren bulanan selama 4 bulan terakhir</p>
                <div class="tv-chart relative w-full flex-grow min-h-0">
                    <canvas id="canvasCoaMtM" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="tv-card border border-gray-100 rounded-xl p-4 md:p-5 bg-white shadow-sm flex flex-col">
                <h3 class="font-bold text-gray-800 mb-1 text-sm md:text-lg">Perbandingan Year-to-Year</h3>
                <p class="text-[10px] md:text-xs text-gray-400 mb-4">Tren tahunan selama 5 tahun terakhir</p>
                <div class="tv-chart relative w-full flex-grow min-h-0">
                    <canvas id="canvasCoaYtY" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

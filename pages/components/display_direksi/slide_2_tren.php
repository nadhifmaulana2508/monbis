<div class="tv-fit tv-trend-slide flex flex-col">
    <div class="tv-card tv-trend-shell bg-white p-3 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="tv-trend-header flex flex-col gap-2.5 md:gap-3 border-b border-gray-100 shrink-0">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h2 class="tv-trend-title text-base md:text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                        <span class="text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">TREND</span> Tren Aset, Kredit, DPK, Laba
                    </h2>
                    <p class="tv-trend-subtitle text-[10px] md:text-xs text-gray-500 font-medium mt-1">Histori mingguan mengikuti bulan pada harian date yang dipilih</p>
                </div>
                <div class="tv-trend-net bg-amber-50 border border-amber-100 rounded-xl px-4 py-2 flex items-center justify-between gap-4 min-w-[340px] shrink-0 w-full md:w-auto">
                    <div class="min-w-0">
                        <span class="block text-[9px] text-amber-700 font-extrabold uppercase tracking-wider">Laba Net</span>
                        <span class="block text-lg md:text-xl font-black text-amber-900 leading-tight whitespace-nowrap" id="txt_tren_laba_net">Rp 0</span>
                    </div>
                    <div id="txt_tren_laba_net_detail" class="text-[9px] md:text-[10px] text-gray-500 font-semibold text-right leading-tight shrink-0"></div>
                </div>
            </div>

            <div class="tv-trend-summary-grid grid grid-cols-2 xl:grid-cols-4 gap-2.5">
                <div class="tv-trend-summary rounded-xl border border-sky-100 bg-sky-50 p-2.5">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-sky-700">Aset Gabungan</div>
                    <div class="text-base md:text-xl font-black text-sky-900 mt-0.5" id="summary_tren_aset">Rp 0</div>
                    <div id="delta_tren_aset"></div>
                </div>
                <div class="tv-trend-summary rounded-xl border border-emerald-100 bg-emerald-50 p-2.5">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">Kredit Baki Debet</div>
                    <div class="text-base md:text-xl font-black text-emerald-900 mt-0.5" id="summary_tren_kredit">Rp 0</div>
                    <div id="delta_tren_kredit"></div>
                </div>
                <div class="tv-trend-summary rounded-xl border border-violet-100 bg-violet-50 p-2.5">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-violet-700">DPK</div>
                    <div class="text-base md:text-xl font-black text-violet-900 mt-0.5" id="summary_tren_dpk">Rp 0</div>
                    <div id="delta_tren_dpk"></div>
                </div>
                <div class="tv-trend-summary rounded-xl border border-amber-100 bg-amber-50 p-2.5">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700">Laba</div>
                    <div class="text-base md:text-xl font-black text-amber-900 mt-0.5" id="summary_tren_laba">Rp 0</div>
                    <div id="delta_tren_laba"></div>
                </div>
            </div>
        </div>

        <div class="relative flex-grow min-h-0">
            <div id="loadingChartCoa" class="absolute inset-0 flex justify-center items-center bg-white bg-opacity-90 z-20 hidden rounded-xl">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
            <div class="tv-trend-grid grid md:grid-cols-2 gap-3 h-full">
                <div class="tv-card tv-trend-panel border border-sky-100 rounded-xl p-3 bg-sky-50 shadow-sm flex flex-col min-h-0">
                    <div class="flex items-start justify-between gap-3 mb-1.5 shrink-0">
                        <div class="min-w-0">
                            <h3 class="font-bold text-sky-900 text-sm md:text-base">Tren Aset</h3>
                            <p class="text-[10px] md:text-xs text-sky-700/80">Aset gabungan per pekan</p>
                        </div>
                        <div id="mini_tren_aset" class="text-right text-[10px] md:text-xs font-bold text-sky-700"></div>
                    </div>
                    <div class="tv-chart relative w-full flex-grow min-h-0">
                        <canvas id="canvasTrendAset" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div class="tv-card tv-trend-panel border border-emerald-100 rounded-xl p-3 bg-emerald-50 shadow-sm flex flex-col min-h-0">
                    <div class="flex items-start justify-between gap-3 mb-1.5 shrink-0">
                        <div class="min-w-0">
                            <h3 class="font-bold text-emerald-900 text-sm md:text-base">Tren Kredit</h3>
                            <p class="text-[10px] md:text-xs text-emerald-700/80">Kredit baki debet per pekan</p>
                        </div>
                        <div id="mini_tren_kredit" class="text-right text-[10px] md:text-xs font-bold text-emerald-700"></div>
                    </div>
                    <div class="tv-chart relative w-full flex-grow min-h-0">
                        <canvas id="canvasTrendKredit" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div class="tv-card tv-trend-panel border border-violet-100 rounded-xl p-3 bg-violet-50 shadow-sm flex flex-col min-h-0">
                    <div class="flex items-start justify-between gap-3 mb-1.5 shrink-0">
                        <div class="min-w-0">
                            <h3 class="font-bold text-violet-900 text-sm md:text-base">Tren DPK</h3>
                            <p class="text-[10px] md:text-xs text-violet-700/80">DPK per pekan</p>
                        </div>
                        <div id="mini_tren_dpk" class="text-right text-[10px] md:text-xs font-bold text-violet-700"></div>
                    </div>
                    <div class="tv-chart relative w-full flex-grow min-h-0">
                        <canvas id="canvasTrendDpk" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div class="tv-card tv-trend-panel border border-amber-100 rounded-xl p-3 bg-amber-50 shadow-sm flex flex-col min-h-0">
                    <div class="flex items-start justify-between gap-3 mb-1.5 shrink-0">
                        <div class="min-w-0">
                            <h3 class="font-bold text-amber-900 text-sm md:text-base">Tren Laba</h3>
                            <p class="text-[10px] md:text-xs text-amber-700/80">Laba per pekan</p>
                        </div>
                        <div id="mini_tren_laba" class="text-right text-[10px] md:text-xs font-bold text-amber-700"></div>
                    </div>
                    <div class="tv-chart relative w-full flex-grow min-h-0">
                        <canvas id="canvasTrendLaba" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

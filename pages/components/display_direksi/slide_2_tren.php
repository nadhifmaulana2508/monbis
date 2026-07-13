<div class="tv-fit flex flex-col">
    <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="flex flex-col gap-4 mb-4 md:mb-6 border-b border-gray-100 pb-4 shrink-0">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-base md:text-xl xl:text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                        <span class="text-blue-500 bg-blue-50 p-1.5 rounded-lg">TREND</span> Tren Aset, Kredit, DPK, Laba
                    </h2>
                    <p class="text-[10px] md:text-sm text-gray-500 font-medium mt-1">Histori mingguan mengikuti bulan pada harian date yang dipilih</p>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 px-5 flex flex-col items-end min-w-[220px] shrink-0 w-full md:w-auto">
                    <span class="text-[10px] text-blue-600 font-extrabold uppercase tracking-wider mb-1" id="lbl_tren_makro_weekly">PERIODE HISTORI</span>
                    <span class="text-xl md:text-3xl font-black text-gray-900" id="txt_tren_weekly_period">-</span>
                    <div id="txt_tren_weekly_date" class="mt-1 text-[10px] md:text-xs text-gray-500 font-semibold"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                <div class="rounded-xl border border-sky-100 bg-sky-50 p-3">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-sky-700">Aset Gabungan</div>
                    <div class="text-lg md:text-2xl font-black text-sky-900 mt-1" id="summary_tren_aset">Rp 0</div>
                    <div class="mt-1" id="delta_tren_aset"></div>
                </div>
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">Kredit Baki Debet</div>
                    <div class="text-lg md:text-2xl font-black text-emerald-900 mt-1" id="summary_tren_kredit">Rp 0</div>
                    <div class="mt-1" id="delta_tren_kredit"></div>
                </div>
                <div class="rounded-xl border border-violet-100 bg-violet-50 p-3">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-violet-700">DPK</div>
                    <div class="text-lg md:text-2xl font-black text-violet-900 mt-1" id="summary_tren_dpk">Rp 0</div>
                    <div class="mt-1" id="delta_tren_dpk"></div>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50 p-3">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700">Laba Net</div>
                    <div class="text-lg md:text-2xl font-black text-amber-900 mt-1" id="summary_tren_laba">Rp 0</div>
                    <div class="mt-1" id="delta_tren_laba"></div>
                </div>
            </div>
        </div>

        <div class="relative flex-grow min-h-0">
            <div id="loadingChartCoa" class="absolute inset-0 flex justify-center items-center bg-white bg-opacity-90 z-20 hidden rounded-xl">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
            <div class="grid md:grid-cols-2 gap-4 h-full">
                <div class="tv-card border border-sky-100 rounded-xl p-4 bg-sky-50 shadow-sm flex flex-col min-h-0">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <h3 class="font-bold text-sky-900 text-sm md:text-lg">Tren Aset</h3>
                            <p class="text-[10px] md:text-xs text-sky-700/80">Aset gabungan per pekan</p>
                        </div>
                        <div id="mini_tren_aset" class="text-right text-[10px] md:text-xs font-bold text-sky-700"></div>
                    </div>
                    <div class="tv-chart relative w-full flex-grow min-h-0">
                        <canvas id="canvasTrendAset" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div class="tv-card border border-emerald-100 rounded-xl p-4 bg-emerald-50 shadow-sm flex flex-col min-h-0">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <h3 class="font-bold text-emerald-900 text-sm md:text-lg">Tren Kredit</h3>
                            <p class="text-[10px] md:text-xs text-emerald-700/80">Kredit baki debet per pekan</p>
                        </div>
                        <div id="mini_tren_kredit" class="text-right text-[10px] md:text-xs font-bold text-emerald-700"></div>
                    </div>
                    <div class="tv-chart relative w-full flex-grow min-h-0">
                        <canvas id="canvasTrendKredit" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div class="tv-card border border-violet-100 rounded-xl p-4 bg-violet-50 shadow-sm flex flex-col min-h-0">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <h3 class="font-bold text-violet-900 text-sm md:text-lg">Tren DPK</h3>
                            <p class="text-[10px] md:text-xs text-violet-700/80">DPK per pekan</p>
                        </div>
                        <div id="mini_tren_dpk" class="text-right text-[10px] md:text-xs font-bold text-violet-700"></div>
                    </div>
                    <div class="tv-chart relative w-full flex-grow min-h-0">
                        <canvas id="canvasTrendDpk" class="w-full h-full"></canvas>
                    </div>
                </div>

                <div class="tv-card border border-amber-100 rounded-xl p-4 bg-amber-50 shadow-sm flex flex-col min-h-0">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <h3 class="font-bold text-amber-900 text-sm md:text-lg">Tren Laba</h3>
                            <p class="text-[10px] md:text-xs text-amber-700/80">Laba net per pekan</p>
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

<div class="tv-fit tv-realisasi-slide flex flex-col">
    <div class="tv-card tv-realisasi-shell bg-white p-3 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="tv-realisasi-header flex items-start justify-between gap-3 border-b border-gray-100 pb-3 shrink-0">
            <div class="min-w-0">
                <h2 class="text-base md:text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                    <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">REALISASI</span>
                    Monitoring Realisasi Cabang
                </h2>
                <p class="text-[10px] md:text-xs text-gray-500 font-medium mt-1">
                    Pantauan realisasi dan growth cabang per periode untuk percepatan follow up
                </p>
            </div>
            <div class="text-right shrink-0 bg-blue-50 border border-blue-100 rounded-xl px-3 py-2 min-w-[180px]">
                <p class="text-[8px] md:text-[9px] font-extrabold uppercase tracking-wider text-blue-700">Posisi Data</p>
                <p id="tv_realisasi_date" class="text-sm md:text-lg font-black text-blue-950 leading-tight">-</p>
                <p id="tv_realisasi_mode" class="text-[9px] md:text-[10px] font-bold text-blue-500 mt-0.5">Konsolidasi</p>
            </div>
        </div>

        <div id="tv_realisasi_summary" class="tv-realisasi-summary grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-2.5 py-3 shrink-0"></div>

        <div class="tv-realisasi-period-grid grid grid-cols-2 xl:grid-cols-4 gap-3 flex-grow min-h-0">
            <section class="tv-realisasi-period border border-slate-100 rounded-xl bg-slate-50 p-3 flex flex-col min-h-0">
                <div class="flex items-start justify-between gap-2 mb-2 shrink-0">
                    <div>
                        <h3 class="text-sm md:text-base font-black text-slate-900">Bulan Ini</h3>
                        <p id="tv_real_period_bulan_ini" class="text-[9px] md:text-[10px] font-bold text-slate-500">-</p>
                    </div>
                    <span id="tv_real_badge_bulan_ini" class="px-2 py-1 rounded-lg bg-white border border-slate-200 text-[10px] font-black text-red-600">0 minus</span>
                </div>
                <div id="tv_real_list_bulan_ini" class="tv-list flex-grow min-h-0 overflow-y-auto custom-scrollbar pr-1"></div>
            </section>

            <section class="tv-realisasi-period border border-emerald-100 rounded-xl bg-emerald-50 p-3 flex flex-col min-h-0">
                <div class="flex items-start justify-between gap-2 mb-2 shrink-0">
                    <div>
                        <h3 class="text-sm md:text-base font-black text-emerald-950">Minggu Lalu</h3>
                        <p id="tv_real_period_minggu_lalu" class="text-[9px] md:text-[10px] font-bold text-emerald-700/70">-</p>
                    </div>
                    <span id="tv_real_badge_minggu_lalu" class="px-2 py-1 rounded-lg bg-white border border-emerald-100 text-[10px] font-black text-red-600">0 minus</span>
                </div>
                <div id="tv_real_list_minggu_lalu" class="tv-list flex-grow min-h-0 overflow-y-auto custom-scrollbar pr-1"></div>
            </section>

            <section class="tv-realisasi-period border border-amber-100 rounded-xl bg-amber-50 p-3 flex flex-col min-h-0">
                <div class="flex items-start justify-between gap-2 mb-2 shrink-0">
                    <div>
                        <h3 class="text-sm md:text-base font-black text-amber-950">Minggu Ini</h3>
                        <p id="tv_real_period_minggu_ini" class="text-[9px] md:text-[10px] font-bold text-amber-700/70">-</p>
                    </div>
                    <span id="tv_real_badge_minggu_ini" class="px-2 py-1 rounded-lg bg-white border border-amber-100 text-[10px] font-black text-red-600">0 minus</span>
                </div>
                <div id="tv_real_list_minggu_ini" class="tv-list flex-grow min-h-0 overflow-y-auto custom-scrollbar pr-1"></div>
            </section>

            <section class="tv-realisasi-period border border-rose-100 rounded-xl bg-rose-50 p-3 flex flex-col min-h-0">
                <div class="flex items-start justify-between gap-2 mb-2 shrink-0">
                    <div>
                        <h3 class="text-sm md:text-base font-black text-rose-950">Hari Ini</h3>
                        <p id="tv_real_period_hari_ini" class="text-[9px] md:text-[10px] font-bold text-rose-700/70">-</p>
                    </div>
                    <span id="tv_real_badge_hari_ini" class="px-2 py-1 rounded-lg bg-white border border-rose-100 text-[10px] font-black text-red-600">0 minus</span>
                </div>
                <div id="tv_real_list_hari_ini" class="tv-list flex-grow min-h-0 overflow-y-auto custom-scrollbar pr-1"></div>
            </section>
        </div>
    </div>
</div>

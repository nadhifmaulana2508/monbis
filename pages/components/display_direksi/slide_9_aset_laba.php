<div class="tv-fit">
    <div class="tv-card bg-white p-3 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="flex items-center gap-2.5 mb-3 border-b border-gray-100 pb-2.5 shrink-0">
            <div class="bg-blue-50 px-2 py-1.5 rounded-lg text-blue-600 font-black text-sm">A</div>
            <div class="min-w-0">
                <p class="text-[9px] font-black tracking-[0.25em] text-blue-400 uppercase">Analytics Overview</p>
                <h2 class="text-base md:text-xl font-extrabold text-gray-900 tracking-tight leading-tight">Net Aset dan Laba per Kantor</h2>
                <p id="label_dist_aset_laba_period" class="text-[10px] text-gray-500 font-bold">Aktual dikurangi bulan sebelumnya</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-3 flex-grow min-h-0">
            <section class="tv-card border border-gray-100 rounded-xl p-3 flex flex-col min-h-0">
                <div class="flex items-start justify-between gap-3 mb-2 shrink-0">
                    <div>
                        <p class="text-[9px] font-black tracking-[0.2em] text-blue-400 uppercase">Aset</p>
                        <h3 class="font-extrabold text-gray-900 text-sm md:text-base">Net Aset per Kantor</h3>
                    </div>
                    <div class="tv-distribution-toggle">
                        <button type="button" id="btn_dist_aset_bar" class="is-active" onclick="setDistributionView('aset','bar')">Bar</button>
                        <button type="button" id="btn_dist_aset_table" onclick="setDistributionView('aset','table')">Table</button>
                    </div>
                </div>
                <div id="dist_aset_bar" class="tv-distribution-chart flex-grow min-h-0"><canvas id="canvasDistAset"></canvas></div>
                <div id="dist_aset_table" class="tv-distribution-table hidden flex-grow min-h-0"></div>
                <div id="dist_aset_summary" class="tv-distribution-summary mt-2 shrink-0"></div>
            </section>

            <section class="tv-card border border-gray-100 rounded-xl p-3 flex flex-col min-h-0">
                <div class="flex items-start justify-between gap-3 mb-2 shrink-0">
                    <div>
                        <p class="text-[9px] font-black tracking-[0.2em] text-indigo-400 uppercase">Laba</p>
                        <h3 class="font-extrabold text-gray-900 text-sm md:text-base">Net Laba Rugi per Kantor</h3>
                    </div>
                    <div class="tv-distribution-toggle">
                        <button type="button" id="btn_dist_laba_bar" onclick="setDistributionView('laba','bar')">Bar</button>
                        <button type="button" id="btn_dist_laba_table" class="is-active" onclick="setDistributionView('laba','table')">Table</button>
                    </div>
                </div>
                <div id="dist_laba_bar" class="tv-distribution-chart hidden flex-grow min-h-0"><canvas id="canvasDistLaba"></canvas></div>
                <div id="dist_laba_table" class="tv-distribution-table flex-grow min-h-0"></div>
                <div id="dist_laba_summary" class="tv-distribution-summary mt-2 shrink-0"></div>
            </section>
        </div>
    </div>
</div>

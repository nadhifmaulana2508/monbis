<div class="tv-fit">
    <div class="tv-card bg-white p-3 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="flex items-center gap-2.5 mb-3 border-b border-gray-100 pb-2.5 shrink-0">
            <div class="bg-emerald-50 px-2 py-1.5 rounded-lg text-emerald-600 font-black text-sm">P</div>
            <div class="min-w-0">
                <p class="text-[9px] font-black tracking-[0.25em] text-emerald-400 uppercase">Analytics Overview</p>
                <h2 class="text-base md:text-xl font-extrabold text-gray-900 tracking-tight leading-tight">Pendapatan dan Beban per Kantor</h2>
                <p id="label_dist_pendapatan_beban_period" class="text-[10px] text-gray-500 font-bold">Posisi data per kantor</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-3 flex-grow min-h-0">
            <section class="tv-card border border-gray-100 rounded-xl p-3 flex flex-col min-h-0">
                <div class="flex items-start justify-between gap-3 mb-2 shrink-0">
                    <div>
                        <p class="text-[9px] font-black tracking-[0.2em] text-emerald-400 uppercase">Pendapatan</p>
                        <h3 class="font-extrabold text-gray-900 text-sm md:text-base">Pendapatan per Kantor</h3>
                    </div>
                    <div class="tv-distribution-toggle">
                        <button type="button" id="btn_dist_pendapatan_bar" class="is-active" onclick="setDistributionView('pendapatan','bar')">Bar</button>
                        <button type="button" id="btn_dist_pendapatan_table" onclick="setDistributionView('pendapatan','table')">Table</button>
                    </div>
                </div>
                <div id="dist_pendapatan_bar" class="tv-distribution-chart flex-grow min-h-0"><canvas id="canvasDistPendapatan"></canvas></div>
                <div id="dist_pendapatan_table" class="tv-distribution-table hidden flex-grow min-h-0"></div>
                <div id="dist_pendapatan_summary" class="tv-distribution-summary mt-2 shrink-0"></div>
            </section>

            <section class="tv-card border border-gray-100 rounded-xl p-3 flex flex-col min-h-0">
                <div class="flex items-start justify-between gap-3 mb-2 shrink-0">
                    <div>
                        <p class="text-[9px] font-black tracking-[0.2em] text-red-400 uppercase">Beban</p>
                        <h3 class="font-extrabold text-gray-900 text-sm md:text-base">Beban per Kantor</h3>
                    </div>
                    <div class="tv-distribution-toggle">
                        <button type="button" id="btn_dist_beban_bar" onclick="setDistributionView('beban','bar')">Bar</button>
                        <button type="button" id="btn_dist_beban_table" class="is-active" onclick="setDistributionView('beban','table')">Table</button>
                    </div>
                </div>
                <div id="dist_beban_bar" class="tv-distribution-chart hidden flex-grow min-h-0"><canvas id="canvasDistBeban"></canvas></div>
                <div id="dist_beban_table" class="tv-distribution-table flex-grow min-h-0"></div>
                <div id="dist_beban_summary" class="tv-distribution-summary mt-2 shrink-0"></div>
            </section>
        </div>
    </div>
</div>

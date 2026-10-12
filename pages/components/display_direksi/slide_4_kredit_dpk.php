<div class="tv-fit">
    <div class="tv-card bg-white p-3 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="flex items-center gap-2.5 mb-2.5 border-b border-gray-100 pb-2.5 shrink-0">
            <div class="bg-yellow-100 px-2 py-1.5 rounded-lg"><span class="text-base md:text-xl">#</span></div>
            <div class="min-w-0">
                <h2 class="text-base md:text-xl font-extrabold text-gray-900 tracking-tight leading-tight">5 Best Performance</h2>
                <p class="text-[10px] md:text-xs text-gray-500 font-medium truncate">Jajaran Cabang dan Pegawai Terbaik</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-12 gap-3 flex-grow min-h-0">
            <div class="lg:col-span-9 flex flex-col min-h-0">
                <div class="grid md:grid-cols-3 gap-x-4 gap-y-3 h-full min-h-0">
                    <section class="flex flex-col min-h-0">
                        <h3 class="font-bold text-gray-800 mb-1.5 text-[11px] md:text-xs flex items-center gap-1.5 truncate"><span class="text-blue-500">1.</span> Top Realisasi Cabang</h3>
                        <div id="best_realisasi" class="tv-list flex-grow"></div>
                    </section>

                    <section class="flex flex-col min-h-0">
                        <h3 class="font-bold text-gray-800 mb-1.5 text-[11px] md:text-xs flex items-center gap-1.5 truncate"><span class="text-emerald-500">2.</span> Top NPL Terendah (Terbaik)</h3>
                        <div id="best_npl" class="tv-list flex-grow"></div>
                    </section>

                    <section class="flex flex-col min-h-0">
                        <h3 class="font-bold text-gray-800 mb-1.5 text-[11px] md:text-xs flex items-center gap-1.5 truncate"><span class="text-teal-500">3.</span> NPL Membaik (Penurunan)</h3>
                        <div id="best_npl_turun" class="tv-list flex-grow"></div>
                    </section>

                    <section class="flex flex-col min-h-0 border-t border-dashed border-gray-200 pt-3">
                        <h3 class="font-bold text-gray-800 mb-1.5 text-[11px] md:text-xs flex items-center gap-1.5 truncate"><span class="text-indigo-500">4.</span> Top Realisasi AO</h3>
                        <div id="best_realisasi_ao" class="tv-list flex-grow"></div>
                    </section>

                    <section class="flex flex-col min-h-0 border-t border-dashed border-gray-200 pt-3">
                        <h3 class="font-bold text-gray-800 mb-1.5 text-[11px] md:text-xs flex items-center gap-1.5 truncate"><span class="text-amber-500">5.</span> Top Repayment Rate (RR)</h3>
                        <div id="best_rr" class="tv-list flex-grow"></div>
                    </section>

                    <div class="hidden md:block border-t border-dashed border-gray-200"></div>
                </div>
            </div>

            <aside class="tv-card bg-slate-800 border border-slate-700 rounded-xl p-3 md:p-4 flex flex-col min-h-0 text-white lg:col-span-3">
                <div class="flex items-center gap-2 mb-2.5 pb-2 border-b border-slate-600 shrink-0">
                    <span class="text-yellow-300 text-base">i</span>
                    <h3 class="font-bold text-yellow-300 text-sm md:text-base">Key Insights</h3>
                </div>
                <div id="dynamic_insights" class="tv-list space-y-2.5 text-[10px] md:text-[12px] leading-relaxed flex-grow"></div>
            </aside>
        </div>
    </div>
</div>

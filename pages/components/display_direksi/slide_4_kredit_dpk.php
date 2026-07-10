<div class="tv-fit">
    <div class="tv-card bg-white p-4 md:p-5 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="flex items-center gap-2.5 md:gap-3 mb-3 md:mb-4 border-b border-gray-100 pb-2.5 md:pb-3 shrink-0">
            <div class="bg-yellow-100 p-1.5 md:p-2 rounded-lg"><span class="text-xl md:text-2xl">★</span></div>
            <div>
                <h2 class="text-lg md:text-xl font-extrabold text-gray-900 tracking-tight">Best Performance</h2>
                <p class="text-[10px] md:text-xs text-gray-500 font-medium">Cabang, AO, RR, dan NPL terbaik seperti tampilan dashboard</p>
            </div>
        </div>

        <div class="grid xl:grid-cols-12 gap-3 md:gap-4 flex-grow min-h-0">
            <div class="xl:col-span-9 grid md:grid-cols-3 gap-3 md:gap-4 min-h-0">
                <section class="tv-card bg-white border border-gray-100 rounded-xl p-3 md:p-3.5 flex flex-col min-h-0">
                    <h3 class="font-bold text-gray-800 mb-2 text-[11px] md:text-[13px] flex items-center gap-2"><span class="text-blue-500">📈</span> Top Realisasi Cabang</h3>
                    <div id="best_realisasi" class="tv-list flex-grow"></div>
                </section>

                <section class="tv-card bg-white border border-gray-100 rounded-xl p-3 md:p-3.5 flex flex-col min-h-0">
                    <h3 class="font-bold text-gray-800 mb-2 text-[11px] md:text-[13px] flex items-center gap-2"><span class="text-red-500">🛡️</span> Top NPL Terendah (Terbaik)</h3>
                    <div id="best_npl" class="tv-list flex-grow"></div>
                </section>

                <section class="tv-card bg-white border border-gray-100 rounded-xl p-3 md:p-3.5 flex flex-col min-h-0">
                    <h3 class="font-bold text-gray-800 mb-2 text-[11px] md:text-[13px] flex items-center gap-2"><span class="text-teal-500">🎉</span> NPL Membaik (Penurunan)</h3>
                    <div id="best_npl_turun" class="tv-list flex-grow"></div>
                </section>

                <section class="tv-card bg-white border border-gray-100 rounded-xl p-3 md:p-3.5 flex flex-col min-h-0">
                    <h3 class="font-bold text-gray-800 mb-2 text-[11px] md:text-[13px] flex items-center gap-2"><span class="text-orange-500">🥇</span> Top Realisasi AO</h3>
                    <div id="best_realisasi_ao" class="tv-list flex-grow"></div>
                </section>

                <section class="tv-card bg-white border border-gray-100 rounded-xl p-3 md:p-3.5 flex flex-col min-h-0">
                    <h3 class="font-bold text-gray-800 mb-2 text-[11px] md:text-[13px] flex items-center gap-2"><span class="text-yellow-500">🏆</span> Top Repayment Rate (RR)</h3>
                    <div id="best_rr" class="tv-list flex-grow"></div>
                </section>

                <div class="hidden md:block"></div>
            </div>

            <aside class="tv-card bg-slate-800 border border-slate-700 rounded-2xl p-4 flex flex-col min-h-0 text-white xl:col-span-3">
                <h3 class="font-bold text-yellow-300 mb-2 md:mb-3 text-sm md:text-base border-b border-slate-600 pb-2 flex items-center gap-2">
                    <span class="text-lg md:text-xl">💡</span> Key Insights
                </h3>
                <div id="dynamic_insights" class="tv-list space-y-2.5 text-[10px] md:text-[12px] leading-relaxed flex-grow"></div>
            </aside>
        </div>
    </div>
</div>

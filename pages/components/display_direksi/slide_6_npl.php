<div class="tv-fit grid lg:grid-cols-12 gap-3 md:gap-4">
    <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 lg:col-span-8 flex flex-col">
        <div class="flex items-center gap-2.5 md:gap-3 mb-4 border-b border-gray-100 pb-3 shrink-0">
            <div class="bg-red-100 p-1.5 md:p-2 rounded-lg"><span class="text-xl md:text-3xl">!</span></div>
            <div>
                <h2 class="text-lg md:text-2xl font-extrabold text-gray-900 tracking-tight">Kredit Non Perform</h2>
                <p class="text-[10px] md:text-sm text-gray-500 font-medium">Cabang yang butuh perhatian pada NPL</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-4 md:gap-5 flex-grow min-h-0">
            <section class="tv-card bg-gray-50 border border-gray-100 rounded-xl p-3 md:p-4 flex flex-col">
                <h3 class="font-bold text-gray-800 mb-3 text-xs md:text-sm">Top NPL Terburuk</h3>
                <div id="list_npl_top" class="tv-list space-y-3 flex-grow"></div>
            </section>
            <section class="tv-card bg-gray-50 border border-gray-100 rounded-xl p-3 md:p-4 flex flex-col">
                <h3 class="font-bold text-gray-800 mb-3 text-xs md:text-sm">NPL Memburuk</h3>
                <div id="list_npl_naik" class="tv-list space-y-3 flex-grow"></div>
            </section>
        </div>
    </div>

    <div class="tv-card bg-[#1e293b] p-4 md:p-6 rounded-xl md:rounded-3xl shadow-md border border-gray-700 lg:col-span-4 flex flex-col">
        <h3 class="font-bold text-yellow-300 mb-4 text-sm md:text-lg border-b border-gray-600 pb-3">Key Insights</h3>
        <div id="dynamic_insights" class="tv-list space-y-4 text-[12px] md:text-sm text-gray-300 font-medium flex-grow"></div>
        <div class="border-t border-gray-700 pt-4 mt-4">
            <h3 class="font-bold text-teal-300 mb-3 text-xs md:text-sm">NPL Membaik</h3>
            <div id="best_npl_turun" class="tv-list space-y-3"></div>
        </div>
    </div>
</div>

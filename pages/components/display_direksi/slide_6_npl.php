<div class="tv-fit">
    <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="flex items-center gap-2.5 md:gap-3 mb-4 md:mb-6 border-b border-gray-100 pb-3 md:pb-4 shrink-0">
            <div class="bg-red-100 p-1.5 md:p-2 rounded-lg"><span class="text-xl md:text-3xl">🚨</span></div>
            <div>
                <h2 class="text-lg md:text-2xl font-extrabold text-gray-900 tracking-tight">Kredit Non Perform</h2>
                <p class="text-[10px] md:text-sm text-gray-500 font-medium">Peringatan Kinerja & Cabang Terburuk</p>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 md:gap-6 flex-grow min-h-0">
            <section class="tv-card bg-white border border-gray-100 rounded-xl p-3 md:p-4 flex flex-col min-h-0">
                <h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-2"><span class="text-red-500">🚨</span> Top NPL Terburuk (Highest)</h3>
                <div id="list_npl_top" class="tv-list flex-grow"></div>
            </section>

            <section class="tv-card bg-white border border-gray-100 rounded-xl p-3 md:p-4 flex flex-col min-h-0">
                <h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-2"><span class="text-orange-500">⚠️</span> NPL Memburuk (Naik)</h3>
                <div id="list_npl_naik" class="tv-list flex-grow"></div>
            </section>

            <section class="tv-card bg-white border border-gray-100 rounded-xl p-3 md:p-4 flex flex-col min-h-0">
                <h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-2"><span class="text-gray-500">📉</span> Bottom Realisasi Cabang</h3>
                <div id="list_realisasi_bottom" class="tv-list flex-grow"></div>
            </section>
        </div>
    </div>
</div>

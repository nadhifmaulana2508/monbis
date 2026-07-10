<div class="tv-fit">
    <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full">
        <div class="flex items-center gap-2.5 md:gap-3 mb-4 md:mb-5 border-b border-gray-100 pb-3 md:pb-4 shrink-0">
            <div class="bg-purple-100 p-1.5 md:p-2 rounded-lg"><span class="text-xl md:text-3xl">DPK</span></div>
            <div>
                <h2 class="text-lg md:text-2xl font-extrabold text-gray-900 tracking-tight">Deposito</h2>
                <p class="text-[10px] md:text-sm text-gray-500 font-medium">Rekapitulasi deposito posisi actual</p>
            </div>
        </div>

        <section class="tv-card bg-gray-50 border border-gray-100 rounded-xl p-3 md:p-4 flex flex-col flex-grow min-h-0">
            <div class="grid md:grid-cols-4 gap-4 min-h-0">
                <div class="min-h-0"><h4 class="font-bold text-gray-700 mb-2 text-[9px] uppercase tracking-wider">Top Saldo Deposito</h4><div id="list_dep_saldo_top" class="tv-list"></div></div>
                <div class="min-h-0"><h4 class="font-bold text-yellow-700 mb-2 text-[9px] uppercase tracking-wider">Bottom Saldo Deposito</h4><div id="list_dep_saldo_bot" class="tv-list"></div></div>
                <div class="min-h-0"><h4 class="font-bold text-green-700 mb-2 text-[9px] uppercase tracking-wider">Deposito Baru Masuk</h4><div id="list_dep_baru" class="tv-list"></div></div>
                <div class="min-h-0"><h4 class="font-bold text-red-700 mb-2 text-[9px] uppercase tracking-wider">Deposito Keluar</h4><div id="list_dep_cair" class="tv-list"></div></div>
            </div>
        </section>
    </div>
</div>

<div class="tv-fit tv-compact-gap flex flex-col gap-4 md:gap-6">
    <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col basis-[52%] min-h-0">
        <div class="flex items-center gap-2.5 md:gap-3 mb-4 md:mb-6 border-b border-gray-100 pb-3 md:pb-4">
            <div class="bg-yellow-100 p-1.5 md:p-2 rounded-lg"><span class="text-xl md:text-3xl">🏆</span></div>
            <div>
                <h2 class="text-lg md:text-2xl font-extrabold text-gray-900 tracking-tight">5 Best Performance</h2>
                <p class="text-[10px] md:text-sm text-gray-500 font-medium">Jajaran Cabang dan Pegawai Terbaik</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 flex-grow min-h-0">
            <div class="space-y-4 md:space-y-5 min-h-0 overflow-hidden">
                <div><h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><span class="text-blue-500">📈</span> Top Realisasi Cabang</h3><div id="best_realisasi" class="tv-list space-y-3"></div></div>
                <div class="pt-3 md:pt-4 border-t border-dashed border-gray-200"><h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><span class="text-orange-500">🥇</span> Top Realisasi AO</h3><div id="best_realisasi_ao" class="tv-list space-y-3"></div></div>
            </div>
            <div class="space-y-4 md:space-y-5 min-h-0 overflow-hidden">
                <div><h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><span class="text-red-500">🛡️</span> Top NPL Terendah (Terbaik)</h3><div id="best_npl" class="tv-list space-y-3"></div></div>
                <div class="pt-3 md:pt-4 border-t border-dashed border-gray-200"><h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><span class="text-yellow-500">🏆</span> Top Repayment Rate (RR)</h3><div id="best_rr" class="tv-list space-y-3"></div></div>
            </div>
            <div class="space-y-4 md:space-y-5 min-h-0 overflow-hidden">
                <h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><span class="text-teal-500">🎉</span> NPL Membaik (Penurunan)</h3>
                <div id="best_npl_turun" class="tv-list space-y-3"></div>
            </div>
            <div class="tv-card bg-[#1e293b] p-4 md:p-5 rounded-xl md:rounded-2xl shadow-md border border-gray-700 min-h-0 overflow-hidden">
                <h3 class="font-bold text-yellow-300 mb-3 md:mb-4 text-sm md:text-lg border-b border-gray-600 pb-2 md:pb-3 flex items-center gap-1.5 md:gap-2"><span class="text-lg md:text-2xl">💡</span> Key Insights</h3>
                <div id="dynamic_insights" class="space-y-3 md:space-y-4 text-[11px] md:text-sm text-gray-300 font-medium"></div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-4 md:gap-6 flex-grow min-h-0">
        <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col">
            <div class="flex items-center gap-2.5 md:gap-3 mb-4 border-b border-gray-100 pb-3">
                <div class="bg-red-100 p-1.5 md:p-2 rounded-lg"><span class="text-xl md:text-3xl">🚨</span></div>
                <div>
                    <h2 class="text-lg md:text-2xl font-extrabold text-gray-900 tracking-tight">Kredit Non Perform</h2>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div><h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><span class="text-red-500">🚨</span> Top NPL Terburuk</h3><div id="list_npl_top" class="tv-list space-y-3"></div></div>
                <div><h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><span class="text-orange-500">⚠️</span> NPL Memburuk</h3><div id="list_npl_naik" class="tv-list space-y-3"></div></div>
            </div>
        </div>

        <div class="tv-card bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100 flex flex-col">
            <div class="flex items-center gap-2.5 md:gap-3 mb-4 border-b border-gray-100 pb-3">
                <div class="bg-purple-100 p-1.5 md:p-2 rounded-lg"><span class="text-xl md:text-3xl">💰</span></div>
                <div>
                    <h2 class="text-lg md:text-2xl font-extrabold text-gray-900 tracking-tight">Dana Pihak Ketiga (DPK)</h2>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <h3 class="font-extrabold text-gray-800 mb-3 tracking-tight flex items-center gap-1.5 text-[13px] md:text-lg"><span>🏦</span> Deposito (H-1)</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div><h3 class="font-bold text-gray-700 mb-2 text-[9px] uppercase tracking-wider">Top Saldo Deposito</h3><div id="list_dep_saldo_top" class="tv-list space-y-2"></div></div>
                        <div><h3 class="font-bold text-green-700 mb-2 text-[9px] uppercase tracking-wider">Deposito Baru</h3><div id="list_dep_baru" class="tv-list space-y-2"></div></div>
                    </div>
                </div>
                <div class="border-t border-dashed border-gray-200"></div>
                <div>
                    <h3 class="font-extrabold text-gray-800 mb-3 tracking-tight flex items-center gap-1.5 text-[13px] md:text-lg"><span>💳</span> Tabungan (H-1)</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div><h3 class="font-bold text-gray-700 mb-2 text-[9px] uppercase tracking-wider">Top Saldo Tabungan</h3><div id="list_tab_saldo_top" class="tv-list space-y-2"></div></div>
                        <div><h3 class="font-bold text-blue-700 mb-2 text-[9px] uppercase tracking-wider">Tabungan Baru</h3><div id="list_tab_baru" class="tv-list space-y-2"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

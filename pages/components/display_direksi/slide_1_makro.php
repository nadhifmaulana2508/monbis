<div class="tv-fit tv-compact-gap flex flex-col gap-4 md:gap-6">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4 shrink-0">
        <div class="tv-card bg-white p-3.5 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden col-span-2 md:col-span-1">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500 rounded-l-2xl"></div>
            <p class="text-[9px] md:text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5 md:mb-1 truncate">Total Baki Debet (OS)</p>
            <h3 id="kpi_os" class="text-lg md:text-xl xl:text-2xl font-black text-gray-900 tracking-tight whitespace-nowrap mb-1.5 md:mb-2.5">Rp 0</h3>
            <div id="kpi_os_pill"></div>
        </div>
        <div class="tv-card bg-white p-3.5 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden col-span-1">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-cyan-500 rounded-l-2xl"></div>
            <p class="text-[9px] md:text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5 md:mb-1 truncate">Saldo Bank</p>
            <h3 id="kpi_saldobank" class="text-lg md:text-xl xl:text-2xl font-black text-cyan-600 tracking-tight whitespace-nowrap mb-1.5 md:mb-2.5">Rp 0</h3>
            <div id="kpi_saldobank_pill"></div>
        </div>
        <div class="tv-card bg-white p-3.5 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden col-span-1">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-red-500 rounded-l-2xl"></div>
            <p class="text-[9px] md:text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5 md:mb-1 truncate">Total OSC NPL</p>
            <h3 id="kpi_npl" class="text-lg md:text-xl xl:text-2xl font-black text-red-600 tracking-tight whitespace-nowrap mb-1.5 md:mb-2.5">Rp 0</h3>
            <div id="kpi_npl_pill"></div>
        </div>
        <div class="tv-card bg-white p-3.5 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden col-span-1">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-green-500 rounded-l-2xl"></div>
            <p class="text-[9px] md:text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5 md:mb-1 truncate">Repayment Rate (RR)</p>
            <h3 id="kpi_rr" class="text-lg md:text-xl xl:text-2xl font-black text-green-600 tracking-tight whitespace-nowrap mb-1.5 md:mb-2.5">Rp 0</h3>
            <div id="kpi_rr_pill"></div>
        </div>
        <div class="tv-card bg-white p-3.5 md:p-4 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden col-span-1 md:col-span-1">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-purple-500 rounded-l-2xl"></div>
            <p class="text-[9px] md:text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5 md:mb-1 truncate">Total DPK (Actual)</p>
            <h3 id="kpi_dpk" class="text-lg md:text-xl xl:text-2xl font-black text-purple-700 tracking-tight whitespace-nowrap mb-1.5 md:mb-2.5">Rp 0</h3>
            <div id="kpi_dpk_pill"></div>
        </div>
    </div>

    <div class="grid lg:grid-cols-12 gap-3 md:gap-4 flex-grow min-h-0">
        <div class="tv-card bg-white p-4 md:p-5 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 lg:col-span-4 flex flex-col">
            <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 mb-3 md:mb-4 border-b border-gray-100 pb-2 text-[13px] md:text-sm"><span class="text-indigo-500">Bank</span> Ringkasan Keuangan (Actual)</h3>
            <div class="tv-list space-y-3">
                <div id="makro_aset" class="flex justify-between items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                    <div><p class="text-[10px] text-gray-500 font-bold uppercase">Total Aset</p><p class="text-sm font-black text-gray-800" id="txt_makro_aset">Rp 0</p></div>
                    <div class="text-right shrink-0 pl-3" id="delta_makro_aset"></div>
                </div>
                <div id="makro_laba" class="flex justify-between items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                    <div><p class="text-[10px] text-gray-500 font-bold uppercase">Laba Rugi</p><p class="text-sm font-black text-gray-800" id="txt_makro_laba">Rp 0</p></div>
                    <div class="text-right shrink-0 pl-3" id="delta_makro_laba"></div>
                </div>
                <div id="makro_pendapatan" class="flex justify-between items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                    <div><p class="text-[10px] text-gray-500 font-bold uppercase">Pendapatan</p><p class="text-sm font-black text-gray-800" id="txt_makro_pendapatan">Rp 0</p></div>
                    <div class="text-right shrink-0 pl-3" id="delta_makro_pendapatan"></div>
                </div>
                <div id="makro_biaya" class="flex justify-between items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                    <div><p class="text-[10px] text-gray-500 font-bold uppercase">Biaya Beban</p><p class="text-sm font-black text-gray-800" id="txt_makro_biaya">Rp 0</p></div>
                    <div class="text-right shrink-0 pl-3" id="delta_makro_biaya"></div>
                </div>
            </div>
        </div>

        <div class="tv-card bg-white p-4 md:p-5 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 lg:col-span-5 flex flex-col">
            <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 mb-3 md:mb-4 border-b border-gray-100 pb-2 text-[13px] md:text-sm"><span class="text-emerald-500">Rasio</span> Indikator Kesehatan Bank (Actual)</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2.5 md:gap-3">
                <div class="bg-gray-50 border border-gray-100 p-2.5 rounded-lg flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1" title="BOPO = Biaya Operasional / Pendapatan Operasional x 100%">BOPO</span>
                    <span class="text-lg font-black" id="txt_rasio_bopo">0%</span>
                    <div id="delta_rasio_bopo" class="mt-1"></div>
                </div>
                <div class="bg-gray-50 border border-gray-100 p-2.5 rounded-lg flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1" title="LDR = Total Kredit / Total DPK x 100%">LDR</span>
                    <span class="text-lg font-black" id="txt_rasio_ldr">0%</span>
                    <div id="delta_rasio_ldr" class="mt-1"></div>
                </div>
                <div class="bg-gray-50 border border-gray-100 p-2.5 rounded-lg flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1" title="CASA = Tabungan / Total DPK x 100%">CASA Ratio</span>
                    <span class="text-lg font-black" id="txt_rasio_casa">0%</span>
                    <div id="delta_rasio_casa" class="mt-1"></div>
                </div>
                <div class="bg-gray-50 border border-gray-100 p-2.5 rounded-lg flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1" title="ROA = Laba Disetahunkan / Total Aset x 100%">ROA</span>
                    <span class="text-lg font-black" id="txt_rasio_roa">0%</span>
                    <div id="delta_rasio_roa" class="mt-1"></div>
                </div>
                <div class="bg-gray-50 border border-gray-100 p-2.5 rounded-lg flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1" title="ROE = Laba Disetahunkan / Total Ekuitas x 100%">ROE</span>
                    <span class="text-lg font-black" id="txt_rasio_roe">0%</span>
                    <div id="delta_rasio_roe" class="mt-1"></div>
                </div>
                <div class="bg-gray-50 border border-gray-100 p-2.5 rounded-lg flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-gray-500 uppercase tracking-wider mb-1" title="Cash Ratio = Alat Likuid / Kewajiban Segera x 100%">Cash Ratio</span>
                    <span class="text-lg font-black" id="txt_rasio_cash">0%</span>
                    <div id="delta_rasio_cash" class="mt-1"></div>
                </div>
            </div>
        </div>

        <div class="tv-card bg-white p-4 md:p-5 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 lg:col-span-3 flex flex-col">
            <h3 class="font-bold text-gray-800 flex items-center gap-1.5 md:gap-2 mb-3 md:mb-4 border-b border-gray-100 pb-2 text-[13px] md:text-sm"><span class="text-red-500">Biaya</span> Top 5 Beban Biaya (Actual)</h3>
            <div id="box_top_biaya" class="tv-list space-y-2 md:space-y-3 flex-grow"></div>
        </div>
    </div>
</div>

<div class="relative z-20 flex-none mb-3 md:mb-4 w-full bg-white p-2 md:p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3 shrink-0">
    
    <div class="flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
        <h1 class="text-base md:text-xl font-extrabold text-slate-800 flex items-center gap-2 whitespace-nowrap">
            <span class="p-1.5 md:p-2 bg-blue-600 rounded-lg text-white shadow-sm shrink-0">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </span>
            Realisasi & Growth
            
            <div class="relative group cursor-help ml-1">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-500 hover:text-blue-700 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                
                <div class="fixed inset-x-4 top-24 md:absolute md:inset-x-auto md:left-0 md:top-full md:mt-2 md:w-[360px] bg-white border border-slate-200 shadow-2xl rounded-xl p-4 hidden group-hover:flex flex-col gap-2 z-[9999] text-xs font-normal text-slate-600 whitespace-normal">
                    <div class="font-bold text-slate-800 mb-1 border-b pb-1 text-sm text-center md:text-left">💡 Panduan Kamus Kolom</div>
                    <p><b>Realisasi Baru:</b> Pencairan kredit baru pada range tanggal data terpilih.</p>
                    <p><b>Restrukturisasi:</b> Kredit yang dilakukan penataan kembali (rescheduling/reconditioning) akibat penurunan kemampuan bayar debitur.</p>
                    <p><b>Run Off:</b> Penurunan baki debet yang disebabkan oleh pembayaran angsuran murni maupun pelunasan dini oleh debitur.</p>
                    <div class="mt-2 bg-blue-50 border border-blue-200 p-2 rounded-lg text-[10.5px] leading-relaxed text-center md:text-left">
                        <b class="text-blue-800">Formula Growth Net:</b><br>
                        (Realisasi Baru + Realisasi Restrukturisasi) - Total Run Off
                    </div>
                </div>
            </div>
        </h1>
        
        <button type="button" onclick="toggleFilter('filterWrapperReal')" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] whitespace-nowrap ml-2 shrink-0">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> Filter
        </button>
    </div>

    <div id="filterWrapperReal" class="hidden xl:flex w-full xl:w-auto flex-1 min-w-0 justify-end transition-all duration-300 shrink-0 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
        <form id="formFilterReal" class="flex flex-row flex-wrap xl:flex-nowrap items-end gap-2 md:gap-2.5 w-full xl:w-auto" onsubmit="event.preventDefault();">
            
            <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[120px]">
                <label class="lbl">CLOSING (M-1)</label>
                <input type="date" id="closing_date" class="inp font-bold text-slate-700 cursor-pointer" required onclick="this.showPicker()" onchange="fetchRekap()">
            </div>
            <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[120px]">
                <label class="lbl">HARIAN (ACTUAL)</label>
                <input type="date" id="harian_date" class="inp font-bold text-slate-700 cursor-pointer" required onclick="this.showPicker()" onchange="fetchRekap()">
            </div>
            <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[160px]">
                <label class="lbl">AREA / CABANG</label>
                <select id="opt_area" class="inp font-bold text-slate-700 truncate" onchange="updateFilterUI()">
                    <option value="ALL">ALL KONSOLIDASI</option>
                </select>
            </div>
            <div class="field flex-1 min-w-[100px] xl:w-[150px] xl:max-w-[200px]">
                <label id="lbl_sub" class="lbl">KORWIL</label>
                <select id="opt_sub_main" class="inp font-bold text-slate-700 truncate" onchange="fetchRekap()">
                    <option value="ALL">ALL KORWIL</option>
                </select>
            </div>
            
            <button type="button" onclick="exportExcelRekap()" class="btn-icon w-[32px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shrink-0 ml-auto xl:ml-0 mt-2 xl:mt-0" title="Download Excel">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></line></svg>
            </button>
        </form>
    </div>
</div>
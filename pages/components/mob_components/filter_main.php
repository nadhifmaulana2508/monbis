<div class="flex-none mb-3 md:mb-4 flex flex-col justify-between items-start gap-3 w-full shrink-0">
    <div class="flex items-center justify-between w-full shrink-0">
        <div class="flex flex-col gap-1.5 w-full">
            <h1 class="text-lg md:text-2xl font-bold text-slate-800 flex items-center gap-1.5 md:gap-2 mb-0.5">
                <span class="p-1 md:p-2.5 bg-blue-600 rounded-lg text-white shadow-sm">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
                MOB (Month Of Booking)
            </h1>
            <p class="text-[9px] md:text-xs text-rose-600 font-bold italic ml-8 md:ml-[42px] leading-tight">
                *Geser tabel ke kanan untuk data lengkap. Klik nominal untuk detail.
            </p>
        </div>
        
        <button type="button" onclick="toggleFilter('filterWrapperMob')" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] md:text-xs whitespace-nowrap ml-2 shrink-0">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Filter
        </button>
    </div>

    <div id="filterWrapperMob" class="hidden xl:flex w-full mt-2 xl:mt-0 transition-all duration-300 shrink-0">
        <!-- Flex wrap ditambahkan agar kolom yang banyak bisa turun ke baris bawah saat layar kecil -->
        <form id="formFilterMob" class="flex flex-row flex-wrap xl:flex-nowrap items-end gap-2 md:gap-3 bg-white p-3 rounded-xl border border-slate-200 shadow-sm w-full" onsubmit="event.preventDefault(); fetchRekapMob();">
            
            <div class="field shrink-0 w-[130px] md:w-[150px]">
                <label class="lbl text-blue-700">POSISI DATA</label>
                <input type="date" id="harian_date_mob" class="inp w-full text-[10px] md:text-xs font-semibold h-[34px] md:h-[38px] cursor-pointer" required onclick="try{this.showPicker()}catch(e){}">
            </div>

            <div class="field shrink-0 w-[130px] md:w-[150px]">
                <label class="lbl text-slate-600">REKAP BY</label>
                <select id="opt_rekap_by" class="inp text-[10px] md:text-xs font-semibold h-[34px] md:h-[38px] cursor-pointer w-full">
                    <option value="bulan">Bulan Realisasi</option>
                    <option value="ao">AO Kredit</option>
                    <option value="kankas">Kantor Kas</option>
                </select>
            </div>

            <div class="w-full md:w-px h-px md:h-8 bg-slate-200 shrink-0 mx-0 xl:mx-1 mb-2 md:mb-1 xl:block"></div>

            <div class="field shrink-0 w-[130px] md:w-[180px]">
                <label class="lbl text-slate-600">CABANG / KORWIL</label>
                <select id="opt_kantor_mob" class="inp text-[10px] md:text-xs font-semibold h-[34px] md:h-[38px] cursor-pointer w-full" onchange="fetchRekapMob()">
                    <option value="">KONSOLIDASI (SEMUA)</option>
                </select>
            </div>

            <!-- Custom Searchable Dropdown KANKAS -->
            <div class="field relative flex-1 min-w-[150px]">
                <label class="lbl text-slate-600">KANKAS</label>
                <input type="hidden" id="val_kankas_main" value="">
                <input type="text" id="search_kankas_main" class="inp w-full text-[10px] md:text-xs font-semibold h-[34px] md:h-[38px] cursor-text placeholder-slate-400" placeholder="Semua Kankas..." autocomplete="off">
                <div id="list_kankas_main" class="search-dropdown-list custom-scrollbar"></div>
            </div>

            <!-- Custom Searchable Dropdown AO -->
            <div class="field relative flex-1 min-w-[150px]">
                <label class="lbl text-slate-600">AO KREDIT</label>
                <input type="hidden" id="val_ao_main" value="">
                <input type="text" id="search_ao_main" class="inp w-full text-[10px] md:text-xs font-semibold h-[34px] md:h-[38px] cursor-text placeholder-slate-400" placeholder="Semua AO..." autocomplete="off">
                <div id="list_ao_main" class="search-dropdown-list custom-scrollbar"></div>
            </div>
            
            <div class="flex items-center gap-1.5 w-full md:w-auto shrink-0 h-[34px] md:h-[38px] mt-2 md:mt-0">
                <button type="submit" class="btn-icon flex-1 md:flex-none px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm text-[10px] md:text-xs font-bold uppercase tracking-wider h-full" title="Cari Data">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" class="mr-1.5"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    CARI
                </button>
                <button type="button" onclick="exportExcelRekapMob()" class="btn-icon h-full w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm" title="Download Excel">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></line></svg>
                </button>
            </div>
        </form>
    </div>
</div>
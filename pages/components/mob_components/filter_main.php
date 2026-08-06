<div class="relative z-20 flex-none mb-3 md:mb-4 w-full bg-white p-2.5 md:p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3 shrink-0">
    
    <div class="flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
        <h1 class="text-base md:text-xl font-extrabold text-slate-800 flex items-center gap-2 whitespace-nowrap">
            <span class="p-1.5 md:p-2 bg-blue-600 rounded-lg text-white shadow-sm shrink-0">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </span>
            MOB <span class="hidden md:inline">(Month Of Booking)</span>
            
            <div class="relative group cursor-help ml-1">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-500 hover:text-blue-700 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                
                <div class="absolute left-0 top-full mt-2 w-[260px] md:w-[320px] bg-white border border-slate-200 shadow-2xl rounded-xl p-3 md:p-4 hidden group-hover:flex flex-col gap-2 z-50 text-xs font-normal text-slate-600 whitespace-normal">
                    <div class="font-bold text-slate-800 mb-1 border-b pb-1 text-sm">💡 Informasi MOB</div>
                    <p class="mb-1.5"><b>Month Of Booking (MOB):</b> Memantau pergerakan <i>Days Past Due</i> (DPD) atau hari menunggak nasabah berdasarkan bulan pencairan kredit.</p>
                    <div class="flex flex-col gap-1.5 mb-2">
                        <div class="flex items-start gap-2">
                            <span class="w-3 h-3 rounded bg-emerald-100 border border-emerald-300 shrink-0 mt-0.5"></span>
                            <p><b class="text-emerald-700">DPD 0 (Lancar):</b> Angsuran bulan ini sudah dibayar atau belum masuk tanggal jatuh tempo.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-400 shrink-0 mt-0.5"></span>
                            <p><b class="text-yellow-700">DPD 1 - 14:</b> Menunggak awal. Lakukan <i>reminder</i> atau penagihan ringan.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-3 h-3 rounded bg-red-100 border border-red-400 shrink-0 mt-0.5"></span>
                            <p><b class="text-red-700">DPD > 14 (Migrasi):</b> Menunggak lanjut kualitas memburuk. <b class="uppercase">Segera lakukan penagihan intensif!</b></p>
                        </div>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 p-2 rounded-lg text-[10.5px] leading-relaxed">
                        <b class="text-amber-800">⚠️ Catatan Status Aman:</b><br>
                        Nasabah bersaldo <b>DPD 0 (Lancar)</b> belum tentu sepenuhnya "Aman" jika <b>tanggal jatuh tempo angsurannya di bulan berjalan belum terlewati</b>. Masih ada potensi migrasi menunggak. Pastikan memantau hingga tanggal jatuh tempo terlewati.
                    </div>
                </div>
            </div>
        </h1>
        
        <button type="button" onclick="toggleFilter('filterWrapperMob')" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] md:text-xs whitespace-nowrap ml-2 shrink-0">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter
        </button>
    </div>

    <div id="filterWrapperMob" class="hidden xl:flex w-full xl:w-auto flex-1 min-w-0 justify-end transition-all duration-300 shrink-0 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
        <form id="formFilterMob" class="flex flex-row flex-wrap xl:flex-nowrap items-end gap-2 md:gap-2.5 w-full xl:w-auto" onsubmit="event.preventDefault();">
            
            <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[110px]">
                <label class="lbl">POSISI DATA</label>
                <input type="date" id="harian_date_mob" class="inp text-[10px] md:text-xs font-bold h-[34px] md:h-[36px] cursor-pointer text-slate-700" title="Posisi Data" required onclick="try{this.showPicker()}catch(e){}" onchange="fetchRekapMob()">
            </div>

            <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[150px]">
                <label class="lbl">AREA / CABANG</label>
                <select id="opt_area" class="inp text-[10px] md:text-xs font-bold text-slate-700 h-[34px] md:h-[36px] cursor-pointer truncate" onchange="updateFilterUI()" title="Pilih Area (Korwil / Cabang)">
                    <option value="ALL">ALL KONSOLIDASI</option>
                </select>
            </div>

            <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[130px]">
                <label class="lbl">TIPE SALDO</label>
                <select id="tipe_saldo_mob" class="inp text-[10px] md:text-xs font-bold text-slate-700 h-[34px] md:h-[36px] cursor-pointer truncate" onchange="fetchRekapMob()" title="Tipe saldo outstanding">
                    <option value="baki_debet">BAKI DEBET</option>
                    <option value="saldo_bank">SALDO BANK</option>
                </select>
            </div>

            <div class="field flex-1 min-w-[100px] xl:w-[130px] xl:max-w-[160px]">
                <label id="lbl_sub" class="lbl text-slate-600">KORWIL</label>
                <select id="opt_sub_main" class="inp text-[10px] md:text-xs font-bold text-slate-700 h-[34px] md:h-[36px] cursor-pointer truncate" onchange="fetchRekapMob()">
                    <option value="ALL">ALL KORWIL</option>
                    <option value="SEMARANG">SEMARANG</option>
                    <option value="SOLO">SOLO</option>
                    <option value="BANYUMAS">BANYUMAS</option>
                    <option value="PEKALONGAN">PEKALONGAN</option>
                </select>
            </div>

            <div class="field flex-1 min-w-[100px] xl:w-[150px] xl:max-w-[200px]">
                <label class="lbl text-blue-700">AO KREDIT</label>
                <select id="opt_ao_main" class="inp text-[10px] md:text-xs font-bold text-blue-800 bg-blue-50/50 border-blue-200 h-[34px] md:h-[36px] cursor-pointer truncate disabled:bg-slate-100 disabled:text-slate-400 disabled:border-slate-200" onchange="fetchRekapMob()" disabled>
                    <option value="ALL">PILIH CABANG DULU</option>
                </select>
            </div>
            
            <div id="mobExportWrap" class="relative shrink-0 ml-auto xl:ml-0 mt-2 xl:mt-0">
                <button type="button" onclick="toggleExportMobMenu(event)" class="btn-icon h-[34px] md:h-[36px] w-[38px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm" title="Download Excel">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></line></svg>
                </button>
                <div id="mobExportMenu" class="hidden absolute right-0 top-full mt-2 w-36 bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden z-[80]">
                    <button type="button" onclick="downloadMobExcelChoice('rekap')" class="w-full px-3 py-2 text-left text-[11px] md:text-xs font-extrabold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Rekap</button>
                    <button type="button" onclick="downloadMobExcelChoice('nominatif')" class="w-full px-3 py-2 text-left text-[11px] md:text-xs font-extrabold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 border-t border-slate-100">Nominatif</button>
                </div>
            </div>
        </form>
    </div>
</div>

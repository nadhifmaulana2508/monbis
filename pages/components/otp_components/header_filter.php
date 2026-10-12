<div class="otp-card relative z-[60] flex-none mb-3 md:mb-4 w-full bg-white p-3 rounded-lg border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3 shrink-0">
    <div class="otp-title-wrap flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
        <div class="flex flex-col gap-0.5 md:gap-1 min-w-0 flex-1">
          <h1 class="text-base md:text-lg font-bold flex items-center gap-2 text-slate-800 whitespace-nowrap">
            <span class="p-1.5 bg-blue-600 text-white rounded shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v10"></path></svg>
            </span>
            <span id="otpTitle" class="otp-title truncate">OTP - ALL</span>
            <div class="relative group cursor-help ml-1 otp-info-root">
              <button type="button" aria-label="Informasi OTP" onclick="toggleOtpHelp(event)" class="inline-flex items-center justify-center rounded-full mt-0.5">
                <svg class="w-4 h-4 text-slate-400 hover:text-blue-600 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
              </button>
              
              <div id="otpHelpPanel" class="otp-help-panel fixed md:absolute inset-x-4 md:inset-x-auto md:left-0 md:top-full mt-2 mx-auto md:mx-0 w-auto max-w-[calc(100vw-32px)] md:w-[350px] max-h-[60vh] overflow-y-auto custom-scrollbar bg-white border border-slate-200 shadow-xl rounded-lg p-4 hidden group-hover:flex flex-col gap-2 z-[100] text-xs font-normal text-slate-600 whitespace-normal">
                <div class="font-bold text-slate-800 mb-1 border-b pb-2 text-sm shrink-0">Informasi OTP</div>
                <p><b>OTP</b> adalah monitoring ketepatan bayar debitur sesuai tanggal jatuh tempo sampai posisi harian/aktual.</p>
                <div class="grid grid-cols-1 gap-1.5 text-[10.5px] leading-relaxed">
                  <div class="rounded border border-slate-200 bg-slate-50 p-2"><b>Target M-1</b>: outstanding/rekening jatuh tempo dari closing bulan sebelumnya.</div>
                  <div class="rounded border border-slate-200 bg-slate-50 p-2"><b>OTP Lancar</b>: rekening yang masih lancar pada posisi aktual.</div>
                  <div class="rounded border border-slate-200 bg-slate-50 p-2"><b>Ditagih</b>: rekening prioritas yang belum memenuhi pembayaran.</div>
                </div>
                <p class="font-bold text-slate-800 border-t pt-2 mt-1"><b>% OTP (Baris Tanggal)</b> = (Lancar + Lunas + Angsuran) / Target Tanggal Terkait.</p>
                <p class="font-bold text-slate-800 border-t pt-2 mt-1"><b>% OTP TOTAL (Baris Atas)</b> = Total Pencapaian Kumulatif / Total Target Kumulatif s.d Hari Berjalan Aktual.</p>
                
                <div class="font-bold text-slate-800 border-t pt-2 mt-1 mb-0.5 shrink-0">Indikator Warna % OTP:</div>
                <div class="flex flex-col gap-1 text-[11px] shrink-0">
                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-emerald-100 border border-emerald-200"></span> <span class="font-semibold text-emerald-700">Optimal</span> (>= 90%)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-amber-100 border border-amber-200"></span> <span class="font-semibold text-amber-700">Monitoring</span> (70% - 89%)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded bg-rose-100 border border-rose-200"></span> <span class="font-semibold text-rose-700">Atensi</span> (&lt; 70%)</div>
                </div>
              </div>
            </div>
          </h1>
        </div>

        <button type="button" onclick="toggleMainFilter()" class="xl:hidden h-[32px] px-3 bg-white border border-slate-200 text-slate-700 rounded flex items-center gap-1.5 shadow-sm transition font-bold text-xs whitespace-nowrap ml-auto shrink-0 hover:bg-slate-50">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
            <span class="hidden sm:inline">Filter</span>
        </button>
    </div>

    <div id="filterWrapperMain" class="filter-transition w-full xl:w-auto xl:flex-1 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
        <form id="formFilterRR" class="w-full flex-1 min-w-0" onsubmit="event.preventDefault(); fetchRekapRR();">
            <div class="flex flex-wrap xl:flex-nowrap items-end gap-2 w-full justify-end">
                
                <div class="field shrink-0 w-[calc(50%-4px)] sm:w-[115px]">
                    <label class="lbl">CLOSING (M-1)</label>
                    <input type="date" id="closing_date" onchange="fetchRekapRR()" class="inp w-full" required onclick="try{this.showPicker()}catch(e){}">
                </div>
                <div class="field shrink-0 w-[calc(50%-4px)] sm:w-[115px]">
                    <label class="lbl">ACTUAL (HARIAN)</label>
                    <input type="date" id="harian_date" onchange="fetchRekapRR()" class="inp w-full" required onclick="try{this.showPicker()}catch(e){}">
                </div>
                
                <div class="w-px h-6 bg-slate-200 shrink-0 mx-0.5 hidden xl:block mb-1.5"></div>
                
                <div class="field shrink-0 w-[calc(50%-4px)] sm:flex-1 sm:min-w-[140px] xl:max-w-[180px]">
                    <label class="lbl">CABANG</label>
                    <select id="opt_kantor" class="inp w-full truncate" onchange="handleCabangChangeOtp()">
                        <option value="">Loading...</option>
                    </select>
                </div>
                <div class="field shrink-0 w-[calc(50%-4px)] sm:flex-1 sm:min-w-[120px] xl:max-w-[140px]">
                    <label id="lbl_sub_otp" class="lbl">KORWIL</label>
                    <select id="opt_sub_otp" class="inp w-full truncate" onchange="fetchRekapRR()">
                        <option value="">ALL KORWIL</option>
                    </select>
                </div>
                
                <div class="field flex-1 min-w-[70px] sm:w-[100px] sm:flex-none">
                    <label class="lbl">DPD BUCKET</label>
                    <select id="opt_dpd_bucket" class="inp w-full" onchange="fetchRekapRR()">
                        <option value="all">ALL</option>
                        <option value="dpd0">DPD 0</option>
                        <option value="dpd1-30">DPD 1-30</option>
                    </select>
                </div>
                <div class="field flex-1 min-w-[80px] sm:w-[140px] xl:max-w-[160px] sm:flex-none">
                    <label class="lbl">AO KREDIT</label>
                    <select id="opt_ao_otp" class="inp w-full truncate disabled:bg-slate-50 disabled:text-slate-400" onchange="fetchRekapRR()" disabled>
                        <option value="">PILIH CABANG DULU</option>
                    </select>
                </div>
                <div class="field shrink-0 w-[44px] sm:w-[48px]">
                    <label class="lbl text-center w-full">KPP</label>
                    <div class="flex items-center justify-center h-[34px] px-2 bg-slate-50 border border-slate-200 rounded cursor-pointer hover:bg-slate-100 transition" onclick="document.getElementById('chk_127').click()">
                        <input type="checkbox" id="chk_127" class="w-3.5 h-3.5 text-blue-600 bg-white border-slate-300 rounded cursor-pointer" onclick="event.stopPropagation()" onchange="fetchRekapRR()">
                    </div>
                </div>
                <div class="field shrink-0 w-[40px]">
                    <label class="lbl opacity-0 hidden sm:block select-none">&nbsp;</label>
                    <button type="button" onclick="exportExcelRekapRR()" class="btn-icon h-[34px] w-full bg-emerald-600 hover:bg-emerald-700 text-white rounded shadow-sm shrink-0 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline></svg>
                    </button>
                </div>
                
            </div>
        </form>
    </div>
</div>
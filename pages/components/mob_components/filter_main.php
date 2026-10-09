<div id="mobHeaderCard" class="relative z-20 flex-none mb-3 md:mb-4 w-full bg-white p-2.5 md:p-3 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between gap-3 shrink-0">
    <div class="min-w-0 flex items-center gap-2 md:gap-3">
        <span class="flex h-8 w-8 md:h-9 md:w-9 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm shrink-0" aria-hidden="true">
            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
        </span>
        <div class="min-w-0 flex items-center gap-1.5 md:gap-2">
            <h1 class="truncate text-base md:text-xl font-extrabold leading-tight tracking-tight text-slate-800">MOB / FPD</h1>
            <div class="relative group shrink-0">
                <button type="button" class="flex h-4 w-4 md:h-5 md:w-5 items-center justify-center rounded-full text-blue-500 hover:text-blue-700 transition" aria-label="Informasi MOB" title="Informasi MOB">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                </button>
                <div class="absolute left-0 top-full mt-2 hidden w-[min(360px,calc(100vw-32px))] flex-col gap-2 rounded-xl border border-slate-200 bg-white p-3 text-[10px] leading-relaxed text-slate-600 shadow-2xl group-hover:flex z-50">
                    <strong class="border-b border-slate-200 pb-1 text-xs text-slate-800">💡 Informasi MOB</strong>
                    <p><b>Month Of Booking (MOB):</b> Memantau pergerakan <i>Days Past Due (DPD)</i> atau hari menunggak nasabah berdasarkan plafon pencairan kredit.</p>

                    <div class="space-y-1">
                        <p class="flex gap-1.5"><span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-[3px] border border-emerald-300 bg-emerald-100"></span><span><b class="text-emerald-600">DPD 0 (Lancar):</b> Angsuran bulan ini sudah dibayar atau belum masuk tanggal jatuh tempo.</span></p>
                        <p class="flex gap-1.5"><span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-[3px] border border-amber-300 bg-amber-100"></span><span><b class="text-amber-600">DPD 1 - 14:</b> Menunggak awal. Lakukan <i>reminder</i> atau penagihan ringan.</span></p>
                        <p class="flex gap-1.5"><span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-[3px] border border-red-300 bg-red-100"></span><span><b class="text-red-600">DPD &gt; 14 (Migrasi):</b> Menunggak lanjut kualitas memburuk. <b>SEGERA LAKUKAN PENAGIHAN INTENSIF!</b></span></p>
                    </div>

                    <div class="rounded-lg border border-amber-300 bg-amber-50 p-2 text-amber-900">
                        <p class="font-bold">⚠️ Catatan Status Aman:</p>
                        <p>Nasabah bersaldo <b>DPD 0 (Lancar)</b> belum tentu sepenuhnya “Aman” jika tanggal jatuh tempo angsurannya di bulan berjalan belum terlewati. Masih ada potensi migrasi menunggak. Pastikan memantau hingga tanggal jatuh tempo terlewati.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mobExportWrap" class="relative shrink-0">
        <button type="button" onclick="toggleExportMobMenu(event)" class="btn-icon h-[32px] w-[34px] md:h-[34px] md:w-[36px] rounded-lg bg-emerald-600 text-white shadow-sm hover:bg-emerald-700" title="Download Excel" aria-label="Download Excel">
            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 20h14"></path><path d="M5 16v4"></path><path d="M19 16v4"></path><path d="M12 3v10"></path><path d="m8 9 4 4 4-4"></path></svg>
        </button>
        <div id="mobExportMenu" class="hidden absolute right-0 top-full mt-2 w-36 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl z-[80]">
            <button type="button" onclick="downloadMobExcelChoice('rekap')" class="w-full px-3 py-2 text-left text-[11px] font-extrabold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Rekap</button>
            <button type="button" onclick="downloadMobExcelChoice('nominatif')" class="w-full border-t border-slate-100 px-3 py-2 text-left text-[11px] font-extrabold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Nominatif</button>
        </div>
    </div>
</div>

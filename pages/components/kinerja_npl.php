<div class="dashboard-panel bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100">
  <div class="flex items-center gap-2.5 md:gap-3 mb-4 md:mb-6 border-b border-gray-100 pb-3 md:pb-4">
    <div class="bg-red-100 p-1.5 md:p-2 rounded-lg"><svg class="dashboard-icon h-5 w-5 md:h-7 md:w-7 text-red-600" aria-hidden="true"><use href="#dashboard-icon-alert"></use></svg></div>
    <div>
      <h2 class="dashboard-section-heading text-lg md:text-2xl font-extrabold text-gray-900 tracking-tight">Kredit Non Perform</h2>
      <p class="text-[10px] md:text-sm text-gray-500 font-medium">Peringatan Kinerja & Cabang Terburuk</p>
    </div>
  </div>
  <div class="grid md:grid-cols-3 gap-4 md:gap-6">
    <div><h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><svg class="dashboard-icon h-4 w-4 text-red-500" aria-hidden="true"><use href="#dashboard-icon-alert"></use></svg> Top NPL Terburuk (Highest)</h3><div id="list_npl_top" class="space-y-3"></div></div>
    <div><h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><svg class="dashboard-icon h-4 w-4 text-orange-500" aria-hidden="true"><use href="#dashboard-icon-alert"></use></svg> NPL Memburuk (Naik)</h3><div id="list_npl_naik" class="space-y-3"></div></div>
    <div><h3 class="font-bold text-gray-800 mb-2.5 md:mb-3 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><svg class="dashboard-icon h-4 w-4 text-gray-500" aria-hidden="true"><use href="#dashboard-icon-trend"></use></svg> Bottom Realisasi Cabang</h3><div id="list_realisasi_bottom" class="space-y-3"></div></div>
  </div>
</div>

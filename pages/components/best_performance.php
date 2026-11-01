<div class="dashboard-panel bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100">
  <div class="flex items-center gap-2.5 md:gap-3 mb-4 md:mb-6 border-b border-gray-100 pb-3 md:pb-4">
    <div class="bg-yellow-100 p-1.5 md:p-2 rounded-lg"><svg class="dashboard-icon h-5 w-5 md:h-7 md:w-7 text-yellow-600" aria-hidden="true"><use href="#dashboard-icon-award"></use></svg></div>
    <div>
      <h2 class="dashboard-section-heading text-lg md:text-2xl font-extrabold text-gray-900 tracking-tight">5 Best Performance</h2>
      <p class="text-[10px] md:text-sm text-gray-500 font-medium">Jajaran Cabang dan Pegawai Terbaik</p>
    </div>
  </div>
  
  <!-- STRUKTUR GRID BARU (DIJAMIN SEJAJAR SEMPURNA) -->
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    
    <!-- BLOCK KIRI (Terdiri dari 2 Baris yang Otomatis Menyesuaikan Data Terpanjang) -->
    <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-0">
        
        <!-- BARIS 1 -->
        <div class="pb-4 lg:pb-6">
            <h3 class="font-bold text-gray-800 mb-3 md:mb-4 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><svg class="dashboard-icon h-4 w-4 text-blue-500" aria-hidden="true"><use href="#dashboard-icon-trend"></use></svg> Top Realisasi Cabang</h3>
            <div id="best_realisasi" class="space-y-3"></div>
        </div>
        <div class="pb-4 lg:pb-6 pt-4 md:pt-0 border-t border-dashed border-gray-200 md:border-none">
            <h3 class="font-bold text-gray-800 mb-3 md:mb-4 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><svg class="dashboard-icon h-4 w-4 text-red-500" aria-hidden="true"><use href="#dashboard-icon-shield"></use></svg> Top NPL Terendah (Terbaik)</h3>
            <div id="best_npl" class="space-y-3"></div>
        </div>
        <div class="pb-4 lg:pb-6 pt-4 md:pt-0 border-t border-dashed border-gray-200 md:border-none">
            <h3 class="font-bold text-gray-800 mb-3 md:mb-4 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><svg class="dashboard-icon h-4 w-4 text-teal-500" aria-hidden="true"><use href="#dashboard-icon-trend"></use></svg> NPL Membaik (Penurunan)</h3>
            <div id="best_npl_turun" class="space-y-3"></div>
        </div>

        <!-- GARIS PEMISAH (Lurus Menyambung, Hanya untuk Desktop/Tablet) -->
        <div class="hidden md:block md:col-span-3 border-t border-dashed border-gray-200 pt-4 lg:pt-6"></div>

        <!-- BARIS 2 -->
        <div class="pt-4 md:pt-0 border-t border-dashed border-gray-200 md:border-none">
            <h3 class="font-bold text-gray-800 mb-3 md:mb-4 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><svg class="dashboard-icon h-4 w-4 text-orange-500" aria-hidden="true"><use href="#dashboard-icon-award"></use></svg> Top Realisasi AO</h3>
            <div id="best_realisasi_ao" class="space-y-3"></div>
        </div>
        <div class="pt-4 md:pt-0 border-t border-dashed border-gray-200 md:border-none">
            <h3 class="font-bold text-gray-800 mb-3 md:mb-4 text-[11px] md:text-[13px] flex items-center gap-1.5 md:gap-2"><svg class="dashboard-icon h-4 w-4 text-yellow-500" aria-hidden="true"><use href="#dashboard-icon-award"></use></svg> Top Repayment Rate (RR)</h3>
            <div id="best_rr" class="space-y-3"></div>
        </div>

    </div>

    <!-- BLOCK KANAN (Insight Card) -->
    <div class="lg:col-span-1 mt-4 lg:mt-0">
        <div class="bg-[#1e293b] p-4 md:p-5 rounded-xl md:rounded-2xl shadow-md h-fit border border-gray-700">
           <h3 class="font-bold text-yellow-300 mb-3 md:mb-4 text-sm md:text-lg border-b border-gray-600 pb-2 md:pb-3 flex items-center gap-1.5 md:gap-2"><svg class="dashboard-icon h-5 w-5 md:h-6 md:w-6 text-yellow-300" aria-hidden="true"><use href="#dashboard-icon-lightbulb"></use></svg> Key Insights</h3>
           <div id="dynamic_insights" class="space-y-3 md:space-y-4 text-[11px] md:text-sm text-gray-300 font-medium"></div>
        </div>
    </div>

  </div>
</div>

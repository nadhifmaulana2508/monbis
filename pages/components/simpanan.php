<div class="dashboard-panel bg-white p-4 md:p-6 rounded-xl md:rounded-3xl shadow-sm border border-gray-100">
  <div class="flex items-center gap-2.5 md:gap-3 mb-4 md:mb-6 border-b border-gray-100 pb-3 md:pb-4">
    <div class="bg-purple-100 p-1.5 md:p-2 rounded-lg"><svg class="dashboard-icon h-5 w-5 md:h-7 md:w-7 text-purple-600" aria-hidden="true"><use href="#dashboard-icon-coins"></use></svg></div>
    <div>
      <h2 class="dashboard-section-heading text-lg md:text-2xl font-extrabold text-gray-900 tracking-tight">Simpanan</h2>
      <p class="text-[10px] md:text-sm text-gray-500 font-medium">Rekapitulasi Deposito & Tabungan (<span data-simpanan-date="summary">-</span>)</p>
    </div>
  </div>
  <div class="space-y-6 md:space-y-8">
    
    <div>
      <h3 class="font-extrabold text-gray-800 mb-3 md:mb-4 tracking-tight flex items-center gap-1.5 md:gap-2 text-[13px] md:text-lg"><svg class="dashboard-icon h-4 w-4 text-purple-600" aria-hidden="true"><use href="#dashboard-icon-bank"></use></svg> Deposito (<span data-simpanan-date="deposito">-</span>)</h3>
      <div class="dashboard-savings-grid grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <div><h3 class="font-bold text-gray-700 mb-2 md:mb-3 text-[9px] md:text-xs uppercase tracking-wider">Top Saldo Deposito</h3><div id="list_dep_saldo_top" class="space-y-2 md:space-y-2.5"></div></div>
        <div><h3 class="font-bold text-gray-700 mb-2 md:mb-3 text-[9px] md:text-xs uppercase tracking-wider">Bottom Saldo Deposito</h3><div id="list_dep_saldo_bot" class="space-y-2 md:space-y-2.5"></div></div>
        <div><h3 class="font-bold text-green-700 mb-2 md:mb-3 text-[9px] md:text-xs uppercase tracking-wider">Deposito Baru Masuk</h3><div id="list_dep_baru" class="space-y-2 md:space-y-2.5"></div></div>
        <div><h3 class="font-bold text-red-700 mb-2 md:mb-3 text-[9px] md:text-xs uppercase tracking-wider">Deposito Keluar</h3><div id="list_dep_cair" class="space-y-2 md:space-y-2.5"></div></div>
      </div>
    </div>

    <div class="border-t border-dashed border-gray-200"></div>
    
    <div>
      <h3 class="font-extrabold text-gray-800 mb-3 md:mb-4 tracking-tight flex items-center gap-1.5 md:gap-2 text-[13px] md:text-lg"><svg class="dashboard-icon h-4 w-4 text-blue-600" aria-hidden="true"><use href="#dashboard-icon-wallet"></use></svg> Tabungan (<span data-simpanan-date="tabungan">-</span>)</h3>
      <div class="dashboard-savings-grid grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
        <div><h3 class="font-bold text-gray-700 mb-2 md:mb-3 text-[9px] md:text-xs uppercase tracking-wider">Top Saldo Tabungan</h3><div id="list_tab_saldo_top" class="space-y-2 md:space-y-2.5"></div></div>
        <div><h3 class="font-bold text-gray-700 mb-2 md:mb-3 text-[9px] md:text-xs uppercase tracking-wider">Bottom Saldo Tabungan</h3><div id="list_tab_saldo_bot" class="space-y-2 md:space-y-2.5"></div></div>
        <div><h3 class="font-bold text-blue-700 mb-2 md:mb-3 text-[9px] md:text-xs uppercase tracking-wider">Tabungan Baru Masuk</h3><div id="list_tab_baru" class="space-y-2 md:space-y-2.5"></div></div>
        <div><h3 class="font-bold text-red-700 mb-2 md:mb-3 text-[9px] md:text-xs uppercase tracking-wider">Tabungan Keluar</h3><div id="list_tab_cair" class="space-y-2 md:space-y-2.5"></div></div>
      </div>
    </div>

  </div>
</div>

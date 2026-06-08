<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  /* Animasi Modal */
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* ========================================================
     🔥 MAGIC STICKY TABLE UTAMA (COMPACT) 🔥
     ======================================================== */
  #tabelMob { border-collapse: separate; border-spacing: 0; }
  #tabelMob th, #tabelMob td { background-clip: padding-box; background-color: #fff; }
  
  #tabelMob thead th { position: sticky !important; z-index: 40; box-shadow: inset 0 -1px 0 #cbd5e1, inset 0 1px 0 #cbd5e1; }
  
  .mob-row-1 th { top: 0 !important; height: 36px; background-color: #f1f5f9 !important; color: #1e3a8a; font-weight: 800; }
  .mob-row-2 th { top: 36px !important; height: 30px; background-color: #f8fafc !important; color: #334155; }
  .mob-row-1 th.sticky-left { z-index: 60 !important; left: 0 !important; box-shadow: inset -1px -1px 0 #cbd5e1; background-color: #e0f2fe !important; border-top-left-radius: 8px; } 
  
  .mob-row-tot th { top: 66px !important; z-index: 45 !important; height: 38px; box-shadow: inset 0 -2px 0 #93c5fd; background-color: #eff6ff !important; cursor: default; }
  .mob-row-tot th.sticky-left { z-index: 62 !important; left: 0 !important; box-shadow: inset -1px -2px 0 #93c5fd; background-color: #dbeafe !important; }

  @media (min-width: 768px) {
      .mob-row-1 th { height: 40px; }
      .mob-row-2 th { top: 40px !important; height: 34px; }
      .mob-row-tot th { top: 74px !important; height: 42px; }
  }

  #bodyMatrix td { position: relative; z-index: 10 !important; }
  .sticky-left { position: sticky !important; left: 0 !important; }
  #bodyMatrix td.sticky-left { z-index: 30 !important; background-color: #ffffff !important; box-shadow: inset -1px 0 0 #e2e8f0; font-weight: bold; }
  
  .cell-hover:hover { background-color: #e0f2fe !important; cursor: pointer; transform: scale(1.05); transition: 0.1s; z-index: 35 !important; position: relative; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #3b82f6; border-radius: 6px; }
  #bodyMatrix tr:hover td { background-color: #f8fafc !important; }
  #bodyMatrix tr:hover td.sticky-left { background-color: #f8fafc !important; filter: brightness(0.98); }

  /* ========================================================
     🔥 TABEL MODAL DETAIL MOB 🔥
     ======================================================== */
  #tableExportMob { border-collapse: separate; border-spacing: 0; }
  #tableExportMob th, #tableExportMob td { background-clip: padding-box; background-color: #fff; }
  
  #tableExportMob thead th { height: 40px; background-color: #f1f5f9 !important; box-shadow: inset 0 -1px 0 #cbd5e1, 0 1px 0 #cbd5e1; top: 0 !important; position: sticky !important; z-index: 40 !important; color: #475569; }

  #bodyModalDetail td { position: relative; z-index: 10; }
  #tableExportMob th.mod-td-rek, #bodyModalDetail td.mod-td-rek { position: sticky !important; left: 0 !important; z-index: 31 !important; background-color: #fff !important; box-shadow: inset -1px 0 0 #e2e8f0; min-width: 100px; max-width: 100px; }
  #tableExportMob th.mod-td-nas, #bodyModalDetail td.mod-td-nas { position: sticky !important; left: 0 !important; z-index: 30 !important; background-color: #fff !important; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); min-width: 160px; max-width: 160px; }
  
  @media (min-width: 768px) { 
      #tableExportMob th.mod-td-rek, #bodyModalDetail td.mod-td-rek { min-width: 120px; max-width: 120px; }
      #tableExportMob th.mod-td-nas, #bodyModalDetail td.mod-td-nas { left: 120px !important; min-width: 250px; max-width: 250px; } 
  }
  #tableExportMob thead th.mod-td-rek, #tableExportMob thead th.mod-td-nas { z-index: 60 !important; background-color: #e2e8f0 !important; }
  #bodyModalDetail tr:hover td { background-color: #f8fafc !important; }
  #bodyModalDetail tr:hover td.mod-td-rek, #bodyModalDetail tr:hover td.mod-td-nas { filter: brightness(0.98); background-color: #f8fafc !important; }

  /* Form Inputs */
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; background:#fff; outline:none; transition: border 0.2s; width: 100%;}
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f8fafc; color: #94a3b8; cursor: not-allowed; border-color: #e2e8f0; }
  select.inp { appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1em; padding-right: 1.8rem; }
  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap;}
  @media (min-width: 768px) { .lbl { font-size:10px; } .inp { border-radius: 8px; padding:0 10px; } select.inp { padding-right: 2rem; } }
  .field { display:flex; flex-direction:column; min-width: 0; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover:not(:disabled) { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
  input[type="date"]::-webkit-inner-spin-button, input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
  input[type="date"] { -moz-appearance: textfield; }
</style>

<div class="max-w-[1920px] w-full mx-auto px-2 md:px-4 py-4 md:py-6 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">
  
  <div class="relative z-[99] flex-none mb-3 md:mb-4 w-full bg-white p-2 md:p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3 shrink-0">
      
      <div class="flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
          <h1 class="text-base md:text-xl font-extrabold text-slate-800 flex items-center gap-2 whitespace-nowrap">
              <span class="p-1.5 md:p-2 bg-blue-600 rounded-lg text-white shadow-sm shrink-0">
                  <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
              </span>
              MOB <span class="hidden md:inline">(Month Of Booking)</span>
              
              <div class="relative group cursor-help ml-1">
                  <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-500 hover:text-blue-700 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                  
                  <div class="absolute left-0 top-full mt-2 w-[280px] md:w-[360px] bg-white border border-slate-200 shadow-2xl rounded-xl p-3 md:p-4 hidden group-hover:flex flex-col gap-2 z-[999] text-xs font-normal text-slate-600 whitespace-normal">
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
              
              <button type="button" onclick="exportExcelRekapMob()" class="btn-icon h-[34px] md:h-[36px] w-[38px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm shrink-0 ml-auto xl:ml-0 mt-2 xl:mt-0" title="Download Excel">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></line></svg>
              </button>
          </form>
      </div>
  </div>

  <div class="flex-1 min-h-0 overflow-hidden bg-white rounded-xl shadow-sm border border-slate-200 relative flex flex-col z-10">
    <div id="loadingMob" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
        <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
        <span>Menyiapkan Matriks...</span>
    </div>
    
    <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table class="w-max min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelMob">
        <thead class="font-bold tracking-wider text-[9px] md:text-[11px] select-none">
          <tr class="mob-row-1">
            <th rowspan="2" class="sticky-left px-2 md:px-3 text-left w-[80px] md:w-[100px] uppercase align-middle border-r border-slate-200">Bulan Real</th>
            <th rowspan="2" class="px-1 md:px-2 py-1.5 border-r border-slate-200 align-middle text-blue-700 w-[40px] md:w-[50px]">MOB</th>
            <th rowspan="2" class="px-2 md:px-3 py-1.5 border-r border-slate-200 text-right w-[100px] md:w-[120px] align-middle text-blue-700">Tot Plafond</th>
            <th colspan="8" class="py-1.5 border-b border-slate-200 text-center uppercase tracking-widest text-slate-600">DPD (Days Past Due)</th>
          </tr>
          <tr class="mob-row-2 text-[8.5px] md:text-[10px]">
            <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-emerald-700">0</th>
            <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-yellow-700">1 - 7</th>
            <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-yellow-700">8 - 14</th>
            <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-orange-700">15 - 21</th>
            <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-orange-700">22 - 30</th>
            <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-red-700">31 - 60</th>
            <th class="px-1 py-1 border-r border-slate-200 w-[70px] md:w-[95px] text-red-700">61 - 90</th>
            <th class="px-1 py-1 border-slate-200 w-[70px] md:w-[95px] text-red-800">&gt; 90</th>
          </tr>
          <tr id="rowTotalMobAtas" class="mob-row-tot text-[9px] md:text-xs font-extrabold tracking-wide"></tr>
        </thead>
        <tbody id="bodyMatrix" class="divide-y divide-slate-100 bg-white text-[9.5px] md:text-xs"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="modalDetailMob" class="fixed inset-0 hidden z-[9999] flex items-end md:items-center justify-center p-0 md:p-4">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalMob()"></div>
  <div class="relative bg-white w-full h-[95vh] md:h-[92vh] max-w-[1700px] rounded-t-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up">
    
    <div class="flex flex-col bg-white border-b shrink-0 w-full z-50">
        <div class="flex flex-row items-center justify-between px-3 py-2.5 md:px-4 md:py-3 gap-2 w-full overflow-x-auto no-scrollbar">
            
            <div class="flex-1 min-w-[180px] shrink-0">
                <h3 class="font-bold text-slate-800 flex items-center gap-1.5 md:gap-2 text-[12px] md:text-xl leading-none">
                    <span class="w-1.5 md:w-2 h-4 md:h-6 bg-blue-600 rounded-full hidden md:block"></span> 
                    <span class="truncate">Detail Debitur MOB</span> 
                    <span id="badgeBucketDetail" class="text-[9px] md:text-sm bg-blue-600 text-white px-2 py-0.5 md:px-2.5 rounded-md md:rounded-full shadow-sm ml-1 font-mono shrink-0">Bucket ?</span>
                </h3>
                <p class="text-[9px] md:text-[11px] text-slate-500 mt-1 md:ml-4 font-mono font-medium leading-none truncate" id="subTitleDetail">Loading...</p>
            </div>
            
            <div class="flex flex-row items-center gap-1.5 md:gap-2 shrink-0 ml-auto">
                <div class="relative w-[130px] md:w-[200px] shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="search_nasabah" onkeyup="filterTableDetail()" class="w-full pl-8 pr-3 py-1.5 h-[30px] md:h-[34px] bg-slate-50 border border-slate-200 rounded-lg text-[10px] md:text-xs outline-none focus:border-blue-500 focus:bg-white transition-all placeholder-slate-400 font-medium" placeholder="Cari nama...">
                </div>
                
                <button onclick="exportExcelDetailMob()" class="btn-icon bg-indigo-600 hover:bg-indigo-700 text-white px-2.5 md:px-3 h-[30px] md:h-[34px] rounded-lg shadow-sm shrink-0 flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="text-[10px] md:text-xs font-bold uppercase tracking-wider hidden sm:inline">Export</span>
                </button>

                <button onclick="closeModalMob()" class="w-[30px] h-[30px] md:w-[34px] md:h-[34px] flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-500 hover:text-white text-red-500 transition font-bold text-lg md:text-xl leading-none shrink-0">&times;</button>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-auto bg-slate-50 relative p-0 md:p-3 custom-scrollbar">
        <div id="loadingModal" class="hidden absolute inset-0 bg-white/90 z-40 flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
            <div class="animate-spin rounded-full h-8 w-8 md:h-10 md:w-10 border-4 border-blue-500 border-t-transparent mb-2 md:mb-3"></div>
            <span class="text-[10px] md:text-sm font-bold uppercase tracking-widest">Memuat Detail...</span>
        </div>
        
        <table class="w-max min-w-full text-center md:text-left text-slate-700 border-separate border-spacing-0 md:border border-slate-200 md:rounded-xl shadow-sm bg-white table-fixed" id="tableExportMob">
            <thead id="headModalMigrasi" class="text-slate-600 font-extrabold select-none text-[9px] md:text-xs uppercase tracking-wider">
                </thead>
            <tbody id="bodyModalDetail" class="divide-y divide-slate-100 bg-white text-[9.5px] md:text-[12px]"></tbody>
        </table>
    </div>

    <div class="px-3 py-2.5 md:px-6 md:py-4 border-t bg-white flex justify-between items-center shrink-0">
        <span id="pageInfoDetail" class="text-[9px] md:text-xs font-bold text-slate-600 bg-slate-100 px-2 md:px-3 py-1 rounded-md md:rounded-lg">0 Data</span>
        <div class="flex gap-1.5 md:gap-2">
            <button id="btnPrevDetail" onclick="changePageDetailMob(-1)" class="px-3 md:px-4 py-1.5 md:py-2 bg-white border border-slate-300 rounded-md md:rounded-lg text-[9px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">« Prev</button>
            <button id="btnNextDetail" onclick="changePageDetailMob(1)" class="px-3 md:px-4 py-1.5 md:py-2 bg-white border border-slate-300 rounded-md md:rounded-lg text-[9px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">Next »</button>
        </div>
    </div>
  </div>
</div>

<script>
// --- CONFIG ---
const API_URL = './api/kredit/'; 
const API_KODE = './api/kode/';
const API_DATE = './api/date/'; 
const nfID = new Intl.NumberFormat('id-ID');
const fmt = n => nfID.format(Math.round(Number(n||0)));
const apiCall = (url, opt={}) => (window.apiFetch ? window.apiFetch(url,opt) : fetch(url,opt));

let abortMainMob;
let detailParamsMob = {}; 
let detailPageMob = 1;
let rekapDataCacheMob = null; 

function toggleFilter(id) {
    const el = document.getElementById(id);
    if(el.classList.contains('hidden')) {
        el.classList.remove('hidden'); el.classList.add('flex');
    } else {
        el.classList.add('hidden'); el.classList.remove('flex');
    }
}

// --- INIT ---
window.addEventListener('DOMContentLoaded', async () => {
    const d = await getLastHarianData(); 
    document.getElementById('harian_date_mob').value = d ? d.last_created : new Date().toISOString().split('T')[0];
    await populateAreaDropdown();
    updateFilterUI(); 
});

async function getLastHarianData(){
    try{ const r=await apiCall(API_DATE); return (await r.json()).data; } catch{ return null; }
}

// DROPDOWN 1: Populate Area (Gabungan)
async function populateAreaDropdown(){
    const optArea = document.getElementById('opt_area');
    const user = (window.getUser && window.getUser()) || null;
    const userKode = (user?.kode ? String(user.kode).padStart(3,'0') : '000');

    if(userKode !== '000'){
        optArea.innerHTML = `<option value="CAB-${userKode}">CABANG ${userKode}</option>`;
        optArea.value = `CAB-${userKode}`;
        optArea.disabled = true;
        return;
    }

    try {
        const res = await apiCall(API_KODE, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
        const json = await res.json();
        const list = Array.isArray(json.data) ? json.data : [];
        
        let html = `<option value="ALL">ALL KONSOLIDASI</option>`;
        list.filter(x => x.kode_kantor && x.kode_kantor !== '000')
            .sort((a,b) => String(a.kode_kantor).localeCompare(b.kode_kantor))
            .forEach(it => { 
                html += `<option value="CAB-${String(it.kode_kantor).padStart(3,'0')}">${String(it.kode_kantor).padStart(3,'0')} - ${it.nama_kantor}</option>`; 
            });
        
        optArea.innerHTML = html;
        optArea.disabled = false;
    } catch(e){ optArea.innerHTML = `<option value="ALL">Error Load Area</option>`; }
}

// UPDATE UI LOGIC (Ganti Judul Kankas/Korwil)
function updateFilterUI() {
    const areaVal = document.getElementById('opt_area').value;
    const lblSub = document.getElementById('lbl_sub');
    const optSub = document.getElementById('opt_sub_main');
    const optAo = document.getElementById('opt_ao_main');

    if(areaVal === 'ALL') {
        lblSub.innerText = "KORWIL";
        optSub.innerHTML = `
            <option value="ALL">ALL KORWIL</option>
            <option value="SEMARANG">SEMARANG</option>
            <option value="SOLO">SOLO</option>
            <option value="BANYUMAS">BANYUMAS</option>
            <option value="PEKALONGAN">PEKALONGAN</option>
        `;
        optAo.innerHTML = `<option value="ALL">PILIH CABANG DULU</option>`;
        optAo.disabled = true;
    } else {
        lblSub.innerText = "KANKAS";
        optSub.innerHTML = `<option value="ALL">ALL KANKAS</option>`;
        optAo.innerHTML = `<option value="ALL">ALL AO</option>`;
        optAo.disabled = false;
    }
    
    fetchRekapMob();
}

// Render Dropdown List Kankas & AO terpisah dari Backend
function renderSubDropdown(selectId, dataArray, defaultLabel) {
    const el = document.getElementById(selectId);
    const currentVal = el.value; 
    
    let html = `<option value="ALL">${defaultLabel}</option>`;
    if(dataArray && dataArray.length > 0) {
        dataArray.forEach(x => { html += `<option value="${x.kode}">${x.kode} - ${x.nama}</option>`; });
    }
    
    el.innerHTML = html;
    if (currentVal && html.includes(`value="${currentVal}"`)) el.value = currentVal;
}

// --- 1. FETCH REKAP MOB ---
async function fetchRekapMob(){
    const loading = document.getElementById('loadingMob');
    const tbody  = document.getElementById('bodyMatrix');
    
    const harian = document.getElementById('harian_date_mob').value;
    const areaVal = document.getElementById('opt_area').value;
    const subVal = document.getElementById('opt_sub_main').value;
    const aoVal  = document.getElementById('opt_ao_main').value;

    if(abortMainMob) abortMainMob.abort();
    abortMainMob = new AbortController();

    loading.classList.remove('hidden');
    tbody.innerHTML = `<tr><td colspan="11" class="py-20 text-center text-slate-400 italic text-[10px] md:text-sm">Sedang mengambil data...</td></tr>`;
    rekapDataCacheMob = null;

    try {
        let payload = { type: "mob_vintage", harian_date: harian, rekap_by: "bulan" };
        
        // Terjemahkan Filter UI ke Backend Payload
        if(areaVal === 'ALL') {
            if(subVal !== 'ALL') payload.korwil = subVal;
        } else {
            payload.kode_kantor = areaVal.replace('CAB-', '');
            if(subVal !== 'ALL') payload.kode_kankas = subVal;
            if(aoVal !== 'ALL') payload.kode_ao = aoVal;
        }
        
        const res = await apiCall(API_URL, {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload), signal: abortMainMob.signal
        });
        const json = await res.json();
        
        if(json.status !== 200) throw new Error(json.message);

        // Update Dropdown Kankas dan AO (jika dalam mode Cabang)
        if(areaVal !== 'ALL' && json.data.dropdown_lists) {
            renderSubDropdown('opt_sub_main', json.data.dropdown_lists.list_kankas, 'ALL KANKAS');
            renderSubDropdown('opt_ao_main', json.data.dropdown_lists.list_ao, 'ALL AO');
        }

        const displayData = json.data.data || [];
        const bucketsKey = json.data.buckets_order || ['0', '1 - 7', '8 - 14', '15 - 21', '22 - 30', '31 - 60', '61 - 90', '> 90'];

        if(displayData.length === 0){
            tbody.innerHTML = `<tr><td colspan="11" class="py-20 text-center text-slate-400 italic text-[10px] md:text-sm">Tidak ada data.</td></tr>`;
            document.getElementById('rowTotalMobAtas').innerHTML = '';
            return;
        }

        rekapDataCacheMob = { data: displayData, buckets: bucketsKey };

        let html = '';
        let grandTotal = { plafond: 0, buckets: {} };
        bucketsKey.forEach(b => grandTotal.buckets[b] = { os:0, noa:0 });

        displayData.forEach(r => {
            let totalPlafondRow = parseFloat(r.total_plafond || 0);
            grandTotal.plafond += totalPlafondRow;
            let cells = '';
            
            bucketsKey.forEach(key => {
                const bData = r.buckets[key] || { pct:0, noa:0, os:0 };
                let bucketOs = parseFloat(bData.os || 0);

                grandTotal.buckets[key].os  += bucketOs;
                grandTotal.buckets[key].noa += parseInt(bData.noa || 0);

                // 🔥 HITUNG PERSENTASE MURNI DI FE 🔥
                let rawPct = totalPlafondRow > 0 ? (bucketOs / totalPlafondRow) * 100 : 0;
                let textPct = rawPct.toFixed(2);

                let bgClass = 'bg-transparent'; let textClass = 'text-slate-800';

                // 🔥 WARNA SESUAI REQUEST (Murni Warna Tanpa Teks Tag) 🔥
                if(key === '0') {
                    if(rawPct > 0) { bgClass = 'bg-emerald-50/50 border-emerald-100'; textClass = 'text-emerald-700'; }
                } else if (key === '1 - 7' || key === '8 - 14') {
                    if(rawPct > 0) { bgClass = 'bg-yellow-50/50 border-yellow-100'; textClass = 'text-yellow-700'; }
                } else if (key === '15 - 21' || key === '22 - 30') {
                    if(rawPct > 0) { bgClass = 'bg-orange-50/50 border-orange-100'; textClass = 'text-orange-700'; }
                } else {
                    if(rawPct > 0) { bgClass = 'bg-red-50/50 border-red-100'; textClass = 'text-red-700'; }
                }

                const clickEv = (bucketOs > 0) ? `onclick="openModalMob('${r.group_id}', '${key}')"` : '';
                const cursor = (bucketOs > 0) ? 'cell-hover' : '';

                cells += `
                    <td class="px-1 md:px-2 py-1.5 border-r border-slate-200 align-middle ${bgClass}">
                        <div class="flex flex-col justify-center h-full ${cursor} transition px-0.5 rounded" ${clickEv}>
                            <div class="font-bold text-[9.5px] md:text-[11px] ${textClass} leading-tight mb-1">${bucketOs > 0 ? fmt(bucketOs) : '-'}</div>
                            <div class="text-[7.5px] md:text-[8.5px] text-slate-500 font-medium leading-tight">NOA: <span class="font-bold text-slate-700">${bData.noa}</span> <span class="mx-0.5 opacity-50">|</span> <span class="font-bold ${textClass}">${textPct}%</span></div>
                        </div>
                    </td>`;
            });

            const txtMob = r.mob ? r.mob : '-';

            html += `
                <tr class="hover:bg-slate-50 border-b border-slate-200 group h-[48px] md:h-[54px]">
                    <td class="sticky-left px-2 md:px-3 py-1.5 text-left font-bold text-[10px] md:text-xs text-slate-700 bg-white border-r border-slate-200 align-middle shadow-[inset_-1px_0_0_#e2e8f0] z-10 min-w-[80px] md:min-w-[100px] truncate" title="${r.group_name}">${r.group_name}</td>
                    <td class="px-1 md:px-2 py-1.5 border-r border-slate-200 text-center font-bold text-[10px] md:text-xs text-blue-700 bg-blue-50/30 align-middle">${txtMob}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-200 text-right font-mono font-bold text-[10px] md:text-xs text-blue-800 bg-blue-50/10 align-middle leading-tight">${fmt(r.total_plafond)}</td>
                    ${cells}
                </tr>`;
        });
        tbody.innerHTML = html;

        // --- RENDER TOTAL STICKY ---
        let tf = `<th class="sticky-left px-2 md:px-3 text-left uppercase tracking-widest align-middle text-blue-900 z-50 bg-[#eff6ff] text-[9px] md:text-[11px] shadow-[inset_-1px_0_0_#93c5fd]">TOTAL</th>
                  <th class="border-r border-blue-300 px-1 md:px-2 text-center align-middle text-blue-900 bg-[#eff6ff]">-</th>
                  <th class="border-r border-blue-300 px-2 md:px-3 text-right font-mono font-bold text-[10px] md:text-[12px] text-blue-900 align-middle bg-[#eff6ff] leading-tight">${fmt(grandTotal.plafond)}</th>`;
        
        let pembagiTotal = grandTotal.plafond > 0 ? grandTotal.plafond : 1;
        
        bucketsKey.forEach(b => { 
            const bTot = grandTotal.buckets[b];
            let rawPctTotal = (bTot.os / pembagiTotal) * 100;
            const pctTotal = rawPctTotal.toFixed(2);
            
            tf += `<th class="border-r border-blue-300 align-middle bg-[#eff6ff] px-1 md:px-2">
                      <div class="flex flex-col justify-center h-full py-1">
                          <div class="text-[9.5px] md:text-[11px] text-blue-900 font-bold leading-tight mb-0.5">${fmt(bTot.os)}</div>
                          <div class="text-[7.5px] md:text-[8.5px] text-blue-600 font-medium leading-tight">NOA: <span class="font-bold text-blue-800">${bTot.noa}</span> <span class="mx-0.5 opacity-50">|</span> <span class="font-bold">${pctTotal}%</span></div>
                      </div>
                   </th>` 
        });
        document.getElementById('rowTotalMobAtas').innerHTML = tf;

    } catch(err) {
        if(err.name !== 'AbortError') tbody.innerHTML = `<tr><td colspan="11" class="py-16 text-center text-red-500 font-bold tracking-widest uppercase text-[10px] md:text-sm">Error: ${err.message}</td></tr>`;
    } finally { loading.classList.add('hidden'); }
}

window.exportExcelRekapMob = function() {
    if(!rekapDataCacheMob || !rekapDataCacheMob.data) return alert("Tidak ada data rekap untuk didownload.");
    const rows = rekapDataCacheMob.data;
    const bk = rekapDataCacheMob.buckets;
    let csv = "Bulan Realisasi\tMOB\tTotal Plafond\t";
    bk.forEach(b => csv += `% ${b}\tOS ${b}\tNOA ${b}\t`);
    csv += "\n";

    rows.forEach(r => {
        let pembagi = parseFloat(r.total_plafond || 0);
        csv += `'${r.group_name}\t${r.mob||'-'}\t${Math.round(r.total_plafond)}\t`;
        bk.forEach(b => {
            const d = r.buckets[b];
            let rowOS = parseFloat(d.os || 0);
            let rowPct = pembagi > 0 ? ((rowOS / pembagi) * 100).toFixed(2) : 0;
            csv += `${rowPct}%\t${Math.round(rowOS)}\t${d.noa}\t`;
        });
        csv += "\n";
    });

    const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
    const a = document.createElement('a');
    a.href = window.URL.createObjectURL(blob);
    a.download = `Rekap_MOB_${document.getElementById("harian_date_mob").value}.xls`; 
    a.click();
}

// --- 2. MODAL DETAIL LOGIC ---
async function openModalMob(bulanReal, bucket){
    const areaVal = document.getElementById('opt_area').value;
    const subVal = document.getElementById('opt_sub_main').value;
    const aoVal  = document.getElementById('opt_ao_main').value;

    detailParamsMob = {
        type: "detail_mob_debitur",
        harian_date: document.getElementById('harian_date_mob').value,
        bulan_realisasi: bulanReal,
        bucket_label: bucket
    };

    if(areaVal === 'ALL') {
        if(subVal !== 'ALL') detailParamsMob.korwil = subVal;
    } else {
        detailParamsMob.kode_kantor = areaVal.replace('CAB-','');
        if(subVal !== 'ALL') detailParamsMob.kode_kankas = subVal;
        if(aoVal !== 'ALL') detailParamsMob.kode_ao = aoVal;
    }

    detailPageMob = 1;

    document.getElementById('modalDetailMob').classList.remove('hidden');
    document.getElementById('badgeBucketDetail').innerText = `Bucket ${bucket}`;
    document.getElementById('subTitleDetail').innerText = `Bulan Realisasi: ${bulanReal}`;
    document.getElementById('search_nasabah').value = '';
    
    renderModalHeaderMigrasi();
    fetchDetailMob();
}

function renderModalHeaderMigrasi() {
    const mHead = document.getElementById('headModalMigrasi');
    mHead.innerHTML = `
        <tr>
            <th class="mod-td-rek hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 rounded-tl-xl text-left md:text-center">Rekening</th>
            <th class="mod-td-nas px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 text-left md:text-center">Nama Nasabah</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[180px] md:w-[250px] text-center">Alamat</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[110px] md:w-[130px] text-center">No HP</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[100px] md:w-[120px] text-center">AO</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[100px] md:w-[120px] text-center">Kankas</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[110px] md:w-[140px] text-center">Tgl Realisasi</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[110px] md:w-[140px] text-right">Plafond</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-green-200 w-[110px] md:w-[140px] text-right bg-green-50 text-green-700">OS Current</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[50px] md:w-[60px] text-center">Kol</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-red-200 w-[100px] md:w-[130px] text-right bg-red-50 text-red-800">Tot Tunggakan</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-green-200 w-[110px] md:w-[140px] text-right bg-green-50 text-green-800">Total Bayar</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[110px] md:w-[140px] text-right">Tabungan</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-slate-200 w-[90px] md:w-[100px] text-center">Stat Tab</th>
        </tr>
    `;
}

window.filterTableDetail = function() {
    const filter = document.getElementById("search_nasabah").value.toLowerCase();
    const trs = document.getElementById("bodyModalDetail").getElementsByTagName("tr");
    for (let i = 0; i < trs.length; i++) {
        const tdName = trs[i].getElementsByTagName("td")[1];
        if (tdName) {
            trs[i].style.display = (tdName.textContent || tdName.innerText).toLowerCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}

async function fetchDetailMob(){
    const loader = document.getElementById('loadingModal');
    const tbody  = document.getElementById('bodyModalDetail');
    
    loader.classList.remove('hidden');
    tbody.innerHTML = '';

    try {
        const payload = { ...detailParamsMob, page: detailPageMob };
        const res = await apiCall(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
        const json = await res.json();
        
        if(json.status !== 200) throw new Error(json.message);

        const list = json.data?.data || [];
        const totalRecords = json.data?.total_records || 0;
        const totalPages   = json.data?.total_pages || 1;

        if(list.length === 0){
            tbody.innerHTML = `<tr><td colspan="14" class="py-20 text-center text-slate-400 italic text-[10px] md:text-sm">Tidak ada data detail.</td></tr>`;
            return;
        }

        let html = '';
        list.forEach(row => {
            let statTabungan = parseFloat(row.tabungan) >= (1.5 * parseFloat(row.totung)) 
                ? `<span class="text-green-600 font-bold text-[9px] md:text-xs">Aman</span>` 
                : `<span class="text-red-500 font-bold text-[9px] md:text-xs">Belum Aman</span>`;

            let alamatPendek = row.alamat && row.alamat.length > 25 ? row.alamat.substring(0, 25) + '...' : (row.alamat||'-');

            html += `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[40px] md:h-[48px] group">
                    <td class="mod-td-rek hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 font-mono text-[9.5px] md:text-[11px] text-slate-500 border-r border-slate-100">${row.no_rekening}</td>
                    <td class="mod-td-nas px-2 md:px-4 py-1.5 md:py-2 font-bold text-[9.5px] md:text-[11px] text-slate-700 truncate border-r border-slate-100" title="${row.nama_nasabah}">${row.nama_nasabah}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-slate-500 text-[9.5px] md:text-[11px] border-r border-slate-100 whitespace-nowrap text-center" title="${row.alamat}">${alamatPendek}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-slate-700 text-[9px] md:text-[11px] border-r border-slate-100">${row.no_hp||'-'}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-[9px] md:text-[11px] text-slate-500 border-r border-slate-100 truncate max-w-[100px]" title="${row.nama_ao}">${row.nama_ao||'-'}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-[9px] md:text-[11px] text-slate-500 border-r border-slate-100 truncate max-w-[100px]" title="${row.nama_kankas}">${row.nama_kankas||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-center font-mono text-[9.5px] md:text-[11px] text-blue-700 bg-blue-50/30 border-r border-blue-100">${row.tgl_realisasi}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-medium text-[9.5px] md:text-[12px] text-slate-500 border-r border-slate-100">${fmt(row.plafond)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-mono font-bold text-[9.5px] md:text-[13px] text-blue-700 border-r border-slate-100 bg-slate-50/50">${fmt(row.os)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-bold text-[9.5px] md:text-sm text-slate-600 border-r border-slate-100">${row.kolektibilitas||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-mono font-bold text-[9.5px] md:text-sm text-red-600 bg-red-50/30 border-r border-red-100">${fmt(row.totung)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-mono font-bold text-[9.5px] md:text-[12px] text-green-700 bg-green-50/30 border-r border-green-100">${fmt(row.transaksi)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-mono font-bold text-[9.5px] md:text-[12px] text-emerald-600 bg-emerald-50/10 border-r border-slate-100">${fmt(row.tabungan)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center">${statTabungan}</td>
                </tr>`;
        });
        tbody.innerHTML = html;

        document.getElementById('pageInfoDetail').innerText = `Hal ${detailPageMob} dari ${totalPages} (${fmt(totalRecords)} Data)`;
        document.getElementById('btnPrevDetail').disabled = detailPageMob <= 1;
        document.getElementById('btnNextDetail').disabled = detailPageMob >= totalPages;
        filterTableDetail();

    } catch(e){
        tbody.innerHTML = `<tr><td colspan="14" class="py-16 text-center text-red-500 font-bold uppercase tracking-widest">Gagal mengambil detail.</td></tr>`;
    } finally { loader.classList.add('hidden'); }
}

window.changePageDetailMob = function(step) { detailPageMob += step; fetchDetailMob(); }

window.exportExcelDetailMob = async function() {
    const btn = event.target.closest('button'); const txt = btn.innerHTML;
    btn.innerHTML = `...`; btn.disabled = true;

    try {
        const payload = { ...detailParamsMob, page: 1, limit: 10000 };
        const res = await apiCall(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
        const json = await res.json();
        const rows = json.data?.data || [];
        
        if(rows.length === 0) return alert("Tidak ada data");

        let csv = `No Rekening\tNama Nasabah\tAO\tKankas\tTgl Realisasi\tPlafond\tBaki Debet\tKol\tTot Tunggakan\tTabungan\n`;
        rows.forEach(x => {
            csv += `'${x.no_rekening}\t${x.nama_nasabah}\t${x.nama_ao||''}\t${x.nama_kankas||''}\t${x.tgl_realisasi}\t${Math.round(x.plafond)}\t${Math.round(x.os)}\t${x.kolektibilitas||''}\t${Math.round(x.totung)}\t${Math.round(x.tabungan)}\n`;
        });

        const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
        const a = document.createElement('a'); a.href = window.URL.createObjectURL(blob);
        a.download = `Detail_MOB_${detailParamsMob.bulan_realisasi}_Bucket_${detailParamsMob.bucket_label}.xls`; a.click();
    } catch(e) { alert("Gagal export data."); } finally { btn.innerHTML = txt; btn.disabled = false; }
}

window.closeModalMob = function(){ document.getElementById('modalDetailMob').classList.add('hidden'); }
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModalMob(); });
</script>
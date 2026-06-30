<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  /* Custom Scrollbar & Utility */
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px;}
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
  
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* ========================================================
     🔥 MAGIC STICKY TABLE UTAMA (COMPACT) 🔥
     ======================================================== */
  #tabelUtama { border-collapse: separate; border-spacing: 0; }
  #tabelUtama th, #tabelUtama td { background-clip: padding-box; background-color: #fff; }
  
  /* Lapis 1 & 2 (Header Utama) */
  .head-lapis-1 th { height: 36px; background-color: #f1f5f9 !important; color: #1e3a8a; font-weight: 800; position: sticky !important; top: 0 !important; z-index: 40; box-shadow: inset 0 -1px 0 #cbd5e1, inset 0 1px 0 #cbd5e1;}
  .head-lapis-2 th { top: 36px !important; height: 30px; background-color: #f8fafc !important; color: #334155; position: sticky !important; z-index: 40; box-shadow: inset 0 -1px 0 #cbd5e1;}
  
  /* Freeze Kolom Kiri Header Utama */
  .head-lapis-1 th.freeze-col-1 { z-index: 60 !important; left: 0 !important; box-shadow: inset -1px -1px 0 #cbd5e1; background-color: #e0f2fe !important; border-top-left-radius: 8px; } 
  .head-lapis-1 th.freeze-col-2 { z-index: 59 !important; left: 50px !important; box-shadow: inset -1px -1px 0 #cbd5e1; background-color: #e0f2fe !important; } 
  
  /* Lapis 3 (Grand Total) - SEKARANG FREEZE DI BAWAH HEADER */
  .mob-row-tot th { top: 66px !important; z-index: 45 !important; height: 38px; box-shadow: inset 0 -2px 0 #93c5fd; background-color: #eff6ff !important; cursor: default; position: sticky !important;}
  .mob-row-tot th.freeze-col-1 { z-index: 62 !important; left: 0 !important; box-shadow: inset -1px -2px 0 #93c5fd; background-color: #dbeafe !important; }
  .mob-row-tot th.freeze-col-2 { z-index: 61 !important; left: 50px !important; box-shadow: inset -1px -2px 0 #93c5fd; background-color: #dbeafe !important; }

  @media (min-width: 768px) {
      .head-lapis-1 th { height: 40px; }
      .head-lapis-2 th { top: 40px !important; height: 34px; }
      .head-lapis-1 th.freeze-col-2 { left: 60px !important; }
      .mob-row-tot th { top: 74px !important; height: 42px; }
      .mob-row-tot th.freeze-col-2 { left: 60px !important; }
  }

  /* Freeze Kiri Body Utama */
  #bodyUtama td { position: relative; z-index: 10 !important; }
  #bodyUtama td.freeze-col-1 { position: sticky !important; left: 0 !important; z-index: 30 !important; background-color: #ffffff !important; box-shadow: inset -1px 0 0 #e2e8f0; font-weight: bold; }
  #bodyUtama td.freeze-col-2 { position: sticky !important; left: 50px !important; z-index: 29 !important; background-color: #ffffff !important; box-shadow: inset -1px 0 0 #e2e8f0; font-weight: bold; }
  @media (min-width: 768px) { #bodyUtama td.freeze-col-2 { left: 60px !important; } }

  /* Hover Effects Utama */
  .cell-hover:hover { background-color: #e0f2fe !important; cursor: pointer; transform: scale(1.05); transition: 0.1s; z-index: 35 !important; position: relative; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #3b82f6; border-radius: 6px; }
  #bodyUtama tr:hover td { background-color: #f8fafc !important; }
  #bodyUtama tr:hover td.freeze-col-1, #bodyUtama tr:hover td.freeze-col-2 { filter: brightness(0.98); background-color: #f8fafc !important;}

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

  /* Form Inputs (Perbaikan Dropdown Select agar Fleksibel) */
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
  
  <div class="relative z-20 flex-none mb-3 md:mb-4 w-full bg-white p-2 md:p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3 shrink-0">
      
      <div class="flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
          <h1 class="text-base md:text-xl font-extrabold text-slate-800 flex items-center gap-2 whitespace-nowrap">
              <span class="p-1.5 md:p-2 bg-blue-600 rounded-lg text-white shadow-sm shrink-0">
                  <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
              </span>
              Realisasi & Growth
              
              <div class="relative group cursor-help ml-1">
                  <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-500 hover:text-blue-700 transition" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                  
                  <div class="absolute left-0 top-full mt-2 w-[260px] md:w-[320px] bg-white border border-slate-200 shadow-2xl rounded-xl p-3 md:p-4 hidden group-hover:flex flex-col gap-2 z-50 text-xs font-normal text-slate-600 whitespace-normal">
                      <div class="font-bold text-slate-800 mb-1 border-b pb-1 text-sm">💡 Panduan Kamus Kolom</div>
                      <p><b>Realisasi Baru:</b> Pencairan kredit baru pada range tanggal data terpilih.</p>
                      <p><b>Restrukturisasi:</b> Kredit yang dilakukan penataan kembali (rescheduling/reconditioning/restructuring) akibat penurunan kemampuan bayar debitur.</p>
                      <p><b>Run Off:</b> Penurunan baki debet yang disebabkan oleh pembayaran angsuran murni maupun pelunasan dini oleh debitur.</p>
                      <div class="mt-2 bg-blue-50 border border-blue-200 p-2 rounded-lg text-[10.5px] leading-relaxed">
                          <b class="text-blue-800">Formula Growth:</b><br>
                          (Realisasi Baru + Realisasi Restrukturisasi) - Total Run Off
                      </div>
                  </div>
              </div>
          </h1>
          
          <button type="button" onclick="toggleFilter('filterWrapperReal')" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] md:text-xs whitespace-nowrap ml-2 shrink-0">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
              Filter
          </button>
      </div>

      <div id="filterWrapperReal" class="hidden xl:flex w-full xl:w-auto flex-1 min-w-0 justify-end transition-all duration-300 shrink-0 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
          <form id="formFilterReal" class="flex flex-row flex-wrap xl:flex-nowrap items-end gap-2 md:gap-2.5 w-full xl:w-auto" onsubmit="event.preventDefault();">
              
              <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[110px]">
                  <label class="lbl">CLOSING (M-1)</label>
                  <input type="date" id="closing_date" class="inp text-[10px] md:text-xs font-bold h-[34px] md:h-[36px] cursor-pointer text-slate-700" title="Posisi Data" required onclick="try{this.showPicker()}catch(e){}" onchange="fetchRekap()">
              </div>

              <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[110px]">
                  <label class="lbl">HARIAN (ACTUAL)</label>
                  <input type="date" id="harian_date" class="inp text-[10px] md:text-xs font-bold h-[34px] md:h-[36px] cursor-pointer text-slate-700" title="Posisi Data" required onclick="try{this.showPicker()}catch(e){}" onchange="fetchRekap()">
              </div>

              <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[150px]">
                  <label class="lbl">AREA / CABANG</label>
                  <select id="opt_area" class="inp text-[10px] md:text-xs font-bold text-slate-700 h-[34px] md:h-[36px] cursor-pointer truncate" onchange="updateFilterUI()" title="Pilih Area (Korwil / Cabang)">
                      <option value="ALL">ALL KONSOLIDASI</option>
                  </select>
              </div>

              <div class="field flex-1 min-w-[100px] xl:w-[130px] xl:max-w-[160px]">
                  <label id="lbl_sub" class="lbl text-slate-600">KORWIL</label>
                  <select id="opt_sub_main" class="inp text-[10px] md:text-xs font-bold text-slate-700 h-[34px] md:h-[36px] cursor-pointer truncate" onchange="fetchRekap()">
                      <option value="ALL">ALL KORWIL</option>
                      <option value="SEMARANG">SEMARANG</option>
                      <option value="SOLO">SOLO</option>
                      <option value="BANYUMAS">BANYUMAS</option>
                      <option value="PEKALONGAN">PEKALONGAN</option>
                  </select>
              </div>


              
              <button type="button" onclick="exportExcelRekap()" class="btn-icon h-[34px] md:h-[36px] w-[38px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm shrink-0 ml-auto xl:ml-0 mt-2 xl:mt-0" title="Download Excel">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></line></svg>
              </button>
          </form>
      </div>
  </div>

  <div class="flex-1 min-h-0 overflow-hidden bg-white rounded-xl shadow-sm border border-slate-200 relative flex flex-col z-10">
    <div id="loadingUtama" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
        <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
        <span>Menyiapkan Data...</span>
    </div>
    
    <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table class="w-max min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelUtama">
        <thead class="font-bold tracking-wider text-[9px] md:text-[11px] select-none" id="headUtama">
          <tr class="head-lapis-1">
            <th id="th_kode" rowspan="2" class="freeze-col-1 w-[50px] md:w-[60px] uppercase align-middle border-r border-slate-200 text-center cursor-pointer hover:bg-slate-200 transition" onclick="sortMainData('kode_kantor')">
                <div class="flex items-center justify-center">KODE</div>
            </th>
            <th id="th_nama" rowspan="2" class="freeze-col-2 w-[120px] md:w-[150px] uppercase align-middle border-r border-slate-200 text-left pl-3 cursor-pointer hover:bg-slate-200 transition" onclick="sortMainData('nama_kantor')">
                <div class="flex items-center">NAMA AREA</div>
            </th>
            <th colspan="2" class="py-1.5 border-b border-r border-slate-200 text-blue-800 bg-blue-50/40">REALISASI BARU</th>
            <th colspan="2" class="py-1.5 border-b border-r border-slate-200 text-purple-800 bg-purple-50/40">RESTRUKTURISASI</th>
            <th colspan="3" class="py-1.5 border-b border-slate-200 text-orange-800 bg-orange-50/40">RUN OFF (PENGURANGAN)</th>
            <th rowspan="2" class="w-[100px] md:w-[130px] border-l border-slate-200 align-middle text-right pr-3 text-slate-900 bg-slate-100/70 cursor-pointer hover:bg-slate-200 transition" onclick="sortMainData('growth')">
                <div class="flex items-center justify-end">GROWTH NET</div>
            </th>
          </tr>
          <tr class="head-lapis-2 text-[8.5px] md:text-[10px]">
            <th class="px-1 py-1 border-r border-slate-200 w-[45px] md:w-[60px] text-blue-700 cursor-pointer hover:bg-blue-100 transition" onclick="sortMainData('noa_realisasi')">NOA</th>
            <th class="px-2 py-1 border-r border-slate-200 w-[100px] md:w-[125px] text-right text-blue-700 cursor-pointer hover:bg-blue-100 transition" onclick="sortMainData('total_realisasi')">NOMINAL</th>
            
            <th class="px-1 py-1 border-r border-slate-200 w-[45px] md:w-[60px] text-purple-700 cursor-pointer hover:bg-purple-100 transition" onclick="sortMainData('noa_restruck')">NOA</th>
            <th class="px-2 py-1 border-r border-slate-200 w-[100px] md:w-[125px] text-right text-purple-700 cursor-pointer hover:bg-purple-100 transition" onclick="sortMainData('total_restruck')">NOMINAL</th>
            
            <th class="px-2 py-1 border-r border-slate-200 w-[95px] md:w-[120px] text-right text-emerald-700 cursor-pointer hover:bg-orange-100 transition" onclick="sortMainData('pelunasan')">PELUNASAN</th>
            <th class="px-2 py-1 border-r border-slate-200 w-[95px] md:w-[120px] text-right text-blue-700 cursor-pointer hover:bg-orange-100 transition" onclick="sortMainData('angsuran_murni')">ANGSURAN</th>
            <th class="px-2 py-1 border-r border-slate-200 w-[95px] md:w-[120px] text-right text-orange-700 cursor-pointer hover:bg-orange-100 transition" onclick="sortMainData('total_run_off')">TOT RUNOFF</th>
          </tr>
          <tr id="rowTotalAtas" class="mob-row-tot text-[9px] md:text-xs font-extrabold tracking-wide"></tr>
        </thead>
        <tbody id="bodyUtama" class="divide-y divide-slate-100 bg-white text-[9.5px] md:text-xs"></tbody>
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
                    <span class="truncate">Detail Debitur Realisasi</span> 
                    <span id="badgeBucketDetail" class="text-[9px] md:text-sm bg-blue-600 text-white px-2 py-0.5 md:px-2.5 rounded-md md:rounded-full shadow-sm ml-1 font-mono shrink-0">Tipe ?</span>
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
                <tr>
                    <th class="mod-td-rek hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 rounded-tl-xl text-left md:text-center">Rekening</th>
                    <th class="mod-td-nas px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 text-left md:text-center">Nama Nasabah</th>
                    <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[180px] md:w-[250px] text-center">Alamat</th>
                    <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[100px] md:w-[120px] text-center">AO</th>
                    <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[100px] md:w-[120px] text-center">Kankas</th>
                    <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[110px] md:w-[140px] text-center">Tgl Realisasi</th>
                    <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-slate-300 w-[110px] md:w-[140px] text-right">Plafond</th>
                </tr>
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
// --- CONFIG & UTILS ---
const API_URL = './api/kredit/'; 
const API_KODE = './api/kode/';
const API_DATE = './api/date/'; 
const nfID = new Intl.NumberFormat('id-ID');
const fmt = n => nfID.format(Math.round(Number(n||0)));
const apiCall = (url, opt={}) => (window.apiFetch ? window.apiFetch(url,opt) : fetch(url,opt));

let abortMain;
let rekapDataCache = [];
let rekapGtCache = null;
let userKodeGlobal = '000';

let detailParamsMob = {}; 
let detailPageMob = 1;

// State sorting
let sortCol = '';
let sortAsc = true;

function toggleFilter(id) {
    const el = document.getElementById(id);
    if(el.classList.contains('hidden')) {
        el.classList.remove('hidden'); el.classList.add('flex');
    } else {
        el.classList.add('hidden'); el.classList.remove('flex');
    }
}

const getSortIcon = (col) => {
    if (col !== sortCol) return ' <span class="opacity-30 text-[8px] ml-1">↕</span>';
    return sortAsc ? ' <span class="text-blue-600 text-[10px] ml-1">▲</span>' : ' <span class="text-blue-600 text-[10px] ml-1">▼</span>';
};

// --- INIT ---
window.addEventListener('DOMContentLoaded', async () => {
    const user = (window.getUser && window.getUser()) || null;
    userKodeGlobal = user?.kode ? String(user.kode).padStart(3, '0') : '000';
    if(userKodeGlobal === '099') userKodeGlobal = '000';

    const d = await getLastHarianData(); 
    if(d) {
        document.getElementById('closing_date').value = d.last_closing;
        document.getElementById('harian_date').value  = d.last_created;
    } else {
        const now = new Date();
        document.getElementById('closing_date').value = `${now.getFullYear() - 1}-12-31`;
        document.getElementById('harian_date').value = now.toISOString().split('T')[0];
    }
    await populateAreaDropdown();
    updateFilterUI(); 
});

async function getLastHarianData(){
    try{ const r=await apiCall(API_DATE); return (await r.json()).data; } catch{ return null; }
}

async function populateAreaDropdown(){
    const optArea = document.getElementById('opt_area');
    if(userKodeGlobal !== '000'){
        optArea.innerHTML = `<option value="CAB-${userKodeGlobal}">CABANG ${userKodeGlobal}</option>`;
        optArea.value = `CAB-${userKodeGlobal}`;
        optArea.disabled = true;
        return;
    }
    try {
        const res = await apiCall(API_KODE, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
        const json = await res.json();
        let html = `<option value="ALL">ALL KONSOLIDASI</option>`;
        (json.data||[]).filter(x => x.kode_kantor && x.kode_kantor !== '000')
            .sort((a,b) => String(a.kode_kantor).localeCompare(b.kode_kantor))
            .forEach(it => { html += `<option value="CAB-${String(it.kode_kantor).padStart(3,'0')}">${String(it.kode_kantor).padStart(3,'0')} - ${it.nama_kantor}</option>`; });
        optArea.innerHTML = html;
    } catch(e){ optArea.innerHTML = `<option value="ALL">Error Load Area</option>`; }
}

async function loadDropdownAPI(type, kode_cabang, selectId, defaultLabel) {
    const el = document.getElementById(selectId);
    if(!kode_cabang) return;
    try {
        const r = await apiCall(API_KODE, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type: type, kode_kantor: kode_cabang}) });
        const j = await r.json();
        let html = `<option value="ALL">${defaultLabel}</option>`;
        if(j.data && Array.isArray(j.data)) {
            j.data.forEach(x => { 
                let kode = x.kode_group1 || x.kode_group2;
                let nama = x.deskripsi_group1 || x.nama_ao || kode;
                html += `<option value="${kode}">${kode} - ${nama}</option>`; 
            });
        }
        el.innerHTML = html;
    } catch(e) {}
}

async function updateFilterUI() {
    const areaVal = document.getElementById('opt_area').value;
    const lblSub = document.getElementById('lbl_sub');
    const optSub = document.getElementById('opt_sub_main');

    if(areaVal === 'ALL') {
        lblSub.innerText = "KORWIL";
        optSub.innerHTML = `
            <option value="ALL">ALL KORWIL</option>
            <option value="SEMARANG">SEMARANG</option>
            <option value="SOLO">SOLO</option>
            <option value="BANYUMAS">BANYUMAS</option>
            <option value="PEKALONGAN">PEKALONGAN</option>
        `;
        fetchRekap();
    } else {
        lblSub.innerText = "KANKAS";
        optSub.innerHTML = `<option value="ALL">Memuat...</option>`;

        const cabang = areaVal.replace('CAB-', '');

        await loadDropdownAPI(
            'kode_kankas',
            cabang,
            'opt_sub_main',
            'ALL KANKAS'
        );

        fetchRekap();
    }
}

// --- FETCH & RENDER DATA UTAMA ---
async function fetchRekap(){
    const loading = document.getElementById('loadingUtama');
    const tbody = document.getElementById('bodyUtama');
    
    const harian = document.getElementById('harian_date').value;
    const closing = document.getElementById('closing_date').value;
    const areaVal = document.getElementById('opt_area').value;
    const subVal = document.getElementById('opt_sub_main').value;
    // const aoVal = document.getElementById('opt_ao_main').value;

    if(abortMain) abortMain.abort();
    abortMain = new AbortController();

    loading.classList.remove('hidden');
    tbody.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-slate-400 italic">Sedang mengambil data...</td></tr>`;
    rekapDataCache = [];
    sortCol = ''; // reset sort setiap fetch baru

    try {
        let payload = { type: "rekap_realisasi_growth", closing_date: closing, harian_date: harian };
        
        if(areaVal === 'ALL') {
            if(subVal !== 'ALL') payload.korwil = subVal;
        } else {
            payload.kode_kantor = areaVal.replace('CAB-', '');
            if(subVal !== 'ALL') payload.kode_kankas = subVal;
            // if(aoVal !== 'ALL') payload.kode_ao = aoVal;
        }

        const res = await apiCall(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload), signal: abortMain.signal });
        const json = await res.json();
        
        if(json.status !== 200) throw new Error(json.message);

        rekapDataCache = json.data?.data || json.data || [];
        rekapGtCache = json.data?.grand_total || json.grand_total || {};

        renderHeaderClickable();
        processAndRenderTable(rekapDataCache, rekapGtCache);

    } catch(err) {
        if(err.name !== 'AbortError') tbody.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-red-500 font-bold">Error: ${err.message}</td></tr>`;
    } finally { loading.classList.add('hidden'); }
}

function renderHeaderClickable() {
    const thKode = document.getElementById('th_kode');
    const thNama = document.getElementById('th_nama');
    
    thKode.innerHTML = `<div class="flex items-center justify-center">KODE${getSortIcon('kode_kantor')}</div>`;
    thNama.innerHTML = `<div class="flex items-center">NAMA AREA${getSortIcon('nama_kantor')}</div>`;
    
    const heads = document.querySelectorAll('.head-lapis-2 th');
    heads[0].innerHTML = `NOA${getSortIcon('noa_realisasi')}`;
    heads[1].innerHTML = `NOMINAL${getSortIcon('total_realisasi')}`;
    heads[2].innerHTML = `NOA${getSortIcon('noa_restruck')}`;
    heads[3].innerHTML = `NOMINAL${getSortIcon('total_restruck')}`;
    heads[4].innerHTML = `PELUNASAN${getSortIcon('pelunasan')}`;
    heads[5].innerHTML = `ANGSURAN${getSortIcon('angsuran_murni')}`;
    heads[6].innerHTML = `TOT RUNOFF${getSortIcon('total_run_off')}`;
}

window.sortMainData = function(column) {
    if (!rekapDataCache || rekapDataCache.length === 0) return;
    
    if (sortCol === column) { sortAsc = !sortAsc; } 
    else { sortCol = column; sortAsc = true; }

    rekapDataCache.sort((a, b) => {
        let fieldA = a[column];
        let fieldB = b[column];

        if (!isNaN(parseFloat(fieldA)) && isFinite(fieldA)) {
            return sortAsc ? parseFloat(fieldA) - parseFloat(fieldB) : parseFloat(fieldB) - parseFloat(fieldA);
        } else {
            fieldA = String(fieldA || '').toLowerCase();
            fieldB = String(fieldB || '').toLowerCase();
            if (fieldA < fieldB) return sortAsc ? -1 : 1;
            if (fieldA > fieldB) return sortAsc ? 1 : -1;
            return 0;
        }
    });

    renderHeaderClickable();
    processAndRenderTable(rekapDataCache, rekapGtCache);
};

function processAndRenderTable(rows, gt) {
    const tbody = document.getElementById('bodyUtama');
    if(rows.length === 0){ tbody.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-slate-400 italic">Tidak ada transaksi.</td></tr>`; return; }

    let html = '';
    rows.forEach(r => {
        let nGrowth = parseFloat(r.growth || 0);
        let gColor = nGrowth >= 0 ? 'text-blue-700 bg-blue-50/20' : 'text-red-600 bg-red-50/20';

        // Hanya kolom yang > 0 yang bisa di klik (kursor dan hover styling)
        const curReal = parseInt(r.noa_realisasi) > 0 ? 'cursor-pointer hover:bg-blue-100' : '';
        const clkReal = parseInt(r.noa_realisasi) > 0 ? `onclick="openDetailModal('${r.kode_kantor}', 110, '${r.nama_kantor}')"` : '';

        const curRes = parseInt(r.noa_restruck) > 0 ? 'cursor-pointer hover:bg-purple-100' : '';
        const clkRes = parseInt(r.noa_restruck) > 0 ? `onclick="openDetailModal('${r.kode_kantor}', 109, '${r.nama_kantor}')"` : '';

        html += `
            <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[40px] md:h-[46px]">
                <td class="freeze-col-1 text-center font-mono font-bold text-slate-500 w-[50px] md:w-[60px] border-r border-slate-100">${r.kode_kantor || '-'}</td>
                <td class="freeze-col-2 text-left font-bold text-slate-700 truncate pl-3 border-r border-slate-100 w-[120px] md:w-[150px]" title="${r.nama_kantor}">${r.nama_kantor}</td>
                
                <td class="text-center text-blue-700 bg-blue-50/10 font-bold border-r border-slate-100 transition ${curReal}" ${clkReal}>${fmt(r.noa_realisasi)}</td>
                <td class="text-right text-blue-800 bg-blue-50/10 font-mono pr-2 border-r border-slate-200 transition ${curReal}" ${clkReal}>${fmt(r.total_realisasi)}</td>
                
                <td class="text-center text-purple-700 bg-purple-50/10 font-bold border-r border-slate-100 transition ${curRes}" ${clkRes}>${fmt(r.noa_restruck || 0)}</td>
                <td class="text-right text-purple-800 bg-purple-50/10 font-mono pr-2 border-r border-slate-200 transition ${curRes}" ${clkRes}>${fmt(r.total_restruck || 0)}</td>
                
                <td class="text-right text-emerald-700 font-mono pr-2 border-r border-slate-100">${fmt(r.pelunasan)}</td>
                <td class="text-right text-blue-700 font-mono pr-2 border-r border-slate-100">${fmt(r.angsuran_murni)}</td>
                <td class="text-right text-orange-700 bg-orange-50/10 font-mono pr-2 border-r border-slate-200">${fmt(r.total_run_off)}</td>
                <td class="text-right font-mono font-extrabold pr-3 ${gColor}">${fmt(nGrowth)}</td>
            </tr>
        `;
    });
    tbody.innerHTML = html;

    if(!gt) return;
    let gtGrowth = parseFloat(gt.growth || 0);
    let gtColor = gtGrowth >= 0 ? 'text-blue-800 bg-blue-100/40' : 'text-red-700 bg-red-100/40';

    document.getElementById('rowTotalAtas').innerHTML = `
        <th class="freeze-col-1 text-center text-blue-900 font-extrabold border-r border-blue-300">ALL</th>
        <th class="freeze-col-2 text-left font-extrabold text-blue-900 pl-3 border-r border-blue-300">GRAND TOTAL</th>
        <th class="text-center text-blue-900 bg-blue-100/30 font-extrabold border-r border-blue-300">${fmt(gt.noa_realisasi)}</th>
        <th class="text-right text-blue-900 bg-blue-100/30 font-mono font-bold pr-2 border-r border-blue-300">${fmt(gt.total_realisasi)}</th>
        <th class="text-center text-purple-900 bg-purple-100/30 font-extrabold border-r border-blue-300">${fmt(gt.noa_restruck || 0)}</th>
        <th class="text-right text-purple-900 bg-purple-100/30 font-mono font-bold pr-2 border-r border-blue-300">${fmt(gt.total_restruck || 0)}</th>
        <th class="text-right text-emerald-800 font-mono font-bold pr-2 border-r border-blue-300">${fmt(gt.pelunasan)}</th>
        <th class="text-right text-blue-800 font-mono font-bold pr-2 border-r border-blue-300">${fmt(gt.angsuran_murni)}</th>
        <th class="text-right text-orange-900 bg-orange-100/30 font-mono font-bold pr-2 border-r border-blue-300">${fmt(gt.total_run_off)}</th>
        <th class="text-right font-mono font-black pr-3 ${gtColor}">${fmt(gtGrowth)}</th>
    `;
}

window.exportExcelRekap = function() {
    if(rekapDataCache.length === 0) return alert("Tidak ada data untuk diexport.");
    let csv = "Kode\tNama Kantor\tNOA Realisasi\tNominal Realisasi\tNOA Restruck\tNominal Restruck\tPelunasan\tAngsuran Murni\tTotal Run Off\tGrowth Net\n";
    rekapDataCache.forEach(r => {
        csv += `'${r.kode_kantor}\t${r.nama_kantor}\t${r.noa_realisasi}\t${Math.round(r.total_realisasi)}\t${r.noa_restruck || 0}\t${Math.round(r.total_restruck || 0)}\t${Math.round(r.pelunasan)}\t${Math.round(r.angsuran_murni)}\t${Math.round(r.total_run_off)}\t${Math.round(r.growth)}\n`;
    });
    const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
    const a = document.createElement('a');
    a.href = window.URL.createObjectURL(blob);
    a.download = `Rekap_Growth_Kredit.xls`;
    a.click();
};

// --- LOGIC MODAL DETAIL ---
window.openDetailModal = function(kode_area, kode_trans, nama_area) {
    const areaVal = document.getElementById('opt_area').value;
    const subVal = document.getElementById('opt_sub_main').value;
    // const aoVal  = document.getElementById('opt_ao_main').value;

    detailParamsMob = {
        type: "detail_realisasi_growth",
        closing_date: document.getElementById('closing_date').value,
        harian_date: document.getElementById('harian_date').value,
        kode_trans: kode_trans
    };

    // Mapping Filter
    if(areaVal === 'ALL') {
        if(subVal === 'ALL') {
            // Jika pilih di grand total ALL
            if (kode_area.length === 3) detailParamsMob.kode_kantor = kode_area; // breakdown cabang
        } else {
            detailParamsMob.korwil = subVal;
            detailParamsMob.kode_kantor = kode_area; // Kankas code is sent to kode_kankas actually
        }
    } else {
        detailParamsMob.kode_kantor = areaVal.replace('CAB-','');
        detailParamsMob.kode_kankas = kode_area; // karena di breakdown jadi kankas
        // if(aoVal !== 'ALL') detailParamsMob.kode_ao = aoVal;
    }

    detailPageMob = 1;

    document.getElementById('modalDetailMob').classList.remove('hidden');
    document.getElementById('badgeBucketDetail').innerText = kode_trans === 110 ? 'REALISASI BARU' : 'RESTRUKTURISASI';
    document.getElementById('badgeBucketDetail').className = kode_trans === 110 
        ? 'text-[9px] md:text-sm bg-blue-600 text-white px-2 py-0.5 md:px-2.5 rounded-md md:rounded-full shadow-sm ml-1 font-mono shrink-0'
        : 'text-[9px] md:text-sm bg-purple-600 text-white px-2 py-0.5 md:px-2.5 rounded-md md:rounded-full shadow-sm ml-1 font-mono shrink-0';
        
    document.getElementById('subTitleDetail').innerText = `Area: ${nama_area}`;
    document.getElementById('search_nasabah').value = '';
    
    fetchDetailMob();
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
            tbody.innerHTML = `<tr><td colspan="7" class="py-20 text-center text-slate-400 italic text-[10px] md:text-sm">Tidak ada data detail.</td></tr>`;
            return;
        }

        let html = '';
        list.forEach(row => {
            let alamatPendek = row.alamat && row.alamat.length > 25 ? row.alamat.substring(0, 25) + '...' : (row.alamat||'-');

            html += `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[40px] md:h-[48px] group">
                    <td class="mod-td-rek hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 font-mono text-[9.5px] md:text-[11px] text-slate-500 border-r border-slate-100">${row.no_rekening}</td>
                    <td class="mod-td-nas px-2 md:px-4 py-1.5 md:py-2 font-bold text-[9.5px] md:text-[11px] text-slate-700 truncate border-r border-slate-100" title="${row.nama_nasabah}">${row.nama_nasabah}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-slate-500 text-[9.5px] md:text-[11px] border-r border-slate-100 whitespace-nowrap text-center" title="${row.alamat}">${alamatPendek}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-[9px] md:text-[11px] text-slate-500 border-r border-slate-100 truncate max-w-[100px]" title="${row.nama_ao}">${row.nama_ao||'-'}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-[9px] md:text-[11px] text-slate-500 border-r border-slate-100 truncate max-w-[100px]" title="${row.nama_kankas}">${row.nama_kankas||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-center font-mono text-[9.5px] md:text-[11px] text-blue-700 bg-blue-50/30 border-r border-blue-100">${row.tgl_realisasi}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-medium text-[9.5px] md:text-[12px] text-slate-500 border-r border-slate-100">${fmt(row.plafond)}</td>
                </tr>`;
        });
        tbody.innerHTML = html;

        document.getElementById('pageInfoDetail').innerText = `Hal ${detailPageMob} dari ${totalPages} (${fmt(totalRecords)} Data)`;
        document.getElementById('btnPrevDetail').disabled = detailPageMob <= 1;
        document.getElementById('btnNextDetail').disabled = detailPageMob >= totalPages;
        filterTableDetail();

    } catch(e){
        tbody.innerHTML = `<tr><td colspan="7" class="py-16 text-center text-red-500 font-bold uppercase tracking-widest">Gagal mengambil detail.</td></tr>`;
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

        let csv = `No Rekening\tNama Nasabah\tAO\tKankas\tTgl Realisasi\tPlafond\n`;
        rows.forEach(x => {
            csv += `'${x.no_rekening}\t${x.nama_nasabah}\t${x.nama_ao||''}\t${x.nama_kankas||''}\t${x.tgl_realisasi}\t${Math.round(x.plafond)}\n`;
        });

        const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
        const a = document.createElement('a'); a.href = window.URL.createObjectURL(blob);
        a.download = `Detail_Realisasi_${detailParamsMob.kode_kantor||'ALL'}.xls`; a.click();
    } catch(e) { alert("Gagal export data."); } finally { btn.innerHTML = txt; btn.disabled = false; }
}

window.closeModalMob = function(){ document.getElementById('modalDetailMob').classList.add('hidden'); }
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModalMob(); });
</script>
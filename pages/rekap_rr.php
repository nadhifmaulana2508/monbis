<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  /* Animasi Modal */
  @keyframes scaleUp { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp 0.2s ease-out forwards; }

  /* ========================================================
     🔥 MAGIC STICKY TABLE (FIX OVERLAP BAKI DEBET & NAMA) 🔥
     ======================================================== */
  
  /* Hindari background tembus pandang di Safari/Webkit Chrome */
  #tabelRR th, #tabelRR td, #tableExportRR th, #tableExportRR td { background-clip: padding-box; } 

  /* 1. Kunci Tinggi dan Posisi Header Tabel Utama */
  #tabelRR thead th { position: sticky !important; z-index: 40; cursor: pointer; transition: background 0.2s; }
  #tabelRR thead th:hover { filter: brightness(0.95); }
  
  .rr-row-1 th { top: 0 !important; height: 36px; box-shadow: inset 0 -1px 0 #d7dee8; }
  .rr-row-2 th { top: 36px !important; height: 34px; box-shadow: inset 0 -1px 0 #d7dee8; }
  .rr-row-tot th { top: 70px !important; height: 42px; box-shadow: inset 0 -1px 0 #d7dee8; z-index: 42 !important; cursor: default; }
  .rr-row-tot th:hover { filter: none; }
  
  @media (min-width: 768px) {
      .rr-row-1 th { height: 44px; }
      .rr-row-2 th { top: 44px !important; height: 40px; }
      .rr-row-tot th { top: 84px !important; height: 48px; }
  }

  /* 2. Kunci Posisi Kiri Sticky (Untuk NAMA KANTOR & KODE) */
  .sticky-left-1 { position: sticky !important; left: 0 !important; }
  .sticky-left-2 { position: sticky !important; left: 0 !important; }
  @media (min-width: 768px) { .sticky-left-2 { left: 80px !important; } }

  /* 3. Tumpukan Z-Index Perpotongan Kiri & Atas (Harus Paling Atas) */
  #tabelRR thead th.sticky-left-1, #tabelRR thead th.sticky-left-2 { z-index: 60 !important; background-color: #dcedc8 !important; }
  #tabelRR thead tr.rr-row-tot th.sticky-left-1, #tabelRR thead tr.rr-row-tot th.sticky-left-2 { z-index: 62 !important; background-color: #eff6ff !important; box-shadow: inset -1px 0 0 #d7dee8, inset 0 -1px 0 #d7dee8; }

  /* 4. Tumpukan Z-Index Data Kiri (Harus Di Atas Data Scroll) */
  #tabelRR tbody td.sticky-left-1, #tabelRR tbody td.sticky-left-2 { 
      z-index: 30 !important; 
      background-color: #ffffff !important; 
      box-shadow: inset -1px 0 0 #e2e8f0; 
  }
  
  /* Hover Effect Tabel Utama */
  #bodyRekap tr:hover td { background-color: #f8fafc !important; }
  #bodyRekap tr:hover td.sticky-left-1, #bodyRekap tr:hover td.sticky-left-2 { background-color: #f8fafc !important; }

  /* ========================================================
     🔥 TABEL MODAL DETAIL RR 🔥
     ======================================================== */
  #tableExportRR th { height: 46px; background-color: #f1f5f9 !important; box-shadow: inset 0 -1px 0 #cbd5e1; top: 0 !important; position: sticky !important; z-index: 40; cursor: pointer; transition: background 0.2s; }
  #tableExportRR th:hover { background-color: #e2e8f0 !important; }
  @media (min-width: 768px) { #tableExportRR th { height: 48px; } }

  /* Kunci Lebar Modal Sticky (Responsif Hide Rekening) */
  .mod-freeze-rek, .mod-td-rekening { position: sticky !important; left: 0 !important; min-width: 100px; max-width: 100px;}
  .mod-freeze-nas, .mod-td-nasabah { position: sticky !important; left: 0 !important; min-width: 160px; max-width: 160px;}
  @media (min-width: 768px) { 
      .mod-freeze-rek, .mod-td-rekening { min-width: 120px; max-width: 120px; }
      .mod-freeze-nas, .mod-td-nasabah { left: 120px !important; min-width: 250px; max-width: 250px;} 
  }

  /* Z-Index Hierarchy Modal */
  #tableExportRR th.mod-freeze-rek, #tableExportRR th.mod-freeze-nas { z-index: 60 !important; background-color: #f1f5f9 !important; }
  #tableExportRR tbody td.mod-td-rekening, #tableExportRR tbody td.mod-td-nasabah { z-index: 30 !important; background-color: #ffffff !important; box-shadow: inset -1px 0 0 #e2e8f0; }

  /* Hover Effect Modal Detail */
  #bodyModalRR tr:hover td { background-color: #f8fafc !important; }
  #bodyModalRR tr:hover td.mod-td-rekening, #bodyModalRR tr:hover td.mod-td-nasabah { background-color: #f8fafc !important; }

  /* Form Inputs */
  .inp { border:1px solid #cbd5e1; border-radius:6px; padding:0 8px; background:#fff; outline:none; transition: border 0.2s;}
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
  .inp:disabled { background-color: #f1f5f9; color: #64748b; font-weight: 700; cursor: not-allowed; }
  .lbl { font-size:9px; color:#475569; font-weight:800; margin-bottom:2px; text-transform:uppercase; letter-spacing:0.05em; display:block; white-space: nowrap;}
  @media (min-width: 768px) { .lbl { font-size:11px; margin-bottom:4px; } .inp { border-radius: 8px; padding:0 12px; } }
  .field { display:flex; flex-direction:column; }
  
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition: transform 0.2s;}
  .btn-icon:hover { transform:translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }

  /* HIDE DATEPICKER ICON */
  input[type="date"]::-webkit-inner-spin-button, input[type="date"]::-webkit-calendar-picker-indicator { display: none; -webkit-appearance: none; }
  input[type="date"] { -moz-appearance: textfield; }

  .rr-header-card { background:#fff; border:1px solid #cbd5e1; border-radius:14px; box-shadow:0 1px 2px rgba(15,23,42,.05); }
  .rr-info-popover { position:absolute; left:20px; top:72px; width:360px; max-width:calc(100vw - 32px); background:#fff; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 20px 40px rgba(15,23,42,.16); z-index:200; overflow:hidden; }
  .rr-info-box { border:1px solid #cbd5e1; border-radius:10px; background:#f8fafc; padding:10px 12px; }
  @media (max-width: 767px) {
    .rr-info-popover { left:12px; top:62px; width:calc(100vw - 24px); }
  }


  /* Header RR: samakan ketebalan garis header */
  #tabelRR thead th { border-color:#d7dee8 !important; }
  #tabelRR .rr-row-1 th,
  #tabelRR .rr-row-2 th,
  #tabelRR .rr-row-tot th {
      box-shadow: inset 0 -1px 0 #d7dee8 !important;
      border-bottom-width: 1px !important;
  }
  #tabelRR thead tr.rr-row-tot th.sticky-left-1,
  #tabelRR thead tr.rr-row-tot th.sticky-left-2 {
      box-shadow: inset -1px 0 0 #d7dee8, inset 0 -1px 0 #d7dee8 !important;
  }

</style>

<div class="max-w-[1920px] mx-auto px-2 md:px-4 py-4 md:py-6 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">
  
  <div class="flex-none mb-3 md:mb-4 w-full shrink-0">
    <div class="relative rr-header-card px-3 md:px-5 py-3 md:py-4">
      <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3 md:gap-4 w-full">
        <div class="flex items-center justify-between w-full xl:w-auto shrink-0">
          <div class="flex items-center gap-2 md:gap-3 min-w-0">
            <span class="p-1.5 md:p-2.5 bg-blue-600 rounded-lg text-white shadow-sm shrink-0">
              <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v12"></path></svg>
            </span>
            <div class="min-w-0">
              <div class="flex items-center gap-2 min-w-0">
                <h1 class="text-[15px] md:text-2xl font-extrabold text-slate-800 tracking-tight leading-none truncate">Rekap Repayment Rate</h1>
                <button type="button" onclick="toggleInfoRR()" class="w-4 h-4 md:w-5 md:h-5 rounded-full bg-blue-500 text-white flex items-center justify-center text-[10px] md:text-xs font-black hover:bg-blue-600 transition shrink-0" title="Informasi RR">i</button>
              </div>
              <p class="text-[9px] md:text-sm text-slate-500 italic mt-1">*RR = Total Baki Debet (Lancar) / Seluruh Baki Debet</p>
            </div>
          </div>

          <button type="button" onclick="toggleMainFilter()" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] whitespace-nowrap ml-2 shrink-0">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Filter
          </button>
        </div>

        <div id="filterWrapperMain" class="hidden xl:flex w-full xl:w-auto transition-all duration-300">
          <form id="formFilter" class="w-full xl:w-auto flex flex-col sm:flex-row sm:items-end gap-2 sm:gap-3" onsubmit="event.preventDefault(); fetchRekap();">
            <div class="flex flex-wrap md:flex-nowrap items-end gap-2 w-full xl:w-auto">
              <div class="field flex-1 min-w-[120px] md:min-w-[130px]">
                <label class="lbl text-slate-700">CLOSING (M-1)</label>
                <input type="date" id="closing_date" onchange="fetchRekap()" class="inp w-full text-[10px] md:text-sm font-semibold h-[32px] md:h-[38px] px-2 md:px-3 text-slate-700 cursor-pointer bg-slate-50" required onclick="try{this.showPicker()}catch(e){}">
              </div>
              <div class="field flex-1 min-w-[120px] md:min-w-[130px]">
                <label class="lbl text-slate-700">ACTUAL (HARIAN)</label>
                <input type="date" id="harian_date" onchange="fetchRekap()" class="inp w-full text-[10px] md:text-sm font-semibold h-[32px] md:h-[38px] px-2 md:px-3 text-slate-700 cursor-pointer bg-slate-50" required onclick="try{this.showPicker()}catch(e){}">
              </div>
              <div class="field flex-1 min-w-[120px] md:min-w-[140px]">
                <label class="lbl text-slate-700">TIPE SALDO</label>
                <select id="tipe_saldo_rr" class="inp bg-slate-50 text-[10px] md:text-sm font-bold h-[32px] md:h-[38px] px-2 md:px-3 text-slate-700 cursor-pointer w-full" onchange="fetchRekap()">
                  <option value="baki_debet">BAKI DEBET</option>
                  <option value="saldo_bank">SALDO BANK</option>
                </select>
              </div>
              <div class="field flex-1 min-w-[180px] md:min-w-[220px]">
                <label class="lbl text-slate-700">AREA / CABANG</label>
                <select id="opt_kantor" class="inp bg-slate-50 text-[10px] md:text-sm font-bold h-[32px] md:h-[38px] px-2 md:px-3 text-slate-700 cursor-pointer w-full truncate" onchange="fetchRekap()">
                  <option value="">Loading...</option>
                </select>
              </div>
              <button type="button" onclick="exportExcelRekap()" class="btn-icon h-[32px] md:h-[38px] w-[36px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm shrink-0" title="Download Excel">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="md:w-[18px] md:h-[18px]"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline></svg>
              </button>
            </div>
          </form>
        </div>
      </div>

      <div id="infoRR" class="rr-info-popover hidden">
        <div class="px-4 py-3 border-b border-slate-100">
          <h3 class="text-sm font-black text-slate-900">Informasi RR</h3>
        </div>
        <div class="px-4 py-3 text-[11px] md:text-xs text-slate-700 leading-relaxed space-y-2">
          <p><b>Repayment Rate (RR)</b> adalah rasio kualitas pembayaran yang membandingkan <b>saldo lancar</b> terhadap <b>seluruh saldo outstanding</b>.</p>
          <div class="rr-info-box"><b>M-1</b>: posisi data pada closing bulan sebelumnya.</div>
          <div class="rr-info-box"><b>Actual</b>: posisi data harian / aktual pada tanggal yang dipilih.</div>
          <div class="rr-info-box"><b>Tipe Saldo</b>: Baki Debet atau Saldo Bank. Rekening lancar dihitung dari kolektibilitas L dan hari menunggak 0.</div>
          <div class="rr-info-box"><b>Delta</b>: detail migrasi RR, yaitu rekening yang pada closing masih L dan hari menunggak 0, lalu pada actual menjadi L dengan hari menunggak lebih dari 0.</div>
          <div class="rr-info-box"><b>Rumus RR</b>: Total Saldo Lancar / Seluruh Saldo Outstanding sesuai tipe saldo yang dipilih.</div>
          <div class="rr-info-box"><b>Status Pembayaran</b>: OTP bayar tepat waktu, Telat bayar setelah jatuh tempo, Belum Jatuh Tempo belum wajib bayar, Belum Bayar sudah lewat jatuh tempo.</div>
          <div class="rr-info-box"><b>Hari Menunggak</b>: selisih tanggal actual dengan tgl jatuh tempo. <b>Tunggakan</b>: tunggakan pokok + tunggakan bunga.</div>
          <div class="pt-1 border-t border-slate-200 font-bold text-slate-900">Semakin tinggi persentase RR, semakin baik kualitas repayment pada area tersebut.</div>
        </div>
      </div>
    </div>
  </div>

  <div class="flex-1 min-h-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm relative flex flex-col">
    
    <div id="loadingRekap" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
        <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
        <span>Menyiapkan Matriks...</span>
    </div>

    <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table class="min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="tabelRR">
        <thead class="uppercase bg-slate-50 text-slate-600 font-bold select-none" id="headRR">
          </thead>
        <tbody id="bodyRekap" class="divide-y divide-slate-100 bg-white group-tbody text-[10px] md:text-sm"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="modalDetailRR" class="fixed inset-0 hidden z-[9999] flex items-end md:items-center justify-center p-0 sm:p-4">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalRR()"></div>
  <div class="relative bg-white w-full h-[95vh] md:h-[92vh] max-w-[1600px] rounded-t-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up">
    
    <div class="flex flex-col bg-white border-b shrink-0 w-full z-50">
        <div class="flex flex-row items-center justify-between px-3 py-2.5 md:px-4 md:py-3 gap-2 w-full overflow-x-auto no-scrollbar">
            
            <div class="flex-1 min-w-[180px] shrink-0" id="modal-title-container">
              <h3 class="font-bold text-slate-800 flex items-center gap-1.5 text-[12px] md:text-xl leading-none truncate">
                  <span class="w-1.5 md:w-2 h-4 md:h-6 bg-blue-600 rounded-full hidden md:block shrink-0"></span> 
                  <span id="modalTitleRR" class="truncate">Detail Rekap RR</span>
              </h3>
              <p class="text-[9px] md:text-sm text-slate-500 mt-1 md:ml-4 font-mono font-medium leading-none truncate" id="modalSubTitleRR">...</p>
            </div>
            
            <div class="flex flex-row items-center gap-1.5 md:gap-2 shrink-0">
                <div class="relative w-[120px] md:w-[200px] shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" id="search_nasabah" onkeyup="filterTableDetail()" class="w-full pl-8 pr-3 py-1.5 h-[32px] bg-slate-50 border border-slate-200 rounded-lg text-[10px] md:text-xs outline-none focus:border-blue-500 focus:bg-white transition-all placeholder-slate-400 font-medium" placeholder="Cari nama...">
                </div>
                
                <button type="button" onclick="document.getElementById('modalFilterWrapper').classList.toggle('hidden'); document.getElementById('modalFilterWrapper').classList.toggle('block')" class="md:hidden h-[32px] w-[32px] bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 rounded-lg flex items-center justify-center transition shrink-0">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </button>
                
                <button onclick="closeModalRR()" class="w-[32px] h-[32px] flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-500 hover:text-white text-red-500 transition font-bold text-xl leading-none shrink-0">&times;</button>
            </div>
        </div>

        <div id="modalFilterWrapper" class="hidden md:block w-full border-t border-slate-100 md:border-none transition-all">
            <div class="flex flex-row items-center justify-end gap-1.5 md:gap-2 px-3 pb-2.5 md:px-4 md:pb-3 overflow-x-auto no-scrollbar">
                <select id="opt_kankas_modal" class="inp px-1 md:px-2 h-[32px] w-[100px] md:w-[130px] text-[10px] md:text-xs font-bold text-blue-800 bg-blue-50/50 border-blue-200 outline-none shrink-0 cursor-pointer" onchange="loadDetailPage(1)">
                    <option value="">Semua Kankas</option>
                </select>

                <select id="opt_ao_modal" class="inp px-1 md:px-2 h-[32px] w-[100px] md:w-[130px] text-[10px] md:text-xs font-bold text-slate-700 bg-slate-50 border-slate-200 outline-none shrink-0 cursor-pointer" onchange="loadDetailPage(1)">
                    <option value="">Semua AO</option>
                </select>

                <select id="status_bayar_modal" class="inp px-1 md:px-2 h-[32px] w-[115px] md:w-[140px] text-[10px] md:text-xs font-bold text-slate-700 bg-slate-50 border-slate-200 outline-none shrink-0 cursor-pointer" onchange="loadDetailPage(1)" title="Status Bayar">
                    <option value="all">Semua Bayar</option>
                    <option value="sudah_bayar">Sudah Bayar</option>
                    <option value="belum_bayar">Belum Bayar</option>
                </select>

                <select id="status_tunggakan_modal" class="inp px-1 md:px-2 h-[32px] w-[115px] md:w-[145px] text-[10px] md:text-xs font-bold text-slate-700 bg-slate-50 border-slate-200 outline-none shrink-0 cursor-pointer" onchange="loadDetailPage(1)" title="Status Tunggakan">
                    <option value="all">Tunggakan All</option>
                    <option value="nol">Tunggakan = 0</option>
                    <option value="lebih">Tunggakan > 0</option>
                </select>

                <select id="status_pembayaran_modal" class="inp px-1 md:px-2 h-[32px] w-[140px] md:w-[175px] text-[10px] md:text-xs font-bold text-slate-700 bg-slate-50 border-slate-200 outline-none shrink-0 cursor-pointer" onchange="loadDetailPage(1)" title="Status Pembayaran">
                    <option value="ALL">Semua Status</option>
                    <option value="OTP">OTP</option>
                    <option value="TELAT">Telat</option>
                    <option value="BELUM_JATUH_TEMPO">Belum Jatuh Tempo</option>
                    <option value="BELUM_BAYAR">Belum Bayar</option>
                </select>
                
                <button onclick="downloadExcelFull()" class="btn-icon bg-emerald-600 hover:bg-emerald-700 text-white px-2.5 md:px-3 h-[32px] rounded-lg shadow-sm shrink-0" title="Export Excel">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="ml-1.5 text-[10px] md:text-xs font-bold uppercase tracking-wider hidden sm:inline">Export</span>
                </button>
            </div>
        </div>

    </div>

    <div class="flex-1 overflow-auto bg-slate-50 relative p-0 md:p-3 custom-scrollbar">
      <div id="loadingModalRR" class="hidden absolute inset-0 bg-white/90 z-40 flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
         <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2 md:mb-3"></div>
         <span class="text-[10px] md:text-sm font-bold uppercase tracking-widest">Memuat Detail...</span>
      </div>
      
      <table class="w-max min-w-full text-center md:text-left text-slate-700 border-separate border-spacing-0 md:border border-slate-200 md:rounded-xl shadow-sm bg-white table-fixed" id="tableExportRR">
        <thead id="headModalRR" class="text-[9px] md:text-xs text-slate-600 uppercase bg-slate-100 font-bold tracking-wider select-none"></thead>
        <tbody id="bodyModalRR" class="divide-y divide-slate-100 bg-white modal-tbody text-[9.5px] md:text-xs"></tbody>
      </table>
    </div>

    <div class="px-3 py-2.5 md:px-5 md:py-4 border-t bg-white flex justify-between items-center shrink-0">
      <span class="text-[9px] md:text-sm font-bold text-slate-600 bg-slate-100 px-2 md:px-3 py-1 rounded-md md:rounded-lg" id="pageInfoRR">0 Data</span>
      <div class="flex gap-1 md:gap-2">
          <button id="btnPrevRR" onclick="changePageDetail(-1)" class="px-2.5 md:px-4 py-1.5 md:py-2 bg-white border border-slate-300 rounded-md md:rounded-lg text-[9px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">« Prev</button>
          <button id="btnNextRR" onclick="changePageDetail(1)" class="px-2.5 md:px-4 py-1.5 md:py-2 bg-white border border-slate-300 rounded-md md:rounded-lg text-[9px] md:text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-400 disabled:opacity-50 transition shadow-sm">Next »</button>
      </div>
    </div>
  </div>
</div>

<script>
  const API_URL  = './api/rr/'; 
  const API_DATE = './api/date/';
  const API_KODE_URL = './api/kode/'; 
  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Math.round(Number(n||0)));

  let abortRekap;
  let rekapDataCache = []; 
  let rekapGtCache = null;
  let detailDataCache = [];
  let userKodeGlobal = '000'; 

  // 🔥 STATE SORTING 🔥
  let sortCol = '';
  let sortAsc = true;
  let sortDetailCol = '';
  let sortDetailAsc = true;

  let currentDetailParams = {};
  let currentDetailPage = 1;
  let currentDetailTotalPages = 1;
  let currentMode = 'NORMAL'; 
  const detailLimit = 20;
  const getTipeSaldoRR = () => document.getElementById('tipe_saldo_rr')?.value || 'baki_debet';
  const getTipeSaldoLabelRR = () => getTipeSaldoRR() === 'saldo_bank' ? 'SALDO BANK' : 'BAKI DEBET';

  const getSortIcon = (col, currentCol, asc) => {
      if (col !== currentCol) return '<span class="opacity-30 text-[8px] md:text-[10px] ml-1.5 font-sans">↕</span>';
      return asc ? '<span class="text-blue-600 ml-1.5 text-[10px] md:text-[11px] font-sans">▲</span>' : '<span class="text-blue-600 ml-1.5 text-[10px] md:text-[11px] font-sans">▼</span>';
  };

  // 🔥 FUNGSI TOGGLE FILTER UTAMA HP 🔥
  function toggleMainFilter() {
      const el = document.getElementById('filterWrapperMain');
      if(el.classList.contains('hidden')) {
          el.classList.remove('hidden');
          el.classList.add('flex');
      } else {
          el.classList.add('hidden');
          el.classList.remove('flex');
      }
  }


  function toggleInfoRR() {
      const el = document.getElementById('infoRR');
      if (!el) return;
      el.classList.toggle('hidden');
  }

  document.addEventListener('click', function(e) {
      const info = document.getElementById('infoRR');
      if (!info) return;
      const btn = e.target.closest('[onclick="toggleInfoRR()"]');
      const box = e.target.closest('#infoRR');
      if (!btn && !box) info.classList.add('hidden');
  });

  window.addEventListener('DOMContentLoaded', async () => {
      const user = (window.getUser && window.getUser()) || null;
      userKodeGlobal = (user?.kode ? String(user.kode).padStart(3,'0') : '000');

      const now = new Date();
      try {
          const r = await fetch(API_DATE); const j = await r.json();
          const d = j.data || null;
          if (d) {
              document.getElementById('closing_date').value = d.last_closing;
              document.getElementById('harian_date').value = d.last_created;
          } else {
              document.getElementById('closing_date').value = `${now.getFullYear() - 1}-12-31`;
              document.getElementById('harian_date').value = now.toISOString().split('T')[0];
          }
      } catch(e) { 
          document.getElementById('closing_date').value = `${now.getFullYear() - 1}-12-31`;
          document.getElementById('harian_date').value = now.toISOString().split('T')[0]; 
      }

      await populateKantor(userKodeGlobal);
      setupRekapDetailClickRR();
      fetchRekap();
  });

  async function apiCall(url, payload, signal = null) {
      const opt = { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) };
      if (signal) opt.signal = signal;
      const res = await fetch(url, opt);
      return await res.json();
  }

  const attrRR = (v) => String(v ?? '').replace(/[&<>"']/g, ch => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
  }[ch])).replace(/\r?\n/g, ' ');

  function setupRekapDetailClickRR() {
      const table = document.getElementById('tabelRR');
      if (!table || table.dataset.detailClickBound === '1') return;
      table.dataset.detailClickBound = '1';
      table.addEventListener('click', (e) => {
          const target = e.target.closest('[data-detail-rr="1"]');
          if (!target) return;
          initModalDetail(
              target.dataset.tgl || 'ALL',
              target.dataset.status || 'ALL',
              target.dataset.kode || null,
              target.dataset.nama || '',
              target.dataset.label || 'Detail',
              target.dataset.kankas || null
          );
      });
  }

  async function loadKankasModalDropdown(kodeKantor = null) {
      const el = document.getElementById('opt_kankas_modal');
      if (!el) return;
      el.innerHTML = '<option value="">Semua Kankas</option>';
      if (!kodeKantor) return;

      try {
          const res = await apiCall(API_KODE_URL, { type: 'kode_kankas', kode_kantor: kodeKantor });
          let h = '<option value="">Semua Kankas</option>';
          (res.data || []).forEach(x => {
              const kode = x.kode_group1 || '';
              const nama = x.deskripsi_group1 || kode;
              h += `<option value="${kode}">${nama}</option>`;
          });
          el.innerHTML = h;
      } catch (err) {
          console.error('Gagal load kankas RR', err);
      }
  }

  async function loadAOModalDropdown(kodeKantor = null) {
      const el = document.getElementById('opt_ao_modal');
      if (!el) return;
      el.innerHTML = '<option value="">Semua AO</option>';
      if (!kodeKantor) return;

      try {
          const res = await apiCall(API_KODE_URL, { type: 'kode_ao_kredit', kode_kantor: kodeKantor });
          let h = '<option value="">Semua AO</option>';
          (res.data || []).forEach(x => {
              const kode = x.kode_group2 || '';
              const nama = x.nama_ao || kode;
              h += `<option value="${kode}">${nama}</option>`;
          });
          el.innerHTML = h;
      } catch (err) {
          console.error('Gagal load AO RR', err);
      }
  }

  async function populateKantor(uKode) {
    const el = document.getElementById('opt_kantor'); if(!el) return;
    if (uKode !== '000') { 
        try {
            const res = await apiCall(API_KODE_URL, { type:'kode_kantor' });
            const myKantor = (res.data||[]).find(x => String(x.kode_kantor).padStart(3,'0') === uKode);
            const nama = myKantor ? myKantor.nama_kantor : `CABANG ${uKode}`;
            el.innerHTML = `<option value="${uKode}">${uKode} - ${nama}</option>`;
        } catch(e) {
            el.innerHTML = `<option value="${uKode}">CABANG ${uKode}</option>`; 
        }
        el.value = uKode;
        el.disabled = true; 
        return; 
    }
    try {
        const res = await apiCall(API_KODE_URL, { type: 'kode_kantor' });
        let h = '<option value="">KONSOLIDASI</option>';
        if(res.data) res.data.filter(x => x.kode_kantor !== '000').forEach(x => { h += `<option value="${x.kode_kantor}">${x.kode_kantor} - ${x.nama_kantor}</option>`; });
        el.innerHTML = h;
    } catch { el.innerHTML = '<option value="">KONSOLIDASI</option>'; }
  }

  // 🔥 SETUP HEADER UTAMA (KUNCI NAMA KANTOR) 🔥
  function setupHeaderRR(userKode) {
      const th = document.getElementById('headRR');
      const saldoLabel = getTipeSaldoLabelRR();
      let thHtml = `<tr class="rr-row-1 text-[10px] md:text-sm">`;

      if (userKode === '000') {
          thHtml += `
            <th rowspan="2" class="hidden md:table-cell sticky-left-1 w-[60px] md:w-[80px] border-r border-b border-slate-200 align-middle bg-[#dcedc8] text-slate-800 text-center" onclick="sortData('kode', 'string')">
                <div class="flex items-center justify-center">KODE ${getSortIcon('kode', sortCol, sortAsc)}</div>
            </th>
            <th rowspan="2" class="sticky-left-2 min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px] border-r border-b border-white align-middle text-left pl-3 md:pl-5 bg-[#dcedc8] text-slate-800 truncate" onclick="sortData('nama', 'string')">
                <div class="flex items-center justify-start">NAMA KANTOR ${getSortIcon('nama', sortCol, sortAsc)}</div>
            </th>
          `;
      } else {
          thHtml += `
            <th rowspan="2" class="sticky-left-1 min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px] border-r border-b border-white align-middle text-left pl-3 md:pl-5 bg-[#dcedc8] text-slate-800 truncate" onclick="sortData('nama', 'string')">
                <div class="flex items-center justify-start">NAMA KANTOR ${getSortIcon('nama', sortCol, sortAsc)}</div>
            </th>
          `;
      }

      thHtml += `
            <th colspan="2" class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 align-middle bg-[#dcedc8] text-slate-800 text-[10px] md:text-sm text-center">M-1</th>
            <th colspan="2" class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 align-middle bg-[#dcedc8] text-slate-800 text-[10px] md:text-sm text-center">ACTUAL</th>
            <th colspan="2" class="px-2 md:px-4 py-1.5 md:py-2 border-b border-slate-200 align-middle bg-[#dcedc8] text-slate-800 text-[10px] md:text-sm text-center">DELTA</th>
          </tr>
          <tr class="rr-row-2 text-[8.5px] md:text-[10px] tracking-wider">
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('m1_lancar_os', 'number')">
                <div class="flex items-center justify-end">${saldoLabel} ${getSortIcon('m1_lancar_os', sortCol, sortAsc)}</div>
            </th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('m1_pct', 'number')">
                <div class="flex items-center justify-center">% ${getSortIcon('m1_pct', sortCol, sortAsc)}</div>
            </th>
            
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('cur_lancar_os', 'number')">
                <div class="flex items-center justify-end">${saldoLabel} ${getSortIcon('cur_lancar_os', sortCol, sortAsc)}</div>
            </th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('cur_pct', 'number')">
                <div class="flex items-center justify-center">% ${getSortIcon('cur_pct', sortCol, sortAsc)}</div>
            </th>

            <th class="px-2 md:px-4 py-1.5 md:py-2 border-r border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('delta_os_lancar', 'number')">
                <div class="flex items-center justify-end">SELISIH ${saldoLabel} ${getSortIcon('delta_os_lancar', sortCol, sortAsc)}</div>
            </th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-slate-200 bg-[#eef2f6]" onclick="sortData('delta_pct', 'number')">
                <div class="flex items-center justify-center">% ${getSortIcon('delta_pct', sortCol, sortAsc)}</div>
            </th>
          </tr>
          <tr class="rr-row-tot font-bold text-[10px] md:text-sm bg-slate-100 sticky-total border-b border-slate-200" id="rowTotalRRAtas"></tr>
      `;
      th.innerHTML = thHtml;
  }

  function getTrafficLightColor(pct) {
      if (pct < 50) return 'text-rose-600 font-bold';    
      if (pct < 60) return 'text-amber-500 font-bold';  
      return 'text-blue-700 font-bold';                
  }

  window.sortData = function(col, type) {
      if (!rekapDataCache || rekapDataCache.length === 0) return;

      if (sortCol === col) {
          sortAsc = !sortAsc;
      } else {
          sortCol = col;
          sortAsc = true;
      }

      rekapDataCache.sort((a, b) => {
          let valA = a[col];
          let valB = b[col];

          if (type === 'number') {
              valA = parseFloat(valA) || 0;
              valB = parseFloat(valB) || 0;
              return sortAsc ? valA - valB : valB - valA;
          } else {
              valA = String(valA || '').toLowerCase();
              valB = String(valB || '').toLowerCase();
              if (valA < valB) return sortAsc ? -1 : 1;
              if (valA > valB) return sortAsc ? 1 : -1;
              return 0;
          }
      });

      setupHeaderRR(userKodeGlobal);
      renderTableBodyRR(rekapDataCache, rekapGtCache);
  }

  document.getElementById('formFilter')?.addEventListener('submit', e => { e.preventDefault(); fetchRekap(); });

  async function fetchRekap() {
      const l = document.getElementById('loadingRekap');
      const tb = document.getElementById('bodyRekap');
      
      if(abortRekap) abortRekap.abort();
      abortRekap = new AbortController();

      l.classList.remove('hidden'); 
      
      const colSpan = userKodeGlobal === '000' ? 8 : 7;
      tb.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-20 text-slate-400 italic text-xs md:text-base">Sedang mengambil data...</td></tr>`;
      
      rekapDataCache = [];
      rekapGtCache = null;
      sortCol = ''; 
      sortAsc = true;

      try {
          const payload = {
              type: 'rr',
              closing_date: document.getElementById('closing_date').value,
              harian_date: document.getElementById('harian_date').value,
              kode_kantor: document.getElementById('opt_kantor').value || null,
              hitung_berdasarkan: getTipeSaldoRR()
          };

          const json = await apiCall(API_URL, payload, abortRekap.signal);
          if(json.status !== 200) throw new Error(json.message);

          rekapDataCache = json.data?.data || [];
          rekapGtCache = json.data?.grand_total || {};

          setupHeaderRR(userKodeGlobal);

          if(rekapDataCache.length === 0) {
              tb.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-20 text-slate-400 italic text-xs md:text-base">Tidak ada data.</td></tr>`;
              return;
          }

          renderTableBodyRR(rekapDataCache, rekapGtCache);

      } catch(e) { 
          if(e.name!=='AbortError') {
              tb.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-16 text-red-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">Error: ${e.message}</td></tr>`;
          }
      } finally { l.classList.add('hidden'); }
  }

  function renderTableBodyRR(rows, gt) {
      const tb = document.getElementById('bodyRekap');
      const trTot = document.getElementById('rowTotalRRAtas');
      let html = '';

      rows.forEach(r => {
          const dNoaClass = r.delta_noa < 0 ? 'text-rose-600' : 'text-slate-500';
          const dOsClass  = r.delta_os_lancar < 0 ? 'text-rose-600' : 'text-slate-700';
          const dPctClass = r.delta_pct < 0 ? 'text-rose-600' : 'text-slate-700';
          
          const pM1Class  = getTrafficLightColor(r.m1_pct);
          const pCurClass = getTrafficLightColor(r.cur_pct);

          const rowKode = attrRR(r.kode || '');
          const rowNama = attrRR(r.nama || '');
          const detailKode = userKodeGlobal === '000' ? rowKode : userKodeGlobal;
          const detailKankas = userKodeGlobal === '000' ? '' : rowKode;
          const actualDetailAttr = `data-detail-rr="1" data-status="ALL" data-kode="${detailKode}" data-kankas="${detailKankas}" data-nama="${rowNama}" data-label="Actual" title="Klik detail Actual"`;
          const deltaDetailAttr = `data-detail-rr="1" data-status="TOTAL_BAYAR" data-kode="${detailKode}" data-kankas="${detailKankas}" data-nama="${rowNama}" data-label="Delta" title="Klik detail Delta"`;
          let rowHtml = `<tr class="transition h-[42px] md:h-[52px] border-b border-slate-100 hover:bg-slate-50">`;
          
          if (userKodeGlobal === '000') {
              // 🔥 HIDE KODE CABANG DI MOBILE 🔥
              rowHtml += `
                <td class="hidden md:table-cell sticky-left-1 px-2 md:px-4 py-2 border-r border-slate-100 font-semibold text-blue-700 z-20 shadow-[inset_-1px_0_0_#e2e8f0] text-center text-[10px] md:text-sm">${r.kode}</td>
                <td class="sticky-left-2 px-3 md:px-5 py-2 border-r border-slate-100 font-bold text-slate-700 text-left truncate z-20 shadow-[inset_-1px_0_0_#e2e8f0] text-[10px] md:text-sm min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px]" title="${r.nama}">${r.nama}</td>
              `;
          } else {
              rowHtml += `
                <td class="sticky-left-1 px-3 md:px-5 py-2 border-r border-slate-100 font-bold text-slate-700 text-left truncate z-20 shadow-[inset_-1px_0_0_#e2e8f0] text-[10px] md:text-sm min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px]" title="${r.nama}">${r.nama}</td>
              `;
          }

          rowHtml += `
                <td class="px-2 md:px-4 py-2 border-r border-slate-100 text-right">
                    <div class="font-medium text-slate-700 text-[10px] md:text-sm">${fmt(r.m1_lancar_os)}</div>
                    <div class="text-[8px] md:text-[10px] text-slate-400 mt-0.5">NOA: <span class="font-bold text-slate-500">${fmt(r.m1_all_noa)}</span></div>
                </td>
                <td class="px-2 md:px-4 py-2 border-r border-slate-100 text-center text-[10px] md:text-sm ${pM1Class}">${r.m1_pct}%</td>
                
                <td ${actualDetailAttr} class="px-2 md:px-4 py-2 border-r border-slate-100 text-right bg-blue-50/20 cursor-pointer hover:bg-blue-100/70 transition">
                    <div class="font-medium text-blue-800 text-[10px] md:text-sm">${fmt(r.cur_lancar_os)}</div>
                    <div class="text-[8px] md:text-[10px] text-blue-400 mt-0.5">NOA: <span class="font-bold text-blue-500">${fmt(r.cur_all_noa)}</span></div>
                </td>
                <td ${actualDetailAttr} class="px-2 md:px-4 py-2 border-r border-slate-100 text-center text-[10px] md:text-sm ${pCurClass} bg-blue-50/20 cursor-pointer hover:bg-blue-100/70 transition">${r.cur_pct}%</td>
                
                <td ${deltaDetailAttr} class="px-2 md:px-4 py-2 border-r border-slate-100 text-right cursor-pointer hover:bg-amber-50 transition">
                    <div class="font-medium ${dOsClass} text-[10px] md:text-sm">${fmt(r.delta_os_lancar)}</div>
                    <div class="text-[8px] md:text-[10px] text-slate-400 mt-0.5">NOA: <span class="font-bold ${dNoaClass}">${fmt(r.delta_noa)}</span></div>
                </td>
                <td ${deltaDetailAttr} class="px-2 md:px-4 py-2 text-center font-bold text-[10px] md:text-sm ${dPctClass} cursor-pointer hover:bg-amber-50 transition">${r.delta_pct}%</td>
            </tr>`;
          html += rowHtml;
      });
      tb.innerHTML = html;

      if(gt && Object.keys(gt).length > 0) {
          const gtDNoaClass = gt.delta_noa < 0 ? 'text-rose-700' : 'text-blue-600';
          const gtDOsClass  = gt.delta_os_lancar < 0 ? 'text-rose-700' : 'text-blue-900';
          const gtDPctClass = gt.delta_pct < 0 ? 'text-rose-700' : 'text-blue-900';

          const gtM1Color  = getTrafficLightColor(gt.m1_pct);
          const gtCurColor = getTrafficLightColor(gt.cur_pct);

          const totalKode = attrRR(document.getElementById('opt_kantor')?.value || '');
          const actualTotalAttr = `data-detail-rr="1" data-status="ALL" data-kode="${totalKode}" data-nama="TOTAL" data-label="Actual Total" title="Klik detail Actual Total"`;
          const deltaTotalAttr = `data-detail-rr="1" data-status="TOTAL_BAYAR" data-kode="${totalKode}" data-nama="TOTAL" data-label="Delta Total" title="Klik detail Delta Total"`;
          let gtHtml = '';
          if (userKodeGlobal === '000') {
              gtHtml += `
                  <th class="hidden md:table-cell sticky-left-1 px-2 md:px-4 border-r border-blue-200 text-center text-blue-900 bg-[#eff6ff] !important text-[10px] md:text-sm">-</th>
                  <th class="sticky-left-2 px-3 md:px-5 border-r border-blue-200 text-left text-blue-900 tracking-wide font-extrabold text-[11px] md:text-base bg-[#eff6ff] !important min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px] truncate" title="GRAND TOTAL">GRAND TOTAL</th>
              `;
          } else {
              gtHtml += `
                  <th class="sticky-left-1 px-3 md:px-5 border-r border-blue-200 text-left text-blue-900 tracking-wide font-extrabold text-[11px] md:text-base bg-[#eff6ff] !important min-w-[120px] max-w-[120px] md:min-w-[200px] md:max-w-[200px] truncate" title="TOTAL KANTOR">TOTAL KANTOR</th>
              `;
          }

          gtHtml += `
              <th class="px-2 md:px-4 border-r border-blue-200 text-right align-middle bg-[#eff6ff]">
                  <div class="font-bold text-[10px] md:text-sm text-blue-900">${fmt(gt.m1_lancar_os)}</div>
                  <div class="text-[8px] md:text-[10px] text-blue-500 mt-0.5 font-normal">NOA: <span class="font-bold text-blue-700">${fmt(gt.m1_all_noa)}</span></div>
              </th>
              <th class="px-2 md:px-4 border-r border-blue-200 text-center align-middle font-bold text-[10px] md:text-sm ${gtM1Color} bg-[#eff6ff]">${gt.m1_pct}%</th>
              
              <th ${actualTotalAttr} class="px-2 md:px-4 border-r border-blue-200 text-right align-middle bg-[#eff6ff] cursor-pointer hover:bg-blue-100 transition">
                  <div class="font-bold text-[10px] md:text-sm text-blue-900">${fmt(gt.cur_lancar_os)}</div>
                  <div class="text-[8px] md:text-[10px] text-blue-500 mt-0.5 font-normal">NOA: <span class="font-bold text-blue-700">${fmt(gt.cur_all_noa)}</span></div>
              </th>
              <th ${actualTotalAttr} class="px-2 md:px-4 border-r border-blue-200 text-center align-middle font-bold text-[10px] md:text-sm ${gtCurColor} bg-[#eff6ff] cursor-pointer hover:bg-blue-100 transition">${gt.cur_pct}%</th>
               
              <th ${deltaTotalAttr} class="px-2 md:px-4 border-r border-blue-200 text-right align-middle bg-[#eff6ff] cursor-pointer hover:bg-amber-50 transition">
                  <div class="font-bold text-[10px] md:text-sm ${gtDOsClass}">${fmt(gt.delta_os_lancar)}</div>
                  <div class="text-[8px] md:text-[10px] text-slate-500 mt-0.5 font-normal">NOA: <span class="font-bold ${gtDNoaClass}">${fmt(gt.delta_noa)}</span></div>
              </th>
              <th ${deltaTotalAttr} class="px-2 md:px-4 text-center align-middle font-bold text-[10px] md:text-sm ${gtDPctClass} bg-[#eff6ff] cursor-pointer hover:bg-amber-50 transition">${gt.delta_pct}%</th>
          `;
          trTot.innerHTML = gtHtml;
          trTot.classList.remove('cursor-pointer');
          trTot.title = '';
          trTot.onclick = null;
      }
  }

  window.exportExcelRekap = function() {
      if(!rekapDataCache || rekapDataCache.length === 0) return alert("Tidak ada data rekap untuk didownload.");

      let csv = "";
      const saldoLabel = getTipeSaldoLabelRR();
      if (userKodeGlobal === '000') {
          csv = `Kode\tNama Kantor\tM-1 ${saldoLabel}\tM-1 NOA\tM-1 %\tActual ${saldoLabel}\tActual NOA\tActual %\tDelta ${saldoLabel}\tDelta NOA\tDelta %\n`;
      } else {
          csv = `Nama Kantor\tM-1 ${saldoLabel}\tM-1 NOA\tM-1 %\tActual ${saldoLabel}\tActual NOA\tActual %\tDelta ${saldoLabel}\tDelta NOA\tDelta %\n`;
      }
      
      rekapDataCache.forEach(r => {
          if (userKodeGlobal === '000') {
              csv += `'${r.kode}\t${r.nama||''}\t`;
          } else {
              csv += `${r.nama||''}\t`;
          }
          csv += `${Math.round(r.m1_lancar_os)}\t${r.m1_all_noa}\t${r.m1_pct}%\t${Math.round(r.cur_lancar_os)}\t${r.cur_all_noa}\t${r.cur_pct}%\t${Math.round(r.delta_os_lancar)}\t${r.delta_noa}\t${r.delta_pct}%\n`;
      });

      const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
      const a = document.createElement('a');
      a.href = window.URL.createObjectURL(blob);
      a.download = `Rekap_RR_${userKodeGlobal}_${document.getElementById("harian_date").value}.xls`; 
      a.click();
  }

  // ==========================================
  // 🔥 MODAL DETAIL LOGIC 🔥
  // ==========================================
  function formatWA(phone) {
      if (!phone) return null;
      let cleaned = phone.replace(/\D/g, ''); 
      if (cleaned.startsWith('0')) { cleaned = '62' + cleaned.substring(1); } 
      else if (cleaned.startsWith('8')) { cleaned = '62' + cleaned; }
      if (cleaned.length < 10) return null;
      return cleaned;
  }

  function createWABtn(phone, nama, norek, totung) {
      const formatted = formatWA(phone);
      if (!formatted) return `<span class="text-slate-400 font-mono text-[9px] md:text-sm">${phone || '-'}</span>`;
      
      // 🔥 FIX 5: Pesan di-comment, langsung redirect ke WA murni 🔥
      // const msg = `Yth. Bapak/Ibu *${nama}*,\n\nKami menginformasikan bahwa terdapat tagihan angsuran kredit pada rekening *${norek}* dengan total tunggakan sebesar *Rp ${fmt(totung)}*.\n\nMohon untuk segera melakukan pembayaran angsuran.\n\n_(Jika Bapak/Ibu sudah melakukan pembayaran, mohon abaikan pesan ini)_\n\nTerima kasih.`;
      // const waUrl = `https://wa.me/${formatted}?text=${encodeURIComponent(msg)}`;
      const waUrl = `https://wa.me/${formatted}`;
      
      return `
          <a href="${waUrl}" target="_blank" class="inline-flex items-center gap-1 md:gap-1.5 px-2 md:px-3 py-1 md:py-1.5 bg-emerald-50 hover:bg-emerald-500 hover:text-white text-emerald-600 rounded-md md:rounded-lg border border-emerald-200 transition font-bold text-[10px] md:text-xs" title="Hubungi WhatsApp">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="md:w-[16px] md:h-[16px]"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.319-.883-.665-1.479-1.488-1.653-1.787-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
              WA
          </a>
      `;
  }

  function renderModalHeaderRR() {
      const mHead = document.getElementById('headModalRR');
      
      if (currentMode === 'NORMAL') {
          mHead.innerHTML = `
              <tr class="modal-head-1 mod-row-1">
                  <th class="mod-freeze-rek hidden md:table-cell px-2 md:px-3 border-b border-r border-slate-300 rounded-tl-lg text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-start md:justify-center">REKENING ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="mod-freeze-nas px-2 md:px-4 border-b border-r border-slate-300 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start md:justify-center">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[200px] md:w-[350px] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start md:justify-center">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_hp', 'string')">
                      <div class="flex items-center justify-center">NO HP (WA) ${getSortIcon('no_hp', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kankas', 'string')">
                      <div class="flex items-center justify-center">KANKAS ${getSortIcon('kankas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[110px] md:w-[150px] text-center text-blue-700 cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_jatuh_tempo', 'string')">
                      <div class="flex items-center justify-center">TGL JT ${getSortIcon('tgl_jatuh_tempo', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[110px] md:w-[150px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_pembayaran_code', 'string')">
                      <div class="flex items-center justify-center">STATUS BAYAR ${getSortIcon('status_pembayaran_code', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('trx_bulan_ini', 'number')">
                      <div class="flex items-center justify-end">BAYAR ${getSortIcon('trx_bulan_ini', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[70px] md:w-[95px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('hari_menunggak_jt', 'number')">
                      <div class="flex items-center justify-center">HARI ${getSortIcon('hari_menunggak_jt', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('jml_pinjaman', 'number')">
                      <div class="flex items-center justify-end">PLAFOND ${getSortIcon('jml_pinjaman', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-blue-50 text-blue-700 border-b border-r border-blue-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-blue-100 transition select-none" onclick="sortDetailRR('os_m1', 'number')">
                      <div class="flex items-center justify-end">TARGET (M-1) ${getSortIcon('os_m1', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-green-50 text-green-700 border-b border-r border-green-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('os_curr', 'number')">
                      <div class="flex items-center justify-end">ACTUAL (CURR) ${getSortIcon('os_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-red-50 text-red-700 border-b border-r border-red-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-red-100 transition select-none" onclick="sortDetailRR('totung', 'number')">
                      <div class="flex items-center justify-end">TUNGGAKAN ${getSortIcon('totung', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[50px] md:w-[70px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('dpd_curr', 'number')">
                      <div class="flex items-center justify-center">DPD ${getSortIcon('dpd_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[100px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tabungan', 'number')">
                      <div class="flex items-center justify-end">TABUNGAN ${getSortIcon('tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_tabungan', 'string')">
                      <div class="flex items-center justify-center">STAT TAB ${getSortIcon('status_tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-slate-300 w-[100px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_ket', 'string')">
                      <div class="flex items-center justify-center">DLL ${getSortIcon('status_ket', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
              <tr id="rowTotalDetailAtas" class="modal-head-2 mod-row-tot"></tr>
          `;
      } else {
          mHead.innerHTML = `
              <tr class="modal-head-1 mod-row-1">
                  <th class="mod-freeze-nas px-2 md:px-4 border-b border-r border-slate-300 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start md:justify-center">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[200px] md:w-[350px] text-left md:text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start md:justify-center">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[100px] md:w-[150px] text-center text-blue-700 cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-center">REK LAMA ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('plafon_lama', 'number')">
                      <div class="flex items-center justify-end">PLAFOND LAMA ${getSortIcon('plafon_lama', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-blue-50 text-blue-700 border-b border-r border-blue-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-blue-100 transition select-none" onclick="sortDetailRR('os_lunas', 'number')">
                      <div class="flex items-center justify-end">OS M-1 ${getSortIcon('os_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 border-b border-r border-slate-300 w-[80px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_lunas', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 bg-green-50 text-green-700 border-b border-r border-green-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('rek_baru', 'string')">
                      <div class="flex items-center justify-center">REK BARU ${getSortIcon('rek_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 bg-green-50 text-green-700 border-b border-r border-green-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('plafond_baru', 'number')">
                      <div class="flex items-center justify-end">PLAFOND BARU ${getSortIcon('plafond_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 bg-green-50 text-green-700 border-b border-green-200 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-green-100 transition select-none" onclick="sortDetailRR('tgl_baru', 'string')">
                      <div class="flex items-center justify-center">TGL REALISASI ${getSortIcon('tgl_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
              <tr id="rowTotalDetailAtas" class="modal-head-2 mod-row-tot"></tr>
          `;
      }
  }

  window.sortDetailRR = function(col, type) {
      if (!detailDataCache || detailDataCache.length === 0) return;

      if (sortDetailCol === col) {
          sortDetailAsc = !sortDetailAsc;
      } else {
          sortDetailCol = col;
          sortDetailAsc = true;
      }

      detailDataCache.sort((a, b) => {
          let valA = a[col];
          let valB = b[col];

          if (type === 'number') {
              valA = parseFloat(valA) || 0;
              valB = parseFloat(valB) || 0;
              return sortDetailAsc ? valA - valB : valB - valA;
          } else {
              valA = String(valA || '').toLowerCase();
              valB = String(valB || '').toLowerCase();
              if (valA < valB) return sortDetailAsc ? -1 : 1;
              if (valA > valB) return sortDetailAsc ? 1 : -1;
              return 0;
          }
      });

      renderModalHeaderRR();
      renderTableDetailBodyRR(detailDataCache);
  }

  async function initModalDetail(tgl, status, kodeKantor = null, namaArea = '', label = 'Detail', kodeKankasAwal = null) {
      currentMode = 'NORMAL';
      const branch = kodeKantor || document.getElementById('opt_kantor').value || null;
      
      await loadKankasModalDropdown(branch);
      if (kodeKankasAwal && document.getElementById('opt_kankas_modal')) {
          document.getElementById('opt_kankas_modal').value = kodeKankasAwal;
      }
      const kankas = kodeKankasAwal || document.getElementById('opt_kankas_modal').value || null; 
      
      await loadAOModalDropdown(branch);
      const ao = document.getElementById('opt_ao_modal').value || null;
      
      currentDetailParams = { 
          type: 'detail_rekap_rr', 
          closing_date: document.getElementById('closing_date').value, 
          harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, 
          kode_kankas: kankas,
          kode_ao: ao,
          tgl_tagih: tgl, 
          status: status, 
          hitung_berdasarkan: getTipeSaldoRR(),
          status_bayar: 'all',
          status_tunggakan: 'all',
          status_pembayaran: 'ALL',
          search: '',
          limit: detailLimit 
      };

      const titleArea = namaArea ? ` - ${namaArea}` : '';
      document.getElementById('modalTitleRR').textContent = `${label} Rekap RR${titleArea}`;
      document.getElementById('modalSubTitleRR').textContent = `${getTipeSaldoLabelRR()} | Lancar = kolektibilitas L dan hari menunggak 0`;
      document.getElementById('modalDetailRR').classList.remove('hidden');
      
      document.getElementById('search_nasabah').value = '';
      if (document.getElementById('status_bayar_modal')) document.getElementById('status_bayar_modal').value = 'all';
      if (document.getElementById('status_tunggakan_modal')) document.getElementById('status_tunggakan_modal').value = 'all';
      if (document.getElementById('status_pembayaran_modal')) document.getElementById('status_pembayaran_modal').value = 'ALL';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      loadDetailPage(1);
  }
  window.initModalDetail = initModalDetail;

  async function initModalLunas(tgl) {
      currentMode = 'LUNAS';
      const branch = document.getElementById('opt_kantor').value || null;

      await loadKankasModalDropdown(branch);
      const kankas = document.getElementById('opt_kankas_modal').value || null;
      
      await loadAOModalDropdown(branch);
      const ao = document.getElementById('opt_ao_modal').value || null;

      currentDetailParams = { 
          type: 'detail_lunas_rr', 
          closing_date: document.getElementById('closing_date').value, 
          harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, 
          kode_kankas: kankas,
          kode_ao: ao,
          tgl_tagih: tgl, 
          limit: detailLimit 
      };

      document.getElementById('modalTitleRR').textContent = `Detail Pelunasan (Tgl ${tgl})`;
      document.getElementById('modalSubTitleRR').textContent = `Cek Refinancing vs Prospek`;
      document.getElementById('modalDetailRR').classList.remove('hidden');
      
      document.getElementById('search_nasabah').value = '';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      loadDetailPage(1);
  }

  let searchDetailTimerRR = null;
  window.filterTableDetail = function() {
      const input = document.getElementById("search_nasabah");
      currentDetailParams.search = input ? input.value.trim() : '';
      clearTimeout(searchDetailTimerRR);
      searchDetailTimerRR = setTimeout(() => loadDetailPage(1), 350);
  }

  async function loadDetailPage(page) {
      const l = document.getElementById('loadingModalRR'); 
      const tb = document.getElementById('bodyModalRR'); 
      const info = document.getElementById('pageInfoRR');
      l.classList.remove('hidden'); tb.innerHTML = '';

      try {
          const kankasModal = document.getElementById('opt_kankas_modal').value;
          currentDetailParams.kode_kankas = kankasModal;

          const aoModal = document.getElementById('opt_ao_modal');
          if(aoModal) {
              currentDetailParams.kode_ao = aoModal.value;
          }
          currentDetailParams.hitung_berdasarkan = getTipeSaldoRR();
          currentDetailParams.status_bayar = document.getElementById('status_bayar_modal')?.value || 'all';
          currentDetailParams.status_tunggakan = document.getElementById('status_tunggakan_modal')?.value || 'all';
          currentDetailParams.status_pembayaran = document.getElementById('status_pembayaran_modal')?.value || 'ALL';

          const payload = { ...currentDetailParams, page: page };
          const res = await apiCall(API_URL, payload);
          if(res.status !== 200) throw new Error(res.message || 'Gagal memuat detail');
          detailDataCache = res.data?.data || [];
          const meta = res.data?.pagination || { total_records:0, total_pages:1 };

          currentDetailPage = page; currentDetailTotalPages = meta.total_pages;

          if(detailDataCache.length === 0) {
              tb.innerHTML = `<tr><td colspan="17" class="py-20 text-center text-slate-500 italic text-xs md:text-base">Tidak ada data detail.</td></tr>`;
              info.innerText = `0 Data`;
          } else {
              sortDetailCol = ''; sortDetailAsc = true;
              renderModalHeaderRR();
              renderTableDetailBodyRR(detailDataCache);

              const start = ((page - 1) * detailLimit) + 1;
              const end = Math.min(page * detailLimit, meta.total_records);
              info.innerText = `Hal ${page} dari ${meta.total_pages} (${fmt(meta.total_records)} Data)`;
          }
          document.getElementById('btnPrevRR').disabled = page <= 1;
          document.getElementById('btnNextRR').disabled = page >= meta.total_pages;
      } catch(err){ 
          console.error(err); 
          tb.innerHTML = `<tr><td colspan="17" class="py-16 text-center text-red-500 font-bold tracking-widest uppercase text-[10px] md:text-sm">Gagal memuat detail</td></tr>`;
      } finally { l.classList.add('hidden'); }
  }

  function getPaymentBadgeRR(r) {
      const code = r.status_pembayaran_code || '';
      const extra = code === 'TELAT' ? ` ${fmt(r.hari_telat)} hr` : (code === 'BELUM_BAYAR' ? ` ${fmt(r.hari_menunggak_jt)} hr` : '');
      if (code === 'OTP') return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 font-black">OTP</span>`;
      if (code === 'TELAT') return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 border border-orange-200 font-black">Telat${extra}</span>`;
      if (code === 'BELUM_JATUH_TEMPO') return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 border border-blue-200 font-black">Belum JT</span>`;
      if (code === 'BELUM_BAYAR') return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 border border-rose-200 font-black">Belum Bayar${extra}</span>`;
      return `<span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-black">-</span>`;
  }

  function renderTableDetailBodyRR(list) {
      const tb = document.getElementById('bodyModalRR');
      let h = '';
      
      list.forEach(r => {
          const aoName = (r.nama_ao || '-').split(' ').slice(0, 2).join(' ');

          if(currentMode === 'NORMAL') {
              let badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">${r.status_ket}</span>`;
              if(r.status_ket==='LANCAR') badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-green-100 text-green-700 border border-green-200">LANCAR</span>`;
              else if(r.status_ket==='MENUNGGAK') badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-red-100 text-red-700 border border-red-200">MENUNGGAK</span>`;
              
              let statTabungan = `<span class="text-red-500 font-bold text-[10px] md:text-xs">Belum Aman</span>`;
              if(r.status_tabungan === 'Aman') statTabungan = `<span class="text-green-600 font-bold text-[10px] md:text-xs">Aman</span>`;

              const btnWa = createWABtn(r.no_hp, r.nama_nasabah, r.no_rekening, r.totung);
              const paymentBadge = getPaymentBadgeRR(r);
              const hariFollowUp = r.status_pembayaran_code === 'TELAT' ? (r.hari_telat || 0) : (r.hari_menunggak_jt || 0);

              // 🔥 ALAMAT FULL TANPA HARDCODE 25 CHAR 🔥
              const alamatLengkap = r.alamat || '-';

              h += `<tr class="transition border-b border-slate-100 h-[40px] md:h-[48px]">
                    <td class="mod-td-rekening hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 font-mono text-[9.5px] md:text-sm text-slate-600 shadow-[1px_0_0_#f1f5f9]">${r.no_rekening}</td>
                    <td class="mod-td-nasabah px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 font-bold text-slate-700 truncate shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] text-[9.5px] md:text-sm" title="${r.nama_nasabah}">${r.nama_nasabah}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-slate-500 text-[9.5px] md:text-sm truncate max-w-[200px] md:max-w-[350px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center">${btnWa}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-mono text-slate-500 text-[9px] md:text-sm">${r.kankas||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-center font-bold text-[9.5px] md:text-sm text-blue-700 truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-mono text-[9.5px] md:text-sm text-slate-500">${r.tgl_jatuh_tempo||'-'}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center text-[9px] md:text-xs">${paymentBadge}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-bold text-blue-700 bg-blue-50/20 text-[9.5px] md:text-sm">${fmt(r.trx_bulan_ini)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-black text-[9.5px] md:text-sm ${hariFollowUp > 0 ? 'text-rose-600' : 'text-slate-500'}">${fmt(hariFollowUp)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-medium text-slate-600 text-[9.5px] md:text-sm">${fmt(r.jml_pinjaman)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-blue-100 text-right font-bold text-blue-700 bg-blue-50/30 text-[9.5px] md:text-sm">${fmt(r.os_m1)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-green-100 text-right font-bold text-green-700 bg-green-50/30 text-[9.5px] md:text-sm">${fmt(r.os_curr)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-red-100 text-right font-bold text-red-600 bg-red-50/30 text-[9.5px] md:text-sm">${fmt(r.totung)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center font-bold text-slate-700 text-[9.5px] md:text-sm">${r.dpd_curr}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-bold text-emerald-600 bg-emerald-50/10 text-[9.5px] md:text-sm">${fmt(r.tabungan)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 text-center">${statTabungan}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-center">${badge}</td>
                </tr>`;
          } else {
              let badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">PROSPEK</span>`;
              if(r.status_lunas === 'REFINANCING / Top Up') badge = `<span class="inline-flex items-center px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[9px] md:text-xs font-bold bg-green-100 text-green-700 border border-green-200">REFINANCING</span>`;
              
              const alamatLengkap = r.alamat || '-';

              h += `<tr class="transition border-b border-slate-100 h-[40px] md:h-[48px]">
                    <td class="mod-td-nasabah px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 font-bold text-slate-700 truncate shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] text-[9.5px] md:text-sm">
                        ${r.nama_nasabah}
                        <div class="text-[8px] md:text-xs text-slate-400 font-mono mt-0.5 font-normal">ID: ${r.nasabah_id}</div>
                    </td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-slate-500 text-[9.5px] md:text-sm truncate max-w-[200px] md:max-w-[350px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-center font-bold text-[9.5px] md:text-sm text-blue-700 truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-100 font-mono text-[9.5px] md:text-sm text-center text-slate-600">${r.no_rekening}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-right font-medium text-slate-600 bg-slate-50/50 text-[9.5px] md:text-sm">${fmt(r.plafon_lama)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-blue-100 text-right font-bold text-blue-700 bg-blue-50/30 text-[9.5px] md:text-sm">${fmt(r.os_lunas)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-slate-100 text-center">${badge}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-green-100 font-mono text-[9.5px] md:text-sm text-center bg-green-50/30 text-green-800 font-bold">${r.rek_baru}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 border-r border-green-100 text-right bg-green-50/30 text-green-700 font-bold text-[9.5px] md:text-sm">${fmt(r.plafond_baru)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center bg-green-50/30 text-[9.5px] md:text-sm font-medium text-green-700">${r.tgl_baru}</td>
                </tr>`;
          }
      });
      tb.innerHTML = h;
  }

  async function downloadExcelFull() {
      const btn = event.target.closest('button'); const txt = btn.innerHTML;
      btn.innerHTML = `<span class="animate-spin inline-block h-3.5 w-3.5 md:h-5 md:w-5 border-2 border-white border-t-transparent rounded-full md:mr-2"></span><span class="hidden md:inline">...</span>`;
      btn.disabled = true;

      try {
          const kankasModal = document.getElementById('opt_kankas_modal').value;
          const aoModal = document.getElementById('opt_ao_modal');
          let kodeAoVal = currentDetailParams.kode_ao;
          if (aoModal) { kodeAoVal = aoModal.value; }

          const payload = {
              ...currentDetailParams,
              kode_kankas: kankasModal,
              kode_ao: kodeAoVal,
              hitung_berdasarkan: getTipeSaldoRR(),
              status_bayar: document.getElementById('status_bayar_modal')?.value || 'all',
              status_tunggakan: document.getElementById('status_tunggakan_modal')?.value || 'all',
              status_pembayaran: document.getElementById('status_pembayaran_modal')?.value || 'ALL',
              page: 1,
              limit: 10000
          };
          const res = await apiCall(API_URL, payload);
          if(res.status !== 200) throw new Error(res.message || 'Export gagal');
          const rows = res.data?.data || [];
          if(rows.length === 0) { alert("Tidak ada data untuk diexport"); return; }

          let csv = "";
          if(currentMode === 'NORMAL') {
              csv = `No Rekening\tNama Nasabah\tAlamat\tNo HP\tKankas\tNama AO\tTgl JT\tStatus Pembayaran\tBayar Bulan Ini\tHari Telat\tHari Menunggak\tPlafond\tTarget (M-1)\tActual (Curr)\tTot Tunggakan\tDPD\tSaldo Tabungan\tStatus Tabungan\tStatus Tagih\n`;
              rows.forEach(r => {
                  csv += `'${r.no_rekening}\t${r.nama_nasabah}\t${r.alamat||''}\t'${r.no_hp||''}\t${r.kankas||''}\t${r.nama_ao}\t${r.tgl_jatuh_tempo}\t${r.status_pembayaran||''}\t${Math.round(r.trx_bulan_ini||0)}\t${r.hari_telat||0}\t${r.hari_menunggak_jt||0}\t${Math.round(r.jml_pinjaman)}\t${Math.round(r.os_m1)}\t${Math.round(r.os_curr)}\t${Math.round(r.totung)}\t${r.dpd_curr}\t${Math.round(r.tabungan)}\t${r.status_tabungan}\t${r.status_ket}\n`;
              });
          } else {
              csv = `Nama Nasabah\tID Nasabah\tAlamat\tNama AO\tRek Lama\tPlafond Lama\tOS Lunas (M-1)\tStatus\tRek Baru\tPlafond Baru\tTgl Realisasi Baru\n`;
              rows.forEach(r => {
                  csv += `${r.nama_nasabah}\t'${r.nasabah_id}\t${r.alamat||''}\t${r.nama_ao}\t'${r.no_rekening}\t${Math.round(r.plafon_lama)}\t${Math.round(r.os_lunas)}\t${r.status_lunas}\t'${r.rek_baru}\t${Math.round(r.plafond_baru)}\t${r.tgl_baru}\n`;
              });
          }

          const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `RR_Detail_${currentMode}_${currentDetailParams.tgl_tagih}.xls`;
          document.body.appendChild(a); a.click(); document.body.removeChild(a);

      } catch(e) { console.error(e); alert("Gagal export data."); } 
      finally { btn.innerHTML = txt; btn.disabled = false; }
  }

  window.changePageDetail = (step) => { const n = currentDetailPage + step; if (n > 0 && n <= currentDetailTotalPages) loadDetailPage(n); }
  window.closeModalRR = () => document.getElementById('modalDetailRR').classList.add('hidden');
  document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModalRR(); });
</script>

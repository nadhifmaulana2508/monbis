<div class="max-w-full mx-auto px-2 md:px-4 py-4 md:py-5 h-[calc(100vh-80px)] flex flex-col bg-slate-50 font-sans relative" id="SD_root">
  
  <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 mb-3 shrink-0">
    <div class="flex items-center gap-2 md:gap-3">
      <div class="bg-blue-600 text-white p-1.5 md:p-2 rounded-lg md:rounded-xl shadow-sm flex items-center justify-center">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="md:w-5 md:h-5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      </div>
      <div>
        <h1 class="text-base md:text-xl font-extrabold text-slate-800 tracking-tight leading-none">Search Debitur</h1>
        <span class="text-[9px] font-bold text-red-600 bg-blue-100  rounded-md">*Create Pipelane + Komitmen</span></span>
        <div class="flex items-center gap-1.5 mt-0.5">
          <span class="text-[9px] font-bold text-blue-600 bg-blue-100 px-1.5 py-0.5 rounded-md">NOA: <span id="SD_sumNoa">0</span></span>
          <span class="text-[9px] font-bold text-emerald-600 bg-emerald-100 px-1.5 py-0.5 rounded-md">BD: <span id="SD_sumBd">0</span></span>
        </div>
      </div>
    </div>

    <div class="flex items-end gap-2 md:gap-3 shrink-0 flex-wrap justify-start md:justify-end w-full md:w-auto">
      
      <div class="flex flex-col gap-1">
          <label class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider ml-1">Closing</label>
          <input type="date" id="SD_closing_date" class="inp w-[115px] md:w-[130px] cursor-pointer text-slate-700 font-bold" title="Tanggal Closing Bulan Lalu">
      </div>
      <div class="flex flex-col gap-1">
          <label class="text-[10px] font-extrabold text-slate-600 uppercase tracking-wider ml-1">Harian</label>
          <input type="date" id="SD_harian_date" class="inp w-[115px] md:w-[130px] cursor-pointer text-blue-700 font-bold" title="Tanggal Data Harian">
      </div>

      <button type="button" onclick="SD_exportExcelAll()" class="h-[32px] px-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg shadow-sm transition flex items-center justify-center gap-1.5 text-xs font-bold" title="Export Excel">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        <span class="hidden md:inline">Excel</span>
      </button>
      <button id="SD_btnToggleFilter" class="h-[32px] px-3 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-lg shadow-sm transition flex items-center justify-center gap-1.5 text-xs font-bold" title="Toggle Filter">
        <svg id="SD_iconToggle" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-300"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
        <span class="hidden md:inline">Filter</span>
      </button>
    </div>
  </div>

  <div id="SD_panelFilter" class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm mb-3 shrink-0 transition-all origin-top">
    <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3 items-end">
      
      <div class="field flex flex-col gap-1">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1">Pencarian</label>
        <input type="text" id="SD_search" placeholder="Nama / Rekening..." class="inp w-full">
      </div>
      <div class="field flex flex-col gap-1">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1">Kantor</label>
        <select id="SD_optKantor" class="inp w-full cursor-pointer"><option value="">Semua</option></select>
      </div>
      <div class="field flex flex-col gap-1">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1">Kankas</label>
        <select id="SD_optKankas" class="w-full cursor-pointer"><option value="">Semua</option></select>
      </div>
      <div class="field flex flex-col gap-1">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1">Kecamatan</label>
        <select id="SD_optKecamatan" class="w-full cursor-pointer"><option value="">Semua</option></select>
      </div>
      <div class="field flex flex-col gap-1">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1">Kelurahan</label>
        <select id="SD_optKelurahan" class="w-full cursor-pointer"><option value="">Semua</option></select>
      </div>
      
      <div class="field flex flex-col gap-1 relative z-20">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1">Kolek</label>
        <select id="SD_optKolek" class="w-full cursor-pointer" multiple placeholder="Semua...">
          <option value="L">L</option><option value="DP">DP</option>
          <option value="KL">KL</option><option value="D">D</option><option value="M">M</option>
        </select>
      </div>

      <div class="field flex flex-col gap-1 relative">
        <div class="flex items-center gap-1 ml-1 mb-0.5">
          <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Status JT</label>
          <div class="relative group inline-block">
             <span class="cursor-help bg-blue-100 text-blue-600 rounded-full w-3.5 h-3.5 flex items-center justify-center text-[8px] font-bold border border-blue-200">!</span>
             <div class="absolute z-[99999] hidden group-hover:block w-72 bg-white text-slate-700 text-[10px] p-3 rounded-lg shadow-2xl border border-slate-200 top-full left-1/2 transform -translate-x-1/2 mt-2 pointer-events-none">
                <p class="font-bold text-blue-600 mb-2 border-b pb-1 text-center">Panduan Status Bayar Baru</p>
                <div class="flex flex-col gap-1.5 text-left">
                  <p><b>Realisasi Baru:</b> Bulan lalu belum ada, bulan ini cair.</p>
                  <p><b>Restruktur:</b> Baki debet sekarang lebih besar dari bulan lalu.</p>
                  <p><b>JT Potensi NPL:</b> Bulan lalu L/DP, bulan ini masuk radar tgl Jatuh Tempo.</p>
                  <p><b>Flow Par:</b> Turun kolek dari L/DP jadi KL/D/M.</p>
                  <p><b>Recovery NPL:</b> Angsuran masuk di akun NPL (KL/D/M).</p>
                  <p><b>Lunas:</b> Baki debet 0 atau akun sudah tutup.</p>
                  <p><b>Byr, Ada Tunggakan:</b> Udah bayar tapi Totung masih > 0.</p>
                  <p><b>OTP (Sesuai JT):</b> Nunggak 0, Kolek L, bayar <= tgl JT.</p>
                  <p><b>OTP (Tdk Sesuai):</b> Nunggak 0, Kolek L, bayar telat dari tgl JT.</p>
                  <p><b>Blm Byr, Belum JT:</b> Belum bayar krn belum waktunya.</p>
                  <p><b>Blm Byr, Lewat JT:</b> Udah lewat tgl JT tapi belum bayar.</p>
                </div>
             </div>
          </div>
        </div>
        <select id="SD_optStatusJT" class="inp w-full cursor-pointer">
          <option value="">Semua Status</option>
          <option value="realisasi_baru">🆕 Realisasi Baru</option>
          <option value="restruktur">🔄 Restruktur</option>
          <option value="jt_potensi_npl">🚨 JT Potensi NPL</option>
          <option value="flow_par">📉 Flow Par</option>
          <option value="recovery_npl">📈 Recovery NPL</option>
          <option value="lunas">🎉 Lunas</option>
          <option value="byr_tunggakan">💸 Byr, Ada Tunggakan</option>
          <option value="otp_sesuai">✅ OTP (Sesuai JT)</option>
          <option value="otp_tidak_sesuai">⚠️ OTP (Tdk Sesuai JT)</option>
          <option value="belum_bayar_belum_jt">⏳ Blm Byr, Belum JT</option>
          <option value="belum_bayar_lewat_jt">❌ Blm Byr, Lewat JT</option>
        </select>
      </div>

      <div class="field flex flex-col gap-1">
        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider ml-1">Totung (Max)</label>
        <input type="number" id="SD_totung" placeholder="0" class="inp w-full">
      </div>
    </div>
  </div>

  <div class="flex-1 min-h-0 relative flex flex-col bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden z-[1]">
    <div id="SD_loading" class="hidden absolute inset-0 bg-white/80 z-50 flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
       <div class="animate-spin h-10 w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-3"></div>
       <span class="text-[10px] font-bold tracking-widest uppercase">Memuat Data...</span>
    </div>

    <div class="flex-1 overflow-auto custom-scrollbar relative" id="SD_tableContainer">
      <table id="SD_table" class="min-w-full text-left text-[11px] table-fixed border-collapse">
        <thead class="bg-slate-50 text-slate-600 uppercase text-[9px] tracking-wider sticky top-0 shadow-sm border-b border-slate-200" style="z-index: 10;">
          <tr>
            <th class="px-2 py-2.5 border-r border-slate-200 text-center hide-mobile sd-col-kc">KC</th>
            <th class="px-2 py-2.5 border-r border-slate-200 text-center hide-mobile sd-col-rek">No Rekening</th>
            <th class="px-3 py-2.5 border-r border-slate-200 sd-col-nas">Nama Debitur</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-40">Alamat</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-28">Kankas</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-32">AO Kredit</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24">Kecamatan</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24">Kelurahan</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-16 text-center">Produk</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-20 text-center">JT</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-12 text-center bg-yellow-50">Kolek Awal</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-12 text-center">Kolek</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-12 text-center">DPD</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-12 text-center">HMP</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-12 text-center">HMB</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-blue-50/50">Plafon</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-blue-50/50">Nilai CKPN</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-emerald-50/50">Baki Debet</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-orange-50/50">T. Pokok</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-orange-50/50">T. Bunga</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-red-50/50">Totung</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right">Tabungan</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-20 text-center bg-slate-100">Tgl Trx Lalu</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-slate-100">Pokok Lalu</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-slate-100">Bunga Lalu</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-20 text-center bg-blue-50">Tgl Trx Skrg</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-blue-50">Bayar Skrg</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-32 text-center bg-blue-50">Status Bayar</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-center bg-purple-50 text-purple-700">Pipeline</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-20 text-center bg-purple-50 text-purple-700">Tindakan</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-28 text-center bg-purple-50 text-purple-700">Plan AO Rem</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-24 text-right bg-purple-50 text-purple-700">Nom PTP</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-20 text-center bg-purple-50 text-purple-700">Tgl PTP</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-28 text-center bg-purple-50 text-purple-700">Status Bucket</th>
            <th class="px-3 py-2.5 border-r border-slate-200 w-32 bg-purple-50 text-purple-700">Keterangan</th>
            <!-- <th class="px-3 py-2.5 w-20 text-center bg-slate-100">Aksi</th> -->
          </tr>
        </thead>
        <tbody id="SD_totalRow" class="sticky top-[33px] font-bold bg-blue-50/95 text-blue-800 border-b-2 border-blue-200 text-[10px] backdrop-blur-sm" style="z-index: 9;"></tbody>
        <tbody id="SD_tbody" class="text-slate-700 divide-y divide-slate-100">
          <tr><td colspan="36" class="px-4 py-12 text-center text-slate-400 font-medium text-xs">Mencari data debitur otomatis...</td></tr>
        </tbody>
      </table>
    </div>

    <div class="bg-slate-50 border-t border-slate-200 p-2.5 flex flex-col md:flex-row items-center justify-between gap-3 shrink-0">
        <span class="text-[10px] md:text-xs text-slate-500 font-medium" id="SD_pageInfo">Total: 0 Debitur</span>
        <div class="flex items-center gap-1.5">
            <button onclick="SD_changePage(-1)" id="SD_btnPrev" class="px-2.5 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100 text-slate-600 disabled:opacity-50 transition text-[10px] font-bold shadow-sm">« Prev</button>
            <span class="text-[10px] md:text-[11px] font-bold text-slate-700 px-2" id="SD_pageCurrent">Hal 1 / 1</span>
            <button onclick="SD_changePage(1)" id="SD_btnNext" class="px-2.5 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100 text-slate-600 disabled:opacity-50 transition text-[10px] font-bold shadow-sm">Next »</button>
        </div>
    </div>
  </div>

  <div id="SD_modalKomitmen" class="fixed inset-0 z-[99999] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[95vh] animate-slide-up">
          <div class="bg-blue-600 px-5 py-3 flex items-center justify-between shrink-0">
              <h2 class="text-white font-bold text-sm flex items-center gap-2">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                  Input Tindakan & Komitmen
              </h2>
              <button type="button" onclick="SD_closeModal()" class="text-white hover:text-blue-200 transition">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </button>
          </div>

          <div class="p-4 md:p-5 overflow-y-auto custom-scrollbar bg-slate-50 flex-1">
              
              <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm mb-3 grid grid-cols-2 md:grid-cols-4 gap-3">
                  <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-500 uppercase">No Rekening</span><span class="text-xs font-bold text-slate-800" id="mod_norek">-</span></div>
                  <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-500 uppercase">Nama Debitur</span><span class="text-xs font-bold text-slate-800 truncate" id="mod_nama" title="">-</span></div>
                  <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-500 uppercase">Kolek Saat Ini</span><span class="text-xs font-bold text-rose-600" id="mod_kolek">-</span></div>
                  <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-500 uppercase">DPD Saat Ini</span><span class="text-xs font-bold text-rose-600" id="mod_dpd">0 Hari</span></div>
              </div>

              <div class="grid grid-cols-2 gap-3 mb-4">
                  <div class="bg-orange-50/50 p-2.5 rounded-lg border border-orange-200 flex flex-col justify-center">
                      <span class="text-[9px] font-bold text-orange-600 uppercase mb-1">Tunggakan Pokok</span>
                      <span class="text-sm font-extrabold text-orange-700" id="mod_tpokok">Rp 0</span>
                      <span class="text-[9px] font-bold text-slate-500 mt-1">HMP: <span class="text-rose-600 font-bold" id="mod_hmp">0 Hari</span></span>
                  </div>
                  <div class="bg-amber-50/50 p-2.5 rounded-lg border border-amber-200 flex flex-col justify-center">
                      <span class="text-[9px] font-bold text-amber-600 uppercase mb-1">Tunggakan Bunga</span>
                      <span class="text-sm font-extrabold text-amber-700" id="mod_tbunga">Rp 0</span>
                      <span class="text-[9px] font-bold text-slate-500 mt-1">HMB: <span class="text-rose-600 font-bold" id="mod_hmb">0 Hari</span></span>
                  </div>
              </div>

              <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 mb-4 flex flex-col md:flex-row items-center justify-between gap-4">
                  <div class="flex-1 w-full text-center">
                      <span class="block text-[9px] font-bold text-blue-600 uppercase mb-1">Bucket Bulan Lalu</span>
                      <div class="bg-white border border-blue-200 px-3 py-1.5 rounded-md text-xs font-bold text-slate-700 shadow-sm" id="mod_bucket_lalu">Loading...</div>
                  </div>
                  <div class="text-blue-300 transform md:rotate-0 rotate-90">
                      <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                  </div>
                  <div class="flex-1 w-full text-center">
                      <span class="block text-[9px] font-bold text-blue-600 uppercase mb-1">Bucket Saat Ini</span>
                      <div class="bg-white border border-blue-200 px-3 py-1.5 rounded-md text-xs font-bold text-slate-700 shadow-sm" id="mod_bucket_skrg">Loading...</div>
                  </div>
                  <div class="flex-1 w-full flex flex-col items-center">
                      <span class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Status Pergerakan</span>
                      <span id="mod_pergerakan" class="px-2 py-1 rounded text-[10px] font-bold bg-slate-200 text-slate-600 uppercase tracking-wider text-center">-</span>
                  </div>
              </div>

              <form id="SD_formKomitmen" class="flex flex-col gap-4">
                  <input type="hidden" id="inp_norek">
                  <input type="hidden" id="inp_kolek">
                  <input type="hidden" id="inp_kode_cabang_mod">

                  <div id="wrap_jatuh_tempo" class="hidden flex-col gap-3 p-3 bg-red-50 border border-red-200 rounded-lg shadow-sm">
                      <h3 class="text-[10px] font-bold text-red-700 uppercase tracking-wider flex items-center gap-1.5">
                          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                          Account Jatuh Tempo (Wajib Lunas)
                      </h3>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <div class="flex flex-col gap-1">
                              <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Tindakan Jatuh Tempo</label>
                              <select id="inp_jt_tindakan" class="inp w-full cursor-pointer border-red-300 focus:border-red-500">
                                  <option value="">-- Pilih --</option>
                                  <option value="LUNAS">LUNAS</option>
                                  <option value="MIGRASI">MIGRASI</option>
                                  <option value="TOP_UP">TOP UP</option>
                              </select>
                          </div>
                          <div id="wrap_plafon_baru" class="hidden flex-col gap-1">
                              <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Nominal Plafon Baru</label>
                              <input type="number" id="inp_plafon_baru" placeholder="0" class="inp w-full border-red-300 focus:border-red-500">
                              <span class="text-[9px] font-bold text-slate-500 mt-0.5 ml-1">Plafon Sebelumnya: <span id="txt_plafon_sebelumnya" class="text-blue-600">Rp 0</span></span>
                          </div>
                      </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div class="flex flex-col gap-1">
                          <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Pipeline Awal</label>
                          <select id="inp_pipeline" class="inp w-full cursor-pointer">
                              <option value="">-- Pilih Pipeline --</option>
                              <option value="BTC">BTC</option>
                              <option value="Back Flow">Back Flow</option>
                              <option value="STAY">STAY</option>
                              <option value="LUNAS">LUNAS</option>
                              <option value="Program LNS <10 Juta">Program LNS &lt;10 Juta</option>
                              <option value="Restruktur">Restruktur</option>
                          </select>
                          <span class="text-[9px] font-bold ml-1 italic text-slate-500" id="txt_hint_pipeline"></span>
                      </div>

                      <div class="flex flex-col gap-1">
                          <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Petugas (AO) <span class="text-red-500">*</span></label>
                          <select id="inp_ao" class="inp w-full cursor-pointer" placeholder="Tunggu, memuat data AO..."></select>
                          <span class="text-[9px] font-bold ml-1 italic text-slate-400" id="txt_hint_ao">...</span>
                      </div>
                  </div>

                  <div class="col-span-1 md:col-span-2 mt-1 p-3 bg-amber-50/50 border border-amber-200 rounded-lg">
                      <label class="flex items-center gap-2 text-[10px] font-bold text-amber-800 uppercase tracking-wider cursor-pointer mb-0">
                          <input type="checkbox" id="chk_pipeline_akhir" class="rounded border-amber-300 text-amber-600 focus:ring-amber-500 w-4 h-4 cursor-pointer">
                          Update Pipeline Akhir Bulan (Antisipasi Migrasi / Pemburukan / Janji Transfer)
                      </label>
                      <div id="wrap_pipeline_akhir" class="hidden grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                          <div class="flex flex-col gap-1">
                              <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Pipeline Akhir Bulan <span class="text-red-500">*</span></label>
                              <select id="inp_pipeline_akhir" class="inp w-full cursor-pointer border-amber-300">
                                  <option value="">-- Pilih Pipeline Akhir --</option>
                                  <option value="BTC">BTC</option>
                                  <option value="Back Flow">Back Flow</option>
                                  <option value="STAY">STAY</option>
                                  <option value="LUNAS">LUNAS</option>
                                  <option value="Program LNS <10 Juta">Program LNS &lt;10 Juta</option>
                                  <option value="Restruktur">Restruktur</option>
                              </select>
                          </div>
                          <div class="flex flex-col gap-1">
                              <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Nominal Estimasi Masuk <span class="text-red-500">*</span></label>
                              <input type="number" id="inp_nominal_akhir" placeholder="0" class="inp w-full border-amber-300">
                          </div>
                      </div>
                  </div>

                  <div class="flex flex-col gap-1">
                      <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Kode Tindakan <span class="text-red-500">*</span></label>
                      <select id="inp_tindakan" class="inp w-full cursor-pointer" required>
                          <option value="">-- Pilih Tindakan --</option>
                          <optgroup label="HOPE">
                              <option value="PTP">PTP - Janji Bayar</option>
                              <option value="HPR">HPR - Hot Prospect</option>
                              <option value="ALM">ALM - Almarhum</option>
                              <option value="SKT">SKT - Sakit</option>
                              <option value="KSS">KSS - Kena Kasus</option>
                              <option value="BCN">BCN - Bencana</option>
                              <option value="PBD">PBD - Menolak Bayar</option>
                              <option value="JJA">JJA - Jual Asset</option>
                              <option value="RES">RES - Restruktur</option>
                          </optgroup>
                          <optgroup label="NO HOPE">
                              <option value="SKP">SKP - Skip Kabur</option>
                              <option value="CRA">CRA - Cerai</option>
                              <option value="PDH">PDH - Pindah Rumah</option>
                              <option value="ASA">ASA - Alamat Salah Sejak Awal</option>
                              <option value="FRD">FRD - Fraud</option>
                          </optgroup>
                      </select>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div id="wrap_input_non_dp" class="hidden flex-col gap-1 w-full">
                          <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Nominal Angsuran Total</label>
                          <input type="number" id="inp_angsuran_total" placeholder="0" class="inp w-full">
                      </div>

                      <div id="wrap_input_dp" class="hidden grid-cols-2 gap-3 w-full">
                          <div class="flex flex-col gap-1">
                              <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Nominal Pokok</label>
                              <input type="number" id="inp_pokok" placeholder="0" class="inp w-full">
                          </div>
                          <div class="flex flex-col gap-1">
                              <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Nominal Bunga</label>
                              <input type="number" id="inp_bunga" placeholder="0" class="inp w-full">
                          </div>
                      </div>

                      <div id="wrap_janji_bayar" class="hidden flex-col gap-1 w-full">
                          <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Tanggal Janji Bayar</label>
                          <input type="date" id="inp_tgl_ptp" class="inp w-full">
                      </div>
                  </div>

                  <div class="flex flex-col gap-1">
                      <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider ml-1">Keterangan / Catatan Kunjungan <span class="text-red-500">*</span></label>
                      <textarea id="inp_keterangan" rows="3" class="inp w-full py-2 h-auto" placeholder="Tulis rincian hasil kunjungan atau alasan penundaan..." required></textarea>
                  </div>
              </form>
          </div>

          <div class="bg-white px-5 py-3 border-t border-slate-200 flex items-center justify-end gap-2 shrink-0">
              <button type="button" onclick="SD_closeModal()" class="px-4 py-2 rounded-lg text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
              <button type="button" onclick="SD_submitKomitmen()" class="px-4 py-2 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                  Simpan Komitmen
              </button>
          </div>
      </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<style>
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .inp { border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0 0.5rem; font-size: 11px; background: #fff; height: 32px; outline: none; transition: 0.2s; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02); }
  .inp:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }
  .inp:disabled { background-color: #f1f5f9; cursor: not-allowed; color: #94a3b8; }
  #SD_table th, #SD_table td { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  @media (max-width: 767px) { .hide-mobile { display: none !important; } }

  .sd-col-kc { position: sticky !important; left: 0 !important; z-index: 5; background-color: #fff; width: 40px; min-width: 40px; max-width: 40px; box-shadow: inset -1px 0 0 #e2e8f0; }
  .sd-col-rek { position: sticky !important; left: 40px !important; z-index: 5; background-color: #fff; width: 90px; min-width: 90px; max-width: 90px; box-shadow: inset -1px 0 0 #e2e8f0; }
  .sd-col-nas { position: sticky !important; left: 0 !important; z-index: 5; background-color: #fff; width: 180px; min-width: 180px; max-width: 180px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); }
  @media (min-width: 768px) { .sd-col-nas { left: 130px !important; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); } }

  #SD_table thead th.sd-col-kc, #SD_table thead th.sd-col-rek, #SD_table thead th.sd-col-nas { z-index: 15 !important; background-color: #f8fafc !important; }
  #SD_totalRow td.sd-col-kc, #SD_totalRow td.sd-col-rek, #SD_totalRow td.sd-col-nas { z-index: 10 !important; background-color: #eff6ff !important; box-shadow: inset -1px 0 0 #bfdbfe, inset 0 1px 0 #bfdbfe !important; }
  #SD_tbody tr:hover td { background-color: #f8fafc !important; }
  #SD_tbody tr:hover td.sd-col-kc, #SD_tbody tr:hover td.sd-col-rek, #SD_tbody tr:hover td.sd-col-nas { filter: brightness(0.97); background-color: #f8fafc !important; }

  /* Tom Select FIX MULTI-SELECT & Z-INDEX */
  .ts-control { border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.35rem 0.5rem; font-size: 11px; background: #fff; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02); min-height: 32px; font-family: inherit; }
  .ts-control.focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }
  .ts-wrapper.single .ts-control { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-size: 14px; background-position: right 8px center; background-repeat: no-repeat; padding-right: 25px; }
  .ts-wrapper.single .ts-control input { font-size: 11px; }
  .ts-dropdown { font-size: 11px; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-color: #cbd5e1; z-index: 9999 !important; }
  .ts-dropdown .option { padding: 6px 10px; }
  .ts-wrapper.multi .ts-control > item { background-color: #eff6ff; color: #1e3a8a; border-radius: 4px; padding: 2px 6px; font-weight: bold; border: 1px solid #bfdbfe; margin: 1px; }
  
  .animate-slide-up { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
  @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  .cursor-help { cursor: help; }
</style>

<script>
  const nf = new Intl.NumberFormat('id-ID');
  let SD_currentPage = 1;
  const SD_limit = 50;
  let SD_totalPage = 1;
  
  let tsKankas, tsKecamatan, tsKelurahan, tsModalAO, tsKolek;
  let isUpdatingDependent = false; 
  let searchTimeout = null;

  const cut = (s, n) => { s = String(s || ''); return s.length <= n ? s : (s.slice(0, n).trimEnd() + '…'); };

  // FUNGSI KONVERSI TANGGAL KE LOKAL
  function getLocalDateString(dateObj) {
      const year = dateObj.getFullYear();
      const month = String(dateObj.getMonth() + 1).padStart(2, '0');
      const day = String(dateObj.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
  }

  document.getElementById('SD_btnToggleFilter').addEventListener('click', () => {
      const panel = document.getElementById('SD_panelFilter');
      const icon = document.getElementById('SD_iconToggle');
      if (panel.style.display === 'none') {
          panel.style.display = 'block';
          setTimeout(() => { panel.style.opacity = '1'; panel.style.transform = 'translateY(0)'; }, 10);
          icon.classList.remove('rotate-180');
      } else {
          panel.style.opacity = '0';
          panel.style.transform = 'translateY(-10px)';
          icon.classList.add('rotate-180');
          setTimeout(() => { panel.style.display = 'none'; }, 300);
      }
  });

  window.addEventListener('DOMContentLoaded', async () => {
      // SET DEFAULT TANGGAL HARIAN & CLOSING (MENGGUNAKAN LOCAL TIME)
      const tglSkrg = new Date();
      const akhirBulanLalu = new Date(tglSkrg.getFullYear(), tglSkrg.getMonth(), 0);
      
      document.getElementById('SD_harian_date').value = getLocalDateString(tglSkrg);
      document.getElementById('SD_closing_date').value = getLocalDateString(akhirBulanLalu);

      tsKankas = new TomSelect('#SD_optKankas', { create: false, placeholder: "Semua Kankas", sortField: {field: "text", direction: "asc"} });
      tsKecamatan = new TomSelect('#SD_optKecamatan', { create: false, placeholder: "Semua Kecamatan", sortField: {field: "text", direction: "asc"} });
      tsKelurahan = new TomSelect('#SD_optKelurahan', { create: false, placeholder: "Semua Kelurahan", sortField: {field: "text", direction: "asc"} });
      tsKolek = new TomSelect('#SD_optKolek', { plugins: ['remove_button'], placeholder: "Semua Kolek..." });
      
      tsModalAO = new TomSelect('#inp_ao', { 
          create: function(input) { return { value: input, text: input + ' (Ketik Manual)' }; },
          createFilter: function(input) {
              const user = (window.getUser && window.getUser()) || JSON.parse(localStorage.getItem('app_user')) || {};
              const jabatan = String(user.jabatan || '').toLowerCase();
              const isDev = user.kode_kantor === '000';
              const isKacab = jabatan.includes('kepala cabang') || jabatan.includes('pemimpin cabang');
              const isKabidKredit = jabatan.includes('kepala bidang kredit') || jabatan.includes('kabid kredit');
              const isKabidRemedial = jabatan.includes('kepala bidang remedial') || jabatan.includes('kabid remedial');
              const isKabidPemasaran = jabatan.includes('kepala bidang pemasaran') || jabatan.includes('kabid pemasaran');
              return isKacab || isKabidPemasaran || isKabidKredit || isKabidRemedial || isDev;
          },
          placeholder: "Pilih / Ketik Nama AO..." 
      });

      await populateKantorSD();
      const user = (window.getUser && window.getUser()) || JSON.parse(localStorage.getItem('app_user')) || {};
      const uKode = String(user?.kode || user?.kode_kantor || '').padStart(3,'0');
      
      const optKantor = document.getElementById('SD_optKantor');
      if (uKode !== '000' && uKode !== '00') {
          optKantor.value = uKode; optKantor.disabled = true;
          optKantor.classList.add('bg-slate-100', 'cursor-not-allowed', 'text-slate-400');
      }
      
      await loadFiltersKankasKecKel(optKantor.value);
      attachAutoSearchEvents();
      fetchDataSD(1);
  });

  function triggerAutoSearch() {
      if(isUpdatingDependent) return;
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => { fetchDataSD(1); }, 400); 
  }

  function attachAutoSearchEvents() {
      document.getElementById('SD_search').addEventListener('input', triggerAutoSearch);
      document.getElementById('SD_totung').addEventListener('input', triggerAutoSearch);
      document.getElementById('SD_optStatusJT').addEventListener('change', triggerAutoSearch);
      document.getElementById('SD_harian_date').addEventListener('change', triggerAutoSearch);
      document.getElementById('SD_closing_date').addEventListener('change', triggerAutoSearch);
      
      document.getElementById('SD_optKantor').addEventListener('change', async (e) => {
          await loadFiltersKankasKecKel(e.target.value);
          triggerAutoSearch();
      });

      tsKankas.on('change', () => triggerAutoSearch());
      tsKolek.on('change', () => triggerAutoSearch());
      
      tsKecamatan.on('change', async (val) => {
          if (isUpdatingDependent) return;
          isUpdatingDependent = true;
          await loadKelurahan(document.getElementById('SD_optKantor').value, val);
          isUpdatingDependent = false;
          triggerAutoSearch();
      });

      tsKelurahan.on('change', () => { if(!isUpdatingDependent) triggerAutoSearch(); });

      // SHOW/HIDE JANJI BAYAR DIBUKA UNTUK L & DP ATAU JIKA PILIH PTP
      document.getElementById('inp_tindakan').addEventListener('change', function(e) {
          const kolek = document.getElementById('inp_kolek').value;
          const wrapJanji = document.getElementById('wrap_janji_bayar');
          if(e.target.value === 'PTP' || kolek === 'DP' || kolek === 'L') {
              wrapJanji.classList.remove('hidden'); wrapJanji.classList.add('flex');
          } else {
              wrapJanji.classList.add('hidden'); wrapJanji.classList.remove('flex');
          }
      });

      // SHOW/HIDE PLAFON BARU JIKA PILIH MIGRASI/TOP_UP
      document.getElementById('inp_jt_tindakan').addEventListener('change', function(e) {
          const wrapPB = document.getElementById('wrap_plafon_baru');
          if(e.target.value === 'MIGRASI' || e.target.value === 'TOP_UP') {
              wrapPB.classList.remove('hidden'); wrapPB.classList.add('flex');
          } else {
              wrapPB.classList.add('hidden'); wrapPB.classList.remove('flex');
          }
      });

      // CHECKBOX PIPELINE AKHIR BULAN
      document.getElementById('chk_pipeline_akhir').addEventListener('change', function(e) {
          const wrapPA = document.getElementById('wrap_pipeline_akhir');
          if(e.target.checked) {
              wrapPA.classList.remove('hidden'); wrapPA.classList.add('grid');
          } else {
              wrapPA.classList.add('hidden'); wrapPA.classList.remove('grid');
          }
      });
  }

  async function populateKantorSD() {
      try {
          const res = await fetch('./api/kode/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'})});
          const json = await res.json();
          let html = `<option value="">Semua Kantor</option>`;
          (json.data || []).filter(x => x.kode_kantor !== '000').forEach(it => { html += `<option value="${it.kode_kantor}">${it.kode_kantor} — ${it.nama_kantor}</option>`; });
          document.getElementById('SD_optKantor').innerHTML = html;
      } catch(e) {}
  }

  async function loadFiltersKankasKecKel(kodeKantor) {
      isUpdatingDependent = true;
      try {
          if (!kodeKantor || kodeKantor === "") {
              tsKankas.clear(); tsKankas.clearOptions(); tsKankas.addOption({value: "", text: "Semua Kankas"});
              tsKankas.setValue(""); tsKankas.disable();
          } else {
              tsKankas.enable();
              fetch('./api/kode/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kankas', kode_kantor: kodeKantor})})
              .then(res => res.json()).then(json => {
                  tsKankas.clear(); tsKankas.clearOptions(); tsKankas.addOption({value: "", text: "Semua Kankas"});
                  (json.data || []).forEach(it => tsKankas.addOption({value: it.kode_group1, text: `${it.kode_group1} - ${it.deskripsi_group1 || it.nama_kankas}`}));
              }).catch(()=>{});
          }
          await loadKecamatan(kodeKantor);
          await loadKelurahan(kodeKantor, "");
      } finally { isUpdatingDependent = false; }
  }

  async function loadKecamatan(kodeKantor) {
      try {
          const res = await fetch('./api/kode/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kecamatan', kode_kantor: kodeKantor})});
          const json = await res.json();
          const currentVal = tsKecamatan.getValue(); 
          tsKecamatan.clear(); tsKecamatan.clearOptions(); tsKecamatan.addOption({value: "", text: "Semua Kecamatan"});
          (json.data || []).forEach(it => tsKecamatan.addOption({value: it.deskripsi_kode_kecamatan, text: it.deskripsi_kode_kecamatan}));
          if(currentVal) tsKecamatan.setValue(currentVal, true); 
      } catch(e) {}
  }

  async function loadKelurahan(kodeKantor, kecamatan) {
      try {
          const res = await fetch('./api/kode/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kelurahan', kode_kantor: kodeKantor, kecamatan: kecamatan})});
          const json = await res.json();
          const currentVal = tsKelurahan.getValue(); 
          tsKelurahan.clear(); tsKelurahan.clearOptions(); tsKelurahan.addOption({value: "", text: "Semua Kelurahan"});
          (json.data || []).forEach(it => tsKelurahan.addOption({value: it.deskripsi_kode_kelurahan, text: it.deskripsi_kode_kelurahan}));
          if(currentVal) tsKelurahan.setValue(currentVal, true);
      } catch(e) {}
  }

  async function fetchDataSD(pageTarget) {
      SD_currentPage = pageTarget;
      const loading = document.getElementById('SD_loading');
      loading.classList.remove('hidden');

      const user = (window.getUser && window.getUser()) || JSON.parse(localStorage.getItem('app_user')) || {};
      const uKodeLogin = String(user?.kode || user?.kode_kantor || '000').padStart(3,'0');

      const payload = {
          type: "cari debitur", user_kode: uKodeLogin,
          kode_kantor: document.getElementById('SD_optKantor').value,
          kolek: tsKolek.getValue().join(','), 
          status_jt: document.getElementById('SD_optStatusJT').value,
          search: document.getElementById('SD_search').value.trim(),
          totung: document.getElementById('SD_totung').value,
          harian_date: document.getElementById('SD_harian_date').value,
          closing_date: document.getElementById('SD_closing_date').value,
          kode_group1: tsKankas.getValue(), kecamatan: tsKecamatan.getValue(), kelurahan: tsKelurahan.getValue(),
          page: SD_currentPage, limit: SD_limit
      };

      try {
          const res = await fetch('./api/flow_par/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
          const json = await res.json();
          if (json.status === 200 && json.data) {
              renderTableSD(json.data.data); renderTotalRowSD(json.data.summary, json.data.data);
              updateSummarySD(json.data.summary); updatePaginationSD(json.data.pagination);
          } else { throw new Error(json.message); }
      } catch(e) {
          document.getElementById('SD_tbody').innerHTML = `<tr><td colspan="36" class="px-4 py-12 text-center text-red-500 font-bold">${e.message}</td></tr>`;
      } finally { loading.classList.add('hidden'); }
  }

  function renderTotalRowSD(sum, dataList) {
      const el = document.getElementById('SD_totalRow');
      if (!sum || dataList.length === 0) { el.innerHTML = ''; return; }
      
      let totalPlafon = 0, totalCkpn = 0, totalTotung = 0, totalTab = 0, totalTPokok = 0, totalTBunga = 0;
      let sumPokokLalu = 0, sumBungaLalu = 0, sumBayarSkrg = 0, sumNomPTP = 0;

      dataList.forEach(d => {
          totalPlafon += Number(d.plafon || 0); totalCkpn += Number(d.nilai_ckpn || 0); 
          totalTotung += Number(d.totung || 0); totalTab += Number(d.saldo_tabungan || 0);
          totalTPokok += Number(d.tunggakan_pokok || 0); totalTBunga += Number(d.tunggakan_bunga || 0);
          sumPokokLalu += Number(d.pokok_lalu || 0); sumBungaLalu += Number(d.bunga_lalu || 0);
          sumBayarSkrg += Number(d.total_bayar_sekarang || 0); sumNomPTP += Number(d.nom_ptp || 0);
      });

      // COLSPAN SUDAH DIPERBAIKI (12) AGAR SEJAJAR LURUS
      el.innerHTML = `
        <tr>
          <td class="px-2 py-2 border-r text-center hide-mobile sd-col-kc">ALL</td>
          <td class="px-2 py-2 border-r text-center hide-mobile sd-col-rek">-</td>
          <td class="px-3 py-2 border-r sd-col-nas">TOTAL KESELURUHAN</td>
          <td colspan="12" class="border-r text-center opacity-30">-</td>
          <td class="px-3 py-2 border-r text-right bg-blue-100/50">${nf.format(totalPlafon)}</td>
          <td class="px-3 py-2 border-r text-right bg-blue-100/50">${nf.format(totalCkpn)}</td>
          <td class="px-3 py-2 border-r text-right">${nf.format(sum.bd_act || 0)}</td>
          <td class="px-3 py-2 border-r text-right">${nf.format(totalTPokok)}</td>
          <td class="px-3 py-2 border-r text-right">${nf.format(totalTBunga)}</td>
          <td class="px-3 py-2 border-r text-right text-red-700">${nf.format(totalTotung)}</td>
          <td class="px-3 py-2 border-r text-right">${nf.format(totalTab)}</td>
          <td class="px-3 py-2 border-r text-center bg-slate-100/50">-</td>
          <td class="px-3 py-2 border-r text-right bg-slate-100/50">${nf.format(sumPokokLalu)}</td>
          <td class="px-3 py-2 border-r text-right bg-slate-100/50">${nf.format(sumBungaLalu)}</td>
          <td class="px-3 py-2 border-r text-center bg-blue-50/50">-</td>
          <td class="px-3 py-2 border-r text-right bg-blue-50/50">${nf.format(sumBayarSkrg)}</td>
          <td class="px-3 py-2 border-r text-center bg-blue-50/50">-</td>
          <td class="px-3 py-2 border-r text-center bg-purple-50/50">-</td>
          <td class="px-3 py-2 border-r text-center bg-purple-50/50">-</td>
          <td class="px-3 py-2 border-r text-center bg-purple-50/50">-</td>
          <td class="px-3 py-2 border-r text-right bg-purple-50/50">${nf.format(sumNomPTP)}</td>
          <td class="px-3 py-2 border-r text-center bg-purple-50/50">-</td>
          <td class="px-3 py-2 border-r text-center bg-purple-50/50">-</td>
          <td class="px-3 py-2 border-r text-center bg-purple-50/50">-</td>
         
        </tr>`;
         // <td class="px-3 py-2 text-center bg-slate-100/50">-</td>
  }

  function renderTableSD(list) {
      const tbody = document.getElementById('SD_tbody');
      if (!list || list.length === 0) {
          tbody.innerHTML = `<tr><td colspan="36" class="px-4 py-12 text-center text-slate-400 font-medium">Debitur tidak ditemukan.</td></tr>`;
          return;
      }
      tbody.innerHTML = list.map(d => {
        let badgeClass = "bg-slate-100 text-slate-600 border-slate-200";
        if(d.status_bayar_berjalan === 'OTP (Sesuai JT)') badgeClass = "bg-emerald-100 text-emerald-700 border-emerald-200";
        else if(d.status_bayar_berjalan === 'Lunas' || d.status_bayar_berjalan === 'Recovery NPL') badgeClass = "bg-blue-100 text-blue-700 border-blue-200";
        else if(d.status_bayar_berjalan === 'OTP (Tidak Sesuai JT)' || d.status_bayar_berjalan === 'Flow Par') badgeClass = "bg-amber-100 text-amber-700 border-amber-200";
        else if(d.status_bayar_berjalan === 'JT Potensi NPL' || d.status_bayar_berjalan === 'Blm Byr, Lewat JT') badgeClass = "bg-rose-100 text-rose-700 border-rose-200";
        else if(d.status_bayar_berjalan === 'Blm Byr, Belum JT') badgeClass = "bg-slate-100 text-slate-700 border-slate-300";
        else if(d.status_bayar_berjalan === 'Realisasi Baru') badgeClass = "bg-teal-100 text-teal-700 border-teal-200";
        else if(d.status_bayar_berjalan === 'Restruktur') badgeClass = "bg-indigo-100 text-indigo-700 border-indigo-200";
        else if(d.status_bayar_berjalan === 'Byr, Ada Tunggakan') badgeClass = "bg-orange-100 text-orange-700 border-orange-200";

        const badgeStatus = `<span class="px-2 py-0.5 border ${badgeClass} rounded text-[9px] uppercase tracking-wider font-bold whitespace-nowrap">${d.status_bayar_berjalan||'-'}</span>`;
        
        // HIGHLIGHT ROW JIKA JT POTENSI NPL (Garis Merah Pudar)
        let trClass = "transition border-b border-slate-100 hover:bg-slate-50";
        if (d.status_bayar_berjalan === 'JT Potensi NPL') {
             trClass = "transition border-b border-rose-200 bg-rose-50/30 hover:bg-rose-50/60";
        }

        return `
        <tr class="${trClass}">
          <td class="px-2 py-2 border-r text-center text-slate-400 font-mono hide-mobile sd-col-kc bg-white">${d.kode_cabang || '-'}</td>
          <td class="px-2 py-2 border-r text-center font-mono text-slate-500 hide-mobile sd-col-rek bg-white">${d.no_rekening||'-'}</td>
          <td class="px-3 py-2 border-r font-bold text-slate-700 truncate sd-col-nas bg-white" title="${d.nama_nasabah}">${d.nama_nasabah||'-'}</td>
          <td class="px-3 py-2 border-r text-slate-600 truncate" title="${d.alamat||''}">${cut(d.alamat, 20)||'-'}</td>
          <td class="px-3 py-2 border-r text-slate-600 truncate" title="${d.nama_kankas||''}">${d.nama_kankas||'-'}</td>
          <td class="px-3 py-2 border-r text-slate-600 truncate" title="${d.nama_ao||''}">${d.nama_ao||'-'}</td>
          <td class="px-3 py-2 border-r text-slate-600 truncate">${d.deskripsi_kode_kecamatan||'-'}</td>
          <td class="px-3 py-2 border-r text-slate-600 truncate">${d.deskripsi_kode_kelurahan||'-'}</td>
          <td class="px-3 py-2 border-r text-center text-slate-500">${d.kode_produk||'-'}</td>
          <td class="px-3 py-2 border-r text-center text-slate-500 font-bold">${d.tgl_jatuh_tempo||'-'}</td>
          <td class="px-3 py-2 border-r text-center font-bold text-slate-400 bg-yellow-50/30">${d.kolek_lalu||'-'}</td>
          <td class="px-3 py-2 border-r text-center font-bold">${d.kolek||'-'}</td>
          <td class="px-3 py-2 border-r text-center">${d.dpd||'0'}</td>
          <td class="px-3 py-2 border-r text-center">${d.hmp||'0'}</td>
          <td class="px-3 py-2 border-r text-center">${d.hmb||'0'}</td>
          <td class="px-3 py-2 border-r text-right text-blue-700 font-bold bg-blue-50/20">${nf.format(d.plafon||0)}</td>
          <td class="px-3 py-2 border-r text-right text-blue-700 font-bold bg-blue-50/20">${nf.format(d.nilai_ckpn||0)}</td>
          <td class="px-3 py-2 border-r text-right text-emerald-700 font-bold bg-emerald-50/20">${nf.format(d.baki_debet||0)}</td>
          <td class="px-3 py-2 border-r text-right text-orange-700 font-bold bg-orange-50/20">${nf.format(d.tunggakan_pokok||0)}</td>
          <td class="px-3 py-2 border-r text-right text-orange-700 font-bold bg-orange-50/20">${nf.format(d.tunggakan_bunga||0)}</td>
          <td class="px-3 py-2 border-r text-right text-red-600 font-bold bg-red-50/20">${nf.format(d.totung||0)}</td>
          <td class="px-3 py-2 border-r text-right text-slate-600">${nf.format(d.saldo_tabungan||0)}</td>
          
          <td class="px-3 py-2 border-r text-center text-slate-500 bg-slate-50/50">${d.tgl_trans_lalu||'-'}</td>
          <td class="px-3 py-2 border-r text-right text-slate-700 font-bold bg-slate-50/50">${nf.format(d.pokok_lalu||0)}</td>
          <td class="px-3 py-2 border-r text-right text-slate-700 font-bold bg-slate-50/50">${nf.format(d.bunga_lalu||0)}</td>
          <td class="px-3 py-2 border-r text-center text-slate-500 bg-blue-50/20">${d.tgl_trans_sekarang||'-'}</td>
          <td class="px-3 py-2 border-r text-right text-blue-700 font-bold bg-blue-50/20">${nf.format(d.total_bayar_sekarang||0)}</td>
          <td class="px-3 py-2 border-r text-center bg-blue-50/20">${badgeStatus}</td>
          
          <td class="px-3 py-2 border-r text-center text-slate-600 bg-purple-50/20 truncate font-bold">${d.pipeline||'-'}</td>
          <td class="px-3 py-2 border-r text-center text-slate-600 bg-purple-50/20 font-bold">${d.kode_tindakan||'-'}</td>
          <td class="px-3 py-2 border-r text-center text-slate-600 bg-purple-50/20 truncate">${d.plan_ao_remedial||'-'}</td>
          <td class="px-3 py-2 border-r text-right text-purple-700 font-bold bg-purple-50/20">${nf.format(d.nom_ptp||0)}</td>
          <td class="px-3 py-2 border-r text-center text-slate-600 bg-purple-50/20">${d.komitmen_tgl_ptp||'-'}</td>
          <td class="px-3 py-2 border-r text-center text-slate-600 bg-purple-50/20 truncate font-bold">${d.status_bucket||'-'}</td>
          <td class="px-3 py-2 border-r text-slate-600 bg-purple-50/20 truncate cursor-help" title="${d.keterangan_komitmen||''}">${cut(d.keterangan_komitmen, 10)||'-'}</td>


        </tr>`;
      }).join('');

          //       <td class="px-3 py-2 text-center bg-slate-50/50">
          //     <button type="button" onclick="SD_openModal('${d.no_rekening}', '${String(d.nama_nasabah).replace(/'/g, "\\'")}', '${d.kolek}', ${d.dpd||0}, ${d.dpd_lalu !== undefined && d.dpd_lalu !== null ? d.dpd_lalu : 'null'}, ${d.tunggakan_pokok||0}, ${d.hmp||0}, ${d.tunggakan_bunga||0}, ${d.hmb||0}, '${d.pipeline||''}', '${d.kode_cabang||''}', '${d.nama_ao||''}', '${d.tgl_jatuh_tempo||''}', ${d.plafon||0})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2.5 py-1 rounded shadow-sm text-[10px] font-bold transition flex items-center gap-1 mx-auto">
          //         <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
          //         Tindakan
          //     </button>
          // </td>
  }

  function updateSummarySD(s) { document.getElementById('SD_sumNoa').innerText = nf.format(s.noa || 0); document.getElementById('SD_sumBd').innerText = nf.format(s.bd_act || 0); }
  function updatePaginationSD(p) {
      SD_totalPage = p.total_page || 1; SD_currentPage = p.current_page || 1;
      document.getElementById('SD_pageInfo').innerText = `Total: ${nf.format(p.total_data || 0)} Debitur`;
      document.getElementById('SD_pageCurrent').innerText = `Hal ${SD_currentPage} / ${SD_totalPage}`;
      document.getElementById('SD_btnPrev').disabled = (SD_currentPage <= 1);
      document.getElementById('SD_btnNext').disabled = (SD_currentPage >= SD_totalPage);
  }
  window.SD_changePage = (dir) => { let n = SD_currentPage + dir; if (n >= 1 && n <= SD_totalPage) fetchDataSD(n); };

  window.SD_exportExcelAll = async function() {
      const btn = document.querySelector('button[title="Export Excel"]');
      const original = btn.innerHTML;
      btn.innerHTML = `<div class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>`;
      try {
          const user = (window.getUser && window.getUser()) || JSON.parse(localStorage.getItem('app_user')) || {};
          const uKodeLogin = String(user?.kode || user?.kode_kantor || '000').padStart(3,'0');

          const payload = {
              type: "cari debitur", user_kode: uKodeLogin, kode_kantor: document.getElementById('SD_optKantor').value,
              kolek: tsKolek.getValue().join(','), status_jt: document.getElementById('SD_optStatusJT').value,
              search: document.getElementById('SD_search').value.trim(), totung: document.getElementById('SD_totung').value,
              harian_date: document.getElementById('SD_harian_date').value, closing_date: document.getElementById('SD_closing_date').value,
              kode_group1: tsKankas.getValue(), kecamatan: tsKecamatan.getValue(), kelurahan: tsKelurahan.getValue(),
              page: 1, limit: 1000000
          };
          const res = await fetch('./api/flow_par/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
          const json = await res.json();
          if (json.status === 200 && json.data?.data) {
              let table = `<table border="1"><thead><tr><th>KANTOR</th><th>REKENING</th><th>NAMA</th><th>ALAMAT</th><th>KANKAS</th><th>AO KREDIT</th><th>KECAMATAN</th><th>KELURAHAN</th><th>PRODUK</th><th>JATUH TEMPO</th><th>KOLEK AWAL</th><th>KOLEK</th><th>DPD</th><th>HMP</th><th>HMB</th><th>PLAFON</th><th>NILAI CKPN</th><th>BAKI DEBET</th><th>T. POKOK</th><th>T. BUNGA</th><th>TOTUNG</th><th>TABUNGAN</th><th>TGL TRX LALU</th><th>POKOK LALU</th><th>BUNGA LALU</th><th>TGL TRX SKRG</th><th>BAYAR SKRG</th><th>STATUS BAYAR</th><th>PIPELINE</th><th>KODE TINDAKAN</th><th>PLAN AO REMEDIAL</th><th>NOM PTP</th><th>TGL PTP</th><th>STATUS BUCKET</th><th>KETERANGAN</th></tr></thead><tbody>`;
              json.data.data.forEach(d => { table += `<tr><td>${d.kode_cabang||''}</td><td style="mso-number-format:'\\@'">${d.no_rekening||''}</td><td>${d.nama_nasabah||''}</td><td>${d.alamat||''}</td><td>${d.nama_kankas||''}</td><td>${d.nama_ao||''}</td><td>${d.deskripsi_kode_kecamatan||''}</td><td>${d.deskripsi_kode_kelurahan||''}</td><td>${d.kode_produk||''}</td><td>${d.tgl_jatuh_tempo||''}</td><td>${d.kolek_lalu||''}</td><td>${d.kolek||''}</td><td>${d.dpd||'0'}</td><td>${d.hmp||'0'}</td><td>${d.hmb||'0'}</td><td>${d.plafon||'0'}</td><td>${d.nilai_ckpn||'0'}</td><td>${d.baki_debet||'0'}</td><td>${d.tunggakan_pokok||'0'}</td><td>${d.tunggakan_bunga||'0'}</td><td>${d.totung||'0'}</td><td>${d.saldo_tabungan||'0'}</td><td>${d.tgl_trans_lalu||''}</td><td>${d.pokok_lalu||'0'}</td><td>${d.bunga_lalu||'0'}</td><td>${d.tgl_trans_sekarang||''}</td><td>${d.total_bayar_sekarang||'0'}</td><td>${d.status_bayar_berjalan||'-'}</td><td>${d.pipeline||'-'}</td><td>${d.kode_tindakan||'-'}</td><td>${d.plan_ao_remedial||'-'}</td><td>${d.nom_ptp||'0'}</td><td>${d.komitmen_tgl_ptp||'-'}</td><td>${d.status_bucket||'-'}</td><td>${d.keterangan_komitmen||'-'}</td></tr>`; });
              table += `</tbody></table>`;
              const blob = new Blob([table], { type: 'application/vnd.ms-excel' });
              const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
              a.download = `Cari_Debitur_Lengkap_${new Date().getTime()}.xls`; a.click();
          }
      } catch(e) { alert("Download gagal"); } finally { btn.innerHTML = original; }
  };

  // --- MODAL & API AO LOGIC ---
  async function SD_openModal(norek, nama, kolek, dpd, dpdLaluRaw, tpokok, hmp, tbunga, hmb, pipeline, kode_cabang, nama_ao_kredit, tgl_jatuh_tempo, plafon) {
      const targetDpd = dpdLaluRaw !== null ? dpdLaluRaw : dpd; 
      
      document.getElementById('inp_norek').value = norek;
      document.getElementById('inp_kolek').value = kolek;
      document.getElementById('inp_kode_cabang_mod').value = kode_cabang;
      document.getElementById('mod_norek').innerText = norek;
      document.getElementById('mod_nama').innerText = nama;
      document.getElementById('mod_kolek').innerText = kolek;
      document.getElementById('mod_dpd').innerText = dpd + ' Hari';
      
      document.getElementById('mod_tpokok').innerText = "Rp " + nf.format(tpokok);
      document.getElementById('mod_hmp').innerText = hmp + ' Hari';
      document.getElementById('mod_tbunga').innerText = "Rp " + nf.format(tbunga);
      document.getElementById('mod_hmb').innerText = hmb + ' Hari';

      // 💡 LOGIC KUNCI PIPELINE
      const inpPipeline = document.getElementById('inp_pipeline');
      const hintPipeline = document.getElementById('txt_hint_pipeline');
      const tglHariIni = new Date().getDate();

      if (pipeline && pipeline !== '-' && pipeline !== 'BELUM ADA PIPELINE') {
          inpPipeline.value = pipeline;
          inpPipeline.disabled = true;
          hintPipeline.innerText = "🔒 Pipeline sudah diisi bulan ini.";
          hintPipeline.className = "text-[9px] font-bold ml-1 italic text-slate-500";
      } else if (tglHariIni > 7) {
          inpPipeline.value = "";
          inpPipeline.disabled = true;
          hintPipeline.innerText = "🔒 Terkunci (Batas isi Pipeline tgl 1 - 7).";
          hintPipeline.className = "text-[9px] font-bold ml-1 italic text-rose-500";
      } else {
          inpPipeline.value = "";
          inpPipeline.disabled = false;
          hintPipeline.innerText = "Silakan isi Pipeline bulan ini.";
          hintPipeline.className = "text-[9px] font-bold ml-1 italic text-emerald-600";
      }

      // RESET CHECKBOX PIPELINE AKHIR
      const chkAkhir = document.getElementById('chk_pipeline_akhir');
      const wrapPA = document.getElementById('wrap_pipeline_akhir');
      chkAkhir.checked = false;
      wrapPA.classList.add('hidden'); wrapPA.classList.remove('grid');
      document.getElementById('inp_pipeline_akhir').value = "";
      document.getElementById('inp_nominal_akhir').value = "";

      // 💡 LOGIC JATUH TEMPO WAJIB LUNAS
      const wrapJatuhTempo = document.getElementById('wrap_jatuh_tempo');
      const wrapPB = document.getElementById('wrap_plafon_baru');
      document.getElementById('inp_jt_tindakan').value = "";
      wrapPB.classList.add('hidden'); wrapPB.classList.remove('flex');
      document.getElementById('inp_plafon_baru').value = "";

      if (tgl_jatuh_tempo && tgl_jatuh_tempo !== '-') {
          const tglJT = new Date(tgl_jatuh_tempo);
          const tglSkrg = new Date();
          const akhirBulanIni = new Date(tglSkrg.getFullYear(), tglSkrg.getMonth() + 1, 0);
          
          if (tglJT <= akhirBulanIni) {
              wrapJatuhTempo.classList.remove('hidden');
              wrapJatuhTempo.classList.add('flex');
              document.getElementById('txt_plafon_sebelumnya').innerText = "Rp " + nf.format(plafon);
          } else {
              wrapJatuhTempo.classList.add('hidden'); wrapJatuhTempo.classList.remove('flex');
          }
      } else {
          wrapJatuhTempo.classList.add('hidden'); wrapJatuhTempo.classList.remove('flex');
      }

      const hintAO = document.getElementById('txt_hint_ao');
      hintAO.classList.remove('text-red-500', 'text-emerald-600', 'text-orange-500');

      if (targetDpd <= 30) {
          tsModalAO.clear(); tsModalAO.clearOptions();
          tsModalAO.addOption({value: nama_ao_kredit, text: `${nama_ao_kredit} (AO KREDIT)`});
          tsModalAO.setValue(nama_ao_kredit);
          tsModalAO.disable();
          hintAO.innerText = "🔒 DPD <= 30 dikunci untuk AO Kredit bersangkutan.";
          hintAO.className = "text-[9px] font-bold ml-1 italic text-slate-500";
      } else {
          tsModalAO.enable();
          tsModalAO.clear(); tsModalAO.clearOptions();
          hintAO.innerText = `Menarik data AO Remedial dari SSO...`;
          hintAO.className = "text-[9px] font-bold ml-1 italic text-slate-500";

          try {
              const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
              const ssoApiUrl = isLocalhost ? 'http://localhost/rest_api_sso/api/ao/list' : 'https://apisso.bkkjateng.co.id/api/ao/list';

              // TANPA TOKEN UNTUK REMEDIAL SSO
              const resRem = await fetch(`${ssoApiUrl}?tipe=remedial&kode_cabang=${kode_cabang}`);
              const jsonRem = await resRem.json();
              
              let count = 0;
              if(jsonRem.data && jsonRem.data.length > 0) {
                  jsonRem.data.forEach(it => {
                      tsModalAO.addOption({value: it.full_name, text: `${it.full_name} (REMEDIAL)`});
                      count++;
                  });
              }

              if(count > 0) {
                  hintAO.innerText = `✅ Berhasil memuat ${count} AO Remedial`;
                  hintAO.classList.add('text-emerald-600');
              } else {
                  hintAO.innerText = `⚠️ Tidak ada AO Remedial di Cabang ini. (Ketik manual jika memiliki akses)`;
                  hintAO.classList.add('text-orange-500');
              }
          } catch(e) {
              hintAO.innerText = `❌ SSO Error. Ketik nama AO manual dan tekan Enter.`;
              hintAO.classList.add('text-red-500');
          }
      }

      const dpdLaluDisp = dpdLaluRaw !== null ? dpdLaluRaw : 0;
      const bucketLalu = getBucketName(dpdLaluDisp);
      const bucketSkrg = getBucketName(dpd);
      document.getElementById('mod_bucket_lalu').innerText = bucketLalu;
      document.getElementById('mod_bucket_skrg').innerText = bucketSkrg;

      const badgePergerakan = document.getElementById('mod_pergerakan');
      if (dpd === 0 && dpdLaluDisp > 0) {
          badgePergerakan.innerText = "BTC (Back To Current)";
          badgePergerakan.className = "px-2 py-1 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wider text-center";
      } else if (dpd < dpdLaluDisp) {
          badgePergerakan.innerText = "Backflow";
          badgePergerakan.className = "px-2 py-1 rounded text-[10px] font-bold bg-blue-100 text-blue-700 uppercase tracking-wider text-center";
      } else if (bucketSkrg === bucketLalu) {
          badgePergerakan.innerText = "Stay";
          badgePergerakan.className = "px-2 py-1 rounded text-[10px] font-bold bg-amber-100 text-amber-700 uppercase tracking-wider text-center";
      } else if (dpd > dpdLaluDisp) {
          badgePergerakan.innerText = "Pemburukan";
          badgePergerakan.className = "px-2 py-1 rounded text-[10px] font-bold bg-rose-100 text-rose-700 uppercase tracking-wider text-center";
      }

      const wrapNonDp = document.getElementById('wrap_input_non_dp');
      const wrapDp = document.getElementById('wrap_input_dp');
      const wrapJanji = document.getElementById('wrap_janji_bayar');

      // RESET SISA INPUTAN
      document.getElementById('inp_tindakan').value = "";
      document.getElementById('inp_angsuran_total').value = "";
      document.getElementById('inp_pokok').value = "";
      document.getElementById('inp_bunga').value = "";
      document.getElementById('inp_tgl_ptp').value = "";
      document.getElementById('inp_keterangan').value = "";

      wrapNonDp.classList.add('hidden'); wrapNonDp.classList.remove('flex');
      wrapDp.classList.add('hidden'); wrapDp.classList.remove('grid');
      wrapJanji.classList.add('hidden'); wrapJanji.classList.remove('flex');

      if(kolek === 'DP' || kolek === 'L') {
          wrapDp.classList.remove('hidden'); wrapDp.classList.add('grid');
          wrapJanji.classList.remove('hidden'); wrapJanji.classList.add('flex');
      } else if (['KL', 'D', 'M'].includes(kolek)) {
          wrapNonDp.classList.remove('hidden'); wrapNonDp.classList.add('flex');
      }

      document.getElementById('SD_modalKomitmen').classList.remove('hidden');
  }

  function SD_closeModal() { document.getElementById('SD_modalKomitmen').classList.add('hidden'); }

  function getBucketName(dpd) {
      dpd = Number(dpd || 0);
      if(dpd === 0) return "Lancar (0)";
      if(dpd >= 1 && dpd <= 30) return "DPD 1 - 30";
      if(dpd >= 31 && dpd <= 60) return "DPD 31 - 60";
      if(dpd >= 61 && dpd <= 90) return "DPD 61 - 90";
      if(dpd >= 91 && dpd <= 120) return "DPD 91 - 120";
      if(dpd >= 121 && dpd <= 150) return "DPD 121 - 150";
      if(dpd >= 151 && dpd <= 180) return "DPD 151 - 180";
      if(dpd >= 181 && dpd <= 210) return "DPD 181 - 210";
      if(dpd >= 211 && dpd <= 240) return "DPD 211 - 240";
      if(dpd >= 241 && dpd <= 270) return "DPD 241 - 270";
      if(dpd >= 271 && dpd <= 300) return "DPD 271 - 300";
      if(dpd >= 301 && dpd <= 330) return "DPD 301 - 330";
      if(dpd >= 331 && dpd <= 360) return "DPD 331 - 360";
      return "DPD > 360";
  }

  function SD_submitKomitmen() {
      alert("Proses simpan komitmen debitur (API belum dipasang).");
      SD_closeModal();
  }
</script>
<!-- Load Library ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
  :root { --primary: #0284c7; --bg: #f8fafc; --text: #334155; }
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
  
  .inp { 
      box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0 0.5rem; 
      font-size: 13px; background: #fff; height: 42px; cursor: pointer; outline: none; transition: border 0.2s; font-weight: 600;
  }
  .inp:focus { border-color: var(--primary); box-shadow: 0 0 0 2px #bae6fd; }
  
  .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  th { background-color: #f8fafc; color: #1e293b; font-weight: 800; padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; font-size: 11px; }
  td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-weight: 700; color: #334155; }
  tr:hover td { background-color: #f0f9ff; }
  
  .card-shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
  
  .tab-btn { padding: 8px 24px; border-radius: 8px; font-size: 13px; font-weight: 800; color: #64748b; transition: all 0.2s; border: 1px solid transparent; cursor: pointer; }
  .tab-btn.active { background-color: #0284c7; color: #fff; box-shadow: 0 4px 6px -1px rgba(2, 132, 199, 0.4); border-color: #0284c7; }
  .tab-btn:hover:not(.active) { color: #0f172a; background-color: #e2e8f0; }

  .local-loader { position: absolute; inset: 0; background: rgba(255,255,255,0.7); z-index: 50; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(2px); border-radius: inherit; }
  .local-loader.hidden { display: none; }

  /* CSS ApexCharts */
  .apexcharts-tooltip { z-index: 99999 !important; background: transparent !important; border: none !important; box-shadow: none !important; }

  /* ====== Ringkasan Korwil (Leaderboard) ====== */
  .korwil-card { position: relative; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; transition: transform .2s, box-shadow .2s; overflow: hidden; }
  .korwil-card:hover { transform: translateY(-2px); box-shadow: 0 6px 14px -4px rgba(2,132,199,.18); }
  .korwil-card .rank-badge { position: absolute; top: -2px; right: 10px; padding: 4px 10px 6px; border-radius: 0 0 8px 8px; font-size: 10px; font-weight: 900; color: #fff; letter-spacing: .5px; }
  .rank-1 { background: linear-gradient(135deg,#f59e0b,#d97706); }
  .rank-2 { background: linear-gradient(135deg,#94a3b8,#64748b); }
  .rank-3 { background: linear-gradient(135deg,#b45309,#92400e); }
  .rank-x { background: linear-gradient(135deg,#0ea5e9,#0284c7); }
  .korwil-card.is-top { border-color: #fbbf24; box-shadow: 0 0 0 2px #fef3c7 inset; }

  /* ====== Info Modal ====== */
  #ldInfoModal { position: fixed; inset: 0; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 1rem; }
  #ldInfoModal.is-open { display: flex; }
  #ldInfoModal .ld-backdrop { position: absolute; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(2px); }
  #ldInfoModal .ld-dialog { position: relative; background: #fff; width: 100%; max-width: 480px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0,0,0,.35); border: 1px solid #e2e8f0; overflow: hidden; transform: scale(.96); opacity: 0; transition: transform .22s ease, opacity .22s ease; }
  #ldInfoModal.is-open .ld-dialog { transform: scale(1); opacity: 1; }
  #ldInfoModal .ld-progress { position: absolute; left: 0; bottom: 0; height: 3px; background: linear-gradient(90deg,#0ea5e9,#0284c7); width: 100%; transform-origin: left; }
  #ldInfoModal.is-open .ld-progress { animation: ldShrink 6s linear forwards; }
  @keyframes ldShrink { from { transform: scaleX(1); } to { transform: scaleX(0); } }

  .info-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 9999px; color: #64748b; background: #f1f5f9; transition: all .15s; border: 1px solid transparent; }
  .info-btn:hover { color: #0284c7; background: #e0f2fe; border-color: #bae6fd; }
</style>

<div class="max-w-[1600px] mx-auto px-3 md:px-4 py-4 flex flex-col gap-5">
  
  <!-- ================= HEADER & GLOBAL FILTER ================= -->
  <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 bg-white p-4 rounded-xl card-shadow border border-slate-100">
    <div>
        <h1 class="text-xl md:text-2xl font-bold flex items-center gap-2 text-slate-800">
            <span class="bg-blue-600 text-white p-1.5 rounded-lg text-sm shadow-sm">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span> 
            <span>Layanan Digital</span>
            <button type="button" onclick="openLdInfoModal()" class="info-btn" title="Informasi Dashboard" aria-label="Informasi Dashboard">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </button>
        </h1>
        <p class="text-xs text-slate-500 mt-1 ml-1 font-medium" id="lbl_periode_aktif">Menunggu data sinkronisasi...</p>
    </div>

    <form id="formFilterGlobal" class="flex flex-col md:flex-row items-end gap-3 w-full xl:w-auto">
        <div class="flex flex-col flex-1 md:w-[140px]">
            <label class="text-[10px] font-extrabold text-slate-500 uppercase ml-1 mb-1 tracking-wider">CLOSING M-1</label>
            <input type="date" id="closing_date" class="inp text-slate-700 shadow-sm" required>
        </div>
        <div class="flex flex-col flex-1 md:w-[140px]">
            <label class="text-[10px] font-extrabold text-slate-500 uppercase ml-1 mb-1 tracking-wider">HARIAN / ACTUAL</label>
            <input type="date" id="harian_date" class="inp text-slate-700 shadow-sm" required>
        </div>

        <div class="flex flex-col w-full md:w-[220px]">
            <label class="text-[10px] font-extrabold text-slate-500 uppercase ml-1 mb-1 tracking-wider">AREA / CABANG</label>
            <select id="opt_area" class="inp text-blue-700 shadow-sm">
                <option value="KONSOLIDASI" class="font-bold">Konsolidasi</option>
                <optgroup label="Berdasarkan Korwil" class="text-slate-400">
                    <option value="KORWIL_SEMARANG" class="text-slate-700">Korwil Semarang</option>
                    <option value="KORWIL_SOLO" class="text-slate-700">Korwil Solo</option>
                    <option value="KORWIL_BANYUMAS" class="text-slate-700">Korwil Banyumas</option>
                    <option value="KORWIL_PEKALONGAN" class="text-slate-700">Korwil Pekalongan</option>
                </optgroup>
                <optgroup label="Berdasarkan Cabang" id="opt_cabang_list" class="text-slate-400"></optgroup>
            </select>
        </div>
        
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white h-[42px] px-6 rounded-lg font-bold text-sm shadow-md flex items-center justify-center transition w-full md:w-auto">
            Tampilkan
        </button>
    </form>
  </div>

  <!-- ================= SUMMARY CARDS ================= -->
  <div class="relative rounded-xl min-h-[100px]">
      <div id="loadSummary" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
      <div id="summaryCardsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
          <!-- JS Inject -->
      </div>
  </div>

  <!-- ================= RINGKASAN KORWIL (LEADERBOARD) ================= -->
  <div class="bg-white rounded-2xl card-shadow border border-slate-100 p-4 md:p-5 relative">
      <div id="loadKorwil" class="local-loader hidden rounded-2xl"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-3 border-b border-slate-100 pb-2.5">
          <div class="flex items-center gap-2">
              <span class="bg-amber-100 text-amber-600 p-1.5 rounded-md">
                  <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v3a5 5 0 01-5 5H10a5 5 0 01-5-5V4zm-1 0h1m14 0h1M9 17h6M12 12v5M9 21h6"></path></svg>
              </span>
              <h2 class="text-base font-black text-slate-800">Ringkasan Korwil</h2>
              <span class="text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 px-2 py-0.5 rounded">Channel: <span id="lblKorwilChannel" class="text-blue-700">VA</span></span>
          </div>
          <p class="text-[11px] text-slate-500">Peringkat 4 Korwil berdasarkan total nominal transaksi bulan ini.</p>
      </div>
      <div id="korwilLeaderboard" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
          <!-- JS Inject -->
      </div>
  </div>

  <!-- ================= TOP 5 & BOTTOM 5 PER CABANG ================= -->
  <div class="bg-white rounded-2xl card-shadow border border-slate-100 p-4 md:p-5 relative">
      <div id="loadTopBottom" class="local-loader hidden rounded-2xl"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4 border-b border-slate-100 pb-2.5">
          <div class="flex items-center gap-2">
              <span class="bg-indigo-100 text-indigo-600 p-1.5 rounded-md">
                  <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
              </span>
              <h2 class="text-base font-black text-slate-800">Top & Bottom 5 Cabang</h2>
              <span class="text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 px-2 py-0.5 rounded">Channel: <span id="lblTopBotChannel" class="text-blue-700">VA</span></span>
          </div>
          <p class="text-[11px] text-slate-500">Peringkat cabang berdasarkan nominal transaksi bulan berjalan.</p>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <!-- TOP 5 -->
          <div>
              <div class="flex items-center gap-2 mb-3">
                  <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] font-black">&#9650;</span>
                  <h3 class="text-sm font-extrabold text-emerald-700">Top 5 Tertinggi</h3>
              </div>
              <div id="listTop5Cabang" class="space-y-2">
                  <!-- JS Inject -->
              </div>
          </div>
          <!-- BOTTOM 5 -->
          <div>
              <div class="flex items-center gap-2 mb-3">
                  <span class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-[10px] font-black">&#9660;</span>
                  <h3 class="text-sm font-extrabold text-red-700">Bottom 5 Terendah</h3>
              </div>
              <div id="listBottom5Cabang" class="space-y-2">
                  <!-- JS Inject -->
              </div>
          </div>
      </div>
  </div>

  <!-- ================= WRAPPER DETAIL TRANSAKSI ================= -->
  <div class="bg-white rounded-2xl card-shadow border border-slate-100 p-4 md:p-6 flex flex-col gap-5 mt-2">
      
      <!-- MASTER TABS CHANNEL -->
      <div class="flex justify-center md:justify-start">
          <div class="flex gap-2 bg-slate-100 border border-slate-200 p-1.5 rounded-xl overflow-x-auto custom-scrollbar">
              <button onclick="changeChannel('VA')" id="tab_VA" class="tab-btn active">Virtual Account (VA)</button>
              <button onclick="changeChannel('BRANCHLESS')" id="tab_BRANCHLESS" class="tab-btn">Branchless</button>
              <button onclick="changeChannel('QRIS')" id="tab_QRIS" class="tab-btn">QRIS Merchant</button>
          </div>
      </div>

      <!-- GRAFIK AREA -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
          <div class="xl:col-span-7 bg-white rounded-xl border border-slate-200 p-5 flex flex-col relative h-[430px]">
              <div id="loadTrend" class="local-loader hidden rounded-xl"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
              <div class="flex justify-between items-center mb-2 border-b border-slate-100 pb-2">
                  <h2 class="font-bold text-slate-800" id="titleTrend">Tren Transaksi VA</h2>
                  <select id="trendPeriode" class="inp h-8 text-[11px] w-[140px]" onchange="fetchTrend()">
                      <option value="bulanan">6 Bulan Terakhir</option>
                      <option value="7_hari">7 Hari Terakhir</option>
                      <option value="30_hari">30 Hari Terakhir</option>
                      <option value="tahunan">Tahunan</option>
                  </select>
              </div>
              <div id="chartTrend" class="w-full mt-2"></div>
          </div>

          <div class="xl:col-span-5 bg-white rounded-xl border border-slate-200 p-5 flex flex-col relative h-[430px]">
              <div id="loadDist" class="local-loader hidden rounded-xl"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
              <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-2">
                  <h2 class="font-bold text-slate-800" id="titleDistribusi">Distribusi per Wilayah (VA)</h2>
              </div>
              <div class="flex-1 flex flex-col md:flex-row gap-4">
                  <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 flex flex-col gap-4 h-full" id="listTop5"></div>
                  <div class="w-full md:w-[220px] flex items-center justify-center shrink-0 h-full pb-2">
                      <div id="chartDonut" class="w-full"></div>
                  </div>
              </div>
          </div>
      </div>

      <!-- TABEL BREAKDOWN -->
      <div class="bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden relative min-h-[200px]">
          <div id="loadTable" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
          <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
              <h2 class="text-base font-black text-slate-800">Breakdown Transaksi Area</h2>
          </div>
          <div class="overflow-x-auto custom-scrollbar max-h-[500px]">
              <table class="w-full text-left">
                  <thead class="sticky top-0 z-10">
                      <tr>
                          <th class="w-[250px] pl-4">NAMA AREA</th>
                          <th class="text-right">NOMINAL BULAN INI</th>
                          <th class="text-right">NOMINAL LALU</th>
                          <th class="text-center w-[100px]">GROWTH (RP)</th>
                          <th class="text-right">TRX BULAN INI</th>
                          <th class="text-right">TRX LALU</th>
                          <th class="text-center w-[100px] pr-4">GROWTH (TRX)</th>
                      </tr>
                  </thead>
                  <tbody id="bodyBreakdown" class="divide-y divide-slate-100"></tbody>
              </table>
          </div>
      </div>

      <!-- TABEL & CHART YOY (YEAR-OVER-YEAR) -->
      <div class="bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden relative min-h-[200px]">
          <div id="loadYoy" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
          <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
              <h2 class="text-base font-black text-slate-800">Perbandingan Year-over-Year (YOY)</h2>
              <span class="text-[10px] font-bold text-slate-400" id="lblYoyPeriode"></span>
          </div>
          <div class="p-4">
              <div id="chartYoy" class="w-full mb-4" style="min-height: 300px;"></div>
          </div>
          <div class="overflow-x-auto custom-scrollbar max-h-[500px]">
              <table class="w-full text-left">
                  <thead class="sticky top-0 z-10">
                      <tr>
                          <th class="w-[250px] pl-4">NAMA AREA</th>
                          <th class="text-right">NOMINAL TAHUN INI</th>
                          <th class="text-right">NOMINAL TAHUN LALU</th>
                          <th class="text-center w-[100px]">YOY (%)</th>
                          <th class="text-right">TRX TAHUN INI</th>
                          <th class="text-right">TRX TAHUN LALU</th>
                          <th class="text-center w-[100px] pr-4">YOY TRX (%)</th>
                      </tr>
                  </thead>
                  <tbody id="bodyYoy" class="divide-y divide-slate-100"></tbody>
              </table>
          </div>
      </div>

  </div> <!-- End Wrapper -->
</div>

<!-- ================= INFO MODAL ================= -->
<div id="ldInfoModal" role="dialog" aria-modal="true" aria-labelledby="ldInfoTitle">
    <div class="ld-backdrop" onclick="closeLdInfoModal()"></div>
    <div class="ld-dialog">
        <div class="flex justify-between items-start px-5 py-4 border-b bg-slate-50">
            <div class="flex items-start gap-3">
                <span class="bg-blue-100 text-blue-600 p-2 rounded-lg shrink-0">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <div>
                    <h3 id="ldInfoTitle" class="text-base font-extrabold text-slate-800 leading-tight">Tentang Dashboard Layanan Digital</h3>
                    <p class="text-[11px] text-slate-500 font-medium">Pesan ini akan tertutup otomatis dalam 6 detik.</p>
                </div>
            </div>
            <button type="button" onclick="closeLdInfoModal()" class="text-slate-400 hover:text-red-500 transition text-2xl leading-none -mt-1" aria-label="Tutup">&times;</button>
        </div>
        <div class="p-5 space-y-3 text-sm text-slate-600">
            <p>Dashboard ini menampilkan ringkasan transaksi <b>Layanan Digital</b> (VA, Branchless, QRIS) secara langsung tanpa perlu berpindah menu.</p>
            <ul class="list-disc pl-5 space-y-1.5 text-[13px]">
                <li><b>Summary Cards</b>: nominal &amp; growth per channel.</li>
                <li><b>Ringkasan Korwil</b>: peringkat 4 korwil berdasarkan total nominal.</li>
                <li><b>Tren &amp; Distribusi</b>: grafik transaksi sesuai channel terpilih.</li>
                <li><b>Breakdown</b>: rincian per area/cabang sesuai filter.</li>
                <li><b>YOY</b>: perbandingan tahun ke tahun.</li>
            </ul>
        </div>
        <div class="px-5 py-3 border-t bg-slate-50 flex justify-end">
            <button type="button" onclick="closeLdInfoModal()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-sm transition">Mengerti</button>
        </div>
        <div class="ld-progress" aria-hidden="true"></div>
    </div>
</div>

<script>
  const API_URL = './api/transaksi/'; 
  const API_KODE = './api/kode/';
  const API_DATE = './api/date/';

  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Number(n||0));
  
  let chartTrendObj = null;
  let chartDonutObj = null;
  let chartYoyObj = null;
  let currentActiveChannel = 'VA'; 

  const showLoad = (id) => document.getElementById(id)?.classList.remove('hidden');
  const hideLoad = (id) => document.getElementById(id)?.classList.add('hidden');

  async function getLastHarianData() {
      try { const r = await fetch(API_DATE); const j = await r.json(); return j.data || null; } 
      catch { return null; }
  }

  window.addEventListener('DOMContentLoaded', async () => {
    const user = (window.getUser && window.getUser()) || null;
    let uKode = user?.kode ? String(user.kode).padStart(3,'0') : '000';
    if(uKode === '099') uKode = '000'; 

    if (uKode === '000') {
        await loadCabangList();
        document.getElementById('opt_area').value = "KONSOLIDASI"; 
    } else {
        document.getElementById('opt_area').innerHTML = `<option value="${uKode}">CABANG ${uKode}</option>`;
        document.getElementById('opt_area').disabled = true; 
    }

    const dateData = await getLastHarianData();
    let hDate = new Date();
    
    if (dateData && dateData.last_created) {
        hDate = new Date(dateData.last_created);
        document.getElementById('harian_date').value = dateData.last_created;
    } else {
        document.getElementById('harian_date').value = hDate.toISOString().split('T')[0];
    }
    
    if (dateData && dateData.closing_date) {
        document.getElementById('closing_date').value = dateData.closing_date;
    } else {
        let pDate = new Date(hDate.getFullYear(), hDate.getMonth(), 0);
        let dd = String(pDate.getDate()).padStart(2, '0');
        let mm = String(pDate.getMonth() + 1).padStart(2, '0');
        let yyyy = pDate.getFullYear();
        document.getElementById('closing_date').value = `${yyyy}-${mm}-${dd}`;
    }
    
    initCharts();
    runFullSync(); 
  });

  async function loadCabangList() {
    try {
        const res = await fetch(API_KODE, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
        const json = await res.json();
        let html = '';
        (json.data || []).filter(x => x.kode_kantor && x.kode_kantor !== '000').sort((a,b) => a.kode_kantor.localeCompare(b.kode_kantor)).forEach(it => {
            html += `<option value="${String(it.kode_kantor).padStart(3,'0')}" class="text-slate-700">${String(it.kode_kantor).padStart(3,'0')} - ${it.nama_kantor}</option>`;
        });
        document.getElementById('opt_cabang_list').innerHTML = html;
    } catch(e){}
  }

  function parseAreaValue() {
      const val = document.getElementById('opt_area').value;
      let kode_kantor = ""; let korwil = "";
      if (val.startsWith('KORWIL_')) { korwil = val.replace('KORWIL_', ''); } 
      else if (val !== 'KONSOLIDASI') { kode_kantor = val; }
      return { kode_kantor, korwil };
  }

  document.getElementById('formFilterGlobal').addEventListener('submit', e => { 
      e.preventDefault(); 
      runFullSync(); 
  });

  function changeChannel(ch) {
      currentActiveChannel = ch;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.getElementById('tab_' + ch).classList.add('active');

      const namaCh = ch === 'VA' ? 'VA' : (ch === 'BRANCHLESS' ? 'Branchless' : 'QRIS');
      document.getElementById('titleTrend').innerText = `Tren Transaksi ${namaCh}`;
      document.getElementById('titleDistribusi').innerText = `Distribusi per Wilayah (${namaCh})`;
      const lblKw = document.getElementById('lblKorwilChannel'); if (lblKw) lblKw.innerText = namaCh;
      const lblTB = document.getElementById('lblTopBotChannel'); if (lblTB) lblTB.innerText = namaCh;

      fetchTrend();
      fetchDistribusi();
      fetchBreakdown();
      fetchYoy();
      fetchRingkasanKorwil();
      fetchTopBottomCabang();
  }

  async function runFullSync() {
      fetchSummaryCards();
      fetchRingkasanKorwil();
      fetchTopBottomCabang();
      fetchTrend();
      fetchDistribusi();
      fetchBreakdown();
      fetchYoy();
  }

  // ==========================================
  // 1. SUMMARY CARDS (BULLETPROOF FIX)
  // ==========================================
  async function fetchSummaryCards() {
      showLoad('loadSummary');
      const area = parseAreaValue();
      const payload = { 
          type: "summary_cards_transaksi",
          harian_date: document.getElementById('harian_date').value,
          closing_date: document.getElementById('closing_date').value,
          kode_kantor: area.kode_kantor,
          korwil: area.korwil
      };
      
      try {
          const res = await fetch(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const j = await res.json();
          
          // Mengecek dengan super aman apakah response Backend sukses dan data valid
          if(j.status === 200 && j.data && j.data.cards) {
              
              if(j.data.meta) {
                  document.getElementById('lbl_periode_aktif').innerHTML = `Periode: <span class="text-blue-700 font-bold">${j.data.meta.closing_date} s/d ${j.data.meta.harian_date}</span>`;
              }
              
              const container = document.getElementById('summaryCardsContainer');
              container.innerHTML = '';
              
              // Filter super aman agar tidak crash jika title string rusak
              const filteredCards = j.data.cards.filter(c => {
                  if(!c || !c.title) return false;
                  const t = String(c.title).toUpperCase();
                  return !t.includes('SEMUA CHANNEL') && !t.includes('TOTAL DIGITAL');
              });

              filteredCards.forEach((c) => {
                  const growthVal = parseFloat(c.growth || 0);
                  const isUp = growthVal >= 0;
                  const bColor = isUp ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700';
                  const arrow = isUp ? '▲' : '▼';
                  
                  const cTitleStr = String(c.title);
                  let bTop = 'border-t-4 border-t-slate-200';
                  if (cTitleStr.includes('VA') && !cTitleStr.includes('BANK')) bTop = 'border-t-4 border-t-blue-600';
                  else if (cTitleStr.includes('MANDIRI')) bTop = 'border-t-4 border-t-blue-400';

                  const pLabel = c.prev_label || 'Bulan Lalu';
                  const pNominal = c.prev_nominal || 'Rp -';

                  container.innerHTML += `
                      <div class="bg-white rounded-xl card-shadow p-3.5 flex flex-col justify-between ${bTop}">
                          <div>
                              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">${c.title}</p>
                              <h3 class="text-xl font-black text-slate-800 leading-tight">${c.value || 'Rp 0'}</h3>
                              <p class="text-[10px] font-bold text-slate-500 mt-0.5">${c.subtitle || ''}</p>
                          </div>
                          <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2">
                              <span class="${bColor} px-2 py-0.5 rounded font-bold text-[11px]">${arrow} ${Math.abs(growthVal)}%</span>
                              <div class="text-right leading-tight">
                                  <span class="text-[9px] text-slate-400">${pLabel}</span><br>
                                  <span class="text-[10px] font-bold text-slate-600">${pNominal}</span>
                              </div>
                          </div>
                      </div>`;
              });
          } else {
              // Jika server mengembalikan 500 (misal kena bug PHP tgl 31 bulan di Server)
              document.getElementById('lbl_periode_aktif').innerHTML = `<span class="text-red-500 font-bold ml-1">Gagal Muat Server: ${j.message || 'Error'}</span>`;
          }
      } catch (e) {
          // Jika terjadi error javascript
          document.getElementById('lbl_periode_aktif').innerHTML = `<span class="text-red-500 font-bold ml-1">Koneksi Terputus: ${e.message}</span>`;
      } finally { 
          hideLoad('loadSummary'); 
      }
  }

  // ==========================================
  // 2. GRAFIK TREN & PIE
  // ==========================================
  function initCharts() {
      chartTrendObj = new ApexCharts(document.querySelector("#chartTrend"), {
          series: [], 
          chart: { type: 'area', height: 340, parentHeightOffset: 0, toolbar: { show: false } },
          colors: ['#0284c7'], 
          dataLabels: { enabled: false }, 
          legend: { show: false }, 
          stroke: { curve: 'smooth', width: 3 },
          fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 100] } },
          grid: { padding: { bottom: 15, left: 10, right: 10 } },
          xaxis: { categories: [], labels: { style: { fontSize: '10px' }, offsetY: -5 }, tooltip: { enabled: false } },
          yaxis: { labels: { formatter: (val) => val >= 1000000000 ? (val/1000000000).toFixed(1)+' M' : (val >= 1000000 ? (val/1000000).toFixed(0)+' Jt' : val) } },
          tooltip: {
              theme: 'light',
              y: {
                  formatter: function(val, opts) {
                      const trx = opts.w.config.series[opts.seriesIndex].trx ? opts.w.config.series[opts.seriesIndex].trx[opts.dataPointIndex] : 0;
                      return `Rp ${nf.format(val)} <span style="color:#64748b; font-size:11px; margin-left:8px; font-weight:normal;">(${nf.format(trx)} Trx)</span>`; 
                  },
                  title: { formatter: () => '' }
              }
          }
      });
      chartTrendObj.render();

      chartDonutObj = new ApexCharts(document.querySelector("#chartDonut"), {
          series: [], 
          chart: { type: 'donut', height: 330, parentHeightOffset: 0 }, 
          labels: [],
          colors: ['#8b5cf6', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#64748b'],
          plotOptions: { donut: { size: '70%' } }, 
          dataLabels: { enabled: false }, 
          legend: { show: true, position: 'bottom', fontSize: '9.5px', fontFamily: 'Inter', offsetY: -5, markers: { width: 8, height: 8, radius: 2 }, itemMargin: { horizontal: 5, vertical: 2 } },
          tooltip: {
              custom: function({series, seriesIndex, dataPointIndex, w}) {
                  const val = series[seriesIndex];
                  const name = w.globals.labels[seriesIndex];
                  const color = w.globals.colors[seriesIndex];
                  const trx = w.config.customTrx ? w.config.customTrx[seriesIndex] : 0;
                  return `
                    <div style="padding: 6px 10px; background: #fff; border: 1px solid #e2e8f0; border-radius: 4px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transform: translateY(-20px);">
                        <div style="font-size: 10px; font-weight: 800; color: ${color}; margin-bottom: 2px;">${name}</div>
                        <div style="font-size: 12px; font-weight: 900; color: #1e293b;">Rp ${nf.format(val)}</div>
                        <div style="font-size: 10px; font-weight: 600; color: #64748b;">${nf.format(trx)} Transaksi</div>
                    </div>`;
              }
          }
      });
      chartDonutObj.render();

      chartYoyObj = new ApexCharts(document.querySelector("#chartYoy"), {
          series: [],
          chart: { type: 'bar', height: 300, toolbar: { show: false } },
          colors: ['#0284c7', '#94a3b8'],
          plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4, dataLabels: { position: 'top' } } },
          dataLabels: { enabled: false },
          stroke: { show: true, width: 2, colors: ['transparent'] },
          xaxis: { categories: [], labels: { style: { fontSize: '10px', fontWeight: 700 } } },
          yaxis: { labels: { formatter: (val) => val >= 1000000000 ? (val/1000000000).toFixed(1)+' M' : (val >= 1000000 ? (val/1000000).toFixed(0)+' Jt' : nf.format(val)), style: { fontSize: '10px' } } },
          fill: { opacity: 1 },
          legend: { position: 'top', fontSize: '12px', fontWeight: 700 },
          tooltip: { y: { formatter: (val) => 'Rp ' + nf.format(val) } }
      });
      chartYoyObj.render();
  }

  async function fetchTrend() {
      showLoad('loadTrend');
      const area = parseAreaValue();
      const payload = { type: "tren_nominal_va", harian_date: document.getElementById('harian_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, periode: document.getElementById('trendPeriode').value, channel: currentActiveChannel };
      try {
          const r = await fetch(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          if(j.status === 200 && j.data && j.data.chart_nominal) {
              chartTrendObj.updateOptions({ xaxis: { categories: j.data.chart_nominal.labels } });
              chartTrendObj.updateSeries(j.data.chart_nominal.series);
          }
      } catch(e){} finally { hideLoad('loadTrend'); }
  }

  async function fetchDistribusi() {
      showLoad('loadDist');
      const area = parseAreaValue();
      const payload = { type: "distribusi_va", harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, channel: currentActiveChannel };
      
      try {
          const r = await fetch(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          if(j.status === 200 && j.data) {
              const d = j.data;
              
              chartDonutObj.updateOptions({ labels: d.donut_chart.labels, customTrx: d.donut_chart.trx });
              chartDonutObj.updateSeries(d.donut_chart.series);

              const listC = document.getElementById('listTop5'); listC.innerHTML = '';
              const colors = ['bg-violet-500', 'bg-sky-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500'];
              if(!d.top_5 || d.top_5.length === 0) { listC.innerHTML = '<p class="text-xs text-slate-400">Data kosong</p>'; hideLoad('loadDist'); return; }
              const maxNom = d.top_5[0].nominal;
              
              d.top_5.forEach((t, i) => {
                  const wPct = (t.nominal / maxNom) * 100;
                  const cColor = colors[i % colors.length];
                  let fNom = t.nominal >= 1000000000 ? (t.nominal/1000000000).toFixed(2)+' M' : (t.nominal >= 1000000 ? (t.nominal/1000000).toFixed(1)+' jt' : nf.format(t.nominal));
                  listC.innerHTML += `
                      <div class="flex flex-col text-xs">
                          <div class="flex justify-between items-end mb-1">
                              <div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full ${cColor} text-white flex items-center justify-center text-[9px] font-bold">${i+1}</span><span class="font-bold text-slate-700">${t.label}</span></div>
                              <div class="text-right leading-none"><span class="font-black text-slate-800">Rp ${fNom}</span><br><span class="text-[9px] text-slate-400">${nf.format(t.trx)} Trx</span></div>
                          </div>
                          <div class="w-full bg-slate-100 rounded-full h-1.5"><div class="${cColor} h-1.5 rounded-full" style="width: ${wPct}%"></div></div>
                      </div>`;
              });
          }
      } catch(e){} finally { hideLoad('loadDist'); }
  }

  // ==========================================
  // 3. TABEL BREAKDOWN 
  // ==========================================
  async function fetchBreakdown() {
      showLoad('loadTable');
      const area = parseAreaValue();
      const payload = { type: "detail_breakdown_transaksi", harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, channel: currentActiveChannel };
      
      try {
          const res = await fetch(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const j = await res.json();
          const tbody = document.getElementById('bodyBreakdown');
          tbody.innerHTML = '';

          if(j.status !== 200 || !j.data || !j.data.data.length) { tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6">Data kosong.</td></tr>`; hideLoad('loadTable'); return; }

          const dt = j.data.data;
          const optAreaVal = document.getElementById('opt_area').value;
          const isKonsolidasi = optAreaVal === 'KONSOLIDASI'; 
          const isSpecificKorwil = optAreaVal.startsWith('KORWIL_');

          const rHtml = (nama, cN, pN, gN, cT, pT, gT, isBold=false, isChild=false) => {
              const bg = isBold ? 'bg-slate-50 font-bold' : (isChild ? 'text-slate-600 bg-slate-50/20' : 'font-bold text-slate-700');
              const pad = isChild ? 'pl-10 relative before:absolute before:w-3 before:h-px before:bg-slate-300 before:left-5 before:top-1/2' : 'pl-4';
              const c_gN = gN > 0 ? `<span class="text-emerald-600">▲ ${gN}%</span>` : (gN < 0 ? `<span class="text-red-600">▼ ${Math.abs(gN)}%</span>` : '-');
              const c_gT = gT > 0 ? `<span class="text-emerald-600">▲ ${gT}%</span>` : (gT < 0 ? `<span class="text-red-600">▼ ${Math.abs(gT)}%</span>` : '-');
              
              return `<tr class="${bg}"><td class="${pad}">${nama}</td><td class="text-right text-blue-700">${fmt(cN)}</td><td class="text-right text-[10px] text-slate-400">${fmt(pN)}</td><td class="text-center text-[11px] font-bold bg-slate-50/50">${c_gN}</td><td class="text-right text-indigo-700">${fmt(cT)}</td><td class="text-right text-[10px] text-slate-400">${fmt(pT)}</td><td class="text-center text-[11px] font-bold bg-slate-50/50 pr-4">${c_gT}</td></tr>`;
          };

          const gt = j.data.grand_total;
          tbody.innerHTML += rHtml('GRAND TOTAL', gt.curr_nom, gt.prev_nom, gt.growth_nom, gt.curr_trx, gt.prev_trx, gt.growth_trx, true);

          if (isKonsolidasi) {
              dt.forEach(kw => {
                  tbody.innerHTML += rHtml(kw.korwil, kw.curr_nom, kw.prev_nom, kw.growth_nom, kw.curr_trx, kw.prev_trx, kw.growth_trx);
              });
          } else if (isSpecificKorwil) {
              dt.forEach(kw => {
                  kw.cabang.forEach(cb => {
                      tbody.innerHTML += rHtml(cb.nama, cb.curr_nom, cb.prev_nom, cb.growth_nom, cb.curr_trx, cb.prev_trx, cb.growth_trx);
                  });
              });
          } else {
              dt.forEach(kk => {
                  tbody.innerHTML += rHtml(kk.nama, kk.curr_nom, kk.prev_nom, kk.growth_nom, kk.curr_trx, kk.prev_trx, kk.growth_trx);
              });
          }
      } catch(e){} finally { hideLoad('loadTable'); }
  }

  // ==========================================
  // 4. YOY (YEAR-OVER-YEAR) COMPARISON
  // ==========================================
  async function fetchYoy() {
      showLoad('loadYoy');
      const area = parseAreaValue();
      const payload = { 
          type: "yoy_transaksi", 
          harian_date: document.getElementById('harian_date').value, 
          closing_date: document.getElementById('closing_date').value, 
          kode_kantor: area.kode_kantor, 
          korwil: area.korwil, 
          channel: currentActiveChannel 
      };

      try {
          const res = await fetch(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const j = await res.json();
          const tbody = document.getElementById('bodyYoy');
          tbody.innerHTML = '';

          if(j.status !== 200 || !j.data || !j.data.data.length) { 
              tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6">Data kosong.</td></tr>`; 
              chartYoyObj.updateSeries([]);
              hideLoad('loadYoy'); 
              return; 
          }

          // Update period label
          if(j.data.meta) {
              const pc = j.data.meta.periode_curr;
              const pp = j.data.meta.periode_prev;
              document.getElementById('lblYoyPeriode').innerHTML = `Periode: ${pc.start} s/d ${pc.end} vs ${pp.start} s/d ${pp.end}`;
          }

          // Update Chart
          if(j.data.chart) {
              chartYoyObj.updateOptions({ xaxis: { categories: j.data.chart.labels } });
              chartYoyObj.updateSeries(j.data.chart.series);
          }

          // Render Table
          const dt = j.data.data;
          const optAreaVal = document.getElementById('opt_area').value;
          const isKonsolidasi = optAreaVal === 'KONSOLIDASI';
          const isSpecificKorwil = optAreaVal.startsWith('KORWIL_');

          const rYoy = (nama, cN, pN, gN, cT, pT, gT, isBold=false, isChild=false) => {
              const bg = isBold ? 'bg-slate-50 font-bold' : (isChild ? 'text-slate-600 bg-slate-50/20' : 'font-bold text-slate-700');
              const pad = isChild ? 'pl-10 relative before:absolute before:w-3 before:h-px before:bg-slate-300 before:left-5 before:top-1/2' : 'pl-4';
              const c_gN = gN > 0 ? `<span class="text-emerald-600">&#9650; ${gN}%</span>` : (gN < 0 ? `<span class="text-red-600">&#9660; ${Math.abs(gN)}%</span>` : '-');
              const c_gT = gT > 0 ? `<span class="text-emerald-600">&#9650; ${gT}%</span>` : (gT < 0 ? `<span class="text-red-600">&#9660; ${Math.abs(gT)}%</span>` : '-');
              return `<tr class="${bg}"><td class="${pad}">${nama}</td><td class="text-right text-blue-700">${fmt(cN)}</td><td class="text-right text-slate-400">${fmt(pN)}</td><td class="text-center text-[11px] font-bold bg-slate-50/50">${c_gN}</td><td class="text-right text-indigo-700">${fmt(cT)}</td><td class="text-right text-slate-400">${fmt(pT)}</td><td class="text-center text-[11px] font-bold bg-slate-50/50 pr-4">${c_gT}</td></tr>`;
          };

          const gt = j.data.grand_total;
          tbody.innerHTML += rYoy('GRAND TOTAL', gt.curr_nom, gt.prev_nom, gt.yoy_growth_nom, gt.curr_trx, gt.prev_trx, gt.yoy_growth_trx, true);

          if (isKonsolidasi) {
              dt.forEach(kw => {
                  tbody.innerHTML += rYoy(kw.korwil, kw.curr_nom, kw.prev_nom, kw.yoy_growth_nom, kw.curr_trx, kw.prev_trx, kw.yoy_growth_trx);
              });
          } else if (isSpecificKorwil) {
              dt.forEach(kw => {
                  (kw.cabang || []).forEach(cb => {
                      tbody.innerHTML += rYoy(cb.nama, cb.curr_nom, cb.prev_nom, cb.yoy_growth_nom, cb.curr_trx, cb.prev_trx, cb.yoy_growth_trx, false, true);
                  });
              });
          } else {
              dt.forEach(kk => {
                  tbody.innerHTML += rYoy(kk.nama, kk.curr_nom, kk.prev_nom, kk.yoy_growth_nom, kk.curr_trx, kk.prev_trx, kk.yoy_growth_trx);
              });
          }
      } catch(e){
          document.getElementById('bodyYoy').innerHTML = `<tr><td colspan="7" class="text-center py-6 text-red-500">Gagal memuat data YOY.</td></tr>`;
      } finally { hideLoad('loadYoy'); }
  }

  // ==========================================
  // 5. RINGKASAN KORWIL (LEADERBOARD)
  // ==========================================
  async function fetchRingkasanKorwil() {
      const container = document.getElementById('korwilLeaderboard');
      if (!container) return;
      showLoad('loadKorwil');
      const payload = {
          type: "detail_breakdown_transaksi",
          harian_date: document.getElementById('harian_date').value,
          closing_date: document.getElementById('closing_date').value,
          kode_kantor: "",
          korwil: "",
          channel: currentActiveChannel
      };
      try {
          const res = await fetch(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const j = await res.json();
          container.innerHTML = '';
          if (j.status !== 200 || !j.data || !Array.isArray(j.data.data) || !j.data.data.length) {
              container.innerHTML = `<div class="col-span-full text-center text-xs text-slate-400 py-4">Data ringkasan korwil belum tersedia.</div>`;
              return;
          }
          const sorted = [...j.data.data].sort((a,b) => (Number(b.curr_nom)||0) - (Number(a.curr_nom)||0));
          const rankClass = ['rank-1','rank-2','rank-3','rank-x'];
          const rankLabel = ['#1','#2','#3','#4'];

          sorted.forEach((kw, i) => {
              const gN = Number(kw.growth_nom || 0);
              const isUp = gN >= 0;
              const arrow = isUp ? '&#9650;' : '&#9660;';
              const gColor = isUp ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50';
              const nom = Number(kw.curr_nom||0);
              const fNom = nom >= 1000000000 ? (nom/1000000000).toFixed(2)+' M' : (nom >= 1000000 ? (nom/1000000).toFixed(1)+' Jt' : fmt(nom));
              const isTop = i === 0 ? 'is-top' : '';
              const rcls = rankClass[i] || 'rank-x';
              const rlbl = rankLabel[i] || `#${i+1}`;
              const namaKw = kw.korwil || kw.nama || `Korwil ${i+1}`;

              container.innerHTML += `
                <div class="korwil-card ${isTop}">
                    <span class="rank-badge ${rcls}">${rlbl}</span>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Korwil</p>
                    <h3 class="text-sm font-black text-slate-800 leading-tight mb-2 truncate">${namaKw}</h3>
                    <div class="flex items-end justify-between gap-2">
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Nominal</p>
                            <p class="text-base font-black text-blue-700 leading-tight">Rp ${fNom}</p>
                            <p class="text-[10px] text-slate-500 font-bold mt-0.5">${fmt(kw.curr_trx||0)} Trx</p>
                        </div>
                        <span class="${gColor} px-2 py-0.5 rounded font-bold text-[11px] shrink-0">${arrow} ${Math.abs(gN)}%</span>
                    </div>
                </div>`;
          });
      } catch(e) {
          container.innerHTML = `<div class="col-span-full text-center text-xs text-red-500 py-4">Gagal memuat ringkasan korwil.</div>`;
      } finally {
          hideLoad('loadKorwil');
      }
  }

  // ==========================================
  // 6. TOP & BOTTOM 5 CABANG
  // ==========================================
  async function fetchTopBottomCabang() {
      const topEl = document.getElementById('listTop5Cabang');
      const botEl = document.getElementById('listBottom5Cabang');
      if (!topEl || !botEl) return;
      showLoad('loadTopBottom');
      const payload = {
          type: "top_bottom_cabang",
          harian_date: document.getElementById('harian_date').value,
          closing_date: document.getElementById('closing_date').value,
          channel: currentActiveChannel
      };
      try {
          const res = await fetch(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const j = await res.json();
          topEl.innerHTML = ''; botEl.innerHTML = '';
          if (j.status !== 200 || !j.data) {
              topEl.innerHTML = `<p class="text-xs text-slate-400">Data belum tersedia.</p>`;
              botEl.innerHTML = `<p class="text-xs text-slate-400">Data belum tersedia.</p>`;
              return;
          }
          const renderList = (container, items, colorClass, bgClass) => {
              if (!items || !items.length) { container.innerHTML = `<p class="text-xs text-slate-400">Data kosong.</p>`; return; }
              const maxNom = items[0].nominal || 1;
              items.forEach((it, i) => {
                  const pct = Math.max((it.nominal / maxNom) * 100, 3);
                  const fNom = it.nominal >= 1000000000 ? (it.nominal/1000000000).toFixed(2)+' M' : (it.nominal >= 1000000 ? (it.nominal/1000000).toFixed(1)+' Jt' : fmt(it.nominal));
                  container.innerHTML += `
                    <div class="flex items-center gap-3 p-2.5 rounded-lg border border-slate-100 hover:border-slate-200 transition">
                        <span class="w-7 h-7 rounded-full ${bgClass} ${colorClass} flex items-center justify-center text-xs font-black shrink-0">${i+1}</span>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-slate-700 truncate">${it.kode} - ${it.nama}</span>
                                <span class="text-xs font-black text-slate-800 shrink-0 ml-2">Rp ${fNom}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div class="${colorClass.replace('text-','bg-')} h-1.5 rounded-full transition-all" style="width:${pct}%"></div>
                            </div>
                            <span class="text-[9px] text-slate-400 font-bold mt-0.5">${fmt(it.trx)} Transaksi</span>
                        </div>
                    </div>`;
              });
          };
          renderList(topEl, j.data.top_5, 'text-emerald-600', 'bg-emerald-100');
          renderList(botEl, j.data.bottom_5, 'text-red-600', 'bg-red-100');
      } catch(e) {
          topEl.innerHTML = `<p class="text-xs text-red-500">Gagal memuat data.</p>`;
          botEl.innerHTML = `<p class="text-xs text-red-500">Gagal memuat data.</p>`;
      } finally {
          hideLoad('loadTopBottom');
      }
  }

  // ==========================================
  // 7. INFO MODAL (Auto-close 6 detik)
  // ==========================================
  let _ldInfoTimer = null;
  function openLdInfoModal() {
      const m = document.getElementById('ldInfoModal');
      if (!m) return;
      m.classList.add('is-open');
      const bar = m.querySelector('.ld-progress');
      if (bar) { bar.style.animation = 'none'; void bar.offsetWidth; bar.style.animation = ''; }
      if (_ldInfoTimer) clearTimeout(_ldInfoTimer);
      _ldInfoTimer = setTimeout(closeLdInfoModal, 6000);
  }
  function closeLdInfoModal() {
      const m = document.getElementById('ldInfoModal');
      if (!m) return;
      m.classList.remove('is-open');
      if (_ldInfoTimer) { clearTimeout(_ldInfoTimer); _ldInfoTimer = null; }
  }
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLdInfoModal(); });
</script>
<!-- Load Library ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
  :root { --primary: #2563eb; --bg: #f8fafc; --text: #334155; }
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
  .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  th { background-color: #f8fafc; color: #1e293b; font-weight: 800; padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; font-size: 11px; }
  td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-weight: 700; color: #334155; }
  tr:hover td { background-color: #eff6ff; }
  .card-shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
  .local-loader { position: absolute; inset: 0; background: rgba(255,255,255,0.7); z-index: 50; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(2px); border-radius: inherit; }
  .local-loader.hidden { display: none; }
  .apexcharts-tooltip { z-index: 99999 !important; background: transparent !important; border: none !important; box-shadow: none !important; }
  @media (max-width: 767px) {
    th { padding: 8px 6px; font-size: 10px; }
    td { padding: 8px 6px; font-size: 11px; }
  }
</style>

<div class="max-w-[1600px] mx-auto px-2 md:px-4 py-4 md:py-6 min-h-screen font-sans space-y-3 md:space-y-4">
  <!-- HEADER & FILTER -->
  <div class="flex flex-col md:flex-row justify-between md:items-end gap-3">
    <div class="flex justify-between items-center w-full md:w-auto">
      <div class="flex items-center gap-2">
        <div>
          <h1 class="text-xl md:text-2xl font-extrabold text-gray-800 tracking-tight flex items-center gap-2">
              <span class="bg-blue-600 text-white p-1.5 rounded-lg text-sm shadow-sm">
                  <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
              </span>
              Virtual Account (VA)
          </h1>
          <p class="text-[10px] md:text-xs text-gray-500 mt-0.5 font-medium" id="lbl_periode_aktif">Menunggu data sinkronisasi...</p>
        </div>
        <button type="button" onclick="openNarrative()" class="w-7 h-7 flex items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-colors shadow-sm" title="Narasi Otomatis">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        </button>
      </div>
      <button type="button" id="btnFilterToggle" onclick="toggleFilter()" class="md:hidden flex items-center gap-1.5 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 active:scale-95 transition-transform">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
        <span id="filterToggleLabel">Filter</span>
      </button>
    </div>
    <form id="formFilterGlobal" class="hidden md:flex flex-col md:flex-row items-end gap-2.5 md:gap-3 bg-white p-2.5 md:p-3 rounded-xl shadow-sm border border-gray-200 w-full md:w-auto">
      <div class="flex w-full md:w-auto gap-2 shrink-0">
        <div class="flex flex-col flex-1 min-w-0 md:w-[130px]">
          <label class="text-[9px] md:text-[10px] font-bold text-gray-500 uppercase tracking-wider">Closing M-1</label>
          <input type="date" id="closing_date" class="border-b-2 border-transparent hover:border-gray-300 px-1 py-1 text-[10px] md:text-sm outline-none focus:border-blue-500 transition-colors font-semibold cursor-pointer w-full bg-transparent" required>
        </div>
        <div class="flex flex-col flex-1 min-w-0 md:w-[130px]">
          <label class="text-[9px] md:text-[10px] font-bold text-gray-500 uppercase tracking-wider">Harian/Actual</label>
          <input type="date" id="harian_date" class="border-b-2 border-transparent hover:border-gray-300 px-1 py-1 text-[10px] md:text-sm outline-none focus:border-blue-500 transition-colors font-semibold cursor-pointer w-full bg-transparent" required>
        </div>
      </div>
      <div class="flex w-full md:w-auto items-end gap-2 shrink-0 mt-0.5 md:mt-0">
        <div class="flex flex-col flex-1 min-w-0 md:w-[180px]">
          <label class="text-[9px] md:text-[10px] font-bold text-gray-500 uppercase tracking-wider">Area/Cabang</label>
          <select id="opt_area" class="border-b-2 border-transparent hover:border-gray-300 px-1 py-1 text-[10px] md:text-sm outline-none focus:border-blue-500 bg-transparent transition-colors font-bold text-blue-700 cursor-pointer w-full truncate">
              <option value="KONSOLIDASI" class="font-bold">Konsolidasi</option>
              <optgroup label="Berdasarkan Korwil" class="text-gray-400">
                  <option value="KORWIL_SEMARANG" class="text-gray-700">Korwil Semarang</option>
                  <option value="KORWIL_SOLO" class="text-gray-700">Korwil Solo</option>
                  <option value="KORWIL_BANYUMAS" class="text-gray-700">Korwil Banyumas</option>
                  <option value="KORWIL_PEKALONGAN" class="text-gray-700">Korwil Pekalongan</option>
              </optgroup>
              <optgroup label="Berdasarkan Cabang" id="opt_cabang_list" class="text-gray-400"></optgroup>
          </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white w-[34px] md:w-auto h-[32px] md:h-[36px] md:px-5 rounded-lg text-sm font-bold transition-all flex items-center justify-center gap-2 shadow-md active:scale-95 shrink-0 mb-[1px]">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="md:hidden"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <span class="hidden md:inline">Tampilkan</span>
        </button>
      </div>
    </form>
  </div>

  <!-- TAB NAVIGATION -->
  <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg w-fit">
    <button onclick="switchView('rekap')" id="tab-rekap" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all bg-white shadow-sm text-blue-700" title="Rekap Cabang">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
      <span class="hidden md:inline">Rekap</span>
    </button>
    <button onclick="switchView('chart')" id="tab-chart" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700" title="Chart">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
      <span class="hidden md:inline">Chart</span>
    </button>
    <button onclick="switchView('detail')" id="tab-detail" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700" title="Detail">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
      <span class="hidden md:inline">Detail</span>
    </button>
  </div>

  <!-- SECTION: REKAP (Unified MoM+YoY Breakdown) -->
  <div id="section-rekap">
  <!-- UNIFIED BREAKDOWN TABLE - MoM & YoY -->
  <div class="bg-white rounded-xl md:rounded-2xl border border-gray-100 flex flex-col overflow-hidden card-shadow relative min-h-[200px]">
      <div id="loadTable" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
      <div class="px-3 md:px-4 py-3 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <h2 class="text-[13px] md:text-base font-black text-gray-800">Breakdown Transaksi VA - MoM & YoY</h2>
      </div>
      <div class="overflow-x-auto custom-scrollbar max-h-[600px]">
          <table class="w-full text-left" style="min-width: 800px;">
              <thead class="sticky top-0 z-10">
                  <tr>
                      <th class="w-[40px] pl-4 text-center">NO</th>
                      <th class="w-[200px]">NAMA CABANG</th>
                      <th class="text-right">NOM THN LALU</th>
                      <th class="text-right">NOM THN INI</th>
                      <th class="text-center w-[80px]">GAP% YOY</th>
                      <th class="text-right">NOM BLN LALU</th>
                      <th class="text-right">NOM BLN INI</th>
                      <th class="text-center w-[80px] pr-4">GAP% MOM</th>
                  </tr>
              </thead>
              <tbody id="bodyUnified" class="divide-y divide-slate-100"></tbody>
          </table>
      </div>
  </div>
  </div><!-- end section-rekap -->

  <!-- SECTION: CHART (Mandiri vs Permata) -->
  <div id="section-chart" class="hidden space-y-3 md:space-y-4">
  <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
      <div class="bg-white rounded-xl border border-slate-200 p-4 md:p-5 card-shadow relative" style="min-height: 350px;">
          <div id="loadChartNom" class="local-loader hidden rounded-xl"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
          <h2 class="font-bold text-slate-800 text-sm mb-2">Tren Nominal VA (Mandiri vs Permata)</h2>
          <div id="chartNominal" class="w-full"></div>
      </div>
      <div class="bg-white rounded-xl border border-slate-200 p-4 md:p-5 card-shadow relative" style="min-height: 350px;">
          <div id="loadChartTrx" class="local-loader hidden rounded-xl"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
          <h2 class="font-bold text-slate-800 text-sm mb-2">Tren Transaksi VA (Mandiri vs Permata)</h2>
          <div id="chartTrx" class="w-full"></div>
      </div>
  </div>
  </div><!-- end section-chart -->

  <!-- SECTION: DETAIL (Monthly Breakdown) -->
  <div id="section-detail" class="hidden">
  <div class="bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden card-shadow relative min-h-[200px]">
      <div id="loadMonthly" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
      <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
          <h2 class="text-base font-black text-slate-800">Breakdown Bulanan - Mandiri vs Permata</h2>
          <span class="text-[10px] font-bold text-slate-400" id="lblTahun"></span>
      </div>
      <div class="overflow-x-auto custom-scrollbar max-h-[600px]">
          <table class="w-full text-left" style="min-width: 700px;">
              <thead class="sticky top-0 z-10">
                  <tr>
                      <th class="w-[40px] text-center pl-2">NO</th>
                      <th class="w-[120px] pl-4">BULAN</th>
                      <th class="text-right text-blue-700" style="background:#eff6ff;">MANDIRI (NOM)</th>
                      <th class="text-right text-blue-700" style="background:#eff6ff;">MANDIRI (TRX)</th>
                      <th class="text-right text-orange-700" style="background:#fff7ed;">PERMATA (NOM)</th>
                      <th class="text-right text-orange-700" style="background:#fff7ed;">PERMATA (TRX)</th>
                      <th class="text-right font-black">TOTAL (NOM)</th>
                      <th class="text-right font-black pr-4">TOTAL (TRX)</th>
                  </tr>
              </thead>
              <tbody id="bodyMonthly" class="divide-y divide-slate-100"></tbody>
              <tfoot class="bg-slate-50 font-black border-t-2 border-slate-300">
                  <tr id="footerMonthly"></tr>
              </tfoot>
          </table>
      </div>
  </div>
  </div><!-- end section-detail -->

  <!-- Narrative Modal -->
  <div id="modalNarrative" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeNarrative()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[80vh] overflow-y-auto p-6">
      <button onclick="closeNarrative()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        Narasi Otomatis
      </h3>
      <div id="narrativeContent" class="text-sm text-gray-700 space-y-2"></div>
    </div>
  </div>
</div>

<script>
  const API_URL = './api/transaksi/';
  const API_KODE = './api/kode/';
  const API_DATE = './api/date/';
  const CHANNEL = 'VA';

  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Number(n||0));
  const esc = (s) => String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

  let chartNomObj = null;
  let chartTrxObj = null;

  const showLoad = (id) => document.getElementById(id)?.classList.remove('hidden');
  const hideLoad = (id) => document.getElementById(id)?.classList.add('hidden');

  function toggleFilter() {
      const form = document.getElementById('formFilterGlobal');
      const label = document.getElementById('filterToggleLabel');
      if (form.classList.contains('hidden')) {
          form.classList.remove('hidden');
          form.classList.add('flex');
          label.textContent = 'Tutup';
      } else {
          form.classList.add('hidden');
          form.classList.remove('flex');
          label.textContent = 'Filter';
      }
  }

  function switchView(view) {
      document.getElementById('section-rekap').classList.add('hidden');
      document.getElementById('section-chart').classList.add('hidden');
      document.getElementById('section-detail').classList.add('hidden');
      document.getElementById('section-' + view).classList.remove('hidden');
      ['rekap','chart','detail'].forEach(t => {
          const btn = document.getElementById('tab-' + t);
          if (t === view) {
              btn.className = 'flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all bg-white shadow-sm text-blue-700';
          } else {
              btn.className = 'flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700';
          }
      });
      if (view === 'chart') {
          setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 100);
      }
  }

  function openNarrative() {
      document.getElementById('narrativeContent').innerHTML = generateNarrative();
      document.getElementById('modalNarrative').classList.remove('hidden');
  }
  function closeNarrative() {
      document.getElementById('modalNarrative').classList.add('hidden');
  }
  function escNarrative(str) {
      const div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
  }
  function generateNarrative() {
      let html = '';
      const tbody = document.getElementById('bodyUnified');
      const rows = tbody ? tbody.querySelectorAll('tr') : [];
      if (rows.length > 1) {
          const gtRow = rows[0];
          const gtCells = gtRow.querySelectorAll('td');
          const totalNomBlnIni = gtCells[6] ? gtCells[6].textContent.trim() : '-';
          const totalGrowthMom = gtCells[7] ? gtCells[7].textContent.trim() : '-';
          const totalNomThnIni = gtCells[3] ? gtCells[3].textContent.trim() : '-';
          const totalGrowthYoy = gtCells[4] ? gtCells[4].textContent.trim() : '-';
          html += '<p class="font-bold text-gray-800">Ringkasan Virtual Account:</p>';
          html += '<ul class="list-disc list-inside space-y-1">';
          html += '<li>Total nominal bulan ini: <strong>' + escNarrative(totalNomBlnIni) + '</strong> (Growth MoM: ' + escNarrative(totalGrowthMom) + ')</li>';
          html += '<li>Total nominal tahun ini: <strong>' + escNarrative(totalNomThnIni) + '</strong> (Growth YoY: ' + escNarrative(totalGrowthYoy) + ')</li>';
          if (rows.length > 2) {
              const topRow = rows[1].querySelectorAll('td');
              const topNama = topRow[1] ? topRow[1].textContent.trim() : '-';
              const topNom = topRow[6] ? topRow[6].textContent.trim() : '-';
              html += '<li>Top performer: <strong>' + escNarrative(topNama) + '</strong> dengan nominal ' + escNarrative(topNom) + '</li>';
          }
          html += '</ul>';
      } else {
          html += '<p class="text-gray-400">Data belum tersedia. Silakan muat data terlebih dahulu.</p>';
      }
      return html;
  }

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
        document.getElementById('opt_area').value = 'KONSOLIDASI';
    } else {
        document.getElementById('opt_area').innerHTML = '<option value="' + uKode + '">CABANG ' + uKode + '</option>';
        document.getElementById('opt_area').disabled = true;
    }

    const dateData = await getLastHarianData();
    const today = new Date();

    document.getElementById('harian_date').value = today.toISOString().split('T')[0];
    const prevMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
    document.getElementById('closing_date').value = prevMonthEnd.getFullYear() + '-' + String(prevMonthEnd.getMonth()+1).padStart(2,'0') + '-' + String(prevMonthEnd.getDate()).padStart(2,'0');

    if (dateData) {
        if (dateData.last_created) document.getElementById('harian_date').value = dateData.last_created;
        if (dateData.last_closing) document.getElementById('closing_date').value = dateData.last_closing;
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
            html += '<option value="' + String(it.kode_kantor).padStart(3,'0') + '" class="text-slate-700">' + String(it.kode_kantor).padStart(3,'0') + ' - ' + it.nama_kantor + '</option>';
        });
        document.getElementById('opt_cabang_list').innerHTML = html;
    } catch(e){}
  }

  function parseAreaValue() {
      const val = document.getElementById('opt_area').value;
      let kode_kantor = ''; let korwil = '';
      if (val.startsWith('KORWIL_')) { korwil = val.replace('KORWIL_', ''); }
      else if (val !== 'KONSOLIDASI') { kode_kantor = val; }
      return { kode_kantor, korwil };
  }

  document.getElementById('formFilterGlobal').addEventListener('submit', e => { e.preventDefault(); runFullSync(); });

  async function runFullSync() { fetchMonthlyData(); fetchUnifiedBreakdown(); }

  // ==========================================
  // INIT CHARTS
  // ==========================================
  function initCharts() {
      chartNomObj = new ApexCharts(document.querySelector('#chartNominal'), {
          series: [],
          chart: { type: 'area', height: 280, toolbar: { show: false } },
          colors: ['#2563eb', '#f97316'],
          dataLabels: { enabled: false },
          stroke: { curve: 'smooth', width: 3 },
          fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 100] } },
          xaxis: { categories: [], labels: { style: { fontSize: '10px' } } },
          yaxis: { labels: { formatter: (val) => val >= 1000000000 ? (val/1000000000).toFixed(1)+' M' : (val >= 1000000 ? (val/1000000).toFixed(0)+' Jt' : nf.format(val)) } },
          legend: { position: 'top', fontSize: '12px', fontWeight: 700 },
          tooltip: { y: { formatter: (val) => 'Rp ' + nf.format(val) } }
      });
      chartNomObj.render();

      chartTrxObj = new ApexCharts(document.querySelector('#chartTrx'), {
          series: [],
          chart: { type: 'bar', height: 280, toolbar: { show: false } },
          colors: ['#2563eb', '#f97316'],
          plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
          dataLabels: { enabled: false },
          stroke: { show: true, width: 2, colors: ['transparent'] },
          xaxis: { categories: [], labels: { style: { fontSize: '10px' } } },
          yaxis: { labels: { formatter: (val) => nf.format(val) } },
          fill: { opacity: 1 },
          legend: { position: 'top', fontSize: '12px', fontWeight: 700 },
          tooltip: { y: { formatter: (val) => nf.format(val) + ' Trx' } }
      });
      chartTrxObj.render();
  }

  // ==========================================
  // FETCH MONTHLY VA DATA (MANDIRI VS PERMATA)
  // ==========================================
  async function fetchMonthlyData() {
      showLoad('loadMonthly');
      showLoad('loadChartNom');
      showLoad('loadChartTrx');
      const area = parseAreaValue();
      const payload = {
          type: 'va_detail_mandiri_permata',
          harian_date: document.getElementById('harian_date').value,
          closing_date: document.getElementById('closing_date').value,
          kode_kantor: area.kode_kantor,
          korwil: area.korwil
      };

      try {
          const res = await fetch(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const j = await res.json();

          if (j.status === 200 && j.data) {
              const data = j.data;

              if (data.meta) {
                  document.getElementById('lbl_periode_aktif').innerHTML = 'Tahun: <span class="text-blue-700 font-bold">' + data.meta.tahun + '</span> | Filter: <span class="text-blue-700 font-bold">' + data.meta.filter + '</span>';
                  document.getElementById('lblTahun').textContent = 'Tahun ' + data.meta.tahun;
              }

              // Render table
              const tbody = document.getElementById('bodyMonthly');
              const tfoot = document.getElementById('footerMonthly');
              tbody.innerHTML = '';

              data.monthly.forEach((row, idx) => {
                  const totalNom = row.mandiri_nom + row.permata_nom;
                  const totalTrx = row.mandiri_trx + row.permata_trx;
                  tbody.innerHTML += '<tr><td class="text-center text-slate-400 pl-2">' + (idx + 1) + '</td><td class="pl-4 font-bold text-slate-700">' + esc(row.bulan) + '</td><td class="text-right text-blue-700">' + fmt(row.mandiri_nom) + '</td><td class="text-right text-blue-600">' + fmt(row.mandiri_trx) + '</td><td class="text-right text-orange-700">' + fmt(row.permata_nom) + '</td><td class="text-right text-orange-600">' + fmt(row.permata_trx) + '</td><td class="text-right font-black text-slate-800">' + fmt(totalNom) + '</td><td class="text-right font-black text-slate-800 pr-4">' + fmt(totalTrx) + '</td></tr>';
              });

              const t = data.total;
              const grandNom = t.mandiri_nom + t.permata_nom;
              const grandTrx = t.mandiri_trx + t.permata_trx;
              tfoot.innerHTML = '<td class="text-center pl-2">-</td><td class="pl-4">TOTAL</td><td class="text-right text-blue-800">' + fmt(t.mandiri_nom) + '</td><td class="text-right text-blue-800">' + fmt(t.mandiri_trx) + '</td><td class="text-right text-orange-800">' + fmt(t.permata_nom) + '</td><td class="text-right text-orange-800">' + fmt(t.permata_trx) + '</td><td class="text-right">' + fmt(grandNom) + '</td><td class="text-right pr-4">' + fmt(grandTrx) + '</td>';

              // Update Charts
              const labels = data.monthly.map(r => r.bulan.substring(0, 3));
              chartNomObj.updateOptions({ xaxis: { categories: labels } });
              chartNomObj.updateSeries([
                  { name: 'Mandiri', data: data.monthly.map(r => r.mandiri_nom) },
                  { name: 'Permata', data: data.monthly.map(r => r.permata_nom) }
              ]);

              chartTrxObj.updateOptions({ xaxis: { categories: labels } });
              chartTrxObj.updateSeries([
                  { name: 'Mandiri', data: data.monthly.map(r => r.mandiri_trx) },
                  { name: 'Permata', data: data.monthly.map(r => r.permata_trx) }
              ]);
          } else {
              document.getElementById('bodyMonthly').innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-400">Data tidak tersedia.</td></tr>';
              document.getElementById('footerMonthly').innerHTML = '';
          }
      } catch(e) {
          document.getElementById('bodyMonthly').innerHTML = '<tr><td colspan="8" class="text-center py-6 text-red-500">Gagal memuat data.</td></tr>';
      } finally {
          hideLoad('loadMonthly');
          hideLoad('loadChartNom');
          hideLoad('loadChartTrx');
      }
  }

  // ==========================================
  // UNIFIED BREAKDOWN TABLE (MoM + YoY)
  // ==========================================
  async function fetchUnifiedBreakdown() {
      showLoad('loadTable');
      const area = parseAreaValue();
      const basePl = { harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, channel: CHANNEL };
      try {
          const [resMom, resYoy] = await Promise.all([
              fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'detail_breakdown_transaksi', ...basePl}) }),
              fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'yoy_transaksi', ...basePl}) })
          ]);
          const jMom = await resMom.json();
          const jYoy = await resYoy.json();
          const tbody = document.getElementById('bodyUnified');
          tbody.innerHTML = '';

          // Remove any previous warning banner
          const prevWarning = tbody.closest('.overflow-x-auto').previousElementSibling;
          if (prevWarning && prevWarning.classList.contains('partial-fetch-warning')) prevWarning.remove();

          // Show warning if either data source failed
          let warningHtml = '';
          if (jMom.status !== 200) warningHtml += '<div class="bg-amber-50 border border-amber-200 text-amber-700 px-3 py-2 rounded text-xs font-bold mb-2">Data MoM tidak tersedia</div>';
          if (jYoy.status !== 200) warningHtml += '<div class="bg-amber-50 border border-amber-200 text-amber-700 px-3 py-2 rounded text-xs font-bold mb-2">Data YoY tidak tersedia</div>';
          if (warningHtml) {
              const warningContainer = document.createElement('div');
              warningContainer.className = 'px-4 pt-3 partial-fetch-warning';
              warningContainer.innerHTML = warningHtml;
              tbody.closest('.overflow-x-auto').insertAdjacentElement('beforebegin', warningContainer);
          }

          const optAreaVal = document.getElementById('opt_area').value;

          // Build merged data map by cabang kode
          const mergedMap = new Map();

          // Extract cabang from MoM data
          if (jMom.status === 200 && jMom.data && jMom.data.data) {
              const dt = jMom.data.data;
              if (optAreaVal === 'KONSOLIDASI') {
                  dt.forEach(kw => {
                      (kw.cabang || []).forEach(cb => {
                          mergedMap.set(cb.kode, { kode: cb.kode, nama: cb.nama, mom_curr_nom: cb.curr_nom || 0, mom_prev_nom: cb.prev_nom || 0, mom_growth: cb.growth_nom || 0, yoy_curr_nom: 0, yoy_prev_nom: 0, yoy_growth: 0 });
                      });
                  });
              } else if (optAreaVal.startsWith('KORWIL_')) {
                  dt.forEach(kw => {
                      (kw.cabang || []).forEach(cb => {
                          mergedMap.set(cb.kode, { kode: cb.kode, nama: cb.nama, mom_curr_nom: cb.curr_nom || 0, mom_prev_nom: cb.prev_nom || 0, mom_growth: cb.growth_nom || 0, yoy_curr_nom: 0, yoy_prev_nom: 0, yoy_growth: 0 });
                      });
                  });
              } else {
                  dt.forEach(kk => {
                      mergedMap.set(kk.kode || kk.nama, { kode: kk.kode || '', nama: kk.nama, mom_curr_nom: kk.curr_nom || 0, mom_prev_nom: kk.prev_nom || 0, mom_growth: kk.growth_nom || 0, yoy_curr_nom: 0, yoy_prev_nom: 0, yoy_growth: 0 });
                  });
              }
          }

          // Enrich with YoY data
          if (jYoy.status === 200 && jYoy.data && jYoy.data.data) {
              const dt = jYoy.data.data;
              if (optAreaVal === 'KONSOLIDASI') {
                  dt.forEach(kw => {
                      (kw.cabang || []).forEach(cb => {
                          if (mergedMap.has(cb.kode)) {
                              const existing = mergedMap.get(cb.kode);
                              existing.yoy_curr_nom = cb.curr_nom || 0;
                              existing.yoy_prev_nom = cb.prev_nom || 0;
                              existing.yoy_growth = cb.yoy_growth_nom || 0;
                          } else {
                              mergedMap.set(cb.kode, { kode: cb.kode, nama: cb.nama, mom_curr_nom: 0, mom_prev_nom: 0, mom_growth: 0, yoy_curr_nom: cb.curr_nom || 0, yoy_prev_nom: cb.prev_nom || 0, yoy_growth: cb.yoy_growth_nom || 0 });
                          }
                      });
                  });
              } else if (optAreaVal.startsWith('KORWIL_')) {
                  dt.forEach(kw => {
                      (kw.cabang || []).forEach(cb => {
                          if (mergedMap.has(cb.kode)) {
                              const existing = mergedMap.get(cb.kode);
                              existing.yoy_curr_nom = cb.curr_nom || 0;
                              existing.yoy_prev_nom = cb.prev_nom || 0;
                              existing.yoy_growth = cb.yoy_growth_nom || 0;
                          } else {
                              mergedMap.set(cb.kode, { kode: cb.kode, nama: cb.nama, mom_curr_nom: 0, mom_prev_nom: 0, mom_growth: 0, yoy_curr_nom: cb.curr_nom || 0, yoy_prev_nom: cb.prev_nom || 0, yoy_growth: cb.yoy_growth_nom || 0 });
                          }
                      });
                  });
              } else {
                  dt.forEach(kk => {
                      const key = kk.kode || kk.nama;
                      if (mergedMap.has(key)) {
                          const existing = mergedMap.get(key);
                          existing.yoy_curr_nom = kk.curr_nom || 0;
                          existing.yoy_prev_nom = kk.prev_nom || 0;
                          existing.yoy_growth = kk.yoy_growth_nom || 0;
                      } else {
                          mergedMap.set(key, { kode: kk.kode || '', nama: kk.nama, mom_curr_nom: 0, mom_prev_nom: 0, mom_growth: 0, yoy_curr_nom: kk.curr_nom || 0, yoy_prev_nom: kk.prev_nom || 0, yoy_growth: kk.yoy_growth_nom || 0 });
                      }
                  });
              }
          }

          if (mergedMap.size === 0) {
              tbody.innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-400">Data kosong.</td></tr>';
              return;
          }

          // Render Grand Total row
          const gtMom = (jMom.status === 200 && jMom.data) ? jMom.data.grand_total : null;
          const gtYoy = (jYoy.status === 200 && jYoy.data) ? jYoy.data.grand_total : null;
          const gtYoyPrev = gtYoy ? (gtYoy.prev_nom || 0) : 0;
          const gtYoyCurr = gtYoy ? (gtYoy.curr_nom || 0) : 0;
          const gtYoyGrowth = gtYoy ? (gtYoy.yoy_growth_nom || 0) : 0;
          const gtMomPrev = gtMom ? (gtMom.prev_nom || 0) : 0;
          const gtMomCurr = gtMom ? (gtMom.curr_nom || 0) : 0;
          const gtMomGrowth = gtMom ? (gtMom.growth_nom || 0) : 0;

          const growthBadge = (g) => {
              if (g > 0) return '<span class="text-emerald-600 font-bold">▲ ' + Math.abs(g) + '%</span>';
              if (g < 0) return '<span class="text-red-600 font-bold">▼ ' + Math.abs(g) + '%</span>';
              return '-';
          };

          tbody.innerHTML += '<tr class="bg-blue-50 font-bold"><td class="text-center pl-4">-</td><td class="font-black text-slate-900">GRAND TOTAL</td><td class="text-right">' + fmt(gtYoyPrev) + '</td><td class="text-right text-blue-700">' + fmt(gtYoyCurr) + '</td><td class="text-center text-[11px]">' + growthBadge(gtYoyGrowth) + '</td><td class="text-right">' + fmt(gtMomPrev) + '</td><td class="text-right text-blue-700">' + fmt(gtMomCurr) + '</td><td class="text-center text-[11px] pr-4">' + growthBadge(gtMomGrowth) + '</td></tr>';

          // Render data rows
          let idx = 1;
          mergedMap.forEach((row) => {
              tbody.innerHTML += '<tr><td class="text-center pl-4 text-slate-400">' + idx + '</td><td class="font-bold text-slate-700">' + esc(row.nama) + '</td><td class="text-right text-slate-500">' + fmt(row.yoy_prev_nom) + '</td><td class="text-right text-blue-700">' + fmt(row.yoy_curr_nom) + '</td><td class="text-center text-[11px]">' + growthBadge(row.yoy_growth) + '</td><td class="text-right text-slate-500">' + fmt(row.mom_prev_nom) + '</td><td class="text-right text-blue-700">' + fmt(row.mom_curr_nom) + '</td><td class="text-center text-[11px] pr-4">' + growthBadge(row.mom_growth) + '</td></tr>';
              idx++;
          });
      } catch(e) { console.error(e); document.getElementById('bodyUnified').innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-400">Gagal memuat data.</td></tr>'; } finally { hideLoad('loadTable'); }
  }
</script>

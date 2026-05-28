<!-- Load Library ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
  :root { --primary: #0284c7; --bg: #f8fafc; --text: #334155; }
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
  .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  th { background-color: #f8fafc; color: #1e293b; font-weight: 800; padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; font-size: 11px; }
  td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-weight: 700; color: #334155; }
  tr:hover td { background-color: #f0f9ff; }
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
                  <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h2v8H3zM9 9h2v12H9zM15 5h2v16h-2zM21 1h2v20h-2z"></path></svg>
              </span>
              Dashboard Layanan Digital
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
    <button onclick="switchView('summary')" id="tab-summary" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all bg-white shadow-sm text-blue-700" title="Summary">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      <span class="hidden md:inline">Summary</span>
    </button>
    <button onclick="switchView('ranking')" id="tab-ranking" class="flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700" title="Ranking">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 010-5H6M18 9h1.5a2.5 2.5 0 000-5H18M4 22h16M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22M18 2H6v7a6 6 0 1012 0V2z"/></svg>
      <span class="hidden md:inline">Ranking</span>
    </button>
  </div>

  <!-- ================= SUMMARY CARDS ================= -->
  <div id="section-summary">
  <div class="relative rounded-xl min-h-[100px]">
      <div id="loadSummary" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
      <div id="summaryCardsContainer" class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <!-- JS Inject -->
      </div>
  </div>
  </div><!-- end section-summary -->

  <!-- ================= CHANNEL SECTIONS ================= -->
  <div id="section-ranking" class="hidden space-y-3 md:space-y-4">

  <!-- VA Section -->
  <div class="bg-white rounded-2xl card-shadow border border-slate-100 p-4 md:p-6 flex flex-col gap-5">
      <h2 class="text-base font-black text-slate-800 border-b border-slate-100 pb-3">Top & Bottom 5 Cabang - Virtual Account (VA)</h2>
      <div class="relative min-h-[350px]">
          <div id="loadChartVA" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
          <div id="chartVA" class="w-full"></div>
      </div>
      <div class="overflow-x-auto custom-scrollbar">
          <table class="w-full text-left">
              <thead>
                  <tr>
                      <th class="w-[60px] pl-4">RANK</th>
                      <th class="w-[250px]">NAMA CABANG</th>
                      <th class="text-right">NOMINAL BULAN INI</th>
                      <th class="text-right">NOMINAL BULAN LALU</th>
                      <th class="text-center w-[100px] pr-4">GROWTH %</th>
                  </tr>
              </thead>
              <tbody id="tableVA" class="divide-y divide-slate-100"></tbody>
          </table>
      </div>
  </div>

  <!-- BRANCHLESS Section -->
  <div class="bg-white rounded-2xl card-shadow border border-slate-100 p-4 md:p-6 flex flex-col gap-5">
      <h2 class="text-base font-black text-slate-800 border-b border-slate-100 pb-3">Top & Bottom 5 Cabang - Branchless</h2>
      <div class="relative min-h-[350px]">
          <div id="loadChartBR" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
          <div id="chartBR" class="w-full"></div>
      </div>
      <div class="overflow-x-auto custom-scrollbar">
          <table class="w-full text-left">
              <thead>
                  <tr>
                      <th class="w-[60px] pl-4">RANK</th>
                      <th class="w-[250px]">NAMA CABANG</th>
                      <th class="text-right">NOMINAL BULAN INI</th>
                      <th class="text-right">NOMINAL BULAN LALU</th>
                      <th class="text-center w-[100px] pr-4">GROWTH %</th>
                  </tr>
              </thead>
              <tbody id="tableBR" class="divide-y divide-slate-100"></tbody>
          </table>
      </div>
  </div>

  <!-- QRIS Section -->
  <div class="bg-white rounded-2xl card-shadow border border-slate-100 p-4 md:p-6 flex flex-col gap-5">
      <h2 class="text-base font-black text-slate-800 border-b border-slate-100 pb-3">Top & Bottom 5 Cabang - QRIS Merchant</h2>
      <div class="relative min-h-[350px]">
          <div id="loadChartQRIS" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-blue-200 border-t-blue-600 rounded-full"></div></div>
          <div id="chartQRIS" class="w-full"></div>
      </div>
      <div class="overflow-x-auto custom-scrollbar">
          <table class="w-full text-left">
              <thead>
                  <tr>
                      <th class="w-[60px] pl-4">RANK</th>
                      <th class="w-[250px]">NAMA CABANG</th>
                      <th class="text-right">NOMINAL BULAN INI</th>
                      <th class="text-right">NOMINAL BULAN LALU</th>
                      <th class="text-center w-[100px] pr-4">GROWTH %</th>
                  </tr>
              </thead>
              <tbody id="tableQRIS" class="divide-y divide-slate-100"></tbody>
          </table>
      </div>
  </div>

  </div><!-- end section-ranking -->

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

  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Number(n||0));

  let chartVAObj = null;
  let chartBRObj = null;
  let chartQRISObj = null;

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
      document.getElementById('section-summary').classList.add('hidden');
      document.getElementById('section-ranking').classList.add('hidden');
      document.getElementById('section-' + view).classList.remove('hidden');
      ['summary','ranking'].forEach(t => {
          const btn = document.getElementById('tab-' + t);
          if (t === view) {
              btn.className = 'flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all bg-white shadow-sm text-blue-700';
          } else {
              btn.className = 'flex items-center gap-1.5 px-3 py-2 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700';
          }
      });
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
      const cards = document.getElementById('summaryCardsContainer');
      if (cards && cards.children.length > 0) {
          html += '<p class="font-bold text-gray-800">Ringkasan Layanan Digital:</p><ul class="list-disc list-inside space-y-1">';
          for (let i = 0; i < cards.children.length; i++) {
              const card = cards.children[i];
              const title = card.querySelector('.uppercase')?.textContent || '';
              const value = card.querySelector('.text-xl')?.textContent || '';
              const growth = card.querySelector('[data-narrative="growth"]')?.textContent || '';
              html += '<li><strong>' + escNarrative(title.trim()) + '</strong>: ' + escNarrative(value.trim()) + ' (Growth: ' + escNarrative(growth.trim()) + ')</li>';
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
        document.getElementById('opt_area').value = "KONSOLIDASI"; 
    } else {
        document.getElementById('opt_area').innerHTML = `<option value="${uKode}">CABANG ${uKode}</option>`;
        document.getElementById('opt_area').disabled = true; 
    }

    const dateData = await getLastHarianData();
    const today = new Date();
    
    // Default: harian = today, closing = last day of previous month
    document.getElementById('harian_date').value = today.toISOString().split('T')[0];
    const prevMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
    document.getElementById('closing_date').value = prevMonthEnd.getFullYear() + '-' + String(prevMonthEnd.getMonth()+1).padStart(2,'0') + '-' + String(prevMonthEnd.getDate()).padStart(2,'0');
    
    // Override with API data if available
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

  async function runFullSync() {
      fetchSummaryCards();
      fetchDashboardData();
  }

  // ==========================================
  // 1. SUMMARY CARDS
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
          
          if(j.status === 200 && j.data && j.data.cards) {
              if(j.data.meta) {
                  document.getElementById('lbl_periode_aktif').innerHTML = `Periode: <span class="text-blue-700 font-bold">${j.data.meta.closing_date} s/d ${j.data.meta.harian_date}</span>`;
              }
              
              const container = document.getElementById('summaryCardsContainer');
              container.innerHTML = '';
              
              // Only show VA, Branchless, QRIS total cards
              const filteredCards = j.data.cards.filter(c => {
                  if(!c || !c.title) return false;
                  const t = String(c.title).toUpperCase();
                  return t.includes('TOTAL VIRTUAL') || t.includes('TOTAL BRANCHLESS') || t.includes('TOTAL QRIS');
              });

              filteredCards.forEach((c) => {
                  const growthVal = parseFloat(c.growth || 0);
                  const isUp = growthVal >= 0;
                  const bColor = isUp ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700';
                  const arrow = isUp ? '&#9650;' : '&#9660;';
                  
                  const cTitleStr = String(c.title).toUpperCase();
                  let bTop = 'border-t-4 border-t-slate-200';
                  if (cTitleStr.includes('VIRTUAL')) bTop = 'border-t-4 border-t-blue-600';
                  else if (cTitleStr.includes('BRANCHLESS')) bTop = 'border-t-4 border-t-emerald-500';
                  else if (cTitleStr.includes('QRIS')) bTop = 'border-t-4 border-t-amber-500';

                  const pLabel = c.prev_label || 'Bulan Lalu';
                  const pNominal = c.prev_nominal || 'Rp -';

                  container.innerHTML += `
                      <div class="bg-white rounded-xl card-shadow p-4 flex flex-col justify-between ${bTop}">
                          <div>
                              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">${c.title}</p>
                              <h3 class="text-xl font-black text-slate-800 leading-tight">${c.value || 'Rp 0'}</h3>
                              <p class="text-[10px] font-bold text-slate-500 mt-0.5">${c.subtitle || ''}</p>
                          </div>
                          <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2">
                              <span class="${bColor} px-2 py-0.5 rounded font-bold text-[11px]" data-narrative="growth">${arrow} ${Math.abs(growthVal)}%</span>
                              <div class="text-right leading-tight">
                                  <span class="text-[9px] text-slate-400">${pLabel}</span><br>
                                  <span class="text-[10px] font-bold text-slate-600">${pNominal}</span>
                              </div>
                          </div>
                      </div>`;
              });
          } else {
              document.getElementById('lbl_periode_aktif').innerHTML = `<span class="text-red-500 font-bold ml-1">Gagal Muat: ${j.message || 'Error'}</span>`;
          }
      } catch (e) {
          document.getElementById('lbl_periode_aktif').innerHTML = `<span class="text-red-500 font-bold ml-1">Koneksi Terputus: ${e.message}</span>`;
      } finally { 
          hideLoad('loadSummary'); 
      }
  }

  // ==========================================
  // 2. DASHBOARD TOP/BOTTOM CHARTS & TABLES
  // ==========================================
  function initCharts() {
      const chartConfig = (elId) => ({
          series: [],
          chart: { type: 'bar', height: 320, toolbar: { show: false } },
          plotOptions: { bar: { horizontal: true, barHeight: '60%', borderRadius: 4, dataLabels: { position: 'right' } } },
          colors: ['#10b981', '#f43f5e'],
          dataLabels: { enabled: false },
          xaxis: { categories: [], labels: { formatter: (val) => val >= 1000000000 ? (val/1000000000).toFixed(1)+' M' : (val >= 1000000 ? (val/1000000).toFixed(0)+' Jt' : nf.format(val)), style: { fontSize: '10px' } } },
          yaxis: { labels: { style: { fontSize: '11px', fontWeight: 700 } } },
          legend: { position: 'top', fontSize: '12px', fontWeight: 700 },
          tooltip: { y: { formatter: (val) => 'Rp ' + nf.format(val) } },
          grid: { xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } }
      });

      chartVAObj = new ApexCharts(document.querySelector("#chartVA"), chartConfig('chartVA'));
      chartVAObj.render();

      chartBRObj = new ApexCharts(document.querySelector("#chartBR"), chartConfig('chartBR'));
      chartBRObj.render();

      chartQRISObj = new ApexCharts(document.querySelector("#chartQRIS"), chartConfig('chartQRIS'));
      chartQRISObj.render();
  }

  async function fetchDashboardData() {
      showLoad('loadChartVA'); showLoad('loadChartBR'); showLoad('loadChartQRIS');
      const area = parseAreaValue();
      const payload = { 
          type: "dashboard_layanan_digital",
          harian_date: document.getElementById('harian_date').value,
          closing_date: document.getElementById('closing_date').value,
          kode_kantor: area.kode_kantor,
          korwil: area.korwil
      };

      try {
          const res = await fetch(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          const j = await res.json();

          if(j.status === 200 && j.data && j.data.channels) {
              renderChannel('VA', j.data.channels.VA, chartVAObj, 'tableVA');
              renderChannel('BRANCHLESS', j.data.channels.BRANCHLESS, chartBRObj, 'tableBR');
              renderChannel('QRIS', j.data.channels.QRIS, chartQRISObj, 'tableQRIS');
          } else {
              ['tableVA','tableBR','tableQRIS'].forEach(id => {
                  document.getElementById(id).innerHTML = `<tr><td colspan="5" class="text-center py-6 text-red-500">Gagal memuat data.</td></tr>`;
              });
          }
      } catch(e) {
          ['tableVA','tableBR','tableQRIS'].forEach(id => {
              document.getElementById(id).innerHTML = `<tr><td colspan="5" class="text-center py-6 text-red-500">Koneksi terputus.</td></tr>`;
          });
      } finally {
          hideLoad('loadChartVA'); hideLoad('loadChartBR'); hideLoad('loadChartQRIS');
      }
  }

  function renderChannel(chName, chData, chartObj, tableId) {
      if(!chData) return;
      const top5 = chData.top5 || [];
      const bottom5 = chData.bottom5 || [];

      // Prepare chart data: top5 green, bottom5 red
      const labels = [];
      const topValues = [];
      const bottomValues = [];

      top5.forEach(item => {
          labels.push(item.nama);
          topValues.push(item.curr_nom);
          bottomValues.push(0);
      });

      bottom5.forEach(item => {
          labels.push(item.nama);
          topValues.push(0);
          bottomValues.push(item.curr_nom);
      });

      chartObj.updateOptions({ xaxis: { categories: labels } });
      chartObj.updateSeries([
          { name: 'Top 5', data: topValues },
          { name: 'Bottom 5', data: bottomValues }
      ]);

      // Render table
      const tbody = document.getElementById(tableId);
      tbody.innerHTML = '';

      if(top5.length === 0 && bottom5.length === 0) {
          tbody.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-slate-400">Data kosong.</td></tr>`;
          return;
      }

      let rank = 1;
      top5.forEach(item => {
          const gVal = parseFloat(item.growth || 0);
          const gColor = gVal >= 0 ? 'text-emerald-600' : 'text-red-600';
          const gArrow = gVal >= 0 ? '&#9650;' : '&#9660;';
          tbody.innerHTML += `<tr class="bg-emerald-50/50">
              <td class="pl-4 text-emerald-700 font-black">${rank}</td>
              <td class="font-bold text-slate-700">${item.nama}</td>
              <td class="text-right text-blue-700 font-bold">${fmt(item.curr_nom)}</td>
              <td class="text-right text-slate-400">${fmt(item.prev_nom)}</td>
              <td class="text-center pr-4"><span class="${gColor} font-bold text-[11px]">${gArrow} ${Math.abs(gVal)}%</span></td>
          </tr>`;
          rank++;
      });

      rank = 1;
      bottom5.forEach(item => {
          const gVal = parseFloat(item.growth || 0);
          const gColor = gVal >= 0 ? 'text-emerald-600' : 'text-red-600';
          const gArrow = gVal >= 0 ? '&#9650;' : '&#9660;';
          tbody.innerHTML += `<tr class="bg-rose-50/50">
              <td class="pl-4 text-rose-700 font-black">B${rank}</td>
              <td class="font-bold text-slate-700">${item.nama}</td>
              <td class="text-right text-blue-700 font-bold">${fmt(item.curr_nom)}</td>
              <td class="text-right text-slate-400">${fmt(item.prev_nom)}</td>
              <td class="text-center pr-4"><span class="${gColor} font-bold text-[11px]">${gArrow} ${Math.abs(gVal)}%</span></td>
          </tr>`;
          rank++;
      });
  }
</script>

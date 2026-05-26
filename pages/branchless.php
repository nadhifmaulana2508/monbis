<!-- Load Library ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
  :root { --primary: #059669; --bg: #f8fafc; --text: #334155; }
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
  .inp { box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0 0.5rem; font-size: 13px; background: #fff; height: 42px; cursor: pointer; outline: none; transition: border 0.2s; font-weight: 600; }
  .inp:focus { border-color: var(--primary); box-shadow: 0 0 0 2px #a7f3d0; }
  .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  th { background-color: #f8fafc; color: #1e293b; font-weight: 800; padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; font-size: 11px; }
  td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-weight: 700; color: #334155; }
  tr:hover td { background-color: #ecfdf5; }
  .card-shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
  .local-loader { position: absolute; inset: 0; background: rgba(255,255,255,0.7); z-index: 50; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(2px); border-radius: inherit; }
  .local-loader.hidden { display: none; }
  .apexcharts-tooltip { z-index: 99999 !important; background: transparent !important; border: none !important; box-shadow: none !important; }
  @media (max-width: 767px) {
    th { padding: 8px 6px; font-size: 10px; }
    td { padding: 8px 6px; font-size: 11px; }
  }
</style>

<div class="max-w-[1600px] mx-auto px-3 md:px-6 py-4 flex flex-col gap-5">
  <!-- HEADER & GLOBAL FILTER -->
  <div class="flex flex-col gap-4 bg-white p-4 md:p-5 rounded-xl card-shadow border border-slate-100">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold flex items-center gap-2 text-slate-800">
                <span class="bg-emerald-600 text-white p-1.5 rounded-lg text-sm shadow-sm">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </span>
                <span>Branchless Banking</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1 ml-1 font-medium" id="lbl_periode_aktif">Menunggu data sinkronisasi...</p>
        </div>
        <!-- Mobile Filter Toggle Button -->
        <button type="button" id="btnFilterToggle" onclick="toggleFilter()" class="md:hidden flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 px-4 py-2 rounded-lg font-bold text-sm">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            <span id="filterToggleLabel">Tampilkan Filter</span>
        </button>
    </div>
    <!-- Filter Form - hidden on mobile by default, always visible on md+ -->
    <div id="filterContainer" class="hidden md:block">
        <form id="formFilterGlobal" class="flex flex-col md:flex-row items-end gap-3 w-full">
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
                <select id="opt_area" class="inp text-emerald-700 shadow-sm">
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
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white h-[42px] px-6 rounded-lg font-bold text-sm shadow-md flex items-center justify-center transition w-full md:w-auto">Tampilkan</button>
        </form>
    </div>
  </div>

  <!-- SUMMARY CARDS -->
  <div class="relative rounded-xl min-h-[100px]">
      <div id="loadSummary" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <div id="summaryCardsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
  </div>

  <!-- TREND CHART -->
  <div class="bg-white rounded-xl border border-slate-200 p-4 md:p-5 card-shadow flex flex-col relative" style="min-height:430px;">
      <div id="loadTrend" class="local-loader hidden rounded-xl"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <div class="flex justify-between items-center mb-2 border-b border-slate-100 pb-2">
          <h2 class="font-bold text-slate-800">Tren Transaksi Branchless</h2>
          <select id="trendPeriode" class="inp h-8 text-[11px] w-[140px]" onchange="fetchTrend()">
              <option value="bulanan">6 Bulan Terakhir</option>
              <option value="7_hari">7 Hari Terakhir</option>
              <option value="30_hari">30 Hari Terakhir</option>
              <option value="tahunan">Tahunan</option>
          </select>
      </div>
      <div id="chartTrend" class="w-full mt-2"></div>
  </div>

  <!-- DISTRIBUTION -->
  <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
      <div class="xl:col-span-7 bg-white rounded-xl border border-slate-200 p-4 md:p-5 card-shadow flex flex-col relative" style="min-height:380px;">
          <div id="loadDist" class="local-loader hidden rounded-xl"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
          <h2 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Top 5 Distribusi Branchless</h2>
          <div class="flex-1 flex flex-col md:flex-row gap-4">
              <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 flex flex-col gap-4" id="listTop5"></div>
              <div class="w-full md:w-[220px] flex items-center justify-center shrink-0">
                  <div id="chartDonut" class="w-full"></div>
              </div>
          </div>
      </div>
      <div class="xl:col-span-5"></div>
  </div>

  <!-- UNIFIED BREAKDOWN TABLE -->
  <div class="bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden card-shadow relative min-h-[200px]">
      <div id="loadTable" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
          <h2 class="text-base font-black text-slate-800">Breakdown Transaksi Branchless - MoM & YoY</h2>
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
</div>

<script>
  const API_URL = './api/transaksi/';
  const API_KODE = './api/kode/';
  const API_DATE = './api/date/';
  const CHANNEL = 'BRANCHLESS';

  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Number(n||0));
  const esc = (s) => String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

  let chartTrendObj = null;
  let chartDonutObj = null;

  const showLoad = (id) => document.getElementById(id)?.classList.remove('hidden');
  const hideLoad = (id) => document.getElementById(id)?.classList.add('hidden');

  function toggleFilter() {
      const container = document.getElementById('filterContainer');
      const label = document.getElementById('filterToggleLabel');
      if (container.classList.contains('hidden')) {
          container.classList.remove('hidden');
          label.textContent = 'Sembunyikan Filter';
      } else {
          container.classList.add('hidden');
          label.textContent = 'Tampilkan Filter';
      }
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

  async function runFullSync() { fetchSummaryCards(); fetchTrend(); fetchDistribusi(); fetchUnifiedBreakdown(); }

  function initCharts() {
      chartTrendObj = new ApexCharts(document.querySelector('#chartTrend'), {
          series: [], chart: { type: 'area', height: 340, toolbar: { show: false } },
          colors: ['#10b981'], dataLabels: { enabled: false }, legend: { show: false },
          stroke: { curve: 'smooth', width: 3 },
          fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 100] } },
          grid: { padding: { bottom: 15, left: 10, right: 10 } },
          xaxis: { categories: [], labels: { style: { fontSize: '10px' } } },
          yaxis: { labels: { formatter: (val) => val >= 1000000000 ? (val/1000000000).toFixed(1)+' M' : (val >= 1000000 ? (val/1000000).toFixed(0)+' Jt' : nf.format(val)) } },
          tooltip: { y: { formatter: (val) => 'Rp ' + nf.format(val) } }
      });
      chartTrendObj.render();

      chartDonutObj = new ApexCharts(document.querySelector('#chartDonut'), {
          series: [], chart: { type: 'donut', height: 280 }, labels: [],
          colors: ['#8b5cf6','#0ea5e9','#10b981','#f59e0b','#f43f5e','#64748b'],
          plotOptions: { donut: { size: '70%' } }, dataLabels: { enabled: false },
          legend: { show: true, position: 'bottom', fontSize: '9.5px' }
      });
      chartDonutObj.render();
  }

  async function fetchSummaryCards() {
      showLoad('loadSummary');
      const area = parseAreaValue();
      const payload = { type: 'summary_cards_transaksi', harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil };
      try {
          const res = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const j = await res.json();
          if(j.status===200 && j.data && j.data.cards) {
              if(j.data.meta) { document.getElementById('lbl_periode_aktif').innerHTML = 'Periode: <span class="text-emerald-700 font-bold">' + j.data.meta.closing_date + ' s/d ' + j.data.meta.harian_date + '</span>'; }
              const container = document.getElementById('summaryCardsContainer');
              container.innerHTML = '';
              const cards = j.data.cards.filter(c => c && c.title && String(c.title).toUpperCase().includes('BRANCHLESS'));
              cards.forEach(c => {
                  const gv = parseFloat(c.growth||0); const isUp = gv >= 0;
                  const bColor = isUp ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700';
                  const arrow = isUp ? '▲' : '▼';
                  container.innerHTML += '<div class="bg-white rounded-xl card-shadow p-5 flex flex-col justify-between border-l-4 border-l-emerald-600"><div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">' + esc(c.title) + '</p><h3 class="text-xl font-black text-slate-800 leading-tight">' + esc(c.value||'Rp 0') + '</h3><p class="text-[10px] font-bold text-slate-500 mt-1">' + esc(c.subtitle||'') + '</p></div><div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3"><span class="' + bColor + ' px-2 py-0.5 rounded font-bold text-[11px]">' + arrow + ' ' + Math.abs(gv) + '%</span><div class="text-right leading-tight"><span class="text-[9px] text-slate-400">' + esc(c.prev_label||'Bulan Lalu') + '</span><br><span class="text-[10px] font-bold text-slate-600">' + esc(c.prev_nominal||'Rp -') + '</span></div></div></div>';
              });
          }
      } catch(e){} finally { hideLoad('loadSummary'); }
  }

  async function fetchTrend() {
      showLoad('loadTrend');
      const area = parseAreaValue();
      const payload = { type: 'tren_nominal_va', harian_date: document.getElementById('harian_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, periode: document.getElementById('trendPeriode').value, channel: CHANNEL };
      try {
          const r = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const j = await r.json();
          if(j.status===200 && j.data && j.data.chart_nominal) {
              chartTrendObj.updateOptions({ xaxis: { categories: j.data.chart_nominal.labels } });
              chartTrendObj.updateSeries(j.data.chart_nominal.series);
          }
      } catch(e){} finally { hideLoad('loadTrend'); }
  }

  async function fetchDistribusi() {
      showLoad('loadDist');
      const area = parseAreaValue();
      const payload = { type: 'distribusi_va', harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, channel: CHANNEL };
      try {
          const r = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const j = await r.json();
          if(j.status===200 && j.data) {
              const d = j.data;
              chartDonutObj.updateOptions({ labels: d.donut_chart.labels });
              chartDonutObj.updateSeries(d.donut_chart.series);
              const listC = document.getElementById('listTop5'); listC.innerHTML = '';
              const colors = ['bg-violet-500','bg-sky-500','bg-emerald-500','bg-amber-500','bg-rose-500'];
              if(!d.top_5 || d.top_5.length===0) { listC.innerHTML = '<p class="text-xs text-slate-400">Data kosong</p>'; return; }
              const maxNom = d.top_5[0].nominal;
              d.top_5.forEach((t,i) => {
                  const wPct = (t.nominal/maxNom)*100;
                  let fNom = t.nominal >= 1000000000 ? (t.nominal/1000000000).toFixed(2)+' M' : (t.nominal >= 1000000 ? (t.nominal/1000000).toFixed(1)+' jt' : nf.format(t.nominal));
                  listC.innerHTML += '<div class="flex flex-col text-xs"><div class="flex justify-between items-end mb-1"><div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full ' + colors[i%colors.length] + ' text-white flex items-center justify-center text-[9px] font-bold">' + (i+1) + '</span><span class="font-bold text-slate-700">' + esc(t.label) + '</span></div><div class="text-right leading-none"><span class="font-black text-slate-800">Rp ' + fNom + '</span><br><span class="text-[9px] text-slate-400">' + nf.format(t.trx) + ' Trx</span></div></div><div class="w-full bg-slate-100 rounded-full h-1.5"><div class="' + colors[i%colors.length] + ' h-1.5 rounded-full" style="width:' + wPct + '%"></div></div></div>';
              });
          }
      } catch(e){} finally { hideLoad('loadDist'); }
  }

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
                  // Specific cabang - show kankas breakdown
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

          tbody.innerHTML += '<tr class="bg-emerald-50 font-bold"><td class="text-center pl-4">-</td><td class="font-black text-slate-900">GRAND TOTAL</td><td class="text-right">' + fmt(gtYoyPrev) + '</td><td class="text-right text-emerald-700">' + fmt(gtYoyCurr) + '</td><td class="text-center text-[11px]">' + growthBadge(gtYoyGrowth) + '</td><td class="text-right">' + fmt(gtMomPrev) + '</td><td class="text-right text-emerald-700">' + fmt(gtMomCurr) + '</td><td class="text-center text-[11px] pr-4">' + growthBadge(gtMomGrowth) + '</td></tr>';

          // Render data rows
          let idx = 1;
          mergedMap.forEach((row) => {
              tbody.innerHTML += '<tr><td class="text-center pl-4 text-slate-400">' + idx + '</td><td class="font-bold text-slate-700">' + esc(row.nama) + '</td><td class="text-right text-slate-500">' + fmt(row.yoy_prev_nom) + '</td><td class="text-right text-blue-700">' + fmt(row.yoy_curr_nom) + '</td><td class="text-center text-[11px]">' + growthBadge(row.yoy_growth) + '</td><td class="text-right text-slate-500">' + fmt(row.mom_prev_nom) + '</td><td class="text-right text-blue-700">' + fmt(row.mom_curr_nom) + '</td><td class="text-center text-[11px] pr-4">' + growthBadge(row.mom_growth) + '</td></tr>';
              idx++;
          });
      } catch(e){ console.error(e); document.getElementById('bodyUnified').innerHTML = '<tr><td colspan="8" class="text-center py-6 text-slate-400">Gagal memuat data.</td></tr>'; } finally { hideLoad('loadTable'); }
  }
</script>

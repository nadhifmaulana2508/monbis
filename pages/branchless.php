<!-- Load Library ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
  :root { --primary: #0284c7; --bg: #f8fafc; --text: #334155; }
  body { font-family: 'Inter', system-ui, sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
  .inp { box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0 0.5rem; font-size: 13px; background: #fff; height: 42px; cursor: pointer; outline: none; transition: border 0.2s; font-weight: 600; }
  .inp:focus { border-color: var(--primary); box-shadow: 0 0 0 2px #bae6fd; }
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
</style>

<div class="max-w-[1600px] mx-auto px-3 md:px-4 py-4 flex flex-col gap-5">
  <!-- HEADER & GLOBAL FILTER -->
  <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 bg-white p-4 rounded-xl card-shadow border border-slate-100">
    <div>
        <h1 class="text-xl md:text-2xl font-bold flex items-center gap-2 text-slate-800">
            <span class="bg-emerald-600 text-white p-1.5 rounded-lg text-sm shadow-sm">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </span>
            <span>Branchless Banking</span>
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
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white h-[42px] px-6 rounded-lg font-bold text-sm shadow-md flex items-center justify-center transition w-full md:w-auto">Tampilkan</button>
    </form>
  </div>

  <!-- SUMMARY CARDS -->
  <div class="relative rounded-xl min-h-[100px]">
      <div id="loadSummary" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <div id="summaryCardsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3"></div>
  </div>

  <!-- TREND CHART -->
  <div class="bg-white rounded-xl border border-slate-200 p-5 card-shadow flex flex-col relative" style="min-height:430px;">
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
      <div class="xl:col-span-7 bg-white rounded-xl border border-slate-200 p-5 card-shadow flex flex-col relative" style="min-height:380px;">
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

  <!-- BREAKDOWN TABLE -->
  <div class="bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden card-shadow relative min-h-[200px]">
      <div id="loadTable" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
          <h2 class="text-base font-black text-slate-800">Breakdown Transaksi Branchless per Area</h2>
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

  <!-- YOY -->
  <div class="bg-white rounded-xl border border-slate-200 flex flex-col overflow-hidden card-shadow relative min-h-[200px]">
      <div id="loadYoy" class="local-loader hidden"><div class="animate-spin h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full"></div></div>
      <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
          <h2 class="text-base font-black text-slate-800">Perbandingan Year-over-Year (YOY)</h2>
          <span class="text-[10px] font-bold text-slate-400" id="lblYoyPeriode"></span>
      </div>
      <div class="p-4"><div id="chartYoy" class="w-full mb-4" style="min-height:300px;"></div></div>
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
</div>

<script>
  const API_URL = './api/transaksi/';
  const API_KODE = './api/kode/';
  const API_DATE = './api/date/';
  const CHANNEL = 'BRANCHLESS';

  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Number(n||0));

  let chartTrendObj = null;
  let chartDonutObj = null;
  let chartYoyObj = null;

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
        document.getElementById('opt_area').value = 'KONSOLIDASI';
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
      let kode_kantor = ''; let korwil = '';
      if (val.startsWith('KORWIL_')) { korwil = val.replace('KORWIL_', ''); }
      else if (val !== 'KONSOLIDASI') { kode_kantor = val; }
      return { kode_kantor, korwil };
  }

  document.getElementById('formFilterGlobal').addEventListener('submit', e => { e.preventDefault(); runFullSync(); });

  async function runFullSync() { fetchSummaryCards(); fetchTrend(); fetchDistribusi(); fetchBreakdown(); fetchYoy(); }

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

      chartYoyObj = new ApexCharts(document.querySelector('#chartYoy'), {
          series: [], chart: { type: 'bar', height: 300, toolbar: { show: false } },
          colors: ['#10b981','#94a3b8'],
          plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
          dataLabels: { enabled: false }, stroke: { show: true, width: 2, colors: ['transparent'] },
          xaxis: { categories: [], labels: { style: { fontSize: '10px', fontWeight: 700 } } },
          yaxis: { labels: { formatter: (val) => val >= 1000000000 ? (val/1000000000).toFixed(1)+' M' : (val >= 1000000 ? (val/1000000).toFixed(0)+' Jt' : nf.format(val)) } },
          fill: { opacity: 1 }, legend: { position: 'top', fontSize: '12px', fontWeight: 700 },
          tooltip: { y: { formatter: (val) => 'Rp ' + nf.format(val) } }
      });
      chartYoyObj.render();
  }

  async function fetchSummaryCards() {
      showLoad('loadSummary');
      const area = parseAreaValue();
      const payload = { type: 'summary_cards_transaksi', harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil };
      try {
          const res = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const j = await res.json();
          if(j.status===200 && j.data && j.data.cards) {
              if(j.data.meta) { document.getElementById('lbl_periode_aktif').innerHTML = `Periode: <span class="text-emerald-700 font-bold">${j.data.meta.closing_date} s/d ${j.data.meta.harian_date}</span>`; }
              const container = document.getElementById('summaryCardsContainer');
              container.innerHTML = '';
              const cards = j.data.cards.filter(c => c && c.title && String(c.title).toUpperCase().includes('BRANCHLESS'));
              cards.forEach(c => {
                  const gv = parseFloat(c.growth||0); const isUp = gv >= 0;
                  const bColor = isUp ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700';
                  const arrow = isUp ? '▲' : '▼';
                  container.innerHTML += `<div class="bg-white rounded-xl card-shadow p-3.5 flex flex-col justify-between border-t-4 border-t-emerald-600"><div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">${c.title}</p><h3 class="text-xl font-black text-slate-800 leading-tight">${c.value||'Rp 0'}</h3><p class="text-[10px] font-bold text-slate-500 mt-0.5">${c.subtitle||''}</p></div><div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2"><span class="${bColor} px-2 py-0.5 rounded font-bold text-[11px]">${arrow} ${Math.abs(gv)}%</span><div class="text-right leading-tight"><span class="text-[9px] text-slate-400">${c.prev_label||'Bulan Lalu'}</span><br><span class="text-[10px] font-bold text-slate-600">${c.prev_nominal||'Rp -'}</span></div></div></div>`;
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
                  listC.innerHTML += `<div class="flex flex-col text-xs"><div class="flex justify-between items-end mb-1"><div class="flex items-center gap-2"><span class="w-4 h-4 rounded-full ${colors[i%colors.length]} text-white flex items-center justify-center text-[9px] font-bold">${i+1}</span><span class="font-bold text-slate-700">${t.label}</span></div><div class="text-right leading-none"><span class="font-black text-slate-800">Rp ${fNom}</span><br><span class="text-[9px] text-slate-400">${nf.format(t.trx)} Trx</span></div></div><div class="w-full bg-slate-100 rounded-full h-1.5"><div class="${colors[i%colors.length]} h-1.5 rounded-full" style="width:${wPct}%"></div></div></div>`;
              });
          }
      } catch(e){} finally { hideLoad('loadDist'); }
  }

  async function fetchBreakdown() {
      showLoad('loadTable');
      const area = parseAreaValue();
      const payload = { type: 'detail_breakdown_transaksi', harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, channel: CHANNEL };
      try {
          const res = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const j = await res.json();
          const tbody = document.getElementById('bodyBreakdown'); tbody.innerHTML = '';
          if(j.status!==200 || !j.data || !j.data.data.length) { tbody.innerHTML = '<tr><td colspan="7" class="text-center py-6">Data kosong.</td></tr>'; return; }
          const rHtml = (nama, cN, pN, gN, cT, pT, gT, isBold, isChild) => {
              const bg = isBold ? 'bg-slate-50 font-bold' : (isChild ? 'text-slate-600 bg-slate-50/20' : 'font-bold text-slate-700');
              const pad = isChild ? 'pl-10' : 'pl-4';
              const c_gN = gN > 0 ? `<span class="text-emerald-600">▲ ${gN}%</span>` : (gN < 0 ? `<span class="text-red-600">▼ ${Math.abs(gN)}%</span>` : '-');
              const c_gT = gT > 0 ? `<span class="text-emerald-600">▲ ${gT}%</span>` : (gT < 0 ? `<span class="text-red-600">▼ ${Math.abs(gT)}%</span>` : '-');
              return `<tr class="${bg}"><td class="${pad}">${nama}</td><td class="text-right text-blue-700">${fmt(cN)}</td><td class="text-right text-[10px] text-slate-400">${fmt(pN)}</td><td class="text-center text-[11px] font-bold bg-slate-50/50">${c_gN}</td><td class="text-right text-indigo-700">${fmt(cT)}</td><td class="text-right text-[10px] text-slate-400">${fmt(pT)}</td><td class="text-center text-[11px] font-bold bg-slate-50/50 pr-4">${c_gT}</td></tr>`;
          };
          const gt = j.data.grand_total;
          tbody.innerHTML += rHtml('GRAND TOTAL', gt.curr_nom, gt.prev_nom, gt.growth_nom, gt.curr_trx, gt.prev_trx, gt.growth_trx, true, false);
          const dt = j.data.data;
          const optAreaVal = document.getElementById('opt_area').value;
          if (optAreaVal === 'KONSOLIDASI') { dt.forEach(kw => { tbody.innerHTML += rHtml(kw.korwil, kw.curr_nom, kw.prev_nom, kw.growth_nom, kw.curr_trx, kw.prev_trx, kw.growth_trx, false, false); }); }
          else if (optAreaVal.startsWith('KORWIL_')) { dt.forEach(kw => { (kw.cabang||[]).forEach(cb => { tbody.innerHTML += rHtml(cb.nama, cb.curr_nom, cb.prev_nom, cb.growth_nom, cb.curr_trx, cb.prev_trx, cb.growth_trx, false, true); }); }); }
          else { dt.forEach(kk => { tbody.innerHTML += rHtml(kk.nama, kk.curr_nom, kk.prev_nom, kk.growth_nom, kk.curr_trx, kk.prev_trx, kk.growth_trx, false, false); }); }
      } catch(e){} finally { hideLoad('loadTable'); }
  }

  async function fetchYoy() {
      showLoad('loadYoy');
      const area = parseAreaValue();
      const payload = { type: 'yoy_transaksi', harian_date: document.getElementById('harian_date').value, closing_date: document.getElementById('closing_date').value, kode_kantor: area.kode_kantor, korwil: area.korwil, channel: CHANNEL };
      try {
          const res = await fetch(API_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
          const j = await res.json();
          const tbody = document.getElementById('bodyYoy'); tbody.innerHTML = '';
          if(j.status!==200 || !j.data || !j.data.data.length) { tbody.innerHTML = '<tr><td colspan="7" class="text-center py-6">Data kosong.</td></tr>'; chartYoyObj.updateSeries([]); return; }
          if(j.data.meta) { document.getElementById('lblYoyPeriode').innerHTML = `Periode: ${j.data.meta.periode_curr.start} s/d ${j.data.meta.periode_curr.end} vs ${j.data.meta.periode_prev.start} s/d ${j.data.meta.periode_prev.end}`; }
          if(j.data.chart) { chartYoyObj.updateOptions({ xaxis: { categories: j.data.chart.labels } }); chartYoyObj.updateSeries(j.data.chart.series); }
          const rYoy = (nama, cN, pN, gN, cT, pT, gT, isBold, isChild) => {
              const bg = isBold ? 'bg-slate-50 font-bold' : (isChild ? 'text-slate-600 bg-slate-50/20' : 'font-bold text-slate-700');
              const pad = isChild ? 'pl-10' : 'pl-4';
              const c_gN = gN > 0 ? `<span class="text-emerald-600">▲ ${gN}%</span>` : (gN < 0 ? `<span class="text-red-600">▼ ${Math.abs(gN)}%</span>` : '-');
              const c_gT = gT > 0 ? `<span class="text-emerald-600">▲ ${gT}%</span>` : (gT < 0 ? `<span class="text-red-600">▼ ${Math.abs(gT)}%</span>` : '-');
              return `<tr class="${bg}"><td class="${pad}">${nama}</td><td class="text-right text-blue-700">${fmt(cN)}</td><td class="text-right text-slate-400">${fmt(pN)}</td><td class="text-center text-[11px] font-bold bg-slate-50/50">${c_gN}</td><td class="text-right text-indigo-700">${fmt(cT)}</td><td class="text-right text-slate-400">${fmt(pT)}</td><td class="text-center text-[11px] font-bold bg-slate-50/50 pr-4">${c_gT}</td></tr>`;
          };
          const gt = j.data.grand_total;
          tbody.innerHTML += rYoy('GRAND TOTAL', gt.curr_nom, gt.prev_nom, gt.yoy_growth_nom, gt.curr_trx, gt.prev_trx, gt.yoy_growth_trx, true, false);
          const dt = j.data.data;
          const optAreaVal = document.getElementById('opt_area').value;
          if (optAreaVal === 'KONSOLIDASI') { dt.forEach(kw => { tbody.innerHTML += rYoy(kw.korwil, kw.curr_nom, kw.prev_nom, kw.yoy_growth_nom, kw.curr_trx, kw.prev_trx, kw.yoy_growth_trx, false, false); }); }
          else if (optAreaVal.startsWith('KORWIL_')) { dt.forEach(kw => { (kw.cabang||[]).forEach(cb => { tbody.innerHTML += rYoy(cb.nama, cb.curr_nom, cb.prev_nom, cb.yoy_growth_nom, cb.curr_trx, cb.prev_trx, cb.yoy_growth_trx, false, true); }); }); }
          else { dt.forEach(kk => { tbody.innerHTML += rYoy(kk.nama, kk.curr_nom, kk.prev_nom, kk.yoy_growth_nom, kk.curr_trx, kk.prev_trx, kk.yoy_growth_trx, false, false); }); }
      } catch(e){} finally { hideLoad('loadYoy'); }
  }
</script>

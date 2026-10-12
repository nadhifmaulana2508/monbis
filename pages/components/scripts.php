<script>
  // --- PARSING ENGINE UTAMA ---
  const nf = new Intl.NumberFormat('id-ID');
  const fmt = n => nf.format(Number(n || 0));
  const fmtB = n => {
    let num = Number(n||0); let absNum = Math.abs(num);
    if(absNum >= 1e12) return (num/1e12).toFixed(3) + ' T'; 
    if(absNum >= 1e9) return (num/1e9).toFixed(2) + ' M';   
    if(absNum >= 1e6) return (num/1e6).toFixed(1) + ' Jt';  
    return fmt(num);
  };
  const pct = x => (x == null ? '0%' : `${(+x).toFixed(2)}%`);

  function getTodayRealtime() {
    let d = new Date();
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }
  function getYesterdayRealtime() {
    let d = new Date(); d.setDate(d.getDate() - 1);
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }
  function getH1Date(dateString) {
    let d = new Date(dateString); d.setDate(d.getDate() - 1);
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }

  const getDeltaHTML = (val, isPercent = false, invertGoodBad = false, tight = false) => {
    let numVal = Number(val || 0);
    let sizeClass = tight ? 'text-[9px] md:text-[11px]' : 'text-xs md:text-sm';
    if(numVal === 0) return `<span class="text-gray-400 font-bold ${sizeClass}">Tetap 0</span>`;
    
    let isGood = invertGoodBad ? numVal < 0 : numVal > 0;
    let color = isGood ? 'text-green-600' : 'text-red-600';
    let icon = numVal > 0 ? '▲' : '▼';
    let displayVal = isPercent ? pct(Math.abs(numVal)) : fmtB(Math.abs(numVal));
    return `<span class="${color} font-black ${sizeClass}">${icon} ${displayVal}</span>`;
  };

  const KPI_YEAR_END_DATE = '2025-12-31';
  const KPI_YEAR_END_LABEL = 'Yearly';

  function renderKpiYearEndComparison(value, current, isPercent = false, invertGoodBad = false) {
    if (value === null || value === undefined || !Number.isFinite(Number(value))) return '';
    const shownValue = isPercent ? pct(value) : `Rp ${fmtB(value)}`;
    const delta = Number(current || 0) - Number(value || 0);
    return `
      <div class="kpi-compare-row kpi-compare-row--year" title="Pembanding 31 Desember 2025">
        <div class="kpi-compare-badge kpi-compare-badge--year">${KPI_YEAR_END_LABEL} <span>${shownValue}</span></div>
        <div class="whitespace-nowrap">${getDeltaHTML(delta, isPercent, invertGoodBad, true)}</div>
      </div>`;
  }

  function getTrendRows(raw) {
    return Array.isArray(raw) ? raw : (raw?.tren_npl || raw?.tren_portofolio || []);
  }

  let chartTrenInstance = null;
  let chartRunoffInstance = null; 
  let initialHarianDate = null; 
  let trenPortoDataGlobal = [];

  function getDashboardChartTheme() {
    const dark = document.documentElement.getAttribute('data-monbis-theme') === 'dark';
    return {
      text: dark ? '#cbd5e1' : '#475569',
      grid: dark ? 'rgba(148,163,184,.18)' : '#f3f4f6'
    };
  }

  function refreshDashboardChartTheme() {
    const theme = getDashboardChartTheme();
    [chartTrenInstance, chartRunoffInstance].forEach(chart => {
      if (!chart) return;
      chart.options.plugins.legend.labels.color = theme.text;
      chart.options.scales.x.ticks.color = theme.text;
      chart.options.scales.y.ticks.color = theme.text;
      chart.options.scales.y.grid.color = theme.grid;
      chart.update('none');
    });
  }

  const apiCall = (url, opt={}) => (window.apiFetch ? window.apiFetch(url, opt) : fetch(url, opt));
  const DASHBOARD_CHART_FONT = "'Roboto', Arial, sans-serif";
  if (typeof Chart !== 'undefined' && Chart.defaults?.font) {
    Chart.defaults.font.family = DASHBOARD_CHART_FONT;
  }

  function getDashboardNominalMode() {
    const mode = document.getElementById('filter_nominal')?.value;
    return mode === 'baki_debet' ? 'baki_debet' : 'saldo_bank';
  }

  function updateDashboardNominalLabels() {
    const label = getDashboardNominalMode() === 'baki_debet' ? 'Baki Debet' : 'Saldo Bank';
    const saldoLabel = document.querySelector('.kpi-card--saldo p');
    const nplLabel = document.querySelector('.kpi-card--npl p');
    if (saldoLabel) saldoLabel.textContent = label;
    if (nplLabel) nplLabel.textContent = `Total NPL (${label})`;
  }

  function syncDashboardRunoffPeriodFilter() {
    const select = document.getElementById('filter_tren_runoff');
    if (!select) return;

    if (getDashboardNominalMode() === 'saldo_bank') {
      select.innerHTML = '<option value="6_bulan" selected>6 Bulan Terakhir</option>';
      select.value = '6_bulan';
      select.disabled = true;
      select.title = 'Saldo Bank memakai enam snapshot nominatif bulanan terakhir';
      select.classList.add('opacity-70', 'cursor-not-allowed');
      return;
    }

    select.innerHTML = `
      <option value="tahunan">Periode Tahunan</option>
      <option value="bulanan" selected>Periode Bulanan</option>
      <option value="mingguan">Periode Mingguan</option>
      <option value="30_hari">30 Hari Terakhir</option>
      <option value="14_hari">14 Hari Terakhir</option>
      <option value="7_hari">7 Hari Terakhir</option>`;
    select.value = 'bulanan';
    select.disabled = false;
    select.title = '';
    select.classList.remove('opacity-70', 'cursor-not-allowed');
  }

  // ==========================================
  // EVENT LISTENER
  // ==========================================
  document.getElementById('btnToggleFilter').addEventListener('click', function() {
      const filterForm = document.getElementById('formFilterMaster');
      filterForm.classList.toggle('hidden');
      filterForm.classList.toggle('flex');
      this.classList.toggle('is-active', !filterForm.classList.contains('hidden'));
      this.setAttribute('aria-expanded', String(!filterForm.classList.contains('hidden')));
  });

  document.getElementById('btnCloseFilter')?.addEventListener('click', function() {
      const filterForm = document.getElementById('formFilterMaster');
      const filterToggle = document.getElementById('btnToggleFilter');
      filterForm.classList.add('hidden');
      filterForm.classList.remove('flex');
      filterToggle?.classList.remove('is-active');
      filterToggle?.setAttribute('aria-expanded', 'false');
  });

  async function getLastHarianData() {
    try { const r = await apiCall('./api/date/'); const j = await r.json(); return j.data || null; } catch { return null; }
  }

  async function populateKantorOptions(userKode) {
    const optKantor = document.getElementById('filter_kantor');
    try {
      if(userKode && userKode !== '000'){
        optKantor.innerHTML = `<option value="${userKode}">${userKode}</option>`; optKantor.value = userKode; optKantor.disabled = true; return;
      }
      const res = await apiCall('./api/kode/', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({type:'kode_kantor'}) });
      const j = await res.json();
      let html = `<option value="000">Konsolidasi</option><option value="SEMARANG">Korwil Semarang</option><option value="SOLO">Korwil Solo</option><option value="BANYUMAS">Korwil Banyumas</option><option value="PEKALONGAN">Korwil Pekalongan</option>`;
      if(j.data) j.data.filter(x => x.kode_kantor !== '000').forEach(k => html += `<option value="${k.kode_kantor}">${k.kode_kantor} - ${k.nama_kantor || k.nama_cabang || ''}</option>`);
      optKantor.innerHTML = html; optKantor.disabled = false;
    } catch(e) {
        optKantor.innerHTML=`<option value="000">Konsolidasi (Semua Cabang)</option>`; optKantor.disabled = false;
    }
  }

  window.addEventListener('DOMContentLoaded', async () => {
    updateDashboardNominalLabels();
    syncDashboardRunoffPeriodFilter();
    const user = (window.getUser && window.getUser()) || null;
    const uKode = (user?.kode ? String(user.kode).padStart(3,'0') : null);
    await populateKantorOptions(uKode);

    const d = await getLastHarianData(); 
    if(d) {
      document.getElementById('filter_closing').value = d.last_closing;
      document.getElementById('filter_harian').value  = d.last_created;
    } else {
      document.getElementById('filter_closing').value = '2026-02-28';
      document.getElementById('filter_harian').value = '2026-03-28';
    }

    initialHarianDate = document.getElementById('filter_harian').value;

    fetchDashboardUtama();
    Promise.all([fetchTrenPortofolio(), fetchTrenRunoff()]);
  });

  document.getElementById('formFilterMaster').addEventListener('submit', e => {
    e.preventDefault();
    updateDashboardNominalLabels();
    fetchDashboardUtama();
    Promise.all([fetchTrenPortofolio(), fetchTrenRunoff()]);
    if(window.innerWidth < 1024) {
        document.getElementById('formFilterMaster').classList.add('hidden');
        document.getElementById('formFilterMaster').classList.remove('flex');
        document.getElementById('btnToggleFilter')?.classList.remove('is-active');
        document.getElementById('btnToggleFilter')?.setAttribute('aria-expanded', 'false');
    }
  });

  let dashboardFilterTimer = null;
  function scheduleDashboardFilter() {
    clearTimeout(dashboardFilterTimer);
    dashboardFilterTimer = setTimeout(() => {
      document.getElementById('formFilterMaster')?.requestSubmit();
    }, 180);
  }

  document.getElementById('filter_nominal')?.addEventListener('change', () => {
    updateDashboardNominalLabels();
    syncDashboardRunoffPeriodFilter();
    scheduleDashboardFilter();
  });
  ['filter_closing','filter_harian','filter_kantor'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', scheduleDashboardFilter);
  });

  document.getElementById('filter_tren').addEventListener('change', () => { fetchTrenPortofolio(); });
  document.getElementById('filter_tren_tipe').addEventListener('change', () => { renderChartPortofolio(); });
  document.getElementById('filter_tren_runoff').addEventListener('change', () => { fetchTrenRunoff(); });
  document.addEventListener('monbis-theme-change', refreshDashboardChartTheme);


  // ==========================================
  // CHART 1: TREN PORTOFOLIO
  // ==========================================
  async function fetchTrenPortofolio() {
    const loadingChart = document.getElementById('loadingChartTren'); 
    loadingChart.classList.remove('hidden');
    let kantor = document.getElementById('filter_kantor').value;
    
    const payload = { 
        type: 'tren_portofolio_kredit', 
        harian_date: document.getElementById('filter_harian').value, 
        periode: document.getElementById('filter_tren').value,
        hitung_berdasarkan: getDashboardNominalMode()
    };
    
    if(kantor !== '000') { 
        if(['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].includes(kantor)) payload.korwil = kantor; 
        else payload.kode_kantor = kantor; 
    } else {
        payload.kode_kantor = "000";
    }

    try {
      const res = await apiCall('./api/dashboard/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      const json = await res.json(); 
      trenPortoDataGlobal = Array.isArray(json.data) ? json.data : (json.data?.tren_portofolio || []);
      renderChartPortofolio();
    } catch(e) {
      trenPortoDataGlobal = [];
      renderChartPortofolio();
    } finally { 
      loadingChart.classList.add('hidden'); 
    }
  }

  function renderChartPortofolio() {
    const canvas = document.getElementById('canvasTrenPortofolio'); 
    const ctx = canvas.getContext('2d');
    const chartTheme = getDashboardChartTheme();
    if(chartTrenInstance) chartTrenInstance.destroy();
    
    if(!trenPortoDataGlobal || trenPortoDataGlobal.length === 0) {
      ctx.clearRect(0, 0, canvas.width, canvas.height); 
      ctx.font = "14px Roboto, Arial, sans-serif"; ctx.fillStyle = "#9ca3af"; ctx.textAlign = "center";
      ctx.fillText("Data tren tidak tersedia untuk periode ini", canvas.width/2, canvas.height/2); 
      return;
    }

    const tipe = document.getElementById('filter_tren_tipe').value;
    const labels = trenPortoDataGlobal.map(d => d.label || d.tanggal); 
    
    let datasets = [];
    let yAxisCallback;

    if (tipe === 'nom') {
        const dataTotal = trenPortoDataGlobal.map(d => Number(d.osc_total)); 
        const dataRR = trenPortoDataGlobal.map(d => Number(d.osc_rr)); 
        const dataNPL = trenPortoDataGlobal.map(d => Number(d.osc_npl)); 
        
        let gradTotal = ctx.createLinearGradient(0, 0, 0, 300); gradTotal.addColorStop(0, 'rgba(59, 130, 246, 0.1)'); gradTotal.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
        let gradRR = ctx.createLinearGradient(0, 0, 0, 300); gradRR.addColorStop(0, 'rgba(16, 185, 129, 0.1)'); gradRR.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
        let gradNPL = ctx.createLinearGradient(0, 0, 0, 300); gradNPL.addColorStop(0, 'rgba(239, 68, 68, 0.1)'); gradNPL.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        datasets = [
            { label: 'OSC Total', data: dataTotal, borderColor: '#3b82f6', backgroundColor: gradTotal, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#3b82f6', pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 },
            { label: 'OSC RR', data: dataRR, borderColor: '#10b981', backgroundColor: gradRR, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#10b981', pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 },
            { label: 'OSC NPL', data: dataNPL, borderColor: '#ef4444', backgroundColor: gradNPL, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#ef4444', pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 }
        ];
        yAxisCallback = function(value) { return fmtB(value); };
    } else if (tipe === 'pct') {
        const dataRRPct = trenPortoDataGlobal.map(d => parseFloat(Number(d.rr_persen).toFixed(2))); 
        const dataNPLPct = trenPortoDataGlobal.map(d => parseFloat(Number(d.npl_persen).toFixed(2))); 
        
        let gradRR = ctx.createLinearGradient(0, 0, 0, 300); gradRR.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); gradRR.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
        let gradNPL = ctx.createLinearGradient(0, 0, 0, 300); gradNPL.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); gradNPL.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        datasets = [
            { label: 'RR (%)', data: dataRRPct, borderColor: '#10b981', backgroundColor: gradRR, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#10b981', pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 },
            { label: 'NPL (%)', data: dataNPLPct, borderColor: '#ef4444', backgroundColor: gradNPL, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#ef4444', pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 }
        ];
        yAxisCallback = function(value) { return `${Number(value).toFixed(2)}%`; };
    } else if (tipe === 'npl') {
        const dataNPLPct = trenPortoDataGlobal.map(d => parseFloat(Number(d.npl_persen).toFixed(2))); 
        let gradNPL = ctx.createLinearGradient(0, 0, 0, 300); gradNPL.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); gradNPL.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        datasets = [
            { label: 'NPL (%)', data: dataNPLPct, borderColor: '#ef4444', backgroundColor: gradNPL, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#ef4444', pointRadius: 4, pointHoverRadius: 6, fill: true, tension: 0.4 }
        ];
        yAxisCallback = function(value) { return `${Number(value).toFixed(2)}%`; };
    }

    const labelPlugin = {
        id: 'alwaysShowLabels',
        afterDatasetsDraw(chart) {
            const periode = document.getElementById('filter_tren').value;
            if (periode !== 'bulanan') return;

            const { ctx, data } = chart;
            ctx.save();
            ctx.font = window.innerWidth < 768 ? 'bold 9px Roboto' : 'bold 11px Roboto';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            const numPoints = data.labels.length;
            const numDatasets = data.datasets.length;

            for (let i = 0; i < numPoints; i++) {
                let points = [];
                for (let j = 0; j < numDatasets; j++) {
                    const meta = chart.getDatasetMeta(j);
                    if (!meta.hidden && meta.data[i]) {
                        const val = data.datasets[j].data[i];
                        const text = (tipe === 'nom') ? fmtB(val) : `${Number(val).toFixed(2)}%`;
                        const pos = meta.data[i].tooltipPosition();
                        points.push({ index: j, text: text, color: data.datasets[j].borderColor, x: pos.x, y: pos.y });
                    }
                }
                points.sort((a, b) => a.y !== b.y ? a.y - b.y : a.index - b.index);
                for (let k = 0; k < points.length; k++) {
                    let desiredY = points[k].y - 12; 
                    if (k > 0) {
                        if (Math.abs(desiredY - points[k-1].drawY) < 16) {
                            desiredY = points[k].y + 14; 
                            if (Math.abs(desiredY - points[k-1].drawY) < 16) {
                                desiredY = points[k-1].drawY + 16;
                            }
                        }
                    }
                    points[k].drawY = desiredY;
                    ctx.fillStyle = points[k].color; 
                    ctx.fillText(points[k].text, points[k].x, points[k].drawY);
                }
            }
            ctx.restore();
        }
    };

    chartTrenInstance = new Chart(ctx, {
      type: 'line',
      data: { labels: labels, datasets: datasets },
      options: { 
          layout: { padding: { top: 30, bottom: 10, left: window.innerWidth < 768 ? -5 : 10, right: 10 } },
          responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
          plugins: { 
              legend: { position: 'top', labels: { color: chartTheme.text, usePointStyle: true, boxWidth: 10, font: {family: DASHBOARD_CHART_FONT, size: window.innerWidth < 768 ? 9 : 11, weight: 'bold'} } }, 
              tooltip: { 
                  backgroundColor: 'rgba(17, 24, 39, 0.95)', padding: 10, usePointStyle: true,
                  callbacks: { 
                      labelColor: function(context) { return { borderColor: context.dataset.borderColor, backgroundColor: context.dataset.borderColor }; },
                      label: function(c) { 
                          const raw = c.raw;
                          const dataObj = trenPortoDataGlobal[c.dataIndex];
                          let text = '';
                          
                          if (tipe === 'nom') {
                              text = `${c.dataset.label}: Rp ${fmtB(raw)}`;
                              if (c.datasetIndex === 0 && dataObj.gap_osc_total) {
                                  let isGood = dataObj.gap_osc_total > 0;
                                  text += ` (${isGood ? '🟢 ▲' : '🔴 ▼'} ${fmtB(Math.abs(dataObj.gap_osc_total))})`;
                              }
                              if (c.datasetIndex === 1 && dataObj.gap_osc_rr) {
                                  let isGood = dataObj.gap_osc_rr > 0;
                                  text += ` (${isGood ? '🟢 ▲' : '🔴 ▼'} ${fmtB(Math.abs(dataObj.gap_osc_rr))})`;
                              }
                              if (c.datasetIndex === 2 && dataObj.gap_osc_npl) {
                                  let isGood = dataObj.gap_osc_npl < 0; 
                                  text += ` (${isGood ? '🟢 ▼' : '🔴 ▲'} ${fmtB(Math.abs(dataObj.gap_osc_npl))})`;
                              }
                          } else if (tipe === 'pct') {
                              text = `${c.dataset.label}: ${Number(raw).toFixed(2)}%`;
                              if (c.datasetIndex === 0 && dataObj.gap_rr_persen) {
                                  let isGood = dataObj.gap_rr_persen > 0;
                                  text += ` (${isGood ? '🟢 ▲' : '🔴 ▼'} ${Math.abs(dataObj.gap_rr_persen)}%)`;
                              }
                              if (c.datasetIndex === 1 && dataObj.gap_npl_persen) {
                                  let isGood = dataObj.gap_npl_persen < 0; 
                                  text += ` (${isGood ? '🟢 ▼' : '🔴 ▲'} ${Math.abs(dataObj.gap_npl_persen)}%)`;
                              }
                          } else if (tipe === 'npl') {
                              text = `NPL: ${Number(raw).toFixed(2)}% (Rp ${fmtB(dataObj.osc_npl)})`;
                              if (dataObj.gap_npl_persen) {
                                  let isGood = dataObj.gap_npl_persen < 0; 
                                  text += ` (${isGood ? '🟢 ▼' : '🔴 ▲'} ${Math.abs(dataObj.gap_npl_persen)}%)`;
                              }
                          }
                          return text;
                      } 
                  } 
              } 
          }, 
          scales: { 
              x: { grid: { display: false }, ticks: { color: chartTheme.text, font: {size: window.innerWidth < 768 ? 8 : 10}} }, 
              y: { beginAtZero: false, grid: { borderDash: [4, 4], color: chartTheme.grid }, ticks: { color: chartTheme.text, font: {size: window.innerWidth < 768 ? 8 : 10}, callback: yAxisCallback } } 
          } 
      },
      plugins: [labelPlugin] 
    });
  }

  // ==========================================
  // CHART 2: TREN RUN OFF
  // ==========================================
  async function fetchTrenRunoff(isRetry = false, targetDateOverride = null) {
    const loadingChart = document.getElementById('loadingChartRunoff');
    if (!isRetry) loadingChart.classList.remove('hidden');

    let kantor = document.getElementById('filter_kantor').value;
    let currFilterDate = document.getElementById('filter_harian').value;
    
    let isDefaultDate = (currFilterDate === initialHarianDate);
    let targetRealtimeDate = isDefaultDate ? getTodayRealtime() : currFilterDate;
    let baseDate = targetDateOverride || targetRealtimeDate;
    document.getElementById('label_runoff_date').innerText = `Berdasarkan Tanggal: ${baseDate}`;

    const payload = { 
      type: 'tren_runoff_realisasi', 
      harian_date: baseDate,
      periode: document.getElementById('filter_tren_runoff').value,
      hitung_berdasarkan: getDashboardNominalMode()
    };

    if(kantor !== '000') { 
        if(['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].includes(kantor)) payload.korwil = kantor; 
        else payload.kode_kantor = kantor; 
    }

    try {
      let res = await apiCall('./api/dashboard/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      let json = await res.json();
      let dataToRender = Array.isArray(json.data) ? json.data : (json.data?.tren_runoff_realisasi || []);
      let isAllZero = dataToRender.length > 0 && dataToRender.every(d => d.total_realisasi === 0 && d.total_runoff === 0);

      if ((dataToRender.length === 0 || isAllZero) && !isRetry && isDefaultDate) {
          return fetchTrenRunoff(true, getYesterdayRealtime()); 
      }
      renderChartRunoff(dataToRender);
    } catch(e) { 
      renderChartRunoff([]);
    } finally {
      if (!isRetry) loadingChart.classList.add('hidden');
    }
  }

  function renderChartRunoff(dataArray) {
    const canvas = document.getElementById('canvasTrenRunoff'); if(!canvas) return; const ctx = canvas.getContext('2d');
    const chartTheme = getDashboardChartTheme();
    if(chartRunoffInstance) chartRunoffInstance.destroy();
    
    if(!dataArray || dataArray.length === 0) {
      ctx.clearRect(0, 0, canvas.width, canvas.height); ctx.font = "14px Roboto, Arial, sans-serif"; ctx.fillStyle = "#9ca3af"; ctx.textAlign = "center";
      ctx.fillText("Data tidak tersedia", canvas.width/2, canvas.height/2); return;
    }

    const labels = dataArray.map(d => d.label); 
    const dataRealisasi = dataArray.map(d => Number(d.total_realisasi) || 0); 
    const dataRealisasiKredit = dataArray.map(d => Number(d.realisasi_kredit) || 0);
    const dataRestruckKredit = dataArray.map(d => Number(d.restruck_kredit ?? d.restrukturisasi) || 0);
    const dataRunoff = dataArray.map(d => Number(d.total_runoff) || 0); 
    const dataLunas = dataArray.map(d => Number(d.total_lunas) || 0);
    const dataNoaLunas = dataArray.map(d => Number(d.noa_lunas) || 0);
    const dataAngsuran = dataArray.map(d => Number(d.total_angsuran) || 0);
    const dataNoaAngsuran = dataArray.map(d => Number(d.noa_angsuran) || 0);
    const dataGrowth = dataArray.map(d => Number(d.growth) || 0);

    let gradReal = ctx.createLinearGradient(0, 0, 0, 300); gradReal.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); gradReal.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
    let gradRunoff = ctx.createLinearGradient(0, 0, 0, 300); gradRunoff.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); gradRunoff.addColorStop(1, 'rgba(239, 68, 68, 0.0)');
    
    const labelPluginRunoff = {
        id: 'alwaysShowLabelsRunoff',
        afterDatasetsDraw(chart) {
            const periode = document.getElementById('filter_tren_runoff').value;
            if (periode !== 'bulanan') return;

            const { ctx, data } = chart;
            ctx.save();
            ctx.font = window.innerWidth < 768 ? 'bold 9px Roboto' : 'bold 11px Roboto';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            const numPoints = data.labels.length;
            const numDatasets = data.datasets.length;

            for (let i = 0; i < numPoints; i++) {
                let points = [];
                for (let j = 0; j < numDatasets; j++) {
                    const meta = chart.getDatasetMeta(j);
                    if (!meta.hidden && meta.data[i]) {
                        const val = data.datasets[j].data[i];
                        const text = fmtB(val);
                        const pos = meta.data[i].tooltipPosition();
                        points.push({ index: j, text: text, color: data.datasets[j].borderColor, x: pos.x, y: pos.y });
                    }
                }
                points.sort((a, b) => a.y !== b.y ? a.y - b.y : a.index - b.index);
                for (let k = 0; k < points.length; k++) {
                    let desiredY = points[k].y - 12; 
                    if (k > 0) {
                        if (Math.abs(desiredY - points[k-1].drawY) < 16) {
                            desiredY = points[k].y + 14; 
                            if (Math.abs(desiredY - points[k-1].drawY) < 16) {
                                desiredY = points[k-1].drawY + 16;
                            }
                        }
                    }
                    points[k].drawY = desiredY;
                    ctx.fillStyle = points[k].color; 
                    ctx.fillText(points[k].text, points[k].x, points[k].drawY);
                }
            }
            ctx.restore();
        }
    };

    chartRunoffInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          { label: 'Realisasi', data: dataRealisasi, borderColor: '#10b981', backgroundColor: gradReal, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#10b981', pointBorderWidth: 2, pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 },
          { label: 'Run Off', data: dataRunoff, borderColor: '#ef4444', backgroundColor: gradRunoff, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#ef4444', pointBorderWidth: 2, pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 }
        ]
      },
      options: {
        layout: { padding: { top: 18, bottom: 4, left: window.innerWidth < 768 ? -5 : 10, right: 10 } },
        responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { position: 'bottom', labels: { color: chartTheme.text, usePointStyle: true, boxWidth: 10, padding: 5, font: {family: DASHBOARD_CHART_FONT, size: window.innerWidth < 768 ? 9 : 12, weight: 'bold'} } },
          tooltip: {
            backgroundColor: 'rgba(17, 24, 39, 0.95)', padding: 12, titleFont: { size: 13, family: DASHBOARD_CHART_FONT }, bodyFont: { size: 12, family: DASHBOARD_CHART_FONT },
            usePointStyle: true,
            callbacks: {
              labelColor: function(context) { return { borderColor: context.dataset.borderColor, backgroundColor: context.dataset.borderColor }; },
              label: function(c) { return `${c.dataset.label}: Rp ${fmtB(c.raw)}`; },
              afterBody: function(c) {
                if (c.length > 0) { 
                  let idx = c[0].dataIndex;
                  let realisasiKredit = dataRealisasiKredit[idx];
                  let restruckKredit = dataRestruckKredit[idx];
                  let lunas = dataLunas[idx]; let noaLunas = dataNoaLunas[idx];
                  let angsuran = dataAngsuran[idx]; let noaAngsuran = dataNoaAngsuran[idx];
                  let g = dataGrowth[idx]; 
                  let lines = [];
                  lines.push('------------------------');
                  lines.push(`Detail Realisasi:`);
                  lines.push(`  • Realisasi Kredit: Rp ${fmtB(realisasiKredit)}`);
                  lines.push(`  • Restruck Kredit: Rp ${fmtB(restruckKredit)}`);
                  lines.push('');
                  lines.push(`Detail Run Off:`);
                  lines.push(`  • Lunas: Rp ${fmtB(lunas)} (${fmt(noaLunas)} NOA)`);
                  lines.push(`  • Angsuran: Rp ${fmtB(angsuran)} (${fmt(noaAngsuran)} NOA)`);
                  lines.push('');
                  let isGood = g >= 0;
                  lines.push(`Growth: ${isGood ? '🟢 ▲' : '🔴 ▼'} Rp ${fmtB(Math.abs(g))}`);
                  return lines;
                }
              }
            }
          }
        },
        scales: { 
            x: { grid: { display: false }, ticks: { color: chartTheme.text, font: {size: window.innerWidth < 768 ? 8 : 10}} }, 
            y: { grid: { borderDash: [4,4], color: chartTheme.grid }, ticks: { color: chartTheme.text, font: {size: window.innerWidth < 768 ? 8 : 10}, callback: function(val) { return fmtB(val); } } } 
        }
      },
      plugins: [labelPluginRunoff]
    });
  }

  // ==========================================
  // FETCH API MODULAR (WIDGET-BASED)
  // ==========================================
  async function fetchWidgetData(type, isH1 = false, options = {}) {
    let kantor = document.getElementById('filter_kantor').value;
    let currDate = options.harian_date || document.getElementById('filter_harian').value;
    
    if (isH1 && !options.harian_date && currDate === initialHarianDate) currDate = getH1Date(currDate);
    let targetRealisasiDate = (currDate === initialHarianDate) ? getTodayRealtime() : currDate;

    const payload = { 
      type: type, 
      closing_date: options.closing_date || document.getElementById('filter_closing').value, 
      harian_date: currDate,
      harian_date_realisasi: targetRealisasiDate,
      hitung_berdasarkan: getDashboardNominalMode()
    };
    if (options.periode) payload.periode = options.periode;
    
    if(kantor !== '000') { 
        if(['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].includes(kantor)) payload.korwil = kantor; 
        else payload.kode_kantor = kantor; 
    }

    try {
      const res = await apiCall('./api/dashboard/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      const json = await res.json();
      return json.data || null;
    } catch(e) { return null; }
  }

  function fetchDashboardUtama() {
    document.getElementById('loadingDash').classList.add('hidden'); 
    document.getElementById('contentDash').classList.remove('hidden');

    let kantorMode = document.getElementById('filter_kantor').value;

    const pSaldoBank    = fetchWidgetData('saldo_bank');
    const pRealProduk   = fetchWidgetData('realisasi_by_produk');
    const pTrenNpl      = fetchWidgetData('test tren npl');
    const pRrCabang     = fetchWidgetData('test rr cabang');
    const pRunoffKorwil = fetchWidgetData('test runoff korwil');
    const pFlowKorwil   = fetchWidgetData('test flow recovery npl');
    const pTopReal      = fetchWidgetData('test top realisasi');
    const pTopNpl       = fetchWidgetData('test top bottom npl');
    const pDeltaNpl     = fetchWidgetData('test delta npl');
    const pDeposito     = fetchWidgetData('test perkembangan deposito', true);
    const pTabungan     = fetchWidgetData('test perkembangan tabungan', true);
    // Snapshot pembanding tetap memakai response endpoint yang sama.
    const pYearEndTrend = fetchWidgetData('test tren npl', false, { periode: 'tahunan' });
    const pYearEndRr = fetchWidgetData('test rr cabang', false, { closing_date: KPI_YEAR_END_DATE });
    const pDepositoYearEnd = fetchWidgetData('test perkembangan deposito', false, { closing_date: KPI_YEAR_END_DATE });
    const pTabunganYearEnd = fetchWidgetData('test perkembangan tabungan', false, { closing_date: KPI_YEAR_END_DATE });

    const yearEndTrendPromise = pYearEndTrend.then(getTrendRows).then(rows => rows.find(row => row?.tanggal === KPI_YEAR_END_DATE) || null);
    const yearEndRrPromise = pYearEndRr.then(raw => (raw?.repayment_rate || raw || {}).grand_total || null);

    Promise.all([pSaldoBank, yearEndTrendPromise]).then(([sb, yearEndTrend]) => {
      if(!sb) return;
      document.getElementById('kpi_saldobank').textContent = `Rp ${fmtB(sb.actual)}`;
      document.getElementById('kpi_saldobank_pill').innerHTML = `
        <div class="kpi-compare-row kpi-compare-row--month">
            <div class="kpi-compare-badge kpi-compare-badge--month" title="Pembanding closing bulan sebelumnya">Monthly <span>Rp ${fmtB(sb.closing)}</span></div>
            <div class="whitespace-nowrap">${getDeltaHTML(sb.delta, false, false, true)}</div>
        </div>
        ${renderKpiYearEndComparison(yearEndTrend?.total_kredit ?? yearEndTrend?.total_baki_debet, sb.actual, false, false)}`;
    });

    pRealProduk.then(rpRaw => {
      if(!rpRaw) return;
      let rp = rpRaw?.realisasi_by_produk || rpRaw || {};
      let prods = (rp.detail_produk || []).slice(0, 5);
      let grandTotal = rp.grand_total?.total_realisasi || 0;
      document.getElementById('label_total_realisasi_produk').textContent = `Rp ${fmtB(grandTotal)}`;
      renderUniversalList('box_realisasi_produk', prods, 'nama_produk', 'total_realisasi', 'noa_realisasi', 'bg-indigo-400', false, 'NOA');
    });

    Promise.all([pTrenNpl, pRrCabang, yearEndTrendPromise, yearEndRrPromise]).then(([tNplRaw, rrRaw, yearEndTrend, yearEndRr]) => {
      let tNpl = getTrendRows(tNplRaw);
      let rrData = rrRaw?.repayment_rate || rrRaw || {};
      
      let osPrev = 0;
      if(tNpl && tNpl.length > 0) {
        const last = tNpl[tNpl.length - 1]; const prev = tNpl.length > 1 ? tNpl[tNpl.length - 2] : last; 
        osPrev = prev.total_baki_debet || prev.osc_total || 0; 
        
        document.getElementById('kpi_npl').textContent = `Rp ${fmtB(last.npl_amt || last.osc_npl)}`;
        document.getElementById('kpi_npl_pill').innerHTML = `
            <div class="kpi-compare-row kpi-compare-row--month">
                <div class="kpi-compare-badge kpi-compare-badge--month" title="Pembanding closing bulan sebelumnya">Monthly <span>${pct(prev.npl_persen)}</span></div>
                <div class="whitespace-nowrap">${getDeltaHTML(last.npl_persen - prev.npl_persen, true, true, true)}</div>
                <div class="kpi-status-badge kpi-status-badge--negative kpi-status-badge--right">Act <span>${pct(last.npl_persen)}</span></div>
            </div>
            ${renderKpiYearEndComparison(yearEndTrend?.npl_persen, last.npl_persen, true, true)}`;
      }

      if(rrData && rrData.grand_total) {
        const rrG = rrData.grand_total; let osCurr = rrG.os_total || 0;
        
        document.getElementById('kpi_os').textContent = `Rp ${fmtB(osCurr)}`;
        document.getElementById('kpi_os_pill').innerHTML = `
          <div class="flex items-center gap-1.5 md:gap-2">
              <div class="kpi-compare-badge kpi-compare-badge--month" title="Pembanding closing bulan sebelumnya">Monthly <span>Rp ${fmtB(osPrev)}</span></div>
              <div class="whitespace-nowrap">${getDeltaHTML(osCurr - osPrev, false, false, true)}</div>
          </div>`;

        document.getElementById('kpi_rr').textContent = `Rp ${fmtB(rrG.os_lancar)}`;
        document.getElementById('kpi_rr_pill').innerHTML = `
          <div class="kpi-compare-row kpi-compare-row--month">
              <div class="kpi-compare-badge kpi-compare-badge--month" title="Pembanding closing bulan sebelumnya">Monthly <span>${pct(rrG.rr_persen_prev)}</span></div>
              <div class="whitespace-nowrap">${getDeltaHTML(rrG.delta_rr, true, false, true)}</div>
              <div class="kpi-status-badge kpi-status-badge--positive kpi-status-badge--right">Act <span>${pct(rrG.rr_persen_curr)}</span></div>
          </div>
          ${renderKpiYearEndComparison(yearEndRr?.rr_persen_prev, rrG.rr_persen_curr, true, false)}`;
      }
    });

    Promise.all([pRunoffKorwil, pFlowKorwil]).then(([roRaw, flowRaw]) => {
      let ro = roRaw?.runoff_vs_realisasi || roRaw || null;
      let flow = flowRaw?.flow_vs_recovery_npl || flowRaw || null;
      let isSpecificBranch = (kantorMode !== '000' && !['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].includes(kantorMode));

      // Pada mode cabang, total hanya ditampilkan jika tersedia minimal
      // dua kode_group1/kankas. Cabang tanpa kantor kas (kode_group1 < 2)
      // cukup menampilkan satu baris datanya saja.
      const shouldShowGrandTotal = rows => !isSpecificBranch
        || (Array.isArray(rows) && rows.length >= 2);
      // Baris agregat dibuat konsisten untuk semua mode filter.
      const totalLabel = 'TOTAL';

      if(ro && ro.detail_korwil) {
        let runoffData = [...ro.detail_korwil];
        if(ro.grand_total && shouldShowGrandTotal(ro.detail_korwil)) {
            let totalData = {...ro.grand_total};
            totalData.nama_korwil = totalLabel;
            runoffData.unshift(totalData); // Pindah total ke paling atas
        }
        renderKorwilCompare('box_runoff_realisasi', runoffData, 'realisasi', 'total_runoff', 'bg-green-400', 'bg-red-400');
      }

      if(flow && flow.detail_korwil) {
        let flowData = [...flow.detail_korwil];
        if(flow.grand_total && shouldShowGrandTotal(flow.detail_korwil)) {
             let totalData = {...flow.grand_total};
             totalData.nama_korwil = totalLabel;
             flowData.unshift(totalData); // Pindah total ke paling atas
        }
        renderKorwilCompare('box_flow_recovery', flowData, 'flow_npl', 'total_recovery', 'bg-red-400', 'bg-green-400');
      }
    });

    // 🔥 FIX: VARIABEL DALAM PROMISE.ALL HARUS DIDEKLARASIKAN!
    Promise.all([pTopReal, pTopNpl, pDeltaNpl, pRrCabang]).then(([realRaw, nplRaw, deltaRaw, rrRaw]) => {
      let real = realRaw?.top_bottom_realisasi || realRaw || {};
      let npl = nplRaw?.top_bottom_npl || nplRaw || {};
      let delta = deltaRaw?.kenaikan_penurunan_npl || deltaRaw || {};
      let rr = rrRaw?.repayment_rate || rrRaw || {};

      if(real.top_cabang) {
        // Langsung hajar render top_cabang bawaan database (tanpa disisipi Total)
        renderUniversalList('best_realisasi', real.top_cabang, 'nama_cabang', 'total_realisasi', 'noa_realisasi', 'bg-blue-500', false, 'NOA');
        // Menempelkan nama cabang ke nama AO
        let topAOCustom = (real.top_ao || []).map(ao => {
            let namaCustom = ao.nama_ao;
            if (ao.nama_cabang && ao.nama_cabang.toLowerCase() !== 'unknown') {
                let cabangShort = ao.nama_cabang.replace(/Kc\. /gi, '').replace(/Cab\. /gi, '');
                namaCustom = `${ao.nama_ao} <span class="text-[9px] text-gray-400">(${cabangShort})</span>`;
            }
            return { ...ao, nama_custom: namaCustom };
        });
        renderUniversalList('best_realisasi_ao', topAOCustom, 'nama_custom', 'total_realisasi', 'noa_realisasi', 'bg-indigo-500', false, 'NOA');
        
        let realBotList = [...(real.bottom_cabang || [])];
        renderUniversalList('list_realisasi_bottom', realBotList, 'nama_cabang', 'total_realisasi', 'noa_realisasi', 'bg-orange-400', false, 'NOA');
      }

      if(npl.bottom) {
        renderUniversalList('best_npl', npl.bottom, 'nama_cabang', 'npl_persen', 'npl_amt', 'bg-emerald-400', true, 'Rp');
        renderUniversalList('list_npl_top', npl.top, 'nama_cabang', 'npl_persen', 'npl_amt', 'bg-red-500', true, 'Rp');
      }

      if(rr.top_rr) {
        // Langsung hajar render top_rr bawaan database (tanpa disisipi Total)
        renderUniversalList('best_rr', rr.top_rr, 'nama_cabang', 'rr_persen_curr', 'os_total', 'bg-green-500', true, 'Rp');
      }

      if(delta.top_penurunan) {
        renderUniversalList('best_npl_turun', delta.top_penurunan, 'nama_cabang', 'delta_npl', 'npl_persen_curr', 'bg-teal-400', true, 'NPL Now');
        renderUniversalList('list_npl_naik', delta.top_kenaikan, 'nama_cabang', 'delta_npl', 'npl_persen_curr', 'bg-orange-500', true, 'NPL Now');
      }

      const tReal = real?.top_cabang?.[0]; const tAo = real?.top_ao?.[0]; 
      const tRR = rr?.top_rr?.[0]; const tNplBest = npl?.bottom?.[0]; const tTurun = delta?.top_penurunan?.[0];
      
      let html = '';
      if(tReal) html += `<div class="mb-3 md:mb-4"><span class="text-blue-400 font-bold">1. Realisasi Tertinggi:</span> <span class="text-white block md:inline mt-0.5 md:mt-0">${tReal.nama_cabang.replace('Kc. ','')} (${fmtB(tReal.total_realisasi)})</span></div>`;
      if(tAo) html += `<div class="mb-3 md:mb-4"><span class="text-indigo-400 font-bold">2. AO Terbaik:</span> <span class="text-white block md:inline mt-0.5 md:mt-0">${tAo.nama_ao} (${fmtB(tAo.total_realisasi)})</span></div>`;
      if(tRR) html += `<div class="mb-3 md:mb-4"><span class="text-green-400 font-bold">3. RR Terbaik:</span> <span class="text-white block md:inline mt-0.5 md:mt-0">${tRR.nama_cabang.replace('Kc. ','')} (${pct(tRR.rr_persen_curr)})</span></div>`;
      if(tNplBest) html += `<div class="mb-3 md:mb-4"><span class="text-emerald-400 font-bold">4. NPL Terbaik:</span> <span class="text-white block md:inline mt-0.5 md:mt-0">${tNplBest.nama_cabang.replace('Kc. ','')} (${pct(tNplBest.npl_persen)})</span></div>`;
      if(tTurun) html += `<div class="mb-3 md:mb-4"><span class="text-teal-400 font-bold">5. Penurunan Terbesar:</span> <span class="text-white block md:inline mt-0.5 md:mt-0">${tTurun.nama_cabang.replace('Kc. ','')} (Δ ${pct(Math.abs(tTurun.delta_npl))})</span></div>`;
      document.getElementById('dynamic_insights').innerHTML = html;
    });

    Promise.all([pDeposito, pTabungan, pDepositoYearEnd, pTabunganYearEnd]).then(([depRaw, tabRaw, depYearRaw, tabYearRaw]) => {
      let dep = depRaw?.perkembangan_deposito || depRaw || {};
      let tab = tabRaw?.perkembangan_tabungan || tabRaw || {};
      let depYear = depYearRaw?.perkembangan_deposito || depYearRaw || {};
      let tabYear = tabYearRaw?.perkembangan_tabungan || tabYearRaw || {};

      const depG = dep.grand_total || {}; const tabG = tab.grand_total || {};
      const dpkCurr = (depG.saldo_curr||0) + (tabG.saldo_curr||0); 
      const dpkPrev = (depG.saldo_prev||0) + (tabG.saldo_prev||0);
      const depYearG = depYear.grand_total || {}; const tabYearG = tabYear.grand_total || {};
      const dpkYearEnd = (depYearG.saldo_prev||0) + (tabYearG.saldo_prev||0);
      
      document.getElementById('kpi_dpk').textContent = `Rp ${fmtB(dpkCurr)}`;
      document.getElementById('kpi_dpk_pill').innerHTML = `
        <div class="kpi-compare-row kpi-compare-row--month">
            <div class="kpi-compare-badge kpi-compare-badge--month" title="Pembanding closing bulan sebelumnya">Monthly <span>Rp ${fmtB(dpkPrev)}</span></div>
            <div class="whitespace-nowrap">${getDeltaHTML(dpkCurr - dpkPrev, false, false, true)}</div>
        </div>
        ${renderKpiYearEndComparison(dpkYearEnd, dpkCurr, false, false)}`;

      if(Object.keys(dep).length > 0) {
        renderUniversalList('list_dep_saldo_top', dep.top_saldo, 'nama_cabang', 'saldo_curr', 'noa_curr', 'bg-yellow-500', false, 'Rek');
        renderUniversalList('list_dep_saldo_bot', dep.bottom_saldo || [], 'nama_cabang', 'saldo_curr', 'noa_curr', 'bg-yellow-400', false, 'Rek');
        renderUniversalList('list_dep_baru', dep.top_baru, 'nama_cabang', 'saldo_baru', 'noa_tambah', 'bg-emerald-500', false, 'Rek Baru');
        renderUniversalList('list_dep_cair', dep.top_pencairan, 'nama_cabang', 'saldo_cair', 'noa_kurang', 'bg-red-400', false, 'Rek Cair');
      }

      if(Object.keys(tab).length > 0) {
        renderUniversalList('list_tab_saldo_top', tab.top_saldo, 'nama_cabang', 'saldo_curr', 'noa_curr', 'bg-teal-500', false, 'Rek');
        renderUniversalList('list_tab_saldo_bot', tab.bottom_saldo || [], 'nama_cabang', 'saldo_curr', 'noa_curr', 'bg-teal-400', false, 'Rek');
        renderUniversalList('list_tab_baru', tab.top_baru, 'nama_cabang', 'saldo_baru', 'noa_tambah', 'bg-blue-500', false, 'Rek Baru');
        renderUniversalList('list_tab_cair', tab.top_pencairan, 'nama_cabang', 'saldo_cair', 'noa_kurang', 'bg-red-400', false, 'Rek Cair');
      }
    });
  }

  // ==========================================
  // HELPER RENDERING UI
  // ==========================================
  function renderKorwilCompare(elId, dataArray, keyA, keyB, colorA, colorB) {
    const box = document.getElementById(elId);
    box.innerHTML = '';
    // Jangan bawa posisi scroll dari filter sebelumnya.
    box.scrollTop = 0;
    box.scrollLeft = 0;

    // Lima baris (total + empat korwil) dibuat tampil penuh tanpa scrollbar.
    // Scrollbar tipis hanya dipakai jika data benar-benar lebih banyak.
    const hasOverflow = Array.isArray(dataArray) && dataArray.length > 5;
    // Tinggi box mengikuti ruang card. Saat data lebih banyak, hanya box
    // yang scroll; tinggi card tetap mengikuti tinggi chart di sebelahnya.
    box.style.maxHeight = 'none';
    box.style.overflowY = hasOverflow ? 'auto' : 'hidden';
    box.style.overflowX = 'hidden';
    box.style.overscrollBehavior = 'contain';

    if(!dataArray || !dataArray.length) return;
    let maxVal = Math.max(...dataArray.flatMap(o => [Number(o[keyA]), Number(o[keyB])])); if(maxVal === 0) maxVal = 1;
    dataArray.forEach(k => {
      let vA = Number(k[keyA]); let vB = Number(k[keyB]); let pctA = (vA / maxVal) * 100; let pctB = (vB / maxVal) * 100;
      let titleClass = k.nama_korwil.includes("KONSOLIDASI") || k.nama_korwil.includes("TOTAL") ? "text-gray-900 font-black border-b border-dashed border-gray-300 pb-1" : "text-gray-700 font-bold";
      box.innerHTML += `<div class="mb-2 min-w-0"><div class="flex justify-between min-w-0 overflow-hidden text-[10px] md:text-[11px] leading-tight ${titleClass} mb-1.5"><span class="min-w-0 truncate">${k.nama_korwil}</span></div><div class="flex flex-col gap-1 md:gap-0.5 relative"><div class="w-full bg-gray-100 h-1.5 md:h-2 rounded-r-full flex relative"><div class="${colorA} h-1.5 md:h-2 rounded-r-full bar-fill z-10" style="width: ${pctA}%"></div><span class="absolute right-0 -top-3.5 md:-top-4 text-[9px] md:text-[10px] text-gray-500 font-medium">${fmtB(vA)}</span></div><div class="w-full bg-gray-100 h-1.5 md:h-2 rounded-r-full flex relative"><div class="${colorB} h-1.5 md:h-2 rounded-r-full bar-fill z-10" style="width: ${pctB}%"></div><span class="absolute right-0 -bottom-3.5 md:-bottom-4 text-[9px] md:text-[10px] text-gray-500 font-medium">${fmtB(vB)}</span></div></div></div>`;
    });
  }

  function renderUniversalList(elId, dataArray, nameKey, valKey, subKey, colorClass, isPercent, subLabel = 'Rp') {
    const box = document.getElementById(elId); box.innerHTML = '';
    if(!dataArray || !Array.isArray(dataArray) || dataArray.length === 0) { box.innerHTML = `<p class="text-[10px] md:text-[11px] text-gray-400 italic py-2 text-center">Tidak ada data.</p>`; return; }
    
    let maxVal = Math.max(...dataArray.map(o => Math.abs(Number(o[valKey]) || 0))); if(maxVal === 0) maxVal = 1;
    
    dataArray.forEach(item => {
      let val = Number(item[valKey] || 0); let sub = Number(item[subKey] || 0); let wPct = Math.abs((val / maxVal) * 100);
      let displayVal = isPercent ? pct(Math.abs(val)) : fmtB(Math.abs(val));
      let displaySub = subLabel === 'Rp' ? `Rp ${fmtB(sub)}` : (subLabel === 'NPL Now' ? `NPL saat ini: ${pct(sub)}` : `${fmt(sub)} ${subLabel}`);
      
      let name = (item[nameKey] || '-').replace(/Kc\. /gi, '');

      // 🔥 FIX: UBAH "KAS CABANG 000" JADI NAMA CABANG DI DROPDOWN
      if (name.includes('KAS CABANG') && name.endsWith('000')) {
          let kodeCab = name.replace('KAS CABANG ', '').substring(0, 3);
          let opt = document.querySelector(`#filter_kantor option[value="${kodeCab}"]`);
          if (opt && opt.text) { name = "Cab. " + opt.text.split('-').pop().trim(); } 
          else { name = "Cabang Utama"; }
      }

      let isTotalRow = name.toUpperCase().includes("TOTAL KESELURUHAN");
      let itemBg = isTotalRow ? 'bg-blue-50 border-b-2 border-blue-200 pb-1.5 mb-3 sticky top-0 pt-1 -mt-1' : 'mb-2.5 md:mb-3 group cursor-default';
      
      box.innerHTML += `
      <div class="${itemBg} relative z-10">
        <div class="flex justify-between items-end mb-1 md:mb-1.5 relative z-10">
            <div class="flex flex-col w-2/3 pr-2">
                <span class="text-[11px] md:text-xs ${isTotalRow ? 'font-black text-blue-800' : 'font-bold text-gray-800'} truncate" title="${name.replace(/(<([^>]+)>)/ig, '')}">${name}</span>
                <span class="text-[9px] md:text-[10px] ${isTotalRow ? 'text-blue-600' : 'text-gray-500'} font-medium leading-tight">${displaySub}</span>
            </div>
            <span class="text-[11px] md:text-xs font-black ${isTotalRow ? 'text-blue-900' : 'text-gray-900'}">${val < 0 ? '-' : ''}${displayVal}</span>
        </div>
        <div class="w-full ${isTotalRow ? 'bg-blue-200' : 'bg-gray-100'} h-1.5 md:h-2 rounded-full overflow-hidden relative z-0">
            <div class="${colorClass} h-1.5 md:h-2 rounded-full bar-fill" style="width: ${Math.max(2, wPct)}%"></div>
        </div>
      </div>`;
    });
  }
</script>

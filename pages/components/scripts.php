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
  const getDashboardClosingDate = () => document.getElementById('filter_closing')?.value || '';
  const formatDashboardDate = value => {
    const parts = String(value || '').split('-');
    return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : '-';
  };
  function updateDashboardFilterSummary() {
    const summary = document.getElementById('dashboardFilterSummary');
    if (!summary) return;

    const closing = formatDashboardDate(document.getElementById('filter_closing')?.value);
    const actual = formatDashboardDate(document.getElementById('filter_harian')?.value);
    const nominal = document.getElementById('filter_nominal')?.selectedOptions?.[0]?.textContent?.trim() || '-';
    const office = document.getElementById('filter_kantor')?.selectedOptions?.[0]?.textContent?.trim() || 'Konsolidasi';
    summary.textContent = `M-1 ${closing} · Actual ${actual} · ${nominal} · ${office}`;
  }
  function getDashboardTabunganDate() {
    const selectedDate = document.getElementById('filter_harian')?.value || '';
    if (!selectedDate) return '';
    return selectedDate === getTodayRealtime() ? getH1Date(selectedDate) : selectedDate;
  }
  function updateDashboardSimpananDate() {
    const selectedDate = formatDashboardDate(document.getElementById('filter_harian')?.value);
    const tabunganDate = formatDashboardDate(getDashboardTabunganDate());
    document.querySelectorAll('[data-simpanan-date="summary"], [data-simpanan-date="deposito"]').forEach(el => {
      el.textContent = selectedDate === '-' ? 'Actual -' : selectedDate;
    });
    document.querySelectorAll('[data-simpanan-date="tabungan"]').forEach(el => {
      el.textContent = tabunganDate === '-' ? 'Actual -' : tabunganDate;
    });
  }
  const getMonthComparisonLabel = date => {
    const selectedDate = String(date || '').trim();
    return selectedDate && initialClosingDate && selectedDate !== initialClosingDate
      ? selectedDate
      : 'Monthly';
  };
  const getMonthComparisonTitle = label => label === 'Monthly'
    ? 'Perbandingan bulanan'
    : `Perbandingan tanggal ${label}`;

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
  let initialClosingDate = null;
  let trenPortoDataGlobal = [];
  let dashboardLoadVersion = 0;
  let dashboardChartTimer = null;
  let dashboardRequestController = null;

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
  const dashboardApiCall = (url, opt = {}) => apiCall(url, {
    ...opt,
    ...(dashboardRequestController ? { signal: dashboardRequestController.signal } : {})
  });
  const DASHBOARD_CHART_FONT = "'Roboto', Arial, sans-serif";
  if (typeof Chart !== 'undefined' && Chart.defaults?.font) {
    Chart.defaults.font.family = DASHBOARD_CHART_FONT;
  }

  function getDashboardNominalMode() {
    const mode = document.getElementById('filter_nominal')?.value;
    return mode === 'baki_debet' ? 'baki_debet' : 'saldo_bank';
  }

  function updateDashboardNominalLabels() {
    const saldoLabel = document.querySelector('.kpi-card--saldo p');
    const nplLabel = document.querySelector('.kpi-card--npl p');
    if (saldoLabel) saldoLabel.textContent = 'Kredit';
    if (nplLabel) nplLabel.textContent = 'NPL';
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

  function closeDashboardFilter() {
      const filterForm = document.getElementById('formFilterMaster');
      const filterToggle = document.getElementById('btnToggleFilter');
      filterForm.classList.add('hidden');
      filterForm.classList.remove('flex');
      filterToggle?.classList.remove('is-active');
      filterToggle?.setAttribute('aria-expanded', 'false');
  }

  document.getElementById('btnCloseFilter')?.addEventListener('click', closeDashboardFilter);

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
    const [d] = await Promise.all([getLastHarianData(), populateKantorOptions(uKode)]);
    if(d) {
      document.getElementById('filter_closing').value = d.last_closing;
      document.getElementById('filter_harian').value  = d.last_created;
    } else {
      document.getElementById('filter_closing').value = '2026-02-28';
      document.getElementById('filter_harian').value = '2026-03-28';
    }

    initialClosingDate = document.getElementById('filter_closing').value;
    initialHarianDate = document.getElementById('filter_harian').value;
    updateDashboardFilterSummary();

    fetchDashboardUtama();
    scheduleDashboardCharts();
  });

  document.getElementById('formFilterMaster').addEventListener('submit', e => {
    e.preventDefault();
    updateDashboardNominalLabels();
    syncDashboardRunoffPeriodFilter();
    updateDashboardFilterSummary();
    updateDashboardSimpananDate();
    fetchDashboardUtama();
    scheduleDashboardCharts();
    if(window.innerWidth < 1024) {
        document.getElementById('formFilterMaster').classList.add('hidden');
        document.getElementById('formFilterMaster').classList.remove('flex');
        document.getElementById('btnToggleFilter')?.classList.remove('is-active');
        document.getElementById('btnToggleFilter')?.setAttribute('aria-expanded', 'false');
    }
  });

  document.getElementById('filter_tren').addEventListener('change', () => { fetchTrenPortofolio(); });
  document.getElementById('filter_tren_tipe').addEventListener('change', () => { renderChartPortofolio(); });
  document.getElementById('filter_tren_runoff').addEventListener('change', () => { fetchTrenRunoff(); });
  document.addEventListener('monbis-theme-change', refreshDashboardChartTheme);


  // ==========================================
  // CHART 1: TREN PORTOFOLIO
  // ==========================================
  async function fetchTrenPortofolio() {
    const requestVersion = dashboardLoadVersion;
    const loadingChart = document.getElementById('loadingChartTren'); 
    if (loadingChart) loadingChart.classList.remove('hidden');
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
      const res = await dashboardApiCall('./api/dashboard/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      const json = await res.json(); 
      if (requestVersion !== dashboardLoadVersion) return;
      trenPortoDataGlobal = Array.isArray(json.data) ? json.data : (json.data?.tren_portofolio || []);
      renderChartPortofolio();
    } catch(e) {
      if (requestVersion !== dashboardLoadVersion) return;
      trenPortoDataGlobal = [];
      renderChartPortofolio();
    } finally { 
      if (requestVersion === dashboardLoadVersion && loadingChart) loadingChart.classList.add('hidden');
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

  function scheduleDashboardCharts() {
    clearTimeout(dashboardChartTimer);
    dashboardChartTimer = setTimeout(() => {
      Promise.all([fetchTrenPortofolio(), fetchTrenRunoff()]);
    }, 80);
  }

  // ==========================================
  // CHART 2: TREN RUN OFF
  // ==========================================
  async function fetchTrenRunoff(isRetry = false, targetDateOverride = null) {
    const requestVersion = dashboardLoadVersion;
    const loadingChart = document.getElementById('loadingChartRunoff');
    if (!isRetry && loadingChart) loadingChart.classList.remove('hidden');

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
      let res = await dashboardApiCall('./api/dashboard/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      let json = await res.json();
      if (requestVersion !== dashboardLoadVersion) return;
      let dataToRender = Array.isArray(json.data) ? json.data : (json.data?.tren_runoff_realisasi || []);
      let isAllZero = dataToRender.length > 0 && dataToRender.every(d => d.total_realisasi === 0 && d.total_runoff === 0);

      if ((dataToRender.length === 0 || isAllZero) && !isRetry && isDefaultDate) {
          return fetchTrenRunoff(true, getYesterdayRealtime()); 
      }
      renderChartRunoff(dataToRender);
    } catch(e) { 
      if (requestVersion !== dashboardLoadVersion) return;
      renderChartRunoff([]);
    } finally {
      if (requestVersion === dashboardLoadVersion && !isRetry && loadingChart) loadingChart.classList.add('hidden');
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
    if (options.year_end_date) payload.year_end_date = options.year_end_date;
    
    if(kantor !== '000') { 
        if(['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].includes(kantor)) payload.korwil = kantor; 
        else payload.kode_kantor = kantor; 
    }

    try {
      const res = await dashboardApiCall('./api/dashboard/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      const json = await res.json();
      if (!res.ok || Number(json.status) !== 200) return null;
      return json.data || null;
    } catch(e) { return null; }
  }

  async function fetchLabaSebelumPajak() {
    const kantor = document.getElementById('filter_kantor').value;
    const payload = {
      type: 'lap_laba_rugi_actual',
      harian_date: document.getElementById('filter_harian').value,
      kode_kantor: '000',
      korwil: ''
    };
    const korwil = {
      SEMARANG: 'SEMARANG',
      SOLO: 'SOLO',
      BANYUMAS: 'BANYUMAS',
      PEKALONGAN: 'PEKALONGAN',
      KW_SEMARANG: 'SEMARANG',
      KW_SOLO: 'SOLO',
      KW_BANYUMAS: 'BANYUMAS',
      KW_PEKALONGAN: 'PEKALONGAN'
    }[kantor];

    if (korwil) {
      payload.korwil = korwil;
    } else {
      payload.kode_kantor = kantor || '000';
    }

    try {
      const res = await dashboardApiCall('./api/lapkeu/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (!res.ok || Number(json.status) !== 200) return null;
      return json.data || null;
    } catch(e) {
      return null;
    }
  }

  async function fetchDashboardAccHistoryKpi() {
    const kantor = document.getElementById('filter_kantor').value;
    const payload = {
      type: 'dashboard_acc_history_kpi',
      harian_date: document.getElementById('filter_harian').value,
      closing_date: document.getElementById('filter_closing').value,
      year_end_date: KPI_YEAR_END_DATE,
      kode_kantor: '000'
    };
    const korwil = {
      SEMARANG: 'SEMARANG',
      SOLO: 'SOLO',
      BANYUMAS: 'BANYUMAS',
      PEKALONGAN: 'PEKALONGAN',
      KW_SEMARANG: 'SEMARANG',
      KW_SOLO: 'SOLO',
      KW_BANYUMAS: 'BANYUMAS',
      KW_PEKALONGAN: 'PEKALONGAN'
    }[kantor];

    if (korwil) {
      payload.korwil = korwil;
    } else {
      payload.kode_kantor = kantor || '000';
    }

    try {
      const res = await dashboardApiCall('./api/lapkeu/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (!res.ok || Number(json.status) !== 200) return null;
      return json.data || null;
    } catch(e) {
      return null;
    }
  }

  function setDashboardKpiLoading() {
    const spinner = '<span class="kpi-loading-spinner" role="status" aria-label="Memuat data"></span>';
    ['kpi_os', 'kpi_saldobank', 'kpi_npl', 'kpi_rr', 'kpi_dpk', 'kpi_laba'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.innerHTML = spinner;
    });
    ['kpi_os_pill', 'kpi_saldobank_pill', 'kpi_npl_pill', 'kpi_rr_pill', 'kpi_dpk_pill', 'kpi_laba_pill'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.innerHTML = '';
    });
    ['kpi_dpk_date', 'kpi_laba_date'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.textContent = 'Actual -';
    });
  }

  function setDashboardCompareLoading() {
    const loading = `
      <div class="flex h-full min-h-[120px] items-center justify-center gap-2 text-[10px] font-bold text-gray-400">
        <span class="kpi-loading-spinner" role="status" aria-label="Memuat data"></span>
        <span>Memuat data...</span>
      </div>`;
    ['box_runoff_realisasi', 'box_flow_recovery'].forEach(id => {
      const box = document.getElementById(id);
      if (box) box.innerHTML = loading;
    });
  }

  function fetchDashboardUtama() {
    const loadVersion = ++dashboardLoadVersion;
    if (dashboardRequestController) dashboardRequestController.abort();
    dashboardRequestController = new AbortController();
    document.getElementById('loadingDash').classList.add('hidden'); 
    document.getElementById('contentDash').classList.remove('hidden');
    setDashboardCompareLoading();
    updateDashboardSimpananDate();
    setDashboardKpiLoading();

    let kantorMode = document.getElementById('filter_kantor').value;

    const pSaldoBank    = fetchWidgetData('saldo_bank', false, { year_end_date: KPI_YEAR_END_DATE });
    const pAccHistoryKpi = fetchDashboardAccHistoryKpi();
    // Rumus lama Laba: endpoint lap_laba_rugi_actual, sekarang digantikan satu query acc_history.
    // const pLaba = fetchLabaSebelumPajak();
    const pRealProduk   = fetchWidgetData('realisasi_by_produk');
    const pRrCabang     = fetchWidgetData('test rr cabang', false, { year_end_date: KPI_YEAR_END_DATE });
    const pRunoffKorwil = fetchWidgetData('test runoff korwil');
    const pFlowKorwil   = fetchWidgetData('test flow recovery npl');
    const pTopReal      = fetchWidgetData('test top realisasi');
    const pDeltaNpl     = fetchWidgetData('test delta npl');
    const simpananDate  = document.getElementById('filter_harian').value;
    const tabunganDate  = getDashboardTabunganDate();
    const pDeposito     = fetchWidgetData('test perkembangan deposito', false, { harian_date: simpananDate });
    const pTabungan     = fetchWidgetData('test perkembangan tabungan', false, { harian_date: tabunganDate });
    // Rumus lama Simpanan: nominatif_deposito + nominatif_tabungan.
    // const pDepositoYearEnd = fetchWidgetData('test perkembangan deposito', false, { closing_date: KPI_YEAR_END_DATE });
    // const pTabunganYearEnd = fetchWidgetData('test perkembangan tabungan', false, { closing_date: KPI_YEAR_END_DATE });
    // Rumus lama KPI Simpanan, dipertahankan sebagai referensi tetapi tidak dijalankan:
    // const dpkCurr = (depG.saldo_curr || 0) + (tabG.saldo_curr || 0);
    // const dpkPrev = (depG.saldo_prev || 0) + (tabG.saldo_prev || 0);
    // const dpkYearEnd = (depYearG.saldo_prev || 0) + (tabYearG.saldo_prev || 0);
    // Rumus lama Laba: const labaSebelumPajak = Number(totals.laba_kotor ?? (pendapatan - biaya));

    pSaldoBank.then(sb => {
      if (loadVersion !== dashboardLoadVersion) return;
      if(!sb) return;
      const comparisonLabel = getMonthComparisonLabel(sb.closing_date || getDashboardClosingDate());
      const comparisonTitle = getMonthComparisonTitle(comparisonLabel);
      document.getElementById('kpi_saldobank').textContent = `Rp ${fmtB(sb.actual)}`;
      document.getElementById('kpi_saldobank_pill').innerHTML = `
        <div class="kpi-compare-row kpi-compare-row--month">
            <div class="kpi-compare-badge kpi-compare-badge--month" title="${comparisonTitle}">${comparisonLabel} <span>Rp ${fmtB(sb.closing)}</span></div>
            <div class="whitespace-nowrap">${getDeltaHTML(sb.delta, false, false, true)}</div>
        </div>
        ${renderKpiYearEndComparison(sb.year_end, sb.actual, false, false)}`;

      const nplActual = Number(sb.npl_actual || 0);
      const nplClosing = Number(sb.npl_closing || 0);
      const nplActualPct = Number(sb.npl_persen_actual || 0);
      const nplClosingPct = Number(sb.npl_persen_closing || 0);
      document.getElementById('kpi_npl').textContent = `Rp ${fmtB(nplActual)}`;
      document.getElementById('kpi_npl_pill').innerHTML = `
        <div class="kpi-compare-row kpi-compare-row--month">
          <div class="kpi-compare-badge kpi-compare-badge--month" title="${comparisonTitle}">${comparisonLabel} <span>${pct(nplClosingPct)}</span></div>
          <div class="whitespace-nowrap">${getDeltaHTML(nplActualPct - nplClosingPct, true, true, true)}</div>
          <div class="kpi-status-badge kpi-status-badge--negative kpi-status-badge--right">Act <span>${nplActualPct.toFixed(2)}</span></div>
        </div>
        ${renderKpiYearEndComparison(sb.npl_persen_year_end, nplActualPct, true, true)}`;
    });

    pAccHistoryKpi.then(financialRaw => {
      if (loadVersion !== dashboardLoadVersion) return;
      const dpkCard = document.getElementById('kpi_dpk');
      const dpkDate = document.getElementById('kpi_dpk_date');
      const dpkPill = document.getElementById('kpi_dpk_pill');
      const labaCard = document.getElementById('kpi_laba');
      const labaDate = document.getElementById('kpi_laba_date');
      const labaPill = document.getElementById('kpi_laba_pill');
      const actual = financialRaw?.snapshots?.actual;
      const closing = financialRaw?.snapshots?.closing;
      const yearEnd = financialRaw?.snapshots?.year_end;

      if (!dpkCard || !dpkDate || !dpkPill || !labaCard || !labaDate || !labaPill) return;
      if (!actual?.tanggal) {
        dpkCard.textContent = 'Tidak tersedia';
        dpkDate.textContent = 'Actual -';
        dpkPill.innerHTML = '<span class="kpi-status-badge kpi-status-badge--negative">Data simpanan gagal dimuat</span>';
        labaCard.textContent = 'Tidak tersedia';
        labaDate.textContent = 'Actual -';
        labaPill.innerHTML = '<span class="kpi-status-badge kpi-status-badge--negative">Data laba gagal dimuat</span>';
        return;
      }

      const simpanan = Number(actual.simpanan || 0);
      const simpananClosing = closing?.tanggal ? Number(closing.simpanan || 0) : null;
      const simpananYearEnd = yearEnd?.tanggal ? Number(yearEnd.simpanan || 0) : null;
      const comparisonLabel = getMonthComparisonLabel(getDashboardClosingDate());
      const comparisonTitle = getMonthComparisonTitle(comparisonLabel);
      const labaSebelumPajak = Number(actual.laba_kotor || 0);
      const labaClosing = closing?.tanggal ? Number(closing.laba_kotor || 0) : null;
      const labaYearEnd = yearEnd?.tanggal ? Number(yearEnd.laba_kotor || 0) : null;

      dpkCard.textContent = `Rp ${fmtB(simpanan)}`;
      dpkDate.textContent = `Actual ${actual.tanggal}`;
      dpkPill.innerHTML = `
        <div class="kpi-compare-row kpi-compare-row--month">
          <div class="kpi-compare-badge kpi-compare-badge--month" title="Saldo acc_history kode 204, ${comparisonTitle.toLowerCase()}">${comparisonLabel} <span>${simpananClosing === null ? '-' : `Rp ${fmtB(simpananClosing)}`}</span></div>
          <div class="whitespace-nowrap">${simpananClosing === null ? '<span class="text-gray-400 font-bold text-[9px] md:text-[11px]">Pembanding belum tersedia</span>' : getDeltaHTML(simpanan - simpananClosing, false, false, true)}</div>
        </div>
        ${renderKpiYearEndComparison(simpananYearEnd, simpanan, false, false)}`;

      labaCard.textContent = `Rp ${fmtB(labaSebelumPajak)}`;
      labaDate.textContent = `Actual ${actual.tanggal}`;
      labaPill.innerHTML = `
        <div class="kpi-compare-row kpi-compare-row--month">
          <div class="kpi-compare-badge kpi-compare-badge--month" title="${comparisonTitle}">${comparisonLabel} <span>${labaClosing === null ? '-' : `Rp ${fmtB(labaClosing)}`}</span></div>
          <div class="whitespace-nowrap">${labaClosing === null ? '<span class="text-gray-400 font-bold text-[9px] md:text-[11px]">Pembanding belum tersedia</span>' : getDeltaHTML(labaSebelumPajak - labaClosing, false, false, true)}</div>
        </div>
        ${renderKpiYearEndComparison(labaYearEnd, labaSebelumPajak, false, false)}`;
    });

    pRealProduk.then(rpRaw => {
      if (loadVersion !== dashboardLoadVersion) return;
      if(!rpRaw) return;
      let rp = rpRaw?.realisasi_by_produk || rpRaw || {};
      let prods = (rp.detail_produk || []).slice(0, 5);
      let grandTotal = rp.grand_total?.total_realisasi || 0;
      document.getElementById('label_total_realisasi_produk').textContent = `Rp ${fmtB(grandTotal)}`;
      renderUniversalList('box_realisasi_produk', prods, 'nama_produk', 'total_realisasi', 'noa_realisasi', 'bg-indigo-400', false, 'NOA');
    });

    pRrCabang.then(rrRaw => {
      if (loadVersion !== dashboardLoadVersion) return;
      let rrData = rrRaw?.repayment_rate || rrRaw || {};

      if(rrData && rrData.grand_total) {
        const rrG = rrData.grand_total;
        const comparisonLabel = getMonthComparisonLabel(getDashboardClosingDate());
        const comparisonTitle = getMonthComparisonTitle(comparisonLabel);
        let osCurr = rrG.os_total || 0;
        let osPrev = rrG.os_total_prev || 0;
        
        document.getElementById('kpi_os').textContent = `Rp ${fmtB(osCurr)}`;
        document.getElementById('kpi_os_pill').innerHTML = `
          <div class="flex items-center gap-1.5 md:gap-2">
              <div class="kpi-compare-badge kpi-compare-badge--month" title="${comparisonTitle}">${comparisonLabel} <span>Rp ${fmtB(osPrev)}</span></div>
              <div class="whitespace-nowrap">${getDeltaHTML(osCurr - osPrev, false, false, true)}</div>
          </div>
          ${renderKpiYearEndComparison(rrG.os_total_year_end, osCurr, false, false)}`;

        document.getElementById('kpi_rr').textContent = `Rp ${fmtB(rrG.os_lancar)}`;
        document.getElementById('kpi_rr_pill').innerHTML = `
          <div class="kpi-compare-row kpi-compare-row--month">
              <div class="kpi-compare-badge kpi-compare-badge--month" title="${comparisonTitle}">${comparisonLabel} <span>${pct(rrG.rr_persen_prev)}</span></div>
              <div class="whitespace-nowrap">${getDeltaHTML(rrG.delta_rr, true, false, true)}</div>
              <div class="kpi-status-badge kpi-status-badge--positive kpi-status-badge--right">Act <span>${Number(rrG.rr_persen_curr || 0).toFixed(2)}</span></div>
          </div>
          ${renderKpiYearEndComparison(rrG.rr_persen_year_end, rrG.rr_persen_curr, true, false)}`;
      } else {
        document.getElementById('kpi_os').textContent = 'Tidak tersedia';
        document.getElementById('kpi_rr').textContent = 'Tidak tersedia';
        document.getElementById('kpi_os_pill').innerHTML = '<span class="kpi-status-badge kpi-status-badge--negative">Data OS gagal dimuat</span>';
        document.getElementById('kpi_rr_pill').innerHTML = '<span class="kpi-status-badge kpi-status-badge--negative">Data RR gagal dimuat</span>';
      }
    });

    Promise.all([pRunoffKorwil, pFlowKorwil]).then(([roRaw, flowRaw]) => {
      if (loadVersion !== dashboardLoadVersion) return;
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
      } else renderKorwilCompare('box_runoff_realisasi', [], 'realisasi', 'total_runoff', 'bg-green-400', 'bg-red-400');

      if(flow && flow.detail_korwil) {
        let flowData = [...flow.detail_korwil];
        if(flow.grand_total && shouldShowGrandTotal(flow.detail_korwil)) {
             let totalData = {...flow.grand_total};
             totalData.nama_korwil = totalLabel;
             flowData.unshift(totalData); // Pindah total ke paling atas
        }
        renderKorwilCompare('box_flow_recovery', flowData, 'flow_npl', 'total_recovery', 'bg-red-400', 'bg-green-400');
      } else renderKorwilCompare('box_flow_recovery', [], 'flow_npl', 'total_recovery', 'bg-red-400', 'bg-green-400');
    });

    // 🔥 FIX: VARIABEL DALAM PROMISE.ALL HARUS DIDEKLARASIKAN!
    Promise.all([pTopReal, pDeltaNpl, pRrCabang]).then(([realRaw, deltaRaw, rrRaw]) => {
      if (loadVersion !== dashboardLoadVersion) return;
      let real = realRaw?.top_bottom_realisasi || realRaw || {};
      let delta = deltaRaw?.kenaikan_penurunan_npl || deltaRaw || {};
      let npl = deltaRaw?.top_bottom_npl || { top: deltaRaw?.top_npl || [], bottom: deltaRaw?.bottom_npl || [] };
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

    Promise.all([pDeposito, pTabungan]).then(([depRaw, tabRaw]) => {
      if (loadVersion !== dashboardLoadVersion) return;
      let dep = depRaw?.perkembangan_deposito || depRaw || {};
      let tab = tabRaw?.perkembangan_tabungan || tabRaw || {};

      const depG = dep.grand_total || {}; const tabG = tab.grand_total || {};

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

    if(!dataArray || !dataArray.length) {
      box.innerHTML = '<p class="text-[10px] md:text-[11px] text-gray-400 italic py-2 text-center">Data tidak tersedia.</p>';
      return;
    }
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

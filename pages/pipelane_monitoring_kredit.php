<?php
// Monitoring pipeline kredit dari DB Prospek + realisasi kredit DB DPK.
?>

<div class="pmk-page max-w-[1920px] mx-auto px-2 md:px-4 py-3 md:py-5 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col bg-slate-50 text-slate-800 overflow-hidden">
  <div class="pmk-top bg-white border border-slate-200 rounded-xl shadow-sm p-3 md:p-4 mb-3 shrink-0">
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-3">
      <div class="min-w-0">
        <div class="flex items-center gap-2">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-sm">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M4 19V5"></path><path d="M4 19h16"></path><path d="M8 16v-5"></path><path d="M13 16V8"></path><path d="M18 16v-9"></path></svg>
          </span>
          <div class="min-w-0">
            <h1 class="text-lg md:text-2xl font-black tracking-tight truncate">Monitoring Pipeline Kredit</h1>
            <p class="text-[10px] md:text-xs text-slate-500 font-semibold">Target, realisasi, dan follow-up pipeline dari e-prospek</p>
          </div>
        </div>
      </div>

      <form class="pmk-filters grid grid-cols-2 md:flex md:flex-wrap xl:flex-nowrap gap-2 md:items-end" onsubmit="event.preventDefault(); pmkFetchAll();">
        <div class="pmk-field">
          <label>Tahun</label>
          <select id="pmk_tahun" class="pmk-input" onchange="pmkFetchAll()"></select>
        </div>
        <div class="pmk-field">
          <label>Periode</label>
          <select id="pmk_bulan" class="pmk-input" onchange="pmkFetchAll()"></select>
        </div>
        <div class="pmk-field col-span-2 md:w-[230px]">
          <label>Kantor Cabang</label>
          <select id="pmk_kantor" class="pmk-input" onchange="pmkFetchAll()">
            <option value="000">000 - Konsolidasi</option>
          </select>
        </div>
        <button type="submit" class="pmk-btn pmk-btn-primary">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.35-4.35"></path></svg>
          Muat
        </button>
        <button type="button" onclick="pmkExportActive()" class="pmk-btn pmk-btn-outline">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="M7 10l5 5 5-5"></path><path d="M12 15V3"></path></svg>
          Unduh Data
        </button>
      </form>
    </div>
  </div>

  <div class="pmk-summary-wrap mb-3 shrink-0">
    <button type="button" id="pmk_summary_toggle" class="pmk-icon-toggle" onclick="pmkToggleSummary()" title="Tampilkan/sembunyikan summary">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path d="M4 19V5"></path><path d="M4 19h16"></path><path d="M8 16v-5"></path><path d="M13 16V8"></path><path d="M18 16v-9"></path></svg>
    </button>
    <div id="pmk_cards" class="grid grid-cols-2 xl:grid-cols-4 gap-2 md:gap-3"></div>
  </div>

  <div class="bg-white border border-slate-200 rounded-xl shadow-sm flex-1 min-h-0 flex flex-col overflow-hidden relative">
    <div id="pmk_loading" class="hidden absolute inset-0 z-40 bg-white/75 backdrop-blur-sm items-center justify-center text-emerald-700 font-black text-xs tracking-widest uppercase">
      <span class="h-8 w-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full animate-spin mr-3"></span>
      Memuat Pipeline...
    </div>

    <div class="flex items-center justify-between gap-3 px-3 md:px-4 pt-3 border-b border-slate-100 shrink-0">
      <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
        <button id="pmk_tab_pipeline" class="pmk-tab active" onclick="pmkSetTab('pipeline')" title="Daftar Pipeline">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path></svg>
          <b id="pmk_count_pipeline">0</b>
        </button>
        <button id="pmk_tab_monitoring" class="pmk-tab" onclick="pmkSetTab('monitoring')" title="Monitoring Mingguan">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24"><path d="M3 3v18h18"></path><path d="m19 9-5 5-4-4-3 3"></path></svg>
          <b id="pmk_count_monitoring">0</b>
        </button>
      </div>
      <div class="relative w-[180px] md:w-[280px] shrink-0">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.35-4.35"></path></svg>
        <input id="pmk_search" class="pmk-search" placeholder="Cari debitur, AO, kantor..." oninput="pmkDebouncedFetch()">
      </div>
    </div>

    <div id="pmk_panel_pipeline" class="flex-1 min-h-0 overflow-auto custom-scrollbar">
      <table class="pmk-table">
        <thead>
          <tr>
            <th class="pmk-sticky-name">Calon Debitur</th>
            <th>Rencana Plafon</th>
            <th>Tanggal Akuisisi</th>
            <th>Target Realisasi</th>
            <th>AO</th>
            <th>Kantor</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="pmk_pipeline_body"></tbody>
      </table>
    </div>

    <div id="pmk_panel_monitoring" class="hidden flex-1 min-h-0 overflow-auto custom-scrollbar">
      <table class="pmk-table">
        <thead>
          <tr>
            <th class="pmk-sticky-name">Calon Debitur</th>
            <th>Plafon / Target</th>
            <th>Status Minggu Lalu</th>
            <th>Action Minggu Ini</th>
            <th>Status Terkini</th>
            <th>Detail</th>
          </tr>
        </thead>
        <tbody id="pmk_monitoring_body"></tbody>
      </table>
    </div>

    <div class="px-3 md:px-4 py-2 border-t border-slate-100 bg-slate-50 flex items-center justify-between gap-2 shrink-0">
      <div id="pmk_page_info" class="text-[10px] md:text-xs text-slate-500 font-bold">-</div>
      <div class="flex gap-2">
        <button class="pmk-page-btn" onclick="pmkChangePage(-1)">Prev</button>
        <button class="pmk-page-btn" onclick="pmkChangePage(1)">Next</button>
      </div>
    </div>
  </div>
</div>

<div id="pmk_history_modal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-3 md:p-6">
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="pmkCloseHistory()"></div>
  <div class="relative bg-white w-full max-w-3xl max-h-[90vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col">
    <div class="px-5 py-4 border-b border-slate-100 flex items-start justify-between gap-3">
      <div>
        <p class="text-[10px] font-black tracking-[.18em] text-emerald-700 uppercase">Riwayat Kelolaan Pipeline</p>
        <h2 id="pmk_history_title" class="text-xl md:text-2xl font-black text-slate-900 mt-1">-</h2>
        <p id="pmk_history_subtitle" class="text-xs text-slate-500 font-semibold mt-1">-</p>
      </div>
      <button onclick="pmkCloseHistory()" class="h-9 w-9 rounded-full bg-slate-100 hover:bg-slate-200 font-black text-slate-600">&times;</button>
    </div>
    <div id="pmk_history_meta" class="grid grid-cols-3 gap-px bg-slate-100 mx-5 mt-4 rounded-xl overflow-hidden"></div>
    <div id="pmk_history_body" class="flex-1 overflow-auto custom-scrollbar p-5"></div>
  </div>
</div>

<style>
  .pmk-page{font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}
  .pmk-field{display:flex;flex-direction:column;gap:.25rem;min-width:8rem}
  .pmk-field label{font-size:.62rem;font-weight:900;letter-spacing:.09em;text-transform:uppercase;color:#64748b}
  .pmk-input{height:2.35rem;border:1px solid #cbd5e1;border-radius:.7rem;background:white;padding:0 .75rem;font-size:.8rem;font-weight:800;color:#0f172a;outline:none}
  .pmk-input:focus,.pmk-search:focus{border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.12)}
  .pmk-btn{height:2.35rem;border-radius:.7rem;padding:0 .85rem;display:inline-flex;align-items:center;justify-content:center;gap:.45rem;font-size:.72rem;font-weight:900;white-space:nowrap}
  .pmk-btn-primary{background:#059669;color:#fff}
  .pmk-btn-outline{background:#fff;color:#047857;border:1px solid #a7f3d0}
  .pmk-summary-wrap{display:flex;align-items:stretch;gap:.5rem}
  .pmk-icon-toggle{width:2.35rem;min-width:2.35rem;border:1px solid #dbe3ee;border-radius:.8rem;background:white;color:#059669;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 2px rgba(15,23,42,.04)}
  .pmk-icon-toggle:hover{background:#ecfdf5;border-color:#a7f3d0}
  .pmk-summary-wrap.is-collapsed #pmk_cards{display:none}
  .pmk-card{border:1px solid #e2e8f0;border-radius:.9rem;background:white;padding:.75rem .85rem;box-shadow:0 1px 2px rgba(15,23,42,.04);display:grid;grid-template-columns:auto 1fr;gap:.65rem;align-items:start}
  .pmk-card-icon{height:2.05rem;width:2.05rem;border-radius:.7rem;display:flex;align-items:center;justify-content:center;background:#f8fafc;color:#64748b}
  .pmk-card .lbl{font-size:.62rem;font-weight:950;letter-spacing:.08em;text-transform:uppercase;color:#64748b}
  .pmk-card .val{font-size:clamp(1rem,1.65vw,1.55rem);font-weight:950;line-height:1.05;color:#0f172a;margin-top:.25rem;white-space:nowrap}
  .pmk-card .sub{font-size:.72rem;font-weight:800;color:#64748b;margin-top:.35rem}
  .pmk-card.green{background:#ecfdf5;border-color:#bbf7d0}.pmk-card.green .val,.pmk-card.green .pmk-card-icon{color:#047857}.pmk-card.green .pmk-card-icon{background:#d1fae5}
  .pmk-card.amber{background:#fffbeb;border-color:#fde68a}.pmk-card.amber .val,.pmk-card.amber .pmk-card-icon{color:#a16207}.pmk-card.amber .pmk-card-icon{background:#fef3c7}
  .pmk-card.blue{background:#eff6ff;border-color:#bfdbfe}.pmk-card.blue .val,.pmk-card.blue .pmk-card-icon{color:#1d4ed8}.pmk-card.blue .pmk-card-icon{background:#dbeafe}
  .pmk-tab{height:2.6rem;padding:0 .35rem;border-bottom:2px solid transparent;display:inline-flex;align-items:center;gap:.45rem;font-size:.8rem;font-weight:950;color:#64748b;white-space:nowrap}
  .pmk-tab.active{border-color:#059669;color:#047857}
  .pmk-tab b{background:#ecfdf5;color:#047857;border-radius:999px;padding:.1rem .45rem;font-size:.68rem}
  .pmk-search{height:2.3rem;width:100%;border:1px solid #dbe3ee;border-radius:.7rem;padding:0 .75rem 0 2rem;font-size:.78rem;font-weight:700;outline:none}
  .pmk-table{width:max-content;min-width:100%;border-collapse:separate;border-spacing:0;table-layout:fixed}
  .pmk-table th{position:sticky;top:0;z-index:20;background:#f8fafc;border-bottom:1px solid #e2e8f0;color:#64748b;font-size:.68rem;font-weight:950;letter-spacing:.08em;text-transform:uppercase;text-align:left;padding:.85rem 1rem}
  .pmk-table td{border-bottom:1px solid #edf2f7;padding:.75rem 1rem;font-size:.82rem;font-weight:750;vertical-align:middle;background:white}
  .pmk-table tbody tr:hover td{background:#f8fafc}
  .pmk-sticky-name{position:sticky!important;left:0;z-index:25!important;width:18rem;min-width:18rem;box-shadow:8px 0 14px rgba(15,23,42,.06)}
  td.pmk-sticky-name{z-index:12!important}
  .pmk-name{font-weight:950;color:#0f172a;font-size:.92rem;line-height:1.15}
  .pmk-sub{font-size:.68rem;color:#64748b;font-weight:800;margin-top:.2rem}
  .pmk-badge{display:inline-flex;align-items:center;border-radius:999px;padding:.3rem .55rem;font-size:.68rem;font-weight:950}
  .pmk-badge.Prospek{background:#eef2ff;color:#4338ca}.pmk-badge.Analisa{background:#e0f2fe;color:#0369a1}.pmk-badge.Komite{background:#f3e8ff;color:#7e22ce}.pmk-badge.Akad{background:#fef3c7;color:#b45309}.pmk-badge.Realisasi{background:#dcfce7;color:#15803d}.pmk-badge.Tertunda{background:#fee2e2;color:#b91c1c}
  .pmk-select{height:2.2rem;min-width:10rem;border:1px solid #dbe3ee;border-radius:.6rem;padding:0 .6rem;background:white;font-size:.74rem;font-weight:800;outline:none}
  .pmk-action{min-width:15rem}
  .pmk-small-btn{height:2.2rem;border-radius:.6rem;padding:0 .75rem;background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;font-size:.7rem;font-weight:950}
  .pmk-page-btn{height:2rem;padding:0 .8rem;border-radius:.6rem;border:1px solid #cbd5e1;background:white;color:#475569;font-size:.72rem;font-weight:900}
  .pmk-timeline{position:relative;padding-left:2rem}
  .pmk-timeline:before{content:"";position:absolute;left:.65rem;top:.2rem;bottom:.2rem;width:1px;background:#dbe3ee}
  .pmk-dot{position:absolute;left:0;top:.2rem;width:1.35rem;height:1.35rem;border-radius:999px;background:#d1fae5;color:#047857;display:flex;align-items:center;justify-content:center;font-weight:950}
  @media (max-width: 768px){
    .pmk-page{height:auto;min-height:calc(100vh - 60px);overflow:auto}
    .pmk-summary-wrap{align-items:flex-start}
    .pmk-sticky-name{width:14rem;min-width:14rem}
    .pmk-table th,.pmk-table td{padding:.65rem .75rem}
  }
</style>

<script>
const PMK_API = './api/pipelane_monitoring_kredit/';
const PMK_KODE_API = './api/kode/';
const PMK_MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
let pmkState = {tab:'pipeline', page:1, limit:25, totalPages:1, searchTimer:null, showSummary:true};
let pmkOptions = {status:[], actions:[]};
let pmkRows = [];

const pmkFmt = new Intl.NumberFormat('id-ID');

function pmkFetch(url, options = {}) {
  return window.apiFetch ? window.apiFetch(url, options) : fetch(url, options);
}

function pmkPost(payload) {
  return pmkFetch(PMK_API, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)}).then(r => r.json());
}

function pmkUserKode() {
  const user = (window.getUser && window.getUser()) || {};
  const raw = user.kode || user.kode_kantor || user.kode_cabang || user.branch_code || '000';
  const kode = String(raw).replace(/\D/g,'').padStart(3,'0').slice(-3);
  return kode === '099' ? '000' : kode;
}

function pmkMoney(value) {
  const n = Number(value || 0);
  const abs = Math.abs(n);
  if (abs >= 1e12) return 'Rp ' + pmkFmt.format(Math.round(n / 1e10) / 100) + ' T';
  if (abs >= 1e9) return 'Rp ' + pmkFmt.format(Math.round(n / 1e7) / 100) + ' M';
  if (abs >= 1e6) return 'Rp ' + pmkFmt.format(Math.round(n / 1e4) / 100) + ' Jt';
  return 'Rp ' + pmkFmt.format(Math.round(n));
}

function pmkDate(value) {
  if (!value) return '-';
  const d = new Date(String(value).replace(' ', 'T'));
  if (Number.isNaN(d.getTime())) return value;
  return d.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
}

function pmkEscape(value) {
  return String(value ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

function pmkShowLoading(show) {
  const el = document.getElementById('pmk_loading');
  el.classList.toggle('hidden', !show);
  el.classList.toggle('flex', show);
}

function pmkToggleSummary() {
  pmkState.showSummary = !pmkState.showSummary;
  const wrap = document.querySelector('.pmk-summary-wrap');
  const btn = document.getElementById('pmk_summary_toggle');
  if (wrap) wrap.classList.toggle('is-collapsed', !pmkState.showSummary);
  if (btn) {
    btn.classList.toggle('bg-emerald-50', pmkState.showSummary);
    btn.setAttribute('aria-pressed', pmkState.showSummary ? 'true' : 'false');
  }
}

function pmkBadge(status) {
  const s = String(status || 'Prospek').trim();
  const cls = s.startsWith('Akad') ? 'Akad' : s.split(' ')[0];
  return `<span class="pmk-badge ${pmkEscape(cls)}">${pmkEscape(s)}</span>`;
}

function pmkInitPeriods() {
  const tahun = document.getElementById('pmk_tahun');
  const bulan = document.getElementById('pmk_bulan');
  const now = new Date();
  let hYear = '';
  for (let y = now.getFullYear() + 1; y >= now.getFullYear() - 4; y--) {
    hYear += `<option value="${y}" ${y === now.getFullYear() ? 'selected' : ''}>${y}</option>`;
  }
  tahun.innerHTML = hYear;
  bulan.innerHTML = PMK_MONTHS.map((m,i) => `<option value="${i+1}" ${i === now.getMonth() ? 'selected' : ''}>${m}</option>`).join('');
}

async function pmkLoadKantor() {
  const el = document.getElementById('pmk_kantor');
  const userKode = pmkUserKode();
  try {
    const res = await pmkFetch(PMK_KODE_API, {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'})});
    const json = await res.json();
    let html = '<option value="000">000 - Konsolidasi</option>';
    (json.data || []).filter(x => !['000','099'].includes(String(x.kode_kantor))).forEach(x => {
      html += `<option value="${pmkEscape(x.kode_kantor)}">${pmkEscape(x.kode_kantor)} - ${pmkEscape(x.nama_kantor || x.nama_cabang || '')}</option>`;
    });
    el.innerHTML = html;
    if (userKode !== '000') {
      el.value = userKode;
      el.disabled = true;
      el.classList.add('bg-slate-100');
    }
  } catch (e) {
    if (userKode !== '000') {
      el.innerHTML = `<option value="${userKode}">${userKode}</option>`;
      el.disabled = true;
    }
  }
}

async function pmkLoadOptions() {
  const json = await pmkPost({type:'options'});
  pmkOptions = json.data || {status:[], actions:[]};
}

function pmkPayload(type) {
  return {
    type,
    tahun: Number(document.getElementById('pmk_tahun').value),
    bulan: Number(document.getElementById('pmk_bulan').value),
    kode_kantor: document.getElementById('pmk_kantor').value || '000',
    search: document.getElementById('pmk_search').value.trim(),
    page: pmkState.page,
    limit: pmkState.limit
  };
}

async function pmkFetchAll() {
  pmkState.page = 1;
  await pmkFetchCurrent();
}

async function pmkFetchCurrent() {
  pmkShowLoading(true);
  try {
    const dashboard = await pmkPost({
      type:'dashboard',
      tahun: Number(document.getElementById('pmk_tahun').value),
      bulan: Number(document.getElementById('pmk_bulan').value),
      kode_kantor: document.getElementById('pmk_kantor').value || '000'
    });
    pmkRenderCards(dashboard.data || {});

    const type = pmkState.tab === 'pipeline' ? 'daftar_pipeline' : 'monitoring_mingguan';
    const json = await pmkPost(pmkPayload(type));
    const data = json.data || {};
    pmkRows = data.data || [];
    pmkState.totalPages = data.pagination?.total_pages || 1;
    const total = data.pagination?.total_records || 0;
    document.getElementById('pmk_count_' + pmkState.tab).textContent = total;
    document.getElementById('pmk_page_info').textContent = `Hal ${pmkState.page} / ${pmkState.totalPages} - ${pmkFmt.format(total)} data`;

    if (pmkState.tab === 'pipeline') pmkRenderPipeline(pmkRows);
    else pmkRenderMonitoring(pmkRows);
  } catch (e) {
    console.error(e);
    const body = pmkState.tab === 'pipeline' ? document.getElementById('pmk_pipeline_body') : document.getElementById('pmk_monitoring_body');
    body.innerHTML = `<tr><td colspan="7" class="text-center py-14 text-rose-600 font-black">Gagal memuat data pipeline.</td></tr>`;
  } finally {
    pmkShowLoading(false);
  }
}

function pmkRenderCards(data) {
  const s = data.summary || {};
  const p = data.pipeline?.pagination || {};
  const targetProduksi = Number(s.target_produksi_rbb || 0);
  const targetRunOff = Number(s.target_run_off_rbb || 0);
  const persenProduksi = Number(s.persen_produksi_rbb || 0);
  const persenRunOff = Number(s.persen_run_off_rbb || 0);
  const monthLabel = `${PMK_MONTHS[(Number(document.getElementById('pmk_bulan').value) || 1) - 1]} ${document.getElementById('pmk_tahun').value}`;
  const cards = [
    ['Target Produksi', monthLabel, pmkMoney(targetProduksi), 'blue', '<path d="M4 19V5"></path><path d="M4 19h16"></path><path d="M8 16v-5"></path><path d="M13 16V8"></path><path d="M18 16v-9"></path>'],
    ['Target Run Off', targetRunOff > 0 ? 'RBB kode 277' : 'RBB kode 277 kosong', targetRunOff > 0 ? pmkMoney(targetRunOff) : '-', 'amber', '<path d="M3 12h18"></path><path d="m8 7-5 5 5 5"></path><path d="m16 7 5 5-5 5"></path>'],
    ['Produksi Bulan Ini', `${pmkFmt.format(p.total_records || 0)} pipeline - ${pmkFmt.format(persenProduksi.toFixed(2))}% target`, pmkMoney(s.realisasi_bulan_ini || 0), 'green', '<path d="m7 17 10-10"></path><path d="M7 7h10v10"></path>'],
    ['Run Off Bulan Ini', `${pmkFmt.format(persenRunOff.toFixed(2))}% target - Growth ${pmkMoney(s.growth_bulan_ini || 0)}`, pmkMoney(s.run_off_bulan_ini || 0), 'amber', '<path d="m17 7-10 10"></path><path d="M7 7h10v10"></path>']
  ];
  document.getElementById('pmk_cards').innerHTML = cards.map(c => `
    <div class="pmk-card ${c[3]}">
      <div class="pmk-card-icon">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.35" viewBox="0 0 24 24">${c[4]}</svg>
      </div>
      <div class="min-w-0">
        <div class="lbl">${c[0]}</div>
        <div class="val">${c[2]}</div>
        <div class="sub truncate">${c[1]}</div>
      </div>
    </div>
  `).join('');
  const wrap = document.querySelector('.pmk-summary-wrap');
  if (wrap) wrap.classList.toggle('is-collapsed', !pmkState.showSummary);
}

function pmkRenderPipeline(rows) {
  const body = document.getElementById('pmk_pipeline_body');
  if (!rows.length) {
    body.innerHTML = `<tr><td colspan="7" class="text-center py-16 text-slate-400 font-bold italic">Tidak ada pipeline.</td></tr>`;
    return;
  }
  body.innerHTML = rows.map(r => `
    <tr>
      <td class="pmk-sticky-name">
        <div class="pmk-name">${pmkEscape(r.nama_debitur)}</div>
        <div class="pmk-sub">${pmkEscape(r.nama_ao || '-')} - ${pmkEscape(r.nama_kantor || '-')}</div>
      </td>
      <td class="font-black text-slate-900">${pmkMoney(r.rencana_plafon)}</td>
      <td>${pmkDate(r.tanggal_akuisisi)}</td>
      <td class="font-black">${pmkDate(r.target_realisasi)}</td>
      <td>${pmkEscape(r.nama_ao || '-')}</td>
      <td>${pmkEscape(r.nama_kantor || '-')}</td>
      <td>${pmkBadge(r.status_terkini)}</td>
    </tr>
  `).join('');
}

function pmkRenderMonitoring(rows) {
  const body = document.getElementById('pmk_monitoring_body');
  if (!rows.length) {
    body.innerHTML = `<tr><td colspan="6" class="text-center py-16 text-slate-400 font-bold italic">Tidak ada monitoring.</td></tr>`;
    return;
  }
  const statusOptions = pmkOptions.status.map(s => `<option value="${pmkEscape(s)}">${pmkEscape(s)}</option>`).join('');
  const actionOptions = pmkOptions.actions.map(a => `<option value="${pmkEscape(a)}">${pmkEscape(a)}</option>`).join('');
  body.innerHTML = rows.map(r => `
    <tr data-prospect="${pmkEscape(r.prospect_id)}">
      <td class="pmk-sticky-name">
        <div class="pmk-name">${pmkEscape(r.nama_debitur)}</div>
        <div class="pmk-sub">${pmkEscape(r.nama_ao || '-')} - ${pmkEscape(r.nama_kantor || '-')}</div>
      </td>
      <td>
        <div class="font-black text-slate-900">${pmkMoney(r.rencana_plafon)}</div>
        <div class="pmk-sub">Target ${pmkDate(r.target_realisasi)}</div>
      </td>
      <td>
        <select class="pmk-select pmk-prev">
          <option value="">-</option>${statusOptions}
        </select>
      </td>
      <td>
        <select class="pmk-select pmk-action">
          <option value="">Pilih action</option>${actionOptions}
        </select>
      </td>
      <td>
        <select class="pmk-select pmk-now">
          <option value="">-</option>${statusOptions}
        </select>
      </td>
      <td>
        <div class="flex gap-2">
          <button class="pmk-small-btn" onclick="pmkSaveRow(${Number(r.prospect_id)}, ${r.pipeline_id ? Number(r.pipeline_id) : 'null'}, '${pmkEscape(r.kode_kantor || '')}')">Simpan</button>
          <button class="pmk-small-btn" onclick="pmkOpenHistory(${Number(r.prospect_id)})">History</button>
        </div>
      </td>
    </tr>
  `).join('');

  rows.forEach(r => {
    const tr = body.querySelector(`tr[data-prospect="${Number(r.prospect_id)}"]`);
    const latest = (r.monitoring && r.monitoring[0]) || r;
    if (!tr) return;
    tr.querySelector('.pmk-prev').value = latest.status_minggu_lalu || '';
    tr.querySelector('.pmk-action').value = latest.action_minggu_ini || '';
    tr.querySelector('.pmk-now').value = latest.status_terkini || r.status_terkini || '';
  });
}

function pmkSetTab(tab) {
  pmkState.tab = tab;
  pmkState.page = 1;
  document.getElementById('pmk_tab_pipeline').classList.toggle('active', tab === 'pipeline');
  document.getElementById('pmk_tab_monitoring').classList.toggle('active', tab === 'monitoring');
  document.getElementById('pmk_panel_pipeline').classList.toggle('hidden', tab !== 'pipeline');
  document.getElementById('pmk_panel_monitoring').classList.toggle('hidden', tab !== 'monitoring');
  pmkFetchCurrent();
}

function pmkDebouncedFetch() {
  clearTimeout(pmkState.searchTimer);
  pmkState.searchTimer = setTimeout(() => {
    pmkState.page = 1;
    pmkFetchCurrent();
  }, 450);
}

function pmkChangePage(step) {
  const next = pmkState.page + step;
  if (next < 1 || next > pmkState.totalPages) return;
  pmkState.page = next;
  pmkFetchCurrent();
}

async function pmkSaveRow(prospectId, pipelineId, kodeKantor) {
  const tr = document.querySelector(`tr[data-prospect="${Number(prospectId)}"]`);
  if (!tr) return;
  const user = (window.getUser && window.getUser()) || {};
  const payload = {
    type:'save_monitoring',
    prospect_id: prospectId,
    pipeline_id: pipelineId,
    kode_kantor: kodeKantor || document.getElementById('pmk_kantor').value,
    tahun: Number(document.getElementById('pmk_tahun').value),
    bulan: Number(document.getElementById('pmk_bulan').value),
    minggu_ke: pmkWeekOfMonth(new Date()),
    status_minggu_lalu: tr.querySelector('.pmk-prev').value,
    action_minggu_ini: tr.querySelector('.pmk-action').value,
    status_terkini: tr.querySelector('.pmk-now').value,
    user: user.nama || user.nama_lengkap || user.employee_id || 'system'
  };
  const json = await pmkPost(payload);
  if (Number(json.status) === 200) {
    tr.classList.add('bg-emerald-50');
    setTimeout(() => tr.classList.remove('bg-emerald-50'), 700);
  } else {
    alert(json.message || 'Gagal menyimpan monitoring');
  }
}

function pmkWeekOfMonth(date) {
  return Math.max(1, Math.ceil(date.getDate() / 7));
}

async function pmkOpenHistory(prospectId) {
  const modal = document.getElementById('pmk_history_modal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  document.getElementById('pmk_history_body').innerHTML = '<div class="text-center py-12 font-bold text-slate-400">Memuat history...</div>';
  const json = await pmkPost({type:'history', prospect_id: prospectId});
  const data = json.data || {};
  const profile = data.profile || {};
  document.getElementById('pmk_history_title').textContent = profile.nama_debitur || '-';
  document.getElementById('pmk_history_subtitle').textContent = `${profile.nama_ao || '-'} - ${profile.nama_kantor || '-'}`;
  document.getElementById('pmk_history_meta').innerHTML = `
    <div class="bg-slate-50 p-3"><div class="pmk-sub">Rencana Plafon</div><div class="font-black">${pmkMoney(profile.rencana_plafon)}</div></div>
    <div class="bg-slate-50 p-3"><div class="pmk-sub">Target Realisasi</div><div class="font-black">${pmkDate(profile.target_realisasi)}</div></div>
    <div class="bg-slate-50 p-3"><div class="pmk-sub">Status</div><div>${pmkBadge(profile.status_terkini)}</div></div>
  `;
  const histories = [...(data.monitoring_history || []), ...(data.prospek_history || [])];
  if (!histories.length) {
    document.getElementById('pmk_history_body').innerHTML = '<div class="text-center py-12 font-bold text-slate-400">Belum ada history.</div>';
    return;
  }
  document.getElementById('pmk_history_body').innerHTML = histories.map(h => `
    <div class="pmk-timeline mb-5">
      <div class="pmk-dot">✓</div>
      <div class="text-[10px] font-black text-slate-400 tracking-widest uppercase">${pmkEscape(h.created_at || h.updated_at || h.tanggal || '-')}</div>
      <div class="font-black text-slate-900 mt-1">${pmkEscape(h.action_label || h.keterangan || h.action || h.status_baru || 'Update pipeline')}</div>
      <div class="text-xs font-semibold text-slate-500 mt-1">${pmkEscape(h.catatan || h.description || '')}</div>
      <div class="text-[11px] font-bold text-slate-400 mt-2">Oleh ${pmkEscape(h.created_by || h.user || '-')}</div>
    </div>
  `).join('');
}

function pmkCloseHistory() {
  const modal = document.getElementById('pmk_history_modal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
}

function pmkExportActive() {
  if (!pmkRows.length) return alert('Data kosong');
  const rows = pmkRows.map(r => [
    r.nama_debitur || '',
    r.rencana_plafon || 0,
    r.tanggal_akuisisi || '',
    r.target_realisasi || '',
    r.nama_ao || '',
    r.nama_kantor || '',
    r.status_terkini || ''
  ]);
  let csv = 'Calon Debitur\tRencana Plafon\tTanggal Akuisisi\tTarget Realisasi\tAO\tKantor\tStatus\n';
  csv += rows.map(row => row.join('\t')).join('\n');
  const blob = new Blob([csv], {type:'application/vnd.ms-excel'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Monitoring_Pipeline_Kredit_${document.getElementById('pmk_tahun').value}_${document.getElementById('pmk_bulan').value}.xls`;
  a.click();
  URL.revokeObjectURL(url);
}

document.addEventListener('DOMContentLoaded', async () => {
  pmkInitPeriods();
  await pmkLoadKantor();
  await pmkLoadOptions();
  pmkFetchAll();
});
</script>

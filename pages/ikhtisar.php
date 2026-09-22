<?php
// Ikhtisar RBB vs Realisasi Monbis V1.
?>

<div id="ikhtisarPage">
  <section class="ikhtisar-header">
    <div class="ikhtisar-heading">
      <div class="ikhtisar-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 19V5"/><path d="M4 19h16"/><path d="m7 15 3-4 3 2 5-6"/>
        </svg>
      </div>
      <div>
        <div class="ikhtisar-kicker">LAPORAN RBB</div>
        <h1>Ikhtisar</h1>
        <p>Perkembangan RBB dan realisasi indikator utama bisnis.</p>
      </div>
    </div>

    <form id="ikhtisarFilter" class="ikhtisar-filter" onsubmit="event.preventDefault(); fetchIkhtisar();">
      <label>
        <span>Actual Harian</span>
        <input id="ikhtisarDate" type="date" onclick="this.showPicker && this.showPicker()">
      </label>
      <label class="ikhtisar-office-field">
        <span>Area / Cabang</span>
        <select id="ikhtisarOffice"><option value="000">Konsolidasi</option></select>
      </label>
      <button class="ikhtisar-export" type="button" onclick="exportIkhtisarCsv()" title="Export Ikhtisar ke Excel" aria-label="Export Ikhtisar ke Excel">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>
      </button>
    </form>
  </section>

  <section class="ikhtisar-workspace">
    <div class="ikhtisar-tabs" role="tablist" aria-label="Jenis Ikhtisar">
      <button type="button" class="ikhtisar-tab active" id="ikhtisarTabSummaryButton" role="tab" aria-selected="true" aria-controls="ikhtisarTabSummary" data-ikhtisar-tab="summary">Ikhtisar</button>
      <button type="button" class="ikhtisar-tab" id="ikhtisarTabDamasButton" role="tab" aria-selected="false" aria-controls="ikhtisarTabDetail" data-ikhtisar-tab="damas">Damas</button>
      <button type="button" class="ikhtisar-tab" id="ikhtisarTabKreditButton" role="tab" aria-selected="false" aria-controls="ikhtisarTabDetail" data-ikhtisar-tab="credit">Kredit</button>
    </div>
    <div class="ikhtisar-toolbar">
      <div>
        <div class="ikhtisar-toolbar-title" id="ikhtisarToolbarTitle">Perkembangan Ikhtisar</div>
        <div class="ikhtisar-toolbar-note">Sumber target: <b>ref_rbb · kategori IKHTISAR</b></div>
      </div>
      <div class="ikhtisar-meta">
        <span id="ikhtisarScope">KONSOLIDASI</span>
        <span id="ikhtisarStatus" class="ikhtisar-status">Memuat...</span>
      </div>
    </div>

    <div id="ikhtisarLoading" class="ikhtisar-loading hidden">
      <span class="ikhtisar-spinner"></span>
      <span>Memuat ikhtisar...</span>
    </div>

    <div id="ikhtisarTabSummary" class="ikhtisar-tab-panel active" role="tabpanel" aria-labelledby="ikhtisarTabSummaryButton">
    <div class="ikhtisar-table-shell">
      <table id="ikhtisarTable">
        <colgroup>
          <col class="ikhtisar-col-name">
          <col class="ikhtisar-col-money"><col class="ikhtisar-col-money">
          <col class="ikhtisar-col-money"><col class="ikhtisar-col-percent"><col class="ikhtisar-col-percent">
        </colgroup>
        <thead>
          <tr>
            <th rowspan="2">PERKEMBANGAN</th>
            <th colspan="2" id="ikhtisarPeriodTitle">PERIODE</th>
            <th rowspan="1" id="ikhtisarYearTitle">DESEMBER</th>
            <th rowspan="2">%</th>
            <th rowspan="2" id="ikhtisarYearAchievementTitle">% DES 2026</th>
          </tr>
          <tr>
            <th>RBB</th>
            <th>REALISASI</th>
            <th>RBB</th>
          </tr>
          <tr class="ikhtisar-formula-row">
            <th></th><th>4</th><th>5</th><th>6</th><th>6 = 5 : 4</th><th>7 = 5 : 6</th>
          </tr>
        </thead>
        <tbody id="ikhtisarBody"></tbody>
      </table>
    </div>
    </div>

    <div id="ikhtisarTabDetail" class="ikhtisar-tab-panel" role="tabpanel" aria-labelledby="ikhtisarTabDamasButton" hidden>
      <div class="ikhtisar-table-shell">
        <table id="ikhtisarDetailTable">
          <colgroup>
            <col class="ikhtisar-col-name">
            <col class="ikhtisar-col-money"><col class="ikhtisar-col-money">
            <col class="ikhtisar-col-money"><col class="ikhtisar-col-percent"><col class="ikhtisar-col-percent">
          </colgroup>
          <thead>
            <tr>
              <th rowspan="2">PERKEMBANGAN</th>
              <th colspan="2" id="ikhtisarDetailPeriodTitle">PERIODE</th>
              <th rowspan="1" id="ikhtisarDetailYearTitle">DESEMBER</th>
              <th rowspan="2">%</th>
              <th rowspan="2" id="ikhtisarDetailYearAchievementTitle">% DES 2026</th>
            </tr>
            <tr>
              <th>RBB</th>
              <th>REALISASI</th>
              <th>RBB</th>
            </tr>
            <tr class="ikhtisar-formula-row">
              <th></th><th>4</th><th>5</th><th>6</th><th>6 = 5 : 4</th><th>7 = 5 : 6</th>
            </tr>
          </thead>
          <tbody id="ikhtisarDetailBody"></tbody>
        </table>
      </div>
    </div>

    <div class="ikhtisar-footnote">
      <span><b>Catatan:</b> nominal ditampilkan dalam rupiah, rasio dalam persen.</span>
      <span id="ikhtisarLoadedAt">-</span>
    </div>
  </section>
</div>

<style>
  #ikhtisarPage {
    --ik-blue:#0f3c5b;
    --ik-blue-2:#1f6b86;
    --ik-teal:#2e98ad;
    --ik-line:#d9e2ea;
    width:100%;
    min-height:calc(100vh - 62px);
    padding:14px;
    background:#f6f9fb;
    color:#18324a;
    font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;
  }
  .ikhtisar-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:13px 15px;border:1px solid #d7e2ec;border-radius:16px;background:#fff;box-shadow:0 4px 16px rgba(15,60,91,.06)}
  .ikhtisar-heading{display:flex;align-items:center;gap:12px;min-width:0}.ikhtisar-icon{display:grid;place-items:center;width:42px;height:42px;flex:0 0 auto;border-radius:11px;background:#2563eb;color:#fff;box-shadow:0 7px 15px rgba(37,99,235,.18)}.ikhtisar-icon svg{width:22px;height:22px}.ikhtisar-kicker{margin-bottom:2px;color:#2e7f9c;font-size:9px;font-weight:950;letter-spacing:.13em}.ikhtisar-heading h1{margin:0;color:#102f4b;font-size:21px;line-height:1.15;font-weight:950;letter-spacing:-.03em}.ikhtisar-heading p{margin:3px 0 0;color:#708397;font-size:10px;font-weight:650}.ikhtisar-filter{display:flex;align-items:end;gap:8px;flex:0 0 auto}.ikhtisar-filter label{display:flex;flex-direction:column;gap:4px;min-width:145px}.ikhtisar-filter label span{padding-left:2px;color:#49637a;font-size:8px;font-weight:950;letter-spacing:.07em;text-transform:uppercase}.ikhtisar-filter input,.ikhtisar-filter select{width:100%;height:36px;padding:0 10px;border:1px solid #c7d8e6;border-radius:9px;outline:0;background:#fff;color:#153956;font-size:10px;font-weight:850}.ikhtisar-filter input:focus,.ikhtisar-filter select:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1)}.ikhtisar-export{display:grid;place-items:center;width:40px;height:36px;border:0;border-radius:9px;background:#079669;color:#fff;cursor:pointer;box-shadow:0 5px 12px rgba(5,150,105,.18)}.ikhtisar-export:hover{background:#047857;transform:translateY(-1px)}.ikhtisar-export svg{width:18px;height:18px}
  .ikhtisar-workspace{position:relative;margin-top:12px;overflow:hidden;border:1px solid #d7e2ec;border-radius:16px;background:#fff;box-shadow:0 4px 16px rgba(15,60,91,.05)}.ikhtisar-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:13px 16px;border-bottom:1px solid #dce7ee;background:#fbfdfe}.ikhtisar-toolbar-title{color:#123c5b;font-size:13px;font-weight:950}.ikhtisar-toolbar-note{margin-top:2px;color:#7790a1;font-size:9px}.ikhtisar-toolbar-note b{color:#397d93}.ikhtisar-meta{display:flex;align-items:center;gap:7px;flex-wrap:wrap;justify-content:flex-end}.ikhtisar-meta>span{display:inline-flex;align-items:center;min-height:23px;padding:3px 8px;border:1px solid #dbe5ec;border-radius:999px;color:#587287;font-size:8px;font-weight:900;white-space:nowrap}.ikhtisar-status{background:#ecfdf5;border-color:#b7ead7!important;color:#047857!important}.ikhtisar-status.error{background:#fff1f2;border-color:#fecdd3!important;color:#be123c!important}.ikhtisar-loading{position:absolute;inset:54px 0 0;z-index:5;display:flex;align-items:center;justify-content:center;gap:9px;background:rgba(255,255,255,.86);color:#236681;font-size:10px;font-weight:900}.ikhtisar-loading.hidden{display:none}.ikhtisar-spinner{width:22px;height:22px;border:3px solid #c8e5eb;border-top-color:#1d7891;border-radius:50%;animation:ikSpin .75s linear infinite}@keyframes ikSpin{to{transform:rotate(360deg)}}
  .ikhtisar-table-shell{width:100%;overflow:auto}.ikhtisar-table-shell::-webkit-scrollbar{height:10px}.ikhtisar-table-shell::-webkit-scrollbar-thumb{background:#b7cbd6;border-radius:999px}.ikhtisar-table-shell::-webkit-scrollbar-track{background:#f4f8fa}#ikhtisarTable{width:100%;min-width:900px;border-collapse:separate;border-spacing:0;table-layout:fixed;color:#17344e}#ikhtisarTable .ikhtisar-col-name{width:31%}#ikhtisarTable .ikhtisar-col-money{width:15%}#ikhtisarTable .ikhtisar-col-percent{width:12%}#ikhtisarTable th,#ikhtisarTable td{border-right:1px solid #d3dfe6;border-bottom:1px solid #d3dfe6}#ikhtisarTable thead th{height:27px;padding:4px 8px;background:#3099ae;color:#fff;font-size:10px;line-height:1.1;font-weight:950;text-align:center;white-space:nowrap}#ikhtisarTable thead tr:nth-child(2) th{background:#dff2f4;color:#1c6177}#ikhtisarTable thead tr:nth-child(3) th{height:21px;padding:3px;background:#e7e8e9;color:#314b5e;font-size:9px;font-weight:800}#ikhtisarTable thead th:first-child{border-left:1px solid #d3dfe6}#ikhtisarTable tbody td{height:27px;padding:4px 10px;background:#fff;font-size:11px;line-height:1.05;vertical-align:middle}#ikhtisarTable tbody tr:nth-child(even) td{background:#fbfcfd}#ikhtisarTable tbody td:first-child{border-left:1px solid #d3dfe6}#ikhtisarTable tbody tr:hover td{background:#f1f8fa}.ikhtisar-name{font-weight:750;text-align:left}.ikhtisar-name.main{font-weight:950;color:#132b42}.ikhtisar-name.ratio{padding-left:24px;font-weight:650}.ikhtisar-name.subheading{font-weight:950;color:#1a2e40}.ikhtisar-code{display:inline-block;min-width:24px;margin-right:4px;color:#628096;font-size:9px;font-weight:900}.ikhtisar-value{text-align:right;font-variant-numeric:tabular-nums;font-family:"JetBrains Mono",ui-monospace,monospace;font-size:10px!important;font-weight:750}.ikhtisar-value.actual{color:#173f5a;font-weight:900}.ikhtisar-value.ratio{color:#1c6177}.ikhtisar-value.empty{color:#9cafbb}.ikhtisar-percent{text-align:right;font-variant-numeric:tabular-nums;font-family:"JetBrains Mono",ui-monospace,monospace;font-size:10px!important}.ikhtisar-total td{background:#eef8fa!important;font-weight:950;border-top:1px solid #acd7df}.ikhtisar-section td{background:#f7fafb!important;color:#17344e}.ikhtisar-section .ikhtisar-name{font-weight:950}.ikhtisar-footnote{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:8px 14px;color:#7890a0;font-size:9px}.ikhtisar-footnote b{color:#42657b}.ikhtisar-footnote span:last-child{white-space:nowrap}
  @media(max-width:900px){#ikhtisarPage{padding:10px}.ikhtisar-header{align-items:flex-start;flex-direction:column}.ikhtisar-filter{width:100%;display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr) 40px}.ikhtisar-filter label{min-width:0}.ikhtisar-toolbar{align-items:flex-start;flex-direction:column}.ikhtisar-meta{justify-content:flex-start}.ikhtisar-loading{inset:99px 0 0}}
  @media(max-width:560px){.ikhtisar-heading h1{font-size:19px}.ikhtisar-heading p{max-width:240px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.ikhtisar-filter{grid-template-columns:1fr 40px}.ikhtisar-office-field{grid-column:1/-1;grid-row:1}.ikhtisar-filter label:first-child{grid-column:1}.ikhtisar-export{grid-column:2;grid-row:2}.ikhtisar-toolbar{padding:11px}.ikhtisar-footnote{align-items:flex-start;flex-direction:column;gap:4px}}
  .ikhtisar-tabs{display:flex;gap:5px;padding:8px 12px 0;border-bottom:1px solid #dce7ee;background:#fbfdfe}.ikhtisar-tab{padding:8px 13px;border:1px solid transparent;border-radius:9px 9px 0 0;background:transparent;color:#6d8798;font-size:10px;font-weight:950;cursor:pointer}.ikhtisar-tab:hover{color:#1f6b86;background:#f0f8fa}.ikhtisar-tab.active{border-color:#cfe1e8;border-bottom-color:#fff;background:#fff;color:#0f3c5b}.ikhtisar-tab-panel[hidden]{display:none}.ikhtisar-tab-panel .ikhtisar-table-shell{border-top:0}#ikhtisarDetailTable{width:100%;min-width:900px;border-collapse:separate;border-spacing:0;table-layout:fixed;color:#17344e}#ikhtisarDetailTable .ikhtisar-col-name{width:31%}#ikhtisarDetailTable .ikhtisar-col-money{width:15%}#ikhtisarDetailTable .ikhtisar-col-percent{width:12%}#ikhtisarDetailTable th,#ikhtisarDetailTable td{border-right:1px solid #d3dfe6;border-bottom:1px solid #d3dfe6}#ikhtisarDetailTable thead th{height:27px;padding:4px 8px;background:#3099ae;color:#fff;font-size:10px;line-height:1.1;font-weight:950;text-align:center;white-space:nowrap}#ikhtisarDetailTable thead tr:nth-child(2) th{background:#dff2f4;color:#1c6177}#ikhtisarDetailTable thead tr:nth-child(3) th{height:21px;padding:3px;background:#e7e8e9;color:#314b5e;font-size:9px;font-weight:800}#ikhtisarDetailTable thead th:first-child,#ikhtisarDetailTable tbody td:first-child{border-left:1px solid #d3dfe6}#ikhtisarDetailTable tbody td{height:27px;padding:4px 10px;background:#fff;font-size:11px;line-height:1.05;vertical-align:middle}#ikhtisarDetailTable tbody tr:nth-child(even) td{background:#fbfcfd}#ikhtisarDetailTable tbody tr:hover td{background:#f1f8fa}.ikhtisar-detail-group td{background:#eef8fa!important;font-weight:950;border-top:1px solid #acd7df}.ikhtisar-detail-category td{background:#f7fafb!important;color:#17344e}.ikhtisar-detail-category .ikhtisar-name{font-weight:950}.ikhtisar-detail-child .ikhtisar-name{padding-left:24px}.ikhtisar-noa{color:#1c6177;font-size:10px!important;font-weight:800}
</style>

<script>
const IKHTISAR_CONFIGURED_BASE = <?= json_encode(defined('BASE_APP') ? BASE_APP : '') ?>;
function ikhtisarResolveBase() {
  const configured = String(IKHTISAR_CONFIGURED_BASE || '').replace(/\/+$/, '');
  if (configured) return configured;
  const origin = window.location.origin;
  const pathname = window.location.pathname.replace(/\/+$/, '');
  const routeMatch = pathname.match(/^(.*)\/ikhtisar(?:\/index\.php)?$/i);
  if (routeMatch) return origin + routeMatch[1];
  if (/\/index\.php$/i.test(pathname)) return origin + pathname.replace(/\/index\.php$/i, '');
  return origin;
}
const IKHTISAR_API_BASE = ikhtisarResolveBase();
const IKHTISAR_RBB_API = `${IKHTISAR_API_BASE}/api/rbb/`;
const IKHTISAR_LAPKEU_API = `${IKHTISAR_API_BASE}/api/lapkeu/`;
const ikhtisarMoney = new Intl.NumberFormat('id-ID', {maximumFractionDigits:0});
const ikhtisarDecimal = new Intl.NumberFormat('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2});
const ikhtisarRatioCodes = new Set(['10','11','12','13','15','16','17','18','19','20','21','22','23','24']);
const ikhtisarInverseAchievementCodes = new Set(['15','16']);
const ikhtisarSectionCodes = new Set(['9','14']);
const ikhtisarMainCodes = ['1','2','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24'];
const ikhtisarRbbCodeMap = {'1':'95','2':'105','5':'63','6':'196','7':'258','8':'261'};
let ikhtisarRows = [];
let ikhtisarActual = {};
let ikhtisarRbbSources = {periode:{}, year_end:{}};
let ikhtisarDetailActual = {damas:{}, credit:{}};
let ikhtisarActiveTab = 'summary';

function ikhtisarFetch(url, options = {}) { return window.apiFetch ? window.apiFetch(url, options) : fetch(url, options); }
async function ikhtisarJson(response, label) {
  const raw = await response.text();
  try { return JSON.parse(raw); }
  catch (error) {
    const preview = raw.replace(/\s+/g, ' ').trim().slice(0, 180);
    throw new Error(`${label} tidak mengembalikan JSON. Periksa route API/server rewrite. ${preview}`);
  }
}
function ikEsc(value) { return String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c])); }
function ikNum(value) { const n = Number(value); return Number.isFinite(n) ? n : null; }
function ikPath(obj, path, fallback = null) { return path.split('.').reduce((value, key) => value && value[key] !== undefined ? value[key] : null, obj) ?? fallback; }
function ikFormatDate(value) { if (!value) return '-'; const date = new Date(String(value).slice(0,10) + 'T00:00:00'); return Number.isNaN(date.getTime()) ? value : new Intl.DateTimeFormat('id-ID',{day:'2-digit',month:'long',year:'numeric'}).format(date); }
function ikMonthLabel(value) { if (!value) return 'PERIODE'; const date = new Date(String(value).slice(0,7) + '-01T00:00:00'); return Number.isNaN(date.getTime()) ? 'PERIODE' : new Intl.DateTimeFormat('id-ID',{month:'long',year:'numeric'}).format(date).toUpperCase(); }
function ikIsRatio(code) { return ikhtisarRatioCodes.has(String(code)); }
function ikFormat(value, ratio = false) { const n = ikNum(value); if (n === null) return '<span class="ikhtisar-value empty">-</span>'; return `<span class="ikhtisar-value${ratio ? ' ratio' : ''}">${ratio ? ikhtisarDecimal.format(n) + '%' : ikhtisarMoney.format(Math.round(n))}</span>`; }
function ikPercent(value) { const n = ikNum(value); return n === null ? '<span class="ikhtisar-value empty">-</span>' : `<span class="ikhtisar-percent">${ikhtisarDecimal.format(n)}%</span>`; }
function ikScopeLabel() { return document.getElementById('ikhtisarOffice')?.selectedOptions?.[0]?.textContent?.trim() || 'KONSOLIDASI'; }
function ikAchievement(actual, target, inverse = false) {
  const actualValue = ikNum(actual); const targetValue = ikNum(target);
  if (actualValue === null || targetValue === null || (inverse ? actualValue === 0 : targetValue === 0)) return null;
  return inverse ? targetValue / actualValue * 100 : actualValue / targetValue * 100;
}

function ikTargetValue(source, code) {
  if (!source || !Object.prototype.hasOwnProperty.call(source, String(code))) return null;
  return ikNum(source[String(code)]);
}

function ikDetailValue(path) { return ikPath(ikhtisarDetailActual, path, null); }

function ikDetailFormat(value, kind = 'money') {
  const n = ikNum(value);
  if (n === null) return '<span class="ikhtisar-value empty">-</span>';
  if (kind === 'noa') return `<span class="ikhtisar-value ikhtisar-noa">${ikhtisarMoney.format(Math.round(n))}</span>`;
  return ikFormat(n);
}

function ikhtisarDetailRows() {
  return [
    {label:'DAMAS', className:'ikhtisar-detail-group', targetCode:'25', path:'damas.total.rupiah', kind:'money'},
    {label:'a. Tabungan', className:'ikhtisar-detail-category'},
    {label:'(1) Rupiah', className:'ikhtisar-detail-child', targetCode:'27', path:'damas.tabungan.rupiah', kind:'money'},
    {label:'(2) Nasabah', className:'ikhtisar-detail-child', targetCode:'28', path:'damas.tabungan.noa', kind:'noa'},
    {label:'b. Deposito', className:'ikhtisar-detail-category'},
    {label:'(1) Rupiah', className:'ikhtisar-detail-child', targetCode:'29', path:'damas.deposito.rupiah', kind:'money'},
    {label:'(2) Nasabah', className:'ikhtisar-detail-child', targetCode:'30', path:'damas.deposito.noa', kind:'noa'},
    {label:'K R E D I T', className:'ikhtisar-detail-group', targetCode:'44', path:'credit.total.rupiah', kind:'money'},
    {label:'a. Lancar', className:'ikhtisar-detail-category'},
    {label:'(1) Rupiah', className:'ikhtisar-detail-child', targetCode:'46', path:'credit.statuses.L.rupiah', kind:'money'},
    {label:'(2) Nasabah', className:'ikhtisar-detail-child', targetCode:'47', path:'credit.statuses.L.noa', kind:'noa'},
    {label:'b. Dalam Perhatian', className:'ikhtisar-detail-category'},
    {label:'(1) Rupiah', className:'ikhtisar-detail-child', targetCode:'48', path:'credit.statuses.DP.rupiah', kind:'money'},
    {label:'(2) Nasabah', className:'ikhtisar-detail-child', targetCode:'49', path:'credit.statuses.DP.noa', kind:'noa'},
    {label:'c. Kurang Lancar', className:'ikhtisar-detail-category'},
    {label:'(1) Rupiah', className:'ikhtisar-detail-child', targetCode:'50', path:'credit.statuses.KL.rupiah', kind:'money'},
    {label:'(2) Nasabah', className:'ikhtisar-detail-child', targetCode:'51', path:'credit.statuses.KL.noa', kind:'noa'},
    {label:'d. Diragukan', className:'ikhtisar-detail-category'},
    {label:'(1) Rupiah', className:'ikhtisar-detail-child', targetCode:'52', path:'credit.statuses.D.rupiah', kind:'money'},
    {label:'(2) Nasabah', className:'ikhtisar-detail-child', targetCode:'53', path:'credit.statuses.D.noa', kind:'noa'},
    {label:'e. Macet', className:'ikhtisar-detail-category'},
    {label:'(1) Rupiah', className:'ikhtisar-detail-child', targetCode:'54', path:'credit.statuses.M.rupiah', kind:'money'},
    {label:'(2) Nasabah', className:'ikhtisar-detail-child', targetCode:'55', path:'credit.statuses.M.noa', kind:'noa'}
  ];
}

function renderIkhtisarDetail() {
  const body = document.getElementById('ikhtisarDetailBody');
  if (!body) return;
  const allRows = ikhtisarDetailRows();
  const rows = ikhtisarActiveTab === 'damas' ? allRows.slice(0, 7) : (ikhtisarActiveTab === 'credit' ? allRows.slice(7) : allRows);
  const periodSource = ikhtisarRbbSources.periode || {};
  const yearSource = ikhtisarRbbSources.year_end || {};
  body.innerHTML = rows.map(row => {
    const target = row.targetCode ? ikTargetValue(periodSource, row.targetCode) : null;
    const yearEnd = row.targetCode ? ikTargetValue(yearSource, row.targetCode) : null;
    const actual = row.path ? ikDetailValue(row.path) : null;
    const periodAchievement = target !== null && target !== 0 && ikNum(actual) !== null ? ikNum(actual) / target * 100 : null;
    const yearAchievement = yearEnd !== null && yearEnd !== 0 && ikNum(actual) !== null ? ikNum(actual) / yearEnd * 100 : null;
    const labelClass = row.className.includes('child') ? 'ratio' : (row.className.includes('category') ? 'subheading' : 'main');
    return `<tr class="${row.className}">
      <td class="ikhtisar-name ${labelClass}">${ikEsc(row.label)}</td>
      <td class="ikhtisar-value">${row.targetCode ? ikDetailFormat(target, row.kind) : ''}</td>
      <td class="ikhtisar-value actual">${row.path ? ikDetailFormat(actual, row.kind) : ''}</td>
      <td class="ikhtisar-value">${row.targetCode ? ikDetailFormat(yearEnd, row.kind) : ''}</td>
      <td class="ikhtisar-percent">${row.targetCode ? ikPercent(periodAchievement) : ''}</td>
      <td class="ikhtisar-percent">${row.targetCode ? ikPercent(yearAchievement) : ''}</td>
    </tr>`;
  }).join('');
}

function setIkhtisarTab(tab) {
  ikhtisarActiveTab = ['damas', 'credit'].includes(tab) ? tab : 'summary';
  const isSummary = ikhtisarActiveTab === 'summary';
  document.querySelectorAll('[data-ikhtisar-tab]').forEach(button => {
    const active = button.dataset.ikhtisarTab === ikhtisarActiveTab;
    button.classList.toggle('active', active);
    button.setAttribute('aria-selected', active ? 'true' : 'false');
  });
  const summary = document.getElementById('ikhtisarTabSummary');
  const detail = document.getElementById('ikhtisarTabDetail');
  if (summary) { summary.classList.toggle('active', isSummary); summary.hidden = !isSummary; }
  if (detail) { detail.classList.toggle('active', !isSummary); detail.hidden = isSummary; }
  const title = document.getElementById('ikhtisarToolbarTitle');
  if (title) title.textContent = isSummary ? 'Perkembangan Ikhtisar' : (ikhtisarActiveTab === 'damas' ? 'Damas & Perkembangan DPK' : 'Kredit & Kolektibilitas');
  if (!isSummary) renderIkhtisarDetail();
}

function ikSource(source, code) {
  const value = ikNum(source?.[String(code)]);
  return value === null ? 0 : value;
}

function ikMappedSource(source, ikhtisarCode) {
  const mappedCode = ikhtisarRbbCodeMap[String(ikhtisarCode)];
  if (mappedCode && source && Object.prototype.hasOwnProperty.call(source, mappedCode)) return ikSource(source, mappedCode);
  return ikSource(source, ikhtisarCode);
}

function ikDerivedRbbTargets(source, monthNumber) {
  const asset = ikMappedSource(source, 1);
  const dpk = ikSource(source, 105) || ikSource(source, 25) || ikMappedSource(source, 2);
  const credit = ikSource(source, 63) || ikSource(source, 44) || ikMappedSource(source, 5);
  const npl = ikSource(source, 56);
  const ckpnCredit = Math.abs(ikSource(source, 70));
  const asetProduktif = ikSource(source, 61) + ikSource(source, 64) + ikSource(source, 65);
  const income = ikMappedSource(source, 6);
  const expense = ikMappedSource(source, 7);
  const laba = ikMappedSource(source, 8) || (income - expense);
  const pendapatanBunga = ikSource(source, 163);
  const bebanBunga = ikSource(source, 198);
  const currentMonth = Math.max(1, Number(monthNumber) || 1);
  const derived = {};
  const setRatio = (code, numerator, denominator, annualized = false) => {
    if (denominator === 0) return;
    let value = numerator / denominator * 100;
    if (annualized) value *= 12 / currentMonth;
    if (Number.isFinite(value)) derived[String(code)] = value;
  };

  // Nominal turunan.
  if (ikMappedSource(source, 8) === 0 && (income !== 0 || expense !== 0)) derived['8'] = income - expense;

  // Rasio yang dapat diturunkan dari nilai RBB detail.
  setRatio('12', npl, credit);
  setRatio('13', ckpnCredit, npl);
  setRatio('15', npl, credit);
  setRatio('16', npl - ckpnCredit, credit);
  setRatio('17', credit, asetProduktif);
  setRatio('18', laba, asset, true);
  setRatio('19', pendapatanBunga - bebanBunga, asetProduktif, true);
  setRatio('20', expense, income);
  setRatio('21', ikSource(source, 57) + ikSource(source, 61), ikSource(source, 96) + dpk + ikSource(source, 120) + ikSource(source, 110) + ikSource(source, 117));
  setRatio('22', credit, dpk);
  setRatio('23', ikSource(source, 31), credit);
  setRatio('24', ikSource(source, 106) || ikSource(source, 27), dpk);
  return derived;
}

function ikEffectiveTarget(row, source, monthNumber) {
  const code = String(row.kode_monbis);
  const mappedCode = ikhtisarRbbCodeMap[code] || code;
  const sourceHasCode = source && Object.prototype.hasOwnProperty.call(source, mappedCode);
  const direct = sourceHasCode ? ikNum(source[mappedCode]) : (row.has_target ? ikNum(row.target_rbb) : null);
  if (direct !== null && Math.abs(direct) > 0.000001) return {value:direct, calculated:false};
  const derived = ikDerivedRbbTargets(source, monthNumber)[code];
  return {value:ikNum(derived), calculated:derived !== undefined};
}

function ikUserKode() {
  const user = (window.getUser && window.getUser()) || {};
  const raw = user.kode || user.kode_kantor || user.kode_cabang || user.branch_code || '000';
  const kode = String(raw).replace(/\D/g,'').padStart(3,'0').slice(-3);
  return kode === '099' ? '000' : kode;
}

async function loadIkhtisarDate() {
  const input = document.getElementById('ikhtisarDate');
  try { const res = await ikhtisarFetch('./api/date/', {cache:'no-store'}); const json = await res.json(); const data = json.data || json || {}; input.value = data.last_created || data.harian_date || new Date().toISOString().slice(0,10); }
  catch (e) { input.value = new Date().toISOString().slice(0,10); }
}

async function loadIkhtisarOffice() {
  const select = document.getElementById('ikhtisarOffice');
  let html = '<option value="000">Konsolidasi</option><option value="SEMARANG">Korwil Semarang</option><option value="SOLO">Korwil Solo</option><option value="BANYUMAS">Korwil Banyumas</option><option value="PEKALONGAN">Korwil Pekalongan</option>';
  try {
    const res = await ikhtisarFetch('./api/kode/', {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type:'kode_kantor'})});
    const json = await res.json();
    (json.data || []).filter(item => item.kode_kantor && item.kode_kantor !== '000').sort((a,b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor))).forEach(item => { const kode = String(item.kode_kantor).padStart(3,'0'); html += `<option value="${ikEsc(kode)}">${ikEsc(kode)} - ${ikEsc(item.nama_kantor || '')}</option>`; });
  } catch (e) {}
  select.innerHTML = html;
  const userKode = ikUserKode();
  if (userKode !== '000') { select.value = userKode; select.disabled = true; }
}

function ikScopePayload(type, dateOverride = null) {
  const office = document.getElementById('ikhtisarOffice').value;
  const payload = {type, harian_date:dateOverride || document.getElementById('ikhtisarDate').value};
  if (['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].includes(office)) payload.korwil = office;
  else payload.kode_kantor = office;
  return payload;
}

function ikShiftDate(value, days) {
  const date = new Date(String(value).slice(0, 10) + 'T00:00:00');
  if (Number.isNaN(date.getTime())) return value;
  date.setDate(date.getDate() - days);
  return date.toISOString().slice(0, 10);
}

function ikActualHasData(data) {
  const makro = data?.makro || {};
  return ['aset', 'dpk', 'pendapatan', 'biaya', 'laba_rugi'].some(key => {
    const value = ikNum(makro[key]?.nominal_aktual);
    return value !== null && Math.abs(value) > 0.000001;
  });
}

function buildIkhtisarActual(data) {
  const detail = data.ringkasan_detail || {};
  const makro = data.makro || {};
  const kredit = detail.kredit_diberikan || {};
  const rasioUtama = detail.rasio_utama || {};
  const rasio = data.kesehatan_rasio || {};
  const detailNominal = ikPath(data, 'rasio.detail_nominal', {}) || {};
  const bakiDebet = ikNum(kredit.baki_debet) ?? 0;
  const saldoBank = ikNum(kredit.saldo_bank_ead) ?? 0;
  const asetProduktif = ikNum(detailNominal.rata_aset_produktif) ?? 0;
  const actual = {
    '1': ikPath(makro, 'aset.nominal_aktual'),
    '2': detail.dana_masyarakat?.total ?? ikPath(makro, 'dpk.nominal_aktual'),
    '5': bakiDebet + saldoBank,
    '6': ikPath(makro, 'pendapatan.nominal_aktual'),
    '7': ikPath(makro, 'biaya.nominal_aktual'),
    '8': detail.laba_sebelum_pajak ?? ikPath(makro, 'laba_rugi.nominal_aktual'),
    '12': rasioUtama.kap,
    '13': rasioUtama.ckpn_terhadap_ppka,
    '15': rasioUtama.npl_baki_debet_gross,
    '16': rasioUtama.npl_baki_debet_netto,
    '17': asetProduktif > 0 ? (bakiDebet / asetProduktif) * 100 : null,
    '18': ikPath(rasio, 'roa.persen_aktual'),
    '19': ikPath(rasio, 'nim.persen_aktual'),
    '20': ikPath(rasio, 'bopo.persen_aktual'),
    '21': ikPath(rasio, 'cash.persen_aktual'),
    '22': ikPath(rasio, 'ldr.persen_aktual'),
    '24': ikPath(rasio, 'casa.persen_aktual')
  };
  return actual;
}

function renderIkhtisar() {
  const map = Object.fromEntries(ikhtisarRows.map(row => [String(row.kode_monbis), row]));
  const body = document.getElementById('ikhtisarBody');
  const candidates = ikhtisarMainCodes.map(code => map[code]).filter(Boolean);
  const visibleCodes = new Set(candidates.filter(row => {
    const code = String(row.kode_monbis);
    if (ikhtisarSectionCodes.has(code)) return false;
    const actual = ikNum(ikhtisarActual[code]);
    return actual !== null && Math.abs(actual) > 0.000001;
  }).map(row => String(row.kode_monbis)));
  if (candidates.some(row => visibleCodes.has(String(row.kode_monbis)) && ['10','11','12','13','15','16','17','18','19','20','21','22','23','24'].includes(String(row.kode_monbis)))) visibleCodes.add('9');
  if (candidates.some(row => visibleCodes.has(String(row.kode_monbis)) && ['15','16'].includes(String(row.kode_monbis)))) visibleCodes.add('14');
  const rows = candidates.filter(row => visibleCodes.has(String(row.kode_monbis)));
  if (!rows.length) { body.innerHTML = '<tr><td colspan="6" class="ikhtisar-empty">Data Ikhtisar belum tersedia.</td></tr>'; return; }
  body.innerHTML = rows.map(row => {
    const code = String(row.kode_monbis);
    const ratio = ikIsRatio(code);
    const section = ikhtisarSectionCodes.has(code);
    const periodDate = document.getElementById('ikhtisarDate').value;
    const monthNumber = Number(String(periodDate).slice(5,7)) || 1;
    const targetInfo = ikEffectiveTarget(row, ikhtisarRbbSources.periode, monthNumber);
    const yearTargetInfo = ikEffectiveTarget(row, ikhtisarRbbSources.year_end, 12);
    const target = targetInfo.value;
    const actual = ikNum(ikhtisarActual[code]);
    const yearEnd = yearTargetInfo.value;
    const inverseAchievement = ikhtisarInverseAchievementCodes.has(code);
    const periodAchievement = ikAchievement(actual, target, inverseAchievement);
    const yearAchievement = ikAchievement(actual, yearEnd, inverseAchievement);
    const className = section ? 'ikhtisar-section' : (['1','2','5','6','7','8'].includes(code) ? 'ikhtisar-total' : '');
    const labelClass = section ? 'subheading' : (ratio ? 'ratio' : 'main');
    return `<tr class="${className}">
      <td class="ikhtisar-name ${labelClass}"><span class="ikhtisar-code">${ikEsc(row.keterangan || '')}</span></td>
      <td class="ikhtisar-value">${section ? '' : ikFormat(target, ratio)}</td>
      <td class="ikhtisar-value actual">${section ? '' : ikFormat(actual, ratio)}</td>
      <td class="ikhtisar-value">${section ? '' : ikFormat(yearEnd, ratio)}</td>
      <td class="ikhtisar-percent">${section ? '' : ikPercent(periodAchievement)}</td>
      <td class="ikhtisar-percent">${section ? '' : ikPercent(yearAchievement)}</td>
    </tr>`;
  }).join('');
}

async function fetchIkhtisar() {
  const loader = document.getElementById('ikhtisarLoading');
  const status = document.getElementById('ikhtisarStatus');
  loader.classList.remove('hidden'); status.classList.remove('error'); status.textContent = 'Memuat...';
  try {
    const requestedDate = document.getElementById('ikhtisarDate').value;
    let actualDate = requestedDate;
    let [rbbRes, actualRes] = await Promise.all([
      ikhtisarFetch(IKHTISAR_RBB_API, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(ikScopePayload('ikhtisar_rbb', requestedDate))}),
      ikhtisarFetch(IKHTISAR_LAPKEU_API, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(ikScopePayload('tv_makro_summary', requestedDate))})
    ]);
    let rbbJson = await ikhtisarJson(rbbRes, 'API RBB'); let actualJson = await ikhtisarJson(actualRes, 'API Lapkeu');
    if (!rbbRes.ok || rbbJson.status === false) throw new Error(rbbJson.message || 'Gagal memuat target RBB');
    if (!actualRes.ok || actualJson.status === false) throw new Error(actualJson.message || 'Gagal memuat realisasi');

    // Snapshot nominatif/lapkeu bisa terlambat satu hari dari tanggal terakhir.
    // Gunakan snapshot aktual terakhir agar tabel tidak kosong hanya karena tanggal.
    if (!ikActualHasData(actualJson.data || {})) {
      for (let offset = 1; offset <= 7; offset += 1) {
        const candidateDate = ikShiftDate(requestedDate, offset);
        try {
          const candidateRes = await ikhtisarFetch(IKHTISAR_LAPKEU_API, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(ikScopePayload('tv_makro_summary', candidateDate))});
          const candidateJson = await ikhtisarJson(candidateRes, 'API Lapkeu');
          if (!candidateRes.ok || candidateJson.status === false || !ikActualHasData(candidateJson.data || {})) continue;
          actualJson = candidateJson;
          actualDate = candidateDate;

          // Detail nominatif mengikuti tanggal aktual yang berhasil ditemukan.
          try {
            const fallbackRbbRes = await ikhtisarFetch(IKHTISAR_RBB_API, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(ikScopePayload('ikhtisar_rbb', candidateDate))});
            const fallbackRbbJson = await ikhtisarJson(fallbackRbbRes, 'API RBB');
            if (fallbackRbbRes.ok && fallbackRbbJson.status !== false) rbbJson = fallbackRbbJson;
          } catch (fallbackError) {}
          break;
        } catch (candidateError) {}
      }
    }
    const rbbData = rbbJson.data || {}; const actualData = actualJson.data || {};
    ikhtisarRows = Array.isArray(rbbData.data) ? rbbData.data : [];
    ikhtisarRbbSources = rbbData.rbb_sources || {periode:{}, year_end:{}};
    ikhtisarDetailActual = rbbData.detail_actual || {damas:{}, credit:{}};
    ikhtisarActual = buildIkhtisarActual(actualData);
    const meta = rbbData.meta || {};
    document.getElementById('ikhtisarPeriodTitle').textContent = ikMonthLabel(meta.periode_rbb || document.getElementById('ikhtisarDate').value);
    document.getElementById('ikhtisarYearTitle').textContent = ikMonthLabel(meta.periode_rbb_year_end || `${String(document.getElementById('ikhtisarDate').value).slice(0,4)}-12`);
    document.getElementById('ikhtisarDetailPeriodTitle').textContent = ikMonthLabel(meta.periode_rbb || document.getElementById('ikhtisarDate').value);
    document.getElementById('ikhtisarDetailYearTitle').textContent = ikMonthLabel(meta.periode_rbb_year_end || `${String(document.getElementById('ikhtisarDate').value).slice(0,4)}-12`);
    const yearAchievementLabel = `% DES ${String(meta.periode_rbb_year_end || document.getElementById('ikhtisarDate').value).slice(0,4)}`;
    document.getElementById('ikhtisarYearAchievementTitle').textContent = yearAchievementLabel;
    document.getElementById('ikhtisarDetailYearAchievementTitle').textContent = yearAchievementLabel;
    document.getElementById('ikhtisarScope').textContent = meta.scope || ikScopeLabel();
    document.getElementById('ikhtisarLoadedAt').textContent = `Posisi actual: ${ikFormatDate(actualDate)}${actualDate !== requestedDate ? ' (snapshot terakhir)' : ''}`;
    renderIkhtisar();
    renderIkhtisarDetail();
    status.textContent = `${ikhtisarRows.length} mapping aktif`;
  } catch (error) {
    document.getElementById('ikhtisarBody').innerHTML = `<tr><td colspan="6" class="ikhtisar-empty">${ikEsc(error.message || 'Data belum dapat dimuat.')}</td></tr>`;
    document.getElementById('ikhtisarDetailBody').innerHTML = `<tr><td colspan="6" class="ikhtisar-empty">${ikEsc(error.message || 'Data belum dapat dimuat.')}</td></tr>`;
    status.classList.add('error'); status.textContent = 'Gagal memuat';
  } finally { loader.classList.add('hidden'); }
}

function exportIkhtisarCsv() {
  const table = document.getElementById(ikhtisarActiveTab === 'summary' ? 'ikhtisarTable' : 'ikhtisarDetailTable');
  const rows = [...table.querySelectorAll('tr')].map(row => [...row.children].map(cell => `"${String(cell.textContent || '').replace(/"/g,'""').trim()}"`).join(','));
  const blob = new Blob(["\ufeff" + rows.join('\n')], {type:'text/csv;charset=utf-8;'});
  const url = URL.createObjectURL(blob); const link = document.createElement('a');
  link.href = url; link.download = `ikhtisar-${ikhtisarActiveTab}-${document.getElementById('ikhtisarDate').value || 'export'}.csv`; link.click(); URL.revokeObjectURL(url);
}

document.addEventListener('DOMContentLoaded', async () => {
  await Promise.all([loadIkhtisarDate(), loadIkhtisarOffice()]);
  document.querySelectorAll('[data-ikhtisar-tab]').forEach(button => button.addEventListener('click', () => setIkhtisarTab(button.dataset.ikhtisarTab)));
  document.getElementById('ikhtisarDate')?.addEventListener('change', fetchIkhtisar);
  document.getElementById('ikhtisarOffice')?.addEventListener('change', fetchIkhtisar);
  fetchIkhtisar();
});
</script>

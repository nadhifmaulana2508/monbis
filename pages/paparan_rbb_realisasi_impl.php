<?php
/*
 * Paparan RBB vs Realisasi untuk direksi.
 * Target berasal dari tabel RBB sistem, realisasi berasal dari snapshot
 * yang sama dengan menu Ikhtisar. Pengaturan tab disimpan di browser
 * agar operator dapat menyiapkan urutan paparan tanpa mengubah menu utama.
 */
?>
<div id="paparanRbbRealisasi" class="prr-page">
  <header class="prr-header">
    <div class="prr-brand">
      <div class="prr-brand-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="m7 15 3-4 3 2 5-6"/></svg>
      </div>
      <div>
        <div class="prr-kicker">PAPARAN DIREKSI · RBB</div>
        <h1>Realisasi RBB</h1>
        <p>Target RBB sistem dibandingkan dengan realisasi pada closing yang dipilih.</p>
      </div>
    </div>
    <div class="prr-controls">
      <label><span>CLOSING</span><input id="prrClosing" type="date" aria-label="Tanggal closing"></label>
      <button id="prrReload" type="button" class="prr-btn prr-btn-primary">Tampilkan</button>
      <button id="prrSettingsOpen" type="button" class="prr-btn prr-btn-light" title="Atur tab paparan">⚙ Atur tab</button>
    </div>
  </header>

  <section class="prr-workspace">
    <div class="prr-note">
      <span><b>SUMBER:</b> RBB sistem + realisasi Ikhtisar</span>
      <span id="prrPosition">Closing: -</span>
      <span id="prrStatus" class="prr-status">Menyiapkan data...</span>
    </div>
    <nav id="prrTabs" class="prr-tabs" role="tablist" aria-label="Tab paparan RBB"></nav>
    <main id="prrPanels" class="prr-panels">
      <div class="prr-loading"><span class="prr-spinner"></span><span>Memuat data RBB sistem...</span></div>
    </main>
    <footer class="prr-footnote">
      <span>Klik kartu nominal untuk melihat history realisasi dari <b>acc_history</b>.</span>
      <span id="prrLoadedAt">-</span>
    </footer>
  </section>
</div>

<div id="prrSettingsModal" class="prr-modal" hidden role="dialog" aria-modal="true" aria-labelledby="prrSettingsTitle">
  <div class="prr-dialog prr-settings-dialog">
    <div class="prr-dialog-head">
      <div><div class="prr-kicker">KONFIGURASI PAPARAN</div><h2 id="prrSettingsTitle">Pilih tab yang ditampilkan</h2><p>Pengaturan ini tersimpan di browser operator ini.</p></div>
      <button type="button" class="prr-close" data-prr-close="settings" aria-label="Tutup">×</button>
    </div>
    <div id="prrSettingsBody" class="prr-settings-body"></div>
    <div class="prr-dialog-actions">
      <button type="button" id="prrSettingsAll" class="prr-btn prr-btn-light">Pilih semua</button>
      <button type="button" id="prrSettingsReset" class="prr-btn prr-btn-light">Kembalikan awal</button>
      <button type="button" id="prrSettingsSave" class="prr-btn prr-btn-primary">Simpan pilihan</button>
    </div>
  </div>
</div>

<div id="prrHistoryModal" class="prr-modal" hidden role="dialog" aria-modal="true" aria-labelledby="prrHistoryTitle">
  <div class="prr-dialog prr-history-dialog">
    <div class="prr-dialog-head">
      <div><div class="prr-kicker">HISTORY REALISASI · ACC_HISTORY</div><h2 id="prrHistoryTitle">History Realisasi</h2><p id="prrHistorySubtitle">Snapshot closing per bulan.</p></div>
      <button type="button" class="prr-close" data-prr-close="history" aria-label="Tutup">×</button>
    </div>
    <div id="prrHistoryBody" class="prr-history-body"><div class="prr-empty">Pilih kartu untuk memuat history.</div></div>
  </div>
</div>

<style>
  #paparanRbbRealisasi{--prr-navy:#0d3154;--prr-blue:#2563eb;--prr-teal:#1687a5;--prr-line:#d8e5ec;--prr-bg:#f5f9fc;min-height:calc(100vh - 62px);padding:14px;background:var(--prr-bg);color:var(--prr-navy);font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}
  #paparanRbbRealisasi *{box-sizing:border-box}.prr-header{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:15px 17px;border:1px solid #d0dfeb;border-radius:17px;background:#fff;box-shadow:0 5px 18px rgba(12,69,117,.08)}.prr-brand{display:flex;align-items:center;gap:12px;min-width:0}.prr-brand-icon{display:grid;place-items:center;width:43px;height:43px;flex:0 0 auto;border-radius:12px;background:#2563eb;color:#fff}.prr-brand-icon svg{width:23px;height:23px}.prr-kicker{margin-bottom:3px;color:#2082a0;font-size:8px;font-weight:950;letter-spacing:.14em}.prr-brand h1{margin:0;color:#0d3154;font-size:22px;line-height:1.15;font-weight:950;letter-spacing:-.03em}.prr-brand p{margin:4px 0 0;color:#72889a;font-size:10px;font-weight:650}.prr-controls{display:flex;align-items:end;gap:8px;flex:0 0 auto}.prr-controls label{display:flex;flex-direction:column;gap:4px}.prr-controls label span{padding-left:2px;color:#49637a;font-size:8px;font-weight:950;letter-spacing:.08em}.prr-controls input{height:36px;width:145px;padding:0 9px;border:1px solid #c7d8e6;border-radius:9px;background:#fff;color:#153956;font-size:10px;font-weight:850}.prr-btn{height:36px;padding:0 12px;border:1px solid #cbdbe5;border-radius:9px;font-size:10px;font-weight:900;cursor:pointer;white-space:nowrap}.prr-btn-primary{border-color:#2563eb;background:#2563eb;color:#fff;box-shadow:0 5px 12px rgba(37,99,235,.18)}.prr-btn-light{background:#fff;color:#49637a}.prr-btn-light:hover{background:#f1f8fc;border-color:#9fc6d7}.prr-workspace{position:relative;margin-top:12px;overflow:hidden;border:1px solid #d7e2ec;border-radius:16px;background:#fff;box-shadow:0 4px 16px rgba(15,60,91,.05)}.prr-note{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:9px 13px;border-bottom:1px solid #dce7ee;background:#fbfdfe;color:#637d8d;font-size:9px}.prr-note span{padding:4px 8px;border:1px solid #dbe5ec;border-radius:999px;background:#fff}.prr-note b{color:#37748c}.prr-status{background:#ecfdf5!important;border-color:#b7ead7!important;color:#047857}.prr-status.error{background:#fff1f2!important;border-color:#fecdd3!important;color:#be123c}.prr-tabs{display:flex;gap:4px;padding:9px 12px 0;overflow:auto;border-bottom:1px solid #dce7ee;background:#fbfdfe}.prr-tab{padding:9px 12px;border:1px solid transparent;border-radius:9px 9px 0 0;background:transparent;color:#6d8798;font-size:10px;font-weight:950;cursor:pointer;white-space:nowrap}.prr-tab:hover{background:#f0f8fa;color:#1f6b86}.prr-tab.active{border-color:#cfe1e8;border-bottom-color:#fff;background:#fff;color:#0f3c5b}.prr-panels{padding:14px}.prr-panel{display:none;animation:prrIn .2s ease}.prr-panel.active{display:block}@keyframes prrIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}.prr-panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:13px}.prr-panel-head h2{margin:0;color:#123c5b;font-size:17px;font-weight:950}.prr-panel-head p{margin:4px 0 0;color:#7890a0;font-size:9px;font-weight:650}.prr-scope{padding:5px 9px;border:1px solid #cfe1e8;border-radius:999px;color:#397d93;background:#f3fbfd;font-size:8px;font-weight:900;white-space:nowrap}.prr-card-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.prr-card{position:relative;min-width:0;padding:12px;border:1px solid #dce8ef;border-radius:13px;background:#fff;box-shadow:0 4px 12px rgba(15,60,91,.05);cursor:pointer;transition:transform .15s ease,box-shadow .15s ease,border-color .15s ease}.prr-card:hover{transform:translateY(-2px);border-color:#8dc2d5;box-shadow:0 9px 18px rgba(15,60,91,.1)}.prr-card:focus-visible{outline:3px solid rgba(37,99,235,.2);outline-offset:2px}.prr-card:after{content:'↗';position:absolute;right:9px;top:8px;display:grid;place-items:center;width:18px;height:18px;border:1px solid #c9e1ee;border-radius:50%;background:#f2faff;color:#19739a;font-size:11px;font-weight:950}.prr-card-head{display:flex;align-items:center;gap:8px;padding-right:20px}.prr-card-icon{display:grid;place-items:center;width:29px;height:29px;border-radius:9px;background:#eff6ff;color:#2563eb;font-size:10px;font-weight:950}.prr-card-head h3{margin:0;color:#173c58;font-size:12px;font-weight:950}.prr-card-head small{display:block;margin-top:2px;color:#7b929f;font-size:8px;font-weight:700}.prr-card-values{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:5px;margin-top:10px}.prr-card-values div{min-width:0;padding:7px 5px;border-radius:8px;background:#f8fbfd}.prr-card-values span,.prr-ratio-table span{display:block;color:#78909e;font-size:7px;font-weight:850;text-transform:uppercase;line-height:1.2}.prr-card-values b{display:block;margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#153b59;font-size:11px;font-weight:950}.prr-card-values .actual b{color:#0f766e}.prr-card-foot{margin-top:8px;color:#7b929f;font-size:8px;font-weight:750}.prr-card.is-ratio .prr-card-values b{font-size:14px}.prr-empty{display:grid;place-items:center;min-height:170px;color:#7890a0;font-size:10px;font-weight:800}.prr-loading{display:flex;align-items:center;justify-content:center;gap:8px;min-height:280px;color:#397d93;font-size:10px;font-weight:850}.prr-spinner{width:20px;height:20px;border:3px solid #c8e5eb;border-top-color:#1d7891;border-radius:50%;animation:prrSpin .75s linear infinite}@keyframes prrSpin{to{transform:rotate(360deg)}}.prr-ratio-table{width:100%;overflow:auto;border:1px solid #dce8ef;border-radius:12px}.prr-ratio-table table{width:100%;min-width:720px;border-collapse:collapse}.prr-ratio-table th,.prr-ratio-table td{padding:9px 10px;border-bottom:1px solid #e4edf2;text-align:right;font-size:10px}.prr-ratio-table th:first-child,.prr-ratio-table td:first-child{text-align:left}.prr-ratio-table thead th{background:#eef8fa;color:#37677a;font-size:8px;font-weight:950;text-transform:uppercase}.prr-ratio-table tbody th{color:#173c58;font-weight:850}.prr-ratio-table tbody td{color:#153b59;font-family:ui-monospace,monospace;font-weight:850}.prr-ratio-table tbody tr:last-child th,.prr-ratio-table tbody tr:last-child td{border-bottom:0}.prr-good{color:#07815f!important}.prr-bad{color:#be3a42!important}.prr-footnote{display:flex;justify-content:space-between;gap:12px;padding:10px 15px;border-top:1px solid #e0eaf0;color:#7b909e;font-size:9px}.prr-footnote b{color:#496e81}.prr-modal{position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(7,30,55,.62);backdrop-filter:blur(6px)}.prr-modal[hidden]{display:none}.prr-dialog{width:min(760px,100%);max-height:min(780px,calc(100dvh - 30px));overflow:hidden;border:1px solid #cfe1ec;border-radius:18px;background:#fff;box-shadow:0 22px 70px rgba(7,47,78,.28)}.prr-dialog-head{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:18px 20px 14px;border-bottom:1px solid #e1edf3;background:linear-gradient(135deg,#f8fdff,#fff)}.prr-dialog-head h2{margin:4px 0 0;color:#0d3154;font-size:19px;font-weight:950}.prr-dialog-head p{margin:4px 0 0;color:#728b9a;font-size:9px;font-weight:700}.prr-close{display:grid;place-items:center;width:31px;height:31px;border:1px solid #d2e1e9;border-radius:9px;background:#fff;color:#527083;font-size:22px;line-height:1;cursor:pointer}.prr-settings-body{max-height:calc(100dvh - 240px);overflow:auto;padding:15px 20px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.prr-setting-group{padding:10px;border:1px solid #dceaf1;border-radius:11px;background:#f8fcfe}.prr-setting-group h3{margin:0 0 8px;color:#17657e;font-size:9px;font-weight:950;text-transform:uppercase;letter-spacing:.07em}.prr-check{display:flex;align-items:center;gap:8px;padding:6px 4px;color:#254c65;font-size:10px;font-weight:800;cursor:pointer}.prr-check input{accent-color:#2563eb}.prr-dialog-actions{display:flex;justify-content:flex-end;gap:8px;padding:12px 20px;border-top:1px solid #e1edf3;background:#fbfdfe}.prr-history-dialog{width:min(900px,100%)}.prr-history-body{max-height:calc(100dvh - 145px);overflow:auto;padding:16px 20px 20px}.prr-history-meta{display:flex;align-items:stretch;gap:8px;flex-wrap:wrap;margin-bottom:13px}.prr-stat{min-width:145px;flex:1 1 145px;padding:9px 11px;border:1px solid #dceaf1;border-radius:10px;background:#f8fcfe}.prr-stat span{display:block;color:#78909e;font-size:8px;font-weight:850;text-transform:uppercase}.prr-stat b{display:block;margin-top:4px;color:#123f5e;font-size:13px;font-weight:950}.prr-stat small{display:block;margin-top:3px;color:#78909e;font-size:8px;font-weight:700}.prr-formula{margin:0 0 12px;padding:8px 10px;border-left:3px solid #25a1bb;border-radius:5px;background:#effafc;color:#527383;font-size:9px;font-weight:750}.prr-chart{overflow:hidden;padding:10px 8px 4px;border:1px solid #dceaf1;border-radius:13px;background:#fff}.prr-chart svg{display:block;width:100%;height:auto;min-height:250px}.prr-axis{fill:#78909e;font-size:10px;font-weight:700}.prr-grid{stroke:#e5eef3;stroke-width:1}.prr-area{fill:rgba(37,145,190,.12)}.prr-line{fill:none;stroke:#1687b1;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}.prr-dot{fill:#fff;stroke:#1687b1;stroke-width:2}.prr-values{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:6px;margin-top:12px}.prr-value{padding:7px 9px;border:1px solid #e3edf2;border-radius:8px;background:#fbfdfe}.prr-value span{display:block;color:#78909e;font-size:8px;font-weight:800}.prr-value b{display:block;margin-top:3px;color:#254c65;font-size:10px;font-family:ui-monospace,monospace}@media(max-width:1050px){.prr-card-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:760px){#paparanRbbRealisasi{padding:9px}.prr-header{align-items:flex-start;flex-direction:column}.prr-controls{width:100%;display:grid;grid-template-columns:1fr auto auto}.prr-controls input{width:100%}.prr-card-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.prr-settings-body{grid-template-columns:1fr}}@media(max-width:520px){.prr-brand h1{font-size:19px}.prr-brand p{font-size:8px}.prr-controls{grid-template-columns:1fr 1fr}.prr-controls label{grid-column:1/-1}.prr-btn{padding:0 8px;font-size:9px}.prr-panels{padding:10px}.prr-card-grid{grid-template-columns:1fr}.prr-panel-head{flex-direction:column}.prr-footnote{align-items:flex-start;flex-direction:column;gap:4px}.prr-dialog-head{padding:14px}.prr-settings-body,.prr-dialog-actions,.prr-history-body{padding-left:12px;padding-right:12px}}
</style>

<style>
  .prr-indicator-extra{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,.85fr);gap:12px;margin-top:12px}.prr-extra-box{min-width:0;padding:12px;border:1px solid #dce8ef;border-radius:12px;background:#fff}.prr-extra-box h3{margin:0;color:#17657e;font-size:11px;font-weight:950}.prr-extra-box p{margin:3px 0 9px;color:#7890a0;font-size:8px;font-weight:700}.prr-rr-summary{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:8px}.prr-rr-stat{min-width:105px;flex:1;padding:7px 8px;border-radius:8px;background:#f8fbfd}.prr-rr-stat span{display:block;color:#78909e;font-size:7px;font-weight:850;text-transform:uppercase}.prr-rr-stat b{display:block;margin-top:3px;color:#153b59;font-size:13px;font-weight:950}.prr-rr-chart{overflow:hidden;border:1px solid #e1edf2;border-radius:9px;background:#fff}.prr-rr-chart svg{display:block;width:100%;height:auto;min-height:190px}.prr-rr-axis{fill:#78909e;font-size:9px;font-weight:700}.prr-rr-grid{stroke:#e5eef3;stroke-width:1}.prr-rr-area{fill:rgba(22,135,177,.12)}.prr-rr-line{fill:none;stroke:#1687b1;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}.prr-rr-dot{fill:#fff;stroke:#1687b1;stroke-width:2}.prr-kolek-table{width:100%;border-collapse:collapse}.prr-kolek-table th,.prr-kolek-table td{padding:7px 5px;border-bottom:1px solid #e4edf2;font-size:9px}.prr-kolek-table th{text-align:left;color:#37677a;font-weight:950}.prr-kolek-table td{text-align:right;color:#153b59;font-family:ui-monospace,monospace;font-weight:850}.prr-kolek-table tr:last-child th,.prr-kolek-table tr:last-child td{border-bottom:0;background:#eef8fa;font-weight:950}.prr-kolek-table .prr-kolek-pct{color:#07815f}.prr-kolek-table .prr-kolek-npl{color:#be3a42}@media(max-width:850px){.prr-indicator-extra{grid-template-columns:1fr}}@media(max-width:520px){.prr-extra-box{padding:9px}.prr-rr-stat{min-width:92px}.prr-rr-stat b{font-size:11px}}
</style>

<style>
  .prr-extra-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:7px}.prr-extra-head h3{margin:0}.prr-chart-toggle{display:flex;gap:3px;padding:3px;border:1px solid #dce8ef;border-radius:8px;background:#f8fbfd}.prr-chart-toggle button{padding:5px 9px;border:0;border-radius:6px;background:transparent;color:#78909e;font-size:8px;font-weight:950;cursor:pointer}.prr-chart-toggle button.active{background:#1687a5;color:#fff;box-shadow:0 2px 5px rgba(22,135,165,.18)}.prr-chart-view[hidden]{display:none!important}.prr-chart-caption{margin:-2px 0 7px;color:#7890a0;font-size:8px;font-weight:750}
  .prr-branch-switch{display:flex;gap:4px;margin-bottom:12px;padding:4px;border:1px solid #dce8ef;border-radius:10px;background:#f8fbfd;width:max-content}.prr-branch-switch button{padding:7px 12px;border:0;border-radius:7px;background:transparent;color:#6d8798;font-size:9px;font-weight:950;cursor:pointer}.prr-branch-switch button.active{background:#2563eb;color:#fff;box-shadow:0 3px 8px rgba(37,99,235,.18)}.prr-members{padding:12px;border:1px solid #dce8ef;border-radius:12px;background:#fff}.prr-members-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:11px}.prr-members-head h3{margin:0;color:#17657e;font-size:13px;font-weight:950}.prr-members-head p{margin:3px 0 0;color:#7890a0;font-size:8px;font-weight:700}.prr-members-head input{width:220px;height:32px;padding:0 9px;border:1px solid #cfe1e8;border-radius:8px;color:#254c65;font-size:9px;font-weight:750;outline:none}.prr-members-head input:focus{border-color:#5baac0;box-shadow:0 0 0 3px rgba(91,170,192,.12)}.prr-member-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:7px;margin-bottom:11px}.prr-member-stat{padding:8px 9px;border-radius:8px;background:#f8fbfd}.prr-member-stat span{display:block;color:#78909e;font-size:7px;font-weight:850;text-transform:uppercase}.prr-member-stat b{display:block;margin-top:3px;color:#153b59;font-size:13px;font-weight:950}.prr-members-table{width:100%;overflow:auto;border:1px solid #dce8ef;border-radius:9px}.prr-members-table table{width:100%;min-width:1120px;border-collapse:collapse}.prr-members-table th,.prr-members-table td{padding:8px 7px;border-bottom:1px solid #e4edf2;text-align:left;color:#254c65;font-size:9px;white-space:nowrap}.prr-members-table thead th{background:#eef8fa;color:#37677a;font-size:7px;font-weight:950;text-transform:uppercase}.prr-members-table tbody th{color:#153b59;font-weight:900}.prr-members-table tbody tr:last-child th,.prr-members-table tbody tr:last-child td{border-bottom:0}.prr-member-empty{text-align:center!important;color:#7890a0!important;padding:22px!important}.prr-members-table tr[hidden]{display:none}
  @media(max-width:650px){.prr-extra-head,.prr-members-head{flex-direction:column}.prr-chart-toggle,.prr-branch-switch{width:100%}.prr-chart-toggle button,.prr-branch-switch button{flex:1}.prr-members-head input{width:100%}.prr-member-summary{grid-template-columns:repeat(2,minmax(0,1fr))}
  }
</style>

<script>
(function () {
  const root = document.getElementById('paparanRbbRealisasi');
  if (!root) return;
  const API_RBB = './api/rbb/';
  const API_LAPKEU = './api/lapkeu/';
  const API_DASHBOARD = './api/dashboard/';
  const API_ANGGOTA = './api/anggota/';
  const API_DATE = './api/date/';
  const API_KODE = './api/kode/';
  const STORE_KEY = 'paparan_rbb_realisasi_tabs_v1';
  const fmt = new Intl.NumberFormat('id-ID', {maximumFractionDigits: 2});
  const fmtInt = new Intl.NumberFormat('id-ID', {maximumFractionDigits: 0});
  const esc = (value) => String(value == null ? '' : value).replace(/[&<>"']/g, function (c) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]); });
  const num = (value) => { const n = Number(value); return Number.isFinite(n) ? n : null; };
  const path = (obj, keys, fallback) => {
    let value = obj;
    String(keys).split('.').forEach(function (key) { value = value && value[key] !== undefined ? value[key] : undefined; });
    return value === undefined || value === null ? fallback : value;
  };
  const state = {scopes:[], selected:[], models:new Map(), active:'', closing:'', actualDate:'', historyRequest:0};
  const defaultTabs = ['kinerja_pusat','indikator_keuangan_pusat','korwil_banyumas','cabang_018','cabang_020'];
  const korwils = [
    {id:'korwil_semarang', type:'korwil', value:'SEMARANG', label:'Korwil Semarang', title:'Kinerja Korwil Semarang'},
    {id:'korwil_solo', type:'korwil', value:'SOLO', label:'Korwil Solo', title:'Kinerja Korwil Solo'},
    {id:'korwil_banyumas', type:'korwil', value:'BANYUMAS', label:'Korwil Banyumas', title:'Kinerja Korwil Banyumas'},
    {id:'korwil_pekalongan', type:'korwil', value:'PEKALONGAN', label:'Korwil Pekalongan', title:'Kinerja Korwil Pekalongan'}
  ];
  const metricDefs = [
    {key:'aset', code:'1', label:'ASSET', caption:'Total Aset', icon:'↗'},
    {key:'tabungan', code:'3', label:'TABUNGAN', caption:'Dana Pihak Ketiga', icon:'◉'},
    {key:'deposito', code:'4', label:'DEPOSITO', caption:'Dana Pihak Ketiga', icon:'Rp'},
    {key:'damas', code:'2', label:'TOTAL DAMAS', caption:'Dana Masyarakat', icon:'◉'},
    {key:'kredit', code:'5', label:'KREDIT', caption:'Saldo Bank', icon:'Rp'},
    {key:'pendapatan', code:'6', label:'PENDAPATAN', caption:'Pendapatan Sistem', icon:'↗'},
    {key:'biaya', code:'7', label:'BIAYA', caption:'Beban Sistem', icon:'▾'},
    {key:'laba', code:'8', label:'LABA (RUGI)', caption:'Laba Sebelum Pajak', icon:'Rp'}
  ];
  const ratioDefs = [
    {code:'10', label:'KPMM'}, {code:'11', label:'Modal Inti'}, {code:'12', label:'KAP'}, {code:'13', label:'PPAP'},
    {code:'15', label:'NPL Brutto · Saldo Bank'}, {code:'16', label:'NPL Netto'}, {code:'17', label:'Kredit / Aset Produktif'},
    {code:'18', label:'ROA'}, {code:'19', label:'NIM'}, {code:'20', label:'BOPO'}, {code:'21', label:'Cash Ratio'},
    {code:'22', label:'LDR'}, {code:'24', label:'CASA'}
  ];
  function apiPost(url, payload) {
    const options = {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload), cache:'no-store'};
    return (window.apiFetch ? window.apiFetch(url, options) : fetch(url, options)).then(function (response) {
      return response.json().then(function (json) { if (!response.ok || Number(json.status) >= 400) throw new Error(json.message || 'API gagal dimuat'); return json.data || {}; });
    });
  }
  function displayNominal(value) {
    const n = num(value);
    if (n === null) return '-';
    const sign = n < 0 ? '-' : '';
    const a = Math.abs(n);
    if (a >= 1e12) return sign + fmt.format(a / 1e12) + ' T';
    if (a >= 1e9) return sign + fmt.format(a / 1e9) + ' M';
    if (a >= 1e6) return sign + fmt.format(a / 1e6) + ' Jt';
    return sign + fmtInt.format(a);
  }
  function displayRatio(value) { const n = num(value); return n === null ? '-' : fmt.format(n) + '%'; }
  function targetValue(model, code, yearEnd) {
    const row = model.rows[String(code)] || {};
    const direct = num(row[yearEnd ? 'target_rbb_year_end' : 'target_rbb']);
    if (direct !== null) return direct;
    const source = path(model.rbb, yearEnd ? 'rbb_sources.year_end' : 'rbb_sources.periode', {}) || {};
    const mapped = num(source[String(code)]);
    if (mapped !== null) return mapped;
    const fallbackMap = {'1':'95','2':'105','5':'63','6':'196','7':'258','8':'261'};
    return num(source[fallbackMap[String(code)]]);
  }
  function actualValues(rbb, actual) {
    const detail = actual.ringkasan_detail || {};
    const macro = actual.makro || {};
    const health = actual.kesehatan_rasio || {};
    const detailActual = rbb.detail_actual || {};
    const damas = detailActual.damas || {};
    const credit = detailActual.credit || {};
    const statuses = credit.statuses || {};
    const kreditTotal = num(credit.total && credit.total.rupiah);
    const nplAmount = ['KL','D','M'].reduce(function (sum, key) { return sum + (num(statuses[key] && statuses[key].rupiah) || 0); }, 0);
    const nplRatio = kreditTotal && kreditTotal > 0 ? nplAmount / kreditTotal * 100 : num(path(macro, 'npl.persen_aktual', null));
    const produktif = num(path(actual, 'rasio.detail_nominal.rata_aset_produktif', null));
    return {
      aset:num(path(macro, 'aset.nominal_aktual', null)),
      tabungan:num(path(damas, 'tabungan.rupiah', null)),
      deposito:num(path(damas, 'deposito.rupiah', null)),
      damas:num(path(damas, 'total.rupiah', null)) ?? num(path(detail, 'dana_masyarakat.total', null)),
      kredit:kreditTotal ?? num(path(detail, 'kredit_diberikan.saldo_bank_ead', null)),
      pendapatan:num(path(macro, 'pendapatan.nominal_aktual', null)),
      biaya:num(path(macro, 'biaya.nominal_aktual', null)),
      laba:num(path(detail, 'laba_sebelum_pajak', null)) ?? num(path(macro, 'laba_rugi.nominal_aktual', null)),
      npl:nplRatio,
      ratio:{
        '10':num(path(actual, 'rasio.kpmm_persen', null)),
        '11':num(path(actual, 'rasio.modal_inti_persen', null)),
        '12':num(path(detail, 'rasio_utama.kap', null)),
        '13':num(path(detail, 'rasio_utama.ckpn_terhadap_ppka', null)),
        '15':nplRatio,
        '16':num(path(detail, 'rasio_utama.npl_baki_debet_netto', null)),
        '17':produktif && produktif > 0 && kreditTotal !== null ? kreditTotal / produktif * 100 : null,
        '18':num(path(health, 'roa.persen_aktual', null)),
        '19':num(path(health, 'nim.persen_aktual', null)),
        '20':num(path(health, 'bopo.persen_aktual', null)),
        '21':num(path(health, 'cash.persen_aktual', null)),
        '22':num(path(health, 'ldr.persen_aktual', null)),
        '24':num(path(health, 'casa.persen_aktual', null))
      }
    };
  }
  function modelFor(scope, rbb, actual) {
    const rows = Object.fromEntries((Array.isArray(rbb.data) ? rbb.data : []).map(function (row) { return [String(row.kode_monbis), row]; }));
    return {scope:scope, rbb:rbb, rows:rows, trend:[], actual:actualValues(rbb, actual), actualDate:path(actual, 'info_tanggal.aktual', state.closing), closing:state.closing};
  }
  function scopePayload(scope) {
    const payload = {type:'ikhtisar_rbb', harian_date:state.closing};
    if (scope.type === 'korwil') payload.korwil = scope.value;
    else payload.kode_kantor = scope.value || '000';
    return payload;
  }
  function actualPayload(scope) {
    const payload = {type:'tv_makro_summary', harian_date:state.closing, h7_fallback:true};
    if (scope.type === 'korwil') payload.korwil = scope.value;
    else payload.kode_kantor = scope.value || '000';
    return payload;
  }
  async function loadModel(scope) {
    const trendPayload = {type:'tren_portofolio_kredit', harian_date:state.closing, periode:'bulanan'};
    if (scope.type === 'korwil') trendPayload.korwil = scope.value;
    else trendPayload.kode_kantor = scope.value || '000';
    const pair = await Promise.all([
      apiPost(API_RBB, scopePayload(scope)),
      apiPost(API_LAPKEU, actualPayload(scope)),
      apiPost(API_DASHBOARD, trendPayload).catch(function () { return []; }),
      scope.type === 'branch'
        ? apiPost(API_ANGGOTA, {type:'rekap_anggota', kode_kantor:scope.value, as_of:state.closing}).catch(function () { return {meta:{}, data:[]}; })
        : Promise.resolve(null)
    ]);
    const model = modelFor(scope, pair[0], pair[1]);
    model.trend = Array.isArray(pair[2]) ? pair[2] : [];
    model.members = pair[3] || null;
    return model;
  }
  function cardHtml(model, def) {
    const actual = model.actual[def.key];
    const target = targetValue(model, def.code, false);
    const year = targetValue(model, def.code, true);
    const achievement = num(actual) !== null && target ? (def.inverse ? target / actual * 100 : actual / target * 100) : null;
    const yearAchievement = num(actual) !== null && year ? (def.inverse ? year / actual * 100 : actual / year * 100) : null;
    const value = function (v) { return def.ratio ? displayRatio(v) : displayNominal(v); };
    const good = def.inverse ? (num(achievement) !== null && achievement >= 100) : (num(achievement) !== null && achievement >= 100);
    return '<article class="prr-card' + (def.ratio ? ' is-ratio' : '') + '" tabindex="0" role="button" data-prr-history="' + esc(def.key) + '" data-prr-scope-type="' + esc(model.scope.type) + '" data-prr-scope-value="' + esc(model.scope.value || '') + '" data-prr-year="' + esc(String(state.closing).slice(0,4)) + '">' +
      '<div class="prr-card-head"><div class="prr-card-icon">' + esc(def.icon) + '</div><div><h3>' + esc(def.label) + '</h3><small>' + esc(def.caption) + '</small></div></div>' +
      '<div class="prr-card-values"><div><span>RBB bulan</span><b>' + esc(value(target)) + '</b></div><div class="actual"><span>Realisasi closing</span><b>' + esc(value(actual)) + '</b></div><div><span>RBB Des</span><b>' + esc(value(year)) + '</b></div></div>' +
      '<div class="prr-card-values"><div><span>Capaian bulan</span><b class="' + (good ? 'prr-good' : 'prr-bad') + '">' + esc(displayRatio(achievement)) + '</b></div><div><span>Capaian Des</span><b class="' + (yearAchievement >= 100 ? 'prr-good' : 'prr-bad') + '">' + esc(displayRatio(yearAchievement)) + '</b></div><div><span>Basis</span><b>' + (def.ratio ? 'Saldo bank' : 'Rupiah') + '</b></div></div>' +
      '<div class="prr-card-foot">Klik untuk history closing</div></article>';
  }
  function ratioHtml(model) {
    const rows = ratioDefs.map(function (def) {
      const actual = model.actual.ratio[def.code];
      const target = targetValue(model, def.code, false);
      const year = targetValue(model, def.code, true);
      const inverse = def.code === '15' || def.code === '16';
      const achieve = actual !== null && target ? (inverse ? target / actual * 100 : actual / target * 100) : null;
      const yearAchieve = actual !== null && year ? (inverse ? year / actual * 100 : actual / year * 100) : null;
      return actual === null && target === null && year === null ? '' : '<tr><th>' + esc(def.label) + '</th><td>' + esc(displayRatio(target)) + '</td><td>' + esc(displayRatio(actual)) + '</td><td>' + esc(displayRatio(year)) + '</td><td class="' + (achieve >= 100 ? 'prr-good' : 'prr-bad') + '">' + esc(displayRatio(achieve)) + '</td><td class="' + (yearAchieve >= 100 ? 'prr-good' : 'prr-bad') + '">' + esc(displayRatio(yearAchieve)) + '</td></tr>';
    }).join('');
    return '<div class="prr-ratio-table"><table><thead><tr><th>INDIKATOR</th><th>RBB BULAN</th><th>REALISASI</th><th>RBB DES</th><th>CAPAIAN BULAN</th><th>CAPAIAN DES</th></tr></thead><tbody>' + (rows || '<tr><td colspan="6">Data rasio belum tersedia.</td></tr>') + '</tbody></table></div>';
  }
  function creditSnapshot(model) {
    const credit = path(model.rbb, 'detail_actual.credit', {}) || {};
    const total = num(path(credit, 'total.rupiah', null)) || 0;
    const statuses = credit.statuses || {};
    const rows = [
      {key:'L', label:'Lancar'},
      {key:'DP', label:'Dalam Perhatian Khusus'},
      {key:'KL', label:'Kurang Lancar'},
      {key:'D', label:'Diragukan'},
      {key:'M', label:'Macet'}
    ].map(function (item) {
      const value = num(path(statuses, item.key + '.rupiah', null)) || 0;
      return {key:item.key, label:item.label, value:value, pct:total > 0 ? value / total * 100 : 0};
    });
    const npl = rows.filter(function (row) { return ['KL','D','M'].indexOf(row.key) >= 0; }).reduce(function (sum, row) { return sum + row.value; }, 0);
    return {total:total, npl:npl, nplPct:total > 0 ? npl / total * 100 : 0, rows:rows};
  }
  function rrTrendRows(model) {
    return (Array.isArray(model.trend) ? model.trend : []).filter(function (row) { return num(row.rr_persen) !== null; });
  }
  function trendChart(rows, field, label) {
    if (!rows.length) return '<div class="prr-empty">History ' + esc(label) + ' belum tersedia.</div>';
    const width = 720, height = 220, pad = {top:18, right:18, bottom:38, left:48};
    const values = rows.map(function (row) { return num(row[field]) || 0; });
    let min = Math.min.apply(null, values), max = Math.max.apply(null, values);
    if (min === max) { min = Math.max(0, min - 5); max += 5; } else { const gap = (max - min) * .16; min = Math.max(0, min - gap); max += gap; }
    const plotW = width - pad.left - pad.right, plotH = height - pad.top - pad.bottom;
    const x = function (index) { return rows.length === 1 ? pad.left + plotW / 2 : pad.left + plotW * index / (rows.length - 1); };
    const y = function (value) { return pad.top + (max - value) / (max - min) * plotH; };
    const points = rows.map(function (row, index) { return x(index).toFixed(2) + ',' + y(num(row[field]) || 0).toFixed(2); }).join(' ');
    const grid = [0,1,2,3,4].map(function (index) {
      const yy = pad.top + plotH * index / 4, value = max - (max - min) * index / 4;
      return '<line class="prr-rr-grid" x1="' + pad.left + '" y1="' + yy + '" x2="' + (width - pad.right) + '" y2="' + yy + '"></line><text class="prr-rr-axis" x="' + (pad.left - 7) + '" y="' + (yy + 3) + '" text-anchor="end">' + esc(displayRatio(value)) + '</text>';
    }).join('');
    const labels = rows.map(function (row, index) {
      const visible = rows.length <= 8 || index === 0 || index === rows.length - 1 || index % Math.ceil(rows.length / 6) === 0;
      return visible ? '<text class="prr-rr-axis" x="' + x(index) + '" y="' + (height - 13) + '" text-anchor="middle">' + esc(row.label || formatDate(row.tanggal)) + '</text>' : '';
    }).join('');
    const dots = rows.map(function (row, index) {
      return '<circle class="prr-rr-dot" cx="' + x(index) + '" cy="' + y(num(row[field]) || 0) + '" r="3.5"><title>' + esc(row.label || formatDate(row.tanggal)) + ': ' + esc(displayRatio(row[field])) + '</title></circle>';
    }).join('');
    return '<svg viewBox="0 0 ' + width + ' ' + height + '" role="img" aria-label="Grafik ' + esc(label) + ' bulanan"><g>' + grid + '</g><polygon class="prr-rr-area" points="' + pad.left + ',' + (height - pad.bottom) + ' ' + points + ' ' + x(rows.length - 1) + ',' + (height - pad.bottom) + '"></polygon><polyline class="prr-rr-line" points="' + points + '"></polyline><g>' + dots + '</g><g>' + labels + '</g></svg>';
  }
  function scopeExtraHtml(model) {
    const credit = creditSnapshot(model);
    const trend = rrTrendRows(model);
    const nplTrend = (Array.isArray(model.trend) ? model.trend : []).filter(function (row) { return num(row.npl_persen) !== null; });
    const latest = trend.length ? trend[trend.length - 1] : null;
    const previous = trend.length > 1 ? trend[trend.length - 2] : null;
    const nplLatest = nplTrend.length ? nplTrend[nplTrend.length - 1] : null;
    const nplPrevious = nplTrend.length > 1 ? nplTrend[nplTrend.length - 2] : null;
    const rrActual = latest ? num(latest.rr_persen) : null;
    const rrPrevious = previous ? num(previous.rr_persen) : null;
    const rrDelta = rrActual !== null && rrPrevious !== null ? rrActual - rrPrevious : null;
    const nplActual = nplLatest ? num(nplLatest.npl_persen) : credit.nplPct;
    const nplPrev = nplPrevious ? num(nplPrevious.npl_persen) : null;
    const nplDelta = nplActual !== null && nplPrev !== null ? nplActual - nplPrev : null;
    const rrStat = function (label, value, tone) { return '<div class="prr-rr-stat"><span>' + esc(label) + '</span><b class="' + (tone || '') + '">' + esc(value) + '</b></div>'; };
    const kolekRows = credit.rows.map(function (row) {
      const nplClass = ['KL','D','M'].indexOf(row.key) >= 0 ? ' prr-kolek-npl' : '';
      return '<tr><th>' + esc(row.key + ' · ' + row.label) + '</th><td>' + esc(displayNominal(row.value)) + '</td><td class="prr-kolek-pct' + nplClass + '">' + esc(displayRatio(row.pct)) + '</td></tr>';
    }).join('');
    return '<div class="prr-indicator-extra" data-prr-extra>' +
      '<div class="prr-extra-box"><div class="prr-extra-head"><div><h3>NPL atau Repayment Rate (RR)</h3><p>Pilih indikator yang ingin dipantau beserta breakdown chart-nya.</p></div><div class="prr-chart-toggle" role="tablist" aria-label="Pilih indikator"><button type="button" data-prr-chart-toggle="npl">NPL</button><button type="button" class="active" data-prr-chart-toggle="rr">RR</button></div></div>' +
        '<div class="prr-chart-view" data-prr-chart-view="npl" hidden><p class="prr-chart-caption">NPL bruto memakai saldo bank dan dibandingkan dengan total kredit saldo bank.</p><div class="prr-rr-summary">' +
          rrStat('NPL saldo bank', displayRatio(credit.nplPct), 'prr-bad') +
          rrStat('NPL nominal', displayNominal(credit.npl)) +
          rrStat('NPL bulan lalu', nplPrev === null ? '-' : displayRatio(nplPrev), '') +
          rrStat('Perubahan NPL', nplDelta === null ? '-' : (nplDelta >= 0 ? '+' : '') + displayRatio(nplDelta), nplDelta !== null && nplDelta > 0 ? 'prr-bad' : 'prr-good') +
        '</div><div class="prr-rr-chart">' + trendChart(nplTrend, 'npl_persen', 'NPL') + '</div></div>' +
        '<div class="prr-chart-view" data-prr-chart-view="rr"><p class="prr-chart-caption">RR mengikuti dashboard dengan basis baki debet.</p><div class="prr-rr-summary">' +
          rrStat('RR actual', rrActual === null ? '-' : displayRatio(rrActual), rrActual !== null && rrPrevious !== null && rrDelta < 0 ? 'prr-bad' : 'prr-good') +
          rrStat('RR bulan lalu', rrPrevious === null ? '-' : displayRatio(rrPrevious), '') +
          rrStat('Perubahan RR', rrDelta === null ? '-' : (rrDelta >= 0 ? '+' : '') + displayRatio(rrDelta), rrDelta !== null && rrDelta < 0 ? 'prr-bad' : 'prr-good') +
          rrStat('Total kredit', displayNominal(credit.total), '') +
        '</div><div class="prr-rr-chart">' + trendChart(trend, 'rr_persen', 'RR') + '</div></div></div>' +
      '<div class="prr-extra-box"><h3>Breakdown Kolektibilitas</h3><p>Nominal setiap kolektibilitas dibandingkan total kredit saldo bank.</p><table class="prr-kolek-table"><thead><tr><th>KOLEKTIBILITAS</th><th>SALDO BANK</th><th>% TOTAL KREDIT</th></tr></thead><tbody>' + kolekRows + '<tr><th>Total Kredit</th><td>' + esc(displayNominal(credit.total)) + '</td><td>100%</td></tr></tbody></table></div>' +
    '</div>';
  }
  function memberGender(value) {
    const gender = String(value == null ? '' : value).trim().toUpperCase();
    if (['L','LAKI-LAKI','LAKI LAKI','M'].indexOf(gender) >= 0) return 'Laki-laki';
    if (['P','PEREMPUAN','W'].indexOf(gender) >= 0) return 'Perempuan';
    return value || '-';
  }
  function memberDate(value) {
    if (!value || String(value).indexOf('0000-00-00') === 0) return '-';
    return formatDate(value);
  }
  function memberHtml(model) {
    const payload = model.members || {};
    const meta = payload.meta || {};
    const members = Array.isArray(payload.data) ? payload.data : [];
    const stat = function (label, value) { return '<div class="prr-member-stat"><span>' + esc(label) + '</span><b>' + esc(value) + '</b></div>'; };
    const rows = members.map(function (row) {
      const searchText = [row.employee_id, row.full_name, row.kelamin, row.status_kepeg, row.unit_kerja, row.job_position, row.level, row.group_jabatan].join(' ').toLowerCase();
      return '<tr data-prr-member-row data-search="' + esc(searchText) + '"><td>' + esc(row.employee_id || '-') + '</td><th>' + esc(row.full_name || '-') + '</th><td>' + esc(row.age == null ? '-' : row.age + ' th') + '</td><td>' + esc(memberGender(row.kelamin)) + '</td><td>' + esc(row.status_kepeg || '-') + '</td><td>' + esc(memberDate(row.mulai_bekerja)) + '</td><td>' + esc(row.unit_kerja || '-') + '</td><td>' + esc(row.job_position || '-') + '</td><td>' + esc(row.level || '-') + '</td><td>' + esc(row.group_jabatan || '-') + '</td></tr>';
    }).join('');
    return '<div class="prr-members"><div class="prr-members-head"><div><h3>Rekap Anggota ' + esc(meta.branch_name || model.scope.label) + '</h3><p>Daftar pegawai aktif pada closing ' + esc(state.closing) + '.</p></div><input type="search" data-prr-member-search placeholder="Cari nama, jabatan, unit..." aria-label="Cari anggota"></div><div class="prr-member-summary">' +
      stat('Total pegawai', meta.total == null ? members.length : meta.total) +
      stat('Laki-laki', meta.male == null ? '-' : meta.male) +
      stat('Perempuan', meta.female == null ? '-' : meta.female) +
      stat('Rata-rata umur', meta.average_age == null ? '-' : meta.average_age + ' th') +
      '</div><div class="prr-members-table"><table><thead><tr><th>ID PEG</th><th>NAMA</th><th>UMUR</th><th>KELAMIN</th><th>STATUS</th><th>MULAI BEKERJA</th><th>UNIT KERJA</th><th>JABATAN</th><th>LEVEL</th><th>GROUP JABATAN</th></tr></thead><tbody>' + (rows || '<tr><td colspan="10" class="prr-member-empty">Belum ada pegawai aktif untuk cabang ini.</td></tr>') + '</tbody></table></div></div>';
  }
  function branchKinerjaHtml(model) {
    return '<div class="prr-branch-switch" role="tablist" aria-label="Rekap cabang"><button type="button" class="active" data-prr-branch-toggle="kinerja">Kinerja</button><button type="button" data-prr-branch-toggle="anggota">Rekap Anggota</button></div><div data-prr-branch-view="kinerja"><div class="prr-card-grid">' + metricDefs.filter(function (def) { return def.key !== 'npl'; }).map(function (def) { return cardHtml(model, def); }).join('') + '</div>' + scopeExtraHtml(model) + '</div><div data-prr-branch-view="anggota" hidden>' + memberHtml(model) + '</div>';
  }
  function panelHtml(model, indicatorOnly, active) {
    const scope = indicatorOnly ? (state.scopes.find(function (x) { return x.indicator === true; }) || model.scope) : model.scope;
    const title = indicatorOnly ? 'Indikator Keuangan ' + scope.label : scope.title;
    const subtitle = indicatorOnly ? 'Rasio RBB dibandingkan dengan realisasi pada closing terpilih.' : 'Perbandingan target RBB sistem dengan realisasi closing terpilih.';
    const content = indicatorOnly ? ratioHtml(model) : (model.scope.type === 'branch' ? branchKinerjaHtml(model) : '<div class="prr-card-grid">' + metricDefs.filter(function (def) { return def.key !== 'npl'; }).map(function (def) { return cardHtml(model, def); }).join('') + '</div>' + scopeExtraHtml(model));
    return '<section id="prr-panel-' + esc(scope.id) + '" class="prr-panel' + (active ? ' active' : '') + '" data-prr-panel="' + esc(scope.id) + '" role="tabpanel"><div class="prr-panel-head"><div><h2>' + esc(title) + '</h2><p>' + esc(subtitle) + '</p></div><span class="prr-scope">' + esc(scope.label) + ' &middot; ' + esc(model.actualDate || state.closing) + '</span></div>' + content + '</section>';
    return '<section id="prr-panel-' + esc(scope.id) + '" class="prr-panel' + (active ? ' active' : '') + '" data-prr-panel="' + esc(scope.id) + '" role="tabpanel"><div class="prr-panel-head"><div><h2>' + esc(title) + '</h2><p>' + esc(subtitle) + '</p></div><span class="prr-scope">' + esc(scope.label) + ' · ' + esc(model.actualDate || state.closing) + '</span></div>' + (indicatorOnly ? ratioHtml(model) : '<div class="prr-card-grid">' + metricDefs.filter(function (def) { return def.key !== 'npl'; }).map(function (def) { return cardHtml(model, def); }).join('') + '</div>') + '</section>';
  }
  function selectActive() {
    const requested = new URLSearchParams(window.location.search).get('tab');
    state.active = state.selected.indexOf(requested) >= 0 ? requested : (state.selected[0] || '');
  }
  function render() {
    selectActive();
    const tabs = document.getElementById('prrTabs');
    const panels = document.getElementById('prrPanels');
    tabs.innerHTML = state.selected.map(function (id) { const scope = state.scopes.find(function (x) { return x.id === id; }); return scope ? '<button type="button" class="prr-tab' + (id === state.active ? ' active' : '') + '" role="tab" aria-selected="' + (id === state.active ? 'true' : 'false') + '" data-prr-tab="' + esc(id) + '">' + esc(scope.label) + '</button>' : ''; }).join('');
    panels.innerHTML = state.selected.map(function (id) { const scope = state.scopes.find(function (x) { return x.id === id; }); const model = state.models.get(id); return !scope ? '' : (model && !model.error ? panelHtml(model, scope.indicator === true, id === state.active) : '<section class="prr-panel' + (id === state.active ? ' active' : '') + '" data-prr-panel="' + esc(id) + '"><div class="prr-empty">' + esc(model && model.error ? model.error : 'Data belum tersedia') + '</div></section>'); }).join('');
    tabs.querySelectorAll('[data-prr-tab]').forEach(function (button) { button.addEventListener('click', function () { state.active = button.dataset.prrTab; tabs.querySelectorAll('.prr-tab').forEach(function (x) { const active = x.dataset.prrTab === state.active; x.classList.toggle('active', active); x.setAttribute('aria-selected', active ? 'true' : 'false'); }); panels.querySelectorAll('[data-prr-panel]').forEach(function (x) { x.classList.toggle('active', x.dataset.prrPanel === state.active); }); const url = new URL(window.location.href); url.searchParams.set('tab', state.active); window.history.replaceState({}, '', url.toString()); }); });
    panels.querySelectorAll('[data-prr-history]').forEach(function (card) { card.addEventListener('click', function () { openHistory(card); }); card.addEventListener('keydown', function (event) { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openHistory(card); } }); });
    panels.querySelectorAll('[data-prr-chart-toggle]').forEach(function (button) { button.addEventListener('click', function () { const box = button.closest('[data-prr-extra]'); const key = button.dataset.prrChartToggle; if (!box) return; box.querySelectorAll('[data-prr-chart-toggle]').forEach(function (item) { item.classList.toggle('active', item === button); }); box.querySelectorAll('[data-prr-chart-view]').forEach(function (view) { view.hidden = view.dataset.prrChartView !== key; }); }); });
    panels.querySelectorAll('[data-prr-branch-toggle]').forEach(function (button) { button.addEventListener('click', function () { const panel = button.closest('[data-prr-panel]'); const key = button.dataset.prrBranchToggle; if (!panel) return; panel.querySelectorAll('[data-prr-branch-toggle]').forEach(function (item) { item.classList.toggle('active', item === button); }); panel.querySelectorAll('[data-prr-branch-view]').forEach(function (view) { view.hidden = view.dataset.prrBranchView !== key; }); }); });
    panels.querySelectorAll('[data-prr-member-search]').forEach(function (input) { input.addEventListener('input', function () { const query = input.value.trim().toLowerCase(); const table = input.closest('.prr-members')?.querySelectorAll('[data-prr-member-row]') || []; table.forEach(function (row) { row.hidden = query !== '' && String(row.dataset.search || '').indexOf(query) < 0; }); }); });
    panels.querySelectorAll('[data-prr-panel]').forEach(function (x) { x.classList.toggle('active', x.dataset.prrPanel === state.active); });
  }
  function storedTabs() {
    try { const data = JSON.parse(localStorage.getItem(STORE_KEY) || 'null'); return Array.isArray(data) ? data : defaultTabs.slice(); } catch (e) { return defaultTabs.slice(); }
  }
  function renderSettings() {
    const body = document.getElementById('prrSettingsBody');
    const groups = [
      {title:'Pusat', items:state.scopes.filter(function (x) { return x.type === 'consolidated'; })},
      {title:'Korwil', items:state.scopes.filter(function (x) { return x.type === 'korwil'; })},
      {title:'Cabang', items:state.scopes.filter(function (x) { return x.type === 'branch'; })}
    ];
    body.innerHTML = groups.map(function (group) { return '<div class="prr-setting-group"><h3>' + esc(group.title) + '</h3>' + group.items.map(function (item) { return '<label class="prr-check"><input type="checkbox" value="' + esc(item.id) + '"' + (state.selected.indexOf(item.id) >= 0 ? ' checked' : '') + '><span>' + esc(item.label) + '</span></label>'; }).join('') + '</div>'; }).join('');
  }
  function collectSettings() { return Array.from(document.querySelectorAll('#prrSettingsBody input[type="checkbox"]:checked')).map(function (input) { return input.value; }); }
  function openModal(id) { const node = document.getElementById(id); if (node) { node.hidden = false; document.body.style.overflow = 'hidden'; } }
  function closeModal(id) { const node = document.getElementById(id); if (node) { node.hidden = true; document.body.style.overflow = ''; } }
  function formatHistoryValue(value, meta) { const n = num(value) || 0; if (meta && (meta.metric === 'npl' || String(meta.unit || '').toLowerCase().indexOf('persen') >= 0)) return displayRatio(n); return displayNominal(n); }
  function formatDate(value) { if (!value) return '-'; const d = new Date(String(value).slice(0,10) + 'T00:00:00'); return Number.isNaN(d.getTime()) ? value : new Intl.DateTimeFormat('id-ID', {day:'2-digit',month:'short',year:'numeric'}).format(d); }
  function historyChart(rows, meta) {
    const width=760,height=300,pad={top:20,right:20,bottom:45,left:65}, values=rows.map(function (x) { return num(x.nilai) || 0; }); let min=Math.min.apply(null,values),max=Math.max.apply(null,values); if (min === max) { const off=Math.abs(max || 1)*.08; min-=off; max+=off; } else { const off=(max-min)*.12; min-=off; max+=off; } const plotW=width-pad.left-pad.right,plotH=height-pad.top-pad.bottom,x=function (i) { return rows.length===1 ? pad.left+plotW/2 : pad.left+plotW*i/(rows.length-1); },y=function (v) { return pad.top+(max-v)/(max-min)*plotH; }; const points=rows.map(function (r,i) { return x(i).toFixed(2)+','+y(num(r.nilai)||0).toFixed(2); }).join(' '); const grid=[0,1,2,3,4].map(function (i) { const yy=pad.top+plotH*i/4, val=max-(max-min)*i/4; return '<line class="prr-grid" x1="'+pad.left+'" y1="'+yy+'" x2="'+(width-pad.right)+'" y2="'+yy+'"></line><text class="prr-axis" x="'+(pad.left-8)+'" y="'+(yy+4)+'" text-anchor="end">'+esc(formatHistoryValue(val,meta))+'</text>'; }).join(''); const labels=rows.map(function (r,i) { return (rows.length<=8 || i===0 || i===rows.length-1 || i%Math.ceil(rows.length/6)===0) ? '<text class="prr-axis" x="'+x(i)+'" y="'+(height-16)+'" text-anchor="middle">'+esc(r.label || formatDate(r.tanggal))+'</text>' : ''; }).join(''); const dots=rows.map(function (r,i) { return '<circle class="prr-dot" cx="'+x(i)+'" cy="'+y(num(r.nilai)||0)+'" r="4"><title>'+esc(formatDate(r.tanggal))+': '+esc(formatHistoryValue(r.nilai,meta))+'</title></circle>'; }).join(''); return '<svg viewBox="0 0 '+width+' '+height+'" role="img" aria-label="Grafik history realisasi"><g>'+grid+'</g><polygon class="prr-area" points="'+pad.left+','+(height-pad.bottom)+' '+points+' '+x(rows.length-1)+','+(height-pad.bottom)+'"></polygon><polyline class="prr-line" points="'+points+'"></polyline><g>'+dots+'</g><g>'+labels+'</g></svg>';
  }
  async function openHistory(card) {
    const modal=document.getElementById('prrHistoryModal'),body=document.getElementById('prrHistoryBody'),title=document.getElementById('prrHistoryTitle'),subtitle=document.getElementById('prrHistorySubtitle'),requestId=++state.historyRequest; openModal('prrHistoryModal'); title.textContent='History Realisasi'; subtitle.textContent='Mengambil snapshot closing dari acc_history...'; body.innerHTML='<div class="prr-loading"><span class="prr-spinner"></span><span>Memuat history...</span></div>'; const metric=card.dataset.prrHistory || 'aset',scopeType=card.dataset.prrScopeType || 'consolidated',scopeValue=card.dataset.prrScopeValue || '000',year=Number(card.dataset.prrYear || String(state.closing).slice(0,4)); const request={type:'realisasi_history',metric:metric,year:year,kode_kantor:'000'}; if (scopeType==='korwil') request.korwil=scopeValue; if (scopeType==='branch') request.kode_kantor=scopeValue;
    try { const data=await apiPost(API_RBB,request); if (requestId !== state.historyRequest) return; const meta=data.meta||{}, rows=Array.isArray(data.history)?data.history:[]; title.textContent=(meta.label || 'History Realisasi')+' · '+(meta.scope || ''); subtitle.textContent='Tahun '+(meta.year || year)+' · '+(meta.unit || 'Snapshot closing per bulan'); if (!rows.length) { body.innerHTML='<div class="prr-empty">Belum ada snapshot closing untuk pilihan ini.</div>'; return; } const latest=rows[rows.length-1],previous=rows.length>1?rows[rows.length-2]:null,growth=previous && num(previous.nilai)!==0 ? (num(latest.nilai)-num(previous.nilai))/Math.abs(num(previous.nilai))*100 : null; const totals=meta.totals && ['pendapatan','biaya','laba'].indexOf(meta.metric)>=0 ? '<div class="prr-history-meta"><div class="prr-stat"><span>Total tahun berjalan</span><b>'+esc(formatHistoryValue(meta.totals[meta.metric],meta))+'</b><small>Akumulasi tahun '+esc(meta.year || year)+'</small></div></div>' : ''; body.innerHTML='<div class="prr-history-meta"><div class="prr-stat"><span>'+ (meta.metric==='npl'?'NPL terbaru':'Closing terbaru') +'</span><b>'+esc(formatHistoryValue(latest.nilai,meta))+'</b><small>'+esc(formatDate(latest.tanggal))+'</small></div><div class="prr-stat"><span>Perubahan vs sebelumnya</span><b>'+esc(growth===null?'-':(growth>=0?'+':'')+fmt.format(growth)+'%')+'</b><small>'+esc(previous?formatDate(previous.tanggal):'Belum ada pembanding')+'</small></div><div class="prr-stat"><span>Jumlah snapshot</span><b>'+rows.length+' closing</b><small>Data tahun '+esc(meta.year || year)+'</small></div></div>'+totals+'<p class="prr-formula"><b>Rumus:</b> '+esc(meta.formula || '-')+' · '+esc(meta.scope || '')+(meta.calculation?' · '+esc(meta.calculation):'')+'</p><div class="prr-chart">'+historyChart(rows,meta)+'</div><div class="prr-values">'+rows.map(function (row) { return '<div class="prr-value"><span>'+esc(row.label || formatDate(row.tanggal))+'</span><b>'+esc(formatHistoryValue(row.nilai,meta))+'</b></div>'; }).join('')+'</div>'; } catch (error) { if (requestId===state.historyRequest) body.innerHTML='<div class="prr-empty">'+esc(error.message || 'History gagal dimuat.')+'</div>'; }
  }
  async function loadOffices() {
    const center={id:'kinerja_pusat',type:'consolidated',value:'000',label:'Kinerja Pusat',title:'Kinerja Pusat'};
    const indicator={id:'indikator_keuangan_pusat',type:'consolidated',value:'000',label:'Indikator Keuangan',title:'Indikator Keuangan',indicator:true};
    let offices=[];
    try { offices=await apiPost(API_KODE,{type:'kode_kantor'}) || []; } catch (e) {}
    const branches=offices.filter(function (x) { return x.kode_kantor && String(x.kode_kantor)!=='000'; }).map(function (x) { const code=String(x.kode_kantor).padStart(3,'0'); return {id:'cabang_'+code,type:'branch',value:code,label:'Cabang '+code+' · '+(x.nama_kantor || ''),title:'Kinerja Cabang '+code+' · '+(x.nama_kantor || '')}; });
    state.scopes=[center,indicator].concat(korwils).concat(branches);
    const saved=storedTabs(); state.selected=saved.filter(function (id) { return state.scopes.some(function (x) { return x.id===id; }); }); if (!state.selected.length) state.selected=[center.id,indicator.id];
    renderSettings();
  }
  async function load() {
    const status=document.getElementById('prrStatus'),panels=document.getElementById('prrPanels'); state.closing=document.getElementById('prrClosing').value; if (!state.closing) return; status.classList.remove('error'); status.textContent='Memuat '+state.selected.length+' tab...'; panels.innerHTML='<div class="prr-loading"><span class="prr-spinner"></span><span>Memuat data RBB sistem...</span></div>'; document.getElementById('prrPosition').textContent='Closing: '+state.closing; state.models=new Map();
    const dataScopes=state.scopes.filter(function (scope) { return state.selected.indexOf(scope.id)>=0 && !scope.indicator; }); const indicatorSelected=state.selected.indexOf('indikator_keuangan_pusat')>=0; const centralScope=state.scopes.find(function (scope) { return scope.id==='kinerja_pusat'; }); if (indicatorSelected && centralScope && !dataScopes.some(function (scope) { return scope.id===centralScope.id; })) dataScopes.push(centralScope); const unique={}; dataScopes.forEach(function (scope) { unique[scope.type+'|'+scope.value]=scope; }); const jobs=Object.values(unique).map(function (scope) { return loadModel(scope).then(function (model) { dataScopes.filter(function (x) { return x.type===scope.type && x.value===scope.value; }).forEach(function (x) { state.models.set(x.id,model); }); }).catch(function (error) { dataScopes.filter(function (x) { return x.type===scope.type && x.value===scope.value; }).forEach(function (x) { state.models.set(x.id,{scope:x,error:error.message || 'Data gagal dimuat'}); }); }); });
    await Promise.all(jobs); const central=state.models.get('kinerja_pusat'); if (state.selected.indexOf('indikator_keuangan_pusat')>=0 && central) state.models.set('indikator_keuangan_pusat',central); state.actualDate=central && central.actualDate ? central.actualDate : state.closing; render(); document.getElementById('prrLoadedAt').textContent='Snapshot actual: '+state.actualDate; status.textContent=state.selected.length+' tab aktif'; 
  }
  async function initDates() { try { const response=await (window.apiFetch ? window.apiFetch(API_DATE,{cache:'no-store'}) : fetch(API_DATE,{cache:'no-store'})); const json=await response.json(); const data=json.data||{}; document.getElementById('prrClosing').value=data.last_closing || data.last_created || new Date().toISOString().slice(0,10); } catch (e) { document.getElementById('prrClosing').value=new Date().toISOString().slice(0,10); } }
  document.getElementById('prrSettingsOpen').addEventListener('click',function () { renderSettings(); openModal('prrSettingsModal'); }); document.getElementById('prrReload').addEventListener('click',load); document.getElementById('prrClosing').addEventListener('change',load); document.querySelectorAll('[data-prr-close]').forEach(function (button) { button.addEventListener('click',function () { closeModal(button.dataset.prrClose==='history'?'prrHistoryModal':'prrSettingsModal'); }); }); document.querySelectorAll('.prr-modal').forEach(function (modal) { modal.addEventListener('click',function (event) { if (event.target===modal) closeModal(modal.id); }); }); document.getElementById('prrSettingsAll').addEventListener('click',function () { document.querySelectorAll('#prrSettingsBody input').forEach(function (input) { input.checked=true; }); }); document.getElementById('prrSettingsReset').addEventListener('click',function () { document.querySelectorAll('#prrSettingsBody input').forEach(function (input) { input.checked=defaultTabs.indexOf(input.value)>=0; }); }); document.getElementById('prrSettingsSave').addEventListener('click',function () { const selected=collectSettings(); if (!selected.length) { alert('Pilih minimal satu tab paparan.'); return; } state.selected=selected; try { localStorage.setItem(STORE_KEY,JSON.stringify(selected)); } catch (e) {} closeModal('prrSettingsModal'); load(); }); document.addEventListener('keydown',function (event) { if (event.key==='Escape') { closeModal('prrSettingsModal'); closeModal('prrHistoryModal'); } });
  (async function () { await initDates(); await loadOffices(); await load(); })();
})();
</script>

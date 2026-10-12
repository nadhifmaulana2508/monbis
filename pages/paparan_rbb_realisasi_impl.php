<?php
/*
 * Paparan RBB vs Realisasi untuk direksi.
 * Target berasal dari tabel RBB sistem, realisasi berasal dari snapshot
 * yang sama dengan menu Ikhtisar. Pengaturan wilayah dan panel disimpan
 * pada akun pengguna agar dashboard siap untuk kebutuhan masing-masing Direksi.
 */
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<div id="paparanRbbRealisasi" class="prr-page">
  <header class="prr-header">
    <div class="prr-brand">
      <div class="prr-brand-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5"/><path d="M4 19h16"/><path d="m7 15 3-4 3 2 5-6"/></svg>
      </div>
      <div>
        <div class="prr-kicker">PAPARAN DIREKSI · RBB</div>
        <h1>Dashboard Executive</h1>
        <p>Ringkasan kinerja dan risiko sesuai periode closing.</p>
      </div>
    </div>
    <div class="prr-controls">
      <label><span>PERIODE CLOSING</span><input id="prrClosing" type="date" aria-label="Periode closing"></label>
      <label><span>WILAYAH / KANTOR</span><select id="prrScope" aria-label="Wilayah dashboard"></select></label>
      <button id="prrSettingsOpen" type="button" class="prr-btn prr-btn-light prr-settings-button" title="Pengaturan dashboard" aria-label="Pengaturan dashboard"><svg class="prr-settings-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h9m4 0h3M4 12h3m4 0h9M4 18h9m4 0h3"/><circle cx="15" cy="6" r="2"/><circle cx="9" cy="12" r="2"/><circle cx="15" cy="18" r="2"/></svg></button>
    </div>
  </header>

  <section class="prr-workspace">
    <div class="prr-note">
      <span><b>SUMBER:</b> RBB sistem + realisasi Ikhtisar</span>
      <span id="prrPosition">Closing: -</span>
      <span id="prrStatus" class="prr-status">Menyiapkan data...</span>
    </div>
    <main id="prrPanels" class="prr-panels">
      <div class="prr-loading"><span class="prr-spinner"></span><span>Memuat data RBB sistem...</span></div>
    </main>
    <footer class="prr-footnote">
      <span>Klik kartu nominal untuk melihat history closing dari <b>acc_history</b>.</span>
      <span id="prrLoadedAt">-</span>
    </footer>
  </section>
</div>

<div id="prrSettingsModal" class="prr-modal" hidden role="dialog" aria-modal="true" aria-labelledby="prrSettingsTitle">
  <div class="prr-dialog prr-settings-dialog">
    <div class="prr-dialog-head">
      <div><div class="prr-kicker">KONFIGURASI DASHBOARD</div><h2 id="prrSettingsTitle">Atur wilayah dan panel</h2><p>Pilihan ini tersimpan di akun Anda dan dipakai kembali saat login.</p></div>
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
  #paparanRbbRealisasi{--prr-navy:#0d3154;--prr-blue:#2563eb;--prr-teal:#1687a5;--prr-line:#d8e5ec;--prr-bg:#f5f9fc;--prr-font-scale:1.15;min-height:calc(100vh - 62px);padding:14px;background:var(--prr-bg);color:var(--prr-navy);font-family:Roboto,Arial,sans-serif;zoom:var(--prr-font-scale)}
  #paparanRbbRealisasi *{box-sizing:border-box}.prr-header{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:15px 17px;border:1px solid #d0dfeb;border-radius:17px;background:#fff;box-shadow:0 5px 18px rgba(12,69,117,.08)}.prr-brand{display:flex;align-items:center;gap:12px;min-width:0}.prr-brand-icon{display:grid;place-items:center;width:43px;height:43px;flex:0 0 auto;border-radius:12px;background:#2563eb;color:#fff}.prr-brand-icon svg{width:23px;height:23px}.prr-kicker{margin-bottom:3px;color:#2082a0;font-size:8px;font-weight:950;letter-spacing:.14em}.prr-brand h1{margin:0;color:#0d3154;font-size:22px;line-height:1.15;font-weight:950;letter-spacing:-.03em}.prr-brand p{margin:4px 0 0;color:#72889a;font-size:10px;font-weight:650}.prr-controls{display:flex;align-items:end;gap:8px;flex:0 0 auto}.prr-controls label{display:flex;flex-direction:column;gap:4px}.prr-controls label span{padding-left:2px;color:#49637a;font-size:8px;font-weight:950;letter-spacing:.08em}.prr-controls input{height:36px;width:145px;padding:0 9px;border:1px solid #c7d8e6;border-radius:9px;background:#fff;color:#153956;font-size:10px;font-weight:850}.prr-btn{height:36px;padding:0 12px;border:1px solid #cbdbe5;border-radius:9px;font-size:10px;font-weight:900;cursor:pointer;white-space:nowrap}.prr-btn-primary{border-color:#2563eb;background:#2563eb;color:#fff;box-shadow:0 5px 12px rgba(37,99,235,.18)}.prr-btn-light{background:#fff;color:#49637a}.prr-btn-light:hover{background:#f1f8fc;border-color:#9fc6d7}.prr-workspace{position:relative;margin-top:12px;overflow:hidden;border:1px solid #d7e2ec;border-radius:16px;background:#fff;box-shadow:0 4px 16px rgba(15,60,91,.05)}.prr-note{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding:9px 13px;border-bottom:1px solid #dce7ee;background:#fbfdfe;color:#637d8d;font-size:9px}.prr-note span{padding:4px 8px;border:1px solid #dbe5ec;border-radius:999px;background:#fff}.prr-note b{color:#37748c}.prr-status{background:#ecfdf5!important;border-color:#b7ead7!important;color:#047857}.prr-status.error{background:#fff1f2!important;border-color:#fecdd3!important;color:#be123c}.prr-tabs{display:flex;gap:4px;padding:9px 12px 0;overflow:auto;border-bottom:1px solid #dce7ee;background:#fbfdfe}.prr-tab{padding:9px 12px;border:1px solid transparent;border-radius:9px 9px 0 0;background:transparent;color:#6d8798;font-size:10px;font-weight:950;cursor:pointer;white-space:nowrap}.prr-tab:hover{background:#f0f8fa;color:#1f6b86}.prr-tab.active{border-color:#cfe1e8;border-bottom-color:#fff;background:#fff;color:#0f3c5b}.prr-panels{padding:14px}.prr-panel{display:none;animation:prrIn .2s ease}.prr-panel.active{display:block}@keyframes prrIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}.prr-panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:13px}.prr-panel-head h2{margin:0;color:#123c5b;font-size:17px;font-weight:950}.prr-panel-head p{margin:4px 0 0;color:#7890a0;font-size:9px;font-weight:650}.prr-scope{padding:5px 9px;border:1px solid #cfe1e8;border-radius:999px;color:#397d93;background:#f3fbfd;font-size:8px;font-weight:900;white-space:nowrap}.prr-card-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}.prr-card{position:relative;min-width:0;padding:12px;border:1px solid #dce8ef;border-radius:13px;background:#fff;box-shadow:0 4px 12px rgba(15,60,91,.05);cursor:pointer;transition:transform .15s ease,box-shadow .15s ease,border-color .15s ease}.prr-card:hover{transform:translateY(-2px);border-color:#8dc2d5;box-shadow:0 9px 18px rgba(15,60,91,.1)}.prr-card:focus-visible{outline:3px solid rgba(37,99,235,.2);outline-offset:2px}.prr-card:after{content:'↗';position:absolute;right:9px;top:8px;display:grid;place-items:center;width:18px;height:18px;border:1px solid #c9e1ee;border-radius:50%;background:#f2faff;color:#19739a;font-size:11px;font-weight:950}.prr-card-head{display:flex;align-items:center;gap:8px;padding-right:20px}.prr-card-icon{display:grid;place-items:center;width:29px;height:29px;border-radius:9px;background:#eff6ff;color:#2563eb;font-size:10px;font-weight:950}.prr-card-head h3{margin:0;color:#173c58;font-size:12px;font-weight:950}.prr-card-head small{display:block;margin-top:2px;color:#7b929f;font-size:8px;font-weight:700}.prr-card-values{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:5px;margin-top:10px}.prr-card-values div{min-width:0;padding:7px 5px;border-radius:8px;background:#f8fbfd}.prr-card-values span,.prr-ratio-table span{display:block;color:#78909e;font-size:7px;font-weight:850;text-transform:uppercase;line-height:1.2}.prr-card-values b{display:block;margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#153b59;font-size:11px;font-weight:950}.prr-card-values .actual b{color:#0f766e}.prr-card-foot{margin-top:8px;color:#7b929f;font-size:8px;font-weight:750}.prr-card.is-ratio .prr-card-values b{font-size:14px}.prr-empty{display:grid;place-items:center;min-height:170px;color:#7890a0;font-size:10px;font-weight:800}.prr-loading{display:flex;align-items:center;justify-content:center;gap:8px;min-height:280px;color:#397d93;font-size:10px;font-weight:850}.prr-spinner{width:20px;height:20px;border:3px solid #c8e5eb;border-top-color:#1d7891;border-radius:50%;animation:prrSpin .75s linear infinite}@keyframes prrSpin{to{transform:rotate(360deg)}}.prr-ratio-table{width:100%;overflow:auto;border:1px solid #dce8ef;border-radius:12px}.prr-ratio-table table{width:100%;min-width:720px;border-collapse:collapse}.prr-ratio-table th,.prr-ratio-table td{padding:9px 10px;border-bottom:1px solid #e4edf2;text-align:right;font-size:10px}.prr-ratio-table th:first-child,.prr-ratio-table td:first-child{text-align:left}.prr-ratio-table thead th{background:#eef8fa;color:#37677a;font-size:8px;font-weight:950;text-transform:uppercase}.prr-ratio-table tbody th{color:#173c58;font-weight:850}.prr-ratio-table tbody td{color:#153b59;font-family:ui-monospace,monospace;font-weight:850}.prr-ratio-table tbody tr:last-child th,.prr-ratio-table tbody tr:last-child td{border-bottom:0}.prr-good{color:#07815f!important}.prr-bad{color:#be3a42!important}.prr-footnote{display:flex;justify-content:space-between;gap:12px;padding:10px 15px;border-top:1px solid #e0eaf0;color:#7b909e;font-size:9px}.prr-footnote b{color:#496e81}.prr-modal{position:fixed;inset:0;z-index:10000;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(7,30,55,.62);backdrop-filter:blur(6px)}.prr-modal[hidden]{display:none}.prr-dialog{width:min(760px,100%);max-height:min(780px,calc(100dvh - 30px));overflow:hidden;border:1px solid #cfe1ec;border-radius:18px;background:#fff;box-shadow:0 22px 70px rgba(7,47,78,.28)}.prr-dialog-head{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:18px 20px 14px;border-bottom:1px solid #e1edf3;background:linear-gradient(135deg,#f8fdff,#fff)}.prr-dialog-head h2{margin:4px 0 0;color:#0d3154;font-size:19px;font-weight:950}.prr-dialog-head p{margin:4px 0 0;color:#728b9a;font-size:9px;font-weight:700}.prr-close{display:grid;place-items:center;width:31px;height:31px;border:1px solid #d2e1e9;border-radius:9px;background:#fff;color:#527083;font-size:22px;line-height:1;cursor:pointer}.prr-settings-body{max-height:calc(100dvh - 240px);overflow:auto;padding:15px 20px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.prr-setting-group{padding:10px;border:1px solid #dceaf1;border-radius:11px;background:#f8fcfe}.prr-setting-group h3{margin:0 0 8px;color:#17657e;font-size:9px;font-weight:950;text-transform:uppercase;letter-spacing:.07em}.prr-check{display:flex;align-items:center;gap:8px;padding:6px 4px;color:#254c65;font-size:10px;font-weight:800;cursor:pointer}.prr-check input{accent-color:#2563eb}.prr-dialog-actions{display:flex;justify-content:flex-end;gap:8px;padding:12px 20px;border-top:1px solid #e1edf3;background:#fbfdfe}.prr-history-dialog{width:min(900px,100%)}.prr-history-body{max-height:calc(100dvh - 145px);overflow:auto;padding:16px 20px 20px}.prr-history-meta{display:flex;align-items:stretch;gap:8px;flex-wrap:wrap;margin-bottom:13px}.prr-stat{min-width:145px;flex:1 1 145px;padding:9px 11px;border:1px solid #dceaf1;border-radius:10px;background:#f8fcfe}.prr-stat span{display:block;color:#78909e;font-size:8px;font-weight:850;text-transform:uppercase}.prr-stat b{display:block;margin-top:4px;color:#123f5e;font-size:13px;font-weight:950}.prr-stat small{display:block;margin-top:3px;color:#78909e;font-size:8px;font-weight:700}.prr-formula{margin:0 0 12px;padding:8px 10px;border-left:3px solid #25a1bb;border-radius:5px;background:#effafc;color:#527383;font-size:9px;font-weight:750}.prr-chart{overflow:hidden;padding:10px 8px 4px;border:1px solid #dceaf1;border-radius:13px;background:#fff}.prr-chart svg{display:block;width:100%;height:auto;min-height:250px}.prr-axis{fill:#78909e;font-size:10px;font-weight:700}.prr-grid{stroke:#e5eef3;stroke-width:1}.prr-area{fill:rgba(37,145,190,.12)}.prr-line{fill:none;stroke:#1687b1;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}.prr-dot{fill:#fff;stroke:#1687b1;stroke-width:2}.prr-values{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:6px;margin-top:12px}.prr-value{padding:7px 9px;border:1px solid #e3edf2;border-radius:8px;background:#fbfdfe}.prr-value span{display:block;color:#78909e;font-size:8px;font-weight:800}.prr-value b{display:block;margin-top:3px;color:#254c65;font-size:10px;font-family:ui-monospace,monospace}@media(max-width:1050px){.prr-card-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}@media(max-width:760px){#paparanRbbRealisasi{padding:9px}.prr-header{align-items:flex-start;flex-direction:column}.prr-controls{width:100%;display:grid;grid-template-columns:1fr auto auto}.prr-controls input{width:100%}.prr-card-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.prr-settings-body{grid-template-columns:1fr}}@media(max-width:520px){.prr-brand h1{font-size:19px}.prr-brand p{font-size:8px}.prr-controls{grid-template-columns:1fr 1fr}.prr-controls label{grid-column:1/-1}.prr-btn{padding:0 8px;font-size:9px}.prr-panels{padding:10px}.prr-card-grid{grid-template-columns:1fr}.prr-panel-head{flex-direction:column}.prr-footnote{align-items:flex-start;flex-direction:column;gap:4px}.prr-dialog-head{padding:14px}.prr-settings-body,.prr-dialog-actions,.prr-history-body{padding-left:12px;padding-right:12px}}
</style>

<style>
  .prr-indicator-extra{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,.85fr);gap:12px;margin-top:12px}.prr-extra-box{min-width:0;padding:12px;border:1px solid #dce8ef;border-radius:12px;background:#fff}.prr-extra-box h3{margin:0;color:#17657e;font-size:11px;font-weight:950}.prr-extra-box p{margin:3px 0 9px;color:#7890a0;font-size:8px;font-weight:700}.prr-rr-summary{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:8px}.prr-rr-stat{min-width:105px;flex:1;padding:7px 8px;border-radius:8px;background:#f8fbfd}.prr-rr-stat span{display:block;color:#78909e;font-size:7px;font-weight:850;text-transform:uppercase}.prr-rr-stat b{display:block;margin-top:3px;color:#153b59;font-size:13px;font-weight:950}.prr-rr-chart{overflow:hidden;border:1px solid #e1edf2;border-radius:9px;background:#fff}.prr-rr-chart svg{display:block;width:100%;height:auto;min-height:190px}.prr-rr-axis{fill:#78909e;font-size:9px;font-weight:700}.prr-rr-grid{stroke:#e5eef3;stroke-width:1}.prr-rr-area{fill:rgba(22,135,177,.12)}.prr-rr-line{fill:none;stroke:#1687b1;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}.prr-rr-dot{fill:#fff;stroke:#1687b1;stroke-width:2}.prr-kolek-table{width:100%;border-collapse:collapse}.prr-kolek-table th,.prr-kolek-table td{padding:7px 5px;border-bottom:1px solid #e4edf2;font-size:9px}.prr-kolek-table th{text-align:left;color:#37677a;font-weight:950}.prr-kolek-table td{text-align:right;color:#153b59;font-family:ui-monospace,monospace;font-weight:850}.prr-kolek-table tr:last-child th,.prr-kolek-table tr:last-child td{border-bottom:0;background:#eef8fa;font-weight:950}.prr-kolek-table .prr-kolek-pct{color:#07815f}.prr-kolek-table .prr-kolek-npl{color:#be3a42}@media(max-width:850px){.prr-indicator-extra{grid-template-columns:1fr}}@media(max-width:520px){.prr-extra-box{padding:9px}.prr-rr-stat{min-width:92px}.prr-rr-stat b{font-size:11px}}
</style>

<style>
  .prr-kolek-table{table-layout:fixed}.prr-kolek-table th:nth-child(1){width:22%}.prr-kolek-table th:nth-child(2){width:17%}.prr-kolek-table th:nth-child(3){width:15%}.prr-kolek-table th:nth-child(4),.prr-kolek-table th:nth-child(5){width:23%}.prr-kolek-table thead th small{display:block;margin-top:2px;color:#78909e;font-size:6px;font-weight:750;text-transform:none;white-space:nowrap}.prr-kolek-compare{text-align:right;line-height:1.2}.prr-kolek-compare>b{display:block;font-size:8px;white-space:nowrap}.prr-kolek-compare>small{display:block;margin-top:2px;color:#8794a2;font-family:Roboto,Arial,sans-serif;font-size:7px;font-weight:750;white-space:nowrap}.prr-kolek-no-data{color:#a0adba}
</style>

<style>
  .prr-extra-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:7px}.prr-extra-head h3{margin:0}.prr-chart-toggle{display:flex;gap:3px;padding:3px;border:1px solid #dce8ef;border-radius:8px;background:#f8fbfd}.prr-chart-toggle button{padding:5px 9px;border:0;border-radius:6px;background:transparent;color:#78909e;font-size:8px;font-weight:950;cursor:pointer}.prr-chart-toggle button.active{background:#1687a5;color:#fff;box-shadow:0 2px 5px rgba(22,135,165,.18)}.prr-chart-view[hidden]{display:none!important}.prr-chart-caption{margin:-2px 0 7px;color:#7890a0;font-size:8px;font-weight:750}
  .prr-branch-switch{display:flex;gap:4px;margin-bottom:12px;padding:4px;border:1px solid #dce8ef;border-radius:10px;background:#f8fbfd;width:max-content}.prr-branch-switch button{padding:7px 12px;border:0;border-radius:7px;background:transparent;color:#6d8798;font-size:9px;font-weight:950;cursor:pointer}.prr-branch-switch button.active{background:#2563eb;color:#fff;box-shadow:0 3px 8px rgba(37,99,235,.18)}.prr-members{padding:12px;border:1px solid #dce8ef;border-radius:12px;background:#fff}.prr-members-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:11px}.prr-members-head h3{margin:0;color:#17657e;font-size:13px;font-weight:950}.prr-members-head p{margin:3px 0 0;color:#7890a0;font-size:8px;font-weight:700}.prr-members-head input{width:220px;height:32px;padding:0 9px;border:1px solid #cfe1e8;border-radius:8px;color:#254c65;font-size:9px;font-weight:750;outline:none}.prr-members-head input:focus{border-color:#5baac0;box-shadow:0 0 0 3px rgba(91,170,192,.12)}.prr-member-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:7px;margin-bottom:11px}.prr-member-stat{padding:8px 9px;border-radius:8px;background:#f8fbfd}.prr-member-stat span{display:block;color:#78909e;font-size:7px;font-weight:850;text-transform:uppercase}.prr-member-stat b{display:block;margin-top:3px;color:#153b59;font-size:13px;font-weight:950}.prr-members-table{width:100%;overflow:auto;border:1px solid #dce8ef;border-radius:9px}.prr-members-table table{width:100%;min-width:1120px;border-collapse:collapse}.prr-members-table th,.prr-members-table td{padding:8px 7px;border-bottom:1px solid #e4edf2;text-align:left;color:#254c65;font-size:9px;white-space:nowrap}.prr-members-table thead th{background:#eef8fa;color:#37677a;font-size:7px;font-weight:950;text-transform:uppercase}.prr-members-table tbody th{color:#153b59;font-weight:900}.prr-members-table tbody tr:last-child th,.prr-members-table tbody tr:last-child td{border-bottom:0}.prr-member-empty{text-align:center!important;color:#7890a0!important;padding:22px!important}.prr-members-table tr[hidden]{display:none}
  @media(max-width:650px){.prr-extra-head,.prr-members-head{flex-direction:column}.prr-chart-toggle,.prr-branch-switch{width:100%}.prr-chart-toggle button,.prr-branch-switch button{flex:1}.prr-members-head input{width:100%}.prr-member-summary{grid-template-columns:repeat(2,minmax(0,1fr))}
  }
</style>

<style>
  .prr-settings-body{align-content:start}
  .prr-settings-button{display:inline-flex;align-items:center;justify-content:center;width:36px;min-width:36px;padding:0}.prr-settings-icon{display:block;width:17px;height:17px;flex:0 0 auto}
  @media(max-width:760px){.prr-controls #prrSettingsOpen{grid-column:1/-1;justify-self:end}}
  .prr-settings-section{grid-column:1/-1;padding:12px;border:1px solid #dceaf1;border-radius:12px;background:#fff}
  .prr-settings-section-head{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:9px}
  .prr-settings-section-head h3{margin:0;color:#17657e;font-size:11px;font-weight:950}
  .prr-settings-section-head p{margin:3px 0 0;color:#7890a0;font-size:8px;font-weight:700}
  .prr-settings-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px}
  .prr-widget-option{display:flex;align-items:flex-start;gap:8px;padding:9px;border:1px solid #e0ebf0;border-radius:9px;background:#f8fcfe}
  .prr-widget-option:hover{border-color:#9fc9d8;background:#f2fbfd}
  .prr-widget-option input{margin-top:2px;accent-color:#2563eb}
  .prr-widget-option span{display:block;color:#254c65;font-size:9px;font-weight:900}
  .prr-widget-option small{display:block;margin-top:2px;color:#7890a0;font-size:7px;line-height:1.35;font-weight:700}
  .prr-pending-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:12px}
  .prr-widget-pending{display:flex;align-items:flex-start;gap:10px;min-height:88px;padding:11px 12px;border:1px dashed #b9d4df;border-radius:11px;background:#f7fcfe;color:#527383;font-size:9px;font-weight:750}
  .prr-widget-pending b{color:#17657e}
  .prr-widget-pending strong{display:block;margin-bottom:3px;color:#17657e;font-size:10px}
  .prr-widget-pending small{display:block;color:#7890a0;font-size:8px;line-height:1.4}
  @media(max-width:850px){.prr-pending-grid{grid-template-columns:1fr}}
  @media(max-width:520px){.prr-settings-grid{grid-template-columns:1fr}.prr-settings-section-head{flex-direction:column}}
</style>

<style>
  #paparanRbbRealisasi{padding:12px;background:#f3f6fa}
  .prr-header{padding:12px 15px;border-radius:14px}
  .prr-brand h1{font-size:20px}
  .prr-controls select{height:36px;min-width:155px;max-width:220px;padding:0 28px 0 9px;border:1px solid #c7d8e6;border-radius:9px;background:#fff;color:#153956;font-size:10px;font-weight:850}
  .prr-workspace{overflow:visible;border:0;background:transparent;box-shadow:none}
  .prr-note{margin-bottom:9px;border:1px solid #dce7ee;border-radius:11px;background:#fff}
  .prr-panels{padding:0}
  .prr-dashboard-grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:10px;align-items:stretch}
  .prr-kpi-grid{grid-column:1/-1;display:flex;flex-direction:column;gap:8px}
  .prr-kpi-row{display:grid;gap:8px}.prr-kpi-row-top{grid-template-columns:repeat(4,minmax(0,1fr))}.prr-kpi-row-bottom{grid-template-columns:repeat(5,minmax(0,1fr))}
  .prr-kpi{--prr-kpi-color:#2874d0;min-width:0;padding:10px;border:1px solid #e0e7ef;border-radius:12px;background:#fff;box-shadow:0 3px 10px rgba(24,55,82,.05);transition:transform .15s ease,box-shadow .15s ease}
  .prr-kpi.is-history{cursor:pointer}.prr-kpi.is-history:hover{transform:translateY(-2px);box-shadow:0 8px 18px rgba(24,55,82,.1)}
  .prr-kpi-blue{--prr-kpi-color:#2874d0}.prr-kpi-green{--prr-kpi-color:#22a45a}.prr-kpi-violet{--prr-kpi-color:#8053cf}.prr-kpi-orange{--prr-kpi-color:#e69a22}.prr-kpi-red{--prr-kpi-color:#e4444c}.prr-kpi-teal{--prr-kpi-color:#16a7a2}.prr-kpi-cyan{--prr-kpi-color:#1c9fbd}
  .prr-kpi-top{display:flex;align-items:center;gap:8px;min-width:0}.prr-kpi-icon{display:grid;place-items:center;flex:0 0 31px;width:31px;height:31px;border-radius:50%;background:color-mix(in srgb,var(--prr-kpi-color) 13%,white);color:var(--prr-kpi-color);font-size:13px;font-weight:950}
  .prr-kpi-top>div{min-width:0}.prr-kpi-top small{display:block;overflow:hidden;color:#718196;font-size:7px;font-weight:900;letter-spacing:.035em;text-overflow:ellipsis;white-space:nowrap}.prr-kpi-top strong{display:block;margin-top:3px;overflow:hidden;color:#1c2e43;font-size:15px;font-weight:950;text-overflow:ellipsis;white-space:nowrap}
  .prr-mini-line{display:block;width:100%;height:23px;margin:6px 0 3px}.prr-mini-line polyline{fill:none;stroke:var(--prr-kpi-color);stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}.prr-mini-track{height:2px;margin:16px 0 14px;background:#edf1f5}.prr-mini-track i{display:block;height:100%;background:var(--prr-kpi-color)}
  .prr-kpi-foot{display:flex;justify-content:space-between;gap:4px;color:#7b8998;font-size:7px;font-weight:750}.prr-kpi-foot span,.prr-kpi-foot b{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.prr-kpi-foot b{color:var(--prr-kpi-color)}.prr-kpi-meter{height:3px;margin-top:6px;overflow:hidden;border-radius:99px;background:#edf1f5}.prr-kpi-meter i{display:block;height:100%;border-radius:inherit;background:var(--prr-kpi-color)}
  .prr-dashboard-panel{min-width:0;padding:12px;border:1px solid #e0e7ef;border-radius:13px;background:#fff;box-shadow:0 3px 10px rgba(24,55,82,.045)}
  .prr-dashboard-panel>header{display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:10px}.prr-dashboard-panel>header h2{margin:0;color:#263a51;font-size:12px;font-weight:950}.prr-dashboard-panel>header p{margin:3px 0 0;color:#8492a2;font-size:8px;font-weight:700}
  .prr-span-3{grid-column:span 3}.prr-span-4{grid-column:span 4}.prr-span-5{grid-column:span 5}.prr-span-8{grid-column:span 8}.prr-span-12{grid-column:1/-1}
  .prr-trend-legend{display:flex;justify-content:center;gap:12px;flex-wrap:wrap;margin:0 0 5px}.prr-trend-legend span{display:flex;align-items:center;gap:4px;color:#718196;font-size:7px;font-weight:800}.prr-trend-legend i{width:7px;height:7px;border-radius:50%}.prr-finance-chart{display:block;width:100%;height:auto;min-height:200px}.prr-trend-grid{stroke:#e7edf3;stroke-width:1}.prr-trend-axis{fill:#8390a0;font-size:9px;font-weight:700}
  .prr-pending-panel{display:flex;align-items:center;gap:10px;min-height:100px;padding:12px;border:1px dashed #cfdae5;border-radius:10px;background:linear-gradient(135deg,#fbfdff,#f6f9fc);color:#75869a}.prr-pending-panel>span{display:grid;place-items:center;flex:0 0 32px;width:32px;height:32px;border-radius:10px;background:#edf4fa;color:#4c7d9c;font-size:14px;font-weight:950}.prr-pending-panel b{display:block;color:#536c80;font-size:9px}.prr-pending-panel small{display:block;margin-top:4px;font-size:8px;line-height:1.45}
  .prr-risk-list{display:grid;gap:7px}.prr-risk-row{display:grid;grid-template-columns:1fr auto;gap:2px 8px;padding:8px;border:1px solid #edf1f5;border-radius:9px;background:#fbfcfe}.prr-risk-row>span{color:#607387;font-size:8px;font-weight:850}.prr-risk-row>b{grid-column:2;grid-row:1/3;color:#1d3045;font-size:12px;font-weight:950}.prr-risk-row>small{color:#95a0ad;font-size:7px}.prr-risk-row>em{grid-column:1/-1;margin-top:2px;font-size:7px;font-style:normal;font-weight:900}.prr-rbb-table{overflow:hidden;border:1px solid #e8edf2;border-radius:9px}.prr-rbb-table table{width:100%;min-width:0;table-layout:fixed;border-collapse:collapse}.prr-rbb-table th,.prr-rbb-table td{padding:8px 6px;border-bottom:1px solid #edf1f5;text-align:right;color:#506378;font-size:8px;white-space:nowrap}.prr-rbb-table th:nth-child(1){width:22%}.prr-rbb-table th:nth-child(2){width:12%}.prr-rbb-table th:nth-child(3){width:13%}.prr-rbb-table th:nth-child(4){width:18%}.prr-rbb-table th:nth-child(5){width:13%}.prr-rbb-table th:nth-child(6){width:22%}.prr-rbb-table thead th{color:#8794a2;font-size:7px;font-weight:900;text-transform:uppercase}.prr-rbb-table th:first-child{text-align:left}.prr-rbb-table tbody th{color:#344a60;font-weight:900}.prr-rbb-table tr:last-child th,.prr-rbb-table tr:last-child td{border-bottom:0}.prr-progress{width:76px;height:5px;margin-left:auto;overflow:hidden;border-radius:99px;background:#edf1f5}.prr-progress i{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#2367d1,#57a6e8)}
  .prr-alert-list{display:grid;gap:5px}.prr-alert-row{display:grid;grid-template-columns:23px 1fr auto;align-items:center;gap:7px;padding:7px 6px;border-bottom:1px solid #edf1f5}.prr-alert-mark{display:grid;place-items:center;width:20px;height:20px;border-radius:50%;background:#fff3e2;color:#d78614;font-size:11px;font-weight:950}.prr-alert-row b{display:block;color:#43596e;font-size:8px}.prr-alert-row small{display:block;margin-top:2px;color:#8794a2;font-size:7px}.prr-alert-row em{color:#cf7e16;font-size:7px;font-style:normal;font-weight:900}.prr-alert-clear{display:flex;align-items:center;gap:8px;min-height:75px;color:#31815f;font-size:9px;font-weight:800}.prr-alert-clear b{display:grid;place-items:center;width:25px;height:25px;border-radius:50%;background:#e6f7ee}
  .prr-ranking-list{display:grid}.prr-ranking-row{display:grid;grid-template-columns:23px minmax(0,1fr) auto;align-items:center;gap:4px 7px;padding:8px 4px;border-bottom:1px solid #edf1f5}.prr-ranking-row:last-child{border-bottom:0}.prr-ranking-index{grid-row:1/3;color:#91a0ae;font-size:8px;font-weight:900}.prr-ranking-row>b{overflow:hidden;color:#40566b;font-size:8px;text-overflow:ellipsis;white-space:nowrap}.prr-ranking-row>small{color:#8b98a6;font-size:7px}.prr-ranking-row>strong{grid-column:3;grid-row:1/3;color:#268758;font-size:10px;font-weight:950}
  .prr-ratio-table{max-height:360px}.prr-ratio-table table{min-width:650px}
  .prr-credit-plan-split{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));align-items:stretch;gap:9px;margin-top:10px}.prr-credit-plan,.prr-credit-runoff{min-width:0;margin:0;padding:10px;border:1px solid #e2eaf1;border-radius:10px;background:#fbfdff}.prr-credit-plan-head{display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px}.prr-credit-plan-head h3,.prr-credit-runoff h3{margin:0;color:#344a60;font-size:9px;font-weight:950}.prr-credit-plan-head p,.prr-credit-runoff p{margin:3px 0 0;color:#8290a0;font-size:7px;font-weight:700}.prr-credit-plan-badge{flex:0 0 auto;padding:4px 7px;border-radius:99px;background:#eaf5fb;color:#28728a;font-size:7px;font-weight:900}.prr-credit-plan-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:5px;margin-bottom:8px}.prr-credit-plan-stat{min-width:0;padding:6px 5px;border:1px solid #edf1f5;border-radius:8px;background:#fff}.prr-credit-plan-stat span{display:block;overflow:hidden;color:#8794a2;font-size:6px;font-weight:900;text-overflow:ellipsis;text-transform:uppercase;white-space:nowrap}.prr-credit-plan-stat b{display:block;margin-top:3px;overflow:hidden;color:#263a51;font-size:10px;font-weight:950;text-overflow:ellipsis;white-space:nowrap}.prr-credit-plan-table-wrap{max-height:300px;overflow:auto;border:1px solid #e5edf3;border-radius:8px}.prr-credit-plan-table{width:100%;min-width:390px;border-collapse:collapse}.prr-credit-plan-table th,.prr-credit-plan-table td{padding:6px 5px;border-bottom:1px solid #edf1f5;text-align:right;color:#52677b;font-size:7px;white-space:nowrap}.prr-credit-plan-table thead th{position:sticky;top:0;z-index:1;background:#eef7fa;color:#6f8293;font-size:6px;font-weight:900;text-transform:uppercase}.prr-credit-plan-table th:first-child,.prr-credit-plan-table td:first-child{text-align:left}.prr-credit-plan-table tbody th{color:#344a60;font-weight:900}.prr-credit-plan-table tr:last-child th,.prr-credit-plan-table tr:last-child td{border-bottom:0}.prr-credit-plan-good{color:#13875e!important}.prr-credit-plan-bad{color:#db454a!important}.prr-credit-plan-note{margin:6px 0 0;color:#8996a4;font-size:7px;line-height:1.4}.prr-credit-trend-summary{display:flex;flex-wrap:wrap;gap:5px;margin:8px 0}.prr-credit-trend-summary span{padding:4px 6px;border-radius:6px;background:#fff;color:#607387;font-size:7px;font-weight:800}.prr-credit-trend-summary b{color:#263a51}.prr-credit-trend-legend{display:flex;justify-content:center;gap:12px;margin:6px 0;color:#607387;font-size:7px;font-weight:800}.prr-credit-trend-legend span{display:flex;align-items:center;gap:4px}.prr-credit-trend-legend i{width:8px;height:8px;border-radius:50%}.prr-credit-trend-svg{display:block;width:100%;height:auto;min-height:190px}.prr-credit-trend-grid{stroke:#e6edf3;stroke-width:1}.prr-credit-trend-axis{fill:#8794a2;font-size:9px;font-weight:700}.prr-credit-trend-real-line,.prr-credit-trend-runoff-line{fill:none;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}.prr-credit-trend-real-line{stroke:#10b981}.prr-credit-trend-runoff-line{stroke:#ef4444}.prr-credit-trend-real-area{fill:rgba(16,185,129,.10)}.prr-credit-trend-runoff-area{fill:rgba(239,68,68,.10)}.prr-credit-trend-dot{fill:#fff;stroke-width:2.5;cursor:help}.prr-credit-trend-dot.real{stroke:#10b981}.prr-credit-trend-dot.runoff{stroke:#ef4444}
  @media(max-width:760px){.prr-credit-plan-split{grid-template-columns:1fr}.prr-credit-plan-badge{white-space:normal;text-align:center}}
  .prr-members{padding:0;border:0}.prr-members-head{flex-direction:column}.prr-member-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.prr-members-table table{min-width:900px}
  .prr-footnote{margin-top:8px;border:1px solid #dce7ee;border-radius:10px;background:#fff}
  @media(max-width:1200px){.prr-span-5,.prr-span-4{grid-column:span 6}.prr-span-3{grid-column:span 6}.prr-span-8{grid-column:1/-1}}
  @media(max-width:760px){.prr-kpi-row-top,.prr-kpi-row-bottom{grid-template-columns:repeat(2,minmax(0,1fr))}.prr-header{align-items:stretch;flex-direction:column}.prr-controls{display:grid;grid-template-columns:1fr 1fr;flex:1}.prr-controls label{min-width:0}.prr-controls input,.prr-controls select{width:100%;max-width:none}.prr-span-3,.prr-span-4,.prr-span-5,.prr-span-8{grid-column:1/-1}.prr-dashboard-grid{gap:8px}}
  @media(max-width:520px){#paparanRbbRealisasi{padding:7px}.prr-kpi-grid,.prr-kpi-row{gap:6px}.prr-kpi{padding:8px}.prr-kpi-top strong{font-size:13px}.prr-kpi-foot{font-size:6.5px}.prr-dashboard-panel{padding:9px}.prr-finance-chart{min-height:170px}.prr-controls{grid-template-columns:1fr 1fr}}
</style>

<style>
  .prr-kpi-comparisons{min-height:28px;margin:5px 0 6px;padding:5px 6px;border-radius:7px;background:#f5f8fc}
  .prr-kpi-comparisons>div{display:none;align-items:center;justify-content:space-between;gap:4px;min-width:0}
  #paparanRbbRealisasi[data-prr-comparison="month"] .prr-kpi-comparison-month,
  #paparanRbbRealisasi[data-prr-comparison="year"] .prr-kpi-comparison-year{display:flex;animation:prrCompareIn .28s ease both}
  .prr-kpi-compare-label{overflow:hidden;color:#718196;font-size:7px;font-weight:800;text-overflow:ellipsis;white-space:nowrap}
  .prr-kpi-comparisons b{overflow:hidden;font-size:7px;font-weight:950;text-align:right;text-overflow:ellipsis;white-space:nowrap}
  .prr-kpi-compare-good{color:#15935b}.prr-kpi-compare-bad{color:#e34b50}.prr-kpi-compare-neutral,.prr-kpi-compare-na{color:#8290a0}
  .prr-kpi-rbb{display:flex;justify-content:space-between;gap:4px;margin-bottom:6px}
  .prr-kpi-rbb span{display:flex;align-items:center;gap:3px;min-width:0;color:#7b8998;font-size:6px;font-weight:850;white-space:nowrap}
  .prr-kpi-rbb b{font-size:7px;font-weight:950}
  @keyframes prrCompareIn{from{opacity:.2;transform:translateY(3px)}to{opacity:1;transform:none}}
  @media(max-width:520px){.prr-kpi-comparisons b,.prr-kpi-compare-label{font-size:6.3px}.prr-kpi-rbb span{font-size:5.6px}.prr-kpi-rbb b{font-size:6.3px}}
</style>

<style>
  .prr-sdm-summary{display:grid;gap:8px}.prr-sdm-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:6px}.prr-sdm-stat{min-width:0;padding:8px;border-radius:9px;background:#f7fafc}.prr-sdm-stat span{display:block;color:#8290a0;font-size:7px;font-weight:850;text-transform:uppercase}.prr-sdm-stat b{display:block;margin-top:3px;overflow:hidden;color:#1d3853;font-size:14px;font-weight:950;text-overflow:ellipsis;white-space:nowrap}.prr-sdm-stat small{display:block;margin-top:2px;overflow:hidden;color:#8290a0;font-size:7px;font-weight:700;text-overflow:ellipsis;white-space:nowrap}
  .prr-sdm-section{min-width:0;padding:8px;border:1px solid #e8eef3;border-radius:9px;background:#fff}.prr-sdm-section h3{margin:0 0 7px;color:#526a7f;font-size:8px;font-weight:950}.prr-sdm-gender-track{display:flex;height:8px;overflow:hidden;border-radius:99px;background:#edf1f5}.prr-sdm-gender-track i{display:block;height:100%}.prr-sdm-gender-track .male,.prr-sdm-legend .male{background:#3383df}.prr-sdm-gender-track .female,.prr-sdm-legend .female{background:#bd68cf}.prr-sdm-gender-track .unknown,.prr-sdm-legend .unknown{background:#aab5c1}.prr-sdm-legend{display:flex;gap:5px 10px;flex-wrap:wrap;margin-top:6px}.prr-sdm-legend span{display:flex;align-items:center;gap:4px;color:#718196;font-size:7px;font-weight:750}.prr-sdm-legend i{width:6px;height:6px;border-radius:50%}.prr-sdm-breakdowns{display:grid;gap:7px}.prr-sdm-row{display:grid;grid-template-columns:minmax(62px,.75fr) minmax(30px,1fr) auto;align-items:center;gap:6px;margin-top:6px}.prr-sdm-row>span{overflow:hidden;color:#718196;font-size:7px;font-weight:750;text-overflow:ellipsis;white-space:nowrap}.prr-sdm-row>b{min-width:18px;color:#354e65;font-size:7px;font-weight:900;text-align:right}.prr-sdm-track{height:5px;overflow:hidden;border-radius:99px;background:#edf1f5}.prr-sdm-track i{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#2683cf,#38b8a2)}
  @media(min-width:761px){.prr-sdm-breakdowns{grid-template-columns:1fr}}
  .prr-sdm-jobs{max-height:260px;overflow:auto}.prr-sdm-job-group h4{margin:9px 0 3px;padding-top:6px;border-top:1px solid #edf1f5;color:#527087;font-size:7px;font-weight:950}.prr-sdm-job-group:first-child h4{margin-top:0;padding-top:0;border-top:0}.prr-sdm-job-group .prr-sdm-row{grid-template-columns:minmax(90px,1.15fr) minmax(30px,1fr) auto}.prr-sdm-no-jobs{padding:10px 0;color:#8290a0;font-size:8px}
  .prr-sdm-summary{gap:12px}.prr-sdm-stats{grid-template-columns:repeat(4,minmax(0,1fr));gap:9px}.prr-sdm-stat{padding:11px 12px;border:1px solid #edf1f5;border-radius:10px}.prr-sdm-stat span{font-size:8px}.prr-sdm-stat b{font-size:17px}.prr-sdm-stat small{font-size:8px}.prr-sdm-details{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.prr-sdm-section{padding:11px;border-radius:10px}.prr-sdm-section h3{font-size:9px}.prr-sdm-job-section{min-width:0}.prr-sdm-jobs{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:9px;max-height:none;overflow:visible}.prr-sdm-job-group{min-width:0;padding:9px;border:1px solid #edf1f5;border-radius:9px;background:#fbfdff}.prr-sdm-job-group h4,.prr-sdm-job-group:first-child h4{margin:0 0 6px;padding:0 0 6px;border:0;border-bottom:1px solid #edf1f5;font-size:9px}.prr-sdm-job-list{box-sizing:border-box;max-height:104px;padding-right:6px;overflow-y:auto;overscroll-behavior:contain;scrollbar-width:thin;scrollbar-color:#c4d0dc transparent}.prr-sdm-job-list::-webkit-scrollbar{width:4px}.prr-sdm-job-list::-webkit-scrollbar-track{background:transparent}.prr-sdm-job-list::-webkit-scrollbar-thumb{border-radius:99px;background:#c4d0dc}.prr-sdm-job-list .prr-sdm-row{min-height:26px;box-sizing:border-box;grid-template-columns:minmax(70px,1.15fr) minmax(40px,1fr) auto;gap:7px;margin-top:0}.prr-sdm-job-group .prr-sdm-row>span,.prr-sdm-job-group .prr-sdm-row>b{font-size:8px}
  @media(max-width:900px){.prr-sdm-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.prr-sdm-jobs{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(max-width:600px){.prr-sdm-details,.prr-sdm-jobs{grid-template-columns:1fr}.prr-sdm-stat{padding:8px}.prr-sdm-stat b{font-size:15px}}
</style>

<style>
  #paparanRbbRealisasi,.prr-dialog{font-family:Roboto,Arial,sans-serif}
  .prr-office-table-wrap{width:100%;overflow:auto;border:1px solid #e1e9f0;border-radius:10px}
  .prr-office-table{width:100%;min-width:980px;border-collapse:separate;border-spacing:0;font-variant-numeric:tabular-nums}
  .prr-office-table th,.prr-office-table td{padding:9px 8px;border-bottom:1px solid #edf1f5;text-align:right;white-space:nowrap;font-size:10px}
  .prr-office-table thead th{position:sticky;top:0;background:#f1f7fb;color:#5f7488;font-size:8px;font-weight:900;letter-spacing:.035em}
  .prr-office-table thead tr:first-child th:not(:first-child){text-align:center;color:#17657e}
  .prr-office-table thead tr:nth-child(2) th{top:31px;background:#f8fbfd;font-size:7px}
  .prr-office-table th:first-child,.prr-office-table tbody th{text-align:left}
  .prr-office-table tbody th{position:sticky;left:0;max-width:230px;overflow:hidden;background:#fff;color:#29455e;font-size:10px;font-weight:800;text-overflow:ellipsis}
  .prr-office-table tbody tr:nth-child(even) th,.prr-office-table tbody tr:nth-child(even) td{background:#fbfdff}
  .prr-office-table tbody tr:last-child th,.prr-office-table tbody tr:last-child td{border-bottom:0}
  .prr-office-amount{color:#243c54;font-weight:850}
  .prr-office-change{display:inline-flex;min-width:68px;flex-direction:column;align-items:center;justify-content:center;gap:2px;padding:5px 7px;border-radius:7px;font-weight:850;cursor:help;line-height:1.15}
  .prr-office-change b{font-size:9px;font-weight:950}.prr-office-change small{font-size:8px;font-weight:850;opacity:.9}
  .prr-office-up{background:#eaf8f1;color:#148052}.prr-office-down{background:#fff0f0;color:#c8434a}.prr-office-na{background:#f1f4f7;color:#718196}
  .prr-office-note{margin:8px 0 0;color:#718196;font-size:9px;line-height:1.45}
  .prr-office-warning{margin:8px 0 0;padding:8px 10px;border-radius:8px;background:#fff7e6;color:#97620c;font-size:9px;line-height:1.4}
  .prr-rbb-table table{min-width:0}
  .prr-rbb-achievement{display:flex;align-items:center;justify-content:flex-end;gap:4px;min-width:0}
  .prr-rbb-achievement .prr-progress{flex:0 0 44px;width:44px;height:4px;margin:0}
  .prr-rbb-achievement>b{min-width:42px;font-size:8px;font-weight:850;text-align:right}
  .prr-rbb-main-panel{grid-column:span 8}.prr-alert-side-panel{grid-column:span 4}.prr-production-panel{grid-column:span 5}.prr-alert-runoff-panel{grid-column:span 7}.prr-credit-plan-topline{display:flex;justify-content:flex-end;margin-bottom:7px}
  .prr-font-scale-value{display:inline-flex;align-items:center;justify-content:center;min-width:48px;padding:5px 8px;border-radius:99px;background:#eaf6fa;color:#17657e;font-size:10px;font-weight:900}
  .prr-font-scale-slider{display:block;width:min(520px,100%);height:24px;accent-color:#2563eb;cursor:pointer}
  @media(max-width:1450px){.prr-span-5,.prr-span-4{grid-column:span 6}.prr-span-3{grid-column:span 6}.prr-span-8{grid-column:1/-1}.prr-rbb-main-panel{grid-column:span 8}.prr-alert-side-panel{grid-column:span 4}.prr-production-panel{grid-column:span 5}.prr-alert-runoff-panel{grid-column:span 7}}
  @media(max-width:760px){.prr-span-3,.prr-span-4,.prr-span-5,.prr-span-8,.prr-rbb-main-panel,.prr-alert-side-panel,.prr-production-panel,.prr-alert-runoff-panel{grid-column:1/-1}.prr-office-table th,.prr-office-table td{padding:8px 6px}.prr-office-table{min-width:900px}}
</style>

<style>
  .prr-credit-plan-topline{justify-content:flex-start!important;margin:0 0 7px!important}
  .prr-credit-plan-badge{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid #cde8f1;background:linear-gradient(110deg,#eefaff,#f5fbff);box-shadow:0 3px 9px rgba(25,111,146,.08);font-size:8px}
  .prr-credit-plan-badge b{color:#087e9d;font-size:13px;font-weight:950;letter-spacing:-.02em}
  .prr-credit-plan-badge span{color:#4f7185;font-size:7px;font-weight:950;letter-spacing:.06em}
  .prr-credit-plan-stats{gap:6px;margin-bottom:8px}
  .prr-credit-plan-stat{position:relative;padding:8px 7px 7px;border-color:#e5edf3;background:linear-gradient(145deg,#fff,#f8fbfd);box-shadow:0 2px 5px rgba(20,65,95,.035);transition:border-color .15s,transform .15s,box-shadow .15s}
  .prr-credit-plan-stat:focus-visible,.prr-credit-plan-stat:hover{outline:none;transform:translateY(-1px);border-color:#add8e5;box-shadow:0 5px 12px rgba(20,105,140,.1)}
  .prr-credit-trend-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:7px;margin:9px 0 0}
  .prr-trend-summary-item{min-width:0;padding:7px 9px;border:1px solid #e3ebf1;border-radius:9px;background:linear-gradient(145deg,#fff,#f8fbfd);box-shadow:0 2px 6px rgba(20,65,95,.04)}
  .prr-credit-trend-summary .prr-trend-summary-item span{display:block;padding:0;background:none;color:#8191a2;font-size:7px;font-weight:900;letter-spacing:.05em}
  .prr-credit-trend-summary .prr-trend-summary-item b{display:block;margin-top:3px;overflow:hidden;color:#243e59;font-size:11px;font-weight:950;text-overflow:ellipsis;white-space:nowrap}
  .prr-trend-summary-item.real{border-top:2px solid #10b981}.prr-trend-summary-item.runoff{border-top:2px solid #ef4444}.prr-trend-summary-item.net{border-top:2px solid #5285ef}
  .prr-trend-summary-item.real b{color:#0b8c67}.prr-trend-summary-item.runoff b{color:#d84c52}.prr-trend-summary-item.net b{color:#345b9d}
  .prr-credit-trend-summary{grid-template-columns:repeat(4,minmax(0,1fr))}
  .prr-trend-summary-item.restruct{border-top:2px solid #16a6a0}.prr-trend-summary-item.installment{border-top:2px solid #ed9b24}.prr-trend-summary-item.paid{border-top:2px solid #ef6268}.prr-trend-summary-item.average{border-top:2px solid #5682dc}.prr-trend-summary-item.target{border-top:2px solid #8b62db}
  .prr-trend-summary-item.restruct b{color:#118982}.prr-trend-summary-item.installment b{color:#bd7813}.prr-trend-summary-item.paid b{color:#d84c52}.prr-trend-summary-item.average b{color:#345b9d}.prr-trend-summary-item.target b{color:#7550c3}
  .prr-credit-plan-table-wrap{scrollbar-width:thin;scrollbar-color:#b7c9d8 transparent;scrollbar-gutter:stable;overscroll-behavior:contain}
  .prr-credit-plan-table-wrap::-webkit-scrollbar{width:5px;height:5px}.prr-credit-plan-table-wrap::-webkit-scrollbar-track{background:transparent}.prr-credit-plan-table-wrap::-webkit-scrollbar-thumb{border-radius:8px;background:#b7c9d8}.prr-credit-plan-table-wrap::-webkit-scrollbar-thumb:hover{background:#849db2}
  .prr-credit-plan-table{min-width:0;table-layout:fixed}.prr-credit-plan-table th:nth-child(1){width:18%}.prr-credit-plan-table th:nth-child(2){width:17%}.prr-credit-plan-table th:nth-child(3){width:18%}.prr-credit-plan-table th:nth-child(4){width:31%}.prr-credit-plan-table th:nth-child(5){width:16%}
  .prr-credit-trend-value{font-size:9px;font-weight:950;paint-order:stroke;stroke:#fff;stroke-width:3px;stroke-linejoin:round;pointer-events:none}.prr-credit-trend-value.real{fill:#07875f}.prr-credit-trend-value.runoff{fill:#df4149}
  .prr-sdm-job-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:9px}.prr-sdm-job-head h3{margin:0!important}
  .prr-sdm-job-toggle{display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border:1px solid #d4e4ed;border-radius:8px;background:linear-gradient(120deg,#f7fcff,#fff);color:#39748c;font:800 8px Roboto,Arial,sans-serif;cursor:pointer;white-space:nowrap;transition:background .15s,border-color .15s,transform .15s}
  .prr-sdm-job-toggle:hover{transform:translateY(-1px);border-color:#9ecbd9;background:#eef9fc}.prr-sdm-job-toggle:focus-visible{outline:2px solid #72bdd0;outline-offset:2px}.prr-sdm-job-toggle-icon{display:grid;place-items:center;width:17px;height:17px;border-radius:5px;background:#e8f5fa;color:#1687a5;font-size:12px;font-weight:950}
  .prr-sdm-ao-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}.prr-sdm-ao-item{min-width:0;padding:8px 9px;border:1px solid #e7eef3;border-radius:8px;background:#fbfdff}.prr-sdm-ao-item .prr-sdm-row{grid-template-columns:minmax(70px,1.1fr) minmax(30px,1fr) auto;margin:0}.prr-sdm-ao-item .prr-sdm-row>span{color:#536d82;font-weight:850}.prr-sdm-ao-empty{grid-column:1/-1;padding:10px;border:1px dashed #dce7ee;border-radius:8px;color:#8290a0;font-size:8px}
  [data-prr-job-compact][hidden],[data-prr-job-complete][hidden]{display:none!important}
  .prr-modern-tooltip{position:fixed;z-index:10030;max-width:min(380px,calc(100vw - 24px));padding:10px 12px;border:1px solid rgba(185,211,230,.24);border-left:3px solid #36b5d0;border-radius:11px;background:rgba(13,31,52,.97);box-shadow:0 12px 32px rgba(8,30,51,.28),0 2px 8px rgba(8,30,51,.16);color:#f7fbff;font:500 11px/1.55 Roboto,Arial,sans-serif;white-space:pre-line;pointer-events:none;opacity:0;transform:translateY(4px);transition:opacity .12s ease,transform .12s ease}
  .prr-modern-tooltip.is-visible{opacity:1;transform:translateY(0)}.prr-modern-tooltip[hidden]{display:none}
  [data-prr-tooltip]{cursor:help}
  #paparanRbbRealisasi *{scrollbar-width:thin;scrollbar-color:#b7c9d8 transparent}
  #paparanRbbRealisasi *::-webkit-scrollbar{width:5px;height:5px}#paparanRbbRealisasi *::-webkit-scrollbar-track{background:transparent}#paparanRbbRealisasi *::-webkit-scrollbar-thumb{border-radius:99px;background:#b7c9d8}#paparanRbbRealisasi *::-webkit-scrollbar-thumb:hover{background:#8ea5b4}
  @media(max-width:760px){.prr-credit-trend-summary{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(max-width:520px){.prr-credit-plan-stats{grid-template-columns:repeat(5,minmax(0,1fr));gap:3px}.prr-credit-plan-stat{padding:6px 3px}.prr-credit-plan-stat span{font-size:5px}.prr-credit-plan-stat b{font-size:8px}.prr-credit-trend-summary{gap:4px}.prr-trend-summary-item{padding:6px 5px}.prr-credit-trend-summary .prr-trend-summary-item b{font-size:9px}}
</style>

<script>
(function () {
  const root = document.getElementById('paparanRbbRealisasi');
  if (!root) return;
  root.dataset.prrComparison = 'month';
  const API_RBB = './api/rbb/';
  const API_LAPKEU = './api/lapkeu/';
  const API_DASHBOARD = './api/dashboard/';
  const API_ANGGOTA = './api/anggota/';
  const API_DATE = './api/date/';
  const API_KODE = './api/kode/';
  const API_SETTINGS = './api/paparan_settings/';
  const STORE_KEY = 'paparan_rbb_realisasi_settings_v2';
  const fmt = new Intl.NumberFormat('id-ID', {maximumFractionDigits: 2});
  const fmtInt = new Intl.NumberFormat('id-ID', {maximumFractionDigits: 0});
  const esc = (value) => String(value == null ? '' : value).replace(/[&<>"']/g, function (c) { return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]); });
  const num = (value) => {
    if (value === null || value === undefined || (typeof value === 'string' && value.trim() === '')) return null;
    const n = Number(value);
    return Number.isFinite(n) ? n : null;
  };
  const path = (obj, keys, fallback) => {
    let value = obj;
    String(keys).split('.').forEach(function (key) { value = value && value[key] !== undefined ? value[key] : undefined; });
    return value === undefined || value === null ? fallback : value;
  };
  const state = {scopes:[], selected:[], widgets:[], models:new Map(), active:'', closing:'', actualDate:'', historyRequest:0, loadRequest:0, comparisonMode:'month', fontScale:1.15};
  function normalizeFontScale(value) { const scale=Number(value); return Number.isFinite(scale) ? Math.min(1.4,Math.max(.85,scale)) : 1.15; }
  function applyFontScale(value) {
    state.fontScale=normalizeFontScale(value);
    root.style.setProperty('--prr-font-scale',String(state.fontScale));
    root.style.zoom=String(state.fontScale);
    const slider=document.getElementById('prrFontScale'),output=document.getElementById('prrFontScaleValue');
    if (slider) slider.value=String(Math.round(state.fontScale*100));
    if (output) output.textContent=Math.round(state.fontScale*100)+'%';
  }
  const defaultTabs = ['kinerja_pusat','korwil_banyumas','cabang_018','cabang_020'];
  const widgetDefs = [
    {key:'kpis', group:'Kinerja Keuangan', label:'Kartu indikator utama', desc:'Aset, kredit, DPK, laba, NPL, BOPO, ROA, dan KPMM.', default:true},
    {key:'trend', group:'Kinerja Keuangan', label:'Tren kinerja keuangan', desc:'Perubahan aset, kredit, DPK, dan laba per closing.', default:true},
    {key:'rbb', group:'Kinerja Keuangan', label:'Realisasi vs target RBB', desc:'Nominal realisasi, target, dan persentase capaian.', default:true},
    {key:'ratios', group:'Kinerja Keuangan', label:'Indikator rasio lengkap', desc:'KPMM, KAP, PPAP, NPL, BOPO, ROA, LDR, CASA, dan lainnya.', default:false},
    {key:'risk', group:'Risiko Kredit', label:'Ringkasan NPL dan RR', desc:'Chart NPL saldo bank dan RR berbasis baki debet.', default:true},
    {key:'kolektibilitas', group:'Risiko Kredit', label:'Breakdown kolektibilitas', desc:'Saldo bank dibanding closing bulan lalu dan akhir tahun lalu.', default:true},
    {key:'risk_summary', group:'Risiko Kredit', label:'Ringkasan indikator risiko', desc:'NPL, BOPO, ROA, dan KPMM dibanding target RBB.', default:true},
    {key:'alerts', group:'Risiko Kredit', label:'Early warning indikator RBB', desc:'Peringatan berdasarkan capaian target RBB yang tersedia.', default:true},
    {key:'map', group:'Panel Direksi', label:'Peta kinerja wilayah', desc:'Ringkasan wilayah; peta geospasial menunggu sumber koordinat.', default:true, pending:true},
    {key:'ranking', group:'Panel Direksi', label:'Kinerja kantor bertingkat', desc:'Pusat menampilkan Korwil, Korwil menampilkan cabang, dan cabang menampilkan Kankas dengan perubahan bulanan dan tahunan.', default:true},
    {key:'compliance', group:'Panel Direksi', label:'Kepatuhan', desc:'Menunggu sumber indikator kepatuhan.', default:true, pending:true},
    {key:'members', group:'Panel Direksi', label:'Sumber daya manusia', desc:'Rekap demografi dan jumlah pegawai per jabatan pada area terpilih.', default:true},
    {key:'notary', group:'Panel Direksi', label:'Dokumen notaris', desc:'Menunggu sumber status dokumen dan SLA notaris.', default:true, pending:true}
  ];
  const defaultWidgets = widgetDefs.filter(function (item) { return item.default; }).map(function (item) { return item.key; });
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
  const dashboardKpis = [
    {key:'aset', label:'TOTAL ASET', code:'1', metric:'aset', icon:'▥', tone:'blue'},
    {key:'kredit', label:'TOTAL KREDIT', code:'5', metric:'kredit', icon:'▣', tone:'green'},
    {key:'damas', label:'TOTAL DPK', code:'2', metric:'damas', icon:'◉', tone:'violet'},
    {key:'laba', label:'LABA SEBELUM PAJAK', code:'8', metric:'laba', icon:'↗', tone:'orange'},
    {key:'npl', label:'NPL SALDO BANK', code:'15', metric:'ratio', ratioCode:'15', inverse:true, icon:'!', tone:'red'},
    {key:'bopo', label:'BOPO', code:'20', metric:'ratio', ratioCode:'20', inverse:true, icon:'◔', tone:'blue'},
    {key:'roa', label:'ROA', code:'18', metric:'ratio', ratioCode:'18', icon:'↗', tone:'teal'},
    {key:'car', label:'KPMM / CAR', code:'10', metric:'ratio', ratioCode:'10', icon:'◇', tone:'cyan'}
  ];
  const financeHistoryMetrics = ['aset','kredit','damas','laba'];
  const dashboardKpiCards = dashboardKpis.filter(function (item) { return item.key !== 'car'; }).concat([
    {key:'pendapatan', label:'PENDAPATAN', code:'6', metric:'pendapatan', icon:'+', tone:'green'},
    {key:'biaya', label:'BEBAN', code:'7', metric:'biaya', icon:'-', tone:'red'}
  ]);
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
  const rbbCodeMap = {'1':'95','2':'105','5':'63','6':'196','7':'258','8':'261'};
  function rbbSourceValue(source, code) { return num(source && source[String(code)]) ?? 0; }
  function rbbMappedSource(source, code) {
    const mappedCode = rbbCodeMap[String(code)];
    if (mappedCode && source && Object.prototype.hasOwnProperty.call(source, mappedCode)) return rbbSourceValue(source, mappedCode);
    return rbbSourceValue(source, code);
  }
  function derivedRbbTargets(source, monthNumber) {
    const asset = rbbMappedSource(source, 1);
    const dpk = rbbSourceValue(source, 105) || rbbSourceValue(source, 25) || rbbMappedSource(source, 2);
    const credit = rbbSourceValue(source, 63) || rbbSourceValue(source, 44) || rbbMappedSource(source, 5);
    const npl = rbbSourceValue(source, 56);
    const ckpnCredit = Math.abs(rbbSourceValue(source, 70));
    const productiveAssets = rbbSourceValue(source, 61) + rbbSourceValue(source, 64) + rbbSourceValue(source, 65);
    const income = rbbMappedSource(source, 6);
    const expense = rbbMappedSource(source, 7);
    const laba = rbbMappedSource(source, 8) || (income - expense);
    const interestIncome = rbbSourceValue(source, 163);
    const interestExpense = rbbSourceValue(source, 198);
    const currentMonth = Math.max(1, Number(monthNumber) || 1);
    const derived = {};
    const setRatio = function (code, numerator, denominator, annualized) {
      if (denominator === 0) return;
      let value = numerator / denominator * 100;
      if (annualized) value *= 12 / currentMonth;
      if (Number.isFinite(value)) derived[String(code)] = value;
    };
    if (rbbMappedSource(source, 8) === 0 && (income !== 0 || expense !== 0)) derived['8'] = income - expense;
    setRatio('12', npl, credit, false);
    setRatio('13', ckpnCredit, npl, false);
    setRatio('15', npl, credit, false);
    setRatio('16', npl - ckpnCredit, credit, false);
    setRatio('17', credit, productiveAssets, false);
    setRatio('18', laba, asset, true);
    setRatio('19', interestIncome - interestExpense, productiveAssets, true);
    setRatio('20', expense, income, false);
    setRatio('21', rbbSourceValue(source, 57) + rbbSourceValue(source, 61), rbbSourceValue(source, 96) + dpk + rbbSourceValue(source, 120) + rbbSourceValue(source, 110) + rbbSourceValue(source, 117), false);
    setRatio('22', credit, dpk, false);
    setRatio('23', rbbSourceValue(source, 31), credit, false);
    setRatio('24', rbbSourceValue(source, 106) || rbbSourceValue(source, 27), dpk, false);
    return derived;
  }
  function targetValue(model, code, yearEnd) {
    const normalizedCode = String(code);
    const row = model.rows[normalizedCode] || {};
    const source = path(model.rbb, yearEnd ? 'rbb_sources.year_end' : 'rbb_sources.periode', {}) || {};
    const mappedCode = rbbCodeMap[normalizedCode] || normalizedCode;
    const sourceHasCode = source && Object.prototype.hasOwnProperty.call(source, mappedCode);
    const direct = sourceHasCode
      ? num(source[mappedCode])
      : (row[yearEnd ? 'has_year_end_target' : 'has_target'] ? num(row[yearEnd ? 'target_rbb_year_end' : 'target_rbb']) : null);
    if (direct !== null && Math.abs(direct) > 0.000001) return direct;
    const monthNumber = yearEnd ? 12 : (Number(String(state.closing).slice(5,7)) || 1);
    return num(derivedRbbTargets(source, monthNumber)[normalizedCode]);
  }
  function actualValues(rbb, actual) {
    const detail = actual.ringkasan_detail || {};
    const macro = actual.makro || {};
    const health = actual.kesehatan_rasio || {};
    const detailActual = rbb.detail_actual || {};
    const damas = detailActual.damas || {};
    const credit = detailActual.credit || {};
    const statuses = credit.statuses || {};
    const macroAvailable = path(actual, 'info_tanggal.tersedia.aktual', null) !== false;
    const actualNominal = function (value, noa, fallback) {
      const amount=num(value), count=num(noa);
      if (amount === null || (Math.abs(amount) < 0.000001 && (count === null || count === 0))) return fallback;
      return amount;
    };
    const kreditTotal = actualNominal(path(credit,'total.rupiah',null),path(credit,'total.noa',null),macroAvailable?num(path(detail,'kredit_diberikan.saldo_bank_ead',null)):null);
    const tabunganActual = actualNominal(path(damas,'tabungan.rupiah',null),path(damas,'tabungan.noa',null),macroAvailable?num(path(detail,'dana_masyarakat.tabungan',null)):null);
    const depositoActual = actualNominal(path(damas,'deposito.rupiah',null),path(damas,'deposito.noa',null),macroAvailable?num(path(detail,'dana_masyarakat.deposito',null)):null);
    const damasActual = actualNominal(path(damas,'total.rupiah',null),path(damas,'total.noa',null),macroAvailable?num(path(detail,'dana_masyarakat.total',null)):null);
    const nplAmount = ['KL','D','M'].reduce(function (sum, key) { return sum + (num(statuses[key] && statuses[key].rupiah) || 0); }, 0);
    const nplRatio = kreditTotal && kreditTotal > 0 ? nplAmount / kreditTotal * 100 : null;
    const produktif = num(path(actual, 'rasio.detail_nominal.rata_aset_produktif', null));
    return {
      aset:macroAvailable?num(path(macro, 'aset.nominal_aktual', null)):null,
      tabungan:tabunganActual,
      deposito:depositoActual,
      damas:damasActual,
      kredit:kreditTotal,
      pendapatan:macroAvailable?num(path(macro, 'pendapatan.nominal_aktual', null)):null,
      biaya:macroAvailable?num(path(macro, 'biaya.nominal_aktual', null)):null,
      laba:macroAvailable?(num(path(detail, 'laba_sebelum_pajak', null)) ?? num(path(macro, 'laba_rugi.nominal_aktual', null))):null,
      npl:nplRatio,
      ratio:{
        '10':num(path(actual, 'rasio.kpmm_persen', null)) ?? num(path(actual, 'kesehatan_rasio.kpmm.persen_aktual', null)),
        '11':num(path(actual, 'rasio.modal_inti_persen', null)) ?? num(path(actual, 'kesehatan_rasio.modal_inti.persen_aktual', null)),
        '12':macroAvailable?num(path(detail, 'rasio_utama.kap', null)):null,
        '13':macroAvailable?num(path(detail, 'rasio_utama.ckpn_terhadap_ppka', null)):null,
        '15':nplRatio,
        '16':macroAvailable?num(path(detail, 'rasio_utama.npl_baki_debet_netto', null)):null,
        '17':produktif && produktif > 0 && kreditTotal !== null ? kreditTotal / produktif * 100 : null,
        '18':macroAvailable?num(path(health, 'roa.persen_aktual', null)):null,
        '19':macroAvailable?num(path(health, 'nim.persen_aktual', null)):null,
        '20':macroAvailable?num(path(health, 'bopo.persen_aktual', null)):null,
        '21':macroAvailable?num(path(health, 'cash.persen_aktual', null)):null,
        '22':macroAvailable?num(path(health, 'ldr.persen_aktual', null)):null,
        '24':macroAvailable?num(path(health, 'casa.persen_aktual', null)):null
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
  function memberPayload(scope) {
    const payload = {type:'rekap_anggota', as_of:state.closing, summary_only:true};
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
  function historyPayload(scope, metric, year) {
    const payload = {type:'realisasi_history', metric:metric, year:Number(year || String(state.closing).slice(0,4)), kode_kantor:'000'};
    if (scope.type === 'korwil') payload.korwil = scope.value;
    else payload.kode_kantor = scope.value || '000';
    return payload;
  }
  async function loadModel(scope) {
    const trendPayload = {type:'tren_portofolio_kredit', harian_date:state.closing, periode:'bulanan'};
    if (scope.type === 'korwil') trendPayload.korwil = scope.value;
    else trendPayload.kode_kantor = scope.value || '000';
    const rbbPayload = scopePayload(scope);
    const needsCreditTrend = state.widgets.indexOf('kpis') >= 0 || state.widgets.indexOf('risk') >= 0 || state.widgets.indexOf('kolektibilitas') >= 0;
    const pair = await Promise.all([
      apiPost(API_RBB, rbbPayload),
      apiPost(API_LAPKEU, actualPayload(scope)),
      needsCreditTrend ? apiPost(API_DASHBOARD, trendPayload).catch(function () { return []; }) : Promise.resolve([]),
      state.widgets.indexOf('members') >= 0
        ? apiPost(API_ANGGOTA, memberPayload(scope)).catch(function () { return {meta:{}, data:[]}; })
        : Promise.resolve(null)
    ]);
    const model = modelFor(scope, pair[0], pair[1]);
    model.trend = Array.isArray(pair[2]) ? pair[2] : [];
    model.members = pair[3] || null;
    model.snapshot = pair[1] || {};
    model.history = Object.fromEntries(financeHistoryMetrics.map(function (metric) { return [metric, []]; }));
    model.comparisonHistory = {
      currentYear: {kredit:[], npl:[]},
      previousYear: {kredit:[], npl:[]}
    };
    model.officePerformance = null;
    model.rbb.credit_comparison = model.rbb.credit_comparison || {};
    model.creditComparisonLoading = state.widgets.indexOf('kolektibilitas') >= 0;
    model.historyLoading = state.widgets.indexOf('kpis') >= 0 || state.widgets.indexOf('trend') >= 0;
    model.officePerformanceLoading = state.widgets.indexOf('ranking') >= 0;
    model.creditPlanLoading = state.widgets.indexOf('rbb') >= 0;
    model.creditTrendLoading = state.widgets.indexOf('rbb') >= 0;
    model.creditPlan = null;
    model.creditTrend = [];
    return model;
  }
  async function loadModelEnrichment(scope, model, requestId) {
    const jobs = [];
    const isCurrentRequest = function () { return requestId === state.loadRequest; };
    if (model.officePerformanceLoading) {
      const payload = scopePayload(scope);
      payload.type = 'kinerja_kantor';
      jobs.push(apiPost(API_RBB, payload).then(function (result) {
        if (!isCurrentRequest()) return;
        model.officePerformance = result || {};
      }).catch(function (error) {
        if (isCurrentRequest()) model.officePerformance = {error:error.message || 'API kinerja kantor gagal dimuat.',rows:[]};
      }).then(function () {
        if (!isCurrentRequest()) return;
        model.officePerformanceLoading = false;
        render();
      }));
    }

    if (model.creditPlanLoading) {
      const payload = scopePayload(scope);
      payload.type = 'realisasi_kredit_rbb_tahunan';
      jobs.push(apiPost(API_RBB, payload).then(function (result) {
        if (isCurrentRequest()) model.creditPlan = result || {};
      }).catch(function (error) {
        if (isCurrentRequest()) model.creditPlanError = error.message || 'Pencapaian RBB kredit gagal dimuat.';
      }).then(function () {
        if (!isCurrentRequest()) return;
        model.creditPlanLoading = false;
        render();
      }));
    }

    if (model.creditTrendLoading) {
      const payload = {type:'tren_runoff_realisasi', harian_date:state.closing, periode:'tahun_berjalan'};
      if (scope.type === 'korwil') payload.korwil = scope.value;
      else payload.kode_kantor = scope.value || '000';
      jobs.push(apiPost(API_DASHBOARD, payload).then(function (result) {
        if (isCurrentRequest()) model.creditTrend = Array.isArray(result) ? result : [];
      }).catch(function (error) {
        if (isCurrentRequest()) model.creditTrendError = error.message || 'Grafik realisasi dan run off gagal dimuat.';
      }).then(function () {
        if (!isCurrentRequest()) return;
        model.creditTrendLoading = false;
        render();
      }));
    }

    if (model.creditComparisonLoading) {
      jobs.push((async function () {
        if (!isCurrentRequest()) return;
        const payload = scopePayload(scope);
        payload.type = 'ikhtisar_credit_comparison';
        try {
          const result = await apiPost(API_RBB, payload);
          if (isCurrentRequest()) model.rbb.credit_comparison = result.credit_comparison || {};
        } catch (error) {
          if (isCurrentRequest()) model.creditComparisonError = error.message || 'Pembanding kolektibilitas gagal dimuat.';
        } finally {
          if (isCurrentRequest()) {
            model.creditComparisonLoading = false;
            render();
          }
        }
      })());
    }

    if (model.historyLoading) {
      const loadHistories = async function () {
        if (!isCurrentRequest()) return;
        const currentYear = Number(String(state.closing).slice(0,4));
        const historyNeeded = new Map();
        financeHistoryMetrics.forEach(function (metric) { historyNeeded.set(metric + ':' + currentYear, [metric, currentYear]); });
        if (state.widgets.indexOf('kpis') >= 0) {
          ['kredit','npl'].forEach(function (metric) {
            historyNeeded.set(metric + ':' + currentYear, [metric, currentYear]);
            historyNeeded.set(metric + ':' + (currentYear - 1), [metric, currentYear - 1]);
          });
        }
        const entries = Array.from(historyNeeded.entries());
        const results = await Promise.all(entries.map(function (entry) {
          const spec = entry[1];
          return apiPost(API_RBB, historyPayload(scope, spec[0], spec[1])).catch(function () { return {history:[]}; });
        }));
        if (!isCurrentRequest()) return;
        const historyMap = {};
        entries.forEach(function (entry, index) { historyMap[entry[0]] = Array.isArray(results[index] && results[index].history) ? results[index].history : []; });
        model.history = Object.fromEntries(financeHistoryMetrics.map(function (metric) { return [metric, historyMap[metric + ':' + currentYear] || []]; }));
        model.comparisonHistory = {
          currentYear: {kredit:historyMap['kredit:' + currentYear] || [], npl:historyMap['npl:' + currentYear] || []},
          previousYear: {kredit:historyMap['kredit:' + (currentYear - 1)] || [], npl:historyMap['npl:' + (currentYear - 1)] || []}
        };
        model.historyLoading = false;
        render();
      };
      jobs.push(loadHistories().catch(function () {
        if (!isCurrentRequest()) return;
        model.historyLoading = false;
        render();
      }));
    }
    await Promise.all(jobs);
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
      '<div class="prr-card-values"><div><span>Capaian bulan</span><b class="' + (achievement === null ? 'prr-kpi-compare-na' : (good ? 'prr-good' : 'prr-bad')) + '">' + esc(displayRatio(achievement)) + '</b></div><div><span>Capaian Des</span><b class="' + (yearAchievement === null ? 'prr-kpi-compare-na' : (yearAchievement >= 100 ? 'prr-good' : 'prr-bad')) + '">' + esc(displayRatio(yearAchievement)) + '</b></div><div><span>Basis</span><b>' + (def.ratio ? 'Saldo bank' : 'Rupiah') + '</b></div></div>' +
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
      const achieveTone=achieve===null?'prr-kpi-compare-na':(achieve>=100?'prr-good':'prr-bad');
      const yearTone=yearAchieve===null?'prr-kpi-compare-na':(yearAchieve>=100?'prr-good':'prr-bad');
      return actual === null && target === null && year === null ? '' : '<tr><th>' + esc(def.label) + '</th><td>' + esc(displayRatio(target)) + '</td><td>' + esc(displayRatio(actual)) + '</td><td>' + esc(displayRatio(year)) + '</td><td class="' + achieveTone + '">' + esc(displayRatio(achieve)) + '</td><td class="' + yearTone + '">' + esc(displayRatio(yearAchieve)) + '</td></tr>';
    }).join('');
    return '<div class="prr-ratio-table"><table><thead><tr><th>INDIKATOR</th><th>RBB BULAN</th><th>REALISASI</th><th>RBB DES</th><th>CAPAIAN BULAN</th><th>CAPAIAN DES</th></tr></thead><tbody>' + (rows || '<tr><td colspan="6">Data rasio belum tersedia.</td></tr>') + '</tbody></table></div>';
  }
  function creditSnapshot(model) {
    const credit = path(model.rbb, 'detail_actual.credit', {}) || {};
    const total = num(model.actual.kredit);
    const statuses = credit.statuses || {};
    const rows = [
      {key:'L', label:'Lancar'},
      {key:'DP', label:'Dalam Perhatian Khusus'},
      {key:'KL', label:'Kurang Lancar'},
      {key:'D', label:'Diragukan'},
      {key:'M', label:'Macet'}
    ].map(function (item) {
      const value = total === null ? null : num(path(statuses, item.key + '.rupiah', null));
      return {key:item.key, label:item.label, value:value, pct:total > 0 && value !== null ? value / total * 100 : null};
    });
    const npl = total === null ? null : rows.filter(function (row) { return ['KL','D','M'].indexOf(row.key) >= 0; }).reduce(function (sum, row) { return sum + (row.value || 0); }, 0);
    return {total:total, npl:npl, nplPct:total > 0 && npl !== null ? npl / total * 100 : null, rows:rows};
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
    const creditComparison = path(model.rbb, 'credit_comparison', {}) || {};
    const previousMonthKolek = creditComparison.previous_month || {};
    const previousYearEndKolek = creditComparison.previous_year_end || {};
    const kolekCompareCell = function (snapshot, key, current) {
      const baseline = key === 'TOTAL' ? num(snapshot.total) : num(path(snapshot, 'statuses.' + key, null));
      if (!snapshot.closing_date || baseline === null) return '<span class="prr-kolek-no-data" title="Closing pembanding tidak tersedia">-</span>';
      const currentValue = num(current);
      const delta = currentValue === null ? null : currentValue - baseline;
      const change = currentValue === null ? null : (Math.abs(baseline) > 0.000001 ? delta / Math.abs(baseline) * 100 : (Math.abs(currentValue) < 0.000001 ? 0 : null));
      const signedChange = change === null ? '-' : (change > 0 ? '+' : '') + displayRatio(change);
      return '<div class="prr-kolek-compare" title="Snapshot closing ' + esc(formatDate(snapshot.closing_date)) + '"><b>' + esc(displayNominal(baseline)) + '</b><small>Selisih ' + esc(displayNominal(delta)) + '</small><small>' + esc(signedChange) + '</small></div>';
    };
    const kolekRows = credit.rows.map(function (row) {
      const nplClass = ['KL','D','M'].indexOf(row.key) >= 0 ? ' prr-kolek-npl' : '';
      return '<tr><th scope="row" title="' + esc(row.key + ' - ' + row.label) + '">' + esc(row.key) + '</th><td>' + esc(displayNominal(row.value)) + '</td><td class="prr-kolek-pct' + nplClass + '">' + esc(displayRatio(row.pct)) + '</td><td>' + kolekCompareCell(previousMonthKolek, row.key, row.value) + '</td><td>' + kolekCompareCell(previousYearEndKolek, row.key, row.value) + '</td></tr>';
    }).join('');
    const riskHtml = state.widgets.indexOf('risk') >= 0 ?
      '<div class="prr-extra-box"><div class="prr-extra-head"><div><h3>NPL atau Repayment Rate (RR)</h3><p>Pilih indikator yang ingin dipantau beserta breakdown chart-nya.</p></div><div class="prr-chart-toggle" role="tablist" aria-label="Pilih indikator"><button type="button" data-prr-chart-toggle="npl">NPL</button><button type="button" class="active" data-prr-chart-toggle="rr">RR</button></div></div>' +
        '<div class="prr-chart-view" data-prr-chart-view="npl" hidden><p class="prr-chart-caption">NPL bruto memakai saldo bank dan dibandingkan dengan total kredit saldo bank.</p><div class="prr-rr-summary">' +
          rrStat('NPL saldo bank', displayRatio(credit.nplPct), 'prr-bad') +
          rrStat('NPL nominal', displayNominal(credit.npl)) +
          rrStat('NPL bulan lalu', nplPrev === null ? '-' : displayRatio(nplPrev), '') +
          rrStat('Perubahan NPL', nplDelta === null ? '-' : (nplDelta >= 0 ? '+' : '') + displayRatio(nplDelta), nplDelta !== null && nplDelta > 0 ? 'prr-bad' : 'prr-good') +
        '</div><div class="prr-rr-chart">' + trendChart(nplTrend, 'npl_persen', 'NPL') + '</div></div>' +
        '<div class="prr-chart-view" data-prr-chart-view="rr"><p class="prr-chart-caption">RR mengikuti dashboard berbasis baki debet; nominal RR adalah baki debet lancar.</p><div class="prr-rr-summary">' +
          rrStat('RR actual', rrActual === null ? '-' : displayRatio(rrActual), rrActual !== null && rrPrevious !== null && rrDelta < 0 ? 'prr-bad' : 'prr-good') +
          rrStat('RR bulan lalu', rrPrevious === null ? '-' : displayRatio(rrPrevious), '') +
          rrStat('Perubahan RR', rrDelta === null ? '-' : (rrDelta >= 0 ? '+' : '') + displayRatio(rrDelta), rrDelta !== null && rrDelta < 0 ? 'prr-bad' : 'prr-good') +
          rrStat('Nominal RR', latest && num(latest.osc_rr) !== null ? displayNominal(latest.osc_rr) : '-', '') +
        '</div><div class="prr-rr-chart">' + trendChart(trend, 'rr_persen', 'RR') + '</div></div></div>' : '';
    const breakdownHtml = state.widgets.indexOf('kolektibilitas') >= 0 ?
      '<div class="prr-extra-box"><h3>Breakdown Kolektibilitas</h3><p>Saldo bank closing ' + esc(formatDate(model.actualDate)) + ' dibanding closing bulan lalu (' + esc(formatDate(previousMonthKolek.closing_date || previousMonthKolek.requested_date)) + ') dan akhir tahun lalu (' + esc(formatDate(previousYearEndKolek.closing_date || previousYearEndKolek.requested_date)) + ').</p><table class="prr-kolek-table"><thead><tr><th>KOLEKTIBILITAS</th><th>SALDO BANK</th><th>% TOTAL KREDIT</th><th>BULAN LALU<br><small>' + esc(formatDate(previousMonthKolek.closing_date || previousMonthKolek.requested_date)) + '</small></th><th>AKHIR TAHUN LALU<br><small>' + esc(formatDate(previousYearEndKolek.closing_date || previousYearEndKolek.requested_date)) + '</small></th></tr></thead><tbody>' + kolekRows + '<tr><th>Total Kredit</th><td>' + esc(displayNominal(credit.total)) + '</td><td>100%</td><td>' + kolekCompareCell(previousMonthKolek, 'TOTAL', credit.total) + '</td><td>' + kolekCompareCell(previousYearEndKolek, 'TOTAL', credit.total) + '</td></tr></tbody></table></div>' : '';
    if (!riskHtml && !breakdownHtml) return '';
    return '<div class="prr-indicator-extra" data-prr-extra>' + riskHtml + breakdownHtml + '</div>';
  }
  function pendingWidgetsHtml() {
    const pending = widgetDefs.filter(function (item) { return item.pending && state.widgets.indexOf(item.key) >= 0; });
    if (!pending.length) return '';
    return '<div class="prr-pending-grid">' + pending.map(function (item) {
      return '<div class="prr-widget-pending"><span aria-hidden="true">i</span><div><strong>' + esc(item.label) + '</strong><small>' + esc(item.desc) + ' Data sumber belum terhubung.</small></div></div>';
    }).join('') + '</div>';
  }
  function memberSummaryHtml(model) {
    const payload = model.members || {};
    const meta = payload.meta || {};
    if (!Object.keys(meta).length) return '<div class="prr-empty">Data rekap SDM belum tersedia untuk area ini.</div>';
    const total = Math.max(0, Number(meta.total) || 0);
    const count = function (value) { return Math.max(0, Number(value) || 0); };
    const share = function (value) { return total ? Math.max(0, Math.min(100, count(value) / total * 100)) : 0; };
    const stat = function (label, value, detail) { return '<div class="prr-sdm-stat"><span>' + esc(label) + '</span><b>' + esc(value) + '</b>' + (detail ? '<small>' + esc(detail) + '</small>' : '') + '</div>'; };
    const age = meta.age_distribution || {};
    const breakdown = function (label, value) {
      const amount = count(value);
      return '<div class="prr-sdm-row"><span>' + esc(label) + '</span><div class="prr-sdm-track"><i style="width:' + share(amount).toFixed(1) + '%"></i></div><b>' + fmtInt.format(amount) + '</b></div>';
    };
    const positions = Array.isArray(meta.positions) ? meta.positions : [];
    const aoPositions = positions.filter(function (item) { return /\bAO\s+(?:KREDIT|DANA|REMEDIAL)\b/i.test(String(item.jabatan || '')); });
    const male = count(meta.male), female = count(meta.female), unknownGender = count(meta.unknown_gender);
    const genderTotal = male + female + unknownGender;
    const genderShare = function (value) { return genderTotal ? Math.max(0, Math.min(100, value / genderTotal * 100)) : 0; };
    const ageRows = [['<30 tahun', age.under_30], ['30–39 tahun', age['30_39']], ['40–49 tahun', age['40_49']], ['50+ tahun', age['50_plus']]];
    if (count(age.unknown)) ageRows.push(['Umur belum tercatat', age.unknown]);
    const jobGroups = function (itemsToShow) { return ['PE', 'PS', 'STAF'].map(function (group) {
      const items = itemsToShow.filter(function (item) { return item.group_jabatan === group; });
      if (!items.length) return '';
      const label = group === 'STAF' ? 'Staf' : group;
      return '<div class="prr-sdm-job-group"><h4>' + label + '</h4><div class="prr-sdm-job-list">' + items.map(function (item) { return breakdown(item.jabatan, item.jumlah); }).join('') + '</div></div>';
    }).join('') || '<div class="prr-sdm-no-jobs">Belum ada rekap jabatan.</div>'; };
    const compactJobs = aoPositions.length ? aoPositions.map(function (item) { return '<div class="prr-sdm-ao-item">' + breakdown(item.jabatan, item.jumlah) + '</div>'; }).join('') : '<div class="prr-sdm-ao-empty">Rekap AO Dana, AO Kredit, dan AO Remedial belum tersedia pada kantor ini.</div>';
    const jobToggle = '<button type="button" class="prr-sdm-job-toggle" data-prr-member-jobs-toggle data-total-jobs="' + positions.length + '" aria-expanded="false" aria-label="Buka rekap jabatan lengkap"><span class="prr-sdm-job-toggle-icon" data-prr-job-toggle-icon aria-hidden="true">↗</span><span data-prr-job-toggle-label>Lihat lengkap (' + fmtInt.format(positions.length) + ')</span></button>';
    return '<div class="prr-sdm-summary"><div class="prr-sdm-stats">' +
      stat('Pegawai aktif', fmtInt.format(total), model.scope.label) +
      stat('Laki-laki', fmtInt.format(male), genderTotal ? fmt.format(genderShare(male)) + '%' : '') +
      stat('Perempuan', fmtInt.format(female), genderTotal ? fmt.format(genderShare(female)) + '%' : '') +
      stat('Rata-rata umur', meta.average_age == null ? '—' : fmt.format(meta.average_age) + ' th', 'Per closing ' + state.closing) +
      '</div><div class="prr-sdm-details"><section class="prr-sdm-section"><h3>Komposisi gender</h3><div class="prr-sdm-gender-track"><i class="male" style="width:' + genderShare(male).toFixed(1) + '%"></i><i class="female" style="width:' + genderShare(female).toFixed(1) + '%"></i><i class="unknown" style="width:' + genderShare(unknownGender).toFixed(1) + '%"></i></div><div class="prr-sdm-legend"><span><i class="male"></i>Laki-laki ' + fmtInt.format(male) + '</span><span><i class="female"></i>Perempuan ' + fmtInt.format(female) + '</span>' + (unknownGender ? '<span><i class="unknown"></i>Belum tercatat ' + fmtInt.format(unknownGender) + '</span>' : '') + '</div></section><section class="prr-sdm-section"><h3>Kelompok umur</h3>' + ageRows.map(function (row) { return breakdown(row[0], row[1]); }).join('') + '</section></div><section class="prr-sdm-section prr-sdm-job-section"><div class="prr-sdm-job-head"><h3>Rekap jabatan</h3>' + jobToggle + '</div><div data-prr-job-compact class="prr-sdm-ao-grid">' + compactJobs + '</div><div data-prr-job-complete hidden class="prr-sdm-jobs">' + jobGroups(positions) + '</div></section></div>';
  }
  function dashboardValue(model, def) {
    return def.metric === 'ratio' ? model.actual.ratio[def.ratioCode] : model.actual[def.metric];
  }
  function dashboardTarget(model, def) { return targetValue(model, def.code, false); }
  function dashboardAchievement(model, def) {
    const actual = num(dashboardValue(model, def));
    const target = num(dashboardTarget(model, def));
    if (actual === null || target === null || target === 0) return null;
    return def.inverse ? target / actual * 100 : actual / target * 100;
  }
  function dashboardYearAchievement(model, def) {
    const actual = num(dashboardValue(model, def));
    const target = num(targetValue(model, def.code, true));
    if (actual === null || target === null || target === 0) return null;
    return def.inverse ? target / actual * 100 : actual / target * 100;
  }
  function previousMonthClosing(dateText) {
    const date = new Date(String(dateText).slice(0,10) + 'T00:00:00');
    if (Number.isNaN(date.getTime())) return '';
    date.setDate(0);
    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2,'0') + '-' + String(date.getDate()).padStart(2,'0');
  }
  function exactHistoryValue(rows, dateText) {
    const row = (Array.isArray(rows) ? rows : []).find(function (item) { return String(item.tanggal || '').slice(0,10) === dateText; });
    return row ? num(row.nilai) : null;
  }
  function comparisonReference(model, def, mode) {
    const year = Number(String(state.closing).slice(0,4));
    const date = mode === 'month' ? previousMonthClosing(state.closing) : (year - 1) + '-12-31';
    const field = mode === 'month' ? 'nominal_bulan_lalu' : 'nominal_tahun_lalu';
    if (def.key === 'kredit' || def.key === 'npl') {
      const histories = model.comparisonHistory || {};
      if (mode === 'month' && String(date).slice(0,4) === String(year)) {
        return exactHistoryValue(histories.currentYear && histories.currentYear[def.key], date);
      }
      return exactHistoryValue(histories.previousYear && histories.previousYear[def.key], date);
    }
    const availabilityKey = mode === 'month' ? 'bulan_lalu' : 'tahun_lalu';
    if (path(model.snapshot, 'info_tanggal.tersedia.' + availabilityKey, null) === false) return null;
    if (def.key === 'aset') return num(path(model.snapshot, 'makro.aset.' + field, null));
    if (def.key === 'damas') return num(path(model.snapshot, 'makro.dpk.' + field, null));
    if (def.key === 'laba') return num(path(model.snapshot, 'makro.laba_rugi.' + field, null));
    if (def.key === 'pendapatan' || def.key === 'biaya') return num(path(model.snapshot, 'makro.' + def.key + '.' + field, null));
    if (def.key === 'bopo') return num(path(model.snapshot, 'kesehatan_rasio.bopo.' + (mode === 'month' ? 'persen_bulan_lalu' : 'persen_tahun_lalu'), null));
    if (def.key === 'roa') return num(path(model.snapshot, 'kesehatan_rasio.roa.' + (mode === 'month' ? 'persen_bulan_lalu' : 'persen_tahun_lalu'), null));
    return null;
  }
  function comparisonLabel(mode) {
    const dateText = mode === 'month' ? previousMonthClosing(state.closing) : (Number(String(state.closing).slice(0,4)) - 1) + '-12-31';
    if (!dateText) return mode === 'month' ? 'vs bulan sebelumnya' : 'vs akhir tahun lalu';
    const date = new Date(dateText + 'T00:00:00');
    const label = new Intl.DateTimeFormat('id-ID', {month:'short',year:'numeric'}).format(date);
    return 'vs ' + label;
  }
  function comparisonMarkup(model, def, mode) {
    const current = num(dashboardValue(model, def));
    const reference = comparisonReference(model, def, mode);
    if (current === null || reference === null) {
      return '<span class="prr-kpi-compare-label">' + esc(comparisonLabel(mode)) + '</span><b class="prr-kpi-compare-na">' + (model.historyLoading ? 'Memuat pembanding...' : 'Data pembanding tidak tersedia') + '</b>';
    }
    const delta = current - reference;
    const tone = Math.abs(delta) < 0.000001 ? 'neutral' : ((def.inverse ? delta < 0 : delta > 0) ? 'good' : 'bad');
    const arrow = delta > 0 ? '▲' : (delta < 0 ? '▼' : '•');
    let value;
    if (def.metric === 'ratio') {
      value = (delta > 0 ? '+' : '') + fmt.format(delta) + ' pp';
    } else {
      const growth = reference !== 0 ? delta / Math.abs(reference) * 100 : null;
      value = (delta > 0 ? '+' : delta < 0 ? '−' : '') + displayNominal(Math.abs(delta));
      if (growth !== null) value += ' · ' + (growth > 0 ? '+' : '') + fmt.format(growth) + '%';
    }
    return '<span class="prr-kpi-compare-label">' + esc(comparisonLabel(mode)) + '</span><b class="prr-kpi-compare-' + tone + '">' + arrow + ' ' + esc(value) + '</b>';
  }
  function miniLine(rows, field) {
    const values = (rows || []).map(function (row) { return num(field ? row[field] : row.nilai); }).filter(function (value) { return value !== null; });
    if (values.length < 2) return '<div class="prr-mini-track"><i style="width:0%"></i></div>';
    const min = Math.min.apply(null, values), max = Math.max.apply(null, values), spread = max - min || Math.abs(max || 1) * .08;
    const points = values.map(function (value, index) { const x = index * 100 / (values.length - 1); const y = 22 - (value - min) / spread * 18; return x.toFixed(1) + ',' + y.toFixed(1); }).join(' ');
    return '<svg class="prr-mini-line" viewBox="0 0 100 24" preserveAspectRatio="none" aria-hidden="true"><polyline points="' + points + '"></polyline></svg>';
  }
  function dashboardKpiHtml(model) {
    const renderCard = function (def) {
      const actual = dashboardValue(model, def), achievement = dashboardAchievement(model, def), yearAchievement = dashboardYearAchievement(model, def);
      const format = def.metric === 'ratio' ? displayRatio : displayNominal;
      const width = achievement === null ? 0 : Math.max(0, Math.min(100, achievement));
      let historyRows = model.history && model.history[def.metric] ? model.history[def.metric] : [];
      let historyField = null;
      if (def.key === 'npl') { historyRows = model.trend || []; historyField = 'npl_persen'; }
      const trend = miniLine(historyRows, historyField);
      const historyMetric = ['aset','kredit','damas','laba','npl'].indexOf(def.key) >= 0;
      return '<article class="prr-kpi prr-kpi-' + esc(def.tone) + (historyMetric ? ' is-history' : '') + '"' + (historyMetric ? ' tabindex="0" role="button" data-prr-history="' + esc(def.key) + '" data-prr-scope-type="' + esc(model.scope.type) + '" data-prr-scope-value="' + esc(model.scope.value || '') + '" data-prr-year="' + esc(String(state.closing).slice(0,4)) + ' title="Klik untuk melihat history closing"' : '') + '><div class="prr-kpi-top"><span class="prr-kpi-icon">' + esc(def.icon) + '</span><div><small>' + esc(def.label) + '</small><strong>' + esc(format(actual)) + '</strong></div></div>' + trend + '<div class="prr-kpi-comparisons"><div class="prr-kpi-comparison-month">' + comparisonMarkup(model,def,'month') + '</div><div class="prr-kpi-comparison-year">' + comparisonMarkup(model,def,'year') + '</div></div><div class="prr-kpi-rbb"><span>RBB BULAN <b class="' + (achievement !== null && achievement >= 100 ? 'prr-good' : 'prr-bad') + '">' + esc(displayRatio(achievement)) + '</b></span><span>RBB DES <b class="' + (yearAchievement !== null && yearAchievement >= 100 ? 'prr-good' : 'prr-bad') + '">' + esc(displayRatio(yearAchievement)) + '</b></span></div><div class="prr-kpi-meter"><i style="width:' + width.toFixed(1) + '%"></i></div></article>';
    };
    return '<div class="prr-kpi-grid"><div class="prr-kpi-row prr-kpi-row-top">' + dashboardKpiCards.slice(0,4).map(renderCard).join('') + '</div><div class="prr-kpi-row prr-kpi-row-bottom">' + dashboardKpiCards.slice(4).map(renderCard).join('') + '</div></div>';
  }
  function financialTrendHtml(model) {
    const defs = [
      {key:'aset', label:'Aset', color:'#2874d0'},
      {key:'kredit', label:'Kredit', color:'#24a65a'},
      {key:'damas', label:'DPK', color:'#8255d6'},
      {key:'laba', label:'Laba', color:'#e6a02a'}
    ].map(function (def) {
      const rows = model.history && model.history[def.key] || [];
      return Object.assign({}, def, {rows:rows, values:new Map(rows.map(function (row) { return [String(row.tanggal).slice(0,10), num(row.nilai)]; }))});
    }).filter(function (def) { return def.rows.length > 0; });
    if (!defs.length) return model.historyLoading ? '<div class="prr-loading" style="min-height:110px"><span class="prr-spinner"></span><span>Memuat tren closing...</span></div>' : '<div class="prr-empty">History closing belum tersedia untuk periode ini.</div>';
    const dates = Array.from(new Set(defs.reduce(function (all, def) { return all.concat(def.rows.map(function (row) { return String(row.tanggal).slice(0,10); })); }, []))).sort();
    const values = defs.reduce(function (all, def) { return all.concat(dates.map(function (date) { return def.values.get(date); }).filter(function (value) { return value !== null && value !== undefined; })); }, []);
    if (!dates.length || !values.length) return '<div class="prr-empty">History closing belum tersedia untuk periode ini.</div>';
    let min = Math.min.apply(null, values), max = Math.max.apply(null, values);
    if (min === max) { const offset = Math.abs(max || 1) * .08; min -= offset; max += offset; }
    else { const offset = (max - min) * .1; min -= offset; max += offset; }
    const width = 760, height = 270, pad = {top:20,right:18,bottom:42,left:78}, plotWidth = width - pad.left - pad.right, plotHeight = height - pad.top - pad.bottom;
    const x = function (index) { return dates.length === 1 ? pad.left + plotWidth / 2 : pad.left + plotWidth * index / (dates.length - 1); };
    const y = function (value) { return pad.top + (max - value) / (max - min) * plotHeight; };
    const grid = [0,1,2,3,4].map(function (index) { const yy = pad.top + plotHeight * index / 4, value = max - (max - min) * index / 4; return '<line class="prr-trend-grid" x1="' + pad.left + '" y1="' + yy + '" x2="' + (width-pad.right) + '" y2="' + yy + '"></line><text class="prr-trend-axis" x="' + (pad.left-8) + '" y="' + (yy+4) + '" text-anchor="end">' + esc(displayNominal(value)) + '</text>'; }).join('');
    const lines = defs.map(function (def) {
      const points = dates.map(function (date, index) { const value = def.values.get(date); return value === null || value === undefined ? null : x(index).toFixed(1) + ',' + y(value).toFixed(1); }).filter(Boolean);
      if (!points.length) return '';
      const dots = dates.map(function (date, index) { const value = def.values.get(date); return value === null || value === undefined ? '' : '<circle cx="' + x(index) + '" cy="' + y(value) + '" r="3.2" fill="#fff" stroke="' + def.color + '" stroke-width="2"><title>' + esc(formatDate(date)) + ': ' + esc(displayNominal(value)) + '</title></circle>'; }).join('');
      return '<polyline fill="none" stroke="' + def.color + '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="' + points.join(' ') + '"></polyline>' + dots;
    }).join('');
    const labels = dates.map(function (date, index) { const month = date.slice(5,7), year = date.slice(0,4); return (dates.length <= 8 || index === 0 || index === dates.length-1 || index % Math.ceil(dates.length/6) === 0) ? '<text class="prr-trend-axis" x="' + x(index) + '" y="' + (height-14) + '" text-anchor="middle">' + esc(new Intl.DateTimeFormat('id-ID',{month:'short'}).format(new Date(year,Number(month)-1,1))) + '</text>' : ''; }).join('');
    const legend = defs.map(function (def) { return '<span><i style="background:' + def.color + '"></i>' + esc(def.label) + '</span>'; }).join('');
    return '<div class="prr-trend-legend">' + legend + '</div><svg class="prr-finance-chart" viewBox="0 0 ' + width + ' ' + height + '" role="img" aria-label="Tren aset, kredit, DPK, dan laba"><g>' + grid + '</g>' + lines + '<g>' + labels + '</g></svg>';
  }
  function dashboardPanel(title, subtitle, body, className) {
    return '<section class="prr-dashboard-panel ' + esc(className || '') + '"><header><div><h2>' + esc(title) + '</h2><p>' + esc(subtitle || '') + '</p></div></header>' + body + '</section>';
  }
  function dashboardRbbHtml(model) {
    const defs = [
      {label:'Aset', code:'1', metric:'aset'},
      {label:'Kredit', code:'5', metric:'kredit'},
      {label:'Tabungan', code:'3', metric:'tabungan'},
      {label:'Deposito', code:'4', metric:'deposito'},
      {label:'Total DPK', code:'2', metric:'damas'},
      {label:'Laba sebelum pajak', code:'8', metric:'laba'}, {label:'BOPO', code:'20', metric:'ratio', ratioCode:'20', inverse:true},
      {label:'NPL saldo bank', code:'15', metric:'ratio', ratioCode:'15', inverse:true}
    ];
    const rows = defs.map(function (def) {
      const value = dashboardValue(model,def);
      const target = targetValue(model,def.code,false), yearTarget = targetValue(model,def.code,true);
      const achievement = dashboardAchievement(model,def), yearAchievement = dashboardYearAchievement(model,def);
      const format = def.metric === 'ratio' ? displayRatio : displayNominal;
      const achievementCell=function(pct) {
        const width=pct===null?0:Math.max(0,Math.min(100,pct));
        const tone=pct===null?'prr-kpi-compare-na':(pct>=100?'prr-good':'prr-bad');
        return '<div class="prr-rbb-achievement"><div class="prr-progress"><i style="width:'+width.toFixed(1)+'%"></i></div><b class="'+tone+'">'+esc(displayRatio(pct))+'</b></div>';
      };
      return '<tr><th>' + esc(def.label) + '</th><td>' + esc(format(value)) + '</td><td>' + esc(format(target)) + '</td><td>' + achievementCell(achievement) + '</td><td>' + esc(format(yearTarget)) + '</td><td>' + achievementCell(yearAchievement) + '</td></tr>';
    }).join('');
    return '<div class="prr-rbb-table"><table><thead><tr><th>INDIKATOR</th><th>REALISASI</th><th>RBB BULAN</th><th>CAPAIAN BULAN</th><th>RBB DESEMBER</th><th>CAPAIAN DESEMBER</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
  }
  function creditPlanHtml(model) {
    if (model.creditPlanLoading) return '<div class="prr-loading" style="min-height:54px"><span class="prr-spinner"></span><span>Memuat target kredit sampai Desember...</span></div>';
    if (model.creditPlanError) return '<div class="prr-empty">' + esc(model.creditPlanError) + '</div>';
    const data = model.creditPlan || {}, meta = data.meta || {}, months = Array.isArray(data.months) ? data.months : [];
    if (!months.length) return '<div class="prr-empty">Target RBB kredit tahunan belum tersedia.</div>';
    const monthlyRows = months.map(function (row) {
      const target = num(row.target), actual = num(row.realisasi), pct = num(row.pencapaian_persen);
      const shortfall = actual === null || target === null ? null : Math.max(0, target - actual);
      const status = actual === null ? 'Belum berjalan' : (target === null ? 'Target belum ada' : (target > actual ? 'Kurang ' + displayNominal(shortfall) : (actual > target ? 'Lebih ' + displayNominal(actual - target) : 'Sesuai target')));
      const statusClass = actual === null || target === null ? '' : (target > actual ? 'prr-credit-plan-bad' : 'prr-credit-plan-good');
      const monthDate = new Date(String(row.periode || '').slice(0,10) + 'T00:00:00');
      const monthLabel = Number.isNaN(monthDate.getTime()) ? row.periode : new Intl.DateTimeFormat('id-ID', {month:'long'}).format(monthDate);
      const rowTooltip = monthLabel + ' · Target RBB: ' + displayNominal(target) + ' · Realisasi: ' + displayNominal(actual) + ' · ' + status + ' · Capaian: ' + displayRatio(pct);
      return '<tr tabindex="0" aria-label="' + esc(rowTooltip) + '" data-prr-tooltip="' + esc(rowTooltip) + '"><th scope="row">' + esc(monthLabel) + '</th><td>' + esc(displayNominal(target)) + '</td><td>' + esc(displayNominal(actual)) + '</td><td class="' + statusClass + '">' + esc(status) + '</td><td class="' + (pct === null ? '' : (pct >= 100 ? 'prr-credit-plan-good' : 'prr-credit-plan-bad')) + '">' + esc(displayRatio(pct)) + '</td></tr>';
    }).join('');
    const stats = [
      ['RBB ' + Number(meta.tahun || String(state.closing).slice(0,4)) + ' · s.d. Desember', meta.target_rbb_tahunan],
      ['Target s.d. bulan ini', meta.target_sampai_bulan_ini],
      ['Realisasi s.d. closing', meta.realisasi_sampai_closing],
      ['Kurang s.d. bulan ini', meta.kekurangan_sampai_bulan_ini],
      ['Sisa target tahunan', meta.sisa_target_tahunan]
    ].map(function (item) { const tooltip = item[0] + ': ' + displayNominal(item[1]) + ' · Closing ' + formatDate(state.closing); return '<div class="prr-credit-plan-stat" data-prr-tooltip="' + esc(tooltip) + '" tabindex="0" aria-label="' + esc(tooltip) + '"><span>' + esc(item[0]) + '</span><b>' + esc(displayNominal(item[1])) + '</b></div>'; }).join('');
    const annualPct = displayRatio(meta.pencapaian_tahunan_persen);
    const completeness = Number(meta.jumlah_bulan_target || 0) < 12 ? 'Target RBB tersedia untuk ' + Number(meta.jumlah_bulan_target || 0) + ' dari 12 bulan.' : 'Target RBB tersedia sampai Desember.';
    const annualTooltip = 'Capaian RBB ' + (meta.tahun || String(state.closing).slice(0,4)) + ': ' + annualPct + '\nRealisasi s.d. closing: ' + displayNominal(meta.realisasi_sampai_closing) + '\nTarget RBB tahunan: ' + displayNominal(meta.target_rbb_tahunan);
    return '<div class="prr-credit-plan-topline"><span class="prr-credit-plan-badge" data-prr-tooltip="' + esc(annualTooltip) + '" tabindex="0" aria-label="' + esc(annualTooltip) + '"><b>' + esc(annualPct) + '</b><span>CAPAIAN RBB ' + esc(meta.tahun || String(state.closing).slice(0,4)) + '</span></span></div><div class="prr-credit-plan-stats">' + stats + '</div><div class="prr-credit-plan-table-wrap"><table class="prr-credit-plan-table"><thead><tr><th>BULAN</th><th>RBB</th><th>REALISASI</th><th>KURANG / LEBIH</th><th>CAPAIAN</th></tr></thead><tbody>' + monthlyRows + '</tbody></table></div><p class="prr-credit-plan-note">' + esc(completeness) + ' Bulan mendatang menampilkan target RBB tanpa realisasi; nilai kurang dihitung dari target dikurangi realisasi bulan tersebut.</p>';
  }
  function creditTrendHtml(model) {
    if (model.creditTrendLoading) return '<div class="prr-loading" style="min-height:220px"><span class="prr-spinner"></span><span>Memuat grafik tahun berjalan...</span></div>';
    if (model.creditTrendError) return '<div class="prr-empty">' + esc(model.creditTrendError) + '</div>';
    const rows = Array.isArray(model.creditTrend) ? model.creditTrend : [];
    if (!rows.length) return '<div class="prr-empty">Data tren realisasi dan run off belum tersedia.</div>';
    const realisasi = rows.map(function (row) { return num(row.total_realisasi) || 0; });
    const runoff = rows.map(function (row) { return num(row.total_runoff) || 0; });
    const values = realisasi.concat(runoff);
    let min = Math.min(0, Math.min.apply(null, values));
    let max = Math.max.apply(null, values);
    if (min === max) max = min + Math.abs(max || 1) * .1;
    else max += (max - min) * .12;
    const width = 720, height = 300, pad = {top:24,right:56,bottom:48,left:70};
    const plotWidth = width - pad.left - pad.right, plotHeight = height - pad.top - pad.bottom;
    const x = function (index) { return rows.length === 1 ? pad.left + plotWidth / 2 : pad.left + plotWidth * index / (rows.length - 1); };
    const y = function (value) { return pad.top + (max - value) / (max - min) * plotHeight; };
    const baseline = y(0);
    const grid = [0,1,2,3,4].map(function (index) {
      const yy = pad.top + plotHeight * index / 4, value = max - (max - min) * index / 4;
      return '<line class="prr-credit-trend-grid" x1="' + pad.left + '" y1="' + yy + '" x2="' + (width-pad.right) + '" y2="' + yy + '"></line><text class="prr-credit-trend-axis" x="' + (pad.left-8) + '" y="' + (yy+3) + '" text-anchor="end">' + esc(displayNominal(value)) + '</text>';
    }).join('');
    const series = [
      {label:'Realisasi', values:realisasi, line:'prr-credit-trend-real-line', area:'prr-credit-trend-real-area', dot:'real'},
      {label:'Run Off', values:runoff, line:'prr-credit-trend-runoff-line', area:'prr-credit-trend-runoff-area', dot:'runoff'}
    ];
    const seriesSvg = series.map(function (item) {
      const points = item.values.map(function (value,index) { return x(index).toFixed(1) + ',' + y(value).toFixed(1); });
      const areaPoints = x(0) + ',' + baseline + ' ' + points.join(' ') + ' ' + x(rows.length-1) + ',' + baseline;
      const dots = item.values.map(function (value,index) {
        const row = rows[index];
        const dateLabel = row.label || formatDate(row.tanggal);
        const realisasiDetail = 'Realisasi: ' + displayNominal(realisasi[index]) + ' (Kredit ' + displayNominal(row.realisasi_kredit) + ' · Restrukturisasi ' + displayNominal(row.restruck_kredit || row.restrukturisasi) + ')';
        const runoffDetail = 'Run Off: ' + displayNominal(runoff[index]) + ' (Lunas ' + displayNominal(row.total_lunas) + ', ' + fmtInt.format(num(row.noa_lunas) || 0) + ' NOA · Angsuran ' + displayNominal(row.total_angsuran) + ', ' + fmtInt.format(num(row.noa_angsuran) || 0) + ' NOA)';
        const growth = num(row.growth) === null ? realisasi[index] - runoff[index] : num(row.growth);
        const tooltip = dateLabel + ' · ' + realisasiDetail + ' · ' + runoffDetail + ' · Growth: ' + displayNominal(growth);
        const pointY = y(value), crowded = Math.abs(y(realisasi[index]) - y(runoff[index])) < 20;
        let labelY = pointY - 9;
        if (crowded && pointY < 35) labelY = pointY + (item.dot === 'real' ? 12 : 25);
        else if (crowded && pointY > height - pad.bottom - 28) labelY = pointY - (item.dot === 'real' ? 10 : 23);
        else if (crowded) labelY = item.dot === 'real' ? pointY - 10 : pointY + 17;
        if (labelY < 12) labelY = y(value) + 16;
        if (labelY > height - pad.bottom - 2) labelY = y(value) - 8;
        const edge = index === 0 ? 1 : (index === rows.length-1 ? -1 : 0);
        const labelX = x(index) + edge * 5, labelAnchor = edge === 0 ? 'middle' : (edge > 0 ? 'start' : 'end');
        const label = '<text class="prr-credit-trend-value ' + item.dot + '" x="' + labelX + '" y="' + labelY + '" text-anchor="' + labelAnchor + '">' + esc(displayNominal(value)) + '</text>';
        return '<circle class="prr-credit-trend-dot ' + item.dot + '" cx="' + x(index) + '" cy="' + y(value) + '" r="5" tabindex="0" aria-label="' + esc(tooltip) + '" data-prr-tooltip="' + esc(tooltip) + '"></circle>' + label;
      }).join('');
      return '<polygon class="' + item.area + '" points="' + areaPoints + '"></polygon><polyline class="' + item.line + '" points="' + points.join(' ') + '"></polyline>' + dots;
    }).join('');
    const labels = rows.map(function (row,index) {
      const visible = rows.length <= 8 || index === 0 || index === rows.length-1 || index % Math.ceil(rows.length/6) === 0;
      return visible ? '<text class="prr-credit-trend-axis" x="' + x(index) + '" y="' + (height-13) + '" text-anchor="middle">' + esc(row.label || row.tanggal) + '</text>' : '';
    }).join('');
    const totalRealisasi = realisasi.reduce(function (sum,value) { return sum + value; }, 0);
    const totalRunoff = runoff.reduce(function (sum,value) { return sum + value; }, 0);
    const net = totalRealisasi - totalRunoff;
    const totalExpansion = rows.reduce(function (sum,row) { return sum + (num(row.realisasi_kredit) || 0); }, 0);
    const totalRestruck = rows.reduce(function (sum,row) { const value = num(row.restruck_kredit); return sum + (value === null ? (num(row.restrukturisasi) || 0) : value); }, 0);
    const totalLunas = rows.reduce(function (sum,row) { return sum + (num(row.total_lunas) || 0); }, 0);
    const totalAngsuran = rows.reduce(function (sum,row) { return sum + (num(row.total_angsuran) || 0); }, 0);
    const averageRunoff = totalRunoff / Math.max(rows.length,1);
    const recommendedRealization = averageRunoff * 1.10;
    const summaryItems = [
      {key:'real',label:'Perluasan YTD',value:totalExpansion,tip:'Realisasi kredit baru / perluasan YTD: '+displayNominal(totalExpansion)+'. Tidak termasuk restrukturisasi.'},
      {key:'restruct',label:'Restrukturisasi YTD',value:totalRestruck,tip:'Restrukturisasi YTD: '+displayNominal(totalRestruck)+'.'},
      {key:'installment',label:'Angsuran bersih YTD',value:totalAngsuran,tip:'Total angsuran di luar pelunasan YTD: '+displayNominal(totalAngsuran)+'.'},
      {key:'paid',label:'Pelunasan YTD',value:totalLunas,tip:'Total pelunasan YTD: '+displayNominal(totalLunas)+'.'},
      {key:'runoff',label:'Total Run Off YTD',value:totalRunoff,tip:'Run Off YTD: '+displayNominal(totalRunoff)+', termasuk angsuran dan pelunasan.'},
      {key:'average',label:'Rata-rata Run Off / bulan',value:averageRunoff,tip:'Rata-rata Run Off per bulan: '+displayNominal(averageRunoff)+'; total '+displayNominal(totalRunoff)+' dibagi '+rows.length+' bulan berjalan.'},
      {key:'net',label:'Net Growth YTD',value:net,tip:'Net Growth YTD: total realisasi termasuk restrukturisasi '+displayNominal(totalRealisasi)+' dikurangi Run Off '+displayNominal(totalRunoff)+'.'},
      {key:'target',label:'Rekomendasi / bulan (+10%)',value:recommendedRealization,tip:'Target indikatif agar tumbuh: rata-rata Run Off '+displayNominal(averageRunoff)+' ditambah buffer 10% ('+displayNominal(recommendedRealization)+'). Berlaku untuk realisasi total termasuk restrukturisasi; belum memperhitungkan pemulihan net growth YTD.'}
    ];
    const summary = '<div class="prr-credit-trend-summary">' + summaryItems.map(function (item) { return '<div class="prr-trend-summary-item '+item.key+'" data-prr-tooltip="'+esc(item.tip)+'" tabindex="0" aria-label="'+esc(item.tip)+'"><span>'+esc(item.label)+'</span><b>'+esc(displayNominal(item.value))+'</b></div>'; }).join('') + '</div>';
    return '<div class="prr-credit-trend-legend"><span><i style="background:#10b981"></i>Realisasi</span><span><i style="background:#ef4444"></i>Run Off</span></div><svg class="prr-credit-trend-svg" viewBox="0 0 ' + width + ' ' + height + '" role="img" aria-label="Tren realisasi kredit dibanding run off tahun berjalan"><g>' + grid + '</g>' + seriesSvg + '<g>' + labels + '</g></svg>' + summary;
  }
  function dashboardRiskSummaryHtml(model) {
    const keys = ['npl','bopo','roa','car'];
    const rows = keys.map(function (key) {
      const def = dashboardKpis.find(function (item) { return item.key === key; });
      const actual = dashboardValue(model,def), target = dashboardTarget(model,def), achievement = dashboardAchievement(model,def);
      const okay = achievement !== null && achievement >= 100;
      const status=actual===null?'Realisasi belum tersedia':(target===null?'Target belum tersedia':(achievement===null?'Capaian belum bisa dihitung':(okay?'Sesuai target':'Perlu perhatian')));
      const tone=achievement===null?'prr-kpi-compare-na':(okay?'prr-good':'prr-bad');
      return '<div class="prr-risk-row"><span>' + esc(def.label) + '</span><b>' + esc(displayRatio(actual)) + '</b><small>' + esc(target === null ? 'Target belum ada' : 'RBB ' + displayRatio(target)) + '</small><em class="' + tone + '">' + status + '</em></div>';
    }).join('');
    return '<div class="prr-risk-list">' + rows + '</div>';
  }
  function dashboardAlertsHtml(model) {
    const alerts = dashboardKpis.map(function (def) {
      const actual = dashboardValue(model,def), target = dashboardTarget(model,def), achievement = dashboardAchievement(model,def);
      if (actual === null || target === null || achievement === null || achievement >= 100) return null;
      const format = def.metric === 'ratio' ? displayRatio : displayNominal;
      return {label:def.label, actual:format(actual), achievement:displayRatio(achievement)};
    }).filter(Boolean).slice(0,6);
    if (!alerts.length) return '<div class="prr-alert-clear"><b>✓</b><span>Tidak ada indikator di bawah target RBB pada closing ini.</span></div>';
    return '<div class="prr-alert-list">' + alerts.map(function (item) { return '<div class="prr-alert-row"><span class="prr-alert-mark">!</span><div><b>' + esc(item.label) + '</b><small>Realisasi ' + esc(item.actual) + ' · capaian ' + esc(item.achievement) + '</small></div><em>Perlu perhatian</em></div>'; }).join('') + '</div>';
  }
  function dashboardRankingHtml(model) {
    const result=model.officePerformance||{}, rows=Array.isArray(result.rows)?result.rows:[], meta=result.meta||{};
    if (model.officePerformanceLoading) return '<div class="prr-loading" style="min-height:110px"><span class="prr-spinner"></span><span>Memuat rekap kinerja kantor...</span></div>';
    if (result.error) return pendingPanelHtml('Data kinerja kantor gagal dimuat',result.error,'!');
    if (!rows.length) return '<div class="prr-empty">Belum ada snapshot kinerja kantor untuk closing ini.</div>';
    const metricDefs=[{key:'kredit',label:'Kredit'},{key:'tabungan',label:'Tabungan'},{key:'deposito',label:'Deposito'}];
    const percentCell=function(metric,metricKey,key,periodLabel,periodKey) {
      const data=metric||{}, pct=num(data[key]), current=num(data.current), baseline=num(data[periodKey]);
      const delta=current===null||baseline===null?null:current-baseline;
      const date=((meta.snapshot_dates||{})[metricKey]||{})[periodKey] || (meta.periods||{})[periodKey] || '-';
      const groupComparisonMissing=path(meta,'group_comparison_available.'+metricKey+'.'+periodKey,true)===false;
      const title=groupComparisonMissing
        ? 'Perbandingan '+periodLabel+' kankas tidak tersedia: snapshot '+date+' belum memiliki kode_group1 yang cukup untuk pemetaan akurat.'
        : pct===null
        ? (delta===null?'Pembanding '+periodLabel+' tidak tersedia untuk '+date+'.':'Selisih '+periodLabel+': '+(delta>0?'+':delta<0?'−':'')+displayNominal(Math.abs(delta))+'; persentase tidak tersedia. Closing pembanding '+displayNominal(baseline)+' pada '+date+'.')
        : 'Selisih '+periodLabel+': '+(delta>0?'+':delta<0?'−':'')+displayNominal(Math.abs(delta))+' ('+(pct>0?'+':'')+fmt.format(pct)+'%) dari '+displayNominal(baseline)+' pada '+date+'.';
      const tone=pct===null?'prr-office-na':(pct>=0?'prr-office-up':'prr-office-down');
      const amount=delta===null?'—':(delta>0?'+':delta<0?'−':'')+displayNominal(Math.abs(delta));
      const percentage=pct===null?'—':(pct>0?'+':'')+fmt.format(pct)+'%';
      return '<td><span class="prr-office-change '+tone+'" title="'+esc(title)+'" tabindex="0"><b>'+esc(amount)+'</b><small>'+esc(percentage)+'</small></span></td>';
    };
    const rowsHtml=rows.map(function(row) {
      const cells=metricDefs.map(function(def) {
        const data=(row.metrics||{})[def.key]||{};
        const date=((meta.snapshot_dates||{})[def.key]||{}).current || (meta.periods||{}).current || '-';
        const amountTitle=def.label+' closing '+date+' · basis '+({kredit:'saldo bank',tabungan:'saldo',deposito:'saldo akhir'}[def.key])+'.';
        return '<td class="prr-office-amount" title="'+esc(amountTitle)+'">'+esc(displayNominal(data.current))+'</td>'+percentCell(data,def.key,'change_month_pct','bulan sebelumnya','previous_month')+percentCell(data,def.key,'change_year_pct','akhir tahun sebelumnya','previous_year');
      }).join('');
      return '<tr><th scope="row">'+esc(row.nama||'Kantor')+'</th>'+cells+'</tr>';
    }).join('');
    const header='<thead><tr><th scope="col" rowspan="2">'+esc(meta.scope_label||model.scope.label)+'</th>'+metricDefs.map(function(def){return '<th scope="colgroup" colspan="3">'+esc(def.label.toUpperCase())+'</th>';}).join('')+'</tr><tr>'+metricDefs.map(function(){return '<th scope="col">Closing</th><th scope="col">vs Bln lalu</th><th scope="col">vs Thn lalu</th>';}).join('')+'</tr></thead>';
    const hasCurrentSnapshot=metricDefs.some(function(def){return (meta.snapshot_dates||{})[def.key] && meta.snapshot_dates[def.key].current;});
    const note='Perubahan dibanding closing bulan sebelumnya dan akhir tahun sebelumnya (31 Desember). Arahkan kursor atau fokuskan persentase untuk melihat nominal dan tanggal pembanding.';
    const missingNotice=hasCurrentSnapshot?'':'<p class="prr-office-warning">Snapshot realisasi tidak ditemukan pada tanggal closing maupun fallback H-7; angka ditandai — agar tidak terbaca sebagai nol.</p>';
    const incompleteGroupYear=meta.scope_type==='branch'&&metricDefs.some(function(def){return path(meta,'group_comparison_available.'+def.key+'.previous_year',true)===false;});
    const groupNotice=incompleteGroupYear?'<p class="prr-office-warning">Pembanding kankas akhir tahun tidak ditampilkan karena snapshot '+esc((meta.periods||{}).previous_year||'akhir tahun')+' belum memiliki kode_group1 yang cukup untuk pemetaan akurat.</p>':'';
    return '<div class="prr-office-table-wrap"><table class="prr-office-table">'+header+'<tbody>'+rowsHtml+'</tbody></table></div>'+missingNotice+groupNotice+'<p class="prr-office-note">'+esc(note)+'</p>';
  }
  function pendingPanelHtml(title, message, icon) {
    return '<div class="prr-pending-panel"><span>' + esc(icon || 'i') + '</span><div><b>' + esc(title) + '</b><small>' + esc(message) + '</small></div></div>';
  }
  function dashboardHtml(model) {
    const parts = [];
    if (state.widgets.indexOf('kpis') >= 0) parts.push(dashboardKpiHtml(model));
    if (state.widgets.indexOf('trend') >= 0) parts.push(dashboardPanel('Tren Kinerja Keuangan','Nominal per closing tahun berjalan.',financialTrendHtml(model),'prr-span-5'));
    if (state.widgets.indexOf('map') >= 0) parts.push(dashboardPanel('Peta Kinerja Wilayah','Ringkasan area yang sedang dipilih.',pendingPanelHtml('Peta wilayah belum terhubung','Data batas wilayah dan koordinat cabang belum tersedia. Area aktif: ' + model.scope.label,'⌖'),'prr-span-3'));
    if (state.widgets.indexOf('risk_summary') >= 0) parts.push(dashboardPanel('Ringkasan Risiko','Rasio utama dibanding target RBB.',dashboardRiskSummaryHtml(model),'prr-span-4'));
    if (state.widgets.indexOf('rbb') >= 0) parts.push(dashboardPanel('Realisasi vs Target RBB','Realisasi dibanding target RBB bulan berjalan dan Desember.',dashboardRbbHtml(model),'prr-span-8 prr-rbb-main-panel'));
    if (state.widgets.indexOf('alerts') >= 0) parts.push(dashboardPanel('Early Warning Alert','Indikator RBB yang belum mencapai target.',dashboardAlertsHtml(model),'prr-span-4 prr-alert-side-panel'));
    if (state.widgets.indexOf('rbb') >= 0) parts.push(dashboardPanel('Pencapaian Produksi Kredit vs RBB','Transaksi kredit kode 110 · ' + model.scope.label + ' · realisasi sampai ' + formatDate(state.closing) + '.',creditPlanHtml(model),'prr-production-panel'));
    if (state.widgets.indexOf('rbb') >= 0) parts.push(dashboardPanel('Tren Realisasi vs Run Off','Tahun berjalan sampai ' + formatDate(state.closing) + '.',creditTrendHtml(model),'prr-alert-runoff-panel'));
    if (state.widgets.indexOf('ranking') >= 0) {
      const officeHint=model.scope.type==='consolidated'?'Pusat · ringkasan 4 Korwil':(model.scope.type==='korwil'?'Korwil · rincian 7 kantor cabang':'Kantor cabang · rincian Kankas');
      parts.push(dashboardPanel('Kinerja Kantor',officeHint+' · Kredit, Tabungan, dan Deposito dengan perubahan bulanan dan tahunan.',dashboardRankingHtml(model),'prr-span-12'));
    }
    if (state.widgets.indexOf('risk') >= 0 || state.widgets.indexOf('kolektibilitas') >= 0) parts.push(dashboardPanel('NPL, Repayment Rate, dan Kolektibilitas','NPL menggunakan saldo bank; RR menggunakan baki debet.',scopeExtraHtml(model) || '<div class="prr-empty">Pilih NPL/RR atau breakdown kolektibilitas.</div>','prr-span-8'));
    if (state.widgets.indexOf('compliance') >= 0) parts.push(dashboardPanel('Budaya Kepatuhan','Indikator dan tindak lanjut kepatuhan.',pendingPanelHtml('Data kepatuhan belum terhubung','Sumber indikator audit dan status tindak lanjut belum tersedia.','✓'),'prr-span-3'));
    if (state.widgets.indexOf('members') >= 0) parts.push(dashboardPanel('Sumber Daya Manusia','Rekap demografi dan jabatan pada area terpilih.',memberSummaryHtml(model),'prr-span-12'));
    if (state.widgets.indexOf('notary') >= 0) parts.push(dashboardPanel('Dokumen Notaris','SLA dan status penyelesaian dokumen.',pendingPanelHtml('Data notaris belum terhubung','Sumber status dokumen, keterlambatan, dan SLA belum tersedia.','▤'),'prr-span-3'));
    if (state.widgets.indexOf('ratios') >= 0) parts.push(dashboardPanel('Indikator Rasio Keuangan','RBB dan realisasi rasio pada periode terpilih.',ratioHtml(model),'prr-span-12'));
    return '<div class="prr-dashboard-grid">' + (parts.join('') || '<div class="prr-empty">Belum ada panel yang dipilih. Buka Atur paparan.</div>') + '</div>';
  }
  function branchKinerjaHtml(model) {
    const showCards = state.widgets.indexOf('cards') >= 0;
    const showMembers = state.widgets.indexOf('members') >= 0;
    const cards = showCards ? '<div class="prr-card-grid">' + metricDefs.filter(function (def) { return def.key !== 'npl'; }).map(function (def) { return cardHtml(model, def); }).join('') + '</div>' : '';
    const kinerja = cards + scopeExtraHtml(model) + pendingWidgetsHtml();
    const switcher = showMembers ? '<div class="prr-branch-switch" role="tablist" aria-label="Ringkasan cabang"><button type="button" class="active" data-prr-branch-toggle="kinerja">Kinerja</button><button type="button" data-prr-branch-toggle="anggota">Rekap SDM</button></div>' : '';
    const kinerjaView = '<div data-prr-branch-view="kinerja">' + (kinerja || '<div class="prr-empty">Pilih panel kinerja dari Atur tab dan panel paparan.</div>') + '</div>';
    const memberView = showMembers ? '<div data-prr-branch-view="anggota" hidden>' + memberSummaryHtml(model) + '</div>' : '';
    return switcher + kinerjaView + memberView;
  }
  function panelHtml(model, indicatorOnly, active) {
    const scope = indicatorOnly ? (state.scopes.find(function (x) { return x.indicator === true; }) || model.scope) : model.scope;
    const title = indicatorOnly ? 'Indikator Keuangan ' + scope.label : scope.title;
    const subtitle = indicatorOnly ? 'Rasio RBB dibandingkan dengan realisasi pada closing terpilih.' : 'Perbandingan target RBB sistem dengan realisasi closing terpilih.';
    const cards = state.widgets.indexOf('cards') >= 0 ? '<div class="prr-card-grid">' + metricDefs.filter(function (def) { return def.key !== 'npl'; }).map(function (def) { return cardHtml(model, def); }).join('') + '</div>' : '';
    const content = indicatorOnly ? ratioHtml(model) : (model.scope.type === 'branch' ? branchKinerjaHtml(model) : (cards + scopeExtraHtml(model) + pendingWidgetsHtml() || '<div class="prr-empty">Pilih panel dari Atur tab dan panel paparan.</div>'));
    return '<section id="prr-panel-' + esc(scope.id) + '" class="prr-panel' + (active ? ' active' : '') + '" data-prr-panel="' + esc(scope.id) + '" role="tabpanel"><div class="prr-panel-head"><div><h2>' + esc(title) + '</h2><p>' + esc(subtitle) + '</p></div><span class="prr-scope">' + esc(scope.label) + ' &middot; ' + esc(model.actualDate || state.closing) + '</span></div>' + content + '</section>';
    return '<section id="prr-panel-' + esc(scope.id) + '" class="prr-panel' + (active ? ' active' : '') + '" data-prr-panel="' + esc(scope.id) + '" role="tabpanel"><div class="prr-panel-head"><div><h2>' + esc(title) + '</h2><p>' + esc(subtitle) + '</p></div><span class="prr-scope">' + esc(scope.label) + ' · ' + esc(model.actualDate || state.closing) + '</span></div>' + (indicatorOnly ? ratioHtml(model) : '<div class="prr-card-grid">' + metricDefs.filter(function (def) { return def.key !== 'npl'; }).map(function (def) { return cardHtml(model, def); }).join('') + '</div>') + '</section>';
  }
  function selectActive() {
    if (state.selected.indexOf(state.active) >= 0) return;
    const center = state.selected.indexOf('kinerja_pusat') >= 0 ? 'kinerja_pusat' : '';
    state.active = center || state.selected[0] || '';
  }
  function render() {
    selectActive();
    const panels = document.getElementById('prrPanels');
    const scope = state.scopes.find(function (item) { return item.id === state.active; });
    const model = state.models.get(state.active);
    panels.innerHTML = model && !model.error ? dashboardHtml(model) : '<div class="prr-empty">' + esc(model && model.error ? model.error : 'Data belum tersedia untuk wilayah ini.') + '</div>';
    panels.querySelectorAll('[data-prr-member-jobs-toggle]').forEach(function (button) { button.addEventListener('click', function () {
      const section = button.closest('.prr-sdm-job-section');
      if (!section) return;
      const expanded = button.getAttribute('aria-expanded') !== 'true';
      const compact = section.querySelector('[data-prr-job-compact]'), complete = section.querySelector('[data-prr-job-complete]');
      if (compact) compact.hidden = expanded;
      if (complete) complete.hidden = !expanded;
      button.setAttribute('aria-expanded', String(expanded));
      button.setAttribute('aria-label', expanded ? 'Tampilkan AO saja' : 'Buka rekap jabatan lengkap');
      const icon = button.querySelector('[data-prr-job-toggle-icon]'), label = button.querySelector('[data-prr-job-toggle-label]');
      if (icon) icon.textContent = expanded ? '↙' : '↗';
      if (label) label.textContent = expanded ? 'AO saja' : 'Lihat lengkap (' + fmtInt.format(button.dataset.totalJobs) + ')';
    }); });
    panels.querySelectorAll('[data-prr-history]').forEach(function (card) { card.addEventListener('click', function () { openHistory(card); }); card.addEventListener('keydown', function (event) { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openHistory(card); } }); });
    panels.querySelectorAll('[data-prr-chart-toggle]').forEach(function (button) { button.addEventListener('click', function () { const box = button.closest('[data-prr-extra]'); const key = button.dataset.prrChartToggle; if (!box) return; box.querySelectorAll('[data-prr-chart-toggle]').forEach(function (item) { item.classList.toggle('active', item === button); }); box.querySelectorAll('[data-prr-chart-view]').forEach(function (view) { view.hidden = view.dataset.prrChartView !== key; }); }); });
    if (scope) document.getElementById('prrScope').value = scope.id;
  }
  function installModernTooltips() {
    const tooltip = document.createElement('div');
    tooltip.id = 'prrModernTooltip';
    tooltip.className = 'prr-modern-tooltip';
    tooltip.setAttribute('role', 'tooltip');
    tooltip.hidden = true;
    document.body.appendChild(tooltip);
    let pointerTarget = null, focusTarget = null, describedTarget = null;
    const findTarget = function (node) { return node instanceof Element ? node.closest('[data-prr-tooltip]') : null; };
    const position = function (x, y) {
      tooltip.style.left = '0px'; tooltip.style.top = '0px';
      const width = tooltip.offsetWidth, height = tooltip.offsetHeight;
      const left = Math.max(12, Math.min(x + 14, window.innerWidth - width - 12));
      const top = y + height + 16 > window.innerHeight ? Math.max(12, y - height - 14) : y + 14;
      tooltip.style.left = left + 'px'; tooltip.style.top = top + 'px';
    };
    const refresh = function (x, y) {
      const target = pointerTarget || focusTarget;
      if (!target) {
        tooltip.classList.remove('is-visible'); tooltip.hidden = true;
        if (describedTarget) describedTarget.removeAttribute('aria-describedby');
        describedTarget = null;
        return;
      }
      if (describedTarget !== target) {
        if (describedTarget) describedTarget.removeAttribute('aria-describedby');
        describedTarget = target;
        target.setAttribute('aria-describedby', tooltip.id);
        tooltip.textContent = target.getAttribute('data-prr-tooltip') || '';
      }
      tooltip.hidden = false;
      tooltip.classList.add('is-visible');
      if (!Number.isFinite(x) || !Number.isFinite(y)) {
        const rect = target.getBoundingClientRect(); x = rect.left + rect.width / 2; y = rect.top + rect.height / 2;
      }
      position(x, y);
    };
    root.addEventListener('pointerover', function (event) { pointerTarget = findTarget(event.target); refresh(event.clientX, event.clientY); });
    root.addEventListener('pointermove', function (event) { if (pointerTarget) refresh(event.clientX, event.clientY); });
    root.addEventListener('pointerout', function (event) {
      const next = findTarget(event.relatedTarget);
      if (next !== pointerTarget) { pointerTarget = next; refresh(event.clientX, event.clientY); }
    });
    root.addEventListener('focusin', function (event) { focusTarget = findTarget(event.target); refresh(NaN, NaN); });
    root.addEventListener('focusout', function (event) {
      const next = findTarget(event.relatedTarget);
      if (next !== focusTarget) { focusTarget = next; refresh(NaN, NaN); }
    });
  }
  function storedSettings() {
    try {
      const data = JSON.parse(localStorage.getItem(STORE_KEY) || 'null');
      if (Array.isArray(data)) return {tabs:data, widgets:defaultWidgets.slice(), fontScale:1.15};
      const widgets=Array.isArray(data && data.widgets) ? data.widgets.slice() : defaultWidgets.slice();
      if (widgets.indexOf('cards') >= 0) {
        widgets.splice(widgets.indexOf('cards'),1,'kpis');
        ['trend','rbb','risk_summary','alerts','ranking'].forEach(function (key) { if (widgets.indexOf(key) < 0) widgets.push(key); });
      }
      return {tabs:Array.isArray(data && data.tabs) ? data.tabs : defaultTabs.slice(), widgets:widgets, fontScale:normalizeFontScale(data && data.fontScale)};
    } catch (e) {
      return {tabs:defaultTabs.slice(), widgets:defaultWidgets.slice(), fontScale:1.15};
    }
  }
  function applySettings(settings) {
    if (!settings || typeof settings !== 'object') return false;
    const validScopes = new Set(state.scopes.filter(function (scope) { return !scope.indicator; }).map(function (scope) { return scope.id; }));
    const validWidgets = new Set(widgetDefs.map(function (item) { return item.key; }));
    const tabs = Array.isArray(settings.tabs) ? settings.tabs.filter(function (id) { return validScopes.has(id); }) : [];
    const widgets = Array.isArray(settings.widgets) ? settings.widgets.filter(function (key) { return validWidgets.has(key); }) : [];
    if (settings.fontScale !== undefined) applyFontScale(settings.fontScale);
    if (tabs.length) state.selected = Array.from(new Set(tabs));
    if (widgets.length) state.widgets = Array.from(new Set(widgets));
    if (state.selected.indexOf(state.active) < 0) state.active = state.selected.indexOf('kinerja_pusat') >= 0 ? 'kinerja_pusat' : state.selected[0];
    return tabs.length > 0 || widgets.length > 0 || settings.fontScale !== undefined;
  }
  async function loadAccountSettings() {
    try {
      const response = await apiPost(API_SETTINGS, {type:'get'});
      if (response.preferences) {
        const preferences=response.preferences;
        if (preferences.fontScale === undefined) preferences.fontScale=storedSettings().fontScale;
        if (!applySettings(preferences)) return;
        try { localStorage.setItem(STORE_KEY, JSON.stringify(preferences)); } catch (e) {}
        syncScopeSelect();
        renderSettings();
      }
    } catch (error) {
      // Pengaturan lokal tetap menjadi fallback saat layanan akun belum tersedia.
    }
  }
  function renderSettings() {
    const body = document.getElementById('prrSettingsBody');
    const groups = [
      {title:'Pusat', items:state.scopes.filter(function (x) { return x.type === 'consolidated' && !x.indicator; })},
      {title:'Korwil', items:state.scopes.filter(function (x) { return x.type === 'korwil'; })},
      {title:'Kantor Cabang', items:state.scopes.filter(function (x) { return x.type === 'branch'; })}
    ];
    const tabsHtml = groups.map(function (group) { return '<div class="prr-setting-group"><h3>' + esc(group.title) + '</h3>' + group.items.map(function (item) { return '<label class="prr-check"><input type="checkbox" data-prr-setting-tab value="' + esc(item.id) + '"' + (state.selected.indexOf(item.id) >= 0 ? ' checked' : '') + '><span>' + esc(item.label) + '</span></label>'; }).join('') + '</div>'; }).join('');
    const widgetGroups = ['Kinerja Keuangan','Risiko Kredit','Panel Direksi'].map(function (group) {
      const items = widgetDefs.filter(function (item) { return item.group === group; });
      return '<div class="prr-setting-group"><h3>' + esc(group) + '</h3>' + items.map(function (item) {
        return '<label class="prr-widget-option"><input type="checkbox" data-prr-setting-widget value="' + esc(item.key) + '"' + (state.widgets.indexOf(item.key) >= 0 ? ' checked' : '') + '><span>' + esc(item.label) + '<small>' + esc(item.desc) + (item.pending ? ' Data belum terhubung.' : '') + '</small></span></label>';
      }).join('') + '</div>';
    }).join('');
    body.innerHTML = '<section class="prr-settings-section"><div class="prr-settings-section-head"><div><h3>Ukuran teks dashboard</h3><p>Atur pembesaran seluruh tampilan agar angka nyaman dibaca di layar Direksi.</p></div><b id="prrFontScaleValue" class="prr-font-scale-value">'+Math.round(state.fontScale*100)+'%</b></div><input id="prrFontScale" class="prr-font-scale-slider" type="range" min="85" max="140" step="5" value="'+Math.round(state.fontScale*100)+'" aria-label="Ukuran teks dashboard"></section><section class="prr-settings-section"><div class="prr-settings-section-head"><div><h3>Wilayah yang tersedia</h3><p>Centang Pusat, Korwil, atau Cabang agar muncul pada pilihan wilayah dashboard.</p></div></div><div class="prr-settings-grid">' + tabsHtml + '</div></section><section class="prr-settings-section"><div class="prr-settings-section-head"><div><h3>Panel dashboard</h3><p>Pilih komponen yang ingin ditampilkan pada dashboard Direksi.</p></div></div><div class="prr-settings-grid">' + widgetGroups + '</div></section>';
    const slider=document.getElementById('prrFontScale');
    if (slider) slider.addEventListener('input',function(){applyFontScale(Number(this.value)/100);});
  }
  function collectSettings() {
    return {
      tabs:Array.from(document.querySelectorAll('#prrSettingsBody [data-prr-setting-tab]:checked')).map(function (input) { return input.value; }),
      widgets:Array.from(document.querySelectorAll('#prrSettingsBody [data-prr-setting-widget]:checked')).map(function (input) { return input.value; }),
      fontScale:state.fontScale
    };
  }
  function syncScopeSelect() {
    const select = document.getElementById('prrScope');
    const scopes = state.scopes.filter(function (scope) { return !scope.indicator && state.selected.indexOf(scope.id) >= 0; });
    if (state.selected.indexOf(state.active) < 0) state.active = scopes.find(function (scope) { return scope.id === 'kinerja_pusat'; })?.id || (scopes[0] && scopes[0].id) || '';
    select.innerHTML = scopes.map(function (scope) { return '<option value="' + esc(scope.id) + '">' + esc(scope.label) + '</option>'; }).join('');
    select.value = state.active;
    select.disabled = scopes.length <= 1;
  }
  function openModal(id) { const node = document.getElementById(id); if (node) { node.hidden = false; document.body.style.overflow = 'hidden'; } }
  function closeModal(id) { const node = document.getElementById(id); if (node) { node.hidden = true; document.body.style.overflow = ''; } }
  function formatHistoryValue(value, meta) { const n = num(value) || 0; if (meta && (meta.metric === 'npl' || String(meta.unit || '').toLowerCase().indexOf('persen') >= 0)) return displayRatio(n); return displayNominal(n); }
  function formatDate(value) { if (!value) return '-'; const d = new Date(String(value).slice(0,10) + 'T00:00:00'); return Number.isNaN(d.getTime()) ? value : new Intl.DateTimeFormat('id-ID', {day:'2-digit',month:'short',year:'numeric'}).format(d); }
  function historyChart(rows, meta) {
    const width=760,height=300,pad={top:20,right:20,bottom:45,left:65}, values=rows.map(function (x) { return num(x.nilai) || 0; }); let min=Math.min.apply(null,values),max=Math.max.apply(null,values); if (min === max) { const off=Math.abs(max || 1)*.08; min-=off; max+=off; } else { const off=(max-min)*.12; min-=off; max+=off; } const plotW=width-pad.left-pad.right,plotH=height-pad.top-pad.bottom,x=function (i) { return rows.length===1 ? pad.left+plotW/2 : pad.left+plotW*i/(rows.length-1); },y=function (v) { return pad.top+(max-v)/(max-min)*plotH; }; const points=rows.map(function (r,i) { return x(i).toFixed(2)+','+y(num(r.nilai)||0).toFixed(2); }).join(' '); const grid=[0,1,2,3,4].map(function (i) { const yy=pad.top+plotH*i/4, val=max-(max-min)*i/4; return '<line class="prr-grid" x1="'+pad.left+'" y1="'+yy+'" x2="'+(width-pad.right)+'" y2="'+yy+'"></line><text class="prr-axis" x="'+(pad.left-8)+'" y="'+(yy+4)+'" text-anchor="end">'+esc(formatHistoryValue(val,meta))+'</text>'; }).join(''); const labels=rows.map(function (r,i) { return (rows.length<=8 || i===0 || i===rows.length-1 || i%Math.ceil(rows.length/6)===0) ? '<text class="prr-axis" x="'+x(i)+'" y="'+(height-16)+'" text-anchor="middle">'+esc(r.label || formatDate(r.tanggal))+'</text>' : ''; }).join(''); const dots=rows.map(function (r,i) { return '<circle class="prr-dot" cx="'+x(i)+'" cy="'+y(num(r.nilai)||0)+'" r="4"><title>'+esc(formatDate(r.tanggal))+': '+esc(formatHistoryValue(r.nilai,meta))+'</title></circle>'; }).join(''); return '<svg viewBox="0 0 '+width+' '+height+'" role="img" aria-label="Grafik history realisasi"><g>'+grid+'</g><polygon class="prr-area" points="'+pad.left+','+(height-pad.bottom)+' '+points+' '+x(rows.length-1)+','+(height-pad.bottom)+'"></polygon><polyline class="prr-line" points="'+points+'"></polyline><g>'+dots+'</g><g>'+labels+'</g></svg>';
  }
  async function openHistory(card) {
    const cardLabel=card.querySelector('h3')?.textContent || card.querySelector('.prr-kpi-top small')?.textContent || '';
    const modal=document.getElementById('prrHistoryModal'),body=document.getElementById('prrHistoryBody'),title=document.getElementById('prrHistoryTitle'),subtitle=document.getElementById('prrHistorySubtitle'),requestId=++state.historyRequest; openModal('prrHistoryModal'); title.textContent='History Realisasi '+cardLabel; subtitle.textContent='Mengambil snapshot closing dari acc_history...'; body.innerHTML='<div class="prr-loading"><span class="prr-spinner"></span><span>Memuat history...</span></div>'; const metric=card.dataset.prrHistory || 'aset',scopeType=card.dataset.prrScopeType || 'consolidated',scopeValue=card.dataset.prrScopeValue || '000',year=Number(card.dataset.prrYear || String(state.closing).slice(0,4)); const request={type:'realisasi_history',metric:metric,year:year,kode_kantor:'000'}; if (scopeType==='korwil') request.korwil=scopeValue; if (scopeType==='branch') request.kode_kantor=scopeValue;
    try { const data=await apiPost(API_RBB,request); if (requestId !== state.historyRequest) return; const meta=data.meta||{}, rows=Array.isArray(data.history)?data.history:[]; title.textContent=(meta.label || 'History Realisasi')+' · '+(meta.scope || ''); subtitle.textContent='Tahun '+(meta.year || year)+' · '+(meta.unit || 'Snapshot closing per bulan'); if (!rows.length) { body.innerHTML='<div class="prr-empty">Belum ada snapshot closing untuk pilihan ini.</div>'; return; } const latest=rows[rows.length-1],previous=rows.length>1?rows[rows.length-2]:null,growth=previous && num(previous.nilai)!==0 ? (num(latest.nilai)-num(previous.nilai))/Math.abs(num(previous.nilai))*100 : null; const totals=meta.totals && ['pendapatan','biaya','laba'].indexOf(meta.metric)>=0 ? '<div class="prr-history-meta"><div class="prr-stat"><span>Total tahun berjalan</span><b>'+esc(formatHistoryValue(meta.totals[meta.metric],meta))+'</b><small>Akumulasi tahun '+esc(meta.year || year)+'</small></div></div>' : ''; body.innerHTML='<div class="prr-history-meta"><div class="prr-stat"><span>'+ (meta.metric==='npl'?'NPL terbaru':'Closing terbaru') +'</span><b>'+esc(formatHistoryValue(latest.nilai,meta))+'</b><small>'+esc(formatDate(latest.tanggal))+'</small></div><div class="prr-stat"><span>Perubahan vs sebelumnya</span><b>'+esc(growth===null?'-':(growth>=0?'+':'')+fmt.format(growth)+'%')+'</b><small>'+esc(previous?formatDate(previous.tanggal):'Belum ada pembanding')+'</small></div><div class="prr-stat"><span>Jumlah snapshot</span><b>'+rows.length+' closing</b><small>Data tahun '+esc(meta.year || year)+'</small></div></div>'+totals+'<p class="prr-formula"><b>Rumus:</b> '+esc(meta.formula || '-')+' · '+esc(meta.scope || '')+(meta.calculation?' · '+esc(meta.calculation):'')+'</p><div class="prr-chart">'+historyChart(rows,meta)+'</div><div class="prr-values">'+rows.map(function (row) { return '<div class="prr-value"><span>'+esc(row.label || formatDate(row.tanggal))+'</span><b>'+esc(formatHistoryValue(row.nilai,meta))+'</b></div>'; }).join('')+'</div>'; } catch (error) { if (requestId===state.historyRequest) body.innerHTML='<div class="prr-empty">'+esc(error.message || 'History gagal dimuat.')+'</div>'; }
  }
  async function loadOffices() {
    const center={id:'kinerja_pusat',type:'consolidated',value:'000',label:'Kinerja Pusat',title:'Kinerja Pusat'};
    const indicator={id:'indikator_keuangan_pusat',type:'consolidated',value:'000',label:'Indikator Keuangan',title:'Indikator Keuangan',indicator:true};
    let offices=[];
    try { offices=await apiPost(API_KODE,{type:'kode_kantor'}) || []; } catch (e) {}
    const branches=offices.filter(function (x) { return x.kode_kantor && String(x.kode_kantor)!=='000'; }).map(function (x) { const code=String(x.kode_kantor).padStart(3,'0'); return {id:'cabang_'+code,type:'branch',value:code,label:'Kinerja Kantor '+code+' · '+(x.nama_kantor || ''),title:'Kinerja Kantor '+code+' · '+(x.nama_kantor || '')}; });
    state.scopes=[center,indicator].concat(korwils).concat(branches);
    const saved=storedSettings(); applyFontScale(saved.fontScale); state.selected=saved.tabs.filter(function (id) { return state.scopes.some(function (x) { return x.id===id && !x.indicator; }); }); if (!state.selected.length) state.selected=[center.id]; state.active=state.selected.indexOf('kinerja_pusat')>=0?'kinerja_pusat':state.selected[0]; state.widgets=widgetDefs.map(function (item) { return item.key; }).filter(function (key) { return saved.widgets.indexOf(key) >= 0; }); if (!state.widgets.length) state.widgets=defaultWidgets.slice();
    syncScopeSelect();
    renderSettings();
  }
  async function load() {
    const requestId=++state.loadRequest;
    const status=document.getElementById('prrStatus'),panels=document.getElementById('prrPanels');
    state.closing=document.getElementById('prrClosing').value;
    state.active=document.getElementById('prrScope').value || state.active;
    const scope=state.scopes.find(function (item) { return item.id===state.active && !item.indicator; });
    if (!state.closing || !scope) return;
    status.classList.remove('error'); status.textContent='Memuat dashboard...';
    panels.innerHTML='<div class="prr-loading"><span class="prr-spinner"></span><span>Memuat data ' + esc(scope.label) + '...</span></div>';
    document.getElementById('prrPosition').textContent='Closing: '+state.closing;
    state.models=new Map();
    try {
      const model=await loadModel(scope);
      if (requestId !== state.loadRequest) return;
      state.models.set(scope.id,model);
      state.actualDate=model.actualDate || state.closing;
      render();
      document.getElementById('prrLoadedAt').textContent='Snapshot actual: '+state.actualDate;
      const enriching=model.historyLoading || model.officePerformanceLoading || model.creditComparisonLoading || model.creditPlanLoading || model.creditTrendLoading;
      status.textContent=enriching ? scope.label+' · data utama siap, melengkapi panel...' : scope.label+' · dashboard siap';
      if (enriching) {
        loadModelEnrichment(scope,model,requestId).then(function () {
          if (requestId === state.loadRequest) status.textContent=scope.label+' · dashboard siap';
        }).catch(function () {
          if (requestId === state.loadRequest) status.textContent=scope.label+' · data utama siap';
        });
      }
    } catch (error) {
      if (requestId !== state.loadRequest) return;
      state.models.set(scope.id,{scope:scope,error:error.message || 'Data gagal dimuat'});
      render();
      status.classList.add('error');
      status.textContent='Data gagal dimuat';
    }
  }
  async function initDates() { try { const response=await (window.apiFetch ? window.apiFetch(API_DATE,{cache:'no-store'}) : fetch(API_DATE,{cache:'no-store'})); const json=await response.json(); const data=json.data||{}; document.getElementById('prrClosing').value=data.last_closing || data.last_created || new Date().toISOString().slice(0,10); } catch (e) { document.getElementById('prrClosing').value=new Date().toISOString().slice(0,10); } }
  document.getElementById('prrSettingsOpen').addEventListener('click',function () { renderSettings(); openModal('prrSettingsModal'); });
  document.getElementById('prrClosing').addEventListener('change',load);
  document.getElementById('prrScope').addEventListener('change',function () { state.active=this.value; load(); });
  document.querySelectorAll('[data-prr-close]').forEach(function (button) { button.addEventListener('click',function () { closeModal(button.dataset.prrClose==='history'?'prrHistoryModal':'prrSettingsModal'); }); });
  document.querySelectorAll('.prr-modal').forEach(function (modal) { modal.addEventListener('click',function (event) { if (event.target===modal) closeModal(modal.id); }); });
  document.getElementById('prrSettingsAll').addEventListener('click',function () { document.querySelectorAll('#prrSettingsBody input').forEach(function (input) { input.checked=true; }); });
  document.getElementById('prrSettingsReset').addEventListener('click',function () {
    document.querySelectorAll('#prrSettingsBody [data-prr-setting-tab]').forEach(function (input) { input.checked=defaultTabs.indexOf(input.value)>=0; });
    document.querySelectorAll('#prrSettingsBody [data-prr-setting-widget]').forEach(function (input) { input.checked=defaultWidgets.indexOf(input.value)>=0; });
    applyFontScale(1.15);
  });
  document.getElementById('prrSettingsSave').addEventListener('click',async function () {
    const selected=collectSettings();
    if (!selected.tabs.length) { alert('Pilih minimal satu wilayah dashboard.'); return; }
    if (!selected.widgets.length) { alert('Pilih minimal satu panel paparan.'); return; }
    state.selected=selected.tabs;
    state.widgets=selected.widgets;
    applyFontScale(selected.fontScale);
    if (state.selected.indexOf(state.active)<0) state.active=state.selected.indexOf('kinerja_pusat')>=0?'kinerja_pusat':state.selected[0];
    syncScopeSelect();
    try { localStorage.setItem(STORE_KEY,JSON.stringify(selected)); } catch (e) {}
    closeModal('prrSettingsModal');
    const button=this;
    let savedToAccount=false;
    button.disabled=true;
    try {
      await apiPost(API_SETTINGS,{type:'save',preferences:selected});
      savedToAccount=true;
    } catch (error) {
      // Tetap gunakan salinan lokal dan beri status setelah dashboard selesai dimuat.
    } finally {
      button.disabled=false;
      await load();
      const status=document.getElementById('prrStatus');
      status.classList.toggle('error',!savedToAccount);
      status.textContent=savedToAccount?'Pengaturan tersimpan pada akun Anda':'Layanan akun belum tersedia; pilihan tersimpan di browser ini';
    }
  });
  document.addEventListener('keydown',function (event) { if (event.key==='Escape') { closeModal('prrSettingsModal'); closeModal('prrHistoryModal'); } });
  window.setInterval(function () {
    if (document.hidden) return;
    state.comparisonMode = state.comparisonMode === 'month' ? 'year' : 'month';
    root.dataset.prrComparison = state.comparisonMode;
  },10000);
  installModernTooltips();
  (async function () { await initDates(); await loadOffices(); await loadAccountSettings(); await load(); })();
})();
</script>

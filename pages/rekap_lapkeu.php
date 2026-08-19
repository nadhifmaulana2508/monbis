<?php
// Rekap Keuangan per kantor - responsive, sticky/freeze, insight kinerja cabang.
?>

<div id="rlPage" class="rl-page">
  <section class="rl-header">
    <div class="rl-brand">
      <div class="rl-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 21h18"/><path d="M7 21V9"/><path d="M12 21V5"/><path d="M17 21v-7"/>
        </svg>
      </div>
      <div class="rl-copy">
        <div class="rl-title-row">
          <h1>Rekap Keuangan</h1>
          <button type="button" class="rl-info-btn" onclick="toggleRlInfo(true)" title="Informasi rekap" aria-label="Informasi rekap">i</button>
        </div>
        <p>Ringkasan posisi keuangan dan laba rugi per kantor.</p>
      </div>
      <button type="button" id="rlFilterToggle" class="rl-filter-toggle" onclick="toggleRlFilter()" aria-expanded="false" aria-controls="rlFilter">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
        <span>Filter</span>
      </button>
    </div>

    <form id="rlFilter" class="rl-filter" onsubmit="event.preventDefault(); fetchRekapLapkeu();">
      <label class="rl-field rl-date-field">
        <span>Actual</span>
        <input id="rlTanggal" type="date" onclick="try{this.showPicker()}catch(e){}">
      </label>
      <label class="rl-field rl-office-field">
        <span>Area / Cabang</span>
        <select id="rlKantor"></select>
      </label>
      <button type="button" class="rl-export" onclick="exportRekapLapkeu()" title="Download Excel" aria-label="Download Excel">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
      </button>
    </form>
  </section>

  <section class="rl-table-card">
    <div id="rlLoader" class="rl-loader rl-hidden">
      <div class="rl-spinner"></div>
      <span>Memuat rekap...</span>
    </div>

    <div class="rl-table-toolbar">
      <div>
        <h2>Rekap Per Kantor</h2>
        <p id="rlMeta">-</p>
      </div>
      <label class="rl-search" aria-label="Cari kantor">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input id="rlSearch" type="search" placeholder="Cari kode / kantor..." autocomplete="off">
      </label>
    </div>

    <div class="rl-table-shell">
      <table id="rlTable">
        <colgroup>
          <col class="rl-col-code">
          <col class="rl-col-office">
          <col class="rl-col-money"><col class="rl-col-money"><col class="rl-col-money">
          <col class="rl-col-money"><col class="rl-col-money"><col class="rl-col-money">
        </colgroup>
        <thead>
          <tr>
            <th class="rl-sticky-code">Kode</th>
            <th class="rl-sticky-office">Kantor</th>
            <th>Aset</th>
            <th>Liabilitas</th>
            <th>Ekuitas</th>
            <th>Pendapatan</th>
            <th>Beban</th>
            <th>Laba Kotor</th>
          </tr>
        </thead>
        <tbody id="rlBody">
          <tr><td colspan="8" class="rl-empty">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </section>
</div>

<div id="rlInfoModal" class="rl-modal rl-hidden" role="dialog" aria-modal="true" aria-labelledby="rlInfoTitle">
  <div class="rl-modal-card">
    <div class="rl-modal-head">
      <div>
        <div class="rl-modal-kicker">Panduan Singkat</div>
        <h3 id="rlInfoTitle">Membaca Rekap Lapkeu</h3>
      </div>
      <button type="button" onclick="toggleRlInfo(false)" aria-label="Tutup">&times;</button>
    </div>
    <div class="rl-modal-body">
      <div id="rlInfoSummary" class="rl-info-summary"></div>
      <div id="rlInfoCondition" class="rl-info-condition">
        Rekap ini membantu membandingkan posisi keuangan antar kantor pada tanggal actual yang dipilih.
      </div>
      <div class="rl-info-performance-grid">
        <section class="rl-info-panel rl-info-panel-priority">
          <div class="rl-info-panel-head">
            <div>
              <span class="rl-info-eyebrow">Prioritas</span>
              <h4>Cabang Perlu Perhatian</h4>
            </div>
            <span id="rlInfoPriorityCount" class="rl-info-count">0</span>
          </div>
          <div id="rlInfoPriorityList" class="rl-info-list"></div>
        </section>

        <section class="rl-info-panel">
          <div class="rl-info-panel-head">
            <div>
              <span class="rl-info-eyebrow">Perbandingan</span>
              <h4>Laba Kotor Terendah</h4>
            </div>
          </div>
          <div id="rlInfoLowestProfit" class="rl-info-list"></div>
        </section>

        <section class="rl-info-panel">
          <div class="rl-info-panel-head">
            <div>
              <span class="rl-info-eyebrow">Beban</span>
              <h4>Beban Nominal Terbesar</h4>
            </div>
          </div>
          <div id="rlInfoTopExpense" class="rl-info-list"></div>
        </section>
      </div>

      <div id="rlInfoActions" class="rl-info-actions"></div>

      <div class="rl-info-grid">
        <div><b>Aset</b><span>Saldo aset gabungan. Pada konsolidasi, total aset mengikuti eliminasi yang diterapkan pada API laporan.</span></div>
        <div><b>Liabilitas &amp; Ekuitas</b><span>Menunjukkan sumber pendanaan dan modal pada kantor atau area yang dipilih.</span></div>
        <div><b>Pendapatan &amp; Beban</b><span>Akumulasi laba rugi berjalan berdasarkan posisi acc_history actual.</span></div>
        <div><b>Laba Kotor</b><span>Pendapatan dikurangi beban. Nilai negatif ditandai merah agar kantor yang perlu perhatian cepat terlihat.</span></div>
      </div>
      <div class="rl-info-note">Daftar prioritas dihitung dari data yang sedang tampil: laba negatif, beban melebihi pendapatan, atau ekuitas negatif. Ranking laba terendah bersifat perbandingan, bukan pengganti target internal/RKAP.</div>
    </div>
  </div>
</div>

<style>
  /* ==========================================================
     REKAP KEUANGAN — RESPONSIVE & PAGE-SCOPED
     Visual mengikuti template monitoring Monbis:
     header compact, filter seragam, summary ringkas,
     sticky table/total, mobile hemat ruang, info bottom-sheet.
     ========================================================== */
  #rlPage,
  #rlInfoModal {
    --rl-primary:#2563eb;
    --rl-success:#059669;
    --rl-danger:#dc2626;
    --rl-warning:#d97706;
    --rl-text:#172033;
    --rl-muted:#64748b;
    --rl-line:#dbe4f0;
    --rl-soft:#f8fafc;
    --rl-head:#eef5fc;
    font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    color:var(--rl-text);
  }

  #rlPage *,
  #rlInfoModal *,
  #rlPage *::before,
  #rlPage *::after,
  #rlInfoModal *::before,
  #rlInfoModal *::after { box-sizing:border-box; }

  /* ---------- Page ---------- */
  #rlPage.rl-page {
    width:100%;
    height:calc(100dvh - 60px);
    min-height:0;
    display:flex;
    flex-direction:column;
    gap:7px;
    padding:7px 10px 10px;
    overflow:hidden;
    background:#f8fafc;
  }

  /* ---------- Header ---------- */
  #rlPage .rl-header {
    flex:0 0 auto;
    display:grid;
    grid-template-columns:minmax(260px,1fr) auto;
    align-items:center;
    gap:14px;
    min-width:0;
    padding:9px 11px;
    border:1px solid var(--rl-line);
    border-radius:11px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.04),0 8px 24px rgba(15,23,42,.025);
  }

  #rlPage .rl-brand {
    display:flex;
    align-items:center;
    min-width:0;
    gap:9px;
  }

  #rlPage .rl-icon {
    width:37px;
    height:37px;
    flex:0 0 37px;
    display:grid;
    place-items:center;
    border-radius:9px;
    background:var(--rl-primary);
    color:#fff;
    box-shadow:0 3px 8px rgba(37,99,235,.18);
  }
  #rlPage .rl-icon svg { width:18px; height:18px; }

  #rlPage .rl-copy { min-width:0; flex:1; }
  #rlPage .rl-title-row {
    display:flex;
    align-items:center;
    min-width:0;
    gap:7px;
  }
  #rlPage .rl-title-row h1 {
    min-width:0;
    margin:0;
    color:#172033;
    font-size:16px;
    line-height:1.08;
    font-weight:900;
    letter-spacing:-.018em;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  #rlPage .rl-copy p {
    max-width:500px;
    margin:4px 0 0;
    color:var(--rl-muted);
    font-size:9px;
    line-height:1.2;
    font-weight:650;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  #rlPage .rl-info-btn {
    width:20px;
    height:20px;
    flex:0 0 20px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:0;
    border:1px solid #bfdbfe;
    border-radius:999px;
    background:#eff6ff;
    color:#2563eb;
    font-size:11px;
    line-height:1;
    font-weight:900;
    cursor:pointer;
    transition:.15s ease;
  }
  #rlPage .rl-info-btn:hover {
    border-color:#93c5fd;
    background:#dbeafe;
    transform:translateY(-1px);
  }

  #rlPage .rl-filter-toggle {
    display:none;
    height:30px;
    margin-left:auto;
    padding:0 9px;
    border:1px solid #dbe4f0;
    border-radius:8px;
    background:#fff;
    color:#475569;
    font-size:9px;
    font-weight:850;
    align-items:center;
    justify-content:center;
    gap:5px;
    cursor:pointer;
  }
  #rlPage .rl-filter-toggle svg { width:13px; height:13px; }
  #rlPage .rl-filter-toggle[aria-expanded="true"] {
    color:#1d4ed8;
    border-color:#bfdbfe;
    background:#eff6ff;
  }

  #rlPage .rl-filter {
    display:grid;
    grid-template-columns:122px minmax(190px,270px) 36px;
    align-items:end;
    gap:7px;
    min-width:0;
  }
  #rlPage .rl-field {
    display:flex;
    flex-direction:column;
    min-width:0;
    gap:3px;
  }
  #rlPage .rl-field span {
    margin-left:1px;
    color:#475569;
    font-size:8px;
    line-height:1;
    font-weight:850;
    letter-spacing:.05em;
    text-transform:uppercase;
    white-space:nowrap;
  }
  #rlPage .rl-field input,
  #rlPage .rl-field select {
    width:100%;
    min-width:0;
    height:35px;
    padding:0 9px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    outline:none;
    background:#fff;
    color:#334155;
    font-size:10.5px;
    font-weight:750;
    transition:border-color .15s,box-shadow .15s;
  }
  #rlPage .rl-field select {
    appearance:none;
    -webkit-appearance:none;
    padding-right:28px;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 8px center;
    background-size:12px 12px;
  }
  #rlPage .rl-field input:focus,
  #rlPage .rl-field select:focus {
    border-color:var(--rl-primary);
    box-shadow:0 0 0 3px rgba(37,99,235,.10);
  }
  #rlPage .rl-field select:disabled {
    color:#64748b;
    background:#f8fafc;
    cursor:not-allowed;
  }

  #rlPage .rl-export {
    width:36px;
    height:35px;
    display:grid;
    place-items:center;
    padding:0;
    border:0;
    border-radius:8px;
    background:var(--rl-success);
    color:#fff;
    cursor:pointer;
    transition:.15s ease;
  }
  #rlPage .rl-export:hover { background:#047857; transform:translateY(-1px); }
  #rlPage .rl-export svg { width:17px; height:17px; }

  /* ---------- Table ---------- */
  #rlPage .rl-table-card {
    position:relative;
    flex:1 1 auto;
    min-height:0;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    border:1px solid var(--rl-line);
    border-radius:11px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.035);
  }

  #rlPage .rl-table-toolbar {
    flex:0 0 auto;
    min-height:46px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding:7px 9px;
    border-bottom:1px solid #e2e8f0;
    background:linear-gradient(180deg,#fff,#fbfdff);
  }
  #rlPage .rl-table-toolbar > div:first-child { min-width:0; }
  #rlPage .rl-table-toolbar h2 {
    margin:0;
    color:#0f172a;
    font-size:12px;
    line-height:1.15;
    font-weight:900;
  }
  #rlPage .rl-table-toolbar p {
    max-width:520px;
    margin:3px 0 0;
    overflow:hidden;
    color:#94a3b8;
    font-size:8px;
    line-height:1.15;
    font-weight:700;
    text-overflow:ellipsis;
    white-space:nowrap;
  }

  #rlPage .rl-search {
    position:relative;
    width:min(250px,31vw);
    height:31px;
    flex:0 0 auto;
    display:flex;
    align-items:center;
    border:1px solid #dbe3ee;
    border-radius:8px;
    background:#fff;
  }
  #rlPage .rl-search svg {
    position:absolute;
    left:8px;
    top:50%;
    width:13px;
    height:13px;
    color:#94a3b8;
    pointer-events:none;
    transform:translateY(-50%);
  }
  #rlPage .rl-search input {
    width:100%;
    height:100%;
    padding:0 8px 0 27px;
    border:0;
    outline:0;
    background:transparent;
    color:#334155;
    font-size:9px;
    font-weight:700;
  }
  #rlPage .rl-search:focus-within {
    border-color:#60a5fa;
    box-shadow:0 0 0 3px rgba(59,130,246,.09);
  }

  #rlPage .rl-table-shell {
    --rl-head-h:37px;
    position:relative;
    flex:1 1 0;
    width:100%;
    min-width:0;
    min-height:0;
    overflow:auto;
    overflow-x:auto;
    overflow-y:auto;
    scrollbar-gutter:stable;
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
    background:#fff;
  }
  #rlPage .rl-table-shell::-webkit-scrollbar { width:7px; height:7px; }
  #rlPage .rl-table-shell::-webkit-scrollbar-track { background:#f8fafc; }
  #rlPage .rl-table-shell::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }

  #rlPage #rlTable {
    position:relative;
    width:100%;
    min-width:980px;
    border-collapse:separate;
    border-spacing:0;
    table-layout:fixed;
    color:#334155;
    font-size:10px;
    font-variant-numeric:tabular-nums;
  }
  #rlPage .rl-col-code { width:62px; }
  #rlPage .rl-col-office { width:185px; }
  #rlPage .rl-col-money { width:122px; }

  #rlPage #rlTable th,
  #rlPage #rlTable td {
    height:37px;
    padding:5px 7px;
    border-right:1px solid #edf2f7;
    border-bottom:1px solid #edf2f7;
    vertical-align:middle;
  }

  #rlPage #rlTable thead th {
    position:-webkit-sticky !important;
    position:sticky !important;
    top:0 !important;
    z-index:60;
    background:#eef5fc;
    color:#334155;
    font-size:8px;
    line-height:1.1;
    font-weight:900;
    letter-spacing:.045em;
    text-align:right;
    text-transform:uppercase;
    white-space:nowrap;
    box-shadow:inset 0 -1px 0 #cbd5e1;
  }

  #rlPage #rlTable thead th.rl-sticky-code,
  #rlPage #rlTable thead th.rl-sticky-office {
    z-index:100 !important;
    text-align:left;
    background:#e6f1fb !important;
    background-clip:padding-box;
  }

  #rlPage #rlTable td {
    background:#fff;
    color:#334155;
    text-align:right;
    font-weight:750;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  #rlPage #rlTable .rl-money {
    font-family:"JetBrains Mono",ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
    font-size:9px;
    letter-spacing:-.025em;
    color:#0f172a;
  }

  #rlPage #rlTable tbody tr:nth-child(even) td { background:#fcfdff; }
  #rlPage #rlTable tbody tr:hover td { background:#f4f8fd !important; }

  #rlPage .rl-sticky-code {
    position:-webkit-sticky !important;
    position:sticky !important;
    left:0 !important;
  }
  #rlPage .rl-sticky-office {
    position:-webkit-sticky !important;
    position:sticky !important;
    left:62px !important;
    box-shadow:8px 0 12px -12px rgba(15,23,42,.85);
  }
  #rlPage #rlTable tbody td.rl-sticky-code,
  #rlPage #rlTable tbody td.rl-sticky-office {
    z-index:30;
    text-align:left;
    background:#fff;
    background-clip:padding-box;
  }
  #rlPage #rlTable tbody tr:nth-child(even) td.rl-sticky-code,
  #rlPage #rlTable tbody tr:nth-child(even) td.rl-sticky-office { background:#fcfdff; }

  #rlPage .rl-office-name {
    display:block;
    overflow:hidden;
    color:#172033;
    font-size:10px;
    line-height:1.15;
    font-weight:850;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  #rlPage .rl-office-code {
    display:block;
    margin-top:2px;
    color:#94a3b8;
    font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
    font-size:7px;
    line-height:1;
    font-weight:750;
  }

  #rlPage #rlTable tbody .rl-total-row td {
    position:-webkit-sticky !important;
    position:sticky !important;
    top:var(--rl-head-h) !important;
    z-index:70;
    height:39px !important;
    background:#eaf3ff !important;
    color:#15366b !important;
    font-weight:900 !important;
    border-bottom-color:#bfdbfe !important;
    box-shadow:0 5px 10px -12px rgba(15,23,42,.85);
  }
  #rlPage #rlTable tbody .rl-total-row td.rl-sticky-code,
  #rlPage #rlTable tbody .rl-total-row td.rl-sticky-office {
    z-index:110 !important;
    background:#eaf3ff !important;
    background-clip:padding-box;
  }

  #rlPage .rl-pos { color:#047857 !important; }
  #rlPage .rl-neg { color:#dc2626 !important; }
  #rlPage .rl-empty {
    height:160px !important;
    padding:24px !important;
    color:#94a3b8 !important;
    text-align:center !important;
    font-size:10px !important;
    font-weight:800 !important;
  }

  #rlPage .rl-loader {
    position:absolute;
    inset:0;
    z-index:90;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:9px;
    background:rgba(255,255,255,.82);
    color:#2563eb;
    backdrop-filter:blur(3px);
    font-size:10px;
    font-weight:850;
    letter-spacing:.06em;
    text-transform:uppercase;
  }
  #rlPage .rl-loader.rl-hidden { display:none; }
  #rlPage .rl-spinner {
    width:28px;
    height:28px;
    border:4px solid #dbeafe;
    border-top-color:#2563eb;
    border-radius:999px;
    animation:rlSpin .75s linear infinite;
  }
  @keyframes rlSpin { to { transform:rotate(360deg); } }

  /* ---------- Info Modal ---------- */
  #rlInfoModal.rl-modal {
    position:fixed;
    inset:0;
    z-index:10000;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:14px;
    background:rgba(15,23,42,.60);
    backdrop-filter:blur(5px);
  }
  #rlInfoModal.rl-hidden { display:none !important; }

  #rlInfoModal .rl-modal-card {
    width:min(820px,calc(100vw - 28px));
    max-height:min(88dvh,720px);
    display:flex;
    flex-direction:column;
    overflow:hidden;
    border:1px solid #dbe4f0;
    border-radius:16px;
    background:#fff;
    box-shadow:0 28px 80px rgba(15,23,42,.34);
  }

  #rlInfoModal .rl-modal-head {
    flex:0 0 auto;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:10px;
    padding:12px 13px;
    border-bottom:1px solid #e2e8f0;
    background:linear-gradient(180deg,#fff,#f8fafc);
  }
  #rlInfoModal .rl-modal-kicker {
    color:#2563eb;
    font-size:7.5px;
    line-height:1;
    font-weight:900;
    letter-spacing:.09em;
    text-transform:uppercase;
  }
  #rlInfoModal .rl-modal-head h3 {
    margin:4px 0 0;
    color:#0f172a;
    font-size:14px;
    line-height:1.15;
    font-weight:900;
  }
  #rlInfoModal .rl-modal-head button {
    width:32px;
    height:32px;
    flex:0 0 32px;
    display:grid;
    place-items:center;
    border:1px solid #e2e8f0;
    border-radius:8px;
    background:#fff;
    color:#64748b;
    font-size:20px;
    line-height:1;
    cursor:pointer;
  }
  #rlInfoModal .rl-modal-head button:hover {
    color:#e11d48;
    border-color:#fecdd3;
    background:#fff1f2;
  }

  #rlInfoModal .rl-modal-body {
    flex:1 1 auto;
    min-height:0;
    overflow:auto;
    padding:11px 12px 13px;
    color:#334155;
  }

  #rlInfoModal .rl-info-summary {
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:7px;
  }
  #rlInfoModal .rl-info-stat {
    min-width:0;
    padding:8px 9px;
    border:1px solid #e2e8f0;
    border-left:3px solid #3b82f6;
    border-radius:9px;
    background:#f8fafc;
  }
  #rlInfoModal .rl-info-stat.good { border-left-color:#10b981; }
  #rlInfoModal .rl-info-stat.warn { border-left-color:#f59e0b; }
  #rlInfoModal .rl-info-stat.bad { border-left-color:#ef4444; }
  #rlInfoModal .rl-info-stat span {
    display:block;
    overflow:hidden;
    color:#64748b;
    font-size:7px;
    line-height:1;
    font-weight:900;
    letter-spacing:.05em;
    text-transform:uppercase;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  #rlInfoModal .rl-info-stat strong {
    display:block;
    margin-top:5px;
    overflow:hidden;
    color:#0f172a;
    font-family:"JetBrains Mono",ui-monospace,monospace;
    font-size:10px;
    line-height:1.15;
    font-weight:900;
    text-overflow:ellipsis;
    white-space:nowrap;
  }

  #rlInfoModal .rl-info-condition {
    margin-top:8px;
    padding:9px 10px;
    border:1px solid #bfdbfe;
    border-left:4px solid #2563eb;
    border-radius:9px;
    background:#eff6ff;
    color:#1e3a8a;
    font-size:8.5px;
    line-height:1.45;
    font-weight:700;
  }
  #rlInfoModal .rl-info-condition.good {
    border-color:#a7f3d0;
    border-left-color:#10b981;
    background:#ecfdf5;
    color:#065f46;
  }
  #rlInfoModal .rl-info-condition.warn {
    border-color:#fde68a;
    border-left-color:#f59e0b;
    background:#fffbeb;
    color:#92400e;
  }

  #rlInfoModal .rl-info-grid {
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:7px;
    margin-top:8px;
  }
  #rlInfoModal .rl-info-grid > div {
    min-width:0;
    padding:9px 10px;
    border:1px solid #e2e8f0;
    border-radius:9px;
    background:#fff;
  }
  #rlInfoModal .rl-info-grid b {
    display:block;
    color:#0f172a;
    font-size:9px;
    line-height:1.15;
    font-weight:900;
  }
  #rlInfoModal .rl-info-grid span {
    display:block;
    margin-top:5px;
    color:#64748b;
    font-size:8px;
    line-height:1.45;
    font-weight:650;
  }
  #rlInfoModal .rl-info-note {
    margin-top:8px;
    padding:8px 9px;
    border:1px solid #dbe4f0;
    border-radius:9px;
    background:#f8fafc;
    color:#475569;
    font-size:8px;
    line-height:1.45;
    font-weight:700;
  }


  #rlInfoModal .rl-info-performance-grid {
    display:grid;
    grid-template-columns:1.25fr 1fr 1fr;
    gap:7px;
    margin-top:8px;
    align-items:start;
  }
  #rlInfoModal .rl-info-panel {
    min-width:0;
    overflow:hidden;
    border:1px solid #e2e8f0;
    border-radius:10px;
    background:#fff;
  }
  #rlInfoModal .rl-info-panel-priority { border-color:#fecdd3; }
  #rlInfoModal .rl-info-panel-head {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    padding:8px 9px;
    border-bottom:1px solid #eef2f7;
    background:#f8fafc;
  }
  #rlInfoModal .rl-info-eyebrow {
    display:block;
    color:#94a3b8;
    font-size:6px;
    line-height:1;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
  }
  #rlInfoModal .rl-info-panel h4 {
    margin:3px 0 0;
    color:#0f172a;
    font-size:9px;
    line-height:1.15;
    font-weight:900;
  }
  #rlInfoModal .rl-info-count {
    min-width:22px;
    height:22px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:0 6px;
    border:1px solid #fecdd3;
    border-radius:999px;
    background:#fff1f2;
    color:#be123c;
    font-size:7px;
    font-weight:900;
  }
  #rlInfoModal .rl-info-list { max-height:235px; overflow:auto; }
  #rlInfoModal .rl-info-list-item {
    display:grid;
    grid-template-columns:minmax(0,1fr) auto;
    gap:8px;
    align-items:center;
    min-width:0;
    padding:7px 8px;
    border-bottom:1px solid #f1f5f9;
  }
  #rlInfoModal .rl-info-list-item:last-child { border-bottom:0; }
  #rlInfoModal .rl-info-list-item.is-bad { background:#fffafa; }
  #rlInfoModal .rl-info-list-main { min-width:0; }
  #rlInfoModal .rl-info-office {
    display:flex;
    align-items:center;
    min-width:0;
    gap:5px;
  }
  #rlInfoModal .rl-info-office b {
    flex:0 0 auto;
    color:#1d4ed8;
    font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
    font-size:7px;
    font-weight:900;
  }
  #rlInfoModal .rl-info-office span {
    min-width:0;
    overflow:hidden;
    color:#334155;
    font-size:7.5px;
    font-weight:850;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  #rlInfoModal .rl-info-reasons {
    display:flex;
    flex-wrap:wrap;
    gap:3px;
    margin-top:4px;
  }
  #rlInfoModal .rl-info-reasons span {
    display:inline-flex;
    align-items:center;
    min-height:16px;
    padding:0 5px;
    border:1px solid #fecdd3;
    border-radius:999px;
    background:#fff1f2;
    color:#be123c;
    font-size:5.8px;
    font-weight:850;
    white-space:nowrap;
  }
  #rlInfoModal .rl-info-list-value {
    min-width:80px;
    text-align:right;
  }
  #rlInfoModal .rl-info-list-value strong {
    display:block;
    color:#0f172a;
    font-family:"JetBrains Mono",ui-monospace,monospace;
    font-size:7.5px;
    font-weight:900;
    white-space:nowrap;
  }
  #rlInfoModal .rl-info-list-item.is-bad .rl-info-list-value strong { color:#dc2626; }
  #rlInfoModal .rl-info-list-value span {
    display:block;
    margin-top:3px;
    color:#94a3b8;
    font-size:6px;
    font-weight:750;
    white-space:nowrap;
  }
  #rlInfoModal .rl-info-empty {
    padding:16px 10px;
    color:#94a3b8;
    text-align:center;
    font-size:7px;
    font-weight:750;
  }
  #rlInfoModal .rl-info-actions {
    margin-top:8px;
    padding:8px 9px;
    border:1px solid #bfdbfe;
    border-radius:9px;
    background:#eff6ff;
    color:#1e3a8a;
  }
  #rlInfoModal .rl-info-actions-title {
    font-size:7px;
    line-height:1;
    font-weight:900;
    letter-spacing:.06em;
    text-transform:uppercase;
  }
  #rlInfoModal .rl-info-actions ul {
    margin:6px 0 0;
    padding-left:15px;
    font-size:7.5px;
    line-height:1.45;
    font-weight:700;
  }
  #rlInfoModal .rl-info-actions li + li { margin-top:4px; }

  @media (max-width:1050px) {
    #rlInfoModal .rl-info-performance-grid { grid-template-columns:1fr 1fr; }
    #rlInfoModal .rl-info-panel-priority { grid-column:1 / -1; }
  }

  /* ---------- Large desktop ---------- */
  @media (min-width:1440px) {
    #rlPage.rl-page {
      height:calc(100dvh - 72px);
      padding:8px 12px 12px;
    }
    #rlPage .rl-header { padding:10px 12px; }
    #rlPage .rl-filter { grid-template-columns:130px minmax(210px,285px) 37px; }
    #rlPage .rl-field input,
    #rlPage .rl-field select,
    #rlPage .rl-export { height:36px; }
    #rlPage .rl-export { width:37px; }
    #rlPage #rlTable { min-width:0; }
    #rlPage #rlTable th,
    #rlPage #rlTable td { height:38px; padding:6px 8px; }
    #rlPage .rl-table-shell { --rl-head-h:38px; }
  }

  /* ---------- Tablet ---------- */
  @media (min-width:768px) and (max-width:1023px) {
    #rlPage.rl-page {
      height:calc(100dvh - 64px);
      padding:6px;
      gap:6px;
    }
    #rlPage .rl-header {
      grid-template-columns:1fr auto;
      gap:7px;
      padding:8px;
    }
    #rlPage .rl-filter-toggle { display:inline-flex; }
    #rlPage .rl-brand { width:100%; }
    #rlPage .rl-filter {
      grid-column:1 / -1;
      display:none;
      grid-template-columns:130px minmax(220px,1fr) 36px;
      width:100%;
      padding-top:7px;
      border-top:1px solid #e2e8f0;
    }
    #rlPage .rl-filter.open { display:grid; }
    #rlPage .rl-search { width:220px; }
    #rlPage #rlTable { min-width:900px; }
  }

  /* ---------- Mobile ---------- */
  @media (max-width:767px) {
    #rlPage.rl-page {
      height:calc(100dvh - 54px);
      min-height:0;
      gap:5px;
      padding:4px 4px max(5px,env(safe-area-inset-bottom));
      overflow:hidden;
    }

    #rlPage .rl-header {
      display:grid;
      grid-template-columns:1fr;
      gap:6px;
      padding:7px;
      border-radius:9px;
    }
    #rlPage .rl-brand { width:100%; gap:7px; }
    #rlPage .rl-icon {
      width:29px;
      height:29px;
      flex-basis:29px;
      border-radius:7px;
    }
    #rlPage .rl-icon svg { width:14px; height:14px; }
    #rlPage .rl-title-row { gap:5px; }
    #rlPage .rl-title-row h1 { font-size:12px; }
    #rlPage .rl-copy p {
      max-width:205px;
      margin-top:2px;
      font-size:7px;
    }
    #rlPage .rl-info-btn {
      width:18px;
      height:18px;
      flex-basis:18px;
      font-size:9px;
    }
    #rlPage .rl-filter-toggle {
      display:inline-flex;
      height:29px;
      padding:0 8px;
      font-size:8px;
    }

    #rlPage .rl-filter {
      display:none;
      grid-template-columns:minmax(0,1fr) minmax(0,1fr) 32px;
      width:100%;
      gap:5px;
      padding-top:6px;
      border-top:1px solid #e2e8f0;
    }
    #rlPage .rl-filter.open { display:grid; }
    #rlPage .rl-field span { font-size:6.5px; }
    #rlPage .rl-field input,
    #rlPage .rl-field select {
      height:31px;
      padding:0 6px;
      border-radius:7px;
      font-size:8.5px;
    }
    #rlPage .rl-field select {
      padding-right:20px;
      background-position:right 5px center;
      background-size:10px 10px;
    }
    #rlPage .rl-export {
      width:32px;
      height:31px;
      border-radius:7px;
    }
    #rlPage .rl-export svg { width:15px; height:15px; }

    #rlPage .rl-table-card { border-radius:9px; }
    #rlPage .rl-table-toolbar {
      min-height:auto;
      display:grid;
      grid-template-columns:minmax(0,1fr) minmax(115px,40vw);
      align-items:center;
      gap:5px;
      padding:6px;
    }
    #rlPage .rl-table-toolbar h2 { font-size:10px; }
    #rlPage .rl-table-toolbar p {
      max-width:145px;
      margin-top:2px;
      font-size:6.5px;
    }
    #rlPage .rl-search {
      width:100%;
      min-width:0;
      height:28px;
      border-radius:7px;
    }
    #rlPage .rl-search svg { left:7px; width:11px; height:11px; }
    #rlPage .rl-search input {
      padding:0 6px 0 23px;
      font-size:7.5px;
    }

    #rlPage .rl-table-shell {
      scrollbar-gutter:auto;
      overflow:auto;
    }

    /* Kode disembunyikan karena sudah ada kecil di bawah Nama Kantor.
       Nama Kantor menjadi satu-satunya freeze column agar ruang data lega. */
    #rlPage #rlTable {
      width:100%;
      min-width:740px;
      table-layout:fixed;
      font-size:8px;
    }
    #rlPage .rl-col-code { width:0 !important; }
    #rlPage .rl-col-office { width:126px !important; }
    #rlPage .rl-col-money { width:102px !important; }

    #rlPage #rlTable th,
    #rlPage #rlTable td {
      height:34px;
      padding:4px 5px;
    }
    #rlPage #rlTable th {
      height:31px;
      font-size:6.5px;
      letter-spacing:.025em;
    }
    #rlPage #rlTable .rl-money {
      font-size:7.2px;
      letter-spacing:-.04em;
    }

    #rlPage #rlTable th.rl-sticky-code,
    #rlPage #rlTable td.rl-sticky-code {
      display:none !important;
    }

    #rlPage #rlTable th.rl-sticky-office,
    #rlPage #rlTable td.rl-sticky-office {
      position:-webkit-sticky !important;
      position:sticky !important;
      left:0 !important;
      width:126px !important;
      min-width:126px !important;
      max-width:126px !important;
    }
    #rlPage #rlTable th.rl-sticky-office {
      z-index:38;
      text-align:left;
      background:#e6f1fb;
    }
    #rlPage #rlTable td.rl-sticky-office {
      z-index:22;
      background:#fff;
      box-shadow:5px 0 9px -9px rgba(15,23,42,.9);
    }
    #rlPage #rlTable tbody tr:nth-child(even) td.rl-sticky-office { background:#fcfdff; }

    #rlPage .rl-office-name {
      font-size:8px;
      line-height:1.1;
    }
    #rlPage .rl-office-code { margin-top:2px; font-size:6px; }

    #rlPage .rl-table-shell { --rl-head-h:31px; }
    #rlPage #rlTable tbody .rl-total-row td {
      top:var(--rl-head-h) !important;
      height:35px !important;
      z-index:70 !important;
    }
    #rlPage #rlTable tbody .rl-total-row td.rl-sticky-office {
      left:0 !important;
      z-index:110 !important;
      background:#eaf3ff !important;
    }

    #rlInfoModal.rl-modal {
      align-items:flex-end;
      padding:0;
    }
    #rlInfoModal .rl-modal-card {
      width:100%;
      max-height:92dvh;
      border-right:0;
      border-bottom:0;
      border-left:0;
      border-radius:16px 16px 0 0;
      box-shadow:0 -18px 48px rgba(15,23,42,.30);
    }
    #rlInfoModal .rl-modal-head { padding:10px; }
    #rlInfoModal .rl-modal-kicker { font-size:6.5px; }
    #rlInfoModal .rl-modal-head h3 { font-size:12px; }
    #rlInfoModal .rl-modal-head button {
      width:30px;
      height:30px;
      flex-basis:30px;
    }
    #rlInfoModal .rl-modal-body {
      padding:8px 9px max(12px,env(safe-area-inset-bottom));
    }
    #rlInfoModal .rl-info-summary {
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:5px;
    }
    #rlInfoModal .rl-info-performance-grid { grid-template-columns:1fr; gap:6px; }
    #rlInfoModal .rl-info-panel-priority { grid-column:auto; }
    #rlInfoModal .rl-info-list { max-height:none; }
    #rlInfoModal .rl-info-list-item { padding:7px; }
    #rlInfoModal .rl-info-office span { font-size:7.2px; }
    #rlInfoModal .rl-info-list-value strong { font-size:7.2px; }
    #rlInfoModal .rl-info-stat { padding:7px; }
    #rlInfoModal .rl-info-stat strong { font-size:9px; }
    #rlInfoModal .rl-info-condition { font-size:7.8px; }
    #rlInfoModal .rl-info-grid { grid-template-columns:1fr; gap:6px; }
    #rlInfoModal .rl-info-grid > div { padding:8px; }
    #rlInfoModal .rl-info-grid b { font-size:8.5px; }
    #rlInfoModal .rl-info-grid span,
    #rlInfoModal .rl-info-note { font-size:7.6px; }
  }

  @media (max-width:380px) {
    #rlPage .rl-copy p { max-width:170px; }
    #rlPage .rl-table-toolbar { grid-template-columns:minmax(0,1fr) 112px; }
    #rlPage #rlTable { min-width:700px; }
    #rlPage #rlTable th.rl-sticky-office,
    #rlPage #rlTable td.rl-sticky-office {
      width:116px !important;
      min-width:116px !important;
      max-width:116px !important;
    }
    #rlPage .rl-col-office { width:116px !important; }
    #rlPage .rl-col-money { width:97px !important; }
  }
</style>

<script>
(function(){
  const API_LAP = './api/lapkeu';
  const API_KODE = './api/kode/';
  const KORWIL = [
    { value:'KW_SEMARANG', label:'Korwil Semarang', korwil:'SEMARANG' },
    { value:'KW_SOLO', label:'Korwil Solo', korwil:'SOLO' },
    { value:'KW_BANYUMAS', label:'Korwil Banyumas', korwil:'BANYUMAS' },
    { value:'KW_PEKALONGAN', label:'Korwil Pekalongan', korwil:'PEKALONGAN' },
  ];

  let rlRaw = null;
  let rlRows = [];
  let rlUser = null;
  let rlFetchTimer = null;

  const $ = (id) => document.getElementById(id);
  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
  const fmt = (v) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.round(Number(v || 0)));
  const fmtShort = (v) => {
    const raw = Number(v || 0);
    const n = Math.abs(raw);
    const sign = raw < 0 ? '-' : '';
    const dec = value => Number(value).toFixed(2).replace('.', ',').replace(/,00$/, '');
    if (n >= 1e12) return sign + dec(n / 1e12) + ' T';
    if (n >= 1e9) return sign + dec(n / 1e9) + ' M';
    if (n >= 1e6) return sign + dec(n / 1e6) + ' Jt';
    return sign + fmt(n);
  };

  function getUser(){
    try {
      if (typeof window.getUser === 'function') return window.getUser();
      const raw = localStorage.getItem('user') || sessionStorage.getItem('user');
      return raw ? JSON.parse(raw) : null;
    } catch(e) {
      return null;
    }
  }

  function officePayload(){
    const val = $('rlKantor').value || '000';
    if (val.startsWith('KW_')) {
      const item = KORWIL.find(x => x.value === val);
      return { kode_kantor:'000', korwil: item ? item.korwil : '' };
    }
    if (val === 'PUSAT') return { kode_kantor:'pusat', korwil:'' };
    return { kode_kantor: val, korwil:'' };
  }

  async function api(url, body){
    const res = await fetch(url, {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify(body || {})
    });
    const json = await res.json();
    if (!res.ok || Number(json.status) >= 400) throw new Error(json.message || 'Gagal memuat data');
    return json;
  }

  function localDateYmd(date = new Date()){
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  function normalizeRlDate(value){
    if (!value) return '';
    const raw = String(value).trim();

    // ISO / datetime: 2026-08-19 or 2026-08-19 00:00:00
    const iso = raw.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (iso) return `${iso[1]}-${iso[2]}-${iso[3]}`;

    // dd/mm/yyyy
    const id = raw.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
    if (id) return `${id[3]}-${id[2]}-${id[1]}`;

    return '';
  }

  async function loadDefaultDate(){
    const input = $('rlTanggal');
    if (!input) return;

    // Set a valid value immediately so the field never stays dd/mm/tttt.
    input.value = localDateYmd();

    try {
      const res = await api(API_LAP, { type:'default_acc_history_date' });
      const data = res?.data;

      const candidate =
        data?.last_created ??
        data?.harian_date ??
        data?.tanggal ??
        data?.date ??
        (typeof data === 'string' ? data : '') ??
        '';

      const date = normalizeRlDate(candidate);
      if (date) input.value = date;
    } catch(e) {
      console.warn('[Rekap Keuangan] default date fallback:', e);
      // Keep local date already assigned above.
    }
  }

  async function loadOfficeOptions(){
    rlUser = getUser();
    const kodeUser = String(rlUser?.kode_kantor || rlUser?.kode || '000').padStart(3, '0');
    const select = $('rlKantor');

    if (kodeUser !== '000') {
      const nama = rlUser?.nama_kantor || rlUser?.kantor || `Kc. ${kodeUser}`;
      select.innerHTML = `<option value="${esc(kodeUser)}">${esc(kodeUser)} - ${esc(nama)}</option>`;
      select.disabled = true;
      return;
    }

    let html = '<option value="000">000 - Konsolidasi</option><option value="PUSAT">000 - Pusat</option>';
    KORWIL.forEach(k => { html += `<option value="${k.value}">${k.label}</option>`; });
    try {
      const res = await api(API_KODE, { type:'kode_kantor' });
      (res.data || []).forEach(row => {
        const kode = String(row.kode_kantor || '').padStart(3, '0');
        html += `<option value="${esc(kode)}">${esc(kode)} - ${esc(row.nama_kantor || '')}</option>`;
      });
    } catch(e) {}
    select.innerHTML = html;
    select.disabled = false;
  }

  function scheduleFetch(){
    clearTimeout(rlFetchTimer);
    rlFetchTimer = setTimeout(fetchRekapLapkeu, 400);
  }

  async function fetchRekapLapkeu(){
    const loader = $('rlLoader');
    loader.classList.remove('rl-hidden');
    try {
      const office = officePayload();
      const res = await api(API_LAP, {
        type:'rekap_lapkeu_actual',
        harian_date:$('rlTanggal').value,
        kode_kantor:office.kode_kantor,
        korwil:office.korwil
      });
      rlRaw = res.data || {};
      rlRows = Array.isArray(rlRaw.rows) ? rlRaw.rows : [];
      renderAll();
    } catch(e) {
      $('rlBody').innerHTML = `<tr><td colspan="8" class="rl-empty">${esc(e.message || 'Gagal memuat data')}</td></tr>`;
    } finally {
      loader.classList.add('rl-hidden');
    }
  }

  function valueClass(v, mode){
    const n = Number(v || 0);
    if (mode === 'laba') return n < 0 ? 'rl-neg' : 'rl-pos';
    return '';
  }

  function moneyCell(v, mode){
    return `<td class="rl-money ${valueClass(v, mode)}" title="${fmt(v)}">${fmt(v)}</td>`;
  }

  function rowHtml(row, isTotal){
    const kode = isTotal ? 'TOTAL' : String(row.kode_kantor || '');
    const name = isTotal ? 'TOTAL' : (row.nama_kantor || '-');
    return `
      <tr class="${isTotal ? 'rl-total-row' : ''}">
        <td class="rl-sticky-code">${esc(kode)}</td>
        <td class="rl-sticky-office">
          <span class="rl-office-name">${esc(name)}</span>
          ${isTotal ? '' : `<span class="rl-office-code">${esc(kode)}</span>`}
        </td>
        ${moneyCell(row.aset)}
        ${moneyCell(row.liabilitas)}
        ${moneyCell(row.ekuitas)}
        ${moneyCell(row.pendapatan)}
        ${moneyCell(row.beban)}
        ${moneyCell(row.laba_kotor, 'laba')}
      </tr>
    `;
  }

  function filteredRows(){
    const q = ($('rlSearch').value || '').trim().toLowerCase();
    if (!q) return rlRows.slice();
    return rlRows.filter(row => {
      const hay = `${row.kode_kantor || ''} ${row.nama_kantor || ''}`.toLowerCase();
      return hay.includes(q);
    });
  }

  function renderAll(){
    const total = rlRaw?.total || {};
    const summary = rlRaw?.summary || {};
    const rows = filteredRows();

    const bebanRow = summary.beban_terbesar || {};
    const labaMinus = rlRows.filter(row => Number(row?.laba_kotor || 0) < 0).length;
    $('rlMeta').textContent = `${rlRaw?.scope_label || '-'} · ${rlRaw?.tanggal || '-'} · ${rows.length} kantor${labaMinus ? ` · ${labaMinus} laba minus` : ''}`;

    let html = rowHtml({
      kode_kantor:'TOTAL',
      nama_kantor:'TOTAL',
      aset:total.aset,
      liabilitas:total.liabilitas,
      ekuitas:total.ekuitas,
      pendapatan:total.pendapatan,
      beban:total.beban,
      laba_kotor:total.laba_kotor
    }, true);

    if (!rows.length) {
      html += `<tr><td colspan="8" class="rl-empty">Tidak ada data.</td></tr>`;
    } else {
      rows.forEach(row => { html += rowHtml(row, false); });
    }
    $('rlBody').innerHTML = html;
    renderRlInfo();
  }

  function rlPct(value){
    const n = Number(value);
    if (!Number.isFinite(n)) return '-';
    return `${n.toFixed(2).replace('.', ',')}%`;
  }

  function rlPerformanceRows(){
    return (Array.isArray(rlRows) ? rlRows : []).map(row => {
      const pendapatan = Number(row?.pendapatan || 0);
      const beban = Number(row?.beban || 0);
      const laba = Number(row?.laba_kotor ?? (pendapatan - beban));
      const ekuitas = Number(row?.ekuitas || 0);
      const margin = pendapatan !== 0 ? (laba / Math.abs(pendapatan)) * 100 : null;
      const expenseRatio = pendapatan !== 0 ? (beban / Math.abs(pendapatan)) * 100 : null;
      const reasons = [];
      if (laba < 0) reasons.push('Laba negatif');
      if (expenseRatio !== null && expenseRatio > 100) reasons.push('Beban > pendapatan');
      if (ekuitas < 0) reasons.push('Ekuitas negatif');
      return {
        row,
        kode:String(row?.kode_kantor || ''),
        nama:String(row?.nama_kantor || '-'),
        pendapatan,
        beban,
        laba,
        ekuitas,
        margin,
        expenseRatio,
        reasons
      };
    });
  }

  function rlPerformanceItem(item, mode='profit'){
    const negative = item.laba < 0;
    let rightMain = '';
    let rightSub = '';
    if (mode === 'expense') {
      rightMain = fmtShort(item.beban);
      rightSub = item.expenseRatio === null ? 'Rasio beban -' : `Beban/Pendapatan ${rlPct(item.expenseRatio)}`;
    } else {
      rightMain = fmtShort(item.laba);
      rightSub = item.margin === null ? 'Margin -' : `Margin ${rlPct(item.margin)}`;
    }
    const badges = item.reasons.length
      ? `<div class="rl-info-reasons">${item.reasons.map(reason => `<span>${esc(reason)}</span>`).join('')}</div>`
      : '';
    return `
      <div class="rl-info-list-item ${negative ? 'is-bad' : ''}">
        <div class="rl-info-list-main">
          <div class="rl-info-office"><b>${esc(item.kode)}</b><span>${esc(item.nama)}</span></div>
          ${badges}
        </div>
        <div class="rl-info-list-value">
          <strong>${esc(rightMain)}</strong>
          <span>${esc(rightSub)}</span>
        </div>
      </div>`;
  }

  function renderRlInfo(){
    const summaryEl = $('rlInfoSummary');
    const conditionEl = $('rlInfoCondition');
    const priorityEl = $('rlInfoPriorityList');
    const priorityCountEl = $('rlInfoPriorityCount');
    const lowestProfitEl = $('rlInfoLowestProfit');
    const topExpenseEl = $('rlInfoTopExpense');
    const actionsEl = $('rlInfoActions');
    if (!summaryEl || !conditionEl || !rlRaw) return;

    const total = rlRaw?.total || {};
    const perf = rlPerformanceRows();
    const laba = Number(total.laba_kotor || 0);
    const negativeRows = perf.filter(item => item.laba < 0);
    const negativeEquity = perf.filter(item => item.ekuitas < 0);
    const priorityRows = perf
      .filter(item => item.reasons.length)
      .sort((a,b) => {
        const aRisk = (a.laba < 0 ? 1000000 : 0) + (a.ekuitas < 0 ? 100000 : 0) + Math.max(0, (a.expenseRatio || 0) - 100) * 1000;
        const bRisk = (b.laba < 0 ? 1000000 : 0) + (b.ekuitas < 0 ? 100000 : 0) + Math.max(0, (b.expenseRatio || 0) - 100) * 1000;
        if (bRisk !== aRisk) return bRisk - aRisk;
        return a.laba - b.laba;
      });
    const lowestProfit = [...perf].sort((a,b) => a.laba - b.laba).slice(0,5);
    const topExpense = [...perf].sort((a,b) => b.beban - a.beban).slice(0,5);
    const maxExpense = topExpense[0];

    summaryEl.innerHTML = `
      <article class="rl-info-stat">
        <span>Total Aset</span>
        <strong>${fmtShort(total.aset)}</strong>
      </article>
      <article class="rl-info-stat ${laba < 0 ? 'bad' : 'good'}">
        <span>Laba Kotor</span>
        <strong>${fmtShort(laba)}</strong>
      </article>
      <article class="rl-info-stat ${negativeRows.length ? 'warn' : 'good'}">
        <span>Laba Minus</span>
        <strong>${negativeRows.length} Kantor</strong>
      </article>
      <article class="rl-info-stat ${negativeEquity.length ? 'warn' : 'good'}">
        <span>Ekuitas Negatif</span>
        <strong>${negativeEquity.length} Kantor</strong>
      </article>
    `;

    if (negativeRows.length) {
      conditionEl.className = 'rl-info-condition warn';
      conditionEl.innerHTML = `<b>${negativeRows.length} kantor memiliki laba kotor negatif.</b> Prioritaskan kantor tersebut untuk review sumber pendapatan dan beban. ${negativeEquity.length ? `<b>${negativeEquity.length} kantor</b> juga memiliki ekuitas negatif.` : ''}`;
    } else if (negativeEquity.length) {
      conditionEl.className = 'rl-info-condition warn';
      conditionEl.innerHTML = `<b>Tidak ada kantor dengan laba kotor negatif,</b> tetapi terdapat <b>${negativeEquity.length} kantor dengan ekuitas negatif</b> yang perlu dicermati.`;
    } else {
      conditionEl.className = 'rl-info-condition good';
      conditionEl.innerHTML = `<b>Tidak ada indikator negatif utama pada data yang sedang tampil.</b> Tetap pantau kantor dengan laba terendah dan beban nominal terbesar agar pemburukan dapat diketahui lebih awal.`;
    }

    if (priorityCountEl) priorityCountEl.textContent = String(priorityRows.length);
    if (priorityEl) {
      priorityEl.innerHTML = priorityRows.length
        ? priorityRows.slice(0,7).map(item => rlPerformanceItem(item, 'profit')).join('')
        : '<div class="rl-info-empty">Tidak ada cabang yang memenuhi indikator perhatian utama.</div>';
    }
    if (lowestProfitEl) {
      lowestProfitEl.innerHTML = lowestProfit.length
        ? lowestProfit.map(item => rlPerformanceItem(item, 'profit')).join('')
        : '<div class="rl-info-empty">Data belum tersedia.</div>';
    }
    if (topExpenseEl) {
      topExpenseEl.innerHTML = topExpense.length
        ? topExpense.map(item => rlPerformanceItem(item, 'expense')).join('')
        : '<div class="rl-info-empty">Data belum tersedia.</div>';
    }

    const actions = [];
    if (negativeRows.length) actions.push(`Review ${negativeRows.length} kantor laba minus: cek penurunan pendapatan, kenaikan beban, dan transaksi nonrutin pada kantor terkait.`);
    if (negativeEquity.length) actions.push(`Cermati ${negativeEquity.length} kantor dengan ekuitas negatif dan telusuri komponen liabilitas/ekuitas penyebabnya.`);
    if (maxExpense) actions.push(`Beban nominal terbesar: ${maxExpense.nama} (${maxExpense.kode}) sebesar ${fmtShort(maxExpense.beban)}${maxExpense.expenseRatio !== null ? ` atau ${rlPct(maxExpense.expenseRatio)} dari pendapatan` : ''}.`);
    if (!actions.length) actions.push('Pertahankan monitoring harian dan bandingkan perubahan pendapatan, beban, laba, serta ekuitas antar kantor.');
    if (actionsEl) actionsEl.innerHTML = `<div class="rl-info-actions-title">Fokus Tindak Lanjut</div><ul>${actions.map(item => `<li>${esc(item)}</li>`).join('')}</ul>`;
  }

  function exportRekapLapkeu(){
    const total = rlRaw?.total || {};
    const rows = filteredRows();
    const escXls = (v) => esc(v).replace(/\n/g, ' ');
    let html = '<table border="1"><thead><tr><th>Kode</th><th>Kantor</th><th>Aset</th><th>Liabilitas</th><th>Ekuitas</th><th>Pendapatan</th><th>Beban</th><th>Laba Kotor</th></tr></thead><tbody>';
    html += `<tr><td>TOTAL</td><td>TOTAL</td><td>${Number(total.aset || 0)}</td><td>${Number(total.liabilitas || 0)}</td><td>${Number(total.ekuitas || 0)}</td><td>${Number(total.pendapatan || 0)}</td><td>${Number(total.beban || 0)}</td><td>${Number(total.laba_kotor || 0)}</td></tr>`;
    rows.forEach(row => {
      html += `<tr><td style="mso-number-format:'\\@'">${escXls(row.kode_kantor || '')}</td><td>${escXls(row.nama_kantor || '')}</td><td>${Number(row.aset || 0)}</td><td>${Number(row.liabilitas || 0)}</td><td>${Number(row.ekuitas || 0)}</td><td>${Number(row.pendapatan || 0)}</td><td>${Number(row.beban || 0)}</td><td>${Number(row.laba_kotor || 0)}</td></tr>`;
    });
    html += '</tbody></table>';
    const blob = new Blob(['\ufeff' + html], {type:'application/vnd.ms-excel;charset=utf-8;'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `rekap_lapkeu_${rlRaw?.tanggal || 'data'}.xls`;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => { URL.revokeObjectURL(a.href); a.remove(); }, 100);
  }

  window.fetchRekapLapkeu = fetchRekapLapkeu;
  window.exportRekapLapkeu = exportRekapLapkeu;
  window.toggleRlInfo = function(show){
    const modal = $('rlInfoModal');
    if (!modal) return;
    if (show) renderRlInfo();
    modal.classList.toggle('rl-hidden', !show);
    modal.setAttribute('aria-hidden', show ? 'false' : 'true');
    document.documentElement.style.overflow = show ? 'hidden' : '';
  };
  window.toggleRlFilter = function(){
    const form = $('rlFilter');
    const opened = !form.classList.contains('open');
    form.classList.toggle('open', opened);
    $('rlFilterToggle')?.setAttribute('aria-expanded', opened ? 'true' : 'false');
  };

  document.addEventListener('DOMContentLoaded', async () => {
    await Promise.all([loadDefaultDate(), loadOfficeOptions()]);
    $('rlTanggal').addEventListener('change', scheduleFetch);
    $('rlKantor').addEventListener('change', scheduleFetch);
    $('rlSearch').addEventListener('input', renderAll);

    const syncStickyHeaderHeight = () => {
      const shell = document.querySelector('#rlPage .rl-table-shell');
      const th = document.querySelector('#rlPage #rlTable thead th');
      if (!shell || !th) return;
      const h = Math.ceil(th.getBoundingClientRect().height || 0);
      if (h > 0) shell.style.setProperty('--rl-head-h', `${h}px`);
    };

    requestAnimationFrame(syncStickyHeaderHeight);
    window.addEventListener('resize', syncStickyHeaderHeight, { passive:true });

    $('rlInfoModal')?.addEventListener('click', (e) => {
      if (e.target === $('rlInfoModal')) toggleRlInfo(false);
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !$('rlInfoModal')?.classList.contains('rl-hidden')) toggleRlInfo(false);
    });

    fetchRekapLapkeu();
  });
})();
</script>

<?php
// Laporan RBB vs Realisasi Neraca dan Laba Rugi.
?>

<div id="rbbLapPage" class="w-full bg-slate-50 font-sans text-slate-900">
  <section id="rbbLapHeader" class="rbb-page-header">
    <div class="rbb-brand-row">
      <div class="rbb-brand-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 6-7"/>
        </svg>
      </div>

      <div class="rbb-brand-copy">
        <div class="rbb-title-row">
          <h1>RBB vs Realisasi</h1>
          <button type="button" onclick="toggleRbbLapInfo(true)" class="rbb-info-btn" title="Analisis kondisi dan rekomendasi direksi" aria-label="Analisis kondisi dan rekomendasi direksi">i</button>
        </div>
        <p>Neraca dan laba rugi berdasarkan RBB periode terpilih</p>
      </div>

      <button type="button" id="rbbLapFilterToggle" class="rbb-filter-toggle" aria-expanded="false" aria-controls="rbbLapFilter">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
        <span>Filter</span>
      </button>
    </div>

    <form id="rbbLapFilter" class="rbb-filter-form" onsubmit="event.preventDefault(); fetchRbbLap();">
      <label class="field rbb-field-type">
        <span>Jenis Laporan</span>
        <select id="rbbLapJenis" class="control">
          <option value="neraca">Neraca</option>
          <option value="laba_rugi">Laba Rugi</option>
        </select>
      </label>

      <label class="field rbb-field-date">
        <span>Actual Harian</span>
        <input id="rbbLapHarian" type="date" class="control" onclick="this.showPicker && this.showPicker()">
      </label>

      <label class="field rbb-field-office">
        <span>Area / Cabang</span>
        <select id="rbbLapKantor" class="control">
          <option value="000">Konsolidasi</option>
        </select>
      </label>

      <button type="button" onclick="exportRbbLapExcel()" class="rbb-excel-btn" title="Download Excel" aria-label="Download Excel">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
      </button>
    </form>
  </section>

  <section id="rbbLapWorkspace" class="rbb-workspace">
    <div id="rbbLapLoading" class="rbb-loading hidden">
      <div class="rbb-spinner"></div>
      <span>Memuat data</span>
    </div>

    <div class="rbb-toolbar">
      <div class="rbb-search-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input id="rbbLapSearch" type="search" placeholder="Cari keterangan, kategori, kode akun..." autocomplete="off">
        <button type="button" id="rbbLapSearchClear" class="rbb-search-clear hidden" title="Hapus pencarian" aria-label="Hapus pencarian">&times;</button>
      </div>

      <select id="rbbLapCategoryFilter" class="rbb-category-filter" title="Filter kategori">
        <option value="ALL">Semua Kategori</option>
      </select>

      <div id="rbbLapResultMeta" class="rbb-result-meta">0 data</div>
    </div>

    <div id="rbbLapQuickInfo" class="rbb-quick-info">
      <span class="rbb-quick-chip"><b>Posisi:</b> <span id="rbbLapQuickDate">-</span></span>
      <span class="rbb-quick-chip"><b>Lingkup:</b> <span id="rbbLapQuickScope">-</span></span>
      <span class="rbb-quick-chip"><b>Satuan:</b> Ribuan rupiah</span>
      <span class="rbb-quick-chip rbb-quick-attention"><b>Perlu perhatian:</b> <span id="rbbLapQuickAttention">0 akun</span></span>
    </div>

    <div class="rbb-table-shell custom-scrollbar">
      <table id="rbbLapTable">
        <colgroup>
          <col class="col-code">
          <col class="col-name">
          <col class="col-money"><col class="col-money"><col class="col-money">
          <col class="col-money"><col class="col-pct">
          <col class="col-money"><col class="col-pct">
        </colgroup>
        <thead>
          <tr>
            <th class="th sticky-code" rowspan="2">Kode</th>
            <th class="th sticky-name text-left" rowspan="2">Keterangan</th>
            <th class="th th-rbb text-center" colspan="2">Target RBB</th>
            <th class="th th-real text-center" colspan="1">Realisasi</th>
            <th class="th th-result text-center" colspan="2">Terhadap RBB Periode</th>
            <th class="th th-year text-center" colspan="2">Terhadap RBB Desember</th>
          </tr>
          <tr>
            <th class="th th-rbb-sub text-right">Periode</th>
            <th class="th th-rbb-sub text-right">Desember</th>
            <th class="th th-real-sub text-right">Actual</th>
            <th class="th th-result-sub text-right">Selisih</th>
            <th class="th th-result-sub text-right">Capaian</th>
            <th class="th th-year-sub text-right">Selisih</th>
            <th class="th th-year-sub text-right">Capaian</th>
          </tr>
        </thead>
        <tbody id="rbbLapBody"></tbody>
      </table>
    </div>

    <div id="rbbLapMobileList" class="rbb-mobile-list"></div>
  </section>
</div>

<div id="rbbLapInfoModal" class="rbb-info-modal hidden" role="dialog" aria-modal="true" aria-labelledby="rbbLapInfoTitle">
  <div class="rbb-info-card">
    <div class="rbb-info-head">
      <div class="min-w-0">
        <div class="rbb-info-kicker">Pusat Keputusan Kepala Cabang &amp; Direksi</div>
        <h2 id="rbbLapInfoTitle">Analisis RBB vs Realisasi</h2>
        <p id="rbbLapInfoSubtitle">Ringkasan kondisi dan prioritas tindakan.</p>
      </div>
      <button type="button" onclick="toggleRbbLapInfo(false)" class="rbb-info-close" title="Tutup" aria-label="Tutup">&times;</button>
    </div>
    <div id="rbbLapInfoBody" class="rbb-info-body custom-scrollbar"></div>
  </div>
</div>

<style>
  #rbbLapPage {
    --header-h-1: 43px;
    --header-h-2: 38px;
    height: calc(100dvh - 60px);
    min-height: 460px;
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    overflow: hidden;
  }

  .rbb-page-header {
    flex: 0 0 auto;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 14px;
    padding: 10px 12px;
    border: 1px solid #dbe3ee;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 1px 3px rgba(15,23,42,.05);
  }

  .rbb-brand-row { display:flex; align-items:center; gap:10px; min-width:0; }
  .rbb-brand-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex:0 0 auto; background:linear-gradient(145deg,#2563eb,#1d4ed8); color:#fff; box-shadow:0 7px 18px rgba(37,99,235,.22); }
  .rbb-brand-icon svg { width:20px; height:20px; }
  .rbb-brand-copy { min-width:0; }
  .rbb-title-row { display:flex; align-items:center; gap:7px; min-width:0; }
  .rbb-title-row h1 { margin:0; color:#0f172a; font-size:20px; line-height:1.1; font-weight:950; letter-spacing:-.025em; white-space:nowrap; }
  .rbb-brand-copy p { margin:3px 0 0; max-width:410px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#64748b; font-size:10px; line-height:1.2; font-weight:700; }

  .rbb-info-btn { width:21px; height:21px; border-radius:999px; border:1px solid #bfdbfe; background:#eff6ff; color:#2563eb; font-size:11px; line-height:1; font-weight:950; display:inline-flex; align-items:center; justify-content:center; flex:0 0 auto; cursor:pointer; transition:.18s ease; }
  .rbb-info-btn:hover { transform:translateY(-1px); background:#dbeafe; border-color:#93c5fd; }

  .rbb-filter-toggle { display:none; height:32px; padding:0 10px; margin-left:auto; align-items:center; justify-content:center; gap:5px; border:1px solid #dbe3ee; border-radius:9px; background:#fff; color:#334155; font-size:10px; font-weight:900; cursor:pointer; }
  .rbb-filter-toggle svg { width:14px; height:14px; }

  .rbb-filter-form { display:grid; grid-template-columns:145px 145px minmax(210px,260px) 42px; align-items:end; gap:8px; min-width:0; }
  #rbbLapPage .field { display:flex; flex-direction:column; gap:3px; min-width:0; }
  #rbbLapPage .field > span { margin-left:2px; color:#475569; font-size:8px; line-height:1; font-weight:950; letter-spacing:.08em; text-transform:uppercase; white-space:nowrap; }
  #rbbLapPage .control { width:100%; min-width:0; height:36px; padding:0 10px; border:1px solid #cbd5e1; border-radius:9px; outline:none; background:#fff; color:#0f172a; font-size:11px; font-weight:800; transition:.18s ease; }
  #rbbLapPage .control:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.11); }
  #rbbLapPage .control:disabled { background:#f8fafc; color:#64748b; cursor:not-allowed; }
  .rbb-excel-btn { width:42px; height:36px; border:0; border-radius:9px; display:flex; align-items:center; justify-content:center; background:#059669; color:#fff; cursor:pointer; box-shadow:0 4px 10px rgba(5,150,105,.18); transition:.18s ease; }
  .rbb-excel-btn:hover { transform:translateY(-1px); background:#047857; }
  .rbb-excel-btn svg { width:17px; height:17px; }

  .rbb-workspace { position:relative; flex:1 1 auto; min-height:0; display:flex; flex-direction:column; overflow:hidden; border:1px solid #dbe3ee; border-radius:14px; background:#fff; box-shadow:0 1px 3px rgba(15,23,42,.04); }
  .rbb-loading { position:absolute; inset:0; z-index:100; align-items:center; justify-content:center; gap:10px; background:rgba(255,255,255,.82); color:#1d4ed8; font-size:10px; font-weight:950; letter-spacing:.1em; text-transform:uppercase; backdrop-filter:blur(4px); }
  .rbb-loading:not(.hidden) { display:flex; }
  .rbb-spinner { width:32px; height:32px; border:4px solid #dbeafe; border-top-color:#2563eb; border-radius:999px; animation:rbbSpin .8s linear infinite; }
  @keyframes rbbSpin { to { transform:rotate(360deg); } }

  .rbb-toolbar { flex:0 0 auto; min-height:48px; display:flex; align-items:center; gap:8px; padding:7px 9px; border-bottom:1px solid #e2e8f0; background:#fbfdff; }
  .rbb-search-wrap { position:relative; flex:1 1 420px; max-width:620px; min-width:180px; }
  .rbb-search-wrap > svg { position:absolute; left:10px; top:50%; width:15px; height:15px; transform:translateY(-50%); color:#94a3b8; pointer-events:none; }
  .rbb-search-wrap input { width:100%; height:34px; padding:0 34px 0 32px; border:1px solid #cbd5e1; border-radius:9px; outline:none; background:#fff; color:#0f172a; font-size:11px; font-weight:750; }
  .rbb-search-wrap input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.09); }
  .rbb-search-clear { position:absolute; right:5px; top:50%; transform:translateY(-50%); width:25px; height:25px; border:0; border-radius:7px; background:#f1f5f9; color:#64748b; font-size:18px; line-height:1; cursor:pointer; }
  .rbb-category-filter { flex:0 0 170px; height:34px; padding:0 9px; border:1px solid #cbd5e1; border-radius:9px; background:#fff; color:#334155; font-size:10px; font-weight:850; outline:none; }
  .rbb-result-meta { margin-left:auto; flex:0 0 auto; min-width:85px; color:#64748b; font-size:10px; font-weight:850; text-align:right; white-space:nowrap; }

  .rbb-quick-info { flex:0 0 auto; display:flex; align-items:center; gap:6px; min-height:34px; padding:5px 9px; overflow-x:auto; border-bottom:1px solid #e2e8f0; background:#fff; scrollbar-width:none; }
  .rbb-quick-info::-webkit-scrollbar { display:none; }
  .rbb-quick-chip { display:inline-flex; align-items:center; gap:4px; min-height:23px; padding:3px 8px; border:1px solid #e2e8f0; border-radius:999px; background:#f8fafc; color:#64748b; font-size:9px; font-weight:750; white-space:nowrap; }
  .rbb-quick-chip b { color:#334155; font-weight:950; }
  .rbb-quick-attention { border-color:#fed7aa; background:#fff7ed; color:#c2410c; }
  .rbb-quick-attention b { color:#9a3412; }

  .rbb-table-shell { flex:1 1 auto; min-height:0; overflow:auto; background:#fff; }
  #rbbLapTable { width:100%; min-width:1060px; border-collapse:separate; border-spacing:0; table-layout:fixed; color:#334155; }
  #rbbLapTable .col-code { width:68px; }
  #rbbLapTable .col-name { width:260px; }
  #rbbLapTable .col-money { width:120px; }
  #rbbLapTable .col-pct { width:90px; }
  #rbbLapTable .th { position:sticky; top:0; z-index:20; height:var(--header-h-1); padding:8px; border-right:1px solid rgba(203,213,225,.7); border-bottom:1px solid #cbd5e1; background:#eff6ff; color:#1e3a8a; font-size:9px; line-height:1.15; font-weight:950; letter-spacing:.045em; text-transform:uppercase; white-space:nowrap; }
  #rbbLapTable thead tr:nth-child(2) .th { top:var(--header-h-1); height:var(--header-h-2); }
  #rbbLapTable .th-rbb { background:#fff7ed; color:#9a3412; border-top:3px solid #f97316; }
  #rbbLapTable .th-rbb-sub { background:#ffedd5; color:#9a3412; }
  #rbbLapTable .th-real { background:#ecfdf5; color:#047857; border-top:3px solid #10b981; }
  #rbbLapTable .th-real-sub { background:#d1fae5; color:#065f46; }
  #rbbLapTable .th-result { background:#eff6ff; color:#1d4ed8; border-top:3px solid #3b82f6; }
  #rbbLapTable .th-result-sub { background:#dbeafe; color:#1e40af; }
  #rbbLapTable .th-year { background:#f5f3ff; color:#6d28d9; border-top:3px solid #8b5cf6; }
  #rbbLapTable .th-year-sub { background:#ede9fe; color:#5b21b6; }
  #rbbLapTable .sticky-code { left:0; z-index:45; }
  #rbbLapTable .sticky-name { left:68px; z-index:44; box-shadow:4px 0 8px -8px rgba(15,23,42,.6); }

  #rbbLapTable tbody td { height:43px; padding:7px 8px; border-right:1px solid #eef2f7; border-bottom:1px solid #eef2f7; background:#fff; color:#334155; font-size:10px; font-weight:750; vertical-align:middle; white-space:nowrap; }
  #rbbLapTable tbody tr:nth-child(even) td { background:#fbfdff; }
  #rbbLapTable tbody tr:hover td { background:#f1f7ff; }
  #rbbLapTable tbody td.sticky-code-cell { position:sticky; left:0; z-index:10; text-align:center; font-family:'JetBrains Mono',ui-monospace,monospace; color:#64748b; }
  #rbbLapTable tbody td.sticky-name-cell { position:sticky; left:68px; z-index:9; box-shadow:4px 0 8px -8px rgba(15,23,42,.55); }
  #rbbLapTable .row-name { overflow:hidden; text-overflow:ellipsis; color:#0f172a; font-size:10.5px; font-weight:900; }
  #rbbLapTable .row-meta { margin-top:2px; overflow:hidden; text-overflow:ellipsis; color:#94a3b8; font-size:8px; font-weight:800; }
  .num { font-family:'JetBrains Mono',ui-monospace,monospace; font-variant-numeric:tabular-nums; }
  .text-right { text-align:right; }
  .text-left { text-align:left; }
  .text-center { text-align:center; }
  .trend-good { color:#047857 !important; }
  .trend-warn { color:#b45309 !important; }
  .trend-bad { color:#dc2626 !important; }
  .trend-neutral { color:#64748b !important; }
  .rbb-pct-pill { display:inline-flex; min-width:65px; min-height:24px; align-items:center; justify-content:center; gap:4px; padding:3px 7px; border:1px solid currentColor; border-radius:999px; font-size:9px; font-weight:950; background:#fff; }
  .rbb-row-status { display:inline-flex; align-items:center; min-height:18px; padding:2px 5px; border-radius:999px; font-size:7px; font-weight:950; letter-spacing:.03em; text-transform:uppercase; }
  .rbb-row-status.good { background:#dcfce7; color:#047857; }
  .rbb-row-status.warn { background:#fef3c7; color:#a16207; }
  .rbb-row-status.bad { background:#fee2e2; color:#b91c1c; }
  .rbb-row-status.neutral { background:#f1f5f9; color:#64748b; }
  .rbb-empty { padding:45px 16px !important; text-align:center; color:#64748b !important; font-size:11px !important; font-weight:850 !important; }

  .rbb-mobile-list { display:none; flex:1 1 auto; min-height:0; overflow:auto; padding:7px; background:#f8fafc; }
  .rbb-mobile-card { margin-bottom:7px; overflow:hidden; border:1px solid #dbe3ee; border-radius:11px; background:#fff; box-shadow:0 1px 2px rgba(15,23,42,.04); }
  .rbb-mobile-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; padding:9px; border-bottom:1px solid #eef2f7; }
  .rbb-mobile-card-title { min-width:0; }
  .rbb-mobile-card-title h3 { margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#0f172a; font-size:10.5px; font-weight:950; }
  .rbb-mobile-card-title p { margin:3px 0 0; color:#94a3b8; font-size:7.5px; font-weight:800; }
  .rbb-mobile-card-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1px; background:#e8eef6; }
  .rbb-mobile-metric { min-width:0; padding:8px 9px; background:#fff; }
  .rbb-mobile-metric span { display:block; color:#94a3b8; font-size:7px; font-weight:950; letter-spacing:.05em; text-transform:uppercase; }
  .rbb-mobile-metric b { display:block; margin-top:3px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#334155; font-size:9px; font-weight:950; font-family:'JetBrains Mono',ui-monospace,monospace; }
  .rbb-mobile-card-foot { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 9px; background:#fbfdff; }
  .rbb-mobile-card-foot small { color:#64748b; font-size:7.5px; font-weight:800; }

  .rbb-info-modal { position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:14px; background:rgba(15,23,42,.58); backdrop-filter:blur(5px); }
  .rbb-info-modal:not(.hidden) { display:flex; }
  .rbb-info-card { width:min(1120px,97vw); max-height:92dvh; display:flex; flex-direction:column; overflow:hidden; border:1px solid #dbeafe; border-radius:18px; background:#f8fafc; box-shadow:0 28px 85px rgba(15,23,42,.34); }
  .rbb-info-head { flex:0 0 auto; display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding:15px 17px; border-bottom:1px solid #dbe3ee; background:#fff; }
  .rbb-info-kicker { color:#2563eb; font-size:8px; font-weight:950; letter-spacing:.11em; text-transform:uppercase; }
  .rbb-info-head h2 { margin:3px 0 0; color:#0f172a; font-size:20px; line-height:1.15; font-weight:950; letter-spacing:-.02em; }
  .rbb-info-head p { margin:4px 0 0; color:#64748b; font-size:9px; line-height:1.4; font-weight:750; }
  .rbb-info-close { width:34px; height:34px; flex:0 0 auto; border:0; border-radius:999px; background:#f1f5f9; color:#475569; font-size:22px; line-height:1; cursor:pointer; }
  .rbb-info-close:hover { background:#fee2e2; color:#dc2626; }
  .rbb-info-body { flex:1 1 auto; min-height:0; overflow:auto; padding:12px; }
  .rbb-insight-summary { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:8px; margin-bottom:9px; }
  .rbb-insight-stat { min-width:0; padding:10px; border:1px solid #dbe3ee; border-radius:12px; background:#fff; }
  .rbb-insight-stat span { display:block; color:#64748b; font-size:7.5px; font-weight:950; letter-spacing:.06em; text-transform:uppercase; }
  .rbb-insight-stat b { display:block; margin-top:5px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#0f172a; font-size:15px; font-weight:950; font-family:'JetBrains Mono',ui-monospace,monospace; }
  .rbb-insight-stat small { display:block; margin-top:4px; color:#64748b; font-size:8px; line-height:1.35; font-weight:750; }
  .rbb-insight-stat.good { border-color:#a7f3d0; background:#f0fdf4; }
  .rbb-insight-stat.warn { border-color:#fde68a; background:#fffbeb; }
  .rbb-insight-stat.bad { border-color:#fecaca; background:#fff7f7; }
  .rbb-insight-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:9px; }
  .rbb-insight-block { min-width:0; padding:11px; border:1px solid #dbe3ee; border-radius:12px; background:#fff; }
  .rbb-insight-block.full { grid-column:1 / -1; }
  .rbb-insight-block h3 { margin:0 0 8px; color:#0f172a; font-size:10px; font-weight:950; }
  .rbb-insight-block p { margin:0; color:#475569; font-size:9px; line-height:1.55; font-weight:720; }
  .rbb-insight-list { margin:0; padding-left:17px; color:#334155; font-size:9px; line-height:1.55; font-weight:750; }
  .rbb-insight-list li + li { margin-top:5px; }
  .rbb-priority-list { display:grid; gap:7px; }
  .rbb-priority-card { display:grid; grid-template-columns:42px minmax(0,1fr) 170px; gap:9px; align-items:start; padding:9px; border:1px solid #e2e8f0; border-radius:10px; background:#fbfdff; }
  .rbb-priority-rank { width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:9px; background:#fee2e2; color:#b91c1c; font-size:9px; font-weight:950; }
  .rbb-priority-main h4 { margin:0; color:#0f172a; font-size:9.5px; font-weight:950; }
  .rbb-priority-main p { margin:4px 0 0; color:#475569; font-size:8.5px; line-height:1.45; font-weight:720; }
  .rbb-priority-meta { color:#64748b; font-size:8px; line-height:1.45; font-weight:750; }
  .rbb-priority-meta b { color:#334155; }
  .rbb-insight-table-wrap { overflow:auto; border:1px solid #e2e8f0; border-radius:9px; }
  .rbb-insight-table { width:100%; min-width:650px; border-collapse:collapse; }
  .rbb-insight-table th, .rbb-insight-table td { padding:7px 8px; border-bottom:1px solid #eef2f7; text-align:left; color:#334155; font-size:8px; font-weight:750; }
  .rbb-insight-table th { background:#f8fafc; color:#475569; font-size:7px; font-weight:950; letter-spacing:.04em; text-transform:uppercase; }
  .rbb-insight-table td.num, .rbb-insight-table th.num { text-align:right; }
  .rbb-question-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px; }
  .rbb-question { display:flex; gap:7px; align-items:flex-start; padding:8px; border:1px solid #dbe3ee; border-radius:9px; background:#f8fafc; color:#334155; font-size:8px; line-height:1.4; font-weight:800; }
  .rbb-question b { width:20px; height:20px; flex:0 0 auto; display:flex; align-items:center; justify-content:center; border-radius:6px; background:#dbeafe; color:#1d4ed8; font-size:7px; }

  .custom-scrollbar::-webkit-scrollbar { width:6px; height:6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background:#f1f5f9; border-radius:999px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background:#94a3b8; }

  @media (min-width:768px) {
    #rbbLapPage { height:calc(100dvh - 80px); padding:12px; gap:10px; }
  }

  @media (min-width:1280px) {
    #rbbLapTable { min-width:100%; }
  }

  @media (max-width:1100px) {
    .rbb-page-header { align-items:stretch; flex-direction:column; gap:9px; }
    .rbb-filter-form { width:100%; grid-template-columns:150px 150px minmax(200px,1fr) 42px; }
    .rbb-insight-summary { grid-template-columns:repeat(3,minmax(0,1fr)); }
  }

  @media (max-width:767px) {
    #rbbLapPage { height:calc(100dvh - 60px); min-height:0; padding:6px; gap:6px; }
    .rbb-page-header { padding:7px; border-radius:11px; }
    .rbb-brand-row { width:100%; gap:7px; }
    .rbb-brand-icon { width:30px; height:30px; border-radius:8px; }
    .rbb-brand-icon svg { width:16px; height:16px; }
    .rbb-title-row h1 { font-size:12px; }
    .rbb-brand-copy p { max-width:185px; margin-top:2px; font-size:7px; }
    .rbb-info-btn { width:17px; height:17px; font-size:8px; }
    .rbb-filter-toggle { display:inline-flex; }

    .rbb-filter-form { display:none; width:100%; grid-template-columns:repeat(2,minmax(0,1fr)); gap:5px; padding-top:7px; border-top:1px solid #eef2f7; }
    .rbb-filter-form.is-open { display:grid; }
    .rbb-field-office { grid-column:1 / -1; }
    #rbbLapPage .field > span { font-size:6.5px; }
    #rbbLapPage .control { height:31px; padding:0 7px; border-radius:7px; font-size:8.5px; }
    .rbb-excel-btn { width:32px; height:31px; border-radius:7px; justify-self:end; grid-column:2; }
    .rbb-excel-btn svg { width:14px; height:14px; }

    .rbb-workspace { border-radius:11px; }
    .rbb-toolbar { min-height:41px; gap:5px; padding:5px; }
    .rbb-search-wrap { flex:1 1 auto; min-width:0; }
    .rbb-search-wrap input { height:31px; padding-left:29px; font-size:8.5px; }
    .rbb-search-wrap > svg { left:8px; width:13px; height:13px; }
    .rbb-category-filter { flex:0 0 102px; width:102px; height:31px; padding:0 5px; font-size:7.5px; }
    .rbb-result-meta { display:none; }
    .rbb-quick-info { min-height:29px; padding:4px 5px; gap:4px; }
    .rbb-quick-chip { min-height:20px; padding:2px 6px; font-size:6.8px; }

    .rbb-table-shell { display:none; }
    .rbb-mobile-list { display:block; }

    .rbb-info-modal { align-items:flex-end; padding:0; }
    .rbb-info-card { width:100%; max-height:94dvh; border-radius:17px 17px 0 0; }
    .rbb-info-head { padding:11px; }
    .rbb-info-kicker { font-size:6px; }
    .rbb-info-head h2 { font-size:14px; }
    .rbb-info-head p { font-size:7px; }
    .rbb-info-close { width:30px; height:30px; font-size:20px; }
    .rbb-info-body { padding:7px 7px calc(8px + env(safe-area-inset-bottom)); }
    .rbb-insight-summary { grid-template-columns:repeat(2,minmax(0,1fr)); gap:5px; margin-bottom:6px; }
    .rbb-insight-stat { padding:7px; border-radius:9px; }
    .rbb-insight-stat span { font-size:6px; }
    .rbb-insight-stat b { font-size:11px; }
    .rbb-insight-stat small { font-size:6.5px; }
    .rbb-insight-grid { grid-template-columns:1fr; gap:6px; }
    .rbb-insight-block, .rbb-insight-block.full { grid-column:auto; padding:8px; border-radius:9px; }
    .rbb-insight-block h3 { font-size:8.5px; }
    .rbb-insight-block p, .rbb-insight-list { font-size:7.5px; }
    .rbb-priority-card { grid-template-columns:32px minmax(0,1fr); gap:7px; }
    .rbb-priority-rank { width:29px; height:29px; font-size:7px; }
    .rbb-priority-main h4 { font-size:8px; }
    .rbb-priority-main p { font-size:7px; }
    .rbb-priority-meta { grid-column:2; font-size:6.8px; }
    .rbb-question-grid { grid-template-columns:1fr; }
    .rbb-question { font-size:7px; }
    .rbb-insight-table { min-width:560px; }
  }

  @media (max-width:380px) {
    .rbb-brand-copy p { max-width:145px; }
    .rbb-category-filter { flex-basis:92px; width:92px; }
  }
</style>

<script>
const RBB_LAP_API = './api/rbb/';
const RBB_LAP_KODE_API = './api/kode/';
const RBB_LAP_DATE_API = './api/date/';
const rbbLapFmt = new Intl.NumberFormat('id-ID');
const rbbLapPctFmt = new Intl.NumberFormat('id-ID', {minimumFractionDigits:2, maximumFractionDigits:2});
let rbbLapRows = [];
let rbbLapFilteredRows = [];
let rbbLapSummary = {};
let rbbLapMeta = {};
let rbbLapCategorySummary = [];
let rbbLapTimer = null;

function rbbLapFetch(url, opt = {}) {
  return window.apiFetch ? window.apiFetch(url, opt) : fetch(url, opt);
}

function rbbLapUserKode() {
  const user = (window.getUser && window.getUser()) || {};
  const raw = user.kode || user.kode_kantor || user.kode_cabang || user.branch_code || '000';
  const kode = String(raw).replace(/\D/g, '').padStart(3, '0').slice(-3);
  return kode === '099' ? '000' : kode;
}

function esc(v) {
  return String(v ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[ch]));
}

function toNum(value) {
  if (typeof value === 'number') return Number.isFinite(value) ? value : 0;
  const raw = String(value ?? '').trim();
  if (!raw) return 0;
  if (/^-?\d+(\.\d+)?$/.test(raw)) return Number(raw) || 0;
  let normalized = raw.replace(/\s/g, '').replace(/[^0-9,().-]/g, '');
  const negativeByParen = normalized.startsWith('(') && normalized.endsWith(')');
  normalized = normalized.replace(/[()]/g, '');
  if (normalized.includes(',') && normalized.includes('.')) normalized = normalized.replace(/\./g, '').replace(',', '.');
  else if (normalized.includes(',')) normalized = normalized.replace(',', '.');
  const parsed = Number(normalized) || 0;
  return negativeByParen ? -Math.abs(parsed) : parsed;
}

function shortRp(v) {
  const n = toNum(v), a = Math.abs(n), sign = n < 0 ? '-' : '';
  if (a >= 1e12) return sign + rbbLapPctFmt.format(a / 1e12) + ' T';
  if (a >= 1e9) return sign + rbbLapPctFmt.format(a / 1e9) + ' M';
  if (a >= 1e6) return sign + rbbLapPctFmt.format(a / 1e6) + ' Jt';
  if (a >= 1e3) return sign + rbbLapPctFmt.format(a / 1e3) + ' Rb';
  return sign + rbbLapFmt.format(Math.round(a));
}

function tableNom(v) {
  return rbbLapFmt.format(Math.round(toNum(v) / 1000));
}

function pct(v) {
  return rbbLapPctFmt.format(toNum(v)) + '%';
}

function selectedOfficeLabel() {
  const el = document.getElementById('rbbLapKantor');
  return el?.selectedOptions?.[0]?.textContent?.trim() || '-';
}

function formatDateID(value) {
  if (!value) return '-';
  const date = new Date(`${String(value).slice(0,10)}T00:00:00`);
  if (Number.isNaN(date.getTime())) return String(value);
  return new Intl.DateTimeFormat('id-ID', {day:'2-digit', month:'short', year:'numeric'}).format(date);
}

function isExpenseRow(row) {
  const category = String(row?.kategori || '').trim().toUpperCase();
  const name = String(row?.keterangan || '').toLowerCase();
  return category === 'BEBAN' || /\b(beban|biaya)\b/.test(name);
}

function rowAssessment(row, yearEnd = false) {
  const target = toNum(yearEnd ? row?.target_rbb_year_end : row?.target_rbb);
  const actual = toNum(row?.realisasi_actual);
  const percentage = toNum(yearEnd ? row?.pencapaian_year_end_persen : row?.pencapaian_persen);
  const expense = isExpenseRow(row);

  if (target === 0) return {tone:'neutral', label:'Tanpa target', favorable:true};

  if (expense) {
    if (actual <= target) return {tone:'good', label:'Terkendali', favorable:true};
    if (actual <= target * 1.05) return {tone:'warn', label:'Melebihi tipis', favorable:false};
    return {tone:'bad', label:'Beban melampaui', favorable:false};
  }

  if (percentage >= 100) return {tone:'good', label:'Tercapai', favorable:true};
  if (percentage >= 85) return {tone:'warn', label:'Perlu dorongan', favorable:false};
  return {tone:'bad', label:'Prioritas', favorable:false};
}

function gapForPriority(row) {
  const target = toNum(row?.target_rbb);
  const actual = toNum(row?.realisasi_actual);
  return isExpenseRow(row) ? Math.max(actual - target, 0) : Math.max(target - actual, 0);
}

function toggleRbbLapInfo(show) {
  const modal = document.getElementById('rbbLapInfoModal');
  if (!modal) return;
  if (show) renderRbbLapInfo();
  modal.classList.toggle('hidden', !show);
  document.body.style.overflow = show ? 'hidden' : '';
  if (show) document.getElementById('rbbLapInfoBody')?.scrollTo({top:0});
}

function scheduleRbbLapFetch(delay = 500) {
  clearTimeout(rbbLapTimer);
  rbbLapTimer = setTimeout(() => fetchRbbLap(), delay);
}

async function initRbbLap() {
  bindRbbLapEvents();
  await Promise.all([loadRbbLapDate(), loadRbbLapKantor()]);
  fetchRbbLap();
}

function bindRbbLapEvents() {
  ['rbbLapJenis','rbbLapHarian','rbbLapKantor'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', () => {
      if (window.innerWidth < 768) setRbbLapFilterOpen(false);
      scheduleRbbLapFetch();
    });
  });

  document.getElementById('rbbLapSearch')?.addEventListener('input', () => {
    clearTimeout(rbbLapTimer);
    rbbLapTimer = setTimeout(applyRbbLapLocalFilter, 160);
  });

  document.getElementById('rbbLapCategoryFilter')?.addEventListener('change', applyRbbLapLocalFilter);
  document.getElementById('rbbLapSearchClear')?.addEventListener('click', () => {
    const input = document.getElementById('rbbLapSearch');
    if (input) input.value = '';
    applyRbbLapLocalFilter();
    input?.focus();
  });

  document.getElementById('rbbLapFilterToggle')?.addEventListener('click', () => {
    const form = document.getElementById('rbbLapFilter');
    setRbbLapFilterOpen(!form?.classList.contains('is-open'));
  });

  document.getElementById('rbbLapInfoModal')?.addEventListener('click', event => {
    if (event.target.id === 'rbbLapInfoModal') toggleRbbLapInfo(false);
  });

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') toggleRbbLapInfo(false);
  });
}

function setRbbLapFilterOpen(open) {
  const form = document.getElementById('rbbLapFilter');
  const button = document.getElementById('rbbLapFilterToggle');
  form?.classList.toggle('is-open', !!open);
  button?.setAttribute('aria-expanded', open ? 'true' : 'false');
  const label = button?.querySelector('span');
  if (label) label.textContent = open ? 'Tutup' : 'Filter';
}

async function loadRbbLapDate() {
  const el = document.getElementById('rbbLapHarian');
  try {
    const res = await rbbLapFetch(RBB_LAP_DATE_API, {cache:'no-store'});
    const json = await res.json();
    const data = json.data || json || {};
    el.value = data.last_created || data.harian_date || new Date().toISOString().slice(0,10);
  } catch (error) {
    el.value = new Date().toISOString().slice(0,10);
  }
}

async function loadRbbLapKantor() {
  const select = document.getElementById('rbbLapKantor');
  const userKode = rbbLapUserKode();
  let html = `<option value="000">Konsolidasi</option><option value="SEMARANG">Korwil Semarang</option><option value="SOLO">Korwil Solo</option><option value="BANYUMAS">Korwil Banyumas</option><option value="PEKALONGAN">Korwil Pekalongan</option>`;
  try {
    const res = await rbbLapFetch(RBB_LAP_KODE_API, {
      method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'})
    });
    const json = await res.json();
    (json.data || [])
      .filter(item => item.kode_kantor && item.kode_kantor !== '000')
      .sort((a,b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor)))
      .forEach(item => {
        const kode = String(item.kode_kantor).padStart(3,'0');
        html += `<option value="${esc(kode)}">${esc(kode)} - ${esc(item.nama_kantor || '')}</option>`;
      });
  } catch (error) {}
  select.innerHTML = html;
  if (userKode !== '000') {
    select.value = userKode;
    select.disabled = true;
  }
}

function buildRbbLapPayload() {
  const kantor = document.getElementById('rbbLapKantor').value;
  const payload = {
    type:'lapkeu_rbb_vs_realisasi',
    jenis_laporan:document.getElementById('rbbLapJenis').value,
    harian_date:document.getElementById('rbbLapHarian').value,
    use_year_end:false
  };
  if (['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].includes(kantor)) payload.korwil = kantor;
  else payload.kode_kantor = kantor;
  return payload;
}

async function fetchRbbLap() {
  const loader = document.getElementById('rbbLapLoading');
  loader?.classList.remove('hidden');
  try {
    const res = await rbbLapFetch(RBB_LAP_API, {
      method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(buildRbbLapPayload())
    });
    const json = await res.json();
    if (!res.ok || Number(json.status) >= 400) throw new Error(json.message || 'Gagal memuat data');

    rbbLapRows = Array.isArray(json.data?.data) ? json.data.data : (Array.isArray(json.data) ? json.data : []);
    rbbLapSummary = json.data?.summary || {};
    rbbLapMeta = json.data?.meta || {};
    rbbLapCategorySummary = Array.isArray(json.data?.category_summary) ? json.data.category_summary : [];

    populateRbbLapCategories();
    applyRbbLapLocalFilter();
    updateRbbLapQuickInfo();
  } catch (error) {
    rbbLapRows = [];
    rbbLapFilteredRows = [];
    const message = esc(error.message || 'Gagal memuat data');
    document.getElementById('rbbLapBody').innerHTML = `<tr><td colspan="9" class="rbb-empty trend-bad">${message}</td></tr>`;
    document.getElementById('rbbLapMobileList').innerHTML = `<div class="rbb-empty trend-bad">${message}</div>`;
  } finally {
    loader?.classList.add('hidden');
  }
}

function populateRbbLapCategories() {
  const select = document.getElementById('rbbLapCategoryFilter');
  const previous = select?.value || 'ALL';
  const categories = [...new Set(rbbLapRows.map(row => String(row.kategori || '').trim()).filter(Boolean))]
    .sort((a,b) => a.localeCompare(b, 'id'));
  if (!select) return;
  select.innerHTML = `<option value="ALL">Semua Kategori</option>` + categories.map(category => `<option value="${esc(category)}">${esc(category)}</option>`).join('');
  select.value = categories.includes(previous) ? previous : 'ALL';
}

function applyRbbLapLocalFilter() {
  const keyword = String(document.getElementById('rbbLapSearch')?.value || '').trim().toLowerCase();
  const category = document.getElementById('rbbLapCategoryFilter')?.value || 'ALL';

  rbbLapFilteredRows = rbbLapRows.filter(row => {
    const matchCategory = category === 'ALL' || String(row.kategori || '') === category;
    const haystack = [row.keterangan,row.kategori,row.kode_perkiraan,row.kode_monbis,row.sandi_lbbpr,row.status_crosscheck]
      .map(value => String(value ?? '').toLowerCase()).join(' ');
    return matchCategory && (!keyword || haystack.includes(keyword));
  });

  document.getElementById('rbbLapSearchClear')?.classList.toggle('hidden', !keyword);
  renderRbbLap();
}

function renderRbbLap() {
  renderRbbLapTable();
  renderRbbLapMobile();
  const total = rbbLapRows.length;
  const shown = rbbLapFilteredRows.length;
  const meta = document.getElementById('rbbLapResultMeta');
  if (meta) meta.textContent = shown === total ? `${total} data` : `${shown} dari ${total} data`;
}

function renderRbbLapTable() {
  const body = document.getElementById('rbbLapBody');
  if (!body) return;
  if (!rbbLapFilteredRows.length) {
    body.innerHTML = `<tr><td colspan="9" class="rbb-empty">Data tidak ditemukan. Coba ubah kata pencarian atau kategori.</td></tr>`;
    return;
  }

  body.innerHTML = rbbLapFilteredRows.map(row => {
    const assessment = rowAssessment(row, false);
    const yearAssessment = rowAssessment(row, true);
    const selisih = toNum(row.selisih);
    const yearSelisih = toNum(row.selisih_year_end);
    const actualGapTone = isExpenseRow(row)
      ? (selisih <= 0 ? 'trend-good' : assessment.tone === 'warn' ? 'trend-warn' : 'trend-bad')
      : (selisih >= 0 ? 'trend-good' : assessment.tone === 'warn' ? 'trend-warn' : 'trend-bad');
    const yearGapTone = isExpenseRow(row)
      ? (yearSelisih <= 0 ? 'trend-good' : yearAssessment.tone === 'warn' ? 'trend-warn' : 'trend-bad')
      : (yearSelisih >= 0 ? 'trend-good' : yearAssessment.tone === 'warn' ? 'trend-warn' : 'trend-bad');

    return `
      <tr>
        <td class="sticky-code-cell num">${esc(row.kode_monbis || row.id_ref || '-')}</td>
        <td class="sticky-name-cell">
          <div class="row-name" title="${esc(row.keterangan)}">${esc(row.keterangan || '-')}</div>
          <div class="row-meta">${esc(row.kategori || '-')}${row.kode_perkiraan ? ' / ' + esc(row.kode_perkiraan) : ''} &nbsp; <span class="rbb-row-status ${assessment.tone}">${esc(assessment.label)}</span></div>
        </td>
        <td class="text-right num trend-neutral">${tableNom(row.target_rbb)}</td>
        <td class="text-right num trend-neutral">${tableNom(row.target_rbb_year_end)}</td>
        <td class="text-right num trend-good">${tableNom(row.realisasi_actual)}</td>
        <td class="text-right num ${actualGapTone}">${tableNom(selisih)}</td>
        <td class="text-right"><span class="rbb-pct-pill trend-${assessment.tone}">${pct(row.pencapaian_persen)}</span></td>
        <td class="text-right num ${yearGapTone}">${tableNom(yearSelisih)}</td>
        <td class="text-right"><span class="rbb-pct-pill trend-${yearAssessment.tone}">${pct(row.pencapaian_year_end_persen)}</span></td>
      </tr>`;
  }).join('');
}

function renderRbbLapMobile() {
  const list = document.getElementById('rbbLapMobileList');
  if (!list) return;
  if (!rbbLapFilteredRows.length) {
    list.innerHTML = `<div class="rbb-empty">Data tidak ditemukan. Coba ubah pencarian.</div>`;
    return;
  }

  list.innerHTML = rbbLapFilteredRows.map(row => {
    const assessment = rowAssessment(row, false);
    const expense = isExpenseRow(row);
    const gap = toNum(row.selisih);
    const gapTone = expense ? (gap <= 0 ? 'trend-good' : 'trend-bad') : (gap >= 0 ? 'trend-good' : 'trend-bad');
    return `
      <article class="rbb-mobile-card">
        <div class="rbb-mobile-card-head">
          <div class="rbb-mobile-card-title">
            <h3 title="${esc(row.keterangan)}">${esc(row.keterangan || '-')}</h3>
            <p>${esc(row.kategori || '-')} · Kode ${esc(row.kode_monbis || row.id_ref || '-')} ${row.kode_perkiraan ? '· Akun ' + esc(row.kode_perkiraan) : ''}</p>
          </div>
          <span class="rbb-row-status ${assessment.tone}">${esc(assessment.label)}</span>
        </div>
        <div class="rbb-mobile-card-grid">
          <div class="rbb-mobile-metric"><span>RBB Periode</span><b>${tableNom(row.target_rbb)}</b></div>
          <div class="rbb-mobile-metric"><span>Realisasi</span><b class="trend-good">${tableNom(row.realisasi_actual)}</b></div>
          <div class="rbb-mobile-metric"><span>Selisih</span><b class="${gapTone}">${tableNom(gap)}</b></div>
          <div class="rbb-mobile-metric"><span>Capaian</span><b class="trend-${assessment.tone}">${pct(row.pencapaian_persen)}</b></div>
        </div>
        <div class="rbb-mobile-card-foot">
          <small>RBB Desember: <b>${tableNom(row.target_rbb_year_end)}</b></small>
          <small>Capaian Desember: <b class="trend-${rowAssessment(row,true).tone}">${pct(row.pencapaian_year_end_persen)}</b></small>
        </div>
      </article>`;
  }).join('');
}

function updateRbbLapQuickInfo() {
  const date = document.getElementById('rbbLapHarian')?.value || rbbLapMeta.actual_date || '-';
  const attention = rbbLapRows.filter(row => ['warn','bad'].includes(rowAssessment(row).tone)).length;
  document.getElementById('rbbLapQuickDate').textContent = formatDateID(date);
  document.getElementById('rbbLapQuickScope').textContent = rbbLapMeta.scope || selectedOfficeLabel();
  document.getElementById('rbbLapQuickAttention').textContent = `${attention} akun`;
}

function representativeTotalRow() {
  const reportType = document.getElementById('rbbLapJenis')?.value || 'neraca';
  const candidates = rbbLapRows.filter(row => {
    const name = String(row.keterangan || '').toLowerCase();
    const code = String(row.kode_perkiraan || '').replace(/\s/g, '');
    if (reportType === 'neraca') return code === '1' || /total\s+aset/.test(name);
    return String(row.kode_monbis || '') === '261' || /laba.*sebelum pajak|total\s+pendapatan|total\s+beban/.test(name);
  });
  return candidates[0] || rbbLapRows[0] || {};
}

function summaryValue(keys, fallback = null) {
  for (const key of keys) {
    if (rbbLapSummary?.[key] !== undefined && rbbLapSummary?.[key] !== null && rbbLapSummary?.[key] !== '') {
      return toNum(rbbLapSummary[key]);
    }
  }
  const totalRow = representativeTotalRow();
  const rowKeyMap = {
    target_rbb:'target_rbb', rbb_actual:'target_rbb', target_actual:'target_rbb',
    realisasi_actual:'realisasi_actual', realisasi:'realisasi_actual', actual:'realisasi_actual',
    selisih:'selisih', pencapaian_persen:'pencapaian_persen', persen:'pencapaian_persen'
  };
  for (const key of keys) {
    const rowKey = rowKeyMap[key];
    if (rowKey && totalRow?.[rowKey] !== undefined) return toNum(totalRow[rowKey]);
  }
  return fallback === null ? 0 : fallback;
}

function recommendationFor(row) {
  const category = String(row?.kategori || '').trim().toUpperCase();
  const name = String(row?.keterangan || '').toLowerCase();
  if (category === 'BEBAN' || /\b(beban|biaya)\b/.test(name)) {
    return {
      action:'Bedah transaksi pembentuk beban, tetapkan batas pengeluaran, dan minta PIC menjelaskan deviasi terhadap RBB.',
      pic:'Kepala Cabang, Operasional, pemilik pos biaya',
      deadline:'Maksimal 3 hari kerja; evaluasi mingguan'
    };
  }
  if (category === 'PENDAPATAN') {
    return {
      action:'Percepat pendapatan bunga dan nonbunga melalui penagihan, peningkatan aset produktif, serta pemantauan yield dan fee based income.',
      pic:'Kepala Cabang, Kabid Pemasaran, AO',
      deadline:'Rencana aksi hari ini; evaluasi mingguan'
    };
  }
  if (category === 'ASET') {
    if (/kredit/.test(name)) return {action:'Pisahkan gap kredit menjadi run off, pelunasan, dan kekurangan realisasi. Tetapkan pipeline pencairan berkualitas per AO.', pic:'Kepala Cabang, Kabid Pemasaran, AO Kredit', deadline:'Update pipeline harian'};
    if (/kas|bank|likuid/.test(name)) return {action:'Pastikan kecukupan likuiditas sekaligus mengurangi dana menganggur. Susun proyeksi arus kas dan penempatan.', pic:'Kepala Cabang, Operasional/Treasury', deadline:'Monitoring harian'};
    return {action:'Telusuri akun penyumbang gap aset dan tetapkan sumber pertumbuhan yang tetap menjaga kualitas dan likuiditas.', pic:'Kepala Cabang dan pemilik akun', deadline:'Evaluasi mingguan'};
  }
  if (category === 'LIABILITAS') {
    return {action:'Tingkatkan stabilitas pendanaan melalui retensi nasabah, pipeline dana baru, dan pemantauan deposan besar serta biaya dana.', pic:'Kepala Cabang, AO Dana/CS', deadline:'Daftar prioritas hari ini; evaluasi mingguan'};
  }
  if (category === 'EKUITAS') {
    return {action:'Hubungkan gap ekuitas dengan laba berjalan, pencadangan, dan kebutuhan modal. Pastikan sumber perbaikannya terukur.', pic:'Kepala Cabang, Keuangan, Manajemen', deadline:'Review pada rapat kinerja berikutnya'};
  }
  return {action:'Validasi penyebab gap, tetapkan PIC, target nominal, dan tanggal penyelesaian yang dapat dipantau.', pic:'Kepala Cabang dan unit terkait', deadline:'Maksimal 5 hari kerja'};
}

function buildPriorityRows() {
  return rbbLapRows
    .filter(row => toNum(row.target_rbb) !== 0 && !rowAssessment(row).favorable)
    .map(row => ({row, gap:gapForPriority(row), assessment:rowAssessment(row)}))
    .sort((a,b) => b.gap - a.gap)
    .slice(0,7);
}

function categoryRowsForInsight() {
  if (rbbLapCategorySummary.length) {
    return rbbLapCategorySummary.map(item => ({
      label:item.label || item.kategori || item.nama || '-',
      target:toNum(item.target_rbb ?? item.target),
      actual:toNum(item.realisasi_actual ?? item.realisasi),
      percent:toNum(item.pencapaian_persen ?? item.persen)
    }));
  }
  const map = new Map();
  rbbLapRows.forEach(row => {
    const category = String(row.kategori || 'LAINNYA').trim();
    if (!map.has(category)) map.set(category, {label:category,target:0,actual:0,percent:0,count:0});
    const item = map.get(category);
    item.target += toNum(row.target_rbb);
    item.actual += toNum(row.realisasi_actual);
    item.count++;
  });
  return [...map.values()].map(item => ({...item, percent:item.target ? item.actual / item.target * 100 : 0}));
}

function renderRbbLapInfo() {
  const body = document.getElementById('rbbLapInfoBody');
  if (!body) return;

  const reportLabel = document.getElementById('rbbLapJenis')?.selectedOptions?.[0]?.textContent || 'Laporan';
  const actualDate = document.getElementById('rbbLapHarian')?.value || rbbLapMeta.actual_date || '';
  document.getElementById('rbbLapInfoTitle').textContent = `${reportLabel} - ${selectedOfficeLabel()}`;
  document.getElementById('rbbLapInfoSubtitle').textContent = `Posisi ${formatDateID(actualDate)} · Analisis otomatis untuk menentukan akun yang perlu dikejar, dikendalikan, dan divalidasi.`;

  const target = summaryValue(['target_rbb','rbb_actual','target_actual']);
  const actual = summaryValue(['realisasi_actual','realisasi','actual']);
  const gap = summaryValue(['selisih'], actual - target);
  const achievement = summaryValue(['pencapaian_persen','persen'], target ? actual / target * 100 : 0);
  const priorities = buildPriorityRows();
  const mappingIssues = rbbLapRows.filter(row => {
    const status = String(row.status_crosscheck || '').toUpperCase();
    return status && status !== 'OK';
  });
  const achieved = rbbLapRows
    .filter(row => toNum(row.target_rbb) !== 0 && rowAssessment(row).favorable)
    .sort((a,b) => Math.abs(toNum(b.selisih)) - Math.abs(toNum(a.selisih)))
    .slice(0,5);
  const categories = categoryRowsForInsight();
  const totalTone = achievement >= 100 ? 'good' : achievement >= 85 ? 'warn' : 'bad';

  const priorityHtml = priorities.length ? priorities.map((item,index) => {
    const row = item.row;
    const recommendation = recommendationFor(row);
    return `
      <article class="rbb-priority-card">
        <div class="rbb-priority-rank">P${index + 1}</div>
        <div class="rbb-priority-main">
          <h4>${esc(row.keterangan || '-')} · ${esc(row.kategori || '-')}</h4>
          <p><b>Kondisi:</b> realisasi ${shortRp(row.realisasi_actual)} terhadap RBB ${shortRp(row.target_rbb)}, capaian ${pct(row.pencapaian_persen)}, gap prioritas ${shortRp(item.gap)}.</p>
          <p><b>Keputusan:</b> ${esc(recommendation.action)}</p>
        </div>
        <div class="rbb-priority-meta"><b>PIC:</b> ${esc(recommendation.pic)}<br><b>Tenggat:</b> ${esc(recommendation.deadline)}</div>
      </article>`;
  }).join('') : `<p>Tidak ada akun prioritas material berdasarkan pencapaian RBB periode. Fokus pada keberlanjutan capaian dan pencegahan pemburukan.</p>`;

  const categoryHtml = categories.length ? `
    <div class="rbb-insight-table-wrap custom-scrollbar"><table class="rbb-insight-table">
      <thead><tr><th>Kategori</th><th class="num">RBB</th><th class="num">Realisasi</th><th class="num">Capaian</th><th>Interpretasi</th></tr></thead>
      <tbody>${categories.map(item => {
        const expense = String(item.label).toUpperCase() === 'BEBAN';
        const favorable = item.target === 0 ? true : (expense ? item.actual <= item.target : item.percent >= 100);
        const tone = favorable ? 'trend-good' : item.percent >= 85 ? 'trend-warn' : 'trend-bad';
        const interpretation = expense
          ? (favorable ? 'Beban masih dalam batas RBB.' : 'Beban melampaui RBB dan perlu dikendalikan.')
          : (favorable ? 'Target kategori telah tercapai.' : 'Masih terdapat gap yang perlu dikejar.');
        return `<tr><td>${esc(item.label)}</td><td class="num">${shortRp(item.target)}</td><td class="num">${shortRp(item.actual)}</td><td class="num ${tone}">${pct(item.percent)}</td><td>${esc(interpretation)}</td></tr>`;
      }).join('')}</tbody>
    </table></div>` : '<p>Ringkasan kategori belum tersedia.</p>';

  const achievedHtml = achieved.length ? `
    <div class="rbb-insight-table-wrap custom-scrollbar"><table class="rbb-insight-table">
      <thead><tr><th>Akun</th><th>Kategori</th><th class="num">RBB</th><th class="num">Realisasi</th><th class="num">Capaian</th></tr></thead>
      <tbody>${achieved.map(row => `<tr><td>${esc(row.keterangan || '-')}</td><td>${esc(row.kategori || '-')}</td><td class="num">${shortRp(row.target_rbb)}</td><td class="num">${shortRp(row.realisasi_actual)}</td><td class="num trend-good">${pct(row.pencapaian_persen)}</td></tr>`).join('')}</tbody>
    </table></div>` : '<p>Belum ada akun yang teridentifikasi telah memenuhi sasaran.</p>';

  const mappingHtml = mappingIssues.length
    ? `<ul class="rbb-insight-list">${mappingIssues.slice(0,8).map(row => `<li><b>${esc(row.keterangan || row.kode_monbis || '-')}:</b> ${esc(row.status_crosscheck)}</li>`).join('')}</ul>`
    : '<p>Seluruh baris yang diterima frontend berstatus pemetaan OK.</p>';

  body.innerHTML = `
    <div class="rbb-insight-summary">
      <div class="rbb-insight-stat"><span>RBB Periode</span><b>${shortRp(target)}</b><small>${esc(rbbLapMeta.periode_rbb || 'Periode mengikuti tanggal actual')}</small></div>
      <div class="rbb-insight-stat"><span>Realisasi</span><b>${shortRp(actual)}</b><small>Posisi ${formatDateID(actualDate)}</small></div>
      <div class="rbb-insight-stat ${totalTone}"><span>Capaian</span><b>${pct(achievement)}</b><small>${achievement >= 100 ? 'Target umum tercapai' : 'Masih perlu tindak lanjut'}</small></div>
      <div class="rbb-insight-stat ${gap >= 0 ? 'good' : 'bad'}"><span>Selisih</span><b>${shortRp(gap)}</b><small>Realisasi dikurangi RBB</small></div>
      <div class="rbb-insight-stat ${priorities.length ? 'bad' : 'good'}"><span>Prioritas</span><b>${priorities.length}</b><small>Akun utama untuk keputusan</small></div>
    </div>

    <div class="rbb-insight-grid">
      <section class="rbb-insight-block full">
        <h3>Kesimpulan Kondisi</h3>
        <p>Realisasi ${esc(rbbLapMeta.scope || selectedOfficeLabel())} mencapai <b>${pct(achievement)}</b> terhadap RBB periode. ${achievement >= 100 ? 'Secara agregat target telah tercapai, tetapi akun yang melampaui batas beban atau masih memiliki gap tetap perlu diawasi.' : 'Secara agregat masih terdapat gap terhadap RBB. Fokus utama diarahkan pada akun dengan nilai gap terbesar dan dampak paling material.'} Analisis warna menggunakan prinsip target: pendapatan/aset/pendanaan umumnya perlu mencapai RBB, sedangkan beban perlu dijaga agar tidak melampaui RBB.</p>
      </section>

      <section class="rbb-insight-block full">
        <h3>Akun yang Perlu Dikejar atau Dikendalikan</h3>
        <div class="rbb-priority-list">${priorityHtml}</div>
      </section>

      <section class="rbb-insight-block full"><h3>Kondisi per Kategori</h3>${categoryHtml}</section>
      <section class="rbb-insight-block"><h3>Akun yang Sudah Tercapai / Terkendali</h3>${achievedHtml}</section>
      <section class="rbb-insight-block"><h3>Kualitas Data &amp; Mapping</h3>${mappingHtml}</section>

      <section class="rbb-insight-block full">
        <h3>Pertanyaan yang Harus Dijawab dalam Rapat Kinerja</h3>
        <div class="rbb-question-grid">
          <div class="rbb-question"><b>1</b><span>Akun mana yang mempunyai gap nominal terbesar terhadap RBB dan apa penyebab utamanya?</span></div>
          <div class="rbb-question"><b>2</b><span>Apakah gap berasal dari volume, harga/yield, run off, transaksi besar, atau keterlambatan eksekusi?</span></div>
          <div class="rbb-question"><b>3</b><span>Siapa PIC setiap akun prioritas, target nominal perbaikannya, dan kapan hasil pertama harus terlihat?</span></div>
          <div class="rbb-question"><b>4</b><span>Untuk beban yang melampaui RBB, transaksi mana yang wajib, dapat ditunda, atau perlu dihentikan?</span></div>
          <div class="rbb-question"><b>5</b><span>Apakah target tercapai dengan kualitas yang sehat, khususnya untuk kredit, pendanaan, likuiditas, dan laba?</span></div>
          <div class="rbb-question"><b>6</b><span>Data apa yang harus dilaporkan kembali pada monitoring berikutnya agar keputusan dapat dievaluasi?</span></div>
        </div>
      </section>

      <section class="rbb-insight-block full">
        <h3>Cara Membaca</h3>
        <ul class="rbb-insight-list">
          <li>RBB Periode mengikuti bulan tanggal actual; RBB Desember menunjukkan sasaran akhir tahun.</li>
          <li>Selisih dihitung dari realisasi dikurangi RBB. Untuk beban, selisih positif berarti pengeluaran melampaui target dan perlu perhatian.</li>
          <li>Pencapaian persentase tidak boleh dibaca sendirian. Gunakan gap nominal, sifat akun, kualitas pertumbuhan, dan tren periode sebelumnya.</li>
          <li>Gunakan pencarian pada halaman utama untuk menemukan kata seperti <b>aset</b>, <b>kredit</b>, <b>tabungan</b>, <b>pendapatan</b>, atau nama pos tertentu.</li>
        </ul>
      </section>
    </div>`;
}

function exportRbbLapExcel() {
  const rows = rbbLapFilteredRows.length ? rbbLapFilteredRows : rbbLapRows;
  if (!rows.length) { alert('Tidak ada data untuk diexport.'); return; }
  let content = '\ufeffKode\tKategori\tKode Perkiraan\tKeterangan\tRBB Periode (Rb)\tRBB Desember (Rb)\tRealisasi (Rb)\tSelisih Periode (Rb)\t% Periode\tSelisih Desember (Rb)\t% Desember\n';
  rows.forEach(row => {
    content += `${row.kode_monbis || ''}\t${row.kategori || ''}\t${row.kode_perkiraan || ''}\t${String(row.keterangan || '').replace(/[\t\r\n]/g,' ')}\t${Math.round(toNum(row.target_rbb)/1000)}\t${Math.round(toNum(row.target_rbb_year_end)/1000)}\t${Math.round(toNum(row.realisasi_actual)/1000)}\t${Math.round(toNum(row.selisih)/1000)}\t${toNum(row.pencapaian_persen).toFixed(2).replace('.',',')}\t${Math.round(toNum(row.selisih_year_end)/1000)}\t${toNum(row.pencapaian_year_end_persen).toFixed(2).replace('.',',')}\n`;
  });
  const blob = new Blob([content], {type:'application/vnd.ms-excel;charset=utf-8;'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `RBB_vs_Realisasi_${document.getElementById('rbbLapJenis').value}_${document.getElementById('rbbLapHarian').value}.xls`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

document.addEventListener('DOMContentLoaded', initRbbLap);
</script>

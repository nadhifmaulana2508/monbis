<?php
// Laporan Laba Rugi Actual.- responsive, compact, tanpa summary card.
?>

<div id="lapNeracaPage" class="ln-page">
  <section class="ln-header">
    <div class="ln-brand">
      <div class="ln-brand-main">
        <div class="ln-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-8h6v8"/>
          </svg>
        </div>
        <div class="ln-brand-copy">
          <div class="ln-title-row">
            <h1>Laba Rugi Aktual</h1>
            <button type="button" id="lnInfoBtn" class="ln-info-btn" title="Informasi laba rugi" aria-label="Buka informasi laba rugi">i</button>
          </div>
          <p>Posisi pendapatan dan biaya berdasarkan acc_history aktual.</p>
        </div>
      </div>

      <button type="button" id="lnFilterToggle" class="ln-filter-toggle" onclick="toggleLnFilter()" aria-expanded="false" aria-controls="lnFilter">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h18M7 12h10M10 20h4"/></svg>
        <span>Filter</span>
      </button>
    </div>

    <form id="lnFilter" class="ln-filter" onsubmit="event.preventDefault(); fetchLapNeraca();">
      <label class="ln-field ln-field-date">
        <span>Actual</span>
        <input id="lnTanggal" type="date" onclick="try{this.showPicker()}catch(e){}">
      </label>

      <label class="ln-field ln-office-field">
        <span>Area / Cabang</span>
        <select id="lnKantor"></select>
      </label>

      <button type="button" class="ln-export" onclick="exportLapNeraca()" title="Download Excel" aria-label="Download Excel">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
      </button>
    </form>
  </section>

  <section class="ln-table-card">
    <div id="lnLoader" class="ln-loader ln-hidden">
      <div class="ln-spinner"></div>
      <span>Memuat laba rugi...</span>
    </div>

    <div class="ln-table-toolbar">
      <div class="ln-table-heading">
        <h2>Laporan Laba Rugi</h2>
        <p id="lnMeta">-</p>
      </div>

      <div class="ln-toolbar-right">
        <div id="lnBalanceStatus" class="ln-balance-status ln-balance-neutral" title="Status laba rugi">
          <span class="ln-status-dot"></span>
          <span>Memuat</span>
        </div>

        <label class="ln-level-control" title="Batasi level akun yang terbuka otomatis">
          <span>Level</span>
          <select id="lnLevel">
            <option value="3">3</option>
            <option value="5" selected>5</option>
            <option value="7">7</option>
            <option value="all">All</option>
          </select>
        </label>

        <label class="ln-search" aria-label="Cari akun laba rugi">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input id="lnSearch" type="search" placeholder="Cari kode / akun..." autocomplete="off">
        </label>
      </div>
    </div>

    <!-- Mobile: satu sisi per tab agar nyaman dibaca -->
    <div class="ln-mobile-tabs" role="tablist" aria-label="Sisi laba rugi">
      <button type="button" id="lnTabPENDAPATAN" class="ln-mobile-tab active" data-side="PENDAPATAN" onclick="setLnMobileSide('PENDAPATAN')" role="tab" aria-selected="true">
        PENDAPATAN
        <small id="lnTabPENDAPATANTotal">Rp 0</small>
      </button>
      <button type="button" id="lnTabBIAYA" class="ln-mobile-tab" data-side="BIAYA" onclick="setLnMobileSide('BIAYA')" role="tab" aria-selected="false">
        BIAYA
        <small id="lnTabBIAYATotal">Rp 0</small>
      </button>
    </div>

    <!-- Desktop / tablet -->
    <div class="ln-table-scroll ln-desktop-table-wrap">
      <table id="lnTable">
        <colgroup>
          <col class="ln-col-code"><col class="ln-col-name"><col class="ln-col-money">
          <col class="ln-col-code"><col class="ln-col-name"><col class="ln-col-money">
        </colgroup>
        <thead>
          <tr class="ln-group-head">
            <th colspan="3" class="ln-group-asset">Pendapatan <span>(Revenues)</span></th>
            <th colspan="3" class="ln-group-passive">Beban &amp; Biaya <span>(Expenses)</span></th>
          </tr>
          <tr class="ln-sub-head">
            <th>Kode</th><th>Uraian Perkiraan</th><th class="ln-text-right">Saldo</th>
            <th>Kode</th><th>Uraian Perkiraan</th><th class="ln-text-right">Saldo</th>
          </tr>
        </thead>
        <tbody id="lnBody">
          <tr><td colspan="6" class="ln-empty">Memuat data...</td></tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2" class="ln-foot-label ln-foot-asset">Total Pendapatan</td>
            <td id="lnFootPendapatan" class="ln-foot-money ln-foot-asset">Rp 0</td>
            <td colspan="2" class="ln-foot-label ln-foot-passive">Total Biaya</td>
            <td id="lnFootBiaya" class="ln-foot-money ln-foot-passive">Rp 0</td>
          </tr>
        </tfoot>
      </table>

      <!-- Ringkasan laba berada DI DALAM area scroll desktop.
           Jadi baris laba baru terlihat setelah user mencapai bagian bawah tabel. -->
      <div id="lnProfitSummaryDesktop" class="ln-profit-summary ln-profit-summary-desktop" aria-live="polite">
        <div class="ln-profit-row ln-profit-gross">
          <span class="ln-profit-code">LR_KOTOR</span>
          <span class="ln-profit-label">LABA RUGI KOTOR</span>
          <strong data-ln-profit-gross>Rp 0</strong>
        </div>

        <div class="ln-profit-row ln-profit-tax ln-hidden" data-ln-profit-tax-row>
          <span class="ln-profit-code">PAJAK</span>
          <span class="ln-profit-label">PAJAK</span>
          <strong data-ln-profit-tax>Rp 0</strong>
        </div>

        <div class="ln-profit-row ln-profit-net">
          <span class="ln-profit-code">LR_BERSIH</span>
          <span class="ln-profit-label">LABA RUGI BERSIH</span>
          <strong data-ln-profit-net>Rp 0</strong>
        </div>
      </div>
    </div>

    <!-- Mobile -->
    <div class="ln-mobile-table-wrap">
      <table id="lnMobileTable">
        <colgroup>
          <col class="ln-mobile-code-col">
          <col>
          <col class="ln-mobile-money-col">
        </colgroup>
        <thead>
          <tr>
            <th>Kode</th>
            <th>Uraian Perkiraan</th>
            <th class="ln-text-right">Saldo</th>
          </tr>
        </thead>
        <tbody id="lnMobileBody">
          <tr><td colspan="3" class="ln-empty">Memuat data...</td></tr>
        </tbody>
      </table>

      <!-- Ringkasan laba berada DI DALAM scroll mobile.
           User scroll sampai bagian akhir akun, lalu baru melihat laba. -->
      <div id="lnProfitSummaryMobile" class="ln-profit-summary ln-profit-summary-mobile" aria-live="polite">
        <div class="ln-profit-row ln-profit-gross">
          <span class="ln-profit-code">LR_KOTOR</span>
          <span class="ln-profit-label">LABA RUGI KOTOR</span>
          <strong data-ln-profit-gross>Rp 0</strong>
        </div>

        <div class="ln-profit-row ln-profit-tax ln-hidden" data-ln-profit-tax-row>
          <span class="ln-profit-code">PAJAK</span>
          <span class="ln-profit-label">PAJAK</span>
          <strong data-ln-profit-tax>Rp 0</strong>
        </div>

        <div class="ln-profit-row ln-profit-net">
          <span class="ln-profit-code">LR_BERSIH</span>
          <span class="ln-profit-label">LABA RUGI BERSIH</span>
          <strong data-ln-profit-net>Rp 0</strong>
        </div>
      </div>
    </div>

    <!-- Mobile total tetap di luar scroll agar nominal total sisi aktif selalu terlihat. -->
    <div id="lnMobileTotalBar" class="ln-mobile-totalbar" aria-live="polite">
      <span id="lnMobileFootLabel">Total Pendapatan</span>
      <strong id="lnMobileFootValue">Rp 0</strong>
    </div>

  </section>
</div>

<!-- INFO MODAL -->
<div id="lnInfoModal" class="ln-info-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="lnInfoTitle">
  <div class="ln-info-card">
    <div class="ln-info-header">
      <div class="ln-info-heading">
        <span class="ln-info-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-8h6v8"/></svg>
        </span>
        <div>
          <h3 id="lnInfoTitle">Informasi Laba Rugi Aktual</h3>
          <p>Ringkasan pendapatan, biaya, laba kotor, dan laba bersih. Pajak hanya untuk konsolidasi.</p>
        </div>
      </div>
      <button type="button" id="lnInfoClose" class="ln-info-close" aria-label="Tutup informasi" title="Tutup">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="ln-info-body">
      <div id="lnInfoSummary" class="ln-info-summary"></div>

      <div id="lnInfoCondition" class="ln-info-condition">
        Laporan menampilkan total pendapatan, total biaya, dan laba pada posisi yang dipilih.
      </div>

      <div class="ln-info-grid">
        <section class="ln-info-block ln-info-blue">
          <div class="ln-info-block-title"><span>P</span> Pendapatan</div>
          <p>Menunjukkan seluruh pendapatan berjalan. Akun disusun bertingkat berdasarkan kode perkiraan agar struktur akun mudah ditelusuri.</p>
        </section>
        <section class="ln-info-block ln-info-green">
          <div class="ln-info-block-title"><span>B</span> Biaya</div>
          <p>Menunjukkan seluruh beban dan biaya berjalan yang mengurangi pendapatan pada posisi laporan yang dipilih.</p>
        </section>
        <section class="ln-info-block ln-info-amber">
          <div class="ln-info-block-title"><span>Δ</span> Laba</div>
          <p>Laba Kotor = Total Pendapatan - Total Biaya. Pengurang pajak hanya ditampilkan pada Konsolidasi; Pusat, Korwil, dan Cabang tidak menggunakan baris pajak.</p>
        </section>
        <section class="ln-info-block ln-info-slate">
          <div class="ln-info-block-title"><span>⌕</span> Pencarian</div>
          <p>Pencarian memfilter kode maupun nama perkiraan. Pada mobile, gunakan tab PENDAPATAN/BIAYA agar daftar tetap lebar dan mudah dibaca.</p>
        </section>
      </div>

      <div class="ln-info-note">
        Gunakan pilihan tanggal dan Area/Cabang untuk membandingkan posisi yang benar. Gunakan laporan ini untuk membaca komposisi pendapatan, biaya, dan laba berjalan pada scope kantor terpilih.
      </div>
    </div>
  </div>
</div>

<style>
  /* =====================================================
     Lap Laba Rugi Actual - PAGE SCOPED
     Tidak mengubah navbar / shell aplikasi secara global.
     ===================================================== */
  #lapNeracaPage,
  #lnInfoModal {
    --ln-primary:#2563eb;
    --ln-success:#059669;
    --ln-bg:#f8fafc;
    --ln-text:#172033;
    --ln-muted:#64748b;
    --ln-line:#dbe4f0;
    --ln-soft-line:#e8eef6;
    font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
    color:var(--ln-text);
  }

  #lapNeracaPage *,
  #lnInfoModal * { box-sizing:border-box; }

  .ln-page {
    width:100%;
    height:calc(100vh - 60px);
    height:calc(100dvh - 60px);
    min-height:0;
    display:flex;
    flex-direction:column;
    gap:7px;
    padding:7px 10px 10px;
    overflow:hidden;
    background:var(--ln-bg);
  }

  /* Header */
  .ln-header {
    flex:0 0 auto;
    display:grid;
    grid-template-columns:minmax(250px,1fr) auto;
    align-items:center;
    gap:14px;
    min-width:0;
    padding:9px 11px;
    border:1px solid var(--ln-line);
    border-radius:11px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.04),0 8px 24px rgba(15,23,42,.025);
  }

  .ln-brand {
    display:flex;
    align-items:center;
    justify-content:space-between;
    min-width:0;
    gap:8px;
  }

  .ln-brand-main {
    display:flex;
    align-items:center;
    min-width:0;
    gap:9px;
  }

  .ln-icon {
    width:37px;
    height:37px;
    flex:0 0 37px;
    display:grid;
    place-items:center;
    border-radius:9px;
    background:var(--ln-primary);
    color:#fff;
    box-shadow:0 3px 8px rgba(37,99,235,.18);
  }
  .ln-icon svg { width:18px; height:18px; }

  .ln-brand-copy { min-width:0; }
  .ln-title-row { display:flex; align-items:center; min-width:0; gap:7px; }
  .ln-title-row h1 {
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
  .ln-brand-copy p {
    max-width:450px;
    margin:4px 0 0;
    color:var(--ln-muted);
    font-size:9px;
    line-height:1.2;
    font-weight:650;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  .ln-info-btn {
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
  .ln-info-btn:hover { background:#dbeafe; border-color:#93c5fd; transform:translateY(-1px); }

  .ln-filter {
    display:grid;
    grid-template-columns:122px minmax(185px,260px) 36px;
    align-items:end;
    gap:7px;
    min-width:0;
  }
  .ln-field { display:flex; flex-direction:column; min-width:0; gap:3px; }
  .ln-field > span {
    margin-left:1px;
    color:#475569;
    font-size:8px;
    line-height:1;
    font-weight:850;
    letter-spacing:.05em;
    text-transform:uppercase;
    white-space:nowrap;
  }
  .ln-filter input,
  .ln-filter select {
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
  .ln-filter select {
    appearance:none;
    padding-right:28px;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 8px center;
    background-size:12px 12px;
  }
  .ln-filter input:focus,
  .ln-filter select:focus { border-color:var(--ln-primary); box-shadow:0 0 0 3px rgba(37,99,235,.10); }
  .ln-filter select:disabled { background-color:#f8fafc; color:#64748b; cursor:not-allowed; }

  .ln-export,
  .ln-filter-toggle {
    border:0;
    font-weight:850;
    cursor:pointer;
    transition:.15s ease;
  }
  .ln-export {
    width:36px;
    height:35px;
    display:grid;
    place-items:center;
    border-radius:8px;
    background:var(--ln-success);
    color:#fff;
  }
  .ln-export:hover { background:#047857; transform:translateY(-1px); }
  .ln-export svg { width:17px; height:17px; }
  .ln-filter-toggle {
    display:none;
    height:30px;
    align-items:center;
    justify-content:center;
    gap:5px;
    padding:0 9px;
    border:1px solid #dbe4f0;
    border-radius:8px;
    background:#fff;
    color:#475569;
    font-size:9px;
  }
  .ln-filter-toggle svg { width:13px; height:13px; }
  .ln-filter-toggle.active { color:#1d4ed8; border-color:#bfdbfe; background:#eff6ff; }

  /* Table shell */
  .ln-table-card {
    position:relative;
    flex:1 1 auto;
    min-height:0;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    border:1px solid var(--ln-line);
    border-radius:11px;
    background:#fff;
    box-shadow:0 1px 2px rgba(15,23,42,.035);
  }

  .ln-loader {
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
  .ln-loader.ln-hidden { display:none; }
  .ln-spinner {
    width:28px;
    height:28px;
    border:4px solid #dbeafe;
    border-top-color:#2563eb;
    border-radius:999px;
    animation:lnSpin .75s linear infinite;
  }
  @keyframes lnSpin { to { transform:rotate(360deg); } }

  .ln-table-toolbar {
    flex:0 0 auto;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    min-height:48px;
    padding:7px 9px;
    border-bottom:1px solid #e2e8f0;
    background:linear-gradient(180deg,#fff,#fbfdff);
  }
  .ln-table-heading { min-width:0; }
  .ln-table-heading h2 { margin:0; color:#0f172a; font-size:12px; line-height:1.15; font-weight:900; }
  .ln-table-heading p {
    max-width:520px;
    margin:3px 0 0;
    color:#94a3b8;
    font-size:8px;
    line-height:1.15;
    font-weight:700;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
  }

  .ln-toolbar-right { display:flex; align-items:center; justify-content:flex-end; gap:6px; min-width:0; }
  .ln-balance-status {
    height:29px;
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:0 8px;
    border:1px solid;
    border-radius:999px;
    font-size:8px;
    font-weight:850;
    white-space:nowrap;
  }
  .ln-status-dot { width:6px; height:6px; border-radius:999px; background:currentColor; }
  .ln-balance-ok { color:#047857; border-color:#a7f3d0; background:#ecfdf5; }
  .ln-balance-warn { color:#b45309; border-color:#fde68a; background:#fffbeb; }
  .ln-balance-bad { color:#be123c; border-color:#fecdd3; background:#fff1f2; }
  .ln-balance-neutral { color:#64748b; border-color:#e2e8f0; background:#f8fafc; }

  .ln-level-control {
    height:31px;
    display:flex;
    align-items:center;
    gap:5px;
    padding:0 5px 0 8px;
    border:1px solid #dbe3ee;
    border-radius:8px;
    background:#fff;
    color:#64748b;
    white-space:nowrap;
  }
  .ln-level-control > span {
    font-size:7px;
    font-weight:900;
    letter-spacing:.05em;
    text-transform:uppercase;
  }
  .ln-level-control select {
    width:48px;
    height:25px;
    padding:0 17px 0 5px;
    border:0;
    outline:0;
    appearance:none;
    -webkit-appearance:none;
    border-radius:6px;
    background-color:#f8fafc;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 4px center;
    background-size:10px 10px;
    color:#1e40af;
    font-size:8px;
    font-weight:900;
    cursor:pointer;
  }
  .ln-level-control:focus-within { border-color:#93c5fd; box-shadow:0 0 0 3px rgba(37,99,235,.08); }

  .ln-tree-toggle {
    width:17px;
    height:17px;
    flex:0 0 17px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    margin-right:4px;
    border:1px solid transparent;
    border-radius:5px;
    background:transparent;
    color:#64748b;
    cursor:pointer;
    transition:transform .16s ease,background .16s ease,border-color .16s ease,color .16s ease;
  }
  .ln-tree-toggle:hover { background:#eff6ff; border-color:#dbeafe; color:#2563eb; }
  .ln-tree-toggle.is-open { transform:rotate(90deg); color:#2563eb; }
  .ln-tree-toggle-placeholder { width:17px; height:17px; flex:0 0 17px; margin-right:4px; }
  .ln-name-line { display:flex; align-items:center; min-width:0; }
  .ln-name-line .ln-name { min-width:0; flex:1; }

  .ln-search {
    position:relative;
    width:min(250px,31vw);
    height:31px;
    display:flex;
    align-items:center;
    border:1px solid #dbe3ee;
    border-radius:8px;
    background:#fff;
  }
  .ln-search svg {
    position:absolute;
    left:8px;
    top:50%;
    width:13px;
    height:13px;
    color:#94a3b8;
    pointer-events:none;
    transform:translateY(-50%);
  }
  .ln-search input {
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
  .ln-search:focus-within { border-color:#60a5fa; box-shadow:0 0 0 3px rgba(59,130,246,.09); }

  /* Desktop table */
  .ln-table-scroll {
    flex:1 1 auto;
    min-height:0;
    overflow:auto;
    scrollbar-gutter:stable;
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
  }
  .ln-table-scroll::-webkit-scrollbar { width:7px; height:7px; }
  .ln-table-scroll::-webkit-scrollbar-track { background:#f8fafc; }
  .ln-table-scroll::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }

  #lnTable {
    width:100%;
    min-width:920px;
    border-collapse:separate;
    border-spacing:0;
    table-layout:fixed;
    color:#334155;
    font-size:10px;
    font-variant-numeric:tabular-nums;
  }
  #lnTable .ln-col-code { width:7%; }
  #lnTable .ln-col-name { width:26%; }
  #lnTable .ln-col-money { width:17%; }
  #lnTable th,
  #lnTable td {
    height:36px;
    padding:5px 7px;
    vertical-align:middle;
    border-right:1px solid #edf2f7;
    border-bottom:1px solid #edf2f7;
  }
  #lnTable thead th {
    position:sticky;
    z-index:30;
    background:#f8fafc;
    color:#475569;
    font-size:8px;
    line-height:1.1;
    font-weight:900;
    letter-spacing:.05em;
    text-transform:uppercase;
  }
  #lnTable .ln-group-head th {
    top:0;
    height:34px;
    text-align:center;
    border-bottom:1px solid #cbd5e1;
    font-size:9px;
    letter-spacing:.08em;
  }
  #lnTable .ln-group-head th span { color:#94a3b8; font-size:7px; letter-spacing:.04em; }
  #lnTable .ln-group-asset { color:#4338ca; background:#f5f3ff; border-top:3px solid #6366f1; }
  #lnTable .ln-group-passive { color:#047857; background:#ecfdf5; border-top:3px solid #10b981; }
  #lnTable .ln-sub-head th { top:34px; height:31px; background:#f8fafc; box-shadow:inset 0 -1px 0 #cbd5e1; }
  .ln-text-right { text-align:right !important; }

  #lnTable tbody tr:hover td { background:#f8fafc; }
  .ln-code {
    color:#1e40af;
    font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
    font-size:9px;
    font-weight:850;
  }
  .ln-side-passive.ln-code { color:#047857; }
  .ln-name {
    min-width:0;
    color:#334155;
    font-size:10px;
    line-height:1.2;
    font-weight:750;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .ln-money {
    color:#0f172a;
    text-align:right;
    font-family:"JetBrains Mono",ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
    font-size:9.5px;
    font-weight:800;
    letter-spacing:-.025em;
    white-space:nowrap;
  }
  .ln-depth-0 .ln-name { color:#0f172a; font-size:11px; font-weight:900; }
  .ln-depth-1 .ln-name { color:#1e293b; font-size:10.5px; font-weight:850; }
  .ln-depth-2 .ln-name { color:#475569; font-weight:750; }
  .ln-empty { height:180px !important; padding:26px !important; color:#94a3b8; text-align:center; font-size:10px; font-weight:800; }

  #lnTable tfoot td {
    position:sticky;
    bottom:0;
    z-index:25;
    height:42px;
    border-top:1px solid #cbd5e1;
    border-bottom:0;
    font-size:9px;
    font-weight:900;
    letter-spacing:.04em;
    text-transform:uppercase;
    box-shadow:0 -5px 12px -12px rgba(15,23,42,.75);
  }
  .ln-foot-asset { background:#eef2ff !important; color:#3730a3 !important; }
  .ln-foot-passive { background:#ecfdf5 !important; color:#047857 !important; }
  .ln-foot-money {
    text-align:right;
    font-family:"JetBrains Mono",ui-monospace,monospace;
    font-size:11px !important;
    letter-spacing:-.02em !important;
  }

  /* Mobile dedicated view */
  .ln-mobile-tabs,
  .ln-mobile-table-wrap,
  .ln-mobile-totalbar { display:none; }

  /* Ringkasan laba/rugi menjadi bagian dari konten scroll.
     Tidak lagi memakan tinggi workspace secara permanen. */
  .ln-profit-summary {
    width:100%;
    border-top:1px solid #cbd5e1;
    background:#fff;
    color:#334155;
  }
  .ln-profit-row {
    position:relative;
    display:grid;
    grid-template-columns:112px minmax(0,1fr) minmax(170px,230px);
    align-items:center;
    min-height:36px;
    border-bottom:1px solid #e2e8f0;
    background:#f8fafc;
  }
  .ln-profit-row::before {
    content:'';
    position:absolute;
    left:0;
    top:0;
    bottom:0;
    width:3px;
    background:#94a3b8;
  }
  .ln-profit-row:last-child { border-bottom:0; }
  .ln-profit-row > * {
    min-width:0;
    padding:7px 10px;
  }
  .ln-profit-code {
    color:#64748b;
    font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
    font-size:8px;
    line-height:1;
    font-weight:900;
    letter-spacing:.035em;
    white-space:nowrap;
  }
  .ln-profit-label {
    color:#334155;
    font-size:9px;
    line-height:1.15;
    font-weight:900;
    letter-spacing:.02em;
  }
  .ln-profit-row strong {
    color:#0f172a;
    text-align:right;
    font-family:"JetBrains Mono",ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;
    font-size:10px;
    line-height:1;
    font-weight:900;
    white-space:nowrap;
  }
  .ln-profit-gross {
    background:#f8fafc;
  }
  .ln-profit-gross::before { background:#64748b; }
  .ln-profit-tax {
    background:#fffbeb;
  }
  .ln-profit-tax::before { background:#f59e0b; }
  .ln-profit-tax .ln-profit-code,
  .ln-profit-tax .ln-profit-label,
  .ln-profit-tax strong { color:#92400e; }
  .ln-profit-net {
    min-height:39px;
    background:#ecfdf5;
    border-top:1px solid #bbf7d0;
  }
  .ln-profit-net::before { background:#10b981; }
  .ln-profit-net .ln-profit-code { color:#047857; }
  .ln-profit-net .ln-profit-label { color:#065f46; }
  .ln-profit-net strong { color:#047857; }
  .ln-profit-summary .ln-hidden { display:none !important; }

  .ln-profit-summary-mobile { display:none; }
  .ln-profit-summary-desktop { display:block; }

  /* Info modal */
  .ln-info-modal {
    position:fixed;
    inset:0;
    z-index:10000;
    display:none;
    align-items:center;
    justify-content:center;
    padding:14px;
    background:rgba(15,23,42,.64);
    backdrop-filter:blur(6px);
  }
  .ln-info-modal.open { display:flex; }
  .ln-info-card {
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
  .ln-info-header {
    flex:0 0 auto;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding:12px 13px;
    border-bottom:1px solid #e2e8f0;
    background:linear-gradient(180deg,#fff,#f8fafc);
  }
  .ln-info-heading { display:flex; align-items:center; min-width:0; gap:9px; }
  .ln-info-icon {
    width:34px;
    height:34px;
    flex:0 0 34px;
    display:grid;
    place-items:center;
    border:1px solid #bfdbfe;
    border-radius:9px;
    background:#eff6ff;
    color:#2563eb;
  }
  .ln-info-icon svg { width:17px; height:17px; }
  .ln-info-heading h3 { margin:0; color:#0f172a; font-size:14px; line-height:1.15; font-weight:900; }
  .ln-info-heading p { margin:3px 0 0; color:#64748b; font-size:8.5px; line-height:1.35; font-weight:650; }
  .ln-info-close {
    width:32px;
    height:32px;
    flex:0 0 32px;
    display:grid;
    place-items:center;
    border:1px solid #e2e8f0;
    border-radius:8px;
    background:#fff;
    color:#64748b;
    cursor:pointer;
  }
  .ln-info-close:hover { border-color:#fecdd3; background:#fff1f2; color:#e11d48; }
  .ln-info-close svg { width:15px; height:15px; }

  .ln-info-body {
    flex:1 1 auto;
    min-height:0;
    overflow:auto;
    padding:11px 12px 13px;
    overscroll-behavior:contain;
  }
  .ln-info-summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:7px; }
  .ln-info-stat {
    min-width:0;
    padding:8px 9px;
    border:1px solid #e2e8f0;
    border-radius:9px;
    background:#f8fafc;
  }
  .ln-info-stat-label { color:#64748b; font-size:7px; font-weight:900; letter-spacing:.06em; text-transform:uppercase; }
  .ln-info-stat-value {
    margin-top:4px;
    overflow:hidden;
    color:#0f172a;
    font-family:"JetBrains Mono",ui-monospace,monospace;
    font-size:11px;
    line-height:1.1;
    font-weight:900;
    white-space:nowrap;
    text-overflow:ellipsis;
  }
  .ln-info-stat-value.blue { color:#3730a3; }
  .ln-info-stat-value.green { color:#047857; }
  .ln-info-stat-value.red { color:#be123c; }

  .ln-info-condition {
    margin-top:8px;
    padding:9px 10px;
    border:1px solid #bfdbfe;
    border-radius:9px;
    background:#eff6ff;
    color:#1e3a8a;
    font-size:9px;
    line-height:1.45;
    font-weight:750;
  }
  .ln-info-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:7px; margin-top:8px; }
  .ln-info-block {
    min-width:0;
    padding:9px 10px;
    border:1px solid #e2e8f0;
    border-left:4px solid var(--ln-block,#3b82f6);
    border-radius:9px;
    background:#fff;
  }
  .ln-info-blue { --ln-block:#6366f1; }
  .ln-info-green { --ln-block:#10b981; }
  .ln-info-amber { --ln-block:#f59e0b; }
  .ln-info-slate { --ln-block:#64748b; }
  .ln-info-block-title { display:flex; align-items:center; gap:6px; color:#0f172a; font-size:9.5px; font-weight:900; }
  .ln-info-block-title span {
    min-width:20px;
    height:18px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:0 5px;
    border-radius:999px;
    background:#f1f5f9;
    color:#475569;
    font-size:7px;
    font-weight:900;
  }
  .ln-info-block p { margin:5px 0 0; color:#475569; font-size:8.5px; line-height:1.45; font-weight:650; }
  .ln-info-note {
    margin-top:8px;
    padding:8px 9px;
    border:1px solid #fde68a;
    border-radius:9px;
    background:#fffbeb;
    color:#92400e;
    font-size:8.5px;
    line-height:1.45;
    font-weight:700;
  }

  /* Large desktop */
  @media (min-width:1440px) {
    .ln-page { height:calc(100dvh - 72px); padding:8px 12px 12px; }
    .ln-header { padding:10px 12px; }
    .ln-filter { grid-template-columns:130px minmax(210px,285px) 37px; }
    .ln-filter input,.ln-filter select,.ln-export { height:36px; }
    .ln-export { width:37px; }
    #lnTable { min-width:0; }
    #lnTable th,#lnTable td { height:38px; padding:6px 8px; }
  }

  /* Laptop */
  @media (min-width:1024px) and (max-width:1439px) {
    .ln-page { padding:6px 8px 8px; }
    #lnTable { min-width:900px; }
    #lnTable .ln-col-code { width:7.5%; }
    #lnTable .ln-col-name { width:25.5%; }
    #lnTable .ln-col-money { width:17%; }
  }

  /* Tablet */
  @media (min-width:768px) and (max-width:1023px) {
    .ln-page { height:calc(100dvh - 64px); padding:6px; }
    .ln-header { grid-template-columns:1fr auto; gap:7px; padding:8px; }
    .ln-filter-toggle { display:inline-flex; }
    .ln-filter {
      grid-column:1 / -1;
      display:none;
      grid-template-columns:130px minmax(220px,1fr) 36px;
      width:100%;
      padding-top:7px;
      border-top:1px solid #e2e8f0;
    }
    .ln-filter.open { display:grid; }
    #lnTable { min-width:900px; }
    .ln-search { width:220px; }
  }

  /* Mobile */
  @media (max-width:767px) {
    .ln-page {
      height:calc(100vh - 54px);
      height:calc(100dvh - 54px);
      min-height:0;
      gap:5px;
      padding:4px 4px max(5px,env(safe-area-inset-bottom));
      overflow:hidden;
    }
    .ln-header {
      display:grid;
      grid-template-columns:1fr;
      gap:6px;
      padding:7px;
      border-radius:9px;
    }
    .ln-brand { width:100%; }
    .ln-brand-main { gap:7px; }
    .ln-icon { width:29px; height:29px; flex-basis:29px; border-radius:7px; }
    .ln-icon svg { width:14px; height:14px; }
    .ln-title-row { gap:5px; }
    .ln-title-row h1 { font-size:12px; }
    .ln-brand-copy p { max-width:205px; margin-top:2px; font-size:7px; }
    .ln-info-btn { width:18px; height:18px; flex-basis:18px; font-size:9px; }
    .ln-filter-toggle { display:inline-flex; height:29px; padding:0 8px; font-size:8px; }

    .ln-filter {
      display:none;
      grid-template-columns:minmax(0,1fr) minmax(0,1fr) 32px;
      width:100%;
      gap:5px;
      padding-top:6px;
      border-top:1px solid #e2e8f0;
    }
    .ln-filter.open { display:grid; }
    .ln-field > span { font-size:6.5px; }
    .ln-filter input,.ln-filter select { height:31px; padding:0 6px; border-radius:7px; font-size:8.5px; }
    .ln-filter select { padding-right:20px; background-position:right 5px center; background-size:10px 10px; }
    .ln-export { width:32px; height:31px; border-radius:7px; }
    .ln-export svg { width:15px; height:15px; }

    .ln-table-card { border-radius:9px; }
    .ln-table-toolbar { min-height:auto; padding:6px; gap:5px; }
    .ln-table-heading h2 { font-size:10px; }
    .ln-table-heading p { max-width:125px; margin-top:2px; font-size:6.5px; }
    .ln-toolbar-right {
      width:100%;
      display:grid;
      grid-template-columns:auto auto minmax(0,1fr);
      gap:4px;
    }
    .ln-balance-status { height:27px; padding:0 6px; font-size:6.5px; }
    .ln-level-control { height:28px; padding:0 4px 0 6px; gap:3px; border-radius:7px; }
    .ln-level-control > span { display:none; }
    .ln-level-control select { width:44px; height:22px; padding-left:4px; font-size:7px; }
    .ln-status-dot { width:5px; height:5px; }
    .ln-search { width:100%; min-width:0; height:28px; border-radius:7px; }
    .ln-search svg { left:7px; width:11px; height:11px; }
    .ln-search input { padding-left:23px; font-size:7.5px; }

    .ln-desktop-table-wrap { display:none; }
    .ln-mobile-tabs {
      flex:0 0 auto;
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:4px;
      padding:5px 6px;
      border-bottom:1px solid #e2e8f0;
      background:#f8fafc;
    }
    .ln-mobile-tab {
      min-width:0;
      height:34px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:5px;
      padding:0 8px;
      border:1px solid #dbe4f0;
      border-radius:8px;
      background:#fff;
      color:#64748b;
      font-size:8px;
      font-weight:900;
      cursor:pointer;
    }
    .ln-mobile-tab small {
      min-width:0;
      overflow:hidden;
      color:#94a3b8;
      font-family:"JetBrains Mono",ui-monospace,monospace;
      font-size:6.5px;
      font-weight:800;
      text-overflow:ellipsis;
      white-space:nowrap;
    }
    .ln-mobile-tab.active[data-side="PENDAPATAN"] { color:#4338ca; border-color:#c7d2fe; background:#eef2ff; }
    .ln-mobile-tab.active[data-side="BIAYA"] { color:#047857; border-color:#a7f3d0; background:#ecfdf5; }
    .ln-mobile-tab.active small { color:inherit; }

    .ln-mobile-table-wrap {
      flex:1 1 0;
      min-height:0;
      display:block;
      overflow:auto;
      overscroll-behavior:contain;
      -webkit-overflow-scrolling:touch;
    }
    .ln-mobile-table-wrap::-webkit-scrollbar { width:4px; height:4px; }
    .ln-mobile-table-wrap::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
    #lnMobileTable {
      width:100%;
      min-width:0;
      border-collapse:separate;
      border-spacing:0;
      table-layout:fixed;
      font-variant-numeric:tabular-nums;
    }
    #lnMobileTable .ln-mobile-code-col { width:58px; }
    #lnMobileTable .ln-mobile-money-col { width:112px; }
    #lnMobileTable th {
      position:sticky;
      top:0;
      z-index:20;
      height:30px;
      padding:4px 5px;
      border-right:1px solid #edf2f7;
      border-bottom:1px solid #cbd5e1;
      background:#f8fafc;
      color:#64748b;
      font-size:6.5px;
      font-weight:900;
      letter-spacing:.045em;
      text-transform:uppercase;
    }
    #lnMobileTable td {
      min-height:34px;
      padding:5px;
      border-right:1px solid #f1f5f9;
      border-bottom:1px solid #edf2f7;
      background:#fff;
      vertical-align:middle;
    }
    #lnMobileTable tbody tr:hover td { background:#f8fafc; }
    #lnMobileTable .ln-code { font-size:7px; letter-spacing:-.02em; }
    #lnMobileTable .ln-tree-toggle { width:14px; height:14px; flex-basis:14px; margin-right:2px; border-radius:4px; font-size:6px; }
    #lnMobileTable .ln-tree-toggle-placeholder { width:14px; height:14px; flex-basis:14px; margin-right:2px; }
    #lnMobileTable .ln-name {
      display:-webkit-box;
      overflow:hidden;
      font-size:8px;
      line-height:1.18;
      white-space:normal;
      -webkit-line-clamp:2;
      -webkit-box-orient:vertical;
      overflow-wrap:anywhere;
    }
    #lnMobileTable .ln-depth-0 .ln-name { font-size:8.8px; }
    #lnMobileTable .ln-depth-1 .ln-name { font-size:8.4px; }
    #lnMobileTable .ln-money { font-size:7.8px; letter-spacing:-.04em; }
    .ln-mobile-totalbar {
      position:relative;
      z-index:26;
      flex:0 0 auto;
      min-height:38px;
      display:grid;
      grid-template-columns:minmax(0,1fr) auto;
      align-items:center;
      gap:8px;
      padding:7px 8px max(7px,env(safe-area-inset-bottom));
      border-top:1px solid #cbd5e1;
      background:#eef2ff;
      color:#3730a3;
      box-shadow:0 -7px 18px -16px rgba(15,23,42,.75);
    }
    .ln-mobile-totalbar span {
      min-width:0;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
      font-size:7px;
      font-weight:900;
      letter-spacing:.04em;
      text-transform:uppercase;
    }
    .ln-mobile-totalbar strong {
      min-width:0;
      text-align:right;
      font-family:"JetBrains Mono",ui-monospace,monospace;
      font-size:8.5px;
      font-weight:900;
      letter-spacing:-.03em;
      white-space:nowrap;
    }
    .ln-mobile-totalbar.ln-mobile-passive { background:#ecfdf5; color:#047857; }

    .ln-profit-summary-desktop { display:none !important; }
    .ln-profit-summary-mobile {
      display:block !important;
      margin-top:0;
      border-top:1px solid #cbd5e1;
    }

    .ln-profit-row {
      grid-template-columns:64px minmax(0,1fr) 112px;
      min-height:31px;
    }
    .ln-profit-row > * { padding:6px 7px; }
    .ln-profit-code {
      font-size:6px;
      letter-spacing:-.01em;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    .ln-profit-label {
      font-size:7px;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    .ln-profit-row strong {
      font-size:7.6px;
      letter-spacing:-.035em;
    }
    .ln-profit-net { min-height:34px; }
    .ln-profit-summary-mobile::after {
      content:'';
      display:block;
      height:4px;
      background:#f8fafc;
    }

    .ln-info-modal { align-items:flex-end; padding:0; }
    .ln-info-card {
      width:100%;
      max-height:92dvh;
      border-radius:16px 16px 0 0;
      border-left:0;
      border-right:0;
      border-bottom:0;
      box-shadow:0 -18px 48px rgba(15,23,42,.30);
    }
    .ln-info-header { padding:10px; }
    .ln-info-icon { width:31px; height:31px; flex-basis:31px; }
    .ln-info-heading h3 { font-size:12px; }
    .ln-info-heading p { font-size:7.5px; }
    .ln-info-close { width:30px; height:30px; flex-basis:30px; }
    .ln-info-body { padding:8px 9px 12px; }
    .ln-info-summary { grid-template-columns:repeat(2,minmax(0,1fr)); gap:5px; }
    .ln-info-stat:last-child { grid-column:1 / -1; }
    .ln-info-stat { padding:7px; }
    .ln-info-stat-value { font-size:9.5px; }
    .ln-info-condition { font-size:8px; }
    .ln-info-grid { grid-template-columns:1fr; gap:6px; }
    .ln-info-block { padding:8px; }
    .ln-info-block p,.ln-info-note { font-size:7.8px; }
  }

  @media (max-width:380px) {
    .ln-brand-copy p { max-width:170px; }
    .ln-table-heading p { max-width:96px; }
    .ln-search { width:112px; }
    .ln-balance-status { padding:0 5px; }
    #lnMobileTable .ln-mobile-code-col { width:52px; }
    #lnMobileTable .ln-mobile-money-col { width:102px; }
    #lnMobileTable .ln-money { font-size:7.2px; }
    .ln-profit-row { grid-template-columns:58px minmax(0,1fr) 104px; }
    .ln-profit-row > * { padding-left:5px; padding-right:5px; }
    .ln-profit-code { font-size:5.7px; }
    .ln-profit-label { font-size:6.6px; }
    .ln-profit-row strong { font-size:7px; }
  }
</style>

<script>
  const LN_API_LAP = './api/lapkeu';
  const LN_API_KODE = './api/kode/';
  let lnRaw = null;
  let lnFetchTimer = null;
  let lnMobileSide = 'PENDAPATAN';
  let lnLevelMode = '5';
  const lnTreeState = {
    PENDAPATAN: { expanded:new Set(), collapsed:new Set(), meta:new Map() },
    BIAYA: { expanded:new Set(), collapsed:new Set(), meta:new Map() }
  };

  const lnKorwilOptions = [
    { value:'KW_SEMARANG', label:'Korwil Semarang' },
    { value:'KW_SOLO', label:'Korwil Solo' },
    { value:'KW_BANYUMAS', label:'Korwil Banyumas' },
    { value:'KW_PEKALONGAN', label:'Korwil Pekalongan' },
  ];

  function toggleLnFilter(force) {
    const form = document.getElementById('lnFilter');
    const btn = document.getElementById('lnFilterToggle');
    if (!form) return;
    const open = typeof force === 'boolean' ? force : !form.classList.contains('open');
    form.classList.toggle('open', open);
    btn?.classList.toggle('active', open);
    btn?.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  function safeLn(text) {
    return String(text ?? '').replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
  }

  function fmtLn(num) {
    return Number(num || 0).toLocaleString('id-ID', { maximumFractionDigits:0 });
  }

  function fmtLnRp(num) {
    return 'Rp ' + fmtLn(num);
  }

  function fmtLnShort(num) {
    const n = Number(num || 0);
    const abs = Math.abs(n);
    const sign = n < 0 ? '-' : '';
    if (abs >= 1e12) return `${sign}Rp ${(abs/1e12).toFixed(2).replace('.',',')} T`;
    if (abs >= 1e9) return `${sign}Rp ${(abs/1e9).toFixed(2).replace('.',',')} M`;
    if (abs >= 1e6) return `${sign}Rp ${(abs/1e6).toFixed(2).replace('.',',')} Jt`;
    return `${sign}Rp ${fmtLn(abs)}`;
  }

  function scheduleLapNeraca(delay = 350) {
    clearTimeout(lnFetchTimer);
    lnFetchTimer = setTimeout(fetchLapNeraca, delay);
  }

  async function loadLnDefaultDate() {
    try {
      const res = await fetch(LN_API_LAP, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({type:'default_acc_history_date'})
      });
      const json = await res.json();
      return json?.data?.last_created || new Date().toISOString().slice(0,10);
    } catch (e) {
      return new Date().toISOString().slice(0,10);
    }
  }

  async function populateLnKantor() {
    const select = document.getElementById('lnKantor');
    const user = (window.getUser && window.getUser()) || null;
    const userKode = user?.kode ? String(user.kode).padStart(3,'0') : '000';

    let list = [];
    try {
      const res = await fetch(LN_API_KODE, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({type:'kode_kantor'})
      });
      const json = await res.json();
      list = Array.isArray(json.data) ? json.data : [];
    } catch (e) {
      list = [];
    }

    if (userKode !== '000') {
      const row = list.find(item => String(item.kode_kantor).padStart(3,'0') === userKode);
      select.innerHTML = `<option value="${userKode}">${userKode} - ${safeLn(row?.nama_kantor || 'Cabang')}</option>`;
      select.disabled = true;
      return;
    }

    let html = '<option value="000">000 - Konsolidasi</option>';
    html += '<option value="pusat">000 - Pusat</option>';
    lnKorwilOptions.forEach(item => {
      html += `<option value="${item.value}">${item.label}</option>`;
    });
    list
      .filter(item => item.kode_kantor && String(item.kode_kantor).padStart(3,'0') !== '000')
      .sort((a,b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor)))
      .forEach(item => {
        const kode = String(item.kode_kantor).padStart(3,'0');
        html += `<option value="${kode}">${kode} - ${safeLn(item.nama_kantor || 'Cabang')}</option>`;
      });
    select.innerHTML = html;
  }

  function getLnPayload() {
    const kantorValue = document.getElementById('lnKantor').value || '000';
    const payload = {
      type:'lap_laba_rugi_actual',
      harian_date:document.getElementById('lnTanggal').value,
      kode_kantor:kantorValue,
      korwil:''
    };

    if (kantorValue.startsWith('KW_')) {
      payload.kode_kantor = '000';
      payload.korwil = kantorValue.replace('KW_', '');
    }
    return payload;
  }

  // Pajak hanya digunakan saat user memilih "000 - Konsolidasi".
  // Pusat, Korwil, dan Cabang tidak menampilkan pengurang pajak.
  function isLnConsolidation() {
    return (document.getElementById('lnKantor')?.value || '') === '000';
  }

  function getLnProfitTotals() {
    const pendapatan = Number(lnRaw?.totals?.PENDAPATAN || 0);
    const biaya = Number(lnRaw?.totals?.BIAYA || 0);
    const labaKotor = Number(lnRaw?.totals?.LABA_KOTOR ?? (pendapatan - biaya));
    const consolidation = isLnConsolidation();
    const pajak = consolidation ? Number(lnRaw?.totals?.PAJAK || 0) : 0;
    const labaBersih = consolidation
      ? Number(lnRaw?.totals?.LABA_BERSIH ?? (labaKotor - pajak))
      : labaKotor;

    return { pendapatan, biaya, labaKotor, pajak, labaBersih, consolidation };
  }

  function renderLnProfitSummary() {
    if (!lnRaw) return;
    const { labaKotor, pajak, labaBersih, consolidation } = getLnProfitTotals();

    document.querySelectorAll('[data-ln-profit-gross]').forEach(el => {
      el.textContent = fmtLnRp(labaKotor);
    });
    document.querySelectorAll('[data-ln-profit-tax]').forEach(el => {
      el.textContent = fmtLnRp(pajak);
    });
    document.querySelectorAll('[data-ln-profit-net]').forEach(el => {
      el.textContent = fmtLnRp(labaBersih);
    });
    document.querySelectorAll('[data-ln-profit-tax-row]').forEach(row => {
      row.classList.toggle('ln-hidden', !consolidation);
    });
  }

  async function fetchLapNeraca() {
    const loader = document.getElementById('lnLoader');
    const body = document.getElementById('lnBody');
    const mobileBody = document.getElementById('lnMobileBody');
    loader?.classList.remove('ln-hidden');

    try {
      const res = await fetch(LN_API_LAP, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify(getLnPayload())
      });
      const json = await res.json();
      if (!res.ok || Number(json.status) !== 200) {
        throw new Error(json.message || 'Gagal memuat data');
      }
      lnRaw = json.data || {};
      lnRaw.PENDAPATAN = Array.isArray(lnRaw.pendapatan) ? lnRaw.pendapatan : [];
      lnRaw.BIAYA = Array.isArray(lnRaw.biaya) ? lnRaw.biaya : [];
      lnRaw.totals = lnRaw.totals || {};
      lnRaw.totals.PENDAPATAN = Number(lnRaw.totals.pendapatan || 0);
      lnRaw.totals.BIAYA = Number(lnRaw.totals.biaya || 0);
      lnRaw.totals.LABA_KOTOR = Number(lnRaw.totals.laba_kotor || 0);
      lnRaw.totals.PAJAK = Number(lnRaw.totals.pajak || 0);
      lnRaw.totals.LABA_BERSIH = Number(lnRaw.totals.laba_bersih || 0);
      rebuildLnTrees(true);
      renderLapNeraca();
    } catch (e) {
      console.error(e);
      lnRaw = null;
      if (body) body.innerHTML = `<tr><td colspan="6" class="ln-empty">Gagal memuat data laba rugi.</td></tr>`;
      if (mobileBody) mobileBody.innerHTML = `<tr><td colspan="3" class="ln-empty">Gagal memuat data laba rugi.</td></tr>`;
      updateLnBalanceStatus(null);
    } finally {
      loader?.classList.add('ln-hidden');
      if (window.innerWidth < 1024) toggleLnFilter(false);
    }
  }

  function rowMatchesSearch(row, query) {
    if (!query) return true;
    return `${row?.kode_perk || ''} ${row?.nama_perkiraan || ''}`.toLowerCase().includes(query);
  }

  function lnCode(row) {
    return String(row?.kode_perk ?? '').trim();
  }

  function buildLnTreeMeta(side) {
    const rows = Array.isArray(lnRaw?.[side]) ? lnRaw[side] : [];
    const codes = rows.map(lnCode).filter(Boolean);
    const codeSet = new Set(codes);
    const meta = new Map();

    codes.forEach(code => {
      let parent = '';
      for (let len = code.length - 1; len >= 1; len--) {
        const candidate = code.slice(0, len);
        if (codeSet.has(candidate)) { parent = candidate; break; }
      }
      meta.set(code, { parent, children:[] });
    });

    meta.forEach((item, code) => {
      if (item.parent && meta.has(item.parent)) meta.get(item.parent).children.push(code);
    });
    meta.forEach(item => item.children.sort((a,b) => a.localeCompare(b, 'id', {numeric:true})));
    lnTreeState[side].meta = meta;
  }

  function rebuildLnTrees(resetManual = false) {
    ['PENDAPATAN','BIAYA'].forEach(side => {
      buildLnTreeMeta(side);
      if (resetManual) {
        lnTreeState[side].expanded.clear();
        lnTreeState[side].collapsed.clear();
      }
    });
  }

  function lnLevelLimit() {
    return lnLevelMode === 'all' ? Infinity : Number(lnLevelMode || 5);
  }

  function lnHasChildren(side, code) {
    return Boolean(lnTreeState[side]?.meta?.get(code)?.children?.length);
  }

  function lnDefaultExpanded(side, code) {
    if (!lnHasChildren(side, code)) return false;
    if (lnLevelMode === 'all') return true;
    const limit = lnLevelLimit();
    const children = lnTreeState[side].meta.get(code)?.children || [];
    return children.some(childCode => childCode.length <= limit);
  }

  function lnIsExpanded(side, code) {
    const state = lnTreeState[side];
    if (!state) return false;
    if (state.collapsed.has(code)) return false;
    if (state.expanded.has(code)) return true;
    return lnDefaultExpanded(side, code);
  }

  function lnIsVisible(side, row) {
    const code = lnCode(row);
    if (!code) return true;
    const meta = lnTreeState[side]?.meta;
    if (!meta?.has(code)) return code.length <= lnLevelLimit();

    let parent = meta.get(code)?.parent || '';
    const visited = new Set();
    while (parent) {
      if (visited.has(parent)) return false;
      visited.add(parent);
      if (!lnIsExpanded(side, parent)) return false;
      parent = meta.get(parent)?.parent || '';
    }
    return true;
  }

  function getLnVisibleRows(side, query = '') {
    const rows = Array.isArray(lnRaw?.[side]) ? lnRaw[side] : [];
    if (query) return rows.filter(row => rowMatchesSearch(row, query));
    return rows.filter(row => lnIsVisible(side, row));
  }

  function toggleLnNode(side, code) {
    const state = lnTreeState[side];
    if (!state || !lnHasChildren(side, code)) return;
    const currentlyOpen = lnIsExpanded(side, code);
    if (currentlyOpen) {
      state.expanded.delete(code);
      state.collapsed.add(code);
    } else {
      state.collapsed.delete(code);
      state.expanded.add(code);
    }
    renderLapNeraca();
  }

  function lnTreeButton(side, row) {
    const code = lnCode(row);
    if (!code || !lnHasChildren(side, code)) return '<span class="ln-tree-toggle-placeholder" aria-hidden="true"></span>';
    const open = lnIsExpanded(side, code);
    return `<button type="button" class="ln-tree-toggle ${open ? 'is-open' : ''}" onclick="event.stopPropagation();toggleLnNode('${side}','${safeLn(code)}')" title="${open ? 'Tutup' : 'Buka'} breakdown ${safeLn(code)}" aria-label="${open ? 'Tutup' : 'Buka'} breakdown ${safeLn(code)}" aria-expanded="${open ? 'true' : 'false'}">▶</button>`;
  }

  function renderSide(row, side) {
    if (!row) return '<td></td><td></td><td></td>';
    const sideClass = side === 'BIAYA' ? 'ln-side-passive' : 'ln-side-asset';
    const depth = Math.max(0, Math.min(8, Number(row.depth || 0)));
    const indent = 3 + (depth * 10);
    return `
      <td class="${sideClass} ln-code ln-depth-${depth}">${safeLn(row.kode_perk)}</td>
      <td class="${sideClass} ln-depth-${depth}"><div class="ln-name-line" style="padding-left:${indent}px">${lnTreeButton(side,row)}<div class="ln-name" title="${safeLn(row.nama_perkiraan)}">${safeLn(row.nama_perkiraan)}</div></div></td>
      <td class="${sideClass} ln-money ln-depth-${depth}" title="${fmtLnRp(row.saldo)}">${fmtLn(row.saldo)}</td>
    `;
  }

  function renderMobileRow(row, side) {
    const depth = Math.max(0, Math.min(8, Number(row?.depth || 0)));
    const indent = Math.min(48, depth * 5);
    const passiveClass = side === 'BIAYA' ? 'ln-side-passive' : 'ln-side-asset';
    return `
      <tr>
        <td class="${passiveClass} ln-code ln-depth-${depth}">${safeLn(row?.kode_perk || '')}</td>
        <td class="${passiveClass} ln-depth-${depth}"><div class="ln-name-line" style="padding-left:${indent}px">${lnTreeButton(side,row)}<div class="ln-name" title="${safeLn(row?.nama_perkiraan || '')}">${safeLn(row?.nama_perkiraan || '')}</div></div></td>
        <td class="${passiveClass} ln-money ln-depth-${depth}" title="${fmtLnRp(row?.saldo)}">${fmtLn(row?.saldo)}</td>
      </tr>`;
  }

  function renderLnMobileTable() {
    const body = document.getElementById('lnMobileBody');
    const table = document.getElementById('lnMobileTable');
    const footLabel = document.getElementById('lnMobileFootLabel');
    const footValue = document.getElementById('lnMobileFootValue');
    if (!body || !table || !footLabel || !footValue) return;

    const query = (document.getElementById('lnSearch')?.value || '').toLowerCase().trim();
    const sideRows = getLnVisibleRows(lnMobileSide, query);
    const total = Number(lnRaw?.totals?.[lnMobileSide] || 0);

    table.classList.toggle('ln-mobile-passive', lnMobileSide === 'BIAYA');
    document.getElementById('lnMobileTotalBar')?.classList.toggle('ln-mobile-passive', lnMobileSide === 'BIAYA');
    footLabel.textContent = lnMobileSide === 'BIAYA' ? 'Total Biaya' : 'Total Pendapatan';
    footValue.textContent = fmtLnRp(total);

    if (!sideRows.length) {
      body.innerHTML = `<tr><td colspan="3" class="ln-empty">Data tidak ditemukan.</td></tr>`;
      return;
    }
    body.innerHTML = sideRows.map(row => renderMobileRow(row, lnMobileSide)).join('');
  }

  function setLnMobileSide(side) {
    lnMobileSide = side === 'BIAYA' ? 'BIAYA' : 'PENDAPATAN';
    const PENDAPATANBtn = document.getElementById('lnTabPENDAPATAN');
    const BIAYABtn = document.getElementById('lnTabBIAYA');
    PENDAPATANBtn?.classList.toggle('active', lnMobileSide === 'PENDAPATAN');
    BIAYABtn?.classList.toggle('active', lnMobileSide === 'BIAYA');
    PENDAPATANBtn?.setAttribute('aria-selected', lnMobileSide === 'PENDAPATAN' ? 'true' : 'false');
    BIAYABtn?.setAttribute('aria-selected', lnMobileSide === 'BIAYA' ? 'true' : 'false');
    renderLnMobileTable();
    document.querySelector('.ln-mobile-table-wrap')?.scrollTo({top:0,left:0,behavior:'auto'});
  }

  function updateLnBalanceStatus(labaKotor) {
    const el = document.getElementById('lnBalanceStatus');
    if (!el) return;
    el.classList.remove('ln-balance-ok','ln-balance-warn','ln-balance-bad','ln-balance-neutral');

    if (labaKotor === null || labaKotor === undefined || !Number.isFinite(Number(labaKotor))) {
      el.classList.add('ln-balance-neutral');
      el.innerHTML = `<span class="ln-status-dot"></span><span>Memuat</span>`;
      return;
    }

    const n = Number(labaKotor || 0);
    const abs = Math.abs(n);
    const base = Math.max(Math.abs(Number(lnRaw?.totals?.PENDAPATAN || 0)), 1);

    if (n > 0) {
      el.classList.add('ln-balance-ok');
      el.innerHTML = `<span class="ln-status-dot"></span><span>Laba ${fmtLnShort(abs)}</span>`;
    } else if (n === 0) {
      el.classList.add('ln-balance-warn');
      el.innerHTML = `<span class="ln-status-dot"></span><span>Impas</span>`;
    } else {
      el.classList.add('ln-balance-bad');
      el.innerHTML = `<span class="ln-status-dot"></span><span>Rugi ${fmtLnShort(abs)}</span>`;
    }
  }

  function renderLapNeraca() {
    const query = (document.getElementById('lnSearch')?.value || '').toLowerCase().trim();
    const PENDAPATAN = getLnVisibleRows('PENDAPATAN', query);
    const BIAYA = getLnVisibleRows('BIAYA', query);
    const maxRows = Math.max(PENDAPATAN.length, BIAYA.length);
    const body = document.getElementById('lnBody');

    if (maxRows === 0) {
      body.innerHTML = `<tr><td colspan="6" class="ln-empty">Data tidak ditemukan.</td></tr>`;
    } else {
      let html = '';
      for (let i = 0; i < maxRows; i++) {
        html += `<tr>${renderSide(PENDAPATAN[i], 'PENDAPATAN')}${renderSide(BIAYA[i], 'BIAYA')}</tr>`;
      }
      body.innerHTML = html;
    }

    const { pendapatan:totalPENDAPATAN, biaya:totalBIAYA, labaKotor } = getLnProfitTotals();
    const PENDAPATANRows = (lnRaw?.PENDAPATAN || []).length;
    const BIAYARows = (lnRaw?.BIAYA || []).length;

    document.getElementById('lnFootPendapatan').textContent = fmtLnRp(totalPENDAPATAN);
    document.getElementById('lnFootBiaya').textContent = fmtLnRp(totalBIAYA);
    document.getElementById('lnTabPENDAPATANTotal').textContent = fmtLnShort(totalPENDAPATAN);
    document.getElementById('lnTabBIAYATotal').textContent = fmtLnShort(totalBIAYA);
    const levelLabel = lnLevelMode === 'all' ? 'All' : `≤ ${lnLevelMode} digit`;
    document.getElementById('lnMeta').textContent = `${lnRaw?.scope_label || '-'} · Posisi ${lnRaw?.tanggal || '-'} · Level ${levelLabel} · ${PENDAPATANRows} akun PENDAPATAN · ${BIAYARows} akun BIAYA`;

    updateLnBalanceStatus(labaKotor);
    renderLnMobileTable();
    renderLnProfitSummary();
    renderLnInfo();
  }

  function renderLnInfo() {
    const summary = document.getElementById('lnInfoSummary');
    const condition = document.getElementById('lnInfoCondition');
    if (!summary || !condition) return;

    const {
      pendapatan:totalPENDAPATAN,
      biaya:totalBIAYA,
      labaKotor,
      pajak,
      labaBersih,
      consolidation
    } = getLnProfitTotals();
    const hasProfit = labaKotor > 0;

    summary.innerHTML = `
      <div class="ln-info-stat">
        <div class="ln-info-stat-label">Total Pendapatan</div>
        <div class="ln-info-stat-value blue">${fmtLnRp(totalPENDAPATAN)}</div>
      </div>
      <div class="ln-info-stat">
        <div class="ln-info-stat-label">Total Biaya</div>
        <div class="ln-info-stat-value green">${fmtLnRp(totalBIAYA)}</div>
      </div>
      <div class="ln-info-stat">
        <div class="ln-info-stat-label">Laba Kotor</div>
        <div class="ln-info-stat-value ${hasProfit ? 'green' : 'red'}">${fmtLnRp(labaKotor)}</div>
      </div>
      ${consolidation ? `
        <div class="ln-info-stat">
          <div class="ln-info-stat-label">Pajak</div>
          <div class="ln-info-stat-value red">${fmtLnRp(pajak)}</div>
        </div>
      ` : ''}
      <div class="ln-info-stat">
        <div class="ln-info-stat-label">Laba Bersih</div>
        <div class="ln-info-stat-value ${labaBersih >= 0 ? 'green' : 'red'}">${fmtLnRp(labaBersih)}</div>
      </div>
    `;

    if (hasProfit) {
      condition.innerHTML = `<b>Laba kotor ${fmtLnRp(labaKotor)}.</b> ${consolidation ? `Konsolidasi memperhitungkan pajak sebesar ${fmtLnRp(pajak)}, sehingga laba bersih menjadi ${fmtLnRp(labaBersih)}.` : `Scope ${safeLn(lnRaw?.scope_label || 'terpilih')} tidak menggunakan pengurang pajak; laba bersih mengikuti laba kotor sebesar ${fmtLnRp(labaBersih)}.`}`;
    } else {
      condition.innerHTML = `<b>Posisi rugi/impas ${fmtLnRp(labaKotor)}.</b> Perlu cek pendapatan utama dan beban terbesar pada ${safeLn(lnRaw?.scope_label || 'scope terpilih')}.`;
    }
  }

  function openLnInfo() {
    renderLnInfo();
    const modal = document.getElementById('lnInfoModal');
    modal?.classList.add('open');
    modal?.setAttribute('aria-hidden','false');
    document.documentElement.style.overflow = 'hidden';
  }

  function closeLnInfo() {
    const modal = document.getElementById('lnInfoModal');
    modal?.classList.remove('open');
    modal?.setAttribute('aria-hidden','true');
    document.documentElement.style.overflow = '';
  }

  function exportLapNeraca() {
    if (!lnRaw) return alert('Data belum dimuat.');
    const PENDAPATAN = lnRaw.PENDAPATAN || [];
    const BIAYA = lnRaw.BIAYA || [];
    const maxRows = Math.max(PENDAPATAN.length, BIAYA.length);
    let html = `<table border="1"><thead><tr><th colspan="3">PENDAPATAN</th><th colspan="3">BIAYA</th></tr><tr><th>Kode</th><th>Uraian</th><th>Saldo</th><th>Kode</th><th>Uraian</th><th>Saldo</th></tr></thead><tbody>`;
    for (let i = 0; i < maxRows; i++) {
      const a = PENDAPATAN[i] || {};
      const p = BIAYA[i] || {};
      html += `<tr><td style="mso-number-format:'\\@'">${safeLn(a.kode_perk || '')}</td><td>${safeLn(a.nama_perkiraan || '')}</td><td>${Math.round(Number(a.saldo || 0))}</td><td style="mso-number-format:'\\@'">${safeLn(p.kode_perk || '')}</td><td>${safeLn(p.nama_perkiraan || '')}</td><td>${Math.round(Number(p.saldo || 0))}</td></tr>`;
    }
    const { pendapatan, biaya, labaKotor, pajak, labaBersih, consolidation } = getLnProfitTotals();
    html += `<tr><td colspan="2"><b>Total Pendapatan</b></td><td>${Math.round(pendapatan)}</td><td colspan="2"><b>Total Biaya</b></td><td>${Math.round(biaya)}</td></tr>`;
    html += `<tr><td><b>LR_KOTOR</b></td><td colspan="4"><b>LABA RUGI KOTOR</b></td><td>${Math.round(labaKotor)}</td></tr>`;
    if (consolidation) {
      html += `<tr><td><b>PAJAK</b></td><td colspan="4"><b>PAJAK</b></td><td>${Math.round(pajak)}</td></tr>`;
    }
    html += `<tr><td><b>LR_BERSIH</b></td><td colspan="4"><b>LABA RUGI BERSIH</b></td><td>${Math.round(labaBersih)}</td></tr></tbody></table>`;
    const blob = new Blob([html], {type:'application/vnd.ms-excel'});
    const a = document.createElement('a');
    const url = URL.createObjectURL(blob);
    a.href = url;
    a.download = `lap_laba_rugi_actual_${lnRaw?.tanggal || 'data'}.xls`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  }

  window.addEventListener('DOMContentLoaded', async () => {
    await populateLnKantor();
    document.getElementById('lnTanggal').value = await loadLnDefaultDate();

    document.getElementById('lnTanggal')?.addEventListener('change', () => scheduleLapNeraca(350));
    document.getElementById('lnKantor')?.addEventListener('change', () => scheduleLapNeraca(350));
    const levelSelect = document.getElementById('lnLevel');
    if (levelSelect) {
      lnLevelMode = levelSelect.value || '5';
      levelSelect.addEventListener('change', () => {
        lnLevelMode = levelSelect.value || '5';
        rebuildLnTrees(true);
        renderLapNeraca();
        document.querySelector('.ln-table-scroll')?.scrollTo({top:0,left:0,behavior:'auto'});
        document.querySelector('.ln-mobile-table-wrap')?.scrollTo({top:0,left:0,behavior:'auto'});
      });
    }
    document.getElementById('lnSearch')?.addEventListener('input', renderLapNeraca);
    document.getElementById('lnInfoBtn')?.addEventListener('click', openLnInfo);
    document.getElementById('lnInfoClose')?.addEventListener('click', closeLnInfo);
    document.getElementById('lnInfoModal')?.addEventListener('click', e => {
      if (e.target.id === 'lnInfoModal') closeLnInfo();
    });

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && document.getElementById('lnInfoModal')?.classList.contains('open')) closeLnInfo();
    });

    fetchLapNeraca();
  });
</script>



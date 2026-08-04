<div id="flowUpdatePage" class="ufp-page">
  <header class="ufp-header">
    <div class="ufp-title-wrap">
      <span class="ufp-title-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 11l3 3L22 4"></path>
          <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
        </svg>
      </span>
      <div class="ufp-title-copy">
        <div class="ufp-title-line">
          <h1 id="judulHalaman">Update Komitmen Flow PAR</h1>
          <span id="ufpCountBadge" class="ufp-count-badge">0 data</span>
        </div>
        <p id="judul_kantor">Memuat informasi area...</p>
      </div>
    </div>

    <div class="ufp-toolbar">
      <label class="ufp-search" aria-label="Cari debitur">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="m21 21-4.35-4.35"></path>
        </svg>
        <input id="updateSearch" type="search" placeholder="Cari nama / rekening..." autocomplete="off">
      </label>

      <select id="updateCommitmentFilter" class="ufp-select" aria-label="Filter komitmen">
        <option value="ALL">Semua Komitmen</option>
        <option value="SUDAH">Sudah Komitmen</option>
        <option value="BELUM">Belum Komitmen</option>
      </select>
    </div>
  </header>

  <section id="ufpSummary" class="ufp-summary" aria-label="Ringkasan komitmen">
    <div class="ufp-summary-item">
      <span>Total Debitur</span>
      <strong id="sumDebitur">0</strong>
    </div>
    <div class="ufp-summary-item ufp-summary-blue">
      <span>Sudah Komitmen</span>
      <strong id="sumSudah">0</strong>
    </div>
    <div class="ufp-summary-item ufp-summary-orange">
      <span>Belum Komitmen</span>
      <strong id="sumBelum">0</strong>
    </div>
    <div class="ufp-summary-item ufp-summary-green">
      <span>Nominal Janji</span>
      <strong id="sumNominal">Rp 0</strong>
    </div>
  </section>

  <main class="ufp-content">
    <div id="updateLoading" class="ufp-loading">
      <span class="ufp-spinner"></span>
      <span>Memuat kandidat Flow PAR...</span>
    </div>

    <div id="upWrap" class="ufp-table-wrap">
      <table id="tblUpdate" class="ufp-table">
        <colgroup>
          <col class="ufp-col-debitur">
          <col class="ufp-col-kolek">
          <col class="ufp-col-money">
          <col class="ufp-col-money">
          <col class="ufp-col-money">
          <col class="ufp-col-dpd">
          <col class="ufp-col-dpd">
          <col class="ufp-col-date">
          <col class="ufp-col-komitmen">
          <col class="ufp-col-date">
          <col class="ufp-col-money">
          <col class="ufp-col-alasan">
          <col class="ufp-col-action">
        </colgroup>
        <thead>
          <tr id="upHead1">
            <th class="freeze-1 col-name text-left">Debitur</th>
            <th class="text-center">Kolek</th>
            <th class="text-right">Baki Debet</th>
            <th class="text-right">Tungg. Pokok</th>
            <th class="text-right">Tungg. Bunga</th>
            <th class="text-center">DPD P</th>
            <th class="text-center">DPD B</th>
            <th class="text-center">Jatuh Tempo</th>
            <th class="text-left">Komitmen</th>
            <th class="text-center">Tgl Bayar</th>
            <th class="text-right">Nominal Janji</th>
            <th class="text-left">Alasan</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody id="upTotalRow"></tbody>
        <tbody id="flowparBody"></tbody>
      </table>
    </div>

    <div id="flowparMobileList" class="ufp-mobile-list"></div>
    <div id="updateEmpty" class="ufp-empty hidden">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <circle cx="11" cy="11" r="7"></circle>
        <path d="m20 20-3.5-3.5"></path>
      </svg>
      <strong>Data tidak ditemukan</strong>
      <span>Coba ubah pencarian atau filter komitmen.</span>
    </div>
  </main>

  <nav id="ufpPagination" class="ufp-pagination hidden" aria-label="Navigasi halaman data">
    <div class="ufp-page-info" id="ufpPageInfo" aria-live="polite">Menampilkan 0 data</div>

    <div class="ufp-page-controls">
      <label class="ufp-page-size">
        <span>Baris</span>
        <select id="ufpPageSize" aria-label="Jumlah data per halaman">
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </label>

      <div class="ufp-page-buttons" id="ufpPageButtons"></div>
    </div>
  </nav>
</div>

<div id="komitmenModal" class="ufp-modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalTitleCommitment">
  <div id="modalCard" class="ufp-modal-card">
    <header class="ufp-modal-header">
      <div class="ufp-modal-heading">
        <span class="ufp-modal-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9"></path>
            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
          </svg>
        </span>
        <div>
          <h2 id="modalTitleCommitment">Update Komitmen</h2>
          <p>Catat target pembayaran agar proyeksi Flow PAR lebih terukur.</p>
        </div>
      </div>
      <button type="button" class="ufp-close-btn" onclick="closeModal()" aria-label="Tutup modal" title="Tutup">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <path d="M18 6 6 18M6 6l12 12"></path>
        </svg>
      </button>
    </header>

    <div class="ufp-modal-body">
      <input type="hidden" id="modal_rekening">

      <section class="ufp-debitur-card">
        <div class="ufp-debitur-top">
          <div class="ufp-debitur-id">
            <span>No. Rekening</span>
            <strong id="modal_rekening_text">-</strong>
          </div>
          <span id="modal_kolek" class="ufp-kolek-badge">-</span>
        </div>
        <div class="ufp-debitur-main">
          <div>
            <strong id="modal_nama">-</strong>
            <span id="modal_alamat_text">-</span>
          </div>
          <b id="modal_baki">Rp 0</b>
        </div>
        <div class="ufp-debitur-risk">
          <span>Pokok: <b id="modal_tp_text">Rp 0</b></span>
          <span>Bunga: <b id="modal_tb_text">Rp 0</b></span>
          <span>DPD P/B: <b id="modal_dpd_text">0 / 0</b></span>
        </div>
      </section>

      <div class="ufp-form-grid">
        <label class="ufp-field ufp-field-full">
          <span>Komitmen DPD</span>
          <select id="modal_komitmen" class="ufp-input"></select>
        </label>

        <label class="ufp-field">
          <span>Tanggal Bayar</span>
          <input type="date" id="modal_tanggal" class="ufp-input">
        </label>

        <label class="ufp-field">
          <span>Nominal Janji</span>
          <input type="number" id="modal_nominal" class="ufp-input" min="0" step="1" inputmode="numeric" placeholder="0">
        </label>

        <label class="ufp-field ufp-field-full">
          <span>Alasan Keterlambatan</span>
          <textarea id="modal_alasan" rows="3" class="ufp-input ufp-textarea" placeholder="Tuliskan alasan keterlambatan atau rencana tindak lanjut..."></textarea>
        </label>
      </div>
    </div>

    <footer class="ufp-modal-footer">
      <button type="button" onclick="closeModal()" class="ufp-btn ufp-btn-secondary">Batal</button>
      <button type="button" id="btnSaveKomitmen" class="ufp-btn ufp-btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
          <path d="M17 21v-8H7v8M7 3v5h8"></path>
        </svg>
        <span>Simpan Komitmen</span>
      </button>
    </footer>
  </div>
</div>

<style>
  :root {
    --ufp-primary:#2563eb;
    --ufp-bg:#f8fafc;
    --ufp-text:#334155;
    --ufp-border:#dbe3ee;
    --ufp-head-h:40px;
  }

  * { box-sizing:border-box; }
  .hidden { display:none !important; }

  .ufp-page {
    display:flex;
    flex-direction:column;
    width:100%;
    height:calc(100vh - 64px);
    height:calc(100dvh - 64px);
    min-height:430px;
    padding:8px;
    gap:7px;
    overflow:hidden;
    background:var(--ufp-bg);
    color:var(--ufp-text);
    font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
  }

  .ufp-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex:0 0 auto;
    min-width:0;
    padding:9px 11px;
    border:1px solid #e2e8f0;
    border-radius:12px;
    background:#fff;
    box-shadow:0 1px 3px rgba(15,23,42,.05);
  }

  .ufp-title-wrap { display:flex; align-items:center; gap:10px; min-width:0; }
  .ufp-title-icon {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:38px;
    height:38px;
    flex:0 0 38px;
    border-radius:10px;
    background:linear-gradient(145deg,#2563eb,#4f46e5);
    color:#fff;
    box-shadow:0 5px 12px rgba(37,99,235,.2);
  }
  .ufp-title-icon svg { width:20px; height:20px; }
  .ufp-title-copy { min-width:0; }
  .ufp-title-line { display:flex; align-items:center; gap:7px; min-width:0; }
  .ufp-title-line h1 {
    margin:0;
    overflow:hidden;
    color:#0f172a;
    font-size:18px;
    font-weight:900;
    line-height:1.1;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .ufp-title-copy p {
    margin:4px 0 0;
    overflow:hidden;
    color:#64748b;
    font-size:10px;
    font-weight:650;
    line-height:1.25;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .ufp-count-badge {
    display:inline-flex;
    align-items:center;
    height:21px;
    padding:0 7px;
    border:1px solid #bfdbfe;
    border-radius:999px;
    background:#eff6ff;
    color:#1d4ed8;
    font-size:9px;
    font-weight:850;
    white-space:nowrap;
  }

  .ufp-toolbar { display:flex; align-items:center; justify-content:flex-end; gap:7px; flex:0 0 auto; }
  .ufp-search { position:relative; display:block; width:230px; }
  .ufp-search svg {
    position:absolute;
    top:50%;
    left:10px;
    width:15px;
    height:15px;
    color:#94a3b8;
    pointer-events:none;
    transform:translateY(-50%);
  }
  .ufp-search input,
  .ufp-select,
  .ufp-input {
    width:100%;
    height:35px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    background:#fff;
    color:#334155;
    font-family:inherit;
    font-size:11px;
    font-weight:650;
    outline:none;
    transition:border-color .16s,box-shadow .16s;
  }
  .ufp-search input { padding:0 10px 0 31px; }
  .ufp-select { width:150px; padding:0 28px 0 9px; }
  .ufp-input { padding:0 10px; }
  .ufp-search input:focus,
  .ufp-select:focus,
  .ufp-input:focus {
    border-color:#3b82f6;
    box-shadow:0 0 0 3px rgba(59,130,246,.11);
  }
  .ufp-select,
  select.ufp-input {
    appearance:none;
    background-image:url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:right 8px center;
    background-size:14px;
  }

  .ufp-summary {
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:6px;
    flex:0 0 auto;
  }
  .ufp-summary-item {
    display:flex;
    align-items:center;
    justify-content:space-between;
    min-width:0;
    height:39px;
    padding:0 10px;
    border:1px solid #e2e8f0;
    border-radius:9px;
    background:#fff;
  }
  .ufp-summary-item span {
    overflow:hidden;
    color:#64748b;
    font-size:8px;
    font-weight:900;
    letter-spacing:.035em;
    text-overflow:ellipsis;
    text-transform:uppercase;
    white-space:nowrap;
  }
  .ufp-summary-item strong {
    margin-left:8px;
    overflow:hidden;
    color:#0f172a;
    font-size:12px;
    font-weight:900;
    text-overflow:ellipsis;
    white-space:nowrap;
    font-variant-numeric:tabular-nums;
  }
  .ufp-summary-blue { border-left:3px solid #3b82f6; }
  .ufp-summary-orange { border-left:3px solid #f59e0b; }
  .ufp-summary-green { border-left:3px solid #10b981; }

  .ufp-content {
    position:relative;
    display:flex;
    flex:1 1 auto;
    min-height:0;
    overflow:hidden;
    border:1px solid var(--ufp-border);
    border-radius:10px;
    background:#fff;
  }
  .ufp-loading {
    position:absolute;
    inset:0;
    z-index:120;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:9px;
    background:rgba(255,255,255,.88);
    color:#2563eb;
    font-size:11px;
    font-weight:800;
    backdrop-filter:blur(3px);
  }
  .ufp-loading.hidden { display:none; }
  .ufp-spinner {
    width:31px;
    height:31px;
    border:4px solid #dbeafe;
    border-top-color:#2563eb;
    border-radius:50%;
    animation:ufpSpin .8s linear infinite;
  }
  @keyframes ufpSpin { to { transform:rotate(360deg); } }

  .ufp-table-wrap {
    --ufp-name:210px;
    position:relative;
    width:100%;
    height:100%;
    overflow:auto;
    -webkit-overflow-scrolling:touch;
    overscroll-behavior:contain;
    scrollbar-gutter:stable;
  }
  .ufp-table-wrap::-webkit-scrollbar { width:7px; height:7px; }
  .ufp-table-wrap::-webkit-scrollbar-track { background:#f8fafc; }
  .ufp-table-wrap::-webkit-scrollbar-thumb { border-radius:999px; background:#cbd5e1; }

  .ufp-table {
    width:100%;
    min-width:1390px;
    border-collapse:separate;
    border-spacing:0;
    table-layout:fixed;
    color:#334155;
    font-size:10px;
  }
  .ufp-col-debitur { width:var(--ufp-name); }
  .ufp-col-kolek { width:64px; }
  .ufp-col-money { width:112px; }
  .ufp-col-dpd { width:58px; }
  .ufp-col-date { width:92px; }
  .ufp-col-komitmen { width:122px; }
  .ufp-col-alasan { width:150px; }
  .ufp-col-action { width:62px; }

  .ufp-table th,
  .ufp-table td {
    height:38px;
    padding:5px 7px;
    border-right:1px solid #edf2f7;
    border-bottom:1px solid #edf2f7;
    vertical-align:middle;
    white-space:nowrap;
  }
  .ufp-table th {
    position:sticky;
    top:0;
    z-index:40;
    height:39px;
    background:#eef4fb;
    color:#475569;
    font-size:8px;
    font-weight:900;
    letter-spacing:.035em;
    text-transform:uppercase;
    box-shadow:inset 0 -1px 0 #cbd5e1;
  }
  .ufp-table tbody tr:nth-child(even) td { background:#fbfdff; }
  .ufp-table tbody tr:hover td { background:#eff6ff; }

  .ufp-table .freeze-1 {
    position:sticky;
    left:0;
    z-index:28;
    width:var(--ufp-name);
    min-width:var(--ufp-name);
    max-width:var(--ufp-name);
    background:#fff;
    border-right:1px solid #cbd5e1;
    box-shadow:5px 0 10px -9px rgba(15,23,42,.85);
  }
  .ufp-table thead .freeze-1 { z-index:70; background:#dfeaf7; }
  .ufp-table tbody tr:nth-child(even) .freeze-1 { background:#fbfdff; }
  .ufp-table tbody tr:hover .freeze-1 { background:#eff6ff; }

  .ufp-total-row td {
    position:sticky;
    top:var(--ufp-head-h);
    z-index:30;
    height:39px;
    background:#eff6ff !important;
    color:#1e40af;
    font-weight:850;
    border-bottom:2px solid #bfdbfe;
    box-shadow:0 4px 7px -6px rgba(15,23,42,.6);
  }
  .ufp-total-row td.freeze-1 { z-index:60; background:#eff6ff !important; }

  .ufp-debitur-cell { min-width:0; }
  .ufp-debitur-name {
    overflow:hidden;
    color:#0f172a;
    font-size:10.5px;
    font-weight:850;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .ufp-debitur-sub {
    display:flex;
    align-items:center;
    gap:6px;
    margin-top:2px;
    overflow:hidden;
    color:#64748b;
    font-family:ui-monospace,SFMono-Regular,Menlo,monospace;
    font-size:7.5px;
    font-weight:750;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .ufp-kolek-badge,
  .ufp-status-badge {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:20px;
    padding:2px 6px;
    border-radius:999px;
    font-size:8px;
    font-weight:900;
    white-space:nowrap;
  }
  .ufp-kolek-badge { min-width:30px; background:#f1f5f9; color:#475569; }
  .ufp-kolek-badge.risk { background:#fee2e2; color:#b91c1c; }
  .ufp-status-badge.done { border:1px solid #a7f3d0; background:#ecfdf5; color:#047857; }
  .ufp-status-badge.empty { border:1px solid #fed7aa; background:#fff7ed; color:#c2410c; }

  .ufp-row-risk td { background:#fffafb !important; }
  .ufp-row-risk td.freeze-1 { background:#fffafb !important; }
  .ufp-cell-risk { background:#fff1f2 !important; color:#be123c; font-weight:900; }
  .ufp-money { text-align:right; font-variant-numeric:tabular-nums; }
  .ufp-money-main { color:#1d4ed8; font-weight:850; }
  .ufp-truncate { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

  .ufp-edit-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:30px;
    height:28px;
    border:0;
    border-radius:7px;
    background:#2563eb;
    color:#fff;
    cursor:pointer;
    transition:transform .15s,background .15s,box-shadow .15s;
  }
  .ufp-edit-btn:hover { background:#1d4ed8; transform:translateY(-1px); box-shadow:0 4px 8px rgba(37,99,235,.2); }
  .ufp-edit-btn svg { width:14px; height:14px; }

  .ufp-mobile-list { display:none; }
  .ufp-empty {
    position:absolute;
    inset:0;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:5px;
    padding:24px;
    color:#94a3b8;
    text-align:center;
  }
  .ufp-empty svg { width:36px; height:36px; }
  .ufp-empty strong { color:#64748b; font-size:12px; }
  .ufp-empty span { font-size:9px; }

  .ufp-modal {
    position:fixed;
    inset:0;
    z-index:2147483647;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:12px;
    background:rgba(15,23,42,.66);
    backdrop-filter:blur(6px);
  }
  .ufp-modal-card {
    display:flex;
    flex-direction:column;
    width:min(620px,calc(100vw - 24px));
    max-height:92dvh;
    overflow:hidden;
    border:1px solid rgba(226,232,240,.9);
    border-radius:16px;
    background:#fff;
    box-shadow:0 30px 80px rgba(15,23,42,.34);
    animation:ufpModalIn .18s ease-out;
  }
  @keyframes ufpModalIn {
    from { opacity:0; transform:translateY(8px) scale(.98); }
    to { opacity:1; transform:translateY(0) scale(1); }
  }
  .ufp-modal-header {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding:11px 13px;
    border-bottom:1px solid #e2e8f0;
    background:linear-gradient(180deg,#fff,#f8fafc);
  }
  .ufp-modal-heading { display:flex; align-items:center; gap:9px; min-width:0; }
  .ufp-modal-icon {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:35px;
    height:35px;
    flex:0 0 35px;
    border:1px solid #bfdbfe;
    border-radius:9px;
    background:#eff6ff;
    color:#2563eb;
  }
  .ufp-modal-icon svg { width:17px; height:17px; }
  .ufp-modal-heading h2 { margin:0; color:#0f172a; font-size:16px; font-weight:900; line-height:1.15; }
  .ufp-modal-heading p { margin:3px 0 0; color:#64748b; font-size:9px; font-weight:600; line-height:1.25; }
  .ufp-close-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:32px;
    height:32px;
    flex:0 0 32px;
    border:1px solid #e2e8f0;
    border-radius:8px;
    background:#fff;
    color:#64748b;
    cursor:pointer;
  }
  .ufp-close-btn:hover { border-color:#fecaca; background:#fff1f2; color:#e11d48; }
  .ufp-close-btn svg { width:15px; height:15px; }
  .ufp-modal-body { padding:12px 13px; overflow-y:auto; }

  .ufp-debitur-card {
    padding:10px;
    border:1px solid #bfdbfe;
    border-radius:11px;
    background:linear-gradient(145deg,#eff6ff,#fff);
  }
  .ufp-debitur-top { display:flex; align-items:center; justify-content:space-between; gap:8px; }
  .ufp-debitur-id { display:flex; align-items:center; gap:6px; min-width:0; }
  .ufp-debitur-id span { color:#64748b; font-size:7.5px; font-weight:900; letter-spacing:.035em; text-transform:uppercase; }
  .ufp-debitur-id strong { overflow:hidden; color:#1e3a8a; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:9px; text-overflow:ellipsis; white-space:nowrap; }
  .ufp-debitur-main { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-top:8px; }
  .ufp-debitur-main > div { min-width:0; }
  .ufp-debitur-main strong { display:block; overflow:hidden; color:#0f172a; font-size:12px; font-weight:900; text-overflow:ellipsis; white-space:nowrap; }
  .ufp-debitur-main span { display:block; margin-top:3px; overflow:hidden; color:#64748b; font-size:8px; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
  .ufp-debitur-main b { flex:0 0 auto; color:#1d4ed8; font-size:12px; font-weight:900; white-space:nowrap; }
  .ufp-debitur-risk { display:flex; flex-wrap:wrap; gap:5px 12px; margin-top:9px; padding-top:8px; border-top:1px solid #dbeafe; color:#64748b; font-size:8px; font-weight:700; }
  .ufp-debitur-risk b { color:#334155; font-weight:900; }

  .ufp-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; margin-top:12px; }
  .ufp-field { display:flex; flex-direction:column; min-width:0; }
  .ufp-field-full { grid-column:1 / -1; }
  .ufp-field > span { margin:0 0 4px 2px; color:#64748b; font-size:8px; font-weight:900; letter-spacing:.04em; text-transform:uppercase; }
  .ufp-input { height:38px; font-size:11px; }
  .ufp-textarea { height:auto; min-height:72px; padding:9px 10px; resize:vertical; line-height:1.35; }
  input[type="date"].ufp-input { position:relative; cursor:pointer; }

  .ufp-modal-footer { display:flex; justify-content:flex-end; gap:8px; padding:10px 13px; border-top:1px solid #e2e8f0; background:#f8fafc; }
  .ufp-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    min-height:36px;
    padding:0 14px;
    border-radius:9px;
    font-family:inherit;
    font-size:10px;
    font-weight:850;
    cursor:pointer;
    transition:.16s ease;
  }
  .ufp-btn svg { width:14px; height:14px; }
  .ufp-btn-secondary { border:1px solid #cbd5e1; background:#fff; color:#475569; }
  .ufp-btn-secondary:hover { background:#f1f5f9; }
  .ufp-btn-primary { border:1px solid #2563eb; background:#2563eb; color:#fff; box-shadow:0 5px 12px rgba(37,99,235,.18); }
  .ufp-btn-primary:hover { background:#1d4ed8; }
  .ufp-btn:disabled { opacity:.6; cursor:not-allowed; }



  /* =========================
     PAGINATION
     ========================= */
  .ufp-pagination {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    flex:0 0 auto;
    min-width:0;
    min-height:42px;
    padding:6px 8px;
    border:1px solid #e2e8f0;
    border-radius:10px;
    background:#fff;
    box-shadow:0 1px 3px rgba(15,23,42,.04);
  }

  .ufp-page-info {
    min-width:0;
    overflow:hidden;
    color:#64748b;
    font-size:9px;
    font-weight:750;
    text-overflow:ellipsis;
    white-space:nowrap;
  }

  .ufp-page-controls,
  .ufp-page-buttons,
  .ufp-page-size {
    display:flex;
    align-items:center;
  }

  .ufp-page-controls { gap:9px; flex:0 0 auto; }
  .ufp-page-buttons { gap:4px; }
  .ufp-page-size { gap:5px; color:#64748b; font-size:8px; font-weight:850; text-transform:uppercase; letter-spacing:.035em; }

  .ufp-page-size select {
    width:58px;
    height:29px;
    padding:0 22px 0 7px;
    border:1px solid #cbd5e1;
    border-radius:7px;
    background:#fff url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m7 10 5 5 5-5'/%3E%3C/svg%3E") no-repeat right 5px center/12px;
    color:#334155;
    font-size:9px;
    font-weight:850;
    outline:none;
    appearance:none;
  }

  .ufp-page-size select:focus {
    border-color:#3b82f6;
    box-shadow:0 0 0 3px rgba(59,130,246,.1);
  }

  .ufp-page-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:29px;
    height:29px;
    padding:0 7px;
    border:1px solid #dbe3ee;
    border-radius:7px;
    background:#fff;
    color:#475569;
    font-family:inherit;
    font-size:9px;
    font-weight:850;
    cursor:pointer;
    transition:background .15s,border-color .15s,color .15s,transform .15s;
  }

  .ufp-page-btn:hover:not(:disabled) {
    border-color:#93c5fd;
    background:#eff6ff;
    color:#1d4ed8;
    transform:translateY(-1px);
  }

  .ufp-page-btn.active {
    border-color:#2563eb;
    background:#2563eb;
    color:#fff;
    box-shadow:0 3px 8px rgba(37,99,235,.2);
  }

  .ufp-page-btn:disabled {
    opacity:.42;
    cursor:not-allowed;
  }

  .ufp-page-ellipsis {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:20px;
    height:29px;
    color:#94a3b8;
    font-size:10px;
    font-weight:900;
  }

  /* Tablet: toolbar tidak menekan judul dan summary tetap terbaca. */
  @media (min-width:768px) and (max-width:1199px) {
    .ufp-header {
      display:grid;
      grid-template-columns:minmax(0,1fr);
      gap:8px;
    }

    .ufp-toolbar {
      display:grid;
      grid-template-columns:minmax(0,1fr) 180px;
      width:100%;
    }

    .ufp-search,
    .ufp-select { width:100%; }

    .ufp-summary { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .ufp-summary-item { height:37px; }
  }

  @media (min-width:1440px) {
    .ufp-page { padding:6px; }
    .ufp-table { min-width:100%; font-size:10.5px; }
    .ufp-table-wrap { --ufp-name:220px; }
    .ufp-col-money { width:118px; }
    .ufp-col-komitmen { width:130px; }
    .ufp-col-alasan { width:170px; }
  }

  @media (min-width:768px) and (max-width:1100px) {
    .ufp-page { padding:6px; }
    .ufp-header { align-items:stretch; flex-direction:column; }
    .ufp-toolbar { width:100%; }
    .ufp-search { flex:1 1 auto; width:auto; }
    .ufp-select { width:170px; }
    .ufp-summary-item { padding:0 8px; }
    .ufp-table-wrap { --ufp-name:185px; }
    .ufp-table { min-width:1320px; }
  }

  @media (max-width:767px) {
    body { overflow:hidden; }
    .ufp-page {
      margin-top:var(--ufp-nav-overlap,0px);
      height:calc(100vh - var(--ufp-page-top,54px));
      height:calc(100dvh - var(--ufp-page-top,54px));
      min-height:0;
      padding:4px;
      gap:4px;
    }
    .ufp-header {
      flex-direction:column;
      align-items:stretch;
      gap:7px;
      padding:7px 8px;
      border-radius:9px;
    }
    .ufp-title-icon { width:31px; height:31px; flex-basis:31px; border-radius:8px; }
    .ufp-title-icon svg { width:16px; height:16px; }
    .ufp-title-line h1 { font-size:13px; }
    .ufp-title-copy p {
      display:-webkit-box;
      margin-top:2px;
      overflow:hidden;
      font-size:7.5px;
      line-height:1.15;
      white-space:normal;
      -webkit-box-orient:vertical;
      -webkit-line-clamp:2;
    }
    .ufp-count-badge { height:18px; padding:0 5px; font-size:7px; }
    .ufp-toolbar { display:grid; grid-template-columns:minmax(0,1fr) 116px; gap:5px; width:100%; }
    .ufp-search { width:100%; }
    .ufp-search input,.ufp-select { height:31px; border-radius:7px; font-size:9px; }
    .ufp-select { width:100%; padding-left:7px; }
    .ufp-search input { padding-left:27px; }
    .ufp-search svg { left:8px; width:13px; height:13px; }

    .ufp-summary {
      display:flex;
      gap:5px;
      overflow-x:auto;
      padding-bottom:1px;
      scrollbar-width:none;
    }
    .ufp-summary::-webkit-scrollbar { display:none; }
    .ufp-summary-item { min-width:118px; height:34px; padding:0 7px; border-radius:8px; }
    .ufp-summary-item span { font-size:6.5px; }
    .ufp-summary-item strong { font-size:9px; }

    .ufp-content { border-radius:8px; background:#f8fafc; }
    .ufp-table-wrap { display:none; }
    .ufp-mobile-list {
      display:grid;
      align-content:start;
      width:100%;
      height:100%;
      gap:6px;
      padding:6px 6px 20px;
      overflow-y:auto;
      -webkit-overflow-scrolling:touch;
      overscroll-behavior:contain;
    }
    .ufp-mobile-card {
      overflow:hidden;
      border:1px solid #e2e8f0;
      border-radius:10px;
      background:#fff;
      box-shadow:0 1px 2px rgba(15,23,42,.04);
    }
    .ufp-mobile-card.risk { border-color:#fecaca; }
    .ufp-mobile-card-head {
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:7px;
      padding:7px 8px 6px;
      border-bottom:1px solid #f1f5f9;
      background:linear-gradient(180deg,#fff,#fbfdff);
    }
    .ufp-mobile-card-identity { min-width:0; }
    .ufp-mobile-card-name { overflow:hidden; color:#0f172a; font-size:10px; font-weight:900; text-overflow:ellipsis; white-space:nowrap; }
    .ufp-mobile-card-rek { margin-top:2px; overflow:hidden; color:#64748b; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:7px; font-weight:800; text-overflow:ellipsis; white-space:nowrap; }
    .ufp-mobile-head-actions { display:flex; align-items:center; gap:5px; flex:0 0 auto; }
    .ufp-mobile-card .ufp-edit-btn { width:27px; height:25px; border-radius:6px; }
    .ufp-mobile-card .ufp-edit-btn svg { width:12px; height:12px; }
    .ufp-mobile-metrics { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1px; background:#e2e8f0; }
    .ufp-mobile-metric { min-width:0; padding:6px 7px; background:#fff; }
    .ufp-mobile-label { color:#64748b; font-size:5.8px; font-weight:900; letter-spacing:.025em; text-transform:uppercase; }
    .ufp-mobile-value { margin-top:2px; overflow:hidden; color:#0f172a; font-size:8.5px; font-weight:900; line-height:1.05; text-overflow:ellipsis; white-space:nowrap; font-variant-numeric:tabular-nums; }
    .ufp-mobile-value.blue { color:#1d4ed8; }
    .ufp-mobile-value.red { color:#be123c; }
    .ufp-mobile-commitment { padding:6px 8px 7px; }
    .ufp-mobile-commitment-top { display:flex; align-items:center; justify-content:space-between; gap:7px; }
    .ufp-mobile-commitment-money { color:#1d4ed8; font-size:8.5px; font-weight:900; white-space:nowrap; }
    .ufp-mobile-commitment-meta { display:grid; grid-template-columns:auto minmax(0,1fr); gap:3px 7px; margin-top:5px; color:#64748b; font-size:6.8px; line-height:1.25; }
    .ufp-mobile-commitment-meta b { color:#334155; font-weight:850; }
    .ufp-mobile-commitment-meta span:nth-child(even) { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }



    .ufp-pagination {
      min-height:38px;
      padding:4px 5px;
      gap:5px;
      border-radius:8px;
    }

    .ufp-page-info {
      flex:1 1 auto;
      font-size:7px;
    }

    .ufp-page-controls { gap:5px; }
    .ufp-page-size { gap:3px; }
    .ufp-page-size > span { display:none; }
    .ufp-page-size select {
      width:47px;
      height:27px;
      padding:0 17px 0 5px;
      background-position:right 3px center;
      background-size:10px;
      border-radius:6px;
      font-size:7.5px;
    }

    .ufp-page-buttons { gap:3px; }
    .ufp-page-btn {
      min-width:27px;
      height:27px;
      padding:0 5px;
      border-radius:6px;
      font-size:7.5px;
    }

    .ufp-page-ellipsis { min-width:12px; height:27px; font-size:8px; }

    .ufp-modal { align-items:flex-end; padding:0; }
    .ufp-modal-card {
      width:100%;
      max-height:94dvh;
      border-right:0;
      border-bottom:0;
      border-left:0;
      border-radius:16px 16px 0 0;
      animation:ufpSheetIn .2s ease-out;
    }
    @keyframes ufpSheetIn { from { transform:translateY(100%); } to { transform:translateY(0); } }
    .ufp-modal-header { padding:8px 9px; }
    .ufp-modal-icon { width:30px; height:30px; flex-basis:30px; border-radius:8px; }
    .ufp-modal-heading h2 { font-size:13px; }
    .ufp-modal-heading p { margin-top:2px; font-size:7.5px; }
    .ufp-close-btn { width:30px; height:30px; }
    .ufp-modal-body { padding:8px 9px; }
    .ufp-debitur-card { padding:8px; border-radius:9px; }
    .ufp-debitur-id span { font-size:6px; }
    .ufp-debitur-id strong { font-size:7.5px; }
    .ufp-debitur-main { margin-top:6px; }
    .ufp-debitur-main strong { font-size:10px; }
    .ufp-debitur-main span { font-size:7px; }
    .ufp-debitur-main b { font-size:10px; }
    .ufp-debitur-risk { gap:4px 8px; margin-top:7px; padding-top:6px; font-size:6.5px; }
    .ufp-form-grid { gap:7px; margin-top:8px; }
    .ufp-field > span { font-size:6.5px; }
    .ufp-input { height:34px; border-radius:7px; font-size:9px; }
    .ufp-textarea { min-height:62px; padding:8px; }
    .ufp-modal-footer { padding:8px 9px calc(8px + env(safe-area-inset-bottom)); }
    .ufp-btn { flex:1 1 0; min-height:34px; padding:0 9px; border-radius:8px; font-size:9px; }
  }

  @media (max-width:374px) {
    .ufp-toolbar { grid-template-columns:minmax(0,1fr) 105px; }
    .ufp-summary-item { min-width:108px; }
    .ufp-mobile-metrics { grid-template-columns:repeat(2,minmax(0,1fr)); }
    .ufp-title-line h1 { font-size:12px; }
    .ufp-page-info { max-width:92px; }
    .ufp-page-btn { min-width:25px; width:25px; padding:0; }
    .ufp-page-size select { width:43px; }
  }
</style>

<script>
  const ufpNumber = new Intl.NumberFormat('id-ID');
  const ufpFmt = value => ufpNumber.format(Number(value || 0));
  const ufpNum = value => Number(value || 0);

  let ufpRowsRaw = [];
  let ufpRowsFiltered = [];
  let ufpCurrentRequest = null;
  let ufpCurrentTP = 0;
  let ufpCurrentTB = 0;
  let ufpSaving = false;
  let ufpCurrentPage = 1;
  let ufpPageSize = window.innerWidth <= 767 ? 10 : 20;
  let ufpFilterTimer = null;

  function ufpId(id) { return document.getElementById(id); }
  function ufpSafe(value, fallback = '-') { return value === null || value === undefined || value === '' ? fallback : String(value); }
  function ufpEscape(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
  function ufpFormatDate(value) {
    if (!value || value === '-') return '-';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return String(value);
    return `${String(date.getDate()).padStart(2,'0')}/${String(date.getMonth()+1).padStart(2,'0')}/${date.getFullYear()}`;
  }
  function ufpHasCommitment(row) {
    return String(row.komitmen || '').trim() !== '' || String(row.tgl_pembayaran || '').trim() !== '' || ufpNum(row.nominal) > 0;
  }
  function ufpKolekClass(value) {
    return ['KL','D','M'].includes(String(value || '').toUpperCase()) ? 'risk' : '';
  }
  function ufpIsRiskRow(row, harianDate) {
    const dpdP = ufpNum(row.hari_menunggak_pokok);
    const dpdB = ufpNum(row.hari_menunggak_bunga);
    if (dpdP >= 90 || dpdB >= 90) return true;
    if (!row.tgl_jatuh_tempo) return false;
    const jt = new Date(row.tgl_jatuh_tempo);
    const ref = harianDate ? new Date(harianDate) : new Date();
    if (Number.isNaN(jt.getTime()) || Number.isNaN(ref.getTime())) return false;
    const currentMonth = jt.getMonth() === ref.getMonth() && jt.getFullYear() === ref.getFullYear();
    const prev = new Date(ref.getFullYear(), ref.getMonth() - 1, 1);
    const previousMonth = jt.getMonth() === prev.getMonth() && jt.getFullYear() === prev.getFullYear();
    return currentMonth || previousMonth;
  }

  function ufpNormalizeRows(rows, isPotensi) {
    return rows.map(row => ({
      ...row,
      no_rekening: row.no_rekening || '',
      nama_nasabah: row.nama_nasabah || '-',
      alamat: row.alamat || '-',
      kolek_harian: row.kolek_harian || row.kolektibilitas || row.kolek || row.status_potensi || '-',
      baki_debet: ufpNum(row.baki_debet_harian || row.baki_debet || row.baki_debet_closing),
      tunggakan_pokok: ufpNum(row.tunggakan_pokok),
      tunggakan_bunga: ufpNum(row.tunggakan_bunga),
      hari_menunggak_pokok: ufpNum(row.hmp_harian ?? row.hari_menunggak_pokok),
      hari_menunggak_bunga: ufpNum(row.hmb_harian ?? row.hari_menunggak_bunga),
      tgl_jatuh_tempo: row.jt_harian || row.tgl_jatuh_tempo || row.jt_closing || '',
      komitmen: row.komitmen || '',
      tgl_pembayaran: row.tgl_pembayaran || '',
      nominal: ufpNum(row.nominal),
      alasan: row.alasan || '',
      __isPotensi: isPotensi
    }));
  }

  function ufpSetStickyHeader() {
    const h = ufpId('upHead1')?.offsetHeight || 39;
    document.documentElement.style.setProperty('--ufp-head-h', `${h}px`);
  }

  async function ufpLoadRows(request) {
    const isPotensi = request.source === 'potensi_npl';
    const endpoint = isPotensi ? './api/npl/' : './api/flow_par/';
    const payload = isPotensi
      ? {
          type:'Debitur Potensi NPL',
          kode_kantor:request.kode_kantor || '',
          kode_kankas:request.kode_kankas || '',
          kode_ao:request.kode_ao || '',
          closing_date:request.closing_date,
          harian_date:request.harian_date
        }
      : {
          type:'KL Baru',
          kode_kantor:request.kode_kantor || '',
          kode_kankas:request.kode_kankas || '',
          korwil:request.korwil || '',
          klasifikasi_flow:request.klasifikasi_flow || '',
          closing_date:request.closing_date,
          harian_date:request.harian_date
        };

    const response = await fetch(endpoint, {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify(payload)
    });
    const json = await response.json();
    if (!response.ok || (json.status && Number(json.status) >= 400)) {
      throw new Error(json.message || 'Gagal mengambil data kandidat.');
    }

    let rows = Array.isArray(json.data) ? json.data : [];
    if (isPotensi) {
      const status = request.status_filter || 'ALL';
      if (status !== 'ALL') {
        rows = rows.filter(row => status === 'AMAN'
          ? row.status_potensi === 'AMAN' || row.status_potensi === 'LUNAS / AMAN'
          : row.status_potensi === status
        );
      }
    }
    return ufpNormalizeRows(rows, isPotensi);
  }

  function ufpUpdateSummary(rows) {
    const total = rows.length;
    const sudah = rows.filter(ufpHasCommitment).length;
    const nominal = rows.reduce((sum,row) => sum + ufpNum(row.nominal), 0);
    ufpId('sumDebitur').textContent = ufpFmt(total);
    ufpId('sumSudah').textContent = ufpFmt(sudah);
    ufpId('sumBelum').textContent = ufpFmt(total - sudah);
    ufpId('sumNominal').textContent = `Rp ${ufpFmt(nominal)}`;
    ufpId('ufpCountBadge').textContent = `${ufpFmt(rows.length)} data`;
  }

  function ufpActionButton(row, mobile = false) {
    const attrs = [
      ['rekening',row.no_rekening],['nama',row.nama_nasabah],['alamat',row.alamat],['kolek',row.kolek_harian],
      ['baki',row.baki_debet],['tp',row.tunggakan_pokok],['tb',row.tunggakan_bunga],
      ['dpd_p',row.hari_menunggak_pokok],['dpd_b',row.hari_menunggak_bunga],
      ['komitmen',row.komitmen],['tgl_pembayaran',row.tgl_pembayaran],['tgl_jt',row.tgl_jatuh_tempo],
      ['nominal',row.nominal],['alasan',row.alasan]
    ].map(([key,value]) => `data-${key}="${ufpEscape(value)}"`).join(' ');

    return `<button type="button" class="open-modal-btn ufp-edit-btn" ${attrs} aria-label="Update komitmen ${ufpEscape(row.nama_nasabah)}" title="Update komitmen">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
    </button>`;
  }

  function ufpRenderTable(rows, aggregateRows = rows) {
    const body = ufpId('flowparBody');
    const total = ufpId('upTotalRow');
    body.innerHTML = '';
    total.innerHTML = '';
    if (!rows.length) return;

    const sum = key => aggregateRows.reduce((acc,row) => acc + ufpNum(row[key]),0);
    const nominal = sum('nominal');
    total.innerHTML = `<tr class="ufp-total-row">
      <td class="freeze-1 col-name">TOTAL (${ufpFmt(aggregateRows.length)})</td>
      <td class="text-center">-</td>
      <td class="ufp-money">${ufpFmt(sum('baki_debet'))}</td>
      <td class="ufp-money">${ufpFmt(sum('tunggakan_pokok'))}</td>
      <td class="ufp-money">${ufpFmt(sum('tunggakan_bunga'))}</td>
      <td colspan="5"></td>
      <td class="ufp-money">${ufpFmt(nominal)}</td>
      <td colspan="2"></td>
    </tr>`;

    body.innerHTML = rows.map(row => {
      const risk = ufpIsRiskRow(row, ufpCurrentRequest?.harian_date);
      const hasCommitment = ufpHasCommitment(row);
      const dpdP = ufpNum(row.hari_menunggak_pokok);
      const dpdB = ufpNum(row.hari_menunggak_bunga);
      return `<tr class="${risk ? 'ufp-row-risk' : ''}">
        <td class="freeze-1 col-name">
          <div class="ufp-debitur-cell">
            <div class="ufp-debitur-name" title="${ufpEscape(row.nama_nasabah)}">${ufpEscape(row.nama_nasabah)}</div>
            <div class="ufp-debitur-sub" title="${ufpEscape(row.alamat)}">${ufpEscape(row.no_rekening)} · ${ufpEscape(row.alamat)}</div>
          </div>
        </td>
        <td class="text-center"><span class="ufp-kolek-badge ${ufpKolekClass(row.kolek_harian)}">${ufpEscape(row.kolek_harian)}</span></td>
        <td class="ufp-money ufp-money-main">${ufpFmt(row.baki_debet)}</td>
        <td class="ufp-money">${ufpFmt(row.tunggakan_pokok)}</td>
        <td class="ufp-money">${ufpFmt(row.tunggakan_bunga)}</td>
        <td class="text-center ${dpdP >= 90 ? 'ufp-cell-risk' : ''}">${ufpFmt(dpdP)}</td>
        <td class="text-center ${dpdB >= 90 ? 'ufp-cell-risk' : ''}">${ufpFmt(dpdB)}</td>
        <td class="text-center">${ufpFormatDate(row.tgl_jatuh_tempo)}</td>
        <td><span class="ufp-status-badge ${hasCommitment ? 'done' : 'empty'}">${hasCommitment ? ufpEscape(row.komitmen || 'Sudah') : 'Belum'}</span></td>
        <td class="text-center">${ufpFormatDate(row.tgl_pembayaran)}</td>
        <td class="ufp-money ufp-money-main">${ufpFmt(row.nominal)}</td>
        <td><span class="ufp-truncate" title="${ufpEscape(row.alasan)}">${ufpEscape(row.alasan || '-')}</span></td>
        <td class="text-center">${ufpActionButton(row)}</td>
      </tr>`;
    }).join('');
  }

  function ufpRenderMobile(rows) {
    const list = ufpId('flowparMobileList');
    list.innerHTML = rows.map(row => {
      const risk = ufpIsRiskRow(row, ufpCurrentRequest?.harian_date);
      const hasCommitment = ufpHasCommitment(row);
      return `<article class="ufp-mobile-card ${risk ? 'risk' : ''}">
        <div class="ufp-mobile-card-head">
          <div class="ufp-mobile-card-identity">
            <div class="ufp-mobile-card-name">${ufpEscape(row.nama_nasabah)}</div>
            <div class="ufp-mobile-card-rek">${ufpEscape(row.no_rekening)} · ${ufpEscape(row.alamat)}</div>
          </div>
          <div class="ufp-mobile-head-actions">
            <span class="ufp-kolek-badge ${ufpKolekClass(row.kolek_harian)}">${ufpEscape(row.kolek_harian)}</span>
            ${ufpActionButton(row,true)}
          </div>
        </div>
        <div class="ufp-mobile-metrics">
          <div class="ufp-mobile-metric"><div class="ufp-mobile-label">Baki Debet</div><div class="ufp-mobile-value blue">${ufpFmt(row.baki_debet)}</div></div>
          <div class="ufp-mobile-metric"><div class="ufp-mobile-label">Tungg. Pokok</div><div class="ufp-mobile-value">${ufpFmt(row.tunggakan_pokok)}</div></div>
          <div class="ufp-mobile-metric"><div class="ufp-mobile-label">Tungg. Bunga</div><div class="ufp-mobile-value">${ufpFmt(row.tunggakan_bunga)}</div></div>
          <div class="ufp-mobile-metric"><div class="ufp-mobile-label">DPD Pokok</div><div class="ufp-mobile-value ${ufpNum(row.hari_menunggak_pokok)>=90?'red':''}">${ufpFmt(row.hari_menunggak_pokok)}</div></div>
          <div class="ufp-mobile-metric"><div class="ufp-mobile-label">DPD Bunga</div><div class="ufp-mobile-value ${ufpNum(row.hari_menunggak_bunga)>=90?'red':''}">${ufpFmt(row.hari_menunggak_bunga)}</div></div>
          <div class="ufp-mobile-metric"><div class="ufp-mobile-label">Jatuh Tempo</div><div class="ufp-mobile-value">${ufpFormatDate(row.tgl_jatuh_tempo)}</div></div>
        </div>
        <div class="ufp-mobile-commitment">
          <div class="ufp-mobile-commitment-top">
            <span class="ufp-status-badge ${hasCommitment ? 'done' : 'empty'}">${hasCommitment ? ufpEscape(row.komitmen || 'Sudah Komitmen') : 'Belum Komitmen'}</span>
            <span class="ufp-mobile-commitment-money">Rp ${ufpFmt(row.nominal)}</span>
          </div>
          <div class="ufp-mobile-commitment-meta">
            <b>Tgl Bayar</b><span>${ufpFormatDate(row.tgl_pembayaran)}</span>
            <b>Alasan</b><span title="${ufpEscape(row.alasan)}">${ufpEscape(row.alasan || '-')}</span>
          </div>
        </div>
      </article>`;
    }).join('');
  }

  function ufpScrollResultTop() {
    const tableWrap = ufpId('upWrap');
    const mobileList = ufpId('flowparMobileList');
    if (tableWrap) tableWrap.scrollTop = 0;
    if (mobileList) mobileList.scrollTop = 0;
  }

  function ufpPageCount() {
    return Math.max(1, Math.ceil(ufpRowsFiltered.length / ufpPageSize));
  }

  function ufpVisiblePageItems(totalPages) {
    if (totalPages <= 1) return [1];

    const compact = window.innerWidth <= 480;
    const radius = compact ? 0 : 1;
    const pages = new Set([1, totalPages, ufpCurrentPage]);

    for (let page = ufpCurrentPage - radius; page <= ufpCurrentPage + radius; page++) {
      if (page >= 1 && page <= totalPages) pages.add(page);
    }

    if (!compact) {
      if (ufpCurrentPage <= 3) [2,3,4].forEach(page => page <= totalPages && pages.add(page));
      if (ufpCurrentPage >= totalPages - 2) {
        [totalPages - 3,totalPages - 2,totalPages - 1].forEach(page => page >= 1 && pages.add(page));
      }
    }

    const sorted = [...pages].sort((a,b) => a - b);
    const items = [];
    sorted.forEach((page,index) => {
      if (index > 0 && page - sorted[index - 1] > 1) items.push('...');
      items.push(page);
    });
    return items;
  }

  function ufpRenderPagination() {
    const pagination = ufpId('ufpPagination');
    const info = ufpId('ufpPageInfo');
    const buttons = ufpId('ufpPageButtons');
    const totalRows = ufpRowsFiltered.length;
    const totalPages = ufpPageCount();

    if (!pagination || !info || !buttons) return;

    pagination.classList.toggle('hidden', totalRows === 0);
    if (totalRows === 0) {
      info.textContent = 'Menampilkan 0 data';
      buttons.innerHTML = '';
      return;
    }

    ufpCurrentPage = Math.min(Math.max(1, ufpCurrentPage), totalPages);
    const start = (ufpCurrentPage - 1) * ufpPageSize + 1;
    const finish = Math.min(ufpCurrentPage * ufpPageSize, totalRows);
    info.textContent = `Menampilkan ${ufpFmt(start)}–${ufpFmt(finish)} dari ${ufpFmt(totalRows)} data`;

    const previousDisabled = ufpCurrentPage <= 1 ? 'disabled' : '';
    const nextDisabled = ufpCurrentPage >= totalPages ? 'disabled' : '';
    const pageItems = ufpVisiblePageItems(totalPages).map(item => {
      if (item === '...') return '<span class="ufp-page-ellipsis" aria-hidden="true">…</span>';
      const active = item === ufpCurrentPage;
      return `<button type="button" class="ufp-page-btn ${active ? 'active' : ''}" data-page="${item}" ${active ? 'aria-current="page"' : ''} aria-label="Halaman ${item}">${item}</button>`;
    }).join('');

    buttons.innerHTML = `
      <button type="button" class="ufp-page-btn" data-page-action="prev" ${previousDisabled} aria-label="Halaman sebelumnya">‹</button>
      ${pageItems}
      <button type="button" class="ufp-page-btn" data-page-action="next" ${nextDisabled} aria-label="Halaman berikutnya">›</button>
    `;
  }

  function ufpRenderCurrentPage({ scrollTop = false } = {}) {
    const totalPages = ufpPageCount();
    ufpCurrentPage = Math.min(Math.max(1, ufpCurrentPage), totalPages);

    const startIndex = (ufpCurrentPage - 1) * ufpPageSize;
    const pageRows = ufpRowsFiltered.slice(startIndex, startIndex + ufpPageSize);
    const hasRows = ufpRowsFiltered.length > 0;

    ufpId('updateEmpty').classList.toggle('hidden', hasRows);
    ufpRenderTable(pageRows, ufpRowsFiltered);
    ufpRenderMobile(pageRows);
    ufpUpdateSummary(ufpRowsFiltered);
    ufpRenderPagination();

    if (scrollTop) ufpScrollResultTop();
    requestAnimationFrame(ufpSetStickyHeader);
  }

  function ufpApplyFilters(resetPage = true) {
    const query = String(ufpId('updateSearch')?.value || '').trim().toLowerCase();
    const commitmentFilter = ufpId('updateCommitmentFilter')?.value || 'ALL';

    ufpRowsFiltered = ufpRowsRaw.filter(row => {
      const haystack = `${row.no_rekening} ${row.nama_nasabah} ${row.alamat} ${row.komitmen} ${row.alasan}`.toLowerCase();
      const queryMatch = !query || haystack.includes(query);
      const has = ufpHasCommitment(row);
      const commitmentMatch = commitmentFilter === 'ALL' || (commitmentFilter === 'SUDAH' ? has : !has);
      return queryMatch && commitmentMatch;
    });

    if (resetPage) ufpCurrentPage = 1;
    ufpRenderCurrentPage({ scrollTop:resetPage });
  }

  function ufpGoToPage(page) {
    const totalPages = ufpPageCount();
    const target = Math.min(Math.max(1, Number(page) || 1), totalPages);
    if (target === ufpCurrentPage) return;
    ufpCurrentPage = target;
    ufpRenderCurrentPage({ scrollTop:true });
  }

  function ufpOpenModal(button) {
    ufpCurrentTP = ufpNum(button.dataset.tp);
    ufpCurrentTB = ufpNum(button.dataset.tb);
    ufpId('modal_rekening').value = button.dataset.rekening || '';
    ufpId('modal_rekening_text').textContent = button.dataset.rekening || '-';
    ufpId('modal_nama').textContent = button.dataset.nama || '-';
    ufpId('modal_alamat_text').textContent = button.dataset.alamat || '-';
    ufpId('modal_kolek').textContent = button.dataset.kolek || '-';
    ufpId('modal_kolek').className = `ufp-kolek-badge ${ufpKolekClass(button.dataset.kolek)}`;
    ufpId('modal_baki').textContent = `Rp ${ufpFmt(button.dataset.baki)}`;
    ufpId('modal_tp_text').textContent = `Rp ${ufpFmt(button.dataset.tp)}`;
    ufpId('modal_tb_text').textContent = `Rp ${ufpFmt(button.dataset.tb)}`;
    ufpId('modal_dpd_text').textContent = `${ufpFmt(button.dataset.dpd_p)} / ${ufpFmt(button.dataset.dpd_b)}`;
    ufpId('modal_tanggal').value = button.dataset.tgl_pembayaran || '';
    ufpId('modal_nominal').value = button.dataset.nominal || '';
    ufpId('modal_alasan').value = button.dataset.alasan || '';

    const select = ufpId('modal_komitmen');
    const jtDate = button.dataset.tgl_jt ? new Date(button.dataset.tgl_jt) : null;
    const refDate = ufpCurrentRequest?.harian_date ? new Date(ufpCurrentRequest.harian_date) : new Date();
    const isJTThisMonth = jtDate && !Number.isNaN(jtDate.getTime()) && jtDate.getMonth() === refDate.getMonth() && jtDate.getFullYear() === refDate.getFullYear();
    const options = isJTThisMonth
      ? [
          ['','-- Pilih Komitmen --'],
          ['E_DPD 91-120','Flow (E_DPD 91-120)'],
          ['O_Lunas','O_Lunas']
        ]
      : [
          ['','-- Pilih Komitmen --'],
          ['A_DPD 0','A_DPD 0'],
          ['B_DPD 1-30','B_DPD 1-30'],
          ['C_DPD 31-60','C_DPD 31-60'],
          ['D_DPD 61-90','D_DPD 61-90'],
          ['E_DPD 91-120','E_DPD 91-120'],
          ['O_Lunas','O_Lunas']
        ];
    const current = button.dataset.komitmen || '';
    if (current && !options.some(([value]) => value === current)) options.push([current,current]);
    select.innerHTML = options.map(([value,label]) => `<option value="${ufpEscape(value)}">${ufpEscape(label)}</option>`).join('');
    select.value = current;

    ufpId('komitmenModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(() => select.focus(), 80);
  }

  function closeModal() {
    ufpId('komitmenModal').classList.add('hidden');
    document.body.style.overflow = '';
  }
  window.closeModal = closeModal;

  async function ufpSaveCommitment() {
    if (ufpSaving) return;
    const payload = {
      type:'Update KL Baru',
      rekening:ufpId('modal_rekening').value,
      komitmen:ufpId('modal_komitmen').value,
      tgl_pembayaran:ufpId('modal_tanggal').value,
      nominal:ufpId('modal_nominal').value,
      alasan:ufpId('modal_alasan').value.trim()
    };
    if (!payload.komitmen) return alert('Pilih komitmen terlebih dahulu.');
    if (!payload.tgl_pembayaran) return alert('Tanggal pembayaran wajib diisi.');
    if (ufpNum(payload.nominal) < 0) return alert('Nominal janji tidak valid.');

    const button = ufpId('btnSaveKomitmen');
    const label = button.querySelector('span');
    ufpSaving = true;
    button.disabled = true;
    if (label) label.textContent = 'Menyimpan...';

    try {
      const response = await fetch('./api/flow_par/', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify(payload)
      });
      const result = await response.json();
      if (!response.ok || (result.status && Number(result.status) >= 400)) {
        throw new Error(result.message || 'Gagal menyimpan komitmen.');
      }
      closeModal();
      location.reload();
    } catch (error) {
      alert(error.message || 'Gagal menyimpan komitmen.');
    } finally {
      ufpSaving = false;
      button.disabled = false;
      if (label) label.textContent = 'Simpan Komitmen';
    }
  }

  document.addEventListener('DOMContentLoaded', async () => {
    const pageSizeSelect = ufpId('ufpPageSize');
    if (pageSizeSelect) pageSizeSelect.value = String(ufpPageSize);

    const stored = sessionStorage.getItem('flowpar_update');
    const loading = ufpId('updateLoading');
    if (!stored) {
      loading.classList.add('hidden');
      ufpId('judul_kantor').textContent = 'Parameter halaman tidak ditemukan. Silakan buka kembali dari detail Flow PAR.';
      ufpId('updateEmpty').classList.remove('hidden');
      return;
    }

    try {
      ufpCurrentRequest = JSON.parse(stored);
      const isPotensi = ufpCurrentRequest.source === 'potensi_npl';
      ufpId('judulHalaman').textContent = isPotensi ? 'Update Komitmen Potensi NPL' : 'Update Komitmen Flow PAR';
      ufpId('judul_kantor').textContent = [
        isPotensi ? 'Potensi NPL' : 'Flow PAR',
        ufpCurrentRequest.korwil ? `Korwil ${ufpCurrentRequest.korwil}` : '',
        ufpCurrentRequest.kode_kantor ? `Kantor ${ufpCurrentRequest.kode_kantor}` : 'Konsolidasi',
        ufpCurrentRequest.kode_kankas ? `Kankas ${ufpCurrentRequest.kode_kankas}` : '',
        ufpCurrentRequest.kode_ao ? `AO ${ufpCurrentRequest.kode_ao}` : '',
        ufpCurrentRequest.klasifikasi_flow ? `Klasifikasi ${ufpCurrentRequest.klasifikasi_flow}` : '',
        ufpCurrentRequest.harian_date ? `Posisi ${ufpFormatDate(ufpCurrentRequest.harian_date)}` : ''
      ].filter(Boolean).join(' · ');

      ufpRowsRaw = await ufpLoadRows(ufpCurrentRequest);
      ufpApplyFilters();
    } catch (error) {
      console.error(error);
      ufpRowsRaw = [];
      ufpApplyFilters();
      ufpId('judul_kantor').textContent = error.message || 'Gagal memuat data.';
    } finally {
      loading.classList.add('hidden');
      requestAnimationFrame(ufpSetStickyHeader);
    }
  });

  document.body.addEventListener('click', event => {
    const button = event.target.closest('.open-modal-btn');
    if (button) ufpOpenModal(button);
  });

  ufpId('updateSearch')?.addEventListener('input', () => {
    clearTimeout(ufpFilterTimer);
    ufpFilterTimer = setTimeout(() => ufpApplyFilters(true), 160);
  });
  ufpId('updateCommitmentFilter')?.addEventListener('change', () => ufpApplyFilters(true));

  ufpId('ufpPageSize')?.addEventListener('change', event => {
    ufpPageSize = Math.max(1, Number(event.target.value) || 20);
    ufpCurrentPage = 1;
    ufpRenderCurrentPage({ scrollTop:true });
  });

  ufpId('ufpPageButtons')?.addEventListener('click', event => {
    const button = event.target.closest('button');
    if (!button || button.disabled) return;

    if (button.dataset.page) {
      ufpGoToPage(button.dataset.page);
      return;
    }

    if (button.dataset.pageAction === 'prev') ufpGoToPage(ufpCurrentPage - 1);
    if (button.dataset.pageAction === 'next') ufpGoToPage(ufpCurrentPage + 1);
  });
  ufpId('modal_komitmen')?.addEventListener('change', event => {
    if (event.target.value === 'O_Lunas') ufpId('modal_nominal').value = ufpCurrentTP + ufpCurrentTB;
  });
  ufpId('btnSaveKomitmen')?.addEventListener('click', ufpSaveCommitment);
  ufpId('komitmenModal')?.addEventListener('click', event => {
    if (event.target.id === 'komitmenModal') closeModal();
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && !ufpId('komitmenModal').classList.contains('hidden')) closeModal();
  });
  function ufpSyncNavbarClearance() {
    const page = ufpId('flowUpdatePage');
    if (!page) return;

    if (window.innerWidth >= 768) {
      page.style.removeProperty('--ufp-nav-overlap');
      page.style.removeProperty('--ufp-page-top');
      return;
    }

    page.style.setProperty('--ufp-nav-overlap', '0px');
    requestAnimationFrame(() => {
      const baseTop = Math.max(0, page.getBoundingClientRect().top);
      let navbarBottom = 0;
      const selectors = 'nav,header,[role="banner"],.navbar,.topbar,.app-navbar,.main-header,#navbar,#topbar,#appHeader';

      document.querySelectorAll(selectors).forEach(node => {
        if (!(node instanceof HTMLElement) || node === page || page.contains(node)) return;
        const style = getComputedStyle(node);
        if (!['fixed','sticky'].includes(style.position) || style.display === 'none' || style.visibility === 'hidden') return;
        const rect = node.getBoundingClientRect();
        if (rect.top <= 12 && rect.bottom > 0 && rect.height >= 32 && rect.height <= 120 && rect.width >= innerWidth * .6) {
          navbarBottom = Math.max(navbarBottom, rect.bottom);
        }
      });

      if (navbarBottom <= 0 && baseTop < 35) navbarBottom = 54;
      const targetTop = Math.max(baseTop, navbarBottom);
      page.style.setProperty('--ufp-nav-overlap', `${Math.max(0, Math.ceil(targetTop - baseTop))}px`);
      page.style.setProperty('--ufp-page-top', `${Math.max(0, Math.ceil(targetTop))}px`);
    });
  }

  window.addEventListener('resize', () => {
    clearTimeout(window.__ufpResizeTimer);
    window.__ufpResizeTimer = setTimeout(() => {
      ufpSetStickyHeader();
      ufpSyncNavbarClearance();
      ufpRenderPagination();
    },100);
  });

  window.addEventListener('orientationchange', () => setTimeout(ufpSyncNavbarClearance,120));
  ufpSyncNavbarClearance();
  setTimeout(ufpSyncNavbarClearance,150);
</script>

<style>
  :root {
    --primary:#2563eb;
    --bg:#f8fafc;
    --text:#0f172a;
    --line:#dbe3ef;
    --line-soft:#edf2f7;
    --total:#eaf3ff;
  }

  html, body {
    height:100%;
    margin:0;
    overflow:hidden;
    background:var(--bg);
    color:var(--text);
    font-family:Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  }

  .custom-scrollbar::-webkit-scrollbar { height:7px; width:7px; }
  .custom-scrollbar::-webkit-scrollbar-track { background:#f8fafc; border-radius:999px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background:#94a3b8; }

  @keyframes scaleUp {
    from { transform:scale(.96); opacity:0; }
    to { transform:scale(1); opacity:1; }
  }
  .animate-scale-up { animation:scaleUp .18s ease-out forwards; }

  .page-container {
    height:calc(100vh - 76px);
    display:flex;
    flex-direction:column;
    padding:1rem;
    box-sizing:border-box;
    overflow:hidden;
    gap:.75rem;
  }

  @media (max-width:767px) {
    .page-container {
      height:calc(100vh - 58px);
      padding:.55rem;
      gap:.55rem;
    }
  }

  .lgd-header-card {
    background:#fff;
    border:1px solid #cbd5e1;
    border-radius:14px;
    box-shadow:0 1px 2px rgba(15,23,42,.05);
    padding:.8rem 1rem;
    position:relative;
    flex:none;
  }

  @media (max-width:767px) {
    .lgd-header-card { padding:.6rem; border-radius:12px; }
  }

  .lgd-icon {
    width:42px;
    height:42px;
    border-radius:10px;
    background:#2563eb;
    color:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 8px 14px rgba(37,99,235,.20);
    flex:none;
  }

  @media (max-width:767px) {
    .lgd-icon { width:32px; height:32px; border-radius:8px; }
  }

  .inp {
    border:1px solid #cbd5e1;
    border-radius:.55rem;
    padding:0 .75rem;
    font-size:13px;
    font-weight:700;
    background:#f8fafc;
    height:38px;
    outline:none;
    transition:border .2s, box-shadow .2s, background .2s;
    color:#0f172a;
  }

  .inp:focus {
    background:#fff;
    border-color:var(--primary);
    box-shadow:0 0 0 3px rgba(37,99,235,.10);
  }

  .lbl {
    font-size:10px;
    color:#475569;
    font-weight:900;
    margin-bottom:4px;
    text-transform:uppercase;
    letter-spacing:.07em;
    display:block;
    white-space:nowrap;
  }

  @media (max-width:767px) {
    .inp { height:32px; font-size:10px; padding:0 .55rem; }
    .lbl { font-size:8.5px; margin-bottom:2px; }
  }

  .icon-action {
    width:38px;
    height:38px;
    border-radius:10px;
    border:0;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:transform .18s, box-shadow .18s, background .18s;
    flex:none;
  }

  .icon-action:hover {
    transform:translateY(-1px);
    box-shadow:0 5px 10px rgba(15,23,42,.12);
  }

  @media (max-width:767px) {
    .icon-action { width:32px; height:32px; border-radius:8px; }
  }

  .info-popover-lgd {
    position:absolute;
    left:18px;
    top:68px;
    width:390px;
    max-width:calc(100vw - 28px);
    background:#fff;
    border:1px solid #e2e8f0;
    border-radius:14px;
    box-shadow:0 20px 40px rgba(15,23,42,.16);
    z-index:250;
    overflow:hidden;
  }

  @media (max-width:767px) {
    .info-popover-lgd {
      left:8px;
      top:55px;
      width:calc(100vw - 18px);
    }
  }

  .info-box-lgd {
    border:1px solid #cbd5e1;
    border-radius:10px;
    background:#f8fafc;
    padding:9px 11px;
  }

  input[type="date"]::-webkit-inner-spin-button,
  input[type="date"]::-webkit-calendar-picker-indicator {
    display:none;
    -webkit-appearance:none;
  }
  input[type="date"] { -moz-appearance:textfield; }

  #lgdScroller {
    flex:1;
    min-height:0;
    position:relative;
    border:1px solid #dbe3ef;
    border-radius:12px;
    background:#fff;
    overflow:auto;
    box-shadow:0 1px 3px rgba(15,23,42,.06);
  }

  #tabelLGD {
    border-collapse:separate;
    border-spacing:0;
    width:100%;
    min-width:980px;
    table-layout:fixed;
    font-size:12px;
    color:#0f172a;
  }

  #tabelLGD th,
  #tabelLGD td {
    white-space:nowrap;
    vertical-align:middle;
    border-bottom:1px solid var(--line-soft);
    border-right:1px solid var(--line-soft);
    padding:10px 14px;
    background-clip:padding-box;
  }

  #tabelLGD thead th {
    position:sticky;
    top:0;
    z-index:60;
    background:#f8fafc;
    color:#334155;
    font-weight:900;
    text-transform:uppercase;
    font-size:10px;
    letter-spacing:.04em;
    height:40px;
    box-shadow:inset 0 -1px 0 #cbd5e1;
    border-bottom:0;
  }

  #lgdTotalRow td {
    position:sticky;
    top:40px;
    z-index:58;
    background:var(--total) !important;
    color:#0d3a91;
    font-weight:900;
    height:42px;
    box-shadow:inset 0 -1px 0 #93c5fd;
    border-bottom:0;
  }

  #lgdBody td {
    height:40px;
    font-size:12px;
    color:#0f172a;
  }

  #lgdBody tr:hover td { background-color:#f8fafc !important; }

  .sticky-left-1 {
    position:sticky !important;
    left:0 !important;
    z-index:40;
    background:#fff !important;
    width:66px;
    min-width:66px;
    max-width:66px;
    text-align:center;
    box-shadow:inset -1px 0 0 #dbe3ef;
  }

  .sticky-left-2 {
    position:sticky !important;
    left:66px !important;
    z-index:39;
    background:#fff !important;
    width:220px;
    min-width:220px;
    max-width:220px;
    text-align:left;
    box-shadow:inset -1px 0 0 #dbe3ef;
  }

  #tabelLGD thead th.sticky-left-1,
  #tabelLGD thead th.sticky-left-2 {
    z-index:80;
    background:#f8fafc !important;
  }

  #lgdTotalRow td.sticky-left-1,
  #lgdTotalRow td.sticky-left-2 {
    z-index:78;
    background:var(--total) !important;
  }

  @media (max-width:767px) {
    #tabelLGD { min-width:900px; font-size:10px; }
    #tabelLGD th, #tabelLGD td { padding:8px 10px; }
    #tabelLGD thead th { font-size:8.5px; height:34px; }
    #lgdTotalRow td { top:34px; height:38px; }
    #lgdBody td { height:36px; font-size:10px; }
    .sticky-left-1 { display:none !important; }
    .sticky-left-2 {
      left:0 !important;
      width:150px;
      min-width:150px;
      max-width:150px;
    }
  }

  #modalBodyScrollLGD {
    position:relative;
    overflow:auto;
  }

  #modalTableLGD {
    border-collapse:separate;
    border-spacing:0;
    width:max-content;
    min-width:1320px;
    table-layout:fixed;
    font-size:12px;
    color:#0f172a;
  }

  #modalTableLGD th,
  #modalTableLGD td {
    white-space:nowrap;
    border-bottom:1px solid #edf2f7;
    border-right:1px solid #edf2f7;
    padding:10px 12px;
    background-clip:padding-box;
  }

  #modalTableLGD th {
    position:sticky !important;
    top:0 !important;
    z-index:50;
    background:#f8fafc !important;
    color:#334155;
    font-weight:900;
    text-transform:uppercase;
    font-size:10px;
    letter-spacing:.04em;
    height:42px;
    box-shadow:inset 0 -1px 0 #cbd5e1;
    border-bottom:0;
  }

  .modal-freeze-rek,
  .modal-td-rek {
    position:sticky !important;
    left:0 !important;
    width:130px;
    min-width:130px;
    max-width:130px;
    z-index:35;
    background:#fff !important;
    box-shadow:inset -1px 0 0 #dbe3ef;
  }

  .modal-freeze-nama,
  .modal-td-nama {
    position:sticky !important;
    left:130px !important;
    width:240px;
    min-width:240px;
    max-width:240px;
    z-index:34;
    background:#fff !important;
    box-shadow:inset -1px 0 0 #dbe3ef;
  }

  #modalTableLGD th.modal-freeze-rek,
  #modalTableLGD th.modal-freeze-nama {
    z-index:75 !important;
    background:#f8fafc !important;
  }

  #modalBodyLGD tr:hover td { background:#f8fafc !important; }
  #modalBodyLGD tr:hover td.modal-td-rek,
  #modalBodyLGD tr:hover td.modal-td-nama { background:#f8fafc !important; }

  @media (max-width:767px) {
    #modalTableLGD { min-width:1180px; font-size:10px; }
    #modalTableLGD th { font-size:8.5px; height:38px; }
    #modalTableLGD th, #modalTableLGD td { padding:8px 9px; }

    .modal-freeze-rek,
    .modal-td-rek {
      width:108px;
      min-width:108px;
      max-width:108px;
    }

    .modal-freeze-nama,
    .modal-td-nama {
      left:108px !important;
      width:170px;
      min-width:170px;
      max-width:170px;
    }
  }
</style>

<div class="page-container">
  <div class="lgd-header-card">
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3 w-full">
      <div class="flex items-center justify-between gap-2 w-full xl:w-auto">
        <div class="flex items-center gap-2 md:gap-3 min-w-0">
          <span class="lgd-icon">
            <svg class="w-4 h-4 md:w-5 md:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 3v18h18"></path>
              <path d="M7 14l4-4 3 3 5-7"></path>
            </svg>
          </span>

          <div class="min-w-0">
            <div class="flex items-center gap-2 min-w-0">
              <h1 class="text-[15px] md:text-2xl font-extrabold text-slate-900 tracking-tight leading-none truncate">Rekap LGD</h1>
              <button type="button" onclick="toggleInfoLGD()" class="w-4 h-4 md:w-5 md:h-5 rounded-full bg-blue-500 text-white flex items-center justify-center text-[10px] md:text-xs font-black hover:bg-blue-600 transition shrink-0" title="Informasi LGD">i</button>
            </div>
            <p class="text-[9px] md:text-[11px] text-slate-500 mt-1 italic truncate">*Loss Given Default (Hapus Buku vs Recovery)</p>
          </div>

          <span id="badgeUnitLGD" class="hidden"></span>
        </div>

        <button type="button" onclick="toggleFilterLGD()" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] whitespace-nowrap shrink-0">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
          </svg>
          Filter
        </button>
      </div>

      <form id="filterFormLGD" class="hidden xl:flex w-full xl:w-auto flex-wrap items-end gap-2" onsubmit="event.preventDefault(); fetchLGD();">
        <div class="flex flex-col w-[130px] md:w-[150px]">
          <label class="lbl">Posisi Data</label>
          <input type="date" id="end_date_lgd" class="inp shadow-sm" required onchange="fetchLGD()" onclick="try{this.showPicker()}catch(e){}">
        </div>

        <button type="button" onclick="exportLGDExcel()" class="icon-action bg-emerald-600 hover:bg-emerald-700 text-white" title="Download Rekap">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
        </button>
      </form>
    </div>

    <div id="infoLGD" class="info-popover-lgd hidden">
      <div class="px-4 py-3 border-b border-slate-100">
        <h3 class="text-sm font-black text-slate-900">Informasi LGD</h3>
      </div>
      <div class="px-4 py-3 text-[11px] md:text-xs text-slate-700 leading-relaxed space-y-2">
        <p><b>LGD</b> adalah persentase potensi kerugian dari portofolio hapus buku setelah memperhitungkan recovery.</p>
        <div class="info-box-lgd"><b>BD Hapus Buku:</b> baki debet nasabah yang sudah masuk posisi hapus buku.</div>
        <div class="info-box-lgd"><b>Recovery Nominal:</b> pembayaran/recovery aktual yang diterima.</div>
        <div class="info-box-lgd"><b>Recovery NPV:</b> nilai recovery setelah penyesuaian nilai kini.</div>
        <div class="info-box-lgd"><b>RR (%):</b> recovery rate terhadap baki debet hapus buku.</div>
        <div class="info-box-lgd"><b>LGD (%):</b> sisa potensi loss setelah recovery.</div>
        <div class="pt-1 border-t border-slate-200 font-bold text-slate-900">Semakin rendah LGD, semakin baik efektivitas recovery.</div>
      </div>
    </div>
  </div>

  <div id="lgdScroller" class="custom-scrollbar">
    <div id="loadingLGD" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
      <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2"></div>
      <span class="text-[10px] md:text-sm font-bold tracking-widest uppercase">Memuat Data...</span>
    </div>

    <table id="tabelLGD">
      <thead>
        <tr>
          <th class="sticky-left-1">Kode</th>
          <th class="sticky-left-2 text-left">Nama Kantor</th>
          <th class="text-center w-[80px]">NOA</th>
          <th class="text-right">Baki Debet HB</th>
          <th class="text-right text-emerald-700">Rec. Nominal</th>
          <th class="text-right">Rec. NPV</th>
          <th class="text-right">Sisa Saldo</th>
          <th class="text-right">RR (%)</th>
          <th class="text-right">LGD (%)</th>
        </tr>
        <tr id="lgdTotalRow"></tr>
      </thead>
      <tbody id="lgdBody"></tbody>
    </table>
  </div>
</div>

<div id="modalLGD" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm items-end md:items-center justify-center z-[9999] p-0 md:p-4">
  <div id="modalCardLGD" class="bg-white rounded-t-xl md:rounded-2xl shadow-2xl flex flex-col w-full max-w-[1600px] h-[95vh] md:h-[92vh] overflow-hidden animate-scale-up">
    <div class="flex items-center justify-between gap-2 px-3 md:px-5 py-3 border-b bg-white shrink-0">
      <div class="min-w-0">
        <h3 class="font-extrabold text-slate-900 text-[13px] md:text-xl flex items-center gap-2 leading-none truncate">
          <span class="w-1.5 h-5 bg-blue-600 rounded-full hidden md:block"></span>
          <span class="truncate">Detail LGD Belum Lunas</span>
        </h3>
        <p class="text-[9px] md:text-xs text-slate-500 mt-1 md:ml-4 font-mono truncate" id="lblModalSubtitleLGD"></p>
      </div>

      <div class="flex items-center gap-1.5 md:gap-2 shrink-0">
        <button onclick="exportDetailLGDExcel()" class="icon-action bg-emerald-600 hover:bg-emerald-700 text-white" title="Download Detail">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
        </button>
        <button onclick="closeModalLGD()" class="icon-action bg-slate-100 hover:bg-red-500 hover:text-white text-slate-600 text-xl font-bold" title="Tutup">&times;</button>
      </div>
    </div>

    <div id="modalBodyScrollLGD" class="flex-1 min-h-0 overflow-auto p-1 md:p-3 bg-slate-50 custom-scrollbar">
      <table id="modalTableLGD" class="bg-white border border-slate-200 md:rounded-xl shadow-sm">
        <thead>
          <tr>
            <th class="modal-freeze-rek text-center">No Rekening</th>
            <th class="modal-freeze-nama text-left cursor-pointer hover:bg-blue-50" onclick="sortModal('nama_nasabah')">Nama Nasabah ⇅</th>
            <th class="text-right w-[140px] cursor-pointer hover:bg-blue-50" onclick="sortModal('balance_hapus_buku')">BD Hapus Buku ⇅</th>
            <th class="text-center w-[90px]">Tahun PH</th>
            <th class="text-center w-[80px]">Bunga</th>
            <th class="text-right text-emerald-700 w-[130px]">Rec. Nominal</th>
            <th class="text-right w-[130px]">Rec. NPV</th>
            <th class="text-right w-[80px]">RR (%)</th>
            <th class="text-right font-bold text-red-600 w-[90px]">LGD (%)</th>
            <th class="text-right w-[130px]">Sisa Saldo</th>
            <th class="text-right w-[130px]">Pokok Lalu</th>
            <th class="text-right text-blue-600 w-[140px]">Pokok Berjalan</th>
          </tr>
        </thead>
        <tbody id="modalBodyLGD"></tbody>
      </table>
    </div>
  </div>
</div>

<div id="modalWarnLGD" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm items-center justify-center z-[10000] px-4">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden p-6 text-center animate-scale-up border-t-4 border-red-500">
    <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">⚠️</div>
    <h3 class="font-bold text-slate-800 text-xl mb-2">Akses Ditolak</h3>
    <p class="text-slate-500 text-sm mb-6">Anda login sebagai Cabang <span class="font-bold text-blue-600" id="warnUserLGD"></span>. Anda tidak diijinkan membuka detail nasabah milik <span class="font-bold text-red-500" id="warnTargetLGD"></span>.</p>
    <button onclick="closeModalWarnLGD()" class="w-full py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold transition">Mengerti</button>
  </div>
</div>

<script>
  const nfLGD = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });
  const fmtLGD = n => nfLGD.format(Number(n || 0));
  const numLGD = v => Number(v || 0);

  let lgdDataList = [];
  let detailLgdRaw = [];
  let currentEndDate = '';
  let sortState = { col: '', dir: 1 };

  function escapeHtmlLGD(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function toggleFilterLGD() {
    const el = document.getElementById('filterFormLGD');
    if (!el) return;
    el.classList.toggle('hidden');
    el.classList.toggle('flex');
  }

  function toggleInfoLGD() {
    const el = document.getElementById('infoLGD');
    if (!el) return;
    el.classList.toggle('hidden');
  }

  document.addEventListener('click', function(e) {
    const info = document.getElementById('infoLGD');
    if (!info) return;
    const btn = e.target.closest('[onclick="toggleInfoLGD()"]');
    const box = e.target.closest('#infoLGD');
    if (!btn && !box) info.classList.add('hidden');
  });

  window.addEventListener('DOMContentLoaded', async () => {
    const user = (window.getUser && window.getUser()) || null;
    const uKode = user?.kode ? String(user.kode).padStart(3, '0') : '000';
    window.currentUser = { kode: uKode };

    const badge = document.getElementById('badgeUnitLGD');
    if (badge) badge.innerText = (uKode === '000') ? 'KONSOLIDASI' : `CABANG: ${uKode}`;

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('end_date_lgd').value = today;
    currentEndDate = today;

    fetchLGD();
  });

  document.getElementById('filterFormLGD').addEventListener('submit', e => {
    e.preventDefault();
    fetchLGD();
  });

  async function fetchLGD() {
    currentEndDate = document.getElementById('end_date_lgd')?.value || currentEndDate;

    const loading = document.getElementById('loadingLGD');
    const tbody = document.getElementById('lgdBody');
    const ttotal = document.getElementById('lgdTotalRow');

    loading.classList.remove('hidden');
    tbody.innerHTML = `<tr><td colspan="9" class="text-center py-16 text-slate-400 italic text-xs md:text-sm">Sedang mengambil data...</td></tr>`;
    ttotal.innerHTML = '';

    try {
      const res = await fetch('./api/hapus_buku/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'get lgd', end_date: currentEndDate })
      });

      const json = await res.json();

      if (json.status && Number(json.status) !== 200) {
        throw new Error(json.message || 'Gagal memuat rekap.');
      }

      lgdDataList = Array.isArray(json.data) ? json.data : [];
      renderLGD(lgdDataList);
    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="9" class="text-center py-16 text-red-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">${escapeHtmlLGD(e.message || 'Gagal memuat rekap.')}</td></tr>`;
    } finally {
      loading.classList.add('hidden');
    }
  }

  function renderLGD(rows) {
    const tbody = document.getElementById('lgdBody');
    const ttotal = document.getElementById('lgdTotalRow');

    tbody.innerHTML = '';
    ttotal.innerHTML = '';

    if (!rows || rows.length === 0) {
      tbody.innerHTML = `<tr><td colspan="9" class="text-center py-16 text-slate-400 italic text-xs md:text-sm">Tidak ada data.</td></tr>`;
      return;
    }

    const totalObj = rows.find(r => {
      const nama = String(r.nama_kantor || '').toUpperCase();
      return nama.includes('KONSOLIDASI') || String(r.kode_kantor || '').toUpperCase() === 'TOTAL';
    });

    if (totalObj) {
      ttotal.innerHTML = `
        <td class="sticky-left-1">ALL</td>
        <td class="sticky-left-2 uppercase">${escapeHtmlLGD(totalObj.nama_kantor || 'KONSOLIDASI')}</td>
        <td class="text-center">${fmtLGD(totalObj.noa)}</td>
        <td class="text-right">${fmtLGD(totalObj.total_balance_ph)}</td>
        <td class="text-right text-emerald-700">${fmtLGD(totalObj.total_recovery_nominal)}</td>
        <td class="text-right">${fmtLGD(totalObj.total_recovery_npv)}</td>
        <td class="text-right">${fmtLGD(totalObj.sisa_saldo_nominal)}</td>
        <td class="text-right">${numLGD(totalObj.persen_rr).toFixed(2)}%</td>
        <td class="text-right text-red-600">${numLGD(totalObj.persen_lgd).toFixed(2)}%</td>
      `;
    }

    const branches = rows.filter(r => r !== totalObj);

    tbody.innerHTML = branches.map(r => {
      const kode = String(r.kode_kantor || '').padStart(3, '0');
      const isOk = (window.currentUser.kode === '000' || window.currentUser.kode === kode);
      const hasDetail = numLGD(r.noa) > 0;

      const linkCls = hasDetail
        ? (isOk ? "text-blue-600 font-bold cursor-pointer hover:underline" : "text-slate-400 font-bold cursor-pointer")
        : "text-slate-300";

      return `
        <tr>
          <td class="sticky-left-1 font-mono text-slate-400">${escapeHtmlLGD(r.kode_kantor || '')}</td>
          <td class="sticky-left-2 ${linkCls}" onclick="handleLGDDetailSecurity('${escapeHtmlLGD(kode)}', '${escapeHtmlLGD(r.nama_kantor || '')}', ${numLGD(r.noa)})">${escapeHtmlLGD(r.nama_kantor || '-')}</td>
          <td class="text-center">${fmtLGD(r.noa)}</td>
          <td class="text-right">${fmtLGD(r.total_balance_ph)}</td>
          <td class="text-right text-emerald-600 font-bold">${fmtLGD(r.total_recovery_nominal)}</td>
          <td class="text-right text-slate-500">${fmtLGD(r.total_recovery_npv)}</td>
          <td class="text-right text-slate-500">${fmtLGD(r.sisa_saldo_nominal)}</td>
          <td class="text-right font-semibold">${numLGD(r.persen_rr).toFixed(2)}%</td>
          <td class="text-right font-bold text-red-500">${numLGD(r.persen_lgd).toFixed(2)}%</td>
        </tr>
      `;
    }).join('');
  }

  window.handleLGDDetailSecurity = function(kode, nama, noa) {
    if (numLGD(noa) === 0) return;

    if (window.currentUser.kode !== '000' && window.currentUser.kode !== kode) {
      document.getElementById('warnUserLGD').innerText = window.currentUser.kode;
      document.getElementById('warnTargetLGD').innerText = nama;
      document.getElementById('modalWarnLGD').classList.remove('hidden');
      document.getElementById('modalWarnLGD').classList.add('flex');
      return;
    }

    openDetailLGD(kode, nama);
  };

  async function openDetailLGD(kode, nama) {
    const modal = document.getElementById('modalLGD');
    const tbody = document.getElementById('modalBodyLGD');

    document.getElementById('lblModalSubtitleLGD').innerText = `${kode} - ${nama} | POSISI: ${currentEndDate}`;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    tbody.innerHTML = `<tr><td colspan="12" class="text-center py-16 uppercase text-xs font-bold tracking-widest text-slate-400">Sedang Menyiapkan Data...</td></tr>`;

    try {
      const res = await fetch('./api/hapus_buku/', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: 'detail lgd blm lunas', end_date: currentEndDate, kode_kantor: kode })
      });

      const json = await res.json();

      if (json.status && Number(json.status) !== 200) {
        throw new Error(json.message || 'Gagal memuat detail.');
      }

      detailLgdRaw = Array.isArray(json.data) ? json.data : [];
      renderDetail(detailLgdRaw);
    } catch (e) {
      tbody.innerHTML = `<tr><td colspan="12" class="text-center py-16 text-red-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">${escapeHtmlLGD(e.message || 'Gagal memuat detail.')}</td></tr>`;
    }
  }

  function renderDetail(list) {
    const tbody = document.getElementById('modalBodyLGD');

    if (!list || list.length === 0) {
      tbody.innerHTML = `<tr><td colspan="12" class="text-center py-16 text-slate-400 italic text-xs md:text-sm">Tidak ada data detail.</td></tr>`;
      return;
    }

    tbody.innerHTML = list.map(d => `
      <tr>
        <td class="modal-td-rek text-center font-mono text-slate-500">${escapeHtmlLGD(d.no_rekening || '')}</td>
        <td class="modal-td-nama font-bold text-slate-700 truncate" title="${escapeHtmlLGD(d.nama_nasabah || '-')}">${escapeHtmlLGD(d.nama_nasabah || '-')}</td>
        <td class="text-right">${fmtLGD(d.balance_hapus_buku)}</td>
        <td class="text-center">${escapeHtmlLGD(d.tahun_ph || '-')}</td>
        <td class="text-center">${escapeHtmlLGD(d.suku_bunga_efektif || '0')}%</td>
        <td class="text-right text-emerald-600 font-bold">${fmtLGD(d.total_recovery_nominal)}</td>
        <td class="text-right">${fmtLGD(d.jumlah_recovery_npv)}</td>
        <td class="text-right">${numLGD(d.recovery_rate_npv).toFixed(2)}%</td>
        <td class="text-right font-bold text-red-600 bg-red-50">${numLGD(d.lgd_persen).toFixed(2)}%</td>
        <td class="text-right font-bold text-blue-700">${fmtLGD(d.sisa_saldo_nominal)}</td>
        <td class="text-right text-slate-400">${fmtLGD(d.pokok_bulan_lalu)}</td>
        <td class="text-right text-blue-600 font-medium">${fmtLGD(d.pokok_bulan_berjalan)}</td>
      </tr>
    `).join('');
  }

  window.sortModal = function(col) {
    sortState.dir = (sortState.col === col) ? -sortState.dir : 1;
    sortState.col = col;

    detailLgdRaw.sort((a, b) => {
      let vA = a[col];
      let vB = b[col];

      if (!isNaN(Number(vA)) && !isNaN(Number(vB))) {
        vA = Number(vA);
        vB = Number(vB);
      } else {
        vA = String(vA || '').toLowerCase();
        vB = String(vB || '').toLowerCase();
      }

      if (vA > vB) return 1 * sortState.dir;
      if (vA < vB) return -1 * sortState.dir;
      return 0;
    });

    renderDetail(detailLgdRaw);
  };

  function closeModalLGD() {
    document.getElementById('modalLGD').classList.add('hidden');
    document.getElementById('modalLGD').classList.remove('flex');
  }

  function closeModalWarnLGD() {
    document.getElementById('modalWarnLGD').classList.add('hidden');
    document.getElementById('modalWarnLGD').classList.remove('flex');
  }

  function downloadExcelFromTable(filename, html) {
    const blob = new Blob(["\ufeff" + html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    a.click();
    URL.revokeObjectURL(a.href);
  }

  function exportLGDExcel() {
    if (lgdDataList.length === 0) return;

    let table = `<table border="1"><thead><tr style="background:#f1f5f9">
      <th>KODE</th><th>KANTOR</th><th>NOA</th><th>BAKI DEBET HB</th><th>REC NOMINAL</th><th>REC NPV</th><th>SISA SALDO</th><th>RR %</th><th>LGD %</th>
    </tr></thead><tbody>`;

    lgdDataList.forEach(r => {
      table += `<tr>
        <td style="mso-number-format:'\\@'">${escapeHtmlLGD(r.kode_kantor || '')}</td>
        <td>${escapeHtmlLGD(r.nama_kantor || '')}</td>
        <td>${numLGD(r.noa)}</td>
        <td>${numLGD(r.total_balance_ph)}</td>
        <td>${numLGD(r.total_recovery_nominal)}</td>
        <td>${numLGD(r.total_recovery_npv)}</td>
        <td>${numLGD(r.sisa_saldo_nominal)}</td>
        <td>${numLGD(r.persen_rr)}</td>
        <td>${numLGD(r.persen_lgd)}</td>
      </tr>`;
    });

    downloadExcelFromTable(`Rekap_LGD_${currentEndDate}.xls`, table + `</tbody></table>`);
  }

  function exportDetailLGDExcel() {
    if (detailLgdRaw.length === 0) return;

    let table = `<table border="1"><thead><tr style="background:#f1f5f9">
      <th>NO REKENING</th><th>NAMA NASABAH</th><th>BD HB</th><th>THN PH</th><th>BUNGA</th><th>REC NOMINAL</th><th>REC NPV</th><th>RR %</th><th>LGD %</th><th>SISA SALDO</th><th>POKOK LALU</th><th>POKOK BERJALAN</th>
    </tr></thead><tbody>`;

    detailLgdRaw.forEach(d => {
      table += `<tr>
        <td style="mso-number-format:'\\@'">${escapeHtmlLGD(d.no_rekening || '')}</td>
        <td>${escapeHtmlLGD(d.nama_nasabah || '')}</td>
        <td>${numLGD(d.balance_hapus_buku)}</td>
        <td>${escapeHtmlLGD(d.tahun_ph || '')}</td>
        <td>${escapeHtmlLGD(d.suku_bunga_efektif || '')}</td>
        <td>${numLGD(d.total_recovery_nominal)}</td>
        <td>${numLGD(d.jumlah_recovery_npv)}</td>
        <td>${numLGD(d.recovery_rate_npv)}</td>
        <td>${numLGD(d.lgd_persen)}</td>
        <td>${numLGD(d.sisa_saldo_nominal)}</td>
        <td>${numLGD(d.pokok_bulan_lalu)}</td>
        <td>${numLGD(d.pokok_bulan_berjalan)}</td>
      </tr>`;
    });

    downloadExcelFromTable(`Detail_LGD_${currentEndDate}.xls`, table + `</tbody></table>`);
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      closeModalLGD();
      closeModalWarnLGD();
    }
  });
</script>

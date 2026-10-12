<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

$tahunSekarang = (int)date('Y');
$realisasiTahunOptions = [];
foreach ([$tahunSekarang, $tahunSekarang - 1, $tahunSekarang - 2] as $tahun) {
    $realisasiTahunOptions[(string)$tahun] = [
        'label' => (string)$tahun,
        'checked' => false,
        'attrs' => ['data-realisasi-year' => (string)$tahun],
    ];
}
ob_start();
mb_render_field([
    'id' => 'realisasi_tahun_produk',
    'label' => 'Tahun Realisasi',
    'type' => 'checklist-dropdown',
    'width' => '150px',
    'field_class' => 'produk-toolbar-year',
    'options' => $realisasiTahunOptions,
]);
$produkYearToolbar = ob_get_clean();
?>

<style>
  .produk-page { height:calc(100vh - 80px); min-height:520px; }
  .produk-filter { display:flex; align-items:flex-end; gap:8px; padding:10px; border:1px solid #dbe3ee; border-radius:12px; background:#fff; box-shadow:0 1px 3px rgba(15,23,42,.05); }
  .produk-field { display:flex; flex-direction:column; min-width:0; }
  .produk-field label { margin:0 0 3px 3px; color:#64748b; font-size:8px; font-weight:900; letter-spacing:.06em; text-transform:uppercase; }
  .produk-input { height:34px; min-width:0; padding:0 9px; border:1px solid #cbd5e1; border-radius:8px; background:#fff; color:#334155; font-size:10px; font-weight:750; outline:0; }
  .produk-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.10); }
  .produk-card { min-height:0; border:1px solid #dbe3ee; border-radius:12px; background:#fff; box-shadow:0 8px 24px rgba(15,23,42,.05); overflow:hidden; }
  .produk-card-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 12px; border-bottom:1px solid #e2e8f0; }
  .produk-card-title { color:#172033; font-size:12px; font-weight:950; }
  .produk-note { color:#64748b; font-size:9px; line-height:1.35; }
  .produk-table-wrap { flex:1 1 auto; min-height:0; overflow:auto; border-radius:0 0 12px 12px; -webkit-overflow-scrolling:touch; }
  .produk-table { width:max-content; min-width:100%; border-collapse:separate; border-spacing:0; color:#334155; font-size:10px; }
  .produk-table th, .produk-table td { padding:7px 9px; border-right:1px solid #e2e8f0; border-bottom:1px solid #e5eaf1; white-space:nowrap; vertical-align:middle; }
  .produk-table thead th { position:sticky; top:0; z-index:20; background:#eaf4ff; color:#173b72; font-size:8px; font-weight:950; text-align:center; text-transform:uppercase; }
  .produk-table thead th:nth-child(1), .produk-table tbody td:nth-child(1) { position:sticky; left:0; z-index:12; width:78px; min-width:78px; background:#fff; }
  .produk-table thead th:nth-child(2), .produk-table tbody td:nth-child(2) { position:sticky; left:78px; z-index:12; width:190px; min-width:190px; text-align:left; background:#fff; box-shadow:4px 0 8px -8px rgba(15,23,42,.9); }
  .produk-table thead th:nth-child(1), .produk-table thead th:nth-child(2) { z-index:30; background:#eaf4ff; }
  .produk-table tbody tr:hover td { background:#f8fbff; }
  .produk-table tbody td:nth-child(n+3) { text-align:right; }
  .produk-table .produk-total td { position:sticky; top:31px; z-index:16; background:#eff6ff; color:#173b72; font-weight:900; border-bottom:2px solid #bfdbfe; }
  .produk-table .produk-total td:nth-child(1), .produk-table .produk-total td:nth-child(2) { z-index:25; background:#eff6ff; }
  .produk-table .produk-link { color:#2563eb; cursor:pointer; font-weight:850; text-decoration:none; }
  .produk-table .produk-link:hover { text-decoration:underline; }
  .produk-table .produk-sub { display:block; margin-top:2px; color:#94a3b8; font-size:8px; font-weight:650; }
  .produk-status { display:inline-flex; align-items:center; justify-content:center; min-width:30px; padding:3px 6px; border-radius:999px; font-size:8px; font-weight:950; }
  .produk-status--l { color:#047857; background:#ecfdf5; }
  .produk-status--dp { color:#b45309; background:#fffbeb; }
  .produk-status--npl { color:#be123c; background:#fff1f2; }
  .produk-detail-backdrop { position:fixed; inset:0; z-index:9998; display:none; align-items:center; justify-content:center; padding:14px; background:rgba(15,23,42,.58); backdrop-filter:blur(4px); }
  .produk-detail-backdrop.is-open { display:flex; }
  .produk-detail-card { position:relative; z-index:1; display:flex; flex-direction:column; width:min(1220px,100%); height:min(88dvh,760px); overflow:hidden; border:1px solid #dbe3ee; border-radius:14px; background:#fff; box-shadow:0 28px 70px rgba(15,23,42,.28); }
  .produk-detail-head { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 14px; border-bottom:1px solid #e2e8f0; }
  .produk-detail-head-actions { display:flex; align-items:flex-end; justify-content:flex-end; gap:7px; min-width:0; }
  .produk-detail-filter { display:flex; flex-direction:column; gap:3px; min-width:170px; color:#64748b; font-size:8px; font-weight:900; text-transform:uppercase; }
  .produk-detail-select { width:100%; height:29px; padding:0 24px 0 8px; border:1px solid #cbd5e1; border-radius:7px; background:#fff; color:#334155; font-size:9px; font-weight:800; outline:0; }
  .produk-detail-select:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
  .produk-detail-title { min-width:0; color:#172033; font-size:13px; font-weight:950; }
  .produk-detail-sub { margin-top:3px; color:#64748b; font-size:9px; }
  .produk-detail-scroll { flex:1 1 auto; min-height:0; overflow:auto; }
  .produk-detail-table { width:max-content; min-width:1500px; border-collapse:separate; border-spacing:0; font-size:9px; }
  .produk-detail-table th, .produk-detail-table td { padding:7px 8px; border-right:1px solid #e2e8f0; border-bottom:1px solid #e5eaf1; white-space:nowrap; }
  .produk-detail-table th { position:sticky; top:0; z-index:10; background:#f1f5f9; color:#334155; font-size:8px; text-transform:uppercase; }
  .produk-detail-footer { display:flex; align-items:center; justify-content:space-between; gap:8px; padding:8px 12px; border-top:1px solid #e2e8f0; }
  .produk-page-btn { height:29px; padding:0 10px; border:1px solid #cbd5e1; border-radius:7px; background:#fff; color:#475569; font-size:9px; font-weight:850; cursor:pointer; }
  .produk-page-btn:disabled { cursor:not-allowed; opacity:.45; }
  @media (max-width:1279px) {
    .produk-filter { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); align-items:end; }
    .produk-filter .produk-actions { grid-column:1/-1; display:flex; gap:7px; }
    .produk-filter .produk-actions button { flex:1; }
  }
  @media (max-width:767px) {
    .produk-page { height:calc(100vh - 58px); min-height:0; padding:5px !important; }
    .produk-filter { grid-template-columns:1fr; padding:8px; }
    .produk-filter .produk-actions { grid-column:auto; }
    .produk-table { min-width:760px; font-size:8px; }
    .produk-table th, .produk-table td { padding:6px 7px; }
    .produk-table thead th:nth-child(2), .produk-table tbody td:nth-child(2) { width:135px; min-width:135px; }
    .produk-table tbody td:nth-child(1) { width:62px; min-width:62px; }
    .produk-table thead th:nth-child(2) { left:62px; }
    .produk-table tbody td:nth-child(2) { left:62px; }
    .produk-detail-backdrop { align-items:flex-end; padding:0; }
    .produk-detail-card { height:94dvh; border-radius:14px 14px 0 0; }
    .produk-detail-head { align-items:flex-start; flex-wrap:wrap; }
    .produk-detail-head-actions { width:100%; align-items:flex-end; }
    .produk-detail-filter { flex:1 1 130px; min-width:0; }
    .produk-detail-table { min-width:1500px; }
  }

  #agingProdukPage { height:calc(100dvh - 64px); min-height:0; }
  #agingProdukPage .mb-report-card--grow { min-height:0; }
  #agingProdukPage .mb-table { width:100%; min-width:0; table-layout:fixed; }
  #agingProdukPage .mb-table-wrap { overflow-x:hidden; }
  #agingProdukPage .mb-table th, #agingProdukPage .mb-table td { min-width:0; padding:6px 4px; overflow:hidden; text-overflow:clip; text-align:left !important; }
  #agingProdukPage .mb-table th { white-space:normal; overflow-wrap:anywhere; word-break:normal; }
  #agingProdukPage .mb-table td { white-space:nowrap; word-break:normal; }
  #agingProdukPage .mb-table td:nth-child(n+3) { text-align:right !important; }
  #agingProdukPage .mb-report-toolbar__tools { flex-wrap:nowrap; }
  #agingProdukPage .mb-report-toolbar__tools .produk-toolbar-year { flex:0 1 150px; width:150px !important; min-width:120px !important; }
  #agingProdukPage .mb-table th:first-child, #agingProdukPage .mb-table td:first-child { width:6.25%; min-width:78px; }
  #agingProdukPage .mb-table th:nth-child(2), #agingProdukPage .mb-table td:nth-child(2) { width:15.25%; min-width:190px; }
  #agingProdukPage .mb-table th:nth-child(3), #agingProdukPage .mb-table td:nth-child(3) { width:12.5%; }
  #agingProdukPage .mb-table th:nth-child(4), #agingProdukPage .mb-table td:nth-child(4),
  #agingProdukPage .mb-table th:nth-child(5), #agingProdukPage .mb-table td:nth-child(5),
  #agingProdukPage .mb-table th:nth-child(6), #agingProdukPage .mb-table td:nth-child(6),
  #agingProdukPage .mb-table th:nth-child(7), #agingProdukPage .mb-table td:nth-child(7),
  #agingProdukPage .mb-table th:nth-child(8), #agingProdukPage .mb-table td:nth-child(8) { width:8.2%; }
  #agingProdukPage .mb-table th:nth-child(9), #agingProdukPage .mb-table td:nth-child(9) { width:10%; }
  #agingProdukPage .mb-table th:nth-child(10), #agingProdukPage .mb-table td:nth-child(10),
  #agingProdukPage .mb-table th:nth-child(11), #agingProdukPage .mb-table td:nth-child(11) { width:7.5%; }
  #agingProdukPage .produk-sub { display:block; margin-top:1px; color:#64748b; font-size:6.4px; font-weight:800; line-height:1.05; }
  #agingProdukPage .aging-meta { padding:5px 9px; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:9px; font-weight:700; }
  @media (max-width:767px) {
    #agingProdukPage { height:calc(100dvh - 58px); padding:6px; }
    #agingProdukPage .mb-report-card--grow { height:calc(100dvh - 154px); }
    #agingProdukPage .mb-table { width:100%; min-width:0; font-size:8.2px; }
    #agingProdukPage .mb-table th, #agingProdukPage .mb-table td { padding:4px 2px; }
    #agingProdukPage .mb-table th:first-child, #agingProdukPage .mb-table td:first-child { width:15%; min-width:54px; }
    #agingProdukPage .mb-table th:nth-child(2), #agingProdukPage .mb-table td:nth-child(2) { width:21%; min-width:80px; }
    #agingProdukPage .mb-table .mb-sticky-left-2 { left:15% !important; }
    #agingProdukPage .mb-table th:nth-child(3), #agingProdukPage .mb-table td:nth-child(3) { width:14%; }
    #agingProdukPage .mb-table th:nth-child(4), #agingProdukPage .mb-table td:nth-child(4),
    #agingProdukPage .mb-table th:nth-child(5), #agingProdukPage .mb-table td:nth-child(5),
    #agingProdukPage .mb-table th:nth-child(6), #agingProdukPage .mb-table td:nth-child(6),
    #agingProdukPage .mb-table th:nth-child(7), #agingProdukPage .mb-table td:nth-child(7),
    #agingProdukPage .mb-table th:nth-child(8), #agingProdukPage .mb-table td:nth-child(8) { width:6%; }
    #agingProdukPage .mb-table th:nth-child(9), #agingProdukPage .mb-table td:nth-child(9) { width:9%; }
    #agingProdukPage .mb-table th:nth-child(10), #agingProdukPage .mb-table td:nth-child(10),
    #agingProdukPage .mb-table th:nth-child(11), #agingProdukPage .mb-table td:nth-child(11) { width:5.5%; }
    #agingProdukPage .produk-sub { font-size:5.2px; }
    #agingProdukPage .mb-report-toolbar__tools .produk-toolbar-year { flex:1 1 120px; width:auto !important; min-width:0 !important; }
  }
</style>

<?php
mb_render_report_page([
    'id' => 'agingProdukPage',
    'class' => 'mb-report-aging',
    'header' => [
        'id' => 'agingProdukHeader',
        'title' => 'Rekap Aging Kredit By Produk',
        'subtitle' => 'Portofolio berdasarkan kode produk sesuai mapping SK per 01/06/2026.',
        'filter_toggle_id' => 'btnToggleProdukFilter',
        'filter_panel_id' => 'panelFilterProduk',
        'filters' => [
            ['id' => 'harian_date_produk', 'label' => 'Tanggal Actual', 'type' => 'date', 'width' => '130px', 'attrs' => ['onclick' => 'this.showPicker && this.showPicker()']],
            ['id' => 'opt_kantor_produk', 'label' => 'Cabang', 'type' => 'select', 'width' => '210px', 'options' => ['' => 'ALL | SEMUA CABANG']],
            ['id' => 'opt_sub_produk', 'label' => 'Korwil', 'label_id' => 'lbl_sub_produk', 'type' => 'select', 'width' => '160px', 'options' => ['' => 'ALL KORWIL']],
        ],
    ],
    'toolbar' => [
        'title' => 'Portofolio Per Produk',
        'title_id' => 'agingProdukTableTitle',
        'before_html' => $produkYearToolbar,
        'actions' => [
            ['attrs' => ['id' => 'btnSwitchAgingProduk'], 'variant' => 'view-switch', 'icon' => 'chart', 'title' => 'Ganti ke Aging Kredit', 'aria_label' => 'Ganti ke Aging Kredit'],
            ['attrs' => ['id' => 'btnExportProduk'], 'tone' => 'success', 'icon' => 'download', 'title' => 'Export Excel', 'aria_label' => 'Export Excel'],
        ],
    ],
    'legend_html' => '<div id="produkMeta" class="aging-meta">Menunggu data...</div>',
    'table' => [
        'wrapper_id' => 'produkTableWrap',
        'table_id' => 'tabelProduk',
        'loading_id' => 'produkLoading',
        'loading_text' => 'Memuat data produk...',
        'thead_html' => '<tr><th class="mb-sticky-left">KODE PRODUK</th><th class="mb-sticky-left-2" style="--mb-sticky-1:78px">NAMA PRODUK</th><th>TOTAL PORTOFOLIO</th><th>L</th><th>DP</th><th>KL</th><th>D</th><th>M</th><th>TOTAL NPL</th><th>% NPL PRODUK</th><th>% NPL / TOTAL PORTO</th></tr>',
        'tbody_ids' => ['totalProduk', 'bodyProduk'],
    ],
]);
?>

<div id="modalDetailProduk" class="produk-detail-backdrop" aria-hidden="true">
  <div class="produk-detail-card">
    <div class="produk-detail-head">
      <div class="min-w-0"><div id="produkDetailTitle" class="produk-detail-title truncate">Detail Produk</div><div id="produkDetailSub" class="produk-detail-sub truncate">-</div></div>
      <div class="produk-detail-head-actions">
        <label class="produk-detail-filter">Produk Lama<select id="produkDetailFilterLama" class="produk-detail-select"><option value="">Semua Produk Lama</option></select></label>
        <button id="btnExportDetailProduk" class="produk-page-btn bg-emerald-600 text-white border-emerald-600">Export</button><button id="btnCloseDetailProduk" class="produk-page-btn text-red-600">Tutup</button>
      </div>
    </div>
    <div class="produk-detail-scroll"><table class="produk-detail-table"><thead><tr><th>NO REKENING</th><th>NAMA NASABAH</th><th>KODE PRODUK LAMA</th><th>PRODUK LAMA</th><th>KODE PRODUK SK</th><th>PRODUK SK</th><th>KOLEK</th><th>TGL REALISASI</th><th>USIA KREDIT</th><th>JML PINJAMAN</th><th>BAKI DEBET</th><th>SALDO BANK</th><th>HARI MENUNGGAK</th><th>TUNGGAKAN POKOK</th><th>TUNGGAKAN BUNGA</th></tr></thead><tbody id="bodyDetailProduk"></tbody></table></div>
    <div class="produk-detail-footer"><span id="produkDetailInfo" class="text-[9px] font-bold text-slate-600">0 Data</span><div class="flex gap-1"><button id="btnPrevDetailProduk" class="produk-page-btn">« Prev</button><button id="btnNextDetailProduk" class="produk-page-btn">Next »</button></div></div>
  </div>
</div>

<script>
  const API_PRODUK = './api/kredit/';
  const API_KODE_PRODUK = './api/kode/';
  const API_DATE_PRODUK = './api/date/';
  const nfProduk = new Intl.NumberFormat('id-ID');
  const fmtProduk = value => nfProduk.format(Number(value || 0));
  const escProduk = value => String(value ?? '').replace(/[&<>'"]/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[ch]));
  const statusMetaProduk = { L:['L','produk-status--l'], DP:['DP','produk-status--dp'], KL:['KL','produk-status--npl'], D:['D','produk-status--npl'], M:['M','produk-status--npl'] };
  let produkRows = [], produkGrand = null, produkParams = {}, detailProdukParams = null, produkPage = 1, produkPages = 1;

  document.getElementById('btnSwitchAgingProduk')?.addEventListener('click', () => {
    window.location.href = 'aging_kredit';
  });

  document.getElementById('btnCloseDetailProduk').addEventListener('click', closeDetailProduk);
  document.getElementById('modalDetailProduk').addEventListener('click', e => { if (e.target.id === 'modalDetailProduk') closeDetailProduk(); });
  document.getElementById('btnPrevDetailProduk').addEventListener('click', () => loadDetailProduk(produkPage - 1));
  document.getElementById('btnNextDetailProduk').addEventListener('click', () => loadDetailProduk(produkPage + 1));
  document.getElementById('produkDetailFilterLama').addEventListener('change', event => {
    if (!detailProdukParams) return;
    detailProdukParams.kode_produk_lama = event.target.value;
    loadDetailProduk(1);
  });
  document.getElementById('harian_date_produk')?.addEventListener('change', fetchProduk);
  document.addEventListener('change', event => {
    if (event.target.matches('[data-realisasi-year]')) fetchProduk();
  });
  document.getElementById('btnExportProduk')?.addEventListener('click', exportProduk);
  document.getElementById('btnExportDetailProduk').addEventListener('click', exportDetailProduk);

  function currentUserCodeProduk() {
    const user = window.getUser ? window.getUser() : null;
    let code = user?.kode ? String(user.kode).padStart(3, '0') : '000';
    return code === '099' ? '000' : code;
  }
  function setSubOptionsProduk(cabang, initial = false) {
    const label = document.getElementById('lbl_sub_produk');
    const select = document.getElementById('opt_sub_produk');
    if (!cabang || cabang === '000') {
      label.textContent = 'Korwil';
      select.innerHTML = '<option value="">ALL KORWIL</option><option value="SEMARANG">SEMARANG</option><option value="SOLO">SOLO</option><option value="BANYUMAS">BANYUMAS</option><option value="PEKALONGAN">PEKALONGAN</option>';
      if (!initial) fetchProduk();
      return;
    }
    label.textContent = 'Kankas';
    select.innerHTML = '<option value="">ALL KANKAS</option>';
    fetch(API_KODE_PRODUK, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type:'kode_kankas',kode_kantor:cabang})})
      .then(r => r.json()).then(j => (j.data || []).forEach(x => select.insertAdjacentHTML('beforeend', `<option value="${escProduk(x.kode_group1)}">${escProduk(x.deskripsi_group1 || x.kode_group1)}</option>`))).catch(() => {}).finally(() => { if (!initial) fetchProduk(); });
  }
  document.getElementById('opt_kantor_produk').addEventListener('change', e => setSubOptionsProduk(e.target.value));
  document.getElementById('opt_sub_produk').addEventListener('change', fetchProduk);

  async function initProduk() {
    const code = currentUserCodeProduk();
    const kantor = document.getElementById('opt_kantor_produk');
    if (code === '000') {
      const j = await fetch(API_KODE_PRODUK, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type:'kode_kantor'})}).then(r => r.json()).catch(() => ({}));
      kantor.innerHTML = '<option value="">ALL | SEMUA CABANG</option>' + (j.data || []).filter(x => x.kode_kantor && x.kode_kantor !== '000').sort((a,b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor))).map(x => `<option value="${escProduk(String(x.kode_kantor).padStart(3,'0'))}">${escProduk(String(x.kode_kantor).padStart(3,'0'))} - ${escProduk(x.nama_kantor)}</option>`).join('');
      kantor.value = '';
    } else { kantor.innerHTML = `<option value="${escProduk(code)}">CABANG ${escProduk(code)}</option>`; kantor.value = code; kantor.disabled = true; }
    setSubOptionsProduk(kantor.value, true);
    const date = await fetch(API_DATE_PRODUK).then(r => r.json()).catch(() => ({}));
    document.getElementById('harian_date_produk').value = date.data?.last_created || new Date().toISOString().slice(0,10);
    fetchProduk();
  }

  function getProdukPayload() {
    const cabang = document.getElementById('opt_kantor_produk').value;
    const sub = document.getElementById('opt_sub_produk').value;
    const yearInputs = [...document.querySelectorAll('[data-realisasi-year]')];
    const selectedYears = yearInputs.filter(input => input.checked).map(input => input.value);
    const payload = {type:'produk_kredit', harian_date:document.getElementById('harian_date_produk').value, kode_kantor:cabang};
    if (selectedYears.length) payload.realisasi_tahun = selectedYears;
    if (!cabang || cabang === '000') payload.korwil = sub; else payload.kode_kankas = sub;
    return payload;
  }
  async function fetchProduk() {
    const loading = document.getElementById('produkLoading'); loading.classList.remove('hidden', 'is-hidden');
    const payload = getProdukPayload();
    try {
      const j = await fetch(API_PRODUK, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}).then(r => r.json());
      if (j.status !== 200) throw new Error(j.message || 'Gagal memuat data produk');
      produkRows = j.data?.data || []; produkGrand = j.data?.grand_total || null; produkParams = payload;
      syncProductYearOptions(j.data?.meta?.realisasi_tahun_options || []);
      const tahunAktif = payload.realisasi_tahun === undefined ? 'SEMUA TAHUN' : (payload.realisasi_tahun.length ? payload.realisasi_tahun.join(', ') : 'tidak ada');
      document.getElementById('produkMeta').textContent = `${j.data?.meta?.filter_aktif || 'KONSOLIDASI'} • Nominatif ${payload.harian_date} • Tahun realisasi: ${tahunAktif} • Produk baru aktif 01/06/2026`;
      renderProduk();
    } catch (e) { document.getElementById('bodyProduk').innerHTML = `<tr><td colspan="11" class="py-16 text-center text-red-500 font-bold">${escProduk(e.message)}</td></tr>`; document.getElementById('totalProduk').innerHTML = ''; }
    finally { loading.classList.add('hidden', 'is-hidden'); }
  }
  function statusCellProduk(row, status) {
    const [label] = statusMetaProduk[status];
    const os = Number(row['os_' + status.toLowerCase()] || 0), noa = Number(row['noa_' + status.toLowerCase()] || 0);
    const code = String(row.kode_produk || '');
    return `<a class="produk-link" href="#" onclick="openDetailProduk(${JSON.stringify(code)}, '${label}');return false;">${fmtProduk(os)}</a><span class="produk-sub">${fmtProduk(noa)} NOA • ${pctProduk(os, row.total_os)}%</span>`;
  }
  function pctProduk(value, total) {
    const denominator = Number(total || 0);
    return denominator > 0 ? (Number(value || 0) * 100 / denominator).toLocaleString('id-ID', {maximumFractionDigits:2}) : '0';
  }
  function syncProductYearOptions(years) {
    const dropdown = document.querySelector('[data-mb-checklist-dropdown]');
    if (!dropdown || !Array.isArray(years) || !years.length) return;
    const menu = dropdown.querySelector('[data-mb-checklist-menu]');
    const currentInputs = [...dropdown.querySelectorAll('input[data-realisasi-year]')];
    const selected = new Set(currentInputs.filter(input => input.checked).map(input => input.value));
    const values = years.map(year => String(year));
    menu.innerHTML = values.map(year => `<label class="mb-checklist__option" for="realisasi_tahun_produk_${year}"><input type="checkbox" id="realisasi_tahun_produk_${year}" name="realisasi_tahun_produk[]" value="${year}" class="mb-checklist__input" data-realisasi-year${selected.has(year) ? ' checked' : ''}><span class="mb-checklist__mark" aria-hidden="true"></span><span class="mb-checklist__text">${year}</span></label>`).join('');
    if (window.MonbisUI?.refreshChecklistSummary) window.MonbisUI.refreshChecklistSummary(dropdown);
  }
  function subProduk(noa, percent) {
    return `<span class="produk-sub">${fmtProduk(noa)} NOA • ${percent}%</span>`;
  }
  function renderProduk() {
    const total = produkGrand || {};
    const nplNoa = Number(total.noa_npl || 0), nplOs = Number(total.os_npl || 0);
    document.getElementById('totalProduk').innerHTML = `<tr class="produk-total mb-total-row"><td class="mb-sticky-left">-</td><td class="mb-sticky-left-2">GRAND TOTAL</td><td>${fmtProduk(total.total_os)}${subProduk(total.total_noa, '100')}</td><td>${fmtProduk(total.os_l)}${subProduk(total.noa_l, pctProduk(total.os_l, total.total_os))}</td><td>${fmtProduk(total.os_dp)}${subProduk(total.noa_dp, pctProduk(total.os_dp, total.total_os))}</td><td>${fmtProduk(total.os_kl)}${subProduk(total.noa_kl, pctProduk(total.os_kl, total.total_os))}</td><td>${fmtProduk(total.os_d)}${subProduk(total.noa_d, pctProduk(total.os_d, total.total_os))}</td><td>${fmtProduk(total.os_m)}${subProduk(total.noa_m, pctProduk(total.os_m, total.total_os))}</td><td>${fmtProduk(nplOs)}${subProduk(nplNoa, pctProduk(nplOs, total.total_os))}</td><td colspan="2">${pctProduk(nplOs, total.total_os)}%</td></tr>`;
    document.getElementById('bodyProduk').innerHTML = produkRows.length ? produkRows.map(row => `<tr><td class="mb-sticky-left font-mono font-bold text-blue-700">${escProduk(row.kode_produk)}</td><td class="mb-sticky-left-2 font-bold">${escProduk(row.nama_produk)}</td><td>${fmtProduk(row.total_os)}${subProduk(row.total_noa, '100')}</td><td>${statusCellProduk(row,'L')}</td><td>${statusCellProduk(row,'DP')}</td><td>${statusCellProduk(row,'KL')}</td><td>${statusCellProduk(row,'D')}</td><td>${statusCellProduk(row,'M')}</td><td>${fmtProduk(row.os_npl)}${subProduk(row.noa_npl, pctProduk(row.os_npl, row.total_os))}</td><td>${pctProduk(row.os_npl, row.total_os)}%</td><td>${pctProduk(row.os_npl, total.total_os)}%</td></tr>`).join('') : '<tr><td colspan="11" class="py-16 text-center text-slate-400">Data tidak ditemukan.</td></tr>';
  }
  window.openDetailProduk = function(code, status) {
    detailProdukParams = {...produkParams, kode_produk: code, status, kode_produk_lama: ''};
    produkPage = 1; produkPages = 1;
    document.getElementById('produkDetailFilterLama').value = '';
    document.getElementById('produkDetailTitle').textContent = `Detail Produk ${code} • ${status}`;
    document.getElementById('produkDetailSub').textContent = `${produkParams.harian_date} • Baki Debet dan Saldo Bank actual`;
    document.getElementById('modalDetailProduk').classList.add('is-open'); document.getElementById('modalDetailProduk').setAttribute('aria-hidden','false'); loadDetailProduk(1);
  };
  function closeDetailProduk() { document.getElementById('modalDetailProduk').classList.remove('is-open'); document.getElementById('modalDetailProduk').setAttribute('aria-hidden','true'); }
  function renderDetailProductOptions(options) {
    const select = document.getElementById('produkDetailFilterLama');
    if (!select) return;
    const current = detailProdukParams?.kode_produk_lama || '';
    select.innerHTML = '<option value="">Semua Produk Lama</option>' + (options || []).map(option => {
      const code = String(option.kode_produk_lama ?? '');
      const name = String(option.nama_produk_lama ?? ('PRODUK ' + code));
      return '<option value="' + escProduk(code) + '">' + escProduk(code + ' - ' + name) + '</option>';
    }).join('');
    select.value = current;
  }
  async function loadDetailProduk(page) {
    if (page < 1 || (produkPages && page > produkPages)) return;
    const body = document.getElementById('bodyDetailProduk'); body.innerHTML = '<tr><td colspan="15" class="py-16 text-center text-slate-400">Memuat data...</td></tr>';
    const payload = {...detailProdukParams, type:'detail_produk_kredit', page, limit:25};
    try {
      const j = await fetch(API_PRODUK,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}).then(r=>r.json());
      if (j.status !== 200) throw new Error(j.message || 'Gagal memuat detail');
      const rows = j.data?.data || [], pg = j.data?.pagination || {}; produkPage = Number(pg.current_page || page); produkPages = Number(pg.total_pages || 1);
      renderDetailProductOptions(j.data?.produk_options || []);
      body.innerHTML = rows.length ? rows.map(r => '<tr><td class="font-mono">' + escProduk(r.no_rekening) + '</td><td class="font-bold">' + escProduk(r.nama_nasabah) + '</td><td class="font-mono text-blue-700">' + escProduk(r.kode_produk_lama) + '</td><td>' + escProduk(r.nama_produk_lama) + '</td><td class="font-mono text-blue-700">' + escProduk(r.kode_produk) + '</td><td>' + escProduk(r.nama_produk) + '</td><td class="font-bold">' + escProduk(r.kolektibilitas) + '</td><td>' + escProduk(r.tgl_realisasi || '-') + '</td><td class="text-right">' + (r.usia_kredit_hari == null ? '-' : fmtProduk(r.usia_kredit_hari) + ' hari') + '</td><td class="text-right">' + fmtProduk(r.jml_pinjaman) + '</td><td class="text-right font-bold text-blue-700">' + fmtProduk(r.baki_debet) + '</td><td class="text-right font-bold text-emerald-700">' + fmtProduk(r.saldo_bank) + '</td><td class="text-right">' + fmtProduk(r.hari_menunggak) + '</td><td class="text-right">' + fmtProduk(r.tunggakan_pokok) + '</td><td class="text-right">' + fmtProduk(r.tunggakan_bunga) + '</td></tr>').join('') : '<tr><td colspan="15" class="py-16 text-center text-slate-400">Data tidak ditemukan.</td></tr>';
      const start = rows.length ? ((produkPage - 1) * 25) + 1 : 0, end = Math.min(produkPage * 25, Number(pg.total_records || 0)); document.getElementById('produkDetailInfo').textContent = 'Menampilkan ' + start + '-' + end + ' dari ' + fmtProduk(pg.total_records) + ' Data';
      document.getElementById('btnPrevDetailProduk').disabled = produkPage <= 1; document.getElementById('btnNextDetailProduk').disabled = produkPage >= produkPages;
    } catch(e) { body.innerHTML = '<tr><td colspan="15" class="py-16 text-center text-red-500 font-bold">' + escProduk(e.message) + '</td></tr>'; }
  }
  function makeTsvProduk(rows) {
    let t = 'Kode Produk\tNama Produk\tTotal Porto\tTotal Porto NOA\tL OS\tL NOA\tL %\tDP OS\tDP NOA\tDP %\tKL OS\tKL NOA\tKL %\tD OS\tD NOA\tD %\tM OS\tM NOA\tM %\tTotal NPL\tTotal NPL NOA\tNPL %\n';
    rows.forEach(r => {
      const total = Number(r.total_os || 0);
      const pct = value => total ? (Number(value || 0) * 100 / total) : 0;
      const npl = pct(r.os_npl);
      t += `${r.kode_produk}\t${r.nama_produk}\t${r.total_os}\t${r.total_noa}\t${r.os_l}\t${r.noa_l}\t${pct(r.os_l)}\t${r.os_dp}\t${r.noa_dp}\t${pct(r.os_dp)}\t${r.os_kl}\t${r.noa_kl}\t${pct(r.os_kl)}\t${r.os_d}\t${r.noa_d}\t${pct(r.os_d)}\t${r.os_m}\t${r.noa_m}\t${pct(r.os_m)}\t${r.os_npl}\t${r.noa_npl}\t${npl}\n`;
    });
    return t;
  }
  function downloadProduk(text, name) { const a=document.createElement('a'); a.href=URL.createObjectURL(new Blob([text],{type:'application/vnd.ms-excel'})); a.download=name; a.click(); setTimeout(()=>URL.revokeObjectURL(a.href),500); }
  function exportProduk() { if (!produkRows.length) return alert('Tidak ada data untuk diexport.'); downloadProduk(makeTsvProduk(produkRows), `Aging_Produk_${produkParams.harian_date}.xls`); }
  async function exportDetailProduk() {
    if (!detailProdukParams) return alert('Buka detail produk terlebih dahulu.');
    const payload = {...detailProdukParams, type:'detail_produk_kredit', page:1, limit:15000, export_all:1};
    const j = await fetch(API_PRODUK,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)}).then(r=>r.json()).catch(()=>({}));
    const rows = j.data?.data || []; if (!rows.length) return alert('Tidak ada data detail untuk diexport.');
    let t = 'No Rekening\tNama Nasabah\tKode Produk Lama\tNama Produk Lama\tKode Produk SK\tProduk SK\tKolektibilitas\tTgl Realisasi\tUsia Kredit (Hari)\tJml Pinjaman\tBaki Debet\tSaldo Bank\tHari Menunggak\tTunggakan Pokok\tTunggakan Bunga\n';
    rows.forEach(r => { t += [r.no_rekening, r.nama_nasabah, r.kode_produk_lama, r.nama_produk_lama, r.kode_produk, r.nama_produk, r.kolektibilitas, r.tgl_realisasi || '', r.usia_kredit_hari ?? '', r.jml_pinjaman, r.baki_debet, r.saldo_bank, r.hari_menunggak, r.tunggakan_pokok, r.tunggakan_bunga].join('\t') + '\n'; });
    downloadProduk(t, 'Detail_Aging_Produk_' + detailProdukParams.kode_produk + '_' + detailProdukParams.harian_date + '.xls');
  }
  initProduk();
</script>

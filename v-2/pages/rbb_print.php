<?php
$rbbPrintRoute = $baseUrl . '/rbb/print';
$printInput = [
    'kode_kantor' => preg_replace('/[^0-9]/', '', (string)($_POST['kode_kantor'] ?? '')),
    'tahun_mulai' => max(0, min(2100, (int)($_POST['tahun_mulai'] ?? 0))),
    'bulan_mulai' => max(0, min(12, (int)($_POST['bulan_mulai'] ?? 0))),
];
?>
<section class="v2-card v2-module-shell v2-rbb-print-page">
  <div class="v2-card-heading v2-module-toolbar v2-rbb-card-heading v2-no-print">
    <div><h2>Proyeksi RBB</h2></div>
    <div class="v2-actions v2-rbb-print-actions">
      <label class="v2-rbb-paper-field"><span>Ukuran kertas</span><select id="v2RbbPrintSize"><option value="a4">A4 landscape</option><option value="legal">Legal landscape</option></select></label>
      <button type="button" class="v2-button v2-button--primary" id="v2RbbDownloadButton"><?= v2_icon('download', 15) ?><span>Download PDF</span></button>
    </div>
  </div>

  <div class="v2-card-body v2-module-body v2-rbb-print-body">
    <article class="v2-rbb-print-sheet">
    <header class="v2-rbb-print-header">
      <div class="v2-rbb-print-brand">
        <div class="v2-rbb-print-mark"><?= v2_icon('file', 23) ?></div>
        <div>
          <p class="v2-rbb-print-kicker">LAPORAN RENCANA BISNIS BANK</p>
          <h2>Ringkasan Proyeksi RBB</h2>
          <p>Ringkasan indikator utama berdasarkan periode kwartal.</p>
        </div>
      </div>
      <div class="v2-rbb-print-meta">
        <span>Cabang</span><strong id="v2RbbPrintBranch">Memuat...</strong>
        <span>Periode</span><strong id="v2RbbPrintPeriod">Memuat...</strong>
      </div>
    </header>

    <div class="v2-rbb-print-status" id="v2RbbPrintStatus">Memuat data RBB...</div>

    <section class="v2-rbb-print-section">
      <div class="v2-rbb-print-section-heading">
        <div><p class="v2-eyebrow">IKHTISAR</p><h3>Target RBB per Kwartal</h3></div>
        <span>Satuan: sesuai input RBB</span>
      </div>
      <div class="v2-rbb-print-table-wrap">
        <table class="v2-rbb-print-table">
          <thead id="v2RbbPrintHead"></thead>
          <tbody id="v2RbbPrintBody"><tr><td colspan="6" class="v2-empty">Memuat ringkasan...</td></tr></tbody>
        </table>
      </div>
    </section>

    <section class="v2-rbb-print-section v2-rbb-approval-section">
      <div class="v2-rbb-print-section-heading">
        <div><p class="v2-eyebrow">PENGESAHAN</p><h3>Persetujuan RBB</h3></div>
        <span id="v2RbbApprovalStatus">Memuat approval...</span>
      </div>
      <div class="v2-rbb-approval-grid" id="v2RbbApprovalGrid"></div>
    </section>

    <footer class="v2-rbb-print-footer">
      <span>Dokumen ini dibuat dari data RBB yang tersimpan pada MONBIS.</span>
      <span id="v2RbbPrintGeneratedAt"></span>
    </footer>
    </article>
  </div>
</section>

<script src="<?= v2_e($baseUrl . '/assets/vendor/html2pdf.bundle.min.js?v=1') ?>"></script>
<script>
(() => {
  const API = <?= json_encode($legacyBase . '/api/rbb/') ?>;
  const LOGO_URL = <?= json_encode($legacyBase . '/img/logo.png') ?>;
  const printInput = <?= json_encode($printInput, JSON_UNESCAPED_SLASHES) ?>;
  const branch = String(printInput.kode_kantor || '').padStart(3, '0');
  const startYear = Number(printInput.tahun_mulai || new Date().getFullYear());
  const startMonth = Math.min(12, Math.max(1, Number(printInput.bulan_mulai || 1)));
  const nextYear = startYear + 1;
  const columns = [
    {label:`Des ${startYear}`, year:startYear, month:12},
    {label:`Q1 ${nextYear}`, year:nextYear, month:3},
    {label:`Q2 ${nextYear}`, year:nextYear, month:6},
    {label:`Q3 ${nextYear}`, year:nextYear, month:9},
    {label:`Q4 ${nextYear}`, year:nextYear, month:12},
  ];
  const summaryRows = [
    {code:'95', label:'1 ASSET', className:'main'},
    {code:'105', label:'2 DANA MASYARAKAT', className:'main'},
    {code:'106', label:'a Tabungan', className:'child'},
    {code:'108', label:'b Deposito', className:'child'},
    {code:'63', label:'3 KREDIT YANG DIBERIKAN', className:'main'},
    {code:'196', label:'4 PENDAPATAN', className:'main'},
    {code:'258', label:'5 BIAYA', className:'main'},
    {code:'261', label:'6 LABA (RUGI) SEBELUM PAJAK', className:'main'},
  ];
  const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const num = (value) => Number(value || 0);
  const fmt = (value) => { const number = num(value); const displayValue = Math.abs(number) >= 1000000 ? Math.trunc(number) : number; return new Intl.NumberFormat('id-ID', {maximumFractionDigits:2}).format(displayValue); };
  const el = (id) => document.getElementById(id);
  async function post(body) {
    const response = await fetch(API, {method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body)});
    const json = await response.json().catch(() => ({}));
    if (!response.ok || Number(json.status) !== 200) throw new Error(json.message || `Request gagal (${response.status})`);
    return json.data || {};
  }
  function rowsByCode(responses) {
    const map = new Map();
    responses.forEach((response) => (response.rows || []).forEach((row) => { if (!map.has(String(row.kode_monbis))) map.set(String(row.kode_monbis), row); }));
    return map;
  }
  function valueAt(responses, code, column) {
    const response = responses.find((item) => Number(item.tahun || item.year) === column.year);
    const row = response?.rows?.find((item) => String(item.kode_monbis) === code);
    return row ? row.values?.[column.month] : null;
  }
  function renderTable(responses) {
    const rowMap = rowsByCode(responses);
    el('v2RbbPrintHead').innerHTML = `<tr><th>INDIKATOR</th>${columns.map((column) => `<th>${column.label}</th>`).join('')}</tr>`;
    el('v2RbbPrintBody').innerHTML = summaryRows.map((item) => `<tr class="${item.className}"><td>${esc(item.label)}<small>Kode ${esc(item.code)}${rowMap.get(item.code)?.kode_perkiraan ? ` · ${esc(rowMap.get(item.code).kode_perkiraan)}` : ''}</small></td>${columns.map((column) => `<td>${valueAt(responses, item.code, column) === null ? '-' : fmt(valueAt(responses, item.code, column))}</td>`).join('')}</tr>`).join('');
  }
  function eventDate(event) { return new Date(String(event.created_at || '').replace(' ', 'T')).getTime() || 0; }
  function latestEvent(events, matcher) { return events.filter(matcher).sort((a, b) => eventDate(a) - eventDate(b)).at(-1) || null; }
  function roleText(event) { return String(event?.actor_role || '').toLowerCase(); }
  function actionLabel(action) { return ({SUBMIT_KANWIL:'Diajukan ke Kanwil', APPROVE_KANWIL:'Disetujui Kanwil', APPROVE_PUSAT:'Disetujui Kantor Pusat', REJECT:'Ditolak', REOPEN:'Dibuka kembali'})[action] || action || 'Belum tercatat'; }
  function barcodeSvg(value) {
    const source = String(value || 'RBB');
    const size = 29;
    const cells = Array(size * size).fill(null);
    const reserved = new Set();
    const cellAt = (x, y) => y * size + x;
    const reserve = (x, y, value = false) => { if (x < 0 || y < 0 || x >= size || y >= size) return; reserved.add(cellAt(x, y)); cells[cellAt(x, y)] = value; };
    const finder = (left, top) => { for (let y = -1; y <= 7; y++) for (let x = -1; x <= 7; x++) { const inside = x >= 0 && x <= 6 && y >= 0 && y <= 6; const dark = inside && (x === 0 || x === 6 || y === 0 || y === 6 || (x >= 2 && x <= 4 && y >= 2 && y <= 4)); reserve(left + x, top + y, dark); } };
    finder(0, 0); finder(size - 7, 0); finder(0, size - 7);
    for (let i = 8; i < size - 8; i++) { reserve(i, 6, i % 2 === 0); reserve(6, i, i % 2 === 0); }
    let seed = 2166136261;
    [...source].forEach((char) => { seed ^= char.charCodeAt(0); seed = Math.imul(seed, 16777619); });
    const randomBit = () => { seed = (Math.imul(seed, 1664525) + 1013904223) | 0; return (seed >>> 29) & 1; };
    cells.forEach((value, index) => { if (value === null && !reserved.has(index)) cells[index] = randomBit() === 1; });
    const modules = cells.map((dark, index) => dark ? `<rect x="${index % size}" y="${Math.floor(index / size)}" width="1" height="1"/>` : '').join('');
    return `<svg class="v2-rbb-qr" viewBox="0 0 ${size} ${size}" role="img" aria-label="Kode verifikasi approval"><rect width="${size}" height="${size}" fill="#fff"/>${modules}<rect x="10.5" y="10.5" width="8" height="8" rx="1" fill="#fff"/><image href="${esc(LOGO_URL)}" x="11.5" y="11.5" width="6" height="6" preserveAspectRatio="xMidYMid slice"/></svg>`;
  }
  function renderApprovals(responses) {
    const events = responses.flatMap((response) => (response.plan?.approvals || []).map((event) => ({...event, tahun:response.tahun || response.year}))).filter((event) => event.action !== 'REOPEN');
    const slots = [
      {label:'Kabid Operasional Cabang', match:(event) => /operasional|kabid.*ops/.test(roleText(event))},
      {label:'Kabid Pemasaran Cabang', match:(event) => /pemasaran|marketing/.test(roleText(event))},
      {label:'Kepala Cabang', match:(event) => /kepala cabang|pimpinan cabang/.test(roleText(event)) || event.action === 'SUBMIT_KANWIL'},
      {label:'Kanwil', match:(event) => event.action === 'APPROVE_KANWIL' || /kanwil/.test(roleText(event))},
      {label:'Kantor Pusat', match:(event) => event.action === 'APPROVE_PUSAT' || /kadiv|kantor pusat|pusat/.test(roleText(event))},
    ];
    const approvedCount = events.filter((event) => ['APPROVE_KANWIL','APPROVE_PUSAT'].includes(event.action)).length;
    el('v2RbbApprovalStatus').textContent = `${approvedCount} approval tercatat`;
    el('v2RbbApprovalGrid').innerHTML = slots.map((slot) => {
      const event = latestEvent(events, slot.match);
      const approved = event && ['APPROVE_KANWIL','APPROVE_PUSAT'].includes(event.action);
      const code = event ? `RBB-${event.tahun}-${branch}-${String(event.id || '0').padStart(6, '0')}` : '';
      const codeMarkup = approved ? `<div class="v2-rbb-approval-code">${barcodeSvg(code)}</div>` : '<div class="v2-rbb-approval-empty">Barcode setelah disetujui</div>';
      const personMarkup = event ? `<strong>${esc(event.actor_name || event.actor_id || 'Pengguna tercatat')}</strong><small>${esc(event.actor_role || slot.label)}</small>` : 'Menunggu persetujuan';
      return `<div class="v2-rbb-approval-card ${approved ? 'is-recorded' : 'is-pending'}"><div class="v2-rbb-approval-card-head"><strong>${esc(slot.label)}</strong><span>${event ? actionLabel(event.action) : 'Belum tercatat'}</span></div><div class="v2-rbb-approval-person">${personMarkup}</div>${codeMarkup}</div>`;
    }).join('');
  }
  async function load() {
    if (!branch || !Number.isFinite(startYear)) throw new Error('Cabang atau periode cetak belum dipilih.');
    const responses = await Promise.all([startYear, nextYear].map((year) => post({type:'rbb_planning_data', tahun:year, tahun_mulai:startYear, bulan_mulai:startMonth, kode_kantor:branch, kategori:'ALL'})));
    const office = responses.flatMap((response) => response.offices || []).find((item) => String(item.kode_kantor).padStart(3, '0') === branch);
    el('v2RbbPrintBranch').textContent = `${branch} · ${office?.nama_kantor || 'Cabang'}`;
    el('v2RbbPrintPeriod').textContent = `${columns[0].label} – ${columns[columns.length - 1].label}`;
    el('v2RbbPrintGeneratedAt').textContent = `Dicetak ${new Intl.DateTimeFormat('id-ID', {dateStyle:'long', timeStyle:'short'}).format(new Date())}`;
    renderTable(responses); renderApprovals(responses);
    el('v2RbbPrintStatus').textContent = 'Data berhasil dimuat dari RBB planning.';
  }
  const printSize = el('v2RbbPrintSize');
  async function downloadPdf() {
    const button = el('v2RbbDownloadButton');
    const sheet = document.querySelector('.v2-rbb-print-sheet');
    if (!button || !sheet) return;
    if (typeof window.html2pdf !== 'function') {
      el('v2RbbPrintStatus').textContent = 'Generator PDF belum siap. Muat ulang halaman lalu coba lagi.';
      el('v2RbbPrintStatus').classList.add('is-error');
      return;
    }
    const size = printSize?.value === 'legal' ? 'legal' : 'a4';
    const filename = `RBB-${branch}-${startYear}.pdf`;
    button.disabled = true;
    button.querySelector('span')?.replaceChildren(document.createTextNode('Menyiapkan PDF...'));
    try {
      await window.html2pdf().set({
        margin: 5,
        filename,
        image: {type: 'jpeg', quality: .98},
        html2canvas: {scale: 2, useCORS: true, backgroundColor: '#fff', windowWidth: 1400, scrollY: 0},
        jsPDF: {unit: 'mm', format: size, orientation: 'landscape'},
        pagebreak: {mode: ['avoid-all']},
      }).from(sheet).save();
      el('v2RbbPrintStatus').textContent = 'PDF berhasil diunduh.';
      el('v2RbbPrintStatus').classList.remove('is-error');
    } catch (error) {
      el('v2RbbPrintStatus').textContent = `Download PDF gagal: ${error.message || 'coba lagi.'}`;
      el('v2RbbPrintStatus').classList.add('is-error');
    } finally {
      button.disabled = false;
      button.querySelector('span')?.replaceChildren(document.createTextNode('Download PDF'));
    }
  }
  el('v2RbbDownloadButton')?.addEventListener('click', downloadPdf);
  load().catch((error) => { el('v2RbbPrintStatus').textContent = error.message; el('v2RbbPrintStatus').classList.add('is-error'); });
})();
</script>

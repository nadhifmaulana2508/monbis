<?php
$tab = strtolower((string)($_GET['tab'] ?? 'projection'));
if (!in_array($tab, ['projection', 'detail', 'aba'], true)) $tab = 'projection';
$rbbCategory = strtoupper((string)($_GET['category'] ?? ($tab === 'projection' ? 'NERACA' : 'ASET')));
if (!in_array($rbbCategory, ['NERACA','LABA_RUGI','ASET','KREDIT','DAMAS','PENDAPATAN','BEBAN','LIABILITAS','EKUITAS','IKHTISAR','ALL'], true)) $rbbCategory = $tab === 'projection' ? 'NERACA' : 'ASET';
$defaultRbbStart = date('Y-m', strtotime('first day of next month'));
$rbbMonthLabels = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
$rbbStartOptions = [];
$rbbStartCursor = new DateTimeImmutable('first day of this month');
for ($offset = 0; $offset <= 15; $offset++) {
    $optionDate = $rbbStartCursor->modify('+' . $offset . ' months');
    $optionValue = $optionDate->format('Y-m');
    $rbbStartOptions[$optionValue] = $rbbMonthLabels[(int)$optionDate->format('n')] . ' ' . $optionDate->format('Y');
}
$rbbProjectionCategoryOptions = [
    'IKHTISAR' => 'Ikhtisar',
    'NERACA' => 'Neraca · Aset, Liabilitas, Ekuitas',
    'ASET' => 'Neraca · Aset',
    'LIABILITAS' => 'Neraca · Liabilitas',
    'EKUITAS' => 'Neraca · Ekuitas',
    'LABA_RUGI' => 'Laba Rugi · Pendapatan & Beban',
    'PENDAPATAN' => 'Laba Rugi · Pendapatan',
    'BEBAN' => 'Laba Rugi · Beban',
    'ALL' => 'Semua kategori',
];
$rbbDetailCategoryOptions = [
    'ASET' => 'Aset', 'LIABILITAS' => 'Liabilitas', 'EKUITAS' => 'Ekuitas',
    'KREDIT' => 'Kredit', 'DAMAS' => 'DAMAS', 'PENDAPATAN' => 'Pendapatan',
    'BEBAN' => 'Beban', 'IKHTISAR' => 'Ikhtisar', 'ALL' => 'Semua kategori',
];
$rbbCategoryOptions = $tab === 'projection' ? $rbbProjectionCategoryOptions : $rbbDetailCategoryOptions;
$bankReferences = [
    'PT BANK DANAMON INDONESIA, Tbk', 'PT BANK MANDIRI (PERSERO), Tbk', 'PT BANK MASPION INDONESIA',
    'PT BANK MAYAPADA INTERNASIONAL, Tbk', 'PT BANK MEGA SYARIAH', 'PT BANK MUAMALAT INDONESIA',
    'PT BANK NEGARA INDONESIA (PERSERO), Tbk', 'PT BANK PEMBANGUNAN DAERAH BANTEN, Tbk',
    'PT BANK PERMATA, Tbk', 'PT BANK RAKYAT INDONESIA (PERSERO), Tbk', 'PT BANK SMC INDONESIA, Tbk',
    'PT BPD JAWA TENGAH', 'PT BPR ARTHA KARYA USAHA', 'PT BPR BKK TEMANGGUNG', 'PT BPR KOTA SEMARANG',
    'PT BPR DANAMAS ADI PERKASA', 'PT BPR HALIM PRIMA', 'PT BPR KARYA PRIMA SENTOSA', 'PT BPR LAWU ARTHA',
    'PT BPR LINGGA SEJAHTERA', 'PT BPR LUNA SINAR INDONESIA', 'PT BPR PARASAHAT BEKASI', 'PT BPR TATA ASIA',
    'PT BPRS KEDUNG ARTO', 'PT BPRS PNM MENTARI', 'Bank Umum Lainnya', 'BPR Lainnya',
];
?>
<section class="v2-page-heading v2-module-heading v2-rbb-page-heading">
  <div><p class="v2-eyebrow">MONBIS / INPUT RBB</p><h1>Input RBB</h1><p>Proyeksi, input detail COA, dan ABA dibangun ulang dengan component FE V2.</p></div>
  <div class="v2-page-status"><?= v2_badge('Akses terbatas', 'warning') ?></div>
</section>

<?php
$rbbFilterFields = [
    ['name'=>'period', 'id'=>'v2RbbPeriod', 'label'=>'Mulai input RBB', 'type'=>'select', 'value'=>$defaultRbbStart, 'options'=>$rbbStartOptions],
    ['name'=>'branch', 'id'=>'v2RbbBranch', 'label'=>'Kantor / Cabang', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Memuat kantor...']],
];
?>
<?= v2_filter_drawer($rbbFilterFields, 'v2RbbFilters') ?>

<datalist id="v2RbbBankReference"><?php foreach ($bankReferences as $bank): ?><option value="<?= v2_e($bank) ?>"><?php endforeach; ?></datalist>
<?php if ($tab === 'projection'): ?>
<?= v2_modal('v2RbbConfirmModal', 'Konfirmasi aksi', '<div class="v2-rbb-confirm"><div class="v2-rbb-confirm-icon">' . v2_icon('alert', 20) . '</div><div><p id="v2RbbConfirmMessage"></p><small>Perubahan status akan dicatat pada riwayat RBB.</small></div></div><div class="v2-rbb-confirm-actions"><button type="button" class="v2-button v2-button--soft" id="v2RbbConfirmCancel">Batal</button><button type="button" class="v2-button v2-button--primary" id="v2RbbConfirmOk">Lanjutkan</button></div>') ?>
<?php endif; ?>
<section class="v2-card v2-module-shell v2-rbb-page">
  <div class="v2-card-heading v2-module-toolbar v2-rbb-card-heading">
    <div><h2><?= $tab === 'projection' ? 'Proyeksi RBB' : ($tab === 'detail' ? 'Input Detail RBB' : 'Input RBB ABA') ?></h2></div>
<?php if ($tab !== 'aba'): ?>
    <div class="v2-rbb-header-controls">
      <label class="v2-search-field v2-rbb-search-field" for="v2RbbSearch"><?= v2_icon('search', 14) ?><input id="v2RbbSearch" type="search" placeholder="Cari COA, indikator, kode akun..." autocomplete="off"></label>
      <label class="v2-rbb-category-field" for="v2RbbCategory"><span>Kategori laporan</span><select id="v2RbbCategory" data-v2-filter-field="<?= $tab === 'projection' ? 'report_category' : 'category' ?>">
        <?php foreach ($rbbCategoryOptions as $value => $label): ?><option value="<?= v2_e($value) ?>"<?= $rbbCategory === $value ? ' selected' : '' ?>><?= v2_e($label) ?></option><?php endforeach; ?>
      </select></label>
      <form class="v2-rbb-print-link-form" id="v2RbbPrintForm" method="post" action="<?= v2_e($rbbRouteBase . '/print') ?>"><input type="hidden" name="kode_kantor" id="v2RbbPrintBranchInput"><input type="hidden" name="tahun_mulai" id="v2RbbPrintYearInput"><input type="hidden" name="bulan_mulai" id="v2RbbPrintMonthInput"><button type="submit" class="v2-button v2-button--soft v2-rbb-print-link" id="v2RbbPrint"><?= v2_icon('printer', 14) ?><span>Cetak</span></button></form>
    </div>
<?php endif; ?>
  </div>
  <div class="v2-card-body v2-module-body">
<?php if ($tab === 'projection'): ?>
    <div class="v2-module-callout v2-rbb-workflow"><div class="v2-rbb-workflow-copy"><strong>Workflow RBB</strong><span class="v2-module-status" id="v2RbbProjectionStatus">-</span></div><div class="v2-actions v2-rbb-approval-actions"><button type="button" class="v2-button v2-button--soft" id="v2RbbReopen" hidden>Edit kembali</button><button type="button" class="v2-button v2-button--primary" id="v2RbbSubmit" hidden>Ajukan</button><button type="button" class="v2-button v2-button--success" id="v2RbbApproveKanwil" hidden>Approve Kanwil</button><button type="button" class="v2-button v2-button--success" id="v2RbbApprovePusat" hidden>Approve Pusat</button><button type="button" class="v2-button v2-button--danger" id="v2RbbReject" hidden>Tolak</button></div></div>
    <div class="v2-table-wrap v2-module-table-wrap"><table class="v2-table v2-rbb-projection-table"><thead id="v2RbbProjectionHead"></thead><tbody id="v2RbbProjectionBody"><tr><td colspan="4" class="v2-empty">Memuat proyeksi RBB...</td></tr></tbody></table></div>
<?php elseif ($tab === 'detail'): ?>
    <div class="v2-module-callout"><div><strong>Input manual detail RBB</strong><p>Baris AUTO dikunci dan mengikuti formula backend. Baris MANUAL dapat diisi per periode.</p></div><button type="button" class="v2-button v2-button--primary" id="v2RbbDetailSave"><?= v2_icon('check', 16) ?><span>Simpan draft</span></button></div>
    <div class="v2-module-progress" id="v2RbbDetailStatus">Draft belum dimuat.</div>
    <div class="v2-table-wrap v2-module-table-wrap"><table class="v2-table v2-rbb-detail-table"><thead id="v2RbbDetailHead"></thead><tbody id="v2RbbDetailBody"><tr><td colspan="4" class="v2-empty">Memuat detail RBB...</td></tr></tbody></table></div>
<?php else: ?>
    <div class="v2-module-callout"><div><strong>Penempatan pada bank lain</strong><p>CKPN otomatis 0,5% dari nominal ABA dan pendapatan bunga memakai formula nominal × (1,25% / 12).</p></div><div class="v2-actions"><button type="button" class="v2-button v2-button--primary" id="v2RbbAbaAdd"><?= v2_icon('plus', 16) ?><span>Tambah baris</span></button><button type="button" class="v2-button v2-button--success" id="v2RbbAbaSave"><?= v2_icon('check', 16) ?><span>Simpan</span></button></div></div>
    <div class="v2-tabs v2-aba-tabs" role="tablist" aria-label="Bagian ABA"><button class="v2-tab is-active" type="button" data-v2-aba-view="placement">COA ABA</button><button class="v2-tab" type="button" data-v2-aba-view="ckpn">CKPN ABA</button><button class="v2-tab" type="button" data-v2-aba-view="interest">Pendapatan Bank Lain</button><button class="v2-tab" type="button" data-v2-aba-view="history">History 3 Tahun</button></div>
    <div class="v2-module-progress" id="v2RbbAbaStatus">Memuat ABA...</div>
    <div class="v2-table-wrap v2-module-table-wrap"><table class="v2-table v2-rbb-aba-table"><thead id="v2RbbAbaHead"></thead><tbody id="v2RbbAbaBody"><tr><td colspan="6" class="v2-empty">Memuat ABA...</td></tr></tbody></table></div>
<?php endif; ?>
  </div>
</section>

<script>
(() => {
  const TAB = <?= json_encode($tab) ?>;
  const API = <?= json_encode($apiBase . '/rbb/') ?>;
  const API_LAPKEU = <?= json_encode($apiBase . '/lapkeu/') ?>;
  const API_KODE = <?= json_encode($apiBase . '/kode/') ?>;
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const state = { rows: [], responses: [], yearData: {}, plans: {}, permissions: {}, periods: [], years: [], actualPreviousDate: null, realization: {values:{}, date:null, source:'', loaded:false}, abaView: 'placement', history: null, interestRate: .0125 };
  const el = (id) => document.getElementById(id);
  const field = (name) => document.querySelector(`[data-v2-filter-field="${name}"]`);
  const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const num = (value) => Number(value || 0);
  const fmt = (value) => { const number = num(value); const displayValue = Math.abs(number) >= 1000000 ? Math.trunc(number) : number; return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(displayValue); };
  const postTo = async (url, body, label = 'RBB') => { const response = await fetch(url, {method:'POST', credentials:'include', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body)}); const json = await response.json().catch(() => ({})); if (!response.ok || Number(json.status) !== 200) throw new Error(json.message || `Request ${label} gagal (${response.status})`); return json.data || {}; };
  const post = (body) => postTo(API, body, 'RBB');
  const toast = (message) => window.V2Toast?.show(message) || window.alert(message);
  const showError = (id, message, colspan) => { const node = el(id); if (node) node.innerHTML = `<tr><td colspan="${colspan}" class="v2-empty v2-negative">${esc(message)}</td></tr>`; };
  const yearValue = (value) => Math.max(2020, Math.min(2100, Number(value) || new Date().getFullYear()));
  const monthValue = (value) => Math.max(1, Math.min(12, Number(value) || 1));
  const periodKey = (year, month) => `${year}-${String(month).padStart(2, '0')}`;
  const periodLabel = (period) => `${months[period.month - 1]} ${period.year}`;
  const shortPeriodLabel = (period) => `<span>${months[period.month - 1]}</span><small>${period.year}</small>`;
  function selectedStart() { const raw = field('period')?.value || '2026-10'; const parts = raw.split('-').map(Number); return {year:yearValue(parts[0]), month:monthValue(parts[1])}; }
  function buildPeriods() { const {year:startYear, month:startMonth} = selectedStart(); const result = []; for (let month = startMonth; month <= 12; month++) result.push({year:startYear, month, key:periodKey(startYear, month)}); for (let month = 1; month <= 12; month++) result.push({year:startYear + 1, month, key:periodKey(startYear + 1, month)}); return result; }
  function setPeriodState() { state.periods = buildPeriods(); state.years = [...new Set(state.periods.map((period) => period.year))]; state.actualPreviousDate = null; const first = state.periods[0]; const last = state.periods[state.periods.length - 1]; if (el('v2RbbPeriodSummary')) el('v2RbbPeriodSummary').innerHTML = `<strong>Periode input: ${esc(periodLabel(first))} – ${esc(periodLabel(last))}</strong><span>${state.periods.length} bulan · tahun berikutnya otomatis penuh Januari–Desember</span>`; }
  function fillOffices(rows) { const select = el('v2RbbBranch'); if (!select) return; const current = select.value; select.innerHTML = rows.length ? rows.map(item => `<option value="${esc(String(item.kode_kantor).padStart(3,'0'))}">${esc(String(item.kode_kantor).padStart(3,'0'))} · ${esc(item.nama_kantor || 'Cabang')}</option>`).join('') : '<option value="">Kantor tidak tersedia</option>'; if (rows.some(item => String(item.kode_kantor).padStart(3,'0') === current)) select.value = current; else if (rows[0]) select.value = String(rows[0].kode_kantor).padStart(3,'0'); }
  function filters(year) { const start = selectedStart(); return {tahun:year, tahun_mulai:start.year, bulan_mulai:start.month, kode_kantor:field('branch')?.value || '', kategori:field('category')?.value || 'ASET'}; }
  function dataRequest(year) { if (TAB === 'projection') return post({type:'rbb_projection_data', ...filters(year)}); if (TAB === 'detail') return post({type:'rbb_planning_data', ...filters(year)}); return post({type:'rbb_aba_data', tahun:year, kode_kantor:field('branch')?.value || '', tipe:'PLACEMENT'}); }
  function v2Badge(label, tone) { const palette = tone === 'success' ? 'v2-inline-badge v2-inline-badge--success' : 'v2-inline-badge'; return `<span class="${palette}">${esc(label)}</span>`; }
  function money(value) { return fmt(value); }
  function rowMap(data) { return new Map((data?.rows || []).map((row) => [String(row.kode_monbis ?? ''), row])); }
  function actualLabel() { if (!state.actualPreviousDate) return 'SEP 2026'; const parts = state.actualPreviousDate.split('-').map(Number); return `${months[parts[1] - 1].slice(0, 3).toUpperCase()} ${parts[0]}`; }
  function headerCells() { return state.periods.map((period) => `<th class="v2-rbb-period-head" data-period="${period.key}">${shortPeriodLabel(period)}</th>`).join(''); }
  function renderHeaders() { const periods = headerCells(); const actual = `<th class="v2-rbb-actual-head">${actualLabel()}</th>`; if (el('v2RbbProjectionHead')) el('v2RbbProjectionHead').innerHTML = `<tr><th>KODE</th><th>COA / INDIKATOR</th><th>KATEGORI</th>${actual}${periods}</tr>`; if (el('v2RbbDetailHead')) el('v2RbbDetailHead').innerHTML = `<tr><th>KODE</th><th>COA / INDIKATOR</th><th>INPUT</th>${actual}${periods}</tr>`; }
  function mergeRows(responses) { const byYear = new Map(responses.map((item) => [Number(item.year ?? item.tahun), rowMap(item)])); state.actualPreviousDate = responses.find((item) => item.actual_previous_date)?.actual_previous_date || null; const codes = []; const seen = new Set(); responses.forEach((data) => (data.rows || []).forEach((row) => { const code = String(row.kode_monbis ?? ''); if (!seen.has(code)) { seen.add(code); codes.push(code); } })); return codes.map((code) => { let base = null; for (const data of responses) { base = rowMap(data).get(code); if (base) break; } const periodValues = Object.fromEntries(state.periods.map((period) => [period.key, num(byYear.get(period.year)?.get(code)?.values?.[period.month])])); return {...base, actualPrevious:base?.actual_previous, periodValues, total:Object.values(periodValues).reduce((sum, value) => sum + num(value), 0)}; }); }
  function statusSummary() { return state.years.map((year) => `${year}: ${state.plans[year]?.status || 'DRAFT BARU'}`).join(' · '); }
  function renderProjection(responses) { state.rows = mergeRows(responses); renderHeaders(); el('v2RbbProjectionStatus').textContent = statusSummary(); updateProjectionActions(); const body = el('v2RbbProjectionBody'); const colspan = 4 + state.periods.length; body.innerHTML = state.rows.length ? state.rows.map((row) => `<tr><td class="v2-code">${esc(row.kode_monbis)}</td><td class="v2-tooltip-host" data-v2-tooltip="${esc(row.keterangan || '-')}" tabindex="0"><strong>${esc(row.keterangan || '-')}</strong><small>${esc(row.kode_perkiraan || '-')}</small></td><td>${esc(row.kategori || '-')}</td><td class="v2-num v2-actual-cell">${row.actualPrevious == null ? '-' : money(row.actualPrevious)}</td>${state.periods.map((period) => `<td class="v2-num">${money(row.periodValues?.[period.key])}</td>`).join('')}</tr>`).join('') : `<tr><td colspan="${colspan}" class="v2-empty">Belum ada COA proyeksi.</td></tr>`; }
  function updateProjectionActions() { const statuses = state.years.map((year) => state.plans[year]?.status || ''); const complete = statuses.length === state.years.length && statuses.every(Boolean); const actions = {reopen:complete && statuses.some((status) => !['DRAFT','REJECTED'].includes(status)), submit:complete && statuses.every((status) => ['DRAFT','REJECTED'].includes(status)), approveKanwil:complete && statuses.every((status) => status === 'SUBMITTED_KANWIL'), approvePusat:complete && statuses.every((status) => status === 'APPROVED_KANWIL'), reject:complete && statuses.every((status) => ['SUBMITTED_KANWIL','APPROVED_KANWIL'].includes(status))}; el('v2RbbReopen')?.toggleAttribute('hidden', !actions.reopen); el('v2RbbSubmit')?.toggleAttribute('hidden', !actions.submit); el('v2RbbApproveKanwil')?.toggleAttribute('hidden', !actions.approveKanwil); el('v2RbbApprovePusat')?.toggleAttribute('hidden', !actions.approvePusat); el('v2RbbReject')?.toggleAttribute('hidden', !actions.reject); }
  function blankAbaRow() { return {id:0, no_urut:0, jenis_penempatan:'Tabungan', nama_bank:'', no_rekening_aba:'', no_rekening_cbs:'', values:Object.fromEntries(months.map((_, index) => [index + 1, 0])), interest_values:Object.fromEntries(months.map((_, index) => [index + 1, 0]))}; }
  function mergeAbaRows(responses) { const count = Math.max(0, ...responses.map((data) => (data.rows || []).length)); return Array.from({length:count}, (_, index) => { const yearRows = {}; state.years.forEach((year) => { yearRows[year] = {...blankAbaRow(), ...(responses.find((data) => Number(data.tahun) === year)?.rows || [])[index]}; }); const first = yearRows[state.years[0]] || blankAbaRow(); const values = Object.fromEntries(state.periods.map((period) => [period.key, num(yearRows[period.year]?.values?.[period.month])])); const interestValues = Object.fromEntries(state.periods.map((period) => [period.key, num(yearRows[period.year]?.interest_values?.[period.month])])); return {...first, yearRows, values, interestValues}; }); }
  function syncDetailInputs() { document.querySelectorAll('#v2RbbDetailBody [data-rbb-year][data-rbb-month]').forEach((input) => { const year = Number(input.dataset.rbbYear); const code = String(input.dataset.rbbCode); const row = (state.yearData[year]?.rows || []).find((item) => String(item.kode_monbis) === code); if (row) { row.values = row.values || {}; row.values[input.dataset.rbbMonth] = num(input.value); } }); }
  function collectDetailForYear(year) { return (state.yearData[year]?.rows || []).map((row) => ({kode_monbis:row.kode_monbis, values:Object.fromEntries(months.map((_, index) => [index + 1, num(row.values?.[index + 1])]))})); }
  function syncAbaInputs() { document.querySelectorAll('#v2RbbAbaBody tr[data-aba-index]').forEach((tr) => { const index = Number(tr.dataset.abaIndex); const current = state.rows[index]; if (!current) return; state.years.forEach((year) => { const row = current.yearRows[year] || blankAbaRow(); row.jenis_penempatan = tr.querySelector('[data-aba-field="jenis"]')?.value || row.jenis_penempatan; row.nama_bank = tr.querySelector('[data-aba-field="bank"]')?.value || ''; row.no_rekening_aba = tr.querySelector('[data-aba-field="aba"]')?.value || ''; row.no_rekening_cbs = tr.querySelector('[data-aba-field="cbs"]')?.value || ''; row.values = row.values || {}; current.yearRows[year] = row; }); tr.querySelectorAll('[data-aba-year][data-aba-month]').forEach((input) => { const year = Number(input.dataset.abaYear); const row = current.yearRows[year] || blankAbaRow(); row.values = row.values || {}; row.values[input.dataset.abaMonth] = num(input.value); current.yearRows[year] = row; }); }); }
  function collectAbaForYear(year) { return state.rows.map((row, index) => { const source = row.yearRows[year] || blankAbaRow(); return {id:num(source.id), no_urut:index + 1, jenis_penempatan:source.jenis_penempatan || 'Tabungan', nama_bank:source.nama_bank || '', no_rekening_aba:source.no_rekening_aba || '', no_rekening_cbs:source.no_rekening_cbs || '', values:Object.fromEntries(months.map((_, monthIndex) => [monthIndex + 1, num(source.values?.[monthIndex + 1])]))}; }); }
  /* v2 layout override: no right-side TOTAL column. */
  function renderDetail(responses) {
    state.rows = mergeRows(responses);
    renderHeaders();
    const editable = state.years.every((year) => state.permissions[year] !== false);
    el('v2RbbDetailStatus').textContent = `${statusSummary()} · ${editable ? 'dapat diedit' : 'sebagian periode terkunci'}`;
    const sums = Object.fromEntries(state.periods.map((period) => [period.key, 0]));
    let actualTotal = 0;
    state.rows.forEach((row) => {
      actualTotal += num(row.actualPrevious);
      state.periods.forEach((period) => { sums[period.key] += num(row.periodValues?.[period.key]); });
    });
    const total = `<tr class="v2-total-row"><td>-</td><td><strong>GRAND TOTAL</strong></td><td>-</td><td class="v2-num">${state.rows.length ? money(actualTotal) : '-'}</td>${state.periods.map((period) => `<td class="v2-num"><strong>${money(sums[period.key])}</strong></td>`).join('')}</tr>`;
    const rows = state.rows.map((row) => {
      const auto = row.input_mode === 'AUTO';
      const cells = state.periods.map((period) => {
        const canEdit = !auto && state.permissions[period.year] !== false;
        return `<td><input class="v2-inline-input v2-rbb-number" data-rbb-code="${esc(row.kode_monbis)}" data-rbb-year="${period.year}" data-rbb-month="${period.month}" value="${num(row.periodValues?.[period.key])}" inputmode="decimal" type="number" min="0" step="0.01"${canEdit ? '' : ' disabled'}></td>`;
      }).join('');
      return `<tr data-rbb-code="${esc(row.kode_monbis)}"><td class="v2-code">${esc(row.kode_monbis)}</td><td class="v2-tooltip-host" data-v2-tooltip="${esc(row.keterangan || '-')}" tabindex="0"><strong>${esc(row.keterangan || '-')}</strong><small>${esc(row.kode_perkiraan || '-')}</small></td><td>${v2Badge(auto ? 'AUTO' : 'MANUAL', auto ? 'default' : 'success')}</td><td class="v2-num v2-actual-cell">${row.actualPrevious == null ? '-' : money(row.actualPrevious)}</td>${cells}</tr>`;
    }).join('');
    const colspan = 4 + state.periods.length;
    el('v2RbbDetailBody').innerHTML = state.rows.length ? total + rows : `<tr><td colspan="${colspan}" class="v2-empty">Belum ada COA pada kategori ini.</td></tr>`;
  }

  function renderAbaHead(view) {
    if (view === 'history') {
      const years = state.history?.years || [2024, 2025, 2026];
      el('v2RbbAbaHead').innerHTML = `<tr><th rowspan="2">BULAN</th><th colspan="${years.length}">PENEMPATAN ABA</th><th colspan="${years.length}">CKPN ABA</th><th colspan="${years.length}">PENDAPATAN BUNGA</th></tr><tr>${years.map((year) => `<th>${year}</th>`).join('')}${years.map((year) => `<th>${year}</th>`).join('')}${years.map((year) => `<th>${year}</th>`).join('')}</tr>`;
      return;
    }
    el('v2RbbAbaHead').innerHTML = `<tr><th>AKSI</th><th>NO</th><th>JENIS</th><th>NAMA BANK</th><th>NO. ABA</th><th>NO. CBS</th>${headerCells()}</tr>`;
  }

  function renderAba() {
    const view = state.abaView;
    renderAbaHead(view);
    const body = el('v2RbbAbaBody');
    if (view === 'history') {
      const history = state.history || {};
      const years = history.years || [2024, 2025, 2026];
      body.innerHTML = months.map((month, index) => `<tr><td>${month}</td>${years.map((year) => `<td class="v2-num">${money(history.nominal?.[year]?.[index + 1])}</td>`).join('')}${years.map((year) => `<td class="v2-num">${money(history.ckpn?.[year]?.[index + 1])}</td>`).join('')}${years.map((year) => `<td class="v2-num">${money(history.interest?.[year]?.[index + 1])}</td>`).join('')}</tr>`).join('');
      return;
    }
    if (!state.rows.length) {
      const colspan = 6 + state.periods.length;
      body.innerHTML = `<tr><td colspan="${colspan}" class="v2-empty">Belum ada rekening. Klik Tambah baris untuk mulai mengisi.</td></tr>`;
      return;
    }
    const factor = view === 'ckpn' ? .005 : state.interestRate / 12;
    body.innerHTML = state.rows.map((row, index) => {
      const readonly = view !== 'placement';
      const cells = state.periods.map((period) => {
        const nominal = num(row.values?.[period.key]);
        const interest = num(row.interestValues?.[period.key]);
        const value = view === 'ckpn' ? nominal * factor : view === 'interest' ? interest : nominal;
        return readonly ? `<td class="v2-num">${money(value)}</td>` : `<td><input class="v2-inline-input v2-rbb-number" data-aba-index="${index}" data-aba-year="${period.year}" data-aba-month="${period.month}" value="${num(value)}" inputmode="decimal" type="number" min="0" step="0.01"></td>`;
      }).join('');
      return `<tr data-aba-index="${index}"><td>${readonly ? '-' : `<button type="button" class="v2-delete-button" data-aba-delete="${index}" aria-label="Hapus baris">&times;</button>`}</td><td>${index + 1}</td><td><select class="v2-inline-input" data-aba-field="jenis"${readonly ? ' disabled' : ''}><option value="Tabungan"${row.jenis_penempatan === 'Tabungan' ? ' selected' : ''}>Tabungan</option><option value="Deposito"${row.jenis_penempatan === 'Deposito' ? ' selected' : ''}>Deposito</option><option value="Giro"${row.jenis_penempatan === 'Giro' ? ' selected' : ''}>Giro</option></select></td><td><input class="v2-inline-input v2-aba-bank" list="v2RbbBankReference" data-aba-field="bank" value="${esc(row.nama_bank || '')}"${readonly ? ' disabled' : ''}></td><td><input class="v2-inline-input" data-aba-field="aba" value="${esc(row.no_rekening_aba || '')}"${readonly ? ' disabled' : ''}></td><td><input class="v2-inline-input" data-aba-field="cbs" value="${esc(row.no_rekening_cbs || '')}"${readonly ? ' disabled' : ''}></td>${cells}</tr>`;
    }).join('');
  }

  async function load() {
    setPeriodState();
    renderHeaders();
    const target = TAB === 'projection' ? 'v2RbbProjectionBody' : TAB === 'detail' ? 'v2RbbDetailBody' : 'v2RbbAbaBody';
    const colspan = TAB === 'projection' ? 4 + state.periods.length : TAB === 'detail' ? 4 + state.periods.length : 6 + state.periods.length;
    const currentNode = el(target);
    if (currentNode) currentNode.innerHTML = `<tr><td colspan="${colspan}" class="v2-empty">Memuat ${TAB === 'aba' ? 'ABA' : 'data RBB'} untuk ${state.periods.length} periode...</td></tr>`;
    try {
      const responses = await Promise.all(state.years.map((year) => dataRequest(year)));
      state.yearData = Object.fromEntries(state.years.map((year, index) => [year, responses[index]]));
      state.plans = Object.fromEntries(state.years.map((year, index) => [year, responses[index].plan || null]));
      state.permissions = Object.fromEntries(state.years.map((year, index) => [year, responses[index].permissions?.can_edit !== false]));
      if (TAB === 'projection') renderProjection(responses);
      else if (TAB === 'detail') renderDetail(responses);
      else {
        state.rows = mergeAbaRows(responses);
        state.history = responses[0]?.history || null;
        state.interestRate = num(responses[0]?.interest_rate) || .0125;
        el('v2RbbAbaStatus').textContent = `${statusSummary()} · Rate bunga ${(state.interestRate * 100).toFixed(2)}% per tahun`;
        renderAba();
      }
    } catch (error) { showError(target, error.message, colspan); }
  }

  function activeCategory() { return String(field(TAB === 'projection' ? 'report_category' : 'category')?.value || (TAB === 'projection' ? 'NERACA' : 'ASET')).toUpperCase(); }
  function actualPreviousDate() { const start = selectedStart(); return new Date(Date.UTC(start.year, start.month - 1, 0)).toISOString().slice(0, 10); }
  function buildIkhtisarRealization(data, mapping = {}) {
    const detail = data?.ringkasan_detail || {};
    const makro = data?.makro || {};
    const mappedDetail = mapping?.detail_actual || {};
    const damas = mappedDetail.damas || {};
    const credit = mappedDetail.credit || {};
    const kredit = detail.kredit_diberikan || {};
    const rasioUtama = detail.rasio_utama || {};
    const rasio = data?.kesehatan_rasio || {};
    const nominal = data?.rasio?.detail_nominal || {};
    const bakiDebet = num(kredit.baki_debet);
    const saldoBank = num(kredit.saldo_bank_ead);
    const asetProduktif = num(nominal.rata_aset_produktif);
    return {
      '1': num(makro.aset?.nominal_aktual),
      '2': num(damas.total?.rupiah ?? detail.dana_masyarakat?.total ?? makro.dpk?.nominal_aktual),
      '3': num(damas.tabungan?.rupiah),
      '4': num(damas.deposito?.rupiah),
      '5': num(credit.total?.rupiah ?? (bakiDebet + saldoBank)),
      '6': num(makro.pendapatan?.nominal_aktual),
      '7': num(makro.biaya?.nominal_aktual),
      '8': num(detail.laba_sebelum_pajak ?? makro.laba_rugi?.nominal_aktual),
      '12': num(rasioUtama.kap),
      '13': num(rasioUtama.ckpn_terhadap_ppka),
      '15': num(rasioUtama.npl_baki_debet_gross),
      '16': num(rasioUtama.npl_baki_debet_netto),
      '17': asetProduktif ? bakiDebet / asetProduktif * 100 : 0,
      '18': num(rasio.roa?.persen_aktual),
      '19': num(rasio.nim?.persen_aktual),
      '20': num(rasio.bopo?.persen_aktual),
      '21': num(rasio.cash?.persen_aktual),
      '22': num(rasio.ldr?.persen_aktual),
      '24': num(rasio.casa?.persen_aktual),
    };
  }
  async function loadRealization() {
    const category = activeCategory();
    const date = actualPreviousDate();
    const branch = field('branch')?.value || '';
    const start = selectedStart();
    const period = `${start.year}-${String(start.month).padStart(2, '0')}-01`;
    if (!branch) return {values:{}, date, source:'Belum ada kantor', loaded:false};
    if (!['IKHTISAR', 'NERACA', 'ASET', 'LIABILITAS', 'EKUITAS', 'LABA_RUGI', 'PENDAPATAN', 'BEBAN', 'ALL'].includes(category)) return {values:{}, date, source:'Saldo dasar', loaded:false};
    try {
      if (category === 'IKHTISAR') {
        const actualData = await postTo(API_LAPKEU, {type:'tv_makro_summary', harian_date:date, kode_kantor:branch, h7_fallback:true}, 'API Realisasi Ikhtisar');
        const actualDate = actualData.info_tanggal?.aktual || date;
        let mapping = {};
        try { mapping = await postTo(API, {type:'ikhtisar_rbb', harian_date:actualDate, kode_kantor:branch}, 'API Ikhtisar'); } catch (error) { mapping = {}; }
        return {values:buildIkhtisarRealization(actualData, mapping), date:actualDate, source:'Ikhtisar', loaded:true};
      }
      if (category === 'ALL') {
        const actualJson = await postTo(API_LAPKEU, {type:'tv_makro_summary', harian_date:date, kode_kantor:branch, h7_fallback:true}, 'API Realisasi Ikhtisar');
        const actualDate = actualJson.info_tanggal?.aktual || date;
        const [mapping, neracaJson, labaRugiJson] = await Promise.all([
          postTo(API, {type:'ikhtisar_rbb', harian_date:actualDate, kode_kantor:branch}, 'API Ikhtisar').catch(() => ({})),
          postTo(API, {type:'lapkeu_rbb_vs_realisasi', jenis_laporan:'neraca', harian_date:actualDate, periode_rbb:period, kode_kantor:branch, h7_fallback:true, include_missing:true}, 'API Realisasi Neraca'),
          postTo(API, {type:'lapkeu_rbb_vs_realisasi', jenis_laporan:'laba_rugi', harian_date:actualDate, periode_rbb:period, kode_kantor:branch, h7_fallback:true, include_missing:true}, 'API Realisasi Laba Rugi'),
        ]);
        const values = {...buildIkhtisarRealization(actualJson || {}, mapping)};
        [...(neracaJson.data || []), ...(labaRugiJson.data || [])].forEach((row) => { const code = String(row.kode_monbis ?? ''); if (code) values[code] = num(row.realisasi_actual); });
        return {values, date:actualDate || neracaJson.meta?.harian_date || date, source:'Semua kategori', loaded:true};
      }
      const jenis = ['LABA_RUGI', 'PENDAPATAN', 'BEBAN'].includes(category) ? 'laba_rugi' : 'neraca';
      const response = await postTo(API, {type:'lapkeu_rbb_vs_realisasi', jenis_laporan:jenis, harian_date:date, periode_rbb:period, kode_kantor:branch, h7_fallback:true, include_missing:true}, 'API RBB vs Realisasi');
      const values = Object.fromEntries((response.data || []).map((row) => [String(row.kode_monbis ?? ''), num(row.realisasi_actual)]).filter(([code]) => code));
      return {values, date:response.meta?.harian_date || date, source:jenis === 'neraca' ? 'Neraca' : 'Laba Rugi', loaded:true};
    } catch (error) {
      return {values:{}, date, source:`Fallback (${error.message})`, loaded:false};
    }
  }
  function mergeRows(responses) {
    const byYear = new Map(responses.map((item) => [Number(item.year ?? item.tahun), rowMap(item)]));
    const actualMap = state.realization.values || {};
    state.actualPreviousDate = state.realization.date || responses.find((item) => item.actual_previous_date)?.actual_previous_date || null;
    const codes = []; const seen = new Set();
    responses.forEach((data) => (data.rows || []).forEach((row) => { const code = String(row.kode_monbis ?? ''); if (!seen.has(code)) { seen.add(code); codes.push(code); } }));
    return codes.map((code) => {
      let base = null;
      for (const data of responses) { base = rowMap(data).get(code); if (base) break; }
      const periodValues = Object.fromEntries(state.periods.map((period) => [period.key, num(byYear.get(period.year)?.get(code)?.values?.[period.month])]));
      const actualPrevious = state.realization.loaded ? (Object.prototype.hasOwnProperty.call(actualMap, code) ? actualMap[code] : null) : base?.actual_previous;
      return {...base, actualPrevious, periodValues, total:Object.values(periodValues).reduce((sum, value) => sum + num(value), 0)};
    });
  }
  function filteredRbbRows() {
    const query = String(el('v2RbbSearch')?.value || '').trim().toLowerCase();
    if (!query) return state.rows;
    return state.rows.filter((row) => [row.kode_monbis, row.keterangan, row.kategori, row.kode_perkiraan, row.sandi_lbbpr].map((value) => String(value ?? '').toLowerCase()).join(' ').includes(query));
  }
  function updatePrintLink() { const form = el('v2RbbPrintForm'); if (!form) return; const start = selectedStart(); const values = {kode_kantor:field('branch')?.value || '', tahun_mulai:String(start.year), bulan_mulai:String(start.month)}; Object.entries(values).forEach(([name, value]) => { const input = form.querySelector(`[name="${name}"]`); if (input) input.value = value; }); }
  function updateRbbResultMeta() { updatePrintLink(); }
  function activeCategoryWhere(year) { const start = selectedStart(); return {tahun:year, tahun_mulai:start.year, bulan_mulai:start.month, kode_kantor:field('branch')?.value || '', kategori:activeCategory()}; }
  function filters(year) { return activeCategoryWhere(year); }
  function dataRequest(year) { if (TAB === 'projection') return post({type:'rbb_projection_data', ...filters(year)}); if (TAB === 'detail') return post({type:'rbb_planning_data', ...filters(year)}); return post({type:'rbb_aba_data', tahun:year, kode_kantor:field('branch')?.value || '', tipe:'PLACEMENT'}); }
  function renderProjection(responses) {
    state.rows = mergeRows(responses); renderHeaders(); el('v2RbbProjectionStatus').textContent = statusSummary(); updateProjectionActions(); updateRbbResultMeta();
    const visible = filteredRbbRows(); const body = el('v2RbbProjectionBody'); const colspan = 4 + state.periods.length;
    body.innerHTML = visible.length ? visible.map((row) => `<tr><td class="v2-code">${esc(row.kode_monbis)}</td><td class="v2-tooltip-host" data-v2-tooltip="${esc(row.keterangan || '-')}" tabindex="0"><strong>${esc(row.keterangan || '-')}</strong><small>${esc(row.kode_perkiraan || '-')}</small></td><td>${esc(row.kategori || '-')}</td><td class="v2-num v2-actual-cell">${row.actualPrevious == null ? '-' : money(row.actualPrevious)}</td>${state.periods.map((period) => `<td class="v2-num">${money(row.periodValues?.[period.key])}</td>`).join('')}</tr>`).join('') : `<tr><td colspan="${colspan}" class="v2-empty">${state.rows.length ? 'Data tidak ditemukan. Coba ubah pencarian.' : 'Belum ada COA proyeksi.'}</td></tr>`;
  }
  function renderDetail(responses) {
    state.rows = mergeRows(responses); renderHeaders(); const editable = state.years.every((year) => state.permissions[year] !== false); el('v2RbbDetailStatus').textContent = `${statusSummary()} · ${editable ? 'dapat diedit' : 'sebagian periode terkunci'}`; updateRbbResultMeta();
    const visible = filteredRbbRows(); const sums = Object.fromEntries(state.periods.map((period) => [period.key, 0])); let actualTotal = 0;
    visible.forEach((row) => { actualTotal += num(row.actualPrevious); state.periods.forEach((period) => { sums[period.key] += num(row.periodValues?.[period.key]); }); });
    const total = `<tr class="v2-total-row"><td>-</td><td><strong>GRAND TOTAL</strong></td><td>-</td><td class="v2-num">${visible.length ? money(actualTotal) : '-'}</td>${state.periods.map((period) => `<td class="v2-num"><strong>${money(sums[period.key])}</strong></td>`).join('')}</tr>`;
    const rows = visible.map((row) => { const auto = row.input_mode === 'AUTO'; const cells = state.periods.map((period) => { const canEdit = !auto && state.permissions[period.year] !== false; return `<td><input class="v2-inline-input v2-rbb-number" data-rbb-code="${esc(row.kode_monbis)}" data-rbb-year="${period.year}" data-rbb-month="${period.month}" value="${num(row.periodValues?.[period.key])}" inputmode="decimal" type="number" min="0" step="0.01"${canEdit ? '' : ' disabled'}></td>`; }).join(''); return `<tr data-rbb-code="${esc(row.kode_monbis)}"><td class="v2-code">${esc(row.kode_monbis)}</td><td class="v2-tooltip-host" data-v2-tooltip="${esc(row.keterangan || '-')}" tabindex="0"><strong>${esc(row.keterangan || '-')}</strong><small>${esc(row.kode_perkiraan || '-')}</small></td><td>${v2Badge(auto ? 'AUTO' : 'MANUAL', auto ? 'default' : 'success')}</td><td class="v2-num v2-actual-cell">${row.actualPrevious == null ? '-' : money(row.actualPrevious)}</td>${cells}</tr>`; }).join('');
    const colspan = 4 + state.periods.length; el('v2RbbDetailBody').innerHTML = visible.length ? total + rows : `<tr><td colspan="${colspan}" class="v2-empty">${state.rows.length ? 'Data tidak ditemukan. Coba ubah pencarian.' : 'Belum ada COA pada kategori ini.'}</td></tr>`;
  }
  async function load() {
    setPeriodState(); renderHeaders(); const target = TAB === 'projection' ? 'v2RbbProjectionBody' : TAB === 'detail' ? 'v2RbbDetailBody' : 'v2RbbAbaBody'; const colspan = TAB === 'projection' ? 4 + state.periods.length : TAB === 'detail' ? 4 + state.periods.length : 6 + state.periods.length; const currentNode = el(target); if (currentNode) currentNode.innerHTML = `<tr><td colspan="${colspan}" class="v2-empty">Memuat ${TAB === 'aba' ? 'ABA' : 'data RBB'} untuk ${state.periods.length} periode...</td></tr>`;
    try { const responses = await Promise.all(state.years.map((year) => dataRequest(year))); state.responses = responses; state.yearData = Object.fromEntries(state.years.map((year, index) => [year, responses[index]])); state.plans = Object.fromEntries(state.years.map((year, index) => [year, responses[index].plan || null])); state.permissions = Object.fromEntries(state.years.map((year, index) => [year, responses[index].permissions?.can_edit !== false])); if (TAB !== 'aba') state.realization = await loadRealization(); if (TAB === 'projection') renderProjection(responses); else if (TAB === 'detail') renderDetail(responses); else { state.rows = mergeAbaRows(responses); state.history = responses[0]?.history || null; state.interestRate = num(responses[0]?.interest_rate) || .0125; el('v2RbbAbaStatus').textContent = `${statusSummary()} · Rate bunga ${(state.interestRate * 100).toFixed(2)}% per tahun`; renderAba(); } } catch (error) { showError(target, error.message, colspan); }
  }

  function confirmTransition(message) { const modal = el('v2RbbConfirmModal'); const messageNode = el('v2RbbConfirmMessage'); const confirmButton = el('v2RbbConfirmOk'); const cancelButton = el('v2RbbConfirmCancel'); if (!modal || !messageNode || !confirmButton || !cancelButton) return Promise.resolve(false); return new Promise((resolve) => { let settled = false; const closeButtons = [cancelButton, ...modal.querySelectorAll('[data-v2-modal-close]')]; const cleanup = () => { confirmButton.removeEventListener('click', onConfirm); closeButtons.forEach((button) => button.removeEventListener('click', onCancel)); document.removeEventListener('keydown', onKeydown); }; const finish = (result) => { if (settled) return; settled = true; cleanup(); modal.setAttribute('hidden', ''); resolve(result); }; const onConfirm = () => finish(true); const onCancel = () => finish(false); const onKeydown = (event) => { if (event.key === 'Escape') finish(false); }; messageNode.textContent = message; confirmButton.addEventListener('click', onConfirm); closeButtons.forEach((button) => button.addEventListener('click', onCancel)); document.addEventListener('keydown', onKeydown); modal.removeAttribute('hidden'); window.setTimeout(() => confirmButton.focus(), 0); }); }
  async function transition(action) { const statuses = state.years.map((year) => state.plans[year]?.status || ''); if (statuses.some((status) => !status)) return toast('Simpan draft untuk semua tahun terlebih dahulu.'); const message = action === 'REOPEN' ? 'Buka kembali RBB ini untuk diedit?' : action === 'REJECT' ? 'Tolak draft RBB ini?' : action === 'SUBMIT_KANWIL' ? 'Ajukan draft RBB ke Kanwil?' : action === 'APPROVE_KANWIL' ? 'Approve draft RBB sebagai Kanwil?' : 'Approve draft RBB sebagai Pusat?'; if (!(await confirmTransition(message))) return; try { const years = action === 'REOPEN' ? state.years.filter((year) => !['DRAFT','REJECTED'].includes(state.plans[year]?.status || '')) : state.years; for (const year of years) { const payload = action === 'REOPEN' ? {type:'rbb_planning_reopen', tahun:year, kode_kantor:field('branch').value} : action === 'SUBMIT_KANWIL' ? {type:'rbb_planning_submit', tahun:year, kode_kantor:field('branch').value} : {type:'rbb_planning_approve', action, tahun:year, kode_kantor:field('branch').value}; await post(payload); } toast(action === 'REOPEN' ? 'RBB berhasil dibuka kembali untuk diedit.' : 'Status Proyeksi RBB berhasil diperbarui.'); await load(); } catch (error) { toast(error.message); } }
  async function init() { try { const response = await fetch(API_KODE, {method:'POST', credentials:'include', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'})}); const json = await response.json(); fillOffices(Array.isArray(json.data) ? json.data.filter((item) => String(item.kode_kantor) !== '000') : []); await load(); } catch (error) { const target = TAB === 'projection' ? 'v2RbbProjectionBody' : TAB === 'detail' ? 'v2RbbDetailBody' : 'v2RbbAbaBody'; showError(target, error.message, 6); } }
  ['period','branch','report_category','category'].forEach((name) => field(name)?.addEventListener('change', load));
  el('v2RbbSearch')?.addEventListener('input', () => { if (TAB === 'projection') renderProjection(state.responses); else if (TAB === 'detail') renderDetail(state.responses); });
  el('v2RbbDetailSave')?.addEventListener('click', async () => { const button = el('v2RbbDetailSave'); button.disabled = true; try { syncDetailInputs(); for (const year of state.years) await post({type:'rbb_planning_save', tahun:year, kode_kantor:field('branch').value, kategori:field('category').value, rows:collectDetailForYear(year)}); toast('Draft RBB berhasil disimpan untuk semua periode.'); await load(); } catch (error) { toast(error.message); } finally { button.disabled = false; } });
  el('v2RbbAbaAdd')?.addEventListener('click', () => { const yearRows = Object.fromEntries(state.years.map((year) => [year, blankAbaRow()])); state.rows.push({...blankAbaRow(), yearRows, values:Object.fromEntries(state.periods.map((period) => [period.key, 0])), interestValues:Object.fromEntries(state.periods.map((period) => [period.key, 0]))}); renderAba(); });
  el('v2RbbAbaSave')?.addEventListener('click', async () => { const button = el('v2RbbAbaSave'); button.disabled = true; try { syncAbaInputs(); for (const year of state.years) await post({type:'rbb_aba_save', tahun:year, kode_kantor:field('branch').value, rows:collectAbaForYear(year)}); toast('Input ABA berhasil disimpan untuk semua periode.'); await load(); } catch (error) { toast(error.message); } finally { button.disabled = false; } });
  el('v2RbbSubmit')?.addEventListener('click', () => transition('SUBMIT_KANWIL'));
  el('v2RbbApproveKanwil')?.addEventListener('click', () => transition('APPROVE_KANWIL'));
  el('v2RbbApprovePusat')?.addEventListener('click', () => transition('APPROVE_PUSAT'));
  el('v2RbbReject')?.addEventListener('click', () => transition('REJECT'));
  el('v2RbbReopen')?.addEventListener('click', () => transition('REOPEN'));
  document.addEventListener('click', (event) => { const deleteButton = event.target.closest('[data-aba-delete]'); if (deleteButton) { state.rows.splice(Number(deleteButton.dataset.abaDelete), 1); renderAba(); } const viewButton = event.target.closest('[data-v2-aba-view]'); if (viewButton) { state.abaView = viewButton.dataset.v2AbaView; document.querySelectorAll('[data-v2-aba-view]').forEach((item) => item.classList.toggle('is-active', item === viewButton)); el('v2RbbAbaAdd')?.toggleAttribute('hidden', state.abaView !== 'placement'); el('v2RbbAbaSave')?.toggleAttribute('hidden', state.abaView !== 'placement'); renderAba(); } });
  init();
})();
</script>

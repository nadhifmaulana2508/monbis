<?php
$tab = strtolower((string)($_GET['tab'] ?? 'summary'));
if (!in_array($tab, ['summary', 'calculate', 'generate', 'setting'], true)) $tab = 'summary';
if ($tab === 'summary') {
  $kpiTabTitle = 'Rekap KPI AO';
  $kpiTabSubtitle = 'Rekap penilaian tahunan dari seluruh periode yang sudah digenerate.';
} elseif ($tab === 'calculate') {
  $kpiTabTitle = 'Nilai KPI AO';
  $kpiTabSubtitle = 'Hitung nilai bulanan satu AO sesuai jabatan, kantor, dan closing.';
} elseif ($tab === 'generate') {
  $kpiTabTitle = 'Generate KPI AO';
  $kpiTabSubtitle = 'Generate penilaian KPI secara massal per kantor dan periode closing.';
} else {
  $kpiTabTitle = 'Setting KPI Jabatan';
  $kpiTabSubtitle = 'Kelola indikator, bobot, parameter skor, dan sumber data KPI bisnis.';
}
if ($tab === 'setting') {
  $kpiFilterFields = [
    ['name'=>'jabatan', 'id'=>'v2KpiJabatan', 'label'=>'Jabatan', 'type'=>'select', 'value'=>'AO_KREDIT', 'options'=>['AO_KREDIT'=>'AO Kredit']],
    ['name'=>'unit', 'id'=>'v2KpiUnit', 'label'=>'Unit indikator', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Semua unit']],
  ];
} elseif ($tab === 'calculate') {
  $kpiFilterFields = [
    ['name'=>'jabatan', 'id'=>'v2KpiJabatan', 'label'=>'Jabatan', 'type'=>'select', 'value'=>'AO_KREDIT', 'options'=>['AO_KREDIT'=>'AO Kredit']],
    ['name'=>'year', 'id'=>'v2KpiYear', 'label'=>'Tahun', 'type'=>'number', 'value'=>date('Y'), 'attrs'=>['min'=>'2020','max'=>'2100','step'=>'1']],
    ['name'=>'closing', 'id'=>'v2KpiClosing', 'label'=>'Closing', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Memuat...']],
    ['name'=>'kantor', 'id'=>'v2KpiKantor', 'label'=>'Kantor', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Pilih kantor dahulu']],
  ];
} elseif ($tab === 'generate') {
  $kpiFilterFields = [
    ['name'=>'jabatan', 'id'=>'v2KpiJabatan', 'label'=>'Jabatan', 'type'=>'select', 'value'=>'AO_KREDIT', 'options'=>['AO_KREDIT'=>'AO Kredit']],
    ['name'=>'year', 'id'=>'v2KpiYear', 'label'=>'Tahun', 'type'=>'number', 'value'=>date('Y'), 'attrs'=>['min'=>'2020','max'=>'2100','step'=>'1']],
    ['name'=>'closing', 'id'=>'v2KpiClosing', 'label'=>'Closing', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Memuat...']],
    ['name'=>'kantor', 'id'=>'v2KpiKantor', 'label'=>'Kantor', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Pilih kantor dahulu']],
    ['name'=>'ao', 'id'=>'v2KpiAo', 'label'=>'AO', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Pilih kantor dahulu']],
  ];
} else {
  $kpiFilterFields = [
    ['name'=>'year', 'id'=>'v2KpiYear', 'label'=>'Tahun', 'type'=>'number', 'value'=>date('Y'), 'attrs'=>['min'=>'2020','max'=>'2100','step'=>'1']],
    ['name'=>'jabatan', 'id'=>'v2KpiJabatan', 'label'=>'Jabatan', 'type'=>'select', 'value'=>'AO_KREDIT', 'options'=>['AO_KREDIT'=>'AO Kredit']],
    ['name'=>'kantor', 'id'=>'v2KpiKantor', 'label'=>'Kantor', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Semua kantor']],
    ['name'=>'closing', 'id'=>'v2KpiClosing', 'label'=>'Closing KPI', 'type'=>'date', 'value'=>''],
  ];
}
?>
<section class="v2-page-heading v2-module-heading v2-kpi-page-heading">
  <div><p class="v2-eyebrow">MONBIS / KPI BISNIS</p><h1><?= v2_e($kpiTabTitle) ?></h1><p><?= v2_e($kpiTabSubtitle) ?></p></div>
  <div class="v2-page-status"><?= v2_badge('API KPI aktif', 'success') ?></div>
</section>

<?= v2_filter_drawer($kpiFilterFields, 'v2KpiFilters') ?>

<section class="v2-card v2-module-shell<?= $tab === 'setting' ? ' v2-kpi-setting-shell' : '' ?>">
  <div class="v2-card-heading v2-module-toolbar v2-kpi-card-heading">
    <div><h2 id="v2KpiSettingTitle"><?= v2_e($kpiTabTitle) ?></h2><?php if ($tab !== 'setting'): ?><p>Backend KPI Monbis dipakai bersama; tampilan dan interaksi sudah dirakit ulang dengan component V2.</p><?php endif; ?></div>
<?php if ($tab === 'setting'): ?>
    <div class="v2-kpi-setting-actions"><?= v2_collapsible_search('v2KpiSettingSearch', 'Cari indikator...') ?><button type="button" class="v2-button v2-button--soft v2-kpi-tukin-toggle" id="v2KpiTukinToggle" title="Tampilkan klasifikasi faktor Tukin" aria-label="Tampilkan klasifikasi faktor Tukin" aria-controls="v2KpiSettingTukinPanel" aria-expanded="false"><?= v2_icon('swap', 16) ?><span class="v2-visually-hidden">Klasifikasi Tukin</span></button><button type="button" class="v2-button v2-button--success v2-kpi-save-button" id="v2KpiSave" title="Simpan semua perubahan" aria-label="Simpan semua perubahan"><?= v2_icon('save', 16) ?><span class="v2-visually-hidden">Simpan</span></button></div>
<?php elseif ($tab === 'calculate'): ?>
    <div class="v2-kpi-calculate-actions"><select class="v2-kpi-ao-inline" id="v2KpiAo" data-v2-filter-field="ao" aria-label="Pilih AO" hidden disabled><option value="">Pilih kantor dahulu</option></select><button type="button" class="v2-button v2-button--success" id="v2KpiRun" title="Hitung AO terpilih" aria-label="Hitung AO terpilih"><?= v2_icon('chart', 16) ?><span>Hitung</span></button></div>
<?php elseif ($tab === 'summary'): ?>
    <div class="v2-kpi-summary-actions"><div class="v2-kpi-rekap-tabs v2-kpi-summary-switch" role="tablist" aria-label="Tampilan rekap KPI"><button type="button" class="v2-button v2-button--soft v2-kpi-summary-switch-button" id="v2KpiSummarySwitch" data-v2-kpi-rekap-switch data-v2-kpi-rekap-tab="breakdown" title="Pilih satu AO untuk membuka breakdown" aria-label="Pilih satu AO untuk membuka breakdown" disabled><?= v2_icon('swap', 16) ?><span class="v2-visually-hidden">Pilih satu AO untuk membuka breakdown</span></button></div><select class="v2-kpi-ao-inline" id="v2KpiAo" data-v2-filter-field="ao" aria-label="Filter AO" hidden disabled><option value="">Pilih kantor dahulu</option></select></div>
<?php endif; ?>
  </div>
  <div class="v2-card-body v2-module-body">
<?php if ($tab === 'summary'): ?>
    <div class="v2-grid v2-grid--4 v2-module-stats">
      <article class="v2-stat"><span class="v2-stat-label">AO DITAMPILKAN</span><strong id="v2KpiCount">-</strong><small id="v2KpiCountMeta">Memuat data...</small></article>
      <article class="v2-stat"><span class="v2-stat-label">PERIODE TERISI</span><strong id="v2KpiPeriod">-</strong><small>Periode yang sudah digenerate</small></article>
      <article class="v2-stat"><span class="v2-stat-label">RATA-RATA NILAI</span><strong id="v2KpiAverage">-</strong><small>Nilai berbobot / 100</small></article>
      <article class="v2-stat"><span class="v2-stat-label">MODE REKAP</span><strong id="v2KpiMode">-</strong><small id="v2KpiModeMeta">Filter kantor</small></article>
    </div>
    <div class="v2-table-wrap v2-module-table-wrap" id="v2KpiSummaryWrap"><table class="v2-table v2-kpi-summary-table"><thead><tr><th>KANTOR</th><th>AO / ID PEG</th><?php foreach (['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $month): ?><th><?= $month ?></th><?php endforeach; ?><th>RATA-RATA</th><th>SKOR</th><th>TUKIN</th><th>BULAN</th></tr></thead><tbody id="v2KpiSummaryBody"><tr><td colspan="18" class="v2-empty">Memuat rekap KPI...</td></tr></tbody></table></div>
    <div class="v2-kpi-rekap-panel" id="v2KpiBreakdownPanel" hidden><div class="v2-table-wrap v2-module-table-wrap"><table class="v2-table v2-kpi-breakdown-table"><thead><tr><th>KANTOR</th><th>AO / ID PEG</th><th>INDIKATOR</th><th>TARGET</th><th>REALISASI</th><th>INDEKS</th><th>SKOR</th><th>BOBOT</th><th>NILAI</th><th>INPUT PA</th></tr></thead><tbody id="v2KpiBreakdownBody"><tr><td colspan="10" class="v2-empty">Memuat breakdown indikator...</td></tr></tbody></table></div></div>
<?php elseif ($tab === 'calculate'): ?>
    <div class="v2-grid v2-grid--4 v2-module-stats v2-kpi-calculate-stats">
      <article class="v2-stat"><span class="v2-stat-label">JABATAN</span><strong id="v2KpiJobName">-</strong><small id="v2KpiJobMeta">Pilih jabatan</small></article>
      <article class="v2-stat"><span class="v2-stat-label">AO TERPILIH</span><strong id="v2KpiAoName">-</strong><small id="v2KpiAoMeta">Pilih kantor dan AO</small></article>
      <article class="v2-stat"><span class="v2-stat-label">NILAI AKHIR</span><strong id="v2KpiFinal">-</strong><small>Kontribusi indikator aktif</small></article>
      <article class="v2-stat"><span class="v2-stat-label">STATUS</span><strong id="v2KpiStatus">BELUM DIHITUNG</strong><small id="v2KpiNote">Pilih jabatan, kantor, AO, dan closing</small></article>
    </div>
    <div class="v2-module-progress" id="v2KpiRunStatus" hidden></div>
    <div class="v2-table-wrap v2-module-table-wrap v2-kpi-evaluation-wrap"><table class="v2-table v2-kpi-evaluation-table"><thead><tr><th>INDIKATOR</th><th>TARGET</th><th>REALISASI</th><th>INDEKS</th><th>SKOR</th><th>SKOR 2</th><th>BOBOT</th><th>NILAI</th><th>NILAI 2</th><th>TUKIN</th><th>KETERANGAN</th></tr></thead><tbody id="v2KpiEvaluationBody"><tr><td colspan="11" class="v2-empty">Pilih jabatan, kantor, AO, dan closing, lalu klik Hitung.</td></tr></tbody></table></div>
<?php elseif ($tab === 'generate'): ?>
    <div class="v2-module-callout"><div><strong>Generate KPI per kantor</strong><p>Pilih jabatan dan kantor. Semua AO pada kantor tersebut akan diproses untuk closing yang tersedia.</p></div><button type="button" class="v2-button v2-button--primary" id="v2KpiGenerate"><?= v2_icon('zap', 16) ?><span>Generate</span></button></div>
    <div class="v2-module-progress" id="v2KpiGenerateStatus" hidden></div>
    <div class="v2-table-wrap v2-module-table-wrap"><table class="v2-table v2-kpi-generated-table"><thead><tr><th>AO</th><th>KANTOR</th><th>CLOSING</th><th>STATUS</th><th>NILAI</th></tr></thead><tbody id="v2KpiBulkBody"><tr><td colspan="5" class="v2-empty">Pilih kantor lalu klik Generate.</td></tr></tbody></table></div>
<?php else: ?>
    <div id="v2KpiIndicatorPanel">
    <div class="v2-table-wrap v2-module-table-wrap v2-kpi-setting-table-wrap"><table class="v2-table v2-kpi-setting-table" id="v2KpiSettingTable"><thead><tr><th>INDIKATOR</th><th>KELOMPOK</th><th>BOBOT</th><th data-score-column="0">0</th><th data-score-column="1">1</th><th data-score-column="2">2</th><th data-score-column="3">3</th><th data-score-column="4">4</th><th data-score-column="5">5</th><th>TARGET DEFAULT</th><th>ARAH</th><th>UNIT</th><th>INPUT PA</th><th>STATUS</th></tr></thead><tbody id="v2KpiSettingBody"><tr><td colspan="14" class="v2-empty">Memuat setting KPI...</td></tr></tbody></table></div>
    </div>
    <section class="v2-kpi-setting-tukin-panel" id="v2KpiSettingTukinPanel" hidden><div class="v2-kpi-setting-tukin-heading"><h3>Klasifikasi Faktor Tukin</h3></div><div class="v2-table-wrap v2-module-table-wrap"><table class="v2-table v2-kpi-tukin-table"><thead><tr><th>SKOR KPI FINAL</th><th>NILAI KPI</th><th>FAKTOR TUKIN</th></tr></thead><tbody id="v2KpiSettingTukinBody"><tr><td colspan="3" class="v2-empty">Memuat parameter Tukin...</td></tr></tbody></table></div></section>
<?php endif; ?>
  </div>
</section>

<?= v2_modal('v2KpiConfirmModal', 'Konfirmasi proses KPI', '<div class="v2-kpi-confirm-copy"><strong id="v2KpiConfirmTitle">Konfirmasi proses</strong><p id="v2KpiConfirmMessage">Proses KPI akan dijalankan.</p></div><div class="v2-kpi-confirm-actions"><button type="button" class="v2-button v2-button--soft" id="v2KpiConfirmCancel">Batal</button><button type="button" class="v2-button v2-button--soft" id="v2KpiConfirmPending">Yang belum ada</button><button type="button" class="v2-button v2-button--primary" id="v2KpiConfirmAll">Generate ulang</button></div>') ?>

<script>
(() => {
  const TAB = <?= json_encode($tab) ?>;
  const API = <?= json_encode($apiBase . '/index.php?request=kpi') ?>;
  const scoreIcon = <?= json_encode(v2_icon('check', 12)) ?>;
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const state = { directory: null, setting: null };
  const el = (id) => document.getElementById(id);
  const field = (name) => document.querySelector(`[data-v2-filter-field="${name}"]`);
  const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const num = (value) => Number(value || 0);
  const fmt = (value, digits = 2) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: digits }).format(num(value));
  const pct = (value) => `${fmt(value, 2)}%`;
  const post = async (body) => {
    const response = await fetch(API, { method: 'POST', credentials: 'include', headers: {'Content-Type':'application/json'}, body: JSON.stringify(body) });
    const json = await response.json().catch(() => ({}));
    if (!response.ok || Number(json.status) !== 200) throw new Error(json.message || `Request KPI gagal (${response.status})`);
    return json.data || {};
  };
  const filters = () => ({ year: field('year')?.value || new Date().getFullYear(), jabatan_kode: field('jabatan')?.value || 'AO_KREDIT', kode_kantor: field('kantor')?.value || '', kode_ao: field('ao')?.value || '', closing_date: field('closing')?.value || '' });
  const showError = (id, message, colspan) => { const node = el(id); if (node) node.innerHTML = `<tr><td colspan="${colspan}" class="v2-empty v2-negative">${esc(message)}</td></tr>`; };
  const toast = (message) => window.V2Toast?.show(message);
  const resetCalculateSummary = () => {
    if (TAB !== 'calculate') return;
    const values = {v2KpiJobName:'-', v2KpiAoName:'-', v2KpiFinal:'-', v2KpiStatus:'BELUM DIHITUNG', v2KpiJobMeta:'Pilih jabatan', v2KpiAoMeta:'Pilih kantor dan AO', v2KpiNote:'Pilih jabatan, kantor, AO, dan closing'};
    Object.entries(values).forEach(([id, value]) => { if (el(id)) el(id).textContent = value; });
    const body = el('v2KpiEvaluationBody');
    if (body) body.innerHTML = '<tr><td colspan="11" class="v2-empty">Pilih jabatan, kantor, AO, dan closing, lalu klik Hitung.</td></tr>';
  };

  function fillDirectory(data) {
    state.directory = data;
    window.__v2KpiDirectory = data;
    window.__v2KpiApi = API;
    const requiresOffice = ['summary', 'calculate', 'generate'].includes(TAB);
    if (requiresOffice && !field('kantor')?.value) state.directory.ao = [];
    const jabatan = el('v2KpiJabatan');
    const currentJob = data.jabatan_terpilih?.kode || jabatan?.value || 'AO_KREDIT';
    if (jabatan) { jabatan.innerHTML = (data.jabatan || []).map(item => `<option value="${esc(item.kode)}">${esc(item.nama)}</option>`).join(''); jabatan.value = currentJob; }
    const kantor = el('v2KpiKantor');
    if (kantor) kantor.innerHTML = `<option value="">${requiresOffice ? 'Pilih kantor dahulu' : 'Semua kantor'}</option>` + (data.kantor || []).map(item => `<option value="${esc(item.kode_kantor)}">${esc(item.kode_kantor)} · ${esc(item.nama_kantor)}</option>`).join('');
    if (kantor && TAB === 'summary' && kantor.options[0]) kantor.options[0].textContent = 'Semua kantor';
    fillAo();
    const closing = el('v2KpiClosing');
    if (closing) {
      const dates = data.closing_dates || [];
      const selected = dates.includes(closing.value) ? closing.value : (dates[dates.length - 1] || '');
      if (closing.tagName === 'SELECT') closing.innerHTML = '<option value="">Pilih closing</option>' + dates.map(item => `<option value="${esc(item)}">${esc(item)}</option>`).join('');
      closing.value = selected;
    }
    if (TAB === 'calculate') resetCalculateSummary();
  }
  function fillAo() {
    const select = el('v2KpiAo'); if (!select) return;
    const branch = field('kantor')?.value || '';
    const current = select.value;
    const requiresOffice = ['summary', 'calculate', 'generate'].includes(TAB);
    const rows = (state.directory?.ao || []).filter(item => !branch || String(item.kode_kantor) === String(branch));
    select.innerHTML = `<option value="">${requiresOffice ? (branch ? 'Pilih AO' : 'Pilih kantor dahulu') : 'Semua AO'}</option>` + rows.map(item => `<option value="${esc(item.kode_ao)}" data-id-peg="${esc(item.id_peg || '')}" data-kantor="${esc(item.kode_kantor || '')}">${esc(item.nama_ao)} · ${esc(item.kode_ao)}</option>`).join('');
    select.disabled = requiresOffice && !branch;
    const hidden = requiresOffice && !branch;
    select.hidden = hidden;
    const fieldWrapper = select.closest('.v2-field');
    if (fieldWrapper) fieldWrapper.hidden = hidden;
    if (rows.some(item => String(item.kode_ao) === current)) select.value = current;
  }
  function renderSummary(data) {
    window.__v2KpiAnnual = data;
    window.dispatchEvent(new CustomEvent('v2:kpi-annual', {detail:data}));
    const rows = data.ao || [], values = rows.filter(row => row.nilai_akhir !== null).map(row => num(row.nilai_akhir));
    const monthsFilled = (data.months || []).filter(item => num(item.terisi) > 0);
    el('v2KpiCount').textContent = fmt(rows.length, 0);
    el('v2KpiCountMeta').textContent = data.is_konsolidasi && num(data.total_ao) > rows.length ? `Top ${rows.length} dari ${fmt(data.total_ao, 0)} AO` : (rows.length ? 'AO dengan penilaian' : 'Belum ada penilaian');
    el('v2KpiPeriod').textContent = monthsFilled.length ? `${months[monthsFilled[0].bulan - 1]} - ${months[monthsFilled[monthsFilled.length - 1].bulan - 1]}` : '-';
    el('v2KpiAverage').textContent = values.length ? `${fmt(values.reduce((a, b) => a + b, 0) / values.length)} / 100` : '-';
    const branch = field('kantor')?.value || ''; el('v2KpiMode').textContent = branch ? (el('v2KpiKantor')?.selectedOptions[0]?.textContent || branch) : 'Konsolidasi';
    const moneyCell = (item, month) => { const value = item.monthly?.[month]; return value ? `<td class="v2-num"><strong>${fmt(value.nilai_akhir)}</strong><small>${pct(value.tukin_persen)} tukin</small></td>` : '<td class="v2-num v2-muted-cell">-</td>'; };
    el('v2KpiSummaryBody').innerHTML = rows.length ? rows.map(item => `<tr><td>${esc(item.kode_kantor || '-')}</td><td><strong>${esc(item.nama_ao || '-')}</strong><small>${esc(item.id_peg || item.kode_ao || '-')}</small></td>${months.map((_, index) => moneyCell(item, index + 1)).join('')}<td class="v2-num"><strong>${item.nilai_akhir === null ? '-' : fmt(item.nilai_akhir)}</strong></td><td class="v2-num">${item.skor_final === null ? '-' : fmt(item.skor_final)} / 5</td><td class="v2-num v2-positive">${item.tukin_persen === null ? '-' : pct(item.tukin_persen)}</td><td class="v2-num">${fmt(item.bulan_terisi, 0)} / 12</td></tr>`).join('') : '<tr><td colspan="18" class="v2-empty">Belum ada penilaian KPI pada filter ini.</td></tr>';
  }
  function renderGenerated(data) {
    const rows = data.generated || [];
    el('v2KpiGeneratedBody').innerHTML = rows.length ? rows.map(item => `<tr><td>${esc(item.nama_ao || item.kode_ao || '-')}<small>${esc(item.id_peg || '-')}</small></td><td>${esc(item.kode_kantor || '-')}</td><td>${esc(item.closing_date || '-')}</td><td>${esc(item.status || '-')}</td><td class="v2-num">${fmt(item.nilai_akhir)}</td><td>${esc(item.keterangan || '-')}</td></tr>`).join('') : '<tr><td colspan="6" class="v2-empty">Belum ada periode KPI yang digenerate.</td></tr>';
  }
  const settingIndicators = () => {
    const job = field('jabatan')?.value || 'AO_KREDIT';
    const unit = field('unit')?.value || '';
    const query = (el('v2KpiSettingSearch')?.value || '').trim().toLowerCase();
    return (state.setting?.indikator || []).filter(item => {
      const haystack = `${item.nama || ''} ${item.kelompok || ''} ${item.definisi || ''}`.toLowerCase();
      return item.jabatan_kode === job && item.status !== 'NONAKTIF' && (!unit || item.unit === unit) && (!query || haystack.includes(query));
    });
  };
  const isScoreCount = (unit) => ['NOA', 'JUMLAH'].includes(String(unit || '').toUpperCase());
  const scoreValue = (value, unit, blankInfinity = false) => {
    const valueNumber = num(value);
    if (blankInfinity && valueNumber >= 999) return '';
    return isScoreCount(unit) ? fmt(valueNumber) : `${fmt(valueNumber * 100)}%`;
  };
  const parseInputNumber = (value) => {
    let raw = String(value ?? '').trim().replace(/\s/g, '').replace(/%$/, '');
    if (!raw) return 0;
    if (raw.includes(',')) raw = raw.replace(/\./g, '').replace(',', '.');
    else if ((raw.match(/\./g) || []).length > 1 || /\.\d{3}$/.test(raw)) raw = raw.replace(/\./g, '');
    const result = Number(raw);
    return Number.isFinite(result) ? result : 0;
  };
  const parseScore = (value, unit) => {
    const raw = String(value ?? '').trim();
    if (!raw) return 999;
    if (raw.endsWith('%')) return parseInputNumber(raw) / 100;
    return isScoreCount(unit) ? parseInputNumber(raw) : parseInputNumber(raw) / 100;
  };
  const renderScoreCell = (item, score, unit) => {
    if (!item) return '<span class="v2-muted-cell">-</span>';
    return `<div class="v2-score-editor" data-score-id="${num(item.id)}" data-score-unit="${esc(unit || '')}" data-score-predikat="${esc(item.predikat || '')}"><div class="v2-score-range"><input data-score-field="min" value="${esc(scoreValue(item.min_indeks, unit))}" inputmode="decimal" aria-label="Indeks minimum skor ${score}"><span>–</span><input data-score-field="max" value="${esc(scoreValue(item.max_indeks, unit, true))}" placeholder="∞" inputmode="decimal" aria-label="Indeks maksimum skor ${score}"></div><button type="button" class="v2-score-save" data-v2-score-save title="Simpan range skor ${score}" aria-label="Simpan range skor ${score}">${scoreIcon}</button></div>`;
  };
  function renderScores() {
    const body = el('v2KpiScoreBody');
    if (!body) return;
    const job = field('jabatan')?.value || 'AO_KREDIT';
    const ranges = (state.setting?.parameter_skor || []).filter(item => item.jabatan_kode === job && num(item.aktif));
    const rows = settingIndicators();
    body.innerHTML = rows.length ? rows.map(item => `<tr><td><strong>${esc(item.nama)}</strong><small>${esc(item.kelompok || '-')} · ${esc(item.unit || '-')}</small></td><td class="v2-num">${pct(num(item.bobot) * 100)}</td>${[0,1,2,3,4,5].map(score => { const range = ranges.find(candidate => Number(candidate.indikator_id) === Number(item.id) && Number(candidate.skor) === score); return `<td>${renderScoreCell(range, score, item.unit)}</td>`; }).join('')}</tr>`).join('') : '<tr><td colspan="8" class="v2-empty">Parameter skor belum tersedia untuk filter ini.</td></tr>';
  }
  const renderInlineScoreCell = (item, score, unit) => {
    if (!item) return '<span class="v2-muted-cell">-</span>';
    return `<div class="v2-score-editor" data-score-id="${num(item.id)}" data-score-unit="${esc(unit || '')}" data-score-predikat="${esc(item.predikat || '')}"><div class="v2-score-range"><input data-score-field="min" value="${esc(scoreValue(item.min_indeks, unit))}" inputmode="decimal" aria-label="Indeks minimum skor ${score}"><span>–</span><input data-score-field="max" value="${esc(scoreValue(item.max_indeks, unit, true))}" placeholder="∞" inputmode="decimal" aria-label="Indeks maksimum skor ${score}"></div></div>`;
  };
  function renderSetting() {
    const job = field('jabatan')?.value || 'AO_KREDIT';
    const ranges = (state.setting?.parameter_skor || []).filter(item => item.jabatan_kode === job && num(item.aktif));
    const rows = settingIndicators();
    el('v2KpiSettingBody').innerHTML = rows.length ? rows.map(item => `<tr data-kpi-id="${num(item.id)}" data-unit="${esc(item.unit || '')}"><td><strong>${esc(item.nama)}</strong><small>${esc(item.definisi || '-')}</small></td><td>${esc(item.kelompok || '-')}</td><td><input class="v2-inline-input" data-kpi-field="bobot" value="${fmt(num(item.bobot) * 100)}%" inputmode="decimal"></td>${[0,1,2,3,4,5].map(score => { const range = ranges.find(candidate => Number(candidate.indikator_id) === Number(item.id) && Number(candidate.skor) === score); return `<td data-score-column="${score}">${renderInlineScoreCell(range, score, item.unit)}</td>`; }).join('')}<td><input class="v2-inline-input" data-kpi-field="target" value="${fmt(item.target_default)}" inputmode="decimal"></td><td>${esc(item.arah || '-')}</td><td>${esc(item.unit || '-')}</td><td><small>${esc(item.input_pa || '-')}</small></td><td><select class="v2-inline-input" data-kpi-field="status"><option value="AKTIF"${item.status === 'AKTIF' ? ' selected' : ''}>AKTIF</option><option value="PILOT"${item.status === 'PILOT' ? ' selected' : ''}>PILOT</option></select></td></tr>`).join('') : '<tr><td colspan="14" class="v2-empty">Tidak ada indikator aktif untuk filter ini.</td></tr>';
  }
  function renderSettingTukin() {
    const body = el('v2KpiSettingTukinBody');
    if (!body) return;
    const rules = state.setting?.tukin_rules || [];
    body.innerHTML = rules.length ? rules.map(item => `<tr><td>${esc(item.label || '-')}</td><td>${item.min_nilai == null ? '-' : fmt(item.min_nilai)} – ${item.max_nilai == null ? '100' : fmt(item.max_nilai)}</td><td>${fmt(item.faktor_persen)}%</td></tr>`).join('') : '<tr><td colspan="3" class="v2-empty">Parameter Tukin belum tersedia.</td></tr>';
  }
  async function loadSummary() { try { renderSummary(await post({type:'annual', ...filters()})); } catch (error) { showError('v2KpiSummaryBody', error.message, 18); } }
  async function loadDirectory() { try { fillDirectory(await post({type:'directory', year:field('year')?.value || new Date().getFullYear(), jabatan_kode:field('jabatan')?.value || 'AO_KREDIT', include_all_ao:TAB === 'summary', include_generated:true})); if (TAB === 'summary') await loadSummary(); } catch (error) { const target = TAB === 'summary' ? 'v2KpiSummaryBody' : (TAB === 'calculate' ? 'v2KpiEvaluationBody' : 'v2KpiSettingBody'); showError(target, error.message, TAB === 'summary' ? 18 : TAB === 'calculate' ? 11 : 14); } }
  async function loadSetting() { try { state.setting = await post({type:'setting'}); const job = el('v2KpiJabatan'); if (job) { const currentJob = job.value || 'AO_KREDIT'; job.innerHTML = (state.setting.jabatan || []).map(item => `<option value="${esc(item.kode)}">${esc(item.nama)}</option>`).join(''); job.value = (state.setting.jabatan || []).some(item => item.kode === currentJob) ? currentJob : (state.setting.jabatan?.[0]?.kode || ''); } const unit = el('v2KpiUnit'); if (unit) { const currentUnit = unit.value || ''; const units = [...new Set((state.setting.indikator || []).map(item => item.unit).filter(Boolean))].sort(); unit.innerHTML = '<option value="">Semua unit</option>' + units.map(item => `<option value="${esc(item)}">${esc(item)}</option>`).join(''); unit.value = units.includes(currentUnit) ? currentUnit : ''; } renderSetting(); renderSettingTukin(); } catch (error) { showError('v2KpiSettingBody', error.message, 14); } }
  el('v2KpiKantor')?.addEventListener('change', async () => {
    if (['summary', 'calculate', 'generate'].includes(TAB)) {
      const branch = field('kantor')?.value || '';
      if (!branch) {
        state.directory.ao = [];
        fillAo();
        if (TAB === 'calculate') resetCalculateSummary();
        if (TAB === 'summary') loadSummary();
        return;
      }
      try {
        const result = await post({type:'bootstrap', year:field('year')?.value || new Date().getFullYear(), jabatan_kode:field('jabatan')?.value || 'AO_KREDIT', kode_kantor:branch});
        state.directory = {...state.directory, ...result, ao:result.ao || []};
        window.__v2KpiDirectory = state.directory;
        fillAo();
        if (TAB === 'calculate') resetCalculateSummary();
        if (TAB === 'summary') loadSummary();
      } catch (error) { toast(error.message); }
    } else {
      fillAo();
      if (TAB === 'summary') loadSummary();
    }
  });
  el('v2KpiAo')?.addEventListener('change', () => { if (TAB === 'summary') loadSummary(); });
  el('v2KpiYear')?.addEventListener('change', loadDirectory);
  el('v2KpiJabatan')?.addEventListener('change', async () => { if (TAB === 'setting') { await loadSetting(); } else await loadDirectory(); });
  el('v2KpiUnit')?.addEventListener('change', renderSetting);
  el('v2KpiSettingSearch')?.addEventListener('input', renderSetting);
  el('v2KpiTukinToggle')?.addEventListener('click', (event) => { const button = event.currentTarget; const panel = el('v2KpiSettingTukinPanel'); const indicatorPanel = el('v2KpiIndicatorPanel'); const title = el('v2KpiSettingTitle'); const search = el('v2KpiSettingSearch')?.closest('.v2-collapsible-search'); const save = el('v2KpiSave'); if (!panel || !indicatorPanel) return; const open = panel.hidden; panel.hidden = !open; indicatorPanel.hidden = open; if (title) title.textContent = open ? 'Klasifikasi Faktor Tukin' : 'Setting KPI Jabatan'; if (search) search.hidden = open; if (save) save.hidden = open; button.setAttribute('aria-expanded', open ? 'true' : 'false'); button.setAttribute('aria-label', open ? 'Kembali ke Setting KPI Jabatan' : 'Tampilkan klasifikasi faktor Tukin'); button.title = open ? 'Kembali ke Setting KPI Jabatan' : 'Tampilkan klasifikasi faktor Tukin'; button.classList.toggle('is-active', open); });
  el('v2KpiSave')?.addEventListener('click', async () => { const rows = [...document.querySelectorAll('#v2KpiSettingBody tr[data-kpi-id]')]; if (!rows.length) return; const button = el('v2KpiSave'); button.disabled = true; try { await Promise.all(rows.map(row => post({type:'save_indicator', id:Number(row.dataset.kpiId), bobot:num(row.querySelector('[data-kpi-field="bobot"]')?.value) / 100, target:num(row.querySelector('[data-kpi-field="target"]')?.value), status:row.querySelector('[data-kpi-field="status"]')?.value || 'AKTIF'}))); toast('Setting KPI berhasil disimpan.'); await loadSetting(); } catch (error) { toast(error.message); } finally { button.disabled = false; } });
  document.addEventListener('click', async (event) => { const button = event.target.closest('[data-v2-score-save]'); if (!button) return; const editor = button.closest('[data-score-id]'); const minInput = editor?.querySelector('[data-score-field="min"]'); const maxInput = editor?.querySelector('[data-score-field="max"]'); const unit = editor?.dataset.scoreUnit || ''; const min = parseScore(minInput?.value, unit); const max = parseScore(maxInput?.value, unit); if (!editor || max < min) return toast('Indeks maksimum tidak boleh lebih kecil dari minimum.'); button.disabled = true; try { await post({type:'save_score', id:Number(editor.dataset.scoreId), min_indeks:min, max_indeks:max, predikat:editor.dataset.scorePredikat || '', aktif:1}); const saved = (state.setting?.parameter_skor || []).find(item => Number(item.id) === Number(editor.dataset.scoreId)); if (saved) { saved.min_indeks = min; saved.max_indeks = max; } toast('Parameter skor berhasil disimpan.'); renderScores(); } catch (error) { toast(error.message); } finally { button.disabled = false; } });
  el('v2KpiSave')?.addEventListener('click', async (event) => { event.preventDefault(); event.stopImmediatePropagation(); const rows = [...document.querySelectorAll('#v2KpiSettingBody tr[data-kpi-id]')]; if (!rows.length) return; const button = el('v2KpiSave'); const scores = rows.flatMap(row => [...row.querySelectorAll('.v2-score-editor')].map(editor => { const unit = editor.dataset.scoreUnit || ''; return {type:'save_score', id:Number(editor.dataset.scoreId), min_indeks:parseScore(editor.querySelector('[data-score-field="min"]')?.value, unit), max_indeks:parseScore(editor.querySelector('[data-score-field="max"]')?.value, unit), predikat:editor.dataset.scorePredikat || '', aktif:1}; })); if (scores.some(item => item.max_indeks < item.min_indeks)) return toast('Indeks maksimum tidak boleh lebih kecil dari minimum.'); button.disabled = true; try { await Promise.all(rows.map(row => post({type:'save_indicator', id:Number(row.dataset.kpiId), bobot:parseInputNumber(row.querySelector('[data-kpi-field="bobot"]')?.value) / 100, target:parseInputNumber(row.querySelector('[data-kpi-field="target"]')?.value), status:row.querySelector('[data-kpi-field="status"]')?.value || 'AKTIF'}))); await Promise.all(scores.map(item => post(item))); toast('Setting KPI dan parameter skor berhasil disimpan.'); await loadSetting(); } catch (error) { toast(error.message); } finally { button.disabled = false; } }, true);
  if (TAB === 'setting') loadSetting(); else loadDirectory();
})();
</script>
<script>
(() => {
  if (<?= json_encode($tab) ?> !== 'summary') return;
  const el = (id) => document.getElementById(id);
  const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const num = (value) => Number(value || 0);
  const fmt = (value) => new Intl.NumberFormat('id-ID', {maximumFractionDigits: 2}).format(num(value));
  const money = (value) => `Rp ${new Intl.NumberFormat('id-ID', {maximumFractionDigits: 0}).format(num(value))}`;
  const pct = (value) => `${fmt(num(value) * 100)}%`;
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const value = (row, key) => row[key] === null || row[key] === undefined ? '-' : row.unit === 'RUPIAH' ? money(row[key]) : row.unit === 'PERSEN' || row.formula_key === 'REALISASI_KREDIT' ? pct(row[key]) : fmt(row[key]);
  function renderBreakdown(data, body) {
    const rows = data?.indicator_breakdown || [];
    if (!rows.length) { body.innerHTML = '<tr><td colspan="10" class="v2-empty">Belum ada breakdown indikator pada filter ini.</td></tr>'; return; }
    const groups = new Map();
    rows.forEach((row) => { const key = String(num(row.bulan) || 0); if (!groups.has(key)) groups.set(key, []); groups.get(key).push(row); });
    const detailRow = (row) => `<tr><td>${esc(row.kode_kantor || '-')}</td><td><strong>${esc(row.nama_ao || '-')}</strong><small>${esc(row.id_peg || '-')}</small></td><td><strong>${esc(row.nama || '-')}</strong><small>${esc(row.kelompok || '-')}</small></td><td>${value(row, 'target')}</td><td>${value(row, 'realisasi')}</td><td>${row.unit === 'PERSEN' || row.formula_key === 'REALISASI_KREDIT' ? pct(row.indeks) : fmt(row.indeks)}</td><td>${fmt(row.skor)} / 5</td><td>${pct(row.bobot)}</td><td>${fmt(row.nilai_100)}</td><td>${esc(row.input_pa || '-')}</td></tr>`;
    const html = [...groups.entries()].sort((a, b) => num(a[0]) - num(b[0])).flatMap(([, monthRows]) => {
      const first = monthRows[0] || {};
      const month = months[(num(first.bulan) || 1) - 1] || first.bulan || '-';
      const year = String(first.closing_date || '').slice(0, 4);
      const people = new Map();
      monthRows.forEach((row) => { const key = String(row.id_peg || row.kode_ao || row.nama_ao || 'ao'); if (!people.has(key)) people.set(key, {final:num(row.nilai_akhir_bulan), tukin:row.tukin_persen_bulan == null ? null : num(row.tukin_persen_bulan)}); });
      const personRows = [...people.values()];
      const averageFinal = personRows.length ? personRows.reduce((sum, row) => sum + row.final, 0) / personRows.length : 0;
      const tukinRows = personRows.filter((row) => row.tukin !== null);
      const averageTukin = tukinRows.length ? tukinRows.reduce((sum, row) => sum + row.tukin, 0) / tukinRows.length : null;
      const totalValue = monthRows.reduce((sum, row) => sum + num(row.nilai_100), 0);
      return [`<tr class="v2-kpi-breakdown-month-row"><td colspan="10"><strong>${esc(month)} ${esc(year)}</strong><small>Breakdown indikator</small></td></tr>`, ...monthRows.map(detailRow), `<tr class="v2-kpi-breakdown-total"><td colspan="3"><strong>Total ${esc(month)}</strong><small>${people.size} AO · ${monthRows.length} indikator</small></td><td colspan="2"><small>NILAI AKHIR</small><strong>${fmt(averageFinal)}</strong></td><td><small>TUKIN</small><strong>${averageTukin === null ? '-' : `${fmt(averageTukin)}%`}</strong></td><td colspan="2"></td><td><strong>${fmt(totalValue)}</strong></td><td>Total nilai bulan</td></tr>`];
    });
    body.innerHTML = html.join('');
  }
  function render(data) {
    const breakdown = data?.indicator_breakdown || [];
    const breakdownBody = el('v2KpiBreakdownBody');
    const breakdownPanel = el('v2KpiBreakdownPanel');
    if (breakdownPanel) breakdownPanel.classList.toggle('v2-kpi-breakdown-single', Boolean(el('v2KpiAo')?.value));
    if (breakdownBody) { renderBreakdown(data, breakdownBody); return; }
    if (breakdownBody) breakdownBody.innerHTML = breakdown.length ? breakdown.map((row) => `<tr><td>${esc(row.kode_kantor || '-')}</td><td><strong>${esc(row.nama_ao || '-')}</strong><small>${esc(row.id_peg || '-')}</small></td><td><strong>${esc(row.nama || '-')}</strong><small>${esc(row.kelompok || '-')}</small></td><td>${value(row, 'target')}</td><td>${value(row, 'realisasi')}</td><td>${row.unit === 'PERSEN' || row.formula_key === 'REALISASI_KREDIT' ? pct(row.indeks) : fmt(row.indeks)}</td><td>${fmt(row.skor)} / 5</td><td>${pct(row.bobot)}</td><td>${fmt(row.nilai_100)}</td><td>${esc(row.input_pa || '-')}</td></tr>`).join('') : '<tr><td colspan="10" class="v2-empty">Belum ada breakdown indikator pada filter ini.</td></tr>';
    const tukinBody = el('v2KpiTukinBody');
    const rules = data?.tukin_rules || [];
    if (tukinBody) tukinBody.innerHTML = rules.length ? rules.map((row) => `<tr><td>${esc(row.label || '-')}</td><td>${row.min_nilai == null ? '-' : fmt(row.min_nilai)} – ${row.max_nilai == null ? '100' : fmt(row.max_nilai)}</td><td>${fmt(row.faktor_persen)}%</td></tr>`).join('') : '<tr><td colspan="3" class="v2-empty">Parameter tukin belum tersedia.</td></tr>';
  }
  function switchTab(name) {
    const switchButton = el('v2KpiSummarySwitch');
    if (switchButton) {
      const nextName = name === 'summary' ? 'breakdown' : 'summary';
      const nextLabel = nextName === 'breakdown' ? 'Buka Breakdown indikator' : 'Kembali ke Ringkasan';
      switchButton.dataset.v2KpiRekapTab = nextName;
      switchButton.title = nextLabel;
      switchButton.setAttribute('aria-label', nextLabel);
      const hiddenLabel = switchButton.querySelector('.v2-visually-hidden');
      if (hiddenLabel) hiddenLabel.textContent = nextLabel;
    }
    document.querySelectorAll('[data-v2-kpi-rekap-tab]').forEach((button) => button.classList.toggle('is-active', !button.hasAttribute('data-v2-kpi-rekap-switch') && button.dataset.v2KpiRekapTab === name));
    const summary = el('v2KpiSummaryWrap'), breakdown = el('v2KpiBreakdownPanel'), tukin = el('v2KpiTukinPanel');
    if (summary) summary.hidden = name !== 'summary';
    if (breakdown) breakdown.hidden = name !== 'breakdown';
    if (tukin) tukin.hidden = name !== 'tukin';
  }
  function syncBreakdownSwitch() {
    const button = el('v2KpiSummarySwitch');
    const aoSelected = Boolean(el('v2KpiAo')?.value);
    const breakdown = el('v2KpiBreakdownPanel');
    if (!button) return;
    if (!aoSelected && breakdown && !breakdown.hidden) switchTab('summary');
    button.disabled = !aoSelected;
    if (!aoSelected) {
      button.title = 'Pilih satu AO untuk membuka breakdown';
      button.setAttribute('aria-label', 'Pilih satu AO untuk membuka breakdown');
    } else {
      const label = button.dataset.v2KpiRekapTab === 'breakdown' ? 'Buka Breakdown indikator' : 'Kembali ke Ringkasan';
      button.title = label;
      button.setAttribute('aria-label', label);
    }
  }
  document.querySelectorAll('[data-v2-kpi-rekap-tab]').forEach((button) => button.addEventListener('click', () => switchTab(button.dataset.v2KpiRekapTab)));
  window.addEventListener('v2:kpi-annual', (event) => render(event.detail));
  window.addEventListener('v2:kpi-annual', syncBreakdownSwitch);
  el('v2KpiAo')?.addEventListener('change', syncBreakdownSwitch);
  if (window.__v2KpiAnnual) render(window.__v2KpiAnnual);
  switchTab('summary');
  syncBreakdownSwitch();
})();
</script>
<script>
(() => {
  const TAB = <?= json_encode($tab) ?>;
  if (!['calculate', 'generate'].includes(TAB)) return;
  const API = <?= json_encode($apiBase . '/index.php?request=kpi') ?>;
  const el = (id) => document.getElementById(id);
  const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const num = (value) => Number(value || 0);
  const fmt = (value) => new Intl.NumberFormat('id-ID', {maximumFractionDigits: 2}).format(num(value));
  const money = (value) => `Rp ${new Intl.NumberFormat('id-ID', {maximumFractionDigits: 0}).format(num(value))}`;
  const pct = (value) => `${fmt(num(value) * 100)}%`;
  const toast = (message) => window.V2Toast?.show(message);
  const post = async (body) => {
    const response = await fetch(API, {method:'POST', credentials:'include', headers:{'Content-Type':'application/json'}, body:JSON.stringify(body)});
    const json = await response.json().catch(() => ({}));
    if (!response.ok || Number(json.status) !== 200) throw new Error(json.message || `Request KPI gagal (${response.status})`);
    return json.data || {};
  };
  const job = () => el('v2KpiJabatan')?.value || 'AO_KREDIT';
  const year = () => el('v2KpiYear')?.value || new Date().getFullYear();
  const directory = () => window.__v2KpiDirectory || {ao:[], generated:[], closing_dates:[]};
  const currentAo = () => directory().ao?.find((item) => String(item.kode_ao) === String(el('v2KpiAo')?.value || ''));
  const showValue = (value, unit) => unit === 'RUPIAH' ? money(value) : unit === 'PERSEN' ? pct(value) : fmt(value);
  const showIndex = (row) => ['PERSEN', 'RUPIAH'].includes(String(row.unit || '').toUpperCase()) ? pct(row.indeks) : fmt(row.indeks);
  const tukinFor = (score) => { const row = (directory().tukin_rules || []).find(item => score >= num(item.min_skor) && (item.max_skor === null || item.max_skor === undefined || score < num(item.max_skor))); return row ? num(row.faktor_persen) : null; };
  const renderCalculateSummary = (evaluation = null) => {
    if (TAB !== 'calculate') return;
    const person = currentAo();
    const selectedJob = (directory().jabatan || []).find(item => item.kode === job()) || directory().jabatan_terpilih || {};
    const saved = findExisting(person, el('v2KpiClosing')?.value || '');
    const active = evaluation || saved;
    const values = {
      v2KpiJobName: selectedJob.nama || job(),
      v2KpiJobMeta: selectedJob.deskripsi || 'Indikator aktif sesuai jabatan',
      v2KpiAoName: person?.nama_ao || '-',
      v2KpiAoMeta: person ? `${person.kode_ao} · Kantor ${person.kode_kantor}` : 'Pilih kantor dan AO',
      v2KpiFinal: active ? fmt(active.nilai_akhir) : '-',
      v2KpiStatus: active ? (active.status || 'SUDAH DIGENERATE') : 'BELUM DIHITUNG',
      v2KpiNote: active ? (active.predikat || 'Nilai tersimpan') : 'Pilih jabatan, kantor, AO, dan closing',
    };
    Object.entries(values).forEach(([id, value]) => { if (el(id)) el(id).textContent = value; });
  };
  const findExisting = (person, closing) => (directory().generated || []).find((item) => {
    const itemIds = [item.id_peg, item.kode_ao].filter(Boolean).map(String);
    const personIds = [person?.id_peg, person?.kode_ao].filter(Boolean).map(String);
    return itemIds.some((id) => personIds.includes(id)) && String(item.closing_date) === String(closing);
  });
  const updateStatus = (message, visible = true) => { const node = el(TAB === 'calculate' ? 'v2KpiRunStatus' : 'v2KpiGenerateStatus'); if (node) { node.hidden = !visible; node.textContent = message; } };

  function modalChoice(title, message, options = {pending:true}) {
    const modal = el('v2KpiConfirmModal');
    const titleNode = el('v2KpiConfirmTitle');
    const messageNode = el('v2KpiConfirmMessage');
    const cancel = el('v2KpiConfirmCancel');
    const pending = el('v2KpiConfirmPending');
    const all = el('v2KpiConfirmAll');
    if (!modal || !titleNode || !messageNode || !cancel || !pending || !all) return Promise.resolve(options.pending ? 'pending' : 'all');
    titleNode.textContent = title;
    messageNode.textContent = message;
    pending.hidden = !options.pending;
    pending.textContent = options.pendingLabel || 'Yang belum ada';
    all.textContent = options.allLabel || 'Generate ulang';
    modal.removeAttribute('hidden');
    return new Promise((resolve) => {
      let settled = false;
      const finish = (value) => {
        if (settled) return;
        settled = true;
        [cancel, pending, all, ...modal.querySelectorAll('[data-v2-modal-close]')].forEach((button) => button.removeEventListener('click', button._v2KpiConfirmHandler));
        document.removeEventListener('keydown', onKey);
        modal.setAttribute('hidden', '');
        resolve(value);
      };
      const bind = (button, value) => { const handler = () => finish(value); button._v2KpiConfirmHandler = handler; button.addEventListener('click', handler); };
      const onKey = (event) => { if (event.key === 'Escape') finish('cancel'); };
      bind(cancel, 'cancel');
      if (options.pending) bind(pending, 'pending');
      bind(all, 'all');
      modal.querySelectorAll('[data-v2-modal-close]').forEach((button) => bind(button, 'cancel'));
      document.addEventListener('keydown', onKey);
    });
  }

  const sourceNote = (row) => {
    const parts = [];
    if (row.catatan) parts.push(String(row.catatan));
    if (row.formula_key === 'REPAYMENT_RATE') parts.push(`OS DPD 0 ${money(row.os_dpd0)} / OS kelolaan ${money(row.os_kelolaan)}`);
    if (row.formula_key === 'MOB_6') parts.push(`OS menunggak MOB 1-6 ${money(row.os_mob_menunggak)} / total OS MOB 1-6 ${money(row.os_mob_total)}`);
    if (row.formula_key === 'EARLY_RUN_OFF') parts.push(`OS pelunasan murni ${money(row.os_run_off)} / OS DPD 0 M-1 ${money(row.os_dpd0_m1)}`);
    if (row.input_pa) parts.push(`Input PA: ${row.input_pa}`);
    parts.push(`Target ${showValue(row.target, row.unit)} / Realisasi ${showValue(row.realisasi, row.unit)} / Indeks ${showIndex(row)} / Skor ${fmt(row.skor)} dari 5`);
    return parts.join(' · ');
  };
  function renderEvaluation(rows) {
    const body = el('v2KpiEvaluationBody');
    if (!body) return;
    if (!rows.length) { body.innerHTML = '<tr><td colspan="11" class="v2-empty">Pilih jabatan, kantor, AO, dan closing, lalu klik Hitung.</td></tr>'; return; }
    const total = rows.reduce((sum, row) => sum + num(row.nilai_tertimbang), 0);
    const total100 = rows.reduce((sum, row) => sum + num(row.nilai_100), 0);
    const totalTukin = tukinFor(total100 / 20);
    const totalNote = `Total nilai ${fmt(total)} dari 5 · Nilai 2 ${fmt(total100)} dari 100 · Skor akhir ${fmt(total100 / 20)} dari 5`;
    const totalRow = `<tr class="v2-total-row"><td><strong>TOTAL</strong></td><td colspan="3"></td><td>${fmt(rows.reduce((sum, row) => sum + num(row.skor), 0))}</td><td>${fmt(rows.reduce((sum, row) => sum + num(row.skor) * 20, 0))}</td><td>${pct(rows.reduce((sum, row) => sum + num(row.bobot), 0))}</td><td><strong>${fmt(total)}</strong></td><td><strong>${fmt(total100)}</strong></td><td>${totalTukin === null ? '-' : `${fmt(totalTukin)}%`}</td><td>${esc(totalNote)}</td></tr>`;
    body.innerHTML = totalRow + rows.map((row) => {
      const note = sourceNote(row);
      return `<tr><td><strong>${esc(row.nama || '-')}</strong><small>${esc(row.kelompok || '-')}</small></td><td>${showValue(row.target, row.unit)}</td><td>${showValue(row.realisasi, row.unit)}</td><td>${showIndex(row)}</td><td>${fmt(row.skor)} / 5</td><td>${fmt(num(row.skor) * 20)}</td><td>${pct(row.bobot)}</td><td><strong>${fmt(row.nilai_tertimbang)}</strong></td><td><strong>${fmt(row.nilai_100)}</strong></td><td>-</td><td title="${esc(note)}">${esc(note)}</td></tr>`;
    }).join('');
  }

  async function loadEvaluation() {
    if (TAB !== 'calculate') return;
    const person = currentAo();
    const closing = el('v2KpiClosing')?.value || '';
    if (!person || !closing) { renderCalculateSummary(null); renderEvaluation([]); return; }
    if (!findExisting(person, closing)) { renderCalculateSummary(null); renderEvaluation([]); return; }
    try {
      const evaluation = await post({type:'evaluation', year:year(), jabatan_kode:job(), kode_ao:person.kode_ao, id_peg:person.id_peg, kode_kantor:person.kode_kantor, closing_date:closing});
      const row = evaluation.data?.[0];
      const detail = row ? await post({type:'detail', penilaian_id:row.id}) : {};
      renderCalculateSummary(row || null);
      renderEvaluation(detail.data || []);
      updateStatus(row ? `Nilai ${fmt(row.nilai_akhir)} · ${row.status || 'TERSIMPAN'}` : 'Belum dihitung.');
    } catch (error) { renderCalculateSummary(null); renderEvaluation([]); updateStatus(error.message); }
  }

  async function runCalculate(event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    const person = currentAo();
    const closing = el('v2KpiClosing')?.value || '';
    if (!person || !closing) return toast('Pilih jabatan, kantor, AO, dan closing terlebih dahulu.');
    const existing = findExisting(person, closing);
    if (existing) {
      const choice = await modalChoice('Penilaian sudah tersedia', `Nilai ${fmt(existing.nilai_akhir)} untuk closing ${existing.closing_date}. Hitung ulang periode ini?`, {pending:false, allLabel:'Hitung ulang'});
      if (choice !== 'all') return;
    }
    const button = el('v2KpiRun');
    button.disabled = true;
    updateStatus('Menghitung KPI...');
    try {
      const result = await post({type:'calculate', year:year(), jabatan_kode:job(), kode_ao:person.kode_ao, id_peg:person.id_peg, kode_kantor:person.kode_kantor, closing_date:closing});
      const saved = result.data?.[0] || {};
      const generated = directory().generated || [];
      const existingIndex = generated.findIndex((item) => {
        const itemIds = [item.id_peg, item.kode_ao].filter(Boolean).map(String);
        const personIds = [person.id_peg, person.kode_ao].filter(Boolean).map(String);
        return itemIds.some((id) => personIds.includes(id)) && String(item.closing_date) === String(closing);
      });
      const generatedRow = {...saved, id_peg:person.id_peg, kode_ao:person.kode_ao, nama_ao:person.nama_ao, kode_kantor:person.kode_kantor, closing_date:closing};
      if (existingIndex >= 0) generated[existingIndex] = generatedRow;
      else generated.push(generatedRow);
      directory().generated = generated;
      await loadEvaluation();
      toast('Penilaian KPI berhasil dihitung.');
    } catch (error) { updateStatus(error.message); toast(error.message); }
    finally { button.disabled = false; }
  }

  function renderBulkRows(rows) {
    const body = el('v2KpiBulkBody');
    if (!body) return;
    body.innerHTML = rows.length ? rows.map((item) => `<tr><td><strong>${esc(item.nama_ao || item.kode_ao || '-')}</strong><small>${esc(item.id_peg || '-')}</small></td><td>${esc(item.kode_kantor || '-')}</td><td>${esc(item.closing_date || '-')}</td><td>${esc(item.status || 'SUDAH DIGENERATE')}</td><td>${fmt(item.nilai_akhir)}</td></tr>`).join('') : '<tr><td colspan="5" class="v2-empty">Belum ada penilaian untuk filter ini.</td></tr>';
  }

  function bulkJobs() {
    const data = directory();
    const branch = el('v2KpiKantor')?.value || '';
    const selected = el('v2KpiAo')?.value || '';
    const people = (data.ao || []).filter((item) => (!branch || String(item.kode_kantor) === String(branch)) && (!selected || String(item.kode_ao) === String(selected)));
    return people.flatMap((person) => (data.closing_dates || []).map((closing) => ({person, closing})));
  }

  async function runBulk(event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    if (!el('v2KpiKantor')?.value) return toast('Pilih kantor terlebih dahulu.');
    const jobs = bulkJobs();
    if (!jobs.length) return toast('Pilih kantor dan pastikan closing serta daftar AO tersedia.');
    const existing = jobs.filter((item) => findExisting(item.person, item.closing));
    let mode = 'pending';
    if (existing.length) {
      mode = await modalChoice('Data KPI sudah tersedia', `${existing.length} dari ${jobs.length} periode sudah pernah digenerate.`, {pending:true, pendingLabel:'Yang belum ada', allLabel:'Generate semua'});
      if (mode === 'cancel') return;
    }
    const work = mode === 'all' ? jobs : jobs.filter((item) => !findExisting(item.person, item.closing));
    const button = el('v2KpiGenerate');
    button.disabled = true;
    const rows = [];
    updateStatus(work.length ? `Memproses ${work.length} periode...` : 'Semua periode sudah tersedia.');
    try {
      let done = 0;
      for (const item of work) {
        const row = {nama_ao:item.person.nama_ao, kode_ao:item.person.kode_ao, id_peg:item.person.id_peg, kode_kantor:item.person.kode_kantor, closing_date:item.closing, status:'Memproses...', nilai_akhir:0};
        rows.push(row); renderBulkRows(rows);
        try {
          const result = await post({type:'calculate', year:year(), jabatan_kode:job(), kode_ao:item.person.kode_ao, id_peg:item.person.id_peg, kode_kantor:item.person.kode_kantor, closing_date:item.closing});
          row.status = 'Selesai'; row.nilai_akhir = result.data?.[0]?.nilai_akhir || 0;
          directory().generated = directory().generated || [];
          directory().generated.push({...row});
        } catch (error) { row.status = `Gagal: ${error.message}`; }
        done += 1; updateStatus(`Selesai ${done} dari ${work.length} periode.`); renderBulkRows(rows);
      }
      toast('Generate KPI selesai.');
    } finally { button.disabled = false; }
  }

  const wait = (callback, tries = 0) => { if (window.__v2KpiDirectory || tries > 80) callback(); else window.setTimeout(() => wait(callback, tries + 1), 100); };
  const bind = () => {
    if (TAB === 'calculate') {
      el('v2KpiRun')?.addEventListener('click', runCalculate, true);
      el('v2KpiAo')?.addEventListener('change', loadEvaluation);
      el('v2KpiClosing')?.addEventListener('change', loadEvaluation);
      el('v2KpiYear')?.addEventListener('change', () => window.setTimeout(loadEvaluation, 250));
      wait(loadEvaluation);
    } else {
      el('v2KpiGenerate')?.addEventListener('click', runBulk, true);
      el('v2KpiKantor')?.addEventListener('change', () => window.setTimeout(() => renderBulkRows((directory().generated || []).filter((item) => !el('v2KpiKantor').value || String(item.kode_kantor) === String(el('v2KpiKantor').value))), 100));
      el('v2KpiAo')?.addEventListener('change', () => window.setTimeout(() => renderBulkRows((directory().generated || []).filter((item) => !el('v2KpiAo').value || String(item.kode_ao) === String(el('v2KpiAo').value))), 100));
      wait(() => renderBulkRows(directory().generated || []));
    }
  };
  bind();
})();
</script>

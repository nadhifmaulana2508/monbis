<?php
$tab = strtolower((string)($_GET['tab'] ?? 'summary'));
if (!in_array($tab, ['summary', 'calculate', 'setting'], true)) $tab = 'summary';
?>
<section class="v2-page-heading <?= $tab === 'setting' ? 'v2-kpi-setting-heading' : 'v2-module-heading' ?>">
<?php if ($tab === 'setting'): ?>
  <div class="v2-kpi-setting-title"><span class="v2-kpi-setting-icon"><?= v2_icon('edit', 22) ?></span><div><h1>Setting KPI Jabatan <span class="v2-kpi-info">i</span></h1><p>Kelola indikator, bobot, arah penilaian, dan sumber data KPI bisnis.</p></div></div>
  <div class="v2-kpi-setting-filters">
    <?= v2_select('v2KpiJabatan', 'Jabatan', ['AO_KREDIT'=>'AO Kredit'], 'AO_KREDIT', ['data-v2-filter-field'=>'jabatan']) ?>
    <?= v2_select('v2KpiUnit', 'Unit', [''=>'Semua Unit'], '', ['data-v2-filter-field'=>'unit']) ?>
  </div>
<?php else: ?>
  <div><p class="v2-eyebrow">MONBIS / KPI BISNIS</p><h1>KPI Bisnis</h1><p>Kelola parameter, proses penilaian, dan rekap KPI menggunakan FE V2.</p></div>
  <div class="v2-page-status"><?= v2_badge('API KPI aktif', 'success') ?></div>
<?php endif; ?>
</section>

<?php if ($tab !== 'setting'): ?>
<?= v2_filter_drawer([
    ['name'=>'year', 'id'=>'v2KpiYear', 'label'=>'Tahun', 'type'=>'number', 'value'=>date('Y'), 'attrs'=>['min'=>'2020','max'=>'2100','step'=>'1']],
    ['name'=>'jabatan', 'id'=>'v2KpiJabatan', 'label'=>'Jabatan', 'type'=>'select', 'value'=>'AO_KREDIT', 'options'=>['AO_KREDIT'=>'AO Kredit']],
    ['name'=>'unit', 'id'=>'v2KpiUnit', 'label'=>'Unit indikator', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Semua unit']],
    ['name'=>'kantor', 'id'=>'v2KpiKantor', 'label'=>'Kantor', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Semua kantor']],
    ['name'=>'ao', 'id'=>'v2KpiAo', 'label'=>'AO', 'type'=>'select', 'value'=>'', 'options'=>[''=>'Semua AO']],
    ['name'=>'closing', 'id'=>'v2KpiClosing', 'label'=>'Closing KPI', 'type'=>'date', 'value'=>''],
], 'v2KpiFilters') ?>
<?php endif; ?>

<section class="v2-card v2-module-shell<?= $tab === 'setting' ? ' v2-kpi-setting-shell' : '' ?>">
  <div class="v2-card-heading v2-module-toolbar">
    <div><h2><?= $tab === 'summary' ? 'Rekap KPI AO' : ($tab === 'calculate' ? 'Hitung dan Generate KPI' : 'Master Indikator KPI') ?></h2><?php if ($tab !== 'setting'): ?><p>Backend KPI lama tetap dipakai; tampilan dan interaksi sudah dirakit ulang dengan component V2.</p><?php endif; ?></div>
<?php if ($tab === 'setting'): ?>
    <div class="v2-kpi-setting-actions"><label class="v2-search-field"><?= v2_icon('search', 14) ?><input id="v2KpiSettingSearch" type="search" placeholder="Cari indikator..."></label><button type="button" class="v2-button v2-button--success" id="v2KpiSave" title="Simpan semua perubahan"><?= v2_icon('save', 16) ?><span>Simpan</span></button></div>
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
<?php elseif ($tab === 'calculate'): ?>
    <div class="v2-module-callout"><div><strong>Generate penilaian KPI</strong><p>Pilih AO. Jika closing dikosongkan, seluruh closing date yang tersedia untuk tahun tersebut akan diproses.</p></div><button type="button" class="v2-button v2-button--primary" id="v2KpiRun"><?= v2_icon('check', 16) ?><span>Hitung / Generate</span></button></div>
    <div class="v2-module-progress" id="v2KpiRunStatus" hidden></div>
    <div class="v2-table-wrap v2-module-table-wrap"><table class="v2-table v2-kpi-generated-table"><thead><tr><th>AO</th><th>KANTOR</th><th>CLOSING</th><th>STATUS</th><th>NILAI AKHIR</th><th>KETERANGAN</th></tr></thead><tbody id="v2KpiGeneratedBody"><tr><td colspan="6" class="v2-empty">Memuat periode KPI...</td></tr></tbody></table></div>
<?php else: ?>
    <div class="v2-table-wrap v2-module-table-wrap v2-kpi-setting-table-wrap"><table class="v2-table v2-kpi-setting-table" id="v2KpiSettingTable"><thead><tr><th>JABATAN</th><th>KELOMPOK</th><th>INDIKATOR</th><th>BOBOT</th><th data-score-column="0">0</th><th data-score-column="1">1</th><th data-score-column="2">2</th><th data-score-column="3">3</th><th data-score-column="4">4</th><th data-score-column="5">5</th><th>TARGET DEFAULT</th><th>ARAH</th><th>UNIT</th><th>STATUS</th></tr></thead><tbody id="v2KpiSettingBody"><tr><td colspan="14" class="v2-empty">Memuat setting KPI...</td></tr></tbody></table></div>
<?php endif; ?>
  </div>
</section>

<script>
(() => {
  const TAB = <?= json_encode($tab) ?>;
  const API = <?= json_encode($legacyBase . '/api/index.php?request=kpi') ?>;
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
    const response = await fetch(API, { method: 'POST', credentials: 'same-origin', headers: {'Content-Type':'application/json'}, body: JSON.stringify(body) });
    const json = await response.json().catch(() => ({}));
    if (!response.ok || Number(json.status) !== 200) throw new Error(json.message || `Request KPI gagal (${response.status})`);
    return json.data || {};
  };
  const filters = () => ({ year: field('year')?.value || new Date().getFullYear(), jabatan_kode: field('jabatan')?.value || 'AO_KREDIT', kode_kantor: field('kantor')?.value || '', kode_ao: field('ao')?.value || '', closing_date: field('closing')?.value || '' });
  const showError = (id, message, colspan) => { const node = el(id); if (node) node.innerHTML = `<tr><td colspan="${colspan}" class="v2-empty v2-negative">${esc(message)}</td></tr>`; };
  const toast = (message) => window.V2Toast?.show(message) || window.alert(message);

  function fillDirectory(data) {
    state.directory = data;
    const jabatan = el('v2KpiJabatan');
    const currentJob = data.jabatan_terpilih?.kode || jabatan?.value || 'AO_KREDIT';
    if (jabatan) { jabatan.innerHTML = (data.jabatan || []).map(item => `<option value="${esc(item.kode)}">${esc(item.nama)}</option>`).join(''); jabatan.value = currentJob; }
    const kantor = el('v2KpiKantor');
    if (kantor) kantor.innerHTML = '<option value="">Semua kantor</option>' + (data.kantor || []).map(item => `<option value="${esc(item.kode_kantor)}">${esc(item.kode_kantor)} · ${esc(item.nama_kantor)}</option>`).join('');
    fillAo();
    const closing = el('v2KpiClosing');
    if (closing && data.closing_dates?.length && !closing.value) closing.value = data.closing_dates[data.closing_dates.length - 1];
  }
  function fillAo() {
    const select = el('v2KpiAo'); if (!select) return;
    const branch = field('kantor')?.value || '';
    const current = select.value;
    const rows = (state.directory?.ao || []).filter(item => !branch || String(item.kode_kantor) === String(branch));
    select.innerHTML = '<option value="">Semua AO</option>' + rows.map(item => `<option value="${esc(item.kode_ao)}" data-id-peg="${esc(item.id_peg || '')}" data-kantor="${esc(item.kode_kantor || '')}">${esc(item.kode_ao)} · ${esc(item.nama_ao)}</option>`).join('');
    if (rows.some(item => String(item.kode_ao) === current)) select.value = current;
  }
  function renderSummary(data) {
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
  function renderSetting() {
    const rows = settingIndicators();
    el('v2KpiSettingBody').innerHTML = rows.length ? rows.map(item => `<tr data-kpi-id="${num(item.id)}"><td>${esc(item.jabatan_nama)}</td><td>${esc(item.kelompok || '-')}</td><td><strong>${esc(item.nama)}</strong><small>${esc(item.definisi || '-')}</small></td><td><input class="v2-inline-input" data-kpi-field="bobot" value="${fmt(num(item.bobot) * 100)}" inputmode="decimal"></td><td><input class="v2-inline-input" data-kpi-field="target" value="${fmt(item.target_default)}" inputmode="decimal"></td><td>${esc(item.arah || '-')}</td><td>${esc(item.unit || '-')}</td><td><select class="v2-inline-input" data-kpi-field="status"><option value="AKTIF"${item.status === 'AKTIF' ? ' selected' : ''}>AKTIF</option><option value="PILOT"${item.status === 'PILOT' ? ' selected' : ''}>PILOT</option></select></td></tr>`).join('') : '<tr><td colspan="8" class="v2-empty">Tidak ada indikator aktif untuk filter ini.</td></tr>';
    renderScores();
  }
  const renderInlineScoreCell = (item, score, unit) => {
    if (!item) return '<span class="v2-muted-cell">-</span>';
    return `<div class="v2-score-editor" data-score-id="${num(item.id)}" data-score-unit="${esc(unit || '')}" data-score-predikat="${esc(item.predikat || '')}"><div class="v2-score-range"><input data-score-field="min" value="${esc(scoreValue(item.min_indeks, unit))}" inputmode="decimal" aria-label="Indeks minimum skor ${score}"><span>–</span><input data-score-field="max" value="${esc(scoreValue(item.max_indeks, unit, true))}" placeholder="∞" inputmode="decimal" aria-label="Indeks maksimum skor ${score}"></div></div>`;
  };
  function renderSetting() {
    const job = field('jabatan')?.value || 'AO_KREDIT';
    const ranges = (state.setting?.parameter_skor || []).filter(item => item.jabatan_kode === job && num(item.aktif));
    const rows = settingIndicators();
    el('v2KpiSettingBody').innerHTML = rows.length ? rows.map(item => `<tr data-kpi-id="${num(item.id)}" data-unit="${esc(item.unit || '')}"><td>${esc(item.jabatan_nama)}</td><td>${esc(item.kelompok || '-')}</td><td><strong>${esc(item.nama)}</strong><small>${esc(item.definisi || '-')}</small></td><td><input class="v2-inline-input" data-kpi-field="bobot" value="${fmt(num(item.bobot) * 100)}%" inputmode="decimal"></td>${[0,1,2,3,4,5].map(score => { const range = ranges.find(candidate => Number(candidate.indikator_id) === Number(item.id) && Number(candidate.skor) === score); return `<td data-score-column="${score}">${renderInlineScoreCell(range, score, item.unit)}</td>`; }).join('')}<td><input class="v2-inline-input" data-kpi-field="target" value="${fmt(item.target_default)}" inputmode="decimal"></td><td>${esc(item.arah || '-')}</td><td>${esc(item.unit || '-')}</td><td><select class="v2-inline-input" data-kpi-field="status"><option value="AKTIF"${item.status === 'AKTIF' ? ' selected' : ''}>AKTIF</option><option value="PILOT"${item.status === 'PILOT' ? ' selected' : ''}>PILOT</option></select></td></tr>`).join('') : '<tr><td colspan="14" class="v2-empty">Tidak ada indikator aktif untuk filter ini.</td></tr>';
  }
  async function loadSummary() { try { renderSummary(await post({type:'annual', ...filters()})); } catch (error) { showError('v2KpiSummaryBody', error.message, 18); } }
  async function loadDirectory() { try { fillDirectory(await post({type:'directory', year:field('year')?.value || new Date().getFullYear(), jabatan_kode:field('jabatan')?.value || 'AO_KREDIT', include_all_ao:true, include_generated:true})); if (TAB === 'summary') await loadSummary(); if (TAB === 'calculate') renderGenerated(state.directory); } catch (error) { const target = TAB === 'summary' ? 'v2KpiSummaryBody' : (TAB === 'calculate' ? 'v2KpiGeneratedBody' : 'v2KpiSettingBody'); showError(target, error.message, TAB === 'summary' ? 18 : TAB === 'calculate' ? 6 : 8); } }
  async function loadSetting() { try { state.setting = await post({type:'setting'}); const job = el('v2KpiJabatan'); if (job) { const currentJob = job.value || 'AO_KREDIT'; job.innerHTML = (state.setting.jabatan || []).map(item => `<option value="${esc(item.kode)}">${esc(item.nama)}</option>`).join(''); job.value = (state.setting.jabatan || []).some(item => item.kode === currentJob) ? currentJob : (state.setting.jabatan?.[0]?.kode || ''); } const unit = el('v2KpiUnit'); if (unit) { const currentUnit = unit.value || ''; const units = [...new Set((state.setting.indikator || []).map(item => item.unit).filter(Boolean))].sort(); unit.innerHTML = '<option value="">Semua unit</option>' + units.map(item => `<option value="${esc(item)}">${esc(item)}</option>`).join(''); unit.value = units.includes(currentUnit) ? currentUnit : ''; } renderSetting(); } catch (error) { showError('v2KpiSettingBody', error.message, 8); showError('v2KpiScoreBody', error.message, 8); } }
  el('v2KpiKantor')?.addEventListener('change', () => { fillAo(); if (TAB === 'summary') loadSummary(); });
  el('v2KpiAo')?.addEventListener('change', () => { if (TAB === 'summary') loadSummary(); });
  el('v2KpiYear')?.addEventListener('change', loadDirectory);
  el('v2KpiJabatan')?.addEventListener('change', async () => { if (TAB === 'setting') { await loadSetting(); } else await loadDirectory(); });
  el('v2KpiUnit')?.addEventListener('change', renderSetting);
  el('v2KpiSettingSearch')?.addEventListener('input', renderSetting);
  el('v2KpiRun')?.addEventListener('click', async () => { const option = el('v2KpiAo')?.selectedOptions[0], code = el('v2KpiAo')?.value; if (!code || !option?.dataset.idPeg) return toast('Pilih AO terlebih dahulu.'); const status = el('v2KpiRunStatus'); status.hidden = false; status.textContent = 'Memproses penilaian KPI...'; try { const result = await post({type:'calculate', year:field('year').value, jabatan_kode:field('jabatan').value, kode_ao:code, id_peg:option.dataset.idPeg, kode_kantor:option.dataset.kantor, closing_date:field('closing')?.value || '', skip_existing:true}); status.textContent = `${(result.data || []).length} periode berhasil diproses.`; toast('KPI berhasil digenerate.'); await loadDirectory(); } catch (error) { status.textContent = error.message; toast(error.message); } });
  el('v2KpiSave')?.addEventListener('click', async () => { const rows = [...document.querySelectorAll('#v2KpiSettingBody tr[data-kpi-id]')]; if (!rows.length) return; const button = el('v2KpiSave'); button.disabled = true; try { await Promise.all(rows.map(row => post({type:'save_indicator', id:Number(row.dataset.kpiId), bobot:num(row.querySelector('[data-kpi-field="bobot"]')?.value) / 100, target:num(row.querySelector('[data-kpi-field="target"]')?.value), status:row.querySelector('[data-kpi-field="status"]')?.value || 'AKTIF'}))); toast('Setting KPI berhasil disimpan.'); await loadSetting(); } catch (error) { toast(error.message); } finally { button.disabled = false; } });
  document.addEventListener('click', async (event) => { const button = event.target.closest('[data-v2-score-save]'); if (!button) return; const editor = button.closest('[data-score-id]'); const minInput = editor?.querySelector('[data-score-field="min"]'); const maxInput = editor?.querySelector('[data-score-field="max"]'); const unit = editor?.dataset.scoreUnit || ''; const min = parseScore(minInput?.value, unit); const max = parseScore(maxInput?.value, unit); if (!editor || max < min) return toast('Indeks maksimum tidak boleh lebih kecil dari minimum.'); button.disabled = true; try { await post({type:'save_score', id:Number(editor.dataset.scoreId), min_indeks:min, max_indeks:max, predikat:editor.dataset.scorePredikat || '', aktif:1}); const saved = (state.setting?.parameter_skor || []).find(item => Number(item.id) === Number(editor.dataset.scoreId)); if (saved) { saved.min_indeks = min; saved.max_indeks = max; } toast('Parameter skor berhasil disimpan.'); renderScores(); } catch (error) { toast(error.message); } finally { button.disabled = false; } });
  el('v2KpiSave')?.addEventListener('click', async (event) => { event.preventDefault(); event.stopImmediatePropagation(); const rows = [...document.querySelectorAll('#v2KpiSettingBody tr[data-kpi-id]')]; if (!rows.length) return; const button = el('v2KpiSave'); const scores = rows.flatMap(row => [...row.querySelectorAll('.v2-score-editor')].map(editor => { const unit = editor.dataset.scoreUnit || ''; return {type:'save_score', id:Number(editor.dataset.scoreId), min_indeks:parseScore(editor.querySelector('[data-score-field="min"]')?.value, unit), max_indeks:parseScore(editor.querySelector('[data-score-field="max"]')?.value, unit), predikat:editor.dataset.scorePredikat || '', aktif:1}; })); if (scores.some(item => item.max_indeks < item.min_indeks)) return toast('Indeks maksimum tidak boleh lebih kecil dari minimum.'); button.disabled = true; try { await Promise.all(rows.map(row => post({type:'save_indicator', id:Number(row.dataset.kpiId), bobot:parseInputNumber(row.querySelector('[data-kpi-field="bobot"]')?.value) / 100, target:parseInputNumber(row.querySelector('[data-kpi-field="target"]')?.value), status:row.querySelector('[data-kpi-field="status"]')?.value || 'AKTIF'}))); await Promise.all(scores.map(item => post(item))); toast('Setting KPI dan parameter skor berhasil disimpan.'); await loadSetting(); } catch (error) { toast(error.message); } finally { button.disabled = false; } }, true);
  if (TAB === 'setting') loadSetting(); else loadDirectory();
})();
</script>

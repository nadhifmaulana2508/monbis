<section class="v2-page-heading v2-collection-heading"><div><p class="v2-eyebrow">COLLECTION / REPORT NPL</p><h1>Report NPL</h1><p>Monitoring kolektibilitas kredit dan perubahan NPL secara konsisten.</p></div><div class="v2-page-status"><?= v2_badge('Live data', 'success') ?></div></section>

<?= v2_filter_drawer([
    ['name'=>'closing', 'id'=>'collectionClosing', 'label'=>'Closing (M-1)', 'type'=>'date', 'value'=>date('Y-m-d', strtotime('last day of previous month'))],
    ['name'=>'actual', 'id'=>'collectionActual', 'label'=>'Actual (Harian)', 'type'=>'date', 'value'=>date('Y-m-d')],
    ['name'=>'saldo', 'id'=>'collectionSaldo', 'label'=>'Tipe Saldo', 'type'=>'select', 'value'=>'baki_debet', 'options'=>['baki_debet'=>'Baki Debet', 'saldo_bank'=>'Saldo Bank']],
    ['name'=>'area', 'id'=>'collectionArea', 'label'=>'Area / Cabang', 'type'=>'select', 'value'=>'ALL', 'options'=>['ALL'=>'Konsolidasi']],
], 'collectionFilters') ?>

<section class="v2-card v2-collection-card">
<div class="v2-card-body v2-collection-card-body">
  <div class="v2-collection-toolbar"><div><span class="v2-eyebrow">REPORT NPL</span><strong class="v2-collection-view-label" id="collectionViewLabel">Kolektibilitas</strong></div><div class="v2-collection-toolbar-actions"><label class="v2-collection-search"><span><?= v2_icon('search', 15) ?></span><input type="search" id="collectionSearch" placeholder="Cari kode atau kantor..." aria-label="Cari kode atau kantor"></label><div class="v2-actions"><?= v2_icon_button('swap', 'Ganti report', 'default', ['data-collection-view-switch'=>'']) ?><?= v2_icon_button('download', 'Export Excel', 'primary', ['data-collection-export'=>'']) ?></div></div></div>
  <div class="v2-collection-loading" id="collectionLoading"><?= v2_spinner('Memuat data collection...') ?></div>
  <div class="v2-empty-state" id="collectionMessage" hidden></div>
  <div class="v2-table-wrap v2-collection-table-wrap" id="collectionTableWrap" hidden><table class="v2-table v2-collection-table" id="collectionTable"><thead id="collectionHead"></thead><tbody id="collectionBody"></tbody></table></div>
</div>
</section>

<script>
(() => {
  const API_BASE = <?= json_encode($apiBase) ?>;
  const API = { date: `${API_BASE}/date/`, kode: `${API_BASE}/kode/`, kolek: `${API_BASE}/kredit/`, npl: `${API_BASE}/npl/` };
  const $ = (selector) => document.querySelector(selector);
  const state = { view: 'kolek', rows: [], total: null, abort: null, kantor: [], closing: '', sort: { key: '', direction: 1 } };
  const num = (value) => Number(value || 0);
  const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const fmt = (value) => new Intl.NumberFormat('id-ID').format(num(value));
  const fmt2 = (value) => new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num(value));
  const field = (name) => $(`[data-v2-filter-field="${name}"]`);
  const selected = (name) => field(name)?.value || '';
  const postJson = async (url, payload, signal) => {
    const response = await fetch(url, { method: 'POST', credentials:'include', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload), signal });
    const json = await response.json();
    if (!response.ok || json.status >= 400) throw new Error(json.message || 'Request failed');
    return json;
  };
  const setLoading = (loading) => {
    $('#collectionLoading').hidden = !loading;
    $('#collectionTableWrap').hidden = loading;
    if (loading) $('#collectionMessage').hidden = true;
  };
  const toggleClosingFilter = () => field('closing')?.closest('.v2-field')?.classList.toggle('is-hidden', state.view !== 'npl');
  const showMessage = (title, message) => {
    const node = $('#collectionMessage');
    node.innerHTML = `<span class="v2-empty-icon"><?= v2_icon('file', 22) ?></span><strong>${esc(title)}</strong><p>${esc(message)}</p>`;
    node.hidden = false;
    $('#collectionTableWrap').hidden = true;
  };
  const money = (value, noa) => `<strong>${fmt(value)}</strong><small>${fmt(noa)} NOA</small>`;
  const status = (value) => num(value) > 0 ? 'Naik' : (num(value) < 0 ? 'Turun' : 'Tetap');
  const signed = (value) => num(value) > 0 ? '+' + fmt(value) : num(value) < 0 ? '-' + fmt(Math.abs(num(value))) : fmt(0);
  const sortableHeader = (label, key) => `<th><button type="button" class="v2-sort-button" data-sort-key="${key}" aria-label="Urutkan ${label}">${label}<span class="v2-sort-indicator">${state.sort.key === key ? (state.sort.direction === 1 ? '&uarr;' : '&darr;') : '&harr;'}</span></button></th>`;
  const sortRows = () => {
    if (!state.sort.key) return;
    const {key, direction} = state.sort;
    state.rows.sort((first, second) => {
      const firstValue = key === 'status' ? status(first.selisih_npl) : (first[key] ?? '');
      const secondValue = key === 'status' ? status(second.selisih_npl) : (second[key] ?? '');
      const firstNumber = Number(firstValue);
      const secondNumber = Number(secondValue);
      const bothNumbers = firstValue !== '' && secondValue !== '' && Number.isFinite(firstNumber) && Number.isFinite(secondNumber);
      const result = bothNumbers
        ? firstNumber - secondNumber
        : String(firstValue).localeCompare(String(secondValue), 'id', {numeric: true, sensitivity: 'base'});
      return result * direction;
    });
  };
  const applySearch = () => {
    const query = ($('#collectionSearch')?.value || '').toLowerCase();
    document.querySelectorAll('#collectionBody tr:not(.v2-total-row)').forEach((row) => { row.hidden = query && !row.textContent.toLowerCase().includes(query); });
  };
  function renderSummary() {}
  function renderKolek() {
    $('#collectionHead').innerHTML = `<tr>${[['Kode','kode_unit'],['Kantor','nama_unit'],['Lancar','bd_L'],['DPK','bd_DP'],['KL','bd_KL'],['D','bd_D'],['M','bd_M'],['Total NPL','bd_npl'],['Portfolio','total_bd'],['% NPL','persentase_npl']].map(([label, key]) => sortableHeader(label, key)).join('')}</tr>`;
    sortRows();
    const total = state.total ? `<tr class="v2-total-row"><td>ALL</td><td><strong>GRAND TOTAL</strong></td><td>${money(state.total.bd_L, state.total.noa_L)}</td><td>${money(state.total.bd_DP, state.total.noa_DP)}</td><td>${money(state.total.bd_KL, state.total.noa_KL)}</td><td>${money(state.total.bd_D, state.total.noa_D)}</td><td>${money(state.total.bd_M, state.total.noa_M)}</td><td>${money(state.total.bd_npl, state.total.noa_npl)}</td><td>${money(state.total.total_bd, state.total.total_noa)}</td><td><strong>${fmt2(state.total.persentase_npl)}%</strong></td></tr>` : '';
    const rows = state.rows.map((row) => `<tr><td>${esc(String(row.kode_unit || '').padStart(3, '0'))}</td><td><strong>${esc(row.nama_unit || '-')}</strong></td><td>${money(row.bd_L, row.noa_L)}</td><td>${money(row.bd_DP, row.noa_DP)}</td><td>${money(row.bd_KL, row.noa_KL)}</td><td>${money(row.bd_D, row.noa_D)}</td><td>${money(row.bd_M, row.noa_M)}</td><td>${money(row.bd_npl, row.noa_npl)}</td><td>${money(row.total_bd, row.total_noa)}</td><td>${fmt2(row.persentase_npl)}%</td></tr>`).join('');
    $('#collectionBody').innerHTML = total + rows;
  }
  function renderNpl() {
    $('#collectionHead').innerHTML = `<tr>${[['Kode','kode_unit'],['Kantor','nama_unit'],['Closing','npl_closing'],['Closing %','npl_closing_persen'],['Actual','npl_harian'],['Actual %','npl_harian_persen'],['Delta','selisih_npl'],['Delta %','selisih_npl_persen'],['Status','status']].map(([label, key]) => sortableHeader(label, key)).join('')}</tr>`;
    sortRows();
    const total = state.total ? `<tr class="v2-total-row"><td>ALL</td><td><strong>GRAND TOTAL</strong></td><td>${fmt(state.total.npl_closing)}</td><td>${fmt2(state.total.npl_closing_persen)}%</td><td>${fmt(state.total.npl_harian)}</td><td>${fmt2(state.total.npl_harian_persen)}%</td><td>${signed(state.total.selisih_npl)}</td><td>${signed(state.total.selisih_npl_persen)}%</td><td>${status(state.total.selisih_npl)}</td></tr>` : '';
    const rows = state.rows.map((row) => `<tr><td>${esc(String(row.kode_unit || '').padStart(3, '0'))}</td><td><strong>${esc(row.nama_unit || '-')}</strong></td><td>${fmt(row.npl_closing)}</td><td>${fmt2(row.npl_closing_persen)}%</td><td>${fmt(row.npl_harian)}</td><td>${fmt2(row.npl_harian_persen)}%</td><td>${signed(row.selisih_npl)}</td><td>${signed(row.selisih_npl_persen)}%</td><td>${status(row.selisih_npl)}</td></tr>`).join('');
    $('#collectionBody').innerHTML = total + rows;
  }
  function render() { renderSummary(); state.view === 'kolek' ? renderKolek() : renderNpl(); applySearch(); }
  function areaPayload() {
    const value = selected('area');
    return { kode_kantor: value.startsWith('CAB-') ? value.replace('CAB-', '') : '', korwil: value.startsWith('KOR-') ? value.replace('KOR-', '') : '' };
  }
  function buildAreaOptions() {
    const select = field('area');
    if (!select) return;
    let html = '<option value="ALL">Konsolidasi</option>';
    ['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'].forEach((name) => { html += `<option value="KOR-${name}">Korwil ${name[0] + name.slice(1).toLowerCase()}</option>`; });
    state.kantor.forEach((item) => { const code = String(item.kode_kantor || '').padStart(3, '0'); html += `<option value="CAB-${code}">${code} - ${esc(item.nama_kantor || 'Cabang')}</option>`; });
    select.innerHTML = html;
  }
  async function fetchData() {
    if (state.abort) state.abort.abort();
    state.abort = new AbortController();
    setLoading(true);
    try {
      const area = areaPayload();
      const common = { harian_date: selected('actual'), hitung_berdasarkan: selected('saldo') || 'baki_debet', ...area };
      const json = state.view === 'kolek'
        ? await postJson(API.kolek, { type: 'kolektibilitas', ...common }, state.abort.signal)
        : await postJson(API.npl, { type: 'NPL', closing_date: selected('closing') || state.closing || selected('actual'), ...common }, state.abort.signal);
      state.rows = Array.isArray(json.data?.data) ? json.data.data : [];
      state.total = json.data?.grand_total || null;
      render();
      $('#collectionMessage').hidden = true;
      $('#collectionTableWrap').hidden = false;
    } catch (error) {
      if (error.name !== 'AbortError') showMessage('Data belum tersedia', error.message || 'Gagal memuat data collection.');
    } finally { setLoading(false); }
  }
  function exportData() {
    const rows = [state.total, ...state.rows].filter(Boolean);
    const headers = state.view === 'kolek' ? ['Kode','Kantor','Lancar','DPK','KL','D','M','Total NPL','Portfolio','% NPL'] : ['Kode','Kantor','Closing','Closing %','Actual','Actual %','Delta','Delta %','Status'];
    const lines = [headers.join('\t')].concat(rows.map((row) => state.view === 'kolek' ? [row.kode_unit || 'ALL', row.nama_unit || 'GRAND TOTAL', row.bd_L, row.bd_DP, row.bd_KL, row.bd_D, row.bd_M, row.bd_npl, row.total_bd, row.persentase_npl].join('\t') : [row.kode_unit || 'ALL', row.nama_unit || 'GRAND TOTAL', row.npl_closing, row.npl_closing_persen, row.npl_harian, row.npl_harian_persen, row.selisih_npl, row.selisih_npl_persen, status(row.selisih_npl)].join('\t')));
    const blob = new Blob([lines.join('\n')], { type: 'application/vnd.ms-excel;charset=utf-8' });
    const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = `collection_${selected('actual') || 'actual'}.xls`; link.click(); URL.revokeObjectURL(link.href);
  }
  document.addEventListener('DOMContentLoaded', async () => {
    document.querySelectorAll('[data-v2-filter-field]').forEach((control) => { if (control.dataset.v2FilterField !== 'search') control.addEventListener('change', fetchData); });
    $('[data-collection-export]')?.addEventListener('click', exportData);
    $('#collectionHead')?.addEventListener('click', (event) => {
      const button = event.target.closest('[data-sort-key]');
      if (!button) return;
      const key = button.dataset.sortKey;
      state.sort = { key, direction: state.sort.key === key ? state.sort.direction * -1 : 1 };
      render();
    });
    $('[data-collection-view-switch]')?.addEventListener('click', () => { state.view = state.view === 'kolek' ? 'npl' : 'kolek'; state.sort = { key: '', direction: 1 }; $('#collectionViewLabel').textContent = state.view === 'kolek' ? 'Kolektibilitas' : 'Perbandingan NPL'; toggleClosingFilter(); fetchData(); });
    $('#collectionSearch')?.addEventListener('input', applySearch);
    try {
      const [dateResponse, kodeResponse] = await Promise.all([fetch(API.date).then((response) => response.json()), postJson(API.kode, {type:'kode_kantor'})]);
      if (dateResponse.data?.last_created) field('actual').value = dateResponse.data.last_created;
      state.closing = dateResponse.data?.last_closing || '';
      if (dateResponse.data?.last_closing) field('closing').value = dateResponse.data.last_closing;
      state.kantor = Array.isArray(kodeResponse.data) ? kodeResponse.data : [];
      buildAreaOptions();
    } catch (error) { buildAreaOptions(); }
    toggleClosingFilter();
    fetchData();
  });
})();
</script>

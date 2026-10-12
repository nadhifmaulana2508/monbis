<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

mb_render_report_page([
    'id'=>'reportRecoveryPage',
    'class'=>'mb-report-recovery mb-report-grouped-head',
    'header'=>[
        'id'=>'reportRecoveryHeader',
        'title'=>'Recovery NPL',
        'subtitle'=>'Flow NPL, recovery, dan posisi bersih closing vs actual.',
        'info_modal_id'=>'reportRecoveryInfo',
        'filters'=>[
            ['id'=>'reportRecoveryClosing','label'=>'Closing (M-1)','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
            ['id'=>'reportRecoveryActual','label'=>'Actual (Harian)','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
            ['id'=>'reportRecoveryArea','label'=>'Kantor','type'=>'select','width'=>'260px','options'=>['ALL'=>'Konsolidasi']],
        ],
        'actions'=>[],
    ],
    'toolbar'=>[
        'title'=>'Rekap Recovery NPL',
        'search'=>['id'=>'reportRecoverySearch','placeholder'=>'Cari kantor...'],
        'actions'=>[
            ['attrs'=>['id'=>'reportRecoveryExport'],'tone'=>'success','icon'=>'download','title'=>'Export Excel','aria_label'=>'Export Excel'],
        ],
    ],
    'table'=>[
          'wrapper_id'=>'reportRecoveryTableWrap',
          'table_id'=>'reportRecoveryTable',
          'loading_id'=>'reportRecoveryLoading',
          'loading_text'=>'Memuat data recovery...',
          'class'=>'mb-recovery-table mb-table--grouped-head',
          'colgroup_html'=>'
            <col style="width:44px">
            <col style="width:136px">
            <col style="width:94px">
            <col style="width:88px">
            <col style="width:50px">
            <col style="width:82px">
            <col style="width:82px">
            <col style="width:82px">
            <col style="width:92px">
            <col style="width:94px">
            <col style="width:92px">
          ',
          'thead_html'=>mb_build_grouped_thead([
              ['label'=>'Kode','class'=>'mb-code-col mb-sticky-left','sort'=>'kode'],
              ['label'=>'Kantor','class'=>'mb-sticky-left-2 mb-text-left','sort'=>'kantor','attrs'=>['id'=>'reportRecoveryNameHead','style'=>'--mb-sticky-1:44px']],
              ['label'=>'OSC NPL Closing','class'=>'mb-group mb-group--blue','sort'=>'npl_closing'],
              ['label'=>'Flow PAR','class'=>'mb-group mb-group--red','children'=>[
                  ['label'=>'Baki Debet','class'=>'mb-group mb-group--red','sort'=>'flow_npl'],
                  ['label'=>'%','class'=>'mb-group mb-group--red','sort'=>'flow_ratio'],
              ]],
              ['label'=>'Recovery NPL','class'=>'mb-group mb-group--green','children'=>[
                  ['label'=>'Lunas','class'=>'mb-group mb-group--green','sort'=>'lunas_npl'],
                  ['label'=>'OSC Backflow','class'=>'mb-group mb-group--green','sort'=>'backflow'],
                  ['label'=>'Angsuran','class'=>'mb-group mb-group--green','sort'=>'angsuran_npl'],
                  ['label'=>'Total','class'=>'mb-group mb-group--green','sort'=>'total_recovery'],
              ]],
              ['label'=>'OSC NPL Actual','class'=>'mb-group mb-group--blue','sort'=>'npl_actual'],
              ['label'=>'-/+ OSC NPL','class'=>'mb-group mb-group--violet','sort'=>'osc_delta'],
          ]),
          'tbody_ids'=>['reportRecoveryTotal','reportRecoveryBody'],
    ],
]);

mb_render_info_modal([
    'id'=>'reportRecoveryInfo',
    'title'=>'Panduan Recovery NPL',
    'subtitle'=>'Ringkasan kolom dan arah tindak lanjut.',
    'body_html'=>'
      <div class="mb-npl-brief">
        <div class="mb-npl-brief__alert">
          <strong id="reportRecoveryInfoText">Data mengikuti filter yang sedang dibuka.</strong>
          <span id="reportRecoveryInfoDesc">Fokus utama adalah menekan Flow NPL dan memperbesar recovery melalui lunas, backflow, serta angsuran NPL.</span>
        </div>
        <div class="mb-npl-brief__metrics">
          <div><span>Total Recovery</span><strong id="reportRecoveryInfoRecovery">-</strong></div>
          <div><span>Flow NPL</span><strong id="reportRecoveryInfoFlow">-</strong></div>
          <div><span>-/+ OSC NPL</span><strong id="reportRecoveryInfoNet">-</strong></div>
        </div>
        <div class="mb-npl-brief__section">
          <div class="mb-npl-brief__section-title">Cara Membaca</div>
          <div class="mb-npl-brief__priority-grid">
            <div class="mb-npl-brief__priority mb-npl-brief__priority--red"><b>1</b><div><strong>Flow NPL</strong><span>Rekening yang memburuk dari posisi closing ke actual.</span></div></div>
            <div class="mb-npl-brief__priority mb-npl-brief__priority--blue"><b>2</b><div><strong>Total Recovery</strong><span>Lunas NPL + backflow + angsuran NPL.</span></div></div>
            <div class="mb-npl-brief__priority mb-npl-brief__priority--violet"><b>3</b><div><strong>-/+ OSC NPL</strong><span>Positif berarti OSC NPL naik karena flow lebih besar dari recovery. Negatif berarti OSC NPL berkurang; jika rasio NPL tetap naik, kemungkinan porto kredit ikut turun.</span></div></div>
          </div>
        </div>
        <div class="mb-info-warning"><span>Catatan:</span><div>Jika total recovery berbeda dengan tabel migrasi kolek, kemungkinan ada kapitalisasi atau restrukturisasi yang mempengaruhi baki debet.</div></div>
      </div>'
]);

mb_render_detail_modal([
    'id'=>'reportRecoveryDetail',
    'title'=>'Detail Debitur',
    'subtitle'=>'Daftar rekening recovery',
    'size'=>'xl',
    'mobile_body_id'=>'reportRecoveryDetailMobile',
    'search_near_close'=>true,
    'collapsible_filters'=>true,
    'toolbar_id'=>'reportRecoveryDetailFilters',
    'search'=>['id'=>'reportRecoveryDetailSearch','placeholder'=>'Cari nama / rekening...'],
    'filters'=>[
        ['id'=>'reportRecoveryDetailKind','label'=>'Jenis','type'=>'select','width'=>'150px','class'=>'is-hidden','options'=>[
            'all'=>'Semua Recovery',
            'lunas'=>'Lunas',
            'backflow'=>'Backflow',
            'angsuran'=>'Stay',
        ]],
        ['id'=>'reportRecoveryDetailJt','label'=>'Backflow','type'=>'select','width'=>'150px','class'=>'is-hidden','options'=>[
            'all'=>'Semua Backflow',
            'sudah'=>'Sudah Angsuran',
            'belum'=>'Potensi Flow',
        ]],
    ],
]);
?>

<script>
(function () {
  const API_NPL = './api/npl/';
  const API_DATE = './api/date/';
  const API_KODE = './api/kode/';
  const ICON_DOWNLOAD = <?= json_encode(mb_svg('download'), JSON_UNESCAPED_SLASHES) ?>;
  const KORWIL = [
    { key:'SEMARANG', label:'Korwil Semarang' },
    { key:'SOLO', label:'Korwil Solo' },
    { key:'BANYUMAS', label:'Korwil Banyumas' },
    { key:'PEKALONGAN', label:'Korwil Pekalongan' }
  ];

  const state = {
    rows: [],
    total: null,
    kantor: [],
    userKode: '000',
    scope: 'konsolidasi',
    abort: null,
    detailRows: [],
    detailType: '',
    detailKode: '',
    detailKind: 'all',
    detailPage: 1,
    detailPageSize: 20,
    sort: { column:'kode', direction:'asc' }
  };

  const el = id => document.getElementById(id);
  const ui = () => window.MonbisUI || {};
  const num = value => Number(value || 0);
  const esc = value => ui().escape ? MonbisUI.escape(value) : String(value ?? '');
  const fmt = value => ui().fmt ? MonbisUI.fmt(value) : new Intl.NumberFormat('id-ID').format(num(value));
  const fmt2 = value => ui().fmt2 ? MonbisUI.fmt2(value) : new Intl.NumberFormat('id-ID', { minimumFractionDigits:2, maximumFractionDigits:2 }).format(num(value));
  const codeNumber = value => Number(String(value ?? '').replace(/\D/g, '') || 0);
  const fmtDate = value => {
    if (!value) return '-';
    const d = new Date(String(value).length === 10 ? value + 'T00:00:00' : value);
    if (Number.isNaN(d.getTime())) return '-';
    return String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0') + '/' + d.getFullYear();
  };
  const fmtDay = value => {
    if (!value) return '-';
    const d = new Date(String(value).length === 10 ? value + 'T00:00:00' : value);
    if (Number.isNaN(d.getTime())) return '-';
    return String(d.getDate()).padStart(2, '0');
  };

  function currentUserKode() {
    const user = (typeof window.getUser === 'function' && window.getUser()) || {};
    const raw = user.kode_kantor || user.kode || user.kantor || '000';
    const kode = String(raw || '000').padStart(3, '0');
    return kode === '099' ? '000' : kode;
  }

  function readDevReportUser() {
    if (typeof window.getUser === 'function') {
      const direct = window.getUser();
      if (direct) return direct;
    }
    for (const key of ['dpk_user', 'app_user', 'user']) {
      try {
        const parsed = JSON.parse(localStorage.getItem(key) || 'null');
        if (parsed) return parsed;
      } catch (error) {}
    }
    return null;
  }

  function canAccessDevReport(user) {
    const fields = [user?.job_position, user?.unit_kerja, user?.branch_name, user?.role]
      .map(value => String(value || '').toLowerCase());
    return fields.some(value => value.includes('divisi operasional')) || fields.includes('dev');
  }

  async function ensureDevReportAccess() {
    for (let i = 0; i < 15; i += 1) {
      const user = readDevReportUser();
      if (user) {
        if (canAccessDevReport(user)) return true;
        el('reportRecoveryPage').innerHTML = '<section class="mb-report-card mb-dev-denied"><strong>Akses khusus Divisi Operasional</strong><span>Halaman ini masih dalam area review component report.</span></section>';
        return false;
      }
      await new Promise(resolve => setTimeout(resolve, 200));
    }
    return true;
  }

  function rowCode(row) {
    return String(row?.kode_cabang || row?.kode_unit || '').padStart(state.scope === 'cabang' ? 6 : 3, '0');
  }

  function rowName(row) {
    return row?.nama_kantor || row?.nama_unit || '-';
  }

  function totalRecovery(row) {
    return num(row?.total_recovery || (num(row?.baki_debet_lunas) + num(row?.baki_debet_backflow) + num(row?.baki_debet_angsuran_npl)));
  }

  function totalRecoveryNoa(row) {
    return num(row?.total_noa_recovery || (num(row?.noa_lunas) + num(row?.noa_backflow) + num(row?.noa_angsuran_npl)));
  }

  // Alias fallback menjaga FE tetap kompatibel jika nama field API dirapikan nanti.
  function oscClosing(row) {
    return num(row?.osc_npl_closing ?? row?.npl_closing);
  }

  function oscActual(row) {
    return num(row?.osc_npl_actual ?? row?.npl_harian ?? row?.npl_closing);
  }

  function oscDelta(row) {
    return num(row?.selisih_os_npl ?? (oscActual(row) - oscClosing(row)));
  }

  function flowRatio(row) {
    const base = oscClosing(row);
    return base > 0 ? (num(row?.baki_debet_flow_npl) / base) * 100 : 0;
  }

  function metric(nom, noa, type, kode, tone) {
    if (num(nom) <= 0 && num(noa) <= 0) {
      return '<span class="mb-rec-empty">-<span class="mb-subvalue">0 NOA</span></span>';
    }
    return '<button type="button" class="mb-rec-metric mb-rec-metric--' + tone + '" data-type="' + esc(type) + '" data-kode="' + esc(kode) + '">' +
      '<span class="mb-rec-main">' + fmt(nom) + '</span><span class="mb-subvalue">' + fmt(noa) + ' NOA</span></button>';
  }

  function simpleMetric(nom, noa, tone) {
    if (num(nom) <= 0 && num(noa) <= 0) return '<span class="mb-rec-empty">-<span class="mb-subvalue">0 NOA</span></span>';
    return '<span class="mb-rec-value mb-rec-value--' + tone + '"><b class="mb-rec-main">' + fmt(nom) + '</b><span class="mb-subvalue">' + fmt(noa) + ' NOA</span></span>';
  }

  function netCell(value) {
    const n = num(value);
    const cls = n > 0 ? 'mb-negative' : (n < 0 ? 'mb-positive' : 'mb-muted');
    return '<span class="' + cls + '">' + (n > 0 ? '+' : '') + fmt(n) + '</span>';
  }

  function ratioCell(row) {
    const value = flowRatio(row);
    const cls = value <= 0 ? 'mb-pill--success' : (value > 7 ? 'mb-pill--danger' : 'mb-pill--neutral');
    return '<span class="mb-pill ' + cls + '">' + fmt2(value) + '%</span>';
  }

  function sortValue(row, column) {
    if (column === 'kode') return rowCode(row);
    if (column === 'kantor') return rowName(row);
    if (column === 'flow_npl') return row?.baki_debet_flow_npl;
    if (column === 'lunas_npl') return row?.baki_debet_lunas;
    if (column === 'backflow') return row?.baki_debet_backflow;
    if (column === 'angsuran_npl') return row?.baki_debet_angsuran_npl;
    if (column === 'total_recovery') return totalRecovery(row);
    if (column === 'flow_ratio') return flowRatio(row);
    if (column === 'npl_closing') return oscClosing(row);
    if (column === 'npl_actual') return oscActual(row);
    if (column === 'osc_delta') return oscDelta(row);
    return row?.[column];
  }

  function sortRows(rows) {
    const column = state.sort.column;
    const dir = state.sort.direction === 'asc' ? 1 : -1;
    return rows.slice().sort((a, b) => {
      const av = sortValue(a, column);
      const bv = sortValue(b, column);
      if (column === 'kode' || column === 'kantor') {
        return String(av || '').localeCompare(String(bv || ''), 'id-ID', { numeric:true }) * dir;
      }
      return (num(av) - num(bv)) * dir;
    });
  }

  function updateSortIcons() {
    document.querySelectorAll('#reportRecoveryTable .mb-sort').forEach(th => {
      const icon = th.querySelector('.mb-sort-icon');
      const active = th.dataset.sort === state.sort.column;
      if (icon) {
        icon.classList.toggle('is-active', active);
        icon.classList.toggle('is-desc', active && state.sort.direction === 'desc');
      }
    });
  }

  function bindSortHeaders() {
    document.querySelectorAll('#reportRecoveryTable .mb-sort').forEach(th => {
      th.addEventListener('click', () => {
        const column = th.dataset.sort;
        state.sort.direction = state.sort.column === column && state.sort.direction === 'asc' ? 'desc' : 'asc';
        state.sort.column = column;
        renderRows();
      });
    });
    updateSortIcons();
  }

  async function fetchJson(url, payload, signal) {
    const response = await fetch(url, {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body:JSON.stringify(payload || {}),
      signal
    });
    const text = await response.text();
    let json = {};
    try { json = text ? JSON.parse(text) : {}; } catch (error) {
      throw new Error('Response bukan JSON: ' + text.slice(0, 120));
    }
    if (!response.ok) throw new Error(json.message || ('HTTP ' + response.status));
    return json;
  }

  function selectedAreaPayload() {
    const value = el('reportRecoveryArea')?.value || 'ALL';
    const payload = {};
    state.scope = 'konsolidasi';
    if (value.startsWith('CAB-')) {
      payload.kode_kantor = value.replace('CAB-', '');
      state.scope = 'cabang';
    } else if (value.startsWith('KOR-')) {
      payload.korwil = value.replace('KOR-', '');
      state.scope = 'korwil';
    }
    return payload;
  }

  function buildAreaOptions() {
    const select = el('reportRecoveryArea');
    if (!select) return;

    if (state.userKode !== '000') {
      const found = state.kantor.find(item => String(item.kode_kantor || '').padStart(3, '0') === state.userKode);
      select.innerHTML = '<option value="CAB-' + state.userKode + '">' + state.userKode + ' - ' + esc(found?.nama_kantor || 'Cabang') + '</option>';
      select.disabled = true;
      return;
    }

    let html = '<option value="ALL">Konsolidasi</option>';
    KORWIL.forEach(item => { html += '<option value="KOR-' + item.key + '">' + item.label + '</option>'; });
    state.kantor
      .filter(item => String(item.kode_kantor || '').padStart(3, '0') !== '000')
      .sort((a, b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor), 'id-ID', { numeric:true }))
      .forEach(item => {
        const kode = String(item.kode_kantor || '').padStart(3, '0');
        html += '<option value="CAB-' + kode + '">' + kode + ' - ' + esc(item.nama_kantor || 'Cabang') + '</option>';
      });
    select.innerHTML = html;
  }

  async function loadDates() {
    try {
      const response = await fetch(API_DATE);
      const json = await response.json();
      const data = json.data || {};
      el('reportRecoveryClosing').value = data.last_closing || '';
      el('reportRecoveryActual').value = data.last_created || '';
    } catch (error) {
      const today = new Date().toISOString().slice(0, 10);
      el('reportRecoveryClosing').value = today;
      el('reportRecoveryActual').value = today;
    }
  }

  async function loadKantor() {
    try {
      const json = await fetchJson(API_KODE, { type:'kode_kantor' });
      state.kantor = Array.isArray(json.data) ? json.data : [];
    } catch (error) {
      state.kantor = [];
    }
    buildAreaOptions();
  }

  function normalizeResponse(json) {
    let rows = [];
    let total = null;
    if (json?.data && json?.grand_total) {
      rows = Array.isArray(json.data?.data) ? json.data.data : (Array.isArray(json.data) ? json.data : []);
      total = json.grand_total;
    } else if (Array.isArray(json?.data)) {
      rows = json.data;
      total = rows.find(row => String(row.nama_kantor || row.nama_unit || '').toUpperCase().includes('TOTAL')) || null;
      rows = rows.filter(row => !String(row.nama_kantor || row.nama_unit || '').toUpperCase().includes('TOTAL'));
    }
    return { rows, total };
  }

  function renderInfo() {
    const total = state.total || {};
    const rec = totalRecovery(total);
    const flow = num(total.baki_debet_flow_npl);
    const net = oscDelta(total);
    if (el('reportRecoveryInfoRecovery')) el('reportRecoveryInfoRecovery').textContent = 'Rp ' + fmt(rec);
    if (el('reportRecoveryInfoFlow')) el('reportRecoveryInfoFlow').textContent = 'Rp ' + fmt(flow);
    if (el('reportRecoveryInfoNet')) {
      el('reportRecoveryInfoNet').textContent = (net > 0 ? '+Rp ' : 'Rp ') + fmt(net);
      el('reportRecoveryInfoNet').className = net > 0 ? 'mb-negative' : (net < 0 ? 'mb-positive' : '');
    }
  }

  function renderTotal() {
    const body = el('reportRecoveryTotal');
    const row = state.total;
    if (!body || !row) {
      if (body) body.innerHTML = '';
      return;
    }
    const kode = totalDetailKode();
    body.innerHTML = '<tr class="mb-total-row">' +
      '<td class="mb-code-col mb-sticky-left mb-code">ALL</td>' +
      '<td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:44px" title="GRAND TOTAL">GRAND TOTAL</td>' +
      '<td class="mb-num">' + fmt(oscClosing(row)) + '</td>' +
      '<td class="mb-num">' + simpleMetric(row.baki_debet_flow_npl, row.noa_flow_npl, 'red') + '</td>' +
      '<td class="mb-noa">' + ratioCell(row) + '</td>' +
      '<td class="mb-num">' + metric(row.baki_debet_lunas, row.noa_lunas, 'lunas', kode, 'blue') + '</td>' +
      '<td class="mb-num">' + metric(row.baki_debet_backflow, row.noa_backflow, 'backflow', kode, 'amber') + '</td>' +
      '<td class="mb-num">' + metric(row.baki_debet_angsuran_npl, row.noa_angsuran_npl, 'angsuran', kode, 'green') + '</td>' +
      '<td class="mb-num">' + metric(totalRecovery(row), totalRecoveryNoa(row), 'total_recovery', kode, 'cyan') + '</td>' +
      '<td class="mb-num">' + fmt(oscActual(row)) + '</td>' +
      '<td class="mb-num">' + netCell(oscDelta(row)) + '</td>' +
      '</tr>';
  }

  function visibleRows() {
    const q = String(el('reportRecoverySearch')?.value || '').trim().toLowerCase();
    let rows = [...state.rows];
    if (q) {
      rows = rows.filter(row => (rowCode(row) + ' ' + rowName(row)).toLowerCase().includes(q));
    }
    return sortRows(rows);
  }

  function renderRows() {
    const body = el('reportRecoveryBody');
    if (!body) return;
    const rows = visibleRows();
    if (!rows.length) {
      body.innerHTML = '<tr><td colspan="11" class="mb-empty">Tidak ada data recovery.</td></tr>';
      return;
    }

    body.innerHTML = rows.map(row => {
      const kode = rowCode(row);
      return '<tr>' +
        '<td class="mb-code-col mb-sticky-left mb-code">' + esc(kode) + '</td>' +
        '<td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:44px" title="' + esc(rowName(row)) + '">' + esc(rowName(row)) + '</td>' +
        '<td class="mb-num">' + fmt(oscClosing(row)) + '</td>' +
        '<td class="mb-num">' + simpleMetric(row.baki_debet_flow_npl, row.noa_flow_npl, 'red') + '</td>' +
        '<td class="mb-noa">' + ratioCell(row) + '</td>' +
        '<td class="mb-num">' + metric(row.baki_debet_lunas, row.noa_lunas, 'lunas', kode, 'blue') + '</td>' +
        '<td class="mb-num">' + metric(row.baki_debet_backflow, row.noa_backflow, 'backflow', kode, 'amber') + '</td>' +
        '<td class="mb-num">' + metric(row.baki_debet_angsuran_npl, row.noa_angsuran_npl, 'angsuran', kode, 'green') + '</td>' +
        '<td class="mb-num">' + metric(totalRecovery(row), totalRecoveryNoa(row), 'total_recovery', kode, 'cyan') + '</td>' +
        '<td class="mb-num">' + fmt(oscActual(row)) + '</td>' +
        '<td class="mb-num">' + netCell(oscDelta(row)) + '</td>' +
        '</tr>';
    }).join('');
    updateSortIcons();
  }

  async function loadRecovery() {
    if (state.abort) state.abort.abort();
    state.abort = new AbortController();
    MonbisUI.showLoading('reportRecoveryLoading', true);

    try {
      const payload = {
        type:'Recovery NPL',
        closing_date: el('reportRecoveryClosing').value,
        harian_date: el('reportRecoveryActual').value,
        ...selectedAreaPayload()
      };
      const json = await fetchJson(API_NPL, payload, state.abort.signal);
      const normalized = normalizeResponse(json);
      state.rows = normalized.rows;
      state.total = normalized.total;
      renderTotal();
      renderRows();
      renderInfo();
      MonbisUI.closeMobileFilter('reportRecoveryHeaderFilters');
    } catch (error) {
      if (error.name !== 'AbortError') {
        console.error(error);
        el('reportRecoveryBody').innerHTML = '<tr><td colspan="11" class="mb-empty mb-negative">Gagal memuat data.</td></tr>';
      }
    } finally {
      MonbisUI.showLoading('reportRecoveryLoading', false);
    }
  }

  function totalDetailKode() {
    const area = el('reportRecoveryArea')?.value || 'ALL';
    if (area.startsWith('CAB-')) return area.replace('CAB-', '');
    if (state.userKode !== '000') return state.userKode;
    return '000';
  }

  function buildDetailPayload(type, kode) {
    const payload = {
      type,
      closing_date: el('reportRecoveryClosing').value,
      harian_date: el('reportRecoveryActual').value
    };
    const clean = String(kode || '').trim();
    const area = el('reportRecoveryArea')?.value || 'ALL';
    if (clean && clean !== '000' && clean.length > 3) {
      payload.kode_kantor = clean.substring(0, 3);
      payload.kode_kankas = clean;
    } else if (clean && clean !== '000' && clean !== 'ALL') {
      payload.kode_kantor = clean.padStart(3, '0');
    } else if (area.startsWith('CAB-')) {
      payload.kode_kantor = area.replace('CAB-', '');
    } else if (area.startsWith('KOR-')) {
      payload.korwil = area.replace('KOR-', '');
    }
    if (type === 'backflow') payload.jt_status = el('reportRecoveryDetailJt')?.value || 'all';
    return payload;
  }

  function detailTypeLabel(type) {
    if (type === 'lunas') return 'Lunas NPL';
    if (type === 'backflow') return 'Backflow';
    if (type === 'angsuran') return 'Angsuran NPL';
    if (type === 'flow_npl') return 'Flow NPL';
    return 'Total Recovery';
  }

  function recoveryKind(row) {
    const raw = String(row?.jenis_recovery || row?.status || row?.jt_status || '').toLowerCase();
    if (raw.includes('lunas')) return 'lunas';
    if (raw.includes('backflow')) return 'backflow';
    if (raw.includes('angsuran') || raw.includes('stay')) return 'angsuran';
    if (state.detailType === 'lunas') return 'lunas';
    if (state.detailType === 'backflow') return 'backflow';
    if (state.detailType === 'angsuran') return 'angsuran';
    return '';
  }

  function recoveryKindLabel(row) {
    const kind = recoveryKind(row);
    if (kind === 'lunas') return 'Lunas';
    if (kind === 'backflow') return 'Backflow';
    if (kind === 'angsuran') return 'Stay';
    return row?.jenis_recovery || '-';
  }

  function recoveryKindBadge(row) {
    const kind = recoveryKind(row);
    const tone = kind === 'lunas' ? 'mb-pill--blue' : (kind === 'backflow' ? 'mb-pill--amber' : (kind === 'angsuran' ? 'mb-pill--success' : 'mb-pill--neutral'));
    return '<span class="mb-pill ' + tone + '">' + esc(recoveryKindLabel(row)) + '</span>';
  }

  function detailKindRows() {
    const kind = state.detailType === 'total_recovery'
      ? (el('reportRecoveryDetailKind')?.value || state.detailKind || 'all')
      : 'all';
    state.detailKind = kind;
    if (kind === 'all') return [...state.detailRows];
    return state.detailRows.filter(row => recoveryKind(row) === kind);
  }

  function detailSearchRows() {
    const q = String(el('reportRecoveryDetailSearch')?.value || '').trim().toLowerCase();
    const rows = detailKindRows();
    if (!q) return rows;
    return rows.filter(row => {
      return String(row.no_rekening || row.rekening || row.nama_nasabah || row.jenis_recovery || '').toLowerCase().includes(q) ||
        String(row.nama_nasabah || '').toLowerCase().includes(q);
    });
  }

  function statusBadge(status) {
    const raw = String(status || '-');
    const lower = raw.toLowerCase();
    const tone = (lower.includes('lunas') || lower.includes('sudah')) ? 'mb-pill--success' : (lower.includes('potensi') || lower.includes('belum') ? 'mb-pill--neutral' : 'mb-pill--danger');
    return '<span class="mb-pill ' + tone + '">' + esc(raw) + '</span>';
  }

  function detailDate(row) {
    return row?.tgl || row?.tanggal || row?.created || row?.tgl_jatuh_tempo;
  }

  function renderDetailFooter(totalRows, totalPages) {
    const footer = el('reportRecoveryDetailFooter');
    if (!footer) return;
    if (!totalRows) {
      footer.classList.add('is-hidden');
      footer.innerHTML = '';
      return;
    }
    footer.classList.remove('is-hidden');
    const from = ((state.detailPage - 1) * state.detailPageSize) + 1;
    const to = Math.min(totalRows, state.detailPage * state.detailPageSize);
    footer.innerHTML = '<div class="mb-detail-page-info">Data ' + fmt(from) + ' - ' + fmt(to) + ' dari ' + fmt(totalRows) + '</div>' +
      '<div class="mb-detail-page-actions">' +
        '<span class="mb-detail-footer-actions"><button type="button" class="mb-detail-footer-icon mb-detail-footer-icon--export" id="reportRecoveryDetailExport" title="Export detail" aria-label="Export detail">' + ICON_DOWNLOAD + '</button></span>' +
        '<span class="mb-detail-pagination"><button type="button" class="mb-detail-page-btn" id="reportRecoveryDetailPrev" ' + (state.detailPage <= 1 ? 'disabled' : '') + '>&lsaquo; Prev</button>' +
        '<span class="mb-detail-page-status">Hal ' + fmt(state.detailPage) + ' / ' + fmt(totalPages) + '</span>' +
        '<button type="button" class="mb-detail-page-btn" id="reportRecoveryDetailNext" ' + (state.detailPage >= totalPages ? 'disabled' : '') + '>Next &rsaquo;</button></span>' +
      '</div>';
    el('reportRecoveryDetailExport')?.addEventListener('click', exportDetailExcel);
    el('reportRecoveryDetailPrev')?.addEventListener('click', () => {
      if (state.detailPage > 1) {
        state.detailPage -= 1;
        renderDetail();
      }
    });
    el('reportRecoveryDetailNext')?.addEventListener('click', () => {
      if (state.detailPage < totalPages) {
        state.detailPage += 1;
        renderDetail();
      }
    });
  }

  function renderDetailMobile(rows) {
    const mobile = el('reportRecoveryDetailMobile');
    if (!mobile) return;
    if (!rows.length) {
      mobile.innerHTML = '<div class="mb-empty">Detail tidak ditemukan.</div>';
      return;
    }
    mobile.innerHTML = rows.map(row => {
      const status = state.detailType === 'lunas' ? 'Lunas' : (row.jt_status || row.status || row.jenis_recovery);
      return '<article class="mb-detail-card">' +
        '<div class="mb-detail-card__head"><div class="mb-detail-card__identity">' +
          '<div class="mb-detail-card__name">' + esc(row.nama_nasabah || '-') + '</div>' +
          '<div class="mb-detail-card__meta">' + esc(row.no_rekening || row.rekening || '-') + '</div>' +
        '</div>' + statusBadge(status) + '</div>' +
        '<div class="mb-detail-card__metrics">' +
          '<div class="mb-detail-card__metric"><span>Baki Debet</span><strong>' + fmt(row.baki_debet) + '</strong></div>' +
          '<div class="mb-detail-card__metric"><span>Recovery</span><strong class="mb-positive">' + fmt(row.recovery_nominal || row.nominal || 0) + '</strong></div>' +
          '<div class="mb-detail-card__metric"><span>Jenis</span><strong>' + esc(recoveryKindLabel(row)) + '</strong></div>' +
          '<div class="mb-detail-card__metric"><span>Kolek</span><strong>' + esc(row.kolek || '-') + ' / ' + esc(row.kolek_update || '-') + '</strong></div>' +
          '<div class="mb-detail-card__metric"><span>Tanggal</span><strong>' + fmtDay(detailDate(row)) + '</strong></div>' +
          '<div class="mb-detail-card__metric"><span>Tgl Bayar</span><strong>' + fmtDate(row.tgl_trans || row.tgl_bayar) + '</strong></div>' +
          '<div class="mb-detail-card__metric"><span>Angsuran Pokok</span><strong>' + fmt(row.angsuran_pokok || row.nominal_pokok || 0) + '</strong></div>' +
          '<div class="mb-detail-card__metric"><span>Angsuran Bunga</span><strong>' + fmt(row.angsuran_bunga || row.nominal_bunga || 0) + '</strong></div>' +
        '</div></article>';
    }).join('');
  }

  function renderDetail() {
    const body = el('reportRecoveryDetailBody');
    const summary = el('reportRecoveryDetailSummary');
    const rows = detailSearchRows();
    const summaryRows = detailKindRows();
    const totalNom = summaryRows.reduce((sum, row) => sum + num(row.recovery_nominal || row.baki_debet || row.nominal), 0);
    const totalBd = summaryRows.reduce((sum, row) => sum + num(row.baki_debet), 0);
    const totalPokok = summaryRows.reduce((sum, row) => sum + num(row.angsuran_pokok || row.nominal_pokok), 0);
    const totalBunga = summaryRows.reduce((sum, row) => sum + num(row.angsuran_bunga || row.nominal_bunga), 0);

    if (summary) {
      summary.classList.remove('is-hidden');
      summary.innerHTML = '<div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">Total Debitur</div><div class="mb-summary-card__value">' + fmt(summaryRows.length) + '</div></div>' +
        '<div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">Recovery</div><div class="mb-summary-card__value">Rp ' + fmt(totalNom) + '</div></div>' +
        '<div class="mb-summary-card"><div class="mb-summary-card__label">Baki Debet</div><div class="mb-summary-card__value">Rp ' + fmt(totalBd) + '</div></div>' +
        '<div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">Pokok + Bunga</div><div class="mb-summary-card__value">Rp ' + fmt(totalPokok + totalBunga) + '</div></div>';
    }

    if (!rows.length) {
      body.innerHTML = '<div class="mb-empty">Detail tidak ditemukan.</div>';
      renderDetailMobile([]);
      renderDetailFooter(0, 1);
      return;
    }

    const totalPages = Math.max(1, Math.ceil(rows.length / state.detailPageSize));
    state.detailPage = Math.min(Math.max(1, state.detailPage), totalPages);
    const start = (state.detailPage - 1) * state.detailPageSize;
    const pageRows = rows.slice(start, start + state.detailPageSize);
    renderDetailMobile(pageRows);

    body.innerHTML = '<table class="mb-table mb-detail-mini"><thead><tr>' +
      '<th>Rekening</th><th>Nama Nasabah</th><th>Baki Debet</th><th>Recovery</th><th>Jenis</th><th>Kolek</th><th>Status</th><th>Tgl</th><th>Tgl Bayar</th><th>Angs. Pokok</th><th>Angs. Bunga</th>' +
      '</tr></thead><tbody>' + pageRows.map(row => {
        const status = state.detailType === 'lunas' ? 'Lunas' : (row.jt_status || row.status || row.jenis_recovery);
        const tanggal = detailDate(row);
        return '<tr>' +
          '<td class="mb-code">' + esc(row.no_rekening || row.rekening || '-') + '</td>' +
          '<td class="mb-name" title="' + esc(row.nama_nasabah || '-') + '">' + esc(row.nama_nasabah || '-') + '</td>' +
          '<td class="mb-text-right">' + fmt(row.baki_debet) + '</td>' +
          '<td class="mb-text-right mb-positive">' + fmt(row.recovery_nominal || row.nominal || 0) + '</td>' +
          '<td class="mb-text-center">' + recoveryKindBadge(row) + '</td>' +
          '<td class="mb-text-center">' + esc(row.kolek || '-') + ' / ' + esc(row.kolek_update || '-') + '</td>' +
          '<td class="mb-text-center">' + statusBadge(status) + '</td>' +
          '<td class="mb-text-center">' + fmtDay(tanggal) + '</td>' +
          '<td class="mb-text-center">' + fmtDate(row.tgl_trans || row.tgl_bayar) + '</td>' +
          '<td class="mb-text-right">' + fmt(row.angsuran_pokok || row.nominal_pokok || 0) + '</td>' +
          '<td class="mb-text-right">' + fmt(row.angsuran_bunga || row.nominal_bunga || 0) + '</td>' +
        '</tr>';
      }).join('') + '</tbody></table>';
    renderDetailFooter(rows.length, totalPages);
    const content = body.closest('.mb-detail-content');
    if (content) content.scrollTop = 0;
  }

  async function openDetail(type, kode) {
    state.detailType = type;
    state.detailKode = kode;
    const title = el('reportRecoveryDetailTitle');
    const subtitle = el('reportRecoveryDetailSubtitle');
    if (title) title.textContent = detailTypeLabel(type);
    if (subtitle) subtitle.textContent = 'Kode ' + kode + ' - posisi ' + fmtDate(el('reportRecoveryActual').value);
    if (el('reportRecoveryDetailSearch')) el('reportRecoveryDetailSearch').value = '';
    state.detailPage = 1;
    state.detailKind = 'all';
    const kind = el('reportRecoveryDetailKind');
    if (kind) kind.value = 'all';
    if (kind) kind.classList.toggle('is-hidden', type !== 'total_recovery');
    if (kind) kind.closest('.mb-field')?.classList.toggle('is-hidden', type !== 'total_recovery');
    const jt = el('reportRecoveryDetailJt');
    if (jt) jt.classList.toggle('is-hidden', type !== 'backflow');
    if (jt) jt.closest('.mb-field')?.classList.toggle('is-hidden', type !== 'backflow');

    MonbisUI.closeMobileFilter('reportRecoveryDetailFilters');
    MonbisUI.openModal('reportRecoveryDetail');
    el('reportRecoveryDetailBody').innerHTML = '<div class="mb-empty">Memuat detail debitur...</div>';
    el('reportRecoveryDetailMobile').innerHTML = '<div class="mb-empty">Memuat detail debitur...</div>';
    renderDetailFooter(0, 1);

    try {
      const json = await fetchJson(API_NPL, buildDetailPayload(type, kode));
      state.detailRows = Array.isArray(json.data) ? json.data : [];
      renderDetail();
    } catch (error) {
      console.error(error);
      state.detailRows = [];
      el('reportRecoveryDetailBody').innerHTML = '<div class="mb-empty mb-negative">Gagal mengambil detail.</div>';
    }
  }

  function exportExcel() {
    const rows = state.rows || [];
    if (!rows.length) return;
    const allRows = state.total ? [{ ...state.total, kode_cabang:'ALL', nama_kantor:'TOTAL' }, ...rows] : rows;
    let html = '<table border="1"><thead><tr><th>Kode</th><th>Nama Kantor</th><th>OSC NPL Closing</th><th>NOA Flow PAR</th><th>Baki Debet Flow PAR</th><th>% Flow PAR</th><th>NOA Lunas</th><th>Lunas</th><th>NOA Backflow</th><th>OSC Backflow</th><th>NOA Angsuran</th><th>Angsuran</th><th>Total NOA Recovery</th><th>Total Recovery</th><th>OSC NPL Actual</th><th>-/+ OSC NPL</th></tr></thead><tbody>';
    allRows.forEach(row => {
      html += '<tr>' +
        '<td style="mso-number-format:\'\\@\'">' + esc(row.kode_cabang || row.kode_unit || '') + '</td>' +
        '<td>' + esc(row.nama_kantor || row.nama_unit || '') + '</td>' +
        '<td>' + oscClosing(row) + '</td>' +
        '<td>' + num(row.noa_flow_npl) + '</td><td>' + num(row.baki_debet_flow_npl) + '</td><td>' + flowRatio(row).toFixed(2) + '%</td>' +
        '<td>' + num(row.noa_lunas) + '</td><td>' + num(row.baki_debet_lunas) + '</td>' +
        '<td>' + num(row.noa_backflow) + '</td><td>' + num(row.baki_debet_backflow) + '</td>' +
        '<td>' + num(row.noa_angsuran_npl) + '</td><td>' + num(row.baki_debet_angsuran_npl) + '</td>' +
        '<td>' + totalRecoveryNoa(row) + '</td><td>' + totalRecovery(row) + '</td>' +
        '<td>' + oscActual(row) + '</td><td>' + oscDelta(row) + '</td>' +
      '</tr>';
    });
    html += '</tbody></table>';
    const blob = new Blob([html], { type:'application/vnd.ms-excel' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'Report_Recovery_NPL_' + (el('reportRecoveryActual').value || 'data') + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  function exportDetailExcel() {
    const rows = detailKindRows();
    if (!rows.length) return;
    let html = '<table border="1"><thead><tr><th>Rekening</th><th>Nama Nasabah</th><th>Baki Debet</th><th>Recovery</th><th>Jenis</th><th>Kolek</th><th>Kolek Update</th><th>Status</th><th>Tgl</th><th>Tgl Bayar</th><th>Angsuran Pokok</th><th>Angsuran Bunga</th></tr></thead><tbody>';
    rows.forEach(row => {
      const status = state.detailType === 'lunas' ? 'Lunas' : (row.jt_status || row.status || row.jenis_recovery || '');
      html += '<tr>' +
        '<td style="mso-number-format:\'\\@\'">' + esc(row.no_rekening || row.rekening || '') + '</td>' +
        '<td>' + esc(row.nama_nasabah || '') + '</td>' +
        '<td>' + num(row.baki_debet) + '</td>' +
        '<td>' + num(row.recovery_nominal || row.nominal || 0) + '</td>' +
        '<td>' + esc(recoveryKindLabel(row)) + '</td>' +
        '<td>' + esc(row.kolek || '') + '</td>' +
        '<td>' + esc(row.kolek_update || '') + '</td>' +
        '<td>' + esc(status) + '</td>' +
        '<td>' + esc(fmtDay(detailDate(row))) + '</td>' +
        '<td>' + esc(fmtDate(row.tgl_trans || row.tgl_bayar)) + '</td>' +
        '<td>' + num(row.angsuran_pokok || row.nominal_pokok || 0) + '</td>' +
        '<td>' + num(row.angsuran_bunga || row.nominal_bunga || 0) + '</td>' +
      '</tr>';
    });
    html += '</tbody></table>';
    const blob = new Blob([html], { type:'application/vnd.ms-excel' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'Detail_Recovery_NPL_' + detailTypeLabel(state.detailType).replace(/\s+/g, '_') + '_' + (el('reportRecoveryActual').value || 'data') + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  async function init() {
    if (!(await ensureDevReportAccess())) return;
    state.userKode = currentUserKode();
    await Promise.all([loadDates(), loadKantor()]);
    ['reportRecoveryClosing', 'reportRecoveryActual', 'reportRecoveryArea'].forEach(id => {
      el(id)?.addEventListener('change', loadRecovery);
    });
    el('reportRecoverySearch')?.addEventListener('input', MonbisUI.debounce(renderRows, 180));
    el('reportRecoveryExport')?.addEventListener('click', exportExcel);
    el('reportRecoveryDetailSearch')?.addEventListener('input', MonbisUI.debounce(() => {
      state.detailPage = 1;
      renderDetail();
    }, 120));
    el('reportRecoveryDetailJt')?.addEventListener('change', () => openDetail(state.detailType, state.detailKode));
    el('reportRecoveryDetailKind')?.addEventListener('change', () => {
      state.detailPage = 1;
      renderDetail();
    });
    bindSortHeaders();
    el('reportRecoveryTable')?.addEventListener('click', event => {
      const btn = event.target.closest('[data-type][data-kode]');
      if (!btn) return;
      event.preventDefault();
      openDetail(btn.dataset.type, btn.dataset.kode);
    });
    loadRecovery();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>

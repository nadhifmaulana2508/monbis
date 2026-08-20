<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

echo '<main class="mb-report-page mb-report-npl" id="reportNplPage">';

mb_render_page_header([
    'id' => 'reportNplHeader',
    'title' => 'Monitoring Kredit',
    'subtitle' => 'Rekap kolektibilitas kredit dan perbandingan NPL closing vs actual.',
    'info_modal_id' => 'reportNplInfo',
    'filters' => [
        ['id'=>'reportNplSaldo','label'=>'Tipe Saldo','type'=>'select','width'=>'132px','options'=>[
            'baki_debet'=>'Baki Debet',
            'saldo_bank'=>'Saldo Bank',
        ]],
        ['id'=>'reportNplClosing','label'=>'Closing (M-1)','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
        ['id'=>'reportNplActual','label'=>'Actual (Harian)','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
        ['id'=>'reportNplArea','label'=>'Area / Cabang','type'=>'select','width'=>'260px','options'=>[
            'ALL'=>'Konsolidasi',
        ]],
    ],
    'actions' => [],
]);
?>

  <section class="mb-report-card mb-report-card--grow">
    <div class="mb-report-toolbar">
      <div class="mb-report-toolbar__title" id="reportNplTableTitle">Kolektibilitas</div>
      <div class="mb-report-toolbar__tools">
        <div class="mb-segmented" role="tablist" aria-label="Jenis report NPL">
          <button type="button" id="btnReportKolek" class="mb-segmented__btn is-active" data-report-view="kolek" title="Kolektibilitas" aria-label="Kolektibilitas"><?php echo mb_svg('file'); ?></button>
          <button type="button" id="btnReportNpl" class="mb-segmented__btn" data-report-view="npl" title="Perbandingan NPL" aria-label="Perbandingan NPL"><?php echo mb_svg('chart'); ?></button>
        </div>
        <label class="mb-search">
          <?php echo mb_svg('search'); ?>
          <input type="search" id="reportNplSearch" class="mb-field-control" placeholder="Cari kode / kantor..." autocomplete="off">
        </label>
        <button type="button" id="reportNplViewSwitch" class="mb-view-switch" title="Ganti report" aria-label="Ganti report"><?php echo mb_svg('chart'); ?></button>
        <button type="button" id="reportNplExport" class="mb-icon-button mb-icon-button--success" title="Export Excel" aria-label="Export Excel"><?php echo mb_svg('download'); ?></button>
      </div>
    </div>
    <?php
      mb_render_table_shell([
          'wrapper_id'=>'reportNplTableWrap',
          'table_id'=>'reportNplTable',
          'loading_id'=>'reportNplLoading',
          'loading_text'=>'Memuat data kredit...',
          'thead_html'=>'',
          'tbody_ids'=>['reportNplTotal','reportNplBody'],
      ]);
    ?>
  </section>
</main>

<?php
mb_render_info_modal([
    'id'=>'reportNplInfo',
    'title'=>'Ringkasan Kondisi NPL',
    'subtitle'=>'Sorotan posisi saat ini dan prioritas tindak lanjut.',
    'body_html'=>'
      <div class="mb-npl-brief">
        <div class="mb-npl-brief__alert">
          <strong id="reportNplInfoText">Data mengikuti filter yang sedang dibuka.</strong>
          <span id="reportNplInfoDesc">Gunakan report kolektibilitas untuk melihat komposisi kredit, lalu cek perbandingan NPL untuk membaca perubahan dari closing ke actual.</span>
        </div>
        <div class="mb-npl-brief__metrics">
          <div><span>% NPL Saat Ini</span><strong id="reportNplInfoPct">-</strong></div>
          <div><span>Nominal NPL</span><strong id="reportNplInfoNom">-</strong></div>
          <div><span id="reportNplInfoThirdLabel">NOA NPL</span><strong id="reportNplInfoNoa">-</strong></div>
        </div>
        <div class="mb-npl-brief__section">
          <div class="mb-npl-brief__section-title" id="reportNplInfoSectionTitle">Urutan Prioritas Tindak Lanjut</div>
          <div class="mb-npl-brief__priority-grid">
            <div class="mb-npl-brief__priority mb-npl-brief__priority--blue"><b>1</b><div><strong id="reportNplInfoP1Title">Potensi NPL</strong><span id="reportNplInfoP1Text">Cek debitur berisiko masuk NPL dan follow-up rekening yang masih bisa dicegah.</span></div></div>
            <div class="mb-npl-brief__priority mb-npl-brief__priority--violet"><b>2</b><div><strong id="reportNplInfoP2Title">Flow PAR</strong><span id="reportNplInfoP2Text">Pastikan komitmen pembayaran dan perubahan kolektibilitas dipantau pada posisi harian berikutnya.</span></div></div>
            <div class="mb-npl-brief__priority mb-npl-brief__priority--red"><b>3</b><div><strong id="reportNplInfoP3Title">Selesaikan NPL Terbesar</strong><span id="reportNplInfoP3Text">Fokus pada exposure terbesar, status hukum, agunan, restrukturisasi, dan jalur penyelesaian.</span></div></div>
          </div>
        </div>
        <div class="mb-info-warning"><span>Catatan:</span><div>Keputusan tindak lanjut tetap mengikuti detail rekening, kondisi debitur, dan ketentuan internal yang berlaku.</div></div>
      </div>'
]);
?>

<script>
(function () {
  const API_NPL = './api/npl/';
  const API_KOLEK = './api/kredit/';
  const API_DATE = './api/date/';
  const API_KODE = './api/kode/';
  const KORWIL = [
    { key:'SEMARANG', label:'Korwil Semarang' },
    { key:'SOLO', label:'Korwil Solo' },
    { key:'BANYUMAS', label:'Korwil Banyumas' },
    { key:'PEKALONGAN', label:'Korwil Pekalongan' },
  ];

  const state = {
    view: 'kolek',
    kantor: [],
    rows: [],
    total: null,
    userKode: '000',
    sort: { column:'kode_unit', direction:'asc' },
    abort: null,
  };

  const el = id => document.getElementById(id);
  const num = value => Number(value || 0);
  const esc = value => (window.MonbisUI ? MonbisUI.escape(value) : String(value ?? ''));
  const fmt = value => (window.MonbisUI ? MonbisUI.fmt(value) : new Intl.NumberFormat('id-ID').format(num(value)));
  const fmt2 = value => (window.MonbisUI ? MonbisUI.fmt2(value) : new Intl.NumberFormat('id-ID', { minimumFractionDigits:2, maximumFractionDigits:2 }).format(num(value)));
  const signed = value => {
    const n = num(value);
    if (n > 0) return '+' + fmt(n);
    if (n < 0) return '(' + fmt(Math.abs(n)) + ')';
    return fmt(0);
  };
  const signedPct = value => {
    const n = num(value);
    if (n > 0) return '^ ' + fmt2(n) + '%';
    if (n < 0) return 'v ' + fmt2(Math.abs(n)) + '%';
    return fmt2(0) + '%';
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
        el('reportNplPage').innerHTML = '<section class="mb-report-card mb-dev-denied"><strong>Akses khusus Divisi Operasional</strong><span>Halaman ini masih dalam area review component report.</span></section>';
        return false;
      }
      await new Promise(resolve => setTimeout(resolve, 200));
    }
    return true;
  }

  function codeOf(row) {
    return String(row?.kode_unit || row?.kode_cabang || '').padStart(3, '0');
  }

  function nameOf(row) {
    return row?.nama_unit || row?.nama_kantor || '-';
  }

  function toneClass(value, reverse = false) {
    const n = num(value);
    if (n === 0) return 'mb-muted';
    const bad = reverse ? n < 0 : n > 0;
    return bad ? 'mb-negative' : 'mb-positive';
  }

  function selectedAreaPayload() {
    const value = el('reportNplArea')?.value || 'ALL';
    const payload = { kode_kantor:'', korwil:'' };
    if (value.startsWith('CAB-')) payload.kode_kantor = value.replace('CAB-', '');
    if (value.startsWith('KOR-')) payload.korwil = value.replace('KOR-', '');
    return payload;
  }

  function buildAreaOptions() {
    const select = el('reportNplArea');
    if (!select) return;
    if (state.userKode !== '000') {
      const found = state.kantor.find(item => String(item.kode_kantor).padStart(3, '0') === state.userKode);
      select.innerHTML = `<option value="CAB-${state.userKode}">${state.userKode} - ${esc(found?.nama_kantor || 'Cabang')}</option>`;
      select.disabled = true;
      return;
    }

    let html = '<option value="ALL">Konsolidasi</option>';
    KORWIL.forEach(item => { html += `<option value="KOR-${item.key}">${item.label}</option>`; });
    state.kantor
      .filter(item => String(item.kode_kantor).padStart(3, '0') !== '000')
      .sort((a, b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor), 'id-ID', { numeric:true }))
      .forEach(item => {
        const kode = String(item.kode_kantor).padStart(3, '0');
        html += `<option value="CAB-${kode}">${kode} - ${esc(item.nama_kantor || 'Cabang')}</option>`;
      });
    select.innerHTML = html;
  }

  function moneyWithNoa(value, noa, cls = '') {
    return `<span class="${cls}">${fmt(value)}</span><span class="mb-subvalue">${fmt(noa)} NOA</span>`;
  }

  function pctBadge(value) {
    const pct = num(value);
    let cls = 'mb-pill--success';
    if (pct > 5) cls = 'mb-pill--danger';
    else if (pct >= 3) cls = 'mb-pill--neutral';
    return `<span class="mb-pill ${cls}">${fmt2(pct)}%</span>`;
  }

  function trendIcon(value) {
    const n = num(value);
    if (n > 0) return '<span class="mb-trend-icon mb-trend-icon--up" title="Naik" aria-label="Naik"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V5M6 11l6-6 6 6"/></svg></span>';
    if (n < 0) return '<span class="mb-trend-icon mb-trend-icon--down" title="Turun" aria-label="Turun"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6"/></svg></span>';
    return '<span class="mb-trend-icon mb-trend-icon--flat" title="Tetap" aria-label="Tetap"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/></svg></span>';
  }

  function setHead(html) {
    const head = document.querySelector('#reportNplTable thead');
    if (head) head.innerHTML = html;
    bindSortHeaders();
  }

  function updateInfoSummary() {
    const t = state.total || {};
    const pct = state.view === 'kolek' ? t.persentase_npl : t.npl_harian_persen;
    const nom = state.view === 'kolek' ? t.bd_npl : t.npl_harian;
    const thirdValue = state.view === 'kolek' ? t.noa_npl : t.selisih_npl;
    const infoText = el('reportNplInfoText');
    const infoPct = el('reportNplInfoPct');
    const infoNom = el('reportNplInfoNom');
    const infoNoa = el('reportNplInfoNoa');
    const infoThirdLabel = el('reportNplInfoThirdLabel');
    if (infoPct) infoPct.textContent = `${fmt2(pct)}%`;
    if (infoNom) infoNom.textContent = `Rp ${fmt(nom)}`;
    if (infoThirdLabel) infoThirdLabel.textContent = state.view === 'kolek' ? 'NOA NPL' : 'Kenaikan NPL';
    if (infoNoa) {
      infoNoa.textContent = state.view === 'kolek'
        ? (thirdValue === undefined || thirdValue === null ? '-' : `${fmt(thirdValue)} NOA`)
        : `Rp ${signed(thirdValue)}`;
      infoNoa.className = state.view === 'npl' ? toneClass(thirdValue) : '';
    }
    if (infoText) {
      const area = el('reportNplArea')?.selectedOptions?.[0]?.textContent || 'Konsolidasi';
      infoText.textContent = state.view === 'kolek'
        ? `Komposisi kolektibilitas ${area}: NPL saat ini ${fmt2(pct)}%.`
        : `Perbandingan NPL ${area}: NPL actual ${fmt2(pct)}%.`;
    }
    const copy = state.view === 'kolek'
      ? {
          desc:'Report ini membaca posisi kredit per kolektibilitas pada tanggal actual. Fokus utama ada di DPK, KL, D, M, total NPL, portofolio, dan rasio NPL.',
          section:'Cara Membaca Kolektibilitas',
          p1:['DPK dan KL','Pantau rekening DPK dan KL sebelum turun menjadi NPL. Prioritaskan follow-up rekening bernominal besar.'],
          p2:['Diragukan dan Macet','Kelompok D dan M adalah sumber utama nominal NPL. Cek agunan, komitmen bayar, restrukturisasi, dan jalur penyelesaian.'],
          p3:['Portofolio dan % NPL','Bandingkan total NPL dengan portofolio agar cabang yang rasio NPL tinggi langsung terlihat.']
        }
      : {
          desc:'Report ini membandingkan posisi NPL closing dengan actual. Icon merah berarti NPL naik, icon hijau berarti NPL turun.',
          section:'Cara Membaca Perbandingan NPL',
          p1:['NPL Naik','Jika selisih positif, cek cabang dan rekening penyumbang kenaikan terbesar sebelum akhir bulan.'],
          p2:['NPL Turun','Jika selisih negatif, pastikan penurunan berasal dari recovery, pelunasan, atau perbaikan kolektibilitas yang valid.'],
          p3:['Tindak Lanjut','Gunakan report kolektibilitas, flow PAR, dan detail NPL untuk menentukan PIC dan langkah penyelesaian.']
        };
    const setText = (id, value) => { const node = el(id); if (node) node.textContent = value; };
    setText('reportNplInfoDesc', copy.desc);
    setText('reportNplInfoSectionTitle', copy.section);
    setText('reportNplInfoP1Title', copy.p1[0]);
    setText('reportNplInfoP1Text', copy.p1[1]);
    setText('reportNplInfoP2Title', copy.p2[0]);
    setText('reportNplInfoP2Text', copy.p2[1]);
    setText('reportNplInfoP3Title', copy.p3[0]);
    setText('reportNplInfoP3Text', copy.p3[1]);
  }

  function renderKolekHead() {
    setHead(`
      <tr>
        <th class="mb-code-col mb-sticky-left mb-sort" data-sort="kode_unit" style="width:46px">Kode <span class="mb-sort-icon"></span></th>
        <th class="mb-sticky-left-2 mb-sort" data-sort="nama_unit" style="--mb-sticky-1:46px;width:154px;text-align:left">Nama Kantor <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--blue mb-sort" data-sort="bd_L" style="width:104px">Lancar <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--amber mb-sort" data-sort="bd_DP" style="width:100px">DPK <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--amber mb-sort" data-sort="bd_KL" style="width:106px">KL <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--red mb-sort" data-sort="bd_D" style="width:104px">D <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--red mb-sort" data-sort="bd_M" style="width:104px">M <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--red mb-sort" data-sort="bd_npl" style="width:106px">NPL <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--blue mb-sort" data-sort="total_bd" style="width:112px">Porto <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--cyan mb-sort" data-sort="persentase_npl" style="width:58px">% <span class="mb-sort-icon"></span></th>
      </tr>`);
  }

  function renderNplHead() {
    setHead(`
      <tr>
        <th class="mb-code-col mb-sticky-left mb-sort" data-sort="kode_unit" style="width:46px">Kode <span class="mb-sort-icon"></span></th>
        <th class="mb-sticky-left-2 mb-sort" data-sort="nama_unit" style="--mb-sticky-1:46px;width:150px;text-align:left">Nama Kantor <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--blue mb-sort" data-sort="npl_closing" style="width:118px">Closing <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--red mb-sort" data-sort="npl_harian" style="width:118px">Actual <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--amber mb-sort" data-sort="selisih_npl" style="width:112px">Selisih <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--blue mb-sort" data-sort="npl_closing_persen" style="width:58px">Cl. % <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--red mb-sort" data-sort="npl_harian_persen" style="width:58px">Act. % <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--amber mb-sort" data-sort="selisih_npl_persen" style="width:58px">Sel. % <span class="mb-sort-icon"></span></th>
        <th class="mb-group mb-group--cyan" style="width:58px">Arah</th>
      </tr>`);
  }

  function renderKolekRow(row, isTotal = false) {
    const trClass = isTotal ? ' class="mb-total-row"' : '';
    return `
      <tr${trClass}>
        <td class="mb-code-col mb-sticky-left">${isTotal ? 'ALL' : esc(codeOf(row))}</td>
        <td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:46px" title="${esc(isTotal ? 'GRAND TOTAL' : nameOf(row))}">${isTotal ? 'GRAND TOTAL' : esc(nameOf(row))}</td>
        <td class="mb-num">${moneyWithNoa(row.bd_L, row.noa_L)}</td>
        <td class="mb-num">${moneyWithNoa(row.bd_DP, row.noa_DP)}</td>
        <td class="mb-num mb-negative">${moneyWithNoa(row.bd_KL, row.noa_KL, 'mb-negative')}</td>
        <td class="mb-num mb-negative">${moneyWithNoa(row.bd_D, row.noa_D, 'mb-negative')}</td>
        <td class="mb-num mb-negative">${moneyWithNoa(row.bd_M, row.noa_M, 'mb-negative')}</td>
        <td class="mb-num mb-negative">${moneyWithNoa(row.bd_npl, row.noa_npl, 'mb-negative')}</td>
        <td class="mb-num">${moneyWithNoa(row.total_bd, row.total_noa)}</td>
        <td class="mb-noa">${pctBadge(row.persentase_npl)}</td>
      </tr>`;
  }

  function renderNplRow(row, isTotal = false) {
    const diffClass = toneClass(row.selisih_npl);
    const pctClass = toneClass(row.selisih_npl_persen);
    const trClass = isTotal ? ' class="mb-total-row"' : '';
    return `
      <tr${trClass}>
        <td class="mb-code-col mb-sticky-left">${isTotal ? 'ALL' : esc(codeOf(row))}</td>
        <td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:46px" title="${esc(isTotal ? 'GRAND TOTAL' : nameOf(row))}">${isTotal ? 'GRAND TOTAL' : esc(nameOf(row))}</td>
        <td class="mb-num">${fmt(row.npl_closing)}</td>
        <td class="mb-num">${fmt(row.npl_harian)}</td>
        <td class="mb-num ${diffClass}">${signed(row.selisih_npl)}</td>
        <td class="mb-num">${fmt2(row.npl_closing_persen)}%</td>
        <td class="mb-num">${fmt2(row.npl_harian_persen)}%</td>
        <td class="mb-num ${pctClass}">${signedPct(row.selisih_npl_persen)}</td>
        <td class="mb-noa">${trendIcon(row.selisih_npl)}</td>
      </tr>`;
  }

  function sortValue(row, column) {
    if (column === 'kode_unit') return codeOf(row);
    if (column === 'nama_unit') return nameOf(row);
    return row?.[column];
  }

  function sortRows(rows) {
    const column = state.sort.column;
    const dir = state.sort.direction === 'asc' ? 1 : -1;
    return rows.slice().sort((a, b) => {
      const av = sortValue(a, column);
      const bv = sortValue(b, column);
      if (column === 'nama_unit' || column === 'kode_unit') {
        return String(av || '').localeCompare(String(bv || ''), 'id-ID', { numeric:true }) * dir;
      }
      return (num(av) - num(bv)) * dir;
    });
  }

  function renderTable() {
    const q = (el('reportNplSearch')?.value || '').trim().toLowerCase();
    const filtered = state.rows.filter(row => !q || (codeOf(row) + ' ' + nameOf(row)).toLowerCase().includes(q));
    const totalHtml = state.total
      ? (state.view === 'kolek' ? renderKolekRow(state.total, true) : renderNplRow(state.total, true))
      : '';
    const bodyHtml = filtered.length
      ? sortRows(filtered).map(row => state.view === 'kolek' ? renderKolekRow(row) : renderNplRow(row)).join('')
      : `<tr><td colspan="${state.view === 'kolek' ? 10 : 9}" class="mb-empty">Data tidak ditemukan.</td></tr>`;
    el('reportNplTotal').innerHTML = totalHtml;
    el('reportNplBody').innerHTML = bodyHtml;
    updateSortIcons();
  }

  function setView(view) {
    state.view = view;
    state.sort = { column:'kode_unit', direction:'asc' };
    document.querySelectorAll('[data-report-view]').forEach(btn => btn.classList.toggle('is-active', btn.dataset.reportView === view));
    const switchBtn = el('reportNplViewSwitch');
    if (switchBtn) {
      switchBtn.innerHTML = view === 'kolek'
        ? '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20V10M12 20V4M19 20v-7"/><path d="M3 20h18"/></svg>'
        : '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h6"/></svg>';
      switchBtn.title = view === 'kolek' ? 'Ganti ke Perbandingan NPL' : 'Ganti ke Kolektibilitas';
      switchBtn.setAttribute('aria-label', switchBtn.title);
    }
    el('reportNplClosing')?.closest('.mb-field')?.classList.toggle('is-hidden', view === 'kolek');
    el('reportNplTableTitle').textContent = view === 'kolek'
      ? 'Kolektibilitas'
      : 'Perbandingan NPL';
    if (view === 'kolek') renderKolekHead();
    else renderNplHead();
    fetchData();
  }

  function updateSortIcons() {
    document.querySelectorAll('#reportNplTable .mb-sort').forEach(th => {
      const icon = th.querySelector('.mb-sort-icon');
      const active = th.dataset.sort === state.sort.column;
      if (icon) {
        icon.classList.toggle('is-active', active);
        icon.classList.toggle('is-desc', active && state.sort.direction === 'desc');
      }
    });
  }

  function bindSortHeaders() {
    document.querySelectorAll('#reportNplTable .mb-sort').forEach(th => {
      th.addEventListener('click', () => {
        const column = th.dataset.sort;
        state.sort.direction = state.sort.column === column && state.sort.direction === 'asc' ? 'desc' : 'asc';
        state.sort.column = column;
        renderTable();
      });
    });
    updateSortIcons();
  }

  async function fetchData() {
    const actual = el('reportNplActual')?.value;
    const closing = el('reportNplClosing')?.value;
    if (!actual || (state.view === 'npl' && !closing)) return;
    if (state.abort) state.abort.abort();
    state.abort = new AbortController();
    MonbisUI.showLoading('reportNplLoading', true);
    try {
      const area = selectedAreaPayload();
      const common = {
        harian_date: actual,
        hitung_berdasarkan: el('reportNplSaldo')?.value || 'baki_debet',
        kode_kantor: area.kode_kantor,
        korwil: area.korwil,
      };
      const json = state.view === 'kolek'
        ? await MonbisUI.postJson(API_KOLEK, { type:'kolektibilitas', ...common }, { signal:state.abort.signal })
        : await MonbisUI.postJson(API_NPL, { type:'NPL', closing_date:closing, ...common }, { signal:state.abort.signal });

      state.rows = Array.isArray(json.data?.data) ? json.data.data : [];
      state.total = json.data?.grand_total || null;
      updateInfoSummary();
      renderTable();
    } catch (error) {
      if (error.name !== 'AbortError') {
        console.error(error);
        state.rows = [];
        state.total = null;
        updateInfoSummary();
        el('reportNplTotal').innerHTML = '';
        el('reportNplBody').innerHTML = `<tr><td colspan="${state.view === 'kolek' ? 10 : 9}" class="mb-empty mb-negative">Gagal memuat data.</td></tr>`;
      }
    } finally {
      MonbisUI.showLoading('reportNplLoading', false);
    }
  }

  function exportTable() {
    const rows = [state.total ? { ...state.total, kode_unit:'ALL', nama_unit:'GRAND TOTAL' } : null, ...sortRows(state.rows)].filter(Boolean);
    const headers = state.view === 'kolek'
      ? ['Kode','Nama Kantor','Lancar','Noa Lancar','DPK','Noa DPK','KL','Noa KL','D','Noa D','M','Noa M','Total NPL','Noa NPL','Portofolio','Noa Portofolio','% NPL']
      : ['Kode','Nama Kantor','NPL Closing','NPL Actual','Selisih','% Closing','% Actual','% Selisih','Status'];
    const lines = [headers.join('\t')].concat(rows.map(row => state.view === 'kolek'
      ? [row.kode_unit || codeOf(row), row.nama_unit || nameOf(row), row.bd_L, row.noa_L, row.bd_DP, row.noa_DP, row.bd_KL, row.noa_KL, row.bd_D, row.noa_D, row.bd_M, row.noa_M, row.bd_npl, row.noa_npl, row.total_bd, row.total_noa, fmt2(row.persentase_npl)].join('\t')
      : [row.kode_unit || codeOf(row), row.nama_unit || nameOf(row), row.npl_closing, row.npl_harian, row.selisih_npl, fmt2(row.npl_closing_persen), fmt2(row.npl_harian_persen), fmt2(row.selisih_npl_persen), num(row.selisih_npl) > 0 ? 'Naik' : (num(row.selisih_npl) < 0 ? 'Turun' : 'Tetap')].join('\t')
    ));
    const blob = new Blob([lines.join('\n')], { type:'application/vnd.ms-excel;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `${state.view === 'kolek' ? 'Kolektibilitas' : 'Perbandingan_NPL'}_${el('reportNplActual').value || 'actual'}.xls`;
    a.click();
    URL.revokeObjectURL(a.href);
  }

  async function init() {
    if (!(await ensureDevReportAccess())) return;
    state.userKode = currentUserKode();
    try {
      const [dateJson, kodeJson] = await Promise.all([
        fetch(API_DATE).then(r => r.json()),
        MonbisUI.postJson(API_KODE, { type:'kode_kantor' }),
      ]);
      const today = new Date().toISOString().slice(0, 10);
      el('reportNplClosing').value = dateJson.data?.last_closing || today;
      el('reportNplActual').value = dateJson.data?.last_created || today;
      state.kantor = Array.isArray(kodeJson.data) ? kodeJson.data : [];
    } catch (error) {
      console.warn('Gagal memuat default report NPL', error);
      const today = new Date().toISOString().slice(0, 10);
      el('reportNplClosing').value ||= today;
      el('reportNplActual').value ||= today;
      state.kantor = [];
    }
    buildAreaOptions();
    renderKolekHead();
    updateInfoSummary();
    await fetchData();
  }

  document.addEventListener('DOMContentLoaded', () => {
    const delayedFetch = MonbisUI.debounce(fetchData, 450);
    ['reportNplClosing','reportNplActual','reportNplSaldo','reportNplArea'].forEach(id => {
      el(id)?.addEventListener('change', delayedFetch);
    });
    document.querySelectorAll('[data-report-view]').forEach(btn => {
      btn.addEventListener('click', () => setView(btn.dataset.reportView));
    });
    el('reportNplViewSwitch')?.addEventListener('click', () => setView(state.view === 'kolek' ? 'npl' : 'kolek'));
    el('reportNplSearch')?.addEventListener('input', MonbisUI.debounce(renderTable, 150));
    el('reportNplExport')?.addEventListener('click', exportTable);
    init();
  });
})();
</script>

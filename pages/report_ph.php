<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

echo '<main class="mb-report-page mb-report-npl mb-report-ph" id="reportPhPage">';

mb_render_page_header([
    'id' => 'reportPhHeader',
    'title' => 'Monitoring PH',
    'subtitle' => 'Recovery pinjaman hapus buku dan Loss Given Default.',
    'icon' => mb_svg('chart'),
    'info_modal_id' => 'reportPhInfo',
    'filters' => [
        ['id'=>'reportPhStart','label'=>'Dari','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
        ['id'=>'reportPhEnd','label'=>'Sampai','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
        ['id'=>'reportPhArea','label'=>'Area / Cabang','type'=>'select','width'=>'260px','options'=>['ALL'=>'Konsolidasi']],
    ],
    'actions' => [
        ['attrs'=>['id'=>'reportPhExport'],'tone'=>'success','icon'=>'download','title'=>'Export Excel'],
    ],
]);
?>
  <section class="mb-report-card mb-report-card--grow">
    <div class="mb-report-toolbar">
      <div class="mb-report-toolbar__title" id="reportPhTableTitle">Recovery PH</div>
      <div class="mb-report-toolbar__tools">
        <div class="mb-segmented" role="tablist" aria-label="Jenis report PH">
          <button type="button" id="reportPhRecoveryTab" class="mb-segmented__btn is-active" data-view="recovery" title="Recovery PH" aria-label="Recovery PH"><?php echo mb_svg('file'); ?></button>
          <button type="button" id="reportPhLgdTab" class="mb-segmented__btn" data-view="lgd" title="Rekap LGD" aria-label="Rekap LGD"><?php echo mb_svg('chart'); ?></button>
        </div>
        <label class="mb-search">
          <?php echo mb_svg('search'); ?>
          <input type="search" id="reportPhSearch" class="mb-field-control" placeholder="Cari kantor..." autocomplete="off">
        </label>
        <button type="button" id="reportPhViewSwitch" class="mb-view-switch" title="Ganti report" aria-label="Ganti report"><?php echo mb_svg('chart'); ?></button>
      </div>
    </div>
    <?php mb_render_table_shell([
        'wrapper_id'=>'reportPhTableWrap',
        'table_id'=>'reportPhTable',
        'loading_id'=>'reportPhLoading',
        'loading_text'=>'Memuat data PH...',
        'class'=>'mb-ph-table',
        'colgroup_html'=>'
          <col style="width:46px">
          <col style="width:140px">
          <col style="width:82px">
          <col style="width:108px">
          <col style="width:108px">
          <col style="width:102px">
          <col style="width:68px">
          <col style="width:68px">
        ',
        'thead_html'=>'',
        'tbody_ids'=>['reportPhTotal','reportPhBody'],
    ]); ?>
  </section>
</main>

<?php
mb_render_info_modal([
    'id'=>'reportPhInfo',
    'title'=>'Panduan Monitoring PH',
    'subtitle'=>'Panduan recovery hapus buku dan Loss Given Default.',
    'body_html'=>'
      <div class="mb-npl-brief">
        <div class="mb-npl-brief__alert"><strong id="reportPhInfoTitle">Recovery PH</strong><span id="reportPhInfoText">Recovery memantau pembayaran pokok dan bunga debitur hapus buku pada periode yang dipilih.</span></div>
        <div class="mb-npl-brief__metrics">
          <div><span id="reportPhInfoMetric1Label">Recovery</span><strong id="reportPhInfoMetric1">-</strong></div>
          <div><span id="reportPhInfoMetric2Label">NOA</span><strong id="reportPhInfoMetric2">-</strong></div>
          <div><span id="reportPhInfoMetric3Label">Periode</span><strong id="reportPhInfoMetric3">-</strong></div>
        </div>
        <div class="mb-npl-brief__section"><div class="mb-npl-brief__section-title">Cara Membaca</div><div class="mb-npl-brief__priority-grid">
          <div class="mb-npl-brief__priority mb-npl-brief__priority--blue"><b>1</b><div><strong>Recovery PH</strong><span>Nilai pokok, bunga, dan total pembayaran hapus buku pada periode aktif.</span></div></div>
          <div class="mb-npl-brief__priority mb-npl-brief__priority--violet"><b>2</b><div><strong>RR dan LGD</strong><span>RR tinggi dan LGD rendah menunjukkan efektivitas recovery yang lebih baik.</span></div></div>
          <div class="mb-npl-brief__priority mb-npl-brief__priority--red"><b>3</b><div><strong>Tindak lanjut</strong><span>Klik baris kantor untuk melihat rekening dan nominal recovery secara rinci.</span></div></div>
        </div></div>
      </div>',
]);

mb_render_detail_modal([
    'id'=>'reportPhDetail',
    'title'=>'Detail Debitur PH',
    'subtitle'=>'- ',
    'size'=>'xl',
    'search'=>['id'=>'reportPhDetailSearch','placeholder'=>'Cari nama / rekening...'],
    'actions'=>[['attrs'=>['id'=>'reportPhDetailExport'],'tone'=>'success','icon'=>'download','title'=>'Export Excel']],
]);
?>

<script>
(() => {
  'use strict';
  const API_PH = './api/hapus_buku/';
  const API_KODE = './api/kode/';
  const API_DATE = './api/date/';
  const el = id => document.getElementById(id);
  const fmt = value => new Intl.NumberFormat('id-ID').format(Number(value || 0));
  const pct = value => Number(value || 0).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}) + '%';
  const esc = value => String(value ?? '').replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
  const asNum = value => Number(value || 0);
  const today = () => new Date().toISOString().slice(0,10);
  const monthStart = date => String(date || today()).slice(0,7) + '-01';
  const displayDate = value => value ? String(value).slice(0,10).split('-').reverse().join('/') : '-';
  const post = (url, body) => window.MonbisUI.postJson(url, body);
  const debounce = (callback, delay = 160) => {
    let timer;
    return (...args) => {
      window.clearTimeout(timer);
      timer = window.setTimeout(() => callback(...args), delay);
    };
  };
  const state = { view:'recovery', rows:[], total:null, offices:[], userKode:'000', sort:{key:'kode_kantor',dir:1}, detail:{rows:[], type:'', kode:'', page:1, perPage:25} };

  function rowName(row) {
    const direct = row.nama_kantor || row.nama_unit;
    if (direct && !/^\d+(?:[.,]\d+)?$/.test(String(direct).trim())) return direct;
    const kode = String(row.kode_kantor || '').padStart(3,'0');
    const office = state.offices.find(item => String(item.kode_kantor || '').padStart(3,'0') === kode);
    return office?.nama_kantor || '-';
  }
  function areaRows(rows) {
    const area = el('reportPhArea')?.value || 'ALL';
    if (area === 'ALL') return rows;
    return rows.filter(row => String(row.kode_kantor).padStart(3,'0') === area);
  }
  function queryRows(rows) {
    const q = String(el('reportPhSearch')?.value || '').trim().toLowerCase();
    return areaRows(rows).filter(row => !q || (String(row.kode_kantor) + ' ' + rowName(row)).toLowerCase().includes(q));
  }
  function sumRows(rows, fields) {
    const total = { kode_kantor:'TOTAL', nama_kantor:'TOTAL' };
    fields.forEach(key => total[key] = rows.reduce((s,row) => s + asNum(row[key]), 0));
    return total;
  }
  function selectedRows() { return queryRows(state.rows.filter(row => String(row.kode_kantor) !== 'TOTAL')); }
  function sortRows(rows) {
    const {key,dir} = state.sort;
    return rows.slice().sort((a,b) => {
      const av = key === 'nama_kantor' ? rowName(a) : a[key];
      const bv = key === 'nama_kantor' ? rowName(b) : b[key];
      if (typeof av === 'string' || typeof bv === 'string') return String(av || '').localeCompare(String(bv || ''), 'id-ID') * dir;
      return (asNum(av) - asNum(bv)) * dir;
    });
  }
  function sortMark(key) { return `<span class="mb-sort-icon ${state.sort.key === key ? 'is-active ' + (state.sort.dir < 0 ? 'is-desc' : '') : ''}"></span>`; }
  function setHead(html) { const node = document.querySelector('#reportPhTable thead'); if (node) node.innerHTML = html; bindSort(); }
  function setView(view) {
    state.view = view;
    state.sort = {key:'kode_kantor',dir:1};
    el('reportPhRecoveryTab')?.classList.toggle('is-active',view === 'recovery');
    el('reportPhLgdTab')?.classList.toggle('is-active',view === 'lgd');
    el('reportPhViewSwitch').innerHTML = view === 'recovery' ? '<?php echo mb_svg('chart'); ?>' : '<?php echo mb_svg('file'); ?>';
    el('reportPhTableTitle').textContent = view === 'recovery' ? 'Recovery PH' : 'Rekap LGD';
    load();
  }
  function recoveryHead() {
    setHead(`<tr><th class="mb-code-col mb-sticky-left mb-sort" data-sort="kode_kantor">Kode ${sortMark('kode_kantor')}</th><th class="mb-sticky-left-2 mb-text-left mb-sort" data-sort="nama_kantor">Nama Kantor ${sortMark('nama_kantor')}</th><th class="mb-group mb-group--blue mb-sort" data-sort="total_pokok">Pokok ${sortMark('total_pokok')}</th><th class="mb-group mb-group--amber mb-sort" data-sort="total_bunga">Bunga ${sortMark('total_bunga')}</th><th class="mb-group mb-group--green mb-sort" data-sort="total_ph">Recovery ${sortMark('total_ph')}</th><th class="mb-group mb-group--violet mb-sort" data-sort="noa">NOA ${sortMark('noa')}</th></tr>`);
  }
  function lgdHead() {
    setHead(`<tr><th class="mb-code-col mb-sticky-left mb-sort" data-sort="kode_kantor">Kode ${sortMark('kode_kantor')}</th><th class="mb-sticky-left-2 mb-text-left mb-sort" data-sort="nama_kantor">Nama Kantor ${sortMark('nama_kantor')}</th><th class="mb-group mb-group--violet mb-sort" data-sort="noa">NOA ${sortMark('noa')}</th><th class="mb-group mb-group--blue mb-sort" data-sort="total_balance_ph">BD Hapus Buku ${sortMark('total_balance_ph')}</th><th class="mb-group mb-group--green mb-sort" data-sort="total_recovery_nominal">Recovery ${sortMark('total_recovery_nominal')}</th><th class="mb-group mb-group--amber mb-sort" data-sort="sisa_saldo_nominal">Sisa Saldo ${sortMark('sisa_saldo_nominal')}</th><th class="mb-group mb-sort" data-sort="persen_rr">RR ${sortMark('persen_rr')}</th><th class="mb-group mb-sort" data-sort="persen_lgd">LGD ${sortMark('persen_lgd')}</th></tr>`);
  }
  function cells(value, noa = null, className = '') { return `<span class="mb-money mb-num ${className}">${fmt(value)}</span>${noa === null ? '' : `<span class="mb-subvalue">${fmt(noa)} NOA</span>`}`; }
  function renderRecovery() {
    recoveryHead();
    const rows = sortRows(selectedRows());
    const total = sumRows(rows,['total_pokok','total_bunga','total_ph','noa']);
    const totalClick = total.noa > 0 ? 'mb-clickable' : '';
    const totalAttrs = total.noa > 0 ? 'data-detail="recovery" data-kode="ALL"' : '';
    el('reportPhTotal').innerHTML = `<tr class="mb-total-row"><td class="mb-code-col mb-sticky-left">ALL</td><td class="mb-sticky-left-2 mb-name ${totalClick}" ${totalAttrs}>GRAND TOTAL</td><td>${cells(total.total_pokok)}</td><td>${cells(total.total_bunga)}</td><td class="${totalClick}" ${totalAttrs}>${cells(total.total_ph, total.noa, 'mb-positive')}</td><td class="mb-noa">${fmt(total.noa)}</td></tr>`;
    el('reportPhBody').innerHTML = rows.length ? rows.map(row => `<tr><td class="mb-code-col mb-sticky-left mb-code">${esc(row.kode_kantor)}</td><td class="mb-sticky-left-2 mb-name mb-clickable" data-detail="recovery" data-kode="${esc(row.kode_kantor)}">${esc(rowName(row))}</td><td>${cells(row.total_pokok)}</td><td>${cells(row.total_bunga)}</td><td class="mb-clickable" data-detail="recovery" data-kode="${esc(row.kode_kantor)}">${cells(row.total_ph,row.noa,'mb-positive')}</td><td class="mb-noa">${fmt(row.noa)}</td></tr>`).join('') : '<tr><td colspan="6" class="mb-empty">Data tidak ditemukan.</td></tr>';
    info(total, 'Recovery PH', 'Total recovery', total.total_ph, 'NOA', total.noa, 'Periode', `${displayDate(el('reportPhStart').value)} - ${displayDate(el('reportPhEnd').value)}`);
  }
  function renderLgd() {
    lgdHead();
    const rows = sortRows(selectedRows());
    const total = sumRows(rows,['noa','total_balance_ph','total_recovery_nominal','sisa_saldo_nominal']);
    total.persen_rr = total.total_balance_ph ? total.total_recovery_nominal / total.total_balance_ph * 100 : 0;
    total.persen_lgd = Math.max(0, 100 - total.persen_rr);
    const row = (item, isTotal=false) => {
      const clickable = asNum(item.noa) > 0;
      const attrs = clickable ? `data-detail="lgd" data-kode="${isTotal ? 'ALL' : esc(item.kode_kantor)}"` : '';
      return `<tr class="${isTotal ? 'mb-total-row' : ''}"><td class="mb-code-col mb-sticky-left">${isTotal ? 'ALL' : esc(item.kode_kantor)}</td><td class="mb-sticky-left-2 mb-name ${clickable ? 'mb-clickable' : ''}" ${attrs}>${isTotal ? 'GRAND TOTAL' : esc(rowName(item))}</td><td class="mb-noa">${fmt(item.noa)}</td><td>${cells(item.total_balance_ph)}</td><td class="mb-positive ${clickable ? 'mb-clickable' : ''}" ${attrs}>${cells(item.total_recovery_nominal)}</td><td>${cells(item.sisa_saldo_nominal)}</td><td class="mb-num">${pct(item.persen_rr)}</td><td class="mb-num">${pct(item.persen_lgd)}</td></tr>`;
    };
    el('reportPhTotal').innerHTML = row(total,true);
    el('reportPhBody').innerHTML = rows.length ? rows.map(item => row(item)).join('') : '<tr><td colspan="8" class="mb-empty">Data tidak ditemukan.</td></tr>';
    info(total, 'Rekap LGD', 'Recovery', total.total_recovery_nominal, 'RR', pct(total.persen_rr), 'LGD', pct(total.persen_lgd));
  }
  function render() { state.view === 'recovery' ? renderRecovery() : renderLgd(); }
  function info(total,title,label1,value1,label2,value2,label3,value3) {
    el('reportPhInfoTitle').textContent = title;
    el('reportPhInfoMetric1Label').textContent = label1; el('reportPhInfoMetric1').textContent = 'Rp ' + fmt(value1);
    el('reportPhInfoMetric2Label').textContent = label2; el('reportPhInfoMetric2').textContent = value2;
    el('reportPhInfoMetric3Label').textContent = label3; el('reportPhInfoMetric3').textContent = value3;
  }
  function bindSort() { document.querySelectorAll('#reportPhTable .mb-sort').forEach(th => th.addEventListener('click', () => { const key = th.dataset.sort; state.sort.dir = state.sort.key === key ? -state.sort.dir : 1; state.sort.key = key; render(); })); }
  async function loadDates() {
    try { const response = await fetch(API_DATE); const json = await response.json(); const data = json.data || {}; const end = data.last_created || today(); el('reportPhEnd').value = end; el('reportPhStart').value = monthStart(end); } catch (_) { el('reportPhEnd').value=today(); el('reportPhStart').value=monthStart(today()); }
  }
  async function loadOffices() {
    try {
      const json = await post(API_KODE,{type:'kode_kantor'}); state.offices = Array.isArray(json.data) ? json.data : [];
      const select = el('reportPhArea'); let options = '<option value="ALL">Konsolidasi</option>';
      state.offices.filter(row => String(row.kode_kantor).padStart(3,'0') !== '000').forEach(row => { const kode=String(row.kode_kantor).padStart(3,'0'); options += `<option value="${kode}">${kode} - ${esc(row.nama_kantor)}</option>`; });
      select.innerHTML = options;
      const user = window.getUser?.(); const kode = String(user?.kode_kantor || '').padStart(3,'0');
      if (kode && kode !== '000' && state.offices.some(row => String(row.kode_kantor).padStart(3,'0') === kode)) { select.value=kode; select.disabled=true; }
    } catch (_) {}
  }
  async function load() {
    MonbisUI.showLoading('reportPhLoading',true);
    try {
      const body = state.view === 'recovery' ? {type:'recovery',start_date:el('reportPhStart').value,end_date:el('reportPhEnd').value} : {type:'get lgd',end_date:el('reportPhEnd').value};
      const json = await post(API_PH,body); state.rows = Array.isArray(json.data) ? json.data : []; render();
    } catch (error) { el('reportPhTotal').innerHTML=''; el('reportPhBody').innerHTML=`<tr><td colspan="8" class="mb-empty mb-negative">${esc(error.message || 'Gagal memuat data.')}</td></tr>`; }
    finally { MonbisUI.showLoading('reportPhLoading',false); MonbisUI.closeMobileFilter('reportPhHeaderFilters'); }
  }
  function detailRows() { const q=String(el('reportPhDetailSearch').value || '').toLowerCase(); return state.detail.rows.filter(row => !q || (String(row.no_rekening || '')+' '+String(row.nama_nasabah || '')).toLowerCase().includes(q)); }
  function renderDetail() {
    const rows=detailRows(), pageRows=rows.slice((state.detail.page-1)*state.detail.perPage,state.detail.page*state.detail.perPage), totalPages=Math.max(1,Math.ceil(rows.length/state.detail.perPage));
    const isRecovery=state.detail.type === 'recovery';
    const allRows = state.detail.rows;
    const totalPokok = allRows.reduce((sum,row) => sum + asNum(row.pokok), 0);
    const totalBunga = allRows.reduce((sum,row) => sum + asNum(row.bunga), 0);
    const totalRecovery = allRows.reduce((sum,row) => sum + asNum(row.total || row.total_recovery_nominal), 0);
    const totalSaldo = allRows.reduce((sum,row) => sum + asNum(row.balance_hapus_buku || row.sisa_saldo_nominal), 0);
    const summary = el('reportPhDetailSummary');
    summary.classList.remove('is-hidden');
    summary.innerHTML = isRecovery
      ? `<div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">Total Debitur</div><div class="mb-summary-card__value">${fmt(allRows.length)}</div></div><div class="mb-summary-card"><div class="mb-summary-card__label">Pokok</div><div class="mb-summary-card__value">Rp ${fmt(totalPokok)}</div></div><div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">Bunga</div><div class="mb-summary-card__value">Rp ${fmt(totalBunga)}</div></div><div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">Total Recovery</div><div class="mb-summary-card__value">Rp ${fmt(totalRecovery)}</div></div>`
      : `<div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">Total Debitur</div><div class="mb-summary-card__value">${fmt(allRows.length)}</div></div><div class="mb-summary-card"><div class="mb-summary-card__label">BD Hapus Buku</div><div class="mb-summary-card__value">Rp ${fmt(totalSaldo)}</div></div><div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">Recovery</div><div class="mb-summary-card__value">Rp ${fmt(totalRecovery)}</div></div><div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">Sisa Saldo</div><div class="mb-summary-card__value">Rp ${fmt(allRows.reduce((sum,row) => sum + asNum(row.sisa_saldo_nominal), 0))}</div></div>`;
    const head=isRecovery ? '<tr><th>Rekening</th><th>Nama Nasabah</th><th>Tgl Bayar</th><th class="mb-text-right">Pokok</th><th class="mb-text-right">Bunga</th><th class="mb-text-right">Total</th></tr>' : '<tr><th>Rekening</th><th>Nama Nasabah</th><th class="mb-text-right">BD Hapus Buku</th><th>Tahun PH</th><th class="mb-text-right">Recovery</th><th class="mb-text-right">Sisa Saldo</th><th class="mb-text-right">RR</th><th class="mb-text-right">LGD</th></tr>';
    const body=pageRows.map(row => isRecovery ? `<tr><td class="mb-code">${esc(row.no_rekening)}</td><td class="mb-name" title="${esc(row.nama_nasabah)}">${esc(row.nama_nasabah)}</td><td class="mb-text-center">${displayDate(row.tanggal_transaksi)}</td><td class="mb-text-right">${fmt(row.pokok)}</td><td class="mb-text-right">${fmt(row.bunga)}</td><td class="mb-text-right mb-positive">${fmt(row.total)}</td></tr>` : `<tr><td class="mb-code">${esc(row.no_rekening)}</td><td class="mb-name" title="${esc(row.nama_nasabah)}">${esc(row.nama_nasabah)}</td><td class="mb-text-right">${fmt(row.balance_hapus_buku)}</td><td class="mb-text-center">${esc(row.tahun_ph)}</td><td class="mb-text-right mb-positive">${fmt(row.total_recovery_nominal)}</td><td class="mb-text-right">${fmt(row.sisa_saldo_nominal)}</td><td class="mb-text-right">${pct(row.recovery_rate_npv)}</td><td class="mb-text-right">${pct(row.lgd_persen)}</td></tr>`).join('');
    el('reportPhDetailBody').innerHTML=`<table class="mb-table mb-detail-mini"><thead>${head}</thead><tbody>${body || `<tr><td colspan="8" class="mb-empty">Data tidak ditemukan.</td></tr>`}</tbody></table>`;
    const footer=el('reportPhDetailFooter'); footer.classList.remove('is-hidden'); footer.innerHTML=`<span class="mb-detail-page-info">${rows.length} data | Hal ${state.detail.page}/${totalPages}</span><div class="mb-detail-page-actions"><button class="mb-detail-page-btn" id="reportPhPrev" ${state.detail.page<=1?'disabled':''}>Prev</button><button class="mb-detail-page-btn" id="reportPhNext" ${state.detail.page>=totalPages?'disabled':''}>Next</button></div>`;
    el('reportPhPrev')?.addEventListener('click',()=>{state.detail.page--;renderDetail();}); el('reportPhNext')?.addEventListener('click',()=>{state.detail.page++;renderDetail();});
  }
  async function openDetail(type,kode) {
    const isAll = kode === 'ALL';
    const office=state.offices.find(row => String(row.kode_kantor).padStart(3,'0')===String(kode).padStart(3,'0')); state.detail={rows:[],type,kode,page:1,perPage:25};
    el('reportPhDetailTitle').textContent=(type==='recovery'?'Detail Recovery PH':'Detail LGD')+' - '+(isAll ? 'Konsolidasi' : (office?.nama_kantor || kode)); el('reportPhDetailSubtitle').textContent='Posisi '+displayDate(el('reportPhEnd').value); el('reportPhDetailSearch').value='';
    el('reportPhDetailSummary').classList.add('is-hidden'); el('reportPhDetailSummary').innerHTML='';
    MonbisUI.openModal('reportPhDetail'); el('reportPhDetailBody').innerHTML='<div class="mb-empty">Memuat detail debitur...</div>';
    try { const json=await post(API_PH,type==='recovery'?{type:'debitur',kode_kantor:kode,start_date:el('reportPhStart').value,end_date:el('reportPhEnd').value}:{type:'detail lgd blm lunas',kode_kantor:kode,end_date:el('reportPhEnd').value}); state.detail.rows=Array.isArray(json.data)?json.data:[]; renderDetail(); }
    catch(error) { el('reportPhDetailBody').innerHTML='<div class="mb-empty mb-negative">Gagal mengambil detail.</div>'; }
  }
  function exportTable() { const table=el('reportPhTable').outerHTML; const blob=new Blob(['<html><meta charset="utf-8"><body>'+table+'</body></html>'],{type:'application/vnd.ms-excel'}); const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download=`${state.view === 'recovery' ? 'Recovery_PH' : 'Rekap_LGD'}_${el('reportPhEnd').value}.xls`; a.click(); URL.revokeObjectURL(a.href); }
  function exportDetail() { const table=el('reportPhDetailBody').querySelector('table'); if (!table) return; const blob=new Blob(['<html><meta charset="utf-8"><body>'+table.outerHTML+'</body></html>'],{type:'application/vnd.ms-excel'}); const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download=`Detail_PH_${state.detail.kode}.xls`; a.click(); URL.revokeObjectURL(a.href); }
  async function init() { await Promise.all([loadDates(),loadOffices()]); await load(); }
  el('reportPhRecoveryTab').addEventListener('click',()=>setView('recovery')); el('reportPhLgdTab').addEventListener('click',()=>setView('lgd')); el('reportPhViewSwitch').addEventListener('click',()=>setView(state.view==='recovery'?'lgd':'recovery'));
  ['reportPhStart','reportPhEnd','reportPhArea'].forEach(id=>el(id).addEventListener('change',()=>state.view==='recovery'||id==='reportPhEnd'?load():render()));
  el('reportPhSearch').addEventListener('input',debounce(render,160)); el('reportPhExport').addEventListener('click',exportTable); el('reportPhDetailExport').addEventListener('click',exportDetail);
  el('reportPhDetailSearch').addEventListener('input',debounce(()=>{state.detail.page=1;renderDetail();},150));
  el('reportPhTable').addEventListener('click',event=>{ const target=event.target.closest('[data-detail]'); if(target) openDetail(target.dataset.detail,target.dataset.kode); });
  function start() {
    if (!window.MonbisUI) { window.setTimeout(start, 25); return; }
    init();
  }
  start();
})();
</script>

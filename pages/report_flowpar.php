<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

$flowHead = mb_build_grouped_thead([
    ['label'=>'Kode','class'=>'mb-code-col mb-sticky-left','sort'=>'kode','attrs'=>['style'=>'width:44px']],
    ['label'=>'Kantor','class'=>'mb-sticky-left-2 mb-text-left','sort'=>'kantor','attrs'=>['style'=>'--mb-sticky-1:44px;width:136px']],
    ['label'=>'Jatuh Tempo / Lainnya','class'=>'mb-group mb-group--blue','sort'=>'nom_jt_lain'],
    ['label'=>'DPD Pokok > 90','class'=>'mb-group mb-group--violet','sort'=>'nom_pokok_90'],
    ['label'=>'DPD Bunga > 90','class'=>'mb-group mb-group--amber','sort'=>'nom_bunga_90'],
    ['label'=>'DPD Pokok + Bunga > 90','class'=>'mb-group mb-group--red','sort'=>'nom_pokok_bunga_90'],
    ['label'=>'Total Flow','class'=>'mb-group mb-group--cyan','sort'=>'baki_debet_flow'],
]);

mb_render_report_page([
    'id'=>'reportFlowPage',
    'class'=>'mb-report-flowpar',
    'header'=>[
        'id'=>'reportFlowHeader',
        'title'=>'Flow PAR',
        'subtitle'=>'Monitoring debitur yang berpindah ke kolektibilitas KL berdasarkan penyebab flow.',
        'icon'=>mb_svg('chart'),
        'info_modal_id'=>'reportFlowInfo',
        'filters'=>[
            ['id'=>'reportFlowClosing','label'=>'Closing (M-1)','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
            ['id'=>'reportFlowActual','label'=>'Actual (Harian)','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
            ['id'=>'reportFlowArea','label'=>'Kantor','type'=>'select','width'=>'260px','options'=>['ALL'=>'Konsolidasi']],
        ],
    ],
    'toolbar'=>[
        'title'=>'Rekap Flow PAR',
        'search'=>['id'=>'reportFlowSearch','placeholder'=>'Cari kode / kantor...'],
        'actions'=>[
            ['attrs'=>['id'=>'reportFlowExport'],'tone'=>'success','icon'=>'download','title'=>'Export Excel','aria_label'=>'Export Excel'],
        ],
    ],
    'table'=>[
        'wrapper_id'=>'reportFlowTableWrap',
        'table_id'=>'reportFlowTable',
        'loading_id'=>'reportFlowLoading',
        'loading_text'=>'Memuat data Flow PAR...',
        'class'=>'mb-flowpar-table',
        'colgroup_html'=>'<col style="width:44px"><col style="width:136px"><col style="width:128px"><col style="width:128px"><col style="width:128px"><col style="width:128px"><col style="width:138px">',
        'thead_html'=>$flowHead,
        'tbody_ids'=>['reportFlowTotal','reportFlowBody'],
    ],
]);

mb_render_info_modal([
    'id'=>'reportFlowInfo',
    'title'=>'Panduan Flow PAR',
    'subtitle'=>'Penyebab rekening masuk kolektibilitas KL.',
    'body_html'=>'
      <div class="mb-npl-brief">
        <div class="mb-npl-brief__alert"><strong>Flow PAR menunjukkan rekening yang memburuk menjadi KL pada posisi actual.</strong><span>Klik nominal atau NOA untuk melihat rekening pembentuk setiap klasifikasi.</span></div>
        <div class="mb-npl-brief__section">
          <div class="mb-npl-brief__section-title">Cara Membaca</div>
          <div class="mb-npl-brief__priority-grid">
            <div class="mb-npl-brief__priority mb-npl-brief__priority--blue"><b>1</b><div><strong>Jatuh Tempo / Lainnya</strong><span>Flow karena jatuh tempo, one obligor, atau penyebab selain DPD pokok dan bunga.</span></div></div>
            <div class="mb-npl-brief__priority mb-npl-brief__priority--red"><b>2</b><div><strong>DPD di atas 90 hari</strong><span>Bedakan penyebab pokok, bunga, atau keduanya untuk menentukan prioritas penagihan.</span></div></div>
            <div class="mb-npl-brief__priority mb-npl-brief__priority--violet"><b>3</b><div><strong>Total Flow</strong><span>Jumlah seluruh rekening Flow PAR dan baki debetnya pada kantor terpilih.</span></div></div>
          </div>
        </div>
        <div class="mb-info-warning"><span>Catatan:</span><div>NOA ditampilkan di bawah nominal agar tabel tetap nyaman pada desktop maupun mobile.</div></div>
      </div>',
]);

mb_render_detail_modal([
    'id'=>'reportFlowDetail',
    'title'=>'Detail Debitur Flow PAR',
    'subtitle'=>'Daftar rekening pembentuk Flow PAR',
    'size'=>'xl',
    'mobile_body_id'=>'reportFlowDetailMobile',
    'search_near_close'=>true,
    'collapsible_filters'=>true,
    'toolbar_id'=>'reportFlowDetailFilters',
    'search'=>['id'=>'reportFlowDetailSearch','placeholder'=>'Cari rekening / nasabah...'],
    'filters'=>[
        ['id'=>'reportFlowDetailCommitment','label'=>'Komitmen','type'=>'select','width'=>'145px','options'=>[
            'ALL'=>'Semua Komitmen','SUDAH'=>'Sudah Komitmen','BELUM'=>'Belum Komitmen',
        ]],
        ['id'=>'reportFlowDetailKankas','label'=>'Kankas','type'=>'select','width'=>'165px','options'=>[''=>'Semua Kankas']],
    ],
]);
?>

<script>
(() => {
  'use strict';
  const API_FLOW='./api/flow_par/', API_DATE='./api/date/', API_KODE='./api/kode/';
  const ICON_EDIT=<?= json_encode(mb_svg('edit'), JSON_UNESCAPED_SLASHES) ?>;
  const ICON_DOWNLOAD=<?= json_encode(mb_svg('download'), JSON_UNESCAPED_SLASHES) ?>;
  const KORWIL=[['SEMARANG','Korwil Semarang'],['SOLO','Korwil Solo'],['BANYUMAS','Korwil Banyumas'],['PEKALONGAN','Korwil Pekalongan']];
  const CLASS_LABELS={jt_lain:'Jatuh Tempo / Lainnya',pokok_90:'DPD Pokok > 90',bunga_90:'DPD Bunga > 90',pokok_bunga_90:'DPD Pokok + Bunga > 90','':'Total Flow'};
  const METRICS=[
    ['noa_jt_lain','nom_jt_lain','jt_lain','blue'],['noa_pokok_90','nom_pokok_90','pokok_90','violet'],
    ['noa_bunga_90','nom_bunga_90','bunga_90','amber'],['noa_pokok_bunga_90','nom_pokok_bunga_90','pokok_bunga_90','red'],
    ['noa_flow','baki_debet_flow','','cyan']
  ];
  const state={rows:[],total:null,kantor:[],userKode:'000',sort:{column:'kode',direction:'asc'},abort:null,detail:[],detailKode:'000',detailKorwil:'',detailClass:'',detailPage:1,detailSize:20};
  const el=id=>document.getElementById(id),ui=()=>window.MonbisUI||{},num=v=>Number(v||0);
  const esc=v=>ui().escape?ui().escape(v):String(v??'');
  const fmt=v=>ui().fmt?ui().fmt(v):new Intl.NumberFormat('id-ID').format(num(v));
  const fmtDate=value=>{if(!value)return '-';const d=new Date(String(value).length===10?value+'T00:00:00':value);return Number.isNaN(d.getTime())?'-':String(d.getDate()).padStart(2,'0')+'/'+String(d.getMonth()+1).padStart(2,'0')+'/'+d.getFullYear()};
  async function post(url,body,signal){const response=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body||{}),signal});const text=await response.text();let json={};try{json=text?JSON.parse(text):{}}catch(error){throw new Error('Response API bukan JSON')};if(!response.ok||Number(json.status)!==200)throw new Error(json.message||('HTTP '+response.status));return json}
  function readUser(){if(window.getUser?.())return window.getUser();for(const key of ['dpk_user','app_user','user']){try{const value=JSON.parse(localStorage.getItem(key)||'null');if(value)return value}catch(error){}}return null}
  function userKode(){const user=readUser()||{};const raw=user.kode_kantor||user.kode||user.kode_cabang||'000';const code=String(raw).padStart(3,'0');return code==='099'?'000':code}
  function canDev(user){const fields=[user?.job_position,user?.unit_kerja,user?.branch_name,user?.role].map(v=>String(v||'').toLowerCase());return fields.some(v=>v.includes('divisi operasional'))||fields.includes('dev')}
  async function ensureAccess(){for(let i=0;i<15;i++){const user=readUser();if(user){if(canDev(user))return true;el('reportFlowPage').innerHTML='<section class="mb-report-card mb-dev-denied"><strong>Akses khusus Divisi Operasional</strong><span>Report Flow PAR berada pada menu Dev Report.</span></section>';return false}await new Promise(r=>setTimeout(r,200))}return true}
  const rowCode=row=>String(row?.kode_cabang||row?.kode_unit||'').padStart(3,'0');
  const rowName=row=>row?.nama_kantor||row?.nama_unit||'-';
  function selectedScope(){const value=el('reportFlowArea').value||'ALL';return value.startsWith('KOR-')?{korwil:value.slice(4)}:value.startsWith('CAB-')?{kode_kantor:value.slice(4)}:{}}
  async function loadDates(){try{const json=await (await fetch(API_DATE)).json();el('reportFlowClosing').value=json.data?.last_closing||'';el('reportFlowActual').value=json.data?.last_created||''}catch(error){const today=new Date().toISOString().slice(0,10);el('reportFlowClosing').value=today;el('reportFlowActual').value=today}}
  async function loadKantor(){try{const json=await post(API_KODE,{type:'kode_kantor'});state.kantor=Array.isArray(json.data)?json.data:[]}catch(error){state.kantor=[]}const select=el('reportFlowArea');if(state.userKode!=='000'){const found=state.kantor.find(x=>String(x.kode_kantor).padStart(3,'0')===state.userKode);select.innerHTML='<option value="CAB-'+state.userKode+'">'+state.userKode+' - '+esc(found?.nama_kantor||'Cabang')+'</option>';select.disabled=true;return}let html='<option value="ALL">Konsolidasi</option>';KORWIL.forEach(x=>html+='<option value="KOR-'+x[0]+'">'+x[1]+'</option>');state.kantor.filter(x=>String(x.kode_kantor).padStart(3,'0')!=='000').sort((a,b)=>String(a.kode_kantor).localeCompare(String(b.kode_kantor),'id-ID',{numeric:true})).forEach(x=>{const code=String(x.kode_kantor).padStart(3,'0');html+='<option value="CAB-'+code+'">'+code+' - '+esc(x.nama_kantor||'Cabang')+'</option>'});select.innerHTML=html}
  function metric(row,noaKey,nomKey,code,kind,tone){const noa=num(row?.[noaKey]),nom=num(row?.[nomKey]);const inner='<span class="mb-potensi-metric__value">'+fmt(nom)+'</span><span class="mb-subvalue">'+fmt(noa)+' NOA</span>';return noa>0?'<button type="button" class="mb-potensi-metric mb-potensi-metric--'+tone+'" data-detail-code="'+esc(code)+'" data-detail-kind="'+esc(kind)+'">'+inner+'</button>':'<div class="mb-potensi-metric mb-potensi-metric--'+tone+'">'+inner+'</div>'}
  function rowHtml(row,total=false){const code=total?'ALL':rowCode(row),name=total?'GRAND TOTAL':rowName(row),target=total?detailTarget():code;return '<tr'+(total?' class="mb-total-row"':'')+'><td class="mb-code-col mb-sticky-left mb-code">'+code+'</td><td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:44px" title="'+esc(name)+'">'+esc(name)+'<span class="mb-potensi-mobile-code">'+code+'</span></td>'+METRICS.map(m=>'<td class="mb-num">'+metric(row,m[0],m[1],target,m[2],m[3])+'</td>').join('')+'</tr>'}
  function detailTarget(){const scope=selectedScope();return scope.kode_kantor||'000'}
  function sortValue(row,column){if(column==='kode')return rowCode(row);if(column==='kantor')return rowName(row);return row?.[column]}
  function visibleRows(){const q=String(el('reportFlowSearch').value||'').trim().toLowerCase(),dir=state.sort.direction==='asc'?1:-1;return state.rows.filter(r=>!q||(rowCode(r)+' '+rowName(r)).toLowerCase().includes(q)).sort((a,b)=>['kode','kantor'].includes(state.sort.column)?String(sortValue(a,state.sort.column)||'').localeCompare(String(sortValue(b,state.sort.column)||''),'id-ID',{numeric:true})*dir:(num(sortValue(a,state.sort.column))-num(sortValue(b,state.sort.column)))*dir)}
  function updateSort(){document.querySelectorAll('#reportFlowTable .mb-sort').forEach(th=>{const icon=th.querySelector('.mb-sort-icon');if(!icon)return;const active=th.dataset.sort===state.sort.column;icon.classList.toggle('is-active',active);icon.classList.toggle('is-desc',active&&state.sort.direction==='desc')})}
  function render(){const rows=visibleRows();el('reportFlowTotal').innerHTML=state.total?rowHtml(state.total,true):'';el('reportFlowBody').innerHTML=rows.length?rows.map(r=>rowHtml(r)).join(''):'<tr><td colspan="7" class="mb-empty">Data Flow PAR tidak ditemukan.</td></tr>';updateSort()}
  async function loadMain(){if(state.abort)state.abort.abort();state.abort=new AbortController();ui().showLoading?ui().showLoading('reportFlowLoading',true):el('reportFlowLoading').classList.remove('is-hidden');el('reportFlowBody').innerHTML='';try{const payload={type:'Flow Par',closing_date:el('reportFlowClosing').value,harian_date:el('reportFlowActual').value,...selectedScope()};const json=await post(API_FLOW,payload,state.abort.signal);state.rows=Array.isArray(json.data?.data)?json.data.data:(Array.isArray(json.data)?json.data:[]);state.total=json.data?.grand_total||null;render();requestAnimationFrame(()=>{if(el('reportFlowTableWrap'))el('reportFlowTableWrap').scrollLeft=0});ui().closeMobileFilter?.('reportFlowHeaderFilters')}catch(error){if(error.name!=='AbortError'){console.error(error);el('reportFlowBody').innerHTML='<tr><td colspan="7" class="mb-empty mb-negative">Gagal memuat data Flow PAR.</td></tr>'}}finally{ui().showLoading?ui().showLoading('reportFlowLoading',false):el('reportFlowLoading').classList.add('is-hidden')}}
  function hasCommitment(row){const text=String(row?.komitmen||'').trim().toLowerCase();return (!!text&&text!=='-'&&!['belum ada','belum komitmen','tidak ada'].includes(text))||!!row?.tgl_pembayaran||num(row?.nominal)>0}
  function filteredDetail(){const q=String(el('reportFlowDetailSearch').value||'').trim().toLowerCase(),status=el('reportFlowDetailCommitment').value;return state.detail.filter(row=>{const committed=hasCommitment(row);if(status==='SUDAH'&&!committed)return false;if(status==='BELUM'&&committed)return false;return !q||[row.no_rekening,row.nama_nasabah,row.alamat,row.kolek_harian,row.komitmen].join(' ').toLowerCase().includes(q)})}
  function detailSummary(rows){const committed=rows.filter(hasCommitment),promise=rows.reduce((s,r)=>s+num(r.nominal),0),baki=rows.reduce((s,r)=>s+num(r.baki_debet),0);const box=el('reportFlowDetailSummary');box.classList.remove('is-hidden');box.innerHTML='<div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">Total Debitur</div><div class="mb-summary-card__value">'+fmt(rows.length)+' NOA</div></div><div class="mb-summary-card mb-summary-card--red"><div class="mb-summary-card__label">Total Baki Debet</div><div class="mb-summary-card__value">Rp '+fmt(baki)+'</div></div><div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">Sudah Komitmen</div><div class="mb-summary-card__value">'+fmt(committed.length)+' NOA</div></div><div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">Total Nominal Janji</div><div class="mb-summary-card__value">Rp '+fmt(promise)+'</div></div>'}
  function detailTable(rows,all){const totals=all.reduce((sum,r)=>{const tung=num(r.total_tunggakan)||num(r.tunggakan_pokok)+num(r.tunggakan_bunga);sum.baki+=num(r.baki_debet);sum.tunggakan+=tung;sum.tabungan+=num(r.saldo_akhir);sum.promise+=num(r.nominal);return sum},{baki:0,tunggakan:0,tabungan:0,promise:0});const body=rows.map(r=>{const tung=num(r.total_tunggakan)||num(r.tunggakan_pokok)+num(r.tunggakan_bunga),committed=hasCommitment(r);return '<tr><td class="mb-sticky-left mb-code" style="width:120px">'+esc(r.no_rekening||'-')+'</td><td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:120px;width:170px" title="'+esc(r.nama_nasabah||'-')+'">'+esc(r.nama_nasabah||'-')+'<span class="mb-subvalue mb-detail-address" title="'+esc(r.alamat||'-')+'">'+esc(r.alamat||'-')+'</span></td><td class="mb-text-center">'+esc(r.kolek_closing||'-')+' → '+esc(r.kolek_harian||r.kolektibilitas||'-')+'</td><td class="mb-num">'+fmt(r.baki_debet)+'</td><td class="mb-num mb-negative">'+fmt(r.tunggakan_pokok)+'<span class="mb-subvalue">B: '+fmt(r.tunggakan_bunga)+' · Total: '+fmt(tung)+'</span></td><td class="mb-num mb-positive">'+fmt(r.saldo_akhir)+'</td><td class="mb-text-center">'+fmtDate(r.tgl_jatuh_tempo)+'</td><td class="mb-text-center">'+fmt(r.hari_menunggak)+'<span class="mb-subvalue">P '+fmt(r.hari_menunggak_pokok)+' · B '+fmt(r.hari_menunggak_bunga)+'</span></td><td class="mb-num">'+fmt(r.angsuran_pokok)+'<span class="mb-subvalue">B: '+fmt(r.angsuran_bunga)+'</span></td><td class="mb-text-center"><span class="mb-pill '+(committed?'mb-pill--success':'mb-pill--amber')+'">'+(committed?'Sudah':'Belum')+'</span></td><td title="'+esc(r.komitmen||'-')+'">'+esc(r.komitmen||'-')+'</td><td class="mb-text-center">'+fmtDate(r.tgl_pembayaran)+'</td><td class="mb-num">'+fmt(r.nominal)+'</td><td title="'+esc(r.alasan||'-')+'">'+esc(r.alasan||'-')+'</td></tr>'}).join('');const empty=body||'<tr><td colspan="14" class="mb-empty">Detail tidak ditemukan.</td></tr>';return '<table class="mb-table mb-detail-mini mb-flowpar-detail-table"><thead><tr><th class="mb-sticky-left" style="width:120px">Rekening</th><th class="mb-sticky-left-2 mb-text-left" style="--mb-sticky-1:120px;width:170px">Nasabah / Alamat</th><th>Kolek C → A</th><th>Baki Debet</th><th>Tunggakan P / B</th><th>Saldo Tab</th><th>JT</th><th>DPD / P / B</th><th>Angsuran P / B</th><th>Komitmen</th><th>Rencana</th><th>Tgl Janji</th><th>Nominal Janji</th><th>Alasan</th></tr></thead><tbody><tr class="mb-total-row"><td class="mb-sticky-left" style="width:120px">TOTAL</td><td class="mb-sticky-left-2" style="--mb-sticky-1:120px;width:170px">'+fmt(all.length)+' Debitur</td><td></td><td class="mb-num">'+fmt(totals.baki)+'</td><td class="mb-num mb-negative">'+fmt(totals.tunggakan)+'</td><td class="mb-num mb-positive">'+fmt(totals.tabungan)+'</td><td colspan="6"></td><td class="mb-num">'+fmt(totals.promise)+'</td><td></td></tr>'+empty+'</tbody></table>'}
  function detailCards(rows){return rows.length?rows.map(r=>{const tung=num(r.total_tunggakan)||num(r.tunggakan_pokok)+num(r.tunggakan_bunga),committed=hasCommitment(r);return '<article class="mb-detail-card"><div class="mb-detail-card__head"><div class="mb-detail-card__identity"><div class="mb-detail-card__name">'+esc(r.nama_nasabah||'-')+'</div><div class="mb-detail-card__meta">'+esc(r.no_rekening||'-')+' · '+esc(r.nama_kankas||'-')+'</div></div><span class="mb-pill '+(committed?'mb-pill--success':'mb-pill--amber')+'">'+(committed?'Sudah':'Belum')+'</span></div><div class="mb-detail-card__address"><span>Alamat</span><strong>'+esc(r.alamat||'-')+'</strong></div><div class="mb-detail-card__metrics"><div class="mb-detail-card__metric"><span>Baki Debet</span><strong>Rp '+fmt(r.baki_debet)+'</strong></div><div class="mb-detail-card__metric"><span>Tunggakan P / B</span><strong class="mb-negative">'+fmt(r.tunggakan_pokok)+' / '+fmt(r.tunggakan_bunga)+'</strong></div><div class="mb-detail-card__metric"><span>Total Tunggakan</span><strong class="mb-negative">Rp '+fmt(tung)+'</strong></div><div class="mb-detail-card__metric"><span>Saldo Tabungan</span><strong class="mb-positive">Rp '+fmt(r.saldo_akhir)+'</strong></div><div class="mb-detail-card__metric"><span>Kolek Closing → Actual</span><strong>'+esc(r.kolek_closing||'-')+' → '+esc(r.kolek_harian||'-')+'</strong></div><div class="mb-detail-card__metric"><span>DPD / Pokok / Bunga</span><strong>'+fmt(r.hari_menunggak)+' / '+fmt(r.hari_menunggak_pokok)+' / '+fmt(r.hari_menunggak_bunga)+'</strong></div><div class="mb-detail-card__metric"><span>Jatuh Tempo</span><strong>'+fmtDate(r.tgl_jatuh_tempo)+'</strong></div><div class="mb-detail-card__metric"><span>Angsuran P / B</span><strong>'+fmt(r.angsuran_pokok)+' / '+fmt(r.angsuran_bunga)+'</strong></div></div><div class="mb-detail-card__foot"><div class="mb-detail-card__note"><span>Status Komitmen</span><strong class="'+(committed?'mb-positive':'mb-amber')+'">'+(committed?'Sudah':'Belum')+'</strong></div><div class="mb-detail-card__note"><span>Nominal Janji</span><strong>Rp '+fmt(r.nominal)+'</strong></div><div class="mb-detail-card__note"><span>Tanggal Janji</span><strong>'+fmtDate(r.tgl_pembayaran)+'</strong></div><div class="mb-detail-card__note"><span>Rencana / Alasan</span><strong title="'+esc((r.komitmen||'-')+' · '+(r.alasan||'-'))+'">'+esc(r.komitmen||r.alasan||'-')+'</strong></div></div></article>'}).join(''):'<div class="mb-empty">Detail tidak ditemukan pada filter ini.</div>'}
  function renderDetail(){const all=filteredDetail(),pages=Math.max(1,Math.ceil(all.length/state.detailSize));state.detailPage=Math.min(Math.max(1,state.detailPage),pages);const start=(state.detailPage-1)*state.detailSize,rows=all.slice(start,start+state.detailSize);detailSummary(all);el('reportFlowDetailBody').innerHTML=detailTable(rows,all);el('reportFlowDetailMobile').innerHTML=detailCards(rows);const footer=el('reportFlowDetailFooter');if(!all.length){footer.classList.add('is-hidden');footer.innerHTML='';return}footer.classList.remove('is-hidden');footer.innerHTML='<div class="mb-detail-page-info">Data '+fmt(start+1)+' - '+fmt(Math.min(start+state.detailSize,all.length))+' dari '+fmt(all.length)+'</div><div class="mb-detail-page-actions"><span class="mb-detail-footer-actions"><button type="button" class="mb-detail-footer-icon mb-detail-footer-icon--edit" id="reportFlowDetailUpdate" title="Edit komitmen" aria-label="Edit komitmen">'+ICON_EDIT+'</button><button type="button" class="mb-detail-footer-icon mb-detail-footer-icon--export" id="reportFlowDetailExport" title="Export detail" aria-label="Export detail">'+ICON_DOWNLOAD+'</button></span><span class="mb-detail-pagination"><button type="button" class="mb-detail-page-btn" data-page="prev" '+(state.detailPage<=1?'disabled':'')+'>&lsaquo; Prev</button><span class="mb-detail-page-status">Hal '+state.detailPage+' / '+pages+'</span><button type="button" class="mb-detail-page-btn" data-page="next" '+(state.detailPage>=pages?'disabled':'')+'>Next &rsaquo;</button></span></div>';el('reportFlowDetailUpdate')?.addEventListener('click',gotoUpdate);el('reportFlowDetailExport')?.addEventListener('click',()=>exportExcel(filteredDetail(),true))}
  async function loadDetailOptions(){const field=el('reportFlowDetailKankas')?.closest('.mb-field');if(state.detailKode==='000'){field?.classList.add('is-hidden');return}field?.classList.remove('is-hidden');try{const json=await post(API_KODE,{type:'kode_kankas',kode_kantor:state.detailKode});el('reportFlowDetailKankas').innerHTML='<option value="">Semua Kankas</option>'+((json.data||[]).map(x=>'<option value="'+esc(x.kode_group1)+'">'+esc(x.kode_group1)+' - '+esc(x.deskripsi_group1||'Kankas')+'</option>').join(''))}catch(error){el('reportFlowDetailKankas').innerHTML='<option value="">Semua Kankas</option>'}}
  async function fetchDetail(){el('reportFlowDetailBody').innerHTML='<div class="mb-empty">Memuat detail debitur...</div>';el('reportFlowDetailMobile').innerHTML='<div class="mb-empty">Memuat detail debitur...</div>';try{const json=await post(API_FLOW,{type:'KL Baru',kode_kantor:state.detailKode==='000'?'':state.detailKode,korwil:state.detailKorwil,kode_kankas:el('reportFlowDetailKankas').value||'',closing_date:el('reportFlowClosing').value,harian_date:el('reportFlowActual').value,klasifikasi_flow:state.detailClass});state.detail=Array.isArray(json.data)?json.data:[];state.detailPage=1;renderDetail()}catch(error){console.error(error);state.detail=[];el('reportFlowDetailBody').innerHTML='<div class="mb-empty mb-negative">Gagal memuat detail Flow PAR.</div>';el('reportFlowDetailMobile').innerHTML='<div class="mb-empty mb-negative">Gagal memuat detail Flow PAR.</div>';el('reportFlowDetailSummary').classList.add('is-hidden');el('reportFlowDetailFooter').classList.add('is-hidden')}}
  async function openDetail(code,kind){const scope=selectedScope();state.detailKode=code==='000'?'000':String(code).slice(0,3);state.detailKorwil=state.detailKode==='000'?(scope.korwil||''):'';state.detailClass=kind||'';el('reportFlowDetailSearch').value='';el('reportFlowDetailCommitment').value='ALL';el('reportFlowDetailTitle').textContent='Detail Flow PAR - '+(state.detailKorwil?'Korwil '+state.detailKorwil:(state.detailKode==='000'?'Konsolidasi':state.detailKode));el('reportFlowDetailSubtitle').textContent=(CLASS_LABELS[state.detailClass]||'Total Flow')+' • '+fmtDate(el('reportFlowClosing').value)+' vs '+fmtDate(el('reportFlowActual').value);ui().closeMobileFilter?.('reportFlowDetailFilters');ui().openModal('reportFlowDetail');await loadDetailOptions();fetchDetail()}
  function gotoUpdate(){sessionStorage.setItem('flowpar_update',JSON.stringify({kode_kantor:state.detailKode==='000'?'':state.detailKode,kode_kankas:el('reportFlowDetailKankas')?.value||'',korwil:state.detailKorwil,klasifikasi_flow:state.detailClass,closing_date:el('reportFlowClosing').value,harian_date:el('reportFlowActual').value}));window.location.href='./update_flowpar'}
  function exportExcel(rows,detail=false){if(!rows.length)return;let table;if(detail){table=detailTable(rows,rows)}else{const all=state.total?[{...state.total,kode_cabang:'ALL',nama_kantor:'GRAND TOTAL'},...rows]:rows;table='<table border="1"><thead><tr><th>Kode</th><th>Kantor</th><th>NOA JT/Lain</th><th>Nom JT/Lain</th><th>NOA Pokok 90</th><th>Nom Pokok 90</th><th>NOA Bunga 90</th><th>Nom Bunga 90</th><th>NOA Pokok+Bunga</th><th>Nom Pokok+Bunga</th><th>Total NOA</th><th>Total Baki</th></tr></thead><tbody>'+all.map(r=>'<tr><td>'+esc(r.kode_cabang||'')+'</td><td>'+esc(r.nama_kantor||'')+'</td><td>'+num(r.noa_jt_lain)+'</td><td>'+num(r.nom_jt_lain)+'</td><td>'+num(r.noa_pokok_90)+'</td><td>'+num(r.nom_pokok_90)+'</td><td>'+num(r.noa_bunga_90)+'</td><td>'+num(r.nom_bunga_90)+'</td><td>'+num(r.noa_pokok_bunga_90)+'</td><td>'+num(r.nom_pokok_bunga_90)+'</td><td>'+num(r.noa_flow)+'</td><td>'+num(r.baki_debet_flow)+'</td></tr>').join('')+'</tbody></table>'}const blob=new Blob(['\ufeff'+table],{type:'application/vnd.ms-excel;charset=utf-8'}),a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=(detail?'Detail_':'')+'Flow_PAR_'+(el('reportFlowActual').value||'data')+'.xls';document.body.appendChild(a);a.click();a.remove();setTimeout(()=>URL.revokeObjectURL(a.href),1000)}
  function bind(){['reportFlowClosing','reportFlowActual','reportFlowArea'].forEach(id=>el(id)?.addEventListener('change',()=>{state.sort={column:'kode',direction:'asc'};loadMain()}));el('reportFlowSearch')?.addEventListener('input',ui().debounce?ui().debounce(render,160):render);el('reportFlowExport')?.addEventListener('click',()=>exportExcel(visibleRows()));el('reportFlowTable')?.addEventListener('click',event=>{const detail=event.target.closest('[data-detail-code]');if(detail)openDetail(detail.dataset.detailCode,detail.dataset.detailKind);const head=event.target.closest('[data-sort]');if(head){const column=head.dataset.sort;state.sort={column,direction:state.sort.column===column&&state.sort.direction==='asc'?'desc':'asc'};render()}});['reportFlowDetailSearch','reportFlowDetailCommitment'].forEach(id=>el(id)?.addEventListener(id.includes('Search')?'input':'change',()=>{state.detailPage=1;renderDetail()}));el('reportFlowDetailKankas')?.addEventListener('change',fetchDetail);el('reportFlowDetailExport')?.addEventListener('click',()=>exportExcel(filteredDetail(),true));el('reportFlowDetailFooter')?.addEventListener('click',event=>{const page=event.target.closest('[data-page]')?.dataset.page;if(!page)return;state.detailPage+=page==='next'?1:-1;renderDetail()})}
  async function init(){if(!(await ensureAccess()))return;state.userKode=userKode();await Promise.all([loadDates(),loadKantor()]);bind();loadMain()}
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
</script>

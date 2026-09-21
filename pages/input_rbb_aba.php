<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

$monthCols = '<col class="aba-no"><col class="aba-kind"><col class="aba-bank"><col class="aba-account"><col class="aba-cbs">'
    . str_repeat('<col class="aba-month">', 12) . '<col class="aba-total">';
$monthHead = '<tr><th>NO</th><th>JENIS PENEMPATAN</th><th>NAMA BANK</th><th>NO. REKENING ABA</th><th>NO. REKENING CBS</th>'
    . '<th>JAN</th><th>FEB</th><th>MAR</th><th>APR</th><th>MEI</th><th>JUN</th><th>JUL</th><th>AGU</th><th>SEP</th><th>OKT</th><th>NOV</th><th>DES</th><th>TOTAL</th></tr>';
$abaViewButtons = '<div class="mb-segmented mb-rbb-aba-view-buttons" id="inputRbbAbaViewButtons" role="tablist" aria-label="Bagian COA ABA">'
    . '<button type="button" class="mb-segmented__btn is-active" data-aba-target="inputRbbAbaPage">COA ABA</button>'
    . '<button type="button" class="mb-segmented__btn" data-aba-target="inputRbbAbaCkpnSection">CKPN ABA</button>'
    . '<button type="button" class="mb-segmented__btn" data-aba-target="inputRbbAbaInterestSection">Pendapatan Bank Lain</button>'
    . '</div>';

mb_render_report_page([
    'id'=>'inputRbbAbaPage', 'class'=>'mb-report-rbb-aba',
    'header'=>[
        'id'=>'inputRbbAbaHeader', 'title'=>'Input RBB ABA',
        'subtitle'=>'Penempatan ABA, CKPN otomatis 0,5%, dan proyeksi pendapatan bunga.',
        'icon'=>mb_svg('chart'), 'info_modal_id'=>'inputRbbAbaInfo',
        'filters'=>[
            ['id'=>'inputRbbAbaTahun','label'=>'Tahun RBB','type'=>'number','width'=>'115px','value'=>'2027','attrs'=>['min'=>'2020','max'=>'2100','step'=>'1']],
            ['id'=>'inputRbbAbaCabang','label'=>'Kantor / Cabang','type'=>'select','width'=>'230px','options'=>[''=>'Memuat kantor...']],
        ],
    ],
    'toolbar'=>[
        'title'=>'A. COA 104 · Proyeksi Saldo Penempatan Pada Bank Lain', 'title_id'=>'inputRbbAbaTableTitle',
        'leading_html'=>'<span class="mb-rbb-aba-badge">INPUT DETAIL</span>', 'before_html'=>$abaViewButtons,
        'actions'=>[
            ['attrs'=>['id'=>'inputRbbAbaAdd'],'tone'=>'primary','icon'=>'file','label'=>'Tambah Baris','title'=>'Tambah baris ABA','aria_label'=>'Tambah baris ABA'],
            ['attrs'=>['id'=>'inputRbbAbaSave'],'tone'=>'success','icon'=>'save','label'=>'Simpan','title'=>'Simpan input ABA','aria_label'=>'Simpan input ABA'],
        ],
    ],
    'table'=>[
        'wrapper_id'=>'inputRbbAbaPlacementWrap','table_id'=>'inputRbbAbaPlacementTable','loading_id'=>'inputRbbAbaLoading','loading_text'=>'Memuat input ABA...',
        'class'=>'mb-rbb-aba-table', 'colgroup_html'=>$monthCols, 'thead_html'=>$monthHead,
        'tbody_ids'=>['inputRbbAbaPlacementTotal','inputRbbAbaPlacementBody'],
    ],
]);
?>

<div class="mb-report-page mb-report-standard mb-report-rbb-aba-extra-wrap">
<section class="mb-report-card mb-report-card--grow mb-rbb-aba-extra" id="inputRbbAbaCkpnSection">
  <div class="mb-report-toolbar"><div class="mb-report-toolbar__title">B. COA 105 · Proyeksi CKPN Penempatan Pada Bank Lain <small>(otomatis 0,5% x nominal ABA)</small></div></div>
  <?php mb_render_table_shell([
      'wrapper_id'=>'inputRbbAbaCkpnWrap','table_id'=>'inputRbbAbaCkpnTable','loading_id'=>'inputRbbAbaCkpnLoading','loading_text'=>'Menghitung CKPN ABA...',
      'class'=>'mb-rbb-aba-table mb-rbb-aba-readonly', 'colgroup_html'=>$monthCols, 'thead_html'=>$monthHead,
      'tbody_ids'=>['inputRbbAbaCkpnTotal','inputRbbAbaCkpnBody'],
  ]); ?>
</section>

<section class="mb-report-card mb-report-card--grow mb-rbb-aba-extra" id="inputRbbAbaInterestSection">
  <div class="mb-report-toolbar"><div class="mb-report-toolbar__title">C. COA 401010102 · Pendapatan Bunga Penempatan Pada Bank Lain <small>(otomatis: nominal ABA x 1,25% / 12)</small></div></div>
  <?php mb_render_table_shell([
      'wrapper_id'=>'inputRbbAbaInterestWrap','table_id'=>'inputRbbAbaInterestTable','loading_id'=>'inputRbbAbaInterestLoading','loading_text'=>'Memuat proyeksi pendapatan bunga...',
      'class'=>'mb-rbb-aba-table mb-rbb-aba-interest', 'colgroup_html'=>$monthCols, 'thead_html'=>$monthHead,
      'tbody_ids'=>['inputRbbAbaInterestTotal','inputRbbAbaInterestBody'],
  ]); ?>
</section>

<section class="mb-report-card mb-report-card--grow mb-rbb-aba-extra" id="inputRbbAbaHistorySection">
  <div class="mb-report-toolbar"><div class="mb-report-toolbar__title">History Aktual 3 Tahun <small>(bulan yang sama dari acc_history)</small></div></div>
  <?php mb_render_table_shell([
      'wrapper_id'=>'inputRbbAbaHistoryWrap','table_id'=>'inputRbbAbaHistoryTable','loading_id'=>'inputRbbAbaHistoryLoading','loading_text'=>'Memuat history ABA...',
      'class'=>'mb-rbb-aba-history-table', 'thead_id'=>'inputRbbAbaHistoryHead',
      'colgroup_html'=>'<col class="history-month">' . str_repeat('<col class="history-year">', 9),
      'thead_html'=>'<tr><th rowspan="2">BULAN</th><th colspan="3">PENEMPATAN ABA</th><th colspan="3">CKPN ABA</th><th colspan="3">PENDAPATAN BUNGA</th></tr><tr><th>2024</th><th>2025</th><th>2026</th><th>2024</th><th>2025</th><th>2026</th><th>2024</th><th>2025</th><th>2026</th></tr>',
      'tbody_ids'=>['inputRbbAbaHistoryBody'],
  ]); ?>
</section>

</div>

<?php mb_render_info_modal(['id'=>'inputRbbAbaInfo','title'=>'Panduan Input ABA','subtitle'=>'Input ABA menjadi sumber Proyeksi RBB.','body_html'=>'<div class="mb-npl-brief"><div class="mb-npl-brief__alert"><strong>Isi nominal penempatan dan pendapatan bunga per rekening.</strong><span>CKPN tidak perlu diinput karena dihitung otomatis sebesar 0,5% dari nominal ABA setiap bulan.</span></div><div class="mb-info-warning"><span>Approval:</span><div>Simpan input dari halaman ini. Draft, pengajuan, penolakan, dan approval dilakukan dari halaman Proyeksi RBB.</div></div></div>']); ?>

<style>
  #inputRbbAbaPage .mb-report-card, .mb-rbb-aba-extra { overflow:hidden; }
  #inputRbbAbaPage .mb-report-toolbar__leading { min-width:105px; }
  #inputRbbAbaPage .mb-rbb-aba-badge { display:inline-flex; align-items:center; min-height:26px; padding:5px 9px; border:1px solid #bfdbfe; border-radius:8px; color:#1d4ed8; background:#eff6ff; font-size:10px; font-weight:900; white-space:nowrap; }
  #inputRbbAbaPage .mb-rbb-aba-view-buttons { flex-wrap:wrap; }
  #inputRbbAbaPage .mb-rbb-aba-view-buttons .mb-segmented__btn { font-size:10px; white-space:nowrap; }
  .mb-rbb-aba-extra { margin-top:12px; }
  .mb-rbb-aba-extra .mb-report-toolbar { border-bottom:1px solid #e2e8f0; }
  .mb-rbb-aba-extra .mb-report-toolbar__title small { color:#64748b; font-size:10px; font-weight:700; }
  .mb-rbb-aba-table { min-width:1540px; table-layout:fixed; }
  .aba-no { width:45px; } .aba-kind { width:145px; } .aba-bank { width:170px; }
  .aba-account, .aba-cbs { width:145px; } .aba-month { width:88px; } .aba-total { width:115px; }
  .aba-input { width:100%; min-width:0; padding:6px 5px; border:1px solid #d7e1ec; border-radius:5px; color:#17375f; background:#fff; font-size:10px; font-weight:700; }
  .aba-number { text-align:right; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; }
  .aba-input:focus { outline:2px solid #2563eb; border-color:#2563eb; }
  .aba-delete { padding:4px 7px; border:0; border-radius:5px; color:#dc2626; background:#fef2f2; font-size:10px; font-weight:900; cursor:pointer; }
  .aba-delete:hover { background:#fee2e2; }
  .mb-rbb-aba-readonly td { background:#f8fafc; }
  .mb-rbb-aba-readonly .mb-num { color:#b91c1c; font-weight:800; }
  .mb-rbb-aba-interest td { background:#f8fafc; }
  .mb-rbb-aba-interest .mb-num { color:#047857; font-weight:800; }
  .mb-rbb-aba-history-table { min-width:980px; table-layout:fixed; }
  .history-month { width:90px; } .history-year { width:100px; }
  .mb-rbb-aba-history-table th { text-align:center; }
  .mb-rbb-aba-history-table td { text-align:right; }
  .mb-rbb-aba-history-table td:first-child { text-align:left; font-weight:800; }
  @media (max-width:767px) {
    #inputRbbAbaPage .mb-icon-button__label { display:none; }
    #inputRbbAbaPage .mb-report-toolbar__leading { min-width:0; }
    .mb-rbb-aba-extra .mb-report-toolbar__title { font-size:11px; line-height:1.35; }
    .mb-rbb-aba-extra .mb-report-toolbar__title small { display:block; margin-top:2px; }
    .mb-rbb-aba-table { min-width:1320px; }
    .mb-rbb-aba-history-table { min-width:980px; }
    .aba-month { width:76px; }
    .aba-kind { width:130px; } .aba-bank { width:145px; } .aba-account, .aba-cbs { width:125px; }
  }
</style>

<script>
(() => {
  'use strict';
  const API='./api/rbb/', API_KODE='./api/kode/';
  const state={rows:[],plan:null,history:null,offices:[],loading:false};
  const el=id=>document.getElementById(id), ui=()=>window.MonbisUI||{}, esc=value=>ui().escape?ui().escape(value):String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
  const RATE=0.0125, MONTH_RATE=RATE/12, num=value=>Number(value||0), fmt=value=>ui().fmt?ui().fmt(Math.round(num(value))):new Intl.NumberFormat('id-ID').format(Math.round(num(value))), months=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  async function post(body){const response=await fetch(API,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});const raw=await response.text();let json={};try{json=raw?JSON.parse(raw):{}}catch(error){throw new Error('Response API bukan JSON.')}if(!response.ok||Number(json.status)!==200)throw new Error(json.message||('Request gagal ('+response.status+').'));return json.data||{}}
  const year=()=>Number(el('inputRbbAbaTahun').value||2027), branch=()=>el('inputRbbAbaCabang').value;
  const values=(row,key)=>row[key]||{};
  function total(data){return months.reduce((sum,_,index)=>sum+num(data[index+1]),0)}
  function interestValues(row){return Object.fromEntries(months.map((_,i)=>[i+1,Math.round(num(row.values?.[i+1])*MONTH_RATE*100)/100]))}
  function commonCells(row,index){return '<td>'+(index+1)+'</td><td class="font-bold text-left">'+esc(row.jenis_penempatan||'-')+'</td><td class="text-left">'+esc(row.nama_bank||'-')+'</td><td class="text-left">'+esc(row.no_rekening_aba||'-')+'</td><td class="text-left">'+esc(row.no_rekening_cbs||'-')+'</td>'}
  function placementRow(row,index){const data=values(row,'values');return '<tr data-row-id="'+num(row.id)+'"><td>'+(index+1)+'</td><td><input class="aba-input" data-field="jenis_penempatan" value="'+esc(row.jenis_penempatan||'')+'" placeholder="Giro / Tabungan"></td><td><input class="aba-input" data-field="nama_bank" value="'+esc(row.nama_bank||'')+'" placeholder="Nama bank"></td><td><input class="aba-input" data-field="no_rekening_aba" value="'+esc(row.no_rekening_aba||'')+'" placeholder="Nomor ABA"></td><td><input class="aba-input" data-field="no_rekening_cbs" value="'+esc(row.no_rekening_cbs||'')+'" placeholder="Nomor CBS"></td>'+months.map((_,i)=>'<td><input class="aba-input aba-number" type="number" min="0" step="0.01" data-month="'+(i+1)+'" value="'+num(data[i+1])+'"></td>').join('')+'<td class="mb-num font-bold">'+fmt(total(data))+' <button type="button" class="aba-delete" data-delete-row>Hapus</button></td></tr>'}
  function readonlyRow(row,index,data,klass=''){return '<tr class="'+klass+'">'+commonCells(row,index)+months.map((_,i)=>'<td class="mb-num">'+fmt(data[i+1])+'</td>').join('')+'<td class="mb-num font-bold">'+fmt(total(data))+'</td></tr>'}
  function totalRow(rows,key,rate=1){const sums=Array(13).fill(0);rows.forEach(row=>{const data=values(row,key);for(let m=1;m<=12;m++)sums[m]+=Math.round(num(data[m])*rate*100)/100});return '<tr class="mb-total-row"><td>-</td><td>GRAND TOTAL</td><td colspan="3">'+rows.length+' rekening</td>'+sums.slice(1).map(v=>'<td class="mb-num">'+fmt(v)+'</td>').join('')+'<td class="mb-num">'+fmt(sums.slice(1).reduce((a,b)=>a+b,0))+'</td></tr>'}
  function renderHistory(){const history=state.history||{},years=history.years||[2024,2025,2026],body=el('inputRbbAbaHistoryBody'),head=el('inputRbbAbaHistoryHead');if(head)head.innerHTML='<tr><th rowspan="2">BULAN</th><th colspan="3">PENEMPATAN ABA</th><th colspan="3">CKPN ABA</th><th colspan="3">PENDAPATAN BUNGA</th></tr><tr>'+years.map(year=>'<th>'+year+'</th>').join('')+years.map(year=>'<th>'+year+'</th>').join('')+years.map(year=>'<th>'+year+'</th>').join('')+'</tr>';body.innerHTML=months.map((month,index)=>{const m=index+1;return '<tr><td>'+month+'</td>'+years.map(year=>'<td class="mb-num">'+fmt(history.nominal?.[year]?.[m])+'</td>').join('')+years.map(year=>'<td class="mb-num">'+fmt(history.ckpn?.[year]?.[m])+'</td>').join('')+years.map(year=>'<td class="mb-num">'+fmt(history.interest?.[year]?.[m])+'</td>').join('')+'</tr>'}).join('')}
  function render(){const rows=state.rows,empty='<tr><td colspan="18" class="mb-empty">Belum ada rekening. Klik Tambah Baris untuk mulai mengisi.</td></tr>';const nominalBody=el('inputRbbAbaPlacementBody'),nominalTotal=el('inputRbbAbaPlacementTotal'),ckpnBody=el('inputRbbAbaCkpnBody'),ckpnTotal=el('inputRbbAbaCkpnTotal'),interestBody=el('inputRbbAbaInterestBody'),interestTotal=el('inputRbbAbaInterestTotal');if(!rows.length){nominalTotal.innerHTML='';nominalBody.innerHTML=empty;ckpnTotal.innerHTML='';ckpnBody.innerHTML=empty;interestTotal.innerHTML='';interestBody.innerHTML=empty;renderHistory();return}nominalTotal.innerHTML=totalRow(rows,'values');nominalBody.innerHTML=rows.map(placementRow).join('');ckpnTotal.innerHTML=totalRow(rows,'values',0.005);ckpnBody.innerHTML=rows.map((row,index)=>{const data=Object.fromEntries(months.map((_,i)=>[i+1,Math.round(num(row.values?.[i+1])*0.005*100)/100]));return readonlyRow(row,index,data,'mb-rbb-aba-ckpn-row')}).join('');const formulaRows=rows.map(row=>({...row,interest_values:interestValues(row)}));interestTotal.innerHTML=totalRow(formulaRows,'interest_values');interestBody.innerHTML=formulaRows.map((row,index)=>{const data=values(row,'interest_values');return '<tr>'+commonCells(row,index)+months.map((_,i)=>'<td class="mb-num">'+fmt(data[i+1])+'</td>').join('')+'<td class="mb-num font-bold">'+fmt(total(data))+'</td></tr>'}).join('');renderHistory()}
  async function loadOffices(){const response=await fetch(API_KODE,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type:'kode_kantor'})}),json=await response.json(),rows=Array.isArray(json.data)?json.data:(json.data?.data||[]);state.offices=rows.filter(row=>String(row.kode_kantor)!=='000');el('inputRbbAbaCabang').innerHTML=state.offices.map(row=>'<option value="'+esc(String(row.kode_kantor).padStart(3,'0'))+'">'+esc(String(row.kode_kantor).padStart(3,'0')+' - '+(row.nama_kantor||'Cabang'))+'</option>').join('')}
  async function load(){if(!branch()||state.loading)return;state.loading=true;el('inputRbbAbaLoading').classList.remove('is-hidden');try{const data=await post({type:'rbb_aba_data',tahun:year(),kode_kantor:branch(),tipe:'PLACEMENT'});state.rows=data.rows||[];state.plan=data.plan||null;state.history=data.history||null;render()}catch(error){const message='<tr><td colspan="18" class="mb-empty mb-negative">'+esc(error.message)+'</td></tr>';el('inputRbbAbaPlacementBody').innerHTML=message;el('inputRbbAbaCkpnBody').innerHTML=message;el('inputRbbAbaInterestBody').innerHTML=message}finally{state.loading=false;el('inputRbbAbaLoading').classList.add('is-hidden')}}
  function collect(){return [...document.querySelectorAll('#inputRbbAbaPlacementBody tr[data-row-id]')].map((tr,index)=>({id:Number(tr.dataset.rowId||0),no_urut:index+1,jenis_penempatan:tr.querySelector('[data-field="jenis_penempatan"]')?.value||'',nama_bank:tr.querySelector('[data-field="nama_bank"]')?.value||'',no_rekening_aba:tr.querySelector('[data-field="no_rekening_aba"]')?.value||'',no_rekening_cbs:tr.querySelector('[data-field="no_rekening_cbs"]')?.value||'',values:Object.fromEntries([...tr.querySelectorAll('[data-month]')].map(input=>[input.dataset.month,Number(input.value||0)]))}))}
  async function save(){if(!branch())return alert('Pilih kantor terlebih dahulu.');try{await post({type:'rbb_aba_save',tahun:year(),kode_kantor:branch(),rows:collect()});alert('Input ABA berhasil disimpan. CKPN dihitung otomatis di Proyeksi RBB.');await load()}catch(error){alert(error.message)}}
  function add(){state.rows.push({id:0,values:Object.fromEntries(months.map((_,i)=>[i+1,0]))});render();document.querySelector('#inputRbbAbaPlacementBody tr:last-child input')?.focus()}
  function bind(){['inputRbbAbaTahun','inputRbbAbaCabang'].forEach(id=>el(id).addEventListener('change',load));document.querySelectorAll('[data-aba-target]').forEach(button=>button.addEventListener('click',()=>{document.querySelectorAll('[data-aba-target]').forEach(item=>{const active=item===button;item.classList.toggle('is-active',active);item.setAttribute('aria-selected',active?'true':'false')});const target=el(button.dataset.abaTarget);if(target)target.scrollIntoView({behavior:'smooth',block:'start'})}));el('inputRbbAbaAdd').addEventListener('click',add);el('inputRbbAbaSave').addEventListener('click',save);el('inputRbbAbaPlacementBody').addEventListener('click',event=>{if(event.target.closest('[data-delete-row]')){event.target.closest('tr').remove();state.rows=collect();render()}})}
  async function init(){bind();try{await loadOffices();await load()}catch(error){el('inputRbbAbaPlacementBody').innerHTML='<tr><td colspan="18" class="mb-empty mb-negative">'+esc(error.message)+'</td></tr>'}}if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
</script>

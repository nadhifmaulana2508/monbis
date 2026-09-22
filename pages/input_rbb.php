<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

$projectionTabs = '<div class="mb-segmented" id="rbbProjectionTabs" role="tablist" aria-label="Bagian Proyeksi RBB">'
    . '<button type="button" class="mb-segmented__btn is-active" data-rbb-projection-category="ASET">' . mb_svg('file') . '<span class="mb-icon-button__label">ABA / Aset</span></button>'
    . '<button type="button" class="mb-segmented__btn" data-rbb-projection-category="KREDIT">' . mb_svg('edit') . '<span class="mb-icon-button__label">Kredit</span></button>'
    . '<button type="button" class="mb-segmented__btn" data-rbb-projection-category="DAMAS">' . mb_svg('chart') . '<span class="mb-icon-button__label">DAMAS</span></button>'
    . '<button type="button" class="mb-segmented__btn" data-rbb-projection-category="PENDAPATAN">' . mb_svg('percent') . '<span class="mb-icon-button__label">Pendapatan</span></button>'
    . '<button type="button" class="mb-segmented__btn" data-rbb-projection-category="BEBAN">' . mb_svg('list') . '<span class="mb-icon-button__label">Beban</span></button>'
    . '</div>';

mb_render_report_page([
    'id'=>'inputRbbPage', 'class'=>'mb-report-rbb-projection',
    'header'=>[
        'id'=>'inputRbbHeader', 'title'=>'Proyeksi RBB',
        'subtitle'=>'Rekap hasil input detail RBB cabang per COA untuk Januari–Desember.',
        'icon'=>mb_svg('chart'), 'info_modal_id'=>'inputRbbInfo',
        'filters'=>[
            ['id'=>'inputRbbTahun','label'=>'Tahun RBB','type'=>'number','width'=>'115px','value'=>'2027','attrs'=>['min'=>'2020','max'=>'2100','step'=>'1']],
            ['id'=>'inputRbbCabang','label'=>'Kantor / Cabang','type'=>'select','width'=>'230px','options'=>[''=>'Memuat kantor...']],
        ],
    ],
    'toolbar'=>[
        'title'=>'Proyeksi RBB · ABA / Aset', 'title_id'=>'inputRbbTableTitle',
        'leading_html'=>'<span id="inputRbbStatus" class="mb-rbb-planning-status">Belum dimuat</span>',
        'before_html'=>$projectionTabs.'<a href="input_rbb_aba?bagian=placement" class="mb-rbb-detail-link">Input Detail ABA</a>',
        'actions'=>[
            ['attrs'=>['id'=>'inputRbbRefresh'],'tone'=>'primary','icon'=>'chart','title'=>'Muat ulang proyeksi','aria_label'=>'Muat ulang proyeksi'],
            ['attrs'=>['id'=>'inputRbbSubmit','class'=>'is-hidden'],'tone'=>'success','icon'=>'save','label'=>'Ajukan Kanwil','title'=>'Ajukan draft ke Kanwil','aria_label'=>'Ajukan draft ke Kanwil'],
            ['attrs'=>['id'=>'inputRbbApproveKanwil','class'=>'is-hidden'],'tone'=>'success','icon'=>'check','label'=>'Approve Kanwil','title'=>'Approve sebagai Kanwil','aria_label'=>'Approve sebagai Kanwil'],
            ['attrs'=>['id'=>'inputRbbApprovePusat','class'=>'is-hidden'],'tone'=>'success','icon'=>'check','label'=>'Approve Pusat','title'=>'Approve sebagai Pusat','aria_label'=>'Approve sebagai Pusat'],
            ['attrs'=>['id'=>'inputRbbReject','class'=>'is-hidden'],'tone'=>'danger','icon'=>'close','label'=>'Tolak','title'=>'Tolak dan kembalikan ke draft','aria_label'=>'Tolak RBB'],
        ],
    ],
    'table'=>[
        'wrapper_id'=>'inputRbbTableWrap','table_id'=>'inputRbbTable','loading_id'=>'inputRbbLoading','loading_text'=>'Memuat proyeksi RBB...',
        'class'=>'mb-rbb-projection-table',
        'colgroup_html'=>'<col class="rbb-proj-code"><col class="rbb-proj-name"><col class="rbb-proj-category"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-month"><col class="rbb-proj-total"><col class="rbb-proj-source">',
        'thead_html'=>'<tr><th>KODE</th><th>COA / INDIKATOR</th><th>BAGIAN</th><th>JAN</th><th>FEB</th><th>MAR</th><th>APR</th><th>MEI</th><th>JUN</th><th>JUL</th><th>AGU</th><th>SEP</th><th>OKT</th><th>NOV</th><th>DES</th><th>TOTAL</th><th>SUMBER</th></tr>',
        'tbody_ids'=>['inputRbbTotal','inputRbbBody'],
    ],
]);

mb_render_info_modal(['id'=>'inputRbbInfo','title'=>'Panduan Proyeksi RBB','subtitle'=>'Halaman ini menampilkan hasil input detail RBB.','body_html'=>'<div class="mb-npl-brief"><div class="mb-npl-brief__alert"><strong>Proyeksi RBB tidak diisi langsung.</strong><span>Gunakan submenu detail seperti Input RBB ABA. Nilai detail akan dijumlahkan otomatis ke COA terkait.</span></div><div class="mb-info-warning"><span>Contoh:</span><div>Total Penempatan ABA masuk ke COA 104 Penempatan Pada Bank Lain, sedangkan total CKPN ABA masuk ke COA 105 Cadangan Kerugian Penurunan Nilai PPBL.</div></div></div>']);
?>

<style>
  #inputRbbPage .mb-report-card { overflow:hidden; }
  #inputRbbPage .mb-report-toolbar__leading { min-width:120px; }
  #inputRbbPage .mb-rbb-planning-status { display:inline-flex; align-items:center; min-height:28px; padding:5px 9px; border:1px solid #dbe5f0; border-radius:8px; color:#64748b; background:#f8fafc; font-size:10px; font-weight:800; white-space:nowrap; }
  #inputRbbPage .mb-rbb-detail-link { display:inline-flex; align-items:center; min-height:28px; padding:0 9px; border:1px solid #bfdbfe; border-radius:7px; color:#1d4ed8; background:#eff6ff; font-size:10px; font-weight:800; white-space:nowrap; }
  #inputRbbPage .mb-rbb-detail-link:hover { background:#dbeafe; }
  #inputRbbPage .mb-icon-button--danger { color:#b91c1c; background:#fef2f2; border-color:#fecaca; }
  #inputRbbPage .mb-rbb-projection-table { min-width:1450px; table-layout:fixed; }
  #inputRbbPage .rbb-proj-code { width:70px; } #inputRbbPage .rbb-proj-name { width:280px; } #inputRbbPage .rbb-proj-category { width:120px; }
  #inputRbbPage .rbb-proj-month { width:88px; } #inputRbbPage .rbb-proj-total { width:115px; } #inputRbbPage .rbb-proj-source { width:100px; }
  #inputRbbPage .mb-rbb-projection-table th { position:sticky; top:0; z-index:3; background:#f1f6fb; color:#17375f; font-size:10px; font-weight:900; }
  #inputRbbPage .mb-rbb-projection-table th:first-child, #inputRbbPage .mb-rbb-projection-table td:first-child { position:sticky; left:0; z-index:2; background:#fff; }
  #inputRbbPage .mb-rbb-projection-table th:nth-child(2), #inputRbbPage .mb-rbb-projection-table td:nth-child(2) { position:sticky; left:70px; z-index:2; background:#fff; box-shadow:8px 0 10px -10px #64748b; }
  #inputRbbPage .mb-rbb-projection-table th:first-child, #inputRbbPage .mb-rbb-projection-table th:nth-child(2) { z-index:4; background:#f1f6fb; }
  #inputRbbPage .mb-rbb-projection-table tr[data-source="INPUT ABA"] td { background:#f0fdf4; }
  #inputRbbPage .mb-rbb-projection-table tr[data-source="INPUT ABA"] td:first-child, #inputRbbPage .mb-rbb-projection-table tr[data-source="INPUT ABA"] td:nth-child(2) { background:#f0fdf4; }
  #inputRbbPage .rbb-proj-source { color:#047857; font-size:9px; font-weight:900; }
  @media (max-width:767px) { #inputRbbPage .mb-icon-button__label { display:none; } #inputRbbPage .mb-rbb-projection-table { min-width:1260px; } #inputRbbPage .rbb-proj-name { width:220px; } #inputRbbPage .rbb-proj-month { width:76px; } }
</style>

<script>
(() => {
  'use strict';
  const API='./api/rbb/', API_KODE='./api/kode/';
  const state={category:'ASET',rows:[],offices:[],plan:null,loading:false};
  const el=id=>document.getElementById(id), ui=()=>window.MonbisUI||{}, esc=value=>ui().escape?ui().escape(value):String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
  const num=value=>Number(value||0), fmt=value=>ui().fmt?ui().fmt(Math.round(num(value))):new Intl.NumberFormat('id-ID').format(Math.round(num(value))), months=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  async function post(body){const response=await fetch(API,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});const raw=await response.text();let json={};try{json=raw?JSON.parse(raw):{}}catch(error){throw new Error('Response API bukan JSON.')}if(!response.ok||Number(json.status)!==200)throw new Error(json.message||('Request gagal ('+response.status+').'));return json.data||{}}
  const year=()=>Number(el('inputRbbTahun').value||2027), branch=()=>el('inputRbbCabang').value;
  function render(){const total=el('inputRbbTotal'),body=el('inputRbbBody');el('inputRbbTableTitle').textContent='Proyeksi RBB · '+({ASET:'ABA / Aset',KREDIT:'Kredit',DAMAS:'DAMAS',PENDAPATAN:'Pendapatan',BEBAN:'Beban'}[state.category]||state.category);const rows=state.rows.filter(row=>state.category==='KREDIT'?['PRODUKSI KREDIT','RUN OFF KREDIT','KREDIT SALDO BANK'].includes(row.kategori):row.kategori===state.category);if(!rows.length){total.innerHTML='';body.innerHTML='<tr><td colspan="17" class="mb-empty">Belum ada hasil input pada bagian ini.</td></tr>';return}const totals=Array(13).fill(0);rows.forEach(row=>{for(let m=1;m<=12;m++)totals[m]+=num(row.values?.[m])});total.innerHTML='<tr class="mb-total-row"><td>-</td><td>GRAND TOTAL</td><td>-</td>'+totals.slice(1).map(v=>'<td class="mb-num">'+fmt(v)+'</td>').join('')+'<td class="mb-num">'+fmt(totals.slice(1).reduce((a,b)=>a+b,0))+'</td><td>-</td></tr>';body.innerHTML=rows.map(row=>{const values=row.values||{},sum=Object.values(values).reduce((a,b)=>a+num(b),0),source=row.source||'INPUT COA';return '<tr data-source="'+esc(source)+'"><td class="font-mono text-blue-700">'+esc(row.kode_monbis)+'</td><td class="font-bold text-left">'+esc(row.keterangan||'-')+'</td><td class="text-left">'+esc(row.kategori||'-')+'</td>'+months.map((_,i)=>'<td class="mb-num">'+fmt(values[i+1])+'</td>').join('')+'<td class="mb-num font-bold">'+fmt(sum)+'</td><td class="rbb-proj-source">'+esc(source)+'</td></tr>'}).join('')}
  async function loadOffices(){const response=await fetch(API_KODE,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type:'kode_kantor'})}),json=await response.json(),rows=Array.isArray(json.data)?json.data:(json.data?.data||[]);state.offices=rows.filter(row=>String(row.kode_kantor)!=='000');el('inputRbbCabang').innerHTML=state.offices.map(row=>'<option value="'+esc(String(row.kode_kantor).padStart(3,'0'))+'">'+esc(String(row.kode_kantor).padStart(3,'0')+' - '+(row.nama_kantor||'Cabang'))+'</option>').join('')}
  async function load(){if(!branch()||state.loading)return;state.loading=true;el('inputRbbLoading').classList.remove('is-hidden');try{const data=await post({type:'rbb_projection_data',tahun:year(),kode_kantor:branch()});state.rows=data.rows||[];state.plan=data.plan||null;el('inputRbbStatus').textContent=state.plan?.status||'Belum ada draft';updateActions();render()}catch(error){el('inputRbbBody').innerHTML='<tr><td colspan="17" class="mb-empty mb-negative">'+esc(error.message)+'</td></tr>';updateActions()}finally{state.loading=false;el('inputRbbLoading').classList.add('is-hidden')}}
  function updateActions(){const status=state.plan?.status||'';['inputRbbSubmit','inputRbbApproveKanwil','inputRbbApprovePusat','inputRbbReject'].forEach(id=>el(id).classList.add('is-hidden'));if(['DRAFT','REJECTED'].includes(status))el('inputRbbSubmit').classList.remove('is-hidden');if(status==='SUBMITTED_KANWIL'){el('inputRbbApproveKanwil').classList.remove('is-hidden');el('inputRbbReject').classList.remove('is-hidden')}if(status==='APPROVED_KANWIL'){el('inputRbbApprovePusat').classList.remove('is-hidden');el('inputRbbReject').classList.remove('is-hidden')}}
  async function transition(type,action){if(!state.plan)return alert('Belum ada draft RBB.');const message=action==='REJECT'?'Tolak draft RBB ini?':type==='submit'?'Ajukan draft RBB ke Kanwil?':action==='APPROVE_KANWIL'?'Approve draft RBB sebagai Kanwil?':'Approve draft RBB sebagai Pusat?';if(!confirm(message))return;try{await post(type==='submit'?{type:'rbb_planning_submit',tahun:year(),kode_kantor:branch()}:{type:'rbb_planning_approve',action,tahun:year(),kode_kantor:branch()});await load();alert('Status Proyeksi RBB berhasil diperbarui.')}catch(error){alert(error.message)}}
  function bind(){document.querySelectorAll('[data-rbb-projection-category]').forEach(button=>button.addEventListener('click',()=>{document.querySelectorAll('[data-rbb-projection-category]').forEach(item=>item.classList.toggle('is-active',item===button));state.category=button.dataset.rbbProjectionCategory;render()}));['inputRbbTahun','inputRbbCabang'].forEach(id=>el(id).addEventListener('change',load));el('inputRbbRefresh').addEventListener('click',load);el('inputRbbSubmit').addEventListener('click',()=>transition('submit','SUBMIT_KANWIL'));el('inputRbbApproveKanwil').addEventListener('click',()=>transition('approve','APPROVE_KANWIL'));el('inputRbbApprovePusat').addEventListener('click',()=>transition('approve','APPROVE_PUSAT'));el('inputRbbReject').addEventListener('click',()=>transition('approve','REJECT'))}
  async function init(){bind();try{await loadOffices();await load()}catch(error){el('inputRbbBody').innerHTML='<tr><td colspan="17" class="mb-empty mb-negative">'+esc(error.message)+'</td></tr>'}}if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
</script>

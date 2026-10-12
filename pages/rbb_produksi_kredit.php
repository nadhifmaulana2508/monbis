<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

$identityOffice = [
    ['label'=>'Kode','class'=>'mb-code-col mb-sticky-left','sort'=>'kode_kantor','attrs'=>['style'=>'width:44px']],
    ['label'=>'Kantor','class'=>'mb-sticky-left-2 mb-text-left','sort'=>'nama_kantor','attrs'=>['style'=>'--mb-sticky-1:44px;width:150px']],
];
$identityMonth = [
    ['label'=>'Bulan','class'=>'mb-sticky-left mb-text-left','sort'=>'periode','attrs'=>['style'=>'width:128px']],
];

$rbbHeads = [
    'rbb_office' => mb_build_grouped_thead(array_merge($identityOffice, [
        ['label'=>'Target RBB','class'=>'mb-group mb-group--violet','sort'=>'nilai_rbb'],
        ['label'=>'Realisasi','class'=>'mb-group mb-group--blue','sort'=>'realisasi_bulan_ini'],
        ['label'=>'% RBB','class'=>'mb-group mb-group--green','sort'=>'persentase_rbb_bulan_ini'],
        ['label'=>'Kurang Lalu','class'=>'mb-group mb-group--red','sort'=>'kekurangan_sd_bulan_lalu'],
        ['label'=>'Total Beban','class'=>'mb-group mb-group--amber','sort'=>'total_beban_target'],
        ['label'=>'% Beban','class'=>'mb-group mb-group--cyan','sort'=>'persentase_rbb_plus_kekurangan'],
    ])),
    'rbb_month' => mb_build_grouped_thead(array_merge($identityMonth, [
        ['label'=>'Target RBB','class'=>'mb-group mb-group--violet','sort'=>'nilai_rbb'],
        ['label'=>'Realisasi','class'=>'mb-group mb-group--blue','sort'=>'realisasi_bulan_ini'],
        ['label'=>'% RBB','class'=>'mb-group mb-group--green','sort'=>'persentase_rbb_bulan_ini'],
        ['label'=>'Selisih','class'=>'mb-group mb-group--cyan','sort'=>'selisih'],
        ['label'=>'Kekurangan','class'=>'mb-group mb-group--amber','sort'=>'kekurangan'],
    ])),
    'history_office' => mb_build_grouped_thead(array_merge($identityOffice, [
        ['label'=>'Realisasi Tahun Ini','class'=>'mb-group mb-group--blue','sort'=>'realisasi_bulan_ini'],
        ['label'=>'Run Off','class'=>'mb-group mb-group--amber','sort'=>'run_off'],
        ['label'=>'Growth','class'=>'mb-group mb-group--green','sort'=>'growth'],
        ['label'=>'% Growth','class'=>'mb-group mb-group--green','sort'=>'growth_persen'],
        ['label'=>'Realisasi Tahun Lalu','class'=>'mb-group mb-group--violet','sort'=>'realisasi_tahun_lalu'],
        ['label'=>'Selisih YoY','class'=>'mb-group mb-group--cyan','sort'=>'selisih'],
        ['label'=>'% YoY','class'=>'mb-group mb-group--cyan','sort'=>'yoy_persen'],
    ])),
    'history_month' => mb_build_grouped_thead(array_merge($identityMonth, [
        ['label'=>'Realisasi Tahun Ini','class'=>'mb-group mb-group--blue','sort'=>'realisasi_bulan_ini'],
        ['label'=>'Run Off','class'=>'mb-group mb-group--amber','sort'=>'run_off'],
        ['label'=>'Growth','class'=>'mb-group mb-group--green','sort'=>'growth'],
        ['label'=>'% Growth','class'=>'mb-group mb-group--green','sort'=>'growth_persen'],
        ['label'=>'Realisasi Tahun Lalu','class'=>'mb-group mb-group--violet','sort'=>'realisasi_tahun_lalu'],
        ['label'=>'Selisih YoY','class'=>'mb-group mb-group--cyan','sort'=>'selisih'],
        ['label'=>'% YoY','class'=>'mb-group mb-group--cyan','sort'=>'yoy_persen'],
    ])),
];

mb_render_report_page([
    'id'=>'reportRbbPage',
    'class'=>'mb-report-rbb',
    'header'=>[
        'id'=>'reportRbbHeader',
        'title'=>'Produksi Kredit vs RBB',
        'subtitle'=>'Monitoring realisasi produksi, pencapaian target RBB, dan pertumbuhan kredit.',
        'info_modal_id'=>'reportRbbInfo',
        'filters'=>[
            ['id'=>'reportRbbActual','label'=>'Actual (Harian)','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
            ['id'=>'reportRbbArea','label'=>'Area / Cabang','type'=>'select','width'=>'260px','options'=>['ALL'=>'Konsolidasi']],
        ],
    ],
    'toolbar'=>[
        'title'=>'Produksi vs RBB',
        'title_id'=>'reportRbbTableTitle',
        'search'=>['id'=>'reportRbbSearch','placeholder'=>'Cari kode / kantor / bulan...'],
        'actions'=>[
            ['attrs'=>['id'=>'reportRbbViewSwitch'],'variant'=>'view-switch','icon'=>'chart','title'=>'Ganti report','aria_label'=>'Ganti report'],
            ['attrs'=>['id'=>'reportRbbExport'],'tone'=>'success','icon'=>'download','title'=>'Export Excel','aria_label'=>'Export Excel'],
        ],
    ],
    'table'=>[
        'wrapper_id'=>'reportRbbTableWrap',
        'table_id'=>'reportRbbTable',
        'loading_id'=>'reportRbbLoading',
        'loading_text'=>'Memuat produksi kredit... ',
        'class'=>'mb-rbb-table',
        'thead_html'=>$rbbHeads['rbb_office'],
        'tbody_ids'=>['reportRbbTotal','reportRbbBody'],
    ],
]);

mb_render_info_modal([
    'id'=>'reportRbbInfo',
    'title'=>'Panduan Produksi Kredit',
    'subtitle'=>'Cara membaca pencapaian RBB dan pertumbuhan produksi.',
    'body_html'=>'<div class="mb-npl-brief">
      <div class="mb-npl-brief__alert"><strong>Dua report tersedia dalam satu tampilan.</strong><span>Gunakan tombol report untuk berpindah antara Produksi vs RBB dan Pertumbuhan / YoY.</span></div>
      <div class="mb-npl-brief__section"><div class="mb-npl-brief__section-title">Cara Membaca</div><div class="mb-npl-brief__priority-grid">
        <div class="mb-npl-brief__priority mb-npl-brief__priority--blue"><b>1</b><div><strong>Produksi vs RBB</strong><span>Bandingkan realisasi dengan target RBB serta beban kekurangan bulan sebelumnya.</span></div></div>
        <div class="mb-npl-brief__priority mb-npl-brief__priority--violet"><b>2</b><div><strong>Pertumbuhan / YoY</strong><span>Bandingkan produksi tahun berjalan dengan run off dan periode tahun sebelumnya.</span></div></div>
        <div class="mb-npl-brief__priority mb-npl-brief__priority--red"><b>3</b><div><strong>Warna Perubahan</strong><span>Nilai positif berwarna hijau, sedangkan nilai negatif berwarna merah.</span></div></div>
      </div></div><div class="mb-info-warning"><span>Catatan:</span><div>Nominal tabel ditampilkan utuh sesuai nilai dari backend. Filter cabang menampilkan breakdown bulanan tahun berjalan.</div></div>
    </div>',
]);
?>

<script>
(() => {
  'use strict';
  const API_RBB='./api/rbb/', API_DATE='./api/date/', API_KODE='./api/kode/';
  const HEADS=<?= json_encode($rbbHeads, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const KORWIL=[['SEMARANG','Korwil Semarang'],['SOLO','Korwil Solo'],['BANYUMAS','Korwil Banyumas'],['PEKALONGAN','Korwil Pekalongan']];
  const state={view:'rbb',rows:[],monthly:[],total:{},meta:{},kantor:[],userKode:'000',sort:{column:'kode_kantor',direction:'asc'},abort:null};
  const el=id=>document.getElementById(id),num=v=>Number(v||0),ui=()=>window.MonbisUI||{};
  const esc=v=>ui().escape?ui().escape(v):String(v??'');
  const fmt=v=>ui().fmt?ui().fmt(Math.round(num(v))):new Intl.NumberFormat('id-ID').format(Math.round(num(v)));
  const fmtNom=v=>fmt(num(v));
  const fmt2=v=>ui().fmt2?ui().fmt2(v):new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(num(v));
  const pct=v=>fmt2(v)+'%';
  const tone=v=>num(v)>=0?'mb-positive':'mb-negative';
  const signed=(v,percent=false)=>{const n=num(v);return '<span class="'+tone(n)+'">'+(n>0?'+':n<0?'−':'')+(percent?pct(Math.abs(n)):fmtNom(Math.abs(n)))+'</span>'};
  const monthLabel=v=>{if(!v)return '-';const d=new Date(v+'T00:00:00');return Number.isNaN(d.getTime())?v:d.toLocaleDateString('id-ID',{month:'long',year:'numeric'})};
  async function post(url,body,signal){const response=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body||{}),signal});const json=await response.json();if(!response.ok||Number(json.status)!==200)throw new Error(json.message||('HTTP '+response.status));return json}
  function readUser(){if(window.getUser?.())return window.getUser();for(const key of ['dpk_user','app_user','user']){try{const value=JSON.parse(localStorage.getItem(key)||'null');if(value)return value}catch(error){}}return {}}
  function currentUserCode(){const user=readUser(),raw=user.kode_kantor||user.kode||user.branch_code||user.kode_cabang||'000',code=String(raw).replace(/\D/g,'').padStart(3,'0').slice(-3);return code==='099'?'000':code}
  function scope(){const value=el('reportRbbArea').value||'ALL';return value.startsWith('KOR-')?{korwil:value.slice(4)}:value.startsWith('CAB-')?{kode_kantor:value.slice(4)}:{}}
  function isBranch(){return !!scope().kode_kantor}
  function source(){return state.monthly.length?state.monthly:state.rows}
  function isMonthly(){return state.monthly.length>0}
  function sum(rows,key){return rows.reduce((total,row)=>total+num(row?.[key]),0)}
  function monthlyTotal(){if(state.view==='history'){const current=sum(state.monthly,'realisasi_bulan_ini'),previous=sum(state.monthly,'realisasi_tahun_lalu'),runOff=sum(state.monthly,'run_off'),growth=sum(state.monthly,'growth'),selisih=current-previous;return {realisasi_bulan_ini:current,realisasi_tahun_lalu:previous,run_off:runOff,growth,growth_persen:runOff?growth/runOff*100:0,selisih,yoy_persen:previous?selisih/previous*100:0}}const target=sum(state.monthly,'nilai_rbb'),actual=sum(state.monthly,'realisasi_bulan_ini'),selisih=actual-target;return {nilai_rbb:target,realisasi_bulan_ini:actual,persentase_rbb_bulan_ini:target?actual/target*100:0,selisih,kekurangan:sum(state.monthly,'kekurangan')}}
  function visibleRows(){const query=String(el('reportRbbSearch').value||'').trim().toLowerCase(),dir=state.sort.direction==='asc'?1:-1;return source().filter(row=>!query||[row.kode_kantor,row.nama_kantor,monthLabel(row.periode)].join(' ').toLowerCase().includes(query)).sort((a,b)=>{const av=a?.[state.sort.column],bv=b?.[state.sort.column];return Number.isFinite(Number(av))&&av!==''?(num(av)-num(bv))*dir:String(av||'').localeCompare(String(bv||''),'id-ID',{numeric:true})*dir})}
  function identity(row,total=false){if(isMonthly())return '<td class="mb-sticky-left mb-name" title="'+esc(monthLabel(row.periode))+'">'+(total?'TOTAL':esc(monthLabel(row.periode)))+'</td>';const code=total?'ALL':String(row.kode_kantor||'').padStart(3,'0'),name=total?'GRAND TOTAL':(row.nama_kantor||'-');return '<td class="mb-code-col mb-sticky-left mb-code">'+esc(code)+'</td><td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:44px" title="'+esc(name)+'">'+esc(name)+'<span class="mb-potensi-mobile-code">'+esc(code)+'</span></td>'}
  function targetCells(row){if(isMonthly()){const difference=num(row.selisih);return '<td class="mb-num mb-violet">'+fmtNom(row.nilai_rbb)+'</td><td class="mb-num mb-blue">'+fmtNom(row.realisasi_bulan_ini)+'</td><td class="mb-num '+(num(row.persentase_rbb_bulan_ini)>=100?'mb-positive':'mb-negative')+'">'+pct(row.persentase_rbb_bulan_ini)+'</td><td class="mb-num">'+signed(difference)+'</td><td class="mb-num mb-amber">'+fmtNom(row.kekurangan)+'</td>'}return '<td class="mb-num mb-violet">'+fmtNom(row.nilai_rbb)+'</td><td class="mb-num mb-blue">'+fmtNom(row.realisasi_bulan_ini)+'</td><td class="mb-num '+(num(row.persentase_rbb_bulan_ini)>=100?'mb-positive':'mb-negative')+'">'+pct(row.persentase_rbb_bulan_ini)+'</td><td class="mb-num mb-negative">'+fmtNom(row.kekurangan_sd_bulan_lalu)+'</td><td class="mb-num mb-amber">'+fmtNom(row.total_beban_target)+'</td><td class="mb-num '+(num(row.persentase_rbb_plus_kekurangan)>=100?'mb-positive':'mb-amber')+'">'+pct(row.persentase_rbb_plus_kekurangan)+'</td>'}
  function historyCells(row){return '<td class="mb-num mb-blue">'+fmtNom(row.realisasi_bulan_ini)+'</td><td class="mb-num mb-amber">'+fmtNom(row.run_off)+'</td><td class="mb-num">'+signed(row.growth)+'</td><td class="mb-num">'+signed(row.growth_persen,true)+'</td><td class="mb-num mb-violet">'+fmtNom(row.realisasi_tahun_lalu)+'</td><td class="mb-num">'+signed(row.selisih)+'</td><td class="mb-num">'+signed(row.yoy_persen,true)+'</td>'}
  function rowHtml(row,total=false){return '<tr'+(total?' class="mb-total-row"':'')+'>'+identity(row,total)+(state.view==='history'?historyCells(row):targetCells(row))+'</tr>'}
  function syncHead(){const key=state.view+'_'+(isMonthly()?'month':'office'),switchButton=el('reportRbbViewSwitch');el('reportRbbTable').querySelector('thead').innerHTML=HEADS[key];el('reportRbbTableTitle').textContent=state.view==='rbb'?'Produksi vs RBB':'Pertumbuhan Produksi / YoY';if(switchButton){switchButton.innerHTML=state.view==='rbb'?'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20V10M12 20V4M19 20v-7"/><path d="M3 20h18"/></svg>':'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h6"/></svg>';switchButton.title=state.view==='rbb'?'Ganti ke Pertumbuhan / YoY':'Ganti ke Produksi vs RBB';switchButton.setAttribute('aria-label',switchButton.title)}updateSort()}
  function updateSort(){document.querySelectorAll('#reportRbbTable [data-sort]').forEach(th=>{const icon=th.querySelector('.mb-sort-icon'),active=th.dataset.sort===state.sort.column;if(icon){icon.classList.toggle('is-active',active);icon.classList.toggle('is-desc',active&&state.sort.direction==='desc')}})}
  function render(){syncHead();const rows=visibleRows(),total=isMonthly()?monthlyTotal():state.total,colspan=(state.view==='history'?7:state.view==='rbb'&&isMonthly()?5:6)+(isMonthly()?1:2);el('reportRbbTotal').innerHTML=Object.keys(total||{}).length?rowHtml(total,true):'';el('reportRbbBody').innerHTML=rows.length?rows.map(row=>rowHtml(row)).join(''):'<tr><td colspan="'+colspan+'" class="mb-empty">Data produksi kredit tidak ditemukan.</td></tr>';updateSort()}
  async function loadDates(){try{const json=await (await fetch(API_DATE)).json();el('reportRbbActual').value=json.data?.last_created||new Date().toISOString().slice(0,10)}catch(error){el('reportRbbActual').value=new Date().toISOString().slice(0,10)}}
  async function loadOffices(){try{const json=await post(API_KODE,{type:'kode_kantor'});state.kantor=Array.isArray(json.data)?json.data:[]}catch(error){state.kantor=[]}const select=el('reportRbbArea');if(state.userKode!=='000'){const office=state.kantor.find(row=>String(row.kode_kantor).padStart(3,'0')===state.userKode);select.innerHTML='<option value="CAB-'+state.userKode+'">'+state.userKode+' - '+esc(office?.nama_kantor||'Cabang')+'</option>';select.disabled=true;return}let html='<option value="ALL">Konsolidasi</option>';KORWIL.forEach(item=>html+='<option value="KOR-'+item[0]+'">'+item[1]+'</option>');state.kantor.filter(row=>String(row.kode_kantor).padStart(3,'0')!=='000').sort((a,b)=>String(a.kode_kantor).localeCompare(String(b.kode_kantor),'id-ID',{numeric:true})).forEach(row=>{const code=String(row.kode_kantor).padStart(3,'0');html+='<option value="CAB-'+code+'">'+code+' - '+esc(row.nama_kantor||'Cabang')+'</option>'});select.innerHTML=html}
  async function load(){if(state.abort)state.abort.abort();state.abort=new AbortController();ui().showLoading?.('reportRbbLoading',true);el('reportRbbTotal').innerHTML='';el('reportRbbBody').innerHTML='';try{const json=await post(API_RBB,{type:'realisasi_rbb_bulan_berjalan',harian_date:el('reportRbbActual').value,compare_mode:state.view,...scope()},state.abort.signal);state.rows=Array.isArray(json.data?.data)?json.data.data:[];state.monthly=Array.isArray(json.data?.monthly_breakdown)?json.data.monthly_breakdown:[];state.total=json.data?.grand_total||{};state.meta=json.data?.meta||{};state.sort={column:isMonthly()?'periode':'kode_kantor',direction:isMonthly()?'desc':'asc'};render();requestAnimationFrame(()=>{el('reportRbbTableWrap').scrollLeft=0});ui().closeMobileFilter?.('reportRbbHeaderFilters')}catch(error){if(error.name!=='AbortError'){console.error(error);state.rows=[];state.monthly=[];state.total={};render();el('reportRbbBody').innerHTML='<tr><td colspan="9" class="mb-empty mb-negative">Gagal memuat data produksi kredit.</td></tr>'}}finally{ui().showLoading?.('reportRbbLoading',false)}}
  function switchView(view){if(!['rbb','history'].includes(view)||view===state.view)return;state.view=view;load()}
  function exportExcel(){const rows=visibleRows(),total=isMonthly()?monthlyTotal():state.total;if(!rows.length)return;const history=state.view==='history',monthly=isMonthly(),headers=history?[(monthly?'Bulan':'Kode'),...(monthly?[]:['Kantor']),'Realisasi Tahun Ini','Run Off','Growth','% Growth','Realisasi Tahun Lalu','Selisih YoY','% YoY']:[(monthly?'Bulan':'Kode'),...(monthly?[]:['Kantor']),'Target RBB','Realisasi','% RBB',...(monthly?['Selisih','Kekurangan']:['Kekurangan Lalu','Total Beban','% Beban'])];const values=row=>history?[monthly?monthLabel(row.periode):row.kode_kantor,...(monthly?[]:[row.nama_kantor]),row.realisasi_bulan_ini,row.run_off,row.growth,row.growth_persen,row.realisasi_tahun_lalu,row.selisih,row.yoy_persen]:[monthly?monthLabel(row.periode):row.kode_kantor,...(monthly?[]:[row.nama_kantor]),row.nilai_rbb,row.realisasi_bulan_ini,row.persentase_rbb_bulan_ini,...(monthly?[row.selisih,row.kekurangan]:[row.kekurangan_sd_bulan_lalu,row.total_beban_target,row.persentase_rbb_plus_kekurangan])];const lines=[headers.join('\t'),values(total).map((v,i)=>i===0?'TOTAL':v).join('\t'),...rows.map(row=>values(row).join('\t'))];const blob=new Blob(['\ufeff'+lines.join('\n')],{type:'application/vnd.ms-excel;charset=utf-8'}),a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=(history?'History_Produksi':'Produksi_vs_RBB')+'_'+(el('reportRbbActual').value||'data')+'.xls';document.body.appendChild(a);a.click();a.remove();setTimeout(()=>URL.revokeObjectURL(a.href),1000)}
  function bind(){el('reportRbbViewSwitch')?.addEventListener('click',()=>switchView(state.view==='rbb'?'history':'rbb'));['reportRbbActual','reportRbbArea'].forEach(id=>el(id)?.addEventListener('change',load));el('reportRbbSearch')?.addEventListener('input',ui().debounce?ui().debounce(render,160):render);el('reportRbbExport')?.addEventListener('click',exportExcel);el('reportRbbTable')?.addEventListener('click',event=>{const head=event.target.closest('[data-sort]');if(!head)return;const column=head.dataset.sort;state.sort={column,direction:state.sort.column===column&&state.sort.direction==='asc'?'desc':'asc'};render()})}
  const renderRbbBase=render;
  render=function(){renderRbbBase();el('reportRbbTable').classList.toggle('mb-rbb-table--monthly',isMonthly())}
  async function init(){state.userKode=currentUserCode();await Promise.all([loadDates(),loadOffices()]);bind();load()}
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
</script>

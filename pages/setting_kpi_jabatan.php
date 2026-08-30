<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');
$head=mb_build_grouped_thead([
 ['label'=>'Jabatan','class'=>'mb-sticky-left','attrs'=>['style'=>'width:120px']],['label'=>'Kelompok','attrs'=>['style'=>'width:120px']],['label'=>'Indikator','class'=>'mb-text-left','attrs'=>['style'=>'width:250px']],['label'=>'Bobot','attrs'=>['style'=>'width:82px']],['label'=>'Arah','attrs'=>['style'=>'width:90px']],['label'=>'Unit','attrs'=>['style'=>'width:90px']],['label'=>'Sumber Data','attrs'=>['style'=>'width:180px']],['label'=>'Status','attrs'=>['style'=>'width:100px']],['label'=>'Aksi','attrs'=>['style'=>'width:80px']]
]);
mb_render_report_page([
 'id'=>'settingKpiJabatanPage','class'=>'mb-kpi-setting-page',
 'header'=>[
   'id'=>'settingKpiHeader','title'=>'Setting KPI Jabatan',
   'subtitle'=>'Kelola indikator, bobot, arah penilaian, dan sumber data KPI bisnis.',
   'icon'=>mb_svg('edit'),'info_modal_id'=>'settingKpiInfo',
   'filters'=>[
     ['id'=>'settingKpiJob','label'=>'Jabatan','type'=>'select','width'=>'190px',
      'options'=>['AO_KREDIT'=>'AO Kredit','AO_REMEDIAL'=>'AO Remedial']]
   ]
 ],
 'toolbar'=>[
   'title'=>'Master Indikator KPI','title_id'=>'settingKpiTitle',
   'search'=>['id'=>'settingKpiSearch','placeholder'=>'Cari indikator...'],
   'actions'=>[['attrs'=>['id'=>'settingKpiSave'],'tone'=>'success','icon'=>'download','title'=>'Muat ulang','aria_label'=>'Muat ulang']]
 ],
 'table'=>[
   'wrapper_id'=>'settingKpiTableWrap','table_id'=>'settingKpiTable','loading_id'=>'settingKpiLoading',
   'loading_text'=>'Memuat setting KPI...','thead_html'=>$head,'tbody_ids'=>['settingKpiBody']
 ]
]);
mb_render_info_modal(['id'=>'settingKpiInfo','title'=>'Setting KPI Jabatan','subtitle'=>'Parameter bersifat versionable dan perlu divalidasi sebelum dipakai.','body_html'=>'<div class="mb-npl-brief"><div class="mb-npl-brief__alert"><strong>Bobot satu jabatan harus berjumlah 100%.</strong><span>Perubahan parameter memengaruhi penilaian pada periode berikutnya.</span></div><div class="mb-info-warning"><span>Catatan:</span><div>Formula indikator akan diaktifkan bertahap setelah definisi sumber data disepakati.</div></div></div>']);
?>
<script>
(()=>{const API='./api/kpi/',EDIT_ICON=<?= json_encode(mb_svg('edit')) ?>,el=id=>document.getElementById(id),ui=()=>window.MonbisUI||{},esc=v=>ui().escape?ui().escape(v):String(v??''),num=v=>Number(v||0),fmt=v=>new Intl.NumberFormat('id-ID',{style:'percent',maximumFractionDigits:1}).format(num(v));let rows=[];
async function post(body){const r=await(window.apiFetch||fetch)(API,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});const j=await r.json();if(!r.ok||Number(j.status)!==200)throw Error(j.message||'Gagal memuat setting');return j.data||{}}
function render(){const job=el('settingKpiJob').value;const list=rows.filter(x=>x.jabatan_kode===job),body=el('settingKpiBody');body.innerHTML=list.length?list.map(x=>`<tr data-id="${x.id}"><td class="mb-sticky-left mb-code"><strong>${esc(x.jabatan_nama)}</strong></td><td>${esc(x.kelompok)}</td><td class="mb-text-left"><strong>${esc(x.nama)}</strong><small class="mb-subvalue">${esc(x.definisi||'-')}</small></td><td><input class="mb-kpi-input" data-field="bobot" type="number" min="0" max="1" step="0.01" value="${num(x.bobot)}"></td><td>${esc(x.arah)}</td><td>${esc(x.unit)}</td><td>${esc(x.sumber_data||'-')}</td><td><select class="mb-kpi-input" data-field="status"><option value="PILOT" ${x.status==='PILOT'?'selected':''}>PILOT</option><option value="AKTIF" ${x.status==='AKTIF'?'selected':''}>AKTIF</option><option value="NONAKTIF" ${x.status==='NONAKTIF'?'selected':''}>NONAKTIF</option></select></td><td><button type="button" class="mb-icon-button mb-icon-button--primary mb-kpi-row-save" title="Simpan indikator" aria-label="Simpan indikator">${EDIT_ICON}</button></td></tr>`).join(''):'<tr><td colspan="9" class="mb-empty">Data indikator tidak ditemukan.</td></tr>'}
async function load(){ui().showLoading?.('settingKpiLoading',true);try{const d=await post({type:'setting'});rows=d.indikator||[];render()}catch(e){el('settingKpiBody').innerHTML=`<tr><td colspan="9" class="mb-empty mb-negative">${esc(e.message)}</td></tr>`}finally{ui().showLoading?.('settingKpiLoading',false)}}
document.addEventListener('click',async e=>{const b=e.target.closest('.mb-kpi-row-save');if(!b)return;const tr=b.closest('tr');const input=tr.querySelector('[data-field="bobot"]'),status=tr.querySelector('[data-field="status"]');try{await post({type:'save_indicator',id:tr.dataset.id,bobot:input.value,status:status.value});alert('Parameter indikator disimpan.')}catch(x){alert(x.message)}});el('settingKpiJob')?.addEventListener('change',render);el('settingKpiSave')?.addEventListener('click',load);el('settingKpiSearch')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('#settingKpiBody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(q)?'':'none')});load();})();
</script>

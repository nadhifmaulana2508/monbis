<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');
$head=mb_build_grouped_thead([
 ['label'=>'Jabatan','class'=>'mb-sticky-left','attrs'=>['style'=>'width:120px']],['label'=>'Kelompok','attrs'=>['style'=>'width:120px']],['label'=>'Indikator','class'=>'mb-text-left','attrs'=>['style'=>'width:250px']],['label'=>'Bobot','attrs'=>['style'=>'width:82px']],['label'=>'Target Default','attrs'=>['style'=>'width:110px']],['label'=>'Arah','attrs'=>['style'=>'width:90px']],['label'=>'Unit','attrs'=>['style'=>'width:90px']],['label'=>'Status','attrs'=>['style'=>'width:100px']],['label'=>'Aksi','attrs'=>['style'=>'width:80px']]
]);
$scoreHead=mb_build_grouped_thead([
 ['label'=>'Skor','attrs'=>['style'=>'width:70px']],['label'=>'Indeks Minimum','attrs'=>['style'=>'width:130px']],['label'=>'Indeks Maksimum','attrs'=>['style'=>'width:130px']],['label'=>'Predikat','class'=>'mb-text-left','attrs'=>['style'=>'width:220px']],['label'=>'Status','attrs'=>['style'=>'width:100px']],['label'=>'Aksi','attrs'=>['style'=>'width:80px']]
]);
mb_render_report_page([
 'id'=>'settingKpiJabatanPage','class'=>'mb-kpi-setting-page',
 'header'=>[
   'id'=>'settingKpiHeader','title'=>'Setting KPI Jabatan',
   'subtitle'=>'Kelola indikator, bobot, arah penilaian, dan sumber data KPI bisnis.',
   'icon'=>mb_svg('edit'),'info_modal_id'=>'settingKpiInfo',
   'filters'=>[
     ['id'=>'settingKpiJob','label'=>'Jabatan','type'=>'select','width'=>'190px',
      'options'=>['AO_KREDIT'=>'AO Kredit','AO_DANA'=>'AO Dana','AO_REMEDIAL'=>'AO Remedial']],
     ['id'=>'settingKpiUnit','label'=>'Unit','type'=>'select','width'=>'130px',
      'options'=>[''=>'Semua Unit','RUPIAH'=>'Rupiah','NOA'=>'NOA','PERSEN'=>'Persen','JUMLAH'=>'Jumlah']]
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
echo '<section class="mb-report-card mb-kpi-score-card"><div class="mb-report-toolbar"><h3 class="mb-report-toolbar__title">Penyesuaian Parameter Skor</h3><div class="mb-report-toolbar__tools"><span class="mb-kpi-score-note">Atur range indeks skor 1–5 sesuai jabatan terpilih</span></div></div><div class="mb-table-region"><div class="mb-table-scroll"><table class="mb-table"><thead>'.$scoreHead.'</thead><tbody id="settingKpiScoreBody"><tr><td colspan="6" class="mb-empty">Memuat parameter skor...</td></tr></tbody></table></div></div></section>';
mb_render_info_modal(['id'=>'settingKpiInfo','title'=>'Setting KPI Jabatan','subtitle'=>'Parameter bersifat versionable dan perlu divalidasi sebelum dipakai.','body_html'=>'<div class="mb-npl-brief"><div class="mb-npl-brief__alert"><strong>Bobot satu jabatan harus berjumlah 100%.</strong><span>Perubahan parameter memengaruhi penilaian pada periode berikutnya.</span></div><div class="mb-info-warning"><span>Catatan:</span><div>Formula indikator akan diaktifkan bertahap setelah definisi sumber data disepakati.</div></div></div>']);
?>
<style>
.mb-kpi-setting-page .mb-report-card--grow{min-height:0!important}.mb-kpi-score-card{display:none!important}.mb-kpi-score-note{font-size:10px;color:var(--mb-muted,#64748b)}.mb-kpi-score-input{width:100%;min-width:0;padding:5px 6px;border:1px solid var(--mb-border,#cbd8e8);border-radius:6px;background:var(--mb-card,#fff);color:inherit;font:inherit;font-size:10px}.mb-kpi-score-card .mb-table{min-width:680px}.mb-kpi-setting-page #settingKpiTable{min-width:1280px}.mb-kpi-setting-page #settingKpiTable th[data-score-column],.mb-kpi-setting-page #settingKpiTable td[data-score-column]{min-width:105px;text-align:center;font-size:10px}.mb-inline-score{display:grid;grid-template-columns:1fr 1fr;gap:2px;align-items:center;min-width:102px}.mb-inline-score input{width:100%;min-width:0;padding:3px 2px;border:1px solid var(--mb-border,#cbd8e8);border-radius:4px;background:var(--mb-card,#fff);color:inherit;font-size:9px;text-align:center}.mb-inline-score button{grid-column:1/-1;justify-self:center;border:0;border-radius:4px;background:#2563eb;color:#fff;font-size:8px;padding:2px 7px;cursor:pointer}.mb-inline-score button:disabled{opacity:.55}
</style>
<style>
.mb-kpi-setting-page #settingKpiTable{width:100%;min-width:0;table-layout:fixed}
.mb-kpi-setting-page #settingKpiTable th,.mb-kpi-setting-page #settingKpiTable td{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.mb-kpi-setting-page #settingKpiTable th[data-score-column],.mb-kpi-setting-page #settingKpiTable td[data-score-column]{width:62px;min-width:62px;padding-left:2px;padding-right:2px}
.mb-kpi-setting-page #settingKpiTable th:nth-child(1),.mb-kpi-setting-page #settingKpiTable td:nth-child(1){width:78px}
.mb-kpi-setting-page #settingKpiTable th:nth-child(2),.mb-kpi-setting-page #settingKpiTable td:nth-child(2){width:92px}
.mb-kpi-setting-page #settingKpiTable th:nth-child(3),.mb-kpi-setting-page #settingKpiTable td:nth-child(3){width:210px}
.mb-kpi-setting-page #settingKpiTable th:nth-child(4),.mb-kpi-setting-page #settingKpiTable td:nth-child(4){width:58px}
.mb-kpi-setting-page #settingKpiTable th:nth-child(11),.mb-kpi-setting-page #settingKpiTable td:nth-child(11){width:105px}
.mb-kpi-setting-page #settingKpiTable th:nth-child(12),.mb-kpi-setting-page #settingKpiTable td:nth-child(12){width:65px}
.mb-kpi-setting-page #settingKpiTable th:nth-child(13),.mb-kpi-setting-page #settingKpiTable td:nth-child(13){width:65px}
.mb-kpi-setting-page #settingKpiTable th:nth-child(14),.mb-kpi-setting-page #settingKpiTable td:nth-child(14){width:74px}
.mb-kpi-setting-page #settingKpiTable th:nth-child(15),.mb-kpi-setting-page #settingKpiTable td:nth-child(15){width:48px}
.mb-inline-score{min-width:0;width:100%;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:1px}
.mb-inline-score input{width:100%;min-width:0;padding:2px 0;font-size:8px;line-height:14px}
.mb-inline-score button{width:18px;height:18px;padding:0;font-size:10px;line-height:18px}
@media (max-width:900px){
  .mb-kpi-setting-page #settingKpiTable th:nth-child(1),.mb-kpi-setting-page #settingKpiTable td:nth-child(1),
  .mb-kpi-setting-page #settingKpiTable th:nth-child(2),.mb-kpi-setting-page #settingKpiTable td:nth-child(2),
  .mb-kpi-setting-page #settingKpiTable th:nth-child(12),.mb-kpi-setting-page #settingKpiTable td:nth-child(12),
  .mb-kpi-setting-page #settingKpiTable th:nth-child(13),.mb-kpi-setting-page #settingKpiTable td:nth-child(13),
  .mb-kpi-setting-page #settingKpiTable th:nth-child(14),.mb-kpi-setting-page #settingKpiTable td:nth-child(14){display:none}
  .mb-kpi-setting-page #settingKpiTable th:nth-child(3),.mb-kpi-setting-page #settingKpiTable td:nth-child(3){width:90px}
  .mb-kpi-setting-page #settingKpiTable th:nth-child(4),.mb-kpi-setting-page #settingKpiTable td:nth-child(4){width:42px}
  .mb-kpi-setting-page #settingKpiTable th[data-score-column],.mb-kpi-setting-page #settingKpiTable td[data-score-column]{width:33px;min-width:33px;font-size:8px}
  .mb-kpi-setting-page #settingKpiTable th:nth-child(11),.mb-kpi-setting-page #settingKpiTable td:nth-child(11){display:none}
  .mb-inline-score input{font-size:7px}
  .mb-inline-score button{width:16px;height:16px;font-size:9px;line-height:16px}
}
</style>
<script>
(()=>{const API='./api/index.php?request=kpi',EDIT_ICON=<?= json_encode(mb_svg('edit')) ?>,el=id=>document.getElementById(id),ui=()=>window.MonbisUI||{},esc=v=>ui().escape?ui().escape(v):String(v??''),num=v=>Number(v||0),fmt=v=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:2}).format(num(v));
function formatTarget(v,unit){const n=num(v);if(unit==='RUPIAH')return 'Rp '+new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(n);if(unit==='PERSEN')return new Intl.NumberFormat('id-ID',{style:'percent',maximumFractionDigits:2}).format(n);return new Intl.NumberFormat('id-ID',{maximumFractionDigits:2}).format(n)}
function parseTarget(value,unit){let s=String(value??'').trim().replace(/^Rp\s*/i,'').replace(/\s/g,'');if(unit==='PERSEN'&&s.endsWith('%'))return (parseFloat(s.slice(0,-1).replace(',','.'))||0)/100;s=s.replace(/\./g,'').replace(',','.');return Number(s)||0}
function formatWeight(v){return new Intl.NumberFormat('id-ID',{maximumFractionDigits:2}).format(num(v)*100)+'%'}
function parseWeight(value){let s=String(value??'').trim().replace(',','.');if(s.endsWith('%'))return (parseFloat(s.slice(0,-1))||0)/100;const n=parseFloat(s);return Number.isFinite(n)?(n>1?n/100:n):0}
function bindTargetFormatting(){document.querySelectorAll('#settingKpiBody [data-field="target"]').forEach(input=>{input.addEventListener('focus',()=>{input.value=String(num(input.dataset.raw))});input.addEventListener('blur',()=>{const value=parseTarget(input.value,input.dataset.unit);input.dataset.raw=String(value);input.value=formatTarget(value,input.dataset.unit)})})}
let rows=[],scoreRows=[];
function kpiHeaders(){return {'Content-Type':'application/json'}}
async function post(body){const r=await fetch(API,{method:'POST',credentials:'same-origin',headers:kpiHeaders(),body:JSON.stringify(body)});const j=await r.json();if(!r.ok||Number(j.status)!==200)throw Error(j.message||'Gagal memuat setting');return j.data||{}}
function rangeText(x){const min=num(x.min_indeks),max=num(x.max_indeks);if(min===0)return '<'+fmt(max*100)+'%';if(max>=999)return '>='+fmt(min*100)+'%';return '>='+fmt(min*100)+'% - <'+fmt(max*100)+'%'}
function renderScores(){const job=el('settingKpiJob').value,indicators=rows.filter(x=>x.jabatan_kode===job&&x.status!=='NONAKTIF'),ranges=scoreRows.filter(x=>x.jabatan_kode===job&&num(x.aktif)).sort((a,b)=>num(a.skor)-num(b.skor)),body=el('settingKpiScoreBody');body.innerHTML=indicators.length?indicators.map(x=>`<tr><td class="mb-sticky-left mb-text-left"><strong>${esc(x.nama)}</strong><small class="mb-subvalue">${esc(x.kelompok||'')}</small></td><td class="mb-num"><strong>${fmt(num(x.bobot)*100)}%</strong></td>${[0,1,2,3,4,5].map(score=>{const item=ranges.find(r=>num(r.skor)===score);return '<td class="mb-text-center">'+(item?rangeText(item):'-')+'</td>'}).join('')}</tr>`).join(''):'<tr><td colspan="8" class="mb-empty">Parameter skor belum tersedia untuk jabatan ini.</td></tr>'}
  function render(){const job=el('settingKpiJob').value,unit=el('settingKpiUnit').value;const list=rows.filter(x=>x.jabatan_kode===job&&x.status==='AKTIF'&&(!unit||x.unit===unit)),body=el('settingKpiBody');body.innerHTML=list.length?list.map(x=>`<tr data-id="${x.id}" data-formula="${esc(x.formula_key||'')}" data-unit="${esc(x.unit||'')}"><td class="mb-sticky-left mb-code"><strong>${esc(x.jabatan_nama)}</strong></td><td>${esc(x.kelompok)}</td><td class="mb-text-left"><strong>${esc(x.nama)}</strong><small class="mb-subvalue">${esc(x.definisi||'-')}</small></td><td><input class="mb-kpi-input" data-field="bobot" type="text" inputmode="decimal" data-raw="${num(x.bobot)}" value="${esc(formatWeight(x.bobot))}" title="Bobot persen"></td><td><input class="mb-kpi-input" data-field="target" data-raw="${num(x.target_default)}" data-unit="${esc(x.unit)}" type="text" inputmode="decimal" value="${esc(formatTarget(x.target_default,x.unit))}" title="Target default global"></td><td>${esc(x.arah)}</td><td>${esc(x.unit)}</td><td><select class="mb-kpi-input" data-field="status"><option value="AKTIF" selected>AKTIF</option></select></td><td><button type="button" class="mb-icon-button mb-icon-button--primary mb-kpi-row-save" title="Simpan indikator dan target" aria-label="Simpan indikator dan target">${EDIT_ICON}</button></td></tr>`).join(''):'<tr><td colspan="9" class="mb-empty">Tidak ada indikator aktif untuk filter ini.</td></tr>';document.querySelectorAll('#settingKpiBody [data-field="bobot"]').forEach(input=>{input.addEventListener('focus',()=>{input.value=String(num(input.dataset.raw)*100)});input.addEventListener('blur',()=>{const value=parseWeight(input.value);input.dataset.raw=String(value);input.value=formatWeight(value)})});bindTargetFormatting()}
async function load(){ui().showLoading?.('settingKpiLoading',true);try{const d=await post({type:'setting'});rows=d.indikator||[];scoreRows=d.parameter_skor||[];render();renderScores()}catch(e){el('settingKpiBody').innerHTML=`<tr><td colspan="9" class="mb-empty mb-negative">${esc(e.message)}</td></tr>`}finally{ui().showLoading?.('settingKpiLoading',false)}}
document.addEventListener('click',async e=>{const scoreButton=e.target.closest('.mb-score-row-save');if(scoreButton){const tr=scoreButton.closest('tr');scoreButton.disabled=true;try{await post({type:'save_score',id:tr.dataset.scoreId,min_indeks:tr.querySelector('[data-score-field="min_indeks"]').value,max_indeks:tr.querySelector('[data-score-field="max_indeks"]').value,predikat:tr.querySelector('[data-score-field="predikat"]').value,aktif:tr.querySelector('[data-score-field="aktif"]').value});await load();alert('Parameter skor disimpan.')}catch(x){alert(x.message)}finally{scoreButton.disabled=false}return}const b=e.target.closest('.mb-kpi-row-save');if(!b)return;const tr=b.closest('tr');const input=tr.querySelector('[data-field="bobot"]'),target=tr.querySelector('[data-field="target"]'),status=tr.querySelector('[data-field="status"]');const targetValue=parseTarget(target.value,target.dataset.unit);target.dataset.raw=String(targetValue);b.disabled=true;try{await post({type:'save_indicator',id:tr.dataset.id,bobot:parseWeight(input.value),status:status.value,target:targetValue});await load();alert('Parameter dan target default disimpan.')}catch(x){alert(x.message)}finally{b.disabled=false}});el('settingKpiJob')?.addEventListener('change',render);el('settingKpiUnit')?.addEventListener('change',render);el('settingKpiSave')?.addEventListener('click',load);el('settingKpiSearch')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('#settingKpiBody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(q)?'':'none')});load();})();
</script>
<script>
(() => {
  const table=document.getElementById('settingKpiTable'), body=document.getElementById('settingKpiBody'), job=document.getElementById('settingKpiJob');
  if(!table||!body||!job)return;
  let scoreRows=[];
  const number=value=>Number(value||0);
  const esc=value=>{const node=document.createElement('span');node.textContent=String(value??'');return node.innerHTML};
  const percent=value=>{const n=number(value)*100;return Number.isInteger(n)?String(n):n.toFixed(2).replace(/0+$/,'').replace(/\.$/,'')};
  const scoreText=(row,unit)=>{const min=number(row.min_indeks),max=number(row.max_indeks),isCount=['NOA','JUMLAH'].includes(String(unit||'').toUpperCase());if(isCount){if(max>=999)return '>='+min;return min===0?'0 - <'+max:'>='+min+' - <'+max}if(max>=999)return '&gt;='+percent(min)+'%';return min===0?'&lt;'+percent(max)+'%':'&gt;='+percent(min)+'% - &lt;'+percent(max)+'%'};
  const scoreInput=(value,unit)=>{const n=number(value);return n>=999?'':(['NOA','JUMLAH'].includes(String(unit||'').toUpperCase())?String(n):percent(n)+'%')};
  const parseScore=(value,unit)=>{let s=String(value??'').trim().replace(',','.');if(!s)return 999;if(s.endsWith('%'))return (parseFloat(s.slice(0,-1))||0)/100;const n=parseFloat(s);return Number.isFinite(n)?(n||0):0};
  async function loadScores(){try{const response=await fetch('./api/index.php?request=kpi',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({type:'setting_scores'})});const json=await response.json();scoreRows=json.data?.parameter_skor||[];decorate()}catch(error){console.error('Gagal memuat range skor',error)}}
  function scoreCell(item,label,unit){if(!item)return '-';unit=unit||item.unit||'';return '<div class="mb-inline-score" data-score-id="'+esc(item.id)+'" data-score-label="'+label+'" data-score-unit="'+esc(unit)+'" title="Range skor '+label+'; simpan perubahan dengan tombol centang"><input type="text" data-score-field="min_indeks" value="'+esc(scoreInput(item.min_indeks,unit))+'" aria-label="Minimum skor '+label+'"><input type="text" data-score-field="max_indeks" value="'+esc(scoreInput(item.max_indeks,unit))+'" placeholder="∞" aria-label="Maksimum skor '+label+'"><button type="button" class="mb-inline-score-save" title="Simpan range skor '+label+'" aria-label="Simpan range skor '+label+'">✓</button></div>'}
  function decorate(){const head=table.querySelector('thead tr');if(!head)return;const ranges=scoreRows.filter(x=>x.jabatan_kode===job.value&&number(x.aktif));const labels=['0','1','2','3','4','5'];const itemFor=(row,label)=>ranges.find(x=>String(x.indikator_id)===String(row.dataset.id)&&String(x.skor)===label);const anchor=head.children[4];labels.forEach(label=>{let cell=head.querySelector('[data-score-column="'+label+'"]');if(!cell){cell=document.createElement('th');cell.dataset.scoreColumn=label;cell.textContent=label;head.insertBefore(cell,anchor||null)}});body.querySelectorAll('tr[data-id]').forEach(row=>{const unit=row.dataset.unit||'';const existing=row.querySelector('[data-score-column="0"]');if(existing){labels.forEach(label=>{const cell=row.querySelector('[data-score-column="'+label+'"]'),item=itemFor(row,label);if(cell)cell.innerHTML=scoreCell(item,label,unit)});return}const target=row.children[4];labels.forEach(label=>{const cell=document.createElement('td');cell.dataset.scoreColumn=label;const item=itemFor(row,label);cell.innerHTML=scoreCell(item,label,unit);row.insertBefore(cell,target||null)})})}
  document.addEventListener('click',async event=>{const button=event.target.closest('.mb-inline-score-save');if(!button)return;event.preventDefault();event.stopPropagation();const box=button.closest('.mb-inline-score'),id=Number(box?.dataset.scoreId||0),item=scoreRows.find(x=>Number(x.id)===id),unit=box?.dataset.scoreUnit||'';if(!id||!item)return;const min=parseScore(box.querySelector('[data-score-field="min_indeks"]')?.value,unit),max=parseScore(box.querySelector('[data-score-field="max_indeks"]')?.value,unit);if(max<min){window.alert('Indeks maksimum tidak boleh lebih kecil dari minimum.');return}button.disabled=true;try{const response=await fetch('./api/index.php?request=kpi',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({type:'save_score',id,min_indeks:min,max_indeks:max,predikat:item.predikat||'',aktif:1})});const json=await response.json();if(!response.ok||Number(json.status)!==200)throw new Error(json.message||'Range skor gagal disimpan');item.min_indeks=min;item.max_indeks=max;decorate();button.classList.add('is-saved');setTimeout(()=>button.classList.remove('is-saved'),700)}catch(error){window.alert(error.message)}finally{button.disabled=false}});
  new MutationObserver(()=>decorate()).observe(body,{childList:true});
  job.addEventListener('change',decorate);loadScores();
})();
</script>

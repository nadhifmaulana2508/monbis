<?php
require_once __DIR__ . '/../components/bootstrap.php';
$hitungKpiDefaultMode = $hitungKpiDefaultMode ?? 'single';
mb_ui_assets('.');

$head = mb_build_grouped_thead([
    ['label'=>'INDIKATOR','class'=>'mb-sticky-left mb-text-left','attrs'=>['style'=>'width:220px']],
    ['label'=>'TARGET','class'=>'mb-group--blue','attrs'=>['style'=>'width:130px']],
    ['label'=>'REALISASI','class'=>'mb-group--green','attrs'=>['style'=>'width:130px']],
    ['label'=>'INDEKS','attrs'=>['style'=>'width:90px']],
    ['label'=>'SKOR','attrs'=>['style'=>'width:70px']],
    ['label'=>'SKOR 2','attrs'=>['style'=>'width:75px']],
    ['label'=>'BOBOT','attrs'=>['style'=>'width:80px']],
    ['label'=>'NILAI','class'=>'mb-group--green','attrs'=>['style'=>'width:90px']],
    ['label'=>'NILAI 2','class'=>'mb-group--green','attrs'=>['style'=>'width:90px']],
    ['label'=>'TUKIN','attrs'=>['style'=>'width:75px']],
    ['label'=>'KETERANGAN','class'=>'mb-text-left','attrs'=>['style'=>'width:360px']],
]);

mb_render_report_page([
    'id'=>'hitungKpiAoPage',
    'class'=>'mb-kpi-calc-page',
    'header'=>[
        'id'=>'hitungKpiAoHeader',
        'title'=>'Penilaian KPI Bisnis',
        'subtitle'=>'Hitung nilai bulanan satu AO sesuai jabatan dan indikator aktif.',
        'icon'=>mb_svg('chart'),
        'info_modal_id'=>'hitungKpiAoInfo',
        'filters'=>[
            ['id'=>'hitungKpiJabatan','label'=>'Jabatan','type'=>'select','width'=>'150px','options'=>[''=>'Memuat...']],
            ['id'=>'hitungKpiYear','label'=>'Tahun','type'=>'number','width'=>'75px','value'=>date('Y')],
            ['id'=>'hitungKpiClosing','label'=>'Closing','type'=>'select','width'=>'130px','options'=>[''=>'Memuat...']],
            ['id'=>'hitungKpiKantor','label'=>'Kantor','type'=>'select','width'=>'150px','options'=>[''=>'Pilih kantor dahulu']],
            ['id'=>'hitungKpiAo','label'=>'AO','type'=>'select','width'=>'250px','options'=>[''=>'Pilih kantor dahulu']],
        ],
    ],
    'toolbar'=>[
        'title'=>'Detail Penilaian Bulanan',
        'title_id'=>'hitungKpiAoTitle',
        'search'=>['id'=>'hitungKpiSearch','placeholder'=>'Cari indikator...'],
        'actions'=>[['attrs'=>['id'=>'hitungKpiRun'],'tone'=>'success','icon'=>'chart','title'=>'Hitung AO terpilih','aria_label'=>'Hitung AO terpilih','label'=>'Hitung KPI']],
    ],
    'legend_html'=>'<div class="mb-summary">
        <div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">JABATAN</div><div id="hitungKpiJobName" class="mb-summary-card__value">-</div><div id="hitungKpiJobMeta" class="mb-summary-card__meta">Pilih jabatan</div></div>
        <div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">AO TERPILIH</div><div id="hitungKpiAoName" class="mb-summary-card__value">-</div><div id="hitungKpiAoMeta" class="mb-summary-card__meta">Pilih kantor dan AO</div></div>
        <div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">NILAI AKHIR</div><div id="hitungKpiFinal" class="mb-summary-card__value">-</div><div class="mb-summary-card__meta">Kontribusi indikator aktif</div></div>
        <div class="mb-summary-card mb-summary-card--red"><div class="mb-summary-card__label">STATUS</div><div id="hitungKpiStatus" class="mb-summary-card__value">BELUM DIHITUNG</div><div id="hitungKpiNote" class="mb-summary-card__meta">Pilih jabatan, kantor, AO, dan closing</div></div>
    </div>',
    'table'=>['wrapper_id'=>'hitungKpiTableWrap','table_id'=>'hitungKpiTable','loading_id'=>'hitungKpiLoading','loading_text'=>'Menghitung KPI...','thead_html'=>$head,'tbody_ids'=>['hitungKpiBody']],
]);

mb_render_info_modal([
    'id'=>'hitungKpiAoInfo',
    'title'=>'Cara Hitung KPI Bisnis',
    'subtitle'=>'Perhitungan satu AO untuk satu closing bulanan.',
    'body_html'=>'<div class="mb-npl-brief"><div class="mb-npl-brief__alert"><strong>Pilih jabatan terlebih dahulu.</strong><span>Setelah kantor dipilih, daftar AO akan menyesuaikan dengan master AO Kredit, AO Dana, atau AO Remedial.</span></div><div class="mb-info-warning"><span>Catatan AO Dana &amp; AO Remedial:</span><div>Untuk sementara sumber data real belum tersedia. Generate memakai data dummy agar alur skor, bobot, penyimpanan, dan tampilan dapat diuji. Detail indikator akan diberi keterangan <b>DUMMY</b>.</div></div><div class="mb-npl-brief__priority-grid"><div class="mb-npl-brief__priority mb-npl-brief__priority--blue"><b>1</b><div><strong>Jabatan dan kantor</strong><span>Jabatan menentukan indikator aktif dan kantor wajib dipilih sebelum daftar AO dibuka.</span></div></div><div class="mb-npl-brief__priority mb-npl-brief__priority--violet"><b>2</b><div><strong>Indeks</strong><span>Realisasi dibagi target, kemudian dicocokkan dengan range skor jabatan.</span></div></div><div class="mb-npl-brief__priority mb-npl-brief__priority--red"><b>3</b><div><strong>Nilai</strong><span>Skor dikalikan bobot indikator aktif dan disimpan pada periode closing yang dipilih.</span></div></div></div></div>',
]);

mb_render_info_modal(['id'=>'hitungKpiSuccess','title'=>'Generate KPI Berhasil','subtitle'=>'Penilaian cabang sudah selesai diproses.','body_id'=>'hitungKpiSuccessBody','body_html'=>'<div class="mb-kpi-success"><div class="mb-kpi-success__icon">✓</div><strong id="hitungKpiSuccessMessage">Generate KPI selesai.</strong><span>Hasil penilaian dapat dilihat pada tabel ini atau menu rekap KPI.</span></div>']);
mb_render_generate_confirm(['id'=>'hitungKpiGenerateConfirm']);
?>
<style>
.mb-kpi-calc-page .mb-report-card--grow{min-height:0!important}
.mb-kpi-calc-page #hitungKpiTable{min-width:1420px}
.mb-kpi-calc-page #hitungKpiTable .kpi-note{min-width:320px;max-width:380px;white-space:normal;line-height:1.35;text-align:left}
.mb-kpi-calc-page #hitungKpiTable .kpi-note small{display:block;margin-top:3px;color:var(--mb-muted,#64748b);font-size:8px;line-height:1.3}
.mb-kpi-calc-page .mb-kpi-bulk{padding:12px;border:1px solid var(--mb-border,#dbe4ef);border-radius:10px;background:var(--mb-card,#fff)}
.mb-kpi-calc-page .mb-kpi-bulk__head{display:flex;flex-direction:column;gap:3px;margin-bottom:10px;color:var(--mb-muted,#64748b);font-size:10px}.mb-kpi-calc-page .mb-kpi-bulk__head strong{color:var(--mb-ink,#0f172a);font-size:13px}
.mb-kpi-calc-page .mb-kpi-bulk__status{margin:10px 0;color:#2563eb;font-size:10px;font-weight:800}.mb-kpi-calc-page .mb-kpi-bulk__table-wrap{max-height:420px;overflow:auto}
.mb-kpi-calc-page .mb-kpi-bulk__table-wrap .mb-table{min-width:100%}.mb-kpi-calc-page .mb-kpi-success{display:flex;flex-direction:column;align-items:center;gap:8px;padding:12px;text-align:center;color:var(--mb-muted,#64748b)}.mb-kpi-success strong{color:var(--mb-ink,#0f172a);font-size:13px}.mb-kpi-success__icon{display:grid;place-items:center;width:38px;height:38px;border-radius:50%;background:#dcfce7;color:#059669;font-size:22px;font-weight:900}
.mb-kpi-generate-confirm .mb-modal__card--info{width:min(520px,calc(100vw - 28px))}.mb-kpi-generate-confirm__copy{display:flex;flex-direction:column;gap:7px;color:#64748b;font-size:11px;line-height:1.5}.mb-kpi-generate-confirm__copy strong{color:#0f172a;font-size:13px}.mb-kpi-generate-confirm__actions{display:flex;justify-content:flex-end;gap:7px;padding:10px 14px;border-top:1px solid #e2e8f0;background:#fff}.mb-kpi-generate-confirm__actions .mb-btn{min-width:94px}
html[data-monbis-theme="dark"] .mb-kpi-generate-confirm__copy strong{color:#e5e7eb}html[data-monbis-theme="dark"] .mb-kpi-generate-confirm__actions{border-color:#334155;background:#111827}
@media(max-width:700px){.mb-kpi-calc-page #hitungKpiTable{min-width:1240px}.mb-kpi-calc-page #hitungKpiTable .kpi-note{min-width:270px;max-width:320px}.mb-kpi-calc-page .mb-kpi-bulk{padding:8px}.mb-kpi-calc-page .mb-kpi-bulk__table-wrap{max-height:300px}}
</style>
<script>
window.MonbisKpiHeaders=window.MonbisKpiHeaders||function(){return {'Content-Type':'application/json'}};
(()=>{
  const API='./api/index.php?request=kpi', el=id=>document.getElementById(id), ui=()=>window.MonbisUI||{};
  const esc=v=>ui().escape?ui().escape(v):String(v??''), num=v=>Number(v||0);
  const fmt=v=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:2}).format(num(v));
  const money=v=>'Rp '+new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(num(v));
  const pct=v=>fmt(num(v)*100)+'%';
  const post=async body=>{const r=await fetch(API,{method:'POST',credentials:'same-origin',headers:window.MonbisKpiHeaders(),body:JSON.stringify(body)});const j=await r.json();if(!r.ok||Number(j.status)!==200)throw Error(j.message||'Gagal memuat KPI');return j.data||{}};
  const state={boot:null,detail:[],mode:'single'};
  const job=()=>el('hitungKpiJabatan')?.value||'AO_KREDIT';
  const aoList=()=>state.boot?.ao||[];
  const currentAo=()=>aoList().find(x=>String(x.kode_ao)===String(el('hitungKpiAo')?.value));
  const currentJob=()=>state.boot?.jabatan_terpilih||((state.boot?.jabatan||[]).find(x=>x.kode===job())||{});

  function fillAo(){
    const office=el('hitungKpiKantor'), ao=el('hitungKpiAo'), branch=office?.value||'';
    const list=aoList().filter(x=>String(x.kode_kantor)===String(branch));
    ao.innerHTML='<option value="">'+(branch?'Pilih AO':'Pilih kantor dahulu')+'</option>'+list.map(x=>`<option value="${esc(x.kode_ao)}">${esc(x.nama_ao)} · ${esc(x.kode_ao)}</option>`).join('');
    ao.disabled=!branch;
    render();
  }
  function fill(d,keepOffice=''){
    state.boot=d; window.__hitungKpiBoot=d;
    const jobs=d.jabatan||[], jobSelect=el('hitungKpiJabatan'), selected=d.jabatan_terpilih?.kode||job();
    jobSelect.innerHTML=jobs.map(x=>`<option value="${esc(x.kode)}">${esc(x.nama)}</option>`).join('');
    if(jobs.some(x=>x.kode===selected))jobSelect.value=selected;
    const dates=d.closing_dates||[], closing=el('hitungKpiClosing');
    closing.innerHTML='<option value="">Pilih closing</option>'+dates.map(x=>`<option value="${esc(x)}">${esc(x)}</option>`).join('');
    if(dates.length)closing.value=dates[dates.length-1];
    const office=el('hitungKpiKantor');
    office.innerHTML='<option value="">Pilih kantor dahulu</option>'+(d.kantor||[]).map(x=>`<option value="${esc(x.kode_kantor)}">${esc(x.kode_kantor)} · ${esc(x.nama_kantor)}</option>`).join('');
    if(keepOffice)office.value=keepOffice;
    el('hitungKpiAo').innerHTML='<option value="">Pilih kantor dahulu</option>';el('hitungKpiAo').disabled=true;
    state.detail=[];el('hitungKpiFinal').textContent='-';el('hitungKpiStatus').textContent='BELUM DIHITUNG';el('hitungKpiNote').textContent='Pilih jabatan, kantor, AO, dan closing';if(keepOffice)fillAo();else render();
  }
  function showValue(value,unit){return unit==='RUPIAH'?money(value):unit==='PERSEN'?pct(value):fmt(value)}
  function tukinFor(score){
    const rules=state.boot?.tukin_rules||[];
    const row=rules.find(x=>score>=num(x.min_skor)&& (x.max_skor===null||x.max_skor===undefined||score<num(x.max_skor)));
    return row?num(row.faktor_persen):null;
  }
  function sourceNote(x){
    const parts=[];
    if(x.catatan)parts.push(String(x.catatan));
    if(x.formula_key==='REPAYMENT_RATE')parts.push(`OS DPD 0 ${money(x.os_dpd0)} / OS kelolaan ${money(x.os_kelolaan)}`);
    if(x.formula_key==='MOB_6')parts.push(`OS menunggak MOB 1-6 ${money(x.os_mob_menunggak)} / total OS MOB 1-6 ${money(x.os_mob_total)}`);
    if(x.formula_key==='EARLY_RUN_OFF')parts.push(`OS pelunasan murni ${money(x.os_run_off)} / OS DPD 0 M-1 ${money(x.os_dpd0_m1)}`);
    if(x.input_pa)parts.push(`Input PA: ${x.input_pa}`);
    parts.push(`Target ${showValue(x.target,x.unit)} / Realisasi ${showValue(x.realisasi,x.unit)} / Indeks ${x.unit==='PERSEN'?pct(x.indeks):fmt(x.indeks)} / Skor ${fmt(x.skor)} dari 5`);
    return parts.join(' · ');
  }
  function existingFor(ao,closing){return (state.boot?.generated||[]).find(x=>{const ids=[x.id_peg,x.kode_ao].filter(Boolean).map(String),aoIds=[ao?.id_peg,ao?.kode_ao].filter(Boolean).map(String);return ids.some(id=>aoIds.includes(id))&&String(x.closing_date)===String(closing)})||null}
  function render(){
    const ao=currentAo(), j=currentJob(), rows=state.detail||[], body=el('hitungKpiBody');
    el('hitungKpiJobName').textContent=j.nama||job();el('hitungKpiJobMeta').textContent=j.deskripsi||'Indikator aktif sesuai jabatan';
    if(!rows.length){const saved=existingFor(ao,el('hitungKpiClosing')?.value);el('hitungKpiFinal').textContent=saved?fmt(saved.nilai_akhir):'-';el('hitungKpiStatus').textContent=saved?(saved.status||'SUDAH DIGENERATE'):'BELUM DIHITUNG';el('hitungKpiNote').textContent=saved?'Nilai tersimpan. Klik Hitung KPI untuk generate ulang.':'Pilih jabatan, kantor, AO, dan closing';}
    el('hitungKpiAoName').textContent=ao?.nama_ao||'-';el('hitungKpiAoMeta').textContent=ao?`${ao.kode_ao} · Kantor ${ao.kode_kantor}`:'Pilih kantor dan AO';
    if(!rows.length){body.innerHTML='<tr><td colspan="11" class="mb-empty">Pilih jabatan, kantor, AO, dan closing, lalu klik Hitung KPI.</td></tr>';return;}
    const totalScore=rows.reduce((sum,x)=>sum+num(x.skor),0),totalScore2=totalScore*20,totalWeight=rows.reduce((sum,x)=>sum+num(x.bobot),0),totalValue=rows.reduce((sum,x)=>sum+num(x.nilai_tertimbang),0),totalValue2=rows.reduce((sum,x)=>sum+num(x.nilai_100),0),tukin=tukinFor(totalValue2/20);
    const totalNote=`Total nilai ${fmt(totalValue)} dari 5 · Nilai 2 ${fmt(totalValue2)} dari 100 · Skor akhir ${fmt(totalValue2/20)} dari 5`;
    const totalRow=`<tr class="mb-total-row"><td class="mb-sticky-left mb-text-left"><strong>TOTAL</strong></td><td></td><td></td><td></td><td class="mb-num">${fmt(totalScore)}</td><td class="mb-num">${fmt(totalScore2)}</td><td class="mb-num">${pct(totalWeight)}</td><td class="mb-num mb-strong">${fmt(totalValue)}</td><td class="mb-num mb-strong">${fmt(totalValue2)}</td><td class="mb-num kpi-tukin">${tukin===null?'-':fmt(tukin)+'%'}</td><td class="kpi-note">${esc(totalNote)}</td></tr>`;
    body.innerHTML=totalRow+rows.map(x=>`<tr><td class="mb-sticky-left mb-text-left"><strong>${esc(x.nama)}</strong><small class="mb-subvalue">${esc(x.kelompok||'')}</small></td><td class="mb-num">${showValue(x.target,x.unit)}</td><td class="mb-num mb-strong">${showValue(x.realisasi,x.unit)}</td><td class="mb-num">${x.unit==='PERSEN'?pct(x.indeks):fmt(x.indeks)}</td><td class="mb-num">${fmt(x.skor)} / 5</td><td class="mb-num">${fmt(num(x.skor)*20)}</td><td class="mb-num">${pct(x.bobot)}</td><td class="mb-num mb-strong">${fmt(x.nilai_tertimbang)}</td><td class="mb-num mb-strong">${fmt(x.nilai_100)}</td><td class="mb-num">-</td><td class="kpi-note" title="${esc(sourceNote(x))}">${esc(sourceNote(x))}</td></tr>`).join('');
  }
  async function boot(){try{fill(await post({type:'directory',year:el('hitungKpiYear').value||new Date().getFullYear(),jabatan_kode:job(),include_generated:true}));}catch(e){el('hitungKpiBody').innerHTML=`<tr><td colspan="8" class="mb-empty mb-negative">${esc(e.message)}</td></tr>`}}
  function chooseRegenerate(saved){const modal=el('hitungKpiGenerateConfirm'),title=el('hitungKpiGenerateConfirmTitle'),message=el('hitungKpiGenerateConfirmMessage');if(!modal)return Promise.resolve('cancel');const all=modal.querySelector('[data-kpi-generate-choice="all"]'),pending=modal.querySelector('[data-kpi-generate-choice="pending"]'),cancel=modal.querySelector('[data-kpi-generate-choice="cancel"]'),original={all:all?.textContent,pending:pending?.textContent};title.textContent='Penilaian periode ini sudah tersedia.';message.textContent=`Nilai tersimpan ${fmt(saved.nilai_akhir)} untuk closing ${saved.closing_date}. Generate ulang untuk memperbarui hasil perhitungan?`;if(all)all.textContent='Generate Ulang';if(pending){pending.textContent='Batal';pending.dataset.kpiGenerateChoice='cancel';}if(cancel)cancel.classList.add('is-hidden');ui().openModal?.('hitungKpiGenerateConfirm');return new Promise(resolve=>{let settled=false;const onKey=e=>{if(e.key==='Escape')finish('cancel')};const finish=value=>{if(settled)return;settled=true;document.removeEventListener('keydown',onKey);if(all)all.textContent=original.all;if(pending){pending.textContent=original.pending;pending.dataset.kpiGenerateChoice='pending';}if(cancel)cancel.classList.remove('is-hidden');ui().closeModal?.('hitungKpiGenerateConfirm');resolve(value)};document.addEventListener('keydown',onKey);modal.querySelectorAll('[data-kpi-generate-choice]').forEach(button=>{button.onclick=()=>finish(button.dataset.kpiGenerateChoice)});modal.querySelectorAll('[data-mb-close-modal]').forEach(button=>{button.onclick=()=>finish('cancel')});});}
  async function run(){
    const ao=currentAo(),closing=el('hitungKpiClosing').value;
    if(!ao||!closing)return alert('Pilih jabatan, kantor, AO, dan closing terlebih dahulu.');
    const saved=existingFor(ao,closing);if(saved){const choice=await chooseRegenerate(saved);if(choice!=='all')return;}
    const btn=el('hitungKpiRun');btn.disabled=true;ui().showLoading?.('hitungKpiLoading',true);
    try{await post({type:'calculate',year:el('hitungKpiYear').value,jabatan_kode:job(),kode_ao:ao.kode_ao,id_peg:ao.id_peg,kode_kantor:ao.kode_kantor,closing_date:closing});state.boot.generated=state.boot.generated||[];if(!state.boot.generated.some(x=>String(x.id_peg||x.kode_ao)===String(ao.id_peg||ao.kode_ao)&&x.closing_date===closing))state.boot.generated.push({id_peg:ao.id_peg,kode_ao:ao.kode_ao,kode_kantor:ao.kode_kantor,closing_date:closing});const e=await post({type:'evaluation',year:el('hitungKpiYear').value,jabatan_kode:job(),kode_ao:ao.kode_ao,closing_date:closing}),row=e.data?.[0];state.detail=row?(await post({type:'detail',penilaian_id:row.id})).data||[]:[];el('hitungKpiFinal').textContent=row?fmt(row.nilai_akhir):'-';el('hitungKpiStatus').textContent=row?.status||'DRAFT';el('hitungKpiNote').textContent=row?.predikat||'Belum lengkap';render();}catch(e){alert(e.message)}finally{btn.disabled=false;ui().showLoading?.('hitungKpiLoading',false)}
  }
  el('hitungKpiJabatan')?.addEventListener('change',boot);el('hitungKpiYear')?.addEventListener('change',boot);el('hitungKpiKantor')?.addEventListener('change',async()=>{const branch=el('hitungKpiKantor').value;if(!branch){state.boot.ao=[];fillAo();return}try{fill(await post({type:'bootstrap',year:el('hitungKpiYear').value||new Date().getFullYear(),jabatan_kode:job(),kode_kantor:branch}),branch)}catch(e){alert(e.message)}});el('hitungKpiAo')?.addEventListener('change',()=>{state.detail=[];render()});el('hitungKpiClosing')?.addEventListener('change',()=>{state.detail=[];render()});el('hitungKpiRun')?.addEventListener('click',run);el('hitungKpiSearch')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('#hitungKpiBody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(q)?'':'none')});

  const runButton=el('hitungKpiRun'), tools=document.querySelector('#hitungKpiAoPage .mb-report-toolbar__tools'), page=document.querySelector('#hitungKpiAoPage .mb-report-card');
  if(runButton&&tools&&page){
    const switcher=document.createElement('button');switcher.type='button';switcher.className='mb-view-switch';switcher.title='Buka Generate Cabang';switcher.setAttribute('aria-label','Buka Generate Cabang');switcher.innerHTML='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h10M7 17h10M5 7l2-2 2 2M19 17l-2 2-2-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';tools.insertBefore(switcher,runButton);
    const panel=document.createElement('div');panel.className='mb-kpi-bulk is-hidden';panel.innerHTML='<div class="mb-kpi-bulk__head"><strong>Generate KPI per Cabang</strong><span>Pilih jabatan dan kantor pada filter di atas, lalu generate semua AO pada kantor tersebut.</span></div><div id="hitungKpiBulkStatus" class="mb-kpi-bulk__status">Belum dijalankan.</div><div class="mb-kpi-bulk__table-wrap"><table class="mb-table"><thead><tr><th>AO</th><th>CABANG</th><th>CLOSING</th><th>STATUS</th><th>NILAI</th></tr></thead><tbody id="hitungKpiBulkBody"><tr><td colspan="5" class="mb-empty">Hasil generate akan tampil di sini.</td></tr></tbody></table></div>';page.appendChild(panel);
    const single=[page.querySelector('.mb-summary'),page.querySelector('.mb-table-region')].filter(Boolean);const activate=mode=>{state.mode=mode;single.forEach(x=>x.classList.toggle('is-hidden',mode!=='single'));panel.classList.toggle('is-hidden',mode!=='bulk');el('hitungKpiAoTitle').textContent=mode==='single'?'Detail Penilaian Bulanan':'Generate KPI per Cabang';runButton.title=mode==='single'?'Hitung AO terpilih':'Generate KPI per cabang';runButton.setAttribute('aria-label',runButton.title);runButton.innerHTML=mode==='single'?'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>':'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5v14h14M8 9h8M8 13h8M8 17h5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';switcher.title=mode==='single'?'Buka Generate Cabang':'Kembali ke Nilai AO';switcher.setAttribute('aria-label',switcher.title)};activate(<?= json_encode($hitungKpiDefaultMode) ?>==='bulk'||new URLSearchParams(location.search).get('mode')==='bulk'?'bulk':'single');switcher.addEventListener('click',()=>activate(state.mode==='single'?'bulk':'single'));
    runButton.addEventListener('click',e=>{if(state.mode!=='bulk')return;e.preventDefault();e.stopImmediatePropagation();bulk()},{capture:true});
    function chooseGenerateMode(existing,total){const modal=el('hitungKpiGenerateConfirm'),title=el('hitungKpiGenerateConfirmTitle'),message=el('hitungKpiGenerateConfirmMessage');if(!modal)return Promise.resolve('pending');title.textContent=`${existing} dari ${total} periode sudah pernah digenerate.`;message.textContent='Generate Semua akan menghitung ulang seluruh periode. Yang Belum hanya memproses periode yang belum tersimpan.';ui().openModal?.('hitungKpiGenerateConfirm');return new Promise(resolve=>{let settled=false;const onKey=e=>{if(e.key==='Escape')finish('cancel')};const finish=value=>{if(settled)return;settled=true;document.removeEventListener('keydown',onKey);ui().closeModal?.('hitungKpiGenerateConfirm');resolve(value)};document.addEventListener('keydown',onKey);modal.querySelectorAll('[data-kpi-generate-choice]').forEach(button=>{button.onclick=()=>finish(button.dataset.kpiGenerateChoice)});modal.querySelectorAll('[data-mb-close-modal]').forEach(button=>{button.onclick=()=>finish('cancel')});});}
    async function bulk(){const branch=el('hitungKpiKantor').value;if(!branch)return alert('Pilih kantor terlebih dahulu.');const status=el('hitungKpiBulkStatus'),body=el('hitungKpiBulkBody'),bootData=window.__hitungKpiBoot||state.boot,aos=aoList().filter(x=>String(x.kode_kantor)===String(branch)),dates=bootData?.closing_dates||[];const generated=new Set((bootData?.generated||[]).map(x=>`${x.id_peg||x.kode_ao}|${x.closing_date}`));const jobs=aos.flatMap(person=>dates.map(date=>({person,date,key:`${person.id_peg||person.kode_ao}|${date}`})));const existing=jobs.filter(x=>generated.has(x.key)).length;let mode='pending';if(existing){mode=await chooseGenerateMode(existing,jobs.length);if(mode==='cancel')return;}const work=mode==='all'?jobs:jobs.filter(x=>!generated.has(x.key));body.innerHTML='';let done=0,failed=0;status.textContent=work.length?`Memproses ${work.length} penilaian${existing?` (${existing} sudah ada)`:''}...`:'Semua periode sudah pernah digenerate.';for(const item of work){const person=item.person,date=item.date,row=document.createElement('tr');row.innerHTML=`<td>${esc(person.nama_ao)}</td><td>${esc(person.kode_kantor)}</td><td>${esc(date)}</td><td>Memproses...</td><td>-</td>`;body.appendChild(row);try{const result=await post({type:'calculate',year:el('hitungKpiYear').value,jabatan_kode:job(),kode_ao:person.kode_ao,id_peg:person.id_peg,kode_kantor:person.kode_kantor,closing_date:date});row.cells[3].textContent=job()==='AO_KREDIT'?'Selesai':'Selesai · DUMMY';row.cells[4].textContent=fmt(result.data?.[0]?.nilai_akhir||0);done++;status.textContent=`Selesai ${done} dari ${work.length} penilaian.`}catch(e){row.cells[3].textContent='Gagal';row.cells[4].textContent=e.message;failed++}}const message=el('hitungKpiSuccessMessage');if(message)message.textContent=`${done} penilaian berhasil digenerate${failed?`, ${failed} gagal`:''} untuk ${aos.length} AO pada kantor ${branch}.`;window.MonbisUI?.openModal?.('hitungKpiSuccess')}
  }
  // Perubahan kantor cukup mengambil daftar AO saja. Listener capture ini
  // mencegah bootstrap penuh dijalankan ulang setiap kali filter berubah.
  el('hitungKpiKantor')?.addEventListener('change',async event=>{
    event.stopImmediatePropagation();
    const branch=el('hitungKpiKantor').value;
    if(!branch){state.boot.ao=[];fillAo();return}
    try{
      const data=await post({type:'ao_list',jabatan_kode:job(),kode_kantor:branch});
      state.boot.ao=data.ao||[];
      fillAo();
    }catch(error){alert(error.message)}
  },true);
  boot();
})();
</script>

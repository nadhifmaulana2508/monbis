<?php
require_once __DIR__ . '/../components/bootstrap.php'; mb_ui_assets('.');
$head=mb_build_grouped_thead([
 ['label'=>'INDIKATOR','class'=>'mb-sticky-left mb-text-left','attrs'=>['style'=>'width:220px']],['label'=>'TARGET','class'=>'mb-group--blue','attrs'=>['style'=>'width:130px']],['label'=>'REALISASI','class'=>'mb-group--green','attrs'=>['style'=>'width:130px']],['label'=>'INDEKS','attrs'=>['style'=>'width:90px']],['label'=>'SKOR','attrs'=>['style'=>'width:70px']],['label'=>'BOBOT','attrs'=>['style'=>'width:80px']],['label'=>'NILAI','class'=>'mb-group--green','attrs'=>['style'=>'width:90px']],['label'=>'KETERANGAN','attrs'=>['style'=>'width:180px']]
]);
mb_render_report_page(['id'=>'hitungKpiAoPage','class'=>'mb-kpi-calc-page','header'=>['id'=>'hitungKpiAoHeader','title'=>'Penilaian KPI AO Kredit','subtitle'=>'Hitung nilai bulanan satu AO berdasarkan realisasi kredit dan NOA Baru.','icon'=>mb_svg('chart'),'info_modal_id'=>'hitungKpiAoInfo','filters'=>[
 ['id'=>'hitungKpiYear','label'=>'Tahun','type'=>'number','width'=>'85px','value'=>date('Y')],['id'=>'hitungKpiClosing','label'=>'Closing','type'=>'select','width'=>'140px','options'=>[''=>'Memuat...']],['id'=>'hitungKpiKantor','label'=>'Kantor','type'=>'select','width'=>'150px','options'=>[''=>'Pilih kantor dahulu']],['id'=>'hitungKpiAo','label'=>'AO Kredit','type'=>'select','width'=>'260px','options'=>[''=>'Pilih kantor dahulu']]
]],'toolbar'=>['title'=>'Detail Penilaian Bulanan','title_id'=>'hitungKpiAoTitle','search'=>['id'=>'hitungKpiSearch','placeholder'=>'Cari indikator...'],'actions'=>[['attrs'=>['id'=>'hitungKpiRun'],'tone'=>'success','icon'=>'chart','title'=>'Hitung AO terpilih','aria_label'=>'Hitung AO terpilih','label'=>'Hitung KPI']]],'legend_html'=>'<div class="mb-summary"><div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">AO TERPILIH</div><div id="hitungKpiAoName" class="mb-summary-card__value">-</div><div id="hitungKpiAoMeta" class="mb-summary-card__meta">Pilih AO Kredit</div></div><div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">TARGET REALISASI</div><div class="mb-summary-card__value">Rp 400 Jt</div><div class="mb-summary-card__meta">Target awal per bulan</div></div><div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">NILAI AKHIR</div><div id="hitungKpiFinal" class="mb-summary-card__value">-</div><div class="mb-summary-card__meta">Kontribusi indikator terpilih</div></div><div class="mb-summary-card mb-summary-card--red"><div class="mb-summary-card__label">STATUS</div><div id="hitungKpiStatus" class="mb-summary-card__value">BELUM DIHITUNG</div><div id="hitungKpiNote" class="mb-summary-card__meta">Pilih AO dan closing</div></div></div><div class="mb-kpi-targets"><label>Target Realisasi<input id="hitungKpiTargetRealisasi" type="number" min="0" step="1000000" value="400000000"></label><label>Target NOA Baru<input id="hitungKpiTargetNoa" type="number" min="0" step="1" value="10"></label><small>Target NOA dapat disesuaikan dengan kebijakan kantor/periode.</small></div>','table'=>['wrapper_id'=>'hitungKpiTableWrap','table_id'=>'hitungKpiTable','loading_id'=>'hitungKpiLoading','loading_text'=>'Menghitung KPI...','thead_html'=>$head,'tbody_ids'=>['hitungKpiBody']]]);
mb_render_info_modal(['id'=>'hitungKpiAoInfo','title'=>'Cara Hitung KPI AO Kredit','subtitle'=>'Perhitungan satu AO untuk satu closing bulanan.','body_html'=>'<div class="mb-npl-brief"><div class="mb-npl-brief__alert"><strong>Fokus tahap awal: Realisasi Kredit dan NOA Baru.</strong><span>Realisasi diambil dari update_realisasi_kredit dengan kode transaksi 110 pada bulan berjalan. NOA Baru memakai kolom baru_lama berstatus nasabah baru.</span></div><div class="mb-npl-brief__priority-grid"><div class="mb-npl-brief__priority mb-npl-brief__priority--blue"><b>1</b><div><strong>Target</strong><span>Realisasi default Rp400 juta dan target NOA bisa disesuaikan.</span></div></div><div class="mb-npl-brief__priority mb-npl-brief__priority--violet"><b>2</b><div><strong>Indeks</strong><span>Realisasi dibagi target, lalu dicocokkan ke range skor 1–5.</span></div></div><div class="mb-npl-brief__priority mb-npl-brief__priority--red"><b>3</b><div><strong>Nilai</strong><span>Skor dikalikan bobot indikator dan ditampilkan sebagai kontribusi nilai.</span></div></div></div></div>']);
mb_render_info_modal(['id'=>'hitungKpiSuccess','title'=>'Generate KPI Berhasil','subtitle'=>'Penilaian cabang sudah selesai diproses.','body_id'=>'hitungKpiSuccessBody','body_html'=>'<div class="mb-kpi-success"><div class="mb-kpi-success__icon">✓</div><strong id="hitungKpiSuccessMessage">Generate KPI selesai.</strong><span>Hasil penilaian dapat dilihat pada tabel Generate Cabang atau menu Rekap KPI.</span></div>']);
?>
<style>
.mb-kpi-targets{display:none!important}
.mb-kpi-targets label{display:flex;flex-direction:column;gap:4px;font-size:10px;font-weight:700;color:var(--mb-muted,#52627a)}
.mb-kpi-targets input{width:150px;padding:8px 9px;border:1px solid var(--mb-border,#cbd8e8);border-radius:8px;background:var(--mb-card,#fff);font:inherit;color:inherit}
.mb-kpi-targets small{font-size:10px;color:var(--mb-muted,#718096)}
.mb-kpi-tabs{display:flex;align-items:center;gap:4px;padding:5px 7px;border:1px solid var(--mb-border,#dbe4ef);border-bottom:0;border-radius:10px 10px 0 0;background:var(--mb-card,#fff)}
.mb-kpi-tabs{display:none}.mb-kpi-calc-page .mb-view-switch{display:inline-flex;width:34px;height:30px}.mb-kpi-calc-page .mb-view-switch span{font-size:16px;font-weight:900;line-height:1}
.mb-kpi-tabs button{display:inline-flex;align-items:center;justify-content:center;min-height:30px;padding:0 13px;border:1px solid var(--mb-border,#dbe4ef);border-radius:7px;background:var(--mb-soft,#f8fafc);color:var(--mb-muted,#64748b);font:800 10px inherit;cursor:pointer;transition:.15s ease}
.mb-kpi-tabs button:hover{border-color:#2563eb;color:#2563eb}.mb-kpi-tabs button.is-active{border-color:#2563eb;background:#2563eb;color:#fff;box-shadow:0 4px 10px rgba(37,99,235,.18)}
.mb-kpi-bulk{padding:12px;border:1px solid var(--mb-border,#dbe4ef);border-radius:0 0 10px 10px;background:var(--mb-card,#fff)}
.mb-kpi-bulk__head{display:flex;flex-direction:column;gap:3px;margin-bottom:10px;color:var(--mb-muted,#64748b);font-size:10px}.mb-kpi-bulk__head strong{color:var(--mb-ink,#0f172a);font-size:13px}
.mb-kpi-bulk__status{margin:10px 0;color:#2563eb;font-size:10px;font-weight:800}.mb-kpi-bulk__table-wrap{max-height:420px;overflow:auto}.mb-kpi-bulk__table-wrap .mb-table{min-width:100%}
.mb-kpi-bulk-start{width:auto!important;min-width:0!important;padding:0 12px!important}.mb-kpi-bulk-start .mb-icon-button__label{display:inline!important}
.mb-kpi-success{display:flex;flex-direction:column;align-items:center;gap:8px;padding:12px;text-align:center;color:var(--mb-muted,#64748b)}.mb-kpi-success strong{color:var(--mb-ink,#0f172a);font-size:13px}.mb-kpi-success__icon{display:grid;place-items:center;width:38px;height:38px;border-radius:50%;background:#dcfce7;color:#059669;font-size:22px;font-weight:900}
@media(max-width:640px){.mb-kpi-tabs button{flex:1;padding:0 6px;font-size:9px}.mb-kpi-bulk{padding:8px}.mb-kpi-bulk__table-wrap{max-height:300px}}
</style>
<script>
window.MonbisKpiHeaders=window.MonbisKpiHeaders||function(){return {'Content-Type':'application/json'}};
(()=>{const API='./api/index.php?request=kpi',el=id=>document.getElementById(id),ui=()=>window.MonbisUI||{},esc=v=>ui().escape?ui().escape(v):String(v??''),num=v=>Number(v||0),fmt=v=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:2}).format(num(v)),money=v=>'Rp '+new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(num(v)),pct=v=>fmt(num(v)*100)+'%',post=async body=>{const r=await fetch(API,{method:'POST',credentials:'same-origin',headers:window.MonbisKpiHeaders(),body:JSON.stringify(body)}),j=await r.json();if(!r.ok||Number(j.status)!==200)throw Error(j.message||'Gagal memuat KPI');return j.data||{}},state={boot:null,detail:[]};
function fill(d){state.boot=d;window.__hitungKpiBoot=d;const dates=d.closing_dates||[],ao=d.ao_kredit||[];el('hitungKpiClosing').innerHTML=dates.map(x=>`<option value="${esc(x)}">${esc(x)}</option>`).join('');el('hitungKpiAo').innerHTML='<option value="">Pilih AO Kredit</option>'+ao.map(x=>`<option value="${esc(x.kode_ao)}">${esc(x.kode_ao)} · ${esc(x.nama_ao)} · ${esc(x.kode_kantor)}</option>`).join('');if(dates.length)el('hitungKpiClosing').value=dates[dates.length-1];document.dispatchEvent(new Event('hitung-kpi-ready'))}
function selectedAo(){return(state.boot?.ao_kredit||[]).find(x=>x.kode_ao===el('hitungKpiAo').value)}
function render(){const b=el('hitungKpiBody'),rows=state.detail||[],ao=selectedAo(),show=x=>x.unit==='RUPIAH'?money(x):x.unit==='PERSEN'?pct(x):fmt(x);el('hitungKpiAoName').textContent=ao?.nama_ao||'-';el('hitungKpiAoMeta').textContent=ao?`${ao.kode_ao} · KCU ${ao.kode_kantor}`:'Pilih AO Kredit';b.innerHTML=rows.length?rows.map(x=>`<tr><td class="mb-sticky-left mb-text-left"><strong>${esc(x.nama)}</strong><small class="mb-subvalue">${esc(x.kelompok||'')}</small></td><td class="mb-num">${show(x.target)}</td><td class="mb-num mb-strong">${show(x.realisasi)}</td><td class="mb-num">${x.unit==='PERSEN'?pct(x.indeks):fmt(x.indeks)}</td><td class="mb-num">${fmt(x.skor)} / 5</td><td class="mb-num">${pct(x.bobot)}</td><td class="mb-num mb-strong">${fmt(x.nilai_100)}</td><td>${x.formula_key==='MOB_6'?`OS ${money(x.os_mob_menunggak)} / ${money(x.os_mob_total)} · ${pct(x.realisasi)}`:x.formula_key==='REPAYMENT_RATE'?`OS DPD 0 ${money(x.os_dpd0)} / OS Kelolaan ${money(x.os_kelolaan)} · ${pct(x.realisasi)}`:x.formula_key==='EARLY_RUN_OFF'?`OS Lunas Murni ${money(x.os_run_off)} / OS DPD 0 M-1 ${money(x.os_dpd0_m1)} · ${pct(x.realisasi)}`:esc(x.catatan||'Terhitung')}</td></tr>`).join(''):'<tr><td colspan="8" class="mb-empty">Pilih AO dan closing, lalu klik Hitung KPI.</td></tr>'}
async function boot(){try{fill(await post({type:'bootstrap',year:el('hitungKpiYear').value||new Date().getFullYear()}));render()}catch(e){el('hitungKpiBody').innerHTML=`<tr><td colspan="8" class="mb-empty mb-negative">${esc(e.message)}</td></tr>`}}
async function run(){const ao=el('hitungKpiAo').value,closing=el('hitungKpiClosing').value;if(!ao||!closing)return alert('Pilih AO Kredit dan closing terlebih dahulu.');const btn=el('hitungKpiRun');btn.disabled=true;ui().showLoading?.('hitungKpiLoading',true);try{await post({type:'calculate',year:el('hitungKpiYear').value,kode_ao:ao,closing_date:closing,indicator_codes:['PENCAIRAN_NETO','NOA_BARU','MOB_6','REPAYMENT_RATE','EARLY_RUN_OFF','PIPELINE']});const e=await post({type:'evaluation',year:el('hitungKpiYear').value,kode_ao:ao,closing_date:closing}),row=e.data?.[0];state.detail=row?(await post({type:'detail',penilaian_id:row.id})).data||[]:[];el('hitungKpiFinal').textContent=row?fmt(row.nilai_akhir):'-';el('hitungKpiStatus').textContent=row?.status||'DRAFT';el('hitungKpiNote').textContent=row?.predikat||'Belum lengkap';render()}catch(e){alert(e.message)}finally{btn.disabled=false;ui().showLoading?.('hitungKpiLoading',false)}}
el('hitungKpiRun')?.addEventListener('click',run);el('hitungKpiAo')?.addEventListener('change',render);el('hitungKpiSearch')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('#hitungKpiBody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(q)?'':'none')});el('hitungKpiYear')?.addEventListener('change',boot);boot();})();
</script>
<script>
(() => {
  document.querySelector('.mb-kpi-targets')?.remove();
  const card = document.querySelector('#hitungKpiAoPage .mb-summary-card--amber');
  if (!card) return;
  const label = card.querySelector('.mb-summary-card__label');
  const value = card.querySelector('.mb-summary-card__value');
  const meta = card.querySelector('.mb-summary-card__meta');
  if (label) label.textContent = 'TARGET DEFAULT';
  if (value) value.textContent = '-';
  if (meta) meta.textContent = 'Diatur dari Setting KPI Jabatan';
})();
</script>
<script>
(() => {
  const office = document.getElementById('hitungKpiKantor'), ao = document.getElementById('hitungKpiAo');
  if (!office || !ao) return;
  const esc = value => window.MonbisUI?.escape ? window.MonbisUI.escape(value) : String(value ?? '');
  const post = async body => { const r=await fetch('./api/index.php?request=kpi',{method:'POST',credentials:'same-origin',headers:window.MonbisKpiHeaders(),body:JSON.stringify(body)}); const j=await r.json(); if(!r.ok||Number(j.status)!==200) throw Error(j.message||'Gagal'); return j.data||{}; };
  let data;
  setTimeout(async () => {
    try {
      data = window.__hitungKpiBoot || await post({type:'bootstrap',year:document.getElementById('hitungKpiYear').value});
      office.innerHTML='<option value="">Pilih kantor dahulu</option>'+(data.kantor||[]).map(k=>`<option value="${esc(k.kode_kantor)}">${esc(k.kode_kantor)} · ${esc(k.nama_kantor)}</option>`).join('');
      ao.innerHTML='<option value="">Pilih kantor dahulu</option>';
    } catch(e) {}
  }, 700);
  office.addEventListener('change', () => {
    const branch=office.value, list=(data?.ao_kredit||[]).filter(x=>x.kode_kantor===branch);
    ao.innerHTML='<option value="">Pilih AO Kredit</option>'+list.map(x=>`<option value="${esc(x.kode_ao)}">${esc(x.kode_ao)} · ${esc(x.nama_ao)}</option>`).join('');
    ao.dispatchEvent(new Event('change'));
  });
})();
</script>
<script>
(() => {
  const run = document.getElementById('hitungKpiRun');
  const ao = document.getElementById('hitungKpiAo');
  const office = document.getElementById('hitungKpiKantor');
  if (!run || !ao || !office) return;
  const toolbar = run.parentElement;
  const tabs = document.createElement('div');
  tabs.className = 'mb-kpi-tabs';
  tabs.innerHTML = '<button type="button" class="is-active" data-tab="single">Nilai AO</button><button type="button" data-tab="bulk">Generate Cabang</button>';
  const card = document.getElementById('hitungKpiAoPage');
  const bulkPanel = document.createElement('div');
  bulkPanel.className = 'mb-kpi-bulk is-hidden';
  bulkPanel.innerHTML = '<div class="mb-kpi-bulk__head"><strong>Generate KPI per Cabang</strong><span>Pilih kantor pada filter di atas, lalu gunakan tombol aksi di toolbar untuk generate semua AO aktif pada kantor tersebut.</span></div><button type="button" id="hitungKpiBulkStart" class="mb-icon-button mb-icon-button--primary mb-kpi-bulk-start" style="display:none" aria-hidden="true"><span class="mb-icon-button__label">Generate Cabang</span></button><div id="hitungKpiBulkStatus" class="mb-kpi-bulk__status">Belum dijalankan.</div><div class="mb-kpi-bulk__table-wrap"><table class="mb-table"><thead><tr><th>AO</th><th>CABANG</th><th>CLOSING</th><th>STATUS</th><th>NILAI</th></tr></thead><tbody id="hitungKpiBulkBody"><tr><td colspan="5" class="mb-empty">Hasil generate akan tampil di sini.</td></tr></tbody></table></div>';
  const page = document.querySelector('#hitungKpiAoPage .mb-report-card') || card;
  page?.parentElement?.insertBefore(tabs, page);
  page?.appendChild(bulkPanel);
  const singleParts=[page.querySelector('.mb-summary'),page.querySelector('.mb-kpi-targets'),page.querySelector('.mb-table-region')].filter(Boolean);
  const switcher=document.createElement('button');
  switcher.type='button'; switcher.id='hitungKpiViewSwitch'; switcher.className='mb-view-switch'; switcher.title='Ganti tampilan'; switcher.setAttribute('aria-label','Ganti tampilan');
  switcher.innerHTML='<span aria-hidden="true">↔</span>';
  switcher.innerHTML='<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h10M7 17h10M5 7l2-2 2 2M19 17l-2 2-2-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  const tools=document.querySelector('#hitungKpiAoPage .mb-report-toolbar__tools');
  const runLabel=run.querySelector('.mb-icon-button__label');
  if(runLabel)runLabel.remove();
  tools?.insertBefore(switcher,run);
  function activate(name) { window.__hitungKpiMode=name; tabs.querySelectorAll('button').forEach(b => b.classList.toggle('is-active', b.dataset.tab === name)); singleParts.forEach(part=>part.classList.toggle('is-hidden',name!=='single')); bulkPanel.classList.toggle('is-hidden', name !== 'bulk'); document.getElementById('hitungKpiAoTitle').textContent=name==='single'?'Detail Penilaian Bulanan':'Generate KPI per Cabang'; run.title=name==='single'?'Hitung KPI AO terpilih':'Generate KPI per cabang'; run.setAttribute('aria-label',run.title); run.innerHTML=name==='single'?'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>':'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5v14h14M8 9h8M8 13h8M8 17h5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'; switcher.title=name==='single'?'Buka Generate Cabang':'Kembali ke Nilai AO'; }
  tabs.querySelectorAll('button').forEach(b => b.addEventListener('click', () => activate(b.dataset.tab)));
  switcher.addEventListener('click',()=>activate(bulkPanel.classList.contains('is-hidden')?'bulk':'single'));
  run.addEventListener('click',event=>{if(window.__hitungKpiMode==='bulk'){event.preventDefault();event.stopImmediatePropagation();document.getElementById('hitungKpiBulkStart')?.click();}},true);
  tabs.style.display='none';
  activate('single');
  document.getElementById('hitungKpiBulkStart').addEventListener('click', async () => {
    const branch = office.value;
    if (!branch) return alert('Pilih kantor terlebih dahulu.');
    const status = document.getElementById('hitungKpiBulkStatus'), body = document.getElementById('hitungKpiBulkBody');
    const post = async payload => { const r = await fetch('./api/index.php?request=kpi', {method:'POST',credentials:'same-origin',headers:window.MonbisKpiHeaders(),body:JSON.stringify(payload)}); const j=await r.json(); if(!r.ok||Number(j.status)!==200) throw Error(j.message||'Gagal'); return j.data||{}; };
    const boot = window.__hitungKpiBoot || await post({type:'bootstrap',year:document.getElementById('hitungKpiYear').value});
    const aos = (boot.ao_kredit||[]).filter(x => x.kode_kantor === branch), dates = boot.closing_dates||[];
    body.innerHTML=''; let done=0; status.textContent=`Memproses ${aos.length} AO × ${dates.length} closing...`;
    for (const person of aos) for (const closing of dates) {
      const row=document.createElement('tr'); row.innerHTML=`<td>${person.nama_ao}</td><td>${person.kode_kantor}</td><td>${closing}</td><td>Memproses...</td><td>-</td>`; body.appendChild(row);
      try { const result=await post({type:'calculate',year:document.getElementById('hitungKpiYear').value,kode_ao:person.kode_ao,closing_date:closing,indicator_codes:['PENCAIRAN_NETO','NOA_BARU','MOB_6','REPAYMENT_RATE','EARLY_RUN_OFF','PIPELINE']}); const saved=result.data?.[0]||{}; row.cells[3].textContent='Selesai'; row.cells[4].textContent=Number(saved.nilai_akhir||0).toFixed(2); done++; status.textContent=`Selesai ${done} dari ${aos.length*dates.length} penilaian.`; } catch(e) { row.cells[3].textContent='Gagal'; row.cells[4].textContent=e.message; }
     }
     const successMessage=document.getElementById('hitungKpiSuccessMessage');
     if(successMessage)successMessage.textContent=`${done} penilaian berhasil digenerate untuk ${aos.length} AO pada cabang ${branch||'-'}.`;
     window.MonbisUI?.openModal?.('hitungKpiSuccess');
   });
})();
</script>
<script>
(() => {
  const run = document.getElementById('hitungKpiRun');
  const aoSelect = document.getElementById('hitungKpiAo');
  if (!run || !aoSelect) return;
  // Bulk sudah ditangani oleh tab Generate Cabang di atas.
  return;
  /* const bulk = run.cloneNode(true);
  bulk.id = 'hitungKpiBulk';
  bulk.title = 'Generate semua AO pada cabang AO terpilih';
  bulk.setAttribute('aria-label', bulk.title);
  bulk.textContent = 'Generate Cabang';
  run.parentElement?.appendChild(bulk);
  bulk.addEventListener('click', async () => {
    const selected = aoSelect.value;
    if (!selected) return alert('Pilih salah satu AO untuk menentukan cabang.');
    if (!confirm('Generate KPI semua AO pada cabang AO terpilih untuk seluruh closing tahun ini?')) return;
    bulk.disabled = true;
    try {
      const post = async body => {
        const r = await fetch('./api/index.php?request=kpi', {method:'POST', credentials:'same-origin', headers:window.MonbisKpiHeaders(), body:JSON.stringify(body)});
        const j = await r.json(); if (!r.ok || Number(j.status) !== 200) throw Error(j.message || 'Gagal'); return j.data || {};
      };
      const boot = await post({type:'bootstrap', year:document.getElementById('hitungKpiYear').value});
      const current = (boot.ao_kredit || []).find(a => a.kode_ao === selected);
      const branch = current?.kode_kantor;
      const aos = (boot.ao_kredit || []).filter(a => !branch || a.kode_kantor === branch);
      const dates = boot.closing_dates || [];
      let done = 0;
      for (const ao of aos) for (const closing of dates) {
        await post({type:'calculate', year:document.getElementById('hitungKpiYear').value, kode_ao:ao.kode_ao, closing_date:closing});
        done++;
      }
      const successBody=document.getElementById('hitungKpiSuccessBody');
      const successMessage=document.getElementById('hitungKpiSuccessMessage');
      if(successMessage)successMessage.textContent=`${done} penilaian berhasil digenerate untuk ${aos.length} AO pada cabang ${branch||'-'}.`;
      if(successBody)successBody.setAttribute('data-generated-count',String(done));
      window.MonbisUI?.openModal?.('hitungKpiSuccess');
    } catch (e) { alert(e.message); }
    finally { bulk.disabled = false; }
  }); */
})();
</script>

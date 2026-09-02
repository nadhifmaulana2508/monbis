<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

$monthNames=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
$columns=[
    ['label'=>'KANTOR','class'=>'mb-text-left'],
    ['label'=>'AO / ID PEG','class'=>'mb-sticky-left mb-text-left'],
];
foreach($monthNames as $index=>$month)$columns[]=['label'=>$month,'class'=>'mb-group--blue kpi-month-col','attrs'=>['data-month'=>$index+1]];
$columns[]=['label'=>'RATA-RATA','class'=>'mb-group--green'];
$columns[]=['label'=>'SKOR'];
$columns[]=['label'=>'TUKIN'];
$columns[]=['label'=>'BULAN'];
$head=mb_build_grouped_thead($columns);
$breakdownHead=mb_build_grouped_thead([
    ['label'=>'KANTOR'],['label'=>'AO / ID PEG','class'=>'mb-sticky-left mb-text-left'],
    ['label'=>'INDIKATOR','class'=>'mb-sticky-left mb-text-left'],['label'=>'BULAN'],
    ['label'=>'NILAI AKHIR','class'=>'mb-group--green'],['label'=>'TUKIN'],
    ['label'=>'TARGET'],['label'=>'REALISASI','class'=>'mb-group--green'],['label'=>'INDEKS'],
    ['label'=>'SKOR'],['label'=>'BOBOT'],['label'=>'NILAI'],['label'=>'INPUT PA','class'=>'mb-text-left']
]);
$tabs=mb_render_icon_tabs([
    ['key'=>'summary','label'=>'Rekap AO','icon'=>mb_svg('chart')],
    ['key'=>'breakdown','label'=>'Breakdown indikator','icon'=>mb_svg('list')],
    ['key'=>'tukin','label'=>'Faktor tukin','icon'=>mb_svg('percent')],
], 'summary', ['class'=>'rekap-kpi-tabs','aria-label'=>'Tampilan rekap KPI']);

mb_render_report_page([
    'id'=>'rekapKpiAoPage',
    'header'=>[
        'id'=>'rekapKpiAoHeader', 'title'=>'Rekap KPI AO',
        'subtitle'=>'Rekap penilaian tahunan dari seluruh periode yang sudah digenerate.',
        'icon'=>mb_svg('chart'),
        'filters'=>[
            ['id'=>'rekapKpiJabatan','label'=>'Jabatan','type'=>'select','width'=>'145px','options'=>[''=>'Memuat...']],
            ['id'=>'rekapKpiYear','label'=>'Tahun','type'=>'number','width'=>'85px','value'=>date('Y')],
            ['id'=>'rekapKpiKantor','label'=>'Kantor','type'=>'select','width'=>'170px','options'=>[''=>'Semua kantor']],
            ['id'=>'rekapKpiAo','label'=>'AO','type'=>'select','width'=>'260px','options'=>[''=>'Semua AO']],
        ]
    ],
    'toolbar'=>['title'=>'Report Tahunan','before_html'=>$tabs,'search'=>['id'=>'rekapKpiSearch','placeholder'=>'Cari kantor / AO...']],
    'legend_html'=>'<div class="mb-summary">
        <div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">AO DINILAI</div><div id="rekapKpiCount" class="mb-summary-card__value">-</div><div id="rekapKpiCountMeta" class="mb-summary-card__meta">Sudah ada penilaian</div></div>
        <div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">PERIODE TERISI</div><div id="rekapKpiPeriod" class="mb-summary-card__value">-</div><div class="mb-summary-card__meta">Hanya bulan yang sudah digenerate</div></div>
        <div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">RATA-RATA NILAI</div><div id="rekapKpiFinal" class="mb-summary-card__value">-</div><div class="mb-summary-card__meta">Nilai berbobot / 100</div></div>
        <div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">MODE REKAP</div><div id="rekapKpiMode" class="mb-summary-card__value">Semua kantor</div><div class="mb-summary-card__meta">Urut nilai terbaik</div></div>
    </div>
    <div id="rekapKpiBreakdownPanel" class="rekap-kpi-extra-panel is-hidden">
      <div class="mb-section-title">Breakdown Semua Indikator</div>
      <div class="mb-table-scroll"><table class="mb-table" id="rekapKpiBreakdownTable"><thead>'.$breakdownHead.'</thead><tbody id="rekapKpiBreakdownBody"><tr><td colspan="13" class="mb-empty">Memuat breakdown indikator...</td></tr></tbody></table></div>
    </div>
    <div id="rekapKpiTukinPanel" class="rekap-kpi-extra-panel is-hidden">
      <div class="mb-section-title">Klasifikasi Faktor Tukin</div>
      <div class="mb-table-scroll"><table class="mb-table mb-kpi-tukin-table" id="rekapKpiTukinTable"><thead><tr><th>Skor KPI Final</th><th>Nilai KPI</th><th>Faktor Tukin</th></tr></thead><tbody id="rekapKpiTukinBody"><tr><td colspan="3" class="mb-empty">Memuat parameter tukin...</td></tr></tbody></table></div>
    </div>',
    'table'=>['wrapper_id'=>'rekapKpiTableWrap','table_id'=>'rekapKpiTable','loading_id'=>'rekapKpiLoading','loading_text'=>'Memuat rekap KPI...','thead_html'=>$head,'tbody_ids'=>['rekapKpiBody']]
]);
?>
<style>
  .rekap-kpi-tabs{margin-right:auto}
  .rekap-kpi-tabs .mb-segmented__btn{width:34px;min-width:34px;padding:0}
  .rekap-kpi-tabs .mb-segmented__btn svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
  .rekap-kpi-extra-panel{display:flex;flex-direction:column;min-height:0;margin-top:12px;padding:12px 14px;border:1px solid var(--mb-border,#dbe5f0);border-radius:10px;background:var(--mb-surface,#fff)}
  .rekap-kpi-extra-panel.is-hidden{display:none}
  .rekap-kpi-extra-panel .mb-section-title{font-weight:700;margin-bottom:8px;color:var(--mb-text,#10203a)}
  .rekap-kpi-extra-panel .mb-table-scroll{min-height:0;max-height:calc(100dvh - 285px);overflow:auto;-webkit-overflow-scrolling:touch;overscroll-behavior:contain;scrollbar-width:thin;scrollbar-color:var(--mb-scroll-thumb,#94a3b8) var(--mb-scroll-track,#f1f5f9)}
  .rekap-kpi-extra-panel .mb-table-scroll::-webkit-scrollbar{width:5px;height:5px}
  .rekap-kpi-extra-panel .mb-table-scroll::-webkit-scrollbar-track{background:var(--mb-scroll-track,#f1f5f9);border-radius:999px}
  .rekap-kpi-extra-panel .mb-table-scroll::-webkit-scrollbar-thumb{background:var(--mb-scroll-thumb,#94a3b8);border-radius:999px}
  #rekapKpiAoPage .mb-table-region{min-height:0;flex:1 1 0}
  #rekapKpiAoPage #rekapKpiTableWrap{min-height:0;height:100%;overflow:auto}
  #rekapKpiBreakdownTable{min-width:1180px}
  #rekapKpiBreakdownTable .kpi-input-pa{min-width:240px;max-width:320px;white-space:normal;line-height:1.35}
  #rekapKpiTable th,#rekapKpiTable td{white-space:nowrap}
  #rekapKpiTable th:first-child,#rekapKpiTable td:first-child{width:62px;min-width:62px}
  #rekapKpiTable th:nth-child(2),#rekapKpiTable .kpi-ao-cell{width:220px;min-width:220px;max-width:260px}
  #rekapKpiTable .kpi-ao-cell{white-space:normal;overflow:hidden;text-overflow:ellipsis}
  #rekapKpiTable .kpi-month-cell{min-width:67px;text-align:center}
  #rekapKpiTable .kpi-month-cell small{display:block;color:var(--mb-muted,#64748b);font-size:9px;margin-top:2px}
  #rekapKpiTable .kpi-empty{color:#94a3b8}
  #rekapKpiTable .kpi-tukin{font-weight:700;color:#008f68}
  .mb-kpi-tukin-table{width:100%;font-size:12px}
  .mb-kpi-tukin-table th,.mb-kpi-tukin-table td{padding:7px 10px;text-align:left;border-bottom:1px solid var(--mb-border,#e5edf5)}
  .mb-kpi-tukin-table th:last-child,.mb-kpi-tukin-table td:last-child{text-align:center}
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #rekapKpiAoPage .rekap-kpi-extra-panel{border-color:#334155;background:#111827}
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #rekapKpiAoPage .rekap-kpi-extra-panel .mb-section-title{color:#e2e8f0}
  @media(max-width:1023px){
    #rekapKpiAoPage .rekap-kpi-extra-panel .mb-table-scroll{max-height:calc(100dvh - 360px)}
    #rekapKpiAoPage .mb-report-toolbar__tools{display:flex;width:auto;flex-wrap:nowrap;justify-content:flex-end;gap:5px}
    #rekapKpiAoPage .rekap-kpi-tabs{flex:0 0 auto;margin:0}
    #rekapKpiAoPage .rekap-kpi-tabs .mb-segmented__btn{flex:0 0 29px}
    #rekapKpiAoPage .mb-search{flex:0 0 84px;width:84px;min-width:0}
  }
  @media(max-width:767px){
    #rekapKpiAoPage .mb-report-toolbar{display:grid;grid-template-columns:minmax(74px,1fr) auto;align-items:center;gap:6px;padding:6px}
    #rekapKpiAoPage .mb-report-toolbar__tools{width:auto;gap:4px}
    #rekapKpiAoPage .mb-report-toolbar__title{font-size:10.5px}
    #rekapKpiTable th:first-child,#rekapKpiTable td:first-child{width:58px;min-width:58px}
    #rekapKpiTable th:nth-child(2),#rekapKpiTable .kpi-ao-cell{width:190px;min-width:190px;max-width:210px}
    #rekapKpiTable .kpi-ao-cell{min-width:190px}
    .rekap-kpi-tabs{width:auto;margin:0}
    .rekap-kpi-tabs .mb-segmented__btn{width:29px;min-width:29px;height:27px}
    .rekap-kpi-extra-panel{padding:10px}
    .rekap-kpi-extra-panel .mb-table-scroll{max-height:calc(100dvh - 360px)}
    .mb-kpi-tukin-table{min-width:360px}
  }
</style>
<script>
(() => {
    const API='./api/index.php?request=kpi';
    const state={boot:null};
    const months=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const el=id=>document.getElementById(id);
    const esc=value=>window.MonbisUI?.escape?window.MonbisUI.escape(value):String(value??'');
    const fmt=value=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:2}).format(Number(value??0));
    const pct=value=>fmt(Number(value??0)*100)+'%';
    const money=value=>'Rp '+new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(Number(value??0));
    const post=async body=>{const response=await fetch(API,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});const json=await response.json();if(!response.ok||json.status!==200)throw Error(json.message||'Gagal memuat data');return json.data||{}};

    function fillAo(){
        const branch=el('rekapKpiKantor').value;
        const list=(state.boot?.ao||[]).filter(item=>!branch||String(item.kode_kantor)===String(branch));
        el('rekapKpiAo').innerHTML='<option value="">Semua AO</option>'+list.map(item=>`<option value="${esc(item.kode_ao)}">${esc(item.kode_ao)} · ${esc(item.nama_ao)}</option>`).join('');
    }

    function monthlyCell(item,month){
        const data=item.monthly?.[month];
        if(!data)return '<td class="kpi-month-cell kpi-empty">–</td>';
        return `<td class="kpi-month-cell"><strong>${fmt(data.nilai_akhir)}</strong><small>${fmt(data.tukin_persen)}% tukin</small></td>`;
    }

    function syncMonthColumns(year){
        const selectedYear=Number(year||0),now=new Date();
        const visible=selectedYear<now.getFullYear()?12:selectedYear>now.getFullYear()?0:Math.max(0,now.getMonth());
        document.querySelectorAll('#rekapKpiTable tr').forEach(row=>{
            Array.from(row.children).slice(2,14).forEach((cell,index)=>{
                cell.hidden=index+1>visible;
            });
        });
    }

    function renderBreakdown(data){
        const rows=data.indicator_breakdown||[];
        const value=(row,key)=>row[key]===null||row[key]===undefined?'–':fmt(row[key]);
        el('rekapKpiBreakdownBody').innerHTML=rows.length?rows.map(row=>`<tr>
            <td class="mb-text-center">${esc(row.kode_kantor)}</td>
            <td class="mb-sticky-left mb-text-left"><strong>${esc(row.nama_ao)}</strong><small class="mb-subvalue">${esc(row.id_peg||'-')}</small></td>
            <td class="mb-sticky-left mb-text-left"><strong>${esc(row.nama)}</strong><small class="mb-subvalue">${esc(row.kelompok||'-')}</small></td>
            <td class="mb-text-center">${esc(months[(Number(row.bulan)||1)-1]||row.bulan)}<small class="mb-subvalue">${esc(row.closing_date||'-')}</small></td>
            <td class="mb-num mb-strong">${row.nilai_akhir_bulan===null||row.nilai_akhir_bulan===undefined?'â€“':fmt(row.nilai_akhir_bulan)}</td>
            <td class="mb-num kpi-tukin">${row.tukin_persen_bulan===null||row.tukin_persen_bulan===undefined?'â€“':fmt(row.tukin_persen_bulan)+'%'}</td>
            <td class="mb-num">${row.unit==='RUPIAH'?money(row.target):row.unit==='PERSEN'?pct(row.target):value(row,'target')}</td>
            <td class="mb-num mb-strong">${row.unit==='RUPIAH'?money(row.realisasi):row.unit==='PERSEN'?pct(row.realisasi):value(row,'realisasi')}</td>
            <td class="mb-num">${row.unit==='PERSEN'?pct(row.indeks):value(row,'indeks')}</td>
            <td class="mb-num">${value(row,'skor')} / 5</td>
            <td class="mb-num">${pct(row.bobot)}</td>
            <td class="mb-num mb-strong">${value(row,'nilai_100')}</td>
            <td class="mb-text-left kpi-input-pa">${esc(row.input_pa||'-')}</td>
        </tr>`).join(''):'<tr><td colspan="13" class="mb-empty">Belum ada breakdown indikator pada filter ini.</td></tr>';
    }

    function renderTukin(data){
        const rows=data.tukin_rules||[];
        el('rekapKpiTukinBody').innerHTML=rows.length?rows.map(row=>`<tr><td>${esc(row.label||'-')}</td><td>${row.min_nilai===null?'–':fmt(row.min_nilai)} – ${row.max_nilai===null?'100':fmt(row.max_nilai)}</td><td class="kpi-tukin">${fmt(row.faktor_persen)}%</td></tr>`).join(''):'<tr><td colspan="3" class="mb-empty">Parameter tukin belum tersedia.</td></tr>';
    }

    function syncTab(name){
        document.querySelectorAll('[data-kpi-tab]').forEach(button=>{
            const active=button.dataset.kpiTab===name;
            button.classList.toggle('is-active',active);
            button.setAttribute('aria-selected',active?'true':'false');
        });
        el('rekapKpiTableWrap')?.classList.toggle('is-hidden',name!=='summary');
        el('rekapKpiBreakdownPanel')?.classList.toggle('is-hidden',name!=='breakdown');
        el('rekapKpiTukinPanel')?.classList.toggle('is-hidden',name!=='tukin');
    }

    function render(data){
        const rows=data.ao||[];
        const monthsFilled=(data.months||[]).filter(item=>item.terisi>0);
        const avgValues=rows.filter(item=>item.nilai_akhir!==null).map(item=>Number(item.nilai_akhir));
        const avg=avgValues.length?avgValues.reduce((a,b)=>a+b,0)/avgValues.length:null;
        el('rekapKpiCount').textContent=fmt(rows.length);
        el('rekapKpiCountMeta').textContent=data.is_konsolidasi&&data.total_ao>rows.length?`Top ${rows.length} dari ${fmt(data.total_ao)} AO terbaik`:(rows.length?'AO dengan minimal 1 bulan':'Belum ada penilaian');
        el('rekapKpiPeriod').textContent=monthsFilled.length?`${months[monthsFilled[0].bulan-1]} – ${months[monthsFilled[monthsFilled.length-1].bulan-1]} ${data.year}`:'–';
        el('rekapKpiFinal').textContent=avg===null?'–':`${fmt(avg)} / 100`;
        const branch=el('rekapKpiKantor').value;
        const branchText=branch?(el('rekapKpiKantor').selectedOptions[0]?.textContent||branch):'Semua kantor';
        el('rekapKpiMode').textContent=data.is_konsolidasi?'Konsolidasi · Top 5':branchText.replace(/^\s+/,'');
        renderBreakdown(data);
        renderTukin(data);
        el('rekapKpiBody').innerHTML=rows.length?rows.map(item=>`<tr>
            <td class="mb-text-center"><strong>${esc(item.kode_kantor)}</strong></td>
            <td class="mb-sticky-left mb-text-left kpi-ao-cell" title="${esc(item.nama_ao)}"><strong>${esc(item.nama_ao)}</strong><small class="mb-subvalue">${esc(item.id_peg||item.kode_ao)}</small></td>
            ${Array.from({length:12},(_,offset)=>monthlyCell(item,offset+1)).join('')}
            <td class="mb-num mb-strong">${item.nilai_akhir===null?'–':fmt(item.nilai_akhir)}</td>
            <td class="mb-num">${item.skor_final===null?'–':fmt(item.skor_final)} / 5</td>
            <td class="mb-num kpi-tukin">${item.tukin_persen===null?'–':fmt(item.tukin_persen)+'%'}</td>
            <td class="mb-num">${fmt(item.bulan_terisi)} / 12</td>
        </tr>`).join(''):'<tr><td colspan="18" class="mb-empty">Belum ada penilaian KPI yang sudah digenerate pada tahun ini.</td></tr>';
        syncMonthColumns(data.year);
    }

    async function load(){
        try{render(await post({type:'annual',year:el('rekapKpiYear').value,jabatan_kode:el('rekapKpiJabatan').value,kode_kantor:el('rekapKpiKantor').value,kode_ao:el('rekapKpiAo').value}));}
        catch(error){el('rekapKpiBody').innerHTML=`<tr><td colspan="18" class="mb-empty mb-negative">${esc(error.message)}</td></tr>`;el('rekapKpiFinal').textContent='–';}
    }

    async function boot(){
        try{const data=await post({type:'bootstrap',year:el('rekapKpiYear').value,jabatan_kode:'AO_KREDIT'});state.boot=data;el('rekapKpiKantor').innerHTML='<option value="">Semua kantor</option>'+(data.kantor||[]).map(item=>`<option value="${esc(item.kode_kantor)}">${esc(item.kode_kantor)} · ${esc(item.nama_kantor)}</option>`).join('');fillAo();await load();}
        catch(error){el('rekapKpiBody').innerHTML=`<tr><td colspan="18" class="mb-empty mb-negative">${esc(error.message)}</td></tr>`;}
    }
    async function bootAnnual(){
        try{
            const data=await post({type:'directory',year:el('rekapKpiYear').value,jabatan_kode:el('rekapKpiJabatan').value||'AO_KREDIT',include_all_ao:true,include_generated:false});
            state.boot=data;
            const jobs=data.jabatan||[],jobSelect=el('rekapKpiJabatan'),selected=data.jabatan_terpilih?.kode||'AO_KREDIT';
            jobSelect.innerHTML=jobs.map(item=>`<option value="${esc(item.kode)}">${esc(item.nama)}</option>`).join('');
            if(jobs.some(item=>item.kode===selected))jobSelect.value=selected;
            el('rekapKpiKantor').innerHTML='<option value="">Semua kantor</option>'+(data.kantor||[]).map(item=>`<option value="${esc(item.kode_kantor)}">${esc(item.kode_kantor)} · ${esc(item.nama_kantor)}</option>`).join('');
            fillAo();
            await load();
        }catch(error){el('rekapKpiBody').innerHTML=`<tr><td colspan="18" class="mb-empty mb-negative">${esc(error.message)}</td></tr>`;}
    }
    el('rekapKpiJabatan')?.addEventListener('change',bootAnnual);
    el('rekapKpiYear')?.addEventListener('change',bootAnnual);
    el('rekapKpiKantor')?.addEventListener('change',()=>{fillAo();load()});
    el('rekapKpiAo')?.addEventListener('change',load);
    el('rekapKpiSearch')?.addEventListener('input',event=>{const query=event.target.value.toLowerCase();document.querySelectorAll('#rekapKpiBody tr').forEach(row=>row.style.display=row.textContent.toLowerCase().includes(query)?'':'none')});
    document.querySelectorAll('[data-kpi-tab]').forEach(button=>button.addEventListener('click',()=>syncTab(button.dataset.kpiTab)));
    syncTab('summary');
    bootAnnual();
})();
</script>

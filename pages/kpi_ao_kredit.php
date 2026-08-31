<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

$head = mb_build_grouped_thead([
    ['label'=>'Bulan','class'=>'mb-sticky-left','attrs'=>['style'=>'width:110px']],
    ['label'=>'Closing','attrs'=>['style'=>'width:120px']],
    ['label'=>'Indikator','attrs'=>['style'=>'width:110px']],
    ['label'=>'Nilai Dasar','class'=>'mb-group--blue','attrs'=>['style'=>'width:120px']],
    ['label'=>'Risk Gate','class'=>'mb-group--amber','attrs'=>['style'=>'width:110px']],
    ['label'=>'Nilai Akhir','class'=>'mb-group--green','attrs'=>['style'=>'width:120px']],
    ['label'=>'Predikat','class'=>'mb-group--cyan','attrs'=>['style'=>'width:150px']],
    ['label'=>'Status','attrs'=>['style'=>'width:100px']],
]);

mb_render_report_page([
    'id'=>'kpiAoKreditPage','class'=>'mb-kpi-page',
    'header'=>[
        'id'=>'kpiAoKreditHeader','title'=>'KPI AO Kredit',
        'subtitle'=>'Penilaian kinerja AO berdasarkan closing bulanan yang tersedia.',
        'icon'=>mb_svg('chart'),'info_modal_id'=>'kpiAoKreditInfo',
        'filters'=>[
            ['id'=>'kpiYear','label'=>'Tahun','type'=>'number','width'=>'92px','value'=>date('Y'),'attrs'=>['min'=>'2020','max'=>'2100']],
            ['id'=>'kpiAo','label'=>'Nama AO','type'=>'select','width'=>'230px','options'=>[''=>'Pilih AO Kredit']],
        ],
    ],
    'toolbar'=>[
        'title'=>'Rekap Penilaian Tahunan','title_id'=>'kpiAoTitle',
        'search'=>['id'=>'kpiSearch','placeholder'=>'Cari bulan / status...'],
        'actions'=>[
            ['attrs'=>['id'=>'kpiCalculate'],'tone'=>'success','icon'=>'chart','title'=>'Hitung KPI AO','aria_label'=>'Hitung KPI AO','label'=>'Hitung'],
            ['attrs'=>['id'=>'kpiRefresh'],'tone'=>'primary','icon'=>'chart','title'=>'Muat penilaian','aria_label'=>'Muat penilaian'],
        ],
    ],
    'legend_html'=>'<div id="kpiAoSummary" class="mb-summary"><div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">Periode Closing</div><div id="kpiPeriodCount" class="mb-summary-card__value">-</div><div class="mb-summary-card__meta">Closing tersedia tahun berjalan</div></div><div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">Nilai Rata-rata</div><div id="kpiAverage" class="mb-summary-card__value">-</div><div class="mb-summary-card__meta">Nilai akhir tersimpan</div></div><div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">Indikator AO Kredit</div><div id="kpiIndicatorCount" class="mb-summary-card__value">-</div><div class="mb-summary-card__meta">Bobot master KPI</div></div><div class="mb-summary-card mb-summary-card--red"><div class="mb-summary-card__label">Status Data</div><div id="kpiDataStatus" class="mb-summary-card__value">Belum dihitung</div><div class="mb-summary-card__meta">Perlu validasi parameter</div></div></div>',
    'table'=>[
        'wrapper_id'=>'kpiAoTableWrap','table_id'=>'kpiAoTable','loading_id'=>'kpiAoLoading','loading_text'=>'Memuat penilaian KPI...','thead_html'=>$head,'tbody_ids'=>['kpiAoBody'],
        'footer_html'=>'<div id="kpiAoFooter" class="mb-map-pager is-hidden"></div>'
    ],
]);
mb_render_info_modal([
    'id'=>'kpiAoKreditInfo','title'=>'Panduan KPI AO Kredit','subtitle'=>'Penilaian tahunan berdasarkan closing bulanan.',
    'body_html'=>'<div class="mb-npl-brief"><div class="mb-npl-brief__alert"><strong>Periode hanya memakai closing yang sudah tersedia.</strong><span>Jika data terakhir baru closing Juli, maka Agustus belum masuk penilaian.</span></div><div class="mb-npl-brief__priority-grid"><div class="mb-npl-brief__priority mb-npl-brief__priority--blue"><b>1</b><div><strong>Target</strong><span>Target indikator disiapkan pada menu Setting KPI Jabatan.</span></div></div><div class="mb-npl-brief__priority mb-npl-brief__priority--violet"><b>2</b><div><strong>Realisasi</strong><span>Nilai aktual diambil dari sumber Monbis sesuai formula indikator.</span></div></div><div class="mb-npl-brief__priority mb-npl-brief__priority--red"><b>3</b><div><strong>Skor</strong><span>Indeks dikonversi menjadi skor 1–5, lalu dikalikan bobot dan risk gate.</span></div></div></div><div class="mb-info-warning"><span>Catatan:</span><div>Indikator yang definisi sumber datanya belum dikunci akan tetap berstatus draft sampai formula disetujui.</div></div></div>'
]);
?>
<script>
(()=>{const API='./api/index.php?request=kpi',el=id=>document.getElementById(id),ui=()=>window.MonbisUI||{},esc=v=>ui().escape?ui().escape(v):String(v??''),num=v=>Number(v||0),fmt=v=>new Intl.NumberFormat('id-ID',{maximumFractionDigits:2}).format(num(v));let state={ao:[],ind:[],dates:[]};
function headers(){const match=document.cookie.match(/(?:^|;\s*)sso_token=([^;]+)/),token=String((match?decodeURIComponent(match[1]):'')||window.AUTH_TOKEN||localStorage.getItem('dpk_token')||'').replace(/^Bearer\s+/i,'').trim(),result={'Content-Type':'application/json'};if(token)result.Authorization='Bearer '+token;return result}
async function post(body){const r=await fetch(API,{method:'POST',credentials:'same-origin',headers:headers(),body:JSON.stringify(body)});const j=await r.json();if(!r.ok||Number(j.status)!==200)throw Error(j.message||'Gagal memuat KPI');return j.data||{}}
function fillAo(list){state.ao=list||[];el('kpiAo').innerHTML='<option value="">Pilih AO Kredit</option>'+state.ao.map(x=>`<option value="${esc(x.kode_ao)}">${esc(x.nama_ao)} · ${esc(x.kode_kantor||'-')}</option>`).join('')}
function render(rows){const body=el('kpiAoBody');if(!rows.length){body.innerHTML='<tr><td colspan="8" class="mb-empty">Belum ada penilaian tersimpan. Atur parameter terlebih dahulu, lalu formula indikator dapat diaktifkan.</td></tr>';return}body.innerHTML=rows.map(r=>`<tr><td class="mb-sticky-left mb-code"><strong>${esc(new Date(r.closing_date).toLocaleString('id-ID',{month:'long'}))}</strong></td><td class="mb-text-center">${esc(r.closing_date)}</td><td class="mb-text-center">${fmt(r.indikator_terisi)} / ${fmt(state.ind.length)}</td><td class="mb-num">${fmt(r.nilai_dasar)}</td><td class="mb-text-center">${esc(r.faktor_risiko)}</td><td class="mb-num mb-strong">${fmt(r.nilai_akhir)}</td><td>${esc(r.predikat||'Belum dihitung')}</td><td class="mb-text-center">${esc(r.status||'DRAFT')}</td></tr>`).join('')}
async function load(){ui().showLoading?.('kpiAoLoading',true);try{const year=el('kpiYear').value||new Date().getFullYear(),data=await post({type:'evaluation',year,kode_ao:el('kpiAo').value});state.dates=data.closing_dates||[];el('kpiPeriodCount').textContent=fmt(state.dates.length);const rows=data.data||[];const done=rows.filter(x=>x.status==='DISETUJUI');el('kpiAverage').textContent=done.length?fmt(done.reduce((a,x)=>a+num(x.nilai_akhir),0)/done.length):'-';el('kpiDataStatus').textContent=done.length?'Tersedia':rows.length?'Draft':'Belum dihitung';render(rows)}catch(e){el('kpiAoBody').innerHTML=`<tr><td colspan="8" class="mb-empty mb-negative">${esc(e.message)}</td></tr>`}finally{ui().showLoading?.('kpiAoLoading',false)}}
async function calculate(){if(!el('kpiAo').value)return alert('Pilih AO Kredit terlebih dahulu.');const b=el('kpiCalculate');b.disabled=true;try{const d=await post({type:'calculate',year:el('kpiYear').value,kode_ao:el('kpiAo').value});alert('KPI tersimpan untuk '+(d.data||[]).length+' periode closing.');await load()}catch(e){alert(e.message)}finally{b.disabled=false}}
async function boot(){try{const d=await post({type:'bootstrap',year:el('kpiYear').value});state.ind=d.indikator||[];el('kpiIndicatorCount').textContent=fmt(state.ind.filter(x=>x.jabatan_kode==='AO_KREDIT').length);fillAo(d.ao_kredit||[])}catch(e){el('kpiAoBody').innerHTML=`<tr><td colspan="8" class="mb-empty mb-negative">${esc(e.message)}</td></tr>`}}
el('kpiRefresh')?.addEventListener('click',load);el('kpiCalculate')?.addEventListener('click',calculate);el('kpiAo')?.addEventListener('change',load);el('kpiYear')?.addEventListener('change',boot);el('kpiSearch')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('#kpiAoBody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(q)?'':'none')});boot();})();
</script>

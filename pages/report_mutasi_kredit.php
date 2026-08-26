<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');

mb_render_report_page([
    'id'=>'reportMutasiPage',
    'class'=>'mb-report-mutasi',
    'header'=>[
        'id'=>'reportMutasiHeader',
        'title'=>'Mutasi Kredit',
        'subtitle'=>'Realisasi, restrukturisasi, run off, dan perubahan portofolio kredit.',
        'icon'=>mb_svg('chart'),
        'info_modal_id'=>'reportMutasiInfo',
        'filters'=>[
            ['id'=>'reportMutasiClosing','label'=>'Closing (M-1)','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
            ['id'=>'reportMutasiActual','label'=>'Actual (Harian)','type'=>'date','width'=>'126px','attrs'=>['onclick'=>'this.showPicker && this.showPicker()']],
            ['id'=>'reportMutasiArea','label'=>'Area / Cabang','type'=>'select','width'=>'260px','options'=>['ALL'=>'Konsolidasi']],
        ],
        'actions'=>[],
    ],
    'toolbar'=>[
        'title'=>'Rekap Mutasi Kredit',
        'search'=>['id'=>'reportMutasiSearch','placeholder'=>'Cari kode / kantor...'],
        'actions'=>[
            ['attrs'=>['id'=>'reportMutasiExport'],'tone'=>'success','icon'=>'download','title'=>'Export Excel','aria_label'=>'Export Excel'],
        ],
    ],
    'table'=>[
        'wrapper_id'=>'reportMutasiTableWrap',
        'table_id'=>'reportMutasiTable',
        'loading_id'=>'reportMutasiLoading',
        'loading_text'=>'Memuat data mutasi kredit...',
        'class'=>'mb-mutasi-table',
        'colgroup_html'=>'<col style="width:44px"><col style="width:136px"><col style="width:112px"><col style="width:112px"><col style="width:112px"><col style="width:104px"><col style="width:104px"><col style="width:108px"><col style="width:112px"><col style="width:108px">',
        'thead_html'=>'<tr><th class="mb-code-col mb-sticky-left mb-sort" data-sort="kode_kantor">Kode <span class="mb-sort-icon"></span></th><th class="mb-sticky-left-2 mb-text-left mb-sort" style="--mb-sticky-1:44px" data-sort="nama_kantor">Kantor <span class="mb-sort-icon"></span></th><th class="mb-group mb-group--blue mb-sort" data-sort="portofolio_closing">Porto Closing <span class="mb-sort-icon"></span></th><th class="mb-group mb-group--green mb-sort" data-sort="total_realisasi">Realisasi <span class="mb-sort-icon"></span></th><th class="mb-group mb-group--violet mb-sort" data-sort="total_restruck">Restrukturisasi <span class="mb-sort-icon"></span></th><th class="mb-group mb-group--amber mb-sort" data-sort="pelunasan">Lunas <span class="mb-sort-icon"></span></th><th class="mb-group mb-group--blue mb-sort" data-sort="angsuran_murni">Angsuran <span class="mb-sort-icon"></span></th><th class="mb-group mb-group--amber mb-sort" data-sort="total_run_off">Total Run Off <span class="mb-sort-icon"></span></th><th class="mb-group mb-group--cyan mb-sort" data-sort="portofolio_harian">Porto Actual <span class="mb-sort-icon"></span></th><th class="mb-group mb-group--green mb-sort" data-sort="growth">Growth <span class="mb-sort-icon"></span></th></tr>',
        'tbody_ids'=>['reportMutasiTotal','reportMutasiBody'],
    ],
]);

mb_render_info_modal([
    'id'=>'reportMutasiInfo',
    'title'=>'Ringkasan Mutasi Kredit',
    'subtitle'=>'Sorotan produksi, run off, dan perubahan portofolio.',
    'body_html'=>'
      <div class="mb-npl-brief">
        <div class="mb-npl-brief__alert">
          <strong id="reportMutasiInfoText">Data mengikuti filter yang sedang dibuka.</strong>
          <span id="reportMutasiInfoDesc">Growth membandingkan produksi kredit terhadap pengurangan portofolio pada periode terpilih.</span>
        </div>
        <div class="mb-npl-brief__metrics">
          <div><span>Realisasi + Restruk</span><strong id="reportMutasiInfoProduksi">-</strong></div>
          <div><span>Total Run Off</span><strong id="reportMutasiInfoRunOff">-</strong></div>
          <div><span>Growth</span><strong id="reportMutasiInfoGrowth">-</strong></div>
        </div>
        <div class="mb-npl-brief__section">
          <div class="mb-npl-brief__section-title">Cara Membaca Mutasi Kredit</div>
          <div class="mb-npl-brief__priority-grid">
            <div class="mb-npl-brief__priority mb-npl-brief__priority--blue"><b>1</b><div><strong>Produksi Kredit</strong><span>Jumlahkan realisasi baru dan restrukturisasi untuk melihat penambahan kredit selama periode.</span></div></div>
            <div class="mb-npl-brief__priority mb-npl-brief__priority--violet"><b>2</b><div><strong>Total Run Off</strong><span>Pelunasan dan angsuran mengurangi portofolio. Bandingkan nilainya dengan produksi kredit.</span></div></div>
            <div class="mb-npl-brief__priority mb-npl-brief__priority--red"><b>3</b><div><strong>Growth</strong><span>Growth positif berarti produksi lebih besar daripada run off; growth negatif berarti portofolio lebih banyak berkurang.</span></div></div>
          </div>
        </div>
        <div class="mb-info-warning"><span>Catatan:</span><div>Nominal mengikuti hasil backend tanpa pembagian 1.000. Gunakan detail rekening untuk memeriksa transaksi pembentuk realisasi dan restrukturisasi.</div></div>
      </div>'
]);

mb_render_detail_modal([
    'id'=>'reportMutasiDetail',
    'title'=>'Detail Debitur',
    'subtitle'=>'Daftar rekening mutasi kredit',
    'size'=>'xl',
    'mobile_body_id'=>'reportMutasiDetailMobile',
    'search_near_close'=>true,
    'search'=>['id'=>'reportMutasiDetailSearch','placeholder'=>'Cari nama / rekening...'],
]);
?>
<script>
(() => {
  'use strict';
  const el=id=>document.getElementById(id), api='./api/kredit/', apiKode='./api/kode/', apiDate='./api/date/';
  const ICON_DOWNLOAD=<?= json_encode(mb_svg('download'), JSON_UNESCAPED_SLASHES) ?>;
  const ui=()=>window.MonbisUI||{}, apiCall=(url,options={})=>window.apiFetch?window.apiFetch(url,options):fetch(url,options), num=v=>Number(v||0), fmt=v=>new Intl.NumberFormat('id-ID').format(Math.round(num(v))), esc=v=>ui().escape?ui().escape(v):String(v??'');
  async function post(url,body,signal){const res=await apiCall(url,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body||{}),signal});const json=await res.json();if(json.status!==200)throw new Error(json.message||'Gagal mengambil data');return json}
  const state={rows:[],total:{},offices:[],detail:[],detailPage:1,detailSize:25,detailType:'',detailKode:'',abort:null,sort:{column:'kode_kantor',direction:'asc'}};
  const previousEnd=value=>{const [y,m]=String(value||'').split('-').map(Number);return y&&m?new Date(y,m-1,0).toISOString().slice(0,10):''};
  const officeName=row=>row.nama_kantor||state.offices.find(x=>String(x.kode_kantor).padStart(3,'0')===String(row.kode_kantor).padStart(3,'0'))?.nama_kantor||'-';
  const metric=(nom,noa=null,tone='')=>`<span class="mb-money mb-num ${tone}">${fmt(nom)}</span>${noa===null?'':`<span class="mb-subvalue">${fmt(noa)} NOA</span>`}`;
  const payload=()=>{const area=el('reportMutasiArea').value,body={type:'rekap_realisasi_growth',closing_date:el('reportMutasiClosing').value,harian_date:el('reportMutasiActual').value};if(area.startsWith('KOR-'))body.korwil=area.slice(4);else if(area.startsWith('CAB-')){body.kode_kantor=area.slice(4);if(body.kode_kantor==='000')body.pusat_only=true}return body};
  const filtered=()=>{const q=el('reportMutasiSearch').value.trim().toLowerCase(),rows=state.rows.filter(r=>!q||(String(r.kode_kantor)+' '+officeName(r)).toLowerCase().includes(q)),column=state.sort.column,direction=state.sort.direction==='desc'?-1:1;return rows.sort((a,b)=>{const av=column==='nama_kantor'?officeName(a):(column==='kode_kantor'?String(a.kode_kantor||''):num(a[column])),bv=column==='nama_kantor'?officeName(b):(column==='kode_kantor'?String(b.kode_kantor||''):num(b[column]));return typeof av==='string'?av.localeCompare(bv,'id',{numeric:true})*direction:(av-bv)*direction})};
  function rowHtml(r,total=false){const code=total?'ALL':r.kode_kantor,name=total?'GRAND TOTAL':officeName(r),growth=num(r.growth),real=num(r.noa_realisasi)>0,res=num(r.noa_restruck)>0,realAttrs=real?`data-kind="110" data-kode="${esc(code)}" data-name="${esc(name)}"`:'',resAttrs=res?`data-kind="109" data-kode="${esc(code)}" data-name="${esc(name)}"`:'';return `<tr class="${total?'mb-total-row':''}"><td class="mb-code-col mb-sticky-left mb-code">${esc(code)}</td><td class="mb-sticky-left-2 mb-name" style="--mb-sticky-1:44px" title="${esc(name)}">${esc(name)}</td><td class="mb-num">${metric(r.portofolio_closing)}</td><td class="mb-num ${real?'mb-clickable':''}" ${realAttrs}>${metric(r.total_realisasi,r.noa_realisasi)}</td><td class="mb-num ${res?'mb-clickable':''}" ${resAttrs}>${metric(r.total_restruck,r.noa_restruck)}</td><td class="mb-num">${metric(r.pelunasan)}</td><td class="mb-num">${metric(r.angsuran_murni)}</td><td class="mb-num">${metric(r.total_run_off,null,'mb-amber')}</td><td class="mb-num">${metric(r.portofolio_harian)}</td><td class="mb-num ${growth<0?'mb-negative':'mb-positive'}">${growth>0?'+':''}${fmt(growth)}</td></tr>`}
  function updateSortIcons(){document.querySelectorAll('#reportMutasiTable .mb-sort').forEach(th=>{const icon=th.querySelector('.mb-sort-icon'),active=th.dataset.sort===state.sort.column;if(!icon)return;icon.classList.toggle('is-active',active);icon.classList.toggle('is-desc',active&&state.sort.direction==='desc')})}
  function renderInfo(){const row=state.total||{},produksi=num(row.total_realisasi)+num(row.total_restruck),runOff=num(row.total_run_off),growth=num(row.growth),growthText=(growth>0?'+Rp ':growth<0?'-Rp ':'Rp ')+fmt(Math.abs(growth)),position=el('reportMutasiActual')?.value||'-';if(el('reportMutasiInfoText'))el('reportMutasiInfoText').textContent='Posisi '+position+': growth '+(growth>0?'positif':growth<0?'negatif':'seimbang')+' sebesar '+growthText+'.';if(el('reportMutasiInfoDesc'))el('reportMutasiInfoDesc').textContent='Produksi Rp '+fmt(produksi)+' dibandingkan total run off Rp '+fmt(runOff)+'.';if(el('reportMutasiInfoProduksi'))el('reportMutasiInfoProduksi').textContent='Rp '+fmt(produksi);if(el('reportMutasiInfoRunOff'))el('reportMutasiInfoRunOff').textContent='Rp '+fmt(runOff);if(el('reportMutasiInfoGrowth')){el('reportMutasiInfoGrowth').textContent=growthText;el('reportMutasiInfoGrowth').className=growth<0?'mb-negative':growth>0?'mb-positive':''}}
  function render(){el('reportMutasiTotal').innerHTML=state.total?rowHtml(state.total,true):'';const list=filtered();el('reportMutasiBody').innerHTML=list.length?list.map(r=>rowHtml(r)).join(''):'<tr><td colspan="10" class="mb-empty">Data tidak ditemukan.</td></tr>';updateSortIcons();renderInfo()}
  async function load(){ui().showLoading('reportMutasiLoading',true);if(state.abort)state.abort.abort();state.abort=new AbortController();try{const json=await post(api,payload(),state.abort.signal);const data=json.data?.data||json.data||[];state.rows=Array.isArray(data)?data:[];state.total=json.data?.grand_total||json.grand_total||{};render()}catch(e){if(e.name!=='AbortError')el('reportMutasiBody').innerHTML=`<tr><td colspan="10" class="mb-empty mb-negative">${esc(e.message||'Gagal memuat data.')}</td></tr>`}finally{ui().showLoading('reportMutasiLoading',false);ui().closeMobileFilter('reportMutasiHeaderFilters')}}
  async function loadOffices(){const json=await post(apiKode,{type:'kode_kantor'});const data=json.data||[];state.offices=Array.isArray(data)?data:[];let html='<option value="ALL">Konsolidasi</option><option value="KOR-SEMARANG">Korwil Semarang</option><option value="KOR-SOLO">Korwil Solo</option><option value="KOR-BANYUMAS">Korwil Banyumas</option><option value="KOR-PEKALONGAN">Korwil Pekalongan</option>';state.offices.filter(x=>String(x.kode_kantor).padStart(3,'0')!=='000').forEach(x=>{const k=String(x.kode_kantor).padStart(3,'0');html+=`<option value="CAB-${k}">${k} - ${esc(x.nama_kantor)}</option>`});el('reportMutasiArea').innerHTML=html;const u=window.getUser?.()||{},k=String(u.kode_kantor||u.kode||u.kode_cabang||'000').padStart(3,'0');if(k!=='000'&&k!=='099'){el('reportMutasiArea').value='CAB-'+k;el('reportMutasiArea').disabled=true}}
  function detailRows(){const q=el('reportMutasiDetailSearch').value.trim().toLowerCase();return state.detail.filter(r=>!q||(String(r.no_rekening||'')+' '+String(r.nama_nasabah||'')+' '+String(r.nama_ao||'')).toLowerCase().includes(q))}
  function renderDetailFooter(totalRows,totalPages){const footer=el('reportMutasiDetailFooter');if(!footer)return;if(!totalRows){footer.classList.add('is-hidden');footer.innerHTML='';return}footer.classList.remove('is-hidden');const from=((state.detailPage-1)*state.detailSize)+1,to=Math.min(totalRows,state.detailPage*state.detailSize);footer.innerHTML=`<div class="mb-detail-page-info">Data ${fmt(from)} - ${fmt(to)} dari ${fmt(totalRows)}</div><div class="mb-detail-page-actions"><span class="mb-detail-footer-actions"><button type="button" class="mb-detail-footer-icon mb-detail-footer-icon--export" id="reportMutasiDetailExport" title="Export detail" aria-label="Export detail">${ICON_DOWNLOAD}</button></span><span class="mb-detail-pagination"><button type="button" class="mb-detail-page-btn" id="reportMutasiDetailPrev" ${state.detailPage<=1?'disabled':''}>&lsaquo; Prev</button><span class="mb-detail-page-status">Hal ${fmt(state.detailPage)} / ${fmt(totalPages)}</span><button type="button" class="mb-detail-page-btn" id="reportMutasiDetailNext" ${state.detailPage>=totalPages?'disabled':''}>Next &rsaquo;</button></span></div>`;el('reportMutasiDetailExport')?.addEventListener('click',exportDetailExcel);el('reportMutasiDetailPrev')?.addEventListener('click',()=>{if(state.detailPage>1){state.detailPage-=1;renderDetail()}});el('reportMutasiDetailNext')?.addEventListener('click',()=>{if(state.detailPage<totalPages){state.detailPage+=1;renderDetail()}})}
  function renderDetailMobile(rows,kind){const mobile=el('reportMutasiDetailMobile');if(!mobile)return;if(!rows.length){mobile.innerHTML='<div class="mb-empty">Detail tidak ditemukan.</div>';return}mobile.innerHTML=rows.map(r=>`<article class="mb-detail-card"><div class="mb-detail-card__head"><div class="mb-detail-card__identity"><div class="mb-detail-card__name">${esc(r.nama_nasabah||'-')}</div><div class="mb-detail-card__meta">${esc(r.no_rekening||'-')} · ${esc(r.nama_ao||'-')}</div></div><span class="mb-pill mb-pill--blue">${esc(kind)}</span></div><div class="mb-detail-card__address"><span>Alamat</span><strong>${esc(r.alamat||'-')}</strong></div><div class="mb-detail-card__metrics"><div class="mb-detail-card__metric"><span>Plafond</span><strong>Rp ${fmt(r.plafond)}</strong></div><div class="mb-detail-card__metric"><span>Tgl Realisasi</span><strong>${esc(r.tgl_realisasi||'-')}</strong></div><div class="mb-detail-card__metric"><span>Kankas</span><strong>${esc(r.nama_kankas||'-')}</strong></div><div class="mb-detail-card__metric"><span>AO</span><strong>${esc(r.nama_ao||'-')}</strong></div></div></article>`).join('')}
  function renderDetail(){const all=detailRows(),summaryRows=state.detail,totalPlafond=summaryRows.reduce((sum,row)=>sum+num(row.plafond),0),average=summaryRows.length?totalPlafond/summaryRows.length:0,pages=Math.max(1,Math.ceil(all.length/state.detailSize));state.detailPage=Math.min(Math.max(1,state.detailPage),pages);const list=all.slice((state.detailPage-1)*state.detailSize,state.detailPage*state.detailSize),kind=state.detailType==='110'?'Realisasi Baru':'Restrukturisasi',summary=el('reportMutasiDetailSummary');summary.classList.remove('is-hidden');summary.innerHTML=`<div class="mb-summary-card mb-summary-card--blue"><div class="mb-summary-card__label">Total Debitur</div><div class="mb-summary-card__value">${fmt(summaryRows.length)}</div></div><div class="mb-summary-card mb-summary-card--green"><div class="mb-summary-card__label">Total Plafond</div><div class="mb-summary-card__value">Rp ${fmt(totalPlafond)}</div></div><div class="mb-summary-card mb-summary-card--amber"><div class="mb-summary-card__label">Rata-rata Plafond</div><div class="mb-summary-card__value">Rp ${fmt(average)}</div></div><div class="mb-summary-card"><div class="mb-summary-card__label">Jenis Transaksi</div><div class="mb-summary-card__value">${esc(kind)}</div></div>`;renderDetailMobile(list,kind);el('reportMutasiDetailBody').innerHTML=`<table class="mb-table mb-detail-mini"><thead><tr><th>Rekening</th><th>Nama Nasabah</th><th>Alamat</th><th>Kankas</th><th>AO</th><th>Tgl Realisasi</th><th>Plafond</th></tr></thead><tbody>${list.map(r=>`<tr><td class="mb-code">${esc(r.no_rekening)}</td><td class="mb-name">${esc(r.nama_nasabah)}</td><td>${esc(r.alamat||'-')}</td><td>${esc(r.nama_kankas||'-')}</td><td>${esc(r.nama_ao||'-')}</td><td>${esc(r.tgl_realisasi||'-')}</td><td class="mb-text-right">${fmt(r.plafond)}</td></tr>`).join('')||'<tr><td colspan="7" class="mb-empty">Detail tidak ditemukan.</td></tr>'}</tbody></table>`;renderDetailFooter(all.length,pages);const content=el('reportMutasiDetailBody').closest('.mb-detail-content');if(content)content.scrollTop=0}
  async function openDetail(kind,kode,name){state.detail=[];state.detailPage=1;state.detailType=kind;state.detailKode=kode;el('reportMutasiDetailTitle').textContent=(kind==='110'?'Realisasi Baru':'Restrukturisasi')+' - '+name;el('reportMutasiDetailSubtitle').textContent='Posisi '+el('reportMutasiActual').value;el('reportMutasiDetailSearch').value='';el('reportMutasiDetailSummary').classList.add('is-hidden');ui().openModal('reportMutasiDetail');el('reportMutasiDetailBody').innerHTML='<div class="mb-empty">Memuat detail debitur...</div>';el('reportMutasiDetailMobile').innerHTML='<div class="mb-empty">Memuat detail debitur...</div>';renderDetailFooter(0,1);const body={type:'detail_realisasi_growth',closing_date:el('reportMutasiClosing').value,harian_date:el('reportMutasiActual').value,kode_trans:kind,page:1,limit:10000},area=el('reportMutasiArea').value;if(kode!=='ALL'&&String(kode).length===3)body.kode_kantor=kode;else if(area.startsWith('KOR-'))body.korwil=area.slice(4);else if(area.startsWith('CAB-'))body.kode_kantor=area.slice(4);if(body.kode_kantor==='000')body.pusat_only=true;try{const json=await post(api,body);state.detail=Array.isArray(json.data)?json.data:(json.data?.data||[]);renderDetail()}catch(e){state.detail=[];el('reportMutasiDetailBody').innerHTML='<div class="mb-empty mb-negative">Gagal mengambil detail.</div>';renderDetailMobile([],'');renderDetailFooter(0,1)}}
  function exportDetailExcel(){const rows=detailRows();if(!rows.length)return;let html='<table border="1"><thead><tr><th>Rekening</th><th>Nama Nasabah</th><th>Alamat</th><th>Kankas</th><th>AO</th><th>Tanggal Realisasi</th><th>Plafond</th></tr></thead><tbody>';rows.forEach(row=>{html+=`<tr><td style="mso-number-format:'\\@'">${esc(row.no_rekening||'')}</td><td>${esc(row.nama_nasabah||'')}</td><td>${esc(row.alamat||'')}</td><td>${esc(row.nama_kankas||'')}</td><td>${esc(row.nama_ao||'')}</td><td>${esc(row.tgl_realisasi||'')}</td><td>${num(row.plafond)}</td></tr>`});html+='</tbody></table>';const blob=new Blob(['\ufeff'+html],{type:'application/vnd.ms-excel;charset=utf-8'}),a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='Detail_Mutasi_Kredit_'+(el('reportMutasiActual').value||'data')+'.xls';document.body.appendChild(a);a.click();document.body.removeChild(a);URL.revokeObjectURL(a.href)}
  function exportTable(){const blob=new Blob(['<html><meta charset="utf-8"><body>'+el('reportMutasiTable').outerHTML+'</body></html>'],{type:'application/vnd.ms-excel'}),a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='Mutasi_Kredit_'+el('reportMutasiActual').value+'.xls';a.click();URL.revokeObjectURL(a.href)}
  async function loadDefaultDates(){let data={};try{const response=await apiCall(apiDate,{method:'GET'}),json=await response.json();if(!response.ok||Number(json.status)!==200)throw new Error(json.message||'Gagal memuat tanggal');data=json.data||{}}catch(error){console.error('Gagal memuat tanggal default:',error)}const now=new Date(),today=[now.getFullYear(),String(now.getMonth()+1).padStart(2,'0'),String(now.getDate()).padStart(2,'0')].join('-'),actual=String(data.last_created||today),closing=String(data.last_closing||previousEnd(actual));el('reportMutasiActual').value=actual;el('reportMutasiClosing').value=closing}
  async function init(){await loadDefaultDates();try{await loadOffices()}catch(error){console.error('Gagal memuat daftar kantor:',error);el('reportMutasiArea').innerHTML='<option value="ALL">Konsolidasi</option>'}await load()}
  ['reportMutasiClosing','reportMutasiActual','reportMutasiArea'].forEach(id=>el(id).addEventListener('change',()=>{if(id==='reportMutasiActual')el('reportMutasiClosing').value=previousEnd(el(id).value);load()}));el('reportMutasiSearch').addEventListener('input',render);el('reportMutasiExport').addEventListener('click',exportTable);el('reportMutasiDetailSearch').addEventListener('input',()=>{state.detailPage=1;renderDetail()});el('reportMutasiTable').addEventListener('click',e=>{const head=e.target.closest('.mb-sort[data-sort]');if(head){const column=head.dataset.sort;state.sort.direction=state.sort.column===column&&state.sort.direction==='asc'?'desc':'asc';state.sort.column=column;render();return}const t=e.target.closest('[data-kind]');if(t)openDetail(t.dataset.kind,t.dataset.kode,t.dataset.name)});
  const start=()=>window.MonbisUI?init():setTimeout(start,25);start();
})();
</script>

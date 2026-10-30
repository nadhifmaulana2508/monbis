<?php
require_once __DIR__ . '/../components/bootstrap.php';
mb_ui_assets('.');
?>

<style>
  #gambaranKreditPage { --gkc-label-width:205px; width:100%; min-width:0; max-width:none; box-sizing:border-box; height:calc(100dvh - 70px); min-height:360px; }
  .gkc-card { width:100%; min-width:0; min-height:0; overflow:hidden; border:1px solid #dbe3ee; border-radius:12px; background:var(--surface,#fff); }
  .gkc-scroll { width:100%; min-width:0; max-width:100%; min-height:0; overflow:auto; overscroll-behavior:contain; scrollbar-width:thin; -webkit-overflow-scrolling:touch; }
  .gkc-table { width:max-content; min-width:100%; border-collapse:separate; border-spacing:0; color:#1e293b; font-size:11px; }
  .gkc-table th,.gkc-table td { height:31px; padding:5px 9px; border-right:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; white-space:nowrap; }
  .gkc-table thead th { position:sticky; top:0; z-index:20; min-width:108px; background:#f1f5f9; color:#334155; font-size:9px; font-weight:800; text-align:center; }
  .gkc-table thead tr:nth-child(2) th { top:31px; z-index:19; background:#f8fafc; font-size:9px; font-weight:600; }
  .gkc-table .gkc-label { position:sticky !important; left:0 !important; inset-inline-start:0 !important; z-index:12; min-width:var(--gkc-label-width); text-align:left; background:#fff; background-clip:padding-box; box-shadow:4px 0 8px -8px rgba(15,23,42,.75); }
  .gkc-table .gkc-label:not([colspan]) { width:var(--gkc-label-width); min-width:var(--gkc-label-width); max-width:var(--gkc-label-width); overflow:hidden; text-overflow:ellipsis; }
  .gkc-table thead .gkc-label { z-index:30; background:#eaf4ff; }
  .gkc-table .gkc-total { position:sticky; right:0; z-index:13; min-width:120px; background:#f8fafc; font-weight:750; box-shadow:-4px 0 8px -8px rgba(15,23,42,.8); }
  .gkc-table thead .gkc-total { z-index:31; background:#eaf4ff; }
  .gkc-table .gkc-section td { height:27px; background:#edf4f8; color:#35566a; font-weight:850; }
  .gkc-table .gkc-section .gkc-label { z-index:14; background:#edf4f8; }
  .gkc-table .gkc-value { text-align:right; font-variant-numeric:tabular-nums; }
  .gkc-table .gkc-empty { padding:32px 12px; color:#64748b; text-align:center; }
  .gkc-toolbar { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 12px; border-bottom:1px solid #e2e8f0; }
  .gkc-date { height:34px; width:150px; padding:0 9px; border:1px solid #cbd5e1; border-radius:8px; background:transparent; color:inherit; font-size:11px; }
  .gkc-export-mode { height:34px; min-width:145px; padding:0 9px; border:1px solid #cbd5e1; border-radius:8px; background:transparent; color:inherit; font-size:11px; }
  .gkc-btn { display:inline-flex; height:34px; align-items:center; gap:6px; padding:0 11px; border:1px solid #cbd5e1; border-radius:8px; background:#fff; color:#334155; font-size:11px; font-weight:750; cursor:pointer; }
  .gkc-btn:disabled { cursor:wait; opacity:.55; }
  .gkc-status { min-width:0; overflow:hidden; color:#64748b; font-size:10px; text-overflow:ellipsis; white-space:nowrap; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage { color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-card { border-color:#334155; background:#111827; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table { color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table th,:is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table td { border-color:#334155; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table thead th { background:#1e293b; color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table thead tr:nth-child(2) th { background:#172033; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table .gkc-label { background:#111827; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table thead .gkc-label,:is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table thead .gkc-total { background:#1e293b; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table .gkc-total { background:#172033; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table .gkc-section td,:is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table .gkc-section .gkc-label { background:#1d3340; color:#d4e7f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-table .gkc-empty,:is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-status { color:#94a3b8; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-date,:is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-btn { border-color:#475569; background:#1e293b; color:#e2e8f0; }
  :is(.mb-theme-dark,:root[data-monbis-theme="dark"]) #gambaranKreditPage .gkc-export-mode { border-color:#475569; background:#1e293b; color:#e2e8f0; }
  @media(max-width:640px) {
    #gambaranKreditPage { --gkc-label-width:160px; height:calc(100dvh - 58px); padding:6px !important; }
    .gkc-toolbar { align-items:flex-start; flex-direction:column; gap:7px; }
    .gkc-controls { display:flex; width:100%; flex-wrap:wrap; gap:6px; }
    .gkc-date { width:auto; min-width:120px; flex:1 1 120px; }
    .gkc-export-mode { min-width:130px; flex:1 1 130px; }
    .gkc-table { font-size:9px; }
    .gkc-table th,.gkc-table td { padding:5px 7px; }
    .gkc-table .gkc-label { min-width:var(--gkc-label-width); }
    .gkc-table thead th { min-width:92px; }
    .gkc-table .gkc-total { min-width:105px; }
  }
</style>

<main id="gambaranKreditPage" class="w-full max-w-[1920px] mx-auto px-3 py-3 md:px-4 md:py-4 flex flex-col gap-3 text-slate-800">
  <section class="gkc-card flex min-h-0 flex-1 flex-col">
    <header class="gkc-toolbar">
      <div class="min-w-0">
        <h1 class="text-sm font-bold">Gambaran Kredit Cabang</h1>
        <p id="gkcMeta" class="gkc-status mt-1">Memuat tanggal nominatif…</p>
      </div>
      <div class="gkc-controls shrink-0">
        <label class="sr-only" for="gkcDate">Bulan nominatif</label>
        <input id="gkcDate" class="gkc-date" type="month">
        <label class="sr-only" for="gkcExportMode">Rentang export</label>
        <select id="gkcExportMode" class="gkc-export-mode">
          <option value="ytd">Tahun berjalan (YTD)</option>
          <option value="month">1 bulan</option>
        </select>
        <button id="gkcRefresh" class="gkc-btn" type="button" title="Muat ulang data">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 7v5h-5M4 17v-5h5"/><path d="M5.6 9a7 7 0 0 1 11.6-2L20 12M4 12l2.8 5a7 7 0 0 0 11.6-2"/></svg>
          Update
        </button>
        <button id="gkcExport" class="gkc-btn" type="button" disabled>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 17v4h14v-4"/></svg>
          Export
        </button>
      </div>
    </header>
    <div class="gkc-scroll flex-1" id="gkcScroll" tabindex="0" aria-label="Tabel gambaran kredit per kantor">
      <table class="gkc-table" id="gkcTable">
        <thead id="gkcHead"></thead>
        <tbody id="gkcBody"><tr><td class="gkc-empty">Menyiapkan laporan…</td></tr></tbody>
      </table>
    </div>
  </section>
</main>

<script>
(() => {
  const dateInput = document.getElementById('gkcDate');
  const head = document.getElementById('gkcHead');
  const body = document.getElementById('gkcBody');
  const meta = document.getElementById('gkcMeta');
  const refresh = document.getElementById('gkcRefresh');
  const exportButton = document.getElementById('gkcExport');
  const exportModeSelect = document.getElementById('gkcExportMode');
  const numberFormat = new Intl.NumberFormat('id-ID', {maximumFractionDigits:0});
  const productRows = [
    ['135','BKK Mikro'],['136','BKK Agrari'],['137','BKK Joglo'],['138','BKK Migunani'],['139','BKK Makaryo']
  ];
  const dpd = [['0','0 hari'],['1_7','1–7 hari'],['8_30','8–30 hari'],['31_60','31–60 hari'],['61_90','61–90 hari'],['91_plus','>90 hari']];
  const kolek = [['l','L'],['dp','DP'],['kl','KL'],['d','D'],['m','M']];
  const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  let currentData = null;
  let controller = null;
  let requestId = 0;

  const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const monthEndDate = monthValue => {
    const [year, month] = String(monthValue || '').split('-').map(Number);
    if (!year || !month) return '';
    const end = new Date(year, month, 0);
    return `${end.getFullYear()}-${String(end.getMonth()+1).padStart(2,'0')}-${String(end.getDate()).padStart(2,'0')}`;
  };
  const completedMonthValue = dateValue => {
    const [year, month, day] = String(dateValue || '').slice(0,10).split('-').map(Number);
    if (!year || !month || !day) return '';
    const lastDay = new Date(year, month, 0).getDate();
    const end = day === lastDay ? new Date(year, month, 0) : new Date(year, month - 1, 0);
    return `${end.getFullYear()}-${String(end.getMonth()+1).padStart(2,'0')}`;
  };
  const get = (office, key) => Number(office?.metrics?.[key] || 0);
  const format = (value, kind) => kind === 'count' ? numberFormat.format(Math.round(value)) : numberFormat.format(Math.round(value));
  const sum = (offices, key) => offices.reduce((total, office) => total + get(office, key), 0);

  function descriptors(periodDate = currentData?.harian_date, includeFlow = true) {
    const rows = [];
    const section = label => rows.push({section:label});
    const metric = (key, label, kind='money') => rows.push({key,label,kind});
    section('PORTOFOLIO');
    metric('kredit_total','Baki Debet');
    metric('ead_total','EAD · Saldo Bank');
    metric('noa_total','NOA Kredit','count');
    metric('kredit_npl','Kredit NPL');
    metric('noa_npl','NOA NPL','count');
    section('PORTOFOLIO PER PRODUK');
    productRows.forEach(([code,name]) => {
      metric(`kredit_produk_${code}`,`${code} · ${name} — Baki Debet`);
      metric(`noa_produk_${code}`,`${code} · ${name} — NOA`,'count');
      metric(`npl_produk_${code}`,`${code} · ${name} — NPL`);
    });
    section('BREAKDOWN DPD');
    dpd.forEach(([key,label]) => {
      metric(`kredit_dpd_${key}`,`Kredit · ${label}`);
      metric(`noa_dpd_${key}`,`NOA · ${label}`,'count');
      metric(`ead_dpd_${key}`,`EAD · ${label}`);
      metric(`ckpn_collective_dpd_${key}`,`CKPN Kolektif · ${label}`);
      metric(`ckpn_individual_dpd_${key}`,`CKPN Individual · ${label}`);
    });
    section('BREAKDOWN KOLEKTIBILITAS');
    kolek.forEach(([key,label]) => {
      metric(`kredit_kolek_${key}`,`Kredit · Kolek ${label}`);
      metric(`noa_kolek_${key}`,`NOA · Kolek ${label}`,'count');
      metric(`ead_kolek_${key}`,`EAD · Kolek ${label}`);
      metric(`ckpn_collective_kolek_${key}`,`CKPN Kolektif · Kolek ${label}`);
      metric(`ckpn_individual_kolek_${key}`,`CKPN Individual · Kolek ${label}`);
    });
    section('CKPN');
    metric('ckpn_collective_total','CKPN Kolektif');
    metric('ckpn_individual_total','CKPN Individual');
    if (includeFlow) {
      section(`PERGERAKAN BULAN ${months[Number((periodDate || '0000-01').slice(5,7)) - 1] || ''}`);
      [
        ['noa_realisasi','NOA Realisasi Kredit','count'],['total_realisasi','Realisasi Kredit · Baki Debet','money'],
        ['noa_pelunasan','NOA Run Off Pelunasan','count'],['pelunasan','Run Off Pelunasan · Baki Debet','money'],
        ['noa_angsuran_murni','NOA Run Off Angsuran','count'],['angsuran_murni','Run Off Angsuran · Baki Debet','money']
      ].forEach(([key,label,kind]) => metric(`flow_${key}`,label,kind));
    }
    return rows;
  }

  function render(data) {
    const offices = data.offices || [];
    const rows = descriptors();
    head.innerHTML = `<tr><th class="gkc-label">Kode Kantor</th>${offices.map(o=>`<th>${escapeHtml(o.kode_kantor)}</th>`).join('')}<th class="gkc-total">Total</th></tr><tr><th class="gkc-label">Nama Kantor</th>${offices.map(o=>`<th title="${escapeHtml(o.nama_kantor)}">${escapeHtml(o.nama_kantor)}</th>`).join('')}<th class="gkc-total">${offices.length} kantor</th></tr>`;
    const hasData = offices.some(office => Object.keys(office.metrics || {}).length > 0);
    if (!hasData) {
      body.innerHTML = `<tr><td class="gkc-empty" colspan="${offices.length + 2}">Tidak ada data untuk tanggal ${escapeHtml(data.harian_date)}.</td></tr>`;
      exportButton.disabled = true;
      return;
    }
    body.innerHTML = rows.map(row => {
      if (row.section) return `<tr class="gkc-section"><td class="gkc-label" colspan="${offices.length + 2}">${escapeHtml(row.section)}</td></tr>`;
      return `<tr><th scope="row" class="gkc-label">${escapeHtml(row.label)}</th>${offices.map(o=>`<td class="gkc-value">${format(row.key.startsWith('flow_') ? Number(o.flow?.[row.key.slice(5)] || 0) : get(o,row.key),row.kind)}</td>`).join('')}<td class="gkc-value gkc-total">${format(row.key.startsWith('flow_') ? offices.reduce((s,o)=>s+Number(o.flow?.[row.key.slice(5)]||0),0) : sum(offices,row.key),row.kind)}</td></tr>`;
    }).join('');
    exportButton.disabled = !offices.length;
  }

  async function load() {
    if (!dateInput.value) return;
    const selectedEndDate = monthEndDate(dateInput.value);
    const id = ++requestId;
    controller?.abort();
    controller = new AbortController();
    refresh.disabled = true;
    exportButton.disabled = true;
    meta.textContent = `Memuat snapshot nominatif akhir bulan ${selectedEndDate}…`;
    body.innerHTML = `<tr><td class="gkc-empty" colspan="${(currentData?.offices?.length || 28) + 2}">Memuat data…</td></tr>`;
    try {
      const response = await fetch('./api/kredit/', {
        method:'POST', credentials:'include', headers:{'Content-Type':'application/json'},
        body:JSON.stringify({type:'gambaran_kredit_cabang',harian_date:selectedEndDate,resolve_latest_on_or_before:true}),
        signal:controller.signal, cache:'no-store'
      });
      const json = await response.json();
      if (!response.ok || Number(json.status) !== 200) throw new Error(json.message || 'Gagal memuat data laporan.');
      if (id !== requestId) return;
      currentData = json.data;
      const [year, month] = dateInput.value.split('-').map(Number);
      const closing = new Date(year, month - 1, 0);
      const closingDate = `${closing.getFullYear()}-${String(closing.getMonth()+1).padStart(2,'0')}-${String(closing.getDate()).padStart(2,'0')}`;
      const flowResponse = await fetch('./api/kredit/', {
        method:'POST', credentials:'include', headers:{'Content-Type':'application/json'},
        body:JSON.stringify({type:'rekap_realisasi_growth',closing_date:closingDate,harian_date:selectedEndDate,nominal_field:'baki_debet'}),
        signal:controller.signal, cache:'no-store'
      });
      const flowJson = await flowResponse.json();
      if (!flowResponse.ok || Number(flowJson.status) !== 200) throw new Error(flowJson.message || 'Gagal memuat pergerakan kredit bulan ini.');
      const flowByOffice = new Map((flowJson.data?.data || []).map(row=>[String(row.kode_kantor).padStart(3,'0'),row]));
      currentData.offices = (currentData.offices || []).map(office=>({...office,flow:flowByOffice.get(String(office.kode_kantor).padStart(3,'0')) || {}}));
      render(currentData);
      meta.textContent = `Snapshot ${currentData.harian_date} · Pergerakan ${months[month-1]} s.d. akhir bulan · nominal baki debet · EAD = Saldo Bank.`;
    } catch (error) {
      if (error.name === 'AbortError' || id !== requestId) return;
      currentData = null;
      head.innerHTML = '';
      body.innerHTML = `<tr><td class="gkc-empty" colspan="30">${escapeHtml(error.message || 'Terjadi kesalahan saat memuat data.')}</td></tr>`;
      meta.textContent = 'Data gagal dimuat.';
    } finally {
      if (id === requestId) refresh.disabled = false;
    }
  }

  async function exportExcel() {
    if (!currentData?.offices?.length) return;
    const selectedYear = Number(dateInput.value.slice(0,4));
    const selectedMonth = Number(dateInput.value.slice(5,7));
    const selectedDate = dateInput.value;
    const selectedMode = exportModeSelect.value;
    const monthLabel = `${months[selectedMonth - 1]} ${selectedYear}`;
    const periodLabel = `akhir ${selectedYear - 1} + Januari–${months[selectedMonth - 1]} ${selectedYear}`;
    exportButton.disabled = true;
    exportModeSelect.disabled = true;
    meta.textContent = `Menyiapkan export ${periodLabel}…`;
    meta.textContent = `Menyiapkan export ${selectedMode === 'month' ? monthLabel : periodLabel}...`;
    const post = async payload => {
      const response = await fetch('./api/kredit/',{method:'POST',credentials:'include',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload),cache:'no-store'});
      const json = await response.json();
      if (!response.ok || Number(json.status)!==200) throw new Error(json.message || 'Gagal mengambil data export.');
      return json.data;
    };
    try {
      const priorYear = selectedYear - 1;
      const priorYearEnd = `${priorYear}-12-31`;
      const sheets = [];
      if (selectedMode === 'ytd') {
        const baseline = await post({type:'gambaran_kredit_cabang',harian_date:priorYearEnd,resolve_latest_on_or_before:true});
        sheets.push({name:`Akhir ${priorYear}`,endDate:priorYearEnd,snapshotDate:baseline.harian_date,offices:baseline.offices || [],includeFlow:false});
      }
      const exportMonths = selectedMode === 'month' ? [selectedMonth] : Array.from({length:selectedMonth},(_,index)=>index+1);
      for (let start=0; start<exportMonths.length; start+=3) {
        const batchMonths = exportMonths.slice(start,start+3);
        const batch = batchMonths.map(async month => {
          const end = new Date(selectedYear,month,0);
          const endDate = `${selectedYear}-${String(month).padStart(2,'0')}-${String(end.getDate()).padStart(2,'0')}`;
          const previous = new Date(selectedYear,month-1,0);
          const closingDate = `${previous.getFullYear()}-${String(previous.getMonth()+1).padStart(2,'0')}-${String(previous.getDate()).padStart(2,'0')}`;
          const [snapshot, flow] = await Promise.all([
            post({type:'gambaran_kredit_cabang',harian_date:endDate,resolve_latest_on_or_before:true}),
            post({type:'rekap_realisasi_growth',closing_date:closingDate,harian_date:endDate,nominal_field:'baki_debet'})
          ]);
          const flowMap = new Map((flow.data || []).map(row=>[String(row.kode_kantor).padStart(3,'0'),row]));
          const offices = (snapshot.offices || []).map(office=>({...office,flow:flowMap.get(String(office.kode_kantor).padStart(3,'0'))||{}}));
          sheets.push({name:months[month-1],endDate,closingDate,snapshotDate:snapshot.harian_date,offices,includeFlow:true});
        });
        await Promise.all(batch);
        if (dateInput.value !== selectedDate || exportModeSelect.value !== selectedMode) throw new Error('Tanggal atau rentang export berubah saat proses berlangsung. Silakan export ulang.');
        meta.textContent = `Data export ${selectedYear}: ${Math.min(start+2,selectedMonth)} dari ${selectedMonth} bulan selesai…`;
      }
      meta.textContent = `Data export ${selectedYear}: ${exportMonths.length} dari ${exportMonths.length} bulan selesai.`;
      const xml = value => String(value ?? '').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&apos;'}[c]));
      const spreadsheetCell = (value, type='Number') => `<Cell><Data ss:Type="${type}">${type==='String'?xml(value):String(Number(value)||0)}</Data></Cell>`;
      const worksheets = sheets.map(sheet=>{
        const rows = descriptors(sheet.endDate,sheet.includeFlow);
        let table = `<Row>${spreadsheetCell('Snapshot nominatif','String')}${spreadsheetCell(sheet.snapshotDate || 'Data tidak tersedia','String')}</Row>`;
        if (sheet.includeFlow) table += `<Row>${spreadsheetCell('Pergerakan','String')}${spreadsheetCell(`${sheet.closingDate} s.d. ${sheet.endDate} (nominal baki debet)`,'String')}</Row>`;
        table += '<Row>'+spreadsheetCell('Indikator','String')+sheet.offices.map(o=>spreadsheetCell(`${o.kode_kantor} · ${o.nama_kantor}`,'String'))+spreadsheetCell('Total','String')+'</Row>';
        rows.forEach(row=>{
          if (row.section) { table += `<Row>${spreadsheetCell(row.section,'String')}</Row>`; return; }
          const value = office => row.key.startsWith('flow_') ? Number(office.flow?.[row.key.slice(5)]||0) : get(office,row.key);
          table += '<Row>'+spreadsheetCell(row.label,'String')+sheet.offices.map(o=>spreadsheetCell(value(o)))+spreadsheetCell(sheet.offices.reduce((sum,o)=>sum+value(o),0))+'</Row>';
        });
        return `<Worksheet ss:Name="${xml(sheet.name)}"><Table>${table}</Table></Worksheet>`;
      }).join('');
      const workbook = '<' + `?xml version="1.0"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"><Styles><Style ss:ID="Default"><NumberFormat ss:Format="#,##0"/></Style></Styles>${worksheets}</Workbook>`;
      const link = document.createElement('a');
      link.href = URL.createObjectURL(new Blob([workbook],{type:'application/vnd.ms-excel;charset=utf-8'}));
      link.download = selectedMode === 'month'
        ? `Gambaran_Kredit_Cabang_${months[selectedMonth-1]}-${selectedYear}.xls`
        : `Gambaran_Kredit_Cabang_Akhir-${priorYear}_Jan-${months[selectedMonth-1]}-${selectedYear}.xls`;
      document.body.appendChild(link); link.click(); link.remove(); setTimeout(()=>URL.revokeObjectURL(link.href),1000);
      meta.textContent = `Export selesai: pijakan akhir ${priorYear} dan Januari–${months[selectedMonth-1]} ${selectedYear}; realisasi tidak termasuk restrukturisasi.`;
      if (selectedMode === 'month') meta.textContent = `Export selesai: 1 sheet untuk ${monthLabel}; realisasi tidak termasuk restrukturisasi.`;
    } catch(error) {
      meta.textContent = error.message || 'Export gagal dibuat.';
    } finally {
      exportModeSelect.disabled = false;
      exportButton.disabled = !currentData?.offices?.length;
    }
  }

  refresh.addEventListener('click',load);
  dateInput.addEventListener('change',load);
  exportButton.addEventListener('click',exportExcel);
  fetch('./api/date/',{credentials:'include',cache:'no-store'}).then(r=>r.json()).then(json=>{
    const latestDate = String(json.data?.last_created || '');
    const latestCompletedMonth = completedMonthValue(latestDate);
    dateInput.max = latestCompletedMonth || '';
    dateInput.value = latestCompletedMonth || completedMonthValue(new Date().toISOString().slice(0,10));
  }).catch(()=>{
    const fallback = new Date();
    fallback.setDate(0);
    dateInput.value = completedMonthValue(`${fallback.getFullYear()}-${String(fallback.getMonth()+1).padStart(2,'0')}-${String(fallback.getDate()).padStart(2,'0')}`);
  }).finally(load);
})();
</script>

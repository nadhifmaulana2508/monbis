<div id="recoveryPHPage" class="max-w-[1920px] mx-auto px-2 md:px-4 py-3 md:py-5 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">

  <!-- =========================================================
       HEADER / FILTER - KONSISTEN DENGAN PAGE LAIN
  ========================================================== -->
  <div class="flex-none mb-3 md:mb-4 w-full shrink-0">
    <div class="relative bg-white border border-slate-200 rounded-xl shadow-sm px-2.5 md:px-5 py-2.5 md:py-4">
      <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3 w-full">
        <div class="flex items-center justify-between w-full xl:w-auto shrink-0">
          <div class="flex items-center gap-2 md:gap-3 min-w-0">
            <span class="p-1.5 md:p-2.5 bg-blue-600 rounded-lg text-white shadow-sm shrink-0">
              <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </span>

            <div class="min-w-0">
              <div class="flex items-center gap-2 min-w-0">
                <h1 class="text-[13px] md:text-2xl font-extrabold text-slate-900 tracking-tight leading-none truncate">Recovery PH</h1>
                <button type="button" onclick="toggleInfoPH()" class="w-4 h-4 md:w-5 md:h-5 rounded-full bg-blue-500 text-white flex items-center justify-center text-[10px] md:text-xs font-black hover:bg-blue-600 transition shrink-0" title="Informasi Recovery PH">i</button>
              </div>
              <p class="text-[7.5px] md:text-[11px] text-slate-500 italic mt-0.5 md:mt-1 truncate">*Recovery Pinjaman Hapus Buku, default posisi data actual.</p>
            </div>
          </div>

          <button type="button" onclick="toggleFilterPH()" class="xl:hidden h-[26px] px-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] whitespace-nowrap ml-2 shrink-0">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            Filter
          </button>
        </div>

        <div id="filterWrapperPH" class="hidden xl:flex w-full xl:w-auto transition-all duration-300">
          <form id="filterForm" class="w-full xl:w-auto flex flex-row items-end gap-2 overflow-x-auto no-scrollbar" onsubmit="event.preventDefault(); fetchData(start_date.value, end_date.value);">
            <div class="field shrink-0 w-[128px] md:w-[145px]">
              <label class="lbl">DARI</label>
              <input type="date" id="start_date" class="inp w-full" required onchange="fetchData(start_date.value, end_date.value)" onclick="try{this.showPicker()}catch(e){}">
            </div>
            <div class="field shrink-0 w-[128px] md:w-[145px]">
              <label class="lbl">SAMPAI</label>
              <input type="date" id="end_date" class="inp w-full" required onchange="fetchData(start_date.value, end_date.value)" onclick="try{this.showPicker()}catch(e){}">
            </div>
            <button type="button" onclick="exportRecoveryPH()" class="btn-icon h-[32px] md:h-[38px] w-[36px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm shrink-0" title="Download Excel">
              <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="md:w-[18px] md:h-[18px]"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            </button>
          </form>
        </div>
      </div>

      <div id="infoPH" class="hidden absolute left-3 md:left-[170px] top-[58px] md:top-[66px] w-[330px] md:w-[390px] max-w-[calc(100vw-24px)] bg-white border border-slate-200 rounded-xl shadow-xl z-[999] overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100">
          <h3 class="text-sm font-black text-slate-900">Informasi Recovery PH</h3>
        </div>
        <div class="px-4 py-3 text-[11px] md:text-xs text-slate-700 leading-relaxed space-y-2">
          <p><b>Recovery PH</b> adalah monitoring pembayaran debitur yang sudah masuk posisi hapus buku.</p>
          <div class="border border-slate-200 rounded-lg p-2 bg-slate-50"><b>Pokok</b>: nominal pembayaran pokok pada periode yang dipilih.</div>
          <div class="border border-slate-200 rounded-lg p-2 bg-slate-50"><b>Bunga</b>: nominal pembayaran bunga pada periode yang dipilih.</div>
          <div class="border border-slate-200 rounded-lg p-2 bg-slate-50"><b>Total</b>: akumulasi pokok + bunga.</div>
          <div class="border-t border-slate-200 pt-2 font-bold text-slate-900">Default tanggal memakai tanggal 1 bulan berjalan sampai hari ini.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- =========================================================
       TABLE
  ========================================================== -->
  <div id="phScroller" class="flex-1 min-h-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm relative flex flex-col">
    <div id="loadingPH" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
      <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-2"></div>
      <span class="text-[10px] md:text-sm font-bold tracking-widest uppercase">Memuat Data...</span>
    </div>

    <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
      <table id="tabelRecovery" class="min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed">
        <thead class="uppercase bg-slate-50 text-slate-600 font-bold select-none">
          <tr id="phHead1" class="text-[8.5px] md:text-[10px] tracking-wider">
            <th class="sticky-ph freeze-1 col1 col-kode w-[64px] md:w-[80px] px-2 md:px-3 py-2 text-center">KODE</th>
            <th class="sticky-ph freeze-2 col2 col-nama px-3 md:px-4 py-2 text-left">NAMA KANTOR</th>
            <th class="px-3 md:px-4 py-2 text-right w-[105px] md:w-[150px]">POKOK</th>
            <th class="px-3 md:px-4 py-2 text-right w-[105px] md:w-[150px] col-bunga">BUNGA</th>
            <th class="px-3 md:px-4 py-2 text-right w-[105px] md:w-[150px] col-total">TOTAL</th>
            <th class="px-2 md:px-4 py-2 text-center w-[70px] md:w-[90px] col-noa">NOA</th>
          </tr>
        </thead>
        <tbody id="tbodyPH" class="divide-y divide-slate-100 bg-white text-[9.5px] md:text-sm"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- =========================================================
     MODAL DETAIL
========================================================== -->
<div id="modalDebitur" class="fixed inset-0 hidden bg-slate-900/60 backdrop-blur-sm items-end md:items-center justify-center z-[100000] p-0 md:p-4">
  <div id="modalCardPH" class="relative bg-white w-full h-[92vh] md:h-[88vh] max-w-[1500px] rounded-t-xl md:rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-scale-up">
    <div class="flex items-center justify-between px-3 md:px-5 py-3 border-b bg-white shrink-0">
      <div class="min-w-0">
        <h3 id="modalTitle" class="text-[13px] md:text-xl font-black text-slate-900 truncate">Daftar Debitur</h3>
        <p id="modalSubTitlePH" class="text-[9px] md:text-xs text-slate-500 mt-1 font-mono truncate">-</p>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <button type="button" onclick="exportDetailPH()" class="btn-icon h-[34px] md:h-[38px] w-[36px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm" title="Download Detail">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="md:w-[18px] md:h-[18px]"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
        </button>
        <button id="btnClosePH" class="w-[34px] h-[34px] md:w-[38px] md:h-[38px] flex items-center justify-center rounded-lg bg-slate-100 hover:bg-red-500 hover:text-white text-slate-500 transition font-bold text-lg" aria-label="Tutup">&times;</button>
      </div>
    </div>
    <div id="modalBody" class="flex-1 overflow-auto bg-slate-50 p-0 md:p-3 custom-scrollbar"></div>
  </div>
</div>

<style>
  .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  @keyframes scaleUp { from { transform: scale(.96); opacity: 0; } to { transform: scale(1); opacity: 1; } }
  .animate-scale-up { animation: scaleUp .18s ease-out forwards; }

  .inp { border:1px solid #cbd5e1; border-radius:8px; height:32px; padding:0 10px; background:#f8fafc; outline:none; color:#0f172a; font-weight:700; font-size:10px; transition:.2s; }
  .inp:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.10); background:#fff; }
  .lbl { font-size:9px; color:#334155; font-weight:900; margin-bottom:4px; text-transform:uppercase; letter-spacing:.08em; display:block; white-space:nowrap; }
  .field { display:flex; flex-direction:column; }
  .btn-icon { display:inline-flex; align-items:center; justify-content:center; border:none; cursor:pointer; transition:.2s; }
  .btn-icon:hover { transform:translateY(-1px); box-shadow:0 6px 12px -6px rgba(15,23,42,.35); }

  @media (min-width:768px) {
    .inp { height:38px; font-size:14px; padding:0 12px; }
    .lbl { font-size:11px; }
  }

  input[type="date"]::-webkit-inner-spin-button,
  input[type="date"]::-webkit-calendar-picker-indicator { display:none; -webkit-appearance:none; }

  #tabelRecovery th, #tabelRecovery td { background-clip:padding-box; }
  #tabelRecovery thead th {
    position:sticky !important;
    top:0 !important;
    z-index:40;
    height:38px;
    background:#eaf6ff !important;
    color:#0f2f68;
    border-right:1px solid #cbd5e1;
    border-bottom:1px solid #cbd5e1;
    font-weight:900;
    white-space:nowrap;
  }

  #tabelRecovery tbody td {
    height:38px;
    border-right:1px solid #e2e8f0;
    border-bottom:1px solid #f1f5f9;
    white-space:nowrap;
  }

  #tabelRecovery .total-row td {
    position:sticky;
    top:38px;
    z-index:38;
    height:38px;
    background:#eff6ff !important;
    color:#0b3a91;
    font-weight:900;
    border-bottom:1px solid #93c5fd;
  }

  .freeze-1 { position:sticky !important; left:0 !important; z-index:30; background:#fff !important; }
  .freeze-2 { position:sticky !important; left:64px !important; z-index:29; background:#fff !important; box-shadow:inset -1px 0 0 #e2e8f0; }
  #tabelRecovery thead th.freeze-1, #tabelRecovery thead th.freeze-2 { z-index:60 !important; background:#eaf6ff !important; }
  #tabelRecovery .total-row td.freeze-1, #tabelRecovery .total-row td.freeze-2 { z-index:58 !important; background:#eff6ff !important; }

  #tbodyPH tr:hover td { background:#f8fafc !important; }
  #tbodyPH tr:hover td.freeze-1, #tbodyPH tr:hover td.freeze-2 { background:#f8fafc !important; }

  .ph-click { color:#0b4eea; font-weight:900; cursor:pointer; }
  .ph-click:hover { text-decoration:underline; }

  #modalTablePH { width:max-content; min-width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; }
  #modalTablePH th {
    position:sticky; top:0; z-index:40;
    background:#f1f5f9 !important;
    color:#334155;
    border-right:1px solid #cbd5e1;
    border-bottom:1px solid #cbd5e1;
    font-weight:900;
    text-transform:uppercase;
    white-space:nowrap;
  }
  #modalTablePH td {
    background:#fff;
    border-right:1px solid #e2e8f0;
    border-bottom:1px solid #e2e8f0;
    white-space:nowrap;
  }
  .modal-freeze-rek, .modal-td-rek { position:sticky !important; left:0 !important; min-width:120px; max-width:120px; z-index:30; background:#fff !important; }
  .modal-freeze-nama, .modal-td-nama { position:sticky !important; left:120px !important; min-width:220px; max-width:220px; z-index:29; background:#fff !important; box-shadow:inset -1px 0 0 #e2e8f0; }
  #modalTablePH th.modal-freeze-rek, #modalTablePH th.modal-freeze-nama { z-index:60 !important; background:#f1f5f9 !important; }
  #modalTablePH tbody tr:hover td { background:#f8fafc !important; }
  #modalTablePH tbody tr:hover td.modal-td-rek, #modalTablePH tbody tr:hover td.modal-td-nama { background:#f8fafc !important; }

  @media (max-width:640px) {
    /* MOBILE SUPER COMPACT: kolom tidak makan space, data tetap kebaca */
    #recoveryPHPage {
      padding-left:3px !important;
      padding-right:3px !important;
      padding-top:5px !important;
      height:calc(100vh - 52px) !important;
    }

    #recoveryPHPage > .flex-none { margin-bottom:5px !important; }
    #recoveryPHPage > .flex-none > .relative {
      border-radius:10px !important;
      padding:6px 7px !important;
    }

    #recoveryPHPage h1 { font-size:11.5px !important; letter-spacing:-.02em; }
    #recoveryPHPage p { font-size:6.8px !important; max-width:145px; }
    #recoveryPHPage .p-1\.5 { padding:5px !important; }
    #recoveryPHPage .w-4.h-4 { width:12px !important; height:12px !important; font-size:7px !important; }
    #recoveryPHPage .rounded-lg { border-radius:8px; }

    #filterWrapperPH form { gap:5px !important; }
    #filterWrapperPH .field { width:104px !important; }
    #filterWrapperPH .inp { height:27px !important; font-size:8.5px !important; padding:0 6px !important; }
    #filterWrapperPH .lbl { font-size:7px !important; margin-bottom:2px !important; }

    #phScroller { border-radius:8px; }
    #phScroller .custom-scrollbar, #phScroller .overflow-auto { scrollbar-width:thin; }

    /* Lebar tabel dibuat kecil, tapi tetap horizontal-scroll bila layar sangat sempit */
    #tabelRecovery {
      min-width:430px !important;
      width:430px !important;
      table-layout:fixed !important;
    }

    #tabelRecovery thead th {
      height:25px !important;
      font-size:6.8px !important;
      padding:3px 3px !important;
      letter-spacing:.015em !important;
      line-height:1.05 !important;
    }

    #tabelRecovery tbody td {
      height:23px !important;
      padding:2px 3px !important;
      font-size:7.4px !important;
      line-height:1.05 !important;
    }

    /* Kode disembunyikan, kolom lain tetap tampil rapat */
    #tabelRecovery th.col-kode,
    #tabelRecovery td.col-kode { display:none !important; }

    #tabelRecovery .freeze-2,
    #tabelRecovery thead th.freeze-2 { left:0 !important; }

    #tabelRecovery .col-nama,
    #tabelRecovery th.col-nama,
    #tabelRecovery td.col-nama {
      width:92px !important;
      min-width:92px !important;
      max-width:92px !important;
      padding-left:5px !important;
      padding-right:3px !important;
      overflow:hidden !important;
      text-overflow:ellipsis !important;
    }

    #tabelRecovery th:nth-child(3), #tabelRecovery td:nth-child(3) { width:82px !important; }
    #tabelRecovery .col-bunga { width:72px !important; display:table-cell !important; }
    #tabelRecovery .col-total { width:82px !important; display:table-cell !important; }
    #tabelRecovery .col-noa { width:42px !important; display:table-cell !important; }

    #tabelRecovery td:nth-child(n+3),
    #tabelRecovery th:nth-child(n+3) {
      padding-left:3px !important;
      padding-right:4px !important;
    }

    #tabelRecovery .total-row td {
      top:25px !important;
      height:24px !important;
      font-size:7.3px !important;
    }

    #tbodyPH .ph-click { font-size:7.2px !important; }

    /* Modal mobile tetap compact dan freeze */
    #modalCardPH { height:92vh !important; }
    #modalTablePH { min-width:610px !important; font-size:8.5px; }
    #modalTablePH th,
    #modalTablePH td { padding:4px 5px !important; font-size:8.5px !important; line-height:1.1 !important; }
    .modal-freeze-rek, .modal-td-rek { min-width:86px !important; max-width:86px !important; }
    .modal-freeze-nama, .modal-td-nama { left:86px !important; min-width:116px !important; max-width:116px !important; }
    .modal-td-nama div { max-width:108px; overflow:hidden; text-overflow:ellipsis; }
  }
</style>

<script>
  const ymd = d => d.toISOString().split('T')[0];

  let phAbortCtrl = null;
  let phModalAbort = null;
  let recoveryPHCache = [];
  let detailPHCache = [];
  let currentDetailKode = '';
  let currentDetailStart = '';
  let currentDetailEnd = '';

  initRecoveryPHDefaultDate();

  function initRecoveryPHDefaultDate() {
    const today = new Date();
    const startDef = new Date(today.getFullYear(), today.getMonth(), 1, 12);
    const endDef = new Date(today.getFullYear(), today.getMonth(), today.getDate(), 12);

    start_date.value = ymd(startDef);
    end_date.value = ymd(endDef);
    fetchData(start_date.value, end_date.value);
  }

  function toggleFilterPH() {
    const el = document.getElementById('filterWrapperPH');
    if (!el) return;
    if (el.classList.contains('hidden')) {
      el.classList.remove('hidden');
      el.classList.add('flex');
    } else {
      el.classList.add('hidden');
      el.classList.remove('flex');
    }
  }

  function toggleInfoPH() {
    document.getElementById('infoPH')?.classList.toggle('hidden');
  }

  document.addEventListener('click', function(e) {
    const info = document.getElementById('infoPH');
    if (!info) return;
    const isBtn = e.target.closest('[onclick="toggleInfoPH()"]');
    const isInside = e.target.closest('#infoPH');
    if (!isBtn && !isInside) info.classList.add('hidden');
  });

  function setPHSticky(){
    const h = document.getElementById('phHead1')?.offsetHeight || 38;
    document.getElementById('phScroller')?.style.setProperty('--headH', h + 'px');
  }

  window.addEventListener('resize', setPHSticky);

  document.getElementById('filterForm').addEventListener('submit', (e)=>{
    e.preventDefault();
    fetchData(start_date.value, end_date.value);
  });

  function fetchData(start_date, end_date){
    if(phAbortCtrl) phAbortCtrl.abort();
    phAbortCtrl = new AbortController();

    const loading = document.getElementById('loadingPH');
    const tbody = document.getElementById('tbodyPH');
    loading?.classList.remove('hidden');
    tbody.innerHTML = `<tr><td colspan="6" class="py-16 text-center text-slate-400 italic">Memuat data...</td></tr>`;

    fetch('./api/hapus_buku/', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type: 'recovery', start_date, end_date }),
      signal: phAbortCtrl.signal
    })
    .then(r=>r.json())
    .then(res=>{
      const data = Array.isArray(res.data) ? res.data : [];
      recoveryPHCache = data;
      renderRecoveryPH(data, start_date, end_date);
    })
    .catch(err=>{
      if(err.name !== 'AbortError') {
        tbody.innerHTML = `<tr><td colspan="6" class="py-16 text-center text-red-500 font-bold uppercase tracking-widest">Gagal memuat data</td></tr>`;
      }
    })
    .finally(()=> loading?.classList.add('hidden'));
  }

  function renderRecoveryPH(data, start_date, end_date) {
    const tbody = document.getElementById('tbodyPH');
    tbody.innerHTML = '';

    const totalRow = data.find(d => d.kode_kantor === 'TOTAL');
    if(totalRow){
      tbody.insertAdjacentHTML('beforeend', `
        <tr class="total-row">
          <td class="freeze-1 col1 col-kode px-2 md:px-3 py-2 text-center">TOTAL</td>
          <td class="freeze-2 col2 col-nama px-3 md:px-4 py-2 text-left">TOTAL</td>
          <td class="px-3 md:px-4 py-2 text-right">${rupiah(totalRow.total_pokok)}</td>
          <td class="px-3 md:px-4 py-2 text-right col-bunga">${rupiah(totalRow.total_bunga)}</td>
          <td class="px-3 md:px-4 py-2 text-right col-total">${rupiah(totalRow.total_ph)}</td>
          <td class="px-2 md:px-4 py-2 text-center col-noa">${fmtInt(totalRow.noa)}</td>
        </tr>
      `);
    }

    data.filter(d => d.kode_kantor !== 'TOTAL').forEach(d=>{
      const kode = escAttr(d.kode_kantor);
      const nama = esc(d.nama_kantor);
      const clickable = Number(d.noa || 0) > 0;
      const clickAttr = clickable ? `onclick="loadDebitur('${kode}','${start_date}','${end_date}', '${escAttr(d.nama_kantor)}')"` : '';
      const cls = clickable ? 'ph-click' : 'text-slate-500';

      tbody.insertAdjacentHTML('beforeend', `
        <tr>
          <td class="freeze-1 col1 col-kode px-2 md:px-3 py-2 text-center font-mono text-slate-500">${esc(d.kode_kantor)}</td>
          <td class="freeze-2 col2 col-nama px-3 md:px-4 py-2 text-left ${cls}" title="${escAttr(d.nama_kantor)}" ${clickAttr}>
            <div class="truncate">${nama}</div>
          </td>
          <td class="px-3 md:px-4 py-2 text-right font-bold text-slate-800">${rupiah(d.total_pokok)}</td>
          <td class="px-3 md:px-4 py-2 text-right col-bunga">${rupiah(d.total_bunga)}</td>
          <td class="px-3 md:px-4 py-2 text-right col-total">${rupiah(d.total_ph)}</td>
          <td class="px-2 md:px-4 py-2 text-center col-noa ${cls}" ${clickAttr}>${fmtInt(d.noa)}</td>
        </tr>
      `);
    });

    setPHSticky();
  }

  function loadDebitur(kodeKantor, start, end, namaKantor = ''){
    if(phModalAbort) phModalAbort.abort();
    phModalAbort = new AbortController();

    currentDetailKode = kodeKantor;
    currentDetailStart = start;
    currentDetailEnd = end;

    const overlay = document.getElementById('modalDebitur');
    const title   = document.getElementById('modalTitle');
    const sub     = document.getElementById('modalSubTitlePH');
    const body    = document.getElementById('modalBody');

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    title.textContent = `Daftar Debitur - Kode Kantor ${kodeKantor}`;
    sub.textContent = `${namaKantor || '-'} | Periode: ${start} s/d ${end}`;
    body.innerHTML = `<div class="h-full flex items-center justify-center py-20 text-blue-600 font-bold uppercase tracking-widest text-[10px] md:text-sm">Mengambil data debitur...</div>`;

    fetch('./api/hapus_buku/detail', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type:'debitur', kode_kantor:kodeKantor, start_date:start, end_date:end }),
      signal: phModalAbort.signal
    })
    .then(r=>r.json())
    .then(res=>{
      const list = Array.isArray(res.data) ? res.data : [];
      detailPHCache = list;
      if(!list.length){
        body.innerHTML = `<div class="py-20 text-center text-red-500 font-bold">Tidak ada data.</div>`;
        return;
      }
      renderDetailPH(list);
    })
    .catch(err=>{
      if(err.name !== 'AbortError') body.innerHTML = `<div class="py-20 text-center text-red-500 font-bold">Gagal mengambil data.</div>`;
    });

    const close = ()=>{ overlay.classList.add('hidden'); overlay.classList.remove('flex'); };
    document.getElementById('btnClosePH').onclick = close;
    overlay.onclick = (e)=>{ if(!e.target.closest('#modalCardPH')) close(); };
  }
  window.loadDebitur = loadDebitur;

  function renderDetailPH(list) {
    let html = `
      <table id="modalTablePH" class="text-left text-slate-700 bg-white">
        <thead class="text-[9px] md:text-xs">
          <tr>
            <th class="modal-freeze-rek px-3 py-2 text-center">No Rekening</th>
            <th class="modal-freeze-nama px-3 py-2 text-left">Nama Nasabah</th>
            <th class="px-3 py-2 text-center w-[130px]">Tanggal Transaksi</th>
            <th class="px-3 py-2 text-right w-[120px]">Pokok</th>
            <th class="px-3 py-2 text-right w-[110px]">Bunga</th>
            <th class="px-3 py-2 text-right w-[120px]">Total</th>
          </tr>
        </thead>
        <tbody class="text-[10px] md:text-sm">`;

    list.forEach(d=>{
      html += `
        <tr>
          <td class="modal-td-rek px-3 py-2 text-center font-mono text-slate-600">${esc(d.no_rekening)}</td>
          <td class="modal-td-nama px-3 py-2 font-bold text-slate-700" title="${escAttr(d.nama_nasabah)}"><div>${esc(d.nama_nasabah)}</div></td>
          <td class="px-3 py-2 text-center font-mono text-slate-600">${esc(d.tanggal_transaksi)}</td>
          <td class="px-3 py-2 text-right">${rupiah(d.pokok)}</td>
          <td class="px-3 py-2 text-right">${rupiah(d.bunga)}</td>
          <td class="px-3 py-2 text-right font-bold text-blue-700">${rupiah(d.total)}</td>
        </tr>`;
    });

    html += `</tbody></table>`;
    document.getElementById('modalBody').innerHTML = html;
  }

  function exportRecoveryPH() {
    if (!recoveryPHCache.length) return alert('Tidak ada data untuk diexport.');
    let table = `<table border="1"><thead><tr><th>Kode Kantor</th><th>Nama Kantor</th><th>Pokok</th><th>Bunga</th><th>Total</th><th>NOA</th></tr></thead><tbody>`;
    recoveryPHCache.forEach(d=>{
      table += `<tr><td style="mso-number-format:'\\@'">${esc(d.kode_kantor)}</td><td>${esc(d.nama_kantor || (d.kode_kantor === 'TOTAL' ? 'TOTAL' : ''))}</td><td>${Number(d.total_pokok || 0)}</td><td>${Number(d.total_bunga || 0)}</td><td>${Number(d.total_ph || 0)}</td><td>${Number(d.noa || 0)}</td></tr>`;
    });
    downloadXls(table + `</tbody></table>`, `Recovery_PH_${start_date.value}_${end_date.value}.xls`);
  }

  function exportDetailPH() {
    if (!detailPHCache.length) return alert('Tidak ada data detail untuk diexport.');
    let table = `<table border="1"><thead><tr><th>No Rekening</th><th>Nama Nasabah</th><th>Tanggal Transaksi</th><th>Pokok</th><th>Bunga</th><th>Total</th></tr></thead><tbody>`;
    detailPHCache.forEach(d=>{
      table += `<tr><td style="mso-number-format:'\\@'">${esc(d.no_rekening)}</td><td>${esc(d.nama_nasabah)}</td><td>${esc(d.tanggal_transaksi)}</td><td>${Number(d.pokok || 0)}</td><td>${Number(d.bunga || 0)}</td><td>${Number(d.total || 0)}</td></tr>`;
    });
    downloadXls(table + `</tbody></table>`, `Detail_Recovery_PH_${currentDetailKode}_${currentDetailStart}_${currentDetailEnd}.xls`);
  }

  function downloadXls(html, filename) {
    const blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    a.click();
    URL.revokeObjectURL(a.href);
  }

  const rupiah = n => new Intl.NumberFormat('id-ID').format(+n || 0);
  const fmtInt = n => new Intl.NumberFormat('id-ID',{ maximumFractionDigits:0 }).format(+n || 0);
  const esc = s => String(s ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#39;');
  const escAttr = s => String(s ?? '').replaceAll('&','&amp;').replaceAll('"','&quot;').replaceAll("'",'&#39;').replaceAll('<','&lt;').replaceAll('>','&gt;');
</script>

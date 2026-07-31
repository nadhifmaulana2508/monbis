<div class="update-page max-w-full mx-auto px-2 md:px-4 py-3 md:py-4">
  <div class="update-header mb-3 rounded-2xl border border-slate-200 bg-white px-3 md:px-5 py-3 shadow-sm">
    <h1 id="judulHalaman" class="text-lg md:text-2xl font-bold text-slate-800 flex items-center gap-2">
      📌 Update Progres Flow PAR
    </h1>
    <p id="judul_kantor" class="text-[10px] md:text-xs text-gray-600 font-semibold mt-1 ml-1"></p>
  </div>

  <div id="upWrap" class="overflow-auto rounded-xl border border-gray-200 bg-white shadow-sm"
       style="--colName: 17rem;">
    <table id="tblUpdate" class="min-w-[1180px] w-full text-[11px] md:text-xs text-left text-gray-800">
      <thead class="uppercase">
        <tr id="upHead1">
          <th class="px-2 py-3 th sticky top-0 freeze-1 col-name">DEBITUR</th>
          <th class="px-2 py-3 th sticky top-0 text-center">KOLEK</th>
          <th class="px-2 py-3 th sticky top-0 text-right">BAKI DEBET</th>
          <th class="px-2 py-3 th sticky top-0 text-right">TUNGG. POKOK</th>
          <th class="px-2 py-3 th sticky top-0 text-right">TUNGG. BUNGA</th>
          <th class="px-2 py-3 th sticky top-0 text-center">DPD TP</th>
          <th class="px-2 py-3 th sticky top-0 text-center">DPD TB</th>
          <th class="px-2 py-3 th sticky top-0 text-center">JATUH TEMPO</th>
          <th class="px-2 py-3 th sticky top-0">KOMITMEN</th>
          <th class="px-2 py-3 th sticky top-0 text-right">NOMINAL</th>
          <th class="px-2 py-3 th sticky top-0">ALASAN</th>
          <th class="px-2 py-3 th sticky top-0 text-center">AKSI</th>
        </tr>
      </thead>

      <tbody id="upTotalRow"></tbody>

      <tbody id="flowparBody"></tbody>
    </table>
  </div>
</div>

<div id="komitmenModal"
     class="fixed inset-0 hidden items-end md:items-center justify-center p-2 md:p-4"
     style="z-index:2147483647; background:rgba(15, 23, 42, 0.6); backdrop-filter:blur(4px);">
  <div id="modalCard"
       class="bg-white rounded-2xl shadow-2xl w-full max-w-[550px] max-h-[92vh] flex flex-col overflow-hidden animate-scale-up">
    
    <div class="flex items-center justify-between px-4 md:px-5 py-3 md:py-4 border-b bg-slate-50">
      <h2 class="text-lg font-bold text-slate-800">📝 Input Komitmen</h2>
      <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition text-2xl">✕</button>
    </div>

    <div class="p-4 md:p-5 overflow-y-auto space-y-4">
      <input type="hidden" id="modal_rekening">

      <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 space-y-2">
        <div class="flex justify-between items-center">
            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">No. Rekening</span>
            <span id="modal_rekening_text" class="text-xs font-mono font-bold text-blue-900"></span>
        </div>
        <div class="flex justify-between items-start gap-4">
            <span class="text-sm font-bold text-slate-700 leading-tight" id="modal_nama"></span>
            <span class="text-sm font-black text-blue-700 whitespace-nowrap" id="modal_baki"></span>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4">
        <div>
          <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5 ml-1">Komitmen DPD</label>
          <select id="modal_komitmen" class="inp w-full font-semibold !h-10"></select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5 ml-1">Tgl Bayar</label>
              <input type="date" id="modal_tanggal" class="inp w-full !h-10">
            </div>
            <div>
              <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5 ml-1">Nominal Janji</label>
              <input type="number" id="modal_nominal" class="inp w-full !h-10" placeholder="0">
            </div>
        </div>

        <div>
          <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1.5 ml-1">Alasan Keterlambatan</label>
          <textarea id="modal_alasan" rows="2" class="inp w-full !h-auto py-2" placeholder="Tulis alasan..."></textarea>
        </div>
      </div>
    </div>

    <div class="px-4 md:px-5 py-3 md:py-4 border-t bg-slate-50 flex gap-3">
      <button onclick="closeModal()" class="flex-1 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-100 transition text-sm">Batal</button>
      <button id="btnSaveKomitmen" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition text-sm">Simpan</button>
    </div>
  </div>
</div>

<style>
/* ====== UI FIXES ====== */
.hide-scrollbar{ scrollbar-width:none; -ms-overflow-style:none; }
.hide-scrollbar::-webkit-scrollbar{ width:0 !important; height:0 !important; }

#tblUpdate{ border-collapse:separate; border-spacing:0; table-layout:fixed; }
/* WARNA HEADER HIJAU SESUAI REQUEST */
#tblUpdate .th{ background:#d9ead3; color:#1e293b; border-bottom:1px solid #cbd5e1; font-weight: 700; }
#tblUpdate th, #tblUpdate td{ border-bottom:1px solid #f1f5f9; white-space: nowrap; }

/* Desktop Column Freeze */
#tblUpdate .col-name{ width:var(--colName); min-width:var(--colName); max-width:var(--colName); }
#tblUpdate .freeze-1{ position:sticky; left:0; z-index:30; background:#fff; border-right:1px solid #e2e8f0; }
#tblUpdate thead th.freeze-1{ z-index:80; background:#d9ead3; }

/* Sticky Total */
#tblUpdate tbody tr.sticky-total td{
  position:sticky; top:var(--up_head, 40px); z-index:20;
  background:#eff6ff; color:#1e40af; border-bottom:2px solid #bfdbfe;
}
#tblUpdate tbody tr.sticky-total td.freeze-1{ z-index:35; background:#eff6ff; }

/* Row Colors */
.row-merah td { background-color: #fef2f2 !important; }
.row-merah td.freeze-1 { background-color: #fef2f2 !important; }
.cell-merah { background-color: #fee2e2 !important; color: #991b1b !important; font-weight: bold; }

/* RESPONSIVE SPACE OPTIMIZER */
html, body{ min-height:100%; }
.update-page{ height:calc(100vh - 76px); min-height:420px; display:flex; flex-direction:column; overflow:hidden; }
.update-header{ flex-shrink:0; }
#upWrap{ flex:1 1 auto; min-height:260px; -webkit-overflow-scrolling:touch; }
#upWrap::-webkit-scrollbar{ width:10px; height:10px; }
#upWrap::-webkit-scrollbar-thumb{ background:#cbd5e1; border-radius:999px; }
#upWrap::-webkit-scrollbar-track{ background:#f8fafc; }

@media (max-width:640px){ 
  .update-page{ height:auto; min-height:calc(100vh - 64px); overflow:visible; padding-bottom:12px; }
  #upWrap { --colName: 132px; height:calc(100vh - 165px) !important; min-height:380px; }
  #tblUpdate { min-width:980px; }
  #tblUpdate .col-name { width: 132px !important; min-width: 132px !important; max-width: 132px !important; }
  #tblUpdate th, #tblUpdate td { padding: 8px 6px; font-size: 10px; }
  #tblUpdate .col-name .truncate { width: 116px; display: block; overflow: hidden; text-overflow: ellipsis; font-size: 10px; }
}

body{ overflow:auto; }
</style>

<script>
const nfID = new Intl.NumberFormat('id-ID');
const fmt = (n)=> nfID.format(Number(n||0));

function id(x){ return document.getElementById(x); }
function safe(v){ return (v==null||v==='')?'-':String(v); }
function escapeAttr(s){ return String(s ?? '').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function setUpSticky(){
  const h = id('upHead1')?.offsetHeight || 40;
  id('upWrap')?.style.setProperty('--up_head', h + 'px');
}

function sizeUpWrap(){
  const wrap = id('upWrap');
  if(!wrap) return;
  const rectTop = wrap.getBoundingClientRect().top;
  const h = Math.max(260, window.innerHeight - rectTop - 15);
  wrap.style.height = h + 'px';
}

document.addEventListener("DOMContentLoaded", async () => {
  const storedData = sessionStorage.getItem("flowpar_update");
  if (!storedData) return;
  const req = JSON.parse(storedData);
  const isPotensi = req.source === 'potensi_npl';
  id("judulHalaman").textContent = isPotensi ? "Update Komitmen Potensi NPL" : "Update Komitmen Flow PAR";
  id("judul_kantor").textContent = [
    isPotensi ? 'Sumber: Potensi NPL' : 'Sumber: Flow PAR',
    `Kode Kantor: ${req.kode_kantor || '-'}`,
    req.kode_kankas ? `Kankas: ${req.kode_kankas}` : '',
    req.kode_ao ? `AO: ${req.kode_ao}` : '',
    req.status_filter && req.status_filter !== 'ALL' ? `Status: ${req.status_filter}` : ''
  ].filter(Boolean).join(' | ');

  try{
    renderRows(await loadUpdateRows(req), req.harian_date);
    setUpSticky(); sizeUpWrap();
  }catch(e){ console.error(e); }
});

window.addEventListener('resize', () => { setUpSticky(); sizeUpWrap(); });

async function loadUpdateRows(req){
  const isPotensi = req.source === 'potensi_npl';
  const endpoint = isPotensi ? './api/npl/' : './api/flow_par/';
  const payload = isPotensi
    ? {
        type: 'Debitur Potensi NPL',
        kode_kantor: req.kode_kantor || '',
        kode_kankas: req.kode_kankas || '',
        kode_ao: req.kode_ao || '',
        closing_date: req.closing_date,
        harian_date: req.harian_date
      }
    : {
        type: 'KL Baru',
        kode_kantor: req.kode_kantor,
        kode_kankas: req.kode_kankas || '',
        korwil: req.korwil || '',
        klasifikasi_flow: req.klasifikasi_flow || '',
        closing_date: req.closing_date,
        harian_date: req.harian_date
      };

  const r = await fetch(endpoint, {
    method:'POST',
    headers:{ 'Content-Type':'application/json' },
    body: JSON.stringify(payload)
  });
  const j = await r.json();
  let rows = Array.isArray(j.data) ? j.data : [];

  if(isPotensi){
    const status = req.status_filter || 'ALL';
    if(status !== 'ALL'){
      rows = rows.filter(d => status === 'AMAN'
        ? (d.status_potensi === 'AMAN' || d.status_potensi === 'LUNAS / AMAN')
        : d.status_potensi === status
      );
    }
    rows = rows.map(d => ({
      no_rekening: d.no_rekening,
      nama_nasabah: d.nama_nasabah,
      kolek_harian: d.kolek_harian || d.status_potensi || '-',
      baki_debet: Number(d.baki_debet_harian || d.baki_debet_closing || 0),
      tunggakan_pokok: Number(d.tunggakan_pokok || 0),
      tunggakan_bunga: Number(d.tunggakan_bunga || 0),
      hari_menunggak_pokok: Number(d.hmp_harian || 0),
      hari_menunggak_bunga: Number(d.hmb_harian || 0),
      tgl_jatuh_tempo: d.jt_harian || d.jt_closing || '',
      komitmen: d.komitmen || '',
      tgl_pembayaran: d.tgl_pembayaran || '',
      nominal: Number(d.nominal || 0),
      alasan: d.alasan || ''
    }));
  }

  return rows;
}

function renderRows(list, harian_date){
  const body = id("flowparBody");
  const tot  = id("upTotalRow");
  body.innerHTML = ""; tot.innerHTML = "";

  if(list.length === 0){
      body.innerHTML = `<tr><td colspan="12" class="text-center py-12 text-slate-400">Data tidak ditemukan.</td></tr>`;
      return;
  }

  const sum = k => list.reduce((s,d)=> s + Number(d[k]||0), 0);
  
  // TOTAL NOMINAL DITAMBAHKAN DISINI
  tot.innerHTML = `
    <tr class="sticky-total font-bold">
      <td class="px-2 py-2 freeze-1 col-name">TOTAL (${list.length})</td>
      <td class="px-2 py-2 text-center">-</td>
      <td class="px-2 py-2 text-right">${fmt(sum('baki_debet'))}</td>
      <td class="px-2 py-2 text-right">${fmt(sum('tunggakan_pokok'))}</td>
      <td class="px-2 py-2 text-right">${fmt(sum('tunggakan_bunga'))}</td>
      <td colspan="3"></td>
      <td class="px-2 py-2"></td>
      <td class="px-2 py-2 text-right text-blue-900">${fmt(sum('nominal'))}</td>
      <td colspan="2"></td>
    </tr>`;

  const now = new Date(harian_date || new Date());
  const curM = now.getMonth(), curY = now.getFullYear();
  let prevM = curM - 1, prevY = curY;
  if(prevM < 0){ prevM = 11; prevY--; }

  for(const d of list){
    const dpdTP = Number(d.hari_menunggak_pokok || 0);
    const dpdTB = Number(d.hari_menunggak_bunga || 0);
    const tglJT = d.tgl_jatuh_tempo || '-';
    
    let rowClass = "";
    if (d.tgl_jatuh_tempo) {
        const jt = new Date(d.tgl_jatuh_tempo);
        if ((jt.getMonth() === curM && jt.getFullYear() === curY) || 
            (jt.getMonth() === prevM && jt.getFullYear() === prevY)) {
            rowClass = "row-merah";
        }
    }

    body.insertAdjacentHTML('beforeend', `
      <tr class="${rowClass}">
        <td class="px-2 py-3 freeze-1 col-name font-medium text-slate-700"><div class="truncate" title="${safe(d.nama_nasabah)}">${safe(d.nama_nasabah)}</div></td>
        <td class="px-2 py-3 text-center font-bold text-slate-500">${d.kolek_harian || '-'}</td>
        <td class="px-2 py-3 text-right font-mono">${fmt(d.baki_debet)}</td>
        <td class="px-2 py-3 text-right">${fmt(d.tunggakan_pokok)}</td>
        <td class="px-2 py-3 text-right">${fmt(d.tunggakan_bunga)}</td>
        <td class="px-2 py-3 text-center ${dpdTP >= 90 ? 'cell-merah' : ''}">${dpdTP}</td>
        <td class="px-2 py-3 text-center ${dpdTB >= 90 ? 'cell-merah' : ''}">${dpdTB}</td>
        <td class="px-2 py-3 text-center">${tglJT}</td>
        <td class="px-2 py-3 text-slate-600 text-[9px]">${d.komitmen ?? "-"}</td>
        <td class="px-2 py-3 text-right font-bold text-blue-700">${fmt(d.nominal)}</td>
        <td class="px-2 py-3 text-slate-600 truncate max-w-[100px]" title="${escapeAttr(d.alasan)}">${d.alasan ?? "-"}</td>
        <td class="px-2 py-3 text-center">
          <button class="open-modal-btn bg-blue-600 text-white px-2 py-1 rounded text-[9px] font-bold"
            data-rekening="${d.no_rekening}" data-nama="${escapeAttr(d.nama_nasabah)}"
            data-baki="${d.baki_debet}" data-tp="${d.tunggakan_pokok || 0}" 
            data-tb="${d.tunggakan_bunga || 0}" data-komitmen="${escapeAttr(d.komitmen ?? '')}"
            data-tgl_pembayaran="${escapeAttr(d.tgl_pembayaran ?? '')}"
            data-tgl_jt="${escapeAttr(tglJT)}" data-nominal="${escapeAttr(d.nominal ?? '')}" 
            data-alasan="${escapeAttr(d.alasan ?? '')}">UPD</button>
        </td>
      </tr>`);
  }
}

/* Modal Logic */
let curTP = 0, curTB = 0;
document.body.addEventListener("click", (e) => {
  const btn = e.target.closest(".open-modal-btn");
  if (!btn) return;
  curTP = Number(btn.dataset.tp); curTB = Number(btn.dataset.tb);
  id('modal_rekening').value = btn.dataset.rekening;
  id('modal_rekening_text').innerText = btn.dataset.rekening;
  id('modal_nama').innerText = btn.dataset.nama;
  id('modal_baki').innerText = "Rp " + fmt(btn.dataset.baki);
  id('modal_tanggal').value  = btn.dataset.tgl_pembayaran;
  id('modal_nominal').value  = btn.dataset.nominal;
  id('modal_alasan').value   = btn.dataset.alasan;

  const select = id('modal_komitmen');
  const jtDate = new Date(btn.dataset.tgl_jt);
  const today = new Date();
  const isJTThisMonth = (jtDate.getMonth() === today.getMonth() && jtDate.getFullYear() === today.getFullYear());

  let opts = '<option value="">-- Pilih Komitmen --</option>';
  if(isJTThisMonth){
      opts += `<option value="E_DPD 91-120">Flow (E_DPD 91-120)</option><option value="O_Lunas">O_Lunas</option>`;
  } else {
      opts += `<option value="A_DPD 0">A_DPD 0</option><option value="B_DPD 1-30">B_DPD 1-30</option>
               <option value="C_DPD 31-60">C_DPD 31-60</option><option value="D_DPD 61-90">D_DPD 61-90</option>
               <option value="E_DPD 91-120">E_DPD 91 - 120</option><option value="O_Lunas">O_Lunas</option>`;
  }
  select.innerHTML = opts;
  select.value = btn.dataset.komitmen;
  id('komitmenModal').classList.replace('hidden', 'flex');
});

id('modal_komitmen').addEventListener('change', (e) => {
  if(e.target.value === 'O_Lunas') id('modal_nominal').value = curTP + curTB;
});

function closeModal(){ id('komitmenModal').classList.replace('flex', 'hidden'); }

id("btnSaveKomitmen").addEventListener("click", async () => {
  const payload = {
    type: "Update KL Baru",
    rekening: id('modal_rekening').value,
    komitmen: id('modal_komitmen').value,
    tgl_pembayaran: id('modal_tanggal').value,
    nominal: id('modal_nominal').value,
    alasan: id('modal_alasan').value
  };
  if (!payload.komitmen || !payload.tgl_pembayaran) return alert("Lengkapi data!");
  try {
    const res = await fetch('./api/flow_par/', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
    const result = await res.json();
    if (result.status == 200) location.reload();
  } catch(e) { alert("Gagal!"); }
});
</script>

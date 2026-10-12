<div class="npl25-page max-w-7xl mx-auto px-3 sm:px-4 py-3 sm:py-4 min-h-[calc(100vh-64px)] flex flex-col font-sans bg-slate-50">
  
  <section class="npl25-page__header flex items-center gap-3 mb-3 shrink-0">
    <div class="npl25-page__icon" aria-hidden="true">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 19V5"></path><path d="M4 19h16"></path><path d="m7 15 3-4 3 2 5-7"></path>
      </svg>
    </div>
    <div class="min-w-0">
      <h1 class="text-lg md:text-2xl font-extrabold tracking-tight text-slate-900 truncate">
          25 Debitur Terbesar NPL
        </h1>
      <p class="text-[10px] md:text-xs text-slate-500 mt-0.5 font-medium">Berdasarkan posisi nominatif closing bulan lalu.</p>
    </div>
  </section>

  <div id="loadingTop" class="hidden flex items-center gap-2 text-sm text-blue-600 font-bold mb-2 ml-1">
    <div class="animate-spin h-4 w-4 border-2 border-blue-200 border-t-blue-600 rounded-full"></div>
    <span>Memuat data...</span>
  </div>

  <div id="nplScroller" class="flex-1 min-h-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm relative">
    <div class="h-full overflow-auto" id="nplScrollInner">
      <table id="tabelTopNpl" class="min-w-full text-[12px] text-left text-gray-700 border-separate border-spacing-0">
        <thead class="uppercase">
          <tr id="nplHead">
            <th class="px-3 py-2 sticky top-0 z-40 col-namakantor border-b border-r">NAMA KANTOR</th>
            <th class="px-3 py-2 sticky top-0 z-50 freeze-1 col-norek border-b border-r">NO REKENING</th>
            <th class="px-3 py-2 sticky top-0 z-40 freeze-2 col-debitur border-b border-r">NAMA DEBITUR</th>
            <th class="px-3 py-2 text-right sticky top-0 z-30 col-amt border-b border-r">PLAFOND</th>
            <th class="px-3 py-2 text-right sticky top-0 z-30 col-amt border-b border-r">BAKI DEBET</th>
            <th class="px-3 py-2 text-right sticky top-0 z-30 col-pct border-b border-r" title="Kontribusi terhadap Total NPL (%)">%</th>
            <th class="px-3 py-2 text-right sticky top-0 z-30 col-amt border-b border-r">T.POKOK</th>
            <th class="px-3 py-2 text-right sticky top-0 z-30 col-amt border-b border-r">T.BUNGA</th>
            <th class="px-3 py-2 text-center sticky top-0 z-30 col-kol border-b border-r">KOLEK</th>
            <th class="px-3 py-2 text-center sticky top-0 z-30 col-kol border-b border-r">UPDATE</th>
            <th class="px-3 py-2 text-right sticky top-0 z-30 col-amt border-b border-r">ANGS POKOK</th>
            <th class="px-3 py-2 text-right sticky top-0 z-30 col-amt border-b border-r">ANGS BUNGA</th>
            <th class="px-3 py-2 text-center sticky top-0 z-30 col-date border-b">TGL TRANS</th>
          </tr>
        </thead>
        <tbody id="tbTotalNpl"></tbody>
        <tbody id="bodyTopNpl"></tbody>
      </table>
      <div class="bottom-spacer" style="height: 60px;"></div>
    </div>
  </div>
</div>

<style>
  .npl25-page { font-family:'Roboto', Arial, sans-serif; color:#334155; }
  .npl25-page__header {
    min-height:58px;
    padding:12px 14px;
    border:1px solid #dbe3ee;
    border-radius:14px;
    background:#fff;
    box-shadow:0 6px 18px rgba(15,23,42,.05);
  }
  .npl25-page__icon {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:32px;
    height:32px;
    flex:0 0 32px;
    border-radius:10px;
    background:#2563eb;
    color:#fff;
    font-size:18px;
    font-weight:900;
    box-shadow:0 5px 12px rgba(37,99,235,.18);
  }
  #nplScroller { scrollbar-width:thin; scrollbar-color:#cbd5e1 transparent; }
  #nplScrollInner::-webkit-scrollbar { width:6px; height:6px; }
  #nplScrollInner::-webkit-scrollbar-track { background:transparent; }
  #nplScrollInner::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
  #nplScrollInner::-webkit-scrollbar-thumb:hover { background:#94a3b8; }
  .inp { border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0 0.75rem; font-size: 13px; background: #fff; width: 100%; outline: none; transition: border 0.2s; }
  .inp:focus { border-color: #2563eb; ring: 2px solid #bfdbfe; }
  
  #tabelTopNpl { font-family:'Roboto', Arial, sans-serif; }
  #tabelTopNpl thead th { background: #eff6ff !important; color: #1e3a5f; font-weight: 800; border-color: #cbd5e1; }
  
  .freeze-1 { position: sticky; left: 0; background: #fff; border-right: 1px solid #e2e8f0; }
  .freeze-2 { position: sticky; left: 7.5rem; background: #fff; border-right: 1px solid #e2e8f0; box-shadow: 2px 0 5px rgba(0,0,0,0.03); }
  
  .col-namakantor { width: 10rem; }
  .col-norek { width: 7.5rem; }
  .col-debitur { width: 13rem; overflow: hidden; text-overflow: ellipsis; }
  .col-amt { width: 8.5rem; text-align: right; }
  .col-pct { width: 4.5rem; text-align: right; }
  .col-kol { width: 5rem; text-align: center; }
  .col-date { width: 7rem; text-align: center; }

  #tbTotalNpl tr td { 
    position: sticky; top: var(--headH, 36px); z-index: 25; 
    background: #f0f7ff; color: #1e40af; font-weight: 700; border-bottom: 2px solid #bfdbfe; 
  }
  #tbTotalNpl tr td.freeze-1 { z-index: 26; background: #f0f7ff; }
  #tbTotalNpl tr td.freeze-2 { z-index: 26; background: #f0f7ff; }

  @media (max-width: 640px) {
    .freeze-1 { display: none !important; }
    .freeze-2 { left: 0 !important; width: 8.5rem; min-width: 8.5rem; white-space: normal; line-height: 1.2; }
    .col-amt { width: 7.5rem; }
    .col-pct { width: 4rem; }
  }
</style>

<script>
  let StateDate = { closing: '', harian: '' };
  const nfID = new Intl.NumberFormat('id-ID');
  const fmt = n => nfID.format(Number(n||0));
  const selCabang = document.getElementById('selCabangNpl');
  const npl25FilterPanel = document.getElementById('npl25NavbarFilterPanel');
  const npl25FilterToggle = document.getElementById('npl25NavbarFilterToggle');

  function toggleNpl25Filter(force) {
    if (!npl25FilterPanel) return;
    const open = typeof force === 'boolean' ? force : npl25FilterPanel.classList.contains('hidden');
    npl25FilterPanel.classList.toggle('hidden', !open);
    npl25FilterPanel.classList.toggle('flex', open);
    npl25FilterToggle?.classList.toggle('is-active', open);
    npl25FilterToggle?.setAttribute('aria-expanded', String(open));
  }

  npl25FilterToggle?.addEventListener('click', () => toggleNpl25Filter());
  document.getElementById('npl25NavbarFilterClose')?.addEventListener('click', () => toggleNpl25Filter(false));
  document.addEventListener('click', event => {
    if (!npl25FilterPanel?.classList.contains('flex')) return;
    if (!npl25FilterPanel.contains(event.target) && !npl25FilterToggle?.contains(event.target)) toggleNpl25Filter(false);
  });

  selCabang.addEventListener('change', () => { document.getElementById('formFilterTopNpl').requestSubmit(); });

  window.addEventListener('DOMContentLoaded', async () => {
    try {
        const d = await (await fetch('./api/date/')).json();
        if (d.data) { StateDate.closing = d.data.last_closing; StateDate.harian = d.data.last_created; }
    } catch(e) {}

    const user = (window.getUser && window.getUser()) || null;
    const uKode = user?.kode_kantor ? String(user.kode_kantor).padStart(3,'0') : (user?.kode ? String(user.kode).padStart(3,'0') : '000');

    await populateKantorOptions(uKode);
    fetchTop25Npl(uKode === '000' ? '' : uKode);
    setHeadHeight();
  });

  async function populateKantorOptions(userKode) {
    if (userKode !== '000') {
      selCabang.innerHTML = `<option value="${userKode}">CABANG ${userKode}</option>`;
      selCabang.disabled = true;
      return;
    }
    try {
        const res = await fetch('./api/kode/', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
        const json = await res.json();
        let html = `<option value="">konsolidasi</option>`;
        (json.data || []).filter(x => x.kode_kantor !== '000').sort((a,b) => String(a.kode_kantor).localeCompare(b.kode_kantor)).forEach(it => {
            html += `<option value="${it.kode_kantor}">${it.kode_kantor} - ${it.nama_kantor}</option>`;
        });
        selCabang.innerHTML = html;
    } catch(e) { selCabang.innerHTML = `<option value="">konsolidasi</option>`; }
  }

  document.getElementById("formFilterTopNpl").addEventListener("submit", (e) => {
    e.preventDefault();
    if(window.innerWidth < 768) toggleNpl25Filter(false);
    fetchTop25Npl(selCabang.value);
  });

  async function fetchTop25Npl(kode) {
    const loading = document.getElementById('loadingTop');
    loading.classList.remove('hidden');
    try {
      const res = await fetch("./api/npl/", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ type: "25 NPL Terbesar", closing_date: StateDate.closing, harian_date: StateDate.harian, kode_cabang: kode })
      });
      const j = await res.json();
      renderTable(j.data || []);
    } catch { renderTable([]); }
    finally { loading.classList.add('hidden'); setTimeout(setHeadHeight, 50); }
  }

  function renderTable(rows) {
    const tb = document.getElementById('bodyTopNpl');
    const ttot = document.getElementById('tbTotalNpl');
    tb.innerHTML = ''; ttot.innerHTML = '';

    const sum = k => rows.reduce((s, r) => s + Number(r[k] || 0), 0);
    const totalPlaf = sum('jml_pinjaman');
    const totalBaki = sum('baki_debet');
    const totalPersen = sum('persen_npl'); // Ambil dari BE baru
    
    ttot.innerHTML = `
      <tr>
        <td class="px-3 py-2 border-r"></td>
        <td class="px-3 py-2 freeze-1 border-r"></td>
        <td class="px-3 py-2 freeze-2 font-bold border-r">TOTAL</td>
        <td class="px-3 py-2 text-right border-r">${fmt(totalPlaf)}</td>
        <td class="px-3 py-2 text-right border-r">${fmt(totalBaki)}</td>
        <td class="px-3 py-2 text-right font-bold text-blue-900 border-r">${totalPersen.toFixed(1)}%</td>
        <td class="px-3 py-2 text-right border-r">${fmt(sum('tunggakan_pokok'))}</td>
        <td class="px-3 py-2 text-right border-r">${fmt(sum('tunggakan_bunga'))}</td>
        <td colspan="2" class="border-r"></td>
        <td class="px-3 py-2 text-right border-r">${fmt(sum('total_pokok'))}</td>
        <td class="px-3 py-2 text-right border-r">${fmt(sum('total_bunga'))}</td>
        <td class="px-3 py-2"></td>
      </tr>`;

    if(rows.length === 0) {
        tb.innerHTML = `<tr><td colspan="13" class="py-10 text-center text-slate-400 font-medium">Data tidak ditemukan.</td></tr>`;
        return;
    }

    rows.forEach(r => {
      const pNpl = Number(r.persen_npl || 0);
      const pctColor = pNpl >= 5 ? 'text-blue-700 font-bold' : 'text-slate-500';

      tb.insertAdjacentHTML('beforeend', `
        <tr class="hover:bg-slate-50 border-b transition">
          <td class="px-3 py-2 truncate border-r border-slate-100" title="${r.nama_kantor}">${r.nama_kantor}</td>
          <td class="px-3 py-2 col-norek freeze-1 font-mono text-slate-500 border-r border-slate-100">${r.no_rekening}</td>
          <td class="px-3 py-2 col-debitur freeze-2 font-semibold text-slate-700 border-r border-slate-100">${r.nama_nasabah}</td>
          <td class="px-3 py-2 text-right border-r border-slate-100">${fmt(r.jml_pinjaman)}</td>
          <td class="px-3 py-2 text-right font-bold text-blue-700 border-r border-slate-100">${fmt(r.baki_debet)}</td>
          <td class="px-3 py-2 text-right ${pctColor} border-r border-slate-100">${pNpl.toFixed(1)}%</td>
          <td class="px-3 py-2 text-right border-r border-slate-100">${fmt(r.tunggakan_pokok)}</td>
          <td class="px-3 py-2 text-right border-r border-slate-100">${fmt(r.tunggakan_bunga)}</td>
          <td class="px-3 py-2 text-center font-bold text-slate-400 border-r border-slate-100">${r.kolek_closing||''}</td>
          <td class="px-3 py-2 text-center font-bold text-red-600 border-r border-slate-100">${r.kolek_harian||''}</td>
          <td class="px-3 py-2 text-right border-r border-slate-100">${fmt(r.total_pokok)}</td>
          <td class="px-3 py-2 text-right border-r border-slate-100">${fmt(r.total_bunga)}</td>
          <td class="px-3 py-2 text-center text-slate-500">${r.tgl_trans || "-"}</td>
        </tr>`);
    });
  }

  function setHeadHeight() {
    const h = document.getElementById('nplHead')?.offsetHeight || 36;
    document.getElementById('nplScroller')?.style.setProperty('--headH', h + 'px');
  }
  window.addEventListener('resize', setHeadHeight);
</script>

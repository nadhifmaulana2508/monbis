<script>
  function exportExcelRekapRR() {
      const rows = Array.isArray(rekapDataRaw) ? rekapDataRaw : [];
      const gt = rekapGtRaw;
      if (!rows.length && !gt) { alert('Tidak ada data untuk diexport.'); return; }
      let csv = 'TGL\tTARGET M-1\tNOA TARGET\tOTP LANCAR\tNOA LANCAR\tDITAGIH\tNOA DITAGIH\tLUNAS\tNOA LUNAS\tANGSURAN\tTOTAL BAYAR\tPERSEN\n';
      if (gt) {
          csv += `TOTAL\t${Math.round(gt.target_os||0)}\t${gt.target_noa||0}\t${Math.round(gt.lancar_os||0)}\t${gt.lancar_noa||0}\t${Math.round(gt.macet_os||0)}\t${gt.macet_noa||0}\t${Math.round(gt.lunas_os||0)}\t${gt.lunas_noa||0}\t${Math.round(gt.angsuran||0)}\t${Math.round(gt.total_bayar||0)}\t${gt.persen||0}%\n`;
      }
      rows.forEach(r => {
          const p = (r.persen !== null && r.persen !== undefined) ? `${r.persen}%` : '-';
          csv += `${r.tgl}\t${Math.round(r.target_os||0)}\t${r.target_noa||0}\t${Math.round(r.lancar_os||0)}\t${r.lancar_noa||0}\t${Math.round(r.macet_os||0)}\t${r.macet_noa||0}\t${Math.round(r.lunas_os||0)}\t${r.lunas_noa||0}\t${Math.round(r.angsuran||0)}\t${Math.round(r.total_bayar||0)}\t${p}\n`;
      });
      const blob = new Blob([csv], { type: 'application/vnd.ms-excel;charset=utf-8' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `OTP_Rekap_${document.getElementById('harian_date')?.value || 'harian'}.xls`;
      document.body.appendChild(a); a.click(); document.body.removeChild(a);
      window.URL.revokeObjectURL(url);
  }

  const API_RR_URL = './api/rr'; 
  const API_KODE_URL = './api/kode/'; 
  const nfID = new Intl.NumberFormat('id-ID');
  const fmt  = n => nfID.format(Number(n||0));
  const fmtDateID = s => {
      if(!s) return '-';
      const d = new Date(s);
      if (isNaN(d)) return String(s).slice(0, 10);
      return `${String(d.getDate()).padStart(2,'0')}-${String(d.getMonth()+1).padStart(2,'0')}-${d.getFullYear()}`;
  };
  
  let rekapDataRaw = [];
  let rekapGtRaw = null;
  let detailDataCache = [];

  const apiCall = async (url, opt = {}) => {
      const res = await fetch(url, opt);
      try {
          const json = await res.json();
          return { ok: res.ok, status: res.status, json: json };
      } catch (e) {
          throw new Error("Gagal parsing JSON.");
      }
  };

  let abortRR;
  let currentDetailParams = {};
  let currentDetailPage = 1;
  let currentDetailTotalPages = 1;
  let currentMode = 'NORMAL'; 
  const detailLimit = 20;

  let sortMainCol = '', sortMainAsc = true;
  let sortDetailCol = '', sortDetailAsc = true;

  const getSortIcon = (col, currentCol, asc) => {
      if (col !== currentCol) return '<span class="opacity-30 text-[10px] ml-1">&#8597;</span>';
      return asc ? '<span class="text-blue-600 ml-1 text-[11px]">&#9650;</span>' : '<span class="text-blue-600 ml-1 text-[11px]">&#9660;</span>';
  };

  const getBucketLabel = bucket => {
      if (bucket === 'dpd0') return 'DPD 0';
      if (bucket === 'dpd1-30') return 'DPD 1-30';
      return 'ALL';
  };

  let mainFilterOpen = window.innerWidth >= 1280;

  function toggleMainFilter() {
      mainFilterOpen = !mainFilterOpen;
      applyFilterState();
  }

  function applyFilterState() {
      const el = document.getElementById('filterWrapperMain');
      if(mainFilterOpen) {
          el.classList.remove('filter-collapsed');
          el.classList.add('filter-expanded');
      } else {
          el.classList.remove('filter-expanded');
          el.classList.add('filter-collapsed');
      }
  }

  function toggleOtpHelp(event) {
      if (event) event.stopPropagation();
      const panel = document.getElementById('otpHelpPanel');
      if (!panel) return;
      panel.classList.toggle('hidden');
      panel.classList.toggle('flex');
  }

  document.addEventListener('click', (event) => {
      const root = document.querySelector('.otp-info-root');
      const panel = document.getElementById('otpHelpPanel');
      if (root && panel && !root.contains(event.target)) {
          panel.classList.add('hidden');
          panel.classList.remove('flex');
      }
  });

  window.addEventListener('DOMContentLoaded', async () => {
    mainFilterOpen = window.innerWidth >= 1280;
    applyFilterState();

    const user = (window.getUser && window.getUser()) || null;
    let uKode = (user && user.kode) ? String(user.kode).padStart(3, '0') : '000';
    if(uKode === '099') uKode = '000';
    
    await populateKantor(uKode);

    const d = await getLastHarianData(); 
    if(d) {
        document.getElementById('closing_date').value = d.last_closing;
        document.getElementById('harian_date').value  = d.last_created;
    } else {
        const now = new Date();
        now.setDate(now.getDate() - 1);
        const strH1 = now.toISOString().split('T')[0];
        document.getElementById('closing_date').value = strH1;
        document.getElementById('harian_date').value  = strH1;
    }
    fetchRekapRR();
  });

  window.addEventListener('resize', () => {
      if(window.innerWidth >= 1280 && !mainFilterOpen) {
          mainFilterOpen = true;
          applyFilterState();
      }
  });

  async function getLastHarianData(){ 
      try{ const r = await fetch('./api/date/'); const j = await r.json(); return j.data||null; }catch{ return null; } 
  }
  
  async function populateKantor(uKode) {
    const el = document.getElementById('opt_kantor'); if(!el) return;
    if (uKode !== '000') { 
        try {
            const res = await fetch(API_KODE_URL, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
            const json = await res.json();
            const myKantor = (json.data||[]).find(x => String(x.kode_kantor).padStart(3,'0') === uKode);
            const nama = myKantor ? myKantor.nama_kantor : `CABANG ${uKode}`;
            el.innerHTML = `<option value="${uKode}">${uKode} - ${nama}</option>`;
        } catch(e) {
            el.innerHTML = `<option value="${uKode}">CABANG ${uKode}</option>`; 
        }
        el.value = uKode; el.disabled = true;
        await handleCabangChangeOtp(true);
        return; 
    }
    try {
        const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ type: 'kode_kantor' }) });
        const j = await r.json();
        let h = '<option value="">ALL CABANG</option>';
        if(j.data) j.data.filter(x => x.kode_kantor !== '000').forEach(x => { h += `<option value="${x.kode_kantor}">${x.kode_kantor} - ${x.nama_kantor}</option>`; });
        el.innerHTML = h;
    } catch { el.innerHTML = '<option value="">ALL CABANG</option>'; }
    await handleCabangChangeOtp(true);
  }

  async function handleCabangChangeOtp(isInit = false) {
      const cabangVal = document.getElementById('opt_kantor').value;
      const lblSub = document.getElementById('lbl_sub_otp');
      const optSub = document.getElementById('opt_sub_otp');
      const optAo = document.getElementById('opt_ao_otp');

      if (cabangVal === "" || cabangVal === "000") {
          lblSub.innerText = "KORWIL";
          optSub.innerHTML = `
              <option value="">ALL KORWIL</option>
              <option value="SEMARANG">SEMARANG</option>
              <option value="SOLO">SOLO</option>
              <option value="BANYUMAS">BANYUMAS</option>
              <option value="PEKALONGAN">PEKALONGAN</option>
          `;
          optAo.innerHTML = '<option value="">PILIH CABANG DULU</option>';
          optAo.disabled = true;
      } else {
          lblSub.innerText = "KANKAS";
          optSub.innerHTML = '<option value="">ALL KANKAS</option>';
          optAo.innerHTML = '<option value="">ALL AO</option>';
          optAo.disabled = false;
          await loadKankasSubOtp(cabangVal);
          await loadAoMainOtp(cabangVal);
      }
      if (!isInit) fetchRekapRR();
  }

  async function loadKankasSubOtp(kodeCabang) {
      const optSub = document.getElementById('opt_sub_otp');
      try {
          const payload = { type: 'kode_kankas', kode_kantor: kodeCabang };
          const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          let h = '<option value="">ALL KANKAS</option>';
          if(j.data && Array.isArray(j.data)) j.data.forEach(x => { h += `<option value="${x.kode_group1}">${x.deskripsi_group1 || x.kode_group1}</option>`; });
          optSub.innerHTML = h;
      } catch(err) {}
  }

  async function loadAoMainOtp(kodeCabang) {
      const optAo = document.getElementById('opt_ao_otp');
      if (!optAo) return;
      try {
          const payload = { type: 'kode_ao_kredit', kode_kantor: kodeCabang };
          const r = await fetch(API_KODE_URL, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload) });
          const j = await r.json();
          let h = '<option value="">ALL AO</option>';
          if(j.data && Array.isArray(j.data)) {
              j.data.forEach(x => { h += `<option value="${x.kode_group2}">${x.nama_ao || x.kode_group2}</option>`; });
          }
          optAo.innerHTML = h;
      } catch(err) {
          optAo.innerHTML = '<option value="">ALL AO</option>';
      }
  }

  /* ============================
     RENDER MAIN TABLE 
     ============================ */
  function renderMainHeaderRR() {
      const head = document.getElementById('headRR');
      head.innerHTML = `
          <tr class="rr-row-1">
            <th rowspan="2" class="head-row px-2 border-b border-r border-slate-200 bg-slate-100 sticky left-0 z-30 shadow-[1px_0_0_#e2e8f0]" onclick="sortMainRR('tgl', 'string')">
                <div class="flex items-center justify-center">TGL ${getSortIcon('tgl', sortMainCol, sortMainAsc)}</div>
            </th>
            <th colspan="3" class="head-row px-3 border-b border-r border-slate-200 bg-slate-100 text-blue-900 cursor-default tracking-widest text-center border-t-2 border-t-blue-500">TOTAL OUTSTANDING</th>
            <th colspan="3" class="head-row px-3 border-b border-slate-200 bg-slate-100 text-purple-900 cursor-default tracking-widest text-center border-t-2 border-t-purple-500">RECOVERY / PEMBAYARAN</th>
            <th rowspan="2" class="head-row px-2 border-b border-l border-slate-200 bg-slate-100 z-30 text-center" onclick="sortMainRR('persen', 'number')">
                <div class="flex items-center justify-center">% ${getSortIcon('persen', sortMainCol, sortMainAsc)}</div>
            </th>
          </tr>
          <tr class="rr-row-2 text-[10px] md:text-[11px] tracking-wider text-slate-700 bg-slate-50">
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[140px] md:w-[180px]" onclick="sortMainRR('target_os', 'number')">
                <div class="flex items-center justify-center">TARGET (M-1) ${getSortIcon('target_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[140px] md:w-[180px]" onclick="sortMainRR('lancar_os', 'number')">
                <div class="flex items-center justify-center">OTP (LANCAR) ${getSortIcon('lancar_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[140px] md:w-[180px]" onclick="sortMainRR('macet_os', 'number')">
                <div class="flex items-center justify-center">DITAGIH ${getSortIcon('macet_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[120px] md:w-[160px]" onclick="sortMainRR('lunas_os', 'number')">
                <div class="flex items-center justify-center">LUNAS ${getSortIcon('lunas_os', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-r border-slate-200 w-[120px] md:w-[160px]" onclick="sortMainRR('angsuran', 'number')">
                <div class="flex items-center justify-center">ANGSURAN ${getSortIcon('angsuran', sortMainCol, sortMainAsc)}</div>
            </th>
            <th class="head-row px-2 md:px-3 border-b border-slate-200 w-[120px] md:w-[160px]" onclick="sortMainRR('total_bayar', 'number')">
                <div class="flex items-center justify-center">TOTAL BAYAR ${getSortIcon('total_bayar', sortMainCol, sortMainAsc)}</div>
            </th>
          </tr>
          <tr class="rr-row-tot bg-slate-50 sticky-total border-b border-slate-300" id="rowTotalRRAtas"></tr>
      `;
  }

  window.sortMainRR = function(col, type) {
      if (!rekapDataRaw || rekapDataRaw.length === 0) return;
      if (sortMainCol === col) sortMainAsc = !sortMainAsc;
      else { sortMainCol = col; sortMainAsc = true; }

      rekapDataRaw.sort((a, b) => {
          let valA = a[col], valB = b[col];
          if (type === 'number') {
              return sortMainAsc ? (parseFloat(valA)||0) - (parseFloat(valB)||0) : (parseFloat(valB)||0) - (parseFloat(valA)||0);
          } else {
              valA = String(valA||'').toLowerCase(); valB = String(valB||'').toLowerCase();
              if (valA < valB) return sortMainAsc ? -1 : 1;
              if (valA > valB) return sortMainAsc ? 1 : -1;
              return 0;
          }
      });
      renderMainHeaderRR(); renderTableRR(rekapDataRaw, rekapGtRaw);
  }

  async function fetchRekapRR(){
    const l = document.getElementById('loadingRR'), tb = document.getElementById('bodyRR'), trTotal = document.getElementById('rowTotalRRAtas'); 
    if(abortRR) abortRR.abort(); abortRR = new AbortController();
    l.classList.remove('hidden'); 
    tb.innerHTML = `<tr><td colspan="8" class="py-20 text-center text-slate-400 italic text-sm">Sedang mengambil data...</td></tr>`;
    if(trTotal) trTotal.innerHTML = '';
    rekapDataRaw = []; rekapGtRaw = null; sortMainCol = ''; sortMainAsc = true;

    try {
        const cabangVal = document.getElementById('opt_kantor').value;
        const subVal = document.getElementById('opt_sub_otp').value;
        const dpdBucket = document.getElementById('opt_dpd_bucket').value;
        const aoVal = document.getElementById('opt_ao_otp')?.value || "";

        let reqKorwil = "", reqKankas = "";
        if (!cabangVal || cabangVal === "000") reqKorwil = subVal;
        else reqKankas = subVal;

        const titleEl = document.getElementById('otpTitle');
        if(titleEl) titleEl.textContent = `OTP - ${getBucketLabel(dpdBucket)}`;

        const payload = { 
            type: 'rekap_rr', closing_date: document.getElementById('closing_date').value, harian_date: document.getElementById('harian_date').value, 
            kode_kantor: cabangVal || null, korwil: reqKorwil, kode_kankas: reqKankas, kode_ao: aoVal, dpd_bucket: dpdBucket, include_127: document.getElementById('chk_127').checked
        };
        
        const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload), signal: abortRR.signal });
        if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Gagal memuat data");
        
        rekapDataRaw = res.json.data.data || []; rekapGtRaw = res.json.data.grand_total;
        renderMainHeaderRR(); renderTableRR(rekapDataRaw, rekapGtRaw);
    } catch(err) {
        if(err.name !== 'AbortError') tb.innerHTML=`<tr><td colspan="8" class="py-16 text-center text-rose-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">Error: ${err.message}</td></tr>`;
    } finally { l.classList.add('hidden'); }
  }

  function renderTableRR(rows, gt) {
      const tb = document.getElementById('bodyRR'), trTotal = document.getElementById('rowTotalRRAtas');
      tb.innerHTML = '';
      if(rows.length === 0){ tb.innerHTML = `<tr><td colspan="8" class="py-20 text-center text-slate-400 text-sm">Tidak ada data penagihan.</td></tr>`; return; }

      if(gt && trTotal) {
        const gtPct = Number(gt.persen || 0);
        const gtPctClass = gtPct >= 90 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : (gtPct >= 70 ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-rose-700 bg-rose-50 border-rose-200');
        
        trTotal.innerHTML = `
            <th class="px-2 sticky left-0 text-center uppercase tracking-widest shadow-[1px_0_0_#cbd5e1] bg-slate-100/80 text-slate-800 z-20">TOTAL</th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="mb-0.5"><a href="javascript:void(0)" onclick="initModalDetail('ALL','ALL')" class="hover:underline">${fmt(gt.target_os)}</a></div>
                <div class="text-[10px]">NOA: <span>${fmt(gt.target_noa)}</span></div>
            </th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="mb-0.5"><a href="javascript:void(0)" onclick="initModalDetail('ALL','LANCAR')" class="hover:underline">${fmt(gt.lancar_os)}</a></div>
                <div class="text-[10px]">NOA: <span>${fmt(gt.lancar_noa)}</span></div>
            </th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="mb-0.5"><a href="javascript:void(0)" onclick="initModalDetail('ALL','MENUNGGAK')" class="hover:underline">${fmt(gt.macet_os)}</a></div>
                <div class="text-[10px]">NOA: <span>${fmt(gt.macet_noa)}</span></div>
            </th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="mb-0.5"><a href="javascript:void(0)" onclick="initModalLunas('ALL')" class="hover:underline">${fmt(gt.lunas_os)}</a></div>
                <div class="text-[10px]">NOA: <span>${fmt(gt.lunas_noa)}</span></div>
            </th>
            <th class="border-r border-slate-200 px-2 md:px-3 text-center">
                <div class="align-middle pt-1 md:pt-2"><span class="text-slate-700">${fmt(gt.angsuran)}</span></div>
            </th>
            <th class="px-2 md:px-3 text-center">
                <div class="align-middle pt-1 md:pt-2"><span class="text-slate-700">${fmt(gt.total_bayar)}</span></div>
            </th>
            <th class="px-2 text-center align-middle bg-slate-50/80 border-l border-slate-200 z-20">
                <div class="inline-flex items-center justify-center min-w-[70px] rounded border px-2 py-1.5 ${gtPctClass}">
                    <span class="leading-none">${gt.persen}%</span>
                </div>
            </th>
        `;
      }

      let h = '';
      rows.forEach(r => {
          let pctHtml = `<span class="text-slate-300 font-bold text-lg">-</span>`;
          
          if (r.persen !== null && r.persen !== undefined) {
              const pct = Number(r.persen);
              const pctClass = pct >= 90 ? 'text-emerald-700 bg-emerald-50 border-emerald-100' : (pct >= 70 ? 'text-amber-700 bg-amber-50 border-amber-100' : 'text-rose-700 bg-rose-50 border-rose-100');
              
              pctHtml = `
                  <div class="inline-flex items-center justify-center min-w-[64px] rounded border px-1.5 py-1 ${pctClass}">
                      <span class="font-semibold leading-none">${pct}%</span>
                  </div>
              `;
          }
          
          h += `
            <tr class="transition border-b border-slate-100 hover:bg-slate-50">
                <td class="px-2 py-2 sticky left-0 bg-white border-r border-slate-100 text-center shadow-[1px_0_0_#f1f5f9] z-20">${r.tgl}</td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition">
                    <a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','ALL')" class="block mb-0.5">${fmt(r.target_os)}</a>
                    <div class="text-[10px]">NOA: <span>${fmt(r.target_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition">
                    <a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','LANCAR')" class="block mb-0.5">${fmt(r.lancar_os)}</a>
                    <div class="text-[10px]">NOA: <span>${fmt(r.lancar_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition">
                    <a href="javascript:void(0)" onclick="initModalDetail('${r.tgl}','MENUNGGAK')" class="block mb-0.5">${fmt(r.macet_os)}</a>
                    <div class="text-[10px]">NOA: <span>${fmt(r.macet_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition">
                    <a href="javascript:void(0)" onclick="initModalLunas('${r.tgl}')" class="block mb-0.5">${fmt(r.lunas_os)}</a>
                    <div class="text-[10px]">NOA: <span>${fmt(r.lunas_noa)}</span></div>
                </td>
                <td class="px-2 md:px-3 py-2 border-r border-slate-100 text-center transition align-middle">
                    <span class="text-slate-700 block">${fmt(r.angsuran)}</span>
                </td>
                <td class="px-2 md:px-3 py-2 text-center transition align-middle border-r border-slate-100">
                    <span class="text-slate-700 block">${fmt(r.total_bayar)}</span>
                </td>
                <td class="px-2 py-2 text-center align-middle">
                    ${pctHtml}
                </td>
            </tr>`;
      });
      tb.innerHTML = h;
  }

  function createWABtn(phone) {
      if (!phone || phone.trim() === '') return `<span class="text-slate-400 font-mono">-</span>`;
      return `<span class="text-slate-600 font-mono">${phone}</span>`;
  }

  function renderModalHeaderRR() {
      const mHead = document.getElementById('headModalRR');
      
      if (currentMode === 'NORMAL') {
          mHead.innerHTML = `
              <tr class="bg-slate-100">
                  <th class="col-rek hidden md:table-cell px-2 md:px-3 py-2 border-b border-r border-slate-200 text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-center">REKENING ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="col-nas px-2 md:px-4 py-2 border-b border-r border-slate-200 text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[60px] md:w-[80px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kode_produk', 'string')">
                      <div class="flex items-center justify-center">PRODUK ${getSortIcon('kode_produk', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[200px] md:w-[320px] text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_hp', 'string')">
                      <div class="flex items-center justify-center">NO HP ${getSortIcon('no_hp', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('kankas', 'string')">
                      <div class="flex items-center justify-center">KANKAS ${getSortIcon('kankas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[150px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_jatuh_tempo', 'string')">
                      <div class="flex items-center justify-center">TGL JT ${getSortIcon('tgl_jatuh_tempo', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('jml_pinjaman', 'number')">
                      <div class="flex items-center justify-end">PLAFOND ${getSortIcon('jml_pinjaman', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('os_m1', 'number')">
                      <div class="flex items-center justify-end">TARGET M-1 ${getSortIcon('os_m1', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('os_curr', 'number')">
                      <div class="flex items-center justify-end">BAKI DEBET ${getSortIcon('os_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('totung', 'number')">
                      <div class="flex items-center justify-end">TUNGGAKAN ${getSortIcon('totung', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[50px] md:w-[70px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('dpd_curr', 'number')">
                      <div class="flex items-center justify-center">DPD ${getSortIcon('dpd_curr', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[100px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tabungan', 'number')">
                      <div class="flex items-center justify-end">TABUNGAN ${getSortIcon('tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[70px] md:w-[100px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_tabungan', 'string')">
                      <div class="flex items-center justify-center">STAT TAB ${getSortIcon('status_tabungan', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('trx_bulan_lalu', 'number')">
                      <div class="flex items-center justify-end">TRX LALU ${getSortIcon('trx_bulan_lalu', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[110px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('trx_bulan_ini', 'number')">
                      <div class="flex items-center justify-end">TRX INI ${getSortIcon('trx_bulan_ini', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-slate-200 w-[100px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_ket', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_ket', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      } else {
          mHead.innerHTML = `
              <tr class="bg-slate-100">
                  <th class="col-nas-lunas px-2 md:px-4 py-2 border-b border-r border-slate-200 text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_nasabah', 'string')">
                      <div class="flex items-center justify-start">NAMA NASABAH ${getSortIcon('nama_nasabah', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[200px] md:w-[350px] text-left cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('alamat', 'string')">
                      <div class="flex items-center justify-start">ALAMAT ${getSortIcon('alamat', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[100px] md:w-[150px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('nama_ao', 'string')">
                      <div class="flex items-center justify-center">AO ${getSortIcon('nama_ao', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('no_rekening', 'string')">
                      <div class="flex items-center justify-center">REK LAMA ${getSortIcon('no_rekening', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('plafon_lama', 'number')">
                      <div class="flex items-center justify-end">PLAFOND LAMA ${getSortIcon('plafon_lama', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('os_lunas', 'number')">
                      <div class="flex items-center justify-end">OS M-1 ${getSortIcon('os_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[80px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('status_lunas', 'string')">
                      <div class="flex items-center justify-center">STATUS ${getSortIcon('status_lunas', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('rek_baru', 'string')">
                      <div class="flex items-center justify-center">REK BARU ${getSortIcon('rek_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-4 py-2 border-b border-r border-slate-200 w-[90px] md:w-[130px] text-right cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('plafond_baru', 'number')">
                      <div class="flex items-center justify-end">PLAFOND BARU ${getSortIcon('plafond_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
                  <th class="px-2 md:px-3 py-2 border-b border-slate-200 w-[80px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition select-none" onclick="sortDetailRR('tgl_baru', 'string')">
                      <div class="flex items-center justify-center">TGL REALISASI ${getSortIcon('tgl_baru', sortDetailCol, sortDetailAsc)}</div>
                  </th>
              </tr>
          `;
      }
  }

  window.sortDetailRR = function(col, type) {
      if (!detailDataCache || detailDataCache.length === 0) return;
      if (sortDetailCol === col) sortDetailAsc = !sortDetailAsc; else { sortDetailCol = col; sortDetailAsc = true; }

      detailDataCache.sort((a, b) => {
          let valA = a[col], valB = b[col];
          if (type === 'number') return sortDetailAsc ? (parseFloat(valA)||0) - (parseFloat(valB)||0) : (parseFloat(valB)||0) - (parseFloat(valA)||0);
          valA = String(valA||'').toLowerCase(); valB = String(valB||'').toLowerCase();
          if (valA < valB) return sortDetailAsc ? -1 : 1;
          if (valA > valB) return sortDetailAsc ? 1 : -1;
          return 0;
      });
      renderModalHeaderRR(); renderTableDetailBodyRR(detailDataCache);
  }

  async function initModalDetail(tgl, status) {
      currentMode = 'NORMAL';
      const branch = document.getElementById('opt_kantor').value || null;
      const subVal = document.getElementById('opt_sub_otp').value;
      const dpdBucket = document.getElementById('opt_dpd_bucket').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;
      const mainAo = document.getElementById('opt_ao_otp')?.value || "";

      let reqKorwil = "", reqKankas = "";
      if (!branch || branch === "000") reqKorwil = subVal;
      else reqKankas = subVal;

      let preselectKankasCode = "";
      if (lblSub === "KANKAS" && subVal !== "") preselectKankasCode = subVal;

      currentDetailParams = { 
          type: 'detail_rr', closing_date: document.getElementById('closing_date').value, harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, korwil: reqKorwil, kode_kankas: preselectKankasCode || reqKankas, kode_ao: mainAo,
          tgl_tagih: tgl, status: status, dpd_bucket: dpdBucket, include_127: document.getElementById('chk_127').checked, limit: detailLimit 
      };

      const bucketLabel = getBucketLabel(dpdBucket);
      document.getElementById('modalTitleRR').textContent = `Detail Matriks OTP ${bucketLabel} - Tgl ${tgl}`;
      document.getElementById('modalSubTitleRR').textContent = `Status: ${status}`;
      document.getElementById('modalDetailRR').classList.remove('hidden');
      
      if(document.getElementById('search_nasabah')) document.getElementById('search_nasabah').value = ''; if(document.getElementById('search_nasabah_mobile')) document.getElementById('search_nasabah_mobile').value = '';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      loadDetailPage(1);
  }

  async function initModalLunas(tgl) {
      currentMode = 'LUNAS';
      const branch = document.getElementById('opt_kantor').value || null;
      const subVal = document.getElementById('opt_sub_otp').value;
      const dpdBucket = document.getElementById('opt_dpd_bucket').value;
      const lblSub = document.getElementById('lbl_sub_otp').innerText;
      const mainAo = document.getElementById('opt_ao_otp')?.value || "";

      let reqKorwil = "", reqKankas = "";
      if (!branch || branch === "000") reqKorwil = subVal;
      else reqKankas = subVal;

      let preselectKankasCode = "";
      if (lblSub === "KANKAS" && subVal !== "") preselectKankasCode = subVal;

      currentDetailParams = { 
          type: 'detail_lunas_rr', closing_date: document.getElementById('closing_date').value, harian_date: document.getElementById('harian_date').value, 
          kode_kantor: branch, korwil: reqKorwil, kode_kankas: preselectKankasCode || reqKankas, kode_ao: mainAo,
          tgl_tagih: tgl, dpd_bucket: dpdBucket, include_127: document.getElementById('chk_127').checked, limit: detailLimit 
      };

      const bucketLabel = getBucketLabel(dpdBucket);
      document.getElementById('modalTitleRR').textContent = `Pelunasan OTP ${bucketLabel} - Tgl ${tgl}`;
      document.getElementById('modalSubTitleRR').textContent = `Refinancing & Prospek`;
      document.getElementById('modalDetailRR').classList.remove('hidden');
      
      if(document.getElementById('search_nasabah')) document.getElementById('search_nasabah').value = ''; if(document.getElementById('search_nasabah_mobile')) document.getElementById('search_nasabah_mobile').value = '';
      sortDetailCol = ''; sortDetailAsc = true;
      renderModalHeaderRR();

      loadDetailPage(1);
  }

  window.filterTableDetail = function() {
      const desktop = document.getElementById("search_nasabah");
      const mobile = document.getElementById("search_nasabah_mobile");
      const active = (desktop && document.activeElement === desktop) ? desktop : ((mobile && document.activeElement === mobile) ? mobile : (desktop || mobile));
      const filter = (active?.value || '').toLowerCase().trim();
      const tbody = document.getElementById("bodyModalRR");
      if (!tbody) return;
      const trs = tbody.getElementsByTagName("tr");

      for (let i = 0; i < trs.length; i++) {
          const cells = trs[i].getElementsByTagName("td");
          if (!cells.length) continue;
          let haystack = '';
          if (currentMode === 'NORMAL') {
              haystack = `${cells[0]?.textContent || ''} ${cells[1]?.textContent || ''}`;
          } else {
              haystack = `${cells[0]?.textContent || ''} ${cells[3]?.textContent || ''} ${cells[7]?.textContent || ''}`;
          }
          trs[i].style.display = haystack.toLowerCase().indexOf(filter) > -1 ? "" : "none";
      }
  }

  async function loadDetailPage(page) {
      const l = document.getElementById('loadingModalRR'), tb = document.getElementById('bodyModalRR'), info = document.getElementById('pageInfoRR');
      l.classList.remove('hidden'); tb.innerHTML = '';

      try {
          const payload = { ...currentDetailParams, page: page };
          const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          
          if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Gagal memuat detail");
          
          detailDataCache = res.json.data?.data || [];
          const meta = res.json.data?.pagination || { total_records:0, total_pages:1 };

          currentDetailPage = page; currentDetailTotalPages = meta.total_pages;

          if(detailDataCache.length === 0) {
              tb.innerHTML = `<tr><td colspan="${currentMode === 'NORMAL' ? 18 : 10}" class="py-20 text-center text-slate-400 italic text-sm">Tidak ada data detail.</td></tr>`;
              info.innerText = `0 Data`;
          } else {
              sortDetailCol = ''; sortDetailAsc = true;
              renderModalHeaderRR(); renderTableDetailBodyRR(detailDataCache);
              info.innerText = `Hal ${page} dari ${meta.total_pages} (${fmt(meta.total_records)} Data)`;
          }

          document.getElementById('btnPrevRR').disabled = page <= 1;
          document.getElementById('btnNextRR').disabled = page >= meta.total_pages;
      } catch(err){ 
          console.error(err); 
          tb.innerHTML = `<tr><td colspan="${currentMode === 'NORMAL' ? 18 : 10}" class="py-16 text-center text-rose-500 font-bold uppercase tracking-widest text-[10px] md:text-sm">Gagal memuat detail</td></tr>`;
      } finally { l.classList.add('hidden'); }
  }

  function renderTableDetailBodyRR(list) {
      const tb = document.getElementById('bodyModalRR');
      let h = '';
      
      list.forEach(r => {
          const aoName = (r.nama_ao || '-').split(' ').slice(0, 2).join(' ');

          if(currentMode === 'NORMAL') {
              const btnWa = createWABtn(r.no_hp);
              const alamatLengkap = r.alamat || '-';

              h += `<tr class="transition border-b border-slate-100 hover:bg-slate-50 h-[44px] md:h-[48px]">
                    <td class="col-rek hidden md:table-cell px-2 md:px-3 py-1.5 border-r border-slate-100 font-mono text-slate-500">${r.no_rekening}</td>
                    <td class="col-nas px-2 md:px-4 py-1.5 border-r border-slate-100 truncate" title="${r.nama_nasabah}">${r.nama_nasabah}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono text-slate-500">${r.kode_produk || '-'}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-slate-500 truncate max-w-[200px] md:max-w-[320px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center">${btnWa}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono">
                        ${r.kankas && r.kankas !== '-' ? `<span class="font-medium text-slate-600">${r.kankas}</span>` : '-'}
                    </td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-center truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center font-mono text-slate-500">${r.tgl_jatuh_tempo||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">${fmt(r.jml_pinjaman)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.os_m1)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.os_curr)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.totung)}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center text-slate-600">${r.dpd_curr}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">${fmt(r.tabungan)}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 text-center">
                        ${r.status_tabungan === 'Aman' ? '<span class="text-emerald-600 font-semibold">Aman</span>' : '<span class="text-rose-500 font-semibold">Belum Aman</span>'}
                    </td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">
                        <div>${fmt(r.trx_bulan_lalu)}</div>
                        <div class="text-[9px] text-slate-400 font-mono">${Number(r.trx_bulan_lalu || 0) > 0 ? fmtDateID(r.tgl_bayar_lalu) : ''}</div>
                    </td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">
                        <div>${fmt(r.trx_bulan_ini)}</div>
                        <div class="text-[9px] text-slate-400 font-mono">${Number(r.trx_bulan_ini || 0) > 0 ? fmtDateID(r.tgl_bayar_ini) : ''}</div>
                    </td>
                    <td class="px-2 md:px-4 py-1.5 text-center font-semibold ${r.status_ket === 'LANCAR' ? 'text-emerald-600' : (r.status_ket === 'MENUNGGAK' ? 'text-rose-600' : 'text-slate-500')}">${r.status_ket}</td>
                </tr>`;
          } else {
              const alamatLengkap = r.alamat || '-';
              let badge = `<span class="text-xs font-semibold text-blue-600">PROSPEK</span>`;
              if(r.status_lunas === 'REFINANCING / Top Up') badge = `<span class="text-xs font-semibold text-emerald-600">REFINANCING</span>`;

              h += `<tr class="transition border-b border-slate-100 hover:bg-slate-50 h-[44px] md:h-[48px]">
                    <td class="col-nas-lunas px-2 md:px-4 py-1.5 border-r border-slate-100 truncate">
                        ${r.nama_nasabah}
                        <div class="text-[9px] text-slate-400 font-mono font-normal">ID: ${r.nasabah_id}</div>
                    </td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-slate-500 truncate max-w-[200px] md:max-w-[350px]" title="${alamatLengkap}">${alamatLengkap}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-center truncate">${aoName}</td>
                    <td class="px-2 md:px-3 py-1.5 border-r border-slate-100 font-mono text-center text-slate-500">${r.no_rekening}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-600">${fmt(r.plafon_lama)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-right text-slate-700 bg-slate-50/50">${fmt(r.os_lunas)}</td>
                    <td class="px-2 md:px-4 py-1.5 border-r border-slate-100 text-center">${badge}</td>
                    <td class="px-2 md:px-3 border-r border-slate-100 font-mono text-center text-emerald-600">${r.rek_baru}</td>
                    <td class="px-2 md:px-4 border-r border-slate-100 text-right text-emerald-600">${fmt(r.plafond_baru)}</td>
                    <td class="px-2 md:px-3 text-center text-slate-500">${r.tgl_baru}</td>
                </tr>`;
          }
      });
      tb.innerHTML = h;
  }

  async function downloadExcelFull(ev) {
      const btn = (ev?.target || window.event?.target)?.closest('button'); if(!btn) return; const txt = btn.innerHTML;
      btn.innerHTML = `<span class="animate-spin inline-block h-3.5 w-3.5 border-2 border-white border-t-transparent rounded-full md:mr-2"></span><span class="hidden md:inline">...</span>`;
      btn.disabled = true;

      try {
          const payload = { ...currentDetailParams, page: 1, limit: 10000 };
          const res = await apiCall(API_RR_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
          if(!res.ok || res.json.status !== 200) throw new Error(res.json.message || "Export gagal");
          
          const rows = res.json.data?.data || [];
          if(rows.length === 0) { alert("Tidak ada data untuk diexport"); return; }

          let csv = "";
          if(currentMode === 'NORMAL') {
              csv = `No Rekening\tNama Nasabah\tKode Produk\tAlamat\tNo HP\tKankas\tNama AO\tTgl JT\tPlafond\tTarget (M-1)\tBaki Debet Actual\tTot Tunggakan\tDPD\tSaldo Tabungan\tStatus Tabungan\tTrx Bulan Lalu\tTgl Bayar Lalu\tTrx Bulan Ini\tTgl Bayar Ini\tStatus Tagih\n`;
              rows.forEach(r => {
                  csv += `'${r.no_rekening}\t${r.nama_nasabah}\t${r.kode_produk||''}\t${r.alamat||''}\t'${r.no_hp||''}\t${r.kankas||''}\t${r.nama_ao}\t${r.tgl_jatuh_tempo}\t${Math.round(r.jml_pinjaman)}\t${Math.round(r.os_m1)}\t${Math.round(r.os_curr)}\t${Math.round(r.totung)}\t${r.dpd_curr}\t${Math.round(r.tabungan)}\t${r.status_tabungan}\t${Math.round(r.trx_bulan_lalu||0)}\t${fmtDateID(r.tgl_bayar_lalu)}\t${Math.round(r.trx_bulan_ini||0)}\t${fmtDateID(r.tgl_bayar_ini)}\t${r.status_ket}\n`;
              });
          } else {
              csv = `Nama Nasabah\tID Nasabah\tAlamat\tNama AO\tRek Lama\tPlafond Lama\tOS Lunas (M-1)\tStatus\tRek Baru\tPlafond Baru\tTgl Realisasi Baru\n`;
              rows.forEach(r => {
                  csv += `${r.nama_nasabah}\t'${r.nasabah_id}\t${r.alamat||''}\t${r.nama_ao}\t'${r.no_rekening}\t${Math.round(r.plafon_lama)}\t${Math.round(r.os_lunas)}\t${r.status_lunas}\t'${r.rek_baru}\t${Math.round(r.plafond_baru)}\t${r.tgl_baru}\n`;
              });
          }

          const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a'); a.href = url; a.download = `RR_Detail_${currentMode}_${currentDetailParams.tgl_tagih}.xls`;
          document.body.appendChild(a); a.click(); document.body.removeChild(a);

      } catch(e) { console.error(e); alert("Gagal export data."); } 
      finally { btn.innerHTML = txt; btn.disabled = false; }
  }

  window.changePageDetail = (step) => { const n = currentDetailPage + step; if (n > 0 && n <= currentDetailTotalPages) loadDetailPage(n); }
  window.closeModalRR = () => document.getElementById('modalDetailRR').classList.add('hidden');
  document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModalRR(); });
</script>
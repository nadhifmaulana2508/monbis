<div class="max-w-full mx-auto px-4 py-5 h-[calc(100vh-80px)] flex flex-col bg-slate-50 font-sans" id="SD_root">
  
  <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-4 shrink-0">
    <div class="flex items-start justify-between w-full md:w-auto">
      <div>
        <h1 class="text-xl md:text-2xl font-bold flex items-center gap-2 text-slate-800">
          <span class="bg-blue-600 text-white p-1.5 rounded-lg shadow-sm text-sm">🔎</span>
          <span>Search Debitur</span>
        </h1>
        
        <div class="flex items-center gap-3 mt-3">
          <div class="px-3 py-1 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-2">
              <span class="text-[9px] font-bold text-blue-600 uppercase tracking-wider">NOA:</span>
              <span class="text-xs font-bold text-blue-900" id="SD_sumNoa">0</span>
          </div>
          <div class="px-3 py-1 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2">
              <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider">BD:</span>
              <span class="text-xs font-bold text-emerald-900" id="SD_sumBd">0</span>
          </div>
        </div>
      </div>

      <button id="SD_btnToggleFilter" class="md:hidden flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 rounded-lg bg-white text-xs font-semibold text-slate-700 shadow-sm transition active:scale-95">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
          Filter
      </button>
    </div>

    <div id="SD_panelFilter" class="hidden md:block w-full md:w-auto transition-all">
        <form id="SD_formFilter" class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-stretch md:items-end gap-3 shrink-0">
          <div class="field flex flex-col gap-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">Kantor</label>
            <select id="SD_optKantor" class="inp w-full md:min-w-[150px] cursor-pointer">
              <option value="">Semua</option>
            </select>
          </div>
          
          <div class="grid grid-cols-2 md:flex gap-3">
              <div class="field flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">Kolek</label>
                <select id="SD_optKolek" class="inp w-full md:min-w-[80px] cursor-pointer">
                  <option value="Semua">Semua</option>
                  <option value="L">L</option><option value="DP">DP</option>
                  <option value="KL">KL</option><option value="D">D</option><option value="M">M</option>
                </select>
              </div>
              <div class="field flex flex-col gap-1">
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">Totung (Max)</label>
                <input type="number" id="SD_totung" placeholder="0" class="inp w-full md:w-[100px]">
              </div>
          </div>

          <div class="field flex flex-col gap-1">
            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">Cari Nama/Rekening/Alamat</label>
            <input type="text" id="SD_search" placeholder="Ketik di sini..." class="inp w-full md:w-[200px]">
          </div>
          
          <div class="flex items-center gap-2 pt-2 md:pt-0">
            <button type="submit" class="flex-1 md:flex-none h-9 px-5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition flex items-center justify-center gap-2 font-bold text-xs uppercase">
              <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              Cari
            </button>
            <button type="button" onclick="SD_exportExcelAll()" class="h-9 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition flex items-center justify-center" title="Excel">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            </button>
          </div>
        </form>
    </div>
  </div>

  <div class="flex-1 min-h-0 relative flex flex-col bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div id="SD_loading" class="hidden absolute inset-0 bg-white/80 z-50 flex flex-col items-center justify-center text-blue-600 backdrop-blur-sm">
       <div class="animate-spin h-10 w-10 border-4 border-blue-200 border-t-blue-600 rounded-full mb-3"></div>
       <span class="text-xs font-bold tracking-widest uppercase">Mencari Data...</span>
    </div>

    <div class="flex-1 overflow-auto custom-scrollbar" id="SD_tableContainer">
      <table id="SD_table" class="min-w-full text-left text-xs table-fixed border-collapse">
        <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] tracking-wider sticky top-0 z-20 shadow-sm border-b border-slate-200">
          <tr>
            <th class="px-2 py-3 border-r border-slate-200 text-center hide-mobile sd-col-kc">Kantor</th>
            <th class="px-2 py-3 border-r border-slate-200 text-center hide-mobile sd-col-rek">No Rekening</th>
            <th class="px-4 py-3 border-r border-slate-200 sd-col-nas">Nama Debitur</th>
            <th class="px-4 py-3 border-r border-slate-200 w-48">Alamat</th>
            <th class="px-4 py-3 border-r border-slate-200 w-24 text-center">Produk</th>
            <th class="px-4 py-3 border-r border-slate-200 w-16 text-center">Kolek</th>
            <th class="px-4 py-3 border-r border-slate-200 w-16 text-center">DPD</th>
            <th class="px-4 py-3 border-r border-slate-200 w-16 text-center">HMP</th>
            <th class="px-4 py-3 border-r border-slate-200 w-16 text-center">HMB</th>
            <th class="px-4 py-3 border-r border-slate-200 w-24 text-center">Jatuh Tempo</th>
            <th class="px-4 py-3 border-r border-slate-200 w-32 text-right bg-emerald-50">Baki Debet</th>
            <th class="px-4 py-3 border-r border-slate-200 w-28 text-right bg-orange-50">T. Pokok</th>
            <th class="px-4 py-3 border-r border-slate-200 w-28 text-right bg-orange-50">T. Bunga</th>
            <th class="px-4 py-3 border-r border-slate-200 w-28 text-right bg-red-50">Totung</th>
            <th class="px-4 py-3 w-32 text-right">Tabungan</th>
          </tr>
        </thead>
        <tbody id="SD_totalRow" class="sticky top-[37px] z-10 font-bold bg-blue-50 text-blue-800 border-b-2 border-blue-200"></tbody>
        
        <tbody id="SD_tbody" class="text-slate-700 divide-y divide-slate-100">
          <tr><td colspan="15" class="px-4 py-12 text-center text-slate-400 font-medium">Silakan masukkan filter dan klik Cari.</td></tr>
        </tbody>
      </table>
    </div>

    <div class="bg-slate-50 border-t border-slate-200 p-3 flex flex-col md:flex-row items-center justify-between gap-3 shrink-0">
        <span class="text-[10px] md:text-xs text-slate-500 font-medium" id="SD_pageInfo">Total: 0 Debitur</span>
        <div class="flex items-center gap-2">
            <button onclick="SD_changePage(-1)" id="SD_btnPrev" class="px-3 py-1.5 bg-white border border-slate-300 rounded hover:bg-slate-100 text-slate-600 disabled:opacity-50 transition text-[10px] font-bold shadow-sm">« Prev</button>
            <span class="text-[10px] md:text-xs font-bold text-slate-700 px-2" id="SD_pageCurrent">Hal 1 / 1</span>
            <button onclick="SD_changePage(1)" id="SD_btnNext" class="px-3 py-1.5 bg-white border border-slate-300 rounded hover:bg-slate-100 text-slate-600 disabled:opacity-50 transition text-[10px] font-bold shadow-sm">Next »</button>
        </div>
    </div>
  </div>
</div>

<style>
  /* Custom Scrollbar */
  .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

  .inp { border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0 0.6rem; font-size: 13px; background: #fff; height: 38px; outline: none; transition: 0.2s; }
  .inp:focus { border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,0.1); }
  
  #SD_table th, #SD_table td { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  
  /* HIDE COLUMNS ON MOBILE */
  @media (max-width: 767px) {
      .hide-mobile { display: none !important; }
  }

  /* ========================================================
     🔥 STICKY/FREEZE CONFIGURATION (Kantor, Norek, Nama) 🔥
     ======================================================== */
  .sd-col-kc { position: sticky !important; left: 0 !important; z-index: 10; background-color: #fff; width: 70px; min-width: 70px; max-width: 70px; box-shadow: inset -1px 0 0 #e2e8f0; }
  .sd-col-rek { position: sticky !important; left: 70px !important; z-index: 10; background-color: #fff; width: 110px; min-width: 110px; max-width: 110px; box-shadow: inset -1px 0 0 #e2e8f0; }
  
  /* Di Mobile: Kantor & Rekening disembunyikan, Nama Debitur langsung ke kiri (left 0) */
  .sd-col-nas { position: sticky !important; left: 0 !important; z-index: 10; background-color: #fff; width: 220px; min-width: 220px; max-width: 220px; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); }

  @media (min-width: 768px) {
      /* Di PC/Laptop: Nama Debitur menggeser menyesuaikan lebar Kantor(70) + Rekening(110) = 180px */
      .sd-col-nas { left: 180px !important; box-shadow: 2px 0 4px -2px rgba(0,0,0,0.1); }
  }

  /* Z-INDEX TINGKAT DEWA UNTUK HEADER DAN TOTAL YANG BERPOTONGAN */
  #SD_table thead th.sd-col-kc, #SD_table thead th.sd-col-rek, #SD_table thead th.sd-col-nas {
      z-index: 30 !important; background-color: #f1f5f9 !important;
  }
  
  #SD_totalRow td.sd-col-kc, #SD_totalRow td.sd-col-rek, #SD_totalRow td.sd-col-nas {
      z-index: 25 !important; background-color: #eff6ff !important;
      box-shadow: inset -1px 0 0 #bfdbfe, inset 0 1px 0 #bfdbfe !important;
  }

  /* Hover Effects Agar Area Sticky Ikut Berganti Warna */
  #SD_tbody tr:hover td { background-color: #f8fafc !important; }
  #SD_tbody tr:hover td.sd-col-kc, #SD_tbody tr:hover td.sd-col-rek, #SD_tbody tr:hover td.sd-col-nas {
      filter: brightness(0.98); background-color: #f8fafc !important;
  }
</style>

<script>
  const nf = new Intl.NumberFormat('id-ID');
  let SD_currentPage = 1;
  const SD_limit = 50;
  let SD_totalPage = 1;

  // Helper untuk potong text jadi max 20 karakter
  const cut = (s, n) => { 
      s = String(s || ''); 
      return s.length <= n ? s : (s.slice(0, n).trimEnd() + '…'); 
  };

  document.getElementById('SD_btnToggleFilter').addEventListener('click', () => {
      document.getElementById('SD_panelFilter').classList.toggle('hidden');
  });

  window.addEventListener('DOMContentLoaded', async () => {
      await populateKantorSD();
      const user = (window.getUser && window.getUser()) || JSON.parse(localStorage.getItem('app_user')) || {};
      const uKode = String(user?.kode || user?.kode_kantor || '').padStart(3,'0');
      if (uKode !== '000' && uKode !== '00') {
          document.getElementById('SD_optKantor').value = uKode;
          document.getElementById('SD_optKantor').disabled = true;
          document.getElementById('SD_optKantor').classList.add('bg-slate-100', 'cursor-not-allowed', 'text-slate-500');
      }
      fetchDataSD(1);
  });

  document.getElementById('SD_formFilter').addEventListener('submit', (e) => {
      e.preventDefault();
      if (window.innerWidth < 768) document.getElementById('SD_panelFilter').classList.add('hidden');
      fetchDataSD(1);
  });

  async function populateKantorSD() {
      try {
          const res = await fetch('./api/kode/', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'})});
          const json = await res.json();
          let html = `<option value="">Semua Kantor</option>`;
          (json.data || []).filter(x => x.kode_kantor !== '000').sort((a,b) => String(a.kode_kantor).localeCompare(b.kode_kantor)).forEach(it => {
              html += `<option value="${it.kode_kantor}">${it.kode_kantor} — ${it.nama_kantor}</option>`;
          });
          document.getElementById('SD_optKantor').innerHTML = html;
      } catch(e) {}
  }

  async function fetchDataSD(pageTarget) {
      SD_currentPage = pageTarget;
      const loading = document.getElementById('SD_loading');
      loading.classList.remove('hidden');

      const user = (window.getUser && window.getUser()) || JSON.parse(localStorage.getItem('app_user')) || {};
      const uKodeLogin = String(user?.kode || user?.kode_kantor || '000').padStart(3,'0');

      const payload = {
          type: "cari debitur",
          user_kode: uKodeLogin,
          kode_kantor: document.getElementById('SD_optKantor').value,
          kolek: document.getElementById('SD_optKolek').value,
          search: document.getElementById('SD_search').value.trim(),
          totung: document.getElementById('SD_totung').value,
          page: SD_currentPage,
          limit: SD_limit
      };

      try {
          const res = await fetch('./api/flow_par/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
          const json = await res.json();
          if (json.status === 200 && json.data) {
              renderTableSD(json.data.data);
              renderTotalRowSD(json.data.summary, json.data.data);
              updateSummarySD(json.data.summary);
              updatePaginationSD(json.data.pagination);
          } else { throw new Error(json.message); }
      } catch(e) {
          document.getElementById('SD_tbody').innerHTML = `<tr><td colspan="15" class="px-4 py-12 text-center text-red-500 font-bold">${e.message}</td></tr>`;
      } finally { loading.classList.add('hidden'); }
  }

  function renderTotalRowSD(sum, dataList) {
      const el = document.getElementById('SD_totalRow');
      if (!sum || dataList.length === 0) { el.innerHTML = ''; return; }
      
      let totalTotung = 0;
      let totalTab = 0;
      let totalTPokok = 0;
      let totalTBunga = 0;
      
      dataList.forEach(d => {
          totalTotung += Number(d.totung || 0);
          totalTab += Number(d.saldo_tabungan || 0);
          totalTPokok += Number(d.tunggakan_pokok || 0);
          totalTBunga += Number(d.tunggakan_bunga || 0);
      });

      // Kolom: KC(1), Norek(1), Nasabah(1), [Alamat, Produk, Kolek, DPD, HMP, HMB, JT](7), BD(1), TPokok(1), TBunga(1), Totung(1), Tab(1) => Total 15 kolom
      el.innerHTML = `
        <tr>
          <td class="px-2 py-2 border-r text-center hide-mobile sd-col-kc bg-blue-50/90 text-[10px]">ALL</td>
          <td class="px-2 py-2 border-r text-center hide-mobile sd-col-rek bg-blue-50/90">-</td>
          <td class="px-4 py-2 border-r sd-col-nas bg-blue-50/90">TOTAL DATA</td>
          <td colspan="7" class="border-r text-center bg-slate-50/30">-</td>
          <td class="px-4 py-2 border-r text-right bg-emerald-100/50">${nf.format(sum.bd_act || 0)}</td>
          <td class="px-4 py-2 border-r text-right bg-orange-100/50">${nf.format(totalTPokok)}</td>
          <td class="px-4 py-2 border-r text-right bg-orange-100/50">${nf.format(totalTBunga)}</td>
          <td class="px-4 py-2 border-r text-right bg-red-100/50">${nf.format(totalTotung)}</td>
          <td class="px-4 py-2 text-right bg-blue-100/30">${nf.format(totalTab)}</td>
        </tr>`;
  }

  function renderTableSD(list) {
      const tbody = document.getElementById('SD_tbody');
      if (!list || list.length === 0) {
          tbody.innerHTML = `<tr><td colspan="15" class="px-4 py-12 text-center text-slate-400 font-medium">Debitur tidak ditemukan.</td></tr>`;
          return;
      }
      tbody.innerHTML = list.map(d => `
        <tr class="transition border-b border-slate-100">
          <td class="px-2 py-2 border-r text-center text-slate-400 font-mono hide-mobile sd-col-kc bg-white">${d.kode_cabang || '-'}</td>
          <td class="px-2 py-2 border-r text-center font-mono text-slate-500 hide-mobile sd-col-rek bg-white text-[11px]">${d.no_rekening||'-'}</td>
          <td class="px-4 py-2 border-r font-bold text-slate-700 truncate sd-col-nas bg-white" title="${d.nama_nasabah}">${d.nama_nasabah||'-'}</td>
          <td class="px-4 py-2 border-r text-slate-600 truncate" title="${d.alamat||''}">${cut(d.alamat, 20)||'-'}</td>
          <td class="px-4 py-2 border-r text-center">${d.kode_produk||'-'}</td>
          <td class="px-4 py-2 border-r text-center font-bold">${d.kolek||'-'}</td>
          <td class="px-4 py-2 border-r text-center">${d.dpd||'0'}</td>
          <td class="px-4 py-2 border-r text-center">${d.hmp||'0'}</td>
          <td class="px-4 py-2 border-r text-center">${d.hmb||'0'}</td>
          <td class="px-4 py-2 border-r text-center">${d.tgl_jatuh_tempo||'-'}</td>
          <td class="px-4 py-2 border-r text-right text-emerald-700 font-bold bg-emerald-50/30">${nf.format(d.baki_debet||0)}</td>
          <td class="px-4 py-2 border-r text-right text-orange-700 font-bold bg-orange-50/30">${nf.format(d.tunggakan_pokok||0)}</td>
          <td class="px-4 py-2 border-r text-right text-orange-700 font-bold bg-orange-50/30">${nf.format(d.tunggakan_bunga||0)}</td>
          <td class="px-4 py-2 border-r text-right text-red-600 font-bold bg-red-50/30">${nf.format(d.totung||0)}</td>
          <td class="px-4 py-2 text-right text-slate-600">${nf.format(d.saldo_tabungan||0)}</td>
        </tr>`).join('');
  }

  function updateSummarySD(s) { 
      document.getElementById('SD_sumNoa').innerText = nf.format(s.noa || 0); 
      document.getElementById('SD_sumBd').innerText = nf.format(s.bd_act || 0); 
  }

  function updatePaginationSD(p) {
      SD_totalPage = p.total_page || 1;
      SD_currentPage = p.current_page || 1;
      document.getElementById('SD_pageInfo').innerText = `Total: ${nf.format(p.total_data || 0)} Debitur`;
      document.getElementById('SD_pageCurrent').innerText = `Hal ${SD_currentPage} / ${SD_totalPage}`;
      document.getElementById('SD_btnPrev').disabled = (SD_currentPage <= 1);
      document.getElementById('SD_btnNext').disabled = (SD_currentPage >= SD_totalPage);
  }
  
  window.SD_changePage = (dir) => { let n = SD_currentPage + dir; if (n >= 1 && n <= SD_totalPage) fetchDataSD(n); };

  window.SD_exportExcelAll = async function() {
      const btn = document.querySelector('button[title="Excel"]');
      const original = btn.innerHTML;
      btn.innerHTML = `<div class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>`;
      try {
          const user = (window.getUser && window.getUser()) || JSON.parse(localStorage.getItem('app_user')) || {};
          const uKodeLogin = String(user?.kode || user?.kode_kantor || '000').padStart(3,'0');

          const payload = {
              type: "cari debitur",
              user_kode: uKodeLogin,
              kode_kantor: document.getElementById('SD_optKantor').value,
              kolek: document.getElementById('SD_optKolek').value,
              search: document.getElementById('SD_search').value.trim(),
              totung: document.getElementById('SD_totung').value,
              page: 1,
              limit: 1000000
          };
          const res = await fetch('./api/flow_par/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
          const json = await res.json();
          if (json.status === 200 && json.data?.data) {
              let table = `<table border="1"><thead><tr><th>KANTOR</th><th>REKENING</th><th>NAMA</th><th>ALAMAT</th><th>PRODUK</th><th>KOLEK</th><th>DPD</th><th>HMP</th><th>HMB</th><th>JATUH TEMPO</th><th>BAKI DEBET</th><th>T. POKOK</th><th>T. BUNGA</th><th>TOTUNG</th><th>TABUNGAN</th></tr></thead><tbody>`;
              json.data.data.forEach(d => { 
                table += `<tr><td>${d.kode_cabang||''}</td><td style="mso-number-format:'\\@'">${d.no_rekening||''}</td><td>${d.nama_nasabah||''}</td><td>${d.alamat||''}</td><td>${d.kode_produk||''}</td><td>${d.kolek||''}</td><td>${d.dpd||'0'}</td><td>${d.hmp||'0'}</td><td>${d.hmb||'0'}</td><td>${d.tgl_jatuh_tempo||''}</td><td>${d.baki_debet||'0'}</td><td>${d.tunggakan_pokok||'0'}</td><td>${d.tunggakan_bunga||'0'}</td><td>${d.totung||'0'}</td><td>${d.saldo_tabungan||'0'}</td></tr>`; 
              });
              table += `</tbody></table>`;
              const blob = new Blob([table], { type: 'application/vnd.ms-excel' });
              const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
              a.download = `Cari_Debitur_${new Date().getTime()}.xls`; a.click();
          }
      } catch(e) { alert("Download gagal"); } finally { btn.innerHTML = original; }
  };
</script>
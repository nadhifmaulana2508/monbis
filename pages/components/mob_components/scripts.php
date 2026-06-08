<script>
const API_URL = './api/kredit/'; 
const API_KODE = './api/kode/';
const API_DATE = './api/date/'; 
const nfID = new Intl.NumberFormat('id-ID');
const fmt = n => nfID.format(Math.round(Number(n||0)));
const apiCall = (url, opt={}) => (window.apiFetch ? window.apiFetch(url,opt) : fetch(url,opt));

let abortMainMob;
let detailParamsMob = {}; 
let detailPageMob = 1;
let rekapDataCacheMob = null; 

// 🔥 INIT SEARCHABLE DROPDOWN VANILLA JS 🔥
function setupSearchableDropdown(inputId, hiddenId, listId, dataArray, placeholderText) {
    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);
    const list = document.getElementById(listId);
    
    input.value = ''; hidden.value = '';
    
    function renderList(filterTxt = '') {
        list.innerHTML = `<div class="dropdown-item text-slate-400 italic" data-val="">-- Semua / Kosongkan --</div>`;
        let count = 0;
        dataArray.forEach(item => {
            const txt = `${item.kode} - ${item.nama}`;
            if (txt.toLowerCase().includes(filterTxt.toLowerCase())) {
                list.innerHTML += `<div class="dropdown-item" data-val="${item.kode}">${txt}</div>`;
                count++;
            }
        });
        if(count === 0) list.innerHTML += `<div class="p-2 text-xs text-slate-400 text-center">Tidak ditemukan</div>`;
    }

    input.addEventListener('focus', () => { renderList(input.value); list.classList.add('show'); });
    input.addEventListener('input', (e) => { renderList(e.target.value); list.classList.add('show'); });
    
    // Sembunyikan list jika klik di luar
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !list.contains(e.target)) {
            list.classList.remove('show');
            // Reset text jika value kosong
            if(!hidden.value) input.value = '';
        }
    });

    // Pilihan diklik
    list.addEventListener('click', (e) => {
        if(e.target.classList.contains('dropdown-item')) {
            const val = e.target.getAttribute('data-val');
            hidden.value = val;
            input.value = val === "" ? "" : e.target.innerText;
            list.classList.remove('show');
            fetchRekapMob(); // Otomatis refresh data
        }
    });
}

function toggleFilter(id) {
    const el = document.getElementById(id);
    if(el.classList.contains('hidden')) {
        el.classList.remove('hidden'); el.classList.add('flex');
    } else {
        el.classList.add('hidden'); el.classList.remove('flex');
    }
}

// --- INIT ---
window.addEventListener('DOMContentLoaded', async () => {
    const d = await getLastHarianData(); 
    document.getElementById('harian_date_mob').value = d ? d.last_created : new Date().toISOString().split('T')[0];
    await populateKantorOptionsMob();
    fetchRekapMob();
});

async function getLastHarianData(){
    try{ const r=await apiCall(API_DATE); return (await r.json()).data; } catch{ return null; }
}

async function populateKantorOptionsMob(){
    const optKantor = document.getElementById('opt_kantor_mob');
    try {
        const res = await apiCall(API_KODE, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({type:'kode_kantor'}) });
        const json = await res.json();
        const list = Array.isArray(json.data) ? json.data : [];
        let html = `<option value="">KONSOLIDASI (SEMUA)</option>`;
        list.filter(x => x.kode_kantor && x.kode_kantor !== '000')
            .sort((a,b) => String(a.kode_kantor).localeCompare(b.kode_kantor))
            .forEach(it => { html += `<option value="${String(it.kode_kantor).padStart(3,'0')}">${String(it.kode_kantor).padStart(3,'0')} - ${it.nama_kantor}</option>`; });
        optKantor.innerHTML = html;
    } catch(e){ optKantor.innerHTML = `<option value="">Error Load</option>`; }
}

// --- 1. FETCH REKAP MOB ---
async function fetchRekapMob(){
    const loading = document.getElementById('loadingMob');
    const tbody  = document.getElementById('bodyMatrix');
    const harian  = document.getElementById('harian_date_mob').value;
    const kode    = document.getElementById('opt_kantor_mob').value || null; 
    const rekapBy = document.getElementById('opt_rekap_by').value;
    const kankas  = document.getElementById('val_kankas_main').value || null;
    const ao      = document.getElementById('val_ao_main').value || null;

    if(abortMainMob) abortMainMob.abort();
    abortMainMob = new AbortController();

    loading.classList.remove('hidden');
    tbody.innerHTML = `<tr><td colspan="11" class="py-20 text-center text-slate-400 italic text-[10px] md:text-sm">Sedang mengambil data...</td></tr>`;
    rekapDataCacheMob = null;

    // Ganti Label Kiri Sesuai Filter
    let labelTh = "Bulan Real";
    if(rekapBy === 'ao') labelTh = "Nama AO Kredit";
    if(rekapBy === 'kankas') labelTh = "Kantor Kas";
    document.getElementById('th_grouping_label').innerText = labelTh;

    try {
        const payload = { 
            type: "mob_vintage", harian_date: harian, rekap_by: rekapBy,
            kode_kantor: kode, kode_kankas: kankas, kode_ao: ao
        };
        
        const res = await apiCall(API_URL, {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload), signal: abortMainMob.signal
        });
        const json = await res.json();
        
        if(json.status !== 200) throw new Error(json.message);

        // Populate Searchable Dropdowns Kankas & AO dari respon Backend
        if(json.data.dropdown_lists) {
            setupSearchableDropdown('search_kankas_main', 'val_kankas_main', 'list_kankas_main', json.data.dropdown_lists.list_kankas || []);
            setupSearchableDropdown('search_ao_main', 'val_ao_main', 'list_ao_main', json.data.dropdown_lists.list_ao || []);
        }

        const displayData = json.data.data || [];
        const bucketsKey = json.data.buckets_order || ['0', '1 - 7', '8 - 14', '15 - 21', '22 - 30', '31 - 60', '61 - 90', '> 90'];

        if(displayData.length === 0){
            tbody.innerHTML = `<tr><td colspan="11" class="py-20 text-center text-slate-400 italic text-[10px] md:text-sm">Tidak ada data.</td></tr>`;
            document.getElementById('rowTotalMobAtas').innerHTML = '';
            return;
        }

        rekapDataCacheMob = { data: displayData, buckets: bucketsKey };

        let html = '';
        let grandTotal = { plafond: 0, buckets: {} };
        bucketsKey.forEach(b => grandTotal.buckets[b] = { os:0, noa:0 });

        displayData.forEach(r => {
            grandTotal.plafond += parseFloat(r.total_plafond || 0);
            let cells = '';
            
            bucketsKey.forEach(key => {
                const bData = r.buckets[key] || { pct:0, noa:0, os:0 };
                grandTotal.buckets[key].os  += parseFloat(bData.os || 0);
                grandTotal.buckets[key].noa += parseInt(bData.noa || 0);

                let bgClass = 'bg-transparent'; let textClass = 'text-slate-800';
                if(key !== '0' && parseFloat(bData.pct) > 0) { bgClass = 'bg-red-50/70 border-red-100'; textClass = 'text-red-700'; }
                if(key === '0' && parseFloat(bData.pct) > 90) { bgClass = 'bg-emerald-50/70 border-emerald-100'; textClass = 'text-emerald-700'; }

                const cabangParam = (!kode) ? '' : kode; // Kirim filter cabang ke detail
                const clickEv = (parseFloat(bData.os) > 0) ? `onclick="openModalMob('${cabangParam}', '${r.group_id}', '${key}', '${rekapBy}')"` : '';
                const cursor = (parseFloat(bData.os) > 0) ? 'cell-hover' : '';

                cells += `
                    <td class="px-2 md:px-3 py-1.5 md:py-2 border-r border-slate-200 align-middle ${bgClass}">
                        <div class="flex flex-col justify-center h-full ${cursor} transition px-1 rounded" ${clickEv}>
                            <div class="font-bold text-[10px] md:text-xs ${textClass} leading-tight mb-0.5">${parseFloat(bData.os)>0 ? fmt(bData.os) : '-'}</div>
                            <div class="text-[8px] md:text-[9.5px] text-slate-500 font-medium leading-tight">NOA: <span class="font-bold text-slate-700">${bData.noa}</span> <span class="mx-0.5 opacity-50">|</span> <span class="font-bold ${textClass}">${bData.pct}%</span></div>
                        </div>
                    </td>`;
            });

            const txtMob = r.mob ? r.mob : '-';

            html += `
                <tr class="hover:bg-slate-50 border-b border-slate-200 group h-[52px] md:h-[60px]">
                    <td class="sticky-left px-3 md:px-4 py-2 text-left font-bold text-[10.5px] md:text-sm text-slate-700 bg-white border-r border-slate-200 align-middle shadow-[inset_-1px_0_0_#e2e8f0] z-10 min-w-[120px] md:min-w-[180px] truncate" title="${r.group_name}">${r.group_name}</td>
                    <td class="px-2 md:px-3 py-2 border-r border-slate-200 text-center font-bold text-[10.5px] md:text-sm text-slate-500 bg-slate-50 align-middle">${txtMob}</td>
                    <td class="px-3 md:px-4 py-2 border-r border-slate-200 text-right font-mono font-bold text-[11px] md:text-sm text-slate-700 bg-slate-50 align-middle leading-tight">${fmt(r.total_plafond)}</td>
                    ${cells}
                </tr>`;
        });
        tbody.innerHTML = html;

        // --- RENDER TOTAL STICKY ---
        let tf = `<th class="sticky-left px-3 md:px-4 text-left uppercase tracking-widest align-middle text-blue-900 z-50 bg-[#eff6ff] text-[10px] md:text-xs shadow-[inset_-1px_0_0_#93c5fd]">TOTAL</th>
                  <th class="border-r border-blue-300 px-2 md:px-3 text-center align-middle text-blue-900 bg-[#eff6ff]">-</th>
                  <th class="border-r border-blue-300 px-3 md:px-4 text-right font-mono font-bold text-[11px] md:text-[14px] text-blue-900 align-middle bg-[#eff6ff] leading-tight">${fmt(grandTotal.plafond)}</th>`;
        
        bucketsKey.forEach(b => { 
            const bTot = grandTotal.buckets[b];
            const pembagiTotal = grandTotal.plafond > 0 ? grandTotal.plafond : 1;
            const pctTotal = ((bTot.os / pembagiTotal) * 100).toFixed(2);
            tf += `<th class="border-r border-blue-300 align-middle bg-[#eff6ff] px-2 md:px-3">
                      <div class="flex flex-col justify-center h-full py-1.5 md:py-2">
                          <div class="text-[10px] md:text-xs text-blue-900 font-bold leading-tight mb-0.5">${fmt(bTot.os)}</div>
                          <div class="text-[8px] md:text-[9.5px] text-blue-600 font-medium leading-tight">NOA: <span class="font-bold text-blue-800">${bTot.noa}</span> <span class="mx-0.5 opacity-50">|</span> <span class="font-bold">${pctTotal}%</span></div>
                      </div>
                   </th>` 
        });
        document.getElementById('rowTotalMobAtas').innerHTML = tf;

    } catch(err) {
        if(err.name !== 'AbortError') tbody.innerHTML = `<tr><td colspan="11" class="py-16 text-center text-red-500 font-bold tracking-widest uppercase text-[10px] md:text-sm">Error: ${err.message}</td></tr>`;
    } finally { loading.classList.add('hidden'); }
}

window.exportExcelRekapMob = function() {
    if(!rekapDataCacheMob || !rekapDataCacheMob.data) return alert("Tidak ada data rekap untuk didownload.");
    const rows = rekapDataCacheMob.data;
    const bk = rekapDataCacheMob.buckets;
    let csv = "Group Name\tMOB\tTotal Plafond\t";
    bk.forEach(b => csv += `% ${b}\tOS ${b}\tNOA ${b}\t`);
    csv += "\n";

    rows.forEach(r => {
        csv += `'${r.group_name}\t${r.mob||'-'}\t${Math.round(r.total_plafond)}\t`;
        bk.forEach(b => {
            const d = r.buckets[b];
            csv += `${d.pct}%\t${Math.round(d.os)}\t${d.noa}\t`;
        });
        csv += "\n";
    });

    const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
    const a = document.createElement('a');
    a.href = window.URL.createObjectURL(blob);
    a.download = `Rekap_MOB_${document.getElementById("harian_date_mob").value}.xls`; 
    a.click();
}

// --- 2. MODAL DETAIL LOGIC ---
async function openModalMob(cabang, groupId, bucket, rekapBy){
    // Sesuaikan parameter berdasarkan filter grup
    detailParamsMob = {
        type: "detail_mob_debitur",
        harian_date: document.getElementById('harian_date_mob').value,
        kode_kantor: cabang, 
        bucket_label: bucket
    };

    // Mapping ulang value yang diklik
    if(rekapBy === 'bulan') detailParamsMob.bulan_realisasi = groupId;
    if(rekapBy === 'ao') detailParamsMob.kode_ao = groupId;
    if(rekapBy === 'kankas') detailParamsMob.kode_kankas = groupId;

    detailPageMob = 1;

    document.getElementById('modalDetailMob').classList.remove('hidden');
    document.getElementById('badgeBucketDetail').innerText = `Bucket ${bucket}`;
    document.getElementById('subTitleDetail').innerText = `Grup: ${groupId} • Cabang: ${cabang || 'SEMUA'}`;
    document.getElementById('search_nasabah').value = '';
    
    renderModalHeaderMigrasi();
    fetchDetailMob();
}

function renderModalHeaderMigrasi() {
    const mHead = document.getElementById('headModalMigrasi');
    mHead.innerHTML = `
        <tr>
            <th class="mod-td-rek hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 rounded-tl-xl text-left md:text-center">Rekening</th>
            <th class="mod-td-nas px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 text-left md:text-center">Nama Nasabah</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[180px] md:w-[250px] text-center">Alamat</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[110px] md:w-[130px] text-center">No HP</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[100px] md:w-[120px] text-center">AO</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[100px] md:w-[120px] text-center">Kankas</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[110px] md:w-[140px] text-center">Tgl Realisasi</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[110px] md:w-[140px] text-right">Plafond</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-green-200 w-[110px] md:w-[140px] text-right bg-green-50 text-green-700">OS Current</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[50px] md:w-[60px] text-center">Kol</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-red-200 w-[100px] md:w-[130px] text-right bg-red-50 text-red-800">Tot Tunggakan</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-green-200 w-[110px] md:w-[140px] text-right bg-green-50 text-green-800">Total Bayar</th>
            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[110px] md:w-[140px] text-right">Tabungan</th>
            <th class="px-2 md:px-3 py-1.5 md:py-2 border-b border-slate-200 w-[90px] md:w-[100px] text-center">Stat Tab</th>
        </tr>
    `;
}

window.filterTableDetail = function() {
    const filter = document.getElementById("search_nasabah").value.toLowerCase();
    const trs = document.getElementById("bodyModalDetail").getElementsByTagName("tr");
    for (let i = 0; i < trs.length; i++) {
        const tdName = trs[i].getElementsByTagName("td")[1];
        if (tdName) {
            trs[i].style.display = (tdName.textContent || tdName.innerText).toLowerCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}

async function fetchDetailMob(){
    const loader = document.getElementById('loadingModal');
    const tbody  = document.getElementById('bodyModalDetail');
    
    loader.classList.remove('hidden');
    tbody.innerHTML = '';

    try {
        const payload = { ...detailParamsMob, page: detailPageMob };
        const res = await apiCall(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
        const json = await res.json();
        
        if(json.status !== 200) throw new Error(json.message);

        const list = json.data?.data || [];
        const totalRecords = json.data?.total_records || 0;
        const totalPages   = json.data?.total_pages || 1;

        if(list.length === 0){
            tbody.innerHTML = `<tr><td colspan="14" class="py-20 text-center text-slate-400 italic text-[10px] md:text-sm">Tidak ada data detail.</td></tr>`;
            return;
        }

        let html = '';
        list.forEach(row => {
            let statTabungan = parseFloat(row.tabungan) >= (1.5 * parseFloat(row.totung)) 
                ? `<span class="text-green-600 font-bold text-[9px] md:text-xs">Aman</span>` 
                : `<span class="text-red-500 font-bold text-[9px] md:text-xs">Belum Aman</span>`;

            let alamatPendek = row.alamat && row.alamat.length > 25 ? row.alamat.substring(0, 25) + '...' : (row.alamat||'-');

            html += `
                <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[40px] md:h-[48px] group">
                    <td class="mod-td-rek hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 font-mono text-[9.5px] md:text-[11px] text-slate-500 border-r border-slate-100">${row.no_rekening}</td>
                    <td class="mod-td-nas px-2 md:px-4 py-1.5 md:py-2 font-bold text-[9.5px] md:text-[11px] text-slate-700 truncate border-r border-slate-100" title="${row.nama_nasabah}">${row.nama_nasabah}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-slate-500 text-[9.5px] md:text-[11px] border-r border-slate-100 whitespace-nowrap text-center" title="${row.alamat}">${alamatPendek}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-slate-700 text-[9px] md:text-[11px] border-r border-slate-100">${row.no_hp||'-'}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-[9px] md:text-[11px] text-slate-500 border-r border-slate-100 truncate max-w-[100px]" title="${row.nama_ao}">${row.nama_ao||'-'}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-[9px] md:text-[11px] text-slate-500 border-r border-slate-100 truncate max-w-[100px]" title="${row.nama_kankas}">${row.nama_kankas||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-center font-mono text-[9.5px] md:text-[11px] text-blue-700 bg-blue-50/30 border-r border-blue-100">${row.tgl_realisasi}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-medium text-[9.5px] md:text-[12px] text-slate-500 border-r border-slate-100">${fmt(row.plafond)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-mono font-bold text-[9.5px] md:text-[13px] text-blue-700 border-r border-slate-100 bg-slate-50/50">${fmt(row.os)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center font-bold text-[9.5px] md:text-sm text-slate-600 border-r border-slate-100">${row.kolektibilitas||'-'}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-mono font-bold text-[9.5px] md:text-sm text-red-600 bg-red-50/30 border-r border-red-100">${fmt(row.totung)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-mono font-bold text-[9.5px] md:text-[12px] text-green-700 bg-green-50/30 border-r border-green-100">${fmt(row.transaksi)}</td>
                    <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-mono font-bold text-[9.5px] md:text-[12px] text-emerald-600 bg-emerald-50/10 border-r border-slate-100">${fmt(row.tabungan)}</td>
                    <td class="px-2 md:px-3 py-1.5 md:py-2 text-center">${statTabungan}</td>
                </tr>`;
        });
        tbody.innerHTML = html;

        document.getElementById('pageInfoDetail').innerText = `Hal ${detailPageMob} dari ${totalPages} (${fmt(totalRecords)} Data)`;
        document.getElementById('btnPrevDetail').disabled = detailPageMob <= 1;
        document.getElementById('btnNextDetail').disabled = detailPageMob >= totalPages;
        filterTableDetail();

    } catch(e){
        tbody.innerHTML = `<tr><td colspan="14" class="py-16 text-center text-red-500 font-bold uppercase tracking-widest">Gagal mengambil detail.</td></tr>`;
    } finally { loader.classList.add('hidden'); }
}

window.changePageDetailMob = function(step) { detailPageMob += step; fetchDetailMob(); }

window.exportExcelDetailMob = async function() {
    const btn = event.target.closest('button'); const txt = btn.innerHTML;
    btn.innerHTML = `...`; btn.disabled = true;

    try {
        const payload = { ...detailParamsMob, page: 1, limit: 10000 };
        const res = await apiCall(API_URL, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
        const json = await res.json();
        const rows = json.data?.data || [];
        
        if(rows.length === 0) return alert("Tidak ada data");

        let csv = `No Rekening\tNama Nasabah\tAO\tKankas\tTgl Realisasi\tPlafond\tBaki Debet\tKol\tTot Tunggakan\tTabungan\n`;
        rows.forEach(x => {
            csv += `'${x.no_rekening}\t${x.nama_nasabah}\t${x.nama_ao||''}\t${x.nama_kankas||''}\t${x.tgl_realisasi}\t${Math.round(x.plafond)}\t${Math.round(x.os)}\t${x.kolektibilitas||''}\t${Math.round(x.totung)}\t${Math.round(x.tabungan)}\n`;
        });

        const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
        const a = document.createElement('a'); a.href = window.URL.createObjectURL(blob);
        a.download = `Detail_MOB.xls`; a.click();
    } catch(e) { alert("Gagal export data."); } finally { btn.innerHTML = txt; btn.disabled = false; }
}

window.closeModalMob = function(){ document.getElementById('modalDetailMob').classList.add('hidden'); }
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModalMob(); });
</script>
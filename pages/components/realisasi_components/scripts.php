<script>
// =========================================================================
// KONFIGURASI GLOBAL & UTILS
// =========================================================================
const API_URL  = './api/kredit/';
const API_KODE = './api/kode/';
const API_DATE = './api/date/';

const nfID = new Intl.NumberFormat('id-ID');
const fmt = n => nfID.format(Math.round(Number(n || 0)));

const apiCall = (url, opt = {}) => {
    return window.apiFetch ? window.apiFetch(url, opt) : fetch(url, opt);
};

let abortMain;
let abortDetail;

let rekapDataCache = [];
let rekapGtCache = null;
let userKodeGlobal = '000';

let detailDataCache = [];
let detailAllDataCache = [];
let detailParamsReal = {};
let detailPageReal = 1;
let detailAoOptionsLoaded = false;
let detailSearchTimer = null;

const DETAIL_PAGE_LIMIT = 50;

let sortCol = '';
let sortAsc = true;

let sortDetailCol = '';
let sortDetailAsc = true;


// =========================================================================
// SAFE HELPER
// =========================================================================
function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function escapeJs(value) {
    return String(value ?? '')
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'")
        .replace(/"/g, '&quot;')
        .replace(/\n/g, ' ')
        .replace(/\r/g, ' ');
}

function escapeExcel(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function toggleFilter(id) {
    const el = document.getElementById(id);
    if (!el) return;

    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        el.classList.add('flex');
    } else {
        el.classList.add('hidden');
        el.classList.remove('flex');
    }
}

function getCurrentAreaState() {
    const areaVal = document.getElementById('opt_area')?.value || 'ALL';
    const subVal  = document.getElementById('opt_sub_main')?.value || 'ALL';

    return { areaVal, subVal };
}

function normalizeKodeKantor(value) {
    if (!value || value === 'ALL' || value === '000') return '000';
    return String(value).replace('CAB-', '').padStart(3, '0');
}


// =========================================================================
// ICON SORT
// =========================================================================
const getSortIcon = (col) => {
    if (col !== sortCol) return ' <span class="opacity-30 text-[8px] ml-1">↕</span>';
    return sortAsc
        ? ' <span class="text-blue-600 text-[10px] ml-1">▲</span>'
        : ' <span class="text-blue-600 text-[10px] ml-1">▼</span>';
};

const getSortIconDetail = (col) => {
    if (col !== sortDetailCol) return ' <span class="opacity-30 text-[8px] ml-1">↕</span>';
    return sortDetailAsc
        ? ' <span class="text-blue-600 text-[10px] ml-1">▲</span>'
        : ' <span class="text-blue-600 text-[10px] ml-1">▼</span>';
};


// =========================================================================
// INIT
// =========================================================================
window.addEventListener('DOMContentLoaded', async () => {
    const user = (window.getUser && window.getUser()) || null;

    userKodeGlobal = user?.kode ? String(user.kode).padStart(3, '0') : '000';
    if (userKodeGlobal === '099') userKodeGlobal = '000';

    const d = await getLastHarianData();

    if (d) {
        document.getElementById('closing_date').value = d.last_closing;
        document.getElementById('harian_date').value  = d.last_created;
    } else {
        const now = new Date();
        document.getElementById('closing_date').value = `${now.getFullYear() - 1}-12-31`;
        document.getElementById('harian_date').value  = now.toISOString().split('T')[0];
    }

    await populateAreaDropdown();
    updateFilterUI();
});

async function getLastHarianData() {
    try {
        const r = await apiCall(API_DATE);
        const j = await r.json();
        return j.data || null;
    } catch {
        return null;
    }
}


// =========================================================================
// FILTER AREA / CABANG / KANKAS
// =========================================================================
async function populateAreaDropdown() {
    const optArea = document.getElementById('opt_area');
    if (!optArea) return;

    if (userKodeGlobal !== '000') {
        optArea.innerHTML = `<option value="CAB-${userKodeGlobal}">CABANG ${userKodeGlobal}</option>`;
        optArea.value = `CAB-${userKodeGlobal}`;
        optArea.disabled = true;
        return;
    }

    try {
        const res = await apiCall(API_KODE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type: 'kode_kantor' })
        });

        const json = await res.json();

        let html = `<option value="ALL">ALL KONSOLIDASI</option>`;

        (json.data || [])
            .filter(x => x.kode_kantor && x.kode_kantor !== '000')
            .sort((a, b) => String(a.kode_kantor).localeCompare(String(b.kode_kantor)))
            .forEach(it => {
                const kode = String(it.kode_kantor).padStart(3, '0');
                html += `<option value="CAB-${kode}">${kode} - ${escapeHtml(it.nama_kantor)}</option>`;
            });

        optArea.innerHTML = html;
    } catch (e) {
        optArea.innerHTML = `<option value="ALL">Error Load Area</option>`;
    }
}

async function updateFilterUI() {
    const areaVal = document.getElementById('opt_area')?.value || 'ALL';
    const lblSub  = document.getElementById('lbl_sub');
    const optSub  = document.getElementById('opt_sub_main');

    if (!lblSub || !optSub) return;

    if (areaVal === 'ALL') {
        lblSub.innerText = "KORWIL";
        optSub.innerHTML = `
            <option value="ALL">ALL KORWIL</option>
            <option value="SEMARANG">SEMARANG</option>
            <option value="SOLO">SOLO</option>
            <option value="BANYUMAS">BANYUMAS</option>
            <option value="PEKALONGAN">PEKALONGAN</option>
        `;

        fetchRekap();
    } else {
        lblSub.innerText = "KANKAS";
        optSub.innerHTML = `<option value="ALL">Memuat...</option>`;

        const cabang = areaVal.replace('CAB-', '');

        try {
            const r = await apiCall(API_KODE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    type: 'kode_kankas',
                    kode_kantor: cabang
                })
            });

            const j = await r.json();

            let html = `<option value="ALL">ALL KANKAS</option>`;

            if (j.data && Array.isArray(j.data)) {
                j.data.forEach(x => {
                    html += `<option value="${escapeHtml(x.kode_group1)}">${escapeHtml(x.kode_group1)} - ${escapeHtml(x.deskripsi_group1 || x.kode_group1)}</option>`;
                });
            }

            optSub.innerHTML = html;
        } catch (e) {
            optSub.innerHTML = `<option value="ALL">ALL KANKAS</option>`;
        }

        fetchRekap();
    }
}


// =========================================================================
// FETCH REKAP
// =========================================================================
async function fetchRekap() {
    const loading = document.getElementById('loadingUtama');
    const tbody   = document.getElementById('bodyUtama');

    const harian  = document.getElementById('harian_date')?.value || '';
    const closing = document.getElementById('closing_date')?.value || '';
    const areaVal = document.getElementById('opt_area')?.value || 'ALL';
    const subVal  = document.getElementById('opt_sub_main')?.value || 'ALL';

    if (!tbody) return;

    if (abortMain) abortMain.abort();
    abortMain = new AbortController();

    loading?.classList.remove('hidden');
    tbody.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-slate-400 italic">Sedang mengambil data...</td></tr>`;

    rekapDataCache = [];
    rekapGtCache = null;
    sortCol = '';
    sortAsc = true;

    try {
        let payload = {
            type: "rekap_realisasi_growth",
            closing_date: closing,
            harian_date: harian
        };

        if (areaVal === 'ALL') {
            if (subVal !== 'ALL') payload.korwil = subVal;
        } else {
            payload.kode_kantor = areaVal.replace('CAB-', '');
            if (subVal !== 'ALL') payload.kode_kankas = subVal;
        }

        const res = await apiCall(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
            signal: abortMain.signal
        });

        const json = await res.json();

        if (json.status !== 200) throw new Error(json.message || 'Gagal mengambil rekap');

        rekapDataCache = json.data?.data || json.data || [];
        rekapGtCache   = json.data?.grand_total || json.grand_total || {};

        renderHeaderClickable();
        processAndRenderTable(rekapDataCache, rekapGtCache);
    } catch (err) {
        if (err.name !== 'AbortError') {
            tbody.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-red-500 font-bold">Error: ${escapeHtml(err.message)}</td></tr>`;
        }
    } finally {
        loading?.classList.add('hidden');
    }
}


// =========================================================================
// HEADER REKAP
// =========================================================================
function renderHeaderClickable() {
    const thead = document.getElementById('headUtama');
    if (!thead) return;

    thead.innerHTML = `
        <tr class="head-lapis-1">
            <th rowspan="2" class="freeze-col-1 hidden md:table-cell w-[50px] md:w-[60px] uppercase align-middle border-r border-slate-200 text-center cursor-pointer hover:bg-slate-200 transition" onclick="sortMainData('kode_kantor')">
                <div class="flex items-center justify-center">KODE${getSortIcon('kode_kantor')}</div>
            </th>

            <th rowspan="2" class="freeze-col-2 w-[120px] md:w-[150px] uppercase align-middle border-r border-slate-200 text-left pl-3 cursor-pointer hover:bg-slate-200 transition" onclick="sortMainData('nama_kantor')">
                <div class="flex items-center">NAMA AREA${getSortIcon('nama_kantor')}</div>
            </th>

            <th colspan="2" class="py-1.5 border-b border-r border-slate-200 text-blue-800 bg-blue-50/40">REALISASI BARU</th>
            <th colspan="2" class="py-1.5 border-b border-r border-slate-200 text-purple-800 bg-purple-50/40">RESTRUKTURISASI</th>
            <th colspan="3" class="py-1.5 border-b border-slate-200 text-orange-800 bg-orange-50/40">RUN OFF (PENGURANGAN)</th>

            <th rowspan="2" class="w-[100px] md:w-[130px] border-l border-slate-200 align-middle text-right pr-3 text-slate-900 bg-slate-100/70 cursor-pointer hover:bg-slate-200 transition" onclick="sortMainData('growth')">
                <div class="flex items-center justify-end">GROWTH NET${getSortIcon('growth')}</div>
            </th>
        </tr>

        <tr class="head-lapis-2 text-[8.5px] md:text-[10px]">
            <th class="px-1 py-1 border-r border-slate-200 w-[45px] md:w-[60px] text-blue-700 cursor-pointer hover:bg-blue-100 transition" onclick="sortMainData('noa_realisasi')">NOA${getSortIcon('noa_realisasi')}</th>
            <th class="px-2 py-1 border-r border-slate-200 w-[100px] md:w-[125px] text-right text-blue-700 cursor-pointer hover:bg-blue-100 transition" onclick="sortMainData('total_realisasi')">NOMINAL${getSortIcon('total_realisasi')}</th>

            <th class="px-1 py-1 border-r border-slate-200 w-[45px] md:w-[60px] text-purple-700 cursor-pointer hover:bg-purple-100 transition" onclick="sortMainData('noa_restruck')">NOA${getSortIcon('noa_restruck')}</th>
            <th class="px-2 py-1 border-r border-slate-200 w-[100px] md:w-[125px] text-right text-purple-700 cursor-pointer hover:bg-purple-100 transition" onclick="sortMainData('total_restruck')">NOMINAL${getSortIcon('total_restruck')}</th>

            <th class="px-2 py-1 border-r border-slate-200 w-[95px] md:w-[120px] text-right text-emerald-700 cursor-pointer hover:bg-orange-100 transition" onclick="sortMainData('pelunasan')">PELUNASAN${getSortIcon('pelunasan')}</th>
            <th class="px-2 py-1 border-r border-slate-200 w-[95px] md:w-[120px] text-right text-blue-700 cursor-pointer hover:bg-orange-100 transition" onclick="sortMainData('angsuran_murni')">ANGSURAN${getSortIcon('angsuran_murni')}</th>
            <th class="px-2 py-1 border-r border-slate-200 w-[95px] md:w-[120px] text-right text-orange-700 cursor-pointer hover:bg-orange-100 transition" onclick="sortMainData('total_run_off')">TOT RUNOFF${getSortIcon('total_run_off')}</th>
        </tr>

        <tr id="rowTotalAtas" class="mob-row-tot text-[9px] md:text-xs font-extrabold tracking-wide"></tr>
    `;
}


// =========================================================================
// SORT REKAP
// =========================================================================
window.sortMainData = function(column) {
    if (!rekapDataCache || rekapDataCache.length === 0) return;

    if (sortCol === column) {
        sortAsc = !sortAsc;
    } else {
        sortCol = column;
        sortAsc = true;
    }

    rekapDataCache.sort((a, b) => {
        let fieldA = a[column];
        let fieldB = b[column];

        if (!isNaN(parseFloat(fieldA)) && isFinite(fieldA)) {
            return sortAsc
                ? parseFloat(fieldA || 0) - parseFloat(fieldB || 0)
                : parseFloat(fieldB || 0) - parseFloat(fieldA || 0);
        }

        fieldA = String(fieldA || '').toLowerCase();
        fieldB = String(fieldB || '').toLowerCase();

        if (fieldA < fieldB) return sortAsc ? -1 : 1;
        if (fieldA > fieldB) return sortAsc ? 1 : -1;
        return 0;
    });

    renderHeaderClickable();
    processAndRenderTable(rekapDataCache, rekapGtCache);
};


// =========================================================================
// RENDER REKAP + GRAND TOTAL CLICKABLE
// =========================================================================
function processAndRenderTable(rows, gt) {
    const tbody = document.getElementById('bodyUtama');
    if (!tbody) return;

    if (!rows || rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-slate-400 italic">Tidak ada transaksi.</td></tr>`;
        const rowTotal = document.getElementById('rowTotalAtas');
        if (rowTotal) rowTotal.innerHTML = '';
        return;
    }

    let html = '';

    rows.forEach(r => {
        let nGrowth = parseFloat(r.growth || 0);
        let gColor = nGrowth >= 0 ? 'text-blue-700 bg-blue-50/20' : 'text-red-600 bg-red-50/20';

        const curReal = parseInt(r.noa_realisasi || 0) > 0 ? 'cursor-pointer hover:bg-blue-100' : '';
        const clkReal = parseInt(r.noa_realisasi || 0) > 0
            ? `onclick="openDetailModal('${escapeJs(r.kode_kantor)}', 110, '${escapeJs(r.nama_kantor)}')"`
            : '';

        const curRes = parseInt(r.noa_restruck || 0) > 0 ? 'cursor-pointer hover:bg-purple-100' : '';
        const clkRes = parseInt(r.noa_restruck || 0) > 0
            ? `onclick="openDetailModal('${escapeJs(r.kode_kantor)}', 109, '${escapeJs(r.nama_kantor)}')"`
            : '';

        html += `
            <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[40px] md:h-[46px]">
                <td class="freeze-col-1 hidden md:table-cell text-center font-mono font-bold text-slate-500 border-r border-slate-100">${escapeHtml(r.kode_kantor || '-')}</td>
                <td class="freeze-col-2 text-left font-bold text-slate-700 truncate pl-3 border-r border-slate-100" title="${escapeHtml(r.nama_kantor)}">${escapeHtml(r.nama_kantor)}</td>

                <td class="text-center text-blue-700 bg-blue-50/10 font-bold border-r border-slate-100 transition ${curReal}" ${clkReal}>${fmt(r.noa_realisasi)}</td>
                <td class="text-right text-blue-800 bg-blue-50/10 font-mono pr-2 border-r border-slate-200 transition ${curReal}" ${clkReal}>${fmt(r.total_realisasi)}</td>

                <td class="text-center text-purple-700 bg-purple-50/10 font-bold border-r border-slate-100 transition ${curRes}" ${clkRes}>${fmt(r.noa_restruck || 0)}</td>
                <td class="text-right text-purple-800 bg-purple-50/10 font-mono pr-2 border-r border-slate-200 transition ${curRes}" ${clkRes}>${fmt(r.total_restruck || 0)}</td>

                <td class="text-right text-emerald-700 font-mono pr-2 border-r border-slate-100">${fmt(r.pelunasan)}</td>
                <td class="text-right text-blue-700 font-mono pr-2 border-r border-slate-100">${fmt(r.angsuran_murni)}</td>
                <td class="text-right text-orange-700 bg-orange-50/10 font-mono pr-2 border-r border-slate-200">${fmt(r.total_run_off)}</td>
                <td class="text-right font-mono font-extrabold pr-3 ${gColor}">${fmt(nGrowth)}</td>
            </tr>
        `;
    });

    tbody.innerHTML = html;

    if (!gt) return;

    let gtGrowth = parseFloat(gt.growth || 0);
    let gtColor = gtGrowth >= 0 ? 'text-blue-800 bg-blue-100/40' : 'text-red-700 bg-red-100/40';

    const gtRealClickable = parseInt(gt.noa_realisasi || 0) > 0 ? 'cursor-pointer hover:bg-blue-200' : '';
    const gtResClickable  = parseInt(gt.noa_restruck || 0) > 0 ? 'cursor-pointer hover:bg-purple-200' : '';

    const gtRealClick = parseInt(gt.noa_realisasi || 0) > 0
        ? `onclick="openDetailModal('ALL', 110, 'GRAND TOTAL')"`
        : '';

    const gtResClick = parseInt(gt.noa_restruck || 0) > 0
        ? `onclick="openDetailModal('ALL', 109, 'GRAND TOTAL')"`
        : '';

    const rowTotalAtas = document.getElementById('rowTotalAtas');
    if (!rowTotalAtas) return;

    rowTotalAtas.innerHTML = `
        <th class="freeze-col-1 hidden md:table-cell text-center text-blue-900 font-extrabold border-r border-blue-300">ALL</th>
        <th class="freeze-col-2 text-left font-extrabold text-blue-900 pl-3 border-r border-blue-300">GRAND TOTAL</th>

        <th class="text-center text-blue-900 bg-blue-100/30 font-extrabold border-r border-blue-300 transition ${gtRealClickable}" ${gtRealClick}>${fmt(gt.noa_realisasi)}</th>
        <th class="text-right text-blue-900 bg-blue-100/30 font-mono font-bold pr-2 border-r border-blue-300 transition ${gtRealClickable}" ${gtRealClick}>${fmt(gt.total_realisasi)}</th>

        <th class="text-center text-purple-900 bg-purple-100/30 font-extrabold border-r border-blue-300 transition ${gtResClickable}" ${gtResClick}>${fmt(gt.noa_restruck || 0)}</th>
        <th class="text-right text-purple-900 bg-purple-100/30 font-mono font-bold pr-2 border-r border-blue-300 transition ${gtResClickable}" ${gtResClick}>${fmt(gt.total_restruck || 0)}</th>

        <th class="text-right text-emerald-800 font-mono font-bold pr-2 border-r border-blue-300">${fmt(gt.pelunasan)}</th>
        <th class="text-right text-blue-800 font-mono font-bold pr-2 border-r border-blue-300">${fmt(gt.angsuran_murni)}</th>
        <th class="text-right text-orange-900 bg-orange-100/30 font-mono font-bold pr-2 border-r border-blue-300">${fmt(gt.total_run_off)}</th>
        <th class="text-right font-mono font-black pr-3 ${gtColor}">${fmt(gtGrowth)}</th>
    `;
}


// =========================================================================
// EXPORT REKAP
// =========================================================================
window.exportExcelRekap = function() {
    if (rekapDataCache.length === 0) return alert("Tidak ada data untuk diexport.");

    let csv = "Kode\tNama Kantor\tNOA Realisasi\tNominal Realisasi\tNOA Restruck\tNominal Restruck\tPelunasan\tAngsuran Murni\tTotal Run Off\tGrowth Net\n";

    rekapDataCache.forEach(r => {
        csv += `'${r.kode_kantor}\t${r.nama_kantor}\t${r.noa_realisasi}\t${Math.round(r.total_realisasi)}\t${r.noa_restruck || 0}\t${Math.round(r.total_restruck || 0)}\t${Math.round(r.pelunasan)}\t${Math.round(r.angsuran_murni)}\t${Math.round(r.total_run_off)}\t${Math.round(r.growth)}\n`;
    });

    const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
    const a = document.createElement('a');

    a.href = window.URL.createObjectURL(blob);
    a.download = `Rekap_Growth_Kredit.xls`;
    a.click();

    URL.revokeObjectURL(a.href);
};


// =========================================================================
// MODAL DETAIL
// =========================================================================
window.openDetailModal = function(kode_area, kode_trans, nama_area) {
    const { areaVal, subVal } = getCurrentAreaState();

    detailParamsReal = {
        type: "detail_realisasi_growth",
        closing_date: document.getElementById('closing_date')?.value || '',
        harian_date: document.getElementById('harian_date')?.value || '',
        kode_trans: String(kode_trans)
    };

    if (areaVal === 'ALL') {
        if (subVal !== 'ALL') {
            detailParamsReal.korwil = subVal;
        }

        if (kode_area && kode_area !== 'ALL' && String(kode_area).length === 3) {
            detailParamsReal.kode_kantor = kode_area;
        }
    } else {
        detailParamsReal.kode_kantor = areaVal.replace('CAB-', '');

        if (kode_area && kode_area !== 'ALL') {
            detailParamsReal.kode_kankas = kode_area;
        }
    }

    detailPageReal = 1;
    sortDetailCol = '';
    sortDetailAsc = true;
    detailAoOptionsLoaded = false;
    detailDataCache = [];
    detailAllDataCache = [];

    document.getElementById('modalDetailReal')?.classList.remove('hidden');

    const badge = document.getElementById('badgeBucketDetail');

    if (badge) {
        if (String(kode_trans) === '110') {
            badge.innerText = 'REALISASI BARU';
            badge.className = 'text-[9px] md:text-sm bg-blue-600 text-white px-2 py-0.5 md:px-2.5 rounded-md shadow-sm ml-1 shrink-0';
        } else {
            badge.innerText = 'RESTRUKTURISASI';
            badge.className = 'text-[9px] md:text-sm bg-purple-600 text-white px-2 py-0.5 md:px-2.5 rounded-md shadow-sm ml-1 shrink-0';
        }
    }

    const subTitle = document.getElementById('subTitleDetail');
    if (subTitle) subTitle.innerText = `Area: ${nama_area}`;

    const searchInput = document.getElementById('search_nasabah');
    if (searchInput) searchInput.value = '';

    const aoSelect = document.getElementById('filter_ao_modal');
    if (aoSelect) {
        aoSelect.innerHTML = `<option value="ALL">MEMUAT AO...</option>`;
        aoSelect.value = 'ALL';
    }

    fetchDetailReal(true);
};

function buildDetailPayload(extra = {}) {
    return {
        ...detailParamsReal,
        page: 1,
        limit: 10000,
        ...extra
    };
}


// =========================================================================
// HELPER DETAIL AO / SEARCH
// =========================================================================
function normalizeSearchText(value) {
    return String(value || '')
        .toLowerCase()
        .replace(/\s+/g, ' ')
        .trim();
}

function getRowAoNama(row) {
    return String(
        row.nama_ao ||
        row.nama_marketing ||
        row.marketing ||
        row.ao ||
        '-'
    ).trim();
}

function getRowAoFilterKey(row) {
    return normalizeSearchText(getRowAoNama(row));
}

function getRowTglRealisasi(row) {
    return row.tgl_realisasi || row.tanggal_realisasi || '-';
}

function getSortValueDetail(row, column) {
    if (column === 'nama_ao') return getRowAoNama(row);
    if (column === 'tgl_realisasi') return getRowTglRealisasi(row);
    return row[column];
}

function getFilteredDetailRows() {
    const aoVal = document.getElementById('filter_ao_modal')?.value || 'ALL';
    const keyword = normalizeSearchText(document.getElementById('search_nasabah')?.value || '');

    return detailAllDataCache.filter(row => {
        const rowAoKey = getRowAoFilterKey(row);

        const matchAo =
            aoVal === 'ALL' ||
            rowAoKey === aoVal;

        const searchText = normalizeSearchText([
            row.no_rekening,
            row.nama_nasabah,
            row.alamat,
            row.nama_kankas,
            getRowAoNama(row),
            getRowTglRealisasi(row),
            row.plafond
        ].join(' '));

        const matchSearch =
            keyword === '' ||
            searchText.includes(keyword);

        return matchAo && matchSearch;
    });
}


// =========================================================================
// LOAD AO OPTION MODAL - AMBIL DARI DATA DETAIL nama_ao
// =========================================================================
async function populateAoModalOptions() {
    const select = document.getElementById('filter_ao_modal');
    if (!select) return;

    const oldValue = select.value || 'ALL';
    const mapAo = new Map();

    detailAllDataCache.forEach(row => {
        const namaAo = getRowAoNama(row);
        const keyAo = getRowAoFilterKey(row);

        if (
            namaAo &&
            namaAo !== '-' &&
            keyAo &&
            keyAo !== 'null' &&
            keyAo !== 'undefined' &&
            !mapAo.has(keyAo)
        ) {
            mapAo.set(keyAo, namaAo);
        }
    });

    let html = `<option value="ALL">SEMUA AO</option>`;

    Array.from(mapAo.entries())
        .sort((a, b) => String(a[1]).localeCompare(String(b[1])))
        .forEach(([keyAo, namaAo]) => {
            html += `<option value="${escapeHtml(keyAo)}">${escapeHtml(namaAo)}</option>`;
        });

    select.innerHTML = html;

    if (oldValue !== 'ALL' && mapAo.has(oldValue)) {
        select.value = oldValue;
    } else {
        select.value = 'ALL';
    }

    detailAoOptionsLoaded = true;
}


// =========================================================================
// HEADER DETAIL
// =========================================================================
function renderDetailHeaderClickable() {
    const thead = document.getElementById('headModalDetail');
    if (!thead) return;

    thead.innerHTML = `
        <tr>
            <th class="hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 rounded-tl-xl text-left md:text-center w-[115px] cursor-pointer hover:bg-slate-200 transition" onclick="sortDetailData('no_rekening')">
                <div class="flex justify-start md:justify-center items-center">Rekening${getSortIconDetail('no_rekening')}</div>
            </th>

            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 text-left md:text-center w-[165px] md:w-[245px] cursor-pointer hover:bg-slate-200 transition" onclick="sortDetailData('nama_nasabah')">
                <div class="flex justify-start md:justify-center items-center">Nama Nasabah${getSortIconDetail('nama_nasabah')}</div>
            </th>

            <th class="hidden sm:table-cell px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[170px] md:w-[245px] text-center cursor-pointer hover:bg-slate-200 transition" onclick="sortDetailData('alamat')">
                <div class="flex justify-center items-center">Alamat${getSortIconDetail('alamat')}</div>
            </th>

            <th class="hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[105px] md:w-[120px] text-center cursor-pointer hover:bg-slate-200 transition" onclick="sortDetailData('nama_kankas')">
                <div class="flex justify-center items-center">Kankas${getSortIconDetail('nama_kankas')}</div>
            </th>

            <th class="hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 border-b border-r border-slate-300 w-[140px] md:w-[180px] text-center cursor-pointer hover:bg-slate-200 transition" onclick="sortDetailData('nama_ao')">
                <div class="flex justify-center items-center">AO${getSortIconDetail('nama_ao')}</div>
            </th>

            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-r border-slate-300 w-[95px] md:w-[135px] text-center cursor-pointer hover:bg-slate-200 transition" onclick="sortDetailData('tgl_realisasi')">
                <div class="flex justify-center items-center">Tgl Realisasi${getSortIconDetail('tgl_realisasi')}</div>
            </th>

            <th class="px-2 md:px-4 py-1.5 md:py-2 border-b border-slate-300 w-[100px] md:w-[140px] text-right cursor-pointer hover:bg-slate-200 transition" onclick="sortDetailData('plafond')">
                <div class="flex justify-end items-center">Plafond${getSortIconDetail('plafond')}</div>
            </th>
        </tr>
    `;
}


// =========================================================================
// RENDER BODY DETAIL
// =========================================================================
function renderTableDetailBody(list) {
    const tbody = document.getElementById('bodyModalDetail');
    if (!tbody) return;

    let html = '';

    list.forEach(row => {
        const alamatPendek = row.alamat && row.alamat.length > 32
            ? row.alamat.substring(0, 32) + '...'
            : (row.alamat || '-');

        const tgl = getRowTglRealisasi(row);
        const namaAo = getRowAoNama(row);

        html += `
            <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[42px] md:h-[48px] group">
                <td class="hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 font-mono text-[9.5px] md:text-[11px] text-slate-500 border-r border-slate-100">
                    ${escapeHtml(row.no_rekening || '')}
                </td>

                <td class="px-2 md:px-4 py-1.5 md:py-2 font-bold text-[9.5px] md:text-[11px] text-slate-700 truncate border-r border-slate-100" title="${escapeHtml(row.nama_nasabah || '')}">
                    <div class="truncate max-w-[150px] md:max-w-none">${escapeHtml(row.nama_nasabah || '-')}</div>
                    <div class="md:hidden text-[8.5px] text-slate-400 font-mono mt-0.5">${escapeHtml(row.no_rekening || '')}</div>
                    <div class="md:hidden text-[8.5px] text-blue-600 font-semibold mt-0.5 truncate">${escapeHtml(namaAo)}</div>
                </td>

                <td class="hidden sm:table-cell px-2 md:px-4 py-1.5 md:py-2 text-slate-500 text-[9.5px] md:text-[11px] border-r border-slate-100 whitespace-nowrap text-center" title="${escapeHtml(row.alamat || '')}">
                    ${escapeHtml(alamatPendek)}
                </td>

                <td class="hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 text-center font-mono text-[9px] md:text-[11px] text-slate-500 border-r border-slate-100 truncate max-w-[100px]" title="${escapeHtml(row.nama_kankas || '')}">
                    ${escapeHtml(row.nama_kankas || '-')}
                </td>

                <td class="hidden md:table-cell px-2 md:px-3 py-1.5 md:py-2 text-center text-[9px] md:text-[11px] text-slate-600 border-r border-slate-100 truncate max-w-[170px]" title="${escapeHtml(namaAo)}">
                    <div class="font-bold truncate">${escapeHtml(namaAo)}</div>
                </td>

                <td class="px-2 md:px-4 py-1.5 md:py-2 text-center font-mono text-[9.5px] md:text-[11px] text-blue-700 bg-blue-50/30 border-r border-blue-100">
                    ${escapeHtml(tgl)}
                </td>

                <td class="px-2 md:px-4 py-1.5 md:py-2 text-right font-medium text-[9.5px] md:text-[12px] text-slate-600 border-slate-100">
                    ${fmt(row.plafond)}
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}


// =========================================================================
// RENDER FILTERED DETAIL PAGE
// =========================================================================
function renderFilteredDetailPage() {
    let filteredRows = getFilteredDetailRows();

    if (sortDetailCol) {
        filteredRows.sort((a, b) => {
            let fieldA = getSortValueDetail(a, sortDetailCol);
            let fieldB = getSortValueDetail(b, sortDetailCol);

            if (sortDetailCol === 'plafond') {
                return sortDetailAsc
                    ? Number(fieldA || 0) - Number(fieldB || 0)
                    : Number(fieldB || 0) - Number(fieldA || 0);
            }

            fieldA = String(fieldA || '').toLowerCase();
            fieldB = String(fieldB || '').toLowerCase();

            if (fieldA < fieldB) return sortDetailAsc ? -1 : 1;
            if (fieldA > fieldB) return sortDetailAsc ? 1 : -1;
            return 0;
        });
    }

    const totalRecords = filteredRows.length;
    const totalPages = Math.max(1, Math.ceil(totalRecords / DETAIL_PAGE_LIMIT));

    if (detailPageReal > totalPages) detailPageReal = totalPages;
    if (detailPageReal < 1) detailPageReal = 1;

    const start = (detailPageReal - 1) * DETAIL_PAGE_LIMIT;
    const end = start + DETAIL_PAGE_LIMIT;

    detailDataCache = filteredRows.slice(start, end);

    renderDetailHeaderClickable();

    const tbody = document.getElementById('bodyModalDetail');
    if (!tbody) return;

    if (detailDataCache.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-20 text-center text-slate-400 italic text-[10px] md:text-sm">
                    Tidak ada data detail.
                </td>
            </tr>
        `;

        const pageInfo = document.getElementById('pageInfoDetail');
        if (pageInfo) pageInfo.innerText = `0 Data`;

        const prev = document.getElementById('btnPrevDetail');
        const next = document.getElementById('btnNextDetail');

        if (prev) prev.disabled = true;
        if (next) next.disabled = true;

        return;
    }

    renderTableDetailBody(detailDataCache);

    const pageInfo = document.getElementById('pageInfoDetail');
    if (pageInfo) {
        pageInfo.innerText = `Hal ${detailPageReal} dari ${totalPages} (${fmt(totalRecords)} Data)`;
    }

    const prev = document.getElementById('btnPrevDetail');
    const next = document.getElementById('btnNextDetail');

    if (prev) prev.disabled = detailPageReal <= 1;
    if (next) next.disabled = detailPageReal >= totalPages;
}

window.renderFilteredDetailPage = renderFilteredDetailPage;


// =========================================================================
// SORT DETAIL
// =========================================================================
window.sortDetailData = function(column) {
    if (!detailAllDataCache || detailAllDataCache.length === 0) return;

    if (sortDetailCol === column) {
        sortDetailAsc = !sortDetailAsc;
    } else {
        sortDetailCol = column;
        sortDetailAsc = true;
    }

    renderFilteredDetailPage();
};


// =========================================================================
// SEARCH DETAIL CLIENT SIDE
// =========================================================================
window.filterTableDetail = function() {
    clearTimeout(detailSearchTimer);

    detailSearchTimer = setTimeout(() => {
        detailPageReal = 1;
        renderFilteredDetailPage();
    }, 200);
};


// =========================================================================
// FETCH DETAIL
// =========================================================================
async function fetchDetailReal(forceReload = false) {
    const loader = document.getElementById('loadingModal');
    const tbody  = document.getElementById('bodyModalDetail');

    if (!tbody) return;

    if (!forceReload && detailAllDataCache.length > 0) {
        renderFilteredDetailPage();
        return;
    }

    if (abortDetail) abortDetail.abort();
    abortDetail = new AbortController();

    loader?.classList.remove('hidden');
    tbody.innerHTML = '';
    detailDataCache = [];
    detailAllDataCache = [];

    try {
        const payload = buildDetailPayload({
            page: 1,
            limit: 10000
        });

        const res = await apiCall(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
            signal: abortDetail.signal
        });

        const json = await res.json();

        if (json.status !== 200) {
            throw new Error(json.message || 'Gagal mengambil detail');
        }

        detailAllDataCache = json.data?.data || json.data || [];

        if (!Array.isArray(detailAllDataCache)) {
            detailAllDataCache = [];
        }

        await populateAoModalOptions();

        detailPageReal = 1;
        renderFilteredDetailPage();
    } catch (e) {
        if (e.name === 'AbortError') return;

        renderDetailHeaderClickable();

        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="py-16 text-center text-red-500 font-bold uppercase tracking-widest">
                    Gagal mengambil detail: ${escapeHtml(e.message || '')}
                </td>
            </tr>
        `;

        const pageInfo = document.getElementById('pageInfoDetail');
        if (pageInfo) pageInfo.innerText = `0 Data`;

        const prev = document.getElementById('btnPrevDetail');
        const next = document.getElementById('btnNextDetail');

        if (prev) prev.disabled = true;
        if (next) next.disabled = true;
    } finally {
        loader?.classList.add('hidden');
    }
}


// =========================================================================
// PAGINATION DETAIL
// =========================================================================
window.changePageDetailReal = function(step) {
    detailPageReal += step;
    if (detailPageReal < 1) detailPageReal = 1;
    renderFilteredDetailPage();
};


// =========================================================================
// EXPORT DETAIL
// =========================================================================
window.exportExcelDetailReal = async function(e) {
    const btn = e?.target?.closest('button') || document.querySelector('button[onclick^="exportExcelDetailReal"]');
    const txt = btn ? btn.innerHTML : '';

    if (btn) {
        btn.innerHTML = `...`;
        btn.disabled = true;
    }

    try {
        const rows = getFilteredDetailRows();

        if (rows.length === 0) {
            alert("Tidak ada data");
            return;
        }

        let tableRows = '';

        rows.forEach((x, i) => {
            const tgl = getRowTglRealisasi(x);
            const namaAo = getRowAoNama(x);

            tableRows += `
                <tr>
                    <td style="text-align:center;">${i + 1}</td>
                    <td style="mso-number-format:'\\@';">${escapeExcel(x.no_rekening || '')}</td>
                    <td>${escapeExcel(x.nama_nasabah || '')}</td>
                    <td>${escapeExcel(x.alamat || '')}</td>
                    <td>${escapeExcel(x.nama_kankas || '')}</td>
                    <td>${escapeExcel(namaAo)}</td>
                    <td style="mso-number-format:'\\@';">${escapeExcel(tgl)}</td>
                    <td style="text-align:right;">${Math.round(Number(x.plafond || 0))}</td>
                </tr>
            `;
        });

        const html = `
            <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 11px; }
                        th { background: #dbeafe; color: #172554; border: 1px solid #94a3b8; padding: 7px; font-weight: bold; }
                        td { border: 1px solid #cbd5e1; padding: 6px; }
                        .title { font-size: 15px; font-weight: bold; }
                    </style>
                </head>
                <body>
                    <table>
                        <tr>
                            <td colspan="8" class="title">Detail Realisasi</td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                Periode: ${escapeExcel(detailParamsReal.closing_date)} s/d ${escapeExcel(detailParamsReal.harian_date)}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                Filter AO: ${escapeExcel(document.getElementById('filter_ao_modal')?.selectedOptions?.[0]?.text || 'SEMUA AO')}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8">
                                Search: ${escapeExcel(document.getElementById('search_nasabah')?.value || '-')}
                            </td>
                        </tr>
                        <tr></tr>
                        <tr>
                            <th>No</th>
                            <th>No Rekening</th>
                            <th>Nama Nasabah</th>
                            <th>Alamat</th>
                            <th>Kankas</th>
                            <th>AO</th>
                            <th>Tgl Realisasi</th>
                            <th>Plafond</th>
                        </tr>
                        ${tableRows}
                    </table>
                </body>
            </html>
        `;

        const blob = new Blob(["\ufeff" + html], {
            type: "application/vnd.ms-excel;charset=utf-8;"
        });

        const aoText = document.getElementById('filter_ao_modal')?.selectedOptions?.[0]?.text || 'SEMUA_AO';
        const safeAo = aoText.replace(/[^\w\s-]/g, '').replace(/\s+/g, '_');

        const a = document.createElement('a');
        a.href = window.URL.createObjectURL(blob);
        a.download = `Detail_Realisasi_${detailParamsReal.kode_kantor || 'ALL'}_${safeAo}.xls`;
        a.click();

        URL.revokeObjectURL(a.href);
    } catch (e) {
        alert("Gagal export data.");
    } finally {
        if (btn) {
            btn.innerHTML = txt;
            btn.disabled = false;
        }
    }
};


// =========================================================================
// CLOSE MODAL
// =========================================================================
window.closeModalReal = function() {
    document.getElementById('modalDetailReal')?.classList.add('hidden');
};

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModalReal();
});
</script>
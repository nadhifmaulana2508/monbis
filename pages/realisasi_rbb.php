<?php
// Laporan Realisasi Kredit vs RBB bulan berjalan.
?>

<div class="max-w-[1920px] w-full mx-auto px-2 md:px-4 py-4 md:py-6 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">
    <div class="relative z-20 flex-none mb-3 md:mb-4 w-full bg-white p-2 md:p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-3 shrink-0">
        <div class="flex items-center justify-between w-full xl:w-auto shrink-0 px-1">
            <h1 class="text-base md:text-xl font-extrabold text-slate-800 flex items-center gap-2 whitespace-nowrap">
                <span class="p-1.5 md:p-2 bg-indigo-600 rounded-lg text-white shadow-sm shrink-0">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-6h6v6m-8 4h10a2 2 0 002-2V9.5a2 2 0 00-.586-1.414l-4.5-4.5A2 2 0 0012.5 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </span>
                Realisasi vs RBB
                <span class="relative group inline-flex">
                    <button type="button" class="rbb-info-btn" aria-label="Info nominal tabel">i</button>
                    <span class="rbb-info-pop">
                        Nominal pada tabel ditampilkan dalam ribuan rupiah. Contoh: 1.000.000.000 tampil menjadi 1.000.000. Kartu ringkasan tetap memakai format singkat.
                    </span>
                </span>
            </h1>

            <button type="button" onclick="toggleRbbFilter()" class="xl:hidden h-[30px] px-3 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] whitespace-nowrap ml-2 shrink-0">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 12h12M10 20h4"></path></svg> Filter
            </button>
        </div>

        <div id="filterWrapperRbb" class="hidden xl:flex w-full xl:w-auto flex-1 min-w-0 justify-end transition-all duration-300 shrink-0 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
            <form class="flex flex-row flex-wrap xl:flex-nowrap items-end gap-2 md:gap-2.5 w-full xl:w-auto" onsubmit="event.preventDefault(); fetchRbb();">
                <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[140px]">
                    <label class="lbl">HARIAN</label>
                    <input type="date" id="rbb_harian_date" class="inp font-bold text-slate-700 cursor-pointer" onclick="this.showPicker && this.showPicker()" onchange="fetchRbb()">
                </div>
                <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[190px]">
                    <label class="lbl">KANTOR</label>
                    <select id="rbb_kantor" class="inp font-bold text-slate-700 truncate" onchange="fetchRbb()">
                        <option value="000">000 - Konsolidasi</option>
                        <option value="SEMARANG">Korwil Semarang</option>
                        <option value="SOLO">Korwil Solo</option>
                        <option value="BANYUMAS">Korwil Banyumas</option>
                        <option value="PEKALONGAN">Korwil Pekalongan</option>
                    </select>
                </div>
                <div class="field shrink-0 w-[calc(50%-4px)] xl:w-[150px]">
                    <label class="lbl">PEMBANDING</label>
                    <select id="rbb_compare_mode" class="inp font-bold text-slate-700 truncate" onchange="fetchRbb()">
                        <option value="auto">Auto</option>
                        <option value="rbb">RBB</option>
                        <option value="history">History YoY</option>
                    </select>
                </div>
                <div class="flex items-end gap-2 shrink-0 ml-auto xl:ml-0 mt-2 xl:mt-0">
                    <button type="button" id="rbb_summary_toggle" onclick="toggleRbbSummary()" class="h-9 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 font-black text-[10px] md:text-xs shadow-sm">
                        Hide
                    </button>
                    <button type="button" onclick="exportRbbExcel()" class="btn-icon w-[32px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shrink-0" title="Download Excel">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="rbb_summary" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-2 md:gap-3 mb-3 shrink-0"></div>

    <div class="flex-1 min-h-0 overflow-hidden bg-white rounded-xl shadow-sm border border-slate-200 relative flex flex-col z-10">
        <div id="rbb_loading" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-indigo-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
            <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full mb-2 md:mb-3"></div>
            <span>Menyiapkan Data...</span>
        </div>

        <div class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
            <table class="w-max min-w-full text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="rbb_table">
                <thead class="sticky top-0 z-20 bg-slate-100 font-bold tracking-wider text-[9px] md:text-[11px] select-none" id="rbb_head"></thead>
                <tbody id="rbb_body" class="divide-y divide-slate-100 bg-white text-[9.5px] md:text-xs"></tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .field{display:flex;flex-direction:column;gap:.25rem}
    .lbl{font-size:.62rem;font-weight:900;letter-spacing:.08em;color:#64748b}
    .inp{height:2.25rem;border:1px solid #cbd5e1;border-radius:.65rem;padding:0 .75rem;font-size:.76rem;outline:none;background:white}
    .inp:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.14)}
    .btn-icon{height:2.25rem;border-radius:.65rem;display:inline-flex;align-items:center;justify-content:center}
    .rbb-card{background:white;border:1px solid #e2e8f0;border-radius:.85rem;padding:.75rem;box-shadow:0 1px 2px rgba(15,23,42,.04)}
    .rbb-card .label{font-size:.62rem;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:#64748b}
    .rbb-card .value{font-size:clamp(1rem,2vw,1.45rem);line-height:1.05;font-weight:950;color:#0f172a;margin-top:.25rem}
    .rbb-card .sub{font-size:.68rem;font-weight:800;color:#64748b;margin-top:.35rem}
    .rbb-info-btn{width:1.35rem;height:1.35rem;border-radius:999px;border:1px solid #c7d2fe;background:#eef2ff;color:#4f46e5;font-size:.78rem;font-weight:950;line-height:1;display:inline-flex;align-items:center;justify-content:center}
    .rbb-info-pop{position:absolute;left:0;top:calc(100% + .5rem);width:min(21rem,calc(100vw - 2rem));padding:.7rem .8rem;border-radius:.75rem;background:#0f172a;color:#f8fafc;border:1px solid rgba(148,163,184,.35);box-shadow:0 18px 40px rgba(15,23,42,.22);font-size:.72rem;line-height:1.45;font-weight:700;white-space:normal;z-index:80;opacity:0;pointer-events:none;transform:translateY(-.2rem);transition:.14s ease}
    .group:hover .rbb-info-pop{opacity:1;transform:translateY(0)}
    .rbb-th{position:sticky;top:0;background:#f1f5f9;border-bottom:1px solid #cbd5e1}
    .rbb-sort{cursor:pointer;transition:background .15s}
    .rbb-sort:hover{background:#e0e7ff}
    .rbb-sticky-code{position:sticky;left:0;z-index:12;background:inherit}
    .rbb-sticky-name{position:sticky;left:4rem;z-index:12;background:inherit;box-shadow:6px 0 10px rgba(15,23,42,.05)}
    thead .rbb-sticky-code,
    thead .rbb-sticky-name{z-index:30;background:#f1f5f9}
    tbody tr{background:#fff}
    tbody tr:hover{background:#f8fafc}
    tbody tr:hover .rbb-sticky-code,
    tbody tr:hover .rbb-sticky-name{background:#f8fafc}
</style>

<script>
const RBB_API = './api/rbb/';
const RBB_KODE_API = './api/kode/';
const RBB_DATE_API = './api/date/';
const RBB_KORWIL = ['SEMARANG', 'SOLO', 'BANYUMAS', 'PEKALONGAN'];
const rbbFmt = new Intl.NumberFormat('id-ID');
let rbbRows = [];
let rbbMonthlyRows = [];
let rbbGrand = {};
let rbbMeta = {};
let rbbSort = { key: 'kode_kantor', asc: true };
let rbbAbort = null;
let rbbSummaryVisible = true;

function rbbApiCall(url, options = {}) {
    return window.apiFetch ? window.apiFetch(url, options) : fetch(url, options);
}

function getRbbUserKode() {
    const user = (window.getUser && window.getUser()) || null;
    const raw = user?.kode ?? user?.kode_kantor ?? user?.branch_code ?? user?.kode_cabang ?? '';
    if (!raw) return '000';
    const kode = String(raw).replace(/\D/g, '').padStart(3, '0').slice(-3);
    return kode === '099' ? '000' : kode;
}

function toggleRbbFilter() {
    const el = document.getElementById('filterWrapperRbb');
    if (!el) return;
    el.classList.toggle('hidden');
    el.classList.toggle('flex');
}

function toggleRbbSummary() {
    rbbSummaryVisible = !rbbSummaryVisible;
    const box = document.getElementById('rbb_summary');
    const btn = document.getElementById('rbb_summary_toggle');
    box?.classList.toggle('hidden', !rbbSummaryVisible);
    if (btn) btn.textContent = rbbSummaryVisible ? 'Hide' : 'View';
}

function rbbEscape(value) {
    return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function rbbNominal(value) {
    const n = Number(value || 0);
    const abs = Math.abs(n);
    if (abs >= 1e12) return `${rbbFmt.format(Math.round(n / 1e10) / 100)} T`;
    if (abs >= 1e9) return `${rbbFmt.format(Math.round(n / 1e7) / 100)} M`;
    if (abs >= 1e6) return `${rbbFmt.format(Math.round(n / 1e4) / 100)} Jt`;
    return rbbFmt.format(Math.round(n));
}

function rbbTableNominal(value) {
    return rbbFmt.format(Math.round(Number(value || 0) / 1000));
}

function rbbPct(value) {
    return `${rbbFmt.format(Number(value || 0).toFixed(2))}%`;
}

function rbbMonthLabel(value) {
    if (!value) return '-';
    const date = new Date(`${value}T00:00:00`);
    if (Number.isNaN(date.getTime())) return value;
    return date.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
}

function rbbSortIcon(key) {
    if (rbbSort.key !== key) return '<span class="opacity-30 ml-1">↕</span>';
    return rbbSort.asc ? '<span class="text-indigo-600 ml-1">▲</span>' : '<span class="text-indigo-600 ml-1">▼</span>';
}

async function initRbbPage() {
    await loadRbbDate();
    await loadRbbKantor();
    fetchRbb();
}

async function loadRbbDate() {
    try {
        const res = await rbbApiCall(RBB_DATE_API);
        const json = await res.json();
        document.getElementById('rbb_harian_date').value = json.data?.last_created || new Date().toISOString().slice(0, 10);
    } catch (e) {
        document.getElementById('rbb_harian_date').value = new Date().toISOString().slice(0, 10);
    }
}

async function loadRbbKantor() {
    const select = document.getElementById('rbb_kantor');
    if (!select) return;

    const userKode = getRbbUserKode();

    try {
        const res = await rbbApiCall(RBB_KODE_API, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({type: 'kode_kantor'})
        });
        const json = await res.json();
        let html = `
            <option value="000">000 - Konsolidasi</option>
            <option value="SEMARANG">Korwil Semarang</option>
            <option value="SOLO">Korwil Solo</option>
            <option value="BANYUMAS">Korwil Banyumas</option>
            <option value="PEKALONGAN">Korwil Pekalongan</option>
        `;

        (json.data || []).forEach(item => {
            const kode = String(item.kode_kantor || '').padStart(3, '0');
            html += `<option value="${rbbEscape(kode)}">${rbbEscape(kode)} - ${rbbEscape(item.nama_kantor || '')}</option>`;
        });

        if (userKode !== '000' && !html.includes(`value="${userKode}"`)) {
            const user = (window.getUser && window.getUser()) || {};
            html += `<option value="${rbbEscape(userKode)}">${rbbEscape(userKode)} - ${rbbEscape(user.branch_name || user.nama_kantor || 'Cabang User')}</option>`;
        }

        select.innerHTML = html;
    } catch (e) {
        select.innerHTML = userKode === '000'
            ? `<option value="000">000 - Konsolidasi</option>`
            : `<option value="${rbbEscape(userKode)}">${rbbEscape(userKode)} - Cabang User</option>`;
    }

    if (userKode !== '000') {
        select.value = userKode;
        select.disabled = true;
        select.classList.add('bg-slate-100', 'cursor-not-allowed', 'text-slate-500');
    } else {
        select.disabled = false;
        select.classList.remove('bg-slate-100', 'cursor-not-allowed', 'text-slate-500');
    }
}

async function fetchRbb() {
    const loading = document.getElementById('rbb_loading');
    const body = document.getElementById('rbb_body');
    const kantor = document.getElementById('rbb_kantor')?.value || '000';
    const harian = document.getElementById('rbb_harian_date')?.value || '';
    const compareMode = document.getElementById('rbb_compare_mode')?.value || 'auto';

    if (rbbAbort) rbbAbort.abort();
    rbbAbort = new AbortController();

    loading?.classList.remove('hidden');
    body.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-slate-400 italic">Sedang mengambil data...</td></tr>`;

    const payload = {
        type: 'realisasi_rbb_bulan_berjalan',
        harian_date: harian,
        compare_mode: compareMode
    };

    if (RBB_KORWIL.includes(kantor)) {
        payload.korwil = kantor;
    } else if (kantor !== '000') {
        payload.kode_kantor = kantor;
    }

    try {
        const res = await rbbApiCall(RBB_API, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload),
            signal: rbbAbort.signal
        });
        const json = await res.json();
        if (json.status !== 200) throw new Error(json.message || 'Gagal mengambil data');

        rbbRows = json.data?.data || [];
        rbbMonthlyRows = json.data?.monthly_breakdown || [];
        rbbGrand = json.data?.grand_total || {};
        rbbMeta = json.data?.meta || {};
        if (rbbMonthlyRows.length) {
            rbbSort = { key: 'periode', asc: false };
        } else if (rbbMeta.compare_mode === 'history') {
            rbbSort = { key: 'selisih', asc: true };
        }
        renderRbbSummary();
        renderRbbTable();
    } catch (e) {
        if (e.name !== 'AbortError') {
            body.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-red-500 font-bold">Error: ${rbbEscape(e.message)}</td></tr>`;
        }
    } finally {
        loading?.classList.add('hidden');
    }
}

function renderRbbSummary() {
    const box = document.getElementById('rbb_summary');
    if (!box) return;

    if (rbbMeta.compare_mode === 'history') {
        const suffix = rbbMeta.fallback_history ? 'Auto History' : 'History YoY';
        const cards = [
            [`Realisasi ${rbbMeta.tahun || 'Tahun Ini'}`, rbbGrand.realisasi_bulan_ini, 'text-blue-700', 'bg-blue-50'],
            ['Selisih YoY', rbbGrand.selisih, Number(rbbGrand.selisih || 0) >= 0 ? 'text-emerald-700' : 'text-red-700', 'bg-slate-50'],
            ['% YoY', rbbGrand.yoy_persen, Number(rbbGrand.yoy_persen || 0) >= 0 ? 'text-emerald-700' : 'text-red-700', 'bg-emerald-50', true],
            ['Growth', rbbGrand.growth, Number(rbbGrand.growth || 0) >= 0 ? 'text-emerald-700' : 'text-red-700', 'bg-blue-50'],
            ['% Growth', rbbGrand.growth_persen, Number(rbbGrand.growth_persen || 0) >= 0 ? 'text-emerald-700' : 'text-red-700', 'bg-indigo-50', true],
            ['Run Off', rbbGrand.run_off, 'text-orange-700', 'bg-orange-50'],
        ];

        box.innerHTML = cards.map(([label, value, color, bg, percent, plain]) => `
            <div class="rbb-card ${bg}">
                <div class="label">${label}</div>
                <div class="value ${color}">${plain ? rbbEscape(value) : (percent ? rbbPct(value) : `Rp ${rbbNominal(value)}`)}</div>
                <div class="sub">${rbbMonthlyRows.length ? `${rbbMonthlyRows.length} bulan` : `${rbbRows.length} kantor`}</div>
            </div>
        `).join('');
        return;
    }

    const cards = [
        ['Target RBB Bulan Ini', rbbGrand.nilai_rbb, 'text-indigo-700', 'bg-indigo-50'],
        ['Realisasi Bulan Ini', rbbGrand.realisasi_bulan_ini, 'text-blue-700', 'bg-blue-50'],
        ['Pencapaian RBB', rbbGrand.persentase_rbb_bulan_ini, 'text-emerald-700', 'bg-emerald-50', true],
        ['Kekurangan s/d Bulan Lalu', rbbGrand.kekurangan_sd_bulan_lalu, 'text-red-700', 'bg-red-50'],
        ['Pencapaian Total Beban', rbbGrand.persentase_rbb_plus_kekurangan, 'text-orange-700', 'bg-orange-50', true]
    ];

    box.innerHTML = cards.map(([label, value, color, bg, percent]) => `
        <div class="rbb-card ${bg}">
            <div class="label">${label}</div>
            <div class="value ${color}">${percent ? rbbPct(value) : `Rp ${rbbNominal(value)}`}</div>
            <div class="sub">${rbbMonthlyRows.length ? `${rbbMonthlyRows.length} bulan` : `${rbbRows.length} kantor`}</div>
        </div>
    `).join('');
}

function renderRbbTable() {
    const head = document.getElementById('rbb_head');
    const body = document.getElementById('rbb_body');
    if (!head || !body) return;

    if (rbbMeta.compare_mode === 'history') {
        if (rbbMonthlyRows.length) {
            renderRbbHistoryMonthlyTable(head, body);
        } else {
            renderRbbHistoryTable(head, body);
        }
        return;
    }

    if (rbbMonthlyRows.length) {
        renderRbbMonthlyTable(head, body);
        return;
    }

    head.innerHTML = `
        <tr>
            <th class="rbb-th rbb-sticky-code rbb-sort px-2 py-2 text-left w-16" onclick="sortRbb('kode_kantor')">Kode${rbbSortIcon('kode_kantor')}</th>
            <th class="rbb-th rbb-sticky-name rbb-sort px-2 py-2 text-left w-44" onclick="sortRbb('nama_kantor')">Kantor${rbbSortIcon('nama_kantor')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-32" onclick="sortRbb('nilai_rbb')">RBB (Rb)${rbbSortIcon('nilai_rbb')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-32" onclick="sortRbb('realisasi_bulan_ini')">Realisasi (Rb)${rbbSortIcon('realisasi_bulan_ini')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-24" onclick="sortRbb('persentase_rbb_bulan_ini')">% RBB${rbbSortIcon('persentase_rbb_bulan_ini')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-36" onclick="sortRbb('kekurangan_sd_bulan_lalu')">Kurang Lalu (Rb)${rbbSortIcon('kekurangan_sd_bulan_lalu')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-36" onclick="sortRbb('total_beban_target')">Total Beban (Rb)${rbbSortIcon('total_beban_target')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-24" onclick="sortRbb('persentase_rbb_plus_kekurangan')">% Beban${rbbSortIcon('persentase_rbb_plus_kekurangan')}</th>
        </tr>
    `;

    if (!rbbRows.length) {
        body.innerHTML = `<tr><td colspan="8" class="py-12 text-center text-slate-400 italic">Tidak ada data.</td></tr>`;
        return;
    }

    const rows = [...rbbRows].sort((a, b) => {
        const av = a[rbbSort.key];
        const bv = b[rbbSort.key];
        if (!isNaN(parseFloat(av)) && isFinite(av)) {
            return rbbSort.asc ? Number(av || 0) - Number(bv || 0) : Number(bv || 0) - Number(av || 0);
        }
        return rbbSort.asc
            ? String(av || '').localeCompare(String(bv || ''))
            : String(bv || '').localeCompare(String(av || ''));
    });

    body.innerHTML = rows.map(r => {
        const pctColor = Number(r.persentase_rbb_bulan_ini || 0) >= 100 ? 'text-emerald-700 bg-emerald-50' : 'text-red-700 bg-red-50';
        const bebanColor = Number(r.persentase_rbb_plus_kekurangan || 0) >= 100 ? 'text-emerald-700 bg-emerald-50' : 'text-orange-700 bg-orange-50';
        return `
            <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[42px]">
                <td class="rbb-sticky-code px-2 py-2 text-left font-mono font-bold text-slate-500">${rbbEscape(r.kode_kantor)}</td>
                <td class="rbb-sticky-name px-2 py-2 text-left font-bold text-slate-800 truncate" title="${rbbEscape(r.nama_kantor)}">${rbbEscape(r.nama_kantor)}</td>
                <td class="px-2 py-2 text-right font-mono font-bold text-indigo-700">${rbbTableNominal(r.nilai_rbb)}</td>
                <td class="px-2 py-2 text-right font-mono font-bold text-blue-700">${rbbTableNominal(r.realisasi_bulan_ini)}</td>
                <td class="px-2 py-2 text-right font-black ${pctColor}">${rbbPct(r.persentase_rbb_bulan_ini)}</td>
                <td class="px-2 py-2 text-right font-mono font-bold text-red-700">${rbbTableNominal(r.kekurangan_sd_bulan_lalu)}</td>
                <td class="px-2 py-2 text-right font-mono font-bold text-orange-700">${rbbTableNominal(r.total_beban_target)}</td>
                <td class="px-2 py-2 text-right font-black ${bebanColor}">${rbbPct(r.persentase_rbb_plus_kekurangan)}</td>
            </tr>
        `;
    }).join('');
}

function renderRbbHistoryTable(head, body) {
    const year = rbbMeta.tahun || 'Tahun Ini';
    const prevYear = rbbMeta.tahun_pembanding || 'Tahun Lalu';
    head.innerHTML = `
        <tr>
            <th class="rbb-th rbb-sticky-code rbb-sort px-2 py-2 text-left w-16" onclick="sortRbb('kode_kantor')">Kode${rbbSortIcon('kode_kantor')}</th>
            <th class="rbb-th rbb-sticky-name rbb-sort px-2 py-2 text-left w-44" onclick="sortRbb('nama_kantor')">Kantor${rbbSortIcon('nama_kantor')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-36" onclick="sortRbb('realisasi_bulan_ini')">Realisasi ${year} (Rb)${rbbSortIcon('realisasi_bulan_ini')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-32" onclick="sortRbb('selisih')">Selisih YoY (Rb)${rbbSortIcon('selisih')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-24" onclick="sortRbb('yoy_persen')">% YoY${rbbSortIcon('yoy_persen')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-32" onclick="sortRbb('angsuran')">Angsuran (Rb)${rbbSortIcon('angsuran')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-32" onclick="sortRbb('pelunasan')">Lunas (Rb)${rbbSortIcon('pelunasan')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-32" onclick="sortRbb('run_off')">Run Off (Rb)${rbbSortIcon('run_off')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-32" onclick="sortRbb('growth')">Growth (Rb)${rbbSortIcon('growth')}</th>
            <th class="rbb-th rbb-sort px-2 py-2 text-right w-24" onclick="sortRbb('growth_persen')">% Growth${rbbSortIcon('growth_persen')}</th>
        </tr>
    `;

    if (!rbbRows.length) {
        body.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-slate-400 italic">Tidak ada data realisasi history.</td></tr>`;
        return;
    }

    const rows = sortedRbbRows(rbbRows);
    body.innerHTML = rows.map(r => {
        const selisih = Number(r.selisih || 0);
        const growth = Number(r.growth || 0);
        const yoyColor = selisih >= 0 ? 'text-emerald-700' : 'text-red-700';
        const growthColor = growth >= 0 ? 'text-emerald-700' : 'text-red-700';
        return `
            <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[42px]">
                <td class="rbb-sticky-code px-2 py-2 text-left font-mono font-bold text-slate-500">${rbbEscape(r.kode_kantor)}</td>
                <td class="rbb-sticky-name px-2 py-2 text-left font-bold text-slate-800 truncate" title="${rbbEscape(r.nama_kantor)}">${rbbEscape(r.nama_kantor)}</td>
                <td class="px-2 py-2 text-right font-mono font-bold text-blue-700">${rbbTableNominal(r.realisasi_bulan_ini)}</td>
                <td class="px-2 py-2 text-right font-mono font-black ${yoyColor}">${selisih >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(selisih))}</td>
                <td class="px-2 py-2 text-right font-black ${yoyColor}">${rbbPct(r.yoy_persen)}</td>
                <td class="px-2 py-2 text-right font-mono font-bold text-cyan-700">${rbbTableNominal(r.angsuran)}</td>
                <td class="px-2 py-2 text-right font-mono font-bold text-violet-700">${rbbTableNominal(r.pelunasan)}</td>
                <td class="px-2 py-2 text-right font-mono font-bold text-orange-700">${rbbTableNominal(r.run_off)}</td>
                <td class="px-2 py-2 text-right font-mono font-black ${growthColor}">${growth >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(growth))}</td>
                <td class="px-2 py-2 text-right font-black ${growthColor}">${rbbPct(r.growth_persen)}</td>
            </tr>
        `;
    }).join('');
}

function renderRbbHistoryMonthlyTable(head, body) {
    const year = rbbMeta.tahun || 'Tahun Ini';
    const prevYear = rbbMeta.tahun_pembanding || 'Tahun Lalu';
    head.innerHTML = `
        <tr>
            <th class="rbb-th rbb-sort px-3 py-2 text-left w-44" onclick="sortRbb('periode')">Bulan${rbbSortIcon('periode')}</th>
            <th class="rbb-th px-3 py-2 text-left w-52">Kantor</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-36" onclick="sortRbb('realisasi_bulan_ini')">Realisasi ${year} (Rb)${rbbSortIcon('realisasi_bulan_ini')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-32" onclick="sortRbb('selisih')">Selisih YoY (Rb)${rbbSortIcon('selisih')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-24" onclick="sortRbb('yoy_persen')">% YoY${rbbSortIcon('yoy_persen')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-32" onclick="sortRbb('angsuran')">Angsuran (Rb)${rbbSortIcon('angsuran')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-32" onclick="sortRbb('pelunasan')">Lunas (Rb)${rbbSortIcon('pelunasan')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-32" onclick="sortRbb('run_off')">Run Off (Rb)${rbbSortIcon('run_off')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-32" onclick="sortRbb('growth')">Growth (Rb)${rbbSortIcon('growth')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-24" onclick="sortRbb('growth_persen')">% Growth${rbbSortIcon('growth_persen')}</th>
        </tr>
    `;

    if (!rbbMonthlyRows.length) {
        body.innerHTML = `<tr><td colspan="10" class="py-12 text-center text-slate-400 italic">Tidak ada breakdown history bulanan.</td></tr>`;
        return;
    }

    const rows = sortedRbbRows(rbbMonthlyRows);
    body.innerHTML = rows.map(r => {
        const selisih = Number(r.selisih || 0);
        const growth = Number(r.growth || 0);
        const yoyColor = selisih >= 0 ? 'text-emerald-700' : 'text-red-700';
        const growthColor = growth >= 0 ? 'text-emerald-700' : 'text-red-700';
        return `
            <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[44px]">
                <td class="px-3 py-2 text-left font-black text-slate-800">${rbbEscape(rbbMonthLabel(r.periode))}</td>
                <td class="px-3 py-2 text-left font-bold text-slate-600 truncate" title="${rbbEscape(r.nama_kantor)}">${rbbEscape(r.nama_kantor)}</td>
                <td class="px-3 py-2 text-right font-mono font-bold text-blue-700">${rbbTableNominal(r.realisasi_bulan_ini)}</td>
                <td class="px-3 py-2 text-right font-mono font-black ${yoyColor}">${selisih >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(selisih))}</td>
                <td class="px-3 py-2 text-right font-black ${yoyColor}">${rbbPct(r.yoy_persen)}</td>
                <td class="px-3 py-2 text-right font-mono font-bold text-cyan-700">${rbbTableNominal(r.angsuran)}</td>
                <td class="px-3 py-2 text-right font-mono font-bold text-violet-700">${rbbTableNominal(r.pelunasan)}</td>
                <td class="px-3 py-2 text-right font-mono font-bold text-orange-700">${rbbTableNominal(r.run_off)}</td>
                <td class="px-3 py-2 text-right font-mono font-black ${growthColor}">${growth >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(growth))}</td>
                <td class="px-3 py-2 text-right font-black ${growthColor}">${rbbPct(r.growth_persen)}</td>
            </tr>
        `;
    }).join('');
}

function sortedRbbRows(sourceRows) {
    return [...sourceRows].sort((a, b) => {
        const av = a[rbbSort.key];
        const bv = b[rbbSort.key];
        if (!isNaN(parseFloat(av)) && isFinite(av)) {
            return rbbSort.asc ? Number(av || 0) - Number(bv || 0) : Number(bv || 0) - Number(av || 0);
        }
        return rbbSort.asc
            ? String(av || '').localeCompare(String(bv || ''))
            : String(bv || '').localeCompare(String(av || ''));
    });
}

function renderRbbMonthlyTable(head, body) {
    head.innerHTML = `
        <tr>
            <th class="rbb-th rbb-sort px-3 py-2 text-left w-44" onclick="sortRbb('periode')">Bulan${rbbSortIcon('periode')}</th>
            <th class="rbb-th px-3 py-2 text-left w-52">Kantor</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-36" onclick="sortRbb('nilai_rbb')">RBB (Rb)${rbbSortIcon('nilai_rbb')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-36" onclick="sortRbb('realisasi_bulan_ini')">Realisasi (Rb)${rbbSortIcon('realisasi_bulan_ini')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-28" onclick="sortRbb('persentase_rbb_bulan_ini')">% RBB${rbbSortIcon('persentase_rbb_bulan_ini')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-36" onclick="sortRbb('selisih')">Selisih (Rb)${rbbSortIcon('selisih')}</th>
            <th class="rbb-th rbb-sort px-3 py-2 text-right w-36" onclick="sortRbb('kekurangan')">Kekurangan (Rb)${rbbSortIcon('kekurangan')}</th>
        </tr>
    `;

    if (!rbbMonthlyRows.length) {
        body.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-slate-400 italic">Tidak ada breakdown bulanan.</td></tr>`;
        return;
    }

    const rows = [...rbbMonthlyRows].sort((a, b) => {
        const av = a[rbbSort.key];
        const bv = b[rbbSort.key];
        if (!isNaN(parseFloat(av)) && isFinite(av)) {
            return rbbSort.asc ? Number(av || 0) - Number(bv || 0) : Number(bv || 0) - Number(av || 0);
        }
        return rbbSort.asc
            ? String(av || '').localeCompare(String(bv || ''))
            : String(bv || '').localeCompare(String(av || ''));
    });

    body.innerHTML = rows.map(r => {
        const pctColor = Number(r.persentase_rbb_bulan_ini || 0) >= 100 ? 'text-emerald-700 bg-emerald-50' : 'text-red-700 bg-red-50';
        const selisih = Number(r.selisih || 0);
        const selisihColor = selisih >= 0 ? 'text-emerald-700' : 'text-red-700';

        return `
            <tr class="hover:bg-slate-50 border-b border-slate-100 transition h-[44px]">
                <td class="px-3 py-2 text-left font-black text-slate-800">${rbbEscape(rbbMonthLabel(r.periode))}</td>
                <td class="px-3 py-2 text-left font-bold text-slate-600 truncate" title="${rbbEscape(r.nama_kantor)}">${rbbEscape(r.nama_kantor)}</td>
                <td class="px-3 py-2 text-right font-mono font-bold text-indigo-700">${rbbTableNominal(r.nilai_rbb)}</td>
                <td class="px-3 py-2 text-right font-mono font-bold text-blue-700">${rbbTableNominal(r.realisasi_bulan_ini)}</td>
                <td class="px-3 py-2 text-right font-black ${pctColor}">${rbbPct(r.persentase_rbb_bulan_ini)}</td>
                <td class="px-3 py-2 text-right font-mono font-black ${selisihColor}">${selisih >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(selisih))}</td>
                <td class="px-3 py-2 text-right font-mono font-bold text-orange-700">${rbbTableNominal(r.kekurangan)}</td>
            </tr>
        `;
    }).join('');
}

function sortRbb(key) {
    if (rbbSort.key === key) {
        rbbSort.asc = !rbbSort.asc;
    } else {
        rbbSort = { key, asc: true };
    }
    renderRbbTable();
}

function exportRbbExcel() {
    const exportRows = rbbMonthlyRows.length ? rbbMonthlyRows : rbbRows;
    if (!exportRows.length) return alert('Tidak ada data untuk diexport.');

    if (rbbMeta.compare_mode === 'history') {
        const year = rbbMeta.tahun || 'Tahun Ini';
        const prevYear = rbbMeta.tahun_pembanding || 'Tahun Lalu';
        const title = rbbMonthlyRows.length ? 'Bulan\tKantor' : 'Kode\tKantor';
        let csv = `${title}\tRealisasi ${year}\tRealisasi ${prevYear}\tSelisih YoY\t% YoY\tAngsuran\tLunas\tRun Off\tGrowth\t% Growth\n`;
        exportRows.forEach(r => {
            const first = rbbMonthlyRows.length ? rbbMonthLabel(r.periode) : `'${r.kode_kantor}`;
            csv += `${first}\t${r.nama_kantor}\t${Math.round(r.realisasi_bulan_ini || 0)}\t${Math.round(r.realisasi_tahun_lalu || 0)}\t${Math.round(r.selisih || 0)}\t${r.yoy_persen}\t${Math.round(r.angsuran || 0)}\t${Math.round(r.pelunasan || 0)}\t${Math.round(r.run_off || 0)}\t${Math.round(r.growth || 0)}\t${r.growth_persen}\n`;
        });

        const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = rbbMonthlyRows.length ? 'History_Realisasi_Bulanan.xls' : 'History_Realisasi_YoY.xls';
        a.click();
        URL.revokeObjectURL(a.href);
        return;
    }

    if (rbbMonthlyRows.length) {
        let csv = 'Bulan\tKantor\tNilai RBB\tRealisasi\t% RBB\tSelisih\tKekurangan\n';
        rbbMonthlyRows.forEach(r => {
            csv += `${rbbMonthLabel(r.periode)}\t${r.nama_kantor}\t${Math.round(r.nilai_rbb || 0)}\t${Math.round(r.realisasi_bulan_ini || 0)}\t${r.persentase_rbb_bulan_ini}\t${Math.round(r.selisih || 0)}\t${Math.round(r.kekurangan || 0)}\n`;
        });

        const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'Breakdown_RBB_Cabang.xls';
        a.click();
        URL.revokeObjectURL(a.href);
        return;
    }

    let csv = 'Kode\tKantor\tNilai RBB\tRealisasi Bulan Ini\t% RBB\tKekurangan s/d Bulan Lalu\tTotal Beban Target\t% Beban\n';
    rbbRows.forEach(r => {
        csv += `'${r.kode_kantor}\t${r.nama_kantor}\t${Math.round(r.nilai_rbb || 0)}\t${Math.round(r.realisasi_bulan_ini || 0)}\t${r.persentase_rbb_bulan_ini}\t${Math.round(r.kekurangan_sd_bulan_lalu || 0)}\t${Math.round(r.total_beban_target || 0)}\t${r.persentase_rbb_plus_kekurangan}\n`;
    });

    const blob = new Blob([csv], { type: 'application/vnd.ms-excel' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'Realisasi_vs_RBB.xls';
    a.click();
    URL.revokeObjectURL(a.href);
}

window.addEventListener('DOMContentLoaded', initRbbPage);
</script>

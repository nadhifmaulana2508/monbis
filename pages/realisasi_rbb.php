<?php
// Laporan Realisasi Kredit vs RBB bulan berjalan.
?>

<div class="max-w-[1920px] w-full mx-auto px-2 md:px-4 py-2.5 md:py-4 xl:py-5 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">
    <div class="relative z-20 flex-none mb-2.5 md:mb-3 w-full bg-white p-2 md:p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col xl:flex-row items-start xl:items-center justify-between gap-2.5 shrink-0">
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

            <div class="xl:hidden flex items-center gap-1.5 ml-2 shrink-0">
                <button type="button" id="rbb_summary_toggle_mobile" onclick="toggleRbbSummary()" class="rbb-summary-toggle h-[30px] w-[30px] bg-white border border-slate-200 text-slate-700 rounded-lg inline-flex items-center justify-center shadow-sm transition" aria-label="Sembunyikan rekap" title="Sembunyikan rekap"></button>
                <button type="button" onclick="toggleRbbFilter()" class="h-[30px] px-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg flex items-center gap-1.5 shadow-sm transition font-bold text-[10px] whitespace-nowrap shrink-0">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 12h12M10 20h4"></path></svg> Filter
                </button>
            </div>
        </div>

        <div id="filterWrapperRbb" class="hidden xl:flex w-full xl:w-auto flex-1 min-w-0 justify-end transition-all duration-300 shrink-0 border-t xl:border-none pt-3 xl:pt-0 mt-2 xl:mt-0">
            <form id="rbb_filter_form" class="rbb-filter-grid w-full xl:w-auto" onsubmit="event.preventDefault(); fetchRbb();">
                <div class="field rbb-filter-date">
                    <label class="lbl">HARIAN</label>
                    <input type="date" id="rbb_harian_date" class="inp font-bold text-slate-700 cursor-pointer" onclick="this.showPicker && this.showPicker()" onchange="fetchRbb()">
                </div>
                <div class="field rbb-filter-office">
                    <label class="lbl">KANTOR</label>
                    <select id="rbb_kantor" class="inp font-bold text-slate-700 truncate" onchange="fetchRbb()">
                        <option value="000">000 - Konsolidasi</option>
                        <option value="SEMARANG">Korwil Semarang</option>
                        <option value="SOLO">Korwil Solo</option>
                        <option value="BANYUMAS">Korwil Banyumas</option>
                        <option value="PEKALONGAN">Korwil Pekalongan</option>
                    </select>
                </div>
                <div class="field rbb-filter-compare">
                    <label class="lbl">PEMBANDING</label>
                    <select id="rbb_compare_mode" class="inp font-bold text-slate-700 truncate" onchange="fetchRbb()">
                        <option value="auto">Auto</option>
                        <option value="rbb">RBB</option>
                        <option value="history">History YoY</option>
                    </select>
                </div>
                <div class="rbb-filter-actions">
                    <button type="button" id="rbb_summary_toggle" onclick="toggleRbbSummary()" class="rbb-summary-toggle hidden xl:inline-flex h-9 w-9 rounded-lg border border-slate-200 bg-white text-slate-700 items-center justify-center shadow-sm transition" aria-label="Sembunyikan rekap" title="Sembunyikan rekap"></button>
                    <button type="button" onclick="exportRbbExcel()" class="btn-icon w-[32px] md:w-[42px] bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shrink-0" title="Download Excel">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="rbb_summary" class="rbb-summary-grid mb-2.5 md:mb-3 shrink-0"></div>

    <div class="flex-1 min-h-0 overflow-hidden bg-white rounded-xl shadow-sm border border-slate-200 relative flex flex-col z-10">
        <div id="rbb_loading" class="hidden absolute inset-0 bg-white/80 z-[100] flex flex-col items-center justify-center text-indigo-600 font-bold uppercase tracking-widest text-[10px] md:text-sm backdrop-blur-sm">
            <div class="animate-spin h-8 w-8 md:h-10 md:w-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full mb-2 md:mb-3"></div>
            <span>Menyiapkan Data...</span>
        </div>

        <div id="rbb_table_scroller" class="flex-1 w-full h-full overflow-auto custom-scrollbar relative">
            <table class="text-center border-separate border-spacing-0 text-slate-700 table-fixed" id="rbb_table">
                <thead class="select-none" id="rbb_head"></thead>
                <tbody id="rbb_body"></tbody>
            </table>
        </div>
    </div>
</div>

<style>
    :root {
        --rbb-code-w: 64px;
        --rbb-name-w: 210px;
        --rbb-period-w: 150px;
        --rbb-money-w: 142px;
        --rbb-money-wide-w: 158px;
        --rbb-pct-w: 96px;
        --rbb-head-h: 40px;
    }

    .field { display:flex; flex-direction:column; gap:.25rem; min-width:0; }
    .lbl { font-size:.62rem; font-weight:900; letter-spacing:.08em; color:#64748b; white-space:nowrap; }
    .inp {
        width:100%; min-width:0; height:2.25rem; border:1px solid #cbd5e1; border-radius:.65rem;
        padding:0 .75rem; font-size:.76rem; outline:none; background:white; color:#334155;
        transition:border-color .15s, box-shadow .15s;
    }
    .inp:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.14); }
    .inp:disabled { background:#f8fafc; color:#64748b; cursor:not-allowed; }
    .btn-icon { height:2.25rem; border-radius:.65rem; display:inline-flex; align-items:center; justify-content:center; }

    .rbb-filter-grid {
        display:grid;
        grid-template-columns:minmax(0,1fr) minmax(0,1fr);
        gap:.5rem;
        align-items:end;
    }
    .rbb-filter-compare { grid-column:1 / 2; }
    .rbb-filter-actions {
        grid-column:2 / 3;
        display:flex;
        align-items:flex-end;
        justify-content:flex-end;
        gap:.5rem;
        min-width:0;
    }

    .rbb-summary-grid {
        display:grid;
        grid-template-columns:repeat(2,minmax(0,1fr));
        gap:.5rem;
    }
    .rbb-summary-grid.hidden { display:none !important; }
    .rbb-summary-toggle {
        flex:0 0 auto;
        color:#475569;
    }
    .rbb-summary-toggle:hover {
        color:#4f46e5;
        border-color:#c7d2fe;
        background:#eef2ff;
    }
    .rbb-summary-toggle svg { width:15px; height:15px; }
    .rbb-card {
        min-width:0; background:white; border:1px solid #e2e8f0; border-radius:.8rem;
        padding:.65rem .7rem; box-shadow:0 1px 2px rgba(15,23,42,.04); overflow:hidden;
    }
    .rbb-card .label {
        font-size:.56rem; line-height:1.25; font-weight:900; letter-spacing:.055em;
        text-transform:uppercase; color:#64748b;
    }
    .rbb-card .value {
        min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
        font-size:clamp(.88rem,3.5vw,1.4rem); line-height:1.1; font-weight:950;
        color:#0f172a; margin-top:.28rem; font-variant-numeric:tabular-nums;
    }
    .rbb-card .sub { font-size:.61rem; font-weight:800; color:#64748b; margin-top:.3rem; }

    .rbb-info-btn {
        width:1.35rem; height:1.35rem; border-radius:999px; border:1px solid #c7d2fe;
        background:#eef2ff; color:#4f46e5; font-size:.78rem; font-weight:950; line-height:1;
        display:inline-flex; align-items:center; justify-content:center;
    }
    .rbb-info-pop {
        position:absolute; left:0; top:calc(100% + .5rem); width:min(21rem,calc(100vw - 2rem));
        padding:.7rem .8rem; border-radius:.75rem; background:#0f172a; color:#f8fafc;
        border:1px solid rgba(148,163,184,.35); box-shadow:0 18px 40px rgba(15,23,42,.22);
        font-size:.72rem; line-height:1.45; font-weight:700; white-space:normal; z-index:80;
        opacity:0; pointer-events:none; transform:translateY(-.2rem); transition:.14s ease;
    }
    .group:hover .rbb-info-pop,
    .group:focus-within .rbb-info-pop { opacity:1; transform:translateY(0); }

    #rbb_table_scroller {
        isolation:isolate; overscroll-behavior:contain; -webkit-overflow-scrolling:touch;
        scrollbar-gutter:stable;
    }
    #rbb_table {
        width:max-content; min-width:100%; border-collapse:separate; border-spacing:0;
        table-layout:fixed; font-variant-numeric:tabular-nums;
    }
    #rbb_table th,
    #rbb_table td {
        box-sizing:border-box; white-space:nowrap; vertical-align:middle;
        padding:.5rem .55rem; border-bottom:1px solid #f1f5f9;
    }
    #rbb_table thead th {
        font-size:.68rem; line-height:1.15; letter-spacing:.025em; text-transform:uppercase;
    }
    #rbb_table tbody td { font-size:.72rem; line-height:1.15; }

    .rbb-th {
        position:sticky; top:0; z-index:24; height:var(--rbb-head-h); background:#f1f5f9;
        color:#475569; border-bottom:1px solid #cbd5e1 !important;
        box-shadow:inset 0 1px 0 #e2e8f0;
    }
    .rbb-total-th {
        position:sticky; top:var(--rbb-head-h); z-index:22; height:38px; background:#eaf1ff;
        color:#334155; border-bottom:1px solid #cbd5e1 !important;
        box-shadow:0 3px 7px -5px rgba(15,23,42,.55);
    }
    .rbb-sort { cursor:pointer; transition:background .15s; }
    .rbb-sort:hover { background:#e0e7ff; }

    .rbb-col-code { width:var(--rbb-code-w); min-width:var(--rbb-code-w); max-width:var(--rbb-code-w); }
    .rbb-col-name { width:var(--rbb-name-w); min-width:var(--rbb-name-w); max-width:var(--rbb-name-w); }
    .rbb-col-period { width:var(--rbb-period-w); min-width:var(--rbb-period-w); max-width:var(--rbb-period-w); }
    .rbb-col-money { width:var(--rbb-money-w); min-width:var(--rbb-money-w); max-width:var(--rbb-money-w); }
    .rbb-col-money-wide { width:var(--rbb-money-wide-w); min-width:var(--rbb-money-wide-w); max-width:var(--rbb-money-wide-w); }
    .rbb-col-pct { width:var(--rbb-pct-w); min-width:var(--rbb-pct-w); max-width:var(--rbb-pct-w); }

    .rbb-sticky-code {
        position:sticky; left:0; z-index:14; background:#fff;
        box-shadow:1px 0 0 #e2e8f0;
    }
    .rbb-sticky-name {
        position:sticky; left:var(--rbb-code-w); z-index:13; background:#fff;
        box-shadow:6px 0 9px -8px rgba(15,23,42,.75), 1px 0 0 #e2e8f0;
    }
    .rbb-sticky-period {
        position:sticky; left:0; z-index:14; background:#fff;
        box-shadow:6px 0 9px -8px rgba(15,23,42,.75), 1px 0 0 #e2e8f0;
    }
    thead .rbb-sticky-code,
    thead .rbb-sticky-name,
    thead .rbb-sticky-period { z-index:34; background:#f1f5f9; }
    thead .rbb-total-th.rbb-sticky-code,
    thead .rbb-total-th.rbb-sticky-name,
    thead .rbb-total-th.rbb-sticky-period { z-index:33; background:#eaf1ff; }

    #rbb_table tbody tr { background:#fff; height:42px; }
    #rbb_table tbody tr:hover td { background:#f8fafc; }
    #rbb_table tbody tr:hover .rbb-sticky-code,
    #rbb_table tbody tr:hover .rbb-sticky-name,
    #rbb_table tbody tr:hover .rbb-sticky-period { background:#f8fafc; }

    .rbb-name-text { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .rbb-number { font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace; font-weight:800; }
    .rbb-positive { color:#047857; }
    .rbb-negative { color:#b91c1c; }
    .rbb-neutral { color:#475569; }

    @media (min-width:640px) {
        .rbb-filter-grid {
            grid-template-columns:130px minmax(190px,1fr) 150px auto;
            gap:.6rem;
        }
        .rbb-filter-date,
        .rbb-filter-office,
        .rbb-filter-compare,
        .rbb-filter-actions { grid-column:auto; }
        .rbb-summary-grid { grid-template-columns:repeat(3,minmax(0,1fr)); gap:.65rem; }
        .rbb-card { padding:.72rem .78rem; }
    }

    @media (min-width:1024px) {
        .rbb-summary-grid { grid-template-columns:repeat(5,minmax(0,1fr)); gap:.75rem; }
        .rbb-card .label { font-size:.61rem; }
        .rbb-card .sub { font-size:.68rem; }
    }

    @media (min-width:1280px) {
        .rbb-filter-grid { grid-template-columns:140px 210px 155px auto; width:auto; }
    }

    @media (min-width:1440px) {
        :root {
            --rbb-code-w:68px;
            --rbb-name-w:240px;
            --rbb-period-w:170px;
            --rbb-money-w:154px;
            --rbb-money-wide-w:174px;
            --rbb-pct-w:104px;
        }
        #rbb_table thead th { font-size:.72rem; }
        #rbb_table tbody td { font-size:.76rem; }
    }

    @media (min-width:768px) and (max-width:1023px) {
        :root {
            --rbb-code-w:56px;
            --rbb-name-w:170px;
            --rbb-period-w:138px;
            --rbb-money-w:124px;
            --rbb-money-wide-w:142px;
            --rbb-pct-w:86px;
            --rbb-head-h:38px;
        }
        #rbb_table th, #rbb_table td { padding:.43rem .45rem; }
        #rbb_table thead th { font-size:.61rem; }
        #rbb_table tbody td { font-size:.67rem; }
    }

    @media (max-width:767px) {
        :root {
            --rbb-code-w:54px;
            --rbb-name-w:118px;
            --rbb-period-w:112px;
            --rbb-money-w:105px;
            --rbb-money-wide-w:118px;
            --rbb-pct-w:76px;
            --rbb-head-h:38px;
        }

        #filterWrapperRbb { max-height:calc(100vh - 130px); overflow:auto; }
        .rbb-filter-grid { width:100%; }
        .rbb-filter-actions > button:first-child { flex:1 1 auto; }

        /* Rekap mobile menjadi satu baris horizontal agar tidak menghabiskan tinggi layar. */
        .rbb-summary-grid {
            grid-template-columns:none;
            grid-auto-flow:column;
            grid-auto-columns:minmax(108px,38vw);
            gap:.35rem;
            overflow-x:auto;
            overflow-y:hidden;
            padding:0 0 2px;
            scroll-snap-type:x proximity;
            -webkit-overflow-scrolling:touch;
            scrollbar-width:thin;
        }
        .rbb-summary-grid > .rbb-card {
            scroll-snap-align:start;
            min-height:56px;
        }
        .rbb-summary-grid > .rbb-card:last-child:nth-child(odd) { grid-column:auto; }
        .rbb-card { border-radius:.62rem; padding:.38rem .46rem; }
        .rbb-card .label {
            min-height:1.55em;
            font-size:.43rem;
            line-height:1.08;
            letter-spacing:.018em;
        }
        .rbb-card .value { font-size:.72rem; margin-top:.14rem; }
        .rbb-card .sub { font-size:.48rem; margin-top:.14rem; }

        /* Tooltip info dibuat fixed agar tidak terpotong sisi layar mobile. */
        .rbb-info-pop {
            position:fixed;
            left:10px;
            right:10px;
            top:var(--rbb-info-top,88px);
            width:auto;
            max-width:none;
            padding:.52rem .62rem;
            border-radius:.58rem;
            font-size:.61rem;
            line-height:1.35;
            z-index:9999;
            transform:translateY(-.15rem);
        }
        .group:hover .rbb-info-pop,
        .group:focus-within .rbb-info-pop {
            transform:translateY(0);
        }

        #rbb_table_scroller { scrollbar-gutter:auto; }
        #rbb_table th, #rbb_table td { padding:.36rem .38rem; }
        #rbb_table thead th {
            font-size:.55rem; letter-spacing:0; white-space:normal; line-height:1.12;
        }
        #rbb_table tbody td { font-size:.61rem; }
        #rbb_table tbody tr { height:38px; }
        .rbb-total-th { height:34px; }

        /* Pada mobile, nama kantor menjadi kolom utama. Kode disembunyikan saat bukan filter cabang. */
        #rbb_table:not(.rbb-branch-view):not(.rbb-monthly-view) .rbb-col-code { display:none !important; }
        #rbb_table:not(.rbb-branch-view):not(.rbb-monthly-view) .rbb-sticky-name { left:0; z-index:16; }
        #rbb_table:not(.rbb-branch-view):not(.rbb-monthly-view) thead .rbb-sticky-name { z-index:36; }
        #rbb_table:not(.rbb-branch-view):not(.rbb-monthly-view) thead .rbb-total-th.rbb-sticky-name { z-index:35; }

        .rbb-name-text {
            display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;
            overflow:hidden; white-space:normal; line-height:1.15;
        }
    }

    @media (max-width:374px) {
        :root {
            --rbb-name-w:106px;
            --rbb-period-w:104px;
            --rbb-money-w:98px;
            --rbb-money-wide-w:110px;
            --rbb-pct-w:70px;
        }
        #rbb_table thead th { font-size:.51rem; }
        #rbb_table tbody td { font-size:.57rem; }
    }
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


function getRbbSelectedOffice() {
    return document.getElementById('rbb_kantor')?.value || '000';
}

function isRbbBranchFilter() {
    const value = getRbbSelectedOffice();
    return /^\d{3}$/.test(value) && value !== '000';
}

function syncRbbTableState(monthly = false) {
    const table = document.getElementById('rbb_table');
    if (!table) return;
    table.classList.toggle('rbb-branch-view', isRbbBranchFilter());
    table.classList.toggle('rbb-monthly-view', monthly);
}

function closeRbbFilterOnSmallScreen() {
    if (window.innerWidth >= 1280) return;
    const wrapper = document.getElementById('filterWrapperRbb');
    if (!wrapper) return;
    wrapper.classList.add('hidden');
    wrapper.classList.remove('flex');
}

function resetRbbTableScroll() {
    const scroller = document.getElementById('rbb_table_scroller');
    if (!scroller) return;
    scroller.scrollLeft = 0;
    scroller.scrollTop = 0;
}

function rbbNameHeader(sortable = true) {
    if (isRbbBranchFilter()) return '';
    const sortClass = sortable ? ' rbb-sort' : '';
    const action = sortable ? ` onclick="sortRbb('nama_kantor')"` : '';
    const icon = sortable ? rbbSortIcon('nama_kantor') : '';
    return `<th class="rbb-th rbb-sticky-name rbb-col-name text-left${sortClass}"${action}>Kantor${icon}</th>`;
}

function rbbNameCell(row, sticky = true) {
    if (isRbbBranchFilter()) return '';
    const stickyClass = sticky ? ' rbb-sticky-name' : '';
    const name = rbbEscape(row?.nama_kantor || '-');
    return `<td class="rbb-col-name${stickyClass} text-left font-bold text-slate-800" title="${name}"><div class="rbb-name-text">${name}</div></td>`;
}

function toggleRbbFilter() {
    const el = document.getElementById('filterWrapperRbb');
    if (!el) return;
    el.classList.toggle('hidden');
    el.classList.toggle('flex');
}

function rbbSummaryToggleIcon() {
    if (rbbSummaryVisible) {
        // Rekap sedang terlihat; ikon ini berarti aksi berikutnya adalah menyembunyikan.
        return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18"></path><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 4.2A10.5 10.5 0 0 1 12 4c5 0 9 4 10 8a13.7 13.7 0 0 1-2.1 4.2"></path><path d="M6.6 6.6A13.8 13.8 0 0 0 2 12c1 4 5 8 10 8a10.7 10.7 0 0 0 5.4-1.5"></path></svg>`;
    }
    // Rekap sedang tersembunyi; ikon ini berarti aksi berikutnya adalah menampilkan.
    return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
}

function syncRbbSummaryUI() {
    const box = document.getElementById('rbb_summary');
    box?.classList.toggle('hidden', !rbbSummaryVisible);

    const label = rbbSummaryVisible ? 'Sembunyikan rekap' : 'Tampilkan rekap';
    ['rbb_summary_toggle', 'rbb_summary_toggle_mobile'].forEach(id => {
        const btn = document.getElementById(id);
        if (!btn) return;
        btn.innerHTML = rbbSummaryToggleIcon();
        btn.setAttribute('aria-label', label);
        btn.setAttribute('title', label);
        btn.setAttribute('aria-expanded', String(rbbSummaryVisible));
    });
}

function toggleRbbSummary() {
    rbbSummaryVisible = !rbbSummaryVisible;
    syncRbbSummaryUI();
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


function initRbbInfoTooltip() {
    const btn = document.querySelector('.rbb-info-btn');
    const pop = document.querySelector('.rbb-info-pop');
    if (!btn || !pop) return;

    const positionTooltip = () => {
        if (window.innerWidth >= 768) {
            pop.style.removeProperty('--rbb-info-top');
            return;
        }

        const rect = btn.getBoundingClientRect();
        const preferredTop = rect.bottom + 7;
        const estimatedHeight = Math.max(pop.offsetHeight || 0, 48);
        const maxTop = Math.max(10, window.innerHeight - estimatedHeight - 10);
        pop.style.setProperty('--rbb-info-top', `${Math.min(preferredTop, maxTop)}px`);
    };

    btn.addEventListener('click', (event) => {
        event.stopPropagation();
        positionTooltip();
        btn.focus();
    });
    btn.addEventListener('focus', positionTooltip);
    btn.addEventListener('mouseenter', positionTooltip);
    window.addEventListener('resize', positionTooltip);
    window.addEventListener('orientationchange', () => setTimeout(positionTooltip, 120));

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.rbb-info-btn') && !event.target.closest('.rbb-info-pop')) {
            btn.blur();
        }
    });
}

async function initRbbPage() {
    syncRbbSummaryUI();
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
    const kantor = getRbbSelectedOffice();
    const harian = document.getElementById('rbb_harian_date')?.value || '';
    const compareMode = document.getElementById('rbb_compare_mode')?.value || 'auto';

    if (rbbAbort) rbbAbort.abort();
    rbbAbort = new AbortController();

    syncRbbTableState(false);
    loading?.classList.remove('hidden');
    body.innerHTML = `<tr><td colspan="12" class="py-12 text-center text-slate-400 italic">Sedang mengambil data...</td></tr>`;

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
        } else {
            rbbSort = { key: 'kode_kantor', asc: true };
        }

        renderRbbSummary();
        renderRbbTable();
        resetRbbTableScroll();
        closeRbbFilterOnSmallScreen();
    } catch (e) {
        if (e.name !== 'AbortError') {
            body.innerHTML = `<tr><td colspan="12" class="py-12 text-center text-red-500 font-bold">Error: ${rbbEscape(e.message)}</td></tr>`;
        }
    } finally {
        loading?.classList.add('hidden');
    }
}

function renderRbbSummary() {
    const box = document.getElementById('rbb_summary');
    if (!box) return;

    if (rbbMeta.compare_mode === 'history') {
        box.className = 'rbb-summary-grid mb-2.5 md:mb-3 shrink-0';
        const cards = [
            [`Realisasi ${rbbMeta.tahun || 'Tahun Ini'}`, rbbGrand.realisasi_bulan_ini, 'text-blue-700', 'bg-blue-50'],
            ['Run Off', rbbGrand.run_off, 'text-orange-700', 'bg-orange-50'],
            ['Growth', rbbGrand.growth, Number(rbbGrand.growth || 0) >= 0 ? 'text-emerald-700' : 'text-red-700', 'bg-blue-50'],
            ['Selisih YoY', rbbGrand.selisih, Number(rbbGrand.selisih || 0) >= 0 ? 'text-emerald-700' : 'text-red-700', 'bg-slate-50'],
            ['% YoY', rbbGrand.yoy_persen, Number(rbbGrand.yoy_persen || 0) >= 0 ? 'text-emerald-700' : 'text-red-700', 'bg-emerald-50', true],
        ];

        box.innerHTML = cards.map(([label, value, color, bg, percent, plain]) => `
            <div class="rbb-card ${bg}">
                <div class="label">${label}</div>
                <div class="value ${color}">${plain ? rbbEscape(value) : (percent ? rbbPct(value) : `Rp ${rbbNominal(value)}`)}</div>
                <div class="sub">${rbbMonthlyRows.length ? `${rbbMonthlyRows.length} bulan` : `${rbbRows.length} kantor`}</div>
            </div>
        `).join('');
        syncRbbSummaryUI();
        return;
    }

    box.className = 'rbb-summary-grid mb-2.5 md:mb-3 shrink-0';
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
    syncRbbSummaryUI();
}

function renderRbbTable() {
    const head = document.getElementById('rbb_head');
    const body = document.getElementById('rbb_body');
    if (!head || !body) return;

    const monthly = rbbMonthlyRows.length > 0;
    syncRbbTableState(monthly);

    if (rbbMeta.compare_mode === 'history') {
        monthly ? renderRbbHistoryMonthlyTable(head, body) : renderRbbHistoryTable(head, body);
        return;
    }

    if (monthly) {
        renderRbbMonthlyTable(head, body);
        return;
    }

    const hideName = isRbbBranchFilter();
    const totalRow = hideName ? '' : renderRbbStandardTotalRow();
    const columnCount = hideName ? 7 : 8;

    head.innerHTML = `
        <tr>
            <th class="rbb-th rbb-sticky-code rbb-col-code rbb-sort text-left" onclick="sortRbb('kode_kantor')">Kode${rbbSortIcon('kode_kantor')}</th>
            ${rbbNameHeader(true)}
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('nilai_rbb')">RBB (Rb)${rbbSortIcon('nilai_rbb')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('realisasi_bulan_ini')">Realisasi (Rb)${rbbSortIcon('realisasi_bulan_ini')}</th>
            <th class="rbb-th rbb-col-pct rbb-sort text-right" onclick="sortRbb('persentase_rbb_bulan_ini')">% RBB${rbbSortIcon('persentase_rbb_bulan_ini')}</th>
            <th class="rbb-th rbb-col-money-wide rbb-sort text-right" onclick="sortRbb('kekurangan_sd_bulan_lalu')">Kurang Lalu (Rb)${rbbSortIcon('kekurangan_sd_bulan_lalu')}</th>
            <th class="rbb-th rbb-col-money-wide rbb-sort text-right" onclick="sortRbb('total_beban_target')">Total Beban (Rb)${rbbSortIcon('total_beban_target')}</th>
            <th class="rbb-th rbb-col-pct rbb-sort text-right" onclick="sortRbb('persentase_rbb_plus_kekurangan')">% Beban${rbbSortIcon('persentase_rbb_plus_kekurangan')}</th>
        </tr>
        ${totalRow}
    `;

    if (!rbbRows.length) {
        body.innerHTML = `<tr><td colspan="${columnCount}" class="py-12 text-center text-slate-400 italic">Tidak ada data.</td></tr>`;
        return;
    }

    const rows = sortedRbbRows(rbbRows);
    body.innerHTML = rows.map(r => {
        const pctOk = Number(r.persentase_rbb_bulan_ini || 0) >= 100;
        const bebanOk = Number(r.persentase_rbb_plus_kekurangan || 0) >= 100;
        return `
            <tr>
                <td class="rbb-sticky-code rbb-col-code text-left rbb-number text-slate-500">${rbbEscape(r.kode_kantor)}</td>
                ${rbbNameCell(r, true)}
                <td class="rbb-col-money text-right rbb-number text-indigo-700">${rbbTableNominal(r.nilai_rbb)}</td>
                <td class="rbb-col-money text-right rbb-number text-blue-700">${rbbTableNominal(r.realisasi_bulan_ini)}</td>
                <td class="rbb-col-pct text-right font-black ${pctOk ? 'text-emerald-700 bg-emerald-50' : 'text-red-700 bg-red-50'}">${rbbPct(r.persentase_rbb_bulan_ini)}</td>
                <td class="rbb-col-money-wide text-right rbb-number text-red-700">${rbbTableNominal(r.kekurangan_sd_bulan_lalu)}</td>
                <td class="rbb-col-money-wide text-right rbb-number text-orange-700">${rbbTableNominal(r.total_beban_target)}</td>
                <td class="rbb-col-pct text-right font-black ${bebanOk ? 'text-emerald-700 bg-emerald-50' : 'text-orange-700 bg-orange-50'}">${rbbPct(r.persentase_rbb_plus_kekurangan)}</td>
            </tr>
        `;
    }).join('');
}

function renderRbbStandardTotalRow() {
    return `
        <tr class="rbb-total-row">
            <th class="rbb-total-th rbb-sticky-code rbb-col-code text-left font-black"></th>
            <th class="rbb-total-th rbb-sticky-name rbb-col-name text-left font-black">TOTAL</th>
            <th class="rbb-total-th rbb-col-money text-right rbb-number text-indigo-700">${rbbTableNominal(rbbGrand.nilai_rbb)}</th>
            <th class="rbb-total-th rbb-col-money text-right rbb-number text-blue-700">${rbbTableNominal(rbbGrand.realisasi_bulan_ini)}</th>
            <th class="rbb-total-th rbb-col-pct text-right font-black text-emerald-700">${rbbPct(rbbGrand.persentase_rbb_bulan_ini)}</th>
            <th class="rbb-total-th rbb-col-money-wide text-right rbb-number text-red-700">${rbbTableNominal(rbbGrand.kekurangan_sd_bulan_lalu)}</th>
            <th class="rbb-total-th rbb-col-money-wide text-right rbb-number text-orange-700">${rbbTableNominal(rbbGrand.total_beban_target)}</th>
            <th class="rbb-total-th rbb-col-pct text-right font-black text-orange-700">${rbbPct(rbbGrand.persentase_rbb_plus_kekurangan)}</th>
        </tr>
    `;
}


function rbbSumRows(rows, key) {
    return (rows || []).reduce((total, row) => total + Number(row?.[key] || 0), 0);
}

function rbbLatestMonthlyRow() {
    if (!rbbMonthlyRows.length) return {};
    return [...rbbMonthlyRows].sort((a, b) => {
        return String(b.periode || '').localeCompare(String(a.periode || ''));
    })[0] || {};
}

function getRbbMonthlyTotals() {
    const nilaiRbb = rbbSumRows(rbbMonthlyRows, 'nilai_rbb');
    const realisasi = rbbSumRows(rbbMonthlyRows, 'realisasi_bulan_ini');
    const selisih = realisasi - nilaiRbb;
    const persentase = nilaiRbb !== 0 ? (realisasi / nilaiRbb) * 100 : 0;
    const kekurangan = rbbSumRows(rbbMonthlyRows, 'kekurangan');

    return {
        nilai_rbb: nilaiRbb,
        realisasi_bulan_ini: realisasi,
        persentase_rbb_bulan_ini: persentase,
        selisih,
        kekurangan
    };
}

function getRbbHistoryMonthlyTotals() {
    const current = rbbSumRows(rbbMonthlyRows, 'realisasi_bulan_ini');
    const previous = rbbSumRows(rbbMonthlyRows, 'realisasi_tahun_lalu');

    // Backend sekarang mengirim Run Off dan Growth murni per bulan.
    // Karena itu baris TOTAL harus menjumlahkan seluruh bulan yang tampil,
    // bukan lagi mengambil posisi bulan terbaru.
    const runOff = rbbSumRows(rbbMonthlyRows, 'run_off');
    const growth = rbbSumRows(rbbMonthlyRows, 'growth');

    const selisih = current - previous;
    const yoyPersen = previous !== 0 ? (selisih / previous) * 100 : 0;
    const growthPersen = runOff !== 0 ? (growth / runOff) * 100 : 0;

    return {
        realisasi_bulan_ini: current,
        realisasi_tahun_lalu: previous,
        run_off: runOff,
        growth: growth,
        growth_persen: growthPersen,
        selisih,
        yoy_persen: yoyPersen
    };
}

function renderRbbHistoryTable(head, body) {
    const year = rbbMeta.tahun || 'Tahun Ini';
    const prevYear = rbbMeta.tahun_pembanding || 'Tahun Lalu';
    const hideName = isRbbBranchFilter();
    const columnCount = hideName ? 8 : 9;

    head.innerHTML = `
        <tr>
            <th class="rbb-th rbb-sticky-code rbb-col-code rbb-sort text-left" onclick="sortRbb('kode_kantor')">Kode${rbbSortIcon('kode_kantor')}</th>
            ${rbbNameHeader(true)}
            <th class="rbb-th rbb-col-money-wide rbb-sort text-right" onclick="sortRbb('realisasi_bulan_ini')">Realisasi ${year} (Rb)${rbbSortIcon('realisasi_bulan_ini')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('run_off')">Run Off (Rb)${rbbSortIcon('run_off')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('growth')">Growth (Rb)${rbbSortIcon('growth')}</th>
            <th class="rbb-th rbb-col-pct rbb-sort text-right" onclick="sortRbb('growth_persen')">% Growth${rbbSortIcon('growth_persen')}</th>
            <th class="rbb-th rbb-col-money-wide rbb-sort text-right" onclick="sortRbb('realisasi_tahun_lalu')">Realisasi ${prevYear} (Rb)${rbbSortIcon('realisasi_tahun_lalu')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('selisih')">Selisih YoY (Rb)${rbbSortIcon('selisih')}</th>
            <th class="rbb-th rbb-col-pct rbb-sort text-right" onclick="sortRbb('yoy_persen')">% YoY${rbbSortIcon('yoy_persen')}</th>
        </tr>
        ${renderRbbHistoryTotalRow(false)}
    `;

    if (!rbbRows.length) {
        body.innerHTML = `<tr><td colspan="${columnCount}" class="py-12 text-center text-slate-400 italic">Tidak ada data realisasi history.</td></tr>`;
        return;
    }

    const rows = sortedRbbRows(rbbRows);
    body.innerHTML = rows.map(r => {
        const growth = Number(r.growth || 0);
        const selisih = Number(r.selisih || 0);
        const growthColor = growth >= 0 ? 'text-emerald-700' : 'text-red-700';
        const yoyColor = selisih >= 0 ? 'text-emerald-700' : 'text-red-700';
        return `
            <tr>
                <td class="rbb-sticky-code rbb-col-code text-left rbb-number text-slate-500">${rbbEscape(r.kode_kantor)}</td>
                ${rbbNameCell(r, true)}
                <td class="rbb-col-money-wide text-right rbb-number text-blue-700">${rbbTableNominal(r.realisasi_bulan_ini)}</td>
                <td class="rbb-col-money text-right rbb-number text-orange-700">${rbbTableNominal(r.run_off)}</td>
                <td class="rbb-col-money text-right rbb-number ${growthColor}">${growth >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(growth))}</td>
                <td class="rbb-col-pct text-right font-black ${growthColor}">${rbbPct(r.growth_persen)}</td>
                <td class="rbb-col-money-wide text-right rbb-number text-slate-700">${rbbTableNominal(r.realisasi_tahun_lalu)}</td>
                <td class="rbb-col-money text-right rbb-number ${yoyColor}">${selisih >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(selisih))}</td>
                <td class="rbb-col-pct text-right font-black ${yoyColor}">${rbbPct(r.yoy_persen)}</td>
            </tr>
        `;
    }).join('');
}

function renderRbbHistoryMonthlyTable(head, body) {
    const year = rbbMeta.tahun || 'Tahun Ini';
    const prevYear = rbbMeta.tahun_pembanding || 'Tahun Lalu';
    const hideName = isRbbBranchFilter();
    const columnCount = hideName ? 8 : 9;

    head.innerHTML = `
        <tr>
            <th class="rbb-th rbb-sticky-period rbb-col-period rbb-sort text-left" onclick="sortRbb('periode')">Bulan${rbbSortIcon('periode')}</th>
            ${hideName ? '' : '<th class="rbb-th rbb-col-name text-left">Kantor</th>'}
            <th class="rbb-th rbb-col-money-wide rbb-sort text-right" onclick="sortRbb('realisasi_bulan_ini')">Realisasi ${year} (Rb)${rbbSortIcon('realisasi_bulan_ini')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('run_off')">Run Off (Rb)${rbbSortIcon('run_off')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('growth')">Growth (Rb)${rbbSortIcon('growth')}</th>
            <th class="rbb-th rbb-col-pct rbb-sort text-right" onclick="sortRbb('growth_persen')">% Growth${rbbSortIcon('growth_persen')}</th>
            <th class="rbb-th rbb-col-money-wide rbb-sort text-right" onclick="sortRbb('realisasi_tahun_lalu')">Realisasi ${prevYear} (Rb)${rbbSortIcon('realisasi_tahun_lalu')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('selisih')">Selisih YoY (Rb)${rbbSortIcon('selisih')}</th>
            <th class="rbb-th rbb-col-pct rbb-sort text-right" onclick="sortRbb('yoy_persen')">% YoY${rbbSortIcon('yoy_persen')}</th>
        </tr>
        ${renderRbbHistoryTotalRow(true)}
    `;

    if (!rbbMonthlyRows.length) {
        body.innerHTML = `<tr><td colspan="${columnCount}" class="py-12 text-center text-slate-400 italic">Tidak ada breakdown history bulanan.</td></tr>`;
        return;
    }

    const rows = sortedRbbRows(rbbMonthlyRows);
    body.innerHTML = rows.map(r => {
        const growth = Number(r.growth || 0);
        const selisih = Number(r.selisih || 0);
        const growthColor = growth >= 0 ? 'text-emerald-700' : 'text-red-700';
        const yoyColor = selisih >= 0 ? 'text-emerald-700' : 'text-red-700';
        return `
            <tr>
                <td class="rbb-sticky-period rbb-col-period text-left font-black text-slate-800">${rbbEscape(rbbMonthLabel(r.periode))}</td>
                ${hideName ? '' : rbbNameCell(r, false)}
                <td class="rbb-col-money-wide text-right rbb-number text-blue-700">${rbbTableNominal(r.realisasi_bulan_ini)}</td>
                <td class="rbb-col-money text-right rbb-number text-orange-700">${rbbTableNominal(r.run_off)}</td>
                <td class="rbb-col-money text-right rbb-number ${growthColor}">${growth >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(growth))}</td>
                <td class="rbb-col-pct text-right font-black ${growthColor}">${rbbPct(r.growth_persen)}</td>
                <td class="rbb-col-money-wide text-right rbb-number text-slate-700">${rbbTableNominal(r.realisasi_tahun_lalu)}</td>
                <td class="rbb-col-money text-right rbb-number ${yoyColor}">${selisih >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(selisih))}</td>
                <td class="rbb-col-pct text-right font-black ${yoyColor}">${rbbPct(r.yoy_persen)}</td>
            </tr>
        `;
    }).join('');
}

function renderRbbHistoryTotalRow(monthly) {
    const totals = monthly ? getRbbHistoryMonthlyTotals() : rbbGrand;
    const growth = Number(totals.growth || 0);
    const selisih = Number(totals.selisih || 0);
    const growthColor = growth >= 0 ? 'text-emerald-700' : 'text-red-700';
    const yoyColor = selisih >= 0 ? 'text-emerald-700' : 'text-red-700';
    const hideName = isRbbBranchFilter();

    const leading = monthly
        ? `<th class="rbb-total-th rbb-sticky-period rbb-col-period text-left font-black">TOTAL</th>${hideName ? '' : '<th class="rbb-total-th rbb-col-name text-left font-black"></th>'}`
        : `<th class="rbb-total-th rbb-sticky-code rbb-col-code text-left font-black">${hideName ? 'TOTAL' : ''}</th>${hideName ? '' : '<th class="rbb-total-th rbb-sticky-name rbb-col-name text-left font-black">TOTAL</th>'}`;

    return `
        <tr class="rbb-total-row">
            ${leading}
            <th class="rbb-total-th rbb-col-money-wide text-right rbb-number text-blue-700">${rbbTableNominal(totals.realisasi_bulan_ini)}</th>
            <th class="rbb-total-th rbb-col-money text-right rbb-number text-orange-700">${rbbTableNominal(totals.run_off)}</th>
            <th class="rbb-total-th rbb-col-money text-right rbb-number ${growthColor}">${growth >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(growth))}</th>
            <th class="rbb-total-th rbb-col-pct text-right font-black ${growthColor}">${rbbPct(totals.growth_persen)}</th>
            <th class="rbb-total-th rbb-col-money-wide text-right rbb-number text-slate-700">${rbbTableNominal(totals.realisasi_tahun_lalu)}</th>
            <th class="rbb-total-th rbb-col-money text-right rbb-number ${yoyColor}">${selisih >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(selisih))}</th>
            <th class="rbb-total-th rbb-col-pct text-right font-black ${yoyColor}">${rbbPct(totals.yoy_persen)}</th>
        </tr>
    `;
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
    const hideName = isRbbBranchFilter();
    const columnCount = hideName ? 6 : 7;

    head.innerHTML = `
        <tr>
            <th class="rbb-th rbb-sticky-period rbb-col-period rbb-sort text-left" onclick="sortRbb('periode')">Bulan${rbbSortIcon('periode')}</th>
            ${hideName ? '' : '<th class="rbb-th rbb-col-name text-left">Kantor</th>'}
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('nilai_rbb')">RBB (Rb)${rbbSortIcon('nilai_rbb')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('realisasi_bulan_ini')">Realisasi (Rb)${rbbSortIcon('realisasi_bulan_ini')}</th>
            <th class="rbb-th rbb-col-pct rbb-sort text-right" onclick="sortRbb('persentase_rbb_bulan_ini')">% RBB${rbbSortIcon('persentase_rbb_bulan_ini')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('selisih')">Selisih (Rb)${rbbSortIcon('selisih')}</th>
            <th class="rbb-th rbb-col-money rbb-sort text-right" onclick="sortRbb('kekurangan')">Kekurangan (Rb)${rbbSortIcon('kekurangan')}</th>
        </tr>
        ${renderRbbMonthlyTotalRow()}
    `;

    if (!rbbMonthlyRows.length) {
        body.innerHTML = `<tr><td colspan="${columnCount}" class="py-12 text-center text-slate-400 italic">Tidak ada breakdown bulanan.</td></tr>`;
        return;
    }

    const rows = sortedRbbRows(rbbMonthlyRows);
    body.innerHTML = rows.map(r => {
        const pctOk = Number(r.persentase_rbb_bulan_ini || 0) >= 100;
        const selisih = Number(r.selisih || 0);
        const selisihColor = selisih >= 0 ? 'text-emerald-700' : 'text-red-700';

        return `
            <tr>
                <td class="rbb-sticky-period rbb-col-period text-left font-black text-slate-800">${rbbEscape(rbbMonthLabel(r.periode))}</td>
                ${hideName ? '' : rbbNameCell(r, false)}
                <td class="rbb-col-money text-right rbb-number text-indigo-700">${rbbTableNominal(r.nilai_rbb)}</td>
                <td class="rbb-col-money text-right rbb-number text-blue-700">${rbbTableNominal(r.realisasi_bulan_ini)}</td>
                <td class="rbb-col-pct text-right font-black ${pctOk ? 'text-emerald-700 bg-emerald-50' : 'text-red-700 bg-red-50'}">${rbbPct(r.persentase_rbb_bulan_ini)}</td>
                <td class="rbb-col-money text-right rbb-number ${selisihColor}">${selisih >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(selisih))}</td>
                <td class="rbb-col-money text-right rbb-number text-orange-700">${rbbTableNominal(r.kekurangan)}</td>
            </tr>
        `;
    }).join('');
}

function renderRbbMonthlyTotalRow() {
    const hideName = isRbbBranchFilter();
    const totals = getRbbMonthlyTotals();
    const selisih = Number(totals.selisih || 0);
    const selisihColor = selisih >= 0 ? 'text-emerald-700' : 'text-red-700';
    const pctColor = Number(totals.persentase_rbb_bulan_ini || 0) >= 100
        ? 'text-emerald-700'
        : 'text-red-700';

    return `
        <tr class="rbb-total-row">
            <th class="rbb-total-th rbb-sticky-period rbb-col-period text-left font-black">TOTAL</th>
            ${hideName ? '' : '<th class="rbb-total-th rbb-col-name"></th>'}
            <th class="rbb-total-th rbb-col-money text-right rbb-number text-indigo-700">${rbbTableNominal(totals.nilai_rbb)}</th>
            <th class="rbb-total-th rbb-col-money text-right rbb-number text-blue-700">${rbbTableNominal(totals.realisasi_bulan_ini)}</th>
            <th class="rbb-total-th rbb-col-pct text-right font-black ${pctColor}">${rbbPct(totals.persentase_rbb_bulan_ini)}</th>
            <th class="rbb-total-th rbb-col-money text-right rbb-number ${selisihColor}">${selisih >= 0 ? '+' : '-'} ${rbbTableNominal(Math.abs(selisih))}</th>
            <th class="rbb-total-th rbb-col-money text-right rbb-number text-orange-700">${rbbTableNominal(totals.kekurangan)}</th>
        </tr>
    `;
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
        let csv = `${title}\tRealisasi ${year}\tRun Off\tGrowth\t% Growth\tRealisasi ${prevYear}\tSelisih YoY\t% YoY\n`;
        exportRows.forEach(r => {
            const first = rbbMonthlyRows.length ? rbbMonthLabel(r.periode) : `'${r.kode_kantor}`;
            csv += `${first}\t${r.nama_kantor}\t${Math.round(r.realisasi_bulan_ini || 0)}\t${Math.round(r.run_off || 0)}\t${Math.round(r.growth || 0)}\t${r.growth_persen}\t${Math.round(r.realisasi_tahun_lalu || 0)}\t${Math.round(r.selisih || 0)}\t${r.yoy_persen}\n`;
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

window.addEventListener('resize', () => {
    clearTimeout(window.__rbbResizeTimer);
    window.__rbbResizeTimer = setTimeout(() => renderRbbTable(), 120);
});
window.addEventListener('DOMContentLoaded', initRbbPage);
</script>

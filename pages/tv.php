<?php include 'components/display_direksi/tv_login_modal.php'; ?>

<div id="tvWrapper" class="tv-shell max-w-[1920px] mx-auto px-3 sm:px-4 lg:px-8 py-3 lg:py-5 h-screen max-h-screen overflow-hidden flex flex-col bg-gray-50 text-gray-900 font-sans transition-colors duration-500">
    
    <?php include 'components/display_direksi/tv_header.php'; ?>

    <div id="loadingDash" class="flex flex-col justify-center items-center flex-grow min-h-0">
        <div class="animate-spin rounded-full h-10 w-10 border-t-4 border-b-4 border-blue-500 mb-4"></div>
        <span class="text-sm text-gray-500 font-semibold animate-pulse">Menyiapkan Data Konsolidasi...</span>
    </div>

    <div id="contentDash" class="hidden relative flex-grow w-full min-h-0 overflow-hidden">
        
        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-100 z-10 overflow-hidden" id="slide-1">
            <?php include 'components/display_direksi/slide_1_makro.php'; ?>
        </div>
        
        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-2">
            <?php include 'components/display_direksi/slide_2_realisasi.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-3">
            <?php include 'components/display_direksi/slide_2_tren.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-4">
            <?php include 'components/display_direksi/slide_3_runoff.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-5">
            <?php include 'components/display_direksi/slide_5_portofolio.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-6">
            <?php include 'components/display_direksi/slide_4_kredit_dpk.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-7">
            <?php include 'components/display_direksi/slide_6_npl.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-8">
            <?php include 'components/display_direksi/slide_7_dpk.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-9">
            <?php include 'components/display_direksi/slide_8_tabungan.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-10">
            <?php include 'components/display_direksi/slide_9_aset_laba.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-11">
            <?php include 'components/display_direksi/slide_10_pendapatan_beban.php'; ?>
        </div>

    </div>
</div>

<style>
    /* Styling scrollbar umum */
    :root {
        --tv-base-font-size: 16px;
        --tv-wrapper-max-width: 1920px;
        --tv-wrapper-padding-x: 1rem;
        --tv-wrapper-padding-y: 1rem;
    }
    html, body { height: 100%; overflow: hidden; font-size: var(--tv-base-font-size); }
    body { background: #f8fafc; }
    .tv-shell {
        container-type: size;
        max-width: var(--tv-wrapper-max-width) !important;
        padding-left: var(--tv-wrapper-padding-x) !important;
        padding-right: var(--tv-wrapper-padding-x) !important;
        padding-top: var(--tv-wrapper-padding-y) !important;
        padding-bottom: var(--tv-wrapper-padding-y) !important;
    }
    .tv-slide { height: 100%; }
    .tv-fit {
        --tv-fit-scale: 1;
        height: calc(100% / var(--tv-fit-scale));
        width: calc(100% / var(--tv-fit-scale));
        min-height: 0;
        overflow: hidden;
        transform: scale(var(--tv-fit-scale));
        transform-origin: top left;
        will-change: transform;
    }
    .tv-card { min-height: 0; overflow: hidden; color: #111827; }
    .tv-list { min-height: 0; overflow: hidden; }
    .tv-chart { min-height: 0; height: 100%; }
    .tv-chart canvas { display: block; width: 100% !important; height: 100% !important; }
    .tv-makro-slide { min-height: 0; overflow: hidden; }
    .tv-makro-kpi-grid { min-height: 0; }
    .tv-makro-kpi { min-height: 5.15rem; }
    .tv-makro-kpi [id$="_pill"] > div {
        flex-wrap: nowrap;
        min-width: 0;
        gap: .35rem !important;
        overflow: hidden;
    }
    .tv-makro-kpi [id$="_pill"] .bg-gray-100 {
        min-width: 0;
        max-width: 68%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tv-makro-main-grid {
        min-height: 0;
        grid-template-rows: minmax(0, 1fr);
    }
    .tv-makro-panel {
        min-height: 0;
        height: 100%;
    }
    .tv-makro-summary-list {
        display: grid;
        grid-template-rows: repeat(4, minmax(0, 1fr));
        gap: .5rem;
        height: 100%;
    }
    .tv-makro-summary-list > div { margin-bottom: 0 !important; }
    .tv-makro-summary-item {
        min-height: 0;
        overflow: hidden;
    }
    .tv-makro-summary-item > div:first-child { min-width: 0; }
    .tv-makro-summary-item p { line-height: 1.1; }
    .tv-makro-ratio-grid {
        min-height: 0;
        height: auto;
        align-content: start;
        grid-template-rows: none;
    }
    .tv-ratio-card { min-height: 0; overflow: hidden; }
    .tv-ratio-card > div {
        align-items: flex-end;
        min-width: 0;
    }
    .tv-ratio-card [id^="delta_rasio_"] {
        min-width: 3.75rem;
        max-width: 4.4rem;
        overflow: visible;
    }
    .tv-ratio-card [id^="delta_rasio_"] .leading-tight {
        width: 100%;
    }
    .tv-ratio-card [id^="delta_rasio_"] .whitespace-nowrap {
        white-space: nowrap;
    }
    #box_top_biaya .group > div:first-child > span:last-child {
        font-size: .92rem;
        line-height: 1;
    }
    .tv-makro-detail-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .45rem;
        overflow: hidden;
    }
    .tv-makro-detail-item {
        min-width: 0;
        border: 1px solid #eef2f7;
        border-radius: .65rem;
        background: #f8fafc;
        padding: .42rem .48rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .45rem;
    }
    .tv-makro-detail-item .label {
        color: #64748b;
        font-size: .56rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .tv-makro-detail-item .sub {
        margin-top: .18rem;
        color: #94a3b8;
        font-size: .55rem;
        line-height: 1;
        font-weight: 700;
    }
    .tv-makro-detail-item .value {
        flex: 0 0 auto;
        max-width: 54%;
        text-align: right;
        font-size: .64rem;
        line-height: 1;
        font-weight: 950;
        white-space: nowrap;
    }
    .tv-trend-shell { gap: .75rem; }
    .tv-trend-header { padding-bottom: .7rem; }
    .tv-trend-title { line-height: 1.08; }
    .tv-trend-net { max-width: 320px; }
    .tv-trend-summary { min-width: 0; }
    .tv-trend-summary > div:first-child,
    .tv-trend-panel h3,
    .tv-trend-panel p { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .tv-trend-grid {
        grid-template-rows: repeat(2, minmax(0, 1fr));
        min-height: 0;
    }
    .tv-trend-panel { min-height: 0; }
    .tv-trend-panel .tv-chart { min-height: 118px; }
    .tv-realisasi-shell { gap: 0; }
    .tv-realisasi-summary { min-height: 0; }
    .tv-realisasi-period-grid { min-height: 0; }
    .tv-realisasi-period { min-width: 0; overflow: hidden; }
    .tv-real-zero-row { min-width: 0; }
    .tv-real-zero-row p { line-height: 1.05; }
    @container (max-width: 980px) {
        .tv-realisasi-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .tv-realisasi-period-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @container (max-width: 620px) {
        .tv-realisasi-header {
            flex-direction: column;
        }
        .tv-realisasi-header > div:last-child {
            width: 100%;
        }
        .tv-realisasi-period-grid {
            grid-template-columns: minmax(0, 1fr);
            overflow-y: auto;
        }
    }
    .tv-trend-perk-tooltip {
        position: absolute;
        z-index: 80;
        width: min(390px, calc(100vw - 24px));
        max-height: min(390px, calc(100vh - 24px));
        overflow: auto;
        opacity: 0;
        pointer-events: none;
        transform: translateZ(0);
        transition: opacity .12s ease;
        border-radius: .85rem;
        border: 1px solid rgba(148, 163, 184, .26);
        background: rgba(15, 23, 42, .96);
        color: #f8fafc;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .28);
        padding: .8rem;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #94a3b8 transparent;
    }
    .tv-trend-perk-tooltip::-webkit-scrollbar { width: 7px; }
    .tv-trend-perk-tooltip::-webkit-scrollbar-track { background: transparent; }
    .tv-trend-perk-tooltip::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 999px; }
    .tv-trend-perk-title {
        font-size: .88rem;
        line-height: 1.1;
        font-weight: 950;
        margin-bottom: .25rem;
    }
    .tv-trend-perk-sub {
        font-size: .58rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #93c5fd;
        margin-bottom: .55rem;
    }
    .tv-trend-perk-formula {
        border: 1px solid rgba(147, 197, 253, .18);
        border-radius: .65rem;
        background: rgba(37, 99, 235, .12);
        padding: .45rem .55rem;
        margin-bottom: .6rem;
    }
    .tv-trend-perk-formula span {
        display: block;
        color: #93c5fd;
        font-size: .55rem;
        font-weight: 950;
        letter-spacing: .12em;
        line-height: 1;
        text-transform: uppercase;
        margin-bottom: .25rem;
    }
    .tv-trend-perk-formula strong {
        display: block;
        color: #f8fafc;
        font-size: .68rem;
        line-height: 1.25;
        font-weight: 900;
    }
    .tv-trend-perk-list {
        display: grid;
        gap: .32rem;
    }
    .tv-trend-perk-row {
        display: grid;
        grid-template-columns: 4.8rem minmax(0, 1fr);
        gap: .45rem;
        align-items: start;
        border-radius: .55rem;
        background: rgba(255, 255, 255, .06);
        padding: .38rem .45rem;
    }
    .tv-trend-perk-row .code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: .68rem;
        line-height: 1.2;
        font-weight: 900;
        color: #bfdbfe;
    }
    .tv-trend-perk-row .name {
        min-width: 0;
        font-size: .68rem;
        line-height: 1.2;
        font-weight: 800;
        color: #e5e7eb;
    }
    .tv-distribution-chart { position: relative; height: 100%; }
    .tv-distribution-chart canvas { display: block; width: 100% !important; height: 100% !important; cursor: pointer; }
    .tv-distribution-toggle {
        display: inline-flex;
        align-items: center;
        gap: .2rem;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        padding: .18rem;
        background: #f8fafc;
        flex: 0 0 auto;
    }
    .tv-distribution-toggle button {
        height: 28px;
        border-radius: 999px;
        padding: 0 .65rem;
        font-size: .68rem;
        font-weight: 900;
        color: #64748b;
    }
    .tv-distribution-toggle button.is-active {
        background: #ffffff;
        color: #2563eb;
        box-shadow: 0 1px 4px rgba(15, 23, 42, .12);
    }
    .tv-distribution-table {
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #eef2f7;
        border-radius: .85rem;
        background: #fff;
        scrollbar-width: thin;
        min-height: 0;
    }
    .tv-distribution-table::-webkit-scrollbar { width: 6px; }
    .tv-distribution-table::-webkit-scrollbar-track { background: transparent; }
    .tv-distribution-table::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    .tv-distribution-table table { width: 100%; border-collapse: collapse; font-size: .72rem; }
    .tv-distribution-table th {
        position: sticky;
        top: 0;
        z-index: 1;
        text-align: left;
        padding: .65rem .75rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-size: .62rem;
        background: #f8fafc;
    }
    .tv-distribution-table td {
        padding: .62rem .75rem;
        border-top: 1px solid #eef2f7;
        color: #1e3a8a;
        font-weight: 800;
    }
    .tv-distribution-table td:last-child,
    .tv-distribution-table th:last-child { text-align: right; }
    .tv-distribution-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .65rem;
    }
    .tv-distribution-summary-card {
        border-radius: .9rem;
        background: #f8fafc;
        padding: .7rem;
        min-width: 0;
    }
    .tv-distribution-summary-card .label {
        font-size: .58rem;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: #94a3b8;
        font-weight: 900;
        margin-bottom: .35rem;
    }
    .tv-distribution-summary-card .value {
        color: #111827;
        font-size: .86rem;
        font-weight: 1000;
        line-height: 1.15;
    }
    .tv-distribution-summary-card .sub {
        margin-top: .25rem;
        color: #64748b;
        font-size: .62rem;
        font-weight: 800;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .bar-fill { transition: height 1s cubic-bezier(0.4, 0, 0.2, 1), width 1s ease-in-out; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .tv-floating-controls {
        position: fixed;
        z-index: 80;
        top: .75rem;
        left: .75rem;
        max-width: calc(100vw - 1.5rem);
        color: #111827;
        transition: opacity .18s ease, transform .18s ease;
    }
    .tv-control-tab {
        height: 38px;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border: 1px solid #dbe3ef;
        border-radius: 999px;
        padding: 0 .75rem;
        background: rgba(255,255,255,.92);
        color: #0f172a;
        font-weight: 900;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .12);
        backdrop-filter: blur(12px);
    }
    .tv-filter-icon { color: #2563eb; }
    .tv-control-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .18);
        opacity: 0;
        pointer-events: none;
        border: 0;
    }
    .tv-control-panel {
        position: fixed;
        left: .75rem;
        top: 4.2rem;
        width: auto;
        max-width: min(calc(100vw - 1.5rem), 1080px);
        opacity: 0;
        transform: translateY(-6px);
        pointer-events: none;
        transition: opacity .18s ease, transform .18s ease;
        border: 1px solid #dbe3ef;
        border-radius: 1rem;
        padding: .55rem;
        background: #ffffff;
        box-shadow: 0 20px 50px rgba(15, 23, 42, .16);
        z-index: 81;
    }
    .tv-floating-controls.is-open .tv-control-backdrop { opacity: 1; pointer-events: auto; }
    .tv-floating-controls.is-open .tv-control-panel {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
    .tv-header-toggle {
        position: fixed;
        right: .9rem;
        bottom: .9rem;
        z-index: 90;
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dbe3ef;
        border-radius: 999px;
        padding: 0;
        background: rgba(255,255,255,.94);
        color: #2563eb;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .12);
        backdrop-filter: blur(12px);
        transition: background .18s ease, color .18s ease, opacity .18s ease, transform .18s ease;
    }
    .tv-header-toggle:hover { background: #eff6ff; color: #1d4ed8; }
    .tv-header-eye { display: block; }
    .tv-header-eye-closed { display: none; }
    body.tv-header-hidden .tv-header-eye-open { display: none; }
    body.tv-header-hidden .tv-header-eye-closed { display: block; }
    body.tv-header-hidden .tv-floating-controls,
    body.tv-header-hidden .tv-slide-controls {
        opacity: 0;
        pointer-events: none;
        transform: translateY(-10px);
    }
    body.tv-header-hidden .tv-floating-controls.is-open .tv-control-panel,
    body.tv-header-hidden .tv-floating-controls.is-open .tv-control-backdrop {
        opacity: 0;
        pointer-events: none;
    }
    body.tv-header-hidden .tv-header-toggle {
        opacity: .72;
    }
    .tv-control-row {
        display: flex;
        align-items: center;
        gap: .45rem;
        min-width: 0;
        flex-wrap: nowrap;
        overflow-x: visible;
        overflow-y: hidden;
        scrollbar-width: thin;
        max-width: min(calc(100vw - 2.6rem), 1040px);
    }
    .tv-control-hint { font-size: .68rem; font-weight: 900; color: #64748b; white-space: nowrap; padding: 0 .35rem; }
    .tv-filter-apply-status {
        height: 42px;
        display: inline-flex;
        align-items: center;
        border-radius: .85rem;
        padding: 0 .75rem;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #c2410c;
        font-size: .68rem;
        font-weight: 1000;
        white-space: nowrap;
        flex: 0 0 auto;
    }
    .tv-filter-apply-status.hidden { display: none; }
    .tv-icon-btn, .tv-nav-btn, .tv-segment, .tv-field, .tv-zoom-control {
        border: 1px solid #dbe3ef;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
    }
    .tv-icon-btn, .tv-nav-btn {
        height: 42px;
        min-width: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .75rem;
        color: #334155;
        transition: background .2s ease, color .2s ease;
    }
    .tv-nav-btn { height: 34px; min-width: 34px; border: 0; box-shadow: none; background: transparent; }
    .tv-nav-btn:hover, .tv-icon-btn:hover { background: #eff6ff; color: #2563eb; }
    .tv-segment { height: 42px; display: inline-flex; align-items: center; gap: .25rem; border-radius: .85rem; padding: .25rem; color: #334155; }
    .tv-zoom-control {
        height: 42px;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border-radius: .85rem;
        padding: .35rem .45rem .35rem .65rem;
        color: #334155;
        flex: 0 0 auto;
    }
    .tv-zoom-control > span {
        font-size: .62rem;
        line-height: 1;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
    }
    .tv-zoom-btn,
    .tv-zoom-value {
        height: 28px;
        min-width: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .55rem;
        color: #334155;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: .75rem;
        font-weight: 1000;
        line-height: 1;
    }
    .tv-zoom-value {
        min-width: 48px;
        padding: 0 .45rem;
        color: #2563eb;
        background: #eff6ff;
        border-color: #bfdbfe;
    }
    .tv-zoom-btn:hover,
    .tv-zoom-value:hover { background: #dbeafe; color: #1d4ed8; }
    .tv-field {
        height: 42px;
        display: flex;
        align-items: center;
        gap: .5rem;
        border-radius: .85rem;
        padding: .35rem .7rem;
        color: #334155;
        min-width: clamp(150px, 16vw, 205px);
    }
    .tv-field-wide { min-width: clamp(220px, 23vw, 360px); flex: 0 0 auto; }
    .tv-field span { font-size: .62rem; line-height: 1; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
    .tv-field input, .tv-field select {
        min-width: 0;
        width: 100%;
        border: 0;
        outline: 0;
        background: #ffffff;
        color: #0f172a;
        font-weight: 800;
        font-size: .82rem;
        appearance: auto;
        -webkit-appearance: auto;
        pointer-events: auto;
    }
    .tv-date-input {
        min-width: 0;
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: #0f172a;
        font-weight: 800;
        font-size: .82rem;
        padding: 0;
    }
    .tv-date-input::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: .9;
    }
    .tv-field-trigger {
        width: 100%;
        min-width: 0;
        border: 0;
        outline: 0;
        background: transparent;
        color: #0f172a;
        font-weight: 800;
        font-size: .82rem;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 0;
        cursor: pointer;
    }
    .tv-native-select.sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        margin: -1px;
        padding: 0;
        border: 0;
        clip: rect(0, 0, 0, 0);
        overflow: hidden;
    }
    .tv-field select:disabled { opacity: .45; }
    .tv-select-modal.hidden { display: none; }
    .tv-select-modal {
        position: fixed;
        inset: 0;
        z-index: 140;
    }
    .tv-select-modal-backdrop {
        position: absolute;
        inset: 0;
        border: 0;
        background: rgba(15, 23, 42, .38);
    }
    .tv-select-modal-sheet {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: min(92vw, 640px);
        max-height: min(78vh, 760px);
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 1rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .24);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .tv-select-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: 1rem 1rem .85rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .tv-select-modal-title {
        font-size: 1rem;
        font-weight: 900;
        color: #0f172a;
    }
    .tv-select-modal-close {
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        border: 1px solid #dbe3ef;
        background: #fff;
        color: #475569;
        font-weight: 900;
    }
    .tv-select-modal-options {
        padding: .65rem;
        overflow-y: auto;
        display: grid;
        gap: .45rem;
    }
    .tv-select-option {
        width: 100%;
        text-align: left;
        border: 1px solid #dbe3ef;
        background: #fff;
        color: #0f172a;
        border-radius: .8rem;
        padding: .8rem .9rem;
        font-size: .92rem;
        font-weight: 800;
    }
    .tv-select-option.is-active {
        border-color: #60a5fa;
        background: #eff6ff;
        color: #1d4ed8;
    }
    #tv_scope_badge {
        display: flex;
        align-items: center;
        color: #2563eb;
        font-weight: 1000;
        font-size: .78rem;
        white-space: nowrap;
        max-width: 140px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tv-slide-controls {
        position: fixed;
        right: 4rem;
        top: .75rem;
        z-index: 85;
        display: flex;
        align-items: center;
        gap: .35rem;
        border: 1px solid #dbe3ef;
        background: rgba(255,255,255,.94);
        color: #0f172a;
        border-radius: 999px;
        padding: .35rem;
        box-shadow: 0 16px 40px rgba(15, 23, 42, .16);
        backdrop-filter: blur(14px);
        transition: opacity .18s ease, transform .18s ease;
    }
    .tv-slide-btn {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        color: #334155;
        transition: background .18s ease, color .18s ease;
    }
    .tv-slide-btn:hover { background: #eff6ff; color: #2563eb; }
    .tv-slide-count {
        min-width: 44px;
        text-align: center;
        font-size: .82rem;
        font-weight: 1000;
        color: #2563eb;
    }
    [class*="helpdesk" i],
    [id*="helpdesk" i],
    [class*="help-desk" i],
    [id*="help-desk" i] { display: none !important; }
    
    /* ========================================= */
    /* LOGIKA DARK MODE (Otomatis Tertimpa)      */
    /* ========================================= */
    body.dark-mode { background-color: #0f172a; }
    body.dark-mode #tvWrapper { background-color: #111827 !important; color: #f3f4f6 !important; }
    body.dark-mode .bg-white { background-color: #1f2937 !important; border-color: #374151 !important; }
    body.dark-mode .tv-card { color: #f8fafc; }
    body.dark-mode .text-gray-800, 
    body.dark-mode .text-gray-900 { color: #f3f4f6 !important; }
    body.dark-mode .text-gray-700 { color: #e5e7eb !important; }
    body.dark-mode .text-gray-500 { color: #9ca3af !important; }
    body.dark-mode .bg-gray-50 { background-color: #111827 !important; border-color: #374151 !important; }
    body.dark-mode .bg-gray-100 { background-color: #374151 !important; }
    body.dark-mode .text-gray-600,
    body.dark-mode .text-gray-700 { color: #d1d5db !important; }
    body.dark-mode .border-gray-100, 
    body.dark-mode .border-gray-200 { border-color: #374151 !important; }
    body.dark-mode .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; }
    body.dark-mode .tv-icon-btn,
    body.dark-mode .tv-header-toggle,
    body.dark-mode .tv-control-tab,
    body.dark-mode .tv-control-panel,
    body.dark-mode .tv-control-backdrop,
    body.dark-mode .tv-slide-controls,
    body.dark-mode .tv-segment,
    body.dark-mode .tv-zoom-control,
    body.dark-mode .tv-field {
        background: #1f2937;
        border-color: #374151;
        color: #f8fafc;
        box-shadow: none;
    }
    body.dark-mode .tv-nav-btn { color: #e5e7eb; }
    body.dark-mode .tv-filter-apply-status {
        background: rgba(251, 146, 60, .12);
        border-color: rgba(251, 146, 60, .35);
        color: #fdba74;
    }
    body.dark-mode .tv-zoom-control > span { color: #9ca3af; }
    body.dark-mode .tv-zoom-btn,
    body.dark-mode .tv-zoom-value {
        background: #111827;
        border-color: #374151;
        color: #bfdbfe;
    }
    body.dark-mode .tv-field input,
    body.dark-mode .tv-field select {
        color: #f8fafc;
        background: #1f2937;
    }
    body.dark-mode .tv-field-trigger { color: #f8fafc; }
    body.dark-mode .tv-field span { color: #9ca3af; }
    body.dark-mode .tv-select-modal-sheet,
    body.dark-mode .tv-select-modal-close,
    body.dark-mode .tv-select-option {
        background: #1f2937;
        border-color: #374151;
        color: #f8fafc;
    }
    body.dark-mode .tv-select-modal-title { color: #f8fafc; }
    body.dark-mode .tv-select-modal-head { border-color: #374151; }
    body.dark-mode .tv-select-option.is-active {
        background: rgba(37, 99, 235, .22);
        border-color: #60a5fa;
        color: #bfdbfe;
    }
    body.dark-mode .tv-distribution-toggle,
    body.dark-mode .tv-distribution-table,
    body.dark-mode .tv-distribution-summary-card {
        background: #111827;
        border-color: #374151;
    }
    body.dark-mode .tv-distribution-toggle button.is-active {
        background: #1f2937;
        color: #bfdbfe;
    }
    body.dark-mode .tv-distribution-table th { background: #111827; color: #9ca3af; }
    body.dark-mode .tv-distribution-table td { border-color: #374151; color: #bfdbfe; }
    body.dark-mode .tv-distribution-summary-card .value { color: #f8fafc; }
    body.dark-mode .tv-makro-detail-item {
        background: #111827;
        border-color: #374151;
    }
    body.dark-mode .tv-makro-detail-item .value { color: #e5e7eb; }
    body.dark-mode #tv_scope_badge,
    body.dark-mode .tv-slide-count { color: #bfdbfe; }
    body.dark-mode .tv-slide-btn { color: #e5e7eb; }
    body.dark-mode .tv-control-hint { color: #9ca3af; }

    body[data-tv-screen-profile="tv_nhd"] {
        --tv-base-font-size: 13px;
        --tv-wrapper-max-width: 100vw;
        --tv-wrapper-padding-x: .7rem;
        --tv-wrapper-padding-y: .65rem;
    }
    body[data-tv-screen-profile="tv_sd"] {
        --tv-base-font-size: 14px;
        --tv-wrapper-max-width: 100vw;
        --tv-wrapper-padding-x: .75rem;
        --tv-wrapper-padding-y: .75rem;
    }
    body[data-tv-screen-profile="tv_xga"] {
        --tv-base-font-size: 14px;
        --tv-wrapper-max-width: 100vw;
        --tv-wrapper-padding-x: .85rem;
        --tv-wrapper-padding-y: .85rem;
    }
    body[data-tv-screen-profile="tv_hd"] {
        --tv-base-font-size: 15px;
        --tv-wrapper-max-width: 100vw;
        --tv-wrapper-padding-x: 1rem;
        --tv-wrapper-padding-y: 1rem;
    }
    body[data-tv-screen-profile="tv_fhd"] {
        --tv-base-font-size: 16px;
        --tv-wrapper-max-width: 100vw;
        --tv-wrapper-padding-x: 1rem;
        --tv-wrapper-padding-y: 1rem;
    }
    body[data-tv-screen-profile="tv_qhd"] {
        --tv-base-font-size: 18px;
        --tv-wrapper-max-width: 100vw;
        --tv-wrapper-padding-x: 1.15rem;
        --tv-wrapper-padding-y: 1.1rem;
    }
    body[data-tv-screen-profile="tv_4k"] {
        --tv-base-font-size: 21px;
        --tv-wrapper-max-width: 100vw;
        --tv-wrapper-padding-x: 1.35rem;
        --tv-wrapper-padding-y: 1.2rem;
    }
    body[data-tv-screen-profile="tablet"] {
        --tv-base-font-size: 14px;
        --tv-wrapper-max-width: 1024px;
    }
    body[data-tv-screen-profile="mobile"] {
        --tv-base-font-size: 13px;
        --tv-wrapper-max-width: 640px;
    }

    @media (max-height: 760px), (max-width: 900px) {
        #tvWrapper { padding-top: .75rem; padding-bottom: .75rem; }
        .tv-card { padding: .75rem !important; border-radius: .75rem !important; }
        .tv-compact-gap { gap: .75rem !important; }
        .tv-list > div { margin-bottom: .5rem !important; }
        .tv-list .bar-fill, .tv-list .bg-gray-700, .tv-list .bg-gray-100 { height: .35rem !important; }
        .tv-control-panel { width: max-content; max-width: calc(100vw - 1.5rem); }
        .tv-control-row { flex-wrap: nowrap; }
        .tv-field { min-width: 150px; flex: 0 0 auto; }
        .tv-field-wide { min-width: 220px; flex: 0 0 auto; }
        #tv_scope_badge { max-width: 120px; overflow: hidden; text-overflow: ellipsis; }
        .tv-trend-shell { gap: .55rem; }
        .tv-trend-header { gap: .45rem !important; padding-bottom: .55rem; }
        .tv-trend-title { font-size: .95rem !important; }
        .tv-trend-subtitle { display: none; }
        .tv-trend-net { min-width: 190px; max-width: 250px; padding: .45rem .6rem !important; }
        .tv-trend-summary-grid { gap: .45rem !important; }
        .tv-trend-summary { padding: .5rem .6rem !important; border-radius: .7rem !important; }
        .tv-trend-summary .text-base,
        .tv-trend-summary .md\:text-xl { font-size: 1rem !important; line-height: 1.1 !important; }
        .tv-trend-summary [id^="delta_tren_"] > div {
            margin-top: .3rem !important;
            padding-top: .3rem !important;
            gap: .35rem !important;
        }
        .tv-trend-grid { gap: .55rem !important; }
        .tv-trend-panel { padding: .65rem !important; border-radius: .75rem !important; }
        .tv-trend-panel .tv-chart { min-height: 92px; }
        .tv-trend-perk-tooltip {
            width: min(320px, calc(100vw - 20px));
            max-height: min(290px, calc(100vh - 20px));
            padding: .65rem;
        }
        .tv-trend-perk-formula { padding: .38rem .45rem; margin-bottom: .45rem; }
        .tv-trend-perk-formula strong { font-size: .6rem; }
        .tv-trend-perk-row {
            grid-template-columns: 4rem minmax(0, 1fr);
            padding: .32rem .4rem;
        }
        .tv-trend-perk-row .code,
        .tv-trend-perk-row .name {
            font-size: .6rem;
        }
        .tv-makro-detail-grid { gap: .32rem; }
        .tv-makro-detail-item { padding: .35rem .4rem; border-radius: .55rem; }
        .tv-makro-detail-item .label { font-size: .5rem; }
        .tv-makro-detail-item .sub { display: none; }
        .tv-makro-detail-item .value { font-size: .58rem; }
    }

    @media (min-width: 1500px) {
        html, body { font-size: 17px; }
    }

    html.tv-mobile-layout,
    body.tv-mobile-layout { height: auto; min-height: 100%; overflow-y: auto; overflow-x: hidden; }
    body.tv-mobile-layout #tvWrapper {
        height: auto !important;
        max-height: none !important;
        min-height: 100vh;
        overflow: visible !important;
        padding-bottom: 5.75rem !important;
    }
    body.tv-mobile-layout #contentDash {
        position: relative;
        display: block;
        height: auto;
        min-height: 0;
        overflow: visible !important;
    }
    body.tv-mobile-layout .tv-slide {
        position: relative !important;
        inset: auto !important;
        height: auto !important;
        min-height: auto;
        overflow: visible !important;
        opacity: 1 !important;
        z-index: auto !important;
        pointer-events: auto !important;
        display: none;
    }
    body.tv-mobile-layout .tv-slide:not(.pointer-events-none) { display: block; }
    body.tv-mobile-layout .tv-fit {
        height: auto !important;
        width: auto !important;
        min-height: auto;
        overflow: visible !important;
        transform: none !important;
    }
    body.tv-mobile-layout .tv-card,
    body.tv-mobile-layout .tv-list {
        overflow: visible !important;
    }
    body.tv-mobile-layout .tv-chart {
        height: clamp(260px, 58vh, 520px);
        min-height: 260px;
    }
    body.tv-mobile-layout .tv-fit.grid,
    body.tv-mobile-layout .tv-fit .grid {
        grid-auto-rows: auto;
    }
    body.tv-mobile-layout .tv-trend-header > div:first-child {
        flex-direction: column;
    }
    body.tv-mobile-layout .tv-trend-net {
        width: 100%;
        max-width: none;
    }
    body.tv-mobile-layout .tv-trend-summary-grid,
    body.tv-mobile-layout .tv-trend-grid {
        grid-template-columns: 1fr !important;
        grid-template-rows: none;
    }
    body.tv-mobile-layout .tv-makro-detail-grid {
        grid-template-columns: 1fr;
    }
    body.tv-mobile-layout .tv-trend-panel .tv-chart {
        height: 260px;
        min-height: 260px;
    }
    body.tv-mobile-layout .tv-makro-slide,
    body.tv-mobile-layout .tv-makro-panel,
    body.tv-mobile-layout .tv-makro-summary-list,
    body.tv-mobile-layout .tv-makro-ratio-grid {
        height: auto !important;
        min-height: auto !important;
    }
    body.tv-mobile-layout .tv-makro-main-grid,
    body.tv-mobile-layout .tv-makro-ratio-grid {
        grid-template-columns: 1fr !important;
        grid-template-rows: none !important;
    }
    body.tv-mobile-layout .tv-makro-summary-list {
        display: block;
    }
    body.tv-mobile-layout .tv-floating-controls,
    body.tv-mobile-layout .tv-slide-controls {
        position: fixed;
    }

    body.tv-desktop-layout .tv-shell {
        width: 100%;
        max-width: none !important;
    }
    body.tv-desktop-layout .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    body.tv-desktop-layout .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
    body.tv-desktop-layout .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    body.tv-desktop-layout .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
    body.tv-desktop-layout .lg\:grid-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)) !important; }
    body.tv-desktop-layout .lg\:grid-cols-12 { grid-template-columns: repeat(12, minmax(0, 1fr)) !important; }
    body.tv-desktop-layout .xl\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
    body.tv-desktop-layout .md\:col-span-1 { grid-column: span 1 / span 1 !important; }
    body.tv-desktop-layout .lg\:col-span-2 { grid-column: span 2 / span 2 !important; }
    body.tv-desktop-layout .lg\:col-span-3 { grid-column: span 3 / span 3 !important; }
    body.tv-desktop-layout .lg\:col-span-4 { grid-column: span 4 / span 4 !important; }
    body.tv-desktop-layout .lg\:col-span-5 { grid-column: span 5 / span 5 !important; }
    body.tv-desktop-layout .lg\:col-span-7 { grid-column: span 7 / span 7 !important; }
    body.tv-desktop-layout .lg\:col-span-8 { grid-column: span 8 / span 8 !important; }
    body.tv-desktop-layout .lg\:col-span-9 { grid-column: span 9 / span 9 !important; }
    body.tv-desktop-layout .md\:flex-row,
    body.tv-desktop-layout .xl\:flex-row { flex-direction: row !important; }
    body.tv-desktop-layout .md\:items-center,
    body.tv-desktop-layout .xl\:items-center { align-items: center !important; }
    body.tv-desktop-layout .md\:w-auto,
    body.tv-desktop-layout .xl\:w-auto { width: auto !important; }
    body.tv-desktop-layout .md\:w-1\/2 { width: 50% !important; }
    body.tv-desktop-layout .lg\:w-1\/3 { width: 33.333333% !important; }
    body.tv-desktop-layout .md\:gap-1 { gap: .25rem !important; }
    body.tv-desktop-layout .md\:gap-2 { gap: .5rem !important; }
    body.tv-desktop-layout .md\:gap-3 { gap: .75rem !important; }
    body.tv-desktop-layout .md\:gap-4 { gap: 1rem !important; }
    body.tv-desktop-layout .md\:gap-5 { gap: 1.25rem !important; }
    body.tv-desktop-layout .md\:gap-6 { gap: 1.5rem !important; }
    body.tv-desktop-layout .tv-makro-slide {
        display: grid !important;
        grid-template-rows: auto minmax(0, 1fr);
        gap: .75rem !important;
    }
    body.tv-desktop-layout .tv-makro-kpi-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: .65rem !important;
    }
    body.tv-desktop-layout .tv-makro-kpi {
        grid-column: span 1 / span 1 !important;
        padding: .72rem .82rem !important;
        min-height: 4.85rem;
    }
    body.tv-desktop-layout .tv-makro-kpi h3 {
        font-size: clamp(1rem, 1.8vw, 1.38rem) !important;
        line-height: 1.05 !important;
        margin-bottom: .35rem !important;
    }
    body.tv-desktop-layout .tv-makro-kpi p {
        font-size: .56rem !important;
        line-height: 1.05 !important;
    }
    body.tv-desktop-layout .tv-makro-main-grid {
        grid-template-columns: minmax(0, 3fr) minmax(0, 5.5fr) minmax(0, 3.5fr) !important;
        gap: .75rem !important;
    }
    body.tv-desktop-layout .tv-makro-main-grid > .tv-card {
        grid-column: auto !important;
        padding: .85rem !important;
    }
    body.tv-desktop-layout .tv-makro-panel h3 {
        font-size: .72rem !important;
        line-height: 1.12 !important;
        margin-bottom: .55rem !important;
        padding-bottom: .45rem !important;
    }
    body.tv-desktop-layout .tv-makro-summary-list {
        gap: .45rem !important;
    }
    body.tv-desktop-layout .tv-makro-summary-item {
        padding: .48rem .55rem !important;
    }
    body.tv-desktop-layout .tv-makro-summary-item p:first-child {
        font-size: .55rem !important;
    }
    body.tv-desktop-layout .tv-makro-summary-item p:last-child {
        font-size: .78rem !important;
        line-height: 1.05 !important;
        white-space: nowrap;
    }
    body.tv-desktop-layout .tv-makro-ratio-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        grid-template-rows: none !important;
        align-content: start !important;
        height: auto !important;
        gap: .62rem .72rem !important;
    }
    body.tv-desktop-layout .tv-ratio-card {
        min-height: 4.25rem !important;
        padding: .68rem .66rem !important;
        border-radius: .7rem !important;
    }
    body.tv-desktop-layout .tv-ratio-card span[id^="txt_rasio_"] {
        font-size: clamp(.96rem, 1.32vw, 1.16rem) !important;
        line-height: 1.05 !important;
        letter-spacing: 0 !important;
    }
    body.tv-desktop-layout .tv-ratio-card [id^="delta_rasio_"] {
        min-width: 3.95rem;
        max-width: 4.45rem;
    }
    body.tv-desktop-layout .tv-ratio-card [id^="delta_rasio_"] .text-\[8px\],
    body.tv-desktop-layout .tv-ratio-card [id^="delta_rasio_"] .md\:text-\[9px\] {
        font-size: .49rem !important;
        line-height: 1.05 !important;
    }
    body.tv-desktop-layout .tv-ratio-card [id^="delta_rasio_"] .text-\[10px\],
    body.tv-desktop-layout .tv-ratio-card [id^="delta_rasio_"] .md\:text-\[11px\] {
        font-size: .56rem !important;
        line-height: 1.05 !important;
    }
    body.tv-desktop-layout .tv-ratio-card > span:first-child {
        font-size: .52rem !important;
        line-height: 1.05 !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    body.tv-desktop-layout #box_top_biaya .group {
        margin-bottom: .8rem !important;
    }
    body.tv-desktop-layout #box_top_biaya .group > div:first-child > span:last-child {
        font-size: .96rem !important;
        line-height: 1 !important;
    }
    body.tv-desktop-layout #box_top_biaya .group .text-xs,
    body.tv-desktop-layout #box_top_biaya .group .text-\[10px\] {
        line-height: 1.05 !important;
    }

    @media (max-width: 1023px) {
        body:not(.tv-desktop-layout) {
            min-height: 100%;
            overflow-y: auto;
            overflow-x: hidden;
        }
        html, body { height: auto; min-height: 100%; overflow-y: auto; overflow-x: hidden; }
        body:not(.tv-desktop-layout) #tvWrapper {
            height: auto !important;
            max-height: none !important;
            min-height: 100vh;
            overflow: visible !important;
            padding-bottom: 5.75rem;
        }
        body:not(.tv-desktop-layout) #contentDash {
            position: relative;
            display: block;
            height: auto;
            min-height: 0;
            overflow: visible !important;
        }
        body:not(.tv-desktop-layout) .tv-slide {
            position: relative !important;
            inset: auto !important;
            height: auto !important;
            min-height: auto;
            overflow: visible !important;
            opacity: 1 !important;
            z-index: auto !important;
            pointer-events: auto !important;
            display: none;
        }
        body:not(.tv-desktop-layout) .tv-slide:not(.pointer-events-none) { display: block; }
        body:not(.tv-desktop-layout) .tv-fit {
            height: auto !important;
            width: auto !important;
            min-height: auto;
            overflow: visible !important;
            transform: none !important;
        }
        body:not(.tv-desktop-layout) .tv-card,
        body:not(.tv-desktop-layout) .tv-list {
            overflow: visible !important;
        }
        body:not(.tv-desktop-layout) .tv-chart {
            height: clamp(260px, 58vh, 520px);
            min-height: 260px;
        }
        body:not(.tv-desktop-layout) .tv-fit.grid,
        body:not(.tv-desktop-layout) .tv-fit .grid {
            grid-auto-rows: auto;
        }
        body:not(.tv-desktop-layout) .tv-trend-header > div:first-child {
            flex-direction: column;
        }
        body:not(.tv-desktop-layout) .tv-trend-net {
            width: 100%;
            max-width: none;
        }
        body:not(.tv-desktop-layout) .tv-trend-summary-grid,
        body:not(.tv-desktop-layout) .tv-trend-grid {
            grid-template-columns: 1fr !important;
            grid-template-rows: none;
        }
        body:not(.tv-desktop-layout) .tv-makro-detail-grid {
            grid-template-columns: 1fr;
        }
        body:not(.tv-desktop-layout) .tv-trend-panel .tv-chart {
            height: 260px;
            min-height: 260px;
        }
        body:not(.tv-desktop-layout) .tv-makro-slide,
        body:not(.tv-desktop-layout) .tv-makro-panel,
        body:not(.tv-desktop-layout) .tv-makro-summary-list,
        body:not(.tv-desktop-layout) .tv-makro-ratio-grid {
            height: auto !important;
            min-height: auto !important;
        }
        body:not(.tv-desktop-layout) .tv-makro-main-grid,
        body:not(.tv-desktop-layout) .tv-makro-ratio-grid {
            grid-template-columns: 1fr !important;
            grid-template-rows: none !important;
        }
        body:not(.tv-desktop-layout) .tv-makro-summary-list {
            display: block;
        }
        body:not(.tv-desktop-layout) .tv-floating-controls,
        body:not(.tv-desktop-layout) .tv-slide-controls {
            position: fixed;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php include 'components/display_direksi/tv_scripts.php'; ?>

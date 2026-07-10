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
            <?php include 'components/display_direksi/slide_2_tren.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-3">
            <?php include 'components/display_direksi/slide_3_runoff.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-4">
            <?php include 'components/display_direksi/slide_4_kredit_dpk.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-5">
            <?php include 'components/display_direksi/slide_5_portofolio.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-6">
            <?php include 'components/display_direksi/slide_6_npl.php'; ?>
        </div>

        <div class="tv-slide absolute inset-0 transition-opacity duration-1000 opacity-0 pointer-events-none z-0 overflow-hidden" id="slide-7">
            <?php include 'components/display_direksi/slide_7_dpk.php'; ?>
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
    .tv-fit { height: 100%; min-height: 0; overflow: hidden; }
    .tv-card { min-height: 0; overflow: hidden; color: #111827; }
    .tv-list { min-height: 0; overflow: hidden; }
    .tv-chart { min-height: 0; height: 100%; }
    .tv-chart canvas { display: block; width: 100% !important; height: 100% !important; }
    .bar-fill { transition: height 1s cubic-bezier(0.4, 0, 0.2, 1), width 1s ease-in-out; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .tv-floating-controls {
        position: fixed;
        z-index: 80;
        bottom: .75rem;
        left: .75rem;
        max-width: calc(100vw - 1.5rem);
        color: #111827;
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
        bottom: 4.2rem;
        width: auto;
        max-width: min(calc(100vw - 1.5rem), 1080px);
        opacity: 0;
        transform: translateY(6px);
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
    .tv-icon-btn, .tv-nav-btn, .tv-segment, .tv-field {
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
        right: .9rem;
        bottom: .9rem;
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
    body.dark-mode .tv-control-tab,
    body.dark-mode .tv-control-panel,
    body.dark-mode .tv-control-backdrop,
    body.dark-mode .tv-slide-controls,
    body.dark-mode .tv-segment,
    body.dark-mode .tv-field {
        background: #1f2937;
        border-color: #374151;
        color: #f8fafc;
        box-shadow: none;
    }
    body.dark-mode .tv-nav-btn { color: #e5e7eb; }
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
    body.dark-mode #tv_scope_badge,
    body.dark-mode .tv-slide-count { color: #bfdbfe; }
    body.dark-mode .tv-slide-btn { color: #e5e7eb; }
    body.dark-mode .tv-control-hint { color: #9ca3af; }

    body[data-tv-screen-profile="tv_nhd"] {
        --tv-base-font-size: 11px;
        --tv-wrapper-max-width: 854px;
        --tv-wrapper-padding-x: .7rem;
        --tv-wrapper-padding-y: .65rem;
    }
    body[data-tv-screen-profile="tv_sd"] {
        --tv-base-font-size: 12px;
        --tv-wrapper-max-width: 960px;
        --tv-wrapper-padding-x: .75rem;
        --tv-wrapper-padding-y: .75rem;
    }
    body[data-tv-screen-profile="tv_xga"] {
        --tv-base-font-size: 13px;
        --tv-wrapper-max-width: 1120px;
        --tv-wrapper-padding-x: .85rem;
        --tv-wrapper-padding-y: .85rem;
    }
    body[data-tv-screen-profile="tv_hd"] {
        --tv-base-font-size: 14px;
        --tv-wrapper-max-width: 1400px;
        --tv-wrapper-padding-x: 1rem;
        --tv-wrapper-padding-y: 1rem;
    }
    body[data-tv-screen-profile="tv_fhd"] {
        --tv-base-font-size: 16px;
        --tv-wrapper-max-width: 1920px;
        --tv-wrapper-padding-x: 1rem;
        --tv-wrapper-padding-y: 1rem;
    }
    body[data-tv-screen-profile="tv_qhd"] {
        --tv-base-font-size: 18px;
        --tv-wrapper-max-width: 2360px;
        --tv-wrapper-padding-x: 1.15rem;
        --tv-wrapper-padding-y: 1.1rem;
    }
    body[data-tv-screen-profile="tv_4k"] {
        --tv-base-font-size: 21px;
        --tv-wrapper-max-width: 3400px;
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
        min-height: auto;
        overflow: visible !important;
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
    body.tv-mobile-layout .tv-floating-controls,
    body.tv-mobile-layout .tv-slide-controls {
        position: fixed;
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
            min-height: auto;
            overflow: visible !important;
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
        body:not(.tv-desktop-layout) .tv-floating-controls,
        body:not(.tv-desktop-layout) .tv-slide-controls {
            position: fixed;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php include 'components/display_direksi/tv_scripts.php'; ?>

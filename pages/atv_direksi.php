<?php include 'components/display_direksi/tv_login_modal.php'; ?>

<div id="tvWrapper" class="tv-shell max-w-[1920px] mx-auto px-3 sm:px-4 lg:px-8 py-3 lg:py-5 h-screen max-h-screen overflow-hidden flex flex-col bg-gray-50 text-gray-900 font-sans transition-colors duration-500">
    
    <?php include 'components/display_direksi/tv_header.php'; ?>

    <div id="loadingDash" class="flex flex-col justify-center items-center flex-grow min-h-0">
        <div class="animate-spin rounded-full h-10 w-10 border-t-4 border-b-4 border-blue-500 mb-4"></div>
        <span class="text-sm text-gray-500 font-semibold animate-pulse">Menyiapkan Data Konsolidasi...</span>
    </div>

    <div id="contentDash" class="hidden relative flex-grow w-full mt-3 lg:mt-5 min-h-0 overflow-hidden">
        
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

    </div>
</div>

<style>
    /* Styling scrollbar umum */
    html, body { height: 100%; overflow: hidden; }
    body { background: #f9fafb; }
    .tv-shell { container-type: size; }
    .tv-slide { height: 100%; }
    .tv-fit { height: 100%; min-height: 0; overflow: hidden; }
    .tv-card { min-height: 0; overflow: hidden; }
    .tv-list { min-height: 0; overflow: hidden; }
    .tv-chart { min-height: 0; height: 100%; }
    .tv-chart canvas { display: block; width: 100% !important; height: 100% !important; }
    .bar-fill { transition: height 1s cubic-bezier(0.4, 0, 0.2, 1), width 1s ease-in-out; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    
    /* ========================================= */
    /* LOGIKA DARK MODE (Otomatis Tertimpa)      */
    /* ========================================= */
    body.dark-mode { background-color: #111827; }
    body.dark-mode #tvWrapper { background-color: #111827 !important; color: #f3f4f6 !important; }
    body.dark-mode .bg-white { background-color: #1f2937 !important; border-color: #374151 !important; }
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

    @media (max-height: 760px), (max-width: 900px) {
        #tvWrapper { padding-top: .75rem; padding-bottom: .75rem; }
        .tv-card { padding: .75rem !important; border-radius: .75rem !important; }
        .tv-compact-gap { gap: .75rem !important; }
        .tv-list > div { margin-bottom: .5rem !important; }
        .tv-list .bar-fill, .tv-list .bg-gray-700, .tv-list .bg-gray-100 { height: .35rem !important; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php include 'components/display_direksi/tv_scripts.php'; ?>

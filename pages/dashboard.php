<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-[1400px] mx-auto px-2 md:px-4 py-4 md:py-6 bg-gray-50 min-h-screen font-sans">
    
    <?php include 'components/header_filter.php'; ?>

    <div id="loadingDash" class="hidden flex flex-col justify-center items-center py-32">
        <div class="animate-spin rounded-full h-10 w-10 md:h-14 md:w-14 border-t-4 border-b-4 border-blue-600 mb-4"></div>
        <span class="text-xs md:text-sm text-gray-500 font-semibold animate-pulse">Loading data dari database...</span>
    </div>

    <div id="contentDash" class="hidden space-y-4 md:space-y-6 overflow-x-hidden">
        
        <?php include 'components/kpi_cards.php'; ?>

        <?php include 'components/chart_kredit.php'; ?>

        <?php include 'components/chart_runoff_npl.php'; ?>

        <?php include 'components/kinerja_npl.php'; ?>

        <?php include 'components/best_performance.php'; ?>

        <?php include 'components/simpanan.php'; ?>

    </div>
</div>

<style>
  .bar-fill { transition: height 1s cubic-bezier(0.4, 0, 0.2, 1), width 1s ease-in-out; }
  .custom-scrollbar::-webkit-scrollbar { width: 4px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<?php include 'components/scripts.php'; ?>
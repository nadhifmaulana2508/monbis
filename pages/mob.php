<?php
// File: pages/mob.php
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php include __DIR__ . '/components/mob_components/style.php'; ?>

<div class="max-w-[1920px] w-full mx-auto px-2 md:px-4 py-4 md:py-6 h-[calc(100vh-60px)] md:h-[calc(100vh-80px)] flex flex-col font-sans text-slate-800 bg-slate-50 overflow-hidden">
    
    <?php include __DIR__ . '/components/mob_components/filter_main.php'; ?>

    <?php include __DIR__ . '/components/mob_components/table_main.php'; ?>

</div>

<?php include __DIR__ . '/components/mob_components/modal_detail.php'; ?>

<?php include __DIR__ . '/components/mob_components/scripts.php'; ?>
<div class="flex items-center gap-3 mt-4 md:mt-0">
    
    <button onclick="toggleTvTheme()" class="p-3 bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-100 transition-colors z-50 relative" title="Ubah Tema">
        <span id="theme_icon" class="text-xl leading-none">🌙</span>
    </button>

    <div class="flex items-center bg-white rounded-xl p-1 border border-gray-200 shadow-sm relative z-50">
        <button onclick="prevTvSlide()" class="p-2 hover:bg-gray-100 rounded-lg text-gray-700 transition-colors cursor-pointer" title="Slide Sebelumnya">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <span id="slide_indicator" class="px-3 font-bold text-gray-600 text-sm">1 / 4</span>
        <button onclick="nextTvSlide()" class="p-2 hover:bg-gray-100 rounded-lg text-gray-700 transition-colors cursor-pointer" title="Slide Berikutnya">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
    </div>

    <div class="text-lg md:text-xl font-black text-blue-600 bg-blue-50 px-5 py-2.5 rounded-xl border border-blue-100 shadow-sm hidden md:block">
        KONSOLIDASI
    </div>
</div>
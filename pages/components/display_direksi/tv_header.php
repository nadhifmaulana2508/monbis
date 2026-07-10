<div id="tvFloatingControls" class="tv-floating-controls">
    <button type="button" class="tv-control-tab" onclick="toggleTvControls()" title="Buka filter display">
        <span id="theme_icon" class="text-base leading-none">◐</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="tv-filter-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18"/><path d="M6 12h12"/><path d="M10 19h4"/></svg>
        <span id="tv_scope_badge">KONSOLIDASI</span>
    </button>

    <button type="button" id="tvControlBackdrop" class="tv-control-backdrop" onclick="closeTvControlsNow()" aria-label="Tutup panel filter"></button>

    <div class="tv-control-panel">
        <div class="tv-control-row">
            <button onclick="toggleTvTheme()" class="tv-icon-btn" title="Ubah Tema">
                <span id="theme_icon_panel" class="text-lg leading-none">◐</span>
            </button>
            <label class="tv-field">
                <span>Layar</span>
                <select id="tv_screen_profile">
                    <option value="auto">Auto</option>
                    <option value="tv_nhd">nHD - 640 x 360</option>
                    <option value="tv_sd">SD - 854 x 480</option>
                    <option value="tv_xga">XGA - 1024 x 576</option>
                    <option value="tv_hd">HD - 1366 x 768</option>
                    <option value="tv_fhd">Full HD - 1920 x 1080</option>
                    <option value="tv_qhd">QHD - 2560 x 1440</option>
                    <option value="tv_4k">4K UHD - 3840 x 2160</option>
                    <option value="tablet">Tablet</option>
                    <option value="mobile">Mobile</option>
                </select>
            </label>
            <label class="tv-field">
                <span>Closing</span>
                <select id="tv_filter_closing"></select>
            </label>
            <label class="tv-field">
                <span>Harian</span>
                <select id="tv_filter_harian"></select>
            </label>
            <label class="tv-field tv-field-wide">
                <span>Filter</span>
                <select id="tv_filter_kantor">
                    <option value="000">Memuat kantor...</option>
                </select>
            </label>
        </div>
    </div>
</div>

<div class="tv-slide-controls">
    <button onclick="prevTvSlide()" class="tv-slide-btn" title="Slide Sebelumnya">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
    </button>
    <span id="slide_indicator" class="tv-slide-count">1 / 1</span>
    <button onclick="nextTvSlide()" class="tv-slide-btn" title="Slide Berikutnya">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </button>
</div>

<div id="tvFloatingControls" class="tv-floating-controls" onmouseenter="openTvControls()" onmouseleave="closeTvControlsSoon()">
    <button type="button" class="tv-control-tab" onclick="toggleTvControls()" title="Buka filter display">
        <span id="theme_icon" class="text-base leading-none">☾</span>
        <span id="tv_scope_badge">KONSOLIDASI</span>
    </button>

    <div class="tv-control-panel">
        <div class="tv-control-row">
            <button onclick="toggleTvTheme()" class="tv-icon-btn" title="Ubah Tema">
                <span id="theme_icon_panel" class="text-lg leading-none">☾</span>
            </button>
            <label class="tv-field">
                <span>Closing</span>
                <input type="date" id="tv_filter_closing">
            </label>
            <label class="tv-field">
                <span>Harian</span>
                <input type="date" id="tv_filter_harian">
            </label>
            <label class="tv-field">
                <span>Mode</span>
                <select id="tv_filter_mode" onchange="handleTvFilterModeChange()">
                    <option value="konsolidasi">Konsolidasi</option>
                    <option value="kantor">Kanwil / Cabang</option>
                </select>
            </label>
            <label class="tv-field tv-field-wide">
                <span>Kantor</span>
                <select id="tv_filter_kantor" disabled>
                    <option value="000">Memuat kantor...</option>
                </select>
            </label>
            <span class="tv-control-hint">Auto apply</span>
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

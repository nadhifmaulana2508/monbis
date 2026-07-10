<script>
    // ==========================================
    // 1. KIOSK LOGIN & NAVIGATION SYSTEM
    // ==========================================
    const TV_PIN = "123456"; 
    let currentSlide = 0;
    let slideIntervalId = null;
    let totalSlides = 0;

    function checkTvLogin() {
        if (localStorage.getItem('tv_kiosk_authorized') !== 'true') {
            document.getElementById('tvLoginModal').classList.remove('hidden');
            document.getElementById('tvPinInput').focus();
        } else {
            initTvDashboard();
        }
    }

    function verifyTvPin() {
        if (document.getElementById('tvPinInput').value === TV_PIN) {
            localStorage.setItem('tv_kiosk_authorized', 'true'); 
            document.getElementById('tvLoginModal').classList.add('hidden');
            initTvDashboard();
        } else {
            document.getElementById('tvLoginError').classList.remove('hidden');
            setTimeout(() => document.getElementById('tvLoginError').classList.add('hidden'), 3000);
        }
    }

    function showSlide(index) {
        const slides = document.querySelectorAll('.tv-slide');
        if(slides.length === 0) return;
        totalSlides = slides.length;
        currentSlide = ((index % totalSlides) + totalSlides) % totalSlides;

        slides.forEach(s => {
            s.classList.replace('opacity-100', 'opacity-0');
            s.classList.replace('z-10', 'z-0');
            s.classList.add('pointer-events-none');
        });

        slides[currentSlide].classList.replace('opacity-0', 'opacity-100');
        slides[currentSlide].classList.replace('z-0', 'z-10');
        slides[currentSlide].classList.remove('pointer-events-none');

        const indicator = document.getElementById('slide_indicator');
        if(indicator) indicator.innerText = `${currentSlide + 1} / ${totalSlides}`;
    }

    function nextTvSlide() {
        totalSlides = document.querySelectorAll('.tv-slide').length || totalSlides || 1;
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
        resetSlideshowTimer();
    }

    function prevTvSlide() {
        totalSlides = document.querySelectorAll('.tv-slide').length || totalSlides || 1;
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
        resetSlideshowTimer();
    }

    function resetSlideshowTimer() {
        if(slideIntervalId) clearInterval(slideIntervalId);
        slideIntervalId = setInterval(nextTvSlide, 30000); // 30 detik
    }

    // ==========================================
    // 2. HELPER FORMATTER 
    // ==========================================
    const nf = new Intl.NumberFormat('id-ID');
    const fmt = n => nf.format(Number(n || 0));
    const fmtB = n => {
        let num = Number(n||0); let absNum = Math.abs(num);
        if(absNum >= 1e12) return (num/1e12).toFixed(3) + ' T'; 
        if(absNum >= 1e9) return (num/1e9).toFixed(2) + ' M';   
        if(absNum >= 1e6) return (num/1e6).toFixed(1) + ' Jt';  
        return fmt(num);
    };
    const pct = x => (x == null ? '0%' : `${(+x).toFixed(2)}%`);
    const tvLimit = (arr, max = 5) => Array.isArray(arr) ? arr.slice(0, max) : [];
    const unwrapData = (raw, key) => raw?.[key] || raw || {};
    const setText = (id, text) => { const el = document.getElementById(id); if(el) el.textContent = text; };
    const setHtml = (id, html) => { const el = document.getElementById(id); if(el) el.innerHTML = html; };
    const setDeltaSummary = (id, label, val, invertGoodBad = false) => {
        const el = document.getElementById(id);
        if(!el) return;
        const numVal = Number(val || 0);
        const sign = numVal >= 0 ? '+' : '-';
        const isGood = invertGoodBad ? numVal < 0 : numVal > 0;
        const color = numVal === 0 ? 'text-gray-600' : (isGood ? 'text-green-600' : 'text-red-600');
        el.innerHTML = `${label}: <span class="${color} font-black">${sign} Rp ${fmtB(Math.abs(numVal))}</span>`;
    };
    
    const getDeltaHTML = (val, isPercent = false, invertGoodBad = false, tight = false) => {
        let numVal = Number(val || 0);
        let sizeClass = tight ? 'text-[9px] md:text-[11px]' : 'text-[10px] md:text-xs';
        if(numVal === 0) return `<span class="text-gray-500 font-bold ${sizeClass}">Tetap 0</span>`;
        let isGood = invertGoodBad ? numVal < 0 : numVal > 0;
        let color = isGood ? 'text-green-500' : 'text-red-500';
        let icon = numVal > 0 ? '▲' : '▼';
        let displayVal = isPercent ? pct(Math.abs(numVal)) : fmtB(Math.abs(numVal));
        return `<span class="${color} font-black ${sizeClass}">${icon} ${displayVal}</span>`;
    };

    function getRasioColor(key, val) {
        let num = Number(val);
        if(key === 'bopo') return num < 80 ? 'text-green-500' : (num < 85 ? 'text-yellow-500' : 'text-red-500');
        if(key === 'ldr')  return (num >= 80 && num <= 90) ? 'text-green-500' : (num > 90 ? 'text-orange-500' : 'text-yellow-500');
        if(key === 'casa') return num > 60 ? 'text-green-500' : 'text-red-500';
        if(key === 'roa')  return num > 1.25 ? 'text-green-500' : 'text-red-500';
        if(key === 'cash') return num > 4.05 ? 'text-green-500' : 'text-red-500';
        if(key === 'cov')  return num >= 100 ? 'text-green-500' : 'text-red-500';
        return 'text-gray-100';
    }

    function tvChartTheme() {
        const dark = document.body.classList.contains('dark-mode');
        return {
            text: dark ? '#e5e7eb' : '#374151',
            muted: dark ? '#9ca3af' : '#6b7280',
            grid: dark ? '#374151' : '#e5e7eb'
        };
    }

    let chartTrenInstance = null;
    let chartRunoffInstance = null; 
    let chartCoaMtMInstance = null;
    let chartCoaYtYInstance = null;

    // ==========================================
    // 3. GLOBAL CONFIG & DATA FETCHING (FIXED LOGIC)
    // ==========================================
    const TV_CONFIG = {
        closing_date: '',
        harian_date: '',
        filter_mode: 'konsolidasi',
        kantor: 'konsolidasi',
        screen_profile: 'tv_sd'
    };
    const TV_KORWIL = ['SEMARANG','SOLO','BANYUMAS','PEKALONGAN'];
    const TV_SCREEN_PROFILES = {
        auto: { layout: 'auto' },
        tv_nhd: { layout: 'desktop', width: 640, height: 360 },
        tv_sd: { layout: 'desktop', width: 854, height: 480 },
        tv_xga: { layout: 'desktop', width: 1024, height: 576 },
        tv_hd: { layout: 'desktop', width: 1366, height: 768 },
        tv_fhd: { layout: 'desktop', width: 1920, height: 1080 },
        tv_qhd: { layout: 'desktop', width: 2560, height: 1440 },
        tv_4k: { layout: 'desktop', width: 3840, height: 2160 },
        tablet: { layout: 'mobile', width: 1024, height: 768 },
        mobile: { layout: 'mobile', width: 430, height: 932 }
    };
    const TV_SELECT_TRIGGER_MAP = {
        tv_screen_profile: 'tv_screen_profile_trigger',
        tv_filter_closing: 'tv_filter_closing_trigger',
        tv_filter_harian: 'tv_filter_harian_trigger',
        tv_filter_kantor: 'tv_filter_kantor_trigger'
    };
    const TV_SELECT_TITLE_MAP = {
        tv_screen_profile: 'Resolusi layar TV',
        tv_filter_closing: 'Pilih Closing Date',
        tv_filter_harian: 'Pilih Harian Date',
        tv_filter_kantor: 'Pilih Filter Kantor'
    };
    let tvFilterApplyTimer = null;
    let tvControlCloseTimer = null;
    let tvActiveSelectModalId = null;
    let tvInitialHarianDate = null;

    const apiCall = (url, opt={}) => fetch(url, opt);

    function openTvControls() {
        if(tvControlCloseTimer) clearTimeout(tvControlCloseTimer);
        document.getElementById('tvFloatingControls')?.classList.add('is-open');
    }

    function closeTvControlsNow() {
        if(tvControlCloseTimer) clearTimeout(tvControlCloseTimer);
        document.getElementById('tvFloatingControls')?.classList.remove('is-open');
    }

    function closeTvControlsSoon() {
        if(tvControlCloseTimer) clearTimeout(tvControlCloseTimer);
        tvControlCloseTimer = setTimeout(() => {
            closeTvControlsNow();
        }, 700);
    }

    function toggleTvControls() {
        const box = document.getElementById('tvFloatingControls');
        if(!box) return;
        box.classList.toggle('is-open');
    }

    function getTodayRealtimeTV() {
        const now = new Date();
        return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    }

    function refreshTvTriggerLabel(selectId) {
        const select = document.getElementById(selectId);
        const trigger = document.getElementById(TV_SELECT_TRIGGER_MAP[selectId] || '');
        if(!select || !trigger) return;
        trigger.textContent = select.selectedOptions?.[0]?.textContent || select.value || '-';
    }

    function refreshAllTvTriggerLabels() {
        Object.keys(TV_SELECT_TRIGGER_MAP).forEach(refreshTvTriggerLabel);
    }

    function closeTvSelectModal() {
        tvActiveSelectModalId = null;
        document.getElementById('tvSelectModal')?.classList.add('hidden');
    }

    function selectTvModalOption(value) {
        if(!tvActiveSelectModalId) return;
        const select = document.getElementById(tvActiveSelectModalId);
        if(!select) return;
        select.value = value;
        refreshTvTriggerLabel(tvActiveSelectModalId);
        select.dispatchEvent(new Event('change', { bubbles: true }));
        closeTvSelectModal();
    }

    function openTvSelectModal(selectId) {
        const select = document.getElementById(selectId);
        const modal = document.getElementById('tvSelectModal');
        const title = document.getElementById('tvSelectModalTitle');
        const optionsBox = document.getElementById('tvSelectModalOptions');
        if(!select || !modal || !title || !optionsBox) return;

        tvActiveSelectModalId = selectId;
        title.textContent = TV_SELECT_TITLE_MAP[selectId] || 'Pilih Opsi';
        optionsBox.innerHTML = [...select.options].map(option => `
            <button type="button" class="tv-select-option ${option.value === select.value ? 'is-active' : ''}" onclick="selectTvModalOption('${String(option.value).replace(/'/g, "\\'")}')">
                ${option.textContent}
            </button>
        `).join('');
        modal.classList.remove('hidden');
    }

    function getH1DateTV(dateStr) {
        let d = new Date(dateStr);
        d.setDate(d.getDate() - 1);
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function formatTvOptionDate(date) {
        const d = new Date(date);
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }

    function buildTvDateOptions(selectId, endDate, totalDays = 31, monthEndOnly = false) {
        const select = document.getElementById(selectId);
        if(!select || !endDate) return;
        const end = new Date(endDate);
        if(Number.isNaN(end.getTime())) return;
        const options = [];

        if(monthEndOnly) {
            const cursor = new Date(end.getFullYear(), end.getMonth() + 1, 0);
            for(let i = 0; i < totalDays; i++) {
                const current = new Date(cursor.getFullYear(), cursor.getMonth() - i + 1, 0);
                options.push(formatTvOptionDate(current));
            }
        } else {
            for(let i = 0; i < totalDays; i++) {
                const current = new Date(end);
                current.setDate(end.getDate() - i);
                options.push(formatTvOptionDate(current));
            }
        }

        select.innerHTML = [...new Set(options)].map(val => `<option value="${val}">${val}</option>`).join('');
    }

    function ensureTvDateOption(selectId, value) {
        const select = document.getElementById(selectId);
        if(!select || !value) return;
        if(![...select.options].some(opt => opt.value === value)) {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            select.prepend(option);
        }
        select.value = value;
        refreshTvTriggerLabel(selectId);
    }

    function isTvKonsolidasi() {
        return TV_CONFIG.kantor === 'konsolidasi';
    }

    function updateTvScopeBadge() {
        const badge = document.getElementById('tv_scope_badge');
        const select = document.getElementById('tv_filter_kantor');
        if(!badge) return;
        if(isTvKonsolidasi()) {
            badge.textContent = 'KONSOLIDASI';
            return;
        }
        const label = select?.selectedOptions?.[0]?.textContent || TV_CONFIG.kantor || 'KANWIL / CABANG';
        badge.textContent = label.replace(/^(\d{3}\s-\s)/, '').toUpperCase();
    }

    async function loadTvKantorOptions() {
        const optKantor = document.getElementById('tv_filter_kantor');
        if(!optKantor) return;
        try {
            const res = await apiCall('./api/kode/', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({type:'kode_kantor'}) });
            const j = await res.json();
            let html = `<option value="konsolidasi">Konsolidasi</option><option value="000">000 - Kantor Pusat</option><option value="SEMARANG">Korwil Semarang</option><option value="SOLO">Korwil Solo</option><option value="BANYUMAS">Korwil Banyumas</option><option value="PEKALONGAN">Korwil Pekalongan</option>`;
            if(j.data) j.data.filter(x => x.kode_kantor !== '000').forEach(k => html += `<option value="${k.kode_kantor}">${k.kode_kantor} - ${k.nama_kantor || k.nama_cabang || ''}</option>`);
            optKantor.innerHTML = html;
            optKantor.value = TV_CONFIG.kantor;
        } catch(e) {
            optKantor.innerHTML = `<option value="000">000 - Kantor Pusat</option>`;
        }
    }

    function syncTvFilterControls() {
        const closing = document.getElementById('tv_filter_closing');
        const harian = document.getElementById('tv_filter_harian');
        const kantor = document.getElementById('tv_filter_kantor');
        const screen = document.getElementById('tv_screen_profile');
        if(closing) ensureTvDateOption('tv_filter_closing', TV_CONFIG.closing_date || '');
        if(harian) ensureTvDateOption('tv_filter_harian', TV_CONFIG.harian_date || '');
        if(screen) screen.value = TV_CONFIG.screen_profile || 'auto';
        if(kantor) {
            kantor.value = TV_CONFIG.kantor;
        }
        updateTvScopeBadge();
        refreshAllTvTriggerLabels();
    }

    function getTvResolvedLayout() {
        const profile = TV_SCREEN_PROFILES[TV_CONFIG.screen_profile] || TV_SCREEN_PROFILES.auto;
        if(profile.layout === 'desktop') return 'desktop';
        if(profile.layout === 'mobile') return 'mobile';
        return window.innerWidth <= 1023 ? 'mobile' : 'desktop';
    }

    function applyTvScreenProfile() {
        const profileKey = TV_CONFIG.screen_profile || 'auto';
        const profile = TV_SCREEN_PROFILES[profileKey] || TV_SCREEN_PROFILES.auto;
        const layout = getTvResolvedLayout();
        document.body.dataset.tvScreenProfile = profileKey;
        document.body.classList.toggle('tv-desktop-layout', layout === 'desktop');
        document.body.classList.toggle('tv-mobile-layout', layout === 'mobile');
        document.documentElement.classList.toggle('tv-desktop-layout', layout === 'desktop');
        document.documentElement.classList.toggle('tv-mobile-layout', layout === 'mobile');

        const wrapper = document.getElementById('tvWrapper');
        if(wrapper) {
            wrapper.style.width = '100%';
            if(layout === 'mobile' && profile.width) wrapper.style.maxWidth = `${profile.width}px`;
            else wrapper.style.maxWidth = 'none';
        }

        if(layout === 'desktop') {
            document.documentElement.style.height = '100%';
            document.body.style.height = '100%';
            document.documentElement.style.overflow = 'hidden';
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflowX = 'hidden';
            document.documentElement.style.overflowY = 'hidden';
            document.body.style.overflowX = 'hidden';
            document.body.style.overflowY = 'hidden';
        } else {
            document.documentElement.style.height = 'auto';
            document.body.style.height = 'auto';
            document.documentElement.style.overflow = 'visible';
            document.body.style.overflow = 'visible';
            document.documentElement.style.overflowX = 'hidden';
            document.documentElement.style.overflowY = 'auto';
            document.body.style.overflowX = 'hidden';
            document.body.style.overflowY = 'auto';
        }
    }

    function handleTvScreenProfileChange() {
        TV_CONFIG.screen_profile = document.getElementById('tv_screen_profile')?.value || 'auto';
        localStorage.setItem('tv_screen_profile', TV_CONFIG.screen_profile);
        applyTvScreenProfile();
        showSlide(currentSlide);
    }

    function handleTvFilterModeChange() {
        TV_CONFIG.kantor = document.getElementById('tv_filter_kantor')?.value || 'konsolidasi';
        TV_CONFIG.filter_mode = isTvKonsolidasi() ? 'konsolidasi' : 'kantor';
        updateTvScopeBadge();
        scheduleTvFilterApply();
    }

    function applyTvFilters() {
        TV_CONFIG.closing_date = document.getElementById('tv_filter_closing')?.value || TV_CONFIG.closing_date;
        TV_CONFIG.harian_date = document.getElementById('tv_filter_harian')?.value || TV_CONFIG.harian_date;
        TV_CONFIG.kantor = document.getElementById('tv_filter_kantor')?.value || 'konsolidasi';
        TV_CONFIG.filter_mode = isTvKonsolidasi() ? 'konsolidasi' : 'kantor';
        syncTvFilterControls();
        fetchAllDataSlide();
        document.getElementById('tvFloatingControls')?.classList.remove('is-open');
    }

    function scheduleTvFilterApply() {
        if(tvFilterApplyTimer) clearTimeout(tvFilterApplyTimer);
        tvFilterApplyTimer = setTimeout(applyTvFilters, 900);
    }

    function bindTvFilterEvents() {
        ['tv_filter_closing', 'tv_filter_harian', 'tv_filter_kantor'].forEach(id => {
            const el = document.getElementById(id);
            if(!el || el.dataset.tvBound === '1') return;
            el.addEventListener('change', id === 'tv_filter_kantor' ? handleTvFilterModeChange : scheduleTvFilterApply);
            el.dataset.tvBound = '1';
        });
        const screen = document.getElementById('tv_screen_profile');
        if(screen && screen.dataset.tvBound !== '1') {
            screen.addEventListener('change', handleTvScreenProfileChange);
            screen.dataset.tvBound = '1';
        }
        Object.entries(TV_SELECT_TRIGGER_MAP).forEach(([selectId, triggerId]) => {
            const trigger = document.getElementById(triggerId);
            const select = document.getElementById(selectId);
            if(trigger && trigger.dataset.tvBound !== '1') {
                trigger.addEventListener('click', () => openTvSelectModal(selectId));
                trigger.dataset.tvBound = '1';
            }
            if(select && select.dataset.tvLabelBound !== '1') {
                select.addEventListener('change', () => refreshTvTriggerLabel(selectId));
                select.dataset.tvLabelBound = '1';
            }
        });
    }

    function attachTvOfficeFilter(payload, isLapkeu = false) {
        if(isLapkeu) {
            payload.kode_kantor = isTvKonsolidasi() ? 'konsolidasi' : (TV_CONFIG.kantor === '000' ? '000' : TV_CONFIG.kantor);
            return payload;
        }
        if(!isTvKonsolidasi()) {
            if(TV_KORWIL.includes(TV_CONFIG.kantor)) payload.korwil = TV_CONFIG.kantor;
            else payload.kode_kantor = TV_CONFIG.kantor;
        }
        return payload;
    }

    async function initTvDashboard() {
        try { 
            const r = await apiCall('./api/date/'); 
            const j = await r.json(); 
            if(j.data) {
                TV_CONFIG.closing_date = j.data.last_closing;
                TV_CONFIG.harian_date = j.data.last_created;
                tvInitialHarianDate = j.data.last_created;
            }
        } catch(e) {
            console.error("Gagal get date", e);
        }

        TV_CONFIG.screen_profile = localStorage.getItem('tv_screen_profile') || 'tv_sd';
        await loadTvKantorOptions();
        buildTvDateOptions('tv_filter_closing', TV_CONFIG.closing_date, 12, true);
        buildTvDateOptions('tv_filter_harian', TV_CONFIG.harian_date, 31, false);
        syncTvFilterControls();
        applyTvScreenProfile();
        bindTvFilterEvents();

        document.getElementById('loadingDash').classList.add('hidden');
        document.getElementById('contentDash').classList.remove('hidden');

        showSlide(0);
        resetSlideshowTimer();

        // Tarik semua data
        fetchAllDataSlide();

        setInterval(() => {
            console.log("Auto-refreshing TV Data...");
            fetchAllDataSlide();
        }, 600000); // 10 menit
    }

    // FUNGSI INI SUDAH DISAMAKAN PERSIS DENGAN KODE ASLI BAPAK
    async function fetchWidgetDataTV(type, isH1 = false) {
        let currDate = TV_CONFIG.harian_date;
        if (isH1 && currDate === tvInitialHarianDate) currDate = getH1DateTV(currDate);
        let targetRealisasiDate = (currDate === tvInitialHarianDate) ? getTodayRealtimeTV() : currDate;

        // Payload dasar
        let payload = { 
            type: type, 
            closing_date: TV_CONFIG.closing_date, 
            harian_date: currDate,
            harian_date_realisasi: targetRealisasiDate
        };

        let endpointUrl = './api/dashboard/';

        // LOGIKA KANTOR KONSOLIDASI (SAMA PERSIS DENGAN ASLINYA)
        if (type === 'summary_perbandingan' || type === 'financial_kpi') {
            endpointUrl = './api/lapkeu/';
            attachTvOfficeFilter(payload, true);
        } else {
            endpointUrl = './api/dashboard/';
            attachTvOfficeFilter(payload, false);
        }

        try {
            const res = await apiCall(endpointUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
            const json = await res.json();
            return json.data || null;
        } catch(e) { return null; }
    }

    // ==========================================
    // 4. RENDER SEMUA DATA
    // ==========================================
    function fetchAllDataSlide() {
        const pSaldoBank    = fetchWidgetDataTV('saldo_bank');
        const pRealProduk   = fetchWidgetDataTV('realisasi_by_produk');
        const pTrenNpl      = fetchWidgetDataTV('test tren npl');
        const pRrCabang     = fetchWidgetDataTV('test rr cabang');
        const pRunoffKorwil = fetchWidgetDataTV('test runoff korwil');
        const pFlowKorwil   = fetchWidgetDataTV('test flow recovery npl');
        const pTopReal      = fetchWidgetDataTV('test top realisasi');
        const pTopNpl       = fetchWidgetDataTV('test top bottom npl');
        const pDeltaNpl     = fetchWidgetDataTV('test delta npl');
        const pDeposito     = fetchWidgetDataTV('test perkembangan deposito', true);
        const pTabungan     = fetchWidgetDataTV('test perkembangan tabungan', true);
        const pSummaryMakro = fetchWidgetDataTV('summary_perbandingan', true);
        const pHealthKpi    = fetchWidgetDataTV('financial_kpi', true);
        
        fetchTrenPortoTV();
        fetchTrenRunoffTV();
        fetchTrenCoaTV();

        pSaldoBank.then(sb => {
            if(!sb) return;
            setText('kpi_saldobank', `Rp ${fmtB(sb.actual)}`);
            setHtml('kpi_saldobank_pill', `<div class="flex items-center gap-2"><div class="bg-gray-100 px-2 py-0.5 rounded text-[11px] text-gray-500">Closing: <span class="text-gray-900">Rp ${fmtB(sb.closing)}</span></div>${getDeltaHTML(sb.delta, false, false, true)}</div>`);
        });

        Promise.all([pTrenNpl, pRrCabang]).then(([tNplRaw, rrRaw]) => {
            let tNpl = Array.isArray(tNplRaw) ? tNplRaw : (tNplRaw?.tren_npl || tNplRaw?.tren_portofolio || []);
            let rrData = rrRaw?.repayment_rate || rrRaw || {};
            let osPrev = 0;
            
            if(tNpl && tNpl.length > 0) {
                const last = tNpl[tNpl.length - 1]; 
                const prev = tNpl.length > 1 ? tNpl[tNpl.length - 2] : last; 
                osPrev = prev.total_kredit || prev.osc_total || 0; 
                setText('kpi_npl', `Rp ${fmtB(last.npl_amt || last.osc_npl)}`);
                setHtml('kpi_npl_pill', `<div class="flex gap-2 mb-1.5"><div class="bg-gray-100 px-2 py-0.5 rounded text-[11px] text-gray-500">Closing: <span class="text-gray-900">${pct(prev.npl_persen)}</span></div><div class="bg-red-50 text-red-700 px-2 py-0.5 rounded text-[11px]">Act: ${pct(last.npl_persen)}</div></div>${getDeltaHTML(last.npl_persen - prev.npl_persen, true, true, true)}`);
            }

            if(rrData && rrData.grand_total) {
                const rrG = rrData.grand_total;
                let osCurr = rrG.os_total || 0;
                setText('kpi_os', `Rp ${fmtB(osCurr)}`);
                setHtml('kpi_os_pill', `<div class="flex gap-2"><div class="bg-gray-100 px-2 py-0.5 rounded text-[11px] text-gray-500">Closing: <span class="text-gray-900">Rp ${fmtB(osPrev)}</span></div>${getDeltaHTML(osCurr - osPrev, false, false, true)}</div>`);

                setText('kpi_rr', `Rp ${fmtB(rrG.os_lancar)}`);
                setHtml('kpi_rr_pill', `<div class="flex gap-2 mb-1.5"><div class="bg-gray-100 px-2 py-0.5 rounded text-[11px] text-gray-500">Closing: <span class="text-gray-900">${pct(rrG.rr_persen_prev)}</span></div><div class="bg-green-50 text-green-700 px-2 py-0.5 rounded text-[11px]">Act: ${pct(rrG.rr_persen_curr)}</div></div>${getDeltaHTML(rrG.delta_rr, true, false, true)}`);
            }
        });

        Promise.all([pDeposito, pTabungan]).then(([depRaw, tabRaw]) => {
            let dep = unwrapData(depRaw, 'perkembangan_deposito');
            let tab = unwrapData(tabRaw, 'perkembangan_tabungan');
            const dpkCurr = ((dep.grand_total?.saldo_curr||0) + (tab.grand_total?.saldo_curr||0)); 
            const dpkPrev = ((dep.grand_total?.saldo_prev||0) + (tab.grand_total?.saldo_prev||0));
            setText('kpi_dpk', `Rp ${fmtB(dpkCurr)}`);
            setHtml('kpi_dpk_pill', `<div class="flex gap-2"><div class="bg-gray-100 px-2 py-0.5 rounded text-[11px] text-gray-500">Closing: <span class="text-gray-900">Rp ${fmtB(dpkPrev)}</span></div>${getDeltaHTML(dpkCurr - dpkPrev, false, false, true)}</div>`);

            if(Object.keys(dep).length > 0) {
                renderUniversalList('list_dep_saldo_top', dep.top_saldo, 'nama_cabang', 'saldo_curr', 'noa_curr', 'bg-yellow-500', false, 'Rek');
                renderUniversalList('list_dep_baru', dep.top_baru, 'nama_cabang', 'saldo_baru', 'noa_tambah', 'bg-green-500', false, 'Rek Baru');
            }
            if(Object.keys(tab).length > 0) {
                renderUniversalList('list_tab_saldo_top', tab.top_saldo, 'nama_cabang', 'saldo_curr', 'noa_curr', 'bg-blue-500', false, 'Rek');
                renderUniversalList('list_tab_baru', tab.top_baru, 'nama_cabang', 'saldo_baru', 'noa_tambah', 'bg-teal-500', false, 'Rek Baru');
            }
        });

        Promise.all([pSummaryMakro, pHealthKpi]).then(([mRaw, kRaw]) => {
            if(mRaw && mRaw.makro) {
                let m = mRaw.makro;
                document.getElementById('txt_makro_aset').textContent = `Rp ${fmtB(m.aset?.nominal_aktual)}`;
                document.getElementById('delta_makro_aset').innerHTML = getDeltaHTML(m.aset?.growth_mom, true, false, true);
                document.getElementById('txt_makro_laba').textContent = `Rp ${fmtB(m.laba_rugi?.nominal_aktual)}`;
                document.getElementById('delta_makro_laba').innerHTML = getDeltaHTML(m.laba_rugi?.growth_mom, true, false, true);
                document.getElementById('txt_makro_pendapatan').textContent = `Rp ${fmtB(m.pendapatan?.nominal_aktual)}`;
                document.getElementById('delta_makro_pendapatan').innerHTML = getDeltaHTML(m.pendapatan?.growth_mom, true, false, true);
                document.getElementById('txt_makro_biaya').textContent = `Rp ${fmtB(m.biaya?.nominal_aktual)}`;
                document.getElementById('delta_makro_biaya').innerHTML = getDeltaHTML(m.biaya?.growth_mom, true, true, true);
            }
            if(kRaw && kRaw.rasio) {
                let r = kRaw.rasio;
                document.getElementById('txt_rasio_bopo').textContent = `${r.bopo_persen}%`; document.getElementById('txt_rasio_bopo').className = `text-lg font-black ${getRasioColor('bopo', r.bopo_persen)}`;
                document.getElementById('txt_rasio_ldr').textContent = `${r.ldr_persen}%`; document.getElementById('txt_rasio_ldr').className = `text-lg font-black ${getRasioColor('ldr', r.ldr_persen)}`;
                document.getElementById('txt_rasio_casa').textContent = `${r.casa_persen}%`; document.getElementById('txt_rasio_casa').className = `text-lg font-black ${getRasioColor('casa', r.casa_persen)}`;
                document.getElementById('txt_rasio_coverage').textContent = `${r.coverage_ratio_persen}%`; document.getElementById('txt_rasio_coverage').className = `text-lg font-black ${getRasioColor('cov', r.coverage_ratio_persen)}`;
                document.getElementById('txt_rasio_roa').textContent = `${r.roa_persen}%`; document.getElementById('txt_rasio_roa').className = `text-lg font-black ${getRasioColor('roa', r.roa_persen)}`;
                document.getElementById('txt_rasio_cash').textContent = `${r.cash_ratio_persen}%`; document.getElementById('txt_rasio_cash').className = `text-lg font-black ${getRasioColor('cash', r.cash_ratio_persen)}`;
                
                if(kRaw.top_5_biaya) renderUniversalList('box_top_biaya', kRaw.top_5_biaya, 'nama', 'nominal', 'kode', 'bg-red-500', false, '');
            }
        });

        pRealProduk.then(rpRaw => {
            if(!rpRaw) return;
            const rp = unwrapData(rpRaw, 'realisasi_by_produk');
            setText('label_total_realisasi_produk', `Rp ${fmtB(rp.grand_total?.total_realisasi)}`);
            renderUniversalList('box_realisasi_produk', rp.detail_produk || [], 'nama_produk', 'total_realisasi', 'noa_realisasi', 'bg-indigo-500', false, 'NOA');
        });

        Promise.all([pRunoffKorwil, pFlowKorwil]).then(([roRaw, flowRaw]) => {
            const ro = unwrapData(roRaw, 'runoff_vs_realisasi');
            const flow = unwrapData(flowRaw, 'flow_vs_recovery_npl');

            if(ro?.grand_total) {
                setText('summary_runoff_realisasi', `Rp ${fmtB(ro.grand_total.realisasi)}`);
                setText('summary_runoff_total', `Rp ${fmtB(ro.grand_total.total_runoff)}`);
                setDeltaSummary('summary_runoff_growth', 'Growth', ro.grand_total.growth, false);
            }
            if(flow?.grand_total) {
                setText('summary_flow_npl', `Rp ${fmtB(flow.grand_total.flow_npl)}`);
                setText('summary_recovery_npl', `Rp ${fmtB(flow.grand_total.total_recovery)}`);
                setDeltaSummary('summary_flow_os_npl', 'OS NPL', Number(flow.grand_total.flow_npl || 0) - Number(flow.grand_total.total_recovery || 0), true);
            }

            if(ro?.detail_korwil) renderKorwilCompare('box_runoff_realisasi', ro.detail_korwil, 'realisasi', 'total_runoff', 'bg-green-500', 'bg-red-500', 4, 'Realisasi', 'Run Off', true);
            if(flow?.detail_korwil) renderKorwilCompare('box_flow_recovery', flow.detail_korwil, 'flow_npl', 'total_recovery', 'bg-red-500', 'bg-green-500', 4, 'Flow NPL', 'Recovery', true);
        });

        Promise.all([pTopReal, pTopNpl, pDeltaNpl, pRrCabang]).then(([realRaw, nplRaw, deltaRaw, rrRaw]) => {
            const real = unwrapData(realRaw, 'top_bottom_realisasi');
            const npl = unwrapData(nplRaw, 'top_bottom_npl');
            const delta = unwrapData(deltaRaw, 'kenaikan_penurunan_npl');
            const rr = unwrapData(rrRaw, 'repayment_rate');

            if(real?.top_cabang) {
                renderUniversalList('best_realisasi', real.top_cabang, 'nama_cabang', 'total_realisasi', 'noa_realisasi', 'bg-blue-500', false, 'NOA');
                renderUniversalList('list_realisasi_bottom', [...(real.bottom_cabang || [])].reverse(), 'nama_cabang', 'total_realisasi', 'noa_realisasi', 'bg-orange-500', false, 'NOA');
                let topAOCustom = (real.top_ao || []).map(ao => ({ ...ao, nama_custom: (ao.nama_cabang && ao.nama_cabang !== 'Unknown' ? `[${ao.nama_cabang.replace(/Kc\. /gi, '')}] ` : '') + ao.nama_ao }));
                renderUniversalList('best_realisasi_ao', topAOCustom, 'nama_custom', 'total_realisasi', 'noa_realisasi', 'bg-indigo-500', false, 'NOA');
            }
            if(npl?.bottom) {
                renderUniversalList('best_npl', npl.bottom, 'nama_cabang', 'npl_persen', 'npl_amt', 'bg-green-500', true, 'Rp');
                renderUniversalList('list_npl_top', npl.top, 'nama_cabang', 'npl_persen', 'npl_amt', 'bg-red-500', true, 'Rp');
            }
            if(rr?.top_rr) renderUniversalList('best_rr', rr.top_rr, 'nama_cabang', 'rr_persen_curr', 'os_total', 'bg-teal-500', true, 'Rp');
            if(delta?.top_penurunan) {
                renderUniversalList('best_npl_turun', delta.top_penurunan, 'nama_cabang', 'delta_npl', 'npl_persen_curr', 'bg-emerald-500', true, 'NPL Now');
                renderUniversalList('list_npl_naik', delta.top_kenaikan, 'nama_cabang', 'delta_npl', 'npl_persen_curr', 'bg-orange-500', true, 'NPL Now');
            }

            const tReal = real?.top_cabang?.[0];
            const tAo = real?.top_ao?.[0];
            const tRR = rr?.top_rr?.[0];
            const tNplBest = npl?.bottom?.[0];
            const tTurun = delta?.top_penurunan?.[0];
            let html = '';
            if(tReal) html += `<div><span class="text-blue-300 font-bold">Realisasi Tertinggi</span><span class="block text-white mt-0.5">${(tReal.nama_cabang || '-').replace('Kc. ','')} (${fmtB(tReal.total_realisasi)})</span></div>`;
            if(tAo) html += `<div><span class="text-indigo-300 font-bold">AO Terbaik</span><span class="block text-white mt-0.5">${tAo.nama_ao || '-'} (${fmtB(tAo.total_realisasi)})</span></div>`;
            if(tRR) html += `<div><span class="text-green-300 font-bold">RR Terbaik</span><span class="block text-white mt-0.5">${(tRR.nama_cabang || '-').replace('Kc. ','')} (${pct(tRR.rr_persen_curr)})</span></div>`;
            if(tNplBest) html += `<div><span class="text-emerald-300 font-bold">NPL Terbaik</span><span class="block text-white mt-0.5">${(tNplBest.nama_cabang || '-').replace('Kc. ','')} (${pct(tNplBest.npl_persen)})</span></div>`;
            if(tTurun) html += `<div><span class="text-teal-300 font-bold">Penurunan NPL Terbesar</span><span class="block text-white mt-0.5">${(tTurun.nama_cabang || '-').replace('Kc. ','')} (${pct(Math.abs(tTurun.delta_npl))})</span></div>`;
            setHtml('dynamic_insights', html || '<span class="text-gray-400">Menunggu data insight.</span>');
        });
    }

    // ==========================================
    // 5. CHART RENDERERS (SAMA PERSIS DENGAN KODE ASLI)
    // ==========================================
    async function fetchTrenPortoTV() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js belum termuat untuk canvasTrenPortofolio');
            return;
        }
        // Dashboard API tidak pakai kode_kantor untuk konsolidasi
        const payload = {
            type: 'tren_portofolio_kredit',
            harian_date: TV_CONFIG.harian_date,
            periode: 'bulanan'
        };
        attachTvOfficeFilter(payload, false);
        const res = await apiCall('./api/dashboard/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const json = await res.json();
        
        let arr = Array.isArray(json.data) ? json.data : (json.data?.tren_portofolio || []);
        if(!arr.length) return;

        const canvas = document.getElementById('canvasTrenPortofolio'); 
        if(!canvas) return; const ctx = canvas.getContext('2d');
        if(chartTrenInstance) chartTrenInstance.destroy();
        const theme = tvChartTheme();

        const labels = arr.map(d => d.label || d.tanggal);
        const dataNPL = arr.map(d => parseFloat(Number(d.npl_persen || 0).toFixed(2)));
        const dataNplAmt = arr.map(d => Number(d.npl_amt || d.osc_npl || 0));
        const lastNpl = arr[arr.length - 1] || {};
        const prevNpl = arr.length > 1 ? arr[arr.length - 2] : lastNpl;
        const deltaNpl = Number(lastNpl.npl_persen || 0) - Number(prevNpl.npl_persen || 0);

        setText('summary_npl_pct', pct(lastNpl.npl_persen));
        setText('summary_npl_amt', `Rp ${fmtB(lastNpl.npl_amt || lastNpl.osc_npl)}`);
        const deltaEl = document.getElementById('summary_npl_delta');
        if(deltaEl) {
            deltaEl.textContent = `${deltaNpl >= 0 ? '+' : '-'}${Math.abs(deltaNpl).toFixed(2)} Poin`;
            deltaEl.className = `text-base md:text-xl font-black ${deltaNpl <= 0 ? 'text-green-600' : 'text-red-600'}`;
        }

        let gradNPL = ctx.createLinearGradient(0, 0, 0, 300); 
        gradNPL.addColorStop(0, 'rgba(239, 68, 68, 0.3)'); gradNPL.addColorStop(1, 'rgba(239, 68, 68, 0.0)');
        const minNpl = Math.min(...dataNPL);
        const maxNpl = Math.max(...dataNPL);

        const nplLabelPlugin = {
            id: 'tvNplValueLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                const meta = chart.getDatasetMeta(0);
                ctx.save();
                ctx.font = 'bold 11px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#ef4444';
                meta.data.forEach((point, index) => {
                    if(!point) return;
                    const value = dataNPL[index];
                    ctx.fillText(`${value.toFixed(2)}%`, point.x, point.y - 14);
                });
                ctx.restore();
            }
        };

        chartTrenInstance = new Chart(ctx, {
            type: 'line',
            data: { labels: labels, datasets: [{ label: 'NPL (%)', data: dataNPL, borderColor: '#ef4444', backgroundColor: gradNPL, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#ef4444', pointBorderWidth: 3, pointRadius: 5, pointHoverRadius: 7, fill: true, tension: 0.35 }] },
            options: { 
                layout: { padding: { top: 34, right: 18, bottom: 12, left: 10 } },
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { color: theme.text, usePointStyle: true, boxWidth: 10, font: { family: 'sans-serif', size: 12, weight: 'bold' } } },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        padding: 12,
                        titleFont: { size: 13, family: 'sans-serif' },
                        bodyFont: { size: 12, family: 'sans-serif' },
                        usePointStyle: true,
                        callbacks: {
                            labelColor: context => ({ borderColor: context.dataset.borderColor, backgroundColor: context.dataset.borderColor }),
                            label: c => `NPL: ${Number(c.raw || 0).toFixed(2)}% (Rp ${fmtB(dataNplAmt[c.dataIndex])})`,
                            afterBody: c => {
                                if(!c.length || c[0].dataIndex === 0) return [];
                                const idx = c[0].dataIndex;
                                const delta = dataNPL[idx] - dataNPL[idx - 1];
                                return [`Perubahan: ${delta >= 0 ? '+' : '-'}${Math.abs(delta).toFixed(2)} Poin`];
                            }
                        }
                    }
                },
                scales: { 
                    x: { ticks: { color: theme.muted }, grid: { display: false } }, 
                    y: {
                        suggestedMin: Math.max(0, minNpl - 0.4),
                        suggestedMax: maxNpl + 0.4,
                        ticks: { color: theme.muted, callback: val => `${Number(val).toFixed(2)}%` },
                        grid: { color: theme.grid, borderDash: [4,4] }
                    } 
                }
            },
            plugins: [nplLabelPlugin]
        });
    }

    async function fetchTrenRunoffTV() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js belum termuat untuk canvasTrenRunoff');
            return;
        }
        const payload = {
            type: 'tren_runoff_realisasi',
            harian_date: TV_CONFIG.harian_date,
            periode: 'bulanan'
        };
        attachTvOfficeFilter(payload, false);
        const res = await apiCall('./api/dashboard/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const json = await res.json();
        
        let arr = Array.isArray(json.data) ? json.data : (json.data?.tren_runoff_realisasi || []);
        if(!arr.length) return;

        const canvas = document.getElementById('canvasTrenRunoff'); 
        if(!canvas) return; const ctx = canvas.getContext('2d');
        if(chartRunoffInstance) chartRunoffInstance.destroy();
        const theme = tvChartTheme();

        const labels = arr.map(d => d.label); 
        const dataReal = arr.map(d => Number(d.total_realisasi) || 0); 
        const dataRunoff = arr.map(d => Number(d.total_runoff) || 0);
        const dataLunas = arr.map(d => Number(d.total_lunas) || 0);
        const dataNoaLunas = arr.map(d => Number(d.noa_lunas) || 0);
        const dataAngsuran = arr.map(d => Number(d.total_angsuran) || 0);
        const dataNoaAngsuran = arr.map(d => Number(d.noa_angsuran) || 0);
        const dataGrowth = arr.map(d => Number(d.growth) || 0);
        const lastRunoff = arr[arr.length - 1] || {};
        setText('label_runoff_date', `Berdasarkan Tanggal: ${TV_CONFIG.harian_date || '-'}`);
        setText('summary_tren_runoff', `Rp ${fmtB(lastRunoff.total_runoff)}`);
        setText('summary_tren_lunas', `Rp ${fmtB(lastRunoff.total_lunas)}`);
        const growthSummary = document.getElementById('summary_tren_growth');
        if(growthSummary) {
            const growthVal = Number(lastRunoff.growth || 0);
            const arrow = growthVal >= 0 ? '▲' : '▼';
            growthSummary.innerHTML = `<span class="${growthVal >= 0 ? 'text-green-600' : 'text-red-600'}">${arrow}</span> Rp ${fmtB(Math.abs(growthVal))}`;
            growthSummary.className = `text-sm md:text-lg font-black ${growthVal >= 0 ? 'text-green-600' : 'text-red-600'}`;
        }

        let gradReal = ctx.createLinearGradient(0, 0, 0, 300);
        gradReal.addColorStop(0, 'rgba(16, 185, 129, 0.20)');
        gradReal.addColorStop(1, 'rgba(16, 185, 129, 0.00)');
        let gradRunoff = ctx.createLinearGradient(0, 0, 0, 300);
        gradRunoff.addColorStop(0, 'rgba(239, 68, 68, 0.20)');
        gradRunoff.addColorStop(1, 'rgba(239, 68, 68, 0.00)');

        const valueLabelPlugin = {
            id: 'tvRunoffValueLabels',
            afterDatasetsDraw(chart) {
                const { ctx, data } = chart;
                ctx.save();
                ctx.font = 'bold 11px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                for (let i = 0; i < data.labels.length; i++) {
                    const points = [];
                    data.datasets.forEach((dataset, datasetIndex) => {
                        const meta = chart.getDatasetMeta(datasetIndex);
                        if(meta.hidden || !meta.data[i]) return;
                        const pos = meta.data[i].tooltipPosition();
                        points.push({ datasetIndex, x: pos.x, y: pos.y, text: fmtB(dataset.data[i]), color: dataset.borderColor });
                    });
                    points.sort((a, b) => a.y !== b.y ? a.y - b.y : a.datasetIndex - b.datasetIndex);
                    points.forEach((point, idx) => {
                        let drawY = point.y - 12;
                        if(idx > 0 && Math.abs(drawY - points[idx - 1].drawY) < 16) drawY = point.y + 14;
                        point.drawY = drawY;
                        ctx.fillStyle = point.color;
                        ctx.fillText(point.text, point.x, drawY);
                    });
                }
                ctx.restore();
            }
        };

        chartRunoffInstance = new Chart(ctx, {
            type: 'line',
            data: { labels: labels, datasets: [
                { label: 'Realisasi', data: dataReal, borderColor: '#10b981', backgroundColor: gradReal, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#10b981', pointBorderWidth: 2, pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 },
                { label: 'Run Off', data: dataRunoff, borderColor: '#ef4444', backgroundColor: gradRunoff, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#ef4444', pointBorderWidth: 2, pointRadius: 3, pointHoverRadius: 5, fill: true, tension: 0.4 }
            ]},
            options: { 
                layout: { padding: { top: 30, bottom: 15, left: 10, right: 10 } },
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 10, color: theme.text, font: { family: 'sans-serif', size: 12, weight: 'bold' } } },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        padding: 12,
                        titleFont: { size: 13, family: 'sans-serif' },
                        bodyFont: { size: 12, family: 'sans-serif' },
                        usePointStyle: true,
                        callbacks: {
                            labelColor: context => ({ borderColor: context.dataset.borderColor, backgroundColor: context.dataset.borderColor }),
                            label: c => `${c.dataset.label}: Rp ${fmtB(c.raw)}`,
                            afterBody: c => {
                                if(!c.length) return [];
                                const idx = c[0].dataIndex;
                                const growth = dataGrowth[idx];
                                return [
                                    '------------------------',
                                    'Detail Run Off:',
                                    `  - Lunas: Rp ${fmtB(dataLunas[idx])} (${fmt(dataNoaLunas[idx])} NOA)`,
                                    `  - Angsuran: Rp ${fmtB(dataAngsuran[idx])} (${fmt(dataNoaAngsuran[idx])} NOA)`,
                                    '',
                                    `Growth: ${growth >= 0 ? '▲' : '▼'} Rp ${fmtB(Math.abs(growth))}`
                                ];
                            }
                        }
                    }
                },
                scales: { 
                    x: { ticks: { color: theme.muted }, grid: { display: false } }, 
                    y: { ticks: { color: theme.muted, callback: val => fmtB(val) }, grid: { color: theme.grid, borderDash: [4,4] } } 
                }
            },
            plugins: [valueLabelPlugin]
        });
    }

    async function fetchTrenCoaTV() {
        if (typeof Chart === 'undefined') {
            console.error('Chart.js belum termuat untuk canvas COA');
            return;
        }
        let currDateH1 = getH1DateTV(TV_CONFIG.harian_date);

        const payload = {
            type: 'test tren perkiraan',
            kode_perk: 'LABA_RUGI',
            harian_date: currDateH1
        };
        attachTvOfficeFilter(payload, true);

        const res = await apiCall('./api/lapkeu/', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        const json = await res.json();
        const data = json.data;

        if(!data || !data.mtm) return;
        
        document.getElementById('txt_coa_saldo').innerText = `Rp ${fmtB(data.summary?.saldo_sekarang)}`;
        document.getElementById('txt_coa_growth').innerHTML = getDeltaHTML(data.summary?.pertumbuhan_persen, true, false, false);

        const ctxMtM = document.getElementById('canvasCoaMtM').getContext('2d');
        const ctxYtY = document.getElementById('canvasCoaYtY').getContext('2d');
        const theme = tvChartTheme();
        if(chartCoaMtMInstance) chartCoaMtMInstance.destroy();
        if(chartCoaYtYInstance) chartCoaYtYInstance.destroy();

        const getConf = (lbls, vals) => ({
            type: 'bar',
            data: { labels: lbls, datasets: [{ data: vals, backgroundColor: vals.map(v => v < 0 ? '#ef4444' : '#22c55e'), borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: theme.muted }, grid: { display: false } }, y: { ticks: { color: theme.muted, callback: v => fmtB(v) }, grid: { color: theme.grid, borderDash: [4,4] } } } }
        });

        chartCoaMtMInstance = new Chart(ctxMtM, getConf(data.mtm.map(d => d.label), data.mtm.map(d => d.saldo)));
        chartCoaYtYInstance = new Chart(ctxYtY, getConf(data.yty.map(d => d.label), data.yty.map(d => d.saldo)));
    }

    // ==========================================
    // 6. DOM RENDER HELPERS
    // ==========================================
    function renderUniversalList(elId, dataArray, nameKey, valKey, subKey, colorClass, isPercent, subLabel = 'Rp') {
        const box = document.getElementById(elId); if(!box) return; box.innerHTML = '';
        if(!dataArray || dataArray.length === 0) return box.innerHTML = `<p class="text-[11px] text-gray-500 italic py-2 text-center">Tidak ada data.</p>`;
        dataArray = tvLimit(dataArray, 5);
        let maxVal = Math.max(...dataArray.map(o => Math.abs(Number(o[valKey]) || 0))); if(maxVal === 0) maxVal = 1;
        dataArray.forEach(item => {
            let val = Number(item[valKey] || 0); let sub = Number(item[subKey] || 0); let wPct = Math.abs((val / maxVal) * 100);
            let displayVal = isPercent ? pct(Math.abs(val)) : fmtB(Math.abs(val));
            let displaySub = subLabel === 'Rp' ? `Rp ${fmtB(sub)}` : (subLabel === 'NPL Now' ? `NPL saat ini: ${pct(sub)}` : `${fmt(sub)} ${subLabel}`);
            if(elId === 'box_top_biaya') displaySub = `<span class="text-gray-500 font-mono">${item[subKey]}</span>`;
            
            box.innerHTML += `<div class="mb-3 group relative"><div class="flex justify-between items-end mb-1 gap-2"><div class="flex flex-col min-w-0 w-2/3"><span class="text-xs font-bold text-gray-800 truncate" title="${(item[nameKey]||'-').replace(/Kc\. /gi, '')}">${(item[nameKey]||'-').replace(/Kc\. /gi, '')}</span><span class="text-[10px] text-gray-500 truncate">${displaySub}</span></div><span class="text-xs font-black text-gray-900 whitespace-nowrap">${val < 0 ? '-' : ''}${displayVal}</span></div><div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden"><div class="${colorClass} h-2 rounded-full bar-fill" style="width: ${Math.max(2, wPct)}%"></div></div></div>`;
        });
    }

    function renderKorwilCompare(elId, dataArray, keyA, keyB, colorA, colorB, maxRows = 6, labelA = 'A', labelB = 'B', compactGrid = false) {
        const box = document.getElementById(elId); if(!box) return; box.innerHTML = '';
        if(!dataArray || !dataArray.length) return;
        if(compactGrid) box.className = 'tv-list grid grid-cols-2 gap-x-3 gap-y-2 flex-grow mb-2';
        dataArray = tvLimit(dataArray, maxRows);
        let maxVal = Math.max(...dataArray.flatMap(o => [Number(o[keyA]), Number(o[keyB])])); if(maxVal === 0) maxVal = 1;
        const labelColorA = colorA.includes('red') ? 'text-red-500' : colorA.includes('green') ? 'text-green-500' : 'text-gray-500';
        const labelColorB = colorB.includes('red') ? 'text-red-500' : colorB.includes('green') ? 'text-green-500' : 'text-gray-500';
        dataArray.forEach(k => {
            let vA = Number(k[keyA]); let vB = Number(k[keyB]); let pctA = (vA / maxVal) * 100; let pctB = (vB / maxVal) * 100;
            box.innerHTML += `
                <div class="min-w-0 rounded-lg bg-gray-50 border border-gray-100 p-2">
                    <div class="flex items-start justify-between gap-2 mb-1.5">
                        <span class="text-[10px] md:text-[11px] text-gray-800 font-black truncate">${k.nama_korwil || '-'}</span>
                        <div class="text-right shrink-0 leading-tight">
                            <div class="text-[8px] md:text-[9px] text-gray-500 font-bold"><span class="${labelColorA}">${labelA}:</span> ${fmtB(vA)}</div>
                            <div class="text-[8px] md:text-[9px] text-gray-500 font-bold"><span class="${labelColorB}">${labelB}:</span> ${fmtB(vB)}</div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden"><div class="${colorA} h-full rounded-full bar-fill" style="width: ${Math.max(2, pctA)}%"></div></div>
                        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden"><div class="${colorB} h-full rounded-full bar-fill" style="width: ${Math.max(2, pctB)}%"></div></div>
                    </div>
                </div>`;
        });
    }

    window.addEventListener('DOMContentLoaded', checkTvLogin);
    window.addEventListener('resize', () => {
        if((TV_CONFIG.screen_profile || 'auto') === 'auto') {
            applyTvScreenProfile();
        }
    });

    // ==========================================
    // FITUR TEMA LIGHT / DARK
    // ==========================================
    function toggleTvTheme() {
        // Toggle class dark-mode di tag body
        document.body.classList.toggle('dark-mode');
        
        // Cek apakah sekarang dark mode
        const isDark = document.body.classList.contains('dark-mode');
        
        // Ganti Icon
        document.getElementById('theme_icon').innerText = isDark ? '☀️' : '🌙';
        
        // Simpan pilihan ke local storage agar tidak hilang saat refresh
        localStorage.setItem('tv_theme_preference', isDark ? 'dark' : 'light');
    }

    // Cek memori tema saat halaman pertama kali diload
    // Defaultnya sudah Light, jadi kita hanya jalankan jika user pernah pilih Dark
    window.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('tv_theme_preference') === 'dark') {
            document.body.classList.add('dark-mode');
            const icon = document.getElementById('theme_icon');
            if(icon) icon.innerText = '☀️';
        }
    });
    function toggleTvTheme() {
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        ['theme_icon', 'theme_icon_panel'].forEach(id => {
            const icon = document.getElementById(id);
            if(icon) icon.innerText = isDark ? '☀' : '☾';
        });
        localStorage.setItem('tv_theme_preference', isDark ? 'dark' : 'light');
        fetchTrenPortoTV();
        fetchTrenRunoffTV();
        fetchTrenCoaTV();
    }

    window.addEventListener('DOMContentLoaded', () => {
        const isDark = localStorage.getItem('tv_theme_preference') === 'dark';
        document.body.classList.toggle('dark-mode', isDark);
        ['theme_icon', 'theme_icon_panel'].forEach(id => {
            const icon = document.getElementById(id);
            if(icon) icon.innerText = isDark ? '☀' : '☾';
        });
    });
</script>

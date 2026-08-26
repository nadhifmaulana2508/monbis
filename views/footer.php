</main>
    <!-- TUTUP AREA KONTEN -->
  </div>
  <!-- TUTUP BUNGKUS KANAN -->
</div>
<!-- TUTUP WRAPPER UTAMA -->

<!-- FLOATING HELPDESK BUTTON -->
<!-- <div class="fixed bottom-4 right-4 z-[60] flex flex-col items-end gap-1">
    <div id="helpdeskContainer" 
         class="flex items-center bg-[#0056b3] text-white shadow-lg rounded-full overflow-hidden transition-all duration-300 ease-in-out cursor-pointer"
         style="max-width: 48px; padding: 12px;"
         onclick="handleHelpdeskClick()">
        <div class="flex items-center gap-3 whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 1 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z"/><path d="M21 16v2a4 4 0 0 1-4 4h-5"/>
            </svg>
            <div id="helpdeskText" class="hidden flex flex-col leading-none pr-4">
                <span class="text-[11px] font-bold uppercase tracking-tight">Helpdesk</span>
                <span class="text-[9px] opacity-90">Sambatan</span>
            </div>
        </div>
    </div>
</div> -->

<script>
    // --- GLOBAL USER EXPORT (PERBAIKAN BUG KODE) ---
    // Dipindah ke paling atas agar script di halaman menu bisa langsung membaca
    window.getUser = function() {
        if (window.__USER) return window.__USER;
        try {
            const rawUser = localStorage.getItem('dpk_user');
            if (rawUser) return JSON.parse(rawUser);
        } catch(e) {}
        return null;
    };

    function monbisResolvePegId(user) {
        const keys = ['id_peg', 'idPeg', 'id_pegawai', 'idPegawai', 'employee_id'];
        for (const key of keys) {
            const value = String(user?.[key] || '').trim();
            if (value === '102-119') return value;
        }
        return '';
    }

    // --- 1. SCRIPT HELPDESK ---
    let isExpanded = false;
    function handleHelpdeskClick() {
        const container = document.getElementById('helpdeskContainer');
        const text = document.getElementById('helpdeskText');
        if (!isExpanded) {
            container.style.maxWidth = '200px';
            text.classList.remove('hidden');
            isExpanded = true;
            setTimeout(() => { if(isExpanded) closeHelpdesk(); }, 5000);
        } else {
            window.open('https://helpdesk.bkkjateng.co.id/', '_blank');
            closeHelpdesk();
        }
    }
    function closeHelpdesk() {
        const container = document.getElementById('helpdeskContainer');
        const text = document.getElementById('helpdeskText');
        container.style.maxWidth = '48px';
        text.classList.add('hidden');
        isExpanded = false;
    }

    // --- 2. SCRIPT DROPDOWN PROFILE ---
    const btnProfile = document.getElementById('btnProfileMenu');
    const menuProfile = document.getElementById('dropdownProfileMenu');
    if(btnProfile && menuProfile) {
        btnProfile.addEventListener('click', (e) => {
            e.stopPropagation();
            menuProfile.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!menuProfile.contains(e.target) && !btnProfile.contains(e.target)) {
                menuProfile.classList.add('hidden');
            }
        });
    }

    // --- 3. SCRIPT MENU SIDEBAR DESKTOP & MOBILE ---
    document.addEventListener('DOMContentLoaded', () => {
      const accordions = document.querySelectorAll('.accordion-btn');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      const btnToggle = document.getElementById('btnToggleSidebar');

      function markActiveSidebarMenu() {
        const current = (window.location.pathname.split('/').pop() || 'dashboard').replace(/\/+$/, '');
        const links = document.querySelectorAll('#sidebar nav a[href]');
        links.forEach(link => {
          const href = (link.getAttribute('href') || '').replace(/^\.?\//, '').replace(/\/+$/, '');
          const isActive = href === current || (!current && href === 'dashboard');
          link.classList.toggle('is-active', isActive);
          if (!isActive) {
            link.classList.remove('bg-blue-50', 'text-blue-600');
          }
          if (isActive) {
            const group = link.closest('.accordion-group');
            const button = group?.querySelector('.accordion-btn');
            button?.classList.add('is-active');
          }
        });
      }

      markActiveSidebarMenu();

      accordions.forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          const content = btn.nextElementSibling;
          const caret = btn.querySelector('.caret');
          
          document.querySelectorAll('.accordion-content').forEach(otherContent => {
            if (otherContent !== content && !otherContent.classList.contains('hidden')) {
              otherContent.classList.add('hidden');
              otherContent.previousElementSibling.querySelector('.caret').classList.remove('rotate-180');
            }
          });
          
          content.classList.toggle('hidden');
          caret.classList.toggle('rotate-180');
        });
      });

      if(sidebar) {
          sidebar.addEventListener('mouseleave', () => {
              if (window.innerWidth >= 768) { 
                  document.querySelectorAll('.accordion-content').forEach(content => { content.classList.add('hidden'); });
                  document.querySelectorAll('.caret').forEach(caret => { caret.classList.remove('rotate-180'); });
              }
          });
      }

      function toggleSidebar() {
        if(sidebar) sidebar.classList.toggle('-translate-x-full');
        if(overlay) overlay.classList.toggle('hidden');
      }

      if(btnToggle) btnToggle.addEventListener('click', toggleSidebar);
      if(overlay) overlay.addEventListener('click', toggleSidebar);
    });

    // --- 4. SCRIPT RENDER USER & ROLE (SSO SYNC AUTO-FETCH) ---
    (async () => {
        const TOKEN_KEY = 'dpk_token', USER_KEY = 'dpk_user', USER_VERIFIED_KEY = 'dpk_user_verified_at';
        const WHOAMI_REFRESH_MS = 5 * 60 * 1000;
        const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        const API_SSO_BASE = isLocal ? 'http://localhost/rest_api_sso' : 'https://apisso.bkkjateng.co.id';
        const API_WHOAMI = `${API_SSO_BASE}/api/auth/whoami`;

        let token = null;
        const match = document.cookie.match(new RegExp('(^| )sso_token=([^;]+)'));
        if (match) token = match[2];
        if (!token) token = (localStorage.getItem(TOKEN_KEY) || '').trim();

        if (!token) return;

        // Deteksi apakah user baru menyebrang dari IMS (Ada cookie tapi LocalStorage kosong)
        const rawUser = localStorage.getItem(USER_KEY);
        const isCrossingFromSSO = !rawUser && match; 

        function paint(u) {
            if (!u) return false;
            const name = document.getElementById('navUserName'),
                  br = document.getElementById('navBranch'),
                  acc = document.getElementById('accHandle'),
                  menuMonevDev = document.getElementById('menuMonevDev'),
                  menuLayananDigital = document.getElementById('menuLayananDigital'),
                  menuEventAdmin = document.getElementById('menuEventAdmin');

            if (name) name.textContent = u.full_name || u.nama || '-';
            if (br) br.textContent = u.branch_name || u.unit_kerja || '-';
            if (acc) acc.textContent = (u.account_handle || u.username || u.email || u.employee_id || u.kode || '-');

            const isDev = (u.role === 'dev' || u.unit_kerja === 'Divisi Operasional' || u.unit_kerja === 'Dewan Komisaris dan Direksi' || u.unit_kerja ===  'Divisi Perencanaan dan Litbang');
            if (menuMonevDev) {
                menuMonevDev.style.setProperty('display', isDev ? 'block' : 'none', 'important');
            }
            if (menuLayananDigital) {
                menuLayananDigital.style.setProperty('display', isDev ? 'block' : 'none', 'important');
            }
            if (menuEventAdmin) {
                const isEventAdmin = monbisResolvePegId(u) === '102-119';
                menuEventAdmin.style.setProperty('display', isEventAdmin ? 'block' : 'none', 'important');
            }
            window.MonbisTheme?.sync?.(u);
            return true;
        }

        if (rawUser) {
            try { window.__USER = JSON.parse(rawUser); paint(window.__USER); } catch(e) {}
        }

        const lastVerified = Number(localStorage.getItem(USER_VERIFIED_KEY) || 0);
        const needVerify = !rawUser || !lastVerified || (Date.now() - lastVerified > WHOAMI_REFRESH_MS) || isCrossingFromSSO;
        if (!needVerify) return;

        try {
            const res = await fetch(API_WHOAMI, {
                method: 'GET',
                headers: {
                    'Authorization': token.startsWith('Bearer') ? token : `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (res.ok) {
                const json = await res.json();
                if (json.status === 200 && json.data) {
                    let u = json.data;
                    
                    if (u.job_position === "Divisi Operasional" || u.unit_kerja === "Divisi Operasional") {
                        u.role = "dev";
                    }

                    // Daftarkan ke Global agar script lain bisa akses kodenya
                    window.__USER = u;
                    localStorage.setItem(TOKEN_KEY, token);
                    localStorage.setItem(USER_KEY, JSON.stringify(u));
                    localStorage.setItem(USER_VERIFIED_KEY, String(Date.now()));
                    
                    if (isCrossingFromSSO) {
                        // SOLUSI RACE CONDITION: Muat ulang halaman sekali saja agar semua script bisa pakai LocalStorage yang baru disave
                        window.location.reload();
                    } else {
                        paint(u);
                    }
                }
            }
        } catch (error) {
            console.error("Gagal sinkronisasi data SSO:", error);
        }
    })();

    // --- 5. FUNGSI LOGOUT SSO ---
    function logoutSSO(e) {
        if(e) e.preventDefault();
        
        localStorage.removeItem('dpk_token');
        localStorage.removeItem('dpk_user');
        localStorage.removeItem('dpk_user_verified_at');
        
        const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        document.cookie = "sso_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        if(!isLocal) {
            document.cookie = "sso_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.bkkjateng.co.id;";
        }
        
        const baseApp = window.BASE_APP || window.location.origin + (window.location.pathname.startsWith('/report-dpk') ? '/report-dpk' : '');
        window.location.href = baseApp + '/login';
    }

    // --- 6. EVENT THEME NAVBAR & SIDEBAR ---
    (function () {
        const API_EVENT_ACTIVE = './api/event_theme/active';
        const root = document.documentElement;
        let currentTheme = null;

        function assetUrl(path) {
            if (!path) return '';
            if (/^https?:\/\//i.test(path)) return path;
            return './' + String(path).replace(/^\.?\//, '');
        }

        function applyTheme(theme) {
            const sidebar = document.getElementById('sidebar');
            const sidebarPromo = document.getElementById('monbisSidebarPromo');
            const badge = document.getElementById('monbisEventBadge');
            const badgeTitle = document.getElementById('monbisEventTitle');
            const badgeImage = document.getElementById('monbisEventImage');
            const isDark = root.getAttribute('data-monbis-theme') === 'dark';
            currentTheme = theme || null;

            if (!theme) {
                root.style.removeProperty('--monbis-event-accent');
                root.style.removeProperty('--monbis-event-header-bg');
                root.style.removeProperty('--monbis-event-sidebar-bg');
                root.style.removeProperty('--monbis-event-text');
                root.style.removeProperty('--monbis-event-sidebar-text');
                root.style.removeProperty('--monbis-event-font');
                if (sidebar) sidebar.style.backgroundImage = '';
                if (sidebarPromo) {
                    sidebarPromo.style.backgroundImage = '';
                    sidebarPromo.classList.remove('has-image');
                    const title = sidebarPromo.querySelector('strong');
                    const desc = sidebarPromo.querySelector('span');
                    if (title) title.textContent = 'Semangat kerja';
                    if (desc) desc.textContent = 'Data rapi, keputusan cepat';
                }
                badge?.classList.remove('is-active');
                return;
            }

            root.style.setProperty('--monbis-event-accent', theme.accent_color || '#2563eb');
            root.style.setProperty('--monbis-event-header-bg', isDark ? '#111827' : (theme.header_bg || '#ffffff'));
            root.style.setProperty('--monbis-event-sidebar-bg', isDark ? '#0f172a' : (theme.sidebar_bg || '#ffffff'));
            root.style.setProperty('--monbis-event-text', isDark ? '#e5e7eb' : (theme.text_color || '#0f172a'));
            root.style.setProperty('--monbis-event-sidebar-text', isDark ? '#cbd5e1' : (theme.sidebar_text || '#334155'));
            root.style.setProperty('--monbis-event-font', theme.font_family || 'Roboto, Arial, system-ui, sans-serif');

            const img = assetUrl(theme.image_path);
            if (sidebar && img) {
                const shade = isDark ? 'rgba(15,23,42,.9), rgba(15,23,42,.96)' : 'rgba(255,255,255,.88), rgba(255,255,255,.96)';
                sidebar.style.backgroundImage = `linear-gradient(180deg, ${shade}), url("${img}")`;
                sidebar.style.backgroundSize = 'cover';
                sidebar.style.backgroundPosition = 'center bottom';
            } else if (sidebar) {
                sidebar.style.backgroundImage = '';
            }
            if (sidebarPromo) {
                if (img) {
                    sidebarPromo.style.backgroundImage = `url("${img}")`;
                    sidebarPromo.style.backgroundSize = theme.image_fit || 'cover';
                    sidebarPromo.style.backgroundPosition = theme.image_position || 'center';
                    sidebarPromo.style.backgroundRepeat = 'no-repeat';
                    sidebarPromo.classList.add('has-image');
                } else {
                    sidebarPromo.style.backgroundImage = '';
                    sidebarPromo.style.backgroundSize = '';
                    sidebarPromo.style.backgroundPosition = '';
                    sidebarPromo.style.backgroundRepeat = '';
                    sidebarPromo.classList.remove('has-image');
                }
                const title = sidebarPromo.querySelector('strong');
                const desc = sidebarPromo.querySelector('span');
                if (title) title.textContent = theme.event_name || 'Event Monbis';
                if (desc) desc.textContent = img ? 'Tema event aktif' : 'Data rapi, keputusan cepat';
            }

            if (badge && badgeTitle) {
                badgeTitle.textContent = theme.event_name || 'Event Monbis';
                badge.title = theme.event_name || '';
                badge.classList.add('is-active');
            }
            if (badgeImage) {
                if (img) {
                    badgeImage.src = img;
                    badgeImage.classList.remove('hidden');
                } else {
                    badgeImage.removeAttribute('src');
                    badgeImage.classList.add('hidden');
                }
            }
        }

        async function refreshEventTheme() {
            try {
                const res = await fetch(API_EVENT_ACTIVE, { cache:'no-store' });
                const json = await res.json();
                applyTheme(json && json.status === 200 ? json.data : null);
            } catch (error) {
                console.warn('Gagal memuat event theme:', error);
            }
        }

        window.MonbisEventTheme = window.MonbisEventTheme || {};
        window.MonbisEventTheme.refresh = refreshEventTheme;
        window.MonbisEventTheme.apply = applyTheme;
        document.addEventListener('monbis-theme-change', () => applyTheme(currentTheme));

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', refreshEventTheme);
        } else {
            refreshEventTheme();
        }
    })();
</script>

<style>
    .monbis-ai-chat {
        position:fixed;
        left:18px;
        bottom:18px;
        z-index:1200;
        font-family:var(--monbis-event-font, Roboto, Arial, system-ui, sans-serif);
    }
    .monbis-ai-chat__button {
        display:none !important;
        width:46px;
        height:46px;
        border:1px solid rgba(37,99,235,.28);
        border-radius:16px;
        background:linear-gradient(135deg,#2563eb,#06b6d4);
        color:#fff;
        display:none !important;
        align-items:center;
        justify-content:center;
        box-shadow:0 18px 38px rgba(37,99,235,.26);
        cursor:pointer;
    }
    .monbis-ai-chat__button svg { width:23px; height:23px; }
    /* The sidebar promo is the only chatbot trigger. */
    #monbisAiToggle { display:none !important; visibility:hidden !important; pointer-events:none !important; }
    .monbis-ai-chat__panel {
        position:absolute;
        left:0;
        bottom:0;
        width:min(390px, calc(100vw - 24px));
        height:min(680px, calc(100dvh - 112px));
        max-height:calc(100dvh - 112px);
        background:#fff;
        color:#0f172a;
        border:1px solid #dbe6f3;
        border-radius:18px;
        box-shadow:0 24px 80px rgba(15,23,42,.24);
        overflow:hidden;
        display:none;
    }
    .monbis-ai-chat.is-open .monbis-ai-chat__panel { display:flex; flex-direction:column; }
    .monbis-ai-chat__head {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        padding:12px 13px;
        border-bottom:1px solid #e2e8f0;
        background:linear-gradient(180deg,#f8fbff,#fff);
    }
    .monbis-ai-chat__title { display:flex; align-items:center; gap:9px; min-width:0; }
    .monbis-ai-chat__icon {
        width:34px;
        height:34px;
        border-radius:12px;
        background:#eaf2ff;
        color:#2563eb;
        display:flex;
        align-items:center;
        justify-content:center;
        flex:0 0 auto;
    }
    .monbis-ai-chat__icon svg { width:18px; height:18px; }
    .monbis-ai-chat__title strong { display:block; font-size:14px; font-weight:900; line-height:1.15; }
    .monbis-ai-chat__title span { display:block; font-size:10px; color:#64748b; font-weight:700; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:260px; }
    .monbis-ai-chat__close {
        width:32px;
        height:32px;
        border-radius:10px;
        border:1px solid #dbe6f3;
        background:#fff;
        color:#64748b;
        cursor:pointer;
        font-weight:900;
    }
    .monbis-ai-chat__body {
        padding:12px;
        overflow:auto;
        min-height:0;
        flex:1 1 auto;
        display:flex;
        flex-direction:column;
        gap:9px;
    }
    .monbis-ai-chat__msg {
        max-width:92%;
        padding:9px 10px;
        border-radius:13px;
        font-size:12px;
        line-height:1.45;
        white-space:pre-wrap;
        font-weight:650;
    }
    .monbis-ai-chat__msg--bot { background:#f1f5f9; color:#243044; align-self:flex-start; }
    .monbis-ai-chat__msg--user { background:#2563eb; color:#fff; align-self:flex-end; }
    .monbis-ai-chat__foot {
        padding:10px;
        border-top:1px solid #e2e8f0;
        display:flex;
        gap:8px;
        background:#fff;
    }
    .monbis-ai-chat__input {
        flex:1;
        min-width:0;
        height:38px;
        border:1px solid #cbd5e1;
        border-radius:12px;
        padding:0 10px;
        font-size:12px;
        font-weight:750;
        outline:none;
    }
    .monbis-ai-chat__input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
    .monbis-ai-chat__send {
        width:40px;
        height:38px;
        border:0;
        border-radius:12px;
        background:#059669;
        color:#fff;
        cursor:pointer;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .monbis-ai-chat__send:disabled { opacity:.55; cursor:wait; }
    .monbis-ai-chat__send svg { width:18px; height:18px; }
    .monbis-ai-chat__quick {
        display:flex;
        gap:6px;
        padding:0 10px 10px;
        background:#fff;
        flex-wrap:wrap;
    }
    .monbis-ai-chat__quick button {
        border:1px solid #dbe6f3;
        background:#f8fafc;
        color:#334155;
        border-radius:999px;
        padding:6px 9px;
        font-size:10px;
        font-weight:850;
        cursor:pointer;
    }
    [data-monbis-theme="dark"] .monbis-ai-chat__panel {
        background:#0f172a;
        color:#e5e7eb;
        border-color:#334155;
        box-shadow:0 24px 80px rgba(0,0,0,.45);
    }
    [data-monbis-theme="dark"] .monbis-ai-chat__head,
    [data-monbis-theme="dark"] .monbis-ai-chat__foot,
    [data-monbis-theme="dark"] .monbis-ai-chat__quick { background:#111827; border-color:#334155; }
    [data-monbis-theme="dark"] .monbis-ai-chat__msg--bot { background:#1e293b; color:#e5e7eb; }
    [data-monbis-theme="dark"] .monbis-ai-chat__close,
    [data-monbis-theme="dark"] .monbis-ai-chat__input,
    [data-monbis-theme="dark"] .monbis-ai-chat__quick button {
        background:#0f172a;
        color:#e5e7eb;
        border-color:#334155;
    }
    @media (max-width:640px) {
        .monbis-ai-chat { left:12px; bottom:12px; }
        .monbis-ai-chat__button { width:42px; height:42px; border-radius:14px; }
        .monbis-ai-chat__panel { left:0; width:calc(100vw - 24px); max-height:72dvh; }
    }
</style>

<div class="monbis-ai-chat" id="monbisAiChat">
    <div class="monbis-ai-chat__panel" role="dialog" aria-label="Chatbot Monbis">
        <div class="monbis-ai-chat__head">
            <div class="monbis-ai-chat__title">
                <div class="monbis-ai-chat__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8V4H8"/><rect x="4" y="8" width="16" height="12" rx="3"/><path d="M2 14h2M20 14h2M9 13h.01M15 13h.01M9 17h6"/></svg>
                </div>
                <div>
                    <strong>Asisten Data</strong>
                    <span id="monbisAiPageLabel">Menjelaskan halaman aktif</span>
                </div>
            </div>
            <button type="button" class="monbis-ai-chat__close" id="monbisAiClose" aria-label="Tutup">×</button>
        </div>
        <div class="monbis-ai-chat__body" id="monbisAiBody">
            <div class="monbis-ai-chat__msg monbis-ai-chat__msg--bot">Halo, aku bisa bantu jelaskan data pada halaman yang sedang dibuka. Data yang dikirim hanya ringkasan tampilan, bukan API key atau data rahasia.</div>
        </div>
        <div class="monbis-ai-chat__quick">
            <button type="button" data-ai-question="Jelaskan ringkasan kondisi halaman ini.">Ringkasan</button>
            <button type="button" data-ai-question="Apa anomali atau risiko utama dari data ini?">Risiko</button>
            <button type="button" data-ai-question="Apa tindak lanjut yang disarankan?">Tindak lanjut</button>
        </div>
        <form class="monbis-ai-chat__foot" id="monbisAiForm">
            <input class="monbis-ai-chat__input" id="monbisAiInput" placeholder="Tanya data halaman ini..." autocomplete="off">
            <button class="monbis-ai-chat__send" id="monbisAiSend" type="submit" aria-label="Kirim">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2 11 13"/><path d="m22 2-7 20-4-9-9-4Z"/></svg>
            </button>
        </form>
    </div>
    <button type="button" class="monbis-ai-chat__button" id="monbisAiToggle" title="Asisten Data" aria-label="Asisten Data">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8V4H8"/><rect x="4" y="8" width="16" height="12" rx="3"/><path d="M2 14h2M20 14h2M9 13h.01M15 13h.01M9 17h6"/></svg>
    </button>
</div>

<script>
    (function () {
        const root = document.getElementById('monbisAiChat');
        if (!root || window.__MONBIS_AI_CHAT_INIT__) return;
        window.__MONBIS_AI_CHAT_INIT__ = true;

        const body = document.getElementById('monbisAiBody');
        const input = document.getElementById('monbisAiInput');
        const form = document.getElementById('monbisAiForm');
        const send = document.getElementById('monbisAiSend');
        const label = document.getElementById('monbisAiPageLabel');
        const API_CHAT = './api/chatbot/ask';

        function canUseAi() {
            const user = window.getUser?.();
            if (!user) return false;
            return user.role === 'dev'
                || user.unit_kerja === 'Divisi Operasional'
                || user.unit_kerja === 'Dewan Komisaris dan Direksi';
        }

        function syncAiAccess() {
            const allowed = canUseAi();
            root.dataset.access = allowed ? 'allowed' : 'denied';
            const promo = document.getElementById('monbisSidebarPromo');
            const promoTitle = document.getElementById('monbisSidebarPromoTitle');
            const promoSubtitle = document.getElementById('monbisSidebarPromoSubtitle');
            promo?.setAttribute('aria-disabled', allowed ? 'false' : 'true');
            promo?.classList.toggle('is-ai-access', allowed);
            promo?.setAttribute('aria-label', allowed ? 'Buka Asisten Data' : 'Semangat kerja');
            if (promoTitle) promoTitle.textContent = allowed ? 'Asisten Data' : 'Semangat kerja';
            if (promoSubtitle) promoSubtitle.textContent = allowed ? 'Tanya kondisi halaman' : 'Data rapi, keputusan cepat';
        }

        syncAiAccess();
        const accessTimer = window.setInterval(() => {
            syncAiAccess();
            if (window.getUser?.()) window.clearInterval(accessTimer);
        }, 500);

        function clean(text, max = 160) {
            return String(text || '').replace(/\s+/g, ' ').trim().slice(0, max);
        }

        function visible(el) {
            if (!el) return false;
            const style = window.getComputedStyle(el);
            return style.display !== 'none' && style.visibility !== 'hidden' && el.offsetParent !== null;
        }

        function addMessage(text, type) {
            const msg = document.createElement('div');
            msg.className = 'monbis-ai-chat__msg monbis-ai-chat__msg--' + (type || 'bot');
            msg.textContent = text;
            body.appendChild(msg);
            body.scrollTop = body.scrollHeight;
            return msg;
        }

        function cleanAnswer(text) {
            return String(text || '')
                .replace(/\*\*(.*?)\*\*/gs, '$1')
                .replace(/\*(.*?)\*/gs, '$1')
                .replace(/^\s*#{1,6}\s*/gm, '')
                .replace(/[ \t]+\n/g, '\n')
                .trim();
        }

        function pageTitle() {
            const candidates = [
                document.querySelector('.mb-page-title h1'),
                document.querySelector('.mb-page-header h1'),
                document.querySelector('main h1'),
                document.querySelector('h1'),
                document.querySelector('.mb-report-toolbar__title')
            ];
            for (const node of candidates) {
                const text = clean(node?.textContent, 90);
                if (text) return text;
            }
            return clean(document.title || location.pathname, 90);
        }

        function collectFilters(scope) {
            return Array.from(scope.querySelectorAll('input, select'))
                .filter(node => visible(node) && !node.closest('.monbis-ai-chat') && !node.closest('.mb-modal'))
                .slice(0, 12)
                .map(node => {
                    const field = node.closest('label,.mb-field');
                    const labelText = clean(field?.querySelector('span,label,.mb-field-label')?.textContent || node.getAttribute('aria-label') || node.id || node.name, 40);
                    const value = node.tagName === 'SELECT'
                        ? clean(node.options[node.selectedIndex]?.text || node.value, 70)
                        : clean(node.value, 70);
                    return value ? { label: labelText, value } : null;
                })
                .filter(Boolean);
        }

        function collectCards(scope) {
            const selectors = '.mb-summary-card,.mb-metric-card,.mb-report-card,[class*="metric"],[class*="card"]';
            return Array.from(scope.querySelectorAll(selectors))
                .filter(node => visible(node) && !node.closest('.monbis-ai-chat') && !node.closest('.mb-modal') && !node.querySelector('table'))
                .slice(0, 16)
                .map(node => clean(node.textContent, 220))
                .filter(text => text && text.length > 8);
        }

        function collectTables(scope) {
            return Array.from(scope.querySelectorAll('table'))
                .filter(table => visible(table) && !table.closest('.monbis-ai-chat') && !table.closest('.mb-modal'))
                .slice(0, 2)
                .map(table => {
                    const headers = Array.from(table.querySelectorAll('thead th')).map(th => clean(th.textContent, 36)).filter(Boolean).slice(0, 12);
                    const rows = Array.from(table.querySelectorAll('tbody tr'))
                        .filter(tr => visible(tr))
                        .slice(0, 12)
                        .map(tr => Array.from(tr.children).map(td => clean(td.textContent, 60)).filter(Boolean).slice(0, 12))
                        .filter(row => row.length);
                    return { headers, rows };
                })
                .filter(table => table.headers.length || table.rows.length);
        }

        function collectContext() {
            const main = document.querySelector('main') || document.body;
            const title = pageTitle();
            if (label) label.textContent = title;
            return {
                page: title,
                path: location.pathname,
                filters: collectFilters(main),
                cards: collectCards(main),
                tables: collectTables(main),
                note: 'Konteks dipotong otomatis untuk hemat token. Detail nasabah/rekening tidak perlu dijelaskan.'
            };
        }

        async function ask(question) {
            if (!canUseAi()) return;
            const q = clean(question || input.value, 1000);
            if (!q) return;
            addMessage(q, 'user');
            input.value = '';
            send.disabled = true;
            const waiting = addMessage('Membaca halaman dan bertanya ke Gemini...', 'bot');
            try {
                const res = await fetch(API_CHAT, {
                    method:'POST',
                    headers:{ 'Content-Type':'application/json' },
                    body:JSON.stringify({ question:q, context:collectContext() })
                });
                const json = await res.json();
                if (!res.ok || json.status !== 200) throw new Error(json.message || 'Gagal meminta jawaban chatbot.');
                waiting.textContent = cleanAnswer(json.data?.answer || 'Belum ada jawaban.');
            } catch (error) {
                waiting.textContent = error.message || 'Chatbot gagal memproses data.';
            } finally {
                send.disabled = false;
            }
        }

        document.getElementById('monbisAiToggle')?.addEventListener('click', () => {
            root.classList.toggle('is-open');
            collectContext();
            if (root.classList.contains('is-open')) setTimeout(() => input?.focus(), 80);
        });
        const sidebarTrigger = document.getElementById('monbisSidebarPromo');
        function toggleFromSidebar() {
            if (!canUseAi()) return;
            root.classList.toggle('is-open');
            collectContext();
            if (root.classList.contains('is-open')) setTimeout(() => input?.focus(), 80);
        }
        sidebarTrigger?.addEventListener('click', toggleFromSidebar);
        sidebarTrigger?.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleFromSidebar();
            }
        });
        document.getElementById('monbisAiClose')?.addEventListener('click', () => root.classList.remove('is-open'));
        form?.addEventListener('submit', event => {
            event.preventDefault();
            ask(input.value);
        });
        document.querySelectorAll('[data-ai-question]').forEach(btn => {
            btn.addEventListener('click', () => ask(btn.dataset.aiQuestion || 'Jelaskan data halaman ini.'));
        });
    })();
</script>

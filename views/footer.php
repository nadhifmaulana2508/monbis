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

            const isDev = (u.role === 'dev' || u.unit_kerja === 'Divisi Operasional' || u.unit_kerja === 'Dewan Komisaris dan Direksi');
            if (menuMonevDev) {
                menuMonevDev.style.setProperty('display', isDev ? 'block' : 'none', 'important');
            }
            if (menuLayananDigital) {
                menuLayananDigital.style.setProperty('display', isDev ? 'block' : 'none', 'important');
            }
            if (menuEventAdmin) {
                const isEventAdmin = monbisResolvePegId(u) > 0;
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
            root.style.setProperty('--monbis-event-font', theme.font_family || 'Inter, system-ui, sans-serif');

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

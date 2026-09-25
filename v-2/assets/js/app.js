(() => {
  'use strict';
  const sidebar = document.getElementById('v2Sidebar');
  const overlay = document.querySelector('[data-v2-sidebar-close]');
  const mobileQuery = window.matchMedia('(max-width: 780px)');
  const toggleSidebar = (open) => {
    sidebar?.classList.toggle('is-open', open);
    overlay?.classList.toggle('is-visible', open);
    document.body.classList.toggle('v2-drawer-open', open && mobileQuery.matches);
    document.querySelectorAll('[data-v2-sidebar-toggle]').forEach((button) => {
      button.classList.toggle('is-open', open);
      button.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
      button.title = open ? 'Tutup menu' : 'Buka menu';
    });
  };
  document.querySelectorAll('[data-v2-sidebar-toggle]').forEach((button) => button.addEventListener('click', () => toggleSidebar(!sidebar?.classList.contains('is-open'))));
  document.querySelectorAll('[data-v2-sidebar-close]').forEach((button) => button.addEventListener('click', () => toggleSidebar(false)));
  const navGroups = [...document.querySelectorAll('.v2-nav-group')];
  const setNavGroupState = (group, open) => {
    const button = group?.querySelector('[data-v2-nav-group]');
    if (!button) return;
    group.classList.toggle('is-open', open);
    button.setAttribute('aria-expanded', open ? 'true' : 'false');
  };
  const closeNavGroups = (except = null) => navGroups.forEach((group) => {
    if (group !== except) setNavGroupState(group, false);
  });
  navGroups.forEach((group, index) => {
    const button = group.querySelector('[data-v2-nav-group]');
    const submenu = group.querySelector('.v2-nav-sub');
    if (!button) return;
    button.setAttribute('aria-expanded', group.classList.contains('is-open') ? 'true' : 'false');
    if (submenu) {
      submenu.id = submenu.id || `v2-nav-sub-${index}`;
      button.setAttribute('aria-controls', submenu.id);
    }
    button.addEventListener('click', () => {
      const open = group.classList.contains('is-open');
      closeNavGroups();
      if (!open) setNavGroupState(group, true);
    });
  });
  document.querySelectorAll('.v2-nav-sub-item.is-active').forEach((item) => item.closest('.v2-nav-group')?.classList.add('is-open'));
  document.querySelectorAll('.v2-nav-group.is-open').forEach((group) => setNavGroupState(group, true));
  sidebar?.addEventListener('mouseleave', () => {
    if (window.matchMedia('(min-width: 781px) and (hover: hover)').matches) closeNavGroups();
  });
  document.querySelectorAll('.v2-nav a').forEach((link) => link.addEventListener('click', () => {
    if (mobileQuery.matches) toggleSidebar(false);
  }));
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && sidebar?.classList.contains('is-open')) toggleSidebar(false);
  });
  const closeOnDesktop = (event) => { if (!event.matches) toggleSidebar(false); };
  if (mobileQuery.addEventListener) mobileQuery.addEventListener('change', closeOnDesktop);
  else mobileQuery.addListener(closeOnDesktop);

  const user = (() => { try { return JSON.parse(localStorage.getItem('dpk_user') || 'null') || {}; } catch { return {}; } })();
  const name = user.full_name || user.nama || user.employee_id || 'Pengguna';
  const initials = name.split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase() || 'MB';
  document.querySelector('[data-v2-user-name]')?.replaceChildren(document.createTextNode(name));
  document.querySelector('[data-v2-user-initials]')?.replaceChildren(document.createTextNode(initials));
  if (Object.keys(user).length) {
    const fields = ['job_position', 'unit_kerja', 'role'].map((key) => String(user[key] || '').toLowerCase());
    const isDev = fields.some((value) => value.includes('divisi operasional') || value === 'dev');
    const kpiAllowed = isDev || fields[1].includes('divisi sdm dan umum');
    const rbbAllowed = ['id_peg', 'idPeg', 'id_pegawai', 'idPegawai', 'employee_id'].some((key) => String(user[key] || '').trim() === '102-119');
    document.querySelectorAll('[data-v2-access]').forEach((item) => {
      const allowed = item.dataset.v2Access === 'kpi' ? kpiAllowed : rbbAllowed;
      if (!allowed) item.hidden = true;
    });
  }

  document.querySelectorAll('[data-v2-theme-toggle]').forEach((button) => button.addEventListener('click', () => {
    const current = window.V2Settings?.get()?.theme || document.documentElement.dataset.v2Theme || 'light';
    window.V2Settings?.set('theme', current === 'dark' ? 'light' : 'dark');
  }));
  document.querySelectorAll('[data-v2-modal-open]').forEach((button) => button.addEventListener('click', () => {
    document.getElementById(button.dataset.v2ModalOpen)?.removeAttribute('hidden');
  }));
  document.querySelectorAll('[data-v2-modal-close]').forEach((button) => button.addEventListener('click', () => {
    button.closest('.v2-modal')?.setAttribute('hidden', '');
  }));
  window.V2Modal = {
    open(id) { document.getElementById(id)?.removeAttribute('hidden'); },
    close(id) { document.getElementById(id)?.setAttribute('hidden', ''); }
  };
})();

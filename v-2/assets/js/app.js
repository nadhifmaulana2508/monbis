(() => {
  'use strict';
  const sidebar = document.getElementById('v2Sidebar');
  const overlay = document.querySelector('[data-v2-sidebar-close]');
  const toggleSidebar = (open) => {
    sidebar?.classList.toggle('is-open', open);
    overlay?.classList.toggle('is-visible', open);
  };
  document.querySelectorAll('[data-v2-sidebar-toggle]').forEach((button) => button.addEventListener('click', () => toggleSidebar(true)));
  document.querySelectorAll('[data-v2-sidebar-close]').forEach((button) => button.addEventListener('click', () => toggleSidebar(false)));
  document.querySelectorAll('[data-v2-nav-group]').forEach((button) => {
    button.addEventListener('click', () => button.closest('.v2-nav-group')?.classList.toggle('is-open'));
  });
  document.querySelectorAll('.v2-nav-sub-item.is-active').forEach((item) => item.closest('.v2-nav-group')?.classList.add('is-open'));

  const user = (() => { try { return JSON.parse(localStorage.getItem('dpk_user') || 'null') || {}; } catch { return {}; } })();
  const name = user.full_name || user.nama || user.employee_id || 'Pengguna';
  const initials = name.split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase() || 'MB';
  document.querySelector('[data-v2-user-name]')?.replaceChildren(document.createTextNode(name));
  document.querySelector('[data-v2-user-initials]')?.replaceChildren(document.createTextNode(initials));

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

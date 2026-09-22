(() => {
  'use strict';
  const root = document.documentElement;
  const key = 'fe_workspace_settings_v1';
  const defaults = { theme: 'light', scale: 'normal', density: 'comfortable', accent: 'teal', sidebar: 'expanded' };
  const read = () => { try { return { ...defaults, ...(JSON.parse(localStorage.getItem(key) || '{}')) }; } catch { return { ...defaults }; } };
  let settings = read();
  const apply = () => {
    root.dataset.v2Theme = settings.theme;
    root.dataset.v2Scale = settings.scale;
    root.dataset.v2Density = settings.density;
    root.dataset.v2Accent = settings.accent;
    document.body.classList.toggle('v2-density-compact', settings.density === 'compact');
    document.body.classList.toggle('v2-sidebar-collapsed', settings.sidebar === 'collapsed');
    localStorage.setItem(key, JSON.stringify(settings));
    document.querySelectorAll('[data-v2-setting]').forEach((control) => {
      if (control.dataset.v2Setting in settings) control.value = settings[control.dataset.v2Setting];
    });
    document.querySelector('[data-v2-sidebar-state]')?.replaceChildren(document.createTextNode(settings.sidebar === 'collapsed' ? 'Ringkas' : 'Lebar'));
    document.querySelectorAll('[data-v2-theme-toggle]').forEach((button) => {
      const dark = settings.theme === 'dark';
      button.classList.toggle('is-dark', dark);
      button.title = dark ? 'Gunakan mode terang' : 'Gunakan mode gelap';
      button.setAttribute('aria-label', button.title);
    });
    document.querySelectorAll('[data-v2-sidebar-setting]').forEach((button) => {
      button.classList.toggle('is-collapsed', settings.sidebar === 'collapsed');
      button.title = settings.sidebar === 'collapsed' ? 'Buka sidebar' : 'Ringkas sidebar';
      button.setAttribute('aria-label', button.title);
    });
  };
  const save = (name, value) => { settings[name] = value; apply(); };
  window.V2Settings = { get: () => ({ ...settings }), set: save, reset: () => { settings = { ...defaults }; apply(); } };
  apply();
  document.querySelectorAll('[data-v2-setting]').forEach((control) => control.addEventListener('change', () => save(control.dataset.v2Setting, control.value)));
  document.querySelector('[data-v2-settings-reset]')?.addEventListener('click', () => { window.V2Settings.reset(); window.V2Toast?.show('Pengaturan dikembalikan ke default.'); });
  document.querySelectorAll('[data-v2-sidebar-setting]').forEach((button) => button.addEventListener('click', () => save('sidebar', settings.sidebar === 'collapsed' ? 'expanded' : 'collapsed')));
  document.querySelectorAll('[data-v2-toast]').forEach((button) => button.addEventListener('click', () => window.V2Toast?.show(button.dataset.v2Toast || 'Aksi berhasil.')));
  document.querySelectorAll('[data-v2-alert-close]').forEach((button) => button.addEventListener('click', () => button.closest('.v2-alert')?.remove()));
  const filterPanel = document.querySelector('[data-v2-filter-panel]');
  const filterToggle = document.querySelector('[data-v2-filter-toggle]');
  const filterCount = document.querySelector('[data-v2-filter-count]');
  const updateFilterCount = () => {
    if (!filterCount || !filterPanel) return;
    const active = [...filterPanel.querySelectorAll('[data-v2-filter-field]')].filter((control) => control.value && control.value !== 'ALL').length;
    filterCount.textContent = String(active);
    filterCount.hidden = active === 0;
  };
  if (filterToggle) {
    filterToggle.hidden = !filterPanel;
    filterToggle.addEventListener('click', () => { if (!filterPanel) return; filterPanel.hidden = !filterPanel.hidden; filterToggle.classList.toggle('is-active', !filterPanel.hidden); });
  }
  filterPanel?.querySelectorAll('[data-v2-filter-field]').forEach((control) => control.addEventListener('input', updateFilterCount));
  filterPanel?.querySelectorAll('[data-v2-filter-field]').forEach((control) => control.addEventListener('change', updateFilterCount));
  filterPanel?.querySelector('[data-v2-filter-close]')?.addEventListener('click', () => { filterPanel.hidden = true; filterToggle?.classList.remove('is-active'); });
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && filterPanel && !filterPanel.hidden) { filterPanel.hidden = true; filterToggle?.classList.remove('is-active'); } });
  document.addEventListener('click', (event) => { if (filterPanel && !filterPanel.hidden && !filterPanel.contains(event.target) && !filterToggle?.contains(event.target)) { filterPanel.hidden = true; filterToggle?.classList.remove('is-active'); } });
  updateFilterCount();
  document.querySelectorAll('[data-v2-tab]').forEach((tab) => tab.addEventListener('click', () => {
    const group = tab.closest('.v2-tabs');
    group?.querySelectorAll('[data-v2-tab]').forEach((item) => item.classList.toggle('is-active', item === tab));
    const target = tab.dataset.v2TabTarget;
    if (target) document.querySelectorAll('[data-v2-tab-panel]').forEach((panel) => panel.hidden = panel.dataset.v2TabPanel !== target);
  }));
  window.V2Toast = {
    show(message) {
      let stack = document.querySelector('.v2-toast-stack');
      if (!stack) { stack = document.createElement('div'); stack.className = 'v2-toast-stack'; document.body.appendChild(stack); }
      const toast = document.createElement('div'); toast.className = 'v2-toast'; toast.textContent = message; stack.appendChild(toast);
      window.setTimeout(() => toast.remove(), 3200);
    }
  };
})();

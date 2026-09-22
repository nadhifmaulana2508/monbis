(() => {
  'use strict';
  const root = document.documentElement;
  const key = 'monbis_v2_settings';
  const defaults = { theme: 'dim', scale: 'normal', density: 'comfortable', accent: 'teal', sidebar: 'expanded' };
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
  };
  const save = (name, value) => { settings[name] = value; apply(); };
  window.V2Settings = { get: () => ({ ...settings }), set: save, reset: () => { settings = { ...defaults }; apply(); } };
  apply();
  document.querySelectorAll('[data-v2-setting]').forEach((control) => control.addEventListener('change', () => save(control.dataset.v2Setting, control.value)));
  document.querySelector('[data-v2-settings-reset]')?.addEventListener('click', () => { window.V2Settings.reset(); window.V2Toast?.show('Pengaturan dikembalikan ke default.'); });
  document.querySelector('[data-v2-sidebar-setting]')?.addEventListener('click', () => save('sidebar', settings.sidebar === 'collapsed' ? 'expanded' : 'collapsed'));
  document.querySelectorAll('[data-v2-toast]').forEach((button) => button.addEventListener('click', () => window.V2Toast?.show(button.dataset.v2Toast || 'Aksi berhasil.')));
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

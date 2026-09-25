(() => {
  'use strict';
  const root = document.documentElement;
  const key = 'fe_workspace_settings_v1';
  const defaults = { theme: 'light', scale: 'normal', density: 'comfortable', accent: 'teal', sidebar: 'auto' };
  const read = () => {
    try {
      const saved = { ...defaults, ...(JSON.parse(localStorage.getItem(key) || '{}')) };
      // Prefer the new hover mode for the old v2 default so the sidebar does
      // not stay permanently wide after this navigation upgrade.
      if (saved.sidebar === 'expanded' || saved.sidebar === 'collapsed') saved.sidebar = 'auto';
      return saved;
    } catch { return { ...defaults }; }
  };
  let settings = read();
  const apply = () => {
    root.dataset.v2Theme = settings.theme;
    root.dataset.v2Scale = settings.scale;
    root.dataset.v2Density = settings.density;
    root.dataset.v2Accent = settings.accent;
    document.body.classList.toggle('v2-density-compact', settings.density === 'compact');
    document.body.classList.toggle('v2-sidebar-auto', settings.sidebar === 'auto');
    document.body.classList.toggle('v2-sidebar-collapsed', settings.sidebar === 'collapsed');
    localStorage.setItem(key, JSON.stringify(settings));
    document.querySelectorAll('[data-v2-setting]').forEach((control) => {
      if (control.dataset.v2Setting in settings) control.value = settings[control.dataset.v2Setting];
    });
    const sidebarState = settings.sidebar === 'auto' ? 'Otomatis' : settings.sidebar === 'collapsed' ? 'Ringkas' : 'Lebar';
    document.querySelector('[data-v2-sidebar-state]')?.replaceChildren(document.createTextNode(sidebarState));
    document.querySelectorAll('[data-v2-theme-toggle]').forEach((button) => {
      const dark = settings.theme === 'dark';
      button.classList.toggle('is-dark', dark);
      button.title = dark ? 'Gunakan mode terang' : 'Gunakan mode gelap';
      button.setAttribute('aria-label', button.title);
    });
    document.querySelectorAll('[data-v2-sidebar-setting]').forEach((button) => {
      button.classList.toggle('is-collapsed', settings.sidebar === 'collapsed');
      button.title = settings.sidebar === 'auto' ? 'Sidebar otomatis: arahkan mouse untuk membuka' : settings.sidebar === 'collapsed' ? 'Buka sidebar saat diklik' : 'Gunakan mode otomatis';
      button.setAttribute('aria-label', button.title);
    });
  };
  const save = (name, value) => { settings[name] = value; apply(); };
  window.V2Settings = { get: () => ({ ...settings }), set: save, reset: () => { settings = { ...defaults }; apply(); } };
  apply();
  document.querySelectorAll('[data-v2-setting]').forEach((control) => control.addEventListener('change', () => save(control.dataset.v2Setting, control.value)));
  document.querySelector('[data-v2-settings-reset]')?.addEventListener('click', () => { window.V2Settings.reset(); window.V2Toast?.show('Pengaturan dikembalikan ke default.'); });
  document.querySelectorAll('[data-v2-sidebar-setting]').forEach((button) => button.addEventListener('click', () => save('sidebar', settings.sidebar === 'auto' ? 'collapsed' : 'auto')));
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

  const tooltip = { anchor: null, node: null };
  const tooltipTarget = (event) => event.target?.closest?.('[data-v2-tooltip]');
  const hideTooltip = () => {
    tooltip.node?.remove();
    tooltip.anchor = null;
    tooltip.node = null;
  };
  const showTooltip = (anchor) => {
    const text = String(anchor?.dataset?.v2Tooltip || '').trim();
    if (!text) return hideTooltip();
    hideTooltip();
    const node = document.createElement('div');
    node.className = 'v2-tooltip-popover';
    node.dataset.placement = 'top';
    node.setAttribute('role', 'tooltip');
    node.textContent = text;
    document.body.appendChild(node);
    const anchorRect = anchor.getBoundingClientRect();
    const tooltipRect = node.getBoundingClientRect();
    const gap = 8;
    const left = Math.max(8, Math.min(anchorRect.left + (anchorRect.width - tooltipRect.width) / 2, window.innerWidth - tooltipRect.width - 8));
    let top = anchorRect.top - tooltipRect.height - gap;
    if (top < 8) { top = anchorRect.bottom + gap; node.dataset.placement = 'bottom'; }
    node.style.left = `${left}px`;
    node.style.top = `${top}px`;
    tooltip.anchor = anchor;
    tooltip.node = node;
  };
  document.addEventListener('pointerover', (event) => {
    const target = tooltipTarget(event);
    if (!target || (event.relatedTarget && target.contains(event.relatedTarget))) return;
    showTooltip(target);
  });
  document.addEventListener('pointerout', (event) => {
    const target = tooltipTarget(event);
    if (target && (!event.relatedTarget || !target.contains(event.relatedTarget)) && tooltip.anchor === target) hideTooltip();
  });
  document.addEventListener('focusin', (event) => { const target = tooltipTarget(event); if (target) showTooltip(target); });
  document.addEventListener('focusout', (event) => { const target = tooltipTarget(event); if (target && tooltip.anchor === target) hideTooltip(); });
  document.addEventListener('click', (event) => { const target = tooltipTarget(event); if (target) { if (tooltip.anchor === target) hideTooltip(); else showTooltip(target); } else hideTooltip(); });
  window.addEventListener('scroll', hideTooltip, true);
  window.addEventListener('resize', hideTooltip);
  window.V2Tooltip = { show: showTooltip, hide: hideTooltip };
})();

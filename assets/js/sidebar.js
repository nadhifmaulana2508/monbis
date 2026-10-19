(() => {
  'use strict';

  const BREAKPOINT = 768;
  const COLLAPSE_DELAY = 3000;

  const initSidebar = () => {
    const sidebar = document.getElementById('sidebar');
    const nav = sidebar?.querySelector('nav');
    if (!sidebar || !nav || sidebar.dataset.sidebarReady === '1') return;

    sidebar.dataset.sidebarReady = '1';
    const shell = sidebar.closest('.monbis-app-shell');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleButton = document.getElementById('btnToggleSidebar');
    const groups = [...nav.querySelectorAll(':scope > .accordion-group')];
    const parts = (group) => ({
      button: group?.querySelector(':scope > .accordion-btn'),
      content: group?.querySelector(':scope > .accordion-content')
    });
    const isDesktop = () => window.innerWidth >= BREAKPOINT;

    let expanded = false;
    let desktopMode = isDesktop();
    let closeTimer = 0;
    let openGroup = null;

    const clearCloseTimer = () => {
      window.clearTimeout(closeTimer);
      closeTimer = 0;
    };

    const syncToggleControl = (open) => {
      const label = open ? 'Tutup sidebar' : 'Buka sidebar';
      toggleButton?.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggleButton?.setAttribute('aria-label', label);
      toggleButton?.setAttribute('title', label);
      toggleButton?.querySelector('[data-sidebar-toggle-icon="closed"]')?.classList.toggle('hidden', open);
      toggleButton?.querySelector('[data-sidebar-toggle-icon="open"]')?.classList.toggle('hidden', !open);
    };

    const setExpanded = (value) => {
      const next = Boolean(value) && isDesktop();
      if (next === expanded) {
        syncToggleControl(expanded);
        return;
      }
      expanded = next;
      sidebar.classList.toggle('is-expanded', expanded);
      sidebar.dataset.sidebarState = expanded ? 'open' : 'closed';
      shell?.classList.toggle('sidebar-expanded', expanded);
      syncToggleControl(expanded);
    };

    const scheduleClose = () => {
      clearCloseTimer();
      if (!isDesktop() || !expanded) return;
      closeTimer = window.setTimeout(() => {
        closeTimer = 0;
        if (expanded && !sidebar.matches(':hover')) setExpanded(false);
      }, COLLAPSE_DELAY);
    };

    const setGroupState = (group, open) => {
      const {button, content} = parts(group);
      if (!button || !content) return;
      content.classList.toggle('hidden', !open);
      button.classList.toggle('is-open', open);
      button.setAttribute('aria-expanded', open ? 'true' : 'false');
      button.querySelector('.caret')?.classList.toggle('rotate-180', open);
      if (open) openGroup = group;
      else if (openGroup === group) openGroup = null;
    };

    const closeOpenGroup = (except = null) => {
      if (openGroup && openGroup !== except) setGroupState(openGroup, false);
    };

    groups.forEach((group, index) => {
      const {button, content} = parts(group);
      if (!button || !content) return;
      const contentId = content.id || `sidebar-menu-${index}`;
      content.id = contentId;
      button.type = 'button';
      button.setAttribute('aria-expanded', 'false');
      button.setAttribute('aria-controls', contentId);
    });

    const normalizePage = (value) => {
      try {
        const url = new URL(value, window.location.href);
        const path = url.pathname.replace(/\/+$/, '');
        return path.split('/').pop() || 'dashboard';
      } catch (error) {
        return String(value || '').split('?')[0].replace(/^\.?\//, '').replace(/\/+$/, '');
      }
    };

    const currentPage = normalizePage(window.location.href);
    let activeLink = null;
    nav.querySelectorAll('a[href]').forEach((link) => {
      const active = normalizePage(link.href) === currentPage;
      link.classList.toggle('is-active', active);
      if (!active) link.classList.remove('bg-blue-50', 'text-blue-600');
      if (active && !activeLink) activeLink = link;
    });

    const activeGroup = activeLink?.closest('.accordion-group');
    if (activeGroup) {
      activeGroup.querySelector(':scope > .accordion-btn')?.classList.add('is-active');
      setGroupState(activeGroup, true);
    }

    const openMobile = () => {
      if (isDesktop()) return;
      sidebar.classList.remove('-translate-x-full');
      overlay?.classList.remove('hidden');
      document.body.classList.add('sidebar-mobile-open');
      syncToggleControl(true);
    };

    const closeMobile = (force = false) => {
      if (isDesktop() && !force) return;
      sidebar.classList.add('-translate-x-full');
      overlay?.classList.add('hidden');
      document.body.classList.remove('sidebar-mobile-open');
      syncToggleControl(false);
    };

    const toggleSidebar = () => {
      if (isDesktop()) {
        clearCloseTimer();
        if (expanded) setExpanded(false);
        else {
          setExpanded(true);
          scheduleClose();
        }
        return;
      }
      if (sidebar.classList.contains('-translate-x-full')) openMobile();
      else closeMobile();
    };

    nav.addEventListener('click', (event) => {
      const button = event.target.closest('.accordion-btn');
      if (!button || !nav.contains(button)) return;
      const group = groups.find((item) => parts(item).button === button);
      if (!group) return;
      event.preventDefault();
      event.stopPropagation();
      if (isDesktop() && !expanded) setExpanded(true);
      const content = parts(group).content;
      const isOpen = Boolean(content && !content.classList.contains('hidden'));
      if (isOpen) setGroupState(group, false);
      else {
        closeOpenGroup(group);
        setGroupState(group, true);
      }
    });

    nav.addEventListener('click', (event) => {
      if (event.target.closest('a[href]') && !isDesktop()) closeMobile();
    });

    sidebar.addEventListener('mouseenter', clearCloseTimer, {passive: true});
    sidebar.addEventListener('mouseleave', scheduleClose, {passive: true});
    sidebar.addEventListener('focusin', () => {
      if (isDesktop()) {
        clearCloseTimer();
        if (!expanded) setExpanded(true);
      }
    });
    sidebar.addEventListener('focusout', (event) => {
      if (isDesktop() && !sidebar.contains(event.relatedTarget)) scheduleClose();
    });
    toggleButton?.addEventListener('click', toggleSidebar);
    overlay?.addEventListener('click', closeMobile);
    syncToggleControl(false);

    document.addEventListener('keydown', (event) => {
      if (event.key !== 'Escape') return;
      if (!isDesktop()) closeMobile();
      else {
        clearCloseTimer();
        setExpanded(false);
      }
    });

    const syncViewportMode = () => {
      const nextDesktopMode = isDesktop();
      if (nextDesktopMode === desktopMode) return;
      desktopMode = nextDesktopMode;
      clearCloseTimer();
      if (nextDesktopMode) {
        closeMobile(true);
        setExpanded(false);
      } else {
        expanded = false;
        sidebar.classList.remove('is-expanded');
        shell?.classList.remove('sidebar-expanded');
        syncToggleControl(false);
      }
    };

    const mediaQuery = window.matchMedia(`(min-width: ${BREAKPOINT}px)`);
    mediaQuery.addEventListener?.('change', syncViewportMode);
    window.addEventListener('resize', syncViewportMode, {passive: true});
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar, {once: true});
  } else {
    initSidebar();
  }
})();

(() => {
  'use strict';

  const BREAKPOINT = 768;
  const EXPAND_DELAY = 0;
  const COLLAPSE_DELAY = 35;

  const initSidebar = () => {
    const sidebar = document.getElementById('sidebar');
    const nav = sidebar?.querySelector('nav');
    if (!sidebar || !nav || sidebar.dataset.sidebarReady === '1') return;

    sidebar.dataset.sidebarReady = '1';
    const shell = sidebar.closest('.monbis-app-shell');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleButton = document.getElementById('btnToggleSidebar');
    const content = sidebar.nextElementSibling;
    const groups = [...nav.querySelectorAll(':scope > .accordion-group')];
    const parts = (group) => ({
      button: group?.querySelector(':scope > .accordion-btn'),
      content: group?.querySelector(':scope > .accordion-content')
    });
    const isDesktop = () => window.innerWidth >= BREAKPOINT;
    let expanded = false;
    let desktopMode = isDesktop();
    let expandTimer = 0;
    let collapseTimer = 0;
    let openGroup = null;

    const setExpanded = (value) => {
      const next = Boolean(value) && isDesktop();
      if (next === expanded) return;
      expanded = next;
      sidebar.classList.toggle('is-expanded', expanded);
      shell?.classList.toggle('sidebar-expanded', expanded);
      if (content) content.classList.toggle('sidebar-content-shifted', expanded);
    };

    const scheduleExpanded = (value, delay = 0) => {
      window.clearTimeout(expandTimer);
      window.clearTimeout(collapseTimer);
      if (delay <= 0) {
        setExpanded(value);
        return;
      }
      if (value) {
        expandTimer = window.setTimeout(() => setExpanded(true), delay);
      } else {
        collapseTimer = window.setTimeout(() => setExpanded(false), delay);
      }
    };

    const setGroupState = (group, open) => {
      const {button, content: groupContent} = parts(group);
      if (!button || !groupContent) return;
      groupContent.classList.toggle('hidden', !open);
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
      const {button, content: groupContent} = parts(group);
      if (!button || !groupContent) return;
      const contentId = groupContent.id || `sidebar-menu-${index}`;
      groupContent.id = contentId;
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
      if (isDesktop()) setExpanded(false);
    }

    nav.addEventListener('click', (event) => {
      const button = event.target.closest('.accordion-btn');
      if (!button || !nav.contains(button)) return;
      const group = groups.find((item) => parts(item).button === button);
      if (!group) return;
      event.preventDefault();
      event.stopPropagation();
      if (isDesktop()) setExpanded(true);
      const groupContent = parts(group).content;
      const isOpen = Boolean(groupContent && !groupContent.classList.contains('hidden'));
      if (isOpen) setGroupState(group, false);
      else {
        closeOpenGroup(group);
        setGroupState(group, true);
      }
    });

    nav.addEventListener('click', (event) => {
      if (event.target.closest('a[href]') && !isDesktop()) closeMobile();
    });

    const openMobile = () => {
      if (isDesktop()) return;
      sidebar.classList.remove('-translate-x-full');
      overlay?.classList.remove('hidden');
      document.body.classList.add('sidebar-mobile-open');
      toggleButton?.setAttribute('aria-expanded', 'true');
    };

    const closeMobile = (force = false) => {
      if (isDesktop() && !force) return;
      sidebar.classList.add('-translate-x-full');
      overlay?.classList.add('hidden');
      document.body.classList.remove('sidebar-mobile-open');
      toggleButton?.setAttribute('aria-expanded', 'false');
    };

    const toggleMobile = () => {
      if (sidebar.classList.contains('-translate-x-full')) openMobile();
      else closeMobile();
    };

    sidebar.addEventListener('mouseenter', () => {
      if (isDesktop()) scheduleExpanded(true, EXPAND_DELAY);
    }, {passive:true});
    sidebar.addEventListener('mouseleave', () => {
      if (isDesktop()) scheduleExpanded(false, COLLAPSE_DELAY);
    }, {passive:true});
    sidebar.addEventListener('focusin', () => {
      if (isDesktop()) scheduleExpanded(true, 0);
    });
    sidebar.addEventListener('focusout', (event) => {
      if (isDesktop() && !sidebar.contains(event.relatedTarget)) scheduleExpanded(false, COLLAPSE_DELAY);
    });
    toggleButton?.addEventListener('click', toggleMobile);
    overlay?.addEventListener('click', closeMobile);
    toggleButton?.setAttribute('aria-expanded', 'false');
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        if (!isDesktop()) closeMobile();
        else scheduleExpanded(false, 0);
      }
    });
    const syncViewportMode = () => {
      const nextDesktopMode = isDesktop();
      if (nextDesktopMode === desktopMode) return;
      desktopMode = nextDesktopMode;
      window.clearTimeout(expandTimer);
      window.clearTimeout(collapseTimer);
      if (nextDesktopMode) {
        closeMobile(true);
        setExpanded(false);
      } else {
        sidebar.classList.remove('is-expanded');
        shell?.classList.remove('sidebar-expanded');
        if (!sidebar.classList.contains('-translate-x-full')) openMobile();
      }
    };

    const mediaQuery = window.matchMedia(`(min-width: ${BREAKPOINT}px)`);
    mediaQuery.addEventListener?.('change', syncViewportMode);
    window.addEventListener('resize', syncViewportMode, {passive:true});
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar, {once:true});
  } else {
    initSidebar();
  }
})();

(function (window, document) {
  'use strict';

  const UI = {
    _openModals: new Set(),

    openModal(id) {
      const modal = document.getElementById(id);
      if (!modal) return;
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      this._openModals.add(id);
      document.documentElement.style.overflow = 'hidden';
      document.body.style.overflow = 'hidden';
    },

    closeModal(id) {
      const modal = document.getElementById(id);
      if (!modal) return;
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      this._openModals.delete(id);
      if (this._openModals.size === 0) {
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
      }
    },

    closeTopModal() {
      const ids = Array.from(this._openModals);
      if (!ids.length) return;
      this.closeModal(ids[ids.length - 1]);
    },

    toggleFilter(panelId, button) {
      const panel = document.getElementById(panelId);
      if (!panel) return;
      const open = panel.classList.toggle('is-open');
      if (button) button.setAttribute('aria-expanded', open ? 'true' : 'false');
    },

    closeMobileFilter(panelId) {
      if (window.innerWidth >= 768) return;
      const panel = document.getElementById(panelId);
      if (panel) panel.classList.remove('is-open');
      const button = document.querySelector('[data-mb-filter-target="' + CSS.escape(panelId) + '"]');
      if (button) button.setAttribute('aria-expanded', 'false');
    },

    showLoading(id, show = true) {
      const el = document.getElementById(id);
      if (!el) return;
      el.classList.toggle('is-hidden', !show);
    },

    debounce(fn, delay = 350) {
      let timer = null;
      return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
      };
    },

    async postJson(url, payload, options = {}) {
      const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
        body: JSON.stringify(payload || {}),
        signal: options.signal
      });
      const text = await response.text();
      let json = {};
      try { json = text ? JSON.parse(text) : {}; } catch (error) {
        throw new Error('Response bukan JSON: ' + text.slice(0, 120));
      }
      if (!response.ok) throw new Error(json.message || ('HTTP ' + response.status));
      return json;
    },

    fmt(value) {
      const num = Number(value || 0);
      return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(num);
    },

    fmt2(value) {
      const num = Number(value || 0);
      return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
    },

    signed(value, formatter) {
      const num = Number(value || 0);
      const fmt = formatter || this.fmt.bind(this);
      if (num > 0) return '+' + fmt(num);
      if (num < 0) return '-' + fmt(Math.abs(num));
      return fmt(0);
    },

    renderSummary(id, cards) {
      const el = document.getElementById(id);
      if (!el) return;
      el.classList.remove('is-hidden');
      el.innerHTML = (cards || []).map(card => {
        const tone = card.tone || 'default';
        const meta = card.meta ? '<div class="mb-summary-card__meta">' + this.escape(card.meta) + '</div>' : '';
        return '<div class="mb-summary-card mb-summary-card--' + this.escape(tone) + '">' +
          '<div class="mb-summary-card__label">' + this.escape(card.label || '') + '</div>' +
          '<div class="mb-summary-card__value">' + this.escape(card.value ?? '-') + '</div>' + meta + '</div>';
      }).join('');
    },

    escape(value) {
      return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }
  };

  document.addEventListener('click', function (event) {
    const open = event.target.closest('[data-mb-open-modal]');
    if (open) {
      event.preventDefault();
      UI.openModal(open.getAttribute('data-mb-open-modal'));
      return;
    }

    const close = event.target.closest('[data-mb-close-modal]');
    if (close) {
      event.preventDefault();
      UI.closeModal(close.getAttribute('data-mb-close-modal'));
      return;
    }

    const toggle = event.target.closest('[data-mb-filter-target]');
    if (toggle) {
      event.preventDefault();
      UI.toggleFilter(toggle.getAttribute('data-mb-filter-target'), toggle);
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') UI.closeTopModal();
  });

  window.MonbisUI = UI;
})(window, document);

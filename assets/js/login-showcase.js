(function () {
  'use strict';

  function initLoginShowcase() {
    const carousel = document.querySelector('[data-login-carousel]');
    if (!carousel) return;

    const slides = Array.from(carousel.querySelectorAll('[data-login-slide]'));
    const dots = Array.from(document.querySelectorAll('[data-login-dot]'));
    const previous = document.querySelector('[data-login-prev]');
    const next = document.querySelector('[data-login-next]');
    if (!slides.length) return;

    let active = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
    let timer = null;

    function paint(index) {
      active = (index + slides.length) % slides.length;
      slides.forEach((slide, position) => slide.classList.toggle('is-active', position === active));
      dots.forEach((dot, position) => {
        const selected = position === active;
        dot.classList.toggle('is-active', selected);
        dot.setAttribute('aria-selected', selected ? 'true' : 'false');
      });
    }

    function restartTimer() {
      window.clearInterval(timer);
      timer = window.setInterval(() => paint(active + 1), 7000);
    }

    previous?.addEventListener('click', () => { paint(active - 1); restartTimer(); });
    next?.addEventListener('click', () => { paint(active + 1); restartTimer(); });
    dots.forEach((dot) => dot.addEventListener('click', () => {
      paint(Number(dot.dataset.loginDot || 0));
      restartTimer();
    }));
    carousel.addEventListener('mouseenter', () => window.clearInterval(timer));
    carousel.addEventListener('mouseleave', restartTimer);
    carousel.addEventListener('focusin', () => window.clearInterval(timer));
    carousel.addEventListener('focusout', (event) => {
      if (!carousel.contains(event.relatedTarget)) restartTimer();
    });
    paint(active);
    restartTimer();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLoginShowcase, { once: true });
  } else {
    initLoginShowcase();
  }
}());

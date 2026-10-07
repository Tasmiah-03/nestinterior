/**
 * Luxe Interior — Main JavaScript
 */
(function() {
  'use strict';

  // ============================================================
  // HEADER SCROLL BEHAVIOR
  // ============================================================
  const header = document.getElementById('site-header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 60);
    }, { passive: true });
  }

  // ============================================================
  // SMOOTH REVEAL ANIMATIONS
  // ============================================================
  const reveals = document.querySelectorAll('[data-reveal]');
  if (reveals.length && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });
    reveals.forEach(el => observer.observe(el));
  }

  // ============================================================
  // PORTFOLIO FILTER
  // ============================================================
  const filterBtns = document.querySelectorAll('.filter-btn');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      filterBtns.forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      const category = this.dataset.category;
      const grid = document.querySelector('.portfolio-grid');
      if (!grid) return;

      const cards = grid.querySelectorAll('.portfolio-card');
      cards.forEach(card => {
        const match = category === 'all' || card.dataset.category === category;
        card.style.opacity = match ? '1' : '0.2';
        card.style.pointerEvents = match ? '' : 'none';
        card.style.transform = match ? 'scale(1)' : 'scale(0.97)';
        card.style.transition = 'all 0.4s ease';
      });
    });
  });

  // ============================================================
  // LIGHTBOX (simple)
  // ============================================================
  const portfolioCards = document.querySelectorAll('.portfolio-card[data-lightbox]');
  portfolioCards.forEach(card => {
    card.addEventListener('click', function() {
      const imgSrc = this.querySelector('img')?.src;
      const title = this.querySelector('h3')?.textContent;
      if (!imgSrc) return;

      const overlay = document.createElement('div');
      overlay.className = 'lightbox-overlay';
      overlay.innerHTML = `
        <div class="lightbox-inner">
          <button class="lightbox-close" aria-label="Close">✕</button>
          <img src="${imgSrc}" alt="${title || ''}">
          ${title ? `<p class="lightbox-caption">${title}</p>` : ''}
        </div>`;

      document.body.appendChild(overlay);
      document.body.style.overflow = 'hidden';
      requestAnimationFrame(() => overlay.classList.add('visible'));

      const close = () => {
        overlay.classList.remove('visible');
        setTimeout(() => { overlay.remove(); document.body.style.overflow = ''; }, 300);
      };
      overlay.querySelector('.lightbox-close').addEventListener('click', close);
      overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
      document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); }, { once: true });
    });
  });

  // ============================================================
  // NEWSLETTER FORM
  // ============================================================
  const newsletterForm = document.querySelector('.newsletter-form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const email = this.querySelector('input[type="email"]')?.value;
      if (email) {
        const btn = this.querySelector('button');
        btn.textContent = 'Subscribed ✓';
        btn.disabled = true;
        btn.style.background = '#5a8a5a';
      }
    });
  }

  // ============================================================
  // CURSOR EFFECT (desktop only)
  // ============================================================
  if (window.matchMedia('(pointer: fine)').matches) {
    const cursor = document.createElement('div');
    cursor.className = 'luxe-cursor';
    document.body.appendChild(cursor);

    let mouseX = 0, mouseY = 0;
    document.addEventListener('mousemove', e => {
      mouseX = e.clientX;
      mouseY = e.clientY;
      cursor.style.transform = `translate(${mouseX - 8}px, ${mouseY - 8}px)`;
    });

    document.querySelectorAll('a, button, .portfolio-card, .filter-btn').forEach(el => {
      el.addEventListener('mouseenter', () => cursor.classList.add('cursor-hover'));
      el.addEventListener('mouseleave', () => cursor.classList.remove('cursor-hover'));
    });
  }

  // ============================================================
  // MOBILE MENU
  // ============================================================
  const menuToggle = document.querySelector('.menu-toggle');
  const mobileMenu = document.querySelector('.mobile-menu');
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', function() {
      const isOpen = mobileMenu.classList.toggle('open');
      this.setAttribute('aria-expanded', isOpen);
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });
  }

  // ============================================================
  // STATS COUNTER ANIMATION
  // ============================================================
  const statNums = document.querySelectorAll('.stat-num[data-count]');
  if (statNums.length && 'IntersectionObserver' in window) {
    const countObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const target = parseInt(el.dataset.count);
        const suffix = el.dataset.suffix || '';
        let current = 0;
        const step = Math.ceil(target / 60);
        const timer = setInterval(() => {
          current = Math.min(current + step, target);
          el.textContent = current + suffix;
          if (current >= target) clearInterval(timer);
        }, 20);
        countObserver.unobserve(el);
      });
    }, { threshold: 0.5 });
    statNums.forEach(el => countObserver.observe(el));
  }

  // ============================================================
  // HERO CAROUSEL
  // ============================================================
  const heroSlider = document.getElementById('hero-slider');
  if (heroSlider) {
    const images = heroSlider.querySelectorAll('.hero-image');
    if (images.length > 1) {
      let currentIndex = 0;
      setInterval(() => {
        images[currentIndex].classList.remove('active');
        currentIndex = (currentIndex + 1) % images.length;
        images[currentIndex].classList.add('active');
      }, 5000);
    }
  }

})();

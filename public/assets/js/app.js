(() => {
    const toggle = document.querySelector('.nav-toggle');
    const closeBtn = document.querySelector('.nav-close');
    const nav = document.querySelector('#site-nav');
    const backdrop = document.querySelector('.nav-backdrop');
    const setNav = (open) => {
        if (!nav) return;
        nav.classList.toggle('is-open', open);
        document.body.classList.toggle('nav-open', open);
        if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (backdrop) backdrop.hidden = !open;
    };
    if (toggle) {
        toggle.addEventListener('click', () => setNav(!nav?.classList.contains('is-open')));
    }
    if (closeBtn) closeBtn.addEventListener('click', () => setNav(false));
    if (backdrop) backdrop.addEventListener('click', () => setNav(false));

    const slides = [...document.querySelectorAll('.hero-slide')];
    const hero = document.querySelector('.hero-banner');
    let heroIndex = 0;
    let heroTimer = 0;
    const heroDelay = 5000;
    const showHero = (next) => {
        if (!slides.length) return;
        heroIndex = (next + slides.length) % slides.length;
        slides.forEach((slide, index) => slide.classList.toggle('is-on', index === heroIndex));
    };
    const stopHero = () => {
        window.clearInterval(heroTimer);
        heroTimer = 0;
    };
    const startHero = () => {
        if (slides.length < 2) return;
        stopHero();
        heroTimer = window.setInterval(() => showHero(heroIndex + 1), heroDelay);
    };
    document.querySelectorAll('[data-hero]').forEach((button) => {
        button.addEventListener('click', () => {
            showHero(heroIndex + Number(button.dataset.hero));
            startHero();
        });
    });
    if (hero && slides.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        startHero();
        hero.addEventListener('mouseenter', stopHero);
        hero.addEventListener('mouseleave', startHero);
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) stopHero();
            else startHero();
        });
    }

    const searchModal = document.querySelector('#search-modal');
    const searchOpen = document.querySelector('.search-open');
    const searchClose = document.querySelector('.search-close');
    const openSearch = () => {
        if (!searchModal) return;
        searchModal.hidden = false;
        if (searchOpen) searchOpen.setAttribute('aria-expanded', 'true');
        searchModal.querySelector('input')?.focus();
    };
    const closeSearch = () => {
        if (!searchModal) return;
        searchModal.hidden = true;
        if (searchOpen) searchOpen.setAttribute('aria-expanded', 'false');
    };
    if (searchOpen) searchOpen.addEventListener('click', openSearch);
    if (searchClose) searchClose.addEventListener('click', closeSearch);
    if (searchModal) {
        searchModal.addEventListener('click', (event) => {
            if (event.target === searchModal) closeSearch();
        });
    }
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeSearch();
            setNav(false);
        }
    });

    document.querySelectorAll('.wish-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const on = button.classList.toggle('is-on');
            button.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
    });

    if (new URLSearchParams(window.location.search).get('sale') === '1') {
        let shown = 0;
        document.querySelectorAll('.product-card[data-sale]').forEach((card) => {
            const match = card.dataset.sale === '1';
            card.hidden = !match;
            if (match) {
                shown += 1;
                card.classList.add('is-in');
            }
        });
        const empty = document.querySelector('.filter-empty');
        if (empty) empty.hidden = shown !== 0;
    }

    const main = document.querySelector('#gallery-main');
    document.querySelectorAll('.thumb').forEach((button) => {
        button.addEventListener('click', () => {
            if (main && button.dataset.src) {
                main.src = button.dataset.src;
            }
        });
    });

    document.querySelectorAll('[data-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            const filter = button.dataset.filter;
            document.querySelectorAll('[data-filter]').forEach((pill) => {
                pill.classList.toggle('is-active', pill === button);
            });
            let shown = 0;
            document.querySelectorAll('.product-card[data-category]').forEach((card) => {
                const match = filter === 'all' || card.dataset.category === filter;
                card.hidden = !match;
                if (match) {
                    shown += 1;
                    card.classList.add('is-in');
                }
            });
            const empty = document.querySelector('.filter-empty');
            if (empty) empty.hidden = shown !== 0;
        });
    });

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduceMotion && document.body.classList.contains('store-body')) {
        const motionSelect = [
            '.shop-cats-head',
            '.cat-tile',
            '.promo-card',
            '.section-head',
            '.filter-pills',
            '.product-card',
            '.mid-banner',
            '.blog-card',
            '.newsletter-card',
            '.footer-grid > *',
            '.page-head',
            '.list-banner',
            '.catalog-side',
            '.contact-form-card',
            '.contact-card',
            '.product-layout > *',
            '.cart-layout > *',
            '.checkout-grid > *',
        ].join(',');
        const motionNodes = [...document.querySelectorAll(motionSelect)];
        const delays = new Map();
        const inFirstView = (el) => el.getBoundingClientRect().top < window.innerHeight * 0.88;
        motionNodes.forEach((el) => {
            const parent = el.parentElement;
            const index = delays.get(parent) || 0;
            el.style.setProperty('--reveal-delay', `${Math.min(index, 7) * 70}ms`);
            delays.set(parent, index + 1);
            if (!inFirstView(el)) el.classList.add('reveal');
        });
        document.documentElement.classList.add('js-motion');
        const motionWatch = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-in');
                motionWatch.unobserve(entry.target);
            });
        }, { threshold: 0.14, rootMargin: '0px 0px -10% 0px' });
        motionNodes.forEach((el) => {
            if (el.classList.contains('reveal')) motionWatch.observe(el);
        });
        const parallaxRoots = [...document.querySelectorAll('.hero-banner, .mid-banner, .page-banner, .list-banner-media')];
        let parallaxFrame = 0;
        const moveParallax = () => {
            parallaxFrame = 0;
            const view = window.innerHeight;
            parallaxRoots.forEach((root) => {
                const box = root.getBoundingClientRect();
                if (box.bottom < 0 || box.top > view) return;
                const shift = ((view - box.top) / (view + box.height) - 0.5) * 54;
                root.querySelectorAll('img').forEach((img) => {
                    img.style.transform = `translate3d(0, ${shift}px, 0) scale(1.08)`;
                });
            });
        };
        const requestParallax = () => {
            if (parallaxFrame) return;
            parallaxFrame = window.requestAnimationFrame(moveParallax);
        };
        window.addEventListener('scroll', requestParallax, { passive: true });
        moveParallax();
    }

    document.querySelectorAll('[data-repeat]').forEach((button) => {
        button.addEventListener('click', () => {
            const wrap = document.getElementById(button.dataset.repeat);
            if (!wrap) return;
            const last = wrap.querySelector('.repeat-row:last-child');
            if (!last) return;
            const clone = last.cloneNode(true);
            const index = wrap.querySelectorAll('.repeat-row').length;
            clone.querySelectorAll('input').forEach((input) => {
                input.value = '';
                input.name = input.name.replace(/\[\d+\]/, '[' + index + ']');
            });
            wrap.appendChild(clone);
        });
    });
})();

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
    let heroIndex = 0;
    const showHero = (next) => {
        if (!slides.length) return;
        heroIndex = (next + slides.length) % slides.length;
        slides.forEach((slide, index) => slide.classList.toggle('is-on', index === heroIndex));
    };
    document.querySelectorAll('[data-hero]').forEach((button) => {
        button.addEventListener('click', () => showHero(heroIndex + Number(button.dataset.hero)));
    });

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
            if (match) shown += 1;
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
                if (match) shown += 1;
            });
            const empty = document.querySelector('.filter-empty');
            if (empty) empty.hidden = shown !== 0;
        });
    });

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

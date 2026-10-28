(() => {
    const toggle = document.querySelector('.nav-toggle');
    const drop = document.querySelector('.nav-drop');
    if (toggle && drop) {
        toggle.addEventListener('click', () => {
            const open = drop.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            const button = drop.querySelector('button');
            if (button) button.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }
    document.querySelectorAll('.nav-drop > button').forEach((button) => {
        button.addEventListener('click', () => {
            const parent = button.parentElement;
            const open = parent.classList.toggle('is-open');
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });

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

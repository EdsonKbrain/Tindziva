import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

document.documentElement.classList.remove('no-js');

Alpine.plugin(focus);

Alpine.data('siteHeader', () => ({
    open: false,
    scrolled: false,
    init() {
        const update = () => (this.scrolled = window.scrollY > 12);
        update();
        window.addEventListener('scroll', update, { passive: true });
        this.$watch('open', (value) => document.body.classList.toggle('overflow-hidden', value));
    },
}));

Alpine.data('lightbox', (items = []) => ({
    items,
    index: null,
    get current() {
        return this.index === null ? null : this.items[this.index];
    },
    show(index) {
        this.index = index;
    },
    close() {
        this.index = null;
    },
    next() {
        this.index = (this.index + 1) % this.items.length;
    },
    prev() {
        this.index = (this.index - 1 + this.items.length) % this.items.length;
    },
}));

Alpine.data('copyLink', (url) => ({
    copied: false,
    async copy() {
        try {
            await navigator.clipboard.writeText(url);
            this.copied = true;
            setTimeout(() => (this.copied = false), 2000);
        } catch {
            window.prompt('Copie a ligação:', url);
        }
    },
}));

window.Alpine = Alpine;
Alpine.start();

const revealObserver = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 })
    : null;

document.querySelectorAll('.reveal').forEach((element) => {
    revealObserver ? revealObserver.observe(element) : element.classList.add('is-visible');
});

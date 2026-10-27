import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

const slugify = (value) => value
    .toString()
    .normalize('NFD')
    .replace(/[̀-ͯ]/g, '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '');

const toEmbedUrl = (url) => {
    const youtube = url.match(/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/);
    if (youtube) {
        return `https://www.youtube-nocookie.com/embed/${youtube[1]}`;
    }

    const vimeo = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
    return vimeo ? `https://player.vimeo.com/video/${vimeo[1]}` : null;
};

Alpine.plugin(focus);

/**
 * Media library picker shared by every media field, gallery and the rich text editor.
 */
Alpine.store('picker', {
    isOpen: false,
    multiple: false,
    type: '',
    items: [],
    selected: [],
    query: '',
    loading: false,
    uploading: false,
    error: '',
    nextUrl: null,
    resolver: null,

    open({ multiple = false, type = 'image' } = {}) {
        this.multiple = multiple;
        this.type = type;
        this.selected = [];
        this.error = '';
        this.isOpen = true;
        this.load(true);

        return new Promise((resolve) => (this.resolver = resolve));
    },

    close(result = null) {
        this.isOpen = false;
        this.resolver?.(result);
        this.resolver = null;
    },

    async load(reset = false) {
        this.loading = true;
        const url = reset || !this.nextUrl
            ? `${window.TindzivaAdmin.mediaUrl}?${new URLSearchParams({ q: this.query, type: this.type })}`
            : this.nextUrl;

        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            const payload = await response.json();
            this.items = reset ? payload.data : [...this.items, ...payload.data];
            this.nextUrl = payload.next_page_url;
        } catch {
            this.error = 'Não foi possível carregar a biblioteca.';
        } finally {
            this.loading = false;
        }
    },

    async upload(fileList) {
        if (!fileList?.length) {
            return;
        }

        const body = new FormData();
        [...fileList].forEach((file) => body.append('files[]', file));

        this.uploading = true;
        this.error = '';

        try {
            const response = await fetch(window.TindzivaAdmin.mediaUrl, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                body,
            });
            const payload = await response.json();

            if (!response.ok) {
                this.error = Object.values(payload.errors ?? {}).flat()[0] ?? payload.message ?? 'Falha no carregamento.';
                return;
            }

            this.items = [...payload.data, ...this.items];
            payload.data.forEach((item) => this.toggle(item));
        } catch {
            this.error = 'Falha no carregamento. Verifique a ligação e tente novamente.';
        } finally {
            this.uploading = false;
        }
    },

    isSelected(item) {
        return this.selected.some((selected) => selected.id === item.id);
    },

    toggle(item) {
        if (!this.multiple) {
            this.selected = [item];
            return;
        }

        this.selected = this.isSelected(item)
            ? this.selected.filter((selected) => selected.id !== item.id)
            : [...this.selected, item];
    },

    confirm() {
        this.close(this.multiple ? this.selected : (this.selected[0] ?? null));
    },
});

Alpine.data('mediaField', (initial = null, type = 'image') => ({
    media: initial,
    async pick() {
        const media = await Alpine.store('picker').open({ type });
        if (media) {
            this.media = media;
        }
    },
    clear() {
        this.media = null;
    },
}));

Alpine.data('galleryField', (items = [], type = 'image') => ({
    items,
    dragging: null,
    async add() {
        const selection = await Alpine.store('picker').open({ multiple: true, type });
        (selection ?? []).forEach((media) => {
            if (!this.items.some((item) => item.id === media.id)) {
                this.items.push(media);
            }
        });
    },
    remove(index) {
        this.items.splice(index, 1);
    },
    start(index) {
        this.dragging = index;
    },
    over(index) {
        if (this.dragging === null || this.dragging === index) {
            return;
        }
        const [moved] = this.items.splice(this.dragging, 1);
        this.items.splice(index, 0, moved);
        this.dragging = index;
    },
}));

Alpine.data('repeater', (rows = [], blank = {}, previews = {}) => ({
    rows: rows.map((row) => ({ ...blank, ...row })),
    previews,
    add() {
        this.rows.push({ ...blank });
    },
    remove(index) {
        this.rows.splice(index, 1);
    },
    move(index, direction) {
        const target = index + direction;
        if (target < 0 || target >= this.rows.length) {
            return;
        }
        const [row] = this.rows.splice(index, 1);
        this.rows.splice(target, 0, row);
    },
    async pickImage(row, field) {
        const media = await Alpine.store('picker').open({ type: 'image' });
        if (media) {
            this.previews[media.id] = media;
            row[field] = media.id;
        }
    },
}));

Alpine.data('sortableList', () => ({
    dragging: null,
    start(event) {
        this.dragging = event.target.closest('[data-sortable-item]');
        event.dataTransfer.effectAllowed = 'move';
    },
    over(event) {
        const target = event.target.closest('[data-sortable-item]');
        if (!this.dragging || !target || target === this.dragging) {
            return;
        }
        const rect = target.getBoundingClientRect();
        const after = event.clientY > rect.top + rect.height / 2;
        target.parentNode.insertBefore(this.dragging, after ? target.nextSibling : target);
    },
    end() {
        this.dragging = null;
    },
}));

Alpine.data('slugField', (initialTitle = '', initialSlug = '') => ({
    title: initialTitle,
    slug: initialSlug,
    touched: initialSlug !== '',
    init() {
        this.$watch('title', (value) => {
            if (!this.touched) {
                this.slug = slugify(value);
            }
        });
    },
}));

Alpine.data('charCounter', (initial = '', max = 160) => ({
    value: initial ?? '',
    max,
    get remaining() {
        return this.max - this.value.length;
    },
}));

Alpine.data('richEditor', () => ({
    quill: null,
    init() {
        const input = this.$refs.input;

        this.quill = new Quill(this.$refs.editor, {
            theme: 'snow',
            placeholder: this.$el.dataset.placeholder || 'Escreva aqui…',
            modules: {
                toolbar: {
                    container: [
                        [{ header: [2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'link'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', { align: [] }],
                        ['image', 'video'],
                        ['clean'],
                    ],
                    handlers: {
                        image: () => this.insertImage(),
                        video: () => this.insertVideo(),
                    },
                },
            },
        });

        if (input.value) {
            this.quill.clipboard.dangerouslyPasteHTML(input.value, 'silent');
        }

        const sync = () => {
            const html = this.quill.getSemanticHTML().replace(/&nbsp;/g, ' ');
            input.value = this.quill.getText().trim() === '' && !html.includes('<img') && !html.includes('<iframe') ? '' : html;
        };

        this.quill.on('text-change', sync);
        this.$el.closest('form')?.addEventListener('submit', sync);
    },
    async insertImage() {
        const media = await Alpine.store('picker').open({ type: 'image' });
        if (!media) {
            return;
        }
        const range = this.quill.getSelection(true);
        this.quill.insertEmbed(range.index, 'image', media.url, 'user');
        this.quill.setSelection(range.index + 1);
    },
    insertVideo() {
        const url = window.prompt('Cole a ligação do vídeo (YouTube ou Vimeo):');
        const embed = url ? toEmbedUrl(url) : null;
        if (url && !embed) {
            window.alert('Apenas são suportadas ligações do YouTube ou Vimeo.');
            return;
        }
        if (embed) {
            const range = this.quill.getSelection(true);
            this.quill.insertEmbed(range.index, 'video', embed, 'user');
        }
    },
}));

Alpine.data('toasts', (initial = []) => ({
    items: initial.map((item, index) => ({ ...item, id: index })),
    init() {
        this.items.forEach((item) => setTimeout(() => this.dismiss(item.id), 6000));
    },
    dismiss(id) {
        this.items = this.items.filter((item) => item.id !== id);
    },
}));

window.Alpine = Alpine;
Alpine.start();

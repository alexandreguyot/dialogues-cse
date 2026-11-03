{{-- Hors du composant Livewire : ces données ne doivent pas repartir à chaque échange. --}}
@push('head')
    <script>
        window.pageMedia = {
            icon: @json(\App\Support\PageIcons::library()),
            image: @json(\App\Support\PageBlocks::imageLibrary()),
        };

        document.addEventListener('alpine:init', () => {
            const normalize = (text) => text.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();

            Alpine.data('mediaPicker', () => ({
                open: false,
                mode: 'icon',
                path: null,
                query: '',
                theme: '',
                opener: null,
                get items() {
                    return window.pageMedia[this.mode];
                },
                get themes() {
                    return [...new Set(this.items.map((item) => item.theme))];
                },
                get filtered() {
                    const query = normalize(this.query);

                    return this.items.filter((item) => (! this.theme || item.theme === this.theme) && normalize(item.label).includes(query));
                },
                show({ mode, path }) {
                    this.opener = document.activeElement;
                    this.mode = mode;
                    this.path = path;
                    this.query = '';
                    this.theme = '';
                    this.open = true;
                    this.$nextTick(() => this.$refs.search.focus());
                },
                close() {
                    this.open = false;
                    this.opener?.focus();
                },
                choose(item) {
                    this.$wire.set(this.path, item.value);
                    this.close();
                },
            }));
        });
    </script>
@endpush

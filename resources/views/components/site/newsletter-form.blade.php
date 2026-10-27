<form method="POST" action="{{ route('newsletter.store') }}" {{ $attributes }}>
    @csrf
    <x-site.honeypot />
    @if (session('newsletter_success'))
        <p role="status" class="mb-3 flex items-center gap-2 text-sm text-ochre-200"><x-lucide-circle-check class="size-4" /> {{ session('newsletter_success') }}</p>
    @endif
    <label for="newsletter-email" class="sr-only">E-mail</label>
    <div class="flex rounded-full bg-white/10 p-1 ring-1 ring-white/15 focus-within:ring-ochre-400">
        <input id="newsletter-email" type="email" name="email" required placeholder="{{ site_text('newsletter_placeholder') }}" autocomplete="email" value="{{ $errors->newsletter->any() ? old('email') : '' }}"
            class="min-w-0 flex-1 border-0 bg-transparent px-4 text-sm text-white placeholder:text-white/50 focus:ring-0">
        <button type="submit" class="grid size-10 shrink-0 place-items-center rounded-full bg-ochre-600 text-white transition hover:bg-ochre-500" aria-label="Subscrever">
            <x-lucide-arrow-right class="size-4" />
        </button>
    </div>
    @error('email', 'newsletter')
        <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
    @enderror
</form>

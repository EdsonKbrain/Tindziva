@props(['services' => collect(), 'selected' => null, 'compact' => false])

@php($budgetOptions = \App\Support\SiteTexts::lines('form_budget_options'))

<form method="POST" action="{{ route('quote.store') }}" {{ $attributes->class('relative') }} novalidate>
    @csrf
    <x-site.honeypot />

    @if (session('quote_success'))
        <x-site.alert class="mb-6">{{ session('quote_success') }}</x-site.alert>
    @elseif ($errors->quote->any())
        <x-site.alert type="error" class="mb-6">{{ site_text('form_error') }}</x-site.alert>
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <x-site.field bag="quote" name="name" :label="site_text('form_name')" required autocomplete="name" />
        <x-site.field bag="quote" name="company" :label="site_text('form_company')" autocomplete="organization" />
        <x-site.field bag="quote" name="email" type="email" :label="site_text('form_email')" required autocomplete="email" />
        <x-site.field bag="quote" name="phone" type="tel" :label="site_text('form_phone')" required autocomplete="tel" :placeholder="site_text('form_phone_placeholder')" />

        @if ($selected)
            <input type="hidden" name="service_id" value="{{ $selected->id }}">
        @else
            <x-site.field bag="quote" name="service_id" type="select" :label="site_text('form_service')" :options="$services->pluck('name', 'id')" :placeholder="site_text('form_service_placeholder')" />
        @endif

        @if ($budgetOptions)
            <x-site.field bag="quote" name="budget" type="select" :label="site_text('form_budget')" :placeholder="site_text('form_budget_placeholder')" :options="array_combine($budgetOptions, $budgetOptions)" />
        @endif

        @unless ($compact)
            <x-site.field bag="quote" name="deadline" type="date" :label="site_text('form_deadline')" :min="now()->toDateString()" />
        @endunless

        <x-site.field bag="quote" class="sm:col-span-2" name="message" type="textarea" :label="site_text('form_project')" required :placeholder="site_text('form_project_placeholder')" />
    </div>

    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-ink/60">{{ site_text('form_privacy') }}</p>
        <x-site.button icon="send">{{ site_text('form_quote_submit') }}</x-site.button>
    </div>
</form>

<form method="POST" action="{{ route('contact.store') }}" {{ $attributes->class('relative') }} novalidate>
    @csrf
    <x-site.honeypot />

    @if (session('contact_success'))
        <x-site.alert class="mb-6">{{ session('contact_success') }}</x-site.alert>
    @elseif ($errors->contact->any())
        <x-site.alert type="error" class="mb-6">{{ site_text('form_error') }}</x-site.alert>
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <x-site.field bag="contact" name="name" :label="site_text('form_name')" required autocomplete="name" />
        <x-site.field bag="contact" name="company" :label="site_text('form_company')" autocomplete="organization" />
        <x-site.field bag="contact" name="email" type="email" :label="site_text('form_email')" required autocomplete="email" />
        <x-site.field bag="contact" name="phone" type="tel" :label="site_text('form_phone')" autocomplete="tel" :placeholder="site_text('form_phone_placeholder')" />
        <x-site.field bag="contact" class="sm:col-span-2" name="subject" :label="site_text('form_subject')" />
        <x-site.field bag="contact" class="sm:col-span-2" name="message" type="textarea" :label="site_text('form_message')" required />
    </div>

    <div class="mt-6">
        <x-site.button icon="send">{{ site_text('form_contact_submit') }}</x-site.button>
    </div>
</form>

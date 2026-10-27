<?php

namespace App\Http\Controllers\Site;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ContactRequest;
use App\Mail\NewLeadReceived;
use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Service;
use App\Services\PublicCache;
use App\Support\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        $page = PublicCache::remember('page.contact', fn () => Page::forTemplate('contact')?->load(['heroImage', 'ogImage']));

        $seo = ($page ? SeoData::for($page, $page->title, $page->subtitle, $page->heroImage) : new SeoData(title: 'Contactos'))
            ->withBreadcrumbs([
                ['name' => site_text('breadcrumb_home'), 'url' => route('home')],
                ['name' => site_text('nav_contact'), 'url' => route('contact')],
            ]);

        return view('site.contact', [
            'page' => $page,
            'services' => Service::published()->ordered()->get(['id', 'name', 'slug']),
            'seo' => $seo,
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $message = ContactMessage::create([
            ...$request->safe()->except('website'),
            'status' => LeadStatus::New,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 490, ''),
        ]);

        self::notifyTeam(new NewLeadReceived($message));

        return redirect()->to(url()->previous().'#contacto')
            ->with('contact_success', site_text('form_contact_success'));
    }

    /**
     * Email the team without failing the submission when SMTP is unavailable.
     */
    public static function notifyTeam(NewLeadReceived $mail): void
    {
        $recipient = setting('notification_email') ?? setting('contact_email');

        if (blank($recipient)) {
            return;
        }

        try {
            Mail::to($recipient)->send($mail);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}

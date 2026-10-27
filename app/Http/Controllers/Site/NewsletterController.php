<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\NewsletterRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function store(NewsletterRequest $request): RedirectResponse
    {
        $subscriber = NewsletterSubscriber::firstOrNew(['email' => Str::lower($request->input('email'))]);
        $subscriber->fill([
            'name' => $request->input('name') ?? $subscriber->name,
            'status' => NewsletterSubscriber::STATUS_SUBSCRIBED,
            'unsubscribed_at' => null,
            'ip_address' => $request->ip(),
        ])->save();

        return redirect()->to(url()->previous().'#newsletter')
            ->with('newsletter_success', site_text('newsletter_success'));
    }

    public function unsubscribe(string $token): View
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->update(['status' => NewsletterSubscriber::STATUS_UNSUBSCRIBED, 'unsubscribed_at' => now()]);

        return view('site.newsletter-unsubscribed');
    }
}

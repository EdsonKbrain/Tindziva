<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request): View
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->filled('q'), fn ($query) => $query->where('email', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('admin.newsletter.index', [
            'subscribers' => $subscribers,
            'totalActive' => NewsletterSubscriber::subscribed()->count(),
        ]);
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->delete();

        return back()->with('success', 'Subscritor removido.');
    }

    public function export(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['E-mail', 'Nome', 'Data de subscrição'], escape: '');

            NewsletterSubscriber::subscribed()->orderBy('created_at')->each(function (NewsletterSubscriber $subscriber) use ($handle): void {
                fputcsv($handle, [$subscriber->email, ltrim((string) $subscriber->name, '=+-@'), $subscriber->created_at->format('Y-m-d H:i')], escape: '');
            });

            fclose($handle);
        }, 'newsletter-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}

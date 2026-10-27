<?php

namespace App\Http\Controllers\Site;

use App\Enums\LeadStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\QuoteRequestRequest;
use App\Mail\NewLeadReceived;
use App\Models\QuoteRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class QuoteRequestController extends Controller
{
    public function store(QuoteRequestRequest $request): RedirectResponse
    {
        $service = $request->filled('service_id') ? Service::find($request->integer('service_id')) : null;

        $quote = QuoteRequest::create([
            ...$request->safe()->except('website'),
            'service_name' => $service?->name,
            'status' => LeadStatus::New,
            'source_url' => Str::limit(url()->previous(), 490, ''),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 490, ''),
        ]);

        ContactController::notifyTeam(new NewLeadReceived($quote));

        return redirect()->to(url()->previous().'#proposta')
            ->with('quote_success', site_text('form_quote_success'));
    }
}

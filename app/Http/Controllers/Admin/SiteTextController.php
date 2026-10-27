<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteTextsRequest;
use App\Services\ActivityLogger;
use App\Services\SettingsRepository;
use App\Support\SiteTexts;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Edits the website's interface texts: menu, buttons, labels and form messages.
 */
class SiteTextController extends Controller
{
    public function __construct(private SettingsRepository $settings) {}

    public function edit(): View
    {
        $values = [];

        foreach (array_keys(SiteTexts::fields()) as $key) {
            $values[$key] = SiteTexts::raw($key);
        }

        return view('admin.texts.edit', ['groups' => SiteTexts::groups(), 'values' => $values]);
    }

    public function update(SiteTextsRequest $request, ActivityLogger $logger): RedirectResponse
    {
        $this->settings->set($request->payload(), 'texts');
        $logger->log('updated', 'Actualizou os textos e o menu do website');

        return redirect()->to(route('admin.texts.edit').($request->filled('tab') ? '#'.$request->input('tab') : ''))
            ->with('success', 'Textos guardados. As alterações já estão visíveis no website.');
    }
}

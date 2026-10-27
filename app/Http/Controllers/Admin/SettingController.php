<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Models\Media;
use App\Services\ActivityLogger;
use App\Services\SettingsRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private SettingsRepository $settings) {}

    public function edit(): View
    {
        $values = $this->settings->all();
        $mediaKeys = ['logo_id', 'logo_light_id', 'favicon_id', 'seo_og_image_id'];

        return view('admin.settings.edit', [
            'values' => $values,
            'media' => Media::whereIn('id', array_filter(array_map(fn ($key) => $values[$key] ?? null, $mediaKeys)))
                ->get()
                ->keyBy('id'),
        ]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        foreach (SettingsRequest::GROUPS as $group => $keys) {
            $this->settings->set(array_intersect_key($validated, array_flip($keys)), $group);
        }

        app(ActivityLogger::class)->log('updated', 'Actualizou as configurações do website');

        return back()->with('success', 'Configurações guardadas com sucesso.');
    }
}

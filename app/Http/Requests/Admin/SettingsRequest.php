<?php

namespace App\Http\Requests\Admin;

use App\Enums\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    /**
     * Settings keys grouped by section.
     *
     * @var array<string, list<string>>
     */
    public const GROUPS = [
        'general' => ['company_name', 'company_tagline', 'logo_id', 'logo_light_id', 'favicon_id'],
        'contact' => ['contact_email', 'contact_phone', 'contact_phone_secondary', 'contact_address', 'map_embed_url', 'notification_email'],
        'social' => ['social_facebook', 'social_instagram', 'social_linkedin', 'social_youtube', 'social_x', 'social_tiktok'],
        'whatsapp' => ['whatsapp_enabled', 'whatsapp_number', 'whatsapp_message'],
        'footer' => ['footer_text', 'footer_copyright'],
        'seo' => ['seo_title', 'seo_description', 'seo_og_image_id', 'seo_google_verification', 'analytics_id'],
        'newsletter' => ['newsletter_enabled', 'newsletter_title', 'newsletter_text'],
    ];

    public function authorize(): bool
    {
        return $this->user()->can(Permission::ManageSettings->value);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'whatsapp_enabled' => $this->boolean('whatsapp_enabled'),
            'newsletter_enabled' => $this->boolean('newsletter_enabled'),
            'whatsapp_number' => preg_replace('/\D+/', '', (string) $this->input('whatsapp_number')) ?: null,
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $media = ['nullable', 'integer', 'exists:media,id'];
        $social = ['nullable', 'url:https,http', 'max:255'];

        return [
            'company_name' => ['required', 'string', 'max:120'],
            'company_tagline' => ['nullable', 'string', 'max:160'],
            'logo_id' => $media,
            'logo_light_id' => $media,
            'favicon_id' => $media,
            'contact_email' => ['nullable', 'email', 'max:190'],
            'notification_email' => ['nullable', 'email', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'contact_phone_secondary' => ['nullable', 'string', 'max:40'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'map_embed_url' => ['nullable', 'url:https', 'max:2000', 'regex:~^https://(www\.)?google\.[a-z.]+/maps/embed~'],
            'social_facebook' => $social,
            'social_instagram' => $social,
            'social_linkedin' => $social,
            'social_youtube' => $social,
            'social_x' => $social,
            'social_tiktok' => $social,
            'whatsapp_enabled' => ['boolean'],
            'whatsapp_number' => ['nullable', 'required_if:whatsapp_enabled,true', 'digits_between:8,15'],
            'whatsapp_message' => ['nullable', 'string', 'max:300'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'footer_copyright' => ['nullable', 'string', 'max:200'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'seo_og_image_id' => $media,
            'seo_google_verification' => ['nullable', 'string', 'max:120', 'alpha_dash'],
            'analytics_id' => ['nullable', 'string', 'max:40', 'regex:/^G-[A-Z0-9]+$/'],
            'newsletter_enabled' => ['boolean'],
            'newsletter_title' => ['nullable', 'string', 'max:120'],
            'newsletter_text' => ['nullable', 'string', 'max:300'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'map_embed_url.regex' => 'Utilize o endereço de incorporação do Google Maps (https://www.google.com/maps/embed?...).',
            'analytics_id.regex' => 'O identificador do Google Analytics deve ter o formato G-XXXXXXX.',
        ];
    }
}

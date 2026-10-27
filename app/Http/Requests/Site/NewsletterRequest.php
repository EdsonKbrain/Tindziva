<?php

namespace App\Http\Requests\Site;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewsletterRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'newsletter';

    public function authorize(): bool
    {
        return (bool) setting('newsletter_enabled', true);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:190'],
            'name' => ['nullable', 'string', 'max:120'],
            'website' => ['prohibited'],
        ];
    }

    protected function getRedirectUrl(): string
    {
        return strtok(parent::getRedirectUrl(), '#').'#newsletter';
    }
}

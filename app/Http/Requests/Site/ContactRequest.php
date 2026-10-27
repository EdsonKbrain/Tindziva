<?php

namespace App\Http\Requests\Site;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'contact';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\s.-]{6,40}$/'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'company' => 'empresa',
            'phone' => 'telefone',
            'subject' => 'assunto',
            'message' => 'mensagem',
        ];
    }

    protected function getRedirectUrl(): string
    {
        return strtok(parent::getRedirectUrl(), '#').'#contacto';
    }
}

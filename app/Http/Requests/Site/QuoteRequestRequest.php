<?php

namespace App\Http\Requests\Site;

use App\Support\SiteTexts;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuoteRequestRequest extends FormRequest
{
    /**
     * @var string
     */
    protected $errorBag = 'quote';

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
            'service_id' => ['nullable', 'integer', Rule::exists('services', 'id')->where('is_published', true)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\s.-]{6,40}$/'],
            'budget' => ['nullable', 'string', 'max:64', Rule::in(SiteTexts::lines('form_budget_options'))],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
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
            'service_id' => 'serviço',
            'name' => 'nome',
            'company' => 'empresa',
            'phone' => 'telefone',
            'budget' => 'orçamento',
            'deadline' => 'prazo',
            'message' => 'mensagem',
        ];
    }

    protected function getRedirectUrl(): string
    {
        return strtok(parent::getRedirectUrl(), '#').'#proposta';
    }
}

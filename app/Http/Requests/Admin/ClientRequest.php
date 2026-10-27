<?php

namespace App\Http\Requests\Admin;

use App\Enums\ClientSegment;
use App\Enums\ClientType;
use App\Enums\Permission;
use App\Http\Requests\Concerns\HasSeoRules;
use App\Models\Client;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    use HasSeoRules;

    public function authorize(): bool
    {
        return $this->user()->can(Permission::ManageClients->value);
    }

    protected function prepareForValidation(): void
    {
        $this->booleans(['is_visible']);
        $this->merge([
            'services_provided' => collect(explode(',', (string) $this->input('services_provided')))
                ->map(fn (string $service): string => trim(strip_tags($service)))
                ->filter()
                ->values()
                ->all(),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Client|null $client */
        $client = $this->route('client');

        return [
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', 'alpha_dash', Rule::unique('clients', 'slug')->ignore($client?->id)],
            'type' => ['required', Rule::enum(ClientType::class)],
            'segment' => ['nullable', Rule::enum(ClientSegment::class)],
            'services_provided' => ['array', 'max:15'],
            'services_provided.*' => ['string', 'max:80'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo_id' => ['nullable', 'integer', 'exists:media,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_visible' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = $this->validated();
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}

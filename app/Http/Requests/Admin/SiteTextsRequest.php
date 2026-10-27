<?php

namespace App\Http\Requests\Admin;

use App\Enums\Permission;
use App\Support\SiteTexts;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SiteTextsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::ManagePages->value);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = ['texts' => ['array']];

        foreach (SiteTexts::fields() as $key => $field) {
            $rules["texts.{$key}"] = match ($field['type'] ?? 'text') {
                'toggle' => ['boolean'],
                'url' => ['nullable', 'string', 'max:500', 'regex:~^(https?://|/|#|mailto:|tel:)~i'],
                'textarea', 'lines' => ['nullable', 'string', 'max:2000'],
                default => ['nullable', 'string', 'max:300'],
            };
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'texts.*.regex' => 'A ligação deve começar por /, #, https://, mailto: ou tel:.',
        ];
    }

    /**
     * Values ready to store: plain text only, switches as 1/0, empty text reverting to the default.
     *
     * @return array<string, string|bool|null>
     */
    public function payload(): array
    {
        $values = [];

        foreach (SiteTexts::fields() as $key => $field) {
            $value = $this->input("texts.{$key}");
            $settingKey = SiteTexts::SETTING_PREFIX.$key;

            $values[$settingKey] = match ($field['type'] ?? 'text') {
                'toggle' => $this->boolean("texts.{$key}"),
                'lines' => trim(strip_tags((string) $value)),
                default => filled($value) ? trim(strip_tags((string) $value)) : null,
            };
        }

        return $values;
    }
}

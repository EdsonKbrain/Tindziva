<?php

namespace App\Http\Requests\Admin;

use App\Enums\LeadStatus;
use App\Enums\Permission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::ManageLeads->value);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(LeadStatus::class)],
            'internal_notes' => ['nullable', 'string', 'max:10000'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->where('is_active', true)],
        ];
    }
}

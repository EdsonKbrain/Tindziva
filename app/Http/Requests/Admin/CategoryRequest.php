<?php

namespace App\Http\Requests\Admin;

use App\Enums\Permission;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::ManageTaxonomies->value);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $model = CategoryController::config((string) $this->route('type'))['model'];

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', 'alpha_dash', Rule::unique((new $model)->getTable(), 'slug')->ignore($this->route('id'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $data = $this->validated();

        if ($this->route('type') === 'tags') {
            unset($data['description'], $data['sort_order']);
        } else {
            $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        }

        return $data;
    }
}

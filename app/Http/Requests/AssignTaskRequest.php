<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('assign', $this->route('task'));
    }

    public function rules(): array
    {
        return [
            'assigned_to' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('role', UserRole::Agent->value)
                    ->where('is_active', true),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'assigned_to' => 'agent',
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_to.exists' => "L'agent selectionne est introuvable ou inactif.",
        ];
    }
}

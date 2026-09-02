<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('task'));
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'min:3', 'max:180'],
            'description' => ['required', 'string', 'max:10000'],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'project_id' => ['nullable', 'integer', Rule::exists('projects', 'id')],
            'due_date' => ['nullable', 'date'],
        ];

        if ($this->user()->isAdmin()) {
            $rules['client_id'] = ['required', 'integer', Rule::exists('users', 'id')->where('role', UserRole::Client->value)];
            $rules['assigned_to'] = ['nullable', 'integer', Rule::exists('users', 'id')->where('role', UserRole::Agent->value)];
            $rules['mission_details'] = ['nullable', 'string', 'max:10000'];
            $rules['estimated_minutes'] = ['nullable', 'integer', 'min:5', 'max:100000'];
            $rules['spent_minutes'] = ['nullable', 'integer', 'min:0', 'max:100000'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'description' => 'description',
            'priority' => 'priorite',
            'project_id' => 'projet',
            'client_id' => 'client',
            'assigned_to' => 'agent',
            'mission_details' => 'missions a effectuer',
            'estimated_minutes' => 'estimation',
            'spent_minutes' => 'temps enregistre',
            'due_date' => 'date limite',
        ];
    }
}

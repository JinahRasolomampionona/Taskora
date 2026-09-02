<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\UserRole;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Task::class);
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'min:3', 'max:180'],
            'description' => ['required', 'string', 'max:10000'],
            'priority' => ['required', Rule::enum(TaskPriority::class)],
            'project_id' => ['nullable', 'integer', Rule::exists('projects', 'id')],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];

        // Seul l'admin renseigne le cadrage interne et distribue le travail.
        if ($this->user()->isAdmin()) {
            $rules['client_id'] = ['required', 'integer', Rule::exists('users', 'id')->where('role', UserRole::Client->value)];
            $rules['assigned_to'] = ['nullable', 'integer', Rule::exists('users', 'id')->where('role', UserRole::Agent->value)];
            $rules['mission_details'] = ['nullable', 'string', 'max:10000'];
            $rules['estimated_minutes'] = ['nullable', 'integer', 'min:5', 'max:100000'];
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
            'due_date' => 'date limite',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'title.min' => 'Le titre doit contenir au moins 3 caracteres.',
            'due_date.after_or_equal' => 'La date limite ne peut pas etre dans le passe.',
        ];
    }
}

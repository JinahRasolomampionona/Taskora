<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project !== null
            ? $this->user()->can('update', $project)
            : $this->user()->can('create', Project::class);
    }

    public function rules(): array
    {
        $project = $this->route('project');

        return [
            'code' => [
                'required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-]+$/',
                Rule::unique('projects', 'code')->ignore($project?->id),
            ],
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'owner_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
            'members' => ['nullable', 'array'],
            'members.*' => ['integer', Rule::exists('users', 'id')],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'code',
            'name' => 'nom du projet',
            'description' => 'description',
            'owner_id' => 'proprietaire',
            'color' => 'couleur',
            'members' => 'membres',
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => 'Le code ne peut contenir que des lettres, chiffres et tirets.',
            'color.regex' => 'La couleur doit etre un code hexadecimal (ex : #2563eb).',
        ];
    }
}

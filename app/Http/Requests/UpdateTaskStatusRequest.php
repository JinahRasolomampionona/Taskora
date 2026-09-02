<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateStatus', $this->route('task'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function status(): TaskStatus
    {
        return TaskStatus::from($this->validated('status'));
    }

    public function attributes(): array
    {
        return [
            'status' => 'statut',
            'comment' => 'commentaire',
        ];
    }
}

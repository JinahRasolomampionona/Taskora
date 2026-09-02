<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('comment', $this->route('task'));
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:2', 'max:5000'],
            'is_internal' => ['nullable', 'boolean'],
        ];
    }

    /** Une note interne n'a de sens que pour l'admin et l'agent. */
    public function isInternal(): bool
    {
        return (bool) $this->boolean('is_internal')
            && $this->user()->can('viewInternalNotes', $this->route('task'));
    }

    public function attributes(): array
    {
        return [
            'body' => 'commentaire',
        ];
    }
}

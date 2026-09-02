<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Inscription publique.
 *
 * Le role est choisi par le visiteur, mais il est valide contre la liste
 * blanche de config/taskora.php : un role retire de la configuration ne peut
 * plus etre cree, meme si le champ est force cote navigateur.
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:180', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(UserRole::registrableValues())],
            'job_title' => ['nullable', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => "Ce type de compte n'est pas ouvert a l'inscription.",
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'email' => 'adresse e-mail',
            'role' => 'type de compte',
            'job_title' => 'fonction',
            'company' => 'societe',
            'phone' => 'telephone',
            'password' => 'mot de passe',
        ];
    }

    /** Le role valide, sous forme d'enum. */
    public function role(): UserRole
    {
        return UserRole::from($this->validated('role'));
    }
}

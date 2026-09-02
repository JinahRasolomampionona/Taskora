<?php

namespace Database\Factories;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password123'),
            'role' => UserRole::Client,
            'job_title' => null,
            'company' => fake()->company(),
            'phone' => fake()->numerify('06 ## ## ## ##'),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Admin,
            'job_title' => 'Responsable des opérations',
            'company' => 'Taskora',
        ]);
    }

    public function agent(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Agent,
            'job_title' => fake()->randomElement([
                'Développeur back-end',
                'Développeuse front-end',
                'Chargé de support',
                'Designer produit',
                'Technicien réseau',
            ]),
            'company' => 'Taskora',
        ]);
    }

    public function client(): static
    {
        return $this->state(fn () => ['role' => UserRole::Client]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}

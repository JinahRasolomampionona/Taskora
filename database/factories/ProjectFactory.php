<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'name' => 'Équipe '.fake()->firstName(),
            'description' => fake()->sentence(12),
            'color' => fake()->randomElement(['#2563eb', '#0891b2', '#7c3aed', '#059669', '#c2410c']),
            'is_active' => true,
        ];
    }
}

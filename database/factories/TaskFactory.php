<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $estimates = [30, 60, 120, 240, 480, 960, 1440];

        return [
            'code' => strtoupper(fake()->unique()->bothify('TSK-####')),
            'title' => rtrim(fake()->sentence(6), '.'),
            'description' => fake()->paragraphs(2, true),
            'mission_details' => fake()->paragraph(),
            'status' => TaskStatus::Pending,
            'priority' => fake()->boolean(25) ? TaskPriority::Urgent : TaskPriority::Normal,
            'estimated_minutes' => fake()->randomElement($estimates),
            'spent_minutes' => 0,
            'due_date' => fake()->boolean(60)
                ? fake()->dateTimeBetween('-10 days', '+25 days')->format('Y-m-d')
                : null,
        ];
    }

    public function status(TaskStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }

    public function urgent(): static
    {
        return $this->state(fn () => ['priority' => TaskPriority::Urgent]);
    }
}

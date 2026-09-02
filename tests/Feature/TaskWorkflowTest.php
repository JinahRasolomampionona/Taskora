<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_agent_can_move_a_task_forward_step_by_step(): void
    {
        $agent = User::factory()->agent()->create();
        $task = Task::factory()->create([
            'assigned_to' => $agent->id,
            'status' => TaskStatus::Todo,
        ]);

        $this->actingAs($agent)
            ->patchJson(route('tasks.status', $task), ['status' => 'in_progress'])
            ->assertOk()
            ->assertJson(['success' => true, 'data' => ['status' => 'in_progress']]);

        $task->refresh();
        $this->assertSame(TaskStatus::InProgress, $task->status);
        $this->assertNotNull($task->started_at);

        $this->actingAs($agent)
            ->patchJson(route('tasks.status', $task), ['status' => 'completed'])
            ->assertOk();

        $task->refresh();
        $this->assertSame(TaskStatus::Completed, $task->status);
        $this->assertNotNull($task->completed_at);
    }

    public function test_an_agent_cannot_skip_a_step(): void
    {
        $agent = User::factory()->agent()->create();
        $task = Task::factory()->create([
            'assigned_to' => $agent->id,
            'status' => TaskStatus::Todo,
        ]);

        $this->actingAs($agent)
            ->patchJson(route('tasks.status', $task), ['status' => 'completed'])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertSame(TaskStatus::Todo, $task->fresh()->status);
    }

    public function test_an_agent_cannot_archive_a_task(): void
    {
        $agent = User::factory()->agent()->create();
        $task = Task::factory()->create([
            'assigned_to' => $agent->id,
            'status' => TaskStatus::Completed,
        ]);

        // Aucune transition n'est ouverte à l'agent depuis "Terminer".
        $this->actingAs($agent)
            ->patchJson(route('tasks.status', $task), ['status' => 'archived'])
            ->assertForbidden();

        $this->assertSame(TaskStatus::Completed, $task->fresh()->status);
    }

    public function test_an_admin_can_archive_a_completed_task(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create(['status' => TaskStatus::Completed]);

        $this->actingAs($admin)
            ->patchJson(route('tasks.status', $task), ['status' => 'archived'])
            ->assertOk();

        $task->refresh();
        $this->assertSame(TaskStatus::Archived, $task->status);
        $this->assertNotNull($task->archived_at);
    }

    public function test_a_client_can_cancel_only_a_pending_request(): void
    {
        $client = User::factory()->client()->create();

        $pending = Task::factory()->create([
            'client_id' => $client->id,
            'status' => TaskStatus::Pending,
        ]);

        $this->actingAs($client)
            ->patchJson(route('tasks.status', $pending), ['status' => 'cancelled'])
            ->assertOk();

        $this->assertSame(TaskStatus::Cancelled, $pending->fresh()->status);

        $inProgress = Task::factory()->create([
            'client_id' => $client->id,
            'status' => TaskStatus::InProgress,
        ]);

        $this->actingAs($client)
            ->patchJson(route('tasks.status', $inProgress), ['status' => 'cancelled'])
            ->assertForbidden();
    }

    public function test_every_transition_is_written_to_the_history(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create(['status' => TaskStatus::Pending]);

        $this->actingAs($admin)
            ->patchJson(route('tasks.status', $task), ['status' => 'todo', 'comment' => 'Prise en charge'])
            ->assertOk();

        $this->assertDatabaseHas('task_status_histories', [
            'task_id' => $task->id,
            'user_id' => $admin->id,
            'old_status' => 'pending',
            'new_status' => 'todo',
            'comment' => 'Prise en charge',
        ]);
    }

    public function test_assigning_an_agent_moves_a_pending_task_to_todo(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();
        $task = Task::factory()->create(['status' => TaskStatus::Pending]);

        $this->actingAs($admin)
            ->patchJson(route('tasks.assign', $task), ['assigned_to' => $agent->id])
            ->assertOk()
            ->assertJson(['success' => true]);

        $task->refresh();
        $this->assertSame($agent->id, $task->assigned_to);
        $this->assertSame(TaskStatus::Todo, $task->status);
    }

    public function test_an_unknown_status_is_rejected_by_validation(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create(['status' => TaskStatus::Pending]);

        $this->actingAs($admin)
            ->patchJson(route('tasks.status', $task), ['status' => 'termine'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }
}

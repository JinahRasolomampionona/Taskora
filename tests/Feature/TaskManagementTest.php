<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_create_a_task_and_assign_it(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($admin)
            ->post(route('admin.tasks.store'), [
                'title' => 'Refonte de la page de contact',
                'description' => 'Reprendre la page de contact et son formulaire.',
                'mission_details' => 'Maquette fournie.',
                'priority' => 'urgent',
                'client_id' => $client->id,
                'assigned_to' => $agent->id,
                'estimated_minutes' => 240,
            ])
            ->assertRedirect();

        $task = Task::firstOrFail();

        $this->assertSame('Refonte de la page de contact', $task->title);
        $this->assertSame($agent->id, $task->assigned_to);
        // Une tâche créée avec un agent entre directement dans le flux.
        $this->assertSame(TaskStatus::Todo, $task->status);
        $this->assertNotEmpty($task->code);
        $this->assertDatabaseHas('task_status_histories', ['task_id' => $task->id]);
    }

    public function test_a_task_created_without_an_agent_stays_pending(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($admin)->post(route('admin.tasks.store'), [
            'title' => 'Audit de sécurité annuel',
            'description' => 'Passer en revue les accès et les dépendances.',
            'priority' => 'normal',
            'client_id' => $client->id,
        ])->assertRedirect();

        $this->assertSame(TaskStatus::Pending, Task::firstOrFail()->status);
    }

    public function test_task_creation_is_validated(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.tasks.create'))
            ->post(route('admin.tasks.store'), ['title' => 'ab'])
            ->assertSessionHasErrors(['title', 'description', 'priority', 'client_id']);

        $this->assertSame(0, Task::count());
    }

    public function test_a_client_creates_a_pending_request_for_themselves(): void
    {
        $client = User::factory()->client()->create();
        $other = User::factory()->client()->create();

        $this->actingAs($client)->post(route('client.tasks.store'), [
            'title' => 'Ajouter une page mentions légales',
            'description' => 'Texte fourni par notre juriste.',
            'priority' => 'normal',
            // Tentative de rattacher la demande à un autre client : doit être ignorée.
            'client_id' => $other->id,
            'assigned_to' => 999,
            'estimated_minutes' => 999,
        ])->assertRedirect();

        $task = Task::firstOrFail();

        $this->assertSame($client->id, $task->client_id);
        $this->assertNull($task->assigned_to);
        $this->assertNull($task->estimated_minutes);
        $this->assertSame(TaskStatus::Pending, $task->status);
    }

    public function test_a_client_can_edit_a_pending_request_but_not_one_in_progress(): void
    {
        $client = User::factory()->client()->create();

        $pending = Task::factory()->create([
            'client_id' => $client->id,
            'status' => TaskStatus::Pending,
        ]);

        $this->actingAs($client)
            ->put(route('client.tasks.update', $pending), [
                'title' => 'Titre corrigé',
                'description' => 'Description corrigée.',
                'priority' => 'urgent',
            ])
            ->assertRedirect();

        $this->assertSame('Titre corrigé', $pending->fresh()->title);

        $inProgress = Task::factory()->create([
            'client_id' => $client->id,
            'status' => TaskStatus::InProgress,
        ]);

        $this->actingAs($client)
            ->get(route('client.tasks.edit', $inProgress))
            ->assertForbidden();
    }

    public function test_an_admin_can_delete_a_task(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.tasks.destroy', $task))
            ->assertRedirect(route('admin.tasks.index'));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_internal_notes_are_hidden_from_the_client(): void
    {
        $client = User::factory()->client()->create();
        $agent = User::factory()->agent()->create();

        $task = Task::factory()->create([
            'client_id' => $client->id,
            'assigned_to' => $agent->id,
        ]);

        $task->comments()->create([
            'user_id' => $agent->id,
            'body' => 'Note interne strictement confidentielle',
            'is_internal' => true,
        ]);

        $task->comments()->create([
            'user_id' => $agent->id,
            'body' => 'Bonjour, votre demande est bien prise en compte',
            'is_internal' => false,
        ]);

        $this->actingAs($client)
            ->get(route('client.tasks.show', $task))
            ->assertOk()
            ->assertDontSee('Note interne strictement confidentielle')
            ->assertSee('votre demande est bien prise en compte', false);
    }

    public function test_a_client_comment_cannot_be_flagged_as_internal(): void
    {
        $client = User::factory()->client()->create();
        $task = Task::factory()->create(['client_id' => $client->id]);

        $this->actingAs($client)
            ->postJson(route('tasks.comments.store', $task), [
                'body' => 'Merci pour le retour rapide.',
                'is_internal' => true,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'is_internal' => false,
        ]);
    }

    public function test_filters_narrow_down_the_task_list(): void
    {
        $admin = User::factory()->admin()->create();

        $urgent = Task::factory()->urgent()->create(['title' => 'Incident critique']);
        $normal = Task::factory()->create(['title' => 'Mise à jour du pied de page', 'priority' => 'normal']);

        $this->actingAs($admin)
            ->get(route('admin.tasks.index', ['priority' => 'urgent']))
            ->assertOk()
            ->assertSee($urgent->code)
            ->assertDontSee($normal->code);
    }

    public function test_archived_tasks_are_excluded_from_the_default_list(): void
    {
        $admin = User::factory()->admin()->create();

        $archived = Task::factory()->create(['status' => TaskStatus::Archived]);
        $active = Task::factory()->create(['status' => TaskStatus::Todo]);

        $this->actingAs($admin)
            ->get(route('admin.tasks.index'))
            ->assertOk()
            ->assertSee($active->code)
            ->assertDontSee($archived->code);
    }

    public function test_a_task_can_be_linked_to_a_project(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();
        $project = Project::factory()->create();

        $this->actingAs($admin)->post(route('admin.tasks.store'), [
            'title' => 'Cadrage du lot 2',
            'description' => 'Réunion de cadrage et compte rendu.',
            'priority' => 'normal',
            'client_id' => $client->id,
            'project_id' => $project->id,
        ])->assertRedirect();

        $this->assertSame($project->id, Task::firstOrFail()->project_id);
    }
}

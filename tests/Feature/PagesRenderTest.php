<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Vérifie que chaque écran attendu s'affiche réellement (rendu Blade inclus).
 */
class PagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_admin_pages_render(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();
        $client = User::factory()->client()->create();
        $project = Project::factory()->create(['owner_id' => $admin->id]);

        $task = Task::factory()->create([
            'client_id' => $client->id,
            'assigned_to' => $agent->id,
            'project_id' => $project->id,
            'created_by' => $admin->id,
        ]);

        $urls = [
            route('admin.dashboard'),
            route('admin.tasks.index'),
            route('admin.tasks.board'),
            route('admin.tasks.create'),
            route('admin.tasks.show', $task),
            route('admin.tasks.edit', $task),
            route('admin.projects.index'),
            route('admin.projects.create'),
            route('admin.projects.show', $project),
            route('admin.projects.edit', $project),
            route('admin.users.index'),
            route('admin.agents.index'),
            route('admin.clients.index'),
            route('admin.users.create'),
            route('admin.users.show', $agent),
            route('admin.users.edit', $agent),
            route('admin.archives.index'),
            route('admin.archives.index', ['scope' => 'cancelled']),
            route('profile.edit'),
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_all_agent_pages_render(): void
    {
        $agent = User::factory()->agent()->create();
        $task = Task::factory()->create(['assigned_to' => $agent->id]);

        foreach ([
            route('agent.dashboard'),
            route('agent.tasks.index'),
            route('agent.tasks.board'),
            route('agent.tasks.history'),
            route('agent.tasks.show', $task),
            route('profile.edit'),
        ] as $url) {
            $this->actingAs($agent)->get($url)->assertOk();
        }
    }

    public function test_all_client_pages_render(): void
    {
        $client = User::factory()->client()->create();
        $task = Task::factory()->create(['client_id' => $client->id]);

        foreach ([
            route('client.dashboard'),
            route('client.tasks.index'),
            route('client.tasks.create'),
            route('client.tasks.show', $task),
            route('client.tasks.edit', $task),
            route('profile.edit'),
        ] as $url) {
            $this->actingAs($client)->get($url)->assertOk();
        }
    }

    public function test_the_task_list_can_be_refreshed_through_ajax(): void
    {
        $admin = User::factory()->admin()->create();
        Task::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('admin.tasks.index', ['q' => '']), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            // Le fragment ne doit contenir ni la sidebar ni la barre supérieure.
            ->assertDontSee('tk-sidebar__brand');
    }

    public function test_empty_states_render_without_data(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.tasks.index'))->assertOk()->assertSee('Aucune tâche');
        $this->actingAs($admin)->get(route('admin.projects.index'))->assertOk()->assertSee('Aucun projet');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cloisonnement des trois espaces : la restriction doit venir du serveur,
 * jamais de l'absence d'un lien dans le menu.
 */
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_client_cannot_reach_the_admin_area(): void
    {
        $client = User::factory()->client()->create();

        foreach ([
            route('admin.dashboard'),
            route('admin.tasks.index'),
            route('admin.users.index'),
            route('admin.projects.index'),
            route('admin.archives.index'),
        ] as $url) {
            $this->actingAs($client)->get($url)->assertForbidden();
        }
    }

    public function test_an_agent_cannot_reach_the_client_area(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)->get(route('client.dashboard'))->assertForbidden();
        $this->actingAs($agent)->get(route('client.tasks.create'))->assertForbidden();
    }

    public function test_an_agent_cannot_reach_the_admin_area(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($agent)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_an_admin_cannot_reach_the_agent_or_client_areas(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('agent.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('client.dashboard'))->assertForbidden();
    }

    public function test_an_agent_only_sees_the_tasks_assigned_to_them(): void
    {
        $agent = User::factory()->agent()->create();
        $other = User::factory()->agent()->create();
        $client = User::factory()->client()->create();

        $mine = Task::factory()->create(['assigned_to' => $agent->id, 'client_id' => $client->id]);
        $theirs = Task::factory()->create(['assigned_to' => $other->id, 'client_id' => $client->id]);

        $this->actingAs($agent)->get(route('agent.tasks.show', $mine))->assertOk();
        $this->actingAs($agent)->get(route('agent.tasks.show', $theirs))->assertForbidden();

        $this->actingAs($agent)
            ->get(route('agent.tasks.index'))
            ->assertOk()
            ->assertSee($mine->code)
            ->assertDontSee($theirs->code);
    }

    public function test_a_client_only_sees_their_own_requests(): void
    {
        $client = User::factory()->client()->create();
        $other = User::factory()->client()->create();

        $mine = Task::factory()->create(['client_id' => $client->id]);
        $theirs = Task::factory()->create(['client_id' => $other->id]);

        $this->actingAs($client)->get(route('client.tasks.show', $mine))->assertOk();
        $this->actingAs($client)->get(route('client.tasks.show', $theirs))->assertForbidden();
    }

    public function test_a_client_cannot_assign_a_task(): void
    {
        $client = User::factory()->client()->create();
        $agent = User::factory()->agent()->create();
        $task = Task::factory()->create(['client_id' => $client->id]);

        $this->actingAs($client)
            ->patchJson(route('tasks.assign', $task), ['assigned_to' => $agent->id])
            ->assertForbidden();

        $this->assertNull($task->fresh()->assigned_to);
    }

    public function test_an_agent_cannot_assign_a_task(): void
    {
        $agent = User::factory()->agent()->create();
        $task = Task::factory()->create(['assigned_to' => $agent->id]);

        $this->actingAs($agent)
            ->patchJson(route('tasks.assign', $task), ['assigned_to' => $agent->id])
            ->assertForbidden();
    }

    public function test_an_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}

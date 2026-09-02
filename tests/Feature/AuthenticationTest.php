<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_reachable_for_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Connexion');
    }

    public function test_each_role_lands_on_its_own_dashboard(): void
    {
        $cases = [
            [User::factory()->admin()->create(), route('admin.dashboard')],
            [User::factory()->agent()->create(), route('agent.dashboard')],
            [User::factory()->client()->create(), route('client.dashboard')],
        ];

        foreach ($cases as [$user, $expected]) {
            $this->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'password123',
            ])->assertRedirect($expected);

            $this->assertAuthenticatedAs($user);
            $this->post(route('logout'));
        }
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->client()->create();

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'mauvais-mot-de-passe',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_deactivated_account_cannot_sign_in(): void
    {
        $user = User::factory()->agent()->inactive()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_creates_an_account_with_the_selected_role(): void
    {
        foreach ([
            'client' => 'client.dashboard',
            'user' => 'agent.dashboard',
            'admin' => 'admin.dashboard',
        ] as $role => $dashboard) {
            $email = $role.'@example.test';

            $this->post(route('register.store'), [
                'name' => 'Camille Durand',
                'email' => $email,
                'role' => $role,
                'password' => 'motdepasse1',
                'password_confirmation' => 'motdepasse1',
            ])->assertRedirect(route($dashboard));

            $this->assertDatabaseHas('users', [
                'email' => $email,
                'role' => $role,
                'is_active' => true,
            ]);

            $this->post(route('logout'));
        }
    }

    public function test_registration_requires_a_role(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Camille Durand',
            'email' => 'camille@example.test',
            'password' => 'motdepasse1',
            'password_confirmation' => 'motdepasse1',
        ])->assertSessionHasErrors('role');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'camille@example.test']);
    }

    public function test_registration_rejects_a_role_closed_in_the_configuration(): void
    {
        config()->set('taskora.registration.roles', ['client']);

        $this->post(route('register.store'), [
            'name' => 'Camille Durand',
            'email' => 'camille@example.test',
            'role' => 'admin',
            'password' => 'motdepasse1',
            'password_confirmation' => 'motdepasse1',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'camille@example.test']);
    }

    public function test_the_registration_form_lists_the_configured_roles(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('value="admin"', false)
            ->assertSee('value="user"', false)
            ->assertSee('value="client"', false);

        config()->set('taskora.registration.roles', ['client']);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('value="client"', false)
            ->assertDontSee('value="admin"', false);
    }

    public function test_registration_is_closed_when_disabled(): void
    {
        config()->set('taskora.registration.enabled', false);

        $this->get(route('register'))->assertNotFound();

        $this->post(route('register.store'), [
            'name' => 'Camille Durand',
            'email' => 'camille@example.test',
            'role' => 'client',
            'password' => 'motdepasse1',
            'password_confirmation' => 'motdepasse1',
        ])->assertSessionHasErrors('role');
    }

    public function test_the_login_page_links_to_the_agent_and_client_sign_up(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('register', ['role' => 'user']), false)
            ->assertSee(route('register', ['role' => 'client']), false);
    }

    public function test_password_fields_expose_a_visibility_toggle(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('data-tk-password-toggle', false)
            ->assertSee('aria-controls="password"', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('aria-controls="password"', false)
            ->assertSee('aria-controls="password_confirmation"', false);
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('agent.dashboard'))->assertRedirect(route('login'));
        $this->get(route('client.dashboard'))->assertRedirect(route('login'));
    }

    public function test_a_user_can_sign_out(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}

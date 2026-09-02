<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Inscription publique.
 *
 * Le visiteur choisit le type de compte a creer (administrateur, agent ou
 * client). Les roles reellement proposes proviennent de
 * config/taskora.php > registration.roles : ils sont utilises pour construire
 * le formulaire et, surtout, pour valider la requete cote serveur.
 */
class RegisterController extends Controller
{
    public function create(Request $request): View
    {
        $roles = $this->availableRoles();

        return view('auth.register', [
            'roles' => $roles,
            'selectedRole' => $this->resolveSelectedRole($request, $roles),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $this->availableRoles();

        $role = $request->role();

        $user = User::create([
            ...$request->safe()->except('role'),
            'role' => $role,
            'is_active' => true,
        ]);

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        return redirect()
            ->route($role->homeRoute())
            ->with('success', 'Votre compte '.$role->label().' Taskora a ete cree.');
    }

    /**
     * Roles ouverts a l'inscription, ou 404 si la fonctionnalite est fermee.
     *
     * @return array<int,UserRole>
     */
    protected function availableRoles(): array
    {
        $roles = UserRole::registrable();

        abort_if($roles === [], 404);

        return $roles;
    }

    /**
     * Role preselectionne : ancienne saisie, parametre `?role=`, puis Client.
     *
     * @param  array<int,UserRole>  $roles
     */
    protected function resolveSelectedRole(Request $request, array $roles): UserRole
    {
        $candidate = UserRole::tryFrom((string) old('role', $request->query('role')));

        if ($candidate && in_array($candidate, $roles, true)) {
            return $candidate;
        }

        return in_array(UserRole::Client, $roles, true) ? UserRole::Client : $roles[0];
    }
}

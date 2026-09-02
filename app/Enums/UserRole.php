<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Agent = 'user';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Agent => 'Agent',
            self::Client => 'Client',
        };
    }

    /** Courte description du rôle, utilisée dans les formulaires. */
    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Supervise les projets, les comptes et toutes les tâches.',
            self::Agent => 'Traite les tâches qui lui sont assignées.',
            self::Client => 'Dépose des demandes et suit leur avancement.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Admin => 'bi-shield-lock',
            self::Agent => 'bi-person-badge',
            self::Client => 'bi-people',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Admin => 'tk-badge-role-admin',
            self::Agent => 'tk-badge-role-agent',
            self::Client => 'tk-badge-role-client',
        };
    }

    /** Route du tableau de bord par défaut pour ce rôle. */
    public function homeRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Agent => 'agent.dashboard',
            self::Client => 'client.dashboard',
        };
    }

    /** @return array<string,string> valeur => libellé */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $r) => [$r->value => $r->label()])
            ->all();
    }

    /*
     | -----------------------------------------------------------------
     | Inscription publique
     | -----------------------------------------------------------------
     | La liste autorisée vit dans config/taskora.php : elle sert à la fois
     | à construire le formulaire et à valider la requête côté serveur.
     */

    /** @return array<int,self> rôles ouverts à l'inscription publique */
    public static function registrable(): array
    {
        if (! config('taskora.registration.enabled', true)) {
            return [];
        }

        $allowed = (array) config('taskora.registration.roles', []);

        return array_values(array_filter(
            self::cases(),
            fn (self $role) => in_array($role->value, $allowed, true)
        ));
    }

    /** @return array<int,string> valeurs des rôles ouverts à l'inscription */
    public static function registrableValues(): array
    {
        return array_map(fn (self $role) => $role->value, self::registrable());
    }

    public function isRegistrable(): bool
    {
        return in_array($this, self::registrable(), true);
    }
}

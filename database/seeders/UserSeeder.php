<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Comptes de démonstration.
 *
 * Mot de passe commun : password123 — usage strictement local.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Sofiane Benali',
                'email' => 'admin@taskora.test',
                'job_title' => 'Responsable des opérations',
            ],
        ];

        $agents = [
            ['name' => 'Léa Moreau', 'email' => 'lea.moreau@taskora.test', 'job_title' => 'Développeuse front-end'],
            ['name' => 'Marc Dubois', 'email' => 'marc.dubois@taskora.test', 'job_title' => 'Développeur back-end'],
            ['name' => 'Inès Charef', 'email' => 'ines.charef@taskora.test', 'job_title' => 'Chargée de support'],
        ];

        $clients = [
            ['name' => 'Camille Durand', 'email' => 'contact@nordbat.fr', 'company' => 'Nord Bâtiment'],
            ['name' => 'Thomas Rivière', 'email' => 'contact@lumidesign.fr', 'company' => 'Lumi Design'],
            ['name' => 'Awa Traoré', 'email' => 'contact@atlas-logistique.fr', 'company' => 'Atlas Logistique'],
        ];

        foreach ($admins as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [...$data, 'role' => UserRole::Admin, 'company' => 'Taskora', 'password' => 'password123', 'is_active' => true, 'email_verified_at' => now()]
            );
        }

        foreach ($agents as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [...$data, 'role' => UserRole::Agent, 'company' => 'Taskora', 'password' => 'password123', 'is_active' => true, 'email_verified_at' => now()]
            );
        }

        foreach ($clients as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [...$data, 'role' => UserRole::Client, 'password' => 'password123', 'is_active' => true, 'email_verified_at' => now()]
            );
        }
    }
}

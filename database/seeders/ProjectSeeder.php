<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', UserRole::Admin->value)->first();
        $agents = User::where('role', UserRole::Agent->value)->get();

        $projects = [
            [
                'code' => 'NORDBAT',
                'name' => 'Équipe Nord Bâtiment',
                'description' => 'Site vitrine, espace client et supports commerciaux du groupe Nord Bâtiment.',
                'color' => '#2563eb',
            ],
            [
                'code' => 'LUMI',
                'name' => 'Équipe Lumi Design',
                'description' => 'Refonte de l\'identité visuelle et de la boutique en ligne.',
                'color' => '#7c3aed',
            ],
            [
                'code' => 'ATLAS',
                'name' => 'Équipe Atlas Logistique',
                'description' => 'Outillage interne : suivi des tournées et tableaux de bord d\'exploitation.',
                'color' => '#059669',
            ],
            [
                'code' => 'INTERNE',
                'name' => 'Maintenance interne',
                'description' => 'Tâches transverses de maintenance et d\'amélioration continue.',
                'color' => '#0891b2',
            ],
        ];

        foreach ($projects as $data) {
            $project = Project::updateOrCreate(
                ['code' => $data['code']],
                [...$data, 'owner_id' => $admin?->id, 'is_active' => true]
            );

            // Chaque projet embarque l'admin et deux agents.
            $members = $agents->random(min(2, $agents->count()))->pluck('id')->all();
            if ($admin) {
                $members[] = $admin->id;
            }

            $project->members()->sync($members);
        }
    }
}

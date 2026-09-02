<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
        ]);

        $this->command?->newLine();
        $this->command?->info('Comptes de démonstration (mot de passe : password123)');
        $this->command?->table(
            ['Rôle', 'Adresse e-mail'],
            [
                ['Administrateur', 'admin@taskora.test'],
                ['Agent', 'lea.moreau@taskora.test'],
                ['Agent', 'marc.dubois@taskora.test'],
                ['Agent', 'ines.charef@taskora.test'],
                ['Client', 'contact@nordbat.fr'],
                ['Client', 'contact@lumidesign.fr'],
                ['Client', 'contact@atlas-logistique.fr'],
            ]
        );
    }
}

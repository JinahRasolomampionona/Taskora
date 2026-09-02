<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Jeu de tâches réaliste couvrant les six statuts et les deux priorités.
 *
 * L'historique est reconstitué de façon cohérente : une tâche terminée
 * possède bien la chaîne En attente → À faire → En cours → Terminer.
 */
class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', UserRole::Admin->value)->firstOrFail();
        $agents = User::where('role', UserRole::Agent->value)->get()->keyBy('email');
        $clients = User::where('role', UserRole::Client->value)->get()->keyBy('email');
        $projects = Project::all()->keyBy('code');

        $lea = $agents['lea.moreau@taskora.test'] ?? $agents->first();
        $marc = $agents['marc.dubois@taskora.test'] ?? $agents->first();
        $ines = $agents['ines.charef@taskora.test'] ?? $agents->first();

        $nordbat = $clients['contact@nordbat.fr'] ?? $clients->first();
        $lumi = $clients['contact@lumidesign.fr'] ?? $clients->first();
        $atlas = $clients['contact@atlas-logistique.fr'] ?? $clients->first();

        $definitions = [
            [
                'title' => 'Mettre à jour la grille tarifaire du site',
                'description' => "Les tarifs affichés sur la page « Nos offres » datent de l'an dernier.\n\nIl faut reprendre les trois formules, mettre à jour les montants et republier la page.",
                'mission_details' => "1. Récupérer la grille validée par la direction commerciale\n2. Mettre à jour les trois blocs tarifaires\n3. Vérifier l'affichage mobile\n4. Republier et prévenir le client",
                'status' => TaskStatus::Pending,
                'priority' => TaskPriority::Normal,
                'client' => $nordbat, 'agent' => null, 'project' => 'NORDBAT',
                'estimated' => 120, 'due' => 6,
            ],
            [
                'title' => 'Formulaire de contact : les pièces jointes ne partent pas',
                'description' => "Depuis lundi, les fichiers joints au formulaire de contact ne sont plus reçus par le service commercial. Le message texte arrive bien, mais sans les pièces jointes.",
                'mission_details' => "Reproduire le problème, vérifier la configuration d'envoi et la taille maximale acceptée, corriger puis tester avec un PDF de 4 Mo.",
                'status' => TaskStatus::InProgress,
                'priority' => TaskPriority::Urgent,
                'client' => $nordbat, 'agent' => $marc, 'project' => 'NORDBAT',
                'estimated' => 240, 'due' => 1, 'spent' => 95,
            ],
            [
                'title' => 'Créer les visuels de la campagne de printemps',
                'description' => "Nous lançons une campagne au 15 du mois prochain. Il nous faut une bannière site, trois visuels réseaux sociaux et une signature e-mail.",
                'mission_details' => "Décliner la charte existante.\nFormats attendus : 1920x600, 1080x1080 (x3), 600x150.\nLivraison en PNG et source.",
                'status' => TaskStatus::Todo,
                'priority' => TaskPriority::Normal,
                'client' => $lumi, 'agent' => $lea, 'project' => 'LUMI',
                'estimated' => 960, 'due' => 12,
            ],
            [
                'title' => 'Refonte de la fiche produit',
                'description' => "La fiche produit actuelle est trop dense. L'objectif est de hiérarchiser l'information : visuel, prix, points clés, puis description longue.",
                'mission_details' => "Maquette validée en pièce jointe du dernier échange. Intégration responsive attendue.",
                'status' => TaskStatus::InProgress,
                'priority' => TaskPriority::Normal,
                'client' => $lumi, 'agent' => $lea, 'project' => 'LUMI',
                'estimated' => 480, 'due' => 9, 'spent' => 180,
            ],
            [
                'title' => 'Export CSV des tournées hebdomadaires',
                'description' => "L'exploitation a besoin d'un export CSV des tournées de la semaine, avec le chauffeur, le véhicule, le nombre d'arrêts et le kilométrage.",
                'mission_details' => "Ajouter un bouton d'export sur l'écran Tournées.\nEncodage UTF-8, séparateur point-virgule.\nFiltre sur la plage de dates sélectionnée.",
                'status' => TaskStatus::Completed,
                'priority' => TaskPriority::Normal,
                'client' => $atlas, 'agent' => $marc, 'project' => 'ATLAS',
                'estimated' => 240, 'due' => -3, 'spent' => 210,
            ],
            [
                'title' => 'Accès bloqué pour trois comptes chauffeurs',
                'description' => "Trois chauffeurs ne parviennent plus à se connecter à l'application depuis la mise à jour de vendredi. Message affiché : identifiants invalides.",
                'mission_details' => "Vérifier l'état des comptes, réinitialiser si nécessaire et confirmer au client par retour.",
                'status' => TaskStatus::Completed,
                'priority' => TaskPriority::Urgent,
                'client' => $atlas, 'agent' => $ines, 'project' => 'ATLAS',
                'estimated' => 60, 'due' => -5, 'spent' => 45,
            ],
            [
                'title' => 'Mise à jour des dépendances et des correctifs de sécurité',
                'description' => "Passage en revue trimestriel des dépendances applicatives et application des correctifs de sécurité disponibles.",
                'mission_details' => "Lister les mises à jour disponibles, appliquer par lot, relancer la suite de tests, documenter les changements.",
                'status' => TaskStatus::Todo,
                'priority' => TaskPriority::Normal,
                'client' => $nordbat, 'agent' => $marc, 'project' => 'INTERNE',
                'estimated' => 480, 'due' => 20,
            ],
            [
                'title' => 'Traduire l\'interface en néerlandais',
                'description' => "Demande annulée : le déploiement aux Pays-Bas est reporté à l'exercice suivant.",
                'mission_details' => null,
                'status' => TaskStatus::Cancelled,
                'priority' => TaskPriority::Normal,
                'client' => $lumi, 'agent' => null, 'project' => 'LUMI',
                'estimated' => 1440, 'due' => null,
            ],
            [
                'title' => 'Ajouter un bandeau de consentement aux cookies',
                'description' => "Mise en conformité : bandeau de consentement avec choix granulaire et mémorisation de la préférence pendant six mois.",
                'mission_details' => "Trois catégories : nécessaires, mesure d'audience, marketing.\nLe refus doit être aussi simple que l'acceptation.",
                'status' => TaskStatus::Archived,
                'priority' => TaskPriority::Normal,
                'client' => $nordbat, 'agent' => $lea, 'project' => 'NORDBAT',
                'estimated' => 240, 'due' => -20, 'spent' => 230,
            ],
            [
                'title' => 'Optimiser le temps de chargement de la page d\'accueil',
                'description' => "La page d'accueil met plus de six secondes à s'afficher sur mobile. Objectif : passer sous les deux secondes.",
                'mission_details' => "Compresser les images, différer les scripts non critiques, activer la mise en cache navigateur.",
                'status' => TaskStatus::InProgress,
                'priority' => TaskPriority::Urgent,
                'client' => $lumi, 'agent' => $marc, 'project' => 'LUMI',
                'estimated' => 480, 'due' => 2, 'spent' => 120,
            ],
            [
                'title' => 'Rédiger la procédure d\'accueil des nouveaux chauffeurs',
                'description' => "Document interne décrivant les étapes de création de compte, de remise du matériel et de première prise en main de l'application.",
                'mission_details' => null,
                'status' => TaskStatus::Pending,
                'priority' => TaskPriority::Normal,
                'client' => $atlas, 'agent' => null, 'project' => 'ATLAS',
                'estimated' => 240, 'due' => 15,
            ],
            [
                'title' => 'Corriger l\'affichage des factures sur Safari',
                'description' => "Les factures téléchargées depuis Safari s'ouvrent avec une mise en page décalée. Le rendu est correct sur Chrome et Firefox.",
                'mission_details' => "Tester sur Safari 17, identifier la règle CSS en cause, corriger sans régression sur les autres navigateurs.",
                'status' => TaskStatus::Todo,
                'priority' => TaskPriority::Urgent,
                'client' => $nordbat, 'agent' => $ines, 'project' => 'NORDBAT',
                'estimated' => 120, 'due' => 3,
            ],
            [
                'title' => 'Mettre en place la sauvegarde quotidienne',
                'description' => "Automatiser une sauvegarde quotidienne de la base de données avec rétention de trente jours.",
                'mission_details' => "Sauvegarde à 3 h du matin, notification en cas d'échec, test de restauration mensuel.",
                'status' => TaskStatus::Completed,
                'priority' => TaskPriority::Normal,
                'client' => $atlas, 'agent' => $marc, 'project' => 'INTERNE',
                'estimated' => 240, 'due' => -8, 'spent' => 260,
            ],
            [
                'title' => 'Intégrer la newsletter mensuelle de mars',
                'description' => "Intégration HTML de la newsletter à partir de la maquette fournie, test d'affichage sur les principaux clients de messagerie.",
                'mission_details' => "Tester sur Outlook, Gmail et Apple Mail. Largeur 600 px, images hébergées.",
                'status' => TaskStatus::Archived,
                'priority' => TaskPriority::Normal,
                'client' => $lumi, 'agent' => $lea, 'project' => 'LUMI',
                'estimated' => 240, 'due' => -25, 'spent' => 200,
            ],
        ];

        foreach ($definitions as $index => $definition) {
            $this->createTask($definition, $index, $admin, $projects);
        }
    }

    private function createTask(array $definition, int $index, User $admin, $projects): void
    {
        $createdAt = now()->subDays(30 - $index * 2)->setTime(9 + ($index % 8), ($index * 7) % 60);

        $task = new Task([
            'title' => $definition['title'],
            'description' => $definition['description'],
            'mission_details' => $definition['mission_details'],
            'priority' => $definition['priority'],
            'estimated_minutes' => $definition['estimated'],
            'spent_minutes' => $definition['spent'] ?? 0,
            'due_date' => isset($definition['due']) && $definition['due'] !== null
                ? now()->addDays($definition['due'])->format('Y-m-d')
                : null,
        ]);

        $task->code = sprintf('TSK-%04d', $index + 1);
        $task->status = $definition['status'];
        $task->client_id = $definition['client']?->id;
        $task->assigned_to = $definition['agent']?->id;
        $task->created_by = $definition['client']?->id ?? $admin->id;
        $task->project_id = $projects[$definition['project']]->id ?? null;
        $task->created_at = $createdAt;
        $task->updated_at = $createdAt;

        $this->applyTimestamps($task, $createdAt);
        $task->save();

        $this->seedHistory($task, $admin, $definition, $createdAt);
        $this->seedComments($task, $admin, $definition, $createdAt);
    }

    /** Reconstitue des horodatages métier cohérents avec le statut final. */
    private function applyTimestamps(Task $task, $createdAt): void
    {
        $status = $task->status;

        if (in_array($status, [TaskStatus::InProgress, TaskStatus::Completed, TaskStatus::Archived], true)) {
            $task->started_at = (clone $createdAt)->addDays(1);
        }

        if (in_array($status, [TaskStatus::Completed, TaskStatus::Archived], true)) {
            $task->completed_at = (clone $createdAt)->addDays(3);
        }

        if ($status === TaskStatus::Archived) {
            $task->archived_at = (clone $createdAt)->addDays(5);
        }

        if ($status === TaskStatus::Cancelled) {
            $task->cancelled_at = (clone $createdAt)->addDays(2);
        }
    }

    /** Chaîne de transitions menant au statut courant. */
    private function seedHistory(Task $task, User $admin, array $definition, $createdAt): void
    {
        $agent = $definition['agent'];
        $client = $definition['client'];

        $steps = [[null, TaskStatus::Pending, $client ?? $admin, 'Création de la demande', 0]];

        $chain = match ($task->status) {
            TaskStatus::Pending => [],
            TaskStatus::Todo => [TaskStatus::Todo],
            TaskStatus::InProgress => [TaskStatus::Todo, TaskStatus::InProgress],
            TaskStatus::Completed => [TaskStatus::Todo, TaskStatus::InProgress, TaskStatus::Completed],
            TaskStatus::Archived => [TaskStatus::Todo, TaskStatus::InProgress, TaskStatus::Completed, TaskStatus::Archived],
            TaskStatus::Cancelled => [TaskStatus::Cancelled],
        };

        $previous = TaskStatus::Pending;
        $offset = 0;

        foreach ($chain as $status) {
            $offset++;

            $actor = match ($status) {
                TaskStatus::Todo, TaskStatus::Archived => $admin,
                TaskStatus::Cancelled => $admin,
                default => $agent ?? $admin,
            };

            $comment = match ($status) {
                TaskStatus::Todo => 'Tâche assignée à '.($agent?->name ?? 'une équipe'),
                TaskStatus::InProgress => 'Prise en charge démarrée',
                TaskStatus::Completed => 'Travail livré au client',
                TaskStatus::Archived => 'Tâche archivée après validation',
                TaskStatus::Cancelled => 'Demande annulée par le client',
                default => null,
            };

            $steps[] = [$previous, $status, $actor, $comment, $offset];
            $previous = $status;
        }

        foreach ($steps as [$old, $new, $actor, $comment, $dayOffset]) {
            $at = (clone $createdAt)->addDays($dayOffset)->addMinutes(45);

            TaskStatusHistory::create([
                'task_id' => $task->id,
                'user_id' => $actor?->id,
                'old_status' => $old?->value,
                'new_status' => $new->value,
                'comment' => $comment,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }
    }

    private function seedComments(Task $task, User $admin, array $definition, $createdAt): void
    {
        // Toutes les tâches ne portent pas d'échange : on garde un jeu réaliste.
        if ($task->id % 3 === 0) {
            return;
        }

        $agent = $definition['agent'];
        $client = $definition['client'];

        if ($client) {
            TaskComment::create([
                'task_id' => $task->id,
                'user_id' => $client->id,
                'body' => 'Merci de me tenir informé dès que la tâche est prise en charge.',
                'is_internal' => false,
                'created_at' => (clone $createdAt)->addHours(3),
                'updated_at' => (clone $createdAt)->addHours(3),
            ]);
        }

        if ($agent) {
            TaskComment::create([
                'task_id' => $task->id,
                'user_id' => $agent->id,
                'body' => "C'est noté, je regarde cela aujourd'hui et je reviens vers vous.",
                'is_internal' => false,
                'created_at' => (clone $createdAt)->addDays(1)->addHours(2),
                'updated_at' => (clone $createdAt)->addDays(1)->addHours(2),
            ]);

            TaskComment::create([
                'task_id' => $task->id,
                'user_id' => $admin->id,
                'body' => 'Note interne : penser à vérifier le contrat de maintenance avant facturation.',
                'is_internal' => true,
                'created_at' => (clone $createdAt)->addDays(1)->addHours(4),
                'updated_at' => (clone $createdAt)->addDays(1)->addHours(4),
            ]);
        }
    }
}

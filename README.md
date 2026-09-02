# Taskora

**Plateforme de gestion et de suivi collaboratif des tâches.**

Taskora permet à des **clients** de déposer des demandes, à des **administrateurs** de les
organiser et de les distribuer, et à des **agents** de les traiter jusqu'à leur finalisation
ou leur archivage.

L'application est construite avec Laravel 12, Blade et Bootstrap 5, sans framework front-end
supplémentaire : les interactions temps réel (changement de statut, Kanban, filtres, commentaires)
passent par la Fetch API.

---

## Table des matières

1. [Objectif](#objectif)
2. [Fonctionnalités](#fonctionnalités)
3. [Rôles et permissions](#rôles-et-permissions)
4. [Workflow des tâches](#workflow-des-tâches)
5. [Architecture](#architecture)
6. [Prérequis](#prérequis)
7. [Installation](#installation)
8. [Configuration `.env`](#configuration-env)
9. [Base de données](#base-de-données)
10. [Lancement en local](#lancement-en-local)
11. [Comptes de démonstration](#comptes-de-démonstration)
12. [Structure des dossiers](#structure-des-dossiers)
13. [Routes principales](#routes-principales)
14. [Endpoints AJAX](#endpoints-ajax)
15. [Sécurité](#sécurité)
16. [Tests](#tests)
17. [Personnalisation](#personnalisation)
18. [Problèmes connus](#problèmes-connus)
19. [Évolutions possibles](#évolutions-possibles)

---

## Objectif

Fournir un outil interne réellement exploitable — pas une démonstration — permettant à une
équipe de :

- centraliser les demandes entrantes des clients ;
- distribuer le travail entre les agents ;
- suivre chaque tâche du dépôt à l'archivage ;
- tracer l'intégralité des changements de statut ;
- piloter l'activité grâce à des statistiques calculées côté serveur.

---

## Fonctionnalités

### Gestion des tâches
- CRUD complet (création, consultation, modification, suppression)
- Code séquentiel automatique (`TSK-0001`)
- Priorité **Normal** / **Urgent**
- Estimation de temps exploitable (stockée en minutes)
- Temps enregistré, date limite, détection des retards
- Rattachement optionnel à un projet
- Missions à effectuer (cadrage opérationnel destiné à l'agent)

### Vues
- **Tableau Kanban** en six colonnes avec glisser-déposer
- **Vue liste** en cartes, filtrable et paginée
- **Page de détail** en deux colonnes : contenu riche + panneau de métadonnées
- **Vue projets** sous forme de tableau professionnel
- **Archives** : tâches archivées et annulées

### Collaboration
- Commentaires par tâche, avec **notes internes** invisibles pour le client
- Historique complet des transitions de statut (auteur, date, commentaire)
- Assignation d'un agent en un clic

### Pilotage
- Tableaux de bord distincts pour chaque rôle
- Statistiques par statut, priorité, retard, agent
- Charge de travail par agent
- Dernières activités et derniers commentaires

### Expérience
- Changement de statut **sans rechargement**
- Filtrage et recherche **en AJAX** avec synchronisation de l'URL
- Notifications toast pour chaque action
- Sidebar réductible (préférence mémorisée) et menu mobile
- Design responsive desktop / tablette / mobile
- Pages d'erreur personnalisées (403, 404, 419, 429, 500, 503)

---

## Rôles et permissions

| | Admin | Agent (`user`) | Client |
|---|---|---|---|
| Tableau de bord dédié | ✅ | ✅ | ✅ |
| Voir toutes les tâches | ✅ | ❌ (les siennes) | ❌ (les siennes) |
| Créer une tâche | ✅ | ❌ | ✅ (demande) |
| Modifier une tâche | ✅ | ❌ | ✅ si encore *En attente* |
| Supprimer une tâche | ✅ | ❌ | ❌ |
| Assigner un agent | ✅ | ❌ | ❌ |
| Faire évoluer le statut | ✅ (large) | ✅ (flux nominal) | ✅ (annuler si *En attente*) |
| Archiver | ✅ | ❌ | ❌ |
| Gérer les comptes et les rôles | ✅ | ❌ | ❌ |
| Gérer les projets | ✅ | ❌ | ❌ |
| Voir les notes internes | ✅ | ✅ (ses tâches) | ❌ |

Les restrictions sont appliquées **côté serveur** par le middleware `role` et par les
Policies (`TaskPolicy`, `UserPolicy`, `ProjectPolicy`). Connaître une URL ne donne aucun accès.

### Création des comptes

Deux chemins mènent à la création d'un compte :

1. **Inscription publique** — `/inscription` propose un sélecteur de type de compte
   (**Administrateur**, **Agent**, **Client**). C'est ce formulaire qui permet de créer
   le tout premier compte administrateur d'une installation neuve. L'écran de connexion
   propose des raccourcis directs vers ce formulaire, avec le rôle présélectionné
   (`/inscription?role=user`, `/inscription?role=client`, `/inscription?role=admin`).
2. **Depuis l'espace administrateur** — l'admin crée agents, clients et administrateurs
   depuis *Utilisateurs* / *Agents* / *Clients*, ou via le menu **Créer** et le bloc
   **Actions rapides** du tableau de bord (tâche, projet, agent, client).

Le rôle envoyé par le formulaire public est validé contre la liste blanche
`config/taskora.php` → `registration.roles` : un rôle absent de cette liste est refusé
côté serveur, même si le champ est modifié dans le navigateur.

```dotenv
# Ouvre les trois types de compte (valeur par défaut)
TASKORA_REGISTRATION_ROLES=admin,user,client

# Recommandé une fois le compte administrateur créé
TASKORA_REGISTRATION_ROLES=client

# Ferme complètement l'inscription publique (/inscription renvoie 404)
TASKORA_REGISTRATION_ENABLED=false
```

> ⚠️ Laisser `admin` ouvert signifie que n'importe quel visiteur peut se créer un compte
> administrateur. C'est pratique en local et pour l'amorçage ; en production, restreignez
> la liste dès que votre compte admin existe.

---

## Workflow des tâches

Les statuts sont stockés sous forme de **valeurs techniques stables** ; les libellés français
ne servent qu'à l'affichage et ne portent aucune logique métier.

| Valeur en base | Libellé UI |
|---|---|
| `pending` | En attente |
| `todo` | À faire |
| `in_progress` | En cours |
| `completed` | Terminer |
| `cancelled` | Annuler |
| `archived` | Archive |

### Transitions autorisées

```
Admin    pending  → todo, in_progress, cancelled
         todo     → pending, in_progress, cancelled
         in_progress → todo, completed, cancelled
         completed   → in_progress, archived
         cancelled   → pending, archived
         archived    → pending, completed

Agent    pending     → todo
         todo        → in_progress
         in_progress → todo, completed

Client   pending     → cancelled
```

Ces règles vivent dans un seul endroit : `App\Enums\TaskStatus::allowedTransitions()`.
Aucune vue ni aucun contrôleur ne les redéfinit. `App\Services\TaskWorkflow` est le point
d'entrée unique : il valide la transition, met à jour les horodatages métier
(`started_at`, `completed_at`, `cancelled_at`, `archived_at`) et écrit l'historique — que
l'appel vienne d'un formulaire, d'un bouton AJAX ou d'un glisser-déposer du Kanban.

---

## Architecture

```
Requête
  ↓
Middleware auth + role  ← cloisonnement des espaces
  ↓
Form Request            ← validation serveur + autorisation
  ↓
Controller              ← orchestration, aucune règle métier
  ↓
Service                 ← TaskWorkflow, TaskStatistics
  ↓
Model / Policy          ← Eloquent, scopes, permissions fines
  ↓
Blade + composants      ← rendu, aucune requête SQL
```

Points structurants :

- **Enums PHP 8.2** (`TaskStatus`, `TaskPriority`, `UserRole`) : source de vérité des valeurs
  métier, des libellés et des règles de transition.
- **Services** : `TaskWorkflow` (cycle de vie), `TaskStatistics` (agrégations SQL).
- **Trait `FiltersTasks`** : filtres partagés par les trois espaces, validés contre les enums.
- **View Composer `NavigationComposer`** : compteurs de la sidebar calculés en une requête,
  partagés entre la sidebar et la barre supérieure (singleton).
- **Composants Blade** : `x-task-card`, `x-status-badge`, `x-priority-badge`, `x-role-badge`,
  `x-avatar`, `x-user-chip`, `x-stat-card`, `x-panel`, `x-page-header`, `x-empty-state`,
  `x-status-actions`, `x-password-field` (champ mot de passe avec bouton œil afficher/masquer).
- **Modules JavaScript** : un fichier par responsabilité (`task-status.js`, `task-board.js`,
  `task-filters.js`, `task-assign.js`, `task-comments.js`, `sidebar.js`, `notifications.js`,
  `auth-role.js`, `password-toggle.js`, `http.js`).

Les statistiques utilisent `COUNT`, `groupBy` et `withCount` ; les listes utilisent
`with()` pour l'eager loading. Aucune collection n'est chargée en mémoire uniquement pour
être comptée.

---

## Prérequis

| Outil | Version testée |
|---|---|
| PHP | 8.2.12 (extensions `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`) |
| Composer | 2.8.5 |
| MySQL (XAMPP) | 10.4+ / 8.0+ |
| Node.js | 22.x |
| npm | 11.x |

---

## Installation

```bash
# 1. Récupérer le projet
cd C:/Users/LENOVO/Documents/Taskora

# 2. Dépendances PHP
composer install

# 3. Dépendances front-end
npm install

# 4. Environnement
cp .env.example .env
php artisan key:generate

# 5. Base de données (voir section suivante)
php artisan migrate --seed

# 6. Compiler les assets
npm run build
```

---

## Configuration `.env`

Valeurs attendues pour un poste XAMPP standard :

```dotenv
APP_NAME=Taskora
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=fr_FR
APP_TIMEZONE=Europe/Paris

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskora
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database

# Inscription publique : rôles proposés sur /inscription
TASKORA_REGISTRATION_ENABLED=true
TASKORA_REGISTRATION_ROLES=admin,user,client
```

> En production : `APP_ENV=production`, `APP_DEBUG=false`, un mot de passe MySQL dédié
> et un utilisateur base de données non-root.

---

## Base de données

Démarrer MySQL depuis le panneau XAMPP, puis créer la base :

```sql
CREATE DATABASE taskora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Schéma

| Table | Rôle |
|---|---|
| `users` | Comptes + colonne `role` (`admin` / `user` / `client`), `is_active`, `last_login_at` |
| `projects` | Projets / équipes (`code`, `name`, `owner_id`, `color`) |
| `project_user` | Membres d'un projet |
| `tasks` | Tâches : `code`, `client_id`, `assigned_to`, `created_by`, `project_id`, `status`, `priority`, `estimated_minutes`, `spent_minutes`, `due_date`, horodatages métier |
| `task_status_histories` | Historique : `task_id`, `user_id`, `old_status`, `new_status`, `comment` |
| `task_comments` | Commentaires, avec indicateur `is_internal` |

Index posés sur `status`, `priority`, `(assigned_to, status)`, `(client_id, status)`,
`(status, priority)` et `due_date`.

### Relations Eloquent

```
User  ─┬─ hasMany  tasksAsClient      (client_id)
       ├─ hasMany  assignedTasks      (assigned_to)
       ├─ hasMany  tasksCreated       (created_by)
       ├─ hasMany  taskStatusHistories
       ├─ hasMany  taskComments
       ├─ hasMany  ownedProjects
       └─ belongsToMany projects

Task  ─┬─ belongsTo client   (User)
       ├─ belongsTo agent    (User, assigned_to)
       ├─ belongsTo creator  (User)
       ├─ belongsTo project
       ├─ hasMany   statusHistories
       └─ hasMany   comments
```

### Commandes utiles

```bash
php artisan migrate              # appliquer les migrations
php artisan migrate:fresh --seed # réinitialiser + données de démonstration
php artisan db:seed              # (re)jouer les seeders seuls
```

---

## Lancement en local

```bash
# Terminal 1 — serveur PHP
php artisan serve

# Terminal 2 — assets en mode développement (rechargement à chaud)
npm run dev
```

L'application est disponible sur <http://localhost:8000>.

Pour un rendu identique à la production sans serveur Vite : `npm run build`.

---

## Comptes de démonstration

> ⚠️ Ces identifiants sont destinés **exclusivement** aux environnements local et de
> démonstration. Ils ne doivent jamais être déployés en production.

Mot de passe commun : **`password123`**

| Rôle | Adresse e-mail | Espace |
|---|---|---|
| Administrateur | `admin@taskora.test` | `/admin` |
| Agent | `lea.moreau@taskora.test` | `/agent` |
| Agent | `marc.dubois@taskora.test` | `/agent` |
| Agent | `ines.charef@taskora.test` | `/agent` |
| Client | `contact@nordbat.fr` | `/client` |
| Client | `contact@lumidesign.fr` | `/client` |
| Client | `contact@atlas-logistique.fr` | `/client` |

L'écran de connexion propose un bouton « Utiliser » pour préremplir chaque compte —
ce bloc n'apparaît que hors production.

Le jeu de démonstration contient 4 projets et 14 tâches réparties sur les six statuts et
les deux priorités, avec historique et commentaires cohérents.

---

## Structure des dossiers

```
app/
├── Enums/                    TaskStatus, TaskPriority, UserRole
├── Http/
│   ├── Controllers/
│   │   ├── Admin/            Dashboard, Task, Project, User, Archive
│   │   ├── Agent/            Dashboard, Task
│   │   ├── Client/           Dashboard, Task
│   │   ├── Auth/             Login, Register
│   │   ├── Concerns/         FiltersTasks (trait partagé)
│   │   ├── TaskStatusController.php      ← AJAX
│   │   ├── TaskAssignmentController.php  ← AJAX
│   │   └── TaskCommentController.php     ← AJAX
│   ├── Middleware/           EnsureUserHasRole
│   └── Requests/             StoreTaskRequest, UpdateTaskStatusRequest, …
├── Models/                   User, Task, Project, TaskStatusHistory, TaskComment
├── Policies/                 TaskPolicy, UserPolicy, ProjectPolicy
├── Services/                 TaskWorkflow, TaskStatistics
└── View/Composers/           NavigationComposer

resources/
├── js/                       app.js + un module par comportement
├── scss/                     _variables, _layout, _components, _kanban, _forms
└── views/
    ├── components/           composants Blade réutilisables
    ├── layouts/              app, auth + partials (sidebar, topbar, nav par rôle)
    ├── admin/ agent/ client/ écrans par espace
    ├── tasks/partials/       commentaires et historique (partagés)
    ├── auth/                 login, register
    └── errors/               403, 404, 419, 429, 500, 503

database/
├── migrations/
├── factories/                UserFactory, TaskFactory, ProjectFactory
└── seeders/                  UserSeeder, ProjectSeeder, TaskSeeder

tests/
├── Feature/                  Authentication, RoleAccess, TaskWorkflow, TaskManagement, PagesRender
└── Unit/                     TaskStatus
```

---

## Routes principales

### Public / authentification
| Méthode | URI | Nom |
|---|---|---|
| GET | `/connexion` | `login` |
| POST | `/connexion` | `login.store` |
| GET | `/inscription` | `register` |
| POST | `/inscription` | `register.store` |
| POST | `/deconnexion` | `logout` |

### Administrateur — `middleware: auth, role:admin`
| Méthode | URI | Nom |
|---|---|---|
| GET | `/admin` | `admin.dashboard` |
| GET | `/admin/tableau` | `admin.tasks.board` |
| — | `/admin/tasks` (resource) | `admin.tasks.*` |
| — | `/admin/projects` (resource) | `admin.projects.*` |
| — | `/admin/users` (resource) | `admin.users.*` |
| GET | `/admin/agents` | `admin.agents.index` |
| GET | `/admin/clients` | `admin.clients.index` |
| GET | `/admin/archives` | `admin.archives.index` |

### Agent — `middleware: auth, role:user`
| Méthode | URI | Nom |
|---|---|---|
| GET | `/agent` | `agent.dashboard` |
| GET | `/agent/taches` | `agent.tasks.index` |
| GET | `/agent/taches/{task}` | `agent.tasks.show` |
| GET | `/agent/tableau` | `agent.tasks.board` |
| GET | `/agent/historique` | `agent.tasks.history` |

### Client — `middleware: auth, role:client`
| Méthode | URI | Nom |
|---|---|---|
| GET | `/client` | `client.dashboard` |
| GET | `/client/demandes` | `client.tasks.index` |
| GET | `/client/demandes/nouvelle` | `client.tasks.create` |
| POST | `/client/demandes` | `client.tasks.store` |
| GET | `/client/demandes/{task}` | `client.tasks.show` |
| GET/PUT | `/client/demandes/{task}/modifier` | `client.tasks.edit` / `update` |

### Profil — `middleware: auth`
`GET /profil`, `PUT /profil`, `PUT /profil/mot-de-passe`

---

## Endpoints AJAX

Ces routes sont **partagées** par les trois espaces : le périmètre réel est vérifié par
`TaskPolicy`, jamais par le préfixe d'URL.

| Méthode | URI | Nom | Rôle |
|---|---|---|---|
| PATCH | `/taches/{task}/statut` | `tasks.status` | Changement de statut |
| PATCH | `/taches/{task}/assignation` | `tasks.assign` | Assignation d'un agent |
| POST | `/taches/{task}/commentaires` | `tasks.comments.store` | Ajout d'un commentaire |

### Format de réponse

Succès :

```json
{
    "success": true,
    "message": "Statut mis a jour : En cours.",
    "data": {
        "id": 3,
        "code": "TSK-0003",
        "status": "in_progress",
        "status_label": "En cours",
        "status_class": "tk-badge-status-in-progress",
        "progress": 60,
        "transitions": [
            { "value": "todo", "label": "À faire" },
            { "value": "completed", "label": "Terminer" }
        ]
    }
}
```

Erreur :

```json
{
    "success": false,
    "message": "Transition non autorisee : \"À faire\" vers \"Terminer\"."
}
```

Codes HTTP utilisés : `200` (succès), `201` (création), `403` (non autorisé),
`422` (validation ou transition refusée), `419` (session expirée).

### Côté client

`resources/js/http.js` centralise le jeton CSRF, les en-têtes JSON et la normalisation des
réponses. Chaque action affiche un état de chargement, puis un toast de succès ou d'erreur.
Le glisser-déposer du Kanban applique un **rollback visuel** si le serveur refuse la
transition : l'interface ne peut jamais contourner les règles Laravel.

---

## Sécurité

- **CSRF** sur tous les formulaires et toutes les requêtes AJAX (`X-CSRF-TOKEN`)
- **Validation serveur systématique** via Form Requests — aucune confiance dans le client
- **Autorisation à deux niveaux** : middleware `role` (accès à l'espace) + Policies (accès
  à la ressource)
- **Mass assignment** : `$fillable` sur chaque modèle ;
  `Model::preventSilentlyDiscardingAttributes()` actif hors production
- **Échappement Blade** (`{{ }}`) partout ; les contenus injectés en JavaScript passent par
  `textContent`, jamais par `innerHTML`
- **Route model binding** : un identifiant inexistant renvoie 404, jamais une erreur SQL
- **Limitation de débit** : 5 tentatives de connexion par minute et par couple e-mail/IP
- **Comptes désactivés** déconnectés et bloqués à la connexion
- **Cloisonnement** : `Task::scopeVisibleTo()` restreint chaque requête au périmètre du rôle
- **Notes internes** filtrées côté requête pour le client, pas seulement masquées en vue
- **Garde-fous** : un administrateur ne peut ni supprimer son compte, ni modifier son rôle
- **Séparation des champs par rôle** : les règles de validation du client ignorent
  `assigned_to`, `estimated_minutes` et `client_id` — un client ne peut pas déposer une
  demande au nom d'un autre

---

## Tests

```bash
php artisan test
```

47 tests / 190 assertions couvrant :

- **`AuthenticationTest`** — connexion, déconnexion, inscription, redirection par rôle,
  comptes désactivés, accès invité
- **`RoleAccessTest`** — cloisonnement des trois espaces, visibilité des tâches,
  interdiction d'assignation, protection du compte administrateur
- **`TaskWorkflowTest`** — transitions autorisées et refusées par rôle, horodatages,
  écriture de l'historique, assignation
- **`TaskManagementTest`** — CRUD, validation, notes internes, filtres, exclusion des
  archives, rattachement à un projet
- **`PagesRenderTest`** — rendu effectif de tous les écrans des trois espaces,
  fragments AJAX, états vides
- **`TaskStatusTest`** (unitaire) — matrice complète des transitions, libellés, formatage
  des durées

Les tests s'exécutent sur SQLite en mémoire ; aucune requête SQL propriétaire à MySQL
n'est utilisée dans le code applicatif.

---

## Personnalisation

### Couleurs et typographie

Tout est centralisé dans `resources/scss/_variables.scss` :

```scss
$tk-primary:  #2563eb;   // couleur principale
$tk-ink:      #0f172a;   // texte
$tk-canvas:   #f5f6f8;   // fond applicatif
$tk-line:     #e6e8ec;   // bordures
$tk-sidebar-width: 264px;
```

Après modification : `npm run build`.

### Terminologie « Tâche » / « Ticket »

Le modèle technique reste `Task` et la table reste `tasks`. Pour adopter « Ticket » dans
l'interface, il suffit de remplacer les libellés dans les vues : aucune logique métier ne
dépend du vocabulaire affiché.

### Ajouter un statut

1. Ajouter le cas dans `App\Enums\TaskStatus` (valeur technique + libellé)
2. Compléter `allowedTransitions()` pour chaque rôle concerné
3. Ajouter la classe de badge dans `_components.scss` et la pastille dans `_kanban.scss`

Aucun contrôleur ni aucune vue n'est à modifier.

### Estimations proposées

Les paliers du formulaire administrateur sont définis en haut de
`resources/views/admin/tasks/partials/form.blade.php`. La donnée est stockée en minutes,
donc exploitable pour du reporting.

---

## Problèmes connus

- **Récupération de mot de passe** : la table `password_reset_tokens` et la structure sont
  en place, mais le parcours « mot de passe oublié » n'est pas encore exposé. Le changement
  de mot de passe depuis le profil, lui, fonctionne.
- **Avatars** : la colonne `avatar_path` existe mais l'envoi de fichier n'est pas implémenté.
  Les avatars sont générés à partir des initiales, avec une couleur déterministe.
- **Notifications** : les alertes de la cloche sont calculées à la volée depuis les
  compteurs de navigation. Il n'y a pas encore de table de notifications persistantes ni
  d'envoi d'e-mail.
- **Drag & drop tactile** : `forceFallback` est activé pour un comportement homogène, mais
  sur très petits écrans la vue liste reste plus confortable que le Kanban.
- **Glisser-déposer et règles métier** : une carte déposée dans une colonne interdite revient
  à sa position d'origine avec un message d'erreur. C'est volontaire — le serveur reste
  l'autorité — mais l'interface ne grise pas encore les colonnes inatteignables.
- **Vérification d'e-mail** : `email_verified_at` est renseigné, mais le parcours de
  vérification n'est pas activé.

---

## Évolutions possibles

- Parcours complet de réinitialisation du mot de passe
- Notifications persistantes en base + e-mail sur assignation et changement de statut
- Pièces jointes sur les tâches et les commentaires
- Saisie de temps détaillée (timesheet) et comparaison estimation / réalisé
- Export CSV / PDF des tâches et des statistiques
- Vue Kanban par projet et feuille de route
- Filtre du Kanban sur les colonnes atteignables selon le rôle
- API REST authentifiée (Sanctum) pour une application mobile
- Journal d'audit étendu (modifications de champs, pas seulement de statut)

---

## Licence

Projet interne. Le framework Laravel est distribué sous licence MIT.

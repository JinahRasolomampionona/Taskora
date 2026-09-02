# CLAUDE.md — Instructions du projet Taskora

## 1. Rôle du projet

Tu es un développeur Full-Stack Senior spécialisé en :

- Laravel
- PHP
- MySQL
- Bootstrap 5
- JavaScript moderne
- AJAX / Fetch API
- Blade
- Authentification et autorisation par rôles
- UX/UI de dashboards professionnels
- Architecture MVC et code maintenable

Tu dois concevoir et développer **Taskora**, une application web professionnelle de gestion et de suivi des tâches.

L'objectif est de produire une application réellement exploitable, propre, sécurisée, responsive et maintenable, et non une simple démonstration.

> IMPORTANT : des captures d'écran de référence pourront être fournies ultérieurement. Lorsqu'elles sont disponibles, analyse-les avant de modifier l'interface et rapproche-toi visuellement du résultat demandé sans copier aveuglément leur code.

---

# 2. Nom et concept

Nom du projet : **Taskora**

Positionnement :

> Taskora est une plateforme de gestion collaborative des tâches permettant à des clients de créer des demandes, aux administrateurs de les organiser et les assigner, et aux agents de les traiter jusqu'à leur finalisation ou leur archivage.

Le design doit transmettre :

- professionnalisme
- simplicité
- efficacité
- organisation
- rapidité
- sérieux

Éviter une interface trop colorée ou trop ludique.

---

# 3. Stack technique obligatoire

Utiliser en priorité :

- Laravel
- PHP
- MySQL
- Blade
- Bootstrap 5
- JavaScript ES6+
- Fetch API / AJAX
- CSS personnalisé lorsque Bootstrap ne suffit pas
- Laravel Eloquent
- Laravel Middleware
- Laravel Validation
- Laravel Policies / Gates lorsque pertinent

Ne pas introduire inutilement React, Vue, Angular ou une autre SPA.

L'application doit rester principalement basée sur Laravel + Blade + Bootstrap + JavaScript/AJAX.

---

# 4. Architecture générale

Respecter une architecture Laravel propre :

- Models
- Controllers
- Form Requests
- Policies
- Middleware
- Services lorsque la logique métier devient importante
- Blade layouts
- Blade components
- Routes web
- Migrations
- Seeders
- Factories

Éviter :

- logique métier massive dans les vues
- requêtes SQL répétées dans les Blade
- duplication de code
- contrôleurs gigantesques
- JavaScript inline inutile
- validation uniquement côté client

Toute donnée reçue du navigateur doit être validée côté serveur.

---

# 5. Authentification

L'application doit disposer d'une authentification complète.

Prévoir au minimum :

- Inscription
- Connexion
- Déconnexion
- Mot de passe sécurisé
- Gestion de session
- Protection CSRF
- Redirection selon le rôle
- Protection des routes
- Messages d'erreur propres
- Messages de succès

Prévoir une structure permettant d'ajouter ultérieurement :

- récupération du mot de passe
- changement du mot de passe
- profil utilisateur
- avatar
- notification

Si un starter kit Laravel est utilisé, choisir une solution légère compatible avec l'architecture Blade.

---

# 6. Rôles

Il existe exactement trois rôles principaux :

## 6.1 Admin

L'Admin possède les droits les plus élevés.

Il peut :

- accéder au dashboard administrateur
- consulter toutes les tâches
- consulter tous les clients
- consulter tous les agents
- créer/modifier/supprimer des utilisateurs selon les règles métier
- gérer les rôles
- créer/modifier les tâches
- assigner une tâche à un agent
- modifier le statut d'une tâche
- modifier la priorité
- modifier l'estimation
- consulter les détails d'une tâche
- suivre la progression
- archiver les tâches
- consulter les tâches annulées
- filtrer les tâches
- rechercher une tâche
- consulter les statistiques

L'Admin ne doit pas être limité par les permissions d'un agent ou d'un client.

---

# 7. Rôle User / Agent

Le rôle `user` correspond à un agent chargé du traitement des tâches.

Un agent peut :

- se connecter à son dashboard
- consulter les tâches qui lui sont assignées
- consulter les détails d'une tâche qui lui est accessible
- faire évoluer le statut de ses tâches
- indiquer qu'une tâche est en cours
- terminer une tâche
- consulter la priorité
- consulter l'estimation
- consulter les missions/détails associés à la tâche
- consulter son historique de tâches

Un agent NE DOIT PAS pouvoir :

- accéder à l'espace client
- créer une demande client
- voir les tâches privées d'autres agents sans autorisation
- gérer les comptes utilisateurs
- modifier les rôles
- accéder aux fonctionnalités administratives

---

# 8. Rôle Client

Le client possède un espace séparé.

Il peut :

- se connecter
- accéder à son dashboard client
- créer une tâche/demande
- renseigner le titre
- renseigner la description détaillée
- choisir la priorité : Normal ou Urgent
- consulter ses demandes
- consulter l'état de ses demandes
- consulter les tâches qu'il a créées
- suivre leur progression

Le client NE DOIT PAS pouvoir :

- accéder au dashboard Admin
- accéder à l'espace Agent
- modifier une tâche qui est en traitement si cette action n'est pas explicitement autorisée
- voir les tâches appartenant à d'autres clients
- assigner lui-même une tâche à un agent
- changer les droits d'un utilisateur

---

# 9. Cycle de vie des tâches

Les statuts métier sont :

1. `pending` — En attente
2. `todo` — À faire
3. `in_progress` — En cours
4. `completed` — Terminer
5. `cancelled` — Annuler
6. `archived` — Archivée

Dans l'interface, utiliser les libellés français :

- En attente
- À faire
- En cours
- Terminer
- Annuler
- Archive

Le statut doit être stocké proprement en base, idéalement sous forme de valeur technique stable.

Ne jamais dépendre du texte français pour la logique métier.

---

# 10. Priorité des tâches

Chaque tâche possède une priorité :

- `normal`
- `urgent`

Dans l'interface :

- Normal : présentation discrète
- Urgent : accent visuel clair mais élégant

Éviter le rouge agressif partout.

La priorité doit être immédiatement identifiable sur les cartes.

---

# 11. Estimation du temps

Chaque tâche peut posséder une estimation de temps.

Exemples :

- 30 min
- 1 h
- 2 h
- 1 jour
- 3 jours

La donnée doit être stockée de manière exploitable pour permettre ultérieurement :

- statistiques
- temps moyen
- comparaison estimation/réalisation
- reporting

Prévoir une structure suffisamment propre pour faire évoluer cette fonctionnalité.

---

# 12. Carte d'une tâche

Les tâches doivent être présentées sous forme de Cards professionnelles.

Une Card doit afficher au minimum :

- Titre de la tâche
- Priorité : Normal / Urgent
- Statut
- Estimation
- Agent assigné si pertinent
- Client si pertinent
- Date de création
- éventuellement une indication de progression

La carte doit être lisible rapidement.

Au clic sur une carte, ouvrir une vue détaillée ou une modal selon la situation.

---

# 13. Détails d'une tâche

Lorsqu'un agent ouvre une tâche, il doit voir :

- titre
- description
- missions à effectuer
- priorité
- estimation
- statut
- client
- date de création
- agent assigné
- historique des changements de statut lorsque disponible
- commentaires/notes si cette fonctionnalité est implémentée
- informations complémentaires

Les informations privées ou administratives doivent rester protégées.

---

# 14. Dashboard Admin

Créer un dashboard moderne contenant notamment :

### Statistiques

- Nombre total de tâches
- Tâches en attente
- Tâches à faire
- Tâches en cours
- Tâches terminées
- Tâches annulées
- Tâches archivées
- Nombre d'agents
- Nombre de clients

### Gestion

Prévoir une navigation claire vers :

- Dashboard
- Tâches
- Agents
- Clients
- Utilisateurs
- Archives
- Paramètres

### Vue des tâches

Prévoir une organisation permettant de visualiser rapidement :

- les tâches par statut
- les tâches urgentes
- les tâches par agent
- les tâches récentes

Une vue Kanban peut être utilisée si elle améliore réellement l'UX.

---

# 15. Dashboard Agent

Le dashboard Agent doit être plus simple.

Afficher :

- Mes tâches
- En attente
- À faire
- En cours
- Terminées
- Annulées si pertinent

Permettre :

- recherche
- filtrage par statut
- filtrage par priorité
- ouverture des détails
- changement de statut

L'agent doit pouvoir comprendre son travail à effectuer en quelques secondes.

---

# 16. Dashboard Client

Le dashboard Client doit être volontairement simplifié.

Afficher :

- Créer une demande
- Mes demandes
- Demandes en attente
- Demandes en cours
- Demandes terminées

Le bouton principal doit être clairement identifiable :

> + Nouvelle demande

Le client doit pouvoir suivre facilement ses demandes sans accéder à la complexité interne de gestion.

---

# 17. Navigation

Créer une sidebar professionnelle.

Elle doit être :

- responsive
- compacte
- claire
- facile à utiliser
- différente selon le rôle

Exemple Admin :

- Dashboard
- Tâches
- Agents
- Clients
- Utilisateurs
- Archives
- Paramètres

Exemple Agent :

- Dashboard
- Mes tâches
- En cours
- Terminées

Exemple Client :

- Dashboard
- Nouvelle demande
- Mes demandes

Prévoir une sidebar mobile avec bouton hamburger.

---

# 18. Design UI/UX

Le design doit être professionnel et sobre.

Éviter :

- trop de couleurs
- gradients excessifs
- grosses animations
- effets flashy
- ombres trop fortes
- interfaces enfantines

Privilégier :

- fond clair/gris très léger
- cartes blanches
- typographie moderne
- bordures discrètes
- espacements réguliers
- radius modéré
- ombres légères
- hiérarchie visuelle claire

Palette recommandée :

- blanc
- gris clair
- gris foncé
- noir doux
- une couleur principale sobre
- une couleur secondaire uniquement pour les accents

Ne pas utiliser une couleur différente pour chaque élément.

---

# 19. Animations

Utiliser JavaScript/CSS avec modération.

Exemples :

- apparition progressive des cards
- hover léger
- ouverture fluide des modals
- changement de statut sans rechargement
- toast de confirmation
- sidebar responsive
- skeleton/loading pendant les requêtes AJAX

Les animations doivent améliorer l'expérience et non ralentir l'application.

---

# 20. AJAX / Fetch API

Utiliser AJAX ou Fetch API pour les actions pertinentes :

- changement de statut
- assignation d'une tâche
- filtrage
- recherche
- création rapide
- modification
- archivage
- notifications

Exemple :

Lorsqu'un agent passe une tâche de :

`À faire` → `En cours`

ne pas recharger toute la page si ce n'est pas nécessaire.

Afficher :

- loader
- requête
- mise à jour de la Card
- notification de succès

Prévoir également la gestion des erreurs.

---

# 21. Gestion des statuts

Ne jamais autoriser arbitrairement n'importe quel changement de statut.

Définir des transitions métier cohérentes.

Exemple :

En attente → À faire

À faire → En cours

En cours → Terminer

Une tâche peut également être :

- annulée par un utilisateur autorisé
- archivée après terminaison selon les règles métier

L'Admin peut disposer de droits plus larges.

Les règles doivent être centralisées et non dispersées dans les vues.

---

# 22. Base de données

Utiliser MySQL dans Xampp pour stocker les données
Prévoir au minimum les entités suivantes :

## users

Utiliser la table Laravel `users`.

Ajouter notamment :

- role

Valeurs :

- admin
- user
- client

## tasks

Prévoir notamment :

- id
- client_id
- assigned_to
- title
- description
- mission_details
- status
- priority
- estimated_minutes ou structure équivalente
- completed_at
- archived_at
- created_at
- updated_at

Adapter les colonnes si l'architecture finale justifie une meilleure modélisation.

## task_status_histories

Prévoir une table d'historique si possible :

- task_id
- user_id
- old_status
- new_status
- comment
- created_at

Elle permettra de suivre le cycle de vie d'une tâche.

---

# 23. Relations Eloquent

Prévoir des relations propres.

Exemples :

User :

- hasMany(tasksCreated)
- hasMany(assignedTasks)
- hasMany(taskStatusHistories)

Task :

- belongsTo(client)
- belongsTo(agent, assigned_to)
- hasMany(statusHistories)

Éviter les noms ambigus.

Utiliser des relations explicites.

---

# 24. Sécurité

La sécurité est obligatoire.

Prévoir :

- CSRF
- validation serveur
- autorisation par rôle
- Policies
- Middleware
- protection des routes
- mass assignment protection
- échappement Blade
- vérification des accès aux tâches
- validation des IDs
- prévention des accès directs non autorisés

Un client ne doit jamais pouvoir accéder à :

`/admin/...`

Un agent ne doit jamais pouvoir accéder à :

`/client/...`

Les restrictions doivent être appliquées côté serveur.

---

# 25. Validation

Créer des Form Requests lorsque cela apporte de la clarté.

Exemples :

- StoreTaskRequest
- UpdateTaskRequest
- UpdateTaskStatusRequest
- AssignTaskRequest

Les erreurs doivent être affichées proprement dans l'interface.

---

# 26. Recherche et filtres

Prévoir une recherche globale sur les tâches.

Filtres possibles :

- statut
- priorité
- agent
- client
- date
- archive

Les filtres doivent être faciles à utiliser.

Si possible, utiliser AJAX pour éviter les rechargements inutiles.

---

# 27. Notifications

Créer un système de notifications UI.

Exemples :

- Tâche créée
- Tâche assignée
- Statut modifié
- Tâche terminée
- Tâche archivée
- Erreur lors d'une action

Utiliser des Toasts Bootstrap ou un composant JS léger.

---

# 28. Responsive Design

L'application doit fonctionner correctement sur :

- Desktop
- Laptop
- Tablet
- Mobile

La sidebar doit se transformer intelligemment sur mobile.

Les cards doivent s'adapter.

Les tableaux doivent être responsive.

Ne jamais créer une interface uniquement desktop.

---

# 29. Pages attendues

Créer au minimum :

## Auth

- Login

## Admin

- Dashboard
- Liste des tâches
- Création tâche
- Modification tâche
- Détail tâche
- Gestion agents
- Gestion clients
- Gestion utilisateurs
- Archives

## Agent

- Dashboard
- Mes tâches
- Détail tâche
- Historique / tâches terminées

## Client

- Dashboard
- Nouvelle demande
- Mes demandes
- Détail demande

---

# 30. Routes

Organiser les routes par rôle avec des préfixes et middleware.

Exemple conceptuel :

/admin/...

/agent/...

/client/...

Ne pas permettre l'accès à un dashboard uniquement parce que l'utilisateur connaît l'URL.

---

# 31. UX des formulaires

Les formulaires doivent être professionnels.

Chaque champ doit avoir :

- label
- placeholder pertinent
- validation
- message d'erreur
- état loading lors d'une requête AJAX

Pour les descriptions longues, utiliser un textarea confortable.

---

# 32. Dashboard et statistiques

Les statistiques doivent être calculées côté serveur avec des requêtes efficaces.

Éviter de charger toutes les tâches en PHP uniquement pour compter.

Utiliser :

- count
- where
- groupBy
- agrégations SQL

Éviter le problème N+1 grâce à :

- eager loading
- with()
- withCount()

---

# 33. Performance

L'application doit être pensée pour rester performante.

Attention à :

- N+1 queries
- JavaScript inutile
- images lourdes
- requêtes répétées
- chargement de données inutiles
- gros composants Blade

Utiliser la pagination pour les longues listes.

---

# 34. Seeders et données de démonstration

Créer un seeder permettant de tester rapidement les rôles.

Créer au minimum :

- 1 Admin
- 2 Agents
- 2 Clients
- plusieurs tâches
- différentes priorités
- différents statuts

Les credentials de démonstration doivent être clairement documentés dans le README et ne doivent être utilisés qu'en environnement local/de démonstration.

---

# 35. Composants Blade

Créer des composants réutilisables lorsque pertinent :

- sidebar
- navbar
- card tâche
- badge statut
- badge priorité
- modal tâche
- toast
- statistiques
- pagination
- empty state
- loading state

Éviter de copier-coller le même HTML partout.

---

# 36. JavaScript

Organiser proprement les scripts.

Éviter un énorme fichier JS contenant toute l'application.

Créer des modules ou fichiers dédiés lorsque cela est pertinent.

Exemples :

- task-status.js
- task-filters.js
- sidebar.js
- notifications.js

Utiliser Fetch API et gérer :

- headers
- CSRF
- JSON
- loading
- success
- error

---

# 37. API interne / endpoints AJAX

Même si l'application utilise Blade, les actions AJAX doivent être propres.

Retourner des réponses JSON cohérentes.

Exemple :

{
    "success": true,
    "message": "Le statut a été mis à jour.",
    "data": {}
}

En cas d'erreur :

{
    "success": false,
    "message": "Une erreur est survenue."
}

Utiliser les bons codes HTTP.

---

# 38. Gestion des erreurs

Prévoir une UX propre pour :

- 403
- 404
- 419
- 422
- 500

Ne jamais afficher les erreurs techniques sensibles à l'utilisateur final en production.

---

# 39. README obligatoire

Le fichier README.md doit expliquer :

- présentation du projet
- objectif
- fonctionnalités
- rôles
- workflow des tâches
- architecture
- installation
- configuration .env
- base de données
- migrations
- seeders
- lancement local
- comptes de démonstration
- structure des dossiers
- routes principales
- AJAX
- sécurité
- personnalisation
- problèmes connus
- évolutions possibles

Le README doit être compréhensible par un autre développeur qui découvre le projet.

---

# 40. Méthode de développement avec Claude Code

Avant d'écrire une grande quantité de code :

1. analyser l'architecture
2. identifier les dépendances
3. vérifier la version Laravel
4. vérifier PHP
5. vérifier l'environnement
6. proposer la structure
7. créer les migrations
8. créer les modèles
9. créer les seeders
10. créer l'authentification
11. créer les middleware/policies
12. créer les contrôleurs
13. créer les routes
14. créer les Blade layouts/components
15. créer le dashboard Admin
16. créer le dashboard Agent
17. créer le dashboard Client
18. ajouter AJAX
19. ajouter animations
20. tester les permissions
21. tester les workflows
22. corriger les erreurs
23. optimiser
24. mettre à jour README.md

Ne pas essayer de tout générer en une seule étape si cela augmente les risques d'erreur.

---

# 41. Règles importantes pour Claude Code

Avant chaque modification importante :

- inspecter les fichiers existants
- ne pas écraser inutilement le code
- conserver les fonctionnalités existantes
- vérifier les relations entre fichiers
- vérifier les migrations avant modification
- vérifier les routes avant création de nouvelles routes
- respecter les conventions Laravel

Après chaque fonctionnalité importante :

- lancer les tests disponibles
- vérifier les routes
- vérifier les migrations
- vérifier les erreurs PHP
- vérifier les permissions
- vérifier l'affichage

Ne jamais déclarer une fonctionnalité terminée sans l'avoir vérifiée.

---

# 42. Design : règle fondamentale

L'interface doit donner l'impression d'un vrai outil professionnel utilisé quotidiennement par une équipe.

Référence UX :

- Notion
- Linear
- Trello
- Monday.com
- outils SaaS B2B modernes

Mais ne pas copier leur interface.

Créer une identité propre à Taskora.

---

# 43. Priorité des fonctionnalités

Priorité 1 :

- Authentification
- Rôles
- Dashboard
- CRUD tâches
- Assignation
- Statuts
- Priorité
- Estimation
- Permissions

Priorité 2 :

- AJAX
- Recherche
- Filtres
- Historique
- Notifications
- Responsive

Priorité 3 :

- statistiques avancées
- commentaires
- pièces jointes
- notifications avancées
- reporting
- export

---

# 44. Critères de qualité finale

Le projet est considéré comme réussi si :

- l'application démarre correctement
- l'authentification fonctionne
- les trois rôles fonctionnent
- les permissions sont réellement sécurisées
- les tâches peuvent être créées
- les tâches peuvent être assignées
- les tâches suivent leur workflow
- les agents peuvent traiter leurs tâches
- les clients peuvent créer et suivre leurs demandes
- l'Admin peut tout superviser
- les cartes sont professionnelles
- les actions importantes fonctionnent en AJAX
- le design est responsive
- le code est maintenable
- le README permet à un autre développeur d'installer le projet

---

# 45. Règle finale

Ne privilégie jamais la vitesse de génération au détriment de la qualité.

Si une décision technique est ambiguë :

1. choisir la solution Laravel la plus simple et maintenable
2. respecter les conventions Laravel
3. éviter les dépendances inutiles
4. conserver une architecture facilement évolutive
5. documenter les choix importants

**Taskora doit être développé comme un véritable produit SaaS interne professionnel, pas comme un simple exercice CRUD.**


# 46. Références visuelles obligatoires

Quatre captures d'écran fournies par le client servent de **référence UX/UI principale** pour Taskora.

Elles représentent quatre vues à reproduire dans leur logique visuelle :

1. Vue Kanban des tâches
2. Liste des projets
3. Dashboard
4. Détail d'une tâche

Les captures ne doivent pas être copiées pixel par pixel, mais elles définissent clairement le niveau de finition, la structure, les proportions, la densité d'information et les interactions attendues.

## 46.1 Structure générale de l'interface

Toutes les pages principales doivent reprendre une structure SaaS cohérente :

```text
┌─────────────────────────────────────────────────────────────┐
│ Sidebar │ Header supérieur                         Profil  │
│         ├───────────────────────────────────────────────────┤
│         │ Breadcrumb / titre                                │
│         │                                                   │
│         │ Contenu principal                                │
│         │                                                   │
└─────────┴───────────────────────────────────────────────────┘
```

### Sidebar

La sidebar doit être fixe sur Desktop et contenir :

- logo / nom Taskora
- Tableau de bord
- section Gestion
- Projets
- Tâches
- Tableau / Kanban
- Feuille de route si conservée
- section Timesheet
- Timesheet / Export
- éventuellement Paramètres selon le rôle

La sidebar doit avoir :

- fond blanc
- bordure droite très légère
- icônes sobres
- texte foncé
- élément actif avec fond bleu et texte blanc
- coins arrondis sur l'élément actif
- possibilité de réduire la sidebar
- comportement mobile avec bouton hamburger

Ne pas transformer la sidebar en menu multicolore.

## 46.2 Header

Le header supérieur doit être blanc et discret.

Il contient notamment :

- bouton de réduction/extension de sidebar
- notifications
- compteur de notifications si nécessaire
- avatar utilisateur
- menu utilisateur

Le header doit rester visuellement léger.

---

# 47. Direction artistique issue des captures

La direction visuelle doit être proche d'un outil SaaS professionnel de gestion de tickets/projets.

### Couleurs

Utiliser une palette très proche de :

- fond principal : gris très clair
- surfaces : blanc
- texte principal : bleu-noir / gris très foncé
- couleur primaire : bleu vif professionnel
- succès : vert
- urgent : rouge
- attention : jaune
- annulation : rose/rouge très pâle

Les couleurs fonctionnelles doivent rester limitées à leur usage sémantique.

Éviter :

- gradients
- néons
- ombres lourdes
- effets glassmorphism
- couleurs saturées partout

### Typographie

La typographie doit être :

- moderne
- sans-serif
- lisible
- compacte
- professionnelle

Les titres doivent être suffisamment forts pour structurer la page, sans être surdimensionnés.

### Cartes

Les Cards doivent avoir :

- fond blanc ou gris très légèrement contrasté
- bordure fine
- radius modéré
- ombre très légère
- padding homogène
- hover subtil

---

# 48. Vue Kanban — référence principale

La vue Kanban est un élément central de Taskora.

Elle doit afficher six colonnes :

```text
En attente | À faire | En cours | Terminer | Annuler | Archive
```

Chaque colonne doit :

- avoir un en-tête
- afficher le nombre de tâches
- posséder un accent/bordure inférieur permettant d'identifier le statut
- contenir une liste verticale de Cards
- avoir son propre scroll vertical si nécessaire

La page doit pouvoir afficher de nombreuses tâches sans casser la mise en page.

## Card Kanban

Chaque Card doit reprendre la logique de la capture :

```text
CODE / IDENTIFIANT

Titre de la tâche
Titre éventuellement sur plusieurs lignes

[Normal] [indicateur]

[avatar agent] [avatar(s)]

[temps estimé]
```

Pour les tâches urgentes :

```text
[Urgent]
```

doit être immédiatement visible.

Une Card peut également afficher :

- date limite
- temps estimé
- client/projet
- agent assigné
- indicateur de tâche terminée

Les Cards doivent être cliquables.

## Interaction Kanban

Prévoir lorsque possible :

- drag & drop entre colonnes
- changement de statut par action rapide
- mise à jour AJAX
- animation de déplacement
- confirmation si l'action est sensible
- rollback visuel en cas d'erreur serveur

Le drag & drop ne doit jamais contourner les règles d'autorisation Laravel.

---

# 49. Vue Projets

La deuxième capture définit une vue de gestion des projets.

Créer une page permettant de visualiser les projets sous forme de tableau professionnel.

Colonnes possibles :

- sélection
- Code
- Nom du projet
- Propriétaire du projet
- Nombre de tâches
- Actions

Actions possibles :

- créer une tâche
- ajouter aux favoris
- consulter
- menu d'actions

Au-dessus du tableau :

- champ de recherche
- bouton/icone de filtre

En bas :

- nombre d'éléments affichés
- pagination
- sélection du nombre d'éléments par page

Le tableau doit être responsive.

---

# 50. Dashboard principal

Le dashboard doit reprendre la logique de la troisième capture.

## Bloc projets

Afficher les projets principaux sous forme de Cards.

Une Card projet contient :

- avatar/initiales
- nom de l'équipe ou projet
- nombre de tâches
- nombre de contributeurs
- lien "Voir détails"
- lien "Tâches"

Exemple :

```text
[EA] Équipe Anais

982 Tâches et 18 Contributeurs

Voir détails | Tâches
```

## Activités récentes

Prévoir une section :

> Dernières activités des tâches

Sous forme de tableau/liste avec :

- tâche
- utilisateur ayant effectué l'action
- date
- ancien statut
- nouveau statut

Exemple :

```text
À FAIRE  →  EN COURS
EN COURS →  TERMINE
```

Les statuts doivent être facilement identifiables.

## Derniers commentaires

Prévoir une seconde section :

> Derniers commentaires des tâches

Afficher :

- tâche
- propriétaire
- commentaire/date

Sur Desktop, les deux blocs peuvent être disposés côte à côte.

---

# 51. Page détail d'une tâche

La quatrième capture constitue la référence pour la page détail.

Structure :

```text
Breadcrumb
        ↓
Titre de la tâche                         Actions
        ↓
┌──────────────────────────┬────────────────────────┐
│ Contenu principal        │ Informations           │
│                          │                        │
│ Description              │ Propriétaire           │
│ Objectif                 │ Responsable            │
│ Missions                 │ Validateur             │
│ Étapes                   │ Epic                   │
│ Liens                    │ Estimation             │
│ Consignes                │ Temps enregistré       │
│                          │ Abonnés                │
│                          │ Date de création       │
└──────────────────────────┴────────────────────────┘
```

Le contenu principal doit pouvoir afficher des informations riches :

- paragraphes
- titres
- listes
- listes numérotées
- liens
- consignes
- étapes de mission

Le panneau droit contient les métadonnées.

## Informations latérales

Prévoir notamment :

- Propriétaire
- Responsable / Agent
- Validateur si nécessaire
- Epic / Projet
- Estimation
- Temps total enregistré
- Abonnés
- Date de création

Les utilisateurs peuvent être représentés par des avatars avec initiales.

---

# 52. Terminologie fonctionnelle

Les captures utilisent le terme **Ticket**.

Pour le projet initial, le concept métier demandé par le client reste **Tâche**.

Toutefois, l'architecture doit être conçue pour pouvoir utiliser "Ticket" dans l'interface sans modifier la logique métier.

Si les captures sont explicitement demandées comme modèle visuel :

- `Task` reste le nom technique du modèle
- `Tâche` reste le libellé métier par défaut
- `Ticket` peut être utilisé comme libellé UI si le client décide d'adopter cette terminologie

Ne pas renommer les modèles ou tables simplement pour reproduire le texte d'une capture.

---

# 53. Projets et équipes

Les captures montrent que les tâches peuvent appartenir à des projets/équipes.

Même si cette fonctionnalité n'était pas dans le cahier des charges initial, prévoir une architecture extensible permettant :

```text
Projet
  ├── Équipe
  ├── Agents
  └── Tâches
```

Une tâche devrait pouvoir être rattachée à un projet.

Cette relation permettra notamment :

- dashboard par projet
- Kanban par projet
- compteur de tâches par projet
- filtrage par projet
- historique par projet

Ne pas complexifier inutilement la V1 si la fonctionnalité n'est pas encore nécessaire, mais ne pas bloquer son ajout futur.

---

# 54. Responsive et densité

La référence est principalement Desktop avec une forte densité d'information.

Sur Desktop :

- sidebar fixe
- contenu large
- Kanban horizontal
- tableaux complets
- panneaux détail en deux colonnes

Sur tablette :

- sidebar rétractable
- Kanban avec scroll horizontal
- tableaux adaptés

Sur mobile :

- sidebar transformée en menu offcanvas
- Kanban avec défilement horizontal
- Cards pleine largeur
- détail en une colonne
- actions accessibles au pouce
- tableaux convertis en cartes ou scrollables

Ne jamais réduire simplement les éléments jusqu'à les rendre illisibles.

---

# 55. Règle de fidélité visuelle

Lorsqu'une capture montre un comportement ou une structure claire, Claude Code doit la considérer comme une exigence UX.

Exemples :

- si un élément est affiché sous forme de Card, ne pas le transformer en gros tableau sans raison
- si une page utilise une sidebar fixe, conserver cette logique
- si un tableau contient des actions rapides, les conserver
- si une vue utilise des badges, les conserver
- si les informations sont réparties entre contenu principal et panneau latéral, respecter cette hiérarchie

L'objectif est de créer une application **inspirée directement de ces références**, mais avec une identité propre à Taskora.


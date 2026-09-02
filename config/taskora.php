<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Inscription publique
    |--------------------------------------------------------------------------
    |
    | `roles` liste les roles proposes dans le formulaire d'inscription. La
    | valeur est egalement utilisee cote serveur pour valider le champ envoye :
    | retirer un role de cette liste le rend immediatement impossible a creer
    | depuis l'exterieur, sans modifier le code.
    |
    | En production, il est recommande de restreindre la liste a "client" une
    | fois le premier compte administrateur cree :
    |
    |     TASKORA_REGISTRATION_ROLES=client
    |
    */

    'registration' => [

        'enabled' => (bool) env('TASKORA_REGISTRATION_ENABLED', true),

        'roles' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TASKORA_REGISTRATION_ROLES', 'admin,user,client'))
        ))),

    ],

];

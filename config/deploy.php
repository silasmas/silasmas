<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Route HTTP de déploiement (/deploy/migrate)
  |--------------------------------------------------------------------------
  |
  | Activez uniquement sur l'hébergement et définissez un secret long et aléatoire.
  | Exemple d'appel : GET /deploy/migrate?secret=VOTRE_SECRET
  |
  */

  'migrate_enabled' => env('DEPLOY_MIGRATE_ENABLED', false),

  'secret' => env('DEPLOY_SECRET'),

];

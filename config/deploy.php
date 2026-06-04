<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Routes HTTP de déploiement (/deploy/migrate, /deploy/seed)
  |--------------------------------------------------------------------------
  |
  | Activez uniquement sur l'hébergement et définissez un secret long et aléatoire.
  | Exemple : GET /deploy/migrate?secret=VOTRE_SECRET
  |           GET /deploy/seed?secret=VOTRE_SECRET
  |
  */

  'migrate_enabled' => env('DEPLOY_MIGRATE_ENABLED', false),

  'seed_enabled' => env('DEPLOY_SEED_ENABLED', false),

  'secret' => env('DEPLOY_SECRET'),

];

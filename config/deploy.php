<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Routes HTTP de déploiement (/deploy/*)
  |--------------------------------------------------------------------------
  |
  | Activez uniquement sur l'hébergement et définissez un secret long et aléatoire.
  | Exemple : GET /deploy/migrate?secret=VOTRE_SECRET
  |           GET /deploy/seed?secret=VOTRE_SECRET
  |           GET /deploy/storage-link?secret=VOTRE_SECRET
  |
  */

  'migrate_enabled' => env('DEPLOY_MIGRATE_ENABLED', false),

  'seed_enabled' => env('DEPLOY_SEED_ENABLED', false),

  'storage_link_enabled' => env('DEPLOY_STORAGE_LINK_ENABLED', false),

  'secret' => env('DEPLOY_SECRET'),

];

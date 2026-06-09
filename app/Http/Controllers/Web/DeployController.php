<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Deploy\MigrationRunnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Actions de déploiement accessibles par HTTP (hébergement sans SSH).
 */
class DeployController extends Controller
{
  /**
   * Configuration des actions de déploiement (clé config + variable .env).
   *
   * @var array<string, array{config: string, env: string}>
   */
  private const DEPLOY_ACTIONS = [
    'migrate' => [
      'config' => 'deploy.migrate_enabled',
      'env' => 'DEPLOY_MIGRATE_ENABLED',
    ],
    'seed' => [
      'config' => 'deploy.seed_enabled',
      'env' => 'DEPLOY_SEED_ENABLED',
    ],
    'storage-link' => [
      'config' => 'deploy.storage_link_enabled',
      'env' => 'DEPLOY_STORAGE_LINK_ENABLED',
    ],
  ];

  /**
   * Exécute les migrations Laravel (équivalent à php artisan migrate --force).
   *
   * @param Request $request Requête HTTP (secret via query ?secret= ou en-tête X-Deploy-Secret)
   * @return JsonResponse Résultat JSON avec code de sortie et sortie console
   */
  public function migrate(Request $request, MigrationRunnerService $migrationRunner): JsonResponse
  {
    $authError = $this->authorizeDeployRequest($request, 'migrate');
    if ($authError !== null) {
      return $authError;
    }

    $result = $migrationRunner->run();

    return response()->json([
      'success' => $result['success'],
      'exit_code' => $result['exit_code'],
      'output' => $result['output'],
    ], $result['success'] ? 200 : 500);
  }

  /**
   * Exécute le seeder par défaut (DatabaseSeeder : SdevSeeder + AcademySeeder).
   *
   * @param Request $request Requête HTTP (secret via query ?secret= ou en-tête X-Deploy-Secret)
   * @return JsonResponse Résultat JSON avec code de sortie et sortie console
   */
  public function seed(Request $request): JsonResponse
  {
    $authError = $this->authorizeDeployRequest($request, 'seed');
    if ($authError !== null) {
      return $authError;
    }

    $exitCode = Artisan::call('db:seed', ['--force' => true]);
    $output = trim(Artisan::output());

    return response()->json([
      'success' => $exitCode === 0,
      'exit_code' => $exitCode,
      'output' => $output !== '' ? $output : 'Seeders exécutés.',
    ], $exitCode === 0 ? 200 : 500);
  }

  /**
   * Crée le lien symbolique public/storage → storage/app/public.
   *
   * @param Request $request Requête HTTP (secret via query ?secret= ou en-tête X-Deploy-Secret)
   * @return JsonResponse Résultat JSON avec code de sortie et sortie console
   */
  /**
   * Vide et reconstruit le cache de configuration (après changement .env ou déploiement).
   *
   * @param Request $request Requête HTTP (secret via query ?secret= ou en-tête X-Deploy-Secret)
   * @return JsonResponse Résultat JSON
   */
  public function configRefresh(Request $request): JsonResponse
  {
    $authError = $this->authorizeDeployRequest($request, 'migrate');
    if ($authError !== null) {
      return $authError;
    }

    Artisan::call('config:clear');
    $clearOutput = trim(Artisan::output());
    Artisan::call('config:cache');
    $cacheOutput = trim(Artisan::output());

    return response()->json([
      'success' => true,
      'exit_code' => 0,
      'output' => trim($clearOutput."\n".$cacheOutput),
      'frontend_url' => config('app.frontend_url'),
      'participant_base' => \App\Support\FrontendUrl::base(),
    ]);
  }

  public function storageLink(Request $request): JsonResponse
  {
    $authError = $this->authorizeDeployRequest($request, 'storage-link');
    if ($authError !== null) {
      return $authError;
    }

    $exitCode = Artisan::call('storage:link');
    $output = trim(Artisan::output());

    return response()->json([
      'success' => $exitCode === 0,
      'exit_code' => $exitCode,
      'output' => $output !== '' ? $output : 'Lien storage créé ou déjà existant.',
    ], $exitCode === 0 ? 200 : 500);
  }

  /**
   * Vérifie que la route de déploiement est activée et que le secret est valide.
   *
   * @param Request $request Requête entrante
   * @param string $action Action demandée : migrate, seed ou storage-link
   * @return JsonResponse|null Réponse d'erreur ou null si autorisé
   */
  private function authorizeDeployRequest(Request $request, string $action): ?JsonResponse
  {
    if (! isset(self::DEPLOY_ACTIONS[$action])) {
      return response()->json([
        'success' => false,
        'message' => 'Action de déploiement inconnue.',
      ], 400);
    }

    $actionConfig = self::DEPLOY_ACTIONS[$action];

    if (! config($actionConfig['config'])) {
      return response()->json([
        'success' => false,
        'message' => "Route désactivée. Définissez {$actionConfig['env']}=true dans .env.",
      ], 403);
    }

    $configuredSecret = config('deploy.secret');
    if (empty($configuredSecret)) {
      return response()->json([
        'success' => false,
        'message' => 'Secret non configuré. Définissez DEPLOY_SECRET dans .env.',
      ], 503);
    }

    $providedSecret = $request->query('secret')
      ?? $request->header('X-Deploy-Secret');

    if (! is_string($providedSecret) || ! hash_equals($configuredSecret, $providedSecret)) {
      return response()->json([
        'success' => false,
        'message' => 'Secret invalide ou manquant.',
      ], 401);
    }

    return null;
  }
}

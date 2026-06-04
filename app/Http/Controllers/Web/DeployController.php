<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Actions de déploiement accessibles par HTTP (hébergement sans SSH).
 */
class DeployController extends Controller
{
  /**
   * Exécute les migrations Laravel (équivalent à php artisan migrate --force).
   *
   * @param Request $request Requête HTTP (secret via query ?secret= ou en-tête X-Deploy-Secret)
   * @return JsonResponse Résultat JSON avec code de sortie et sortie console
   */
  public function migrate(Request $request): JsonResponse
  {
    $authError = $this->authorizeDeployRequest($request, 'migrate');
    if ($authError !== null) {
      return $authError;
    }

    $exitCode = Artisan::call('migrate', ['--force' => true]);
    $output = trim(Artisan::output());

    return response()->json([
      'success' => $exitCode === 0,
      'exit_code' => $exitCode,
      'output' => $output !== '' ? $output : 'Aucune migration en attente.',
    ], $exitCode === 0 ? 200 : 500);
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
   * Vérifie que la route de déploiement est activée et que le secret est valide.
   *
   * @param Request $request Requête entrante
   * @param string $action Action demandée : migrate ou seed
   * @return JsonResponse|null Réponse d'erreur ou null si autorisé
   */
  private function authorizeDeployRequest(Request $request, string $action): ?JsonResponse
  {
    $enabledKey = $action === 'seed' ? 'deploy.seed_enabled' : 'deploy.migrate_enabled';
    $envKey = $action === 'seed' ? 'DEPLOY_SEED_ENABLED' : 'DEPLOY_MIGRATE_ENABLED';

    if (! config($enabledKey)) {
      return response()->json([
        'success' => false,
        'message' => "Route désactivée. Définissez {$envKey}=true dans .env.",
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

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
    if (! config('deploy.migrate_enabled')) {
      return response()->json([
        'success' => false,
        'message' => 'Route désactivée. Définissez DEPLOY_MIGRATE_ENABLED=true dans .env.',
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

    $exitCode = Artisan::call('migrate', ['--force' => true]);
    $output = trim(Artisan::output());

    return response()->json([
      'success' => $exitCode === 0,
      'exit_code' => $exitCode,
      'output' => $output !== '' ? $output : 'Aucune migration en attente.',
    ], $exitCode === 0 ? 200 : 500);
  }
}

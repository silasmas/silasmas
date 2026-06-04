<?php

namespace App\Http\Controllers;

use App\Models\SessionPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Webhook serveur-à-serveur après paiement Mobile Money ou carte.
 */
class AcademyPaymentCallbackController extends Controller
{
  /**
   * Traite la notification de paiement du prestataire.
   *
   * @param Request $request Corps POST du prestataire
   * @return Response Réponse HTTP 200 attendue par le prestataire
   */
  public function handle(Request $request): Response
  {
    $payload = $request->all();

    $merchantReference = (string) (
      $payload['reference']
      ?? $payload['yourReference']
      ?? $payload['merchantReference']
      ?? ''
    );

    $orderNumber = (string) (
      $payload['orderNumber']
      ?? $payload['order_number']
      ?? ''
    );

    $payment = null;

    if ($merchantReference !== '') {
      $payment = SessionPayment::where('reference', $merchantReference)->first();
    }

    if ($payment === null && $orderNumber !== '') {
      $payment = SessionPayment::where('provider_reference', $orderNumber)->first();
    }

    if ($payment === null) {
      return response('OK', 200);
    }

    if ($payment->status === 'paid') {
      return response('OK', 200);
    }

    $status = $payload['status']
      ?? $payload['transactionStatus']
      ?? $payload['code']
      ?? null;

    $status = is_numeric($status) ? (int) $status : $status;

    if ($status === 0 || $status === '0') {
      $payment->update([
        'status' => 'paid',
        'paid_at' => now(),
        'provider_reference' => $orderNumber !== '' ? $orderNumber : $payment->provider_reference,
      ]);

      if ($payment->registration !== null) {
        $payment->registration->update(['status' => 'confirmed']);
      }
    } elseif ($status === 1 || $status === '1') {
      $payment->update(['status' => 'cancelled']);
    }

    return response('OK', 200);
  }
}

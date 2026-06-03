<?php

namespace App\Http\Controllers\API;

use App\Mail\message as ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * API publique pour le formulaire de contact (front Next.js).
 */
class ContactController extends BaseController
{
  /**
   * Envoie un message de contact par e-mail.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'nom' => ['required', 'string', 'max:255'],
      'subject' => ['required', 'string', 'max:255'],
      'phone' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
      'message' => ['required', 'string'],
    ]);

    $sent = Mail::to('ir-masimango@silasmas.com')->send(new ContactMail(
      $validated['email'],
      $validated['nom'],
      $validated['subject'],
      $validated['message'],
      $validated['phone']
    ));

    if ($sent) {
      return $this->handleResponse(
        ['sent' => true],
        'Votre message a été envoyé avec succès.'
      );
    }

    return $this->handleError('Erreur lors de l\'envoi du message.', [], 500);
  }
}

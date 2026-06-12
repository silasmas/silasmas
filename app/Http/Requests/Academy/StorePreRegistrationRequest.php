<?php

namespace App\Http\Requests\Academy;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valide une pré-inscription (intérêt avant ouverture officielle).
 */
class StorePreRegistrationRequest extends FormRequest
{
  /**
   * Autorise toute requête publique de pré-inscription.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Règles de validation du formulaire de pré-inscription.
   *
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'training_session_slug' => ['required', 'string', 'exists:training_sessions,slug'],
      'firstname' => ['required', 'string', 'max:255'],
      'lastname' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255'],
      'phone' => ['nullable', 'string', 'max:30'],
      'marketing_opt_in' => ['nullable', 'boolean'],
    ];
  }

  /**
   * Messages d'erreur en français.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'training_session_slug.required' => 'La session de formation est obligatoire.',
      'training_session_slug.exists' => 'Cette session de formation est introuvable.',
      'firstname.required' => 'Le prénom est obligatoire.',
      'lastname.required' => 'Le nom est obligatoire.',
      'email.required' => 'L\'adresse e-mail est obligatoire.',
      'email.email' => 'L\'adresse e-mail n\'est pas valide.',
    ];
  }
}

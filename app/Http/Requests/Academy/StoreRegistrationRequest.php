<?php

namespace App\Http\Requests\Academy;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valide une demande d'inscription SDev Academy.
 */
class StoreRegistrationRequest extends FormRequest
{
  /**
   * Autorise toute requête publique d'inscription.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Règles de validation du formulaire d'inscription.
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
      'city' => ['nullable', 'string', 'max:255'],
      'country' => ['nullable', 'string', 'max:100'],
      'education_level' => ['nullable', 'string', 'max:255'],
      'occupation' => ['nullable', 'string', 'max:255'],
      'motivation' => ['nullable', 'string', 'max:2000'],
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

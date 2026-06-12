<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Valide un événement analytics (visite ou clic) envoyé par le frontend.
 */
class StoreSiteVisitRequest extends FormRequest
{
  /**
   * Autorise le tracking public anonyme.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Règles de validation.
   *
   * @return array<string, mixed>
   */
  public function rules(): array
  {
    return [
      'event_type' => ['required', 'string', Rule::in(['page_view', 'click'])],
      'path' => ['required', 'string', 'max:500'],
      'page_title' => ['nullable', 'string', 'max:255'],
      'click_label' => ['nullable', 'string', 'max:255'],
      'click_target' => ['nullable', 'string', 'max:500'],
      'visitor_key' => ['nullable', 'string', 'max:64'],
      'referrer' => ['nullable', 'string', 'max:500'],
      'visited_at' => ['nullable', 'date'],
    ];
  }
}

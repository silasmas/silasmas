<?php

namespace App\Services;

use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Génère un export PDF des inscriptions Academy.
 */
class RegistrationPdfExporter
{
  /**
   * Produit le binaire PDF pour une requête ou une collection d'inscriptions.
   *
   * @param Builder<Registration>|Collection<int, Registration> $queryOrCollection Source des données
   * @param string $title Titre du document
   * @return \Barryvdh\DomPDF\PDF Instance PDF téléchargeable
   */
  public function export(Builder|Collection $queryOrCollection, string $title = 'Inscriptions Academy')
  {
    if ($queryOrCollection instanceof Collection) {
      $registrations = $queryOrCollection->load(['student', 'trainingSession', 'latestPayment']);
    } else {
      $registrations = $queryOrCollection
        ->with(['student', 'trainingSession', 'latestPayment'])
        ->orderByDesc('registered_at')
        ->get();
    }

    return Pdf::loadView('exports.registrations-pdf', [
      'title' => $title,
      'registrations' => $registrations,
      'generatedAt' => now()->locale('fr')->translatedFormat('j F Y à H:i'),
    ])->setPaper('a4', 'landscape');
  }
}

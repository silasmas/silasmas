<?php

namespace App\Exports;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Export Excel des inscriptions Academy avec toutes les informations formulaire.
 */
class RegistrationsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
  /**
   * @param Builder<Registration>|Collection<int, Registration> $queryOrCollection Requête ou collection
   */
  public function __construct(
    protected Builder|Collection $queryOrCollection
  ) {
  }

  /**
   * @return Collection<int, Registration>
   */
  public function collection(): Collection
  {
    if ($this->queryOrCollection instanceof Collection) {
      return $this->queryOrCollection;
    }

    return $this->queryOrCollection
      ->with(['student', 'trainingSession', 'latestPayment'])
      ->orderByDesc('registered_at')
      ->get();
  }

  /**
   * @return list<string>
   */
  public function headings(): array
  {
    return [
      'ID inscription',
      'Session',
      'Prénom',
      'Nom',
      'E-mail',
      'Téléphone',
      'Ville',
      'Pays',
      'Niveau d\'études',
      'Profession',
      'Motivation',
      'Marketing opt-in',
      'Statut inscription',
      'Source',
      'Inscrit le',
      'Notif. e-mail',
      'Notif. SMS',
      'Notif. WhatsApp',
      'Confidentialité acceptée le',
      'Montant paiement',
      'Devise',
      'Référence paiement',
      'Statut paiement',
      'Payé le',
    ];
  }

  /**
   * @param Registration $registration Ligne exportée
   * @return list<string|int|float|null>
   */
  public function map($registration): array
  {
    $student = $registration->student;
    $session = $registration->trainingSession;
    $payment = $registration->latestPayment;

    return [
      $registration->id,
      $session?->title ?? '',
      $student?->firstname ?? '',
      $student?->lastname ?? '',
      $student?->email ?? '',
      $student?->phone ?? '',
      $student?->city ?? '',
      $student?->country ?? '',
      $student?->education_level ?? '',
      $student?->occupation ?? '',
      $registration->motivation ?? '',
      $student?->marketing_opt_in ? 'Oui' : 'Non',
      $this->registrationStatusLabel($registration->status),
      $registration->source ?? '',
      $registration->registered_at?->format('d/m/Y H:i') ?? '',
      $registration->notify_email ? 'Oui' : 'Non',
      $registration->notify_sms ? 'Oui' : 'Non',
      $registration->notify_whatsapp ? 'Oui' : 'Non',
      $registration->confidentiality_accepted_at?->format('d/m/Y H:i') ?? '',
      $payment?->amount ?? '',
      $payment?->currency ?? '',
      $payment?->reference ?? '',
      $this->paymentStatusLabel($payment?->status),
      $payment?->paid_at?->format('d/m/Y H:i') ?? '',
    ];
  }

  /**
   * @param string|null $status Code statut inscription
   * @return string Libellé
   */
  protected function registrationStatusLabel(?string $status): string
  {
    return match ($status) {
      'pending' => 'En attente',
      'pending_payment' => 'En attente de paiement',
      'confirmed' => 'Confirmée',
      'waitlist' => 'Liste d\'attente',
      'pre_registered' => 'Pré-inscrit',
      'cancelled' => 'Annulée',
      default => $status ?? '',
    };
  }

  /**
   * @param string|null $status Code statut paiement
   * @return string Libellé
   */
  protected function paymentStatusLabel(?string $status): string
  {
    return match ($status) {
      'pending' => 'En attente',
      'processing' => 'En cours',
      'paid' => 'Payé',
      'failed' => 'Échoué',
      'refunded' => 'Remboursé',
      'cancelled' => 'Annulé',
      default => $status ?? 'Non initié',
    };
  }
}

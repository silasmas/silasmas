<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>{{ $title }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111; }
    h1 { font-size: 16px; margin: 0 0 4px; }
    .meta { color: #555; margin-bottom: 14px; font-size: 8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 4px 5px; text-align: left; vertical-align: top; }
    th { background: #f3f4f6; font-size: 8px; }
    td { word-break: break-word; }
  </style>
</head>
<body>
  <h1>{{ $title }}</h1>
  <p class="meta">Généré le {{ $generatedAt }} — {{ $registrations->count() }} inscription(s)</p>

  <table>
    <thead>
      <tr>
        <th>Session</th>
        <th>Prénom</th>
        <th>Nom</th>
        <th>E-mail</th>
        <th>Tél.</th>
        <th>Ville</th>
        <th>Pays</th>
        <th>Études</th>
        <th>Profession</th>
        <th>Motivation</th>
        <th>Statut</th>
        <th>Inscrit le</th>
        <th>Paiement</th>
        <th>Montant</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($registrations as $registration)
        @php
          $student = $registration->student;
          $session = $registration->trainingSession;
          $payment = $registration->latestPayment;
          $statusLabel = match ($registration->status) {
            'pending' => 'En attente',
            'pending_payment' => 'Attente paiement',
            'confirmed' => 'Confirmée',
            'waitlist' => 'Liste d\'attente',
            'pre_registered' => 'Pré-inscrit',
            'cancelled' => 'Annulée',
            default => $registration->status,
          };
          $paymentLabel = match ($payment?->status) {
            'pending' => 'En attente',
            'processing' => 'En cours',
            'paid' => 'Payé',
            'failed' => 'Échoué',
            'refunded' => 'Remboursé',
            'cancelled' => 'Annulé',
            default => $payment?->status ?? '—',
          };
        @endphp
        <tr>
          <td>{{ $session?->title }}</td>
          <td>{{ $student?->firstname }}</td>
          <td>{{ $student?->lastname }}</td>
          <td>{{ $student?->email }}</td>
          <td>{{ $student?->phone }}</td>
          <td>{{ $student?->city }}</td>
          <td>{{ $student?->country }}</td>
          <td>{{ $student?->education_level }}</td>
          <td>{{ $student?->occupation }}</td>
          <td>{{ \Illuminate\Support\Str::limit($registration->motivation, 120) }}</td>
          <td>{{ $statusLabel }}</td>
          <td>{{ $registration->registered_at?->format('d/m/Y H:i') }}</td>
          <td>{{ $paymentLabel }}</td>
          <td>
            @if ($payment?->amount)
              {{ number_format((float) $payment->amount, 2, ',', ' ') }} {{ $payment->currency }}
            @else
              —
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>

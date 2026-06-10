@php
  $siteSettings = \App\Models\SiteSetting::instance();
  $brandName = $siteSettings->site_title ?? config('app.name');
  $studentName = $student
    ? trim(($student->firstname ?? '').' '.($student->lastname ?? ''))
    : 'Inconnu';
  $serverLines = $serverLines ?? $presenter->serverResponseLines();
  $failedAt = $payment->failed_at?->timezone('Africa/Kinshasa')->format('d/m/Y à H:i')
    ?? now()->timezone('Africa/Kinshasa')->format('d/m/Y à H:i');
@endphp
@component('mail::message')
# Échec de paiement — SDev Academy

Un paiement d'inscription **n'a pas abouti**. Voici le résumé pour investigation.

@component('mail::panel')
**Référence** — {{ $payment->reference ?? '—' }}

**Session** — {{ $session?->title ?? '—' }}

**Participant** — {{ $studentName }}

**E-mail** — {{ $student?->email ?? '—' }}

**Montant** — {{ number_format((float) $payment->amount, 2, ',', ' ') }} {{ $payment->currency }}

**Méthode** — {{ $presenter->paymentMethodLabel() }}

**Contexte** — {{ $contextLabel }}

**Raison** — {{ $payment->failure_reason ?? 'Non précisé' }}

**Date** — {{ $failedAt }} (Kinshasa)
@endcomponent

@if(count($serverLines) > 0)
## Détail technique FlexPay

@component('mail::table')
| Champ | Valeur |
|:-----:|:-------|
@foreach($serverLines as $line)
| **{{ $line['label'] }}** | {{ $line['value'] }} |
@endforeach
@endcomponent
@else
_Aucune réponse serveur enregistrée pour cet échec._
@endif

@component('mail::button', ['url' => $adminPaymentUrl, 'color' => 'primary'])
Ouvrir dans le dashboard
@endcomponent

{{ $brandName }} — Surveillance paiements Academy
@endcomponent

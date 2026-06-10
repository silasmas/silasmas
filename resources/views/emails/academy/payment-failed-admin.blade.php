@php
  $siteSettings = \App\Models\SiteSetting::instance();
  $brandName = $siteSettings->site_title ?? config('app.name');
  $studentName = $student
    ? trim(($student->firstname ?? '').' '.($student->lastname ?? ''))
    : 'Inconnu';
  $serverResponse = $payment->formattedServerResponse();
@endphp
@component('mail::message')
# Échec de paiement — SDev Academy

Un paiement d'inscription n'a **pas abouti**.

@component('mail::panel')
**Référence :** {{ $payment->reference ?? '—' }}

**Session :** {{ $session?->title ?? '—' }}

**Participant :** {{ $studentName }}

**E-mail :** {{ $student?->email ?? '—' }}

**Montant :** {{ number_format((float) $payment->amount, 2, ',', ' ') }} {{ $payment->currency }}

**Méthode :** {{ $payment->payment_method ?? $payment->channel ?? '—' }}

**Contexte :** {{ $contextLabel }}

**Raison :** {{ $payment->failure_reason ?? 'Non précisé' }}

**Date :** {{ $payment->failed_at?->timezone('Africa/Kinshasa')->format('d/m/Y H:i') ?? now()->timezone('Africa/Kinshasa')->format('d/m/Y H:i') }}
@endcomponent

**Réponse serveur (FlexPay / API) :**

@if($serverResponse !== '—')
```
{{ $serverResponse }}
```
@else
_Aucune réponse serveur enregistrée._
@endif

@component('mail::button', ['url' => $adminPaymentUrl, 'color' => 'primary'])
Voir dans le dashboard
@endcomponent

{{ $brandName }} — Surveillance paiements Academy
@endcomponent

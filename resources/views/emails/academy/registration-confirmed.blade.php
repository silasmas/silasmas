@php
  $siteSettings = \App\Models\SiteSetting::instance();
  $brandName = $siteSettings->site_title ?? config('app.name');
  $startLabel = $session->start_date?->locale('fr')->translatedFormat('j F Y');
  $endLabel = $session->end_date?->locale('fr')->translatedFormat('j F Y');
  $formatLabel = match ($session->format) {
    'online' => 'En ligne',
    'in_person' => 'Présentiel',
    'hybrid' => 'Hybride',
    default => $session->format ?? 'En ligne',
  };
@endphp
@component('mail::message')
# Bonjour {{ $student->firstname }},

@if($paymentConfirmed)
Votre **paiement** est confirmé. Vous êtes officiellement inscrit·e à la formation ci-dessous.
@else
Votre **inscription** est confirmée. Nous avons hâte de vous accompagner pendant cette formation.
@endif

@component('mail::panel')
**{{ $session->title }}**

@if($session->subtitle)
{{ $session->subtitle }}

@endif
**Dates :** {{ $startLabel }}@if($endLabel) — {{ $endLabel }}@endif

**Format :** {{ $formatLabel }}
@endcomponent

Accédez à votre **espace participant** pour suivre le compte à rebours, consulter vos informations et retrouver les ressources de la session :

@component('mail::button', ['url' => $participantUrl, 'color' => 'primary'])
Mon espace formation
@endcomponent

Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :

{{ $participantUrl }}

Merci pour votre confiance,<br>
**{{ $brandName }}** — SDev Academy
@endcomponent

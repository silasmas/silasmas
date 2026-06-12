@php
  $siteSettings = \App\Models\SiteSetting::instance();
  $brandName = $siteSettings->site_title ?? config('app.name');
  $startLabel = $session->start_date?->locale('fr')->translatedFormat('j F Y');
  $endLabel = $session->end_date?->locale('fr')->translatedFormat('j F Y');
  $formatLabel = match ($session->format) {
    'online' => 'En ligne',
    'onsite' => 'Présentiel',
    'hybrid' => 'Hybride',
    default => $session->format ?? 'En ligne',
  };
@endphp
@component('mail::message')
# Bonjour {{ $student->firstname }},

Vous vous étiez **pré-inscrit(e)** à notre prochaine formation. Les **inscriptions sont maintenant ouvertes** — finalisez votre place dès aujourd'hui.

@component('mail::panel')
**{{ $session->title }}**

@if($session->subtitle)
{{ $session->subtitle }}

@endif
**Dates :** {{ $startLabel }}@if($endLabel) — {{ $endLabel }}@endif

**Format :** {{ $formatLabel }}
@endcomponent

Cliquez ci-dessous pour accéder au formulaire d'inscription sur le site :

@component('mail::button', ['url' => $registrationUrl, 'color' => 'primary'])
S'inscrire maintenant
@endcomponent

Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :

{{ $registrationUrl }}

À très bientôt,<br>
**{{ $brandName }}** — SDev Academy
@endcomponent

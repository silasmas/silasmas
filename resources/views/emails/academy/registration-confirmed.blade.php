@component('mail::message')
# Bonjour {{ $student->firstname }},

@if($paymentConfirmed)
Votre **paiement** pour la formation **{{ $session->title }}** est bien enregistré.
@else
Votre **inscription** à la formation **{{ $session->title }}** est confirmée.
@endif

**Dates :** {{ $session->start_date?->format('d/m/Y') }} — {{ $session->end_date?->format('d/m/Y') }}

Accédez à votre espace participant pour voir le compte à rebours, vos informations et les ressources de la session :

@component('mail::button', ['url' => $participantUrl])
Mon espace formation
@endcomponent

Conservez ce lien : [{{ $participantUrl }}]({{ $participantUrl }})

Merci,<br>
{{ config('app.name') }} — SDev Academy
@endcomponent

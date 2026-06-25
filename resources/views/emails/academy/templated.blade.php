@php
  $siteSettings = \App\Models\SiteSetting::instance();
  $brandName = $siteSettings->site_title ?? config('app.name');
@endphp
@component('mail::message')
{!! \App\Support\EmailBodyFormatter::bodyToHtml($mailBody) !!}

@if(!empty($paymentResumeUrl))
@component('mail::button', ['url' => $paymentResumeUrl, 'color' => 'primary'])
Finaliser mon paiement
@endcomponent

Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :

{{ $paymentResumeUrl }}
@endif

Merci,<br>
**{{ $brandName }}** — SDev Academy
@endcomponent

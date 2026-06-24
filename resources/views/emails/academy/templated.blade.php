@php
  $siteSettings = \App\Models\SiteSetting::instance();
  $brandName = $siteSettings->site_title ?? config('app.name');
@endphp
@component('mail::message')
@if($firstname !== '')
Bonjour **{{ $firstname }}**,
@else
Bonjour,
@endif

{!! nl2br(e($mailBody)) !!}

Merci,<br>
**{{ $brandName }}** — SDev Academy
@endcomponent

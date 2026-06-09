@php
  $mailSettings = \App\Models\SiteSetting::instance();
  $mailHomeUrl = \App\Support\FrontendUrl::base();
  $mailBrandName = $mailSettings->site_title ?? config('app.name');
@endphp
@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => $mailHomeUrl])
{{ $mailBrandName }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ $mailBrandName }} — SDev Academy
@endcomponent
@endslot
@endcomponent

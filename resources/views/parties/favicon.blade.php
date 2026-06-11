@php
    $customFaviconUrl = \App\Support\SiteFavicon::configuredPublicUrl(
        \App\Models\SiteSetting::instance()->favicon ?? null
    );
@endphp
@if ($customFaviconUrl)
    <link rel="icon" href="{{ $customFaviconUrl }}">
@else
    <link rel="apple-touch-icon" type="image/png" sizes="180x180" href="{{ \App\Support\SiteFavicon::defaultAssetUrl('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ \App\Support\SiteFavicon::defaultAssetUrl('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ \App\Support\SiteFavicon::defaultAssetUrl('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ \App\Support\SiteFavicon::defaultAssetUrl('favicon.ico') }}">
    <link rel="manifest" href="{{ \App\Support\SiteFavicon::defaultAssetUrl('site.webmanifest') }}">
@endif

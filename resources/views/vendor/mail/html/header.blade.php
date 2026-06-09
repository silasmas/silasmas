@php
  $mailSettings = \App\Models\SiteSetting::instance();
  $mailLogoUrl = $mailSettings->logoUrl();
  $mailBrandName = $mailSettings->site_title ?? config('app.name');
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if($mailLogoUrl)
    <img src="{{ $mailLogoUrl }}" class="logo" alt="{{ $mailBrandName }}">
@else
    <span style="color: #1a1a1a; font-size: 18px; font-weight: 700;">{{ $mailBrandName }}</span>
@endif
</a>
</td>
</tr>

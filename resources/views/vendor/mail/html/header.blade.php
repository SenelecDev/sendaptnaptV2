@props(['url'])
@php
    $logoPath = public_path('img/logo.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<table cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto;">
<tr>
<td style="text-align: center; padding-bottom: 10px;">
@if($logoBase64)
<img src="{{ $logoBase64 }}" alt="Senelec" style="max-width: 80px; height: auto;">
@endif
</td>
</tr>
<tr>
<td style="text-align: center;">
<span style="color: #2B1444; font-size: 22px; font-weight: bold;">{{ $slot }}</span>
</td>
</tr>
</table>
</a>
</td>
</tr>

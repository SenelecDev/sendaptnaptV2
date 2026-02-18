@props(['url'])
@php
    $logoBase64 = '';
    $logoPaths = [
        public_path('img/logo.png'),
        public_path('img/logo.jpg'),
        public_path('images/logo.png'),
        base_path('public/img/logo.png'),
    ];
    foreach ($logoPaths as $logoPath) {
        if (file_exists($logoPath)) {
            $ext = pathinfo($logoPath, PATHINFO_EXTENSION);
            $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';
            $logoBase64 = "data:{$mime};base64," . base64_encode(file_get_contents($logoPath));
            break;
        }
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
@else
<div style="display: inline-block; background: linear-gradient(135deg, #2B1444, #E85D04); padding: 12px 20px; border-radius: 10px;">
    <span style="color: #ffffff; font-size: 18px; font-weight: bold; letter-spacing: 1px;">SENELEC</span>
</div>
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

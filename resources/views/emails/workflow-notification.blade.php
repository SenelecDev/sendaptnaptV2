<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            max-width: 600px; 
            margin: 0 auto; 
            padding: 0;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(43, 20, 68, 0.1);
            margin: 20px;
        }
        .header { 
            background: linear-gradient(135deg, #2B1444 0%, #4A2066 100%);
            padding: 25px; 
            text-align: center;
        }
        .header img { 
            max-width: 80px; 
            margin-bottom: 10px; 
        }
        .header h1 { 
            color: #ffffff; 
            margin: 10px 0 5px 0; 
            font-size: 20px; 
        }
        .header p { 
            color: #e0e0e0; 
            margin: 5px 0; 
            font-size: 12px; 
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2B1444;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .message-box {
            background-color: #fff8f3;
            border-left: 4px solid #E85D04;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        .message-box.success {
            background-color: #e6fff0;
            border-left-color: #059669;
        }
        .message-box.warning {
            background-color: #fff8e6;
            border-left-color: #f59e0b;
        }
        .message-box.error {
            background-color: #ffe6e6;
            border-left-color: #dc2626;
        }
        .message-box.info {
            background-color: #e6f0ff;
            border-left-color: #0D1CB0;
        }
        .btn {
            display: inline-block;
            background-color: #E85D04;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
        .btn:hover {
            background-color: #d54e00;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            border-top: 3px solid #E85D04;
        }
        .footer p {
            color: #666;
            font-size: 12px;
            margin: 5px 0;
        }
        .footer a {
            color: #E85D04;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .details-table th {
            background-color: #2B1444;
            color: #ffffff;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 13px;
        }
        .details-table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('img/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
    @endphp
    <div class="email-container">
        <div class="header">
            @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Senelec">
            @endif
            <h1>{{ config('app.name') }}</h1>
            <p>Système de gestion des DAPT et NAPT</p>
        </div>
        
        <div class="content">
            <p class="greeting">Bonjour {{ $userName }},</p>
            
            <div class="message-box {{ $messageType ?? '' }}">
                <strong style="color: #2B1444; font-size: 16px;">{{ $title }}</strong>
                <p style="margin: 10px 0 0 0;">{{ $message }}</p>
            </div>
            
            @if(isset($details) && count($details) > 0)
            <table class="details-table">
                <thead>
                    <tr>
                        <th colspan="2">Informations complémentaires</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $label => $value)
                    <tr>
                        <td><strong>{{ $label }}</strong></td>
                        <td>{{ $value }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            
            @if(isset($actionUrl) && $actionUrl)
            <div style="text-align: center;">
                <a href="{{ url($actionUrl) }}" class="btn">{{ $actionText ?? 'Voir les détails' }}</a>
            </div>
            @endif
            
            <p style="margin-top: 30px; color: #666;">
                Merci d'utiliser l'application {{ config('app.name') }}.
            </p>
        </div>
        
        <div class="footer">
            <p><strong>DESA/DESE - SENELEC</strong></p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p style="font-size: 11px; color: #999;">
                Cet email a été envoyé automatiquement. Merci de ne pas répondre directement à ce message.
            </p>
        </div>
    </div>
</body>
</html>

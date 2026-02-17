<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diffusion Hebdomadaire NAPT</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 900px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #f5821f; }
        .header img { max-width: 120px; margin-bottom: 10px; }
        .header h1 { color: #f5821f; margin: 10px 0 5px 0; font-size: 24px; }
        .header p { color: #666; margin: 5px 0; }
        h2 { color: #2d5a27; margin-top: 25px; font-size: 18px; }
        h3 { color: #f5821f; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5821f; color: white; }
        .summary-table th { background-color: #2d5a27; }
        .recap-table { font-size: 11px; }
        .recap-table th { background-color: #2B1444; color: white; padding: 6px; font-size: 10px; }
        .recap-table td { padding: 5px; font-size: 10px; }
        .recap-table tr:nth-child(even) { background-color: #f9f9f9; }
        .napt-section { background-color: #f9f9f9; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #f5821f; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
        .highlight { background-color: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info-box { background-color: #e8f4fc; border: 1px solid #b3d9f2; border-radius: 5px; padding: 10px; margin: 15px 0; }
        .info-box p { margin: 5px 0; color: #1e5f8a; }
    </style>
</head>
<body>
    <!-- En-tête avec logo -->
    <div class="header">
        <img src="{{ asset('img/logo.png') }}" alt="Senelec" style="max-width: 100px;">
        <h1>Diffusion Hebdomadaire des NAPT</h1>
        <p><strong>Semaine S{{ $semaine }} - Année {{ $annee }}</strong></p>
        <p style="font-size: 12px; color: #888;">{{ $napts->count() }} NAPT(s) - Envoyé le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
    
    <p>Bonjour,</p>
    
    <p>Veuillez trouver ci-dessous et en pièce jointe la liste des Notes d'Arrêt Pour Travaux (NAPT) pour la semaine {{ $semaine }} de l'année {{ $annee }}.</p>
    
    <!-- Tableau Récapitulatif comme dans le PDF -->
    <h2>Récapitulatif des NAPT</h2>
    <table class="recap-table">
        <thead>
            <tr>
                <th>S.</th>
                <th>N° NAPT</th>
                <th>Demandeur</th>
                <th>Lieu</th>
                <th>Installations Consignées</th>
                <th>Travaux</th>
                <th>Début</th>
                <th>J</th>
                <th>Fin</th>
                <th>Indications</th>
            </tr>
        </thead>
        <tbody>
            @foreach($napts as $napt)
            <tr>
                <td>{{ $napt->numero_semaine ?? '-' }}</td>
                <td><strong>{{ $napt->numero_note }}</strong></td>
                <td>{{ $napt->demande->demandeur->name ?? 'N/A' }}</td>
                <td>
                    @if($napt->demande->mode_saisie === 'manuelle' && $napt->demande->lieu_execution_manuel)
                        {{ $napt->demande->lieu_execution_manuel }}
                    @else
                        {{ $napt->demande->lieu_execution ?? 'N/A' }}
                    @endif
                </td>
                <td>
                    @php
                        $installations = [];
                        
                        // Mode manuel
                        if (isset($napt->demande->mode_saisie) && $napt->demande->mode_saisie === 'manuel') {
                            if (!empty($napt->demande->ouvrages_consigner_manuel)) {
                                $installations[] = $napt->demande->ouvrages_consigner_manuel;
                            }
                        } else {
                            // Mode GMAO - Lignes
                            if (!empty($napt->demande->lignes_oracle)) {
                                $lignesData = json_decode($napt->demande->lignes_oracle, true);
                                if (is_array($lignesData)) {
                                    foreach ($lignesData as $ligne) {
                                        $desc = is_array($ligne) ? ($ligne['description'] ?? $ligne['EQUIPMENT_DES'] ?? $ligne['code'] ?? null) : $ligne;
                                        if ($desc) $installations[] = $desc;
                                    }
                                }
                            }
                            
                            // Mode GMAO - Équipements
                            if (!empty($napt->demande->equipements_oracle)) {
                                $equipementsData = json_decode($napt->demande->equipements_oracle, true);
                                if (is_array($equipementsData)) {
                                    foreach ($equipementsData as $key => $data) {
                                        if (is_array($data) && isset($data['description'])) {
                                            $installations[] = $data['description'];
                                        } elseif (is_array($data)) {
                                            $lastItem = end($data);
                                            $desc = is_array($lastItem) ? ($lastItem['description'] ?? $lastItem['EQUIPMENT_DES'] ?? null) : null;
                                            if ($desc) $installations[] = $desc;
                                        }
                                    }
                                }
                            }
                        }
                        
                        $installations = array_unique(array_filter($installations));
                    @endphp
                    {{ !empty($installations) ? implode(', ', $installations) : '-' }}
                </td>
                <td>{{ $napt->demande->designation ?? 'N/A' }}</td>
                <td>{{ $napt->ddt ? \Carbon\Carbon::parse($napt->ddt)->format('d/m/Y H:i') : 'N/A' }}</td>
                <td style="text-align: center;">
                    @if($napt->ddt && $napt->dft)
                        {{ \Carbon\Carbon::parse($napt->ddt)->diffInDays(\Carbon\Carbon::parse($napt->dft)) + 1 }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $napt->dft ? \Carbon\Carbon::parse($napt->dft)->format('d/m/Y H:i') : 'N/A' }}</td>
                <td>{{ $napt->renseignementN ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="info-box">
        <p><strong>RESUME : {{ $napts->count() }} NAPT(s)</strong></p>
        <p>Semaine {{ $semaine }}/{{ $annee }} - Groupe : {{ $groupeNom }}</p>
    </div>
    
    @if($pdfPath)
    <div class="highlight">
        <strong>Un document PDF récapitulatif détaillé est joint à cet email.</strong>
    </div>
    @endif
    
    <div class="footer">
        <p>Cordialement,<br>
        <strong>DESA/DESE</strong><br>
        {{ config('app.name') }}</p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>DAPT {{ $demande->numero_demande }}</title>
    <style>
    * {
        font-family: 'DejaVu Sans', sans-serif !important;
    }
    .box-missionView p {
        font-size: 12px;
    }
    html, body, h1, h2, h3, h4, h5, h6, p, strong {
        font-family: 'DejaVu Sans', sans-serif !important;
    }
    table {
        border: 1px solid #eee;
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin: 10px 0;
    }
    .logo {
        text-align: center;
        width: 140px;
        margin: auto;
        margin-bottom: 5px;
    }
    .direction {
        margin-bottom: 20px;
    }
    .missionContent {
        background: #fff;
        position: relative;
    }
    .info-mission-flex p {
        margin: 0 !important;
    }
    .info-mission p,
    .mission-text p {
        margin-bottom: 0 !important;
        margin-top: 6px;
    }
    .mission-title {
        text-align: center;
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 15px;
    }
    .mission-title p {
        margin: 10px;
    }
    .table-mission {
        width: 100%;
        margin-bottom: 30px;
    }
    .box-missionView {
        max-width: 100%;
        margin: auto;
        position: relative;
    }
    .table-mission.table-participants tr {
        border-bottom: 1px solid #ccc;
        font-size: 12px;
    }
    @page { 
        margin: 10mm !important; 
    }
    body { 
        margin: 10px !important; 
        font-size: 11px;
    }
    .objet-mission {
        color: #000;
        padding-left: 30px;
        display: block;
        margin-top: 5px;
        position: relative;
        margin-bottom: 10px;
    }
    .objet-mission::before {
        content: '';
        width: 8px;
        height: 8px;
        background: #B3006C;
        left: 0;
        position: absolute;
        top: 4px;
        left: 10px;
    }
    .table-mission table thead {
        background: #B3006C;
        color: #fff;
    }
    .table-mission table thead th {
        padding: 8px;
        font-weight: bold;
    }
    .info-mission-flex p {
        display: inline-block !important;
    }
    .info-mission-flex p {
        margin-right: 10px;
    }
    .mission-foo {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        left: 0;
        right: 0;
        border-top: 1px solid #ccc;
    }
    .mission-foo p {
        text-align: center;
        max-width: 550px;
        margin: auto;
        display: inline-block;
        font-size: 10px;
        color: #666;
    }
    .signataire-table thead tr th {
        padding: 5px;
        border: 1px solid #ccc;
        font-size: 12px;
    }
    .signataire-table .signRow {
        height: 100px;
    }
    .signataire-table tbody td {
        text-align: center;
        padding: 10px;
        border: 1px solid #ccc;
    }
    .info-mission-align {
        text-align: justify;
    }
    .info-mission-align > p {
        margin-right: 20px !important;
        display: inline;
    }
    .info-mission-align > p:last-child {
        margin-right: 0 !important;
    }
    .signValid {
        position: relative;
    }
    .signValid .cachet {
        position: absolute;
        top: -10px;
        left: 0;
        right: 0;
        margin: auto;
        width: 180px;
    }
    .signValid .cachet img,
    .signValid .signature img {
        object-fit: cover !important;
    }

    .table-mission table tbody tr {
        border-bottom: 1px solid #ccc;
    }

    .table-mission table tbody tr td {
        padding: 6px 15px;
        text-align: center;
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        border-left: 1px solid #ccc;
    }
    .table-spacing td {
        padding: 8px;
    }
    
    .schema img {
        max-width: 100%;
        height: auto;
        margin: 10px auto;
        display: block;
    }
    
    ul {
        margin: 5px 0;
        padding-left: 20px;
    }
    li {
        margin: 2px 0;
    }
    </style>
</head>
<body>
    <div class="box-missionView">
        <div class="missionContent" id="missionContent">

            <div class="logo" style="text-align: center; width: 100%;">
                @php
                    $logoPath = public_path('img/logo.png');
                    $logoData = '';
                    if (file_exists($logoPath)) {
                        $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                    }
                @endphp
                @if($logoData)
                    <img style="display: inline-block;" src="{{ $logoData }}" width="100" alt="Logo Senelec">
                @endif
            </div>
            <br>
            <div class="mission-title">
                <h1 style="font-size: 1.2rem; margin-bottom: 5px; color: #B3006C;">DEMANDE D'ARRÊT POUR TRAVAUX N° {{ $demande->numero_demande }}</h1>
            </div>

            <div class="direction">
                <table class="table-spacing" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td colspan="2" style="border: 1px solid #ccc; text-align: center; background: #f9f9f9;">
                            <strong>{{ $demande->destinataire }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ccc; width: 50%;">
                            <strong>Date :</strong> {{ \Carbon\Carbon::parse($demande->date)->format('d/m/Y') }}<br><br>
                            <strong>Demande adressée par :</strong> {{ $demande->demandeur->name ?? '-' }}
                        </td>
                        <td style="border: 1px solid #ccc;">
                            <strong>Fonction :</strong> {{ $demande->demandeur->user_title ?? $demande->demandeur->poste ?? '-' }}<br>
                            <strong>Téléphone :</strong> {{ $demande->telephone_demandeur ?? '-' }}<br><br>
                            <strong>Appartenance :</strong> {{ $demande->demandeur->service ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td style="border: 1px solid #ccc;">
                            <strong>Chargé des travaux :</strong> {{ $demande->charge_travaux_info->nom ?? '-' }}@if($demande->charge_travaux_info && $demande->charge_travaux_info->type === 'externe') <small>(Externe)</small>@endif<br><br>
                            <strong>Fonction :</strong> {{ $demande->charge_travaux_info->type === 'externe' ? 'Ext.' : ($demande->chargeTravaux->user_title ?? $demande->chargeTravaux->poste ?? '-') }}
                        </td>
                        <td style="border: 1px solid #ccc;">
                            <strong>Appartenance :</strong> {{ $demande->charge_travaux_info->entreprise ?? '-' }}<br><br>
                            <strong>Téléphone :</strong> {{ $demande->charge_travaux_info->telephone ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: center; border: 1px solid #ccc; background: #f9f9f9;">
                            <strong>Lieu d'exécution :</strong> {{ $demande->lieu_execution ?? $demande->lieu_execution_manuel ?? '-' }}
                        </td>
                    </tr>
                </table>
            </div>

            <p style="text-align: left; margin-bottom: 15px;">
                <strong>Désignation des travaux envisagés :</strong> {{ $demande->designation ?? '-' }}
            </p>

            <div class="table-mission table-participants">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50%;">Ouvrages à consigner</th>
                            <th style="width: 50%;">Ouvrages sur lesquels les travaux sont à exécuter</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="vertical-align: top; text-align: left;">
                                {{-- Mode manuel : afficher le texte saisi --}}
                                @if($demande->mode_saisie === 'manuel')
                                    {!! nl2br(e($demande->ouvrages_consigner_manuel ?? 'Non renseigné')) !!}
                                @else
                                    {{-- Lignes à consigner depuis Oracle --}}
                                    @if($demande->lignes_oracle)
                                        @php
                                            $lignesConsigner = json_decode($demande->lignes_oracle, true);
                                        @endphp
                                        @if($lignesConsigner && is_array($lignesConsigner))
                                            <strong>Lignes :</strong><br>
                                            @foreach($lignesConsigner as $ligne)
                                                @if(is_array($ligne))
                                                    • {{ $ligne['description'] ?? $ligne['code'] ?? '-' }}<br>
                                                @else
                                                    • {{ $ligne }}<br>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif

                                    {{-- Équipements à consigner depuis Oracle (dernier niveau uniquement) --}}
                                    @if($demande->equipements_oracle)
                                        @php
                                            $equipementsConsigner = json_decode($demande->equipements_oracle, true);
                                            $dernierNiveauEquipements = [];
                                            if (is_array($equipementsConsigner)) {
                                                $niveauxAvecData = [];
                                                foreach ($equipementsConsigner as $levelKey => $levelData) {
                                                    if (preg_match('/level_(\d+)/', $levelKey, $m) && is_array($levelData) && !empty($levelData)) {
                                                        $niveauxAvecData[$m[1]] = $levelData;
                                                    }
                                                }
                                                if (!empty($niveauxAvecData)) {
                                                    $dernierNiveau = max(array_keys($niveauxAvecData));
                                                    foreach ($niveauxAvecData[$dernierNiveau] as $equipement) {
                                                        if (is_array($equipement)) {
                                                            $dernierNiveauEquipements[] = $equipement['description'] ?? $equipement['code'] ?? '';
                                                        } elseif (is_string($equipement)) {
                                                            $dernierNiveauEquipements[] = $equipement;
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if(!empty($dernierNiveauEquipements))
                                            <strong>Équipements :</strong>
                                            <ul>
                                                @foreach($dernierNiveauEquipements as $equip)
                                                    <li>{{ $equip }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif

                                    @if(!$demande->lignes_oracle && !$demande->equipements_oracle)
                                        -
                                    @endif
                                @endif
                            </td>
                            <td style="vertical-align: top; text-align: left;">
                                {{-- Mode manuel : afficher le texte saisi --}}
                                @if($demande->mode_saisie === 'manuel')
                                    {!! nl2br(e($demande->ouvrages_installer_manuel ?? 'Non renseigné')) !!}
                                @else
                                    {{-- Lignes à installer depuis Oracle --}}
                                    @if($demande->lignes_installer_oracle)
                                        @php
                                            $lignesInstaller = json_decode($demande->lignes_installer_oracle, true);
                                        @endphp
                                        @if($lignesInstaller && is_array($lignesInstaller))
                                            <strong>Lignes :</strong><br>
                                            @foreach($lignesInstaller as $ligne)
                                                @if(is_array($ligne))
                                                    • {{ $ligne['description'] ?? $ligne['code'] ?? '-' }}<br>
                                                @else
                                                    • {{ $ligne }}<br>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif

                                    {{-- Équipements à installer depuis Oracle (dernier niveau uniquement) --}}
                                    @if($demande->equipements_installer_oracle)
                                        @php
                                            $equipementsInstaller = json_decode($demande->equipements_installer_oracle, true);
                                            $dernierNiveauEquipementsInstaller = [];
                                            if (is_array($equipementsInstaller)) {
                                                $niveauxAvecData = [];
                                                foreach ($equipementsInstaller as $levelKey => $levelData) {
                                                    if (preg_match('/level_(\d+)/', $levelKey, $m) && is_array($levelData) && !empty($levelData)) {
                                                        $niveauxAvecData[$m[1]] = $levelData;
                                                    }
                                                }
                                                if (!empty($niveauxAvecData)) {
                                                    $dernierNiveau = max(array_keys($niveauxAvecData));
                                                    foreach ($niveauxAvecData[$dernierNiveau] as $equipement) {
                                                        if (is_array($equipement)) {
                                                            $dernierNiveauEquipementsInstaller[] = $equipement['description'] ?? $equipement['code'] ?? '';
                                                        } elseif (is_string($equipement)) {
                                                            $dernierNiveauEquipementsInstaller[] = $equipement;
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if(!empty($dernierNiveauEquipementsInstaller))
                                            <strong>Équipements :</strong>
                                            <ul>
                                                @foreach($dernierNiveauEquipementsInstaller as $equip)
                                                    <li>{{ $equip }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif

                                    @if(!$demande->lignes_installer_oracle && !$demande->equipements_installer_oracle)
                                        -
                                    @endif
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p style="text-align: left; margin: 15px 0; font-size: 9px;">
                <strong>Consignation en :</strong>
                @if($demande->etape === 'ue')
                    Une étape
                @elseif($demande->etape === 'de')
                    Deux étapes
                @else
                    {{ $demande->etape ?? '-' }}
                @endif
                @php
                    $mcceRaw = $demande->getAttributes()['mcce'] ?? $demande->mcce ?? null;
                    $mteRaw = $demande->getAttributes()['mte'] ?? $demande->mte ?? null;
                    $hasMcce = $mcceRaw === 'oui' || $mcceRaw === 1 || $mcceRaw === '1' || $mcceRaw === true;
                    $hasMte = $mteRaw === 'oui' || $mteRaw === 1 || $mteRaw === '1' || $mteRaw === true;
                @endphp
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong>Mise en court-circuit aux extrémités :</strong> {{ $hasMcce ? 'Oui' : 'Non' }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong>Mise à la terre aux extrémités :</strong> {{ $hasMte ? 'Oui' : 'Non' }}
            </p>

            <div class="table-mission table-participants">
                <table>
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Proposée</th>
                            <th>Acceptée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Date début</strong></td>
                            <td>{{ $demande->ddp ? \Carbon\Carbon::parse($demande->ddp)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $demande->dda ? \Carbon\Carbon::parse($demande->dda)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Heure début</strong></td>
                            <td>{{ $demande->hdp ?? '-' }}</td>
                            <td>{{ $demande->hda ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Date fin</strong></td>
                            <td>{{ $demande->dfp ? \Carbon\Carbon::parse($demande->dfp)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $demande->dfa ? \Carbon\Carbon::parse($demande->dfa)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Heure fin</strong></td>
                            <td>{{ $demande->hfp ?? '-' }}</td>
                            <td>{{ $demande->hfa ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Délai max de restitution</strong></td>
                            <td>
                                @php
                                    // Obtenir la valeur brute sans le cast boolean
                                    $delaiRaw = $demande->getAttributes()['dmrp'] ?? null;
                                    
                                    // Vérifier si c'est une valeur valide
                                    $isValidDelai = $delaiRaw !== null && $delaiRaw !== '' && $delaiRaw !== 'non_applicable' && $delaiRaw !== 0 && $delaiRaw !== '0' && $delaiRaw !== false;
                                    
                                    // Si c'est un format time (HH:MM ou HH:MM:SS), formater correctement
                                    if ($isValidDelai && is_string($delaiRaw) && strpos($delaiRaw, ':') !== false) {
                                        $parts = explode(':', $delaiRaw);
                                        $hours = intval($parts[0]);
                                        $minutes = intval($parts[1] ?? 0);
                                        if ($hours > 0 && $minutes > 0) {
                                            $displayDelai = $hours . 'h' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
                                        } elseif ($hours > 0) {
                                            $displayDelai = $hours . 'h';
                                        } elseif ($minutes > 0) {
                                            $displayDelai = $minutes . 'mn';
                                        } else {
                                            $displayDelai = $delaiRaw;
                                        }
                                    } else {
                                        $displayDelai = $delaiRaw;
                                    }
                                @endphp
                                @if($isValidDelai)
                                    {{ $displayDelai }}
                                @else
                                    Non applicable
                                @endif
                            </td>
                            <td>
                                @php
                                    $restitutionRaw = $demande->getAttributes()['dmrp_restitution'] ?? null;
                                @endphp
                                @if($restitutionRaw == 1 || $restitutionRaw === true || $restitutionRaw === '1')
                                    Restituer le soir
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p style="text-align: left; margin: 15px 0;">
                <strong>Renseignements ou informations complémentaires :</strong> {{ $demande->renseignement ?? '-' }}
            </p>

            {{-- Section Schéma --}}
            @if(isset($schema) && $schema)
            <div class="schema" style="margin: 20px 0;">
                <p><strong>Schéma :</strong></p>
                <img src="{{ $schema }}" alt="Schéma" style="max-width: 100%; height: auto;">
            </div>
            @endif

            <div class="mission-foo">
                <p>
                    Société Anonyme au Capital de 175 236 340 000 Francs CFA - 28, rue Vincens - BP 93 Dakar (Sénégal)<br>
                    N°RC : SN-DK-84-B-30 - NINEA : 00140012G3<br>
                    Tél. : (221) 33 839 30 30 - Fax : (221) 33 823 12 67 - <strong>www.senelec.sn</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

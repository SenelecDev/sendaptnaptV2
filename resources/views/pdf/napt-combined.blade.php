<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notes d'arrêt - Senelec</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            word-wrap: break-word;
            width: 100%;
            padding: 2mm;
        }

        .napt-page {
            width: 100%;
            max-width: 100%;
            padding: 0;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .napt-page + .napt-page {
            page-break-before: always;
        }

        /* Header */
        .header {
            width: 100%;
            margin-bottom: 5px;
        }

        .header-left {
            float: left;
            width: 12%;
        }

        .header-right {
            float: right;
            width: 88%;
            text-align: center;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .logo-img {
            width: 80px;
            display: block;
            margin-bottom: 5px;
        }

        .title h2 {
            font-size: 14px;
            margin: 3px 0;
        }

        .title p {
            font-size: 10px;
            margin: 2px 0;
        }

        /* Main layout table */
        .main-layout {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .main-layout td {
            vertical-align: top;
            overflow: hidden;
        }

        .sidebar-left,
        .sidebar-right {
            width: 4%;
            background-color: #e8e8e8;
            text-align: center;
            padding: 2px;
        }

        .sidebar-left h4,
        .sidebar-right h4 {
            font-size: 7px;
            margin: 0 0 2px 0;
        }

        .calendar-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .calendar-list li {
            border: 1px solid #000;
            padding: 0;
            font-size: 6px;
            line-height: 1;
            text-align: center;
        }

        .highlight-date {
            background-color: green !important;
            color: white !important;
            font-weight: bold;
        }

        .main-content {
            width: 92%;
            padding: 3px 5px;
        }

        /* Warning box */
        .warning-box {
            border: 2px solid #000;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            padding: 4px;
            margin-bottom: 5px;
        }

        /* Info paragraphs */
        .info-line {
            margin: 2px 0;
            font-size: 9px;
        }

        /* Data tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            table-layout: fixed;
        }

        .data-table th,
        .data-table td {
            border: 1px solid black;
            padding: 2px;
            text-align: left;
            font-size: 8px;
            word-wrap: break-word;
            overflow: hidden;
        }

        /* Two column layout */
        .two-columns {
            width: 100%;
            border-collapse: collapse;
        }

        .two-columns > tbody > tr > td {
            width: 50%;
            vertical-align: top;
            padding: 3px;
        }

        /* Signataires */
        .signataire-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .signataire-table th {
            border: 1px solid #ccc;
            padding: 3px;
            font-size: 9px;
            background-color: #f0f0f0;
            text-align: center;
        }

        .signataire-table td {
            border: 1px solid #ccc;
            padding: 3px;
            text-align: center;
            height: 50px;
            vertical-align: middle;
        }

        .signature-img {
            max-width: 80px;
            max-height: 50px;
        }

        /* .signature-name supprimé - noms non affichés */

        /* Colors */
        .red-text {
            color: red;
        }

        .blue-text {
            color: blue;
        }

        /* Footer */
        .footer-text {
            text-align: center;
            font-size: 8px;
            margin-top: 5px;
            color: #666;
        }

        .small-text {
            font-size: 8px;
        }

        .center-text {
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        // Compatibilité: accepter $notes ou $napts
        $notes = $notes ?? $napts ?? collect();
        $semaine = $notes->first()->numero_semaine ?? now()->weekOfYear;
        $annee = $notes->first() ? \Carbon\Carbon::parse($notes->first()->created_at)->year : now()->year;

        // Pré-encoder le logo une seule fois
        $logoPath = public_path('img/logo.png');
        $logoData = '';
        if (file_exists($logoPath)) {
            $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Cache des signatures base64 par chemin de fichier pour éviter de ré-encoder le même fichier
        $signatureCache = [];
        $getSignatureBase64 = function($signaturePath) use (&$signatureCache) {
            if (!$signaturePath) return null;
            if (Str::startsWith($signaturePath, ['http://', 'https://'])) return $signaturePath;
            if (isset($signatureCache[$signaturePath])) return $signatureCache[$signaturePath];
            $sigPath = public_path('storage/' . ltrim($signaturePath, '/'));
            if (file_exists($sigPath)) {
                $ext = pathinfo($sigPath, PATHINFO_EXTENSION);
                $signatureCache[$signaturePath] = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($sigPath));
                return $signatureCache[$signaturePath];
            }
            return null;
        };
    @endphp

    <!-- Page de prévisualisation / Récapitulatif -->
    <div class="napt-page">
        <div class="header clearfix" style="margin-bottom: 10px;">
            <div class="header-left">
                @if($logoData)
                    <img class="logo-img" src="{{ $logoData }}" alt="Logo Senelec">
                @endif
                <p style="font-size: 9px; margin-bottom: 8px;"><b>DESA/DESE</b></p>
            </div>
            <div class="header-right">
                <div class="title">
                    <h2 style="font-size: 14px; color: #333; margin-bottom: 5px;">RÉCAPITULATIF DES NOTES D'ARRÊT POUR TRAVAUX</h2>
                    <p style="font-size: 11px; color: #666;">Semaine S<b>{{ $semaine }}</b> - Année <b>{{ $annee }}</b></p>
                    <p style="font-size: 10px; color: #888; margin-top: 3px;">{{ $notes->count() }} NAPT(s) - Généré le {{ now()->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 7px; margin-top: 10px;">
            <thead>
                <tr style="background-color: #2B1444; color: white;">
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">S.</th>
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">N° NAPT</th>
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">Demandeur</th>
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">Lieu</th>
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">Installations Consignées</th>
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">Travaux</th>
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">Début</th>
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">J</th>
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">Fin</th>
                    <th style="border: 1px solid #333; padding: 4px; text-align: left;">Indications</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notes as $napt)
                <tr style="background-color: {{ $loop->even ? '#f9f9f9' : '#fff' }};">
                    <td style="border: 1px solid #ccc; padding: 3px;">{{ $napt->numero_semaine ?? '-' }}</td>
                    <td style="border: 1px solid #ccc; padding: 3px; font-weight: bold;">{{ $napt->numero_note }}</td>
                    <td style="border: 1px solid #ccc; padding: 3px;">{{ $napt->demande->demandeur->name ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ccc; padding: 3px;">
                        @if($napt->demande->mode_saisie === 'manuelle' && $napt->demande->lieu_execution_manuel)
                            {{ $napt->demande->lieu_execution_manuel }}
                        @else
                            {{ $napt->demande->lieu_execution ?? 'N/A' }}
                        @endif
                    </td>
                    <td style="border: 1px solid #ccc; padding: 3px;">
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
                                
                                // Mode GMAO - Équipements (dernier niveau uniquement)
                                if (!empty($napt->demande->equipements_oracle)) {
                                    $equipementsData = json_decode($napt->demande->equipements_oracle, true);
                                    if (is_array($equipementsData)) {
                                        $niveauxAvecData = [];
                                        foreach ($equipementsData as $levelKey => $levelData) {
                                            if (preg_match('/level_(\d+)/', $levelKey, $m) && is_array($levelData) && !empty($levelData)) {
                                                $niveauxAvecData[$m[1]] = $levelData;
                                            }
                                        }
                                        if (!empty($niveauxAvecData)) {
                                            $dernierNiveau = max(array_keys($niveauxAvecData));
                                            foreach ($niveauxAvecData[$dernierNiveau] as $equip) {
                                                $desc = is_array($equip) ? ($equip['description'] ?? $equip['EQUIPMENT_DES'] ?? $equip['code'] ?? null) : $equip;
                                                if ($desc) $installations[] = $desc;
                                            }
                                        }
                                    }
                                }
                            }
                            
                            if (empty($installations) && !empty($napt->demande->lieu_execution)) {
                                $installations[] = $napt->demande->lieu_execution;
                            }
                            
                            $installations = array_unique(array_filter($installations));
                        @endphp
                        {{ !empty($installations) ? implode(', ', $installations) : '-' }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 3px;">{{ $napt->demande->designation ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ccc; padding: 3px;">
                        {{ $napt->ddt ? \Carbon\Carbon::parse($napt->ddt)->format('d/m/Y H:i') : 'N/A' }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 3px; text-align: center;">
                        @if($napt->ddt && $napt->dft)
                            {{ \Carbon\Carbon::parse($napt->ddt)->diffInDays(\Carbon\Carbon::parse($napt->dft)) + 1 }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="border: 1px solid #ccc; padding: 3px;">
                        {{ $napt->dft ? \Carbon\Carbon::parse($napt->dft)->format('d/m/Y H:i') : 'N/A' }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 3px;">{{ $napt->renseignementN ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 15px; padding: 8px; background-color: #e8f4fc; border: 1px solid #b3d9f2; border-radius: 4px;">
            <p style="font-size: 9px; color: #1e5f8a; font-weight: bold; margin-bottom: 3px;">RESUME : {{ $notes->count() }} NAPT(s)</p>
            <p style="font-size: 8px; color: #2b7eb8;">Semaine {{ $semaine }}/{{ $annee }} - Les fiches detaillees suivent ci-apres.</p>
        </div>
    </div>

    @foreach($notes as $note)
    <div class="napt-page">
        <!-- En-tête -->
        <div class="header clearfix">
            <div class="header-left">
                @if($logoData)
                    <img class="logo-img" src="{{ $logoData }}" alt="Logo Senelec">
                @endif
                <p style="font-size: 9px; margin-bottom: 8px;"><b>DESA/DESE</b></p>
            </div>
            <div class="header-right">
                <div class="title">
                    <h2>NOTE D'ARRÊT POUR TRAVAUX N° <b>{{ $note->numero_note }}</b></h2>
                    <p>Dakar, le <b>{{ \Carbon\Carbon::parse($note->date)->format('d/m/Y') }}</b></p>
                    <p>De la semaine S<b>{{ $note->numero_semaine }}</b></p>
                </div>
            </div>
        </div>

        <!-- Layout principal -->
        <table class="main-layout">
            <tr>
                <!-- Calendrier début -->
                <td class="sidebar-left">
                    <h4>Début<br>Mois</h4>
                    <ul class="calendar-list">
                        <li>N° {{ \Carbon\Carbon::parse($note->ddt)->format('m') }}</li>
                        <li>Date</li>
                        @for ($i = 1; $i <= 31; $i++)
                            <li class="{{ $i == \Carbon\Carbon::parse($note->ddt)->day ? 'highlight-date' : '' }}">{{ $i }}</li>
                        @endfor
                    </ul>
                </td>

                <!-- Contenu principal -->
                <td class="main-content">
                    <div class="warning-box">EN AUCUN CAS CETTE NOTE NE PEUT TENIR LIEU D'AUTORISATION DE TRAVAIL</div>
                    
                    @php
                        $etabliUser = $note->etabliPar;
                        $isInterimEtabli = $etabliUser && method_exists($etabliUser, 'estInterimaireA') && $etabliUser->estInterimaireA('desa', $note->date);
                    @endphp
                    <p class="info-line">
                        Etablie par : <b>
                        @if($isInterimEtabli)
                            {{ $etabliUser->name }} <span class="red-text">(PI)</span>
                        @elseif($etabliUser)
                            {{ $etabliUser->name }}
                        @else
                            N/A
                        @endif
                        </b> &nbsp;&nbsp; Fonction : <b>{{ $etabliUser?->poste ?? 'N/A' }}</b>
                    </p>
                    <p class="info-line">Demandée par : <b>{{ $note->demande->demandeur->name ?? 'N/A' }}</b> &nbsp;&nbsp; Fonction : <b>{{ $note->demande->demandeur->poste ?? 'N/A' }}</b></p>

                    <table class="data-table" style="margin-top: 5px;">
                        <tr>
                            <td style="width: 28%;"><b>Installation à consigner :</b></td>
                            <td><b>
                                @if($note->demande->lieu_execution)
                                    Lieu : {{ $note->demande->lieu_execution }}
                                    @if($note->demande->lieu_code)({{ $note->demande->lieu_code }})@endif
                                    <br>
                                @endif
                                @if(isset($note->demande->mode_saisie) && $note->demande->mode_saisie === 'manuel')
                                    {!! nl2br(e($note->demande->ouvrages_consigner_manuel ?? '')) !!}
                                @elseif($note->demande->ouvrage_type === 'ligne' && $note->demande->lignes_oracle)
                                    @php $lignesData = json_decode($note->demande->lignes_oracle, true); @endphp
                                    @if($lignesData && is_array($lignesData))
                                        Lignes : 
                                        @foreach($lignesData as $ligne)
                                            @if(is_array($ligne) && isset($ligne['description']))
                                                {{ $ligne['description'] }}{{ isset($ligne['code']) ? ' ('.$ligne['code'].')' : '' }}@if(!$loop->last), @endif
                                            @elseif(is_string($ligne))
                                                {{ $ligne }}@if(!$loop->last), @endif
                                            @endif
                                        @endforeach
                                    @endif
                                @elseif($note->demande->ouvrage_type === 'poste' && $note->demande->equipements_oracle)
                                    @php
                                        $equipData = json_decode($note->demande->equipements_oracle, true);
                                        $dernierNiveauEquips = [];
                                        if (is_array($equipData)) {
                                            $niveaux = [];
                                            foreach ($equipData as $k => $v) {
                                                if (preg_match('/level_(\d+)/', $k, $m) && is_array($v) && !empty($v)) {
                                                    $niveaux[$m[1]] = $v;
                                                }
                                            }
                                            if (!empty($niveaux)) {
                                                $dernierNiveauEquips = $niveaux[max(array_keys($niveaux))];
                                            }
                                        }
                                    @endphp
                                    @if(!empty($dernierNiveauEquips))
                                        Équipements : @foreach($dernierNiveauEquips as $eq)
                                            {{ is_array($eq) ? ($eq['description'] ?? $eq['code'] ?? '') : $eq }}@if(!$loop->last), @endif
                                        @endforeach
                                    @endif
                                @endif
                            </b></td>
                        </tr>
                        <tr>
                            <td><b>Travaux à réaliser sur :</b></td>
                            <td><b>
                                @if(isset($note->demande->mode_saisie) && $note->demande->mode_saisie === 'manuel')
                                    {!! nl2br(e($note->demande->ouvrages_installer_manuel ?? '')) !!}
                                @elseif($note->demande->ouvrage_type_installer === 'ligne_installer' && $note->demande->lignes_installer_oracle)
                                    @php $lignesInstallerData = json_decode($note->demande->lignes_installer_oracle, true); @endphp
                                    @if($lignesInstallerData && is_array($lignesInstallerData))
                                        @foreach($lignesInstallerData as $ligne)
                                            @if(is_array($ligne) && isset($ligne['description']))
                                                {{ $ligne['description'] }}{{ isset($ligne['code']) ? ' ('.$ligne['code'].')' : '' }}@if(!$loop->last), @endif
                                            @elseif(is_string($ligne))
                                                {{ $ligne }}@if(!$loop->last), @endif
                                            @endif
                                        @endforeach
                                    @endif
                                @elseif($note->demande->ouvrage_type_installer === 'poste_installer' && $note->demande->equipements_installer_oracle)
                                    @php
                                        $equipInstallerData = json_decode($note->demande->equipements_installer_oracle, true);
                                        $dernierNiveauEquipsInstaller = [];
                                        if (is_array($equipInstallerData)) {
                                            $niveaux = [];
                                            foreach ($equipInstallerData as $k => $v) {
                                                if (preg_match('/level_(\d+)/', $k, $m) && is_array($v) && !empty($v)) {
                                                    $niveaux[$m[1]] = $v;
                                                }
                                            }
                                            if (!empty($niveaux)) {
                                                $dernierNiveauEquipsInstaller = $niveaux[max(array_keys($niveaux))];
                                            }
                                        }
                                    @endphp
                                    @if(!empty($dernierNiveauEquipsInstaller))
                                        @foreach($dernierNiveauEquipsInstaller as $eq)
                                            {{ is_array($eq) ? ($eq['description'] ?? $eq['code'] ?? '') : $eq }}@if(!$loop->last), @endif
                                        @endforeach
                                    @endif
                                @endif
                            </b></td>
                        </tr>
                        <tr>
                            <td><b>Consistance sommaire des travaux :</b></td>
                            <td><b>{{ $note->demande->designation ?? 'N/A' }}</b></td>
                        </tr>
                    </table>

                    <!-- Détails et Contacts côte à côte -->
                    <table class="two-columns">
                        <tr>
                            <td>
                                <table class="data-table">
                                    <thead>
                                        <tr><th></th><th>Date</th><th>Heure</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>Retrait de l'exploitation</b></td>
                                            <td><b>{{ \Carbon\Carbon::parse($note->dre)->format('d/m/Y') }}</b></td>
                                            <td><b>{{ \Carbon\Carbon::parse($note->dre)->format('H:i') }}</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Début des travaux</b></td>
                                            <td><b>{{ \Carbon\Carbon::parse($note->ddt)->format('d/m/Y') }}</b></td>
                                            <td><b>{{ \Carbon\Carbon::parse($note->ddt)->format('H:i') }}</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Fin des travaux</b></td>
                                            <td><b>{{ \Carbon\Carbon::parse($note->dft)->format('d/m/Y') }}</b></td>
                                            <td><b>{{ \Carbon\Carbon::parse($note->dft)->format('H:i') }}</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Remise à l'exploitation</b></td>
                                            <td><b>{{ \Carbon\Carbon::parse($note->drex)->format('d/m/Y') }}</b></td>
                                            <td><b>{{ \Carbon\Carbon::parse($note->drex)->format('H:i') }}</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Délai max de restitution</b></td>
                                            <td><b>@if(isset($note->demande->dmrp)){{ \Carbon\Carbon::parse($note->demande->dmrp)->format('H:i') }}@else N/A @endif</b></td>
                                            <td><b class="blue-text">@if($note->demande->dmrp_restitution)Avec restitution le soir @endif</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <table class="data-table">
                                    <thead>
                                        <tr><th></th><th>Nom</th><th>Fonction</th><th>Adresse</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>Chargé(s) consignation</b></td>
                                            <td><b>@if(isset($note->chargesConsignation) && $note->chargesConsignation->isNotEmpty())
                                                @foreach($note->chargesConsignation as $cc){{ $cc->nom }}@if($cc->telephone) ({{ $cc->telephone }})@endif @if(!$loop->last), @endif @endforeach
                                            @endif</b></td>
                                            <td><b>@if(isset($note->chargesConsignation) && $note->chargesConsignation->isNotEmpty())
                                                @foreach($note->chargesConsignation as $cc){{ $cc->fonction }}@if(!$loop->last), @endif @endforeach
                                            @endif</b></td>
                                            <td><b>{{ $note->adresse_charges_consignation ?? '' }}</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Correspondants</b></td>
                                            <td><b>@if(isset($note->correspondants) && $note->correspondants->isNotEmpty())
                                                @foreach($note->correspondants as $corr){{ $corr->nom }}@if(!$loop->last), @endif @endforeach
                                            @else N/A @endif</b></td>
                                            <td><b>@if(isset($note->correspondants) && $note->correspondants->isNotEmpty())
                                                @foreach($note->correspondants as $corr){{ $corr->fonction }}@if(!$loop->last), @endif @endforeach
                                            @else N/A @endif</b></td>
                                            <td><b>{{ $note->adresse_correspondants ?? 'N/A' }}</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Chargé(s) travaux</b></td>
                                            <td><b>{{ optional($note->demande->charge_travaux_info)->nom ?? 'N/A' }} @if(optional($note->demande->charge_travaux_info)->telephone)({{ $note->demande->charge_travaux_info->telephone }})@endif</b></td>
                                            <td><b>{{ optional($note->demande->charge_travaux_info)->type === 'externe' ? 'Externe' : (optional($note->demande->chargeTravaux)->poste ?? 'N/A') }}</b></td>
                                            <td><b>{{ optional($note->demande->charge_travaux_info)->entreprise ?? 'N/A' }}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div style="margin-top: 8px;">
                        <p class="small-text">Indications éventuelles concernant les manœuvres et condamnations à effectuer à la diligence du chef de consignation: Consignes habituelles de mise hors tension et de consignation des ouvrages à consigner listés dans la présente note</p>
                        <p class="center-text small-text" style="margin-top: 5px;"><b>Indications complémentaires: <br><span class="red-text">à la fin des travaux, les travées des postes seront ré-aiguillées suivant la configuration initiale sauf indication contraire par le dispatching</span></b></p>
                        @if($note->renseignementN)
                            <p style="margin-top: 5px;" class="small-text"><b>Commentaires : {{ $note->renseignementN }}</b></p>
                        @endif
                    </div>

                    <!-- Signatures -->
                    <table class="signataire-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Destinataires</th>
                                <th style="width: 25%;">Etablie par</th>
                                <th style="width: 25%;">Vérifiée par</th>
                                <th style="width: 25%;">Validée par</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-size: 8px; vertical-align: middle;">
                                    @if(isset($note->services) && $note->services->isNotEmpty())
                                        @foreach($note->services as $service){{ $service->nom }}@if(!$loop->last), @endif @endforeach
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $etabliUser = $note->etabliPar;
                                        $signatureEtabli = $etabliUser && $etabliUser->signature ? $etabliUser->signature : null;
                                        $signatureEtabliUrl = $getSignatureBase64($signatureEtabli);
                                    @endphp
                                    @if(in_array($note->statut, ['en attente de vérification', 'vérifiée', 'en cours d\'exécution', 'validée', 'executée', 'annulée']))
                                        @if($signatureEtabliUrl)
                                            <img src="{{ $signatureEtabliUrl }}" class="signature-img">
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $verifieUser = $note->verifiePar;
                                        $signatureVerifie = $verifieUser && $verifieUser->signature ? $verifieUser->signature : null;
                                        $signatureVerifieUrl = $getSignatureBase64($signatureVerifie);
                                    @endphp
                                    @if(in_array($note->statut, ['vérifiée', 'en cours d\'exécution', 'validée', 'executée', 'annulée']))
                                        @if($signatureVerifieUrl)
                                            <img src="{{ $signatureVerifieUrl }}" class="signature-img">
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $valideUser = $note->validePar;
                                        $signatureValide = $valideUser && $valideUser->signature ? $valideUser->signature : null;
                                        $signatureValideUrl = $getSignatureBase64($signatureValide);
                                    @endphp
                                    @if(in_array($note->statut, ['validée', 'en cours d\'exécution', 'executée', 'annulée']))
                                        @if($signatureValideUrl)
                                            <img src="{{ $signatureValideUrl }}" class="signature-img">
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <!-- Calendrier fin -->
                <td class="sidebar-right">
                    <h4>Fin<br>Mois</h4>
                    <ul class="calendar-list">
                        <li>N° {{ \Carbon\Carbon::parse($note->dft)->format('m') }}</li>
                        <li>Date</li>
                        @for ($i = 1; $i <= 31; $i++)
                            <li class="{{ $i == \Carbon\Carbon::parse($note->dft)->day ? 'highlight-date' : '' }}">{{ $i }}</li>
                        @endfor
                    </ul>
                </td>
            </tr>
        </table>

        <p class="footer-text">NAPT {{ $loop->iteration }}/{{ $loop->count }} - Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
    @endforeach
</body>
</html>

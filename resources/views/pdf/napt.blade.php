<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note d'arrêt - Senelec</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f3f3f3;
}

.container {
    width: 90%;
    margin: 20px auto;
    background-color: white;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.logo img {
    text-align: left;
    width: 140px;
    margin: auto;
    margin-bottom: 5px;
}

.title {
    text-align: right;
}

h2, h3 {
    text-align: center;
    margin: 10px 0;
}

.content-with-sidebar {
    display: flex;
    justify-content: space-between;
}

.sidebar {
    width: 10%;
    text-align: center;
    background-color: #e8e8e8;
    padding: 10px;
    border-radius: 5px;
}

.main-content {
    width: 70%;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table, th, td {
    border: 1px solid black;
    padding: 8px;
    text-align: left;
}

.footer {
    margin-top: 30px;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 5px;
    text-align: center;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


.signataire-table thead tr th {
        padding: 5px;
        border: 1px solid #ccc;
        font-size: 12px;
}
.signataire-table .signRow {
    height: 200px;
}
.signataire-table tbody td {
    text-align: center;
    padding: 10px;
    border: 1px solid #ccc;
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
    /* height: 180px; */
}
.signValid .cachet img,
.signValid .signature img {
    object-fit: cover !important;
}

.signValid .signature h4 {
    display: block !important;
    visibility: visible !important;
    color: red !important;
    border: 1px solid red !important;
    font-size: 12px !important;
    font-weight: bold !important;
    text-align: center !important;
    margin: 5px auto !important;
    padding: 3px !important;
    width: auto !important;
    max-width: 100px !important;
    min-width: 80px !important;
    position: absolute !important;
    bottom: 5px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    background-color: transparent !important;
    z-index: 3 !important;
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: clip !important;
    height: auto !important;
    min-height: 18px !important;
    line-height: 1.1 !important;
    word-wrap: break-word !important;
    font-weight: bold !important;
}

/* Personnalisation du modal */
.modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.3) !important;
    backdrop-filter: blur(5px) !important;
    -webkit-backdrop-filter: blur(5px) !important;
}

.modal.show {
    background-color: transparent;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.modal-title {
    font-weight: bold;
}

@media print {
    /* Orientation paysage pour l'impression */
    @page {
        size: A4 portrait;
        margin: 0.2in;
    }

    /* Masquer tous les boutons */
    button,
    form {
        display: none;
    }

    /* Masquer les autres éléments spécifiques si nécessaire */
    .no-print {
        display: none;
    }

    /* Ajuster la taille du contenu pour tenir sur une seule page */
    body {
        font-size: 10px;
        line-height: 1.1;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
        print-color-adjust: exact;
        transform: scale(0.88);
        transform-origin: top center;
        margin: 0 !important;
        padding: 0 !important;
        height: 100vh;
        overflow: hidden;
    }

    .container {
        width: 100%;
        margin: 0 auto;
        padding: 5px;
        box-shadow: none;
        max-width: 100%;
    }

    /* Optimiser l'en-tête */
    header {
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo img {
        width: 85px;
        margin-bottom: 2px;
    }

    .title h2 {
        font-size: 15px;
        margin: 3px 0;
    }

    .title p {
        font-size: 10px;
        margin: 2px 0;
    }

    /* Optimiser le contenu principal */
    .content-with-sidebar {
        display: flex;
        gap: 6px;
    }

    .sidebar {
        width: 70px;
        font-size: 8px;
    }

    .sidebar h3,
    .sidebar h4 {
        font-size: 9px;
        margin: 3px 0;
    }

    .sidebar ul li {
        padding: 1px;
        font-size: 8px;
    }

    .main-content {
        flex: 1;
        padding: 0;
    }

    .main-content h3 {
        font-size: 11px;
        margin: 8px 0;
        padding: 3px;
    }

    .main-content p {
        font-size: 10px;
        margin: 3px 0;
    }

    /* Optimiser les tableaux */
    table {
        font-size: 9px;
        width: 100%;
        margin-bottom: 4px;
        page-break-inside: avoid;
    }

    th, td {
        padding: 2px 3px;
        font-size: 9px;
    }

    /* Styles spécifiques pour les tableaux avec font-size inline */
    td[style*="font-size: 12px"],
    th[style*="font-size: 12px"] {
        font-size: 10px !important;
    }

    td[style*="font-size: 10px"] {
        font-size: 9px !important;
    }

    /* Optimisation spécifique pour le premier tableau (info-table) */
    .info-table td[style*="font-size: 12px"],
    .info-table strong[style*="font-size: 12px"] {
        font-size: 9px !important;
    }

    /* Forcer l'impression des couleurs d'arrière-plan */
    li[style*="background-color: green"] {
        background-color: green !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
        print-color-adjust: exact;
        color: white !important;
        font-weight: bold !important;
    }

    /* Optimiser la section des signatures */
    .signataire-table {
        font-size: 8px !important;
        margin-top: 10px !important;
        margin-bottom: 5px !important;
    }

    .signataire-table thead tr th {
        padding: 3px !important;
        font-size: 9px !important;
        height: 25px !important;
    }

    .signataire-table tbody td {
        padding: 3px !important;
        font-size: 8px !important;
        height: 90px !important;
        vertical-align: top !important;
    }

    .signataire-table .signRow {
        height: 90px !important;
    }

    .signValid {
        height: 85px !important;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 3px;
    }

    .signValid .signature {
        height: 110px !important;
        width: 100% !important;
        display: block !important;
        text-align: center !important;
        position: relative !important;
        padding: 5px;
    }

    .signValid .signature img {
        max-width: 90px !important;
        max-height: 70px !important;
        object-fit: contain !important;
        margin: 0 auto !important;
        display: block !important;
        position: relative !important;
        z-index: 1 !important;
    }

    .signValid .signature h4 {
        font-size: 9px !important;
        font-weight: bold !important;
        margin: 0 !important;
        padding: 2px 3px !important;
        text-align: center !important;
        line-height: 1.1 !important;
        border: 1px solid red !important;
        color: red !important;
        display: block !important;
        visibility: visible !important;
        position: absolute !important;
        bottom: 25px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        background-color: transparent !important;
        z-index: 3 !important;
        width: 85px !important;
        max-width: 90px !important;
        height: auto !important;
        min-height: 14px !important;
        white-space: nowrap !important;
        overflow: visible !important;
        text-overflow: clip !important;
        font-weight: bold !important;
    }

    .signValid .cachet {
        display: none !important;
    }

    /* Éviter les sauts de page */
    * {
        page-break-inside: avoid;
    }

    /* Forcer le contenu à tenir sur une page */
    html {
        height: 100vh;
        overflow: hidden;
    }

    /* Optimiser les espaces */
    h1, h2, h3, h4, h5, h6 {
        margin: 2px 0;
        padding: 0;
    }

    br {
        line-height: 0.3;
    }

    /* Réduire les marges des listes */
    ul, ol {
        margin: 1px 0;
        padding-left: 10px;
    }

    li {
        margin: 0;
        padding: 0;
    }

    /* Améliorer l'utilisation de l'espace */
    .mission-signataires {
        margin-top: 10px;
        margin-bottom: 3px;
    }
}

</style>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <img style="display: inline-block;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAADvCAMAAAB/qBnpAAAC/VBMVEVHcEy8Wmffc1bUV2Hmb1DqWUbpZ0rqWEjtf0vsW0Hub0TvckTvdEXzmkzsfEvtZ0P0cEX9d0jweUXxf0b+ikrygkbvlE/tYUL7Y0buaUPubEPvd0XwfEbxhUfwl1LsXULtY0LtZUPyikfxX0PsX0Lyh0f+m0z0nlHsW0H0lUnxVUDrV0HrWUHzjUjzkUjrVUH0mUn0U0zsU0DqUkD1nUrpT0PqUED3p0v/r1HmTkb2oUrkTUrdSVviS0zgS1HhTE/eSlfmUV7eSlTcSVbdSlfbSVnZSFy6RXHHRm3XR1/un1HhS23VR2LRRW7URW7MRmzTRmTSRmbSRWnRRWjPQ27MRG/QRWrZR3bPRGvORG3NQ3BzSolnSo1mS45YTJNSTZRWTZRhS5BkS49oS45rSoxvSotySYp2SIh7R4ayR3SyQnSvQnW1RHPGPmVdTJFWUp92SYl/T5KMRoKDI2iCKm6LJWKYLWOjOXCrPGVWTZN6SId+SIaBR4WGJWaIJGOSJl1aTJKNJWCkL1mPJV6uNleAO3tRTZWUJlu3M02+PVLPQUqRIViYJlfVPlaWJllNTpebJlXaQE+qLE2ER4SdJlOiJ1CIPXufJ1KHRoOXG0qjIEakJ07v0dj69Pa7YoCdGEK3TGn////XmKenKEtYQYliPoRrPYF1PH+FGFjmQzeQcaSnjreaXpK1eZ/Fk7GtZJGfTIGYOnPJe5K8T2+mGjzyk4rrgIDsbmbq3+n+/PzYuc3axdjOq8Xcqrn26u7pt7/yxMv23eDyoJypQ3esQ3arKEiaRHy6THC+Um6PRoCRRX+URX6ONHGtJEKeRHuuKEa+TWO2nsDql6KuHDewKUSYRX2cRHyyKULAVm3haXWgRHq4SXHCWWzHYWnUYnC0KUC3KT6mQ3ijQ3m3R3LDW2vFXWrJZGfLZ2bNamXFWFbPbWTRcWPTdGHVeGDXe1/ZfV7bgl3af13dhFzVdGLii1nfh1rxlGDkkFfnlFX4o1jqmFTsnFOdRX6xRXbMZWlfrp8ZAAAA/3RSTlMAECY6Z5Stxd7m////jUv/////////sP/////////K///////////k//////////////////////////+y////5v///////1F9/////9zylv////+vxv//////Ij9qjbHj////////2qir////+/////////v//P7+////////////+///////+/z7//////////z///////////////////////////////////////////////////////////////////////////////////////////////////////////////////r///////////+V///////////V7tgxTC8eAAAkXElEQVR4AezSBXUDURQA0WX6zBwm/wr7Um6jIDAS7pnqAaqbtuv6YZzmBSGEMZ6mcSAdberqV68a2g3jzLiAOEcLWEFSCQ0ZNVlCm5cS1HT96Lz3nIfAQojL8jFWSlLKLHM2RkNlRehzL1bTflwD1Gazha5U8R9WzgoypuwAbLJP61V3w7z3fr8+HJwDLfYL62qVvqwMWJVyPO706XQm9BmfGmaAulzW639Yt2O9kWcXPI7DQBSA1SY5BtEyM0Oudp0yM/z/n3PPzeRcstyTluxORSvWpzcvtldiSSvMxc3l5aG/XvHygq+327d3d/f3hAUrwprbQrIirAfMo4xXen3W7zOknp6eCIuCdWLYwtgKWI/gel4PrnTwNbz9k8koLE1lqS2cDRbmBsvI3OfyPvHwNpvNJljXC1iGLSSsm+dLcfXTaapfn3nIOU+w4mAR1mJlqS1csALW8/NldPXLYaowzPHcPyx9v/9YBQsTic20u1QYrscyVNa8FWMsH135DnYVqAoFDdaWBous9FhSK3ItXF6xEBZKMRatofHkQFiLWzhjlWd5t5orFZTDCqgmWFyHpT6G+n5f2EI5Iorc+Sz61bBSBhYli2uw9B/Dg6VYjLCEANdmypGyqtXK5QolK8HKmLE0/X4jsUCVYIFKIFtXaRc2UFLFWKUXwZrdQsKSXL/s38BavV4nrMJLYjFVWYK0fKup0sVarQGs10yWIKzI8ppHrJqN5lslS5CWtbFqAevtkmWvFmLVarXeLlmUrU0bP4KIVRtYjRdPFkYliykrGguz1anW2rAC1ksmS2qBSmHlVbTs7a1uG1aEtfg1fMHrzoTK6myh2du9BKupsEqGu+GJ5qFUg8XUfac/q+VbtYI9WOmwoKXF+v3frw75WEuN/Mues3x3MOgZsVZ/z9o3vmdNph9LyZ9cTM+Sr+CgNzRg5VZ8KTU+O6hsCfrF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" width="100" alt="Logo Senelec">
                <P><b>DESA/DESE</b></P>
            </div>
            <div class="title">
                <h2 style="font-size: 18px;">NOTE D'ARRÊT POUR TRAVAUX N° <b>{{ $note->numero_note }}</b></h2>
                {{-- <p>Demandée par : <b>{{ $note->demande->demandeur->name }}</b></p> --}}
                <p>Dakar, le <b>{{ \Carbon\Carbon::parse($note->date)->format('d/m/Y') }}</b></p>
                <p>De la semaine S <b>{{ $note->numero_semaine }}</b></p>
            </div>
        </header>

        {{-- Affichage des motifs de retour si statut retournée --}}
        @if($note->statut === 'retournée')
            <div class="no-print" style="background-color: #fee2e2; border: 2px solid #dc2626; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <h4 style="color: #dc2626; margin: 0 0 10px 0; font-size: 16px;">
                    <strong>⚠️ Note retournée</strong>
                </h4>
                @if($note->motifbis)
                    <div style="margin-bottom: 10px;">
                        <strong style="color: #991b1b;">Motif du retour (Valideur) :</strong>
                        <p style="margin: 5px 0; padding: 10px; background-color: white; border-radius: 4px; border-left: 4px solid #dc2626;">
                            {{ $note->motifbis }}
                        </p>
                        @if($note->retourne2)
                            <small style="color: #6b7280;">Retournée par : {{ $note->retourne2->name }} le {{ $note->updated_at->format('d/m/Y à H:i') }}</small>
                        @endif
                    </div>
                @endif
                @if($note->motif)
                    <div>
                        <strong style="color: #991b1b;">Motif du retour (Vérificateur) :</strong>
                        <p style="margin: 5px 0; padding: 10px; background-color: white; border-radius: 4px; border-left: 4px solid #dc2626;">
                            {{ $note->motif }}
                        </p>
                        @if($note->retourne1)
                            <small style="color: #6b7280;">Retournée par : {{ $note->retourne1->name }}</small>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <div class="content-with-sidebar">
            <!-- Colonne de gauche - Calendrier début -->
            <div class="sidebar">
                <h3>Début</h3>
                <h4>Mois</h4>
                <ul style="list-style: none;margin:0;padding:0">
                    <li style="text-align: center;border:1px solid #000;">N° {{ \Carbon\Carbon::parse($note->ddt)->format('m') }}</li>
                    <li style="text-align: center;border:1px solid #000;">Date</li>
                    @for ($i = 1; $i <= 31; $i++)
                        <li style="text-align: center; border:1px solid #000;
                            {{ $i == \Carbon\Carbon::parse($note->ddt)->format('d') ? 'background-color: green;' : '' }}">
                            {{ $i }}
                        </li>
                    @endfor
                </ul>
            </div>

            <!-- Contenu principal -->
            <div class="main-content">
                <h3 style="border: #000 solid 3px; font-size: 14px; font-weight: bold;">EN AUCUN CAS CETTE NOTE NE PEUT TENIR LIEU D'AUTORISATION DE TRAVAIL</h3>
                <br><br>
                <div>
                    <p>
                        Etablie par : <b>
                        @php
                            $etabliUser = $note->etabliPar;
                            $isInterimEtabli = $etabliUser && method_exists($etabliUser, 'estInterimaireA') && $etabliUser->estInterimaireA($note->date, 'desa');
                        @endphp
                        @if($isInterimEtabli)
                            {{ $etabliUser->name }} <span style="color: red;">(PI)</span>
                        @elseif($etabliUser)
                            {{ $etabliUser->name }}
                        @else
                            N/A
                        @endif
                        </b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fonction : <b>{{ $etabliUser?->poste ?? 'N/A' }}</b>
                    </p>
                    <p>Demandée par : <b>{{ $note->demande->demandeur->name }}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fonction : <b>{{ $note->demande->demandeur->poste ?? 'N/A' }}</b></p>
                </div>
                <br>
                <table class="info-table">
                    <tr>
                        <td><b style="font-size: 12px;">Installation à consigner :</b></td>
                        <td><b style="font-size: 12px;">
                            @if($note->demande->lieu_execution)
                                Lieu d'éxècution : {{ $note->demande->lieu_execution }}
                                @if($note->demande->lieu_code)
                                    ({{ $note->demande->lieu_code }})
                                @endif
                                <br>
                            @endif
			    {{-- Mode manuel : afficher le texte saisi --}}
                            @if(isset($note->demande->mode_saisie) && $note->demande->mode_saisie === 'manuel')
                                {!! nl2br(e($note->demande->ouvrages_consigner_manuel ?? 'Non renseigné')) !!}
                            @else
                            {{-- Mode GMAO --}}
                            @if($note->demande->ouvrage_type === 'ligne' && $note->demande->lignes_oracle)
                                @php
                                    $lignesData = json_decode($note->demande->lignes_oracle, true);
                                @endphp
                                @if($lignesData && is_array($lignesData))
                                    Lignes :
                                    <ul>
                                        @foreach($lignesData as $ligne)
                                            <li>
                                                @if(is_array($ligne) && isset($ligne['description']))
                                                    {{ $ligne['description'] }}
                                                    @if(isset($ligne['code']))
                                                        ({{ $ligne['code'] }})
                                                    @endif
                                                @elseif(is_string($ligne))
                                                    {{ $ligne }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endif

                            @if($note->demande->ouvrage_type === 'poste' && $note->demande->equipements_oracle)
                                @php
                                    $equipementsData = json_decode($note->demande->equipements_oracle, true);
                                    
                                    // Fonction pour récupérer la description d'un équipement
                                    if (!function_exists('getEquipementDescriptionPDF')) {
                                        function getEquipementDescriptionPDF($code) {
                                            if (empty($code) || !is_string($code)) return $code;

                                            try {
                                                // Connexion Oracle directe pour récupérer la description
                                                $host = '10.101.3.171';
                                                $service = 'CWPROD';
                                                $port = '1521';
                                                $user = 'smdt';
                                                $pass = 'Smdt2025';

                                                $connStr = "//{$host}:{$port}/{$service}";
                                                $conn = @oci_connect($user, $pass, $connStr, 'AL32UTF8');

                                                if (!$conn) {
                                                    return $code; // Retourner le code si connexion échoue
                                                }

                                                $sql = "SELECT ereq_description FROM coswin.t_equipment WHERE ereq_code = :code";
                                                $stid = oci_parse($conn, $sql);
                                                oci_bind_by_name($stid, ':code', $code);
                                                oci_execute($stid);

                                                $row = oci_fetch_assoc($stid);
                                                oci_free_statement($stid);
                                                oci_close($conn);

                                                return $row ? $row['EREQ_DESCRIPTION'] : $code;
                                            } catch (\Exception $e) {
                                                return $code; // En cas d'erreur, retourner le code
                                            }
                                        }
                                    }
				    // Récupérer uniquement le dernier niveau d'équipements
                                    $dernierNiveauEquipements = [];
                                    if ($equipementsData && is_array($equipementsData)) {
                                        $niveauxAvecData = [];
                                        foreach ($equipementsData as $levelKey => $levelData) {
                                            if (preg_match('/level_(\d+)/', $levelKey, $m) && is_array($levelData) && !empty($levelData)) {
                                                $niveauxAvecData[$m[1]] = $levelData;
                                            }
                                        }
                                        if (!empty($niveauxAvecData)) {
                                            $dernierNiveau = max(array_keys($niveauxAvecData));
                                            $dernierNiveauEquipements = $niveauxAvecData[$dernierNiveau];
                                        }
                                    }
                                @endphp
                                @if(!empty($dernierNiveauEquipements))
                                    Équipement :
                                    <ul>
                                        @foreach($dernierNiveauEquipements as $dernierEquipement)
                                            @php
                                                if (is_array($dernierEquipement)) {
                                                    $equipementCode = $dernierEquipement['code'] ?? '';
                                                    $description = $dernierEquipement['description'] ?? ($equipementCode ? getEquipementDescriptionPDF($equipementCode) : '');
                                                } else {
                                                    $equipementCode = $dernierEquipement;
                                                    $description = getEquipementDescriptionPDF($dernierEquipement);
                                                }
                                                $descriptionCourte = $description;
                                                if (strpos($description ?? '', ' - ') !== false) {
                                                    $parties = explode(' - ', $description);
                                                    $descriptionCourte = end($parties);
                                                }
                                            @endphp
                                            <li>
                                                {{ $descriptionCourte }}
                                                @if($description !== $equipementCode)
                                                    ({{ $equipementCode }})
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
				@endif
				@endif
                                @endif
                           
                        </b></td>
                    </tr>
                    <tr>
                        <td><b style="font-size: 12px;">Travaux à réaliser sur :</b></td>
                        <td><b style="font-size: 12px;">
			    {{-- Mode manuel : afficher le texte saisi --}}
                            @if(isset($note->demande->mode_saisie) && $note->demande->mode_saisie === 'manuel')
                                {!! nl2br(e($note->demande->ouvrages_installer_manuel ?? 'Non renseigné')) !!}
                            @else
                            {{-- Mode GMAO --}}
                            @if($note->demande->ouvrage_type_installer === 'ligne_installer' && $note->demande->lignes_installer_oracle)
                                @php
                                    $lignesInstallerData = json_decode($note->demande->lignes_installer_oracle, true);
                                @endphp
                                @if($lignesInstallerData && is_array($lignesInstallerData))
                                    Lignes à installer :
                                    <ul>
                                        @foreach($lignesInstallerData as $ligne)
                                            <li>
                                                @if(is_array($ligne) && isset($ligne['description']))
                                                    {{ $ligne['description'] }}
                                                    @if(isset($ligne['code']))
                                                        ({{ $ligne['code'] }})
                                                    @endif
                                                @elseif(is_string($ligne))
                                                    {{ $ligne }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endif

                            @if($note->demande->ouvrage_type_installer === 'poste_installer' && $note->demande->equipements_installer_oracle)
                                @php
                                    $equipementsInstallerData = json_decode($note->demande->equipements_installer_oracle, true);
                                    
                                    // Fonction pour récupérer la description d'un équipement à installer
                                    if (!function_exists('getEquipementDescriptionPDFInstaller')) {
                                        function getEquipementDescriptionPDFInstaller($code) {
                                            if (empty($code) || !is_string($code)) return $code;

                                            try {
                                                // Connexion Oracle directe pour récupérer la description
                                                $host = '10.101.3.171';
                                                $service = 'CWPROD';
                                                $port = '1521';
                                                $user = 'smdt';
                                                $pass = 'Smdt2025';

                                                $connStr = "//{$host}:{$port}/{$service}";
                                                $conn = @oci_connect($user, $pass, $connStr, 'AL32UTF8');

                                                if (!$conn) {
                                                    return $code; // Retourner le code si connexion échoue
                                                }

                                                $sql = "SELECT ereq_description FROM coswin.t_equipment WHERE ereq_code = :code";
                                                $stid = oci_parse($conn, $sql);
                                                oci_bind_by_name($stid, ':code', $code);
                                                oci_execute($stid);

                                                $row = oci_fetch_assoc($stid);
                                                oci_free_statement($stid);
                                                oci_close($conn);

                                                return $row ? $row['EREQ_DESCRIPTION'] : $code;
                                            } catch (\Exception $e) {
                                                return $code; // En cas d'erreur, retourner le code
                                            }
                                        }
                                    }
				// Récupérer uniquement le dernier niveau d'équipements à installer
                                    $dernierNiveauEquipementsInstaller = [];
                                    if ($equipementsInstallerData && is_array($equipementsInstallerData)) {
                                        $niveauxAvecData = [];
                                        foreach ($equipementsInstallerData as $levelKey => $levelData) {
                                            if (preg_match('/level_(\d+)/', $levelKey, $m) && is_array($levelData) && !empty($levelData)) {
                                                $niveauxAvecData[$m[1]] = $levelData;
                                            }
                                        }
                                        if (!empty($niveauxAvecData)) {
                                            $dernierNiveau = max(array_keys($niveauxAvecData));
                                            $dernierNiveauEquipementsInstaller = $niveauxAvecData[$dernierNiveau];
                                        }
                                    }
                                @endphp
                                @if(!empty($dernierNiveauEquipementsInstaller))
                                    Équipement sur lequel les travaux sont à realiser :
                                    <ul>
                                        @foreach($dernierNiveauEquipementsInstaller as $dernierEquipementInstaller)
                                            @php
                                                if (is_array($dernierEquipementInstaller)) {
                                                    $equipementCode = $dernierEquipementInstaller['code'] ?? '';
                                                    $description = $dernierEquipementInstaller['description'] ?? ($equipementCode ? getEquipementDescriptionPDFInstaller($equipementCode) : '');
                                                } else {
                                                    $equipementCode = $dernierEquipementInstaller;
                                                    $description = getEquipementDescriptionPDFInstaller($dernierEquipementInstaller);
                                                }
                                                $descriptionCourte = $description;
                                                if (strpos($description ?? '', ' - ') !== false) {
                                                    $parties = explode(' - ', $description);
                                                    $descriptionCourte = end($parties);
                                                }
                                            @endphp
                                            @if($description)
                                            <li>
                                                {{ $descriptionCourte }}
                                                @if($description !== $equipementCode)
                                                    ({{ $equipementCode }})
                                                @endif
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
				@endif
				@endif
                            @endif
                        </b></td>
                    </tr>
                    <tr>
                        <td><strong style="font-size: 12px;">Consistance sommaire des travaux </strong></td>
                        <td><b style="font-size: 12px;">{{ $note->demande->designation }}</b></td>
                    </tr>
                </table>

                <br>

                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <!-- Table des détails - côté gauche -->
                    <div style="flex: 1;">
                        <table class="details-table" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="font-size: 11px;"><b>Retrait de l'exploitation</b></td>
                                    <td style="font-size: 11px;"><b>{{ \Carbon\Carbon::parse($note->dre)->format('d/m/Y') }}</b></td>
                                    <td style="font-size: 11px;"><b>{{ \Carbon\Carbon::parse($note->dre)->format('H:i') }}</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;"><b>Début des travaux</b></td>
                                    <td style="font-size: 11px;"><b>{{ \Carbon\Carbon::parse($note->ddt)->format('d/m/Y') }}</b></td>
                                    <td style="font-size: 11px;"><b>{{ \Carbon\Carbon::parse($note->ddt)->format('H:i') }}</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;"><b>Fin des travaux</b></td>
                                    <td style="font-size: 11px;"><b>{{ \Carbon\Carbon::parse($note->dft)->format('d/m/Y') }}</b></td>
                                    <td style="font-size: 11px;"><b>{{ \Carbon\Carbon::parse($note->dft)->format('H:i') }}</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;"><b>Remise à l'exploitation</b></td>
                                    <td style="font-size: 11px;"><b>{{ \Carbon\Carbon::parse($note->drex)->format('d/m/Y') }}</b></td>
                                    <td style="font-size: 11px;"><b>{{ \Carbon\Carbon::parse($note->drex)->format('H:i') }}</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;"><b>Délai max de restitution</b></td>
                                    <td style="font-size: 11px;"><b>@if(isset($note->demande->dmrp))
                                        {{ \Carbon\Carbon::parse($note->demande->dmrp)->format('H:i') }} @else N/A
                                    @endif</b></td>
                                    <td style="font-size: 11px;"><b style="color:blue;">@if($note->demande->dmrp_restitution) Avec restitution le soir @endif</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="flex: 1;">
                        <table class="contacts-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="font-size: 11px;"></th>
                                    <th style="font-size: 11px;">Nom</th>
                                    <th style="font-size: 11px;">Fonction</th>
                                    <th style="font-size: 11px;">Adresse</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-size: 11px;"><b>Chargé(s) consignation</b></td>
                                    <td style="font-size: 11px;"><b>@if(isset($note->chargesConsignation) && $note->chargesConsignation->isNotEmpty())
                                        @foreach($note->chargesConsignation as $sousEquipement)
                                            {{ $sousEquipement->nom }}
                                            @if($sousEquipement->telephone)
                                                {{ $sousEquipement->telephone }}
                                            @endif
                                            @if(!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    @endif</b></td>
                                    <td style="font-size: 11px;"><b>@if(isset($note->chargesConsignation) && $note->chargesConsignation->isNotEmpty())
                                        @foreach($note->chargesConsignation as $sousEquipement)
                                            {{ $sousEquipement->fonction }}
                                            @if(!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    @endif</b></td>
                                    <td style="font-size: 11px;"><b>@if(isset($note->chargesConsignation) && $note->chargesConsignation->isNotEmpty())
                                        @if($note->adresse_charges_consignation)
                                            @foreach(explode(',', $note->adresse_charges_consignation) as $adresse)
                                                    {{ trim($adresse) }}@if(!$loop->last), @endif
                                            @endforeach
                                        @endif
                                    @endif</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;"><b>Correspondants</b></td>
                                    <td style="font-size: 11px;"><b>@if(isset($note->correspondants) && $note->correspondants->isNotEmpty())
                                        @foreach($note->correspondants as $sousEquipement)
                                            {{ $sousEquipement->nom }}
                                            @if(!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    @else
                                        N/A
                                    @endif</b></td>
                                    <td style="font-size: 11px;"><b>@if(isset($note->correspondants) && $note->correspondants->isNotEmpty())
                                        @foreach($note->correspondants as $sousEquipement)
                                            {{ $sousEquipement->fonction }}
                                            @if(!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    @else
                                        N/A
                                    @endif</b></td>
                                    <td style="font-size: 11px;"><b>@if(isset($note->correspondants) && $note->correspondants->isNotEmpty())
                                        @if($note->adresse_correspondants)
                                            @foreach(explode(',', $note->adresse_correspondants) as $adresse)
                                                {{ trim($adresse) }}@if(!$loop->last), @endif
                                            @endforeach
                                        @endif
                                    @else
                                        N/A
                                    @endif</b></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 11px;"><b>Chargé(s) travaux</b></td>
                                    <td style="font-size: 11px;"><b>{{ $note->demande->charge_travaux_info->nom ?? 'N/A' }} {{ $note->demande->charge_travaux_info->telephone ?? '' }}</b></td>
                                    <td style="font-size: 11px;"><b>{{ $note->demande->charge_travaux_info->type === 'externe' ? 'Externe' : ($note->demande->chargeTravaux->poste ?? 'N/A') }}</b></td>
                                    <td style="font-size: 11px;"><b>{{ $note->demande->charge_travaux_info->entreprise ?? 'N/A' }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>       
                </div>
                <br>

                
                <div>
                    <p>Indications éventuelles concernant les manœuvres et condamnations à effectuer à la diligence du chef
                        de consignation: Consignes habituelles de mise hors tension et de consignation des ouvrages à consigner listés dans la présente note</p>
                    <p style="text-align: center;font-size:15px;"><b>Indications complémentaires: <br><br><span style="color: red; font-size:13px;">à la fin des travaux, les travées des postes seront ré-aiguillées suivant la configuration initiale sauf indication contraire par le dispatching</span></b></p>
                    <!-- Section Commentaires ajoutée -->                    
                    <p style="margin: 0; font-size: 15px;"><strong>Commentaires : {{ $note->renseignementN ?? '' }} </strong></p>
                        
                            
                        
                    
                </div>
                <div class="row" style="display: flex; justify-content: space-between; align-items: flex-start;">
                
                <!-- Section Signatures - déplacée ici juste après commentaires -->
                <div class="mission-signataires" style="margin-top: 20px;">
                    <table class="signataire-table" style="font-size: 9px;">
                        <thead>
                            <tr>
                                <th style="border-collapse: collapse; font-size: 9px;"><strong>Destinataires</strong></th>
                                <th style="border-collapse: collapse; font-size: 9px;"><strong>Etablie par</strong></th>
                                <th style="border-collapse: collapse; font-size: 9px;"><strong>Vérifiée par</strong></th>
                                <th style="border-collapse: collapse; font-size: 9px;"><strong>Validée par</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="signRow">
                                <td><p style="font-size:9px;">@if(isset($note->services) && $note->services->isNotEmpty())
                                        @foreach($note->services as $sousEquipement)
                                            {{ $sousEquipement->nom }}
                                            @if(!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    @endif</p>
                                </td>
                                <td>
                                    <div class="signValid">
                                        <div class="signature">
                                            @php
                                                $etabliUser = $note->etabliPar;
                                                $isInterimEtabli = $etabliUser && method_exists($etabliUser, 'estInterimaireA') && $etabliUser->estInterimaireA($note->date, 'desa');
                                                $signatureEtabli = $etabliUser && $etabliUser->signature ? $etabliUser->signature : ($signatureN1 ?? null);
                                            @endphp
                                            @if($note->statut == 'en attente de vérification' || $note->statut == 'vérifiée' || $note->statut == 'en cours d\'exécution' || $note->statut == 'validée' || $note->statut == 'executée' || $note->statut == 'annulée')
                                                @if($signatureEtabli)
                                                    @php
                                                        $signatureUrl = Str::startsWith($signatureEtabli, ['http://', 'https://'])
                                                            ? $signatureEtabli
                                                            : asset('storage/' . ltrim($signatureEtabli, '/'));
                                                    @endphp
                                                    <img src="{{ $signatureUrl }}" alt="Signature de {{ $etabliUser?->name ?? 'N/A' }}" width="100" height="100" style="margin: auto; object-fit: contain;">
                                                @else
                                                    <p style="font-size: 10px; text-align: center; font-weight: bold; margin-top: 30px;">
                                                        {{ $etabliUser?->name ?? 'N/A' }}
                                                        @if(isset($isInterimEtabli) && $isInterimEtabli)
                                                            <span style="color:red;">(PI)</span>
                                                        @endif
                                                    </p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="signValid">
                                        <div class="signature">
                                            @php
                                                $verifieUser = $note->verifiePar;
                                                $isInterimVerifie = $verifieUser && method_exists($verifieUser, 'estInterimaireA') && $verifieUser->estInterimaireA($note->date, 'verificateur');
                                                $signatureVerifie = $verifieUser && $verifieUser->signature ? $verifieUser->signature : ($signatureN2 ?? null);
                                            @endphp
                                            @if($note->statut == 'vérifiée' || $note->statut == 'en cours d\'exécution' || $note->statut == 'validée' || $note->statut == 'executée' || $note->statut == 'annulée')
                                                @if($signatureVerifie)
                                                    @php
                                                        $signatureVerifieUrl = Str::startsWith($signatureVerifie, ['http://', 'https://'])
                                                            ? $signatureVerifie
                                                            : asset('storage/' . ltrim($signatureVerifie, '/'));
                                                    @endphp
                                                    <img src="{{ $signatureVerifieUrl }}" alt="Signature de {{ $verifieUser?->name ?? 'N/A' }}" width="100" height="100" style="margin: auto; object-fit: contain;">
                                                @else
                                                    <p style="font-size: 10px; text-align: center; font-weight: bold; margin-top: 30px;">
                                                        {{ $verifieUser?->name ?? 'N/A' }}
                                                        @if(isset($isInterimVerifie) && $isInterimVerifie)
                                                            <span style="color:red;">(PI)</span>
                                                        @endif
                                                    </p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="signValid">
                                        <div class="signature">
                                            @php
                                                $valideUser = $note->validePar;
                                                $isInterimValide = $valideUser && method_exists($valideUser, 'estInterimaireA') && $valideUser->estInterimaireA($note->date, 'valideur');
                                                $signatureValide = $valideUser && $valideUser->signature ? $valideUser->signature : ($signatureN3 ?? null);
                                            @endphp
                                            @if($note->statut == 'validée' || $note->statut == 'en cours d\'exécution' || $note->statut == 'executée' || $note->statut == 'annulée')
                                                @if($signatureValide)
                                                    @php
                                                        $signatureValideUrl = Str::startsWith($signatureValide, ['http://', 'https://'])
                                                            ? $signatureValide
                                                            : asset('storage/' . ltrim($signatureValide, '/'));
                                                    @endphp
                                                    <img src="{{ $signatureValideUrl }}" alt="Signature de {{ $valideUser?->name ?? 'N/A' }}" width="100" height="100" style="margin: auto; object-fit: contain;">
                                                @else
                                                    <p style="font-size: 10px; text-align: center; font-weight: bold; margin-top: 30px;">
                                                        {{ $valideUser?->name ?? 'N/A' }}
                                                        @if(isset($isInterimValide) && $isInterimValide)
                                                            <span style="color:red;">(PI)</span>
                                                        @endif
                                                    </p>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>

            <!-- Colonne de droite - Calendrier fin -->
            <div class="sidebar">
                <h3>Fin</h3>
                <h4>Mois</h4>
                <ul style="list-style: none;margin:0;padding:0">
                    <li style="text-align: center;border:1px solid #000;">N° {{ \Carbon\Carbon::parse($note->dft)->format('m') }}</li>
                    <li style="text-align: center;border:1px solid #000;">Date</li>
                    @for ($i = 1; $i <= 31; $i++)
                        <li style="text-align: center; border:1px solid #000;
                            {{ $i == \Carbon\Carbon::parse($note->dft)->format('d') ? 'background-color: green;' : '' }}">
                            {{ $i }}
                        </li>
                    @endfor
                </ul>
            </div>
        </div>

        <div class="footer">
            {{-- <p>Destinataires : <b>@if(isset($note->services) && $note->services->isNotEmpty())
                @foreach($note->services as $sousEquipement)
                    {{ $sousEquipement->nom }}
                    @if(!$loop->last)
                        ,
                    @endif
                @endforeach
            @endif</b></p> --}}

            <div class="mission-signataires" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: center; margin-top: 20px;">                
                @unless(auth()->user()->hasRole('operateurchef') || auth()->user()->hasRole('operateur'))
                <button type="button" class="btn btn-secondary no-print" onclick="setTimeout(function(){ window.print(); }, 100);">
                   Imprimer
                </button>
                @endunless
                @php
                    $userBtns = Auth::user();
                    $isVerificateurBtn = $userBtns && ($userBtns->hasRole('verificateur') || $userBtns->hasRole('admin') || $userBtns->estInterimaireA($note->date, 'verificateur'));
                    $isOperateurRouteBtn = Request::is('operateur*') || Request::is('operateurchef*');
                    $isOperateurViewBtn = in_array(request()->query('view'), ['operateur', 'operateurchef']);
                @endphp
                @if($isVerificateurBtn)
                    @if(in_array($note->statut, ['en attente de vérification', 'retournée']) && in_array($note->demande->statut, ['créée', 'en cours de traitement', 'acceptée']))
                        <form action="{{ route('verificateur.notes.update', $note) }}" method="POST" style="display: inline;" target="_top">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="verifier">
                            <button type="submit" class="btn btn-success no-print">
                                Vérifier la note
                            </button>
                        </form>
                    @endif
                    @if($note->statut !== 'validée' && isset($note->document) && $note->document)
                        <a href="{{ asset('storage/' . $note->document) }}" target="_blank" class="btn btn-warning no-print">
                            Visualiser le fichier d'étude
                        </a>
                    @endif
                    @if(!$isOperateurRouteBtn && !$isOperateurViewBtn && in_array($note->statut, ['en attente de vérification', 'retournée']))
                        <button type="button" class="btn btn-danger no-print" id="openModalButton">
                            Retourner la note
                        </button>
                    @endif
                @endif
                @php
                    $userBtnsVal = Auth::user();
                    $isValideurBtn = $userBtnsVal && ($userBtnsVal->hasRole('valideur') || $userBtnsVal->hasRole('admin') || $userBtnsVal->estInterimaireA($note->date, 'valideur'));
                @endphp
                @if($isValideurBtn && !auth()->user()->estInterimaireA($note->date, 'verificateur'))
                    @if($note->statut === 'vérifiée')
                        <form action="{{ route('valideur.notes.update', $note) }}" method="POST" style="display: inline;" target="_top">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="valider">
                            <button type="submit" class="btn btn-success no-print">
                                Valider la note
                            </button>
                        </form>
                    @endif
                    @if($note->statut !== 'en attente de vérification' && isset($note->document) && $note->document && !$userBtnsVal->hasRole('admin'))
                        <a href="{{ asset('storage/' . $note->document) }}" target="_blank" class="btn btn-warning no-print">
                            Visualiser le fichier d'étude
                        </a>
                    @endif
                    @if($note->statut !== 'retournée' && $note->statut !== 'validée' && $note->statut !== 'en attente de vérification')
                        <button type="button" class="btn btn-danger no-print" data-bs-toggle="modal" data-bs-target="#retourModalValideur">
                            Retourner la note
                        </button>
                    @endif
                @endif
                @unless(auth()->user()->hasRole('operateurchef') || auth()->user()->hasRole('operateur'))
                @if($note->demande)
                    @if($note->demande->pdf_path)
                        <a href="{{ Storage::url($note->demande->pdf_path) }}" target="_blank" class="btn no-print" style="background-color: #0D1CB0; color: white; border-color: #0D1CB0;">
                            <i class="fas fa-file-pdf"></i> Voir la DAPT associée
                        </a>
                    @else
                        <a href="{{ route('pdf.dapt.view', $note->demande) }}" target="_blank" class="btn no-print" style="background-color: #0D1CB0; color: white; border-color: #0D1CB0;">
                            <i class="fas fa-file-pdf"></i> Voir la DAPT associée
                        </a>
                    @endif
                @endif
                @endunless
                
                {{-- Bouton Annuler supprimé - disponible dans la page show.blade.php --}}
            </div>
                <script>
                    // Fonction d'impression robuste
                    function printDocument() {
                        try {
                            // Vérifier si l'impression est supportée
                            if (window.print) {
                                // Donner le focus à la fenêtre avant d'imprimer
                                window.focus();
                                // Lancer l'impression avec un délai
                                setTimeout(function() {
                                    window.print();
                                }, 200);
                            } else {
                                alert('L\'impression n\'est pas supportée par ce navigateur.');
                            }
                        } catch (error) {
                            console.error('Erreur lors de l\'impression:', error);
                            alert('Erreur lors de l\'impression. Utilisez Ctrl+P ou le menu du navigateur.');
                        }
                    }
                    // Fonction pour ajuster la taille du texte selon la longueur du nom
                    function adjustSignatureNameSize(isForPrint = false) {
                        const signatureNames = document.querySelectorAll('.signature-name');

                        signatureNames.forEach(function(nameElement) {
                            const textContent = nameElement.textContent.trim();
                            const textLength = textContent.length;

                            if (isForPrint) {
                                // Tailles pour l'impression (plus petites)
                                let fontSize;
                                if (textLength <= 8) {
                                    fontSize = '7px';
                                } else if (textLength <= 12) {
                                    fontSize = '6px';
                                } else if (textLength <= 16) {
                                    fontSize = '5px';
                                } else {
                                    fontSize = '4px';
                                }
                                nameElement.style.fontSize = fontSize + ' !important';

                                // Ajuster pour l'impression
                                nameElement.style.width = 'auto';
                                nameElement.style.maxWidth = '90px';
                                nameElement.style.minWidth = '70px';
                                nameElement.style.overflow = 'visible';
                                nameElement.style.textOverflow = 'clip';
                                nameElement.style.whiteSpace = 'normal';
                                nameElement.style.wordWrap = 'break-word';
                                nameElement.style.backgroundColor = 'transparent';
                                nameElement.style.color = 'red';
                                nameElement.style.borderColor = 'red';
                                nameElement.style.fontWeight = 'bold';
                            } else {
                                // Tailles pour l'affichage écran (plus grandes)
                                let fontSize;
                                if (textLength <= 8) {
                                    fontSize = '12px';
                                } else if (textLength <= 12) {
                                    fontSize = '11px';
                                } else if (textLength <= 16) {
                                    fontSize = '10px';
                                } else {
                                    fontSize = '9px';
                                }
                                nameElement.style.fontSize = fontSize + ' !important';

                                // Ajuster pour l'écran
                                nameElement.style.width = 'auto';
                                nameElement.style.maxWidth = '100px';
                                nameElement.style.minWidth = '80px';
                                nameElement.style.overflow = 'visible';
                                nameElement.style.textOverflow = 'clip';
                                nameElement.style.whiteSpace = 'normal';
                                nameElement.style.wordWrap = 'break-word';
                                nameElement.style.backgroundColor = 'transparent';
                                nameElement.style.color = 'red';
                                nameElement.style.borderColor = 'red';
                                nameElement.style.fontWeight = 'bold';
                            }
                        });
                    }

                    // Exécuter au chargement de la page (mode écran)
                    document.addEventListener('DOMContentLoaded', function() {
                        adjustSignatureNameSize(false);
                    });

                    // Exécuter avant l'impression (mode impression)
                    window.addEventListener('beforeprint', function() {
                        adjustSignatureNameSize(true);
                    });

                    // Restaurer après l'impression (mode écran)
                    window.addEventListener('afterprint', function() {
                        adjustSignatureNameSize(false);
                    });
                </script>

                @php
                    $user = Auth::user();
                    $isVerificateur = $user && ($user->hasRole('verificateur') || $user->hasRole('admin') || $user->estInterimaireA($note->date, 'verificateur'));
                    $isOperateurRoute = Request::is('operateur*') || Request::is('operateurchef*');
                    $isOperateurView = in_array(request()->query('view'), ['operateur', 'operateurchef']);
                @endphp
                @if($isVerificateur && !$isOperateurRoute && !$isOperateurView && in_array($note->statut, ['en attente de vérification', 'retournée']))
                    <!-- Modal -->
                    <div class="modal fade" id="retourModal" tabindex="-1" aria-labelledby="retourModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="background-color: white;">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="retourModalLabel">Motif du retour</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('verificateur.notes.update', $note) }}" method="POST" target="_top">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="retourner">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="motif_retour">Motif du retour</label>
                                            <textarea class="form-control" id="motif_retour" name="motif" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-danger">Confirmer le retour</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @role('operateurchef')
                    <!-- Opérateur Chef : uniquement joindre fiche de manœuvre, imprimer, voir DAPT -->
                    <div class="no-print" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: center; margin-top: 20px;">
                        <!-- Bouton Imprimer -->
                        <button type="button" class="btn btn-secondary" onclick="setTimeout(function(){ window.print(); }, 100);">
                            Imprimer
                        </button>

                        <!-- Voir la DAPT associée -->
                        @if($note->demande)
                            @if($note->demande->pdf_path)
                                <a href="{{ Storage::url($note->demande->pdf_path) }}" target="_blank" class="btn" style="background-color: #0D1CB0; color: white; border-color: #0D1CB0;">
                                    <i class="fas fa-file-pdf"></i> Voir la DAPT associée
                                </a>
                            @else
                                <a href="{{ route('pdf.dapt.view', $note->demande) }}" target="_blank" class="btn" style="background-color: #0D1CB0; color: white; border-color: #0D1CB0;">
                                    <i class="fas fa-file-pdf"></i> Voir la DAPT associée
                                </a>
                            @endif
                        @endif

                        <!-- Bouton pour voir la fiche de manœuvre si elle existe -->
                        @if($note->fiche_manoeuvre)
                            <a target="_blank" href="{{ asset('storage/' . $note->fiche_manoeuvre) }}" class="btn btn-success">
                                <i class="fas fa-eye"></i> Voir la fiche de manœuvre
                            </a>
                        @endif
                        {{-- Boutons joindre/modifier fiche supprimés - disponibles dans la page show.blade.php --}}
                    </div>
                @endrole

               {{-- @role('operateurchef')
                <!-- Section pour les fiches de manœuvre -->
                <div class="mt-3 row no-print">
                    <div class="col-12">
                        <h6>Fiche de manœuvre :</h6>
                        @if($note->fiche_manoeuvre)
                            <div class="d-flex align-items-center">
                                <a href="{{ asset('storage/' . $note->fiche_manoeuvre) }}" class="mr-2 btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Télécharger la fiche
                                 </a>
                                <span class="text-muted">{{ basename($note->fiche_manoeuvre) }}</span>
                            </div>
                        @else
                            <p class="text-muted">Aucune fiche de manœuvre jointe</p>
                        @endif
                    </div>
                </div>
                @endrole --}}

                {{-- Modal fiche manœuvre supprimé - disponible dans la page show.blade.php --}}


                @php
                    $user = Auth::user();
                    $isValideur = $user && ($user->hasRole('valideur') || $user->hasRole('admin') || $user->estInterimaireA($note->date, 'valideur'));
                    $isOperateurRoute = Request::is('operateur*') || Request::is('operateurchef*');
                    $isOperateurView = in_array(request()->query('view'), ['operateur', 'operateurchef']);
                @endphp
                @if($isValideur && !$isOperateurRoute && !$isOperateurView && !auth()->user()->estInterimaireA($note->date, 'verificateur') && $note->statut !== 'en attente de vérification')
                    <!-- Modal Valideur -->
                <div class="modal fade" id="retourModalValideur" tabindex="-1" aria-labelledby="retourModalValideurLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="background-color: white;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="retourModalValideurLabel">Motif du retour</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('valideur.notes.update', $note) }}" method="POST" target="_top">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="retourner">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="motif_retour_valideur">Motif du retour</label>
                                        <textarea class="form-control" id="motif_retour_valideur" name="motifbis" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Confirmer le retour</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                {{-- @php
                    $user = Auth::user();
                    $isVerificateur = $user && ($user->hasRole('verificateur') || $user->estInterimaireA($note->date, 'verificateur'));
                @endphp
                @if($isVerificateur && $note->statut !== 'vérifiée' && $note->statut !== 'validée')
                <a
                href="{{route('verificateur.demandes')}}"
                class="float-right btn btn-primary veiwbutton no-print"
                >Revenir sur les notes reçues</a>
                @endif --}}

                @php
                    $user = Auth::user();
                    $isVerificateur = $user && ($user->hasRole('verificateur') || $user->estInterimaireA($note->date, 'verificateur'));
                @endphp

                @role('operateur')
                    <div class="no-print" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: center; margin-top: 20px;">
                        <!-- Bouton Imprimer -->
                        <button type="button" class="btn btn-secondary" onclick="setTimeout(function(){ window.print(); }, 100);">
                            Imprimer
                        </button>
                        <!-- Bouton pour voir la fiche de manœuvre si elle existe -->
                        @if($note->fiche_manoeuvre)
                            <a target="_blank" href="{{ asset('storage/' . $note->fiche_manoeuvre) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> Voir la fiche de manœuvre
                            </a>
                        @endif
                        @if(isset($note->document) && $note->document)
                            <a href="{{ asset('storage/' . $note->document) }}" target="_blank" class="btn btn-warning">
                                Visualiser le fichier d'étude
                            </a>
                        @endif
                        <!-- Voir la DAPT associée -->
                        @if($note->demande)
                            @if($note->demande->pdf_path)
                                <a href="{{ Storage::url($note->demande->pdf_path) }}" target="_blank" class="btn" style="background-color: #0D1CB0; color: white; border-color: #0D1CB0;">
                                    <i class="fas fa-file-pdf"></i> Voir la DAPT associée
                                </a>
                            @else
                                <a href="{{ route('pdf.dapt.view', $note->demande) }}" target="_blank" class="btn" style="background-color: #0D1CB0; color: white; border-color: #0D1CB0;">
                                    <i class="fas fa-file-pdf"></i> Voir la DAPT associée
                                </a>
                            @endif
                        @endif
                        @if($note->statut === 'validée' && $note->fiche_manoeuvre)
                            <!-- Bouton pour mettre en cours d'exécution -->
                            <form action="{{ route('operateur.notes.update', $note) }}" method="POST" style="display: inline;" target="_top">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="demarrer">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-play"></i> Mettre en cours d'exécution
                                </button>
                            </form>
                        @endif

                        @if($note->statut === 'en cours d\'exécution')
                            <!-- Formulaire pour le bouton Exécuter (nécessite un modal pour les dates) -->
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#executerModal">
                                <i class="fas fa-check"></i> Exécuter
                            </button>
                        @endif

                        {{-- Bouton Annuler supprimé - disponible dans la page show.blade.php --}}
                    </div>

                    @if($note->statut === 'validée' && !$note->fiche_manoeuvre)
                        <p class="text-info no-print" style="margin-top: 15px;"><i class="fas fa-info-circle"></i> En attente que l'opérateur chef de poste joigne une fiche de manœuvre.</p>
                    @elseif($note->statut !== 'executée' && $note->statut !== 'annulée' && $note->statut !== 'en cours d\'exécution' && $note->statut !== 'validée')
                        <p class="text-info no-print"><i class="fas fa-info-circle"></i> La note doit être validée avant exécution.</p>
                    @endif
                @endrole

                @role('operateur')
                    @if($note->statut !== 'executée' && $note->statut !== 'annulée')
                        <!-- Formulaire pour le bouton Exécuter -->
                        {{-- <form action="{{ route('note.executer', $note) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary no-print" style="margin-right: 15px;">
                                Exécuter la NAPT
                            </button>
                        </form> --}}

                            {{-- <button type="button" class="btn btn-danger no-print" data-role="operateur" data-bs-toggle="modal" data-bs-target="#annulationModal" style="margin-right: 15px;">
                                Annuler la NAPT
                            </button> --}}
                    @endif
                @endrole

                @role('operateur')
                @endrole

                {{-- Modal annulationModal supprimé - disponible dans les pages show.blade.php --}}

                <!-- Modal pour l'exécution -->
                @role('operateur')
                <div class="modal fade" id="executerModal" tabindex="-1" aria-labelledby="executerModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="executerModalLabel">Confirmer l'exécution</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('operateur.notes.update', $note) }}" method="POST" target="_top">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="terminer">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="ddt" class="form-label">Date de début des travaux</label>
                                        <input type="datetime-local" class="form-control" id="ddt" name="ddt" value="{{ $note->ddt ? \Carbon\Carbon::parse($note->ddt)->format('Y-m-d\\TH:i') : '' }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="dft" class="form-label">Date de fin des travaux</label>
                                        <input type="datetime-local" class="form-control" id="dft" name="dft" value="{{ $note->dft ? \Carbon\Carbon::parse($note->dft)->format('Y-m-d\\TH:i') : '' }}" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-success">Confirmer l'exécution</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endrole

                <!-- Modal -->
                <div class="modal fade" id="retourModal" tabindex="-1" aria-labelledby="retourModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="background-color: white;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="retourModalLabel">Motif du retour</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('verificateur.notes.update', $note) }}" method="POST" target="_top">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="retourner">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="motif_retour">Motif du retour</label>
                                        <textarea class="form-control" id="motif_retour" name="motif" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-danger">Confirmer le retour</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // JS pour ouvrir le modal en cliquant sur le bouton
        document.getElementById('openModalButton').addEventListener('click', function () {
            var myModal = new bootstrap.Modal(document.getElementById('retourModal'));
            myModal.show();
        });
    </script>
    {{-- Script annulation supprimé - géré dans les pages show.blade.php --}}
</body>
</html>

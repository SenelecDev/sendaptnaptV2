@extends('layouts.app')

@section('title', 'Documentation')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="rounded-2xl p-8 shadow-xl" style="background: linear-gradient(to right, #2B1444, #4A2066);">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl" style="background: rgba(255,255,255,0.2);">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold font-['Rajdhani'] text-white">Documentation SENDAPTNAPT</h1>
                    <p class="mt-1" style="color: #e5e7eb;">Guide utilisateur complet — DAPT, NAPT, diffusions, admin et outils</p>
                </div>
            </div>
            <a href="{{ route('documentation.pdf') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl font-semibold text-white shadow-md transition hover:opacity-95 shrink-0"
               style="background: linear-gradient(135deg, #E87400, #B3006C); padding: 14px 22px; gap: 10px;">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Télécharger plaquette documentation</span>
            </a>
        </div>
    </div>

    <!-- Table des matières -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 text-[#2B1444]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            Table des matières
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @php
                $toc = [
                    ['#introduction', '1', 'Introduction', '#0D1CB0'],
                    ['#workflow', '2', 'Workflow général', '#B3006C'],
                    ['#statuts', '3', 'Statuts & glossaire', '#E87400'],
                    ['#roles', '4', 'Rôles', '#7C3AED'],
                    ['#creer-demande', '5', 'Créer une DAPT', '#0D9488'],
                    ['#gmao', '6', 'Mode GMAO', '#059669'],
                    ['#traitement-napt', '7', 'Traitement NAPT', '#0A91A3'],
                    ['#diffusion', '8', 'Diffusion hebdomadaire', '#B3006C'],
                    ['#retours-annulations', '9', 'Retours & annulations', '#DC2626'],
                    ['#execution', '10', 'Fiche manœuvre & exécution', '#0A91A3'],
                    ['#directeur', '11', 'Directeur', '#2B1444'],
                    ['#admin', '12', 'Administration', '#4A2066'],
                    ['#outils', '13', 'Outils communs', '#B3006C'],
                    ['#exports', '14', 'Exports Excel & PDF', '#E87400'],
                    ['#interims', '15', 'Intérims & absences', '#059669'],
                    ['#notifications', '16', 'Notifications', '#0D1CB0'],
                    ['#faq', '17', 'Bonnes pratiques', '#7C3AED'],
                ];
            @endphp
            @foreach($toc as [$href, $num, $label, $bg])
                <a href="{{ $href }}" class="flex items-center gap-3 p-3 rounded-lg text-white transition hover:opacity-90" style="background: {{ $bg }};">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0" style="background: rgba(255,255,255,0.2);">{{ $num }}</span>
                    <span class="font-medium text-sm">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- 1. Introduction -->
    <div id="introduction" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">1</span>
            <h2 class="text-2xl font-bold text-gray-900">Introduction</h2>
        </div>

        <p class="text-gray-600 mb-4">
            <strong>SENDAPTNAPT</strong> gère le cycle électronique des
            <strong>Demandes d'Arrêt Pour Travaux (DAPT)</strong> et des
            <strong>Notes d'Arrêt Pour Travaux (NAPT)</strong> — SENELEC / DESA-DESE.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                <h4 class="font-semibold text-blue-800">DAPT</h4>
                <p class="text-blue-700 text-sm mt-1">Demande formelle d'arrêt programmé d'équipements pour travaux de maintenance ou d'intervention.</p>
            </div>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                <h4 class="font-semibold text-green-800">NAPT</h4>
                <p class="text-green-700 text-sm mt-1">Document officiel établi par le DESA : dates, destinataires, consignes et suivi jusqu'à l'exécution.</p>
            </div>
        </div>

        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 text-sm text-gray-600 space-y-2">
            <p><strong>Connexion :</strong> authentification locale, puis LDAP si activé. Rôle <em>demandeur</em> par défaut si aucun rôle.</p>
            <p><strong>Multi-rôles :</strong> toutes vos sections apparaissent dans la barre latérale (ex. admin + desa).</p>
            <p><strong>Aide :</strong> cette page, le tutoriel d'accueil (premier login), et l'<strong>assistant</strong> (bouton bas-droite / icône chat du header).</p>
        </div>
    </div>

    <!-- 2. Workflow -->
    <div id="workflow" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">2</span>
            <h2 class="text-2xl font-bold text-gray-900">Workflow général</h2>
        </div>

        <div class="relative mb-6">
            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-[#2B1444] to-green-500"></div>
            <div class="space-y-5">
                @php
                    $steps = [
                        ['1', '#2B1444', 'Création DAPT', 'Le Demandeur crée (brouillon) ou soumet (créée) une demande avec schéma.', 'Demandeur → Demandes'],
                        ['2', '#4A2066', 'Traitement DESA', 'Prise en charge, acceptation des dates, Faire NAPT, ou retour au demandeur.', 'Éditeur → Demandes'],
                        ['3', '#6B3D99', 'Vérification', 'Le Vérificateur contrôle la NAPT : vérifier (statut vérifiée) ou retourner.', 'Vérificateur → Notes'],
                        ['4', '#E87400', 'Validation', 'Le Valideur valide (NAPT validée + DAPT acceptée) ou retourne.', 'Valideur → Notes'],
                        ['5', '#0A91A3', 'Fiche manœuvre', 'L\'Opérateur Chef joint le PDF/image obligatoire avant exécution.', 'Opérateur Chef → Notes'],
                        ['6', '#059669', 'Exécution', 'L\'Opérateur démarre puis termine (dates réelles ou créneaux).', 'Opérateur → Notes'],
                    ];
                @endphp
                @foreach($steps as [$n, $color, $title, $desc, $menu])
                    <div class="relative flex gap-6">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center z-10 ring-4 ring-white shrink-0" style="background: {{ $color }};">
                            <span class="text-white font-bold text-lg">{{ $n }}</span>
                        </div>
                        <div class="flex-1 p-5 rounded-xl" style="background: linear-gradient(to right, {{ $color }}12, transparent);">
                            <h4 class="font-bold text-gray-900">{{ $title }}</h4>
                            <p class="text-gray-600 text-sm mt-1">{{ $desc }}</p>
                            <span class="inline-flex mt-2 text-xs bg-white/80 text-gray-700 px-2 py-1 rounded-full border border-gray-200">{{ $menu }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800 space-y-1">
            <p><strong>Règles clés :</strong> une DAPT = une seule NAPT · document d'étude obligatoire si étude = oui · exécution bloquée sans fiche manœuvre.</p>
            <p>À l'envoi en vérification, le DESA enregistre les dates acceptées sur la DAPT et régénère le PDF DAPT.</p>
        </div>
    </div>

    <!-- 3. Statuts & glossaire -->
    <div id="statuts" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">3</span>
            <h2 class="text-2xl font-bold text-gray-900">Statuts & glossaire</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Statuts DAPT</h3>
                <div class="overflow-hidden rounded-xl border border-gray-200 text-sm">
                    <table class="w-full">
                        <thead class="bg-[#2B1444] text-white"><tr><th class="text-left px-4 py-2">Statut</th><th class="text-left px-4 py-2">Signification</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="px-4 py-2"><span class="badge-secondary">Brouillon</span></td><td class="px-4 py-2 text-gray-600">Non soumise, modifiable</td></tr>
                            <tr><td class="px-4 py-2"><span class="badge-info">Créée</span></td><td class="px-4 py-2 text-gray-600">Soumise, en attente DESA</td></tr>
                            <tr><td class="px-4 py-2"><span class="badge-warning">En cours de traitement</span></td><td class="px-4 py-2 text-gray-600">Prise en charge DESA</td></tr>
                            <tr><td class="px-4 py-2"><span class="badge-success">Acceptée</span></td><td class="px-4 py-2 text-gray-600">Dates acceptées / NAPT validée</td></tr>
                            <tr><td class="px-4 py-2"><span class="badge-danger">Retournée</span></td><td class="px-4 py-2 text-gray-600">À corriger par le demandeur</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Statuts NAPT</h3>
                <div class="overflow-hidden rounded-xl border border-gray-200 text-sm">
                    <table class="w-full">
                        <thead class="bg-[#2B1444] text-white"><tr><th class="text-left px-4 py-2">Statut</th><th class="text-left px-4 py-2">Signification</th></tr></thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            <tr><td class="px-4 py-2">Brouillon / En étude</td><td class="px-4 py-2">Rédaction DESA</td></tr>
                            <tr><td class="px-4 py-2">En attente de vérification</td><td class="px-4 py-2">Chez le vérificateur</td></tr>
                            <tr><td class="px-4 py-2">Vérifiée</td><td class="px-4 py-2">Prête pour le valideur</td></tr>
                            <tr><td class="px-4 py-2">Validée</td><td class="px-4 py-2">Fiche manœuvre possible</td></tr>
                            <tr><td class="px-4 py-2">En cours d'exécution / Exécutée</td><td class="px-4 py-2">Travaux terrain</td></tr>
                            <tr><td class="px-4 py-2">Retournée / Annulée</td><td class="px-4 py-2">Correction DESA / arrêt</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h3 class="font-semibold text-gray-900 mb-3">Glossaire</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600">
            <div class="p-3 bg-gray-50 rounded-lg border"><strong>GMAO</strong> — saisie ouvrages depuis le référentiel équipements</div>
            <div class="p-3 bg-gray-50 rounded-lg border"><strong>MTE</strong> — Mesures Techniques d'Exploitation (oui/non)</div>
            <div class="p-3 bg-gray-50 rounded-lg border"><strong>MCCE</strong> — Mesures de Consignation / Contrôle Électrique</div>
            <div class="p-3 bg-gray-50 rounded-lg border"><strong>UE / DE</strong> — étape / unité d'exécution sur la DAPT</div>
            <div class="p-3 bg-gray-50 rounded-lg border"><strong>Restitution le soir</strong> — travaux avec restitution quotidienne → exécution par créneaux</div>
            <div class="p-3 bg-gray-50 rounded-lg border"><strong>Fiche manœuvre</strong> — document opérateur chef obligatoire avant démarrage</div>
        </div>
    </div>

    <!-- 4. Rôles -->
    <div id="roles" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">4</span>
            <h2 class="text-2xl font-bold text-gray-900">Rôles et menus</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @foreach([
                ['Demandeur', 'Dashboard, Demandes, Absences, Observations — créer/suivre DAPT (soi + groupe).'],
                ['Éditeur (DESA)', 'Dashboard, Diffusions, Demandes (filtres), Notes (filtres), Absences, Observations.'],
                ['Vérificateur', 'Dashboard, Notes en attente — vérifier ou retourner avec motif.'],
                ['Valideur', 'Dashboard, Notes vérifiées — valider (DAPT acceptée) ou retourner.'],
                ['Opérateur Chef', 'Dashboard, Notes validées — fiche manœuvre, annulation si validée.'],
                ['Opérateur', 'Dashboard, Notes — démarrer/terminer exécution (bloque sans fiche).'],
                ['Directeur', 'Dashboard, DAPT, NAPT, Feedback — consultation et statistiques.'],
                ['Admin', 'Utilisateurs, groupes, référentiels, intérims, observations, journal, gestion DAPT/NAPT.'],
            ] as [$name, $desc])
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                    <h3 class="font-bold text-gray-900 mb-1">{{ $name }}</h3>
                    <p class="text-gray-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 5. Créer DAPT -->
    <div id="creer-demande" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">5</span>
            <h2 class="text-2xl font-bold text-gray-900">Créer une DAPT</h2>
        </div>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-6 text-sm text-yellow-800">
            Menu : <strong>Demandeur → Demandes → Nouvelle demande</strong>
        </div>
        <ol class="space-y-4 text-sm text-gray-600 list-decimal list-inside">
            <li><strong>Période prévue</strong> (dates/heures), destinataire (DESA/DD), désignation des travaux.</li>
            <li><strong>Schéma</strong> (image) obligatoire.</li>
            <li>Ouvrages en mode <strong>GMAO</strong> ou <strong>manuel</strong> (voir section suivante).</li>
            <li>Chargé de travaux interne (annuaire) ou externe + téléphones.</li>
            <li>Options : MTE, MCCE, étape UE/DE, case <strong>restitution le soir</strong> si applicable.</li>
            <li>
                <strong>Enregistrer brouillon</strong> (modifiable plus tard) ou
                <strong>Valider et soumettre</strong> (statut créée → transmis au DESA).
            </li>
        </ol>
        <p class="mt-4 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
            Après soumission, modification possible uniquement si <strong>retournée</strong> ou <strong>brouillon</strong>.
            Vous voyez vos DAPT et celles de votre <strong>groupe</strong>.
        </p>
    </div>

    <!-- 6. GMAO -->
    <div id="gmao" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">6</span>
            <h2 class="text-2xl font-bold text-gray-900">Mode GMAO (ouvrages)</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
            <div class="border border-teal-200 rounded-xl p-5">
                <h3 class="font-bold text-teal-700 mb-3">Parcours GMAO</h3>
                <ol class="space-y-2 list-decimal list-inside text-gray-600">
                    <li>Choisir le mode de saisie <strong>GMAO</strong></li>
                    <li>Rechercher et sélectionner le <strong>lieu d'exécution</strong></li>
                    <li>Ajouter les ouvrages à <strong>consigner</strong> et/ou à <strong>installer</strong> (équipements, lignes, postes…)</li>
                    <li>Les données viennent du référentiel GMAO (API interne)</li>
                </ol>
            </div>
            <div class="border border-gray-200 rounded-xl p-5">
                <h3 class="font-bold text-gray-800 mb-3">Mode manuel</h3>
                <p class="text-gray-600">Saisie libre du texte des ouvrages à consigner et à installer lorsque l'équipement n'est pas (encore) dans la GMAO.</p>
            </div>
        </div>
    </div>

    <!-- 7. Traitement NAPT -->
    <div id="traitement-napt" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">7</span>
            <h2 class="text-2xl font-bold text-gray-900">Traitement DAPT & NAPT</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
            <div class="border border-purple-200 rounded-xl p-5">
                <h3 class="font-bold text-purple-700 mb-3">DESA — Demandes</h3>
                <ul class="space-y-2 text-gray-600 list-disc list-inside">
                    <li>Filtres sidebar : Reçues, En cours, Retournées, Acceptées</li>
                    <li>Prendre en charge → en cours de traitement</li>
                    <li>Accepter les dates / <strong>Faire NAPT</strong></li>
                    <li>Retourner au demandeur avec motif</li>
                    <li>Alternative : Notes → sélectionner une DAPT sans NAPT</li>
                </ul>
            </div>
            <div class="border border-purple-200 rounded-xl p-5">
                <h3 class="font-bold text-purple-700 mb-3">DESA — Notes</h3>
                <ul class="space-y-2 text-gray-600 list-disc list-inside">
                    <li>N°, semaine, dates travaux/retrait, étude oui/non</li>
                    <li>Document obligatoire si étude = oui</li>
                    <li>Chargés consignation, correspondants, services</li>
                    <li>Brouillon → en étude → envoyer en vérification</li>
                    <li>Corriger une NAPT retournée puis renvoyer</li>
                    <li>Filtres par statut dans la sidebar</li>
                </ul>
            </div>
            <div class="border border-orange-200 rounded-xl p-5">
                <h3 class="font-bold text-orange-700 mb-3">Vérificateur</h3>
                <p class="text-gray-600">Ouvrir la NAPT → <strong>Vérifier</strong> (statut vérifiée) ou <strong>Retourner</strong> avec motif vers le DESA.</p>
            </div>
            <div class="border border-amber-200 rounded-xl p-5">
                <h3 class="font-bold text-amber-700 mb-3">Valideur</h3>
                <p class="text-gray-600">Ouvrir la NAPT vérifiée → <strong>Valider</strong> (NAPT validée + DAPT acceptée) ou retourner. Déposer une signature dans Profil → Ma signature (PNG/JPG, max 2 Mo) pour les PDF.</p>
            </div>
        </div>
    </div>

    <!-- 8. Diffusion -->
    <div id="diffusion" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">8</span>
            <h2 class="text-2xl font-bold text-gray-900">Diffusion hebdomadaire (DESA)</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Menu <strong>Éditeur → Diffusions</strong> — envoi des NAPT de la semaine aux groupes destinataires (email + PDF combiné).</p>
        <ol class="space-y-3 text-sm text-gray-600">
            <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-[#B3006C]/10 text-[#B3006C] flex items-center justify-center text-xs font-bold shrink-0">1</span><span>Choisir <strong>semaine</strong>, <strong>année</strong> et éventuellement un filtre de statut</span></li>
            <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-[#B3006C]/10 text-[#B3006C] flex items-center justify-center text-xs font-bold shrink-0">2</span><span>Sélectionner les <strong>groupes</strong> destinataires (tout sélectionner / désélectionner)</span></li>
            <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-[#B3006C]/10 text-[#B3006C] flex items-center justify-center text-xs font-bold shrink-0">3</span><span><strong>Prévisualiser</strong> la liste des NAPT concernées</span></li>
            <li class="flex gap-3"><span class="w-6 h-6 rounded-full bg-[#B3006C]/10 text-[#B3006C] flex items-center justify-center text-xs font-bold shrink-0">4</span><span><strong>Envoyer</strong> : génération du PDF combiné et envoi par email aux groupes</span></li>
        </ol>
        <p class="mt-4 text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg p-3">Les groupes doivent être correctement configurés (membres / emails) côté Admin → Groupes pour recevoir la diffusion.</p>
    </div>

    <!-- 9. Retours -->
    <div id="retours-annulations" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">9</span>
            <h2 class="text-2xl font-bold text-gray-900">Retours & annulations</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Retours</h3>
                <ul class="space-y-2 text-gray-600">
                    <li class="p-3 bg-red-50 rounded-lg border border-red-100"><strong>DAPT :</strong> DESA → demandeur (motif). Correction puis resoumission.</li>
                    <li class="p-3 bg-orange-50 rounded-lg border border-orange-100"><strong>NAPT (vérificateur) :</strong> retour DESA avec motif.</li>
                    <li class="p-3 bg-amber-50 rounded-lg border border-amber-100"><strong>NAPT (valideur) :</strong> 2ᵉ niveau de retour vers DESA.</li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Annulations NAPT</h3>
                <ul class="space-y-2 text-gray-600">
                    <li class="p-3 bg-purple-50 rounded-lg border border-purple-100"><strong>DESA :</strong> la plupart des statuts (sauf déjà exécutée/annulée). Motif ≥ 10 caractères.</li>
                    <li class="p-3 bg-teal-50 rounded-lg border border-teal-100"><strong>Opérateur Chef :</strong> si validée.</li>
                    <li class="p-3 bg-green-50 rounded-lg border border-green-100"><strong>Opérateur :</strong> si validée ou en cours d'exécution.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 10. Exécution -->
    <div id="execution" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">10</span>
            <h2 class="text-2xl font-bold text-gray-900">Fiche manœuvre & exécution</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
            <div class="border border-teal-200 rounded-xl p-5">
                <h3 class="font-bold text-teal-700 mb-2">Opérateur Chef</h3>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li>Sur NAPT <strong>validée</strong> uniquement</li>
                    <li>Upload PDF, JPG ou PNG (max. 10 Mo)</li>
                    <li>Modification / suppression possibles avant exécution</li>
                    <li>Obligatoire pour que l'opérateur puisse démarrer</li>
                </ul>
            </div>
            <div class="border border-green-200 rounded-xl p-5">
                <h3 class="font-bold text-green-700 mb-2">Opérateur</h3>
                <ul class="list-disc list-inside text-gray-600 space-y-1">
                    <li><strong>Démarrer :</strong> date/heure réelle de début</li>
                    <li><strong>Terminer :</strong> dates réelles de fin</li>
                    <li>Si restitution le soir sur la DAPT : saisie par <strong>créneaux</strong> (slots)</li>
                    <li>Statut final : <strong>exécutée</strong></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 11. Directeur -->
    <div id="directeur" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">11</span>
            <h2 class="text-2xl font-bold text-gray-900">Directeur — Supervision</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Accès en <strong>consultation</strong> (pas d'édition du workflow). Menu Directeur :</p>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600">
            <li class="p-3 bg-indigo-50 rounded-lg border border-indigo-100"><strong>Dashboard :</strong> indicateurs filtrables (semaine / mois / année)</li>
            <li class="p-3 bg-indigo-50 rounded-lg border border-indigo-100"><strong>DAPT / NAPT :</strong> listes, détail, pages statistiques dédiées</li>
            <li class="p-3 bg-indigo-50 rounded-lg border border-indigo-100 md:col-span-2"><strong>Feedback :</strong> envoyer un retour de supervision (distinct de « Mes observations »)</li>
        </ul>
    </div>

    <!-- 12. Admin -->
    <div id="admin" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">12</span>
            <h2 class="text-2xl font-bold text-gray-900">Administration</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm mb-5">
            <div class="p-4 rounded-xl border bg-gray-50"><h4 class="font-semibold text-gray-900 mb-1">Utilisateurs</h4><p class="text-gray-600">CRUD, rôles, groupes. Accès Sync et Simuler (super admin).</p></div>
            <div class="p-4 rounded-xl border bg-gray-50"><h4 class="font-semibold text-gray-900 mb-1">Groupes</h4><p class="text-gray-600">Organisation + membres (exports, diffusion).</p></div>
            <div class="p-4 rounded-xl border bg-gray-50"><h4 class="font-semibold text-gray-900 mb-1">Référentiels NAPT</h4><p class="text-gray-600">Chargés consignation, correspondants, services destinataires.</p></div>
            <div class="p-4 rounded-xl border bg-gray-50"><h4 class="font-semibold text-gray-900 mb-1">Observations</h4><p class="text-gray-600">Traiter bugs/suggestions (ouvert → en cours → résolu).</p></div>
            <div class="p-4 rounded-xl border bg-gray-50"><h4 class="font-semibold text-gray-900 mb-1">Intérims</h4><p class="text-gray-600">Toutes les absences / délégations.</p></div>
            <div class="p-4 rounded-xl border bg-gray-50"><h4 class="font-semibold text-gray-900 mb-1">Gestion DAPT / NAPT</h4><p class="text-gray-600">Supervision, exports, timelines / historique.</p></div>
            <div class="p-4 rounded-xl border bg-gray-50"><h4 class="font-semibold text-gray-900 mb-1">Journal d'activités</h4><p class="text-gray-600">Audit des actions (Spatie Activity Log).</p></div>
            <div class="p-4 rounded-xl border bg-gray-50 md:col-span-2"><h4 class="font-semibold text-gray-900 mb-1">Sync & impersonation</h4><p class="text-gray-600">Super admin uniquement : Utilisateurs → Sync Oracle/LDAP/photos. Bouton « Simuler » → bannière → Arrêter l'impersonation.</p></div>
        </div>
    </div>

    <!-- 13. Outils -->
    <div id="outils" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">13</span>
            <h2 class="text-2xl font-bold text-gray-900">Outils communs</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="p-4 rounded-xl border bg-gray-50"><strong>Recherche</strong> (header, ≥ 2 car.) : n° DAPT, désignation, lieu, destinataire, demandeur ; n° NAPT, semaine ; users si admin.</div>
            <div class="p-4 rounded-xl border bg-gray-50"><strong>Calendrier NAPT</strong> : vue planifiée des notes (menu Outils).</div>
            <div class="p-4 rounded-xl border bg-gray-50"><strong>Profil & signature</strong> : PNG/JPG ~300×200, max 2 Mo, pour PDF NAPT.</div>
            <div class="p-4 rounded-xl border bg-gray-50"><strong>Mes observations</strong> : bug / suggestion / question. Distinct du Feedback Directeur.</div>
            <div class="p-4 rounded-xl border bg-gray-50"><strong>PDF unitaires</strong> : visualiser / télécharger depuis le détail DAPT ou NAPT.</div>
            <div class="p-4 rounded-xl border bg-gray-50"><strong>Historique</strong> : timelines sur fiches admin / directeur / détail pour suivre les changements de statut.</div>
            <div class="p-4 rounded-xl border bg-[#B3006C]/5 border-[#B3006C]/20 md:col-span-2">
                <strong>Assistant intelligent</strong> — bouton violet/magenta en bas à droite (ou icône chat du header).
                Posez des questions sur le workflow, vos DAPT/NAPT et la file d'attente.
                Fonctionne en <strong>mode local</strong> sans Internet ; Gemini optionnel si clé API + réseau.
            </div>
        </div>
    </div>

    <!-- 14. Exports -->
    <div id="exports" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">14</span>
            <h2 class="text-2xl font-bold text-gray-900">Exports Excel & PDF</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Menu <strong>Outils → Export Excel</strong> (<code class="text-xs bg-gray-100 px-1 rounded">/exports</code>).</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-4 text-sm">
            <div class="border border-orange-200 rounded-xl p-5">
                <h3 class="font-bold text-[#E87400] mb-2">DAPT Excel</h3>
                <p class="text-gray-600">Filtres : date début / fin, statut, groupe demandeur.</p>
            </div>
            <div class="border border-teal-200 rounded-xl p-5">
                <h3 class="font-bold text-[#0A91A3] mb-2">NAPT Excel + PDF</h3>
                <p class="text-gray-600">Recherche n°, demandeur, ouvrage, type, dates, semaine, année, statut, groupe. Boutons Excel (orange) et PDF (teal).</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">Aussi : exports DESA (PDF listes + dashboard), Admin (Excel + dashboard), PDF joint aux emails de diffusion.</p>
    </div>

    <!-- 15. Intérims -->
    <div id="interims" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">15</span>
            <h2 class="text-2xl font-bold text-gray-900">Intérims & absences</h2>
        </div>
        <p class="text-sm text-gray-600 mb-4">Menu <strong>Absences</strong> (tous les rôles). Admin → Intérims pour toutes les absences.</p>
        <ol class="space-y-2 text-sm text-gray-600 mb-4 list-decimal list-inside">
            <li>Nouvelle absence → dates début / fin</li>
            <li>Rôle(s) à déléguer (ou tous)</li>
            <li>Choisir l'intérimaire + motif</li>
        </ol>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
            Badge <strong class="bg-amber-500 text-white px-1.5 py-0.5 rounded text-xs">INTÉRIM</strong> dans la sidebar.
            L'intérimaire dispose des mêmes droits que le titulaire pendant la période. Notifications à l'attribution et à la fin.
        </div>
    </div>

    <!-- 16. Notifications -->
    <div id="notifications" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">16</span>
            <h2 class="text-2xl font-bold text-gray-900">Notifications</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
            <ul class="space-y-1 list-disc list-inside bg-blue-50 border border-blue-100 rounded-xl p-4">
                <li>DAPT créée, acceptée, retournée</li>
                <li>NAPT soumise, vérifiée, validée, retournée, exécutée, annulée</li>
                <li>Intérim attribué / terminé</li>
                <li>Réponse observation / feedback</li>
            </ul>
            <div class="bg-gray-50 border rounded-xl p-4">
                <p class="mb-2">Header (compteur) + page Notifications. Email si adresse renseignée et notifications activées.</p>
                <p>Actions : marquer lu, tout lire, supprimer. Rappels automatiques possibles pour dossiers en attente / en retard.</p>
            </div>
        </div>
    </div>

    <!-- 17. Bonnes pratiques -->
    <div id="faq" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">17</span>
            <h2 class="text-2xl font-bold text-gray-900">Bonnes pratiques</h2>
        </div>
        <div class="space-y-3">
            @foreach([
                'Joignez un schéma clair ; préférez la GMAO quand l\'équipement existe.',
                'Utilisez le brouillon si la DAPT n\'est pas finalisée ; soumettez seulement quand elle est complète.',
                'Si étude = oui, joignez le document avant l\'envoi en vérification.',
                'Une DAPT = une NAPT : en cas de retour, le DESA corrige la note existante.',
                'Déposez la fiche manœuvre avant de demander le démarrage terrain.',
                'Renseignez votre signature (Profil) pour les PDF NAPT.',
                'Déclarez vos absences à l\'avance avec un intérimaire.',
                'Avant export ou diffusion : filtrez correctement (semaine, statut, groupes).',
                'En cas d\'anomalie : Mes observations (bug/suggestion) ou l\'assistant chat.',
            ] as $i => $tip)
                <div class="flex gap-3 p-4 rounded-xl border border-gray-100">
                    <span class="w-7 h-7 rounded-full bg-[#B3006C] text-white text-xs font-bold flex items-center justify-center shrink-0">{{ $i + 1 }}</span>
                    <p class="text-sm text-gray-700">{{ $tip }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Contact -->
    <div class="rounded-xl p-6 border border-gray-200" style="background: linear-gradient(to right, #f9fafb, #f3f4f6);">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl" style="background: #2B1444;">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Besoin d'aide ?</h3>
                    <p class="text-gray-600 text-sm">Utilisez l'assistant (bas-droite) ou envoyez une observation.</p>
                </div>
            </div>
            <a href="{{ route('mes-observations.create') }}" class="inline-flex items-center gap-2 text-white px-4 py-2 rounded-lg hover:opacity-90" style="background: #2B1444;">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                Envoyer une observation
            </a>
        </div>
    </div>
</div>
@endsection

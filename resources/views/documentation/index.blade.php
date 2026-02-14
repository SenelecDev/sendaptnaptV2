@extends('layouts.app')

@section('title', 'Documentation')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="rounded-2xl p-8 shadow-xl" style="background: linear-gradient(to right, #2B1444, #4A2066);">
        <div class="flex items-center gap-4 mb-4">
            <div class="p-3 rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold font-['Rajdhani'] text-white">Documentation SENDAPTNAPT</h1>
                <p class="mt-1" style="color: #e5e7eb;">Guide complet pour la gestion des DAPT et NAPT</p>
            </div>
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="#introduction" class="flex items-center gap-3 p-3 rounded-lg text-white transition hover:opacity-90" style="background: #0D1CB0;">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold" style="background: rgba(255,255,255,0.2);">1</span>
                <span class="font-medium">Introduction</span>
            </a>
            <a href="#workflow" class="flex items-center gap-3 p-3 rounded-lg text-white transition hover:opacity-90" style="background: #B3006C;">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold" style="background: rgba(255,255,255,0.2);">2</span>
                <span class="font-medium">Workflow Général</span>
            </a>
            <a href="#roles" class="flex items-center gap-3 p-3 rounded-lg text-white transition hover:opacity-90" style="background: #E85D04;">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold" style="background: rgba(255,255,255,0.2);">3</span>
                <span class="font-medium">Rôles et Responsabilités</span>
            </a>
            <a href="#creer-demande" class="flex items-center gap-3 p-3 rounded-lg text-white transition hover:opacity-90" style="background: #0D9488;">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold" style="background: rgba(255,255,255,0.2);">4</span>
                <span class="font-medium">Créer une Demande (DAPT)</span>
            </a>
            <a href="#traitement-napt" class="flex items-center gap-3 p-3 rounded-lg text-white transition hover:opacity-90" style="background: #7C3AED;">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold" style="background: rgba(255,255,255,0.2);">5</span>
                <span class="font-medium">Traitement NAPT</span>
            </a>
            <a href="#interims" class="flex items-center gap-3 p-3 rounded-lg text-white transition hover:opacity-90" style="background: #059669;">
                <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold" style="background: rgba(255,255,255,0.2);">6</span>
                <span class="font-medium">Gestion des Intérims</span>
            </a>
        </div>
    </div>

    <!-- Section 1: Introduction -->
    <div id="introduction" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">1</span>
            <h2 class="text-2xl font-bold text-gray-900">Introduction</h2>
        </div>
        
        <div class="prose prose-lg max-w-none">
            <p class="text-gray-600 mb-4">
                <strong>SENDAPTNAPT</strong> est le système de gestion électronique des <strong>Demandes d'Arrêt Pour Travaux (DAPT)</strong> 
                et des <strong>Notes d'Arrêt Pour Travaux (NAPT)</strong> de SENELEC.
            </p>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
                <div class="flex items-start gap-3">
                    <svg class="h-6 w-6 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-blue-800">Qu'est-ce qu'une DAPT ?</h4>
                        <p class="text-blue-700 text-sm mt-1">
                            Une DAPT est une demande formelle émise par un demandeur pour solliciter un arrêt programmé 
                            d'équipements électriques en vue d'effectuer des travaux de maintenance ou d'intervention.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <svg class="h-6 w-6 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-green-800">Qu'est-ce qu'une NAPT ?</h4>
                        <p class="text-green-700 text-sm mt-1">
                            Une NAPT est le document officiel généré après validation d'une DAPT. Elle contient toutes les informations 
                            techniques nécessaires pour l'exécution sécurisée des travaux, incluant les consignes de sécurité et les 
                            équipements concernés.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Workflow -->
    <div id="workflow" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">2</span>
            <h2 class="text-2xl font-bold text-gray-900">Workflow Général</h2>
        </div>
        
        <p class="text-gray-600 mb-6">Le processus complet de gestion d'un arrêt pour travaux suit les étapes suivantes :</p>
        
        <!-- Timeline Workflow -->
        <div class="relative">
            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-[#2B1444] to-green-500"></div>
            
            <div class="space-y-6">
                <!-- Étape 1 -->
                <div class="relative flex gap-6">
                    <div class="w-16 h-16 rounded-full bg-[#2B1444] flex items-center justify-center z-10 ring-4 ring-white">
                        <span class="text-white font-bold text-lg">1</span>
                    </div>
                    <div class="flex-1 bg-gradient-to-r from-[#2B1444]/5 to-transparent p-5 rounded-xl">
                        <h4 class="font-bold text-gray-900">Création de la DAPT</h4>
                        <p class="text-gray-600 text-sm mt-1">Le <strong>Demandeur</strong> crée une demande avec les informations sur les travaux prévus</p>
                        <span class="inline-flex items-center gap-1 mt-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            Statut: Créée
                        </span>
                    </div>
                </div>
                
                <!-- Étape 2 -->
                <div class="relative flex gap-6">
                    <div class="w-16 h-16 rounded-full bg-[#4A2066] flex items-center justify-center z-10 ring-4 ring-white">
                        <span class="text-white font-bold text-lg">2</span>
                    </div>
                    <div class="flex-1 bg-gradient-to-r from-[#4A2066]/5 to-transparent p-5 rounded-xl">
                        <h4 class="font-bold text-gray-900">Traitement par l'Éditeur (DESA)</h4>
                        <p class="text-gray-600 text-sm mt-1">L'<strong>Éditeur</strong> examine la demande et crée la NAPT correspondante</p>
                        <span class="inline-flex items-center gap-1 mt-2 text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                            Statut: En cours
                        </span>
                    </div>
                </div>
                
                <!-- Étape 3 -->
                <div class="relative flex gap-6">
                    <div class="w-16 h-16 rounded-full bg-[#6B3D99] flex items-center justify-center z-10 ring-4 ring-white">
                        <span class="text-white font-bold text-lg">3</span>
                    </div>
                    <div class="flex-1 bg-gradient-to-r from-[#6B3D99]/5 to-transparent p-5 rounded-xl">
                        <h4 class="font-bold text-gray-900">Vérification</h4>
                        <p class="text-gray-600 text-sm mt-1">Le <strong>Vérificateur</strong> contrôle les informations techniques de la NAPT</p>
                        <span class="inline-flex items-center gap-1 mt-2 text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded-full">
                            <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                            Statut: En attente vérification
                        </span>
                    </div>
                </div>
                
                <!-- Étape 4 -->
                <div class="relative flex gap-6">
                    <div class="w-16 h-16 rounded-full bg-amber-500 flex items-center justify-center z-10 ring-4 ring-white">
                        <span class="text-white font-bold text-lg">4</span>
                    </div>
                    <div class="flex-1 bg-gradient-to-r from-amber-500/5 to-transparent p-5 rounded-xl">
                        <h4 class="font-bold text-gray-900">Validation</h4>
                        <p class="text-gray-600 text-sm mt-1">Le <strong>Valideur</strong> approuve officiellement la NAPT</p>
                        <span class="inline-flex items-center gap-1 mt-2 text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-full">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            Statut: Validée
                        </span>
                    </div>
                </div>
                
                <!-- Étape 5 -->
                <div class="relative flex gap-6">
                    <div class="w-16 h-16 rounded-full bg-teal-500 flex items-center justify-center z-10 ring-4 ring-white">
                        <span class="text-white font-bold text-lg">5</span>
                    </div>
                    <div class="flex-1 bg-gradient-to-r from-teal-500/5 to-transparent p-5 rounded-xl">
                        <h4 class="font-bold text-gray-900">Préparation par l'Opérateur Chef</h4>
                        <p class="text-gray-600 text-sm mt-1">L'<strong>Opérateur Chef</strong> prépare la fiche manœuvre et planifie l'exécution</p>
                        <span class="inline-flex items-center gap-1 mt-2 text-xs bg-teal-100 text-teal-700 px-2 py-1 rounded-full">
                            <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                            Fiche manœuvre créée
                        </span>
                    </div>
                </div>
                
                <!-- Étape 6 -->
                <div class="relative flex gap-6">
                    <div class="w-16 h-16 rounded-full bg-green-500 flex items-center justify-center z-10 ring-4 ring-white">
                        <span class="text-white font-bold text-lg">6</span>
                    </div>
                    <div class="flex-1 bg-gradient-to-r from-green-500/5 to-transparent p-5 rounded-xl">
                        <h4 class="font-bold text-gray-900">Exécution</h4>
                        <p class="text-gray-600 text-sm mt-1">L'<strong>Opérateur</strong> exécute les manœuvres et marque la NAPT comme exécutée</p>
                        <span class="inline-flex items-center gap-1 mt-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            Statut: Exécutée
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Rôles -->
    <div id="roles" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">3</span>
            <h2 class="text-2xl font-bold text-gray-900">Rôles et Responsabilités</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Demandeur -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100/30 p-6 rounded-xl border border-blue-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Demandeur</h3>
                </div>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Créer des demandes d'arrêt pour travaux (DAPT)
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Suivre l'état de ses demandes
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Modifier ou corriger une demande retournée
                    </li>
                </ul>
            </div>
            
            <!-- Éditeur DESA -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100/30 p-6 rounded-xl border border-purple-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-purple-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Éditeur (DESA)</h3>
                </div>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-purple-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Traiter les demandes reçues
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-purple-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Créer et éditer les NAPT
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-purple-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Gérer les diffusions hebdomadaires
                    </li>
                </ul>
            </div>
            
            <!-- Vérificateur -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100/30 p-6 rounded-xl border border-orange-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Vérificateur</h3>
                </div>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-orange-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Contrôler les informations techniques
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-orange-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Vérifier ou retourner les NAPT
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-orange-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Ajouter des observations si nécessaire
                    </li>
                </ul>
            </div>
            
            <!-- Valideur -->
            <div class="bg-gradient-to-br from-amber-50 to-amber-100/30 p-6 rounded-xl border border-amber-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-amber-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Valideur</h3>
                </div>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Valider officiellement les NAPT
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Retourner pour correction si nécessaire
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Apposer sa signature électronique
                    </li>
                </ul>
            </div>
            
            <!-- Opérateur Chef -->
            <div class="bg-gradient-to-br from-teal-50 to-teal-100/30 p-6 rounded-xl border border-teal-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-teal-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Opérateur Chef</h3>
                </div>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-teal-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Créer les fiches manœuvre
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-teal-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Planifier l'exécution des NAPT
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-teal-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Superviser les opérateurs
                    </li>
                </ul>
            </div>
            
            <!-- Opérateur -->
            <div class="bg-gradient-to-br from-green-50 to-green-100/30 p-6 rounded-xl border border-green-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Opérateur</h3>
                </div>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Exécuter les manœuvres sur le terrain
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Marquer les NAPT comme exécutées
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="h-5 w-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Saisir les heures d'exécution
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Section 4: Créer une Demande -->
    <div id="creer-demande" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">4</span>
            <h2 class="text-2xl font-bold text-gray-900">Créer une Demande (DAPT)</h2>
        </div>
        
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-6">
            <div class="flex items-start gap-3">
                <svg class="h-6 w-6 text-yellow-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h4 class="font-semibold text-yellow-800">Prérequis</h4>
                    <p class="text-yellow-700 text-sm mt-1">Vous devez avoir le rôle <strong>Demandeur</strong> pour créer une DAPT.</p>
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-[#2B1444]/10 flex items-center justify-center shrink-0">
                    <span class="text-[#2B1444] font-bold">1</span>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Accéder au formulaire</h4>
                    <p class="text-gray-600 text-sm mt-1">
                        Depuis votre tableau de bord Demandeur, cliquez sur <strong>"Nouvelle demande"</strong> ou accédez à 
                        <strong>Demandes → Créer</strong> dans le menu latéral.
                    </p>
                </div>
            </div>
            
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-[#2B1444]/10 flex items-center justify-center shrink-0">
                    <span class="text-[#2B1444] font-bold">2</span>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Remplir les informations générales</h4>
                    <ul class="text-gray-600 text-sm mt-2 space-y-1 list-disc list-inside">
                        <li><strong>Motif des travaux</strong> : Description claire et concise</li>
                        <li><strong>Nature des travaux</strong> : Type d'intervention prévue</li>
                        <li><strong>Date et horaires</strong> : Période souhaitée pour les travaux</li>
                        <li><strong>Lieu d'exécution</strong> : Sélectionnez depuis la GMAO</li>
                    </ul>
                </div>
            </div>
            
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-[#2B1444]/10 flex items-center justify-center shrink-0">
                    <span class="text-[#2B1444] font-bold">3</span>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Sélectionner les équipements</h4>
                    <p class="text-gray-600 text-sm mt-1">
                        Utilisez la recherche intégrée pour trouver et sélectionner les équipements concernés par l'arrêt. 
                        Vous pouvez ajouter plusieurs équipements à votre demande.
                    </p>
                </div>
            </div>
            
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-[#2B1444]/10 flex items-center justify-center shrink-0">
                    <span class="text-[#2B1444] font-bold">4</span>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Indiquer le chargé de travaux</h4>
                    <p class="text-gray-600 text-sm mt-1">
                        Le chargé de travaux est la personne responsable de l'exécution des travaux sur le terrain. 
                        Vous pouvez vous désigner vous-même ou sélectionner un autre utilisateur.
                    </p>
                </div>
            </div>
            
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">Soumettre la demande</h4>
                    <p class="text-gray-600 text-sm mt-1">
                        Vérifiez toutes les informations et cliquez sur <strong>"Enregistrer"</strong>. 
                        Votre demande sera automatiquement transmise à l'éditeur DESA pour traitement.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 5: Traitement NAPT -->
    <div id="traitement-napt" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">5</span>
            <h2 class="text-2xl font-bold text-gray-900">Traitement NAPT</h2>
        </div>
        
        <p class="text-gray-600 mb-6">Une fois la DAPT soumise, elle passe par plusieurs étapes de traitement :</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pour l'Éditeur -->
            <div class="border border-purple-200 rounded-xl p-6">
                <h3 class="text-lg font-bold text-purple-700 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Pour l'Éditeur (DESA)
                </h3>
                <ol class="space-y-3 text-sm text-gray-600">
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold shrink-0">1</span>
                        <span>Examiner la DAPT reçue</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold shrink-0">2</span>
                        <span>Cliquer sur "Faire NAPT" pour créer la note</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold shrink-0">3</span>
                        <span>Compléter les informations techniques</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold shrink-0">4</span>
                        <span>Sélectionner les chargés de consignation et correspondants</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold shrink-0">5</span>
                        <span>Envoyer pour vérification</span>
                    </li>
                </ol>
            </div>
            
            <!-- Pour le Vérificateur -->
            <div class="border border-orange-200 rounded-xl p-6">
                <h3 class="text-lg font-bold text-orange-700 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Pour le Vérificateur
                </h3>
                <ol class="space-y-3 text-sm text-gray-600">
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center text-xs font-bold shrink-0">1</span>
                        <span>Consulter les NAPT en attente</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center text-xs font-bold shrink-0">2</span>
                        <span>Contrôler les informations techniques</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center text-xs font-bold shrink-0">3</span>
                        <span><strong>Vérifier</strong> si tout est correct, ou</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center text-xs font-bold shrink-0">4</span>
                        <span><strong>Retourner</strong> avec un motif si corrections nécessaires</span>
                    </li>
                </ol>
            </div>
            
            <!-- Pour le Valideur -->
            <div class="border border-amber-200 rounded-xl p-6">
                <h3 class="text-lg font-bold text-amber-700 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Pour le Valideur
                </h3>
                <ol class="space-y-3 text-sm text-gray-600">
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold shrink-0">1</span>
                        <span>Consulter les NAPT vérifiées</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold shrink-0">2</span>
                        <span>Effectuer une dernière revue</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold shrink-0">3</span>
                        <span><strong>Valider</strong> pour approbation officielle</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold shrink-0">4</span>
                        <span>La signature est automatiquement apposée</span>
                    </li>
                </ol>
            </div>
            
            <!-- Pour les Opérateurs -->
            <div class="border border-green-200 rounded-xl p-6">
                <h3 class="text-lg font-bold text-green-700 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Pour les Opérateurs
                </h3>
                <ol class="space-y-3 text-sm text-gray-600">
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold shrink-0">1</span>
                        <span><strong>Op. Chef</strong> : Créer la fiche manœuvre</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold shrink-0">2</span>
                        <span><strong>Op. Chef</strong> : Lancer l'exécution</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold shrink-0">3</span>
                        <span><strong>Opérateur</strong> : Exécuter les manœuvres</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold shrink-0">4</span>
                        <span><strong>Opérateur</strong> : Marquer comme exécutée</span>
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Section 6: Intérims -->
    <div id="interims" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 scroll-mt-8">
        <div class="flex items-center gap-3 mb-6">
            <span class="bg-[#2B1444] text-white w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">6</span>
            <h2 class="text-2xl font-bold text-gray-900">Gestion des Intérims</h2>
        </div>
        
        <p class="text-gray-600 mb-6">
            Le système d'intérim permet de déléguer temporairement vos responsabilités à un autre utilisateur 
            pendant une période d'absence (congés, mission, etc.).
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Comment créer un intérim ?</h3>
                <ol class="space-y-3 text-sm text-gray-600">
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#2B1444]/10 text-[#2B1444] flex items-center justify-center text-xs font-bold shrink-0">1</span>
                        <span>Accédez à <strong>Absences</strong> dans le menu</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#2B1444]/10 text-[#2B1444] flex items-center justify-center text-xs font-bold shrink-0">2</span>
                        <span>Cliquez sur <strong>"Nouvelle absence"</strong></span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#2B1444]/10 text-[#2B1444] flex items-center justify-center text-xs font-bold shrink-0">3</span>
                        <span>Sélectionnez les dates de début et fin</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#2B1444]/10 text-[#2B1444] flex items-center justify-center text-xs font-bold shrink-0">4</span>
                        <span>Choisissez le ou les rôles à déléguer</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#2B1444]/10 text-[#2B1444] flex items-center justify-center text-xs font-bold shrink-0">5</span>
                        <span>Sélectionnez l'intérimaire</span>
                    </li>
                </ol>
            </div>
            
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Indicateur d'intérim actif</h3>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 text-amber-700 text-sm font-medium mb-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        INTÉRIM ACTIF
                    </div>
                    <p class="text-amber-600 text-sm">
                        Lorsqu'un intérim est actif, un badge <strong class="bg-amber-500 text-white px-1.5 py-0.5 rounded text-xs">INTÉRIM</strong> 
                        apparaît à côté du rôle concerné dans la barre latérale.
                    </p>
                </div>
                
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <strong>Important :</strong> L'intérimaire hérite temporairement de toutes les permissions 
                        du rôle délégué et peut effectuer les mêmes actions que le titulaire.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support -->
    <div class="rounded-xl p-6 border border-gray-200 mt-8" style="background: linear-gradient(to right, #f9fafb, #f3f4f6);">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl" style="background: #2B1444;">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Besoin d'aide supplémentaire ?</h3>
                    <p class="text-gray-600 text-sm">Contactez le support technique ou envoyez vos observations.</p>
                </div>
            </div>
            <a href="{{ route('mes-observations.create') }}" class="inline-flex items-center gap-2 text-white px-4 py-2 rounded-lg transition hover:opacity-90" style="background: #2B1444;">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                Envoyer une observation
            </a>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Diffusion Hebdomadaire des NAPT')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">Diffusion Hebdomadaire</h1>
            <p class="text-gray-600">Envoyez les NAPT validées aux groupes destinataires</p>
        </div>
        <a href="{{ route('desa.dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au dashboard
        </a>
    </div>

    <!-- Formulaire de diffusion -->
    <div class="card-senelec p-6">
        <form id="diffusion-form" class="space-y-6">
            @csrf

            <!-- Filtres semaine / année / statut -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="semaine" class="label">Semaine</label>
                    <input type="number" name="semaine" id="semaine" class="input-senelec w-full"
                           min="1" max="53" value="{{ $semaineCourante }}" required>
                </div>
                <div>
                    <label for="annee" class="label">Année</label>
                    <input type="number" name="annee" id="annee" class="input-senelec w-full"
                           min="2020" max="2035" value="{{ $anneeCourante }}" required>
                </div>
                <div>
                    <label for="statut" class="label">Statut</label>
                    <select name="statut" id="statut" class="input-senelec w-full">
                        <option value="">Tous les statuts</option>
                        <option value="validée">Validée</option>
                        <option value="vérifiée">Vérifiée</option>
                        <option value="en cours d'exécution">En cours d'exécution</option>
                        <option value="executée">Exécutée</option>
                        <option value="annulée">Annulée</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="previewDiffusion()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-500 text-white font-semibold rounded-xl shadow-md hover:bg-blue-600 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Prévisualiser
                    </button>
                </div>
                <div class="flex items-end">
                    <button type="button" id="send-btn" onclick="sendDiffusion()" 
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-500 text-white font-semibold rounded-xl shadow-md hover:bg-green-600 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Envoyer
                    </button>
                </div>
            </div>

            <!-- Sélection des groupes -->
            <div class="border-t pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Groupes destinataires</h3>
                    <div class="flex gap-2">
                        <button type="button" onclick="selectAllGroups()" 
                                class="px-3 py-1.5 text-sm bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                            Tout sélectionner
                        </button>
                        <button type="button" onclick="deselectAllGroups()" 
                                class="px-3 py-1.5 text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                            Tout désélectionner
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($groupes as $groupe)
                        <label for="groupe_{{ $groupe->id }}" 
                               class="groupe-card flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-green-300 hover:bg-green-50 transition-all">
                            <input class="mt-1 h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500" 
                                   type="checkbox"
                                   name="groupes[]" 
                                   value="{{ $groupe->id }}"
                                   id="groupe_{{ $groupe->id }}">
                            <div class="ml-3 flex-1">
                                <span class="block font-medium text-gray-900">{{ $groupe->nom }}</span>
                                {{-- @if($groupe->email)
                                    <span class="block text-sm text-gray-500 truncate" title="{{ $groupe->email }}">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $groupe->email }}
                                    </span>
                                @else
                                    <span class="block text-sm text-amber-500">
                                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        Pas d'email défini
                                    </span>
                                @endif --}}
                                @if($groupe->users_count ?? false)
                                    <span class="block text-xs text-gray-400 mt-1">
                                        {{ $groupe->users_count }} membre(s)
                                    </span>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>

                @if($groupes->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p>Aucun groupe disponible</p>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Zone de prévisualisation -->
    <div id="preview-area" class="card-senelec p-6 hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
                <svg class="w-5 h-5 inline mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Prévisualisation
            </h3>
            <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="preview-content" class="border rounded-lg p-4 bg-gray-50 max-h-96 overflow-y-auto"></div>
    </div>

    <!-- Zone de résultat -->
    <div id="result-area" class="hidden">
        <div id="result-content"></div>
    </div>
</div>

<style>
    .groupe-card input:checked + div {
        color: #047857;
    }
    .groupe-card:has(input:checked) {
        border-color: #10b981;
        background-color: #ecfdf5;
    }
</style>

<script>
function selectAllGroups() {
    document.querySelectorAll('input[name="groupes[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllGroups() {
    document.querySelectorAll('input[name="groupes[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function closePreview() {
    document.getElementById('preview-area').classList.add('hidden');
}

function previewDiffusion() {
    const semaine = document.getElementById('semaine').value;
    const annee = document.getElementById('annee').value;
    const statut = document.getElementById('statut').value;
    const selectedGroups = [];

    document.querySelectorAll('input[name="groupes[]"]:checked').forEach(checkbox => {
        selectedGroups.push(checkbox.value);
    });

    if (selectedGroups.length === 0) {
        alert('Veuillez sélectionner au moins un groupe.');
        return;
    }

    // Afficher un loader
    const previewContent = document.getElementById('preview-content');
    previewContent.innerHTML = '<div class="text-center py-8"><svg class="animate-spin h-8 w-8 mx-auto text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><p class="mt-2 text-gray-500">Chargement...</p></div>';
    document.getElementById('preview-area').classList.remove('hidden');

    // Afficher la prévisualisation pour le premier groupe sélectionné
    const groupeId = selectedGroups[0];
    let url = `{{ route('desa.diffusion.preview') }}?groupe_id=${groupeId}&semaine=${semaine}&annee=${annee}`;

    if (statut) {
        url += `&statut=${encodeURIComponent(statut)}`;
    }

    fetch(url)
        .then(response => response.text())
        .then(html => {
            previewContent.innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur lors de la prévisualisation:', error);
            previewContent.innerHTML = '<div class="text-center py-8 text-red-500"><svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><p>Erreur lors de la prévisualisation</p></div>';
        });
}

function sendDiffusion() {
    const semaine = document.getElementById('semaine').value;
    const annee = document.getElementById('annee').value;
    const statut = document.getElementById('statut').value;
    const selectedGroups = [];

    document.querySelectorAll('input[name="groupes[]"]:checked').forEach(checkbox => {
        selectedGroups.push(checkbox.value);
    });

    if (selectedGroups.length === 0) {
        alert('Veuillez sélectionner au moins un groupe.');
        return;
    }

    // Changer le bouton pour indiquer l'envoi en cours
    const sendBtn = document.getElementById('send-btn');
    const originalText = sendBtn.innerHTML;
    sendBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Envoi...';
    sendBtn.disabled = true;

    // Préparer les données
    const formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('semaine', semaine);
    formData.append('annee', annee);
    if (statut) formData.append('statut', statut);
    selectedGroups.forEach(groupeId => {
        formData.append('groupes[]', groupeId);
    });

    // Envoyer la requête
    fetch('{{ route('desa.diffusion.send') }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        displayResult(data);
        sendBtn.innerHTML = originalText;
        sendBtn.disabled = false;
    })
    .catch(error => {
        console.error('Erreur lors de l\'envoi:', error);
        displayResult({
            success: false,
            message: 'Erreur technique lors de l\'envoi: ' + error.message
        });
        sendBtn.innerHTML = originalText;
        sendBtn.disabled = false;
    });
}

function displayResult(data) {
    const resultContent = document.getElementById('result-content');
    const resultArea = document.getElementById('result-area');
    
    let html = '';
    if (data.success) {
        html = `
            <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-green-800">${data.message}</h4>
                        ${data.details ? `
                            <div class="mt-3 text-sm text-green-700">
                                <p><strong>Détails :</strong></p>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>NAPT envoyées : ${data.details.napts_count || 0}</li>
                                    <li>Groupes contactés : ${data.details.groups_sent || 0}</li>
                                    <li>Emails envoyés : ${data.details.emails_sent || 0}</li>
                                    <li>Semaine : ${data.details.week || 'N/A'}</li>
                                    ${data.details.pdf_generated ? '<li>PDF généré et joint aux emails</li>' : ''}
                                </ul>
                            </div>
                        ` : ''}
                        ${data.warnings && data.warnings.length > 0 ? `
                            <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                <p class="font-semibold text-amber-700">Avertissements :</p>
                                <ul class="list-disc list-inside mt-1 text-sm text-amber-600">
                                    ${data.warnings.map(warning => `<li>${warning}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    } else {
        html = `
            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-lg font-semibold text-red-800">${data.message}</h4>
                    </div>
                </div>
            </div>
        `;
    }
    
    resultContent.innerHTML = html;
    resultArea.classList.remove('hidden');
    resultArea.scrollIntoView({ behavior: 'smooth' });
}
</script>
@endsection

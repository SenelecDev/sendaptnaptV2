@extends('layouts.app')

@section('title', 'Calendrier des Travaux')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
    .fc-event {
        cursor: pointer;
        padding: 2px 4px;
        font-size: 0.75rem;
        border-radius: 4px;
    }
    .fc-daygrid-event {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .fc-toolbar-title {
        font-family: 'Rajdhani', sans-serif;
        color: #2B1444 !important;
    }
    .fc-button-primary {
        background-color: #2B1444 !important;
        border-color: #2B1444 !important;
    }
    .fc-button-primary:hover {
        background-color: #4A2066 !important;
    }
    .fc-button-active {
        background-color: #E85D04 !important;
        border-color: #E85D04 !important;
    }
    .fc-day-today {
        background-color: rgba(232, 93, 4, 0.1) !important;
    }
    .fc-daygrid-day-number {
        color: #374151;
        font-weight: 500;
    }
    .fc-col-header-cell-cushion {
        color: #2B1444;
        font-weight: 600;
    }
    .event-tooltip {
        position: absolute;
        z-index: 9999;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        padding: 12px;
        min-width: 200px;
        max-width: 300px;
    }

    /* Popup "+X autres" */
    .fc-popover {
        z-index: 10000 !important;
        max-width: 520px;
        width: max-content;
        background: #ffffff !important;
        opacity: 1 !important;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.22);
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        isolation: isolate;
    }

    .fc-popover-header {
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
    }

    .fc-popover-body {
        max-height: 320px;
        overflow-y: auto;
        padding: 6px;
        background: #ffffff !important;
    }

    .fc-popover .fc-daygrid-event-harness {
        margin-bottom: 4px;
    }

    .fc-popover .fc-daygrid-event {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        line-height: 1.25;
        padding: 3px 6px;
        position: relative;
        z-index: 2;
    }

    .fc-popover .fc-event-title {
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-['Rajdhani']">
                <svg class="w-7 h-7 inline-block mr-2 text-senelec-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Calendrier des Travaux
            </h1>
            <p class="text-gray-600 mt-1">Vue d'ensemble des NAPT planifiées</p>
        </div>
        @if(auth()->user()->hasAnyRole(['desa', 'admin']))
        <div class="flex gap-2">
            <a href="{{ route('desa.notes.create') }}" class="btn-senelec inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle NAPT
            </a>
        </div>
        @endif
    </div>

    <!-- Légende -->
    <div class="card-senelec p-4">
        <div class="flex flex-wrap items-center gap-4 text-sm">
            <span class="font-semibold text-gray-700">Légende:</span>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-gray-600">En attente</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-gray-600">Validée</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span class="text-gray-600">En exécution</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                <span class="text-gray-600">Exécutée</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-senelec-orange"></span>
                <span class="text-gray-600">Autre</span>
            </div>
        </div>
    </div>

    <!-- Calendrier -->
    <div class="card-senelec p-4 overflow-visible">
        <div id="calendar"></div>
    </div>

    <!-- Modal détails -->
    <div id="event-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
            <div class="p-5 border-b border-gray-200 flex items-center justify-between bg-senelec-purple rounded-t-xl">
                <h3 class="text-lg font-bold text-white" id="modal-title">Détails NAPT</h3>
                <button onclick="closeModal()" class="text-white/80 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-5 space-y-3">
                <div>
                    <span class="text-xs text-gray-500 uppercase">N° NAPT</span>
                    <p class="font-mono font-bold text-senelec-purple" id="modal-numero"></p>
                </div>
                <div>
                    <span class="text-xs text-gray-500 uppercase">Lieu d'exécution</span>
                    <p class="text-gray-900" id="modal-lieu"></p>
                </div>
                <div>
                    <span class="text-xs text-gray-500 uppercase">Installations consignées</span>
                    <p class="text-gray-900" id="modal-installations"></p>
                </div>
                <div>
                    <span class="text-xs text-gray-500 uppercase">Demandeur</span>
                    <p class="text-gray-900" id="modal-demandeur"></p>
                </div>
                <div>
                    <span class="text-xs text-gray-500 uppercase">Statut</span>
                    <p id="modal-statut"></p>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <a id="modal-link" href="#" class="btn-senelec w-full text-center">
                    Voir la NAPT
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            list: 'Liste'
        },
        events: '{{ route("calendrier.events") }}',
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            showModal(info.event);
        },
        eventDidMount: function(info) {
            // Tooltip on hover
            info.el.setAttribute('title', info.event.title);
        },
        height: 'auto',
        firstDay: 1, // Lundi
        navLinks: true,
        dayMaxEvents: 3,
        moreLinkText: function(num) {
            return '+' + num + ' autres';
        }
    });
    calendar.render();
});

function showModal(event) {
    document.getElementById('modal-title').textContent = event.title;
    document.getElementById('modal-numero').textContent = event.title.split(' - ')[0];
    document.getElementById('modal-lieu').textContent = event.extendedProps.lieu;
    document.getElementById('modal-installations').textContent = event.extendedProps.installations || 'N/A';
    document.getElementById('modal-demandeur').textContent = event.extendedProps.demandeur;
    
    const statut = event.extendedProps.statut;
    const statutEl = document.getElementById('modal-statut');
    statutEl.textContent = statut;
    statutEl.className = 'inline-block px-2 py-1 rounded text-sm font-medium ' + getStatutClass(statut);
    
    document.getElementById('modal-link').href = event.url;
    document.getElementById('event-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('event-modal').classList.add('hidden');
}

function getStatutClass(statut) {
    switch(statut) {
        case 'validée': return 'bg-green-100 text-green-700';
        case 'executée': return 'bg-gray-100 text-gray-700';
        case 'en cours d\'exécution': return 'bg-blue-100 text-blue-700';
        default: return 'bg-amber-100 text-amber-700';
    }
}

// Fermer modal en cliquant dehors
document.getElementById('event-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
@endsection

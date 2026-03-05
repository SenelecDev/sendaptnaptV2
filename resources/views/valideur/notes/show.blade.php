@extends('layouts.app')

@section('title', 'NAPT ' . $note->numero_note)

@section('content')
<div class="w-full h-full">
    @if($note->fiche_manoeuvre)
    <div class="bg-green-50 border-b border-green-200 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <span class="text-sm font-medium text-green-800">Fiche de manœuvre disponible</span>
        </div>
        <a href="{{ $note->fiche_manoeuvre_url }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Voir la fiche
        </a>
    </div>
    @endif
    <iframe src="{{ route('pdf.napt.view', $note) }}" class="w-full border-0" style="height: calc(100vh - {{ $note->fiche_manoeuvre ? '112px' : '64px' }});"></iframe>
</div>
@endsection

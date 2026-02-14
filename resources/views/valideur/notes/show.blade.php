@extends('layouts.app')

@section('title', 'NAPT ' . $note->numero_note)

@section('content')
<div class="w-full h-full">
    <iframe src="{{ route('pdf.napt.view', $note) }}" class="w-full border-0" style="height: calc(100vh - 64px);"></iframe>
</div>
@endsection

@component('mail::message')
# {{ $customMessage }}

@component('mail::panel')
**Numéro de demande :** {{ $demande->numero_demande }}

**Statut actuel :** 
@if($demande->statut === 'acceptee')
✅ Acceptée
@elseif($demande->statut === 'retournee')
🔄 Retournée
@elseif($demande->statut === 'rejetee')
❌ Rejetée
@elseif($demande->statut === 'validee')
✅ Validée
@else
{{ ucfirst($demande->statut) }}
@endif
@endcomponent

---

## 📋 Détails de la demande

@component('mail::table')
| Information | Valeur |
|:------------|:-------|
| **Date de création** | {{ $demande->created_at ? \Carbon\Carbon::parse($demande->created_at)->format('d/m/Y à H:i') : '-' }} |
| **Date prévue** | {{ $demande->date ? \Carbon\Carbon::parse($demande->date)->format('d/m/Y') : '-' }} |
| **Destinataire** | {{ $demande->destinataire ?? '-' }} |
| **Lieu d'exécution** | {{ $demande->lieu_execution ?? ($demande->lieu_execution_manuel ?? '-') }} |
| **Désignation des travaux** | {{ $demande->designation ?? '-' }} |
@endcomponent

## 📅 Période prévue

@component('mail::table')
| Début | Fin | Durée |
|:------|:----|:------|
| {{ $demande->ddp ? \Carbon\Carbon::parse($demande->ddp)->format('d/m/Y') : '-' }} à {{ $demande->hdp ?? '-' }} | {{ $demande->dfp ? \Carbon\Carbon::parse($demande->dfp)->format('d/m/Y') : '-' }} à {{ $demande->hfp ?? '-' }} | @if($demande->ddp && $demande->dfp) {{ \Carbon\Carbon::parse($demande->ddp)->diffInDays(\Carbon\Carbon::parse($demande->dfp)) + 1 }} jour(s) @else - @endif |
@endcomponent

## 👥 Intervenants

@component('mail::table')
| Rôle | Nom | Contact |
|:-----|:----|:--------|
| **Demandeur** | {{ $demande->demandeur->name ?? '-' }} | {{ $demande->demandeur->email ?? '-' }} |
| **Chargé des travaux** | {{ $demande->charge_travaux_info->nom ?? '-' }}{{ $demande->charge_travaux_info && $demande->charge_travaux_info->type === 'externe' ? ' (Externe)' : '' }} | {{ $demande->charge_travaux_info->telephone ?? '-' }} |
@if($demande->demandeur && $demande->demandeur->n1)
| **Responsable N+1** | {{ $demande->demandeur->n1->name ?? '-' }} | {{ $demande->demandeur->n1->email ?? '-' }} |
@endif
@endcomponent

@if($demande->commentaire)
## 💬 Commentaire

> {{ $demande->commentaire }}
@endif

---

@component('mail::button', ['url' => route('demandeur.demandes.show', $demande->id), 'color' => 'primary'])
Voir la demande complète
@endcomponent

Cordialement,<br>
**DESA/DESE**<br>
{{ config('app.name') }}

@endcomponent

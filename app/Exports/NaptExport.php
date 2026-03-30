<?php

namespace App\Exports;

use App\Models\Note;
use App\Models\Demande;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class NaptExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Note::with(['demande.demandeur', 'etabliPar', 'verifiePar', 'validePar']);
        $user = auth()->user();

        // Filtrer par groupe uniquement pour un demandeur "pur"
        // (ne pas restreindre les profils multi-roles comme verificateur/valideur/directeur, etc.)
        if ($user->hasRole('demandeur') && !$user->hasAnyRole([
            'admin',
            'desa',
            'operateur',
            'operateurchef',
            'verificateur',
            'valideur',
            'directeur',
            'super_admin',
        ])) {
            $query->whereHas('demande.demandeur', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            });
        }

        // Filtres additionnels
        if ($this->request) {
            if ($this->request->filled('statut')) {
                $statut = $this->request->statut;
                $statutMap = [
                    'exécutée' => 'executée',
                    'établie' => 'brouillon',
                ];
                $query->where('statut', $statutMap[$statut] ?? $statut);
            }
            if ($this->request->filled('numero_semaine')) {
                $query->where('numero_semaine', $this->request->numero_semaine);
            }
            if ($this->request->filled('date_debut')) {
                $query->where('date', '>=', $this->request->date_debut);
            }
            if ($this->request->filled('date_fin')) {
                $query->where('date', '<=', $this->request->date_fin);
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'N° NAPT',
            'Semaine',
            'N° DAPT',
            'Demandeur',
            'Désignation',
            'Lieu',
            'Ouvrages à consigner',
            'Installations consignées',
            'Travaux',
            'Date début',
            'Date fin',
            'Jour',
            'Indications particulières',
            'Établi par',
            'Vérifié par',
            'Validé par',
            'Statut',
            'Date création',
        ];
    }

    private function formatOuvragesAConsigner(?Demande $demande): string
    {
        if (!$demande) return '';

        if (($demande->mode_saisie ?? 'gmao') === 'manuel') {
            return trim((string) ($demande->ouvrages_consigner_manuel ?? ''));
        }

        $gmao = $demande->ouvrages_consigner_gmao;
        if (is_string($gmao)) {
            $gmao = json_decode($gmao, true);
        }
        if (!is_array($gmao)) {
            return '';
        }

        $items = collect($gmao)
            ->map(function ($row) {
                if (is_string($row)) return $row;
                if (!is_array($row)) return null;
                return $row['designation'] ?? $row['description'] ?? $row['libelle'] ?? $row['nom'] ?? null;
            })
            ->filter()
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values();

        return $items->implode(', ');
    }

    public function map($note): array
    {
        // Récupérer les installations consignées
        $installations = '';
        if ($note->lignes_oracle) {
            $lignes = is_array($note->lignes_oracle) ? $note->lignes_oracle : json_decode($note->lignes_oracle, true);
            if ($lignes) {
                $installations = collect($lignes)->pluck('description')->filter()->implode(', ');
            }
        }
        if (!$installations && $note->equipements_oracle) {
            $equipements = is_array($note->equipements_oracle) ? $note->equipements_oracle : json_decode($note->equipements_oracle, true);
            if ($equipements) {
                $installations = collect($equipements)->pluck('description')->filter()->implode(', ');
            }
        }

        return [
            $note->numero_note,
            'S' . $note->numero_semaine,
            $note->demande->numero_demande ?? 'N/A',
            $note->demande->demandeur->name ?? 'N/A',
            $note->demande->designation ?? '',
            $note->demande->lieu_execution ?? '',
            $this->formatOuvragesAConsigner($note->demande),
            $installations ?: ($note->renseignementN ?? ''),
            $note->renseignementO ?? '',
            $note->ddt ? \Carbon\Carbon::parse($note->ddt)->format('d/m/Y') : '',
            $note->dft ? \Carbon\Carbon::parse($note->dft)->format('d/m/Y') : '',
            $note->jour ?? '',
            $note->renseignementP ?? '',
            $note->etabliPar->name ?? 'N/A',
            $note->verifiePar->name ?? '',
            $note->validePar->name ?? '',
            $note->statut,
            $note->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E85D04'],
                ],
            ],
        ];
    }
}

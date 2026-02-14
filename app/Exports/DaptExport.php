<?php

namespace App\Exports;

use App\Models\Demande;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

class DaptExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Demande::with(['demandeur', 'chargeTravaux', 'chargeTravauxExterne']);
        $user = auth()->user();

        // Filtrer par groupe pour les demandeurs
        if ($user->hasRole('demandeur') && !$user->hasAnyRole(['admin', 'desa', 'operateur', 'operateurchef'])) {
            $query->whereHas('demandeur', function ($q) use ($user) {
                $q->where('groupe_id', $user->groupe_id);
            });
        }

        // Filtres additionnels
        if ($this->request) {
            if ($this->request->filled('statut')) {
                $query->where('statut', $this->request->statut);
            }
            if ($this->request->filled('date_debut')) {
                $query->where('ddp', '>=', $this->request->date_debut);
            }
            if ($this->request->filled('date_fin')) {
                $query->where('dfp', '<=', $this->request->date_fin);
            }
            if ($this->request->filled('demandeur_id')) {
                $query->where('demandeur_id', $this->request->demandeur_id);
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'N° DAPT',
            'Demandeur',
            'Matricule',
            'Désignation',
            'Lieu d\'exécution',
            'Date début',
            'Date fin',
            'Heure début',
            'Heure fin',
            'Chargé de travaux',
            'Téléphone',
            'Statut',
            'Date création',
        ];
    }

    public function map($demande): array
    {
        return [
            $demande->numero_demande,
            $demande->demandeur->name ?? 'N/A',
            $demande->demandeur->matricule ?? 'N/A',
            $demande->designation,
            $demande->lieu_execution,
            $demande->ddp ? \Carbon\Carbon::parse($demande->ddp)->format('d/m/Y') : '',
            $demande->dfp ? \Carbon\Carbon::parse($demande->dfp)->format('d/m/Y') : '',
            $demande->hdp ?? '',
            $demande->hfp ?? '',
            $demande->chargeTravaux->name ?? ($demande->chargeTravauxExterne->nom ?? 'N/A'),
            $demande->chargeTravauxExterne->telephone ?? '',
            $demande->statut,
            $demande->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2B1444'],
                ],
            ],
        ];
    }
}

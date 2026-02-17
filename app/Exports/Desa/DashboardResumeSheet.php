<?php

namespace App\Exports\Desa;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardResumeSheet implements FromArray, WithStyles, WithTitle
{
    public function __construct(
        protected array $demandesStats,
        protected array $notesStats,
        protected ?string $periodeLabel = null
    ) {}

    public function array(): array
    {
        return [
            ['Résumé des statistiques - Tableau de bord DESA'],
            ['Période', $this->periodeLabel ?? 'Toutes les données'],
            [],
            ['DAPT (Demandes)'],
            ['Reçues', $this->demandesStats['recues'] ?? 0],
            ['En cours', $this->demandesStats['en_cours'] ?? 0],
            ['Retournées', $this->demandesStats['retournees'] ?? 0],
            ['Acceptées', $this->demandesStats['acceptees'] ?? 0],
            ['Total', $this->demandesStats['total'] ?? 0],
            [],
            ['NAPT (Notes)'],
            ['En étude', $this->notesStats['en_etude'] ?? 0],
            ['En vérification', $this->notesStats['en_attente_verification'] ?? 0],
            ['Vérifiées', $this->notesStats['verifiees'] ?? 0],
            ['Validées', $this->notesStats['validees'] ?? 0],
            ['En exécution', $this->notesStats['en_cours_execution'] ?? 0],
            ['Exécutées', $this->notesStats['executees'] ?? 0],
            ['Retournées', $this->notesStats['retournees'] ?? 0],
            ['Annulées', $this->notesStats['annulees'] ?? 0],
            ['Total', $this->notesStats['total'] ?? 0],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2B1444'],
                ],
            ],
            4 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Résumé';
    }
}

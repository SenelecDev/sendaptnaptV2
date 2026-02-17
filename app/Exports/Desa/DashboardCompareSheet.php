<?php

namespace App\Exports\Desa;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardCompareSheet implements FromArray, WithStyles, WithTitle
{
    public function __construct(protected array $compareData) {}

    public function array(): array
    {
        if (empty($this->compareData)) {
            return [['Aucune comparaison (sélectionnez plusieurs groupes)']];
        }

        $rows = [];
        $rows[] = ['Comparaison DAPT par groupe'];
        $rows[] = ['Groupe', 'Total DAPT', 'Créées', 'En cours', 'Acceptées', 'Retournées'];
        foreach ($this->compareData as $item) {
            $rows[] = [
                $item['groupe']->nom,
                $item['demandes']['total'],
                $item['demandes']['creees'],
                $item['demandes']['en_cours'],
                $item['demandes']['acceptees'],
                $item['demandes']['retournees'],
            ];
        }
        $rows[] = [];
        $rows[] = ['Comparaison NAPT par groupe'];
        $rows[] = ['Groupe', 'Total NAPT', 'En étude', 'Vérification', 'Vérifiées', 'Validées', 'Exécutées', 'Retournées'];
        foreach ($this->compareData as $item) {
            $rows[] = [
                $item['groupe']->nom,
                $item['notes']['total'],
                $item['notes']['en_etude'],
                $item['notes']['en_verification'],
                $item['notes']['verifiees'],
                $item['notes']['validees'],
                $item['notes']['executees'],
                $item['notes']['retournees'],
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            2 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2B1444'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Comparaison groupes';
    }
}

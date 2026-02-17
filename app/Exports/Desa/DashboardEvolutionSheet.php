<?php

namespace App\Exports\Desa;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardEvolutionSheet implements FromArray, WithStyles, WithTitle
{
    public function __construct(protected array $graphData) {}

    public function array(): array
    {
        $labels = $this->graphData['labels'] ?? [];
        $data = $this->graphData['datasets'] ?? [];

        if (empty($labels)) {
            return [['Aucune donnée disponible pour la période sélectionnée']];
        }

        $statutLabels = [
            'brouillon' => 'Brouillon',
            'en_etude' => 'En étude',
            'en_attente_verification' => 'En vérification',
            'verifiees' => 'Vérifiées',
            'en_attente_validation' => 'En attente validation',
            'validees' => 'Validées',
            'en_cours_execution' => 'En exécution',
            'executees' => 'Exécutées',
            'retournees' => 'Retournées',
            'annulees' => 'Annulées',
        ];

        $rows = [];
        $header = ['Période'];
        foreach (array_keys($data) as $key) {
            $header[] = $statutLabels[$key] ?? $key;
        }
        $header[] = 'Total';
        $rows[] = $header;

        foreach ($labels as $i => $label) {
            $row = [$label];
            $total = 0;
            foreach ($data as $values) {
                $val = $values[$i] ?? 0;
                $row[] = $val;
                $total += $val;
            }
            $row[] = $total;
            $rows[] = $row;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E87400'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Évolution NAPT';
    }
}

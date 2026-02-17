<?php

namespace App\Exports\Desa;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardRetourneesSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(protected $topGroupesRetournees) {}

    public function collection()
    {
        return $this->topGroupesRetournees;
    }

    public function headings(): array
    {
        return ['Rang', 'Groupe', 'Code', 'DAPT Retournées', '% du total'];
    }

    public function map($groupe): array
    {
        $total = $this->topGroupesRetournees->sum('demandes_retournees_count');
        $pct = $total > 0 ? round(($groupe->demandes_retournees_count / $total) * 100, 1) : 0;

        return [
            $this->topGroupesRetournees->search($groupe) + 1,
            $groupe->nom,
            $groupe->code ?? 'N/A',
            $groupe->demandes_retournees_count,
            $pct . '%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EF4444'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'DAPT Retournées';
    }
}

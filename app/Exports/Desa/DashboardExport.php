<?php

namespace App\Exports\Desa;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class DashboardExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        protected array $demandesStats,
        protected array $notesStats,
        protected $topGroupesRetournees,
        protected array $graphData,
        protected ?array $compareData,
        protected ?string $periodeLabel = null
    ) {}

    public function sheets(): array
    {
        $sheets = [
            new DashboardResumeSheet($this->demandesStats, $this->notesStats, $this->periodeLabel),
            new DashboardEvolutionSheet($this->graphData),
        ];

        if ($this->topGroupesRetournees && $this->topGroupesRetournees->count() > 0) {
            $sheets[] = new DashboardRetourneesSheet($this->topGroupesRetournees);
        }

        if ($this->compareData && count($this->compareData) > 1) {
            $sheets[] = new DashboardCompareSheet($this->compareData);
        }

        return $sheets;
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\DaptExport;
use App\Exports\NaptExport;
use App\Models\Groupe;
use App\Services\NaptPdfExportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Export DAPT vers Excel
     */
    public function exportDapt(Request $request)
    {
        $filename = 'DAPT_Export_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new DaptExport($request), $filename);
    }

    /**
     * Export NAPT vers Excel
     */
    public function exportNapt(Request $request)
    {
        $filename = 'NAPT_Export_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new NaptExport($request), $filename);
    }

    /**
     * Export NAPT filtrées en PDF (mêmes filtres que Excel)
     */
    public function exportNaptPdf(Request $request, NaptPdfExportService $pdfExport)
    {
        return $pdfExport->exportFiltered($request);
    }

    /**
     * Page d'export avec filtres
     */
    public function index()
    {
        $groupes = Groupe::query()
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return view('exports.index', compact('groupes'));
    }
}

<?php

namespace App\Services;

use App\Models\Note;
use App\Support\NaptQueryFilters;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class NaptPdfExportService
{
    public function exportFiltered(Request $request): Response|RedirectResponse
    {
        ini_set('memory_limit', '1G');
        set_time_limit(300);

        $query = Note::with([
            'demande.demandeur',
            'demande.chargeTravaux',
            'etabliPar',
            'verifiePar',
            'validePar',
            'chargesConsignation',
            'correspondants',
            'services',
            'retourne1',
            'retourne2',
        ])
            ->join('demandes', 'notes.demande_id', '=', 'demandes.id')
            ->join('users', 'demandes.demandeur_id', '=', 'users.id')
            ->select('notes.*');

        (new NaptQueryFilters())->apply($query, $request, joined: true);

        $notes = $query->orderBy('notes.created_at', 'desc')->limit(150)->get();

        if ($notes->isEmpty()) {
            return redirect()->back()->with('error', 'Aucune note à imprimer avec ces filtres.');
        }

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $html = view('pdf.napt-combined', compact('notes'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'NAPT_Export_' . now()->format('Y-m-d_His') . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}

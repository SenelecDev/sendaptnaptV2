<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class DocumentationController extends Controller
{
    /**
     * Affiche la page de documentation / tutoriels
     */
    public function index()
    {
        return view('documentation.index');
    }

    /**
     * Télécharge la plaquette documentation en PDF
     */
    public function downloadPdf(): Response
    {
        $pdf = Pdf::loadView('pdf.documentation')
            ->setPaper('a4', 'portrait');

        $filename = 'plaquette-documentation-sendaptnapt-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}

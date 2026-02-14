<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Demande;
use App\Models\ChargeCons;
use App\Models\Correspondant;
use App\Models\ServiceDest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

class NoteController extends Controller
{
    /**
     * Display a listing of notes for DESA.
     */
    public function index(Request $request)
    {
        $query = Note::with(['demande', 'etabliPar'])
            ->join('demandes', 'notes.demande_id', '=', 'demandes.id')
            ->join('users', 'demandes.demandeur_id', '=', 'users.id')
            ->select('notes.*');
        
        // Recherche
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(notes.numero_note) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(demandes.numero_demande) like ?', ["%{$search}%"]);
            });
        }
        
        // Filtre par statut
        if ($request->filled('statut')) {
            // Mapping des valeurs URL vers les constantes
            $statutMap = [
                'brouillon' => Note::STATUT_BROUILLON,
                'en_etude' => Note::STATUT_EN_ETUDE,
                'en_attente_verification' => Note::STATUT_EN_ATTENTE_VERIFICATION,
                'verifiee' => Note::STATUT_VERIFIEE,
                'en_attente_validation' => Note::STATUT_EN_ATTENTE_VALIDATION,
                'validee' => Note::STATUT_VALIDEE,
                'en_cours_execution' => Note::STATUT_EN_COURS_EXECUTION,
                'executee' => Note::STATUT_EXECUTEE,
                'retournee' => Note::STATUT_RETOURNEE,
                'annulee' => Note::STATUT_ANNULEE,
            ];
            
            $statut = $statutMap[$request->statut] ?? $request->statut;
            $query->where('notes.statut', $statut);
        }
        
        // Filtre par date début (sur ddt de la note - affiche les NAPT qui commencent à cette date)
        if ($request->filled('date_debut')) {
            $dateDebut = $request->date_debut;
            $query->whereDate('notes.ddt', '=', $dateDebut);
        }
        
        // Filtre par date fin (sur dft de la note - affiche les NAPT qui finissent à cette date)
        if ($request->filled('date_fin')) {
            $dateFin = $request->date_fin;
            $query->whereDate('notes.dft', '=', $dateFin);
        }
        
        // Filtre par semaine
        if ($request->filled('semaine')) {
            $query->where('notes.numero_semaine', $request->semaine);
        }
        
        // Filtre par année (sur la date des travaux de la note)
        if ($request->filled('annee')) {
            $query->whereYear('notes.ddt', $request->annee);
        }
        
        // Filtre par groupe (via le demandeur de la demande)
        if ($request->filled('groupe_id')) {
            $query->where('users.groupe_id', $request->groupe_id);
        }
        
        $notes = $query->orderBy('notes.created_at', 'desc')->paginate(15);
        
        return view('desa.notes.index', compact('notes'));
    }

    /**
     * Show the list of demandes in progress to select for creating a note.
     */
    public function selectDemande()
    {
        // Récupérer les demandes créées qui n'ont pas encore de NAPT
        $demandes = Demande::with(['demandeur', 'chargeTravaux'])
                           ->where('statut', Demande::STATUT_CREEE)
                           ->doesntHave('notes')
                           ->orderBy('created_at', 'desc')
                           ->get();
        
        return view('desa.notes.select-demande', compact('demandes'));
    }

    /**
     * Show the form for creating a new note.
     */
    public function create(Request $request)
    {
        $demande_id = $request->query('demande_id');
        $demande = null;
        
        if ($demande_id) {
            $demande = Demande::with(['demandeur', 'chargeTravaux'])
                              ->whereIn('statut', [Demande::STATUT_CREEE, Demande::STATUT_EN_COURS])
                              ->findOrFail($demande_id);
        }
        
        $demandesAcceptees = Demande::whereIn('statut', [Demande::STATUT_CREEE, Demande::STATUT_EN_COURS])
                                     ->doesntHave('notes')
                                     ->get();
        
        $chargecons = ChargeCons::orderBy('nom')->get();
        $correspondants = Correspondant::orderBy('nom')->get();
        $services = ServiceDest::orderBy('nom')->get();
        
        return view('desa.notes.create', compact('demande', 'demandesAcceptees', 'chargecons', 'correspondants', 'services'));
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'demande_id' => 'required|exists:demandes,id',
            'numero_note' => 'required|string|unique:notes,numero_note',
            'numero_semaine' => 'required|integer|min:1|max:53',
            'date' => 'required|date',
            'ddt' => 'required|date',
            'dft' => 'required|date|after_or_equal:ddt',
            'dre' => 'required|date',
            'drex' => 'required|date|after_or_equal:dre',
            'renseignementN' => 'nullable|string',
            'etude' => 'nullable|in:oui,non',
            'adresse_charges_consignation' => 'nullable|string',
            'adresse_correspondants' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        
        $demande = Demande::findOrFail($request->demande_id);
        $action = $request->input('action', 'brouillon');
        
        // Déterminer le statut selon l'action
        $statut = Note::STATUT_BROUILLON;
        if ($action === 'en_cours_etude') {
            $statut = Note::STATUT_EN_ETUDE;
        } elseif ($action === 'attente_verification') {
            $statut = Note::STATUT_EN_ATTENTE_VERIFICATION;
        }
        
        $note = new Note();
        $note->demande_id = $validated['demande_id'];
        $note->numero_note = $validated['numero_note'];
        $note->numero_semaine = $validated['numero_semaine'];
        $note->date = $validated['date'];
        $note->ddt = $validated['ddt'];
        $note->dft = $validated['dft'];
        $note->dre = $validated['dre'];
        $note->drex = $validated['drex'];
        $note->renseignementN = $validated['renseignementN'] ?? null;
        $note->etude = $validated['etude'] ?? 'non';
        $note->adresse_charges_consignation = $validated['adresse_charges_consignation'] ?? null;
        $note->adresse_correspondants = $validated['adresse_correspondants'] ?? null;
        $note->etabli_id = Auth::id();
        $note->statut = $statut;
        
        // Upload du document si présent
        if ($request->hasFile('document')) {
            $note->document = $request->file('document')->store('documents/notes', 'public');
        }
        
        $note->save();
        
        // Mettre à jour le statut de la demande à "en cours de traitement"
        $demande->statut = Demande::STATUT_EN_COURS;
        $demande->traite_id = Auth::id();
        $demande->save();
        
        // Attacher les chargés de consignation (filtrer N/A = 0)
        if ($request->has('charges_consignation')) {
            $chargeIds = array_filter($request->charges_consignation, fn($id) => $id > 0);
            $note->chargesCons()->sync($chargeIds);
        }
        // Attacher les correspondants (filtrer N/A = 0)
        if ($request->has('correspondants')) {
            $corrIds = array_filter($request->correspondants, fn($id) => $id > 0);
            $note->correspondants()->sync($corrIds);
        }
        // Attacher les services destinataires (filtrer N/A = 0)
        if ($request->has('services')) {
            $serviceIds = array_filter($request->services, fn($id) => $id > 0);
            $note->servicesDest()->sync($serviceIds);
        }
        
        $message = 'Note créée avec succès.';
        if ($statut === Note::STATUT_EN_ETUDE) {
            $message = 'Note mise en cours d\'étude.';
        } elseif ($statut === Note::STATUT_EN_ATTENTE_VERIFICATION) {
            $message = 'Note envoyée en vérification.';
            // Régénérer le PDF de la DAPT avec les dates acceptées
            $this->regenerateDaptPdf($demande);            // Notification aux vérificateurs
            app(NotificationService::class)->notifyNaptSubmitted($note);        }
        
        return redirect()->route('desa.notes.show', $note)
                         ->with('success', $message . ' Numéro: ' . $note->numero_note);
    }

    /**
     * Display the specified note.
     */
    public function show(Note $note)
    {
        $note->load(['demande.demandeur', 'etabliPar', 'verifiePar', 'validePar', 'chargecons', 'correspondants', 'services']);
        return view('desa.notes.show', compact('note'));
    }

    /**
     * Show the form for editing the specified note.
     */
    public function edit(Note $note)
    {
        // Ne peut modifier que si brouillon ou en étude ou retournée
        if (!in_array($note->statut, [Note::STATUT_BROUILLON, Note::STATUT_EN_ETUDE, Note::STATUT_RETOURNEE])) {
            return redirect()->route('desa.notes.show', $note)
                             ->with('error', 'Cette note ne peut plus être modifiée.');
        }
        
        $note->load(['demande', 'chargecons', 'correspondants', 'services']);
        
        $chargecons = ChargeCons::orderBy('nom')->get();
        $correspondants = Correspondant::orderBy('nom')->get();
        $services = ServiceDest::orderBy('nom')->get();
        
        return view('desa.notes.edit', compact('note', 'chargecons', 'correspondants', 'services'));
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note)
    {
        // Vérifier que la note peut être modifiée
        if (!in_array($note->statut, [Note::STATUT_BROUILLON, Note::STATUT_EN_ETUDE, Note::STATUT_RETOURNEE])) {
            return redirect()->route('desa.notes.show', $note)
                             ->with('error', 'Cette note ne peut plus être modifiée.');
        }

        $action = $request->input('action');
        
        // Validation des données
        $validated = $request->validate([
            'numero_semaine' => 'required|integer|min:1|max:53',
            'date' => 'required|date',
            'ddt' => 'required|date',
            'dft' => 'required|date|after_or_equal:ddt',
            'dre' => 'required|date',
            'drex' => 'required|date|after_or_equal:dre',
            'renseignementN' => 'nullable|string',
            'etude' => 'nullable|in:oui,non',
            'adresse_charges_consignation' => 'nullable|string',
            'adresse_correspondants' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        
        // Mise à jour des champs
        $note->numero_semaine = $validated['numero_semaine'];
        $note->date = $validated['date'];
        $note->ddt = $validated['ddt'];
        $note->dft = $validated['dft'];
        $note->dre = $validated['dre'];
        $note->drex = $validated['drex'];
        $note->renseignementN = $validated['renseignementN'] ?? null;
        $note->etude = $validated['etude'] ?? 'non';
        $note->adresse_charges_consignation = $validated['adresse_charges_consignation'] ?? null;
        $note->adresse_correspondants = $validated['adresse_correspondants'] ?? null;
        
        // Upload du document si présent
        if ($request->hasFile('document')) {
            // Supprimer l'ancien document si existe
            if ($note->document) {
                Storage::disk('public')->delete($note->document);
            }
            $note->document = $request->file('document')->store('documents/notes', 'public');
        }
        
        // Déterminer le nouveau statut selon l'action
        if ($action === 'attente_verification') {
            // Vérifier que le document est présent si étude = oui
            if ($note->etude === 'oui' && !$note->document) {
                return redirect()->back()
                                 ->withInput()
                                 ->with('error', 'Un document est obligatoire pour une NAPT nécessitant une étude.');
            }
            $note->statut = Note::STATUT_EN_ATTENTE_VERIFICATION;
            $note->etabli_id = Auth::id(); // Enregistrer qui a établi/soumis la note
            $message = 'Note envoyée en vérification.';
            // Régénérer le PDF de la DAPT avec les dates acceptées
            $this->regenerateDaptPdf($note->demande);
        } elseif ($action === 'en_cours_etude') {
            // Passer de brouillon à en étude
            $note->statut = Note::STATUT_EN_ETUDE;
            $message = 'Note mise en cours d\'étude.';
        } else {
            // Sauvegarde simple - remettre en étude si retournée
            if ($note->statut === Note::STATUT_RETOURNEE) {
                $note->statut = Note::STATUT_EN_ETUDE;
            }
            $message = 'Note mise à jour avec succès.';
        }
        
        $note->save();
        
        // Attacher les chargés de consignation (filtrer N/A = 0)
        if ($request->has('charges_consignation')) {
            $chargeIds = array_filter($request->charges_consignation, fn($id) => $id > 0);
            $note->chargesCons()->sync($chargeIds);
        } else {
            $note->chargesCons()->detach();
        }
        
        // Attacher les correspondants (filtrer N/A = 0)
        if ($request->has('correspondants')) {
            $corrIds = array_filter($request->correspondants, fn($id) => $id > 0);
            $note->correspondants()->sync($corrIds);
        } else {
            $note->correspondants()->detach();
        }
        
        // Attacher les services destinataires (filtrer N/A = 0)
        if ($request->has('services')) {
            $serviceIds = array_filter($request->services, fn($id) => $id > 0);
            $note->servicesDest()->sync($serviceIds);
        } else {
            $note->servicesDest()->detach();
        }
        
        return redirect()->route('desa.notes.show', $note)
                         ->with('success', $message);
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(Note $note)
    {
        // Ne peut supprimer que si brouillon
        if ($note->statut !== Note::STATUT_BROUILLON) {
            return redirect()->route('desa.notes.index')
                             ->with('error', 'Cette note ne peut pas être supprimée.');
        }
        
        $note->delete();
        
        return redirect()->route('desa.notes.index')
                         ->with('success', 'Note supprimée avec succès.');
    }

    /**
     * Annuler une note.
     */
    public function annuler(Request $request, Note $note)
    {
        // On peut annuler une note si elle n'est pas déjà annulée ou exécutée
        $statutsAnnulables = [
            Note::STATUT_BROUILLON,
            Note::STATUT_EN_ETUDE,
            Note::STATUT_EN_ATTENTE_VERIFICATION,
            Note::STATUT_VERIFIEE,
            Note::STATUT_EN_ATTENTE_VALIDATION,
            Note::STATUT_VALIDEE,
            Note::STATUT_EN_COURS_EXECUTION,
            Note::STATUT_RETOURNEE,
        ];

        if (!in_array($note->statut, $statutsAnnulables)) {
            return redirect()->route('desa.notes.show', $note)
                             ->with('error', 'Cette note ne peut pas être annulée.');
        }

        $request->validate([
            'commentanul' => 'required|string|min:10|max:1000',
        ], [
            'commentanul.required' => 'Le motif d\'annulation est obligatoire.',
            'commentanul.min' => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        $note->statut = Note::STATUT_ANNULEE;
        $note->commentanul = $request->commentanul;
        $note->annule_id = Auth::id();
        $note->save();

        // Notification au demandeur
        app(NotificationService::class)->notifyNaptCancelled($note, 'desa', $request->commentanul);

        return redirect()->route('desa.notes.index')
                         ->with('success', 'La note a été annulée avec succès.');
    }

    /**
     * Export filtered notes as PDF (combined individual NAPT PDFs)
     */
    public function exportPdf(Request $request)
    {
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
            'retourne2'
        ])
            ->join('demandes', 'notes.demande_id', '=', 'demandes.id')
            ->join('users', 'demandes.demandeur_id', '=', 'users.id')
            ->select('notes.*');
        
        // Appliquer les mêmes filtres que l'index
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(notes.numero_note) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(demandes.numero_demande) like ?', ["%{$search}%"]);
            });
        }
        
        if ($request->filled('statut')) {
            $statutMap = [
                'brouillon' => Note::STATUT_BROUILLON,
                'en_etude' => Note::STATUT_EN_ETUDE,
                'en_attente_verification' => Note::STATUT_EN_ATTENTE_VERIFICATION,
                'verifiee' => Note::STATUT_VERIFIEE,
                'en_attente_validation' => Note::STATUT_EN_ATTENTE_VALIDATION,
                'validee' => Note::STATUT_VALIDEE,
                'en_cours_execution' => Note::STATUT_EN_COURS_EXECUTION,
                'executee' => Note::STATUT_EXECUTEE,
                'retournee' => Note::STATUT_RETOURNEE,
                'annulee' => Note::STATUT_ANNULEE,
            ];
            $statut = $statutMap[$request->statut] ?? $request->statut;
            $query->where('notes.statut', $statut);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('notes.ddt', '=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('notes.dft', '=', $request->date_fin);
        }
        
        if ($request->filled('semaine')) {
            $query->where('notes.numero_semaine', $request->semaine);
        }
        
        if ($request->filled('annee')) {
            $query->whereYear('notes.ddt', $request->annee);
        }
        
        if ($request->filled('groupe_id')) {
            $query->where('users.groupe_id', $request->groupe_id);
        }
        
        $notes = $query->orderBy('notes.created_at', 'desc')->get();
        
        if ($notes->isEmpty()) {
            return back()->with('error', 'Aucune note à imprimer avec ces filtres.');
        }
        
        // Générer le PDF combiné avec les vrais PDF NAPT
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        
        $html = view('pdf.napt-combined', compact('notes'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'notes_' . now()->format('Y-m-d_His') . '.pdf';
        
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Regenerate the DAPT PDF with accepted period dates
     */
    private function regenerateDaptPdf(Demande $demande): void
    {
        try {
            $demande->load(['demandeur.n1', 'chargeTravaux']);
            
            // Récupérer le schéma en base64 si existe
            $schema = null;
            if ($demande->schema_path && Storage::disk('public')->exists($demande->schema_path)) {
                $schemaPath = storage_path('app/public/' . $demande->schema_path);
                $schemaContent = file_get_contents($schemaPath);
                $schema = 'data:image/' . pathinfo($schemaPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($schemaContent);
            }

            // Récupérer la signature du N+1 si existe
            $n1 = $demande->demandeur->n1 ?? null;
            $signatureN1 = null;
            if ($n1 && $n1->signature && Storage::disk('public')->exists($n1->signature)) {
                $signaturePath = storage_path('app/public/' . $n1->signature);
                $signatureContent = file_get_contents($signaturePath);
                $signatureN1 = 'data:image/' . pathinfo($signaturePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($signatureContent);
            }

            // Configurer Dompdf
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);

            // Générer le HTML
            $html = view('pdf.dapt', compact('demande', 'schema', 'signatureN1'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Nom du fichier
            $fileName = 'demande_' . $demande->numero_demande . '.pdf';
            $filePath = 'pdfs/' . $fileName;

            // Supprimer les anciens PDF
            $existingFiles = Storage::disk('public')->files('pdfs');
            foreach ($existingFiles as $file) {
                if (str_contains($file, 'demande_' . $demande->numero_demande)) {
                    Storage::disk('public')->delete($file);
                }
            }

            // Enregistrer le nouveau PDF
            Storage::disk('public')->put($filePath, $dompdf->output());
            $demande->update(['pdf_path' => $filePath]);
            
            Log::info("DAPT PDF regenerated for demande: " . $demande->numero_demande);
            
        } catch (\Exception $e) {
            Log::error("Erreur lors de la régénération du PDF DAPT: " . $e->getMessage());
        }
    }

    /**
     * Affiche la page de gestion de la diffusion hebdomadaire
     */
    public function manageDiffusion()
    {
        $groupes = \App\Models\Groupe::withCount('users')->orderBy('nom')->get();
        $semaineCourante = now()->weekOfYear;
        $anneeCourante = now()->year;

        return view('desa.diffusion', compact('groupes', 'semaineCourante', 'anneeCourante'));
    }

    /**
     * Prévisualise la diffusion pour un groupe
     */
    public function previewDiffusion(Request $request)
    {
        try {
            $semaine = $request->input('semaine', now()->weekOfYear);
            $annee = $request->input('annee', now()->year);
            $statut = $request->input('statut');

            $query = Note::with(['demande.demandeur'])
                ->where('numero_semaine', $semaine)
                ->whereYear('created_at', $annee);
            
            if ($statut && $statut !== 'tous') {
                $query->where('statut', $statut);
            }
            // Si statut = 'tous' ou vide, on affiche toutes les NAPT de la semaine

            $napts = $query->orderBy('numero_note')->get();

            return view('desa.napt-diffusion-preview', compact('napts', 'semaine', 'annee', 'statut'));
        } catch (\Exception $e) {
            Log::error('Erreur dans previewDiffusion: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Envoie la diffusion hebdomadaire aux groupes sélectionnés
     */
    public function sendDiffusion(Request $request)
    {
        try {
            $request->validate([
                'semaine' => 'required|integer|min:1|max:53',
                'annee' => 'required|integer|min:2020|max:2035',
                'groupes' => 'required|array|min:1',
                'groupes.*' => 'exists:groupes,id',
            ]);

            $semaine = $request->input('semaine');
            $annee = $request->input('annee');
            $statut = $request->input('statut');
            $groupeIds = $request->input('groupes');

            // Récupérer les NAPT
            $query = Note::with(['demande.demandeur', 'etabliPar', 'chargesConsignation', 'correspondants', 'services'])
                ->where('numero_semaine', $semaine)
                ->whereYear('created_at', $annee);
            
            if ($statut && $statut !== 'tous') {
                $query->where('statut', $statut);
            }
            // Si statut vide ou 'tous', on envoie toutes les NAPT de la semaine

            $napts = $query->orderBy('numero_note')->get();

            if ($napts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune NAPT trouvée pour cette semaine avec les critères sélectionnés.'
                ]);
            }

            // Générer le PDF combiné
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.napt-combined', compact('napts'))
                ->setPaper('a4', 'portrait');
            
            $pdfFileName = "NAPT_Diffusion_Semaine_{$semaine}_{$annee}_" . now()->format('Ymd_His') . ".pdf";
            $pdfPath = 'diffusions/' . $pdfFileName;
            Storage::disk('public')->put($pdfPath, $pdf->output());

            // Envoyer aux groupes
            $groupes = \App\Models\Groupe::whereIn('id', $groupeIds)->get();
            $emailsSent = 0;
            $warnings = [];

            foreach ($groupes as $groupe) {
                // Récupérer les emails des membres du groupe
                $members = $groupe->users()->whereNotNull('email')->get();
                
                if ($members->isEmpty()) {
                    $warnings[] = "Le groupe '{$groupe->nom}' n'a aucun membre avec email.";
                    continue;
                }

                foreach ($members as $member) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($member->email)
                            ->send(new \App\Mail\NaptWeeklyDiffusionMail(
                                $napts,
                                $semaine,
                                $annee,
                                $groupe->nom,
                                storage_path('app/public/' . $pdfPath)
                            ));
                        $emailsSent++;
                    } catch (\Exception $e) {
                        Log::error("Erreur envoi diffusion NAPT à {$member->email}: " . $e->getMessage());
                        $warnings[] = "Erreur d'envoi à {$member->email}";
                    }
                }
            }

            if ($emailsSent === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun email n\'a pu être envoyé.',
                    'warnings' => $warnings
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Diffusion réussie! {$napts->count()} NAPT(s) envoyée(s) à {$emailsSent} destinataire(s).",
                'details' => [
                    'napts_count' => $napts->count(),
                    'groups_sent' => count($groupeIds),
                    'emails_sent' => $emailsSent,
                    'week' => "S{$semaine}/{$annee}",
                    'pdf_generated' => true
                ],
                'warnings' => $warnings
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides: ' . implode(', ', $e->errors()['groupes'] ?? ['Erreur de validation'])
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur dans sendDiffusion: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de la diffusion: ' . $e->getMessage()
            ], 500);
        }
    }
}

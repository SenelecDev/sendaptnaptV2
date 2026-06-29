<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Route pour arrêter l'impersonation (accessible à tout utilisateur authentifié)
Route::get('/impersonate/stop', [\App\Http\Controllers\Admin\ImpersonateController::class, 'stop'])
    ->name('impersonate.stop')
    ->middleware('auth');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard - redirect to role-specific dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('desa')) {
            return redirect()->route('desa.dashboard');
        }
        if ($user->hasRole('verificateur')) {
            return redirect()->route('verificateur.dashboard');
        }
        if ($user->hasRole('valideur')) {
            return redirect()->route('valideur.dashboard');
        }
        if ($user->hasRole('operateurchef')) {
            return redirect()->route('operateurchef.dashboard');
        }
        if ($user->hasRole('operateur')) {
            return redirect()->route('operateur.dashboard');
        }
        if ($user->hasRole('directeur')) {
            return redirect()->route('directeur.dashboard');
        }
        if ($user->hasRole('demandeur')) {
            return redirect()->route('demandeur.dashboard');
        }
        
        // Default dashboard view for other roles
        return view('dashboard');
    })->name('dashboard');
    
    // Search route
    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');

    // Outils communs à tous les profils
    Route::get('/calendrier', [\App\Http\Controllers\CalendrierController::class, 'index'])->name('calendrier');
    Route::get('/calendrier/events', [\App\Http\Controllers\CalendrierController::class, 'events'])->name('calendrier.events');
    Route::get('/exports', [\App\Http\Controllers\ExportController::class, 'index'])->name('exports.index');
    Route::get('/exports/dapt', [\App\Http\Controllers\ExportController::class, 'exportDapt'])->name('exports.dapt');
    Route::get('/exports/napt', [\App\Http\Controllers\ExportController::class, 'exportNapt'])->name('exports.napt');
    Route::get('/exports/napt/pdf', [\App\Http\Controllers\ExportController::class, 'exportNaptPdf'])->name('exports.napt.pdf');
    Route::get('/documentation', [\App\Http\Controllers\DocumentationController::class, 'index'])->name('documentation');
    
    // Profile routes
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');
    
    Route::get('/profile/signature', function () {
        return view('profile.signature');
    })->name('profile.signature');
    
    Route::post('/profile/signature', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048'
        ]);
        
        $user = auth()->user();
        
        // Supprimer l'ancienne signature si existe
        if ($user->signature) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->signature);
        }
        
        // Dimensions recommandées
        $maxWidth = 300;
        $maxHeight = 200;
        
        $uploadedFile = $request->file('signature');
        $mime = $uploadedFile->getMimeType();
        
        // Charger l'image source
        list($origWidth, $origHeight) = getimagesize($uploadedFile->getRealPath());
        
        if ($mime === 'image/png') {
            $srcImage = imagecreatefrompng($uploadedFile->getRealPath());
        } else {
            $srcImage = imagecreatefromjpeg($uploadedFile->getRealPath());
        }
        
        // Calculer les dimensions finales
        if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
            $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
            $newWidth = (int) ($origWidth * $ratio);
            $newHeight = (int) ($origHeight * $ratio);
        } else {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        }
        
        // Créer l'image destination (toujours en PNG pour transparence)
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
        imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        
        // Redimensionner
        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagedestroy($srcImage);
        
        // Supprimer le fond blanc (pour JPEG ou PNG avec fond blanc)
        // Tolérance : pixels proches du blanc (R, G, B > 240)
        $tolerance = 240;
        for ($x = 0; $x < $newWidth; $x++) {
            for ($y = 0; $y < $newHeight; $y++) {
                $rgba = imagecolorat($dstImage, $x, $y);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;
                
                // Si le pixel est proche du blanc, le rendre transparent
                if ($r >= $tolerance && $g >= $tolerance && $b >= $tolerance) {
                    imagesetpixel($dstImage, $x, $y, $transparent);
                }
            }
        }
        
        // Sauvegarder en PNG (pour conserver la transparence)
        $tempPath = sys_get_temp_dir() . '/' . uniqid('sig_') . '.png';
        imagepng($dstImage, $tempPath, 9);
        imagedestroy($dstImage);
        
        // Stocker le fichier
        $filename = 'signatures/' . uniqid() . '.png';
        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, file_get_contents($tempPath));
        unlink($tempPath);
        
        $user->update(['signature' => $filename]);
        
        return redirect()->route('profile.signature')->with('success', 'Signature enregistrée avec succès. Le fond blanc a été rendu transparent.');
    })->name('profile.signature.store');
    
    Route::delete('/profile/signature', function () {
        $user = auth()->user();
        
        if ($user->signature) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->signature);
            $user->update(['signature' => null]);
        }
        
        return redirect()->route('profile.signature')->with('success', 'Signature supprimée.');
    })->name('profile.signature.delete');
    
    // Onboarding - marquer le tutoriel comme complété (formulaire POST classique)
    Route::post('/onboarding/complete', function () {
        auth()->user()->update(['onboarding_completed' => true]);
        return redirect()->back();
    })->name('onboarding.complete');

    // ===== MES ABSENCES / INTÉRIMS (tout utilisateur) =====
    Route::resource('mes-absences', \App\Http\Controllers\MesAbsencesController::class)->except(['show']);
    
    // ===== MES OBSERVATIONS (tout utilisateur) =====
    Route::resource('mes-observations', \App\Http\Controllers\MesObservationsController::class)->only(['index', 'create', 'store', 'show']);
    
    // ===== NOTIFICATIONS (tout utilisateur) =====
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::match(['get', 'post'], 'notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications-read', [\App\Http\Controllers\NotificationController::class, 'destroyRead'])->name('notifications.destroy-read');
    Route::get('api/notifications/count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.count');
    Route::get('api/notifications/latest', [\App\Http\Controllers\NotificationController::class, 'getLatest'])->name('notifications.latest');
    
    // ===== ADMIN ROUTES =====
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/export', [AdminDashboardController::class, 'export'])->name('dashboard.export');
        
        // Users management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        
        // Oracle Sync
        Route::get('users-sync', [\App\Http\Controllers\Admin\UserSyncController::class, 'index'])->name('users.sync.index');
        Route::post('users/{user}/sync', [\App\Http\Controllers\Admin\UserSyncController::class, 'syncUser'])->name('users.sync');
        Route::post('users-sync-all', [\App\Http\Controllers\Admin\UserSyncController::class, 'syncAll'])->name('users.sync-all');
        Route::post('users-sync-ldap', [\App\Http\Controllers\Admin\UserSyncController::class, 'syncLdap'])->name('users.sync-ldap');
        Route::post('users-sync-photos', [\App\Http\Controllers\Admin\UserSyncController::class, 'syncPhotos'])->name('users.sync-photos');
        Route::get('users-search-oracle', [\App\Http\Controllers\Admin\UserSyncController::class, 'searchOracle'])->name('users.search-oracle');
        Route::post('users-import', [\App\Http\Controllers\Admin\UserSyncController::class, 'importUser'])->name('users.import');
        Route::post('users-import-all', [\App\Http\Controllers\Admin\UserSyncController::class, 'importAll'])->name('users.import-all');
        Route::get('users-sync-logs', [\App\Http\Controllers\Admin\UserSyncController::class, 'getLogs'])->name('users.sync-logs');
        Route::post('users-sync-logs/clear', [\App\Http\Controllers\Admin\UserSyncController::class, 'clearLogs'])->name('users.sync-logs.clear');
        
        // Groupes management
        Route::resource('groupes', \App\Http\Controllers\Admin\GroupeController::class);
        Route::post('groupes/{groupe}/add-user', [\App\Http\Controllers\Admin\GroupeController::class, 'addUser'])->name('groupes.add-user');
        Route::delete('groupes/{groupe}/remove-user/{user}', [\App\Http\Controllers\Admin\GroupeController::class, 'removeUser'])->name('groupes.remove-user');
        
        // Chargés de consignation
        Route::resource('chargecons', \App\Http\Controllers\Admin\ChargeConsController::class);
        
        // Correspondants
        Route::resource('correspondants', \App\Http\Controllers\Admin\CorrespondantController::class);
        
        // Services destinataires
        Route::resource('services', \App\Http\Controllers\Admin\ServiceDestController::class);
        
        // Observations/Feedback
        Route::resource('observations', \App\Http\Controllers\Admin\ObservationController::class)->except(['create', 'store', 'edit']);
        Route::post('observations/{observation}/mark-processed', [\App\Http\Controllers\Admin\ObservationController::class, 'markAsProcessed'])->name('observations.mark-processed');
        
        // Absences/Intérims
        Route::resource('absences', \App\Http\Controllers\Admin\AbsenceController::class);
        
        // DAPT Management (admin view)
        Route::resource('demandes', \App\Http\Controllers\Admin\DemandeController::class)->only(['index', 'show', 'destroy']);
        Route::get('demandes-statistiques', [\App\Http\Controllers\Admin\DemandeController::class, 'statistiques'])->name('demandes.statistiques');
        Route::get('demandes-export', [\App\Http\Controllers\Admin\DemandeController::class, 'export'])->name('demandes.export');
        
        // NAPT Management (admin view)
        Route::resource('notes', \App\Http\Controllers\Admin\NoteController::class)->only(['index', 'show', 'destroy']);
        Route::get('notes-statistiques', [\App\Http\Controllers\Admin\NoteController::class, 'statistiques'])->name('notes.statistiques');
        Route::get('notes-export', [\App\Http\Controllers\Admin\NoteController::class, 'export'])->name('notes.export');
        Route::get('notes/{note}/timeline', [\App\Http\Controllers\Admin\NoteController::class, 'timeline'])->name('notes.timeline');
        
        // Impersonation (simuler un utilisateur)
        Route::post('impersonate/{user}', [\App\Http\Controllers\Admin\ImpersonateController::class, 'start'])->name('impersonate.start');
        
        // Journal d'activités
        Route::get('activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
    });
    
    // ===== DEMANDEUR ROUTES =====
    Route::middleware(['roleOrInterim:demandeur|admin'])->prefix('demandeur')->name('demandeur.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Demandeur\DemandeController::class, 'dashboard'])->name('dashboard');
        Route::resource('demandes', \App\Http\Controllers\Demandeur\DemandeController::class);
        Route::resource('observations', \App\Http\Controllers\Demandeur\ObservationController::class)->only(['index', 'create', 'store', 'show']);
    });
    
    // ===== API INTERNE (GMAO) =====
    Route::prefix('api-internal')->name('api.')->group(function () {
        Route::get('/lieux-execution', [\App\Http\Controllers\Api\GmaoController::class, 'lieuxExecution'])->name('lieux-execution');
        Route::get('/equipements-enfants', [\App\Http\Controllers\Api\GmaoController::class, 'equipementsEnfants'])->name('equipements-enfants');
        Route::get('/equipements-par-codes', [\App\Http\Controllers\Api\GmaoController::class, 'equipementsParCodes'])->name('equipements-par-codes');
        Route::get('/all-lignes', [\App\Http\Controllers\Api\GmaoController::class, 'allLignes'])->name('all-lignes');
    });
    
    // ===== PDF NAPT VIEW =====
    Route::get('/napt/{note}', function (\App\Models\Note $note) {
        $note->load([
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
        ]);
        return view('pdf.napt', compact('note'));
    })->name('pdf.napt.view');
    
    // ===== PDF DAPT VIEW (accessible à tous les utilisateurs authentifiés) =====
    Route::get('/dapt/{demande}', function (\App\Models\Demande $demande) {
        $demande->load(['demandeur', 'chargeTravaux']);

        $schema = null;
        if (!empty($demande->schema)) {
            $schemaPath = \Illuminate\Support\Facades\Storage::disk('public')->path($demande->schema);
            if (is_file($schemaPath)) {
                $schema = 'data:image/' . pathinfo($schemaPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($schemaPath));
            }
        }

        return view('pdf.dapt', compact('demande', 'schema'));
    })->name('pdf.dapt.view');
    
    // ===== PDF DAPT DOWNLOAD =====
    Route::get('/dapt/{demande}/download', function (\App\Models\Demande $demande) {
        $demande->load(['demandeur', 'chargeTravaux']);

        $schema = null;
        if (!empty($demande->schema)) {
            $schemaPath = \Illuminate\Support\Facades\Storage::disk('public')->path($demande->schema);
            if (is_file($schemaPath)) {
                $schema = 'data:image/' . pathinfo($schemaPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($schemaPath));
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.dapt', compact('demande', 'schema'));
        return $pdf->download('DAPT-' . $demande->numero . '.pdf');
    })->name('pdf.dapt.download');
    
    // ===== DESA ROUTES =====
    Route::middleware(['roleOrInterim:desa|admin'])->prefix('desa')->name('desa.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Desa\DemandeController::class, 'dashboard'])->name('dashboard');
        Route::get('dashboard/export', [\App\Http\Controllers\Desa\DemandeController::class, 'exportDashboard'])->name('dashboard.export');
        Route::get('demandes/export-pdf', [\App\Http\Controllers\Desa\DemandeController::class, 'exportPdf'])->name('demandes.export-pdf');
        Route::post('demandes/{demande}/faire-napt', [\App\Http\Controllers\Desa\DemandeController::class, 'faire_napt'])->name('demandes.faire-napt');
        Route::post('demandes/{demande}/retourner-napt', [\App\Http\Controllers\Desa\DemandeController::class, 'retourner_napt'])->name('demandes.retourner-napt');
        Route::resource('demandes', \App\Http\Controllers\Desa\DemandeController::class);
        Route::get('notes/select-demande', [\App\Http\Controllers\Desa\NoteController::class, 'selectDemande'])->name('notes.select-demande');
        Route::get('notes/export-pdf', [\App\Http\Controllers\Desa\NoteController::class, 'exportPdf'])->name('notes.export-pdf');
        Route::post('notes/{note}/annuler', [\App\Http\Controllers\Desa\NoteController::class, 'annuler'])->name('notes.annuler');
        Route::resource('notes', \App\Http\Controllers\Desa\NoteController::class);
        Route::resource('observations', \App\Http\Controllers\Desa\ObservationController::class)->only(['index', 'show']);
        
        // Diffusion hebdomadaire
        Route::get('diffusion', [\App\Http\Controllers\Desa\NoteController::class, 'manageDiffusion'])->name('diffusion');
        Route::get('diffusion/preview', [\App\Http\Controllers\Desa\NoteController::class, 'previewDiffusion'])->name('diffusion.preview');
        Route::post('diffusion/send', [\App\Http\Controllers\Desa\NoteController::class, 'sendDiffusion'])->name('diffusion.send');
    });
    
    // ===== VERIFICATEUR ROUTES =====
    Route::middleware(['roleOrInterim:verificateur|admin'])->prefix('verificateur')->name('verificateur.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Verificateur\NoteController::class, 'dashboard'])->name('dashboard');
        Route::resource('notes', \App\Http\Controllers\Verificateur\NoteController::class);
    });
    
    // ===== VALIDEUR ROUTES =====
    Route::middleware(['roleOrInterim:valideur|admin'])->prefix('valideur')->name('valideur.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Valideur\NoteController::class, 'dashboard'])->name('dashboard');
        Route::resource('notes', \App\Http\Controllers\Valideur\NoteController::class);
    });
    
    // ===== OPERATEUR CHEF ROUTES (Fiche manœuvre) =====
    Route::middleware(['roleOrInterim:operateurchef|admin'])->prefix('operateurchef')->name('operateurchef.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\OperateurChef\NoteController::class, 'dashboard'])->name('dashboard');
        Route::resource('notes', \App\Http\Controllers\OperateurChef\NoteController::class)->except(['create', 'store', 'destroy']);
        Route::delete('notes/{note}/fiche', [\App\Http\Controllers\OperateurChef\NoteController::class, 'destroyFiche'])->name('notes.destroy-fiche');
        Route::post('notes/{note}/annuler', [\App\Http\Controllers\OperateurChef\NoteController::class, 'annuler'])->name('notes.annuler');
    });

    // ===== OPERATEUR ROUTES (Exécution) =====
    Route::middleware(['roleOrInterim:operateur|admin'])->prefix('operateur')->name('operateur.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Operateur\NoteController::class, 'dashboard'])->name('dashboard');
        Route::resource('notes', \App\Http\Controllers\Operateur\NoteController::class)->except(['create', 'store', 'destroy']);
        Route::post('notes/{note}/annuler', [\App\Http\Controllers\Operateur\NoteController::class, 'annuler'])->name('notes.annuler');
    });

    // ===== DIRECTEUR ROUTES (Supervision / Lecture seule) =====
    Route::middleware(['roleOrInterim:directeur|admin'])->prefix('directeur')->name('directeur.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Directeur\DirecteurController::class, 'dashboard'])->name('dashboard');
        Route::get('dapt', [\App\Http\Controllers\Directeur\DirecteurController::class, 'dapt'])->name('dapt');
        Route::get('dapt/statistiques', [\App\Http\Controllers\Directeur\DirecteurController::class, 'statistiquesDapt'])->name('dapt.statistiques');
        Route::get('dapt/{demande}', [\App\Http\Controllers\Directeur\DirecteurController::class, 'showDapt'])->name('dapt.show');
        Route::get('napt', [\App\Http\Controllers\Directeur\DirecteurController::class, 'napt'])->name('napt');
        Route::get('napt/statistiques', [\App\Http\Controllers\Directeur\DirecteurController::class, 'statistiquesNapt'])->name('napt.statistiques');
        Route::get('napt/{note}', [\App\Http\Controllers\Directeur\DirecteurController::class, 'showNapt'])->name('napt.show');
        Route::get('feedback', [\App\Http\Controllers\Directeur\DirecteurController::class, 'feedback'])->name('feedback');
        Route::post('feedback', [\App\Http\Controllers\Directeur\DirecteurController::class, 'storeFeedback'])->name('feedback.store');
    });
});


<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Demande;
use App\Models\User;
use App\Models\Groupe;
use App\Services\NotificationService;
use App\Notifications\WorkflowNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $demandeur;
    protected User $desa;
    protected User $verificateur;
    protected User $valideur;
    protected User $operateur;
    protected Demande $demande;
    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Créer un groupe
        $groupe = Groupe::create([
            'nom' => 'Groupe Test',
            'email' => 'groupe-test@senelec.sn',
        ]);

        // Créer les utilisateurs avec leurs rôles
        $this->demandeur = User::factory()->create(['groupe_id' => $groupe->id]);
        $this->demandeur->assignRole('demandeur');

        $this->desa = User::factory()->create();
        $this->desa->assignRole('desa');

        $this->verificateur = User::factory()->create();
        $this->verificateur->assignRole('verificateur');

        $this->valideur = User::factory()->create();
        $this->valideur->assignRole('valideur');

        $this->operateur = User::factory()->create();
        $this->operateur->assignRole('operateur');

        // Créer une demande acceptée
        $this->demande = Demande::factory()->acceptee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $this->notificationService = new NotificationService();
    }

    /** @test */
    public function notification_sent_when_demande_accepted()
    {
        Notification::fake();

        $demande = Demande::factory()->creee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $demande->statut = Demande::STATUT_ACCEPTEE;
        $demande->accepte_id = $this->desa->id;
        $demande->save();

        $this->notificationService->notifyDemandeAccepted($demande);

        Notification::assertSentTo(
            $this->demandeur,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'acceptée');
            }
        );
    }

    /** @test */
    public function notification_sent_when_demande_returned()
    {
        Notification::fake();

        $demande = Demande::factory()->creee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $demande->statut = Demande::STATUT_RETOURNEE;
        $demande->motif = 'Informations manquantes';
        $demande->save();

        $this->notificationService->notifyDemandeReturned($demande, 'Informations manquantes');

        Notification::assertSentTo(
            $this->demandeur,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'retournée');
            }
        );
    }

    /** @test */
    public function notification_sent_to_verificateurs_when_napt_sent()
    {
        Notification::fake();

        $note = Note::factory()->enAttenteVerification()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
        ]);

        $this->notificationService->notifyNaptCreated($note);

        Notification::assertSentTo(
            $this->verificateur,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'vérification');
            }
        );
    }

    /** @test */
    public function notification_sent_to_valideurs_when_napt_verified()
    {
        Notification::fake();

        $note = Note::factory()->verifiee()->create([
            'demande_id' => $this->demande->id,
            'verifie_id' => $this->verificateur->id,
        ]);

        $this->notificationService->notifyNaptVerified($note);

        Notification::assertSentTo(
            $this->valideur,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'vérifiée');
            }
        );
    }

    /** @test */
    public function notification_sent_when_napt_validated()
    {
        Notification::fake();

        $note = Note::factory()->validee()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
            'valide_id' => $this->valideur->id,
        ]);

        $this->notificationService->notifyNaptValidated($note);

        // Notification envoyée au DESA
        Notification::assertSentTo(
            $this->desa,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'validée');
            }
        );

        // Notification envoyée aux opérateurs
        Notification::assertSentTo(
            $this->operateur,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'validée');
            }
        );
    }

    /** @test */
    public function notification_sent_to_desa_when_napt_returned_by_verificateur()
    {
        Notification::fake();

        $note = Note::factory()->retournee()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
            'retourne1_id' => $this->verificateur->id,
            'motif' => 'Dates incorrectes',
        ]);

        $this->notificationService->notifyNaptReturned($note, 'vérificateur', 'Dates incorrectes');

        Notification::assertSentTo(
            $this->desa,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->message, 'vérificateur');
            }
        );
    }

    /** @test */
    public function notification_sent_to_verificateur_when_napt_returned_by_valideur()
    {
        Notification::fake();

        $note = Note::factory()->retournee()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
            'verifie_id' => $this->verificateur->id,
            'retourne2_id' => $this->valideur->id,
            'motifbis' => 'Vérification incomplète',
        ]);

        $this->notificationService->notifyNaptReturned($note, 'valideur', 'Vérification incomplète');

        Notification::assertSentTo(
            $this->verificateur,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->message, 'valideur');
            }
        );
    }

    /** @test */
    public function notification_sent_when_napt_cancelled_by_desa()
    {
        Notification::fake();

        $note = Note::factory()->annulee()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
            'annule_id' => $this->desa->id,
        ]);

        $this->notificationService->notifyNaptCancelled($note, 'desa', 'Annulation pour sécurité');

        // Notification envoyée au demandeur
        Notification::assertSentTo(
            $this->demandeur,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'annulée');
            }
        );
    }

    /** @test */
    public function notification_sent_to_desa_when_napt_cancelled_by_operateur()
    {
        Notification::fake();

        $note = Note::factory()->annulee()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
            'annule_id' => $this->operateur->id,
        ]);

        $this->notificationService->notifyNaptCancelled($note, 'operateur', 'Conditions météo');

        // Notification envoyée au demandeur
        Notification::assertSentTo(
            $this->demandeur,
            WorkflowNotification::class
        );

        // Notification envoyée au DESA
        Notification::assertSentTo(
            $this->desa,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->message, 'operateur');
            }
        );
    }

    /** @test */
    public function notification_sent_when_napt_executed()
    {
        Notification::fake();

        $note = Note::factory()->executee()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
            'execute_id' => $this->operateur->id,
        ]);

        $this->notificationService->notifyNaptExecuted($note);

        // Notification envoyée au DESA
        Notification::assertSentTo(
            $this->desa,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'exécutée');
            }
        );

        // Notification envoyée au demandeur
        Notification::assertSentTo(
            $this->demandeur,
            WorkflowNotification::class,
            function ($notification) {
                return str_contains($notification->title, 'exécutée');
            }
        );
    }
}

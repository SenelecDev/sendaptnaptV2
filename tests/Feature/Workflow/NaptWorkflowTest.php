<?php

namespace Tests\Feature\Workflow;

use App\Models\Note;
use App\Models\Demande;
use App\Models\User;
use App\Models\Groupe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NaptWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $demandeur;
    protected User $desa;
    protected User $verificateur;
    protected User $valideur;
    protected User $operateur;
    protected Demande $demande;

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
    }

    /** @test */
    public function desa_can_create_napt_from_accepted_demande()
    {
        $response = $this->actingAs($this->desa)
                         ->get(route('desa.notes.create', ['demande_id' => $this->demande->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function desa_can_save_napt_as_brouillon()
    {
        $response = $this->actingAs($this->desa)
                         ->post(route('desa.notes.store'), [
                             'demande_id' => $this->demande->id,
                             'ddt' => now()->addDays(7)->format('Y-m-d'),
                             'dft' => now()->addDays(8)->format('Y-m-d'),
                             'hdt' => '08:00',
                             'hft' => '17:00',
                             'action' => 'brouillon',
                         ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('notes', [
            'demande_id' => $this->demande->id,
            'statut' => Note::STATUT_BROUILLON,
        ]);
    }

    /** @test */
    public function desa_can_send_napt_to_verification()
    {
        $note = Note::factory()->brouillon()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
        ]);

        $response = $this->actingAs($this->desa)
                         ->put(route('desa.notes.update', $note), [
                             'ddt' => $note->ddt->format('Y-m-d'),
                             'dft' => $note->dft->format('Y-m-d'),
                             'hdt' => $note->hdt,
                             'hft' => $note->hft,
                             'action' => 'envoyer_verification',
                         ]);

        $response->assertRedirect();
        
        $note->refresh();
        $this->assertEquals(Note::STATUT_EN_ATTENTE_VERIFICATION, $note->statut);
    }

    /** @test */
    public function verificateur_can_see_notes_to_verify()
    {
        $note = Note::factory()->enAttenteVerification()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->verificateur)
                         ->get(route('verificateur.notes.index'));

        $response->assertStatus(200);
        $response->assertSee($note->numero_note);
    }

    /** @test */
    public function verificateur_can_verify_note()
    {
        $note = Note::factory()->enAttenteVerification()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->verificateur)
                         ->put(route('verificateur.notes.update', $note), [
                             'action' => 'verifier',
                         ]);

        $response->assertRedirect();
        
        $note->refresh();
        $this->assertEquals(Note::STATUT_VERIFIEE, $note->statut);
        $this->assertEquals($this->verificateur->id, $note->verifie_id);
    }

    /** @test */
    public function verificateur_can_return_note_to_desa()
    {
        $note = Note::factory()->enAttenteVerification()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
        ]);

        $response = $this->actingAs($this->verificateur)
                         ->put(route('verificateur.notes.update', $note), [
                             'action' => 'retourner',
                             'motif' => 'Dates incorrectes',
                         ]);

        $response->assertRedirect();
        
        $note->refresh();
        $this->assertEquals(Note::STATUT_RETOURNEE, $note->statut);
        $this->assertEquals('Dates incorrectes', $note->motif);
        $this->assertEquals($this->verificateur->id, $note->retourne1_id);
    }

    /** @test */
    public function verificateur_cannot_return_note_without_motif()
    {
        $note = Note::factory()->enAttenteVerification()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->verificateur)
                         ->put(route('verificateur.notes.update', $note), [
                             'action' => 'retourner',
                             'motif' => '',
                         ]);

        $response->assertSessionHasErrors('motif');
    }

    /** @test */
    public function valideur_can_see_verified_notes()
    {
        $note = Note::factory()->verifiee()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->valideur)
                         ->get(route('valideur.notes.index'));

        $response->assertStatus(200);
        $response->assertSee($note->numero_note);
    }

    /** @test */
    public function valideur_can_validate_note()
    {
        $note = Note::factory()->verifiee()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->valideur)
                         ->put(route('valideur.notes.update', $note), [
                             'action' => 'valider',
                         ]);

        $response->assertRedirect();
        
        $note->refresh();
        $this->assertEquals(Note::STATUT_VALIDEE, $note->statut);
        $this->assertEquals($this->valideur->id, $note->valide_id);
    }

    /** @test */
    public function valideur_can_return_note_to_verificateur()
    {
        $note = Note::factory()->verifiee()->create([
            'demande_id' => $this->demande->id,
            'verifie_id' => $this->verificateur->id,
        ]);

        $response = $this->actingAs($this->valideur)
                         ->put(route('valideur.notes.update', $note), [
                             'action' => 'retourner',
                             'motifbis' => 'Vérification incomplète',
                         ]);

        $response->assertRedirect();
        
        $note->refresh();
        $this->assertEquals(Note::STATUT_RETOURNEE, $note->statut);
        $this->assertEquals('Vérification incomplète', $note->motifbis);
        $this->assertEquals($this->valideur->id, $note->retourne2_id);
    }

    /** @test */
    public function operateur_can_see_validated_notes()
    {
        $note = Note::factory()->validee()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->operateur)
                         ->get(route('operateur.notes.index', ['statut' => Note::STATUT_VALIDEE]));

        $response->assertStatus(200);
        $response->assertSee($note->numero_note);
    }

    /** @test */
    public function operateur_can_start_execution()
    {
        $note = Note::factory()->validee()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->operateur)
                         ->put(route('operateur.notes.update', $note), [
                             'action' => 'demarrer',
                         ]);

        $response->assertRedirect();
        
        $note->refresh();
        $this->assertEquals(Note::STATUT_EN_COURS_EXECUTION, $note->statut);
    }

    /** @test */
    public function operateur_can_finish_execution()
    {
        $note = Note::factory()->enCoursExecution()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->operateur)
                         ->put(route('operateur.notes.update', $note), [
                             'action' => 'terminer',
                         ]);

        $response->assertRedirect();
        
        $note->refresh();
        $this->assertEquals(Note::STATUT_EXECUTEE, $note->statut);
    }

    /** @test */
    public function desa_can_cancel_note()
    {
        $note = Note::factory()->validee()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
        ]);

        $response = $this->actingAs($this->desa)
                         ->post(route('desa.notes.annuler', $note), [
                             'commentanul' => 'Annulation pour raison de sécurité',
                         ]);

        $response->assertRedirect();
        
        $note->refresh();
        $this->assertEquals(Note::STATUT_ANNULEE, $note->statut);
        $this->assertEquals('Annulation pour raison de sécurité', $note->commentanul);
    }

    /** @test */
    public function operateur_can_cancel_validated_note()
    {
        $note = Note::factory()->validee()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->operateur)
                         ->post(route('operateur.notes.annuler', $note), [
                             'commentanul' => 'Conditions météo défavorables',
                         ]);

        $response->assertRedirect();
        
        $note->refresh();
        $this->assertEquals(Note::STATUT_ANNULEE, $note->statut);
    }

    /** @test */
    public function cannot_cancel_already_executed_note()
    {
        $note = Note::factory()->executee()->create([
            'demande_id' => $this->demande->id,
        ]);

        $response = $this->actingAs($this->desa)
                         ->post(route('desa.notes.annuler', $note), [
                             'commentanul' => 'Tentative annulation',
                         ]);

        $note->refresh();
        $this->assertEquals(Note::STATUT_EXECUTEE, $note->statut);
    }

    /** @test */
    public function complete_workflow_test()
    {
        // 1. DESA crée une NAPT
        $response = $this->actingAs($this->desa)
                         ->post(route('desa.notes.store'), [
                             'demande_id' => $this->demande->id,
                             'ddt' => now()->addDays(7)->format('Y-m-d'),
                             'dft' => now()->addDays(8)->format('Y-m-d'),
                             'hdt' => '08:00',
                             'hft' => '17:00',
                             'action' => 'envoyer_verification',
                         ]);

        $note = Note::where('demande_id', $this->demande->id)->first();
        $this->assertNotNull($note);
        $this->assertEquals(Note::STATUT_EN_ATTENTE_VERIFICATION, $note->statut);

        // 2. Vérificateur vérifie
        $response = $this->actingAs($this->verificateur)
                         ->put(route('verificateur.notes.update', $note), [
                             'action' => 'verifier',
                         ]);

        $note->refresh();
        $this->assertEquals(Note::STATUT_VERIFIEE, $note->statut);

        // 3. Valideur valide
        $response = $this->actingAs($this->valideur)
                         ->put(route('valideur.notes.update', $note), [
                             'action' => 'valider',
                         ]);

        $note->refresh();
        $this->assertEquals(Note::STATUT_VALIDEE, $note->statut);

        // 4. Opérateur démarre
        $response = $this->actingAs($this->operateur)
                         ->put(route('operateur.notes.update', $note), [
                             'action' => 'demarrer',
                         ]);

        $note->refresh();
        $this->assertEquals(Note::STATUT_EN_COURS_EXECUTION, $note->statut);

        // 5. Opérateur termine
        $response = $this->actingAs($this->operateur)
                         ->put(route('operateur.notes.update', $note), [
                             'action' => 'terminer',
                         ]);

        $note->refresh();
        $this->assertEquals(Note::STATUT_EXECUTEE, $note->statut);
    }

    /** @test */
    public function return_workflow_test_valideur_to_verificateur()
    {
        $note = Note::factory()->verifiee()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
            'verifie_id' => $this->verificateur->id,
        ]);

        // Valideur retourne au vérificateur
        $response = $this->actingAs($this->valideur)
                         ->put(route('valideur.notes.update', $note), [
                             'action' => 'retourner',
                             'motifbis' => 'Vérification incomplète',
                         ]);

        $note->refresh();
        $this->assertEquals(Note::STATUT_RETOURNEE, $note->statut);
        $this->assertEquals($this->valideur->id, $note->retourne2_id);

        // Le vérificateur peut re-vérifier
        $response = $this->actingAs($this->verificateur)
                         ->put(route('verificateur.notes.update', $note), [
                             'action' => 'verifier',
                         ]);

        $note->refresh();
        $this->assertEquals(Note::STATUT_VERIFIEE, $note->statut);
    }

    /** @test */
    public function return_workflow_test_verificateur_to_desa()
    {
        $note = Note::factory()->enAttenteVerification()->create([
            'demande_id' => $this->demande->id,
            'etabli_id' => $this->desa->id,
        ]);

        // Vérificateur retourne au DESA
        $response = $this->actingAs($this->verificateur)
                         ->put(route('verificateur.notes.update', $note), [
                             'action' => 'retourner',
                             'motif' => 'Dates incorrectes',
                         ]);

        $note->refresh();
        $this->assertEquals(Note::STATUT_RETOURNEE, $note->statut);
        $this->assertEquals($this->verificateur->id, $note->retourne1_id);

        // Le DESA peut modifier et renvoyer
        $response = $this->actingAs($this->desa)
                         ->put(route('desa.notes.update', $note), [
                             'ddt' => now()->addDays(10)->format('Y-m-d'),
                             'dft' => now()->addDays(11)->format('Y-m-d'),
                             'hdt' => '08:00',
                             'hft' => '17:00',
                             'action' => 'envoyer_verification',
                         ]);

        $note->refresh();
        $this->assertEquals(Note::STATUT_EN_ATTENTE_VERIFICATION, $note->statut);
    }
}

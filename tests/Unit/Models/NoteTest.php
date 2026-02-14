<?php

namespace Tests\Unit\Models;

use App\Models\Note;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    /** @test */
    public function it_can_create_a_note()
    {
        $demande = Demande::factory()->create();
        $user = User::factory()->create();
        
        $note = Note::create([
            'numero_note' => '00001-2026',
            'demande_id' => $demande->id,
            'etabli_id' => $user->id,
            'numero_semaine' => 7,
            'date' => now(),
            'ddt' => now()->addDays(7),
            'dft' => now()->addDays(8),
            'hdt' => '08:00',
            'hft' => '17:00',
            'statut' => Note::STATUT_BROUILLON,
        ]);

        $this->assertDatabaseHas('notes', [
            'numero_note' => '00001-2026',
            'statut' => Note::STATUT_BROUILLON,
        ]);
    }

    /** @test */
    public function it_belongs_to_a_demande()
    {
        $demande = Demande::factory()->create();
        $note = Note::factory()->create(['demande_id' => $demande->id]);

        $this->assertInstanceOf(Demande::class, $note->demande);
        $this->assertEquals($demande->id, $note->demande->id);
    }

    /** @test */
    public function it_has_etabli_par_relationship()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['etabli_id' => $user->id]);

        $this->assertInstanceOf(User::class, $note->etabliPar);
        $this->assertEquals($user->id, $note->etabliPar->id);
    }

    /** @test */
    public function it_has_verifie_par_relationship()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['verifie_id' => $user->id]);

        $this->assertInstanceOf(User::class, $note->verifiePar);
        $this->assertEquals($user->id, $note->verifiePar->id);
    }

    /** @test */
    public function it_has_valide_par_relationship()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['valide_id' => $user->id]);

        $this->assertInstanceOf(User::class, $note->validePar);
        $this->assertEquals($user->id, $note->validePar->id);
    }

    /** @test */
    public function it_returns_correct_statut_badge_class()
    {
        $note = Note::factory()->create(['statut' => Note::STATUT_BROUILLON]);
        $this->assertEquals('badge-secondary', $note->getStatutBadgeClass());

        $note->statut = Note::STATUT_VALIDEE;
        $this->assertEquals('badge-success', $note->getStatutBadgeClass());

        $note->statut = Note::STATUT_RETOURNEE;
        $this->assertEquals('badge-danger', $note->getStatutBadgeClass());

        $note->statut = Note::STATUT_ANNULEE;
        $this->assertEquals('badge-danger', $note->getStatutBadgeClass());
    }

    /** @test */
    public function it_can_check_workflow_status()
    {
        $note = Note::factory()->create(['statut' => Note::STATUT_BROUILLON]);
        
        $this->assertTrue($note->isBrouillon());
        $this->assertFalse($note->isValidee());
        $this->assertFalse($note->isAnnulee());

        $note->statut = Note::STATUT_VALIDEE;
        $this->assertTrue($note->isValidee());

        $note->statut = Note::STATUT_ANNULEE;
        $this->assertTrue($note->isAnnulee());
    }

    /** @test */
    public function it_can_check_if_editable()
    {
        $noteBrouillon = Note::factory()->create(['statut' => Note::STATUT_BROUILLON]);
        $noteValidee = Note::factory()->create(['statut' => Note::STATUT_VALIDEE]);

        $this->assertTrue($noteBrouillon->isEditable());
        $this->assertFalse($noteValidee->isEditable());
    }

    /** @test */
    public function it_can_check_if_verifiable()
    {
        $noteEnAttente = Note::factory()->create(['statut' => Note::STATUT_EN_ATTENTE_VERIFICATION]);
        $noteValidee = Note::factory()->create(['statut' => Note::STATUT_VALIDEE]);

        $this->assertTrue($noteEnAttente->isVerifiable());
        $this->assertFalse($noteValidee->isVerifiable());
    }

    /** @test */
    public function it_can_check_if_validable()
    {
        $noteVerifiee = Note::factory()->create(['statut' => Note::STATUT_VERIFIEE]);
        $noteBrouillon = Note::factory()->create(['statut' => Note::STATUT_BROUILLON]);

        $this->assertTrue($noteVerifiee->isValidable());
        $this->assertFalse($noteBrouillon->isValidable());
    }

    /** @test */
    public function it_can_check_if_annulable()
    {
        $noteValidee = Note::factory()->create(['statut' => Note::STATUT_VALIDEE]);
        $noteExecutee = Note::factory()->create(['statut' => Note::STATUT_EXECUTEE]);
        $noteAnnulee = Note::factory()->create(['statut' => Note::STATUT_ANNULEE]);

        $this->assertTrue($noteValidee->isAnnulable());
        $this->assertFalse($noteExecutee->isAnnulable());
        $this->assertFalse($noteAnnulee->isAnnulable());
    }

    /** @test */
    public function it_generates_unique_numero_note()
    {
        $numero1 = Note::generateNumero();
        $numero2 = Note::generateNumero();

        // Les numéros doivent suivre le format XXXXX-YYYY
        $this->assertMatchesRegularExpression('/^\d{5}-\d{4}$/', $numero1);
        
        // Le deuxième doit être différent (incrémenté)
        $this->assertNotEquals($numero1, $numero2);
    }
}

<?php

namespace Tests\Unit\Models;

use App\Models\Demande;
use App\Models\User;
use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemandeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    /** @test */
    public function it_can_create_a_demande()
    {
        $user = User::factory()->create();
        
        $demande = Demande::create([
            'numero_demande' => '00001-2026',
            'demandeur_id' => $user->id,
            'designation' => 'Test designation',
            'lieu_execution' => 'Poste Test',
            'ddp' => now()->addDays(7),
            'dfp' => now()->addDays(8),
            'hdp' => '08:00',
            'hfp' => '17:00',
            'statut' => Demande::STATUT_CREEE,
        ]);

        $this->assertDatabaseHas('demandes', [
            'numero_demande' => '00001-2026',
            'statut' => Demande::STATUT_CREEE,
        ]);
    }

    /** @test */
    public function it_belongs_to_a_demandeur()
    {
        $user = User::factory()->create();
        
        $demande = Demande::factory()->create([
            'demandeur_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $demande->demandeur);
        $this->assertEquals($user->id, $demande->demandeur->id);
    }

    /** @test */
    public function it_can_have_a_note()
    {
        $demande = Demande::factory()->create();
        
        $note = Note::factory()->create([
            'demande_id' => $demande->id,
        ]);

        $this->assertInstanceOf(Note::class, $demande->note);
        $this->assertEquals($note->id, $demande->note->id);
    }

    /** @test */
    public function it_can_check_if_editable()
    {
        $demandeCreee = Demande::factory()->create(['statut' => Demande::STATUT_CREEE]);
        $demandeAcceptee = Demande::factory()->create(['statut' => Demande::STATUT_ACCEPTEE]);

        $this->assertTrue($demandeCreee->isEditable());
        $this->assertFalse($demandeAcceptee->isEditable());
    }

    /** @test */
    public function it_can_check_if_napt_can_be_created()
    {
        $demandeAcceptee = Demande::factory()->create(['statut' => Demande::STATUT_ACCEPTEE]);
        
        $this->assertTrue($demandeAcceptee->canCreateNapt());

        // Créer une note pour cette demande
        Note::factory()->create(['demande_id' => $demandeAcceptee->id]);
        
        // Rafraîchir la demande
        $demandeAcceptee->refresh();
        
        $this->assertFalse($demandeAcceptee->canCreateNapt());
    }

    /** @test */
    public function it_returns_correct_badge_class()
    {
        $demande = Demande::factory()->create(['statut' => Demande::STATUT_CREEE]);
        $this->assertEquals('badge-info', $demande->getStatutBadgeClass());

        $demande->statut = Demande::STATUT_ACCEPTEE;
        $this->assertEquals('badge-success', $demande->getStatutBadgeClass());

        $demande->statut = Demande::STATUT_RETOURNEE;
        $this->assertEquals('badge-danger', $demande->getStatutBadgeClass());
    }

    /** @test */
    public function it_generates_unique_numero_demande()
    {
        $numero1 = Demande::generateNumero();
        $numero2 = Demande::generateNumero();

        // Les numéros doivent suivre le format XXXXX-YYYY
        $this->assertMatchesRegularExpression('/^\d{5}-\d{4}$/', $numero1);
        
        // Le deuxième doit être différent (incrémenté)
        $this->assertNotEquals($numero1, $numero2);
    }
}

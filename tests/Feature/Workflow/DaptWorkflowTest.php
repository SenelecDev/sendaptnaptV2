<?php

namespace Tests\Feature\Workflow;

use App\Models\Demande;
use App\Models\User;
use App\Models\Groupe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DaptWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $demandeur;
    protected User $desa;
    protected Groupe $groupe;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Créer un groupe
        $this->groupe = Groupe::create([
            'nom' => 'Groupe Test',
            'email' => 'groupe-test@senelec.sn',
        ]);

        // Créer un demandeur
        $this->demandeur = User::factory()->create([
            'groupe_id' => $this->groupe->id,
        ]);
        $this->demandeur->assignRole('demandeur');

        // Créer un DESA
        $this->desa = User::factory()->create();
        $this->desa->assignRole('desa');
    }

    /** @test */
    public function demandeur_can_access_demande_create_page()
    {
        $response = $this->actingAs($this->demandeur)
                         ->get(route('demandeur.demandes.create'));

        $response->assertStatus(200);
        $response->assertSee('Nouvelle demande');
    }

    /** @test */
    public function demandeur_can_create_a_dapt()
    {
        $response = $this->actingAs($this->demandeur)
                         ->post(route('demandeur.demandes.store'), [
                             'designation' => 'Test de création DAPT',
                             'lieu_execution' => 'Poste Test',
                             'ddp' => now()->addDays(7)->format('Y-m-d'),
                             'dfp' => now()->addDays(8)->format('Y-m-d'),
                             'hdp' => '08:00',
                             'hfp' => '17:00',
                             'description_travaux' => 'Description des travaux de test',
                         ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('demandes', [
            'designation' => 'Test de création DAPT',
            'demandeur_id' => $this->demandeur->id,
            'statut' => Demande::STATUT_CREEE,
        ]);
    }

    /** @test */
    public function demandeur_can_view_their_demandes()
    {
        $demande = Demande::factory()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $response = $this->actingAs($this->demandeur)
                         ->get(route('demandeur.demandes.index'));

        $response->assertStatus(200);
        $response->assertSee($demande->numero_demande);
    }

    /** @test */
    public function demandeur_cannot_see_other_users_demandes()
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('demandeur');
        
        $demande = Demande::factory()->create([
            'demandeur_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->demandeur)
                         ->get(route('demandeur.demandes.index'));

        $response->assertStatus(200);
        $response->assertDontSee($demande->numero_demande);
    }

    /** @test */
    public function demandeur_can_edit_created_demande()
    {
        $demande = Demande::factory()->creee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $response = $this->actingAs($this->demandeur)
                         ->get(route('demandeur.demandes.edit', $demande));

        $response->assertStatus(200);
    }

    /** @test */
    public function demandeur_cannot_edit_accepted_demande()
    {
        $demande = Demande::factory()->acceptee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $response = $this->actingAs($this->demandeur)
                         ->get(route('demandeur.demandes.edit', $demande));

        $response->assertRedirect();
    }

    /** @test */
    public function desa_can_see_all_demandes()
    {
        $demande1 = Demande::factory()->create(['demandeur_id' => $this->demandeur->id]);
        $demande2 = Demande::factory()->create();

        $response = $this->actingAs($this->desa)
                         ->get(route('desa.demandes.index'));

        $response->assertStatus(200);
        $response->assertSee($demande1->numero_demande);
        $response->assertSee($demande2->numero_demande);
    }

    /** @test */
    public function desa_can_accept_demande()
    {
        $demande = Demande::factory()->creee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $response = $this->actingAs($this->desa)
                         ->put(route('desa.demandes.update', $demande), [
                             'action' => 'accepter',
                         ]);

        $response->assertRedirect();
        
        $demande->refresh();
        $this->assertEquals(Demande::STATUT_ACCEPTEE, $demande->statut);
        $this->assertEquals($this->desa->id, $demande->accepte_id);
    }

    /** @test */
    public function desa_can_return_demande_with_motif()
    {
        $demande = Demande::factory()->creee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $response = $this->actingAs($this->desa)
                         ->put(route('desa.demandes.update', $demande), [
                             'action' => 'retourner',
                             'motif' => 'Informations manquantes sur le lieu',
                         ]);

        $response->assertRedirect();
        
        $demande->refresh();
        $this->assertEquals(Demande::STATUT_RETOURNEE, $demande->statut);
        $this->assertEquals('Informations manquantes sur le lieu', $demande->motif);
    }

    /** @test */
    public function desa_cannot_return_demande_without_motif()
    {
        $demande = Demande::factory()->creee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $response = $this->actingAs($this->desa)
                         ->put(route('desa.demandes.update', $demande), [
                             'action' => 'retourner',
                             'motif' => '',
                         ]);

        $response->assertSessionHasErrors('motif');
        
        $demande->refresh();
        $this->assertEquals(Demande::STATUT_CREEE, $demande->statut);
    }

    /** @test */
    public function demandeur_can_edit_returned_demande()
    {
        $demande = Demande::factory()->retournee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $response = $this->actingAs($this->demandeur)
                         ->get(route('demandeur.demandes.edit', $demande));

        $response->assertStatus(200);
    }

    /** @test */
    public function demandeur_can_resubmit_returned_demande()
    {
        $demande = Demande::factory()->retournee()->create([
            'demandeur_id' => $this->demandeur->id,
        ]);

        $response = $this->actingAs($this->demandeur)
                         ->put(route('demandeur.demandes.update', $demande), [
                             'designation' => 'Demande corrigée',
                             'lieu_execution' => $demande->lieu_execution,
                             'ddp' => $demande->ddp->format('Y-m-d'),
                             'dfp' => $demande->dfp->format('Y-m-d'),
                             'hdp' => $demande->hdp,
                             'hfp' => $demande->hfp,
                             'description_travaux' => 'Travaux mis à jour',
                         ]);

        $response->assertRedirect();
        
        $demande->refresh();
        $this->assertEquals(Demande::STATUT_CREEE, $demande->statut);
        $this->assertEquals('Demande corrigée', $demande->designation);
    }

    /** @test */
    public function guest_cannot_access_demande_pages()
    {
        $response = $this->get(route('demandeur.demandes.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('demandeur.demandes.create'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function demandeur_cannot_access_desa_pages()
    {
        $response = $this->actingAs($this->demandeur)
                         ->get(route('desa.demandes.index'));

        $response->assertStatus(403);
    }
}

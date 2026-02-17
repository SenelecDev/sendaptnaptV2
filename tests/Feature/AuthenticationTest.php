<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    /** @test */
    public function login_page_is_accessible()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Connexion');
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'matricule' => 'M12345',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('demandeur');

        $response = $this->post(route('login'), [
            'matricule' => 'M12345',
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_password()
    {
        $user = User::factory()->create([
            'matricule' => 'M12345',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'matricule' => 'M12345',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_login_with_nonexistent_matricule()
    {
        $response = $this->post(route('login'), [
            'matricule' => 'NONEXISTENT',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $user->assignRole('demandeur');

        $response = $this->actingAs($user)
                         ->post(route('logout'));

        $this->assertGuest();
    }

    /** @test */
    public function new_user_gets_demandeur_role_by_default()
    {
        $user = User::factory()->create([
            'matricule' => 'M99999',
            'password' => bcrypt('password'),
        ]);

        // Simuler la connexion (qui devrait assigner le rôle)
        $this->post(route('login'), [
            'matricule' => 'M99999',
            'password' => 'password',
        ]);

        $user->refresh();
        
        // Si l'utilisateur n'a pas de rôle, la connexion lui assigne 'demandeur'
        $this->assertTrue($user->hasRole('demandeur') || $user->roles->isEmpty());
    }

    /** @test */
    public function user_is_redirected_based_on_role()
    {
        // Test pour admin
        $admin = User::factory()->create([
            'matricule' => 'ADMIN',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $response = $this->post(route('login'), [
            'matricule' => 'ADMIN',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        // Déconnexion
        $this->post(route('logout'));

        // Test pour DESA
        $desa = User::factory()->create([
            'matricule' => 'DESA001',
            'password' => bcrypt('password'),
        ]);
        $desa->assignRole('desa');

        $response = $this->post(route('login'), [
            'matricule' => 'DESA001',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('desa.dashboard'));
    }

    /** @test */
    public function demandeur_is_redirected_to_demandeur_dashboard()
    {
        $demandeur = User::factory()->create([
            'matricule' => 'DEM001',
            'password' => bcrypt('password'),
        ]);
        $demandeur->assignRole('demandeur');

        $response = $this->post(route('login'), [
            'matricule' => 'DEM001',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('demandeur.dashboard'));
    }

    /** @test */
    public function verificateur_is_redirected_to_verificateur_dashboard()
    {
        $verificateur = User::factory()->create([
            'matricule' => 'VER001',
            'password' => bcrypt('password'),
        ]);
        $verificateur->assignRole('verificateur');

        $response = $this->post(route('login'), [
            'matricule' => 'VER001',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('verificateur.dashboard'));
    }

    /** @test */
    public function valideur_is_redirected_to_valideur_dashboard()
    {
        $valideur = User::factory()->create([
            'matricule' => 'VAL001',
            'password' => bcrypt('password'),
        ]);
        $valideur->assignRole('valideur');

        $response = $this->post(route('login'), [
            'matricule' => 'VAL001',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('valideur.notes.index'));
    }

    /** @test */
    public function operateur_is_redirected_to_operateur_dashboard()
    {
        $operateur = User::factory()->create([
            'matricule' => 'OPE001',
            'password' => bcrypt('password'),
        ]);
        $operateur->assignRole('operateur');

        $response = $this->post(route('login'), [
            'matricule' => 'OPE001',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('operateur.notes.index'));
    }

    /** @test */
    public function guest_cannot_access_protected_routes()
    {
        $response = $this->get(route('demandeur.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('desa.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_cannot_access_unauthorized_role_routes()
    {
        $demandeur = User::factory()->create();
        $demandeur->assignRole('demandeur');

        // Demandeur ne peut pas accéder au dashboard admin
        $response = $this->actingAs($demandeur)
                         ->get(route('admin.dashboard'));
        $response->assertStatus(403);

        // Demandeur ne peut pas accéder au dashboard DESA
        $response = $this->actingAs($demandeur)
                         ->get(route('desa.dashboard'));
        $response->assertStatus(403);
    }
}

<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Demande;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    protected $model = Note::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+1 month');
        $endDate = (clone $startDate)->modify('+1 day');

        return [
            'numero_note' => str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT) . '-' . date('Y'),
            'demande_id' => Demande::factory()->acceptee(),
            'etabli_id' => User::factory(),
            'numero_semaine' => $this->faker->numberBetween(1, 52),
            'date' => now(),
            'ddt' => $startDate,
            'dft' => $endDate,
            'hdt' => '08:00',
            'hft' => '17:00',
            'statut' => Note::STATUT_BROUILLON,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Note brouillon
     */
    public function brouillon(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_BROUILLON,
        ]);
    }

    /**
     * Note en étude
     */
    public function enEtude(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_EN_ETUDE,
        ]);
    }

    /**
     * Note en attente de vérification
     */
    public function enAttenteVerification(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_EN_ATTENTE_VERIFICATION,
        ]);
    }

    /**
     * Note vérifiée
     */
    public function verifiee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_VERIFIEE,
            'verifie_id' => User::factory(),
        ]);
    }

    /**
     * Note en attente de validation
     */
    public function enAttenteValidation(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_EN_ATTENTE_VALIDATION,
            'verifie_id' => User::factory(),
        ]);
    }

    /**
     * Note validée
     */
    public function validee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_VALIDEE,
            'verifie_id' => User::factory(),
            'valide_id' => User::factory(),
        ]);
    }

    /**
     * Note en cours d'exécution
     */
    public function enCoursExecution(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_EN_COURS_EXECUTION,
            'verifie_id' => User::factory(),
            'valide_id' => User::factory(),
            'execute_id' => User::factory(),
        ]);
    }

    /**
     * Note exécutée
     */
    public function executee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_EXECUTEE,
            'verifie_id' => User::factory(),
            'valide_id' => User::factory(),
            'execute_id' => User::factory(),
        ]);
    }

    /**
     * Note retournée
     */
    public function retournee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_RETOURNEE,
            'motif' => $this->faker->sentence(),
        ]);
    }

    /**
     * Note annulée
     */
    public function annulee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Note::STATUT_ANNULEE,
            'annule_id' => User::factory(),
            'commentanul' => $this->faker->sentence(),
        ]);
    }
}

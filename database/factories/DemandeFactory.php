<?php

namespace Database\Factories;

use App\Models\Demande;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Demande>
 */
class DemandeFactory extends Factory
{
    protected $model = Demande::class;

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
            'numero_demande' => str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT) . '-' . date('Y'),
            'demandeur_id' => User::factory(),
            'designation' => $this->faker->sentence(4),
            'lieu_execution' => 'Poste ' . $this->faker->city(),
            'ddp' => $startDate,
            'dfp' => $endDate,
            'hdp' => '08:00',
            'hfp' => '17:00',
            'description_travaux' => $this->faker->paragraph(),
            'statut' => Demande::STATUT_CREEE,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Demande créée
     */
    public function creee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Demande::STATUT_CREEE,
        ]);
    }

    /**
     * Demande acceptée
     */
    public function acceptee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Demande::STATUT_ACCEPTEE,
            'accepte_id' => User::factory(),
        ]);
    }

    /**
     * Demande retournée
     */
    public function retournee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => Demande::STATUT_RETOURNEE,
            'motif' => $this->faker->sentence(),
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'matricule' => 'M' . fake()->unique()->numerify('#####'),
            'name' => fake()->name(),
            'prenom' => fake()->firstName(),
            'nom' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'telephone' => fake()->phoneNumber(),
            'service' => fake()->randomElement(['DPE', 'SME', 'SML', 'DSI', 'DRH']),
            'departement' => fake()->randomElement(['Direction Technique', 'Direction Commerciale', 'Direction Financière']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is a demandeur.
     */
    public function demandeur(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('demandeur');
        });
    }

    /**
     * Indicate that the user is a DESA.
     */
    public function desa(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('desa');
        });
    }

    /**
     * Indicate that the user is a verificateur.
     */
    public function verificateur(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('verificateur');
        });
    }

    /**
     * Indicate that the user is a valideur.
     */
    public function valideur(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('valideur');
        });
    }

    /**
     * Indicate that the user is an operateur.
     */
    public function operateur(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('operateur');
        });
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('admin');
        });
    }
}

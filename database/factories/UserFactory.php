<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory pour le modèle User.
 */
class UserFactory extends Factory
{
  /**
   * État par défaut du modèle User.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'firstname' => fake()->firstName(),
      'lastname' => fake()->lastName(),
      'email' => fake()->unique()->safeEmail(),
      'phone' => fake()->unique()->numerify('+243#########'),
      'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
      'status_id' => Status::query()->first()?->id,
      'email_verified_at' => now(),
      'remember_token' => Str::random(10),
    ];
  }

  /**
   * Utilisateur sans e-mail vérifié.
   */
  public function unverified(): static
  {
    return $this->state(fn (array $attributes) => [
      'email_verified_at' => null,
    ]);
  }
}

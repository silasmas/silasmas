<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Données initiales : statuts, rôles et compte administrateur Filament.
 */
class SdevSeeder extends Seeder
{
  /**
   * Exécute le seed des données SDEV.
   */
  public function run(): void
  {
    $actif = Status::firstOrCreate(
      ['status_name' => 'Actif'],
      [
        'status_description' => 'Compte ou ressource active',
        'color' => '#22c55e',
      ]
    );

    Status::firstOrCreate(
      ['status_name' => 'Inactif'],
      [
        'status_description' => 'Compte ou ressource inactive',
        'color' => '#94a3b8',
      ]
    );

    Status::firstOrCreate(
      ['status_name' => 'Nouveau'],
      [
        'status_description' => 'Message ou élément non traité',
        'color' => '#f59e0b',
      ]
    );

    $adminRole = Role::firstOrCreate(
      ['role_name' => 'Administrateur'],
      ['role_description' => 'Accès au panneau Filament et gestion complète']
    );

    Role::firstOrCreate(
      ['role_name' => 'Collaborateur'],
      ['role_description' => 'Membre de l\'équipe SDEV']
    );

    $adminEmail = env('ADMIN_EMAIL', 'admin@silasmas.com');
    $adminPassword = env('ADMIN_PASSWORD', 'Password123!');

    $admin = User::firstOrCreate(
      ['email' => $adminEmail],
      [
        'firstname' => 'Admin',
        'lastname' => 'SDEV',
        'phone' => null,
        'password' => Hash::make($adminPassword),
        'status_id' => $actif->id,
        'email_verified_at' => now(),
      ]
    );

    $admin->roles()->syncWithoutDetaching([$adminRole->id]);
  }
}

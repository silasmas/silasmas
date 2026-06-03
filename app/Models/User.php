<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Utilisateur (équipe SDEV et accès admin Filament).
 */
class User extends Authenticatable implements FilamentUser
{
  use HasApiTokens, HasFactory, Notifiable;

  protected $guarded = [];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'birthdate' => 'date',
  ];

  /**
   * Nom affiché dans Filament et l'interface.
   */
  public function getNameAttribute(): string
  {
    $fullName = trim("{$this->firstname} {$this->lastname}");

    return $fullName !== '' ? $fullName : (string) ($this->email ?? $this->phone ?? 'Utilisateur');
  }

  /**
   * Détermine si l'utilisateur peut accéder au panneau admin.
   */
  public function canAccessPanel(Panel $panel): bool
  {
    if ($panel->getId() !== 'admin') {
      return false;
    }

    return $this->roles()->where('role_name', 'Administrateur')->exists();
  }

  /**
   * Rôles (relation N-N).
   */
  public function roles()
  {
    return $this->belongsToMany(Role::class);
  }

  /**
   * Statut de l'utilisateur.
   */
  public function status()
  {
    return $this->belongsTo(Status::class);
  }

  /**
   * Projets associés à l'utilisateur.
   */
  public function projects()
  {
    return $this->hasMany(Project::class);
  }
}

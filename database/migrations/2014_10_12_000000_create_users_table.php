<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Crée la table des utilisateurs (colonnes de base ; status_id ajouté ensuite).
   */
  public function up(): void
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('firstname')->nullable();
      $table->string('lastname')->nullable();
      $table->string('surname')->nullable();
      $table->string('gender', 20)->nullable();
      $table->date('birthdate')->nullable();
      $table->string('phone', 30)->nullable()->unique();
      $table->string('email')->nullable()->unique();
      $table->string('avatar_url')->nullable();
      $table->text('profile_description')->nullable();
      $table->string('password');
      $table->timestamp('email_verified_at')->nullable();
      $table->rememberToken();
      $table->timestamps();
    });
  }

  /**
   * Supprime la table users.
   */
  public function down(): void
  {
    Schema::dropIfExists('users');
  }
};

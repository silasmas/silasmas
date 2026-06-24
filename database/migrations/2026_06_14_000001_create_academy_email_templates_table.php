<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Crée la table des modèles d'e-mails Academy personnalisables.
   */
  public function up(): void
  {
    Schema::create('academy_email_templates', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('slug')->unique();
      $table->string('category')->default('general');
      $table->string('subject');
      $table->longText('body');
      $table->text('description')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }

  /**
   * Supprime la table des modèles d'e-mails Academy.
   */
  public function down(): void
  {
    Schema::dropIfExists('academy_email_templates');
  }
};

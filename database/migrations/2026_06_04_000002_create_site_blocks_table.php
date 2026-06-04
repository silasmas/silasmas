<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Crée la table de contenu dynamique du site vitrine.
   */
  public function up(): void
  {
    Schema::create('site_blocks', function (Blueprint $table) {
      $table->id();
      $table->string('group', 32);
      $table->string('title');
      $table->string('subtitle')->nullable();
      $table->text('body')->nullable();
      $table->text('secondary_body')->nullable();
      $table->string('icon', 64)->nullable();
      $table->unsignedTinyInteger('level')->nullable();
      $table->string('image')->nullable();
      $table->unsignedInteger('sort_order')->default(0);
      $table->boolean('is_published')->default(true);
      $table->timestamps();

      $table->index(['group', 'is_published', 'sort_order']);
    });
  }

  /**
   * Supprime la table site_blocks.
   */
  public function down(): void
  {
    Schema::dropIfExists('site_blocks');
  }
};

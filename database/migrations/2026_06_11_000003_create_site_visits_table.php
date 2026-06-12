<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Crée la table de suivi des visites et clics sur le site vitrine.
   */
  public function up(): void
  {
    Schema::create('site_visits', function (Blueprint $table) {
      $table->id();
      $table->string('event_type', 20);
      $table->string('path', 500);
      $table->string('page_title')->nullable();
      $table->string('click_label')->nullable();
      $table->string('click_target', 500)->nullable();
      $table->string('country_code', 2)->default('XX');
      $table->string('country_name', 100)->nullable();
      $table->timestamp('visited_at');
      $table->string('visitor_key', 64)->nullable();
      $table->string('referrer', 500)->nullable();
      $table->timestamps();

      $table->index(['visited_at', 'event_type']);
      $table->index('country_code');
      $table->index('path');
    });
  }

  /**
   * Supprime la table de suivi.
   */
  public function down(): void
  {
    Schema::dropIfExists('site_visits');
  }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Crée la table de paramétrage global du site (singleton).
   */
  public function up(): void
  {
    Schema::create('site_settings', function (Blueprint $table) {
      $table->id();
      $table->string('site_title');
      $table->string('site_tagline')->nullable();
      $table->string('logo')->nullable();
      $table->string('favicon')->nullable();
      $table->string('email')->nullable();
      $table->string('phone_primary')->nullable();
      $table->string('phone_secondary')->nullable();
      $table->string('address')->nullable();
      $table->text('footer_description')->nullable();
      $table->timestamps();
    });
  }

  /**
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('site_settings');
  }
};

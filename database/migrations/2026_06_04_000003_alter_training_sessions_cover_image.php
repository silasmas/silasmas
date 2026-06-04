<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Élargit cover_image pour les chemins JSON Filament.
   */
  public function up(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->text('cover_image')->nullable()->change();
    });
  }

  /**
   * @return void
   */
  public function down(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->string('cover_image')->nullable()->change();
    });
  }
};

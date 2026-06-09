<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Avantages dynamiques affichés sous le formulaire d'inscription.
   */
  public function up(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->json('registration_benefits')->nullable()->after('participant_benefits');
    });
  }

  /**
   * @return void
   */
  public function down(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->dropColumn('registration_benefits');
    });
  }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Ajoute le suivi d'envoi de l'e-mail d'ouverture des inscriptions.
   */
  public function up(): void
  {
    Schema::table('registrations', function (Blueprint $table) {
      $table->timestamp('pre_registration_notified_at')->nullable()->after('confirmation_notified_at');
    });
  }

  /**
   * Supprime la colonne de suivi.
   */
  public function down(): void
  {
    Schema::table('registrations', function (Blueprint $table) {
      $table->dropColumn('pre_registration_notified_at');
    });
  }
};

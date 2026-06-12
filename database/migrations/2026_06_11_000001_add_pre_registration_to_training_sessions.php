<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Ajoute la configuration de page pré-inscription aux sessions Academy.
   */
  public function up(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->boolean('pre_registration_enabled')->default(false)->after('is_featured');
      $table->dateTime('registration_opens_at')->nullable()->after('pre_registration_enabled');
      $table->text('pre_registration_message')->nullable()->after('registration_opens_at');
      $table->string('pre_registration_cover_image')->nullable()->after('pre_registration_message');
    });
  }

  /**
   * Supprime les colonnes de pré-inscription.
   */
  public function down(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->dropColumn([
        'pre_registration_enabled',
        'registration_opens_at',
        'pre_registration_message',
        'pre_registration_cover_image',
      ]);
    });
  }
};

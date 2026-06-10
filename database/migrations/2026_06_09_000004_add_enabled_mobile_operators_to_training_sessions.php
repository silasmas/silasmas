<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Opérateurs Mobile Money visibles sur le formulaire d'inscription par session.
 */
return new class extends Migration
{
  private const DEFAULT_OPERATORS = ['mpesa', 'airtel', 'orange', 'afrimoney'];

  /**
   * Applique la migration.
   */
  public function up(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->json('enabled_mobile_operators')->nullable()->after('payment_card_enabled');
    });

    DB::table('training_sessions')
      ->whereNull('enabled_mobile_operators')
      ->update([
        'enabled_mobile_operators' => json_encode(self::DEFAULT_OPERATORS),
      ]);
  }

  /**
   * Annule la migration.
   */
  public function down(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->dropColumn('enabled_mobile_operators');
    });
  }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ajoute les options d'affichage des moyens de paiement par session.
 */
return new class extends Migration
{
  /**
   * Applique la migration.
   */
  public function up(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->boolean('payment_mobile_money_enabled')->default(true)->after('currency');
      $table->boolean('payment_card_enabled')->default(true)->after('payment_mobile_money_enabled');
    });
  }

  /**
   * Annule la migration.
   */
  public function down(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->dropColumn(['payment_mobile_money_enabled', 'payment_card_enabled']);
    });
  }
};

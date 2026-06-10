<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Stocke la réponse brute FlexPay / API lors d'un échec de paiement.
   */
  public function up(): void
  {
    Schema::table('session_payments', function (Blueprint $table) {
      $table->longText('failure_server_response')->nullable()->after('failure_reason');
    });
  }

  /**
   * @return void
   */
  public function down(): void
  {
    Schema::table('session_payments', function (Blueprint $table) {
      $table->dropColumn('failure_server_response');
    });
  }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Tarification des sessions et champs FlexPay sur les paiements.
   */
  public function up(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->boolean('is_free')->default(true)->after('is_featured');
      $table->decimal('price', 12, 2)->nullable()->after('is_free');
      $table->string('currency', 3)->default('USD')->after('price');
    });

    Schema::table('session_payments', function (Blueprint $table) {
      $table->string('provider_reference')->nullable()->after('reference');
      $table->string('channel')->nullable()->after('payment_method');
      $table->string('mobile_operator')->nullable()->after('channel');
    });
  }

  /**
   * @return void
   */
  public function down(): void
  {
    Schema::table('session_payments', function (Blueprint $table) {
      $table->dropColumn(['provider_reference', 'channel', 'mobile_operator']);
    });

    Schema::table('training_sessions', function (Blueprint $table) {
      $table->dropColumn(['is_free', 'price', 'currency']);
    });
  }
};

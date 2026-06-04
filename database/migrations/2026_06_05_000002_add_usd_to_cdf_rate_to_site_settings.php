<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Taux de change USD → CDF pour affichage double devise.
   */
  public function up(): void
  {
    Schema::table('site_settings', function (Blueprint $table) {
      $table->decimal('usd_to_cdf_rate', 14, 2)->nullable()->after('footer_description');
    });
  }

  /**
   * @return void
   */
  public function down(): void
  {
    Schema::table('site_settings', function (Blueprint $table) {
      $table->dropColumn('usd_to_cdf_rate');
    });
  }
};

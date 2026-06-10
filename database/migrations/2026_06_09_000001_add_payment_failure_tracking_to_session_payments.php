<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Suivi des échecs de paiement Academy (alerte admin + dashboard).
   */
  public function up(): void
  {
    Schema::table('session_payments', function (Blueprint $table) {
      $table->string('failure_context')->nullable()->after('notes');
      $table->text('failure_reason')->nullable()->after('failure_context');
      $table->timestamp('failed_at')->nullable()->after('failure_reason');
      $table->timestamp('admin_notified_at')->nullable()->after('failed_at');
    });
  }

  /**
   * @return void
   */
  public function down(): void
  {
    Schema::table('session_payments', function (Blueprint $table) {
      $table->dropColumn([
        'failure_context',
        'failure_reason',
        'failed_at',
        'admin_notified_at',
      ]);
    });
  }
};

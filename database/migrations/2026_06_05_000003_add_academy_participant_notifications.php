<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Notifications, espace participant et ressources de session.
   */
  public function up(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->boolean('notify_by_email')->default(true)->after('currency');
      $table->boolean('notify_by_sms')->default(false)->after('notify_by_email');
      $table->boolean('notify_by_whatsapp')->default(false)->after('notify_by_sms');
      $table->text('confidentiality_notice')->nullable()->after('notify_by_whatsapp');
      $table->text('participant_benefits')->nullable()->after('confidentiality_notice');
      $table->json('session_resources')->nullable()->after('participant_benefits');
    });

    Schema::table('registrations', function (Blueprint $table) {
      $table->string('access_token', 64)->nullable()->unique()->after('registered_at');
      $table->boolean('notify_email')->default(false)->after('access_token');
      $table->boolean('notify_sms')->default(false)->after('notify_email');
      $table->boolean('notify_whatsapp')->default(false)->after('notify_sms');
      $table->timestamp('confidentiality_accepted_at')->nullable()->after('notify_whatsapp');
      $table->timestamp('confirmation_notified_at')->nullable()->after('confidentiality_accepted_at');
      $table->timestamp('last_reminder_at')->nullable()->after('confirmation_notified_at');
    });
  }

  /**
   * @return void
   */
  public function down(): void
  {
    Schema::table('registrations', function (Blueprint $table) {
      $table->dropColumn([
        'access_token',
        'notify_email',
        'notify_sms',
        'notify_whatsapp',
        'confidentiality_accepted_at',
        'confirmation_notified_at',
        'last_reminder_at',
      ]);
    });

    Schema::table('training_sessions', function (Blueprint $table) {
      $table->dropColumn([
        'notify_by_email',
        'notify_by_sms',
        'notify_by_whatsapp',
        'confidentiality_notice',
        'participant_benefits',
        'session_resources',
      ]);
    });
  }
};

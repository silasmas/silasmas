<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Ajoute vidéo spot et champs portfolio / paiements.
   */
  public function up(): void
  {
    Schema::table('training_sessions', function (Blueprint $table) {
      $table->string('spot_video_type')->default('none')->after('cover_image');
      $table->string('spot_video')->nullable()->after('spot_video_type');
      $table->string('spot_video_external_url')->nullable()->after('spot_video');
    });

    Schema::table('projects', function (Blueprint $table) {
      $table->string('client_name')->nullable()->after('project_description');
      $table->string('category')->nullable()->after('client_name');
      $table->string('project_date')->nullable()->after('category');
      $table->string('slug')->nullable()->after('project_name');
      $table->unsignedInteger('sort_order')->default(0)->after('user_id');
      $table->boolean('is_published')->default(true)->after('sort_order');
      $table->json('gallery_urls')->nullable()->after('logo_url');
    });

    Schema::create('session_payments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
      $table->foreignId('registration_id')->nullable()->constrained()->nullOnDelete();
      $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
      $table->decimal('amount', 12, 2);
      $table->string('currency', 3)->default('USD');
      $table->string('status')->default('pending');
      $table->string('payment_method')->nullable();
      $table->string('reference')->nullable()->unique();
      $table->timestamp('paid_at')->nullable();
      $table->text('notes')->nullable();
      $table->timestamps();
    });
  }

  /**
   * @return void
   */
  public function down(): void
  {
    Schema::dropIfExists('session_payments');

    Schema::table('projects', function (Blueprint $table) {
      $table->dropColumn([
        'client_name',
        'category',
        'project_date',
        'slug',
        'sort_order',
        'is_published',
        'gallery_urls',
      ]);
    });

    Schema::table('training_sessions', function (Blueprint $table) {
      $table->dropColumn(['spot_video_type', 'spot_video', 'spot_video_external_url']);
    });
  }
};

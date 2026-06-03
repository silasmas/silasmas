<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Crée les tables SDev Academy (étudiants, sessions, inscriptions).
   */
  public function up(): void
  {
    Schema::create('students', function (Blueprint $table) {
      $table->id();
      $table->string('firstname');
      $table->string('lastname');
      $table->string('email')->unique();
      $table->string('phone', 30)->nullable();
      $table->string('city')->nullable();
      $table->string('country')->default('RDC');
      $table->string('education_level')->nullable();
      $table->string('occupation')->nullable();
      $table->text('notes')->nullable();
      $table->boolean('marketing_opt_in')->default(true);
      $table->timestamps();
    });

    Schema::create('training_sessions', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('slug')->unique();
      $table->string('subtitle')->nullable();
      $table->text('description')->nullable();
      $table->longText('program')->nullable();
      $table->date('start_date');
      $table->date('end_date');
      $table->string('format')->default('online');
      $table->string('status')->default('draft');
      $table->unsignedInteger('max_participants')->nullable();
      $table->boolean('is_featured')->default(false);
      $table->string('cover_image')->nullable();
      $table->timestamps();
    });

    Schema::create('registrations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('student_id')->constrained()->cascadeOnDelete();
      $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
      $table->string('status')->default('pending');
      $table->string('source')->default('website');
      $table->text('motivation')->nullable();
      $table->timestamp('registered_at')->useCurrent();
      $table->timestamps();
      $table->unique(['student_id', 'training_session_id']);
    });
  }

  /**
   * Supprime les tables SDev Academy.
   */
  public function down(): void
  {
    Schema::dropIfExists('registrations');
    Schema::dropIfExists('training_sessions');
    Schema::dropIfExists('students');
  }
};

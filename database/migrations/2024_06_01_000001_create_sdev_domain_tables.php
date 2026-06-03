<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Crée statuts, rôles et lie users aux statuts.
   */
  public function up(): void
  {
    Schema::create('statuses', function (Blueprint $table) {
      $table->id();
      $table->string('status_name')->unique();
      $table->text('status_description')->nullable();
      $table->string('color', 20)->nullable();
      $table->timestamps();
    });

    Schema::create('roles', function (Blueprint $table) {
      $table->id();
      $table->string('role_name')->unique();
      $table->text('role_description')->nullable();
      $table->timestamps();
    });

    Schema::table('users', function (Blueprint $table) {
      $table->foreignId('status_id')->nullable()->after('password')->constrained('statuses')->nullOnDelete();
    });

    Schema::create('role_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->foreignId('role_id')->constrained()->cascadeOnDelete();
      $table->timestamps();
      $table->unique(['user_id', 'role_id']);
    });

    Schema::create('projects', function (Blueprint $table) {
      $table->id();
      $table->string('project_name');
      $table->text('project_description')->nullable();
      $table->string('web_url')->nullable();
      $table->string('android_url')->nullable();
      $table->string('ios_url')->nullable();
      $table->string('logo_url')->nullable();
      $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
      $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();
    });

    Schema::create('messages', function (Blueprint $table) {
      $table->id();
      $table->string('message_subject');
      $table->text('message_content');
      $table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();
      $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();
    });

    Schema::create('websites', function (Blueprint $table) {
      $table->id();
      $table->string('website_name')->nullable();
      $table->string('website_url')->unique();
      $table->string('logo_url')->nullable();
      $table->string('icon')->nullable();
      $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();
    });
  }

  /**
   * Supprime les tables métier SDEV.
   */
  public function down(): void
  {
    Schema::dropIfExists('websites');
    Schema::dropIfExists('messages');
    Schema::dropIfExists('projects');
    Schema::dropIfExists('role_user');
    Schema::table('users', function (Blueprint $table) {
      $table->dropConstrainedForeignId('status_id');
    });
    Schema::dropIfExists('roles');
    Schema::dropIfExists('statuses');
  }
};

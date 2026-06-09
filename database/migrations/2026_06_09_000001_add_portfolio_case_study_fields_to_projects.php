<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Ajoute les champs étude de cas au portfolio.
   */
  public function up(): void
  {
    Schema::table('projects', function (Blueprint $table) {
      $table->text('context')->nullable()->after('project_description');
      $table->text('challenge')->nullable()->after('context');
      $table->text('outcome')->nullable()->after('challenge');
      $table->json('tags')->nullable()->after('outcome');
      $table->json('metrics')->nullable()->after('tags');
    });
  }

  /**
   * Supprime les champs étude de cas.
   */
  public function down(): void
  {
    Schema::table('projects', function (Blueprint $table) {
      $table->dropColumn(['context', 'challenge', 'outcome', 'tags', 'metrics']);
    });
  }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('teams', function (Blueprint $table): void {
      $table->unsignedBigInteger('id')->primary();
      $table->foreignId('user_id')->index();
      $table->string('name');
      $table->boolean('personal_team');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('teams');
  }
};

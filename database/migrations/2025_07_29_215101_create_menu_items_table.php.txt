<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('menu_items', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('url')->default('#');
      $table->string('icon')->nullable();
      $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
      $table->integer('order')->default(0);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('menu_items');
  }
};

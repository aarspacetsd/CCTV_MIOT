<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('menu_item_role', function (Blueprint $table) {
      $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
      $table->foreignId('role_id')->constrained()->onDelete('cascade');
      $table->primary(['menu_item_id', 'role_id']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('menu_item_role');
  }
};

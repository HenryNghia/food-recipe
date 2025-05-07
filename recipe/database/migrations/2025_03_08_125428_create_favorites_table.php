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
        Schema::create('favorites', function (Blueprint $table) {
            $table->Integer('user_id');
            $table->Integer('recipe_id');
            $table->dateTime('saved_date');
            $table->timestamps();
            $table->primary(['user_id', 'recipe_id']); // Tạo khóa chính từ user_id và recipe_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};

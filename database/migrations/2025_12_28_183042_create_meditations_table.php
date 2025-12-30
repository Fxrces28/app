<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('meditations', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description');
        $table->string('audio_path');
        $table->integer('duration');
        $table->string('image_path')->nullable();
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->boolean('is_premium')->default(false);
        $table->integer('play_count')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meditations');
    }
};

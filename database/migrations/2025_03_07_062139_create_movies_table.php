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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->year('release_year'); // Changed from integer to year
            $table->timestamps();
        });

        Schema::create('movie_genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Changed to 'name' for clarity
            $table->timestamps();
        });

        Schema::create('movie_movie_genre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained('movies')->onDelete('cascade');
            $table->foreignId('movie_genre_id')->constrained('movie_genres')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_movie_genre');
        Schema::dropIfExists('movies');
        Schema::dropIfExists('movie_genres');
    }
};

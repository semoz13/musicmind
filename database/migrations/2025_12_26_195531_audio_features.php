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
        Schema::create('audio_features', function (Blueprint $table) {
            $table->id();

            $table->string('spotify_id', 50)->unique()->index();
        
            $table->float('danceability')->nullable();
            $table->float('energy')->nullable();
            $table->float('valence')->nullable();
            $table->float('acousticness')->nullable();
            $table->float('instrumentalness')->nullable();
            $table->float('speechiness')->nullable();
            $table->integer('tempo')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

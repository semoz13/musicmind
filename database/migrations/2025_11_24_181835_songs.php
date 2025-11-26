<?php

use App\Models\Artist;
use App\Models\Genre;
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
        Schema::create('songs', function (Blueprint $table){
            $table->id();
            $table->string('spotify_id')->unique();
            $table->string('title');
            $table->foreignIdFor(Artist::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Genre::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('overview')->nullable();
            $table->string('mood')->nullable();
            $table->date('release_date')->nullable();
            $table->string('preview_url')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};

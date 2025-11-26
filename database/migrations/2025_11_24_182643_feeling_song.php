<?php

use App\Models\Feeling;
use App\Models\Song;
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
        Schema::create('feeling_song', function (Blueprint $table){
            $table->foreignIdFor(Feeling::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Song::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->primary(['feeling_id','song_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeling_song');
    }
};

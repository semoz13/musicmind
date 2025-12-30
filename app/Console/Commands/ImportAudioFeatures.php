<?php

namespace App\Console\Commands;

use App\Models\AudioFeature;
use Illuminate\Console\Command;
use League\Csv\Reader;


class ImportAudioFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataset:import-audio-features {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Spotify audio features  dataset';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->argument('path');

        if (! file_exists($path)) {
            $this->error('Dataset file not found: ' . $path);
            return self::FAILURE;
        }

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0); 
        $imported = 0;
        foreach ($csv as $row){
            if (empty($row['track_id'])){
                continue;
            }
            AudioFeature::updateOrCreate(
                ['spotify_id' => $row['track_id']],
                [
                    'danceability'     => (float) $row['danceability'],
                    'energy'           => (float) $row['energy'],
                    'valence'          => (float) $row['valence'],
                    'tempo'            => (int) round((float) $row['tempo']),
                    'acousticness'     => (float) $row['acousticness'],
                    'instrumentalness' => (float) $row['instrumentalness'],
                    'speechiness'      => (float) $row['speechiness'],
                ]
            );
            $imported++;
        }
        $this->info("Imported {$imported} audio feature rows..");

        return self::SUCCESS;
    }
}

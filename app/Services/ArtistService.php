<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

use function Laravel\Prompts\search;

class ArtistService extends SpotifyService
{

    protected string $baseUrl = 'https://api.spotify.com/v1';

    protected function client()
    {
        return Http::withOptions([
            'verify' =>false,
        ])
        ->withToken($this->getAccessToken());
    }


    public function getArtistsByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $response = $this->client()->get(
            $this->baseUrl . '/artists',
            [
            'ids' => implode(',' , $ids),
            ]);
        if (!$response->successful()){
            return [];
        }
        return $response->json('artists') ?? [];
            
    }

    public function searchArtists(string $query, int $limit = 10): array
    {
        $response = $this->client()->get(
            $this->baseUrl . '/search',
            [
            'q' => $query,
            'type' => 'artist',
            'limit' => $limit,
            ]);
        if(!$response->successful()){
            return [];
        }

        return $response->json('artists.items') ?? [];
    }

    public function getArtistTopTracks(string $artistId)
    {
        $response = $this->client()->get(
            $this->baseUrl . "/artists/{$artistId}/top-tracks");

            if (!$response->successful()){
                return [];
            }
            return $response->json('tracks') ?? [];

    }

    public function getArtistAlbums(string $artistId, int $limit=20, int $offset=0): array
    {
        $response = $this->client()->get(
            $this->baseUrl . "/artists/{$artistId}/albums",
            [
                'include_groups' => 'album,single',
                'limit' => $limit,
                'offset' => $offset,
            ]
            );

            if(!$response->successful()){
                return [];
            }
            return $response->json('items') ?? [];
    }
}    
<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class ArtistService extends SpotifyService
{

    protected $client;

    
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.spotify.com/v1/',
        ]);

        $this->accessToken = $this->getAccessToken();
    }


    public function getArtist($artistId)
        {
            try {
                $response = $this->client->get("artists/{$artistId}", [
                    'headers' => [
                        'Authorization' => 'Bearer '.$this->accessToken,
                        'Accept' => 'application/json',
                    ],
                    // DEV: skip certificate verification (use only for local dev)
                    'verify' => false,
                ]);

                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                return $data;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $resp = $e->getResponse();
                $body = $resp ? (string) $resp->getBody() : $e->getMessage();
                throw new \RuntimeException('Spotify API client error: '.$body, $resp ? $resp->getStatusCode() : 400);
            } catch (\Exception $e) {
                throw new \RuntimeException('Spotify API error: '.$e->getMessage(), $e->getCode() ?: 500);
            }
        }
}    
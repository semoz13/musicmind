<?php

/*
The Idea here is to create a SpotifyService that will handle all interactions with the Spotify API.
This service will be responsible for fetching data such as songs, albums, and artists from Spotify.
The steps here are as follows:

Auth Section:
-----------------------------------------------------------------------------------
1. handle authentication with Spotify API:

    The idea here is to request an access token using the Client Credentials Flow.

    You will need to send a POST request to Spotify's token endpoint using your Client ID and Client Secret.

    To request the access token, you must get your Client ID and Client Secret.

    The client credentials are found in your Spotify Developer Dashboard under the app you created.
    ----IMPORTANT NOTE---- Save these credentials in your .env file as SPOTIFY_CLIENT_ID and SPOTIFY_CLIENT_SECRET
    and then get them in code using env('SPOTIFY_CLIENT_ID') and env('SPOTIFY_CLIENT_SECRET').

    Important Clarification:
    The access token you get from Client Credentials Flow is always valid for 1 hour.
    The logic in our project is to store one token in memory and automatically request a new one whenever it expires.
    We are not storing user-specific tokens — only one token for all requests.

    I recommend reading the Spotify API documentation in this URL:
    https://developer.spotify.com/documentation/web-api/tutorials/getting-started#create-an-app

    Example of requesting an access token using curl:

    curl -X POST "https://accounts.spotify.com/api/token" \
      -H "Content-Type: application/x-www-form-urlencoded" \
      -d "grant_type=client_credentials&client_id=your-client-id&client_secret=your-client-secret"
    The response will return an access token valid for 1 hour:
    {
      "access_token": "BQDBKJ5eo5jxbtpWjVOj7ryS84khybFpP_lTqzV7uV-T_m0cTfwvdn5BnBSKPxKgEb11",
      "token_type": "Bearer",
      "expires_in": 3600
    }


-------------------------------------------------------------------------------------
2. Create methods to fetch data from Spotify API:

  For this example, we will use the Get Artist endpoint to request information about an artist.
  According to the API Reference, the endpoint needs the Spotify ID of the artist.

  An easy way to get the Spotify ID of an artist is using the Spotify Desktop App:

    Search the artist
    Click on the three dots icon from the artist profile
    Select Share > Copy link to artist. The Spotify ID is the value that comes right after the open.spotify.com/artist URI.
    Our API call must include the access token we have just generated using the Authorization header as follows:

    curl "https://api.spotify.com/v1/artists/4Z8W4fKeB5YxbusRsdQVPb" \
      -H "Authorization: Bearer  BQDBKJ5eo5jxbtpWjVOj7ryS84khybFpP_lTqzV7uV-T_m0cTfwvdn5BnBSKPxKgEb11"

  If everything goes well, the API will return the following JSON response:
    {
      "external_urls": {
        "spotify": "https://open.spotify.com/artist/4Z8W4fKeB5YxbusRsdQVPb"
      },
      "followers": {
        "href": null,
        "total": 7625607
      },
      "genres": [
        "alternative rock",
        "art rock",
        "melancholia",
        "oxford indie",
        "permanent wave",
        "rock"
      ],
      "href": "https://api.spotify.com/v1/artists/4Z8W4fKeB5YxbusRsdQVPb",
      "id": "4Z8W4fKeB5YxbusRsdQVPb",
      "images": [
        {
          "height": 640,
          "url": "https://i.scdn.co/image/ab6761610000e5eba03696716c9ee605006047fd",
          "width": 640
        },
        {
          "height": 320,
          "url": "https://i.scdn.co/image/ab67616100005174a03696716c9ee605006047fd",
          "width": 320
        },
        {
          "height": 160,
          "url": "https://i.scdn.co/image/ab6761610000f178a03696716c9ee605006047fd",
          "width": 160
        }
      ],
      "name": "Radiohead",
      "popularity": 79,
      "type": "artist",
      "uri": "spotify:artist:4Z8W4fKeB5YxbusRsdQVPb"
    }
*/

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SpotifyService
{
    protected $client;

    protected $accessToken;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.spotify.com/v1/',
        ]);

        $this->accessToken = $this->getAccessToken();
    }

    // Get Access Token using Client Credentials Flow
    public function getAccessToken()
    {
        // Try cache first — token is shared app-wide (Client Credentials flow)
        $cached = Cache::get('spotify_access_token');
        if (! empty($cached)) {
            return $cached;
        }

        $clientId = env('SPOTIFY_CLIENT_ID');
        $clientSecret = env('SPOTIFY_CLIENT_SECRET');

        if (empty($clientId) || empty($clientSecret)) {
            throw new \RuntimeException('Missing SPOTIFY_CLIENT_ID or SPOTIFY_CLIENT_SECRET in .env');
        }

        $response = (new Client)->post('https://accounts.spotify.com/api/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ],
            // DEV: skip certificate verification to workaround local CA issues
            'verify' => false,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if (empty($data['access_token'])) {
            throw new \RuntimeException('Unable to retrieve Spotify access token');
        }

        $token = $data['access_token'];
        $expiresIn = isset($data['expires_in']) ? (int) $data['expires_in'] : 3600;

        // Store in cache slightly shorter than actual expiry to avoid using an expired token
        $ttl = max(30, $expiresIn - 60);
        Cache::put('spotify_access_token', $token, $ttl);

        return $token;
    }

    
    
    public function getSongsByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        $reponse = Http::withOptions([
            'verify' => false,
        ])
        ->withToken($this->getAccessToken())
            ->get('https://api.spotify.com/v1/tracks',[
                'ids' =>implode(',', $ids)
            ]);
            return $reponse->json('songs') ?? [];
    }

    // Your additional methods to interact with Spotify API can be added here
    // such as searchArtists, getArtistTopTracks, etc.

    // and for the songs list and the song data and song search functionality
    // you can create methods like getSongs, getSongById, searchSongs, etc.

    public function testConnection()
    {
        try {
            $testArtistId = '4Z8W4fKeB5YxbusRsdQVPb';
        
            $response = $this->client->get("artists/{$testArtistId}", [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
            ]);
        
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
        
            return [
                'success' => true,
                'artist_name' => $data['name'] ?? 'Unknown',
                'message' => 'Connection successful!'
            ];
        
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

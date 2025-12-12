<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'spotify_id' => $this->spotify_id,
            'title' => $this->title,
            'overview' => $this->overview,
            'mood' => $this->mood,
            'release_date' => $this->release_date,
            'preview_url' => $this->preview_url,
            // 'artist' => new ArtistResource($this->whenLoaded('artist')),
            // 'genre' => new GenreResource($this->whenLoaded('genre')),

        ];
    }
}

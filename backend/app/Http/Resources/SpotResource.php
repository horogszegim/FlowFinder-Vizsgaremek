<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_by' => new UserResource($this->whenLoaded('user')),
            'sports_and_tags' => SportsAndTagResource::collection($this->whenLoaded('sportsAndTags')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'spot_id' => $this->spot_id,
            'path' => $this->path,
            'url' => $this->path ? Storage::disk('public')->url($this->path) : null,
            'spot' => new SpotResource($this->whenLoaded('spot')),
        ];
    }
}

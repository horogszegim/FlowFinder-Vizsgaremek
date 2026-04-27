<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isExternalUrl = $this->path && Str::startsWith($this->path, ['http://', 'https://']);

        return [
            'id' => $this->id,
            'spot_id' => $this->spot_id,
            'path' => $this->path,
            'url' => $this->path ? ($isExternalUrl ? $this->path : Storage::disk('public')->url($this->path)) : null,
            'sort_order' => $this->sort_order,
            'spot' => new SpotResource($this->whenLoaded('spot')),
        ];
    }
}

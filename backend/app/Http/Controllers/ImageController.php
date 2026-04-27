<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
    {
        return ImageResource::collection(
            Image::with(['spot.user'])->orderBy('spot_id')->orderBy('sort_order')->orderBy('id')->get()
        );
    }

    public function store(StoreImageRequest $request)
    {
        $spot = Spot::withCount('images')->findOrFail($request->spot_id);

        if (! $this->canManageSpot($spot, $request->user())) {
            abort(403);
        }

        $path = $request->file('image')->store("spots/{$request->spot_id}", 'public');
        $sortOrder = ((int) $spot->images()->max('sort_order')) + 1;

        $image = Image::create([
            'spot_id' => $request->spot_id,
            'path' => $path,
            'sort_order' => $sortOrder,
        ]);

        return new ImageResource($image->load(['spot.user']));
    }

    public function show(Image $image)
    {
        return new ImageResource($image->load(['spot.user']));
    }

    public function destroy(Request $request, Image $image)
    {
        $image->load('spot');

        if (! $this->canManageSpot($image->spot, $request->user())) {
            abort(403);
        }

        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }

        if ($image->delete()) {
            return response()->noContent();
        }

        return abort(500);
    }

    private function canManageSpot(?Spot $spot, ?User $user): bool
    {
        return $spot && $user && ($user->role === 'admin' || (int) $spot->created_by === (int) $user->id);
    }
}

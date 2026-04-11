<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
    {
        return ImageResource::collection(
            Image::with(['spot.user'])->orderBy('id')->get()
        );
    }

    public function store(StoreImageRequest $request)
    {
        $path = $request->file('image')->store("spots/{$request->spot_id}", 'public');

        $image = Image::create([
            'spot_id' => $request->spot_id,
            'path' => $path,
        ]);

        return new ImageResource($image->load(['spot.user']));
    }

    public function show(Image $image)
    {
        return new ImageResource($image->load(['spot.user']));
    }

    public function destroy(Image $image)
    {
        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }

        if ($image->delete()) {
            return response()->noContent();
        }

        return abort(500);
    }
}

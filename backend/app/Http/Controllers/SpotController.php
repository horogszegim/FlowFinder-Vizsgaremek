<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpotRequest;
use App\Http\Requests\UpdateSpotImageOrderRequest;
use App\Http\Resources\SpotResource;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SpotController extends Controller
{
    public function index()
    {
        return SpotResource::collection(
            Spot::with(['user', 'images', 'sportsAndTags'])->orderBy('id')->get()
        );
    }

    public function store(StoreSpotRequest $request)
    {
        $data = $request->validated();

        $spot = Spot::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'created_by' => Auth::id(),
        ]);

        $spot->sportsAndTags()->sync($data['sports_and_tags'] ?? []);

        return new SpotResource($spot->load(['user', 'images', 'sportsAndTags']));
    }

    public function show(Spot $spot)
    {
        return new SpotResource($spot->load(['user', 'images', 'sportsAndTags']));
    }

    public function update(StoreSpotRequest $request, Spot $spot)
    {
        if (! $this->canManageSpot($spot, $request->user())) {
            abort(403);
        }

        $data = $request->validated();

        $spot->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ]);

        $spot->sportsAndTags()->sync($data['sports_and_tags'] ?? []);

        return new SpotResource($spot->load(['user', 'images', 'sportsAndTags']));
    }

    public function updateImageOrder(UpdateSpotImageOrderRequest $request, Spot $spot)
    {
        if (! $this->canManageSpot($spot, $request->user())) {
            abort(403);
        }

        $data = $request->validated();
        $requestedImageIds = array_map('intval', $data['image_order']);

        foreach ($requestedImageIds as $index => $imageId) {
            $spot->images()->where('id', $imageId)->update([
                'sort_order' => $index + 1,
            ]);
        }

        return new SpotResource($spot->fresh()->load(['user', 'images', 'sportsAndTags']));
    }

    public function destroy(Spot $spot)
    {
        if (! $this->canManageSpot($spot, request()->user())) {
            abort(403);
        }

        $spot->load('images');

        foreach ($spot->images as $image) {
            if ($image->path) {
                Storage::disk('public')->delete($image->path);
            }
        }

        Storage::disk('public')->deleteDirectory("spots/{$spot->id}");

        if ($spot->delete()) {
            return response()->noContent();
        }

        return abort(500);
    }

    private function canManageSpot(Spot $spot, ?User $user): bool
    {
        return $user && ($user->role === 'admin' || (int) $spot->created_by === (int) $user->id);
    }
}

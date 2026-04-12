<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpotRequest;
use App\Http\Resources\SpotResource;
use App\Models\Spot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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

        $data['created_by'] = Auth::id();

        $spot = Spot::create($data);

        if (isset($data['sports_and_tags'])) {
            $spot->sportsAndTags()->sync($data['sports_and_tags']);
        }

        return new SpotResource($spot->load(['user', 'images', 'sportsAndTags']));
    }

    public function show(Spot $spot)
    {
        return new SpotResource($spot->load(['user', 'images', 'sportsAndTags']));
    }

    public function destroy(Spot $spot)
    {
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
}

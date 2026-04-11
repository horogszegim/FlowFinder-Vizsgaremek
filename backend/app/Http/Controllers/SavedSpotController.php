<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSavedSpotRequest;
use App\Http\Resources\SavedSpotResource;
use App\Models\SavedSpot;
use Illuminate\Http\Request;

class SavedSpotController extends Controller
{
    public function index(Request $request)
    {
        return SavedSpotResource::collection(
            SavedSpot::with(['user', 'spot'])
                ->where('user_id', $request->user()->id)
                ->orderBy('id')
                ->get()
        );
    }

    public function store(StoreSavedSpotRequest $request)
    {
        $user = $request->user();

        $savedSpot = SavedSpot::firstOrCreate([
            'user_id' => $user->id,
            'spot_id' => $request->spot_id,
        ]);

        return new SavedSpotResource(
            $savedSpot->load(['user', 'spot'])
        );
    }

    public function show(SavedSpot $savedSpot)
    {
        return new SavedSpotResource($savedSpot->load(['user', 'spot']));
    }

    public function destroy(Request $request, SavedSpot $savedSpot)
    {
        if ($savedSpot->user_id !== $request->user()->id) {
            abort(403);
        }

        $savedSpot->delete();

        return response()->noContent();
    }
}

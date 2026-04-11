<?php

namespace App\Http\Controllers;

use App\Models\SportsAndTag;
use App\Http\Resources\SportsAndTagResource;

class SportsAndTagController extends Controller
{
    public function index()
    {
        return SportsAndTagResource::collection(SportsAndTag::query()->orderBy('id')->get());
    }

    public function show($id)
    {
        return new SportsAndTagResource(SportsAndTag::findOrFail($id));
    }
}

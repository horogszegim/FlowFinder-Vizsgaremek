<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::with('spots')->get());
    }

    public function show(User $user)
    {
        return new UserResource($user->load('spots'));
    }
}

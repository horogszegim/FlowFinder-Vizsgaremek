<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (Auth::attempt($data)) {
            $user = $request->user();
            $token = $user->createToken('app');

            return response()->json([
                'data' => [
                    'token' => $token->plainTextToken,
                    'user' => new UserResource($user),
                ],
            ]);
        }

        return response()->json([
            'data' => [
                'message' => 'Sikertelen belépés',
            ],
        ], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'data' => [
                'message' => 'Sikeres kijelentkezés',
            ],
        ]);
    }
}

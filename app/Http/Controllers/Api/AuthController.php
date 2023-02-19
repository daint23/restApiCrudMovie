<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $registerRequest)
    {
        try {
            $validData = $registerRequest->validated();

            $user = User::create([
                'name' => $validData['name'],
                'email' => $validData['email'],
                'password' => Hash::make($validData['password'])
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'data' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    public function login(Request $request)
    {
        try {
            if(! Auth::attempt($request->only('email', 'password')))
            {
                return response()->json([
                    'message' => 'Unauthorizes'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user = User::where('email', $request->email)->firstOrFail();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login success',
                'token' => $token,
                'token_type' => 'Bearer'
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_CONFLICT);
        }
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json([
                'message' => 'logout success'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], Response::HTTP_CONFLICT);
        }
    }
}

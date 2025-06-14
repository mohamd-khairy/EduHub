<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        // Attempt login
        if (!Auth::attempt($credentials)) {
            return $this->fail('Invalid credentials.', 401);
        }

        // Create token
        $user = Auth::user();
        $token = $user->createToken('login-token')->plainTextToken;

        return $this->success([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user
        ]);
    }
}

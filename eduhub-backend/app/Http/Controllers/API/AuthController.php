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
        $roles = $user->roles()->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');

        unset($user->roles); // remove if loaded
        unset($user->permissions); // remove if loaded

        return $this->success([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
            'roles' =>   $roles,
            'permissions' => $permissions,
        ]);
    }

    public function Me(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // If no user is authenticated, return 401 Unauthorized
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user = Auth::user();
        $roles = $user->roles()->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');

        unset($user->roles); // remove if loaded
        unset($user->permissions); // remove if loaded

        return $this->success([
            'message' => 'Login successful.',
            'user' => $user,
            'roles' =>   $roles,
            'permissions' => $permissions,
        ]);
    }

    public function logout(Request $request)
    {
        // Logout the user
        Auth::logout();

        // Invalidate the session to ensure no session fixation attacks
        $request->session()->invalidate();

        // Regenerate the session token to avoid session fixation
        $request->session()->regenerateToken();

        // Return a response indicating successful logout
        return $this->success(true);
    }
}

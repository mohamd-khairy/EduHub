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
}

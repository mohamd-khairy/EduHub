<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Teacher;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'type'     => 'required|in:user,student,parent,teacher',
        ]);

        $map = [
            'user'    => [User::class, 'user'],
            'student' => [Student::class, 'student'],
            'parent'  => [ParentModel::class, 'parent'],
            'teacher' => [Teacher::class, 'teacher'],
        ];

        [$modelClass, $guard] = $map[$request->type];

        $user = $modelClass::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->fail('Invalid credentials.', 401);
        }

        // Issue Sanctum token
        $token = $user->createToken("{$guard}-token")->plainTextToken;

        // Get roles & permissions via Spatie
        $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : [];
        $permissions = method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name') : [];

        $user->makeHidden(['roles', 'permissions', 'pivot', 'role']);

        return $this->success([
            'message'     => 'Login successful.',
            'token'       => $token,
            'user'        => $user,
            'guard_type'  => $guard,
            'roles'       => $roles,
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

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'type' => 'required|in:user,student,parent,teacher',
        ]);

        $guard = $request->type == 'user' ? 'web' : $request->type;

        $user = Auth::guard($guard)->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->fail('The provided password was incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->success(true);
    }
}

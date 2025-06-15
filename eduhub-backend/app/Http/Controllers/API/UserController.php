<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function store(
        Request $request
    ) {
        $user = User::create($request->all());
        $user->roles()->sync($request->roles);

        return $this->success($user);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        $user->roles()->sync($request->roles);

        return $this->success($user);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //

    public function updatePermission(Request $request, $id)
    {
        $role = Role::find($id);
        $role->permissions()->sync($request->permissions);
        return $this->success($request->permissions);
    }
}

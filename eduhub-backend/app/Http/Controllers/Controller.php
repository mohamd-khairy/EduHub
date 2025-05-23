<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function success($data = null)
    {
        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $data
        ]);
    }

    public function fail()
    {
        return response()->json([
            'status' => false,
            'code' => 400,
            'data' => []
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            if ($request->relations)
                $relations = explode(',', $request->relations);

            $data = $model->with($relations ?? [])->paginate();

            return  $this->success($data);
        } catch (\Throwable $th) {
            //throw $th;
            return  $this->fail([]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deleteAll(Request $request)
    {
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $data = $model->whereIn('id', $request->ids)->delete();

            return  $this->success($data);
        } catch (\Throwable $th) {
            throw $th;
            return  $this->fail([]);
        }
    }
}

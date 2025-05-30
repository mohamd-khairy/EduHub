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
     * Display a listing of the resource.
     */
    public function All(Request $request)
    {
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            if ($request->relations)
                $relations = explode(',', $request->relations);

            if ($request->search && !empty($request->search))
                $model = $model->where('name', 'like', '%' . $request->search . '%');

            $data = $model->with($relations ?? [])->take(50)->get();

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
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $data = $model->create($request->all());

            return $this->success($data);
        } catch (\Throwable $th) {
            //throw $th;
            return  $this->fail([]);
        }
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
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $model = $model->where('id', $id)->first();

            $model->update($request->all());

            return $this->success($model);
        } catch (\Throwable $th) {
            //throw $th;
            return  $this->fail([]);
        }
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{

    // Helper function to manually parse the filter string if not in JSON format
    private function parseFilterString($filters)
    {
        $filtersArray = [];

        // Check if the string is in the format filter=[[exam_id,1],...]
        // Use regex to properly match the key-value pairs inside square brackets
        preg_match_all('/\[(\w+),\s*(\d+)\]/', $filters, $matches);

        // Iterate through the matches and prepare the filters array
        foreach ($matches[1] as $index => $key) {
            $filtersArray[] = [$key, $matches[2][$index]];
        }

        return $filtersArray;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            //relations
            if ($request->relations)
                $relations = explode(',', $request->relations);

            //filters
            if ($request->filters)
                foreach ($this->parseFilterString($request->filters) as $filter) {
                    if (count($filter) == 2) {
                        $column = $filter[0];
                        $value = $filter[1];
                        $model = $model->where($column, $value);
                    }
                }

            $data = $model->with($relations ?? [])->orderBy('id', 'desc')->paginate();

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

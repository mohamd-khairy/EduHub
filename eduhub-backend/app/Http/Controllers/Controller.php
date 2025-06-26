<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
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

            if ($request->search && !empty($request->search))
                foreach ($this->parseFilterString($request->search) as $filter) {
                    if (count($filter) == 2) {
                        $column = $filter[0];
                        $value = $filter[1];
                        $model = $model->where($column, 'like', '%' . $value . '%');
                    }
                }

            $data = $model->with($relations ?? [])->orderBy('id', 'desc')->paginate(request('per_page', 15));

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
            $limit = request('limit', 50);
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            if ($request->relations)
                $relations = explode(',', $request->relations);

            if ($request->search && !empty($request->search))
                $model = $model->where('name', 'like', '%' . $request->search . '%');

            $data = $model->with($relations ?? []);

            if ($limit != 'all') {
                $data = $data->take(request('limit', 50));
            }
            $data = $data->orderBy('id', 'desc')->get();

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
            $inpput = $request->all();

            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            if ($request->image) {
                $image = $request->image->store('images', 'public');
                $inpput['image'] = url('/storage/' . $image);
            }

            $data = $model->create($inpput);

            return $this->success($data);
        } catch (\Throwable $th) {
            //throw $th;
            return  $this->fail([]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            //relations
            if ($request->relations)
                $relations = explode(',', $request->relations);

            $data = $model->with($relations ?? [])->orderBy('id', 'desc')
                ->where('id', $id)
                ->first();

            return  $this->success($data);
        } catch (\Throwable $th) {
            //throw $th;
            return  $this->fail([]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $input = $request->all();
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $model = $model->where('id', $id)->first();

            if ($request->image) {
                $image = $request->image->store('images', 'public');
                $input['image'] = url('/storage/' . $image);
            }

            $model->update($input);

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
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $data = $model->where('id', $id)->delete();

            return  $this->success($data);
        } catch (\Throwable $th) {
            // throw $th;
            return  $this->fail([]);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $data = $model->whereIn('id', $request->ids)->delete();

            return  $this->success($data);
        } catch (\Throwable $th) {
            // throw $th;
            return  $this->fail([]);
        }
    }

    // Helper function to manually parse the filter string if not in JSON format
    private function parseFilterString($filters)
    {
        $filtersArray = [];

        // Match [key,value] pairs, allowing string values
        preg_match_all('/\[(\w+),\s*([^\]]+)\]/', $filters, $matches);

        foreach ($matches[1] as $index => $key) {
            // Trim whitespace from value
            $value = trim($matches[2][$index]);

            // Optional: remove quotes if present
            $value = trim($value, "\"'");

            $filtersArray[] = [$key, $value];
        }

        return $filtersArray;
    }

    public function success($data = null, $msg = null)
    {
        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $msg ?? 'sucess',
            'data' => $data
        ], 200);
    }

    public function fail($msg, $code)
    {
        return response()->json([
            'status' => false,
            'code' => $code ?? 400,
            'message' => $msg ?? 'fail',
            'data' => []
        ], $code ?? 400);
    }
}

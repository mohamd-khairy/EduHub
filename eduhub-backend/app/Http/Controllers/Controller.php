<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Exports\TableExport;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\ResponseCache\Facades\ResponseCache;

abstract class Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $model_name = ucfirst(request()->segment(2));
            $model = app('App\\Models\\' . $model_name);

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

            if (request('export', null) == 1 && $model->count() > 0) {
                return Excel::download(new TableExport($model->get()), $model_name . '-' . date('Y-m-d-H-i-s') . '.xlsx');
            }

            $data = $model->with($relations ?? [])->orderBy('id', 'desc')->paginate(request('per_page', 10));

            // dd($data->toSql());
            return $this->success($data);
        } catch (\Throwable $th) {
            throw $th;
            // return  $this->fail([]);
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

            return $this->success($data);
        } catch (\Throwable $th) {
            throw $th;
            // return  $this->fail([]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $inpput = $request->all();
            $type = request()->segment(2);

            $model = app('App\\Models\\' . ucfirst($type));

            if ($request->image) {
                $image = $request->image->store('images', 'public');
                $inpput['image'] = url('/storage/' . $image);
            }

            $data = $model->create($inpput);

            $user = auth()->user(); //User::find(1);
            $user->notify(new NewMessageNotification(
                __('general.' . $type . '.store'),
                __('general.' . $type . '.store') . ' باسم ' . $data->name . ' بواسطة ' . $user->name,
                $type,
                $data->toArray()
            ));

            // broadcast(new NewMessage($data->toArray()));

            return $this->success($data);
        } catch (\Throwable $th) {
            throw $th;
            // return  $this->fail([]);
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

            return $this->success($data);
        } catch (\Throwable $th) {
            throw $th;
            // return  $this->fail([]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $input = $request->all();
            $type = request()->segment(2);
            $model = app('App\\Models\\' . ucfirst($type));

            $data = $model->where('id', $id)->first();

            if ($request->image) {
                $image = $request->image->store('images', 'public');
                $input['image'] = url('/storage/' . $image);
            }

            $data->update($input);

            $user = auth()->user(); //User::find(1);
            $user->notify(new NewMessageNotification(
                __('general.' . $type . '.store'),
                __('general.' . $type . '.store') . ' باسم ' . $data->name . ' بواسطة ' . $user->name,
                $type,
                $data->toArray()
            ));

            return $this->success($data);
        } catch (\Throwable $th) {
            throw $th;
            // return  $this->fail([]);
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

            return $this->success($data);
        } catch (\Throwable $th) {
            throw $th;
            // return  $this->fail([]);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $data = $model->whereIn('id', $request->ids)->delete();

            return $this->success($data);
        } catch (\Throwable $th) {
            throw $th;
            // return  $this->fail([]);
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
        if (
            request()->isMethod('post')
            || request()->isMethod('put')
            || request()->isMethod('delete')
        ) {
            ResponseCache::clear();
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $msg ?? 'sucess',
            'data' => $data
        ], 200);
    }

    public function fail($msg = null, $code = 400)
    {
        return response()->json([
            'status' => false,
            'code' => $code ?? 400,
            'message' => $msg ?? 'fail',
            'data' => []
        ], $code ?? 400);
    }
}

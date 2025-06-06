<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function changeStatus(Request $request)
    {
        try {
            $input = $request->all();
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $model = $model->where('student_id', $request->student_id)
                ->where('group_id', $request->group_id)
                ->first();

            if ($model) {
                $model->update(['status' => $input['status']]);
            }

            return $this->success($model);
        } catch (\Throwable $th) {
            //throw $th;
            return  $this->fail([]);
        }
    }

    public function delete(Request $request)
    {
        try {
            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $model = $model->where('student_id', $request->student_id)
                ->where('group_id', $request->group_id)
                ->first();

            if ($model) {
                $model->delete();
            }

            return $this->success($model);
        } catch (\Throwable $th) {
            //throw $th;
            return  $this->fail([]);
        }
    }
}

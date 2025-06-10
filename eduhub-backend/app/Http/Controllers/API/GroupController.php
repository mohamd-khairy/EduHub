<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Carbon\Carbon;

class GroupController extends Controller
{

    public function getGroupsByTime(Request $request)
    {
        try {
            $today = Carbon::today();
            $dayName = $today->format('l'); // l is the format for the full weekday name

            // or in Arabic
            $dayNameArabic = [
                'Saturday' => "السبت",
                'Sunday' => "الأحد",
                'Monday' => " الإثنين",
                'Tuesday' => "الثلاثاء",
                'Wednesday' => "الأربعاء",
                'Thursday' => "الخميس",
                'Friday' => "الجمعة",
            ][$dayName];

            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $currentTime = Carbon::now()->format('H:i:s');

            $data = $model->with('schedules')
                ->whereHas('schedules', function ($query) use ($dayNameArabic, $currentTime) {
                    $query->where('day', $dayNameArabic);
                    // ->where(
                    //     function ($query) use ($currentTime) {
                    //         $query->where('start_time', '<=', $currentTime)
                    //             ->where('end_time', '>=', $currentTime);
                    //     }
                    // );
                })->take(9)
                ->get();

            return $this->success($data);
        } catch (\Throwable $th) {
            //throw $th;
            return  $this->fail([]);
        }
    }

    public function store(Request $request)
    {
        try {
            $inpput = $request->all();

            $model = app('App\\Models\\' . ucfirst(request()->segment(2)));

            $data = $model->create($inpput);

            if ($request->schedules) {
                $schedules = [];
                foreach ($request->schedules as $schedule) {
                    $schedule['group_id'] = $data->id;
                    Schedule::create($schedule);
                }
                $data->schedules = $schedules;
            }

            return $this->success($data);
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

            $model->update($input);

            if ($request->schedules) {
                foreach ($request->schedules as $schedule) {
                    if (isset($schedule['id'])) {
                        $existingSchedule = Schedule::find($schedule['id']);
                        if ($existingSchedule) {
                            $existingSchedule->update($schedule);
                        }
                    } else {
                        $schedule['group_id'] = $model->id;
                        Schedule::create($schedule);
                    }
                }
            }

            return $this->success($model);
        } catch (\Throwable $th) {
            //throw $th;
            return  $this->fail([]);
        }
    }
}

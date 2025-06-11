<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Schedule;
use Carbon\Carbon;

use function App\Helpers\get_day_name_by_date;

class GroupController extends Controller
{

    public function groupAttendance(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        // Eager load students and today's attendance for the given group
        $group = Group::with('students')->findOrFail($validated['group_id']);

        return $this->success($group);
    }


    public function getGroupsByTime(Request $request)
    {
        try {
            $data = Group::with('currentSchedules')
                ->has('currentSchedules')
                // ->whereHas('schedules', function ($query) {
                //     $query->where('day', get_day_name_by_date())
                //     ->where(
                //         function ($query) use ($currentTime) {
                //          $currentTime = Carbon::now()->format('H:i:s');
                //             $query->where('start_time', '<=', $currentTime)
                //                 ->where('end_time', '>=', $currentTime);
                //         }
                //     );
                // })
                // ->take(9)
                ->get();

            return $this->success($data);
        } catch (\Throwable $th) {
            throw $th;
            return  $this->fail([]);
        }
    }

    public function store(Request $request)
    {
        try {
            $inpput = $request->all();

            $data = Group::create($inpput);

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

            $model = Group::where('id', $id)->first();

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

<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;

class StudentController extends Controller
{
    public function information($id)
    {
        $student = Student::with(['parent', 'groups.schedules', 'groups.course', 'groups.teacher'])->findOrFail($id);

        foreach ($student->groups as $key => $group) {
            foreach ($group->schedules as $schedule) {

                $schedule->attendances = Attendance::where('group_id', $group->id)
                    ->where('schedule_id', $schedule->id)
                    ->where('student_id', $student->id)
                    ->get();
            }
        }

        return $this->success($student);
    }
}

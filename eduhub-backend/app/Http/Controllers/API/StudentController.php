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
        $student = Student::with([
            'parent',
            'payments',
            'groups.schedules',
            'groups.course',
            'groups.teacher',
            'groups.exams'
        ])->findOrFail($id);

        foreach ($student->groups ?? [] as $group) {
            foreach ($group->schedules ?? [] as $schedule) {
                $schedule->attendances = $schedule->attendances()
                    ->where('student_id', $student->id)
                    ->get();
            }

            foreach ($group->exams ?? [] as $exam) {
                $exam->results = $exam->results()
                    ->where('student_id', $student->id)
                    ->get();
            }
        }

        return $this->success($student);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Group;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'schedule_id' => 'required|exists:schedules,id',
            'group_id' => 'required|exists:groups,id',
            'status' => 'required',
        ]);

        $attendance = Attendance::updateOrCreate(
            $request->except('status') + ['date' => today()],
            ['status' => $request->status]
        );
        return $this->success($attendance);
    }

    public function updateAllAttendance(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'group_id' => 'required|exists:groups,id',
            'status' => 'required',
        ]);

        $group = Group::with('students')->find($validated['group_id']);
        $students = $group->students;

        if ($students->isEmpty()) {
            return $this->fail('لا يوجد طلاب في هذه المجموعة.');
        }

        $students->each(function ($student) use ($validated) {
            Attendance::updateOrCreate(
                [
                    'group_id'  => $validated['group_id'],
                    'student_id'  => $student->id,
                    'schedule_id' => $validated['schedule_id'],
                    'date'        => today(),
                ],
                [
                    'status' => $validated['status'],
                ]
            );
        });

        return $this->success($group);
    }
}

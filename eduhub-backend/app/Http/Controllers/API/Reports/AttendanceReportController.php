<?php

namespace App\Http\Controllers\API\Reports;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceReportController extends Controller
{

    public function overallStudentCommitment(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d H:i:s', strtotime($request->start)) : false;
        $end = filled($request->end) ? date('Y-m-d H:i:s', strtotime($request->end)) : false;
        $student_id = filled($request->student_id) ? $request->student_id : false;
        $group_id = filled($request->group_id) ? $request->group_id : false;

        $query = DB::table('students')
            ->leftJoin('attendances', 'attendances.student_id', '=', 'students.id')
            ->select(
                'students.name as student_name',
                DB::raw('COUNT(attendances.id) as total_sessions'),
                DB::raw('SUM(CASE WHEN attendances.status = "حضر" THEN 1 ELSE 0 END) as present_count')
            )
            ->when($student_id, fn($q) => $q->where('students.id', $student_id))
            ->when($group_id, fn($q) => $q->where('attendances.group_id', $group_id))
            ->when($start, fn($q) => $q->whereDate('attendances.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('attendances.created_at', '<=', $end))
            ->groupBy('students.id', 'students.name');

        $results = $query->get();

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $percentage = $row->total_sessions > 0
                ? round(($row->present_count / $row->total_sessions) * 100, 1)
                : 0;

            $labels[] = $row->student_name;
            $data[] = $percentage;
        }

        $studentName = $student_id ? Student::where('id', $student_id)->value('name') : null;
        $groupName = $group_id ? Group::where('id', $group_id)->value('name') : null;

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' =>  $studentName ? "التزام الطالب: $studentName" : ($groupName ? "التزام المجموعة: $groupName" : 'التزام الطلاب'),
                    'data' => $data,
                    'backgroundColor' => '#1E93C8'
                ]
            ]
        ]);
    }

    public function commitmentOverTime(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d', strtotime($request->start)) : now()->subMonths(3)->format('Y-m-d');
        $end = filled($request->end) ? date('Y-m-d', strtotime($request->end)) : now()->format('Y-m-d');
        $student_id = filled($request->student_id) ? $request->student_id : null;
        $group_id = filled($request->group_id) ? $request->group_id : null;

        $query = DB::table('attendances')
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month")
            ->selectRaw('COUNT(*) as total_sessions')
            ->selectRaw("SUM(CASE WHEN status = 'حضر' THEN 1 ELSE 0 END) as actual_attendance")
            ->when($student_id, fn($q) => $q->where('student_id', $student_id))
            ->when($group_id, fn($q) => $q->where('group_id', $group_id))
            ->whereBetween('date', [$start, $end])
            ->groupByRaw("DATE_FORMAT(date, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(date, '%Y-%m')");

        $results = $query->get();

        $labels = [];
        $actual = [];
        $booked = [];

        foreach ($results as $row) {
            $monthName = \Carbon\Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('F');
            $labels[] = $monthName;
            $actual[] = (int) $row->actual_attendance;
            $booked[] = (int) $row->total_sessions;
        }

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'الحضور الفعلي',
                    'data' => $actual,
                    'borderColor' => '#36A2EB',
                    'fill' => false,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'الدروس المحجوزة',
                    'data' => $booked,
                    'borderColor' => '#FF6384',
                    'fill' => false,
                    'tension' => 0.4,
                ]
            ]
        ]);
    }
}

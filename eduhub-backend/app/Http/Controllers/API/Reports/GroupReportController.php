<?php

namespace App\Http\Controllers\API\Reports;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupReportController extends Controller
{
    public function averageScores(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d H:i:s', strtotime($request->start)) : false;
        $end = filled($request->end) ? date('Y-m-d H:i:s', strtotime($request->end)) : false;
        $group_id = filled($request->group_id) ? $request->group_id : false;
        $student_id = filled($request->student_id) ? $request->student_id : false;

        $data = DB::table('exam_results')
            ->join('students', 'exam_results.student_id', '=', 'students.id')
            ->join('exams', 'exam_results.exam_id', '=', 'exams.id')
            ->join('groups', 'exams.group_id', '=', 'groups.id')
            ->select(
                'groups.name as group_name',
                DB::raw('ROUND(AVG(exam_results.score), 1) as average_score')
            )
            ->when($group_id, fn($q) => $q->where('groups.id', $group_id))
            ->when($student_id, fn($q) => $q->where('students.id', $student_id))
            ->when($start, fn($q) => $q->whereDate('exam_results.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('exam_results.created_at', '<=', $end))
            ->groupBy('groups.name')
            ->get();

        // Transform into Chart.js-like format
        $labels = $data->pluck('group_name');
        $scores = $data->pluck('average_score');

        // foreach ($labels as $key => $label) {
        //     $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        // }

        $studentName = $student_id ? Student::where('id', $student_id)->value('name') : null;
        $groupName = $group_id ? Group::where('id', $group_id)->value('name') : null;

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($studentName ? 'متوسط درجات الطالب ' . $studentName : 'متوسط درجات الطلاب') .
                        ' ' .
                        ($groupName ? 'في المجموعة ' . $groupName : ' في جميع المجموعات'),
                    'data' => $scores,
                    'backgroundColor' => '#1E93C8',
                ],
            ],
        ];

        return $this->success($chartData);
    }

    public function activeStudents(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d H:i:s', strtotime($request->start)) : false;
        $end = filled($request->end) ? date('Y-m-d H:i:s', strtotime($request->end)) : false;
        $group_id = filled($request->group_id) ? $request->group_id : false;
        $student_id = filled($request->student_id) ? $request->student_id : false;

        $students = DB::table('enrollments')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->join('groups', 'enrollments.group_id', '=', 'groups.id')
            ->select(
                'groups.name as group_name',
                DB::raw('COUNT(CASE WHEN enrollments.status = 1 THEN 1 END) as active_count'),
                DB::raw('COUNT(CASE WHEN enrollments.status = 0 THEN 1 END) as inactive_count')
            )
            ->when($group_id, fn($q) => $q->where('groups.id', $group_id))
            ->when($student_id, fn($q) => $q->where('students.id', $student_id))
            ->when($start, fn($q) => $q->whereDate('enrollments.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('enrollments.created_at', '<=', $end))
            ->groupBy('groups.name')
            ->get();

        $labels = $students->pluck('group_name');
        $activeData = $students->pluck('active_count');
        $inactiveData = $students->pluck('inactive_count');

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'نشط',
                    'data' => $activeData,
                    'backgroundColor' => '#28a745'
                ],
                [
                    'label' => 'غير نشط',
                    'data' => $inactiveData,
                    'backgroundColor' => '#dc3545'
                ]
            ]
        ]);
    }

    public function attendancePercentage(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d H:i:s', strtotime($request->start)) : false;
        $end = filled($request->end) ? date('Y-m-d H:i:s', strtotime($request->end)) : false;
        $group_id = filled($request->group_id) ? $request->group_id : false;
        $student_id = filled($request->student_id) ? $request->student_id : false;

        $query = DB::table('groups')
            ->leftJoin('attendances', 'attendances.group_id', '=', 'groups.id')
            ->select(
                'groups.id as group_id',
                'groups.name as group_name',
                DB::raw('COUNT(attendances.id) as total_sessions'),
                DB::raw('SUM(CASE WHEN attendances.status = "حضر" THEN 1 ELSE 0 END) as present_count')
            )
            ->when($group_id, fn($q) => $q->where('groups.id', $group_id))
            ->when($student_id, fn($q) => $q->where('attendances.student_id', $student_id))
            ->when($start, fn($q) => $q->whereDate('attendances.date', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('attendances.date', '<=', $end))
            ->groupBy('groups.id', 'groups.name');

        $results = $query->get();

        $labels = [];
        $percentages = [];
        $colors = [];
        foreach ($results as $row) {
            $total = $row->total_sessions;
            $present = $row->present_count ?? 0;

            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            $labels[] = $row->group_name;
            $percentages[] = $percentage;
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        $studentName = $student_id ? Student::where('id', $student_id)->value('name') : null;

        if ($studentName) {
            $title = ($studentName ? 'نسبة حضور الطالب ' . $studentName : 'نسبة حضور الطلاب') . ' (%)';
        } else {
            $title = 'نسبة الحضور (%)';
        }

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $title,
                    'data' => $percentages,
                    'backgroundColor' => $colors // '#1E93C8'
                ]
            ]
        ]);
    }

    public function absentPercentage(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d H:i:s', strtotime($request->start)) : false;
        $end = filled($request->end) ? date('Y-m-d H:i:s', strtotime($request->end)) : false;
        $group_id = filled($request->group_id) ? $request->group_id : false;
        $student_id = filled($request->student_id) ? $request->student_id : false;

        $query = DB::table('groups')
            ->leftJoin('attendances', 'attendances.group_id', '=', 'groups.id')
            ->select(
                'groups.id as group_id',
                'groups.name as group_name',
                DB::raw('COUNT(attendances.id) as total_sessions'),
                DB::raw('SUM(CASE WHEN attendances.status = "غائب" THEN 1 ELSE 0 END) as present_count')
            )
            ->when($group_id, fn($q) => $q->where('groups.id', $group_id))
            ->when($student_id, fn($q) => $q->where('attendances.student_id', $student_id))
            ->when($start, fn($q) => $q->whereDate('attendances.date', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('attendances.date', '<=', $end))
            ->groupBy('groups.id', 'groups.name');

        $results = $query->get();

        $labels = [];
        $percentages = [];
        $colors = [];

        foreach ($results as $row) {
            $total = $row->total_sessions;
            $present = $row->present_count ?? 0;

            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            $labels[] = $row->group_name;
            $percentages[] = $percentage;
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        $studentName = $student_id ? Student::where('id', $student_id)->value('name') : null;

        if ($studentName) {
            $title = ($studentName ? 'نسبة غياب الطالب ' . $studentName : 'نسبة غياب الطلاب') . ' (%)';
        } else {
            $title = 'نسبة الغياب (%)';
        }

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $title,
                    'data' => $percentages,
                    'backgroundColor' => $colors // '#1E93C8'
                ]
            ]
        ]);
    }

    public function latePercentage(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d H:i:s', strtotime($request->start)) : false;
        $end = filled($request->end) ? date('Y-m-d H:i:s', strtotime($request->end)) : false;
        $group_id = filled($request->group_id) ? $request->group_id : false;
        $student_id = filled($request->student_id) ? $request->student_id : false;

        $query = DB::table('groups')
            ->leftJoin('attendances', 'attendances.group_id', '=', 'groups.id')
            ->select(
                'groups.id as group_id',
                'groups.name as group_name',
                DB::raw('COUNT(attendances.id) as total_sessions'),
                DB::raw('SUM(CASE WHEN attendances.status = "متأخر" THEN 1 ELSE 0 END) as present_count')
            )
            ->when($group_id, fn($q) => $q->where('groups.id', $group_id))
            ->when($student_id, fn($q) => $q->where('attendances.student_id', $student_id))
            ->when($start, fn($q) => $q->whereDate('attendances.date', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('attendances.date', '<=', $end))
            ->groupBy('groups.id', 'groups.name');

        $results = $query->get();

        $labels = [];
        $percentages = [];
        $colors = [];
        
        foreach ($results as $row) {
            $total = $row->total_sessions;
            $present = $row->present_count ?? 0;

            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            $labels[] = $row->group_name;
            $percentages[] = $percentage;
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        $studentName = $student_id ? Student::where('id', $student_id)->value('name') : null;

        if ($studentName) {
            $title = ($studentName ? 'نسبة تأخير الطالب ' . $studentName : 'نسبة تأخير الطلاب') . ' (%)';
        } else {
            $title = 'نسبة التأخير (%)';
        }

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $title,
                    'data' => $percentages,
                    'backgroundColor' => $colors // '#1E93C8'
                ]
            ]
        ]);
    }
}

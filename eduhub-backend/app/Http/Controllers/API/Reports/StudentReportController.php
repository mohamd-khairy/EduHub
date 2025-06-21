<?php

namespace App\Http\Controllers\API\Reports;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentReportController extends Controller
{
    public function studentPerformancePerGroup(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d H:i:s', strtotime($request->start)) : false;
        $end = filled($request->end) ? date('Y-m-d H:i:s', strtotime($request->end)) : false;
        $group_id = filled($request->group_id) ? $request->group_id : false;
        $student_id = filled($request->student_id) ? $request->student_id : false;

        $groups = Group::with(['students', 'students.examResults'])
            ->when($group_id, function ($q) use ($group_id) {
                $q->where('id', $group_id);
            })
            ->when($student_id, function ($q) use ($student_id) {
                $q->whereHas('students', function ($q) use ($student_id) {
                    $q->where('student_id', $student_id);
                });
            })
            ->when($start && $end, function ($q) use ($start, $end) {
                $start = date('Y-m-d H:i:s', strtotime($start));
                $end = date('Y-m-d H:i:s', strtotime($end));
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($groups as $group) {
            $labels[] = $group->name;

            $totalScore = 0;

            foreach ($group->students as $student) {
                $totalScore += $student->examResults->sum('score');
            }

            $data[] = $totalScore;

            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($student_id ? 'مجموع درجات الطالب ' . Student::find($student_id)->name : 'مجموع درجات الطلاب') . ' ' . ($group_id ? 'في المجموعة ' . Group::find($group_id)->name : ' في جميع المجموعات'),
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
        ];

        return $this->success($chartData);
    }

    public function studentPerformanceOverTime(Request $request)
    {
        $groupId = $request->input('group_id');
        $studentId = $request->input('student_id');
        $startDate = $request->input('start');
        $endDate = $request->input('end');

        $query = DB::table('exam_results')
            ->select(
                DB::raw("DATE_FORMAT(exam_results.created_at, '%Y-%m') as month"),
                DB::raw("SUM(score) as score")
            )
            ->join('exams', 'exam_results.exam_id', '=', 'exams.id');

        if ($studentId) {
            $query->where('exam_results.student_id', $studentId);
        }

        if ($groupId) {
            $query->where('exams.group_id', $groupId);
        }

        if ($startDate) {
            $query->whereDate('exam_results.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('exam_results.created_at', '<=', $endDate);
        }

        $results = $query
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $monthName = Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('F');
            $labels[] = $monthName;
            $data[] = round($row->score, 2);
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($studentId ? 'مجموع درجات الطالب ' . Student::find($studentId)->name : 'مجموع درجات الطلاب') . ' ' . ($groupId ? 'في المجموعة ' . Group::find($groupId)->name : ' في جميع المجموعات'),
                    'data' => $data,
                    'borderColor' => "rgba(59,130,246,0.7)",
                    'tension' => 0.4,
                    'fill' => false,
                ],
            ],
        ];

        return $this->success($chartData);
    }

    public function studentPerformancePerExam(Request $request)
    {
        $studentId = $request->input('student_id');
        $groupId = $request->input('group_id');
        $startDate = $request->input('start');
        $endDate = $request->input('end');

        $query = DB::table('exam_results')
            ->join('exams', 'exam_results.exam_id', '=', 'exams.id')
            ->select(
                'exams.title as exam_name',
                DB::raw('SUM(exam_results.score) as total_score')
            );

        if ($studentId) {
            $query->where('exam_results.student_id', $studentId);
        }

        if ($groupId) {
            $query->where('exams.group_id', $groupId);
        }

        if ($startDate) {
            $query->whereDate('exam_results.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('exam_results.created_at', '<=', $endDate);
        }

        $results = $query
            ->groupBy('exams.title')
            ->get();

        $labels = [];
        $data = [];
        $colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#DA2D78", "#53EEE1", "#BF92DA", "#CD1756", "#A94852"];

        foreach ($results as $index => $result) {
            $labels[] = $result->exam_name;
            $data[] = round($result->total_score, 2);
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($studentId ? 'مجموع درجات الطالب ' . Student::find($studentId)?->name : 'مجموع درجات الطلاب') .
                        ($groupId ? ' في المجموعة ' . Group::find($groupId)?->name : ' في جميع المجموعات'),
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ]
        ];

        return $this->success($chartData);
    }

    public function studentAttendanceSummary(Request $request)
    {
        $studentId = $request->input('student_id');
        $groupId = $request->input('group_id');
        $startDate = $request->input('start');
        $endDate = $request->input('end');

        $query = DB::table('attendances')
            ->select(
                DB::raw("SUM(CASE WHEN status = 'حضر' THEN 1 ELSE 0 END) as present_count"),
                DB::raw("SUM(CASE WHEN status = 'غائب' THEN 1 ELSE 0 END) as absent_count"),
                DB::raw("SUM(CASE WHEN status = 'متأخر' THEN 1 ELSE 0 END) as late_count")
            );

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $result = $query->first();

        $labels = ['حضور', 'غياب', 'تأخير'];
        $data = [(int) $result->present_count, (int) $result->absent_count, (int) $result->late_count];
        $colors = ['green', 'red', '#FFCE56'];

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($studentId ? 'حضور الطالب ' . Student::find($studentId)->name : 'حضور الطلاب') .
                        ($groupId ? ' في المجموعة ' . Group::find($groupId)->name : ' في جميع المجموعات'),
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
        ];

        return $this->success($chartData);
    }
}

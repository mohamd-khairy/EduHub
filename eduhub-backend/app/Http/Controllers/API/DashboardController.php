<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Group;
use App\Models\ParentModel;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d H:i:s', strtotime($request->start)) : false;
        $end = filled($request->end) ? date('Y-m-d H:i:s', strtotime($request->end)) : false;
        $group_id = filled($request->group_id) ? $request->group_id : false;

        $courseCount = Course::query()
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($group_id, fn($q) => $q->whereHas('groups', fn($q) => $q->where('id', $group_id)))
            ->count();
        $teacherCount = Teacher::query()
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($group_id, fn($q) => $q->whereHas('groups', fn($q) => $q->where('id', $group_id)))
            ->count();
        $studentCount = Student::query()
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($group_id, fn($q) => $q->whereHas('enrollments', fn($q) => $q->where('group_id', $group_id)))
            ->count();
        $parentCount = ParentModel::query()
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($group_id, fn($q) => $q->whereHas('students.enrollments', fn($q) => $q->where('group_id', $group_id)))
            ->count();
        $examCount = Exam::query()
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($group_id, fn($q) => $q->where('group_id', $group_id))
            ->count();
        $groupCount = Group::query()
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($group_id, fn($q) => $q->where('id', $group_id))
            ->count();
        $paymentSum = Payment::query()
            ->when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $end]))
            ->when($group_id, fn($q) => $q->whereHas('student.enrollments', fn($q) => $q->where('group_id', $group_id)))
            ->where('status', 'paid')->sum('amount');

        $data = [
            [
                'path' =>  'courses',
                'label' => 'المواد الدراسية',
                'icon' => 'i-heroicons-book-open',
                'count' => $courseCount,
            ],
            [
                'path' =>  'teachers',
                'label' => 'المدرسين',
                'icon' => 'i-heroicons-user-group',
                'count' => $teacherCount,
            ],
            [
                'path' =>  'students',
                'label' => 'الطلاب',
                'icon' => 'i-heroicons-user-circle',
                'count' => $studentCount,
            ],
            [
                'path' =>  'parents',
                'label' => 'أولياء الأمور',
                'icon' => 'i-heroicons-users',
                'count' => $parentCount,
            ],
            [
                'path' =>  'exams',
                'label' => 'الاختبارات',
                'icon' => 'i-heroicons-document-text',
                'count' => $examCount,
            ],
            [
                'path' =>  'groups',
                'label' => 'المجموعات',
                'icon' => 'i-heroicons-folder-open',
                'count' => $groupCount,
            ],
            [
                'path' => 'payments',
                'label' => 'المدفوعات',
                'icon' => 'i-heroicons-credit-card',
                'count' => number_format($paymentSum, 2) . ' $' // Format the amount
            ]
        ];

        return $this->success($data);
    }

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
}

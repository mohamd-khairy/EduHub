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
use Illuminate\Http\Request;

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
        $groups = Group::with(['students.examResults'])->get();

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
                    'label' => 'مجموع درجات الطلاب في المجموعة',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
        ];

        return $this->success($chartData);
    }
}

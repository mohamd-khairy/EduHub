<?php

namespace App\Http\Controllers\API\Reports;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Group;
use App\Models\ParentModel;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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

    public function monthlyStudentCount(Request $request)
    {
        // Get start and end dates from request, or default to the current month
        $start = filled($request->start) ? date('Y-m-d', strtotime($request->start)) : null;
        $end = filled($request->end) ? date('Y-m-d', strtotime($request->end)) : null;
        $group_id = $request->group_id ?? null;

        // Initialize $months as an empty array
        $months = [];

        // If start and end are provided, generate months within the range
        if ($start && $end) {
            $start_date = \Carbon\Carbon::parse($start);
            $end_date = \Carbon\Carbon::parse($end);

            // Generate all months between the start and end dates
            while ($start_date->lte($end_date)) {
                $months[] = $start_date->format('Y-m');
                $start_date->addMonth();
            }
        } else {
            // If no start and end dates are provided, generate all 12 months of the current year
            $currentYear = now()->year;
            $months = [];
            for ($month = 1; $month <= 12; $month++) {
                $months[] = $currentYear . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            }
        }

        // Query to get the count of students grouped by month
        $students = DB::table('students')
            ->selectRaw("DATE_FORMAT(students.created_at, '%Y-%m') as month, COUNT(*) as count")
            ->leftJoin('enrollments', 'students.id', '=', 'enrollments.student_id') // Join the enrollments table
            ->when($group_id, fn($q) => $q->where('enrollments.group_id', $group_id))  // Filter by group_id if provided
            ->when($start, fn($q) => $q->whereDate('students.created_at', '>=', $start))  // Filter by start date if provided
            ->when($end, fn($q) => $q->whereDate('students.created_at', '<=', $end))  // Filter by end date if provided
            ->groupBy('month')  // Group the results by month
            ->orderBy('month', 'asc')  // Order by month in ascending order
            ->get();

        // Prepare the data
        $labels = $months;
        $data = [];

        // For each month, check if there's a count in the result, otherwise set it to 0
        foreach ($months as $month) {
            $result = $students->firstWhere('month', $month);
            $data[] = $result ? $result->count : 0;  // If no result, set count to 0
        }

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' =>  'عدد الطلاب'
                        . ($group_id ?  ' في المجموعة:  ' . DB::table('groups')->where('id', $group_id)->value('name') : '  في جميع المجموعات'),
                    'data' => $data,
                    'backgroundColor' => '#00a155',
                ]
            ]
        ]);
    }

    public function getGroupScoresRatio(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d', strtotime($request->start)) : null;
        $end = filled($request->end) ? date('Y-m-d', strtotime($request->end)) : now()->format('Y-m-d');
        $group_id = $request->group_id ?? null;
        $student_id = $request->student_id ?? null;

        $query = DB::table('groups')
            ->leftJoin('exams', 'exams.group_id', '=', 'groups.id')
            ->leftJoin('exam_results', 'exam_results.exam_id', '=', 'exams.id')
            ->select(
                'groups.name as group_name',
                DB::raw('SUM(exams.total_marks) as total_score'),
                DB::raw('(SUM(exam_results.score) / SUM(exams.total_marks) * 100) as score_ratio')
            )
            ->when($group_id, fn($q) => $q->where('groups.id', $group_id))
            ->when($student_id, fn($q) => $q->where('exam_results.student_id', $student_id))
            ->when($start, fn($q) => $q->whereDate('exams.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('exams.created_at', '<=', $end))
            ->groupBy('groups.id', 'groups.name');

        // Execute the query and get the results
        $results = $query->get();

        // Get all group names to ensure every group is returned in the result
        $allGroups = DB::table('groups')->pluck('name')->toArray();

        // Prepare labels and data
        $labels = [];
        $data = [];

        // Loop through all groups and check if there's data for the group, if not, assign default value (0)
        foreach ($allGroups as $group) {
            // Find the matching group data, if available
            $matchingGroup = $results->firstWhere('group_name', $group);

            if ($matchingGroup) {
                // If data exists for the group, use its score ratio
                $labels[] = $matchingGroup->group_name;
                $data[] = round($matchingGroup->score_ratio, 1);
            } else {
                // If no data for the group, set score ratio to 0
                $labels[] = $group;
                $data[] = 0;
            }
        }

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($student_id ? 'نسبة الدرجات للطالب: ' . DB::table('students')->where('id', $student_id)->value('name') : 'نسبة الدرجات لكل الطلاب')
                        . ($group_id ?  ' في المجموعة:  ' . DB::table('groups')->where('id', $group_id)->value('name')    : '  في جميع المجموعات') . ' (%) ',
                    'data' =>  $data,
                    'backgroundColor' => '#00a155',
                ]
            ]
        ]);
    }
}

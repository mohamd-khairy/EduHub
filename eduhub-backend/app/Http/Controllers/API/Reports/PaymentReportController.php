<?php

namespace App\Http\Controllers\API\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentReportController extends Controller
{

    public function paymentsPerStudent(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d H:i:s', strtotime($request->start)) : null;
        $end = filled($request->end) ? date('Y-m-d H:i:s', strtotime($request->end)) : null;
        $group_id = filled($request->group_id) ? $request->group_id : null;
        $student_id = filled($request->student_id) ? $request->student_id : null;

        $query = DB::table('payments')
            ->join('students', 'payments.student_id', '=', 'students.id')
            ->select(
                'students.name as student_name',
                DB::raw('SUM(payments.amount) as total_paid')
            )
            ->when($group_id, function ($q) {
                $q->join('enrollments', 'enrollments.student_id', '=', 'students.id')
                    ->where('enrollments.group_id', request()->group_id);
            })
            ->when($student_id, fn($q) => $q->where('students.id', $student_id))
            ->when($start, fn($q) => $q->whereDate('payments.payment_date', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('payments.payment_date', '<=', $end))
            ->groupBy('students.id', 'students.name')
            ->orderByDesc('total_paid')
            ->get();

        $labels = $query->pluck('student_name');
        $amounts = $query->pluck('total_paid');

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($student_id ? "المدفوعات للطالب: " . DB::table('students')->where('id', $student_id)->value('name') : 'المدفوعات لكل الطلاب')
                        . ($group_id ?  ' في المجموعة:  ' . DB::table('groups')->where('id', $group_id)->value('name')    : '  في جميع المجموعات'),
                    'data' => $amounts,
                    'backgroundColor' => '#36A2EB'
                ]
            ]
        ]);
    }

    public function monthlyRevenueReport(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d', strtotime($request->start)) : now()->subMonths(6)->startOfMonth()->toDateString();
        $end = filled($request->end) ? date('Y-m-d', strtotime($request->end)) : now()->toDateString();
        $group_id = filled($request->group_id) ? $request->group_id : null;
        $student_id = filled($request->student_id) ? $request->student_id : null;

        $query = DB::table('payments')
            ->join('students', 'payments.student_id', '=', 'students.id')
            ->when($group_id, function ($q) {
                $q->join('enrollments', 'enrollments.student_id', '=', 'students.id')
                    ->where('enrollments.group_id', request()->group_id);
            })
            ->when($student_id, fn($q) => $q->where('students.id', $student_id))
            ->whereBetween('payments.payment_date', [$start, $end])
            ->selectRaw("DATE_FORMAT(payments.payment_date, '%Y-%m') as month")
            ->selectRaw('SUM(payments.amount) as total')
            ->groupByRaw("DATE_FORMAT(payments.payment_date, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(payments.payment_date, '%Y-%m')")
            ->get();

        $labels = [];
        $values = [];

        foreach ($query as $row) {
            $labels[] = \Carbon\Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('F');
            $values[] = (float) $row->total;
        }

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($student_id ? "إيرادات الطالب: " . DB::table('students')->where('id', $student_id)->value('name') : 'إيرادات جميع الطلاب')
                        . ($group_id ?  ' في المجموعة:  ' . DB::table('groups')->where('id', $group_id)->value('name')    : '  في جميع المجموعات'),
                    'data' => $values,
                    'borderColor' => '#4BC0C0',
                    'fill' => false,
                    'tension' => 0.4,
                ]
            ]
        ]);
    }

    public function overduePaymentsReport(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d', strtotime($request->start)) : null;
        $end = filled($request->end) ? date('Y-m-d', strtotime($request->end)) : now()->format('Y-m-d');
        $group_id = $request->group_id ?? null;
        $student_id = $request->student_id ?? null;

        $query = DB::table('payments')
            ->join('students', 'payments.student_id', '=', 'students.id')
            ->when($group_id, function ($q) {
                $q->join('enrollments', 'enrollments.student_id', '=', 'students.id')
                    ->where('enrollments.group_id', request()->group_id);
            })
            ->when($student_id, fn($q) => $q->where('students.id', $student_id))
            ->where('payments.status', 'pending')
            ->when($start, fn($q) => $q->whereDate('payments.created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('payments.created_at', '<=', $end))
            ->select(
                'students.name as student_name',
                DB::raw('SUM(payments.amount) as total_due')
            )
            ->groupBy('students.id', 'students.name')
            ->orderByDesc('total_due')
            ->get();

        $labels = $query->pluck('student_name');
        $values = $query->pluck('total_due');

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($student_id ? "المدفوعات المتأخرة للطالب: " . DB::table('students')->where('id', $student_id)->value('name') : 'المدفوعات المتأخرة لكل الطلاب')
                        . ($group_id ?  ' في المجموعة:  ' . DB::table('groups')->where('id', $group_id)->value('name')    : '  في جميع المجموعات'),
                    'data' => $values,
                    'backgroundColor' => '#FF6384'
                ]
            ]
        ]);
    }

    public function paymentsByGroupReport(Request $request)
    {
        $start = filled($request->start) ? date('Y-m-d', strtotime($request->start)) : null;
        $end = filled($request->end) ? date('Y-m-d', strtotime($request->end)) : null;
        $group_id = $request->group_id ?? null;
        $student_id = $request->student_id ?? null;

        $query = DB::table('payments')
            ->join('students', 'payments.student_id', '=', 'students.id')
            ->join('enrollments', 'enrollments.student_id', '=', 'students.id')
            ->join('groups', 'enrollments.group_id', '=', 'groups.id')
            ->select('groups.name as group_name', DB::raw('SUM(payments.amount) as total_paid'))
            ->when($group_id, fn($q) => $q->where('groups.id', $group_id))
            ->when($student_id, fn($q) => $q->where('students.id', $student_id))
            ->when($start, fn($q) => $q->whereDate('payments.payment_date', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('payments.payment_date', '<=', $end))
            ->groupBy('groups.id', 'groups.name')
            ->orderBy('groups.name')
            ->get();

        $labels = $query->pluck('group_name');
        $data = $query->pluck('total_paid');
        $colors = collect(range(0, $labels->count() - 1))->map(fn() => sprintf('#%06X', mt_rand(0, 0xFFFFFF)));

        return $this->success([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ($student_id ? "المدفوعات للطالب: " . DB::table('students')->where('id', $student_id)->value('name') : 'المدفوعات لكل الطلاب')
                        . ($group_id ?  ' في المجموعة:  ' . DB::table('groups')->where('id', $group_id)->value('name')    : '  في جميع المجموعات'),
                    'data' => $data,
                    'backgroundColor' => $colors,
                ]
            ]
        ]);
    }
}

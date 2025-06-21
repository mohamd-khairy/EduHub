<?php

namespace App\Http\Controllers\API\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceReportController extends Controller
{
    public function studentAttendanceCommitmentReport(Request $request)
    {
        $groupId = $request->input('group_id');
        $startDate = $request->input('start');
        $endDate = $request->input('end');

        $query = DB::table('sessions')
            ->join('attendances', 'sessions.id', '=', 'attendances.session_id')
            ->join('students', 'attendances.student_id', '=', 'students.id')
            ->select(
                'students.id as student_id',
                'students.name as student_name',
                DB::raw('COUNT(sessions.id) as total_sessions'),
                DB::raw("SUM(CASE WHEN attendances.status = 'present' THEN 1 ELSE 0 END) as attended_sessions")
            )
            ->whereNull('sessions.deleted_at')
            ->groupBy('students.id', 'students.name');

        // تصفية بالمجموعة
        if ($groupId) {
            $query->where('sessions.group_id', $groupId);
        }

        // تصفية بالتاريخ
        if ($startDate) {
            $query->whereDate('sessions.date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('sessions.date', '<=', $endDate);
        }

        $results = $query->get();

        // تجهيز البيانات للرسم البياني
        $labels = [];
        $data = [];
        $colors = ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#DA2D78', '#53EEE1', '#BF92DA', '#CD1756', '#A94852'];

        foreach ($results as $index => $row) {
            $commitment = $row->total_sessions > 0
                ? round(($row->attended_sessions / $row->total_sessions) * 100, 2)
                : 0;

            $labels[] = $row->student_name;
            $data[] = $commitment;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'نسبة الالتزام',
                        'data' => $data,
                        'backgroundColor' => array_slice($colors, 0, count($data))
                    ]
                ]
            ]
        ]);
    }
}

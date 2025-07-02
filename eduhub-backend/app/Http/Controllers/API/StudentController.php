<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function generatePDF($id)
    {
        // Fetch student data from the database
        $student = Student::findOrFail($id);

        // Return PDF as a response, rendered from a Blade view
        $pdf = Pdf::loadView('student-card', compact('student'));

        // Download the PDF file
        return $pdf->download('student-card-' . $student->id . '.pdf');
    }

    public function viewgeneratePDF($id)
    {
        // Fetch student data from the database
        $student = Student::findOrFail($id);

        return view('student-card', compact('student'));
    }
}

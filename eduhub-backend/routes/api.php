<?php

use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AuditController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\ExamController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\Reports\HomeController;
use App\Http\Controllers\API\ParentController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\Reports\AttendanceReportController;
use App\Http\Controllers\API\Reports\GroupReportController;
use App\Http\Controllers\API\Reports\PaymentReportController;
use App\Http\Controllers\API\Reports\StudentReportController;
use App\Http\Controllers\API\ResultController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\StudyYearController;
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\UserController;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;



Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'chat'], function () {
        Route::get('/all', [ChatController::class, 'All']);
        Route::get('/', [ChatController::class, 'index']);
        Route::get('/{id}', [ChatController::class, 'show']);
        Route::post('/', [ChatController::class, 'store']);
        Route::post('/delete-all', [ChatController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'chatMessage'], function () {
        Route::post('/', [ChatMessage::class, 'store']);
    });
    Route::group(['prefix' => 'audit'], function () {
        Route::get('/', [AuditController::class, 'index']);
    });
    Route::group(['prefix' => 'auth'], function () {
        Route::get('/me', [AuthController::class, 'Me']);
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });
    Route::group(['prefix' => 'notification'], function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/all', [NotificationController::class, 'All']);
        Route::post('/read/{id}', [NotificationController::class, 'readNotification']);
        Route::post('/read-all', [NotificationController::class, 'readAllNotification']);
        Route::post('/delete-all', [NotificationController::class, 'deleteAll']);
    });
    Route::group(['prefix' => 'group'], function () {
        Route::get('/all', [GroupController::class, 'All']);
        Route::get('/groups-by-time', [GroupController::class, 'getGroupsByTime']);
        Route::post('/group-attendance', [GroupController::class, 'groupAttendance']);
        Route::post('/delete-all', [GroupController::class, 'deleteAll']);
        Route::get('/', [GroupController::class, 'index']);
        Route::post('', [GroupController::class, 'store']);
        Route::get('{id}', [GroupController::class, 'show']);
        Route::put('{id}', [GroupController::class, 'update']);
    });
    Route::group(['prefix' => 'studyYear'], function () {
        Route::get('/all', [StudyYearController::class, 'All']);
        Route::post('/delete-all', [StudyYearController::class, 'deleteAll']);
        Route::get('/', [StudyYearController::class, 'index']);
        Route::post('', [StudyYearController::class, 'store']);
        Route::get('{id}', [StudyYearController::class, 'show']);
        Route::put('{id}', [StudyYearController::class, 'update']);
    });
    Route::group(['prefix' => 'course'], function () {
        Route::get('/all', [CourseController::class, 'All']);
        Route::post('/delete-all', [CourseController::class, 'deleteAll']);
        Route::get('/', [CourseController::class, 'index']);
        Route::post('', [CourseController::class, 'store']);
        Route::get('{id}', [CourseController::class, 'show']);
        Route::put('{id}', [CourseController::class, 'update']);
    });
    Route::group(['prefix' => 'teacher'], function () {
        Route::get('/all', [TeacherController::class, 'All']);
        Route::post('/delete-all', [TeacherController::class, 'deleteAll']);
        Route::get('/', [TeacherController::class, 'index']);
        Route::post('', [TeacherController::class, 'store']);
        Route::get('{id}', [TeacherController::class, 'update']);
        Route::put('{id}', [TeacherController::class, 'update']);
    });
    Route::group(['prefix' => 'user'], function () {
        Route::get('/all', [UserController::class, 'All']);
        Route::post('/delete-all', [UserController::class, 'deleteAll']);
        Route::get('/', [UserController::class, 'index']);
        Route::post('', [UserController::class, 'store']);
        Route::get('{id}', [UserController::class, 'show']);
        Route::post('{id}', [UserController::class, 'update']);
    });
    Route::group(['prefix' => 'student'], function () {
        Route::get('/all', [StudentController::class, 'All']);
        Route::get('/information/{id}', [StudentController::class, 'information']);
        Route::post('/delete-all', [StudentController::class, 'deleteAll']);
        Route::get('/', [StudentController::class, 'index']);
        Route::post('', [StudentController::class, 'store']);
        Route::get('{id}', [StudentController::class, 'show']);
        Route::post('{id}', [StudentController::class, 'update']);
        Route::get('/student-card/{id}/pdf', [StudentController::class, 'generatePDF']);
    });
    Route::group(['prefix' => 'enrollment'], function () {
        Route::get('/all', [EnrollmentController::class, 'All']);
        Route::post('/delete', [EnrollmentController::class, 'delete']);
        Route::post('/delete-all', [EnrollmentController::class, 'deleteAll']);
        Route::put('/change-status', [EnrollmentController::class, 'changeStatus']);
        Route::get('/', [EnrollmentController::class, 'index']);
        Route::post('', [EnrollmentController::class, 'store']);
        Route::get('{id}', [EnrollmentController::class, 'show']);
        Route::post('{id}', [EnrollmentController::class, 'update']);
    });
    Route::group(['prefix' => 'parentModel'], function () {
        Route::get('/all', [ParentController::class, 'All']);
        Route::post('/delete-all', [ParentController::class, 'deleteAll']);
        Route::get('/', [ParentController::class, 'index']);
        Route::post('', [ParentController::class, 'store']);
        Route::get('{id}', [ParentController::class, 'show']);
        Route::put('{id}', [ParentController::class, 'update']);
    });
    Route::group(['prefix' => 'exam'], function () {
        Route::get('/all', [ExamController::class, 'All']);
        Route::post('/delete-all', [ExamController::class, 'deleteAll']);
        Route::get('/', [ExamController::class, 'index']);
        Route::post('', [ExamController::class, 'store']);
        Route::get('{id}', [ExamController::class, 'show']);
        Route::put('{id}', [ExamController::class, 'update']);
    });
    Route::group(['prefix' => 'examResult'], function () {
        Route::get('/all', [ResultController::class, 'All']);
        Route::post('/delete-all', [ResultController::class, 'deleteAll']);
        Route::get('/', [ResultController::class, 'index']);
        Route::post('', [ResultController::class, 'store']);
        Route::get('{id}', [ResultController::class, 'show']);
        Route::put('{id}', [ResultController::class, 'update']);
    });
    Route::group(['prefix' => 'attendance'], function () {
        Route::post('/update-all-attendance', [AttendanceController::class, 'updateAllAttendance']);
        Route::get('/all', [AttendanceController::class, 'All']);
        Route::post('/delete-all', [AttendanceController::class, 'deleteAll']);
        Route::get('/', [AttendanceController::class, 'index']);
        Route::post('', [AttendanceController::class, 'store']);
        Route::get('{id}', [AttendanceController::class, 'show']);
        Route::put('{id}', [AttendanceController::class, 'update']);
    });
    Route::group(['prefix' => 'payment'], function () {
        Route::get('/all', [PaymentController::class, 'All']);
        Route::post('/delete-all', [PaymentController::class, 'deleteAll']);
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('', [PaymentController::class, 'store']);
        Route::get('{id}', [PaymentController::class, 'show']);
        Route::put('{id}', [PaymentController::class, 'update']);
    });
    Route::group(['prefix' => 'role'], function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
        Route::get('/all', [RoleController::class, 'All']);
        Route::put('/{id}/permissions', [RoleController::class, 'updatePermission']);
    });
    Route::group(['prefix' => 'permission'], function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::get('/all', [PermissionController::class, 'All']);
    });
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [HomeController::class, 'index']);
        Route::get('/monthly-student-count', [HomeController::class, 'monthlyStudentCount']);
        Route::get('/group-scores-ratio', [HomeController::class, 'getGroupScoresRatio']);
        /************************************************ */
        Route::get('/student-performance-per-group', [StudentReportController::class, 'studentPerformancePerGroup']);
        Route::get('/student-performance-over-time', [StudentReportController::class, 'studentPerformanceOverTime']);
        Route::get('/student-performance-per-exam', [StudentReportController::class, 'studentPerformancePerExam']);
        Route::get('/student-attendance-summary', [StudentReportController::class, 'studentAttendanceSummary']);
        /************************************************ */
        Route::get('/group-average-scores', [GroupReportController::class, 'averageScores']);
        Route::get('/group-active-students', [GroupReportController::class, 'activeStudents']);
        Route::get('/group-attendance-percentage', [GroupReportController::class, 'attendancePercentage']);
        Route::get('/group-absent-percentage', [GroupReportController::class, 'absentPercentage']);
        Route::get('/group-late-percentage', [GroupReportController::class, 'latePercentage']);
        /************************************************ */
        Route::get('/attendance-overall-student-commitment', [AttendanceReportController::class, 'overallStudentCommitment']);
        Route::get('/attendance-commitment-over-time', [AttendanceReportController::class, 'commitmentOverTime']);
        Route::get('/attendance-compare-groups', [AttendanceReportController::class, 'compareGroups']);
        Route::get('/attendance-rank-students', [AttendanceReportController::class, 'rankStudents']);
        /************************************************ */
        Route::get('/payment-per-students', [PaymentReportController::class, 'paymentsPerStudent']);
        Route::get('/payment-monthly-revenue', [PaymentReportController::class, 'monthlyRevenueReport']);
        Route::get('/payment-overdue-student-payment', [PaymentReportController::class, 'overduePaymentsReport']);
        Route::get('/payment-per-groups', [PaymentReportController::class, 'paymentsByGroupReport']);
    });
});

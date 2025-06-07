<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\AttendanceFactory;
use Database\Factories\CourseFactory;
use Database\Factories\EnrollmentFactory;
use Database\Factories\ExamFactory;
use Database\Factories\ExamResultFactory;
use Database\Factories\GroupFactory;
use Database\Factories\ParentModelFactory;
use Database\Factories\PaymentFactory;
use Database\Factories\ScheduleFactory;
use Database\Factories\StudentFactory;
use Database\Factories\TeacherAttendanceFactory;
use Database\Factories\TeacherFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        UserFactory::new()->count(50)->create();
        CourseFactory::new()->count(50)->create();
        TeacherFactory::new()->count(50)->create();
        ParentModelFactory::new()->count(50)->create();
        GroupFactory::new()->count(50)->create();
        // ScheduleFactory::new()->count(50)->create();
        StudentFactory::new()->count(50)->create();
        TeacherAttendanceFactory::new()->count(50)->create();
        PaymentFactory::new()->count(50)->create();
        ExamFactory::new()->count(50)->create();
        ExamResultFactory::new()->count(50)->create();
        // EnrollmentFactory::new()->count(50)->create();
        // AttendanceFactory::new()->count(50)->create();
        Role::create(["name" => "admin"]);
        User::get()->each(function (User $user) {
            $user->assignRole("admin");
        });
    }
}

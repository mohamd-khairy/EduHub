<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use App\Services\RoleService;
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
        RoleService::create(['admin', 'teacher', 'student', 'parent']);

        UserFactory::new()->count(10)->create();
        CourseFactory::new()->count(10)->create();
        TeacherFactory::new()->count(10)->create();
        ParentModelFactory::new()->count(10)->create();
        GroupFactory::new()->count(7)->create();
        // ScheduleFactory::new()->count(10)->create();
        StudentFactory::new()->count(20)->create();
        // PaymentFactory::new()->count(10)->create();
        // ExamFactory::new()->count(10)->create();
        // ExamResultFactory::new()->count(10)->create();
        // EnrollmentFactory::new()->count(10)->create();
        // AttendanceFactory::new()->count(10)->create();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'email_verified_at' => now(),
            'phone' => '1234567890',
            'image' => 'http://eduhub.test/eduhub-backend/public/images/photo.jpg'
        ])->assignRole('admin');

        Role::findByName('parent')->givePermissionTo(Permission::where('group', 'parentmodel')->pluck('id'));

        Role::findByName('teacher')->givePermissionTo(Permission::where('group', 'teacher')->pluck('id'));

        Role::findByName('student')->givePermissionTo(Permission::where('group', 'student')->pluck('id'));
    }
}

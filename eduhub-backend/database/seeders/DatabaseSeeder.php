<?php

namespace Database\Seeders;

use App\Models\ParentModel;
use App\Models\Permission;
use App\Models\Student;
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
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        RoleService::create([
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'teacher', 'guard_name' => 'teacher'],
            ['name' => 'student', 'guard_name' => 'student'],
            ['name' => 'parent', 'guard_name' => 'parent'],
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'email_verified_at' => now(),
            'phone' => '1234567890',
            'image' => 'http://eduhub.test/eduhub-backend/public/images/photo.jpg'
        ])->assignRole('admin');

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

        $permissions = Permission::whereIn('name', [
            'read-student',
            'read-exam',
            'read-examresult',
            'read-attendance',
            'read-group',
            'read-teacher'
        ])->pluck('id');

        foreach ($permissions as $key => $value) {
            DB::table('role_has_permissions')->insert(['role_id' => Role::where('name', 'student')->value('id'), 'permission_id' => $value]);
        }

        $permissions = Permission::whereIn('name', [
            'read-student',
            'read-exam',
            'read-examresult',
            'read-attendance',
            'read-group',
            'read-teacher'
        ])->pluck('id');

        foreach ($permissions as $key => $value) {
            DB::table('role_has_permissions')->insert(['role_id' => Role::where('name', 'parent')->value('id'), 'permission_id' => $value]);
        }


        $permissions = Permission::whereIn('group', [
            'student',
            'exam',
            'examresult',
            'attendance',
            'group',
        ])->pluck('id');

        foreach ($permissions as $key => $value) {
            DB::table('role_has_permissions')->insert(['role_id' => Role::where('name', 'teacher')->value('id'), 'permission_id' => $value]);
        }




        // Role::findByName('parent', 'parent')
        //     ->givePermissionTo(Permission::whereIn('name', [
        //         'read-student',
        //         'read-exam',
        //         'read-examresult',
        //         'read-attendance',
        //         'read-group',
        //         'read-teacher'
        //     ])->pluck('id'));

        // Role::findByName('teacher', 'teacher')
        //     ->givePermissionTo(Permission::whereIn('group', 'teacher')->pluck('id'));

        // Role::findByName('student', 'student')
        //     ->givePermissionTo(Permission::whereIn('group', 'student')->pluck('id'));
    }
}

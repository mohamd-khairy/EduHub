<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\ParentModel;
use App\Models\Permission;
use App\Models\User;
use App\Services\RoleService;
use Database\Factories\CourseFactory;
use Database\Factories\GroupFactory;
use Database\Factories\ParentModelFactory;
use Database\Factories\StudentFactory;
use Database\Factories\StudyYearFactory;
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

        StudyYearFactory::new()->count(1)->create();
        UserFactory::new()->count(10)->create();
        CourseFactory::new()->count(10)->create();
        TeacherFactory::new()->count(10)->create();
        GroupFactory::new()->count(7)->create();
        ParentModelFactory::new()->count(20)->create();
        // ScheduleFactory::new()->count(10)->create();
        StudentFactory::new()->count(20)->create();
        // PaymentFactory::new()->count(10)->create();
        // ExamFactory::new()->count(10)->create();
        // ExamResultFactory::new()->count(10)->create();
        // EnrollmentFactory::new()->count(10)->create();
        // AttendanceFactory::new()->count(10)->create();

        $rolePermissions = [
            'student' => [
                'by_name' => [
                    'read-student',
                    'read-exam',
                    'read-examresult',
                    'read-attendance',
                    'read-group',
                    'read-teacher',
                    'read-parentmodel',
                    'read-student-parent',
                    'read-student-group',
                    'read-student-exam',
                    'read-student-attendance',
                    'read-student-payment',
                    'read-payment',
                    'read-notification'
                ]
            ],
            'parent' => [
                'by_name' => [
                    'read-student',
                    'read-exam',
                    'read-examresult',
                    'read-attendance',
                    'read-group',
                    'read-teacher',
                    'read-parentmodel',
                    'update-parentmodel',
                    'read-student-group',
                    'read-student-exam',
                    'read-student-attendance',
                    'read-student-payment',
                    'read-payment',
                    'read-notification'
                ]
            ],
            'teacher' => [
                'by_group' => [
                    'student',
                    'exam',
                    'examresult',
                    'attendance',
                ],
                'by_name' => [
                    'read-group',
                    'read-teacher',
                    'read-parentmodel',
                    'read-payment',
                    'read-notification'
                ]
            ],
        ];

        foreach ($rolePermissions as $roleName => $filters) {
            $roleId = Role::where('name', $roleName)->value('id');

            $permissions = Permission::where(function ($query) use ($filters) {
                if (isset($filters['by_name']))
                    $query->orWhereIn('name', $filters['by_name']);

                if (isset($filters['by_group']))
                    $query->orWhereIn('group', $filters['by_group']);
            })->pluck('id');

            $insertData = $permissions->map(function ($permId) use ($roleId) {
                return [
                    'role_id' => $roleId,
                    'permission_id' => $permId,
                ];
            })->toArray();

            DB::table('role_has_permissions')->insert($insertData);
        }


        foreach (ParentModel::all() as $parent) {
            $user = User::find(1);

            $chat = Chat::create([
                'sender_type' => $user->getMorphClass(),
                'sender_id' => $user->id,
                'receiver_type' => $parent->getMorphClass(),
                'receiver_id' => $parent->id
            ]);

            for ($i = 0; $i < 20; $i++) {
                $chat->messages()->create([
                    'sender_type' => $i % 2 ? $parent->getMorphClass() : $user->getMorphClass(),
                    'sender_id' => $i % 2 ? $parent->id : $user->id,
                    'message' => ' When your application is in debug mode, detailed error messages with
     stack traces will be shown on every error that occurs within your
     application. If disabled, a simple generic error page is shown.',
                    'sent_at' => now(),
                ]);
            }
        }
    }
}

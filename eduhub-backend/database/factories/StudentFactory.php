<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\ExamResult;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'الطالب ' . $this->faker->firstName(),
            'gender' => $this->faker->randomElement(['ذكر', 'أنثى']),
            'grade_level' => $this->faker->randomElement(['الصف الأول', 'الصف الثاني', 'الصف الثالث']),
            'school_name' => $this->faker->company() . ' School',
            'parent_id' => DB::table('parent_models')->inRandomOrder()->value('id'),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'image' => [
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/antfu',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/larbish',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/benjamincanac',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/celinedumerc',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/danielroe',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/farnabaz',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/FerdinandCoumau',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/hugorcd',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/pi0',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/SarahM19',
                'https://ipx.nuxt.com/f_auto,s_192x192/gh_avatar/atinux'
            ][rand(0, 10)]
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Student $student) {

            $student->assignRole("student");

            $start = $this->faker->dateTimeBetween('-2 months', 'now');
            $end = $this->faker->dateTimeBetween($start, '+2 months');
            $groupIds = DB::table('groups')->pluck('id'); // You can change 2 to any number

            foreach ($groupIds as $groupId) {

                Enrollment::factory()->create([
                    'student_id' => $student->id,
                    'group_id' => $groupId,
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => $end->format('Y-m-d'),
                    'status' => true,
                ]);
            }

            //exam Results
            Enrollment::with('group')->where('student_id', $student->id)->get()
                ->each(function ($enrollment) {
                    $enrollment->group->exams()->each(function ($exam) use ($enrollment) {
                        ExamResult::factory()->create([
                            'student_id' => $enrollment->student_id,
                            'exam_id' => $exam->id,
                            'score' => $this->faker->randomFloat(2, 0, $exam->total_marks ?? 100),
                        ]);
                    });

                    Payment::factory()->create([
                        'student_id' => $enrollment->student_id,
                        'amount' => $this->faker->randomFloat(2, 100, 1000), // Between 100 and 1000
                        'payment_date' => $this->faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
                        'method' => $this->faker->randomElement(['كاش', 'تحويل بنكي', 'فيزا']),
                        'status' => $this->faker->randomElement(['paid', 'pending', 'cancelled']),
                        'note' => 'فلوس شهر ' . $this->faker->monthName(),
                    ]);
                });
        });
    }
}

<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\ExamResult;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'parent_id' => \App\Models\ParentModel::inRandomOrder()->value('id'),
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
            $start = $this->faker->dateTimeBetween('-2 months', 'now');
            $end = $this->faker->dateTimeBetween($start, '+2 months');
            $groupIds = Group::pluck('id'); // You can change 2 to any number

            foreach ($groupIds as $groupId) {

                Enrollment::factory()->create([
                    'student_id' => $student->id,
                    'group_id' => $groupId,
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => $end->format('Y-m-d'),
                    'status' => true,
                ]);

                // for ($i = 0; $i < 10; $i++) {
                // Attendance::factory()->create([
                //     'student_id' => $student->id,
                //     'group_id' => $groupId,
                //     'schedule_id' => \App\Models\Schedule::inRandomOrder()->where('group_id', $groupId)->value('id'),
                //     'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                //     'status' => $this->faker->randomElement(['حضر', 'غائب', 'متأخر']),
                //     'note' => $this->faker->sentence(),
                // ]);

                // Payment::factory()->create([
                //     'student_id' => $student->id,
                //     'amount' => $this->faker->randomFloat(2, 100, 1000), // Between 100 and 1000
                //     'payment_date' => $this->faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
                //     'method' => $this->faker->randomElement(['كاش', 'تحويل بنكي', 'فيزا']),
                //     'status' => $this->faker->randomElement(['paid', 'pending', 'cancelled']),
                //     'note' => 'فلوس شهر ' . $this->faker->monthName(),
                // ]);
                // }
            }

            //exam Results
            Enrollment::where('student_id', $student->id)->get()
                ->each(function ($enrollment) {
                    $enrollment->group->exams()->each(function ($exam) use ($enrollment) {
                        for ($i = 0; $i < 3; $i++) {
                            ExamResult::factory()->create([
                                'student_id' => $enrollment->student_id,
                                'exam_id' => $exam->id,
                                'score' => $this->faker->randomFloat(2, 0, $exam->total_marks ?? 100),
                            ]);
                        }
                    });

                    $enrollment->group->schedules()->each(function ($schedule) use ($enrollment) {
                        Attendance::factory()->create([
                            'student_id' => $enrollment->student_id,
                            'group_id' =>  $enrollment->group_id,
                            'schedule_id' => $schedule->id,
                            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                            'status' => $this->faker->randomElement(['حضر', 'غائب', 'متأخر']),
                            'note' => $this->faker->sentence(),
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

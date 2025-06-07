<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Group;
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
            'name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['ذكر', 'أنثى']),
            'grade_level' => $this->faker->randomElement(['الصف الأول', 'الصف الثاني', 'الصف الثالث']),
            'school_name' => $this->faker->company() . ' School',
            'parent_id' => \App\Models\ParentModel::inRandomOrder()->value('id'),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'image' => $this->faker->imageUrl(200, 200, 'people'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Student $student) {
            $start = $this->faker->dateTimeBetween('-2 months', 'now');
            $end = $this->faker->dateTimeBetween($start, '+2 months');
            $groupIds = Group::inRandomOrder()->limit(2)->pluck('id'); // You can change 2 to any number

            foreach ($groupIds as $groupId) {

                Enrollment::factory()->create([
                    'student_id' => $student->id,
                    'group_id' => $groupId,
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => $end->format('Y-m-d'),
                    'status' => true,
                ]);
            }
            for ($i = 0; $i < 10; $i++) {
                Attendance::factory()->create([
                    'student_id' => $student->id,
                    'group_id' => $groupId,
                    'schedule_id' => \App\Models\Schedule::inRandomOrder()->where('group_id', $groupId)->value('id'),
                    'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                    'status' => $this->faker->randomElement(['حضر', 'غائب', 'متأخر']),
                    'note' => $this->faker->sentence(),
                ]);
            }
        });
    }
}

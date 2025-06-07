<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'name' => $this->faker->randomElement(['المجموعة أ', 'المجموعة ب', 'المجموعة ج']) . rand(1, 1000),
            'teacher_id' => Teacher::inRandomOrder()->first()?->id ?? Teacher::factory(),
            'course_id' => Course::inRandomOrder()->first()?->id ?? Course::factory(),
            'max_students' => $this->faker->numberBetween(15, 50),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Group $group) {
            $days = collect([
                'الأحد',
                'الإثنين',
                'الثلاثاء',
                'الأربعاء',
                'الخميس',
                'الجمعة',
                'السبت'
            ])->shuffle()->take(3); // Get 3 unique days randomly

            foreach ($days as $day) {
                Schedule::factory()->create([
                    'group_id' => $group->id,
                    'day' => $day,
                    'start_time' => now()->addHours(rand(8, 14))->format('H:i'),
                    'end_time' => now()->addHours(rand(15, 20))->format('H:i'),
                    'room_id' => 1,
                ]);
            }
        });
    }
}

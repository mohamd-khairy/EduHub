<?php

namespace Database\Factories;

use App\Models\Course;
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

            'name' => $this->faker->randomElement(['المجموعة أ', 'المجموعة ب', 'المجموعة ج']),
            'teacher_id' => Teacher::inRandomOrder()->first()?->id ?? Teacher::factory(),
            'course_id' => Course::inRandomOrder()->first()?->id ?? Course::factory(),
            'schedule' => $this->faker->randomElement([
                'الاربعاء-07:31, الاثنين-19:21,  الخميس-04:18',
                'الاحد-07:31, الثلاثاء-19:21,  الاثنين-04:18',
                'الجمعة-07:31, الثلاثاء-19:21,  السبت-04:18',
            ]),
            'max_students' => $this->faker->numberBetween(15, 50),
        ];
    }
}

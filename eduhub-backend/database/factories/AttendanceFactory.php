<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\Student::inRandomOrder()->value('id'),
            'group_id' => \App\Models\Group::inRandomOrder()->value('id'),
            'schedule_id' => \App\Models\Schedule::inRandomOrder()->value('id'),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['حضر', 'غائب', 'متأخر']),
            'note' => $this->faker->sentence(),
        ];
    }
}

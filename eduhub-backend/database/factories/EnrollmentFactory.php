<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $start = $this->faker->dateTimeBetween('-2 months', 'now');
        $end = $this->faker->dateTimeBetween($start, '+2 months');

        return [
            'student_id' => \App\Models\Student::inRandomOrder()->value('id') ?? \App\Models\Student::factory()->create()->id,
            'group_id' => \App\Models\Group::inRandomOrder()->value('id') ?? \App\Models\Group::factory()->create()->id,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'status' => $this->faker->randomElement(['active', 'completed', 'paused']),
        ];
    }
}

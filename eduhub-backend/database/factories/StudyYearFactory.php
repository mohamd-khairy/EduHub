<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudyYear>
 */
class StudyYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' =>  ' السنة الدراسية لعام ' . $this->faker->year() . ' الترم الاول',
            'start_date' => '2025-01-01',
            'end_date' => '2025-06-29',
            'status' => 1,
        ];
    }
}

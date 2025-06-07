<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamResult>
 */
class ExamResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $exam = \App\Models\Exam::inRandomOrder()->first();

        return [
            'student_id' => \App\Models\Student::inRandomOrder()->value('id'),
            'exam_id' => $exam->id,
            'score' => $this->faker->randomFloat(2, 0, $exam->total_marks ?? 100),
        ];
    }
}

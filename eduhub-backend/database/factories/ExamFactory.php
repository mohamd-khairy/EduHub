<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => \App\Models\Course::inRandomOrder()->value('id') ?? \App\Models\Course::factory()->create()->id,
            'title' => $this->faker->randomElement([
                'امتحان منتصف الفصل',
                'الاختبار النهائي',
                'اختبار الوحدة الأولى',
                'تقييم شهري'
            ]),
            'date' => $this->faker->dateTimeBetween('-2 weeks', '+2 weeks')->format('Y-m-d'),
            'total_marks' => $this->faker->numberBetween(20, 100),
        ];
    }
}

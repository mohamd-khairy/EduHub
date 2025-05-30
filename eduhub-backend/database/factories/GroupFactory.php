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
            'name' => $this->faker->randomElement([
                'رياضيات متقدمة',
                'الفيزياء الحديثة',
                'لغة عربية 2',
                'لغة إنجليزية 1',
                'كيمياء تطبيقية'
            ]),
            'teacher_id' => Teacher::inRandomOrder()->first()?->id ?? Teacher::factory(),
            'course_id' => Course::inRandomOrder()->first()?->id ?? Course::factory(),
            'schedule' => $this->faker->randomElement([
                'الأحد والثلاثاء 5:00 - 7:00 مساءً',
                'الإثنين والأربعاء 6:00 - 8:00 مساءً',
                'الجمعة 2:00 - 5:00 مساءً'
            ]),
            'max_students' => $this->faker->numberBetween(15, 50),
        ];
    }
}

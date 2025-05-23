<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
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
            'teacher_id' => 1, // يُفضل تغييره في Seeder لعلاقة ديناميكية مع TeacherFactory
            'group_name' => $this->faker->randomElement(['المجموعة أ', 'المجموعة ب', 'المجموعة ج']),
            'schedule' => $this->faker->randomElement([
                'الأحد والثلاثاء 5:00 - 7:00 مساءً',
                'الإثنين والأربعاء 6:00 - 8:00 مساءً',
                'الجمعة 2:00 - 5:00 مساءً'
            ]),
            'max_students' => $this->faker->numberBetween(15, 50),
        ];
    }
}

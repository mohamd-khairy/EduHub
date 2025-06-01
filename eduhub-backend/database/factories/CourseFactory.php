<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
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
            'description' => $this->faker->sentence()
        ];
    }
}

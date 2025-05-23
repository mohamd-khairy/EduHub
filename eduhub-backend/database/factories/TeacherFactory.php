<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'specialization' => $this->faker->randomElement([
                'رياضيات', 'علوم', 'لغة عربية', 'لغة إنجليزية', 'فيزياء', 'كيمياء'
            ]),
            'salary_type' => $this->faker->randomElement(['fixed', 'per_hour', 'percentage']),
            'salary_amount' => $this->faker->randomFloat(2, 1000, 5000), // قيمة بين 1000 و5000
        ];
    }
}

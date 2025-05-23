<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class StudentFactory extends Factory
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
            'gender' => $this->faker->randomElement(['ذكر', 'أنثى']),
            'grade_level' => $this->faker->randomElement(['الصف الأول', 'الصف الثاني', 'الصف الثالث']),
            'school_name' => $this->faker->company() . ' School',
            'parent_id' => \App\Models\ParentModel::inRandomOrder()->value('id') ?? \App\Models\ParentModel::factory()->create()->id,
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'image' => $this->faker->imageUrl(200, 200, 'people'),
        ];
    }
}

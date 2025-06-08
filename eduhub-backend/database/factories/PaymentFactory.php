<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'amount' => $this->faker->randomFloat(2, 100, 1000), // بين 100 و1000 ريال مثلاً
            'payment_date' => $this->faker->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'method' => $this->faker->randomElement(['كاش', 'تحويل بنكي', 'فيزا']),
            'status' => $this->faker->randomElement(['paid', 'pending', 'cancelled']),
            'note' => $this->faker->optional()->sentence(),
        ];
    }
}

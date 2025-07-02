<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => \App\Models\Group::inRandomOrder()->value('id'),
            'day' => [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday'
            ][rand(0, 6)],
            'start_time' => $this->faker->dateTimeBetween('-2 weeks', '+2 weeks')->format('H:i'),
            'end_time' => $this->faker->dateTimeBetween('-2 weeks', '+2 weeks')->format('H:i'),
            'room_id' => 1,
        ];
    }
}

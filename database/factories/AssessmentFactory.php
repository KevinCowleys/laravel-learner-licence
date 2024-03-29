<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 2,
            'number' => 1,
        ];
    }

    /**
     * Assessment One for light motor vehicle
     *
     * @return static
     */
    public function lightMotorVehicleTestOne(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 2,
                'number' => 1,
            ];
        });
    }

    /**
     * Sets the assessment as expired
     *
     * @return static
     */
    public function expired(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => Carbon::now()->subHour()
            ];
        });
    }
}

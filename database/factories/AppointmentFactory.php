<?php

namespace Database\Factories;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{

    protected $model = Appointment::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'       => \App\Models\User::factory(), // Creates a user automatically
            'title'         => $this->faker->sentence,
            'description'   => $this->faker->paragraph,
            'date_time'     => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'), // Future date
            'timezone'      => 'UTC',
            'guests'        => ['guest1@example.com', 'guest2@example.com'], // Example guest emails
            'reminder_time' => 30,
            'is_canceled'   => false,
        ];
    }
}

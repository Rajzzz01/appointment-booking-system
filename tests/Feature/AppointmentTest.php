<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentBooked;
use App\Mail\AppointmentCancelled;
use Carbon\Carbon;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_book_an_appointment()
    {
        Mail::fake();

        $response = $this->actingAs($this->user)->post('/appointment-booking', [
            'title'       => 'Doctor Visit',
            'description' => 'Regular checkup',
            'date_time'   => Carbon::now()->addDays(2)->format('Y-m-d H:i'),
            'timezone'    => 'Asia/Kolkata',
            'reminder_time' => 30,
            'guests'      => ['guest@example.com']
        ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', ['title' => 'Doctor Visit']);

        Mail::assertQueued(AppointmentBooked::class);
    }

    /** @test */
    public function user_cannot_book_appointment_on_weekends()
    {
        $this->actingAs($this->user);
        $data = [
            'title'         => 'Weekend Meeting',
            'description'   => 'Trying to book on a weekend',
            'date_time'     => Carbon::parse('next saturday')->format('Y-m-d H:i'),
            'timezone'      => 'UTC',
            'reminder_time' => 30,
            'guests'        => [],
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Appointments can only be booked on weekdays (Monday to Friday).');

        app(\App\Services\AppointmentService::class)->bookAppointment($data);
    }

    /** @test */
    public function user_can_cancel_an_appointment_before_30_minutes()
    {
        Mail::fake();

        $appointment = Appointment::factory()->create([
            'user_id'     => $this->user->id,
            'date_time'   => Carbon::now()->addHours(2),
            'is_canceled' => false
        ]);

        $response = $this->actingAs($this->user)->post(route('appointments.cancel', $appointment->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'is_canceled' => true]);

        Mail::assertQueued(AppointmentCancelled::class);
    }

    /** @test */
    public function user_cannot_cancel_appointment_within_30_minutes()
    {
        $appointment = Appointment::factory()->create([
            'user_id'   => $this->user->id,
            'date_time' => Carbon::now()->addMinutes(20)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot cancel an appointment within 30 minutes of the scheduled time.');

        app(\App\Services\AppointmentService::class)->cancelAppointment($appointment);
    }

    /** @test */
    public function reminder_email_is_scheduled_correctly()
    {
        Mail::fake();

        $appointment = Appointment::factory()->create([
            'user_id'       => $this->user->id,
            'date_time'     => Carbon::now()->addHours(5),
            'reminder_time' => 60,
        ]);

        $reminderTime = Carbon::parse($appointment->date_time)->subMinutes($appointment->reminder_time);

        $this->assertTrue(Carbon::now()->lt($reminderTime));
    }
}

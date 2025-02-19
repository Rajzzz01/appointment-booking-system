<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $appointment;

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->appointment->user->email)->send(new AppointmentReminder($this->appointment));

        foreach ($this->appointment->guests as $guestEmail) {
            Mail::to($guestEmail)->send(new AppointmentReminder($this->appointment));
        }
    }
}

<?php

namespace App\Services;

use App\Jobs\SendAppointmentReminder;
use App\Mail\AppointmentBooked;
use App\Mail\AppointmentCancelled;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendAppointmentBookedEmails($appointment)
    {
        $reminderTime = $appointment['date_time']->subMinutes($appointment->reminder_time);
        dispatch(new SendAppointmentReminder($appointment))->delay($reminderTime);

        Mail::to($appointment->user->email)->queue(new AppointmentBooked($appointment));
        foreach ($appointment->guests as $guestEmail) {
            Mail::to($guestEmail)->queue(new AppointmentBooked($appointment));
        }
    }

    public function sendAppointmentCancellationEmails($appointment)
    {
        Mail::to($appointment->user->email)->queue(new AppointmentCancelled($appointment));
        foreach ($appointment->guests as $guestEmail) {
            Mail::to($guestEmail)->queue(new AppointmentCancelled($appointment));
        }
    }
}

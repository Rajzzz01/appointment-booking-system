<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentService
{
    public function validateAppointmentDate($date, $timezone)
    {
        $appointmentDate = Carbon::parse($date, $timezone)->setTimezone('UTC');

        if ($appointmentDate->isWeekend()) {
            throw new \Exception('Appointments can only be booked on weekdays (Monday to Friday).');
        }

        return $appointmentDate;
    }

    public function bookAppointment($data)
    {
        $appointmentDate = $this->validateAppointmentDate($data['date_time'], $data['timezone']);

        $appointment = Appointment::create([
            'user_id'       => Auth::id(),
            'title'         => $data['title'],
            'description'   => $data['description'],
            'date_time'     => $appointmentDate,
            'timezone'      => $data['timezone'],
            'guests'        => $data['guests'],
            'reminder_time' => $data['reminder_time'] ?? 30,
        ]);

        return $appointment;
    }

    public function cancelAppointment($appointment)
    {
        if (Carbon::now()->diffInMinutes($appointment->date_time, false) < 30) {
            throw new \Exception('Cannot cancel an appointment within 30 minutes of the scheduled time.');
        }

        $appointment->update(['is_canceled' => true]);

        return $appointment;
    }
}

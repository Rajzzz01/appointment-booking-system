<?php

namespace App\Mail;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AppointmentBooked extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $appointment;
    public $appointmentData;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment->load('user');
        if (!$this->appointment || !$this->appointment->user) {
            Log::error('Appointment or User not found in AppointmentBooked Mailable.', ['appointment' => $appointment]);
            return;
        }

        $appointmentTime = Carbon::parse($this->appointment->date_time, 'UTC')
            ->setTimezone($this->appointment->timezone)
            ->format('d M Y, h:i A');

        $this->appointmentData = [
            'name'       => $this->appointment->user->name,
            'title'      => $this->appointment->title,
            'description' => $this->appointment->description,
            'date_time'  => $appointmentTime,
            'timezone'   => $this->appointment->timezone,
            'guests'     => $this->appointment->guests ?? [],
        ];
        Log::info('Appointment Details for Email:', $this->appointmentData);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Confirmation'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment_booked',
            with: $this->appointmentData
        );
    }
}

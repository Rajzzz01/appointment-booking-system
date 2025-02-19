<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;

class AppointmentController extends Controller
{
    protected $appointmentService;
    protected $emailService;

    public function __construct(AppointmentService $appointmentService, EmailService $emailService)
    {
        $this->appointmentService = $appointmentService;
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $sortBy = $request->query('sort', 'created_at');
        $sortOrder = $sortBy === 'date_time' ? 'asc' : 'desc';

        $appointments = Appointment::where('user_id', $user->id)
            ->orderBy($sortBy, $sortOrder)
            ->get();

        return Inertia::render('Appointment/Appointments', [
            'appointments' => $appointments,
            'sortBy' => $sortBy
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'date_time'     => 'required|date|after:now',
            'reminder_time' => 'integer|min:5|max:60',
            'timezone'      => 'required|string',
            'guests'        => 'array',
            'guests.*'      => 'email',
        ]);

        try {
            $appointment = $this->appointmentService->bookAppointment($request->all());
            $this->emailService->sendAppointmentBookedEmails($appointment);
            return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()], 422);
        }
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);

        try {
            $this->appointmentService->cancelAppointment($appointment);
            $this->emailService->sendAppointmentCancellationEmails($appointment);
            return redirect()->back()->with('success', 'Appointment canceled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

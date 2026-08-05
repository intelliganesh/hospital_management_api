<?php

namespace App\Mail\Appointment\Patient;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ReSchedule extends Mailable
{


    public $details;
    public $appointmentNumber;
    public $newAppointmentDateTime;

    /**
     * Summary of __construct
     * @param mixed $details
     * @param mixed $appointmentNumber
     * @param mixed $newAppointmentDateTime
     */
    public function __construct($details, $appointmentNumber, $newAppointmentDateTime)
    {
        $this->details = $details;
        $this->appointmentNumber = $appointmentNumber;
        $this->newAppointmentDateTime = $newAppointmentDateTime;
    }

    /**
     * Summary of envelope
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ReSchedule Appointment',
        );
    }

    /**
     * Summary of content
     * @return Content
     */
    public function content(): Content
    {

        return new Content(
            view: 'templates.appointments.patient.updated',
            with: [
                "doctorName" => $this->details->doctor_name,
                "patientName" => $this->details->patient_name,
                "appointmentNumber" => $this->appointmentNumber,
                "newAppointmentDateTime" => $this->newAppointmentDateTime,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

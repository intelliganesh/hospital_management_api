<?php

namespace App\Mail\Appointment\Doctor;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class Schedule extends Mailable
{


    public $details;
    public $appointmentNumber;
    public $appointmentDateTime;


    /**
     * Summary of __construct
     * @param mixed $details
     * @param mixed $appointmentNumber
     * @param mixed $appointmentDateTime
     */
    public function __construct($details, $appointmentNumber, $appointmentDateTime)
    {
        $this->details = $details;
        $this->appointmentNumber = $appointmentNumber;
        $this->appointmentDateTime = $appointmentDateTime;
    }

    /**
     * Summary of envelope
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Schedule Appointment',
        );
    }

    /**
     * Summary of content
     * @return Content
     */
    public function content(): Content
    {

        return new Content(
            view: 'templates.appointments.doctor.created',
            with: [
                "doctorName" => $this->details->doctor_name,
                "patientName" => $this->details->patient_name,
                "appointmentNumber" => $this->appointmentNumber,
                "appointmentDateTime" => $this->appointmentDateTime,
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

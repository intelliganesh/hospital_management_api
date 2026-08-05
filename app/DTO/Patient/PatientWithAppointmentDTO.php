<?php

namespace App\DTO\Patient;

class PatientWithAppointmentDTO
{
    public string $id;
    public string $email;
    public string $doctorId;
    public string $patientName;
    public string $prescription;
    public string $specialization;
    public function __construct(string $id, string $email, string $patientName, string $doctorId, string $prescription, string $specialization)
    {
        $this->id = $id;
        $this->email = $email;
        $this->doctorId = $doctorId;
        $this->patientName = $patientName;
        $this->prescription = $prescription;
        $this->specialization = $specialization;
    }

}
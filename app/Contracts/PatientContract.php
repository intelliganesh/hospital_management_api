<?php

namespace App\Contracts;

use App\DTO\Patient\PatientWithAppointmentDTO;

interface PatientContract
{
    public function register(array $data): bool;
    public function getPatientById(int $id): ?array;
    public function updatePatient(int $id, array $data): ?PatientWithAppointmentDTO;
}
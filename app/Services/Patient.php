<?php

namespace App\Services;

use App\Contracts\PatientContract;
use App\DTO\Patient\PatientWithAppointmentDTO;
// use App\Models\Patient as PatientModel;

class Patient implements PatientContract
{
    public function register(array $data): bool
    {
        return true;
        // return PatientModel::create($data) ? true : false;
    }

    public function getPatientById(int $id): ?array
    {
        // $patient = PatientModel::find($id);
        // return $patient ? $patient->toArray() : null;
    }

    public function updatePatient(int $id, array $data): ?PatientWithAppointmentDTO
    {
        $patient = [
            "id" => $id,
            'email' => $data['email'],
            'doctorId' => $data['doctorId'],
            'doctorName' => $data['doctorName'],
            'patientName' => $data['patientName'],
            'prescription' => $data['prescription'],
            'specialization' => $data['specialization'],
        ];
        return new PatientWithAppointmentDTO(
            $patient['id'],
            $patient['email'],
            $patient['doctorId'],
            $patient['patientName'],
            $patient['prescription'],
            $patient['specialization']
        );
        // $patient = PatientModel::find($id);
        // if (!$patient) {
        //     return false;
        // }
        // return $patient->update($data);
    }
}

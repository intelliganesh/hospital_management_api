<?php

namespace App\Services\Shared;

use App\Enums\RemovedEnums;
use App\Models\Vital;
use App\Models\Invoice;
use App\Models\Allopathy;
// use App\Models\Payment;
use App\Models\Proctology;
use App\Models\Examination;
use App\Models\Appointments;
use App\Models\NonProctology;
use App\Models\Consultations;
use App\Attributes\Transactional;
use App\Enums\Consultation\TypeEnum;

class AppointmentHelperService
{
    /**
     * Summary of getDoctorAndPatientAndFrontDesk
     * @param mixed $appointment
     * @return array{appointment_number: mixed, doctor_email: mixed, doctor_name: mixed, doctor_phone: mixed, front_desk_user_email: mixed, front_desk_user_name: mixed, front_desk_user_phone: mixed, patient_email: mixed, patient_name: mixed, patient_number: mixed, patient_phone: mixed}
     */
    public function getDoctorAndPatientAndFrontDesk($appointment)
    {
        return [
            // 'doctor_name' => $appointment->doctor_name ?? '',
            // 'doctor_email' => $appointment->doctor_email ?? '',
            // 'doctor_phone' => $appointment->doctor_phone ?? '',

            'patient_name' => $appointment->patient_name ?? '',
            'patient_phone' => $appointment->patient_phone ?? '',
            'patient_email' => $appointment->patient_email ?? '',
            'patient_number' => $appointment->patient_number ?? '',

            'appointment_number' => $appointment->appointment_number ?? '',

            // 'front_desk_user_name' => $appointment->front_desk_user_name ?? '',
            // 'front_desk_user_email' => $appointment->front_desk_user_email ?? '',
            // 'front_desk_user_phone' => $appointment->front_desk_user_phone ?? '',


            // 'referred_by_name' => $appointment->referred_by_name ?? '',
            // 'referred_by_email' => $appointment->referred_by_email ?? '',
            // 'referred_by_phone_no' => $appointment->referred_by_phone_no ?? '',
            // 'referred_by_hospital_name' => $appointment->referred_by_hospital_name ?? '',
        ];
    }

    /**
     * Summary of getAppointmentRequiredData
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return array{appointment_number: mixed, doctor_email: mixed, doctor_name: mixed, doctor_phone: mixed, front_desk_user_email: mixed, front_desk_user_name: mixed, front_desk_user_phone: mixed, patient_email: mixed, patient_name: mixed, patient_number: mixed, patient_phone: mixed}
     */
    public function getAppointmentRequiredData(string $id)
    {
        $appointmentData = Appointments::findOrFail($id);
        return $this->getDoctorAndPatientAndFrontDesk($appointmentData);
    }

    /**
     * Summary of updateFieldDynamicAndReturn
     * @param string $id
     * @param array $data
     * @return array{appointment_number: mixed, doctor_email: mixed, doctor_name: mixed, doctor_phone: mixed, front_desk_user_email: mixed, front_desk_user_name: mixed, front_desk_user_phone: mixed, patient_email: mixed, patient_name: mixed, patient_number: mixed, patient_phone: mixed}
     */
    public function updateFieldDynamicAndReturn(string $id, array $data)
    {
        $appointment = Appointments::findOrFail($id);
        $appointment->update($data);
        return $this->getDoctorAndPatientAndFrontDesk($appointment);
    }

    /**
     * Summary of deleteAppointmentRelatedData
     * @param string $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Delete appointments and related record within a secure transaction')]
    public function deleteAppointmentRelatedData(string $id)
    {
        $appointment = Appointments::findOrFail($id);
        // Payment::where('appointment_id', $id)->delete();
        $consultation = Consultations::where('appointment_id', $id)->first();
        if ($consultation) {
            if ($consultation->type === TypeEnum::NonProctology->value) {
                // NonProctology::where('consultation_id', $consultation->id)->delete();
                NonProctology::where('consultation_id', $consultation->id)->update(['removed' => RemovedEnums::Removed->value]);
            } else if ($consultation->type === TypeEnum::Proctology->value) {
                // Proctology::where('consultation_id', $consultation->id)->delete();
                Proctology::where('consultation_id', $consultation->id)->update(['removed' => RemovedEnums::Removed->value]);
            } else if ($consultation->type === TypeEnum::Allopathy->value) {
                // Allopathy::where('consultation_id', $consultation->id)->delete();
                Allopathy::where('consultation_id', $consultation->id)->update(['removed' => RemovedEnums::Removed->value]);
            }
            // Vital::where('consultation_id', $consultation->id)->delete();
            Vital::where('consultation_id', $consultation->id)->update(['removed' => RemovedEnums::Removed->value]);
            // Invoice::where('consultation_id', $consultation->id)->delete();
            Invoice::where('consultation_id', $consultation->id)->update(['removed' => RemovedEnums::Removed->value]);
            // $consultation->delete();
            $consultation->update(['removed' => RemovedEnums::Removed->value]);
        }
        $examination = Examination::where('appointment_id', $id);
        if ($examination->exists()) {
            // $examination->delete();
            $examination->update(['removed' => RemovedEnums::Removed->value]);
        }
        $appointment->delete();
    }

}
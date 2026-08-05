<?php

namespace App\Services\Shared;

use App\Models\Vital;
use App\Models\Payment;
use App\Models\Allopathy;
use App\Models\Proctology;
use App\Models\Examination;
use App\Models\Appointments;
use App\Models\Consultations;
use App\Models\NonProctology;
use App\Attributes\Transactional;
use App\Enums\Consultation\TypeEnum;

class ConsultationAppointmentsHelperService
{

    /**
     * Summary of delete
     * @param string $id
     * @param mixed $columnName
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Delete consultations and related record within a secure transaction')]
    public function delete(string $id, ?string $columnName = 'id')
    {
        $consultation = Consultations::where($columnName, $id)->first();
        if (!empty($consultation)) {
            $appointment = Appointments::where('id', $consultation->appointment_id)->first();
            if (!empty($appointment)) {
                $appointment->appointment_fees = $consultation->fees;
                $appointment->save();
            }
        }

        $paymentData = Payment::where('consultation_id', $consultation->id)->first();
        $paymentData->amount = $consultation->fees;
        $paymentData->save();

        if ($consultation) {
            if ($consultation->type === TypeEnum::NonProctology->value) {
                NonProctology::where('consultation_id', $consultation->id)->delete();
            } else if ($consultation->type === TypeEnum::Proctology->value) {
                Proctology::where('consultation_id', $consultation->id)->delete();
            } else if ($consultation->type === TypeEnum::Allopathy->value) {
                Allopathy::where('consultation_id', $consultation->id)->delete();
            }
            Vital::where('consultation_id', $consultation->id)->delete();
            Examination::where('consultation_id', $consultation->id)->delete();
            $consultation->delete();
        }
    }
}
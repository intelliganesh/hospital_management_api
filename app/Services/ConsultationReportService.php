<?php

namespace App\Services;

use Mpdf\Mpdf;
use App\Models\Proctology;
use App\Models\NonProctology;
use App\Models\Consultations;
// use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\Consultation\TypeEnum;

class ConsultationReportService
{

    private $invoiceService;
    private $systemSettingsService;

    /**
     * Summary of __construct
     * @param \App\Services\InvoiceService $invoiceService
     * @param \App\Services\SystemSettingsService $systemSettingsService
     */
    public function __construct(SystemSettingsService $systemSettingsService, InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
        $this->systemSettingsService = $systemSettingsService;
    }

    /**
     * Summary of generateConsultationReport
     * @param string $id
     * @return string
     */
    public function generateConsultationReport(string $id)
    {
        return $this->invoiceService->getConsultationDownload($id);
        // return $this->invoiceService->getConsultationDownloadTest($id);
        // return $this->getConsultationData($id, 'templates.downloads.generate-consultation');
        // return $this->getConsultationData($id, 'templates.downloads.consultation-test');
    }


    /**
     * Summary of downloadPrescription
     * @param string $id
     * @return string
     */
    public function downloadPrescription(string $id)
    {
        return $this->invoiceService->getPrescriptionDownload($id);
        // return $this->getConsultationData($id, 'templates.downloads.consultation-prescription');
        // return $this->getConsultationData($id, 'templates.downloads.prescription-for-consultation');
    }


    /**
     * Summary of getConsultationData
     * @param string $id
     * @return string
     */
    private function getConsultationData(string $id, string $view)
    {
        // Sample data for the consultation report
        $settings = $this->systemSettingsService->all();
        $data = Consultations::where('id', $id)->first();
        $proctologyOrNonProctology = null;
        if ($data->type == TypeEnum::Proctology->value) {
            $proctologyOrNonProctology = Proctology::where('consultation_id', $data->id)->first();
        } else if ($data->type == TypeEnum::NonProctology->value) {
            $proctologyOrNonProctology = NonProctology::where('consultation_id', $data->id)->first();
        }

        $data = [
            'type' => $data->type,
            'advice' => $data->advice,
            'status' => $data->status,
            'complaint' => $data->complaint,
            'preliminary_diagnosis' => $data->preliminary_diagnosis,
            'appointment_id' => $data->appointment_id,
            'payment_status' => $data->payment_status,
            'next_visit_date' => $data->next_visit_date,
            'appointment_number' => $data->appointment_number,
            'patient_name' => $data->patient_name,
            'patient_email' => $data->patient_email,
            'patient_phone' => $data->patient_phone,
            'patient_number' => $data->patient_number,
            'doctor_name' => $data->doctor_name,
            'doctor_email' => $data->doctor_email,
            'doctor_phone' => $data->doctor_phone,
            'front_desk_user_name' => $data->front_desk_user_name,
            'front_desk_user_email' => $data->front_desk_user_email,
            'front_desk_user_phone' => $data->front_desk_user_phone,
            'referred_by_name' => $data->referred_by_name,
            'referred_by_email' => $data->referred_by_email,
            'referred_by_phone_no' => $data->referred_by_phone_no,
            'referred_by_hospital_name' => $data->referred_by_hospital_name,
            'settings' => $settings,
            'proctology' => $proctologyOrNonProctology instanceof Proctology ? $proctologyOrNonProctology : null,
            'nonProctology' => $proctologyOrNonProctology instanceof NonProctology ? $proctologyOrNonProctology : null,
        ];
        $html = view($view, $data)->render();
        $mpdf = new Mpdf([
            'default_font' => 'NotoSansKannada', // recommended for Kannada
        ]);
        $mpdf->WriteHTML($html);
        return $mpdf->Output('', 'S');

        // $pdf = Pdf::loadView($view, [
        //     'type' => $data->type,
        //     'advice' => $data->advice,
        //     'status' => $data->status,
        //     'complaint' => $data->complaint,
        //     'preliminary_diagnosis' => $data->preliminary_diagnosis,
        //     'appointment_id' => $data->appointment_id,
        //     'payment_status' => $data->payment_status,
        //     'next_visit_date' => $data->next_visit_date,
        //     'appointment_number' => $data->appointment_number,
        //     'patient_name' => $data->patient_name,
        //     'patient_email' => $data->patient_email,
        //     'patient_phone' => $data->patient_phone,
        //     'patient_number' => $data->patient_number,
        //     'doctor_name' => $data->doctor_name,
        //     'doctor_email' => $data->doctor_email,
        //     'doctor_phone' => $data->doctor_phone,
        //     'front_desk_user_name' => $data->front_desk_user_name,
        //     'front_desk_user_email' => $data->front_desk_user_email,
        //     'front_desk_user_phone' => $data->front_desk_user_phone,
        //     'referred_by_name' => $data->referred_by_name,
        //     'referred_by_email' => $data->referred_by_email,
        //     'referred_by_phone_no' => $data->referred_by_phone_no,
        //     'referred_by_hospital_name' => $data->referred_by_hospital_name,
        //     'settings' => $settings,
        //     'proctology' => $proctologyOrNonProctology instanceof Proctology ? $proctologyOrNonProctology : null,
        //     'nonProctology' => $proctologyOrNonProctology instanceof NonProctology ? $proctologyOrNonProctology : null,
        // ]);
        // Return the generated PDF as a response
        // return $pdf->download('consultation_report.pdf');

        // return $pdf->output();
    }
}
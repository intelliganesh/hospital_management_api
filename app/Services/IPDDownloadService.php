<?php
namespace App\Services;

use App\Models\Invoice;
use App\Models\IPD;
use App\Models\IpdDocument;
use App\Models\IPDInvoiceItem;
use App\Models\Patient;
use App\Models\Receipt;
use App\Models\SystemSettings;
use Exception;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class IPDDownloadService
{
    /**
     * Generate PDF for various IPD documents
     */
    public function generatePdf(string $ipdId, string $type, string $ipd_surgery_id = null)
    {
        try {
            $ipd = IPD::with('patient', 'consultation', 'staffs', 'preliminaryNotes')->findOrFail($ipdId);
            $ipd->patient->proofData = $ipd->patient->proofData();
            $htmlContent = '';
            $fileName    = '';

            $system = SystemSettings::first();

            $footer_content        = '';
            $letter_header_address = '';
            if (! empty($system)) {
                $letter_header_address = $system->billing_letter_header;
                $footer_content        = $system->footer_content ?? '';
            }

            // Decode HTML entities so they render correctly
            $footerHtml = html_entity_decode($footer_content);
            $headerHtml = view('templates.downloads.IPD.header', compact('letter_header_address'))->render();

            switch ($type) {
                case 'preliminary_notes':
                    $fileName    = 'preliminary_notes_' . $ipd->ipd_number . '.pdf';
                    $htmlContent = view('templates.downloads.IPD.preliminary_notes', compact('ipd'))->render();
                    break;

                case 'doctor_notes':
                    $fileName          = 'doctor_notes_' . $ipd->ipd_number . '.pdf';
                    $doctor_notes      = $ipd->doctorNotes()->orderBy('datetime', 'asc')->get();
                    $ipd->doctor_notes = $doctor_notes;
                    $htmlContent       = view('templates.downloads.IPD.doctor_notes', compact('ipd'))->render();
                    break;

                case 'nurse_notes':
                    $fileName         = 'nurse_notes_' . $ipd->ipd_number . '.pdf';
                    $nurse_notes      = $ipd->nurseNotes()->orderBy('datetime', 'asc')->get();
                    $ipd->nurse_notes = $nurse_notes;
                    $htmlContent      = view('templates.downloads.IPD.nurse_notes', compact('ipd'))->render();
                    break;

                case 'anaesthesia_consent_form':
                    $surgery_report      = $ipd->surgery?->where('id', $ipd_surgery_id)->first();
                    $anaesthesia_report  = $ipd->anaesthesia?->where('ipd_surgery_id', $ipd_surgery_id)->first();
                    $ipd->surgery_report = $surgery_report;
                    $ipd->anaesthesia    = $anaesthesia_report;
                    $fileName            = 'anaesthesia_consent_form_' . $ipd->ipd_number . '_' . str_replace(["-", " ", ","], "_", $surgery_report->surgery_name) . '.pdf';
                    $htmlContent         = view('templates.downloads.IPD.anaesthesia_consent_form', compact('ipd'))->render();
                    break;

                case 'pre_anaesthesia_assessment':
                    $surgery_report                                  = $ipd->surgery?->where('id', $ipd_surgery_id)->first();
                    $anaesthesia_report                              = $ipd->anaesthesia?->where('ipd_surgery_id', $ipd_surgery_id)->first();
                    $ipd->surgery_report                             = $surgery_report;
                    $ipd->anaesthesia                                = $anaesthesia_report;
                    $ipd->anaesthesia_pre_operative_evaluation_chart = $ipd->preOperativeAnaesthesiaEvaluation?->where('ipd_surgery_id', $ipd_surgery_id)->first();
                    $fileName                                        = 'pre_anaesthesia_assessment_' . $ipd->ipd_number . '_' . str_replace(["-", " ", ","], "_", $surgery_report->surgery_name) . '.pdf';
                    $htmlContent                                     = view('templates.downloads.IPD.pre_operative_anaesthesia_evaluation_chart', compact('ipd'))->render();
                    break;

                case 'department_of_anaesthesia':
                    $surgery_report             = $ipd->surgery?->where('id', $ipd_surgery_id)->first();
                    $anaesthesia_report         = $ipd->anaesthesia?->where('ipd_surgery_id', $ipd_surgery_id)->first();
                    $ipd->surgery_report        = $surgery_report;
                    $ipd->anaesthesia           = $anaesthesia_report;
                    $ipd->anaesthesiaDepartment = $ipd->anaesthesiaDepartment?->where('ipd_surgery_id', $ipd_surgery_id)->first();
                    $fileName                   = 'department_of_anaesthesia_' . $ipd->ipd_number . '_' . str_replace(["-", " ", ","], "_", $surgery_report->surgery_name) . '.pdf';
                    $htmlContent                = view('templates.downloads.IPD.department_of_anaesthesia', compact('ipd'))->render();
                    break;

                case 'anaesthesia_recovery_room_observation':
                    $surgery_report           = $ipd->surgery?->where('id', $ipd_surgery_id)->first();
                    $anaesthesia_report       = $ipd->anaesthesia?->where('ipd_surgery_id', $ipd_surgery_id)->first();
                    $ipd->surgery_report      = $surgery_report;
                    $ipd->anaesthesia         = $anaesthesia_report;
                    $ipd->recoveryObservation = $ipd->recoveryObservation?->where('ipd_surgery_id', $ipd_surgery_id)->first();
                    $fileName                 = 'anaesthesia_recovery_room_observation_' . $ipd->ipd_number . '_' . str_replace(["-", " ", ","], "_", $surgery_report->surgery_name) . '.pdf';
                    $htmlContent              = view('templates.downloads.IPD.anaesthesia_recovery_room_observation', compact('ipd'))->render();
                    break;

                case 'anaesthesia_record':
                    $surgery_report      = $ipd->surgery?->where('id', $ipd_surgery_id)->first();
                    $anaesthesia_report  = $ipd->anaesthesia?->where('ipd_surgery_id', $ipd_surgery_id)->first();
                    $ipd->surgery_report = $surgery_report;
                    $ipd->anaesthesia    = $anaesthesia_report;
                    $fileName            = 'anaesthesia_record_' . $ipd->ipd_number . '_' . str_replace(["-", " ", ","], "_", $surgery_report->surgery_name) . '.pdf';
                    $htmlContent         = view('templates.downloads.IPD.anaesthesia_record', compact('ipd'))->render();
                    break;

                case 'surgery_consent_form':
                    $surgery_report       = $ipd->surgery?->where('id', $ipd_surgery_id)->first();
                    $ipd->surgery_report  = $surgery_report;
                    $ipd->final_diagnosis = $ipd->preliminaryNotes?->where('ipd_id', $ipdId)->first()?->final_diagnosis;
                    $fileName             = 'surgery_consent_form_' . $ipd->ipd_number . '_' . str_replace(["-", " ", ","], "_", $surgery_report->surgery_name) . '.pdf';
                    $htmlContent          = view('templates.downloads.IPD.surgery_consent_form', compact('ipd'))->render();
                    break;

                case 'surgery_report':
                    $surgery_report      = $ipd->surgery?->where('id', $ipd_surgery_id)->first();
                    $ipd->surgery_report = $surgery_report;
                    $fileName            = 'surgery_report_' . $ipd->ipd_number . '_' . str_replace(["-", " ", ","], "_", $surgery_report->surgery_name) . '.pdf';
                    $htmlContent         = view('templates.downloads.IPD.surgery_report', compact('ipd'))->render();
                    break;

                case 'pre_operative_checklist':
                    $surgery_report             = $ipd->surgery?->where('id', $ipd_surgery_id)->first();
                    $preOperativeChecklist      = $ipd->preOperativeChecklist?->where('ipd_surgery_id', $ipd_surgery_id)->first();
                    $ipd->preOperativeChecklist = $preOperativeChecklist;
                    $fileName                   = 'pre_operative_checklist_' . $ipd->ipd_number . '_' . str_replace(["-", " ", ","], "_", $surgery_report->surgery_name) . '.pdf';
                    $htmlContent                = view('templates.downloads.IPD.pre_operative_checklist', compact('ipd'))->render();
                    break;

                case 'discharge_summary':
                    $ipd->discharge_summary = $ipd->dischargeSummaryReport;
                    $fileName               = 'discharge_summary_' . $ipd->ipd_number . '.pdf';
                    $htmlContent            = view('templates.downloads.IPD.discharge_summary', compact('ipd'))->render();
                    break;

                case 'billing_invoice':
                    $ipd->discharge_summary = $ipd->dischargeSummaryReport;
                    $bill                   = $this->billingInvoiceData($ipd->id);
                    $fileName               = 'billing_invoice_' . $ipd->ipd_number . '.pdf';
                    $htmlContent            = view('templates.downloads.IPD.billing_invoice', compact('ipd', 'bill'))->render();
                    break;

                default:
                    throw new Exception('Invalid document type: ' . $type);
            }

            $filePath = storage_path("app/public/pdfs/ipd/{$ipd->ipd_number}/{$fileName}");

            // Ensure the directory exists
            if (! Storage::disk('public')->exists("pdfs/ipd/{$ipd->ipd_number}")) {
                Storage::disk('public')->makeDirectory("pdfs/ipd/{$ipd->ipd_number}");
            }

            $footerHtml = '
                <div style="
                    width:100%;
                    font-size:10px;
                    text-align:center;
                    padding-top:5px;
                ">
                    Page <span class="pageNumber"></span> of <span class="totalPages"></span>
                </div>
                ';

            // Generate and save the PDF using Browsershot
            Browsershot::html($htmlContent)
                ->format('A4')
                ->margins(35, 10, 10, 10)
                ->noSandbox()
                ->waitUntilNetworkIdle()
                ->showBrowserHeaderAndFooter()
                ->headerHtml($headerHtml)
                ->footerHtml($footerHtml)
            // ->setOption('margin-left', '10')
            // ->setOption('margin-right', '10')
            // ->setOption('margin-top', '10')
            // ->setOption('margin-bottom', '30')  // 🔥 remove wkhtml default bottom margin
            // ->setOption('footer-spacing', '0') // 🔥 remove spacing between body & footer
            // ->setOption('printBackground', true)
                ->savePdf($filePath);

            // Store only the relative path
            $relativePath = "pdfs/ipd/{$ipd->ipd_number}/{$fileName}";

            IpdDocument::updateOrCreate([
                'ipd_id'         => $ipd->id,
                'ipd_surgery_id' => $ipd_surgery_id,
                'document_type'  => $type,
                'document_path'  => $relativePath,
            ]);

            // Return the public URL
            return asset("storage/{$relativePath}");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function downloadPdf(string $ipdId, string $type, string $ipd_surgery_id = null)
    {
        if ($ipd_surgery_id) {
            $ipdDocument = IpdDocument::where('ipd_id', $ipdId)
                ->where('ipd_surgery_id', $ipd_surgery_id)
                ->where('document_type', $type)
                ->get();
        } else {
            $ipdDocument = IpdDocument::where('ipd_id', $ipdId)->where('document_type', $type)->get();
        }
        if (count($ipdDocument) > 0) {
            if (count($ipdDocument) == 1) {
                return asset("storage/{$ipdDocument[0]->document_path}");
            } else {
                $path = [];
                foreach ($ipdDocument as $document) {
                    $path[] = asset("storage/{$document->document_path}");
                }
                return $path;
            }
        }
        return null;
    }

    public function downloadprefilledUploadPdf(string $ipdId, ?string $type = 'all')
    {
        $type       = $type ?: 'all';
        $ipd        = IPD::with('patient', 'surgery')->findOrFail($ipdId);
        $surgeryIds = $ipd->surgery->pluck('id');

        $anaesthesiaBySurgery = \App\Models\IPDAnaesthesia::where('ipd_id', $ipdId)
            ->whereIn('ipd_surgery_id', $surgeryIds)
            ->get()
            ->keyBy('ipd_surgery_id');
        $preAnaesthesiaBySurgery = \App\Models\IPDPreOperativeAnaesthesiaEvaluation::where('ipd_id', $ipdId)
            ->whereIn('ipd_surgery_id', $surgeryIds)
            ->get()
            ->keyBy('ipd_surgery_id');
        $anaesthesiaDepartmentBySurgery = \App\Models\IPDAnaesthesiaDepartment::where('ipd_id', $ipdId)
            ->whereIn('ipd_surgery_id', $surgeryIds)
            ->get()
            ->keyBy('ipd_surgery_id');
        $recoveryObservationBySurgery = \App\Models\IPDAnaesthesiaRecoverObservation::where('ipd_id', $ipdId)
            ->whereIn('ipd_surgery_id', $surgeryIds)
            ->get()
            ->keyBy('ipd_surgery_id');
        $preOperativeChecklistBySurgery = \App\Models\IPDPreOperativeChecklist::where('ipd_id', $ipdId)
            ->whereIn('ipd_surgery_id', $surgeryIds)
            ->get()
            ->keyBy('ipd_surgery_id');

        $data = [];
        foreach ($ipd->surgery as $surgery) {
            $anaesthesia           = $anaesthesiaBySurgery->get($surgery->id);
            $preAnaesthesia        = $preAnaesthesiaBySurgery->get($surgery->id);
            $anaesthesiaDepartment = $anaesthesiaDepartmentBySurgery->get($surgery->id);
            $recoveryObservation   = $recoveryObservationBySurgery->get($surgery->id);
            $preOperativeChecklist = $preOperativeChecklistBySurgery->get($surgery->id);

            $data[] = [
                'type'    => 'anaesthesia_consent_form',
                'label'   => 'Anaesthesia Consent Form - ' . $surgery->surgery_name,
                'content' => $anaesthesia?->uploaded_consent_path,
            ];
            $data[] = [
                'type'    => 'pre_anaesthesia_assessment',
                'label'   => 'Pre-Anaesthesia Assessment - ' . $surgery->surgery_name,
                'content' => $preAnaesthesia?->upload_pdf_path,
            ];
            $data[] = [
                'type'    => 'department_of_anaesthesia',
                'label'   => 'Department of Anaesthesia - ' . $surgery->surgery_name,
                'content' => $anaesthesiaDepartment?->upload_pdf_path,
            ];
            $data[] = [
                'type'    => 'anaesthesia_recovery_room_observation',
                'label'   => 'Anaesthesia Recovery Room Observation - ' . $surgery->surgery_name,
                'content' => $recoveryObservation?->upload_pdf_path,
            ];
            $data[] = [
                'type'    => 'anaesthesia_record',
                'label'   => 'Anaesthesia Record - ' . $surgery->surgery_name,
                'content' => $anaesthesia?->upload_anaesthesia_record_path,
            ];
            $data[] = [
                'type'    => 'surgery_consent_form',
                'label'   => 'Surgery Consent Form - ' . $surgery->surgery_name,
                'content' => $surgery->uploaded_consent_path,
            ];
            $data[] = [
                'type'    => 'surgery_report',
                'label'   => 'Surgery Report - ' . $surgery->surgery_name,
                'content' => $surgery->uploaded_report_path,
            ];
            $data[] = [
                'type'    => 'pre_operative_checklist',
                'label'   => 'Pre-Operative Checklist - ' . $surgery->surgery_name,
                'content' => $preOperativeChecklist?->upload_pdf_path,
            ];
        }

        $dischargeSummary = \App\Models\IPDDischargeSummary::where('ipd_id', $ipdId)->first();
        $data[]           = [
            'type'    => 'discharge_summary',
            'label'   => 'Discharge Summary',
            'content' => $dischargeSummary?->upload_pdf_path,
        ];

        if ($type === 'all') {
            return $data;
        }

        return array_values(array_filter($data, fn ($item) => $item['type'] === $type));
    }

    public function downloademptyPdf(string $ipdId, string $type)
    {
        try {
            $ipd = IPD::with('patient', 'surgery')->findOrFail($ipdId);

            $htmlContent = '';
            $fileName    = '';

            $system = SystemSettings::first();

            $footer_content        = '';
            $letter_header_address = '';
            if (! empty($system)) {
                $letter_header_address = $system->billing_letter_header;
                $footer_content        = $system->footer_content ?? '';
            }

            // Decode HTML entities so they render correctly
            $footerHtml = html_entity_decode($footer_content);
            $headerHtml = view('templates.downloads.IPD.header', compact('letter_header_address'))->render();

            switch ($type) {
                case 'preliminary_notes':
                    $fileName    = 'preliminary_notes_' . $ipd->ipd_number . '.pdf';
                    $htmlContent = view('templates.downloads.IPD.empty.preliminary_notes', compact('ipd'))->render();
                    break;

                case 'doctor_notes':
                    $fileName          = 'doctor_notes_' . $ipd->ipd_number . '.pdf';
                    $htmlContent       = view('templates.downloads.IPD.empty.doctor_notes', compact('ipd'))->render();
                    break;

                case 'nurse_notes':
                    $fileName         = 'nurse_notes_' . $ipd->ipd_number . '.pdf';
                    $htmlContent      = view('templates.downloads.IPD.empty.nurse_notes', compact('ipd'))->render();
                    break;

                case 'anaesthesia_consent_form':
                    $fileName            = 'anaesthesia_consent_form_' . $ipd->ipd_number . '.pdf';
                    $htmlContent         = view('templates.downloads.IPD.empty.anaesthesia_consent_form', compact('ipd'))->render();
                    break;

                case 'pre_anaesthesia_assessment':
                    $fileName     = 'pre_anaesthesia_assessment_' . $ipd->ipd_number . '.pdf';
                    $htmlContent  = view('templates.downloads.IPD.empty.pre_operative_anaesthesia_evaluation_chart', compact('ipd'))->render();
                    break;

                case 'department_of_anaesthesia':
                    $fileName    = 'department_of_anaesthesia_' . $ipd->ipd_number . '.pdf';
                    $htmlContent = view('templates.downloads.IPD.empty.department_of_anaesthesia', compact('ipd'))->render();
                    break;

                case 'anaesthesia_recovery_room_observation':
                    $fileName    = 'anaesthesia_recovery_room_observation_' . $ipd->ipd_number . '.pdf';
                    $htmlContent = view('templates.downloads.IPD.empty.anaesthesia_recovery_room_observation', compact('ipd'))->render();
                    break;

                case 'anaesthesia_record':
                    $fileName            = 'anaesthesia_record_' . $ipd->ipd_number . '.pdf';
                    $htmlContent         = view('templates.downloads.IPD.empty.anaesthesia_record', compact('ipd'))->render();
                    break;

                case 'surgery_consent_form':
                    $fileName            = 'surgery_consent_form_' . $ipd->ipd_number . '.pdf';
                    $htmlContent         = view('templates.downloads.IPD.empty.surgery_consent_form', compact('ipd'))->render();
                    break;

                case 'surgery_report':
                    $fileName            = 'surgery_report_' . $ipd->ipd_number . '.pdf';
                    $htmlContent         = view('templates.downloads.IPD.empty.surgery_report', compact('ipd'))->render();
                    break;

                case 'pre_operative_checklist':
                    $fileName    = 'pre_operative_checklist_' . $ipd->ipd_number . '.pdf';
                    $htmlContent = view('templates.downloads.IPD.empty.pre_operative_checklist', compact('ipd'))->render();
                    break;

                case 'discharge_summary':
                    $fileName               = 'discharge_summary_' . $ipd->ipd_number . '.pdf';
                    $htmlContent            = view('templates.downloads.IPD.empty.discharge_summary', compact('ipd'))->render();
                    break;

                default:
                    throw new Exception('Invalid document type: ' . $type);
            }

            $filePath = storage_path("app/public/pdfs/ipd/{$ipd->ipd_number}/empty_files/{$fileName}");

            // Ensure the directory exists
            if (! Storage::disk('public')->exists("pdfs/ipd/{$ipd->ipd_number}/empty_files")) {
                Storage::disk('public')->makeDirectory("pdfs/ipd/{$ipd->ipd_number}/empty_files");
            }

            $footerHtml = '
                <div style="
                    width:100%;
                    font-size:10px;
                    text-align:center;
                    padding-top:5px;
                ">
                    Page <span class="pageNumber"></span> of <span class="totalPages"></span>
                </div>
                ';

            // Generate and save the PDF using Browsershot
            Browsershot::html($htmlContent)
                ->format('A4')
                ->margins(35, 10, 10, 10)
                ->noSandbox()
                ->waitUntilNetworkIdle()
                ->showBrowserHeaderAndFooter()
                ->headerHtml($headerHtml)
                ->footerHtml($footerHtml)
            // ->setOption('margin-left', '10')
            // ->setOption('margin-right', '10')
            // ->setOption('margin-top', '10')
            // ->setOption('margin-bottom', '30')  // 🔥 remove wkhtml default bottom margin
            // ->setOption('footer-spacing', '0') // 🔥 remove spacing between body & footer
            // ->setOption('printBackground', true)
                ->savePdf($filePath);

            // Store only the relative path
            $relativePath = "pdfs/ipd/{$ipd->ipd_number}/empty_files/{$fileName}";

            // Return the public URL
            return asset("storage/{$relativePath}");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function emptyValueObject(): object
    {
        return new class {
            public function __get(string $name): string
            {
                if (in_array($name, [
                    'abp_details',
                    'airway',
                    'central_blocks_epidural',
                    'central_blocks_spinal',
                    'cvp_details',
                    'drugs_regional',
                    'endotracheal_tube',
                    'endotracheal_tube_type',
                    'iv_access',
                    'laryngoscopy',
                    'maintenance',
                    'mask_anaesthesia',
                    'monitors',
                    'monitoring',
                    'nasogastric_tube',
                    'nerve_stimulator',
                    'patient_safety',
                    'post_operative_complications',
                    'post_operative_medications',
                    'pre_anaesthesia_state',
                    'regional_blocks',
                    'regional_supplements',
                    'ventilated_patient',
                    'vital_monitoring',
                ])) {
                    return '[]';
                }

                return '';
            }

            public function __isset(string $name): bool
            {
                return true;
            }

            public function pluck(string $value)
            {
                return collect();
            }
        };
    }


    private function billingInvoiceData(string $ipdId): object
    {
        $invoice = Invoice::where('ipd_id', $ipdId)->first();
        $items   = IPDInvoiceItem::where('ipd_id', $ipdId)
            ->orderBy('service_category')
            ->orderBy('service_date')
            ->get();

        $groupedItems = $items
            ->groupBy(fn($item) => $item->service_category ?: 'Other Charges')
            ->map(function ($categoryItems, $category) {
                $rate = $categoryItems->pluck('amount')->unique()->count() === 1
                    ? (float) $categoryItems->first()->amount
                    : '';
                $taxPercent = $categoryItems->pluck('tax_percent')->unique()->count() === 1
                    ? $categoryItems->first()->tax_percent
                    : '';

                return (object) [
                    'category'    => $category,
                    'rate'        => $rate,
                    'tax_percent' => $taxPercent,
                    'days_count'  => $categoryItems->count(),
                    'amount'      => (float) $categoryItems->sum('amount'),
                    'total_amount' => (float) $categoryItems->sum('total_amount'),
                    'tax_amount'  => (float) $categoryItems->sum('tax_amount'),
                ];
            })
            ->values();

        $professionalItems = $groupedItems
            ->filter(fn($item) => stripos($item->category, 'professional') !== false)
            ->values();
        $invoiceItems = $groupedItems
            ->reject(fn($item) => stripos($item->category, 'professional') !== false)
            ->values();

        $receipts       = $invoice ? Receipt::where('invoice_id', $invoice->id)->get() : collect();
        $totalAmount    = (float) $items->sum('total_amount');
        $receivedAmount = (float) $receipts->sum('amount');
        $balanceAmount  = max($totalAmount - $receivedAmount, 0);

        return (object) [
            'bill_no'              => $invoice?->invoice_number,
            'bill_date'            => $invoice?->created_at,
            'invoice_items'        => $invoiceItems,
            'professional_charges' => $professionalItems,
            'total_amount'         => $totalAmount,
            'net_amount'           => round($totalAmount),
            'advance_amount'       => 0,
            'received_amount'      => $receivedAmount,
            'balance_amount'       => $balanceAmount,
            'amount_in_words'      => $this->amountInWords($totalAmount),
            'receipts'             => $receipts,
        ];
    }

    public function preOperative_checklist(string $patient_id)
    {
        try {

            $system = SystemSettings::first();

            $footer_content        = '';
            $letter_header_address = '';
            if (! empty($system)) {
                $letter_header_address = $system->billing_letter_header;
                $footer_content        = $system->footer_content ?? '';
            }

            // Decode HTML entities so they render correctly
            $headerHtml = view('templates.downloads.IPD.header', compact('letter_header_address'))->render();
            $footerHtml = html_entity_decode($footer_content);

            $patient     = Patient::where('id', $patient_id)->first();
            $fileName    = 'pre_operative_checklist_' . str_replace([".", " "], "_", $patient->name) . '.pdf';
            $htmlContent = view('templates.downloads.IPD.patient_pre_operative_checklist', compact('patient'))->render();
            $filePath    = storage_path("app/public/pdfs/ipd/pre_operative_checklist/{$fileName}");

            // Ensure the directory exists
            if (! Storage::disk('public')->exists("pdfs/ipd/pre_operative_checklist")) {
                Storage::disk('public')->makeDirectory("pdfs/ipd/pre_operative_checklist");
            }

            $footerHtml = '
                <div style="
                    width:100%;
                    font-size:10px;
                    text-align:center;
                    padding-top:5px;
                ">
                    Page <span class="pageNumber"></span> of <span class="totalPages"></span>
                </div>
                ';

            // Generate and save the PDF using Browsershot
            Browsershot::html($htmlContent)
                ->format('A4')
                ->margins(35, 10, 10, 10)
                ->noSandbox()
                ->waitUntilNetworkIdle()
                ->showBrowserHeaderAndFooter()
                ->headerHtml($headerHtml)
                ->footerHtml($footerHtml)
            // ->setOption('margin-left', '10')
            // ->setOption('margin-right', '10')
            // ->setOption('margin-top', '10')
            // ->setOption('margin-bottom', '30')  // 🔥 remove wkhtml default bottom margin
            // ->setOption('footer-spacing', '0') // 🔥 remove spacing between body & footer
            // ->setOption('printBackground', true)
                ->savePdf($filePath);

            // Store only the relative path
            $relativePath = "pdfs/ipd/pre_operative_checklist/{$fileName}";

            // Return the public URL
            return asset("storage/{$relativePath}");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    private function amountInWords($amount): string{
        $amount = round((float) $amount, 2);

        $number = (int) floor($amount);
        $paise = (int) round(($amount - $number) * 100);

        $ones = [
            0 => '',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
        ];

        $tens = [
            2 => 'Twenty',
            3 => 'Thirty',
            4 => 'Forty',
            5 => 'Fifty',
            6 => 'Sixty',
            7 => 'Seventy',
            8 => 'Eighty',
            9 => 'Ninety',
        ];

        $convertTwoDigits = function ($number) use ($ones, $tens) {
            if ($number < 20) {
                return $ones[$number];
            }

            $ten = intdiv($number, 10);
            $unit = $number % 10;

            return $tens[$ten] . ($unit ? ' ' . $ones[$unit] : '');
        };

        $convertNumber = function ($number) use ($ones, $convertTwoDigits) {
            if ($number == 0) {
                return '';
            }

            $result = '';

            // Crore
            if ($number >= 10000000) {
                $crore = intdiv($number, 10000000);
                $result .= $convertNumber($crore) . ' Crore ';
                $number %= 10000000;
            }

            // Lakh
            if ($number >= 100000) {
                $lakh = intdiv($number, 100000);
                $result .= $convertTwoDigits($lakh) . ' Lakh ';
                $number %= 100000;
            }

            // Thousand
            if ($number >= 1000) {
                $thousand = intdiv($number, 1000);
                $result .= $convertTwoDigits($thousand) . ' Thousand ';
                $number %= 1000;
            }

            // Hundred
            if ($number >= 100) {
                $hundred = intdiv($number, 100);
                $result .= $ones[$hundred] . ' Hundred ';
                $number %= 100;
            }

            // Remaining two digits
            if ($number > 0) {
                $result .= $convertTwoDigits($number);
            }

            return trim($result);
        };

        if ($number == 0) {
            $result = 'Zero';
        } else {
            $result = $convertNumber($number);
        }

        $result = 'Rupees ' . trim($result);

        if ($paise > 0) {
            $result .= ' and ' . $convertTwoDigits($paise) . ' Paise';
        }

        return $result . ' Only';
    }
}

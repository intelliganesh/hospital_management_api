<?php
namespace App\Services;

// use Mpdf\Mpdf;
// use App\Models\Theme;
use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Enums\Consultation\TypeEnum;
use App\Enums\Payment\PaymentStatusEnum;
use App\Enums\ServiceType;
use App\Models\Allopathy;
use App\Models\Appointments;
use App\Models\Consultations;
use App\Models\Invoice;
use App\Models\Master\Test;
use App\Models\NonProctology;
use App\Models\Patient;
use App\Models\Payment;
// use App\Models\InvoiceAmount;
use App\Models\Proctology;
use App\Models\Receipt;
use App\Models\SystemSettings;
use App\Models\User;
use App\Models\Vital;
use App\Services\CheckValidation;
use App\Traits\InvoiceValidation;
use AutoIdGenerate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

// use App\Models\Master\YogaAsana;
// use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService implements CRUDContract, FilterContract
{
    use InvoiceValidation;

    private $columns;
    private $paymentService;
    private $patientHelperService;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\PaymentService $paymentService
     * @param \App\Services\PatientHelperService $patientHelperService
     */
    public function __construct(CheckValidation $checkValidationService, PaymentService $paymentService, PatientHelperService $patientHelperService)
    {
        $this->columns                = Invoice::$columns;
        $this->paymentService         = $paymentService;
        $this->patientHelperService   = $patientHelperService;
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        return $data->where('consultations.next_visit_date', 'like', '%' . $searchText . '%')
            ->orWhere('consultations.patient_number', 'like', '%' . $searchText . '%')
            ->orWhere('consultations.patient_name', 'like', '%' . $searchText . '%')
            ->orWhere('consultations.appointment_number', 'like', '%' . $searchText . '%')->orWhere('status', 'like', '%' . $searchText . '%')->orWhere('payment_status', 'like', '%' . $searchText . '%')
            ->orWhere('consultations.doctor_name', 'like', '%' . $searchText . '%')->orWhereHas('invoiceNumber', function ($query) use ($searchText) {
            $query->where('invoice_number', 'like', '%' . $searchText . '%');
        });
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        if (isset($request['patient_phone']) && $request['patient_phone'] != null && $request['patient_phone'] != '') {
            $data->where('consultations.patient_phone', $request['patient_phone']);
        }

        if (isset($request['invoice_number']) && $request['invoice_number'] != null && $request['invoice_number'] != '') {
            $data->where('invoice.invoice_number', $request['invoice_number']);
        }

        if (isset($request['patient_name']) && $request['patient_name'] != null && $request['patient_name'] != '') {
            $data->where('consultations.patient_name', $request['patient_name']);
        }

        if (isset($request['appointment_number']) && $request['appointment_number'] != null && $request['appointment_number'] != '') {
            $data->where('consultations.appointment_number', $request['appointment_number']);
        }

        if (isset($request['doctor_name']) && $request['doctor_name'] != null && $request['doctor_name'] != '') {
            $data->where('consultations.doctor_name', $request['doctor_name']);
        }

        if (isset($request['status']) && $request['status'] != null && $request['status'] != '') {
            $data->where('consultations.status', $request['status']);
        }

        if (isset($request['bill_amount']) && $request['bill_amount'] != null && $request['bill_amount'] != '') {
            $data->where('invoice.collected_amount', $request['bill_amount']);
        }

        if (isset($request['payment_status']) && $request['payment_status'] != null && $request['payment_status'] != '') {
            $data->where('consultations.payment_status', $request['payment_status']);
        }

        return $data;
    }

    /**
     * Summary of filterByDateRange
     * @param string $searchText
     * @param mixed $data
     */
    public function filterByDateRange(string $searchText, $data)
    {
        $dates = explode("|", $searchText);
        $data->whereBetween('consultations.created_at', [$dates[0], $dates[1]]);
        return $data;
    }

    /**
     * @deprecated this function is not in use
     */
    public function sortData(string $searchText, $data)
    {
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Invoice::create(array_merge($request->all(), ['invoice_number' => AutoIdGenerate::generateId(ServiceType::Invoice)]));
    }

    /**
     * Summary of addOrUpdate
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create  invoice  record within a secure transaction')]
    public function addOrUpdate(Request $request, string $id)
    {
        $columnName = $request->columnName ?? 'id';
        $this->checkValidationService->checkValidation($this->validate($request));

        $findAttributes           = [$columnName => $id];
        $fillableAttributes       = $request->only($this->columns);
        $invoice                  = Invoice::where($findAttributes)->first();
        $consultation             = Consultations::where('id', $request->consultationId)->first();
        $additional_amount_reason = isset($request->additional_amount_reason) ? $request->additional_amount_reason : null;
        Payment::where('consultation_id', $request->consultationId)->where('payment_status', PaymentStatusEnum::Pending->value)
            ->update(['additional_amount_reason' => $additional_amount_reason]);
        Payment::where('consultation_id', $request->consultationId)
            ->update(['payment_status' => PaymentStatusEnum::Completed->value, 'payment_date' => \Carbon\Carbon::now()]);

        if ($consultation) {
            $consultation->status         = $request->status;
            $consultation->payment_status = $request->paymentStatus;

            if (strtolower($request->paymentStatus) == "completed") {
                $consultation->payment_date = \Carbon\Carbon::now();
            }
            $consultation->save();
        }
        // $consultation->update(['status' => $request->status, 'payment_status' => $request->paymentStatus]);
        $filleFields = [
            'patient_id',
            'doctor_id',
            'front_desk_user_id',
            'patient_name',
            'patient_email',
            'patient_phone',
            "patient_number",
            'doctor_name',
            'doctor_email',
            'doctor_phone',
            'referred_by_name',
            'referred_by_email',
            'referred_by_phone_no',
            'referred_by_hospital_name',
            'comment',
        ];
        if ($invoice) {
            $invoice->update(array_merge($fillableAttributes, $consultation->only($filleFields)));
            $invoice->update(['comment' => $request->comment ?? null]);
        } else {
            $fillableAttributes['invoice_number'] = AutoIdGenerate::generateId(ServiceType::Invoice);
            $invoice                              = Invoice::create(array_merge([
                'balanced_amount'  => $request->balanced_amount,
                'collected_amount' => $request->collected_amount,
            ], $fillableAttributes, $consultation->only($filleFields)));
        }
        $test = Test::whereIn('id', explode(',', $invoice->consultation_data()->test_id))->get();
        if ($invoice->consultation_data()->type === TypeEnum::NonProctology->value) {
            $protologyOrNonProctology = NonProctology::where('consultation_id', $invoice->consultation_id)->first();
            // $protologyOrNonProctology['yoga'] = YogaAsana::where('id', $protologyOrNonProctology->yoga_asana)->first();
        } else if ($invoice->consultation_data()->type === TypeEnum::Proctology->value) {
            $protologyOrNonProctology = Proctology::where('consultation_id', $invoice->consultation_id)->first();
        } else if ($invoice->consultation_data()->type === TypeEnum::Allopathy->value) {
            $protologyOrNonProctology = Allopathy::where('consultation_id', $invoice->consultation_id)->first();
        }
        $paymentData = Payment::where('include_in_invoice', 1)->where('consultation_id', $invoice->consultation_id)->get();

        $consultationCost = $paymentData->firstWhere('amount_for', 'Consultation Cost');

        $remainingPayments = $paymentData->reject(function ($payment) {
            return $payment->amount_for === 'Consultation Cost';
        });

        if (! empty($consultationCost) && isset($consultationCost)) {
            $paymentData = collect([$consultationCost])->merge($remainingPayments);
        } else {
            $paymentData = collect($remainingPayments);
        }

        // $consultation['total_amount'] = $this->totalAmount($consultation, $paymentData, $test);
        $total_amount          = $this->totalAmount($paymentData, $test);
        $discount_amount       = $protologyOrNonProctology ? $this->discountAmount($total_amount, $protologyOrNonProctology->consultation_discount) : 0;
        $discount_total_amount = $consultation['total_amount'] - $discount_amount;

        if (isset($request->instalment)) {
            $ids              = [];
            $collected_amount = 0;
            foreach ($request->instalment as $instalment) {
                if (isset($instalment['id']) && $instalment['id'] != null) {
                    $updated_receipt = Receipt::where('id', $instalment['id'])->update([
                        'invoice_id'     => $invoice->id,
                        'amount'         => $instalment['amount'],
                        'date'           => $instalment['date'] ?? \Carbon\Carbon::now(),
                        'payment_type'   => $instalment['payment_type'] ?? 'cash',
                        'transaction_id' => $instalment['transaction_id'] ?? '',
                        'notes'          => $instalment['notes'] ?? '',
                        'currency'       => $instalment['notes'] ?? '₹',
                    ]);
                    $ids[] = $instalment['id'];
                } else {
                    $updated_receipt = Receipt::create([
                        'invoice_id'     => $invoice->id,
                        'amount'         => $instalment['amount'],
                        'date'           => $instalment['date'] ?? \Carbon\Carbon::now(),
                        'payment_type'   => $instalment['payment_type'] ?? 'cash',
                        'transaction_id' => $instalment['transaction_id'] ?? '',
                        'status'         => $instalment['status'] ?? 'Pending',
                        'notes'          => $instalment['notes'] ?? '',
                        'currency'       => $instalment['notes'] ?? '₹',
                    ]);
                    $ids[] = $updated_receipt->id;
                }
                $collected_amount += $instalment['amount'];
                Log::info(json_encode($updated_receipt));
            }

            $invoice->update([
                'collected_amount' => $collected_amount,
                'balanced_amount'  => $discount_total_amount - $collected_amount,
            ]);
            Receipt::where('invoice_id', $invoice->id)->whereNotIn('id', $ids)->delete();
        }
        if (strtolower($request->paymentStatus) == "completed" && count($invoice->receipt) > 0) {
            Receipt::where('invoice_id', $invoice->id)->update([
                'status' => 'Completed',
            ]);
        }

        // InvoiceAmount::updateOrCreate([
        //     'invoice_id' => $invoice->id,
        //     'transaction_id' => $request->transaction_id
        // ], [
        //     'invoice_id' => $invoice->id,
        //     'payment_type' => $request->payment_type,
        //     'transaction_id' => $request->transaction_id,
        //     'balanced_amount' => $request->balanced_amount,
        //     'collected_amount' => $request->collected_amount,
        // ]);
        // Invoice::updateOrCreate([$columnName => $id], array_merge($request->only($this->columns, ['invoice_number' => AutoIdGenerate::generateId(ServiceType::Invoice)])));
    }

    /**
     * @deprecated this function is not in use
     */
    public function update(Request $request, string | null $id): void
    {

    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string | null $id): void
    {
        //code here
    }

    /**
     * @deprecated this function is not in use
     */
    public function delete(string $id): void
    {
    }

    /**
     * Summary of get
     * @param string $id
     * @return array
     */
    public function get(string $id): mixed
    {
        return $this->invoiceData($id);
    }

    /**
     * Summary of getConsultationDownload
     * @param string $id
     * @return string
     */
    public function getConsultationDownload(string $id)
    {
        // $pdf = Pdf::loadView('templates.downloads.consultation-test', $this->invoiceData($id));
        // // return $pdf->output();

        // //   $pdf = Pdf::loadView('templates.downloads.invoice-bill', $this->invoiceData($id));
        // // return $pdf->output();
        // $fileName = 'consultation_' . $id . '_' . time() . '.pdf';
        // $filePath = 'pdfs/' . $fileName;

        // if (!Storage::disk('public')->exists('pdfs')) {
        //     Storage::disk('public')->makeDirectory('pdfs');
        // }

        // Storage::disk('public')->put($filePath, $pdf->output());

        // $downloadUrl = asset('storage/' . $filePath);
        // return $downloadUrl;

        $invoiceData = $this->invoiceData($id);
        $html        = view('templates.downloads.consultation-test', $invoiceData)->render();

        // Define PDF filename
        $fileName = 'consultation_' . $id . '_' . time() . '.pdf';
        $filePath = storage_path("app/public/pdfs/{$fileName}");

        // Ensure the directory exists
        if (! Storage::disk('public')->exists('pdfs')) {
            Storage::disk('public')->makeDirectory('pdfs');
        }

        if (is_null($invoiceData['letter_footer_info']) || empty($invoiceData['letter_footer_info'])) {
            $footer_content = $invoiceData['footerContent'];
        } else {
            $footer_content = $invoiceData['letter_footer_info'];
        }
        // Decode HTML entities so they render correctly
        $footerHtml = html_entity_decode($footer_content);

        // Generate and save the PDF using Browsershot
        Browsershot::html($html)
            ->format('A4')
            ->margins(0, 0, 20, 0) // reduce bottom margin here
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->showBrowserHeaderAndFooter()
            ->footerHtml($footerHtml)
            ->setOption('margin-bottom', '0')  // 🔥 remove wkhtml default bottom margin
            ->setOption('footer-spacing', '0') // 🔥 remove spacing between body & footer
            ->setOption('printBackground', true)
            ->savePdf($filePath);

        // Return the public URL
        return asset("storage/pdfs/{$fileName}");
    }
    // /**
    //  * Summary of getPrescriptionDownload
    //  * @param string $id
    //  * @return string
    //  */
    // public function getPrescriptionDownload(string $id)
    // {

    //     // $pdf = Pdf::loadView('templates.downloads.consultation-prescription', $this->invoiceData($id))->setPaper('a5');
    //     // return $pdf->output();
    //     // $html = view('templates.downloads.consultation-prescription', $this->invoiceData($id))->render();
    //     // $mpdf = new Mpdf([
    //     //     'default_font' => 'NotoSansKannada', // recommended for Kannada
    //     // ]);
    //     // var_dump(file_exists(storage_path('fonts/BalooTamma2-Regular.ttf')));

    //     // $mpdf = new Mpdf([
    //     //     'mode' => 'utf-8',
    //     //     'format' => 'A4',
    //     //     'default_font' => 'notosanskannada',
    //     //     'fontDir' => [
    //     //         storage_path('fonts/'),
    //     //         __DIR__ . '/../../vendor/mpdf/mpdf/ttfonts', // fallback system dir
    //     //     ],
    //     //     'fontdata' => [
    //     //         'notosanskannada' => [
    //     //             'R' => 'BalooTamma2-Regular.ttf',
    //     //             'useOTL' => 0xFF,
    //     //             'useKashida' => 75,
    //     //         ],
    //     //     ],
    //     // ]);
    //     // $mpdf = new Mpdf([
    //     //     'mode' => 'utf-8',
    //     //     'format' => 'A4',
    //     //     'default_font' => 'notosanskannada',
    //     //     'default_font_size' => 12,
    //     //     'tempDir' => storage_path('app/mpdf_temp'), // Optional but recommended
    //     //     'fontDir' => [storage_path('fonts/Noto_Sans_Kannada/static/')],
    //     //     'fontdata' => [
    //     //         'notosanskannada' => [
    //     //             'R' => 'NotoSansKannada-Regular.ttf',
    //     //             'useOTL' => 0xFF,      // Required for Indic languages
    //     //             'useKashida' => 75     // Optional (used for Arabic, ignore if not needed)
    //     //         ],
    //     //     ],
    //     // ]);
    //     // $mpdf = new Mpdf([
    //     //     'mode' => 'utf-8',
    //     //     'format' => 'A4',
    //     //     // 'default_font' => 'notosanskannada',
    //     //     'default_font_size' => 12,
    //     //     'tempDir' => storage_path('app/mpdf_temp'), // Optional but recommended
    //     //     'fontDir' => [storage_path('fonts')],
    //     //     'default_font' => 'balootamma2',
    //     //     'fontdata' => [
    //     //         'balootamma2' => [
    //     //             'R' => 'BalooTamma2-Regular.ttf',
    //     //             'useOTL' => 0xFF,
    //     //         ],
    //     //     ],
    //     // ]);
    //     // $mpdf = new Mpdf([
    //     //     'mode' => 'utf-8',
    //     //     'default_font' => 'notosanskannada',
    //     //     'fontDir' => [
    //     //         storage_path('fonts/'),
    //     //         __DIR__ . '/../../vendor/mpdf/mpdf/ttfonts',
    //     //     ],
    //     //     'fontdata' => [
    //     //         'notosanskannada' => [
    //     //             'R' => 'NotoSansKannada-Regular.ttf',
    //     //             'useOTL' => 0xFF, // Enables complex script shaping
    //     //             'useKashida' => 75,
    //     //         ],
    //     //     ],
    //     // ]);
    //     // $mpdf->WriteHTML($html);
    //     // return $mpdf->Output('', 'S');

    //     // $fileName = 'prescription_' . $id . '_' . time() . '.pdf';
    //     // $filePath = 'pdfs/' . $fileName;

    //     // if (!Storage::disk('public')->exists('pdfs')) {
    //     //     Storage::disk('public')->makeDirectory('pdfs');
    //     // }

    //     // // Storage::disk('public')->put($filePath, $pdf->output());
    //     // // Storage::disk('public')->put($filePath, $mpdf->Output('', 'S'));

    //     // $downloadUrl = asset('storage/' . $filePath);
    //     // return $downloadUrl;

    //     $html = view('templates.downloads.consultation-prescription', $this->invoiceData($id))->render();

    //     // Define PDF filename
    //     $fileName = 'prescription_' . $id . '_' . time() . '.pdf';
    //     $filePath = storage_path("app/public/pdfs/{$fileName}");

    //     // Ensure the directory exists
    //     if (!Storage::disk('public')->exists('pdfs')) {
    //         Storage::disk('public')->makeDirectory('pdfs');
    //     }

    //     // Generate the PDF using Browsershot
    //     $pdfBinary = Browsershot::html($html)
    //         ->format('A4')
    //         ->margins(10, 10, 10, 10)
    //         ->noSandbox()
    //         ->waitUntilNetworkIdle()
    //         ->pdf();

    //     return $pdfBinary;
    //     // return asset('storage/pdfs/' . $fileName);

    // }

    /**
     * Summary of getPrescriptionDownload
     * @param string $id
     * @return string
     */
    public function getPrescriptionDownload(string $id)
    {
        $invoiceData = $this->invoiceData($id);
        $html        = view('templates.downloads.consultation-prescription', $invoiceData)->render();

        // Define PDF filename
        $fileName = 'prescription_' . $id . '_' . time() . '.pdf';
        $filePath = storage_path("app/public/pdfs/{$fileName}");

        // Ensure the directory exists
        if (! Storage::disk('public')->exists('pdfs')) {
            Storage::disk('public')->makeDirectory('pdfs');
        }

        if (is_null($invoiceData['letter_footer_info']) || empty($invoiceData['letter_footer_info'])) {
            $footer_content = $invoiceData['footerContent'];
        } else {
            $footer_content = $invoiceData['letter_footer_info'];
        }
        // Decode HTML entities so they render correctly
        $footerHtml = html_entity_decode($footer_content);

        // Generate and save the PDF using Browsershot
        Browsershot::html($html)
            ->format('A5')
            ->margins(0, 0, 20, 0) // reduce bottom margin here
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->showBrowserHeaderAndFooter()
            ->footerHtml($footerHtml)
            ->setOption('margin-bottom', '0')  // 🔥 remove wkhtml default bottom margin
            ->setOption('footer-spacing', '0') // 🔥 remove spacing between body & footer
            ->setOption('printBackground', true)
            ->savePdf($filePath);

        // Return the public URL
        return asset("storage/pdfs/{$fileName}");
    }

    /**
     * Summary of download
     * @param string $id
     * @return string
     */
    public function download(string $id)
    {
        // // $pdf = Pdf::loadView('templates.downloads.invoice', $this->invoiceData($id));
        // $pdf = Pdf::loadView('templates.downloads.invoice-bill', $this->invoiceData($id));
        // // return $pdf->output();
        // $fileName = 'invoice_' . $id . '_' . time() . '.pdf';
        // $filePath = 'pdfs/' . $fileName;

        // if (!Storage::disk('public')->exists('pdfs')) {
        //     Storage::disk('public')->makeDirectory('pdfs');
        // }

        // Storage::disk('public')->put($filePath, $pdf->output());

        // $downloadUrl = asset('storage/' . $filePath);
        // return $downloadUrl;
        // // return response($pdf->output(), 200)
        // //     ->header('Content-Type', 'application/pdf')
        // //     ->header('Content-Disposition', 'attachment; filename="invoice.pdf"');
        $invoiceData = $this->invoiceData($id);
        $html        = view('templates.downloads.invoice-bill', $invoiceData)->render();

        // Define PDF filename
        $fileName = 'invoice_' . $id . '_' . time() . '.pdf';
        $filePath = storage_path("app/public/pdfs/{$fileName}");

        // Ensure the directory exists
        if (! Storage::disk('public')->exists('pdfs')) {
            Storage::disk('public')->makeDirectory('pdfs');
        }

        if (is_null($invoiceData['letter_footer_info']) || empty($invoiceData['letter_footer_info'])) {
            $footer_content = $invoiceData['footerContent'];
        } else {
            $footer_content = $invoiceData['letter_footer_info'];
        }
        // Decode HTML entities so they render correctly
        $footerHtml = html_entity_decode($footer_content);

        // Generate and save the PDF using Browsershot
        Browsershot::html($html)
            ->format('A5')
            ->margins(0, 0, 20, 0) // reduce bottom margin here
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->showBrowserHeaderAndFooter()
            ->footerHtml($footerHtml)
            ->setOption('margin-bottom', '0')  // 🔥 remove wkhtml default bottom margin
            ->setOption('footer-spacing', '0') // 🔥 remove spacing between body & footer
            ->setOption('printBackground', true)
            ->savePdf($filePath);

        // Return the public URL
        return asset("storage/pdfs/{$fileName}");
    }

    /**
     * Summary of addPayment
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function addPayment(Request $request)
    {
        $getDoctorAndPatient = $this->patientHelperService->getPatientAndUsers($request->doctor_id, $request->patient_id, $request->front_desk_user_id);

        $this->paymentService->create(array_merge($request->all(), $getDoctorAndPatient));
        $consultation = Consultations::where('id', $request->consultation_id)->first();
        $consultation->update([
            'payment_status' => PaymentStatusEnum::Pending->value,
        ]);
        $additionl_cost = $request->amount_for . '#' . $request->amount;
        if ($consultation->type === TypeEnum::Proctology->value) {
            $proctologyornonproctology = Proctology::where('consultation_id', $consultation->id)->first();
        } else if ($consultation->type === TypeEnum::NonProctology->value) {
            $proctologyornonproctology = NonProctology::where('consultation_id', $consultation->id)->first();
        } else if ($consultation->type === TypeEnum::Allopathy->value) {
            $proctologyornonproctology = Allopathy::where('consultation_id', $consultation->id)->first();
        }

        $existing_cost   = explode(',', $proctologyornonproctology->additional_cost);
        $existing_cost[] = $additionl_cost;

        // // Calculate discount amount using consultation_discount percentage
        // $discountPercentage = $proctologyornonproctology->consultation_discount;
        // $discountedAmount = $this->discountAmount($request->amount, $discountPercentage);
        // $discount=$proctologyornonproctology->discount_amount + $discountedAmount;

        // Update the proctology record with the new data
        $proctologyornonproctology->additional_cost = implode(',', $existing_cost);
        $proctologyornonproctology->save();

        // $invoice = Invoice::where('consultation_id', $request->consultation_id)->first();

        // if (!empty($request->amount)) {
        //     $invoice->balanced_amount += $request->amount;
        //     $invoice->save();
        // }

        // if (!empty($request?->balanced_amount)) {
        //     $invoice->balanced_amount = $request->balanced_amount;
        //     $invoice->save();
        // }
        // if (!empty($request?->collected_amount)) {
        //     $invoice->collected_amount = $request->collected_amount;
        //     $invoice->save();
        // }
    }

    /**
     * Summary of paymentDetails
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Collection<int, Payment>
     */
    public function paymentDetails(string $id)
    {
        return $this->paymentService->getByColumnNameDynamic('consultation_id', $id);
    }

    public function testDetails(string $id)
    {
        return view("templates.downloads.invoice-bill", $this->invoiceData($id));
    }

    /**
     * Summary of invoiceData
     * @param string $id
     * @return array
     */
    private function invoiceData(string $id)
    {
        $protologyOrNonProctology = null;
        $consultation             = Consultations::where('id', $id)->first();
        $auth                     = Auth::user();
        // $consultation['primary_color'] = Theme::where('user_id', $auth->id)->first()->primary_color ?? "";
        $consultation['primary_color']     = "";
        $consultation['consultation_type'] = $consultation->type;
        $test                              = Test::whereIn('id', explode(',', $consultation->test_id))->get();
        if ($consultation->type === TypeEnum::NonProctology->value) {
            $protologyOrNonProctology = NonProctology::where('consultation_id', $consultation->id)->first();
            // $protologyOrNonProctology['yoga'] = YogaAsana::where('id', $protologyOrNonProctology->yoga_asana)->first();
        } else if ($consultation->type === TypeEnum::Proctology->value) {
            $protologyOrNonProctology = Proctology::where('consultation_id', $consultation->id)->first();
        } else if ($consultation->type === TypeEnum::Allopathy->value) {
            $protologyOrNonProctology = Allopathy::where('consultation_id', $consultation->id)->first();
        }
        $consultation['payment_type'] = "";
        $invoice                      = Invoice::where('consultation_id', $consultation->id)->first();
        if ($invoice) {
            if (count($invoice->receipt) > 0) {
                $payment_type                 = $invoice->receipt->pluck('payment_type')->unique();
                $consultation['payment_type'] = implode(",", $payment_type->toArray());
            }
        }

        $user    = User::where('id', $consultation->doctor_id)->first();
        $patient = Patient::where('id', $consultation->patient_id)->first();

        $consultation['patient_document']   = $patient->getDocuments();
        $consultation['qualification']      = $user->qualification;
        $consultation['designation']        = $user->designation;
        $consultation['letter_header_info'] = $user->letter_header_info;
        $consultation['letter_footer_info'] = $user->letter_footer_info;
        $consultation['enroll_fees']        = $patient->enroll_fees;
        $consultation['age']                = $patient->age;
        $consultation['gender']             = $patient->gender;
        $consultation['department_type']    = $consultation->type;
        $consultation['type']               = Appointments::where('id', $consultation->appointment_id)->first()?->type ?? 'N/A';
        $consultation['collected_amount']   = $invoice->collected_amount ?? "";
        $consultation['balanced_amount']    = $invoice->balanced_amount ?? "";
        $consultation['payment_status']     = $consultation->payment_status ?? "";
        $consultation['transaction_id']     = $invoice->transaction_id ?? "";
        $consultation['invoice_number']     = $invoice->invoice_number ?? "";
        $consultation['comment']            = $invoice->comment ?? '';
        $additionalData                     = $protologyOrNonProctology ? $protologyOrNonProctology->toArray() : [];

        $system = SystemSettings::where('id', $user->system_settings_id)->first();

        if (! empty($system)) {
            $consultation['letter_header_address'] = $system->letter_header;
            $consultation['billing_letter_header'] = $system->billing_letter_header;
            $consultation['footerContent']         = $system->footer_content;
        }
        $vitals = Vital::where('consultation_id', $consultation->id)->first();
        // $additionalData['vitals'] = $vitals->toArray();
        $additionalData['vitals'] = $vitals ? $vitals->toArray() : [];

        $paymentData = Payment::where('include_in_invoice', 1)->where('consultation_id', $consultation->id)->get();

        $consultationCost = $paymentData->firstWhere('amount_for', 'Consultation Cost');

        $remainingPayments = $paymentData->reject(function ($payment) {
            return $payment->amount_for === 'Consultation Cost';
        });

        if (! empty($consultationCost) && isset($consultationCost)) {
            $paymentData = collect([$consultationCost])->merge($remainingPayments);
        } else {
            $paymentData = collect($remainingPayments);
        }

        // $consultation['total_amount'] = $this->totalAmount($consultation, $paymentData, $test);
        $consultation['total_amount']          = $this->totalAmount($paymentData, $test);
        $consultation['discount_amount']       = $protologyOrNonProctology ? $this->discountAmount($consultation['total_amount'], $protologyOrNonProctology->consultation_discount) : 0;
        $consultation['discount_total_amount'] = $consultation['total_amount'] - $consultation['discount_amount'];
        $consultation['prefill_amount']        = $this->prefillAmount($paymentData);

        $consultationArray = $consultation->toArray();
        $receipts          = Receipt::where('invoice_id', $invoice->id)->get();

        $paymentArray     = $receipts ? $receipts->toArray() : [];
        $paymentlistArray = $paymentData ? $paymentData->toArray() : [];

        $merged = array_merge($consultationArray, ['protologyOrNonProctology' => $additionalData, 'paymentArray' => $paymentArray, 'paymentlistArray' => $paymentlistArray, 'test' => $test]);

        return $merged;
    }

    /**
     * Summary of discountTotalAmount
     * @param mixed $consultation
     * @param mixed $paymentData
     * @param mixed $test
     * @return int
     */
    private function discountTotalAmount($paymentData)
    {
        $totalAmount = 0;
        foreach ($paymentData as $value) {
            if (! empty($value->discount_amount)) {
                $totalAmount += $value->discount_amount;
            }
        }
        return $totalAmount;
    }

    private function discountAmount($total, $discount)
    {
        // $totalAmount = 0;
        // foreach ($paymentData as $value) {
        //     if (! empty($value->discount_amount)) {
        //         $totalAmount += $value->discount_amount;
        //     }
        // }
        // return $totalAmount;

        if (empty($total) || empty($discount) || $discount <= 0) {
            return 0;
        }

        // Calculate the discount amount
        $discountAmount = ($total * $discount) / 100;

        // Round to 2 decimal places
        return round($discountAmount, 2);
    }

    private function prefillAmount($paymentData)
    {
        $totalAmount = 0;
        foreach ($paymentData as $value) {
            if ($value->payment_status === PaymentStatusEnum::Pending->value) {
                if (! empty($value->discount_amount)) {
                    $totalAmount += $value->amount - $value->discount_amount;
                } else {
                    $totalAmount += $value->amount;
                }
            }
        }
        return $totalAmount;
    }

    /**
     * Summary of totalAmount
     * @param mixed $paymentData
     * @param mixed $test
     * @return int
     */
    // private function totalAmount($consultation, $paymentData, $test)
    private function totalAmount($paymentData, $test)
    {
        $totalAmount = 0;
        // if ($consultation['type'] === "First Visit") {
        //     // $consultation['total_amount'] = $consultation['enroll_fees'];
        //     $totalAmount = $consultation['enroll_fees'];
        // }
        foreach ($paymentData as $value) {
            // $consultation['total_amount'] += $value->amount
            if (! empty($value->amount)) {
                $totalAmount += $value->amount;
            }
        }

        foreach ($test as $value) {
            $total  = $value->test_price + $value->tax_price;
            // $consultation['total_amount'] += $total;
            $totalAmount += $total;
        }

        return $totalAmount;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {

        // $consultation = Consultations::query()->orderBy('created_at', 'desc')->leftJoin('invoice', 'consultations.id', '=', 'invoice.consultation_id');
        // $consultation = Consultations::query()
        //     ->leftJoin('invoice', 'consultations.id', '=', 'invoice.consultation_id');
        // // $consultation = $consultation->where('status', 'Completed');
        // $consultation = $consultation->with('invoiceNumber');

        // $consultation = Consultations::query()
        //                 ->join('invoice', function ($join) {
        //                     $join->on('consultations.id', '=', 'invoice.consultation_id')
        //                          ->whereNotNull('invoice.invoice_number'); // only invoices with numbers
        //                 })
        //                 // ->where('consultations.status', 'Completed')       // only completed consultations
        //                 ->with('invoiceNumber')
        //                 ->select('consultations.*'); // avoid column collision

        $consultation = Consultations::query()
            ->whereExists(function ($q) {
                $q->selectRaw(1)
                    ->from('invoice')
                    ->whereColumn('invoice.consultation_id', 'consultations.id')
                    ->whereNotNull('invoice.invoice_number');
            })
            ->with('invoiceNumber'); // eager load if needed

        if ($request->has('search')) {
            $searchValue  = $request->search;
            $consultation = $this->search($searchValue, $consultation);
        }

        if ($request->has('sort_by')) {
            $sortBy    = $request->sort_by ?? '';
            $sortOrder = $request->sort_order ?? 'desc';
            if ($sortBy == "invoice_number") {
                $consultation = $consultation
                    ->leftJoin('invoice as i', function ($join) {
                        $join->on('i.consultation_id', '=', 'consultations.id')
                            ->whereNotNull('i.invoice_number');
                    })
                    ->orderBy('i.created_at', $sortOrder)
                    ->distinct('consultations.id')
                    ->select('consultations.*');
            } else {
                $consultation = $consultation->orderBy($sortBy, $sortOrder);
            }
        } else {
            $consultation = $consultation->orderBy('consultations.created_at', 'desc');
        }

        if ($request->has('multiple_filter')) {
            $consultation = $this->filterMultipleFields($request->multiple_filter, $consultation);
        }

        if ($request->has("from_date") && $request->has("to_date")) {
            $consultation = $this->filterByDateRange($request->from_date . "|" . $request->to_date, $consultation);
        }

        $consultations = $consultation->select(
            'consultations.id',
            'consultations.status',
            'consultations.doctor_name',
            'consultations.payment_status',
            'consultations.patient_number',
            'consultations.next_visit_date',
            'consultations.appointment_number',
            'consultations.appointment_id',
            'consultations.type',
            'consultations.test_id',
        )->paginate(env('PAGINATION', 25));

        // Add total amount for each consultation
        $consultations->getCollection()->transform(function ($consult) {
            $test = ! empty($consult->test_id) ? Test::whereIn('id', explode(',', $consult->test_id))->get() : collect();

            $protologyOrNonProctology = null;
            if ($consult->type === TypeEnum::NonProctology->value) {
                $protologyOrNonProctology = NonProctology::where('consultation_id', $consult->id)->first();
            } else if ($consult->type === TypeEnum::Proctology->value) {
                $protologyOrNonProctology = Proctology::where('consultation_id', $consult->id)->first();
            } else if ($consult->type === TypeEnum::Allopathy->value) {
                $protologyOrNonProctology = Allopathy::where('consultation_id', $consult->id)->first();
            }

            $paymentData       = Payment::where('include_in_invoice', 1)->where('consultation_id', $consult->id)->get();
            $consultationCost  = $paymentData->firstWhere('amount_for', 'Consultation Cost');
            $remainingPayments = $paymentData->reject(function ($payment) {
                return $payment->amount_for === 'Consultation Cost';
            });

            if (! empty($consultationCost)) {
                $paymentData = collect([$consultationCost])->merge($remainingPayments);
            } else {
                $paymentData = collect($remainingPayments);
            }

            $total_amount                   = $this->totalAmount($paymentData, $test);
            $discount_amount                = $protologyOrNonProctology ? $this->discountAmount($total_amount, $protologyOrNonProctology->consultation_discount) : 0;
            $discount_total_amount          = $total_amount - $discount_amount;
            $consult->discount_total_amount = $discount_total_amount;
            return $consult;
        });

        return $consultations;
    }

    /**
     * Summary of amountIncludeInInvoice
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function amountIncludeInInvoice(Request $request)
    {
        $invoice                     = Payment::where('id', $request->id)->first();
        $invoice->include_in_invoice = $request->include_in_invoice;
        $invoice->save();
    }

    public function downloadPrescriptionweb(string $id)
    {
        return view('templates.downloads.consultation-test', $this->invoiceData($id));
    }

    public function getConsultationPrescriptionData(string $appointment_id)
    {
        $appointment = Appointments::where('id', $appointment_id)->first();
        if (! $appointment) {
            return response()->json(['message' => 'No appointment found for the given ID.'], 404);
        }
        $consultation = Consultations::where('appointment_id', $appointment->id)->first();
        if (! $consultation) {
            return response()->json(['message' => 'No consultation found for the given date.'], 404);
        }
        $invoiceData = $this->invoiceData($consultation->id);
        $html        = view('templates.downloads.consultation_table', $invoiceData)->render();
        $html        = str_replace(["\r", "\n"], '', $html);
        return $html;
    }

}

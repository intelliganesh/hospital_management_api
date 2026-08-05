<?php
namespace App\Services;

use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Enums\Consultation\TypeEnum;
use App\Enums\Payment\PaymentStatusEnum;
use App\Models\Allopathy;
use App\Models\Invoice;
use App\Models\Master\Test;
use App\Models\NonProctology;
use App\Models\Payment;
use App\Models\Proctology;
use App\Services\CheckValidation;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

// use Rap2hpoutre\FastExcel\Facades\FastExcel;

class InvoiceReportsService implements CRUDContract, FilterContract
{
    private $filter = [
        'collected_amount',
        'balanced_amount',

        // Snapshot fields for patient
        'patient_name',
        'patient_email',
        'patient_phone',
        "patient_number",

        // Snapshot fields for doctor
        'doctor_name',
        'doctor_email',
        'doctor_phone',

        'referred_by_name',
        'referred_by_email',
        'referred_by_phone_no',
        'referred_by_hospital_name',
    ];
    private $columns = [
        "id",
        'patient_id',
        'doctor_id',
        'consultation_id',

        'collected_amount',
        'balanced_amount',
        'currency',

        // Snapshot fields for patient
        'patient_name',
        'patient_email',
        'patient_phone',
        "patient_number",

        // Snapshot fields for doctor
        'doctor_name',
        'doctor_email',
        'doctor_phone',

        'referred_by_name',
        'referred_by_email',
        'referred_by_phone_no',
        'referred_by_hospital_name',
    ];
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;

    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        foreach ($this->columns as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        foreach ($this->filter as $column) {
            if (! empty($request[$column])) {
                if ($column == "patient_name") {
                    $data->where($column, 'like', '%' . $request[$column] . '%');
                } elseif ($column == "doctor_name") {
                    $data->where("$column", 'like', '%' . $request[$column] . '%');
                } else {
                    $data->where("$column", $request[$column]);
                }
            }
        }
        return $data;
    }

    /**
     * Summary of filterByDateRange
     * @param string $searchText
     * @param mixed $data
     * @param mixed $columnName
     */
    public function filterByDateRange(string $searchText, $data, $columnName = 'created_at')
    {
        $dates = explode("|", $searchText);
        $start = \Carbon\Carbon::parse($dates[0])->startOfDay();
        $end   = \Carbon\Carbon::parse($dates[1])->endOfDay();
        $data->whereBetween($columnName, [$start, $end]);
        return $data;
    }

    /**
     * @deprecated this function is not in use
     */
    public function sortData(string $searchText, $data)
    {
    }

    /**
     * @deprecated message
     */
    public function create(Request $request): void
    {

    }

    /**
     * @deprecated message
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
     * @deprecated message
     */
    public function delete(string $id): void
    {
    }

    /**
     * Summary of get
     * @param string $id
     * @return Invoice
     */
    public function get(string $id): Invoice
    {
        return Invoice::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return array{balanced_amount: mixed, collected_amount: mixed, table: mixed}
     */
    public function all(?Request $request): mixed
    {
        /*
    |--------------------------------------------------------------------------
    | 1. Base Invoice Query (DO NOT EXECUTE THIS DIRECTLY)
    |--------------------------------------------------------------------------
    */
        $baseInvoiceQuery = Invoice::query();

        if ($request->has('from_date') && $request->has('to_date')) {
            $baseInvoiceQuery = $this->filterByDateRange(
                $request->from_date . '|' . $request->to_date,
                $baseInvoiceQuery
            );
        }

        if ($request->has('search')) {
            $baseInvoiceQuery = $this->search($request->search, $baseInvoiceQuery);
        }

        if ($request->has('multiple_filter')) {
            $baseInvoiceQuery = $this->filterMultipleFields(
                $request->multiple_filter,
                $baseInvoiceQuery
            );
        }

        if ($request->has('sort_by')) {
            $baseInvoiceQuery = $baseInvoiceQuery->orderBy(
                $request->sort_by,
                $request->sort_order ?? 'desc'
            );
        } else {
            $baseInvoiceQuery = $baseInvoiceQuery->orderBy('created_at', 'desc');
        }

        /*
    |--------------------------------------------------------------------------
    | 2. Totals (SAFE CLONES)
    |--------------------------------------------------------------------------
    */
        $balanced_amount = (clone $baseInvoiceQuery)->sum('balanced_amount');

        /*
    |--------------------------------------------------------------------------
    | 3. Payment Types Summary (Receipts)
    |--------------------------------------------------------------------------
    */
        $invoiceForGroup = (clone $baseInvoiceQuery)
            ->withoutGlobalScopes()
            ->with('receipt')
            ->get();

        $typesOfPayment = $invoiceForGroup
            ->flatMap(fn($invoice) => $invoice->receipt)
            ->where('status', 'Completed')
            ->whereNotNull('payment_type')
            ->groupBy('payment_type')
            ->map(fn($receipts, $type) => [
                'payment_type'    => $type,
                'total_collected' => $receipts->sum('amount'),
            ])
            ->values();

        /*
    |--------------------------------------------------------------------------
    | 4. Payment Query (FIXED ID MAPPING)
    |--------------------------------------------------------------------------
    */
        $consultationIds = (clone $baseInvoiceQuery)
            ->pluck('consultation_id')
            ->unique()
            ->values();

        $basePaymentQuery = Payment::query()
            ->whereIn('consultation_id', $consultationIds)
            ->where('payment_status', PaymentStatusEnum::Completed->value);

        /*
    |--------------------------------------------------------------------------
    | 5. Payment Calculations (NO SIDE EFFECTS)
    |--------------------------------------------------------------------------
    */
        $collected_amount = (clone $basePaymentQuery)
            ->where('include_in_invoice', 1)
            ->sum('amount');

        $includeInvoiceAmount = (clone $basePaymentQuery)
            ->where('include_in_invoice', 1)
            ->sum('amount');

        $excludeInvoiceAmount = (clone $basePaymentQuery)
            ->where('include_in_invoice', 0)
            ->sum('amount');

        $discountIncludeInvoiceAmount = (clone $basePaymentQuery)
            ->where('include_in_invoice', 1)
            ->sum('discount_amount');

        $discountExcludeInvoiceAmount = (clone $basePaymentQuery)
            ->where('include_in_invoice', 0)
            ->sum('discount_amount');

        /*
    |--------------------------------------------------------------------------
    | 6. Payment Breakpoint
    |--------------------------------------------------------------------------
    */
        $paymentBreakPoint = (clone $basePaymentQuery)
            ->selectRaw('amount_for, SUM(amount) as total_amount, SUM(discount_amount) as total_discount')
            ->groupBy('amount_for')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | 7. Pagination (FINAL QUERY ONLY)
    |--------------------------------------------------------------------------
    */
        $paginatedInvoices = (clone $baseInvoiceQuery)
            ->with('receipt')
            ->select($this->columns)
            ->paginate(env('PAGINATION', 25));

        $paginatedInvoices->getCollection()->transform(function ($inv) {
            $pmtBreakdown = [];
            foreach ($inv->getPaymentTypeBreakdown() as $type => $amount) {
                $object                 = [];
                $object['payment_type'] = $type;
                $object['amount']       = $amount;
                $pmtBreakdown[]         = $object;
            }
            $inv->paymentBreakdown = $pmtBreakdown;

            $test = Test::whereIn('id', explode(',', $inv->consultation_data()->test_id))->get();
            if ($inv->consultation_data()->type === TypeEnum::NonProctology->value) {
                $protologyOrNonProctology = NonProctology::where('consultation_id', $inv->consultation_id)->first();
                // $protologyOrNonProctology['yoga'] = YogaAsana::where('id', $protologyOrNonProctology->yoga_asana)->first();
            } else if ($inv->consultation_data()->type === TypeEnum::Proctology->value) {
                $protologyOrNonProctology = Proctology::where('consultation_id', $inv->consultation_id)->first();
            } else if ($inv->consultation_data()->type === TypeEnum::Allopathy->value) {
                $protologyOrNonProctology = Allopathy::where('consultation_id', $inv->consultation_id)->first();
            }
            $paymentData = Payment::where('include_in_invoice', 1)->where('consultation_id', $inv->consultation_id)->get();

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
            $total_amount               = $this->totalAmount($paymentData, $test);
            $discount_amount            = $protologyOrNonProctology ? $this->discountAmount($total_amount, $protologyOrNonProctology->consultation_discount) : 0;
            $discount_total_amount      = $total_amount - $discount_amount;
            $inv->discount_total_amount = $discount_total_amount;

            return $inv;
        });

        /*
    |--------------------------------------------------------------------------
    | 8. Response
    |--------------------------------------------------------------------------
    */
        return [
            'typesOfPayment'               => $typesOfPayment,
            'balanced_amount'              => $balanced_amount,
            'collected_amount'             => $collected_amount - $discountIncludeInvoiceAmount,
            'paymentBreakPoint'            => $paymentBreakPoint,
            'excludeInvoiceAmount'         => $excludeInvoiceAmount,
            'includeInvoiceAmount'         => $includeInvoiceAmount,
            'discountIncludeInvoiceAmount' => $discountIncludeInvoiceAmount,
            'discountExcludeInvoiceAmount' => $discountExcludeInvoiceAmount,
            'table'                        => $paginatedInvoices,
        ];

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
     * Summary of downloadExcel
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadExcel(Request $request)
    {
        $invoiceReports = Invoice::query();
        if ($request->has("from_date") && $request->has("to_date")) {
            $invoiceReports = $this->filterByDateRange($request->from_date . "|" . $request->to_date, $invoiceReports);
        }
        if ($request?->has('search')) {
            $invoiceReports = $this->search($request->search, $invoiceReports);
        }

        if ($request?->has('sort_by')) {
            $invoiceReports = $invoiceReports
                ->orderBy($request->sort_by, $request->sort_order ?? 'desc');
        }

        if ($request->has('multiple_filter')) {
            $invoiceReports = $this->filterMultipleFields($request->multiple_filter, $invoiceReports);
        }

        $fileName = 'invoice_report_' . time() . '.xlsx';
        $tempPath = storage_path('app/public/' . $fileName);

        $payments = Payment::selectRaw('consultation_id, include_in_invoice, payment_status, SUM(amount) as total')
            ->groupBy('consultation_id', 'include_in_invoice', 'payment_status')
            ->get()
            ->groupBy('consultation_id');

        $invoices = $invoiceReports->get();

        // Create a custom export with proper number formatting
        $data = $invoices->map(function ($invoice) use ($payments) {
            $cid = $invoice->consultation_id;

            $includeInvoiced = 0;
            $excludeInvoiced = 0;
            $completedAmt    = 0;
            $pendingAmt      = 0;

            if (isset($payments[$cid])) {
                foreach ($payments[$cid] as $p) {
                    if ($p->include_in_invoice == 1) {
                        $includeInvoiced = $p->total;
                    } elseif ($p->include_in_invoice == 0) {
                        $excludeInvoiced = $p->total;
                    }

                    if ($p->payment_status === PaymentStatusEnum::Completed->value) {
                        $completedAmt = $p->total;
                    } elseif ($p->payment_status === PaymentStatusEnum::Pending->value) {
                        $pendingAmt = $p->total;
                    }
                }
            }

            // Make sure all amount values are properly formatted as numbers
            // Convert to float and ensure no leading apostrophes or formatting issues
            $collectedAmount = 0;
            if (is_numeric($invoice->collected_amount)) {
                $collectedAmount = (float) $invoice->collected_amount;
            }

            $discountAmount = 0;
            if (is_numeric($invoice->discount_amount)) {
                $discountAmount = (float) $invoice->discount_amount;
            }

            $discountPercentage = 0;
            if (is_numeric($invoice->discount_percentage)) {
                $discountPercentage = (float) $invoice->discount_percentage;
            }

            $includeInvoicedAmount = 0;
            if (is_numeric($includeInvoiced)) {
                $includeInvoicedAmount = (float) $includeInvoiced;
            }

            $excludeInvoicedAmount = 0;
            if (is_numeric($excludeInvoiced)) {
                $excludeInvoicedAmount = (float) $excludeInvoiced;
            }

            $completedAmount = 0;
            if (is_numeric($completedAmt)) {
                $completedAmount = (float) $completedAmt;
            }

            $pendingAmount = 0;
            if (is_numeric($pendingAmt)) {
                $pendingAmount = (float) $pendingAmt;
            }

            $modeOfPayment = '';
            $count         = count($invoice->getPaymentTypeBreakdown());
            foreach ($invoice->getPaymentTypeBreakdown() as $type => $amount) {
                $modeOfPayment .= $type . ':' . $amount;
                if ($count > 1) {
                    $modeOfPayment .= ', ';
                }
                $count--;
            }
            if ($discountAmount > 0) {
                $discountAmount .= '(' . $discountPercentage . '%)';
            }

            return [
                'Date'                   => \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') ?? '',
                'Patient Name'           => $invoice->patient_name ?? '',
                'Patient Email'          => $invoice->patient_email ?? '',
                'Patient Phone'          => $invoice->patient_phone ?? '',
                'Patient Number'         => $invoice->patient_number ?? '',
                'Doctor Name'            => $invoice->doctor_name ?? '',
                'Doctor Email'           => $invoice->doctor_email ?? '',
                'Doctor Phone'           => $invoice->doctor_phone ?? '',
                'Referred By Name'       => $invoice->referred_by_name ?? '',
                'Total Amount'           => $invoice->currency . $collectedAmount + $pendingAmount,
                'Discount Amount'        => $invoice->currency . $discountAmount,
                'Collected Amount'       => $invoice->currency . $collectedAmount,
                // 'Included Invoice Amount' => $includeInvoicedAmount,
                // 'Excluded Invoice Amount' => $excludeInvoicedAmount,
                // 'Completed Payment Amount' => $completedAmount,
                'Pending Payment Amount' => $invoice->currency . $pendingAmount,
                'Mode of Payment'        => $modeOfPayment,
                // 'Payment Type' => $invoice->payment_type ?? '',
            ];
        });

        // Export the mapped data
        (new FastExcel($data))->export($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    // public function downloadExcel(Request $request)
    // {
    //     $invoiceReports = Invoice::query();

    //     if ($request?->has('search')) {
    //         $searchValue = $request->search;
    //         $invoiceReports = $this->search($searchValue, $invoiceReports);
    //     }

    //     if ($request?->has('sort_by')) {
    //         $sortBy = $request->sort_by ?? '';
    //         $sortOrder = $request->sort_order ?? 'desc';
    //         $invoiceReports = $invoiceReports->orderBy($sortBy, $sortOrder);
    //     }

    //     if ($request->has('multiple_filter')) {
    //         $invoiceReports = $this->filterMultipleFields($request->multiple_filter, $invoiceReports);
    //     }

    //     $fileName = 'invoice_report_' . time() . '.xlsx';
    //     $tempPath = storage_path('app/public/' . $fileName);

    //     $payments = Payment::selectRaw('consultation_id, include_in_invoice, SUM(amount) as total')
    //         ->groupBy('consultation_id', 'include_in_invoice')
    //         ->get()
    //         ->groupBy('consultation_id');

    //     $invoices = $invoiceReports->get();

    //     (new FastExcel($invoices))->export($tempPath, function ($row) use ($payments) {
    //         $consultationId = $row->consultation_id;

    //         $includeAmount = 0;
    //         $excludeAmount = 0;

    //         if (isset($payments[$consultationId])) {
    //             $grouped = $payments[$consultationId];

    //             foreach ($grouped as $payment) {
    //                 if ($payment->include_in_invoice == 1) {
    //                     $includeAmount = $payment->total;
    //                 } elseif ($payment->include_in_invoice == 0) {
    //                     $excludeAmount = $payment->total;
    //                 }
    //             }
    //         }

    //         return [
    //             'Patient Name' => $row->patient_name ?? '',
    //             'Patient Email' => $row->patient_email ?? '',
    //             'Patient Phone' => $row->patient_phone ?? '',
    //             'Patient Number' => $row->patient_number ?? '',
    //             'Doctor Name' => $row->doctor_name ?? '',
    //             'Doctor Email' => $row->doctor_email ?? '',
    //             'Doctor Phone' => $row->doctor_phone ?? '',
    //             'Referred By Name' => $row->referred_by_name ?? '',
    //             'Collected Amount' => $row->collected_amount ?? '',
    //             'Discount Amount' => $row->discount_amount ?? '',
    //             'Discount in Percent' => $row->discount_percentage ?? '',
    //             'Included Invoice Amount' => $includeAmount,
    //             'Excluded Invoice Amount' => $excludeAmount,
    //         ];
    //     });

    //     return response()->download($tempPath)->deleteFileAfterSend(true);
    // }

    // public function downloadExcel(Request $request)
    // {
    //     $invoiceReports = Invoice::query();
    //     if ($request?->has('search')) {
    //         $searchValue = $request->search;
    //         $invoiceReports = $this->search($searchValue, $invoiceReports);
    //     }

    //     if ($request?->has('sort_by')) {
    //         $sortBy = $request->sort_by ?? '';
    //         $sortOrder = $request->sort_order ?? 'desc';
    //         $invoiceReports = $invoiceReports->orderBy($sortBy, $sortOrder);
    //     }

    //     if ($request->has('multiple_filter')) {
    //         $invoiceReports = $this->filterMultipleFields($request->multiple_filter, $invoiceReports);
    //     }

    //     $fileName = 'invoice_report_' . time() . '.xlsx';
    //     $tempPath = storage_path('app/public/' . $fileName);
    //     // Create Excel file
    //     // return Excel::download(new Expense($data), 'orders-products-data.xlsx');
    //     // (new FastExcel(collect($invoiceReports)))->export($tempPath);
    //     (new FastExcel($invoiceReports->get()))
    //         ->export($tempPath, function ($row) {
    //             return [
    //                 'Patient Name' => $row->patient_name ?? '',
    //                 'Patient Email' => $row->patient_email ?? '',
    //                 'Patient Phone' => $row->patient_phone ?? '',
    //                 'Patient Number' => $row->patient_number ?? '',
    //                 'Doctor Name' => $row->doctor_name ?? '',
    //                 'Doctor Email' => $row->doctor_email ?? '',
    //                 'Doctor Phone' => $row->doctor_phone ?? '',
    //                 'Referred By Name' => $row->referred_by_name ?? '',
    //                 'Collected Amount' => $row->collected_amount ?? '',
    //                 // 'Balance Amount' => $row->balanced_amount ?? '',
    //                 'Discount Amount' => $row->discount_amount ?? '',
    //                 'Discount in Percent' => $row->discount_percentage ?? '',
    //             ];
    //         });

    //     // Return downloadable file
    //     return response()->download($tempPath)->deleteFileAfterSend(true);

    // }

}

<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Models\Invoice;
use App\Models\IPD;
use App\Models\IPDInvoiceItem;
use App\Models\Receipt;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class IPDBillingService
{
    private $invoiceService;
    private $invoiceColumns;
    private $paymentService;

    /**
     * Summary of __construct
     * @param \App\Services\InvoiceService $invoiceService
     * @param \App\Services\PaymentService $paymentService
     */
    public function __construct(

        InvoiceService $invoiceService,
        PaymentService $paymentService,

    ) {
        $this->invoiceService = $invoiceService;
        $this->paymentService = $paymentService;
        $this->invoiceColumns = Invoice::$columns;
    }

    public function all(?Request $request)
    {
        $query = Invoice::query()
            ->whereNotNull('ipd_id')
            ->with('receipt');

        if ($request?->filled('search')) {
            $search = $request->search;
            $ipdIds = IPD::where('ipd_number', 'like', "%{$search}%")
                ->orWhere('patient_number', 'like', "%{$search}%")
                ->orWhere('patient_name', 'like', "%{$search}%")
                ->pluck('id');

            $query->where(function ($q) use ($search, $ipdIds) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('patient_number', 'like', "%{$search}%")
                    ->orWhere('patient_name', 'like', "%{$search}%")
                    ->orWhere('patient_phone', 'like', "%{$search}%")
                    ->orWhereIn('ipd_id', $ipdIds);
            });
        }

        if ($request?->has('multiple_filter')) {
            $query = $this->filterMultipleFields($request->multiple_filter, $query);
        }

        if ($request?->filled('sort_by')) {
            $query->orderBy($request->sort_by, $request->sort_order === 'asc' ? 'asc' : 'desc');
        }

        $perPage  = $request?->input('per_page', env('PAGINATION', 15)) ?? env('PAGINATION', 15);
        $invoices = $query->paginate((int) $perPage);

        $invoices->getCollection()->transform(function (Invoice $invoice) {
            return $this->syncAndAppendBillingSummary($invoice);
        });

        return $invoices;
    }

    public function get(string $ipdId)
    {
        $ipd     = IPD::findOrFail($ipdId);
        $invoice = Invoice::where('ipd_id', $ipdId)->first();
        $this->syncAndAppendBillingSummary($invoice);

        $ipd->ward_name  = $ipd->ward->name;
        $ipd->room_name  = $ipd->room->name;
        $ipd->bed_number = $ipd->bed->bed_number;

        $data                  = [];
        $data['summary']       = $this->billingTotals($ipdId, $invoice?->id);
        $data['ipd']           = $ipd;
        $data['invoice_items'] = $this->itemsByCategory($ipdId);
        $data['invoice']       = $invoice;
        $data['receipts']      = $invoice ? $invoice->receipt : [];

        return $data;
    }

    #[Transactional(secure: true, requiredRole: null, description: 'Create or update IPD billing record within a secure transaction')]
    public function updateInvoice(Request $request, string $ipdId)
    {
        $ipd = IPD::findOrFail($ipdId);
        $invoice = Invoice::where('ipd_id', $ipdId)->first();
        if ($invoice) {
            if(is_null($ipd->discharge_date_time)) {
                $ipd->discharge_date_time = now();
                $ipd->status = 'Discharged';
                $ipd->save();
            } 
            $invoice->update([
                'ipd_billing_status' => $request->ipd_billing_status ?? $invoice->ipd_billing_status,
            ]);
            $this->syncAndAppendBillingSummary($invoice); 
        }

    }

    #[Transactional(secure: true, requiredRole: null, description: 'Create IPD billing receipt within a secure transaction')]
    public function addPayment(Request $request, string $ipdId)
    {
        $this->validatePayment($request);

        $invoice = Invoice::where('ipd_id', $ipdId)->first();
        if (! $invoice) {
            $invoice = $this->addOrUpdate(new Request([
                'currency' => $request->currency ?? '₹',
            ]), $ipdId);
        }

        $receipt = Receipt::create([
            'invoice_id'     => $invoice->id,
            'amount'         => $request->amount,
            'currency'       => $request->currency ?? ($invoice->currency ?? '₹'),
            'date'           => $request->date ?? now(),
            'payment_type'   => $request->payment_type,
            'transaction_id' => $request->transaction_id ?? '',
            'status'         => $request->status ?? 'Completed',
            'notes'          => $request->notes ?? '',
        ]);

        $this->syncAndAppendBillingSummary($invoice->fresh('receipt'));
    }

    #[Transactional(secure: true, requiredRole: null, description: 'Create IPD billing Invoice Item within a secure transaction')]
    public function addCharges(Request $request, string $ipdId)
    {
        $ipd       = IPD::findOrFail($ipdId);
        $invoice   = Invoice::where('ipd_id', $ipdId)->first();
        $frontDesk = User::where('id', $request->front_desk_user_id)->first();

        $this->validateCharges($request);
        IPDInvoiceItem::create([
            'invoice_id'            => $invoice->id,
            'category'              => $request->category,
            'description'           => $request->description,
            'amount'                => $request->amount,
            'ipd_id'                => $ipdId,
            'front_desk_user_id'    => $request->front_desk_user_id ?? null,
            'front_desk_user_name'  => $frontDesk->name,
            'front_desk_user_phone' => $frontDesk->phone,
            'front_desk_user_email' => $frontDesk->email,
            'service_category'      => $request->service_category,
            'currency'              => $request->currency ?? '₹',
            'description'           => $request->description,
            'tax_percent'           => $request->tax_percent ?? 0,
            'tax_amount'            => round($request->amount - (($request->amount * $request->tax_percent) / (100 + $request->tax_percent)), 2) ?? 0,
            'service_date'          => $request->service_date ?? null,
        ]);

        $this->syncAndAppendBillingSummary($invoice->fresh('receipt'));

    }

    private function validateCharges(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'amount'           => 'required|numeric|min:0',
            'service_category' => 'required|string|max:100',
            'description'      => 'nullable|string|max:255',
            'tax_percent'      => 'required|numeric|min:0|max:100',
            'service_date'     => 'nullable|date',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validatePayment(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'amount'         => 'required|numeric|min:0',
            'currency'       => 'nullable|string|max:10',
            'date'           => 'nullable|date',
            'payment_type'   => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:255',
            'status'         => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function paymentDetails(string $ipdId)
    {
        $invoice = Invoice::where('ipd_id', $ipdId)->firstOrFail();

        return Receipt::where('invoice_id', $invoice->id)->get();
    }

    private function filterMultipleFields($request, $query)
    {
        foreach (['ipd_id', 'invoice_number', 'patient_number', 'patient_name', 'patient_phone', 'doctor_name'] as $field) {
            if (! empty($request[$field])) {
                $operator = in_array($field, ['patient_name', 'doctor_name']) ? 'like' : '=';
                $value    = $operator === 'like' ? '%' . $request[$field] . '%' : $request[$field];
                $query->where($field, $operator, $value);
            }
        }

        return $query;
    }

    private function syncAndAppendBillingSummary(Invoice $invoice): Invoice
    {
        $totals = $this->billingTotals($invoice->ipd_id, $invoice->id);
        $invoice->update([
            'collected_amount' => $totals['paid_amount'],
            'balanced_amount'  => $totals['balance_amount'],
        ]);

        return $this->appendBillingSummary($invoice->fresh('receipt'));
    }

    private function billingStatus(float $totalAmount, float $paidAmount): string
    {
        if ($totalAmount <= 0 || $paidAmount <= 0) {
            return 'Running';
        }

        if ($paidAmount >= $totalAmount) {
            return 'Paid';
        }

        return 'Partial';
    }

    private function billingTotals(string $ipdId, ?string $invoiceId = null): array
    {
        $items         = IPDInvoiceItem::where('ipd_id', $ipdId)->get();
        $itemAmount    = (float) $items->sum('amount');
        $taxAmount     = (float) $items->sum('tax_amount');
        $totalAmount   = $itemAmount;
        $paidAmount    = $invoiceId ? (float) Receipt::where('invoice_id', $invoiceId)->sum('amount') : 0;
        $balanceAmount = max($totalAmount - $paidAmount, 0);

        return [
            'item_amount'    => $itemAmount,
            'tax_amount'     => $taxAmount,
            'total_amount'   => $totalAmount,
            'paid_amount'    => $paidAmount,
            'balance_amount' => $balanceAmount,
            'billing_status' => $this->billingStatus($totalAmount, $paidAmount),
        ];
    }

    private function appendBillingSummary(Invoice $invoice): Invoice
    {
        $totals = $this->billingTotals($invoice->ipd_id, $invoice->id);

        $invoice->setAttribute('invoice_items', $this->itemsByCategory($invoice->ipd_id));
        $invoice->setAttribute('receipt_total', $totals['paid_amount']);
        $invoice->setAttribute('item_amount', $totals['item_amount']);
        $invoice->setAttribute('tax_amount', $totals['tax_amount']);
        $invoice->setAttribute('total_amount', $totals['total_amount']);
        $invoice->setAttribute('collected_amount', $totals['paid_amount']);
        $invoice->setAttribute('balanced_amount', $totals['balance_amount']);
        $invoice->setAttribute('billing_status', $totals['billing_status']);
        $invoice->setAttribute('ipd', IPD::find($invoice->ipd_id));

        return $invoice;
    }

    private function itemsByCategory(string $ipdId)
    {
        return IPDInvoiceItem::where('ipd_id', $ipdId)
            ->orderBy('service_category')
            ->orderBy('service_date')
            ->get()
            ->groupBy('service_category')
            ->map(function ($items, $category) {
                return [
                    'service_category' => $category,
                    'item_amount'      => (float) $items->sum('amount'),
                    'tax_amount'       => (float) $items->sum('tax_amount'),
                    'total_amount'     => (float) $items->sum('amount') + (float) $items->sum('tax_amount'),
                    'items'            => $items->values(),
                ];
            })
            ->values();
    }

    public function updateCharges(Request $request, string $id)
    {
        $invoiceItem = IPDInvoiceItem::findOrFail($id);
        $invoice     = Invoice::findOrFail($invoiceItem->invoice_id);
        $invoiceItem->update($request->all());
        $this->syncAndAppendBillingSummary($invoice);

    }

    public function deleteCharges(string $id)
    {
        $invoiceItem = IPDInvoiceItem::findOrFail($id);
        $invoice     = Invoice::findOrFail($invoiceItem->invoice_id);
        $this->syncAndAppendBillingSummary($invoice);
        $invoiceItem->delete();
    }
}

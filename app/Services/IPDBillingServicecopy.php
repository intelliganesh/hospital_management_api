<?php

namespace App\Services;

use App\Attributes\Transactional;
use App\Enums\ServiceType;
use App\Models\IPD;
use App\Models\IPDInvoiceItem;
use App\Models\Invoice;
use App\Models\Receipt;
use AutoIdGenerate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class IPDBillingService
{
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

        $perPage = $request?->input('per_page', env('PAGINATION', 15)) ?? env('PAGINATION', 15);
        $invoices = $query->paginate((int) $perPage);

        $invoices->getCollection()->transform(function (Invoice $invoice) {
            return $this->syncAndAppendBillingSummary($invoice);
        });

        return $invoices;
    }

    public function get(string $ipdId)
    {
        $ipd = IPD::with('invoiceItems')->findOrFail($ipdId);
        $invoice = Invoice::where('ipd_id', $ipdId)->with('receipt')->first();

        return [
            'ipd' => $ipd,
            'billing' => $invoice ? $this->syncAndAppendBillingSummary($invoice) : null,
            'invoice_items' => $this->itemsByCategory($ipdId),
            'receipts' => $invoice ? $invoice->receipt : [],
            'summary' => $this->billingTotals($ipdId, $invoice?->id),
        ];
    }

    #[Transactional(secure: true, requiredRole: null, description: 'Create or update IPD billing record within a secure transaction')]
    public function addOrUpdate(Request $request, string $ipdId)
    {
        $this->validateBilling($request);

        $ipd = IPD::findOrFail($ipdId);
        $invoice = Invoice::where('ipd_id', $ipdId)->first();

        $data = array_merge($this->invoiceSnapshot($ipd), [
            'consultation_id' => $ipd->consultation_id,
            'ipd_id' => $ipd->id,
            'collected_amount' => 0,
            'balanced_amount' => 0,
            'discount_amount' => $request->discount_amount ?? ($invoice->discount_amount ?? 0),
            'discount_percentage' => $request->discount_percentage ?? ($invoice->discount_percentage ?? null),
            'comment' => $request->comment ?? ($invoice->comment ?? null),
            'currency' => $request->currency ?? ($invoice->currency ?? 'INR'),
        ]);

        if (! $invoice) {
            $data['invoice_number'] = AutoIdGenerate::generateId(ServiceType::Invoice);
        }

        $invoice = Invoice::updateOrCreate(['ipd_id' => $ipd->id], $data);

        if ($request->has('items')) {
            $this->syncInvoiceItems($request->items, $ipd, $data['currency']);
        }

        return $this->syncAndAppendBillingSummary($invoice->fresh('receipt'));
    }

    #[Transactional(secure: true, requiredRole: null, description: 'Create IPD billing receipt within a secure transaction')]
    public function addPayment(Request $request, string $ipdId)
    {
        $this->validatePayment($request);

        $invoice = Invoice::where('ipd_id', $ipdId)->first();
        if (! $invoice) {
            $invoice = $this->addOrUpdate(new Request([
                'currency' => $request->currency ?? 'INR',
            ]), $ipdId);
        }

        $receipt = Receipt::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'currency' => $request->currency ?? ($invoice->currency ?? 'INR'),
            'date' => $request->date ?? now(),
            'payment_type' => $request->payment_type,
            'transaction_id' => $request->transaction_id ?? '',
            'status' => $request->status ?? 'Completed',
            'notes' => $request->notes ?? '',
        ]);

        return [
            'invoice' => $this->syncAndAppendBillingSummary($invoice->fresh('receipt')),
            'receipt' => $receipt,
        ];
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
                $value = $operator === 'like' ? '%' . $request[$field] . '%' : $request[$field];
                $query->where($field, $operator, $value);
            }
        }

        return $query;
    }

    private function invoiceSnapshot(IPD $ipd): array
    {
        return [
            'patient_id' => $ipd->patient_id,
            'doctor_id' => $ipd->doctor_id,
            'patient_name' => $ipd->patient_name ?? '',
            'patient_email' => $ipd->patient_email ?? '',
            'patient_phone' => $ipd->patient_phone,
            'patient_number' => $ipd->patient_number ?? '',
            'doctor_name' => $ipd->doctor_name ?? '',
            'doctor_email' => $ipd->doctor_email ?? '',
            'doctor_phone' => $ipd->doctor_phone,
        ];
    }

    private function syncAndAppendBillingSummary(Invoice $invoice): Invoice
    {
        $totals = $this->billingTotals($invoice->ipd_id, $invoice->id);
        $invoice->update([
            'collected_amount' => $totals['paid_amount'],
            'balanced_amount' => $totals['balance_amount'],
        ]);

        return $this->appendBillingSummary($invoice->fresh('receipt'));
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

    private function billingTotals(string $ipdId, ?string $invoiceId = null): array
    {
        $items = IPDInvoiceItem::where('ipd_id', $ipdId)->get();
        $itemAmount = (float) $items->sum('amount');
        $taxAmount = (float) $items->sum('tax_amount');
        $totalAmount = $itemAmount + $taxAmount;
        $paidAmount = $invoiceId ? (float) Receipt::where('invoice_id', $invoiceId)->sum('amount') : 0;
        $balanceAmount = max($totalAmount - $paidAmount, 0);

        return [
            'item_amount' => $itemAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance_amount' => $balanceAmount,
            'billing_status' => $this->billingStatus($totalAmount, $paidAmount),
        ];
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
                    'item_amount' => (float) $items->sum('amount'),
                    'tax_amount' => (float) $items->sum('tax_amount'),
                    'total_amount' => (float) $items->sum('amount') + (float) $items->sum('tax_amount'),
                    'items' => $items->values(),
                ];
            })
            ->values();
    }

    private function syncInvoiceItems(array $items, IPD $ipd, string $currency): void
    {
        $receivedIds = [];

        foreach ($items as $item) {
            $payload = $this->invoiceItemPayload($item, $ipd, $currency);

            if (! empty($item['id'])) {
                $invoiceItem = IPDInvoiceItem::where('ipd_id', $ipd->id)->findOrFail($item['id']);
                $invoiceItem->update($payload);
                $receivedIds[] = $invoiceItem->id;
            } else {
                $invoiceItem = IPDInvoiceItem::create($payload);
                $receivedIds[] = $invoiceItem->id;
            }
        }

        IPDInvoiceItem::where('ipd_id', $ipd->id)
            ->whereNotIn('id', $receivedIds)
            ->delete();
    }

    private function invoiceItemPayload(array $item, IPD $ipd, string $currency): array
    {
        $amount = (float) ($item['amount'] ?? 0);
        $taxPercent = (float) ($item['tax_percent'] ?? 0);
        $taxAmount = array_key_exists('tax_amount', $item)
            ? (float) $item['tax_amount']
            : round($amount * ($taxPercent / 100), 2);

        return [
            'ipd_id' => $ipd->id,
            'amount' => $amount,
            'front_desk_user_id' => $item['front_desk_user_id'] ?? null,
            'front_desk_user_name' => $item['front_desk_user_name'] ?? null,
            'front_desk_user_email' => $item['front_desk_user_email'] ?? null,
            'front_desk_user_phone' => $item['front_desk_user_phone'] ?? null,
            'service_category' => $item['service_category'] ?? null,
            'currency' => $item['currency'] ?? $currency,
            'description' => $item['description'] ?? null,
            'tax_percent' => $taxPercent,
            'tax_amount' => $taxAmount,
            'service_date' => $item['service_date'] ?? now()->toDateString(),
        ];
    }

    private function validateBilling(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'comment' => 'nullable|string',
            'currency' => 'nullable|string|max:10',
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|uuid|exists:ipd_invoice_items,id',
            'items.*.amount' => 'required_with:items|numeric|min:0',
            'items.*.front_desk_user_id' => 'nullable|integer|exists:users,id',
            'items.*.front_desk_user_name' => 'nullable|string|max:255',
            'items.*.front_desk_user_email' => 'nullable|email|max:255',
            'items.*.front_desk_user_phone' => 'nullable|string|max:255',
            'items.*.service_category' => 'required_with:items|string|max:255',
            'items.*.currency' => 'nullable|string|max:10',
            'items.*.description' => 'nullable|string',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
            'items.*.service_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validatePayment(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'date' => 'nullable|date',
            'payment_type' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}

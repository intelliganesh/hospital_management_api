<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\IPD;
use App\Models\IPDInvoiceItem;
use App\Models\Receipt;
use App\Models\SystemSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Browsershot\Browsershot;

/**
 * Payment receipt PDF (A5) for OPD invoice payments and IPD payments / advances.
 * One receipt = one row of the `receipts` table.
 */
class ReceiptService
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    /** Generate the PDF for one receipt and return its public URL. */
    public function download(string $receiptId): string
    {
        $receipt = Receipt::withoutGlobalScopes()->where('id', $receiptId)->first();
        if (! $receipt) {
            throw new ModelNotFoundException('Receipt not found.');
        }
        return $this->render($receipt);
    }

    /** Generate the PDF for the most recent receipt of an invoice. */
    public function downloadLatestForInvoice(string $invoiceId): string
    {
        $receipt = Receipt::withoutGlobalScopes()
            ->where('invoice_id', $invoiceId)
            ->orderBy('receipt_number', 'desc')
            ->first();
        if (! $receipt) {
            throw new ModelNotFoundException('No receipt found for this invoice.');
        }
        return $this->render($receipt);
    }

    /** Receipt for an online (external) appointment whose payment was verified: latest receipt of its consultation's invoice. */
    public function downloadForExternalAppointment(string $externalAppointmentId): string
    {
        $consultationId = \App\Models\Consultations::where('external_appointment_id', $externalAppointmentId)->value('id');
        $invoiceId      = $consultationId ? Invoice::withoutGlobalScopes()->where('consultation_id', $consultationId)->value('id') : null;
        if (! $invoiceId) {
            throw new ModelNotFoundException('No receipt found for this appointment (payment not verified yet).');
        }
        return $this->downloadLatestForInvoice($invoiceId);
    }

    private function render(Receipt $receipt): string
    {
        $html = view('templates.downloads.receipt', $this->data($receipt))->render();

        $dir = 'pdfs/receipts';
        if (! Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }
        $fileName = 'receipt_' . $receipt->receipt_number . '_' . time() . '.pdf';
        $filePath = storage_path("app/public/{$dir}/{$fileName}");

        Browsershot::html($html)
            ->format('A5')
            ->margins(0, 0, 0, 0)
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->setOption('printBackground', true)
            ->savePdf($filePath);

        return asset("storage/{$dir}/{$fileName}");
    }

    /** Everything the template needs. */
    public function data(Receipt $receipt): array
    {
        $invoice = Invoice::withoutGlobalScopes()->where('id', $receipt->invoice_id)->first();
        if (! $invoice) {
            throw new ModelNotFoundException('Invoice not found for this receipt.');
        }

        $isIpd    = ! empty($invoice->ipd_id);
        $currency = $this->currency($receipt->currency, $invoice->currency);
        $amount   = (float) $receipt->amount;

        // Paid up to and including this receipt (receipt numbers are monotonic; same "all statuses count" rule as billing totals).
        $paidUpTo = (float) Receipt::withoutGlobalScopes()
            ->where('invoice_id', $invoice->id)
            ->where('receipt_number', '<=', $receipt->receipt_number)
            ->sum('amount');

        $total   = null;
        $balance = null;
        $reference = [];
        try {
            if ($isIpd) {
                $total = (float) IPDInvoiceItem::where('ipd_id', $invoice->ipd_id)->sum('total_amount');
                $ipd   = IPD::find($invoice->ipd_id);
                if ($ipd) {
                    $reference[] = ['IPD No', $ipd->ipd_number];
                }
            } elseif (! empty($invoice->consultation_id)) {
                $total = $this->invoiceService->payableTotal($invoice->consultation_id);
                $appt  = \App\Models\Consultations::where('id', $invoice->consultation_id)->value('appointment_number');
                if ($appt) {
                    $reference[] = ['OPD / Appointment No', $appt];
                }
            }
        } catch (\Throwable $e) {
            $total = null;
        }
        if ($isIpd && $total !== null && $total <= 0) {
            $total = null; // advance before any charges: a "0.00 bill / 0.00 balance" line would mislead
        }
        if ($total !== null) {
            $balance = max(round($total - $paidUpTo, 2), 0);
        }
        $reference[] = ['Invoice No', $invoice->invoice_number];

        // Collected by: stored at collection time; older receipts fall back to the user printing it.
        $collectedBy = $receipt->getAttribute('collected_by_name') ?: (Auth::user()->name ?? '');

        // OPD instalment saves rewrite receipts.date, so created_at is the true collection time there.
        $when = $isIpd ? ($receipt->date ?: $receipt->created_at) : ($receipt->created_at ?: $receipt->date);
        // IPD UI sends a date only (00:00:00): borrow the clock time from created_at for the same day.
        if ($isIpd && $when && $receipt->created_at) {
            $w = \Carbon\Carbon::parse($when);
            if ($w->format('H:i:s') === '00:00:00') {
                $c = \Carbon\Carbon::parse($receipt->created_at);
                $when = $w->setTime((int) $c->format('H'), (int) $c->format('i'), (int) $c->format('s'));
            }
        }

        $settings = null;
        $userSettingsId = Auth::user()->system_settings_id ?? null;
        if ($userSettingsId) {
            $settings = SystemSettings::where('id', $userSettingsId)->first();
        }
        $settings = $settings ?: SystemSettings::first();

        return [
            'settings'       => $settings,
            'logo'           => $this->logoDataUri($settings),
            'receipt_number' => $receipt->receipt_number,
            'when'           => $when ? \Carbon\Carbon::parse($when)->timezone(config('app.timezone'))->format('d/m/Y h:i A') : '',
            'patient_name'   => $invoice->patient_name,
            'patient_number' => $invoice->patient_number,
            'reference'      => $reference,
            'payment_type'   => $receipt->payment_type,
            'transaction_id' => $receipt->transaction_id,
            'notes'          => $receipt->notes,
            'currency'       => $currency,
            'amount'         => $amount,
            'amount_text'    => $this->money($amount),
            'amount_words'   => $this->amountInWords($amount, $currency),
            'total'          => $total !== null ? $this->money($total) : null,
            'balance'        => $balance !== null ? $this->money($balance) : null,
            'collected_by'   => $collectedBy,
            'is_ipd'         => $isIpd,
        ];
    }

    private function money(float $v): string
    {
        return number_format($v, 2, '.', ',');
    }

    /** receipts.currency is unreliable (OPD save writes the notes text there). Accept only short symbol/codes. */
    private function currency($receiptCurrency, $invoiceCurrency): string
    {
        foreach ([$receiptCurrency, $invoiceCurrency] as $c) {
            $c = trim((string) $c);
            if ($c !== '' && $c !== '?' && mb_strlen($c) <= 4) {
                return $c === 'INR' || strtolower($c) === 'rs' || strtolower($c) === 'rs.' ? '₹' : $c;
            }
        }
        return '₹';
    }

    /** Logo as a data URI so Chrome never has to fetch it from the (single-threaded) dev server. */
    private function logoDataUri($settings): ?string
    {
        $candidates = [];
        $logo = $settings->hospital_logo ?? null;
        if ($logo) {
            $candidates[] = public_path('images/' . ltrim($logo, '/'));
            $candidates[] = public_path(ltrim($logo, '/'));
            $candidates[] = storage_path('app/public/' . ltrim($logo, '/'));
        }
        $candidates[] = public_path('logo.png'); // same hard-coded fallback the other templates use
        foreach ($candidates as $path) {
            if (is_file($path) && is_readable($path)) {
                $mime = @mime_content_type($path) ?: 'image/png';
                if (strpos($mime, 'image/') === 0) {
                    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                }
            }
        }
        return null;
    }

    /** Indian-system amount in words. Rupees/Paise for INR, otherwise "<symbol> ... Only". */
    private function amountInWords(float $amount, string $currency): string
    {
        $amount = round($amount, 2);
        $whole  = (int) floor($amount);
        $paise  = (int) round(($amount - $whole) * 100);
        if ($paise === 100) {
            $whole++;
            $paise = 0;
        }
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
            'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = [2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty', 6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'];

        $two = function (int $n) use ($ones, $tens): string {
            if ($n < 20) {
                return $ones[$n];
            }
            return $tens[intdiv($n, 10)] . ($n % 10 ? ' ' . $ones[$n % 10] : '');
        };
        $conv = function (int $n) use (&$conv, $ones, $two): string {
            $r = '';
            if ($n >= 10000000) {
                $r .= $conv(intdiv($n, 10000000)) . ' Crore ';
                $n %= 10000000;
            }
            if ($n >= 100000) {
                $r .= $two(intdiv($n, 100000)) . ' Lakh ';
                $n %= 100000;
            }
            if ($n >= 1000) {
                $r .= $two(intdiv($n, 1000)) . ' Thousand ';
                $n %= 1000;
            }
            if ($n >= 100) {
                $r .= $ones[intdiv($n, 100)] . ' Hundred ';
                $n %= 100;
                if ($n > 0) {
                    $r .= 'and ';
                }
            }
            if ($n > 0) {
                $r .= $two($n);
            }
            return trim($r);
        };

        $isInr = $currency === '₹';
        $words = $whole === 0 ? 'Zero' : $conv($whole);
        $text  = ($isInr ? 'Rupees ' : $currency . ' ') . $words;
        if ($paise > 0) {
            $text .= ' and ' . $two($paise) . ($isInr ? ' Paise' : ' Cents');
        }
        return $text . ' Only';
    }
}

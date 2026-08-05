<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
    }

    .title {
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 4px;
    }

    .container {
        width: 100%;
        border: 1px solid #000;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td,
    th {
        border: 1px solid #000;
        padding: 3px;
        vertical-align: top;
    }

    .no-border {
        border: none !important;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }
    .text-left{
        text-align: left;
    }

    .bold {
        font-weight: bold;
    }

    .small {
        font-size: 11px;
    }

    .category-row td {
        /* font-weight: bold; */
        text-transform: uppercase;
        padding-top: 6px;
        padding-bottom: 2px;
    }

    .blank-area {
        height: 200px;
    }

    .footer-space {
        height: 45px;
    }
    .no-border,
    .no-border th,
    .no-border td {
        border: none;
    }
    </style>
</head>

<body>
    <div class="title">Inpatient Bill</div>
    <div class="container">
        <table>
            <tr>
                <td width="48%">
                    <b>Bill No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $bill->bill_no ?? '' }}</b><br>
                    <b>Bill Date &nbsp;&nbsp;&nbsp; : {{ !empty($bill->bill_date) ? \Carbon\Carbon::parse($bill->bill_date)->format('d-m-Y') : '' }}</b>
                </td>
                <td width="52%">
                    <b>MR No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $ipd->surgery_report?->mr_no ?? '' }}</b><br>
                    <b>Patient's Name &nbsp; : {{ $ipd->patient_name ?? '' }}</b><br>
                    <b>Age/Gender &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $ipd->patient_age ?? '' }} / {{ $ipd->patient->gender ?? '' }}</b>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="no-border">
                        <tr>
                            <td width="50%">
                                <b>I.P. No</b>
                            </td>
                            <td width="50%">
                                <b>: {{ $ipd->ipd_number ?? '' }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%">
                                <b>Admission Date & Time</b>
                            </td>
                            <td width="50%">
                                : {{ !empty($ipd->admission_date_time) ? \Carbon\Carbon::parse($ipd->admission_date_time)->format('d-m-Y / h:i A') : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td width="50%">
                                <b>Discharge Date & Time</b>
                            </td>
                            <td width="50%">
                                : {{ !empty($ipd->discharge_date_time) ? \Carbon\Carbon::parse($ipd->discharge_date_time)->format('d-m-Y / h:i A') : '' }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="no-border">
                        <tr>
                            <td width="50%">
                                <b>Doctor</b>
                            </td>
                            <td width="50%">
                                <b>: {{ $ipd->doctor_name ?? '' }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%">
                                <b>Anaesthetist</b>
                            </td>
                            <td width="50%">
                                <b>: {{ $ipd->surgery_report?->pluck('anaesthetist')->implode(', ') ?? '' }}</b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="no-border" style="border:1px solid black !important;">
            <thead>
                <tr style="border:1px solid black !important; border-left:none;border-right:none;">
                    <th width="48%" class="text-left">Ward Particulars<br>Service Particulars</th>
                    <th width="13%">Rate/Day</th>
                    <th width="10%">Tax %</th>
                    <th width="10%">Days<br>Count</th>
                    <th width="19%">Amount<br>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($bill->invoice_items ?? []) as $category)
                <tr class="category-row" @if($loop->last) style="border-bottom: 1px solid #000;" @endif>
                    <td>{{ $category->category ?? '' }}</td>
                    <td class="text-right">{{ !empty($category->rate) ? number_format($category->rate, 2) : '' }}</td>
                    <td class="text-right">{{ $category->tax_percent ?? '' }}</td>
                    <td class="text-right">{{ $category->days_count ?? '' }}</td>
                    <td class="text-right">{{ number_format($category->amount ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No billing items found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <table class="no-border" style="border:1px solid black !important; margin-top: 5px;border-left:none;border-right:none;  ">
            <thead>
                <tr style="border:1px solid black !important;">
                    <td width="48%" class="bold">Professional Charges</td>
                    <td width="13%" class="text-center bold">Fee</td>
                    <td width="10%" class="text-center bold">GST%</td>
                    <td width="10%" class="text-center bold">Visits</td>
                    <td width="19%" class="text-center bold">Amount</td>
                </tr>
            </thead>
            @if(!empty($bill->professional_charges) && count($bill->professional_charges) > 0)
            <tbody>
                @foreach($bill->professional_charges as $charge)
                <tr @if($loop->last) style="border-bottom: 1px solid #000;" @endif>
                    <td>{{ $charge->category ?? '' }}</td>
                    <td class="text-right">{{ !empty($charge->rate) ? number_format($charge->rate, 2) : '' }}</td>
                    <td class="text-right">{{ $charge->tax_percent ?? '' }}</td>
                    <td class="text-right">{{ $charge->days_count ?? '' }}</td>
                    <td class="text-right">{{ number_format($charge->amount ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            @endif
        </table>
        <table>
            <tr>
                <td width="62%" class="no-border"></td>
                <td width="22%" class="bold no-border">Total</td>
                <td width="16%" class="text-right bold no-border">
                    {{ number_format($bill->total_amount ?? 0, 2) }}
                </td>
            </tr>
            <tr>
                <td class="no-border"></td>
                <td class="bold no-border">Net Bill Amount R/O</td>
                <td class="text-right bold no-border">
                    {{ number_format($bill->net_amount ?? 0, 2) }}
                </td>
            </tr>
            <tr>
                <td class="no-border"></td>
                <td class="bold no-border">Less Advance</td>
                <td class="text-right bold no-border">
                    {{ number_format($bill->advance_amount ?? 0, 2) }}
                </td>
            </tr>
            <tr>
                <td class="no-border"></td>
                <td class="bold no-border">Less Amount Received</td>
                <td class="text-right bold no-border">
                    {{ number_format($bill->received_amount ?? 0, 2) }}
                </td>
            </tr>
            <tr>
                <td class="no-border"></td>
                <td class="bold no-border">Balance</td>
                <td class="text-right bold" style="border-top:1px solid #000;">
                    {{ number_format($bill->balance_amount ?? 0, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="3" class="no-border">
                    <b>Rupees</b> {{ $bill->amount_in_words ?? '' }}
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th width="17%">Receipt No.</th>
                    <th width="17%">Receipt Dt.</th>
                    <th width="17%">Payment Type</th>
                    <th width="17%">Amount</th>
                    <th width="32%">Payment Mode</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($bill->receipts ?? []) as $receipt)
                <tr>
                    <td>{{ $receipt->receipt_number ?? '' }}</td>
                    <td class="text-center">
                        {{ !empty($receipt->date) ? \Carbon\Carbon::parse($receipt->date)->format('d-m-Y') : '' }}
                    </td>
                    <td class="text-center">{{ $receipt->notes ?? '' }}  {{ $receipt->transaction_id ?? '' }}</td>
                    <td class="text-right">{{ number_format($receipt->amount ?? 0, 2) }}</td>
                    <td>{{ $receipt->payment_type ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td class="blank-area no-border"></td>
            </tr>
            <tr>
                <td class="text-center bold no-border">
                    We wish you years of uninterrupted Good Health
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="2">
                    <b>For: ACHARYA SUSHRUTHA HEALTHCARE PVT. LTD.</b>
                </td>
            </tr>
            <tr class="footer-space">
                <td width="50%" class="no-border"></td>
                <td width="50%" class="no-border"></td>
            </tr>
            <tr>
                <td class="bold no-border">Authorized Signatory</td>
                <td class="text-center bold no-border">Hospital's Seal</td>
            </tr>
        </table>
    </div>
</body>

</html>

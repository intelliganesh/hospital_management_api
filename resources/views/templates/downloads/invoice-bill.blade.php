<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Bill</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            padding: 0px;
            margin: 0px;
            color: black;
        }

        p {
            line-height: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .section {
            margin-bottom: 10px;
            padding :0 15px;
        }

        .flex-row {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: start;
            font-size: 14px;
        }

        .flex-row p {
            margin: 4px 0;
        }

        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        .bill-table th,
        .bill-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            color: black;
        }

        /* .bill-table th {
            background-color: #333;
        } */

        .totals {
            /* margin-top: 10px; */
            text-align: right;
        }

        .totals p {
            margin: 4px 0;
        }

        .payment-info {
            font-size: 14px;
        }

        .label {
            font-weight: bold;
        }
    </style>
</head>

<body>

    @include('templates.downloads.letter_header', [
        'generic_letter_header' => true,
        'primary_color' => $primary_color,
        'letter_header_info' => $letter_header_info,
        'letter_header_address' => $billing_letter_header,
    ])
    <h2 style="font-size: 18px; text-align: center;">RECEIPT</h2>
    <div class="section">
    <table style="width: 100%; margin-bottom: 10px; font-size: 14px; border: none; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: top;">
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($created_at)->format('d-m-Y') }}</p>
                <p><strong>Bill No:</strong> {{ $invoice_number }}</p>

            </td>

            <td style="text-align: right; vertical-align: top;">
                <p>{{ $patient_name }}</p>
                <p>{{ $gender }}{{ $age ? ', ' . $age . ' yrs' : '' }}</p>
                {{-- <p>Dr.{{ $doctor_name . ($qualification ? ', ' . $qualification : '') }}</p>
                <p>{{ $designation }}</p> --}}
            </td>
        </tr>
    </table>
</div>



    <div class="section">
        <p class="label">Bill Details</p>
        <table class="bill-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Rate ({{!empty($paymentlistArray) ? $paymentlistArray[0]['currency'] : 'INR'}})</th>
                    {{-- <th>Discount Amount (Rs)</th> --}}
                    {{-- <th>Discount in (%) </th> --}}
                    {{-- <th>Total Amount (Rs)</th> --}}
                    {{-- <th>Payment Status</th> --}}
                </tr>
            </thead>
            <tbody>
                @if (!empty($test))
                    @foreach ($test as $item)
                        <tr>
                            <td>{{ $item->test_name ?? '' }} ({{ $item->test_number ?? '' }})</td>
                            <td>{{ $item->test_price ?? 0 }}</td>
                            <td>{{ ($item->test_price ?? 0) + ($item->tax_price ?? 0) }}</td>
                        </tr>
                    @endforeach
                @endif

                @if (!empty($paymentlistArray))
                    @foreach ($paymentlistArray as $item)
                        <tr>
                            <td>{{ $item['amount_for'] ?? '' }}</td>
                            <td>{{ $item['amount'] ?? 0 }}</td>
                            {{-- <td>{{ $item['discount_amount'] ?? 0 }}</td> --}}
                            {{-- <td>{{ $item['amount'] ?? 0 }}</td> --}}
                            {{-- <td>{{ $item['discount_percentage'] ?? 0 }} %</td> --}}
                            {{-- <td>{{ $item['payment_status'] }}</td> --}}
                        </tr>
                    @endforeach
                @endif

            </tbody>
        </table>
    </div>

    <div class="section">
    <table style="width: 100%; font-size: 14px; ">
        <tr>
            <td style="vertical-align: top;">
                <p><strong>Payment Mode:</strong> {{ $payment_type }}</p>
                @if (!empty($transaction_id))
                    <p><strong>Transaction Id:</strong> {{ $transaction_id }}</p>
                @endif
                <p><strong>Payment Status:</strong> {{ $payment_status }}</p>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <p><strong>Sub Total:</strong> ({{!empty($paymentArray) ? $paymentArray[0]['currency'] : 'INR'}}).&nbsp; {{ $total_amount}}</p>
                @if (!empty($discount_total_amount) && isset($discount_total_amount) && $discount_total_amount > 0)
                    <p>
                        <strong>Discount Amount:</strong> ({{!empty($paymentArray) ? $paymentArray[0]['currency'] : 'INR'}}). &nbsp;
                        {{ $discount_amount }}
                    </p>
                @endif
                @if (isset($paymentArray) && !empty($paymentArray))
                    <p><strong>Total:</strong>
                        ({{!empty($paymentArray) ? $paymentArray[0]['currency'] : 'INR'}}).
                        @if (isset($paymentArray))
                            @if (!empty($paymentArray[0]) && isset($paymentArray[0]))

                                    {{ $discount_total_amount }}

                            @endif
                            @if (
                                !empty($paymentArray[0]) &&
                                    isset($paymentArray[0]) &&
                                    isset($paymentArray[0]['discount_percentage']) &&
                                    !empty($paymentArray[0]['discount_percentage']))
                                ({{ $paymentArray[0]['discount_percentage'] }}%)
                            @endif
                        @endif
                    </p>
                @endif
            </td>
        </tr>
    </table>
</div>

</body>

</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payment Voucher</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 20px;
        color: #000;
    }

    .header h2 {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }

    .header p {
        margin: 2px 0;
        font-size: 11px;
    }

    .voucher-title {
        text-align: center;
        font-weight: bold;
        border: 1px solid #000;
        padding: 6px;
        background: #333;
        color: #fff;
        font-size: 14px;
        margin: 10px 0;
        text-transform: uppercase;
    }

    .field {
        margin: 15px 0;
        border-bottom: 1px solid #000;
        padding-bottom: 4px;

    }

    .field span {
        font-weight: bold;
        margin-right: 5px;
    }

    .checkbox {
        display: inline-block;
        border: 1px solid #000;
        width: 14px;
        height: 14px;
        margin-right: 5px;
        text-align: center;
        vertical-align: middle;
    }

    .signature-line {
        border-top: 1px solid #000;
        height: 20px;
        margin-top: 5px;
    }
    </style>
</head>

<body>
    @php
        function numberToWords($num) {
            $ones = array(
                0 => "Zero",
                1 => "One",
                2 => "Two",
                3 => "Three",
                4 => "Four",
                5 => "Five",
                6 => "Six",
                7 => "Seven",
                8 => "Eight",
                9 => "Nine",
                10 => "Ten",
                11 => "Eleven",
                12 => "Twelve",
                13 => "Thirteen",
                14 => "Fourteen",
                15 => "Fifteen",
                16 => "Sixteen",
                17 => "Seventeen",
                18 => "Eighteen",
                19 => "Nineteen"
            );
            $tens = array(
                0 => "",
                1 => "",
                2 => "Twenty",
                3 => "Thirty",
                4 => "Forty",
                5 => "Fifty",
                6 => "Sixty",
                7 => "Seventy",
                8 => "Eighty",
                9 => "Ninety"
            );
            $hundreds = array(
                "Hundred",
                "Thousand",
                "Million",
                "Billion",
                "Trillion",
                "Quadrillion"
            );

            $num = number_format($num, 2, ".", ",");
            $num_arr = explode(".", $num);
            $wholenum = $num_arr[0];
            $decnum = $num_arr[1];
            $whole_arr = array_reverse(explode(",", $wholenum));
            krsort($whole_arr);
            $rettxt = "";

            foreach($whole_arr as $key => $i) {
                if($i < 20) {
                    $rettxt .= $ones[$i];
                } elseif($i < 100) {
                    $rettxt .= $tens[substr($i, 0, 1)];
                    if(substr($i, 1, 1) != '0') {
                        $rettxt .= " " . $ones[substr($i, 1, 1)];
                    }
                } else {
                    $rettxt .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
                    if(substr($i, 1, 1) != '0') {
                        $rettxt .= " " . $tens[substr($i, 1, 1)];
                    }
                    if(substr($i, 2, 1) != '0') {
                        $rettxt .= " " . $ones[substr($i, 2, 1)];
                    }
                }

                if($key > 0) {
                    $rettxt .= " " . $hundreds[$key] . " ";
                }
            }

            if($decnum > 0) {
                $rettxt .= " and ";
                if($decnum < 20) {
                    $rettxt .= $ones[$decnum];
                } elseif($decnum < 100) {
                    $rettxt .= $tens[substr($decnum, 0, 1)];
                    if(substr($decnum, 1, 1) != '0') {
                        $rettxt .= " " . $ones[substr($decnum, 1, 1)];
                    }
                }
                $rettxt .= " Paise";
            }

            return $rettxt;
        }
    @endphp
    <!-- Header -->
    <div class="header">
        @include('templates.downloads.letter_header', [
        'generic_letter_header' => true,
        'letter_header_address' => $billing_letter_header,
        ])
    </div>
    <!-- Voucher Title -->
    <div class="voucher-title">PAYMENT VOUCHER</div>
    <!-- Voucher Info Table -->
    <table width="100%" border="0" cellspacing="0" cellpadding="5" style="margin-bottom:15px; border-collapse: collapse;">
        <tr>
            <td style="width:1%"><strong>No:</strong></td>
            <td style="border:1px solid #000; width:15%;height:20px;"> {{ $expense->voucher_number ?? '' }}</td>
            <td style="width:5%;text-align:right;"><strong>Date:</strong></td>
            <td style="border:1px solid #000; width:15%;height:20px;"> {{ \Carbon\Carbon::parse($expense->created_at)->format('d/m/Y') }}</td>
            <td style="width:5%;text-align:right;"><strong>₹</strong></td>
            <td style="border:1px solid #000; width:15%;height:20px;"> {{ number_format($expense->amount, 2) }}</td>
        </tr>
    </table>
    <!-- Fields -->
    <div class="field"><span>Pay To:</span> {{ $expense->expense_name ?? '' }}</div>
    <div class="field"><span>Rupees (in words):</span>{{ numberToWords($expense->amount) . " Only" }}</div>
    <div class="field"><span>Being:</span> {{ $expense->description ?? '' }}</div>
    <!-- Footer Section -->
    <p style="margin-top:40px;"><strong>DEBIT/CREDIT</strong></p>
    <table width="100%" border="0" cellspacing="0" cellpadding="5" style="margin-bottom:15px; border-collapse: collapse;">
        <tr>
            <td style="width:5%"><strong>Approved By</strong></td>
            <td style="border:1px solid #000; width:20%;height:30px;">{{ $expense->entered_name ?? '' }}</td>
            <td style="width:5%;text-align:center;padding-left:10px"><strong>Paid By</strong></td>
            <td style="border:1px solid #000; width:20%;height:30px;"> {{ $expense->for_name ?? '' }}</td>
        </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="5" style="margin-bottom:15px; border-collapse: collapse;">
        <tr>
            <td style="width:7.2% "><strong>Drawn On</strong></td>
            <td style="border:1px solid #000; width:19.5%;height:30px;"> {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
            <td style=" width:10%;height:30px;"> </td>
            <td style="width:15%;text-align:right;" valign="bottom"><strong>Receiver's Signature</strong></td>
        </tr>
    </table>
</body>
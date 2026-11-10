<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $receipt_number }}</title>
    <style>
        @page { size: A5; margin: 0; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; margin: 0; padding: 0; }
        .page { width: 148mm; height: 209mm; padding: 8mm 9mm; position: relative; }
        .frame { border: 1px solid #333; height: 100%; padding: 6mm 6mm 5mm; position: relative; }
        .head { display: table; width: 100%; border-bottom: 2px solid #333; padding-bottom: 6px; }
        .head .logo { display: table-cell; width: 22mm; vertical-align: middle; }
        .head .logo img { max-width: 20mm; max-height: 20mm; }
        .head .txt { display: table-cell; vertical-align: middle; text-align: center; overflow-wrap: anywhere; }
        .head .txt h1 { margin: 2px 0; font-size: 15px; }
        .head .txt p { margin: 1px 0; font-size: 9.5px; }
        .title { text-align: center; font-size: 15px; font-weight: bold; letter-spacing: 3px; margin: 9px 0 7px; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.meta td { padding: 3px 0; vertical-align: top; }
        table.kv { width: 100%; border-collapse: collapse; }
        table.kv td { padding: 4px 6px; border: 1px solid #bbb; vertical-align: top; overflow-wrap: anywhere; word-break: break-word; }
        table.kv td.k { width: 36%; background: #f2f2f2; font-weight: bold; }
        .amount { margin-top: 10px; border: 2px solid #333; padding: 7px 10px; display: table; width: 100%; }
        .amount .l { display: table-cell; font-weight: bold; font-size: 12px; vertical-align: middle; }
        .amount .r { display: table-cell; text-align: right; font-size: 20px; font-weight: bold; vertical-align: middle; white-space: nowrap; }
        .words { margin-top: 6px; font-size: 11px; overflow-wrap: anywhere; }
        .words b { margin-right: 4px; }
        .sum { margin-top: 8px; width: 100%; border-collapse: collapse; }
        .sum td { padding: 3px 6px; border-bottom: 1px dotted #999; }
        .sum td.v { text-align: right; font-weight: bold; white-space: nowrap; }
        .thanks { margin-top: 14px; text-align: center; font-style: italic; font-size: 12px; }
        .sign { position: absolute; left: 6mm; right: 6mm; bottom: 5mm; display: table; width: calc(100% - 12mm); }
        .sign .c { display: table-cell; vertical-align: bottom; }
        .sign .c.r { text-align: right; }
        .sign .line { display: inline-block; width: 55mm; border-top: 1px solid #333; padding-top: 3px; text-align: center; font-size: 10px; }
        .small { font-size: 9px; color: #555; }
    </style>
</head>
<body>
<div class="page">
    <div class="frame">
        <div class="head">
            @if(!empty($logo))
                <div class="logo"><img src="{{ $logo }}" alt=""></div>
            @endif
            <div class="txt">
                @if(!empty($settings?->billing_letter_header))
                    {!! html_entity_decode($settings->billing_letter_header) !!}
                @else
                    <h1>{{ $settings->hospital_name ?? '' }}</h1>
                    <p>{{ $settings->address ?? '' }}</p>
                @endif
            </div>
            @if(!empty($logo))
                <div class="logo"></div>
            @endif
        </div>

        <div class="title">RECEIPT</div>

        <table class="meta">
            <tr>
                <td><b>Receipt No:</b> {{ $receipt_number }}</td>
                <td style="text-align:right;"><b>Date:</b> {{ $when }}</td>
            </tr>
        </table>

        <table class="kv">
            <tr><td class="k">Received from</td><td>{{ $patient_name }}</td></tr>
            <tr><td class="k">Patient No</td><td>{{ $patient_number }}</td></tr>
            @foreach($reference as $ref)
                <tr><td class="k">{{ $ref[0] }}</td><td>{{ $ref[1] }}</td></tr>
            @endforeach
            <tr><td class="k">Payment mode</td><td>{{ $payment_type ?: '-' }}</td></tr>
            @if(!empty($transaction_id))
                <tr><td class="k">Transaction ID</td><td>{{ $transaction_id }}</td></tr>
            @endif
            @if(!empty($notes))
                <tr><td class="k">Remarks</td><td>{{ $notes }}</td></tr>
            @endif
        </table>

        <div class="amount">
            <div class="l">Amount received</div>
            <div class="r">{{ $currency }} {{ $amount_text }}</div>
        </div>
        <div class="words"><b>In words:</b> {{ $amount_words }}</div>

        @if($total !== null || $balance !== null)
            <table class="sum">
                @if($total !== null)
                    <tr><td>{{ $is_ipd ? 'Total bill so far' : 'Total payable' }}</td><td class="v">{{ $currency }} {{ $total }}</td></tr>
                @endif
                @if($balance !== null)
                    <tr><td>Balance after this payment</td><td class="v">{{ $currency }} {{ $balance }}</td></tr>
                @endif
            </table>
        @endif

        <div class="thanks">Received with thanks</div>

        <div class="sign">
            <div class="c">
                <span class="small">Collected by:</span><br>
                <b>{{ $collected_by }}</b>
            </div>
            <div class="c r"><span class="line">Authorised Signature</span></div>
        </div>
    </div>
</div>
</body>
</html>

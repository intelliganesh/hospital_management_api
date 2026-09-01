<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Kannada&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
    }

    .container {
        width: 100%;
    }

    .title {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
        vertical-align: top;
    }

    td,
    th {
        border: 0.1rem solid #000;
        padding: 4px;
        border: none !important;
        vertical-align: top;
    }

    .medicine{
        width: auto;
        border-collapse: collapse;
        margin-top: 5px;
        vertical-align: top;
        margin:0px 5px;
    }
    .medicine td,.medicine th{
        border: 0.05rem solid #000 !important;
        padding: 4px;
        vertical-align: top;
    }

    .section-title {
        font-weight: bold;
        margin-left: 5px;
    }

    .text-center {
        text-align: center;
    }

    .no-border,
    .no-border th,
    .no-border td {
        border: none;
    }
    </style>
</head>

<body>
    @php
    $hasValue = fn($value) => !is_null($value) && trim((string) $value) !== '';
    @endphp
    <div class="title">Discharge Summary</div>
    <div class="container" style="border:1px solid">
        <!-- Top Details -->
        <table>
            <tr>
                <td width="50%">
                    <b>I.P. No :</b> {{ $ipd->ipd_number ?? '' }}<br>
                    <b>Patient's Name :</b> {{ $ipd->patient_name ?? '' }}<br>
                    <b>Age/Sex :</b> {{ $ipd->patient_age ?? '' }} /
                    {{ $ipd->patient->gender ?? '' }}<br>
                    <b>MR No :</b> {{ $ipd->surgery_report?->mr_no ?? '' }}<br>
                    <b>Address :</b><br>
                    {{ $ipd->patient?->address ?? '' }}
                </td>
                <td width="50%">
                    <b>Admission Date & Time :</b>
                    {{ $ipd->admission_date_time
                        ? \Carbon\Carbon::parse($ipd->admission_date_time)->format('d-m-Y | h:i A')
                        : ''
                    }}<br>

                    <b>Discharge Date & Time :</b>
                    {{ $ipd->discharge_date_time
                        ? \Carbon\Carbon::parse($ipd->discharge_date_time)->format('d-m-Y | h:i A')
                        : ''
                    }}<br><br>
                    <b>Doctor Incharge :</b><br>
                    {{ $ipd->discharge_summary?->doctor_incharge ?? '' }}
                </td>
            </tr>
        </table>
        <table style="border-top:1px solid">
            <tr>
                <td>
                    @if($hasValue($ipd->discharge_summary?->consultants ?? ''))
                    <b>Consultants:</b><br>
                    {{ $ipd->discharge_summary?->consultants ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->diagnosis ?? ''))
                    <b>Diagnosis:</b><br>
                    {{ $ipd->discharge_summary?->diagnosis ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->case_history ?? ''))
                    <b>Case History & Complaints:</b><br>
                    {{ $ipd->discharge_summary?->case_history ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->general_examination ?? ''))
                    <b>General Examination:</b><br>
                    {{ $ipd->discharge_summary?->general_examination ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->systemic_examination ?? ''))
                    <b>Systemic Examination:</b><br>
                    {{ $ipd->discharge_summary?->systemic_examination ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->investigations ?? ''))
                    <b>Investigations:</b><br>
                    {{ $ipd->discharge_summary?->investigations ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->operation_done ?? ''))
                    <b>Operation Done:</b><br>
                    {{ $ipd->discharge_summary?->operation_done ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->findings_and_procedure ?? ''))
                    <b>Findings And Procedure:</b><br>
                    {{ $ipd->discharge_summary?->findings_and_procedure ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->course_in_hospital ?? ''))
                    <b>Course In Hospital:</b><br>
                    {{ $ipd->discharge_summary?->course_in_hospital ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->patient_health_condition_at_discharge ?? ''))
                    <b>Patient's health condition at discharge:</b><br>
                    {{ $ipd->discharge_summary?->patient_health_condition_at_discharge ?? '' }}
                    <br><br>
                    @endif
                    @if($hasValue($ipd->discharge_summary?->advice_on_discharge ?? ''))
                    <b>Advice On Discharge:</b><br>
                    {!! nl2br($ipd->discharge_summary?->advice_on_discharge ?? '') !!}
                    @endif
                </td>
            </tr>
        </table>
        @php
        $medicineStr = $ipd->discharge_summary?->medicines ?? '';
        $rawArray = is_array($medicineStr) ? $medicineStr : explode(',', $medicineStr);
        // Filter out truly valid entries that have a medicine name
        $medicineArray = array_filter(
        array_map(function ($item) {
        $parts = explode('#', trim($item));
        return !empty($parts[0]) ? $parts : null;
        }, $rawArray),
        );
        @endphp
        <!-- Medicines -->
        @if (!empty($medicineArray))
        <div class="section">
            <p class="section-title">Medicines</p>
            <table class="medicine">
                <thead>
                    <tr>
                        <th style="width:30%;">Medicine</th>
                        <th>Dosage</th>
                        <th>Timing</th>
                        <th>With</th>
                        <th>Days</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicineArray as $parts)
                    <tr>
                        <td style="width:30%;">{{ ucwords(strtolower($parts[0] ?? '')) }}</td>
                        <td>{{ ucwords($parts[2] ?? '') }}</td>
                        <td>{{ ucwords(strtolower(str_replace(['-', '_'], ' ', $parts[3] ?? '')))}}</td>
                        <td>{{ ucwords(strtolower(str_replace(['-', '_'], ' ', $parts[4] ?? '')))}}</td>
                        <td>{{ $parts[5] ?? '-' }}</td>
                    </tr>
                    @endforeach
                    @if(!is_null($ipd->discharge_summary?->combination_medicines))
                    @php
                    $comboMedicine=json_decode($ipd->discharge_summary?->combination_medicines,true);
                    @endphp
                    @foreach($comboMedicine as $combo)
                    @php $medicine = collect($combo['combination_ingredients'])->map(function ($item) {
                    return ucwords(strtolower($item['combination_medicine'])) .
                    " (" . $item['combination_quantity'] . " " . $item['combination_unit'] . ")";
                    })->implode(' + ');
                    @endphp
                    <tr>
                        <td style="width:30%;">{{$medicine}}</td>
                        <td>{{$combo['combination_dosage'] ?? ''}}</td>
                        <td>{{ ucwords(strtolower(str_replace(['-', '_'], ' ', $combo['combination_timing'] ?? '')))}}</td>
                        <td>{{ ucwords(strtolower(str_replace(['-', '_'], ' ', $combo['combination_take_with'] ?? '')))}}</td>
                        <td>{{$combo['combination_medicine_days']}}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        @endif
        <!-- Tests -->
        @if (!empty($ipd->discharge_summary?->tests) && count(json_decode($ipd->discharge_summary?->tests, true)) > 0)
        <div class="section" style="margin-top: 10px">
            <p class="section-title">Tests</p>
            <ol>
                @foreach (json_decode($ipd->discharge_summary?->tests, true) as $test)
                <li>{{ $test['label'] }}</li>
                @endforeach
            </ol>
        </div>
        @endif

        @if (!empty($ipd->discharge_summary?->diet_plan) && count(json_decode($ipd->discharge_summary?->diet_plan, true)) > 0)
        <div class="section">
            <p class="section-title">Diet:</p>
            <ol>
                @foreach (json_decode($ipd->discharge_summary?->diet_plan, true) as $diet_plan)
                <li style="font-family: 'Noto Sans Kannada', 'Arial', sans-serif;">{{ $diet_plan['label'] }}</li>
                @endforeach
            </ol>
        </div>
        @endif
        <table>
            <tr>
                <!-- Space for Signature -->
            <tr style="height:120px">
                <td></td>
            </tr>
        </table>
        <!-- Footer -->
        <table style="border-top:1px solid">
            <tr>
                <td width="50%" style="border-right:1px solid !important">
                    <b>For ACHARYA SUSHRUTHA HEALTHCARE PVT. LTD.</b>
                </td>
                <td width="50%" class="text-center">
                    <b>ACHARYA SUSHRUTHA HEALTHCARE PVT. LTD.</b><br>
                    No. 479, 13th Cross, MPM Layout,<br>
                    Mallathahalli, Bengaluru – 560056.
                </td>
            </tr>
        </table>
    </div>
</body>

</html>

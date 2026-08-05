<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Prescription Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Kannada&display=swap" rel="stylesheet">
    <style>

        body {

            font-family: 'Arial', sans-serif;
            color: black;
            padding: 0px;
            margin: 0;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin-bottom: 5px;
            font-size: 20px;
        }

        p {
            line-height: 10px;
            font-size: 12px;
        }

        .header p {
            font-size: 12px;
            margin: 2px 0;
        }

        .section-title {
            font-weight: bold;
            margin: 10px 0 5px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        .section {
            margin-bottom: 10px;
            padding: 0px 15px
            /* white-space: pre-line; */
        }

        ol {
            padding-left: 14px;
        }

        ol li {
            font-size: 12px;
        }

        .footer-note {
            /* margin-top: 40px; */
            font-size: 14px;
        }

        .advice,
        .advice * {
            line-height: 1.2;
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
    {{-- <p>ದಿನನಿತ್ಯದ ಸೇವನೆಯಲ್ಲಿ ಕಡಿಮೆ ಬಳಸಿ</p>
    @php
        $html1 = mb_convert_encoding('ದಿನನಿತ್ಯದ ಸೇವನೆಯಲ್ಲಿ ಕಡಿಮೆ ಬಳಸಿ', 'HTML-ENTITIES', 'UTF-8');
    @endphp
    {{ $html1 }} --}}
    {{-- <h1>ದಿನನಿತ್ಯದ</h1> --}}
    <p style="text-align: center; font-size: 16px; font-weight: bold">Prescription</p>
    <div class="section" style="padding:0 10px;">
        <table  style="width: 100%;  font-size: 14px; border: none; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top; border: none;">
                    {{-- <p style="line-height:1;"><strong>Patient:</strong></p> --}}
                    <p>{{ $patient_name }}({{ $patient_number }})</p>
                    <p>{{ $gender }}, {{ $age }} yrs </p>
                </td>
                <td style="vertical-align: top; text-align: right; border: none;">
                    <p  style="line-height:0.5;">Dr.{{ $doctor_name . ($qualification ? ', ' . $qualification : '') }}</p>
                    <p style="line-height:0.5;" >{{ $designation }}</p>
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($created_at)->format('d/m/Y') }}</p>
                </td>
            </tr>
        </table>
    </div>

    @if(!is_null($referred_by_name))
    <div class="section">
        <p class="section-title">Referral Details</p>
        <div>
            <p><strong>Referred By:</strong>{{ $referred_by_name }}
                @if(!is_null($referred_by_phone_no))
                ({{ $referred_by_phone_no}} {{ ','.$referred_by_email ?? '' }})
                @endif
            </p>
            @if(!is_null($referred_by_hospital_name))
            <p><strong>Referred Hospital:</strong>{{ $referred_by_hospital_name }} </p>
            @endif
        </div>
    </div>
    @endif

    @if(!empty($protologyOrNonProctology['chief_complaints']) && count(json_decode($protologyOrNonProctology['chief_complaints'], true)) > 0)
    <div class="section" style="margin-top: 20px">
            <p class="section-title">Chief Complaints</p>
            <ol>
                @foreach (json_decode($protologyOrNonProctology['chief_complaints'], true) as $chief_complaint)
                    <li>{{ $chief_complaint['value'] }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    <!-- @if(!empty($protologyOrNonProctology['on_examination']) && count(json_decode($protologyOrNonProctology['on_examination'], true)) > 0)
    <div class="section" style="margin-top: 20px">
            <p class="section-title">Local Examination</p>
            <ol>
                @foreach (json_decode($protologyOrNonProctology['on_examination'], true) as $on_examination)
                    <li>{{ $on_examination['label'] }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    @if(!empty($protologyOrNonProctology['previous_scar']))
    <div class="section" style="margin-top: 20px">
            <p class="section-title">Previous Scar: <span style="font-weight: normal;">{{ $protologyOrNonProctology['previous_scar'] }}</span> &nbsp;&nbsp;&nbsp;&nbsp;
            Previous Scar Position: <span style="font-weight: normal;">{{ $protologyOrNonProctology['previous_scar_position'] ?? '-' }}</span></p>

        </div>
    @endif

    @if(!empty($protologyOrNonProctology['abscess']))
    <div class="section" style="margin-top: 20px">
            <p class="section-title">Abscess: <span style="font-weight: normal;">{{ $protologyOrNonProctology['abscess'] }}</span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Abscess Position: <span style="font-weight: normal;">{{ $protologyOrNonProctology['abscess_position'] ?? '-' }} </span></p>

        </div>
    @endif

    @if(!empty($protologyOrNonProctology['dre']) && count(json_decode($protologyOrNonProctology['dre'], true)) > 0)
    <div class="section" style="margin-top: 20px">
            <p class="section-title">DRE</p>
            <ol>
                @foreach (json_decode($protologyOrNonProctology['dre'], true) as $dre)
                    <li>{{ $dre['value'] }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    @if(!empty($protologyOrNonProctology['dre_induration_at']))
    <div class="section" style="margin-top: 20px">
            <p class="section-title">DRE Induction At: <span style="font-weight: normal;">{{ $protologyOrNonProctology['dre_induration_at'] ?? '-' }}</span></p>

        </div>
    @endif

    @if(!empty($protologyOrNonProctology['proctoscopy']) && count(json_decode($protologyOrNonProctology['proctoscopy'], true)) > 0)
    <div class="section" style="margin-top: 20px">
            <p class="section-title">Proctoscopy</p>
            <ol>
                @foreach (json_decode($protologyOrNonProctology['proctoscopy'], true) as $proctoscopy)
                    <li>{{ $proctoscopy['value'] }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    @if(!empty($protologyOrNonProctology['proctoscopy_anal_polyp_at']))
    <div class="section" style="margin-top: 20px">
            <p class="section-title">Anal Polyp At: <span style="font-weight: normal;">{{ $protologyOrNonProctology['proctoscopy_anal_polyp_at'] ?? '-' }}</span></p>

        </div>
    @endif

    @if(!empty($protologyOrNonProctology['diagnosis_summary']))
    <div class="section" style="margin-top: 20px">
            <p class="section-title">Diagnosis</p>
            <p>{{ $protologyOrNonProctology['diagnosis_summary'] }}
        </div>
    @endif -->

    @if(!empty($protologyOrNonProctology['co_morbidities']) && count(json_decode($protologyOrNonProctology['co_morbidities'], true)) > 0)
    <div class="section" style="margin-top: 20px">
            <p class="section-title">Co Morbities</p>
            <ol>
                @foreach (json_decode($protologyOrNonProctology['co_morbidities'], true) as $co_morbidities)
                    <li>{{ $co_morbidities['label'] ?? $co_morbidities }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    <!-- On Examination -->
    {{-- @if(!empty($protologyOrNonProctology['on_examination']) && count(json_decode($protologyOrNonProctology['on_examination'], true)) > 0)
    <div class="section" style="margin-top: 10px">
            <p class="section-title">On Examination</p>
            <ol>
                @foreach (json_decode($protologyOrNonProctology['on_examination'], true) as $on_examination)
                    <li>{{ $on_examination['label'] }}</li>
                @endforeach
            </ol>
            @if(isset($protologyOrNonProctology['examination_overview']) && !is_null($protologyOrNonProctology['examination_overview']))<p>{{ $protologyOrNonProctology['examination_overview'] }}</p>@endif
        </div>
    @endif
 --}}
    @php
        $medicineStr = $protologyOrNonProctology['medicines'] ?? '';
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
            <table>
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

                    @if(!is_null($protologyOrNonProctology['combination_medicines']))
                        @php
                            $comboMedicine=json_decode($protologyOrNonProctology['combination_medicines'],true);
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
    @if (!empty($protologyOrNonProctology['tests']) && count(json_decode($protologyOrNonProctology['tests'], true)) > 0)
        <div class="section" style="margin-top: 10px">
            <p class="section-title">Tests</p>
            <ol>
                @foreach (json_decode($protologyOrNonProctology['tests'], true) as $test)
                    <li>{{ $test['label'] }}</li>
                @endforeach
            </ol>
        </div>
    @endif

@php
$cleanAdvice = trim(strip_tags($advice ?? ''));
$cleanYoga= trim(strip_tags($protologyOrNonProctology['yoga'] ??''));
@endphp
    <!-- Advice -->
    @if (!empty($advice) && !is_null($advice) && !empty($cleanAdvice))
        <div class="section">
            <p class="section-title">Advice:</p>
            <div class="advice">{!! $advice !!}</div>
        </div>
    @endif

    <!-- Diet -->
    @if (!empty($protologyOrNonProctology['diet_plan']) && !is_null($protologyOrNonProctology['diet_plan'])  && count(json_decode($protologyOrNonProctology['diet_plan'], true))>0)
        <div class="section">
            <p class="section-title">Diet:</p>
            {{-- <p>ದಿನನಿತ್ಯದ</p> --}}
            @foreach (json_decode($protologyOrNonProctology['diet_plan'], true) as $diet_plan)
                <p style="line-height: 1.2;font-family: 'Noto Sans Kannada', 'Arial', sans-serif;">{{ $diet_plan['label'] }}</p>
            @endforeach
        </div>
    @endif

    <!-- Yoga -->
    @if (!empty($protologyOrNonProctology['yoga']) && !is_null($protologyOrNonProctology['yoga']) && !empty($cleanYoga))
        <div class="section">
            <p class="section-title">Yoga:</p>
            @foreach (json_decode($protologyOrNonProctology['yoga'], true) as $yoga)
                <p>{{ $yoga['label'] }}</p>
            @endforeach
        </div>
    @endif

    <!-- Next Visit -->
    @if(!is_null($next_visit_date))
    <div class="section footer-note">
        <p><strong style="font-size: 16px">Next Visit:</strong>
            {{ \Carbon\Carbon::parse($next_visit_date)->format('d/m/Y') }}</p>
    </div>
    @endif
</body>

</html>

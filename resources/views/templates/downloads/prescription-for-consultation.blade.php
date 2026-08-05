<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Heal Note - Lovable</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 40px;
            line-height: 1.6;
        }

        h1,
        h2,
        h3 {
            margin-bottom: 10px;
        }

        .section {
            margin-top: 30px;
            margin-bottom: 25px;
        }

        .label {
            font-weight: bold;
        }

        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
        }

        .advice-list {
            list-style-type: disc;
            padding-left: 20px;
        }

        hr {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div style="display: flex; align-items: center; justify-content: center;">
        <p style="text-align: center">
            <strong style="font-size: 24px">{{ env('APP_NAME') }}</strong><br />
            {{ env('HOSPITAL_ADDRESS') }}
        </p>
    </div>
    <div style="border:2px solid; width:100%"></div>
    {{-- <table width="100%" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; vertical-align: top; text-align: left;">
                <table>
                    <tr>
                        <td><strong>{{ $doctor_name }}</strong></td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; text-align: right;">
                <table>
                    <tr>
                        <td>
                            <strong>Date:</strong> June 10, 2025<br />
                            <strong>Time:</strong> 10:30 AM
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table> --}}
    <div class="section">
        <h2>Patient Information</h2>
        <hr />
        <div style="display: flex; align-items: start; justify-content: space-between;">
            <table class="info-table">
                <tr>
                    <td class="label">Name:</td>
                    <td>{{ $patient_name }}</td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td>{{ $patient_email }}</td>
                </tr>
            </table>
            <table class="info-table">
                <tr>
                    <td class="label">Phone:</td>
                    <td>{{ $patient_phone }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="section">
        <h2>Doctor Information</h2>
        <hr />
        <div style="display: flex; align-items: start; justify-content: space-between;">
            <table class="info-table">
                <tr>
                    <td class="label">Name:</td>
                    <td>{{ $doctor_name }}</td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td>{{ $doctor_email }}</td>
                </tr>
            </table>
            <table class="info-table">
                <tr>
                    <td class="label">Phone:</td>
                    <td>{{ $doctor_phone }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- <div class="section">
        <h2>Chief Complaint</h2>
        <hr />
        <p>
            {{ $complaint }}
        </p>
    </div>

    <div class="section">
        <h2>Core Vitals</h2>
        <hr />
        <ul class="advice-list">
            <li><strong>Blood Pressure</strong>: {{ $protologyOrNonProctology['vitals']['bp'] ?? 'N/A' }}</li>
            <li><strong>Temperature</strong> {{ $protologyOrNonProctology['vitals']['temperature'] ?? 'N/A' }}</li>
            <li><strong>Respiratory Rate</strong> {!! $protologyOrNonProctology['vitals']['rs'] ?? 'N/A' !!}</li>
            <li><strong>Cardiovascular System</strong> {!! $protologyOrNonProctology['vitals']['cvs'] ?? 'N/A' !!}</li>
        </ul>
    </div>

    <div class="section">
        <h2>Diagnosis</h2>
        <hr />
        <p>
            <strong>Preliminary</strong>: {{ $preliminary_diagnosis }}<br />
            <strong>Advice</strong>: {{ $advice }}
        </p>
    </div> --}}

    <div class="section">
        <h2>Rx (Prescription)</h2>
        <hr />
        <table width="100%">
            @php
                $medicines = $protologyOrNonProctology['medicines'] ?? '';
                $medicinesArray = $medicines ? explode(',', $medicines) : [];
            @endphp
            @foreach ($medicinesArray as $item)
                @php
                    $medicinesData = $item ? (is_string($item) ? explode('#', $item) : []) : [];
                @endphp
                <tr>
                    <td>1. <strong>{{ $medicinesData[0] ?? 'NA' }}</strong></td>
                    <td>{{ $medicinesData[2] ?? 'NA' }}</td>
                    <td>{{ $medicinesData[3] ?? 'NA' }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>Tests Advised</h2>
        <hr />
        <ol>
            @foreach ($test as $item)
                <li>{{ $item->test_name }}</li>
            @endforeach
        </ol>
    </div>
    {{-- @if ($consultation_type == 'Proctology')
        <div class="section">
            <h2>Diet Advice</h2>
            <hr />
            <ul class="advice-list">
                <li>
                    <strong>Advice Field</strong>: {{ $protologyOrNonProctology['advice_field'] }}
                </li>
                <li>
                    <strong>Diagnosis Summary</strong>: {!! $protologyOrNonProctology['diagnosis_summary'] !!}
                </li>
                <li>
                    <strong>Examination Overview</strong>: {!! $protologyOrNonProctology['examination_overview'] !!}
                </li>
                <li>
                    <strong>Preliminary Diagnostic</stong>: {!! $protologyOrNonProctology['preliminary_diagnostic'] !!}
                </li>
            </ul>
        </div>
    @elseif($consultation_type == 'Non Proctology')
        <div class="section">
            <h2>Yoga & Exercise Advice</h2>
            <hr />
            <ul class="advice-list">
                <li>
                    <strong>Name</strong>: {{ $protologyOrNonProctology['asana_name'] }}
                </li>
                <li>
                    <strong>Description</strong>: {{ $protologyOrNonProctology['description'] }}
                </li>
                <li>
                    <strong>Description</strong>: {!! $protologyOrNonProctology['benefits'] !!}
                </li>
                <li>
                    <strong>Contraindications</strong>: {!! $protologyOrNonProctology['contraindications'] !!}
                </li>
                <li>
                    <strong>Difficulty Level</strong>: {!! $protologyOrNonProctology['difficulty_level'] !!}
                </li>
                <li>
                    <strong>Recommended Duration (in seconds)</strong>: {!! $protologyOrNonProctology['recommended_duration'] !!}
                </li>
            </ul>
        </div>
        <div class="section">
            <h2>Food Prsceription</h2>
            <hr />
            <p><strong>Breakfast</strong>: {{ $protologyOrNonProctology['breakfast'] }}</p>
            <p><strong>Lunch</strong>: {{ $protologyOrNonProctology['lunch'] }}</p>
            <p><strong>Dinner</strong>: {{ $protologyOrNonProctology['dinner'] }}</p>
            {{ $protologyOrNonProctology['food_prescription'] }}
        </div>
        <div class="section">
            <h2>Diagnosis Summary</h2>
            <hr />
            {{ $protologyOrNonProctology['diagnosis_summary'] }}
        </div>
        <div class="section">
            <h2>Examination Overview</h2>
            <hr />
            {{ $protologyOrNonProctology['examination_overview'] }}
        </div>
        <div class="section">
            <h2>Preliminary Diagnostic</h2>
            <hr />
            {{ $protologyOrNonProctology['preliminary_diagnostic'] }}
        </div>
        <div class="section">
            <h2>Findings</h2>
            <hr />
            <ul class="advice-list">
                <li>
                    <strong>Name</strong>: {{ $protologyOrNonProctology['finding_fields'] }}
                </li>
            </ul>
        </div>
    @endif --}}
    <hr />
    <div class="section">
        <h2>Next Visit</h2>
        <table width="100%">
            <tr>
                <td align="left" style="vertical-align: top;">
                    <table>
                        <tr>
                            <td>
                                <strong>Date:</strong> {{ \Carbon\Carbon::parse($next_visit_date)->format('F j, Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Time:</strong> {{ \Carbon\Carbon::parse($next_visit_date)->format('g:i A') }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td align="right" style="vertical-align: top;">
                    <table>
                        <tr>
                            <td>
                                Doctor's Signature<br />
                                <strong>{{ $doctor_name }}</strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>

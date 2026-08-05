<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pre-operative Check List</title>
    <style>
        h1 {
            font-size: 18px;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 20px;
        }

        /* table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        } */

        .info-table td {
            padding: 6px 4px;
            /* vertical-align: bottom; */
        }

        .label {
            font-weight: bold;
            width: 20%;
        }

        /* .dotted {
            border-bottom: 1px dotted #000;
            width: 100%;
        } */

        .checklist-table th,
        .checklist-table td {
            /* border: 1px solid #000; */
            padding: 6px;
            /* vertical-align: top; */
        }

        .checklist-table th {
            background-color: #f2f2f2;
        }

        .sn-column {
            width: 40px;
        }

        .response-column {
            width: 180px;
        }

        .signature-section {
            margin-top: 30px;
            font-size: 13px;
        }

        .signature-box {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }

        .note-section {
            font-style: italic;
            font-size: 12px;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    @include('templates.downloads.letter_header', [
        'generic_letter_header' => true,
        'letter_header_address' => $patient->letter_header_address,
    ])
    <h1>Pre-operative Check List</h1>

    <div class="info-block">
        <div class="row">
            <div class="label">Name:</div>
            <div class="value dotted">{{ $patient->first_name }} {{ $patient->last_name }}</div>

            <div class="label">Age/Gender:</div>
            <div class="value dotted">{{ $patient->age }} / {{ $patient->gender }}</div>
        </div>

        <div class="row">
            <div class="label">Date:</div>
            <div class="value dotted">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</div>

            <div class="label">Time:</div>
            <div class="value dotted">{{ \Carbon\Carbon::now()->format('H:i') }}</div>
        </div>

        <div class="row">
            <div class="label">Ward Number:</div>
            <div class="value dotted">{{ $patient->ward_number ?? '__________' }}</div>

            <div class="label">Bed Number:</div>
            <div class="value dotted">{{ $patient->bed_number ?? '__________' }}</div>
        </div>
    </div>

    <table class="checklist-table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th class="sn-column">S.N</th>
                <th>HISTORY</th>
                <th class="response-column">YES / NO / DETAILS</th>
            </tr>
        </thead>
        <tbody>
            @php
                $questions = [
                    'ALL INVESTIGATIONS DONE?',
                    'CHEST X RAY done?',
                    'ECG done? NORMAL?',
                    'HBSAG AND HIV I AND II NON-REACTIVE?',
                    'IS PATIENT COMES UNDER MINOR AGE GROUP? PARENTS ARE PRESENT?',
                    'IS PATIENT ON BLOOD THINNER?',
                    'ECOSPRIN/ CLOPIDOGREL/ ACITROM / ANY OTHER?',
                    'IF YES, STOPPED SINCE?',
                    'IS PATIENT ASTMATIC? IN ACUTE STATE? SYMPTOMATIC NOW?',
                    'IF YES WHAT IS THE TREATMENT AT PRESENT?',
                    'IS PATIENT ALLERGIC TO ANY MEDICINES?',
                    'TOOTH EXTRACTION EARLIER UNDER LA?',
                    'ANY PROCEDURE UNDER LOCAL ANESTHESIA EARLIER?',
                    'IS PATIENT DIABETIC? IF YES TREATMENT DETAILS',
                    'IF YES BLOOD SUGAR READING TODAY?',
                    'HAD THYRONORM / THYROXIN TODAY? (IF HYPOTHYROID)',
                    'IS PATIENT HYPERTENSIVE? ANY DRUGS?',
                    'IS BLOOD PRESSURE NORMAL? READING?',
                    'CONSENT FOR FISTULA / PILES / FISSURE TAKEN?',
                    'SURGICAL / ANESTHESIA CONSENT TAKEN?',
                    'PATIENT AWARE OF TYPE OF ANESTHESIA?',
                    'PATIENT AWARE OF OPERATIVE PROCEDURE?',
                    'ANY CLARIFICATIONS REQUIRED ABOUT ANESTHESIA / PROCEDURE?',
                    'MALE >55 YRS? ANY LOWER URINARY TRACT SYMPTOMS?',
                    'IS BPH DIAGNOSED? PVR?',
                    'URINARY OBSTRUCTION HISTORY? CATHETER?',
                    'CAN PATIENT LIE IN LITHOTOMY POSITION?',
                    'ANY HISTORY OF KNEE / HIP SURGERY?',
                    'PATIENT BELONGS TO SETTY COMMUNITY?',
                    'ANY NOTABLE EVENT IN PREVIOUS SURGERIES?',
                    'IF FEMALE - IS PATIENT PREGNANT?',
                    'IS PATIENT EPILEPTIC?',
                    'IS PATIENT ON ANTIPSYCHOTIC DRUGS?',
                    'SPINE SURGERIES / INJURIES?',
                    'LAST INTAKE OF FOOD / LIQUIDS? DATE & TIME?',
                ];
            @endphp

            @foreach ($questions as $index => $question)
                <tr>
                    <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $question }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div><strong>SIGNATURE OF DOCTOR</strong></div><br><br><br>
            <div>DATE AND TIME</div>
            <div>PLACE:</div>
        </div>
        <div class="signature-box">
            <div><strong>SIGNATURE OF PATIENT</strong></div>
        </div>
    </div>

    <div class="note-section">
        <strong>Note:</strong> Please mark in _____ if anything significant in the history and bring to the notice of
        the primary Surgeon and Anesthesiologist.
    </div>
</body>

</html>

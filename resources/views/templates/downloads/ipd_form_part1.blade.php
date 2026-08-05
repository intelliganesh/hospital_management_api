<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Patient/Day Care Record</title>
    <style>
        body {
            color: #000;
            margin: 0 auto;
            background-color: white;
            font-family: Arial, sans-serif;
        }

        .form-container {
            /* padding: 10px; */
            position: relative;
            background-color: #ffff;
        }

        .header {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .address {
            font-size: 12px;
            margin-bottom: 20px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
        }

        .row::after {
            content: "";
            display: table;
            clear: both;
        }

        .col-left,
        .col-right {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .col-left {
            float: left;
        }

        .col-right {
            float: right;
        }

        .field {
            margin-bottom: 12px;
        }

        .field-label {
            font-weight: bold;
            display: block;
        }

        .consultants-field {
            margin-left: 15px;
        }

        .divider {
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        #examination-section {
            margin-top: 15px;
        }

        .examination-label {
            font-weight: bold;
            display: inline-block;
            /* width: 130px; */
        }

        .examination-field {
            display: inline-block;
            /* width: 100px; */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    @include('templates.downloads.letter_header', [
        'generic_letter_header' => true,
        'letter_header_address' => $patient->letter_header_address,
    ])

    <div class="form-container">

        <div class="title">IN PATIENT/DAY CARE RECORD</div>

        <div class="row">
            <div class="col-left">
                <div class="field">
                    <span class="field-label">NAME</span>
                    <div>{{ $patient->first_name }} {{ $patient->last_name }}</div>
                </div>
                <div class="field">
                    <span class="field-label">ADDRESS</span>
                    <div>{{ $patient->address }}, {{ $patient->city }}, {{ $patient->state }}, {{ $patient->country }}
                    </div>
                </div>
            </div>
            <div class="col-right">
                <div class="field">
                    <span class="field-label">AGE / SEX</span>
                    <div>{{ $patient->age }} / {{ $patient->gender }}</div>
                </div>
                <div class="field">
                    <span class="field-label">IP NUMBER</span>
                    <div>{{ $patient->patient_number }}</div>
                </div>
                <div class="field">
                    <span class="field-label">DOA & TIME</span>
                    {{-- <div>{{ $patient->created_at->format('d-m-Y h:i') }}</div> --}}
                </div>
                <div class="field">
                    <span class="field-label">DOD & TIME</span>
                    {{-- <div>{{ $patient->updated_at->format('d-m-Y h:i') }}</div> --}}
                </div>
            </div>
        </div>

        <div class="field">
            <span class="field-label">PROFESSION</span>
            <div>{{ $patient->profession ?? 'N/A' }}</div>
        </div>
        <div style="display:flex; align-items: center; justify-content: center;">
            <div class="field">
                <span class="field-label">PHONE NUMBER</span>
                <div>{{ $patient->phone_no }}</div>
            </div>
            <div class="field">
                <span class="field-label">EMAIL ID</span>
                <div>{{ $patient->email }}</div>
            </div>
        </div>
        <div class="field">
            <span class="field-label">AADHAAR / PASSPORT</span>
            {{-- <div>{{ $patient->aadhaar_number ?? 'N/A' }}</div> --}}
        </div>
        <div class="field">
            <span class="field-label">NEAREST RELATIVE / ATTENDANT NAME</span>
            <div>{{ $patient->attendant_with_patient_name ?? 'N/A' }}</div>
        </div>
        <div class="field">
            <span class="field-label">RELATIVE PHONE NUMBER</span>
            <div>{{ $patient->attendant_with_patient_phone_no ?? 'N/A' }}</div>
        </div>
        <div class="field">
            <span class="field-label">CONSULTANTS NAME AND SIGNATURE</span>
            {{-- <div class="consultants-field">1.
                {{ $patient->first_name && $patient->last_name ? $patient->first_name . ' ' . $patient->last_name : '______________________' }}
            </div> --}}
            <div class="consultants-field">1. ______________________</div>
            <div class="consultants-field">2. ______________________</div>
            <div class="consultants-field">3. ______________________</div>
        </div>

        <div class="divider"></div>

        <div class="field">
            <span class="field-label">CHIEF COMPLAINTS WITH DURATION</span>
            <div>{{ $patient->chief_complaints ?? 'Not Recorded' }}</div>
        </div>
        <div class="field">
            <span class="field-label">ASSOCIATED COMPLAINTS</span>
            <div>{{ $patient->associated_complaints ?? 'Not Recorded' }}</div>
        </div>
        <div class="field">
            <span class="field-label">PREVIOUS TREATMENT HISTORY</span>
            <div>{{ $patient->previous_treatment ?? 'Not Recorded' }}</div>
        </div>
        <div class="field">
            <span class="field-label">ASSOCIATED MEDICAL ILLNESS / CURRENT TREATMENT</span>
            <div>{{ $patient->treatment_status }}</div>
        </div>
        <div class="field">
            <span class="field-label">FAMILY HISTORY</span>
            <div>{{ $patient->family_history ?? 'Not Recorded' }}</div>
        </div>
        <div class="field">
            <span class="field-label">PERSONAL HISTORY</span>
            <div>{{ $patient->personal_history ?? 'Not Recorded' }}</div>
        </div>
        <div class="field">
            <span class="field-label">ALLERGIES</span>
            <div>{{ $patient->allergy ?? 'None' }}</div>
        </div>

        <div id="examination-section">
            <span class="field-label">EXAMINATION</span>
            {!! $patient->examination !!}
            {{-- {{ $patient->examination }} --}}
            <br><br>
            {{-- <div>{!! $patient->examination !!}</div> --}}
            <div>
                <span class="examination-label">A. GENERAL</span><br />
                @if ($patient->bp)
                    <span class="examination-field">BP: {!! $patient->bp !!}</span><br />
                @else
                    <span class="examination-field">BP: -</span><br />
                @endif
                @if ($patient->pulse)
                    <span class="examination-field">PULSE: {!! $patient->pulse !!}</span><br />
                @else
                    <span class="examination-field">PULSE: -</span><br />
                @endif
                @if ($patient->temperature)
                    <span class="examination-field">TEMP: {!! $patient->temperature !!}</span>
                @else
                    <span class="examination-field">TEMP: -</span>
                @endif
                <br />
                <br />
                <br />
            </div>
            <div>
                @if ($patient->cvs)
                    <span class="examination-label">CVS: {!! $patient->cvs !!}</span><br />
                @else
                    <span class="examination-label">CVS: - </span><br />
                @endif
                @if ($patient->rs)
                    <span class="examination-label">RS: {!! $patient->rs !!}</span><br />
                @else
                    <span class="examination-label">RS: -</span><br />
                @endif
            </div>
            <br />
            <br />
            <br />
            <div>
                <span class="examination-label">PER ABDOMEN: {{ $patient->per_abdomen ?? '-' }}</span>
                <br />
                <br />
                <br />
                <br />
            </div>
            <div>
                <span class="examination-label">LOCAL EXAMINATION: {{ $patient->local_exam ?? '-' }}</span>
            </div>
        </div>
    </div>
</body>

</html>

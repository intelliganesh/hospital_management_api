<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Consent for Anaesthesia/Sedation</title>
    <style>
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            text-decoration: underline;
        }

        .consent-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            text-decoration: underline;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .field-line {
            font-size: 12px;
            margin-bottom: 10px;
        }

        /* .dotted {
            display: inline-block;
            border-bottom: 1px dotted #000;
            height: 12px;
            vertical-align: middle;
            margin: 0 5px;
        } */

        .paragraph {
            font-size: 12px;
            line-height: 1.4;
            text-align: justify;
            margin-bottom: 10px;
        }

        /* .st,
        .nd {
            vertical-align: super;
            font-size: smaller;
        } */

        /* table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 15px;
        } */

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .info-group {
            font-size: 12px;
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .label {
            font-weight: bold;
            min-width: 120px;
            margin-right: 10px;
        }

        .value {
            flex: 1;
            min-width: 150px;
        }

        .dotted {
            border-bottom: 1px dotted #000;
            padding-bottom: 2px;
        }

        .wide {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="form-container">
        @include('templates.downloads.letter_header', [
            'generic_letter_header' => true,
            'letter_header_address' => $patient->letter_header_address,
        ])
        {{-- <div class="header-title">ACHARYA SUSHRUTHA HEALTHCARE PVT.LTD.</div>
        <div class="address">
            NO.53, 1<span class="st">ST</span> CROSS, ITI LAYOUT, 80 FEET ROAD, NAGARABHAVI 2<span
                class="nd">ND</span> STAGE, MALLATHAHALLI, BANGALORE<br>
            56056, PH:9739733372
        </div> --}}

        <div class="consent-title">CONSENT FOR ANAESTHESIA/SEDATION</div>

        <div class="info-group">
            <div class="row">
                <div class="label">DATE:</div>
                <div class="value dotted">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</div>
            </div>

            <div class="row">
                <div class="label">PATIENT NAME:</div>
                <div class="value dotted">{{ $patient->first_name }} {{ $patient->last_name }}</div>

                <div class="label">AGE/GENDER:</div>
                <div class="value dotted">{{ $patient->age }} / {{ $patient->gender }}</div>
            </div>

            <div class="row">
                <div class="label">IP NO.:</div>
                <div class="value dotted">{{ $patient->patient_number }}</div>
            </div>

            <div class="row">
                <div class="label">Diagnosis:</div>
                <div class="value dotted wide">{{ $patient->diagnosis ?? '..........................................' }}
                </div>
            </div>

            <div class="row">
                <div class="label">Operative Procedure/Operation:</div>
                <div class="value dotted wide">
                    {{ $patient->procedure_name ?? '..........................................' }}</div>
            </div>
        </div>

        {{-- <div class="field-line"><span class="field-label">DATE:</span> <span
                class="dotted">{{ \Carbon\Carbon::now()->format('d-m-Y') }}</span></div>

        <div class="field-line">
            <span class="field-label">PATIENT NAME:</span>
            <span class="dotted">{{ $patient->first_name }} {{ $patient->last_name }}</span>
            <span class="field-label" style="margin-left: 20px;">AGE/GENDER:</span>
            <span class="dotted" style="width: 100px;">{{ $patient->age }} / {{ $patient->gender }}</span>
        </div>

        <div class="field-line"><span class="field-label">IP NO.:</span> <span
                class="dotted">{{ $patient->patient_number }}</span></div>

        <div class="field-line"><span class="field-label">Diagnosis:</span> <span class="dotted"
                style="width: 400px;">{{ $patient->diagnosis ?? '' }}</span></div>

        <div class="field-line"><span class="field-label">Operative Procedure/Operation:</span> <span class="dotted"
                style="width: 350px;">{{ $patient->procedure_name ?? '' }}</span></div> --}}

        <div class="field-line"><span class="field-label">TYPE OF ANAESTHESIA:</span> Local / General / Spinal /
            Epidural / Nerve Block</div>

        <div class="paragraph">
            I <span class="dotted" style="width: 250px;">{{ $patient->first_name }} {{ $patient->last_name }}</span>
            (Patient's name), give my full consent as an act of my own free will to undergo the following
            surgery/procedure <br /><span class="dotted"
                style="width: 200px;">{{ $patient->procedure_name ?? '' }}</span> at
            Acharya Sushrutha Healthcare Pvt. Ltd., Bangalore. I understand that the above-mentioned procedure
            necessitates the administration of <br />local/sedation/regional/general or any combination thereof. I
            hereby
            authorize Dr. <span class="dotted" style="width: 180px;">{{ $patient->anaesthetist_name ?? '' }}</span>
            (Anaesthetist) and their associates to provide the required anaesthesia service.
        </div>

        <div class="paragraph">
            I understand that the results and effects of anaesthesia depend on the type administered and may vary.<br />
            I have been explained the risks, including bruising, breathing difficulties, seizures, allergic reactions,
            etc. I understand rare risks include stroke, paralysis, or death.
        </div>

        <div class="paragraph">
            I have been informed in a language I understand about the surgery, anaesthesia, alternatives, risks, costs,
            and prognosis.
        </div>

        <div class="paragraph">
            I understand local anaesthesia may fail, and alternate methods may be required.
        </div>

        <div class="paragraph">
            I hereby absolve Acharya Sushrutha Healthcare Pvt. Ltd., Bangalore and its team from liabilities arising
            from the procedure.
        </div>

        <div class="paragraph">
            <span class="field-label">Consent of patient representative/surrogate:</span><br>
            The patient is unable to consent because <span class="dotted"
                style="width: 250px;">{{ $patient->reason_for_surrogate ?? '' }}</span>. I, <span class="dotted"
                style="width: 200px;">{{ $patient->surrogate_name ?? '' }}</span>
            ({{ $patient->surrogate_relation ?? 'relation' }}) give consent on their behalf after discussion with the
            doctor.
        </div>

        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Signature</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Patient/Surrogate</td>
                    <td>{{ $patient->surrogate_name ?? $patient->first_name . ' ' . $patient->last_name }}</td>
                    <td>_____________________</td>
                    <td>{{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::now()->format('H:i') }}</td>
                </tr>
                <tr>
                    <td>Witness</td>
                    <td>{{ $patient->witness_name ?? '' }}</td>
                    <td>_____________________</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Doctor</td>
                    <td>{{ $patient->doctor_name ?? '' }}</td>
                    <td>_____________________</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Interpreter</td>
                    <td>{{ $patient->interpreter_name ?? '' }}</td>
                    <td>_____________________</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>

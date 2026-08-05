<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Consent Form</title>
    <style>
        .content {
            text-align: justify;
            margin-bottom: 20px;
        }

        .signature-section {
            margin-top: 50px;
        }

        .signature-box {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .signature-box-right {
            float: right;
        }

        .signature-box-left {
            float: left;
        }

        .signature-field {
            margin-bottom: 25px;
        }

        .full-width-signature {
            clear: both;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    @include('templates.downloads.letter_header', [
        'generic_letter_header' => true,
        'letter_header_address' => $patient->letter_header_address,
    ])
    <div class="form-container">
        <div class="title">CONSENT FORM</div>

        <div class="content">
            I, Mr./Ms./Mrs. <strong>{{ $patient->first_name }} {{ $patient->last_name }}</strong>, acknowledge that my
            consulting physician and their team of doctors have explained the diagnosis and prognosis of my present
            health condition in my own language, including the recommended procedure of
            <strong>{{ $patient->procedure_name ?? '__________________' }}</strong> under
            <strong>{{ $patient->anesthesia_type ?? '__________________' }}</strong> anesthesia, along with any
            associated risks and possible complications, also in my own language.
        </div>

        <div class="content">
            I hereby give my full consent to undergo the procedure of
            <strong>{{ $patient->procedure_name ?? '__________________' }}</strong> and any additional procedure deemed
            necessary in the judgment of my doctor during my stay in the hospital. I also acknowledge that I have been
            made fully aware of the requirement for a possible follow-up procedure, which may be determined by my
            treating surgeon. I therefore agree, understand, and acknowledge that the consultant, medical team, nurses,
            hospital staff, and administration shall not be held responsible for any unfortunate event that may worsen
            my health, specific to my individual circumstances whether internal or external.
        </div>

        <div class="signature-section">
            <div class="signature-box signature-box-left">
                <div class="signature-field">Date: {{ now()->format('d-m-Y') }}</div>
                <div class="signature-field">Patient Name: {{ $patient->first_name }} {{ $patient->last_name }}</div>
                <div class="signature-field">Patient's Signature: ________________________</div>
            </div>

            <div class="signature-box signature-box-right">
                <div class="signature-field">Attender's Name:
                    {{ $patient->attendant_with_patient_name ?? '____________________' }}
                </div>
                <div class="signature-field">Attender's Signature: ________________________</div>
            </div>

            <div class="full-width-signature">
                <div class="signature-field">Surgeon Name: {{ $patient->surgeon_name ?? '____________________' }}</div>
                <div class="signature-field" style="margin-top: 30px;">Surgeon Signature: ________________________</div>
            </div>
        </div>
    </div>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Diagnosis Form</title>
    <style>
        .section {
            margin-bottom: 0;
            position: relative;
        }

        .label {
            font-weight: bold;
            margin-bottom: 0;
        }

        #investigations {
            height: 120px;
        }

        #provisional-diagnosis {
            height: 220px;
        }

        #final-diagnosis {
            height: 280px;
        }

        #line-of-treatment {
            height: 440px;
        }

        #treatment-advised {
            height: 400px;
        }

        #preoperative-instructions {
            height: 460px;
        }

        #treatment-given {
            height: 750px;
        }
    </style>
</head>

<body>

    <div class="form-container">
        @include('templates.downloads.letter_header', [
            'generic_letter_header' => true,
            'letter_header_address' => $patient->letter_header_address,
        ])

        <div id="investigations" class="section">
            <div class="label">INVESTIGATIONS:</div>
        </div>

        <div id="provisional-diagnosis" class="section">
            <div class="label">PROVISIONAL DIAGNOSIS:</div>
        </div>

        <div id="final-diagnosis" class="section">
            <div class="label">FINAL DIAGNOSIS:</div>
        </div>

        <div id="line-of-treatment" class="section">
            <div class="label">LINE OF TREATMENT: MEDICAL/ SURGICAL</div>
        </div>

        <div id="treatment-advised" class="section">
            <div class="label">TREATMENT ADVISED:</div>
        </div>

        <div id="preoperative-instructions" class="section">
            <div class="label">PREOPERATIVE INSTRUCTIONS:</div>
        </div>

        <div id="treatment-given" class="section">
            <div class="label">TREATMENT GIVEN</div>
        </div>
    </div>
</body>

</html>

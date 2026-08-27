<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        color: #000;
        line-height: 1.9;
    }


    .container {
        width: 100%;
        font-weight: bold;
        padding: 0 50px;
        box-sizing: border-box;
        word-break: normal;
        overflow-wrap: break-word;
    }

    .title {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 20px;
    }

    .dotted-line {
        border-bottom: 1px dotted #000;
        display: inline-block;
        min-width: 600px;
        font-weight: normal;
    }

    .full-line {
        border-bottom: 1px dotted #000;
        display: inline-block;
        width: 100%;
        height: 14px;
    }

    .section-space {
        margin-top: 25px;
    }

    .signature-space {
        margin-top: 40px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">CONSENT FORM</div>
        <p>
            I, <span class="dotted-line" style="min-width:450px;">&nbsp;&nbsp;{{ $ipd->patient_name ?? '' }}</span>
            do hereby consent to my treating doctor, the attending doctors and the staff at
            Acharya Sushrutha Healthcare Pvt Ltd to perform
            <span class="dotted-line" style="min-width:450px;">{{ $ipd->surgery_report?->surgery_name ?? '' }}</span>
            for
            <span class="dotted-line" >{{ $ipd->final_diagnosis ?? '' }}</span>{{--
            <span class="dotted-line"></span> --}}
        </p>
        <p>
            I further Authorize
            <span class="dotted-line" style="min-width:200px;">Dr. {{ $ipd->doctor_name ?? '' }}</span>
            and the staffs of Acharya Sushrutha Healthcare Pvt Ltd to perform such additional
            diagnostic or surgical procedures as maybe required or deemed advisable to
            safeguard life or health during the course of diagnostic or surgical procedure.
        </p>
        <p>
            For patient providing consent, the anticipated diagnostic or surgical procedure has been explained
            in patient's own language. The nature, anticipated effect, risks and alternatives understood
            and as in the best interest of the patient. The possible complications like
        </p>
        <div class="full-line"></div>
        <p>
            is been explained to me by my own language.
        </p>
        <!-- Date & Patient Signature Section -->
        <div class="section-space">
            <p>
                Date :
                <span class="dotted-line" style="min-width:120px;">
                    {{ date('d/m/Y') }}
                </span>
            </p>
            <p>
                Name of Patient :
                <span class="dotted-line" style="min-width:200px;">
                    {{ $ipd->patient_name ?? '' }}
                </span>
            </p>
            <p>
                Signature of Patient :
                <span class="dotted-line" style="min-width:180px;"></span>
            </p>
        </div>
        <!-- Witness Section -->
        <div class="signature-space">
            <p>
                Name of Witness :
                <span class="dotted-line" style="min-width:270px;"></span>
            </p>
            <p>
                Signature of Witness :
                <span class="dotted-line" style="min-width:250px;"></span>
            </p>
        </div>
        <!-- Surgeon Section -->
        <div class="signature-space">
            <p>
                Name of Surgeon :
                <span class="dotted-line" style="min-width:200px;">{{ $ipd->surgery_report?->surgeon ?? '' }}</span>
            </p>
            <p>
                Signature of Surgeon :
                <span class="dotted-line" style="min-width:180px;"></span>
            </p>
        </div>
    </div>
</body>

</html>

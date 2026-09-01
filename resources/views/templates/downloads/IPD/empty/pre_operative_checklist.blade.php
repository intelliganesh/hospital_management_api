<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <title>In Patient / Day Care Record</title> -->
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
        }

        p {
            margin: 0px !important;
        }

        .container {
            width: 100%;
            /* padding: 10px; */
        }

        .header {
            text-align: center;
            font-weight: bold;
        }

        .sub-header {
            text-align: center;
            font-size: 11px;
            margin-top: 4px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            text-decoration: underline;
            text-underline-offset: 5px;
            /* border-bottom: 2px solid #000; */
            /* padding: 5px 0; */
            margin-bottom: 5px;
        }


        .label {
            font-weight: bold;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 12px;
        }

        .space {
            min-height: 30px;
        }

        .line {
            border-top: 2px solid #000;
        }

        .row {
            display: flex;
            width: 100%;
            margin-bottom: 6px;
        }

        .field {
            display: flex;
            width: 33.33%;
        }

        .label {
            font-weight: bold;
            margin-right: 6px;
            white-space: nowrap;
        }

        .value {
            white-space: nowrap;
        }

        .column {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .pair {
            display: flex;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        td {
            border: 0.1rem solid #000;
            padding: 4px;
            vertical-align: top;
        }

        th {
            border: 0.1rem solid #000;
            padding: 0px;
            vertical-align: top;
        }

        .no-border {
            border: none;
            margin-top: 0px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .no-border,
        .no-border th,
        .no-border td {
            border: none !important;
            border-collapse: collapse;
            padding-bottom: 20px
        }

        .highlight {
    background: #e6e6e6 !important;
    {{-- border: 2px solid #000 !important; --}}
    font-weight: bold !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

@media print {
    .highlight {
        background: #e6e6e6 !important;
        {{-- border: 2px solid #000 !important; --}}
        font-weight: bold !important;
    }
}

    </style>
</head>

<body>
    <div class="container">
        <div class="title">PRE-OPERATIVE CHECKLIST</div>
        <!-- Patient Details -->
        <table class="no-border">
            <tr>
                <td style="width:40%"><b>Name of Patient: </b>{{ $ipd->patient_name }}</td>
                <td style="width:30%"><b>Age/Gender: </b>{{ $ipd->patient_age }} / {{ $ipd->patient->gender }}</td>
                <td style="width:30%"><b>Date: </b></td>
            </tr>
            <tr style="padding-top: 20px;">
                <td><b>IP No: {{ $ipd->ipd_number }}</b></td>
                <td><b>WARD NO: {{ $ipd->ward_number }}</b> </td>
                <td><b>Time: </b></td>
            </tr>
        </table>
        <!-- Checklist Table -->
        <table style="margin-top:20px;font-weight:bold !important;">
            <tr>
                <th width="5%">Sl.No</th>
                <th width="70%">QUESTIONNAIRE</th>
                <th width="25%">YES / NO / DETAILS</th>
            </tr>
            <tr>
                <td>01</td>
                <td><b>ALL INVESTIGATIONS DONE? ARE THE REPORTS IN NORMAL RANGE</b></td>
                <td></td>
            </tr>
            <tr>
                <td>02</td>
                <td><b>CHEST XRAY / ECG DONE</b></td>
                <td></td>
            </tr>
            <tr>
                <td>03</td>
                <td><b>IS PATIENT UNDER MINOR AGE GROUP? PARENTS ARE PRESENT?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>04 a)</td>
                <td><b>IS PATIENT ON BLOOD THINNERS?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>04 b)</td>
                <td><b>IF YES – NAME OF MEDICINE AND STOPPED SINCE?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>05 a)</td>
                <td><b>IS PATIENT SUFFERING FROM BRONCHIAL ASTHMA?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>05 b)</td>
                <td><b>IF YES WHAT IS THE TREATMENT AT PRESENT?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>06</td>
                <td><b>IS PATIENT ALLERGIC TO ANY MEDICATIONS?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>07</td>
                <td><b>HAS PATIENT UNDERGONE TOOTH EXTRACTION UNDER LOCAL ANESTHESIA?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>08</td>
                <td><b>ANY SURGICAL PROCEDURE UNDER LOCAL ANESTHESIA?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>09 a)</td>
                <td><b>IS PATIENT DIABETIC? IF YES, NAME OF THE MEDICINE?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>09 b)</td>
                <td><b>IF YES, BLOOD SUGAR READING TODAY</b></td>
                <td></td>
            </tr>
            <tr>
                <td>10</td>
                <td><b>IS PATIENT UNDER THYROID MEDICATION?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>11 a)</td>
                <td><b>IS PATIENT A KNOWN CASE OF HYPERTENSION?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>11 b)</td>
                <td><b>IF YES, NAME OF THE MEDICINE AND THE PRESENT BP READING?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>11 c)</td>
                <td><b>IF YES, HAS PATIENT TAKEN THE MEDICATION FOR HYPERTENSION?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>12</td>
                <td><b>ALL INFORMED CONSENTS SIGNED BY PATIENT / PATIENT ATTENDER?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>13</td>
                <td><b>IS PATIENT AWARE OF THE TYPE OF ANESTHESIA TO BE ADMINISTERED?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>14</td>
                <td><b>IS THE PATIENT AWARE OF THE OPERATIVE PROCEDURE TO BE PERFORMED?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>15 a)</td>
                <td><b>IN CASE OF MALE PATIENT – IF PATIENT MORE THAN 55 YEARS</b></td>
                <td></td>
            </tr>
            <tr>
                <td>15 b)</td>
                <td><b>IF YES, ANY URINARY SYMPTOMS / DIAGNOSED BPH – IF YES MARK RED</b></td>
                <td></td>
            </tr>
            <tr>
                <td>16</td>
                <td><b>ANY HISTORY OF URINARY OBSTRUCTION / HISTORY OF CATHETER INSERTION?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>17</td>
                <td><b>IS PATIENT ABLE TO LIE DOWN IN LITHOTOMY POSITION?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>18</td>
                <td><b>ANY HISTORY OF KNEE / HIP / SPINE SURGERY?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>19</td>
                <td><b>IS PATIENT BELONGING TO SETTI / VASIYA / CHETTIAR COMMUNITY?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>20</td>
                <td><b>ANY NOTABLE EVENTS IN PREVIOUS SURGERY?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>21</td>
                <td><b>IN CASE OF FEMALE PATIENT – IS PATIENT PREGNANT?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>22</td>
                <td><b>ANY HISTORY OF EPILEPSY? IF YES MENTION IF ON ANY MEDICATIONS?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>23</td>
                <td><b>IS PATIENT TAKING ANY ANTIPSYCHOTIC MEDICATIONS?</b></td>
                <td></td>
            </tr>
            <tr>
                <td>24</td>
                <td><b>WHEN WAS THE LAST INTAKE OF FOOD / LIQUIDS – MENTION DATE AND TIME</b></td>
                <td></td>
            </tr>
        </table>
        <!-- Signatures -->
        <table class="no-border" style="margin-top:50px;">
            <tr>
                <td width="50%">
                    <b>Signature of Patient</b><br><br><br>
                    Date: ____________ &nbsp;&nbsp;&nbsp;
                    Time: ____________
                </td>
                <td width="50%" class="center">
                    <b>Signature of Doctor</b>
                </td>
            </tr>
        </table>
        <p style="padding-top:40px;">
            <b>NOTE: PLEASE MARK IN RED INK IF ANYTHING SIGNIFICANT IN THE HISTORY AND ALSO BRING TO THE NOTICE OF ANESTHETIST AND PRIMARY SURGEON.</b>
        </p>
    </div>
</body>

</html>

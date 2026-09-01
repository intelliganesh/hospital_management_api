<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10px;
        color: #000;
    }

    .title {
        text-align: center;
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 3px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
    }

    td,
    th {
        border: 0.1rem solid #000;
        padding: 2px;
        vertical-align: top;
    }

    .no-border td,
    .no-border th {
        border: none;
    }

    .center {
        text-align: center;
    }

    .bold {
        font-weight: bold;
    }
    .vertical-middle{
        vertical-align:middle;
    }

    .vertical-text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        text-align: center;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .grid td {
        width: 9px;
        height: 9px;
        padding: 0;
    }
    </style>
</head>

<body>
    <div class="title">ANAESTHESIA RECORD</div>
    <!-- Header -->
    <table>
        <tr>
            <td width="35%"><b>Name:</b> {{ $ipd->patient_name ?? '' }}</td>
            <td width="10%"><b>Age:</b> {{ $ipd->patient_age ?? '' }}</td>
            <td width="10%"><b>Sex:</b> {{ $ipd->patient->gender ?? '' }}</td>
            <td width="15%"><b>Hosp. No:</b> {{ $ipd->ipd_number ?? '' }}</td>
        </tr>
    </table>
    <table>
        <tr>
            <td width="70%"><b>Surgical Procedure:</b> </td>
            <td width="30%"><b>Position: </b></td>
        </tr>
    </table>
    <table>
        <tr>
            <td width="10%"><b>Anaesthetist:</b> </td>
            <td width="40%"></td>
            <td width="10%"><b>Surgeons:</b> </td>
            <td width="40%"></td>
        </tr>
    </table>
    <table>
        <tr>
            <td width="10%"><b>Assistants:</b> </td>
            <td width="40%"></td>
            <td width="10%"><b>Assistants:</b> </td>
            <td width="40%"></td>
        </tr>
    </table>
    <!-- TOP HORIZONTAL SCALE -->
    <table>
        <tr class="bold">
            <td rowspan="18" width="2%" class="vertical-text">ANAESTHETIC DRUGS</td>
            <td width="15%" colspan="2">Time</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"  style='text-align: center;'>TOTAL</td>
        </tr>
        <tr class="bold">
            <td width="15%" colspan="2">Oxygen (L/min)</td>
            @for($i=0;$i<38;$i++)
            <td></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td width="15%" colspan="2">N₂O (L/min)</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td width="15%" colspan="2">Air (L/min)</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td rowspan="30" width="2%" class="vertical-text">FLUIDS</td>
            <td width="15%" colspan="2">IV Fluid</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" colspan="2"></td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td width="15%" colspan="2">Urine Output</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td width="15%" colspan="2">Blood Loss</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="center bold">
            <td width="15%" rowspan="2">BaseLine Values</td>
            <td rowspan="2" class="vertical-middle" style="border-bottom: 1px solid #fff !important;">240</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-bottom: 1px solid #fff !important;">SYMBOLS</td>
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            <td width="15%" rowspan="6">Blood Pressure</td>
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">220</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">V &nbsp; Systolic BP</td>
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">200</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;"> Λ &nbsp; Diastolic BP</td>
        </tr>
        <tr class="center bold">

            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">180</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">○ &nbsp; Heart Rate</td>
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            <td width="15%" rowspan="4">Pulse</td>
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">160</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">+ &nbsp; Oxygen Sat</td>
        </tr>

        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
        <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">140</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">● &nbsp; ETCO2</td>
        </tr>
        <tr class="center bold">

            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
        <td width="15%" rowspan="6">Respiratory Rate</td>
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">120</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">Temp</td>
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">100</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">⊗ &nbsp; Anaesthesia</td>
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">80</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">△ &nbsp; Surgery</td>
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
        <td width="15%" rowspan="8">Temp</td>
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">60</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">T &nbsp; Spont. Resp.</td>
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">40</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;">⊥ &nbsp; Cont. Resp.</td>
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;border-bottom: 1px solid #fff !important;">20</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%" rowspan="4">Tourniquet Time</td>
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            <td rowspan="2" class="vertical-middle" style="border-top: 1px solid #fff !important;">0</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="center bold">
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
        </tr>
        <tr class="bold">
            <td rowspan="30" width="2%" class="vertical-text">VENT</td>
            <td width="15%" colspan="2">O₂ Saturation</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="cbold">
            <td width="15%" colspan="2">E₂CO₂</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td width="15%" colspan="2">Tidal Volume</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td width="15%" colspan="2">Respiratiory Rate</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td width="15%" colspan="2">Peak Pressure</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td width="15%" colspan="2">Symbols of Remarks</td>
            @for($i=0;$i<38;$i++)
            <td width="8" height="8"></td>
            @endfor
            <td width="15%"></td>
        </tr>
        <tr class="bold">
            <td width="15%" rowspan="8" colspan="40">Remarks</td>

            <td width="15%" style="padding:0;">
                <table style="width:100%;margin-top:0px">
                    <tr>
                        <td style="border-top: 1px solid #ffffff !important;border-left: 1px solid #ffffff !important;"></td>
                        <td style="border-top: 1px solid #ffffff !important;text-align:center;">Anaesthesia</td>
                        <td style="border-top: 1px solid #ffffff !important; text-align:center;">Surgery</td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid #ffffff !important;">Start</td>
                        <td style="border:1px solid #000;"></td>
                        <td style="border:1px solid #000;"></td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid #ffffff !important;">End</td>
                        <td style="border:1px solid #000;"></td>
                        <td style="border:1px solid #000;"></td>
                    </tr>
                    <tr>
                        <td style="border-left: 1px solid #ffffff !important;">Duration</td>
                        <td style="border:1px solid #000;"></td>
                        <td style="border:1px solid #000;"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

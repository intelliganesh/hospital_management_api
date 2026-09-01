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

    p {
        margin: 0px !important;
    }

    .container {
        width: 100%;
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
        text-underline-offset:5px;
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
        padding: 4px;
        vertical-align: top;
        font-weight: bold;
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
        padding-bottom: 10px
    }
    </style>
</head>

<body>

    <div class="container">
       
        <div class="title">DOCTOR NOTES</div>

    <!-- Patient Details -->
    <table class="no-border" style="margin-bottom:8px;">
        <tr>
            <td width="50%">
                <b>Name of the patient:</b>
                <span class="label-line">{{ $ipd->patient_name ?? '' }}</span>
            </td>
            <td width="50%">
                <b>Age/Gender:</b>
                <span class="label-line">{{ $ipd->patient_age ?? '' }}/{{ $ipd->patient_gender ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <b>IP No:</b>
                <span class="label-line">{{ $ipd->ipd_number ?? '' }}</span>
            </td>
            <td>
                <b>Room/Bed No:</b>
                <span class="label-line">{{ $ipd->room_number ?? '' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <b>Name of the Consultant:</b>
                <span class="label-line">{{ $ipd->doctor_name ?? '' }}</span>
            </td>
        </tr>
    </table>

    <!-- Doctor Notes Table -->
    <table>
        <tr>
            <th width="12%">Date & Time</th>
            <th width="35%">Clinical Notes</th>
            <th width="18%">Attending Doctor</th>
        </tr>
        <tr>
            <td style="height: 500px;"></td>
            <td></td>
            <td></td>
        </tr>
        
    </table>

</div>
</body>
</html>

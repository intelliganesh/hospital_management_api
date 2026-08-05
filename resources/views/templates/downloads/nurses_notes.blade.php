<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Nurses Notes</title>
    <style>
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 20px;
        }

        .patient-info {
            font-size: 12px;
            margin-bottom: 20px;
        }

        .info-line {
            margin-bottom: 8px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .value {
            display: inline-block;
            border-bottom: 1px dotted #000;
            width: 150px;
            height: 14px;
            vertical-align: bottom;
            margin-right: 20px;
        }

        /* table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 10px;
        } */

        th,
        td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            font-weight: bold;
            background-color: white;
        }

        .date-column {
            width: 15%;
        }

        .intervention-column {
            width: 50%;
        }

        .remarks-column {
            width: 15%;
        }

        .signature-column {
            width: 20%;
        }

        .large-cell {
            height: 500px;
        }
    </style>
</head>

<body>
    @include('templates.downloads.letter_header', [
        'generic_letter_header' => true,
        'letter_header_address' => $patient->letter_header_address,
    ])
    <div class="form-container">
        <div class="header">Nurses Notes</div>

        <div class="patient-info">
            <div class="info-line">
                <span class="label">Name of the patient:</span>
                <span class="value">{{ $patient->first_name }} {{ $patient->last_name }}</span>

                <span class="label">Age:</span>
                <span class="value">{{ $patient->age }}</span>

                <span class="label">Gender:</span>
                <span class="value">{{ $patient->gender }}</span>
            </div>

            <div class="info-line">
                <span class="label">MR No:</span>
                <span class="value">{{ $patient->mr_number ?? '__________' }}</span>

                <span class="label">IP No:</span>
                <span class="value">{{ $patient->patient_number }}</span>

                <span class="label">Room/Bed No:</span>
                <span class="value">{{ $patient->room_no ?? '__________' }}</span>
            </div>

            <div class="info-line">
                <span class="label">Consultant Name:</span>
                <span class="value" style="width: 250px;">{{ $patient->doctor_name ?? '__________' }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="date-column">Date & Time</th>
                    <th class="intervention-column">Nursing Interventions / Note</th>
                    <th class="remarks-column">Remarks</th>
                    <th class="signature-column">Name & Signature</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="large-cell"></td>
                    <td class="large-cell"></td>
                    <td class="large-cell"></td>
                    <td class="large-cell"></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>

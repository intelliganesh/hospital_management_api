<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Follow Up</title>
    <style>
        @page {
            size: A4;
            {{-- margin: 18mm 12mm 18mm 12mm; --}}
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .meta span {
            display: inline-block;
            min-width: 48%;
            border-bottom: 1px solid #000;
            padding: 2px 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 6px;
            height: 22px;
            vertical-align: middle;
        }

        th {
            text-align: center;
            font-size: 12px;
        }

        /* Column widths tuned to fit A4 nicely */
        .w-date {
            width: 12%;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .w-ks {
            width: 11%;
        }

        .w-drs {
            width: 11%;
        }

        .w-lay {
            width: 13%;
        }

        .w-fup {
            width: 16%;
        }

        .w-iad {
            width: 13%;
        }

        .w-prim {
            width: 13%;
        }

        .w-cut {
            width: 13%;
        }

        /* Footer page mark (static, to match your sample “3/8”) */
        .pageno {
            position: fixed;
            bottom: 8mm;
            right: 12mm;
            font-size: 11px;
        }
    </style>
</head>
@php
    // Use the patientData if available, otherwise try to extract from postSurgeryFollowUps
    $name = isset($patientData) ? $patientData['name'] : null;
    $age = isset($patientData) ? $patientData['age'] : null;

    // Fallback to old method if patientData is not provided
    if ((!isset($name) || !isset($age)) && !empty($postSurgeryFollowUps) && $postSurgeryFollowUps->isNotEmpty()) {
        $postSurgeryFollowUpsData = $postSurgeryFollowUps->first();
        if (isset($postSurgeryFollowUpsData) && $postSurgeryFollowUpsData->postSurgeryDetails && $postSurgeryFollowUpsData->postSurgeryDetails->patient) {
            $patient = $postSurgeryFollowUpsData->postSurgeryDetails->patient;
            $name = $name ?? ($patient->name ?? $patient->first_name);
            $age = $age ?? ($patient->age ?? '');
        }
    }
@endphp

<body>
    <div class="title">E. FOLLOW UP</div>

    <div class="meta">
        <div>Name: <span>{{ $name ?? 'NA' }}</span></div>
        <div style="text-align:right;">Age: <span style="min-width:25%;">{{ $age ?? 'NA' }}</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="w-date">App. No.</th>
                <th class="w-date">Date</th>
               {{--  <th class="w-date">Patient Name</th>
                <th class="w-date">Patient Age</th> --}}
                <th class="w-ks">Ks Changed</th>
                <th class="w-drs">Dressing</th>
                <th class="w-lay">Partial Lay open</th>
                <th class="w-fup">Follow up examination</th>
                {{-- <th class="w-iad">New abscess I&amp;D</th> --}}
                <th class="w-prim">New tract primary threading</th>
                <th class="w-cut">Cut through/ any other</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($postSurgeryFollowUps as $key)
                <tr>
                    <td style="font-size: 10px;">{{ $key->appointment_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($key->date)->format('d-m-Y') ?? '' }}</td>
            {{--         <td>{{ $key->postSurgeryDetails->patient->name ?? $key->postSurgeryDetails->patient->first_name . ' ' . $key->postSurgeryDetails->patient->last_name }}</td>
                    <td>{{ $key->postSurgeryDetails->patient->age ?? 'N/A' }}</td> --}}
                    <td>{{ $key->ks_changed }}</td>
                    <td>{{ $key->dressing }}</td>
                    <td>{{ $key->partial_lay_open }}</td>
                    <td>{{ $key->follow_up_examination }}</td>
                    <td>{{ $key->new_abscess_threading }}</td>
                    <td>{{ $key->cut_through }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

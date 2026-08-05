<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
    }

    .container {
        width: 100%;
    }

    .title {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
        vertical-align:top;
    }

    td,
    th {
        border: 0.1rem solid #000;
        padding: 4px;
        border: none !important;
        vertical-align:top;
    }

    .no-border,
    .no-border td,
    .no-border th {}

    .section-title {
        font-weight: bold;
    }

    .text-center {
        text-align: center;
    }

    .sub-title {
        font-size: 14px;
    }

    .no-border,
    .no-border th,
    .no-border td {
        border: none;
        border-collapse: collapse;
        padding-bottom: 0px
    }

    </style>
</head>

<body>
<div class="title">Surgery Report</div>
    <div class="container" style="border:1px solid">
        
        <!-- Top Details Box -->
        <table>
            <tr>
                <td width="50%">
                    <b>I.P. No</b> : {{ $ipd->ip_no ?? '1191' }}
                </td>
                <td width="50%">
                    <b>Surgery Start Date & Time</b> :
                    {{ optional($ipd->surgery_report)->surgery_start_datetime
                        ? \Carbon\Carbon::parse($ipd->surgery_report->surgery_start_datetime)->format('d-m-Y | h:i A')
                        : '' }}
                    <br>

                    <b>Surgery End Date & Time</b> :
                    {{ optional($ipd->surgery_report)->surgery_end_datetime
                        ? \Carbon\Carbon::parse($ipd->surgery_report->surgery_end_datetime)->format('d-m-Y | h:i A')
                        : '' }}
                    <br>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <b>MR No</b> : {{ $ipd->surgery_report?->mr_no ?? '2234' }}<br>
                    <b>Age</b> : {{ $ipd->patient_age ?? '64' }}
                </td>
                <td width="50%">
                    <b>Patient's Name</b> : {{ $ipd->patient_name ?? 'VASU B R' }}<br>
                    <b>Gender</b> : {{ $ipd->patient->gender ?? '' }}
                </td>
            </tr>
        </table>
        <!-- Main Content Box -->
        <table style="border-top:1px solid">
            <tr style="padding-bottom:20px;">
                <td width="50%">
                    <b>Surgeon</b> : {{ $ipd->surgery_report?->surgeon ?? '' }}
                </td>
                <td width="50%">
                    <b>Anaesthetist</b> : {{ $ipd->surgery_report?->anaesthetist ?? '' }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Department</b> : {{$ipd->surgery_report?->department ?? '' }}
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <b>Surgery Class</b> : {{$ipd->surgery_report?->surgery_type ?? '' }}
                    <br><br>
                </td>
                <td width="50%">
                    <b>Procedure</b> : {{ $ipd->surgery_report?->surgery_name ?? '' }}
                    <br><br>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>Surgery Status</b> : {{$ipd->surgery_report?->status ?? '' }}<br><br>
                    <b>Assistant Surgeon</b> :<br>
                    {{$ipd->surgery_report?->assistant_surgeon ?? '' }}<br><br>
                    <b>Scrub Nurse</b> :<br>
                   {{$ipd->surgery_report?->scrub_nurse ?? '' }}<br><br>
                    <b>Specimen For HPE:</b><br>
                    {{$ipd->surgery_report?->specimen_for_hpe ?? '' }}<br><br>
                    <b>Operative Notes:</b><br>
                    {{$ipd->surgery_report?->operative_notes ?? '' }}<br><br>
                    <b>Operative Findings:</b><br>
                    {{$ipd->surgery_report?->operative_findings ?? '' }}<br><br>
                    <b>Post Operative Instructions:</b><br>
                    {{$ipd->surgery_report?->post_operative_instructions ?? '' }}
                    <br><br>
                    {{$ipd->surgery_report?->summary ?? '' }}
                </td>
            </tr>
            <!-- Empty area to match long layout -->
            <tr style="height:350px;">
                <td colspan="2"></td>
            </tr>
        </table>
        <!-- Footer Signature Section -->
        <table class="small-padding" style="border-top:1px solid">
            <tr >
                <td width="50%" height="50px" style="border-right:1px solid !important">
                    <b>For ACHARYA SUSHRUTHA HEALTHCARE PVT. LTD.</b>
                </td>
                <td width="50%"></td>
            </tr>
            <tr >
                <td class="signature-box" style="border-right:1px solid !important">
                    <b>{{$ipd->surgery_report?->surgeon ?? '' }}</b>
                </td>
                <td class="signature-box">
                    <b>Anaesthetist</b>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>

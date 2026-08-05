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
    vertical-align: top;
}

td, th {
    border: 0.1rem solid #000;
    padding: 4px;
    border: none !important;
    vertical-align: top;
}

.section-title {
    font-weight: bold;
}

.text-center {
    text-align: center;
}

.no-border,
.no-border th,
.no-border td {
    border: none;
}
</style>
</head>

<body>

<div class="title">Discharge Summary</div>

<div class="container" style="border:1px solid">

<!-- Top Details -->
<table>
<tr>
<td width="50%">
<b>I.P. No :</b> {{ $ipd->ipd_number ?? '' }}<br>

<b>Patient's Name :</b> {{ $ipd->patient_name ?? '' }}<br>

<b>Age/Sex :</b> {{ $ipd->patient_age ?? '' }} /
{{ $ipd->patient->gender ?? '' }}<br>

<b>MR No :</b> {{ $ipd->surgery_report?->mr_no ?? '' }}<br>

<b>Address :</b><br>
{{ $ipd->patient?->address ?? '' }}
</td>

<td width="50%">
<b>Admission Date & Time :</b>
{{ optional($ipd->admission_date_time)->format('d-m-Y | h:i A') ?? '' }}<br>

<b>Discharge Date & Time :</b>
{{ optional($ipd->discharge_date_time)->format('d-m-Y | h:i A') ?? '' }}<br><br>

<b>Doctor Incharge :</b><br>
{{ $ipd->surgery_report?->surgeon ?? '' }}
</td>
</tr>
</table>

<table style="border-top:1px solid">
<tr>
<td>

<b>Consultants:</b><br>
{{ $ipd->discharge_summary?->consultants ?? '' }}

<br><br>

<b>Diagnosis:</b><br>
{{ $ipd->discharge_summary?->diagnosis ?? '' }}

<br><br>

<b>Case History & Complaints:</b><br>
{{ $ipd->discharge_summary?->case_history ?? '' }}

<br><br>

<b>General Examination:</b><br>
{{ $ipd->discharge_summary?->general_examination ?? '' }}

<br><br>

<b>Systemic Examination:</b><br>
{{ $ipd->discharge_summary?->systemic_examination ?? '' }}

<br><br>

<b>Investigations:</b><br>
{{ $ipd->discharge_summary?->investigations ?? '' }}

<br><br>

<b>Operation Done:</b><br>
{{ $ipd->discharge_summary?->operation_done ?? '' }}

<br><br>

<b>Findings And Procedure:</b><br>
{{ $ipd->discharge_summary?->findings_and_procedure ?? '' }}

<br><br>

<b>Course In Hospital:</b><br>
{{ $ipd->discharge_summary?->course_in_hospital ?? '' }}

<br><br>

<b>Patient's health condition at discharge:</b><br>
{{ $ipd->discharge_summary?->patient_health_condition_at_discharge ?? '' }}

<br><br>

<b>Advice On Discharge:</b><br>
{!! nl2br($ipd->discharge_summary?->advice_on_discharge ?? '') !!}

</td>
</tr>

<!-- Space for Signature -->
<tr style="height:120px">
<td></td>
</tr>

</table>

<!-- Footer -->
<table style="border-top:1px solid">
<tr>
<td width="50%" style="border-right:1px solid !important">
<b>For ACHARYA SUSHRUTHA HEALTHCARE PVT. LTD.</b>
</td>

<td width="50%" class="text-center">
<b>ACHARYA SUSHRUTHA HEALTHCARE PVT. LTD.</b><br>
No. 479, 13th Cross, MPM Layout,<br>
Mallathahalli, Bengaluru – 560056.
</td>
</tr>
</table>

</div>

</body>
</html>

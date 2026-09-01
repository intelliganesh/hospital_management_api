<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Kannada&display=swap" rel="stylesheet">
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

    td,
    th {
        border: 0.1rem solid #000;
        padding: 4px;
        border: none !important;
        vertical-align: top;
    }

    .medicine{
        width: auto;
        border-collapse: collapse;
        margin-top: 5px;
        vertical-align: top;
        margin:0px 5px;
    }
    .medicine td,.medicine th{
        border: 0.05rem solid #000 !important;
        padding: 4px;
        vertical-align: top;
    }

    .section-title {
        font-weight: bold;
        margin-left: 5px;
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
                    <b>MR No :</b> <br>
                    <b>Address :</b><br>
                    {{ $ipd->patient?->address ?? '' }}
                </td>
                <td width="50%">
                    <b>Admission Date & Time :</b>
                    {{ $ipd->admission_date_time
                        ? \Carbon\Carbon::parse($ipd->admission_date_time)->format('d-m-Y | h:i A')
                        : ''
                    }}<br>

                    <b>Discharge Date & Time :</b>
                    <br><br>
                    <b>Doctor Incharge :</b><br>
                    
                </td>
            </tr>
        </table>
        <table style="border-top:1px solid">
            <tr>
                <td>
                    <b>Consultants:</b><br>
                    
                    <br><br>
                  
                    <b>Diagnosis:</b><br>
                   
                    <br><br>
                  
                    <b>Diagnosis:</b><br>
                    
                    <b>General Examination:</b><br>
                   
                    <br><br>
                   
                    <b>Systemic Examination:</b><br>
                    
                    <br><br>
                    
                    <b>Investigations:</b><br>
                    
                    <br><br>
                    
                    <b>Operation Done:</b><br>
                    
                    <br><br>
                   
                    <b>Findings And Procedure:</b><br>
                    
                    <br><br>
                    
                    <b>Course In Hospital:</b><br>
                   
                    <br><br>
                   
                    <b>Patient's health condition at discharge:</b><br>
                    
                    <br><br>
                   
                    <b>Advice On Discharge:</b><br>
                   
                    @endif
                </td>
            </tr>
        </table>

        <table>
            <tr>
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

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
                    <b>I.P. No</b> : {{ $ipd->ip_no ?? '' }}
                </td>
                <td width="50%">
                    
                            <b>Surgery Start Date & Time</b> :
                            
                            <br>
                       
                            <b>Surgery End Date & Time</b> :
                           
                        <br>
                  
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <b>MR No</b> : <br>
                    <b>Age</b> : {{ $ipd->patient_age ?? '64' }}
                </td>
                <td width="50%">
                    <b>Patient's Name</b> : {{ $ipd->patient_name ?? '' }}<br>
                    <b>Gender</b> : {{ $ipd->patient->gender ?? '' }}
                </td>
            </tr>
        </table>
        <!-- Main Content Box -->
        <table style="border-top:1px solid">
            
                <tr style="padding-bottom:20px;">
                   
                        <td width="50%">
                            <b>Surgeon</b> : 
                        </td>
                   
                        <td width="50%">
                            <b>Anaesthetist</b> : 
                        </td>
                    
                </tr>
           
                <tr>
                    <td colspan="2">
                        <b>Department</b> : 
                    </td>
                </tr>
            
                <tr>
                   
                        <td width="50%">
                            <b>Surgery Class</b> :
                            <br><br>
                        </td>
                    
                        <td width="50%">
                            <b>Procedure</b> : 
                            <br><br>
                        </td>
                   
                </tr>
            
                <tr>
                    <td colspan="2">
                        
                            <b>Surgery Status</b> : <br><br>
                        
                            <b>Assistant Surgeon</b> :<br>
                           <br><br>
                        
                            <b>Scrub Nurse</b> :<br>
                           <br><br>
                       <b>Specimen For HPE:</b><br>
                           <br><br>
                       
                            <b>Operative Notes:</b><br>
                            <br><br>
                        
                            <b>Operative Findings:</b><br>
                            <br><br>
                       
                            <b>Post Operative Instructions:</b><br>
                            
                            <br><br>
                        
                    </td>
                </tr>
            @endif
            <!-- Empty area to match long layout -->
            <tr style="height:50px;">
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
                    <b></b>
                </td>
                <td class="signature-box">
                    <b>Anaesthetist</b>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>

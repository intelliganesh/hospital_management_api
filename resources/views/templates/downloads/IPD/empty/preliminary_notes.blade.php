<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <title>In Patient / Day Care Record</title> -->
    <style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000;
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
        text-decoration: underline;
        text-underline-offset: 6px;
        margin-bottom: 20px;
    }

    .label {
        font-weight: bold;
    }

    .section-title {
        font-weight: bold;
        margin-top: 8px;
        margin-bottom: 4px;
    }

    .space {
        min-height: 30px;
    }

    .line {
        border-top: 2px solid #000;
    }

    /* Main row */
    .row {
        display: flex;
        width: 100%;
        margin-bottom: 6px;
    }

    /* Default 3-column field */
    .field {
        display: flex;
        width: 33.33%;
        align-items: flex-start;
        min-width: 0;
        box-sizing: border-box;
    }

    .label {
        font-weight: bold;
        margin-right: 6px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .value {
        white-space: normal;
        overflow-wrap: break-word;
        word-break: break-word;
        min-width: 0;
    }

    /* Address */
    .address-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
        width: 60%;
        min-width: 0;
        box-sizing: border-box;
    }

    .address-field .label {
        white-space: normal;
        margin-right: 0;
    }

    .address-field .value {
        width: 100%;
        min-width: 0;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    /* Date section */
    .date-field {
        width: 40%;
        min-width: 0;
        box-sizing: border-box;
    }

    .date-field .column {
        width: 100%;
        min-width: 0;
    }

    .column {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .pair {
        display: flex;
        width: 100%;
        min-width: 0;
        align-items: flex-start;
    }

    .pair .label {
        flex-shrink: 0;
    }

    .pair .value {
        min-width: 0;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td,
    th {
        padding: 4px;
        vertical-align: top;
    }
    </style>
</head>

<body>
  
    <div class="container">
        <div class="title">
            IN PATIENT / DAY CARE RECORD
        </div>
        <!-- Patient Basic Details -->
        <div class="row">
            <div class="field">
                <div class="label">NAME</div>
                <div class="value">
                    {{ $ipd->patient_name }}
                </div>
            </div>
            <div class="field">
                <div class="label">AGE/SEX</div>
                <div class="value">
                    {{ $ipd->patient->age ?? '' }}
                </div>
            </div>
            <div class="field">
                <div class="label">IP NUMBER</div>
                <div class="value">
                    {{ $ipd->ipd_number }}
                </div>
            </div>
        </div>
        <!-- Address + Admission / Discharge -->
        <div class="row" style="margin-top:20px;">
            <!-- Address - 60% -->
            <div class="address-field">
                <div class="label">
                    ADDRESS
                </div>
                <div class="value">
                    {{ $ipd->patient?->address ?? '' }}
                </div>
            </div>
            <!-- DOA / DOD - 40% -->
            <div class="date-field">
                <div class="column">
                    <div class="pair">
                        <div class="label">
                            DOA & TIME
                        </div>
                        <div class="value">
                            {{ date('d/m/Y h:i A', strtotime($ipd->admission_date_time)) }}
                        </div>
                    </div>
                    <div class="pair">
                        <div class="label">
                            DOD & TIME
                        </div>
                        <div class="value">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Profession -->
        <div>
            <p>
                <span class="label">PROFESSION: </span>
                {{ $ipd->patient->occupation ?? '' }}
            </p>
        </div>
        <!-- Phone Number -->
        <div>
            <p>
                <span class="label">PHONE NUMBER: </span>
                {{ $ipd->patient_phone ?? '' }}
            </p>
        </div>
        <!-- Email -->
        <div>
            <p>
                <span class="label">EMAIL ID: </span>
                {{ $ipd->patient_email ?? '' }}
            </p>
        </div>
        <!-- Passport / Aadhar -->
        <div>
            <p>
                <span class="label">PASSPORT / AADHAR NUMBER: </span>
                {{ $ipd->patient_passport_aadhar ?? '' }}
            </p>
        </div>
        <!-- Attendant Name -->
        <div>
            <p>
                <span class="label">NEAREST RELATIVE / ATTENDANT NAME: </span>
                {{ $ipd->patient_attendant_name ?? '' }}
            </p>
        </div>
        <!-- Attendant Phone -->
        <div>
            <p>
                <span class="label">PHONE NUMBER: </span>
                {{ $ipd->patient_attendant_phone ?? '' }}
            </p>
        </div>
        <!-- Consultants -->
        <div class="section-title">
            CONSULTANTS NAME AND SIGNATURE
        </div>
       
        <table>
           
            <tr>
                <td style="padding-bottom:5px;">1.</td>
            </tr>
            <tr>
                <td style="padding-bottom:5px;">2.</td>
            </tr>
            <tr>
                <td style="padding-bottom:5px;">3.</td>
            </tr>

        </table>
        
        <div class="line"></div>
        <!-- Chief Complaints -->
       
        <div style="min-height:40px">
            <p>
                <span class="label">
                    CHIEF COMPLAINTS WITH DURATION:
                </span>
                
            </p>
        </div>
       
        <!-- Associated Complaints -->
       
        <div style="min-height:40px">
            <p>
                <span class="label">
                    ASSOCIATED COMPLAINTS:
                </span>
               
            </p>
        </div>
       
        <!-- Previous Treatment History -->
  
        <div style="min-height:40px">
            <p>
                <span class="label">
                    PREVIOUS TREATMENT HISTORY:
                </span>
              
            </p>
        </div>
      
        <div style="min-height:40px">
            <p>
                <span class="label">
                    ASSOCIATED MEDICAL ILLNESS AND CURRENT TREATMENT / MEDICINES:
                </span>
             
            </p>
        </div>
        
        <div style="min-height:40px">
            <p>
                <span class="label">
                    FAMILY HISTORY:
                </span>
               
            </p>
        </div>
       
        <div style="min-height:40px">
            <p>
                <span class="label">
                    PERSONAL HISTORY:
                </span>
                
            </p>
        </div>
       
        <div style="min-height:40px">
            <p>
                <span class="label">
                    ALLERGY IF ANY:
                </span>
               
            </p>
        </div>
        
        <!-- Examination -->
        <div class="section-title">
            EXAMINATION
        </div>
        <!-- General -->
       
        <div style="margin-left:20px;margin-top:10px">
            <p class="label">A. GENERAL</p>
            <div class="row" style="margin-left:20px;" >
               
                <div class="field">
                    <div class="label">
                        BP:
                    </div>
                    <div class="value">
                       
                    </div>
                </div>
               
                <div class="field">
                    <div class="label">
                        PULSE:
                    </div>
                    <div class="value">
                       
                    </div>
                </div>
                
                <div class="field">
                    <div class="label">
                        TEMP:
                    </div>
                    <div class="value">
                       
                    </div>
                </div>
                
            </div>
        </div>
        
        <div class="row" style="margin-left:40px;margin-top:10px;">

           
            <div class="field">
                <div class="label">
                    HEIGHT:
                </div>
                <div class="value">
                  
                </div>
            </div>
           
            <div class="field">
                <div class="label">
                    WEIGHT:
                </div>
                <div class="value">
                    
                </div>
            </div>
            
            <div class="field">
                <div class="label">
                    SPO2:
                </div>
                <div class="value">
                    
                </div>
            </div>
           
        </div>
       
        <!-- CVS / RS -->
      
        <div class="row" style="margin-left:40px;margin-top:10px">
           
            <div class="field">
                <div class="label">
                    CVS:
                </div>
                <div class="value">
                    
                </div>
            </div>
           
            <div class="field">
                <div class="label">
                    RS:
                </div>
                <div class="value">
                    
                </div>
            </div>
           
        </div>
       
        <!-- Per Abdomen -->
        
        <div style="margin-left:40px;">
            <p>
                <span class="label">
                    PER ABDOMEN:
                </span>
                
            </p>
        </div>
       
        <div style="margin-left:40px;min-height:50px">
            <p>
                <span class="label">
                    LOCAL EXAMINATION:
                </span>
               
            </p>
            <div style="margin-left:20px;">
               
                <p>
                    <span class="label">
                        P/R:
                    </span>
                    
                </p>
               
                <p>
                    <span class="label">
                        DRE:
                    </span>
                   
                </p>
                
                <p>
                    <span class="label">
                        PROCTOSCOPY:
                    </span>
                
                </p>
               
            </div>
        </div>
       
        <div style="margin-left:40px;min-height:50px">
            <p>
                <span class="label">
                    EXAMINATION COMMENTS:
                </span>
                
            </p>
        </div>
       
        <div style="margin-left:40px;min-height:100px">
           
            <p>
                <span class="label">
                    INVESTIGATIONS:
                </span>
               
            </p>
           
            <div class="row" style="margin-left:20px;margin-top:20px">
               
                <div class="field">
                    <div class="label">
                        HB%:
                    </div>
                    <div class="value">
                      
                    </div>
                </div>
               
                <div class="field">
                    <div class="label">
                        TC:
                    </div>
                    <div class="value">
                       
                    </div>
                </div>
               
                <div class="field">
                    <div class="label">
                        ESR:
                    </div>
                    <div class="value">
                      
                    </div>
                </div>
               
            </div>
           
            <div class="row" style="margin-left:20px;margin-top:20px">
                
                <div class="field">
                    <div class="label">
                        RBS:
                    </div>
                    <div class="value">
                      
                    </div>
                </div>
               
                <div class="field">
                    <div class="label">
                        BT:
                    </div>
                    <div class="value">
                       
                    </div>
                </div>
                
                <div class="field">
                    <div class="label">
                        CT:
                    </div>
                    <div class="value">
                      
                    </div>
                </div>
               
            </div>
           
           
            <div class="row" style="margin-left:20px;margin-top:20px">
               
                <div class="field">
                    <div class="label">
                        Blood Urea:
                    </div>
                    <div class="value">
                       
                    </div>
                </div>
               
                <div class="field">
                    <div class="label">
                        HIV I & II:
                    </div>
                    <div class="value">
                        
                    </div>
                </div>
               
                <div class="field">
                    <div class="label">
                        HBsAG:
                    </div>
                    <div class="value">
                       
                    </div>
                </div>
                
            </div>
          
        </div>
      
        <div style="margin-left:40px;min-height:30px">
            <p>
                <span class="label">
                    PROVISIONAL DIAGNOSIS:
                </span>
               
            </p>
        </div>
      
        <div style="margin-left:40px;min-height:30px">
            <p>
                <span class="label">
                    FINAL DIAGNOSIS:
                </span>
              
            </p>
        </div>
        
        <div style="margin-left:40px;min-height:30px">
            <p>
                <span class="label">
                    LINE OF TREATMENT:MEDICAL/SURGICAL:
                </span>
                
            </p>
        </div>
       
        <div style="margin-left:40px;min-height:40px">
            <p>
                <span class="label">
                    TREATMENT ADVICED:
                </span>
                
            </p>
        </div>
       
        <div style="margin-left:40px;min-height:100px">
            <p>
                <span class="label">
                    PREOPERATIVE INSTRUCTIONS:
                </span>
               
            </p>
        </div>
       
        <div style="margin-left:40px;min-height:100px">
            <p>
                <span class="label">
                    TREATMENT GIVEN:
                </span>
                
            </p>
        </div>
       
    </div>
</body>

</html>

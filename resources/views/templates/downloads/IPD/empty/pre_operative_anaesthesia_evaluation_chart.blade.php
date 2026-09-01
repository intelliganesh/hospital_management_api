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
            border: none;
            border-collapse: collapse;
            padding-bottom: 0px
        }
        .circle{
            border:1px solid #000;
            border-radius:15px;
            padding:2px 8px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="title">
            PRE-OPERATIVE ANESTHESIA EVALUATION CHART
        </div>
        <table>
            <tr>
                <td><b>Name: </b> {{ $ipd->patient_name }} </td>
                <td><b>Age: </b> {{ $ipd->patient_age }} </td>
                <td><b>Sex: </b> {{ $ipd->patient->gender ?? '' }} </td>
                <td><b>Height: </b>  </td>
                <td><b>Weight: </b></td>
                <td><b>Date: </b> </td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="3"><b>Community: </b>  </td>
                <td colspan="3"><b>Mother Tongue: </b></td>
                <td colspan="3"><b>Hospital No: </b> </td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="50%"><b>Diagnosis</b> <br><br><br></td>
                <td width="50%"><b>Proposed Surgery</b> <br><br><br></td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="50%"><b>Previous Anaesthesia/Surgery</b> <br><br><br></td>
                <td width="50%"><b>Current Medication</b> <br><br><br></td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="50%">
                    <b class="section-title">AIRWAY ASSESSMENT</b>
                    <table class="no-border">
                        <tr>
                            <td><b>Mouth opening</b></td>
                            <td><b>Teeth</b></td>
                        </tr>
                        <tr>
                            <td><b>Neck Movements</b> </td>
                            <td><b>TMD</b> </td>
                        </tr>
                        <tr>
                            <td><b>Mallampattti Score</b> </td>
                            <td><b>Dentures</b></td>
                            <td><b></b></td>
                        </tr>
                    </table>
                </td>
                
                <td width="30%"><b>Allergies</b></td>
                <td><b>A S A Grading</b><br><br><b> 1  &nbsp;&nbsp;  2  &nbsp;&nbsp;  3 &nbsp;&nbsp;  4 &nbsp;&nbsp; E </b></td>
            </tr>
        </table>
        <table>
            <tr>
                <th class="section-title" width="28%">SYSTEMS</th>
                <th class="section-title" width="42%">CLINICAL EVALUATION</th>
                <th class="section-title" width="30%">INVESTIGATIONS</th>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">RESPIRATORY SYSTEM</p>
                    <table class="no-border">
                        <tr>
                            <td width="50%"><b>☐ Asthma</b></td>
                            <td width="50%"><b>☐ Dyspnoea</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>☐ Chronic Bronchitis</b></td>
                            <td width="50%"><b>☐ Orthopnoea</b><br><b>☐ Smoker</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>☐ COPD</b></td>
                            <td width="50%"><b>☐ Cough</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>☐ Pneumonia</b></td>
                            <td width="50%"><b>☐ Recent URI</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
                <td style="padding:0px">
                    <p style="padding:5px;border-bottom:1px solid"> <b>Hb% / HCT</b> </p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>TC</b></p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>Platelets</b> </p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>BT / CT</b> </p>
                    <p style="padding:5px;"> <b>PT / PTT</b> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">CARDIO VASCULAR SYSTEM</p>
                    <table class="no-border">
                        <tr>
                            <td width="50%"><b>☐ Hypertension</b></td>
                            <td width="50%"><b>☐ CAD / MI</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>☐ RHD / Valvular</b></td>
                            <td width="50%"><b>☐ Angina</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>☐ Dysrhythmias</b></td>
                            <td width="50%"><b>☐ Pace Maker</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>☐ Dyspnoea on Exertion</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>☐ Congestive Heart Failure</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
                <td style="padding:0px">
                    <p style="padding:5px;border-bottom:1px solid"> <b>INR</b> </p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>Blood Group</b> </p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>FBS/RBS</b> </p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>BUN/S. Greet</b> </p>
                    <p style="padding:5px;"> <b>Na 'K'</b> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">CNS MUSCULOSKELETAL</p>
                    <table class="no-border">
                        <tr>
                            <td width="50%"><b>☐ CVA / Stroke</b></td>
                            <td width="50%"><b>☐ Head Injury</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>☐ Seizures</b></td>
                            <td width="50%"><b>☐ Spinal Injury</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>☐ Paraplegia</b></td>
                            <td width="50%"><b>☐ Others</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>☐ Neuromuscular Disorder</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
                <td rowspan="2" style="padding:0px">
                    <p style="padding:5px;border-bottom:1px solid;padding-bottom:35px"> <b>Chest X-ray</b> </p>
                    <p style="padding:5px;border-bottom:1px solid;padding-bottom:35px"> <b>ECG</b> </p>
                    <p style="padding:5px;"> <b>Echo</b> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">HEPATIC / RENAL</p>
                    <table class="no-border">
                        <tr>
                            <td><b>☐ Jaundice</b></td>
                        </tr>
                        <tr>
                            <td><b>☐ Chronic Renal Failure</b></td>
                        </tr>
                        <tr>
                            <td><b>☐ Hepatitis</b></td>
                        </tr>
                        <tr>
                            <td><b>☐ Oliguria</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">ENDOCRINE</p>
                    <table class="no-border">
                        <tr>
                            <td width="50%"><b>☐ Diabetes</b></td>
                            <td width="50%"><b>☐ Ketosis</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>☐ Thyroid: Hypo&nbsp;&nbsp;Hyper</b> </td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>☐ Pituitary</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>☐ Adrenals</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
                <td rowspan="2" style="padding:0px">
                    <p style="padding:5px"> <b>Others (Eg.LFT,RFT,Thyroid function etc)</b> </p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">OTHERS</p>
                    <table class="no-border">
                        <tr>
                            <td><b>☐ Anemia</b></td>
                        </tr>
                        <tr>
                            <td><b>☐ Bleeding Disorders</b></td>
                        </tr>
                        <tr>
                            <td><b>☐ Cancer Chemotherapy</b></td>
                        </tr>
                        <tr>
                            <td><b>☐ Pregnancy</b></td>
                        </tr>
                        <tr>
                            <td><b>☐ Psychiatric Illness</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
            </tr>
        </table>
        <table>
            <tr style="height:80px;">
                <td width="50%"><b>SPECIFIC ANTICIPATED PROBLEMS OF ANESTHESIA</b>
                    
                        <br><br><br><br><br>
                </td>
                <td width="50%"><b>PRE-OPERATIVE ANESTHETIC INSTRUCTIONS AND MEDICATIONS</b>
                   
                        <br><br><br><br><br>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="no-border">
                    <b>NAME & SIGNATURE OF THE EVALUATING DOCTOR</b>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>

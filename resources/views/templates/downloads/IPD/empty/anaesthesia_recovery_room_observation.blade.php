<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
    /* ✅ Your Provided Style */
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10px;
        color: #000;
    }

    .container {
        width: 100%;
    }

    .title {
        text-align: center;
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
        border: 1px solid;
    }

    td,
    th {
        border: 0.1rem solid #000;
        padding: 4px;
        vertical-align: top;
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
        font-size: 12px;
    }

    .no-border,
    .no-border th,
    .no-border td {
        border: none;
        border-collapse: collapse;

    }

    .verticle-middle {
        vertical-align: middle;
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="text-center bold">Department of Anaesthesia</div>
        <div class="title">RECOVERY ROOM OBSERVATION</div>
        <!-- Patient Details -->
        <table>
            <tr>
                <td width="40%"><b>Name:</b> {{ $ipd->patient_name ?? '' }}</td>
                <td width="15%"><b>Age:</b> {{ $ipd->patient_age ?? '' }}</td>
                <td width="10%"><b>Sex:</b> {{ $ipd->patient->gender ?? '' }}</td>
                <td width="20%"><b>Date:</b> {{ date('d/m/Y') }}</td>
            </tr>
        </table>
        <table class="no-border" style="border: 1px solid">
            <tr>
                <td width="50%">
                    <b>Surgical Procedure:</b>
                </td>
                <td width="50%">
                    <b>Time Patient Received:</b>
                </td>
            </tr>
        </table>
        <!-- Post Operative Instructions + Monitors -->
        <table class="no-border" style="border: 1px solid">
            <tr>
                <td width="30%">
                    <b>Post Operative Instructions</b><br>
                    Routinely check the following <br>every 5 to 10 minutes:<br>
                    Pulse Rate<br>
                    Blood Pressure<br>
                    Respiration
                </td>
                <td width="70%">
                    <b>MONITORS</b><br>
                    <table class="no-border">
                        <tr>
                            <td>☐ ECG</td>
                            <td>☐ NIBP</td>
                            <td>☐ SpO₂</td>
                        </tr>
                        <tr>
                            <td>☐ ABP </td>
                            <td>☐ CVP</td>
                            <td>☐ Urine Output</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- Medications & Complications -->
        <table class="no-border" style="border: 1px solid">
            <tr>
                <td width="50%" class="section-title ">POST OPERATIVE MEDICATIONS</td>
                <td width="50%" class="section-title text-center">POST OPERATIVE COMPLICATIONS</td>
            </tr>
            <tr style="height:60px;">
                <td>
                   1.<br>
                   2.<br>
                   3.<br>
                   4.<br>
                   5.<br>
                </td>
                <td>
                    <table class="no-border">
                        <tr>
                            <td>☐ Pain</td>
                            <td>☐ Hypo / Hyperventilation</td>
                        </tr>
                        <tr>
                            <td>☐ Hypoxia </td>
                            <td>☐ Hypo / Hypertension</td>
                        </tr>
                        <tr>
                            <td>☐ Nausea / Vomiting </td>
                            <td>☐ Any Other</td>
                        </tr>
                        <tr>
                            <td>☐ Laryngospasm / Bronchospasm</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>☐ Arrhythmias</td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- Recovery Score -->
        <table>
            <tr>
                <td colspan="5" class="section-title">RECOVERY SCORE :</td>
            </tr>
            <tr class="text-center">
                <td width="5%"></td>
                <td width="25%"><b>Parameter</b></td>
                <td width="23%"><b>Score 0</b></td>
                <td width="23%"><b>Score 1</b></td>
                <td width="24%"><b>Score 2</b></td>
            </tr>
            <tr>
                <td>1</td>
                <td>Level of Consciousness</td>
                <td>Responsive only to tactile stimulation</td>
                <td>Arousable with Minimal stimulation</td>
                <td>Awake and oriented</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Physical Activity</td>
                <td>Unable to voluntarily move extremities</td>
                <td>Some weakness in movement of extremities</td>
                <td>Able to move all extremities on command</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Hemodynamic Stability</td>
                <td>
                    Blood Pressure &gt; 50% ± Baseline SBP Value
                </td>
                <td>
                    Blood Pressure = 20%–50% ± Baseline SBP Value
                </td>
                <td>
                    Blood Pressure &lt; 20% ± Baseline SBP Value
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Respiratory Stability</td>
                <td>Dyspnea with weak cough</td>
                <td>Tachypnea with good cough</td>
                <td>Able to breathe deeply</td>
            </tr>
            <tr>
                <td>5</td>
                <td>Oxygen Saturation Status</td>
                <td>
                    Saturation &lt; 90% with supplemental Oxygen
                </td>
                <td>
                    Maintains value 90% with supplemental oxygen
                </td>
                <td>
                    Maintains value &gt; 90% on room air
                </td>
            </tr>
            <tr>
                <td>6</td>
                <td>Postoperative Pain Assessment</td>
                <td>Persistent severe Pain</td>
                <td>Moderate to severe pain controlled with IV Analgesics</td>
                <td>None or mild discomfort</td>
            </tr>
            <tr>
                <td>7</td>
                <td>Postoperative Emetic Symptoms</td>
                <td>Persistent Nausea and vomiting</td>
                <td>Transient vomiting or retching</td>
                <td>None or mild nausea with no active vomiting</td>
            </tr>
        </table>
        <!-- Score Summary -->
        <table class="no-border" style="border: 1px solid;">
            <tr>
                <td width="20%">Patient's Score on Admission to Recovery:</td>
                <td width="40%" class="text-center verticle-middle title">  / 14</td>
                <td width="20%">Patient's Score on Before Transfer: </td>
                <td width="40%" class="text-center verticle-middle title"> / 14</td>
            </tr>
        </table>
        <!-- Observation Grid -->
        <table>
            <tr class="text-center">
                <td width="10%"><b>Time</b></td>
                <td width="20%"><b>Consciousness</b></td>
                <td width="10%"><b>Respiration</b></td>
                <td width="10%"><b>Pulse Rate</b></td>
                <td width="10%"><b>BP</b></td>
                <td width="10%"><b>SpO₂</b></td>
                <td width="30%"><b>Remarks</b></td>
            </tr>

            @php
                $rows = 10 // Ensure at least 10 rows
            @endphp

            @for($i = 0; $i < $rows; $i++)
            <tr style="height:25px;">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
            
        </table>
        <!-- Bottom Section -->
        <table class="no-border">
            <tr>
                <td width="50%">
                    Transfer To:
                        @foreach(['Ward','MICU','PICU','NSICU','NICU','CCU'] as $transfer)
                        
                            {{ $transfer }}
                        

                        @if(!$loop->last) / @endif
                    @endforeach<br>
                    Time: 
                </td>
                <td width="50%">
                    O₂ mask / ETT + Spont / ETT + Ventilator
                </td>
            </tr>
        </table>
        <table class="no-border">
            <tr>
                <td>Vitals at Shifting:</td>
                <td>Pulse: </td>
                <td>SBP: </td>
                <td>DBP: </td>
                <td>RR: </td>
            </tr>
            <tr>
                <td colspan="5">Post-Operative Instruction: </td>
            </tr>
            </tr>
        </table>
    </div>
</body>

</html>
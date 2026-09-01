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
        border: none !important;
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
        <div class="title">Department of Anaesthesia</div>
        <!-- Patient Basic Details -->
        <table>
            <tr>
                <td width="35%"><b>Name:</b> {{ $ipd->patient_name }}</td>
                <td width="20%"><b>Age:</b> {{ $ipd->patient_age }}</td>
                <td width="15%"><b>Sex:</b> {{ $ipd->patient->gender ?? '' }}</td>
                <td width="30%"><b>Hosp. No:</b> {{ $ipd->ipd_number ?? '' }}</td>
            </tr>
        </table>
        <!-- Pre Anaesthesia State -->
        <table>
            <tr style="font-weight:bold;font-size:12px">
                <td width="50%">
                    Pre-Anaesthesia State
                </td>
                <td width="50%">
                    NPO Status: 
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight:bold">
                    Awake &nbsp;
                    ☐ Apprehensive &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Uncooperative &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Calm &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Asleep &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Confused &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Unresponsive &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ GCS
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight:bold">
                    Ventilated Patient &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ VIA ETT &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ VIA Tracheostomy
                </td>
            </tr>
        </table>
        <!-- Patient Safety -->
        <table>
            <tr>
                <td style="font-size:12px">
                    <b>Patient Safety</b><br>
                </td>
            </tr>
            <tr>
                <td style="font-weight:bold">
                    Anes. Machine Checked &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Pressure Points Checked &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Eye Care &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Ointment &nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Eye Pad
                </td>
            </tr>
        </table>
        <!-- GENERAL ANAESTHETIC TECHNIQUE -->
        <table>
            <tr>
                <th colspan="6" class="text-center sub-title">GENERAL ANAESTHETIC TECHNIQUE</th>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Laryngoscopy</b>
                </td>
                <td>
                    ☐ Direct
                </td>
                <td>
                    ☐ Fibre optic Scope
                </td>
                <td>
                    ☐ Blind
                </td>
                <td>
                    ☐ Others
                </td>
                <td>
                    ☐ Difficult Intubation
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Endotracheal tube</b>
                </td>
                <td>
                    ☐ Oral
                </td>
                <td>
                    ☐ Nasal
                </td>
                <td>
                    ☐ Cuff
                </td>
                <td>
                    ☐ Size:  <span style="text-decoration:underline;width:100px;"> /span>
                </td>
                <td>
                    ☐ Fixedat: <span style="text-decoration:underline; width:100px;"> </span>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>ET Type</b>
                </td>
                <td>
                    ☐ Regular
                </td>
                <td>
                    ☐ Reinforced
                </td>
                <td>
                    ☐ RAE
                </td>
                <td>
                    ☐ MLS Tube
                </td>
                <td>
                    ☐ Endobronchial&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    ☐ Laser
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Airway</b>
                </td>
                <td>
                    ☐ Oral
                </td>
                <td>
                    ☐ Nasal
                </td>
                <td>
                    ☐ LMA
                </td>
                <td>
                    ☐ I-Gel
                </td>
                <td>
                    ☐ Size
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Mask Anaesthesia</b>
                </td>
                <td>
                    ☐ Nasal Cannula
                </td>
                <td>
                    ☐ Oxygen Mask
                </td>
                <td>
                    ☐ Throat Pack
                </td>
                <td>
                    -Insert <br>
                    -Removed 
                </td>
                <td>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Nasogastric Tube</b>
                </td>
                <td>
                    -Insert <br>
                    -Removed 
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                    D Regional<br>
                    Size G. Size G. Size G<br>
                    OT/Ward<br> .OT/Ward<br>OT/Ward
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td style="font-size:12px">
                    <b>Maintenance</b>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Inhalational</b>
                </td>
                <td>
                    ☐ TTVA
                </td>
                <td>
                    ☐ Regional
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td style="font-size:12px">
                    <b>IV ACCESS</b>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td colspan="6">
                    1 Site ___________________________________Size G __________________________OT / Ward
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td colspan="6">
                    2 Site ___________________________________Size G __________________________OT / Ward
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td colspan="6">
                    3 Site ___________________________________Size G __________________________OT / Ward
                </td>
            </tr>

        </table>
        <!-- Regional Anaesthesia -->
        <table>
            <tr>
                <th colspan="6" class="text-center sub-title">REGIONAL ANAESTHESIA / ANALGESIA</th>
            </tr>
            <tr>
                <td style="font-size:12px">
                    <b>Central Blocks</b>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>SPINAL</b>
                </td>
                <td>
                    ☐ Needle G: 
                </td>
                <td>
                    ☐ Catheter
                </td>
                <td>
                    ☐ Single
                </td>
                <td>
                    ☐ Cont.
                </td>
                <td rowspan="2">
                    <table>
                        <tr>
                            <th>Drugs</th>
                            <th>Conc.</th>
                            <th>Volume</th>
                        </tr>
                        <tr>
                            <td>☐ Lignocaine</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>☐ Bupivacaine</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>EPIDURAL</b>
                </td>
                <td>
                    ☐ Needle G: 
                </td>
                <td>
                    ☐ Catheter
                </td>
                <td>
                    ☐ Single
                </td>
                <td>
                    ☐ Cont.
                </td>
            </tr>
            <tr>
                <td style="font-size:12px">
                    <b>Regional Blocks</b>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Brachial Plexus</b>
                </td>
                <td>
                    ☐ Sciatic
                </td>
                <td>
                    ☐ Femoral
                </td>
                <td>
                    ☐ Ankle
                </td>
                <td>
                    ☐ Caudal
                </td>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Local<br>Others</b>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td colspan="6">
                    <table class="no-border">
                        <tr>
                            <td>☐ Nerve Stimulator</td>
                            <td>
                                ☐ Yes
                            </td>
                            <td>
                                ☐ No
                            </td>
                            <td>
                                ☐ Complete
                            </td>
                            <td>
                                ☐ Incomplete
                            </td>
                            <td>
                                ☐ Supplements
                            </td>
                            <td>
                                ☐  GA
                            </td>
                            <td>
                               ☐ Sedation
                            </td>
                            <td>
                                ☐ Complication
                            </td>
                        </tr>
                    </table>
                <td>
            </tr>
        </table>
        <!-- Monitoring -->
        <table>
            <tr>
                <th colspan="5" class="text-center sub-title">MONITORING</th>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>ECG</b>
                </td>
                <td>
                    ☐ NIBP
                </td>
                <td>
                    ☐ Pulse Oximetry
                </td>
                <td>
                    ☐ E<sub>1</sub>CO<sub>2</sub>
                </td>
                <td width="20%;" style="vertical-align: top;" rowspan="4">
                    ☐ Urine Output<br>
                    ☐ Blood Loss<br>
                    ☐ Other Fluids<br>
                    ☐ Warmer
                </td>
            </tr>
            <tr style="font-weight:bold;">
                <td>
                    <b>ABP</b>
                </td>
                <td colspan="6">
                    Site ________________________________Size G __________________________OT / ICU
                </td>

               
            </tr>
            <tr style="font-weight:bold;">
                <td>
                    <b>CVP</b>
                </td>
                <td colspan="6">
                    Site ________________________________Size G __________________________OT / ICU
                </td>
            </tr>
            <tr style="font-weight:bold;">
                <td colspan="5">
                    <b>Temperature</b>
                </td>
            </tr>
        </table>
        <!-- Fluids -->
        <table>
            <tr>
                <th colspan="3" class="text-center sub-title">TOTAL FLUIDS TRANSFUSED</th>
            </tr>
            <tr style="font-weight:bold;">
                <td>Crystalloids: </td>
                <td> ☐ Colloids: </td>
                <td> ☐ Blood: </td>
            </tr>
        </table>
        <!-- Technique Brief -->
        <table>
            <tr>
                <th class="text-center sub-title">ANAESTHESIA TECHNIQUE BRIEF</th>
            </tr>
            <tr style="height:60px;">
                <td></td>
            </tr>
        </table>
    </div>
</body>

</html>

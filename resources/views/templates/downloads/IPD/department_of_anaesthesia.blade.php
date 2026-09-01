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
    @php
        $pre_anaesthesia_state= $ipd->anaesthesiaDepartment?->pre_anaesthesia_state ? json_decode($ipd->anaesthesiaDepartment?->pre_anaesthesia_state, true) : [];
        $ventilated_patient= $ipd->anaesthesiaDepartment?->ventilated_patient ? json_decode($ipd->anaesthesiaDepartment?->ventilated_patient, true) : [];
        $patient_safety= $ipd->anaesthesiaDepartment?->patient_safety ? json_decode($ipd->anaesthesiaDepartment?->patient_safety, true) : [];
        $laryngoscopy= $ipd->anaesthesiaDepartment?->laryngoscopy ? json_decode($ipd->anaesthesiaDepartment?->laryngoscopy, true) : [];
        $endotracheal_tube= $ipd->anaesthesiaDepartment?->endotracheal_tube ? json_decode($ipd->anaesthesiaDepartment?->endotracheal_tube, true) : [];
        $et_type= $ipd->anaesthesiaDepartment?->endotracheal_tube_type ? json_decode($ipd->anaesthesiaDepartment?->endotracheal_tube_type, true) : [];
        $airway= $ipd->anaesthesiaDepartment?->airway ? json_decode($ipd->anaesthesiaDepartment?->airway, true) : [];
        $mask_anaesthesia= $ipd->anaesthesiaDepartment?->mask_anaesthesia ? json_decode($ipd->anaesthesiaDepartment?->mask_anaesthesia, true) : [];
        $nasogastric_tube= $ipd->anaesthesiaDepartment?->nasogastric_tube ? json_decode($ipd->anaesthesiaDepartment?->nasogastric_tube, true) : [];
        $maintenance= $ipd->anaesthesiaDepartment?->maintenance ? json_decode($ipd->anaesthesiaDepartment?->maintenance, true) : [];
        $central_blocks_spinal= $ipd->anaesthesiaDepartment?->central_blocks_spinal ? json_decode($ipd->anaesthesiaDepartment?->central_blocks_spinal, true) : [];
        $central_blocks_epidural= $ipd->anaesthesiaDepartment?->central_blocks_epidural ? json_decode($ipd->anaesthesiaDepartment?->central_blocks_epidural, true) : [];
        $regional_blocks= $ipd->anaesthesiaDepartment?->regional_blocks ? json_decode($ipd->anaesthesiaDepartment?->regional_blocks, true) : [];
        $nerve_stimulator= $ipd->anaesthesiaDepartment?->nerve_stimulator ? json_decode($ipd->anaesthesiaDepartment?->nerve_stimulator, true) : [];
        $regional_supplements= $ipd->anaesthesiaDepartment?->regional_supplements ? json_decode($ipd->anaesthesiaDepartment?->regional_supplements, true) : [];
        $monitoring= $ipd->anaesthesiaDepartment?->monitoring ? json_decode($ipd->anaesthesiaDepartment?->monitoring, true) : [];
        $abp_details = $ipd->anaesthesiaDepartment?->abp_details ? json_decode($ipd->anaesthesiaDepartment->abp_details, true) : [];
        $cvp_details = $ipd->anaesthesiaDepartment?->cvp_details ? json_decode($ipd->anaesthesiaDepartment->cvp_details, true) : [];
        $drugs_regional= $ipd->anaesthesiaDepartment?->drugs_regional ? json_decode($ipd->anaesthesiaDepartment?->drugs_regional, true) : [];
        if (!empty($abp_details) && isset($abp_details['site'])) {
            $abp_details = [$abp_details];
        }

        if (!empty($cvp_details) && isset($cvp_details['site'])) {
            $cvp_details = [$cvp_details];
        }
        @endphp
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
                    NPO Status: {{ $ipd->anaesthesiaDepartment?->npo_status ?? '' }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight:bold">
                    Awake &nbsp;
                    {{ in_array('Apprehensive', $pre_anaesthesia_state) ? '☑' : '☐' }} Apprehensive &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('Uncooperative', $pre_anaesthesia_state) ? '☑' : '☐' }} Uncooperative &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('Calm', $pre_anaesthesia_state) ? '☑' : '☐' }} Calm &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('Asleep', $pre_anaesthesia_state) ? '☑' : '☐' }} Asleep &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('Confused', $pre_anaesthesia_state) ? '☑' : '☐' }} Confused &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('Unresponsive', $pre_anaesthesia_state) ? '☑' : '☐' }} Unresponsive &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('GCS', $pre_anaesthesia_state) ? '☑' : '☐' }} GCS
                </td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight:bold">
                    Ventilated Patient &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('VIA ETT', $ventilated_patient) ? '☑' : '☐' }} VIAETT &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('VIA Tracheostomy', $ventilated_patient) ? '☑' : '☐' }} VIA Tracheostomy
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
                    {{ in_array('Pressure Points Checked', $patient_safety) ? '☑' : '☐' }} Pressure Points Checked &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('Eye Care', $patient_safety) ? '☑' : '☐' }} Eye Care &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('Ointment', $patient_safety) ? '☑' : '☐' }} Ointment &nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('Eye Pad', $patient_safety) ? '☑' : '☐' }} Eye Pad
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
                    {{ in_array('Direct', $laryngoscopy) ? '☑' : '☐' }} Direct
                </td>
                <td>
                    {{ in_array('Fibre optic Scope', $laryngoscopy) ? '☑' : '☐' }} Fibre optic Scope
                </td>
                <td>
                    {{ in_array('Blind', $laryngoscopy) ? '☑' : '☐' }} Blind
                </td>
                <td>
                    {{ in_array('Others', $laryngoscopy) ? '☑' : '☐' }} Others
                </td>
                <td>
                    {{ in_array('Difficult Intubation', $laryngoscopy) ? '☑' : '☐' }} Difficult Intubation
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Endotracheal tube</b>
                </td>
                <td>
                    {{ in_array('Oral', $endotracheal_tube) ? '☑' : '☐' }} Oral
                </td>
                <td>
                    {{ in_array('Nasal', $endotracheal_tube) ? '☑' : '☐' }} Nasal
                </td>
                <td>
                    {{ in_array('Cuff', $endotracheal_tube) ? '☑' : '☐' }} Cuff
                </td>
                <td>
                    {{ in_array('Size', $endotracheal_tube) ? '☑' : '☐' }} Size:  <span style="text-decoration:underline;"> {{ $ipd->anaesthesiaDepartment?->endotracheal_tube_size ?? '' }}</span>
                </td>
                <td>
                    {{ in_array('Fixedat', $endotracheal_tube) ? '☑' : '☐' }} Fixedat: <span style="text-decoration:underline;"> {{ $ipd->anaesthesiaDepartment?->endotracheal_tube_fixed_at ?? '' }}</span>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>ET Type</b>
                </td>
                <td>
                    {{ in_array('Regular', $et_type) ? '☑' : '☐' }} Regular
                </td>
                <td>
                    {{ in_array('Reinforced', $et_type) ? '☑' : '☐' }} Reinforced
                </td>
                <td>
                    {{ in_array('RAE', $et_type) ? '☑' : '☐' }} RAE
                </td>
                <td>
                    {{ in_array('MLS Tube', $et_type) ? '☑' : '☐' }} MLS Tube
                </td>
                <td>
                    {{ in_array('Endobronchial', $et_type) ? '☑' : '☐' }} Endobronchial&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ in_array('Laser', $et_type) ? '☑' : '☐' }} Laser
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Airway</b>
                </td>
                <td>
                    {{ in_array('Oral', $airway) ? '☑' : '☐' }} Oral
                </td>
                <td>
                    {{ in_array('Nasal', $airway) ? '☑' : '☐' }} Nasal
                </td>
                <td>
                    {{ in_array('LMA', $airway) ? '☑' : '☐' }} LMA
                </td>
                <td>
                    {{ in_array('I-Gel', $airway) ? '☑' : '☐' }} I-Gel
                </td>
                <td>
                    {{ in_array('Size', $airway) ? '☑' : '☐' }} Size
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Mask Anaesthesia</b>
                </td>
                <td>
                    {{ in_array('Nasal Cannula', $mask_anaesthesia) ? '☑' : '☐' }} Nasal Cannula
                </td>
                <td>
                    {{ in_array('Oxygen Mask', $mask_anaesthesia) ? '☑' : '☐' }} Oxygen Mask
                </td>
                <td>
                    {{ in_array('Throat Pack', $mask_anaesthesia) ? '☑' : '☐' }} Throat Pack
                </td>
                <td>
                    -Insert {{ strtolower($ipd->anaesthesiaDepartment?->throat_pack)=="inserted" ? '✓' : ''}}<br>
                    -Removed {{ strtolower($ipd->anaesthesiaDepartment?->throat_pack)=="removed" ? '✓' : '' }}
                </td>
                <td>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>Nasogastric Tube</b>
                </td>
                <td>
                    -Insert {{ strtolower($ipd->anaesthesiaDepartment?->nasogastric_tube)=="inserted" ? '✓' : '' }}<br>
                    -Removed {{ strtolower($ipd->anaesthesiaDepartment?->nasogastric_tube)=="removed" ? '✓' : '' }}
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
                    {{ in_array('TTVA', $maintenance) ? '☑' : '☐' }} TTVA
                </td>
                <td>
                    {{ in_array('Regional', $maintenance) ? '☑' : '☐' }} Regional
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
            <!-- <tr style="font-weight:bold">
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
            </tr> -->

            @php
                $ivAccess = json_decode($ipd->anaesthesiaDepartment?->iv_access ?? '[]', true);
                $rows = max(3, count($ivAccess));
            @endphp

            @for($i = 0; $i < $rows; $i++)
            <tr style="font-weight:bold">
                <td colspan="6">
                    {{ $i + 1 }}

                    Site
                    <span style="text-decoration:underline;">
                        {{ $ivAccess[$i]['site'] ?? '________________' }}
                    </span>

                    &nbsp;&nbsp;&nbsp;

                    Size G
                    <span style="text-decoration:underline;">
                        {{ $ivAccess[$i]['size'] ?? '________________' }}
                    </span>

                    &nbsp;&nbsp;&nbsp;

                    @if(($ivAccess[$i]['location'] ?? '') == 'OT')
                        <span class="circle">OT</span>
                    @else
                        OT
                    @endif
                    /
                    @if(($ivAccess[$i]['location'] ?? '') == 'Ward')
                        <span class="circle">Ward</span>
                    @else
                        Ward
                    @endif
                </td>
            </tr>
            @endfor
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
                    {{ in_array('Needle G:', $central_blocks_spinal) ? '☑' : '☐' }} Needle G: {{ $ipd->anaesthesiaDepartment?->central_blocks_spinal_needle_g ?? '' }}
                </td>
                <td>
                    {{ in_array('Catheter', $central_blocks_spinal) ? '☑' : '☐' }} Catheter
                </td>
                <td>
                    {{ in_array('Single', $central_blocks_spinal) ? '☑' : '☐' }} Single
                </td>
                <td>
                    {{ in_array('Cont.', $central_blocks_spinal) ? '☑' : '☐' }} Cont.
                </td>
                <td rowspan="2">
                    <table>
                        <tr>
                            <th>Drugs</th>
                            <th>Conc.</th>
                            <th>Volume</th>
                        </tr>
                        <tr>
                            @php
                                $bupivacaine = collect($regional_supplements)->firstWhere('name', 'Bupivacaine');
                                $lignocaine = collect($regional_supplements)->firstWhere('name', 'Lignocaine');
                            @endphp
                            <td>{{in_array('Lignocaine', $drugs_regional) ? '☑' : '☐' }} Lignocaine</td>
                            <td>{{$lignocaine['conc'] ?? ''}}</td>
                            <td>{{$lignocaine['vol'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>{{in_array('Bupivacaine', $drugs_regional) ? '☑' : '☐' }} Bupivacaine</td>
                            <td>{{$bupivacaine['conc'] ?? ''}}</td>
                            <td>{{$bupivacaine['vol'] ?? ''}}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="font-weight:bold">
                <td>
                    <b>EPIDURAL</b>
                </td>
                <td>
                    {{ in_array('Needle G:', $central_blocks_epidural) ? '☑' : '☐' }} Needle G: {{ $ipd->anaesthesiaDepartment?->central_blocks_epidural_g ?? '' }}
                </td>
                <td>
                    {{ in_array('Catheter', $central_blocks_epidural) ? '☑' : '☐' }} Catheter
                </td>
                <td>
                    {{ in_array('Single', $central_blocks_epidural) ? '☑' : '☐' }} Single
                </td>
                <td>
                    {{ in_array('Cont.', $central_blocks_epidural) ? '☑' : '☐' }} Cont.
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
                    {{ in_array('Sciatic', $regional_blocks) ? '☑' : '☐' }} Sciatic
                </td>
                <td>
                    {{ in_array('Femoral', $regional_blocks) ? '☑' : '☐' }} Femoral
                </td>
                <td>
                    {{ in_array('Ankle', $regional_blocks) ? '☑' : '☐' }} Ankle
                </td>
                <td>
                    {{ in_array('Caudal', $regional_blocks) ? '☑' : '☐' }} Caudal
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
                            <td>{{$nerve_stimulator  ? '☑' : '☐' }} Nerve Stimulator</td>
                            <td>
                                {{in_array('Yes', $nerve_stimulator) ? '☑' : '☐' }} Yes
                            </td>
                            <td>
                                {{in_array('No', $nerve_stimulator) ? '☑' : '☐' }} No
                            </td>
                            <td>
                                {{in_array('Complete', $nerve_stimulator) ? '☑' : '☐' }} Complete
                            </td>
                            <td>
                                {{ in_array('Incomplete', $nerve_stimulator) ? '☑' : '☐' }} Incomplete
                            </td>
                            <td>
                                {{$nerve_stimulator ? '☑' : '☐' }} Supplements
                            </td>
                            <td>
                                {{ in_array('GA', $nerve_stimulator) ? '☑' : '☐' }}  GA
                            <td>
                               {{ in_array('Sedation', $nerve_stimulator) ? '☑' : '☐' }} Sedation
                            </td>
                            <td>
                                {{ in_array('Complication', $nerve_stimulator) ? '☑' : '☐' }} Complication
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
                    {{ in_array('NIBP', $monitoring) ? '☑' : '☐' }} NIBP
                </td>
                <td>
                    {{ in_array('Pulse-Oximetry', $monitoring) ? '☑' : '☐' }} Pulse Oximetry
                </td>
                <td>
                    {{ in_array('e1co2', $monitoring) ? '☑' : '☐' }} E<sub>1</sub>CO<sub>2</sub>
                </td>
                <td width="20%;" style="vertical-align: top;" rowspan="4">
                    {{ in_array('Urine Output', $monitoring) ? '☑' : '☐' }} Urine Output<br>
                    {{ in_array('Blood Loss', $monitoring) ? '☑' : '☐' }} Blood Loss<br>
                    {{ in_array('Other Fluids', $monitoring) ? '☑' : '☐' }} Other Fluids<br>
                    {{ in_array('Warmer', $monitoring) ? '☑' : '☐' }} Warmer
                </td>
            </tr>
            <tr style="font-weight:bold;">
                <td>
                    <b>ABP</b>
                </td>
                <!-- <td colspan="6">
                    Site ________________________________Size G __________________________OT / ICU
                </td> -->

                @foreach($abp_details as $index => $abp)

                <td colspan="6">
                    {{ (int)$index + 1 }}
                    Site <span style="text-decoration:underline;">{{ $abp['site'] ?? '' }}</span>
                    &nbsp;&nbsp;&nbsp;

                    Size G <span style="text-decoration:underline;">{{ $abp['size'] ?? '' }}</span>
                    &nbsp;&nbsp;&nbsp;

                    @if( $abp['location'] == 'OT') <span class="circle">OT</span>@else OT @endif /
                    @if( $abp['location'] == 'Ward') <span class="circle">Ward</span>@else Ward @endif
                </td>

            @endforeach
            </tr>
            <tr style="font-weight:bold;">
                <td>
                    <b>CVP</b>
                </td>
                @foreach($cvp_details as $index => $cvp)

                <td colspan="6">
                    {{ (int)$index + 1 }}
                    Site <span style="text-decoration:underline;">{{ $cvp['site'] ?? '' }}</span>
                    &nbsp;&nbsp;&nbsp;

                    Size G <span style="text-decoration:underline;">{{ $cvp['size'] ?? '' }}</span>
                    &nbsp;&nbsp;&nbsp;

                    @if( $cvp['location'] == 'OT') <span class="circle">OT</span>@else OT @endif/
                    @if( $cvp['location'] == 'Ward') <span class="circle">Ward</span>@else Ward @endif
                </td>

            @endforeach
            </tr>
            <tr style="font-weight:bold;">
                <td colspan="5">
                    <b>Temperature</b> {{$ipd->anaesthesiaDepartment?->temperature ?? '' }}
                </td>
            </tr>
        </table>
        <!-- Fluids -->
        <table>
            <tr>
                <th colspan="3" class="text-center sub-title">TOTAL FLUIDS TRANSFUSED</th>
            </tr>
            <tr style="font-weight:bold;">
                <td>Crystalloids: {{$ipd->anaesthesiaDepartment?->crystalloids_ml ?? '' }}</td>
                <td> {{$ipd->anaesthesiaDepartment?->colloids_ml ? '☑' : '☐' }} Colloids: {{$ipd->anaesthesiaDepartment?->colloids_ml ?? '' }}</td>
                <td> {{$ipd->anaesthesiaDepartment?->blood_ml ? '☑' : '☐' }} Blood: {{$ipd->anaesthesiaDepartment?->blood_ml ?? '' }}</td>
            </tr>
        </table>
        <!-- Technique Brief -->
        <table>
            <tr>
                <th class="text-center sub-title">ANAESTHESIA TECHNIQUE BRIEF</th>
            </tr>
            <tr style="height:60px;">
                <td>{{ $ipd->anaesthesiaDepartment?->anaesthesia_technique_brief ?? '' }} <br><br> {{ $ipd->anaesthesiaDepartment?->summary ?? '' }}</td>
            </tr>
        </table>
    </div>
</body>

</html>

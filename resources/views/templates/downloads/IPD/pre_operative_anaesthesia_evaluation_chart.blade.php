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

@php
    $respiratory_system = $ipd->anaesthesia_pre_operative_evaluation_chart?->respiratory_system ? explode(',', $ipd->anaesthesia_pre_operative_evaluation_chart?->respiratory_system) : [];
    $cardio_vascular_system = $ipd->anaesthesia_pre_operative_evaluation_chart?->cardio_vascular_system ? explode(',', $ipd->anaesthesia_pre_operative_evaluation_chart?->cardio_vascular_system) : [];
    $cns_musculoskeletal = $ipd->anaesthesia_pre_operative_evaluation_chart?->cns_musculoskeletal ? explode(',', $ipd->anaesthesia_pre_operative_evaluation_chart?->cns_musculoskeletal) : [];
    $hepatic_renal = $ipd->anaesthesia_pre_operative_evaluation_chart?->hepatic_renal ? explode(',', $ipd->anaesthesia_pre_operative_evaluation_chart?->hepatic_renal) : [];
    $endocrine = $ipd->anaesthesia_pre_operative_evaluation_chart?->endocrine ? explode(',', $ipd->anaesthesia_pre_operative_evaluation_chart?->endocrine) : [];
    $other_system = $ipd->anaesthesia_pre_operative_evaluation_chart?->other_system ? explode(',', $ipd->anaesthesia_pre_operative_evaluation_chart?->other_system) : [];
@endphp
    <div class="container">
        <div class="title">
            PRE-OPERATIVE ANESTHESIA EVALUATION CHART
        </div>
        <table>
            <tr>
                <td><b>Name: </b> {{ $ipd->patient_name }} </td>
                <td><b>Age: </b> {{ $ipd->patient_age }} </td>
                <td><b>Sex: </b> {{ $ipd->patient->gender ?? '' }} </td>
                <td><b>Height: </b> {{$ipd->anaesthesia?->patient_height ?? ''}} </td>
                <td><b>Weight: </b> {{$ipd->anaesthesia?->patient_weight ?? ''}} </td>
                <td><b>Date: </b> {{ date('d/m/Y') }} </td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="3"><b>Community: </b> {{$ipd->anaesthesia?->patient_community ?? ''}} </td>
                <td colspan="3"><b>Mother Tongue: </b> {{$ipd->anaesthesia?->mother_tongue ?? ''}} </td>
                <td colspan="3"><b>Hospital No: </b> {{$ipd->ipd_number ?? ''}} </td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="50%"><b>Diagnosis</b> {{$ipd->anaesthesia?->diagnosis ?? ''}} <br><br><br></td>
                <td width="50%"><b>Proposed Surgery</b> {{$ipd->surgery_report?->surgery_name ?? ''}} <br><br><br></td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="50%"><b>Previous Anaesthesia/Surgery</b> {{$ipd->anaesthesia_pre_operative_evaluation_chart?->previous_anaesthesia_surgery ?? ''}}<br><br><br></td>
                <td width="50%"><b>Current Medication</b> {{$ipd->anaesthesia_pre_operative_evaluation_chart?->current_medication ?? ''}}<br><br><br></td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="50%">
                    <b class="section-title">AIRWAY ASSESSMENT</b>
                    <table class="no-border">
                        <tr>
                            <td><b>Mouth opening</b> {{$ipd->anaesthesia_pre_operative_evaluation_chart?->mouth_opening ?? ''}}</td>
                            <td><b>Teeth</b> {{$ipd->anaesthesia_pre_operative_evaluation_chart?->teeth ?? ''}}</td>
                        </tr>
                        <tr>
                            <td><b>Neck Movements</b> {{$ipd->anaesthesia_pre_operative_evaluation_chart?->neck_movements ?? ''}}</td>
                            <td><b>TMD</b> {{$ipd->anaesthesia_pre_operative_evaluation_chart?->tmd ?? ''}}
                        </tr>
                        <tr>
                            <td><b>Mallampattti Score</b> {{$ipd->anaesthesia_pre_operative_evaluation_chart?->mallampatti_score ?? ''}}</td>
                            <td><b>Dentures</b></td>
                            <td><b>{{$ipd->anaesthesia_pre_operative_evaluation_chart?->dentures_check ?? ''}}</b></td>
                        </tr>
                    </table>
                </td>
                @php $selected = $ipd->anaesthesia_pre_operative_evaluation_chart?->asa_grading ?? ''; @endphp
                <td width="30%"><b>Allergies</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->allergies ?? '' }}</td>
                <td><b>A S A Grading</b><br><br><b> @if($selected=="1") <span class="circle">1</span> @else 1 @endif &nbsp;&nbsp; @if($selected=="2") <span class="circle">2</span> @else 2 @endif&nbsp;&nbsp; @if($selected=="3") <span class="circle">3</span> @else 3 @endif&nbsp;&nbsp; @if($selected=="4") <span class="circle">4</span> @else 4 @endif&nbsp;&nbsp; @if($selected=="E") <span class="circle">E</span> @else E @endif</b></td>
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
                            <td width="50%"><b>{{ in_array('Asthma', $respiratory_system) ? '☑' : '☐' }} Asthma</b></td>
                            <td width="50%"><b>{{ in_array('Dyspnoea', $respiratory_system) ? '☑' : '☐' }} Dyspnoea</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>{{ in_array('Chronic Bronchitis', $respiratory_system) ? '☑' : '☐' }} Chronic Bronchitis</b></td>
                            <td width="50%"><b>{{ in_array('Orthopnoea', $respiratory_system) ? '☑' : '☐' }} Orthopnoea</b><br><b>{{ in_array('Smoker', $respiratory_system   ) ? '☑' : '☐' }} Smoker</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>{{ in_array('COPD', $respiratory_system) ? '☑' : '☐' }} COPD</b></td>
                            <td width="50%"><b>{{ in_array('Cough', $respiratory_system) ? '☑' : '☐' }} Cough</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>{{ in_array('Pneumonia', $respiratory_system) ? '☑' : '☐' }} Pneumonia</b></td>
                            <td width="50%"><b>{{ in_array('Recent URI', $respiratory_system) ? '☑' : '☐' }} Recent URI</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
                <td style="padding:0px">
                    <p style="padding:5px;border-bottom:1px solid"> <b>Hb% / HCT</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->hb_hct ?? '' }}</p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>TC</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->tc ?? '' }}</p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>Platelets</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->platelets ?? '' }}</p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>BT / CT</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->bt_ct ?? '' }}</p>
                    <p style="padding:5px;"> <b>PT / PTT</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->pt_ptt ?? '' }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">CARDIO VASCULAR SYSTEM</p>
                    <table class="no-border">
                        <tr>
                            <td width="50%"><b>{{ in_array('Hypertension', $cardio_vascular_system) ? '☑' : '☐' }} Hypertension</b></td>
                            <td width="50%"><b>{{ in_array('CAD / MI', $cardio_vascular_system) ? '☑' : '☐' }} CAD / MI</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>{{ in_array('RHD / Valvular', $cardio_vascular_system) ? '☑' : '☐' }} RHD / Valvular</b></td>
                            <td width="50%"><b>{{ in_array('Angina', $cardio_vascular_system) ? '☑' : '☐' }} Angina</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>{{ in_array('Dysrhythmias', $cardio_vascular_system) ? '☑' : '☐' }} Dysrhythmias</b></td>
                            <td width="50%"><b>{{ in_array('Pace Maker', $cardio_vascular_system) ? '☑' : '☐' }} Pace Maker</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>{{ in_array('Dyspnoea on Exertion', $cardio_vascular_system) ? '☑' : '☐' }} Dyspnoea on Exertion</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>{{ in_array('Congestive Heart Failure', $cardio_vascular_system) ? '☑' : '☐' }} Congestive Heart Failure</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
                <td style="padding:0px">
                    <p style="padding:5px;border-bottom:1px solid"> <b>INR</b>  {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->inr ?? '' }}</p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>Blood Group</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->blood_group ?? '' }}</p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>FBS/RBS</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->fbs_rbs ?? '' }}</p>
                    <p style="padding:5px;border-bottom:1px solid"> <b>BUN/S. Greet</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->bun ?? '' }}</p>
                    <p style="padding:5px;"> <b>Na 'K'</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->na_k ?? '' }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">CNS MUSCULOSKELETAL</p>
                    <table class="no-border">
                        <tr>
                            <td width="50%"><b>{{ in_array('CVA / Stroke', $cns_musculoskeletal) ? '☑' : '☐' }} CVA / Stroke</b></td>
                            <td width="50%"><b>{{ in_array('Head Injury', $cns_musculoskeletal) ? '☑' : '☐' }} Head Injury</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>{{ in_array('Seizures', $cns_musculoskeletal) ? '☑' : '☐' }} Seizures</b></td>
                            <td width="50%"><b>{{ in_array('Spinal Injury', $cns_musculoskeletal) ? '☑' : '☐' }} Spinal Injury</b></td>
                        </tr>
                        <tr>
                            <td width="50%"><b>{{ in_array('Paraplegia', $cns_musculoskeletal) ? '☑' : '☐' }} Paraplegia</b></td>
                            <td width="50%"><b>{{ in_array('Others', $cns_musculoskeletal) ? '☑' : '☐' }} Others</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>{{ in_array('Neuromuscular Disorder', $cns_musculoskeletal) ? '☑' : '☐' }} Neuromuscular Disorder</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
                <td rowspan="2" style="padding:0px">
                    <p style="padding:5px;border-bottom:1px solid;padding-bottom:35px"> <b>Chest X-ray</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->chest_xray ?? '' }}</p>
                    <p style="padding:5px;border-bottom:1px solid;padding-bottom:35px"> <b>ECG</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->ecg ?? '' }}</p>
                    <p style="padding:5px;"> <b>Echo</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->echo ?? '' }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">HEPATIC / RENAL</p>
                    <table class="no-border">
                        <tr>
                            <td><b>{{ in_array('Jaundice', $hepatic_renal) ? '☑' : '☐' }} Jaundice</b></td>
                        </tr>
                        <tr>
                            <td><b>{{ in_array('Chronic Renal Failure', $hepatic_renal) ? '☑' : '☐' }} Chronic Renal Failure</b></td>
                        </tr>
                        <tr>
                            <td><b>{{ in_array('Hepatitis', $hepatic_renal) ? '☑' : '☐' }} Hepatitis</b></td>
                        </tr>
                        <tr>
                            <td><b>{{ in_array('Oliguria', $hepatic_renal) ? '☑' : '☐' }} Oliguria</b></td>
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
                            <td width="50%"><b>{{ in_array('Diabetes', $endocrine) ? '☑' : '☐' }} Diabetes</b></td>
                            <td width="50%"><b>{{ in_array('Ketosis', $endocrine) ? '☑' : '☐' }} Ketosis</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>{{ in_array('Thyroid', $endocrine) ? '☑' : '☐' }} Thyroid: Hypo&nbsp;&nbsp;Hyper</b> </td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>{{ in_array('Pituitary', $endocrine) ? '☑' : '☐' }} Pituitary</b></td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>{{ in_array('Adrenals', $endocrine) ? '☑' : '☐' }}  Adrenals</b></td>
                        </tr>
                    </table>
                </td>
                <td>
                </td>
                <td rowspan="2" style="padding:0px">
                    <p style="padding:5px"> <b>Others (Eg.LFT,RFT,Thyroid function etc)</b> {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->other_investigation ?? '' }}</p>
                </td>
            </tr>
            <tr>
                <td>
                    <p class="section-title text-center">OTHERS</p>
                    <table class="no-border">
                        <tr>
                            <td><b>{{ in_array('Anemia', $other_system) ? '☑' : '☐' }} Anemia</b></td>
                        </tr>
                        <tr>
                            <td><b>{{ in_array('Bleeding Disorders', $other_system) ? '☑' : '☐' }} Bleeding Disorders</b></td>
                        </tr>
                        <tr>
                            <td><b>{{ in_array('Cancer Chemotherapy', $other_system) ? '☑' : '☐' }} Cancer Chemotherapy</b></td>
                        </tr>
                        <tr>
                            <td><b>{{ in_array('Pregnancy', $other_system) ? '☑' : '☐' }} Pregnancy</b></td>
                        </tr>
                        <tr>
                            <td><b>{{ in_array('Psychiatric Illness', $other_system) ? '☑' : '☐' }} Psychiatric Illness</b></td>
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
                    @if($ipd->anaesthesia_pre_operative_evaluation_chart?->specific_anaesthesia_problem)
                        {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->specific_anaesthesia_problem }}
                    @else
                        <br><br><br><br><br>
                    @endif
                </td>
                <td width="50%"><b>PRE-OPERATIVE ANESTHETIC INSTRUCTIONS AND MEDICATIONS</b>
                    @if($ipd->anaesthesia_pre_operative_evaluation_chart?->pre_operative_anaesthesia_instruction)
                        {{ $ipd->anaesthesia_pre_operative_evaluation_chart?->pre_operative_anaesthesia_instruction }}
                    @else
                        <br><br><br><br><br>
                    @endif
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

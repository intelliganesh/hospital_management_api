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
            /* border-bottom: 2px solid #000; */
            /* padding: 5px 0; */
            text-decoration:underline;
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
    </style>
</head>

<body>
    @php
        $hasValue = fn($value) => !is_null($value) && trim((string) $value) !== '';
    @endphp

    <div class="container">
        <div class="title">
            IN PATIENT / DAY CARE RECORD
        </div>

        <div class="row">
            <div class="field">
                <div class="label">NAME</div>
                <div class="value">{{$ipd->patient_name}}</div>
            </div>

            <div class="field">
                <div class="label">AGE/SEX</div>
                <div class="value">{{$ipd->patient->age ?? ''}}</div>
            </div>

            <div class="field">
                <div class="label">IP NUMBER</div>
                <div class="value">{{$ipd->ipd_number}}</div>
            </div>
        </div>
        <div class="row" style="margin-top:20px;">
            <div class="field" style="width:60%;">
                <div class="label" style="min-height:60px;">ADDRESS</div>
                <div class="value">{{$ipd->patient_address ?? ''}}</div>
            </div>

            <div class="field">
                <div class="column">
                    <div class="pair">
                        <div class="label">DOA & TIME</div>
                        <div class="value">{{date('d/m/Y h:i A',strtotime($ipd->admission_date_time))}}</div>
                    </div>

                    <div class="pair">
                        <div class="label">DOD & TIME</div>
                        <div class="value">{{!is_null($ipd->discharge_date_time) ? date('d/m/Y h:i A',strtotime($ipd->discharge_date_time)) : ''}}</div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <p><span class="label">PROFESSION: </span>{{$ipd->patient->occupation ?? ''}}
            </p>
        </div>
        <div>
            <p><span class="label">PHONE NUMBER: </span>{{ $ipd->patient_phone ?? ''}}
            </p>
        </div>
        <div>
            <p><span class="label">EMAIL ID: </span>{{ $ipd->patient_email ?? ''}}
            </p>
        </div>
        <div>
            <p><span class="label">PASSPORT / AADHAR NUMBER: </span>{{$ipd->patient_}}
            </p>
        </div>
        <div>
            <p><span class="label">NEAREST RELATIVE / ATTENDANT NAME: </span>{{ $ipd->patient_attendant_name ?? ''}}
            </p>
        </div>
        <div>
            <p><span class="label">PHONE NUMBER: </span>{{ $ipd->patient_attendant_phone ?? ''}}
            </p>
        </div>

        <div class="section-title">CONSULTANTS NAME AND SIGNATURE</div>
        @php
            $doctors = $ipd->consultantDoctors;
            $count = count($doctors);
            $totalRows = max(3, $count);
        @endphp

        <table>
            @for($i = 0; $i < $totalRows; $i++)
                <tr>
                    <td style="padding-bottom:5px;">
                        {{ $i + 1 }}.
                        {{ $doctors[$i]->user->name ?? '' }}
                    </td>
                </tr>
            @endfor
        </table>

        @php
            $preliminaryNotes = $ipd->preliminaryNotes->first();
        @endphp

        @if($preliminaryNotes)
            <div class="line"></div>

            @if($hasValue($preliminaryNotes->chief_complaint))
                <div style="min-height:40px">
                    <p><span class="label">CHIEF COMPLAINTS WITH DURATION: </span>{{ $preliminaryNotes->chief_complaint }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->associated_complaint))
                <div style="min-height:40px">
                    <p><span class="label">ASSOCIATED COMPLAINTS: </span>{{ $preliminaryNotes->associated_complaint }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->previous_treatment_history))
                <div style="min-height:40px">
                    <p><span class="label">PREVIOUS TREATMENT HISTORY: </span>{{ $preliminaryNotes->previous_treatment_history }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->medical_history))
                <div style="min-height:40px">
                    <p><span class="label">ASSOCIATED MEDICAL ILLNESS AND CURRENT TREATMENT / MEDICINES: </span>{{ $preliminaryNotes->medical_history }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->family_history))
                <div style="min-height:40px">
                    <p><span class="label">FAMILY HISTORY: </span>{{ $preliminaryNotes->family_history }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->personal_history))
                <div style="min-height:40px">
                    <p><span class="label">PERSONAL HISTORY: </span>{{ $preliminaryNotes->personal_history }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->allergy))
                <div style="min-height:40px">
                    <p><span class="label">ALLERGY IF ANY: </span>{{ $preliminaryNotes->allergy }}</p>
                </div>
            @endif

            @if(
                $hasValue($preliminaryNotes->bp) ||
                $hasValue($preliminaryNotes->pulse) ||
                $hasValue($preliminaryNotes->temp) ||
                $hasValue($preliminaryNotes->height) ||
                $hasValue($preliminaryNotes->weight) ||
                $hasValue($preliminaryNotes->spo2) ||
                $hasValue($preliminaryNotes->cvs) ||
                $hasValue($preliminaryNotes->rs) ||
                $hasValue($preliminaryNotes->per_abdomen) ||
                $hasValue($preliminaryNotes->local_examination) ||
                $hasValue($preliminaryNotes->pr) ||
                $hasValue($preliminaryNotes->dre) ||
                $hasValue($preliminaryNotes->proctoscopy)
            )
                <div class="section-title">EXAMINATION</div>

                @if(
                    $hasValue($preliminaryNotes->bp) ||
                    $hasValue($preliminaryNotes->pulse) ||
                    $hasValue($preliminaryNotes->temp)
                )
                    <div class="row" style="margin-left:20px;margin-top:10px">
                        <div class="field">
                            <div class="label">A. GENERAL</div>
                        </div>

                        @if($hasValue($preliminaryNotes->bp))
                            <div class="field">
                                <div class="label">BP:</div>
                                <div class="value">{{ $preliminaryNotes->bp }}</div>
                            </div>
                        @endif

                        @if($hasValue($preliminaryNotes->pulse))
                            <div class="field">
                                <div class="label">PULSE:</div>
                                <div class="value">{{ $preliminaryNotes->pulse }}</div>
                            </div>
                        @endif

                        @if($hasValue($preliminaryNotes->temp))
                            <div class="field">
                                <div class="label">TEMP:</div>
                                <div class="value">{{ $preliminaryNotes->temp }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                @if(
                    $hasValue($preliminaryNotes->height) ||
                    $hasValue($preliminaryNotes->weight) ||
                    $hasValue($preliminaryNotes->spo2)
                )
                    <div class="row" style="margin-left:20px;margin-top:10px;">
                        <div class="field">
                            <div class="label"></div>
                        </div>

                        @if($hasValue($preliminaryNotes->height))
                            <div class="field">
                                <div class="label">HEIGHT:</div>
                                <div class="value">{{ $preliminaryNotes->height }}</div>
                            </div>
                        @endif

                        @if($hasValue($preliminaryNotes->weight))
                            <div class="field">
                                <div class="label">WEIGHT:</div>
                                <div class="value">{{ $preliminaryNotes->weight }}</div>
                            </div>
                        @endif

                        @if($hasValue($preliminaryNotes->spo2))
                            <div class="field">
                                <div class="label">SPO2:</div>
                                <div class="value">{{ $preliminaryNotes->spo2 }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                @if($hasValue($preliminaryNotes->cvs) || $hasValue($preliminaryNotes->rs))
                    <div class="row" style="margin-left:40px;margin-top:20px">
                        @if($hasValue($preliminaryNotes->cvs))
                            <div class="field">
                                <div class="label">CVS:</div>
                                <div class="value">{{ $preliminaryNotes->cvs }}</div>
                            </div>
                        @endif

                        @if($hasValue($preliminaryNotes->rs))
                            <div class="field">
                                <div class="label">RS:</div>
                                <div class="value">{{ $preliminaryNotes->rs }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                @if($hasValue($preliminaryNotes->per_abdomen))
                    <div style="margin-left:40px;">
                        <p><span class="label">PER ABDOMEN: </span>{{ $preliminaryNotes->per_abdomen }}</p>
                    </div>
                @endif

                @if(
                    $hasValue($preliminaryNotes->local_examination) ||
                    $hasValue($preliminaryNotes->pr) ||
                    $hasValue($preliminaryNotes->dre) ||
                    $hasValue($preliminaryNotes->proctoscopy)
                )
                    <div style="margin-left:40px;min-height:50px">
                        @if($hasValue($preliminaryNotes->local_examination))
                            <p><span class="label">LOCAL EXAMINATION: </span>{{ $preliminaryNotes->local_examination }}</p>
                        @endif

                        <div style="margin-left:20px;">
                            @if($hasValue($preliminaryNotes->pr))
                                <p><span class="label">P/R:</span>{{ $preliminaryNotes->pr }}</p>
                            @endif
                            @if($hasValue($preliminaryNotes->dre))
                                <p><span class="label">DRE:</span>{{ $preliminaryNotes->dre }}</p>
                            @endif
                            @if($hasValue($preliminaryNotes->proctoscopy))
                                <p><span class="label">PROCTOSCOPY:</span>{{ $preliminaryNotes->proctoscopy }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                @if($hasValue($preliminaryNotes->examination_comments))
                    <div style="margin-left:40px;min-height:50px">
                        @if($hasValue($preliminaryNotes->examination_comments))
                            <p><span class="label">EXAMINATION COMMENTS: </span>{{ $preliminaryNotes->examination_comments }}</p>
                        @endif
                    </div>
                @endif

                @if(
                    $hasValue($preliminaryNotes->investigation) ||
                    $hasValue($preliminaryNotes->hb) ||
                    $hasValue($preliminaryNotes->tc) ||
                    $hasValue($preliminaryNotes->esr) ||
                    $hasValue($preliminaryNotes->rbs) ||
                    $hasValue($preliminaryNotes->bt) ||
                    $hasValue($preliminaryNotes->ct) ||
                    $hasValue($preliminaryNotes->blood_urea) ||
                    $hasValue($preliminaryNotes->hiv) ||
                    $hasValue($preliminaryNotes->hbsag)
                )
                    <div style="margin-left:40px;min-height:100px">
                        @if($hasValue($preliminaryNotes->investigation))
                            <p><span class="label">INVESTIGATIONS: </span>{{ $preliminaryNotes->investigation }}</p>
                        @endif

                        @if(
                            $hasValue($preliminaryNotes->hb) ||
                            $hasValue($preliminaryNotes->tc) ||
                            $hasValue($preliminaryNotes->esr)
                        )
                            <div class="row" style="margin-left:20px;margin-top:20px">
                                @if($hasValue($preliminaryNotes->hb))
                                    <div class="field">
                                        <div class="label">HB%:</div>
                                        <div class="value">{{ $preliminaryNotes->hb }}</div>
                                    </div>
                                @endif
                                @if($hasValue($preliminaryNotes->tc))
                                    <div class="field">
                                        <div class="label">TC:</div>
                                        <div class="value">{{ $preliminaryNotes->tc }}</div>
                                    </div>
                                @endif
                                @if($hasValue($preliminaryNotes->esr))
                                    <div class="field">
                                        <div class="label">ESR:</div>
                                        <div class="value">{{ $preliminaryNotes->esr }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(
                            $hasValue($preliminaryNotes->rbs) ||
                            $hasValue($preliminaryNotes->bt) ||
                            $hasValue($preliminaryNotes->ct)
                        )
                            <div class="row" style="margin-left:20px;margin-top:20px">
                                @if($hasValue($preliminaryNotes->rbs))
                                    <div class="field">
                                        <div class="label">RBS:</div>
                                        <div class="value">{{ $preliminaryNotes->rbs }}</div>
                                    </div>
                                @endif
                                @if($hasValue($preliminaryNotes->bt))
                                    <div class="field">
                                        <div class="label">BT:</div>
                                        <div class="value">{{ $preliminaryNotes->bt }}</div>
                                    </div>
                                @endif
                                @if($hasValue($preliminaryNotes->ct))
                                    <div class="field">
                                        <div class="label">CT:</div>
                                        <div class="value">{{ $preliminaryNotes->ct }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(
                            $hasValue($preliminaryNotes->blood_urea) ||
                            $hasValue($preliminaryNotes->hiv) ||
                            $hasValue($preliminaryNotes->hbsag)
                        )
                            <div class="row" style="margin-left:20px;margin-top:20px">
                                @if($hasValue($preliminaryNotes->blood_urea))
                                    <div class="field">
                                        <div class="label">Blood Urea:</div>
                                        <div class="value">{{ $preliminaryNotes->blood_urea }}</div>
                                    </div>
                                @endif
                                @if($hasValue($preliminaryNotes->hiv))
                                    <div class="field">
                                        <div class="label">HIV I & II:</div>
                                        <div class="value">{{ $preliminaryNotes->hiv }}</div>
                                    </div>
                                @endif
                                @if($hasValue($preliminaryNotes->hbsag))
                                    <div class="field">
                                        <div class="label">HBsAG:</div>
                                        <div class="value">{{ $preliminaryNotes->hbsag }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            @endif

            @if($hasValue($preliminaryNotes->provisional_diagnosis))
                <div style="margin-left:40px;min-height:30px">
                    <p><span class="label">PROVISIONAL DIAGNOSIS: </span>{{ $preliminaryNotes->provisional_diagnosis }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->final_diagnosis))
                <div style="margin-left:40px;min-height:30px">
                    <p><span class="label">FINAL DIAGNOSIS: </span>{{ $preliminaryNotes->final_diagnosis }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->line_of_treatment))
                <div style="margin-left:40px;min-height:30px">
                    <p><span class="label">LINE OF TREATMENT:MEDICAL/SURGICAL: </span>{{ $preliminaryNotes->line_of_treatment }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->treatment_advised))
                <div style="margin-left:40px;min-height:40px">
                    <p><span class="label">TREATMENT ADVICED: </span>{{ $preliminaryNotes->treatment_advised }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->preoperative_instruction))
                <div style="margin-left:40px;min-height:100px">
                    <p><span class="label">PREOPERATIVE INSTRUCTIONS: </span>{{ $preliminaryNotes->preoperative_instruction }}</p>
                </div>
            @endif

            @if($hasValue($preliminaryNotes->treatment_given))
                <div style="margin-left:40px;min-height:100px">
                    <p><span class="label">TREATMENT GIVEN: </span>{{ $preliminaryNotes->treatment_given }}</p>
                </div>
            @endif
        @endif
    </div>
</body>

</html>

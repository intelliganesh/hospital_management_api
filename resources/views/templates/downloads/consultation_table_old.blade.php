@php
    $proctologyType = $department_type == 'Proctology' ? true : false;
    $nonProctologyType = $department_type == 'Non Proctology' ? true : false;
@endphp
<style>
    .section {
        margin-top: 20px;
        padding: 0px 15px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }

    li,
    p {
        font-size: 12px;
    }

    .info-table td,
    .info-table th {
        padding: 8px;
        border: 1px solid #ccc;
        font-size: 12px;
        vertical-align: top;
    }

    .label {
        font-weight: bold;
        white-space: nowrap;
    }

    .section-title {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 8px;
    }

    ul {
        margin: 0;
        padding-left: 20px;
    }

    .attachment-link {
        color: #0d6efd;
    }

    .footer-note {
        font-size: 12px;
    }

    ol{
        padding-left:10px
    }
</style>
    <h2 style="text-align:center; font-size: 18px;">CONSULTATION REPORT</h2>

    {{-- 1. Patient Details --}}
    <div class="section">
        <div class="section-title">Patient Information</div>
        <table class="info-table">
            <tr>
                <td class="label">Name</td>
                <td>{{ $patient_name }}</td>
                <td class="label">Patient No</td>
                <td>{{ $patient_number }}</td>
                <td class="label">Age / Gender</td>
                <td>{{ $age }} / {{ $gender }}</td>
            </tr>
            <tr>
                <td class="label">Phone</td>
                <td>{{ $patient_phone }}</td>
                <td class="label">Email</td>
                <td colspan="3">{{ $patient_email ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- 2. Consultation Details --}}
    <div class="section">
        <div class="section-title">Consultation Information</div>
        <table class="info-table">
            <tr>
                <td class="label">Appointment No</td>
                <td>{{ $appointment_number }}</td>
                <td class="label">Type</td>
                <td>{{ $appointment_type }}</td>
                <td class="label">Consultation Type</td>
                <td>{{ $consultation_type }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>{{ $status }}</td>
                <td class="label">Front Desk</td>
                <td colspan="3">{{ $front_desk_user_name }}</td>
            </tr>
        </table>
    </div>


    {{-- 3. Doctor Info --}}
    <div class="section">
        <div class="section-title">Doctor Details</div>
        <table class="info-table">
            <tr>
                <td class="label">Doctor Name</td>
                <td>{{ $doctor_name }}</td>
                <td class="label">Qualification</td>
                <td>{{ $qualification }}</td>
                <td class="label">Contact</td>
                <td>{{ $doctor_email }} / {{ $doctor_phone }}</td>
            </tr>
        </table>
    </div>

    {{-- 4. Clinical Notes --}}
    <div class="section">
        <div class="section-title">Clinical Summary</div>
        <table class="info-table">
            <tr>
                <td class="label">Chief Complaints</td>
                @php
                    $chiefComplaints = $protologyOrNonProctology['chief_complaints'] ?? [];

                    if (is_string($chiefComplaints)) {
                        $chiefComplaints = json_decode($chiefComplaints, true) ?? [];
                    }
                @endphp
                @if (count($chiefComplaints) > 0)
                    <td>
                        <ol>

                            @foreach ($chiefComplaints as $item)
                                <li>{{ $item['label'] ?? $item }}</li>
                            @endforeach
                        </ol>
                    </td>
                @else
                    <td>-</td>
                @endif
                <td class="label">Preliminary Diagnosis</td>
                @php
                    $diagnosisList = $preliminary_diagnosis ?? [];

                    if (is_string($diagnosisList)) {
                        $diagnosisList = json_decode($diagnosisList, true) ?? [];
                    }
                @endphp
                @if (count($diagnosisList) > 0)
                    <td>
                        <ol>

                            @foreach ($diagnosisList as $item)
                                <li>{{ $item['label'] ?? $item }}</li>
                            @endforeach

                        </ol>
                    </td>
                @else
                    <td>-</td>
                @endif
            </tr>
            <tr>
                <td class="label">On Examination</td>
                @php
                    $onExamination = $protologyOrNonProctology['on_examination'] ?? [];

                    if (is_string($onExamination)) {
                        $onExamination = json_decode($onExamination, true) ?? [];
                    }
                @endphp
                @if (count($onExamination) > 0)
                    <td>
                        <ol>

                            @foreach ($onExamination as $item)
                                <li>{{ $item['label'] ?? $item }}</li>
                            @endforeach

                        </ol>
                    </td>
                @else
                    <td>-</td>
                @endif
                <td class="label">Surgical History</td>
                @php
                    $surgicalHistory = $protologyOrNonProctology['surgical_history'] ?? [];

                    if (is_string($surgicalHistory)) {
                        $surgicalHistory = json_decode($surgicalHistory, true) ?? [];
                    }
                @endphp
                @if (count($surgicalHistory) > 0)
                    <td>
                        <ol>

                            @foreach ($surgicalHistory as $item)
                                <li>{{ $item['label'] ?? $item }}</li>
                            @endforeach

                        </ol>
                    </td>
                @else
                    <td>-</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    <td class="label">DRE</td>
                    @php
                        $dre = $protologyOrNonProctology['dre'] ?? [];

                        if (is_string($dre)) {
                            $dre = json_decode($dre, true) ?? [];
                        }
                    @endphp
                    @if (count($dre) > 0)
                        <td>
                            <ol>

                                @foreach ($dre as $item)
                                    <li>{{ $item['label'] ?? $item }}</li>
                                @endforeach

                            </ol>
                        </td>
                    @else
                        <td>-</td>
                    @endif
                @endif
                @if ($proctologyType)
                    <td class="label">Proctoscopy</td>
                    @php
                        $proctoscopy = $protologyOrNonProctology['proctoscopy'] ?? [];

                        if (is_string($proctoscopy)) {
                            $proctoscopy = json_decode($proctoscopy, true) ?? [];
                        }
                    @endphp
                    @if (count($proctoscopy) > 0)
                        <td>
                            <ol>
                                @foreach ($proctoscopy as $item)
                                    <li>{{ $item['label'] ?? $item }}</li>
                                @endforeach

                            </ol>
                        </td>
                    @else
                        <td>-</td>
                    @endif
                @endif
            </tr>
            <tr>
                <td class="label">Co-Morbidities</td>
                @php
                    $co_morbidities = $protologyOrNonProctology['co_morbidities'] ?? [];

                    if (is_string($co_morbidities)) {
                        $co_morbidities = json_decode($co_morbidities, true) ?? [];
                    }
                @endphp
                @if (count($co_morbidities) > 0)
                    <td>
                        <ol>
                            @foreach ($co_morbidities as $item)
                                <li>{{ $item['label'] ?? $item }}</li>
                            @endforeach
                        </ol>
                    </td>
                @else
                    <td>-</td>
                @endif

                @if ($proctologyType)
                    <td class="label">Dre Induration</td>
                    @php
                        $dre_induration_at = $protologyOrNonProctology['dre_induration_at'] ?? [];

                        if (is_string($dre_induration_at)) {
                            $dre_induration_at = json_decode($dre_induration_at, true) ?? [];
                        }
                    @endphp
                    @if (count($dre_induration_at) > 0)
                        <td>
                            <ol>
                                @foreach ($dre_induration_at as $item)
                                    <li>{{ $item['label'] ?? $item }}</li>
                                @endforeach

                            </ol>
                        </td>
                    @else
                        <td>-</td>
                    @endif
                @endif
            </tr>
            <tr>
                <td class="label">Treatment Plan</td>
                <td colspan="3">{!! nl2br($protologyOrNonProctology['treatment_plan'] ?? '-') !!}</td>
            </tr>
            <tr>
                <td class="label">Advice</td>
                @if (isset($protologyOrNonProctology['advice_field']) && $protologyOrNonProctology['advice_field'])
                    <td colspan="3">{!! $advice !!}</td>
                @endif
                <td colspan="3">{!! $protologyOrNonProctology['advice_field'] ?? ($advice ?? '-') !!}</td>
            </tr>
            <tr>
                @if ($nonProctologyType)
                    <td class="label">Prakriti</td>
                    <td>{!! nl2br($protologyOrNonProctology['prakriti'] ?? '-') !!}</td>
                @endif
                @if ($nonProctologyType)
                    <td class="label">Vikruti</td>
                    <td>{!! nl2br($protologyOrNonProctology['vikruti'] ?? '-') !!}</td>
                @endif
            </tr>
            <tr>
                @if ($nonProctologyType)
                    <td class="label">Agni</td>
                    <td>{!! nl2br($protologyOrNonProctology['agni'] ?? '-') !!}</td>
                @endif
                @if ($nonProctologyType)
                    <td class="label">Koshta</td>
                    <td>{!! nl2br($protologyOrNonProctology['koshta'] ?? '-') !!}</td>
                @endif
            </tr>
            <tr>
                @if ($nonProctologyType)
                    <td class="label">Avastha</td>
                    <td colspan="3">{!! nl2br($protologyOrNonProctology['avastha'] ?? '-') !!}</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    <td class="label">Proctoscopy Anal Polyp</td>
                    <td colspan="3">{!! nl2br($protologyOrNonProctology['proctoscopy_anal_polyp_at'] ?? '-') !!}</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    <td class="label">Dre Secondary Position</td>
                    <td>{!! nl2br($protologyOrNonProctology['dre_secondary_position'] ?? '-') !!}</td>
                @endif
                @if ($proctologyType)
                    <td class="label">Proctoscopy Secondary Position</td>
                    <td>{!! nl2br($protologyOrNonProctology['proctoscopy_secondary_position'] ?? '-') !!}</td>
                @endif
            </tr>

            {{-- extra field --}}
            <tr>
                @if ($proctologyType)
                    <td class="label">Previous Scar</td>
                    <td>{{ $protologyOrNonProctology['previous_scar'] ?? '-' }}</td>
                @endif
                @if ($proctologyType)
                    <td class="label">Previous Scar Position</td>
                    <td>{{ $protologyOrNonProctology['previous_scar_position'] ?? '-' }}</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    <td class="label">Abscess</td>
                    <td>{{ $protologyOrNonProctology['abscess'] ?? '-' }}</td>
                @endif
                @if ($proctologyType)
                    <td class="label">Abscess Position</td>
                    <td>{{ $protologyOrNonProctology['abscess_position'] ?? '-' }}</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    <td class="label">Internal Opening Position</td>
                    <td>{{ $protologyOrNonProctology['internal_opening_position'] ?? '-' }}</td>
                @endif
                @if ($proctologyType)
                    <td class="label">Anal Valve</td>
                    <td>{{ $protologyOrNonProctology['anal_valve'] ?? '-' }}</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    <td class="label">Secondary Opening Position</td>
                    <td>{{ $protologyOrNonProctology['secondary_opening_position'] ?? '-' }}</td>
                @endif
                @if ($proctologyType)
                    <td class="label">Secondary Anal Valve</td>
                    <td>{{ $protologyOrNonProctology['secondary_anal_valve'] ?? '-' }}</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    <td class="label">Fistula Type Position</td>
                    <td>{{ $protologyOrNonProctology['type_of_fistula_position'] ?? '-' }}</td>
                @endif
                @if ($proctologyType)
                    <td class="label">Fistula Sphincter</td>
                    <td>{{ $protologyOrNonProctology['type_of_fistula_sphincter'] ?? '-' }}</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    <td class="label">No of Tracks in One Fistula</td>
                    <td>{{ $protologyOrNonProctology['no_of_tracks_in_one_fistula'] ?? '-' }}</td>
                @endif
                @if ($proctologyType)
                    <td class="label">No of Fistula</td>
                    <td>{{ $protologyOrNonProctology['no_of_fistula'] ?? '-' }}</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    <td class="label">Posterior Fistulous Angle</td>
                    <td>{{ $protologyOrNonProctology['posterior_fistulous_angle'] ?? '-' }}</td>
                @endif
                @if ($proctologyType)
                    <td class="label">Sonologist</td>
                    <td>{{ $protologyOrNonProctology['sonologist'] ?? '-' }}</td>
                @endif
            </tr>
            <tr>
                @if ($proctologyType)
                    @php
                        $managements = $protologyOrNonProctology['managements'] ?? [];

                        if (is_string($managements)) {
                            $managements = json_decode($managements, true) ?? [];
                        }
                    @endphp
                    <td class="label">Managements</td>
                    @if (count($managements) > 0)
                        <td>
                            <ol>

                                @foreach ($managements as $management)
                                    <li>{{ $management['label'] ?? $management }}</li>
                                @endforeach

                            </ol>
                        </td>
                    @else
                        <td>-</td>
                    @endif
                @endif
                @if ($proctologyType)
                    <td class="label">Managements Date</td>
                    <td>{{ $protologyOrNonProctology['managements_date'] ?? '-' }}</td>
                @endif
            </tr>
            <tr>
                <td class="label">Co-Morbidities Description</td>
                <td colspan="3">{{ $protologyOrNonProctology['co_morbidities_description'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- 5. Vitals --}}
    <div class="section">
        <div class="section-title">Vitals</div>
        <table class="info-table">
            <tr>
                <td class="label">Temperature</td>
                <td>{{ $protologyOrNonProctology['vitals']['temperature'] ?? '-' }}</td>
                <td class="label">BP</td>
                <td>{{ (isset($protologyOrNonProctology['vitals']) && isset($protologyOrNonProctology['vitals']['bp'])) ? $protologyOrNonProctology['vitals']['bp'].' mmHg' : '-' }}</td>
                <td class="label">Pulse</td>
                <td>{{ (isset($protologyOrNonProctology['vitals']) && isset($protologyOrNonProctology['vitals']['pulse']))  ? $protologyOrNonProctology['vitals']['pulse'].' bpm' : '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- 6. Tests --}}
    <div class="section">
        <div class="section-title">Investigations</div>
        <table class="info-table">
            <tr>
                <td class="label">Tests Suggested</td>
                @php
                    $testList = $protologyOrNonProctology['tests'] ?? [];

                    if (is_string($testList)) {
                        $testList = json_decode($testList, true) ?? [];
                    }
                @endphp
                @if (count($testList) > 0)


                    <td>
                        <ol>

                            @foreach ($testList as $test)
                                <li>{{ $test['label'] ?? $test }}</li>
                            @endforeach

                        </ol>
                    </td>
                @else
                    <td>-</td>
                @endif
                {{-- <td class="label">In Same Hospital?</td> --}}
                {{-- <td>{{ $test_in_same_hospital ? 'Yes' : 'No' }}</td> --}}
            </tr>
        </table>
    </div>

    {{-- 7. Medicines --}}
    @php
        $medicineStr = $protologyOrNonProctology['medicines'] ?? '';
        $rawArray = is_array($medicineStr) ? $medicineStr : explode(',', $medicineStr);

        // Filter out truly valid entries that have a medicine name
        $medicineArray = array_filter(
            array_map(function ($item) {
                $parts = explode('#', trim($item));
                return !empty($parts[0]) ? $parts : null;
            }, $rawArray),
        );
    @endphp
    @if (!empty($medicineArray))
        <div class="section">
            <div class="section-title">Prescribed Medicines</div>
            <table class="info-table">
                <thead>
                    <tr>
                        <th style="width:30%;">Medicine</th>
                        <th>Dosage</th>
                        <th>Timing</th>
                        <th>With</th>
                        <th>Days</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medicineArray as $parts)
                        <tr>
                            <td style="width:30%;">{{ ucwords(strtolower($parts[0] ?? '')) }}</td>
                            <td>{{ ucwords($parts[2] ?? '') }}</td>
                            <td>{{ ucwords(strtolower(str_replace(['-', '_'], ' ', $parts[3] ?? '')))}}</td>
                            <td>{{ ucwords(strtolower(str_replace(['-', '_'], ' ', $parts[4] ?? '')))}}</td>
                            <td>{{ $parts[5] ?? '-' }}</td>
                        </tr>
                    @endforeach

                    @if(!is_null($protologyOrNonProctology['combination_medicines']))
                        @php
                            $comboMedicine=json_decode($protologyOrNonProctology['combination_medicines'],true);
                        @endphp
                        @foreach($comboMedicine as $combo)
                        @php $medicine = collect($combo['combination_ingredients'])->map(function ($item) {
                                    return ucwords(strtolower($item['combination_medicine'])) .
                                        " (" . $item['combination_quantity'] . " " . $item['combination_unit'] . ")";
                                })->implode(' + ');
                        @endphp
                        <tr>
                            <td style="width:30%;">{{$medicine}}</td>
                            <td>{{$combo['combination_dosage'] ?? ''}}</td>
                            <td>{{ ucwords(strtolower(str_replace(['-', '_'], ' ', $combo['combination_timing'] ?? '')))}}</td>
                            <td>{{ ucwords(strtolower(str_replace(['-', '_'], ' ', $combo['combination_take_with'] ?? '')))}}</td>
                            <td>{{$combo['combination_medicine_days']}}</td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    @endif

    {{-- 8. Diet Plan --}}
    <div class="section">
        <div class="section-title">Lifestyle Recommendations</div>
        <table class="info-table">
            <tr>
                <td class="label">Diet Plan</td>
                @php
                    $dietPlan = $protologyOrNonProctology['diet_plan'] ?? [];

                    if (is_string($dietPlan)) {
                        $dietPlan = json_decode($dietPlan, true) ?? [];
                    }
                @endphp
                @if (count($dietPlan) > 0)
                    <td>
                        <ol>
                            @foreach ($dietPlan as $diet)
                                <li style="font-family: 'Noto Sans Kannada', 'Arial', sans-serif;">{{ $diet['label'] ?? $diet }}</li>
                            @endforeach
                        </ol>
                    </td>
                @else
                    <td>-</td>
                @endif
            </tr>
            @if ($nonProctologyType)
                <tr>
                    <td class="label">Food Advice</td>
                    @php
                        $foodAdvice = $protologyOrNonProctology['food_advice'] ?? [];

                        if (is_string($foodAdvice)) {
                            $foodAdvice = json_decode($foodAdvice, true) ?? [];
                        }
                    @endphp
                    @if (count($foodAdvice) > 0)
                        <td>
                            <ol>
                                @foreach ($foodAdvice as $diet)
                                    <li>{{ $diet['label'] ?? $diet }}</li>
                                @endforeach
                            </ol>
                        </td>
                    @else
                        <td>-</td>
                    @endif
                </tr>
            @endif
            @if ($nonProctologyType)
                <tr>
                    <td class="label">Yoga Asana</td>
                    @php
                        $yogaAsana = $protologyOrNonProctology['yoga_asana'] ?? [];

                        if (is_string($yogaAsana)) {
                            $yogaAsana = json_decode($yogaAsana, true) ?? [];
                        }
                    @endphp
                    @if (count($yogaAsana) > 0)
                        <td>
                            <ol>
                                @foreach ($yogaAsana as $diet)
                                    <li>{{ $diet['label'] ?? $diet }}</li>
                                @endforeach
                            </ol>
                        </td>
                    @else
                        <td>-</td>
                    @endif
                </tr>
            @endif
        </table>
    </div>


    {{-- 9. Admission Advice --}}
    <div class="section">
        <div class="section-title">Admission Advice</div>
        <table class="info-table">
            <tr>
                <td class="label">Admission Advice</td>
                <td @if (!$advice_admition) colspan="2" @endif>{{ $advice_admition ? 'Yes' : 'No' }}</td>
                @if ($advice_admition)
                    <td class="label">Admission Advice Date</td>
                    <td>{{ \Carbon\Carbon::parse($advice_admition_date)->format('d/m/Y') }}</p>
                    </td>
                @endif
            </tr>
        </table>
    </div>

    {{-- 10. Follow-Up --}}
    <div class="section footer-note">
        <p><strong style="font-size: 14px">Next Visit:</strong>
            {{ \Carbon\Carbon::parse($next_visit_date)->format('d/m/Y') }}</p>
    </div>

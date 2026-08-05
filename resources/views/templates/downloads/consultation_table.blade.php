@php
    $departmentType = trim((string)($department_type ?? ''));
    $proctologyType = strcasecmp($departmentType, 'Proctology') === 0;
    $nonProctologyType = strcasecmp($departmentType, 'Non Proctology') === 0;
    $allopathyType = strcasecmp($departmentType, 'Allopathy') === 0;
    $clinical = $protologyOrNonProctology ?? [];

    $decode = function ($value) use (&$decode) {
        if ($value === null || $value === '') {
        return [];
    }

    if (is_array($value)) {
        return $value;
    }

if (is_object($value)) {
return (array) $value;
}
if (is_string($value)) {
$trimmed = trim($value);
if ($trimmed === '') {
return [];
}
$json = json_decode($trimmed, true);
if (json_last_error() === JSON_ERROR_NONE) {
return is_array($json) ? $json : [$json];
}
return array_values(array_filter(array_map('trim', explode(',', $trimmed)), fn ($item) => $item !== ''));
}
return [$value];
};
$field = function ($source, $key, $default = null) {
if (is_array($source) && array_key_exists($key, $source)) {
return $source[$key];
}
if (is_object($source) && isset($source->{$key})) {
return $source->{$key};
}
return $default;
};
$text = function ($value, $default = '-') {
if ($value === null || $value === '') {
return $default;
}
if (is_array($value) || is_object($value)) {
return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: $default;
}
return (string) $value;
};
$listItems = function ($value) use ($decode) {
return array_values(array_filter(array_map(function ($item) {
if (is_array($item)) {
return $item['label'] ?? $item['name'] ?? $item['value'] ?? $item['title'] ?? null;
}
if (is_object($item)) {
return $item->label ?? $item->name ?? $item->value ?? $item->title ?? null;
}
return $item;
}, $decode($value)), fn ($item) => $item !== null && $item !== ''));
};
$parseMedicines = function ($value) use ($decode, $field) {
$rows = [];
foreach ($decode($value) as $item) {
if ($item === null || $item === '') {
continue;
}
if (is_array($item) || is_object($item)) {
$source = is_object($item) ? (array) $item : $item;
$name = $field($source, 'medicine_name', '');
if ($name === null || $name === '') {
continue;
}
$rows[] = [
'name' => $name,
'dosage' => $field($source, 'dosage', ''),
'frequency' => $field($source, 'frequency', ''),
'duration' => $field($source, 'duration', ''),
'instruction' => $field($source, 'instruction', ''),
];
continue;
}
foreach (explode(',', (string) $item) as $medicine) {
$parts = array_map('trim', explode('#', $medicine));
if (($parts[0] ?? '') === '') {
continue;
}
$rows[] = [
'name' => $parts[0] ?? '',
'dosage' => $parts[1] ?? '',
'frequency' => $parts[2] ?? '',
'duration' => $parts[3] ?? '',
'instruction' => $parts[4] ?? '',
];
}
}
return $rows;
};
$parseCombinationMedicines = function ($value) use ($decode, $field, $text) {
$rows = [];
foreach ($decode($value) as $item) {
if (!is_array($item) && !is_object($item)) {
continue;
}
$source = is_object($item) ? (array) $item : $item;
$ingredients = [];
foreach ($decode($field($source, 'combination_ingredients', [])) as $ingredient) {
if (!is_array($ingredient) && !is_object($ingredient)) {
continue;
}
$ingredientSource = is_object($ingredient) ? (array) $ingredient : $ingredient;
$medicine = $field($ingredientSource, 'combination_medicine');
if ($medicine === null || $medicine === '') {
continue;
}
$quantity = $field($ingredientSource, 'combination_quantity');
$unit = $field($ingredientSource, 'combination_unit');
$ingredients[] = trim($text($medicine, '') . ' ' . $text($quantity, '') . ' ' . $text($unit, ''));
}
if (empty($ingredients)) {
continue;
}
$rows[] = [
'name' => implode(', ', $ingredients),
'dosage' => $field($source, 'combination_dosage'),
'frequency' => $field($source, 'combination_timing'),
'duration' => $field($source, 'combination_medicine_days'),
'instruction' => $field($source, 'combination_take_with'),
];
}
return $rows;
};
$medicineRows = $parseMedicines($field($clinical, 'medicines', []));
$combinationMedicineRows = $parseCombinationMedicines($field($clinical, 'combination_medicines', []));
$diagnosisValue = $field($clinical, 'diagnosis_summary');
$printText = fn ($value, $default = '-') => nl2br(e(strip_tags($text($value, $default))));
$printList = function ($value) use ($listItems) {
$items = $listItems($value);
if (count($items) === 0) {
echo '-';
return;
}
echo '<ol>';
    foreach ($items as $item) {
    echo '<li>' . e($item) . '</li>';
    }
    echo '</ol>';
};
$splitValues = function ($value) use ($decode) {
if ($value === null || $value === '') {
return [];
}
if (is_array($value) || is_object($value)) {
return array_values(array_filter(array_map(function ($item) {
if (is_array($item)) {
return $item['label'] ?? $item['name'] ?? $item['value'] ?? reset($item);
}
if (is_object($item)) {
return $item->label ?? $item->name ?? $item->value ?? null;
}
return $item;
}, $decode($value)), fn ($item) => $item !== null && $item !== ''));
}
$delimiter = strpos((string) $value, '#') !== false ? '#' : ',';
return array_values(array_filter(array_map('trim', explode($delimiter, (string) $value)), fn ($item) => $item !== ''));
};
$clockText = function ($value) use ($text) {
$value = trim((string) $text($value, ''));
if ($value === '') {
return '-';
}
if (stripos($value, "o'clock") !== false) {
return $value;
}
return $value . " o'clock";
};
$valueAt = function ($items, $index, $default = '-') {
return $items[$index] ?? $default;
};
@endphp
<style>
.section {
    margin-top: 20px;
    padding: 0 15px;
}

.info-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
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
    width: 16%;
}

.section-title {
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 8px;
}

.detail-card {
    border: 1px solid #d9e1ec;
    margin-bottom: 14px;
    padding: 12px;
}

.detail-card-title {
    font-weight: bold;
    font-size: 13px;
    margin-bottom: 8px;
}

li,
p {
    font-size: 12px;
}

ul,
ol {
    margin: 0;
    padding-left: 18px;
}

.attachment-link {
    color: #0d6efd;
}

.footer-note {
    font-size: 12px;
}
</style>
<div style="padding: 15px 0;
    margin: 15px;">
    <h2 style="text-align:center; font-size:18px;  ">CONSULTATION REPORT</h2>

    @if (!empty($patient_document ?? ''))
        <div class="section">
            <div class="section-title">Patient Documents</div>
            <table class="info-table">
                @foreach (explode(",",$patient_document) as $attachment)
                <tr>
                    <td class="label">Attachment</td>
                    <td colspan="5">
                        @php
                        $url = is_array($attachment) ? ($attachment['url'] ?? $attachment['path'] ?? null) : $attachment;
                        $name = is_array($attachment) ? ($attachment['name'] ?? basename((string) $url)) : basename((string) $attachment);
                        @endphp
                        @if ($url)
                        <a class="attachment-link" target="_blank" href="{{ env('APP_URL').'/images/'.$url }}">{{ $text($name) }}</a>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    @endif


    <div class="section">
        <div class="section-title">Patient Information</div>
        <table class="info-table">
            <tr>
                <td class="label">Name</td>
                <td>{{ $text($patient_name ?? null) }}</td>
                <td class="label">Patient No</td>
                <td>{{ $text($patient_number ?? null) }}</td>
                <td class="label">Age / Gender</td>
                <td>{{ $text($age ?? null) }} / {{ $text($gender ?? null) }}</td>
            </tr>
            <tr>
                <td class="label">Phone</td>
                <td>{{ $text($patient_phone ?? null) }}</td>
                <td class="label">Email</td>
                <td colspan="3">{{ $text($patient_email ?? null) }}</td>
            </tr>
        </table>
    </div>
    <div class="section">
        <div class="section-title">Consultation Information</div>
        <table class="info-table">
            <tr>
                <td class="label">Appointment No</td>
                <td>{{ $text($appointment_number ?? null) }}</td>
                <td class="label">Type</td>
                <td>{{ $text($appointment_type ?? null) }}</td>
                <td class="label">Consultation Type</td>
                <td>{{ $text($consultation_type ?? null) }}</td>
            </tr>
            <tr>
                <td class="label">Department</td>
                <td>{{ $text($departmentType) }}</td>
                <td class="label">Status</td>
                <td>{{ $text($status ?? null) }}</td>
                <td class="label">Front Desk</td>
                <td>{{ $text($front_desk_user_name ?? null) }}</td>
            </tr>
        </table>
    </div>
    <div class="section">
        <div class="section-title">Doctor Details</div>
        <table class="info-table">
            <tr>
                <td class="label">Doctor Name</td>
                <td>{{ $text($doctor_name ?? null) }}</td>
                <td class="label">Qualification</td>
                <td>{{ $text($qualification ?? null) }}</td>
                <td class="label">Contact</td>
                <td>{{ $text($doctor_email ?? null) }} / {{ $text($doctor_phone ?? null) }}</td>
            </tr>
        </table>
    </div>
    <div class="section">
        <div class="section-title">Clinical Summary</div>
        <table class="info-table">
            <tr>
                <td class="label">Chief Complaints</td>
                <td>
                    <?php $printList($field($clinical, 'chief_complaints')); ?>
                </td>
                <td class="label">Preliminary Diagnostic</td>
                <td colspan="3">{!! $printText($field($clinical, 'preliminary_diagnostic')) !!}</td>
            </tr>
            <tr>
                <td class="label">Local Examination</td>
                <td>
                    <?php $printList($field($clinical, 'on_examination')); ?>
                </td>
                <td class="label">Surgical History</td>
                <td colspan="3">
                    <?php $printList($field($clinical, 'surgical_history')); ?>
                </td>
            </tr>
            <tr>
                <td class="label">Co-Morbidities</td>
                <td>
                    <?php $printList($field($clinical, 'co_morbidities')); ?>
                </td>
                <td class="label">Co-Morbidities Description</td>
                <td colspan="3">{!! $printText($field($clinical, 'co_morbidities_description')) !!}</td>
            </tr>
            <tr>
                <td class="label">Treatment Plan</td>
                <td colspan="5">{!! $printText($field($clinical, 'treatment_plan')) !!}</td>
            </tr>
            <tr>
                <td class="label">Advice</td>
                <td colspan="5">{!! $printText($field($clinical, 'advice_field', $advice ?? null)) !!}</td>
            </tr>
        </table>
    </div>
    @if ($proctologyType)
    <div class="section">
        <div class="section-title">Proctology Examination</div>
        <table class="info-table">
            <tr>
                <td class="label">DRE</td>
                <td>
                    <?php $printList($field($clinical, 'dre')); ?>
                </td>
                <td class="label">DRE Induration At</td>
                <td colspan="3">
                    <?php $printList($field($clinical, 'dre_induration_at')); ?>
                </td>
            </tr>
            <tr>
                <td class="label">Proctoscopy</td>
                <td>
                    <?php $printList($field($clinical, 'proctoscopy')); ?>
                </td>
                <td class="label">Anal Polyp At</td>
                <td colspan="3">{{ $clockText($field($clinical, 'proctoscopy_anal_polyp_at')) }}</td>
            </tr>
            <tr>
                <td class="label">Previous Scar</td>
                <td>{!! $printText($field($clinical, 'previous_scar')) !!}</td>
                <td class="label">Previous Scar Position</td>
                <td colspan="3">{{ $clockText($field($clinical, 'previous_scar_position')) }}</td>
            </tr>
            <tr>
                <td class="label">Abscess</td>
                <td>{!! $printText($field($clinical, 'abscess')) !!}</td>
                <td class="label">Abscess Position</td>
                <td colspan="3">{{ $clockText($field($clinical, 'abscess_position')) }}</td>
            </tr>
            <tr>
                <td class="label">Diagnosis</td>
                <td colspan="5">{!! $printText($field($clinical, 'diagnosis_summary')) !!}</td>

            </tr>
        </table>
    </div>
    @php
    $tracks = $splitValues($field($clinical, 'no_of_tracks_in_one_fistula'));
    $externalOpeningCounts = $splitValues($field($clinical, 'no_of_external_opening_position'));
    $externalOpeningPositions = $splitValues($field($clinical, 'external_opening_position'));
    $secondaryOpeningCounts = $splitValues($field($clinical, 'no_of_secondary_opening_position'));
    $secondaryOpeningPositions = $splitValues($field($clinical, 'secondary_anal_valve'));
    $internalOpeningDistances = $splitValues($field($clinical, 'internal_opening_distance'));
    $internalOpeningRaw = $splitValues($field($clinical, 'internal_opening_position'));
    $internalOpeningLevels = $splitValues($field($clinical, 'internal_opening_position_level'));
    $anyOther = $splitValues($field($clinical, 'any_other'));
    $openingRows = max(
    1,
    count($tracks),
    count($externalOpeningCounts),
    count($externalOpeningPositions),
    count($secondaryOpeningCounts),
    count($secondaryOpeningPositions),
    count($internalOpeningDistances),
    max(count($internalOpeningLevels), (int) ceil(count($internalOpeningRaw) / 2)),
    count($anyOther)
    );
    $fistulaCrypts = $splitValues($field($clinical, 'type_of_crypt'));
    $secondaryCauses = $splitValues($field($clinical, 'crypt_cause'));
    $fistulaSphincters = $splitValues($field($clinical, 'type_of_fistula_sphincter'));
    $fistulaPositions = $splitValues($field($clinical, 'type_of_fistula_position'));
    $fistulaRiding = $splitValues($field($clinical, 'basis_of_high_low_riding'));
    $distantVisceral = $splitValues($field($clinical, 'distant_visceral_communication'));
    $classificationRows = max(1, count($fistulaCrypts), count($secondaryCauses), count($fistulaSphincters), count($fistulaPositions), count($fistulaRiding), count($distantVisceral));
    @endphp
    <div class="section">
        <div class="section-title">Fistula Details</div>
        <div class="detail-card">
            <div class="detail-card-title">Fistula Openings</div>
            <table class="info-table">
                <thead>
                    <tr>
                        <th>No. of Tracks in Fistula</th>
                        <th>No. of External Opening</th>
                        <th>Positions of External Openings</th>
                        <th>No. of Secondary Opening</th>
                        <th>Positions of Secondary Openings</th>
                        <th>Internal Opening Distance</th>
                        <th>Internal Opening Position</th>
                        <th>Internal Opening Level</th>
                        <th>Any Other</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($index = 0; $index < $openingRows; $index++) @php $internalPosition=$valueAt($internalOpeningRaw, $index * 2, null); $internalLevel=$valueAt($internalOpeningLevels, $index, $valueAt($internalOpeningRaw, ($index * 2) + 1, '-' )); @endphp <tr>
                        <td>{{ $text($valueAt($tracks, $index, null)) }}</td>
                        <td>{{ $text($valueAt($externalOpeningCounts, $index, null)) }}</td>
                        <td>{{ $clockText($valueAt($externalOpeningPositions, $index, null)) }}</td>
                        <td>{{ $text($valueAt($secondaryOpeningCounts, $index, null)) }}</td>
                        <td>{{ $clockText($valueAt($secondaryOpeningPositions, $index, null)) }}</td>
                        <td>{{ $text($valueAt($internalOpeningDistances, $index, null)) }}</td>
                        <td>{{ $clockText($internalPosition) }}</td>
                        <td>{!! $printText($internalLevel) !!}</td>
                        <td>{!! $printText($valueAt($anyOther, $index, null)) !!}</td>
                        </tr>
                        @endfor
                </tbody>
            </table>
            <div>
                <div class="detail-card-title">Fistula Classification</div>
                <table class="info-table">
                    <thead>
                        <tr>
                            <th>Fistula Crypt</th>
                            <th>Secondary Cause</th>
                            <th>Fistula Sphincter</th>
                            <th>Fistula Position</th>
                            <th>Fistula High / Low Riding</th>
                            <th>Distant / Visceral</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($index = 0; $index < $classificationRows; $index++) <tr>
                            <td>{!! $printText($valueAt($fistulaCrypts, $index, null)) !!}</td>
                            <td>{!! $printText($valueAt($secondaryCauses, $index, null)) !!}</td>
                            <td>{!! $printText($valueAt($fistulaSphincters, $index, null)) !!}</td>
                            <td>{!! $printText($valueAt($fistulaPositions, $index, null)) !!}</td>
                            <td>{!! $printText($valueAt($fistulaRiding, $index, null)) !!}</td>
                            <td>{!! $printText($valueAt($distantVisceral, $index, null)) !!}</td>
                            </tr>
                            @endfor
                    </tbody>
                </table>
            </div>
            <div>
                <div class="detail-card-title">Investigations</div>
                <table class="info-table">
                    <tr>
                        <td class="label">Sonofistulogram</td>
                        <td>{!! $printText($field($clinical, 'anal_valve')) !!}</td>
                        <td class="label">MRI Fistulogram</td>
                        <td colspan="3">{!! $printText($field($clinical, 'mri_fistula_gram')) !!}</td>
                    </tr>
                    <tr>
                        <td class="label">Posterior Fistulous Angle</td>
                        <td>{!! $printText($field($clinical, 'posterior_fistulous_angle')) !!}</td>
                        <td class="label">Sonologist/Radiologist</td>
                        <td colspan="3">{!! $printText($field($clinical, 'sonologist')) !!}</td>
                    </tr>
                    <tr>
                        <td class="label">Sonologist/Radiologist Findings</td>
                        <td>{!! $printText($field($clinical, 'sonologist_findings')) !!}</td>
                        <td class="label">Any Other Investigations</td>
                        <td colspan="3">{!! $printText($field($clinical, 'other_investigation')) !!}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @endif
    @if ($nonProctologyType)
    <div class="section">
        <div class="section-title">Ayurvedic Assessment</div>
        <table class="info-table">
            <tr>
                <td class="label">Prakriti</td>
                <td>{!! $printText($field($clinical, 'prakriti')) !!}</td>
                <td class="label">Vikruti</td>
                <td colspan="3">{!! $printText($field($clinical, 'vikruti')) !!}</td>
            </tr>
            <tr>
                <td class="label">Agni</td>
                <td>{!! $printText($field($clinical, 'agni')) !!}</td>
                <td class="label">Koshta</td>
                <td colspan="3">{!! $printText($field($clinical, 'koshta')) !!}</td>
            </tr>
            <tr>
                <td class="label">Avastha</td>
                <td colspan="5">{!! $printText($field($clinical, 'avastha')) !!}</td>
            </tr>
        </table>
    </div>
    @endif
    @if ($allopathyType)
    <div class="section">
        <div class="section-title">Allopathy Details</div>
        <table class="info-table">
            <tr>
                <td class="label">Finding Fields</td>
                <td colspan="5">{!! $printText($field($clinical, 'finding_fields')) !!}</td>
            </tr>
            <tr>
                <td class="label">Examination Overview</td>
                <td colspan="5">{!! $printText($field($clinical, 'examination_overview')) !!}</td>
            </tr>
            <tr>
                <td class="label">Previous Scar</td>
                <td>{!! $printText($field($clinical, 'previous_scar')) !!}</td>
                <td class="label">Previous Scar Position</td>
                <td colspan="3">{{ $clockText($field($clinical, 'previous_scar_position')) }}</td>
            </tr>
            <tr>
                <td class="label">Abscess</td>
                <td>{!! $printText($field($clinical, 'abscess')) !!}</td>
                <td class="label">Abscess Position</td>
                <td colspan="3">{{ $clockText($field($clinical, 'abscess_position')) }}</td>
            </tr>
            <tr>
                <td class="label">Diagnosis Summary</td>
                <td colspan="5">{!! $printText($diagnosisValue) !!}</td>
            </tr>
        </table>
    </div>
    @endif
    <div class="section">
        <div class="section-title">Test,Medicines & lifestyle</div>
        <table class="info-table">
            <tr>
                <td class="label">Tests Suggested</td>
                <td colspan="5">
                    <?php $printList($field($clinical, 'tests')); ?>
                </td>
            </tr>
            <tr>
                <td class="label">Diet</td>
                <td colspan="5">
                    <?php $printList($field($clinical, 'diet_plan')); ?>
                </td>
            </tr>
            @if ($nonProctologyType)
            <tr>
                <td class="label">Yoga Asana</td>
                <td colspan="5">
                    <?php $printList($field($clinical, 'yoga_asana')); ?>
                </td>
            </tr>
            <tr>
                <td class="label">Food Advice</td>
                <td colspan="5">
                    <?php $printList($field($clinical, 'food_advice')); ?>
                </td>
            </tr>
            @endif
        </table>
    </div>
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
    {{-- <div class="section">
        <div class="section-title">Cost Details</div>
        <table class="info-table">
            <tr>
                <td class="label">Amount</td>
                <td>{{ $text($field($clinical, 'amount')) }}</td>
                <td class="label">Additional Cost</td>
                <td colspan="3">{!! $printText($field($clinical, 'additional_cost')) !!}</td>
            </tr>
        </table>
    </div> --}}
    {{-- @if (!empty($attachments ?? []))
    <div class="section">
        <div class="section-title">Attachments</div>
        <table class="info-table">
            @foreach ($attachments as $attachment)
            <tr>
                <td class="label">Attachment</td>
                <td colspan="5">
                    @php
                    $url = is_array($attachment) ? ($attachment['url'] ?? $attachment['path'] ?? null) : $attachment;
                    $name = is_array($attachment) ? ($attachment['name'] ?? basename((string) $url)) : basename((string) $attachment);
                    @endphp
                    @if ($url)
                    <a class="attachment-link" href="{{ $url }}">{{ $text($name) }}</a>
                    @else
                    -
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
    <p class="footer-note">
        This report is generated from the consultation record. Please verify medicines, investigations, and advice before sharing with the patient.
    </p> --}}
</div>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Consultation Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            color: #333;
        }

        .section {
            margin-top: 20px;
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
    </style>
</head>

<body>
    @include('templates.downloads.letter_header', [
        'letter_header_address' => $letter_header_address,
    ])
    <div style="max-width: 64rem; margin: 0 auto;">
        <div
            style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #0d6efd;">
            <div style="display: flex; align-items: center;">
                @if (!empty($settings->hospital_logo))
                    <img src="{{ env('APP_URL') . $settings->hospital_logo }}" alt="Hospital Logo"
                        style="height: 2rem; width: 2rem; border-radius: 9999px; margin-right: 0.5rem;" />
                @endif
            </div>
            <div style="text-align: right;">
                <h1 style="font-size: 1.5rem; font-weight: bold; color: #000; margin-bottom: 0.5rem;">
                    {{ env('APP_NAME') }}
                </h1>
            </div>
        </div>
    </div>

    <h2 style="font-size: 18px;">CONSULTATION REPORT</h2>

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
                <td colspan="3">{{ $patient_email }}</td>
            </tr>
        </table>
    </div>

    {{-- 2. Consultation Details --}}
    <div class="section">
        <div class="section-title">Consultation Information</div>
        <table class="info-table">
            <tr>
                <td class="label">Consultation ID</td>
                <td>{{ $id }}</td>
                <td class="label">Appointment No</td>
                <td>{{ $appointment_number }}</td>
                <td class="label">Type</td>
                <td>{{ $appointment_type }} ({{ $type }})</td>
            </tr>
            <tr>
                <td class="label">Consultation Type</td>
                <td>{{ $consultation_type }}</td>
                <td class="label">Status</td>
                <td>{{ $status }}</td>
                <td class="label">Front Desk</td>
                <td>{{ $front_desk_user_name }}</td>
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
                <td>
                    <ul>
                        @php
                            $chiefComplaints = $protologyOrNonProctology['chief_complaints'] ?? [];

                            if (is_string($chiefComplaints)) {
                                $chiefComplaints = json_decode($chiefComplaints, true) ?? [];
                            }
                        @endphp

                        @foreach ($chiefComplaints as $item)
                            <li>{{ $item['label'] ?? $item }}</li>
                        @endforeach
                    </ul>
                </td>
                <td class="label">Preliminary Diagnosis</td>
                <td>
                    <ul>
                        @php
                            $diagnosisList = $preliminary_diagnosis ?? [];

                            if (is_string($diagnosisList)) {
                                $diagnosisList = json_decode($diagnosisList, true) ?? [];
                            }
                        @endphp

                        @foreach ($diagnosisList as $item)
                            <li>{{ $item['label'] ?? $item }}</li>
                        @endforeach

                    </ul>
                </td>
            </tr>
            <tr>
                <td class="label">On Examination</td>
                <td>
                    <ul>
                        @php
                            $onExamination = $protologyOrNonProctology['on_examination'] ?? [];

                            if (is_string($onExamination)) {
                                $onExamination = json_decode($onExamination, true) ?? [];
                            }
                        @endphp

                        @foreach ($onExamination as $item)
                            <li>{{ $item['label'] ?? $item }}</li>
                        @endforeach

                    </ul>
                </td>
                <td class="label">Surgical History</td>
                <td>
                    <ul>
                        @php
                            $surgicalHistory = $protologyOrNonProctology['surgical_history'] ?? [];

                            if (is_string($surgicalHistory)) {
                                $surgicalHistory = json_decode($surgicalHistory, true) ?? [];
                            }
                        @endphp

                        @foreach ($surgicalHistory as $item)
                            <li>{{ $item['label'] ?? $item }}</li>
                        @endforeach

                    </ul>
                </td>
            </tr>
            <tr>
                <td class="label">Treatment Plan</td>
                <td colspan="3">{!! nl2br($protologyOrNonProctology['treatment_plan'] ?? '-') !!}</td>
            </tr>
            <tr>
                <td class="label">Advice</td>
                <td colspan="3">{{ $protologyOrNonProctology['advice_field'] ?? ($advice ?? '-') }}</td>
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
                <td>{{ $protologyOrNonProctology['vitals']['bp'] ?? '-' }}</td>
                <td class="label">Pulse</td>
                <td>{{ $protologyOrNonProctology['vitals']['pulse'] ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- 6. Tests --}}
    <div class="section">
        <div class="section-title">Investigations</div>
        <table class="info-table">
            <tr>
                <td class="label">Tests Suggested</td>
                <td>
                    <ul>
                        @php
                            $testList = $protologyOrNonProctology['tests'] ?? [];

                            if (is_string($testList)) {
                                $testList = json_decode($testList, true) ?? [];
                            }
                        @endphp

                        @foreach ($testList as $test)
                            <li>{{ $test['label'] ?? $test }}</li>
                        @endforeach

                    </ul>
                </td>
                {{-- <td class="label">In Same Hospital?</td> --}}
                {{-- <td>{{ $test_in_same_hospital ? 'Yes' : 'No' }}</td> --}}
            </tr>
        </table>
    </div>

    {{-- 7. Medicines --}}
    <div class="section">
        <div class="section-title">Prescribed Medicines</div>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Timing</th>
                    <th>With</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $medicineStr = $protologyOrNonProctology['medicines'] ?? '';
                    $medicineArray = is_array($medicineStr) ? $medicineStr : explode(',', $medicineStr);
                @endphp
                @foreach ($medicineArray as $med)
                    @php
                        $parts = explode('#', trim($med));
                    @endphp
                    <tr>
                        <td>{{ $parts[0] ?? '' }}</td>
                        <td>{{ $parts[1] ?? '' }}</td>
                        <td>{{ $parts[2] ?? '' }}</td>
                        <td>{{ $parts[3] ?? '' }}</td>
                        <td>{{ $parts[4] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 8. Diet Plan --}}
    <div class="section">
        <div class="section-title">Diet Plan</div>
        <ul>
            @php
                $dietPlan = $protologyOrNonProctology['diet_plan'] ?? [];

                if (is_string($dietPlan)) {
                    $dietPlan = json_decode($dietPlan, true) ?? [];
                }
            @endphp

            @foreach ($dietPlan as $diet)
                <li>{{ $diet['label'] ?? $diet }}</li>
            @endforeach

        </ul>
    </div>

    {{-- 9. Admission Advice --}}
    <div class="section">
        <div class="section-title">Admission Advice</div>
        <p>{{ $advice_admition ? 'Yes' : 'No' }}</p>
    </div>

    {{-- 12. Payment Summary --}}
    <div class="section">
        <div class="section-title">Payment Summary</div>
        <table class="info-table">
            <tr>
                <td class="label">Total Amount</td>
                <td>{{ $total_amount }}</td>
                <td class="label">Collected</td>
                <td>{{ $collected_amount }}</td>
                <td class="label">Balance</td>
                <td>{{ $balanced_amount }}</td>
            </tr>
            <tr>
                <td class="label">Payment Status</td>
                <td colspan="5">{{ $payment_status }}</td>
            </tr>
        </table>

        <div class="section-title" style="margin-top: 10px;">Payment Breakdown</div>
        <table class="info-table">
            <thead>
                <tr>
                    <th>Amount</th>
                    <th>For</th>
                    <th>Transaction ID</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paymentArray as $payment)
                    <tr>
                        <td>{{ $payment['amount'] }}</td>
                        <td>{{ $payment['amount_for'] ?: 'N/A' }}</td>
                        <td>{{ '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 10. Follow-Up --}}
    <div class="section">
        <div class="section-title">Next Visit</div>
        <p>{{ \Carbon\Carbon::parse($next_visit_date)->format('d-m-Y') }}</p>
    </div>

</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Consultation Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 10px;
            color: #333;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .report-container {
            padding: 20px;
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        .report-container table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .report-container th,
        .report-container td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .report-container th {
            background-color: #f4f4f4;
        }
    </style>
</head>

<body>
    <div style="min-height: 100vh; padding: 2rem;">
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
    </div>

    <h2>INVOICE REPORT</h2>

    <div>

        <div class="report-container">
            <div class="section-title">PATIENT INFORMATION</div>
            <table>
                <tr>
                    <th>Name</th>
                    <td>{{ $patient_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $patient_email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $patient_phone }}</td>
                </tr>
                <tr>
                    <th>Patient Number</th>
                    <td>{{ $patient_number }}</td>
                </tr>
            </table>
        </div>

        <div class="report-container">
            <div class="section-title">DOCTOR INFORMATION</div>
            <table>
                <tr>
                    <th>Name</th>
                    <td>{{ $doctor_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $doctor_email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $doctor_phone }}</td>
                </tr>
            </table>
        </div>

        <div class="report-container">
            <div class="section-title">FRONT DESK USER INFORMATION</div>
            <table>
                <tr>
                    <th>Name</th>
                    <td>{{ $front_desk_user_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $front_desk_user_email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $front_desk_user_phone }}</td>
                </tr>
            </table>
        </div>

        <div class="report-container">
            <div class="section-title">REFERRAL INFORMATION</div>
            <table>
                <tr>
                    <th>Referred By</th>
                    <td>{{ $referred_by_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $referred_by_email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $referred_by_phone_no }}</td>
                </tr>
                <tr>
                    <th>Hospital Name</th>
                    <td>{{ $referred_by_hospital_name }}</td>
                </tr>
            </table>
        </div>
        <div class="report-container">
            <table>
                <tr>
                    <th>Next Visit Date</th>
                    <td>{{ \Carbon\Carbon::parse($next_visit_date)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <th>Appointment Number</th>
                    <td>{{ $appointment_number }}</td>
                </tr>
                <tr>
                    <th>Complaint</th>
                    <td>{{ $complaint }}</td>
                </tr>
                <tr>
                    <th>Advice</th>
                    <td>{{ $advice }}</td>
                </tr>
            </table>
        </div>
        <div class="report-container">
            <table>
                <tr>
                    <th>Payment</th>
                    <td>

                        Number : {{ $paymentArray['payment_number'] }}
                        <br />
                        Type : {{ $paymentArray['payment_type'] }}
                        <br />
                        Amount For : {{ $paymentArray['amount_for'] }}
                        <br />
                        Enroll Fees : Rs {{ $enroll_fees }}
                        <br />
                        Fees : Rs {{ $paymentArray['amount'] }}
                        <br />
                        Total Fee : {{ $enroll_fees + $paymentArray['amount'] }}
                        <br />
                        Collected Amount : Rs {{ $collected_amount }}
                        <br />
                        Balanced Amount : Rs {{ $balanced_amount }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #007b83;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo {
            width: 120px;
        }

        .hospital-details {
            text-align: right;
        }

        h2,
        h3 {
            color: #007b83;
            margin-bottom: 5px;
        }

        .section {
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px 10px;
            vertical-align: top;
        }

        .prescription-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .prescription-table th,
        .prescription-table td {
            border: 1px solid #007b83;
            padding: 8px;
            text-align: left;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .signature {
            height: 80px;
            border-bottom: 1px dashed #000;
            margin-top: 40px;
            width: 200px;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            border: 1px dashed #ccc;
        }

        @media print {
            .container {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div><img src="{{ asset('images/logo.png') }}" alt="Hospital Logo" class="logo"></div>
            <div class="hospital-details">
                <h2>City Care Multispeciality Hospital</h2>
                <p>123 Health Ave, Bengaluru, Karnataka</p>
                <p>Phone: +91 98765 43210</p>
                <p>Email: contact@citycarehospital.in</p>
            </div>
        </div>

        <div class="section">
            <h3>Patient Information</h3>
            <table class="info-table">
                <tr>
                    <td><strong>Name:</strong> {{ $patient['name'] }}</td>
                    <td><strong>Age:</strong> {{ $patient['age'] }}</td>
                    <td><strong>Gender:</strong> {{ $patient['gender'] }}</td>
                </tr>
                <tr>
                    <td><strong>Patient ID:</strong> {{ $patient['id'] }}</td>
                    <td><strong>Contact:</strong> {{ $patient['contact'] }}</td>
                    <td><strong>Date of Visit:</strong> {{ $visit_date }}</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Address:</strong> {{ $patient['address'] }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>Doctor Information</h3>
            <table class="info-table">
                <tr>
                    <td><strong>Name:</strong> Dr. {{ $doctor['name'] }}</td>
                    <td><strong>Specialty:</strong> {{ $doctor['specialty'] }}</td>
                    <td><strong>Signature:</strong>
                        <div class="signature"></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>Diagnosis / Complaint</h3>
            <p>{{ $diagnosis }}</p>
        </div>

        <div class="section">
            <h3>Prescription</h3>
            <table class="prescription-table">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prescriptions as $item)
                        <tr>
                            <td>{{ $item['medicine'] }}</td>
                            <td>{{ $item['dosage'] }}</td>
                            <td>{{ $item['frequency'] }}</td>
                            <td>{{ $item['duration'] }}</td>
                            <td>{{ $item['notes'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3>Additional Instructions</h3>
            <p>{{ $notes }}</p>
        </div>

        <div class="footer">
            <div>
                <p>City Care Multispeciality Hospital</p>
                <p>www.citycarehospital.in | +91 98765 43210</p>
            </div>
            {{-- <div>
                <div class="qr-code">
                    <!-- Placeholder for QR Code -->
                </div>
                <small>Scan to verify</small>
            </div> --}}
        </div>
    </div>
</body>

</html>

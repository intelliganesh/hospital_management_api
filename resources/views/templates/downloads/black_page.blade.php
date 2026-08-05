<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acharya Sushrutha Healthcare Pvt Ltd</title>
    <style>
        .page {
            width: 210mm;
            /* height: 297mm; */
            /* padding: 20mm 0; */
            margin: 0 auto;
            background-color: white;
            position: relative;
        }

        .letterhead {
            font-size: 12px;
            font-weight: bold;
            color: #000000;
            text-align: left;
            /* margin-left: 20mm;
            margin-right: 20mm;
            margin-bottom: 10mm; */
        }

        /* .vertical-line {
            position: absolute;
            left: 50mm;
            top: 40mm;
            bottom: 20mm;
            width: 1px;
            background-color: #000000;
        } */

        .page-number {
            position: absolute;
            bottom: 10mm;
            left: 20mm;
            font-size: 10px;
        }
    </style>
</head>

<body>
    @include('templates.downloads.letter_header', [
        'generic_letter_header' => true,
        'letter_header_address' => $patient->letter_header_address,
    ])
    <div class="page">
        {{-- <div class="letterhead">
            ACHARYA SUSHRUTHA HEALTHCARE PVT LTD<br>
            NO 52, 1<sup>ST</sup> CROSS,80 FEET ROAD, ITTLAYOUTTH, NAGARABHAVI 2<sup>ND</sup> STAGE, MALLATHAHALLI-<br>
            BENGALURU 560056
        </div> --}}
        {{-- <div class="vertical-line"></div> --}}
    </div>
</body>

</html>

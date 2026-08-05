<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .letter-header-container {
            width: 100%;
            height: 100px;
            position: relative;
        }

        .letter-header-inner-container {
            height: 75px;
            background: green;
        }

        .letter-header-container img {
            left: 0px;
            top: -11px;
            right: 0px;
            bottom: 0px;
            position: absolute;
        }
    </style>
</head>

<body>
    {{-- @include('templates.downloads.letter_header', [
        'letter_header_address' => $letter_header_address,
    ]) --}}
    <div class="letter-header-container">
        <div class="letter-header-inner-container">
            <img style="height: 100%;" src="{{ asset('images/' . $letter_header_address) }}" alt="">
        </div>
    </div>


    <table style="width: 100%; background-color: lightgray ; padding: 10px;">
        <tr cellspacing="0">
            <td style="height: 60px;">
                <img style="height: 100%;" src="{{ asset('images/' . $letter_header_address) }}" alt="">
            </td>
            <td style="color: white; font-size: 20px;">
                <strong>Acharya Sushrutha</strong><br>
                HEALTHCARE Pvt. Ltd.
            </td>
        </tr>
    </table>
</body>

</html>

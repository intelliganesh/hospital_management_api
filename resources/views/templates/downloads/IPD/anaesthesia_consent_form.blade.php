<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color: #000;
    {{-- line-height: 1.8; --}}
}

.container {
    width: 100%;
    {{-- padding: 0 30px;
    box-sizing: border-box; --}}
    word-break: normal;
    overflow-wrap: break-word;
    line-height:1.8;

}

.title {
    text-align: center;
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 20px;
    text-decoration: underline;
}

.dotted-line {
    border-bottom: 1px dotted #000;
    display: inline-block;
    min-width: 250px;
    font-weight: normal;
}

.full-line {
    border-bottom: 1px dotted #000;
    display: block;
    width: 100%;
    height: 18px;
    margin-top: 5px;
}

.section {
    margin-top: 15px;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

td, th {
    border: 1px solid #000;
    padding: 6px;
    font-weight: normal;
}

.text-center {
    text-align: center;
}
</style>
</head>

<body>
@php
    $preliminaryNotes = $ipd->preliminaryNotes->first();
@endphp
<div class="container">

    <div class="title">CONSENT FOR ANAESTHESIA / SEDATION</div>

    <p>
        <b>DATE:</b> {{ date('d/m/Y') }}
    </p>

    <div>
        <p style="display: inline-block"><b>PATIENT NAME:</b> &nbsp;&nbsp;&nbsp;{{ $ipd->patient_name ?? '' }}

        <p style="float:right;"><b>AGE/GENDER:</b>&nbsp;&nbsp;&nbsp;
            <span  style="min-width:100px;">
                {{ $ipd->patient_age ?? '' }} / {{ $ipd->patient->gender ?? '' }}
            </span>
        </p>
    </div>

    <p>
        <b>IP NO.:</b>&nbsp;&nbsp;{{ $ipd->ipd_number ?? '' }}
    </p>

    <p>
        <b>Diagnosis:</b><span class="dotted-line" style="min-width:630px;">{{$preliminaryNotes?->final_diagnosis ?? ''}}</span>
    </p>

    <p>
        <b>Operative procedure:</b><span class="dotted-line" style="min-width:580px;">&nbsp;&nbsp;&nbsp;{{ $ipd->surgery_report?->surgery_name ?? '' }}</span>
    </p>
    @php $selected = $ipd->anaesthesia?->type_of_anaesthesia ?? ''; @endphp
    <p>
        <b>Type of Anaesthesia:</b>

        @foreach(['Local','General','Spinal','Epidural','Nerve Block'] as $type)
            @if($selected === $type)
                <span style="border:1px solid #000;border-radius:15px;padding:2px 8px;margin-left: 3px; margin-right: 3px;">
                    {{ $type }}
                </span>
            @else
                {{ $type }}
            @endif

            @if(!$loop->last) / @endif
        @endforeach
    </p>

    <div class="section">
        <p>
            I, <span class="dotted-line" style="min-width:250px;">&nbsp;&nbsp;&nbsp;{{ $ipd->patient_name ?? '' }}</span>
            (Name of patient), give my full consent as an act of my own free will to undergo the following
            surgery/ procedures
            <span class="dotted-line" style="min-width:300px;">&nbsp;&nbsp;{{ $ipd->surgery_report?->surgery_name ?? '' }}</span>
            at Acharya Sushrutha Healthcare Pvt Ltd, Bangalore.
            I understand that the above mentioned procedure necessitates the administration of
            Local/Sedation/Regional/General or any combination thereof to provide pain management during and/ or after surgery.
            I hereby authorize
            <span class="dotted-line" style="min-width:200px;"> &nbsp;&nbsp;&nbsp;{{ $ipd->surgery_report?->anaesthetist ?? '' }}{{ $ipd->surgery_report?->external_anaesthetist ? ', ' . $ipd->surgery_report?->external_anaesthetist : '' }}</span>
            (Anaesthetist) and their associates to provide the required anaesthesia service.
        </p>

        <p>
            I understand that the results and effects of anaesthesia depends on the type of anaesthesia administered
            and it can vary from temporary decreased loss of feeling/ numbness, loss of movement to total unconscious state.
            I have been explained that all forms of anaesthesia involve some risks and no guarantees or promises can be made
            concerning the results of the procedure. I understand that there are some infrequent complications that can occur
            due to use of anaesthesia. These include bruising, pain or some injury at the site of injection, temporary nerve damage,
            muscle pains, asthmatic reactions, headaches, the possibility of sensation during operation, damage to teeth and dental prosthesis,
            lip tongue injury, temporary difficult speaking or hoarseness and epileptic seizure. There can also be some very rare serious complications including:
            heart attack, stroke, severe allergic or sensitivity reactions, brain damage, kidney or liver failure,
            lung damage, paraplegia or quadriplegia, permanent nerve or blood vessel damage, eye injury,
            damage to larynx (voice box) and vocal cords, pneumonia and infection from blood transfusion.
            The possibility or more serious complications including death is quite remote, but it does exist.
        </p>

        <p>
            I have been explained in the language known & understood by me about the nature of the surgery/ procedure,
            type of anaesthesia used and its benefits, and costs, risks associated with it, other alternatives and its prognosis.
        </p>

        <p>
            I understand that local anaesthesia with or without sedation may not be successful and therefore an alternative method may be used as deemed necessary.
        </p>

        <p>
            I hereby absolve Acharya Sushrutha Healthcare Pvt. Ltd., Bangalore and its surgical team & hospital staff of any liability for consequences arising because of the above mentioned surgery/ procedures.
        </p>

        <p>
            <b>Consent of Patient representative / Surrogate</b>
        </p>

        <p>
            The patient is unable to consent because
            <span class="dotted-line" style="min-width:350px;"></span>
            and hence I,
            <span class="dotted-line" style="min-width:250px;"></span>
            (Name/ relationship with patient), thereof give my consent on behalf of the patient after discussion with the doctor for the above mentioned Surgery/ Procedure.
        </p>
    </div>

    <!-- Signature Table -->
    <table>
        <tr class="text-center">
            <th></th>
            <th width="20%"><b>Name</b></th>
            <th width="20%"><b>Signature</b></th>
            <th width="20%"><b>Date</b></th>
            <th width="20%"><b>Time</b></th>
        </tr>

        <tr>
            <td><b>Patient / Patient Surrogate</b></td>
            <td>{{ $ipd->patient_name ?? '' }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td><b>Witness</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <tr>
            <td><b>Doctor</b></td>
            <td>{{ $ipd->surgery_report?->anaesthetist ?? '' }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        @if($ipd->surgery_report?->external_anaesthetist)
        <tr>
            <td><b>Doctor</b></td>
            <td>{{ $ipd->surgery_report?->external_anaesthetist ?? '' }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endif

        <tr>
            <td><b>Interpreter</b></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

</div>

</body>
</html>

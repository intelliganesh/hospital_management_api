{{-- <style>
    .text * {
        margin: 0px;
        padding: 5px 0px;
        line-height: 1.5;
    }
</style> --}}
@php
    if (!function_exists('hexToRgba1')) {
        function hexToRgba1($hex, $opacity)
        {
            $hex = str_replace('#', '', $hex);
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            return "rgba($r, $g, $b, $opacity)";
        }
    }

    $bgColor = isset($primary_color) && !empty($primary_color) ? hexToRgba1($primary_color, 0.85) : 'rgba(211, 211, 211, 0.85)'; // lightgray with opacity
@endphp
@if (isset($generic_letter_header) && $generic_letter_header == true)

<table cellspacing="0" cellpadding="0"
        style="width: 100%; background-color: @if (isset($bgColor) && !empty($bgColor)) {{ $bgColor }} @else lightgray @endif; padding: 10px;">
        <tr>
            <td style="height: 40px;  border: none; width: 40%;">
                <img src="{{ asset('logo.png') }}" style="height: 60px;">
            </td>
            @if (isset($letter_header_info))
                <td class="text" style="font-size: 16px; border: none;">
                    {!! html_entity_decode($letter_header_address) !!}
                </td>
            @endif
        </tr>
    </table>
@else
    <table cellspacing="0" cellpadding="0"
        style="width: 100%; background-color: @if (isset($bgColor) && !empty($bgColor)) {{ $bgColor }} @else lightgray @endif; padding: 10px;">
        <tr>
            <td style="height: 40px;  border: none; width: 40%;">
                <img src="{{ public_path('images/' . $letter_header_address) }}" style="height: 60px;">
            </td>
            @if (isset($letter_header_info))
                <td class="text" style="color: white; font-size: 16px; border: none;">
                    {!! html_entity_decode($letter_header_info) !!}
                </td>
            @endif
        </tr>
    </table>
@endif

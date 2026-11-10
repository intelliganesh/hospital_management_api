{{-- Ruled lines for hand-writing on blank forms. Usage: @include('templates.downloads.IPD.write_lines', ['n' => 4]) --}}
<div style="page-break-inside: avoid; margin-bottom:8px;">
@for ($__i = 0; $__i < ($n ?? 4); $__i++)
<div style="height:22px; border-bottom:1px solid #000;"></div>
@endfor
</div>

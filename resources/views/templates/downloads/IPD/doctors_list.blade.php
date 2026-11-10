@php
    $__docs = $ipd->all_doctors ?? [];
    if (empty($__docs) && !empty($ipd->doctor_name)) {
        $__docs = [['name' => $ipd->doctor_name, 'designation' => '']];
    }
    $__prefix = $prefix ?? '';
    $__sep = $sep ?? '<br>';
    $__parts = [];
    foreach ($__docs as $__d) {
        $__parts[] = e($__prefix . $__d['name'] . (!empty($__d['designation']) ? ' (' . $__d['designation'] . ')' : ''));
    }
@endphp
{!! implode($__sep, $__parts) !!}

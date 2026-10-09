{{-- <x-admin.progress :percent="63" label="Drive C:\" hint="120 GB free" />  green < 75%, amber < 90%, red above --}}
@props(['percent' => 0, 'label' => null, 'hint' => null])
@php
    $p = max(0, min(100, (float) $percent));
    $color = $p >= 90 ? 'bg-red-500' : ($p >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
@endphp
<div>
    <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
        <span class="font-medium text-slate-700">{{ $label }}</span>
        <span class="text-slate-500">{{ $p }}%</span>
    </div>
    <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
        <div class="h-full rounded-full {{ $color }}" style="width: {{ $p }}%"></div>
    </div>
    @if ($hint) <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p> @endif
</div>

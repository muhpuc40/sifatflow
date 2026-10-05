{{-- <x-admin.alert type="success|error|warning|info">Message</x-admin.alert> --}}
@props(['type' => 'info'])
@php
    $styles = [
        'success' => ['bg-emerald-50 text-emerald-800 ring-emerald-600/20', 'check-circle'],
        'error' => ['bg-red-50 text-red-800 ring-red-600/20', 'alert-circle'],
        'warning' => ['bg-amber-50 text-amber-800 ring-amber-600/20', 'alert-circle'],
        'info' => ['bg-sky-50 text-sky-800 ring-sky-600/20', 'alert-circle'],
    ];
    [$classes, $icon] = $styles[$type] ?? $styles['info'];
@endphp
<div role="alert" {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-lg p-4 text-sm ring-1 ring-inset $classes"]) }}>
    <x-admin.icon :name="$icon" class="mt-0.5 size-5 shrink-0" />
    <div class="flex-1">{{ $slot }}</div>
</div>

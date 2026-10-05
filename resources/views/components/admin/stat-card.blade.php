{{-- <x-admin.stat-card label="Students" :value="$stats['students']" icon="users" hint="All time" /> --}}
@props(['label', 'value', 'icon' => 'activity', 'hint' => null])
<div {{ $attributes->merge(['class' => 'flex items-center gap-4 rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-900/5']) }}>
    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
        <x-admin.icon :name="$icon" class="size-6" />
    </span>
    <div class="min-w-0">
        <p class="truncate text-sm text-slate-500">{{ $label }}</p>
        <p class="text-2xl font-semibold text-slate-900">{{ is_numeric($value) ? number_format($value) : $value }}</p>
        @if ($hint) <p class="truncate text-xs text-slate-400">{{ $hint }}</p> @endif
    </div>
</div>

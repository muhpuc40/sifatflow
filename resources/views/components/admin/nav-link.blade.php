@props(['href', 'active' => false, 'icon' => null])
<a href="{{ $href }}" @if ($active) aria-current="page" @endif
   {{ $attributes->class([
        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
        'bg-slate-800 text-white' => $active,
        'text-slate-300 hover:bg-slate-800/60 hover:text-white' => ! $active,
   ]) }}>
    @if ($icon)
        <x-admin.icon :name="$icon" :class="$active ? 'size-5 text-indigo-400' : 'size-5 text-slate-400 group-hover:text-slate-200'" />
    @endif
    <span>{{ $slot }}</span>
</a>

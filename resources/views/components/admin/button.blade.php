{{--
  <x-admin.button type="submit">Save</x-admin.button>
  <x-admin.button href="/x" variant="secondary" size="sm">Back</x-admin.button>
  variant: primary | secondary | danger | ghost     size: sm | md | lg
--}}
@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button'])
@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white shadow-sm hover:bg-indigo-500 focus-visible:ring-indigo-600',
        'secondary' => 'bg-white text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus-visible:ring-slate-400',
        'danger' => 'bg-red-600 text-white shadow-sm hover:bg-red-500 focus-visible:ring-red-600',
        'ghost' => 'text-slate-600 hover:bg-slate-100 focus-visible:ring-slate-400',
    ];
    $sizes = ['sm' => 'px-3 py-1.5 text-xs', 'md' => 'px-4 py-2 text-sm', 'lg' => 'px-5 py-2.5 text-base'];
    $classes = 'inline-flex items-center justify-center gap-2 rounded-lg font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 '
        .($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']);
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif

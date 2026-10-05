{{--
  <x-admin.dropdown align="right" width="w-56">
      <x-slot:trigger> <button>Open</button> </x-slot:trigger>
      ...menu content...
  </x-admin.dropdown>
--}}
@props(['align' => 'right', 'width' => 'w-56'])
@php($position = $align === 'left' ? 'left-0 origin-top-left' : 'right-0 origin-top-right')
<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <div @click="open = !open">{{ $trigger }}</div>

    <div x-show="open" x-cloak x-transition.opacity.scale.95
         class="absolute z-50 mt-2 {{ $width }} {{ $position }} rounded-xl bg-white py-1 shadow-lg ring-1 ring-slate-900/10"
         @click="open = false">
        {{ $slot }}
    </div>
</div>

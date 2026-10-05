{{--
  <x-admin.modal name="delete-user" title="Delete user"> ...body...  <x-slot:footer> buttons </x-slot:footer> </x-admin.modal>
  Open:   <x-admin.button x-on:click="$dispatch('open-modal', 'delete-user')">Delete</x-admin.button>
  Close:  x-on:click="$dispatch('close-modal')"
--}}
@props(['name', 'title' => null, 'maxWidth' => 'max-w-lg'])
<div x-data="{ open: false }"
     x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
     x-on:close-modal.window="open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open" x-cloak
     class="fixed inset-0 z-[60] flex items-center justify-center p-4" role="dialog" aria-modal="true">

    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-900/50" @click="open = false"></div>

    <div x-show="open" x-transition.scale.95 class="relative w-full {{ $maxWidth }} rounded-2xl bg-white shadow-xl">
        @if ($title)
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                <button type="button" class="rounded-md p-1 text-slate-400 hover:text-slate-600" @click="open = false" aria-label="Close">
                    <x-admin.icon name="x" />
                </button>
            </div>
        @endif
        <div class="px-6 py-5 text-sm text-slate-700">{{ $slot }}</div>
        @isset($footer)
            <div class="flex justify-end gap-2 rounded-b-2xl bg-slate-50 px-6 py-4">{{ $footer }}</div>
        @endisset
    </div>
</div>

{{-- <x-admin.empty-state title="No students yet" message="They show up here after sign-up." icon="users"> <x-admin.button>Add</x-admin.button> </x-admin.empty-state> --}}
@props(['title', 'message' => null, 'icon' => 'inbox'])
<div class="flex flex-col items-center px-6 py-12 text-center">
    <span class="flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
        <x-admin.icon :name="$icon" class="size-6" />
    </span>
    <h3 class="mt-3 text-sm font-semibold text-slate-900">{{ $title }}</h3>
    @if ($message) <p class="mt-1 text-sm text-slate-500">{{ $message }}</p> @endif
    @if (! $slot->isEmpty()) <div class="mt-4">{{ $slot }}</div> @endif
</div>

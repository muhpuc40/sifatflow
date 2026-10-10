<x-admin.layout title="Edit batch">
    <x-admin.page-header title="Edit batch" :description="$batch->name.' · '.$batch->code">
        <x-admin.button :href="route('admin.batches.show', $batch)" variant="secondary">
            <x-admin.icon name="arrow-left" class="size-4" /> Back to details
        </x-admin.button>
    </x-admin.page-header>

    <x-admin.card>
        <form method="POST" action="{{ route('admin.batches.update', $batch) }}">
            @csrf
            @method('PUT')
            @include('admin.batches._form')

            <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-5">
                <p class="text-xs text-slate-400">Created {{ $batch->created_at?->diffForHumans() }}</p>
                <div class="flex gap-2">
                    <x-admin.button :href="route('admin.batches.show', $batch)" variant="secondary">Cancel</x-admin.button>
                    <x-admin.button type="submit">Save changes</x-admin.button>
                </div>
            </div>
        </form>
    </x-admin.card>
</x-admin.layout>

<x-admin.layout title="New batch">
    <x-admin.page-header title="New batch" description="Basic information first. Classes, exams and assignments are added on the details page.">
        <x-admin.button :href="route('admin.batches.index')" variant="secondary">
            <x-admin.icon name="arrow-left" class="size-4" /> Back
        </x-admin.button>
    </x-admin.page-header>

    @if ($courses->isEmpty())
        <x-admin.alert type="warning" class="mb-6">
            There is no course yet. <a href="{{ route('admin.courses.create') }}" class="font-medium underline">Create a course</a> first.
        </x-admin.alert>
    @endif

    <x-admin.card>
        <form method="POST" action="{{ route('admin.batches.store') }}">
            @csrf
            @include('admin.batches._form')

            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-5">
                <x-admin.button :href="route('admin.batches.index')" variant="secondary">Cancel</x-admin.button>
                <x-admin.button type="submit">Create batch</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin.layout>

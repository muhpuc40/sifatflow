<x-admin.layout title="Create new course">
    <x-admin.page-header title="Create new course"
        description="Basic information first. Stages, price and the rest are added on the details page.">
        <x-admin.button :href="route('admin.courses.index')" variant="secondary">
            <x-admin.icon name="arrow-left" class="size-4" /> Back
        </x-admin.button>
    </x-admin.page-header>

    @if ($categories->isEmpty())
        <x-admin.alert type="warning" class="mb-6">
            There is no category yet. <a href="{{ route('admin.categories.index') }}" class="font-medium underline">Add a
                category</a> first.
        </x-admin.alert>
    @endif

    <x-admin.card>
        <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.courses._form')

            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-5">
                <x-admin.button :href="route('admin.courses.index')" variant="secondary">Cancel</x-admin.button>
                <x-admin.button type="submit">Create course</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</x-admin.layout>
{{-- Session messages.  redirect()->back()->with('success', 'Saved.')   or  ->with('error', '...') --}}
@if (session('success'))
    <x-admin.alert type="success" class="mb-6">{{ session('success') }}</x-admin.alert>
@endif
@if (session('error'))
    <x-admin.alert type="error" class="mb-6">{{ session('error') }}</x-admin.alert>
@endif
@if (session('status'))
    <x-admin.alert type="info" class="mb-6">{{ session('status') }}</x-admin.alert>
@endif
{{-- Dialog forms send a hidden _modal field. The dialog is closed after the reload, so list its errors here. --}}
@if ($errors->any() && old('_modal'))
    <x-admin.alert type="error" class="mb-6">
        <p class="font-medium">Please fix the following:</p>
        <ul class="mt-1 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-admin.alert>
@endif

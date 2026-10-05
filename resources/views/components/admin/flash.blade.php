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

<x-admin.guest title="Device limit">
    <h1 class="text-lg font-semibold text-slate-900">Device limit reached</h1>
    <p class="mt-1 text-sm text-slate-500">
        This account can be signed in on {{ $limit }} {{ Str::plural('device', $limit) }} at a time.
        Choose one to sign out. It is removed after you verify the code.
    </p>

    <ul class="mt-6 space-y-3">
        @foreach ($devices as $device)
            <li class="rounded-lg p-3 ring-1 ring-inset ring-slate-300">
                <p class="text-sm font-medium text-slate-900">{{ $device->device_name ?? 'Unknown device' }}</p>
                <p class="mt-0.5 text-xs text-slate-500">
                    {{ $device->ip_address ?? '—' }} · last used {{ $device->last_used_at?->diffForHumans() ?? 'never' }}
                </p>
                <form method="POST" action="{{ route('admin.login.devices.choose') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="device" value="{{ $device->id }}">
                    <x-admin.button type="submit" variant="secondary" size="sm">Sign out this device and continue</x-admin.button>
                </form>
            </li>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('admin.login.cancel') }}" class="mt-6 border-t border-slate-100 pt-4 text-center">
        @csrf
        <button type="submit" class="text-sm text-slate-500 hover:underline">Cancel</button>
    </form>
</x-admin.guest>

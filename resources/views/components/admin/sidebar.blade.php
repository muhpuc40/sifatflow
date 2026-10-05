{{-- Menu comes from config/admin.php. Items whose route does not exist yet are skipped. --}}
@php
    $items = [];
    $pendingHeading = null;

    foreach (config('admin.menu', []) as $entry) {
        if (isset($entry['heading'])) {
            $pendingHeading = $entry['heading'];
            continue;
        }
        if (! \Illuminate\Support\Facades\Route::has($entry['route'])) {
            continue;
        }
        if ($pendingHeading) {
            $items[] = ['heading' => $pendingHeading];
            $pendingHeading = null;
        }
        $items[] = $entry;
    }
@endphp

{{-- dark backdrop (mobile) --}}
<div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"></div>

<aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900 text-slate-300 transition-transform duration-200 max-lg:-translate-x-full"
       :class="{ 'max-lg:translate-x-0!': sidebarOpen }">

    <div class="flex h-16 shrink-0 items-center justify-between px-5">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-white">
            <span class="flex size-8 items-center justify-center rounded-lg bg-indigo-600">
                <x-admin.icon name="shield" class="size-5" />
            </span>
            <span class="font-semibold">{{ config('app.name') }}</span>
        </a>
        <button type="button" class="rounded-md p-1 text-slate-400 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="Close menu">
            <x-admin.icon name="x" />
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($items as $item)
            @if (isset($item['heading']))
                <p class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $item['heading'] }}</p>
            @else
                <x-admin.nav-link :href="route($item['route'])"
                                  :icon="$item['icon'] ?? null"
                                  :active="request()->routeIs($item['match'] ?? $item['route'])">
                    {{ $item['label'] }}
                </x-admin.nav-link>
            @endif
        @endforeach
    </nav>
</aside>

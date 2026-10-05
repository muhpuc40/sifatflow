@php($admin = auth()->user())
<header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
    <button type="button" class="-ml-1 rounded-md p-2 text-slate-600 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = true" aria-label="Open menu">
        <x-admin.icon name="menu" class="size-6" />
    </button>

    <div class="flex-1"></div>

    <x-admin.dropdown align="right" width="w-60">
        <x-slot:trigger>
            <button type="button" class="flex items-center gap-2 rounded-full py-1 pl-1 pr-2 hover:bg-slate-100">
                <span class="flex size-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-semibold uppercase text-white">
                    {{ mb_substr($admin->name ?? 'A', 0, 1) }}
                </span>
                <span class="hidden text-sm font-medium text-slate-700 sm:block">{{ $admin->name }}</span>
                <x-admin.icon name="chevron-down" class="size-4 text-slate-400" />
            </button>
        </x-slot:trigger>

        <div class="border-b border-slate-100 px-4 py-3">
            <p class="truncate text-sm font-medium text-slate-900">{{ $admin->name }}</p>
            <p class="truncate text-xs text-slate-500">{{ $admin->email }}</p>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">
                <x-admin.icon name="log-out" class="size-4 text-slate-400" /> Log out
            </button>
        </form>
    </x-admin.dropdown>
</header>

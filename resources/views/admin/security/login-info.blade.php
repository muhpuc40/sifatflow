<x-admin.layout title="Login info">
    <div x-data="{
            userOpen: false, deviceOpen: false, locOpen: false, locLoading: false,
            profile: null, device: null, loc: null,
            showUser(profile) { this.profile = profile; this.userOpen = true; },
            showDevice(device) { this.device = device; this.deviceOpen = true; },
            async showLocation(url) {
                this.loc = null; this.locLoading = true; this.locOpen = true;
                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    this.loc = await res.json();
                } catch (e) {
                    this.loc = { ok: false, message: 'Could not load the location. Please try again.' };
                }
                this.locLoading = false;
            },
         }" @keydown.escape.window="userOpen = false; deviceOpen = false; locOpen = false">

        <x-admin.page-header title="Login info"
            description="Every login attempt: successful, failed and blocked. Use the icons to see the account, device or location." />

        {{-- filters --}}
        <form method="GET" action="{{ route('admin.login-infos.index') }}"
            class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_12rem_10rem_auto]">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by email, phone or IP..."
                class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600">

            <select name="user_type"
                class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <option value="">All user types</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected(request('user_type') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>

            <select name="status"
                class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <option value="">Any status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <x-admin.button type="submit">Filter</x-admin.button>
                @if (request()->hasAny(['q', 'user_type', 'status']))
                    <x-admin.button :href="route('admin.login-infos.index')" variant="secondary">Reset</x-admin.button>
                @endif
            </div>
        </form>

        <x-admin.card :padded="false">
            @if ($logins->isEmpty())
                <x-admin.empty-state icon="activity" :title="request()->hasAny(['q', 'user_type', 'status']) ? 'No login matches your filter' : 'No login activity yet'"
                    message="Sign-ins, failed attempts and blocked logins are listed here." />
            @else
                <x-admin.table>
                    <x-slot:head>
                        <th>#</th>
                        <th>Account</th>
                        <th>User type</th>
                        <th>Status</th>

                        <th>Created</th>
                        <th class="text-right">Actions</th>
                    </x-slot:head>
                    @foreach ($logins as $login)
                        @php
                            $profile = $profiles[$login->id];
                            $device = $devices[$login->id];
                            $color = match ($login->status) { 'success' => 'green', 'blocked' => 'amber', default => 'red'};
                        @endphp
                        <tr>
                            <td class="text-slate-400">{{ $login->id }}</td>
                            <td>
                                <p class="font-medium text-slate-900">{{ $login->login_identifier ?? '—' }}</p>
                                <p class="text-xs {{ $login->user ? 'text-slate-500' : 'text-amber-600' }}">
                                    {{ $login->user?->name ?? 'Account not found' }}
                                </p>
                            </td>
                            <td><x-admin.badge color="indigo" class="capitalize">{{ $login->user_type }}</x-admin.badge></td>
                            <td>
                                <x-admin.badge :color="$color">
                                    {{ ucfirst($login->status) }}@if ($login->failure_reason) ·
                                    {{ str_replace('_', ' ', $login->failure_reason) }}@endif
                                </x-admin.badge>
                            </td>

                            <td class="whitespace-nowrap">{{ $login->created_at }}</td>
                            <td class="whitespace-nowrap text-right">
                                {{-- 1. user details --}}
                                @if ($profile)
                                    <button type="button"
                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                        title="User details" aria-label="User details"
                                        @click="showUser({{ \Illuminate\Support\Js::from($profile) }})">
                                        <x-admin.icon name="user" class="size-4" />
                                    </button>
                                @else
                                    <span class="inline-flex p-1.5 text-slate-300" title="Account not found">
                                        <x-admin.icon name="user" class="size-4" />
                                    </span>
                                @endif

                                {{-- 2. device --}}
                                @if ($device)
                                    <button type="button"
                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                        title="Device details" aria-label="Device details"
                                        @click="showDevice({{ \Illuminate\Support\Js::from($device) }})">
                                        <x-admin.icon name="monitor" class="size-4" />
                                    </button>
                                @else
                                    <span class="inline-flex p-1.5 text-slate-300" title="No device saved">
                                        <x-admin.icon name="monitor" class="size-4" />
                                    </span>
                                @endif

                                {{-- 3. location --}}
                                @if ($login->ip_address)
                                    <button type="button"
                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                        title="Location details" aria-label="Location details"
                                        @click="showLocation('{{ route('admin.login-infos.location', $login->id) }}')">
                                        <x-admin.icon name="map-pin" class="size-4" />
                                    </button>
                                @else
                                    <span class="inline-flex p-1.5 text-slate-300" title="No IP saved">
                                        <x-admin.icon name="map-pin" class="size-4" />
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-admin.table>
            @endif
        </x-admin.card>

        @if ($logins->hasPages())
            <div class="mt-4">{{ $logins->links() }}</div>
        @endif

        {{-- ------------------------------------------------ user details modal (2 columns) --}}
        <x-admin.dialog show="userOpen" close="userOpen = false" title="'User details'" maxWidth="max-w-3xl">
            <template x-if="profile">
                <div>
                    <div class="grid gap-6 md:grid-cols-[18rem_1fr]">

                        {{-- left: full body image & badges --}}
                        <div class="flex flex-col rounded-xl bg-slate-50 p-4 ring-1 ring-slate-900/5">
                            {{-- Image Box --}}
                            <div class="w-full overflow-hidden rounded-lg bg-white ring-1 ring-slate-200">
                                <template x-if="profile.avatar">
                                    <img :src="profile.avatar" alt="Profile Image"
                                        class="h-auto w-full aspect-square object-cover">
                                </template>
                                <template x-if="!profile.avatar">
                                    <span
                                        class="flex w-full aspect-square items-center justify-center bg-indigo-600 text-6xl font-semibold uppercase text-white"
                                        x-text="profile.name.charAt(0)"></span>
                                </template>
                            </div>

                            {{-- Status and User Type under the image box --}}
                            <div class="mt-4 flex flex-wrap justify-center gap-2">
                                <span
                                    class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20"
                                    x-text="profile.type"></span>
                                <span
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset"
                                    :class="profile.status === 'active' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-red-600/20'"
                                    x-text="profile.status"></span>
                                <span x-show="profile.deleted" x-cloak
                                    class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20">Deleted</span>
                            </div>
                        </div>

                        {{-- right: details --}}
                        <div class="min-w-0">
                            <dl class="divide-y divide-slate-100 text-sm">

                                {{-- Name Row --}}
                                <div class="flex items-start justify-between gap-4 py-2.5 first:pt-0">
                                    <dt class="shrink-0 text-slate-500">Name</dt>
                                    <dd class="min-w-0 break-all text-right font-medium text-slate-900"
                                        x-text="profile.name || '—'"></dd>
                                </div>

                                {{-- Email Row --}}
                                <div class="flex items-start justify-between gap-4 py-2.5">
                                    <dt class="shrink-0 text-slate-500">Email</dt>
                                    <dd class="min-w-0 break-all text-right font-medium text-slate-900"
                                        x-text="profile.email || '—'"></dd>
                                </div>

                                {{-- Remaining rows (hiding ID, Name, and Email to avoid duplicates) --}}
                                <template x-for="row in profile.rows" :key="row[0]">
                                    <div x-show="row[0].toLowerCase() !== 'email' && row[0].toLowerCase() !== 'name' && row[0].toLowerCase() !== 'id' && row[0].toLowerCase() !== 'user id'"
                                        class="flex items-start justify-between gap-4 py-2.5">
                                        <dt class="shrink-0 text-slate-500" x-text="row[0]"></dt>
                                        <dd class="min-w-0 whitespace-pre-line break-words text-right font-medium text-slate-900"
                                            x-text="(row[1] === null || row[1] === '') ? '—' : row[1]"></dd>
                                    </div>
                                </template>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end border-t border-slate-100 pt-4">
                        <x-admin.button type="button" variant="secondary"
                            @click="userOpen = false">Close</x-admin.button>
                    </div>
                </div>
            </template>
        </x-admin.dialog>

        {{-- ------------------------------------------------ device modal --}}
        <x-admin.dialog show="deviceOpen" close="deviceOpen = false" title="'Device details'" maxWidth="max-w-lg">
            <template x-if="device">
                <dl class="divide-y divide-slate-100 text-sm">
                    <template x-for="row in device.rows" :key="row[0]">
                        <div class="flex items-start justify-between gap-4 py-2.5 first:pt-0">
                            <dt class="shrink-0 text-slate-500" x-text="row[0]"></dt>
                            <dd class="min-w-0 break-all text-right font-medium text-slate-900"
                                x-text="(row[1] === null || row[1] === '') ? '—' : row[1]"></dd>
                        </div>
                    </template>
                </dl>
            </template>
            <div class="mt-5 flex justify-end border-t border-slate-100 pt-4">
                <x-admin.button type="button" variant="secondary" @click="deviceOpen = false">Close</x-admin.button>
            </div>
        </x-admin.dialog>

        {{-- ------------------------------------------------ location modal (details left, map right) --}}
        <x-admin.dialog show="locOpen" close="locOpen = false" title="'Login location'" maxWidth="max-w-4xl">
            <div x-show="locLoading" x-cloak class="flex items-center justify-center gap-3 py-10 text-slate-500">
                <span class="size-5 animate-spin rounded-full border-2 border-slate-300 border-t-indigo-600"></span>
                Looking up the location...
            </div>

            <template x-if="loc && !loc.ok">
                <x-admin.alert type="warning"><span x-text="loc.message"></span></x-admin.alert>
            </template>

            <template x-if="loc && loc.ok">
                <div class="grid gap-6 md:grid-cols-2">
                    {{-- left: details --}}
                    <div class="min-w-0">

                        <dl class="mt-3 divide-y divide-slate-100 text-sm">
                            <template x-for="row in loc.rows" :key="row[0]">
                                <div class="flex items-start justify-between gap-4 py-2">
                                    <dt class="shrink-0 text-slate-500" x-text="row[0]"></dt>
                                    <dd class="min-w-0 break-words text-right font-medium text-slate-900"
                                        x-text="(row[1] === null || row[1] === '') ? '—' : row[1]"></dd>
                                </div>
                            </template>
                        </dl>
                    </div>

                    {{-- right: map --}}
                    <div class="flex min-w-0 flex-col">
                        <template x-if="loc.map_embed">
                            <div class="flex flex-1 flex-col">
                                <iframe :src="loc.map_embed"
                                    class="min-h-80 w-full flex-1 rounded-lg ring-1 ring-slate-200" loading="lazy"
                                    referrerpolicy="no-referrer" title="Location map"></iframe>
                                <a :href="loc.map_link" target="_blank" rel="noopener"
                                    class="mt-2 inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline">
                                    Open larger map <x-admin.icon name="external-link" class="size-3.5" />
                                </a>
                            </div>
                        </template>
                        <template x-if="!loc.map_embed">
                            <div
                                class="flex min-h-80 flex-1 items-center justify-center rounded-lg bg-slate-50 text-sm text-slate-400 ring-1 ring-slate-200">
                                Map is not available for this IP.
                            </div>
                        </template>

                        <p class="mt-3 text-xs text-slate-500">
                            IP locations are approximate. They usually show the internet provider's area, not the exact
                            place of the user.
                        </p>
                    </div>
                </div>
            </template>

            <div class="mt-5 flex justify-end border-t border-slate-100 pt-4">
                <x-admin.button type="button" variant="secondary" @click="locOpen = false">Close</x-admin.button>
            </div>
        </x-admin.dialog>
    </div>
</x-admin.layout>
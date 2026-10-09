@php
    use App\Support\ServerInfo;

    $sys = $info['sys'];
    $mem = $info['memory'];
    $db = $info['database'];
    $diskMain = collect($info['disks'])->firstWhere('project', true) ?? collect($info['disks'])->first();
@endphp

<x-admin.layout title="Server">
    <div x-data="{
            auto: false, timer: null,
            toggle() {
                this.auto = ! this.auto;
                clearInterval(this.timer);
                if (this.auto) { this.timer = setInterval(() => location.reload(), 10000); }
            }
         }">

        <x-admin.page-header title="Server" description="Live information about the machine that runs SifatFlow. Updated at {{ now()->format('h:i:s A') }}.">
            <x-admin.button type="button" variant="secondary" @click="toggle()">
                <x-admin.icon name="clock" class="size-4" />
                <span x-text="auto ? 'Auto refresh: ON (10s)' : 'Auto refresh: OFF'"></span>
            </x-admin.button>
            <x-admin.button :href="route('admin.server.index')">
                <x-admin.icon name="activity" class="size-4" /> Refresh
            </x-admin.button>
        </x-admin.page-header>

        {{-- top numbers --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-admin.stat-card label="CPU load" :value="$sys['load_pct'] !== null ? $sys['load_pct'].' %' : 'N/A'" icon="activity"
                :hint="($sys['threads'] ?? '?').' threads'" />
            <x-admin.stat-card label="Memory used" :value="$mem['percent'] !== null ? $mem['percent'].' %' : 'N/A'" icon="layers"
                :hint="ServerInfo::bytes($mem['used']).' of '.ServerInfo::bytes($mem['total'])" />
            <x-admin.stat-card label="Disk used" :value="$diskMain ? $diskMain['percent'].' %' : 'N/A'" icon="folder"
                :hint="$diskMain ? ServerInfo::bytes($diskMain['free']).' free on '.$diskMain['path'] : null" />
            <x-admin.stat-card label="Database" :value="$db['ok'] ? $db['latency'].' ms' : 'Down'" icon="monitor"
                :hint="$db['ok'] ? 'Connected' : 'Cannot connect'" />
        </div>

        @unless ($db['ok'])
            <x-admin.alert type="error" class="mt-6">Database connection failed: {{ $db['error'] }}</x-admin.alert>
        @endunless

        <div class="mt-8 grid gap-6 lg:grid-cols-2">

            <x-admin.card title="Server & System">
                <x-admin.info-list :items="$info['server']" />
            </x-admin.card>

            <x-admin.card title="Processor (CPU)">
                @if ($sys['load_pct'] !== null)
                    <div class="mb-4"><x-admin.progress :percent="$sys['load_pct']" label="CPU load right now" /></div>
                @endif
                <x-admin.info-list :items="$info['cpu']" />
            </x-admin.card>

            <x-admin.card title="Memory (RAM)">
                @if ($mem['percent'] !== null)
                    <div class="mb-4">
                        <x-admin.progress :percent="$mem['percent']" label="RAM used"
                            :hint="ServerInfo::bytes($mem['used']).' used · '.ServerInfo::bytes($mem['free']).' free · '.ServerInfo::bytes($mem['total']).' total'" />
                    </div>
                @endif
                <x-admin.info-list :items="[
                    'Total RAM' => ServerInfo::bytes($mem['total']),
                    'Used' => ServerInfo::bytes($mem['used']),
                    'Available' => ServerInfo::bytes($mem['free']),
                    'PHP memory limit' => $mem['php_limit'],
                    'PHP memory now' => ServerInfo::bytes((float) $mem['php_now']),
                    'PHP memory peak' => ServerInfo::bytes((float) $mem['php_peak']),
                ]" />
            </x-admin.card>

            <x-admin.card title="Disk storage">
                @forelse ($info['disks'] as $disk)
                    <div class="{{ $loop->last ? '' : 'mb-5' }}">
                        <x-admin.progress :percent="$disk['percent']"
                            :label="$disk['path'].($disk['project'] ? '  (project drive)' : '')"
                            :hint="ServerInfo::bytes($disk['used']).' used · '.ServerInfo::bytes($disk['free']).' free · '.ServerInfo::bytes($disk['total']).' total'" />
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Disk information is not available.</p>
                @endforelse
            </x-admin.card>

            <x-admin.card>
                <div class="-mx-5 -mt-5 mb-4 flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Database</h2>
                    <x-admin.badge :color="$db['ok'] ? 'green' : 'red'">{{ $db['ok'] ? 'Connected' : 'Down' }}</x-admin.badge>
                </div>
                @if ($db['ok'])
                    <x-admin.info-list :items="$db['items']" />
                @else
                    <p class="text-sm text-red-600">{{ $db['error'] }}</p>
                @endif
            </x-admin.card>

            <x-admin.card title="Application (Laravel)">
                <x-admin.info-list :items="$info['laravel']" />
            </x-admin.card>

            <x-admin.card title="PHP" class="lg:col-span-2">
                <x-admin.info-list :items="$info['php']" />
            </x-admin.card>

            <x-admin.card :title="'PHP extensions ('.count($info['extensions']).')'" class="lg:col-span-2">
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($info['extensions'] as $extension)
                        <x-admin.badge color="indigo">{{ $extension }}</x-admin.badge>
                    @endforeach
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin.layout>

<x-admin.layout title="Dashboard">
    <x-admin.page-header title="Dashboard" description="A quick look at the platform." />

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Students" :value="$stats['students']" icon="users" />
        <x-admin.stat-card label="Instructors" :value="$stats['instructors']" icon="user" />
        <x-admin.stat-card label="Admins" :value="$stats['admins']" icon="shield" />
        <x-admin.stat-card label="Devices online" :value="$stats['online']" icon="monitor" hint="Used in the last 5 minutes" />
    </div>

    <x-admin.card title="Recent login activity" :padded="false" class="mt-8">
        @if ($recentLogins->isEmpty())
            <x-admin.empty-state title="No login activity yet" message="Sign-ins and failed attempts will be listed here." icon="activity" />
        @else
            <x-admin.table>
                <x-slot:head>
                    <th>Account</th>
                    <th>Panel</th>
                    <th>Result</th>
                    <th>IP address</th>
                    <th>When</th>
                </x-slot:head>
                @foreach ($recentLogins as $login)
                    <tr>
                        <td class="font-medium text-slate-900">{{ $login->login_identifier ?? '—' }}</td>
                        <td class="capitalize">{{ $login->user_type }}</td>
                        <td>
                            <x-admin.badge :color="match ($login->status) { 'success' => 'green', 'blocked' => 'amber', default => 'red' }">
                                {{ ucfirst($login->status) }}@if ($login->failure_reason) · {{ str_replace('_', ' ', $login->failure_reason) }}@endif
                            </x-admin.badge>
                        </td>
                        <td>{{ $login->ip_address ?? '—' }}</td>
                        <td class="whitespace-nowrap text-slate-500">{{ $login->created_at?->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </x-admin.table>
        @endif
    </x-admin.card>
</x-admin.layout>

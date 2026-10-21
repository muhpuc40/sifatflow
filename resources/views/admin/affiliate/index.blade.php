<x-admin.layout title="Affiliates">
    <x-admin.page-header title="Affiliates" description="Everyone who signed up for the affiliate program." />

    <form method="GET" action="{{ route('admin.affiliates.index') }}" class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_12rem_auto]">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by name, email or ref code..."
               class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600">

        <select name="status" class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600">
            <option value="">Any status</option>
            <option value="pending" @selected($filter === 'pending')>Pending</option>
            <option value="approved" @selected($filter === 'approved')>Approved</option>
            <option value="rejected" @selected($filter === 'rejected')>Rejected</option>
            <option value="suspended" @selected($filter === 'suspended')>Suspended</option>
        </select>

        <div class="flex gap-2">
            <x-admin.button type="submit">Filter</x-admin.button>
            @if (request()->hasAny(['q', 'status']))
                <x-admin.button :href="route('admin.affiliates.index')" variant="secondary">Reset</x-admin.button>
            @endif
        </div>
    </form>

    <x-admin.card :padded="false">
        @if ($profiles->isEmpty())
            <x-admin.empty-state icon="users"
                :title="request()->hasAny(['q', 'status']) ? 'No affiliate matches your filter' : 'No affiliates yet'"
                message="They show up here after they sign up for the affiliate program." />
        @else
            <x-admin.table>
                <x-slot:head>
                    <th>Affiliate</th>
                    <th>Ref code</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Decided</th>
                    <th class="text-right">Actions</th>
                </x-slot:head>
                @foreach ($profiles as $profile)
                    @php
                        $color = ['pending' => 'amber', 'approved' => 'green', 'rejected' => 'red', 'suspended' => 'gray'][$profile->status->value];
                    @endphp
                    <tr>
                        <td>
                            <p class="font-medium text-slate-900">{{ $profile->affiliate->name }}</p>
                            <p class="text-xs text-slate-500">{{ $profile->affiliate->email }}</p>
                        </td>
                        <td class="font-mono text-xs font-medium text-slate-900">{{ $profile->ref_code }}</td>
                        <td><x-admin.badge :color="$color">{{ ucfirst($profile->status->value) }}</x-admin.badge></td>
                        <td class="text-slate-500">{{ $profile->created_at->format('d M Y') }}</td>
                        <td class="text-slate-500">
                            @if ($profile->approved_at)
                                {{ $profile->approved_at->format('d M Y') }}
                                @if ($profile->approvedBy)
                                    <span class="block text-xs text-slate-400">by {{ $profile->approvedBy->name }}</span>
                                @endif
                            @else
                                &mdash;
                            @endif
                        </td>
                        <td class="whitespace-nowrap text-right">
                            @if ($profile->status === \App\Enums\AffiliateStatus::Pending)
                                <form method="POST" action="{{ route('admin.affiliates.approve', $profile) }}" class="inline"
                                      onsubmit="return confirm('Approve this affiliate?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600"
                                            title="Approve" aria-label="Approve">
                                        <x-admin.icon name="check" class="size-4" />
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.affiliates.reject', $profile) }}" class="inline"
                                      onsubmit="return confirm('Reject this affiliate?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600"
                                            title="Reject" aria-label="Reject">
                                        <x-admin.icon name="x-circle" class="size-4" />
                                    </button>
                                </form>
                            @elseif ($profile->status === \App\Enums\AffiliateStatus::Approved)
                                <form method="POST" action="{{ route('admin.affiliates.suspend', $profile) }}" class="inline"
                                      onsubmit="return confirm('Suspend this affiliate?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600"
                                            title="Suspend" aria-label="Suspend">
                                        <x-admin.icon name="shield" class="size-4" />
                                    </button>
                                </form>
                            @elseif ($profile->status === \App\Enums\AffiliateStatus::Suspended)
                                <form method="POST" action="{{ route('admin.affiliates.reactivate', $profile) }}" class="inline"
                                      onsubmit="return confirm('Reactivate this affiliate?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600"
                                            title="Reactivate" aria-label="Reactivate">
                                        <x-admin.icon name="check-circle" class="size-4" />
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        @endif
    </x-admin.card>

    @if ($profiles->hasPages())
        <div class="mt-4">{{ $profiles->links() }}</div>
    @endif
</x-admin.layout>

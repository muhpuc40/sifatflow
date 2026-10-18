<x-admin.layout title="Vouchers">
    <div x-data="{ open: false, form: { student_id: '', code: '', title: '', amount_type: 'flat', amount: '', expires_at: '', note: '', notify: true } }"
         @keydown.escape.window="open = false">
        <x-admin.page-header title="Vouchers" description="A one-time discount code for one student.">
            <x-admin.button type="button" @click="open = true">
                <x-admin.icon name="plus" class="size-4" /> New voucher
            </x-admin.button>
        </x-admin.page-header>

        <form method="GET" action="{{ route('admin.vouchers.index') }}" class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_12rem_auto]">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by code, student name or email..."
                   class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600">

            <select name="status" class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <option value="">Any status</option>
                <option value="active" @selected($filter === 'active')>Active</option>
                <option value="expired" @selected($filter === 'expired')>Expired</option>
                <option value="used" @selected($filter === 'used')>Used</option>
                <option value="cancelled" @selected($filter === 'cancelled')>Cancelled</option>
            </select>

            <div class="flex gap-2">
                <x-admin.button type="submit">Filter</x-admin.button>
                @if (request()->hasAny(['q', 'status']))
                    <x-admin.button :href="route('admin.vouchers.index')" variant="secondary">Reset</x-admin.button>
                @endif
            </div>
        </form>

        <x-admin.card :padded="false">
            @if ($vouchers->isEmpty())
                <x-admin.empty-state icon="gift"
                    :title="request()->hasAny(['q', 'status']) ? 'No voucher matches your filter' : 'No vouchers yet'"
                    message="Make a one-time discount code for a single student.">
                    <!-- <x-admin.button type="button" @click="open = true"><x-admin.icon name="plus" class="size-4" /> New voucher</x-admin.button> -->
                </x-admin.empty-state>
            @else
                <x-admin.table>
                    <x-slot:head>
                        <th>Code</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Expires</th>
                        <th>Email</th>
                        <th class="text-right">Actions</th>
                    </x-slot:head>
                    @foreach ($vouchers as $voucher)
                        @php
                            $status = $voucher->displayStatus();
                            $color = ['active' => 'green', 'used' => 'indigo', 'cancelled' => 'gray', 'expired' => 'red'][$status];
                        @endphp
                        <tr>
                            <td class="font-mono text-xs font-medium text-slate-900">{{ $voucher->code }}</td>
                            <td>
                                <p class="font-medium text-slate-900">{{ $voucher->student->name }}</p>
                                <p class="text-xs text-slate-500">{{ $voucher->student->email }}</p>
                            </td>
                            <td>{{ $voucher->amount_type->value === 'percent' ? rtrim(rtrim((string) $voucher->amount, '0'), '.').'%' : 'Tk '.number_format((float) $voucher->amount) }}</td>
                            <td><x-admin.badge :color="$color">{{ ucfirst($status) }}</x-admin.badge></td>
                            <td class="text-slate-500">{{ $voucher->expires_at->format('d M Y') }}</td>
                            <td class="text-slate-500">
                                @if ($voucher->emailed_at)
                                    <span class="inline-flex items-center gap-1 text-emerald-600">
                                        <x-admin.icon name="check" class="size-3.5" /> Sent
                                    </span>
                                @else
                                    <span class="text-amber-600">Not sent</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-right">
                                @if ($voucher->canBeUsed())
                                    <form method="POST" action="{{ route('admin.vouchers.resend', $voucher) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                                title="Resend email" aria-label="Resend email">
                                            <x-admin.icon name="upload" class="size-4" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.vouchers.cancel', $voucher) }}" class="inline"
                                          onsubmit="return confirm('Cancel this voucher?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-red-600"
                                                title="Cancel voucher" aria-label="Cancel voucher">
                                            <x-admin.icon name="x-circle" class="size-4" />
                                        </button>
                                    </form>
                                @endif
                                @if (! $voucher->used_at)
                                    <x-admin.delete-form :action="route('admin.vouchers.destroy', $voucher)"
                                        confirm="Delete this voucher?" label="Delete voucher">
                                        <x-admin.icon name="trash" class="size-4" />
                                    </x-admin.delete-form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </x-admin.table>
            @endif
        </x-admin.card>

        @if ($vouchers->hasPages())
            <div class="mt-4">{{ $vouchers->links() }}</div>
        @endif

        <x-admin.dialog show="open" close="open = false" title="'New voucher'" maxWidth="max-w-xl">
            <form method="POST" action="{{ route('admin.vouchers.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_modal" value="1">

                <x-admin.select name="student_id" label="Student" x-model="form.student_id" required>
                    <option value="">Choose a student...</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} &mdash; {{ $student->email }}</option>
                    @endforeach
                </x-admin.select>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-admin.select name="amount_type" label="Amount type" x-model="form.amount_type" required>
                        <option value="flat">Flat (taka)</option>
                        <option value="percent">Percent</option>
                    </x-admin.select>
                    <x-admin.input name="amount" type="number" step="0.01" min="0.01" label="Amount" x-model="form.amount" required />
                </div>

                <x-admin.input name="expires_at" type="date" label="Expires" x-model="form.expires_at" required />

                <x-admin.input name="title" label="Title (optional)" x-model="form.title" placeholder="Eid gift" />
                <x-admin.input name="code" label="Code (optional)" x-model="form.code"
                    hint="Empty = made automatically, for example VCH-7F3K2A." />
                <x-admin.textarea name="note" label="Note (optional, admin only)" x-model="form.note" rows="2" />

                <x-admin.checkbox name="notify" label="Email the code to the student now" x-model="form.notify" :checked="true" />

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <x-admin.button type="button" variant="secondary" @click="open = false">Cancel</x-admin.button>
                    <x-admin.button type="submit">Create voucher</x-admin.button>
                </div>
            </form>
        </x-admin.dialog>
    </div>
</x-admin.layout>

<x-admin.layout title="Device limit">
    <div x-data="{ open: false, action: '', form: {} }" @keydown.escape.window="open = false">
        <x-admin.page-header title="Device limit"
            description="How many devices one account can be signed in on at the same time." />

        <x-admin.card>
            <x-admin.table>
                <x-slot:head>
                    <th>Account type</th>
                    <th>Max devices</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th class="text-right">Actions</th>
                </x-slot:head>
                @foreach ($rows as $row)
                @php($limit = $row['limit'])
                <tr>
                    <td class="font-medium capitalize text-slate-900">{{ $row['type'] }}</td>
                    <td>
                        @if ($limit)
                            {{ $limit->max_devices }}
                        @else
                            <x-admin.badge color="amber">Not set (login blocked)</x-admin.badge>
                        @endif
                    </td>
                    <td>{{ $limit?->created_at ?? '—' }}</td>
                    <td>{{ $limit?->updated_at ?? '—' }}</td>
                    <td class="whitespace-nowrap text-right">
                        <button type="button"
                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                            title="Update limit" aria-label="Update limit"
                            @click="action = '{{ route('admin.device-limits.update', $row['type']) }}'; form = {{ \Illuminate\Support\Js::from(['type' => ucfirst($row['type']), 'max_devices' => $limit?->max_devices ?? 2]) }}; open = true">
                            <x-admin.icon name="edit" class="size-4" />
                        </button>
                    </td>
                </tr>
                @endforeach
            </x-admin.table>
        </x-admin.card>

        <x-admin.dialog show="open" close="open = false" title="'Update device limit: ' + form.type">
            <form method="POST" :action="action" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="_modal" value="1">

                <x-admin.input id="limit_max" name="max_devices" type="number" min="1" max="20"
                    label="Max devices per account" x-model="form.max_devices"
                    hint="From 1 to 20. A limit of 0 is not allowed because it would block every login." required />

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <x-admin.button type="button" variant="secondary" @click="open = false">Cancel</x-admin.button>
                    <x-admin.button type="submit">Save</x-admin.button>
                </div>
            </form>
        </x-admin.dialog>
    </div>
</x-admin.layout>
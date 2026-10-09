{{-- <x-admin.info-list :items="['PHP version' => '8.3', 'Debug' => false]" />  (label => value; booleans become Yes/No badges) --}}
@props(['items' => []])
<dl class="divide-y divide-slate-100 text-sm">
    @foreach ($items as $label => $value)
        <div class="flex items-start justify-between gap-4 py-2.5">
            <dt class="shrink-0 text-slate-500">{{ $label }}</dt>
            <dd class="min-w-0 break-words text-right font-medium text-slate-900">
                @if (is_bool($value))
                    <x-admin.badge :color="$value ? 'green' : 'gray'">{{ $value ? 'Yes' : 'No' }}</x-admin.badge>
                @else
                    {{ ($value === null || $value === '') ? '—' : $value }}
                @endif
            </dd>
        </div>
    @endforeach
</dl>

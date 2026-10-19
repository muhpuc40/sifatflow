<x-admin.layout title="Affiliate settings">
    <x-admin.page-header title="Affiliate settings"
        description="Rules for every affiliate. Changing these never changes a commission already earned - only sales made after you save." />

    <form method="POST" action="{{ route('admin.affiliate-settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <x-admin.card title="Program">
            <div class="space-y-4">
                <x-admin.checkbox name="is_active" label="Affiliate program is on"
                    :checked="$settings->is_active"
                    hint="Turn off to stop new clicks and sign-ups from earning commission. Existing affiliates stay." />
                <x-admin.checkbox name="auto_approve" label="Approve new affiliates automatically"
                    :checked="$settings->auto_approve"
                    hint="Off: every affiliate who registers waits in Pending until you approve them." />
            </div>
        </x-admin.card>

        <x-admin.card title="Commission">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.select name="commission_type" label="Commission type" :value="$settings->commission_type->value">
                    <option value="percent">Percent of the sale</option>
                    <option value="flat">Flat amount per sale</option>
                </x-admin.select>
                <x-admin.input name="commission_value" type="number" step="0.01" min="0" label="Commission value"
                    :value="$settings->commission_value"
                    hint="A percent (e.g. 10) or a flat taka amount per paid order, depending on the type above." />
            </div>
            <div class="mt-4">
                <x-admin.input name="hold_days" type="number" min="0" max="365" label="Hold period (days)"
                    :value="$settings->hold_days"
                    hint="How long to wait after the student fully pays before the commission becomes available for payout. 0 = available immediately." />
            </div>
        </x-admin.card>

        <x-admin.card title="Tracking and payouts">
            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.input name="cookie_days" type="number" min="1" max="365" label="Click is remembered for (days)"
                    :value="$settings->cookie_days"
                    hint="If the student checks out within this many days of clicking the ref link, the affiliate is credited." />
                <x-admin.input name="payout_day" type="number" min="1" max="28" label="Payout day of month"
                    :value="$settings->payout_day"
                    hint="The day you run payouts. Kept to 1-28 so it falls in every month." />
            </div>
            <div class="mt-4">
                <x-admin.input name="min_payout_amount" type="number" step="0.01" min="0" label="Minimum payout amount (Tk)"
                    :value="$settings->min_payout_amount"
                    hint="An affiliate must have at least this much available before they can request a payout." />
            </div>
        </x-admin.card>

        <div class="flex items-center justify-between">
            <p class="text-xs text-slate-500">
                @if ($settings->updatedBy)
                    Last updated by {{ $settings->updatedBy->name }} on {{ $settings->updated_at->format('d M Y, h:i A') }}
                @else
                    Not changed since setup.
                @endif
            </p>
            <x-admin.button type="submit">Save settings</x-admin.button>
        </div>
    </form>
</x-admin.layout>

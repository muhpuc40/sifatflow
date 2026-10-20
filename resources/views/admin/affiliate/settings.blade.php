<x-admin.layout title="Affiliate settings">
    <x-admin.page-header title="Affiliate settings"
        description="Configure affiliate tracking, commissions, and payouts." />

    <form method="POST" action="{{ route('admin.affiliate-settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-12">
            <x-admin.card title="Program" class="lg:col-span-4">
                <div class="space-y-4">
                    <x-admin.checkbox name="is_active" label="Affiliate program is on" :checked="$settings->is_active"
                        hint="New clicks and sign-ups can earn commission." />
                    <x-admin.checkbox name="auto_approve" label="Approve new affiliates automatically"
                        :checked="$settings->auto_approve" hint="Turn off to review new affiliates manually." />
                </div>
            </x-admin.card>

            <x-admin.card title="Commission" class="lg:col-span-8">
                <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3">
                    <x-admin.select name="commission_type" label="Type" :value="$settings->commission_type->value">
                        <option value="percent">Percent</option>
                        <option value="flat">Flat amount</option>
                    </x-admin.select>
                    <x-admin.input name="commission_value" type="number" step="0.01" min="0" label="Value"
                        :value="$settings->commission_value" hint="Percent/taka, based on the type." />
                    <x-admin.input name="hold_days" type="number" min="0" max="365" label="Hold (days)"
                        :value="$settings->hold_days" hint="0 = available immediately." />
                </div>
            </x-admin.card>
        </div>

        <x-admin.card title="Tracking and payouts">
            <div class="grid gap-4 sm:grid-cols-3">
                <x-admin.input name="cookie_days" type="number" min="1" max="365" label="Cookie duration (days)"
                    :value="$settings->cookie_days" hint="How long a referral click is remembered." />
                <x-admin.input name="payout_day" type="number" min="1" max="28" label="Payout day"
                    :value="$settings->payout_day" hint="Use a day from 1 to 28." />
                <x-admin.input name="min_payout_amount" type="number" step="0.01" min="0" label="Minimum payout (Tk)"
                    :value="$settings->min_payout_amount" hint="Minimum balance required to request payout." />
            </div>
        </x-admin.card>

        <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
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
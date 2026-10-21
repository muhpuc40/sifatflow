<x-admin.layout title="SMS composer">
    <div x-data="{
            sending: false,
            confirmOpen: false,
            recipientType: @js(old('recipient_type', 'student')),
            selectedIds: @js(array_map('strval', (array) old('user_ids', []))),
            customNumbers: @js(old('custom_numbers', '')),
            message: @js(old('message', '')),
            rate: {{ (float) $rate }},

            get parts() {
                const len = this.message.length;
                if (len === 0) return { count: 0, limit: 160, remaining: 160 };
                if (len <= 160) return { count: 1, limit: 160, remaining: 160 - len };
                const limit = 153;
                const count = Math.ceil(len / limit);
                return { count, limit, remaining: (count * limit) - len };
            },
            get customCount() {
                return this.customNumbers.split(/[\n,;]+/).map(n => n.trim()).filter(n => n.length >= 11).length;
            },
            get recipientCount() {
                return this.selectedIds.length + this.customCount;
            },
            get approxCost() {
                return (this.parts.count * this.recipientCount * this.rate).toFixed(2);
            },
            toggleSelected(event) {
                this.selectedIds = Array.from(event.target.selectedOptions).map(o => o.value);
            },
         }">

        <x-admin.page-header title="SMS composer"
            description="Send a plain text SMS to selected students, instructors, affiliates or custom numbers." />

        <form method="POST" action="{{ route('admin.communication.sms.send') }}" class="space-y-6"
              @submit.prevent="confirmOpen = true">
            @csrf

            <div class="grid gap-6 lg:grid-cols-2">
                <x-admin.card title="Recipients">
                    <div class="space-y-5">
                        <x-admin.select name="recipient_type" label="User type" x-model="recipientType" @change="selectedIds = []" required>
                            <option value="student">Students</option>
                            <option value="instructor">Instructors</option>
                            <option value="affiliate">Affiliates</option>
                        </x-admin.select>

                        @foreach ($recipients as $type => $users)
                            <div x-show="recipientType === '{{ $type }}'" x-cloak>
                                <label for="{{ $type }}_ids" class="mb-1.5 block text-sm font-medium text-slate-700">
                                    Select {{ \Illuminate\Support\Str::plural(ucfirst($type)) }}
                                </label>
                                <select id="{{ $type }}_ids" name="user_ids[]" multiple size="8"
                                    :disabled="recipientType !== '{{ $type }}'"
                                    @change="toggleSelected($event)"
                                    class="block w-full rounded-lg border-0 text-sm text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(in_array($user->id, (array) old('user_ids', [])))>
                                            {{ $user->name }} — {{ $user->phone }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1.5 text-xs text-slate-500">Hold Ctrl (Windows) or Command (Mac) to select multiple. Only users with a phone number are listed.</p>
                            </div>
                        @endforeach

                        @error('user_ids')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </x-admin.card>

                <x-admin.card title="Custom numbers">
                    <label for="custom_numbers" class="mb-1.5 block text-sm font-medium text-slate-700">Phone numbers</label>
                    <textarea id="custom_numbers" name="custom_numbers" rows="8" x-model="customNumbers"
                        placeholder="01710000000, 01820000000&#10;or one number per line"
                        class="block w-full rounded-lg border-0 px-3 py-2 text-sm text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600"></textarea>
                    <p class="mt-1.5 text-xs text-slate-500">Separate numbers with commas, semicolons, or new lines. Bangladeshi numbers only.</p>
                    @error('custom_numbers')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </x-admin.card>
            </div>

           
                <label for="sms_message" class="mb-1.5 block text-sm font-medium text-slate-700">Message</label>
                <textarea id="sms_message" name="message" rows="5" x-model="message" maxlength="1530"
                    placeholder="Write your SMS here..."
                    class="block w-full rounded-lg border-0 px-3 py-2 text-sm text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600"></textarea>

                <div class="mt-2 flex flex-wrap gap-x-6 gap-y-1 text-xs text-slate-500">
                    <span>Character count: <strong class="text-slate-700" x-text="message.length"></strong></span>
                    <span>SMS parts: <strong class="text-slate-700" x-text="parts.count"></strong></span>
                    <span>Remaining in this part: <strong :class="parts.remaining < 0 ? 'text-red-600' : 'text-slate-700'" x-text="parts.remaining"></strong></span>
                </div>
                <p class="mt-1.5 text-xs text-amber-600" x-show="parts.count > 1">
                    Over 160 characters: the message is split into <span x-text="parts.count"></span> SMS parts. Each part is billed separately.
                </p>

                @error('message')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
        

            <div class="flex justify-end">
                <x-admin.button type="submit">
                    <x-admin.icon name="send" class="size-4" /> Review and send
                </x-admin.button>
            </div>
        </form>

        {{-- confirmation --}}
        <x-admin.dialog show="confirmOpen" close="confirmOpen = false" title="'Send this SMS?'" maxWidth="max-w-md">
            <dl class="space-y-2.5 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Recipients (approx.)</dt>
                    <dd class="font-medium text-slate-900" x-text="recipientCount"></dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">SMS parts</dt>
                    <dd class="font-medium text-slate-900" x-text="parts.count"></dd></div>
                <div class="flex justify-between border-t border-slate-100 pt-2.5"><dt class="text-slate-500">Approximate cost</dt>
                    <dd class="font-semibold text-slate-900">৳ <span x-text="approxCost"></span></dd></div>
            </dl>
            <p class="mt-3 rounded-lg bg-slate-50 p-3 text-xs text-slate-600" x-text="message || 'Empty message'"></p>
            <p class="mt-3 text-xs text-slate-500">The exact recipient count is confirmed on the server (duplicates and invalid numbers are removed). Cost is an estimate at ৳{{ number_format($rate, 2) }} per SMS part per number.</p>

            <div class="mt-5 flex justify-end gap-2 border-t border-slate-100 pt-4">
                <x-admin.button type="button" variant="secondary" @click="confirmOpen = false">Cancel</x-admin.button>
                <x-admin.button type="button" x-bind:disabled="sending"
                    @click="sending = true; confirmOpen = false; $root.querySelector('form').submit();">
                    <span x-show="!sending">Confirm &amp; send</span>
                    <span x-show="sending" x-cloak class="inline-flex items-center gap-2">
                        <span class="size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span> Sending...
                    </span>
                </x-admin.button>
            </div>
        </x-admin.dialog>
    </div>
</x-admin.layout>
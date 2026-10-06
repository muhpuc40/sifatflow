@php
    $stagesCount = $course->curricula->count();
    $modulesCount = $course->curricula->sum(fn ($s) => $s->modules->count());
    $itemsCount = $course->curricula->sum(fn ($s) => $s->modules->sum(fn ($m) => $m->items->count()));
    $approved = $course->reviews->where('status', \App\Enums\ReviewStatus::Approved);
    $final = $currentPrice?->finalPrice();

    $checks = [
        ['Has at least one stage and module', $modulesCount > 0],
        ['Modules have items (classes, exams ...)', $itemsCount > 0],
        ['Price is set', (bool) $currentPrice],
        ['Installments add up to the price', $currentPrice ? $currentPrice->hasValidPaymentPlan() : false],
        ['Learning outcomes added', $course->learningOutcomes->count() > 0],
        ['Thumbnail uploaded', (bool) $course->thumbnail],
    ];
@endphp

<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <x-admin.card title="About this course">
            @if ($course->description)
                <p class="whitespace-pre-line text-sm leading-6 text-slate-700">{{ $course->description }}</p>
            @else
                <p class="text-sm text-slate-400">No description yet. <a href="{{ route('admin.courses.edit', $course) }}" class="text-indigo-600 underline">Add one</a>.</p>
            @endif
        </x-admin.card>

        <x-admin.card title="Details">
            <dl class="grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">Category</dt><dd class="mt-0.5 font-medium text-slate-900">{{ $course->category?->name ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Slug</dt><dd class="mt-0.5 font-mono text-slate-900">{{ $course->slug }}</dd></div>
                <div><dt class="text-slate-500">Level</dt><dd class="mt-0.5 font-medium capitalize text-slate-900">{{ $course->level ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Status</dt><dd class="mt-0.5"><x-admin.status-badge :status="$course->status" /></dd></div>
                <div><dt class="text-slate-500">Active</dt><dd class="mt-0.5 font-medium text-slate-900">{{ $course->is_active ? 'Yes' : 'No' }}</dd></div>
                <div>
                    <dt class="text-slate-500">Preview video</dt>
                    <dd class="mt-0.5">
                        @if ($course->preview_video)
                            <a href="{{ $course->preview_video }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-indigo-600 hover:underline">
                                Open link <x-admin.icon name="external-link" class="size-3.5" />
                            </a>
                        @else — @endif
                    </dd>
                </div>
                <div><dt class="text-slate-500">Created</dt><dd class="mt-0.5 text-slate-900">{{ $course->created_at?->format('d M Y, H:i') }}</dd></div>
                <div><dt class="text-slate-500">Last updated</dt><dd class="mt-0.5 text-slate-900">{{ $course->updated_at?->format('d M Y, H:i') }}</dd></div>
            </dl>
        </x-admin.card>
    </div>

    <div class="space-y-6">
        <x-admin.card title="Summary">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Stages</dt><dd class="font-medium text-slate-900">{{ $stagesCount }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Modules</dt><dd class="font-medium text-slate-900">{{ $modulesCount }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Items</dt><dd class="font-medium text-slate-900">{{ $itemsCount }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-500">Rating</dt>
                    <dd class="font-medium text-slate-900">{{ $approved->count() ? number_format($approved->avg('rating'), 1).' ('.$approved->count().')' : '—' }}</dd></div>
                <div class="flex justify-between border-t border-slate-100 pt-3"><dt class="text-slate-500">Price</dt>
                    <dd class="font-semibold text-slate-900">{{ $final !== null ? '৳'.number_format($final) : 'Not set' }}</dd></div>
            </dl>
        </x-admin.card>

        <x-admin.card title="Ready to publish?">
            <ul class="space-y-2.5 text-sm">
                @foreach ($checks as [$text, $ok])
                    <li class="flex items-start gap-2">
                        <x-admin.icon :name="$ok ? 'check-circle' : 'alert-circle'" :class="'mt-0.5 size-4 shrink-0 '.($ok ? 'text-emerald-500' : 'text-amber-500')" />
                        <span class="{{ $ok ? 'text-slate-700' : 'text-slate-500' }}">{{ $text }}</span>
                    </li>
                @endforeach
            </ul>
        </x-admin.card>
    </div>
</div>

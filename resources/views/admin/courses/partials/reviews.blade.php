{{-- Student reviews. Only approved ones count in the rating. --}}
@php $counts = $course->reviews->groupBy(fn ($r) => $r->status->value)->map->count(); @endphp

<x-admin.card>
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-100 px-5 py-4">
        <h3 class="mr-auto font-semibold text-slate-900">Reviews</h3>
        <x-admin.badge color="amber">{{ $counts['pending'] ?? 0 }} pending</x-admin.badge>
        <x-admin.badge color="green">{{ $counts['approved'] ?? 0 }} approved</x-admin.badge>
        <x-admin.badge color="red">{{ $counts['rejected'] ?? 0 }} rejected</x-admin.badge>
    </div>

    @forelse ($course->reviews->sortByDesc('id') as $review)
        <div class="flex flex-wrap items-start gap-4 border-b border-slate-100 px-5 py-4 last:border-0">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-medium text-slate-900">{{ $review->student?->name ?? 'Deleted student' }}</span>
                    <span class="text-amber-500" aria-label="{{ $review->rating }} of 5">{{ str_repeat('★', $review->rating) }}<span class="text-slate-300">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                    <x-admin.badge :color="match ($review->status->value) { 'approved' => 'green', 'rejected' => 'red', default => 'amber' }" class="capitalize">{{ $review->status->value }}</x-admin.badge>
                    <span class="text-xs text-slate-400">{{ $review->created_at?->diffForHumans() }}</span>
                </div>
                @if ($review->comment) <p class="mt-1 whitespace-pre-line text-sm text-slate-600">{{ $review->comment }}</p> @endif
            </div>
            <div class="flex shrink-0 items-center gap-1">
                @foreach (['approved' => ['Approve', 'secondary'], 'rejected' => ['Reject', 'secondary']] as $status => [$label, $variant])
                    @if ($review->status->value !== $status)
                        <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="{{ $status }}">
                            <x-admin.button type="submit" :variant="$variant" size="sm">{{ $label }}</x-admin.button>
                        </form>
                    @endif
                @endforeach
                <x-admin.delete-form :action="route('admin.reviews.destroy', $review)" confirm="Delete this review?" label="Delete review">
                    <x-admin.icon name="trash" class="size-4" />
                </x-admin.delete-form>
            </div>
        </div>
    @empty
        <x-admin.empty-state icon="star" title="No reviews yet" message="Students can review the course after they enroll." />
    @endforelse
</x-admin.card>

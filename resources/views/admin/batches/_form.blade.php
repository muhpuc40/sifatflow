{{-- Shared by create and edit. Needs: $batch, $courses, $instructors, $statuses, $courseLocked (edit only) --}}
@php($locked = $courseLocked ?? false)
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        @if ($locked)
            {{-- A disabled field is not sent, so the value travels in a hidden field --}}
            <input type="hidden" name="course_list_id" value="{{ $batch->course_list_id }}">
            <x-admin.select id="course_locked" name="course_locked" label="Course" :value="$batch->course_list_id" disabled>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </x-admin.select>
            <p class="mt-1.5 text-xs text-slate-500">The course cannot change because this batch already has classes, exams or assignments.</p>
        @else
            <x-admin.select name="course_list_id" label="Course" :value="$batch->course_list_id" required>
                <option value="">Choose a course...</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </x-admin.select>
        @endif
    </div>

    <x-admin.input name="name" label="Batch name" :value="$batch->name" placeholder="Batch 12" required />

    <x-admin.input name="code" label="Code" :value="$batch->code" placeholder="FSW-12"
        hint="Leave empty to make it automatically (for example FSW-1)." />

    <x-admin.select name="instructor_id" label="Main instructor" :value="$batch->instructor_id">
        <option value="">Not set</option>
        @foreach ($instructors as $instructor)
            <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
        @endforeach
    </x-admin.select>

    <x-admin.input name="start_date" type="date" label="Start date" :value="$batch->start_date?->format('Y-m-d')" required />
    <x-admin.input name="approx_end_date" type="date" label="Approx. end date" :value="$batch->approx_end_date?->format('Y-m-d')" />

    <x-admin.input name="enroll_deadline" type="date" label="Enroll deadline" :value="$batch->enroll_deadline?->format('Y-m-d')"
        hint="Empty = enrollment stays open." />
    <x-admin.input name="extended_enroll_deadline" type="date" label="Extended enroll deadline"
        :value="$batch->extended_enroll_deadline?->format('Y-m-d')" hint="Use it to give more time after the deadline." />

    <x-admin.input name="seat_limit" type="number" min="1" label="Seat limit" :value="$batch->seat_limit"
        hint="Empty = unlimited seats." />

    <x-admin.select name="status" label="Status" :value="$batch->status?->value" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
        @endforeach
    </x-admin.select>

    <div class="sm:col-span-2">
        <x-admin.checkbox name="is_active" label="Active (an inactive batch is hidden from enrollment)"
            :checked="$batch->is_active" />
    </div>
</div>

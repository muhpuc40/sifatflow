{{-- Exams of the batch. The exam item comes from the course curriculum. --}}
@php
    $examItems = $itemOptions['exam'] ?? [];
    $empty = ['course_module_items_id' => '', 'title' => '', 'exam_date' => '', 'start_time' => '10:00', 'duration_minutes' => 60,
              'total_marks' => 100, 'pass_marks' => 40, 'status' => 'scheduled', 'instruction' => ''];
@endphp

<div x-data="{
        open: false, mode: 'add', action: '', form: {},
        show(mode, action, form) { this.mode = mode; this.action = action; this.form = form; this.open = true; },
     }">

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">An exam is linked to an exam item of the course. Publish the result when marking is done.</p>
        <x-admin.button type="button" @click="show('add', '{{ route('admin.batch-exams.store', $batch) }}', {{ \Illuminate\Support\Js::from($empty) }})">
            <x-admin.icon name="plus" class="size-4" /> Add exam
        </x-admin.button>
    </div>

    <x-admin.card>
        @if ($batch->exams->isEmpty())
            <x-admin.empty-state icon="check-circle" title="No exams yet"
                :message="$examItems ? 'Add the first exam of this batch.' : 'This course has no exam items yet. Add them on the course curriculum first.'" />
        @else
            <x-admin.table>
                <x-slot:head><th>Exam</th><th>Date</th><th>Duration</th><th>Marks</th><th>Result</th><th>Status</th><th class="text-right">Actions</th></x-slot:head>
                @foreach ($batch->exams as $exam)
                    <tr>
                        <td>
                            <p class="font-medium text-slate-900">{{ $exam->title }}</p>
                            <p class="text-xs text-slate-500">{{ $exam->item?->module?->title }}</p>
                        </td>
                        <td class="whitespace-nowrap">
                            <p>{{ $exam->exam_date->format('d M Y') }}</p>
                            <p class="text-xs text-slate-500">{{ \Illuminate\Support\Carbon::parse($exam->start_time)->format('h:i A') }}</p>
                        </td>
                        <td>{{ $exam->duration_minutes }} min</td>
                        <td>{{ $exam->pass_marks }} / {{ $exam->total_marks }}</td>
                        <td>
                            @if ($exam->isResultPublished())
                                <x-admin.badge color="green">Published</x-admin.badge>
                            @else
                                <x-admin.badge color="gray">Hidden</x-admin.badge>
                            @endif
                        </td>
                        <td><x-admin.state-badge :status="$exam->status" /></td>
                        <td class="whitespace-nowrap text-right">
                            <form method="POST" action="{{ route('admin.batch-exams.result', $exam) }}" class="inline">
                                @csrf @method('PATCH')
                                <x-admin.button type="submit" variant="secondary" size="sm">{{ $exam->isResultPublished() ? 'Hide result' : 'Publish result' }}</x-admin.button>
                            </form>
                            <button type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit" aria-label="Edit exam"
                                @click="show('edit', '{{ route('admin.batch-exams.update', $exam) }}', {{ \Illuminate\Support\Js::from([
                                    'course_module_items_id' => (string) $exam->course_module_items_id, 'title' => $exam->title,
                                    'exam_date' => $exam->exam_date->format('Y-m-d'), 'start_time' => substr($exam->start_time, 0, 5),
                                    'duration_minutes' => $exam->duration_minutes, 'total_marks' => $exam->total_marks,
                                    'pass_marks' => $exam->pass_marks, 'status' => $exam->status->value, 'instruction' => $exam->instruction ?? '',
                                ]) }})">
                                <x-admin.icon name="edit" class="size-4" />
                            </button>
                            <x-admin.delete-form :action="route('admin.batch-exams.destroy', $exam)" confirm="Delete this exam?" label="Delete exam">
                                <x-admin.icon name="trash" class="size-4" />
                            </x-admin.delete-form>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        @endif
    </x-admin.card>

    <x-admin.dialog show="open" close="open = false" title="mode === 'add' ? 'Add exam' : 'Edit exam'" max-width="max-w-xl">
        <form method="POST" :action="action" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="1">
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'add'">

            <x-admin.select id="exam_item" name="course_module_items_id" label="Exam item of the course" x-model="form.course_module_items_id" required>
                <option value="">Choose an exam...</option>
                @foreach ($examItems as $item) <option value="{{ $item['id'] }}">{{ $item['label'] }}</option> @endforeach
            </x-admin.select>
            <x-admin.input id="exam_title" name="title" label="Title" x-model="form.title" placeholder="Mid-term exam" required />

            <div class="grid grid-cols-3 gap-3">
                <x-admin.input id="exam_date" name="exam_date" type="date" label="Date" x-model="form.exam_date" required />
                <x-admin.input id="exam_time" name="start_time" type="time" label="Start time" x-model="form.start_time" required />
                <x-admin.input id="exam_duration" name="duration_minutes" type="number" min="1" label="Minutes" x-model="form.duration_minutes" required />
            </div>
            <div class="grid grid-cols-3 gap-3">
                <x-admin.input id="exam_total" name="total_marks" type="number" min="1" label="Total marks" x-model="form.total_marks" required />
                <x-admin.input id="exam_pass" name="pass_marks" type="number" min="0" label="Pass marks" x-model="form.pass_marks" />
                <x-admin.select id="exam_status" name="status" label="Status" x-model="form.status" required>
                    @foreach (\App\Enums\ExamStatus::cases() as $status) <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option> @endforeach
                </x-admin.select>
            </div>
            <x-admin.textarea id="exam_instruction" name="instruction" label="Instruction" x-model="form.instruction" rows="3" />

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <x-admin.button type="button" variant="secondary" @click="open = false">Cancel</x-admin.button>
                <x-admin.button type="submit">Save exam</x-admin.button>
            </div>
        </form>
    </x-admin.dialog>
</div>

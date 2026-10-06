{{-- What students learn (outcomes) and frequently asked questions. --}}
<div class="grid gap-6 lg:grid-cols-2"
     x-data="{
        dlg: null, mode: 'add', action: '', form: {},
        open(kind, mode, action, form) { this.dlg = kind; this.mode = mode; this.action = action; this.form = form; },
     }">

    {{-- ------------------------------------------------------------ outcomes --}}
    <x-admin.card>
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <h3 class="font-semibold text-slate-900">What students will learn</h3>
            <x-admin.button type="button" variant="secondary" size="sm"
                @click="open('outcome', 'add', '{{ route('admin.outcomes.store', $course) }}', { title: '', sort_order: '' })">
                <x-admin.icon name="plus" class="size-4" /> Add
            </x-admin.button>
        </div>
        @forelse ($course->learningOutcomes as $outcome)
            <div class="flex items-start gap-3 border-b border-slate-100 px-5 py-3 last:border-0">
                <x-admin.icon name="check-circle" class="mt-0.5 size-5 shrink-0 text-emerald-500" />
                <p class="min-w-0 flex-1 text-sm text-slate-700">{{ $outcome->title }}</p>
                <div class="flex shrink-0 items-center">
                    <button type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit" aria-label="Edit outcome"
                        @click="open('outcome', 'edit', '{{ route('admin.outcomes.update', $outcome) }}', {{ \Illuminate\Support\Js::from(['title' => $outcome->title, 'sort_order' => $outcome->sort_order]) }})">
                        <x-admin.icon name="edit" class="size-4" />
                    </button>
                    <x-admin.delete-form :action="route('admin.outcomes.destroy', $outcome)" confirm="Delete this line?" label="Delete outcome">
                        <x-admin.icon name="trash" class="size-4" />
                    </x-admin.delete-form>
                </div>
            </div>
        @empty
            <x-admin.empty-state icon="check-circle" title="No outcomes yet" message="List what a student can do after finishing the course." />
        @endforelse
    </x-admin.card>

    {{-- ------------------------------------------------------------ FAQ --}}
    <x-admin.card>
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <h3 class="font-semibold text-slate-900">FAQ</h3>
            <x-admin.button type="button" variant="secondary" size="sm"
                @click="open('faq', 'add', '{{ route('admin.faqs.store', $course) }}', { question: '', answer: '', sort_order: '' })">
                <x-admin.icon name="plus" class="size-4" /> Add
            </x-admin.button>
        </div>
        @forelse ($course->faqs as $faq)
            <div class="border-b border-slate-100 px-5 py-3 last:border-0">
                <div class="flex items-start gap-3">
                    <p class="min-w-0 flex-1 text-sm font-medium text-slate-900">{{ $faq->question }}</p>
                    <div class="flex shrink-0 items-center">
                        <button type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit" aria-label="Edit question"
                            @click="open('faq', 'edit', '{{ route('admin.faqs.update', $faq) }}', {{ \Illuminate\Support\Js::from(['question' => $faq->question, 'answer' => $faq->answer, 'sort_order' => $faq->sort_order]) }})">
                            <x-admin.icon name="edit" class="size-4" />
                        </button>
                        <x-admin.delete-form :action="route('admin.faqs.destroy', $faq)" confirm="Delete this question?" label="Delete question">
                            <x-admin.icon name="trash" class="size-4" />
                        </x-admin.delete-form>
                    </div>
                </div>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-500">{{ $faq->answer }}</p>
            </div>
        @empty
            <x-admin.empty-state icon="inbox" title="No questions yet" message="Add the questions students ask most." />
        @endforelse
    </x-admin.card>

    {{-- ------------------------------------------------------------ dialogs --}}
    <x-admin.dialog show="dlg === 'outcome'" close="dlg = null" title="mode === 'add' ? 'Add outcome' : 'Edit outcome'">
        <form method="POST" :action="action" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="1">
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'add'">
            <x-admin.input id="outcome_title" name="title" label="Outcome" x-model="form.title" placeholder="Build a responsive website" required />
            <x-admin.input id="outcome_order" name="sort_order" type="number" min="0" label="Order" x-model="form.sort_order" hint="Empty = last" />
            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <x-admin.button type="button" variant="secondary" @click="dlg = null">Cancel</x-admin.button>
                <x-admin.button type="submit">Save</x-admin.button>
            </div>
        </form>
    </x-admin.dialog>

    <x-admin.dialog show="dlg === 'faq'" close="dlg = null" title="mode === 'add' ? 'Add question' : 'Edit question'">
        <form method="POST" :action="action" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="1">
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'add'">
            <x-admin.input id="faq_question" name="question" label="Question" x-model="form.question" required />
            <x-admin.textarea id="faq_answer" name="answer" label="Answer" x-model="form.answer" rows="5" required />
            <x-admin.input id="faq_order" name="sort_order" type="number" min="0" label="Order" x-model="form.sort_order" hint="Empty = last" />
            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <x-admin.button type="button" variant="secondary" @click="dlg = null">Cancel</x-admin.button>
                <x-admin.button type="submit">Save</x-admin.button>
            </div>
        </form>
    </x-admin.dialog>
</div>

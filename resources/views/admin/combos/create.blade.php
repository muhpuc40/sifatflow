<x-admin.layout title="New combo offer">
    <div x-data="{
            form: {
                title: @js(old('title', '')),
                description: @js(old('description', '')),
                price_type: @js(old('price_type', 'percent')),
                price_value: @js(old('price_value', '')),
                starts_at: @js(old('starts_at', '')),
                ends_at: @js(old('ends_at', '')),
                is_active: @js((bool) old('is_active', true)),
                course_ids: @js(old('course_ids', [])),
            },
            courses: @js($courses->map(fn ($c) => ['id' => $c->id, 'title' => $c->title, 'price' => (float) ($c->currentPrice?->finalPrice() ?? 0)])->values()),
            get selectedCourses() { return this.courses.filter(c => this.form.course_ids.includes(c.id)) },
            get sum() { return this.selectedCourses.reduce((s, c) => s + c.price, 0) },
            get final() {
                if (this.form.price_type === 'flat') return Math.min(Number(this.form.price_value || 0), this.sum);
                const pct = Math.min(Math.max(Number(this.form.price_value || 0), 0), 100);
                return Math.max(this.sum - (this.sum * pct / 100), 0);
            },
            get discount() { return Math.max(this.sum - this.final, 0) },
        }">
        <x-admin.page-header title="New combo offer" description="Sell several courses together for one price.">
            <x-admin.button :href="route('admin.combos.index')" variant="secondary">
                <x-admin.icon name="arrow-left" class="size-4" /> Combo offers
            </x-admin.button>
        </x-admin.page-header>

        <form method="POST" action="{{ route('admin.combos.store') }}">
            @csrf
            @include('admin.combos._form', ['combo' => $combo, 'courses' => $courses, 'selected' => $selected])
        </form>
    </div>
</x-admin.layout>

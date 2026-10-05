{{-- <x-admin.textarea name="description" label="Description" :value="$course->description" rows="4" /> --}}
@props(['name', 'label' => null, 'value' => null])
<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}"
              {{ $attributes->class([
                'block w-full rounded-lg border-0 px-3 py-2 text-sm text-slate-900 shadow-sm ring-1 ring-inset placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:outline-none',
                'ring-slate-300 focus:ring-indigo-600' => ! $errors->has($name),
                'ring-red-400 focus:ring-red-500' => $errors->has($name),
              ]) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

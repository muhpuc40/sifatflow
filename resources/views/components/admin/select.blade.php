{{--
  <x-admin.select name="status" label="Status" :value="$user->status->value">
      <option value="active">Active</option>
  </x-admin.select>
--}}
@props(['name', 'label' => null, 'value' => null])
<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <select id="{{ $name }}" name="{{ $name }}" data-selected="{{ old($name, $value) }}"
            x-data x-init="if ($el.dataset.selected !== '') $el.value = $el.dataset.selected"
            {{ $attributes->class([
                'block w-full rounded-lg border-0 px-3 py-2 text-sm text-slate-900 shadow-sm ring-1 ring-inset focus:ring-2 focus:ring-inset focus:outline-none',
                'ring-slate-300 focus:ring-indigo-600' => ! $errors->has($name),
                'ring-red-400 focus:ring-red-500' => $errors->has($name),
            ]) }}>
        {{ $slot }}
    </select>
    @error($name)
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

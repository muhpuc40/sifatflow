{{--
  <x-admin.input name="email" label="Email" type="email" :value="$user->email" required />
  Shows the old() value and the validation error for the field automatically.
--}}
@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'hint' => null, 'id' => null])
<div>
    @if ($label)
        <label for="{{ $id ?? $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <input id="{{ $id ?? $name }}" name="{{ $name }}" type="{{ $type }}"
           @if (! in_array($type, ['password', 'file'])) value="{{ old($name, $value) }}" @endif
           {{ $attributes->class([
                'block w-full rounded-lg border-0 px-3 py-2 text-sm text-slate-900 shadow-sm ring-1 ring-inset placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:outline-none',
                'ring-slate-300 focus:ring-indigo-600' => ! $errors->has($name),
                'ring-red-400 focus:ring-red-500' => $errors->has($name),
           ]) }}>
    @if ($hint && ! $errors->has($name))
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

{{-- <x-admin.checkbox name="is_active" label="Active" :checked="$course->is_active" /> --}}
@props(['name', 'label', 'checked' => false, 'value' => '1'])
<label class="flex items-center gap-2 text-sm text-slate-700">
    {{-- hidden 0 makes an unchecked box send a value --}}
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked(old($name, $checked))
           {{ $attributes->merge(['class' => 'size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600']) }}>
    <span>{{ $label }}</span>
</label>

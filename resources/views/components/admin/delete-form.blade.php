{{--
  A button that deletes after a confirm question.
  <x-admin.delete-form :action="route('admin.modules.destroy', $module)" confirm="Delete this module?">
      <x-admin.icon name="trash" class="size-4" />
  </x-admin.delete-form>
--}}
@props(['action', 'confirm' => 'Are you sure?', 'label' => 'Delete'])
<form method="POST" action="{{ $action }}" class="inline" data-confirm="{{ $confirm }}" onsubmit="return confirm(this.dataset.confirm)">
    @csrf
    @method('DELETE')
    <button type="submit" title="{{ $label }}" aria-label="{{ $label }}"
            {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600']) }}>
        {{ $slot }}
    </button>
</form>

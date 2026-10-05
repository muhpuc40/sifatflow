{{--
  <x-admin.card title="Recent logins" padded="false"> ... </x-admin.card>
  Optional slot <x-slot:actions> for buttons in the card header.
--}}
@props(['title' => null, 'padded' => true])
<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-900/5']) }}>
    @if ($title || isset($actions))
        <header class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">{{ $title }}</h2>
            @isset($actions) <div class="flex items-center gap-2">{{ $actions }}</div> @endisset
        </header>
    @endif
    <div @class(['p-5' => filter_var($padded, FILTER_VALIDATE_BOOLEAN)])>{{ $slot }}</div>
</section>

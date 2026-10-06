{{--
  A popup whose open/closed state lives in the PARENT Alpine scope (x-data), so one dialog
  can serve "add" and "edit" of many rows.

  <div x-data="{ open: false, mode: 'add', action: '', form: {} }">
      <x-admin.dialog show="open" close="open = false" title="mode === 'add' ? 'Add module' : 'Edit module'">
          <form method="POST" :action="action"> ... </form>
      </x-admin.dialog>
  </div>

  show / close / title are Alpine expressions (not plain text).
--}}
@props(['show' => 'open', 'close' => 'open = false', 'title' => "''", 'maxWidth' => 'max-w-lg'])
<div x-show="{{ $show }}" x-cloak
     @keydown.escape.window="{{ $close }}"
     class="fixed inset-0 z-[60] overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex min-h-full items-start justify-center p-4 sm:items-center">
        <div x-show="{{ $show }}" x-transition.opacity @click="{{ $close }}" class="fixed inset-0 bg-slate-900/50"></div>

        <div x-show="{{ $show }}" x-transition.scale.95
             class="relative my-8 w-full {{ $maxWidth }} rounded-2xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-base font-semibold text-slate-900" x-text="{{ $title }}"></h3>
                <button type="button" class="rounded-md p-1 text-slate-400 hover:text-slate-600" @click="{{ $close }}" aria-label="Close">
                    <x-admin.icon name="x" />
                </button>
            </div>
            <div class="px-6 py-5 text-sm text-slate-700">{{ $slot }}</div>
        </div>
    </div>
</div>

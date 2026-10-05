{{--
  <x-admin.table>
      <x-slot:head> <th>Name</th> <th>Email</th> </x-slot:head>
      @foreach ($rows as $row) <tr> <td>..</td> </tr> @endforeach
  </x-admin.table>
  Cells: use <th> in head and <td> in rows; the styles below apply to them automatically.
--}}
<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-slate-200 text-sm [&_th]:whitespace-nowrap [&_th]:px-5 [&_th]:py-3 [&_th]:text-left [&_th]:text-xs [&_th]:font-semibold [&_th]:uppercase [&_th]:tracking-wide [&_th]:text-slate-500 [&_td]:px-5 [&_td]:py-3 [&_td]:text-slate-700']) }}>
        <thead class="bg-slate-50"><tr>{{ $head }}</tr></thead>
        <tbody class="divide-y divide-slate-100 bg-white">{{ $slot }}</tbody>
    </table>
</div>

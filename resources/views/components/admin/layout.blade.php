{{--
  Main admin page.   <x-admin.layout title="Dashboard"> ...page content... </x-admin.layout>
--}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

    <x-admin.sidebar />

    <div class="lg:pl-64">
        <x-admin.topbar />

        <main class="p-4 sm:p-6 lg:p-8">
            <x-admin.flash />
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>
</html>

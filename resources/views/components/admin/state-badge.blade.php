{{-- <x-admin.state-badge :status="$batch->status" />  colored badge for batch / class / exam / assignment statuses --}}
@props(['status'])
@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $color = match ($value) {
        'upcoming', 'scheduled' => 'blue',
        'running', 'held', 'finished', 'published' => 'green',
        'rescheduled', 'draft' => 'amber',
        'cancelled' => 'red',
        default => 'gray',   // completed, closed
    };
@endphp
<x-admin.badge :color="$color" {{ $attributes }}>{{ ucfirst($value) }}</x-admin.badge>

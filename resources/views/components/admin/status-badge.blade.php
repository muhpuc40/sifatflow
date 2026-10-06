{{-- <x-admin.status-badge :status="$course->status" /> draft / published / archived, pending / approved / rejected --}}
@props(['status'])
@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $color = match ($value) {
        'published', 'approved' => 'green',
        'draft', 'pending' => 'amber',
        'rejected' => 'red',
        default => 'gray',
    };
@endphp
<x-admin.badge :color="$color" {{ $attributes }}>{{ ucfirst($value) }}</x-admin.badge>

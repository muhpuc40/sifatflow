@extends('messages.layouts.email')

@section('content')
    <p>Hello {{ $name }},</p>
    <p>Your login code is:</p>
    <p style="font-size:32px;font-weight:bold;letter-spacing:6px;margin:16px 0;">{{ $code }}</p>
    <p>This code expires in {{ $minutes }} minutes. Do not share it with anyone.</p>
@endsection

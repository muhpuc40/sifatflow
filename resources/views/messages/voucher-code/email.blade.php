@extends('messages.layouts.email')

@section('content')
    <p>Hello {{ $name }},</p>
    <p>You have a discount voucher for your next course purchase:</p>
    <p style="font-size:28px;font-weight:bold;letter-spacing:3px;margin:16px 0;color:#4f46e5;">{{ $code }}</p>
    <p>Worth: <strong>{{ $amount_label }}</strong></p>
    <p>Use it before <strong>{{ $expires_at }}</strong>. This code is only for your account and can be used once.</p>
@endsection

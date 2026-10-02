<!DOCTYPE html>
<html lang="en">
<body style="margin:0;padding:24px;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <div style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:8px;padding:24px;">
        <h2 style="margin:0 0 16px;color:#4f46e5;">{{ config('app.name') }}</h2>

        @yield('content')

        <p style="margin-top:24px;font-size:12px;color:#6b7280;">
            If you did not request this, you can ignore this message.
        </p>
    </div>
</body>
</html>

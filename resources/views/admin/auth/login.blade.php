<x-admin.guest title="Sign in">
    <h1 class="text-lg font-semibold text-slate-900">Sign in to the admin panel</h1>
    <p class="mt-1 text-sm text-slate-500">Use your email or phone number.</p>

    <form method="POST" action="{{ url('login') }}" class="mt-6 space-y-5">
        @csrf

        <x-admin.input name="login" label="Email or phone" autocomplete="username" autofocus required />
        <x-admin.input name="password" label="Password" type="password" autocomplete="current-password" required />

        <div class="flex items-center justify-between">
            <x-admin.checkbox name="remember" label="Remember me" />
        </div>

        <x-admin.button type="submit" class="w-full">Sign in</x-admin.button>
    </form>
</x-admin.guest>

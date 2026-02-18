@extends('alt-google-2fa::layouts.layout')

@section('content')
    <div class="flex flex-col justify-center items-center">
        <h1 class="text-xl mb-4">Verify 2FA</h1>

        <p class="my-4 text-center">Enter your 2FA code from your <br /> Google Authenticator to login.</p>

        <form action="{{ route('alt-google-2fa.verify') }}" class="flex flex-col justify-center" method="POST">
            @csrf

            <input name="one_time_password"
                   autocomplete="one-time-code"
                   inputmode="numeric"
                   pattern="[0-9]*"
                   type="text"
                   required
                   aria-label="One Time Password"
                   placeholder="OTP Code">

            <button type="submit">Authenticate</button>
        </form>

        @foreach ($errors->all() as $error)
            <p class="mt-4 text-red-500">{{ $error }}</p>
        @endforeach

        <a href="{{ route('statamic.logout') }}" class="underline mt-4 text-sm text-gray-600 dark:text-gray-300">
            Cancel & Logout
        </a>
    </div>
@endsection
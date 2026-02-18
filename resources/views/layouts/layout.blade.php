<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>{{ config('app.name') }} — {{ $title ?? 'Authentication' }}</title>

    @include('statamic::partials.head')
    <s:AltGoogle2FA:FrontendAssets />
</head>
    <body class="alt-google-2fa min-h-screen flex justify-center bg-gray-100 dark:bg-gray-900">
        <div class="m-5">
            <div class="p-10 relative z-10">
                <s:AltGoogle2FA:OutsideLogo />
            </div>
            <div class="w-full max-w-md bg-white dark:bg-gray-800 dark:text-white rounded-xl shadow-xl p-8">
                @yield('content')
            </div>
        </div>
    </body>
</html>

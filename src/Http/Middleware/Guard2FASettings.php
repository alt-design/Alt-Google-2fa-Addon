<?php

namespace AltDesign\AltGoogle2FA\Http\Middleware;

use AltDesign\AltGoogle2FA\Helpers\Data;
use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Statamic\Facades\User;

class Guard2FASettings
{
    public function handle(Request $request, Closure $next)
    {
        $user = User::current();
        if (!$user->can('access alt google 2fa settings')) {
            abort(403);
        }

        return $next($request);
    }
}

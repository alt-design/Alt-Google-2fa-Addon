<?php namespace AltDesign\AltGoogle2FA\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckFor2FA
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure the user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Skip checking 2FA for routes related to 2FA (to prevent infinite loops)
        if ($request->routeIs('alt-google-2fa.prompt', 'alt-google-2fa.enable-2fa', 'alt-google-2fa.verify')) {
            return $next($request);
        }

        // Check if the user has 2FA enabled but has not verified it
        if (!session('2fa_verified') && !empty($user->google_secret_2fa_key) && ($user->enabled_2fa ?? false)) {
            return redirect()->route('alt-google-2fa.prompt'); // Redirect to 2FA prompt
        }

        // If the user does not have 2FA enabled, redirect to enable 2FA
        if (empty($user->google_secret_2fa_key)) {
            return redirect()->route('alt-google-2fa.enable-2fa'); // Redirect to enable 2FA
        }

        // Proceed with the request if 2FA is verified
        return $next($request);
    }
}

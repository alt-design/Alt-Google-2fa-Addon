<?php namespace AltDesign\AltGoogle2FA\Http\Middleware;

use AltDesign\AltGoogle2FA\Helpers\Data;
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

        $data = new Data('settings');
        $superUserPolicy = $data->data['alt_google_2fa_forced_super_user'] ?? 'off';
        $forcedRoles = $data->data['alt_google_2fa_forced_roles'] ?? [];
        $optionalRoles = $data->data['alt_google_2fa_optional_roles'] ?? [];

        // Skip checking 2FA for routes related to 2FA (to prevent infinite loops)
        if ($request->routeIs('alt-google-2fa.prompt', 'alt-google-2fa.enable-2fa', 'alt-google-2fa.verify')) {
            return $next($request);
        }

        $isSuperUser = $user->isSuper(); // Assuming `isSuper()` checks if the user is a superuser in Statamic.

        if ($isSuperUser) {
            if ($superUserPolicy === 'off') {
                // Superusers are not required to use 2FA
                return $next($request);
            } elseif ($superUserPolicy === 'optional' && !$user->enabled_2fa) {
                // 2FA is optional but not enabled
                return $next($request);
            }
        }

        // Get user roles
        $userRoles = $user->roles()->map->handle()->toArray(); // Assuming roles can be retrieved and handles mapped.

        // Check if 2FA is enforced for the user's roles
        if (!empty(array_intersect($userRoles, $forcedRoles))) {
            // Enforce 2FA for roles listed in `alt_google_2fa_forced_roles`
            if (!session('2fa_verified') && !empty($user->google_secret_2fa_key) && ($user->enabled_2fa ?? false)) {
                return redirect()->route('alt-google-2fa.prompt'); // Redirect to 2FA prompt
            }

            if (!($user->enabled_2fa ?? false)) {
                return redirect()->route('alt-google-2fa.enable-2fa'); // Redirect to enable 2FA
            }
        }

        // Check if 2FA is optional for the user's roles
        if (!empty(array_intersect($userRoles, $optionalRoles))) {
            // If 2FA is optional and not enabled, allow access
            if (empty($user->google_secret_2fa_key)) {
                return $next($request);
            }
        }

        // Default behavior: If 2FA settings don’t explicitly match the user, enforce it
        if (!session('2fa_verified') && !empty($user->google_secret_2fa_key) && ($user->enabled_2fa ?? false)) {
            return redirect()->route('alt-google-2fa.prompt'); // Redirect to 2FA prompt
        }

        if (empty($user->google_secret_2fa_key)) {
            return redirect()->route('alt-google-2fa.enable-2fa'); // Redirect to enable 2FA
        }

        // Proceed with the request if 2FA is verified
        return $next($request);
    }
}

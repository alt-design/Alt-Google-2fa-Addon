<?php

namespace AltDesign\AltGoogle2FA\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use AltDesign\AltGoogle2FA\Helpers\Data;

class CheckFor2FA
{
    public function handle(Request $request, Closure $next)
    {
        // We actually don't care if a user is logged in here, if they're not, let the request of the middleware run.
        if (!Auth::check()) {
            return $next($request);
        }

        $userRepository = config('statamic.users.repository');
        if($userRepository === 'file') {
            $user = Auth::user();
        } elseif ($userRepository === 'eloquent') {
            $user = \Statamic\Auth\Eloquent\User::find(Auth::id());
        } else {
            throw new \Exception('User Repository not defined');
        }

        $data = new Data('settings');
        $superUserPolicy = $data->data['alt_google_2fa_forced_super_user'] ?? 'off';
        $forcedRoles = $data->data['alt_google_2fa_forced_roles'] ?? [];
        $optionalRoles = $data->data['alt_google_2fa_optional_roles'] ?? [];
        $noRedirectUnverified = $data->data['alt_google_2fa_unverified_user_no_redirect'] ?? false;

        if ($noRedirectUnverified) {
            // If user's email requires verification, skip this until they're verified
            $authUser = Auth::user();
            if ($authUser instanceof MustVerifyEmail && !$authUser->hasVerifiedEmail()) {
                return $next($request);
            }
        }

        // Skip checking 2FA for routes related to 2FA (to prevent infinite loops)
        if ($request->routeIs('alt-google-2fa.prompt', 'alt-google-2fa.enable-2fa', 'alt-google-2fa.verify', 'statamic.logout')) {
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
        if (!empty(array_intersect($userRoles, $forcedRoles)) || $user->user_opt_in) {
            // Enforce 2FA for roles listed in `alt_google_2fa_forced_roles`
            if (!session('2fa_verified') && !empty($user->google_secret_2fa_key) && ($user->enabled_2fa ?? false)) {
                return $this->redirectToRouteSaveReferrer('alt-google-2fa.prompt'); // Redirect to 2FA prompt
            }

            if (!($user->enabled_2fa ?? false)) {
                return $this->redirectToRouteSaveReferrer('alt-google-2fa.enable-2fa'); // Redirect to enable 2FA
            }
        }

        // Check if 2FA is optional for the user's roles
        if (!empty(array_intersect($userRoles, $optionalRoles))) {
            // If 2FA is optional and not enabled, allow access
            if (empty($user->google_secret_2fa_key)) {
                return $next($request);
            }
        }

        // If nothing is enforced for users.
        if(!$isSuperUser && empty($forcedRoles) && empty($optionalRoles)) {
            return $next($request);
        }

        // Default behavior: If 2FA settings don’t explicitly match the user, enforce it
        if (!session('2fa_verified') && !empty($user->google_secret_2fa_key) && ($user->enabled_2fa ?? false)) {
            return $this->redirectToRouteSaveReferrer('alt-google-2fa.prompt'); // Redirect to 2FA prompt
        }

        if (empty($user->google_secret_2fa_key)) {
            return $this->redirectToRouteSaveReferrer('alt-google-2fa.enable-2fa'); // Redirect to enable 2FA
        }

        // Proceed with the request if 2FA is verified
        return $next($request);
    }

    private function redirectToRouteSaveReferrer(
        string $route
    )
    {
        session()->put(
            'url.intended',
            request()->getRequestUri()
        );
        return redirect()->route($route);
    }
}

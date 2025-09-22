<?php

namespace AltDesign\AltGoogle2FA\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use AltDesign\AltGoogle2FA\Helpers\Data;
use Illuminate\Support\Facades\Redirect;
use Statamic\Auth\Eloquent\User;

class CheckFor2FA
{
    protected array $skipRoutes = [
        'alt-google-2fa.verify',
        'statamic.logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (
            !Auth::check() ||
            $request->routeIs(...$this->skipRoutes) ||
            session('2fa_verified') ||
            $request->isXmlHttpRequest()
        ) {
            return $next($request);
        }

        $user  = $this->getUser();
        $check = $this->checkFor2FA(
            userRoles: $user->roles()->map->handle()->toArray(),
            isSuperUser: $user->isSuper(),
            enabled: $user->enabled_2fa,
        );

        $route = $user->enabled_2fa ? 'alt-google-2fa.prompt' : 'alt-google-2fa.enable-2fa';
        if ($check && !$request->routeIs($route)) {
            Redirect::setIntendedUrl($request->fullUrl());
            return redirect()->route($route);
        }

        return $next($request);
    }

    protected function checkFor2FA(array $userRoles, bool $isSuperUser, bool $enabled): bool
    {
        $data = new Data('settings');
        $superUserPolicy = $data->data['alt_google_2fa_forced_super_user'] ?? 'off';
        $forcedRoles = $data->data['alt_google_2fa_forced_roles'] ?? [];
        $optionalRoles = $data->data['alt_google_2fa_optional_roles'] ?? [];

        if ($isSuperUser) {
            if ($superUserPolicy === 'enforce' || ($superUserPolicy === 'optional' && $enabled)) {
                return true;
            }
        }

        if (!empty(array_intersect($userRoles, $forcedRoles))) {
            return true;
        }

        if (!empty(array_intersect($userRoles, $optionalRoles)) && $enabled) {
            return true;
        }

        return false;
    }

    protected function getUser(): Authenticatable|User
    {
        $userRepository = config('statamic.users.repository');
        return match($userRepository) {
            'file' => Auth::user(),
            'eloquent' => \Statamic\Auth\Eloquent\User::find(Auth::id()) ?? throw new \Exception('User not found'),
            default => throw new \Exception('User Repository not defined'),
        };
    }
}

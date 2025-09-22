<?php

namespace AltDesign\AltGoogle2FA\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use AltDesign\AltGoogle2FA\Helpers\Data;
use Illuminate\Support\Facades\Redirect;
use Statamic\Auth\Eloquent\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class CheckFor2FA
{
    protected array $skipRoutes = [
        'alt-google-2fa.verify',
        'statamic.logout',
    ];

    public function __construct(protected Data $data)
    {

    }

    public function handle(Request $request, Closure $next)
    {
        $user = $this->getUser();

        if (
            !$user ||
            $request->routeIs(...$this->skipRoutes) ||
            session('2fa_verified') ||
            $request->isXmlHttpRequest() ||
            ($this->data->noRedirectUnverified() && $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail())
        ) {
            return $next($request);
        }

        $route = !empty($user->google_secret_2fa_key) ? 'alt-google-2fa.prompt' : 'alt-google-2fa.enable-2fa';
        if ($request->routeIs($route)) {
            return $next($request);
        }

        $isEnforced = $this->enforced(
            userRoles: $user->roles()->map->handle()->toArray(),
            isSuperUser: $user->isSuper(),
        );

        $isOptional = $this->optional(
            userRoles: $user->roles()->map->handle()->toArray(),
            isSuperUser: $user->isSuper(),
        );

        if ($isEnforced || ($isOptional && $user->enabled_2fa)) {
            Redirect::setIntendedUrl($request->fullUrl());
            return redirect()->route($route);
        }

        return $next($request);
    }

    protected function enforced(array $userRoles, bool $isSuperUser): bool
    {
        if ($isSuperUser && $this->data->superUserPolicy() === 'enforce') {
            return true;
        }

        if (!empty(array_intersect($userRoles, $this->data->forcedRoles()))) {
            return true;
        }

        return false;
    }

    protected function optional(array $userRoles, bool $isSuperUser): bool
    {
        if ($isSuperUser && $this->data->superUserPolicy() === 'optional') {
            return true;
        }

        if (!empty(array_intersect($userRoles, $this->data->optionalRoles()))) {
            return true;
        }

        return false;
    }

    protected function getUser(): Authenticatable|User|null
    {
        $userRepository = config('statamic.users.repository');
        return match($userRepository) {
            'file' => Auth::user(),
            'eloquent' => \Statamic\Auth\Eloquent\User::find(Auth::id()),
            default => throw new \Exception('User Repository not defined'),
        };
    }
}

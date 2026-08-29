<?php

namespace AltDesign\AltGoogle2FA;

use Facades\Statamic\Version;
use Illuminate\Support\Str;
use Statamic\Facades\CP\Nav;
use Statamic\Http\Middleware\AuthGuard;
use Statamic\Providers\AddonServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package  AltDesign\AltGoogle2FA
 * @author   The gang @ Alt Design <ben@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class ServiceProvider extends AddonServiceProvider
{
    protected $viewNamespace = 'alt-google-2fa';

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
        'web' => __DIR__.'/../routes/web.php',
    ];

    protected $vite = [
        'input' => [
            'resources/css/alt-google-2fa-frontend.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    protected $middlewareGroups = [
        'statamic.cp.authenticated' => [
            'alt-google-2fa' => \AltDesign\AltGoogle2FA\Http\Middleware\CheckFor2FA::class,
        ],
        AuthGuard::class => [
            'alt-google-2fa' => \AltDesign\AltGoogle2FA\Http\Middleware\CheckFor2FA::class
        ]
    ];

    public function addToNav()
    {
        Nav::extend(function ($nav) {
            $nav->content('Alt Google 2FA')
                ->section('Tools')
                ->route('alt-google-2fa.index')
                ->icon(config('alt-google-2fa.alt_google_2fa_icon'));
        });
    }

    public function bootAddon()
    {
        $this->addToNav();

        // Statamic >= V6 - unbind the settings blueprint to remove the default settings page and permissions 
        // as we are handling this manually instead
        if(intval(Str::before(Version::get(), '.')) >= 6) {
            app()->offsetUnset("statamic.addons.alt-google-2fa.settings_blueprint");
        }
    }

    public function boot()
    {
        parent::boot();

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'alt-google-2fa-migrations');

        $this->publishes([
            __DIR__ . '/../routes/cp.php' => base_path('routes/vendor/alt-google-2fa/cp.php'),
            __DIR__ . '/../routes/web.php' => base_path('routes/vendor/alt-google-2fa/web.php'),
        ], 'alt-google-2fa-routes');
    }

    protected function bootRoutes()
    {
        $cpPath = base_path('routes/vendor/alt-google-2fa/cp.php');
        $webPath = base_path('routes/vendor/alt-google-2fa/web.php');
        if (file_exists($cpPath)) {
            $this->routes['cp'] = $cpPath;
        }
        if (file_exists($webPath)) {
            $this->routes['web'] = $webPath;
        }
        return parent::bootRoutes();
    }
}


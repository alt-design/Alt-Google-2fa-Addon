<?php

namespace AltDesign\AltGoogle2FA;

use AltDesign\AltGoogle2FA\Events\Alt2FAEvents;
use Illuminate\Support\Facades\Event;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;
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
            'resources/css/cp.css',
            'resources/js/cp.js',
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

    public function addToNav(): self
    {
        Nav::extend(function ($nav) {
            $nav->content('Alt Google 2FA')
                ->section('Tools')
                ->route('alt-google-2fa.index')
                ->icon('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 24 24"><g id="Custom_Size_1"> <path d="M-36.609-78.623l-.284.477C-37.746-76.665-38.456-76-39.167-76c-.853,0-1.137-1.146-1.137-4.968,0-3.773.379-4.919,1.942-6.066l.663-.477a3.611,3.611,0,0,0,1.09-1.146Zm8.527,3.031c0-.013,0-.024,0-.037l0-5.334v-6.83c0-3.916-1.184-6.161-3.979-7.594A14.253,14.253,0,0,0-38.5-96.772c-5.068,0-9.663,2.532-9.663,5.349,0,1.863,1.705,3.2,4.074,3.2,2.463,0,4.264-1.385,4.264-3.248s-1.658-3.152-4.027-3.152a4.971,4.971,0,0,0-1.374.143,9.537,9.537,0,0,1,4.974-1.528c2.652,0,3.837,1.385,3.837,4.442,0,2.914-1,3.964-4.832,5.206l-1.184.382c-4.642,1.48-6.442,3.248-6.442,6.161,0,3.2,2.842,5.684,6.395,5.684,2.842,0,4.311-.907,6.064-3.964a5.322,5.322,0,0,0,5.543,3.964C-28.028-74.017-28.082-75.2-28.082-75.592Z" transform="translate(50.095 97.449)" fill="none" stroke-linecap="round" stroke="currentcolor" stroke-linejoin="round" stroke-width="1"/> </g> </svg>');
        });

        return $this;
    }

    private function registerFieldtypes(): self
    {
        \AltDesign\AltGoogle2FA\Fieldtypes\TwoFactorStatus::register();

        return $this;
    }

    private function registerPublishes(): self
    {
        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'alt-google-2fa-migrations');

        $this->publishes([
            __DIR__.'/../resources/blueprints/2fa-user-fields.yaml' => base_path('resources/blueprints/vendor/alt-google-2fa/2fa-user-fields.yaml'),
        ], 'alt-google-2fa-user-blueprint');

        return $this;
    }

    private function registerEvents(): self
    {
        Event::subscribe(Alt2FAEvents::class);

        return $this;
    }

    private function registerPermissions(): self
    {
        Permission::group('alt-google-2fa', 'Alt Google 2FA Addon', function () {
            Permission::register('reset user 2fa enrollments')
                ->label('Reset User 2FA Enrollments');
        });

        return $this;
    }

    public function bootAddon(): void
    {
        $this->addToNav()
            ->registerEvents()
            ->registerFieldtypes()
            ->registerPublishes()
            ->registerPermissions();
    }

}


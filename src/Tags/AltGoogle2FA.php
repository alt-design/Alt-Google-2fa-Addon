<?php

namespace AltDesign\AltGoogle2FA\Tags;

use AltDesign\AltGoogle2FA\View\Components\OutsideLogo;
use Google2FA;

use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Auth;
use Statamic\Tags\Tags;

/**
 * Class AltGoogle2FA
 *
 * @package  AltDesign\AltGoogle2FA
 * @author   The gang @ Alt Design <ben@alt-design.net>
 * @license  Copyright (C) Alt Design Limited - All Rights Reserved - licensed under the MIT license
 * @link     https://alt-design.net
 */
class AltGoogle2FA extends Tags
{
    protected $viteBuild = 'vendor/alt-google-2fa/build';
    protected $viteHot = __DIR__ . '/../../resources/dist/hot';
    protected static $handle = 'AltGoogle2FA';

    /**
     * Generates the secret key for a user, then creates the QR code.
     *
     * @return mixed - QR Code for setting the secret key.
     */
    public function index()
    {
        $user = Auth::user();

        Google2FA::setQRCodeBackend('svg');

        // If we have no Secret Key already set for this user, get one added.
        if(empty($user->google_secret_2fa_key ?? false)) {
            $user->google_secret_2fa_key = Google2FA::generateSecretKey(16, '');
            $user->saveQuietly(); // Don't trigger events
        }

        return Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google_secret_2fa_key
        );
    }

    /**
     * Checks if user is logged in, and if the user has 2FA enabled.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        if(!Auth::check()) {
            return false;
        }

        return Auth::user()->enabled_2fa;
    }

    /**
     * The {{ AltGoogle2FA:FrontendAssets }} tag.
     * Puts the Vite assets on the frontend
     * @return string|array
     */
    public function FrontendAssets()
    {
        $vite = (new Vite)->useHotfile( $this->viteHot)->useBuildDirectory($this->viteBuild);
        $assets = sprintf('<link rel="stylesheet" href="%s">', $vite->asset('resources/css/alt-google-2fa-addon.css'));
        return $assets;
    }

    /**
     * The {{ AltGoogle2FA:OutsideLogo }} tag.
     * Returns the outside logo component
     * @return string
     */
    public function OutsideLogo()
    {
        return (new OutsideLogo)->render()->render() ?? '';
    }
}

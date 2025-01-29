<?php namespace AltDesign\AltGoogle2FA\Tags;

use Illuminate\Support\Facades\Auth;
use Statamic\Tags\Tags;

use Google2FA;

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
    protected static $handle = 'AltGoogle2FA';

    /**
     * @return mixed
     */
    public function index()
    {
        $user = Auth::user();

        // If we have no Secret Key already set for this user, get one added.
        if(empty($user->google_secret_2fa_key ?? false)) {
            $user->google_secret_2fa_key = Google2FA::generateSecretKey(16, '');
            $user->saveQuietly(); // Don't trigger events
        }

        return Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google_secret_2fa_key
        );;
    }

    public function enable()
    {
        // todo
    }

    public function disable()
    {
        // todo
    }
}

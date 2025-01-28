<?php namespace AltDesign\AltGoogle2FA\Tags;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Statamic\Tags\Tags;
use Google2FA;

class AltGoogle2FA extends Tags
{
    protected static $handle = 'AltGoogle2FA';

    public function index()
    {
        Google2FA::setQRCodeBackend('svg');

        $user = Auth::user();
        if(empty($user->google_secret_2fa_key ?? false)) {
            $user->google_secret_2fa_key = Google2FA::generateSecretKey(16, '');
            $user->saveQuietly();
        }

        $qr = Google2FA::getQRCodeInline(
            config('app.name'),
            Auth::user()->email,
            $user->google_secret_2fa_key
        );

        return $qr;
    }
}

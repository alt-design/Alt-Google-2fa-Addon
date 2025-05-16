<?php
use Illuminate\Support\Facades\Route;
use AltDesign\AltGoogle2FA\Http\Controllers\AltGoogle2FAController;

Route::group(['middleware' => ['statamic.cp.authenticated', 'alt-google-2fa.protected'], 'namespace' => 'AltDesign\AltGoogle2FA\Http\Controllers'], function() {
    Route::get('/alt-design/alt-google-2fa/', [AltGoogle2FAController::class, 'index'])->name('alt-google-2fa.index');
    Route::post('/alt-design/alt-google-2fa/', [AltGoogle2FAController::class, 'update'])->name('alt-google-2fa.save');
});

Route::get('/alt-design/alt-disable-2fa', [AltGoogle2FAController::class, 'disableTargetUser'])
    ->name('alt-google-2fa.disable-target-user')
    ->middleware('statamic.cp.authenticated');

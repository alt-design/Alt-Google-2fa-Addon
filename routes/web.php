<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'web'], 'namespace' => 'AltDesign\AltGoogle2FA\Http\Controllers'], function() {
    Route::get('/alt-design/alt-enable-2fa', 'AltGoogle2FAController@enable')->name('alt-google-2fa.enable-2fa');

    Route::get('/alt-design/alt-attempt-2fa', 'AltGoogle2FAController@show')->name('alt-google-2fa.prompt');
    Route::post('/alt-design/alt-check-2fa', 'AltGoogle2FAController@verify')->name('alt-google-2fa.verify');

    Route::get('/alt-design/alt-disable-2fa', 'AltGoogle2FAController@disableForm')->name('alt-google-2fa.disable-2fa');
    Route::post('/alt-design/alt-disable-2fa', 'AltGoogle2FAController@disable')->name('alt-google-2fa.disable');
});
